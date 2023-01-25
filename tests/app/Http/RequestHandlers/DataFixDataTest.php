<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test DataFixDataTest class.
 *
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\DataFixData
 * @covers \Fisharebest\Webtrees\Module\FixSearchAndReplace
 */
class DataFixDataTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * Test request handler
     */
    public function testHandlerForFixSearchAndReplace(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');

        $data_fix_service = new DataFixService();
        $datatables_service = new DatatablesService();
        $module_service = new ModuleService();

        $handler = new DataFixData($data_fix_service, $datatables_service, $module_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, [
                'type'       => Individual::RECORD_TYPE,
                'search-for' => 'DOE',
                'method'     => 'exact',
                'case'       =>  ''
            ])
            ->withAttribute('tree', $tree)
            ->withAttribute('data_fix', 'fix-search-and-replace');

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
