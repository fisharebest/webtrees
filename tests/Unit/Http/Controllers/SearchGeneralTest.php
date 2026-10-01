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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Http\Controllers\SearchGeneral;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchGeneral::class)]
class SearchGeneralTest extends TestCase
{
    public function testRecordTypeOptionsExistOnlyForTheCurrentTree(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        Registry::container()->set(ModuleThemeInterface::class, self::createStub(ModuleThemeInterface::class));
        $controller   = new SearchGeneral(new SearchService($tree_service), $tree_service);
        $request      = self::createRequest(attributes: ['tree' => $tree]);

        DB::query(table: 'other')->insertBatch([
            ['o_id' => 'N1', 'o_file' => $other_tree->id(), 'o_type' => Note::RECORD_TYPE, 'o_gedcom' => '0 @N1@ NOTE Text'],
            ['o_id' => 'L1', 'o_file' => $other_tree->id(), 'o_type' => Location::RECORD_TYPE, 'o_gedcom' => '0 @L1@ _LOC'],
            ['o_id' => 'R1', 'o_file' => $other_tree->id(), 'o_type' => Repository::RECORD_TYPE, 'o_gedcom' => '0 @R1@ REPO'],
        ]);
        DB::query(table: 'sources')->insert([
            's_id' => 'S1', 's_file' => $other_tree->id(), 's_name' => 'Source', 's_gedcom' => '0 @S1@ SOUR',
        ]);

        $empty_html = (string) $controller->get($request, $tree)->getBody();
        self::assertStringNotContainsString('name="search_notes"', $empty_html);
        self::assertStringNotContainsString('name="search_locations"', $empty_html);
        self::assertStringNotContainsString('name="search_repositories"', $empty_html);
        self::assertStringNotContainsString('name="search_sources"', $empty_html);

        DB::query(table: 'other')->insertBatch([
            ['o_id' => 'N1', 'o_file' => $tree->id(), 'o_type' => Note::RECORD_TYPE, 'o_gedcom' => '0 @N1@ NOTE Text'],
            ['o_id' => 'L1', 'o_file' => $tree->id(), 'o_type' => Location::RECORD_TYPE, 'o_gedcom' => '0 @L1@ _LOC'],
            ['o_id' => 'R1', 'o_file' => $tree->id(), 'o_type' => Repository::RECORD_TYPE, 'o_gedcom' => '0 @R1@ REPO'],
        ]);
        DB::query(table: 'sources')->insert([
            's_id' => 'S1', 's_file' => $tree->id(), 's_name' => 'Source', 's_gedcom' => '0 @S1@ SOUR',
        ]);

        $html = (string) $controller->get($request, $tree)->getBody();
        self::assertStringContainsString('name="search_notes"', $html);
        self::assertStringContainsString('name="search_locations"', $html);
        self::assertStringContainsString('name="search_repositories"', $html);
        self::assertStringContainsString('name="search_sources"', $html);
    }
}
