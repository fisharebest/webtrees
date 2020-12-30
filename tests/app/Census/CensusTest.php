<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusColumnAgeFemale5Years
 */
class CensusTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\Census
     *
     * @return void
     */
    public function testCensusPlaces(): void
    {
        $censuses = Census::censusPlaces('XX');

        self::assertCount(9, $censuses);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[0]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[1]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[2]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[3]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[4]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[5]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[6]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[7]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[8]);
    }
}
