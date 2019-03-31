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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Http\Controllers\Admin\UsersController;
use Fisharebest\Webtrees\Services\UserService;

/**
 * Test the user administration pages
 */
class UserAdminTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testUserDetailsAreShownOnUserAdminPage(): void
    {
        $user_service = new UserService();
        $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $controller = app(UsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertContains('AdminName', $response->getContent());
        $this->assertContains('Administrator', $response->getContent());
        $this->assertContains('admin@example.com', $response->getContent());
        $this->assertContains('UserName', $response->getContent());
        $this->assertContains('RealName', $response->getContent());
        $this->assertContains('user@example.com', $response->getContent());
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testFilteringUserAdminPage(): void
    {
        $user_service = new UserService();
        $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = new Request(['search' => ['value' => 'admin']]);
        app()->instance(Request::class, $request);
        $controller = app(UsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertContains('AdminName', $response->getContent());
        $this->assertContains('Administrator', $response->getContent());
        $this->assertContains('admin@example.com', $response->getContent());
        $this->assertNotContains('UserName', $response->getContent());
        $this->assertNotContains('RealName', $response->getContent());
        $this->assertNotContains('user@example.com', $response->getContent());
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testPaginatingUserAdminPage(): void
    {
        $user_service = new UserService();
        $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = new Request(['length' => 1]);
        app()->instance(Request::class, $request);
        $controller = app(UsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertContains('AdminName', $response->getContent());
        $this->assertNotContains('UserName', $response->getContent());
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testSortingUserAdminPage(): void
    {
        $user_service = new UserService();
        $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = new Request(['order' => [['column' => 2, 'dir' => 'asc']]]);
        app()->instance(Request::class, $request);
        $controller = app(UsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $pos1 = strpos($response->getContent(), 'AdminName');
        $pos2 = strpos($response->getContent(), 'UserName');
        $this->assertLessThan($pos2, $pos1);

        $request = new Request(['order' => [['column' => 2, 'dir' => 'desc']]]);
        app()->instance(Request::class, $request);
        $controller = app(UsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $pos1 = strpos($response->getContent(), 'AdminName');
        $pos2 = strpos($response->getContent(), 'UserName');
        $this->assertGreaterThan($pos2, $pos1);
    }
}
