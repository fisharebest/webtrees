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

use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Http\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Test the module admin controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\ModuleController
 */
class ModuleControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testList(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'list');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListAnalytics(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listAnalytics');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListBlocks(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listBlocks');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListCharts(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listCharts');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListFooters(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listFooters');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListHistory(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listHistory');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListLanguages(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listLanguages');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListMenus(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listMenus');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListReports(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listReports');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListSidebars(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listSidebars');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListTabs(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listTabs');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testListThemes(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'listThemes');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'update');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateAnalytics(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateAnalytics');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateBlocks(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateBlocks');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateCharts(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateCharts');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateFooters(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateFooters');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateHistory(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateHistory');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateLanguages(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateLanguages');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateMenus(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateMenus');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateReports(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateReports');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateSidebars(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateSidebars');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateTabs(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateTabs');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdateThemes(): void
    {
        Tree::create('name', 'title');
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'updateThemes');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testDeleteModuleSettings(): void
    {
        $controller = app(ModuleController::class);
        $response   = app()->dispatch($controller, 'deleteModuleSettings');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
