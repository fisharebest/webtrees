<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\BroadcastPage
 */
class BroadcastPageTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return void
     */
    public function testMissingParameterTo(): void
    {
        $message_service = $this->createStub(MessageService::class);
        $message_service->method('recipientTypes')->willReturn(['foo' => 'FOO']);

        $request = self::createRequest()
            ->withAttribute('to', 'bar');

        $handler = new BroadcastPage($message_service);

        $this->expectException(HttpBadRequestException::class);

        $handler->handle($request);
    }
    /**
     * @return void
     */
    public function testHandler(): void
    {
        $message_service = $this->createStub(MessageService::class);
        $message_service->method('recipientTypes')->willReturn(['foo' => 'FOO', 'bar' => 'BAR']);
        $message_service->method('recipientUsers')->willReturn(new Collection());

        $request = self::createRequest()
            ->withAttribute('to', 'foo');

        $handler  = new BroadcastPage($message_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
