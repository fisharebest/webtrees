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
class CensusOfUnitedStates1820 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate(): string
    {
        return '07 AUG 1820';
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
            new CensusColumnNull($this, 'M0-10', 'Free white males 0-10 years'),
            new CensusColumnNull($this, 'M10-16', 'Free white males 10-16 years'),
            new CensusColumnNull($this, 'M16-18', 'Free white males 16-18 years'),
            new CensusColumnNull($this, 'M16-26', 'Free white males 16-26 years'),
            new CensusColumnNull($this, 'M26-45', 'Free white males 26-45 years'),
            new CensusColumnNull($this, 'M45+', 'Free white males 45+ years'),
            new CensusColumnNull($this, 'F0-10', 'Free white females 0-10 years'),
            new CensusColumnNull($this, 'F10-16', 'Free white females 10-16 years'),
            new CensusColumnNull($this, 'F16-26', 'Free white females 16-26 years'),
            new CensusColumnNull($this, 'F26-45', 'Free white females 26-45 years'),
            new CensusColumnNull($this, 'F45+', 'Free white females 45+ years'),
            new CensusColumnNull($this, 'FNR', 'Foreigners not naturalized'),
            new CensusColumnNull($this, 'AG', 'No. engaged in agriculture'),
            new CensusColumnNull($this, 'COM', 'No. engaged in commerce'),
            new CensusColumnNull($this, 'MNF', 'No. engaged in manufactures'),
            new CensusColumnNull($this, 'M0', 'Slave males 0-14 years'),
            new CensusColumnNull($this, 'M14', 'Slave males 14-26 years'),
            new CensusColumnNull($this, 'M26', 'Slave males 26-45 years'),
            new CensusColumnNull($this, 'M45', 'Slave males 45+ years'),
            new CensusColumnNull($this, 'F0', 'Slave females 0-14 years'),
            new CensusColumnNull($this, 'F14', 'Slave females 14-26 years'),
            new CensusColumnNull($this, 'F26', 'Slave females 26-45 years'),
            new CensusColumnNull($this, 'F45', 'Slave females 45+ years'),
            new CensusColumnNull($this, 'M0', 'Free colored males 0-14 years'),
            new CensusColumnNull($this, 'M14', 'Free colored males 14-26 years'),
            new CensusColumnNull($this, 'M26', 'Free colored males 26-45 years'),
            new CensusColumnNull($this, 'M45', 'Free colored males 45+ years'),
            new CensusColumnNull($this, 'F0', 'Free colored females 0-14 years'),
            new CensusColumnNull($this, 'F14', 'Free colored females 14-26 years'),
            new CensusColumnNull($this, 'F26', 'Free colored females 26-45 years'),
            new CensusColumnNull($this, 'F45', 'Free colored females 45+ years'),
        ];
    }
}
