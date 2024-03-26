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

#[CoversClass(CensusOfCanadaPraries1916::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfCanadaPraries1916Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanadaPraries1916();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('01 JUN 1916', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCanadaPraries1916();
        $columns = $census->columns();

        self::assertCount(19, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[2]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[3]);
        self::assertInstanceOf(CensusColumnConditionCanada::class, $columns[4]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Mil', $columns[1]->abbreviation());
        self::assertSame('Relation', $columns[2]->abbreviation());
        self::assertSame('Sex', $columns[3]->abbreviation());
        self::assertSame('S/M/W/D/L', $columns[4]->abbreviation());
        self::assertSame('Age', $columns[5]->abbreviation());
        self::assertSame('Birth Loc', $columns[6]->abbreviation());
        self::assertSame('Religion', $columns[7]->abbreviation());
        self::assertSame('Yr. immigrated', $columns[8]->abbreviation());
        self::assertSame('Nationality', $columns[9]->abbreviation());
        self::assertSame('Origin', $columns[10]->abbreviation());
        self::assertSame('English', $columns[11]->abbreviation());
        self::assertSame('French', $columns[12]->abbreviation());
        self::assertSame('Language', $columns[13]->abbreviation());
        self::assertSame('Read', $columns[14]->abbreviation());
        self::assertSame('Write', $columns[15]->abbreviation());
        self::assertSame('Occupation', $columns[16]->abbreviation());
        self::assertSame('E/W/OA', $columns[17]->abbreviation());
        self::assertSame('Where employed', $columns[18]->abbreviation());

        self::assertSame('Name of each person in family, household or institution', $columns[0]->title());
        self::assertSame('Military Service', $columns[1]->title());
        self::assertSame('Relationship to Head of Family or household', $columns[2]->title());
        self::assertSame('Sex', $columns[3]->title());
        self::assertSame('Single, Married, Widowed, Divorced or Legally Separated', $columns[4]->title());
        self::assertSame('Age at last birthday - on June 1, 1916', $columns[5]->title());
        self::assertSame('Country or place of birth', $columns[6]->title());
        self::assertSame('Religion', $columns[7]->title());
        self::assertSame('Year of immigration to Canada', $columns[8]->title());
        self::assertSame('Nationality', $columns[9]->title());
        self::assertSame('Racial or tribal origin', $columns[10]->title());
        self::assertSame('Can speak English', $columns[11]->title());
        self::assertSame('Can speak French', $columns[12]->title());
        self::assertSame('Mother tongue', $columns[13]->title());
        self::assertSame('Can read', $columns[14]->title());
        self::assertSame('Can write', $columns[15]->title());
        self::assertSame('Chief occupation or trade', $columns[16]->title());
        self::assertSame('Employer "e", Employee or worker "w", Working on own account "o.a."', $columns[17]->title());
        self::assertSame('State where person was employed as "on farm", "in cotton mill", "in foundry", "in dry goods store", "in saw-mill", etc.', $columns[18]->title());
    }
}
