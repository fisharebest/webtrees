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

#[CoversClass(CensusOfScotland1911::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfScotland1911Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfScotland1911();

        self::assertSame('Scotland', $census->censusPlace());
        self::assertSame('02 APR 1911', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfScotland1911();
        $columns = $census->columns();

        self::assertCount(18, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHeadEnglish::class, $columns[3]);
        self::assertInstanceOf(CensusColumnAgeMale::class, $columns[4]);
        self::assertInstanceOf(CensusColumnAgeFemale::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnConditionEnglish::class, $columns[7]);
        self::assertInstanceOf(CensusColumnYearsMarried::class, $columns[8]);
        self::assertInstanceOf(CensusColumnChildrenBornAlive::class, $columns[9]);
        self::assertInstanceOf(CensusColumnChildrenLiving::class, $columns[10]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);

        self::assertSame('Rooms', $columns[0]->abbreviation());
        self::assertSame('Name', $columns[1]->abbreviation());
        self::assertSame('NoPers', $columns[2]->abbreviation());
        self::assertSame('Relation', $columns[3]->abbreviation());
        self::assertSame('AgeM', $columns[4]->abbreviation());
        self::assertSame('AgeF', $columns[5]->abbreviation());
        self::assertSame('Lang', $columns[6]->abbreviation());
        self::assertSame('Condition', $columns[7]->abbreviation());
        self::assertSame('YrM', $columns[8]->abbreviation());
        self::assertSame('ChA', $columns[9]->abbreviation());
        self::assertSame('ChL', $columns[10]->abbreviation());
        self::assertSame('Occupation', $columns[11]->abbreviation());
        self::assertSame('Ind', $columns[12]->abbreviation());
        self::assertSame('Emp', $columns[13]->abbreviation());
        self::assertSame('Home', $columns[14]->abbreviation());
        self::assertSame('Birthplace', $columns[15]->abbreviation());
        self::assertSame('Nat', $columns[16]->abbreviation());
        self::assertSame('Infirm', $columns[17]->abbreviation());

        self::assertSame('Rooms with one or more windows', $columns[0]->title());
        self::assertSame('Name and surname', $columns[1]->title());
        self::assertSame('Number of persons in the house', $columns[2]->title());
        self::assertSame('Relation to head of household', $columns[3]->title());
        self::assertSame('Age (males)', $columns[4]->title());
        self::assertSame('Age (females)', $columns[5]->title());
        self::assertSame('Gaelic or G & E', $columns[6]->title());
        self::assertSame('Condition', $columns[7]->title());
        self::assertSame('Years married', $columns[8]->title());
        self::assertSame('Children born alive', $columns[9]->title());
        self::assertSame('Children who are still alive', $columns[10]->title());
        self::assertSame('Rank, profession or occupation', $columns[11]->title());
        self::assertSame('Industry', $columns[12]->title());
        self::assertSame('Employer, worker or own account', $columns[13]->title());
        self::assertSame('Working at home', $columns[14]->title());
        self::assertSame('Where born', $columns[15]->title());
        self::assertSame('Nationality', $columns[16]->title());
        self::assertSame('Infirmity', $columns[17]->title());
    }
}
