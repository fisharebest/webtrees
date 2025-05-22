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
class CensusOfFrance1851 extends CensusOfFrance implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '16 JAN 1851';
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
            new CensusColumnOccupation($this, 'Professions', ''),
            new CensusColumnConditionFrenchGarcon($this, 'Garçons', ''),
            new CensusColumnConditionFrenchHomme($this, 'Hommes', 'Hommes mariés'),
            new CensusColumnConditionFrenchVeuf($this, 'Veufs', ''),
            new CensusColumnConditionFrenchFille($this, 'Filles', ''),
            new CensusColumnConditionFrenchFemme($this, 'Femmes', 'Femmes mariées'),
            new CensusColumnConditionFrenchVeuve($this, 'Veuves', ''),
            new CensusColumnAge($this, 'Âge', 'Âge'),
            new CensusColumnNull($this, 'Fr', 'Français d’origine'),
            new CensusColumnNull($this, 'Nat', 'Naturalisés français'),
            new CensusColumnNull($this, 'Etr', 'Étrangers (indiquer leur pays d’origine)'),
        ];
    }
}
