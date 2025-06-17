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

#[CoversClass(Census::class)]
class CensusTest extends TestCase
{
    public function testCensusPlacesCzech(): void
    {
        $censuses = Census::censusPlaces('cs');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[0]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[1]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[2]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[3]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[4]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[5]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[6]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[7]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[8]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[9]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[10]);
    }

    public function testCensusPlacesDanish(): void
    {
        $censuses = Census::censusPlaces('da');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[0]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[1]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[2]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[3]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[4]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[6]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[7]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[8]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[9]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[10]);
    }

    public function testCensusPlacesGerman(): void
    {
        $censuses = Census::censusPlaces('de');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[0]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[1]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[2]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[3]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[4]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[6]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[7]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[8]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[9]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[10]);
    }

    public function testCensusPlacesAustralianEnglish(): void
    {
        $censuses = Census::censusPlaces('en-AU');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[0]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[1]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[2]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[3]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[4]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[6]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[7]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[8]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[9]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[10]);
    }

    public function testCensusPlacesBritishEnglish(): void
    {
        $censuses = Census::censusPlaces('en-GB');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[0]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[1]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[2]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[3]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[4]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[6]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[7]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[8]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[9]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[10]);
    }

    public function testCensusPlacesUSEnglish(): void
    {
        $censuses = Census::censusPlaces('en-US');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[0]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[1]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[2]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[3]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[4]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[6]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[7]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[8]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[9]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[10]);
    }

    public function testCensusPlacesFrench(): void
    {
        $censuses = Census::censusPlaces('fr');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[0]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[1]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[2]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[3]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[4]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[6]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[7]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[8]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[9]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[10]);
    }

    public function testCensusPlacesCanadianFrench(): void
    {
        $censuses = Census::censusPlaces('fr-CA');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[0]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[1]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[2]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[3]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[4]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[6]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[7]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[8]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[9]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[10]);
    }

    public function testCensusPlacesSlovak(): void
    {
        $censuses = Census::censusPlaces('sk');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[0]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[1]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[2]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[3]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[4]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[5]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[6]);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[7]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[8]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[9]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[10]);
    }

    public function testCensusPlacesDefault(): void
    {
        $censuses = Census::censusPlaces('XX');

        self::assertCount(11, $censuses);
        self::assertInstanceOf(CensusOfUnitedStates::class, $censuses[0]);
        self::assertInstanceOf(CensusOfRhodeIsland::class, $censuses[1]);
        self::assertInstanceOf(CensusOfEngland::class, $censuses[2]);
        self::assertInstanceOf(CensusOfScotland::class, $censuses[3]);
        self::assertInstanceOf(CensusOfWales::class, $censuses[4]);
        self::assertInstanceOf(CensusOfDeutschland::class, $censuses[5]);
        self::assertInstanceOf(CensusOfFrance::class, $censuses[6]);
        self::assertInstanceOf(CensusOfCzechRepublic::class, $censuses[7]);
        self::assertInstanceOf(CensusOfSlovakia::class, $censuses[8]);
        self::assertInstanceOf(CensusOfDenmark::class, $censuses[9]);
        self::assertInstanceOf(CensusOfCanada::class, $censuses[10]);
    }
}
