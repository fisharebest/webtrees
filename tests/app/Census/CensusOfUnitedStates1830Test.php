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

declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfUnitedStates1830
 */
class CensusOfUnitedStates1830Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1830
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1830();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('01 JUN 1830', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1830
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1830();
        $columns = $census->columns();

        $this->assertCount(51, $columns);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[13]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[14]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[16]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[17]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[18]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[19]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[20]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[21]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[22]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[23]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[24]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[25]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[26]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[27]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[28]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[29]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[30]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[31]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[32]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[33]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[34]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[35]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[36]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[37]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[38]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[39]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[40]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[41]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[42]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[43]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[44]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[45]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[46]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[47]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[48]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[49]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[50]);

        $this->assertSame('Name', $columns[0]->abbreviation());
        $this->assertSame('M0', $columns[1]->abbreviation());
        $this->assertSame('M5', $columns[2]->abbreviation());
        $this->assertSame('M10', $columns[3]->abbreviation());
        $this->assertSame('M15', $columns[4]->abbreviation());
        $this->assertSame('M20', $columns[5]->abbreviation());
        $this->assertSame('M30', $columns[6]->abbreviation());
        $this->assertSame('M40', $columns[7]->abbreviation());
        $this->assertSame('M50', $columns[8]->abbreviation());
        $this->assertSame('M60', $columns[9]->abbreviation());
        $this->assertSame('M70', $columns[10]->abbreviation());
        $this->assertSame('M80', $columns[11]->abbreviation());
        $this->assertSame('M90', $columns[12]->abbreviation());
        $this->assertSame('M100', $columns[13]->abbreviation());
        $this->assertSame('F0', $columns[14]->abbreviation());
        $this->assertSame('F5', $columns[15]->abbreviation());
        $this->assertSame('F10', $columns[16]->abbreviation());
        $this->assertSame('F15', $columns[17]->abbreviation());
        $this->assertSame('F20', $columns[18]->abbreviation());
        $this->assertSame('F30', $columns[19]->abbreviation());
        $this->assertSame('F40', $columns[20]->abbreviation());
        $this->assertSame('F50', $columns[21]->abbreviation());
        $this->assertSame('F60', $columns[22]->abbreviation());
        $this->assertSame('F70', $columns[23]->abbreviation());
        $this->assertSame('F80', $columns[24]->abbreviation());
        $this->assertSame('F90', $columns[25]->abbreviation());
        $this->assertSame('F100', $columns[26]->abbreviation());
        $this->assertSame('M0', $columns[27]->abbreviation());
        $this->assertSame('M10', $columns[28]->abbreviation());
        $this->assertSame('M24', $columns[29]->abbreviation());
        $this->assertSame('M36', $columns[30]->abbreviation());
        $this->assertSame('M55', $columns[31]->abbreviation());
        $this->assertSame('M100', $columns[32]->abbreviation());
        $this->assertSame('F0', $columns[33]->abbreviation());
        $this->assertSame('F10', $columns[34]->abbreviation());
        $this->assertSame('F24', $columns[35]->abbreviation());
        $this->assertSame('F36', $columns[36]->abbreviation());
        $this->assertSame('F55', $columns[37]->abbreviation());
        $this->assertSame('F100', $columns[38]->abbreviation());
        $this->assertSame('M0', $columns[39]->abbreviation());
        $this->assertSame('M10', $columns[40]->abbreviation());
        $this->assertSame('M24', $columns[41]->abbreviation());
        $this->assertSame('M36', $columns[42]->abbreviation());
        $this->assertSame('M55', $columns[43]->abbreviation());
        $this->assertSame('M100', $columns[44]->abbreviation());
        $this->assertSame('F0', $columns[45]->abbreviation());
        $this->assertSame('F10', $columns[46]->abbreviation());
        $this->assertSame('F24', $columns[47]->abbreviation());
        $this->assertSame('F36', $columns[48]->abbreviation());
        $this->assertSame('F55', $columns[49]->abbreviation());
        $this->assertSame('F100', $columns[50]->abbreviation());

        $this->assertSame('Name of head of family', $columns[0]->title());
        $this->assertSame('Free white males 0-5 years', $columns[1]->title());
        $this->assertSame('Free white males 5-10 years', $columns[2]->title());
        $this->assertSame('Free white males 10-15 years', $columns[3]->title());
        $this->assertSame('Free white males 15-20 years', $columns[4]->title());
        $this->assertSame('Free white males 20-30 years', $columns[5]->title());
        $this->assertSame('Free white males 30-40 years', $columns[6]->title());
        $this->assertSame('Free white males 40-50 years', $columns[7]->title());
        $this->assertSame('Free white males 50-60 years', $columns[8]->title());
        $this->assertSame('Free white males 60-70 years', $columns[9]->title());
        $this->assertSame('Free white males 70-80 years', $columns[10]->title());
        $this->assertSame('Free white males 80-90 years', $columns[11]->title());
        $this->assertSame('Free white males 90-100 years', $columns[12]->title());
        $this->assertSame('Free white males 100+ years', $columns[13]->title());
        $this->assertSame('Free white females 0-5 years', $columns[14]->title());
        $this->assertSame('Free white females 5-10 years', $columns[15]->title());
        $this->assertSame('Free white females 10-15 years', $columns[16]->title());
        $this->assertSame('Free white females 15-20 years', $columns[17]->title());
        $this->assertSame('Free white females 20-30 years', $columns[18]->title());
        $this->assertSame('Free white females 30-40 years', $columns[19]->title());
        $this->assertSame('Free white females 40-50 years', $columns[20]->title());
        $this->assertSame('Free white females 50-60 years', $columns[21]->title());
        $this->assertSame('Free white females 60-70 years', $columns[22]->title());
        $this->assertSame('Free white females 70-80 years', $columns[23]->title());
        $this->assertSame('Free white females 80-90 years', $columns[24]->title());
        $this->assertSame('Free white females 90-100 years', $columns[25]->title());
        $this->assertSame('Free white females 100+ years', $columns[26]->title());
        $this->assertSame('Slave males 0-10 years', $columns[27]->title());
        $this->assertSame('Slave males 10-24 years', $columns[28]->title());
        $this->assertSame('Slave males 24-36 years', $columns[29]->title());
        $this->assertSame('Slave males 36-55 years', $columns[30]->title());
        $this->assertSame('Slave males 55-100 years', $columns[31]->title());
        $this->assertSame('Slave males 100+ years', $columns[32]->title());
        $this->assertSame('Slave females 0-10 years', $columns[33]->title());
        $this->assertSame('Slave females 10-24 years', $columns[34]->title());
        $this->assertSame('Slave females 24-36 years', $columns[35]->title());
        $this->assertSame('Slave females 36-55 years', $columns[36]->title());
        $this->assertSame('Slave females 55-100 years', $columns[37]->title());
        $this->assertSame('Slave females 100+ years', $columns[38]->title());
        $this->assertSame('Free colored males 0-10 years', $columns[39]->title());
        $this->assertSame('Free colored males 10-24 years', $columns[40]->title());
        $this->assertSame('Free colored males 24-36 years', $columns[41]->title());
        $this->assertSame('Free colored males 36-55 years', $columns[42]->title());
        $this->assertSame('Free colored males 55-100 years', $columns[43]->title());
        $this->assertSame('Free colored males 100+ years', $columns[44]->title());
        $this->assertSame('Free colored females 0-10 years', $columns[45]->title());
        $this->assertSame('Free colored females 10-24 years', $columns[46]->title());
        $this->assertSame('Free colored females 24-36 years', $columns[47]->title());
        $this->assertSame('Free colored females 36-55 years', $columns[48]->title());
        $this->assertSame('Free colored females 55-100 years', $columns[49]->title());
        $this->assertSame('Free colored females 100+ years', $columns[50]->title());
    }
}
