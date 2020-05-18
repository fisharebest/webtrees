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
 * Test harness for the class CensusOfUnitedStates1950
 */

class CensusOfUnitedStates1950Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1950
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1950();

        $this->assertSame('United States', $census->censusPlace());
        $this->assertSame('APR 1950', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfUnitedStates1950
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1950();
        $columns = $census->columns();

        $this->assertCount(21, $columns);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnSexMF::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnConditionUs::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[13]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[14]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[16]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[17]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[18]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[19]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[20]);

        $this->assertSame('Street', $columns[0]->abbreviation());
        $this->assertSame('Number', $columns[1]->abbreviation());
        $this->assertSame('Serial', $columns[2]->abbreviation());
        $this->assertSame('Farm', $columns[3]->abbreviation());
        $this->assertSame('Acres', $columns[4]->abbreviation());
        $this->assertSame('Name', $columns[5]->abbreviation());
        $this->assertSame('Relation', $columns[6]->abbreviation());
        $this->assertSame('Race', $columns[7]->abbreviation());
        $this->assertSame('Sex', $columns[8]->abbreviation());
        $this->assertSame('Age', $columns[9]->abbreviation());
        $this->assertSame('Cond', $columns[10]->abbreviation());
        $this->assertSame('BP', $columns[11]->abbreviation());
        $this->assertSame('Nat', $columns[12]->abbreviation());
        $this->assertSame('Type', $columns[13]->abbreviation());
        $this->assertSame('AnyWork', $columns[14]->abbreviation());
        $this->assertSame('Seeking', $columns[15]->abbreviation());
        $this->assertSame('Employed', $columns[16]->abbreviation());
        $this->assertSame('Hours', $columns[17]->abbreviation());
        $this->assertSame('Occupation', $columns[18]->abbreviation());
        $this->assertSame('Industry', $columns[19]->abbreviation());
        $this->assertSame('Class', $columns[20]->abbreviation());

        $this->assertSame('Name of street,avenue,or road', $columns[0]->title());
        $this->assertSame('House (and apartment) number', $columns[1]->title());
        $this->assertSame('Serial number of dwelling unit', $columns[2]->title());
        $this->assertSame('Is this house on a farm (or ranch)?', $columns[3]->title());
        $this->assertSame('If No in item 4-Is this house on a place of three or more acres?', $columns[4]->title());
        $this->assertSame('What is the name of the had of this household? What are the names of all other personsl who live here?', $columns[5]->title());
        $this->assertSame('Enter relationship of person to head of the household', $columns[6]->title());
        $this->assertSame('White(W) Negro(Neg) American Indian(Ind) Japanese(Jap) Chinese(Chi) Filipino(Fil) Other race-spell out', $columns[7]->title());
        $this->assertSame('Sex-Male (M),Female (F)', $columns[8]->title());
        $this->assertSame('How old was he on his last birthday?', $columns[9]->title());
        $this->assertSame('Is he now married,widowed,divorced,separated,or never married?', $columns[10]->title());
        $this->assertSame('What State (or foreign country) was he born in?', $columns[11]->title());
        $this->assertSame('If foreign born-Is he naturalized?', $columns[12]->title());
        $this->assertSame('What was this person doing most of last week-working,keeping house,or something else?', $columns[13]->title());
        $this->assertSame('If H or Ot in item 15-Did this person do any work at all last week,not counting work around the house?', $columns[14]->title());
        $this->assertSame('If No in item 16-Was this person looking for work?', $columns[15]->title());
        $this->assertSame('If No in item 17-Even though he didn’t work last week,does he have a job or business?', $columns[16]->title());
        $this->assertSame('If Wk in item 15 or Yes in item 16-How many hours did he work last week?', $columns[17]->title());
        $this->assertSame('What kind of work was he doing?', $columns[18]->title());
        $this->assertSame('What kind of business or industry was he working in?', $columns[19]->title());
        $this->assertSame('Class of worker', $columns[20]->title());
    }
}
