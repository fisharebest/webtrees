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
use Fisharebest\Webtrees\Services\MailService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;

use function app;

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
        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_GET, ['route' => 'admin-users'])
            ->withAttribute('user', Auth::user());
        $response   = $controller->index($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testData(): void
    {
        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_GET, ['route' => 'admin-users-data']);
        $response   = $controller->data($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_GET, ['route' => 'admin-users-create']);
        $response   = $controller->create($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testSave(): void
    {
        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST, ['route' => 'admin-users-create'], [
            'username'  => 'User name',
            'email'     => 'email@example.com',
            'real_name' => 'Real Name',
            'password'  => 'Secret1234',
        ]);
        $response   = $controller->save($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testEdit(): void
    {
        $user       = (new UserService())->create('user', 'real', 'email', 'pass');
        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_GET, ['route' => 'admin-users-edit', 'user_id' => (string) $user->id()]);
        $response   = $controller->edit($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        /** @var User $user */
        $user       = app(UserService::class)->create('user', 'real', 'email', 'pass');
        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_POST, ['route' => 'admin-users-edit'], [
            'user_id'        => $user->id(),
            'username'       => '',
            'real_name'      => '',
            'email'          => '',
            'theme'          => '',
            'password'       => '',
            'language'       => '',
            'timezone'       => '',
            'comment'        => '',
            'contact_method' => '',
            'auto_accept'    => '',
            'canadmin'       => '',
            'visible_online' => '',
            'verified'       => '',
            'approved'       => '',
        ])
            ->withAttribute('user', $user);
        $response   = $controller->update($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCleanup(): void
    {
        $controller = app(UsersController::class);
        $request    = self::createRequest(RequestMethodInterface::METHOD_GET, ['route' => 'admin-users-cleanup']);
        $response   = $controller->cleanup($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCleanupAction(): void
    {
        $datatables_service = new DatatablesService();
        $mail_service       = new MailService();
        $module_service     = new ModuleService();
        $user_service       = new UserService();
        $controller         = new UsersController($datatables_service, $mail_service, $module_service, $user_service);
        $request            = self::createRequest(RequestMethodInterface::METHOD_POST, ['route' => 'admin-users-cleanup']);
        $response           = $controller->cleanupAction($request);

        $this->assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
