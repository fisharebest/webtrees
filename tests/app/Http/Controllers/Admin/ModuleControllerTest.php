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

use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;

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
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'modules']);
        $response   = $controller->list();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListAnalytics(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'analytics']);
        $response   = $controller->listAnalytics();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListBlocks(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'blocks']);
        $response   = $controller->listBlocks();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListCharts(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'charts']);
        $response   = $controller->listCharts();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListFooters(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'footers']);
        $response   = $controller->listFooters();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListHistory(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'history']);
        $response   = $controller->listHistory();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListLanguages(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'languages']);
        $response   = $controller->listLanguages();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListMenus(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'menus']);
        $response   = $controller->listMenus();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListReports(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'reports']);
        $response   = $controller->listReports();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListSidebars(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'sidebars']);
        $response   = $controller->listSidebars();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListTabs(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'tabs']);
        $response   = $controller->listTabs();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testListThemes(): void
    {
        $controller = new ModuleController(new ModuleService());
        self::createRequest('GET', ['route' => 'themes']);
        $response   = $controller->listThemes();

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'modules']);
        $response   = $controller->update($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateAnalytics(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'analytics']);
        $response   = $controller->updateAnalytics($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateBlocks(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'blocks']);
        $response   = $controller->updateBlocks($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateCharts(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'charts']);
        $response   = $controller->updateCharts($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateFooters(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'footers']);
        $response   = $controller->updateFooters($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateHistory(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'history']);
        $response   = $controller->updateHistory($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateLanguages(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'languages']);
        $response   = $controller->updateLanguages($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateMenus(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'menus']);
        $response   = $controller->updateMenus($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateReports(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'reports']);
        $response   = $controller->updateReports($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateSidebars(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'sidebars']);
        $response   = $controller->updateSidebars($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateTabs(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'tabs']);
        $response   = $controller->updateTabs($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateThemes(): void
    {
        Tree::create('name', 'title');
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'themes']);
        $response   = $controller->updateThemes($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testDeleteModuleSettings(): void
    {
        $controller = new ModuleController(new ModuleService());
        $request    = self::createRequest('POST', ['route' => 'delete-module-settings'], ['module_name' => 'foo']);
        $response   = $controller->deleteModuleSettings($request);

        $this->assertSame(self::STATUS_FOUND, $response->getStatusCode());
    }
}
