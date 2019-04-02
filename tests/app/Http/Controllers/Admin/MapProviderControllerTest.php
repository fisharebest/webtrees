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

use Fisharebest\Webtrees\Http\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Test the changes log controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\MapProviderController
 */
class MapProviderControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testMapProviderEdit(): void
    {
        $controller = app(MapProviderController::class);
        $response   = app()->dispatch($controller, 'mapProviderEdit');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @return void
     */
    public function testMapProviderSave(): void
    {
        $controller = app(MapProviderController::class);
        $response   = app()->dispatch($controller, 'mapProviderSave');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
