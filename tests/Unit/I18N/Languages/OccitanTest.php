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
use Fisharebest\Webtrees\I18N\Languages\Occitan;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Occitan::class)]
class OccitanTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Occitan();
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
        self::assertSame('oc', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('occitan', self::language()->endonym());
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
        self::assertSame('-123 456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-123 456,0789 %', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }



    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 genièr 2000'],
            ['JAN 2000', 'genièr 2000'],
            ['ABT JAN 2000', 'about genièr 2000'],
            ['FROM JAN 2000', 'de genièr 2000'],
            ['AFT JAN 2000', 'après genièr 2000'],
            ['BEF JAN 2000', 'before genièr 2000'],
            ['15 FEB 2000', '15 febrièr 2000'],
            ['FEB 2000', 'febrièr 2000'],
            ['ABT FEB 2000', 'about febrièr 2000'],
            ['FROM FEB 2000', 'de febrièr 2000'],
            ['AFT FEB 2000', 'après febrièr 2000'],
            ['BEF FEB 2000', 'before febrièr 2000'],
            ['15 MAR 2000', '15 març 2000'],
            ['MAR 2000', 'març 2000'],
            ['ABT MAR 2000', 'about març 2000'],
            ['FROM MAR 2000', 'de març 2000'],
            ['AFT MAR 2000', 'après març 2000'],
            ['BEF MAR 2000', 'before març 2000'],
            ['15 APR 2000', '15 abril 2000'],
            ['APR 2000', 'abril 2000'],
            ['ABT APR 2000', 'about abril 2000'],
            ['FROM APR 2000', 'd’abril 2000'],
            ['AFT APR 2000', 'après abril 2000'],
            ['BEF APR 2000', 'before abril 2000'],
            ['15 MAY 2000', '15 mai 2000'],
            ['MAY 2000', 'mai 2000'],
            ['ABT MAY 2000', 'about mai 2000'],
            ['FROM MAY 2000', 'de mai 2000'],
            ['AFT MAY 2000', 'après mai 2000'],
            ['BEF MAY 2000', 'before mai 2000'],
            ['15 JUN 2000', '15 junh 2000'],
            ['JUN 2000', 'junh 2000'],
            ['ABT JUN 2000', 'about junh 2000'],
            ['FROM JUN 2000', 'de junh 2000'],
            ['AFT JUN 2000', 'après junh 2000'],
            ['BEF JUN 2000', 'before junh 2000'],
            ['15 JUL 2000', '15 julhet 2000'],
            ['JUL 2000', 'julhet 2000'],
            ['ABT JUL 2000', 'about julhet 2000'],
            ['FROM JUL 2000', 'de julhet 2000'],
            ['AFT JUL 2000', 'après julhet 2000'],
            ['BEF JUL 2000', 'before julhet 2000'],
            ['15 AUG 2000', '15 agost 2000'],
            ['AUG 2000', 'agost 2000'],
            ['ABT AUG 2000', 'about agost 2000'],
            ['FROM AUG 2000', 'd’agost 2000'],
            ['AFT AUG 2000', 'après agost 2000'],
            ['BEF AUG 2000', 'before agost 2000'],
            ['15 SEP 2000', '15 setembre 2000'],
            ['SEP 2000', 'setembre 2000'],
            ['ABT SEP 2000', 'about setembre 2000'],
            ['FROM SEP 2000', 'de setembre 2000'],
            ['AFT SEP 2000', 'après setembre 2000'],
            ['BEF SEP 2000', 'before setembre 2000'],
            ['15 OCT 2000', '15 octobre 2000'],
            ['OCT 2000', 'octobre 2000'],
            ['ABT OCT 2000', 'about octobre 2000'],
            ['FROM OCT 2000', 'd’octobre 2000'],
            ['AFT OCT 2000', 'après octobre 2000'],
            ['BEF OCT 2000', 'before octobre 2000'],
            ['15 NOV 2000', '15 novembre 2000'],
            ['NOV 2000', 'novembre 2000'],
            ['ABT NOV 2000', 'about novembre 2000'],
            ['FROM NOV 2000', 'de novembre 2000'],
            ['AFT NOV 2000', 'après novembre 2000'],
            ['BEF NOV 2000', 'before novembre 2000'],
            ['15 DEC 2000', '15 decembre 2000'],
            ['DEC 2000', 'decembre 2000'],
            ['ABT DEC 2000', 'about decembre 2000'],
            ['FROM DEC 2000', 'de decembre 2000'],
            ['AFT DEC 2000', 'après decembre 2000'],
            ['BEF DEC 2000', 'before decembre 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'about 15 genièr 2000'],
            ['CAL 15 JAN 2000', 'calculated 15 genièr 2000'],
            ['EST 15 JAN 2000', 'estimated 15 genièr 2000'],
            ['BEF 15 JAN 2000', 'before 15 genièr 2000'],
            ['AFT 15 JAN 2000', 'après 15 genièr 2000'],
            ['FROM 15 JAN 2000', 'de 15 genièr 2000'],
            ['TO 15 JAN 2000', 'to 15 genièr 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between 15 genièr 2000 and 15 febrièr 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'de 15 genièr 2000 a 15 febrièr 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 genièr 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 genièr 1700 AC'],
            ['@#DJULIAN@ JAN 1700', 'genièr 1700 AC'],
            ['ABT @#DJULIAN@ JAN 1700', 'about genièr 1700 AC'],
            ['FROM @#DJULIAN@ JAN 1700', 'de genièr 1700 AC'],
            ['AFT @#DJULIAN@ JAN 1700', 'après genièr 1700 AC'],
            ['BEF @#DJULIAN@ JAN 1700', 'before genièr 1700 AC'],
            ['@#DJULIAN@ 15 FEB 1700', '15 febrièr 1700 AC'],
            ['@#DJULIAN@ FEB 1700', 'febrièr 1700 AC'],
            ['ABT @#DJULIAN@ FEB 1700', 'about febrièr 1700 AC'],
            ['FROM @#DJULIAN@ FEB 1700', 'de febrièr 1700 AC'],
            ['AFT @#DJULIAN@ FEB 1700', 'après febrièr 1700 AC'],
            ['BEF @#DJULIAN@ FEB 1700', 'before febrièr 1700 AC'],
            ['@#DJULIAN@ 15 MAR 1700', '15 març 1700 AC'],
            ['@#DJULIAN@ MAR 1700', 'març 1700 AC'],
            ['ABT @#DJULIAN@ MAR 1700', 'about març 1700 AC'],
            ['FROM @#DJULIAN@ MAR 1700', 'de març 1700 AC'],
            ['AFT @#DJULIAN@ MAR 1700', 'après març 1700 AC'],
            ['BEF @#DJULIAN@ MAR 1700', 'before març 1700 AC'],
            ['@#DJULIAN@ 15 APR 1700', '15 abril 1700 AC'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 abril 1645/46 AC'],
            ['@#DJULIAN@ APR 1700', 'abril 1700 AC'],
            ['ABT @#DJULIAN@ APR 1700', 'about abril 1700 AC'],
            ['FROM @#DJULIAN@ APR 1700', 'd’abril 1700 AC'],
            ['AFT @#DJULIAN@ APR 1700', 'après abril 1700 AC'],
            ['BEF @#DJULIAN@ APR 1700', 'before abril 1700 AC'],
            ['@#DJULIAN@ 15 MAY 1700', '15 mai 1700 AC'],
            ['@#DJULIAN@ MAY 1700', 'mai 1700 AC'],
            ['ABT @#DJULIAN@ MAY 1700', 'about mai 1700 AC'],
            ['FROM @#DJULIAN@ MAY 1700', 'de mai 1700 AC'],
            ['AFT @#DJULIAN@ MAY 1700', 'après mai 1700 AC'],
            ['BEF @#DJULIAN@ MAY 1700', 'before mai 1700 AC'],
            ['@#DJULIAN@ 15 JUN 1700', '15 junh 1700 AC'],
            ['@#DJULIAN@ JUN 1700', 'junh 1700 AC'],
            ['ABT @#DJULIAN@ JUN 1700', 'about junh 1700 AC'],
            ['FROM @#DJULIAN@ JUN 1700', 'de junh 1700 AC'],
            ['AFT @#DJULIAN@ JUN 1700', 'après junh 1700 AC'],
            ['BEF @#DJULIAN@ JUN 1700', 'before junh 1700 AC'],
            ['@#DJULIAN@ 15 JUL 1700', '15 julhet 1700 AC'],
            ['@#DJULIAN@ JUL 1700', 'julhet 1700 AC'],
            ['ABT @#DJULIAN@ JUL 1700', 'about julhet 1700 AC'],
            ['FROM @#DJULIAN@ JUL 1700', 'de julhet 1700 AC'],
            ['AFT @#DJULIAN@ JUL 1700', 'après julhet 1700 AC'],
            ['BEF @#DJULIAN@ JUL 1700', 'before julhet 1700 AC'],
            ['@#DJULIAN@ 15 AUG 1700', '15 agost 1700 AC'],
            ['@#DJULIAN@ AUG 1700', 'agost 1700 AC'],
            ['ABT @#DJULIAN@ AUG 1700', 'about agost 1700 AC'],
            ['FROM @#DJULIAN@ AUG 1700', 'd’agost 1700 AC'],
            ['AFT @#DJULIAN@ AUG 1700', 'après agost 1700 AC'],
            ['BEF @#DJULIAN@ AUG 1700', 'before agost 1700 AC'],
            ['@#DJULIAN@ 15 SEP 1700', '15 setembre 1700 AC'],
            ['@#DJULIAN@ SEP 1700', 'setembre 1700 AC'],
            ['ABT @#DJULIAN@ SEP 1700', 'about setembre 1700 AC'],
            ['FROM @#DJULIAN@ SEP 1700', 'de setembre 1700 AC'],
            ['AFT @#DJULIAN@ SEP 1700', 'après setembre 1700 AC'],
            ['BEF @#DJULIAN@ SEP 1700', 'before setembre 1700 AC'],
            ['@#DJULIAN@ 15 OCT 1700', '15 octobre 1700 AC'],
            ['@#DJULIAN@ OCT 1700', 'octobre 1700 AC'],
            ['ABT @#DJULIAN@ OCT 1700', 'about octobre 1700 AC'],
            ['FROM @#DJULIAN@ OCT 1700', 'd’octobre 1700 AC'],
            ['AFT @#DJULIAN@ OCT 1700', 'après octobre 1700 AC'],
            ['BEF @#DJULIAN@ OCT 1700', 'before octobre 1700 AC'],
            ['@#DJULIAN@ 15 NOV 1700', '15 novembre 1700 AC'],
            ['@#DJULIAN@ NOV 1700', 'novembre 1700 AC'],
            ['ABT @#DJULIAN@ NOV 1700', 'about novembre 1700 AC'],
            ['FROM @#DJULIAN@ NOV 1700', 'de novembre 1700 AC'],
            ['AFT @#DJULIAN@ NOV 1700', 'après novembre 1700 AC'],
            ['BEF @#DJULIAN@ NOV 1700', 'before novembre 1700 AC'],
            ['@#DJULIAN@ 15 DEC 1700', '15 decembre 1700 AC'],
            ['@#DJULIAN@ DEC 1700', 'decembre 1700 AC'],
            ['ABT @#DJULIAN@ DEC 1700', 'about decembre 1700 AC'],
            ['FROM @#DJULIAN@ DEC 1700', 'de decembre 1700 AC'],
            ['AFT @#DJULIAN@ DEC 1700', 'après decembre 1700 AC'],
            ['BEF @#DJULIAN@ DEC 1700', 'before decembre 1700 AC'],
            ['@#DJULIAN@ 1700', '1700 AC'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about 15 genièr 1700 AC'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15 genièr 1700 AC'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 genièr 1700 AC'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before 15 genièr 1700 AC'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'après 15 genièr 1700 AC'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'de 15 genièr 1700 AC'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15 genièr 1700 AC'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between 15 genièr 1700 AC and 15 febrièr 1700 AC'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'de 15 genièr 1700 AC a 15 febrièr 1700 AC'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 genièr 1700 AC'],
            ['@#DHEBREW@ 15 TSH 5765', '15 tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'about tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'de tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'après tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'before tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'about heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'de heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'après heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'before heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'about kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'de kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'après kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'before kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'about tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'de tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'après tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'before tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'about shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'de shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'après shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'before shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'about adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'd’adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'après adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'before adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'about adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'd’adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'après adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'before adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'about nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'de nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'après nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'before nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'about iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'd’iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'après iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'before iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'about sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'de sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'après sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'before sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'de tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'après tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 av 5765'],
            ['@#DHEBREW@ AAV 5765', 'av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'about av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'd’av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'après av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'before av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'about elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'd’elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'après elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'before elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about 15 tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15 tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before 15 tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'après 15 tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'de 15 tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15 tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between 15 tishrei 5765 and 15 heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'de 15 tishrei 5765 a 15 heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'de vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'après vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'de brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'après brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'de frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'après frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'de nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'après nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'de pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'après pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'de ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'après ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'de germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'après germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'de floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'après floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'de prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'après prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'de messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'après messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'de thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'après thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'de fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'après fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'de jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'après jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about 15 vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15 vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before 15 vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'après 15 vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'de 15 vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15 vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between 15 vendémiaire An XII and 15 brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'de 15 vendémiaire An XII a 15 brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'de muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'après muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'de safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'après safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 rabi al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'rabi al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about rabi al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'de rabi al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'après rabi al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before rabi al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 rabi al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'rabi al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about rabi al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'de rabi al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'après rabi al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before rabi al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 jumada-al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'jumada-al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about jumada-al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'de jumada-al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'après jumada-al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before jumada-al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 jumada-al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'jumada-al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about jumada-al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'de jumada-al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'après jumada-al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before jumada-al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'de rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'après rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'de sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'après sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'de ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'après ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'de shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'après shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 dhu-al-qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'dhu-al-qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about dhu-al-qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'de dhu-al-qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'après dhu-al-qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before dhu-al-qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'de 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'après 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about 15 muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated 15 muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15 muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before 15 muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'après 15 muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'de 15 muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to 15 muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between 15 muharram 1425 and 15 safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'de 15 muharram 1425 a 15 safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15 muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'about farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'de farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'après farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'before farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'd’ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'après ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'about khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'de khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'après khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'before khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 tir 1384'],
            ['@#DJALALI@ TIR 1384', 'tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'about tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'de tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'après tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'before tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'about mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'de mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'après mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'before mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'de shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'après shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'about mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'de mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'après mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'before mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'about aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'd’aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'après aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'before aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'about azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'd’azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'après azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'before azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 dey 1384'],
            ['@#DJALALI@ DEY 1384', 'dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'about dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'de dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'après dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'before dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'de bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'après bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'd’esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'après esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about 15 farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated 15 farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15 farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before 15 farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'après 15 farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'de 15 farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to 15 farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between 15 farvardin 1384 and 15 ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'de 15 farvardin 1384 a 15 ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15 farvardin 1384'],
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
        self::assertSame('one o two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two o three', $language->formatListOr(['one', 'two', 'three']));
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
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
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
        self::assertRelationshipNames('espòsa', 'espòs', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-espòs', 'ex-espòsa', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('promesa', 'promès', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('maire', 'filh', [$son, $fm, $wife]);
        self::assertRelationshipNames('paire', 'filh', [$son, $fm, $husband]);
        self::assertRelationshipNames('maire', 'filha', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('maire adoptiva', 'filh adoptiu', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('paire adoptiu', 'filh adoptiu', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames("maire d'acòlhiment", "filh d'acòlhiment", [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames("paire d'acòlhiment", "filh d'acòlhiment", [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('sòrre pichòta', 'frair grand', [$son, $fm, $daughter]);
        self::assertRelationshipNames('frair grand', 'sòrre pichòta', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipNames('mièg-frair', 'mièja-sòrre', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('pairastra', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('filhastra', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('sògra', 'gendre', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('sògre', 'gendre', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('nòra', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('aviòla', 'petit-filh', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('aviol', 'petit-filh', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('petita-filha', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('besaviol', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('besaviòla', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tanta', 'nebot', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('òncle', 'nebot', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('nebòda', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nebot', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('cosina', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('cosin', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('bestanta', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('besòncle', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
