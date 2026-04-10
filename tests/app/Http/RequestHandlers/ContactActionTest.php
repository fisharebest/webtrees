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
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\RateLimitService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ContactAction::class)]
class ContactActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ContactAction::class));
    }

    public function testHandleThrowsNotFoundForNonExistentUser(): void
    {
        $this->expectException(HttpNotFoundException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ca-nouser', 'CA No User');
        $user_service = new UserService();

        $captcha_service    = self::createStub(CaptchaService::class);
        $email_service      = self::createStub(EmailService::class);
        $message_service    = self::createStub(MessageService::class);
        $rate_limit_service = self::createStub(RateLimitService::class);

        $handler = new ContactAction(
            $captcha_service,
            $email_service,
            $message_service,
            $rate_limit_service,
            $user_service,
        );
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'       => 'Hello',
            'from_email' => 'guest@example.com',
            'from_name'  => 'Guest',
            'subject'    => 'Test',
            'to'         => 'nonexistent',
            'url'        => 'https://webtrees.test',
        ])->withAttribute('tree', $tree);

        $handler->handle($request);
    }

    public function testHandleThrowsAccessDeniedForInvalidContact(): void
    {
        $this->expectException(HttpAccessDeniedException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ca-invalid', 'CA Invalid');
        $user_service = new UserService();
        $user         = $user_service->create('causer1', 'CA User', 'causer1@example.com', 'password');

        $captcha_service    = self::createStub(CaptchaService::class);
        $email_service      = self::createStub(EmailService::class);
        $rate_limit_service = self::createStub(RateLimitService::class);

        // User exists but is not a valid contact
        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('validContacts')
            ->with($tree)
            ->willReturn([]);

        $handler = new ContactAction(
            $captcha_service,
            $email_service,
            $message_service,
            $rate_limit_service,
            $user_service,
        );
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'       => 'Hello',
            'from_email' => 'guest@example.com',
            'from_name'  => 'Guest',
            'subject'    => 'Test',
            'to'         => 'causer1',
            'url'        => 'https://webtrees.test',
        ])->withAttribute('tree', $tree);

        $handler->handle($request);
    }

    public function testHandleRedirectsOnValidationErrors(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ca-errors', 'CA Errors');
        $user_service = new UserService();
        $user         = $user_service->create('causer2', 'CA User 2', 'causer2@example.com', 'password');
        $tree->setPreference('CONTACT_USER_ID', (string) $user->id());

        $captcha_service = $this->createMock(CaptchaService::class);
        $captcha_service
            ->expects(self::once())
            ->method('isRobot')
            ->willReturn(true);

        $email_service      = self::createStub(EmailService::class);
        $rate_limit_service = self::createStub(RateLimitService::class);

        $message_service = $this->createMock(MessageService::class);
        $message_service->method('validContacts')->willReturn([$user]);

        $handler = new ContactAction(
            $captcha_service,
            $email_service,
            $message_service,
            $rate_limit_service,
            $user_service,
        );
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'       => 'Hello',
            'from_email' => 'guest@example.com',
            'from_name'  => 'Guest',
            'subject'    => 'Test subject',
            'to'         => 'causer2',
            'url'        => 'https://webtrees.test',
        ])->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        // Robot detection causes redirect back to contact page
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleSuccessfulDelivery(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ca-success', 'CA Success');
        $user_service = new UserService();
        $user         = $user_service->create('causer3', 'CA User 3', 'causer3@example.com', 'password');
        $tree->setPreference('CONTACT_USER_ID', (string) $user->id());

        $captcha_service = $this->createMock(CaptchaService::class);
        $captcha_service->method('isRobot')->willReturn(false);

        $email_service = $this->createMock(EmailService::class);
        $email_service->method('isValidEmail')->willReturn(true);

        $rate_limit_service = $this->createMock(RateLimitService::class);
        $rate_limit_service
            ->expects(self::once())
            ->method('limitRateForUser');

        $message_service = $this->createMock(MessageService::class);
        $message_service->method('validContacts')->willReturn([$user]);
        $message_service
            ->expects(self::once())
            ->method('deliverMessage')
            ->willReturn(true);

        $handler = new ContactAction(
            $captcha_service,
            $email_service,
            $message_service,
            $rate_limit_service,
            $user_service,
        );
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'       => 'Hello there',
            'from_email' => 'guest@example.com',
            'from_name'  => 'Guest User',
            'subject'    => 'Enquiry',
            'to'         => 'causer3',
            'url'        => 'https://webtrees.test',
        ])->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleFailedDelivery(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ca-fail', 'CA Fail');
        $user_service = new UserService();
        $user         = $user_service->create('causer4', 'CA User 4', 'causer4@example.com', 'password');
        $tree->setPreference('CONTACT_USER_ID', (string) $user->id());

        $captcha_service = $this->createMock(CaptchaService::class);
        $captcha_service->method('isRobot')->willReturn(false);

        $email_service = $this->createMock(EmailService::class);
        $email_service->method('isValidEmail')->willReturn(true);

        $rate_limit_service = self::createStub(RateLimitService::class);

        $message_service = $this->createMock(MessageService::class);
        $message_service->method('validContacts')->willReturn([$user]);
        $message_service
            ->expects(self::once())
            ->method('deliverMessage')
            ->willReturn(false);

        $handler = new ContactAction(
            $captcha_service,
            $email_service,
            $message_service,
            $rate_limit_service,
            $user_service,
        );
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'       => 'Hello there',
            'from_email' => 'guest@example.com',
            'from_name'  => 'Guest User',
            'subject'    => 'Enquiry',
            'to'         => 'causer4',
            'url'        => 'https://webtrees.test',
        ])->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        // Failed delivery still redirects
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRedirectsOnEmptyFields(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ca-empty', 'CA Empty');
        $user_service = new UserService();
        $user         = $user_service->create('causer5', 'CA User 5', 'causer5@example.com', 'password');
        $tree->setPreference('CONTACT_USER_ID', (string) $user->id());

        $captcha_service = $this->createMock(CaptchaService::class);
        $captcha_service->method('isRobot')->willReturn(false);

        $email_service      = self::createStub(EmailService::class);
        $rate_limit_service = self::createStub(RateLimitService::class);

        $message_service = $this->createMock(MessageService::class);
        $message_service->method('validContacts')->willReturn([$user]);
        // deliverMessage should NOT be called when validation fails
        $message_service->expects(self::never())->method('deliverMessage');

        $handler = new ContactAction(
            $captcha_service,
            $email_service,
            $message_service,
            $rate_limit_service,
            $user_service,
        );
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'       => '',
            'from_email' => '',
            'from_name'  => '',
            'subject'    => '',
            'to'         => 'causer5',
            'url'        => 'https://webtrees.test',
        ])->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
