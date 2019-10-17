<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository\Interfaces;

use stdClass;

/**
 * A repository providing methods for place related statistics.
 */
interface PlaceRepositoryInterface
{
    /**
     * Places
     *
     * @param string $what
     * @param string $fact
     * @param int    $parent
     * @param bool   $country
     *
     * @return int[]|stdClass[]
     */
    public function statsPlaces(string $what = 'ALL', string $fact = '', int $parent = 0, bool $country = false): array;

    /**
     * A list of common birth places.
     *
     * @return string
     */
    public function commonBirthPlacesList(): string;

    /**
     * A list of common death places.
     *
     * @return string
     */
    public function commonDeathPlacesList(): string;

    /**
     * A list of common marriage places.
     *
     * @return string
     */
    public function commonMarriagePlacesList(): string;

    /**
     * A list of common countries.
     *
     * @return string
     */
    public function commonCountriesList(): string;

    /**
     * Count total places.
     *
     * @return string
     */
    public function totalPlaces(): string;

    /**
     * Create a chart showing where events occurred.
     *
     * @param string $chart_shows
     * @param string $chart_type
     * @param string $surname
     *
     * @return string
     */
    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string;
}
