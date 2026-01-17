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

#[CoversClass(CensusOfFrance1901::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfFrance1901Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfFrance1901();

        self::assertSame('France', $census->censusPlace());
        self::assertSame('17 JAN 1901', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfFrance1901();
        $columns = $census->columns();

        self::assertCount(8, $columns);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[0]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNationality::class, $columns[3]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[4]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);

        self::assertSame('Noms', $columns[0]->abbreviation());
        self::assertSame('Prénoms', $columns[1]->abbreviation());
        self::assertSame('Âge', $columns[2]->abbreviation());
        self::assertSame('Nationalité', $columns[3]->abbreviation());
        self::assertSame('Situation', $columns[4]->abbreviation());
        self::assertSame('Profession', $columns[5]->abbreviation());
        self::assertSame('Lieu', $columns[6]->abbreviation());
        self::assertSame('Empl', $columns[7]->abbreviation());

        self::assertSame('Noms de famille', $columns[0]->title());
        self::assertSame('', $columns[1]->title());
        self::assertSame('', $columns[2]->title());
        self::assertSame('', $columns[3]->title());
        self::assertSame('Situation par rapport au chef de ménage', $columns[4]->title());
        self::assertSame('', $columns[5]->title());
        self::assertSame('Lieu de naissance', $columns[6]->title());
        self::assertSame('', $columns[7]->title());
    }
}
