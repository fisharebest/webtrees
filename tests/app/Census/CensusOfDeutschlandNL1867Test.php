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

#[CoversClass(CensusOfDeutschlandNL1867::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfDeutschlandNL1867Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDeutschlandNL1867();

        self::assertSame('Mecklenburg-Schwerin (Nachtragsliste), Deutschland', $census->censusPlace());
        self::assertSame('03 DEC 1867', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDeutschlandNL1867();
        $columns = $census->columns();

        self::assertCount(18, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[2]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[5]);
        self::assertInstanceOf(CensusColumnReligion::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);

        self::assertSame('1.Nr.', $columns[0]->abbreviation());
        self::assertSame('2.Vorname', $columns[1]->abbreviation());
        self::assertSame('3.Familienname', $columns[2]->abbreviation());
        self::assertSame('4.männlich', $columns[3]->abbreviation());
        self::assertSame('5.weiblich', $columns[4]->abbreviation());
        self::assertSame('6.Geburtsjahr', $columns[5]->abbreviation());
        self::assertSame('7.Religion', $columns[6]->abbreviation());
        self::assertSame('8.ledig', $columns[7]->abbreviation());
        self::assertSame('9.verehelicht', $columns[8]->abbreviation());
        self::assertSame('10.verwittwet', $columns[9]->abbreviation());
        self::assertSame('11.geschieden', $columns[10]->abbreviation());
        self::assertSame('12.StA_M-S', $columns[11]->abbreviation());
        self::assertSame('13.StA', $columns[12]->abbreviation());
        self::assertSame('14.', $columns[13]->abbreviation());
        self::assertSame('15.', $columns[14]->abbreviation());
        self::assertSame('16.', $columns[15]->abbreviation());
        self::assertSame('17.', $columns[16]->abbreviation());
        self::assertSame('18.Aufenthaltsort', $columns[17]->abbreviation());

        self::assertSame('Ordnungs-Nummer.', $columns[0]->title());
        self::assertSame('I. Vor- und Familienname jeder Person. Vorname.', $columns[1]->title());
        self::assertSame('I. Vor- und Familienname jeder Person. Familienname.', $columns[2]->title());
        self::assertSame('II. Geschlecht männlich.', $columns[3]->title());
        self::assertSame('II. Geschlecht weiblich.', $columns[4]->title());
        self::assertSame('III. Alter.', $columns[5]->title());
        self::assertSame('IV. Religionsbekenntnis.', $columns[6]->title());
        self::assertSame('V. Familienstand. ledig.', $columns[7]->title());
        self::assertSame('V. Familienstand. verehelicht.', $columns[8]->title());
        self::assertSame('V. Familienstand. verwittwet.', $columns[9]->title());
        self::assertSame('V. Familienstand. geschieden.', $columns[10]->title());
        self::assertSame('VI. Staatsangehörigkeit. Mecklenburg-Schwerinscher Unterthan.', $columns[11]->title());
        self::assertSame('VI. Staatsangehörigkeit. Anderen Staaten angehörig. Welchem Staat?', $columns[12]->title());
        self::assertSame('VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende als See- oder Flußschiffer.', $columns[13]->title());
        self::assertSame('VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende auf Land- oder Seereisen.', $columns[14]->title());
        self::assertSame('VII. Art des Abwesenheit vom Zählungsorte. Nicht über ein Jahr Abwesende auf Besuch außerhalb des Orts.', $columns[15]->title());
        self::assertSame('VII. Art des Aufenthalts am Zählungsort. Ueber ein Jahr, oder in anderer Art als nach Spalte 14 bis 16 Abwesende.', $columns[16]->title());
        self::assertSame('VIII. Vermuthlicher Aufenthaltsort zur Zählungszeit.', $columns[17]->title());
    }
}
