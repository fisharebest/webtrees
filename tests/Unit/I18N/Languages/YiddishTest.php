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
use Fisharebest\Webtrees\I18N\Languages\Yiddish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Yiddish::class)]
class YiddishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Yiddish();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Hebr, self::language()->script());
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
        self::assertSame(['א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'כ', 'ל', 'מ', 'נ', 'ס', 'ע', 'פ', 'צ', 'ק', 'ר', 'ש', 'ת'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('yi', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('ייִדיש', self::language()->endonym());
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
            ['15 JAN 2000', '15 יאַנואַר 2000'],
            ['JAN 2000', 'יאַנואַר 2000'],
            ['ABT JAN 2000', 'וועגן יאַנואַר 2000'],
            ['FROM JAN 2000', 'פון יאַנואַר 2000'],
            ['AFT JAN 2000', 'נאָך יאַנואַר 2000'],
            ['BEF JAN 2000', 'פאַר יאַנואַר 2000'],
            ['15 FEB 2000', '15 פעברואַר 2000'],
            ['FEB 2000', 'פעברואַר 2000'],
            ['ABT FEB 2000', 'וועגן פעברואַר 2000'],
            ['FROM FEB 2000', 'פון פעברואַר 2000'],
            ['AFT FEB 2000', 'נאָך פעברואַר 2000'],
            ['BEF FEB 2000', 'פאַר פעברואַר 2000'],
            ['15 MAR 2000', '15 מאַרץ 2000'],
            ['MAR 2000', 'מאַרץ 2000'],
            ['ABT MAR 2000', 'וועגן מאַרץ 2000'],
            ['FROM MAR 2000', 'פון מאַרץ 2000'],
            ['AFT MAR 2000', 'נאָך מאַרץ 2000'],
            ['BEF MAR 2000', 'פאַר מאַרץ 2000'],
            ['15 APR 2000', '15 אַפּריל 2000'],
            ['APR 2000', 'אַפּריל 2000'],
            ['ABT APR 2000', 'וועגן אַפּריל 2000'],
            ['FROM APR 2000', 'פון אַפּריל 2000'],
            ['AFT APR 2000', 'נאָך אַפּריל 2000'],
            ['BEF APR 2000', 'פאַר אַפּריל 2000'],
            ['15 MAY 2000', '15 מייַ 2000'],
            ['MAY 2000', 'מייַ 2000'],
            ['ABT MAY 2000', 'וועגן מייַ 2000'],
            ['FROM MAY 2000', 'פון מייַ 2000'],
            ['AFT MAY 2000', 'נאָך מייַ 2000'],
            ['BEF MAY 2000', 'פאַר מייַ 2000'],
            ['15 JUN 2000', '15 יוני 2000'],
            ['JUN 2000', 'יוני 2000'],
            ['ABT JUN 2000', 'וועגן יוני 2000'],
            ['FROM JUN 2000', 'פון יוני 2000'],
            ['AFT JUN 2000', 'נאָך יוני 2000'],
            ['BEF JUN 2000', 'פאַר יוני 2000'],
            ['15 JUL 2000', '15 יולי 2000'],
            ['JUL 2000', 'יולי 2000'],
            ['ABT JUL 2000', 'וועגן יולי 2000'],
            ['FROM JUL 2000', 'פון יולי 2000'],
            ['AFT JUL 2000', 'נאָך יולי 2000'],
            ['BEF JUL 2000', 'פאַר יולי 2000'],
            ['15 AUG 2000', '15 אויגוסט 2000'],
            ['AUG 2000', 'אויגוסט 2000'],
            ['ABT AUG 2000', 'וועגן אויגוסט 2000'],
            ['FROM AUG 2000', 'פון אויגוסט 2000'],
            ['AFT AUG 2000', 'נאָך אויגוסט 2000'],
            ['BEF AUG 2000', 'פאַר אויגוסט 2000'],
            ['15 SEP 2000', '15 סעפּטעמבער 2000'],
            ['SEP 2000', 'סעפּטעמבער 2000'],
            ['ABT SEP 2000', 'וועגן סעפּטעמבער 2000'],
            ['FROM SEP 2000', 'פון סעפּטעמבער 2000'],
            ['AFT SEP 2000', 'נאָך סעפּטעמבער 2000'],
            ['BEF SEP 2000', 'פאַר סעפּטעמבער 2000'],
            ['15 OCT 2000', '15 אָקטאָבער 2000'],
            ['OCT 2000', 'אָקטאָבער 2000'],
            ['ABT OCT 2000', 'וועגן אָקטאָבער 2000'],
            ['FROM OCT 2000', 'פון אָקטאָבער 2000'],
            ['AFT OCT 2000', 'נאָך אָקטאָבער 2000'],
            ['BEF OCT 2000', 'פאַר אָקטאָבער 2000'],
            ['15 NOV 2000', '15 נאוועמבער 2000'],
            ['NOV 2000', 'נאוועמבער 2000'],
            ['ABT NOV 2000', 'וועגן נאוועמבער 2000'],
            ['FROM NOV 2000', 'פון נאוועמבער 2000'],
            ['AFT NOV 2000', 'נאָך נאוועמבער 2000'],
            ['BEF NOV 2000', 'פאַר נאוועמבער 2000'],
            ['15 DEC 2000', '15 דעצעמבער 2000'],
            ['DEC 2000', 'דעצעמבער 2000'],
            ['ABT DEC 2000', 'וועגן דעצעמבער 2000'],
            ['FROM DEC 2000', 'פון דעצעמבער 2000'],
            ['AFT DEC 2000', 'נאָך דעצעמבער 2000'],
            ['BEF DEC 2000', 'פאַר דעצעמבער 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'וועגן 15 יאַנואַר 2000'],
            ['CAL 15 JAN 2000', 'אויסגערעכנט 15 יאַנואַר 2000'],
            ['EST 15 JAN 2000', 'ווערט שאַצט 15 יאַנואַר 2000'],
            ['BEF 15 JAN 2000', 'פאַר 15 יאַנואַר 2000'],
            ['AFT 15 JAN 2000', 'נאָך 15 יאַנואַר 2000'],
            ['FROM 15 JAN 2000', 'פון 15 יאַנואַר 2000'],
            ['TO 15 JAN 2000', 'צו 15 יאַנואַר 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'צווישן 15 יאַנואַר 2000 און 15 פעברואַר 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'פון 15 יאַנואַר 2000 צו 15 פעברואַר 2000'],
            ['INT 15 JAN 2000', 'אינטערפּרעטאַציע 15 יאַנואַר 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 יאַנואַר 1700 נ"ק'],
            ['@#DJULIAN@ JAN 1700', 'יאַנואַר 1700 נ"ק'],
            ['ABT @#DJULIAN@ JAN 1700', 'וועגן יאַנואַר 1700 נ"ק'],
            ['FROM @#DJULIAN@ JAN 1700', 'פון יאַנואַר 1700 נ"ק'],
            ['AFT @#DJULIAN@ JAN 1700', 'נאָך יאַנואַר 1700 נ"ק'],
            ['BEF @#DJULIAN@ JAN 1700', 'פאַר יאַנואַר 1700 נ"ק'],
            ['@#DJULIAN@ 15 FEB 1700', '15 פעברואַר 1700 נ"ק'],
            ['@#DJULIAN@ FEB 1700', 'פעברואַר 1700 נ"ק'],
            ['ABT @#DJULIAN@ FEB 1700', 'וועגן פעברואַר 1700 נ"ק'],
            ['FROM @#DJULIAN@ FEB 1700', 'פון פעברואַר 1700 נ"ק'],
            ['AFT @#DJULIAN@ FEB 1700', 'נאָך פעברואַר 1700 נ"ק'],
            ['BEF @#DJULIAN@ FEB 1700', 'פאַר פעברואַר 1700 נ"ק'],
            ['@#DJULIAN@ 15 MAR 1700', '15 מאַרץ 1700 נ"ק'],
            ['@#DJULIAN@ MAR 1700', 'מאַרץ 1700 נ"ק'],
            ['ABT @#DJULIAN@ MAR 1700', 'וועגן מאַרץ 1700 נ"ק'],
            ['FROM @#DJULIAN@ MAR 1700', 'פון מאַרץ 1700 נ"ק'],
            ['AFT @#DJULIAN@ MAR 1700', 'נאָך מאַרץ 1700 נ"ק'],
            ['BEF @#DJULIAN@ MAR 1700', 'פאַר מאַרץ 1700 נ"ק'],
            ['@#DJULIAN@ 15 APR 1700', '15 אַפּריל 1700 נ"ק'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 אַפּריל 1645/46 נ"ק'],
            ['@#DJULIAN@ APR 1700', 'אַפּריל 1700 נ"ק'],
            ['ABT @#DJULIAN@ APR 1700', 'וועגן אַפּריל 1700 נ"ק'],
            ['FROM @#DJULIAN@ APR 1700', 'פון אַפּריל 1700 נ"ק'],
            ['AFT @#DJULIAN@ APR 1700', 'נאָך אַפּריל 1700 נ"ק'],
            ['BEF @#DJULIAN@ APR 1700', 'פאַר אַפּריל 1700 נ"ק'],
            ['@#DJULIAN@ 15 MAY 1700', '15 מייַ 1700 נ"ק'],
            ['@#DJULIAN@ MAY 1700', 'מייַ 1700 נ"ק'],
            ['ABT @#DJULIAN@ MAY 1700', 'וועגן מייַ 1700 נ"ק'],
            ['FROM @#DJULIAN@ MAY 1700', 'פון מייַ 1700 נ"ק'],
            ['AFT @#DJULIAN@ MAY 1700', 'נאָך מייַ 1700 נ"ק'],
            ['BEF @#DJULIAN@ MAY 1700', 'פאַר מייַ 1700 נ"ק'],
            ['@#DJULIAN@ 15 JUN 1700', '15 יוני 1700 נ"ק'],
            ['@#DJULIAN@ JUN 1700', 'יוני 1700 נ"ק'],
            ['ABT @#DJULIAN@ JUN 1700', 'וועגן יוני 1700 נ"ק'],
            ['FROM @#DJULIAN@ JUN 1700', 'פון יוני 1700 נ"ק'],
            ['AFT @#DJULIAN@ JUN 1700', 'נאָך יוני 1700 נ"ק'],
            ['BEF @#DJULIAN@ JUN 1700', 'פאַר יוני 1700 נ"ק'],
            ['@#DJULIAN@ 15 JUL 1700', '15 יולי 1700 נ"ק'],
            ['@#DJULIAN@ JUL 1700', 'יולי 1700 נ"ק'],
            ['ABT @#DJULIAN@ JUL 1700', 'וועגן יולי 1700 נ"ק'],
            ['FROM @#DJULIAN@ JUL 1700', 'פון יולי 1700 נ"ק'],
            ['AFT @#DJULIAN@ JUL 1700', 'נאָך יולי 1700 נ"ק'],
            ['BEF @#DJULIAN@ JUL 1700', 'פאַר יולי 1700 נ"ק'],
            ['@#DJULIAN@ 15 AUG 1700', '15 אויגוסט 1700 נ"ק'],
            ['@#DJULIAN@ AUG 1700', 'אויגוסט 1700 נ"ק'],
            ['ABT @#DJULIAN@ AUG 1700', 'וועגן אויגוסט 1700 נ"ק'],
            ['FROM @#DJULIAN@ AUG 1700', 'פון אויגוסט 1700 נ"ק'],
            ['AFT @#DJULIAN@ AUG 1700', 'נאָך אויגוסט 1700 נ"ק'],
            ['BEF @#DJULIAN@ AUG 1700', 'פאַר אויגוסט 1700 נ"ק'],
            ['@#DJULIAN@ 15 SEP 1700', '15 סעפּטעמבער 1700 נ"ק'],
            ['@#DJULIAN@ SEP 1700', 'סעפּטעמבער 1700 נ"ק'],
            ['ABT @#DJULIAN@ SEP 1700', 'וועגן סעפּטעמבער 1700 נ"ק'],
            ['FROM @#DJULIAN@ SEP 1700', 'פון סעפּטעמבער 1700 נ"ק'],
            ['AFT @#DJULIAN@ SEP 1700', 'נאָך סעפּטעמבער 1700 נ"ק'],
            ['BEF @#DJULIAN@ SEP 1700', 'פאַר סעפּטעמבער 1700 נ"ק'],
            ['@#DJULIAN@ 15 OCT 1700', '15 אָקטאָבער 1700 נ"ק'],
            ['@#DJULIAN@ OCT 1700', 'אָקטאָבער 1700 נ"ק'],
            ['ABT @#DJULIAN@ OCT 1700', 'וועגן אָקטאָבער 1700 נ"ק'],
            ['FROM @#DJULIAN@ OCT 1700', 'פון אָקטאָבער 1700 נ"ק'],
            ['AFT @#DJULIAN@ OCT 1700', 'נאָך אָקטאָבער 1700 נ"ק'],
            ['BEF @#DJULIAN@ OCT 1700', 'פאַר אָקטאָבער 1700 נ"ק'],
            ['@#DJULIAN@ 15 NOV 1700', '15 נאוועמבער 1700 נ"ק'],
            ['@#DJULIAN@ NOV 1700', 'נאוועמבער 1700 נ"ק'],
            ['ABT @#DJULIAN@ NOV 1700', 'וועגן נאוועמבער 1700 נ"ק'],
            ['FROM @#DJULIAN@ NOV 1700', 'פון נאוועמבער 1700 נ"ק'],
            ['AFT @#DJULIAN@ NOV 1700', 'נאָך נאוועמבער 1700 נ"ק'],
            ['BEF @#DJULIAN@ NOV 1700', 'פאַר נאוועמבער 1700 נ"ק'],
            ['@#DJULIAN@ 15 DEC 1700', '15 דעצעמבער 1700 נ"ק'],
            ['@#DJULIAN@ DEC 1700', 'דעצעמבער 1700 נ"ק'],
            ['ABT @#DJULIAN@ DEC 1700', 'וועגן דעצעמבער 1700 נ"ק'],
            ['FROM @#DJULIAN@ DEC 1700', 'פון דעצעמבער 1700 נ"ק'],
            ['AFT @#DJULIAN@ DEC 1700', 'נאָך דעצעמבער 1700 נ"ק'],
            ['BEF @#DJULIAN@ DEC 1700', 'פאַר דעצעמבער 1700 נ"ק'],
            ['@#DJULIAN@ 1700', '1700 נ"ק'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'וועגן 15 יאַנואַר 1700 נ"ק'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'אויסגערעכנט 15 יאַנואַר 1700 נ"ק'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'ווערט שאַצט 15 יאַנואַר 1700 נ"ק'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'פאַר 15 יאַנואַר 1700 נ"ק'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'נאָך 15 יאַנואַר 1700 נ"ק'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'פון 15 יאַנואַר 1700 נ"ק'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'צו 15 יאַנואַר 1700 נ"ק'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'צווישן 15 יאַנואַר 1700 נ"ק און 15 פעברואַר 1700 נ"ק'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'פון 15 יאַנואַר 1700 נ"ק צו 15 פעברואַר 1700 נ"ק'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'אינטערפּרעטאַציע 15 יאַנואַר 1700 נ"ק'],
            ['@#DHEBREW@ 15 TSH 5765', 'ט״ו תשרי ה׳תשס״ה'],
            ['@#DHEBREW@ TSH 5765', 'תשרי ה׳תשס״ה'],
            ['ABT @#DHEBREW@ TSH 5765', 'וועגן תשרי ה׳תשס״ה'],
            ['FROM @#DHEBREW@ TSH 5765', 'פון תשרי ה׳תשס״ה'],
            ['AFT @#DHEBREW@ TSH 5765', 'נאָך תשרי ה׳תשס״ה'],
            ['BEF @#DHEBREW@ TSH 5765', 'פאַר תשרי ה׳תשס״ה'],
            ['@#DHEBREW@ 15 CSH 5765', 'ט״ו מרחשון ה׳תשס״ה'],
            ['@#DHEBREW@ CSH 5765', 'מרחשון ה׳תשס״ה'],
            ['ABT @#DHEBREW@ CSH 5765', 'וועגן מרחשון ה׳תשס״ה'],
            ['FROM @#DHEBREW@ CSH 5765', 'פון מרחשון ה׳תשס״ה'],
            ['AFT @#DHEBREW@ CSH 5765', 'נאָך מרחשון ה׳תשס״ה'],
            ['BEF @#DHEBREW@ CSH 5765', 'פאַר מרחשון ה׳תשס״ה'],
            ['@#DHEBREW@ 15 KSL 5765', 'ט״ו כסלו ה׳תשס״ה'],
            ['@#DHEBREW@ KSL 5765', 'כסלו ה׳תשס״ה'],
            ['ABT @#DHEBREW@ KSL 5765', 'וועגן כסלו ה׳תשס״ה'],
            ['FROM @#DHEBREW@ KSL 5765', 'פון כסלו ה׳תשס״ה'],
            ['AFT @#DHEBREW@ KSL 5765', 'נאָך כסלו ה׳תשס״ה'],
            ['BEF @#DHEBREW@ KSL 5765', 'פאַר כסלו ה׳תשס״ה'],
            ['@#DHEBREW@ 15 TVT 5765', 'ט״ו טבת ה׳תשס״ה'],
            ['@#DHEBREW@ TVT 5765', 'טבת ה׳תשס״ה'],
            ['ABT @#DHEBREW@ TVT 5765', 'וועגן טבת ה׳תשס״ה'],
            ['FROM @#DHEBREW@ TVT 5765', 'פון טבת ה׳תשס״ה'],
            ['AFT @#DHEBREW@ TVT 5765', 'נאָך טבת ה׳תשס״ה'],
            ['BEF @#DHEBREW@ TVT 5765', 'פאַר טבת ה׳תשס״ה'],
            ['@#DHEBREW@ 15 SHV 5765', 'ט״ו שבט ה׳תשס״ה'],
            ['@#DHEBREW@ SHV 5765', 'שבט ה׳תשס״ה'],
            ['ABT @#DHEBREW@ SHV 5765', 'וועגן שבט ה׳תשס״ה'],
            ['FROM @#DHEBREW@ SHV 5765', 'פון שבט ה׳תשס״ה'],
            ['AFT @#DHEBREW@ SHV 5765', 'נאָך שבט ה׳תשס״ה'],
            ['BEF @#DHEBREW@ SHV 5765', 'פאַר שבט ה׳תשס״ה'],
            ['@#DHEBREW@ 15 ADR 5765', 'ט״ו אדר א׳ ה׳תשס״ה'],
            ['@#DHEBREW@ ADR 5765', 'אדר א׳ ה׳תשס״ה'],
            ['ABT @#DHEBREW@ ADR 5765', 'וועגן אדר א׳ ה׳תשס״ה'],
            ['FROM @#DHEBREW@ ADR 5765', 'פון אדר א׳ ה׳תשס״ה'],
            ['AFT @#DHEBREW@ ADR 5765', 'נאָך אדר א׳ ה׳תשס״ה'],
            ['BEF @#DHEBREW@ ADR 5765', 'פאַר אדר א׳ ה׳תשס״ה'],
            ['@#DHEBREW@ 15 ADS 5765', 'ט״ו אדר ב׳ ה׳תשס״ה'],
            ['@#DHEBREW@ ADS 5765', 'אדר ב׳ ה׳תשס״ה'],
            ['ABT @#DHEBREW@ ADS 5765', 'וועגן אדר ב׳ ה׳תשס״ה'],
            ['FROM @#DHEBREW@ ADS 5765', 'פון אדר ב׳ ה׳תשס״ה'],
            ['AFT @#DHEBREW@ ADS 5765', 'נאָך אדר ב׳ ה׳תשס״ה'],
            ['BEF @#DHEBREW@ ADS 5765', 'פאַר אדר ב׳ ה׳תשס״ה'],
            ['@#DHEBREW@ 15 NSN 5765', 'ט״ו ניסן ה׳תשס״ה'],
            ['@#DHEBREW@ NSN 5765', 'ניסן ה׳תשס״ה'],
            ['ABT @#DHEBREW@ NSN 5765', 'וועגן ניסן ה׳תשס״ה'],
            ['FROM @#DHEBREW@ NSN 5765', 'פון ניסן ה׳תשס״ה'],
            ['AFT @#DHEBREW@ NSN 5765', 'נאָך ניסן ה׳תשס״ה'],
            ['BEF @#DHEBREW@ NSN 5765', 'פאַר ניסן ה׳תשס״ה'],
            ['@#DHEBREW@ 15 IYR 5765', 'ט״ו אייר ה׳תשס״ה'],
            ['@#DHEBREW@ IYR 5765', 'אייר ה׳תשס״ה'],
            ['ABT @#DHEBREW@ IYR 5765', 'וועגן אייר ה׳תשס״ה'],
            ['FROM @#DHEBREW@ IYR 5765', 'פון אייר ה׳תשס״ה'],
            ['AFT @#DHEBREW@ IYR 5765', 'נאָך אייר ה׳תשס״ה'],
            ['BEF @#DHEBREW@ IYR 5765', 'פאַר אייר ה׳תשס״ה'],
            ['@#DHEBREW@ 15 SVN 5765', 'ט״ו סיון ה׳תשס״ה'],
            ['@#DHEBREW@ SVN 5765', 'סיון ה׳תשס״ה'],
            ['ABT @#DHEBREW@ SVN 5765', 'וועגן סיון ה׳תשס״ה'],
            ['FROM @#DHEBREW@ SVN 5765', 'פון סיון ה׳תשס״ה'],
            ['AFT @#DHEBREW@ SVN 5765', 'נאָך סיון ה׳תשס״ה'],
            ['BEF @#DHEBREW@ SVN 5765', 'פאַר סיון ה׳תשס״ה'],
            ['@#DHEBREW@ 15 TMZ 5765', 'ט״ו תמוז ה׳תשס״ה'],
            ['@#DHEBREW@ TMZ 5765', 'תמוז ה׳תשס״ה'],
            ['ABT @#DHEBREW@ TMZ 5765', 'וועגן תמוז ה׳תשס״ה'],
            ['FROM @#DHEBREW@ TMZ 5765', 'פון תמוז ה׳תשס״ה'],
            ['AFT @#DHEBREW@ TMZ 5765', 'נאָך תמוז ה׳תשס״ה'],
            ['BEF @#DHEBREW@ TMZ 5765', 'פאַר תמוז ה׳תשס״ה'],
            ['@#DHEBREW@ 15 AAV 5765', 'ט״ו אב ה׳תשס״ה'],
            ['@#DHEBREW@ AAV 5765', 'אב ה׳תשס״ה'],
            ['ABT @#DHEBREW@ AAV 5765', 'וועגן אב ה׳תשס״ה'],
            ['FROM @#DHEBREW@ AAV 5765', 'פון אב ה׳תשס״ה'],
            ['AFT @#DHEBREW@ AAV 5765', 'נאָך אב ה׳תשס״ה'],
            ['BEF @#DHEBREW@ AAV 5765', 'פאַר אב ה׳תשס״ה'],
            ['@#DHEBREW@ 15 ELL 5765', 'ט״ו אלול ה׳תשס״ה'],
            ['@#DHEBREW@ ELL 5765', 'אלול ה׳תשס״ה'],
            ['ABT @#DHEBREW@ ELL 5765', 'וועגן אלול ה׳תשס״ה'],
            ['FROM @#DHEBREW@ ELL 5765', 'פון אלול ה׳תשס״ה'],
            ['AFT @#DHEBREW@ ELL 5765', 'נאָך אלול ה׳תשס״ה'],
            ['BEF @#DHEBREW@ ELL 5765', 'פאַר אלול ה׳תשס״ה'],
            ['@#DHEBREW@ 5765', 'ה׳תשס״ה'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'וועגן ט״ו תשרי ה׳תשס״ה'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'אויסגערעכנט ט״ו תשרי ה׳תשס״ה'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'ווערט שאַצט ט״ו תשרי ה׳תשס״ה'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'פאַר ט״ו תשרי ה׳תשס״ה'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'נאָך ט״ו תשרי ה׳תשס״ה'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'פון ט״ו תשרי ה׳תשס״ה'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'צו ט״ו תשרי ה׳תשס״ה'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'צווישן ט״ו תשרי ה׳תשס״ה און ט״ו מרחשון ה׳תשס״ה'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'פון ט״ו תשרי ה׳תשס״ה צו ט״ו מרחשון ה׳תשס״ה'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'אינטערפּרעטאַציע ט״ו תשרי ה׳תשס״ה'],
            ['@#DFRENCH R@ 15 VEND 12', '15 וענדעמיאר An XII'],
            ['@#DFRENCH R@ VEND 12', 'וענדעמיאר An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'וועגן וענדעמיאר An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'פון וענדעמיאר An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'נאָך וענדעמיאר An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'פאַר וענדעמיאר An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 ברימער An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ברימער An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'וועגן ברימער An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'פון ברימער An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'נאָך ברימער An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'פאַר ברימער An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 פרימער An XII'],
            ['@#DFRENCH R@ FRIM 12', 'פרימער An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'וועגן פרימער An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'פון פרימער An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'נאָך פרימער An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'פאַר פרימער An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 ניבוז An XII'],
            ['@#DFRENCH R@ NIVO 12', 'ניבוז An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'וועגן ניבוז An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'פון ניבוז An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'נאָך ניבוז An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'פאַר ניבוז An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 פליביוז An XII'],
            ['@#DFRENCH R@ PLUV 12', 'פליביוז An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'וועגן פליביוז An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'פון פליביוז An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'נאָך פליביוז An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'פאַר פליביוז An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ונטוז An XII'],
            ['@#DFRENCH R@ VENT 12', 'ונטוז An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'וועגן ונטוז An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'פון ונטוז An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'נאָך ונטוז An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'פאַר ונטוז An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 ז׳רמינאל An XII'],
            ['@#DFRENCH R@ GERM 12', 'ז׳רמינאל An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'וועגן ז׳רמינאל An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'פון ז׳רמינאל An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'נאָך ז׳רמינאל An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'פאַר ז׳רמינאל An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 פלוראל An XII'],
            ['@#DFRENCH R@ FLOR 12', 'פלוראל An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'וועגן פלוראל An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'פון פלוראל An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'נאָך פלוראל An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'פאַר פלוראל An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 פריריאל An XII'],
            ['@#DFRENCH R@ PRAI 12', 'פריריאל An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'וועגן פריריאל An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'פון פריריאל An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'נאָך פריריאל An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'פאַר פריריאל An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 מעסידור An XII'],
            ['@#DFRENCH R@ MESS 12', 'מעסידור An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'וועגן מעסידור An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'פון מעסידור An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'נאָך מעסידור An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'פאַר מעסידור An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 תערמידור An XII'],
            ['@#DFRENCH R@ THER 12', 'תערמידור An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'וועגן תערמידור An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'פון תערמידור An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'נאָך תערמידור An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'פאַר תערמידור An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 פרוקטידור An XII'],
            ['@#DFRENCH R@ FRUC 12', 'פרוקטידור An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'וועגן פרוקטידור An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'פון פרוקטידור An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'נאָך פרוקטידור An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'פאַר פרוקטידור An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 אַדדיטיאָנאַל טעג An XII'],
            ['@#DFRENCH R@ COMP 12', 'אַדדיטיאָנאַל טעג An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'וועגן אַדדיטיאָנאַל טעג An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'פון אַדדיטיאָנאַל טעג An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'נאָך אַדדיטיאָנאַל טעג An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'פאַר אַדדיטיאָנאַל טעג An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'וועגן 15 וענדעמיאר An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'אויסגערעכנט 15 וענדעמיאר An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'ווערט שאַצט 15 וענדעמיאר An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'פאַר 15 וענדעמיאר An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'נאָך 15 וענדעמיאר An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'פון 15 וענדעמיאר An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'צו 15 וענדעמיאר An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'צווישן 15 וענדעמיאר An XII און 15 ברימער An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'פון 15 וענדעמיאר An XII צו 15 ברימער An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'אינטערפּרעטאַציע 15 וענדעמיאר An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 מוחראם 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'מוחראם 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'וועגן מוחראם 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'פון מוחראם 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'נאָך מוחראם 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'פאַר מוחראם 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 צאפר 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'צאפר 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'וועגן צאפר 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'פון צאפר 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'נאָך צאפר 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'פאַר צאפר 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 ראביע אל-אוואל 1425'],
            ['@#DHIJRI@ RABIA 1425', 'ראביע אל-אוואל 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'וועגן ראביע אל-אוואל 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'פון ראביע אל-אוואל 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'נאָך ראביע אל-אוואל 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'פאַר ראביע אל-אוואל 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 ראביע אל-ת׳אני 1425'],
            ['@#DHIJRI@ RABIT 1425', 'ראביע אל-ת׳אני 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'וועגן ראביע אל-ת׳אני 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'פון ראביע אל-ת׳אני 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'נאָך ראביע אל-ת׳אני 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'פאַר ראביע אל-ת׳אני 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 ג׳ומאדא אל-אוואל 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'ג׳ומאדא אל-אוואל 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'וועגן ג׳ומאדא אל-אוואל 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'פון ג׳ומאדא אל-אוואל 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'נאָך ג׳ומאדא אל-אוואל 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'פאַר ג׳ומאדא אל-אוואל 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 ג׳ומאדא אל-ת׳אניה 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'ג׳ומאדא אל-ת׳אניה 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'וועגן ג׳ומאדא אל-ת׳אניה 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'פון ג׳ומאדא אל-ת׳אניה 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'נאָך ג׳ומאדא אל-ת׳אניה 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'פאַר ג׳ומאדא אל-ת׳אניה 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 ראג׳אב 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'ראג׳אב 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'וועגן ראג׳אב 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'פון ראג׳אב 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'נאָך ראג׳אב 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'פאַר ראג׳אב 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 שאבאן 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'שאבאן 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'וועגן שאבאן 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'פון שאבאן 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'נאָך שאבאן 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'פאַר שאבאן 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 ראמדאן 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ראמדאן 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'וועגן ראמדאן 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'פון ראמדאן 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'נאָך ראמדאן 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'פאַר ראמדאן 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 שאוואל 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'שאוואל 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'וועגן שאוואל 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'פון שאוואל 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'נאָך שאוואל 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'פאַר שאוואל 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 ז׳ו אל-קעדה 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'ז׳ו אל-קעדה 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'וועגן ז׳ו אל-קעדה 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'פון ז׳ו אל-קעדה 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'נאָך ז׳ו אל-קעדה 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'פאַר ז׳ו אל-קעדה 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'וועגן 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'פון 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'נאָך 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'פאַר 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'וועגן 15 מוחראם 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'אויסגערעכנט 15 מוחראם 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'ווערט שאַצט 15 מוחראם 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'פאַר 15 מוחראם 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'נאָך 15 מוחראם 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'פון 15 מוחראם 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'צו 15 מוחראם 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'צווישן 15 מוחראם 1425 און 15 צאפר 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'פון 15 מוחראם 1425 צו 15 צאפר 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'אינטערפּרעטאַציע 15 מוחראם 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 פארבארדין 1384'],
            ['@#DJALALI@ FARVA 1384', 'פארבארדין 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'וועגן פארבארדין 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'פון פארבארדין 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'נאָך פארבארדין 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'פאַר פארבארדין 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 אורדיבהשת 1384'],
            ['@#DJALALI@ ORDIB 1384', 'אורדיבהשת 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'וועגן אורדיבהשת 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'פון אורדיבהשת 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'נאָך אורדיבהשת 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'פאַר אורדיבהשת 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 חורדאד 1384'],
            ['@#DJALALI@ KHORD 1384', 'חורדאד 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'וועגן חורדאד 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'פון חורדאד 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'נאָך חורדאד 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'פאַר חורדאד 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 טיר 1384'],
            ['@#DJALALI@ TIR 1384', 'טיר 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'וועגן טיר 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'פון טיר 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'נאָך טיר 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'פאַר טיר 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 מורדאד 1384'],
            ['@#DJALALI@ MORDA 1384', 'מורדאד 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'וועגן מורדאד 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'פון מורדאד 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'נאָך מורדאד 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'פאַר מורדאד 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 שאהריבאר 1384'],
            ['@#DJALALI@ SHAHR 1384', 'שאהריבאר 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'וועגן שאהריבאר 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'פון שאהריבאר 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'נאָך שאהריבאר 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'פאַר שאהריבאר 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 מעהר 1384'],
            ['@#DJALALI@ MEHR 1384', 'מעהר 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'וועגן מעהר 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'פון מעהר 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'נאָך מעהר 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'פאַר מעהר 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 אבאן 1384'],
            ['@#DJALALI@ ABAN 1384', 'אבאן 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'וועגן אבאן 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'פון אבאן 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'נאָך אבאן 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'פאַר אבאן 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 אזר 1384'],
            ['@#DJALALI@ AZAR 1384', 'אזר 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'וועגן אזר 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'פון אזר 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'נאָך אזר 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'פאַר אזר 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 דיי 1384'],
            ['@#DJALALI@ DEY 1384', 'דיי 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'וועגן דיי 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'פון דיי 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'נאָך דיי 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'פאַר דיי 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 באהמן 1384'],
            ['@#DJALALI@ BAHMA 1384', 'באהמן 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'וועגן באהמן 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'פון באהמן 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'נאָך באהמן 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'פאַר באהמן 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 עספנד 1384'],
            ['@#DJALALI@ ESFAN 1384', 'עספנד 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'וועגן עספנד 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'פון עספנד 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'נאָך עספנד 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'פאַר עספנד 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'וועגן 15 פארבארדין 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'אויסגערעכנט 15 פארבארדין 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'ווערט שאַצט 15 פארבארדין 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'פאַר 15 פארבארדין 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'נאָך 15 פארבארדין 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'פון 15 פארבארדין 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'צו 15 פארבארדין 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'צווישן 15 פארבארדין 1384 און 15 אורדיבהשת 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'פון 15 פארבארדין 1384 צו 15 אורדיבהשת 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'אינטערפּרעטאַציע 15 פארבארדין 1384'],
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
        self::assertSame('one און two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two און three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one אָדער two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two אָדער three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Create individuals
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $child = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fosterSon = self::male('fs', "1 FAMC @fd@\n2 PEDI foster");
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $grandsonSon = self::male('gs', "1 FAMC @fson@");
        $granddaughterSon = self::female('gd', "1 FAMC @fson@");
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $cousinFemale = self::female('cf', "1 FAMC @fbro@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        // Create families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fs@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@\n1 CHIL @gs@\n1 CHIL @gd@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $stepDaughter, $fosterSon,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $grandsonSon, $granddaughterSon, $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('ווײַב', 'מאַן', [$husband, $fm, $wife]);
        self::assertRelationshipNames('געוועזענער מאַן', 'געוועזענע ווײַב', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('כּלה', 'חתן', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('מאַמע', 'זון', [$son, $fm, $wife]);
        self::assertRelationshipNames('טאַטע', 'זון', [$son, $fm, $husband]);
        self::assertRelationshipNames('מאַמע', 'טאָכטער', [$daughter, $fm, $wife]);
        self::assertRelationshipNames('טאַטע', 'קינד', [$child, $fm, $husband]);

        // Adopted
        self::assertRelationshipNames('אַדאָפּטיוו-מאַמע', 'אַדאָפּטירטער זון', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('אַדאָפּטיוו-טאַטע', 'אַדאָפּטירטער זון', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('פֿלעגע-מאַמע', 'פֿלעגע-זון', [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames('פֿלעגע-טאַטע', 'פֿלעגע-זון', [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('ייִנגערע שוועסטער', 'עלטערער ברודער', [$son, $fm, $daughter]);
        self::assertRelationshipNames('עלטערער ברודער', 'ייִנגערע שוועסטער', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipNames('האַלב-ברודער', 'האַלב-שוועסטער', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('שטיפֿטאַטע', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('שטיפֿטאָכטער', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('שוויגער', 'איידעם', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('שווער', 'איידעם', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('שנור', [$fatherOfH, $fp, $husband, $fm, $wife]);
        self::assertRelationshipName('איידעם', [$motherOfW, $fw, $wife, $fm, $husband]);

        // Grandparents and grandchildren
        self::assertRelationshipNames('באָבע', 'אייניקל', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('זיידע', 'אייניקל', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('אייניקלע', [$fatherOfH, $fp, $husband, $fm, $daughter]);
        self::assertRelationshipName('אייניקל', [$fatherOfH, $fp, $husband, $fm, $son]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('עלטער-עלטער-זיידע', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('עלטער-עלטער-באָבע', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('מומע', 'פּלימעניק', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('פֿעטער', 'פּלימעניק', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('פּלימעניצע', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('פּלימעניק', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('קוזינע', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('קוזין', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('עלטער-מומע', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('עלטער-פֿעטער', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
