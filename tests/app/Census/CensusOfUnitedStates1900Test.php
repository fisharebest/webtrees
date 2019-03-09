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
 * Test harness for the class CensusOfUnitedStates1900
 */
class CensusOfUnitedStates1900Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1900
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfUnitedStates1900();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('01 JUN 1900', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1900
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfUnitedStates1900();
        $columns = $census->columns();

        $this->assertCount(26, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSexMF', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthMonth', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthYear', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionUs', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnYearsMarried', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnChildrenLiving', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlaceSimple', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFatherBirthPlaceSimple', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnMotherBirthPlaceSimple', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[16]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[17]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[18]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[19]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[20]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[21]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[22]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[23]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[24]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[25]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Relation', $columns[1]->abbreviation());
        $this->assertSame('Race', $columns[2]->abbreviation());
        $this->assertSame('Sex', $columns[3]->abbreviation());
        $this->assertSame('Month', $columns[4]->abbreviation());
        $this->assertSame('Year', $columns[5]->abbreviation());
        $this->assertSame('Age', $columns[6]->abbreviation());
        $this->assertSame('Cond', $columns[7]->abbreviation());
        $this->assertSame('Marr', $columns[8]->abbreviation());
        $this->assertSame('Chil', $columns[9]->abbreviation());
        $this->assertSame('Chil', $columns[10]->abbreviation());
        $this->assertSame('BP', $columns[11]->abbreviation());
        $this->assertSame('FBP', $columns[12]->abbreviation());
        $this->assertSame('MBP', $columns[13]->abbreviation());
        $this->assertSame('Imm', $columns[14]->abbreviation());
        $this->assertSame('US', $columns[15]->abbreviation());
        $this->assertSame('Nat', $columns[16]->abbreviation());
        $this->assertSame('Occupation', $columns[17]->abbreviation());
        $this->assertSame('Unemp', $columns[18]->abbreviation());
        $this->assertSame('School', $columns[19]->abbreviation());
        $this->assertSame('Read', $columns[20]->abbreviation());
        $this->assertSame('Write', $columns[21]->abbreviation());
        $this->assertSame('Eng', $columns[22]->abbreviation());
        $this->assertSame('Home', $columns[23]->abbreviation());
        $this->assertSame('Mort', $columns[24]->abbreviation());
        $this->assertSame('Farm', $columns[25]->abbreviation());

        $this->assertSame('Name', $columns[0]->title());
        $this->assertSame('Relationship of each person to the head of the family', $columns[1]->title());
        $this->assertSame('Color or race', $columns[2]->title());
        $this->assertSame('Sex', $columns[3]->title());
        $this->assertSame('Month of birth', $columns[4]->title());
        $this->assertSame('Year of birth', $columns[5]->title());
        $this->assertSame('Age at last birthday', $columns[6]->title());
        $this->assertSame('Whether single, married, widowed, or divorced', $columns[7]->title());
        $this->assertSame('Number of years married', $columns[8]->title());
        $this->assertSame('Mother of how many children', $columns[9]->title());
        $this->assertSame('Number of these children living', $columns[10]->title());
        $this->assertSame('Place of birth of this person', $columns[11]->title());
        $this->assertSame('Place of birth of father of this person', $columns[12]->title());
        $this->assertSame('Place of birth of mother of this person', $columns[13]->title());
        $this->assertSame('Year of immigration to the United States', $columns[14]->title());
        $this->assertSame('Number of years in the United States', $columns[15]->title());
        $this->assertSame('Naturalization', $columns[16]->title());
        $this->assertSame('Occupation, trade of profession', $columns[17]->title());
        $this->assertSame('Months not unemployed', $columns[18]->title());
        $this->assertSame('Attended school (in months)', $columns[19]->title());
        $this->assertSame('Can read', $columns[20]->title());
        $this->assertSame('Can write', $columns[21]->title());
        $this->assertSame('Can speak English', $columns[22]->title());
        $this->assertSame('Owned or rented', $columns[23]->title());
        $this->assertSame('Owned free or mortgaged', $columns[24]->title());
        $this->assertSame('Farm or house', $columns[25]->title());
    }
}
