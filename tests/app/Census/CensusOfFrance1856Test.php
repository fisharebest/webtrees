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
 * Test harness for the class CensusOfFrance1856
 */
class CensusOfFrance1856Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1856
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfFrance1856();

        $this->assertSame('France', $census->censusPlace());
        $this->assertSame('17 JAN 1856', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1856
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfFrance1856();
        $columns = $census->columns();

        $this->assertCount(10, $columns);
        $this->assertInstanceOf(CensusColumnSurname::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnConditionFrenchGarcon::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnConditionFrenchHomme::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnConditionFrenchVeuf::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnConditionFrenchFille::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnConditionFrenchFemme::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnConditionFrenchVeuve::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[9]);

        $this->assertSame('Noms', $columns[0]->abbreviation());
        $this->assertSame('Prénoms', $columns[1]->abbreviation());
        $this->assertSame('Titres', $columns[2]->abbreviation());
        $this->assertSame('Garçons', $columns[3]->abbreviation());
        $this->assertSame('Hommes', $columns[4]->abbreviation());
        $this->assertSame('Veufs', $columns[5]->abbreviation());
        $this->assertSame('Filles', $columns[6]->abbreviation());
        $this->assertSame('Femmes', $columns[7]->abbreviation());
        $this->assertSame('Veuves', $columns[8]->abbreviation());
        $this->assertSame('Âge', $columns[9]->abbreviation());

        $this->assertSame('Noms de famille', $columns[0]->title());
        $this->assertSame('', $columns[1]->title());
        $this->assertSame('Titres, qualifications, état ou profession et fonctions', $columns[2]->title());
        $this->assertSame('', $columns[3]->title());
        $this->assertSame('Hommes mariés', $columns[4]->title());
        $this->assertSame('', $columns[5]->title());
        $this->assertSame('', $columns[6]->title());
        $this->assertSame('Femmes mariées', $columns[7]->title());
        $this->assertSame('', $columns[8]->title());
        $this->assertSame('', $columns[9]->title());
    }
}
