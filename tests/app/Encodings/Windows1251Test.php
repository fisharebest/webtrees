<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Encodings\Windows1251;
use PHPUnit\Framework\TestCase;

use function chr;
use function dechex;
use function iconv;
use function range;

/**
 * Tests for class Windows1251.
 */
class Windows1251Test extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Encodings\AbstractEncoding
     * @covers \Fisharebest\Webtrees\Encodings\Windows1251
     *
     * @return void
     */
    public function testToUtf8(): void
    {
        $encoding = new Windows1251();

        foreach (range(0, 255) as $code_point) {
            $character = chr($code_point);
            $actual    = $encoding->toUtf8($character);
            $expected  = iconv(Windows1251::NAME, 'UTF-8//IGNORE', $character);
            $expected  = $expected === '' ? UTF8::REPLACEMENT_CHARACTER : $expected;

            static::assertSame($expected, $actual, dechex($code_point) . '=>' . $actual . ' ' . $expected);
        }
    }
}
