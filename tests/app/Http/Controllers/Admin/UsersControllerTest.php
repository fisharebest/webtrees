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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test UsersController class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UsersController
 */
class UsersControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $controller         = new UsersController($datatables_service, $mail_service, $module_service, $tree_service, $user_service);
        $request            = self::createRequest()
            ->withAttribute('user', Auth::user());
        $response           = $controller->index($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testData(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $controller         = new UsersController($datatables_service, $mail_service, $module_service, $tree_service, $user_service);
        $request            = self::createRequest();
        $response           = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $controller         = new UsersController($datatables_service, $mail_service, $module_service, $tree_service, $user_service);
        $request            = self::createRequest();
        $response   = $controller->create($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $controller         = new UsersController($datatables_service, $mail_service, $module_service, $tree_service, $user_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username'  => 'User name',
            'email'     => 'email@example.com',
            'real_name' => 'Real Name',
            'password'  => 'Secret1234',
        ]);
        $response           = $controller->save($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEdit(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $user               = $user_service->create('user', 'real', 'email', 'pass');
        $controller         = new UsersController($datatables_service, $mail_service, $module_service, $tree_service, $user_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_GET, ['user_id' => (string) $user->id()]);
        $response           = $controller->edit($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new EmailService();
        $module_service     = new ModuleService();
        $tree_service       = new TreeService();
        $user_service       = new UserService();
        $user               = $user_service->create('user', 'real', 'email', 'pass');
        $controller         = new UsersController($datatables_service, $mail_service, $module_service, $tree_service, $user_service);
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'user_id'        => $user->id(),
            'username'       => '',
            'real_name'      => '',
            'email'          => '',
            'theme'          => '',
            'password'       => '',
            'language'       => '',
            'timezone'       => '',
            'comment'        => '',
            'contact-method' => '',
            'auto_accept'    => '',
            'canadmin'       => '',
            'visible-online' => '',
            'verified'       => '',
            'approved'       => '',
        ])
            ->withAttribute('user', $user);
        $response   = $controller->update($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
