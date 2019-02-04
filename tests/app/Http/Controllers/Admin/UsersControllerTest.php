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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test UsersController class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
 */
class UsersControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->index(new Request(), Auth::user());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testData(): void
    {
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->data(new DatatablesService(), new Request(), Auth::User());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->create(new Request());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->save(new Request());

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEdit(): void
    {
        $user       = UserService::create('user', 'real', 'email', 'pass');
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->edit(new Request(['user_id' => $user->id()]));

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        /** @var User $user */
        $user       = UserService::create('user', 'real', 'email', 'pass');
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->update(new Request(['user_id' => $user->id()]), $user);

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCleanup(): void
    {
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->cleanup(new Request());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCleanupAction(): void
    {
        $controller = new UsersController(new ModuleService(), new UserService());
        $response   = $controller->cleanupAction(new Request());

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
