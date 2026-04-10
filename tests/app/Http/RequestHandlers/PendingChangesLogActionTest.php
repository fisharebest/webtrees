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
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PendingChangesLogAction::class)]
class PendingChangesLogActionTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(PendingChangesLogAction::class));
    }

    public function testHandleRedirectsToLogPage(): void
    {
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'tree'     => 'test-tree',
            'from'     => '2026-01-01',
            'to'       => '2026-12-31',
            'type'     => 'pending',
            'oldged'   => '',
            'newged'   => '',
            'xref'     => 'I1',
            'username' => 'admin',
        ]);

        $handler  = new PendingChangesLogAction();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('test-tree', $response->getHeaderLine('location'));
        self::assertStringContainsString('from=2026-01-01', $response->getHeaderLine('location'));
    }
}
