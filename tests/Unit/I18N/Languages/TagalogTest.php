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
use Fisharebest\Webtrees\I18N\Languages\Tagalog;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Tagalog::class)]
class TagalogTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Tagalog();
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
        self::assertSame(PaperSize::USLetter, self::language()->paperSize());
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
        self::assertSame('tl', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Tagalog', self::language()->endonym());
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
            ['15 JAN 2000', '15 Enero 2000'],
            ['JAN 2000', 'Enero 2000'],
            ['ABT JAN 2000', 'about Enero 2000'],
            ['FROM JAN 2000', 'from Enero 2000'],
            ['AFT JAN 2000', 'after Enero 2000'],
            ['BEF JAN 2000', 'before Enero 2000'],
            ['15 FEB 2000', '15 Pebrero 2000'],
            ['FEB 2000', 'Pebrero 2000'],
            ['ABT FEB 2000', 'about Pebrero 2000'],
            ['FROM FEB 2000', 'from Pebrero 2000'],
            ['AFT FEB 2000', 'after Pebrero 2000'],
            ['BEF FEB 2000', 'before Pebrero 2000'],
            ['15 MAR 2000', '15 Marso 2000'],
            ['MAR 2000', 'Marso 2000'],
            ['ABT MAR 2000', 'about Marso 2000'],
            ['FROM MAR 2000', 'from Marso 2000'],
            ['AFT MAR 2000', 'after Marso 2000'],
            ['BEF MAR 2000', 'before Marso 2000'],
            ['15 APR 2000', '15 Abril 2000'],
            ['APR 2000', 'Abril 2000'],
            ['ABT APR 2000', 'about Abril 2000'],
            ['FROM APR 2000', 'from Abril 2000'],
            ['AFT APR 2000', 'after Abril 2000'],
            ['BEF APR 2000', 'before Abril 2000'],
            ['15 MAY 2000', '15 Mayo 2000'],
            ['MAY 2000', 'Mayo 2000'],
            ['ABT MAY 2000', 'about Mayo 2000'],
            ['FROM MAY 2000', 'from Mayo 2000'],
            ['AFT MAY 2000', 'after Mayo 2000'],
            ['BEF MAY 2000', 'before Mayo 2000'],
            ['15 JUN 2000', '15 Hunyo 2000'],
            ['JUN 2000', 'Hunyo 2000'],
            ['ABT JUN 2000', 'about Hunyo 2000'],
            ['FROM JUN 2000', 'from Hunyo 2000'],
            ['AFT JUN 2000', 'after Hunyo 2000'],
            ['BEF JUN 2000', 'before Hunyo 2000'],
            ['15 JUL 2000', '15 Hulyo 2000'],
            ['JUL 2000', 'Hulyo 2000'],
            ['ABT JUL 2000', 'about Hulyo 2000'],
            ['FROM JUL 2000', 'from Hulyo 2000'],
            ['AFT JUL 2000', 'after Hulyo 2000'],
            ['BEF JUL 2000', 'before Hulyo 2000'],
            ['15 AUG 2000', '15 Agosto 2000'],
            ['AUG 2000', 'Agosto 2000'],
            ['ABT AUG 2000', 'about Agosto 2000'],
            ['FROM AUG 2000', 'from Agosto 2000'],
            ['AFT AUG 2000', 'after Agosto 2000'],
            ['BEF AUG 2000', 'before Agosto 2000'],
            ['15 SEP 2000', '15 Setyembre 2000'],
            ['SEP 2000', 'Setyembre 2000'],
            ['ABT SEP 2000', 'about Setyembre 2000'],
            ['FROM SEP 2000', 'from Setyembre 2000'],
            ['AFT SEP 2000', 'after Setyembre 2000'],
            ['BEF SEP 2000', 'before Setyembre 2000'],
            ['15 OCT 2000', '15 Oktubre 2000'],
            ['OCT 2000', 'Oktubre 2000'],
            ['ABT OCT 2000', 'about Oktubre 2000'],
            ['FROM OCT 2000', 'from Oktubre 2000'],
            ['AFT OCT 2000', 'after Oktubre 2000'],
            ['BEF OCT 2000', 'before Oktubre 2000'],
            ['15 NOV 2000', '15 Nobyembre 2000'],
            ['NOV 2000', 'Nobyembre 2000'],
            ['ABT NOV 2000', 'about Nobyembre 2000'],
            ['FROM NOV 2000', 'from Nobyembre 2000'],
            ['AFT NOV 2000', 'after Nobyembre 2000'],
            ['BEF NOV 2000', 'before Nobyembre 2000'],
            ['15 DEC 2000', '15 Disyembre 2000'],
            ['DEC 2000', 'Disyembre 2000'],
            ['ABT DEC 2000', 'about Disyembre 2000'],
            ['FROM DEC 2000', 'from Disyembre 2000'],
            ['AFT DEC 2000', 'after Disyembre 2000'],
            ['BEF DEC 2000', 'before Disyembre 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'about 15 Enero 2000'],
            ['CAL 15 JAN 2000', 'calculated 15 Enero 2000'],
            ['EST 15 JAN 2000', 'estimated 15 Enero 2000'],
            ['BEF 15 JAN 2000', 'before 15 Enero 2000'],
            ['AFT 15 JAN 2000', 'after 15 Enero 2000'],
            ['FROM 15 JAN 2000', 'from 15 Enero 2000'],
            ['TO 15 JAN 2000', 'to 15 Enero 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between 15 Enero 2000 and 15 Pebrero 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from 15 Enero 2000 to 15 Pebrero 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 Enero 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Enero 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Enero 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about Enero 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from Enero 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after Enero 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before Enero 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Pebrero 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Pebrero 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about Pebrero 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from Pebrero 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after Pebrero 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before Pebrero 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Marso 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Marso 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about Marso 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from Marso 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after Marso 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before Marso 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 Abril 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Abril 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'Abril 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about Abril 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from Abril 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after Abril 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before Abril 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Mayo 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Mayo 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about Mayo 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from Mayo 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after Mayo 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before Mayo 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Hunyo 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Hunyo 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about Hunyo 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from Hunyo 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after Hunyo 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before Hunyo 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Hulyo 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Hulyo 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about Hulyo 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from Hulyo 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after Hulyo 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before Hulyo 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Agosto 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Agosto 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about Agosto 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from Agosto 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after Agosto 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before Agosto 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Setyembre 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Setyembre 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about Setyembre 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from Setyembre 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after Setyembre 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before Setyembre 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Oktubre 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Oktubre 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about Oktubre 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from Oktubre 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after Oktubre 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before Oktubre 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Nobyembre 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Nobyembre 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about Nobyembre 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from Nobyembre 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after Nobyembre 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before Nobyembre 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Disyembre 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Disyembre 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about Disyembre 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from Disyembre 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after Disyembre 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before Disyembre 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about 15 Enero 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15 Enero 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 Enero 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before 15 Enero 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after 15 Enero 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from 15 Enero 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15 Enero 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between 15 Enero 1700 ᴄᴇ and 15 Pebrero 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from 15 Enero 1700 ᴄᴇ to 15 Pebrero 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 Enero 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'about Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'from Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'after Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'before Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'about Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'from Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'after Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'before Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'about Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'from Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'after Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'before Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'about Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'from Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'after Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'before Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'about Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'from Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'after Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'before Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'about Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'from Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'after Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'before Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'about Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'from Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'after Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'before Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'about Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'from Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'after Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'before Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'about Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'from Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'after Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'before Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'about Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'from Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'after Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'before Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'about Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'from Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'after Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'before Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'about Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'from Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'after Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'before Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between 15 Tishrei 5765 and 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from 15 Tishrei 5765 to 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between 15 Vendémiaire An XII and 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from 15 Vendémiaire An XII to 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated 15 Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between 15 Muharram 1425 and 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from 15 Muharram 1425 to 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'about Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'from Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'after Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'before Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'about Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'from Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'after Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'before Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'about Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'from Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'after Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'before Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'about Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'from Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'after Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'before Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'about Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'from Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'after Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'before Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'about Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'from Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'after Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'before Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'about Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'from Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'after Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'before Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'about Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'from Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'after Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'before Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between 15 Farvardin 1384 and 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from 15 Farvardin 1384 to 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15 Farvardin 1384'],
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
        self::assertSame('one at two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two at three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one o two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two o three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife with son (born 2000) and daughter (born 1998)
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1970");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 FAMS @fson@\n1 BIRT\n2 DATE 1 JAN 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 1 JAN 1998");
        $child = self::unknown('c', "1 FAMC @fm@");

        // Husband's family
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMS @fbro@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1965");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1965");
        $youngerBro = self::male('yb', "1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1975");
        $youngerSis = self::female('ys', "1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1975");

        // Wife's family
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Nieces/nephews
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

        // Families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@\n1 CHIL @yb@\n1 CHIL @ys@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @cm@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cf@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH, $youngerBro, $youngerSis,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromSis, $nephewFromSis,
             $cousinM, $cousinF,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle],
            [$fm, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fgp]
        );

        // Parents
        self::assertRelationshipName('nanay', [$son, $fm, $wife]);
        self::assertRelationshipName('tatay', [$son, $fm, $husband]);
        // Children
        self::assertRelationshipName('anak na babae', [$husband, $fm, $daughter]);
        self::assertRelationshipName('anak na lalaki', [$husband, $fm, $son]);
        // Siblings — elder/younger
        self::assertRelationshipName('kuya', [$husband, $fp, $brotherOfH]);
        self::assertRelationshipName('ate', [$husband, $fp, $sisterOfH]);
        self::assertRelationshipName('nakababatang kapatid na lalaki', [$husband, $fp, $youngerBro]);
        self::assertRelationshipName('nakababatang kapatid na babae', [$husband, $fp, $youngerSis]);
        // Spouse
        self::assertRelationshipName('asawa', [$husband, $fm, $wife]);
        self::assertRelationshipName('asawa', [$wife, $fm, $husband]);
        // Grandparents
        self::assertRelationshipName('lolo', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('lola', [$son, $fm, $husband, $fp, $motherOfH]);
        // Grandchildren
        self::assertRelationshipName('apo na lalaki', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('apo na babae', [$fatherOfH, $fp, $husband, $fm, $daughter]);
        // Aunts/Uncles
        self::assertRelationshipName('tito', [$son, $fm, $husband, $fp, $brotherOfH]);
        self::assertRelationshipName('tita', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('tita', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('tito', [$son, $fm, $wife, $fw, $brotherOfW]);
        // Nieces/Nephews
        self::assertRelationshipName('pamangkin na babae', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('pamangkin na lalaki', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);
        // Cousins
        self::assertRelationshipName('pinsan na lalaki', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinM]);
        self::assertRelationshipName('pinsan na babae', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinF]);
        // In-laws (spouse's parents)
        self::assertRelationshipName('biyenang babae', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('biyenang lalaki', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('biyenang babae', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('biyenang lalaki', [$wife, $fm, $husband, $fp, $fatherOfH]);
        // In-laws (child's spouse)
        self::assertRelationshipName('manugang na babae', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('manugang na lalaki', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);
        // In-laws (spouse's siblings)
        self::assertRelationshipName('bayaw', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('hipag', [$husband, $fm, $wife, $fw, $sisterOfW]);
        // Great-grandparents (dynamic)
        self::assertRelationshipName('lola sa tuhod', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('lolo sa tuhod', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);
        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('tita', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('tito', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
