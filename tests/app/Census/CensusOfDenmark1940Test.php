<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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


#[CoversClass(CensusOfDenmark1940::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusOfDenmark1940Test extends TestCase
{
    /**
     * Test the census place and date
     */
    public function testPlaceAndDate(): void
    {
        $census = new CensusOfDenmark1940();

        self::assertSame('Danmark', $census->censusPlace());
        self::assertSame('05 NOV 1940', $census->censusDate());
    }

    /**
     * Test the census columns
     */
    public function testColumns(): void
    {
        $census  = new CensusOfDenmark1940();
        $columns = $census->columns();

        self::assertCount(15, $columns);
        self::assertInstanceOf(CensusColumnSurnameGivenNames::class, $columns[0]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[1]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[2]);
        self::assertInstanceOf(CensusColumnSexMK::class, $columns[3]);
        self::assertInstanceOf(CensusColumnBirthDaySlashMonth::class, $columns[4]);
        self::assertInstanceOf(CensusColumnBirthYear::class, $columns[5]);
        self::assertInstanceOf(CensusColumnBirthPlace::class, $columns[6]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[7]);
        self::assertInstanceOf(CensusColumnConditionDanish::class, $columns[8]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[9]);
        self::assertInstanceOf(CensusColumnRelationToHead::class, $columns[10]);
        self::assertInstanceOf(CensusColumnOccupation::class, $columns[11]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[12]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[13]);
        self::assertInstanceOf(CensusColumnNull::class, $columns[14]);

        self::assertSame('Navn', $columns[0]->abbreviation());
        self::assertSame('Nærværende', $columns[1]->abbreviation());
        self::assertSame('Fraværende', $columns[2]->abbreviation());
        self::assertSame('Køn', $columns[3]->abbreviation());
        self::assertSame('Fødselsdag', $columns[4]->abbreviation());
        self::assertSame('Fødselsaar', $columns[5]->abbreviation());
        self::assertSame('Fødested', $columns[6]->abbreviation());
        self::assertSame('Statsborgerforhold', $columns[7]->abbreviation());
        self::assertSame('Civilstand', $columns[8]->abbreviation());
        self::assertSame('Indgaaelse', $columns[9]->abbreviation());
        self::assertSame('Stilling i familien', $columns[10]->abbreviation());
        self::assertSame('Erhverv', $columns[11]->abbreviation());
        self::assertSame('Virksomhedens', $columns[12]->abbreviation());
        self::assertSame('Hustruen', $columns[13]->abbreviation());
        self::assertSame('Døtre', $columns[14]->abbreviation());

        self::assertSame('', $columns[0]->title());
        self::assertSame('Hvis den i Rubrik 1 opførte Person er midleritidg nærværende d.v.s. har fast Bopæl ????? (er optaget under en anden Address i Folkeregistret), anføres her den faste Bopæls Adresse (Kommunens Navn og den fuldstændige Adresse i denne; for Udlændinge dog kun Landets Navn).', $columns[1]->title());
        self::assertSame('Hvis den i Rubrik 1 opførte Person er midleritidg fraværende d.v.s. har fast Bopæl paa Tællingsstedet (er optaget underdenne Address i Folkeregistret), men den 5. Novemer ikke er til Stede paa Tællingsstedet, anføres her „fraværende“ og Adressen paa det midlertidige Opholdssted (ved Ophold i Udlandet anføres jun Landets Navn).', $columns[2]->title());
        self::assertSame('Køn Mand (M) Kvinde (K)', $columns[3]->title());
        self::assertSame('', $columns[4]->title());
        self::assertSame('', $columns[5]->title());
        self::assertSame('', $columns[6]->title());
        self::assertSame('', $columns[7]->title());
        self::assertSame('Ægteskabelig Stillinge. Ugift (U), Gift (G), Enkemand eller Enke (E), Separeret (S), Fraskilt (F).', $columns[8]->title());
        self::assertSame('Date for det nuværende Ægteskabs Indgaaelse. NB." RUbrikken udfyldes ikke al Enkemaend, Enker, Separerede eller Fraskilte.', $columns[9]->title());
        self::assertSame('', $columns[10]->title());
        self::assertSame('', $columns[11]->title());
        self::assertSame('Virksomhedens (Branchens) Art', $columns[12]->title());
        self::assertSame('Besvares kun af Hustruen og hjemmeboende Børn over 14 Aar', $columns[13]->title());
        self::assertSame('Besvares kun af hjemmeboende Døtre over 14 Aar', $columns[14]->title());
    }
}
