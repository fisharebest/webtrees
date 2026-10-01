<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\ChangeStatus;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_keys;

#[CoversClass(PendingChangesService::class)]
class PendingChangesServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChangesService::class));
    }

    public function testPendingXrefsAndChangesRespectTreeStatusAndLimit(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree = $tree_service->create('first', 'First');
        $other_tree = $tree_service->create('second', 'Second');
        $user = (new UserService(new SystemClock()))->create('tester', 'Tester', 'tester@example.com', 'secret');
        $service = new PendingChangesService(new GedcomImportService());

        self::assertFalse($service->pendingChangesExist());

        foreach (
            [
                [$tree->id(), 'I2', ChangeStatus::Pending->value],
                [$tree->id(), 'I1', ChangeStatus::Pending->value],
                [$tree->id(), 'I1', ChangeStatus::Pending->value],
                [$tree->id(), 'I3', ChangeStatus::Accepted->value],
                [$other_tree->id(), 'I4', ChangeStatus::Pending->value],
            ] as [$tree_id, $xref, $status]
        ) {
            DB::query(table: 'change')->insert(values: [
                'gedcom_id'  => $tree_id,
                'xref'       => $xref,
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => '0 @' . $xref . '@ INDI',
                'user_id'    => $user->id(),
            ]);
        }

        self::assertTrue($service->pendingChangesExist());
        self::assertSame(['I1', 'I2'], $service->pendingXrefs($tree)->all());
        self::assertSame(['I4'], $service->pendingXrefs($other_tree)->all());
        self::assertSame(['I1'], array_keys($service->pendingChanges($tree, 1)));
        $changes = $service->pendingChanges($tree, 1)['I1'];
        self::assertCount(2, $changes);
        self::assertSame('I1', $changes[0]->record->xref());
        self::assertSame($user->userName(), $changes[0]->user_name);
        self::assertSame([], $service->pendingChanges($tree, 0));
    }

    public function testAcceptChangeProcessesOnlyMatchingPendingChanges(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $user = (new UserService(new SystemClock()))->create('tester', 'Tester', 'tester@example.com', 'secret');
        $change_ids = [];

        foreach (['I1', 'I1', 'I1', 'I2'] as $xref) {
            $change_ids[] = DB::query(table: 'change')->insertGetId(
                values: [
                    'gedcom_id' => $tree->id(),
                    'xref' => $xref,
                    'old_gedcom' => '',
                    'new_gedcom' => '0 @' . $xref . '@ INDI',
                    'user_id' => $user->id(),
                ],
                id_column: 'change_id',
            );
        }

        $import_service = self::createMock(GedcomImportService::class);
        $import_service->expects(self::exactly(2))->method('updateRecord');

        (new PendingChangesService($import_service))->acceptChange($tree, 'I1', (string) $change_ids[1]);

        self::assertSame(
            [
                ChangeStatus::Accepted->value,
                ChangeStatus::Accepted->value,
                ChangeStatus::Pending->value,
                ChangeStatus::Pending->value,
            ],
            DB::query()
                ->from('change')
                ->orderBy('change_id')
                ->pluck('status'),
        );
    }

    public function testAcceptTreeProcessesOnlyRequestedXrefsInChangeOrder(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $other_tree = (new TreeService(new GedcomImportService()))->create('second', 'Second');
        $user = (new UserService(new SystemClock()))->create('tester', 'Tester', 'tester@example.com', 'secret');

        foreach (
            [
                [$tree->id(), 'I2', 'Second'],
                [$tree->id(), 'I1', 'First'],
                [$tree->id(), 'I1', 'Updated'],
                [$other_tree->id(), 'I1', 'Other tree'],
            ] as [$tree_id, $xref, $name]
        ) {
            DB::query(table: 'change')->insert(values: [
                'gedcom_id'  => $tree_id,
                'xref'       => $xref,
                'old_gedcom' => '',
                'new_gedcom' => '0 @' . $xref . "@ INDI\n1 NAME " . $name,
                'user_id'    => $user->id(),
            ]);
        }

        $processed_records = [];
        $import_service = self::createMock(GedcomImportService::class);
        $import_service->expects(self::exactly(2))
            ->method('updateRecord')
            ->willReturnCallback(static function (string $gedcom) use (&$processed_records): void {
                $processed_records[] = $gedcom;
            });

        (new PendingChangesService($import_service))->acceptTree($tree, 1);

        self::assertSame([
            "0 @I1@ INDI\n1 NAME First",
            "0 @I1@ INDI\n1 NAME Updated",
        ], $processed_records);
        self::assertSame(
            [
                ChangeStatus::Pending->value,
                ChangeStatus::Accepted->value,
                ChangeStatus::Accepted->value,
                ChangeStatus::Pending->value,
            ],
            DB::query()
                ->from('change')
                ->orderBy('change_id')
                ->pluck('status'),
        );
    }

    public function testAcceptRecordOnlyProcessesPendingChangesForThatRecord(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $user = (new UserService(new SystemClock()))->create('tester', 'Tester', 'tester@example.com', 'secret');

        foreach (
            [
                ['I1', ChangeStatus::Pending->value],
                ['I2', ChangeStatus::Pending->value],
                ['I1', ChangeStatus::Accepted->value],
                ['I1', ChangeStatus::Pending->value],
            ] as [$xref, $status]
        ) {
            DB::query(table: 'change')->insert(values: [
                'gedcom_id'  => $tree->id(),
                'xref'       => $xref,
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => '0 @' . $xref . '@ INDI',
                'user_id'    => $user->id(),
            ]);
        }

        $import_service = self::createMock(GedcomImportService::class);
        $import_service->expects(self::exactly(2))->method('updateRecord');
        $record = Registry::individualFactory()->new('I1', '0 @I1@ INDI', null, $tree);

        (new PendingChangesService($import_service))->acceptRecord($record);

        self::assertSame(
            [
                ChangeStatus::Accepted->value,
                ChangeStatus::Pending->value,
                ChangeStatus::Accepted->value,
                ChangeStatus::Accepted->value,
            ],
            DB::query()
                ->from('change')
                ->orderBy('change_id')
                ->pluck('status'),
        );
    }
}
