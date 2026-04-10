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
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EmptyClipboard::class)]
class EmptyClipboardTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(EmptyClipboard::class));
    }

    public function testHandleEmptiesClipboardAndRedirects(): void
    {
        $clipboard_service = $this->createMock(ClipboardService::class);
        $clipboard_service->expects(self::once())
            ->method('emptyClipboard');

        $handler  = new EmptyClipboard($clipboard_service);
        $request  = self::createRequest('POST', [], [
            'url' => 'https://webtrees.test/index.php',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
