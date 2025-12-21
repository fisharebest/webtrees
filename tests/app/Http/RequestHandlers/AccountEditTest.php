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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AccountEdit::class)]
class AccountEditTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testHandler(): void
    {
        $user            = $this->createStub(User::class);
        $message_service = $this->createStub(MessageService::class);
        $module_service  = $this->createStub(ModuleService::class);

        $module_service->method('findByInterface')->willReturn(new Collection([]));

        $request = self::createRequest()
            ->withAttribute('user', $user);

        $handler  = new AccountEdit($message_service, $module_service);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
