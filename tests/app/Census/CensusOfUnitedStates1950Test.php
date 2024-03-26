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
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusOfUnitedStates1950::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfUnitedStates1950Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfUnitedStates1950();

        self::assertSame('United States', $census->censusPlace());
        self::assertSame('APR 1950', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfUnitedStates1950();
        $columns = $census->columns();

        self::assertCount(21, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[5]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[8]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[9]);
        self::assertInstanceOf(CensusColumnConditionUs::class, $columns[10]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);

        self::assertSame('Street', $columns[0]->abbreviation());
        self::assertSame('Number', $columns[1]->abbreviation());
        self::assertSame('Serial', $columns[2]->abbreviation());
        self::assertSame('Farm', $columns[3]->abbreviation());
        self::assertSame('Acres', $columns[4]->abbreviation());
        self::assertSame('Name', $columns[5]->abbreviation());
        self::assertSame('Relation', $columns[6]->abbreviation());
        self::assertSame('Race', $columns[7]->abbreviation());
        self::assertSame('Sex', $columns[8]->abbreviation());
        self::assertSame('Age', $columns[9]->abbreviation());
        self::assertSame('Cond', $columns[10]->abbreviation());
        self::assertSame('BP', $columns[11]->abbreviation());
        self::assertSame('Nat', $columns[12]->abbreviation());
        self::assertSame('Type', $columns[13]->abbreviation());
        self::assertSame('AnyWork', $columns[14]->abbreviation());
        self::assertSame('Seeking', $columns[15]->abbreviation());
        self::assertSame('Employed', $columns[16]->abbreviation());
        self::assertSame('Hours', $columns[17]->abbreviation());
        self::assertSame('Occupation', $columns[18]->abbreviation());
        self::assertSame('Industry', $columns[19]->abbreviation());
        self::assertSame('Class', $columns[20]->abbreviation());

        self::assertSame('Name of street,avenue,or road', $columns[0]->title());
        self::assertSame('House (and apartment) number', $columns[1]->title());
        self::assertSame('Serial number of dwelling unit', $columns[2]->title());
        self::assertSame('Is this house on a farm (or ranch)?', $columns[3]->title());
        self::assertSame('If No in item 4-Is this house on a place of three or more acres?', $columns[4]->title());
        self::assertSame('What is the name of the had of this household? What are the names of all other personsl who live here?', $columns[5]->title());
        self::assertSame('Enter relationship of person to head of the household', $columns[6]->title());
        self::assertSame('White(W) Negro(Neg) American Indian(Ind) Japanese(Jap) Chinese(Chi) Filipino(Fil) Other race-spell out', $columns[7]->title());
        self::assertSame('Sex-Male (M),Female (F)', $columns[8]->title());
        self::assertSame('How old was he on his last birthday?', $columns[9]->title());
        self::assertSame('Is he now married,widowed,divorced,separated,or never married?', $columns[10]->title());
        self::assertSame('What State (or foreign country) was he born in?', $columns[11]->title());
        self::assertSame('If foreign born-Is he naturalized?', $columns[12]->title());
        self::assertSame('What was this person doing most of last week-working,keeping house,or something else?', $columns[13]->title());
        self::assertSame('If H or Ot in item 15-Did this person do any work at all last week,not counting work around the house?', $columns[14]->title());
        self::assertSame('If No in item 16-Was this person looking for work?', $columns[15]->title());
        self::assertSame('If No in item 17-Even though he didn’t work last week,does he have a job or business?', $columns[16]->title());
        self::assertSame('If Wk in item 15 or Yes in item 16-How many hours did he work last week?', $columns[17]->title());
        self::assertSame('What kind of work was he doing?', $columns[18]->title());
        self::assertSame('What kind of business or industry was he working in?', $columns[19]->title());
        self::assertSame('Class of worker', $columns[20]->title());
    }
}
