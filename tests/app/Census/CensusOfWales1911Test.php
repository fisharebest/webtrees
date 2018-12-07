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
 * Test harness for the class CensusOfWales1911
 */
class CensusOfWales1911Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfWales1911
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfWales1911();

        $this->assertSame('Wales', $census->censusPlace());
        $this->assertSame('02 APR 1911', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfWales1911
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfWales1911();
        $columns = $census->columns();

        $this->assertCount(17, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAgeMale', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAgeFemale', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionEnglish', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnYearsMarried', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnChildrenBornAlive', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnChildrenLiving', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnChildrenDied', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlace', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNationality', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[16]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('Relation', $columns[1]->abbreviation());
        $this->assertSame('AgeM', $columns[2]->abbreviation());
        $this->assertSame('AgeF', $columns[3]->abbreviation());
        $this->assertSame('Condition', $columns[4]->abbreviation());
        $this->assertSame('YrM', $columns[5]->abbreviation());
        $this->assertSame('ChA', $columns[6]->abbreviation());
        $this->assertSame('ChL', $columns[7]->abbreviation());
        $this->assertSame('ChD', $columns[8]->abbreviation());
        $this->assertSame('Occupation', $columns[9]->abbreviation());
        $this->assertSame('Ind', $columns[10]->abbreviation());
        $this->assertSame('Emp', $columns[11]->abbreviation());
        $this->assertSame('Home', $columns[12]->abbreviation());
        $this->assertSame('Birthplace', $columns[13]->abbreviation());
        $this->assertSame('Nat', $columns[14]->abbreviation());
        $this->assertSame('Infirm', $columns[15]->abbreviation());
        $this->assertSame('Lang', $columns[16]->abbreviation());

        $this->assertSame('Name and surname', $columns[0]->title());
        $this->assertSame('Relation to head of household', $columns[1]->title());
        $this->assertSame('Age (males)', $columns[2]->title());
        $this->assertSame('Age (females)', $columns[3]->title());
        $this->assertSame('Condition', $columns[4]->title());
        $this->assertSame('Years married', $columns[5]->title());
        $this->assertSame('Children born alive', $columns[6]->title());
        $this->assertSame('Children who are still alive', $columns[7]->title());
        $this->assertSame('Children who have died', $columns[8]->title());
        $this->assertSame('Rank, profession or occupation', $columns[9]->title());
        $this->assertSame('Industry', $columns[10]->title());
        $this->assertSame('Employer, worker or own account', $columns[11]->title());
        $this->assertSame('Working at home', $columns[12]->title());
        $this->assertSame('Where born', $columns[13]->title());
        $this->assertSame('Nationality', $columns[14]->title());
        $this->assertSame('Infirmity', $columns[15]->title());
        $this->assertSame('Language spoken', $columns[16]->title());
    }
}
