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
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$element = new MultimediaFormat('label');
    }

    /**
     * @return void
     */
    public function testCanonical(): void
    {
        self::assertSame('jpg', self::$element->canonical("jpg"));
        self::assertSame('jpg', self::$element->canonical("jpeg"));
        self::assertSame('jpg', self::$element->canonical("JPG"));
        self::assertSame('jpg', self::$element->canonical("JPEG"));
        self::assertSame('tif', self::$element->canonical("tif"));
        self::assertSame('tif', self::$element->canonical("tiff"));
        self::assertSame('tif', self::$element->canonical("TIF"));
        self::assertSame('tif', self::$element->canonical("TIFF"));
        self::assertSame('pdf', self::$element->canonical("pdf"));
    }
}
