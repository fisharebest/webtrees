<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserEditPage::class)]
class UserEditPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UserEditPage::class));
    }

    public function testHandleReturnsEditPageForExistingUser(): void
    {
        $user_service    = new UserService();
        $user            = $user_service->create('edituser', 'Edit User', 'edit@example.com', 'password1');

        $module_service  = $this->createMock(ModuleService::class);
        $module_service->expects(self::exactly(2))
            ->method('findByInterface')
            ->willReturn(new Collection([]));
        $module_service->expects(self::once())
            ->method('titleMapper')
            ->willReturn(static fn ($module) => $module->title());

        $mail_service    = new EmailService();
        $message_service = new MessageService($mail_service, $user_service);
        $tree_service    = $this->createMock(TreeService::class);
        $tree_service->expects(self::once())
            ->method('all')
            ->willReturn(new Collection([]));

        $handler  = new UserEditPage($message_service, $module_service, $tree_service, $user_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['user_id' => (string) $user->id()]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleThrowsNotFoundForNonExistingUser(): void
    {
        $this->expectException(HttpNotFoundException::class);

        $user_service    = new UserService();
        $module_service  = self::createStub(ModuleService::class);
        $mail_service    = new EmailService();
        $message_service = new MessageService($mail_service, $user_service);
        $tree_service    = self::createStub(TreeService::class);

        $handler = new UserEditPage($message_service, $module_service, $tree_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['user_id' => '99999']);
        $handler->handle($request);
    }
}
