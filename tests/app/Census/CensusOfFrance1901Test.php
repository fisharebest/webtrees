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
namespace Fisharebest\Webtrees\Census;

/**
 * Test harness for the class CensusOfFrance1901
 */
class CensusOfFrance1901Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfFrance1901
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfFrance1901();

        $this->assertSame('France', $census->censusPlace());
        $this->assertSame('17 JAN 1901', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfFrance1901
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfFrance1901();
        $columns = $census->columns();

        $this->assertCount(8, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurname', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnGivenNames', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNationality', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnRelationToHead', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnBirthPlace', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnNull', $columns[7]);

        $this->assertSame('Noms', $columns[0]->abbreviation());
        $this->assertSame('Prénoms', $columns[1]->abbreviation());
        $this->assertSame('Âge', $columns[2]->abbreviation());
        $this->assertSame('Nationalité', $columns[3]->abbreviation());
        $this->assertSame('Situation', $columns[4]->abbreviation());
        $this->assertSame('Profession', $columns[5]->abbreviation());
        $this->assertSame('Lieu', $columns[6]->abbreviation());
        $this->assertSame('Empl', $columns[7]->abbreviation());

        $this->assertSame('Noms de famille', $columns[0]->title());
        $this->assertSame('', $columns[1]->title());
        $this->assertSame('', $columns[2]->title());
        $this->assertSame('', $columns[3]->title());
        $this->assertSame('Situation par rapport au chef de ménage', $columns[4]->title());
        $this->assertSame('', $columns[5]->title());
        $this->assertSame('Lieu de naissance', $columns[6]->title());
        $this->assertSame('', $columns[7]->title());
    }
}
