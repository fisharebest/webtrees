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
use Fisharebest\Webtrees\I18N\Languages\Korean;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Korean::class)]
class KoreanTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Korean();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Kore, self::language()->script());
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
        self::assertSame('ko', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('한국어', self::language()->endonym());
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
            ['15 JAN 2000', '15 1월 2000'],
            ['JAN 2000', '1월 2000'],
            ['ABT JAN 2000', '약 1월 2000'],
            ['FROM JAN 2000', '1월 2000 에서'],
            ['AFT JAN 2000', '1월 2000 이후'],
            ['BEF JAN 2000', '1월 2000 이전'],
            ['15 FEB 2000', '15 2월 2000'],
            ['FEB 2000', '2월 2000'],
            ['ABT FEB 2000', '약 2월 2000'],
            ['FROM FEB 2000', '2월 2000 에서'],
            ['AFT FEB 2000', '2월 2000 이후'],
            ['BEF FEB 2000', '2월 2000 이전'],
            ['15 MAR 2000', '15 3월 2000'],
            ['MAR 2000', '3월 2000'],
            ['ABT MAR 2000', '약 3월 2000'],
            ['FROM MAR 2000', '3월 2000 에서'],
            ['AFT MAR 2000', '3월 2000 이후'],
            ['BEF MAR 2000', '3월 2000 이전'],
            ['15 APR 2000', '15 4월 2000'],
            ['APR 2000', '4월 2000'],
            ['ABT APR 2000', '약 4월 2000'],
            ['FROM APR 2000', '4월 2000 에서'],
            ['AFT APR 2000', '4월 2000 이후'],
            ['BEF APR 2000', '4월 2000 이전'],
            ['15 MAY 2000', '15 5월 2000'],
            ['MAY 2000', '5월 2000'],
            ['ABT MAY 2000', '약 5월 2000'],
            ['FROM MAY 2000', '5월 2000 에서'],
            ['AFT MAY 2000', '5월 2000 이후'],
            ['BEF MAY 2000', '5월 2000 이전'],
            ['15 JUN 2000', '15 6월 2000'],
            ['JUN 2000', '6월 2000'],
            ['ABT JUN 2000', '약 6월 2000'],
            ['FROM JUN 2000', '6월 2000 에서'],
            ['AFT JUN 2000', '6월 2000 이후'],
            ['BEF JUN 2000', '6월 2000 이전'],
            ['15 JUL 2000', '15 7월 2000'],
            ['JUL 2000', '7월 2000'],
            ['ABT JUL 2000', '약 7월 2000'],
            ['FROM JUL 2000', '7월 2000 에서'],
            ['AFT JUL 2000', '7월 2000 이후'],
            ['BEF JUL 2000', '7월 2000 이전'],
            ['15 AUG 2000', '15 8월 2000'],
            ['AUG 2000', '8월 2000'],
            ['ABT AUG 2000', '약 8월 2000'],
            ['FROM AUG 2000', '8월 2000 에서'],
            ['AFT AUG 2000', '8월 2000 이후'],
            ['BEF AUG 2000', '8월 2000 이전'],
            ['15 SEP 2000', '15 9월 2000'],
            ['SEP 2000', '9월 2000'],
            ['ABT SEP 2000', '약 9월 2000'],
            ['FROM SEP 2000', '9월 2000 에서'],
            ['AFT SEP 2000', '9월 2000 이후'],
            ['BEF SEP 2000', '9월 2000 이전'],
            ['15 OCT 2000', '15 10월 2000'],
            ['OCT 2000', '10월 2000'],
            ['ABT OCT 2000', '약 10월 2000'],
            ['FROM OCT 2000', '10월 2000 에서'],
            ['AFT OCT 2000', '10월 2000 이후'],
            ['BEF OCT 2000', '10월 2000 이전'],
            ['15 NOV 2000', '15 11월 2000'],
            ['NOV 2000', '11월 2000'],
            ['ABT NOV 2000', '약 11월 2000'],
            ['FROM NOV 2000', '11월 2000 에서'],
            ['AFT NOV 2000', '11월 2000 이후'],
            ['BEF NOV 2000', '11월 2000 이전'],
            ['15 DEC 2000', '15 12월 2000'],
            ['DEC 2000', '12월 2000'],
            ['ABT DEC 2000', '약 12월 2000'],
            ['FROM DEC 2000', '12월 2000 에서'],
            ['AFT DEC 2000', '12월 2000 이후'],
            ['BEF DEC 2000', '12월 2000 이전'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', '약 15 1월 2000'],
            ['CAL 15 JAN 2000', '계산 된 15 1월 2000'],
            ['EST 15 JAN 2000', '예상 15 1월 2000'],
            ['BEF 15 JAN 2000', '15 1월 2000 이전'],
            ['AFT 15 JAN 2000', '15 1월 2000 이후'],
            ['FROM 15 JAN 2000', '15 1월 2000 에서'],
            ['TO 15 JAN 2000', '15 1월 2000까지'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '15 1월 2000와 15 2월 2000 사이'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '15 1월 2000에서 15 2월 2000까지'],
            ['INT 15 JAN 2000', '설명 15 1월 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 1월 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', '1월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', '약 1월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', '1월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ JAN 1700', '1월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ JAN 1700', '1월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 FEB 1700', '15 2월 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', '2월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', '약 2월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', '2월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ FEB 1700', '2월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ FEB 1700', '2월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 MAR 1700', '15 3월 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', '3월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', '약 3월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', '3월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ MAR 1700', '3월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ MAR 1700', '3월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 APR 1700', '15 4월 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 4월 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', '4월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', '약 4월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', '4월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ APR 1700', '4월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ APR 1700', '4월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 MAY 1700', '15 5월 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', '5월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', '약 5월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', '5월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ MAY 1700', '5월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ MAY 1700', '5월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 JUN 1700', '15 6월 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', '6월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', '약 6월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', '6월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ JUN 1700', '6월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ JUN 1700', '6월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 JUL 1700', '15 7월 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', '7월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', '약 7월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', '7월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ JUL 1700', '7월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ JUL 1700', '7월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 AUG 1700', '15 8월 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', '8월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', '약 8월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', '8월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ AUG 1700', '8월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ AUG 1700', '8월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 SEP 1700', '15 9월 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', '9월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', '약 9월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', '9월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ SEP 1700', '9월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ SEP 1700', '9월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 OCT 1700', '15 10월 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', '10월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', '약 10월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', '10월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ OCT 1700', '10월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ OCT 1700', '10월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 NOV 1700', '15 11월 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', '11월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', '약 11월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', '11월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ NOV 1700', '11월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ NOV 1700', '11월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 15 DEC 1700', '15 12월 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', '12월 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', '약 12월 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', '12월 1700 ᴄᴇ 에서'],
            ['AFT @#DJULIAN@ DEC 1700', '12월 1700 ᴄᴇ 이후'],
            ['BEF @#DJULIAN@ DEC 1700', '12월 1700 ᴄᴇ 이전'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', '약 15 1월 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', '계산 된 15 1월 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', '예상 15 1월 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', '15 1월 1700 ᴄᴇ 이전'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '15 1월 1700 ᴄᴇ 이후'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '15 1월 1700 ᴄᴇ 에서'],
            ['TO @#DJULIAN@ 15 JAN 1700', '15 1월 1700 ᴄᴇ까지'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '15 1월 1700 ᴄᴇ와 15 2월 1700 ᴄᴇ 사이'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '15 1월 1700 ᴄᴇ에서 15 2월 1700 ᴄᴇ까지'],
            ['INT @#DJULIAN@ 15 JAN 1700', '설명 15 1월 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 티슈레이 5765'],
            ['@#DHEBREW@ TSH 5765', '티슈레이 5765'],
            ['ABT @#DHEBREW@ TSH 5765', '약 티슈레이 5765'],
            ['FROM @#DHEBREW@ TSH 5765', '티슈레이 5765 에서'],
            ['AFT @#DHEBREW@ TSH 5765', '티슈레이 5765 이후'],
            ['BEF @#DHEBREW@ TSH 5765', '티슈레이 5765 이전'],
            ['@#DHEBREW@ 15 CSH 5765', '15 헤시반 5765'],
            ['@#DHEBREW@ CSH 5765', '헤시반 5765'],
            ['ABT @#DHEBREW@ CSH 5765', '약 헤시반 5765'],
            ['FROM @#DHEBREW@ CSH 5765', '헤시반 5765 에서'],
            ['AFT @#DHEBREW@ CSH 5765', '헤시반 5765 이후'],
            ['BEF @#DHEBREW@ CSH 5765', '헤시반 5765 이전'],
            ['@#DHEBREW@ 15 KSL 5765', '15 키슬레브 5765'],
            ['@#DHEBREW@ KSL 5765', '키슬레브 5765'],
            ['ABT @#DHEBREW@ KSL 5765', '약 키슬레브 5765'],
            ['FROM @#DHEBREW@ KSL 5765', '키슬레브 5765 에서'],
            ['AFT @#DHEBREW@ KSL 5765', '키슬레브 5765 이후'],
            ['BEF @#DHEBREW@ KSL 5765', '키슬레브 5765 이전'],
            ['@#DHEBREW@ 15 TVT 5765', '15 테벳 5765'],
            ['@#DHEBREW@ TVT 5765', '테벳 5765'],
            ['ABT @#DHEBREW@ TVT 5765', '약 테벳 5765'],
            ['FROM @#DHEBREW@ TVT 5765', '테벳 5765 에서'],
            ['AFT @#DHEBREW@ TVT 5765', '테벳 5765 이후'],
            ['BEF @#DHEBREW@ TVT 5765', '테벳 5765 이전'],
            ['@#DHEBREW@ 15 SHV 5765', '15 슈밧 5765'],
            ['@#DHEBREW@ SHV 5765', '슈밧 5765'],
            ['ABT @#DHEBREW@ SHV 5765', '약 슈밧 5765'],
            ['FROM @#DHEBREW@ SHV 5765', '슈밧 5765 에서'],
            ['AFT @#DHEBREW@ SHV 5765', '슈밧 5765 이후'],
            ['BEF @#DHEBREW@ SHV 5765', '슈밧 5765 이전'],
            ['@#DHEBREW@ 15 ADR 5765', '15 아다르 I 5765'],
            ['@#DHEBREW@ ADR 5765', '아다르 I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', '약 아다르 I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', '아다르 I 5765 에서'],
            ['AFT @#DHEBREW@ ADR 5765', '아다르 I 5765 이후'],
            ['BEF @#DHEBREW@ ADR 5765', '아다르 I 5765 이전'],
            ['@#DHEBREW@ 15 ADS 5765', '15 아다르 II 5765'],
            ['@#DHEBREW@ ADS 5765', '아다르 II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', '약 아다르 II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', '아다르 II 5765 에서'],
            ['AFT @#DHEBREW@ ADS 5765', '아다르 II 5765 이후'],
            ['BEF @#DHEBREW@ ADS 5765', '아다르 II 5765 이전'],
            ['@#DHEBREW@ 15 NSN 5765', '15 니산 5765'],
            ['@#DHEBREW@ NSN 5765', '니산 5765'],
            ['ABT @#DHEBREW@ NSN 5765', '약 니산 5765'],
            ['FROM @#DHEBREW@ NSN 5765', '니산 5765 에서'],
            ['AFT @#DHEBREW@ NSN 5765', '니산 5765 이후'],
            ['BEF @#DHEBREW@ NSN 5765', '니산 5765 이전'],
            ['@#DHEBREW@ 15 IYR 5765', '15 이야르 5765'],
            ['@#DHEBREW@ IYR 5765', '이야르 5765'],
            ['ABT @#DHEBREW@ IYR 5765', '약 이야르 5765'],
            ['FROM @#DHEBREW@ IYR 5765', '이야르 5765 에서'],
            ['AFT @#DHEBREW@ IYR 5765', '이야르 5765 이후'],
            ['BEF @#DHEBREW@ IYR 5765', '이야르 5765 이전'],
            ['@#DHEBREW@ 15 SVN 5765', '15 시반 5765'],
            ['@#DHEBREW@ SVN 5765', '시반 5765'],
            ['ABT @#DHEBREW@ SVN 5765', '약 시반 5765'],
            ['FROM @#DHEBREW@ SVN 5765', '시반 5765 에서'],
            ['AFT @#DHEBREW@ SVN 5765', '시반 5765 이후'],
            ['BEF @#DHEBREW@ SVN 5765', '시반 5765 이전'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 타무즈 5765'],
            ['@#DHEBREW@ TMZ 5765', '타무즈 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', '약 타무즈 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', '타무즈 5765 에서'],
            ['AFT @#DHEBREW@ TMZ 5765', '타무즈 5765 이후'],
            ['BEF @#DHEBREW@ TMZ 5765', '타무즈 5765 이전'],
            ['@#DHEBREW@ 15 AAV 5765', '15 아브 5765'],
            ['@#DHEBREW@ AAV 5765', '아브 5765'],
            ['ABT @#DHEBREW@ AAV 5765', '약 아브 5765'],
            ['FROM @#DHEBREW@ AAV 5765', '아브 5765 에서'],
            ['AFT @#DHEBREW@ AAV 5765', '아브 5765 이후'],
            ['BEF @#DHEBREW@ AAV 5765', '아브 5765 이전'],
            ['@#DHEBREW@ 15 ELL 5765', '15 엘룰 5765'],
            ['@#DHEBREW@ ELL 5765', '엘룰 5765'],
            ['ABT @#DHEBREW@ ELL 5765', '약 엘룰 5765'],
            ['FROM @#DHEBREW@ ELL 5765', '엘룰 5765 에서'],
            ['AFT @#DHEBREW@ ELL 5765', '엘룰 5765 이후'],
            ['BEF @#DHEBREW@ ELL 5765', '엘룰 5765 이전'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', '약 15 티슈레이 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', '계산 된 15 티슈레이 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', '예상 15 티슈레이 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '15 티슈레이 5765 이전'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '15 티슈레이 5765 이후'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '15 티슈레이 5765 에서'],
            ['TO @#DHEBREW@ 15 TSH 5765', '15 티슈레이 5765까지'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '15 티슈레이 5765와 15 헤시반 5765 사이'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '15 티슈레이 5765에서 15 헤시반 5765까지'],
            ['INT @#DHEBREW@ 15 TSH 5765', '설명 15 티슈레이 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 방데미에르 An XII'],
            ['@#DFRENCH R@ VEND 12', '방데미에르 An XII'],
            ['ABT @#DFRENCH R@ VEND 12', '약 방데미에르 An XII'],
            ['FROM @#DFRENCH R@ VEND 12', '방데미에르 An XII 에서'],
            ['AFT @#DFRENCH R@ VEND 12', '방데미에르 An XII 이후'],
            ['BEF @#DFRENCH R@ VEND 12', '방데미에르 An XII 이전'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 브뤼메르 An XII'],
            ['@#DFRENCH R@ BRUM 12', '브뤼메르 An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', '약 브뤼메르 An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', '브뤼메르 An XII 에서'],
            ['AFT @#DFRENCH R@ BRUM 12', '브뤼메르 An XII 이후'],
            ['BEF @#DFRENCH R@ BRUM 12', '브뤼메르 An XII 이전'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 프리메르 An XII'],
            ['@#DFRENCH R@ FRIM 12', '프리메르 An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', '약 프리메르 An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', '프리메르 An XII 에서'],
            ['AFT @#DFRENCH R@ FRIM 12', '프리메르 An XII 이후'],
            ['BEF @#DFRENCH R@ FRIM 12', '프리메르 An XII 이전'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 니보스 An XII'],
            ['@#DFRENCH R@ NIVO 12', '니보스 An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', '약 니보스 An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', '니보스 An XII 에서'],
            ['AFT @#DFRENCH R@ NIVO 12', '니보스 An XII 이후'],
            ['BEF @#DFRENCH R@ NIVO 12', '니보스 An XII 이전'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 플뤼비오스 An XII'],
            ['@#DFRENCH R@ PLUV 12', '플뤼비오스 An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', '약 플뤼비오스 An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', '플뤼비오스 An XII 에서'],
            ['AFT @#DFRENCH R@ PLUV 12', '플뤼비오스 An XII 이후'],
            ['BEF @#DFRENCH R@ PLUV 12', '플뤼비오스 An XII 이전'],
            ['@#DFRENCH R@ 15 VENT 12', '15 방토스 An XII'],
            ['@#DFRENCH R@ VENT 12', '방토스 An XII'],
            ['ABT @#DFRENCH R@ VENT 12', '약 방토스 An XII'],
            ['FROM @#DFRENCH R@ VENT 12', '방토스 An XII 에서'],
            ['AFT @#DFRENCH R@ VENT 12', '방토스 An XII 이후'],
            ['BEF @#DFRENCH R@ VENT 12', '방토스 An XII 이전'],
            ['@#DFRENCH R@ 15 GERM 12', '15 제르미날 An XII'],
            ['@#DFRENCH R@ GERM 12', '제르미날 An XII'],
            ['ABT @#DFRENCH R@ GERM 12', '약 제르미날 An XII'],
            ['FROM @#DFRENCH R@ GERM 12', '제르미날 An XII 에서'],
            ['AFT @#DFRENCH R@ GERM 12', '제르미날 An XII 이후'],
            ['BEF @#DFRENCH R@ GERM 12', '제르미날 An XII 이전'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 플로레알 An XII'],
            ['@#DFRENCH R@ FLOR 12', '플로레알 An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', '약 플로레알 An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', '플로레알 An XII 에서'],
            ['AFT @#DFRENCH R@ FLOR 12', '플로레알 An XII 이후'],
            ['BEF @#DFRENCH R@ FLOR 12', '플로레알 An XII 이전'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 프레리알 An XII'],
            ['@#DFRENCH R@ PRAI 12', '프레리알 An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', '약 프레리알 An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', '프레리알 An XII 에서'],
            ['AFT @#DFRENCH R@ PRAI 12', '프레리알 An XII 이후'],
            ['BEF @#DFRENCH R@ PRAI 12', '프레리알 An XII 이전'],
            ['@#DFRENCH R@ 15 MESS 12', '15 메시도르 An XII'],
            ['@#DFRENCH R@ MESS 12', '메시도르 An XII'],
            ['ABT @#DFRENCH R@ MESS 12', '약 메시도르 An XII'],
            ['FROM @#DFRENCH R@ MESS 12', '메시도르 An XII 에서'],
            ['AFT @#DFRENCH R@ MESS 12', '메시도르 An XII 이후'],
            ['BEF @#DFRENCH R@ MESS 12', '메시도르 An XII 이전'],
            ['@#DFRENCH R@ 15 THER 12', '15 테르미도르 An XII'],
            ['@#DFRENCH R@ THER 12', '테르미도르 An XII'],
            ['ABT @#DFRENCH R@ THER 12', '약 테르미도르 An XII'],
            ['FROM @#DFRENCH R@ THER 12', '테르미도르 An XII 에서'],
            ['AFT @#DFRENCH R@ THER 12', '테르미도르 An XII 이후'],
            ['BEF @#DFRENCH R@ THER 12', '테르미도르 An XII 이전'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 프뤽티도르 An XII'],
            ['@#DFRENCH R@ FRUC 12', '프뤽티도르 An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', '약 프뤽티도르 An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', '프뤽티도르 An XII 에서'],
            ['AFT @#DFRENCH R@ FRUC 12', '프뤽티도르 An XII 이후'],
            ['BEF @#DFRENCH R@ FRUC 12', '프뤽티도르 An XII 이전'],
            ['@#DFRENCH R@ 15 COMP 12', '15 보충일 An XII'],
            ['@#DFRENCH R@ COMP 12', '보충일 An XII'],
            ['ABT @#DFRENCH R@ COMP 12', '약 보충일 An XII'],
            ['FROM @#DFRENCH R@ COMP 12', '보충일 An XII 에서'],
            ['AFT @#DFRENCH R@ COMP 12', '보충일 An XII 이후'],
            ['BEF @#DFRENCH R@ COMP 12', '보충일 An XII 이전'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', '약 15 방데미에르 An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', '계산 된 15 방데미에르 An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', '예상 15 방데미에르 An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', '15 방데미에르 An XII 이전'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '15 방데미에르 An XII 이후'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '15 방데미에르 An XII 에서'],
            ['TO @#DFRENCH R@ 15 VEND 12', '15 방데미에르 An XII까지'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '15 방데미에르 An XII와 15 브뤼메르 An XII 사이'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '15 방데미에르 An XII에서 15 브뤼메르 An XII까지'],
            ['INT @#DFRENCH R@ 15 VEND 12', '설명 15 방데미에르 An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 무하람 1425'],
            ['@#DHIJRI@ MUHAR 1425', '무하람 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', '약 무하람 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', '무하람 1425 에서'],
            ['AFT @#DHIJRI@ MUHAR 1425', '무하람 1425 이후'],
            ['BEF @#DHIJRI@ MUHAR 1425', '무하람 1425 이전'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 사파르 1425'],
            ['@#DHIJRI@ SAFAR 1425', '사파르 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', '약 사파르 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', '사파르 1425 에서'],
            ['AFT @#DHIJRI@ SAFAR 1425', '사파르 1425 이후'],
            ['BEF @#DHIJRI@ SAFAR 1425', '사파르 1425 이전'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 라비 알아왈 1425'],
            ['@#DHIJRI@ RABIA 1425', '라비 알아왈 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', '약 라비 알아왈 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', '라비 알아왈 1425 에서'],
            ['AFT @#DHIJRI@ RABIA 1425', '라비 알아왈 1425 이후'],
            ['BEF @#DHIJRI@ RABIA 1425', '라비 알아왈 1425 이전'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 라비 앗사니 1425'],
            ['@#DHIJRI@ RABIT 1425', '라비 앗사니 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', '약 라비 앗사니 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', '라비 앗사니 1425 에서'],
            ['AFT @#DHIJRI@ RABIT 1425', '라비 앗사니 1425 이후'],
            ['BEF @#DHIJRI@ RABIT 1425', '라비 앗사니 1425 이전'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 주마다 알울라 1425'],
            ['@#DHIJRI@ JUMAA 1425', '주마다 알울라 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', '약 주마다 알울라 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', '주마다 알울라 1425 에서'],
            ['AFT @#DHIJRI@ JUMAA 1425', '주마다 알울라 1425 이후'],
            ['BEF @#DHIJRI@ JUMAA 1425', '주마다 알울라 1425 이전'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 주마다 앗사니야 1425'],
            ['@#DHIJRI@ JUMAT 1425', '주마다 앗사니야 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', '약 주마다 앗사니야 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', '주마다 앗사니야 1425 에서'],
            ['AFT @#DHIJRI@ JUMAT 1425', '주마다 앗사니야 1425 이후'],
            ['BEF @#DHIJRI@ JUMAT 1425', '주마다 앗사니야 1425 이전'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 라자브 1425'],
            ['@#DHIJRI@ RAJAB 1425', '라자브 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', '약 라자브 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', '라자브 1425 에서'],
            ['AFT @#DHIJRI@ RAJAB 1425', '라자브 1425 이후'],
            ['BEF @#DHIJRI@ RAJAB 1425', '라자브 1425 이전'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 샤반 1425'],
            ['@#DHIJRI@ SHAAB 1425', '샤반 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', '약 샤반 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', '샤반 1425 에서'],
            ['AFT @#DHIJRI@ SHAAB 1425', '샤반 1425 이후'],
            ['BEF @#DHIJRI@ SHAAB 1425', '샤반 1425 이전'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 라마단 1425'],
            ['@#DHIJRI@ RAMAD 1425', '라마단 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', '약 라마단 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', '라마단 1425 에서'],
            ['AFT @#DHIJRI@ RAMAD 1425', '라마단 1425 이후'],
            ['BEF @#DHIJRI@ RAMAD 1425', '라마단 1425 이전'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 샤왈 1425'],
            ['@#DHIJRI@ SHAWW 1425', '샤왈 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', '약 샤왈 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', '샤왈 1425 에서'],
            ['AFT @#DHIJRI@ SHAWW 1425', '샤왈 1425 이후'],
            ['BEF @#DHIJRI@ SHAWW 1425', '샤왈 1425 이전'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 둘 카다 1425'],
            ['@#DHIJRI@ DHUAQ 1425', '둘 카다 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', '약 둘 카다 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', '둘 카다 1425 에서'],
            ['AFT @#DHIJRI@ DHUAQ 1425', '둘 카다 1425 이후'],
            ['BEF @#DHIJRI@ DHUAQ 1425', '둘 카다 1425 이전'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', '약 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', '1425 에서'],
            ['AFT @#DHIJRI@ DHUAL 1425', '1425 이후'],
            ['BEF @#DHIJRI@ DHUAL 1425', '1425 이전'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', '약 15 무하람 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', '계산 된 15 무하람 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', '예상 15 무하람 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '15 무하람 1425 이전'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '15 무하람 1425 이후'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '15 무하람 1425 에서'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '15 무하람 1425까지'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '15 무하람 1425와 15 사파르 1425 사이'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '15 무하람 1425에서 15 사파르 1425까지'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', '설명 15 무하람 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 파르바르딘 1384'],
            ['@#DJALALI@ FARVA 1384', '파르바르딘 1384'],
            ['ABT @#DJALALI@ FARVA 1384', '약 파르바르딘 1384'],
            ['FROM @#DJALALI@ FARVA 1384', '파르바르딘 1384 에서'],
            ['AFT @#DJALALI@ FARVA 1384', '파르바르딘 1384 이후'],
            ['BEF @#DJALALI@ FARVA 1384', '파르바르딘 1384 이전'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 오르디베헤시트 1384'],
            ['@#DJALALI@ ORDIB 1384', '오르디베헤시트 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', '약 오르디베헤시트 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', '오르디베헤시트 1384 에서'],
            ['AFT @#DJALALI@ ORDIB 1384', '오르디베헤시트 1384 이후'],
            ['BEF @#DJALALI@ ORDIB 1384', '오르디베헤시트 1384 이전'],
            ['@#DJALALI@ 15 KHORD 1384', '15 호르다드 1384'],
            ['@#DJALALI@ KHORD 1384', '호르다드 1384'],
            ['ABT @#DJALALI@ KHORD 1384', '약 호르다드 1384'],
            ['FROM @#DJALALI@ KHORD 1384', '호르다드 1384 에서'],
            ['AFT @#DJALALI@ KHORD 1384', '호르다드 1384 이후'],
            ['BEF @#DJALALI@ KHORD 1384', '호르다드 1384 이전'],
            ['@#DJALALI@ 15 TIR 1384', '15 티르 1384'],
            ['@#DJALALI@ TIR 1384', '티르 1384'],
            ['ABT @#DJALALI@ TIR 1384', '약 티르 1384'],
            ['FROM @#DJALALI@ TIR 1384', '티르 1384 에서'],
            ['AFT @#DJALALI@ TIR 1384', '티르 1384 이후'],
            ['BEF @#DJALALI@ TIR 1384', '티르 1384 이전'],
            ['@#DJALALI@ 15 MORDA 1384', '15 모르다드 1384'],
            ['@#DJALALI@ MORDA 1384', '모르다드 1384'],
            ['ABT @#DJALALI@ MORDA 1384', '약 모르다드 1384'],
            ['FROM @#DJALALI@ MORDA 1384', '모르다드 1384 에서'],
            ['AFT @#DJALALI@ MORDA 1384', '모르다드 1384 이후'],
            ['BEF @#DJALALI@ MORDA 1384', '모르다드 1384 이전'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 샤흐리바르 1384'],
            ['@#DJALALI@ SHAHR 1384', '샤흐리바르 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', '약 샤흐리바르 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', '샤흐리바르 1384 에서'],
            ['AFT @#DJALALI@ SHAHR 1384', '샤흐리바르 1384 이후'],
            ['BEF @#DJALALI@ SHAHR 1384', '샤흐리바르 1384 이전'],
            ['@#DJALALI@ 15 MEHR 1384', '15 메흐르 1384'],
            ['@#DJALALI@ MEHR 1384', '메흐르 1384'],
            ['ABT @#DJALALI@ MEHR 1384', '약 메흐르 1384'],
            ['FROM @#DJALALI@ MEHR 1384', '메흐르 1384 에서'],
            ['AFT @#DJALALI@ MEHR 1384', '메흐르 1384 이후'],
            ['BEF @#DJALALI@ MEHR 1384', '메흐르 1384 이전'],
            ['@#DJALALI@ 15 ABAN 1384', '15 아반 1384'],
            ['@#DJALALI@ ABAN 1384', '아반 1384'],
            ['ABT @#DJALALI@ ABAN 1384', '약 아반 1384'],
            ['FROM @#DJALALI@ ABAN 1384', '아반 1384 에서'],
            ['AFT @#DJALALI@ ABAN 1384', '아반 1384 이후'],
            ['BEF @#DJALALI@ ABAN 1384', '아반 1384 이전'],
            ['@#DJALALI@ 15 AZAR 1384', '15 아자르 1384'],
            ['@#DJALALI@ AZAR 1384', '아자르 1384'],
            ['ABT @#DJALALI@ AZAR 1384', '약 아자르 1384'],
            ['FROM @#DJALALI@ AZAR 1384', '아자르 1384 에서'],
            ['AFT @#DJALALI@ AZAR 1384', '아자르 1384 이후'],
            ['BEF @#DJALALI@ AZAR 1384', '아자르 1384 이전'],
            ['@#DJALALI@ 15 DEY 1384', '15 데이 1384'],
            ['@#DJALALI@ DEY 1384', '데이 1384'],
            ['ABT @#DJALALI@ DEY 1384', '약 데이 1384'],
            ['FROM @#DJALALI@ DEY 1384', '데이 1384 에서'],
            ['AFT @#DJALALI@ DEY 1384', '데이 1384 이후'],
            ['BEF @#DJALALI@ DEY 1384', '데이 1384 이전'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 바흐만 1384'],
            ['@#DJALALI@ BAHMA 1384', '바흐만 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', '약 바흐만 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', '바흐만 1384 에서'],
            ['AFT @#DJALALI@ BAHMA 1384', '바흐만 1384 이후'],
            ['BEF @#DJALALI@ BAHMA 1384', '바흐만 1384 이전'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 에스판드 1384'],
            ['@#DJALALI@ ESFAN 1384', '에스판드 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', '약 에스판드 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', '에스판드 1384 에서'],
            ['AFT @#DJALALI@ ESFAN 1384', '에스판드 1384 이후'],
            ['BEF @#DJALALI@ ESFAN 1384', '에스판드 1384 이전'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', '약 15 파르바르딘 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', '계산 된 15 파르바르딘 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', '예상 15 파르바르딘 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '15 파르바르딘 1384 이전'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '15 파르바르딘 1384 이후'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '15 파르바르딘 1384 에서'],
            ['TO @#DJALALI@ 15 FARVA 1384', '15 파르바르딘 1384까지'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '15 파르바르딘 1384와 15 오르디베헤시트 1384 사이'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '15 파르바르딘 1384에서 15 오르디베헤시트 1384까지'],
            ['INT @#DJALALI@ 15 FARVA 1384', '설명 15 파르바르딘 1384'],
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
        self::assertSame('one 그리고 two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two 그리고 three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one 또는 two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two 또는 three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife with son (born 2000) and daughter (born 1998)
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1970");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@\n1 BIRT\n2 DATE 1 JAN 1972");
        $son = self::male('s', "1 FAMC @fm@\n1 FAMS @fson@\n1 BIRT\n2 DATE 1 JAN 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 1 JAN 1998");
        $child = self::unknown('c', "1 FAMC @fm@");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $fosterDaughter = self::female('fsd', "1 FAMC @fd@\n2 PEDI foster");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");

        // Husband's family (paternal side)
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $olderBroOfH = self::male('obh', "1 FAMS @fobro@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1965");
        $youngerBroOfH = self::male('ybh', "1 FAMS @fybro@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1975");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        // Wife's family (maternal side)
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMS @fbrow@\n1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@\n1 BIRT\n2 DATE 1 JAN 1975");
        $olderSisOfW = self::female('osw', "1 FAMC @fw@\n1 BIRT\n2 DATE 1 JAN 1968");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Nieces/nephews — from brother
        $nieceFromBro = self::female('nb', "1 FAMC @fobro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fobro@");

        // Nieces/nephews — from sister
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins from father's brother (사촌) — older/younger than son
        $cousinOlderM = self::male('com', "1 FAMC @fobro@\n1 BIRT\n2 DATE 1 JAN 1998");
        $cousinYoungerM = self::male('cym', "1 FAMC @fobro@\n1 BIRT\n2 DATE 1 JAN 2002");
        $cousinOlderF = self::female('cof', "1 FAMC @fobro@\n1 BIRT\n2 DATE 1 JAN 1998");
        $cousinYoungerF = self::female('cyf', "1 FAMC @fobro@\n1 BIRT\n2 DATE 1 JAN 2002");
        // Generic cousin from other lines (sister's child)
        $cousinGeneric = self::male('cg', "1 FAMC @fsis@");

        // Sibling's spouses
        $wifeOfOlderBro = self::female('wob', "1 FAMS @fobro@");
        $wifeOfYoungerBro = self::female('wyb', "1 FAMS @fybro@");
        $husbandOfSis = self::male('hsh', "1 FAMS @fsis@");

        // Uncle/aunt spouses
        $wifeOfBroW = self::female('wbw', "1 FAMS @fbrow@");

        // Grandchildren
        $grandsonFromSon = self::male('gs', "1 FAMC @fson@");
        $granddaughterFromSon = self::female('gds', "1 FAMC @fson@");
        $granddaughterFromDau = self::female('gdd', "1 FAMC @fdau@");
        $grandsonFromDau = self::male('gsd', "1 FAMC @fdau@");

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
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @obh@\n1 CHIL @ybh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@\n1 CHIL @osw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@\n1 CHIL @gs@\n1 CHIL @gds@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@\n1 CHIL @gdd@\n1 CHIL @gsd@");
        $fobro = self::family('fobro', "0 @fobro@ FAM\n1 MARR Y\n1 HUSB @obh@\n1 WIFE @wob@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @com@\n1 CHIL @cym@\n1 CHIL @cof@\n1 CHIL @cyf@");
        $fybro = self::family('fybro', "0 @fybro@ FAM\n1 MARR Y\n1 HUSB @ybh@\n1 WIFE @wyb@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 MARR Y\n1 HUSB @hsh@\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cg@");
        $fbrow = self::family('fbrow', "0 @fbrow@ FAM\n1 MARR Y\n1 HUSB @bw@\n1 WIFE @wbw@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $olderBroOfH, $youngerBroOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW, $olderSisOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinOlderM, $cousinYoungerM, $cousinOlderF, $cousinYoungerF, $cousinGeneric,
             $wifeOfOlderBro, $wifeOfYoungerBro, $husbandOfSis, $wifeOfBroW,
             $grandsonFromSon, $granddaughterFromSon, $granddaughterFromDau, $grandsonFromDau,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fobro, $fybro, $fsis, $fbrow, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('아내', '남편', [$husband, $fm, $wife]);
        self::assertRelationshipNames('전남편', '전처', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('약혼녀', '약혼자', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('어머니', '아들', [$son, $fm, $wife]);
        self::assertRelationshipNames('아버지', '아들', [$son, $fm, $husband]);
        self::assertRelationshipNames('어머니', '딸', [$daughter, $fm, $wife]);
        self::assertRelationshipName('자녀', [$husband, $fm, $child]);
        self::assertRelationshipName('아버지', [$child, $fm, $husband]);

        // Adopted / Fostered
        self::assertRelationshipNames('양어머니', '양아들', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('수양어머니', '수양딸', [$fosterDaughter, $fd, $wife]);

        // Step
        self::assertRelationshipName('새아버지', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('의붓딸', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // Siblings — ego-relative elder/younger
        // daughter born 1998 is older than son born 2000
        // son (male) looking at older sister → 누나
        self::assertRelationshipName('누나', [$son, $fm, $daughter]);
        // daughter (female) looking at younger brother → 남동생
        self::assertRelationshipName('남동생', [$daughter, $fm, $son]);
        // wife (female) looking at older sister → 언니
        self::assertRelationshipName('언니', [$wife, $fw, $olderSisOfW]);

        // In-laws — spouse's parents
        self::assertRelationshipName('시어머니', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('시아버지', [$wife, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('장모', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('장인', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // In-laws — child's spouse
        self::assertRelationshipName('며느리', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('사위', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('시누이', [$wife, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('처남', [$husband, $fm, $wife, $fw, $brotherOfW]);

        // In-laws — husband's older/younger brothers
        self::assertRelationshipName('아주버니', [$wife, $fm, $husband, $fp, $olderBroOfH]);
        self::assertRelationshipName('시동생', [$wife, $fm, $husband, $fp, $youngerBroOfH]);

        // In-laws — wife's older/younger sisters
        self::assertRelationshipName('처형', [$husband, $fm, $wife, $fw, $olderSisOfW]);
        self::assertRelationshipName('처제', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // In-laws — sibling's spouse
        self::assertRelationshipName('형수', [$husband, $fp, $olderBroOfH, $fobro, $wifeOfOlderBro]);
        self::assertRelationshipName('매형', [$husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);

        // Grandparents — paternal
        self::assertRelationshipName('할머니', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('할아버지', [$son, $fm, $husband, $fp, $fatherOfH]);
        // Grandparents — maternal
        self::assertRelationshipName('외할머니', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('외할아버지', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren — son's children
        self::assertRelationshipName('손자', [$husband, $fm, $son, $fson, $grandsonFromSon]);
        self::assertRelationshipName('손녀', [$husband, $fm, $son, $fson, $granddaughterFromSon]);
        // Grandchildren — daughter's children
        self::assertRelationshipName('외손녀', [$husband, $fm, $daughter, $fdau, $granddaughterFromDau]);
        self::assertRelationshipName('외손자', [$husband, $fm, $daughter, $fdau, $grandsonFromDau]);

        // Aunts/Uncles — paternal
        self::assertRelationshipName('고모', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('큰아버지', [$son, $fm, $husband, $fp, $olderBroOfH]);
        self::assertRelationshipName('작은아버지', [$son, $fm, $husband, $fp, $youngerBroOfH]);
        // Aunts/Uncles — maternal
        self::assertRelationshipName('이모', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('외삼촌', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Uncle/aunt spouses
        self::assertRelationshipName('고모부', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);
        self::assertRelationshipName('숙모', [$son, $fm, $husband, $fp, $olderBroOfH, $fobro, $wifeOfOlderBro]);
        self::assertRelationshipName('외숙모', [$son, $fm, $wife, $fw, $brotherOfW, $fbrow, $wifeOfBroW]);

        // Nieces/Nephews — brother's children
        self::assertRelationshipName('조카딸', [$husband, $fp, $olderBroOfH, $fobro, $nieceFromBro]);
        self::assertRelationshipName('조카', [$husband, $fp, $olderBroOfH, $fobro, $nephewFromBro]);
        // Nieces/Nephews — sister's children
        self::assertRelationshipName('조카딸', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('조카', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — father's brother's children (사촌) with age
        self::assertRelationshipName('사촌오빠', [$son, $fm, $husband, $fp, $olderBroOfH, $fobro, $cousinOlderM]);
        self::assertRelationshipName('사촌남동생', [$son, $fm, $husband, $fp, $olderBroOfH, $fobro, $cousinYoungerM]);
        self::assertRelationshipName('사촌누나', [$son, $fm, $husband, $fp, $olderBroOfH, $fobro, $cousinOlderF]);
        self::assertRelationshipName('사촌여동생', [$son, $fm, $husband, $fp, $olderBroOfH, $fobro, $cousinYoungerF]);
        // Cousins — generic (parent's sister's child)
        self::assertRelationshipName('사촌', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinGeneric]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('증조할머니', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('증조할아버지', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('2대 이모/고모', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('2대 삼촌/외삼촌', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
