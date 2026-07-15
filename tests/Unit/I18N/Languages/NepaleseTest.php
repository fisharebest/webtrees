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
use Fisharebest\Webtrees\I18N\Languages\Nepalese;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Nepalese::class)]
class NepaleseTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Nepalese();
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
        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('ne', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('नेपाली', self::language()->endonym());
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
            ['15 JAN 2000', '१५ जनवरी २०००'],
            ['JAN 2000', 'जनवरी २०००'],
            ['ABT JAN 2000', 'जनवरी २००० बारेमा'],
            ['FROM JAN 2000', 'जनवरी २००० बाट'],
            ['AFT JAN 2000', 'जनवरी २००० पछि'],
            ['BEF JAN 2000', 'अगाडि जनवरी २०००'],
            ['15 FEB 2000', '१५ फेब्रुवरी २०००'],
            ['FEB 2000', 'फेब्रुवरी २०००'],
            ['ABT FEB 2000', 'फेब्रुवरी २००० बारेमा'],
            ['FROM FEB 2000', 'फेब्रुवरी २००० बाट'],
            ['AFT FEB 2000', 'फेब्रुवरी २००० पछि'],
            ['BEF FEB 2000', 'अगाडि फेब्रुवरी २०००'],
            ['15 MAR 2000', '१५ मार्च २०००'],
            ['MAR 2000', 'मार्च २०००'],
            ['ABT MAR 2000', 'मार्च २००० बारेमा'],
            ['FROM MAR 2000', 'मार्च २००० बाट'],
            ['AFT MAR 2000', 'मार्च २००० पछि'],
            ['BEF MAR 2000', 'अगाडि मार्च २०००'],
            ['15 APR 2000', '१५ अप्रिल २०००'],
            ['APR 2000', 'अप्रिल २०००'],
            ['ABT APR 2000', 'अप्रिल २००० बारेमा'],
            ['FROM APR 2000', 'अप्रिल २००० बाट'],
            ['AFT APR 2000', 'अप्रिल २००० पछि'],
            ['BEF APR 2000', 'अगाडि अप्रिल २०००'],
            ['15 MAY 2000', '१५ मई २०००'],
            ['MAY 2000', 'मई २०००'],
            ['ABT MAY 2000', 'मई २००० बारेमा'],
            ['FROM MAY 2000', 'मई २००० बाट'],
            ['AFT MAY 2000', 'मई २००० पछि'],
            ['BEF MAY 2000', 'अगाडि मई २०००'],
            ['15 JUN 2000', '१५ जुन २०००'],
            ['JUN 2000', 'जुन २०००'],
            ['ABT JUN 2000', 'जुन २००० बारेमा'],
            ['FROM JUN 2000', 'जुन २००० बाट'],
            ['AFT JUN 2000', 'जुन २००० पछि'],
            ['BEF JUN 2000', 'अगाडि जुन २०००'],
            ['15 JUL 2000', '१५ जुलाई २०००'],
            ['JUL 2000', 'जुलाई २०००'],
            ['ABT JUL 2000', 'जुलाई २००० बारेमा'],
            ['FROM JUL 2000', 'जुलाई २००० बाट'],
            ['AFT JUL 2000', 'जुलाई २००० पछि'],
            ['BEF JUL 2000', 'अगाडि जुलाई २०००'],
            ['15 AUG 2000', '१५ अगस्ट २०००'],
            ['AUG 2000', 'अगस्ट २०००'],
            ['ABT AUG 2000', 'अगस्ट २००० बारेमा'],
            ['FROM AUG 2000', 'अगस्ट २००० बाट'],
            ['AFT AUG 2000', 'अगस्ट २००० पछि'],
            ['BEF AUG 2000', 'अगाडि अगस्ट २०००'],
            ['15 SEP 2000', '१५ सेप्टेम्बर २०००'],
            ['SEP 2000', 'सेप्टेम्बर २०००'],
            ['ABT SEP 2000', 'सेप्टेम्बर २००० बारेमा'],
            ['FROM SEP 2000', 'सेप्टेम्बर २००० बाट'],
            ['AFT SEP 2000', 'सेप्टेम्बर २००० पछि'],
            ['BEF SEP 2000', 'अगाडि सेप्टेम्बर २०००'],
            ['15 OCT 2000', '१५ अक्टोबर २०००'],
            ['OCT 2000', 'अक्टोबर २०००'],
            ['ABT OCT 2000', 'अक्टोबर २००० बारेमा'],
            ['FROM OCT 2000', 'अक्टोबर २००० बाट'],
            ['AFT OCT 2000', 'अक्टोबर २००० पछि'],
            ['BEF OCT 2000', 'अगाडि अक्टोबर २०००'],
            ['15 NOV 2000', '१५ नोभेम्बर २०००'],
            ['NOV 2000', 'नोभेम्बर २०००'],
            ['ABT NOV 2000', 'नोभेम्बर २००० बारेमा'],
            ['FROM NOV 2000', 'नोभेम्बर २००० बाट'],
            ['AFT NOV 2000', 'नोभेम्बर २००० पछि'],
            ['BEF NOV 2000', 'अगाडि नोभेम्बर २०००'],
            ['15 DEC 2000', '१५ डिसेम्बर २०००'],
            ['DEC 2000', 'डिसेम्बर २०००'],
            ['ABT DEC 2000', 'डिसेम्बर २००० बारेमा'],
            ['FROM DEC 2000', 'डिसेम्बर २००० बाट'],
            ['AFT DEC 2000', 'डिसेम्बर २००० पछि'],
            ['BEF DEC 2000', 'अगाडि डिसेम्बर २०००'],
            ['2000', '२०००'],
            ['ABT 15 JAN 2000', '१५ जनवरी २००० बारेमा'],
            ['CAL 15 JAN 2000', '१५ जनवरी २००० गणना गरियो'],
            ['EST 15 JAN 2000', 'अनुमानित १५ जनवरी २०००'],
            ['BEF 15 JAN 2000', 'अगाडि १५ जनवरी २०००'],
            ['AFT 15 JAN 2000', '१५ जनवरी २००० पछि'],
            ['FROM 15 JAN 2000', '१५ जनवरी २००० बाट'],
            ['TO 15 JAN 2000', '१५ जनवरी २००० लाई'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '१५ जनवरी २००० र १५ फेब्रुवरी २००० को बीचमा'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '१५ जनवरी २००० देखि १५ फेब्रुवरी २००० सम्म'],
            ['INT 15 JAN 2000', 'interpreted १५ जनवरी २०००'],
            ['@#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'जनवरी १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'जनवरी १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ JAN 1700', 'जनवरी १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ JAN 1700', 'जनवरी १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ JAN 1700', 'अगाडि जनवरी १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '१५ फेब्रुवरी १७०० ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'फेब्रुवरी १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'फेब्रुवरी १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ FEB 1700', 'फेब्रुवरी १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ FEB 1700', 'फेब्रुवरी १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ FEB 1700', 'अगाडि फेब्रुवरी १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '१५ मार्च १७०० ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'मार्च १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'मार्च १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ MAR 1700', 'मार्च १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ MAR 1700', 'मार्च १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ MAR 1700', 'अगाडि मार्च १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '१५ अप्रिल १७०० ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '१४ अप्रिल १६४५/४६ ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'अप्रिल १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'अप्रिल १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ APR 1700', 'अप्रिल १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ APR 1700', 'अप्रिल १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ APR 1700', 'अगाडि अप्रिल १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '१५ मई १७०० ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'मई १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'मई १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ MAY 1700', 'मई १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ MAY 1700', 'मई १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ MAY 1700', 'अगाडि मई १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '१५ जुन १७०० ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'जुन १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'जुन १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ JUN 1700', 'जुन १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ JUN 1700', 'जुन १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ JUN 1700', 'अगाडि जुन १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '१५ जुलाई १७०० ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'जुलाई १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'जुलाई १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ JUL 1700', 'जुलाई १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ JUL 1700', 'जुलाई १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ JUL 1700', 'अगाडि जुलाई १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '१५ अगस्ट १७०० ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'अगस्ट १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'अगस्ट १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ AUG 1700', 'अगस्ट १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ AUG 1700', 'अगस्ट १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ AUG 1700', 'अगाडि अगस्ट १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '१५ सेप्टेम्बर १७०० ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'सेप्टेम्बर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'सेप्टेम्बर १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ SEP 1700', 'सेप्टेम्बर १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ SEP 1700', 'सेप्टेम्बर १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ SEP 1700', 'अगाडि सेप्टेम्बर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '१५ अक्टोबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'अक्टोबर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'अक्टोबर १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ OCT 1700', 'अक्टोबर १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ OCT 1700', 'अक्टोबर १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ OCT 1700', 'अगाडि अक्टोबर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '१५ नोभेम्बर १७०० ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'नोभेम्बर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'नोभेम्बर १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ NOV 1700', 'नोभेम्बर १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ NOV 1700', 'नोभेम्बर १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ NOV 1700', 'अगाडि नोभेम्बर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '१५ डिसेम्बर १७०० ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'डिसेम्बर १७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'डिसेम्बर १७०० ᴄᴇ बारेमा'],
            ['FROM @#DJULIAN@ DEC 1700', 'डिसेम्बर १७०० ᴄᴇ बाट'],
            ['AFT @#DJULIAN@ DEC 1700', 'डिसेम्बर १७०० ᴄᴇ पछि'],
            ['BEF @#DJULIAN@ DEC 1700', 'अगाडि डिसेम्बर १७०० ᴄᴇ'],
            ['@#DJULIAN@ 1700', '१७०० ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० ᴄᴇ बारेमा'],
            ['CAL @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० ᴄᴇ गणना गरियो'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'अनुमानित १५ जनवरी १७०० ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'अगाडि १५ जनवरी १७०० ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० ᴄᴇ पछि'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० ᴄᴇ बाट'],
            ['TO @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० ᴄᴇ लाई'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '१५ जनवरी १७०० ᴄᴇ र १५ फेब्रुवरी १७०० ᴄᴇ को बीचमा'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '१५ जनवरी १७०० ᴄᴇ देखि १५ फेब्रुवरी १७०० ᴄᴇ सम्म'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted १५ जनवरी १७०० ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '१५ तिश्री ५७६५'],
            ['@#DHEBREW@ TSH 5765', 'तिश्री ५७६५'],
            ['ABT @#DHEBREW@ TSH 5765', 'तिश्री ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ TSH 5765', 'तिश्री ५७६५ बाट'],
            ['AFT @#DHEBREW@ TSH 5765', 'तिश्री ५७६५ पछि'],
            ['BEF @#DHEBREW@ TSH 5765', 'अगाडि तिश्री ५७६५'],
            ['@#DHEBREW@ 15 CSH 5765', '१५ हेस्भान ५७६५'],
            ['@#DHEBREW@ CSH 5765', 'हेस्भान ५७६५'],
            ['ABT @#DHEBREW@ CSH 5765', 'हेस्भान ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ CSH 5765', 'हेस्भान ५७६५ बाट'],
            ['AFT @#DHEBREW@ CSH 5765', 'हेस्भान ५७६५ पछि'],
            ['BEF @#DHEBREW@ CSH 5765', 'अगाडि हेस्भान ५७६५'],
            ['@#DHEBREW@ 15 KSL 5765', '१५ किस्लेभ ५७६५'],
            ['@#DHEBREW@ KSL 5765', 'किस्लेभ ५७६५'],
            ['ABT @#DHEBREW@ KSL 5765', 'किस्लेभ ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ KSL 5765', 'किस्लेभ ५७६५ बाट'],
            ['AFT @#DHEBREW@ KSL 5765', 'किस्लेभ ५७६५ पछि'],
            ['BEF @#DHEBREW@ KSL 5765', 'अगाडि किस्लेभ ५७६५'],
            ['@#DHEBREW@ 15 TVT 5765', '१५ टेभेट ५७६५'],
            ['@#DHEBREW@ TVT 5765', 'टेभेट ५७६५'],
            ['ABT @#DHEBREW@ TVT 5765', 'टेभेट ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ TVT 5765', 'टेभेट ५७६५ बाट'],
            ['AFT @#DHEBREW@ TVT 5765', 'टेभेट ५७६५ पछि'],
            ['BEF @#DHEBREW@ TVT 5765', 'अगाडि टेभेट ५७६५'],
            ['@#DHEBREW@ 15 SHV 5765', '१५ शेवत ५७६५'],
            ['@#DHEBREW@ SHV 5765', 'शेवत ५७६५'],
            ['ABT @#DHEBREW@ SHV 5765', 'शेवत ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ SHV 5765', 'शेवत ५७६५ बाट'],
            ['AFT @#DHEBREW@ SHV 5765', 'शेवत ५७६५ पछि'],
            ['BEF @#DHEBREW@ SHV 5765', 'अगाडि शेवत ५७६५'],
            ['@#DHEBREW@ 15 ADR 5765', '१५ अडार १ ५७६५'],
            ['@#DHEBREW@ ADR 5765', 'अडार १ ५७६५'],
            ['ABT @#DHEBREW@ ADR 5765', 'अडार १ ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ ADR 5765', 'अडार १ ५७६५ बाट'],
            ['AFT @#DHEBREW@ ADR 5765', 'अडार १ ५७६५ पछि'],
            ['BEF @#DHEBREW@ ADR 5765', 'अगाडि अडार १ ५७६५'],
            ['@#DHEBREW@ 15 ADS 5765', '१५ अडार २ ५७६५'],
            ['@#DHEBREW@ ADS 5765', 'अडार २ ५७६५'],
            ['ABT @#DHEBREW@ ADS 5765', 'अडार २ ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ ADS 5765', 'अडार २ ५७६५ बाट'],
            ['AFT @#DHEBREW@ ADS 5765', 'अडार २ ५७६५ पछि'],
            ['BEF @#DHEBREW@ ADS 5765', 'अगाडि अडार २ ५७६५'],
            ['@#DHEBREW@ 15 NSN 5765', '१५ निसान ५७६५'],
            ['@#DHEBREW@ NSN 5765', 'निसान ५७६५'],
            ['ABT @#DHEBREW@ NSN 5765', 'निसान ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ NSN 5765', 'निसान ५७६५ बाट'],
            ['AFT @#DHEBREW@ NSN 5765', 'निसान ५७६५ पछि'],
            ['BEF @#DHEBREW@ NSN 5765', 'अगाडि निसान ५७६५'],
            ['@#DHEBREW@ 15 IYR 5765', '१५ इयार ५७६५'],
            ['@#DHEBREW@ IYR 5765', 'इयार ५७६५'],
            ['ABT @#DHEBREW@ IYR 5765', 'इयार ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ IYR 5765', 'इयार ५७६५ बाट'],
            ['AFT @#DHEBREW@ IYR 5765', 'इयार ५७६५ पछि'],
            ['BEF @#DHEBREW@ IYR 5765', 'अगाडि इयार ५७६५'],
            ['@#DHEBREW@ 15 SVN 5765', '१५ सिभान ५७६५'],
            ['@#DHEBREW@ SVN 5765', 'सिभान ५७६५'],
            ['ABT @#DHEBREW@ SVN 5765', 'सिभान ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ SVN 5765', 'सिभान ५७६५ बाट'],
            ['AFT @#DHEBREW@ SVN 5765', 'सिभान ५७६५ पछि'],
            ['BEF @#DHEBREW@ SVN 5765', 'अगाडि सिभान ५७६५'],
            ['@#DHEBREW@ 15 TMZ 5765', '१५ टामुज ५७६५'],
            ['@#DHEBREW@ TMZ 5765', 'टामुज ५७६५'],
            ['ABT @#DHEBREW@ TMZ 5765', 'टामुज ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ TMZ 5765', 'टामुज ५७६५ बाट'],
            ['AFT @#DHEBREW@ TMZ 5765', 'टामुज ५७६५ पछि'],
            ['BEF @#DHEBREW@ TMZ 5765', 'अगाडि टामुज ५७६५'],
            ['@#DHEBREW@ 15 AAV 5765', '१५ एभ ५७६५'],
            ['@#DHEBREW@ AAV 5765', 'एभ ५७६५'],
            ['ABT @#DHEBREW@ AAV 5765', 'एभ ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ AAV 5765', 'एभ ५७६५ बाट'],
            ['AFT @#DHEBREW@ AAV 5765', 'एभ ५७६५ पछि'],
            ['BEF @#DHEBREW@ AAV 5765', 'अगाडि एभ ५७६५'],
            ['@#DHEBREW@ 15 ELL 5765', '१५ एलउल ५७६५'],
            ['@#DHEBREW@ ELL 5765', 'एलउल ५७६५'],
            ['ABT @#DHEBREW@ ELL 5765', 'एलउल ५७६५ बारेमा'],
            ['FROM @#DHEBREW@ ELL 5765', 'एलउल ५७६५ बाट'],
            ['AFT @#DHEBREW@ ELL 5765', 'एलउल ५७६५ पछि'],
            ['BEF @#DHEBREW@ ELL 5765', 'अगाडि एलउल ५७६५'],
            ['@#DHEBREW@ 5765', '५७६५'],
            ['ABT @#DHEBREW@ 15 TSH 5765', '१५ तिश्री ५७६५ बारेमा'],
            ['CAL @#DHEBREW@ 15 TSH 5765', '१५ तिश्री ५७६५ गणना गरियो'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'अनुमानित १५ तिश्री ५७६५'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'अगाडि १५ तिश्री ५७६५'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '१५ तिश्री ५७६५ पछि'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '१५ तिश्री ५७६५ बाट'],
            ['TO @#DHEBREW@ 15 TSH 5765', '१५ तिश्री ५७६५ लाई'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '१५ तिश्री ५७६५ र १५ हेस्भान ५७६५ को बीचमा'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '१५ तिश्री ५७६५ देखि १५ हेस्भान ५७६५ सम्म'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted १५ तिश्री ५७६५'],
            ['@#DFRENCH R@ 15 VEND 12', '१५ भेन्डेमाइर An XII'],
            ['@#DFRENCH R@ VEND 12', 'भेन्डेमाइर An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'भेन्डेमाइर An XII बारेमा'],
            ['FROM @#DFRENCH R@ VEND 12', 'भेन्डेमाइर An XII बाट'],
            ['AFT @#DFRENCH R@ VEND 12', 'भेन्डेमाइर An XII पछि'],
            ['BEF @#DFRENCH R@ VEND 12', 'अगाडि भेन्डेमाइर An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '१५ ब्रुमेयर An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ब्रुमेयर An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'ब्रुमेयर An XII बारेमा'],
            ['FROM @#DFRENCH R@ BRUM 12', 'ब्रुमेयर An XII बाट'],
            ['AFT @#DFRENCH R@ BRUM 12', 'ब्रुमेयर An XII पछि'],
            ['BEF @#DFRENCH R@ BRUM 12', 'अगाडि ब्रुमेयर An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '१५ फ्रिमेयर An XII'],
            ['@#DFRENCH R@ FRIM 12', 'फ्रिमेयर An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'फ्रिमेयर An XII बारेमा'],
            ['FROM @#DFRENCH R@ FRIM 12', 'फ्रिमेयर An XII बाट'],
            ['AFT @#DFRENCH R@ FRIM 12', 'फ्रिमेयर An XII पछि'],
            ['BEF @#DFRENCH R@ FRIM 12', 'अगाडि फ्रिमेयर An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '१५ निभोस An XII'],
            ['@#DFRENCH R@ NIVO 12', 'निभोस An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'निभोस An XII बारेमा'],
            ['FROM @#DFRENCH R@ NIVO 12', 'निभोस An XII बाट'],
            ['AFT @#DFRENCH R@ NIVO 12', 'निभोस An XII पछि'],
            ['BEF @#DFRENCH R@ NIVO 12', 'अगाडि निभोस An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '१५ प्लुभिओस् An XII'],
            ['@#DFRENCH R@ PLUV 12', 'प्लुभिओस् An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'प्लुभिओस् An XII बारेमा'],
            ['FROM @#DFRENCH R@ PLUV 12', 'प्लुभिओस् An XII बाट'],
            ['AFT @#DFRENCH R@ PLUV 12', 'प्लुभिओस् An XII पछि'],
            ['BEF @#DFRENCH R@ PLUV 12', 'अगाडि प्लुभिओस् An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '१५ भेन्टोज An XII'],
            ['@#DFRENCH R@ VENT 12', 'भेन्टोज An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'भेन्टोज An XII बारेमा'],
            ['FROM @#DFRENCH R@ VENT 12', 'भेन्टोज An XII बाट'],
            ['AFT @#DFRENCH R@ VENT 12', 'भेन्टोज An XII पछि'],
            ['BEF @#DFRENCH R@ VENT 12', 'अगाडि भेन्टोज An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '१५ कीटाणुजन्य An XII'],
            ['@#DFRENCH R@ GERM 12', 'कीटाणुजन्य An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'कीटाणुजन्य An XII बारेमा'],
            ['FROM @#DFRENCH R@ GERM 12', 'कीटाणुजन्य An XII बाट'],
            ['AFT @#DFRENCH R@ GERM 12', 'कीटाणुजन्य An XII पछि'],
            ['BEF @#DFRENCH R@ GERM 12', 'अगाडि कीटाणुजन्य An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '१५ फ्लोरियल An XII'],
            ['@#DFRENCH R@ FLOR 12', 'फ्लोरियल An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'फ्लोरियल An XII बारेमा'],
            ['FROM @#DFRENCH R@ FLOR 12', 'फ्लोरियल An XII बाट'],
            ['AFT @#DFRENCH R@ FLOR 12', 'फ्लोरियल An XII पछि'],
            ['BEF @#DFRENCH R@ FLOR 12', 'अगाडि फ्लोरियल An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '१५ प्रेयरियल An XII'],
            ['@#DFRENCH R@ PRAI 12', 'प्रेयरियल An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'प्रेयरियल An XII बारेमा'],
            ['FROM @#DFRENCH R@ PRAI 12', 'प्रेयरियल An XII बाट'],
            ['AFT @#DFRENCH R@ PRAI 12', 'प्रेयरियल An XII पछि'],
            ['BEF @#DFRENCH R@ PRAI 12', 'अगाडि प्रेयरियल An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '१५ मेसीडोर An XII'],
            ['@#DFRENCH R@ MESS 12', 'मेसीडोर An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'मेसीडोर An XII बारेमा'],
            ['FROM @#DFRENCH R@ MESS 12', 'मेसीडोर An XII बाट'],
            ['AFT @#DFRENCH R@ MESS 12', 'मेसीडोर An XII पछि'],
            ['BEF @#DFRENCH R@ MESS 12', 'अगाडि मेसीडोर An XII'],
            ['@#DFRENCH R@ 15 THER 12', '१५ थर्मिडर An XII'],
            ['@#DFRENCH R@ THER 12', 'थर्मिडर An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'थर्मिडर An XII बारेमा'],
            ['FROM @#DFRENCH R@ THER 12', 'थर्मिडर An XII बाट'],
            ['AFT @#DFRENCH R@ THER 12', 'थर्मिडर An XII पछि'],
            ['BEF @#DFRENCH R@ THER 12', 'अगाडि थर्मिडर An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '१५ फ्रुक्टिडोर An XII'],
            ['@#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII बारेमा'],
            ['FROM @#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII बाट'],
            ['AFT @#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII पछि'],
            ['BEF @#DFRENCH R@ FRUC 12', 'अगाडि फ्रुक्टिडोर An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '१५ पूरक दिनहरू An XII'],
            ['@#DFRENCH R@ COMP 12', 'पूरक दिनहरू An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'पूरक दिनहरू An XII बारेमा'],
            ['FROM @#DFRENCH R@ COMP 12', 'पूरक दिनहरू An XII बाट'],
            ['AFT @#DFRENCH R@ COMP 12', 'पूरक दिनहरू An XII पछि'],
            ['BEF @#DFRENCH R@ COMP 12', 'अगाडि पूरक दिनहरू An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', '१५ भेन्डेमाइर An XII बारेमा'],
            ['CAL @#DFRENCH R@ 15 VEND 12', '१५ भेन्डेमाइर An XII गणना गरियो'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'अनुमानित १५ भेन्डेमाइर An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'अगाडि १५ भेन्डेमाइर An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '१५ भेन्डेमाइर An XII पछि'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '१५ भेन्डेमाइर An XII बाट'],
            ['TO @#DFRENCH R@ 15 VEND 12', '१५ भेन्डेमाइर An XII लाई'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '१५ भेन्डेमाइर An XII र १५ ब्रुमेयर An XII को बीचमा'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '१५ भेन्डेमाइर An XII देखि १५ ब्रुमेयर An XII सम्म'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted १५ भेन्डेमाइर An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '१५ मुहाराम १४२५'],
            ['@#DHIJRI@ MUHAR 1425', 'मुहाराम १४२५'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'मुहाराम १४२५ बारेमा'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'मुहाराम १४२५ बाट'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'मुहाराम १४२५ पछि'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'अगाडि मुहाराम १४२५'],
            ['@#DHIJRI@ 15 SAFAR 1425', '१५ सफार १४२५'],
            ['@#DHIJRI@ SAFAR 1425', 'सफार १४२५'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'सफार १४२५ बारेमा'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'सफार १४२५ बाट'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'सफार १४२५ पछि'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'अगाडि सफार १४२५'],
            ['@#DHIJRI@ 15 RABIA 1425', '१५ रबी अल-अव्वल १४२५'],
            ['@#DHIJRI@ RABIA 1425', 'रबी अल-अव्वल १४२५'],
            ['ABT @#DHIJRI@ RABIA 1425', 'रबी अल-अव्वल १४२५ बारेमा'],
            ['FROM @#DHIJRI@ RABIA 1425', 'रबी अल-अव्वल १४२५ बाट'],
            ['AFT @#DHIJRI@ RABIA 1425', 'रबी अल-अव्वल १४२५ पछि'],
            ['BEF @#DHIJRI@ RABIA 1425', 'अगाडि रबी अल-अव्वल १४२५'],
            ['@#DHIJRI@ 15 RABIT 1425', '१५ रबी अस-सानी १४२५'],
            ['@#DHIJRI@ RABIT 1425', 'रबी अस-सानी १४२५'],
            ['ABT @#DHIJRI@ RABIT 1425', 'रबी अस-सानी १४२५ बारेमा'],
            ['FROM @#DHIJRI@ RABIT 1425', 'रबी अस-सानी १४२५ बाट'],
            ['AFT @#DHIJRI@ RABIT 1425', 'रबी अस-सानी १४२५ पछि'],
            ['BEF @#DHIJRI@ RABIT 1425', 'अगाडि रबी अस-सानी १४२५'],
            ['@#DHIJRI@ 15 JUMAA 1425', '१५ जुमाद अल अव्वल १४२५'],
            ['@#DHIJRI@ JUMAA 1425', 'जुमाद अल अव्वल १४२५'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'जुमाद अल अव्वल १४२५ बारेमा'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'जुमाद अल अव्वल १४२५ बाट'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'जुमाद अल अव्वल १४२५ पछि'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'अगाडि जुमाद अल अव्वल १४२५'],
            ['@#DHIJRI@ 15 JUMAT 1425', '१५ जुमादा अल थानी १४२५'],
            ['@#DHIJRI@ JUMAT 1425', 'जुमादा अल थानी १४२५'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'जुमादा अल थानी १४२५ बारेमा'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'जुमादा अल थानी १४२५ बाट'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'जुमादा अल थानी १४२५ पछि'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'अगाडि जुमादा अल थानी १४२५'],
            ['@#DHIJRI@ 15 RAJAB 1425', '१५ राजब १४२५'],
            ['@#DHIJRI@ RAJAB 1425', 'राजब १४२५'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'राजब १४२५ बारेमा'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'राजब १४२५ बाट'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'राजब १४२५ पछि'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'अगाडि राजब १४२५'],
            ['@#DHIJRI@ 15 SHAAB 1425', '१५ शाबान १४२५'],
            ['@#DHIJRI@ SHAAB 1425', 'शाबान १४२५'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'शाबान १४२५ बारेमा'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'शाबान १४२५ बाट'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'शाबान १४२५ पछि'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'अगाडि शाबान १४२५'],
            ['@#DHIJRI@ 15 RAMAD 1425', '१५ रमादान १४२५'],
            ['@#DHIJRI@ RAMAD 1425', 'रमादान १४२५'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'रमादान १४२५ बारेमा'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'रमादान १४२५ बाट'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'रमादान १४२५ पछि'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'अगाडि रमादान १४२५'],
            ['@#DHIJRI@ 15 SHAWW 1425', '१५ साववाल १४२५'],
            ['@#DHIJRI@ SHAWW 1425', 'साववाल १४२५'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'साववाल १४२५ बारेमा'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'साववाल १४२५ बाट'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'साववाल १४२५ पछि'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'अगाडि साववाल १४२५'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '१५ धु अल-किदाह १४२५'],
            ['@#DHIJRI@ DHUAQ 1425', 'धु अल-किदाह १४२५'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'धु अल-किदाह १४२५ बारेमा'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'धु अल-किदाह १४२५ बाट'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'धु अल-किदाह १४२५ पछि'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'अगाडि धु अल-किदाह १४२५'],
            ['@#DHIJRI@ 15 DHUAL 1425', '१४२५'],
            ['@#DHIJRI@ DHUAL 1425', '१४२५'],
            ['ABT @#DHIJRI@ DHUAL 1425', '१४२५ बारेमा'],
            ['FROM @#DHIJRI@ DHUAL 1425', '१४२५ बाट'],
            ['AFT @#DHIJRI@ DHUAL 1425', '१४२५ पछि'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'अगाडि १४२५'],
            ['@#DHIJRI@ 1425', '१४२५'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', '१५ मुहाराम १४२५ बारेमा'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', '१५ मुहाराम १४२५ गणना गरियो'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'अनुमानित १५ मुहाराम १४२५'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'अगाडि १५ मुहाराम १४२५'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '१५ मुहाराम १४२५ पछि'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '१५ मुहाराम १४२५ बाट'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '१५ मुहाराम १४२५ लाई'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '१५ मुहाराम १४२५ र १५ सफार १४२५ को बीचमा'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '१५ मुहाराम १४२५ देखि १५ सफार १४२५ सम्म'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted १५ मुहाराम १४२५'],
            ['@#DJALALI@ 15 FARVA 1384', '१५ फार्भार्डिन १३८४'],
            ['@#DJALALI@ FARVA 1384', 'फार्भार्डिन १३८४'],
            ['ABT @#DJALALI@ FARVA 1384', 'फार्भार्डिन १३८४ बारेमा'],
            ['FROM @#DJALALI@ FARVA 1384', 'फार्भार्डिन १३८४ बाट'],
            ['AFT @#DJALALI@ FARVA 1384', 'फार्भार्डिन १३८४ पछि'],
            ['BEF @#DJALALI@ FARVA 1384', 'अगाडि फार्भार्डिन १३८४'],
            ['@#DJALALI@ 15 ORDIB 1384', '१५ ओर्डिबेहेश १३८४'],
            ['@#DJALALI@ ORDIB 1384', 'ओर्डिबेहेश १३८४'],
            ['ABT @#DJALALI@ ORDIB 1384', 'ओर्डिबेहेश १३८४ बारेमा'],
            ['FROM @#DJALALI@ ORDIB 1384', 'ओर्डिबेहेश १३८४ बाट'],
            ['AFT @#DJALALI@ ORDIB 1384', 'ओर्डिबेहेश १३८४ पछि'],
            ['BEF @#DJALALI@ ORDIB 1384', 'अगाडि ओर्डिबेहेश १३८४'],
            ['@#DJALALI@ 15 KHORD 1384', '१५ खोर्डाद १३८४'],
            ['@#DJALALI@ KHORD 1384', 'खोर्डाद १३८४'],
            ['ABT @#DJALALI@ KHORD 1384', 'खोर्डाद १३८४ बारेमा'],
            ['FROM @#DJALALI@ KHORD 1384', 'खोर्डाद १३८४ बाट'],
            ['AFT @#DJALALI@ KHORD 1384', 'खोर्डाद १३८४ पछि'],
            ['BEF @#DJALALI@ KHORD 1384', 'अगाडि खोर्डाद १३८४'],
            ['@#DJALALI@ 15 TIR 1384', '१५ टिर १३८४'],
            ['@#DJALALI@ TIR 1384', 'टिर १३८४'],
            ['ABT @#DJALALI@ TIR 1384', 'टिर १३८४ बारेमा'],
            ['FROM @#DJALALI@ TIR 1384', 'टिर १३८४ बाट'],
            ['AFT @#DJALALI@ TIR 1384', 'टिर १३८४ पछि'],
            ['BEF @#DJALALI@ TIR 1384', 'अगाडि टिर १३८४'],
            ['@#DJALALI@ 15 MORDA 1384', '१५ मोर्डाद १३८४'],
            ['@#DJALALI@ MORDA 1384', 'मोर्डाद १३८४'],
            ['ABT @#DJALALI@ MORDA 1384', 'मोर्डाद १३८४ बारेमा'],
            ['FROM @#DJALALI@ MORDA 1384', 'मोर्डाद १३८४ बाट'],
            ['AFT @#DJALALI@ MORDA 1384', 'मोर्डाद १३८४ पछि'],
            ['BEF @#DJALALI@ MORDA 1384', 'अगाडि मोर्डाद १३८४'],
            ['@#DJALALI@ 15 SHAHR 1384', '१५ शाह्रिभर १३८४'],
            ['@#DJALALI@ SHAHR 1384', 'शाह्रिभर १३८४'],
            ['ABT @#DJALALI@ SHAHR 1384', 'शाह्रिभर १३८४ बारेमा'],
            ['FROM @#DJALALI@ SHAHR 1384', 'शाह्रिभर १३८४ बाट'],
            ['AFT @#DJALALI@ SHAHR 1384', 'शाह्रिभर १३८४ पछि'],
            ['BEF @#DJALALI@ SHAHR 1384', 'अगाडि शाह्रिभर १३८४'],
            ['@#DJALALI@ 15 MEHR 1384', '१५ मेहर १३८४'],
            ['@#DJALALI@ MEHR 1384', 'मेहर १३८४'],
            ['ABT @#DJALALI@ MEHR 1384', 'मेहर १३८४ बारेमा'],
            ['FROM @#DJALALI@ MEHR 1384', 'मेहर १३८४ बाट'],
            ['AFT @#DJALALI@ MEHR 1384', 'मेहर १३८४ पछि'],
            ['BEF @#DJALALI@ MEHR 1384', 'अगाडि मेहर १३८४'],
            ['@#DJALALI@ 15 ABAN 1384', '१५ अबान १३८४'],
            ['@#DJALALI@ ABAN 1384', 'अबान १३८४'],
            ['ABT @#DJALALI@ ABAN 1384', 'अबान १३८४ बारेमा'],
            ['FROM @#DJALALI@ ABAN 1384', 'अबान १३८४ बाट'],
            ['AFT @#DJALALI@ ABAN 1384', 'अबान १३८४ पछि'],
            ['BEF @#DJALALI@ ABAN 1384', 'अगाडि अबान १३८४'],
            ['@#DJALALI@ 15 AZAR 1384', '१५ अजार १३८४'],
            ['@#DJALALI@ AZAR 1384', 'अजार १३८४'],
            ['ABT @#DJALALI@ AZAR 1384', 'अजार १३८४ बारेमा'],
            ['FROM @#DJALALI@ AZAR 1384', 'अजार १३८४ बाट'],
            ['AFT @#DJALALI@ AZAR 1384', 'अजार १३८४ पछि'],
            ['BEF @#DJALALI@ AZAR 1384', 'अगाडि अजार १३८४'],
            ['@#DJALALI@ 15 DEY 1384', '१५ डेइ १३८४'],
            ['@#DJALALI@ DEY 1384', 'डेइ १३८४'],
            ['ABT @#DJALALI@ DEY 1384', 'डेइ १३८४ बारेमा'],
            ['FROM @#DJALALI@ DEY 1384', 'डेइ १३८४ बाट'],
            ['AFT @#DJALALI@ DEY 1384', 'डेइ १३८४ पछि'],
            ['BEF @#DJALALI@ DEY 1384', 'अगाडि डेइ १३८४'],
            ['@#DJALALI@ 15 BAHMA 1384', '१५ बाहमन १३८४'],
            ['@#DJALALI@ BAHMA 1384', 'बाहमन १३८४'],
            ['ABT @#DJALALI@ BAHMA 1384', 'बाहमन १३८४ बारेमा'],
            ['FROM @#DJALALI@ BAHMA 1384', 'बाहमन १३८४ बाट'],
            ['AFT @#DJALALI@ BAHMA 1384', 'बाहमन १३८४ पछि'],
            ['BEF @#DJALALI@ BAHMA 1384', 'अगाडि बाहमन १३८४'],
            ['@#DJALALI@ 15 ESFAN 1384', '१५ इस्फान्ड १३८४'],
            ['@#DJALALI@ ESFAN 1384', 'इस्फान्ड १३८४'],
            ['ABT @#DJALALI@ ESFAN 1384', 'इस्फान्ड १३८४ बारेमा'],
            ['FROM @#DJALALI@ ESFAN 1384', 'इस्फान्ड १३८४ बाट'],
            ['AFT @#DJALALI@ ESFAN 1384', 'इस्फान्ड १३८४ पछि'],
            ['BEF @#DJALALI@ ESFAN 1384', 'अगाडि इस्फान्ड १३८४'],
            ['@#DJALALI@ 1384', '१३८४'],
            ['ABT @#DJALALI@ 15 FARVA 1384', '१५ फार्भार्डिन १३८४ बारेमा'],
            ['CAL @#DJALALI@ 15 FARVA 1384', '१५ फार्भार्डिन १३८४ गणना गरियो'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'अनुमानित १५ फार्भार्डिन १३८४'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'अगाडि १५ फार्भार्डिन १३८४'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '१५ फार्भार्डिन १३८४ पछि'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '१५ फार्भार्डिन १३८४ बाट'],
            ['TO @#DJALALI@ 15 FARVA 1384', '१५ फार्भार्डिन १३८४ लाई'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '१५ फार्भार्डिन १३८४ र १५ ओर्डिबेहेश १३८४ को बीचमा'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '१५ फार्भार्डिन १३८४ देखि १५ ओर्डिबेहेश १३८४ सम्म'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted १५ फार्भार्डिन १३८४'],
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
        self::assertSame('one र two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two र three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one वा two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two वा three', $language->formatListOr(['one', 'two', 'three']));
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
        $elderBroOfH = self::male('ebh', "1 FAMS @febro@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1960");
        $youngerBroOfH = self::male('ybh', "1 FAMC @fp@\n1 BIRT\n2 DATE 1 JAN 1980");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        // Wife's family
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Brother's children (nieces/nephews through brother)
        $nieceFromBro = self::female('nb', "1 FAMC @febro@");
        $nephewFromBro = self::male('npb', "1 FAMC @febro@");

        // Sister's children (nieces/nephews through sister)
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins — paternal uncle's children
        $cousinMPat = self::male('cmp', "1 FAMC @febro@");
        // Cousins — paternal aunt's children
        $cousinFPat = self::female('cfp', "1 FAMC @fsis@");

        // Great-grandparents
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");

        // Uncle/aunt spouses
        $wifeOfElderBro = self::female('webo', "1 FAMS @febro@");
        $husbandOfSis = self::male('hsis', "1 FAMS @fsis@");

        // Wife's brother/sister with spouses for मामी/मौसा
        $brotherOfWife = self::male('bow', "1 FAMS @fbow@\n1 FAMC @fw@");
        $wifeOfBOW = self::female('wbow', "1 FAMS @fbow@");
        $sisterOfWife = self::female('sow', "1 FAMS @fsow@\n1 FAMC @fw@");
        $husbandOfSOW = self::male('hsow', "1 FAMS @fsow@");

        // Families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @ebh@\n1 CHIL @ybh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@\n1 CHIL @bow@\n1 CHIL @sow@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $febro = self::family('febro', "0 @febro@ FAM\n1 MARR Y\n1 HUSB @ebh@\n1 WIFE @webo@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cmp@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 MARR Y\n1 HUSB @hsis@\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cfp@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fbow = self::family('fbow', "0 @fbow@ FAM\n1 MARR Y\n1 HUSB @bow@\n1 WIFE @wbow@");
        $fsow = self::family('fsow', "0 @fsow@ FAM\n1 MARR Y\n1 HUSB @hsow@\n1 WIFE @sow@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child,
             $fatherOfH, $motherOfH, $elderBroOfH, $youngerBroOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinMPat, $cousinFPat,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $wifeOfElderBro, $husbandOfSis,
             $brotherOfWife, $wifeOfBOW, $sisterOfWife, $husbandOfSOW],
            [$fm, $fp, $fw, $fson, $fdau, $febro, $fsis, $fgp, $fbow, $fsow]
        );

        // Parents / Children
        self::assertRelationshipNames('आमा', 'छोरा', [$son, $fm, $wife]);
        self::assertRelationshipNames('बुबा', 'छोरा', [$son, $fm, $husband]);
        self::assertRelationshipNames('आमा', 'छोरी', [$daughter, $fm, $wife]);

        // Partners
        self::assertRelationshipNames('पत्नी', 'पति', [$husband, $fm, $wife]);

        // Siblings — elder/younger
        self::assertRelationshipNames('दिदी', 'भाइ', [$son, $fm, $daughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('सासू', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('ससुरा', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('सासू', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('ससुरा', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('बुहारी', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('ज्वाइँ', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — husband's siblings
        self::assertRelationshipName('ननन्द', [$wife, $fm, $husband, $fp, $sisterOfH]);

        // In-laws — wife's siblings
        self::assertRelationshipName('साला', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('साली', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // In-laws — sibling's spouse
        self::assertRelationshipName('भाउजू', [$husband, $fp, $elderBroOfH, $febro, $wifeOfElderBro]);
        self::assertRelationshipName('बहिनी ज्वाइँ', [$husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);

        // Grandparents — paternal
        self::assertRelationshipName('बज्यै', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('बाजे', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Grandparents — maternal
        self::assertRelationshipName('नानी', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('नाना', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('नाति', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('नातिनी', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts — paternal (फुपू = father's sister)
        self::assertRelationshipName('फुपू', [$son, $fm, $husband, $fp, $sisterOfH]);

        // Aunts — maternal (माइजू = mother's sister)
        self::assertRelationshipName('माइजू', [$son, $fm, $wife, $fw, $sisterOfWife]);

        // Uncles — paternal (काका = father's brother)
        self::assertRelationshipName('काका', [$son, $fm, $husband, $fp, $elderBroOfH]);
        self::assertRelationshipName('काका', [$son, $fm, $husband, $fp, $youngerBroOfH]);

        // Uncles — maternal (मामा = mother's brother)
        self::assertRelationshipName('मामा', [$son, $fm, $wife, $fw, $brotherOfWife]);

        // Uncle/aunt spouses
        self::assertRelationshipName('काकी', [$son, $fm, $husband, $fp, $elderBroOfH, $febro, $wifeOfElderBro]);
        self::assertRelationshipName('फुपाजु', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);
        self::assertRelationshipName('मामी', [$son, $fm, $wife, $fw, $brotherOfWife, $fbow, $wifeOfBOW]);
        self::assertRelationshipName('मौसा', [$son, $fm, $wife, $fw, $sisterOfWife, $fsow, $husbandOfSOW]);

        // Nieces/Nephews — through brother (भतिजी/भतिजा)
        self::assertRelationshipName('भतिजी', [$husband, $fp, $elderBroOfH, $febro, $nieceFromBro]);
        self::assertRelationshipName('भतिजा', [$husband, $fp, $elderBroOfH, $febro, $nephewFromBro]);

        // Nieces/Nephews — through sister (भान्जी/भान्जा)
        self::assertRelationshipName('भान्जी', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('भान्जा', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — paternal uncle's son (चचेरा भाइ)
        self::assertRelationshipName('चचेरा भाइ', [$son, $fm, $husband, $fp, $elderBroOfH, $febro, $cousinMPat]);

        // Cousins — paternal aunt's daughter (फुपुवा बहिनी)
        self::assertRelationshipName('फुपुवा बहिनी', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinFPat]);

        // Great-grandparents (dynamic — पर prefix)
        self::assertRelationshipName('परबज्यै', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('परबाजे', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('फुपू/माइजू ठूली', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('काका/मामा ठूला', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
