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
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test the module admin controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\ModuleController
 */
class ModuleControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testList(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->list($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListAnalytics(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listAnalytics($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListBlocks(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listBlocks($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListCharts(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listCharts($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListFooters(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listFooters($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListHistory(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listHistory($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListLanguages(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listLanguages($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListMenus(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listMenus($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListReports(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listReports($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListSidebars(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listSidebars($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListTabs(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listTabs($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListThemes(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest();
        $response   = $controller->listThemes($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->update($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateAnalytics(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateAnalytics($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateBlocks(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateBlocks($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateCharts(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateCharts($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateFooters(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateFooters($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateHistory(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateHistory($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateLanguages(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateLanguages($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateMenus(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateMenus($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateReports(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateReports($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateSidebars(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateSidebars($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateTabs(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateTabs($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateThemes(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST);
        $response   = $controller->updateThemes($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteModuleSettings(): void
    {
        $controller = new ModuleController(new ModuleService(), new TreeService());
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST, [], ['module_name' => 'foo']);
        $response   = $controller->deleteModuleSettings($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
