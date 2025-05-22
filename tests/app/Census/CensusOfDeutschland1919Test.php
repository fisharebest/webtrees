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

#[CoversClass(CensusOfDeutschland1919::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfDeutschland1919Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDeutschland1919();

        self::assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        self::assertSame('08 OCT 1919', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfDeutschland1919();
        $columns = $census->columns();

        self::assertCount(17, $columns);
        self::assertInstanceOf(CensusColumnNull::class, $columns[0]);
        self::assertInstanceOf(CensusColumnGivenNames::class, $columns[1]);
        self::assertInstanceOf(CensusColumnSurname::class, $columns[2]);
        self::assertInstanceOf(CensusColumnRelationToHeadGerman::class, $columns[3]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[4]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[5]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[6]);
        self::assertInstanceOf(CensusColumnBirthDay::class, $columns[7]);
        self::assertInstanceOf(CensusColumnBirthMonth::class, $columns[8]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[9]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[10]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);

        self::assertSame('Nummer', $columns[0]->abbreviation());
        self::assertSame('Vorname', $columns[1]->abbreviation());
        self::assertSame('Familienname', $columns[2]->abbreviation());
        self::assertSame('Stellung im Haushalt', $columns[3]->abbreviation());
        self::assertSame('männlich', $columns[4]->abbreviation());
        self::assertSame('weiblich', $columns[5]->abbreviation());
        self::assertSame('Familienstand', $columns[6]->abbreviation());
        self::assertSame('Geburts-Tag', $columns[7]->abbreviation());
        self::assertSame('Geburts-Monat', $columns[8]->abbreviation());
        self::assertSame('Geburts-Jahr', $columns[9]->abbreviation());
        self::assertSame('Geburtsort', $columns[10]->abbreviation());
        self::assertSame('Amt, Kreis, Bezirk', $columns[11]->abbreviation());
        self::assertSame('StA', $columns[12]->abbreviation());
        self::assertSame('Gemeinde Brotversorgung', $columns[13]->abbreviation());
        self::assertSame('Wohn-/ Aufenthaltsort', $columns[14]->abbreviation());
        self::assertSame('Dienstgrad', $columns[15]->abbreviation());
        self::assertSame('Kriegsgefangener', $columns[16]->abbreviation());

        self::assertSame('Laufende Nummer', $columns[0]->title());
        self::assertSame('Vorname', $columns[1]->title());
        self::assertSame('Familienname', $columns[2]->title());
        self::assertSame('Stellung im Haushalt', $columns[3]->title());
        self::assertSame('Geschlecht männlich', $columns[4]->title());
        self::assertSame('Geschlecht weiblich', $columns[5]->title());
        self::assertSame('Familienstand', $columns[6]->title());
        self::assertSame('Geburts-Tag', $columns[7]->title());
        self::assertSame('Geburts-Monat', $columns[8]->title());
        self::assertSame('Geburts-Jahr', $columns[9]->title());
        self::assertSame('Name des Geburtsorts', $columns[10]->title());
        self::assertSame('Amt, Kreis oder sonstiger obrigkeitlicher Bezirk', $columns[11]->title());
        self::assertSame('Staatsangehörigkeit', $columns[12]->title());
        self::assertSame('Gemeinde der Brotversorgung', $columns[13]->title());
        self::assertSame('Wohnort bei nur vorübergehend Anwesenden. Aufenthaltsort bei vorübergehend Abwesenden', $columns[14]->title());
        self::assertSame('Für Militärpersonen: Angabe des Dienstgrades', $columns[15]->title());
        self::assertSame('Angabe ob Kriegsgefangener', $columns[16]->title());
    }
}
