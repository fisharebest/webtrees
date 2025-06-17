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

namespace Fisharebest\Webtrees\Tests\Encodings;

use Fisharebest\Webtrees\Encodings\AbstractEncoding;
use Fisharebest\Webtrees\Encodings\CP850;
use Fisharebest\Webtrees\Encodings\UTF8;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function chr;
use function iconv;

#[CoversClass(AbstractEncoding::class)]
#[CoversClass(CP850::class)]
class CP850Test extends TestCase
{
    public function testToUtf8(): void
    {
        $encoding = new CP850();

        foreach (range(0, 255) as $code_point) {
            $character = chr($code_point);
            $actual    = $encoding->toUtf8($character);
            $expected  = iconv(CP850::NAME, UTF8::NAME, $character);

            self::assertSame($expected, $actual, dechex($code_point) . '=>' . $actual . ' ' . $expected);
        }
    }
}
