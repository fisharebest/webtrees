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

/**
 * @covers \Fisharebest\Webtrees\Census\CensusOfDeutschland1867
 */
class CensusOfDeutschland1867Test extends TestCase
{
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDeutschland1867();

        self::assertSame('Mecklenburg-Schwerin, Deutschland', $census->censusPlace());
        self::assertSame('03 DEC 1867', $census->censusDate());
    }

    public function testColumns(): void
    {
        $census  = new CensusOfDeutschland1867();
        $columns = $census->columns();

        self::assertCount(23, $columns);
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
        self::assertInstanceOf(CensusColumnRelationToHeadGerman::class, $columns[11]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[15]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[16]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[17]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[18]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[19]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[20]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[21]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[22]);

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
        self::assertSame('12.Stellung', $columns[11]->abbreviation());
        self::assertSame('13.Stand/Beruf', $columns[12]->abbreviation());
        self::assertSame('14.StA_M-S', $columns[13]->abbreviation());
        self::assertSame('15.StA', $columns[14]->abbreviation());
        self::assertSame('16.', $columns[15]->abbreviation());
        self::assertSame('17.', $columns[16]->abbreviation());
        self::assertSame('18.', $columns[17]->abbreviation());
        self::assertSame('19.', $columns[18]->abbreviation());
        self::assertSame('20.blind', $columns[19]->abbreviation());
        self::assertSame('21.taubstumm', $columns[20]->abbreviation());
        self::assertSame('22.blödsinnig', $columns[21]->abbreviation());
        self::assertSame('23.irrsinnig', $columns[22]->abbreviation());

        self::assertSame('Ordnungs-Nummer (1-15).', $columns[0]->title());
        self::assertSame('I. Vor- und Familien-Name jeder Person. Vorname', $columns[1]->title());
        self::assertSame('I. Vor- und Familien-Name jeder Person. Familienname.', $columns[2]->title());
        self::assertSame('II. Geschlecht männlich.', $columns[3]->title());
        self::assertSame('II. Geschlecht weiblich.', $columns[4]->title());
        self::assertSame('III. Alter.', $columns[5]->title());
        self::assertSame('IV. Religionsbekenntnis.', $columns[6]->title());
        self::assertSame('V. Familienstand. ledig.', $columns[7]->title());
        self::assertSame('V. Familienstand. verehelicht.', $columns[8]->title());
        self::assertSame('V. Familienstand. verwittwet.', $columns[9]->title());
        self::assertSame('V. Familienstand. geschieden.', $columns[10]->title());
        self::assertSame('V. Familienstand. Verhältnis der Familienglieder zum Haushaltungsvorstand.', $columns[11]->title());
        self::assertSame('VI. Stand, Beruf oder Vorbereitung zum Beruf, Arbeits- und Dienstverhältnis.', $columns[12]->title());
        self::assertSame('VII. Staatsangehörigkeit. Mecklenburg-Schwerinscher Unterthan.', $columns[13]->title());
        self::assertSame('VII. Staatsangehörigkeit. Anderen Staaten angehörig. Welchem Staat?', $columns[14]->title());
        self::assertSame('VIII. Art des Aufenthalts am Zählungsort. Norddeutscher und Zollvereins- See- und Flußschiffer.', $columns[15]->title());
        self::assertSame('VIII. Art des Aufenthalts am Zählungsort. Reisender im Gasthof.', $columns[16]->title());
        self::assertSame('VIII. Art des Aufenthalts am Zählungsort. Gast der Familie (zum Besuch aus).', $columns[17]->title());
        self::assertSame('VIII. Art des Aufenthalts am Zählungsort. Alle übrigen Anwesenden.', $columns[18]->title());
        self::assertSame('IX. Besondere Mängel einzelner Individuen. blind auf beiden Augen.', $columns[19]->title());
        self::assertSame('IX. Besondere Mängel einzelner Individuen. taubstumm.', $columns[20]->title());
        self::assertSame('IX. Besondere Mängel einzelner Individuen. blödsinnig.', $columns[21]->title());
        self::assertSame('IX. Besondere Mängel einzelner Individuen. irrsinnig.', $columns[22]->title());
    }
}
