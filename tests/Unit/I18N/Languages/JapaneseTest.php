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
use Fisharebest\Webtrees\I18N\Languages\Japanese;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Japanese::class)]
class JapaneseTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Japanese();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Jpan, self::language()->script());
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
        self::assertSame('ja', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('日本語', self::language()->endonym());
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
            ['15 JAN 2000', '15 1月 2000'],
            ['JAN 2000', '1月 2000'],
            ['ABT JAN 2000', 'about 1月 2000'],
            ['FROM JAN 2000', 'from 1月 2000'],
            ['AFT JAN 2000', 'after 1月 2000'],
            ['BEF JAN 2000', 'before 1月 2000'],
            ['15 FEB 2000', '15 2月 2000'],
            ['FEB 2000', '2月 2000'],
            ['ABT FEB 2000', 'about 2月 2000'],
            ['FROM FEB 2000', 'from 2月 2000'],
            ['AFT FEB 2000', 'after 2月 2000'],
            ['BEF FEB 2000', 'before 2月 2000'],
            ['15 MAR 2000', '15 3月 2000'],
            ['MAR 2000', '3月 2000'],
            ['ABT MAR 2000', 'about 3月 2000'],
            ['FROM MAR 2000', 'from 3月 2000'],
            ['AFT MAR 2000', 'after 3月 2000'],
            ['BEF MAR 2000', 'before 3月 2000'],
            ['15 APR 2000', '15 4月 2000'],
            ['APR 2000', '4月 2000'],
            ['ABT APR 2000', 'about 4月 2000'],
            ['FROM APR 2000', 'from 4月 2000'],
            ['AFT APR 2000', 'after 4月 2000'],
            ['BEF APR 2000', 'before 4月 2000'],
            ['15 MAY 2000', '15 5月 2000'],
            ['MAY 2000', '5月 2000'],
            ['ABT MAY 2000', 'about 5月 2000'],
            ['FROM MAY 2000', 'from 5月 2000'],
            ['AFT MAY 2000', 'after 5月 2000'],
            ['BEF MAY 2000', 'before 5月 2000'],
            ['15 JUN 2000', '15 6月 2000'],
            ['JUN 2000', '6月 2000'],
            ['ABT JUN 2000', 'about 6月 2000'],
            ['FROM JUN 2000', 'from 6月 2000'],
            ['AFT JUN 2000', 'after 6月 2000'],
            ['BEF JUN 2000', 'before 6月 2000'],
            ['15 JUL 2000', '15 7月 2000'],
            ['JUL 2000', '7月 2000'],
            ['ABT JUL 2000', 'about 7月 2000'],
            ['FROM JUL 2000', 'from 7月 2000'],
            ['AFT JUL 2000', 'after 7月 2000'],
            ['BEF JUL 2000', 'before 7月 2000'],
            ['15 AUG 2000', '15 8月 2000'],
            ['AUG 2000', '8月 2000'],
            ['ABT AUG 2000', 'about 8月 2000'],
            ['FROM AUG 2000', 'from 8月 2000'],
            ['AFT AUG 2000', 'after 8月 2000'],
            ['BEF AUG 2000', 'before 8月 2000'],
            ['15 SEP 2000', '15 9月 2000'],
            ['SEP 2000', '9月 2000'],
            ['ABT SEP 2000', 'about 9月 2000'],
            ['FROM SEP 2000', 'from 9月 2000'],
            ['AFT SEP 2000', 'after 9月 2000'],
            ['BEF SEP 2000', 'before 9月 2000'],
            ['15 OCT 2000', '15 10月 2000'],
            ['OCT 2000', '10月 2000'],
            ['ABT OCT 2000', 'about 10月 2000'],
            ['FROM OCT 2000', 'from 10月 2000'],
            ['AFT OCT 2000', 'after 10月 2000'],
            ['BEF OCT 2000', 'before 10月 2000'],
            ['15 NOV 2000', '15 11月 2000'],
            ['NOV 2000', '11月 2000'],
            ['ABT NOV 2000', 'about 11月 2000'],
            ['FROM NOV 2000', 'from 11月 2000'],
            ['AFT NOV 2000', 'after 11月 2000'],
            ['BEF NOV 2000', 'before 11月 2000'],
            ['15 DEC 2000', '15 12月 2000'],
            ['DEC 2000', '12月 2000'],
            ['ABT DEC 2000', 'about 12月 2000'],
            ['FROM DEC 2000', 'from 12月 2000'],
            ['AFT DEC 2000', 'after 12月 2000'],
            ['BEF DEC 2000', 'before 12月 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'about 15 1月 2000'],
            ['CAL 15 JAN 2000', 'calculated 15 1月 2000'],
            ['EST 15 JAN 2000', 'estimated 15 1月 2000'],
            ['BEF 15 JAN 2000', 'before 15 1月 2000'],
            ['AFT 15 JAN 2000', 'after 15 1月 2000'],
            ['FROM 15 JAN 2000', 'from 15 1月 2000'],
            ['TO 15 JAN 2000', 'to 15 1月 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between 15 1月 2000 and 15 2月 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from 15 1月 2000 to 15 2月 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 1月 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 1月 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', '1月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about 1月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from 1月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after 1月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before 1月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 2月 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', '2月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about 2月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from 2月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after 2月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before 2月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 3月 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', '3月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about 3月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from 3月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after 3月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before 3月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 4月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 4月 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', '4月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about 4月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from 4月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after 4月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before 4月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 5月 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', '5月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about 5月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from 5月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after 5月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before 5月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 6月 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', '6月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about 6月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from 6月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after 6月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before 6月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 7月 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', '7月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about 7月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from 7月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after 7月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before 7月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 8月 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', '8月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about 8月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from 8月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after 8月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before 8月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 9月 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', '9月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about 9月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from 9月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after 9月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before 9月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 10月 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', '10月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about 10月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from 10月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after 10月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before 10月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 11月 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', '11月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about 11月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from 11月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after 11月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before 11月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 12月 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', '12月 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about 12月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from 12月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after 12月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before 12月 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about 15 1月 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15 1月 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 1月 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before 15 1月 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after 15 1月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from 15 1月 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15 1月 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between 15 1月 1700 ᴄᴇ and 15 2月 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from 15 1月 1700 ᴄᴇ to 15 2月 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 1月 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 ティシュレー 5765'],
            ['@#DHEBREW@ TSH 5765', 'ティシュレー 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'about ティシュレー 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'from ティシュレー 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'after ティシュレー 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'before ティシュレー 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 ヘシュヴァン 5765'],
            ['@#DHEBREW@ CSH 5765', 'ヘシュヴァン 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'about ヘシュヴァン 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'from ヘシュヴァン 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'after ヘシュヴァン 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'before ヘシュヴァン 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 キスレーヴ 5765'],
            ['@#DHEBREW@ KSL 5765', 'キスレーヴ 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'about キスレーヴ 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'from キスレーヴ 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'after キスレーヴ 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'before キスレーヴ 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 テベット 5765'],
            ['@#DHEBREW@ TVT 5765', 'テベット 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'about テベット 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'from テベット 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'after テベット 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'before テベット 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 シュバット 5765'],
            ['@#DHEBREW@ SHV 5765', 'シュバット 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'about シュバット 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'from シュバット 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'after シュバット 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'before シュバット 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 アダル I 5765'],
            ['@#DHEBREW@ ADR 5765', 'アダル I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'about アダル I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'from アダル I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'after アダル I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'before アダル I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 アダル II 5765'],
            ['@#DHEBREW@ ADS 5765', 'アダル II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'about アダル II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'from アダル II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'after アダル II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'before アダル II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 ニサン 5765'],
            ['@#DHEBREW@ NSN 5765', 'ニサン 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'about ニサン 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'from ニサン 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'after ニサン 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'before ニサン 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 イヤル 5765'],
            ['@#DHEBREW@ IYR 5765', 'イヤル 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'about イヤル 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'from イヤル 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'after イヤル 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'before イヤル 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 シバン 5765'],
            ['@#DHEBREW@ SVN 5765', 'シバン 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'about シバン 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'from シバン 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'after シバン 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'before シバン 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 タムズ 5765'],
            ['@#DHEBREW@ TMZ 5765', 'タムズ 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about タムズ 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from タムズ 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after タムズ 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before タムズ 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 アブ 5765'],
            ['@#DHEBREW@ AAV 5765', 'アブ 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'about アブ 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'from アブ 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'after アブ 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'before アブ 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 エルール 5765'],
            ['@#DHEBREW@ ELL 5765', 'エルール 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'about エルール 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'from エルール 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'after エルール 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'before エルール 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about 15 ティシュレー 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15 ティシュレー 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 ティシュレー 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before 15 ティシュレー 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after 15 ティシュレー 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from 15 ティシュレー 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15 ティシュレー 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between 15 ティシュレー 5765 and 15 ヘシュヴァン 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from 15 ティシュレー 5765 to 15 ヘシュヴァン 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 ティシュレー 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 ヴァンデミエール An XII'],
            ['@#DFRENCH R@ VEND 12', 'ヴァンデミエール An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about ヴァンデミエール An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from ヴァンデミエール An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after ヴァンデミエール An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before ヴァンデミエール An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 ブリュメール An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ブリュメール An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about ブリュメール An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from ブリュメール An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after ブリュメール An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before ブリュメール An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 フリメール An XII'],
            ['@#DFRENCH R@ FRIM 12', 'フリメール An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about フリメール An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from フリメール An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after フリメール An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before フリメール An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 ニヴォーズ An XII'],
            ['@#DFRENCH R@ NIVO 12', 'ニヴォーズ An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about ニヴォーズ An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from ニヴォーズ An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after ニヴォーズ An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before ニヴォーズ An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 プリュヴィオーズ An XII'],
            ['@#DFRENCH R@ PLUV 12', 'プリュヴィオーズ An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about プリュヴィオーズ An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from プリュヴィオーズ An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after プリュヴィオーズ An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before プリュヴィオーズ An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ヴァントーズ An XII'],
            ['@#DFRENCH R@ VENT 12', 'ヴァントーズ An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about ヴァントーズ An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from ヴァントーズ An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after ヴァントーズ An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before ヴァントーズ An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 ジェルミナール An XII'],
            ['@#DFRENCH R@ GERM 12', 'ジェルミナール An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about ジェルミナール An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from ジェルミナール An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after ジェルミナール An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before ジェルミナール An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 フロレアール An XII'],
            ['@#DFRENCH R@ FLOR 12', 'フロレアール An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about フロレアール An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from フロレアール An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after フロレアール An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before フロレアール An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 プレリアール An XII'],
            ['@#DFRENCH R@ PRAI 12', 'プレリアール An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about プレリアール An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from プレリアール An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after プレリアール An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before プレリアール An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 メシドール An XII'],
            ['@#DFRENCH R@ MESS 12', 'メシドール An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about メシドール An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from メシドール An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after メシドール An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before メシドール An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 テルミドール An XII'],
            ['@#DFRENCH R@ THER 12', 'テルミドール An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about テルミドール An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from テルミドール An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after テルミドール An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before テルミドール An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 フリュクティドール An XII'],
            ['@#DFRENCH R@ FRUC 12', 'フリュクティドール An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about フリュクティドール An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from フリュクティドール An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after フリュクティドール An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before フリュクティドール An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 補充日 An XII'],
            ['@#DFRENCH R@ COMP 12', '補充日 An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about 補充日 An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from 補充日 An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after 補充日 An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before 補充日 An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about 15 ヴァンデミエール An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15 ヴァンデミエール An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 ヴァンデミエール An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before 15 ヴァンデミエール An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after 15 ヴァンデミエール An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from 15 ヴァンデミエール An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15 ヴァンデミエール An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between 15 ヴァンデミエール An XII and 15 ブリュメール An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from 15 ヴァンデミエール An XII to 15 ブリュメール An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 ヴァンデミエール An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 ムハッラム 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'ムハッラム 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about ムハッラム 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from ムハッラム 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after ムハッラム 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before ムハッラム 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 サファル 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'サファル 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about サファル 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from サファル 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after サファル 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before サファル 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 ラビーウル・アウワル 1425'],
            ['@#DHIJRI@ RABIA 1425', 'ラビーウル・アウワル 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about ラビーウル・アウワル 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from ラビーウル・アウワル 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after ラビーウル・アウワル 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before ラビーウル・アウワル 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 ラビーウッサーニー 1425'],
            ['@#DHIJRI@ RABIT 1425', 'ラビーウッサーニー 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about ラビーウッサーニー 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from ラビーウッサーニー 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after ラビーウッサーニー 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before ラビーウッサーニー 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 ジュマーダル・ウーラー 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'ジュマーダル・ウーラー 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about ジュマーダル・ウーラー 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from ジュマーダル・ウーラー 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after ジュマーダル・ウーラー 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before ジュマーダル・ウーラー 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 ジュマーダッサーニー 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'ジュマーダッサーニー 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about ジュマーダッサーニー 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from ジュマーダッサーニー 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after ジュマーダッサーニー 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before ジュマーダッサーニー 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 ラジャブ 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'ラジャブ 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about ラジャブ 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from ラジャブ 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after ラジャブ 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before ラジャブ 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 シャアバーン 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'シャアバーン 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about シャアバーン 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from シャアバーン 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after シャアバーン 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before シャアバーン 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 ラマダーン 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ラマダーン 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about ラマダーン 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from ラマダーン 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after ラマダーン 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before ラマダーン 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 シャウワール 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'シャウワール 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about シャウワール 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from シャウワール 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after シャウワール 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before シャウワール 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 ズー・アルカアダ 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'ズー・アルカアダ 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about ズー・アルカアダ 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from ズー・アルカアダ 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after ズー・アルカアダ 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before ズー・アルカアダ 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about 15 ムハッラム 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated 15 ムハッラム 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15 ムハッラム 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before 15 ムハッラム 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after 15 ムハッラム 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from 15 ムハッラム 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to 15 ムハッラム 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between 15 ムハッラム 1425 and 15 サファル 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from 15 ムハッラム 1425 to 15 サファル 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15 ムハッラム 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 ファルヴァルディーン 1384'],
            ['@#DJALALI@ FARVA 1384', 'ファルヴァルディーン 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'about ファルヴァルディーン 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'from ファルヴァルディーン 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'after ファルヴァルディーン 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'before ファルヴァルディーン 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 オルディーベヘシュト 1384'],
            ['@#DJALALI@ ORDIB 1384', 'オルディーベヘシュト 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about オルディーベヘシュト 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from オルディーベヘシュト 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after オルディーベヘシュト 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before オルディーベヘシュト 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 ホルダード 1384'],
            ['@#DJALALI@ KHORD 1384', 'ホルダード 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'about ホルダード 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'from ホルダード 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'after ホルダード 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'before ホルダード 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 ティール 1384'],
            ['@#DJALALI@ TIR 1384', 'ティール 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'about ティール 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'from ティール 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'after ティール 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'before ティール 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 モルダード 1384'],
            ['@#DJALALI@ MORDA 1384', 'モルダード 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'about モルダード 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'from モルダード 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'after モルダード 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'before モルダード 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 シャフリーヴァル 1384'],
            ['@#DJALALI@ SHAHR 1384', 'シャフリーヴァル 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about シャフリーヴァル 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from シャフリーヴァル 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after シャフリーヴァル 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before シャフリーヴァル 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 メフル 1384'],
            ['@#DJALALI@ MEHR 1384', 'メフル 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'about メフル 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'from メフル 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'after メフル 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'before メフル 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 アーバーン 1384'],
            ['@#DJALALI@ ABAN 1384', 'アーバーン 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'about アーバーン 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'from アーバーン 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'after アーバーン 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'before アーバーン 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 アーザル 1384'],
            ['@#DJALALI@ AZAR 1384', 'アーザル 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'about アーザル 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'from アーザル 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'after アーザル 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'before アーザル 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 デイ 1384'],
            ['@#DJALALI@ DEY 1384', 'デイ 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'about デイ 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'from デイ 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'after デイ 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'before デイ 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 バフマン 1384'],
            ['@#DJALALI@ BAHMA 1384', 'バフマン 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about バフマン 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from バフマン 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after バフマン 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before バフマン 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 エスファンド 1384'],
            ['@#DJALALI@ ESFAN 1384', 'エスファンド 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about エスファンド 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from エスファンド 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after エスファンド 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before エスファンド 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about 15 ファルヴァルディーン 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated 15 ファルヴァルディーン 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15 ファルヴァルディーン 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before 15 ファルヴァルディーン 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after 15 ファルヴァルディーン 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from 15 ファルヴァルディーン 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to 15 ファルヴァルディーン 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between 15 ファルヴァルディーン 1384 and 15 オルディーベヘシュト 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from 15 ファルヴァルディーン 1384 to 15 オルディーベヘシュト 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15 ファルヴァルディーン 1384'],
        ];
    }

    public function testFormatList(): void
    {
        $language = static::language();

        self::assertSame('', $language->formatList([]));
        self::assertSame('one', $language->formatList(['one']));
        self::assertSame('one、two', $language->formatList(['one', 'two']));
        self::assertSame('one、two、three', $language->formatList(['one', 'two', 'three']));

        self::assertSame('', $language->formatListAnd([]));
        self::assertSame('one', $language->formatListAnd(['one']));
        self::assertSame('one、two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one、two、three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('oneまたはtwo', $language->formatListOr(['one', 'two']));
        self::assertSame('one、twoまたはthree', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife with son (born 2000) and daughter (born 1998)
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1970");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 FAMS @fson@\n1 BIRT\n2 DATE 1 JAN 2000");
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
        $brotherOfW = self::male('bw', "1 FAMS @fbrow@\n1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Nieces/nephews — from brother
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");

        // Nieces/nephews — from sister
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins (older/younger than son)
        $cousinOlderM = self::male('com', "1 FAMC @fbro@\n1 BIRT\n2 DATE 1 JAN 1998");
        $cousinYoungerM = self::male('cym', "1 FAMC @fbro@\n1 BIRT\n2 DATE 1 JAN 2002");
        $cousinOlderF = self::female('cof', "1 FAMC @fsis@\n1 BIRT\n2 DATE 1 JAN 1998");
        $cousinYoungerF = self::female('cyf', "1 FAMC @fsis@\n1 BIRT\n2 DATE 1 JAN 2002");

        // Uncle/aunt spouses
        $wifeOfBro = self::female('wbh', "1 FAMS @fbro@");
        $husbandOfSis = self::male('hsh', "1 FAMS @fsis@");
        $wifeOfBroW = self::female('wbw', "1 FAMS @fbrow@");

        // Grandchildren
        $grandsonFromSon = self::male('gs', "1 FAMC @fson@");
        $granddaughterFromDau = self::female('gdd', "1 FAMC @fdau@");

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
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@\n1 CHIL @gs@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@\n1 CHIL @gdd@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 MARR Y\n1 HUSB @bh@\n1 WIFE @wbh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @com@\n1 CHIL @cym@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 MARR Y\n1 HUSB @hsh@\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cof@\n1 CHIL @cyf@");
        $fbrow = self::family('fbrow', "0 @fbrow@ FAM\n1 MARR Y\n1 HUSB @bw@\n1 WIFE @wbw@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinOlderM, $cousinYoungerM, $cousinOlderF, $cousinYoungerF,
             $wifeOfBro, $husbandOfSis, $wifeOfBroW,
             $grandsonFromSon, $granddaughterFromDau,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fbrow, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('妻', '夫', [$husband, $fm, $wife]);
        self::assertRelationshipNames('元夫', '元妻', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('婚約者', '婚約者', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('母', '息子', [$son, $fm, $wife]);
        self::assertRelationshipNames('父', '息子', [$son, $fm, $husband]);
        self::assertRelationshipNames('母', '娘', [$daughter, $fm, $wife]);

        // Adopted / Fostered
        self::assertRelationshipNames('養母', '養子', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('里母', '里娘', [$fosterDaughter, $fd, $wife]);

        // Siblings — elder/younger (daughter born 1998 is older than son born 2000)
        self::assertRelationshipNames('姉', '弟', [$son, $fm, $daughter]);

        // Stepfamily
        self::assertRelationshipName('継父', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('継娘', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('義母', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('義父', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('姑', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('舅', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('嫁', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('婿', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('義姉妹', [$husband, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('義兄弟', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('小姑', [$wife, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('義兄弟', [$wife, $fm, $husband, $fp, $brotherOfH]);

        // In-laws — sibling's spouse
        self::assertRelationshipName('兄嫁', [$husband, $fp, $brotherOfH, $fbro, $wifeOfBro]);
        self::assertRelationshipName('姉婿', [$husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);

        // Grandparents — paternal
        self::assertRelationshipName('父方の祖母', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('父方の祖父', [$son, $fm, $husband, $fp, $fatherOfH]);
        // Grandparents — maternal
        self::assertRelationshipName('母方の祖母', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('母方の祖父', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren — son's children
        self::assertRelationshipName('孫息子', [$husband, $fm, $son, $fson, $grandsonFromSon]);
        // Grandchildren — daughter's children
        self::assertRelationshipName('孫娘', [$husband, $fm, $daughter, $fdau, $granddaughterFromDau]);

        // Aunts/Uncles — paternal
        self::assertRelationshipName('父方の伯母/叔母', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('父方の伯父/叔父', [$son, $fm, $husband, $fp, $brotherOfH]);
        // Aunts/Uncles — maternal
        self::assertRelationshipName('母方の伯母/叔母', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('母方の伯父/叔父', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Uncle/aunt's spouses
        self::assertRelationshipName('伯母/叔母の夫', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);
        self::assertRelationshipName('伯父/叔父の妻', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $wifeOfBro]);

        // Nieces/Nephews — brother's children
        self::assertRelationshipName('姪', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('甥', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        // Nieces/Nephews — sister's children
        self::assertRelationshipName('姪', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('甥', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — older/younger
        self::assertRelationshipName('従兄', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinOlderM]);
        self::assertRelationshipName('従弟', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinYoungerM]);
        self::assertRelationshipName('従姉', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinOlderF]);
        self::assertRelationshipName('従妹', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinYoungerF]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('曾祖母', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('曾祖父', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('2世の伯母/叔母', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('2世の伯父/叔父', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
