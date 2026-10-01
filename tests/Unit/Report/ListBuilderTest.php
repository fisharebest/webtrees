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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Report\ListBuilder;
use Fisharebest\Webtrees\Report\VariableTable;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_map;

#[CoversClass(ListBuilder::class)]
class ListBuilderTest extends TestCase
{
    public function testFiltersIndividualsByLiteralNameAndDate(): void
    {
        self::createDatabase();

        $tree           = (new TreeService(new GedcomImportService()))->create('report', 'Report');
        $import_service = new GedcomImportService();

        $import_service->importRecord("0 @I1@ INDI\n1 NAME 100% /Smith/\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME 100% /Smith/\n1 BIRT\n2 DATE 1 JAN 2000", $tree, true);
        $import_service->importRecord("0 @I3@ INDI\n1 NAME 1000 /Smith/\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);

        $records = (new ListBuilder($tree))->buildIndividualList(
            ['filter1' => 'NAME CONTAINS %', 'filter2' => 'BIRT:DATE LTE 1 JAN 1950'],
            '',
            static fn (string $value, bool $is_html): string => $value,
            '',
            '',
            '',
            new VariableTable([]),
        );

        self::assertSame(['I1'], array_map(static fn (GedcomRecord $record): string => $record->xref(), $records));
    }

    public function testFiltersIndividualsByLiteralPlaceSubstring(): void
    {
        self::createDatabase();

        $tree           = (new TreeService(new GedcomImportService()))->create('report', 'Report');
        $import_service = new GedcomImportService();

        $import_service->importRecord("0 @I1@ INDI\n1 BIRT\n2 DATE 1 JAN 1900\n2 PLAC Some_place, England", $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 BIRT\n2 DATE 1 JAN 1900\n2 PLAC Someplace, England", $tree, true);

        $records = (new ListBuilder($tree))->buildIndividualList(
            ['filter1' => 'BIRT:PLAC CONTAINS Some_place'],
            '',
            static fn (string $value, bool $is_html): string => $value,
            '',
            '',
            '',
            new VariableTable([]),
        );

        self::assertSame(['I1'], array_map(static fn (GedcomRecord $record): string => $record->xref(), $records));
    }

    public function testFiltersFamiliesByEitherSpousesLiteralName(): void
    {
        self::createDatabase();

        $tree           = (new TreeService(new GedcomImportService()))->create('report', 'Report');
        $import_service = new GedcomImportService();

        $import_service->importRecord("0 @I1@ INDI\n1 NAME 100_ /Smith/\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME 1000 /Smith/\n1 BIRT\n2 DATE 1 JAN 1900", $tree, true);
        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @I1@\n1 WIFE @I2@", $tree, true);
        $import_service->importRecord("0 @F2@ FAM\n1 HUSB @I2@\n1 WIFE @I1@", $tree, true);
        $import_service->importRecord("0 @F3@ FAM\n1 HUSB @I2@\n1 WIFE @I2@", $tree, true);

        $records = (new ListBuilder($tree))->buildFamilyList(
            ['filter1' => 'NAME CONTAINS _'],
            '',
            static fn (string $value, bool $is_html): string => $value,
            '',
            '',
            '',
            new VariableTable([]),
        );

        self::assertEqualsCanonicalizing(['F1', 'F2'], array_map(static fn (GedcomRecord $record): string => $record->xref(), $records));
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(ListBuilder::class));
    }

    public function testListsOnlyTheLatestPendingVersionOfEachRecord(): void
    {
        self::createDatabase();

        $user         = (new UserService(new SystemClock()))->create('report', 'Report', 'report@example.com', 'secret');
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('report', 'Report');
        $other_tree   = $tree_service->create('other', 'Other');
        $builder      = new ListBuilder($tree);
        $variables    = new VariableTable([]);

        $rows = [
            [$tree->id(), 'I1', 'Old pending', 'pending'],
            [$tree->id(), 'I1', 'Latest pending', 'pending'],
            [$tree->id(), 'I2', 'Accepted', 'accepted'],
            [$other_tree->id(), 'I3', 'Other tree', 'pending'],
        ];

        foreach ($rows as [$tree_id, $xref, $name, $status]) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $tree_id,
                'xref'       => $xref,
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => '0 @' . $xref . "@ INDI\n1 NAME " . $name,
                'user_id'    => $user->id(),
            ]);
        }

        $records = $builder->buildChangeList([], '', '', '', '', $variables);

        self::assertCount(1, $records);
        self::assertSame('I1', $records[0]->xref());
        self::assertStringContainsString('Latest pending', $records[0]->gedcom());
    }

    public function testReturnsEmptyListWithoutPendingChanges(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('empty', 'Empty');

        self::assertSame([], (new ListBuilder($tree))->buildChangeList([], '', '', '', '', new VariableTable([])));
    }
}
