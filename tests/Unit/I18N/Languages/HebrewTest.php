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
use Fisharebest\Webtrees\I18N\Languages\Hebrew;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Hebrew::class)]
class HebrewTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Hebrew();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Hebr, self::language()->script());
    }

    public function testFirstDay(): void
    {
        self::assertSame(Weekday::Sunday, self::language()->firstDay());
    }
    public function testPaperSize(): void
    {
        self::assertSame(PaperSize::A4, self::language()->paperSize());
    }


    public function testTextDirection(): void
    {
        self::assertSame(TextDirection::RTL, self::language()->textDirection());
    }

    public function testAlphabet(): void
    {
        self::assertSame(['א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'כ', 'ל', 'מ', 'נ', 'ס', 'ע', 'פ', 'צ', 'ק', 'ר', 'ש', 'ת'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('he', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('עברית', self::language()->endonym());
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
        self::assertSame('‎-123,456.0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('‎-123,456.0789%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 ינואר 2000'],
            ['JAN 2000', 'ינואר 2000'],
            ['ABT JAN 2000', 'בערך ינואר 2000'],
            ['FROM JAN 2000', 'מינואר 2000'],
            ['AFT JAN 2000', 'אחרי ינואר 2000'],
            ['BEF JAN 2000', 'לפני ינואר 2000'],
            ['15 FEB 2000', '15 פברואר 2000'],
            ['FEB 2000', 'פברואר 2000'],
            ['ABT FEB 2000', 'בערך פברואר 2000'],
            ['FROM FEB 2000', 'מפברואר 2000'],
            ['AFT FEB 2000', 'אחרי פברואר 2000'],
            ['BEF FEB 2000', 'לפני פברואר 2000'],
            ['15 MAR 2000', '15 מרץ 2000'],
            ['MAR 2000', 'מרץ 2000'],
            ['ABT MAR 2000', 'בערך מרץ 2000'],
            ['FROM MAR 2000', 'ממרץ 2000'],
            ['AFT MAR 2000', 'אחרי מרץ 2000'],
            ['BEF MAR 2000', 'לפני מרץ 2000'],
            ['15 APR 2000', '15 אפריל 2000'],
            ['APR 2000', 'אפריל 2000'],
            ['ABT APR 2000', 'בערך אפריל 2000'],
            ['FROM APR 2000', 'מאפריל 2000'],
            ['AFT APR 2000', 'אחרי אפריל 2000'],
            ['BEF APR 2000', 'לפני אפריל 2000'],
            ['15 MAY 2000', '15 מאי 2000'],
            ['MAY 2000', 'מאי 2000'],
            ['ABT MAY 2000', 'בערך מאי 2000'],
            ['FROM MAY 2000', 'ממאי 2000'],
            ['AFT MAY 2000', 'אחרי מאי 2000'],
            ['BEF MAY 2000', 'לפני מאי 2000'],
            ['15 JUN 2000', '15 יוני 2000'],
            ['JUN 2000', 'יוני 2000'],
            ['ABT JUN 2000', 'בערך יוני 2000'],
            ['FROM JUN 2000', 'מיוני 2000'],
            ['AFT JUN 2000', 'אחרי יוני 2000'],
            ['BEF JUN 2000', 'לפני יוני 2000'],
            ['15 JUL 2000', '15 יולי 2000'],
            ['JUL 2000', 'יולי 2000'],
            ['ABT JUL 2000', 'בערך יולי 2000'],
            ['FROM JUL 2000', 'מיולי 2000'],
            ['AFT JUL 2000', 'אחרי יולי 2000'],
            ['BEF JUL 2000', 'לפני יולי 2000'],
            ['15 AUG 2000', '15 אוגוסט 2000'],
            ['AUG 2000', 'אוגוסט 2000'],
            ['ABT AUG 2000', 'בערך אוגוסט 2000'],
            ['FROM AUG 2000', 'מאוגוסט 2000'],
            ['AFT AUG 2000', 'אחרי אוגוסט 2000'],
            ['BEF AUG 2000', 'לפני אוגוסט 2000'],
            ['15 SEP 2000', '15 ספטמבר 2000'],
            ['SEP 2000', 'ספטמבר 2000'],
            ['ABT SEP 2000', 'בערך ספטמבר 2000'],
            ['FROM SEP 2000', 'מספטמבר 2000'],
            ['AFT SEP 2000', 'אחרי ספטמבר 2000'],
            ['BEF SEP 2000', 'לפני ספטמבר 2000'],
            ['15 OCT 2000', '15 אוקטובר 2000'],
            ['OCT 2000', 'אוקטובר 2000'],
            ['ABT OCT 2000', 'בערך אוקטובר 2000'],
            ['FROM OCT 2000', 'מאוקטובר 2000'],
            ['AFT OCT 2000', 'אחרי אוקטובר 2000'],
            ['BEF OCT 2000', 'לפני אוקטובר 2000'],
            ['15 NOV 2000', '15 נובמבר 2000'],
            ['NOV 2000', 'נובמבר 2000'],
            ['ABT NOV 2000', 'בערך נובמבר 2000'],
            ['FROM NOV 2000', 'מנובמבר 2000'],
            ['AFT NOV 2000', 'אחרי נובמבר 2000'],
            ['BEF NOV 2000', 'לפני נובמבר 2000'],
            ['15 DEC 2000', '15 דצמבר 2000'],
            ['DEC 2000', 'דצמבר 2000'],
            ['ABT DEC 2000', 'בערך דצמבר 2000'],
            ['FROM DEC 2000', 'מדצמבר 2000'],
            ['AFT DEC 2000', 'אחרי דצמבר 2000'],
            ['BEF DEC 2000', 'לפני דצמבר 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'בערך 15 ינואר 2000'],
            ['CAL 15 JAN 2000', 'מחושב 15 ינואר 2000'],
            ['EST 15 JAN 2000', 'מוערך 15 ינואר 2000'],
            ['BEF 15 JAN 2000', 'לפני 15 ינואר 2000'],
            ['AFT 15 JAN 2000', 'אחרי 15 ינואר 2000'],
            ['FROM 15 JAN 2000', 'מ15 ינואר 2000'],
            ['TO 15 JAN 2000', 'עד 15 ינואר 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'בין 15 ינואר 2000 ל15 פברואר 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'מ15 ינואר 2000 עד 15 פברואר 2000'],
            ['INT 15 JAN 2000', 'פרשנות 15 ינואר 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 ינואר 1700 אחה”ס'],
            ['@#DJULIAN@ JAN 1700', 'ינואר 1700 אחה”ס'],
            ['ABT @#DJULIAN@ JAN 1700', 'בערך ינואר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ JAN 1700', 'מינואר 1700 אחה”ס'],
            ['AFT @#DJULIAN@ JAN 1700', 'אחרי ינואר 1700 אחה”ס'],
            ['BEF @#DJULIAN@ JAN 1700', 'לפני ינואר 1700 אחה”ס'],
            ['@#DJULIAN@ 15 FEB 1700', '15 פברואר 1700 אחה”ס'],
            ['@#DJULIAN@ FEB 1700', 'פברואר 1700 אחה”ס'],
            ['ABT @#DJULIAN@ FEB 1700', 'בערך פברואר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ FEB 1700', 'מפברואר 1700 אחה”ס'],
            ['AFT @#DJULIAN@ FEB 1700', 'אחרי פברואר 1700 אחה”ס'],
            ['BEF @#DJULIAN@ FEB 1700', 'לפני פברואר 1700 אחה”ס'],
            ['@#DJULIAN@ 15 MAR 1700', '15 מרץ 1700 אחה”ס'],
            ['@#DJULIAN@ MAR 1700', 'מרץ 1700 אחה”ס'],
            ['ABT @#DJULIAN@ MAR 1700', 'בערך מרץ 1700 אחה”ס'],
            ['FROM @#DJULIAN@ MAR 1700', 'ממרץ 1700 אחה”ס'],
            ['AFT @#DJULIAN@ MAR 1700', 'אחרי מרץ 1700 אחה”ס'],
            ['BEF @#DJULIAN@ MAR 1700', 'לפני מרץ 1700 אחה”ס'],
            ['@#DJULIAN@ 15 APR 1700', '15 אפריל 1700 אחה”ס'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 אפריל 1645/46 אחה”ס'],
            ['@#DJULIAN@ APR 1700', 'אפריל 1700 אחה”ס'],
            ['ABT @#DJULIAN@ APR 1700', 'בערך אפריל 1700 אחה”ס'],
            ['FROM @#DJULIAN@ APR 1700', 'מאפריל 1700 אחה”ס'],
            ['AFT @#DJULIAN@ APR 1700', 'אחרי אפריל 1700 אחה”ס'],
            ['BEF @#DJULIAN@ APR 1700', 'לפני אפריל 1700 אחה”ס'],
            ['@#DJULIAN@ 15 MAY 1700', '15 מאי 1700 אחה”ס'],
            ['@#DJULIAN@ MAY 1700', 'מאי 1700 אחה”ס'],
            ['ABT @#DJULIAN@ MAY 1700', 'בערך מאי 1700 אחה”ס'],
            ['FROM @#DJULIAN@ MAY 1700', 'ממאי 1700 אחה”ס'],
            ['AFT @#DJULIAN@ MAY 1700', 'אחרי מאי 1700 אחה”ס'],
            ['BEF @#DJULIAN@ MAY 1700', 'לפני מאי 1700 אחה”ס'],
            ['@#DJULIAN@ 15 JUN 1700', '15 יוני 1700 אחה”ס'],
            ['@#DJULIAN@ JUN 1700', 'יוני 1700 אחה”ס'],
            ['ABT @#DJULIAN@ JUN 1700', 'בערך יוני 1700 אחה”ס'],
            ['FROM @#DJULIAN@ JUN 1700', 'מיוני 1700 אחה”ס'],
            ['AFT @#DJULIAN@ JUN 1700', 'אחרי יוני 1700 אחה”ס'],
            ['BEF @#DJULIAN@ JUN 1700', 'לפני יוני 1700 אחה”ס'],
            ['@#DJULIAN@ 15 JUL 1700', '15 יולי 1700 אחה”ס'],
            ['@#DJULIAN@ JUL 1700', 'יולי 1700 אחה”ס'],
            ['ABT @#DJULIAN@ JUL 1700', 'בערך יולי 1700 אחה”ס'],
            ['FROM @#DJULIAN@ JUL 1700', 'מיולי 1700 אחה”ס'],
            ['AFT @#DJULIAN@ JUL 1700', 'אחרי יולי 1700 אחה”ס'],
            ['BEF @#DJULIAN@ JUL 1700', 'לפני יולי 1700 אחה”ס'],
            ['@#DJULIAN@ 15 AUG 1700', '15 אוגוסט 1700 אחה”ס'],
            ['@#DJULIAN@ AUG 1700', 'אוגוסט 1700 אחה”ס'],
            ['ABT @#DJULIAN@ AUG 1700', 'בערך אוגוסט 1700 אחה”ס'],
            ['FROM @#DJULIAN@ AUG 1700', 'מאוגוסט 1700 אחה”ס'],
            ['AFT @#DJULIAN@ AUG 1700', 'אחרי אוגוסט 1700 אחה”ס'],
            ['BEF @#DJULIAN@ AUG 1700', 'לפני אוגוסט 1700 אחה”ס'],
            ['@#DJULIAN@ 15 SEP 1700', '15 ספטמבר 1700 אחה”ס'],
            ['@#DJULIAN@ SEP 1700', 'ספטמבר 1700 אחה”ס'],
            ['ABT @#DJULIAN@ SEP 1700', 'בערך ספטמבר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ SEP 1700', 'מספטמבר 1700 אחה”ס'],
            ['AFT @#DJULIAN@ SEP 1700', 'אחרי ספטמבר 1700 אחה”ס'],
            ['BEF @#DJULIAN@ SEP 1700', 'לפני ספטמבר 1700 אחה”ס'],
            ['@#DJULIAN@ 15 OCT 1700', '15 אוקטובר 1700 אחה”ס'],
            ['@#DJULIAN@ OCT 1700', 'אוקטובר 1700 אחה”ס'],
            ['ABT @#DJULIAN@ OCT 1700', 'בערך אוקטובר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ OCT 1700', 'מאוקטובר 1700 אחה”ס'],
            ['AFT @#DJULIAN@ OCT 1700', 'אחרי אוקטובר 1700 אחה”ס'],
            ['BEF @#DJULIAN@ OCT 1700', 'לפני אוקטובר 1700 אחה”ס'],
            ['@#DJULIAN@ 15 NOV 1700', '15 נובמבר 1700 אחה”ס'],
            ['@#DJULIAN@ NOV 1700', 'נובמבר 1700 אחה”ס'],
            ['ABT @#DJULIAN@ NOV 1700', 'בערך נובמבר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ NOV 1700', 'מנובמבר 1700 אחה”ס'],
            ['AFT @#DJULIAN@ NOV 1700', 'אחרי נובמבר 1700 אחה”ס'],
            ['BEF @#DJULIAN@ NOV 1700', 'לפני נובמבר 1700 אחה”ס'],
            ['@#DJULIAN@ 15 DEC 1700', '15 דצמבר 1700 אחה”ס'],
            ['@#DJULIAN@ DEC 1700', 'דצמבר 1700 אחה”ס'],
            ['ABT @#DJULIAN@ DEC 1700', 'בערך דצמבר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ DEC 1700', 'מדצמבר 1700 אחה”ס'],
            ['AFT @#DJULIAN@ DEC 1700', 'אחרי דצמבר 1700 אחה”ס'],
            ['BEF @#DJULIAN@ DEC 1700', 'לפני דצמבר 1700 אחה”ס'],
            ['@#DJULIAN@ 1700', '1700 אחה”ס'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'בערך 15 ינואר 1700 אחה”ס'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'מחושב 15 ינואר 1700 אחה”ס'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'מוערך 15 ינואר 1700 אחה”ס'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'לפני 15 ינואר 1700 אחה”ס'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'אחרי 15 ינואר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'מ15 ינואר 1700 אחה”ס'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'עד 15 ינואר 1700 אחה”ס'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'בין 15 ינואר 1700 אחה”ס ל15 פברואר 1700 אחה”ס'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'מ15 ינואר 1700 אחה”ס עד 15 פברואר 1700 אחה”ס'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'פרשנות 15 ינואר 1700 אחה”ס'],
            ['@#DHEBREW@ 15 TSH 5765', 'ט״ו בתשרי תשס״ה'],
            ['@#DHEBREW@ TSH 5765', 'תשרי תשס״ה'],
            ['ABT @#DHEBREW@ TSH 5765', 'בערך בתשרי תשס״ה'],
            ['FROM @#DHEBREW@ TSH 5765', 'מבתשרי תשס״ה'],
            ['AFT @#DHEBREW@ TSH 5765', 'אחרי תשרי תשס״ה'],
            ['BEF @#DHEBREW@ TSH 5765', 'לפני תשרי תשס״ה'],
            ['@#DHEBREW@ 15 CSH 5765', 'ט״ו בחשוון תשס״ה'],
            ['@#DHEBREW@ CSH 5765', 'חשוון תשס״ה'],
            ['ABT @#DHEBREW@ CSH 5765', 'בערך בחשוון תשס״ה'],
            ['FROM @#DHEBREW@ CSH 5765', 'מבחשוון תשס״ה'],
            ['AFT @#DHEBREW@ CSH 5765', 'אחרי חשוון תשס״ה'],
            ['BEF @#DHEBREW@ CSH 5765', 'לפני חשוון תשס״ה'],
            ['@#DHEBREW@ 15 KSL 5765', 'ט״ו בכסלו תשס״ה'],
            ['@#DHEBREW@ KSL 5765', 'כסלו תשס״ה'],
            ['ABT @#DHEBREW@ KSL 5765', 'בערך בכסלו תשס״ה'],
            ['FROM @#DHEBREW@ KSL 5765', 'מבכסלו תשס״ה'],
            ['AFT @#DHEBREW@ KSL 5765', 'אחרי כסלו תשס״ה'],
            ['BEF @#DHEBREW@ KSL 5765', 'לפני כסלו תשס״ה'],
            ['@#DHEBREW@ 15 TVT 5765', 'ט״ו בטבת תשס״ה'],
            ['@#DHEBREW@ TVT 5765', 'טבת תשס״ה'],
            ['ABT @#DHEBREW@ TVT 5765', 'בערך בטבת תשס״ה'],
            ['FROM @#DHEBREW@ TVT 5765', 'מבטבת תשס״ה'],
            ['AFT @#DHEBREW@ TVT 5765', 'אחרי טבת תשס״ה'],
            ['BEF @#DHEBREW@ TVT 5765', 'לפני טבת תשס״ה'],
            ['@#DHEBREW@ 15 SHV 5765', 'ט״ו בשבט תשס״ה'],
            ['@#DHEBREW@ SHV 5765', 'שבט תשס״ה'],
            ['ABT @#DHEBREW@ SHV 5765', 'בערך בשבט תשס״ה'],
            ['FROM @#DHEBREW@ SHV 5765', 'מבשבט תשס״ה'],
            ['AFT @#DHEBREW@ SHV 5765', 'אחרי שבט תשס״ה'],
            ['BEF @#DHEBREW@ SHV 5765', 'לפני שבט תשס״ה'],
            ['@#DHEBREW@ 15 ADR 5765', 'ט״ו באדר א׳ תשס״ה'],
            ['@#DHEBREW@ ADR 5765', 'אדר א׳ תשס״ה'],
            ['ABT @#DHEBREW@ ADR 5765', 'בערך באדר א׳ תשס״ה'],
            ['FROM @#DHEBREW@ ADR 5765', 'מבאדר א׳ תשס״ה'],
            ['AFT @#DHEBREW@ ADR 5765', 'אחרי אדר א׳ תשס״ה'],
            ['BEF @#DHEBREW@ ADR 5765', 'לפני אדר א׳ תשס״ה'],
            ['@#DHEBREW@ 15 ADS 5765', 'ט״ו באדר ב׳ תשס״ה'],
            ['@#DHEBREW@ ADS 5765', 'אדר ב׳ תשס״ה'],
            ['ABT @#DHEBREW@ ADS 5765', 'בערך באדר ב׳ תשס״ה'],
            ['FROM @#DHEBREW@ ADS 5765', 'מבאדר ב׳ תשס״ה'],
            ['AFT @#DHEBREW@ ADS 5765', 'אחרי אדר ב׳ תשס״ה'],
            ['BEF @#DHEBREW@ ADS 5765', 'לפני אדר ב׳ תשס״ה'],
            ['@#DHEBREW@ 15 NSN 5765', 'ט״ו בניסן תשס״ה'],
            ['@#DHEBREW@ NSN 5765', 'ניסן תשס״ה'],
            ['ABT @#DHEBREW@ NSN 5765', 'בערך בניסן תשס״ה'],
            ['FROM @#DHEBREW@ NSN 5765', 'מבניסן תשס״ה'],
            ['AFT @#DHEBREW@ NSN 5765', 'אחרי ניסן תשס״ה'],
            ['BEF @#DHEBREW@ NSN 5765', 'לפני ניסן תשס״ה'],
            ['@#DHEBREW@ 15 IYR 5765', 'ט״ו באייר תשס״ה'],
            ['@#DHEBREW@ IYR 5765', 'אייר תשס״ה'],
            ['ABT @#DHEBREW@ IYR 5765', 'בערך באייר תשס״ה'],
            ['FROM @#DHEBREW@ IYR 5765', 'מבאייר תשס״ה'],
            ['AFT @#DHEBREW@ IYR 5765', 'אחרי אייר תשס״ה'],
            ['BEF @#DHEBREW@ IYR 5765', 'לפני אייר תשס״ה'],
            ['@#DHEBREW@ 15 SVN 5765', 'ט״ו בסיוון תשס״ה'],
            ['@#DHEBREW@ SVN 5765', 'סיוון תשס״ה'],
            ['ABT @#DHEBREW@ SVN 5765', 'בערך בסיוון תשס״ה'],
            ['FROM @#DHEBREW@ SVN 5765', 'מבסיוון תשס״ה'],
            ['AFT @#DHEBREW@ SVN 5765', 'אחרי סיוון תשס״ה'],
            ['BEF @#DHEBREW@ SVN 5765', 'לפני סיוון תשס״ה'],
            ['@#DHEBREW@ 15 TMZ 5765', 'ט״ו בתמוז תשס״ה'],
            ['@#DHEBREW@ TMZ 5765', 'תמוז תשס״ה'],
            ['ABT @#DHEBREW@ TMZ 5765', 'בערך בתמוז תשס״ה'],
            ['FROM @#DHEBREW@ TMZ 5765', 'מבתמוז תשס״ה'],
            ['AFT @#DHEBREW@ TMZ 5765', 'אחרי תמוז תשס״ה'],
            ['BEF @#DHEBREW@ TMZ 5765', 'לפני תמוז תשס״ה'],
            ['@#DHEBREW@ 15 AAV 5765', 'ט״ו באב תשס״ה'],
            ['@#DHEBREW@ AAV 5765', 'אב תשס״ה'],
            ['ABT @#DHEBREW@ AAV 5765', 'בערך באב תשס״ה'],
            ['FROM @#DHEBREW@ AAV 5765', 'מבאב תשס״ה'],
            ['AFT @#DHEBREW@ AAV 5765', 'אחרי אב תשס״ה'],
            ['BEF @#DHEBREW@ AAV 5765', 'לפני אב תשס״ה'],
            ['@#DHEBREW@ 15 ELL 5765', 'ט״ו באלול תשס״ה'],
            ['@#DHEBREW@ ELL 5765', 'אלול תשס״ה'],
            ['ABT @#DHEBREW@ ELL 5765', 'בערך באלול תשס״ה'],
            ['FROM @#DHEBREW@ ELL 5765', 'מבאלול תשס״ה'],
            ['AFT @#DHEBREW@ ELL 5765', 'אחרי אלול תשס״ה'],
            ['BEF @#DHEBREW@ ELL 5765', 'לפני אלול תשס״ה'],
            ['@#DHEBREW@ 5765', 'תשס״ה'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'בערך ט״ו בתשרי תשס״ה'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'מחושב ט״ו בתשרי תשס״ה'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'מוערך ט״ו בתשרי תשס״ה'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'לפני ט״ו בתשרי תשס״ה'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'אחרי ט״ו בתשרי תשס״ה'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'מט״ו בתשרי תשס״ה'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'עד ט״ו בתשרי תשס״ה'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'בין ט״ו בתשרי תשס״ה לט״ו בחשוון תשס״ה'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'מט״ו בתשרי תשס״ה עד ט״ו בחשוון תשס״ה'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'פרשנות ט״ו בתשרי תשס״ה'],
            ['@#DFRENCH R@ 15 VEND 12', '15 ונדמיר An XII'],
            ['@#DFRENCH R@ VEND 12', 'ונדמיר An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'בערך ונדמיר An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'מונדמיר An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'אחרי ונדמיר An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'לפני ונדמיר An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 ברימר An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ברימר An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'בערך ברימר An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'מברימר An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'אחרי ברימר An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'לפני ברימר An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 פרימר An XII'],
            ['@#DFRENCH R@ FRIM 12', 'פרימר An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'בערך פרימר An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'מפרימר An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'אחרי פרימר An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'לפני פרימר An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 ניבוז An XII'],
            ['@#DFRENCH R@ NIVO 12', 'ניבוז An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'בערך ניבוז An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'מניבוז An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'אחרי ניבוז An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'לפני ניבוז An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 פליביוז An XII'],
            ['@#DFRENCH R@ PLUV 12', 'פליביוז An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'בערך פליביוז An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'מפליביוז An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'אחרי פליביוז An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'לפני פליביוז An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ונטוז An XII'],
            ['@#DFRENCH R@ VENT 12', 'ונטוז An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'בערך ונטוז An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'מונטוז An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'אחרי ונטוז An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'לפני ונטוז An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 ז׳רמינאל An XII'],
            ['@#DFRENCH R@ GERM 12', 'ז׳רמינאל An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'בערך ז׳רמינאל An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'מז׳רמינאל An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'אחרי ז׳רמינאל An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'לפני ז׳רמינאל An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 פלוראל An XII'],
            ['@#DFRENCH R@ FLOR 12', 'פלוראל An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'בערך פלוראל An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'מפלוראל An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'אחרי פלוראל An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'לפני פלוראל An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 פריריאל An XII'],
            ['@#DFRENCH R@ PRAI 12', 'פריריאל An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'בערך פריריאל An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'מפריריאל An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'אחרי פריריאל An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'לפני פריריאל An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 מסידור An XII'],
            ['@#DFRENCH R@ MESS 12', 'מסידור An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'בערך מסידור An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'ממסידור An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'אחרי מסידור An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'לפני מסידור An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 תרמידור An XII'],
            ['@#DFRENCH R@ THER 12', 'תרמידור An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'בערך תרמידור An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'מתרמידור An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'אחרי תרמידור An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'לפני תרמידור An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 פרוקטידור An XII'],
            ['@#DFRENCH R@ FRUC 12', 'פרוקטידור An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'בערך פרוקטידור An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'מפרוקטידור An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'אחרי פרוקטידור An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'לפני פרוקטידור An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 ימים משלימים An XII'],
            ['@#DFRENCH R@ COMP 12', 'ימים משלימים An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'בערך ימים משלימים An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'מימים משלימים An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'אחרי ימים משלימים An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'לפני ימים משלימים An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'בערך 15 ונדמיר An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'מחושב 15 ונדמיר An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'מוערך 15 ונדמיר An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'לפני 15 ונדמיר An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'אחרי 15 ונדמיר An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'מ15 ונדמיר An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'עד 15 ונדמיר An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'בין 15 ונדמיר An XII ל15 ברימר An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'מ15 ונדמיר An XII עד 15 ברימר An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'פרשנות 15 ונדמיר An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 מוחרם 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'מוחרם 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'בערך מוחרם 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'ממוחרם 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'אחרי מוחרם 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'לפני מוחרם 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 צפר 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'צפר 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'בערך צפר 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'מצפר 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'אחרי צפר 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'לפני צפר 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 רביע אל-אוול 1425'],
            ['@#DHIJRI@ RABIA 1425', 'רביע אל-אוול 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'בערך רביע אל-אוול 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'מרביע אל-אוול 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'אחרי רביע אל-אוול 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'לפני רביע אל-אוול 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 רביע את-ת׳אני 1425'],
            ['@#DHIJRI@ RABIT 1425', 'רביע את-ת׳אני 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'בערך רביע את-ת׳אני 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'מרביע את-ת׳אני 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'אחרי רביע את-ת׳אני 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'לפני רביע את-ת׳אני 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 ג׳ומאדא אל-אוואל 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'ג׳ומאדא אל-אוואל 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'בערך ג׳ומאדא אל-אוואל 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'מג׳ומאדא אל-אוואל 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'אחרי ג׳ומאדא אל-אוואל 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'לפני ג׳ומאדא אל-אוואל 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 ג׳ומאדא אל-ת׳אניה 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'ג׳ומאדא אל-ת׳אניה 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'בערך ג׳ומאדא אל-ת׳אניה 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'מג׳ומאדא אל-ת׳אניה 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'אחרי ג׳ומאדא אל-ת׳אניה 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'לפני ג׳ומאדא אל-ת׳אניה 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 רג׳ב 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'רג׳ב 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'בערך רג׳ב 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'מרג׳ב 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'אחרי רג׳ב 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'לפני רג׳ב 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 שעבאן 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'שעבאן 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'בערך שעבאן 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'משעבאן 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'אחרי שעבאן 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'לפני שעבאן 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 רמדאן 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'רמדאן 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'בערך רמדאן 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'מרמדאן 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'אחרי רמדאן 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'לפני רמדאן 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 שוואל 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'שוואל 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'בערך שוואל 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'משוואל 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'אחרי שוואל 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'לפני שוואל 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 ז׳ו אל-קעדה 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'ז׳ו אל-קעדה 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'בערך ז׳ו אל-קעדה 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'מז׳ו אל-קעדה 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'אחרי ז׳ו אל-קעדה 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'לפני ז׳ו אל-קעדה 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'בערך 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'מ1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'אחרי 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'לפני 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'בערך 15 מוחרם 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'מחושב 15 מוחרם 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'מוערך 15 מוחרם 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'לפני 15 מוחרם 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'אחרי 15 מוחרם 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'מ15 מוחרם 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'עד 15 מוחרם 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'בין 15 מוחרם 1425 ל15 צפר 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'מ15 מוחרם 1425 עד 15 צפר 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'פרשנות 15 מוחרם 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 פרברדין 1384'],
            ['@#DJALALI@ FARVA 1384', 'פרברדין 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'בערך פרברדין 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'מפרברדין 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'אחרי פרברדין 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'לפני פרברדין 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 אורדיבהשת 1384'],
            ['@#DJALALI@ ORDIB 1384', 'אורדיבהשת 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'בערך אורדיבהשת 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'מאורדיבהשת 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'אחרי אורדיבהשת 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'לפני אורדיבהשת 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 חורדאד 1384'],
            ['@#DJALALI@ KHORD 1384', 'חורדאד 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'בערך חורדאד 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'מחורדאד 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'אחרי חורדאד 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'לפני חורדאד 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 טיר 1384'],
            ['@#DJALALI@ TIR 1384', 'טיר 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'בערך טיר 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'מטיר 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'אחרי טיר 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'לפני טיר 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 מורדאד 1384'],
            ['@#DJALALI@ MORDA 1384', 'מורדאד 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'בערך מורדאד 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'ממורדאד 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'אחרי מורדאד 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'לפני מורדאד 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 שהריבר 1384'],
            ['@#DJALALI@ SHAHR 1384', 'שהריבר 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'בערך שהריבר 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'משהריבר 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'אחרי שהריבר 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'לפני שהריבר 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 מהר 1384'],
            ['@#DJALALI@ MEHR 1384', 'מהר 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'בערך מהר 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'ממהר 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'אחרי מהר 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'לפני מהר 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 אבאן 1384'],
            ['@#DJALALI@ ABAN 1384', 'אבאן 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'בערך אבאן 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'מאבאן 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'אחרי אבאן 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'לפני אבאן 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 אזר 1384'],
            ['@#DJALALI@ AZAR 1384', 'אזר 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'בערך אזר 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'מאזר 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'אחרי אזר 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'לפני אזר 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 דיי 1384'],
            ['@#DJALALI@ DEY 1384', 'דיי 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'בערך דיי 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'מדיי 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'אחרי דיי 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'לפני דיי 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 בהמן 1384'],
            ['@#DJALALI@ BAHMA 1384', 'בהמן 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'בערך בהמן 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'מבהמן 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'אחרי בהמן 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'לפני בהמן 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 אספנד 1384'],
            ['@#DJALALI@ ESFAN 1384', 'אספנד 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'בערך אספנד 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'מאספנד 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'אחרי אספנד 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'לפני אספנד 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'בערך 15 פרברדין 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'מחושב 15 פרברדין 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'מוערך 15 פרברדין 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'לפני 15 פרברדין 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'אחרי 15 פרברדין 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'מ15 פרברדין 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'עד 15 פרברדין 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'בין 15 פרברדין 1384 ל15 אורדיבהשת 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'מ15 פרברדין 1384 עד 15 אורדיבהשת 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'פרשנות 15 פרברדין 1384'],
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
        self::assertSame('one וtwo', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two וthree', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one אוtwo', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two אוthree', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife + children
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1970");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 FAMS @fson@\n1 BIRT\n2 DATE 1 JAN 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 1 JAN 1998");
        $child = self::unknown('c', "1 FAMC @fm@");

        // Ex-husband (divorced)
        $exHusband = self::male('ex', "1 FAMS @fd@");
        // Adopted son
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        // Foster daughter
        $fosterDaughter = self::female('fsd', "1 FAMC @fd@\n2 PEDI foster");
        // Step-daughter
        $stepDaughter = self::female('sd', "1 FAMC @fd@");

        // Husband's parents
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@\n1 FAMS @fbro@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@\n1 FAMS @fsis@");

        // Wife's parents
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Nieces/nephews from brother
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        // Nieces/nephews from sister
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins
        $cousinM = self::male('cm', "1 FAMC @fbro@");
        $cousinF = self::female('cf', "1 FAMC @fsis@");

        // Grandparents
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");

        // Engaged
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        // Families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fsd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cm@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cf@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinM, $cousinF,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('אישה', 'בעל', [$husband, $fm, $wife]);
        self::assertRelationshipNames('גרוש', 'גרושה', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('ארוסה', 'ארוס', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('אם', 'בן', [$son, $fm, $wife]);
        self::assertRelationshipNames('אב', 'בן', [$son, $fm, $husband]);
        self::assertRelationshipNames('אם', 'בת', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('אם מאמצת', 'בן מאומץ', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('אב מאמץ', 'בן מאומץ', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('אם אומנת', 'בת אומנה', [$fosterDaughter, $fd, $wife]);

        // Siblings (older/younger)
        self::assertRelationshipNames('אחות גדולה', 'אח קטן', [$son, $fm, $daughter]);
        self::assertRelationshipNames('אח קטן', 'אחות גדולה', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('אב חורג', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('בת חורגת', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws (spouse's parents)
        self::assertRelationshipNames('חמות', 'חתן', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('חם', 'חתן', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // In-laws (child's spouse)
        self::assertRelationshipName('כלה', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('חתן', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws (spouse's siblings)
        self::assertRelationshipName('גיס', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('גיסה', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents
        self::assertRelationshipNames('סבתא', 'נכד', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('סבא', 'נכד', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('נכדה', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts/uncles
        self::assertRelationshipName('דודה', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('דוד', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces/nephews
        self::assertRelationshipName('אחיינית', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('אחיין', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        self::assertRelationshipName('אחיינית', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('אחיין', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins
        self::assertRelationshipName('בן דוד/ה', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinM]);
        self::assertRelationshipName('בת דוד/ה', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinF]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('סבתא רבה', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('סבא רבא', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('דודה רבה', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('דוד רבא', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
