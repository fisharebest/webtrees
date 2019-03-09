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
namespace Fisharebest\Webtrees\Census;

/**
 * Definitions for a census
 */
class CensusOfFrance1931 extends CensusOfFrance implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate()
    {
        return '15 JAN 1931';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns()
    {
        return array(
            new CensusColumnSurname($this, 'Noms', 'Noms de famille'),
            new CensusColumnGivenNames($this, 'Prénoms', ''),
            new CensusColumnBirthYear($this, 'Année', 'Année de naissance'),
            new CensusColumnBirthPlace($this, 'Lieu', 'Lieu de naissance'),
            new CensusColumnNationality($this, 'Nationalité', ''),
            new CensusColumnRelationToHead($this, 'Situation', 'Situation par rapport au chef de ménage'),
            new CensusColumnOccupation($this, 'Profession', ''),
            new CensusColumnNull($this, 'Empl', ''),
        );
    }
}
