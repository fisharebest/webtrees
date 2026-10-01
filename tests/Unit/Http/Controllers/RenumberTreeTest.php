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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Controllers\RenumberTree;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function strlen;
use function substr;

#[CoversClass(RenumberTree::class)]
class RenumberTreeTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string, string, array<string, int|string>}>
     */
    public static function recordTypes(): iterable
    {
        yield 'family' => ['families', 'f_file', 'f_id', 'FAM', ['f_numchil' => 0]];
        yield 'source' => ['sources', 's_file', 's_id', 'SOUR', ['s_name' => 'Test source']];
        yield 'repository' => ['other', 'o_file', 'o_id', 'REPO', ['o_type' => 'REPO']];
        yield 'note' => ['other', 'o_file', 'o_id', 'NOTE', ['o_type' => 'NOTE']];
        yield 'media' => ['media', 'm_file', 'm_id', 'OBJE', []];
        yield 'other' => ['other', 'o_file', 'o_id', 'SUBM', ['o_type' => 'SUBM']];
    }

    /**
     * @return iterable<string, array{string, string, string, string, string, array<string, int|string>}>
     */
    public static function sourceLinkTargets(): iterable
    {
        yield 'individual' => ['individuals', 'i_file', 'i_id', 'i_gedcom', 'INDI', ['i_rin' => '', 'i_sex' => 'U']];
        yield 'family' => ['families', 'f_file', 'f_id', 'f_gedcom', 'FAM', ['f_numchil' => 0]];
        yield 'media' => ['media', 'm_file', 'm_id', 'm_gedcom', 'OBJE', []];
        yield 'other' => ['other', 'o_file', 'o_id', 'o_gedcom', 'SUBM', ['o_type' => 'SUBM']];
    }

    /**
     * @param array<string, int|string> $additional_columns
     */
    #[DataProvider('sourceLinkTargets')]
    public function testLinkedSourceUpdatesOnlyMatchingRecords(
        string $table,
        string $file_column,
        string $xref_column,
        string $gedcom_column,
        string $record_type,
        array $additional_columns,
    ): void {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');

        foreach ([$tree, $other_tree] as $family_tree) {
            DB::query(table: 'sources')->insert([
                's_file'   => $family_tree->id(),
                's_id'     => 'S1',
                's_name'   => 'Renumber',
                's_gedcom' => '0 @S1@ SOUR',
            ]);
        }

        foreach (['A1', 'A2'] as $xref) {
            DB::query(table: $table)->insert([
                $file_column       => $tree->id(),
                $xref_column       => $xref,
                $gedcom_column     => "0 @$xref@ $record_type\n1 SOUR @S1@",
                ...$additional_columns,
            ]);
        }

        DB::query(table: $table)->insert([
            $file_column       => $other_tree->id(),
            $xref_column       => 'B1',
            $gedcom_column     => "0 @B1@ $record_type\n1 SOUR @S1@",
            ...$additional_columns,
        ]);

        foreach (
            [
                [$tree->id(), 'A1', 'SOUR'],
                [$tree->id(), 'A2', 'NOTE'],
                [$other_tree->id(), 'B1', 'SOUR'],
            ] as [$tree_id, $xref, $tag]
        ) {
            DB::query(table: 'link')->insert([
                'l_file' => $tree_id,
                'l_from' => $xref,
                'l_to'   => 'S1',
                'l_type' => $tag,
            ]);
        }

        self::createRequest(attributes: ['tree' => $tree]);
        (new RenumberTree(
            new AdminService(),
            new TimeoutService(new PhpService(), new SystemClock()),
        ))->post($tree);

        $new_source_xref = DB::query(table: 'sources')
            ->whereEqual('s_file', $tree->id())
            ->whereEqual('s_name', 'Renumber')
            ->value('s_id');
        self::assertNotSame('S1', $new_source_xref);

        self::assertSame(
            "0 @A1@ $record_type\n1 SOUR @$new_source_xref@",
            DB::query(table: $table)->whereEqual($file_column, $tree->id())->whereEqual($xref_column, 'A1')->value($gedcom_column),
        );
        self::assertSame(
            "0 @A2@ $record_type\n1 SOUR @S1@",
            DB::query(table: $table)->whereEqual($file_column, $tree->id())->whereEqual($xref_column, 'A2')->value($gedcom_column),
        );
        self::assertSame(
            "0 @B1@ $record_type\n1 SOUR @S1@",
            DB::query(table: $table)->whereEqual($file_column, $other_tree->id())->whereEqual($xref_column, 'B1')->value($gedcom_column),
        );
    }

    public function testIndividualLinksRequireMatchingTreeRecordAndTag(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');

        foreach (['F1', 'F2', 'F3'] as $xref) {
            $family_tree = $xref === 'F3' ? $other_tree : $tree;
            DB::query(table: 'families')->insert([
                'f_file'    => $family_tree->id(),
                'f_id'      => $xref,
                'f_gedcom'  => "0 @$xref@ FAM\n1 CHIL @X1@",
                'f_numchil' => 1,
            ]);
        }

        foreach (['I2', 'I3'] as $xref) {
            DB::query(table: 'individuals')->insert([
                'i_file'   => $tree->id(),
                'i_id'     => $xref,
                'i_rin'    => '',
                'i_sex'    => 'U',
                'i_gedcom' => "0 @$xref@ INDI\n1 ALIA @X1@",
            ]);
        }

        foreach (
            [
                [$tree->id(), 'F1', 'CHIL'],
                [$tree->id(), 'F2', 'HUSB'],
                [$other_tree->id(), 'F3', 'CHIL'],
                [$tree->id(), 'I2', 'ALIA'],
                [$tree->id(), 'I3', 'ASSO'],
            ] as [$tree_id, $xref, $tag]
        ) {
            DB::query(table: 'link')->insert([
                'l_file' => $tree_id,
                'l_from' => $xref,
                'l_to'   => 'X1',
                'l_type' => $tag,
            ]);
        }

        self::createRequest(attributes: ['tree' => $tree]);
        (new RenumberTree(
            new AdminService(),
            new TimeoutService(new PhpService(), new SystemClock()),
        ))->post($tree);

        $new_xref = DB::query(table: 'link')
            ->whereEqual('l_file', $tree->id())
            ->whereEqual('l_from', 'F1')
            ->whereEqual('l_type', 'CHIL')
            ->value('l_to');
        self::assertNotSame('X1', $new_xref);

        self::assertSame(
            "0 @F1@ FAM\n1 CHIL @$new_xref@",
            DB::query(table: 'families')->whereEqual('f_file', $tree->id())->whereEqual('f_id', 'F1')->value('f_gedcom'),
        );
        self::assertSame(
            "0 @F2@ FAM\n1 CHIL @X1@",
            DB::query(table: 'families')->whereEqual('f_file', $tree->id())->whereEqual('f_id', 'F2')->value('f_gedcom'),
        );
        self::assertSame(
            "0 @F3@ FAM\n1 CHIL @X1@",
            DB::query(table: 'families')->whereEqual('f_file', $other_tree->id())->whereEqual('f_id', 'F3')->value('f_gedcom'),
        );
        self::assertSame(
            "0 @I2@ INDI\n1 ALIA @$new_xref@",
            DB::query(table: 'individuals')->whereEqual('i_file', $tree->id())->whereEqual('i_id', 'I2')->value('i_gedcom'),
        );
        self::assertSame(
            "0 @I3@ INDI\n1 ALIA @X1@",
            DB::query(table: 'individuals')->whereEqual('i_file', $tree->id())->whereEqual('i_id', 'I3')->value('i_gedcom'),
        );
    }

    /**
     * @param array<string, int|string> $additional_columns
     */
    #[DataProvider('recordTypes')]
    public function testRenumbersRecordHeadersWithoutChangingOtherTrees(
        string $table,
        string $file_column,
        string $xref_column,
        string $record_type,
        array $additional_columns,
    ): void {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');

        foreach ([$tree, $other_tree] as $family_tree) {
            DB::query(table: 'individuals')->whereEqual('i_file', $family_tree->id())->delete();
            DB::query(table: $table)->insert([
                $file_column                            => $family_tree->id(),
                $xref_column                            => 'A1',
                substr($xref_column, 0, 1) . '_gedcom' => "0 @A1@ $record_type\n1 NOTE Test",
                ...$additional_columns,
            ]);
        }

        self::createRequest(attributes: ['tree' => $tree]);
        (new RenumberTree(
            new AdminService(),
            new TimeoutService(new PhpService(), new SystemClock()),
        ))->post($tree);

        $new_record = DB::query(table: $table)
            ->select('*')
            ->whereEqual($file_column, $tree->id())
            ->whereNotEqual($xref_column, 'HEAD')
            ->first();

        self::assertNotNull($new_record);
        self::assertNotSame('A1', $new_record->$xref_column);
        $new_xref = $new_record->$xref_column;
        self::assertSame(
            "0 @$new_xref@ $record_type\n1 NOTE Test",
            $new_record->{substr($xref_column, 0, 1) . '_gedcom'},
        );
        self::assertSame(
            'A1',
            DB::query(table: $table)
                ->whereEqual($file_column, $other_tree->id())
                ->whereEqual($xref_column, 'A1')
                ->value($xref_column),
        );
    }

    public function testRenumbersAnIndividualAndUpdatesFamilyReferencesOnlyInItsTree(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');

        foreach ([$tree, $other_tree] as $family_tree) {
            $family_xref = $family_tree === $tree ? 'F1' : 'F2';
            DB::query(table: 'families')->insert([
                'f_file'    => $family_tree->id(),
                'f_id'      => $family_xref,
                'f_gedcom'  => "0 @$family_xref@ FAM\n1 HUSB @X1@\n1 WIFE @X1@",
                'f_husb'    => 'X1',
                'f_wife'    => 'X1',
                'f_numchil' => 0,
            ]);
        }

        self::createRequest(attributes: ['tree' => $tree]);

        $controller = new RenumberTree(
            new AdminService(),
            new TimeoutService(new PhpService(), new SystemClock()),
        );
        $controller->post($tree);

        $renumbered = DB::query(table: 'individuals')
            ->select('*')
            ->whereEqual('i_file', $tree->id())
            ->whereNotEqual('i_id', 'X1')
            ->first();

        self::assertNotNull($renumbered);
        self::assertSame(
            "0 @$renumbered->i_id@ INDI",
            substr($renumbered->i_gedcom, 0, strlen("0 @$renumbered->i_id@ INDI")),
        );
        self::assertSame('0 @X1@ INDI', substr(
            DB::query(table: 'individuals')->whereEqual('i_file', $other_tree->id())->value('i_gedcom'),
            0,
            strlen('0 @X1@ INDI'),
        ));

        $family = DB::query(table: 'families')->select('*')->whereEqual('f_file', $tree->id())->whereEqual('f_id', 'F1')->first();
        self::assertSame($renumbered->i_id, $family->f_husb);
        self::assertSame($renumbered->i_id, $family->f_wife);
        self::assertSame("0 @F1@ FAM\n1 HUSB @$renumbered->i_id@\n1 WIFE @$renumbered->i_id@", $family->f_gedcom);

        $other_family = DB::query(table: 'families')->select('*')->whereEqual('f_file', $other_tree->id())->whereEqual('f_id', 'F2')->first();
        self::assertSame('X1', $other_family->f_husb);
        self::assertSame('X1', $other_family->f_wife);
    }
}
