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

use Fisharebest\Webtrees\Http\Controllers\AdminUsersController;
use Fisharebest\Webtrees\Services\DatatablesService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test the user administration pages
 */
class UserAdminTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\AdminUsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testUserDetailsAreShownOnUserAdminPage(): void
    {
        $admin = User::create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user  = User::create('UserName', 'RealName', 'user@example.com', 'secret');

        $controller = app()->make(AdminUsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertContains('AdminName', $response->getContent());
        $this->assertContains('Administrator', $response->getContent());
        $this->assertContains('admin@example.com', $response->getContent());
        $this->assertContains('UserName', $response->getContent());
        $this->assertContains('RealName', $response->getContent());
        $this->assertContains('user@example.com', $response->getContent());
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\AdminUsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testFilteringUserAdminPage(): void
    {
        $admin = User::create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user  = User::create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = new Request(['search' => ['value' => 'admin']]);
        app()->instance(Request::class, $request);
        $controller = app()->make(AdminUsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertContains('AdminName', $response->getContent());
        $this->assertContains('Administrator', $response->getContent());
        $this->assertContains('admin@example.com', $response->getContent());
        $this->assertNotContains('UserName', $response->getContent());
        $this->assertNotContains('RealName', $response->getContent());
        $this->assertNotContains('user@example.com', $response->getContent());
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\AdminUsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testPaginatingUserAdminPage(): void
    {
        $admin = User::create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user  = User::create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = new Request(['length' => 1]);
        app()->instance(Request::class, $request);
        $controller = app()->make(AdminUsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertContains('AdminName', $response->getContent());
        $this->assertNotContains('UserName', $response->getContent());
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\AdminUsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testSortingUserAdminPage(): void
    {
        $admin = User::create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user  = User::create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = new Request(['order' => [['column' => 2, 'dir' => 'asc']]]);
        app()->instance(Request::class, $request);
        $controller = app()->make(AdminUsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $pos1     = strpos($response->getContent(), 'AdminName');
        $pos2     = strpos($response->getContent(), 'UserName');
        $this->assertLessThan($pos2, $pos1);

        $request = new Request(['order' => [['column' => 2, 'dir' => 'desc']]]);
        app()->instance(Request::class, $request);
        $controller = app()->make(AdminUsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $pos1     = strpos($response->getContent(), 'AdminName');
        $pos2     = strpos($response->getContent(), 'UserName');
        $this->assertGreaterThan($pos2, $pos1);
    }
}
