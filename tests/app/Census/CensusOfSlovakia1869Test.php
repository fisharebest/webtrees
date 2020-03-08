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
 * Test harness for the class CensusOfSlovakia1869
 */
class CensusOfSlovakia1869Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfSlovakia1869
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfSlovakia1869();

        $this->assertSame('Slovensko', $census->censusPlace());
        $this->assertSame('31 DEC 1869', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfSlovakia1869
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfSlovakia1869();
        $columns = $census->columns();

        $this->assertCount(20, $columns);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnRelationToHead::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnSexMZ::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnBirthYear::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnReligion::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[13]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[14]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[16]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[17]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[18]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[19]);

        $this->assertSame('Č. b.', $columns[0]->abbreviation());
        $this->assertSame('Č. os.', $columns[1]->abbreviation());
        $this->assertSame('Meno', $columns[2]->abbreviation());
        $this->assertSame('Vzťah', $columns[3]->abbreviation());
        $this->assertSame('Poh.', $columns[4]->abbreviation());
        $this->assertSame('Nar.', $columns[5]->abbreviation());
        $this->assertSame('Náb.', $columns[6]->abbreviation());
        $this->assertSame('Stav', $columns[7]->abbreviation());
        $this->assertSame('Povolanie', $columns[8]->abbreviation());
        $this->assertSame('Zamestnanie', $columns[9]->abbreviation());
        $this->assertSame('Rodisko', $columns[10]->abbreviation());
        $this->assertSame('Dom.', $columns[11]->abbreviation());
        $this->assertSame('Cudz.', $columns[12]->abbreviation());
        $this->assertSame('P. doč.', $columns[13]->abbreviation());
        $this->assertSame('P. trv.', $columns[14]->abbreviation());
        $this->assertSame('Vz. doč.', $columns[15]->abbreviation());
        $this->assertSame('Vz. dlho.', $columns[16]->abbreviation());
        $this->assertSame('Čít.', $columns[17]->abbreviation());
        $this->assertSame('Pís.', $columns[18]->abbreviation());
        $this->assertSame('Poz.', $columns[19]->abbreviation());

        $this->assertSame('Poradové číslo bytu', $columns[0]->title());
        $this->assertSame('Poradové číslo osoby', $columns[1]->title());
        $this->assertSame('Priezvisko a krstné meno, titul', $columns[2]->title());
        $this->assertSame('Postavenie (rodinný vzťah k hlave domácnosti)', $columns[3]->title());
        $this->assertSame('Pohlavie', $columns[4]->title());
        $this->assertSame('Rok narodenia', $columns[5]->title());
        $this->assertSame('Náboženstvo', $columns[6]->title());
        $this->assertSame('Rodinský stav', $columns[7]->title());
        $this->assertSame('Povolanie', $columns[8]->title());
        $this->assertSame('Okolnosti zamestnania', $columns[9]->title());
        $this->assertSame('Rodisko - štát/krajina, stolica/okres/sídlo/vidiek, mesto/obec', $columns[10]->title());
        $this->assertSame('Príslušnosť k obci - zdejší', $columns[11]->title());
        $this->assertSame('Príslušnosť k obci - cudzí', $columns[12]->title());
        $this->assertSame('Prítomný dočasne - do jedného mesiaca', $columns[13]->title());
        $this->assertSame('Prítomný trvalo', $columns[14]->title());
        $this->assertSame('Vzdialený dočasne - do jedného mesiaca', $columns[15]->title());
        $this->assertSame('Vzdialený dlhodobo - nad jeden mesiac', $columns[16]->title());
        $this->assertSame('Osoba vie čítať', $columns[17]->title());
        $this->assertSame('Osoba vie čítať a písať', $columns[18]->title());
        $this->assertSame('Poznámka', $columns[19]->title());
    }
}
