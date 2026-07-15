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
use Fisharebest\Webtrees\I18N\Languages\Swedish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Swedish::class)]
class SwedishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Swedish();
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
        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Å', 'Ä', 'Ö'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('sv', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('svenska', self::language()->endonym());
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
        self::assertSame('−123 456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('−123 456,0789 %', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 januari 2000'],
            ['JAN 2000', 'januari 2000'],
            ['ABT JAN 2000', 'ungefär januari 2000'],
            ['FROM JAN 2000', 'från januari 2000'],
            ['AFT JAN 2000', 'efter januari 2000'],
            ['BEF JAN 2000', 'före januari 2000'],
            ['15 FEB 2000', '15 februari 2000'],
            ['FEB 2000', 'februari 2000'],
            ['ABT FEB 2000', 'ungefär februari 2000'],
            ['FROM FEB 2000', 'från februari 2000'],
            ['AFT FEB 2000', 'efter februari 2000'],
            ['BEF FEB 2000', 'före februari 2000'],
            ['15 MAR 2000', '15 mars 2000'],
            ['MAR 2000', 'mars 2000'],
            ['ABT MAR 2000', 'ungefär mars 2000'],
            ['FROM MAR 2000', 'från mars 2000'],
            ['AFT MAR 2000', 'efter mars 2000'],
            ['BEF MAR 2000', 'före mars 2000'],
            ['15 APR 2000', '15 april 2000'],
            ['APR 2000', 'april 2000'],
            ['ABT APR 2000', 'ungefär april 2000'],
            ['FROM APR 2000', 'från april 2000'],
            ['AFT APR 2000', 'efter april 2000'],
            ['BEF APR 2000', 'före april 2000'],
            ['15 MAY 2000', '15 maj 2000'],
            ['MAY 2000', 'maj 2000'],
            ['ABT MAY 2000', 'ungefär maj 2000'],
            ['FROM MAY 2000', 'från maj 2000'],
            ['AFT MAY 2000', 'efter maj 2000'],
            ['BEF MAY 2000', 'före maj 2000'],
            ['15 JUN 2000', '15 juni 2000'],
            ['JUN 2000', 'juni 2000'],
            ['ABT JUN 2000', 'ungefär juni 2000'],
            ['FROM JUN 2000', 'från juni 2000'],
            ['AFT JUN 2000', 'efter juni 2000'],
            ['BEF JUN 2000', 'före juni 2000'],
            ['15 JUL 2000', '15 juli 2000'],
            ['JUL 2000', 'juli 2000'],
            ['ABT JUL 2000', 'ungefär juli 2000'],
            ['FROM JUL 2000', 'från juli 2000'],
            ['AFT JUL 2000', 'efter juli 2000'],
            ['BEF JUL 2000', 'före juli 2000'],
            ['15 AUG 2000', '15 augusti 2000'],
            ['AUG 2000', 'augusti 2000'],
            ['ABT AUG 2000', 'ungefär augusti 2000'],
            ['FROM AUG 2000', 'från augusti 2000'],
            ['AFT AUG 2000', 'efter augusti 2000'],
            ['BEF AUG 2000', 'före augusti 2000'],
            ['15 SEP 2000', '15 september 2000'],
            ['SEP 2000', 'september 2000'],
            ['ABT SEP 2000', 'ungefär september 2000'],
            ['FROM SEP 2000', 'från september 2000'],
            ['AFT SEP 2000', 'efter september 2000'],
            ['BEF SEP 2000', 'före september 2000'],
            ['15 OCT 2000', '15 oktober 2000'],
            ['OCT 2000', 'oktober 2000'],
            ['ABT OCT 2000', 'ungefär oktober 2000'],
            ['FROM OCT 2000', 'från oktober 2000'],
            ['AFT OCT 2000', 'efter oktober 2000'],
            ['BEF OCT 2000', 'före oktober 2000'],
            ['15 NOV 2000', '15 november 2000'],
            ['NOV 2000', 'november 2000'],
            ['ABT NOV 2000', 'ungefär november 2000'],
            ['FROM NOV 2000', 'från november 2000'],
            ['AFT NOV 2000', 'efter november 2000'],
            ['BEF NOV 2000', 'före november 2000'],
            ['15 DEC 2000', '15 december 2000'],
            ['DEC 2000', 'december 2000'],
            ['ABT DEC 2000', 'ungefär december 2000'],
            ['FROM DEC 2000', 'från december 2000'],
            ['AFT DEC 2000', 'efter december 2000'],
            ['BEF DEC 2000', 'före december 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'ungefär 15 januari 2000'],
            ['CAL 15 JAN 2000', 'beräknad 15 januari 2000'],
            ['EST 15 JAN 2000', 'uppskattad 15 januari 2000'],
            ['BEF 15 JAN 2000', 'före 15 januari 2000'],
            ['AFT 15 JAN 2000', 'efter 15 januari 2000'],
            ['FROM 15 JAN 2000', 'från 15 januari 2000'],
            ['TO 15 JAN 2000', 'till 15 januari 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'mellan 15 januari 2000 och 15 februari 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'från 15 januari 2000 till 15 februari 2000'],
            ['INT 15 JAN 2000', 'tolkat 15 januari 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 januari 1700 evt'],
            ['@#DJULIAN@ JAN 1700', 'januari 1700 evt'],
            ['ABT @#DJULIAN@ JAN 1700', 'ungefär januari 1700 evt'],
            ['FROM @#DJULIAN@ JAN 1700', 'från januari 1700 evt'],
            ['AFT @#DJULIAN@ JAN 1700', 'efter januari 1700 evt'],
            ['BEF @#DJULIAN@ JAN 1700', 'före januari 1700 evt'],
            ['@#DJULIAN@ 15 FEB 1700', '15 februari 1700 evt'],
            ['@#DJULIAN@ FEB 1700', 'februari 1700 evt'],
            ['ABT @#DJULIAN@ FEB 1700', 'ungefär februari 1700 evt'],
            ['FROM @#DJULIAN@ FEB 1700', 'från februari 1700 evt'],
            ['AFT @#DJULIAN@ FEB 1700', 'efter februari 1700 evt'],
            ['BEF @#DJULIAN@ FEB 1700', 'före februari 1700 evt'],
            ['@#DJULIAN@ 15 MAR 1700', '15 mars 1700 evt'],
            ['@#DJULIAN@ MAR 1700', 'mars 1700 evt'],
            ['ABT @#DJULIAN@ MAR 1700', 'ungefär mars 1700 evt'],
            ['FROM @#DJULIAN@ MAR 1700', 'från mars 1700 evt'],
            ['AFT @#DJULIAN@ MAR 1700', 'efter mars 1700 evt'],
            ['BEF @#DJULIAN@ MAR 1700', 'före mars 1700 evt'],
            ['@#DJULIAN@ 15 APR 1700', '15 april 1700 evt'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 april 1645/46 evt'],
            ['@#DJULIAN@ APR 1700', 'april 1700 evt'],
            ['ABT @#DJULIAN@ APR 1700', 'ungefär april 1700 evt'],
            ['FROM @#DJULIAN@ APR 1700', 'från april 1700 evt'],
            ['AFT @#DJULIAN@ APR 1700', 'efter april 1700 evt'],
            ['BEF @#DJULIAN@ APR 1700', 'före april 1700 evt'],
            ['@#DJULIAN@ 15 MAY 1700', '15 maj 1700 evt'],
            ['@#DJULIAN@ MAY 1700', 'maj 1700 evt'],
            ['ABT @#DJULIAN@ MAY 1700', 'ungefär maj 1700 evt'],
            ['FROM @#DJULIAN@ MAY 1700', 'från maj 1700 evt'],
            ['AFT @#DJULIAN@ MAY 1700', 'efter maj 1700 evt'],
            ['BEF @#DJULIAN@ MAY 1700', 'före maj 1700 evt'],
            ['@#DJULIAN@ 15 JUN 1700', '15 juni 1700 evt'],
            ['@#DJULIAN@ JUN 1700', 'juni 1700 evt'],
            ['ABT @#DJULIAN@ JUN 1700', 'ungefär juni 1700 evt'],
            ['FROM @#DJULIAN@ JUN 1700', 'från juni 1700 evt'],
            ['AFT @#DJULIAN@ JUN 1700', 'efter juni 1700 evt'],
            ['BEF @#DJULIAN@ JUN 1700', 'före juni 1700 evt'],
            ['@#DJULIAN@ 15 JUL 1700', '15 juli 1700 evt'],
            ['@#DJULIAN@ JUL 1700', 'juli 1700 evt'],
            ['ABT @#DJULIAN@ JUL 1700', 'ungefär juli 1700 evt'],
            ['FROM @#DJULIAN@ JUL 1700', 'från juli 1700 evt'],
            ['AFT @#DJULIAN@ JUL 1700', 'efter juli 1700 evt'],
            ['BEF @#DJULIAN@ JUL 1700', 'före juli 1700 evt'],
            ['@#DJULIAN@ 15 AUG 1700', '15 augusti 1700 evt'],
            ['@#DJULIAN@ AUG 1700', 'augusti 1700 evt'],
            ['ABT @#DJULIAN@ AUG 1700', 'ungefär augusti 1700 evt'],
            ['FROM @#DJULIAN@ AUG 1700', 'från augusti 1700 evt'],
            ['AFT @#DJULIAN@ AUG 1700', 'efter augusti 1700 evt'],
            ['BEF @#DJULIAN@ AUG 1700', 'före augusti 1700 evt'],
            ['@#DJULIAN@ 15 SEP 1700', '15 september 1700 evt'],
            ['@#DJULIAN@ SEP 1700', 'september 1700 evt'],
            ['ABT @#DJULIAN@ SEP 1700', 'ungefär september 1700 evt'],
            ['FROM @#DJULIAN@ SEP 1700', 'från september 1700 evt'],
            ['AFT @#DJULIAN@ SEP 1700', 'efter september 1700 evt'],
            ['BEF @#DJULIAN@ SEP 1700', 'före september 1700 evt'],
            ['@#DJULIAN@ 15 OCT 1700', '15 oktober 1700 evt'],
            ['@#DJULIAN@ OCT 1700', 'oktober 1700 evt'],
            ['ABT @#DJULIAN@ OCT 1700', 'ungefär oktober 1700 evt'],
            ['FROM @#DJULIAN@ OCT 1700', 'från oktober 1700 evt'],
            ['AFT @#DJULIAN@ OCT 1700', 'efter oktober 1700 evt'],
            ['BEF @#DJULIAN@ OCT 1700', 'före oktober 1700 evt'],
            ['@#DJULIAN@ 15 NOV 1700', '15 november 1700 evt'],
            ['@#DJULIAN@ NOV 1700', 'november 1700 evt'],
            ['ABT @#DJULIAN@ NOV 1700', 'ungefär november 1700 evt'],
            ['FROM @#DJULIAN@ NOV 1700', 'från november 1700 evt'],
            ['AFT @#DJULIAN@ NOV 1700', 'efter november 1700 evt'],
            ['BEF @#DJULIAN@ NOV 1700', 'före november 1700 evt'],
            ['@#DJULIAN@ 15 DEC 1700', '15 december 1700 evt'],
            ['@#DJULIAN@ DEC 1700', 'december 1700 evt'],
            ['ABT @#DJULIAN@ DEC 1700', 'ungefär december 1700 evt'],
            ['FROM @#DJULIAN@ DEC 1700', 'från december 1700 evt'],
            ['AFT @#DJULIAN@ DEC 1700', 'efter december 1700 evt'],
            ['BEF @#DJULIAN@ DEC 1700', 'före december 1700 evt'],
            ['@#DJULIAN@ 1700', '1700 evt'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'ungefär 15 januari 1700 evt'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'beräknad 15 januari 1700 evt'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'uppskattad 15 januari 1700 evt'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'före 15 januari 1700 evt'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'efter 15 januari 1700 evt'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'från 15 januari 1700 evt'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'till 15 januari 1700 evt'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'mellan 15 januari 1700 evt och 15 februari 1700 evt'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'från 15 januari 1700 evt till 15 februari 1700 evt'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'tolkat 15 januari 1700 evt'],
            ['@#DHEBREW@ 15 TSH 5765', '15 tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'ungefär tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'från tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'efter tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'före tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'ungefär heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'från heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'efter heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'före heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'ungefär kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'från kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'efter kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'före kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'ungefär tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'från tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'efter tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'före tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 shvat 5765'],
            ['@#DHEBREW@ SHV 5765', 'shvat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'ungefär shvat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'från shvat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'efter shvat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'före shvat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'ungefär adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'från adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'efter adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'före adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'ungefär adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'från adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'efter adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'före adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'ungefär nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'från nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'efter nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'före nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 ijar 5765'],
            ['@#DHEBREW@ IYR 5765', 'ijar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'ungefär ijar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'från ijar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'efter ijar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'före ijar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'ungefär sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'från sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'efter sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'före sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'ungefär tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'från tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'efter tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'före tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 av 5765'],
            ['@#DHEBREW@ AAV 5765', 'av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'ungefär av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'från av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'efter av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'före av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'ungefär elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'från elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'efter elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'före elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'ungefär 15 tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'beräknad 15 tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'uppskattad 15 tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'före 15 tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'efter 15 tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'från 15 tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'till 15 tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'mellan 15 tishrei 5765 och 15 heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'från 15 tishrei 5765 till 15 heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'tolkat 15 tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'ungefär Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'från Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'efter Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'före Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'ungefär Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'från Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'efter Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'före Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'ungefär Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'från Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'efter Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'före Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'ungefär Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'från Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'efter Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'före Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'ungefär Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'från Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'efter Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'före Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'ungefär Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'från Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'efter Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'före Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'ungefär Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'från Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'efter Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'före Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'ungefär Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'från Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'efter Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'före Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'ungefär Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'från Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'efter Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'före Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'ungefär Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'från Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'efter Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'före Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'ungefär Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'från Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'efter Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'före Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'ungefär Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'från Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'efter Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'före Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'ungefär jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'från jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'efter jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'före jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'ungefär 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'beräknad 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'uppskattad 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'före 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'efter 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'från 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'till 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'mellan 15 Vendémiaire An XII och 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'från 15 Vendémiaire An XII till 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'tolkat 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'ungefär Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'från Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'efter Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'före Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'ungefär Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'från Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'efter Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'före Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'ungefär Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'från Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'efter Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'före Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'ungefär Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'från Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'efter Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'före Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'ungefär Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'från Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'efter Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'före Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-akhirah 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-akhirah 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'ungefär Jumada al-akhirah 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'från Jumada al-akhirah 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'efter Jumada al-akhirah 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'före Jumada al-akhirah 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'ungefär Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'från Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'efter Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'före Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’ban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’ban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'ungefär Sha’ban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'från Sha’ban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'efter Sha’ban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'före Sha’ban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'ungefär Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'från Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'efter Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'före Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'ungefär Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'från Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'efter Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'före Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu l-Qa’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu l-Qa’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'ungefär Dhu l-Qa’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'från Dhu l-Qa’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'efter Dhu l-Qa’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'före Dhu l-Qa’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'ungefär 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'från 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'efter 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'före 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'ungefär 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'beräknad 15 Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'uppskattad 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'före 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'efter 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'från 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'till 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'mellan 15 Muharram 1425 och 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'från 15 Muharram 1425 till 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'tolkat 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'ungefär Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'från Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'efter Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'före Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'ungefär Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'från Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'efter Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'före Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'ungefär Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'från Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'efter Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'före Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'ungefär Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'från Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'efter Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'före Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'ungefär Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'från Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'efter Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'före Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'ungefär Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'från Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'efter Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'före Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'ungefär Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'från Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'efter Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'före Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'ungefär Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'från Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'efter Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'före Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'ungefär Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'från Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'efter Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'före Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'ungefär Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'från Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'efter Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'före Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'ungefär Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'från Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'efter Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'före Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'ungefär Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'från Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'efter Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'före Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'ungefär 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'beräknad 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'uppskattad 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'före 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'efter 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'från 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'till 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'mellan 15 Farvardin 1384 och 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'från 15 Farvardin 1384 till 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'tolkat 15 Farvardin 1384'],
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
        self::assertSame('one och two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two och three', $language->formatListAnd(['one', 'two', 'three']));

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
        self::assertRelationshipNames('hustru', 'make', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-man', 'ex-fru', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('fästmö', 'fästman', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mor', 'son', [$son, $fm, $wife]);
        self::assertRelationshipNames('far', 'son', [$son, $fm, $husband]);
        self::assertRelationshipNames('mor', 'dotter', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('lillasyster', 'storebror', [$son, $fm, $daughter]);
        self::assertRelationshipNames('storebror', 'lillasyster', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('halvbror', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('styvfar', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('styvdotter', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('svärmor', 'svärson', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('svärfar', 'svärson', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('svärdotter', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents (paternal/maternal specific)
        self::assertRelationshipNames('farmor', 'sonson', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('farfar', 'sonson', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipNames('mormor', 'dotterson', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('morfar', 'dotterson', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('gammelfarfar', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('gammelfarmor', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles (paternal/maternal specific)
        self::assertRelationshipNames('faster', 'brorson', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('farbror', 'brorson', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('brorsdotter', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('brorson', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('kusin', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('kusin', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }
}
