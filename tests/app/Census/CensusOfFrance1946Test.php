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


#[CoversClass(CensusOfFrance1946::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfFrance1946Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfFrance1946();

        self::assertSame('France', $census->censusPlace());
        self::assertSame('17 JAN 1946', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfFrance1946();
        $columns = $census->columns();

        self::assertCount(6, $columns);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[0]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[2]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[4]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[5]);

        self::assertSame('Nom', $columns[0]->abbreviation());
        self::assertSame('Prénom', $columns[1]->abbreviation());
        self::assertSame('Parenté', $columns[2]->abbreviation());
        self::assertSame('Année', $columns[3]->abbreviation());
        self::assertSame('Nationalité', $columns[4]->abbreviation());
        self::assertSame('Profession', $columns[5]->abbreviation());

        self::assertSame('Nom de famille', $columns[0]->title());
        self::assertSame('Prénom usuel', $columns[1]->title());
        self::assertSame('Parenté avec le chef de ménage ou situation dans le ménage', $columns[2]->title());
        self::assertSame('Année de naissance', $columns[3]->title());
        self::assertSame('', $columns[4]->title());
        self::assertSame('', $columns[5]->title());
    }
}
