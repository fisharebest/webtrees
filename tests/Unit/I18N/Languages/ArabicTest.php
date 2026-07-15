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
use Fisharebest\Webtrees\I18N\Languages\Arabic;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Arabic::class)]
class ArabicTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Arabic();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Arab, self::language()->script());
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
        self::assertSame(TextDirection::RTL, self::language()->textDirection());
    }

    public function testAlphabet(): void
    {
        self::assertSame(['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي', 'ء', 'ة', 'ى', 'و'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('ar', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('العربية', self::language()->endonym());
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
        self::assertSame('-١٢٣,٤٥٦.٠٧٨٩', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('؜-١٢٣٬٤٥٦٫٠٧٨٩', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('؜-١٢٣٬٤٥٦٫٠٧٨٩٪؜', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '١٥ يناير ٢٠٠٠'],
            ['JAN 2000', 'يناير ٢٠٠٠'],
            ['ABT JAN 2000', 'حوالي يناير ٢٠٠٠'],
            ['FROM JAN 2000', 'من يناير ٢٠٠٠'],
            ['AFT JAN 2000', 'بعد يناير ٢٠٠٠'],
            ['BEF JAN 2000', 'قبل يناير ٢٠٠٠'],
            ['15 FEB 2000', '١٥ فبراير ٢٠٠٠'],
            ['FEB 2000', 'فبراير ٢٠٠٠'],
            ['ABT FEB 2000', 'حوالي فبراير ٢٠٠٠'],
            ['FROM FEB 2000', 'من فبراير ٢٠٠٠'],
            ['AFT FEB 2000', 'بعد فبراير ٢٠٠٠'],
            ['BEF FEB 2000', 'قبل فبراير ٢٠٠٠'],
            ['15 MAR 2000', '١٥ مارس ٢٠٠٠'],
            ['MAR 2000', 'مارس ٢٠٠٠'],
            ['ABT MAR 2000', 'حوالي مارس ٢٠٠٠'],
            ['FROM MAR 2000', 'من مارس ٢٠٠٠'],
            ['AFT MAR 2000', 'بعد مارس ٢٠٠٠'],
            ['BEF MAR 2000', 'قبل مارس ٢٠٠٠'],
            ['15 APR 2000', '١٥ أبريل ٢٠٠٠'],
            ['APR 2000', 'أبريل ٢٠٠٠'],
            ['ABT APR 2000', 'حوالي أبريل ٢٠٠٠'],
            ['FROM APR 2000', 'من أبريل ٢٠٠٠'],
            ['AFT APR 2000', 'بعد أبريل ٢٠٠٠'],
            ['BEF APR 2000', 'قبل أبريل ٢٠٠٠'],
            ['15 MAY 2000', '١٥ مايو ٢٠٠٠'],
            ['MAY 2000', 'مايو ٢٠٠٠'],
            ['ABT MAY 2000', 'حوالي مايو ٢٠٠٠'],
            ['FROM MAY 2000', 'من مايو ٢٠٠٠'],
            ['AFT MAY 2000', 'بعد مايو ٢٠٠٠'],
            ['BEF MAY 2000', 'قبل مايو ٢٠٠٠'],
            ['15 JUN 2000', '١٥ يونيو ٢٠٠٠'],
            ['JUN 2000', 'يونيو ٢٠٠٠'],
            ['ABT JUN 2000', 'حوالي يونيو ٢٠٠٠'],
            ['FROM JUN 2000', 'من يونيو ٢٠٠٠'],
            ['AFT JUN 2000', 'بعد يونيو ٢٠٠٠'],
            ['BEF JUN 2000', 'قبل يونيو ٢٠٠٠'],
            ['15 JUL 2000', '١٥ يوليو ٢٠٠٠'],
            ['JUL 2000', 'يوليو ٢٠٠٠'],
            ['ABT JUL 2000', 'حوالي يوليو ٢٠٠٠'],
            ['FROM JUL 2000', 'من يوليو ٢٠٠٠'],
            ['AFT JUL 2000', 'بعد يوليو ٢٠٠٠'],
            ['BEF JUL 2000', 'قبل يوليو ٢٠٠٠'],
            ['15 AUG 2000', '١٥ أغسطس ٢٠٠٠'],
            ['AUG 2000', 'أغسطس ٢٠٠٠'],
            ['ABT AUG 2000', 'حوالي أغسطس ٢٠٠٠'],
            ['FROM AUG 2000', 'من أغسطس ٢٠٠٠'],
            ['AFT AUG 2000', 'بعد أغسطس ٢٠٠٠'],
            ['BEF AUG 2000', 'قبل أغسطس ٢٠٠٠'],
            ['15 SEP 2000', '١٥ سبتمبر ٢٠٠٠'],
            ['SEP 2000', 'سبتمبر ٢٠٠٠'],
            ['ABT SEP 2000', 'حوالي سبتمبر ٢٠٠٠'],
            ['FROM SEP 2000', 'من سبتمبر ٢٠٠٠'],
            ['AFT SEP 2000', 'بعد سبتمبر ٢٠٠٠'],
            ['BEF SEP 2000', 'قبل سبتمبر ٢٠٠٠'],
            ['15 OCT 2000', '١٥ أكتوبر ٢٠٠٠'],
            ['OCT 2000', 'أكتوبر ٢٠٠٠'],
            ['ABT OCT 2000', 'حوالي أكتوبر ٢٠٠٠'],
            ['FROM OCT 2000', 'من أكتوبر ٢٠٠٠'],
            ['AFT OCT 2000', 'بعد أكتوبر ٢٠٠٠'],
            ['BEF OCT 2000', 'قبل أكتوبر ٢٠٠٠'],
            ['15 NOV 2000', '١٥ نوفمبر ٢٠٠٠'],
            ['NOV 2000', 'نوفمبر ٢٠٠٠'],
            ['ABT NOV 2000', 'حوالي نوفمبر ٢٠٠٠'],
            ['FROM NOV 2000', 'من نوفمبر ٢٠٠٠'],
            ['AFT NOV 2000', 'بعد نوفمبر ٢٠٠٠'],
            ['BEF NOV 2000', 'قبل نوفمبر ٢٠٠٠'],
            ['15 DEC 2000', '١٥ ديسمبر ٢٠٠٠'],
            ['DEC 2000', 'ديسمبر ٢٠٠٠'],
            ['ABT DEC 2000', 'حوالي ديسمبر ٢٠٠٠'],
            ['FROM DEC 2000', 'من ديسمبر ٢٠٠٠'],
            ['AFT DEC 2000', 'بعد ديسمبر ٢٠٠٠'],
            ['BEF DEC 2000', 'قبل ديسمبر ٢٠٠٠'],
            ['2000', '٢٠٠٠'],
            ['ABT 15 JAN 2000', 'حوالي ١٥ يناير ٢٠٠٠'],
            ['CAL 15 JAN 2000', 'حسب ١٥ يناير ٢٠٠٠'],
            ['EST 15 JAN 2000', 'تقديراً ١٥ يناير ٢٠٠٠'],
            ['BEF 15 JAN 2000', 'قبل ١٥ يناير ٢٠٠٠'],
            ['AFT 15 JAN 2000', 'بعد ١٥ يناير ٢٠٠٠'],
            ['FROM 15 JAN 2000', 'من ١٥ يناير ٢٠٠٠'],
            ['TO 15 JAN 2000', 'إلى ١٥ يناير ٢٠٠٠'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'بين ١٥ يناير ٢٠٠٠ و ١٥ فبراير ٢٠٠٠'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'من ١٥ يناير ٢٠٠٠ إلى ١٥ فبراير ٢٠٠٠'],
            ['INT 15 JAN 2000', 'أعتبر ١٥ يناير ٢٠٠٠'],
            ['@#DJULIAN@ 15 JAN 1700', '١٥ يناير ١٧٠٠ م'],
            ['@#DJULIAN@ JAN 1700', 'يناير ١٧٠٠ م'],
            ['ABT @#DJULIAN@ JAN 1700', 'حوالي يناير ١٧٠٠ م'],
            ['FROM @#DJULIAN@ JAN 1700', 'من يناير ١٧٠٠ م'],
            ['AFT @#DJULIAN@ JAN 1700', 'بعد يناير ١٧٠٠ م'],
            ['BEF @#DJULIAN@ JAN 1700', 'قبل يناير ١٧٠٠ م'],
            ['@#DJULIAN@ 15 FEB 1700', '١٥ فبراير ١٧٠٠ م'],
            ['@#DJULIAN@ FEB 1700', 'فبراير ١٧٠٠ م'],
            ['ABT @#DJULIAN@ FEB 1700', 'حوالي فبراير ١٧٠٠ م'],
            ['FROM @#DJULIAN@ FEB 1700', 'من فبراير ١٧٠٠ م'],
            ['AFT @#DJULIAN@ FEB 1700', 'بعد فبراير ١٧٠٠ م'],
            ['BEF @#DJULIAN@ FEB 1700', 'قبل فبراير ١٧٠٠ م'],
            ['@#DJULIAN@ 15 MAR 1700', '١٥ مارس ١٧٠٠ م'],
            ['@#DJULIAN@ MAR 1700', 'مارس ١٧٠٠ م'],
            ['ABT @#DJULIAN@ MAR 1700', 'حوالي مارس ١٧٠٠ م'],
            ['FROM @#DJULIAN@ MAR 1700', 'من مارس ١٧٠٠ م'],
            ['AFT @#DJULIAN@ MAR 1700', 'بعد مارس ١٧٠٠ م'],
            ['BEF @#DJULIAN@ MAR 1700', 'قبل مارس ١٧٠٠ م'],
            ['@#DJULIAN@ 15 APR 1700', '١٥ أبريل ١٧٠٠ م'],
            ['@#DJULIAN@ 14 APR 1645/46', '١٤ أبريل ١٦٤٥/٤٦ م'],
            ['@#DJULIAN@ APR 1700', 'أبريل ١٧٠٠ م'],
            ['ABT @#DJULIAN@ APR 1700', 'حوالي أبريل ١٧٠٠ م'],
            ['FROM @#DJULIAN@ APR 1700', 'من أبريل ١٧٠٠ م'],
            ['AFT @#DJULIAN@ APR 1700', 'بعد أبريل ١٧٠٠ م'],
            ['BEF @#DJULIAN@ APR 1700', 'قبل أبريل ١٧٠٠ م'],
            ['@#DJULIAN@ 15 MAY 1700', '١٥ مايو ١٧٠٠ م'],
            ['@#DJULIAN@ MAY 1700', 'مايو ١٧٠٠ م'],
            ['ABT @#DJULIAN@ MAY 1700', 'حوالي مايو ١٧٠٠ م'],
            ['FROM @#DJULIAN@ MAY 1700', 'من مايو ١٧٠٠ م'],
            ['AFT @#DJULIAN@ MAY 1700', 'بعد مايو ١٧٠٠ م'],
            ['BEF @#DJULIAN@ MAY 1700', 'قبل مايو ١٧٠٠ م'],
            ['@#DJULIAN@ 15 JUN 1700', '١٥ يونيو ١٧٠٠ م'],
            ['@#DJULIAN@ JUN 1700', 'يونيو ١٧٠٠ م'],
            ['ABT @#DJULIAN@ JUN 1700', 'حوالي يونيو ١٧٠٠ م'],
            ['FROM @#DJULIAN@ JUN 1700', 'من يونيو ١٧٠٠ م'],
            ['AFT @#DJULIAN@ JUN 1700', 'بعد يونيو ١٧٠٠ م'],
            ['BEF @#DJULIAN@ JUN 1700', 'قبل يونيو ١٧٠٠ م'],
            ['@#DJULIAN@ 15 JUL 1700', '١٥ يوليو ١٧٠٠ م'],
            ['@#DJULIAN@ JUL 1700', 'يوليو ١٧٠٠ م'],
            ['ABT @#DJULIAN@ JUL 1700', 'حوالي يوليو ١٧٠٠ م'],
            ['FROM @#DJULIAN@ JUL 1700', 'من يوليو ١٧٠٠ م'],
            ['AFT @#DJULIAN@ JUL 1700', 'بعد يوليو ١٧٠٠ م'],
            ['BEF @#DJULIAN@ JUL 1700', 'قبل يوليو ١٧٠٠ م'],
            ['@#DJULIAN@ 15 AUG 1700', '١٥ أغسطس ١٧٠٠ م'],
            ['@#DJULIAN@ AUG 1700', 'أغسطس ١٧٠٠ م'],
            ['ABT @#DJULIAN@ AUG 1700', 'حوالي أغسطس ١٧٠٠ م'],
            ['FROM @#DJULIAN@ AUG 1700', 'من أغسطس ١٧٠٠ م'],
            ['AFT @#DJULIAN@ AUG 1700', 'بعد أغسطس ١٧٠٠ م'],
            ['BEF @#DJULIAN@ AUG 1700', 'قبل أغسطس ١٧٠٠ م'],
            ['@#DJULIAN@ 15 SEP 1700', '١٥ سبتمبر ١٧٠٠ م'],
            ['@#DJULIAN@ SEP 1700', 'سبتمبر ١٧٠٠ م'],
            ['ABT @#DJULIAN@ SEP 1700', 'حوالي سبتمبر ١٧٠٠ م'],
            ['FROM @#DJULIAN@ SEP 1700', 'من سبتمبر ١٧٠٠ م'],
            ['AFT @#DJULIAN@ SEP 1700', 'بعد سبتمبر ١٧٠٠ م'],
            ['BEF @#DJULIAN@ SEP 1700', 'قبل سبتمبر ١٧٠٠ م'],
            ['@#DJULIAN@ 15 OCT 1700', '١٥ أكتوبر ١٧٠٠ م'],
            ['@#DJULIAN@ OCT 1700', 'أكتوبر ١٧٠٠ م'],
            ['ABT @#DJULIAN@ OCT 1700', 'حوالي أكتوبر ١٧٠٠ م'],
            ['FROM @#DJULIAN@ OCT 1700', 'من أكتوبر ١٧٠٠ م'],
            ['AFT @#DJULIAN@ OCT 1700', 'بعد أكتوبر ١٧٠٠ م'],
            ['BEF @#DJULIAN@ OCT 1700', 'قبل أكتوبر ١٧٠٠ م'],
            ['@#DJULIAN@ 15 NOV 1700', '١٥ نوفمبر ١٧٠٠ م'],
            ['@#DJULIAN@ NOV 1700', 'نوفمبر ١٧٠٠ م'],
            ['ABT @#DJULIAN@ NOV 1700', 'حوالي نوفمبر ١٧٠٠ م'],
            ['FROM @#DJULIAN@ NOV 1700', 'من نوفمبر ١٧٠٠ م'],
            ['AFT @#DJULIAN@ NOV 1700', 'بعد نوفمبر ١٧٠٠ م'],
            ['BEF @#DJULIAN@ NOV 1700', 'قبل نوفمبر ١٧٠٠ م'],
            ['@#DJULIAN@ 15 DEC 1700', '١٥ ديسمبر ١٧٠٠ م'],
            ['@#DJULIAN@ DEC 1700', 'ديسمبر ١٧٠٠ م'],
            ['ABT @#DJULIAN@ DEC 1700', 'حوالي ديسمبر ١٧٠٠ م'],
            ['FROM @#DJULIAN@ DEC 1700', 'من ديسمبر ١٧٠٠ م'],
            ['AFT @#DJULIAN@ DEC 1700', 'بعد ديسمبر ١٧٠٠ م'],
            ['BEF @#DJULIAN@ DEC 1700', 'قبل ديسمبر ١٧٠٠ م'],
            ['@#DJULIAN@ 1700', '١٧٠٠ م'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'حوالي ١٥ يناير ١٧٠٠ م'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'حسب ١٥ يناير ١٧٠٠ م'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'تقديراً ١٥ يناير ١٧٠٠ م'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'قبل ١٥ يناير ١٧٠٠ م'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'بعد ١٥ يناير ١٧٠٠ م'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'من ١٥ يناير ١٧٠٠ م'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'إلى ١٥ يناير ١٧٠٠ م'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'بين ١٥ يناير ١٧٠٠ م و ١٥ فبراير ١٧٠٠ م'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'من ١٥ يناير ١٧٠٠ م إلى ١٥ فبراير ١٧٠٠ م'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'أعتبر ١٥ يناير ١٧٠٠ م'],
            ['@#DHEBREW@ 15 TSH 5765', '١٥ تشرين ٥٧٦٥'],
            ['@#DHEBREW@ TSH 5765', 'تشرين ٥٧٦٥'],
            ['ABT @#DHEBREW@ TSH 5765', 'حوالي تشرين ٥٧٦٥'],
            ['FROM @#DHEBREW@ TSH 5765', 'من تشرين ٥٧٦٥'],
            ['AFT @#DHEBREW@ TSH 5765', 'بعد تشرين ٥٧٦٥'],
            ['BEF @#DHEBREW@ TSH 5765', 'قبل تشرين ٥٧٦٥'],
            ['@#DHEBREW@ 15 CSH 5765', '١٥ حِشوان ٥٧٦٥'],
            ['@#DHEBREW@ CSH 5765', 'حِشوان ٥٧٦٥'],
            ['ABT @#DHEBREW@ CSH 5765', 'حوالي حِشوان ٥٧٦٥'],
            ['FROM @#DHEBREW@ CSH 5765', 'من حِشوان ٥٧٦٥'],
            ['AFT @#DHEBREW@ CSH 5765', 'بعد حِشوان ٥٧٦٥'],
            ['BEF @#DHEBREW@ CSH 5765', 'قبل حِشوان ٥٧٦٥'],
            ['@#DHEBREW@ 15 KSL 5765', '١٥ كِسلو ٥٧٦٥'],
            ['@#DHEBREW@ KSL 5765', 'كِسلو ٥٧٦٥'],
            ['ABT @#DHEBREW@ KSL 5765', 'حوالي كِسلو ٥٧٦٥'],
            ['FROM @#DHEBREW@ KSL 5765', 'من كِسلو ٥٧٦٥'],
            ['AFT @#DHEBREW@ KSL 5765', 'بعد كِسلو ٥٧٦٥'],
            ['BEF @#DHEBREW@ KSL 5765', 'قبل كِسلو ٥٧٦٥'],
            ['@#DHEBREW@ 15 TVT 5765', '١٥ طِيبيت ٥٧٦٥'],
            ['@#DHEBREW@ TVT 5765', 'طِيبيت ٥٧٦٥'],
            ['ABT @#DHEBREW@ TVT 5765', 'حوالي طِيبيت ٥٧٦٥'],
            ['FROM @#DHEBREW@ TVT 5765', 'من طِيبيت ٥٧٦٥'],
            ['AFT @#DHEBREW@ TVT 5765', 'بعد طِيبيت ٥٧٦٥'],
            ['BEF @#DHEBREW@ TVT 5765', 'قبل طِيبيت ٥٧٦٥'],
            ['@#DHEBREW@ 15 SHV 5765', '١٥ شباط ٥٧٦٥'],
            ['@#DHEBREW@ SHV 5765', 'شباط ٥٧٦٥'],
            ['ABT @#DHEBREW@ SHV 5765', 'حوالي شباط ٥٧٦٥'],
            ['FROM @#DHEBREW@ SHV 5765', 'من شباط ٥٧٦٥'],
            ['AFT @#DHEBREW@ SHV 5765', 'بعد شباط ٥٧٦٥'],
            ['BEF @#DHEBREW@ SHV 5765', 'قبل شباط ٥٧٦٥'],
            ['@#DHEBREW@ 15 ADR 5765', '١٥ أدار الأول ٥٧٦٥'],
            ['@#DHEBREW@ ADR 5765', 'أدار الأول ٥٧٦٥'],
            ['ABT @#DHEBREW@ ADR 5765', 'حوالي أدار الأول ٥٧٦٥'],
            ['FROM @#DHEBREW@ ADR 5765', 'من أدار الأول ٥٧٦٥'],
            ['AFT @#DHEBREW@ ADR 5765', 'بعد أدار الأول ٥٧٦٥'],
            ['BEF @#DHEBREW@ ADR 5765', 'قبل أدار الأول ٥٧٦٥'],
            ['@#DHEBREW@ 15 ADS 5765', '١٥ أدار الثاني ٥٧٦٥'],
            ['@#DHEBREW@ ADS 5765', 'أدار الثاني ٥٧٦٥'],
            ['ABT @#DHEBREW@ ADS 5765', 'حوالي أدار الثاني ٥٧٦٥'],
            ['FROM @#DHEBREW@ ADS 5765', 'من أدار الثاني ٥٧٦٥'],
            ['AFT @#DHEBREW@ ADS 5765', 'بعد أدار الثاني ٥٧٦٥'],
            ['BEF @#DHEBREW@ ADS 5765', 'قبل أدار الثاني ٥٧٦٥'],
            ['@#DHEBREW@ 15 NSN 5765', '١٥ نيسان ٥٧٦٥'],
            ['@#DHEBREW@ NSN 5765', 'نيسان ٥٧٦٥'],
            ['ABT @#DHEBREW@ NSN 5765', 'حوالي نيسان ٥٧٦٥'],
            ['FROM @#DHEBREW@ NSN 5765', 'من نيسان ٥٧٦٥'],
            ['AFT @#DHEBREW@ NSN 5765', 'بعد نيسان ٥٧٦٥'],
            ['BEF @#DHEBREW@ NSN 5765', 'قبل نيسان ٥٧٦٥'],
            ['@#DHEBREW@ 15 IYR 5765', '١٥ إيار ٥٧٦٥'],
            ['@#DHEBREW@ IYR 5765', 'إيار ٥٧٦٥'],
            ['ABT @#DHEBREW@ IYR 5765', 'حوالي إيار ٥٧٦٥'],
            ['FROM @#DHEBREW@ IYR 5765', 'من إيار ٥٧٦٥'],
            ['AFT @#DHEBREW@ IYR 5765', 'بعد إيار ٥٧٦٥'],
            ['BEF @#DHEBREW@ IYR 5765', 'قبل إيار ٥٧٦٥'],
            ['@#DHEBREW@ 15 SVN 5765', '١٥ سيوان ٥٧٦٥'],
            ['@#DHEBREW@ SVN 5765', 'سيوان ٥٧٦٥'],
            ['ABT @#DHEBREW@ SVN 5765', 'حوالي سيوان ٥٧٦٥'],
            ['FROM @#DHEBREW@ SVN 5765', 'من سيوان ٥٧٦٥'],
            ['AFT @#DHEBREW@ SVN 5765', 'بعد سيوان ٥٧٦٥'],
            ['BEF @#DHEBREW@ SVN 5765', 'قبل سيوان ٥٧٦٥'],
            ['@#DHEBREW@ 15 TMZ 5765', '١٥ تموز ٥٧٦٥'],
            ['@#DHEBREW@ TMZ 5765', 'تموز ٥٧٦٥'],
            ['ABT @#DHEBREW@ TMZ 5765', 'حوالي تموز ٥٧٦٥'],
            ['FROM @#DHEBREW@ TMZ 5765', 'من تموز ٥٧٦٥'],
            ['AFT @#DHEBREW@ TMZ 5765', 'بعد تموز ٥٧٦٥'],
            ['BEF @#DHEBREW@ TMZ 5765', 'قبل تموز ٥٧٦٥'],
            ['@#DHEBREW@ 15 AAV 5765', '١٥ آب ٥٧٦٥'],
            ['@#DHEBREW@ AAV 5765', 'آب ٥٧٦٥'],
            ['ABT @#DHEBREW@ AAV 5765', 'حوالي آب ٥٧٦٥'],
            ['FROM @#DHEBREW@ AAV 5765', 'من آب ٥٧٦٥'],
            ['AFT @#DHEBREW@ AAV 5765', 'بعد آب ٥٧٦٥'],
            ['BEF @#DHEBREW@ AAV 5765', 'قبل آب ٥٧٦٥'],
            ['@#DHEBREW@ 15 ELL 5765', '١٥ إيلول ٥٧٦٥'],
            ['@#DHEBREW@ ELL 5765', 'إيلول ٥٧٦٥'],
            ['ABT @#DHEBREW@ ELL 5765', 'حوالي إيلول ٥٧٦٥'],
            ['FROM @#DHEBREW@ ELL 5765', 'من إيلول ٥٧٦٥'],
            ['AFT @#DHEBREW@ ELL 5765', 'بعد إيلول ٥٧٦٥'],
            ['BEF @#DHEBREW@ ELL 5765', 'قبل إيلول ٥٧٦٥'],
            ['@#DHEBREW@ 5765', '٥٧٦٥'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'حوالي ١٥ تشرين ٥٧٦٥'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'حسب ١٥ تشرين ٥٧٦٥'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'تقديراً ١٥ تشرين ٥٧٦٥'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'قبل ١٥ تشرين ٥٧٦٥'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'بعد ١٥ تشرين ٥٧٦٥'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'من ١٥ تشرين ٥٧٦٥'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'إلى ١٥ تشرين ٥٧٦٥'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'بين ١٥ تشرين ٥٧٦٥ و ١٥ حِشوان ٥٧٦٥'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'من ١٥ تشرين ٥٧٦٥ إلى ١٥ حِشوان ٥٧٦٥'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'أعتبر ١٥ تشرين ٥٧٦٥'],
            ['@#DFRENCH R@ 15 VEND 12', '١٥ فاندميير An XII'],
            ['@#DFRENCH R@ VEND 12', 'فاندميير An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'حوالي فاندميير An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'من فاندميير An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'بعد فاندميير An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'قبل فاندميير An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '١٥ برومير An XII'],
            ['@#DFRENCH R@ BRUM 12', 'برومير An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'حوالي برومير An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'من برومير An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'بعد برومير An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'قبل برومير An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '١٥ فريمير An XII'],
            ['@#DFRENCH R@ FRIM 12', 'فريمير An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'حوالي فريمير An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'من فريمير An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'بعد فريمير An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'قبل فريمير An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '١٥ نيفوا An XII'],
            ['@#DFRENCH R@ NIVO 12', 'نيفوا An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'حوالي نيفوا An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'من نيفوا An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'بعد نيفوا An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'قبل نيفوا An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '١٥ بلوفوا An XII'],
            ['@#DFRENCH R@ PLUV 12', 'بلوفوا An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'حوالي بلوفوا An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'من بلوفوا An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'بعد بلوفوا An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'قبل بلوفوا An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '١٥ فينتوا An XII'],
            ['@#DFRENCH R@ VENT 12', 'فينتوا An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'حوالي فينتوا An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'من فينتوا An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'بعد فينتوا An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'قبل فينتوا An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '١٥ جيرمينال An XII'],
            ['@#DFRENCH R@ GERM 12', 'جيرمينال An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'حوالي جيرمينال An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'من جيرمينال An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'بعد جيرمينال An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'قبل جيرمينال An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '١٥ فلوريال An XII'],
            ['@#DFRENCH R@ FLOR 12', 'فلوريال An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'حوالي فلوريال An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'من فلوريال An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'بعد فلوريال An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'قبل فلوريال An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '١٥ براريال An XII'],
            ['@#DFRENCH R@ PRAI 12', 'براريال An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'حوالي براريال An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'من براريال An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'بعد براريال An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'قبل براريال An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '١٥ ميسيدور An XII'],
            ['@#DFRENCH R@ MESS 12', 'ميسيدور An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'حوالي ميسيدور An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'من ميسيدور An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'بعد ميسيدور An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'قبل ميسيدور An XII'],
            ['@#DFRENCH R@ 15 THER 12', '١٥ ثيرميدور An XII'],
            ['@#DFRENCH R@ THER 12', 'ثيرميدور An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'حوالي ثيرميدور An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'من ثيرميدور An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'بعد ثيرميدور An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'قبل ثيرميدور An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '١٥ فركتيدور An XII'],
            ['@#DFRENCH R@ FRUC 12', 'فركتيدور An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'حوالي فركتيدور An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'من فركتيدور An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'بعد فركتيدور An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'قبل فركتيدور An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '١٥ أيام مكملة An XII'],
            ['@#DFRENCH R@ COMP 12', 'أيام مكملة An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'حوالي أيام مكملة An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'من أيام مكملة An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'بعد أيام مكملة An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'قبل أيام مكملة An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'حوالي ١٥ فاندميير An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'حسب ١٥ فاندميير An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'تقديراً ١٥ فاندميير An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'قبل ١٥ فاندميير An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'بعد ١٥ فاندميير An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'من ١٥ فاندميير An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'إلى ١٥ فاندميير An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'بين ١٥ فاندميير An XII و ١٥ برومير An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'من ١٥ فاندميير An XII إلى ١٥ برومير An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'أعتبر ١٥ فاندميير An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '١٥ محرّم ١٤٢٥'],
            ['@#DHIJRI@ MUHAR 1425', 'محرّم ١٤٢٥'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'حوالي محرّم ١٤٢٥'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'من محرّم ١٤٢٥'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'بعد محرّم ١٤٢٥'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'قبل محرّم ١٤٢٥'],
            ['@#DHIJRI@ 15 SAFAR 1425', '١٥ صفر ١٤٢٥'],
            ['@#DHIJRI@ SAFAR 1425', 'صفر ١٤٢٥'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'حوالي صفر ١٤٢٥'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'من صفر ١٤٢٥'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'بعد صفر ١٤٢٥'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'قبل صفر ١٤٢٥'],
            ['@#DHIJRI@ 15 RABIA 1425', '١٥ ربيع الأول ١٤٢٥'],
            ['@#DHIJRI@ RABIA 1425', 'ربيع الأول ١٤٢٥'],
            ['ABT @#DHIJRI@ RABIA 1425', 'حوالي ربيع الأول ١٤٢٥'],
            ['FROM @#DHIJRI@ RABIA 1425', 'من ربيع الأول ١٤٢٥'],
            ['AFT @#DHIJRI@ RABIA 1425', 'بعد ربيع الأول ١٤٢٥'],
            ['BEF @#DHIJRI@ RABIA 1425', 'قبل ربيع الأول ١٤٢٥'],
            ['@#DHIJRI@ 15 RABIT 1425', '١٥ ربيع الثاني ١٤٢٥'],
            ['@#DHIJRI@ RABIT 1425', 'ربيع الثاني ١٤٢٥'],
            ['ABT @#DHIJRI@ RABIT 1425', 'حوالي ربيع الثاني ١٤٢٥'],
            ['FROM @#DHIJRI@ RABIT 1425', 'من ربيع الثاني ١٤٢٥'],
            ['AFT @#DHIJRI@ RABIT 1425', 'بعد ربيع الثاني ١٤٢٥'],
            ['BEF @#DHIJRI@ RABIT 1425', 'قبل ربيع الثاني ١٤٢٥'],
            ['@#DHIJRI@ 15 JUMAA 1425', '١٥ جمادى الأول ١٤٢٥'],
            ['@#DHIJRI@ JUMAA 1425', 'جمادى الأول ١٤٢٥'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'حوالي جمادى الأول ١٤٢٥'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'من جمادى الأول ١٤٢٥'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'بعد جمادى الأول ١٤٢٥'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'قبل جمادى الأول ١٤٢٥'],
            ['@#DHIJRI@ 15 JUMAT 1425', '١٥ جمادى الثاني ١٤٢٥'],
            ['@#DHIJRI@ JUMAT 1425', 'جمادى الثاني ١٤٢٥'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'حوالي جمادى الثاني ١٤٢٥'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'من جمادى الثاني ١٤٢٥'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'بعد جمادى الثاني ١٤٢٥'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'قبل جمادى الثاني ١٤٢٥'],
            ['@#DHIJRI@ 15 RAJAB 1425', '١٥ رجب ١٤٢٥'],
            ['@#DHIJRI@ RAJAB 1425', 'رجب ١٤٢٥'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'حوالي رجب ١٤٢٥'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'من رجب ١٤٢٥'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'بعد رجب ١٤٢٥'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'قبل رجب ١٤٢٥'],
            ['@#DHIJRI@ 15 SHAAB 1425', '١٥ شعبان ١٤٢٥'],
            ['@#DHIJRI@ SHAAB 1425', 'شعبان ١٤٢٥'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'حوالي شعبان ١٤٢٥'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'من شعبان ١٤٢٥'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'بعد شعبان ١٤٢٥'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'قبل شعبان ١٤٢٥'],
            ['@#DHIJRI@ 15 RAMAD 1425', '١٥ رمضان ١٤٢٥'],
            ['@#DHIJRI@ RAMAD 1425', 'رمضان ١٤٢٥'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'حوالي رمضان ١٤٢٥'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'من رمضان ١٤٢٥'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'بعد رمضان ١٤٢٥'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'قبل رمضان ١٤٢٥'],
            ['@#DHIJRI@ 15 SHAWW 1425', '١٥ شوّال ١٤٢٥'],
            ['@#DHIJRI@ SHAWW 1425', 'شوّال ١٤٢٥'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'حوالي شوّال ١٤٢٥'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'من شوّال ١٤٢٥'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'بعد شوّال ١٤٢٥'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'قبل شوّال ١٤٢٥'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '١٥ ذو القعدة ١٤٢٥'],
            ['@#DHIJRI@ DHUAQ 1425', 'ذو القعدة ١٤٢٥'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'حوالي ذو القعدة ١٤٢٥'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'من ذو القعدة ١٤٢٥'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'بعد ذو القعدة ١٤٢٥'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'قبل ذو القعدة ١٤٢٥'],
            ['@#DHIJRI@ 15 DHUAL 1425', '١٤٢٥'],
            ['@#DHIJRI@ DHUAL 1425', '١٤٢٥'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'حوالي ١٤٢٥'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'من ١٤٢٥'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'بعد ١٤٢٥'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'قبل ١٤٢٥'],
            ['@#DHIJRI@ 1425', '١٤٢٥'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'حوالي ١٥ محرّم ١٤٢٥'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'حسب ١٥ محرّم ١٤٢٥'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'تقديراً ١٥ محرّم ١٤٢٥'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'قبل ١٥ محرّم ١٤٢٥'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'بعد ١٥ محرّم ١٤٢٥'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'من ١٥ محرّم ١٤٢٥'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'إلى ١٥ محرّم ١٤٢٥'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'بين ١٥ محرّم ١٤٢٥ و ١٥ صفر ١٤٢٥'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'من ١٥ محرّم ١٤٢٥ إلى ١٥ صفر ١٤٢٥'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'أعتبر ١٥ محرّم ١٤٢٥'],
            ['@#DJALALI@ 15 FARVA 1384', '١٥ فروردين ١٣٨٤'],
            ['@#DJALALI@ FARVA 1384', 'فروردين ١٣٨٤'],
            ['ABT @#DJALALI@ FARVA 1384', 'حوالي فروردين ١٣٨٤'],
            ['FROM @#DJALALI@ FARVA 1384', 'من فروردين ١٣٨٤'],
            ['AFT @#DJALALI@ FARVA 1384', 'بعد فروردين ١٣٨٤'],
            ['BEF @#DJALALI@ FARVA 1384', 'قبل فروردين ١٣٨٤'],
            ['@#DJALALI@ 15 ORDIB 1384', '١٥ ارديبهشت ١٣٨٤'],
            ['@#DJALALI@ ORDIB 1384', 'ارديبهشت ١٣٨٤'],
            ['ABT @#DJALALI@ ORDIB 1384', 'حوالي ارديبهشت ١٣٨٤'],
            ['FROM @#DJALALI@ ORDIB 1384', 'من ارديبهشت ١٣٨٤'],
            ['AFT @#DJALALI@ ORDIB 1384', 'بعد ارديبهشت ١٣٨٤'],
            ['BEF @#DJALALI@ ORDIB 1384', 'قبل ارديبهشت ١٣٨٤'],
            ['@#DJALALI@ 15 KHORD 1384', '١٥ خُرداد ١٣٨٤'],
            ['@#DJALALI@ KHORD 1384', 'خُرداد ١٣٨٤'],
            ['ABT @#DJALALI@ KHORD 1384', 'حوالي خُرداد ١٣٨٤'],
            ['FROM @#DJALALI@ KHORD 1384', 'من خُرداد ١٣٨٤'],
            ['AFT @#DJALALI@ KHORD 1384', 'بعد خُرداد ١٣٨٤'],
            ['BEF @#DJALALI@ KHORD 1384', 'قبل خُرداد ١٣٨٤'],
            ['@#DJALALI@ 15 TIR 1384', '١٥ تير ١٣٨٤'],
            ['@#DJALALI@ TIR 1384', 'تير ١٣٨٤'],
            ['ABT @#DJALALI@ TIR 1384', 'حوالي تير ١٣٨٤'],
            ['FROM @#DJALALI@ TIR 1384', 'من تير ١٣٨٤'],
            ['AFT @#DJALALI@ TIR 1384', 'بعد تير ١٣٨٤'],
            ['BEF @#DJALALI@ TIR 1384', 'قبل تير ١٣٨٤'],
            ['@#DJALALI@ 15 MORDA 1384', '١٥ مُرداد ١٣٨٤'],
            ['@#DJALALI@ MORDA 1384', 'مُرداد ١٣٨٤'],
            ['ABT @#DJALALI@ MORDA 1384', 'حوالي مُرداد ١٣٨٤'],
            ['FROM @#DJALALI@ MORDA 1384', 'من مُرداد ١٣٨٤'],
            ['AFT @#DJALALI@ MORDA 1384', 'بعد مُرداد ١٣٨٤'],
            ['BEF @#DJALALI@ MORDA 1384', 'قبل مُرداد ١٣٨٤'],
            ['@#DJALALI@ 15 SHAHR 1384', '١٥ شهريور ١٣٨٤'],
            ['@#DJALALI@ SHAHR 1384', 'شهريور ١٣٨٤'],
            ['ABT @#DJALALI@ SHAHR 1384', 'حوالي شهريور ١٣٨٤'],
            ['FROM @#DJALALI@ SHAHR 1384', 'من شهريور ١٣٨٤'],
            ['AFT @#DJALALI@ SHAHR 1384', 'بعد شهريور ١٣٨٤'],
            ['BEF @#DJALALI@ SHAHR 1384', 'قبل شهريور ١٣٨٤'],
            ['@#DJALALI@ 15 MEHR 1384', '١٥ مِهر ١٣٨٤'],
            ['@#DJALALI@ MEHR 1384', 'مِهر ١٣٨٤'],
            ['ABT @#DJALALI@ MEHR 1384', 'حوالي مِهر ١٣٨٤'],
            ['FROM @#DJALALI@ MEHR 1384', 'من مِهر ١٣٨٤'],
            ['AFT @#DJALALI@ MEHR 1384', 'بعد مِهر ١٣٨٤'],
            ['BEF @#DJALALI@ MEHR 1384', 'قبل مِهر ١٣٨٤'],
            ['@#DJALALI@ 15 ABAN 1384', '١٥ آبان ١٣٨٤'],
            ['@#DJALALI@ ABAN 1384', 'آبان ١٣٨٤'],
            ['ABT @#DJALALI@ ABAN 1384', 'حوالي آبان ١٣٨٤'],
            ['FROM @#DJALALI@ ABAN 1384', 'من آبان ١٣٨٤'],
            ['AFT @#DJALALI@ ABAN 1384', 'بعد آبان ١٣٨٤'],
            ['BEF @#DJALALI@ ABAN 1384', 'قبل آبان ١٣٨٤'],
            ['@#DJALALI@ 15 AZAR 1384', '١٥ آذر ١٣٨٤'],
            ['@#DJALALI@ AZAR 1384', 'آذر ١٣٨٤'],
            ['ABT @#DJALALI@ AZAR 1384', 'حوالي آذر ١٣٨٤'],
            ['FROM @#DJALALI@ AZAR 1384', 'من آذر ١٣٨٤'],
            ['AFT @#DJALALI@ AZAR 1384', 'بعد آذر ١٣٨٤'],
            ['BEF @#DJALALI@ AZAR 1384', 'قبل آذر ١٣٨٤'],
            ['@#DJALALI@ 15 DEY 1384', '١٥ دى ١٣٨٤'],
            ['@#DJALALI@ DEY 1384', 'دى ١٣٨٤'],
            ['ABT @#DJALALI@ DEY 1384', 'حوالي دى ١٣٨٤'],
            ['FROM @#DJALALI@ DEY 1384', 'من دى ١٣٨٤'],
            ['AFT @#DJALALI@ DEY 1384', 'بعد دى ١٣٨٤'],
            ['BEF @#DJALALI@ DEY 1384', 'قبل دى ١٣٨٤'],
            ['@#DJALALI@ 15 BAHMA 1384', '١٥ بهمن ١٣٨٤'],
            ['@#DJALALI@ BAHMA 1384', 'بهمن ١٣٨٤'],
            ['ABT @#DJALALI@ BAHMA 1384', 'حوالي بهمن ١٣٨٤'],
            ['FROM @#DJALALI@ BAHMA 1384', 'من بهمن ١٣٨٤'],
            ['AFT @#DJALALI@ BAHMA 1384', 'بعد بهمن ١٣٨٤'],
            ['BEF @#DJALALI@ BAHMA 1384', 'قبل بهمن ١٣٨٤'],
            ['@#DJALALI@ 15 ESFAN 1384', '١٥ إسفند ١٣٨٤'],
            ['@#DJALALI@ ESFAN 1384', 'إسفند ١٣٨٤'],
            ['ABT @#DJALALI@ ESFAN 1384', 'حوالي إسفند ١٣٨٤'],
            ['FROM @#DJALALI@ ESFAN 1384', 'من إسفند ١٣٨٤'],
            ['AFT @#DJALALI@ ESFAN 1384', 'بعد إسفند ١٣٨٤'],
            ['BEF @#DJALALI@ ESFAN 1384', 'قبل إسفند ١٣٨٤'],
            ['@#DJALALI@ 1384', '١٣٨٤'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'حوالي ١٥ فروردين ١٣٨٤'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'حسب ١٥ فروردين ١٣٨٤'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'تقديراً ١٥ فروردين ١٣٨٤'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'قبل ١٥ فروردين ١٣٨٤'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'بعد ١٥ فروردين ١٣٨٤'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'من ١٥ فروردين ١٣٨٤'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'إلى ١٥ فروردين ١٣٨٤'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'بين ١٥ فروردين ١٣٨٤ و ١٥ ارديبهشت ١٣٨٤'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'من ١٥ فروردين ١٣٨٤ إلى ١٥ ارديبهشت ١٣٨٤'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'أعتبر ١٥ فروردين ١٣٨٤'],
        ];
    }

    public function testFormatList(): void
    {
        $language = static::language();

        self::assertSame('', $language->formatList([]));
        self::assertSame('one', $language->formatList(['one']));
        self::assertSame('one، two', $language->formatList(['one', 'two']));
        self::assertSame('one، two، three', $language->formatList(['one', 'two', 'three']));

        self::assertSame('', $language->formatListAnd([]));
        self::assertSame('one', $language->formatListAnd(['one']));
        self::assertSame('one وtwo', $language->formatListAnd(['one', 'two']));
        self::assertSame('one، two وthree', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one أوtwo', $language->formatListOr(['one', 'two']));
        self::assertSame('one، two أوthree', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $child = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $fosterDaughter = self::female('fsd', "1 FAMC @fd@\n2 PEDI foster");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");

        // Husband's family (paternal side of son)
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMS @fbro@\n1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        // Wife's family (maternal side of son)
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

        // Cousins — paternal uncle's children
        $cousinMPU = self::male('cmpu', "1 FAMC @fbro@");
        $cousinFPU = self::female('cfpu', "1 FAMC @fbro@");
        // Cousins — paternal aunt's children
        $cousinMPA = self::male('cmpa', "1 FAMC @fsis@");
        $cousinFPA = self::female('cfpa', "1 FAMC @fsis@");
        // Cousins — maternal uncle's children
        $cousinMMU = self::male('cmmu', "1 FAMC @fmbro@");
        $cousinFMU = self::female('cfmu', "1 FAMC @fmbro@");
        // Cousins — maternal aunt's children
        $cousinMMA = self::male('cmma', "1 FAMC @fmsis@");
        $cousinFMA = self::female('cfma', "1 FAMC @fmsis@");

        // Maternal uncle and aunt of wife (for cousin tests via son's mother)
        $maternalUncle = self::male('mu', "1 FAMS @fmbro@\n1 FAMC @fw@");
        $maternalAunt = self::female('ma', "1 FAMS @fmsis@\n1 FAMC @fw@");

        // Grandparents (husband's parents' parents)
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");

        // Engaged
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        // Husband's half-siblings (through father only)
        $halfBroPaternal = self::male('hbp', "1 FAMC @fhalf@");
        $fatherOfHFamily2 = self::male('fh', "1 FAMS @fp@\n1 FAMS @fhalf@");

        // Families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fsd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@\n1 CHIL @mu@\n1 CHIL @ma@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cmpu@\n1 CHIL @cfpu@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cmpa@\n1 CHIL @cfpa@");
        $fmbro = self::family('fmbro', "0 @fmbro@ FAM\n1 HUSB @mu@\n1 CHIL @cmmu@\n1 CHIL @cfmu@");
        $fmsis = self::family('fmsis', "0 @fmsis@ FAM\n1 WIFE @ma@\n1 CHIL @cmma@\n1 CHIL @cfma@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinMPU, $cousinFPU, $cousinMPA, $cousinFPA,
             $cousinMMU, $cousinFMU, $cousinMMA, $cousinFMA,
             $maternalUncle, $maternalAunt,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fmbro, $fmsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('زوجة', 'زوج', [$husband, $fm, $wife]);
        self::assertRelationshipNames('مطلّق', 'مطلّقة', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('خطيبة', 'خطيب', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('أم', 'ابن', [$son, $fm, $wife]);
        self::assertRelationshipNames('أب', 'ابن', [$son, $fm, $husband]);
        self::assertRelationshipNames('أم', 'ابنة', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('أم بالتبني', 'ابن بالتبني', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('أب بالتبني', 'ابن بالتبني', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('أم حاضنة', 'ابنة بالحضانة', [$fosterDaughter, $fd, $wife]);

        // Siblings (older/younger)
        self::assertRelationshipNames('أخت صغرى', 'أخ أكبر', [$son, $fm, $daughter]);
        self::assertRelationshipNames('أخ أكبر', 'أخت صغرى', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('زوج الأم', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('ربيبة', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws (spouse's parents)
        self::assertRelationshipNames('حماة', 'صهر', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('حمو', 'صهر', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // In-laws (child's spouse)
        self::assertRelationshipName('كنّة', [$fatherOfH, $fp, $husband, $fm, $wife]);
        self::assertRelationshipName('كنّة', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('صهر', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws (spouse's siblings) — from husband's perspective
        self::assertRelationshipName('سلف', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('سلفة', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents
        self::assertRelationshipNames('الجدة', 'حفيد', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('الجد', 'حفيد', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('حفيدة', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts — paternal
        self::assertRelationshipName('عمة', [$son, $fm, $husband, $fp, $sisterOfH]);
        // Aunts — maternal
        self::assertRelationshipName('خالة', [$son, $fm, $wife, $fw, $sisterOfW]);
        // Uncles — paternal
        self::assertRelationshipName('عم', [$son, $fm, $husband, $fp, $brotherOfH]);
        // Uncles — maternal
        self::assertRelationshipName('خال', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces/nephews — through brother
        self::assertRelationshipName('بنت الأخ', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('ابن الأخ', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        // Nieces/nephews — through sister
        self::assertRelationshipName('بنت الأخت', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('ابن الأخت', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — paternal uncle's children (ابن العم / بنت العم)
        self::assertRelationshipName('ابن العم', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMPU]);
        self::assertRelationshipName('بنت العم', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFPU]);
        // Cousins — paternal aunt's children (ابن العمة / بنت العمة)
        self::assertRelationshipName('ابن العمة', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinMPA]);
        self::assertRelationshipName('بنت العمة', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinFPA]);
        // Cousins — maternal uncle's children (ابن الخال / بنت الخال)
        self::assertRelationshipName('ابن الخال', [$son, $fm, $wife, $fw, $maternalUncle, $fmbro, $cousinMMU]);
        self::assertRelationshipName('بنت الخال', [$son, $fm, $wife, $fw, $maternalUncle, $fmbro, $cousinFMU]);
        // Cousins — maternal aunt's children (ابن الخالة / بنت الخالة)
        self::assertRelationshipName('ابن الخالة', [$son, $fm, $wife, $fw, $maternalAunt, $fmsis, $cousinMMA]);
        self::assertRelationshipName('بنت الخالة', [$son, $fm, $wife, $fw, $maternalAunt, $fmsis, $cousinFMA]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('الجدة الكبرى', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('الجد الأكبر', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('عمة/خالة كبرى', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('عم/خال أكبر', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
