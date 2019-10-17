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
 * Test harness for the class CensusOfDenmark1870
 */
class CensusOfDenmark1870Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1870
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1870();

        $this->assertSame('Danmark', $census->censusPlace());
        $this->assertSame('01 FEB 1870', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDenmark1870
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1870();
        $columns = $census->columns();

        $this->assertCount(14, $columns);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnSexMK::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnConditionDanish::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnReligion::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnRelationToHead::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[13]);

        $this->assertSame('Navn', $columns[0]->abbreviation());
        $this->assertSame('Køn', $columns[1]->abbreviation());
        $this->assertSame('Alder', $columns[2]->abbreviation());
        $this->assertSame('Civilstand', $columns[3]->abbreviation());
        $this->assertSame('Religion', $columns[4]->abbreviation());
        $this->assertSame('Erhverv', $columns[5]->abbreviation());
        $this->assertSame('Stilling i familien', $columns[6]->abbreviation());
        $this->assertSame('', $columns[7]->abbreviation());
        $this->assertSame('', $columns[8]->abbreviation());
        $this->assertSame('', $columns[9]->abbreviation());
        $this->assertSame('', $columns[10]->abbreviation());
        $this->assertSame('', $columns[11]->abbreviation());
        $this->assertSame('', $columns[12]->abbreviation());
        $this->assertSame('', $columns[13]->abbreviation());

        $this->assertSame('', $columns[0]->title());
        $this->assertSame('', $columns[1]->title());
        $this->assertSame('', $columns[2]->title());
        $this->assertSame('', $columns[3]->title());
        $this->assertSame('', $columns[4]->title());
        $this->assertSame('', $columns[5]->title());
        $this->assertSame('', $columns[6]->title());
        $this->assertSame('', $columns[7]->title());
        $this->assertSame('', $columns[8]->title());
        $this->assertSame('', $columns[9]->title());
        $this->assertSame('', $columns[10]->title());
        $this->assertSame('', $columns[11]->title());
        $this->assertSame('', $columns[12]->title());
        $this->assertSame('', $columns[13]->title());
    }
}
