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
use Fisharebest\Webtrees\Http\Controllers\UserEdit;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserEdit::class)]
class UserEditTest extends TestCase
{
    public function testApprovalEmailDoesNotUseAdministratorForReplyTo(): void
    {
        self::createDatabase();

        $user_service = new UserService(new SystemClock());
        $administrator = $user_service->create('admin', 'Administrator', 'admin@example.com', 'secret');
        $new_user      = $user_service->create('new-user', 'New User', 'new@example.com', 'secret');

        $email_service = self::createMock(EmailService::class);
        $email_service->expects(self::once())
            ->method('send')
            ->with(
                self::isInstanceOf(SiteUser::class),
                self::callback(static fn ($user): bool => $user->id() === $new_user->id()),
                self::isInstanceOf(SiteUser::class),
                self::anything(),
                self::anything(),
                self::anything(),
            )
            ->willReturn(true);

        $controller = new UserEdit(
            $administrator,
            new MessageService($email_service, $user_service),
            self::createStub(ModuleService::class),
            new TreeService(new GedcomImportService()),
            $user_service,
            $email_service,
        );

        $request = self::createRequest('POST', [], [
            'user_id'        => (string) $new_user->id(),
            'username'       => $new_user->userName(),
            'real_name'      => $new_user->realName(),
            'email'          => $new_user->email(),
            'password'       => '',
            'theme'          => '',
            'language'       => 'en-US',
            'timezone'       => 'UTC',
            'contact-method' => '',
            'comment'        => '',
            'approved'       => '1',
        ]);

        $controller->post($request);
    }
}
