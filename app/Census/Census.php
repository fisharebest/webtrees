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

namespace Fisharebest\Webtrees\Census;

readonly class Census
{
    /**
     * @return list<CensusPlaceInterface>
     */
    public static function censusPlaces(string $locale): array
    {
        return match ($locale) {
            'cs'    => [
                new CensusOfCzechRepublic(),
                new CensusOfSlovakia(),
                new CensusOfDenmark(),
                new CensusOfDeutschland(),
                new CensusOfEngland(),
                new CensusOfFrance(),
                new CensusOfScotland(),
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfWales(),
                new CensusOfCanada(),
            ],
            'da'    => [
                new CensusOfDenmark(),
                new CensusOfDeutschland(),
                new CensusOfCzechRepublic(),
                new CensusOfEngland(),
                new CensusOfFrance(),
                new CensusOfScotland(),
                new CensusOfSlovakia(),
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfWales(),
                new CensusOfCanada(),
            ],
            'de'    => [
                new CensusOfDeutschland(),
                new CensusOfCzechRepublic(),
                new CensusOfDenmark(),
                new CensusOfEngland(),
                new CensusOfFrance(),
                new CensusOfScotland(),
                new CensusOfSlovakia(),
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfWales(),
                new CensusOfCanada(),
            ],
            'en-AU',
            'en-GB' => [
                new CensusOfEngland(),
                new CensusOfScotland(),
                new CensusOfWales(),
                new CensusOfCanada(),
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfCzechRepublic(),
                new CensusOfDenmark(),
                new CensusOfDeutschland(),
                new CensusOfFrance(),
                new CensusOfSlovakia(),
            ],
            'en-US' => [
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfCanada(),
                new CensusOfCzechRepublic(),
                new CensusOfDenmark(),
                new CensusOfDeutschland(),
                new CensusOfEngland(),
                new CensusOfFrance(),
                new CensusOfScotland(),
                new CensusOfSlovakia(),
                new CensusOfWales(),
            ],
            'fr'    => [
                new CensusOfFrance(),
                new CensusOfCanada(),
                new CensusOfCzechRepublic(),
                new CensusOfDenmark(),
                new CensusOfDeutschland(),
                new CensusOfEngland(),
                new CensusOfScotland(),
                new CensusOfSlovakia(),
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfWales(),
            ],
            'fr-CA' => [
                new CensusOfCanada(),
                new CensusOfFrance(),
                new CensusOfCzechRepublic(),
                new CensusOfDenmark(),
                new CensusOfDeutschland(),
                new CensusOfEngland(),
                new CensusOfScotland(),
                new CensusOfSlovakia(),
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfWales(),
            ],
            'sk'    => [
                new CensusOfSlovakia(),
                new CensusOfCzechRepublic(),
                new CensusOfDenmark(),
                new CensusOfDeutschland(),
                new CensusOfEngland(),
                new CensusOfFrance(),
                new CensusOfScotland(),
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfWales(),
                new CensusOfCanada(),
            ],
            default => [
                new CensusOfUnitedStates(),
                new CensusOfRhodeIsland(),
                new CensusOfEngland(),
                new CensusOfScotland(),
                new CensusOfWales(),
                new CensusOfDeutschland(),
                new CensusOfFrance(),
                new CensusOfCzechRepublic(),
                new CensusOfSlovakia(),
                new CensusOfDenmark(),
                new CensusOfCanada(),
            ],
        };
    }
}
