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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Routing;

use Fisharebest\Webtrees\Http\Routing\MatchResult;
use Fisharebest\Webtrees\Http\Routing\Route;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MatchResult::class)]
class MatchResultTest extends TestCase
{
    public function testSuccess(): void
    {
        $route  = new Route(url: '/test/{id}', controller: self::class, middleware: []);
        $result = MatchResult::success($route, ['id' => '42']);

        self::assertTrue($result->isSuccess());
        self::assertSame($route, $result->route);
        self::assertSame(['id' => '42'], $result->attributes);
        self::assertNull($result->failureReason);
    }

    public function testNotFound(): void
    {
        $result = MatchResult::notFound();

        self::assertFalse($result->isSuccess());
        self::assertNull($result->route);
        self::assertSame([], $result->attributes);
        self::assertSame('not_found', $result->failureReason);
    }
}
