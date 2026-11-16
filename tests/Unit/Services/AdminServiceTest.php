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
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Database\Capsule\Manager;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_map;
use function count;
use function max;
use function method_exists;
use function min;

#[CoversClass(AdminService::class)]
class AdminServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(AdminService::class));
    }

    public function testFindsXrefsSharedBetweenTrees(): void
    {
        self::createDatabase();

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $first_tree     = $tree_service->create('first', 'First');
        $second_tree    = $tree_service->create('second', 'Second');
        $admin_service  = new AdminService();

        $initial_count = $admin_service->countCommonXrefs($first_tree, $second_tree);
        $initial_xrefs = $admin_service->duplicateXrefs($first_tree);

        $import_service->importRecord("0 @I1@ INDI\n1 NAME First /Person/", $first_tree, true);
        $import_service->importRecord("0 @S1@ SOUR\n1 TITL First source", $first_tree, true);
        $import_service->importRecord("0 @I1@ INDI\n1 NAME Second /Person/", $second_tree, true);
        $import_service->importRecord("0 @S1@ SOUR\n1 TITL Second source", $second_tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME Unique /Person/", $second_tree, true);

        self::assertSame($initial_count + 2, $admin_service->countCommonXrefs($first_tree, $second_tree));
        $duplicate_xrefs = $admin_service->duplicateXrefs($first_tree);
        self::assertCount(count($initial_xrefs) + 2, $duplicate_xrefs);
        self::assertSame('INDI', $duplicate_xrefs['I1']);
        self::assertSame('SOUR', $duplicate_xrefs['S1']);
    }

    public function testOnlyPendingChangesContributeToSharedXrefs(): void
    {
        self::createDatabase();

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $first_tree     = $tree_service->create('first', 'First');
        $second_tree    = $tree_service->create('second', 'Second');
        $user           = (new UserService(new SystemClock()))
            ->create('tester', 'Tester', 'tester@example.com', 'secret');
        $admin_service  = new AdminService();
        $initial_count  = $admin_service->countCommonXrefs($first_tree, $second_tree);

        $import_service->importRecord("0 @I1@ INDI\n1 NAME First /Person/", $first_tree, true);
        $import_service->importRecord("0 @S1@ SOUR\n1 TITL Second source", $second_tree, true);

        foreach (
            [
                [$first_tree->id(), 'S1', ChangeStatus::Accepted->value],
                [$first_tree->id(), 'S1', ChangeStatus::Rejected->value],
                [$second_tree->id(), 'I1', ChangeStatus::Accepted->value],
                [$second_tree->id(), 'I1', ChangeStatus::Rejected->value],
            ] as [$tree_id, $xref, $status]
        ) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $tree_id,
                'xref'       => $xref,
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => '0 @' . $xref . '@ INDI',
                'user_id'    => $user->id(),
            ]);
        }

        self::assertSame($initial_count, $admin_service->countCommonXrefs($first_tree, $second_tree));

        foreach (
            [
                [$first_tree->id(), 'S1'],
                [$second_tree->id(), 'I1'],
            ] as [$tree_id, $xref]
        ) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $tree_id,
                'xref'       => $xref,
                'status'     => ChangeStatus::Pending->value,
                'old_gedcom' => '',
                'new_gedcom' => '0 @' . $xref . '@ INDI',
                'user_id'    => $user->id(),
            ]);
        }

        self::assertSame($initial_count + 2, $admin_service->countCommonXrefs($first_tree, $second_tree));

        foreach ([$first_tree->id(), $second_tree->id()] as $tree_id) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $tree_id,
                'xref'       => 'Z999999',
                'status'     => ChangeStatus::Pending->value,
                'old_gedcom' => '',
                'new_gedcom' => '0 @Z999999@ INDI',
                'user_id'    => $user->id(),
            ]);
        }

        self::assertSame($initial_count + 3, $admin_service->countCommonXrefs($first_tree, $second_tree));
    }

    public function testFindsDuplicateRecordsWithinOneTree(): void
    {
        self::createDatabase();

        $pdo = Manager::connection()->getPdo();
        if (method_exists($pdo, 'createFunction')) {
            $pdo->createFunction('LEAST', min(...), 2);
            $pdo->createFunction('GREATEST', max(...), 2);
        } else {
            $pdo->sqliteCreateFunction('LEAST', min(...), 2);
            $pdo->sqliteCreateFunction('GREATEST', max(...), 2);
        }

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $tree           = $tree_service->create('first', 'First');
        $other_tree     = $tree_service->create('second', 'Second');

        $import_service->importRecord("0 @S1@ SOUR\n1 TITL Shared title", $tree, true);
        $import_service->importRecord("0 @S2@ SOUR\n1 TITL Shared title", $tree, true);
        $import_service->importRecord("0 @S3@ SOUR\n1 TITL Unique title", $tree, true);
        $import_service->importRecord("0 @S4@ SOUR\n1 TITL Shared title", $other_tree, true);

        $import_service->importRecord("0 @M1@ OBJE\n1 FILE first.jpg\n2 FORM jpg\n2 TITL Shared image", $tree, true);
        $import_service->importRecord("0 @M2@ OBJE\n1 FILE second.jpg\n2 FORM jpg\n2 TITL Shared image", $tree, true);
        $import_service->importRecord("0 @I1@ INDI\n1 NAME Same /Person/\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME Same /Person/\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @I1@\n1 WIFE @I2@", $tree, true);
        $import_service->importRecord("0 @F2@ FAM\n1 HUSB @I2@\n1 WIFE @I1@", $tree, true);
        $import_service->importRecord("0 @F3@ FAM", $tree, true);
        $import_service->importRecord("0 @F4@ FAM", $tree, true);
        $import_service->importRecord("0 @F5@ FAM\n1 HUSB @I1@", $tree, true);
        $import_service->importRecord("0 @F6@ FAM\n1 WIFE @I1@", $tree, true);
        $import_service->importRecord("0 @F7@ FAM\n1 HUSB @I2@", $tree, true);
        $import_service->importRecord("0 @F8@ FAM\n1 HUSB @I1@\n1 WIFE @I2@", $other_tree, true);

        $duplicates = (new AdminService())->duplicateRecords($tree);

        self::assertSame(['S1', 'S2'], array_map(static fn ($source): string => $source->xref(), $duplicates['Sources'][0]));
        self::assertSame(['M1', 'M2'], array_map(static fn ($media): string => $media->xref(), $duplicates['Media objects'][0]));
        self::assertSame(['I1', 'I2'], array_map(static fn ($individual): string => $individual->xref(), $duplicates['Individuals'][0]));
        self::assertEqualsCanonicalizing(
            [['F1', 'F2'], ['F5', 'F6']],
            array_map(
                static fn (array $families): array => array_map(static fn ($family): string => $family->xref(), $families),
                $duplicates['Families'],
            ),
        );
    }
}
