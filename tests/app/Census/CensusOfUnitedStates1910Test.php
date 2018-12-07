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
 * Test harness for the class CensusOfUnitedStates1910
 */
class CensusOfUnitedStates1910Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1910
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfUnitedStates1910();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('15 APR 1910', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1910
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfUnitedStates1910();
        $columns = $census->columns();

        $this->assertCount(29, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurnameGivenNameInitial', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSexMF', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionUs', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnYearsMarried', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnChildrenLiving', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlaceSimple', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlaceSimple', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnMotherBirthPlaceSimple', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[17]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[18]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[19]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[20]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[21]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[22]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[23]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[24]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[25]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[26]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[27]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[28]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Relation', $columns[1]->abbreviation());
        $this->assertSame('Sex', $columns[2]->abbreviation());
        $this->assertSame('Race', $columns[3]->abbreviation());
        $this->assertSame('Age', $columns[4]->abbreviation());
        $this->assertSame('Cond', $columns[5]->abbreviation());
        $this->assertSame('Marr', $columns[6]->abbreviation());
        $this->assertSame('Chil', $columns[7]->abbreviation());
        $this->assertSame('Chil', $columns[8]->abbreviation());
        $this->assertSame('BP', $columns[9]->abbreviation());
        $this->assertSame('FBP', $columns[10]->abbreviation());
        $this->assertSame('MBP', $columns[11]->abbreviation());
        $this->assertSame('Imm', $columns[12]->abbreviation());
        $this->assertSame('Nat', $columns[13]->abbreviation());
        $this->assertSame('Lang', $columns[14]->abbreviation());
        $this->assertSame('Occupation', $columns[15]->abbreviation());
        $this->assertSame('Ind', $columns[16]->abbreviation());
        $this->assertSame('Emp', $columns[17]->abbreviation());
        $this->assertSame('Unemp', $columns[18]->abbreviation());
        $this->assertSame('Unemp', $columns[19]->abbreviation());
        $this->assertSame('R', $columns[20]->abbreviation());
        $this->assertSame('W', $columns[21]->abbreviation());
        $this->assertSame('Sch', $columns[22]->abbreviation());
        $this->assertSame('Home', $columns[23]->abbreviation());
        $this->assertSame('Mort', $columns[24]->abbreviation());
        $this->assertSame('Farm', $columns[25]->abbreviation());
        $this->assertSame('CW', $columns[26]->abbreviation());
        $this->assertSame('Blind', $columns[27]->abbreviation());
        $this->assertSame('Deaf', $columns[28]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        $this->assertSame('Sex', $columns[2]->title());
        $this->assertSame('Color or race', $columns[3]->title());
        $this->assertSame('Age at last birthday', $columns[4]->title());
        $this->assertSame('Whether single, married, widowed, or divorced', $columns[5]->title());
        $this->assertSame('Number of years of present marriage', $columns[6]->title());
        $this->assertSame('Mother of how many children', $columns[7]->title());
        $this->assertSame('Number of these children living', $columns[8]->title());
        $this->assertSame('Place of birth of this person', $columns[9]->title());
        $this->assertSame('Place of birth of father of this person', $columns[10]->title());
        $this->assertSame('Place of birth of mother of this person', $columns[11]->title());
        $this->assertSame('Year of immigration to the United States', $columns[12]->title());
        $this->assertSame('Whether naturalized or alien', $columns[13]->title());
        $this->assertSame('Whether able to speak English, of if not, give language spoken', $columns[14]->title());
        $this->assertSame('Trade or profession of, or particular kind of work done by this person', $columns[15]->title());
        $this->assertSame('General nature of industry', $columns[16]->title());
        $this->assertSame('Whether an employer, employee, or work on own account', $columns[17]->title());
        $this->assertSame('Whether out of work on April 15, 1910', $columns[18]->title());
        $this->assertSame('Number of weeks out of work in 1909', $columns[19]->title());
        $this->assertSame('Whether able to read', $columns[20]->title());
        $this->assertSame('Whether able to write', $columns[21]->title());
        $this->assertSame('Attended school since September 1, 1909', $columns[22]->title());
        $this->assertSame('Owned or rented', $columns[23]->title());
        $this->assertSame('Owned free or mortgaged', $columns[24]->title());
        $this->assertSame('Farm or house', $columns[25]->title());
        $this->assertSame('Whether a survivor of the Union or Confederate Army or Navy', $columns[26]->title());
        $this->assertSame('Whether blind (both eyes)', $columns[27]->title());
        $this->assertSame('Whether deaf and dumb', $columns[28]->title());
    }
}
