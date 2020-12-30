<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Controllers\Admin\UsersController;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
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
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $message_service    = new MessageService($mail_service, $user_service);
        $admin              = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $controller = new UsersController($datatables_service, $mail_service, $message_service, $module_service, $tree_service, $user_service);
        $request    = self::createRequest()
            ->withQueryParams(['length' => '10'])
            ->withAttribute('user', $admin);
        $response   = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        self::assertStringContainsString('AdminName', $html);
        self::assertStringContainsString('Administrator', $html);
        self::assertStringContainsString('admin@example.com', $html);
        self::assertStringContainsString('UserName', $html);
        self::assertStringContainsString('RealName', $html);
        self::assertStringContainsString('user@example.com', $html);
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testFilteringUserAdminPage(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $message_service    = new MessageService($mail_service, $user_service);
        $admin              = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $controller = new UsersController($datatables_service, $mail_service, $message_service, $module_service, $tree_service, $user_service);
        $request    = self::createRequest()
            ->withQueryParams(['search' => ['value' => 'admin']])
            ->withAttribute('user', $admin);
        $response   = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        self::assertStringContainsString('AdminName', $html);
        self::assertStringContainsString('Administrator', $html);
        self::assertStringContainsString('admin@example.com', $html);
        self::assertStringNotContainsString('UserName', $html);
        self::assertStringNotContainsString('RealName', $html);
        self::assertStringNotContainsString('user@example.com', $html);
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testPaginatingUserAdminPage(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $message_service    = new MessageService($mail_service, $user_service);
        $admin              = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $controller = new UsersController($datatables_service, $mail_service, $message_service, $module_service, $tree_service, $user_service);
        $request    = self::createRequest()
            ->withQueryParams(['length' => 1])
            ->withAttribute('user', $admin);
        $response   = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        self::assertStringContainsString('AdminName', $html);
        self::assertStringNotContainsString('UserName', $html);
    }

    /**
     * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
     * @covers \Fisharebest\Webtrees\Services\DatatablesService
     * @return void
     */
    public function testSortingUserAdminPage(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $message_service    = new MessageService($mail_service, $user_service);

        $admin = $user_service->create('AdminName', 'Administrator', 'admin@example.com', 'secret');
        $user_service->create('UserName', 'RealName', 'user@example.com', 'secret');

        $controller = new UsersController($datatables_service, $mail_service, $message_service, $module_service, $tree_service, $user_service);
        $request    = self::createRequest()
            ->withQueryParams(['column' => 2, 'dir' => 'asc'])
            ->withAttribute('user', $admin);
        $response   = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        $pos1 = strpos($html, 'AdminName');
        $pos2 = strpos($html, 'UserName');
        self::assertLessThan($pos2, $pos1);

        $request = self::createRequest()
            ->withQueryParams(['order' => [['column' => 2, 'dir' => 'desc']]])
            ->withAttribute('user', $admin);

        $controller = new UsersController($datatables_service, $mail_service, $message_service, $module_service, $tree_service, $user_service);
        $response   = $controller->data($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $html = (string) $response->getBody();

        $pos1 = strpos($html, 'AdminName');
        $pos2 = strpos($html, 'UserName');
        self::assertGreaterThan($pos2, $pos1);
    }
}
