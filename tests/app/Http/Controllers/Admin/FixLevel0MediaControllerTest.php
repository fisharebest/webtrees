<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test FixLevel0MediaControllerTest class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\FixLevel0MediaController
 */
class FixLevel0MediaControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testFixLevel0Media(): void
    {
        $datatables_service = new DatatablesService();
        $tree_service       = new TreeService();
        $controller         = new FixLevel0MediaController($datatables_service, $tree_service);
        $request    = self::createRequest();
        $response   = $controller->fixLevel0Media($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testFixLevel0MediaAction(): void
    {
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $controller   = new FixLevel0MediaController(new DatatablesService(), $tree_service);
        $request      = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'tree_id'   => $tree->id(),
            'fact_id'   => '',
            'indi_xref' => '',
            'obje_xref' => '',
        ]);
        $response   = $controller->fixLevel0MediaAction($request);

        $this->assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testFixLevel0MediaData(): void
    {
        $datatables_service = new DatatablesService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $controller         = new FixLevel0MediaController($datatables_service, $tree_service);
        $request      = self::createRequest(RequestMethodInterface::METHOD_GET, ['tree_id' => $tree->id()]);
        $response     = $controller->fixLevel0MediaData($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
