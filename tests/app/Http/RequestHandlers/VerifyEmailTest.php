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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(VerifyEmail::class)]
class VerifyEmailTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(VerifyEmail::class));
    }

    public function testHandleWithUnknownUser(): void
    {
        $email_service = self::createStub(EmailService::class);
        $user_service  = $this->createMock(UserService::class);
        $user_service->expects(self::once())
            ->method('findByUserName')
            ->with('unknown')
            ->willReturn(null);

        $handler  = new VerifyEmail($email_service, $user_service);
        $request  = self::createRequest()
            ->withAttribute('username', 'unknown')
            ->withAttribute('token', 'some-token');
        $response = $handler->handle($request);

        // Unknown user renders the failure page
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithInvalidToken(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('verifyuser', 'Verify User', 'verify@example.com', 'secret');
        $user->setPreference('verification_token', 'correct-token');

        $email_service = self::createStub(EmailService::class);
        $mock_user_service = $this->createMock(UserService::class);
        $mock_user_service->expects(self::once())
            ->method('findByUserName')
            ->with('verifyuser')
            ->willReturn($user);

        $handler  = new VerifyEmail($email_service, $mock_user_service);
        $request  = self::createRequest()
            ->withAttribute('username', 'verifyuser')
            ->withAttribute('token', 'wrong-token');
        $response = $handler->handle($request);

        // Wrong token renders the failure page
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
