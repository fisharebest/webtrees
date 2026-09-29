<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Services\RegistrationService
 */
class RegistrationServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    private RegistrationService $registration_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registration_service = new RegistrationService(new EmailService(), new UserService());

        Site::setPreference('SMTP_ACTIVE', 'internal');
    }

    public function testRegistrationAllowed(): void
    {
        Site::setPreference('USE_REGISTRATION_MODULE', '0');
        self::assertFalse($this->registration_service->registrationAllowed());

        Site::setPreference('USE_REGISTRATION_MODULE', '1');
        self::assertTrue($this->registration_service->registrationAllowed());
    }

    public function testCheckRegistrationDetailsRejectsMissingFields(): void
    {
        $error = $this->registration_service->checkRegistrationDetails('', 'user@example.com', 'User', 'Why', 'secret', 'https://example.com');

        self::assertNotNull($error);
    }

    public function testCheckRegistrationDetailsRejectsDuplicateUsername(): void
    {
        (new UserService())->create('user', 'User', 'user@example.com', 'secret');

        $error = $this->registration_service->checkRegistrationDetails('user', 'other@example.com', 'Other', 'Why', 'secret', 'https://example.com');

        self::assertNotNull($error);
    }

    public function testCheckRegistrationDetailsRejectsDuplicateEmail(): void
    {
        (new UserService())->create('user', 'User', 'user@example.com', 'secret');

        $error = $this->registration_service->checkRegistrationDetails('other', 'user@example.com', 'Other', 'Why', 'secret', 'https://example.com');

        self::assertNotNull($error);
    }

    public function testCheckRegistrationDetailsRejectsExternalLinksInComments(): void
    {
        $error = $this->registration_service->checkRegistrationDetails(
            'user',
            'user@example.com',
            'User',
            'Visit https://spam.example.com for more',
            'secret',
            'https://example.com'
        );

        self::assertNotNull($error);
    }

    public function testCheckRegistrationDetailsAcceptsValidInput(): void
    {
        $error = $this->registration_service->checkRegistrationDetails(
            'user',
            'user@example.com',
            'User',
            'I am related to the family',
            'secret',
            'https://example.com'
        );

        self::assertNull($error);
    }

    public function testRegisterCreatesAPendingUnverifiedUnapprovedAccount(): void
    {
        $user = $this->registration_service->register(
            'user',
            'user@example.com',
            'User',
            'secret',
            'I am related to the family',
            'https://example.com'
        );

        self::assertSame('user', $user->userName());
        self::assertSame('user@example.com', $user->email());
        self::assertSame('', $user->getPreference(UserInterface::PREF_IS_EMAIL_VERIFIED));
        self::assertSame('', $user->getPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED));
        self::assertSame('', $user->getPreference(UserInterface::PREF_IS_ADMINISTRATOR));
        self::assertNotSame('', $user->getPreference(UserInterface::PREF_VERIFICATION_TOKEN));
        self::assertSame('I am related to the family', $user->getPreference(UserInterface::PREF_NEW_ACCOUNT_COMMENT));
    }

    public function testRegisterNotifiesEveryAdministratorByInternalMessage(): void
    {
        $admin = (new UserService())->create('admin', 'Admin', 'admin@example.com', 'secret');
        $admin->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        $admin->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_INTERNAL_AND_EMAIL);

        $this->registration_service->register(
            'user',
            'user@example.com',
            'User',
            'secret',
            'I am related to the family',
            'https://example.com'
        );

        $message = DB::table('message')
            ->where('user_id', '=', $admin->id())
            ->first();

        self::assertNotNull($message, 'the administrator must get an internal message about the new registration');
    }
}
