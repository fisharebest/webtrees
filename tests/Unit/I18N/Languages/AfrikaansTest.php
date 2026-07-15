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
use Fisharebest\Webtrees\I18N\Languages\Afrikaans;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Afrikaans::class)]
class AfrikaansTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Afrikaans();
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
        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('af', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Afrikaans', self::language()->endonym());
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
            ['15 JAN 2000', '15 Januarie 2000'],
            ['JAN 2000', 'Januarie 2000'],
            ['ABT JAN 2000', 'op ongeveer Januarie 2000'],
            ['FROM JAN 2000', 'vanaf Januarie 2000'],
            ['AFT JAN 2000', 'na Januarie 2000'],
            ['BEF JAN 2000', 'voor Januarie 2000'],
            ['15 FEB 2000', '15 Februarie 2000'],
            ['FEB 2000', 'Februarie 2000'],
            ['ABT FEB 2000', 'op ongeveer Februarie 2000'],
            ['FROM FEB 2000', 'vanaf Februarie 2000'],
            ['AFT FEB 2000', 'na Februarie 2000'],
            ['BEF FEB 2000', 'voor Februarie 2000'],
            ['15 MAR 2000', '15 Maart 2000'],
            ['MAR 2000', 'Maart 2000'],
            ['ABT MAR 2000', 'op ongeveer Maart 2000'],
            ['FROM MAR 2000', 'vanaf Maart 2000'],
            ['AFT MAR 2000', 'na Maart 2000'],
            ['BEF MAR 2000', 'voor Maart 2000'],
            ['15 APR 2000', '15 April 2000'],
            ['APR 2000', 'April 2000'],
            ['ABT APR 2000', 'op ongeveer April 2000'],
            ['FROM APR 2000', 'vanaf April 2000'],
            ['AFT APR 2000', 'na April 2000'],
            ['BEF APR 2000', 'voor April 2000'],
            ['15 MAY 2000', '15 Mei 2000'],
            ['MAY 2000', 'Mei 2000'],
            ['ABT MAY 2000', 'op ongeveer Mei 2000'],
            ['FROM MAY 2000', 'vanaf Mei 2000'],
            ['AFT MAY 2000', 'na Mei 2000'],
            ['BEF MAY 2000', 'voor Mei 2000'],
            ['15 JUN 2000', '15 Junie 2000'],
            ['JUN 2000', 'Junie 2000'],
            ['ABT JUN 2000', 'op ongeveer Junie 2000'],
            ['FROM JUN 2000', 'vanaf Junie 2000'],
            ['AFT JUN 2000', 'na Junie 2000'],
            ['BEF JUN 2000', 'voor Junie 2000'],
            ['15 JUL 2000', '15 Julie 2000'],
            ['JUL 2000', 'Julie 2000'],
            ['ABT JUL 2000', 'op ongeveer Julie 2000'],
            ['FROM JUL 2000', 'vanaf Julie 2000'],
            ['AFT JUL 2000', 'na Julie 2000'],
            ['BEF JUL 2000', 'voor Julie 2000'],
            ['15 AUG 2000', '15 Augustus 2000'],
            ['AUG 2000', 'Augustus 2000'],
            ['ABT AUG 2000', 'op ongeveer Augustus 2000'],
            ['FROM AUG 2000', 'vanaf Augustus 2000'],
            ['AFT AUG 2000', 'na Augustus 2000'],
            ['BEF AUG 2000', 'voor Augustus 2000'],
            ['15 SEP 2000', '15 September 2000'],
            ['SEP 2000', 'September 2000'],
            ['ABT SEP 2000', 'op ongeveer September 2000'],
            ['FROM SEP 2000', 'vanaf September 2000'],
            ['AFT SEP 2000', 'na September 2000'],
            ['BEF SEP 2000', 'voor September 2000'],
            ['15 OCT 2000', '15 Oktober 2000'],
            ['OCT 2000', 'Oktober 2000'],
            ['ABT OCT 2000', 'op ongeveer Oktober 2000'],
            ['FROM OCT 2000', 'vanaf Oktober 2000'],
            ['AFT OCT 2000', 'na Oktober 2000'],
            ['BEF OCT 2000', 'voor Oktober 2000'],
            ['15 NOV 2000', '15 November 2000'],
            ['NOV 2000', 'November 2000'],
            ['ABT NOV 2000', 'op ongeveer November 2000'],
            ['FROM NOV 2000', 'vanaf November 2000'],
            ['AFT NOV 2000', 'na November 2000'],
            ['BEF NOV 2000', 'voor November 2000'],
            ['15 DEC 2000', '15 Desember 2000'],
            ['DEC 2000', 'Desember 2000'],
            ['ABT DEC 2000', 'op ongeveer Desember 2000'],
            ['FROM DEC 2000', 'vanaf Desember 2000'],
            ['AFT DEC 2000', 'na Desember 2000'],
            ['BEF DEC 2000', 'voor Desember 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'op ongeveer 15 Januarie 2000'],
            ['CAL 15 JAN 2000', 'bereken as 15 Januarie 2000'],
            ['EST 15 JAN 2000', 'beraam op 15 Januarie 2000'],
            ['BEF 15 JAN 2000', 'voor 15 Januarie 2000'],
            ['AFT 15 JAN 2000', 'na 15 Januarie 2000'],
            ['FROM 15 JAN 2000', 'vanaf 15 Januarie 2000'],
            ['TO 15 JAN 2000', 'tot 15 Januarie 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'tussen 15 Januarie 2000 en 15 Februarie 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'vanaf 15 Januarie 2000 tot 15 Februarie 2000'],
            ['INT 15 JAN 2000', 'geïnterpreteer 15 Januarie 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Januarie 1700 n.C.'],
            ['@#DJULIAN@ JAN 1700', 'Januarie 1700 n.C.'],
            ['ABT @#DJULIAN@ JAN 1700', 'op ongeveer Januarie 1700 n.C.'],
            ['FROM @#DJULIAN@ JAN 1700', 'vanaf Januarie 1700 n.C.'],
            ['AFT @#DJULIAN@ JAN 1700', 'na Januarie 1700 n.C.'],
            ['BEF @#DJULIAN@ JAN 1700', 'voor Januarie 1700 n.C.'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Februarie 1700 n.C.'],
            ['@#DJULIAN@ FEB 1700', 'Februarie 1700 n.C.'],
            ['ABT @#DJULIAN@ FEB 1700', 'op ongeveer Februarie 1700 n.C.'],
            ['FROM @#DJULIAN@ FEB 1700', 'vanaf Februarie 1700 n.C.'],
            ['AFT @#DJULIAN@ FEB 1700', 'na Februarie 1700 n.C.'],
            ['BEF @#DJULIAN@ FEB 1700', 'voor Februarie 1700 n.C.'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Maart 1700 n.C.'],
            ['@#DJULIAN@ MAR 1700', 'Maart 1700 n.C.'],
            ['ABT @#DJULIAN@ MAR 1700', 'op ongeveer Maart 1700 n.C.'],
            ['FROM @#DJULIAN@ MAR 1700', 'vanaf Maart 1700 n.C.'],
            ['AFT @#DJULIAN@ MAR 1700', 'na Maart 1700 n.C.'],
            ['BEF @#DJULIAN@ MAR 1700', 'voor Maart 1700 n.C.'],
            ['@#DJULIAN@ 15 APR 1700', '15 April 1700 n.C.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 April 1645/46 n.C.'],
            ['@#DJULIAN@ APR 1700', 'April 1700 n.C.'],
            ['ABT @#DJULIAN@ APR 1700', 'op ongeveer April 1700 n.C.'],
            ['FROM @#DJULIAN@ APR 1700', 'vanaf April 1700 n.C.'],
            ['AFT @#DJULIAN@ APR 1700', 'na April 1700 n.C.'],
            ['BEF @#DJULIAN@ APR 1700', 'voor April 1700 n.C.'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Mei 1700 n.C.'],
            ['@#DJULIAN@ MAY 1700', 'Mei 1700 n.C.'],
            ['ABT @#DJULIAN@ MAY 1700', 'op ongeveer Mei 1700 n.C.'],
            ['FROM @#DJULIAN@ MAY 1700', 'vanaf Mei 1700 n.C.'],
            ['AFT @#DJULIAN@ MAY 1700', 'na Mei 1700 n.C.'],
            ['BEF @#DJULIAN@ MAY 1700', 'voor Mei 1700 n.C.'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Junie 1700 n.C.'],
            ['@#DJULIAN@ JUN 1700', 'Junie 1700 n.C.'],
            ['ABT @#DJULIAN@ JUN 1700', 'op ongeveer Junie 1700 n.C.'],
            ['FROM @#DJULIAN@ JUN 1700', 'vanaf Junie 1700 n.C.'],
            ['AFT @#DJULIAN@ JUN 1700', 'na Junie 1700 n.C.'],
            ['BEF @#DJULIAN@ JUN 1700', 'voor Junie 1700 n.C.'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Julie 1700 n.C.'],
            ['@#DJULIAN@ JUL 1700', 'Julie 1700 n.C.'],
            ['ABT @#DJULIAN@ JUL 1700', 'op ongeveer Julie 1700 n.C.'],
            ['FROM @#DJULIAN@ JUL 1700', 'vanaf Julie 1700 n.C.'],
            ['AFT @#DJULIAN@ JUL 1700', 'na Julie 1700 n.C.'],
            ['BEF @#DJULIAN@ JUL 1700', 'voor Julie 1700 n.C.'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Augustus 1700 n.C.'],
            ['@#DJULIAN@ AUG 1700', 'Augustus 1700 n.C.'],
            ['ABT @#DJULIAN@ AUG 1700', 'op ongeveer Augustus 1700 n.C.'],
            ['FROM @#DJULIAN@ AUG 1700', 'vanaf Augustus 1700 n.C.'],
            ['AFT @#DJULIAN@ AUG 1700', 'na Augustus 1700 n.C.'],
            ['BEF @#DJULIAN@ AUG 1700', 'voor Augustus 1700 n.C.'],
            ['@#DJULIAN@ 15 SEP 1700', '15 September 1700 n.C.'],
            ['@#DJULIAN@ SEP 1700', 'September 1700 n.C.'],
            ['ABT @#DJULIAN@ SEP 1700', 'op ongeveer September 1700 n.C.'],
            ['FROM @#DJULIAN@ SEP 1700', 'vanaf September 1700 n.C.'],
            ['AFT @#DJULIAN@ SEP 1700', 'na September 1700 n.C.'],
            ['BEF @#DJULIAN@ SEP 1700', 'voor September 1700 n.C.'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Oktober 1700 n.C.'],
            ['@#DJULIAN@ OCT 1700', 'Oktober 1700 n.C.'],
            ['ABT @#DJULIAN@ OCT 1700', 'op ongeveer Oktober 1700 n.C.'],
            ['FROM @#DJULIAN@ OCT 1700', 'vanaf Oktober 1700 n.C.'],
            ['AFT @#DJULIAN@ OCT 1700', 'na Oktober 1700 n.C.'],
            ['BEF @#DJULIAN@ OCT 1700', 'voor Oktober 1700 n.C.'],
            ['@#DJULIAN@ 15 NOV 1700', '15 November 1700 n.C.'],
            ['@#DJULIAN@ NOV 1700', 'November 1700 n.C.'],
            ['ABT @#DJULIAN@ NOV 1700', 'op ongeveer November 1700 n.C.'],
            ['FROM @#DJULIAN@ NOV 1700', 'vanaf November 1700 n.C.'],
            ['AFT @#DJULIAN@ NOV 1700', 'na November 1700 n.C.'],
            ['BEF @#DJULIAN@ NOV 1700', 'voor November 1700 n.C.'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Desember 1700 n.C.'],
            ['@#DJULIAN@ DEC 1700', 'Desember 1700 n.C.'],
            ['ABT @#DJULIAN@ DEC 1700', 'op ongeveer Desember 1700 n.C.'],
            ['FROM @#DJULIAN@ DEC 1700', 'vanaf Desember 1700 n.C.'],
            ['AFT @#DJULIAN@ DEC 1700', 'na Desember 1700 n.C.'],
            ['BEF @#DJULIAN@ DEC 1700', 'voor Desember 1700 n.C.'],
            ['@#DJULIAN@ 1700', '1700 n.C.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'op ongeveer 15 Januarie 1700 n.C.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'bereken as 15 Januarie 1700 n.C.'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'beraam op 15 Januarie 1700 n.C.'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'voor 15 Januarie 1700 n.C.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'na 15 Januarie 1700 n.C.'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'vanaf 15 Januarie 1700 n.C.'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'tot 15 Januarie 1700 n.C.'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'tussen 15 Januarie 1700 n.C. en 15 Februarie 1700 n.C.'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'vanaf 15 Januarie 1700 n.C. tot 15 Februarie 1700 n.C.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'geïnterpreteer 15 Januarie 1700 n.C.'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'op ongeveer Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'vanaf Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'na Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'voor Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'op ongeveer Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'vanaf Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'na Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'voor Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'op ongeveer Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'vanaf Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'na Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'voor Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'op ongeveer Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'vanaf Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'na Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'voor Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'op ongeveer Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'vanaf Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'na Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'voor Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'op ongeveer Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'vanaf Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'na Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'voor Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'op ongeveer Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'vanaf Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'na Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'voor Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'op ongeveer Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'vanaf Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'na Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'voor Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'op ongeveer Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'vanaf Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'na Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'voor Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'op ongeveer Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'vanaf Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'na Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'voor Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'op ongeveer Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'vanaf Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'na Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'voor Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'op ongeveer Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'vanaf Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'na Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'voor Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'op ongeveer Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'vanaf Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'na Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'voor Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'op ongeveer 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'bereken as 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'beraam op 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'voor 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'na 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'vanaf 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'tot 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'tussen 15 Tishrei 5765 en 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'vanaf 15 Tishrei 5765 tot 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'geïnterpreteer 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'op ongeveer Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'vanaf Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'na Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'voor Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'op ongeveer Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'vanaf Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'na Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'voor Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'op ongeveer Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'vanaf Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'na Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'voor Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'op ongeveer Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'vanaf Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'na Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'voor Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'op ongeveer Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'vanaf Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'na Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'voor Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'op ongeveer Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'vanaf Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'na Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'voor Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'op ongeveer Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'vanaf Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'na Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'voor Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'op ongeveer Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'vanaf Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'na Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'voor Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'op ongeveer Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'vanaf Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'na Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'voor Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'op ongeveer Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'vanaf Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'na Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'voor Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'op ongeveer Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'vanaf Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'na Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'voor Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'op ongeveer Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'vanaf Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'na Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'voor Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'op ongeveer jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'vanaf jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'na jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'voor jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'op ongeveer 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'bereken as 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'beraam op 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'voor 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'na 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'vanaf 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'tot 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'tussen 15 Vendémiaire An XII en 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'vanaf 15 Vendémiaire An XII tot 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'geïnterpreteer 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'op ongeveer Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'vanaf Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'na Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'voor Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'op ongeveer Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'vanaf Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'na Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'voor Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'op ongeveer Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'vanaf Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'na Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'voor Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'op ongeveer Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'vanaf Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'na Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'voor Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'op ongeveer Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'vanaf Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'na Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'voor Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'op ongeveer Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'vanaf Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'na Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'voor Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'op ongeveer Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'vanaf Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'na Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'voor Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'op ongeveer Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'vanaf Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'na Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'voor Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'op ongeveer Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'vanaf Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'na Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'voor Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'op ongeveer Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'vanaf Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'na Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'voor Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'op ongeveer Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'vanaf Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'na Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'voor Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'op ongeveer 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'vanaf 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'na 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'voor 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'op ongeveer 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'bereken as 15 Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'beraam op 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'voor 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'na 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'vanaf 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'tot 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'tussen 15 Muharram 1425 en 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'vanaf 15 Muharram 1425 tot 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'geïnterpreteer 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvadin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvadin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'op ongeveer Farvadin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'vanaf Farvadin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'na Farvadin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'voor Farvadin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'op ongeveer Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'vanaf Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'na Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'voor Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'op ongeveer Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'vanaf Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'na Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'voor Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'op ongeveer Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'vanaf Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'na Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'voor Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'op ongeveer Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'vanaf Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'na Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'voor Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'op ongeveer Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'vanaf Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'na Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'voor Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'op ongeveer Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'vanaf Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'na Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'voor Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'op ongeveer Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'vanaf Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'na Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'voor Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'op ongeveer Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'vanaf Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'na Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'voor Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'op ongeveer Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'vanaf Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'na Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'voor Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'op ongeveer Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'vanaf Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'na Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'voor Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'op ongeveer Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'vanaf Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'na Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'voor Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'op ongeveer 15 Farvadin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'bereken as 15 Farvadin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'beraam op 15 Farvadin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'voor 15 Farvadin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'na 15 Farvadin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'vanaf 15 Farvadin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'tot 15 Farvadin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'tussen 15 Farvadin 1384 en 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'vanaf 15 Farvadin 1384 tot 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'geïnterpreteer 15 Farvadin 1384'],
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
        self::assertSame('one en two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two en three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one of two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two of three', $language->formatListOr(['one', 'two', 'three']));
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
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fosterSon = self::male('fs', "1 FAMC @fd@\n2 PEDI foster");
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $cousinFemale = self::female('cf', "1 FAMC @fbro@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fs@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $stepDaughter, $fosterSon,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('vrou', 'man', [$husband, $fm, $wife]);
        self::assertRelationshipNames('eks-man', 'eks-vrou', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('verloofde', 'verloofde', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('moeder', 'seun', [$son, $fm, $wife]);
        self::assertRelationshipNames('vader', 'seun', [$son, $fm, $husband]);
        self::assertRelationshipNames('moeder', 'dogter', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('aanneemmoeder', 'aangenome seun', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('aanneemvader', 'aangenome seun', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('pleegmoeder', 'pleegseun', [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames('pleegvader', 'pleegseun', [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('jonger suster', 'ouer broer', [$son, $fm, $daughter]);
        self::assertRelationshipNames('ouer broer', 'jonger suster', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipNames('halfbroer', 'halfsuster', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('stiefvader', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('stiefdogter', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('skoonmoeder', 'skoonseun', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('skoonvader', 'skoonseun', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('skoondogter', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('ouma', 'kleinseun', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('oupa', 'kleinseun', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('kleindogter', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('groot-groot-oupa', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('groot-groot-ouma', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tante', 'neef', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('oom', 'neef', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('niggie', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('neef', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('niggie', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('neef', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('groot-tante', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('groot-oom', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
