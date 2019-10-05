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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction
 */
class ModuleActionTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testModuleAction(): void
    {
        $tree = Tree::create('tree', 'tree');
        app()->instance(Tree::class, $tree);
        $user           = new GuestUser();
        $module_service = new ModuleService();
        $handler        = new ModuleAction($module_service, $user);
        $request        = self::createRequest(self::METHOD_GET, ['route' => 'module', 'module' => 'faq', 'action' => 'Show', 'ged' => $tree->name()])
            ->withAttribute('tree', $tree);

        app()->instance(ServerRequestInterface::class, $request);

        $response = $handler->handle($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Method getFishAction() not found in faq
     * @return void
     */
    public function testNonExistingAction(): void
    {
        $user           = new GuestUser();
        $module_service = new ModuleService();
        $handler        = new ModuleAction($module_service, $user);
        $request        = self::createRequest(self::METHOD_GET, ['route' => 'module', 'module' => 'faq', 'action' => 'Fish']);
        $handler->handle($request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Module fish does not exist
     * @return void
     */
    public function testNonExistingModule(): void
    {
        $user           = new GuestUser();
        $module_service = new ModuleService();
        $handler        = new ModuleAction($module_service, $user);
        $request        = self::createRequest(self::METHOD_GET, ['route' => 'module', 'module' => 'fish', 'action' => 'Show']);
        $response       = $handler->handle($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @expectedExceptionMessage Admin only action
     * @return void
     */
    public function testAdminAction(): void
    {
        $tree = Tree::create('tree', 'tree');
        app()->instance(Tree::class, $tree);
        $user           = new GuestUser();
        $module_service = new ModuleService();
        $handler        = new ModuleAction($module_service, $user);
        $request        = self::createRequest(self::METHOD_GET, ['route' => 'module', 'module' => 'faq', 'action' => 'Admin', 'ged' => $tree->name()]);
        $handler->handle($request);
    }
}
