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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BroadcastAction::class)]
class BroadcastActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(BroadcastAction::class));
    }

    public function testHandleSuccessfulBroadcastToAll(): void
    {
        $user_service = new UserService();
        $sender       = $user_service->create('bcsender1', 'BC Sender', 'bcsender1@example.com', 'password');
        $recipient    = $user_service->create('bcrecip1', 'BC Recip', 'bcrecip1@example.com', 'password');

        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('recipientTypes')
            ->willReturn(['all' => I18N::translate('Send a message to all users')]);
        $message_service
            ->expects(self::once())
            ->method('recipientUsers')
            ->with('all')
            ->willReturn(new Collection([$recipient]));
        $message_service
            ->expects(self::once())
            ->method('deliverMessage')
            ->willReturn(true);

        $handler  = new BroadcastAction($message_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Broadcast message',
            'subject' => 'Important notice',
        ])
            ->withAttribute('user', $sender)
            ->withAttribute('to', 'all')
            ->withAttribute('client-ip', '127.0.0.1');

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleFailedBroadcastDelivery(): void
    {
        $user_service = new UserService();
        $sender       = $user_service->create('bcsender2', 'BC Sender 2', 'bcsender2@example.com', 'password');
        $recipient    = $user_service->create('bcrecip2', 'BC Recip 2', 'bcrecip2@example.com', 'password');

        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('recipientTypes')
            ->willReturn(['all' => 'all']);
        $message_service
            ->expects(self::once())
            ->method('recipientUsers')
            ->with('all')
            ->willReturn(new Collection([$recipient]));
        $message_service
            ->expects(self::once())
            ->method('deliverMessage')
            ->willReturn(false);

        $handler  = new BroadcastAction($message_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Broadcast message',
            'subject' => 'Important notice',
        ])
            ->withAttribute('user', $sender)
            ->withAttribute('to', 'all')
            ->withAttribute('client-ip', '127.0.0.1');

        $response = $handler->handle($request);

        // Redirects to control panel even on failure
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleBroadcastToMultipleRecipients(): void
    {
        $user_service = new UserService();
        $sender       = $user_service->create('bcsender3', 'BC Sender 3', 'bcsender3@example.com', 'password');
        $recipient1   = $user_service->create('bcrecip3a', 'BC Recip 3a', 'bcrecip3a@example.com', 'password');
        $recipient2   = $user_service->create('bcrecip3b', 'BC Recip 3b', 'bcrecip3b@example.com', 'password');

        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('recipientTypes')
            ->willReturn(['all' => 'all']);
        $message_service
            ->expects(self::once())
            ->method('recipientUsers')
            ->with('all')
            ->willReturn(new Collection([$recipient1, $recipient2]));
        // deliverMessage called once per recipient
        $message_service
            ->expects(self::exactly(2))
            ->method('deliverMessage')
            ->willReturn(true);

        $handler  = new BroadcastAction($message_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Broadcast to all',
            'subject' => 'Notice',
        ])
            ->withAttribute('user', $sender)
            ->withAttribute('to', 'all')
            ->withAttribute('client-ip', '127.0.0.1');

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleBroadcastWithNoRecipients(): void
    {
        $user_service = new UserService();
        $sender       = $user_service->create('bcsender4', 'BC Sender 4', 'bcsender4@example.com', 'password');

        $message_service = $this->createMock(MessageService::class);
        $message_service
            ->expects(self::once())
            ->method('recipientTypes')
            ->willReturn(['all' => 'all']);
        $message_service
            ->expects(self::once())
            ->method('recipientUsers')
            ->with('all')
            ->willReturn(new Collection([]));
        // No recipients means deliverMessage is never called
        $message_service
            ->expects(self::never())
            ->method('deliverMessage');

        $handler  = new BroadcastAction($message_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Broadcast to none',
            'subject' => 'Notice',
        ])
            ->withAttribute('user', $sender)
            ->withAttribute('to', 'all')
            ->withAttribute('client-ip', '127.0.0.1');

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
