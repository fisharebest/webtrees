<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

        self::assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        self::assertSame('AUG 1819', $census->censusDate());
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

        self::assertCount(13, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnFullName::class, $columns[2]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[3]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);

        self::assertSame('Nr.', $columns[0]->abbreviation());
        self::assertSame('Geschlecht', $columns[1]->abbreviation());
        self::assertSame('Name', $columns[2]->abbreviation());
        self::assertSame('Geburtsdatum', $columns[3]->abbreviation());
        self::assertSame('Geburtsort', $columns[4]->abbreviation());
        self::assertSame('Kirchspiel', $columns[5]->abbreviation());
        self::assertSame('', $columns[6]->abbreviation());
        self::assertSame('Stand/Beruf', $columns[7]->abbreviation());
        self::assertSame('Besitz', $columns[8]->abbreviation());
        self::assertSame('hier seit', $columns[9]->abbreviation());
        self::assertSame('Familienstand', $columns[10]->abbreviation());
        self::assertSame('Religion', $columns[11]->abbreviation());
        self::assertSame('Bemerkungen', $columns[12]->abbreviation());

        self::assertSame('Laufende Num̅er.', $columns[0]->title());
        self::assertSame('Ob männlichen oder weiblichen Geschlechts.', $columns[1]->title());
        self::assertSame('Vor- und Zuname.', $columns[2]->title());
        self::assertSame('Jahr und Tag der Geburt.', $columns[3]->title());
        self::assertSame('Geburtsort.', $columns[4]->title());
        self::assertSame('Kirchspiel, wohin der Geburtsort gehört.', $columns[5]->title());
        self::assertSame('leere Spalte', $columns[6]->title());
        self::assertSame('Stand und Gewerbe.', $columns[7]->title());
        self::assertSame('Grundbesitz.', $columns[8]->title());
        self::assertSame('Wie lange er schon hier ist.', $columns[9]->title());
        self::assertSame('Ob ledig oder verheirathet.', $columns[10]->title());
        self::assertSame('Religion.', $columns[11]->title());
        self::assertSame('Allgemeine Bemerkungen.', $columns[12]->title());
    }
}
