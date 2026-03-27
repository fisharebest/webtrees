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

#[CoversClass(GedcomService::class)]
class GedcomServiceTest extends TestCase
{
    private GedcomService $gedcom_service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gedcom_service = new GedcomService();
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(GedcomService::class));
    }

    public function testCanonicalTagReturnsStandardTags(): void
    {
        self::assertSame('BIRT', $this->gedcom_service->canonicalTag('BIRT'));
        self::assertSame('DEAT', $this->gedcom_service->canonicalTag('DEAT'));
        self::assertSame('NAME', $this->gedcom_service->canonicalTag('NAME'));
    }

    public function testCanonicalTagConvertsLongFormsToAbbreviations(): void
    {
        self::assertSame('BIRT', $this->gedcom_service->canonicalTag('BIRTH'));
        self::assertSame('DEAT', $this->gedcom_service->canonicalTag('DEATH'));
        self::assertSame('MARR', $this->gedcom_service->canonicalTag('MARRIAGE'));
        self::assertSame('BAPM', $this->gedcom_service->canonicalTag('BAPTISM'));
        self::assertSame('BURI', $this->gedcom_service->canonicalTag('BURIAL'));
        self::assertSame('CENS', $this->gedcom_service->canonicalTag('CENSUS'));
    }

    public function testCanonicalTagIsCaseInsensitive(): void
    {
        self::assertSame('BIRT', $this->gedcom_service->canonicalTag('birt'));
        self::assertSame('BIRT', $this->gedcom_service->canonicalTag('Birth'));
        self::assertSame('MARR', $this->gedcom_service->canonicalTag('marriage'));
    }

    public function testCanonicalTagConvertsSynonyms(): void
    {
        self::assertSame('_WT_USER', $this->gedcom_service->canonicalTag('_PGVU'));
    }

    public function testCanonicalTagReturnsUnknownTagsUnchanged(): void
    {
        self::assertSame('_CUSTOM', $this->gedcom_service->canonicalTag('_CUSTOM'));
        self::assertSame('ZZZZZ', $this->gedcom_service->canonicalTag('ZZZZZ'));
    }

    public function testReadLatitudeWithNorthValue(): void
    {
        self::assertSame(51.5, $this->gedcom_service->readLatitude('N51.5'));
    }

    public function testReadLatitudeWithSouthValue(): void
    {
        self::assertSame(-33.86, $this->gedcom_service->readLatitude('S33.86'));
    }

    public function testReadLongitudeWithEastValue(): void
    {
        self::assertSame(0.1276, $this->gedcom_service->readLongitude('E0.1276'));
    }

    public function testReadLongitudeWithWestValue(): void
    {
        self::assertSame(-73.935, $this->gedcom_service->readLongitude('W73.935'));
    }

    public function testReadLatitudeWithPlainNumber(): void
    {
        self::assertSame(51.5, $this->gedcom_service->readLatitude('51.5'));
    }

    public function testReadLatitudeWithInvalidInput(): void
    {
        self::assertNull($this->gedcom_service->readLatitude('invalid'));
        self::assertNull($this->gedcom_service->readLatitude(''));
    }
}
