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

#[CoversClass(CensusOfCanadaPraries1926::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfCanadaPraries1926Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfCanadaPraries1926();

        self::assertSame('Canada', $census->censusPlace());
        self::assertSame('01 JUN 1926', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfCanadaPraries1926();
        $columns = $census->columns();

        self::assertCount(18, $columns);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[0]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSexMF::class, $columns[2]);
        self::assertInstanceOf(CensusColumnConditionCanada::class, $columns[3]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[5]);
        self::assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[6]);
        self::assertInstanceOf(CensusColumnMotherBirthPlaceSimple::class, $columns[7]);
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

        self::assertSame('Name', $columns[0]->abbreviation());
        self::assertSame('Relation', $columns[1]->abbreviation());
        self::assertSame('Sex', $columns[2]->abbreviation());
        self::assertSame('S/M/W/D/L', $columns[3]->abbreviation());
        self::assertSame('Age', $columns[4]->abbreviation());
        self::assertSame('Birth Loc', $columns[5]->abbreviation());
        self::assertSame('FBP', $columns[6]->abbreviation());
        self::assertSame('MBP', $columns[7]->abbreviation());
        self::assertSame('Origin', $columns[8]->abbreviation());
        self::assertSame('Yr. immigrated', $columns[9]->abbreviation());
        self::assertSame('Yr. naturalized', $columns[10]->abbreviation());
        self::assertSame('Ctznshp', $columns[11]->abbreviation());
        self::assertSame('English', $columns[12]->abbreviation());
        self::assertSame('French', $columns[13]->abbreviation());
        self::assertSame('Other language', $columns[14]->abbreviation());
        self::assertSame('Read', $columns[15]->abbreviation());
        self::assertSame('Write', $columns[16]->abbreviation());
        self::assertSame('Ms school', $columns[17]->abbreviation());

        self::assertSame('Name of each person in family, household or institution', $columns[0]->title());
        self::assertSame('Relationship to Head of Family or household', $columns[1]->title());
        self::assertSame('Sex', $columns[2]->title());
        self::assertSame('Single, Married, Widowed, Divorced or Legally Separated', $columns[3]->title());
        self::assertSame('Age at last birthday - on June 1, 1926', $columns[4]->title());
        self::assertSame('Place of birth of person', $columns[5]->title());
        self::assertSame('Place of birth of father', $columns[6]->title());
        self::assertSame('Place of birth of mother', $columns[7]->title());
        self::assertSame('Racial or tribal origin', $columns[8]->title());
        self::assertSame('Year of immigration to Canada', $columns[9]->title());
        self::assertSame('Year of naturalization', $columns[10]->title());
        self::assertSame('Citizenship', $columns[11]->title());
        self::assertSame('Can speak English', $columns[12]->title());
        self::assertSame('Can speak French', $columns[13]->title());
        self::assertSame('Mother tongue', $columns[14]->title());
        self::assertSame('Can read', $columns[15]->title());
        self::assertSame('Can write', $columns[16]->title());
        self::assertSame('Months at school since Sept. 1, 1925', $columns[17]->title());
    }
}
