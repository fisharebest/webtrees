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

use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Http\Request;

/**
 * Test the changes log controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\ChangesLogController
 */
class ChangesLogControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testChangeLog(): void
    {
        $request = self::createRequest('GET', ['route' => 'admin-changes-log']);

        $controller = new ChangesLogController();
        $response   = $controller->changesLog($request, new UserService());
        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testChangeLogData(): void
    {
        $tree = Tree::create('name', 'title');
        $user = (new UserService)->create('user', 'name', 'email', 'password');
        Auth::login($user);
        $individual = $tree->createIndividual("0 @@ INDI\n1 NAME Joe Bloggs");

        $request = self::createRequest('GET', [
            'route' => 'admin-changes-log-data',
            'search' => 'Joe',
            'from'   => '2000-01-01',
            'to'     => '2099-12-31',
            'type'   => 'pending',
            'xref'   => $individual->xref(),
            'ged'    => $tree->name(),
            'user'   => $user->userName(),
        ]);
        $controller = new ChangesLogController();
        $response   = $controller->changesLogData($request, new DatatablesService(), new MyersDiff());

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testChangeLogDownload(): void
    {
        $tree = Tree::create('name', 'title');
        $user = (new UserService)->create('user', 'name', 'email', 'password');
        Auth::login($user);
        $tree->createIndividual("0 @@ INDI\n1 NAME Joe Bloggs");

        $request = self::createRequest('GET', [
            'route' => 'admin-changes-log-download',
            'ged'    => $tree->name(),
        ]);
        $controller = new ChangesLogController();
        $response   = $controller->changesLogDownload($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }
}
