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
use Fisharebest\Webtrees\I18N\Languages\Thai;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Thai::class)]
class ThaiTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Thai();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Thai, self::language()->script());
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
        self::assertSame(TextDirection::LTR, self::language()->textDirection());
    }

    public function testAlphabet(): void
    {
        self::assertSame([], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('th', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('ไทย', self::language()->endonym());
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
        self::assertSame('-๑๒๓,๔๕๖.๐๗๘๙', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('-๑๒๓,๔๕๖.๐๗๘๙', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-๑๒๓,๔๕๖.๐๗๘๙%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '๑๕ มกราคม ๒๐๐๐'],
            ['JAN 2000', 'มกราคม ๒๐๐๐'],
            ['ABT JAN 2000', 'khoảng มกราคม ๒๐๐๐'],
            ['FROM JAN 2000', 'từ มกราคม ๒๐๐๐'],
            ['AFT JAN 2000', 'sau มกราคม ๒๐๐๐'],
            ['BEF JAN 2000', 'trước มกราคม ๒๐๐๐'],
            ['15 FEB 2000', '๑๕ กุมภาพันธ์ ๒๐๐๐'],
            ['FEB 2000', 'กุมภาพันธ์ ๒๐๐๐'],
            ['ABT FEB 2000', 'khoảng กุมภาพันธ์ ๒๐๐๐'],
            ['FROM FEB 2000', 'từ กุมภาพันธ์ ๒๐๐๐'],
            ['AFT FEB 2000', 'sau กุมภาพันธ์ ๒๐๐๐'],
            ['BEF FEB 2000', 'trước กุมภาพันธ์ ๒๐๐๐'],
            ['15 MAR 2000', '๑๕ มีนาคม ๒๐๐๐'],
            ['MAR 2000', 'มีนาคม ๒๐๐๐'],
            ['ABT MAR 2000', 'khoảng มีนาคม ๒๐๐๐'],
            ['FROM MAR 2000', 'từ มีนาคม ๒๐๐๐'],
            ['AFT MAR 2000', 'sau มีนาคม ๒๐๐๐'],
            ['BEF MAR 2000', 'trước มีนาคม ๒๐๐๐'],
            ['15 APR 2000', '๑๕ เมษายน ๒๐๐๐'],
            ['APR 2000', 'เมษายน ๒๐๐๐'],
            ['ABT APR 2000', 'khoảng เมษายน ๒๐๐๐'],
            ['FROM APR 2000', 'từ เมษายน ๒๐๐๐'],
            ['AFT APR 2000', 'sau เมษายน ๒๐๐๐'],
            ['BEF APR 2000', 'trước เมษายน ๒๐๐๐'],
            ['15 MAY 2000', '๑๕ พฤษภาคม ๒๐๐๐'],
            ['MAY 2000', 'พฤษภาคม ๒๐๐๐'],
            ['ABT MAY 2000', 'khoảng พฤษภาคม ๒๐๐๐'],
            ['FROM MAY 2000', 'từ พฤษภาคม ๒๐๐๐'],
            ['AFT MAY 2000', 'sau พฤษภาคม ๒๐๐๐'],
            ['BEF MAY 2000', 'trước พฤษภาคม ๒๐๐๐'],
            ['15 JUN 2000', '๑๕ มิถุนายน ๒๐๐๐'],
            ['JUN 2000', 'มิถุนายน ๒๐๐๐'],
            ['ABT JUN 2000', 'khoảng มิถุนายน ๒๐๐๐'],
            ['FROM JUN 2000', 'từ มิถุนายน ๒๐๐๐'],
            ['AFT JUN 2000', 'sau มิถุนายน ๒๐๐๐'],
            ['BEF JUN 2000', 'trước มิถุนายน ๒๐๐๐'],
            ['15 JUL 2000', '๑๕ กรกฎาคม ๒๐๐๐'],
            ['JUL 2000', 'กรกฎาคม ๒๐๐๐'],
            ['ABT JUL 2000', 'khoảng กรกฎาคม ๒๐๐๐'],
            ['FROM JUL 2000', 'từ กรกฎาคม ๒๐๐๐'],
            ['AFT JUL 2000', 'sau กรกฎาคม ๒๐๐๐'],
            ['BEF JUL 2000', 'trước กรกฎาคม ๒๐๐๐'],
            ['15 AUG 2000', '๑๕ สิงหาคม ๒๐๐๐'],
            ['AUG 2000', 'สิงหาคม ๒๐๐๐'],
            ['ABT AUG 2000', 'khoảng สิงหาคม ๒๐๐๐'],
            ['FROM AUG 2000', 'từ สิงหาคม ๒๐๐๐'],
            ['AFT AUG 2000', 'sau สิงหาคม ๒๐๐๐'],
            ['BEF AUG 2000', 'trước สิงหาคม ๒๐๐๐'],
            ['15 SEP 2000', '๑๕ กันยายน ๒๐๐๐'],
            ['SEP 2000', 'กันยายน ๒๐๐๐'],
            ['ABT SEP 2000', 'khoảng กันยายน ๒๐๐๐'],
            ['FROM SEP 2000', 'từ กันยายน ๒๐๐๐'],
            ['AFT SEP 2000', 'sau กันยายน ๒๐๐๐'],
            ['BEF SEP 2000', 'trước กันยายน ๒๐๐๐'],
            ['15 OCT 2000', '๑๕ ตุลาคม ๒๐๐๐'],
            ['OCT 2000', 'ตุลาคม ๒๐๐๐'],
            ['ABT OCT 2000', 'khoảng ตุลาคม ๒๐๐๐'],
            ['FROM OCT 2000', 'từ ตุลาคม ๒๐๐๐'],
            ['AFT OCT 2000', 'sau ตุลาคม ๒๐๐๐'],
            ['BEF OCT 2000', 'trước ตุลาคม ๒๐๐๐'],
            ['15 NOV 2000', '๑๕ พฤศจิกายน ๒๐๐๐'],
            ['NOV 2000', 'พฤศจิกายน ๒๐๐๐'],
            ['ABT NOV 2000', 'khoảng พฤศจิกายน ๒๐๐๐'],
            ['FROM NOV 2000', 'từ พฤศจิกายน ๒๐๐๐'],
            ['AFT NOV 2000', 'sau พฤศจิกายน ๒๐๐๐'],
            ['BEF NOV 2000', 'trước พฤศจิกายน ๒๐๐๐'],
            ['15 DEC 2000', '๑๕ ธันวาคม ๒๐๐๐'],
            ['DEC 2000', 'ธันวาคม ๒๐๐๐'],
            ['ABT DEC 2000', 'khoảng ธันวาคม ๒๐๐๐'],
            ['FROM DEC 2000', 'từ ธันวาคม ๒๐๐๐'],
            ['AFT DEC 2000', 'sau ธันวาคม ๒๐๐๐'],
            ['BEF DEC 2000', 'trước ธันวาคม ๒๐๐๐'],
            ['2000', '๒๐๐๐'],
            ['ABT 15 JAN 2000', 'khoảng ๑๕ มกราคม ๒๐๐๐'],
            ['CAL 15 JAN 2000', 'được tính ๑๕ มกราคม ๒๐๐๐'],
            ['EST 15 JAN 2000', 'ước tính ๑๕ มกราคม ๒๐๐๐'],
            ['BEF 15 JAN 2000', 'trước ๑๕ มกราคม ๒๐๐๐'],
            ['AFT 15 JAN 2000', 'sau ๑๕ มกราคม ๒๐๐๐'],
            ['FROM 15 JAN 2000', 'từ ๑๕ มกราคม ๒๐๐๐'],
            ['TO 15 JAN 2000', 'đến ๑๕ มกราคม ๒๐๐๐'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'giữa ๑๕ มกราคม ๒๐๐๐ và ๑๕ กุมภาพันธ์ ๒๐๐๐'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'từ ๑๕ มกราคม ๒๐๐๐ đến ๑๕ กุมภาพันธ์ ๒๐๐๐'],
            ['INT 15 JAN 2000', 'giải thích là ๑๕ มกราคม ๒๐๐๐'],
            ['@#DJULIAN@ 15 JAN 1700', '๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'มกราคม ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'khoảng มกราคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'từ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'sau มกราคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'trước มกราคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '๑๕ กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'khoảng กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'từ กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'sau กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'trước กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '๑๕ มีนาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'มีนาคม ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'khoảng มีนาคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'từ มีนาคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'sau มีนาคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'trước มีนาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '๑๕ เมษายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '๑๔ เมษายน ๑๖๔๕/๔๖ ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'เมษายน ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'khoảng เมษายน ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'từ เมษายน ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'sau เมษายน ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'trước เมษายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '๑๕ พฤษภาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'พฤษภาคม ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'khoảng พฤษภาคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'từ พฤษภาคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'sau พฤษภาคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'trước พฤษภาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '๑๕ มิถุนายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'มิถุนายน ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'khoảng มิถุนายน ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'từ มิถุนายน ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'sau มิถุนายน ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'trước มิถุนายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '๑๕ กรกฎาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'กรกฎาคม ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'khoảng กรกฎาคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'từ กรกฎาคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'sau กรกฎาคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'trước กรกฎาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '๑๕ สิงหาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'สิงหาคม ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'khoảng สิงหาคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'từ สิงหาคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'sau สิงหาคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'trước สิงหาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '๑๕ กันยายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'กันยายน ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'khoảng กันยายน ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'từ กันยายน ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'sau กันยายน ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'trước กันยายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '๑๕ ตุลาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'ตุลาคม ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'khoảng ตุลาคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'từ ตุลาคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'sau ตุลาคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'trước ตุลาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '๑๕ พฤศจิกายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'พฤศจิกายน ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'khoảng พฤศจิกายน ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'từ พฤศจิกายน ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'sau พฤศจิกายน ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'trước พฤศจิกายน ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '๑๕ ธันวาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'ธันวาคม ๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'khoảng ธันวาคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'từ ธันวาคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'sau ธันวาคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'trước ธันวาคม ๑๗๐๐ ᴄᴇ'],
            ['@#DJULIAN@ 1700', '๑๗๐๐ ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'khoảng ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'được tính ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'ước tính ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'trước ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'sau ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'từ ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'đến ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'giữa ๑๕ มกราคม ๑๗๐๐ ᴄᴇ và ๑๕ กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'từ ๑๕ มกราคม ๑๗๐๐ ᴄᴇ đến ๑๕ กุมภาพันธ์ ๑๗๐๐ ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'giải thích là ๑๕ มกราคม ๑๗๐๐ ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '๑๕ ทิชเร ๕๗๖๕'],
            ['@#DHEBREW@ TSH 5765', 'ทิชเร ๕๗๖๕'],
            ['ABT @#DHEBREW@ TSH 5765', 'khoảng ทิชเร ๕๗๖๕'],
            ['FROM @#DHEBREW@ TSH 5765', 'từ ทิชเร ๕๗๖๕'],
            ['AFT @#DHEBREW@ TSH 5765', 'sau ทิชเร ๕๗๖๕'],
            ['BEF @#DHEBREW@ TSH 5765', 'trước ทิชเร ๕๗๖๕'],
            ['@#DHEBREW@ 15 CSH 5765', '๑๕ เฮชวาน ๕๗๖๕'],
            ['@#DHEBREW@ CSH 5765', 'เฮชวาน ๕๗๖๕'],
            ['ABT @#DHEBREW@ CSH 5765', 'khoảng เฮชวาน ๕๗๖๕'],
            ['FROM @#DHEBREW@ CSH 5765', 'từ เฮชวาน ๕๗๖๕'],
            ['AFT @#DHEBREW@ CSH 5765', 'sau เฮชวาน ๕๗๖๕'],
            ['BEF @#DHEBREW@ CSH 5765', 'trước เฮชวาน ๕๗๖๕'],
            ['@#DHEBREW@ 15 KSL 5765', '๑๕ คิสเลฟ ๕๗๖๕'],
            ['@#DHEBREW@ KSL 5765', 'คิสเลฟ ๕๗๖๕'],
            ['ABT @#DHEBREW@ KSL 5765', 'khoảng คิสเลฟ ๕๗๖๕'],
            ['FROM @#DHEBREW@ KSL 5765', 'từ คิสเลฟ ๕๗๖๕'],
            ['AFT @#DHEBREW@ KSL 5765', 'sau คิสเลฟ ๕๗๖๕'],
            ['BEF @#DHEBREW@ KSL 5765', 'trước คิสเลฟ ๕๗๖๕'],
            ['@#DHEBREW@ 15 TVT 5765', '๑๕ เตเวต ๕๗๖๕'],
            ['@#DHEBREW@ TVT 5765', 'เตเวต ๕๗๖๕'],
            ['ABT @#DHEBREW@ TVT 5765', 'khoảng เตเวต ๕๗๖๕'],
            ['FROM @#DHEBREW@ TVT 5765', 'từ เตเวต ๕๗๖๕'],
            ['AFT @#DHEBREW@ TVT 5765', 'sau เตเวต ๕๗๖๕'],
            ['BEF @#DHEBREW@ TVT 5765', 'trước เตเวต ๕๗๖๕'],
            ['@#DHEBREW@ 15 SHV 5765', '๑๕ เชวัต ๕๗๖๕'],
            ['@#DHEBREW@ SHV 5765', 'เชวัต ๕๗๖๕'],
            ['ABT @#DHEBREW@ SHV 5765', 'khoảng เชวัต ๕๗๖๕'],
            ['FROM @#DHEBREW@ SHV 5765', 'từ เชวัต ๕๗๖๕'],
            ['AFT @#DHEBREW@ SHV 5765', 'sau เชวัต ๕๗๖๕'],
            ['BEF @#DHEBREW@ SHV 5765', 'trước เชวัต ๕๗๖๕'],
            ['@#DHEBREW@ 15 ADR 5765', '๑๕ อาดาร์ 1 ๕๗๖๕'],
            ['@#DHEBREW@ ADR 5765', 'อาดาร์ 1 ๕๗๖๕'],
            ['ABT @#DHEBREW@ ADR 5765', 'khoảng อาดาร์ 1 ๕๗๖๕'],
            ['FROM @#DHEBREW@ ADR 5765', 'từ อาดาร์ 1 ๕๗๖๕'],
            ['AFT @#DHEBREW@ ADR 5765', 'sau อาดาร์ 1 ๕๗๖๕'],
            ['BEF @#DHEBREW@ ADR 5765', 'trước อาดาร์ 1 ๕๗๖๕'],
            ['@#DHEBREW@ 15 ADS 5765', '๑๕ อาดาร์ 2 ๕๗๖๕'],
            ['@#DHEBREW@ ADS 5765', 'อาดาร์ 2 ๕๗๖๕'],
            ['ABT @#DHEBREW@ ADS 5765', 'khoảng อาดาร์ 2 ๕๗๖๕'],
            ['FROM @#DHEBREW@ ADS 5765', 'từ อาดาร์ 2 ๕๗๖๕'],
            ['AFT @#DHEBREW@ ADS 5765', 'sau อาดาร์ 2 ๕๗๖๕'],
            ['BEF @#DHEBREW@ ADS 5765', 'trước อาดาร์ 2 ๕๗๖๕'],
            ['@#DHEBREW@ 15 NSN 5765', '๑๕ นิสซาน ๕๗๖๕'],
            ['@#DHEBREW@ NSN 5765', 'นิสซาน ๕๗๖๕'],
            ['ABT @#DHEBREW@ NSN 5765', 'khoảng นิสซาน ๕๗๖๕'],
            ['FROM @#DHEBREW@ NSN 5765', 'từ นิสซาน ๕๗๖๕'],
            ['AFT @#DHEBREW@ NSN 5765', 'sau นิสซาน ๕๗๖๕'],
            ['BEF @#DHEBREW@ NSN 5765', 'trước นิสซาน ๕๗๖๕'],
            ['@#DHEBREW@ 15 IYR 5765', '๑๕ อิยาร์ ๕๗๖๕'],
            ['@#DHEBREW@ IYR 5765', 'อิยาร์ ๕๗๖๕'],
            ['ABT @#DHEBREW@ IYR 5765', 'khoảng อิยาร์ ๕๗๖๕'],
            ['FROM @#DHEBREW@ IYR 5765', 'từ อิยาร์ ๕๗๖๕'],
            ['AFT @#DHEBREW@ IYR 5765', 'sau อิยาร์ ๕๗๖๕'],
            ['BEF @#DHEBREW@ IYR 5765', 'trước อิยาร์ ๕๗๖๕'],
            ['@#DHEBREW@ 15 SVN 5765', '๑๕ สิวาน ๕๗๖๕'],
            ['@#DHEBREW@ SVN 5765', 'สิวาน ๕๗๖๕'],
            ['ABT @#DHEBREW@ SVN 5765', 'khoảng สิวาน ๕๗๖๕'],
            ['FROM @#DHEBREW@ SVN 5765', 'từ สิวาน ๕๗๖๕'],
            ['AFT @#DHEBREW@ SVN 5765', 'sau สิวาน ๕๗๖๕'],
            ['BEF @#DHEBREW@ SVN 5765', 'trước สิวาน ๕๗๖๕'],
            ['@#DHEBREW@ 15 TMZ 5765', '๑๕ ทามุซ ๕๗๖๕'],
            ['@#DHEBREW@ TMZ 5765', 'ทามุซ ๕๗๖๕'],
            ['ABT @#DHEBREW@ TMZ 5765', 'khoảng ทามุซ ๕๗๖๕'],
            ['FROM @#DHEBREW@ TMZ 5765', 'từ ทามุซ ๕๗๖๕'],
            ['AFT @#DHEBREW@ TMZ 5765', 'sau ทามุซ ๕๗๖๕'],
            ['BEF @#DHEBREW@ TMZ 5765', 'trước ทามุซ ๕๗๖๕'],
            ['@#DHEBREW@ 15 AAV 5765', '๑๕ อัฟ ๕๗๖๕'],
            ['@#DHEBREW@ AAV 5765', 'อัฟ ๕๗๖๕'],
            ['ABT @#DHEBREW@ AAV 5765', 'khoảng อัฟ ๕๗๖๕'],
            ['FROM @#DHEBREW@ AAV 5765', 'từ อัฟ ๕๗๖๕'],
            ['AFT @#DHEBREW@ AAV 5765', 'sau อัฟ ๕๗๖๕'],
            ['BEF @#DHEBREW@ AAV 5765', 'trước อัฟ ๕๗๖๕'],
            ['@#DHEBREW@ 15 ELL 5765', '๑๕ เอลุล ๕๗๖๕'],
            ['@#DHEBREW@ ELL 5765', 'เอลุล ๕๗๖๕'],
            ['ABT @#DHEBREW@ ELL 5765', 'khoảng เอลุล ๕๗๖๕'],
            ['FROM @#DHEBREW@ ELL 5765', 'từ เอลุล ๕๗๖๕'],
            ['AFT @#DHEBREW@ ELL 5765', 'sau เอลุล ๕๗๖๕'],
            ['BEF @#DHEBREW@ ELL 5765', 'trước เอลุล ๕๗๖๕'],
            ['@#DHEBREW@ 5765', '๕๗๖๕'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'khoảng ๑๕ ทิชเร ๕๗๖๕'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'được tính ๑๕ ทิชเร ๕๗๖๕'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'ước tính ๑๕ ทิชเร ๕๗๖๕'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'trước ๑๕ ทิชเร ๕๗๖๕'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'sau ๑๕ ทิชเร ๕๗๖๕'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'từ ๑๕ ทิชเร ๕๗๖๕'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'đến ๑๕ ทิชเร ๕๗๖๕'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'giữa ๑๕ ทิชเร ๕๗๖๕ và ๑๕ เฮชวาน ๕๗๖๕'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'từ ๑๕ ทิชเร ๕๗๖๕ đến ๑๕ เฮชวาน ๕๗๖๕'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'giải thích là ๑๕ ทิชเร ๕๗๖๕'],
            ['@#DFRENCH R@ 15 VEND 12', '๑๕ วองเดมีแยร์ An XII'],
            ['@#DFRENCH R@ VEND 12', 'วองเดมีแยร์ An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'khoảng วองเดมีแยร์ An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'từ วองเดมีแยร์ An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'sau วองเดมีแยร์ An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'trước วองเดมีแยร์ An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '๑๕ บรูแมร์ An XII'],
            ['@#DFRENCH R@ BRUM 12', 'บรูแมร์ An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'khoảng บรูแมร์ An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'từ บรูแมร์ An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'sau บรูแมร์ An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'trước บรูแมร์ An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '๑๕ ฟรีแมร์ An XII'],
            ['@#DFRENCH R@ FRIM 12', 'ฟรีแมร์ An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'khoảng ฟรีแมร์ An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'từ ฟรีแมร์ An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'sau ฟรีแมร์ An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'trước ฟรีแมร์ An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '๑๕ นีโวส An XII'],
            ['@#DFRENCH R@ NIVO 12', 'นีโวส An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'khoảng นีโวส An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'từ นีโวส An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'sau นีโวส An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'trước นีโวส An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '๑๕ พลูวีโอส An XII'],
            ['@#DFRENCH R@ PLUV 12', 'พลูวีโอส An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'khoảng พลูวีโอส An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'từ พลูวีโอส An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'sau พลูวีโอส An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'trước พลูวีโอส An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '๑๕ วองโตส An XII'],
            ['@#DFRENCH R@ VENT 12', 'วองโตส An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'khoảng วองโตส An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'từ วองโตส An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'sau วองโตส An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'trước วองโตส An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '๑๕ แฌร์มีนาล An XII'],
            ['@#DFRENCH R@ GERM 12', 'แฌร์มีนาล An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'khoảng แฌร์มีนาล An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'từ แฌร์มีนาล An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'sau แฌร์มีนาล An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'trước แฌร์มีนาล An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '๑๕ ฟลอเรอัล An XII'],
            ['@#DFRENCH R@ FLOR 12', 'ฟลอเรอัล An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'khoảng ฟลอเรอัล An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'từ ฟลอเรอัล An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'sau ฟลอเรอัล An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'trước ฟลอเรอัล An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '๑๕ แปรรีอัล An XII'],
            ['@#DFRENCH R@ PRAI 12', 'แปรรีอัล An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'khoảng แปรรีอัล An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'từ แปรรีอัล An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'sau แปรรีอัล An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'trước แปรรีอัล An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '๑๕ เมสซีดอร์ An XII'],
            ['@#DFRENCH R@ MESS 12', 'เมสซีดอร์ An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'khoảng เมสซีดอร์ An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'từ เมสซีดอร์ An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'sau เมสซีดอร์ An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'trước เมสซีดอร์ An XII'],
            ['@#DFRENCH R@ 15 THER 12', '๑๕ แตร์มีดอร์ An XII'],
            ['@#DFRENCH R@ THER 12', 'แตร์มีดอร์ An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'khoảng แตร์มีดอร์ An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'từ แตร์มีดอร์ An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'sau แตร์มีดอร์ An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'trước แตร์มีดอร์ An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '๑๕ ฟรุกตีดอร์ An XII'],
            ['@#DFRENCH R@ FRUC 12', 'ฟรุกตีดอร์ An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'khoảng ฟรุกตีดอร์ An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'từ ฟรุกตีดอร์ An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'sau ฟรุกตีดอร์ An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'trước ฟรุกตีดอร์ An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '๑๕ วันเสริม An XII'],
            ['@#DFRENCH R@ COMP 12', 'วันเสริม An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'khoảng วันเสริม An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'từ วันเสริม An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'sau วันเสริม An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'trước วันเสริม An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'khoảng ๑๕ วองเดมีแยร์ An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'được tính ๑๕ วองเดมีแยร์ An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'ước tính ๑๕ วองเดมีแยร์ An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'trước ๑๕ วองเดมีแยร์ An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'sau ๑๕ วองเดมีแยร์ An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'từ ๑๕ วองเดมีแยร์ An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'đến ๑๕ วองเดมีแยร์ An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'giữa ๑๕ วองเดมีแยร์ An XII và ๑๕ บรูแมร์ An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'từ ๑๕ วองเดมีแยร์ An XII đến ๑๕ บรูแมร์ An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'giải thích là ๑๕ วองเดมีแยร์ An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '๑๕ มุฮัรรอม ๑๔๒๕'],
            ['@#DHIJRI@ MUHAR 1425', 'มุฮัรรอม ๑๔๒๕'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'khoảng มุฮัรรอม ๑๔๒๕'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'từ มุฮัรรอม ๑๔๒๕'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'sau มุฮัรรอม ๑๔๒๕'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'trước มุฮัรรอม ๑๔๒๕'],
            ['@#DHIJRI@ 15 SAFAR 1425', '๑๕ ซอฟัร ๑๔๒๕'],
            ['@#DHIJRI@ SAFAR 1425', 'ซอฟัร ๑๔๒๕'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'khoảng ซอฟัร ๑๔๒๕'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'từ ซอฟัร ๑๔๒๕'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'sau ซอฟัร ๑๔๒๕'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'trước ซอฟัร ๑๔๒๕'],
            ['@#DHIJRI@ 15 RABIA 1425', '๑๕ รอบีอุลเอาวัล ๑๔๒๕'],
            ['@#DHIJRI@ RABIA 1425', 'รอบีอุลเอาวัล ๑๔๒๕'],
            ['ABT @#DHIJRI@ RABIA 1425', 'khoảng รอบีอุลเอาวัล ๑๔๒๕'],
            ['FROM @#DHIJRI@ RABIA 1425', 'từ รอบีอุลเอาวัล ๑๔๒๕'],
            ['AFT @#DHIJRI@ RABIA 1425', 'sau รอบีอุลเอาวัล ๑๔๒๕'],
            ['BEF @#DHIJRI@ RABIA 1425', 'trước รอบีอุลเอาวัล ๑๔๒๕'],
            ['@#DHIJRI@ 15 RABIT 1425', '๑๕ รอบีอุษษานี ๑๔๒๕'],
            ['@#DHIJRI@ RABIT 1425', 'รอบีอุษษานี ๑๔๒๕'],
            ['ABT @#DHIJRI@ RABIT 1425', 'khoảng รอบีอุษษานี ๑๔๒๕'],
            ['FROM @#DHIJRI@ RABIT 1425', 'từ รอบีอุษษานี ๑๔๒๕'],
            ['AFT @#DHIJRI@ RABIT 1425', 'sau รอบีอุษษานี ๑๔๒๕'],
            ['BEF @#DHIJRI@ RABIT 1425', 'trước รอบีอุษษานี ๑๔๒๕'],
            ['@#DHIJRI@ 15 JUMAA 1425', '๑๕ ญุมาดัลอูลา ๑๔๒๕'],
            ['@#DHIJRI@ JUMAA 1425', 'ญุมาดัลอูลา ๑๔๒๕'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'khoảng ญุมาดัลอูลา ๑๔๒๕'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'từ ญุมาดัลอูลา ๑๔๒๕'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'sau ญุมาดัลอูลา ๑๔๒๕'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'trước ญุมาดัลอูลา ๑๔๒๕'],
            ['@#DHIJRI@ 15 JUMAT 1425', '๑๕ ญุมาดัษษานียะฮ์ ๑๔๒๕'],
            ['@#DHIJRI@ JUMAT 1425', 'ญุมาดัษษานียะฮ์ ๑๔๒๕'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'khoảng ญุมาดัษษานียะฮ์ ๑๔๒๕'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'từ ญุมาดัษษานียะฮ์ ๑๔๒๕'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'sau ญุมาดัษษานียะฮ์ ๑๔๒๕'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'trước ญุมาดัษษานียะฮ์ ๑๔๒๕'],
            ['@#DHIJRI@ 15 RAJAB 1425', '๑๕ รอญับ ๑๔๒๕'],
            ['@#DHIJRI@ RAJAB 1425', 'รอญับ ๑๔๒๕'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'khoảng รอญับ ๑๔๒๕'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'từ รอญับ ๑๔๒๕'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'sau รอญับ ๑๔๒๕'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'trước รอญับ ๑๔๒๕'],
            ['@#DHIJRI@ 15 SHAAB 1425', '๑๕ ชะอ์บาน ๑๔๒๕'],
            ['@#DHIJRI@ SHAAB 1425', 'ชะอ์บาน ๑๔๒๕'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'khoảng ชะอ์บาน ๑๔๒๕'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'từ ชะอ์บาน ๑๔๒๕'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'sau ชะอ์บาน ๑๔๒๕'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'trước ชะอ์บาน ๑๔๒๕'],
            ['@#DHIJRI@ 15 RAMAD 1425', '๑๕ รอมะฎอน ๑๔๒๕'],
            ['@#DHIJRI@ RAMAD 1425', 'รอมะฎอน ๑๔๒๕'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'khoảng รอมะฎอน ๑๔๒๕'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'từ รอมะฎอน ๑๔๒๕'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'sau รอมะฎอน ๑๔๒๕'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'trước รอมะฎอน ๑๔๒๕'],
            ['@#DHIJRI@ 15 SHAWW 1425', '๑๕ เชาวาล ๑๔๒๕'],
            ['@#DHIJRI@ SHAWW 1425', 'เชาวาล ๑๔๒๕'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'khoảng เชาวาล ๑๔๒๕'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'từ เชาวาล ๑๔๒๕'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'sau เชาวาล ๑๔๒๕'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'trước เชาวาล ๑๔๒๕'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '๑๕ ซุลกิอ์ดะฮ์ ๑๔๒๕'],
            ['@#DHIJRI@ DHUAQ 1425', 'ซุลกิอ์ดะฮ์ ๑๔๒๕'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'khoảng ซุลกิอ์ดะฮ์ ๑๔๒๕'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'từ ซุลกิอ์ดะฮ์ ๑๔๒๕'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'sau ซุลกิอ์ดะฮ์ ๑๔๒๕'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'trước ซุลกิอ์ดะฮ์ ๑๔๒๕'],
            ['@#DHIJRI@ 15 DHUAL 1425', '๑๔๒๕'],
            ['@#DHIJRI@ DHUAL 1425', '๑๔๒๕'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'khoảng ๑๔๒๕'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'từ ๑๔๒๕'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'sau ๑๔๒๕'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'trước ๑๔๒๕'],
            ['@#DHIJRI@ 1425', '๑๔๒๕'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'khoảng ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'được tính ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'ước tính ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'trước ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'sau ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'từ ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'đến ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'giữa ๑๕ มุฮัรรอม ๑๔๒๕ và ๑๕ ซอฟัร ๑๔๒๕'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'từ ๑๕ มุฮัรรอม ๑๔๒๕ đến ๑๕ ซอฟัร ๑๔๒๕'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'giải thích là ๑๕ มุฮัรรอม ๑๔๒๕'],
            ['@#DJALALI@ 15 FARVA 1384', '๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['@#DJALALI@ FARVA 1384', 'ฟาร์วาร์ดิน ๑๓๘๔'],
            ['ABT @#DJALALI@ FARVA 1384', 'khoảng ฟาร์วาร์ดิน ๑๓๘๔'],
            ['FROM @#DJALALI@ FARVA 1384', 'từ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['AFT @#DJALALI@ FARVA 1384', 'sau ฟาร์วาร์ดิน ๑๓๘๔'],
            ['BEF @#DJALALI@ FARVA 1384', 'trước ฟาร์วาร์ดิน ๑๓๘๔'],
            ['@#DJALALI@ 15 ORDIB 1384', '๑๕ ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['@#DJALALI@ ORDIB 1384', 'ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['ABT @#DJALALI@ ORDIB 1384', 'khoảng ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['FROM @#DJALALI@ ORDIB 1384', 'từ ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['AFT @#DJALALI@ ORDIB 1384', 'sau ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['BEF @#DJALALI@ ORDIB 1384', 'trước ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['@#DJALALI@ 15 KHORD 1384', '๑๕ คอร์ดาด ๑๓๘๔'],
            ['@#DJALALI@ KHORD 1384', 'คอร์ดาด ๑๓๘๔'],
            ['ABT @#DJALALI@ KHORD 1384', 'khoảng คอร์ดาด ๑๓๘๔'],
            ['FROM @#DJALALI@ KHORD 1384', 'từ คอร์ดาด ๑๓๘๔'],
            ['AFT @#DJALALI@ KHORD 1384', 'sau คอร์ดาด ๑๓๘๔'],
            ['BEF @#DJALALI@ KHORD 1384', 'trước คอร์ดาด ๑๓๘๔'],
            ['@#DJALALI@ 15 TIR 1384', '๑๕ ตีร์ ๑๓๘๔'],
            ['@#DJALALI@ TIR 1384', 'ตีร์ ๑๓๘๔'],
            ['ABT @#DJALALI@ TIR 1384', 'khoảng ตีร์ ๑๓๘๔'],
            ['FROM @#DJALALI@ TIR 1384', 'từ ตีร์ ๑๓๘๔'],
            ['AFT @#DJALALI@ TIR 1384', 'sau ตีร์ ๑๓๘๔'],
            ['BEF @#DJALALI@ TIR 1384', 'trước ตีร์ ๑๓๘๔'],
            ['@#DJALALI@ 15 MORDA 1384', '๑๕ มอร์ดาด ๑๓๘๔'],
            ['@#DJALALI@ MORDA 1384', 'มอร์ดาด ๑๓๘๔'],
            ['ABT @#DJALALI@ MORDA 1384', 'khoảng มอร์ดาด ๑๓๘๔'],
            ['FROM @#DJALALI@ MORDA 1384', 'từ มอร์ดาด ๑๓๘๔'],
            ['AFT @#DJALALI@ MORDA 1384', 'sau มอร์ดาด ๑๓๘๔'],
            ['BEF @#DJALALI@ MORDA 1384', 'trước มอร์ดาด ๑๓๘๔'],
            ['@#DJALALI@ 15 SHAHR 1384', '๑๕ ชาห์ริวาร์ ๑๓๘๔'],
            ['@#DJALALI@ SHAHR 1384', 'ชาห์ริวาร์ ๑๓๘๔'],
            ['ABT @#DJALALI@ SHAHR 1384', 'khoảng ชาห์ริวาร์ ๑๓๘๔'],
            ['FROM @#DJALALI@ SHAHR 1384', 'từ ชาห์ริวาร์ ๑๓๘๔'],
            ['AFT @#DJALALI@ SHAHR 1384', 'sau ชาห์ริวาร์ ๑๓๘๔'],
            ['BEF @#DJALALI@ SHAHR 1384', 'trước ชาห์ริวาร์ ๑๓๘๔'],
            ['@#DJALALI@ 15 MEHR 1384', '๑๕ เมห์ร ๑๓๘๔'],
            ['@#DJALALI@ MEHR 1384', 'เมห์ร ๑๓๘๔'],
            ['ABT @#DJALALI@ MEHR 1384', 'khoảng เมห์ร ๑๓๘๔'],
            ['FROM @#DJALALI@ MEHR 1384', 'từ เมห์ร ๑๓๘๔'],
            ['AFT @#DJALALI@ MEHR 1384', 'sau เมห์ร ๑๓๘๔'],
            ['BEF @#DJALALI@ MEHR 1384', 'trước เมห์ร ๑๓๘๔'],
            ['@#DJALALI@ 15 ABAN 1384', '๑๕ อาบาน ๑๓๘๔'],
            ['@#DJALALI@ ABAN 1384', 'อาบาน ๑๓๘๔'],
            ['ABT @#DJALALI@ ABAN 1384', 'khoảng อาบาน ๑๓๘๔'],
            ['FROM @#DJALALI@ ABAN 1384', 'từ อาบาน ๑๓๘๔'],
            ['AFT @#DJALALI@ ABAN 1384', 'sau อาบาน ๑๓๘๔'],
            ['BEF @#DJALALI@ ABAN 1384', 'trước อาบาน ๑๓๘๔'],
            ['@#DJALALI@ 15 AZAR 1384', '๑๕ อาซาร์ ๑๓๘๔'],
            ['@#DJALALI@ AZAR 1384', 'อาซาร์ ๑๓๘๔'],
            ['ABT @#DJALALI@ AZAR 1384', 'khoảng อาซาร์ ๑๓๘๔'],
            ['FROM @#DJALALI@ AZAR 1384', 'từ อาซาร์ ๑๓๘๔'],
            ['AFT @#DJALALI@ AZAR 1384', 'sau อาซาร์ ๑๓๘๔'],
            ['BEF @#DJALALI@ AZAR 1384', 'trước อาซาร์ ๑๓๘๔'],
            ['@#DJALALI@ 15 DEY 1384', '๑๕ เดย์ ๑๓๘๔'],
            ['@#DJALALI@ DEY 1384', 'เดย์ ๑๓๘๔'],
            ['ABT @#DJALALI@ DEY 1384', 'khoảng เดย์ ๑๓๘๔'],
            ['FROM @#DJALALI@ DEY 1384', 'từ เดย์ ๑๓๘๔'],
            ['AFT @#DJALALI@ DEY 1384', 'sau เดย์ ๑๓๘๔'],
            ['BEF @#DJALALI@ DEY 1384', 'trước เดย์ ๑๓๘๔'],
            ['@#DJALALI@ 15 BAHMA 1384', '๑๕ บาห์มัน ๑๓๘๔'],
            ['@#DJALALI@ BAHMA 1384', 'บาห์มัน ๑๓๘๔'],
            ['ABT @#DJALALI@ BAHMA 1384', 'khoảng บาห์มัน ๑๓๘๔'],
            ['FROM @#DJALALI@ BAHMA 1384', 'từ บาห์มัน ๑๓๘๔'],
            ['AFT @#DJALALI@ BAHMA 1384', 'sau บาห์มัน ๑๓๘๔'],
            ['BEF @#DJALALI@ BAHMA 1384', 'trước บาห์มัน ๑๓๘๔'],
            ['@#DJALALI@ 15 ESFAN 1384', '๑๕ เอสฟานด์ ๑๓๘๔'],
            ['@#DJALALI@ ESFAN 1384', 'เอสฟานด์ ๑๓๘๔'],
            ['ABT @#DJALALI@ ESFAN 1384', 'khoảng เอสฟานด์ ๑๓๘๔'],
            ['FROM @#DJALALI@ ESFAN 1384', 'từ เอสฟานด์ ๑๓๘๔'],
            ['AFT @#DJALALI@ ESFAN 1384', 'sau เอสฟานด์ ๑๓๘๔'],
            ['BEF @#DJALALI@ ESFAN 1384', 'trước เอสฟานด์ ๑๓๘๔'],
            ['@#DJALALI@ 1384', '๑๓๘๔'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'khoảng ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'được tính ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'ước tính ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'trước ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'sau ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'từ ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'đến ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'giữa ๑๕ ฟาร์วาร์ดิน ๑๓๘๔ và ๑๕ ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'từ ๑๕ ฟาร์วาร์ดิน ๑๓๘๔ đến ๑๕ ออร์ดิเบเฮชต์ ๑๓๘๔'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'giải thích là ๑๕ ฟาร์วาร์ดิน ๑๓๘๔'],
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
        self::assertSame('one และtwo', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two และthree', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one หรือtwo', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two หรือthree', $language->formatListOr(['one', 'two', 'three']));
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

        // Cousins
        $cousinM = self::male('cmpu', "1 FAMC @fbro@");

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
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cmpu@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro,
             $cousinM,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('ภรรยา', 'สามี', [$husband, $fm, $wife]);
        self::assertRelationshipNames('อดีตสามี', 'อดีตภรรยา', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('คู่หมั้นหญิง', 'คู่หมั้นชาย', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('แม่', 'ลูกชาย', [$son, $fm, $wife]);
        self::assertRelationshipNames('พ่อ', 'ลูกชาย', [$son, $fm, $husband]);
        self::assertRelationshipNames('แม่', 'ลูกสาว', [$daughter, $fm, $wife]);

        // Adopted / Fostered
        self::assertRelationshipNames('แม่บุญธรรม', 'ลูกชายบุญธรรม', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('แม่อุปถัมภ์', 'ลูกสาวอุปถัมภ์', [$fosterDaughter, $fd, $wife]);

        // Siblings — elder/younger (daughter born 1998 is older than son born 2000)
        self::assertRelationshipNames('พี่สาว', 'น้องชาย', [$son, $fm, $daughter]);
        self::assertRelationshipNames('น้องชาย', 'พี่สาว', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('พ่อเลี้ยง', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('ลูกเลี้ยงหญิง', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('แม่ยาย', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('พ่อตา', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('แม่สามี', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('พ่อสามี', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('ลูกสะใภ้', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('ลูกเขย', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('พี่น้องภรรยา', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('พี่น้องภรรยา', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents — paternal
        self::assertRelationshipName('ย่า', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('ปู่', [$son, $fm, $husband, $fp, $fatherOfH]);
        // Grandparents — maternal
        self::assertRelationshipName('ยาย', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('ตา', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('หลานชาย', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('หลานสาว', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts/Uncles — paternal (อา)
        self::assertRelationshipName('อา', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('อา', [$son, $fm, $husband, $fp, $brotherOfH]);
        // Aunts/Uncles — maternal (น้า)
        self::assertRelationshipName('น้า', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('น้า', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces/Nephews
        self::assertRelationshipName('หลานสาว', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('หลานชาย', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('ลูกพี่ลูกน้อง', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinM]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('ทวดหญิง', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('ทวดชาย', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('ป้าชั้นที่ 2', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('ลุงชั้นที่ 2', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
