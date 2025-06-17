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

/**
 * Definitions for a census
 */
class CensusOfUnitedStates1790 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '02 AUG 1790';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnFullName($this, 'Name', 'Name of head of family'),
            new CensusColumnOccupation($this, 'Occupation', 'Professions and occupation'),
            new CensusColumnNull($this, 'White male 16+', 'White male of 16 yrs upward'),
            new CensusColumnNull($this, 'White male 0-16', 'White males of under 16 yrs'),
            new CensusColumnNull($this, 'White female', 'All White Females'),
            new CensusColumnNull($this, 'Free', 'All other free persons'),
            new CensusColumnNull($this, 'Slaves', 'Number of slaves'),
            new CensusColumnNull($this, 'Total', 'Total'),
        ];
    }
}
