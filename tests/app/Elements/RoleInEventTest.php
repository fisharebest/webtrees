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

namespace Fisharebest\Webtrees\Elements;

/**
 * @covers \Fisharebest\Webtrees\Elements\AbstractElement
 * @covers \Fisharebest\Webtrees\Elements\RoleInEvent
 */
class RoleInEventTest extends AbstractElementTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$element = new RoleInEvent('label');
    }

    public function testCanonical(): void
    {
        self::assertSame('CHIL', self::$element->canonical('cHiL'));
        self::assertSame('(Foo bAr baZ)', self::$element->canonical('Foo  bAr  baZ'));
        self::assertSame('(Foo bAr baZ)', self::$element->canonical("\t Foo\t bAr \tbaZ\t "));
        self::assertSame('(Foo bAr baZ)', self::$element->canonical("\nFoo \n\r bAr \r\n baZ\r"));
    }
}
