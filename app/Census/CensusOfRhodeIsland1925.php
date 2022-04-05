<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
class CensusOfRhodeIsland1925 extends CensusOfRhodeIsland implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return 'APR 1925';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnSurnameGivenNameInitial($this, 'Name', 'Name'),
            new CensusColumnRelationToHeadEnglish($this, 'Relation', 'Relationship of this person to head of the family'),
            new CensusColumnSexMF($this, 'Sex', 'Sex'),
            new CensusColumnNull($this, 'Race', 'Color or race'),
            new CensusColumnAge($this, 'Age', 'Age at last birthday'),
            new CensusColumnBirthPlaceSimple($this, 'BP', 'Place of birth'),
            new CensusColumnNull($this, 'Cit', 'Citzenship'),
        ];
    }
}
