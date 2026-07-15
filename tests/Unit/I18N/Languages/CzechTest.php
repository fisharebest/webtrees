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
use Fisharebest\Webtrees\I18N\Languages\Czech;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Czech::class)]
class CzechTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Czech();
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
        self::assertSame(['A', 'Á', 'B', 'C', 'Č', 'D', 'Ď', 'E', 'É', 'Ě', 'F', 'G', 'H', 'CH', 'I', 'Í', 'J', 'K', 'L', 'M', 'N', 'Ň', 'O', 'Ó', 'P', 'Q', 'R', 'Ř', 'S', 'Š', 'T', 'Ť', 'U', 'Ú', 'Ů', 'V', 'W', 'X', 'Y', 'Ý', 'Z', 'Ž'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('cs', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('čeština', self::language()->endonym());
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
        self::assertSame('-123 456,0789 %', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15. ledna 2000'],
            ['JAN 2000', 'leden 2000'],
            ['ABT JAN 2000', 'kolem ledna 2000'],
            ['FROM JAN 2000', 'od ledna 2000'],
            ['AFT JAN 2000', 'po lednu 2000'],
            ['BEF JAN 2000', 'před lednem 2000'],
            ['15 FEB 2000', '15. února 2000'],
            ['FEB 2000', 'únor 2000'],
            ['ABT FEB 2000', 'kolem února 2000'],
            ['FROM FEB 2000', 'od února 2000'],
            ['AFT FEB 2000', 'po únoru 2000'],
            ['BEF FEB 2000', 'před únorem 2000'],
            ['15 MAR 2000', '15. března 2000'],
            ['MAR 2000', 'březen 2000'],
            ['ABT MAR 2000', 'kolem března 2000'],
            ['FROM MAR 2000', 'od března 2000'],
            ['AFT MAR 2000', 'po březnu 2000'],
            ['BEF MAR 2000', 'před březnem 2000'],
            ['15 APR 2000', '15. dubna 2000'],
            ['APR 2000', 'duben 2000'],
            ['ABT APR 2000', 'kolem dubna 2000'],
            ['FROM APR 2000', 'od dubna 2000'],
            ['AFT APR 2000', 'po dubnu 2000'],
            ['BEF APR 2000', 'před dubnem 2000'],
            ['15 MAY 2000', '15. května 2000'],
            ['MAY 2000', 'květen 2000'],
            ['ABT MAY 2000', 'kolem května 2000'],
            ['FROM MAY 2000', 'od května 2000'],
            ['AFT MAY 2000', 'po květnu 2000'],
            ['BEF MAY 2000', 'před květnem 2000'],
            ['15 JUN 2000', '15. června 2000'],
            ['JUN 2000', 'červen 2000'],
            ['ABT JUN 2000', 'kolem června 2000'],
            ['FROM JUN 2000', 'od června 2000'],
            ['AFT JUN 2000', 'po červnu 2000'],
            ['BEF JUN 2000', 'před červnem 2000'],
            ['15 JUL 2000', '15. července 2000'],
            ['JUL 2000', 'červenec 2000'],
            ['ABT JUL 2000', 'kolem července 2000'],
            ['FROM JUL 2000', 'od července 2000'],
            ['AFT JUL 2000', 'po červenci 2000'],
            ['BEF JUL 2000', 'před červencem 2000'],
            ['15 AUG 2000', '15. srpna 2000'],
            ['AUG 2000', 'srpen 2000'],
            ['ABT AUG 2000', 'kolem srpna 2000'],
            ['FROM AUG 2000', 'od srpna 2000'],
            ['AFT AUG 2000', 'po srpnu 2000'],
            ['BEF AUG 2000', 'před srpnem 2000'],
            ['15 SEP 2000', '15. září 2000'],
            ['SEP 2000', 'září 2000'],
            ['ABT SEP 2000', 'kolem září 2000'],
            ['FROM SEP 2000', 'od září 2000'],
            ['AFT SEP 2000', 'po září 2000'],
            ['BEF SEP 2000', 'před zářím 2000'],
            ['15 OCT 2000', '15. října 2000'],
            ['OCT 2000', 'říjen 2000'],
            ['ABT OCT 2000', 'kolem října 2000'],
            ['FROM OCT 2000', 'od října 2000'],
            ['AFT OCT 2000', 'po říjnu 2000'],
            ['BEF OCT 2000', 'před říjnem 2000'],
            ['15 NOV 2000', '15. listopadu 2000'],
            ['NOV 2000', 'listopad 2000'],
            ['ABT NOV 2000', 'kolem listopadu 2000'],
            ['FROM NOV 2000', 'od listopadu 2000'],
            ['AFT NOV 2000', 'po listopadu 2000'],
            ['BEF NOV 2000', 'před listopadem 2000'],
            ['15 DEC 2000', '15. prosince 2000'],
            ['DEC 2000', 'prosinec 2000'],
            ['ABT DEC 2000', 'kolem prosince 2000'],
            ['FROM DEC 2000', 'od prosince 2000'],
            ['AFT DEC 2000', 'po prosinci 2000'],
            ['BEF DEC 2000', 'před prosincem 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'kolem 15. ledna 2000'],
            ['CAL 15 JAN 2000', 'dopočítáno 15. ledna 2000'],
            ['EST 15 JAN 2000', 'odhadem 15. ledna 2000'],
            ['BEF 15 JAN 2000', 'před 15. ledna 2000'],
            ['AFT 15 JAN 2000', 'po 15. ledna 2000'],
            ['FROM 15 JAN 2000', 'od 15. ledna 2000'],
            ['TO 15 JAN 2000', 'do 15. ledna 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'mezi 15. ledna 2000 a 15. února 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'od 15. ledna 2000 do 15. února 2000'],
            ['INT 15 JAN 2000', 'interpretován 15. ledna 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. ledna 1700 n. l.'],
            ['@#DJULIAN@ JAN 1700', 'leden 1700 n. l.'],
            ['ABT @#DJULIAN@ JAN 1700', 'kolem ledna 1700 n. l.'],
            ['FROM @#DJULIAN@ JAN 1700', 'od ledna 1700 n. l.'],
            ['AFT @#DJULIAN@ JAN 1700', 'po lednu 1700 n. l.'],
            ['BEF @#DJULIAN@ JAN 1700', 'před lednem 1700 n. l.'],
            ['@#DJULIAN@ 15 FEB 1700', '15. února 1700 n. l.'],
            ['@#DJULIAN@ FEB 1700', 'únor 1700 n. l.'],
            ['ABT @#DJULIAN@ FEB 1700', 'kolem února 1700 n. l.'],
            ['FROM @#DJULIAN@ FEB 1700', 'od února 1700 n. l.'],
            ['AFT @#DJULIAN@ FEB 1700', 'po únoru 1700 n. l.'],
            ['BEF @#DJULIAN@ FEB 1700', 'před únorem 1700 n. l.'],
            ['@#DJULIAN@ 15 MAR 1700', '15. března 1700 n. l.'],
            ['@#DJULIAN@ MAR 1700', 'březen 1700 n. l.'],
            ['ABT @#DJULIAN@ MAR 1700', 'kolem března 1700 n. l.'],
            ['FROM @#DJULIAN@ MAR 1700', 'od března 1700 n. l.'],
            ['AFT @#DJULIAN@ MAR 1700', 'po březnu 1700 n. l.'],
            ['BEF @#DJULIAN@ MAR 1700', 'před březnem 1700 n. l.'],
            ['@#DJULIAN@ 15 APR 1700', '15. dubna 1700 n. l.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. dubna 1645/46 n. l.'],
            ['@#DJULIAN@ APR 1700', 'duben 1700 n. l.'],
            ['ABT @#DJULIAN@ APR 1700', 'kolem dubna 1700 n. l.'],
            ['FROM @#DJULIAN@ APR 1700', 'od dubna 1700 n. l.'],
            ['AFT @#DJULIAN@ APR 1700', 'po dubnu 1700 n. l.'],
            ['BEF @#DJULIAN@ APR 1700', 'před dubnem 1700 n. l.'],
            ['@#DJULIAN@ 15 MAY 1700', '15. května 1700 n. l.'],
            ['@#DJULIAN@ MAY 1700', 'květen 1700 n. l.'],
            ['ABT @#DJULIAN@ MAY 1700', 'kolem května 1700 n. l.'],
            ['FROM @#DJULIAN@ MAY 1700', 'od května 1700 n. l.'],
            ['AFT @#DJULIAN@ MAY 1700', 'po květnu 1700 n. l.'],
            ['BEF @#DJULIAN@ MAY 1700', 'před květnem 1700 n. l.'],
            ['@#DJULIAN@ 15 JUN 1700', '15. června 1700 n. l.'],
            ['@#DJULIAN@ JUN 1700', 'červen 1700 n. l.'],
            ['ABT @#DJULIAN@ JUN 1700', 'kolem června 1700 n. l.'],
            ['FROM @#DJULIAN@ JUN 1700', 'od června 1700 n. l.'],
            ['AFT @#DJULIAN@ JUN 1700', 'po červnu 1700 n. l.'],
            ['BEF @#DJULIAN@ JUN 1700', 'před červnem 1700 n. l.'],
            ['@#DJULIAN@ 15 JUL 1700', '15. července 1700 n. l.'],
            ['@#DJULIAN@ JUL 1700', 'červenec 1700 n. l.'],
            ['ABT @#DJULIAN@ JUL 1700', 'kolem července 1700 n. l.'],
            ['FROM @#DJULIAN@ JUL 1700', 'od července 1700 n. l.'],
            ['AFT @#DJULIAN@ JUL 1700', 'po červenci 1700 n. l.'],
            ['BEF @#DJULIAN@ JUL 1700', 'před červencem 1700 n. l.'],
            ['@#DJULIAN@ 15 AUG 1700', '15. srpna 1700 n. l.'],
            ['@#DJULIAN@ AUG 1700', 'srpen 1700 n. l.'],
            ['ABT @#DJULIAN@ AUG 1700', 'kolem srpna 1700 n. l.'],
            ['FROM @#DJULIAN@ AUG 1700', 'od srpna 1700 n. l.'],
            ['AFT @#DJULIAN@ AUG 1700', 'po srpnu 1700 n. l.'],
            ['BEF @#DJULIAN@ AUG 1700', 'před srpnem 1700 n. l.'],
            ['@#DJULIAN@ 15 SEP 1700', '15. září 1700 n. l.'],
            ['@#DJULIAN@ SEP 1700', 'září 1700 n. l.'],
            ['ABT @#DJULIAN@ SEP 1700', 'kolem září 1700 n. l.'],
            ['FROM @#DJULIAN@ SEP 1700', 'od září 1700 n. l.'],
            ['AFT @#DJULIAN@ SEP 1700', 'po září 1700 n. l.'],
            ['BEF @#DJULIAN@ SEP 1700', 'před zářím 1700 n. l.'],
            ['@#DJULIAN@ 15 OCT 1700', '15. října 1700 n. l.'],
            ['@#DJULIAN@ OCT 1700', 'říjen 1700 n. l.'],
            ['ABT @#DJULIAN@ OCT 1700', 'kolem října 1700 n. l.'],
            ['FROM @#DJULIAN@ OCT 1700', 'od října 1700 n. l.'],
            ['AFT @#DJULIAN@ OCT 1700', 'po říjnu 1700 n. l.'],
            ['BEF @#DJULIAN@ OCT 1700', 'před říjnem 1700 n. l.'],
            ['@#DJULIAN@ 15 NOV 1700', '15. listopadu 1700 n. l.'],
            ['@#DJULIAN@ NOV 1700', 'listopad 1700 n. l.'],
            ['ABT @#DJULIAN@ NOV 1700', 'kolem listopadu 1700 n. l.'],
            ['FROM @#DJULIAN@ NOV 1700', 'od listopadu 1700 n. l.'],
            ['AFT @#DJULIAN@ NOV 1700', 'po listopadu 1700 n. l.'],
            ['BEF @#DJULIAN@ NOV 1700', 'před listopadem 1700 n. l.'],
            ['@#DJULIAN@ 15 DEC 1700', '15. prosince 1700 n. l.'],
            ['@#DJULIAN@ DEC 1700', 'prosinec 1700 n. l.'],
            ['ABT @#DJULIAN@ DEC 1700', 'kolem prosince 1700 n. l.'],
            ['FROM @#DJULIAN@ DEC 1700', 'od prosince 1700 n. l.'],
            ['AFT @#DJULIAN@ DEC 1700', 'po prosinci 1700 n. l.'],
            ['BEF @#DJULIAN@ DEC 1700', 'před prosincem 1700 n. l.'],
            ['@#DJULIAN@ 1700', '1700 n. l.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'kolem 15. ledna 1700 n. l.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'dopočítáno 15. ledna 1700 n. l.'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'odhadem 15. ledna 1700 n. l.'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'před 15. ledna 1700 n. l.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'po 15. ledna 1700 n. l.'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'od 15. ledna 1700 n. l.'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'do 15. ledna 1700 n. l.'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'mezi 15. ledna 1700 n. l. a 15. února 1700 n. l.'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'od 15. ledna 1700 n. l. do 15. února 1700 n. l.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretován 15. ledna 1700 n. l.'],
            ['@#DHEBREW@ 15 TSH 5765', '15. Tišri 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tišri 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'kolem Tišri 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'od Tišri 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'po Tišri 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'před Tišri 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. Chešvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Chešvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'kolem Chešvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'od Chešvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'po Chešvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'před Chešvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'kolem Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'od Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'po Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'před Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'kolem Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'od Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'po Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'před Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. Ševat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Ševat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'kolem Ševat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'od Ševat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'po Ševat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'před Ševat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'kolem Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'od Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'po Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'před Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'kolem Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'od Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'po Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'před Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. Nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'kolem Nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'od Nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'po Nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'před Nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Ijar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Ijar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'kolem Ijar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'od Ijar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'po Ijar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'před Ijar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'kolem Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'od Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'po Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'před Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'kolem Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'od Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'po Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'před Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'kolem Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'od Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'po Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'před Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'kolem Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'od Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'po Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'před Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'kolem 15. Tišri 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'dopočítáno 15. Tišri 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'odhadem 15. Tišri 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'před 15. Tišri 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'po 15. Tišri 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'od 15. Tišri 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'do 15. Tišri 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'mezi 15. Tišri 5765 a 15. Chešvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'od 15. Tišri 5765 do 15. Chešvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretován 15. Tišri 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'kolem Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'od Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'po Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'před Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'kolem Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'od Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'po Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'před Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'kolem Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'od Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'po Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'před Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'kolem Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'od Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'po Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'před Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'kolem Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'od Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'po Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'před Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'kolem Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'od Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'po Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'před Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'kolem Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'od Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'po Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'před Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'kolem Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'od Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'po Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'před Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'kolem Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'od Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'po Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'před Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'kolem Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'od Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'po Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'před Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'kolem Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'od Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'po Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'před Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'kolem Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'od Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'po Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'před Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. jours complémentaires (svátky) An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires (svátky) An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'kolem jours complémentaires (svátky) An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'od jours complémentaires (svátky) An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'po jours complémentaires (svátky) An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'před jours complémentaires (svátky) An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'kolem 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'dopočítáno 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'odhadem 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'před 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'po 15. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'od 15. Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'do 15. Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'mezi 15. Vendémiaire An XII a 15. Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'od 15. Vendémiaire An XII do 15. Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretován 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. al-muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'al-muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'kolem al-muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'od al-muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'po al-muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'před al-muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'kolem safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'od safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'po safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'před safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'kolem Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'od Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'po Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'před Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'kolem Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'od Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'po Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'před Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. džumádá l-úlá 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'džumádá l-úlá 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'kolem džumádá l-úlá 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'od džumádá l-úlá 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'po džumádá l-úlá 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'před džumádá l-úlá 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. džumádá l-áchira 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'džumádá l-áchira 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'kolem džumádá l-áchira 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'od džumádá l-áchira 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'po džumádá l-áchira 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'před džumádá l-áchira 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. radžab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'radžab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'kolem radžab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'od radžab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'po radžab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'před radžab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. ša’bán 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'ša’bán 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'kolem ša’bán 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'od ša’bán 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'po ša’bán 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'před ša’bán 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. ramadán 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ramadán 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'kolem ramadán 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'od ramadán 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'po ramadán 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'před ramadán 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. šauvál 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'šauvál 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'kolem šauvál 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'od šauvál 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'po šauvál 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'před šauvál 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. dhú l-ka’da 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'dhú l-ka’da 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'kolem dhú l-ka’da 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'od dhú l-ka’da 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'po dhú l-ka’da 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'před dhú l-ka’da 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'kolem 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'od 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'po 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'před 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'kolem 15. al-muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'dopočítáno 15. al-muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'odhadem 15. al-muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'před 15. al-muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'po 15. al-muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'od 15. al-muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'do 15. al-muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'mezi 15. al-muharram 1425 a 15. safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'od 15. al-muharram 1425 do 15. safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretován 15. al-muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'kolem Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'od Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'po Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'před Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'kolem Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'od Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'po Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'před Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'kolem Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'od Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'po Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'před Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'kolem Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'od Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'po Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'před Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'kolem Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'od Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'po Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'před Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'kolem Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'od Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'po Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'před Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'kolem Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'od Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'po Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'před Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'kolem Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'od Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'po Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'před Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'kolem Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'od Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'po Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'před Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'kolem Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'od Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'po Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'před Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'kolem Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'od Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'po Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'před Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'kolem Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'od Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'po Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'před Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'kolem 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'dopočítáno 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'odhadem 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'před 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'po 15. Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'od 15. Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'do 15. Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'mezi 15. Farvardin 1384 a 15. Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'od 15. Farvardin 1384 do 15. Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretován 15. Farvardin 1384'],
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
        self::assertSame('one nebo two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two nebo three', $language->formatListOr(['one', 'two', 'three']));
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
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @nb@\n1 CHIL @npb@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromSis, $nephewFromSis,
             $paternalGF, $paternalGM, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('manželka', 'manžel', [$husband, $fm, $wife]);
        self::assertRelationshipNames('bývalý manžel', 'bývalá manželka', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('snoubenka', 'snoubenec', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('matka', 'syn', [$son, $fm, $wife]);
        self::assertRelationshipNames('otec', 'syn', [$son, $fm, $husband]);
        self::assertRelationshipNames('matka', 'dcera', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('mladší sestra', 'starší bratr', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('nevlastní bratr', [$stepDaughter, $fd, $wife, $fm, $son]);

        // In-laws (wife's parents)
        self::assertRelationshipName('tchyně', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('tchán', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // Grandparents
        self::assertRelationshipName('babička', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('dědeček', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (fixed)
        self::assertRelationshipName('pradědeček', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('prababička', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipName('teta', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('strýc', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('neteř', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('synovec', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);
    }
}
