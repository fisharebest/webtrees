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
use Fisharebest\Webtrees\I18N\Languages\Galician;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Galician::class)]
class GalicianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Galician();
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
        self::assertSame('gl', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('galego', self::language()->endonym());
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
        self::assertSame('-123.456,0789 %', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 xaneiro 2000'],
            ['JAN 2000', 'xaneiro 2000'],
            ['ABT JAN 2000', 'about xaneiro 2000'],
            ['FROM JAN 2000', 'from xaneiro 2000'],
            ['AFT JAN 2000', 'after xaneiro 2000'],
            ['BEF JAN 2000', 'before xaneiro 2000'],
            ['15 FEB 2000', '15 febreiro 2000'],
            ['FEB 2000', 'febreiro 2000'],
            ['ABT FEB 2000', 'about febreiro 2000'],
            ['FROM FEB 2000', 'from febreiro 2000'],
            ['AFT FEB 2000', 'after febreiro 2000'],
            ['BEF FEB 2000', 'before febreiro 2000'],
            ['15 MAR 2000', '15 marzo 2000'],
            ['MAR 2000', 'marzo 2000'],
            ['ABT MAR 2000', 'about marzo 2000'],
            ['FROM MAR 2000', 'from marzo 2000'],
            ['AFT MAR 2000', 'after marzo 2000'],
            ['BEF MAR 2000', 'before marzo 2000'],
            ['15 APR 2000', '15 abril 2000'],
            ['APR 2000', 'abril 2000'],
            ['ABT APR 2000', 'about abril 2000'],
            ['FROM APR 2000', 'from abril 2000'],
            ['AFT APR 2000', 'after abril 2000'],
            ['BEF APR 2000', 'before abril 2000'],
            ['15 MAY 2000', '15 maio 2000'],
            ['MAY 2000', 'maio 2000'],
            ['ABT MAY 2000', 'about maio 2000'],
            ['FROM MAY 2000', 'from maio 2000'],
            ['AFT MAY 2000', 'after maio 2000'],
            ['BEF MAY 2000', 'before maio 2000'],
            ['15 JUN 2000', '15 xuño 2000'],
            ['JUN 2000', 'xuño 2000'],
            ['ABT JUN 2000', 'about xuño 2000'],
            ['FROM JUN 2000', 'from xuño 2000'],
            ['AFT JUN 2000', 'after xuño 2000'],
            ['BEF JUN 2000', 'before xuño 2000'],
            ['15 JUL 2000', '15 xullo 2000'],
            ['JUL 2000', 'xullo 2000'],
            ['ABT JUL 2000', 'about xullo 2000'],
            ['FROM JUL 2000', 'from xullo 2000'],
            ['AFT JUL 2000', 'after xullo 2000'],
            ['BEF JUL 2000', 'before xullo 2000'],
            ['15 AUG 2000', '15 agosto 2000'],
            ['AUG 2000', 'agosto 2000'],
            ['ABT AUG 2000', 'about agosto 2000'],
            ['FROM AUG 2000', 'from agosto 2000'],
            ['AFT AUG 2000', 'after agosto 2000'],
            ['BEF AUG 2000', 'before agosto 2000'],
            ['15 SEP 2000', '15 setembro 2000'],
            ['SEP 2000', 'setembro 2000'],
            ['ABT SEP 2000', 'about setembro 2000'],
            ['FROM SEP 2000', 'from setembro 2000'],
            ['AFT SEP 2000', 'after setembro 2000'],
            ['BEF SEP 2000', 'before setembro 2000'],
            ['15 OCT 2000', '15 outubro 2000'],
            ['OCT 2000', 'outubro 2000'],
            ['ABT OCT 2000', 'about outubro 2000'],
            ['FROM OCT 2000', 'from outubro 2000'],
            ['AFT OCT 2000', 'after outubro 2000'],
            ['BEF OCT 2000', 'before outubro 2000'],
            ['15 NOV 2000', '15 novembro 2000'],
            ['NOV 2000', 'novembro 2000'],
            ['ABT NOV 2000', 'about novembro 2000'],
            ['FROM NOV 2000', 'from novembro 2000'],
            ['AFT NOV 2000', 'after novembro 2000'],
            ['BEF NOV 2000', 'before novembro 2000'],
            ['15 DEC 2000', '15 decembro 2000'],
            ['DEC 2000', 'decembro 2000'],
            ['ABT DEC 2000', 'about decembro 2000'],
            ['FROM DEC 2000', 'from decembro 2000'],
            ['AFT DEC 2000', 'after decembro 2000'],
            ['BEF DEC 2000', 'before decembro 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'about 15 xaneiro 2000'],
            ['CAL 15 JAN 2000', 'calculated 15 xaneiro 2000'],
            ['EST 15 JAN 2000', 'estimated 15 xaneiro 2000'],
            ['BEF 15 JAN 2000', 'before 15 xaneiro 2000'],
            ['AFT 15 JAN 2000', 'after 15 xaneiro 2000'],
            ['FROM 15 JAN 2000', 'from 15 xaneiro 2000'],
            ['TO 15 JAN 2000', 'to 15 xaneiro 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between 15 xaneiro 2000 and 15 febreiro 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from 15 xaneiro 2000 to 15 febreiro 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 xaneiro 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 xaneiro 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'xaneiro 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about xaneiro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from xaneiro 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after xaneiro 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before xaneiro 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 febreiro 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'febreiro 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about febreiro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from febreiro 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after febreiro 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before febreiro 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 marzo 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'marzo 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about marzo 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from marzo 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after marzo 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before marzo 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 abril 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 abril 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'abril 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about abril 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from abril 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after abril 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before abril 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 maio 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'maio 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about maio 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from maio 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after maio 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before maio 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 xuño 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'xuño 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about xuño 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from xuño 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after xuño 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before xuño 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 xullo 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'xullo 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about xullo 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from xullo 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after xullo 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before xullo 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 agosto 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'agosto 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about agosto 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from agosto 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after agosto 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before agosto 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 setembro 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'setembro 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about setembro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from setembro 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after setembro 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before setembro 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 outubro 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'outubro 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about outubro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from outubro 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after outubro 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before outubro 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 novembro 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'novembro 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about novembro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from novembro 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after novembro 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before novembro 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 decembro 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'decembro 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about decembro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from decembro 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after decembro 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before decembro 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about 15 xaneiro 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15 xaneiro 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 xaneiro 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before 15 xaneiro 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after 15 xaneiro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from 15 xaneiro 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15 xaneiro 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between 15 xaneiro 1700 ᴄᴇ and 15 febreiro 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from 15 xaneiro 1700 ᴄᴇ to 15 febreiro 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 xaneiro 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'about tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'from tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'after tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'before tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 heshván 5765'],
            ['@#DHEBREW@ CSH 5765', 'heshván 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'about heshván 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'from heshván 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'after heshván 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'before heshván 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'about kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'from kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'after kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'before kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'about tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'from tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'after tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'before tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'about shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'from shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'after shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'before shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'about adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'from adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'after adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'before adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'about adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'from adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'after adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'before adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 nisán 5765'],
            ['@#DHEBREW@ NSN 5765', 'nisán 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'about nisán 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'from nisán 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'after nisán 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'before nisán 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'about iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'from iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'after iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'before iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 siván 5765'],
            ['@#DHEBREW@ SVN 5765', 'siván 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'about siván 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'from siván 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'after siván 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'before siván 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 av 5765'],
            ['@#DHEBREW@ AAV 5765', 'av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'about av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'from av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'after av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'before av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'about elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'from elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'after elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'before elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about 15 tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15 tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before 15 tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after 15 tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from 15 tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15 tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between 15 tishrei 5765 and 15 heshván 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from 15 tishrei 5765 to 15 heshván 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 vendimario An XII'],
            ['@#DFRENCH R@ VEND 12', 'vendimario An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about vendimario An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from vendimario An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after vendimario An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before vendimario An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 brumario An XII'],
            ['@#DFRENCH R@ BRUM 12', 'brumario An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about brumario An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from brumario An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after brumario An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before brumario An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 frimario An XII'],
            ['@#DFRENCH R@ FRIM 12', 'frimario An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about frimario An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from frimario An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after frimario An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before frimario An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 nivoso An XII'],
            ['@#DFRENCH R@ NIVO 12', 'nivoso An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about nivoso An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from nivoso An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after nivoso An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before nivoso An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 pluvioso An XII'],
            ['@#DFRENCH R@ PLUV 12', 'pluvioso An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about pluvioso An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from pluvioso An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after pluvioso An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before pluvioso An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ventoso An XII'],
            ['@#DFRENCH R@ VENT 12', 'ventoso An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about ventoso An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from ventoso An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after ventoso An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before ventoso An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 xerminal An XII'],
            ['@#DFRENCH R@ GERM 12', 'xerminal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about xerminal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from xerminal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after xerminal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before xerminal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 floreal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'floreal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about floreal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from floreal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after floreal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before floreal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 pradial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'pradial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about pradial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from pradial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after pradial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before pradial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 mesidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'mesidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about mesidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from mesidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after mesidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before mesidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 termidor An XII'],
            ['@#DFRENCH R@ THER 12', 'termidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about termidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from termidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after termidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before termidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 frutidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'frutidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about frutidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from frutidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after frutidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before frutidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 días complementarios An XII'],
            ['@#DFRENCH R@ COMP 12', 'días complementarios An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about días complementarios An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from días complementarios An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after días complementarios An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before días complementarios An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about 15 vendimario An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15 vendimario An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 vendimario An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before 15 vendimario An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after 15 vendimario An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from 15 vendimario An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15 vendimario An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between 15 vendimario An XII and 15 brumario An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from 15 vendimario An XII to 15 brumario An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 vendimario An XII'],
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
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabiʿ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabiʿ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about Rabiʿ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Rabiʿ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after Rabiʿ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before Rabiʿ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabiʿ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabiʿ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about Rabiʿ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Rabiʿ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after Rabiʿ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before Rabiʿ al-thani 1425'],
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
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Shaʿbán 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Shaʿbán 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about Shaʿbán 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Shaʿbán 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after Shaʿbán 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before Shaʿbán 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadán 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadán 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about Ramadán 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Ramadán 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after Ramadán 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before Ramadán 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qiʿdah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qiʿdah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about Dhu al-Qiʿdah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Dhu al-Qiʿdah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after Dhu al-Qiʿdah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before Dhu al-Qiʿdah 1425'],
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
        self::assertSame('one e two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two e three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one ou two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two ou three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
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

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $adoptedSon, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('esposa', 'marido', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-marido', 'ex-esposa', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('noiva', 'noivo', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('nai', 'fillo', [$son, $fm, $wife]);
        self::assertRelationshipNames('pai', 'fillo', [$son, $fm, $husband]);
        self::assertRelationshipNames('nai', 'filla', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('nai adoptiva', 'fillo adoptivo', [$adoptedSon, $fd, $wife]);

        // Siblings
        self::assertRelationshipNames('irmá menor', 'irmán maior', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('medio irmán', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('padrastro', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('enteada', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('sogra', 'xenro', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('sogro', 'xenro', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('nora', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('avoa', 'neto', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('avó', 'neto', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('neta', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic) - n-1=2 → "tris"
        self::assertRelationshipName('trisavó', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('trisavoa', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tía', 'sobriño', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('tío', 'sobriño', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('sobriña', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('sobriño', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('curmá', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('curmán', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) - n=2, great(n-1=1) → "bis"
        self::assertRelationshipName('bistía', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('bistío', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
