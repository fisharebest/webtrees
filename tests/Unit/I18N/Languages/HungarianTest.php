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
use Fisharebest\Webtrees\I18N\Languages\Hungarian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Hungarian::class)]
class HungarianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Hungarian();
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
        self::assertSame(['A', 'Á', 'B', 'C', 'CS', 'D', 'DZ', 'DZS', 'E', 'É', 'F', 'G', 'GY', 'H', 'I', 'Í', 'J', 'K', 'L', 'LY', 'M', 'N', 'NY', 'O', 'Ó', 'Ö', 'Ő', 'P', 'Q', 'R', 'S', 'SZ', 'T', 'TY', 'U', 'Ú', 'Ü', 'Ű', 'V', 'W', 'X', 'Y', 'Z', 'ZS'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('hu', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('magyar', self::language()->endonym());
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
        return 'YMD';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '2000. január 15'],
            ['JAN 2000', '2000. január'],
            ['ABT JAN 2000', '2000. január körül'],
            ['FROM JAN 2000', 'ettől: 2000. január'],
            ['AFT JAN 2000', '2000. január után'],
            ['BEF JAN 2000', '2000. január előtt'],
            ['15 FEB 2000', '2000. február 15'],
            ['FEB 2000', '2000. február'],
            ['ABT FEB 2000', '2000. február körül'],
            ['FROM FEB 2000', 'ettől: 2000. február'],
            ['AFT FEB 2000', '2000. február után'],
            ['BEF FEB 2000', '2000. február előtt'],
            ['15 MAR 2000', '2000. március 15'],
            ['MAR 2000', '2000. március'],
            ['ABT MAR 2000', '2000. március körül'],
            ['FROM MAR 2000', 'ettől: 2000. március'],
            ['AFT MAR 2000', '2000. március után'],
            ['BEF MAR 2000', '2000. március előtt'],
            ['15 APR 2000', '2000. április 15'],
            ['APR 2000', '2000. április'],
            ['ABT APR 2000', '2000. április körül'],
            ['FROM APR 2000', 'ettől: 2000. április'],
            ['AFT APR 2000', '2000. április után'],
            ['BEF APR 2000', '2000. április előtt'],
            ['15 MAY 2000', '2000. május 15'],
            ['MAY 2000', '2000. május'],
            ['ABT MAY 2000', '2000. május körül'],
            ['FROM MAY 2000', 'ettől: 2000. május'],
            ['AFT MAY 2000', '2000. május után'],
            ['BEF MAY 2000', '2000. május előtt'],
            ['15 JUN 2000', '2000. június 15'],
            ['JUN 2000', '2000. június'],
            ['ABT JUN 2000', '2000. június körül'],
            ['FROM JUN 2000', 'ettől: 2000. június'],
            ['AFT JUN 2000', '2000. június után'],
            ['BEF JUN 2000', '2000. június előtt'],
            ['15 JUL 2000', '2000. július 15'],
            ['JUL 2000', '2000. július'],
            ['ABT JUL 2000', '2000. július körül'],
            ['FROM JUL 2000', 'ettől: 2000. július'],
            ['AFT JUL 2000', '2000. július után'],
            ['BEF JUL 2000', '2000. július előtt'],
            ['15 AUG 2000', '2000. augusztus 15'],
            ['AUG 2000', '2000. augusztus'],
            ['ABT AUG 2000', '2000. augusztus körül'],
            ['FROM AUG 2000', 'ettől: 2000. augusztus'],
            ['AFT AUG 2000', '2000. augusztus után'],
            ['BEF AUG 2000', '2000. augusztus előtt'],
            ['15 SEP 2000', '2000. szeptember 15'],
            ['SEP 2000', '2000. szeptember'],
            ['ABT SEP 2000', '2000. szeptember körül'],
            ['FROM SEP 2000', 'ettől: 2000. szeptember'],
            ['AFT SEP 2000', '2000. szeptember után'],
            ['BEF SEP 2000', '2000. szeptember előtt'],
            ['15 OCT 2000', '2000. október 15'],
            ['OCT 2000', '2000. október'],
            ['ABT OCT 2000', '2000. október körül'],
            ['FROM OCT 2000', 'ettől: 2000. október'],
            ['AFT OCT 2000', '2000. október után'],
            ['BEF OCT 2000', '2000. október előtt'],
            ['15 NOV 2000', '2000. november 15'],
            ['NOV 2000', '2000. november'],
            ['ABT NOV 2000', '2000. november körül'],
            ['FROM NOV 2000', 'ettől: 2000. november'],
            ['AFT NOV 2000', '2000. november után'],
            ['BEF NOV 2000', '2000. november előtt'],
            ['15 DEC 2000', '2000. december 15'],
            ['DEC 2000', '2000. december'],
            ['ABT DEC 2000', '2000. december körül'],
            ['FROM DEC 2000', 'ettől: 2000. december'],
            ['AFT DEC 2000', '2000. december után'],
            ['BEF DEC 2000', '2000. december előtt'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', '2000. január 15 körül'],
            ['CAL 15 JAN 2000', 'számított 2000. január 15'],
            ['EST 15 JAN 2000', 'becsült 2000. január 15'],
            ['BEF 15 JAN 2000', '2000. január 15 előtt'],
            ['AFT 15 JAN 2000', '2000. január 15 után'],
            ['FROM 15 JAN 2000', 'ettől: 2000. január 15'],
            ['TO 15 JAN 2000', 'eddig: 2000. január 15'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '2000. január 15 és 2000. február 15 között'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'ettől: 2000. január 15 eddig: 2000. február 15'],
            ['INT 15 JAN 2000', 'értelmezhető 2000. január 15'],
            ['@#DJULIAN@ 15 JAN 1700', 'i. u. 1700. január 15'],
            ['@#DJULIAN@ JAN 1700', 'i. u. 1700. január'],
            ['ABT @#DJULIAN@ JAN 1700', 'i. u. 1700. január körül'],
            ['FROM @#DJULIAN@ JAN 1700', 'ettől: i. u. 1700. január'],
            ['AFT @#DJULIAN@ JAN 1700', 'i. u. 1700. január után'],
            ['BEF @#DJULIAN@ JAN 1700', 'i. u. 1700. január előtt'],
            ['@#DJULIAN@ 15 FEB 1700', 'i. u. 1700. február 15'],
            ['@#DJULIAN@ FEB 1700', 'i. u. 1700. február'],
            ['ABT @#DJULIAN@ FEB 1700', 'i. u. 1700. február körül'],
            ['FROM @#DJULIAN@ FEB 1700', 'ettől: i. u. 1700. február'],
            ['AFT @#DJULIAN@ FEB 1700', 'i. u. 1700. február után'],
            ['BEF @#DJULIAN@ FEB 1700', 'i. u. 1700. február előtt'],
            ['@#DJULIAN@ 15 MAR 1700', 'i. u. 1700. március 15'],
            ['@#DJULIAN@ MAR 1700', 'i. u. 1700. március'],
            ['ABT @#DJULIAN@ MAR 1700', 'i. u. 1700. március körül'],
            ['FROM @#DJULIAN@ MAR 1700', 'ettől: i. u. 1700. március'],
            ['AFT @#DJULIAN@ MAR 1700', 'i. u. 1700. március után'],
            ['BEF @#DJULIAN@ MAR 1700', 'i. u. 1700. március előtt'],
            ['@#DJULIAN@ 15 APR 1700', 'i. u. 1700. április 15'],
            ['@#DJULIAN@ 14 APR 1645/46', 'i. u. 1645/46. április 14'],
            ['@#DJULIAN@ APR 1700', 'i. u. 1700. április'],
            ['ABT @#DJULIAN@ APR 1700', 'i. u. 1700. április körül'],
            ['FROM @#DJULIAN@ APR 1700', 'ettől: i. u. 1700. április'],
            ['AFT @#DJULIAN@ APR 1700', 'i. u. 1700. április után'],
            ['BEF @#DJULIAN@ APR 1700', 'i. u. 1700. április előtt'],
            ['@#DJULIAN@ 15 MAY 1700', 'i. u. 1700. május 15'],
            ['@#DJULIAN@ MAY 1700', 'i. u. 1700. május'],
            ['ABT @#DJULIAN@ MAY 1700', 'i. u. 1700. május körül'],
            ['FROM @#DJULIAN@ MAY 1700', 'ettől: i. u. 1700. május'],
            ['AFT @#DJULIAN@ MAY 1700', 'i. u. 1700. május után'],
            ['BEF @#DJULIAN@ MAY 1700', 'i. u. 1700. május előtt'],
            ['@#DJULIAN@ 15 JUN 1700', 'i. u. 1700. június 15'],
            ['@#DJULIAN@ JUN 1700', 'i. u. 1700. június'],
            ['ABT @#DJULIAN@ JUN 1700', 'i. u. 1700. június körül'],
            ['FROM @#DJULIAN@ JUN 1700', 'ettől: i. u. 1700. június'],
            ['AFT @#DJULIAN@ JUN 1700', 'i. u. 1700. június után'],
            ['BEF @#DJULIAN@ JUN 1700', 'i. u. 1700. június előtt'],
            ['@#DJULIAN@ 15 JUL 1700', 'i. u. 1700. július 15'],
            ['@#DJULIAN@ JUL 1700', 'i. u. 1700. július'],
            ['ABT @#DJULIAN@ JUL 1700', 'i. u. 1700. július körül'],
            ['FROM @#DJULIAN@ JUL 1700', 'ettől: i. u. 1700. július'],
            ['AFT @#DJULIAN@ JUL 1700', 'i. u. 1700. július után'],
            ['BEF @#DJULIAN@ JUL 1700', 'i. u. 1700. július előtt'],
            ['@#DJULIAN@ 15 AUG 1700', 'i. u. 1700. augusztus 15'],
            ['@#DJULIAN@ AUG 1700', 'i. u. 1700. augusztus'],
            ['ABT @#DJULIAN@ AUG 1700', 'i. u. 1700. augusztus körül'],
            ['FROM @#DJULIAN@ AUG 1700', 'ettől: i. u. 1700. augusztus'],
            ['AFT @#DJULIAN@ AUG 1700', 'i. u. 1700. augusztus után'],
            ['BEF @#DJULIAN@ AUG 1700', 'i. u. 1700. augusztus előtt'],
            ['@#DJULIAN@ 15 SEP 1700', 'i. u. 1700. szeptember 15'],
            ['@#DJULIAN@ SEP 1700', 'i. u. 1700. szeptember'],
            ['ABT @#DJULIAN@ SEP 1700', 'i. u. 1700. szeptember körül'],
            ['FROM @#DJULIAN@ SEP 1700', 'ettől: i. u. 1700. szeptember'],
            ['AFT @#DJULIAN@ SEP 1700', 'i. u. 1700. szeptember után'],
            ['BEF @#DJULIAN@ SEP 1700', 'i. u. 1700. szeptember előtt'],
            ['@#DJULIAN@ 15 OCT 1700', 'i. u. 1700. október 15'],
            ['@#DJULIAN@ OCT 1700', 'i. u. 1700. október'],
            ['ABT @#DJULIAN@ OCT 1700', 'i. u. 1700. október körül'],
            ['FROM @#DJULIAN@ OCT 1700', 'ettől: i. u. 1700. október'],
            ['AFT @#DJULIAN@ OCT 1700', 'i. u. 1700. október után'],
            ['BEF @#DJULIAN@ OCT 1700', 'i. u. 1700. október előtt'],
            ['@#DJULIAN@ 15 NOV 1700', 'i. u. 1700. november 15'],
            ['@#DJULIAN@ NOV 1700', 'i. u. 1700. november'],
            ['ABT @#DJULIAN@ NOV 1700', 'i. u. 1700. november körül'],
            ['FROM @#DJULIAN@ NOV 1700', 'ettől: i. u. 1700. november'],
            ['AFT @#DJULIAN@ NOV 1700', 'i. u. 1700. november után'],
            ['BEF @#DJULIAN@ NOV 1700', 'i. u. 1700. november előtt'],
            ['@#DJULIAN@ 15 DEC 1700', 'i. u. 1700. december 15'],
            ['@#DJULIAN@ DEC 1700', 'i. u. 1700. december'],
            ['ABT @#DJULIAN@ DEC 1700', 'i. u. 1700. december körül'],
            ['FROM @#DJULIAN@ DEC 1700', 'ettől: i. u. 1700. december'],
            ['AFT @#DJULIAN@ DEC 1700', 'i. u. 1700. december után'],
            ['BEF @#DJULIAN@ DEC 1700', 'i. u. 1700. december előtt'],
            ['@#DJULIAN@ 1700', 'i. u. 1700'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'i. u. 1700. január 15 körül'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'számított i. u. 1700. január 15'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'becsült i. u. 1700. január 15'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'i. u. 1700. január 15 előtt'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'i. u. 1700. január 15 után'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'ettől: i. u. 1700. január 15'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'eddig: i. u. 1700. január 15'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'i. u. 1700. január 15 és i. u. 1700. február 15 között'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'ettől: i. u. 1700. január 15 eddig: i. u. 1700. február 15'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'értelmezhető i. u. 1700. január 15'],
            ['@#DHEBREW@ 15 TSH 5765', '5765. Tisri 15'],
            ['@#DHEBREW@ TSH 5765', '5765. Tisri'],
            ['ABT @#DHEBREW@ TSH 5765', '5765. Tisri körül'],
            ['FROM @#DHEBREW@ TSH 5765', 'ettől: 5765. Tisri'],
            ['AFT @#DHEBREW@ TSH 5765', '5765. Tisri után'],
            ['BEF @#DHEBREW@ TSH 5765', '5765. Tisri előtt'],
            ['@#DHEBREW@ 15 CSH 5765', '5765. Hesván 15'],
            ['@#DHEBREW@ CSH 5765', '5765. Hesván'],
            ['ABT @#DHEBREW@ CSH 5765', '5765. Hesván körül'],
            ['FROM @#DHEBREW@ CSH 5765', 'ettől: 5765. Hesván'],
            ['AFT @#DHEBREW@ CSH 5765', '5765. Hesván után'],
            ['BEF @#DHEBREW@ CSH 5765', '5765. Hesván előtt'],
            ['@#DHEBREW@ 15 KSL 5765', '5765. Kiszlév 15'],
            ['@#DHEBREW@ KSL 5765', '5765. Kiszlév'],
            ['ABT @#DHEBREW@ KSL 5765', '5765. Kiszlév körül'],
            ['FROM @#DHEBREW@ KSL 5765', 'ettől: 5765. Kiszlév'],
            ['AFT @#DHEBREW@ KSL 5765', '5765. Kiszlév után'],
            ['BEF @#DHEBREW@ KSL 5765', '5765. Kiszlév előtt'],
            ['@#DHEBREW@ 15 TVT 5765', '5765. Tévész 15'],
            ['@#DHEBREW@ TVT 5765', '5765. Tévész'],
            ['ABT @#DHEBREW@ TVT 5765', '5765. Tévész körül'],
            ['FROM @#DHEBREW@ TVT 5765', 'ettől: 5765. Tévész'],
            ['AFT @#DHEBREW@ TVT 5765', '5765. Tévész után'],
            ['BEF @#DHEBREW@ TVT 5765', '5765. Tévész előtt'],
            ['@#DHEBREW@ 15 SHV 5765', '5765. Svát 15'],
            ['@#DHEBREW@ SHV 5765', '5765. Svát'],
            ['ABT @#DHEBREW@ SHV 5765', '5765. Svát körül'],
            ['FROM @#DHEBREW@ SHV 5765', 'ettől: 5765. Svát'],
            ['AFT @#DHEBREW@ SHV 5765', '5765. Svát után'],
            ['BEF @#DHEBREW@ SHV 5765', '5765. Svát előtt'],
            ['@#DHEBREW@ 15 ADR 5765', '5765. Ádár risón 15'],
            ['@#DHEBREW@ ADR 5765', '5765. Ádár risón'],
            ['ABT @#DHEBREW@ ADR 5765', '5765. Ádár risón körül'],
            ['FROM @#DHEBREW@ ADR 5765', 'ettől: 5765. Ádár risón'],
            ['AFT @#DHEBREW@ ADR 5765', '5765. Ádár risón után'],
            ['BEF @#DHEBREW@ ADR 5765', '5765. Ádár risón előtt'],
            ['@#DHEBREW@ 15 ADS 5765', '5765. dr sni 15'],
            ['@#DHEBREW@ ADS 5765', '5765. dr sni'],
            ['ABT @#DHEBREW@ ADS 5765', '5765. dr sni körül'],
            ['FROM @#DHEBREW@ ADS 5765', 'ettől: 5765. dr sni'],
            ['AFT @#DHEBREW@ ADS 5765', '5765. dr sni után'],
            ['BEF @#DHEBREW@ ADS 5765', '5765. dr sni előtt'],
            ['@#DHEBREW@ 15 NSN 5765', '5765. Niszán 15'],
            ['@#DHEBREW@ NSN 5765', '5765. Niszán'],
            ['ABT @#DHEBREW@ NSN 5765', '5765. Niszán körül'],
            ['FROM @#DHEBREW@ NSN 5765', 'ettől: 5765. Niszán'],
            ['AFT @#DHEBREW@ NSN 5765', '5765. Niszán után'],
            ['BEF @#DHEBREW@ NSN 5765', '5765. Niszán előtt'],
            ['@#DHEBREW@ 15 IYR 5765', '5765. Ijár 15'],
            ['@#DHEBREW@ IYR 5765', '5765. Ijár'],
            ['ABT @#DHEBREW@ IYR 5765', '5765. Ijár körül'],
            ['FROM @#DHEBREW@ IYR 5765', 'ettől: 5765. Ijár'],
            ['AFT @#DHEBREW@ IYR 5765', '5765. Ijár után'],
            ['BEF @#DHEBREW@ IYR 5765', '5765. Ijár előtt'],
            ['@#DHEBREW@ 15 SVN 5765', '5765. Sziván 15'],
            ['@#DHEBREW@ SVN 5765', '5765. Sziván'],
            ['ABT @#DHEBREW@ SVN 5765', '5765. Sziván körül'],
            ['FROM @#DHEBREW@ SVN 5765', 'ettől: 5765. Sziván'],
            ['AFT @#DHEBREW@ SVN 5765', '5765. Sziván után'],
            ['BEF @#DHEBREW@ SVN 5765', '5765. Sziván előtt'],
            ['@#DHEBREW@ 15 TMZ 5765', '5765. Tamuz 15'],
            ['@#DHEBREW@ TMZ 5765', '5765. Tamuz'],
            ['ABT @#DHEBREW@ TMZ 5765', '5765. Tamuz körül'],
            ['FROM @#DHEBREW@ TMZ 5765', 'ettől: 5765. Tamuz'],
            ['AFT @#DHEBREW@ TMZ 5765', '5765. Tamuz után'],
            ['BEF @#DHEBREW@ TMZ 5765', '5765. Tamuz előtt'],
            ['@#DHEBREW@ 15 AAV 5765', '5765. Áv 15'],
            ['@#DHEBREW@ AAV 5765', '5765. Áv'],
            ['ABT @#DHEBREW@ AAV 5765', '5765. Áv körül'],
            ['FROM @#DHEBREW@ AAV 5765', 'ettől: 5765. Áv'],
            ['AFT @#DHEBREW@ AAV 5765', '5765. Áv után'],
            ['BEF @#DHEBREW@ AAV 5765', '5765. Áv előtt'],
            ['@#DHEBREW@ 15 ELL 5765', '5765. Elul 15'],
            ['@#DHEBREW@ ELL 5765', '5765. Elul'],
            ['ABT @#DHEBREW@ ELL 5765', '5765. Elul körül'],
            ['FROM @#DHEBREW@ ELL 5765', 'ettől: 5765. Elul'],
            ['AFT @#DHEBREW@ ELL 5765', '5765. Elul után'],
            ['BEF @#DHEBREW@ ELL 5765', '5765. Elul előtt'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', '5765. Tisri 15 körül'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'számított 5765. Tisri 15'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'becsült 5765. Tisri 15'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '5765. Tisri 15 előtt'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '5765. Tisri 15 után'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'ettől: 5765. Tisri 15'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'eddig: 5765. Tisri 15'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '5765. Tisri 15 és 5765. Hesván 15 között'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'ettől: 5765. Tisri 15 eddig: 5765. Hesván 15'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'értelmezhető 5765. Tisri 15'],
            ['@#DFRENCH R@ 15 VEND 12', 'An XII. Szüret hava 15'],
            ['@#DFRENCH R@ VEND 12', 'An XII. Szüret hava'],
            ['ABT @#DFRENCH R@ VEND 12', 'An XII. Szüret hava körül'],
            ['FROM @#DFRENCH R@ VEND 12', 'ettől: An XII. Szüret hava'],
            ['AFT @#DFRENCH R@ VEND 12', 'An XII. Szüret hava után'],
            ['BEF @#DFRENCH R@ VEND 12', 'An XII. Szüret hava előtt'],
            ['@#DFRENCH R@ 15 BRUM 12', 'An XII. Köd hava 15'],
            ['@#DFRENCH R@ BRUM 12', 'An XII. Köd hava'],
            ['ABT @#DFRENCH R@ BRUM 12', 'An XII. Köd hava körül'],
            ['FROM @#DFRENCH R@ BRUM 12', 'ettől: An XII. Köd hava'],
            ['AFT @#DFRENCH R@ BRUM 12', 'An XII. Köd hava után'],
            ['BEF @#DFRENCH R@ BRUM 12', 'An XII. Köd hava előtt'],
            ['@#DFRENCH R@ 15 FRIM 12', 'An XII. Dér hava 15'],
            ['@#DFRENCH R@ FRIM 12', 'An XII. Dér hava'],
            ['ABT @#DFRENCH R@ FRIM 12', 'An XII. Dér hava körül'],
            ['FROM @#DFRENCH R@ FRIM 12', 'ettől: An XII. Dér hava'],
            ['AFT @#DFRENCH R@ FRIM 12', 'An XII. Dér hava után'],
            ['BEF @#DFRENCH R@ FRIM 12', 'An XII. Dér hava előtt'],
            ['@#DFRENCH R@ 15 NIVO 12', 'An XII. Hó hava 15'],
            ['@#DFRENCH R@ NIVO 12', 'An XII. Hó hava'],
            ['ABT @#DFRENCH R@ NIVO 12', 'An XII. Hó hava körül'],
            ['FROM @#DFRENCH R@ NIVO 12', 'ettől: An XII. Hó hava'],
            ['AFT @#DFRENCH R@ NIVO 12', 'An XII. Hó hava után'],
            ['BEF @#DFRENCH R@ NIVO 12', 'An XII. Hó hava előtt'],
            ['@#DFRENCH R@ 15 PLUV 12', 'An XII. Eső hava 15'],
            ['@#DFRENCH R@ PLUV 12', 'An XII. Eső hava'],
            ['ABT @#DFRENCH R@ PLUV 12', 'An XII. Eső hava körül'],
            ['FROM @#DFRENCH R@ PLUV 12', 'ettől: An XII. Eső hava'],
            ['AFT @#DFRENCH R@ PLUV 12', 'An XII. Eső hava után'],
            ['BEF @#DFRENCH R@ PLUV 12', 'An XII. Eső hava előtt'],
            ['@#DFRENCH R@ 15 VENT 12', 'An XII. Szél hava 15'],
            ['@#DFRENCH R@ VENT 12', 'An XII. Szél hava'],
            ['ABT @#DFRENCH R@ VENT 12', 'An XII. Szél hava körül'],
            ['FROM @#DFRENCH R@ VENT 12', 'ettől: An XII. Szél hava'],
            ['AFT @#DFRENCH R@ VENT 12', 'An XII. Szél hava után'],
            ['BEF @#DFRENCH R@ VENT 12', 'An XII. Szél hava előtt'],
            ['@#DFRENCH R@ 15 GERM 12', 'An XII. Sarjadás hava 15'],
            ['@#DFRENCH R@ GERM 12', 'An XII. Sarjadás hava'],
            ['ABT @#DFRENCH R@ GERM 12', 'An XII. Sarjadás hava körül'],
            ['FROM @#DFRENCH R@ GERM 12', 'ettől: An XII. Sarjadás hava'],
            ['AFT @#DFRENCH R@ GERM 12', 'An XII. Sarjadás hava után'],
            ['BEF @#DFRENCH R@ GERM 12', 'An XII. Sarjadás hava előtt'],
            ['@#DFRENCH R@ 15 FLOR 12', 'An XII. Virágzás hava 15'],
            ['@#DFRENCH R@ FLOR 12', 'An XII. Virágzás hava'],
            ['ABT @#DFRENCH R@ FLOR 12', 'An XII. Virágzás hava körül'],
            ['FROM @#DFRENCH R@ FLOR 12', 'ettől: An XII. Virágzás hava'],
            ['AFT @#DFRENCH R@ FLOR 12', 'An XII. Virágzás hava után'],
            ['BEF @#DFRENCH R@ FLOR 12', 'An XII. Virágzás hava előtt'],
            ['@#DFRENCH R@ 15 PRAI 12', 'An XII. Rét hava 15'],
            ['@#DFRENCH R@ PRAI 12', 'An XII. Rét hava'],
            ['ABT @#DFRENCH R@ PRAI 12', 'An XII. Rét hava körül'],
            ['FROM @#DFRENCH R@ PRAI 12', 'ettől: An XII. Rét hava'],
            ['AFT @#DFRENCH R@ PRAI 12', 'An XII. Rét hava után'],
            ['BEF @#DFRENCH R@ PRAI 12', 'An XII. Rét hava előtt'],
            ['@#DFRENCH R@ 15 MESS 12', 'An XII. Aratás hónapja 15'],
            ['@#DFRENCH R@ MESS 12', 'An XII. Aratás hónapja'],
            ['ABT @#DFRENCH R@ MESS 12', 'An XII. Aratás hónapja körül'],
            ['FROM @#DFRENCH R@ MESS 12', 'ettől: An XII. Aratás hónapja'],
            ['AFT @#DFRENCH R@ MESS 12', 'An XII. Aratás hónapja után'],
            ['BEF @#DFRENCH R@ MESS 12', 'An XII. Aratás hónapja előtt'],
            ['@#DFRENCH R@ 15 THER 12', 'An XII. Hőség hónapja 15'],
            ['@#DFRENCH R@ THER 12', 'An XII. Hőség hónapja'],
            ['ABT @#DFRENCH R@ THER 12', 'An XII. Hőség hónapja körül'],
            ['FROM @#DFRENCH R@ THER 12', 'ettől: An XII. Hőség hónapja'],
            ['AFT @#DFRENCH R@ THER 12', 'An XII. Hőség hónapja után'],
            ['BEF @#DFRENCH R@ THER 12', 'An XII. Hőség hónapja előtt'],
            ['@#DFRENCH R@ 15 FRUC 12', 'An XII. Gyümölcs hava 15'],
            ['@#DFRENCH R@ FRUC 12', 'An XII. Gyümölcs hava'],
            ['ABT @#DFRENCH R@ FRUC 12', 'An XII. Gyümölcs hava körül'],
            ['FROM @#DFRENCH R@ FRUC 12', 'ettől: An XII. Gyümölcs hava'],
            ['AFT @#DFRENCH R@ FRUC 12', 'An XII. Gyümölcs hava után'],
            ['BEF @#DFRENCH R@ FRUC 12', 'An XII. Gyümölcs hava előtt'],
            ['@#DFRENCH R@ 15 COMP 12', 'An XII. extra napok 15'],
            ['@#DFRENCH R@ COMP 12', 'An XII. extra napok'],
            ['ABT @#DFRENCH R@ COMP 12', 'An XII. extra napok körül'],
            ['FROM @#DFRENCH R@ COMP 12', 'ettől: An XII. extra napok'],
            ['AFT @#DFRENCH R@ COMP 12', 'An XII. extra napok után'],
            ['BEF @#DFRENCH R@ COMP 12', 'An XII. extra napok előtt'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'An XII. Szüret hava 15 körül'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'számított An XII. Szüret hava 15'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'becsült An XII. Szüret hava 15'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'An XII. Szüret hava 15 előtt'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'An XII. Szüret hava 15 után'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'ettől: An XII. Szüret hava 15'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'eddig: An XII. Szüret hava 15'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'An XII. Szüret hava 15 és An XII. Köd hava 15 között'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'ettől: An XII. Szüret hava 15 eddig: An XII. Köd hava 15'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'értelmezhető An XII. Szüret hava 15'],
            ['@#DHIJRI@ 15 MUHAR 1425', '1425. Moharrem 15'],
            ['@#DHIJRI@ MUHAR 1425', '1425. Moharrem'],
            ['ABT @#DHIJRI@ MUHAR 1425', '1425. Moharrem körül'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'ettől: 1425. Moharrem'],
            ['AFT @#DHIJRI@ MUHAR 1425', '1425. Moharrem után'],
            ['BEF @#DHIJRI@ MUHAR 1425', '1425. Moharrem előtt'],
            ['@#DHIJRI@ 15 SAFAR 1425', '1425. Szafar 15'],
            ['@#DHIJRI@ SAFAR 1425', '1425. Szafar'],
            ['ABT @#DHIJRI@ SAFAR 1425', '1425. Szafar körül'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'ettől: 1425. Szafar'],
            ['AFT @#DHIJRI@ SAFAR 1425', '1425. Szafar után'],
            ['BEF @#DHIJRI@ SAFAR 1425', '1425. Szafar előtt'],
            ['@#DHIJRI@ 15 RABIA 1425', '1425. Rabi’ al-awwal 15'],
            ['@#DHIJRI@ RABIA 1425', '1425. Rabi’ al-awwal'],
            ['ABT @#DHIJRI@ RABIA 1425', '1425. Rabi’ al-awwal körül'],
            ['FROM @#DHIJRI@ RABIA 1425', 'ettől: 1425. Rabi’ al-awwal'],
            ['AFT @#DHIJRI@ RABIA 1425', '1425. Rabi’ al-awwal után'],
            ['BEF @#DHIJRI@ RABIA 1425', '1425. Rabi’ al-awwal előtt'],
            ['@#DHIJRI@ 15 RABIT 1425', '1425. Rabi’ al-thani 15'],
            ['@#DHIJRI@ RABIT 1425', '1425. Rabi’ al-thani'],
            ['ABT @#DHIJRI@ RABIT 1425', '1425. Rabi’ al-thani körül'],
            ['FROM @#DHIJRI@ RABIT 1425', 'ettől: 1425. Rabi’ al-thani'],
            ['AFT @#DHIJRI@ RABIT 1425', '1425. Rabi’ al-thani után'],
            ['BEF @#DHIJRI@ RABIT 1425', '1425. Rabi’ al-thani előtt'],
            ['@#DHIJRI@ 15 JUMAA 1425', '1425. Dsemádi el avvel 15'],
            ['@#DHIJRI@ JUMAA 1425', '1425. Dsemádi el avvel'],
            ['ABT @#DHIJRI@ JUMAA 1425', '1425. Dsemádi el avvel körül'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'ettől: 1425. Dsemádi el avvel'],
            ['AFT @#DHIJRI@ JUMAA 1425', '1425. Dsemádi el avvel után'],
            ['BEF @#DHIJRI@ JUMAA 1425', '1425. Dsemádi el avvel előtt'],
            ['@#DHIJRI@ 15 JUMAT 1425', '1425. Dsemádi el accher 15'],
            ['@#DHIJRI@ JUMAT 1425', '1425. Dsemádi el accher'],
            ['ABT @#DHIJRI@ JUMAT 1425', '1425. Dsemádi el accher körül'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'ettől: 1425. Dsemádi el accher'],
            ['AFT @#DHIJRI@ JUMAT 1425', '1425. Dsemádi el accher után'],
            ['BEF @#DHIJRI@ JUMAT 1425', '1425. Dsemádi el accher előtt'],
            ['@#DHIJRI@ 15 RAJAB 1425', '1425. Redseb 15'],
            ['@#DHIJRI@ RAJAB 1425', '1425. Redseb'],
            ['ABT @#DHIJRI@ RAJAB 1425', '1425. Redseb körül'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'ettől: 1425. Redseb'],
            ['AFT @#DHIJRI@ RAJAB 1425', '1425. Redseb után'],
            ['BEF @#DHIJRI@ RAJAB 1425', '1425. Redseb előtt'],
            ['@#DHIJRI@ 15 SHAAB 1425', '1425. Sabán 15'],
            ['@#DHIJRI@ SHAAB 1425', '1425. Sabán'],
            ['ABT @#DHIJRI@ SHAAB 1425', '1425. Sabán körül'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'ettől: 1425. Sabán'],
            ['AFT @#DHIJRI@ SHAAB 1425', '1425. Sabán után'],
            ['BEF @#DHIJRI@ SHAAB 1425', '1425. Sabán előtt'],
            ['@#DHIJRI@ 15 RAMAD 1425', '1425. Ramadán 15'],
            ['@#DHIJRI@ RAMAD 1425', '1425. Ramadán'],
            ['ABT @#DHIJRI@ RAMAD 1425', '1425. Ramadán körül'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'ettől: 1425. Ramadán'],
            ['AFT @#DHIJRI@ RAMAD 1425', '1425. Ramadán után'],
            ['BEF @#DHIJRI@ RAMAD 1425', '1425. Ramadán előtt'],
            ['@#DHIJRI@ 15 SHAWW 1425', '1425. Sevvál 15'],
            ['@#DHIJRI@ SHAWW 1425', '1425. Sevvál'],
            ['ABT @#DHIJRI@ SHAWW 1425', '1425. Sevvál körül'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'ettől: 1425. Sevvál'],
            ['AFT @#DHIJRI@ SHAWW 1425', '1425. Sevvál után'],
            ['BEF @#DHIJRI@ SHAWW 1425', '1425. Sevvál előtt'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '1425. Dsül kade 15'],
            ['@#DHIJRI@ DHUAQ 1425', '1425. Dsül kade'],
            ['ABT @#DHIJRI@ DHUAQ 1425', '1425. Dsül kade körül'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'ettől: 1425. Dsül kade'],
            ['AFT @#DHIJRI@ DHUAQ 1425', '1425. Dsül kade után'],
            ['BEF @#DHIJRI@ DHUAQ 1425', '1425. Dsül kade előtt'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', '1425 körül'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'ettől: 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', '1425 után'],
            ['BEF @#DHIJRI@ DHUAL 1425', '1425 előtt'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', '1425. Moharrem 15 körül'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'számított 1425. Moharrem 15'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'becsült 1425. Moharrem 15'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '1425. Moharrem 15 előtt'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '1425. Moharrem 15 után'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'ettől: 1425. Moharrem 15'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'eddig: 1425. Moharrem 15'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '1425. Moharrem 15 és 1425. Szafar 15 között'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'ettől: 1425. Moharrem 15 eddig: 1425. Szafar 15'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'értelmezhető 1425. Moharrem 15'],
            ['@#DJALALI@ 15 FARVA 1384', '1384. Farvardin 15'],
            ['@#DJALALI@ FARVA 1384', '1384. Farvardin'],
            ['ABT @#DJALALI@ FARVA 1384', '1384. Farvardin körül'],
            ['FROM @#DJALALI@ FARVA 1384', 'ettől: 1384. Farvardin'],
            ['AFT @#DJALALI@ FARVA 1384', '1384. Farvardin után'],
            ['BEF @#DJALALI@ FARVA 1384', '1384. Farvardin előtt'],
            ['@#DJALALI@ 15 ORDIB 1384', '1384. Ordibehesht 15'],
            ['@#DJALALI@ ORDIB 1384', '1384. Ordibehesht'],
            ['ABT @#DJALALI@ ORDIB 1384', '1384. Ordibehesht körül'],
            ['FROM @#DJALALI@ ORDIB 1384', 'ettől: 1384. Ordibehesht'],
            ['AFT @#DJALALI@ ORDIB 1384', '1384. Ordibehesht után'],
            ['BEF @#DJALALI@ ORDIB 1384', '1384. Ordibehesht előtt'],
            ['@#DJALALI@ 15 KHORD 1384', '1384. Khordad 15'],
            ['@#DJALALI@ KHORD 1384', '1384. Khordad'],
            ['ABT @#DJALALI@ KHORD 1384', '1384. Khordad körül'],
            ['FROM @#DJALALI@ KHORD 1384', 'ettől: 1384. Khordad'],
            ['AFT @#DJALALI@ KHORD 1384', '1384. Khordad után'],
            ['BEF @#DJALALI@ KHORD 1384', '1384. Khordad előtt'],
            ['@#DJALALI@ 15 TIR 1384', '1384. Tir 15'],
            ['@#DJALALI@ TIR 1384', '1384. Tir'],
            ['ABT @#DJALALI@ TIR 1384', '1384. Tir körül'],
            ['FROM @#DJALALI@ TIR 1384', 'ettől: 1384. Tir'],
            ['AFT @#DJALALI@ TIR 1384', '1384. Tir után'],
            ['BEF @#DJALALI@ TIR 1384', '1384. Tir előtt'],
            ['@#DJALALI@ 15 MORDA 1384', '1384. Mordad 15'],
            ['@#DJALALI@ MORDA 1384', '1384. Mordad'],
            ['ABT @#DJALALI@ MORDA 1384', '1384. Mordad körül'],
            ['FROM @#DJALALI@ MORDA 1384', 'ettől: 1384. Mordad'],
            ['AFT @#DJALALI@ MORDA 1384', '1384. Mordad után'],
            ['BEF @#DJALALI@ MORDA 1384', '1384. Mordad előtt'],
            ['@#DJALALI@ 15 SHAHR 1384', '1384. Shahrivar 15'],
            ['@#DJALALI@ SHAHR 1384', '1384. Shahrivar'],
            ['ABT @#DJALALI@ SHAHR 1384', '1384. Shahrivar körül'],
            ['FROM @#DJALALI@ SHAHR 1384', 'ettől: 1384. Shahrivar'],
            ['AFT @#DJALALI@ SHAHR 1384', '1384. Shahrivar után'],
            ['BEF @#DJALALI@ SHAHR 1384', '1384. Shahrivar előtt'],
            ['@#DJALALI@ 15 MEHR 1384', '1384. Mehr 15'],
            ['@#DJALALI@ MEHR 1384', '1384. Mehr'],
            ['ABT @#DJALALI@ MEHR 1384', '1384. Mehr körül'],
            ['FROM @#DJALALI@ MEHR 1384', 'ettől: 1384. Mehr'],
            ['AFT @#DJALALI@ MEHR 1384', '1384. Mehr után'],
            ['BEF @#DJALALI@ MEHR 1384', '1384. Mehr előtt'],
            ['@#DJALALI@ 15 ABAN 1384', '1384. Aban 15'],
            ['@#DJALALI@ ABAN 1384', '1384. Aban'],
            ['ABT @#DJALALI@ ABAN 1384', '1384. Aban körül'],
            ['FROM @#DJALALI@ ABAN 1384', 'ettől: 1384. Aban'],
            ['AFT @#DJALALI@ ABAN 1384', '1384. Aban után'],
            ['BEF @#DJALALI@ ABAN 1384', '1384. Aban előtt'],
            ['@#DJALALI@ 15 AZAR 1384', '1384. Azar 15'],
            ['@#DJALALI@ AZAR 1384', '1384. Azar'],
            ['ABT @#DJALALI@ AZAR 1384', '1384. Azar körül'],
            ['FROM @#DJALALI@ AZAR 1384', 'ettől: 1384. Azar'],
            ['AFT @#DJALALI@ AZAR 1384', '1384. Azar után'],
            ['BEF @#DJALALI@ AZAR 1384', '1384. Azar előtt'],
            ['@#DJALALI@ 15 DEY 1384', '1384. Dey 15'],
            ['@#DJALALI@ DEY 1384', '1384. Dey'],
            ['ABT @#DJALALI@ DEY 1384', '1384. Dey körül'],
            ['FROM @#DJALALI@ DEY 1384', 'ettől: 1384. Dey'],
            ['AFT @#DJALALI@ DEY 1384', '1384. Dey után'],
            ['BEF @#DJALALI@ DEY 1384', '1384. Dey előtt'],
            ['@#DJALALI@ 15 BAHMA 1384', '1384. Bahman 15'],
            ['@#DJALALI@ BAHMA 1384', '1384. Bahman'],
            ['ABT @#DJALALI@ BAHMA 1384', '1384. Bahman körül'],
            ['FROM @#DJALALI@ BAHMA 1384', 'ettől: 1384. Bahman'],
            ['AFT @#DJALALI@ BAHMA 1384', '1384. Bahman után'],
            ['BEF @#DJALALI@ BAHMA 1384', '1384. Bahman előtt'],
            ['@#DJALALI@ 15 ESFAN 1384', '1384. Esfand 15'],
            ['@#DJALALI@ ESFAN 1384', '1384. Esfand'],
            ['ABT @#DJALALI@ ESFAN 1384', '1384. Esfand körül'],
            ['FROM @#DJALALI@ ESFAN 1384', 'ettől: 1384. Esfand'],
            ['AFT @#DJALALI@ ESFAN 1384', '1384. Esfand után'],
            ['BEF @#DJALALI@ ESFAN 1384', '1384. Esfand előtt'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', '1384. Farvardin 15 körül'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'számított 1384. Farvardin 15'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'becsült 1384. Farvardin 15'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '1384. Farvardin 15 előtt'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '1384. Farvardin 15 után'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'ettől: 1384. Farvardin 15'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'eddig: 1384. Farvardin 15'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '1384. Farvardin 15 és 1384. Ordibehesht 15 között'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'ettől: 1384. Farvardin 15 eddig: 1384. Ordibehesht 15'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'értelmezhető 1384. Farvardin 15'],
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
        self::assertSame('one és two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two és three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one vagy two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two vagy three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $child = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@\n1 BIRT\n2 DATE 1968");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $cousinFemale = self::female('cf', "1 FAMC @fbro@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('feleség', 'férj', [$husband, $fm, $wife]);
        self::assertRelationshipNames('volt férj', 'volt feleség', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('menyasszony', 'vőlegény', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('anya', 'fia', [$son, $fm, $wife]);
        self::assertRelationshipNames('apa', 'fia', [$son, $fm, $husband]);
        self::assertRelationshipNames('anya', 'lánya', [$daughter, $fm, $wife]);

        // Siblings (elder/younger)
        self::assertRelationshipNames('húg', 'báty', [$son, $fm, $daughter]);
        self::assertRelationshipNames('báty', 'húg', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('féltestvér', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('mostohaapa', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('mostohalánya', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('anyós', 'vő', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('após', 'vő', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('meny', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('nagymama', 'unoka', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('nagypapa', 'unoka', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipNames('nagymama', 'unoka', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('nagypapa', 'unoka', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('dédnagypapa', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('dédnagymama', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('nagynéni', 'unokaöcs', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('nagybácsi', 'unokaöcs', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('unokahúg', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('unokaöcs', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('unokatestvér', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('unokatestvér', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }
}
