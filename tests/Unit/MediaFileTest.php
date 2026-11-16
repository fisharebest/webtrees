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

use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MediaFile::class)]
class MediaFileTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(MediaFile::class));
    }

    public function testDisplayImageDisablesNativeDragging(): void
    {
        $media = self::createMock(Media::class);
        $media->expects(self::exactly(2))->method('fullName')->willReturn('Portrait');
        $media->expects(self::once())->method('url')->willReturn('/media');

        $media_file = $this->getMockBuilder(MediaFile::class)
            ->setConstructorArgs(["1 FILE portrait.jpg", $media])
            ->onlyMethods(['imageUrl', 'downloadUrl'])
            ->getMock();
        $media_file->expects(self::exactly(4))->method('imageUrl')->willReturn('/thumbnail');
        $media_file->expects(self::once())->method('downloadUrl')->willReturn('/image');

        $html = $media_file->displayImage(100, 100, 'contain');

        self::assertStringContainsString('<a href="/image" draggable="false"', $html);
        self::assertStringContainsString('<img draggable="false"', $html);
    }
}
