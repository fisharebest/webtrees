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

#[CoversClass(CensusOfFrance1876::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfFrance1876Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfFrance1876();

        self::assertSame('France', $census->censusPlace());
        self::assertSame('20 JAN 1876', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfFrance1876();
        $columns = $census->columns();

        self::assertCount(11, $columns);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[0]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[2]);
        self::assertInstanceOf(CensusColumnConditionFrenchGarcon::class, $columns[3]);
        self::assertInstanceOf(CensusColumnConditionFrenchHomme::class, $columns[4]);
        self::assertInstanceOf(CensusColumnConditionFrenchVeuf::class, $columns[5]);
        self::assertInstanceOf(CensusColumnConditionFrenchFille::class, $columns[6]);
        self::assertInstanceOf(CensusColumnConditionFrenchFemme::class, $columns[7]);
        self::assertInstanceOf(CensusColumnConditionFrenchVeuve::class, $columns[8]);
        self::assertInstanceOf(CensusColumnAge::class, $columns[9]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[10]);

        self::assertSame('Noms', $columns[0]->abbreviation());
        self::assertSame('Prénoms', $columns[1]->abbreviation());
        self::assertSame('Titres', $columns[2]->abbreviation());
        self::assertSame('Garçons', $columns[3]->abbreviation());
        self::assertSame('Hommes', $columns[4]->abbreviation());
        self::assertSame('Veufs', $columns[5]->abbreviation());
        self::assertSame('Filles', $columns[6]->abbreviation());
        self::assertSame('Femmes', $columns[7]->abbreviation());
        self::assertSame('Veuves', $columns[8]->abbreviation());
        self::assertSame('Âge', $columns[9]->abbreviation());
        self::assertSame('Nationalité', $columns[10]->abbreviation());

        self::assertSame('Noms de famille', $columns[0]->title());
        self::assertSame('', $columns[1]->title());
        self::assertSame('Titres, qualifications, état ou profession et fonctions', $columns[2]->title());
        self::assertSame('', $columns[3]->title());
        self::assertSame('Hommes mariés', $columns[4]->title());
        self::assertSame('', $columns[5]->title());
        self::assertSame('', $columns[6]->title());
        self::assertSame('Femmes mariées', $columns[7]->title());
        self::assertSame('', $columns[8]->title());
        self::assertSame('', $columns[9]->title());
        self::assertSame('Nationalité - Lieu de naissance', $columns[10]->title());
    }
}
