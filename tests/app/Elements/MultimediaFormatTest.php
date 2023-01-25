<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
 * Test harness for the class MultimediaFormat
 *
 * @covers \Fisharebest\Webtrees\Elements\AbstractElement
 * @covers \Fisharebest\Webtrees\Elements\MultimediaFormat
 */
class MultimediaFormatTest extends AbstractElementTest
{
    /**
     * Standard tests for all elements.
     */
    public static function setupBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$element = new MultimediaFormat('label');
    }

    /**
     * @return void
     */
    public function testCanonical(): void
    {
        self::assertSame('JPG', self::$element->canonical('jpg'));
        self::assertSame('JPG', self::$element->canonical('jpeg'));
        self::assertSame('JPG', self::$element->canonical('JPG'));
        self::assertSame('JPG', self::$element->canonical('JPEG'));
        self::assertSame('TIF', self::$element->canonical('tif'));
        self::assertSame('TIF', self::$element->canonical('tiff'));
        self::assertSame('TIF', self::$element->canonical('TIF'));
        self::assertSame('TIF', self::$element->canonical('TIFF'));
        self::assertSame('PDF', self::$element->canonical('pdf'));
    }
}
