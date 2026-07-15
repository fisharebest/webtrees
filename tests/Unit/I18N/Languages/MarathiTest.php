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
use Fisharebest\Webtrees\I18N\Languages\Marathi;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Marathi::class)]
class MarathiTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Marathi();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Deva, self::language()->script());
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
        self::assertSame('mr', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('मराठी', self::language()->endonym());
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
        self::assertSame('-१२३,४५६.०७८९', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('-१,२३,४५६.०७८९', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-१,२३,४५६.०७८९%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '१५ जानेवारी २०००'],
            ['JAN 2000', 'जानेवारी २०००'],
            ['ABT JAN 2000', 'about जानेवारी २०००'],
            ['FROM JAN 2000', 'from जानेवारी २०००'],
            ['AFT JAN 2000', 'after जानेवारी २०००'],
            ['BEF JAN 2000', 'before जानेवारी २०००'],
            ['15 FEB 2000', '१५ फेब्रुवारी २०००'],
            ['FEB 2000', 'फेब्रुवारी २०००'],
            ['ABT FEB 2000', 'about फेब्रुवारी २०००'],
            ['FROM FEB 2000', 'from फेब्रुवारी २०००'],
            ['AFT FEB 2000', 'after फेब्रुवारी २०००'],
            ['BEF FEB 2000', 'before फेब्रुवारी २०००'],
            ['15 MAR 2000', '१५ मार्च २०००'],
            ['MAR 2000', 'मार्च २०००'],
            ['ABT MAR 2000', 'about मार्च २०००'],
            ['FROM MAR 2000', 'from मार्च २०००'],
            ['AFT MAR 2000', 'after मार्च २०००'],
            ['BEF MAR 2000', 'before मार्च २०००'],
            ['15 APR 2000', '१५ एप्रिल २०००'],
            ['APR 2000', 'एप्रिल २०००'],
            ['ABT APR 2000', 'about एप्रिल २०००'],
            ['FROM APR 2000', 'from एप्रिल २०००'],
            ['AFT APR 2000', 'after एप्रिल २०००'],
            ['BEF APR 2000', 'before एप्रिल २०००'],
            ['15 MAY 2000', '१५ मे २०००'],
            ['MAY 2000', 'मे २०००'],
            ['ABT MAY 2000', 'about मे २०००'],
            ['FROM MAY 2000', 'from मे २०००'],
            ['AFT MAY 2000', 'after मे २०००'],
            ['BEF MAY 2000', 'before मे २०००'],
            ['15 JUN 2000', '१५ जून २०००'],
            ['JUN 2000', 'जून २०००'],
            ['ABT JUN 2000', 'about जून २०००'],
            ['FROM JUN 2000', 'from जून २०००'],
            ['AFT JUN 2000', 'after जून २०००'],
            ['BEF JUN 2000', 'before जून २०००'],
            ['15 JUL 2000', '१५ जुलै २०००'],
            ['JUL 2000', 'जुलै २०००'],
            ['ABT JUL 2000', 'about जुलै २०००'],
            ['FROM JUL 2000', 'from जुलै २०००'],
            ['AFT JUL 2000', 'after जुलै २०००'],
            ['BEF JUL 2000', 'before जुलै २०००'],
            ['15 AUG 2000', '१५ ऑगस्ट २०००'],
            ['AUG 2000', 'ऑगस्ट २०००'],
            ['ABT AUG 2000', 'about ऑगस्ट २०००'],
            ['FROM AUG 2000', 'from ऑगस्ट २०००'],
            ['AFT AUG 2000', 'after ऑगस्ट २०००'],
            ['BEF AUG 2000', 'before ऑगस्ट २०००'],
            ['15 SEP 2000', '१५ सप्टेंबर २०००'],
            ['SEP 2000', 'सप्टेंबर २०००'],
            ['ABT SEP 2000', 'about सप्टेंबर २०००'],
            ['FROM SEP 2000', 'from सप्टेंबर २०००'],
            ['AFT SEP 2000', 'after सप्टेंबर २०००'],
            ['BEF SEP 2000', 'before सप्टेंबर २०००'],
            ['15 OCT 2000', '१५ ओक्टोबर २०००'],
            ['OCT 2000', 'ओक्टोबर २०००'],
            ['ABT OCT 2000', 'about ओक्टोबर २०००'],
            ['FROM OCT 2000', 'from ओक्टोबर २०००'],
            ['AFT OCT 2000', 'after ओक्टोबर २०००'],
            ['BEF OCT 2000', 'before ओक्टोबर २०००'],
            ['15 NOV 2000', '१५ नोव्हेंबर २०००'],
            ['NOV 2000', 'नोव्हेंबर २०००'],
            ['ABT NOV 2000', 'about नोव्हेंबर २०००'],
            ['FROM NOV 2000', 'from नोव्हेंबर २०००'],
            ['AFT NOV 2000', 'after नोव्हेंबर २०००'],
            ['BEF NOV 2000', 'before नोव्हेंबर २०००'],
            ['15 DEC 2000', '१५ डिसेंबर २०००'],
            ['DEC 2000', 'डिसेंबर २०००'],
            ['ABT DEC 2000', 'about डिसेंबर २०००'],
            ['FROM DEC 2000', 'from डिसेंबर २०००'],
            ['AFT DEC 2000', 'after डिसेंबर २०००'],
            ['BEF DEC 2000', 'before डिसेंबर २०००'],
            ['2000', '२०००'],
            ['ABT 15 JAN 2000', 'about १५ जानेवारी २०००'],
            ['CAL 15 JAN 2000', 'calculated १५ जानेवारी २०००'],
            ['EST 15 JAN 2000', 'estimated १५ जानेवारी २०००'],
            ['BEF 15 JAN 2000', 'before १५ जानेवारी २०००'],
            ['AFT 15 JAN 2000', 'after १५ जानेवारी २०००'],
            ['FROM 15 JAN 2000', 'from १५ जानेवारी २०००'],
            ['TO 15 JAN 2000', 'to १५ जानेवारी २०००'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between १५ जानेवारी २००० and १५ फेब्रुवारी २०००'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from १५ जानेवारी २००० to १५ फेब्रुवारी २०००'],
            ['INT 15 JAN 2000', 'interpreted १५ जानेवारी २०००'],
            ['@#DJULIAN@ 15 JAN 1700', '१५ जानेवारी १७०० ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'जानेवारी १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about जानेवारी १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from जानेवारी १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after जानेवारी १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before जानेवारी १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '१५ फेब्रुवारी १७०० ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'फेब्रुवारी १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about फेब्रुवारी १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from फेब्रुवारी १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after फेब्रुवारी १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before फेब्रुवारी १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '१५ मार्च १७०० ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'मार्च १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about मार्च १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from मार्च १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after मार्च १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before मार्च १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '१५ एप्रिल १७०० ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '१४ एप्रिल १६४५/४६ ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'एप्रिल १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about एप्रिल १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from एप्रिल १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after एप्रिल १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before एप्रिल १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '१५ मे १७०० ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'मे १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about मे १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from मे १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after मे १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before मे १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '१५ जून १७०० ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'जून १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about जून १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from जून १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after जून १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before जून १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '१५ जुलै १७०० ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'जुलै १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about जुलै १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from जुलै १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after जुलै १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before जुलै १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '१५ ऑगस्ट १७०० ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'ऑगस्ट १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about ऑगस्ट १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from ऑगस्ट १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after ऑगस्ट १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before ऑगस्ट १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '१५ सप्टेंबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'सप्टेंबर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about सप्टेंबर १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from सप्टेंबर १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after सप्टेंबर १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before सप्टेंबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '१५ ओक्टोबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'ओक्टोबर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about ओक्टोबर १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from ओक्टोबर १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after ओक्टोबर १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before ओक्टोबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '१५ नोव्हेंबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'नोव्हेंबर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about नोव्हेंबर १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from नोव्हेंबर १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after नोव्हेंबर १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before नोव्हेंबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '१५ डिसेंबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'डिसेंबर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about डिसेंबर १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from डिसेंबर १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after डिसेंबर १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before डिसेंबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 1700', '१७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about १५ जानेवारी १७०० ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated १५ जानेवारी १७०० ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated १५ जानेवारी १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before १५ जानेवारी १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after १५ जानेवारी १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from १५ जानेवारी १७०० ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to १५ जानेवारी १७०० ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between १५ जानेवारी १७०० ᴄᴇ and १५ फेब्रुवारी १७०० ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from १५ जानेवारी १७०० ᴄᴇ to १५ फेब्रुवारी १७०० ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted १५ जानेवारी १७०० ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '१५ तिश्री ५७६५'],
            ['@#DHEBREW@ TSH 5765', 'तिश्री ५७६५'],
            ['ABT @#DHEBREW@ TSH 5765', 'about तिश्री ५७६५'],
            ['FROM @#DHEBREW@ TSH 5765', 'from तिश्री ५७६५'],
            ['AFT @#DHEBREW@ TSH 5765', 'after तिश्री ५७६५'],
            ['BEF @#DHEBREW@ TSH 5765', 'before तिश्री ५७६५'],
            ['@#DHEBREW@ 15 CSH 5765', '१५ हेश्वान ५७६५'],
            ['@#DHEBREW@ CSH 5765', 'हेश्वान ५७६५'],
            ['ABT @#DHEBREW@ CSH 5765', 'about हेश्वान ५७६५'],
            ['FROM @#DHEBREW@ CSH 5765', 'from हेश्वान ५७६५'],
            ['AFT @#DHEBREW@ CSH 5765', 'after हेश्वान ५७६५'],
            ['BEF @#DHEBREW@ CSH 5765', 'before हेश्वान ५७६५'],
            ['@#DHEBREW@ 15 KSL 5765', '१५ किस्लेव ५७६५'],
            ['@#DHEBREW@ KSL 5765', 'किस्लेव ५७६५'],
            ['ABT @#DHEBREW@ KSL 5765', 'about किस्लेव ५७६५'],
            ['FROM @#DHEBREW@ KSL 5765', 'from किस्लेव ५७६५'],
            ['AFT @#DHEBREW@ KSL 5765', 'after किस्लेव ५७६५'],
            ['BEF @#DHEBREW@ KSL 5765', 'before किस्लेव ५७६५'],
            ['@#DHEBREW@ 15 TVT 5765', '१५ तेवेत ५७६५'],
            ['@#DHEBREW@ TVT 5765', 'तेवेत ५७६५'],
            ['ABT @#DHEBREW@ TVT 5765', 'about तेवेत ५७६५'],
            ['FROM @#DHEBREW@ TVT 5765', 'from तेवेत ५७६५'],
            ['AFT @#DHEBREW@ TVT 5765', 'after तेवेत ५७६५'],
            ['BEF @#DHEBREW@ TVT 5765', 'before तेवेत ५७६५'],
            ['@#DHEBREW@ 15 SHV 5765', '१५ शेवत ५७६५'],
            ['@#DHEBREW@ SHV 5765', 'शेवत ५७६५'],
            ['ABT @#DHEBREW@ SHV 5765', 'about शेवत ५७६५'],
            ['FROM @#DHEBREW@ SHV 5765', 'from शेवत ५७६५'],
            ['AFT @#DHEBREW@ SHV 5765', 'after शेवत ५७६५'],
            ['BEF @#DHEBREW@ SHV 5765', 'before शेवत ५७६५'],
            ['@#DHEBREW@ 15 ADR 5765', '१५ अदार १ ५७६५'],
            ['@#DHEBREW@ ADR 5765', 'अदार १ ५७६५'],
            ['ABT @#DHEBREW@ ADR 5765', 'about अदार १ ५७६५'],
            ['FROM @#DHEBREW@ ADR 5765', 'from अदार १ ५७६५'],
            ['AFT @#DHEBREW@ ADR 5765', 'after अदार १ ५७६५'],
            ['BEF @#DHEBREW@ ADR 5765', 'before अदार १ ५७६५'],
            ['@#DHEBREW@ 15 ADS 5765', '१५ अदार २ ५७६५'],
            ['@#DHEBREW@ ADS 5765', 'अदार २ ५७६५'],
            ['ABT @#DHEBREW@ ADS 5765', 'about अदार २ ५७६५'],
            ['FROM @#DHEBREW@ ADS 5765', 'from अदार २ ५७६५'],
            ['AFT @#DHEBREW@ ADS 5765', 'after अदार २ ५७६५'],
            ['BEF @#DHEBREW@ ADS 5765', 'before अदार २ ५७६५'],
            ['@#DHEBREW@ 15 NSN 5765', '१५ निसान ५७६५'],
            ['@#DHEBREW@ NSN 5765', 'निसान ५७६५'],
            ['ABT @#DHEBREW@ NSN 5765', 'about निसान ५७६५'],
            ['FROM @#DHEBREW@ NSN 5765', 'from निसान ५७६५'],
            ['AFT @#DHEBREW@ NSN 5765', 'after निसान ५७६५'],
            ['BEF @#DHEBREW@ NSN 5765', 'before निसान ५७६५'],
            ['@#DHEBREW@ 15 IYR 5765', '१५ इयार ५७६५'],
            ['@#DHEBREW@ IYR 5765', 'इयार ५७६५'],
            ['ABT @#DHEBREW@ IYR 5765', 'about इयार ५७६५'],
            ['FROM @#DHEBREW@ IYR 5765', 'from इयार ५७६५'],
            ['AFT @#DHEBREW@ IYR 5765', 'after इयार ५७६५'],
            ['BEF @#DHEBREW@ IYR 5765', 'before इयार ५७६५'],
            ['@#DHEBREW@ 15 SVN 5765', '१५ सिवान ५७६५'],
            ['@#DHEBREW@ SVN 5765', 'सिवान ५७६५'],
            ['ABT @#DHEBREW@ SVN 5765', 'about सिवान ५७६५'],
            ['FROM @#DHEBREW@ SVN 5765', 'from सिवान ५७६५'],
            ['AFT @#DHEBREW@ SVN 5765', 'after सिवान ५७६५'],
            ['BEF @#DHEBREW@ SVN 5765', 'before सिवान ५७६५'],
            ['@#DHEBREW@ 15 TMZ 5765', '१५ तमुझ ५७६५'],
            ['@#DHEBREW@ TMZ 5765', 'तमुझ ५७६५'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about तमुझ ५७६५'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from तमुझ ५७६५'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after तमुझ ५७६५'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before तमुझ ५७६५'],
            ['@#DHEBREW@ 15 AAV 5765', '१५ आव ५७६५'],
            ['@#DHEBREW@ AAV 5765', 'आव ५७६५'],
            ['ABT @#DHEBREW@ AAV 5765', 'about आव ५७६५'],
            ['FROM @#DHEBREW@ AAV 5765', 'from आव ५७६५'],
            ['AFT @#DHEBREW@ AAV 5765', 'after आव ५७६५'],
            ['BEF @#DHEBREW@ AAV 5765', 'before आव ५७६५'],
            ['@#DHEBREW@ 15 ELL 5765', '१५ एलुल ५७६५'],
            ['@#DHEBREW@ ELL 5765', 'एलुल ५७६५'],
            ['ABT @#DHEBREW@ ELL 5765', 'about एलुल ५७६५'],
            ['FROM @#DHEBREW@ ELL 5765', 'from एलुल ५७६५'],
            ['AFT @#DHEBREW@ ELL 5765', 'after एलुल ५७६५'],
            ['BEF @#DHEBREW@ ELL 5765', 'before एलुल ५७६५'],
            ['@#DHEBREW@ 5765', '५७६५'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about १५ तिश्री ५७६५'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated १५ तिश्री ५७६५'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated १५ तिश्री ५७६५'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before १५ तिश्री ५७६५'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after १५ तिश्री ५७६५'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from १५ तिश्री ५७६५'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to १५ तिश्री ५७६५'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between १५ तिश्री ५७६५ and १५ हेश्वान ५७६५'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from १५ तिश्री ५७६५ to १५ हेश्वान ५७६५'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted १५ तिश्री ५७६५'],
            ['@#DFRENCH R@ 15 VEND 12', '१५ वेन्डेमियेर An XII'],
            ['@#DFRENCH R@ VEND 12', 'वेन्डेमियेर An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about वेन्डेमियेर An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from वेन्डेमियेर An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after वेन्डेमियेर An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before वेन्डेमियेर An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '१५ ब्रुमेयर An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ब्रुमेयर An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about ब्रुमेयर An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from ब्रुमेयर An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after ब्रुमेयर An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before ब्रुमेयर An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '१५ फ्रिमेयर An XII'],
            ['@#DFRENCH R@ FRIM 12', 'फ्रिमेयर An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about फ्रिमेयर An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from फ्रिमेयर An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after फ्रिमेयर An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before फ्रिमेयर An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '१५ निव्होस An XII'],
            ['@#DFRENCH R@ NIVO 12', 'निव्होस An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about निव्होस An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from निव्होस An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after निव्होस An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before निव्होस An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '१५ प्लुव्हिओस An XII'],
            ['@#DFRENCH R@ PLUV 12', 'प्लुव्हिओस An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about प्लुव्हिओस An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from प्लुव्हिओस An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after प्लुव्हिओस An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before प्लुव्हिओस An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '१५ व्हेन्टोस An XII'],
            ['@#DFRENCH R@ VENT 12', 'व्हेन्टोस An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about व्हेन्टोस An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from व्हेन्टोस An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after व्हेन्टोस An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before व्हेन्टोस An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '१५ जर्मिनल An XII'],
            ['@#DFRENCH R@ GERM 12', 'जर्मिनल An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about जर्मिनल An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from जर्मिनल An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after जर्मिनल An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before जर्मिनल An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '१५ फ्लोरियल An XII'],
            ['@#DFRENCH R@ FLOR 12', 'फ्लोरियल An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about फ्लोरियल An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from फ्लोरियल An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after फ्लोरियल An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before फ्लोरियल An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '१५ प्रेरियल An XII'],
            ['@#DFRENCH R@ PRAI 12', 'प्रेरियल An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about प्रेरियल An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from प्रेरियल An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after प्रेरियल An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before प्रेरियल An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '१५ मेसीडोर An XII'],
            ['@#DFRENCH R@ MESS 12', 'मेसीडोर An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about मेसीडोर An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from मेसीडोर An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after मेसीडोर An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before मेसीडोर An XII'],
            ['@#DFRENCH R@ 15 THER 12', '१५ थर्मिडोर An XII'],
            ['@#DFRENCH R@ THER 12', 'थर्मिडोर An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about थर्मिडोर An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from थर्मिडोर An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after थर्मिडोर An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before थर्मिडोर An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '१५ फ्रुक्टिडोर An XII'],
            ['@#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about फ्रुक्टिडोर An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from फ्रुक्टिडोर An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after फ्रुक्टिडोर An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before फ्रुक्टिडोर An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '१५ पूरक दिवस An XII'],
            ['@#DFRENCH R@ COMP 12', 'पूरक दिवस An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about पूरक दिवस An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from पूरक दिवस An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after पूरक दिवस An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before पूरक दिवस An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about १५ वेन्डेमियेर An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated १५ वेन्डेमियेर An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated १५ वेन्डेमियेर An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before १५ वेन्डेमियेर An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after १५ वेन्डेमियेर An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from १५ वेन्डेमियेर An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to १५ वेन्डेमियेर An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between १५ वेन्डेमियेर An XII and १५ ब्रुमेयर An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from १५ वेन्डेमियेर An XII to १५ ब्रुमेयर An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted १५ वेन्डेमियेर An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '१५ मुहर्रम १४२५'],
            ['@#DHIJRI@ MUHAR 1425', 'मुहर्रम १४२५'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about मुहर्रम १४२५'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from मुहर्रम १४२५'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after मुहर्रम १४२५'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before मुहर्रम १४२५'],
            ['@#DHIJRI@ 15 SAFAR 1425', '१५ सफर १४२५'],
            ['@#DHIJRI@ SAFAR 1425', 'सफर १४२५'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about सफर १४२५'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from सफर १४२५'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after सफर १४२५'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before सफर १४२५'],
            ['@#DHIJRI@ 15 RABIA 1425', '१५ रबी उल-अव्वल १४२५'],
            ['@#DHIJRI@ RABIA 1425', 'रबी उल-अव्वल १४२५'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about रबी उल-अव्वल १४२५'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from रबी उल-अव्वल १४२५'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after रबी उल-अव्वल १४२५'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before रबी उल-अव्वल १४२५'],
            ['@#DHIJRI@ 15 RABIT 1425', '१५ रबी उल-आखिर १४२५'],
            ['@#DHIJRI@ RABIT 1425', 'रबी उल-आखिर १४२५'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about रबी उल-आखिर १४२५'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from रबी उल-आखिर १४२५'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after रबी उल-आखिर १४२५'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before रबी उल-आखिर १४२५'],
            ['@#DHIJRI@ 15 JUMAA 1425', '१५ जमादिल अव्वल १४२५'],
            ['@#DHIJRI@ JUMAA 1425', 'जमादिल अव्वल १४२५'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about जमादिल अव्वल १४२५'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from जमादिल अव्वल १४२५'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after जमादिल अव्वल १४२५'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before जमादिल अव्वल १४२५'],
            ['@#DHIJRI@ 15 JUMAT 1425', '१५ जमादिल सानी १४२५'],
            ['@#DHIJRI@ JUMAT 1425', 'जमादिल सानी १४२५'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about जमादिल सानी १४२५'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from जमादिल सानी १४२५'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after जमादिल सानी १४२५'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before जमादिल सानी १४२५'],
            ['@#DHIJRI@ 15 RAJAB 1425', '१५ रजब १४२५'],
            ['@#DHIJRI@ RAJAB 1425', 'रजब १४२५'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about रजब १४२५'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from रजब १४२५'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after रजब १४२५'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before रजब १४२५'],
            ['@#DHIJRI@ 15 SHAAB 1425', '१५ शाबान १४२५'],
            ['@#DHIJRI@ SHAAB 1425', 'शाबान १४२५'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about शाबान १४२५'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from शाबान १४२५'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after शाबान १४२५'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before शाबान १४२५'],
            ['@#DHIJRI@ 15 RAMAD 1425', '१५ रमजान १४२५'],
            ['@#DHIJRI@ RAMAD 1425', 'रमजान १४२५'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about रमजान १४२५'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from रमजान १४२५'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after रमजान १४२५'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before रमजान १४२५'],
            ['@#DHIJRI@ 15 SHAWW 1425', '१५ शव्वाल १४२५'],
            ['@#DHIJRI@ SHAWW 1425', 'शव्वाल १४२५'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about शव्वाल १४२५'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from शव्वाल १४२५'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after शव्वाल १४२५'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before शव्वाल १४२५'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '१५ जिल्कद १४२५'],
            ['@#DHIJRI@ DHUAQ 1425', 'जिल्कद १४२५'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about जिल्कद १४२५'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from जिल्कद १४२५'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after जिल्कद १४२५'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before जिल्कद १४२५'],
            ['@#DHIJRI@ 15 DHUAL 1425', '१४२५'],
            ['@#DHIJRI@ DHUAL 1425', '१४२५'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about १४२५'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from १४२५'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after १४२५'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before १४२५'],
            ['@#DHIJRI@ 1425', '१४२५'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about १५ मुहर्रम १४२५'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated १५ मुहर्रम १४२५'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated १५ मुहर्रम १४२५'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before १५ मुहर्रम १४२५'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after १५ मुहर्रम १४२५'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from १५ मुहर्रम १४२५'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to १५ मुहर्रम १४२५'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between १५ मुहर्रम १४२५ and १५ सफर १४२५'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from १५ मुहर्रम १४२५ to १५ सफर १४२५'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted १५ मुहर्रम १४२५'],
            ['@#DJALALI@ 15 FARVA 1384', '१५ फरवर्दिन १३८४'],
            ['@#DJALALI@ FARVA 1384', 'फरवर्दिन १३८४'],
            ['ABT @#DJALALI@ FARVA 1384', 'about फरवर्दिन १३८४'],
            ['FROM @#DJALALI@ FARVA 1384', 'from फरवर्दिन १३८४'],
            ['AFT @#DJALALI@ FARVA 1384', 'after फरवर्दिन १३८४'],
            ['BEF @#DJALALI@ FARVA 1384', 'before फरवर्दिन १३८४'],
            ['@#DJALALI@ 15 ORDIB 1384', '१५ ऑर्डिबेहेश्त १३८४'],
            ['@#DJALALI@ ORDIB 1384', 'ऑर्डिबेहेश्त १३८४'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about ऑर्डिबेहेश्त १३८४'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from ऑर्डिबेहेश्त १३८४'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after ऑर्डिबेहेश्त १३८४'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before ऑर्डिबेहेश्त १३८४'],
            ['@#DJALALI@ 15 KHORD 1384', '१५ खोरदाद १३८४'],
            ['@#DJALALI@ KHORD 1384', 'खोरदाद १३८४'],
            ['ABT @#DJALALI@ KHORD 1384', 'about खोरदाद १३८४'],
            ['FROM @#DJALALI@ KHORD 1384', 'from खोरदाद १३८४'],
            ['AFT @#DJALALI@ KHORD 1384', 'after खोरदाद १३८४'],
            ['BEF @#DJALALI@ KHORD 1384', 'before खोरदाद १३८४'],
            ['@#DJALALI@ 15 TIR 1384', '१५ तीर १३८४'],
            ['@#DJALALI@ TIR 1384', 'तीर १३८४'],
            ['ABT @#DJALALI@ TIR 1384', 'about तीर १३८४'],
            ['FROM @#DJALALI@ TIR 1384', 'from तीर १३८४'],
            ['AFT @#DJALALI@ TIR 1384', 'after तीर १३८४'],
            ['BEF @#DJALALI@ TIR 1384', 'before तीर १३८४'],
            ['@#DJALALI@ 15 MORDA 1384', '१५ मोरदाद १३८४'],
            ['@#DJALALI@ MORDA 1384', 'मोरदाद १३८४'],
            ['ABT @#DJALALI@ MORDA 1384', 'about मोरदाद १३८४'],
            ['FROM @#DJALALI@ MORDA 1384', 'from मोरदाद १३८४'],
            ['AFT @#DJALALI@ MORDA 1384', 'after मोरदाद १३८४'],
            ['BEF @#DJALALI@ MORDA 1384', 'before मोरदाद १३८४'],
            ['@#DJALALI@ 15 SHAHR 1384', '१५ शहरीवर १३८४'],
            ['@#DJALALI@ SHAHR 1384', 'शहरीवर १३८४'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about शहरीवर १३८४'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from शहरीवर १३८४'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after शहरीवर १३८४'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before शहरीवर १३८४'],
            ['@#DJALALI@ 15 MEHR 1384', '१५ मेहर १३८४'],
            ['@#DJALALI@ MEHR 1384', 'मेहर १३८४'],
            ['ABT @#DJALALI@ MEHR 1384', 'about मेहर १३८४'],
            ['FROM @#DJALALI@ MEHR 1384', 'from मेहर १३८४'],
            ['AFT @#DJALALI@ MEHR 1384', 'after मेहर १३८४'],
            ['BEF @#DJALALI@ MEHR 1384', 'before मेहर १३८४'],
            ['@#DJALALI@ 15 ABAN 1384', '१५ आबान १३८४'],
            ['@#DJALALI@ ABAN 1384', 'आबान १३८४'],
            ['ABT @#DJALALI@ ABAN 1384', 'about आबान १३८४'],
            ['FROM @#DJALALI@ ABAN 1384', 'from आबान १३८४'],
            ['AFT @#DJALALI@ ABAN 1384', 'after आबान १३८४'],
            ['BEF @#DJALALI@ ABAN 1384', 'before आबान १३८४'],
            ['@#DJALALI@ 15 AZAR 1384', '१५ आझर १३८४'],
            ['@#DJALALI@ AZAR 1384', 'आझर १३८४'],
            ['ABT @#DJALALI@ AZAR 1384', 'about आझर १३८४'],
            ['FROM @#DJALALI@ AZAR 1384', 'from आझर १३८४'],
            ['AFT @#DJALALI@ AZAR 1384', 'after आझर १३८४'],
            ['BEF @#DJALALI@ AZAR 1384', 'before आझर १३८४'],
            ['@#DJALALI@ 15 DEY 1384', '१५ दे १३८४'],
            ['@#DJALALI@ DEY 1384', 'दे १३८४'],
            ['ABT @#DJALALI@ DEY 1384', 'about दे १३८४'],
            ['FROM @#DJALALI@ DEY 1384', 'from दे १३८४'],
            ['AFT @#DJALALI@ DEY 1384', 'after दे १३८४'],
            ['BEF @#DJALALI@ DEY 1384', 'before दे १३८४'],
            ['@#DJALALI@ 15 BAHMA 1384', '१५ बहमन १३८४'],
            ['@#DJALALI@ BAHMA 1384', 'बहमन १३८४'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about बहमन १३८४'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from बहमन १३८४'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after बहमन १३८४'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before बहमन १३८४'],
            ['@#DJALALI@ 15 ESFAN 1384', '१५ इस्फंद १३८४'],
            ['@#DJALALI@ ESFAN 1384', 'इस्फंद १३८४'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about इस्फंद १३८४'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from इस्फंद १३८४'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after इस्फंद १३८४'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before इस्फंद १३८४'],
            ['@#DJALALI@ 1384', '१३८४'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about १५ फरवर्दिन १३८४'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated १५ फरवर्दिन १३८४'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated १५ फरवर्दिन १३८४'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before १५ फरवर्दिन १३८४'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after १५ फरवर्दिन १३८४'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from १५ फरवर्दिन १३८४'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to १५ फरवर्दिन १३८४'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between १५ फरवर्दिन १३८४ and १५ ऑर्डिबेहेश्त १३८४'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from १५ फरवर्दिन १३८४ to १५ ऑर्डिबेहेश्त १३८४'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted १५ फरवर्दिन १३८४'],
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
        self::assertSame('one आणि two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two आणि three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one किंवा two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two किंवा three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife with son and daughter
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1970");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 1 JAN 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 1 JAN 1998");
        $child = self::unknown('c', "1 FAMC @fm@");

        // Husband's family
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 BIRT\n2 DATE 1 JAN 1940");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMS @fbro@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1960");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        // Wife's family
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMS @fbow@\n1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMS @fsow@\n1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Brother's children (nieces/nephews)
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");

        // Sister's children (nieces/nephews)
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins — paternal uncle's children
        $cousinMPat = self::male('cmp', "1 FAMC @fbro@");
        // Cousins — paternal aunt's children
        $cousinFPat = self::female('cfp', "1 FAMC @fsis@");

        // Great-grandparents
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");

        // Sibling spouses
        $wifeOfBro = self::female('wbro', "1 FAMS @fbro@");
        $husbandOfSis = self::male('hsis', "1 FAMS @fsis@");

        // Uncle/aunt spouses
        $wifeOfBOW = self::female('wbow', "1 FAMS @fbow@");
        $husbandOfSOW = self::male('hsow', "1 FAMS @fsow@");

        // Families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 MARR Y\n1 HUSB @bh@\n1 WIFE @wbro@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cmp@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 MARR Y\n1 HUSB @hsis@\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cfp@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fbow = self::family('fbow', "0 @fbow@ FAM\n1 MARR Y\n1 HUSB @bw@\n1 WIFE @wbow@");
        $fsow = self::family('fsow', "0 @fsow@ FAM\n1 MARR Y\n1 HUSB @hsow@\n1 WIFE @sw@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinMPat, $cousinFPat,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $wifeOfBro, $husbandOfSis,
             $wifeOfBOW, $husbandOfSOW],
            [$fm, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fgp, $fbow, $fsow]
        );

        // Parents / Children
        self::assertRelationshipNames('आई', 'मुलगा', [$son, $fm, $wife]);
        self::assertRelationshipNames('वडील', 'मुलगा', [$son, $fm, $husband]);
        self::assertRelationshipNames('आई', 'मुलगी', [$daughter, $fm, $wife]);

        // Partners
        self::assertRelationshipNames('पत्नी', 'पती', [$husband, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('मोठी बहीण', 'लहान भाऊ', [$son, $fm, $daughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('सासू', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('सासरे', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('सासू', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('सासरे', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('सून', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('जावई', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — husband's siblings
        self::assertRelationshipName('नणंद', [$wife, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('दीर', [$wife, $fm, $husband, $fp, $brotherOfH]);

        // In-laws — wife's siblings
        self::assertRelationshipName('मेहुणा', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('मेहुणी', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // In-laws — sibling's spouse
        self::assertRelationshipName('वहिनी', [$husband, $fp, $brotherOfH, $fbro, $wifeOfBro]);
        self::assertRelationshipName('भावोजी', [$husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);

        // Grandparents (no paternal/maternal distinction)
        self::assertRelationshipName('आजी', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('आजोबा', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('आजी', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('आजोबा', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('नातू', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('नात', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts — paternal (आत्या = father's sister)
        self::assertRelationshipName('आत्या', [$son, $fm, $husband, $fp, $sisterOfH]);

        // Aunts — maternal (मावशी = mother's sister)
        self::assertRelationshipName('मावशी', [$son, $fm, $wife, $fw, $sisterOfW]);

        // Uncles — paternal (काका = father's brother)
        self::assertRelationshipName('काका', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Uncles — maternal (मामा = mother's brother)
        self::assertRelationshipName('मामा', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Uncle/aunt spouses
        self::assertRelationshipName('काकू', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $wifeOfBro]);
        self::assertRelationshipName('मामी', [$son, $fm, $wife, $fw, $brotherOfW, $fbow, $wifeOfBOW]);
        self::assertRelationshipName('मावसा', [$son, $fm, $wife, $fw, $sisterOfW, $fsow, $husbandOfSOW]);

        // Nieces/Nephews — through brother (पुतणी/पुतण्या)
        self::assertRelationshipName('पुतणी', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('पुतण्या', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Nieces/Nephews — through sister (भाची/भाचा)
        self::assertRelationshipName('भाची', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('भाचा', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — paternal uncle's son (चुलत भाऊ)
        self::assertRelationshipName('चुलत भाऊ', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMPat]);

        // Cousins — paternal aunt's daughter (आत्ये बहीण)
        self::assertRelationshipName('आत्ये बहीण', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinFPat]);

        // Great-grandparents (dynamic — पणजी/पणजोबा)
        self::assertRelationshipName('पणजी', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('पणजोबा', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('आत्या/मावशी मोठी', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('काका/मामा मोठे', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
