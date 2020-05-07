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

namespace Fisharebest\Webtrees\Census;

/**
 * Definitions for a census
 */
class CensusOfCzechRepublic extends Census implements CensusPlaceInterface
{
    /**
     * All available censuses for this census place.
     *
     * @return CensusInterface[]
     */
    public function allCensusDates(): array
    {
        return [
            new CensusOfCzechRepublic1880(),
            new CensusOfCzechRepublic1890(),
            new CensusOfCzechRepublic1900(),
            new CensusOfCzechRepublic1910(),
            new CensusOfCzechRepublic1921(),
        ];
    }

    /**
     * Where did this census occur, in GEDCOM format.
     *
     * @return string
     */
    public function censusPlace(): string
    {
        return 'Česko';
    }

    /**
     * In which language was this census written.
     *
     * @return string
     */
    public function censusLanguage(): string
    {
        return 'cs';
    }
}
