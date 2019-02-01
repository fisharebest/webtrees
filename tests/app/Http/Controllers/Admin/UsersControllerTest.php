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
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'index');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testData(): void
    {
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'create');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'save');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testEdit(): void
    {
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'edit');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'update');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testCleanup(): void
    {
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'cleanup');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testCleanupAction(): void
    {
        $controller = app()->make(UsersController::class);
        $response   = app()->dispatch($controller, 'cleanupAction');

        $this->assertInstanceOf(Response::class, $response);
    }
}
