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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MessagePage::class)]
class MessagePageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MessagePage::class));
    }

    public function testHandleReturnsPageForContactableUser(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('msg-page', 'Message Page');
        $user_service = new UserService();
        $sender       = $user_service->create('msgsender', 'Msg Sender', 'sender@example.com', 'password');
        $recipient    = $user_service->create('msgrecip', 'Msg Recipient', 'recip@example.com', 'password');

        // Set the recipient's contact method to allow messaging
        $recipient->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_INTERNAL);

        $handler  = new MessagePage($user_service);
        $request  = self::createRequest('GET', ['to' => 'msgrecip'])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleThrowsExceptionForNonContactableUser(): void
    {
        $this->expectException(HttpAccessDeniedException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('msg-nocontact', 'Message No Contact');
        $user_service = new UserService();
        $sender       = $user_service->create('msgsender2', 'Msg Sender 2', 'sender2@example.com', 'password');
        $recipient    = $user_service->create('msgrecip2', 'Msg Recipient 2', 'recip2@example.com', 'password');

        // Set contact method to none
        $recipient->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_NONE);

        $handler = new MessagePage($user_service);
        $request = self::createRequest('GET', ['to' => 'msgrecip2'])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);
        $handler->handle($request);
    }

    public function testHandleThrowsExceptionForNonExistentUser(): void
    {
        $this->expectException(HttpAccessDeniedException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('msg-nouser', 'Message No User');
        $user_service = new UserService();
        $sender       = $user_service->create('msgsender3', 'Msg Sender 3', 'sender3@example.com', 'password');

        $handler = new MessagePage($user_service);
        $request = self::createRequest('GET', ['to' => 'nonexistent'])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);
        $handler->handle($request);
    }
}
