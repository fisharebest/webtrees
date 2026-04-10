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

    public function testCanonicalTag(): void
    {
        self::assertSame('BIRT', $this->gedcom_service->canonicalTag('BIRTH'));
        self::assertSame('DEAT', $this->gedcom_service->canonicalTag('DEATH'));
        self::assertSame('MARR', $this->gedcom_service->canonicalTag('MARRIAGE'));
        self::assertSame('INDI', $this->gedcom_service->canonicalTag('INDIVIDUAL'));
        self::assertSame('BIRT', $this->gedcom_service->canonicalTag('birth'));
    }

    public function testCanonicalTagSynonyms(): void
    {
        self::assertSame('_WT_USER', $this->gedcom_service->canonicalTag('_PGVU'));
        self::assertSame('_WT_OBJE_SORT', $this->gedcom_service->canonicalTag('_PGV_OBJS'));
    }

    public function testCanonicalTagPassthrough(): void
    {
        self::assertSame('BIRT', $this->gedcom_service->canonicalTag('BIRT'));
        self::assertSame('CUSTOM', $this->gedcom_service->canonicalTag('custom'));
    }

    public function testReadLatitude(): void
    {
        self::assertSame(52.5, $this->gedcom_service->readLatitude('N52.5'));
        self::assertSame(-33.8, $this->gedcom_service->readLatitude('S33.8'));
        self::assertSame(52.5, $this->gedcom_service->readLatitude('52.5'));
        self::assertNull($this->gedcom_service->readLatitude('invalid'));
        self::assertNull($this->gedcom_service->readLatitude(''));
    }

    public function testReadLongitude(): void
    {
        self::assertSame(13.4, $this->gedcom_service->readLongitude('E13.4'));
        self::assertSame(-122.4, $this->gedcom_service->readLongitude('W122.4'));
        self::assertSame(13.4, $this->gedcom_service->readLongitude('13.4'));
        self::assertNull($this->gedcom_service->readLongitude('invalid'));
    }
}
