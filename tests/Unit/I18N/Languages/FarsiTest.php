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
use Fisharebest\Webtrees\I18N\Languages\Farsi;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Farsi::class)]
class FarsiTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Farsi();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Arab, self::language()->script());
    }

    public function testFirstDay(): void
    {
        self::assertSame(Weekday::Saturday, self::language()->firstDay());
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
        self::assertSame('fa', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('فارسی', self::language()->endonym());
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
        self::assertSame('-۱۲۳,۴۵۶.۰۷۸۹', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('‎−۱۲۳٬۴۵۶٫۰۷۸۹', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('‎−۱۲۳٬۴۵۶٫۰۷۸۹٪', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '۱۵ ژانویه ۲۰۰۰'],
            ['JAN 2000', 'ژانویه ۲۰۰۰'],
            ['ABT JAN 2000', 'درباره ژانویه ۲۰۰۰'],
            ['FROM JAN 2000', 'از ژانویه ۲۰۰۰'],
            ['AFT JAN 2000', 'بعد ژانویه ۲۰۰۰'],
            ['BEF JAN 2000', 'قبل ژانویه ۲۰۰۰'],
            ['15 FEB 2000', '۱۵ فوریه ۲۰۰۰'],
            ['FEB 2000', 'فوریه ۲۰۰۰'],
            ['ABT FEB 2000', 'درباره فوریه ۲۰۰۰'],
            ['FROM FEB 2000', 'از فوریه ۲۰۰۰'],
            ['AFT FEB 2000', 'بعد فوریه ۲۰۰۰'],
            ['BEF FEB 2000', 'قبل فوریه ۲۰۰۰'],
            ['15 MAR 2000', '۱۵ مارس ۲۰۰۰'],
            ['MAR 2000', 'مارس ۲۰۰۰'],
            ['ABT MAR 2000', 'درباره مارس ۲۰۰۰'],
            ['FROM MAR 2000', 'از مارس ۲۰۰۰'],
            ['AFT MAR 2000', 'بعد مارس ۲۰۰۰'],
            ['BEF MAR 2000', 'قبل مارس ۲۰۰۰'],
            ['15 APR 2000', '۱۵ آوریل ۲۰۰۰'],
            ['APR 2000', 'آوریل ۲۰۰۰'],
            ['ABT APR 2000', 'درباره آوریل ۲۰۰۰'],
            ['FROM APR 2000', 'از آوریل ۲۰۰۰'],
            ['AFT APR 2000', 'بعد آوریل ۲۰۰۰'],
            ['BEF APR 2000', 'قبل آوریل ۲۰۰۰'],
            ['15 MAY 2000', '۱۵ می ۲۰۰۰'],
            ['MAY 2000', 'می ۲۰۰۰'],
            ['ABT MAY 2000', 'درباره می ۲۰۰۰'],
            ['FROM MAY 2000', 'از می ۲۰۰۰'],
            ['AFT MAY 2000', 'بعد می ۲۰۰۰'],
            ['BEF MAY 2000', 'قبل می ۲۰۰۰'],
            ['15 JUN 2000', '۱۵ ژوئن ۲۰۰۰'],
            ['JUN 2000', 'ژوئن ۲۰۰۰'],
            ['ABT JUN 2000', 'درباره ژوئن ۲۰۰۰'],
            ['FROM JUN 2000', 'از ژوئن ۲۰۰۰'],
            ['AFT JUN 2000', 'بعد ژوئن ۲۰۰۰'],
            ['BEF JUN 2000', 'قبل ژوئن ۲۰۰۰'],
            ['15 JUL 2000', '۱۵ جولای ۲۰۰۰'],
            ['JUL 2000', 'جولای ۲۰۰۰'],
            ['ABT JUL 2000', 'درباره جولای ۲۰۰۰'],
            ['FROM JUL 2000', 'از جولای ۲۰۰۰'],
            ['AFT JUL 2000', 'بعد جولای ۲۰۰۰'],
            ['BEF JUL 2000', 'قبل جولای ۲۰۰۰'],
            ['15 AUG 2000', '۱۵ آگوست ۲۰۰۰'],
            ['AUG 2000', 'آگوست ۲۰۰۰'],
            ['ABT AUG 2000', 'درباره آگوست ۲۰۰۰'],
            ['FROM AUG 2000', 'از آگوست ۲۰۰۰'],
            ['AFT AUG 2000', 'بعد آگوست ۲۰۰۰'],
            ['BEF AUG 2000', 'قبل آگوست ۲۰۰۰'],
            ['15 SEP 2000', '۱۵ سپتامبر ۲۰۰۰'],
            ['SEP 2000', 'سپتامبر ۲۰۰۰'],
            ['ABT SEP 2000', 'درباره سپتامبر ۲۰۰۰'],
            ['FROM SEP 2000', 'از سپتامبر ۲۰۰۰'],
            ['AFT SEP 2000', 'بعد سپتامبر ۲۰۰۰'],
            ['BEF SEP 2000', 'قبل سپتامبر ۲۰۰۰'],
            ['15 OCT 2000', '۱۵ اکتبر ۲۰۰۰'],
            ['OCT 2000', 'اکتبر ۲۰۰۰'],
            ['ABT OCT 2000', 'درباره اکتبر ۲۰۰۰'],
            ['FROM OCT 2000', 'از اکتبر ۲۰۰۰'],
            ['AFT OCT 2000', 'بعد اکتبر ۲۰۰۰'],
            ['BEF OCT 2000', 'قبل اکتبر ۲۰۰۰'],
            ['15 NOV 2000', '۱۵ نوامبر ۲۰۰۰'],
            ['NOV 2000', 'نوامبر ۲۰۰۰'],
            ['ABT NOV 2000', 'درباره نوامبر ۲۰۰۰'],
            ['FROM NOV 2000', 'از نوامبر ۲۰۰۰'],
            ['AFT NOV 2000', 'بعد نوامبر ۲۰۰۰'],
            ['BEF NOV 2000', 'قبل نوامبر ۲۰۰۰'],
            ['15 DEC 2000', '۱۵ دسامبر ۲۰۰۰'],
            ['DEC 2000', 'دسامبر ۲۰۰۰'],
            ['ABT DEC 2000', 'درباره دسامبر ۲۰۰۰'],
            ['FROM DEC 2000', 'از دسامبر ۲۰۰۰'],
            ['AFT DEC 2000', 'بعد دسامبر ۲۰۰۰'],
            ['BEF DEC 2000', 'قبل دسامبر ۲۰۰۰'],
            ['2000', '۲۰۰۰'],
            ['ABT 15 JAN 2000', 'درباره ۱۵ ژانویه ۲۰۰۰'],
            ['CAL 15 JAN 2000', 'محاسبه شده ۱۵ ژانویه ۲۰۰۰'],
            ['EST 15 JAN 2000', 'برآورد شده ۱۵ ژانویه ۲۰۰۰'],
            ['BEF 15 JAN 2000', 'قبل ۱۵ ژانویه ۲۰۰۰'],
            ['AFT 15 JAN 2000', 'بعد ۱۵ ژانویه ۲۰۰۰'],
            ['FROM 15 JAN 2000', 'از ۱۵ ژانویه ۲۰۰۰'],
            ['TO 15 JAN 2000', 'به ۱۵ ژانویه ۲۰۰۰'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'بین ۱۵ ژانویه ۲۰۰۰ و ۱۵ فوریه ۲۰۰۰'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'از ۱۵ ژانویه ۲۰۰۰ تا ۱۵ فوریه ۲۰۰۰'],
            ['INT 15 JAN 2000', 'تعریف شده ۱۵ ژانویه ۲۰۰۰'],
            ['@#DJULIAN@ 15 JAN 1700', '۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ JAN 1700', 'ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ JAN 1700', 'درباره ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ JAN 1700', 'از ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ JAN 1700', 'بعد ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ JAN 1700', 'قبل ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 FEB 1700', '۱۵ فوریه ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ FEB 1700', 'فوریه ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ FEB 1700', 'درباره فوریه ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ FEB 1700', 'از فوریه ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ FEB 1700', 'بعد فوریه ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ FEB 1700', 'قبل فوریه ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 MAR 1700', '۱۵ مارس ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ MAR 1700', 'مارس ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ MAR 1700', 'درباره مارس ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ MAR 1700', 'از مارس ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ MAR 1700', 'بعد مارس ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ MAR 1700', 'قبل مارس ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 APR 1700', '۱۵ آوریل ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 14 APR 1645/46', '۱۴ آوریل ۱۶۴۵/۴۶ پس ازمیلاد'],
            ['@#DJULIAN@ APR 1700', 'آوریل ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ APR 1700', 'درباره آوریل ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ APR 1700', 'از آوریل ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ APR 1700', 'بعد آوریل ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ APR 1700', 'قبل آوریل ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 MAY 1700', '۱۵ می ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ MAY 1700', 'می ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ MAY 1700', 'درباره می ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ MAY 1700', 'از می ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ MAY 1700', 'بعد می ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ MAY 1700', 'قبل می ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 JUN 1700', '۱۵ ژوئن ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ JUN 1700', 'ژوئن ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ JUN 1700', 'درباره ژوئن ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ JUN 1700', 'از ژوئن ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ JUN 1700', 'بعد ژوئن ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ JUN 1700', 'قبل ژوئن ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 JUL 1700', '۱۵ جولای ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ JUL 1700', 'جولای ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ JUL 1700', 'درباره جولای ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ JUL 1700', 'از جولای ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ JUL 1700', 'بعد جولای ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ JUL 1700', 'قبل جولای ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 AUG 1700', '۱۵ آگوست ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ AUG 1700', 'آگوست ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ AUG 1700', 'درباره آگوست ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ AUG 1700', 'از آگوست ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ AUG 1700', 'بعد آگوست ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ AUG 1700', 'قبل آگوست ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 SEP 1700', '۱۵ سپتامبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ SEP 1700', 'سپتامبر ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ SEP 1700', 'درباره سپتامبر ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ SEP 1700', 'از سپتامبر ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ SEP 1700', 'بعد سپتامبر ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ SEP 1700', 'قبل سپتامبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 OCT 1700', '۱۵ اکتبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ OCT 1700', 'اکتبر ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ OCT 1700', 'درباره اکتبر ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ OCT 1700', 'از اکتبر ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ OCT 1700', 'بعد اکتبر ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ OCT 1700', 'قبل اکتبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 NOV 1700', '۱۵ نوامبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ NOV 1700', 'نوامبر ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ NOV 1700', 'درباره نوامبر ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ NOV 1700', 'از نوامبر ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ NOV 1700', 'بعد نوامبر ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ NOV 1700', 'قبل نوامبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 15 DEC 1700', '۱۵ دسامبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ DEC 1700', 'دسامبر ۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ DEC 1700', 'درباره دسامبر ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ DEC 1700', 'از دسامبر ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ DEC 1700', 'بعد دسامبر ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ DEC 1700', 'قبل دسامبر ۱۷۰۰ پس ازمیلاد'],
            ['@#DJULIAN@ 1700', '۱۷۰۰ پس ازمیلاد'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'درباره ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'محاسبه شده ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'برآورد شده ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'قبل ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'بعد ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'از ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'به ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'بین ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد و ۱۵ فوریه ۱۷۰۰ پس ازمیلاد'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'از ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد تا ۱۵ فوریه ۱۷۰۰ پس ازمیلاد'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'تعریف شده ۱۵ ژانویه ۱۷۰۰ پس ازمیلاد'],
            ['@#DHEBREW@ 15 TSH 5765', '۱۵ تیشری ۵۷۶۵'],
            ['@#DHEBREW@ TSH 5765', 'تیشری ۵۷۶۵'],
            ['ABT @#DHEBREW@ TSH 5765', 'درباره تیشری ۵۷۶۵'],
            ['FROM @#DHEBREW@ TSH 5765', 'از تیشری ۵۷۶۵'],
            ['AFT @#DHEBREW@ TSH 5765', 'بعد تیشری ۵۷۶۵'],
            ['BEF @#DHEBREW@ TSH 5765', 'قبل تیشری ۵۷۶۵'],
            ['@#DHEBREW@ 15 CSH 5765', '۱۵ هشوان ۵۷۶۵'],
            ['@#DHEBREW@ CSH 5765', 'هشوان ۵۷۶۵'],
            ['ABT @#DHEBREW@ CSH 5765', 'درباره هشوان ۵۷۶۵'],
            ['FROM @#DHEBREW@ CSH 5765', 'از هشوان ۵۷۶۵'],
            ['AFT @#DHEBREW@ CSH 5765', 'بعد هشوان ۵۷۶۵'],
            ['BEF @#DHEBREW@ CSH 5765', 'قبل هشوان ۵۷۶۵'],
            ['@#DHEBREW@ 15 KSL 5765', '۱۵ کیسلِو ۵۷۶۵'],
            ['@#DHEBREW@ KSL 5765', 'کیسلِو ۵۷۶۵'],
            ['ABT @#DHEBREW@ KSL 5765', 'درباره کیسلِو ۵۷۶۵'],
            ['FROM @#DHEBREW@ KSL 5765', 'از کیسلِو ۵۷۶۵'],
            ['AFT @#DHEBREW@ KSL 5765', 'بعد کیسلِو ۵۷۶۵'],
            ['BEF @#DHEBREW@ KSL 5765', 'قبل کیسلِو ۵۷۶۵'],
            ['@#DHEBREW@ 15 TVT 5765', '۱۵ تِوِت ۵۷۶۵'],
            ['@#DHEBREW@ TVT 5765', 'تِوِت ۵۷۶۵'],
            ['ABT @#DHEBREW@ TVT 5765', 'درباره تِوِت ۵۷۶۵'],
            ['FROM @#DHEBREW@ TVT 5765', 'از تِوِت ۵۷۶۵'],
            ['AFT @#DHEBREW@ TVT 5765', 'بعد تِوِت ۵۷۶۵'],
            ['BEF @#DHEBREW@ TVT 5765', 'قبل تِوِت ۵۷۶۵'],
            ['@#DHEBREW@ 15 SHV 5765', '۱۵ شوات ۵۷۶۵'],
            ['@#DHEBREW@ SHV 5765', 'شوات ۵۷۶۵'],
            ['ABT @#DHEBREW@ SHV 5765', 'درباره شوات ۵۷۶۵'],
            ['FROM @#DHEBREW@ SHV 5765', 'از شوات ۵۷۶۵'],
            ['AFT @#DHEBREW@ SHV 5765', 'بعد شوات ۵۷۶۵'],
            ['BEF @#DHEBREW@ SHV 5765', 'قبل شوات ۵۷۶۵'],
            ['@#DHEBREW@ 15 ADR 5765', '۱۵ ادار ۱ ۵۷۶۵'],
            ['@#DHEBREW@ ADR 5765', 'ادار ۱ ۵۷۶۵'],
            ['ABT @#DHEBREW@ ADR 5765', 'درباره ادار ۱ ۵۷۶۵'],
            ['FROM @#DHEBREW@ ADR 5765', 'از ادار ۱ ۵۷۶۵'],
            ['AFT @#DHEBREW@ ADR 5765', 'بعد ادار ۱ ۵۷۶۵'],
            ['BEF @#DHEBREW@ ADR 5765', 'قبل ادار ۱ ۵۷۶۵'],
            ['@#DHEBREW@ 15 ADS 5765', '۱۵ ادار ۲ ۵۷۶۵'],
            ['@#DHEBREW@ ADS 5765', 'ادار ۲ ۵۷۶۵'],
            ['ABT @#DHEBREW@ ADS 5765', 'درباره ادار ۲ ۵۷۶۵'],
            ['FROM @#DHEBREW@ ADS 5765', 'از ادار ۲ ۵۷۶۵'],
            ['AFT @#DHEBREW@ ADS 5765', 'بعد ادار ۲ ۵۷۶۵'],
            ['BEF @#DHEBREW@ ADS 5765', 'قبل ادار ۲ ۵۷۶۵'],
            ['@#DHEBREW@ 15 NSN 5765', '۱۵ نیسان ۵۷۶۵'],
            ['@#DHEBREW@ NSN 5765', 'نیسان ۵۷۶۵'],
            ['ABT @#DHEBREW@ NSN 5765', 'درباره نیسان ۵۷۶۵'],
            ['FROM @#DHEBREW@ NSN 5765', 'از نیسان ۵۷۶۵'],
            ['AFT @#DHEBREW@ NSN 5765', 'بعد نیسان ۵۷۶۵'],
            ['BEF @#DHEBREW@ NSN 5765', 'قبل نیسان ۵۷۶۵'],
            ['@#DHEBREW@ 15 IYR 5765', '۱۵ لیار ۵۷۶۵'],
            ['@#DHEBREW@ IYR 5765', 'لیار ۵۷۶۵'],
            ['ABT @#DHEBREW@ IYR 5765', 'درباره لیار ۵۷۶۵'],
            ['FROM @#DHEBREW@ IYR 5765', 'از لیار ۵۷۶۵'],
            ['AFT @#DHEBREW@ IYR 5765', 'بعد لیار ۵۷۶۵'],
            ['BEF @#DHEBREW@ IYR 5765', 'قبل لیار ۵۷۶۵'],
            ['@#DHEBREW@ 15 SVN 5765', '۱۵ سیوان ۵۷۶۵'],
            ['@#DHEBREW@ SVN 5765', 'سیوان ۵۷۶۵'],
            ['ABT @#DHEBREW@ SVN 5765', 'درباره سیوان ۵۷۶۵'],
            ['FROM @#DHEBREW@ SVN 5765', 'از سیوان ۵۷۶۵'],
            ['AFT @#DHEBREW@ SVN 5765', 'بعد سیوان ۵۷۶۵'],
            ['BEF @#DHEBREW@ SVN 5765', 'قبل سیوان ۵۷۶۵'],
            ['@#DHEBREW@ 15 TMZ 5765', '۱۵ تموز ۵۷۶۵'],
            ['@#DHEBREW@ TMZ 5765', 'تموز ۵۷۶۵'],
            ['ABT @#DHEBREW@ TMZ 5765', 'درباره تموز ۵۷۶۵'],
            ['FROM @#DHEBREW@ TMZ 5765', 'از تموز ۵۷۶۵'],
            ['AFT @#DHEBREW@ TMZ 5765', 'بعد تموز ۵۷۶۵'],
            ['BEF @#DHEBREW@ TMZ 5765', 'قبل تموز ۵۷۶۵'],
            ['@#DHEBREW@ 15 AAV 5765', '۱۵ آو ۵۷۶۵'],
            ['@#DHEBREW@ AAV 5765', 'آو ۵۷۶۵'],
            ['ABT @#DHEBREW@ AAV 5765', 'درباره آو ۵۷۶۵'],
            ['FROM @#DHEBREW@ AAV 5765', 'از آو ۵۷۶۵'],
            ['AFT @#DHEBREW@ AAV 5765', 'بعد آو ۵۷۶۵'],
            ['BEF @#DHEBREW@ AAV 5765', 'قبل آو ۵۷۶۵'],
            ['@#DHEBREW@ 15 ELL 5765', '۱۵ اِلول ۵۷۶۵'],
            ['@#DHEBREW@ ELL 5765', 'اِلول ۵۷۶۵'],
            ['ABT @#DHEBREW@ ELL 5765', 'درباره اِلول ۵۷۶۵'],
            ['FROM @#DHEBREW@ ELL 5765', 'از اِلول ۵۷۶۵'],
            ['AFT @#DHEBREW@ ELL 5765', 'بعد اِلول ۵۷۶۵'],
            ['BEF @#DHEBREW@ ELL 5765', 'قبل اِلول ۵۷۶۵'],
            ['@#DHEBREW@ 5765', '۵۷۶۵'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'درباره ۱۵ تیشری ۵۷۶۵'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'محاسبه شده ۱۵ تیشری ۵۷۶۵'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'برآورد شده ۱۵ تیشری ۵۷۶۵'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'قبل ۱۵ تیشری ۵۷۶۵'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'بعد ۱۵ تیشری ۵۷۶۵'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'از ۱۵ تیشری ۵۷۶۵'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'به ۱۵ تیشری ۵۷۶۵'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'بین ۱۵ تیشری ۵۷۶۵ و ۱۵ هشوان ۵۷۶۵'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'از ۱۵ تیشری ۵۷۶۵ تا ۱۵ هشوان ۵۷۶۵'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'تعریف شده ۱۵ تیشری ۵۷۶۵'],
            ['@#DFRENCH R@ 15 VEND 12', '۱۵ وندیمیر An XII'],
            ['@#DFRENCH R@ VEND 12', 'وندیمیر An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'درباره وندیمیر An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'از وندیمیر An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'بعد وندیمیر An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'قبل وندیمیر An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '۱۵ برومیر An XII'],
            ['@#DFRENCH R@ BRUM 12', 'برومیر An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'درباره برومیر An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'از برومیر An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'بعد برومیر An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'قبل برومیر An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '۱۵ فریمایر An XII'],
            ['@#DFRENCH R@ FRIM 12', 'فریمایر An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'درباره فریمایر An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'از فریمایر An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'بعد فریمایر An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'قبل فریمایر An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '۱۵ نیوسه An XII'],
            ['@#DFRENCH R@ NIVO 12', 'نیوسه An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'درباره نیوسه An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'از نیوسه An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'بعد نیوسه An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'قبل نیوسه An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '۱۵ پلویوسه An XII'],
            ['@#DFRENCH R@ PLUV 12', 'پلویوسه An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'درباره پلویوسه An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'از پلویوسه An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'بعد پلویوسه An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'قبل پلویوسه An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '۱۵ ونتوسه An XII'],
            ['@#DFRENCH R@ VENT 12', 'ونتوسه An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'درباره ونتوسه An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'از ونتوسه An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'بعد ونتوسه An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'قبل ونتوسه An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '۱۵ ژرمینال An XII'],
            ['@#DFRENCH R@ GERM 12', 'ژرمینال An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'درباره ژرمینال An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'از ژرمینال An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'بعد ژرمینال An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'قبل ژرمینال An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '۱۵ فلورل An XII'],
            ['@#DFRENCH R@ FLOR 12', 'فلورل An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'درباره فلورل An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'از فلورل An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'بعد فلورل An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'قبل فلورل An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '۱۵ پریریال An XII'],
            ['@#DFRENCH R@ PRAI 12', 'پریریال An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'درباره پریریال An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'از پریریال An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'بعد پریریال An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'قبل پریریال An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '۱۵ مسیدور An XII'],
            ['@#DFRENCH R@ MESS 12', 'مسیدور An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'درباره مسیدور An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'از مسیدور An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'بعد مسیدور An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'قبل مسیدور An XII'],
            ['@#DFRENCH R@ 15 THER 12', '۱۵ ترمیدور An XII'],
            ['@#DFRENCH R@ THER 12', 'ترمیدور An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'درباره ترمیدور An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'از ترمیدور An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'بعد ترمیدور An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'قبل ترمیدور An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '۱۵ فروکتیدور An XII'],
            ['@#DFRENCH R@ FRUC 12', 'فروکتیدور An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'درباره فروکتیدور An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'از فروکتیدور An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'بعد فروکتیدور An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'قبل فروکتیدور An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '۱۵ جورس کومپلمنتایرس An XII'],
            ['@#DFRENCH R@ COMP 12', 'جورس کومپلمنتایرس An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'درباره جورس کومپلمنتایرس An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'از جورس کومپلمنتایرس An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'بعد جورس کومپلمنتایرس An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'قبل جورس کومپلمنتایرس An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'درباره ۱۵ وندیمیر An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'محاسبه شده ۱۵ وندیمیر An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'برآورد شده ۱۵ وندیمیر An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'قبل ۱۵ وندیمیر An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'بعد ۱۵ وندیمیر An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'از ۱۵ وندیمیر An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'به ۱۵ وندیمیر An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'بین ۱۵ وندیمیر An XII و ۱۵ برومیر An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'از ۱۵ وندیمیر An XII تا ۱۵ برومیر An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'تعریف شده ۱۵ وندیمیر An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '۱۵ محرم ۱۴۲۵'],
            ['@#DHIJRI@ MUHAR 1425', 'محرم ۱۴۲۵'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'درباره محرم ۱۴۲۵'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'از محرم ۱۴۲۵'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'بعد محرم ۱۴۲۵'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'قبل محرم ۱۴۲۵'],
            ['@#DHIJRI@ 15 SAFAR 1425', '۱۵ صفر ۱۴۲۵'],
            ['@#DHIJRI@ SAFAR 1425', 'صفر ۱۴۲۵'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'درباره صفر ۱۴۲۵'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'از صفر ۱۴۲۵'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'بعد صفر ۱۴۲۵'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'قبل صفر ۱۴۲۵'],
            ['@#DHIJRI@ 15 RABIA 1425', '۱۵ ربیع الاول ۱۴۲۵'],
            ['@#DHIJRI@ RABIA 1425', 'ربیع الاول ۱۴۲۵'],
            ['ABT @#DHIJRI@ RABIA 1425', 'درباره ربیع الاول ۱۴۲۵'],
            ['FROM @#DHIJRI@ RABIA 1425', 'از ربیع الاول ۱۴۲۵'],
            ['AFT @#DHIJRI@ RABIA 1425', 'بعد ربیع الاول ۱۴۲۵'],
            ['BEF @#DHIJRI@ RABIA 1425', 'قبل ربیع الاول ۱۴۲۵'],
            ['@#DHIJRI@ 15 RABIT 1425', '۱۵ ربیع الثانی ۱۴۲۵'],
            ['@#DHIJRI@ RABIT 1425', 'ربیع الثانی ۱۴۲۵'],
            ['ABT @#DHIJRI@ RABIT 1425', 'درباره ربیع الثانی ۱۴۲۵'],
            ['FROM @#DHIJRI@ RABIT 1425', 'از ربیع الثانی ۱۴۲۵'],
            ['AFT @#DHIJRI@ RABIT 1425', 'بعد ربیع الثانی ۱۴۲۵'],
            ['BEF @#DHIJRI@ RABIT 1425', 'قبل ربیع الثانی ۱۴۲۵'],
            ['@#DHIJRI@ 15 JUMAA 1425', '۱۵ جمادی الاول ۱۴۲۵'],
            ['@#DHIJRI@ JUMAA 1425', 'جمادی الاول ۱۴۲۵'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'درباره جمادی الاول ۱۴۲۵'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'از جمادی الاول ۱۴۲۵'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'بعد جمادی الاول ۱۴۲۵'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'قبل جمادی الاول ۱۴۲۵'],
            ['@#DHIJRI@ 15 JUMAT 1425', '۱۵ جمادی الثانی ۱۴۲۵'],
            ['@#DHIJRI@ JUMAT 1425', 'جمادی الثانی ۱۴۲۵'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'درباره جمادی الثانی ۱۴۲۵'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'از جمادی الثانی ۱۴۲۵'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'بعد جمادی الثانی ۱۴۲۵'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'قبل جمادی الثانی ۱۴۲۵'],
            ['@#DHIJRI@ 15 RAJAB 1425', '۱۵ رجب ۱۴۲۵'],
            ['@#DHIJRI@ RAJAB 1425', 'رجب ۱۴۲۵'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'درباره رجب ۱۴۲۵'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'از رجب ۱۴۲۵'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'بعد رجب ۱۴۲۵'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'قبل رجب ۱۴۲۵'],
            ['@#DHIJRI@ 15 SHAAB 1425', '۱۵ شعبان ۱۴۲۵'],
            ['@#DHIJRI@ SHAAB 1425', 'شعبان ۱۴۲۵'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'درباره شعبان ۱۴۲۵'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'از شعبان ۱۴۲۵'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'بعد شعبان ۱۴۲۵'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'قبل شعبان ۱۴۲۵'],
            ['@#DHIJRI@ 15 RAMAD 1425', '۱۵ رمضان ۱۴۲۵'],
            ['@#DHIJRI@ RAMAD 1425', 'رمضان ۱۴۲۵'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'درباره رمضان ۱۴۲۵'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'از رمضان ۱۴۲۵'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'بعد رمضان ۱۴۲۵'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'قبل رمضان ۱۴۲۵'],
            ['@#DHIJRI@ 15 SHAWW 1425', '۱۵ شوال ۱۴۲۵'],
            ['@#DHIJRI@ SHAWW 1425', 'شوال ۱۴۲۵'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'درباره شوال ۱۴۲۵'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'از شوال ۱۴۲۵'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'بعد شوال ۱۴۲۵'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'قبل شوال ۱۴۲۵'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '۱۵ ذوالقعده ۱۴۲۵'],
            ['@#DHIJRI@ DHUAQ 1425', 'ذوالقعده ۱۴۲۵'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'درباره ذوالقعده ۱۴۲۵'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'از ذوالقعده ۱۴۲۵'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'بعد ذوالقعده ۱۴۲۵'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'قبل ذوالقعده ۱۴۲۵'],
            ['@#DHIJRI@ 15 DHUAL 1425', '۱۴۲۵'],
            ['@#DHIJRI@ DHUAL 1425', '۱۴۲۵'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'درباره ۱۴۲۵'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'از ۱۴۲۵'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'بعد ۱۴۲۵'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'قبل ۱۴۲۵'],
            ['@#DHIJRI@ 1425', '۱۴۲۵'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'درباره ۱۵ محرم ۱۴۲۵'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'محاسبه شده ۱۵ محرم ۱۴۲۵'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'برآورد شده ۱۵ محرم ۱۴۲۵'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'قبل ۱۵ محرم ۱۴۲۵'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'بعد ۱۵ محرم ۱۴۲۵'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'از ۱۵ محرم ۱۴۲۵'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'به ۱۵ محرم ۱۴۲۵'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'بین ۱۵ محرم ۱۴۲۵ و ۱۵ صفر ۱۴۲۵'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'از ۱۵ محرم ۱۴۲۵ تا ۱۵ صفر ۱۴۲۵'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'تعریف شده ۱۵ محرم ۱۴۲۵'],
            ['@#DJALALI@ 15 FARVA 1384', '۱۵ فروردین ۱۳۸۴'],
            ['@#DJALALI@ FARVA 1384', 'فروردین ۱۳۸۴'],
            ['ABT @#DJALALI@ FARVA 1384', 'درباره فروردین ۱۳۸۴'],
            ['FROM @#DJALALI@ FARVA 1384', 'از فروردین ۱۳۸۴'],
            ['AFT @#DJALALI@ FARVA 1384', 'بعد فروردین ۱۳۸۴'],
            ['BEF @#DJALALI@ FARVA 1384', 'قبل فروردین ۱۳۸۴'],
            ['@#DJALALI@ 15 ORDIB 1384', '۱۵ اردیبهشت ۱۳۸۴'],
            ['@#DJALALI@ ORDIB 1384', 'اردیبهشت ۱۳۸۴'],
            ['ABT @#DJALALI@ ORDIB 1384', 'درباره اردیبهشت ۱۳۸۴'],
            ['FROM @#DJALALI@ ORDIB 1384', 'از اردیبهشت ۱۳۸۴'],
            ['AFT @#DJALALI@ ORDIB 1384', 'بعد اردیبهشت ۱۳۸۴'],
            ['BEF @#DJALALI@ ORDIB 1384', 'قبل اردیبهشت ۱۳۸۴'],
            ['@#DJALALI@ 15 KHORD 1384', '۱۵ خرداد ۱۳۸۴'],
            ['@#DJALALI@ KHORD 1384', 'خرداد ۱۳۸۴'],
            ['ABT @#DJALALI@ KHORD 1384', 'درباره خرداد ۱۳۸۴'],
            ['FROM @#DJALALI@ KHORD 1384', 'از خرداد ۱۳۸۴'],
            ['AFT @#DJALALI@ KHORD 1384', 'بعد خرداد ۱۳۸۴'],
            ['BEF @#DJALALI@ KHORD 1384', 'قبل خرداد ۱۳۸۴'],
            ['@#DJALALI@ 15 TIR 1384', '۱۵ تیر ۱۳۸۴'],
            ['@#DJALALI@ TIR 1384', 'تیر ۱۳۸۴'],
            ['ABT @#DJALALI@ TIR 1384', 'درباره تیر ۱۳۸۴'],
            ['FROM @#DJALALI@ TIR 1384', 'از تیر ۱۳۸۴'],
            ['AFT @#DJALALI@ TIR 1384', 'بعد تیر ۱۳۸۴'],
            ['BEF @#DJALALI@ TIR 1384', 'قبل تیر ۱۳۸۴'],
            ['@#DJALALI@ 15 MORDA 1384', '۱۵ مرداد ۱۳۸۴'],
            ['@#DJALALI@ MORDA 1384', 'مرداد ۱۳۸۴'],
            ['ABT @#DJALALI@ MORDA 1384', 'درباره مرداد ۱۳۸۴'],
            ['FROM @#DJALALI@ MORDA 1384', 'از مرداد ۱۳۸۴'],
            ['AFT @#DJALALI@ MORDA 1384', 'بعد مرداد ۱۳۸۴'],
            ['BEF @#DJALALI@ MORDA 1384', 'قبل مرداد ۱۳۸۴'],
            ['@#DJALALI@ 15 SHAHR 1384', '۱۵ شهریور ۱۳۸۴'],
            ['@#DJALALI@ SHAHR 1384', 'شهریور ۱۳۸۴'],
            ['ABT @#DJALALI@ SHAHR 1384', 'درباره شهریور ۱۳۸۴'],
            ['FROM @#DJALALI@ SHAHR 1384', 'از شهریور ۱۳۸۴'],
            ['AFT @#DJALALI@ SHAHR 1384', 'بعد شهریور ۱۳۸۴'],
            ['BEF @#DJALALI@ SHAHR 1384', 'قبل شهریور ۱۳۸۴'],
            ['@#DJALALI@ 15 MEHR 1384', '۱۵ مهر ۱۳۸۴'],
            ['@#DJALALI@ MEHR 1384', 'مهر ۱۳۸۴'],
            ['ABT @#DJALALI@ MEHR 1384', 'درباره مهر ۱۳۸۴'],
            ['FROM @#DJALALI@ MEHR 1384', 'از مهر ۱۳۸۴'],
            ['AFT @#DJALALI@ MEHR 1384', 'بعد مهر ۱۳۸۴'],
            ['BEF @#DJALALI@ MEHR 1384', 'قبل مهر ۱۳۸۴'],
            ['@#DJALALI@ 15 ABAN 1384', '۱۵ آبان ۱۳۸۴'],
            ['@#DJALALI@ ABAN 1384', 'آبان ۱۳۸۴'],
            ['ABT @#DJALALI@ ABAN 1384', 'درباره آبان ۱۳۸۴'],
            ['FROM @#DJALALI@ ABAN 1384', 'از آبان ۱۳۸۴'],
            ['AFT @#DJALALI@ ABAN 1384', 'بعد آبان ۱۳۸۴'],
            ['BEF @#DJALALI@ ABAN 1384', 'قبل آبان ۱۳۸۴'],
            ['@#DJALALI@ 15 AZAR 1384', '۱۵ آذر ۱۳۸۴'],
            ['@#DJALALI@ AZAR 1384', 'آذر ۱۳۸۴'],
            ['ABT @#DJALALI@ AZAR 1384', 'درباره آذر ۱۳۸۴'],
            ['FROM @#DJALALI@ AZAR 1384', 'از آذر ۱۳۸۴'],
            ['AFT @#DJALALI@ AZAR 1384', 'بعد آذر ۱۳۸۴'],
            ['BEF @#DJALALI@ AZAR 1384', 'قبل آذر ۱۳۸۴'],
            ['@#DJALALI@ 15 DEY 1384', '۱۵ دی ۱۳۸۴'],
            ['@#DJALALI@ DEY 1384', 'دی ۱۳۸۴'],
            ['ABT @#DJALALI@ DEY 1384', 'درباره دی ۱۳۸۴'],
            ['FROM @#DJALALI@ DEY 1384', 'از دی ۱۳۸۴'],
            ['AFT @#DJALALI@ DEY 1384', 'بعد دی ۱۳۸۴'],
            ['BEF @#DJALALI@ DEY 1384', 'قبل دی ۱۳۸۴'],
            ['@#DJALALI@ 15 BAHMA 1384', '۱۵ بهمن ۱۳۸۴'],
            ['@#DJALALI@ BAHMA 1384', 'بهمن ۱۳۸۴'],
            ['ABT @#DJALALI@ BAHMA 1384', 'درباره بهمن ۱۳۸۴'],
            ['FROM @#DJALALI@ BAHMA 1384', 'از بهمن ۱۳۸۴'],
            ['AFT @#DJALALI@ BAHMA 1384', 'بعد بهمن ۱۳۸۴'],
            ['BEF @#DJALALI@ BAHMA 1384', 'قبل بهمن ۱۳۸۴'],
            ['@#DJALALI@ 15 ESFAN 1384', '۱۵ اسفند ۱۳۸۴'],
            ['@#DJALALI@ ESFAN 1384', 'اسفند ۱۳۸۴'],
            ['ABT @#DJALALI@ ESFAN 1384', 'درباره اسفند ۱۳۸۴'],
            ['FROM @#DJALALI@ ESFAN 1384', 'از اسفند ۱۳۸۴'],
            ['AFT @#DJALALI@ ESFAN 1384', 'بعد اسفند ۱۳۸۴'],
            ['BEF @#DJALALI@ ESFAN 1384', 'قبل اسفند ۱۳۸۴'],
            ['@#DJALALI@ 1384', '۱۳۸۴'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'درباره ۱۵ فروردین ۱۳۸۴'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'محاسبه شده ۱۵ فروردین ۱۳۸۴'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'برآورد شده ۱۵ فروردین ۱۳۸۴'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'قبل ۱۵ فروردین ۱۳۸۴'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'بعد ۱۵ فروردین ۱۳۸۴'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'از ۱۵ فروردین ۱۳۸۴'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'به ۱۵ فروردین ۱۳۸۴'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'بین ۱۵ فروردین ۱۳۸۴ و ۱۵ اردیبهشت ۱۳۸۴'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'از ۱۵ فروردین ۱۳۸۴ تا ۱۵ اردیبهشت ۱۳۸۴'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'تعریف شده ۱۵ فروردین ۱۳۸۴'],
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
        self::assertSame('one و two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one، two و three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one یا two', $language->formatListOr(['one', 'two']));
        self::assertSame('one، two یا three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('زن', 'شوهر', [$husband, $fm, $wife]);
        self::assertRelationshipNames('شوهر سابق', 'همسر سابق', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('نامزد', 'نامزد', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('مادر', 'پسر', [$son, $fm, $wife]);
        self::assertRelationshipNames('پدر', 'پسر', [$son, $fm, $husband]);
        self::assertRelationshipNames('مادر', 'دختر', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('مادرخوانده', 'پسرخوانده', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('پدرخوانده', 'پسرخوانده', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('مادر رضاعی', 'دختر رضاعی', [$fosterDaughter, $fd, $wife]);

        // Siblings (older/younger)
        self::assertRelationshipNames('خواهر کوچک‌تر', 'برادر بزرگ‌تر', [$son, $fm, $daughter]);
        self::assertRelationshipNames('برادر بزرگ‌تر', 'خواهر کوچک‌تر', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('ناپدری', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('نادختری', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws (spouse's parents) — from husband's perspective (wife's parents)
        self::assertRelationshipName('مادرزن', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('پدرزن', [$husband, $fm, $wife, $fw, $fatherOfW]);
        // In-laws (spouse's parents) — from wife's perspective (husband's parents)
        self::assertRelationshipName('مادرشوهر', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('پدرشوهر', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws (child's spouse)
        self::assertRelationshipName('عروس', [$fatherOfH, $fp, $husband, $fm, $wife]);
        self::assertRelationshipName('عروس', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('داماد', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws (spouse's siblings) — from wife's perspective
        self::assertRelationshipName('برادرشوهر', [$wife, $fm, $husband, $fp, $brotherOfH]);
        self::assertRelationshipName('خواهرشوهر', [$wife, $fm, $husband, $fp, $sisterOfH]);
        // In-laws (spouse's siblings) — from husband's perspective
        self::assertRelationshipName('برادرزن', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('خواهرزن', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // In-laws (sibling's spouse) — tested via the definitions, not as a path here
        // (brother's test family has no wife defined)

        // Grandparents
        self::assertRelationshipNames('مادربزرگ', 'نوه', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('پدربزرگ', 'نوه', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('نوه', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts — paternal
        self::assertRelationshipName('عمه', [$son, $fm, $husband, $fp, $sisterOfH]);
        // Aunts — maternal
        self::assertRelationshipName('خاله', [$son, $fm, $wife, $fw, $sisterOfW]);
        // Uncles — paternal
        self::assertRelationshipName('عمو', [$son, $fm, $husband, $fp, $brotherOfH]);
        // Uncles — maternal
        self::assertRelationshipName('دایی', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces/nephews — through brother
        self::assertRelationshipName('دختر برادر', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('پسر برادر', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        // Nieces/nephews — through sister
        self::assertRelationshipName('دختر خواهر', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('پسر خواهر', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — paternal uncle's children (پسرعمو / دخترعمو)
        self::assertRelationshipName('پسرعمو', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMPU]);
        self::assertRelationshipName('دخترعمو', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFPU]);
        // Cousins — paternal aunt's children (پسرعمه / دخترعمه)
        self::assertRelationshipName('پسرعمه', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinMPA]);
        self::assertRelationshipName('دخترعمه', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinFPA]);
        // Cousins — maternal uncle's children (پسردایی / دختردایی)
        self::assertRelationshipName('پسردایی', [$son, $fm, $wife, $fw, $maternalUncle, $fmbro, $cousinMMU]);
        self::assertRelationshipName('دختردایی', [$son, $fm, $wife, $fw, $maternalUncle, $fmbro, $cousinFMU]);
        // Cousins — maternal aunt's children (پسرخاله / دخترخاله)
        self::assertRelationshipName('پسرخاله', [$son, $fm, $wife, $fw, $maternalAunt, $fmsis, $cousinMMA]);
        self::assertRelationshipName('دخترخاله', [$son, $fm, $wife, $fw, $maternalAunt, $fmsis, $cousinFMA]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('مادربزرگ بزرگ', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('پدربزرگ بزرگ', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('عمه/خاله بزرگ', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('عمو/دایی بزرگ', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
