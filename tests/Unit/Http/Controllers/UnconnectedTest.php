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
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Controllers\Unconnected;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Unconnected::class)]
class UnconnectedTest extends TestCase
{
    public function testListsDisconnectedIndividualsAndOptionalAliasLinks(): void
    {
        self::createDatabase();

        $import_service = new GedcomImportService();
        $tree           = (new TreeService($import_service))->create('name', 'title');
        $tree->setPreference('PEDIGREE_ROOT_ID', 'X1');

        $import_service->importRecord("0 @i2@ INDI\n1 NAME Connected /Person/\n1 BIRT\n2 DATE 01 JAN 1850", $tree, true);
        $import_service->importRecord("0 @i3@ INDI\n1 NAME Isolated /Person/\n1 BIRT\n2 DATE 01 JAN 1850", $tree, true);
        $import_service->importRecord("0 @f1@ FAM\n1 HUSB @X1@\n1 WIFE @i2@", $tree, true);
        DB::query(table: 'link')->insertBatch([
            ['l_file' => $tree->id(), 'l_from' => 'X1', 'l_type' => 'FAMS', 'l_to' => 'f1'],
            ['l_file' => $tree->id(), 'l_from' => 'i2', 'l_type' => 'FAMS', 'l_to' => 'f1'],
            ['l_file' => $tree->id(), 'l_from' => 'i3', 'l_type' => 'ALIA', 'l_to' => 'X1'],
        ]);

        $controller = new Unconnected(new GuestUser());
        $request    = self::createRequest(attributes: ['tree' => $tree]);
        $html       = (string) $controller->get($request, $tree)->getBody();

        self::assertStringContainsString('Isolated', $html);
        self::assertStringNotContainsString('Connected', $html);

        $aliases_request = self::createRequest(query: ['aliases' => '1'], attributes: ['tree' => $tree]);
        $aliases_html    = (string) $controller->get($aliases_request, $tree)->getBody();

        self::assertStringNotContainsString('Isolated', $aliases_html);
    }
}
