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

use Fisharebest\Webtrees\MediaCrop;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MediaCrop::class)]
class MediaCropTest extends TestCase
{
    public function testFromGedcom(): void
    {
        $crop = MediaCrop::fromGedcom("1 OBJE @M1@\n2 CROP\n3 TOP 10\n3 LEFT 20\n3 HEIGHT 30\n3 WIDTH 40\n2 _PRIM Y");

        self::assertEquals(new MediaCrop(top: 10, left: 20, height: 30, width: 40), $crop);
    }

    public function testFromGedcomUsesDefaultsForMissingValues(): void
    {
        $crop = MediaCrop::fromGedcom("1 OBJE @M1@\n2 CROP\n3 LEFT 20");

        self::assertEquals(new MediaCrop(top: 0, left: 20, height: null, width: null), $crop);
    }

    public function testFromGedcomWithoutCrop(): void
    {
        self::assertNull(MediaCrop::fromGedcom("1 OBJE @M1@\n2 _PRIM Y"));
    }

    public function testFromGedcomIgnoresValuesOutsideTheCrop(): void
    {
        $crop = MediaCrop::fromGedcom("1 OBJE @M1@\n2 CROP\n3 TOP 10\n2 NOTE x\n3 WIDTH 99");

        self::assertEquals(new MediaCrop(top: 10, left: 0, height: null, width: null), $crop);
    }

    public function testStringRoundTrip(): void
    {
        $crop = new MediaCrop(top: 10, left: 20, height: null, width: 40);

        self::assertSame('10,20,,40', $crop->toString());
        self::assertEquals($crop, MediaCrop::fromString($crop->toString()));
    }

    public function testFromStringRejectsInvalidValues(): void
    {
        self::assertNull(MediaCrop::fromString(''));
        self::assertNull(MediaCrop::fromString('1,2,3'));
        self::assertNull(MediaCrop::fromString('-1,2,3,4'));
    }
}
