<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
 * Test harness for the class CensusOfUnitedStates1810
 */
class CensusOfUnitedStates1810Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1810
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1810();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('06 AUG 1810', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1810
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1810();
        $columns = $census->columns();

        self::assertCount(14, $columns);
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

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('M0-10', $columns[1]->abbreviation());
        self::assertSame('M10-16', $columns[2]->abbreviation());
        self::assertSame('M16-26', $columns[3]->abbreviation());
        self::assertSame('M26-45', $columns[4]->abbreviation());
        self::assertSame('M45+', $columns[5]->abbreviation());
        self::assertSame('F0-10', $columns[6]->abbreviation());
        self::assertSame('F10-16', $columns[7]->abbreviation());
        self::assertSame('F16-26', $columns[8]->abbreviation());
        self::assertSame('F26-45', $columns[9]->abbreviation());
        self::assertSame('F45+', $columns[10]->abbreviation());
        self::assertSame('Free', $columns[11]->abbreviation());
        self::assertSame('Slaves', $columns[12]->abbreviation());
        self::assertSame('Total', $columns[13]->abbreviation());

        self::assertSame('Name of head of family', $columns[0]->title());
        self::assertSame('Free white males 0-10 years', $columns[1]->title());
        self::assertSame('Free white males 10-16 years', $columns[2]->title());
        self::assertSame('Free white males 16-26 years', $columns[3]->title());
        self::assertSame('Free white males 26-45 years', $columns[4]->title());
        self::assertSame('Free white males 45+ years', $columns[5]->title());
        self::assertSame('Free white females 0-10 years', $columns[6]->title());
        self::assertSame('Free white females 10-16 years', $columns[7]->title());
        self::assertSame('Free white females 16-26 years', $columns[8]->title());
        self::assertSame('Free white females 26-45 years', $columns[9]->title());
        self::assertSame('Free white females 45+ years', $columns[10]->title());
        self::assertSame('All other free persons, except Indians not taxed', $columns[11]->title());
        self::assertSame('Number of slaves', $columns[12]->title());
        self::assertSame('Total number of individuals', $columns[13]->title());
    }
}
