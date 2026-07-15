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
use Fisharebest\Webtrees\I18N\Languages\Vietnamese;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Vietnamese::class)]
class VietnameseTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Vietnamese();
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
        self::assertSame('vi', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Tiếng Việt', self::language()->endonym());
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
        self::assertSame('-123.456,0789%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Tháng Giêng 2000'],
            ['JAN 2000', 'Tháng Giêng 2000'],
            ['ABT JAN 2000', 'khoảng Tháng Giêng 2000'],
            ['FROM JAN 2000', 'từ Tháng Giêng 2000'],
            ['AFT JAN 2000', 'sau Tháng Giêng 2000'],
            ['BEF JAN 2000', 'trước Tháng Giêng 2000'],
            ['15 FEB 2000', '15 Tháng Hai 2000'],
            ['FEB 2000', 'Tháng Hai 2000'],
            ['ABT FEB 2000', 'khoảng Tháng Hai 2000'],
            ['FROM FEB 2000', 'từ Tháng Hai 2000'],
            ['AFT FEB 2000', 'sau Tháng Hai 2000'],
            ['BEF FEB 2000', 'trước Tháng Hai 2000'],
            ['15 MAR 2000', '15 Tháng Ba 2000'],
            ['MAR 2000', 'Tháng Ba 2000'],
            ['ABT MAR 2000', 'khoảng Tháng Ba 2000'],
            ['FROM MAR 2000', 'từ Tháng Ba 2000'],
            ['AFT MAR 2000', 'sau Tháng Ba 2000'],
            ['BEF MAR 2000', 'trước Tháng Ba 2000'],
            ['15 APR 2000', '15 Tháng Tư 2000'],
            ['APR 2000', 'Tháng Tư 2000'],
            ['ABT APR 2000', 'khoảng Tháng Tư 2000'],
            ['FROM APR 2000', 'từ Tháng Tư 2000'],
            ['AFT APR 2000', 'sau Tháng Tư 2000'],
            ['BEF APR 2000', 'trước Tháng Tư 2000'],
            ['15 MAY 2000', '15 Tháng Năm 2000'],
            ['MAY 2000', 'Tháng Năm 2000'],
            ['ABT MAY 2000', 'khoảng Tháng Năm 2000'],
            ['FROM MAY 2000', 'từ Tháng Năm 2000'],
            ['AFT MAY 2000', 'sau Tháng Năm 2000'],
            ['BEF MAY 2000', 'trước Tháng Năm 2000'],
            ['15 JUN 2000', '15 Tháng Sáu 2000'],
            ['JUN 2000', 'Tháng Sáu 2000'],
            ['ABT JUN 2000', 'khoảng Tháng Sáu 2000'],
            ['FROM JUN 2000', 'từ Tháng Sáu 2000'],
            ['AFT JUN 2000', 'sau Tháng Sáu 2000'],
            ['BEF JUN 2000', 'trước Tháng Sáu 2000'],
            ['15 JUL 2000', '15 Tháng Bảy 2000'],
            ['JUL 2000', 'Tháng Bảy 2000'],
            ['ABT JUL 2000', 'khoảng Tháng Bảy 2000'],
            ['FROM JUL 2000', 'từ Tháng Bảy 2000'],
            ['AFT JUL 2000', 'sau Tháng Bảy 2000'],
            ['BEF JUL 2000', 'trước Tháng Bảy 2000'],
            ['15 AUG 2000', '15 Tháng Tám 2000'],
            ['AUG 2000', 'Tháng Tám 2000'],
            ['ABT AUG 2000', 'khoảng Tháng Tám 2000'],
            ['FROM AUG 2000', 'từ Tháng Tám 2000'],
            ['AFT AUG 2000', 'sau Tháng Tám 2000'],
            ['BEF AUG 2000', 'trước Tháng Tám 2000'],
            ['15 SEP 2000', '15 Tháng Chín 2000'],
            ['SEP 2000', 'Tháng Chín 2000'],
            ['ABT SEP 2000', 'khoảng Tháng Chín 2000'],
            ['FROM SEP 2000', 'từ Tháng Chín 2000'],
            ['AFT SEP 2000', 'sau Tháng Chín 2000'],
            ['BEF SEP 2000', 'trước Tháng Chín 2000'],
            ['15 OCT 2000', '15 Tháng Mười 2000'],
            ['OCT 2000', 'Tháng Mười 2000'],
            ['ABT OCT 2000', 'khoảng Tháng Mười 2000'],
            ['FROM OCT 2000', 'từ Tháng Mười 2000'],
            ['AFT OCT 2000', 'sau Tháng Mười 2000'],
            ['BEF OCT 2000', 'trước Tháng Mười 2000'],
            ['15 NOV 2000', '15 Tháng Mười Một 2000'],
            ['NOV 2000', 'Tháng Mười Một 2000'],
            ['ABT NOV 2000', 'khoảng Tháng Mười Một 2000'],
            ['FROM NOV 2000', 'từ Tháng Mười Một 2000'],
            ['AFT NOV 2000', 'sau Tháng Mười Một 2000'],
            ['BEF NOV 2000', 'trước Tháng Mười Một 2000'],
            ['15 DEC 2000', '15 Tháng Mười Hai 2000'],
            ['DEC 2000', 'Tháng Mười Hai 2000'],
            ['ABT DEC 2000', 'khoảng Tháng Mười Hai 2000'],
            ['FROM DEC 2000', 'từ Tháng Mười Hai 2000'],
            ['AFT DEC 2000', 'sau Tháng Mười Hai 2000'],
            ['BEF DEC 2000', 'trước Tháng Mười Hai 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'khoảng 15 Tháng Giêng 2000'],
            ['CAL 15 JAN 2000', 'được tính 15 Tháng Giêng 2000'],
            ['EST 15 JAN 2000', 'ước tính 15 Tháng Giêng 2000'],
            ['BEF 15 JAN 2000', 'trước 15 Tháng Giêng 2000'],
            ['AFT 15 JAN 2000', 'sau 15 Tháng Giêng 2000'],
            ['FROM 15 JAN 2000', 'từ 15 Tháng Giêng 2000'],
            ['TO 15 JAN 2000', 'đến 15 Tháng Giêng 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'giữa 15 Tháng Giêng 2000 và 15 Tháng Hai 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'từ 15 Tháng Giêng 2000 đến 15 Tháng Hai 2000'],
            ['INT 15 JAN 2000', 'giải thích là 15 Tháng Giêng 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Tháng Giêng 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Tháng Giêng 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'khoảng Tháng Giêng 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'từ Tháng Giêng 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'sau Tháng Giêng 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'trước Tháng Giêng 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Tháng Hai 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Tháng Hai 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'khoảng Tháng Hai 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'từ Tháng Hai 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'sau Tháng Hai 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'trước Tháng Hai 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Tháng Ba 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Tháng Ba 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'khoảng Tháng Ba 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'từ Tháng Ba 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'sau Tháng Ba 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'trước Tháng Ba 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 Tháng Tư 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Tháng Tư 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'Tháng Tư 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'khoảng Tháng Tư 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'từ Tháng Tư 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'sau Tháng Tư 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'trước Tháng Tư 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Tháng Năm 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Tháng Năm 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'khoảng Tháng Năm 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'từ Tháng Năm 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'sau Tháng Năm 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'trước Tháng Năm 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Tháng Sáu 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Tháng Sáu 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'khoảng Tháng Sáu 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'từ Tháng Sáu 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'sau Tháng Sáu 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'trước Tháng Sáu 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Tháng Bảy 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Tháng Bảy 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'khoảng Tháng Bảy 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'từ Tháng Bảy 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'sau Tháng Bảy 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'trước Tháng Bảy 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Tháng Tám 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Tháng Tám 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'khoảng Tháng Tám 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'từ Tháng Tám 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'sau Tháng Tám 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'trước Tháng Tám 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Tháng Chín 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Tháng Chín 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'khoảng Tháng Chín 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'từ Tháng Chín 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'sau Tháng Chín 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'trước Tháng Chín 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Tháng Mười 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Tháng Mười 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'khoảng Tháng Mười 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'từ Tháng Mười 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'sau Tháng Mười 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'trước Tháng Mười 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Tháng Mười Một 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Tháng Mười Một 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'khoảng Tháng Mười Một 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'từ Tháng Mười Một 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'sau Tháng Mười Một 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'trước Tháng Mười Một 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Tháng Mười Hai 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Tháng Mười Hai 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'khoảng Tháng Mười Hai 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'từ Tháng Mười Hai 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'sau Tháng Mười Hai 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'trước Tháng Mười Hai 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'khoảng 15 Tháng Giêng 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'được tính 15 Tháng Giêng 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'ước tính 15 Tháng Giêng 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'trước 15 Tháng Giêng 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'sau 15 Tháng Giêng 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'từ 15 Tháng Giêng 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'đến 15 Tháng Giêng 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'giữa 15 Tháng Giêng 1700 ᴄᴇ và 15 Tháng Hai 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'từ 15 Tháng Giêng 1700 ᴄᴇ đến 15 Tháng Hai 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'giải thích là 15 Tháng Giêng 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'khoảng Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'từ Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'sau Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'trước Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'khoảng Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'từ Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'sau Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'trước Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'khoảng Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'từ Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'sau Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'trước Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'khoảng Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'từ Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'sau Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'trước Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'khoảng Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'từ Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'sau Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'trước Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'khoảng Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'từ Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'sau Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'trước Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'khoảng Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'từ Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'sau Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'trước Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'khoảng Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'từ Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'sau Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'trước Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'khoảng Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'từ Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'sau Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'trước Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'khoảng Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'từ Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'sau Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'trước Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'khoảng Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'từ Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'sau Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'trước Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'khoảng Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'từ Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'sau Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'trước Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'khoảng Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'từ Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'sau Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'trước Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'khoảng 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'được tính 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'ước tính 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'trước 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'sau 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'từ 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'đến 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'giữa 15 Tishrei 5765 và 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'từ 15 Tishrei 5765 đến 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'giải thích là 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'khoảng Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'từ Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'sau Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'trước Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'khoảng Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'từ Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'sau Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'trước Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'khoảng Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'từ Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'sau Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'trước Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'khoảng Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'từ Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'sau Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'trước Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'khoảng Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'từ Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'sau Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'trước Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'khoảng Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'từ Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'sau Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'trước Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'khoảng Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'từ Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'sau Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'trước Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'khoảng Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'từ Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'sau Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'trước Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'khoảng Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'từ Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'sau Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'trước Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'khoảng Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'từ Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'sau Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'trước Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'khoảng Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'từ Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'sau Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'trước Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'khoảng Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'từ Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'sau Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'trước Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'khoảng jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'từ jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'sau jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'trước jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'khoảng 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'được tính 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'ước tính 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'trước 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'sau 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'từ 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'đến 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'giữa 15 Vendémiaire An XII và 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'từ 15 Vendémiaire An XII đến 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'giải thích là 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'khoảng Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'từ Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'sau Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'trước Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'khoảng Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'từ Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'sau Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'trước Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'khoảng Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'từ Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'sau Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'trước Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'khoảng Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'từ Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'sau Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'trước Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'khoảng Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'từ Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'sau Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'trước Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'khoảng Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'từ Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'sau Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'trước Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'khoảng Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'từ Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'sau Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'trước Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'khoảng Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'từ Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'sau Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'trước Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'khoảng Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'từ Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'sau Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'trước Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'khoảng Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'từ Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'sau Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'trước Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'khoảng Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'từ Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'sau Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'trước Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'khoảng 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'từ 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'sau 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'trước 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'khoảng 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'được tính 15 Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'ước tính 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'trước 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'sau 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'từ 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'đến 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'giữa 15 Muharram 1425 và 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'từ 15 Muharram 1425 đến 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'giải thích là 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'khoảng Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'từ Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'sau Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'trước Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'khoảng Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'từ Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'sau Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'trước Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'khoảng Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'từ Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'sau Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'trước Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'khoảng Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'từ Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'sau Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'trước Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'khoảng Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'từ Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'sau Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'trước Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'khoảng Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'từ Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'sau Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'trước Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'khoảng Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'từ Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'sau Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'trước Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'khoảng Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'từ Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'sau Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'trước Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'khoảng Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'từ Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'sau Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'trước Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'khoảng Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'từ Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'sau Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'trước Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'khoảng Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'từ Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'sau Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'trước Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'khoảng Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'từ Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'sau Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'trước Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'khoảng 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'được tính 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'ước tính 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'trước 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'sau 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'từ 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'đến 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'giữa 15 Farvardin 1384 và 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'từ 15 Farvardin 1384 đến 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'giải thích là 15 Farvardin 1384'],
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
        self::assertSame('one và two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two và three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one hoặc two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two hoặc three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife with son (born 2000) and daughter (born 1998)
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 1 JAN 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 1 JAN 1998");
        $child = self::unknown('c', "1 FAMC @fm@");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $fosterDaughter = self::female('fsd', "1 FAMC @fd@\n2 PEDI foster");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");

        // Husband's family (paternal side)
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMS @fbro@\n1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        // Wife's family (maternal side)
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Nieces/nephews
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins (via husband's siblings — paternal side of son)
        $cousinMPU = self::male('cmpu', "1 FAMC @fbro@");
        $cousinFPA = self::female('cfpa', "1 FAMC @fsis@");

        // Maternal uncle and aunt of wife (for cousin tests)
        $maternalUncle = self::male('mu', "1 FAMS @fmbro@\n1 FAMC @fw@");
        $maternalAunt = self::female('ma', "1 FAMS @fmsis@\n1 FAMC @fw@");

        // Great-grandparents
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
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cmpu@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cfpa@");
        $fmbro = self::family('fmbro', "0 @fmbro@ FAM\n1 HUSB @mu@");
        $fmsis = self::family('fmsis', "0 @fmsis@ FAM\n1 WIFE @ma@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinMPU, $cousinFPA,
             $maternalUncle, $maternalAunt,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fmbro, $fmsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('vợ', 'chồng', [$husband, $fm, $wife]);
        self::assertRelationshipNames('chồng cũ', 'vợ cũ', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('hôn thê', 'hôn phu', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('mẹ', 'con trai', [$son, $fm, $wife]);
        self::assertRelationshipNames('bố', 'con trai', [$son, $fm, $husband]);
        self::assertRelationshipNames('mẹ', 'con gái', [$daughter, $fm, $wife]);

        // Adopted / Fostered
        self::assertRelationshipNames('mẹ nuôi', 'con trai nuôi', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('mẹ đỡ đầu', 'con gái đỡ đầu', [$fosterDaughter, $fd, $wife]);

        // Siblings — elder/younger (daughter born 1998 is older than son born 2000)
        self::assertRelationshipNames('chị', 'em trai', [$son, $fm, $daughter]);
        self::assertRelationshipNames('em trai', 'chị', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('bố dượng', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('con gái riêng', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents (distinguished by side)
        self::assertRelationshipName('mẹ vợ', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('bố vợ', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('mẹ chồng', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('bố chồng', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('con dâu', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('con rể', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('anh/em vợ', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('chị/em vợ', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // In-laws — sibling's spouse (brother's wife = chị/em dâu, sister's husband = anh/em rể)
        // These are tested implicitly via the sibling()->wife()/husband() rules
        // The brother's wife and sister's husband individuals aren't set up separately

        // Grandparents — paternal (father's parents)
        self::assertRelationshipName('bà nội', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('ông nội', [$son, $fm, $husband, $fp, $fatherOfH]);
        // Grandparents — maternal (mother's parents)
        self::assertRelationshipName('bà ngoại', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('ông ngoại', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('cháu trai', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('cháu gái', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts/Uncles — paternal
        self::assertRelationshipName('cô', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('chú/bác', [$son, $fm, $husband, $fp, $brotherOfH]);
        // Aunts/Uncles — maternal
        self::assertRelationshipName('dì', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('cậu', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces/Nephews — through brother
        self::assertRelationshipName('cháu gái', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('cháu trai', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        // Nieces/Nephews — through sister
        self::assertRelationshipName('cháu gái', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('cháu trai', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — paternal uncle's son
        self::assertRelationshipName('anh chị em họ', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMPU]);
        // Cousins — paternal aunt's daughter
        self::assertRelationshipName('anh chị em họ', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinFPA]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('cụ bà', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('cụ ông', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic — grandparent's sibling, n=2)
        self::assertRelationshipName('cô/dì', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('chú/bác/cậu', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
