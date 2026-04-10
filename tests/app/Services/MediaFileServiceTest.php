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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MediaFileService::class)]
class MediaFileServiceTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(MediaFileService::class));
    }

    public function testCreateMediaFileGedcomWithLocalFile(): void
    {
        $service = new MediaFileService(new PhpService());

        $gedcom = $service->createMediaFileGedcom('photo.jpg', 'photo', 'My Photo', '');

        self::assertStringContainsString('1 FILE photo.jpg', $gedcom);
        self::assertStringContainsString('2 FORM JPG', $gedcom);
        self::assertStringContainsString('3 TYPE photo', $gedcom);
        self::assertStringContainsString('2 TITL My Photo', $gedcom);
        self::assertStringNotContainsString('1 NOTE', $gedcom);
    }

    public function testCreateMediaFileGedcomWithUrl(): void
    {
        $service = new MediaFileService(new PhpService());

        $gedcom = $service->createMediaFileGedcom('https://example.com/photo.jpg', '', '', '');

        self::assertStringStartsWith('1 FILE https://example.com/photo.jpg', $gedcom);
        self::assertStringNotContainsString('2 FORM', $gedcom);
        self::assertStringNotContainsString('3 TYPE', $gedcom);
        self::assertStringNotContainsString('2 TITL', $gedcom);
        self::assertStringNotContainsString('1 NOTE', $gedcom);
    }

    public function testCreateMediaFileGedcomWithNote(): void
    {
        $service = new MediaFileService(new PhpService());

        $gedcom = $service->createMediaFileGedcom('doc.pdf', '', '', 'Some note');

        self::assertStringContainsString('1 FILE doc.pdf', $gedcom);
        self::assertStringContainsString('2 FORM PDF', $gedcom);
        self::assertStringNotContainsString('3 TYPE', $gedcom);
        self::assertStringNotContainsString('2 TITL', $gedcom);
        self::assertStringContainsString('1 NOTE Some note', $gedcom);
    }
}
