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
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RegisterAction::class)]
class RegisterActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(RegisterAction::class));
    }

    public function testHandleThrowsNotFoundWhenRegistrationDisabled(): void
    {
        Site::setPreference('USE_REGISTRATION_MODULE', '0');

        $captcha_service    = self::createStub(CaptchaService::class);
        $email_service      = self::createStub(EmailService::class);
        $rate_limit_service = self::createStub(RateLimitService::class);
        $user_service       = self::createStub(UserService::class);

        $handler = new RegisterAction($captcha_service, $email_service, $rate_limit_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'username' => 'test',
            'realname' => 'Test',
            'email'    => 'test@example.com',
            'password' => 'secret',
            'comments' => 'Hello',
        ]);

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}
