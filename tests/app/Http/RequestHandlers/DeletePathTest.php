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
use Fisharebest\Webtrees\TestCase;
use League\Flysystem\WhitespacePathNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DeletePath::class)]
class DeletePathTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(DeletePath::class));
    }

    public function testHandleRefusesToDeleteProtectedFile(): void
    {
        $handler = new DeletePath(new WhitespacePathNormalizer());
        $request = self::createRequest(
            query: ['path' => 'config.ini.php'],
        );
        $response = $handler->handle($request);

        // Protected files cannot be deleted; handler returns empty response
        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    public function testHandleRefusesToDeleteHtaccess(): void
    {
        $handler = new DeletePath(new WhitespacePathNormalizer());
        $request = self::createRequest(
            query: ['path' => '.htaccess'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    public function testHandleRefusesToDeleteIndexPhp(): void
    {
        $handler = new DeletePath(new WhitespacePathNormalizer());
        $request = self::createRequest(
            query: ['path' => 'index.php'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }
}
