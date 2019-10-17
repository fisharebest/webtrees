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
 * Test harness for the class CensusOfDeutschland1819
 */
class CensusOfDeutschland1819Test extends TestCase
{
    /**
     * Test the census place and date
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDeutschland1819
     *
     * @return void
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDeutschland1819();

        $this->assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        $this->assertSame('AUG 1819', $census->censusDate());
    }

    /**
     * Test the census columns
     *
     * @covers \Fisharebest\Webtrees\Census\CensusOfDeutschland1819
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     *
     * @return void
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDeutschland1819();
        $columns = $census->columns();

        $this->assertCount(13, $columns);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[0]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[1]);
        $this->assertInstanceOf(CensusColumnFullName::class, $columns[2]);
        $this->assertInstanceOf(CensusColumnBirthYear::class, $columns[3]);
        $this->assertInstanceOf(CensusColumnBirthPlace::class, $columns[4]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[5]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[6]);
        $this->assertInstanceOf(CensusColumnOccupation::class, $columns[7]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[8]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[10]);
        $this->assertInstanceOf(CensusColumnReligion::class, $columns[11]);
        $this->assertInstanceOf(CensusColumnNull::class, $columns[12]);

        $this->assertSame('Nr.', $columns[0]->abbreviation());
        $this->assertSame('Geschlecht', $columns[1]->abbreviation());
        $this->assertSame('Name', $columns[2]->abbreviation());
        $this->assertSame('Geburtsdatum', $columns[3]->abbreviation());
        $this->assertSame('Geburtsort', $columns[4]->abbreviation());
        $this->assertSame('Kirchspiel', $columns[5]->abbreviation());
        $this->assertSame('', $columns[6]->abbreviation());
        $this->assertSame('Stand/Beruf', $columns[7]->abbreviation());
        $this->assertSame('Besitz', $columns[8]->abbreviation());
        $this->assertSame('hier seit', $columns[9]->abbreviation());
        $this->assertSame('Familienstand', $columns[10]->abbreviation());
        $this->assertSame('Religion', $columns[11]->abbreviation());
        $this->assertSame('Bemerkungen', $columns[12]->abbreviation());

        $this->assertSame('Laufende Num̅er.', $columns[0]->title());
        $this->assertSame('Ob männlichen oder weiblichen Geschlechts.', $columns[1]->title());
        $this->assertSame('Vor- und Zuname.', $columns[2]->title());
        $this->assertSame('Jahr und Tag der Geburt.', $columns[3]->title());
        $this->assertSame('Geburtsort.', $columns[4]->title());
        $this->assertSame('Kirchspiel, wohin der Geburtsort gehört.', $columns[5]->title());
        $this->assertSame('leere Spalte', $columns[6]->title());
        $this->assertSame('Stand und Gewerbe.', $columns[7]->title());
        $this->assertSame('Grundbesitz.', $columns[8]->title());
        $this->assertSame('Wie lange er schon hier ist.', $columns[9]->title());
        $this->assertSame('Ob ledig oder verheirathet.', $columns[10]->title());
        $this->assertSame('Religion.', $columns[11]->title());
        $this->assertSame('Allgemeine Bemerkungen.', $columns[12]->title());
    }
}
