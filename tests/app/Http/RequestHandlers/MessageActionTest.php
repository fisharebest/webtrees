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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MessageAction::class)]
class MessageActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MessageAction::class));
    }

    public function testHandleThrowsExceptionForNonContactableUser(): void
    {
        $this->expectException(HttpAccessDeniedException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ma-nocontact', 'MA No Contact');
        $user_service = new UserService();
        $sender       = $user_service->create('masender1', 'MA Sender 1', 'masender1@example.com', 'password');
        $recipient    = $user_service->create('marecip1', 'MA Recip 1', 'marecip1@example.com', 'password');

        $recipient->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_NONE);

        $message_service = self::createStub(MessageService::class);

        $handler = new MessageAction($message_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Hello',
            'subject' => 'Test',
            'to'      => 'marecip1',
            'url'     => 'https://webtrees.test',
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);

        $handler->handle($request);
    }

    public function testHandleThrowsExceptionForNonExistentUser(): void
    {
        $this->expectException(HttpAccessDeniedException::class);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ma-nouser', 'MA No User');
        $user_service = new UserService();
        $sender       = $user_service->create('masender2', 'MA Sender 2', 'masender2@example.com', 'password');

        $message_service = self::createStub(MessageService::class);

        $handler = new MessageAction($message_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Hello',
            'subject' => 'Test',
            'to'      => 'nonexistent',
            'url'     => 'https://webtrees.test',
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);

        $handler->handle($request);
    }

    public function testHandleRedirectsOnEmptyBody(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ma-empty', 'MA Empty');
        $user_service = new UserService();
        $sender       = $user_service->create('masender3', 'MA Sender 3', 'masender3@example.com', 'password');
        $recipient    = $user_service->create('marecip3', 'MA Recip 3', 'marecip3@example.com', 'password');

        $recipient->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_INTERNAL);

        $message_service = $this->createMock(MessageService::class);
        // deliverMessage should NOT be called when body is empty
        $message_service->expects(self::never())->method('deliverMessage');

        $handler = new MessageAction($message_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => '',
            'subject' => 'Test',
            'to'      => 'marecip3',
            'url'     => 'https://webtrees.test',
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);

        $response = $handler->handle($request);

        // Redirects back to message page
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleSuccessfulDelivery(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ma-success', 'MA Success');
        $user_service = new UserService();
        $sender       = $user_service->create('masender4', 'MA Sender 4', 'masender4@example.com', 'password');
        $recipient    = $user_service->create('marecip4', 'MA Recip 4', 'marecip4@example.com', 'password');

        $recipient->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_INTERNAL);

        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('deliverMessage')
            ->willReturn(true);

        $handler = new MessageAction($message_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Hello there',
            'subject' => 'Greetings',
            'to'      => 'marecip4',
            'url'     => 'https://webtrees.test',
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleFailedDelivery(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('ma-fail', 'MA Fail');
        $user_service = new UserService();
        $sender       = $user_service->create('masender5', 'MA Sender 5', 'masender5@example.com', 'password');
        $recipient    = $user_service->create('marecip5', 'MA Recip 5', 'marecip5@example.com', 'password');

        $recipient->setPreference(UserInterface::PREF_CONTACT_METHOD, MessageService::CONTACT_METHOD_INTERNAL);

        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('deliverMessage')
            ->willReturn(false);

        $handler = new MessageAction($message_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Hello there',
            'subject' => 'Greetings',
            'to'      => 'marecip5',
            'url'     => 'https://webtrees.test',
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $sender);

        $response = $handler->handle($request);

        // Failed delivery still redirects back
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
