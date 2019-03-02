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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test MediaController class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\MediaController
 */
class MediaControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $controller = app(MediaController::class);
        $response   = app()->dispatch($controller, 'index');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testDataLocal(): void
    {
        app()->instance(Request::class, new Request(['files' => 'local']));
        $controller = app(MediaController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testDataExternal(): void
    {
        app()->instance(Request::class, new Request(['files' => 'external']));
        $controller = app(MediaController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testDataUnused(): void
    {
        app()->instance(Request::class, new Request(['files' => 'unused']));
        $controller = app(MediaController::class);
        $response   = app()->dispatch($controller, 'data');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testDelete(): void
    {
        $controller = app(MediaController::class);
        $response   = app()->dispatch($controller, 'delete');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testUpload(): void
    {
        $controller = app(MediaController::class);
        $response   = app()->dispatch($controller, 'upload');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testUploadAction(): void
    {
        $controller = app(MediaController::class);
        $response   = app()->dispatch($controller, 'uploadAction');

        $this->assertInstanceOf(Response::class, $response);
    }
}
