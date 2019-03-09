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
class CensusOfUnitedStates1840 extends CensusOfUnitedStates implements CensusInterface
{
    /**
     * When did this census occur.
     *
     * @return string
     */
    public function censusDate()
    {
        return '01 JUN 1840';
    }

    /**
     * The columns of the census.
     *
     * @return CensusColumnInterface[]
     */
    public function columns()
    {
        return array(

            new CensusColumnFullName($this, 'Name', 'Name of head of family'),
            new CensusColumnNull($this, 'M0', 'Free white males 0-5 years'),
            new CensusColumnNull($this, 'M5', 'Free white males 5-10 years'),
            new CensusColumnNull($this, 'M10', 'Free white males 10-15 years'),
            new CensusColumnNull($this, 'M15', 'Free white males 15-20 years'),
            new CensusColumnNull($this, 'M20', 'Free white males 20-30 years'),
            new CensusColumnNull($this, 'M30', 'Free white males 30-40 years'),
            new CensusColumnNull($this, 'M40', 'Free white males 40-50 years'),
            new CensusColumnNull($this, 'M50', 'Free white males 50-60 years'),
            new CensusColumnNull($this, 'M60', 'Free white males 60-70 years'),
            new CensusColumnNull($this, 'M70', 'Free white males 70-80 years'),
            new CensusColumnNull($this, 'M80', 'Free white males 80-90 years'),
            new CensusColumnNull($this, 'M90', 'Free white males 90-100 years'),
            new CensusColumnNull($this, 'M100', 'Free white males 100+ years'),
            new CensusColumnNull($this, 'F0', 'Free white females 0-5 years'),
            new CensusColumnNull($this, 'F5', 'Free white females 5-10 years'),
            new CensusColumnNull($this, 'F10', 'Free white females 10-15 years'),
            new CensusColumnNull($this, 'F15', 'Free white females 15-20 years'),
            new CensusColumnNull($this, 'F20', 'Free white females 20-30 years'),
            new CensusColumnNull($this, 'F30', 'Free white females 30-40 years'),
            new CensusColumnNull($this, 'F40', 'Free white females 40-50 years'),
            new CensusColumnNull($this, 'F50', 'Free white females 50-60 years'),
            new CensusColumnNull($this, 'F60', 'Free white females 60-70 years'),
            new CensusColumnNull($this, 'F70', 'Free white females 70-80 years'),
            new CensusColumnNull($this, 'F80', 'Free white females 80-90 years'),
            new CensusColumnNull($this, 'F90', 'Free white females 90-100 years'),
            new CensusColumnNull($this, 'F100', 'Free white females 100+ years'),
            new CensusColumnNull($this, 'M0', 'Free colored males 0-10 years'),
            new CensusColumnNull($this, 'M10', 'Free colored males 10-24 years'),
            new CensusColumnNull($this, 'M24', 'Free colored males 24-36 years'),
            new CensusColumnNull($this, 'M36', 'Free colored males 36-55 years'),
            new CensusColumnNull($this, 'M55', 'Free colored males 55-100 years'),
            new CensusColumnNull($this, 'M100', 'Free colored males 100+ years'),
            new CensusColumnNull($this, 'F0', 'Free colored females 0-10 years'),
            new CensusColumnNull($this, 'F10', 'Free colored females 10-24 years'),
            new CensusColumnNull($this, 'F24', 'Free colored females 24-36 years'),
            new CensusColumnNull($this, 'F36', 'Free colored females 36-55 years'),
            new CensusColumnNull($this, 'F55', 'Free colored females 55-100 years'),
            new CensusColumnNull($this, 'F100', 'Free colored females 100+ years'),
        );
    }
}
