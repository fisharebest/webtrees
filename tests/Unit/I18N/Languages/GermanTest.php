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
use Fisharebest\Webtrees\I18N\Languages\German;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(German::class)]
class GermanTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new German();
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
        self::assertSame('de', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Deutsch', self::language()->endonym());
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
            ['15 JAN 2000', '15. Januar 2000'],
            ['JAN 2000', 'Januar 2000'],
            ['ABT JAN 2000', 'um Januar 2000'],
            ['FROM JAN 2000', 'von Januar 2000'],
            ['AFT JAN 2000', 'nach Januar 2000'],
            ['BEF JAN 2000', 'vor Januar 2000'],
            ['15 FEB 2000', '15. Februar 2000'],
            ['FEB 2000', 'Februar 2000'],
            ['ABT FEB 2000', 'um Februar 2000'],
            ['FROM FEB 2000', 'von Februar 2000'],
            ['AFT FEB 2000', 'nach Februar 2000'],
            ['BEF FEB 2000', 'vor Februar 2000'],
            ['15 MAR 2000', '15. März 2000'],
            ['MAR 2000', 'März 2000'],
            ['ABT MAR 2000', 'um März 2000'],
            ['FROM MAR 2000', 'von März 2000'],
            ['AFT MAR 2000', 'nach März 2000'],
            ['BEF MAR 2000', 'vor März 2000'],
            ['15 APR 2000', '15. April 2000'],
            ['APR 2000', 'April 2000'],
            ['ABT APR 2000', 'um April 2000'],
            ['FROM APR 2000', 'von April 2000'],
            ['AFT APR 2000', 'nach April 2000'],
            ['BEF APR 2000', 'vor April 2000'],
            ['15 MAY 2000', '15. Mai 2000'],
            ['MAY 2000', 'Mai 2000'],
            ['ABT MAY 2000', 'um Mai 2000'],
            ['FROM MAY 2000', 'von Mai 2000'],
            ['AFT MAY 2000', 'nach Mai 2000'],
            ['BEF MAY 2000', 'vor Mai 2000'],
            ['15 JUN 2000', '15. Juni 2000'],
            ['JUN 2000', 'Juni 2000'],
            ['ABT JUN 2000', 'um Juni 2000'],
            ['FROM JUN 2000', 'von Juni 2000'],
            ['AFT JUN 2000', 'nach Juni 2000'],
            ['BEF JUN 2000', 'vor Juni 2000'],
            ['15 JUL 2000', '15. Juli 2000'],
            ['JUL 2000', 'Juli 2000'],
            ['ABT JUL 2000', 'um Juli 2000'],
            ['FROM JUL 2000', 'von Juli 2000'],
            ['AFT JUL 2000', 'nach Juli 2000'],
            ['BEF JUL 2000', 'vor Juli 2000'],
            ['15 AUG 2000', '15. August 2000'],
            ['AUG 2000', 'August 2000'],
            ['ABT AUG 2000', 'um August 2000'],
            ['FROM AUG 2000', 'von August 2000'],
            ['AFT AUG 2000', 'nach August 2000'],
            ['BEF AUG 2000', 'vor August 2000'],
            ['15 SEP 2000', '15. September 2000'],
            ['SEP 2000', 'September 2000'],
            ['ABT SEP 2000', 'um September 2000'],
            ['FROM SEP 2000', 'von September 2000'],
            ['AFT SEP 2000', 'nach September 2000'],
            ['BEF SEP 2000', 'vor September 2000'],
            ['15 OCT 2000', '15. Oktober 2000'],
            ['OCT 2000', 'Oktober 2000'],
            ['ABT OCT 2000', 'um Oktober 2000'],
            ['FROM OCT 2000', 'von Oktober 2000'],
            ['AFT OCT 2000', 'nach Oktober 2000'],
            ['BEF OCT 2000', 'vor Oktober 2000'],
            ['15 NOV 2000', '15. November 2000'],
            ['NOV 2000', 'November 2000'],
            ['ABT NOV 2000', 'um November 2000'],
            ['FROM NOV 2000', 'von November 2000'],
            ['AFT NOV 2000', 'nach November 2000'],
            ['BEF NOV 2000', 'vor November 2000'],
            ['15 DEC 2000', '15. Dezember 2000'],
            ['DEC 2000', 'Dezember 2000'],
            ['ABT DEC 2000', 'um Dezember 2000'],
            ['FROM DEC 2000', 'von Dezember 2000'],
            ['AFT DEC 2000', 'nach Dezember 2000'],
            ['BEF DEC 2000', 'vor Dezember 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'um 15. Januar 2000'],
            ['CAL 15 JAN 2000', 'berechnet 15. Januar 2000'],
            ['EST 15 JAN 2000', 'geschätzt 15. Januar 2000'],
            ['BEF 15 JAN 2000', 'vor 15. Januar 2000'],
            ['AFT 15 JAN 2000', 'nach 15. Januar 2000'],
            ['FROM 15 JAN 2000', 'von 15. Januar 2000'],
            ['TO 15 JAN 2000', 'bis 15. Januar 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'zwischen 15. Januar 2000 und 15. Februar 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'von 15. Januar 2000 bis 15. Februar 2000'],
            ['INT 15 JAN 2000', 'interpretiert 15. Januar 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. Januar 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ JAN 1700', 'Januar 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ JAN 1700', 'um Januar 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ JAN 1700', 'von Januar 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ JAN 1700', 'nach Januar 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ JAN 1700', 'vor Januar 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 FEB 1700', '15. Februar 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ FEB 1700', 'Februar 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ FEB 1700', 'um Februar 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ FEB 1700', 'von Februar 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ FEB 1700', 'nach Februar 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ FEB 1700', 'vor Februar 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 MAR 1700', '15. März 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ MAR 1700', 'März 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ MAR 1700', 'um März 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ MAR 1700', 'von März 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ MAR 1700', 'nach März 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ MAR 1700', 'vor März 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 APR 1700', '15. April 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. April 1645/46&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ APR 1700', 'April 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ APR 1700', 'um April 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ APR 1700', 'von April 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ APR 1700', 'nach April 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ APR 1700', 'vor April 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 MAY 1700', '15. Mai 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ MAY 1700', 'Mai 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ MAY 1700', 'um Mai 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ MAY 1700', 'von Mai 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ MAY 1700', 'nach Mai 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ MAY 1700', 'vor Mai 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 JUN 1700', '15. Juni 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ JUN 1700', 'Juni 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ JUN 1700', 'um Juni 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ JUN 1700', 'von Juni 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ JUN 1700', 'nach Juni 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ JUN 1700', 'vor Juni 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 JUL 1700', '15. Juli 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ JUL 1700', 'Juli 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ JUL 1700', 'um Juli 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ JUL 1700', 'von Juli 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ JUL 1700', 'nach Juli 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ JUL 1700', 'vor Juli 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 AUG 1700', '15. August 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ AUG 1700', 'August 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ AUG 1700', 'um August 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ AUG 1700', 'von August 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ AUG 1700', 'nach August 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ AUG 1700', 'vor August 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 SEP 1700', '15. September 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ SEP 1700', 'September 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ SEP 1700', 'um September 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ SEP 1700', 'von September 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ SEP 1700', 'nach September 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ SEP 1700', 'vor September 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 OCT 1700', '15. Oktober 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ OCT 1700', 'Oktober 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ OCT 1700', 'um Oktober 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ OCT 1700', 'von Oktober 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ OCT 1700', 'nach Oktober 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ OCT 1700', 'vor Oktober 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 NOV 1700', '15. November 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ NOV 1700', 'November 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ NOV 1700', 'um November 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ NOV 1700', 'von November 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ NOV 1700', 'nach November 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ NOV 1700', 'vor November 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 15 DEC 1700', '15. Dezember 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ DEC 1700', 'Dezember 1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ DEC 1700', 'um Dezember 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ DEC 1700', 'von Dezember 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ DEC 1700', 'nach Dezember 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ DEC 1700', 'vor Dezember 1700&#7478;&#7489;&#7480;'],
            ['@#DJULIAN@ 1700', '1700&#7478;&#7489;&#7480;'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'um 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'berechnet 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'geschätzt 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'vor 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'nach 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'von 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'bis 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'zwischen 15. Januar 1700&#7478;&#7489;&#7480; und 15. Februar 1700&#7478;&#7489;&#7480;'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'von 15. Januar 1700&#7478;&#7489;&#7480; bis 15. Februar 1700&#7478;&#7489;&#7480;'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretiert 15. Januar 1700&#7478;&#7489;&#7480;'],
            ['@#DHEBREW@ 15 TSH 5765', '15. Tischri 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tischri 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'um Tischri 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'von Tischri 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'nach Tischri 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'vor Tischri 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. Cheschwan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Cheschwan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'um Cheschwan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'von Cheschwan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'nach Cheschwan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'vor Cheschwan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. Kislew 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislew 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'um Kislew 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'von Kislew 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'nach Kislew 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'vor Kislew 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. Tewet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tewet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'um Tewet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'von Tewet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'nach Tewet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'vor Tewet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. Schwat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Schwat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'um Schwat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'von Schwat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'nach Schwat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'vor Schwat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'um Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'von Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'nach Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'vor Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'um Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'von Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'nach Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'vor Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. Nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'um Nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'von Nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'nach Nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'vor Nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Ijar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Ijar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'um Ijar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'von Ijar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'nach Ijar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'vor Ijar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. Siwan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Siwan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'um Siwan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'von Siwan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'nach Siwan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'vor Siwan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. Tammus 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tammus 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'um Tammus 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'von Tammus 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'nach Tammus 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'vor Tammus 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Aw 5765'],
            ['@#DHEBREW@ AAV 5765', 'Aw 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'um Aw 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'von Aw 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'nach Aw 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'vor Aw 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'um Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'von Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'nach Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'vor Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'um 15. Tischri 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'berechnet 15. Tischri 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'geschätzt 15. Tischri 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'vor 15. Tischri 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'nach 15. Tischri 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'von 15. Tischri 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'bis 15. Tischri 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'zwischen 15. Tischri 5765 und 15. Cheschwan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'von 15. Tischri 5765 bis 15. Cheschwan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretiert 15. Tischri 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'um Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'von Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'nach Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'vor Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'um Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'von Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'nach Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'vor Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'um Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'von Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'nach Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'vor Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'um Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'von Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'nach Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'vor Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'um Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'von Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'nach Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'vor Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'um Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'von Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'nach Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'vor Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'um Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'von Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'nach Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'vor Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'um Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'von Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'nach Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'vor Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'um Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'von Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'nach Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'vor Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'um Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'von Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'nach Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'vor Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'um Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'von Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'nach Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'vor Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'um Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'von Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'nach Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'vor Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. Ergänzungungstage An XII'],
            ['@#DFRENCH R@ COMP 12', 'Ergänzungungstage An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'um Ergänzungungstage An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'von Ergänzungungstage An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'nach Ergänzungungstage An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'vor Ergänzungungstage An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'um 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'berechnet 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'geschätzt 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'vor 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'nach 15. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'von 15. Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'bis 15. Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'zwischen 15. Vendémiaire An XII und 15. Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'von 15. Vendémiaire An XII bis 15. Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretiert 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'um Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'von Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'nach Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'vor Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'um Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'von Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'nach Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'vor Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rabiʿ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabiʿ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'um Rabiʿ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'von Rabiʿ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'nach Rabiʿ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'vor Rabiʿ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rabiʿ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabiʿ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'um Rabiʿ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'von Rabiʿ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'nach Rabiʿ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'vor Rabiʿ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. Dschumādā l-ūlā 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Dschumādā l-ūlā 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'um Dschumādā l-ūlā 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'von Dschumādā l-ūlā 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'nach Dschumādā l-ūlā 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'vor Dschumādā l-ūlā 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. Dschumādā th-thāniya 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Dschumādā th-thāniya 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'um Dschumādā th-thāniya 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'von Dschumādā th-thāniya 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'nach Dschumādā th-thāniya 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'vor Dschumādā th-thāniya 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'um Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'von Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'nach Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'vor Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. Schaʿbān 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Schaʿbān 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'um Schaʿbān 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'von Schaʿbān 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'nach Schaʿbān 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'vor Schaʿbān 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'um Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'von Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'nach Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'vor Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. Schawwāl 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Schawwāl 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'um Schawwāl 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'von Schawwāl 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'nach Schawwāl 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'vor Schawwāl 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. Dhu al-Qiʿdah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qiʿdah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'um Dhu al-Qiʿdah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'von Dhu al-Qiʿdah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'nach Dhu al-Qiʿdah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'vor Dhu al-Qiʿdah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'um 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'von 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'nach 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'vor 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'um 15. Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'berechnet 15. Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'geschätzt 15. Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'vor 15. Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'nach 15. Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'von 15. Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'bis 15. Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'zwischen 15. Muharram 1425 und 15. Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'von 15. Muharram 1425 bis 15. Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretiert 15. Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'um Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'von Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'nach Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'vor Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'um Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'von Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'nach Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'vor Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'um Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'von Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'nach Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'vor Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'um Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'von Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'nach Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'vor Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'um Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'von Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'nach Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'vor Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'um Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'von Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'nach Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'vor Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'um Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'von Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'nach Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'vor Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'um Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'von Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'nach Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'vor Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'um Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'von Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'nach Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'vor Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'um Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'von Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'nach Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'vor Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'um Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'von Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'nach Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'vor Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'um Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'von Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'nach Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'vor Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'um 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'berechnet 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'geschätzt 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'vor 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'nach 15. Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'von 15. Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'bis 15. Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'zwischen 15. Farvardin 1384 und 15. Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'von 15. Farvardin 1384 bis 15. Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretiert 15. Farvardin 1384'],
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
        self::assertSame('one und two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two und three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one oder two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two oder three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Create individuals
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
        $grandsonSon = self::male('gs', "1 FAMC @fson@");
        $granddaughterSon = self::female('gd', "1 FAMC @fson@");
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

        // Create families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fs@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@\n1 CHIL @gs@\n1 CHIL @gd@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $stepDaughter, $fosterSon,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $grandsonSon, $granddaughterSon, $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('Ehefrau', 'Ehemann', [$husband, $fm, $wife]);
        self::assertRelationshipNames('Ex-Ehemann', 'Ex-Ehefrau', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('Verlobte', 'Verlobter', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('Mutter', 'Sohn', [$son, $fm, $wife]);
        self::assertRelationshipNames('Vater', 'Sohn', [$son, $fm, $husband]);
        self::assertRelationshipNames('Mutter', 'Tochter', [$daughter, $fm, $wife]);
        self::assertRelationshipNames('Vater', 'Kind', [$child, $fm, $husband]);

        // Adopted
        self::assertRelationshipNames('Adoptivmutter', 'Adoptivsohn', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('Adoptivvater', 'Adoptivsohn', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('Pflegemutter', 'Pflegesohn', [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames('Pflegevater', 'Pflegesohn', [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('kleine Schwester', 'großer Bruder', [$son, $fm, $daughter]);
        self::assertRelationshipNames('großer Bruder', 'kleine Schwester', [$daughter, $fm, $son]);
        self::assertRelationshipNames('jüngeres Geschwister', 'großer Bruder', [$son, $fm, $child]);
        self::assertRelationshipName('Bruder', [$stepDaughter, $fd, $adoptedSon]);
        self::assertRelationshipName('Schwester', [$adoptedSon, $fd, $stepDaughter]);

        // Half-siblings
        self::assertRelationshipNames('Halbbruder', 'Halbschwester', [$stepDaughter, $fd, $wife, $fm, $son]);
        self::assertRelationshipName('Halbschwester', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily (stepDaughter has default pedigree, so parent's new spouse = step-parent)
        self::assertRelationshipName('Stiefvater', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('Stieftochter', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('Schwiegermutter', 'Schwiegersohn', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('Schwiegervater', 'Schwiegersohn', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('Schwiegertochter', [$fatherOfH, $fp, $husband, $fm, $wife]);
        self::assertRelationshipName('Schwiegersohn', [$motherOfW, $fw, $wife, $fm, $husband]);

        // Grandparents and grandchildren
        self::assertRelationshipNames('Großmutter', 'Enkel', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('Großvater', 'Enkel', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('Enkelin', [$fatherOfH, $fp, $husband, $fm, $daughter]);
        self::assertRelationshipName('Enkel', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('Enkelkind', [$fatherOfH, $fp, $husband, $fm, $child]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('UrUrGroßvater', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('UrUrGroßmutter', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('Tante', 'Neffe', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('Onkel', 'Neffe', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews (sibling's children)
        self::assertRelationshipName('Nichte', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('Neffe', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('Cousine', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('Cousin', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('UrTante', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('UrOnkel', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
