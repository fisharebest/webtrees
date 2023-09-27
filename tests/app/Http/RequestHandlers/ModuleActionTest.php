<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Psr\Http\Message\ResponseInterface;

use function response;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\ModuleAction
 */
class ModuleActionTest extends TestCase
{
    /**
     * @return void
     */
    public function testModuleAction(): void
    {
        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByName')
            ->with('test')
            ->willReturn($this->fooModule());

        $user     = new GuestUser();
        $request  = self::createRequest()
            ->withAttribute('module', 'test')
            ->withAttribute('action', 'Test')
            ->withAttribute('user', $user);
        $handler  = new ModuleAction($module_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('It works!', (string) $response->getBody());
    }

    /**
     * @return void
     */
    public function testNonExistingAction(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('Method getTestingAction() not found in test');

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByName')
            ->with('test')
            ->willReturn($this->fooModule());

        $user    = new GuestUser();
        $request = self::createRequest()
            ->withAttribute('module', 'test')
            ->withAttribute('action', 'Testing')
            ->withAttribute('user', $user);
        $handler = new ModuleAction($module_service);
        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testNonExistingModule(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('Module test does not exist');

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByName')
            ->with('test')
            ->willReturn(null);

        $user    = new GuestUser();
        $request = self::createRequest()
            ->withAttribute('module', 'test')
            ->withAttribute('action', 'Test')
            ->withAttribute('user', $user);
        $handler = new ModuleAction($module_service);
        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testAdminAction(): void
    {
        $this->expectException(HttpAccessDeniedException::class);
        $this->expectExceptionMessage('Admin only action');

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('findByName')
            ->with('test')
            ->willReturn($this->fooModule());

        $user    = new GuestUser();
        $request = self::createRequest()
            ->withAttribute('module', 'test')
            ->withAttribute('action', 'Admin')
            ->withAttribute('user', $user);
        $handler = new ModuleAction($module_service);
        $handler->handle($request);
    }

    /**
     * @return ModuleInterface
     */
    private function fooModule(): ModuleInterface
    {
        return new class () extends AbstractModule {
            /**
             * @return ResponseInterface
             */
            public function getTestAction(): ResponseInterface
            {
                return response('It works!');
            }
        };
    }
}
