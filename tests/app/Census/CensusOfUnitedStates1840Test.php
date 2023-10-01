<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CensusOfUnitedStates1840
 */
class CensusOfUnitedStates1840Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1840
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1840();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('01 JUN 1840', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1840
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1840();
        $columns = $census->columns();

        self::assertCount(39, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[23]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[24]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[25]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[26]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[27]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[28]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[29]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[30]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[31]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[32]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[33]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[34]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[35]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[36]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[37]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[38]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('M0', $columns[1]->abbreviation());
        self::assertSame('M5', $columns[2]->abbreviation());
        self::assertSame('M10', $columns[3]->abbreviation());
        self::assertSame('M15', $columns[4]->abbreviation());
        self::assertSame('M20', $columns[5]->abbreviation());
        self::assertSame('M30', $columns[6]->abbreviation());
        self::assertSame('M40', $columns[7]->abbreviation());
        self::assertSame('M50', $columns[8]->abbreviation());
        self::assertSame('M60', $columns[9]->abbreviation());
        self::assertSame('M70', $columns[10]->abbreviation());
        self::assertSame('M80', $columns[11]->abbreviation());
        self::assertSame('M90', $columns[12]->abbreviation());
        self::assertSame('M100', $columns[13]->abbreviation());
        self::assertSame('F0', $columns[14]->abbreviation());
        self::assertSame('F5', $columns[15]->abbreviation());
        self::assertSame('F10', $columns[16]->abbreviation());
        self::assertSame('F15', $columns[17]->abbreviation());
        self::assertSame('F20', $columns[18]->abbreviation());
        self::assertSame('F30', $columns[19]->abbreviation());
        self::assertSame('F40', $columns[20]->abbreviation());
        self::assertSame('F50', $columns[21]->abbreviation());
        self::assertSame('F60', $columns[22]->abbreviation());
        self::assertSame('F70', $columns[23]->abbreviation());
        self::assertSame('F80', $columns[24]->abbreviation());
        self::assertSame('F90', $columns[25]->abbreviation());
        self::assertSame('F100', $columns[26]->abbreviation());
        self::assertSame('M0', $columns[27]->abbreviation());
        self::assertSame('M10', $columns[28]->abbreviation());
        self::assertSame('M24', $columns[29]->abbreviation());
        self::assertSame('M36', $columns[30]->abbreviation());
        self::assertSame('M55', $columns[31]->abbreviation());
        self::assertSame('M100', $columns[32]->abbreviation());
        self::assertSame('F0', $columns[33]->abbreviation());
        self::assertSame('F10', $columns[34]->abbreviation());
        self::assertSame('F24', $columns[35]->abbreviation());
        self::assertSame('F36', $columns[36]->abbreviation());
        self::assertSame('F55', $columns[37]->abbreviation());
        self::assertSame('F100', $columns[38]->abbreviation());

        self::assertSame('Name of head of family', $columns[0]->title());
        self::assertSame('Free white males 0-5 years', $columns[1]->title());
        self::assertSame('Free white males 5-10 years', $columns[2]->title());
        self::assertSame('Free white males 10-15 years', $columns[3]->title());
        self::assertSame('Free white males 15-20 years', $columns[4]->title());
        self::assertSame('Free white males 20-30 years', $columns[5]->title());
        self::assertSame('Free white males 30-40 years', $columns[6]->title());
        self::assertSame('Free white males 40-50 years', $columns[7]->title());
        self::assertSame('Free white males 50-60 years', $columns[8]->title());
        self::assertSame('Free white males 60-70 years', $columns[9]->title());
        self::assertSame('Free white males 70-80 years', $columns[10]->title());
        self::assertSame('Free white males 80-90 years', $columns[11]->title());
        self::assertSame('Free white males 90-100 years', $columns[12]->title());
        self::assertSame('Free white males 100+ years', $columns[13]->title());
        self::assertSame('Free white females 0-5 years', $columns[14]->title());
        self::assertSame('Free white females 5-10 years', $columns[15]->title());
        self::assertSame('Free white females 10-15 years', $columns[16]->title());
        self::assertSame('Free white females 15-20 years', $columns[17]->title());
        self::assertSame('Free white females 20-30 years', $columns[18]->title());
        self::assertSame('Free white females 30-40 years', $columns[19]->title());
        self::assertSame('Free white females 40-50 years', $columns[20]->title());
        self::assertSame('Free white females 50-60 years', $columns[21]->title());
        self::assertSame('Free white females 60-70 years', $columns[22]->title());
        self::assertSame('Free white females 70-80 years', $columns[23]->title());
        self::assertSame('Free white females 80-90 years', $columns[24]->title());
        self::assertSame('Free white females 90-100 years', $columns[25]->title());
        self::assertSame('Free white females 100+ years', $columns[26]->title());
        self::assertSame('Free colored males 0-10 years', $columns[27]->title());
        self::assertSame('Free colored males 10-24 years', $columns[28]->title());
        self::assertSame('Free colored males 24-36 years', $columns[29]->title());
        self::assertSame('Free colored males 36-55 years', $columns[30]->title());
        self::assertSame('Free colored males 55-100 years', $columns[31]->title());
        self::assertSame('Free colored males 100+ years', $columns[32]->title());
        self::assertSame('Free colored females 0-10 years', $columns[33]->title());
        self::assertSame('Free colored females 10-24 years', $columns[34]->title());
        self::assertSame('Free colored females 24-36 years', $columns[35]->title());
        self::assertSame('Free colored females 36-55 years', $columns[36]->title());
        self::assertSame('Free colored females 55-100 years', $columns[37]->title());
        self::assertSame('Free colored females 100+ years', $columns[38]->title());
    }
}
