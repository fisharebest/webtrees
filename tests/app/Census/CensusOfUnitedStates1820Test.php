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
 * Test harness for the class CensusOfUnitedStates1820
 */
class CensusOfUnitedStates1820Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1820
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfUnitedStates1820();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('07 AUG 1820', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1820
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfUnitedStates1820();
        $columns = $census->columns();

        $this->assertCount(32, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnFullName', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[9]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[10]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[11]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[12]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[13]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[14]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[15]);
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
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[29]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[30]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[31]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('M0-10', $columns[1]->abbreviation());
        $this->assertSame('M10-16', $columns[2]->abbreviation());
        $this->assertSame('M16-18', $columns[3]->abbreviation());
        $this->assertSame('M16-26', $columns[4]->abbreviation());
        $this->assertSame('M26-45', $columns[5]->abbreviation());
        $this->assertSame('M45+', $columns[6]->abbreviation());
        $this->assertSame('F0-10', $columns[7]->abbreviation());
        $this->assertSame('F10-16', $columns[8]->abbreviation());
        $this->assertSame('F16-26', $columns[9]->abbreviation());
        $this->assertSame('F26-45', $columns[10]->abbreviation());
        $this->assertSame('F45+', $columns[11]->abbreviation());
        $this->assertSame('FNR', $columns[12]->abbreviation());
        $this->assertSame('AG', $columns[13]->abbreviation());
        $this->assertSame('COM', $columns[14]->abbreviation());
        $this->assertSame('MNF', $columns[15]->abbreviation());
        $this->assertSame('M0', $columns[16]->abbreviation());
        $this->assertSame('M14', $columns[17]->abbreviation());
        $this->assertSame('M26', $columns[18]->abbreviation());
        $this->assertSame('M45', $columns[19]->abbreviation());
        $this->assertSame('F0', $columns[20]->abbreviation());
        $this->assertSame('F14', $columns[21]->abbreviation());
        $this->assertSame('F26', $columns[22]->abbreviation());
        $this->assertSame('F45', $columns[23]->abbreviation());
        $this->assertSame('M0', $columns[24]->abbreviation());
        $this->assertSame('M14', $columns[25]->abbreviation());
        $this->assertSame('M26', $columns[26]->abbreviation());
        $this->assertSame('M45', $columns[27]->abbreviation());
        $this->assertSame('F0', $columns[28]->abbreviation());
        $this->assertSame('F14', $columns[29]->abbreviation());
        $this->assertSame('F26', $columns[30]->abbreviation());
        $this->assertSame('F45', $columns[31]->abbreviation());

        $this->assertSame('Name of head of family', $columns[0]->title());
        $this->assertSame('Free white males 0-10 years', $columns[1]->title());
        $this->assertSame('Free white males 10-16 years', $columns[2]->title());
        $this->assertSame('Free white males 16-18 years', $columns[3]->title());
        $this->assertSame('Free white males 16-26 years', $columns[4]->title());
        $this->assertSame('Free white males 26-45 years', $columns[5]->title());
        $this->assertSame('Free white males 45+ years', $columns[6]->title());
        $this->assertSame('Free white females 0-10 years', $columns[7]->title());
        $this->assertSame('Free white females 10-16 years', $columns[8]->title());
        $this->assertSame('Free white females 16-26 years', $columns[9]->title());
        $this->assertSame('Free white females 26-45 years', $columns[10]->title());
        $this->assertSame('Free white females 45+ years', $columns[11]->title());
        $this->assertSame('Foreigners not naturalized', $columns[12]->title());
        $this->assertSame('No. engaged in agriculture', $columns[13]->title());
        $this->assertSame('No. engaged in commerce', $columns[14]->title());
        $this->assertSame('No. engaged in manufactures', $columns[15]->title());
        $this->assertSame('Slave males 0-14 years', $columns[16]->title());
        $this->assertSame('Slave males 14-26 years', $columns[17]->title());
        $this->assertSame('Slave males 26-45 years', $columns[18]->title());
        $this->assertSame('Slave males 45+ years', $columns[19]->title());
        $this->assertSame('Slave females 0-14 years', $columns[20]->title());
        $this->assertSame('Slave females 14-26 years', $columns[21]->title());
        $this->assertSame('Slave females 26-45 years', $columns[22]->title());
        $this->assertSame('Slave females 45+ years', $columns[23]->title());
        $this->assertSame('Free colored males 0-14 years', $columns[24]->title());
        $this->assertSame('Free colored males 14-26 years', $columns[25]->title());
        $this->assertSame('Free colored males 26-45 years', $columns[26]->title());
        $this->assertSame('Free colored males 45+ years', $columns[27]->title());
        $this->assertSame('Free colored females 0-14 years', $columns[28]->title());
        $this->assertSame('Free colored females 14-26 years', $columns[29]->title());
        $this->assertSame('Free colored females 26-45 years', $columns[30]->title());
        $this->assertSame('Free colored females 45+ years', $columns[31]->title());
    }
}
