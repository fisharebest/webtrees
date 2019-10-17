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
 * Test harness for the class CensusOfFrance1881
 */
class CensusOfFrance1881Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1881
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfFrance1881();

        $this->assertSame('France', $census->censusPlace());
        $this->assertSame('20 JAN 1881', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1881
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfFrance1881();
        $columns = $census->columns();

        $this->assertCount(5, $columns);
        $this->assertInstanceOf(CensusColumnSurname::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnAge::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnRelationToHead::class, $columns[4]);

        $this->assertSame('Noms', $columns[0]->abbreviation());
        $this->assertSame('Prénoms', $columns[1]->abbreviation());
        $this->assertSame('Âge', $columns[2]->abbreviation());
        $this->assertSame('Profession', $columns[3]->abbreviation());
        $this->assertSame('Position', $columns[4]->abbreviation());

        $this->assertSame('Noms de famille', $columns[0]->title());
        $this->assertSame('', $columns[1]->title());
        $this->assertSame('', $columns[2]->title());
        $this->assertSame('', $columns[3]->title());
        $this->assertSame('Position dans le ménage', $columns[4]->title());
    }
}
