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
use Fisharebest\Webtrees\I18N\Languages\Polish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Polish::class)]
class PolishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Polish();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Latn, self::language()->script());
    }

    public function testFirstDay(): void
    {
        self::assertSame(Weekday::Monday, self::language()->firstDay());
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
        self::assertSame(['A', 'B', 'C', 'Ć', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'Ł', 'M', 'N', 'O', 'Ó', 'P', 'Q', 'R', 'S', 'Ś', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ź', 'Ż'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('pl', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('polski', self::language()->endonym());
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
        self::assertSame('-123 456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-123 456,0789%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 stycznia 2000'],
            ['JAN 2000', 'styczeń 2000'],
            ['ABT JAN 2000', 'ok. stycznia 2000'],
            ['FROM JAN 2000', 'od stycznia 2000'],
            ['AFT JAN 2000', 'po styczniu 2000'],
            ['BEF JAN 2000', 'przed styczniem 2000'],
            ['15 FEB 2000', '15 lutego 2000'],
            ['FEB 2000', 'luty 2000'],
            ['ABT FEB 2000', 'ok. lutego 2000'],
            ['FROM FEB 2000', 'od lutego 2000'],
            ['AFT FEB 2000', 'po lutym 2000'],
            ['BEF FEB 2000', 'przed lutym 2000'],
            ['15 MAR 2000', '15 marca 2000'],
            ['MAR 2000', 'marzec 2000'],
            ['ABT MAR 2000', 'ok. marca 2000'],
            ['FROM MAR 2000', 'od marca 2000'],
            ['AFT MAR 2000', 'po marcu 2000'],
            ['BEF MAR 2000', 'przed marcem 2000'],
            ['15 APR 2000', '15 kwietnia 2000'],
            ['APR 2000', 'kwiecień 2000'],
            ['ABT APR 2000', 'ok. kwietnia 2000'],
            ['FROM APR 2000', 'od kwietnia 2000'],
            ['AFT APR 2000', 'po kwietniu 2000'],
            ['BEF APR 2000', 'przed kwietniem 2000'],
            ['15 MAY 2000', '15 maja 2000'],
            ['MAY 2000', 'maj 2000'],
            ['ABT MAY 2000', 'ok. maja 2000'],
            ['FROM MAY 2000', 'od maja 2000'],
            ['AFT MAY 2000', 'po maju 2000'],
            ['BEF MAY 2000', 'przed majem 2000'],
            ['15 JUN 2000', '15 czerwca 2000'],
            ['JUN 2000', 'czerwiec 2000'],
            ['ABT JUN 2000', 'ok. czerwca 2000'],
            ['FROM JUN 2000', 'od czerwca 2000'],
            ['AFT JUN 2000', 'po czerwcu 2000'],
            ['BEF JUN 2000', 'przed czerwcem 2000'],
            ['15 JUL 2000', '15 lipca 2000'],
            ['JUL 2000', 'lipiec 2000'],
            ['ABT JUL 2000', 'ok. lipca 2000'],
            ['FROM JUL 2000', 'od lipca 2000'],
            ['AFT JUL 2000', 'po lipcu 2000'],
            ['BEF JUL 2000', 'przed lipcem 2000'],
            ['15 AUG 2000', '15 sierpnia 2000'],
            ['AUG 2000', 'sierpień 2000'],
            ['ABT AUG 2000', 'ok. sierpnia 2000'],
            ['FROM AUG 2000', 'od sierpnia 2000'],
            ['AFT AUG 2000', 'po sierpniu 2000'],
            ['BEF AUG 2000', 'przed sierpniem 2000'],
            ['15 SEP 2000', '15 września 2000'],
            ['SEP 2000', 'wrzesień 2000'],
            ['ABT SEP 2000', 'ok. września 2000'],
            ['FROM SEP 2000', 'od września 2000'],
            ['AFT SEP 2000', 'po wrześniu 2000'],
            ['BEF SEP 2000', 'przed wrześniem 2000'],
            ['15 OCT 2000', '15 października 2000'],
            ['OCT 2000', 'październik 2000'],
            ['ABT OCT 2000', 'ok. października 2000'],
            ['FROM OCT 2000', 'od października 2000'],
            ['AFT OCT 2000', 'po październiku 2000'],
            ['BEF OCT 2000', 'przed październikiem 2000'],
            ['15 NOV 2000', '15 listopada 2000'],
            ['NOV 2000', 'listopad 2000'],
            ['ABT NOV 2000', 'ok. listopada 2000'],
            ['FROM NOV 2000', 'od listopada 2000'],
            ['AFT NOV 2000', 'po listopadzie 2000'],
            ['BEF NOV 2000', 'przed listopadem 2000'],
            ['15 DEC 2000', '15 grudnia 2000'],
            ['DEC 2000', 'grudzień 2000'],
            ['ABT DEC 2000', 'ok. grudnia 2000'],
            ['FROM DEC 2000', 'od grudnia 2000'],
            ['AFT DEC 2000', 'po grudniu 2000'],
            ['BEF DEC 2000', 'przed grudniem 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'ok. 15 stycznia 2000'],
            ['CAL 15 JAN 2000', 'wyliczone na 15 stycznia 2000'],
            ['EST 15 JAN 2000', 'szacowane na 15 stycznia 2000'],
            ['BEF 15 JAN 2000', 'przed 15 stycznia 2000'],
            ['AFT 15 JAN 2000', 'po 15 stycznia 2000'],
            ['FROM 15 JAN 2000', 'od 15 stycznia 2000'],
            ['TO 15 JAN 2000', 'do 15 stycznia 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'pomiędzy 15 stycznia 2000 a 15 lutego 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'od 15 stycznia 2000 do 15 lutego 2000'],
            ['INT 15 JAN 2000', 'zinterpretowane jako 15 stycznia 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 stycznia 1700 n.e.'],
            ['@#DJULIAN@ JAN 1700', 'styczeń 1700 n.e.'],
            ['ABT @#DJULIAN@ JAN 1700', 'ok. stycznia 1700 n.e.'],
            ['FROM @#DJULIAN@ JAN 1700', 'od stycznia 1700 n.e.'],
            ['AFT @#DJULIAN@ JAN 1700', 'po styczniu 1700 n.e.'],
            ['BEF @#DJULIAN@ JAN 1700', 'przed styczniem 1700 n.e.'],
            ['@#DJULIAN@ 15 FEB 1700', '15 lutego 1700 n.e.'],
            ['@#DJULIAN@ FEB 1700', 'luty 1700 n.e.'],
            ['ABT @#DJULIAN@ FEB 1700', 'ok. lutego 1700 n.e.'],
            ['FROM @#DJULIAN@ FEB 1700', 'od lutego 1700 n.e.'],
            ['AFT @#DJULIAN@ FEB 1700', 'po lutym 1700 n.e.'],
            ['BEF @#DJULIAN@ FEB 1700', 'przed lutym 1700 n.e.'],
            ['@#DJULIAN@ 15 MAR 1700', '15 marca 1700 n.e.'],
            ['@#DJULIAN@ MAR 1700', 'marzec 1700 n.e.'],
            ['ABT @#DJULIAN@ MAR 1700', 'ok. marca 1700 n.e.'],
            ['FROM @#DJULIAN@ MAR 1700', 'od marca 1700 n.e.'],
            ['AFT @#DJULIAN@ MAR 1700', 'po marcu 1700 n.e.'],
            ['BEF @#DJULIAN@ MAR 1700', 'przed marcem 1700 n.e.'],
            ['@#DJULIAN@ 15 APR 1700', '15 kwietnia 1700 n.e.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 kwietnia 1645/46 n.e.'],
            ['@#DJULIAN@ APR 1700', 'kwiecień 1700 n.e.'],
            ['ABT @#DJULIAN@ APR 1700', 'ok. kwietnia 1700 n.e.'],
            ['FROM @#DJULIAN@ APR 1700', 'od kwietnia 1700 n.e.'],
            ['AFT @#DJULIAN@ APR 1700', 'po kwietniu 1700 n.e.'],
            ['BEF @#DJULIAN@ APR 1700', 'przed kwietniem 1700 n.e.'],
            ['@#DJULIAN@ 15 MAY 1700', '15 maja 1700 n.e.'],
            ['@#DJULIAN@ MAY 1700', 'maj 1700 n.e.'],
            ['ABT @#DJULIAN@ MAY 1700', 'ok. maja 1700 n.e.'],
            ['FROM @#DJULIAN@ MAY 1700', 'od maja 1700 n.e.'],
            ['AFT @#DJULIAN@ MAY 1700', 'po maju 1700 n.e.'],
            ['BEF @#DJULIAN@ MAY 1700', 'przed majem 1700 n.e.'],
            ['@#DJULIAN@ 15 JUN 1700', '15 czerwca 1700 n.e.'],
            ['@#DJULIAN@ JUN 1700', 'czerwiec 1700 n.e.'],
            ['ABT @#DJULIAN@ JUN 1700', 'ok. czerwca 1700 n.e.'],
            ['FROM @#DJULIAN@ JUN 1700', 'od czerwca 1700 n.e.'],
            ['AFT @#DJULIAN@ JUN 1700', 'po czerwcu 1700 n.e.'],
            ['BEF @#DJULIAN@ JUN 1700', 'przed czerwcem 1700 n.e.'],
            ['@#DJULIAN@ 15 JUL 1700', '15 lipca 1700 n.e.'],
            ['@#DJULIAN@ JUL 1700', 'lipiec 1700 n.e.'],
            ['ABT @#DJULIAN@ JUL 1700', 'ok. lipca 1700 n.e.'],
            ['FROM @#DJULIAN@ JUL 1700', 'od lipca 1700 n.e.'],
            ['AFT @#DJULIAN@ JUL 1700', 'po lipcu 1700 n.e.'],
            ['BEF @#DJULIAN@ JUL 1700', 'przed lipcem 1700 n.e.'],
            ['@#DJULIAN@ 15 AUG 1700', '15 sierpnia 1700 n.e.'],
            ['@#DJULIAN@ AUG 1700', 'sierpień 1700 n.e.'],
            ['ABT @#DJULIAN@ AUG 1700', 'ok. sierpnia 1700 n.e.'],
            ['FROM @#DJULIAN@ AUG 1700', 'od sierpnia 1700 n.e.'],
            ['AFT @#DJULIAN@ AUG 1700', 'po sierpniu 1700 n.e.'],
            ['BEF @#DJULIAN@ AUG 1700', 'przed sierpniem 1700 n.e.'],
            ['@#DJULIAN@ 15 SEP 1700', '15 września 1700 n.e.'],
            ['@#DJULIAN@ SEP 1700', 'wrzesień 1700 n.e.'],
            ['ABT @#DJULIAN@ SEP 1700', 'ok. września 1700 n.e.'],
            ['FROM @#DJULIAN@ SEP 1700', 'od września 1700 n.e.'],
            ['AFT @#DJULIAN@ SEP 1700', 'po wrześniu 1700 n.e.'],
            ['BEF @#DJULIAN@ SEP 1700', 'przed wrześniem 1700 n.e.'],
            ['@#DJULIAN@ 15 OCT 1700', '15 października 1700 n.e.'],
            ['@#DJULIAN@ OCT 1700', 'październik 1700 n.e.'],
            ['ABT @#DJULIAN@ OCT 1700', 'ok. października 1700 n.e.'],
            ['FROM @#DJULIAN@ OCT 1700', 'od października 1700 n.e.'],
            ['AFT @#DJULIAN@ OCT 1700', 'po październiku 1700 n.e.'],
            ['BEF @#DJULIAN@ OCT 1700', 'przed październikiem 1700 n.e.'],
            ['@#DJULIAN@ 15 NOV 1700', '15 listopada 1700 n.e.'],
            ['@#DJULIAN@ NOV 1700', 'listopad 1700 n.e.'],
            ['ABT @#DJULIAN@ NOV 1700', 'ok. listopada 1700 n.e.'],
            ['FROM @#DJULIAN@ NOV 1700', 'od listopada 1700 n.e.'],
            ['AFT @#DJULIAN@ NOV 1700', 'po listopadzie 1700 n.e.'],
            ['BEF @#DJULIAN@ NOV 1700', 'przed listopadem 1700 n.e.'],
            ['@#DJULIAN@ 15 DEC 1700', '15 grudnia 1700 n.e.'],
            ['@#DJULIAN@ DEC 1700', 'grudzień 1700 n.e.'],
            ['ABT @#DJULIAN@ DEC 1700', 'ok. grudnia 1700 n.e.'],
            ['FROM @#DJULIAN@ DEC 1700', 'od grudnia 1700 n.e.'],
            ['AFT @#DJULIAN@ DEC 1700', 'po grudniu 1700 n.e.'],
            ['BEF @#DJULIAN@ DEC 1700', 'przed grudniem 1700 n.e.'],
            ['@#DJULIAN@ 1700', '1700 n.e.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'ok. 15 stycznia 1700 n.e.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'wyliczone na 15 stycznia 1700 n.e.'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'szacowane na 15 stycznia 1700 n.e.'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'przed 15 stycznia 1700 n.e.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'po 15 stycznia 1700 n.e.'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'od 15 stycznia 1700 n.e.'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'do 15 stycznia 1700 n.e.'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'pomiędzy 15 stycznia 1700 n.e. a 15 lutego 1700 n.e.'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'od 15 stycznia 1700 n.e. do 15 lutego 1700 n.e.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'zinterpretowane jako 15 stycznia 1700 n.e.'],
            ['@#DHEBREW@ 15 TSH 5765', '15 tiszri 5765'],
            ['@#DHEBREW@ TSH 5765', 'tiszri 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'ok. tiszri 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'od tiszri 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'po tiszri 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'przed tiszri 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 cheszwan 5765'],
            ['@#DHEBREW@ CSH 5765', 'cheszwan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'ok. cheszwan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'od cheszwan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'po cheszwan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'przed cheszwan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 kislew 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislew 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'ok. kislew 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'od kislew 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'po kislew 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'przed kislew 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 tewet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tewet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'ok. tewet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'od tewet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'po tewet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'przed tewet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 szwat 5765'],
            ['@#DHEBREW@ SHV 5765', 'szwat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'ok. szwat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'od szwat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'po szwat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'przed szwat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'ok. adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'od adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'po adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'przed adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'ok. adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'od adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'po adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'przed adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'ok. nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'od nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'po nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'przed nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 ijar 5765'],
            ['@#DHEBREW@ IYR 5765', 'ijar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'ok. ijar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'od ijar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'po ijar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'przed ijar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 siwan 5765'],
            ['@#DHEBREW@ SVN 5765', 'siwan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'ok. siwan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'od siwan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'po siwan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'przed siwan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'ok. tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'od tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'po tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'przed tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 aw 5765'],
            ['@#DHEBREW@ AAV 5765', 'aw 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'ok. aw 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'od aw 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'po aw 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'przed aw 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'ok. elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'od elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'po elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'przed elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'ok. 15 tiszri 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'wyliczone na 15 tiszri 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'szacowane na 15 tiszri 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'przed 15 tiszri 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'po 15 tiszri 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'od 15 tiszri 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'do 15 tiszri 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'pomiędzy 15 tiszri 5765 a 15 cheszwan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'od 15 tiszri 5765 do 15 cheszwan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'zinterpretowane jako 15 tiszri 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'ok. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'od Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'po Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'przed Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'ok. Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'od Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'po Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'przed Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'ok. Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'od Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'po Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'przed Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'ok. Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'od Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'po Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'przed Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'ok. Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'od Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'po Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'przed Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'ok. Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'od Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'po Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'przed Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'ok. Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'od Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'po Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'przed Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'ok. Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'od Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'po Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'przed Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'ok. Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'od Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'po Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'przed Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'ok. Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'od Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'po Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'przed Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'ok. Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'od Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'po Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'przed Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'ok. Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'od Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'po Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'przed Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 Dni Sankiulotów An XII'],
            ['@#DFRENCH R@ COMP 12', 'Dni Sankiulotów An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'ok. Dni Sankiulotów An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'od Dni Sankiulotów An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'po Dni Sankiulotów An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'przed Dni Sankiulotów An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'ok. 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'wyliczone na 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'szacowane na 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'przed 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'po 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'od 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'do 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'pomiędzy 15 Vendémiaire An XII a 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'od 15 Vendémiaire An XII do 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'zinterpretowane jako 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'ok. muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'od muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'po muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'przed muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'ok. safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'od safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'po safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'przed safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 rabi al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'rabi al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'ok. rabi al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'od rabi al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'po rabi al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'przed rabi al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 rabi al-sani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'rabi al-sani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'ok. rabi al-sani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'od rabi al-sani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'po rabi al-sani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'przed rabi al-sani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 dżumada al-ula 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'dżumada al-ula 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'ok. dżumada al-ula 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'od dżumada al-ula 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'po dżumada al-ula 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'przed dżumada al-ula 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 dżumada as-sani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'dżumada as-sani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'ok. dżumada as-sani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'od dżumada as-sani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'po dżumada as-sani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'przed dżumada as-sani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 radżab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'radżab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'ok. radżab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'od radżab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'po radżab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'przed radżab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 szaban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'szaban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'ok. szaban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'od szaban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'po szaban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'przed szaban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'ok. ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'od ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'po ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'przed ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 szawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'szawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'ok. szawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'od szawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'po szawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'przed szawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 zu al-kada 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'zu al-kada 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'ok. zu al-kada 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'od zu al-kada 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'po zu al-kada 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'przed zu al-kada 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'ok. 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'od 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'po 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'przed 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'ok. 15 muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'wyliczone na 15 muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'szacowane na 15 muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'przed 15 muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'po 15 muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'od 15 muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'do 15 muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'pomiędzy 15 muharram 1425 a 15 safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'od 15 muharram 1425 do 15 safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'zinterpretowane jako 15 muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farwardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farwardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'ok. Farwardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'od Farwardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'po Farwardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'przed Farwardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibeheszt 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibeheszt 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'ok. Ordibeheszt 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'od Ordibeheszt 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'po Ordibeheszt 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'przed Ordibeheszt 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Chordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Chordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'ok. Chordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'od Chordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'po Chordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'przed Chordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'ok. Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'od Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'po Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'przed Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'ok. Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'od Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'po Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'przed Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Szahriwar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Szahriwar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'ok. Szahriwar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'od Szahriwar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'po Szahriwar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'przed Szahriwar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'ok. Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'od Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'po Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'przed Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'ok. Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'od Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'po Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'przed Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Asar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Asar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'ok. Asar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'od Asar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'po Asar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'przed Asar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dei 1384'],
            ['@#DJALALI@ DEY 1384', 'Dei 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'ok. Dei 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'od Dei 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'po Dei 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'przed Dei 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'ok. Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'od Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'po Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'przed Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'ok. Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'od Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'po Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'przed Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'ok. 15 Farwardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'wyliczone na 15 Farwardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'szacowane na 15 Farwardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'przed 15 Farwardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'po 15 Farwardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'od 15 Farwardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'do 15 Farwardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'pomiędzy 15 Farwardin 1384 a 15 Ordibeheszt 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'od 15 Farwardin 1384 do 15 Ordibeheszt 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'zinterpretowane jako 15 Farwardin 1384'],
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
        self::assertSame('one i two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two i three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one lub two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two lub three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $nieceFromSis = self::female('nb', "1 FAMC @fsis@");
        $nephewFromSis = self::male('npb', "1 FAMC @fsis@");
        $nieceFromBro = self::female('nbr', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npbr', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @nb@\n1 CHIL @npb@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nbr@\n1 CHIL @npbr@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromSis, $nephewFromSis, $nieceFromBro, $nephewFromBro,
             $paternalGF, $paternalGM, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fsis, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('żona', 'mąż', [$husband, $fm, $wife]);
        self::assertRelationshipNames('były mąż', 'była żona', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('narzeczona', 'narzeczony', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('matka', 'syn', [$son, $fm, $wife]);
        self::assertRelationshipNames('ojciec', 'syn', [$son, $fm, $husband]);
        self::assertRelationshipNames('matka', 'córka', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('młodsza siostra', 'starszy brat', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('brat przyrodni', [$stepDaughter, $fd, $wife, $fm, $son]);

        // In-laws (wife's parents)
        self::assertRelationshipName('teściowa', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('teść', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // Grandparents
        self::assertRelationshipName('babcia', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('dziadek', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (fixed)
        self::assertRelationshipName('pradziadek', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('prababcia', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipName('ciotka', [$son, $fm, $husband, $fp, $sisterOfH]);
        // Father's brother = stryj
        self::assertRelationshipName('stryj', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews (sister's children)
        self::assertRelationshipName('siostrzenica', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('siostrzeniec', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Nieces and nephews (brother's children)
        self::assertRelationshipName('bratanica', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('bratanek', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
    }
}
