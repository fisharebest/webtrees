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

namespace Fisharebest\Webtrees\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

use function random_string;
use function strlen;

#[CoversFunction('random_string')]
class RandomStringTest extends TestCase
{
    public function testGeneratesAlphanumericTokensOfRequestedLength(): void
    {
        foreach ([1, 32, 40] as $length) {
            $token = random_string($length);
            self::assertSame($length, strlen($token));
            self::assertMatchesRegularExpression('/\A[A-Za-z0-9]+\z/D', $token);
        }

        self::assertSame('', random_string(0));
    }

    public function testRejectsNegativeLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        random_string(-1);
    }
}
