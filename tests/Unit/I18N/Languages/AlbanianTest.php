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
use Fisharebest\Webtrees\I18N\Languages\Albanian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Albanian::class)]
class AlbanianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Albanian();
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
        self::assertSame([], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('sq', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('shqip', self::language()->endonym());
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
            ['15 JAN 2000', '15 Janar 2000'],
            ['JAN 2000', 'Janar 2000'],
            ['ABT JAN 2000', 'rreth Janar 2000'],
            ['FROM JAN 2000', 'nga Janar 2000'],
            ['AFT JAN 2000', 'pas Janar 2000'],
            ['BEF JAN 2000', 'para Janar 2000'],
            ['15 FEB 2000', '15 Shkurt 2000'],
            ['FEB 2000', 'Shkurti 2000'],
            ['ABT FEB 2000', 'rreth Shkurt 2000'],
            ['FROM FEB 2000', 'nga Shkurt 2000'],
            ['AFT FEB 2000', 'pas Shkurti 2000'],
            ['BEF FEB 2000', 'para Shkurt 2000'],
            ['15 MAR 2000', '15 Mars 2000'],
            ['MAR 2000', 'Mars 2000'],
            ['ABT MAR 2000', 'rreth Mars 2000'],
            ['FROM MAR 2000', 'nga Mars 2000'],
            ['AFT MAR 2000', 'pas Mars 2000'],
            ['BEF MAR 2000', 'para Mars 2000'],
            ['15 APR 2000', '15 Prill 2000'],
            ['APR 2000', 'Prilli 2000'],
            ['ABT APR 2000', 'rreth Prill 2000'],
            ['FROM APR 2000', 'nga Prill 2000'],
            ['AFT APR 2000', 'pas Prilli 2000'],
            ['BEF APR 2000', 'para Prill 2000'],
            ['15 MAY 2000', '15 Majit 2000'],
            ['MAY 2000', 'Maj 2000'],
            ['ABT MAY 2000', 'rreth Majit 2000'],
            ['FROM MAY 2000', 'nga Majit 2000'],
            ['AFT MAY 2000', 'pas Maj 2000'],
            ['BEF MAY 2000', 'para Maj 2000'],
            ['15 JUN 2000', '15 Qershor 2000'],
            ['JUN 2000', 'Qershor 2000'],
            ['ABT JUN 2000', 'rreth Qershor 2000'],
            ['FROM JUN 2000', 'nga Qershor 2000'],
            ['AFT JUN 2000', 'pas Qershor 2000'],
            ['BEF JUN 2000', 'para Qershor 2000'],
            ['15 JUL 2000', '15 Korrik 2000'],
            ['JUL 2000', 'Korrik 2000'],
            ['ABT JUL 2000', 'rreth Korrik 2000'],
            ['FROM JUL 2000', 'nga Korrik 2000'],
            ['AFT JUL 2000', 'pas Korrik 2000'],
            ['BEF JUL 2000', 'para Korrik 2000'],
            ['15 AUG 2000', '15 Gusht 2000'],
            ['AUG 2000', 'Gushti 2000'],
            ['ABT AUG 2000', 'rreth Gusht 2000'],
            ['FROM AUG 2000', 'nga Gusht 2000'],
            ['AFT AUG 2000', 'pas Gushti 2000'],
            ['BEF AUG 2000', 'para Gusht 2000'],
            ['15 SEP 2000', '15 Shtator 2000'],
            ['SEP 2000', 'Shtatori 2000'],
            ['ABT SEP 2000', 'rreth Shtator 2000'],
            ['FROM SEP 2000', 'nga Shtator 2000'],
            ['AFT SEP 2000', 'pas Shtator 2000'],
            ['BEF SEP 2000', 'para Shtatorin 2000'],
            ['15 OCT 2000', '15 Tetor 2000'],
            ['OCT 2000', 'Tetor 2000'],
            ['ABT OCT 2000', 'rreth Tetor 2000'],
            ['FROM OCT 2000', 'nga Tetor 2000'],
            ['AFT OCT 2000', 'pas Tetor 2000'],
            ['BEF OCT 2000', 'para Tetor 2000'],
            ['15 NOV 2000', '15 Nëntor 2000'],
            ['NOV 2000', 'Nëntor 2000'],
            ['ABT NOV 2000', 'rreth Nëntor 2000'],
            ['FROM NOV 2000', 'nga Nëntor 2000'],
            ['AFT NOV 2000', 'pas Nëntor 2000'],
            ['BEF NOV 2000', 'para Nëntor 2000'],
            ['15 DEC 2000', '15 Dhjetori 2000'],
            ['DEC 2000', 'Dhjetori 2000'],
            ['ABT DEC 2000', 'rreth Dhjetori 2000'],
            ['FROM DEC 2000', 'nga Dhjetori 2000'],
            ['AFT DEC 2000', 'pas Dhjetori 2000'],
            ['BEF DEC 2000', 'para Dhjetor 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'rreth 15 Janar 2000'],
            ['CAL 15 JAN 2000', 'kalkuluar 15 Janar 2000'],
            ['EST 15 JAN 2000', 'vlerësuar 15 Janar 2000'],
            ['BEF 15 JAN 2000', 'para 15 Janar 2000'],
            ['AFT 15 JAN 2000', 'pas 15 Janar 2000'],
            ['FROM 15 JAN 2000', 'nga 15 Janar 2000'],
            ['TO 15 JAN 2000', 'deri te 15 Janar 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'ndërmjet 15 Janar 2000 dhe 15 Shkurt 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'nga 15 Janar 2000 deri në 15 Shkurt 2000'],
            ['INT 15 JAN 2000', 'interpretuar 15 Janar 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Janar 1700 ER'],
            ['@#DJULIAN@ JAN 1700', 'Janar 1700 ER'],
            ['ABT @#DJULIAN@ JAN 1700', 'rreth Janar 1700 ER'],
            ['FROM @#DJULIAN@ JAN 1700', 'nga Janar 1700 ER'],
            ['AFT @#DJULIAN@ JAN 1700', 'pas Janar 1700 ER'],
            ['BEF @#DJULIAN@ JAN 1700', 'para Janar 1700 ER'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Shkurt 1700 ER'],
            ['@#DJULIAN@ FEB 1700', 'Shkurti 1700 ER'],
            ['ABT @#DJULIAN@ FEB 1700', 'rreth Shkurt 1700 ER'],
            ['FROM @#DJULIAN@ FEB 1700', 'nga Shkurt 1700 ER'],
            ['AFT @#DJULIAN@ FEB 1700', 'pas Shkurti 1700 ER'],
            ['BEF @#DJULIAN@ FEB 1700', 'para Shkurt 1700 ER'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Mars 1700 ER'],
            ['@#DJULIAN@ MAR 1700', 'Mars 1700 ER'],
            ['ABT @#DJULIAN@ MAR 1700', 'rreth Mars 1700 ER'],
            ['FROM @#DJULIAN@ MAR 1700', 'nga Mars 1700 ER'],
            ['AFT @#DJULIAN@ MAR 1700', 'pas Mars 1700 ER'],
            ['BEF @#DJULIAN@ MAR 1700', 'para Mars 1700 ER'],
            ['@#DJULIAN@ 15 APR 1700', '15 Prill 1700 ER'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Prill 1645/46 ER'],
            ['@#DJULIAN@ APR 1700', 'Prilli 1700 ER'],
            ['ABT @#DJULIAN@ APR 1700', 'rreth Prill 1700 ER'],
            ['FROM @#DJULIAN@ APR 1700', 'nga Prill 1700 ER'],
            ['AFT @#DJULIAN@ APR 1700', 'pas Prilli 1700 ER'],
            ['BEF @#DJULIAN@ APR 1700', 'para Prill 1700 ER'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Majit 1700 ER'],
            ['@#DJULIAN@ MAY 1700', 'Maj 1700 ER'],
            ['ABT @#DJULIAN@ MAY 1700', 'rreth Majit 1700 ER'],
            ['FROM @#DJULIAN@ MAY 1700', 'nga Majit 1700 ER'],
            ['AFT @#DJULIAN@ MAY 1700', 'pas Maj 1700 ER'],
            ['BEF @#DJULIAN@ MAY 1700', 'para Maj 1700 ER'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Qershor 1700 ER'],
            ['@#DJULIAN@ JUN 1700', 'Qershor 1700 ER'],
            ['ABT @#DJULIAN@ JUN 1700', 'rreth Qershor 1700 ER'],
            ['FROM @#DJULIAN@ JUN 1700', 'nga Qershor 1700 ER'],
            ['AFT @#DJULIAN@ JUN 1700', 'pas Qershor 1700 ER'],
            ['BEF @#DJULIAN@ JUN 1700', 'para Qershor 1700 ER'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Korrik 1700 ER'],
            ['@#DJULIAN@ JUL 1700', 'Korrik 1700 ER'],
            ['ABT @#DJULIAN@ JUL 1700', 'rreth Korrik 1700 ER'],
            ['FROM @#DJULIAN@ JUL 1700', 'nga Korrik 1700 ER'],
            ['AFT @#DJULIAN@ JUL 1700', 'pas Korrik 1700 ER'],
            ['BEF @#DJULIAN@ JUL 1700', 'para Korrik 1700 ER'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Gusht 1700 ER'],
            ['@#DJULIAN@ AUG 1700', 'Gushti 1700 ER'],
            ['ABT @#DJULIAN@ AUG 1700', 'rreth Gusht 1700 ER'],
            ['FROM @#DJULIAN@ AUG 1700', 'nga Gusht 1700 ER'],
            ['AFT @#DJULIAN@ AUG 1700', 'pas Gushti 1700 ER'],
            ['BEF @#DJULIAN@ AUG 1700', 'para Gusht 1700 ER'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Shtator 1700 ER'],
            ['@#DJULIAN@ SEP 1700', 'Shtatori 1700 ER'],
            ['ABT @#DJULIAN@ SEP 1700', 'rreth Shtator 1700 ER'],
            ['FROM @#DJULIAN@ SEP 1700', 'nga Shtator 1700 ER'],
            ['AFT @#DJULIAN@ SEP 1700', 'pas Shtator 1700 ER'],
            ['BEF @#DJULIAN@ SEP 1700', 'para Shtatorin 1700 ER'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Tetor 1700 ER'],
            ['@#DJULIAN@ OCT 1700', 'Tetor 1700 ER'],
            ['ABT @#DJULIAN@ OCT 1700', 'rreth Tetor 1700 ER'],
            ['FROM @#DJULIAN@ OCT 1700', 'nga Tetor 1700 ER'],
            ['AFT @#DJULIAN@ OCT 1700', 'pas Tetor 1700 ER'],
            ['BEF @#DJULIAN@ OCT 1700', 'para Tetor 1700 ER'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Nëntor 1700 ER'],
            ['@#DJULIAN@ NOV 1700', 'Nëntor 1700 ER'],
            ['ABT @#DJULIAN@ NOV 1700', 'rreth Nëntor 1700 ER'],
            ['FROM @#DJULIAN@ NOV 1700', 'nga Nëntor 1700 ER'],
            ['AFT @#DJULIAN@ NOV 1700', 'pas Nëntor 1700 ER'],
            ['BEF @#DJULIAN@ NOV 1700', 'para Nëntor 1700 ER'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Dhjetori 1700 ER'],
            ['@#DJULIAN@ DEC 1700', 'Dhjetori 1700 ER'],
            ['ABT @#DJULIAN@ DEC 1700', 'rreth Dhjetori 1700 ER'],
            ['FROM @#DJULIAN@ DEC 1700', 'nga Dhjetori 1700 ER'],
            ['AFT @#DJULIAN@ DEC 1700', 'pas Dhjetori 1700 ER'],
            ['BEF @#DJULIAN@ DEC 1700', 'para Dhjetor 1700 ER'],
            ['@#DJULIAN@ 1700', '1700 ER'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'rreth 15 Janar 1700 ER'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'kalkuluar 15 Janar 1700 ER'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'vlerësuar 15 Janar 1700 ER'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'para 15 Janar 1700 ER'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'pas 15 Janar 1700 ER'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'nga 15 Janar 1700 ER'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'deri te 15 Janar 1700 ER'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'ndërmjet 15 Janar 1700 ER dhe 15 Shkurt 1700 ER'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'nga 15 Janar 1700 ER deri në 15 Shkurt 1700 ER'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretuar 15 Janar 1700 ER'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'rreth Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'nga Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'pas Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'para Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'rreth Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'nga Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'pas Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'para Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'rreth Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'nga Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'pas Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'para Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'rreth Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'nga Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'pas Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'para Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'rreth Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'nga Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'pas Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'para Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'rreth Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'nga Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'pas Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'para Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'rreth Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'nga Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'pas Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'para Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'rreth Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'nga Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'pas Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'para Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 lyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'lyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'rreth lyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'nga lyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'pas lyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'para lyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'rreth Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'nga Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'pas Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'para Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'rreth Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'nga Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'pas Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'para Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'rreth Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'nga Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'pas Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'para Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'rreth Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'nga Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'pas Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'para Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'rreth 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'kalkuluar 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'vlerësuar 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'para 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'pas 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'nga 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'deri te 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'ndërmjet 15 Tishrei 5765 dhe 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'nga 15 Tishrei 5765 deri në 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretuar 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'rreth Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'nga Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'pas Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'para Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'rreth Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'nga Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'pas Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'para Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'rreth Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'nga Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'pas Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'para Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'rreth Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'nga Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'pas Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'para Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'rreth Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'nga Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'pas Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'para Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'rreth Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'nga Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'pas Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'para Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'rreth Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'nga Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'pas Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'para Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'rreth Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'nga Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'pas Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'para Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'rreth Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'nga Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'pas Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'para Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'rreth Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'nga Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'pas Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'para Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'rreth Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'nga Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'pas Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'para Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'rreth Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'nga Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'pas Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'para Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'rreth jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'nga jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'pas jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'para jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'rreth 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'kalkuluar 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'vlerësuar 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'para 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'pas 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'nga 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'deri te 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'ndërmjet 15 Vendémiaire An XII dhe 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'nga 15 Vendémiaire An XII deri në 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretuar 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'rreth Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'nga Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'pas Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'para Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'rreth Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'nga Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'pas Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'para Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'rreth Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'nga Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'pas Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'para Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'rreth Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'nga Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'pas Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'para Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'rreth Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'nga Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'pas Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'para Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'rreth Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'nga Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'pas Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'para Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'rreth Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'nga Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'pas Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'para Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'rreth Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'nga Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'pas Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'para Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramazanit 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramazanit 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'rreth Ramazanit 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'nga Ramazanit 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'pas Ramazanit 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'para Ramazanit 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'rreth Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'nga Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'pas Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'para Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'rreth Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'nga Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'pas Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'para Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'rreth 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'nga 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'pas 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'para 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'rreth 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'kalkuluar 15 Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'vlerësuar 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'para 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'pas 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'nga 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'deri te 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'ndërmjet 15 Muharram 1425 dhe 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'nga 15 Muharram 1425 deri në 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretuar 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'rreth Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'nga Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'pas Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'para Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'rreth Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'nga Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'pas Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'para Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'rreth Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'nga Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'pas Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'para Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'rreth Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'nga Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'pas Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'para Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'rreth Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'nga Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'pas Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'para Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'rreth Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'nga Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'pas Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'para Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'rreth Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'nga Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'pas Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'para Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'rreth Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'nga Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'pas Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'para Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'rreth Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'nga Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'pas Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'para Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'rreth Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'nga Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'pas Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'para Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'rreth Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'nga Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'pas Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'para Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'rreth Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'nga Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'pas Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'para Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'rreth 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'kalkuluar 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'vlerësuar 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'para 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'pas 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'nga 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'deri te 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'ndërmjet 15 Farvardin 1384 dhe 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'nga 15 Farvardin 1384 deri në 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretuar 15 Farvardin 1384'],
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
        self::assertSame('one dhe two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two dhe three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one ose two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two ose three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('grua', 'burrë', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ish-burrë', 'ish-grua', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('e fejuar', 'i fejuar', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('nënë', 'djalë', [$son, $fm, $wife]);
        self::assertRelationshipNames('baba', 'djalë', [$son, $fm, $husband]);
        self::assertRelationshipNames('nënë', 'vajzë', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('nënë birësuese', 'djalë i birësuar', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('baba birësues', 'djalë i birësuar', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('nënë kujdestare', 'djalë në kujdestari', [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames('baba kujdestar', 'djalë në kujdestari', [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('motër e vogël', 'vëlla i madh', [$son, $fm, $daughter]);
        self::assertRelationshipNames('vëlla i madh', 'motër e vogël', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipNames('gjysmëvëlla', 'gjysmëmotër', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('njerk', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('vajzë vitregë', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('vjehrrë', 'dhëndër', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('vjehërr', 'dhëndër', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('nuse', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('gjyshe', 'nip', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('gjysh', 'nip', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('mbesë', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('stërgjysh', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('stërgjyshe', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles (paternal side - through father)
        self::assertRelationshipNames('hallë', 'nip', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('xhaxha', 'nip', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('mbesë', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nip', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('kushërirë', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('kushëri', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('stërteze', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('stërxhaxha', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
