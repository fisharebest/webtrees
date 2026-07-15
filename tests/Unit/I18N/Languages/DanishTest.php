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
use Fisharebest\Webtrees\I18N\Languages\Danish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Danish::class)]
class DanishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Danish();
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
        self::assertSame('da', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('dansk', self::language()->endonym());
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
            ['15 JAN 2000', '15. januar 2000'],
            ['JAN 2000', 'januar 2000'],
            ['ABT JAN 2000', 'omkring januar 2000'],
            ['FROM JAN 2000', 'fra januar 2000'],
            ['AFT JAN 2000', 'efter januar 2000'],
            ['BEF JAN 2000', 'før januar 2000'],
            ['15 FEB 2000', '15. februar 2000'],
            ['FEB 2000', 'februar 2000'],
            ['ABT FEB 2000', 'omkring februar 2000'],
            ['FROM FEB 2000', 'fra februar 2000'],
            ['AFT FEB 2000', 'efter februar 2000'],
            ['BEF FEB 2000', 'før februar 2000'],
            ['15 MAR 2000', '15. marts 2000'],
            ['MAR 2000', 'marts 2000'],
            ['ABT MAR 2000', 'omkring marts 2000'],
            ['FROM MAR 2000', 'fra marts 2000'],
            ['AFT MAR 2000', 'efter marts 2000'],
            ['BEF MAR 2000', 'før marts 2000'],
            ['15 APR 2000', '15. april 2000'],
            ['APR 2000', 'april 2000'],
            ['ABT APR 2000', 'omkring april 2000'],
            ['FROM APR 2000', 'fra april 2000'],
            ['AFT APR 2000', 'efter april 2000'],
            ['BEF APR 2000', 'før april 2000'],
            ['15 MAY 2000', '15. maj 2000'],
            ['MAY 2000', 'maj 2000'],
            ['ABT MAY 2000', 'omkring maj 2000'],
            ['FROM MAY 2000', 'fra maj 2000'],
            ['AFT MAY 2000', 'efter maj 2000'],
            ['BEF MAY 2000', 'før maj 2000'],
            ['15 JUN 2000', '15. juni 2000'],
            ['JUN 2000', 'juni 2000'],
            ['ABT JUN 2000', 'omkring juni 2000'],
            ['FROM JUN 2000', 'fra juni 2000'],
            ['AFT JUN 2000', 'efter juni 2000'],
            ['BEF JUN 2000', 'før juni 2000'],
            ['15 JUL 2000', '15. juli 2000'],
            ['JUL 2000', 'juli 2000'],
            ['ABT JUL 2000', 'omkring juli 2000'],
            ['FROM JUL 2000', 'fra juli 2000'],
            ['AFT JUL 2000', 'efter juli 2000'],
            ['BEF JUL 2000', 'før juli 2000'],
            ['15 AUG 2000', '15. august 2000'],
            ['AUG 2000', 'august 2000'],
            ['ABT AUG 2000', 'omkring august 2000'],
            ['FROM AUG 2000', 'fra august 2000'],
            ['AFT AUG 2000', 'efter august 2000'],
            ['BEF AUG 2000', 'før august 2000'],
            ['15 SEP 2000', '15. september 2000'],
            ['SEP 2000', 'september 2000'],
            ['ABT SEP 2000', 'omkring september 2000'],
            ['FROM SEP 2000', 'fra september 2000'],
            ['AFT SEP 2000', 'efter september 2000'],
            ['BEF SEP 2000', 'før september 2000'],
            ['15 OCT 2000', '15. oktober 2000'],
            ['OCT 2000', 'oktober 2000'],
            ['ABT OCT 2000', 'omkring oktober 2000'],
            ['FROM OCT 2000', 'fra oktober 2000'],
            ['AFT OCT 2000', 'efter oktober 2000'],
            ['BEF OCT 2000', 'før oktober 2000'],
            ['15 NOV 2000', '15. november 2000'],
            ['NOV 2000', 'november 2000'],
            ['ABT NOV 2000', 'omkring november 2000'],
            ['FROM NOV 2000', 'fra november 2000'],
            ['AFT NOV 2000', 'efter november 2000'],
            ['BEF NOV 2000', 'før november 2000'],
            ['15 DEC 2000', '15. december 2000'],
            ['DEC 2000', 'december 2000'],
            ['ABT DEC 2000', 'omkring december 2000'],
            ['FROM DEC 2000', 'fra december 2000'],
            ['AFT DEC 2000', 'efter december 2000'],
            ['BEF DEC 2000', 'før december 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'omkring 15. januar 2000'],
            ['CAL 15 JAN 2000', 'beregnet 15. januar 2000'],
            ['EST 15 JAN 2000', 'omkring 15. januar 2000'],
            ['BEF 15 JAN 2000', 'før 15. januar 2000'],
            ['AFT 15 JAN 2000', 'efter 15. januar 2000'],
            ['FROM 15 JAN 2000', 'fra 15. januar 2000'],
            ['TO 15 JAN 2000', 'til 15. januar 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'mellem 15. januar 2000 og 15. februar 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'fra 15. januar 2000 til 15. februar 2000'],
            ['INT 15 JAN 2000', 'fortolket 15. januar 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. januar 1700 e.v.t'],
            ['@#DJULIAN@ JAN 1700', 'januar 1700 e.v.t'],
            ['ABT @#DJULIAN@ JAN 1700', 'omkring januar 1700 e.v.t'],
            ['FROM @#DJULIAN@ JAN 1700', 'fra januar 1700 e.v.t'],
            ['AFT @#DJULIAN@ JAN 1700', 'efter januar 1700 e.v.t'],
            ['BEF @#DJULIAN@ JAN 1700', 'før januar 1700 e.v.t'],
            ['@#DJULIAN@ 15 FEB 1700', '15. februar 1700 e.v.t'],
            ['@#DJULIAN@ FEB 1700', 'februar 1700 e.v.t'],
            ['ABT @#DJULIAN@ FEB 1700', 'omkring februar 1700 e.v.t'],
            ['FROM @#DJULIAN@ FEB 1700', 'fra februar 1700 e.v.t'],
            ['AFT @#DJULIAN@ FEB 1700', 'efter februar 1700 e.v.t'],
            ['BEF @#DJULIAN@ FEB 1700', 'før februar 1700 e.v.t'],
            ['@#DJULIAN@ 15 MAR 1700', '15. marts 1700 e.v.t'],
            ['@#DJULIAN@ MAR 1700', 'marts 1700 e.v.t'],
            ['ABT @#DJULIAN@ MAR 1700', 'omkring marts 1700 e.v.t'],
            ['FROM @#DJULIAN@ MAR 1700', 'fra marts 1700 e.v.t'],
            ['AFT @#DJULIAN@ MAR 1700', 'efter marts 1700 e.v.t'],
            ['BEF @#DJULIAN@ MAR 1700', 'før marts 1700 e.v.t'],
            ['@#DJULIAN@ 15 APR 1700', '15. april 1700 e.v.t'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. april 1645/46 e.v.t'],
            ['@#DJULIAN@ APR 1700', 'april 1700 e.v.t'],
            ['ABT @#DJULIAN@ APR 1700', 'omkring april 1700 e.v.t'],
            ['FROM @#DJULIAN@ APR 1700', 'fra april 1700 e.v.t'],
            ['AFT @#DJULIAN@ APR 1700', 'efter april 1700 e.v.t'],
            ['BEF @#DJULIAN@ APR 1700', 'før april 1700 e.v.t'],
            ['@#DJULIAN@ 15 MAY 1700', '15. maj 1700 e.v.t'],
            ['@#DJULIAN@ MAY 1700', 'maj 1700 e.v.t'],
            ['ABT @#DJULIAN@ MAY 1700', 'omkring maj 1700 e.v.t'],
            ['FROM @#DJULIAN@ MAY 1700', 'fra maj 1700 e.v.t'],
            ['AFT @#DJULIAN@ MAY 1700', 'efter maj 1700 e.v.t'],
            ['BEF @#DJULIAN@ MAY 1700', 'før maj 1700 e.v.t'],
            ['@#DJULIAN@ 15 JUN 1700', '15. juni 1700 e.v.t'],
            ['@#DJULIAN@ JUN 1700', 'juni 1700 e.v.t'],
            ['ABT @#DJULIAN@ JUN 1700', 'omkring juni 1700 e.v.t'],
            ['FROM @#DJULIAN@ JUN 1700', 'fra juni 1700 e.v.t'],
            ['AFT @#DJULIAN@ JUN 1700', 'efter juni 1700 e.v.t'],
            ['BEF @#DJULIAN@ JUN 1700', 'før juni 1700 e.v.t'],
            ['@#DJULIAN@ 15 JUL 1700', '15. juli 1700 e.v.t'],
            ['@#DJULIAN@ JUL 1700', 'juli 1700 e.v.t'],
            ['ABT @#DJULIAN@ JUL 1700', 'omkring juli 1700 e.v.t'],
            ['FROM @#DJULIAN@ JUL 1700', 'fra juli 1700 e.v.t'],
            ['AFT @#DJULIAN@ JUL 1700', 'efter juli 1700 e.v.t'],
            ['BEF @#DJULIAN@ JUL 1700', 'før juli 1700 e.v.t'],
            ['@#DJULIAN@ 15 AUG 1700', '15. august 1700 e.v.t'],
            ['@#DJULIAN@ AUG 1700', 'august 1700 e.v.t'],
            ['ABT @#DJULIAN@ AUG 1700', 'omkring august 1700 e.v.t'],
            ['FROM @#DJULIAN@ AUG 1700', 'fra august 1700 e.v.t'],
            ['AFT @#DJULIAN@ AUG 1700', 'efter august 1700 e.v.t'],
            ['BEF @#DJULIAN@ AUG 1700', 'før august 1700 e.v.t'],
            ['@#DJULIAN@ 15 SEP 1700', '15. september 1700 e.v.t'],
            ['@#DJULIAN@ SEP 1700', 'september 1700 e.v.t'],
            ['ABT @#DJULIAN@ SEP 1700', 'omkring september 1700 e.v.t'],
            ['FROM @#DJULIAN@ SEP 1700', 'fra september 1700 e.v.t'],
            ['AFT @#DJULIAN@ SEP 1700', 'efter september 1700 e.v.t'],
            ['BEF @#DJULIAN@ SEP 1700', 'før september 1700 e.v.t'],
            ['@#DJULIAN@ 15 OCT 1700', '15. oktober 1700 e.v.t'],
            ['@#DJULIAN@ OCT 1700', 'oktober 1700 e.v.t'],
            ['ABT @#DJULIAN@ OCT 1700', 'omkring oktober 1700 e.v.t'],
            ['FROM @#DJULIAN@ OCT 1700', 'fra oktober 1700 e.v.t'],
            ['AFT @#DJULIAN@ OCT 1700', 'efter oktober 1700 e.v.t'],
            ['BEF @#DJULIAN@ OCT 1700', 'før oktober 1700 e.v.t'],
            ['@#DJULIAN@ 15 NOV 1700', '15. november 1700 e.v.t'],
            ['@#DJULIAN@ NOV 1700', 'november 1700 e.v.t'],
            ['ABT @#DJULIAN@ NOV 1700', 'omkring november 1700 e.v.t'],
            ['FROM @#DJULIAN@ NOV 1700', 'fra november 1700 e.v.t'],
            ['AFT @#DJULIAN@ NOV 1700', 'efter november 1700 e.v.t'],
            ['BEF @#DJULIAN@ NOV 1700', 'før november 1700 e.v.t'],
            ['@#DJULIAN@ 15 DEC 1700', '15. december 1700 e.v.t'],
            ['@#DJULIAN@ DEC 1700', 'december 1700 e.v.t'],
            ['ABT @#DJULIAN@ DEC 1700', 'omkring december 1700 e.v.t'],
            ['FROM @#DJULIAN@ DEC 1700', 'fra december 1700 e.v.t'],
            ['AFT @#DJULIAN@ DEC 1700', 'efter december 1700 e.v.t'],
            ['BEF @#DJULIAN@ DEC 1700', 'før december 1700 e.v.t'],
            ['@#DJULIAN@ 1700', '1700 e.v.t'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'omkring 15. januar 1700 e.v.t'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'beregnet 15. januar 1700 e.v.t'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'omkring 15. januar 1700 e.v.t'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'før 15. januar 1700 e.v.t'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'efter 15. januar 1700 e.v.t'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'fra 15. januar 1700 e.v.t'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'til 15. januar 1700 e.v.t'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'mellem 15. januar 1700 e.v.t og 15. februar 1700 e.v.t'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'fra 15. januar 1700 e.v.t til 15. februar 1700 e.v.t'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'fortolket 15. januar 1700 e.v.t'],
            ['@#DHEBREW@ 15 TSH 5765', '15. Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'omkring Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'fra Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'efter Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'før Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'omkring Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'fra Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'efter Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'før Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'omkring Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'fra Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'efter Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'før Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'omkring Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'fra Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'efter Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'før Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'omkring Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'fra Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'efter Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'før Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'omkring Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'fra Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'efter Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'før Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'omkring Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'fra Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'efter Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'før Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'omkring Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'fra Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'efter Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'før Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'omkring Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'fra Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'efter Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'før Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'omkring Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'fra Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'efter Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'før Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'omkring Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'fra Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'efter Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'før Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'omkring Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'fra Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'efter Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'før Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'omkring Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'fra Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'efter Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'før Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'omkring 15. Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'beregnet 15. Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'omkring 15. Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'før 15. Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'efter 15. Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'fra 15. Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'til 15. Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'mellem 15. Tishrei 5765 og 15. Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'fra 15. Tishrei 5765 til 15. Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'fortolket 15. Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'omkring Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'fra Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'efter Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'før Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'omkring Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'fra Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'efter Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'før Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'omkring Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'fra Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'efter Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'før Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'omkring Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'fra Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'efter Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'før Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'omkring Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'fra Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'efter Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'før Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'omkring Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'fra Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'efter Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'før Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'omkring Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'fra Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'efter Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'før Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'omkring Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'fra Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'efter Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'før Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'omkring Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'fra Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'efter Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'før Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'omkring Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'fra Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'efter Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'før Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'omkring Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'fra Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'efter Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'før Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'omkring Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'fra Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'efter Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'før Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'omkring jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'fra jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'efter jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'før jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'omkring 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'beregnet 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'omkring 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'før 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'efter 15. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'fra 15. Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'til 15. Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'mellem 15. Vendémiaire An XII og 15. Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'fra 15. Vendémiaire An XII til 15. Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'fortolket 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'omkring Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'fra Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'efter Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'før Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'omkring Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'fra Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'efter Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'før Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rabi al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'omkring Rabi al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'fra Rabi al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'efter Rabi al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'før Rabi al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rabi al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'omkring Rabi al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'fra Rabi al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'efter Rabi al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'før Rabi al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'omkring Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'fra Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'efter Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'før Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'omkring Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'fra Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'efter Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'før Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'omkring Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'fra Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'efter Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'før Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'omkring Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'fra Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'efter Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'før Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'omkring Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'fra Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'efter Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'før Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'omkring Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'fra Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'efter Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'før Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'omkring Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'fra Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'efter Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'før Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'omkring 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'fra 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'efter 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'før 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'omkring 15. Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'beregnet 15. Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'omkring 15. Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'før 15. Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'efter 15. Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'fra 15. Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'til 15. Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'mellem 15. Muharram 1425 og 15. Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'fra 15. Muharram 1425 til 15. Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'fortolket 15. Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'omkring Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'fra Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'efter Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'før Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'omkring Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'fra Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'efter Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'før Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'omkring Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'fra Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'efter Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'før Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'omkring Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'fra Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'efter Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'før Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'omkring Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'fra Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'efter Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'før Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'omkring Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'fra Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'efter Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'før Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'omkring Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'fra Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'efter Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'før Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'omkring Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'fra Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'efter Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'før Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'omkring Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'fra Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'efter Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'før Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'omkring Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'fra Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'efter Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'før Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'omkring Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'fra Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'efter Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'før Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'omkring Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'fra Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'efter Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'før Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'omkring 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'beregnet 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'omkring 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'før 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'efter 15. Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'fra 15. Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'til 15. Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'mellem 15. Farvardin 1384 og 15. Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'fra 15. Farvardin 1384 til 15. Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'fortolket 15. Farvardin 1384'],
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
        self::assertRelationshipNames('hustru', 'mand', [$husband, $fm, $wife]);
        self::assertRelationshipNames('eksmand', 'ekskone', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('forlovede', 'forlovede', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mor', 'søn', [$son, $fm, $wife]);
        self::assertRelationshipNames('far', 'søn', [$son, $fm, $husband]);
        self::assertRelationshipNames('mor', 'datter', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('lillesøster', 'storebror', [$son, $fm, $daughter]);
        self::assertRelationshipNames('storebror', 'lillesøster', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('halvbror', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('stedfar', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('steddatter', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('svigermor', 'svigersøn', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('svigerfar', 'svigersøn', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('svigerdatter', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents (paternal/maternal specific)
        self::assertRelationshipNames('farmor', 'barnebarn', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('farfar', 'barnebarn', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipNames('mormor', 'barnebarn', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('morfar', 'barnebarn', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('tipoldefar', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('tipoldemor', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles (paternal/maternal specific)
        self::assertRelationshipNames('faster', 'nevø', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('farbror', 'nevø', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('niece', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nevø', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('kusine', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('fætter', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }
}
