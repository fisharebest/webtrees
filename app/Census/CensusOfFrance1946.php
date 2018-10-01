<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
class CensusOfFrance1946 extends CensusOfFrance implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '17 JAN 1946';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns(): array
    {
        return [
            new CensusColumnSurname($this, 'Nom', 'Nom de famille'),
            new CensusColumnGivenNames($this, 'Prénom', 'Prénom usuel'),
            new CensusColumnRelationToHead($this, 'Parenté', 'Parenté avec le chef de ménage ou situation dans le ménage'),
            new CensusColumnBirthYear($this, 'Année', 'Année de naissance'),
            new CensusColumnNationality($this, 'Nationalité', ''),
            new CensusColumnOccupation($this, 'Profession', ''),
        ];
    }
}
