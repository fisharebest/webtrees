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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Controllers\Register;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Register::class)]
class RegisterTest extends TestCase
{
    public function testRegistrationEmailUsesSiteUserForReplyToWhenTreeIsSelected(): void
    {
        self::createDatabase();
        Site::setPreference('USE_REGISTRATION_MODULE', '1');

        $clock        = new SystemClock();
        $user_service = new UserService($clock);
        $tree         = (new TreeService(new GedcomImportService()))->create('family', 'Family');
        Registry::container()->set(ModuleThemeInterface::class, self::createStub(ModuleThemeInterface::class));

        $captcha_service = self::createStub(CaptchaService::class);
        $captcha_service->method('isRobot')->willReturn(false);

        $email_service = self::createMock(EmailService::class);
        $email_service->expects(self::once())
            ->method('send')
            ->with(
                self::isInstanceOf(SiteUser::class),
                self::isInstanceOf(UserInterface::class),
                self::isInstanceOf(SiteUser::class),
                self::anything(),
                self::anything(),
                self::anything(),
            )
            ->willReturn(true);

        $controller = new Register(
            $captcha_service,
            $email_service,
            new RateLimitService($clock),
            $user_service,
        );

        $request = self::createRequest(
            'POST',
            [],
            [
                'comments' => 'Please register me',
                'email'    => 'new@example.com',
                'password' => 'secret',
                'realname' => 'New User',
                'username' => 'new-user',
            ],
            [],
            ['tree' => $tree],
        );

        $controller->post($request);
    }
}
