<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Tests\Unit\I18N\Languages;

use Fisharebest\Webtrees\Contracts\LanguageInterface;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\TextDirection;
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\I18N\Languages\Indonesian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Indonesian::class)]
class IndonesianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Indonesian();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Latn, self::language()->script());
    }

    public function testFirstDay(): void
    {
        self::assertSame(Weekday::Sunday, self::language()->firstDay());
    }
    public function testPaperSize(): void
    {
        self::assertSame(PaperSize::A4, self::language()->paperSize());
    }


    public function testTextDirection(): void
    {
        self::assertSame(TextDirection::LTR, self::language()->textDirection());
    }

    public function testAlphabet(): void
    {
        self::assertSame([], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('id', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Indonesia', self::language()->endonym());
    }



    public function testStrtolower(): void
    {
        self::assertSame('abc', self::language()->strtolower('Abc'));
        self::assertSame('école', self::language()->strtolower('ÉCOLE'));
    }

    public function testStrtoupper(): void
    {
        self::assertSame('ABC', self::language()->strtoupper('Abc'));
        self::assertSame('ÉCOLE', self::language()->strtoupper('école'));
    }
    public function testDigits(): void
    {
        self::assertSame('-123,456.0789', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('-123.456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-123.456,0789%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Januari 2000'],
            ['JAN 2000', 'Januari 2000'],
            ['ABT JAN 2000', 'tentang Januari 2000'],
            ['FROM JAN 2000', 'dari Januari 2000'],
            ['AFT JAN 2000', 'setelah Januari 2000'],
            ['BEF JAN 2000', 'sebelum Januari 2000'],
            ['15 FEB 2000', '15 Februari 2000'],
            ['FEB 2000', 'Februari 2000'],
            ['ABT FEB 2000', 'tentang Februari 2000'],
            ['FROM FEB 2000', 'dari Februari 2000'],
            ['AFT FEB 2000', 'setelah Februari 2000'],
            ['BEF FEB 2000', 'sebelum Februari 2000'],
            ['15 MAR 2000', '15 Maret 2000'],
            ['MAR 2000', 'Maret 2000'],
            ['ABT MAR 2000', 'tentang Maret 2000'],
            ['FROM MAR 2000', 'dari Maret 2000'],
            ['AFT MAR 2000', 'setelah Maret 2000'],
            ['BEF MAR 2000', 'sebelum Maret 2000'],
            ['15 APR 2000', '15 April 2000'],
            ['APR 2000', 'April 2000'],
            ['ABT APR 2000', 'tentang April 2000'],
            ['FROM APR 2000', 'dari April 2000'],
            ['AFT APR 2000', 'setelah April 2000'],
            ['BEF APR 2000', 'sebelum April 2000'],
            ['15 MAY 2000', '15 Mei 2000'],
            ['MAY 2000', 'Mei 2000'],
            ['ABT MAY 2000', 'tentang Mei 2000'],
            ['FROM MAY 2000', 'dari Mei 2000'],
            ['AFT MAY 2000', 'setelah Mei 2000'],
            ['BEF MAY 2000', 'sebelum Mei 2000'],
            ['15 JUN 2000', '15 Juni 2000'],
            ['JUN 2000', 'Juni 2000'],
            ['ABT JUN 2000', 'tentang Juni 2000'],
            ['FROM JUN 2000', 'dari Juni 2000'],
            ['AFT JUN 2000', 'setelah Juni 2000'],
            ['BEF JUN 2000', 'sebelum Juni 2000'],
            ['15 JUL 2000', '15 Juli 2000'],
            ['JUL 2000', 'Juli 2000'],
            ['ABT JUL 2000', 'tentang Juli 2000'],
            ['FROM JUL 2000', 'dari Juli 2000'],
            ['AFT JUL 2000', 'setelah Juli 2000'],
            ['BEF JUL 2000', 'sebelum Juli 2000'],
            ['15 AUG 2000', '15 Agustus 2000'],
            ['AUG 2000', 'Agustus 2000'],
            ['ABT AUG 2000', 'tentang Agustus 2000'],
            ['FROM AUG 2000', 'dari Agustus 2000'],
            ['AFT AUG 2000', 'setelah Agustus 2000'],
            ['BEF AUG 2000', 'sebelum Agustus 2000'],
            ['15 SEP 2000', '15 September 2000'],
            ['SEP 2000', 'September 2000'],
            ['ABT SEP 2000', 'tentang September 2000'],
            ['FROM SEP 2000', 'dari September 2000'],
            ['AFT SEP 2000', 'setelah September 2000'],
            ['BEF SEP 2000', 'sebelum September 2000'],
            ['15 OCT 2000', '15 Oktober 2000'],
            ['OCT 2000', 'Oktober 2000'],
            ['ABT OCT 2000', 'tentang Oktober 2000'],
            ['FROM OCT 2000', 'dari Oktober 2000'],
            ['AFT OCT 2000', 'setelah Oktober 2000'],
            ['BEF OCT 2000', 'sebelum Oktober 2000'],
            ['15 NOV 2000', '15 Nopember 2000'],
            ['NOV 2000', 'Nopember 2000'],
            ['ABT NOV 2000', 'tentang Nopember 2000'],
            ['FROM NOV 2000', 'dari Nopember 2000'],
            ['AFT NOV 2000', 'setelah Nopember 2000'],
            ['BEF NOV 2000', 'sebelum Nopember 2000'],
            ['15 DEC 2000', '15 Desember 2000'],
            ['DEC 2000', 'Desember 2000'],
            ['ABT DEC 2000', 'tentang Desember 2000'],
            ['FROM DEC 2000', 'dari Desember 2000'],
            ['AFT DEC 2000', 'setelah Desember 2000'],
            ['BEF DEC 2000', 'sebelum Desember 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'tentang 15 Januari 2000'],
            ['CAL 15 JAN 2000', 'kalkulasi 15 Januari 2000'],
            ['EST 15 JAN 2000', 'estimasi 15 Januari 2000'],
            ['BEF 15 JAN 2000', 'sebelum 15 Januari 2000'],
            ['AFT 15 JAN 2000', 'setelah 15 Januari 2000'],
            ['FROM 15 JAN 2000', 'dari 15 Januari 2000'],
            ['TO 15 JAN 2000', 'untuk 15 Januari 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'antara 15 Januari 2000 dan 15 Februari 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'dari 15 Januari 2000 ke 15 Februari 2000'],
            ['INT 15 JAN 2000', 'penafsiran 15 Januari 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Januari 1700 M'],
            ['@#DJULIAN@ JAN 1700', 'Januari 1700 M'],
            ['ABT @#DJULIAN@ JAN 1700', 'tentang Januari 1700 M'],
            ['FROM @#DJULIAN@ JAN 1700', 'dari Januari 1700 M'],
            ['AFT @#DJULIAN@ JAN 1700', 'setelah Januari 1700 M'],
            ['BEF @#DJULIAN@ JAN 1700', 'sebelum Januari 1700 M'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Februari 1700 M'],
            ['@#DJULIAN@ FEB 1700', 'Februari 1700 M'],
            ['ABT @#DJULIAN@ FEB 1700', 'tentang Februari 1700 M'],
            ['FROM @#DJULIAN@ FEB 1700', 'dari Februari 1700 M'],
            ['AFT @#DJULIAN@ FEB 1700', 'setelah Februari 1700 M'],
            ['BEF @#DJULIAN@ FEB 1700', 'sebelum Februari 1700 M'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Maret 1700 M'],
            ['@#DJULIAN@ MAR 1700', 'Maret 1700 M'],
            ['ABT @#DJULIAN@ MAR 1700', 'tentang Maret 1700 M'],
            ['FROM @#DJULIAN@ MAR 1700', 'dari Maret 1700 M'],
            ['AFT @#DJULIAN@ MAR 1700', 'setelah Maret 1700 M'],
            ['BEF @#DJULIAN@ MAR 1700', 'sebelum Maret 1700 M'],
            ['@#DJULIAN@ 15 APR 1700', '15 April 1700 M'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 April 1645/46 M'],
            ['@#DJULIAN@ APR 1700', 'April 1700 M'],
            ['ABT @#DJULIAN@ APR 1700', 'tentang April 1700 M'],
            ['FROM @#DJULIAN@ APR 1700', 'dari April 1700 M'],
            ['AFT @#DJULIAN@ APR 1700', 'setelah April 1700 M'],
            ['BEF @#DJULIAN@ APR 1700', 'sebelum April 1700 M'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Mei 1700 M'],
            ['@#DJULIAN@ MAY 1700', 'Mei 1700 M'],
            ['ABT @#DJULIAN@ MAY 1700', 'tentang Mei 1700 M'],
            ['FROM @#DJULIAN@ MAY 1700', 'dari Mei 1700 M'],
            ['AFT @#DJULIAN@ MAY 1700', 'setelah Mei 1700 M'],
            ['BEF @#DJULIAN@ MAY 1700', 'sebelum Mei 1700 M'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Juni 1700 M'],
            ['@#DJULIAN@ JUN 1700', 'Juni 1700 M'],
            ['ABT @#DJULIAN@ JUN 1700', 'tentang Juni 1700 M'],
            ['FROM @#DJULIAN@ JUN 1700', 'dari Juni 1700 M'],
            ['AFT @#DJULIAN@ JUN 1700', 'setelah Juni 1700 M'],
            ['BEF @#DJULIAN@ JUN 1700', 'sebelum Juni 1700 M'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Juli 1700 M'],
            ['@#DJULIAN@ JUL 1700', 'Juli 1700 M'],
            ['ABT @#DJULIAN@ JUL 1700', 'tentang Juli 1700 M'],
            ['FROM @#DJULIAN@ JUL 1700', 'dari Juli 1700 M'],
            ['AFT @#DJULIAN@ JUL 1700', 'setelah Juli 1700 M'],
            ['BEF @#DJULIAN@ JUL 1700', 'sebelum Juli 1700 M'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Agustus 1700 M'],
            ['@#DJULIAN@ AUG 1700', 'Agustus 1700 M'],
            ['ABT @#DJULIAN@ AUG 1700', 'tentang Agustus 1700 M'],
            ['FROM @#DJULIAN@ AUG 1700', 'dari Agustus 1700 M'],
            ['AFT @#DJULIAN@ AUG 1700', 'setelah Agustus 1700 M'],
            ['BEF @#DJULIAN@ AUG 1700', 'sebelum Agustus 1700 M'],
            ['@#DJULIAN@ 15 SEP 1700', '15 September 1700 M'],
            ['@#DJULIAN@ SEP 1700', 'September 1700 M'],
            ['ABT @#DJULIAN@ SEP 1700', 'tentang September 1700 M'],
            ['FROM @#DJULIAN@ SEP 1700', 'dari September 1700 M'],
            ['AFT @#DJULIAN@ SEP 1700', 'setelah September 1700 M'],
            ['BEF @#DJULIAN@ SEP 1700', 'sebelum September 1700 M'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Oktober 1700 M'],
            ['@#DJULIAN@ OCT 1700', 'Oktober 1700 M'],
            ['ABT @#DJULIAN@ OCT 1700', 'tentang Oktober 1700 M'],
            ['FROM @#DJULIAN@ OCT 1700', 'dari Oktober 1700 M'],
            ['AFT @#DJULIAN@ OCT 1700', 'setelah Oktober 1700 M'],
            ['BEF @#DJULIAN@ OCT 1700', 'sebelum Oktober 1700 M'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Nopember 1700 M'],
            ['@#DJULIAN@ NOV 1700', 'Nopember 1700 M'],
            ['ABT @#DJULIAN@ NOV 1700', 'tentang Nopember 1700 M'],
            ['FROM @#DJULIAN@ NOV 1700', 'dari Nopember 1700 M'],
            ['AFT @#DJULIAN@ NOV 1700', 'setelah Nopember 1700 M'],
            ['BEF @#DJULIAN@ NOV 1700', 'sebelum Nopember 1700 M'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Desember 1700 M'],
            ['@#DJULIAN@ DEC 1700', 'Desember 1700 M'],
            ['ABT @#DJULIAN@ DEC 1700', 'tentang Desember 1700 M'],
            ['FROM @#DJULIAN@ DEC 1700', 'dari Desember 1700 M'],
            ['AFT @#DJULIAN@ DEC 1700', 'setelah Desember 1700 M'],
            ['BEF @#DJULIAN@ DEC 1700', 'sebelum Desember 1700 M'],
            ['@#DJULIAN@ 1700', '1700 M'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'tentang 15 Januari 1700 M'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'kalkulasi 15 Januari 1700 M'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimasi 15 Januari 1700 M'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'sebelum 15 Januari 1700 M'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'setelah 15 Januari 1700 M'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'dari 15 Januari 1700 M'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'untuk 15 Januari 1700 M'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'antara 15 Januari 1700 M dan 15 Februari 1700 M'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'dari 15 Januari 1700 M ke 15 Februari 1700 M'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'penafsiran 15 Januari 1700 M'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tisre 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tisre 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'tentang Tisre 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'dari Tisre 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'setelah Tisre 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'sebelum Tisre 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvana 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvana 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'tentang Heshvana 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'dari Heshvana 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'setelah Heshvana 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'sebelum Heshvana 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislep 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislep 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'tentang Kislep 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'dari Kislep 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'setelah Kislep 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'sebelum Kislep 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tepet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tepet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'tentang Tepet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'dari Tepet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'setelah Tepet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'sebelum Tepet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Sifat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Sifat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'tentang Sifat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'dari Sifat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'setelah Sifat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'sebelum Sifat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar 1 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar 1 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'tentang Adar 1 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'dari Adar 1 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'setelah Adar 1 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'sebelum Adar 1 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar 2 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar 2 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'tentang Adar 2 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'dari Adar 2 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'setelah Adar 2 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'sebelum Adar 2 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'tentang Nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'dari Nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'setelah Nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'sebelum Nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Yare 5765'],
            ['@#DHEBREW@ IYR 5765', 'Yare 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'tentang Yare 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'dari Yare 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'setelah Yare 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'sebelum Yare 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sipan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sipan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'tentang Sipan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'dari Sipan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'setelah Sipan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'sebelum Sipan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamud 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamud 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'tentang Tamud 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'dari Tamud 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'setelah Tamud 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'sebelum Tamud 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'tentang Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'dari Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'setelah Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'sebelum Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Eluls 5765'],
            ['@#DHEBREW@ ELL 5765', 'Eluls 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'tentang Eluls 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'dari Eluls 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'setelah Eluls 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'sebelum Eluls 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'tentang 15 Tisre 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'kalkulasi 15 Tisre 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimasi 15 Tisre 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'sebelum 15 Tisre 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'setelah 15 Tisre 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'dari 15 Tisre 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'untuk 15 Tisre 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'antara 15 Tisre 5765 dan 15 Heshvana 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'dari 15 Tisre 5765 ke 15 Heshvana 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'penafsiran 15 Tisre 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'tentang Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'dari Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'setelah Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'sebelum Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'tentang Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'dari Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'setelah Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'sebelum Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'tentang Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'dari Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'setelah Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'sebelum Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'tentang Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'dari Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'setelah Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'sebelum Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'tentang Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'dari Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'setelah Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'sebelum Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'tentang Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'dari Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'setelah Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'sebelum Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'tentang Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'dari Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'setelah Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'sebelum Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'tentang Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'dari Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'setelah Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'sebelum Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'tentang Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'dari Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'setelah Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'sebelum Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'tentang Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'dari Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'setelah Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'sebelum Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'tentang Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'dari Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'setelah Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'sebelum Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'tentang Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'dari Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'setelah Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'sebelum Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 hari pelengkap An XII'],
            ['@#DFRENCH R@ COMP 12', 'hari pelengkap An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'tentang hari pelengkap An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'dari hari pelengkap An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'setelah hari pelengkap An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'sebelum hari pelengkap An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'tentang 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'kalkulasi 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimasi 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'sebelum 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'setelah 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'dari 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'untuk 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'antara 15 Vendémiaire An XII dan 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'dari 15 Vendémiaire An XII ke 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'penafsiran 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharam 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharam 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'tentang Muharam 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'dari Muharam 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'setelah Muharam 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'sebelum Muharam 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Sapar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Sapar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'tentang Sapar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'dari Sapar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'setelah Sapar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'sebelum Sapar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabiul Awal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabiul Awal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'tentang Rabiul Awal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'dari Rabiul Awal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'setelah Rabiul Awal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'sebelum Rabiul Awal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabiul Akhir 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabiul Akhir 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'tentang Rabiul Akhir 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'dari Rabiul Akhir 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'setelah Rabiul Akhir 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'sebelum Rabiul Akhir 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumadil Awal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumadil Awal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'tentang Jumadil Awal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'dari Jumadil Awal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'setelah Jumadil Awal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'sebelum Jumadil Awal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumadil Tsani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumadil Tsani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'tentang Jumadil Tsani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'dari Jumadil Tsani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'setelah Jumadil Tsani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'sebelum Jumadil Tsani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rojab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rojab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'tentang Rojab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'dari Rojab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'setelah Rojab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'sebelum Rojab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sya’ban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sya’ban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'tentang Sya’ban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'dari Sya’ban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'setelah Sya’ban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'sebelum Sya’ban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Romadhon 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Romadhon 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'tentang Romadhon 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'dari Romadhon 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'setelah Romadhon 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'sebelum Romadhon 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Syawal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Syawal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'tentang Syawal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'dari Syawal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'setelah Syawal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'sebelum Syawal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dzulqa’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dzulqa’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'tentang Dzulqa’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'dari Dzulqa’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'setelah Dzulqa’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'sebelum Dzulqa’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'tentang 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'dari 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'setelah 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'sebelum 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'tentang 15 Muharam 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'kalkulasi 15 Muharam 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimasi 15 Muharam 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'sebelum 15 Muharam 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'setelah 15 Muharam 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'dari 15 Muharam 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'untuk 15 Muharam 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'antara 15 Muharam 1425 dan 15 Sapar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'dari 15 Muharam 1425 ke 15 Sapar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'penafsiran 15 Muharam 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Parpardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Parpardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'tentang Parpardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'dari Parpardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'setelah Parpardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'sebelum Parpardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordi 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordi 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'tentang Ordi 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'dari Ordi 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'setelah Ordi 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'sebelum Ordi 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Korad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Korad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'tentang Korad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'dari Korad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'setelah Korad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'sebelum Korad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tear 1384'],
            ['@#DJALALI@ TIR 1384', 'Tear 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'tentang Tear 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'dari Tear 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'setelah Tear 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'sebelum Tear 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Murdad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Murdad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'tentang Murdad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'dari Murdad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'setelah Murdad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'sebelum Murdad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Sahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Sahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'tentang Sahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'dari Sahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'setelah Sahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'sebelum Sahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Meher 1384'],
            ['@#DJALALI@ MEHR 1384', 'Meher 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'tentang Meher 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'dari Meher 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'setelah Meher 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'sebelum Meher 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Abana 1384'],
            ['@#DJALALI@ ABAN 1384', 'Abana 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'tentang Abana 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'dari Abana 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'setelah Abana 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'sebelum Abana 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azars 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azars 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'tentang Azars 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'dari Azars 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'setelah Azars 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'sebelum Azars 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Hari 1384'],
            ['@#DJALALI@ DEY 1384', 'Hari 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'tentang Hari 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'dari Hari 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'setelah Hari 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'sebelum Hari 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahmana 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahmana 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'tentang Bahmana 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'dari Bahmana 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'setelah Bahmana 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'sebelum Bahmana 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Espan 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Espan 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'tentang Espan 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'dari Espan 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'setelah Espan 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'sebelum Espan 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'tentang 15 Parpardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'kalkulasi 15 Parpardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimasi 15 Parpardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'sebelum 15 Parpardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'setelah 15 Parpardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'dari 15 Parpardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'untuk 15 Parpardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'antara 15 Parpardin 1384 dan 15 Ordi 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'dari 15 Parpardin 1384 ke 15 Ordi 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'penafsiran 15 Parpardin 1384'],
        ];
    }

    public function testFormatList(): void
    {
        $language = static::language();

        self::assertSame('', $language->formatList([]));
        self::assertSame('one', $language->formatList(['one']));
        self::assertSame('one, two', $language->formatList(['one', 'two']));
        self::assertSame('one, two, three', $language->formatList(['one', 'two', 'three']));

        self::assertSame('', $language->formatListAnd([]));
        self::assertSame('one', $language->formatListAnd(['one']));
        self::assertSame('one dan two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two dan three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one atau two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two atau three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife with son (born 2000) and daughter (born 1998)
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 1 JAN 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 1 JAN 1998");
        $child = self::unknown('c', "1 FAMC @fm@");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $fosterDaughter = self::female('fsd', "1 FAMC @fd@\n2 PEDI foster");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");

        // Husband's family
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMS @fbro@\n1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        // Wife's family
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Nieces/nephews
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins
        $cousinM = self::male('cm', "1 FAMC @fbro@");
        $cousinF = self::female('cf', "1 FAMC @fsis@");

        // Great-grandparents
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");

        // Engaged
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        // Families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fsd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cm@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cf@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinM, $cousinF,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('istri', 'suami', [$husband, $fm, $wife]);
        self::assertRelationshipNames('mantan suami', 'mantan istri', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('tunangan perempuan', 'tunangan laki-laki', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('ibu', 'anak laki-laki', [$son, $fm, $wife]);
        self::assertRelationshipNames('ayah', 'anak laki-laki', [$son, $fm, $husband]);
        self::assertRelationshipNames('ibu', 'anak perempuan', [$daughter, $fm, $wife]);

        // Adopted / Fostered
        self::assertRelationshipNames('ibu angkat', 'anak laki-laki angkat', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('ibu asuh', 'anak perempuan asuh', [$fosterDaughter, $fd, $wife]);

        // Siblings — elder/younger (daughter born 1998 is older than son born 2000)
        self::assertRelationshipNames('kakak perempuan', 'adik laki-laki', [$son, $fm, $daughter]);
        self::assertRelationshipNames('adik laki-laki', 'kakak perempuan', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('ayah tiri', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('anak perempuan tiri', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('mertua perempuan', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('mertua laki-laki', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('mertua perempuan', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('mertua laki-laki', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('menantu perempuan', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('menantu laki-laki', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('ipar laki-laki', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('ipar perempuan', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents
        self::assertRelationshipName('nenek', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('kakek', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('nenek', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('kakek', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('cucu laki-laki', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('cucu perempuan', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts/Uncles
        self::assertRelationshipName('tante', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('paman', [$son, $fm, $husband, $fp, $brotherOfH]);
        self::assertRelationshipName('tante', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('paman', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces/Nephews
        self::assertRelationshipName('keponakan perempuan', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('keponakan laki-laki', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        self::assertRelationshipName('keponakan perempuan', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('keponakan laki-laki', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins
        self::assertRelationshipName('sepupu laki-laki', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinM]);
        self::assertRelationshipName('sepupu perempuan', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinF]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('nenek buyut', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('kakek buyut', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic — grandparent's sibling, n=2)
        self::assertRelationshipName('tante', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('paman', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
