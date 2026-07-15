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
use Fisharebest\Webtrees\I18N\Languages\NorwegianNynorsk;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NorwegianNynorsk::class)]
class NorwegianNynorskTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new NorwegianNynorsk();
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
        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Æ', 'Ø', 'Å'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('nn', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('norsk nynorsk', self::language()->endonym());
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
            ['15 JAN 2000', '15. januar 2000'],
            ['JAN 2000', 'januar 2000'],
            ['ABT JAN 2000', 'omlag januar 2000'],
            ['FROM JAN 2000', 'frå januar 2000'],
            ['AFT JAN 2000', 'etter januar 2000'],
            ['BEF JAN 2000', 'før januar 2000'],
            ['15 FEB 2000', '15. februar 2000'],
            ['FEB 2000', 'februar 2000'],
            ['ABT FEB 2000', 'omlag februar 2000'],
            ['FROM FEB 2000', 'frå februar 2000'],
            ['AFT FEB 2000', 'etter februar 2000'],
            ['BEF FEB 2000', 'før februar 2000'],
            ['15 MAR 2000', '15. mars 2000'],
            ['MAR 2000', 'mars 2000'],
            ['ABT MAR 2000', 'omlag mars 2000'],
            ['FROM MAR 2000', 'frå mars 2000'],
            ['AFT MAR 2000', 'etter mars 2000'],
            ['BEF MAR 2000', 'før mars 2000'],
            ['15 APR 2000', '15. april 2000'],
            ['APR 2000', 'april 2000'],
            ['ABT APR 2000', 'omlag april 2000'],
            ['FROM APR 2000', 'frå april 2000'],
            ['AFT APR 2000', 'etter april 2000'],
            ['BEF APR 2000', 'før april 2000'],
            ['15 MAY 2000', '15. mai 2000'],
            ['MAY 2000', 'mai 2000'],
            ['ABT MAY 2000', 'omlag mai 2000'],
            ['FROM MAY 2000', 'frå mai 2000'],
            ['AFT MAY 2000', 'etter mai 2000'],
            ['BEF MAY 2000', 'før mai 2000'],
            ['15 JUN 2000', '15. juni 2000'],
            ['JUN 2000', 'juni 2000'],
            ['ABT JUN 2000', 'omlag juni 2000'],
            ['FROM JUN 2000', 'frå juni 2000'],
            ['AFT JUN 2000', 'etter juni 2000'],
            ['BEF JUN 2000', 'før juni 2000'],
            ['15 JUL 2000', '15. juli 2000'],
            ['JUL 2000', 'juli 2000'],
            ['ABT JUL 2000', 'omlag juli 2000'],
            ['FROM JUL 2000', 'frå juli 2000'],
            ['AFT JUL 2000', 'etter juli 2000'],
            ['BEF JUL 2000', 'før juli 2000'],
            ['15 AUG 2000', '15. august 2000'],
            ['AUG 2000', 'august 2000'],
            ['ABT AUG 2000', 'omlag august 2000'],
            ['FROM AUG 2000', 'frå august 2000'],
            ['AFT AUG 2000', 'etter august 2000'],
            ['BEF AUG 2000', 'før august 2000'],
            ['15 SEP 2000', '15. september 2000'],
            ['SEP 2000', 'september 2000'],
            ['ABT SEP 2000', 'omlag september 2000'],
            ['FROM SEP 2000', 'frå september 2000'],
            ['AFT SEP 2000', 'etter september 2000'],
            ['BEF SEP 2000', 'før september 2000'],
            ['15 OCT 2000', '15. oktober 2000'],
            ['OCT 2000', 'oktober 2000'],
            ['ABT OCT 2000', 'omlag oktober 2000'],
            ['FROM OCT 2000', 'frå oktober 2000'],
            ['AFT OCT 2000', 'etter oktober 2000'],
            ['BEF OCT 2000', 'før oktober 2000'],
            ['15 NOV 2000', '15. november 2000'],
            ['NOV 2000', 'november 2000'],
            ['ABT NOV 2000', 'omlag november 2000'],
            ['FROM NOV 2000', 'frå november 2000'],
            ['AFT NOV 2000', 'etter november 2000'],
            ['BEF NOV 2000', 'før november 2000'],
            ['15 DEC 2000', '15. desember 2000'],
            ['DEC 2000', 'desember 2000'],
            ['ABT DEC 2000', 'omlag desember 2000'],
            ['FROM DEC 2000', 'frå desember 2000'],
            ['AFT DEC 2000', 'etter desember 2000'],
            ['BEF DEC 2000', 'før desember 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'omlag 15. januar 2000'],
            ['CAL 15 JAN 2000', 'berekna 15. januar 2000'],
            ['EST 15 JAN 2000', 'estimert 15. januar 2000'],
            ['BEF 15 JAN 2000', 'før 15. januar 2000'],
            ['AFT 15 JAN 2000', 'etter 15. januar 2000'],
            ['FROM 15 JAN 2000', 'frå 15. januar 2000'],
            ['TO 15 JAN 2000', 'til 15. januar 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'mellom 15. januar 2000 og 15. februar 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'frå 15. januar 2000 til 15. februar 2000'],
            ['INT 15 JAN 2000', 'tolka 15. januar 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. januar 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ JAN 1700', 'januar 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ JAN 1700', 'omlag januar 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ JAN 1700', 'frå januar 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ JAN 1700', 'etter januar 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ JAN 1700', 'før januar 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 FEB 1700', '15. februar 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ FEB 1700', 'februar 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ FEB 1700', 'omlag februar 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ FEB 1700', 'frå februar 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ FEB 1700', 'etter februar 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ FEB 1700', 'før februar 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 MAR 1700', '15. mars 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ MAR 1700', 'mars 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ MAR 1700', 'omlag mars 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ MAR 1700', 'frå mars 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ MAR 1700', 'etter mars 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ MAR 1700', 'før mars 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 APR 1700', '15. april 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. april 1645/46 evt. Juliansk kalender'],
            ['@#DJULIAN@ APR 1700', 'april 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ APR 1700', 'omlag april 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ APR 1700', 'frå april 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ APR 1700', 'etter april 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ APR 1700', 'før april 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 MAY 1700', '15. mai 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ MAY 1700', 'mai 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ MAY 1700', 'omlag mai 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ MAY 1700', 'frå mai 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ MAY 1700', 'etter mai 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ MAY 1700', 'før mai 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 JUN 1700', '15. juni 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ JUN 1700', 'juni 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ JUN 1700', 'omlag juni 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ JUN 1700', 'frå juni 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ JUN 1700', 'etter juni 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ JUN 1700', 'før juni 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 JUL 1700', '15. juli 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ JUL 1700', 'juli 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ JUL 1700', 'omlag juli 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ JUL 1700', 'frå juli 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ JUL 1700', 'etter juli 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ JUL 1700', 'før juli 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 AUG 1700', '15. august 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ AUG 1700', 'august 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ AUG 1700', 'omlag august 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ AUG 1700', 'frå august 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ AUG 1700', 'etter august 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ AUG 1700', 'før august 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 SEP 1700', '15. september 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ SEP 1700', 'september 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ SEP 1700', 'omlag september 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ SEP 1700', 'frå september 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ SEP 1700', 'etter september 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ SEP 1700', 'før september 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 OCT 1700', '15. oktober 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ OCT 1700', 'oktober 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ OCT 1700', 'omlag oktober 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ OCT 1700', 'frå oktober 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ OCT 1700', 'etter oktober 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ OCT 1700', 'før oktober 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 NOV 1700', '15. november 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ NOV 1700', 'november 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ NOV 1700', 'omlag november 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ NOV 1700', 'frå november 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ NOV 1700', 'etter november 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ NOV 1700', 'før november 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 15 DEC 1700', '15. desember 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ DEC 1700', 'desember 1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ DEC 1700', 'omlag desember 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ DEC 1700', 'frå desember 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ DEC 1700', 'etter desember 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ DEC 1700', 'før desember 1700 evt. Juliansk kalender'],
            ['@#DJULIAN@ 1700', '1700 evt. Juliansk kalender'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'omlag 15. januar 1700 evt. Juliansk kalender'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'berekna 15. januar 1700 evt. Juliansk kalender'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimert 15. januar 1700 evt. Juliansk kalender'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'før 15. januar 1700 evt. Juliansk kalender'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'etter 15. januar 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'frå 15. januar 1700 evt. Juliansk kalender'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'til 15. januar 1700 evt. Juliansk kalender'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'mellom 15. januar 1700 evt. Juliansk kalender og 15. februar 1700 evt. Juliansk kalender'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'frå 15. januar 1700 evt. Juliansk kalender til 15. februar 1700 evt. Juliansk kalender'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'tolka 15. januar 1700 evt. Juliansk kalender'],
            ['@#DHEBREW@ 15 TSH 5765', '15. Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'omlag Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'frå Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'etter Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'før Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'omlag Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'frå Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'etter Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'før Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'omlag Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'frå Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'etter Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'før Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'omlag Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'frå Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'etter Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'før Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'omlag Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'frå Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'etter Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'før Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'omlag Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'frå Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'etter Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'før Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'omlag Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'frå Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'etter Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'før Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. Nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'omlag Nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'frå Nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'etter Nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'før Nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'omlag Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'frå Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'etter Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'før Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'omlag Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'frå Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'etter Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'før Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'omlag Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'frå Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'etter Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'før Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'omlag Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'frå Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'etter Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'før Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'omlag Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'frå Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'etter Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'før Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'omlag 15. Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'berekna 15. Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimert 15. Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'før 15. Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'etter 15. Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'frå 15. Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'til 15. Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'mellom 15. Tishrei 5765 og 15. Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'frå 15. Tishrei 5765 til 15. Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'tolka 15. Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'omlag Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'frå Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'etter Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'før Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'omlag Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'frå Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'etter Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'før Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'omlag Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'frå Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'etter Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'før Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'omlag Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'frå Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'etter Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'før Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'omlag Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'frå Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'etter Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'før Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'omlag Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'frå Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'etter Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'før Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'omlag Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'frå Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'etter Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'før Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'omlag Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'frå Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'etter Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'før Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'omlag Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'frå Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'etter Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'før Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'omlag Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'frå Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'etter Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'før Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'omlag Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'frå Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'etter Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'før Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'omlag Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'frå Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'etter Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'før Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'omlag jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'frå jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'etter jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'før jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'omlag 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'berekna 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimert 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'før 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'etter 15. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'frå 15. Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'til 15. Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'mellom 15. Vendémiaire An XII og 15. Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'frå 15. Vendémiaire An XII til 15. Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'tolka 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'omlag Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'frå Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'etter Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'før Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'omlag Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'frå Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'etter Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'før Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'omlag Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'frå Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'etter Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'før Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'omlag Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'frå Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'etter Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'før Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'omlag Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'frå Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'etter Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'før Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'omlag Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'frå Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'etter Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'før Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'omlag Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'frå Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'etter Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'før Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'omlag Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'frå Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'etter Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'før Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'omlag Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'frå Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'etter Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'før Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'omlag Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'frå Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'etter Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'før Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'omlag Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'frå Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'etter Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'før Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'omlag 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'frå 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'etter 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'før 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'omlag 15. Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'berekna 15. Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimert 15. Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'før 15. Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'etter 15. Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'frå 15. Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'til 15. Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'mellom 15. Muharram 1425 og 15. Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'frå 15. Muharram 1425 til 15. Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'tolka 15. Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'omlag Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'frå Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'etter Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'før Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'omlag Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'frå Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'etter Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'før Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'omlag Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'frå Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'etter Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'før Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'omlag Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'frå Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'etter Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'før Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'omlag Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'frå Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'etter Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'før Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'omlag Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'frå Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'etter Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'før Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'omlag Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'frå Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'etter Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'før Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'omlag Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'frå Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'etter Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'før Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'omlag Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'frå Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'etter Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'før Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'omlag Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'frå Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'etter Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'før Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'omlag Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'frå Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'etter Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'før Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'omlag Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'frå Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'etter Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'før Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'omlag 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'berekna 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimert 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'før 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'etter 15. Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'frå 15. Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'til 15. Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'mellom 15. Farvardin 1384 og 15. Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'frå 15. Farvardin 1384 til 15. Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'tolka 15. Farvardin 1384'],
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
        self::assertSame('one og two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two og three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one eller two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two eller three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('hustru', 'mann', [$husband, $fm, $wife]);
        self::assertRelationshipNames('eksmann', 'ekskone', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('forlova', 'forlova', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mor', 'son', [$son, $fm, $wife]);
        self::assertRelationshipNames('far', 'son', [$son, $fm, $husband]);
        self::assertRelationshipNames('mor', 'dotter', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('lillesyster', 'storebror', [$son, $fm, $daughter]);
        self::assertRelationshipNames('storebror', 'lillesyster', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('halvbror', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('stefar', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('stedotter', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('svigermor', 'svigerson', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('svigerfar', 'svigerson', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('svigerdotter', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents (paternal/maternal specific)
        self::assertRelationshipNames('farmor', 'barnebarn', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('farfar', 'barnebarn', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipNames('mormor', 'barnebarn', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('morfar', 'barnebarn', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('tippoldefar', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('tippoldemor', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles (paternal/maternal specific)
        self::assertRelationshipNames('faster', 'nevø', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('farbror', 'nevø', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('niese', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nevø', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('kusine', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('fetter', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }
}
