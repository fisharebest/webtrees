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

/**
 * Test harness for the class CensusOfFrance1851
 */
class CensusOfFrance1851Test extends \Fisharebest\Webtrees\TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1851
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfFrance1851();

        $this->assertSame('France', $census->censusPlace());
        $this->assertSame('16 JAN 1851', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfFrance1851
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfFrance1851();
        $columns = $census->columns();

        $this->assertCount(13, $columns);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnSurname::class, $columns[0]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnGivenNames::class, $columns[1]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnOccupation::class, $columns[2]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnConditionFrenchGarcon::class, $columns[3]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnConditionFrenchHomme::class, $columns[4]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuf::class, $columns[5]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille::class, $columns[6]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFemme::class, $columns[7]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve::class, $columns[8]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnAge::class, $columns[9]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnNull::class, $columns[10]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(\Fisharebest\Webtrees\Census\CensusColumnNull::class, $columns[12]);

        $this->assertSame('Noms', $columns[0]->abbreviation());
        $this->assertSame('Prénoms', $columns[1]->abbreviation());
        $this->assertSame('Professions', $columns[2]->abbreviation());
        $this->assertSame('Garçons', $columns[3]->abbreviation());
        $this->assertSame('Hommes', $columns[4]->abbreviation());
        $this->assertSame('Veufs', $columns[5]->abbreviation());
        $this->assertSame('Filles', $columns[6]->abbreviation());
        $this->assertSame('Femmes', $columns[7]->abbreviation());
        $this->assertSame('Veuves', $columns[8]->abbreviation());
        $this->assertSame('Âge', $columns[9]->abbreviation());
        $this->assertSame('Fr', $columns[10]->abbreviation());
        $this->assertSame('Nat', $columns[11]->abbreviation());
        $this->assertSame('Etr', $columns[12]->abbreviation());

        $this->assertSame('Noms de famille', $columns[0]->title());
        $this->assertSame('', $columns[1]->title());
        $this->assertSame('', $columns[2]->title());
        $this->assertSame('', $columns[3]->title());
        $this->assertSame('Hommes mariés', $columns[4]->title());
        $this->assertSame('', $columns[5]->title());
        $this->assertSame('', $columns[6]->title());
        $this->assertSame('Femmes mariées', $columns[7]->title());
        $this->assertSame('', $columns[8]->title());
        $this->assertSame('Français d’origine', $columns[10]->title());
        $this->assertSame('Naturalisés français', $columns[11]->title());
        $this->assertSame('Étrangers (indiquer leur pays d’origine)', $columns[12]->title());
    }
}
