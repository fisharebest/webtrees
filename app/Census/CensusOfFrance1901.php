<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
class CensusOfFrance1901 extends CensusOfFrance implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '17 JAN 1901';
    }

    /**
     * The columns of the census.
     *
     * @return array<CensusColumnInterface>
     */
    public function columns(): array
    {
        return [
            new CensusColumnSurname($this, 'Noms', 'Noms de famille'),
            new CensusColumnGivenNames($this, 'Prénoms', ''),
            new CensusColumnAge($this, 'Âge', ''),
            new CensusColumnNationality($this, 'Nationalité', ''),
            new CensusColumnRelationToHead($this, 'Situation', 'Situation par rapport au chef de ménage'),
            new CensusColumnOccupation($this, 'Profession', ''),
            new CensusColumnBirthPlace($this, 'Lieu', 'Lieu de naissance'),
            new CensusColumnNull($this, 'Empl', ''),
        ];
    }
}
