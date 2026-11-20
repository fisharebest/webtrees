<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Tests\Unit\Http\Middleware;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Middleware\DebugLogger;
use Fisharebest\Webtrees\Tests\TestCase;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Pdo\Sqlite;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DebugLogger::class)]
class DebugLoggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);
    }

    public function testDebugHeadersIncludeDatabaseStatements(): void
    {

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, ['x-rows' => DB::connection()->queryOne('SELECT 1')]);
            }
        };

        $response = (new DebugLogger())->process((new ServerRequest('GET', '/'))->withAttribute('debug', '1'), $handler);

        self::assertStringContainsString('Queries: 1', $response->getHeaderLine('x-debug-sql'));
        self::assertStringContainsString('SELECT 1', $response->getHeaderLine('x-debug-sql'));
        self::assertNull(DB::connection()->statementLogger());
    }

    public function testDebugHeadersHandleNoDatabaseStatements(): void
    {
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };

        $response = (new DebugLogger())->process((new ServerRequest('GET', '/'))->withAttribute('debug', '1'), $handler);

        self::assertStringContainsString('Queries: 0', $response->getHeaderLine('x-debug-sql'));
        self::assertNull(DB::connection()->statementLogger());
    }
}
