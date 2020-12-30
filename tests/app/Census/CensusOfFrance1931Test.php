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
 * Test harness for the class CensusOfFrance1931
 */
class CensusOfFrance1931Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1931
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfFrance1931();

        self::assertSame('France', $census->censusPlace());
        self::assertSame('15 JAN 1931', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1931
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfFrance1931();
        $columns = $census->columns();

        self::assertCount(8, $columns);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[0]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[2]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[4]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[5]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);

        self::assertSame('Noms', $columns[0]->abbreviation());
        self::assertSame('Prénoms', $columns[1]->abbreviation());
        self::assertSame('Année', $columns[2]->abbreviation());
        self::assertSame('Lieu', $columns[3]->abbreviation());
        self::assertSame('Nationalité', $columns[4]->abbreviation());
        self::assertSame('Situation', $columns[5]->abbreviation());
        self::assertSame('Profession', $columns[6]->abbreviation());
        self::assertSame('Empl', $columns[7]->abbreviation());

        self::assertSame('Noms de famille', $columns[0]->title());
        self::assertSame('', $columns[1]->title());
        self::assertSame('Année de naissance', $columns[2]->title());
        self::assertSame('Lieu de naissance', $columns[3]->title());
        self::assertSame('', $columns[4]->title());
        self::assertSame('Situation par rapport au chef de ménage', $columns[5]->title());
        self::assertSame('', $columns[6]->title());
        self::assertSame('', $columns[7]->title());
    }
}
