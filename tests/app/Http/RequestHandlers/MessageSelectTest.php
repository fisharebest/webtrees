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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MessageSelect::class)]
class MessageSelectTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MessageSelect::class));
    }

    public function testHandleRedirectsToMessagePage(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('msg-select', 'Message Select');

        $handler  = new MessageSelect();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'body'    => 'Test body',
            'subject' => 'Test subject',
            'to'      => 'someuser',
            'url'     => 'https://webtrees.test/tree/test',
        ])->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleRedirectsWithDefaultValues(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('msg-select2', 'Message Select 2');

        $handler  = new MessageSelect();
        // All fields use defaults when not provided
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST)
            ->withAttribute('tree', $tree);

        $response = $handler->handle($request);

        // Should still redirect even with default/empty fields
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
