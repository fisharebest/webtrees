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
use Fisharebest\Webtrees\I18N\Languages\ChineseSimplified;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ChineseSimplified::class)]
class ChineseSimplifiedTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new ChineseSimplified();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Hans, self::language()->script());
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
        self::assertSame('zh-Hans', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('简体中文', self::language()->endonym());
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
        return 'YMD';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '2000年1月15日'],
            ['JAN 2000', '2000年1月'],
            ['ABT JAN 2000', '关于 2000年1月'],
            ['FROM JAN 2000', '从 2000年1月'],
            ['AFT JAN 2000', '在2000年1月 之后'],
            ['BEF JAN 2000', '在2000年1月 之前'],
            ['15 FEB 2000', '2000年2月15日'],
            ['FEB 2000', '2000年2月'],
            ['ABT FEB 2000', '关于 2000年2月'],
            ['FROM FEB 2000', '从 2000年2月'],
            ['AFT FEB 2000', '在2000年2月 之后'],
            ['BEF FEB 2000', '在2000年2月 之前'],
            ['15 MAR 2000', '2000年3月15日'],
            ['MAR 2000', '2000年3月'],
            ['ABT MAR 2000', '关于 2000年3月'],
            ['FROM MAR 2000', '从 2000年3月'],
            ['AFT MAR 2000', '在2000年3月 之后'],
            ['BEF MAR 2000', '在2000年3月 之前'],
            ['15 APR 2000', '2000年4月15日'],
            ['APR 2000', '2000年4月'],
            ['ABT APR 2000', '关于 2000年4月'],
            ['FROM APR 2000', '从 2000年4月'],
            ['AFT APR 2000', '在2000年4月 之后'],
            ['BEF APR 2000', '在2000年4月 之前'],
            ['15 MAY 2000', '2000年5月15日'],
            ['MAY 2000', '2000年5月'],
            ['ABT MAY 2000', '关于 2000年5月'],
            ['FROM MAY 2000', '从 2000年5月'],
            ['AFT MAY 2000', '在2000年5月 之后'],
            ['BEF MAY 2000', '在2000年5月 之前'],
            ['15 JUN 2000', '2000年6月15日'],
            ['JUN 2000', '2000年6月'],
            ['ABT JUN 2000', '关于 2000年6月'],
            ['FROM JUN 2000', '从 2000年6月'],
            ['AFT JUN 2000', '在2000年6月 之后'],
            ['BEF JUN 2000', '在2000年6月 之前'],
            ['15 JUL 2000', '2000年7月15日'],
            ['JUL 2000', '2000年7月'],
            ['ABT JUL 2000', '关于 2000年7月'],
            ['FROM JUL 2000', '从 2000年7月'],
            ['AFT JUL 2000', '在2000年7月 之后'],
            ['BEF JUL 2000', '在2000年7月 之前'],
            ['15 AUG 2000', '2000年8月15日'],
            ['AUG 2000', '2000年8月'],
            ['ABT AUG 2000', '关于 2000年8月'],
            ['FROM AUG 2000', '从 2000年8月'],
            ['AFT AUG 2000', '在2000年8月 之后'],
            ['BEF AUG 2000', '在2000年8月 之前'],
            ['15 SEP 2000', '2000年9月15日'],
            ['SEP 2000', '2000年9月'],
            ['ABT SEP 2000', '关于 2000年9月'],
            ['FROM SEP 2000', '从 2000年9月'],
            ['AFT SEP 2000', '在2000年9月 之后'],
            ['BEF SEP 2000', '在2000年9月 之前'],
            ['15 OCT 2000', '2000年10月15日'],
            ['OCT 2000', '2000年10月'],
            ['ABT OCT 2000', '关于 2000年10月'],
            ['FROM OCT 2000', '从 2000年10月'],
            ['AFT OCT 2000', '在2000年10月 之后'],
            ['BEF OCT 2000', '在2000年10月 之前'],
            ['15 NOV 2000', '2000年11月15日'],
            ['NOV 2000', '2000年11月'],
            ['ABT NOV 2000', '关于 2000年11月'],
            ['FROM NOV 2000', '从 2000年11月'],
            ['AFT NOV 2000', '在2000年11月 之后'],
            ['BEF NOV 2000', '在2000年11月 之前'],
            ['15 DEC 2000', '2000年12月15日'],
            ['DEC 2000', '2000年12月'],
            ['ABT DEC 2000', '关于 2000年12月'],
            ['FROM DEC 2000', '从 2000年12月'],
            ['AFT DEC 2000', '在2000年12月 之后'],
            ['BEF DEC 2000', '在2000年12月 之前'],
            ['2000', '2000年'],
            ['ABT 15 JAN 2000', '关于 2000年1月15日'],
            ['CAL 15 JAN 2000', '计算出 2000年1月15日'],
            ['EST 15 JAN 2000', '估计于2000年1月15日年'],
            ['BEF 15 JAN 2000', '在2000年1月15日 之前'],
            ['AFT 15 JAN 2000', '在2000年1月15日 之后'],
            ['FROM 15 JAN 2000', '从 2000年1月15日'],
            ['TO 15 JAN 2000', '到 2000年1月15日'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '在2000年1月15日和2000年2月15日 间'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '从 2000年1月15日 到 2000年2月15日'],
            ['INT 15 JAN 2000', '解释 2000年1月15日'],
            ['@#DJULIAN@ 15 JAN 1700', 'AD 1700年1月15日'],
            ['@#DJULIAN@ JAN 1700', 'AD 1700年1月'],
            ['ABT @#DJULIAN@ JAN 1700', '关于 AD 1700年1月'],
            ['FROM @#DJULIAN@ JAN 1700', '从 AD 1700年1月'],
            ['AFT @#DJULIAN@ JAN 1700', '在AD 1700年1月 之后'],
            ['BEF @#DJULIAN@ JAN 1700', '在AD 1700年1月 之前'],
            ['@#DJULIAN@ 15 FEB 1700', 'AD 1700年2月15日'],
            ['@#DJULIAN@ FEB 1700', 'AD 1700年2月'],
            ['ABT @#DJULIAN@ FEB 1700', '关于 AD 1700年2月'],
            ['FROM @#DJULIAN@ FEB 1700', '从 AD 1700年2月'],
            ['AFT @#DJULIAN@ FEB 1700', '在AD 1700年2月 之后'],
            ['BEF @#DJULIAN@ FEB 1700', '在AD 1700年2月 之前'],
            ['@#DJULIAN@ 15 MAR 1700', 'AD 1700年3月15日'],
            ['@#DJULIAN@ MAR 1700', 'AD 1700年3月'],
            ['ABT @#DJULIAN@ MAR 1700', '关于 AD 1700年3月'],
            ['FROM @#DJULIAN@ MAR 1700', '从 AD 1700年3月'],
            ['AFT @#DJULIAN@ MAR 1700', '在AD 1700年3月 之后'],
            ['BEF @#DJULIAN@ MAR 1700', '在AD 1700年3月 之前'],
            ['@#DJULIAN@ 15 APR 1700', 'AD 1700年4月15日'],
            ['@#DJULIAN@ 14 APR 1645/46', 'AD 1645/46年4月14日'],
            ['@#DJULIAN@ APR 1700', 'AD 1700年4月'],
            ['ABT @#DJULIAN@ APR 1700', '关于 AD 1700年4月'],
            ['FROM @#DJULIAN@ APR 1700', '从 AD 1700年4月'],
            ['AFT @#DJULIAN@ APR 1700', '在AD 1700年4月 之后'],
            ['BEF @#DJULIAN@ APR 1700', '在AD 1700年4月 之前'],
            ['@#DJULIAN@ 15 MAY 1700', 'AD 1700年5月15日'],
            ['@#DJULIAN@ MAY 1700', 'AD 1700年5月'],
            ['ABT @#DJULIAN@ MAY 1700', '关于 AD 1700年5月'],
            ['FROM @#DJULIAN@ MAY 1700', '从 AD 1700年5月'],
            ['AFT @#DJULIAN@ MAY 1700', '在AD 1700年5月 之后'],
            ['BEF @#DJULIAN@ MAY 1700', '在AD 1700年5月 之前'],
            ['@#DJULIAN@ 15 JUN 1700', 'AD 1700年6月15日'],
            ['@#DJULIAN@ JUN 1700', 'AD 1700年6月'],
            ['ABT @#DJULIAN@ JUN 1700', '关于 AD 1700年6月'],
            ['FROM @#DJULIAN@ JUN 1700', '从 AD 1700年6月'],
            ['AFT @#DJULIAN@ JUN 1700', '在AD 1700年6月 之后'],
            ['BEF @#DJULIAN@ JUN 1700', '在AD 1700年6月 之前'],
            ['@#DJULIAN@ 15 JUL 1700', 'AD 1700年7月15日'],
            ['@#DJULIAN@ JUL 1700', 'AD 1700年7月'],
            ['ABT @#DJULIAN@ JUL 1700', '关于 AD 1700年7月'],
            ['FROM @#DJULIAN@ JUL 1700', '从 AD 1700年7月'],
            ['AFT @#DJULIAN@ JUL 1700', '在AD 1700年7月 之后'],
            ['BEF @#DJULIAN@ JUL 1700', '在AD 1700年7月 之前'],
            ['@#DJULIAN@ 15 AUG 1700', 'AD 1700年8月15日'],
            ['@#DJULIAN@ AUG 1700', 'AD 1700年8月'],
            ['ABT @#DJULIAN@ AUG 1700', '关于 AD 1700年8月'],
            ['FROM @#DJULIAN@ AUG 1700', '从 AD 1700年8月'],
            ['AFT @#DJULIAN@ AUG 1700', '在AD 1700年8月 之后'],
            ['BEF @#DJULIAN@ AUG 1700', '在AD 1700年8月 之前'],
            ['@#DJULIAN@ 15 SEP 1700', 'AD 1700年9月15日'],
            ['@#DJULIAN@ SEP 1700', 'AD 1700年9月'],
            ['ABT @#DJULIAN@ SEP 1700', '关于 AD 1700年9月'],
            ['FROM @#DJULIAN@ SEP 1700', '从 AD 1700年9月'],
            ['AFT @#DJULIAN@ SEP 1700', '在AD 1700年9月 之后'],
            ['BEF @#DJULIAN@ SEP 1700', '在AD 1700年9月 之前'],
            ['@#DJULIAN@ 15 OCT 1700', 'AD 1700年10月15日'],
            ['@#DJULIAN@ OCT 1700', 'AD 1700年10月'],
            ['ABT @#DJULIAN@ OCT 1700', '关于 AD 1700年10月'],
            ['FROM @#DJULIAN@ OCT 1700', '从 AD 1700年10月'],
            ['AFT @#DJULIAN@ OCT 1700', '在AD 1700年10月 之后'],
            ['BEF @#DJULIAN@ OCT 1700', '在AD 1700年10月 之前'],
            ['@#DJULIAN@ 15 NOV 1700', 'AD 1700年11月15日'],
            ['@#DJULIAN@ NOV 1700', 'AD 1700年11月'],
            ['ABT @#DJULIAN@ NOV 1700', '关于 AD 1700年11月'],
            ['FROM @#DJULIAN@ NOV 1700', '从 AD 1700年11月'],
            ['AFT @#DJULIAN@ NOV 1700', '在AD 1700年11月 之后'],
            ['BEF @#DJULIAN@ NOV 1700', '在AD 1700年11月 之前'],
            ['@#DJULIAN@ 15 DEC 1700', 'AD 1700年12月15日'],
            ['@#DJULIAN@ DEC 1700', 'AD 1700年12月'],
            ['ABT @#DJULIAN@ DEC 1700', '关于 AD 1700年12月'],
            ['FROM @#DJULIAN@ DEC 1700', '从 AD 1700年12月'],
            ['AFT @#DJULIAN@ DEC 1700', '在AD 1700年12月 之后'],
            ['BEF @#DJULIAN@ DEC 1700', '在AD 1700年12月 之前'],
            ['@#DJULIAN@ 1700', 'AD 1700年'],
            ['ABT @#DJULIAN@ 15 JAN 1700', '关于 AD 1700年1月15日'],
            ['CAL @#DJULIAN@ 15 JAN 1700', '计算出 AD 1700年1月15日'],
            ['EST @#DJULIAN@ 15 JAN 1700', '估计于AD 1700年1月15日年'],
            ['BEF @#DJULIAN@ 15 JAN 1700', '在AD 1700年1月15日 之前'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '在AD 1700年1月15日 之后'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '从 AD 1700年1月15日'],
            ['TO @#DJULIAN@ 15 JAN 1700', '到 AD 1700年1月15日'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '在AD 1700年1月15日和AD 1700年2月15日 间'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '从 AD 1700年1月15日 到 AD 1700年2月15日'],
            ['INT @#DJULIAN@ 15 JAN 1700', '解释 AD 1700年1月15日'],
            ['@#DHEBREW@ 15 TSH 5765', '5765年1月15日'],
            ['@#DHEBREW@ TSH 5765', '5765年1月'],
            ['ABT @#DHEBREW@ TSH 5765', '关于 5765年1月'],
            ['FROM @#DHEBREW@ TSH 5765', '从 5765年1月'],
            ['AFT @#DHEBREW@ TSH 5765', '在5765年1月 之后'],
            ['BEF @#DHEBREW@ TSH 5765', '在5765年1月 之前'],
            ['@#DHEBREW@ 15 CSH 5765', '5765年2月15日'],
            ['@#DHEBREW@ CSH 5765', '5765年2月'],
            ['ABT @#DHEBREW@ CSH 5765', '关于 5765年2月'],
            ['FROM @#DHEBREW@ CSH 5765', '从 5765年2月'],
            ['AFT @#DHEBREW@ CSH 5765', '在5765年2月 之后'],
            ['BEF @#DHEBREW@ CSH 5765', '在5765年2月 之前'],
            ['@#DHEBREW@ 15 KSL 5765', '5765年3月15日'],
            ['@#DHEBREW@ KSL 5765', '5765年3月'],
            ['ABT @#DHEBREW@ KSL 5765', '关于 5765年3月'],
            ['FROM @#DHEBREW@ KSL 5765', '从 5765年3月'],
            ['AFT @#DHEBREW@ KSL 5765', '在5765年3月 之后'],
            ['BEF @#DHEBREW@ KSL 5765', '在5765年3月 之前'],
            ['@#DHEBREW@ 15 TVT 5765', '5765年4月15日'],
            ['@#DHEBREW@ TVT 5765', '5765年4月'],
            ['ABT @#DHEBREW@ TVT 5765', '关于 5765年4月'],
            ['FROM @#DHEBREW@ TVT 5765', '从 5765年4月'],
            ['AFT @#DHEBREW@ TVT 5765', '在5765年4月 之后'],
            ['BEF @#DHEBREW@ TVT 5765', '在5765年4月 之前'],
            ['@#DHEBREW@ 15 SHV 5765', '5765年5月15日'],
            ['@#DHEBREW@ SHV 5765', '5765年5月'],
            ['ABT @#DHEBREW@ SHV 5765', '关于 5765年5月'],
            ['FROM @#DHEBREW@ SHV 5765', '从 5765年5月'],
            ['AFT @#DHEBREW@ SHV 5765', '在5765年5月 之后'],
            ['BEF @#DHEBREW@ SHV 5765', '在5765年5月 之前'],
            ['@#DHEBREW@ 15 ADR 5765', '5765年6月15日'],
            ['@#DHEBREW@ ADR 5765', '5765年6月'],
            ['ABT @#DHEBREW@ ADR 5765', '关于 5765年6月'],
            ['FROM @#DHEBREW@ ADR 5765', '从 5765年6月'],
            ['AFT @#DHEBREW@ ADR 5765', '在5765年6月 之后'],
            ['BEF @#DHEBREW@ ADR 5765', '在5765年6月 之前'],
            ['@#DHEBREW@ 15 ADS 5765', '5765年7月15日'],
            ['@#DHEBREW@ ADS 5765', '5765年7月'],
            ['ABT @#DHEBREW@ ADS 5765', '关于 5765年7月'],
            ['FROM @#DHEBREW@ ADS 5765', '从 5765年7月'],
            ['AFT @#DHEBREW@ ADS 5765', '在5765年7月 之后'],
            ['BEF @#DHEBREW@ ADS 5765', '在5765年7月 之前'],
            ['@#DHEBREW@ 15 NSN 5765', '5765年8月15日'],
            ['@#DHEBREW@ NSN 5765', '5765年8月'],
            ['ABT @#DHEBREW@ NSN 5765', '关于 5765年8月'],
            ['FROM @#DHEBREW@ NSN 5765', '从 5765年8月'],
            ['AFT @#DHEBREW@ NSN 5765', '在5765年8月 之后'],
            ['BEF @#DHEBREW@ NSN 5765', '在5765年8月 之前'],
            ['@#DHEBREW@ 15 IYR 5765', '5765年9月15日'],
            ['@#DHEBREW@ IYR 5765', '5765年9月'],
            ['ABT @#DHEBREW@ IYR 5765', '关于 5765年9月'],
            ['FROM @#DHEBREW@ IYR 5765', '从 5765年9月'],
            ['AFT @#DHEBREW@ IYR 5765', '在5765年9月 之后'],
            ['BEF @#DHEBREW@ IYR 5765', '在5765年9月 之前'],
            ['@#DHEBREW@ 15 SVN 5765', '5765年10月15日'],
            ['@#DHEBREW@ SVN 5765', '5765年10月'],
            ['ABT @#DHEBREW@ SVN 5765', '关于 5765年10月'],
            ['FROM @#DHEBREW@ SVN 5765', '从 5765年10月'],
            ['AFT @#DHEBREW@ SVN 5765', '在5765年10月 之后'],
            ['BEF @#DHEBREW@ SVN 5765', '在5765年10月 之前'],
            ['@#DHEBREW@ 15 TMZ 5765', '5765年11月15日'],
            ['@#DHEBREW@ TMZ 5765', '5765年11月'],
            ['ABT @#DHEBREW@ TMZ 5765', '关于 5765年11月'],
            ['FROM @#DHEBREW@ TMZ 5765', '从 5765年11月'],
            ['AFT @#DHEBREW@ TMZ 5765', '在5765年11月 之后'],
            ['BEF @#DHEBREW@ TMZ 5765', '在5765年11月 之前'],
            ['@#DHEBREW@ 15 AAV 5765', '5765年12月15日'],
            ['@#DHEBREW@ AAV 5765', '5765年12月'],
            ['ABT @#DHEBREW@ AAV 5765', '关于 5765年12月'],
            ['FROM @#DHEBREW@ AAV 5765', '从 5765年12月'],
            ['AFT @#DHEBREW@ AAV 5765', '在5765年12月 之后'],
            ['BEF @#DHEBREW@ AAV 5765', '在5765年12月 之前'],
            ['@#DHEBREW@ 15 ELL 5765', '5765年13月15日'],
            ['@#DHEBREW@ ELL 5765', '5765年13月'],
            ['ABT @#DHEBREW@ ELL 5765', '关于 5765年13月'],
            ['FROM @#DHEBREW@ ELL 5765', '从 5765年13月'],
            ['AFT @#DHEBREW@ ELL 5765', '在5765年13月 之后'],
            ['BEF @#DHEBREW@ ELL 5765', '在5765年13月 之前'],
            ['@#DHEBREW@ 5765', '5765年'],
            ['ABT @#DHEBREW@ 15 TSH 5765', '关于 5765年1月15日'],
            ['CAL @#DHEBREW@ 15 TSH 5765', '计算出 5765年1月15日'],
            ['EST @#DHEBREW@ 15 TSH 5765', '估计于5765年1月15日年'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '在5765年1月15日 之前'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '在5765年1月15日 之后'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '从 5765年1月15日'],
            ['TO @#DHEBREW@ 15 TSH 5765', '到 5765年1月15日'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '在5765年1月15日和5765年2月15日 间'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '从 5765年1月15日 到 5765年2月15日'],
            ['INT @#DHEBREW@ 15 TSH 5765', '解释 5765年1月15日'],
            ['@#DFRENCH R@ 15 VEND 12', 'An XII年1月15日'],
            ['@#DFRENCH R@ VEND 12', 'An XII年1月'],
            ['ABT @#DFRENCH R@ VEND 12', '关于 An XII年1月'],
            ['FROM @#DFRENCH R@ VEND 12', '从 An XII年1月'],
            ['AFT @#DFRENCH R@ VEND 12', '在An XII年1月 之后'],
            ['BEF @#DFRENCH R@ VEND 12', '在An XII年1月 之前'],
            ['@#DFRENCH R@ 15 BRUM 12', 'An XII年2月15日'],
            ['@#DFRENCH R@ BRUM 12', 'An XII年2月'],
            ['ABT @#DFRENCH R@ BRUM 12', '关于 An XII年2月'],
            ['FROM @#DFRENCH R@ BRUM 12', '从 An XII年2月'],
            ['AFT @#DFRENCH R@ BRUM 12', '在An XII年2月 之后'],
            ['BEF @#DFRENCH R@ BRUM 12', '在An XII年2月 之前'],
            ['@#DFRENCH R@ 15 FRIM 12', 'An XII年3月15日'],
            ['@#DFRENCH R@ FRIM 12', 'An XII年3月'],
            ['ABT @#DFRENCH R@ FRIM 12', '关于 An XII年3月'],
            ['FROM @#DFRENCH R@ FRIM 12', '从 An XII年3月'],
            ['AFT @#DFRENCH R@ FRIM 12', '在An XII年3月 之后'],
            ['BEF @#DFRENCH R@ FRIM 12', '在An XII年3月 之前'],
            ['@#DFRENCH R@ 15 NIVO 12', 'An XII年4月15日'],
            ['@#DFRENCH R@ NIVO 12', 'An XII年4月'],
            ['ABT @#DFRENCH R@ NIVO 12', '关于 An XII年4月'],
            ['FROM @#DFRENCH R@ NIVO 12', '从 An XII年4月'],
            ['AFT @#DFRENCH R@ NIVO 12', '在An XII年4月 之后'],
            ['BEF @#DFRENCH R@ NIVO 12', '在An XII年4月 之前'],
            ['@#DFRENCH R@ 15 PLUV 12', 'An XII年5月15日'],
            ['@#DFRENCH R@ PLUV 12', 'An XII年5月'],
            ['ABT @#DFRENCH R@ PLUV 12', '关于 An XII年5月'],
            ['FROM @#DFRENCH R@ PLUV 12', '从 An XII年5月'],
            ['AFT @#DFRENCH R@ PLUV 12', '在An XII年5月 之后'],
            ['BEF @#DFRENCH R@ PLUV 12', '在An XII年5月 之前'],
            ['@#DFRENCH R@ 15 VENT 12', 'An XII年6月15日'],
            ['@#DFRENCH R@ VENT 12', 'An XII年6月'],
            ['ABT @#DFRENCH R@ VENT 12', '关于 An XII年6月'],
            ['FROM @#DFRENCH R@ VENT 12', '从 An XII年6月'],
            ['AFT @#DFRENCH R@ VENT 12', '在An XII年6月 之后'],
            ['BEF @#DFRENCH R@ VENT 12', '在An XII年6月 之前'],
            ['@#DFRENCH R@ 15 GERM 12', 'An XII年7月15日'],
            ['@#DFRENCH R@ GERM 12', 'An XII年7月'],
            ['ABT @#DFRENCH R@ GERM 12', '关于 An XII年7月'],
            ['FROM @#DFRENCH R@ GERM 12', '从 An XII年7月'],
            ['AFT @#DFRENCH R@ GERM 12', '在An XII年7月 之后'],
            ['BEF @#DFRENCH R@ GERM 12', '在An XII年7月 之前'],
            ['@#DFRENCH R@ 15 FLOR 12', 'An XII年8月15日'],
            ['@#DFRENCH R@ FLOR 12', 'An XII年8月'],
            ['ABT @#DFRENCH R@ FLOR 12', '关于 An XII年8月'],
            ['FROM @#DFRENCH R@ FLOR 12', '从 An XII年8月'],
            ['AFT @#DFRENCH R@ FLOR 12', '在An XII年8月 之后'],
            ['BEF @#DFRENCH R@ FLOR 12', '在An XII年8月 之前'],
            ['@#DFRENCH R@ 15 PRAI 12', 'An XII年9月15日'],
            ['@#DFRENCH R@ PRAI 12', 'An XII年9月'],
            ['ABT @#DFRENCH R@ PRAI 12', '关于 An XII年9月'],
            ['FROM @#DFRENCH R@ PRAI 12', '从 An XII年9月'],
            ['AFT @#DFRENCH R@ PRAI 12', '在An XII年9月 之后'],
            ['BEF @#DFRENCH R@ PRAI 12', '在An XII年9月 之前'],
            ['@#DFRENCH R@ 15 MESS 12', 'An XII年10月15日'],
            ['@#DFRENCH R@ MESS 12', 'An XII年10月'],
            ['ABT @#DFRENCH R@ MESS 12', '关于 An XII年10月'],
            ['FROM @#DFRENCH R@ MESS 12', '从 An XII年10月'],
            ['AFT @#DFRENCH R@ MESS 12', '在An XII年10月 之后'],
            ['BEF @#DFRENCH R@ MESS 12', '在An XII年10月 之前'],
            ['@#DFRENCH R@ 15 THER 12', 'An XII年11月15日'],
            ['@#DFRENCH R@ THER 12', 'An XII年11月'],
            ['ABT @#DFRENCH R@ THER 12', '关于 An XII年11月'],
            ['FROM @#DFRENCH R@ THER 12', '从 An XII年11月'],
            ['AFT @#DFRENCH R@ THER 12', '在An XII年11月 之后'],
            ['BEF @#DFRENCH R@ THER 12', '在An XII年11月 之前'],
            ['@#DFRENCH R@ 15 FRUC 12', 'An XII年12月15日'],
            ['@#DFRENCH R@ FRUC 12', 'An XII年12月'],
            ['ABT @#DFRENCH R@ FRUC 12', '关于 An XII年12月'],
            ['FROM @#DFRENCH R@ FRUC 12', '从 An XII年12月'],
            ['AFT @#DFRENCH R@ FRUC 12', '在An XII年12月 之后'],
            ['BEF @#DFRENCH R@ FRUC 12', '在An XII年12月 之前'],
            ['@#DFRENCH R@ 15 COMP 12', 'An XII年13月15日'],
            ['@#DFRENCH R@ COMP 12', 'An XII年13月'],
            ['ABT @#DFRENCH R@ COMP 12', '关于 An XII年13月'],
            ['FROM @#DFRENCH R@ COMP 12', '从 An XII年13月'],
            ['AFT @#DFRENCH R@ COMP 12', '在An XII年13月 之后'],
            ['BEF @#DFRENCH R@ COMP 12', '在An XII年13月 之前'],
            ['@#DFRENCH R@ 12', 'An XII年'],
            ['ABT @#DFRENCH R@ 15 VEND 12', '关于 An XII年1月15日'],
            ['CAL @#DFRENCH R@ 15 VEND 12', '计算出 An XII年1月15日'],
            ['EST @#DFRENCH R@ 15 VEND 12', '估计于An XII年1月15日年'],
            ['BEF @#DFRENCH R@ 15 VEND 12', '在An XII年1月15日 之前'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '在An XII年1月15日 之后'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '从 An XII年1月15日'],
            ['TO @#DFRENCH R@ 15 VEND 12', '到 An XII年1月15日'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '在An XII年1月15日和An XII年2月15日 间'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '从 An XII年1月15日 到 An XII年2月15日'],
            ['INT @#DFRENCH R@ 15 VEND 12', '解释 An XII年1月15日'],
            ['@#DHIJRI@ 15 MUHAR 1425', '1425年1月15日'],
            ['@#DHIJRI@ MUHAR 1425', '1425年1月'],
            ['ABT @#DHIJRI@ MUHAR 1425', '关于 1425年1月'],
            ['FROM @#DHIJRI@ MUHAR 1425', '从 1425年1月'],
            ['AFT @#DHIJRI@ MUHAR 1425', '在1425年1月 之后'],
            ['BEF @#DHIJRI@ MUHAR 1425', '在1425年1月 之前'],
            ['@#DHIJRI@ 15 SAFAR 1425', '1425年2月15日'],
            ['@#DHIJRI@ SAFAR 1425', '1425年2月'],
            ['ABT @#DHIJRI@ SAFAR 1425', '关于 1425年2月'],
            ['FROM @#DHIJRI@ SAFAR 1425', '从 1425年2月'],
            ['AFT @#DHIJRI@ SAFAR 1425', '在1425年2月 之后'],
            ['BEF @#DHIJRI@ SAFAR 1425', '在1425年2月 之前'],
            ['@#DHIJRI@ 15 RABIA 1425', '1425年3月15日'],
            ['@#DHIJRI@ RABIA 1425', '1425年3月'],
            ['ABT @#DHIJRI@ RABIA 1425', '关于 1425年3月'],
            ['FROM @#DHIJRI@ RABIA 1425', '从 1425年3月'],
            ['AFT @#DHIJRI@ RABIA 1425', '在1425年3月 之后'],
            ['BEF @#DHIJRI@ RABIA 1425', '在1425年3月 之前'],
            ['@#DHIJRI@ 15 RABIT 1425', '1425年4月15日'],
            ['@#DHIJRI@ RABIT 1425', '1425年4月'],
            ['ABT @#DHIJRI@ RABIT 1425', '关于 1425年4月'],
            ['FROM @#DHIJRI@ RABIT 1425', '从 1425年4月'],
            ['AFT @#DHIJRI@ RABIT 1425', '在1425年4月 之后'],
            ['BEF @#DHIJRI@ RABIT 1425', '在1425年4月 之前'],
            ['@#DHIJRI@ 15 JUMAA 1425', '1425年5月15日'],
            ['@#DHIJRI@ JUMAA 1425', '1425年5月'],
            ['ABT @#DHIJRI@ JUMAA 1425', '关于 1425年5月'],
            ['FROM @#DHIJRI@ JUMAA 1425', '从 1425年5月'],
            ['AFT @#DHIJRI@ JUMAA 1425', '在1425年5月 之后'],
            ['BEF @#DHIJRI@ JUMAA 1425', '在1425年5月 之前'],
            ['@#DHIJRI@ 15 JUMAT 1425', '1425年6月15日'],
            ['@#DHIJRI@ JUMAT 1425', '1425年6月'],
            ['ABT @#DHIJRI@ JUMAT 1425', '关于 1425年6月'],
            ['FROM @#DHIJRI@ JUMAT 1425', '从 1425年6月'],
            ['AFT @#DHIJRI@ JUMAT 1425', '在1425年6月 之后'],
            ['BEF @#DHIJRI@ JUMAT 1425', '在1425年6月 之前'],
            ['@#DHIJRI@ 15 RAJAB 1425', '1425年7月15日'],
            ['@#DHIJRI@ RAJAB 1425', '1425年7月'],
            ['ABT @#DHIJRI@ RAJAB 1425', '关于 1425年7月'],
            ['FROM @#DHIJRI@ RAJAB 1425', '从 1425年7月'],
            ['AFT @#DHIJRI@ RAJAB 1425', '在1425年7月 之后'],
            ['BEF @#DHIJRI@ RAJAB 1425', '在1425年7月 之前'],
            ['@#DHIJRI@ 15 SHAAB 1425', '1425年8月15日'],
            ['@#DHIJRI@ SHAAB 1425', '1425年8月'],
            ['ABT @#DHIJRI@ SHAAB 1425', '关于 1425年8月'],
            ['FROM @#DHIJRI@ SHAAB 1425', '从 1425年8月'],
            ['AFT @#DHIJRI@ SHAAB 1425', '在1425年8月 之后'],
            ['BEF @#DHIJRI@ SHAAB 1425', '在1425年8月 之前'],
            ['@#DHIJRI@ 15 RAMAD 1425', '1425年9月15日'],
            ['@#DHIJRI@ RAMAD 1425', '1425年9月'],
            ['ABT @#DHIJRI@ RAMAD 1425', '关于 1425年9月'],
            ['FROM @#DHIJRI@ RAMAD 1425', '从 1425年9月'],
            ['AFT @#DHIJRI@ RAMAD 1425', '在1425年9月 之后'],
            ['BEF @#DHIJRI@ RAMAD 1425', '在1425年9月 之前'],
            ['@#DHIJRI@ 15 SHAWW 1425', '1425年10月15日'],
            ['@#DHIJRI@ SHAWW 1425', '1425年10月'],
            ['ABT @#DHIJRI@ SHAWW 1425', '关于 1425年10月'],
            ['FROM @#DHIJRI@ SHAWW 1425', '从 1425年10月'],
            ['AFT @#DHIJRI@ SHAWW 1425', '在1425年10月 之后'],
            ['BEF @#DHIJRI@ SHAWW 1425', '在1425年10月 之前'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '1425年11月15日'],
            ['@#DHIJRI@ DHUAQ 1425', '1425年11月'],
            ['ABT @#DHIJRI@ DHUAQ 1425', '关于 1425年11月'],
            ['FROM @#DHIJRI@ DHUAQ 1425', '从 1425年11月'],
            ['AFT @#DHIJRI@ DHUAQ 1425', '在1425年11月 之后'],
            ['BEF @#DHIJRI@ DHUAQ 1425', '在1425年11月 之前'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425年'],
            ['@#DHIJRI@ DHUAL 1425', '1425年'],
            ['ABT @#DHIJRI@ DHUAL 1425', '关于 1425年'],
            ['FROM @#DHIJRI@ DHUAL 1425', '从 1425年'],
            ['AFT @#DHIJRI@ DHUAL 1425', '在1425年 之后'],
            ['BEF @#DHIJRI@ DHUAL 1425', '在1425年 之前'],
            ['@#DHIJRI@ 1425', '1425年'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', '关于 1425年1月15日'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', '计算出 1425年1月15日'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', '估计于1425年1月15日年'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '在1425年1月15日 之前'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '在1425年1月15日 之后'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '从 1425年1月15日'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '到 1425年1月15日'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '在1425年1月15日和1425年2月15日 间'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '从 1425年1月15日 到 1425年2月15日'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', '解释 1425年1月15日'],
            ['@#DJALALI@ 15 FARVA 1384', '1384年1月15日'],
            ['@#DJALALI@ FARVA 1384', '1384年1月'],
            ['ABT @#DJALALI@ FARVA 1384', '关于 1384年1月'],
            ['FROM @#DJALALI@ FARVA 1384', '从 1384年1月'],
            ['AFT @#DJALALI@ FARVA 1384', '在1384年1月 之后'],
            ['BEF @#DJALALI@ FARVA 1384', '在1384年1月 之前'],
            ['@#DJALALI@ 15 ORDIB 1384', '1384年2月15日'],
            ['@#DJALALI@ ORDIB 1384', '1384年2月'],
            ['ABT @#DJALALI@ ORDIB 1384', '关于 1384年2月'],
            ['FROM @#DJALALI@ ORDIB 1384', '从 1384年2月'],
            ['AFT @#DJALALI@ ORDIB 1384', '在1384年2月 之后'],
            ['BEF @#DJALALI@ ORDIB 1384', '在1384年2月 之前'],
            ['@#DJALALI@ 15 KHORD 1384', '1384年3月15日'],
            ['@#DJALALI@ KHORD 1384', '1384年3月'],
            ['ABT @#DJALALI@ KHORD 1384', '关于 1384年3月'],
            ['FROM @#DJALALI@ KHORD 1384', '从 1384年3月'],
            ['AFT @#DJALALI@ KHORD 1384', '在1384年3月 之后'],
            ['BEF @#DJALALI@ KHORD 1384', '在1384年3月 之前'],
            ['@#DJALALI@ 15 TIR 1384', '1384年4月15日'],
            ['@#DJALALI@ TIR 1384', '1384年4月'],
            ['ABT @#DJALALI@ TIR 1384', '关于 1384年4月'],
            ['FROM @#DJALALI@ TIR 1384', '从 1384年4月'],
            ['AFT @#DJALALI@ TIR 1384', '在1384年4月 之后'],
            ['BEF @#DJALALI@ TIR 1384', '在1384年4月 之前'],
            ['@#DJALALI@ 15 MORDA 1384', '1384年5月15日'],
            ['@#DJALALI@ MORDA 1384', '1384年5月'],
            ['ABT @#DJALALI@ MORDA 1384', '关于 1384年5月'],
            ['FROM @#DJALALI@ MORDA 1384', '从 1384年5月'],
            ['AFT @#DJALALI@ MORDA 1384', '在1384年5月 之后'],
            ['BEF @#DJALALI@ MORDA 1384', '在1384年5月 之前'],
            ['@#DJALALI@ 15 SHAHR 1384', '1384年6月15日'],
            ['@#DJALALI@ SHAHR 1384', '1384年6月'],
            ['ABT @#DJALALI@ SHAHR 1384', '关于 1384年6月'],
            ['FROM @#DJALALI@ SHAHR 1384', '从 1384年6月'],
            ['AFT @#DJALALI@ SHAHR 1384', '在1384年6月 之后'],
            ['BEF @#DJALALI@ SHAHR 1384', '在1384年6月 之前'],
            ['@#DJALALI@ 15 MEHR 1384', '1384年7月15日'],
            ['@#DJALALI@ MEHR 1384', '1384年7月'],
            ['ABT @#DJALALI@ MEHR 1384', '关于 1384年7月'],
            ['FROM @#DJALALI@ MEHR 1384', '从 1384年7月'],
            ['AFT @#DJALALI@ MEHR 1384', '在1384年7月 之后'],
            ['BEF @#DJALALI@ MEHR 1384', '在1384年7月 之前'],
            ['@#DJALALI@ 15 ABAN 1384', '1384年8月15日'],
            ['@#DJALALI@ ABAN 1384', '1384年8月'],
            ['ABT @#DJALALI@ ABAN 1384', '关于 1384年8月'],
            ['FROM @#DJALALI@ ABAN 1384', '从 1384年8月'],
            ['AFT @#DJALALI@ ABAN 1384', '在1384年8月 之后'],
            ['BEF @#DJALALI@ ABAN 1384', '在1384年8月 之前'],
            ['@#DJALALI@ 15 AZAR 1384', '1384年9月15日'],
            ['@#DJALALI@ AZAR 1384', '1384年9月'],
            ['ABT @#DJALALI@ AZAR 1384', '关于 1384年9月'],
            ['FROM @#DJALALI@ AZAR 1384', '从 1384年9月'],
            ['AFT @#DJALALI@ AZAR 1384', '在1384年9月 之后'],
            ['BEF @#DJALALI@ AZAR 1384', '在1384年9月 之前'],
            ['@#DJALALI@ 15 DEY 1384', '1384年10月15日'],
            ['@#DJALALI@ DEY 1384', '1384年10月'],
            ['ABT @#DJALALI@ DEY 1384', '关于 1384年10月'],
            ['FROM @#DJALALI@ DEY 1384', '从 1384年10月'],
            ['AFT @#DJALALI@ DEY 1384', '在1384年10月 之后'],
            ['BEF @#DJALALI@ DEY 1384', '在1384年10月 之前'],
            ['@#DJALALI@ 15 BAHMA 1384', '1384年11月15日'],
            ['@#DJALALI@ BAHMA 1384', '1384年11月'],
            ['ABT @#DJALALI@ BAHMA 1384', '关于 1384年11月'],
            ['FROM @#DJALALI@ BAHMA 1384', '从 1384年11月'],
            ['AFT @#DJALALI@ BAHMA 1384', '在1384年11月 之后'],
            ['BEF @#DJALALI@ BAHMA 1384', '在1384年11月 之前'],
            ['@#DJALALI@ 15 ESFAN 1384', '1384年12月15日'],
            ['@#DJALALI@ ESFAN 1384', '1384年12月'],
            ['ABT @#DJALALI@ ESFAN 1384', '关于 1384年12月'],
            ['FROM @#DJALALI@ ESFAN 1384', '从 1384年12月'],
            ['AFT @#DJALALI@ ESFAN 1384', '在1384年12月 之后'],
            ['BEF @#DJALALI@ ESFAN 1384', '在1384年12月 之前'],
            ['@#DJALALI@ 1384', '1384年'],
            ['ABT @#DJALALI@ 15 FARVA 1384', '关于 1384年1月15日'],
            ['CAL @#DJALALI@ 15 FARVA 1384', '计算出 1384年1月15日'],
            ['EST @#DJALALI@ 15 FARVA 1384', '估计于1384年1月15日年'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '在1384年1月15日 之前'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '在1384年1月15日 之后'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '从 1384年1月15日'],
            ['TO @#DJALALI@ 15 FARVA 1384', '到 1384年1月15日'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '在1384年1月15日和1384年2月15日 间'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '从 1384年1月15日 到 1384年2月15日'],
            ['INT @#DJALALI@ 15 FARVA 1384', '解释 1384年1月15日'],
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
        self::assertSame('one和two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one、two和three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one或two', $language->formatListOr(['one', 'two']));
        self::assertSame('one、two或three', $language->formatListOr(['one', 'two', 'three']));
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

        // Nieces/nephews — from brother (brother's children)
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");

        // Nieces/nephews — from sister (sister's children)
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins from father's brother (堂 cousins)
        $tangGe = self::male('tg', "1 FAMC @fbro@\n1 BIRT\n2 DATE 1 JAN 1998"); // older than son
        $tangDi = self::male('td', "1 FAMC @fbro@\n1 BIRT\n2 DATE 1 JAN 2002"); // younger than son

        // Cousins from father's sister (表 cousins)
        $biaoJie = self::female('bj', "1 FAMC @fsis@\n1 BIRT\n2 DATE 1 JAN 1998"); // older than son
        $biaoMei = self::female('bm', "1 FAMC @fsis@\n1 BIRT\n2 DATE 1 JAN 2002"); // younger than son

        // Cousins from mother's brother (表 cousins)
        $biaoGe = self::male('bg', "1 FAMC @fbrow@\n1 BIRT\n2 DATE 1 JAN 1998");  // older than son

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
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 MARR Y\n1 HUSB @bh@\n1 WIFE @wbh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @tg@\n1 CHIL @td@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 MARR Y\n1 HUSB @hsh@\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @bj@\n1 CHIL @bm@");
        $fbrow = self::family('fbrow', "0 @fbrow@ FAM\n1 MARR Y\n1 HUSB @bw@\n1 WIFE @wbw@\n1 CHIL @bg@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $tangGe, $tangDi, $biaoJie, $biaoMei, $biaoGe,
             $wifeOfBro, $husbandOfSis, $wifeOfBroW,
             $grandsonFromSon, $granddaughterFromDau,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fbrow, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('妻子', '丈夫', [$husband, $fm, $wife]);
        self::assertRelationshipNames('前夫', '前妻', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('未婚妻', '未婚夫', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('母亲', '儿子', [$son, $fm, $wife]);
        self::assertRelationshipNames('父亲', '儿子', [$son, $fm, $husband]);
        self::assertRelationshipNames('母亲', '女儿', [$daughter, $fm, $wife]);

        // Adopted / Fostered
        self::assertRelationshipNames('养母', '养子', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('寄养母亲', '寄养女', [$fosterDaughter, $fd, $wife]);

        // Siblings — elder/younger (daughter born 1998 is older than son born 2000)
        self::assertRelationshipNames('姐姐', '弟弟', [$son, $fm, $daughter]);

        // Stepfamily
        self::assertRelationshipName('继父', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('继女', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('岳母', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('岳父', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('婆婆', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('公公', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('儿媳', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('女婿', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('姨子', [$husband, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('舅子', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('姑子', [$wife, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('夫之兄弟', [$wife, $fm, $husband, $fp, $brotherOfH]);

        // In-laws — sibling's spouse
        self::assertRelationshipName('嫂子', [$husband, $fp, $brotherOfH, $fbro, $wifeOfBro]);
        self::assertRelationshipName('姐夫', [$husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);

        // Grandparents — paternal
        self::assertRelationshipName('奶奶', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('爷爷', [$son, $fm, $husband, $fp, $fatherOfH]);
        // Grandparents — maternal
        self::assertRelationshipName('外婆', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('外公', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren — son's children
        self::assertRelationshipName('孙子', [$husband, $fm, $son, $fson, $grandsonFromSon]);
        // Grandchildren — daughter's children
        self::assertRelationshipName('外孙女', [$husband, $fm, $daughter, $fdau, $granddaughterFromDau]);

        // Aunts/Uncles — paternal
        self::assertRelationshipName('姑姑', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('叔伯', [$son, $fm, $husband, $fp, $brotherOfH]);
        // Aunts/Uncles — maternal
        self::assertRelationshipName('姨妈', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('舅舅', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Uncle/aunt's spouses
        self::assertRelationshipName('姑父', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);
        self::assertRelationshipName('婶母', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $wifeOfBro]);
        self::assertRelationshipName('舅妈', [$son, $fm, $wife, $fw, $brotherOfW, $fbrow, $wifeOfBroW]);

        // Nieces/Nephews — brother's children
        self::assertRelationshipName('侄女', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('侄子', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        // Nieces/Nephews — sister's children
        self::assertRelationshipName('外甥女', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('外甥', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — 堂 (paternal uncle's children) with older/younger
        self::assertRelationshipName('堂哥', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $tangGe]); // born 1998, older than son
        self::assertRelationshipName('堂弟', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $tangDi]); // born 2002, younger than son

        // Cousins — 表 (paternal aunt's children) with older/younger
        self::assertRelationshipName('表姐', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $biaoJie]); // born 1998, older than son
        self::assertRelationshipName('表妹', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $biaoMei]); // born 2002, younger than son

        // Cousins — 表 (maternal uncle's children)
        self::assertRelationshipName('表哥', [$son, $fm, $wife, $fw, $brotherOfW, $fbrow, $biaoGe]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('曾祖母', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('曾祖父', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('2世姑姨', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('2世叔舅', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
