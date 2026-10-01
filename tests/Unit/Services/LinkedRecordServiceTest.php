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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\ChangeStatus;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LinkedRecordService::class)]
class LinkedRecordServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(LinkedRecordService::class));
    }

    public function testJoinedRecordLookupsPreserveTypesFiltersAndCollections(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $tree           = $tree_service->create('first', 'First');
        $other_tree     = $tree_service->create('second', 'Second');

        $records = [
            'I1' => 'INDI',
            'I2' => 'INDI',
            'F1' => 'FAM',
            'L1' => '_LOC',
            'M1' => 'OBJE',
            'N1' => 'NOTE',
            'R1' => 'REPO',
            'S1' => 'SOUR',
            'U1' => 'SUBM',
        ];

        foreach ($records as $xref => $tag) {
            $import_service->importRecord("0 @$xref@ $tag", $tree, true);
        }
        $import_service->importRecord('0 @I2@ INDI', $other_tree, true);

        foreach (['I2', 'F1', 'L1', 'M1', 'N1', 'R1', 'S1', 'U1'] as $xref) {
            DB::query(table: 'link')->insert(values: [
                'l_file' => $tree->id(), 'l_from' => $xref, 'l_type' => 'TEST', 'l_to' => 'I1',
            ]);
        }
        DB::query(table: 'link')->insert(values: [
            'l_file' => $tree->id(), 'l_from' => 'I2', 'l_type' => 'OTHER', 'l_to' => 'I1',
        ]);
        DB::query(table: 'link')->insert(values: [
            'l_file' => $other_tree->id(), 'l_from' => 'I2', 'l_type' => 'TEST', 'l_to' => 'I1',
        ]);

        $record = Registry::individualFactory()->make('I1', $tree);
        self::assertInstanceOf(Individual::class, $record);
        $service = new LinkedRecordService();

        $lookups = [
            ['linkedFamilies', $service->linkedFamilies($record), Family::class, 'F1'],
            ['linkedIndividuals', $service->linkedIndividuals($record), Individual::class, 'I2'],
            ['linkedLocations', $service->linkedLocations($record), Location::class, 'L1'],
            ['linkedMedia', $service->linkedMedia($record), Media::class, 'M1'],
            ['linkedNotes', $service->linkedNotes($record), Note::class, 'N1'],
            ['linkedRepositories', $service->linkedRepositories($record), Repository::class, 'R1'],
            ['linkedSources', $service->linkedSources($record), Source::class, 'S1'],
            ['linkedSubmitters', $service->linkedSubmitters($record), Submitter::class, 'U1'],
        ];

        foreach ($lookups as [$method, $results, $class, $xref]) {
            self::assertCount(1, $results, $method);
            self::assertInstanceOf($class, $results->first());
            self::assertSame($xref, $results->first()->xref());
        }

        self::assertSame('I2', $service->linkedIndividuals($record, 'OTHER')->first()?->xref());
        self::assertCount(0, $service->linkedIndividuals($record, 'MISSING'));
        self::assertCount(0, $service->linkedFamilies($record, 'MISSING'));
    }

    public function testAllLinkedRecordsUsesLatestPendingChangesAndLiteralXrefs(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree_service = new TreeService($import_service);
        $tree = $tree_service->create('first', 'First');
        $other_tree = $tree_service->create('second', 'Second');

        foreach (['I_1' => 'INDI', 'I2' => 'INDI', 'S1' => 'SOUR', 'S2' => 'SOUR', 'S3' => 'SOUR'] as $xref => $tag) {
            $import_service->importRecord("0 @$xref@ $tag", $tree, true);
        }

        DB::query(table: 'link')->insert(values: [
            'l_file' => $tree->id(),
            'l_from' => 'I2',
            'l_to' => 'I_1',
            'l_type' => 'TEST',
        ]);

        $changes = [
            ['I2', "0 @I2@ INDI\n1 ASSO @I_1@", ChangeStatus::Pending->value, $tree->id()],
            ['S1', "0 @S1@ SOUR\n1 NOTE @I_1@", ChangeStatus::Pending->value, $tree->id()],
            ['S1', '0 @S1@ SOUR', ChangeStatus::Pending->value, $tree->id()],
            ['S2', "0 @S2@ SOUR\n1 NOTE @I_1@", ChangeStatus::Accepted->value, $tree->id()],
            ['S2', "0 @S2@ SOUR\n1 NOTE @I_1@", ChangeStatus::Pending->value, $tree->id()],
            ['S3', "0 @S3@ SOUR\n1 NOTE @IX1@", ChangeStatus::Pending->value, $tree->id()],
            ['S3', "0 @S3@ SOUR\n1 NOTE @I_1@", ChangeStatus::Accepted->value, $tree->id()],
            ['I_1', "0 @I_1@ INDI\n1 NOTE Self", ChangeStatus::Pending->value, $tree->id()],
            ['S3', "0 @S3@ SOUR\n1 NOTE @I_1@", ChangeStatus::Pending->value, $other_tree->id()],
        ];

        foreach ($changes as [$xref, $gedcom, $status, $tree_id]) {
            DB::query(table: 'change')->insert(values: [
                'gedcom_id' => $tree_id,
                'xref' => $xref,
                'old_gedcom' => '',
                'new_gedcom' => $gedcom,
                'status' => $status,
                'user_id' => $user->id(),
            ]);
        }

        $target = Registry::individualFactory()->make('I_1', $tree);
        self::assertInstanceOf(Individual::class, $target);

        $linked = (new LinkedRecordService())->allLinkedRecords($target);

        self::assertSame(['I2', 'S2'], $linked->map(static fn ($record): string => $record->xref())->sort()->values()->all());
    }
}
