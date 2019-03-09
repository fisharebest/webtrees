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
 * Test harness for the class CensusOfUnitedStates1940
 */
class CensusOfUnitedStates1940Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1940
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfUnitedStates1940();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('APR 1940', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1940
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfUnitedStates1940();
        $columns = $census->columns();

        $this->assertCount(27, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSexMF', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionUs', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAgeMarried', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlaceSimple', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlaceSimple', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnMotherBirthPlaceSimple', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[17]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[18]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[19]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[20]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[21]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[22]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[23]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[24]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[25]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[26]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Relation', $columns[1]->abbreviation());
        $this->assertSame('Home', $columns[2]->abbreviation());
        $this->assertSame('V/R', $columns[3]->abbreviation());
        $this->assertSame('Farm', $columns[4]->abbreviation());
        $this->assertSame('Sex', $columns[5]->abbreviation());
        $this->assertSame('Race', $columns[6]->abbreviation());
        $this->assertSame('Age', $columns[7]->abbreviation());
        $this->assertSame('Cond', $columns[8]->abbreviation());
        $this->assertSame('AM', $columns[9]->abbreviation());
        $this->assertSame('School', $columns[10]->abbreviation());
        $this->assertSame('R/W', $columns[11]->abbreviation());
        $this->assertSame('BP', $columns[12]->abbreviation());
        $this->assertSame('FBP', $columns[13]->abbreviation());
        $this->assertSame('MBP', $columns[14]->abbreviation());
        $this->assertSame('Lang', $columns[15]->abbreviation());
        $this->assertSame('Imm', $columns[16]->abbreviation());
        $this->assertSame('Nat', $columns[17]->abbreviation());
        $this->assertSame('Eng', $columns[18]->abbreviation());
        $this->assertSame('Occupation', $columns[19]->abbreviation());
        $this->assertSame('Industry', $columns[20]->abbreviation());
        $this->assertSame('Code', $columns[21]->abbreviation());
        $this->assertSame('Emp', $columns[22]->abbreviation());
        $this->assertSame('Work', $columns[23]->abbreviation());
        $this->assertSame('Unemp', $columns[24]->abbreviation());
        $this->assertSame('Vet', $columns[25]->abbreviation());
        $this->assertSame('War', $columns[26]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        $this->assertSame('Home owned or rented', $columns[2]->title());
        $this->assertSame('Value of house, if owned, or monthly rental if rented', $columns[3]->title());
        $this->assertSame('Does this family live on a farm', $columns[4]->title());
        $this->assertSame('Sex', $columns[5]->title());
        $this->assertSame('Color or race', $columns[6]->title());
        $this->assertSame('Age at last birthday', $columns[7]->title());
        $this->assertSame('Whether single, married, widowed, or divorced', $columns[8]->title());
        $this->assertSame('Age at first marriage', $columns[9]->title());
        $this->assertSame('Attended school since Sept. 1, 1929', $columns[10]->title());
        $this->assertSame('Whether able to read and write', $columns[11]->title());
        $this->assertSame('Place of birth', $columns[12]->title());
        $this->assertSame('Place of birth of father', $columns[13]->title());
        $this->assertSame('Place of birth of mother', $columns[14]->title());
        $this->assertSame('Language spoken in home before coming to the United States', $columns[15]->title());
        $this->assertSame('Year of immigration to the United States', $columns[16]->title());
        $this->assertSame('Naturalization', $columns[17]->title());
        $this->assertSame('Whether able to speak English', $columns[18]->title());
        $this->assertSame('Trade, profession, or particular kind of work done', $columns[19]->title());
        $this->assertSame('Industry, business of establishment in which at work', $columns[20]->title());
        $this->assertSame('Industry code', $columns[21]->title());
        $this->assertSame('Class of worker', $columns[22]->title());
        $this->assertSame('Whether normally at work yesterday or the last regular working day', $columns[23]->title());
        $this->assertSame('If not, …', $columns[24]->title());
        $this->assertSame('Whether a veteran of U.S. military or …', $columns[25]->title());
        $this->assertSame('What war or …', $columns[26]->title());
    }
}
