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

/**
 * @covers \Fisharebest\Webtrees\Census\CensusOfSlovakia
 */
class CensusOfSlovakiaTest extends TestCase
{
    public function testPlace(): void
    {
        $census = new CensusOfSlovakia();

        self::assertSame('Slovensko', $census->censusPlace());
    }

    public function testLanguage(): void
    {
        $census = new CensusOfSlovakia();

        self::assertSame('sk', $census->censusLanguage());
    }

    public function testAllDates(): void
    {
        $census = new CensusOfSlovakia();

        $census_dates = $census->allCensusDates();

        self::assertCount(3, $census_dates);
        self::assertInstanceOf(CensusOfSlovakia1869::class, $census_dates[0]);
        self::assertInstanceOf(CensusOfSlovakia1930::class, $census_dates[1]);
        self::assertInstanceOf(CensusOfSlovakia1940::class, $census_dates[2]);
    }
}
