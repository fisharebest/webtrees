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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test FixLevel0MediaControllerTest class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\FixLevel0MediaController
 */
class FixLevel0MediaControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testFixLevel0Media(): void
    {
        $controller = app()->make(FixLevel0MediaController::class);
        $response   = app()->dispatch($controller, 'fixLevel0Media');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testFixLevel0MediaAction(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Request::class, new Request([], ['tree_id' => $tree->id()]));

        $controller = app()->make(FixLevel0MediaController::class);
        $response   = app()->dispatch($controller, 'fixLevel0MediaAction');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testFixLevel0MediaData(): void
    {
        // Can't test this yet - the query uses MySQL-specific functions
        //$controller = app()->make(FixLevel0MediaController::class);
        //$response   = app()->dispatch($controller, 'fixLevel0MediaData');

        //$this->assertInstanceOf(Response::class, $response);
    }
}
