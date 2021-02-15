<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

/**
 * Test harness for the class AgeAtEvent
 *
 * @covers \Fisharebest\Webtrees\Elements\AbstractElement
 * @covers \Fisharebest\Webtrees\Elements\AgeAtEvent
 */
class AgeAtEventTest extends AbstractElementTest
{
    /**
     * Standard tests for all elements.
     */
    public static function setupBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$element = new AgeAtEvent('label');
    }


    /**
     * @return void
     */
    public function testCanonical(): void
    {
        self::assertSame('CHILD', self::$element->canonical("cHiLd"));
        self::assertSame('INFANT', self::$element->canonical("iNfAnT "));
        self::assertSame('STILLBORN', self::$element->canonical(" sTiLlBoRn"));
        self::assertSame('fish', self::$element->canonical("fIsH"));
        self::assertSame('1y 2m 3d', self::$element->canonical("1Y  2M  3D"));
    }
}
