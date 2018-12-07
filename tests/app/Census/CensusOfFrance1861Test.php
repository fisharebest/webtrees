<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
 * Test harness for the class CensusOfFrance1861
 */
class CensusOfFrance1861Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the census place and date
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfFrance1861
     */
    public function testPlaceAndDate()
    {
        $census = new CensusOfFrance1861();

        $this->assertSame('France', $census->censusPlace());
        $this->assertSame('17 JAN 1861', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers Fisharebest\Webtrees\Census\CensusOfFrance1861
     * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testColumns()
    {
        $census  = new CensusOfFrance1861();
        $columns = $census->columns();

        $this->assertCount(10, $columns);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnSurname', $columns[0]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnGivenNames', $columns[1]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnOccupation', $columns[2]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionFrenchGarcon', $columns[3]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionFrenchHomme', $columns[4]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuf', $columns[5]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille', $columns[6]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFemme', $columns[7]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve', $columns[8]);
        $this->assertInstanceOf('Fisharebest\Webtrees\Census\CensusColumnAge', $columns[9]);

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
