<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

/**
 * Definitions for a census
 */
class Census
{
    /**
     * @param string $locale
     *
     * @return array<CensusPlaceInterface>
     */
    public static function censusPlaces(string $locale): array
    {
        switch ($locale) {
            case 'cs':
                return [
                    new CensusOfCzechRepublic(),
                    new CensusOfSlovakia(),
                    new CensusOfDenmark(),
                    new CensusOfDeutschland(),
                    new CensusOfEngland(),
                    new CensusOfFrance(),
                    new CensusOfScotland(),
                    new CensusOfUnitedStates(),
                    new CensusOfWales(),
                    new CensusOfCanada(),
                ];

            case 'da':
                return [
                    new CensusOfDenmark(),
                    new CensusOfDeutschland(),
                    new CensusOfCzechRepublic(),
                    new CensusOfEngland(),
                    new CensusOfFrance(),
                    new CensusOfScotland(),
                    new CensusOfSlovakia(),
                    new CensusOfUnitedStates(),
                    new CensusOfWales(),
                    new CensusOfCanada(),
                ];

            case 'de':
                return [
                    new CensusOfDeutschland(),
                    new CensusOfCzechRepublic(),
                    new CensusOfDenmark(),
                    new CensusOfEngland(),
                    new CensusOfFrance(),
                    new CensusOfScotland(),
                    new CensusOfSlovakia(),
                    new CensusOfUnitedStates(),
                    new CensusOfWales(),
                    new CensusOfCanada(),
                ];

            case 'en-AU':
            case 'en-GB':
                return [
                    new CensusOfEngland(),
                    new CensusOfScotland(),
                    new CensusOfWales(),
                    new CensusOfCanada(),
                    new CensusOfUnitedStates(),
                    new CensusOfCzechRepublic(),
                    new CensusOfDenmark(),
                    new CensusOfDeutschland(),
                    new CensusOfFrance(),
                    new CensusOfSlovakia(),
                ];

            case 'en-US':
                return [
                    new CensusOfUnitedStates(),
                    new CensusOfCanada(),
                    new CensusOfCzechRepublic(),
                    new CensusOfDenmark(),
                    new CensusOfDeutschland(),
                    new CensusOfEngland(),
                    new CensusOfFrance(),
                    new CensusOfScotland(),
                    new CensusOfSlovakia(),
                    new CensusOfWales(),
                ];

            case 'fr':
                return [
                    new CensusOfFrance(),
                    new CensusOfCanada(),
                    new CensusOfCzechRepublic(),
                    new CensusOfDenmark(),
                    new CensusOfDeutschland(),
                    new CensusOfEngland(),
                    new CensusOfScotland(),
                    new CensusOfSlovakia(),
                    new CensusOfUnitedStates(),
                    new CensusOfWales(),
                ];

            case 'fr-CA':
                return [
                    new CensusOfCanada(),
                    new CensusOfFrance(),
                    new CensusOfCzechRepublic(),
                    new CensusOfDenmark(),
                    new CensusOfDeutschland(),
                    new CensusOfEngland(),
                    new CensusOfScotland(),
                    new CensusOfSlovakia(),
                    new CensusOfUnitedStates(),
                    new CensusOfWales(),
                ];

            case 'sk':
                return [
                    new CensusOfSlovakia(),
                    new CensusOfCzechRepublic(),
                    new CensusOfDenmark(),
                    new CensusOfDeutschland(),
                    new CensusOfEngland(),
                    new CensusOfFrance(),
                    new CensusOfScotland(),
                    new CensusOfUnitedStates(),
                    new CensusOfWales(),
                    new CensusOfCanada(),
                ];

            default:
                return [
                    new CensusOfUnitedStates(),
                    new CensusOfEngland(),
                    new CensusOfScotland(),
                    new CensusOfWales(),
                    new CensusOfDeutschland(),
                    new CensusOfFrance(),
                    new CensusOfCzechRepublic(),
                    new CensusOfSlovakia(),
                    new CensusOfDenmark(),
                    new CensusOfCanada(),
                ];
        }
    }
}
