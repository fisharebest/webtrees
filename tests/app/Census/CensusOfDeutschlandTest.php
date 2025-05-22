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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusOfDeutschland::class)]
class CensusOfDeutschlandTest extends TestCase
{
    public function testPlace(): void
    {
        $census = new CensusOfDeutschland();

        self::assertSame('Deutschland', $census->censusPlace());
    }

    public function testLanguage(): void
    {
        $census = new CensusOfDeutschland();

        self::assertSame('de', $census->censusLanguage());
    }

    public function testAllDates(): void
    {
        $census = new CensusOfDeutschland();

        $census_dates = $census->allCensusDates();

        self::assertCount(5, $census_dates);
        self::assertInstanceOf(CensusOfDeutschland1819::class, $census_dates[0]);
        self::assertInstanceOf(CensusOfDeutschland1867::class, $census_dates[1]);
        self::assertInstanceOf(CensusOfDeutschlandNL1867::class, $census_dates[2]);
        self::assertInstanceOf(CensusOfDeutschland1900::class, $census_dates[3]);
        self::assertInstanceOf(CensusOfDeutschland1919::class, $census_dates[4]);
    }
}
