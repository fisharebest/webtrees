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
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ContactPage::class)]
class ContactPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ContactPage::class));
    }

    public function testHandleReturnsPageForValidContact(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('contact-test', 'Contact Test');
        $user_service = new UserService();
        $user         = $user_service->create('contactuser', 'Contact User', 'contact@example.com', 'password');

        // Set user as a valid contact for the tree
        $tree->setPreference('CONTACT_USER_ID', (string) $user->id());

        $captcha_service = $this->createMock(CaptchaService::class);
        $captcha_service
            ->expects(self::once())
            ->method('createCaptcha')
            ->willReturn('<div>captcha</div>');

        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('validContacts')
            ->with($tree)
            ->willReturn([$user]);

        $handler  = new ContactPage($captcha_service, $message_service, $user_service);
        $request  = self::createRequest('GET', ['to' => 'contactuser'])
            ->withAttribute('tree', $tree);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleThrowsExceptionForInvalidContact(): void
    {
        $this->expectException(HttpAccessDeniedException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('contact-invalid', 'Contact Invalid');
        $user_service = new UserService();
        $user         = $user_service->create('notcontact', 'Not Contact', 'notcontact@example.com', 'password');

        $captcha_service = self::createStub(CaptchaService::class);

        // The user exists but is NOT a valid contact
        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('validContacts')
            ->with($tree)
            ->willReturn([]);

        $handler = new ContactPage($captcha_service, $message_service, $user_service);
        $request = self::createRequest('GET', ['to' => 'notcontact'])
            ->withAttribute('tree', $tree);
        $handler->handle($request);
    }

    public function testHandleThrowsExceptionForNonExistentUser(): void
    {
        $this->expectException(HttpAccessDeniedException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('contact-nouser', 'Contact No User');
        $user_service = new UserService();

        $captcha_service = self::createStub(CaptchaService::class);
        $message_service = self::createStub(MessageService::class);

        $handler = new ContactPage($captcha_service, $message_service, $user_service);
        $request = self::createRequest('GET', ['to' => 'nonexistent'])
            ->withAttribute('tree', $tree);
        $handler->handle($request);
    }
}
