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
use Fisharebest\Webtrees\I18N\Languages\Welsh;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Welsh::class)]
class WelshTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Welsh();
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
        self::assertSame('cy', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Cymraeg', self::language()->endonym());
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
        self::assertSame('-123,456.0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-123,456.0789%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Ionawr 2000'],
            ['JAN 2000', 'Ionawr 2000'],
            ['ABT JAN 2000', 'tua Ionawr 2000'],
            ['FROM JAN 2000', 'o Ionawr 2000'],
            ['AFT JAN 2000', 'ar ôl Ionawr 2000'],
            ['BEF JAN 2000', 'cyn Ionawr 2000'],
            ['15 FEB 2000', '15 Chwefror 2000'],
            ['FEB 2000', 'Chwefror 2000'],
            ['ABT FEB 2000', 'tua Chwefror 2000'],
            ['FROM FEB 2000', 'o Chwefror 2000'],
            ['AFT FEB 2000', 'ar ôl Chwefror 2000'],
            ['BEF FEB 2000', 'cyn Chwefror 2000'],
            ['15 MAR 2000', '15 Mawrth 2000'],
            ['MAR 2000', 'Mawrth 2000'],
            ['ABT MAR 2000', 'tua Mawrth 2000'],
            ['FROM MAR 2000', 'o Mawrth 2000'],
            ['AFT MAR 2000', 'ar ôl Mawrth 2000'],
            ['BEF MAR 2000', 'cyn Mawrth 2000'],
            ['15 APR 2000', '15 Ebrill 2000'],
            ['APR 2000', 'Ebrill 2000'],
            ['ABT APR 2000', 'tua Ebrill 2000'],
            ['FROM APR 2000', 'o Ebrill 2000'],
            ['AFT APR 2000', 'ar ôl Ebrill 2000'],
            ['BEF APR 2000', 'cyn Ebrill 2000'],
            ['15 MAY 2000', '15 Mai 2000'],
            ['MAY 2000', 'Mai 2000'],
            ['ABT MAY 2000', 'tua Mai 2000'],
            ['FROM MAY 2000', 'o Mai 2000'],
            ['AFT MAY 2000', 'ar ôl Mai 2000'],
            ['BEF MAY 2000', 'cyn Mai 2000'],
            ['15 JUN 2000', '15 Mehefin 2000'],
            ['JUN 2000', 'Mehefin 2000'],
            ['ABT JUN 2000', 'tua Mehefin 2000'],
            ['FROM JUN 2000', 'o Mehefin 2000'],
            ['AFT JUN 2000', 'ar ôl Mehefin 2000'],
            ['BEF JUN 2000', 'cyn Mehefin 2000'],
            ['15 JUL 2000', '15 Gorffennaf 2000'],
            ['JUL 2000', 'Gorffennaf 2000'],
            ['ABT JUL 2000', 'tua Gorffennaf 2000'],
            ['FROM JUL 2000', 'o Gorffennaf 2000'],
            ['AFT JUL 2000', 'ar ôl Gorffennaf 2000'],
            ['BEF JUL 2000', 'cyn Gorffennaf 2000'],
            ['15 AUG 2000', '15 Awst 2000'],
            ['AUG 2000', 'Awst 2000'],
            ['ABT AUG 2000', 'tua Awst 2000'],
            ['FROM AUG 2000', 'o Awst 2000'],
            ['AFT AUG 2000', 'ar ôl Awst 2000'],
            ['BEF AUG 2000', 'cyn Awst 2000'],
            ['15 SEP 2000', '15 Medi 2000'],
            ['SEP 2000', 'Medi 2000'],
            ['ABT SEP 2000', 'tua Medi 2000'],
            ['FROM SEP 2000', 'o Medi 2000'],
            ['AFT SEP 2000', 'ar ôl Medi 2000'],
            ['BEF SEP 2000', 'cyn Medi 2000'],
            ['15 OCT 2000', '15 Hydref 2000'],
            ['OCT 2000', 'Hydref 2000'],
            ['ABT OCT 2000', 'tua Hydref 2000'],
            ['FROM OCT 2000', 'o Hydref 2000'],
            ['AFT OCT 2000', 'ar ôl Hydref 2000'],
            ['BEF OCT 2000', 'cyn Hydref 2000'],
            ['15 NOV 2000', '15 Tachwedd 2000'],
            ['NOV 2000', 'Tachwedd 2000'],
            ['ABT NOV 2000', 'tua Tachwedd 2000'],
            ['FROM NOV 2000', 'o Tachwedd 2000'],
            ['AFT NOV 2000', 'ar ôl Tachwedd 2000'],
            ['BEF NOV 2000', 'cyn Tachwedd 2000'],
            ['15 DEC 2000', '15 Rhagfyr 2000'],
            ['DEC 2000', 'Rhagfyr 2000'],
            ['ABT DEC 2000', 'tua Rhagfyr 2000'],
            ['FROM DEC 2000', 'o Rhagfyr 2000'],
            ['AFT DEC 2000', 'ar ôl Rhagfyr 2000'],
            ['BEF DEC 2000', 'cyn Rhagfyr 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'tua 15 Ionawr 2000'],
            ['CAL 15 JAN 2000', 'cyfrifwyd 15 Ionawr 2000'],
            ['EST 15 JAN 2000', 'amcangyfrifwyd 15 Ionawr 2000'],
            ['BEF 15 JAN 2000', 'cyn 15 Ionawr 2000'],
            ['AFT 15 JAN 2000', 'ar ôl 15 Ionawr 2000'],
            ['FROM 15 JAN 2000', 'o 15 Ionawr 2000'],
            ['TO 15 JAN 2000', 'hyd 15 Ionawr 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'rhwng 15 Ionawr 2000 a 15 Chwefror 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'o 15 Ionawr 2000 hyd 15 Chwefror 2000'],
            ['INT 15 JAN 2000', 'dehonglwyd 15 Ionawr 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Ionawr 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Ionawr 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'tua Ionawr 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'o Ionawr 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'ar ôl Ionawr 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'cyn Ionawr 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Chwefror 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Chwefror 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'tua Chwefror 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'o Chwefror 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'ar ôl Chwefror 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'cyn Chwefror 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Mawrth 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Mawrth 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'tua Mawrth 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'o Mawrth 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'ar ôl Mawrth 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'cyn Mawrth 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 Ebrill 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Ebrill 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'Ebrill 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'tua Ebrill 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'o Ebrill 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'ar ôl Ebrill 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'cyn Ebrill 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Mai 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Mai 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'tua Mai 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'o Mai 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'ar ôl Mai 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'cyn Mai 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Mehefin 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Mehefin 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'tua Mehefin 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'o Mehefin 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'ar ôl Mehefin 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'cyn Mehefin 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Gorffennaf 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Gorffennaf 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'tua Gorffennaf 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'o Gorffennaf 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'ar ôl Gorffennaf 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'cyn Gorffennaf 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Awst 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Awst 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'tua Awst 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'o Awst 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'ar ôl Awst 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'cyn Awst 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Medi 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Medi 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'tua Medi 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'o Medi 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'ar ôl Medi 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'cyn Medi 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Hydref 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Hydref 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'tua Hydref 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'o Hydref 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'ar ôl Hydref 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'cyn Hydref 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Tachwedd 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Tachwedd 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'tua Tachwedd 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'o Tachwedd 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'ar ôl Tachwedd 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'cyn Tachwedd 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Rhagfyr 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Rhagfyr 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'tua Rhagfyr 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'o Rhagfyr 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'ar ôl Rhagfyr 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'cyn Rhagfyr 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'tua 15 Ionawr 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'cyfrifwyd 15 Ionawr 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'amcangyfrifwyd 15 Ionawr 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'cyn 15 Ionawr 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'ar ôl 15 Ionawr 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'o 15 Ionawr 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'hyd 15 Ionawr 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'rhwng 15 Ionawr 1700 ᴄᴇ a 15 Chwefror 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'o 15 Ionawr 1700 ᴄᴇ hyd 15 Chwefror 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'dehonglwyd 15 Ionawr 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'tua Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'o Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'ar ôl Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'cyn Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'tua Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'o Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'ar ôl Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'cyn Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'tua Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'o Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'ar ôl Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'cyn Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'tua Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'o Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'ar ôl Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'cyn Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'tua Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'o Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'ar ôl Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'cyn Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'tua Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'o Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'ar ôl Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'cyn Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'tua Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'o Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'ar ôl Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'cyn Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'tua Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'o Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'ar ôl Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'cyn Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'tua Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'o Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'ar ôl Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'cyn Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'tua Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'o Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'ar ôl Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'cyn Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'tua Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'o Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'ar ôl Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'cyn Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'tua Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'o Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'ar ôl Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'cyn Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'tua Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'o Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'ar ôl Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'cyn Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'tua 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'cyfrifwyd 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'amcangyfrifwyd 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'cyn 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'ar ôl 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'o 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'hyd 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'rhwng 15 Tishrei 5765 a 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'o 15 Tishrei 5765 hyd 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'dehonglwyd 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'tua Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'o Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'ar ôl Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'cyn Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'tua Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'o Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'ar ôl Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'cyn Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'tua Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'o Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'ar ôl Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'cyn Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'tua Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'o Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'ar ôl Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'cyn Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'tua Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'o Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'ar ôl Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'cyn Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'tua Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'o Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'ar ôl Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'cyn Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'tua Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'o Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'ar ôl Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'cyn Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'tua Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'o Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'ar ôl Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'cyn Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'tua Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'o Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'ar ôl Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'cyn Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'tua Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'o Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'ar ôl Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'cyn Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'tua Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'o Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'ar ôl Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'cyn Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'tua Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'o Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'ar ôl Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'cyn Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'tua jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'o jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'ar ôl jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'cyn jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'tua 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'cyfrifwyd 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'amcangyfrifwyd 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'cyn 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'ar ôl 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'o 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'hyd 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'rhwng 15 Vendémiaire An XII a 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'o 15 Vendémiaire An XII hyd 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'dehonglwyd 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muḥarram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muḥarram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'tua Muḥarram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'o Muḥarram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'ar ôl Muḥarram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'cyn Muḥarram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Ṣafar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Ṣafar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'tua Ṣafar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'o Ṣafar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'ar ôl Ṣafar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'cyn Ṣafar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabiʿ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabiʿ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'tua Rabiʿ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'o Rabiʿ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'ar ôl Rabiʿ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'cyn Rabiʿ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabiʿ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabiʿ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'tua Rabiʿ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'o Rabiʿ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'ar ôl Rabiʿ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'cyn Rabiʿ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumādá al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumādá al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'tua Jumādá al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'o Jumādá al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'ar ôl Jumādá al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'cyn Jumādá al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumādá al-thānī 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumādá al-thānī 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'tua Jumādá al-thānī 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'o Jumādá al-thānī 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'ar ôl Jumādá al-thānī 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'cyn Jumādá al-thānī 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'tua Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'o Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'ar ôl Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'cyn Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Shaʿbān 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Shaʿbān 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'tua Shaʿbān 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'o Shaʿbān 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'ar ôl Shaʿbān 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'cyn Shaʿbān 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'tua Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'o Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'ar ôl Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'cyn Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'tua Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'o Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'ar ôl Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'cyn Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhū al-Qiʿdah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhū al-Qiʿdah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'tua Dhū al-Qiʿdah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'o Dhū al-Qiʿdah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'ar ôl Dhū al-Qiʿdah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'cyn Dhū al-Qiʿdah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'tua 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'o 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'ar ôl 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'cyn 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'tua 15 Muḥarram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'cyfrifwyd 15 Muḥarram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'amcangyfrifwyd 15 Muḥarram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'cyn 15 Muḥarram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'ar ôl 15 Muḥarram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'o 15 Muḥarram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'hyd 15 Muḥarram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'rhwng 15 Muḥarram 1425 a 15 Ṣafar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'o 15 Muḥarram 1425 hyd 15 Ṣafar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'dehonglwyd 15 Muḥarram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'tua Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'o Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'ar ôl Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'cyn Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'tua Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'o Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'ar ôl Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'cyn Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordād 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordād 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'tua Khordād 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'o Khordād 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'ar ôl Khordād 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'cyn Khordād 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tīr 1384'],
            ['@#DJALALI@ TIR 1384', 'Tīr 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'tua Tīr 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'o Tīr 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'ar ôl Tīr 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'cyn Tīr 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordād 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordād 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'tua Mordād 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'o Mordād 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'ar ôl Mordād 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'cyn Mordād 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrīvar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrīvar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'tua Shahrīvar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'o Shahrīvar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'ar ôl Shahrīvar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'cyn Shahrīvar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'tua Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'o Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'ar ôl Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'cyn Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Ābān 1384'],
            ['@#DJALALI@ ABAN 1384', 'Ābān 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'tua Ābān 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'o Ābān 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'ar ôl Ābān 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'cyn Ābān 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Āzar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Āzar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'tua Āzar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'o Āzar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'ar ôl Āzar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'cyn Āzar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'tua Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'o Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'ar ôl Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'cyn Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'tua Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'o Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'ar ôl Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'cyn Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'tua Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'o Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'ar ôl Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'cyn Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'tua 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'cyfrifwyd 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'amcangyfrifwyd 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'cyn 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'ar ôl 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'o 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'hyd 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'rhwng 15 Farvardin 1384 a 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'o 15 Farvardin 1384 hyd 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'dehonglwyd 15 Farvardin 1384'],
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
        self::assertSame('one a two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two a three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one neu two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two neu three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('gwraig', 'gŵr', [$husband, $fm, $wife]);
        self::assertRelationshipNames('cyn-ŵr', 'cyn-wraig', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('dyweddi', 'dyweddi', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mam', 'mab', [$son, $fm, $wife]);
        self::assertRelationshipNames('tad', 'mab', [$son, $fm, $husband]);
        self::assertRelationshipNames('mam', 'merch', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('mam fabwysiedig', 'mab mabwysiedig', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('tad mabwysiedig', 'mab mabwysiedig', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('mam faeth', 'mab maeth', [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames('tad maeth', 'mab maeth', [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('chwaer iau', 'brawd hŷn', [$son, $fm, $daughter]);
        self::assertRelationshipNames('brawd hŷn', 'chwaer iau', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipNames('hanner brawd', 'hanner chwaer', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('llystad', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('llysferch', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('mam-yng-nghyfraith', 'mab-yng-nghyfraith', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('tad-yng-nghyfraith', 'mab-yng-nghyfraith', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('merch-yng-nghyfraith', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('mam-gu', 'ŵyr', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('tad-cu', 'ŵyr', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('wyres', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('hen dad-cu', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('hen fam-gu', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('modryb', 'nai', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('ewythr', 'nai', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('nith', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nai', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('cyfnither', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('cefnder', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('hen fodryb', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('hen ewythr', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
