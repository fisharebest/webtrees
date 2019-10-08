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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Controllers\Admin\UsersController;
use Fisharebest\Webtrees\Services\UserService;

use function app;

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
        $admin        = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_GET, ['length' => '10',])->withAttribute('user', $admin);
        $response   = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        $this->assertContains('AdminName', $html);
        $this->assertContains('Administrator', $html);
        $this->assertContains('admin@example.com', $html);
        $this->assertContains('UserName', $html);
        $this->assertContains('RealName', $html);
        $this->assertContains('user@example.com', $html);
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testFilteringUserAdminPage(): void
    {
        $user_service = new UserService();
        $admin        = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = self::createRequest()
            ->withQueryParams(['search' => ['value' => 'admin']])
            ->withAttribute('user', $admin);

        $controller = app(UsersController::class);
        $response   = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        $this->assertContains('AdminName', $html);
        $this->assertContains('Administrator', $html);
        $this->assertContains('admin@example.com', $html);
        $this->assertNotContains('UserName', $html);
        $this->assertNotContains('RealName', $html);
        $this->assertNotContains('user@example.com', $html);
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testPaginatingUserAdminPage(): void
    {
        $user_service = new UserService();
        $admin        = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = self::createRequest()
            ->withQueryParams(['length' => 1])
            ->withAttribute('user', $admin);

        $controller = app(UsersController::class);
        $response   = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        $this->assertContains('AdminName', $html);
        $this->assertNotContains('UserName', $html);
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testSortingUserAdminPage(): void
    {
        $user_service = new UserService();

        $admin = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $request = self::createRequest()
            ->withQueryParams(['column' => 2, 'dir' => 'asc'])
            ->withAttribute('user', $admin);

        $response = app(UsersController::class)->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        $pos1 = strpos($html, 'AdminName');
        $pos2 = strpos($html, 'UserName');
        $this->assertLessThan($pos2, $pos1);

        $request = self::createRequest()
            ->withQueryParams(['order' => [['column' => 2, 'dir' => 'desc']]])
            ->withAttribute('user', $admin);

        $controller = app(UsersController::class);
        $response   = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        $pos1 = strpos($html, 'AdminName');
        $pos2 = strpos($html, 'UserName');
        $this->assertGreaterThan($pos2, $pos1);
    }
}
