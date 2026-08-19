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

use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\DataFixData;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\FixSearchAndReplace;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DataFixData::class)]
#[CoversClass(FixSearchAndReplace::class)]
class DataFixDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testHandlerForFixSearchAndReplace(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');

        $data_fix_service   = new DataFixService();
        $datatables_service = new DatatablesService();
        $module_service     = new ModuleService();

        $controller = new DataFixData($data_fix_service, $datatables_service, $module_service);

        $request = self::createRequest(HttpRequestMethod::POST->value, [], [
            'type'       => Individual::RECORD_TYPE,
            'search-for' => 'DOE',
            'method'     => 'exact',
            'case'       => '',
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('data_fix', 'fix-search-and-replace');

        $response = $controller->get($request, $tree);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }
}
