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
namespace Fisharebest\Webtrees\Census;

/**
 * Test harness for the class CensusOfUnitedStates1860
 */
class CensusOfUnitedStates1860Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1860
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfUnitedStates1860();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('BET JUN 1860 AND OCT 1860', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1860
     */
    public function testColumns()
    {
        $census  = new CensusOfUnitedStates1860();
        $columns = $census->columns();

        $this->assertCount(12, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSexMF', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlace', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnMarriedWithinYear', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Age', $columns[1]->abbreviation());
        $this->assertSame('Sex', $columns[2]->abbreviation());
        $this->assertSame('Color', $columns[3]->abbreviation());
        $this->assertSame('Occupation', $columns[4]->abbreviation());
        $this->assertSame('RE', $columns[5]->abbreviation());
        $this->assertSame('PE', $columns[6]->abbreviation());
        $this->assertSame('Birthplace', $columns[7]->abbreviation());
        $this->assertSame('Mar', $columns[8]->abbreviation());
        $this->assertSame('School', $columns[9]->abbreviation());
        $this->assertSame('R+W', $columns[10]->abbreviation());
        $this->assertSame('Infirm', $columns[11]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Age', $columns[1]->title());
        $this->assertSame('Sex', $columns[2]->title());
        $this->assertSame('White, black, or mulatto', $columns[3]->title());
        $this->assertSame('Profession, occupation, or trade', $columns[4]->title());
        $this->assertSame('Value of real estate owned', $columns[5]->title());
        $this->assertSame('Value of personal estate owned', $columns[6]->title());
        $this->assertSame('Place of birth, naming the state, territory, or country', $columns[7]->title());
        $this->assertSame('Married within the year', $columns[8]->title());
        $this->assertSame('Attended school within the year', $columns[9]->title());
        $this->assertSame('Persons over 20 years of age who cannot read and write', $columns[10]->title());
        $this->assertSame('Whether deaf and dumb, blind, insane, idiotic, pauper or convict', $columns[11]->title());
    }
}
