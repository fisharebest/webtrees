<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusOfUnitedStates1820::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfUnitedStates1820Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1820();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('07 AUG 1820', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1820();
        $columns = $census->columns();

        self::assertCount(32, $columns);
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

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('M0-10', $columns[1]->abbreviation());
        self::assertSame('M10-16', $columns[2]->abbreviation());
        self::assertSame('M16-18', $columns[3]->abbreviation());
        self::assertSame('M16-26', $columns[4]->abbreviation());
        self::assertSame('M26-45', $columns[5]->abbreviation());
        self::assertSame('M45+', $columns[6]->abbreviation());
        self::assertSame('F0-10', $columns[7]->abbreviation());
        self::assertSame('F10-16', $columns[8]->abbreviation());
        self::assertSame('F16-26', $columns[9]->abbreviation());
        self::assertSame('F26-45', $columns[10]->abbreviation());
        self::assertSame('F45+', $columns[11]->abbreviation());
        self::assertSame('FNR', $columns[12]->abbreviation());
        self::assertSame('AG', $columns[13]->abbreviation());
        self::assertSame('COM', $columns[14]->abbreviation());
        self::assertSame('MNF', $columns[15]->abbreviation());
        self::assertSame('M0', $columns[16]->abbreviation());
        self::assertSame('M14', $columns[17]->abbreviation());
        self::assertSame('M26', $columns[18]->abbreviation());
        self::assertSame('M45', $columns[19]->abbreviation());
        self::assertSame('F0', $columns[20]->abbreviation());
        self::assertSame('F14', $columns[21]->abbreviation());
        self::assertSame('F26', $columns[22]->abbreviation());
        self::assertSame('F45', $columns[23]->abbreviation());
        self::assertSame('M0', $columns[24]->abbreviation());
        self::assertSame('M14', $columns[25]->abbreviation());
        self::assertSame('M26', $columns[26]->abbreviation());
        self::assertSame('M45', $columns[27]->abbreviation());
        self::assertSame('F0', $columns[28]->abbreviation());
        self::assertSame('F14', $columns[29]->abbreviation());
        self::assertSame('F26', $columns[30]->abbreviation());
        self::assertSame('F45', $columns[31]->abbreviation());

        self::assertSame('Name of head of family', $columns[0]->title());
        self::assertSame('Free white males 0-10 years', $columns[1]->title());
        self::assertSame('Free white males 10-16 years', $columns[2]->title());
        self::assertSame('Free white males 16-18 years', $columns[3]->title());
        self::assertSame('Free white males 16-26 years', $columns[4]->title());
        self::assertSame('Free white males 26-45 years', $columns[5]->title());
        self::assertSame('Free white males 45+ years', $columns[6]->title());
        self::assertSame('Free white females 0-10 years', $columns[7]->title());
        self::assertSame('Free white females 10-16 years', $columns[8]->title());
        self::assertSame('Free white females 16-26 years', $columns[9]->title());
        self::assertSame('Free white females 26-45 years', $columns[10]->title());
        self::assertSame('Free white females 45+ years', $columns[11]->title());
        self::assertSame('Foreigners not naturalized', $columns[12]->title());
        self::assertSame('No. engaged in agriculture', $columns[13]->title());
        self::assertSame('No. engaged in commerce', $columns[14]->title());
        self::assertSame('No. engaged in manufactures', $columns[15]->title());
        self::assertSame('Slave males 0-14 years', $columns[16]->title());
        self::assertSame('Slave males 14-26 years', $columns[17]->title());
        self::assertSame('Slave males 26-45 years', $columns[18]->title());
        self::assertSame('Slave males 45+ years', $columns[19]->title());
        self::assertSame('Slave females 0-14 years', $columns[20]->title());
        self::assertSame('Slave females 14-26 years', $columns[21]->title());
        self::assertSame('Slave females 26-45 years', $columns[22]->title());
        self::assertSame('Slave females 45+ years', $columns[23]->title());
        self::assertSame('Free colored males 0-14 years', $columns[24]->title());
        self::assertSame('Free colored males 14-26 years', $columns[25]->title());
        self::assertSame('Free colored males 26-45 years', $columns[26]->title());
        self::assertSame('Free colored males 45+ years', $columns[27]->title());
        self::assertSame('Free colored females 0-14 years', $columns[28]->title());
        self::assertSame('Free colored females 14-26 years', $columns[29]->title());
        self::assertSame('Free colored females 26-45 years', $columns[30]->title());
        self::assertSame('Free colored females 45+ years', $columns[31]->title());
    }
}
