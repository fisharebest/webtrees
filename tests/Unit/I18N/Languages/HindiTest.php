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
use Fisharebest\Webtrees\I18N\Languages\Hindi;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Hindi::class)]
class HindiTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Hindi();
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
        self::assertSame('hi', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('हिन्दी', self::language()->endonym());
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
            ['ABT JAN 2000', 'लगभग जनवरी २०००'],
            ['FROM JAN 2000', 'जनवरी २००० से'],
            ['AFT JAN 2000', 'जनवरी २००० के बाद'],
            ['BEF JAN 2000', 'जनवरी २००० से पहले'],
            ['15 FEB 2000', '१५ फरवरी २०००'],
            ['FEB 2000', 'फरवरी २०००'],
            ['ABT FEB 2000', 'लगभग फरवरी २०००'],
            ['FROM FEB 2000', 'फरवरी २००० से'],
            ['AFT FEB 2000', 'फरवरी २००० के बाद'],
            ['BEF FEB 2000', 'फरवरी २००० से पहले'],
            ['15 MAR 2000', '१५ मार्च २०००'],
            ['MAR 2000', 'मार्च २०००'],
            ['ABT MAR 2000', 'लगभग मार्च २०००'],
            ['FROM MAR 2000', 'मार्च २००० से'],
            ['AFT MAR 2000', 'मार्च २००० के बाद'],
            ['BEF MAR 2000', 'मार्च २००० से पहले'],
            ['15 APR 2000', '१५ अप्रैल २०००'],
            ['APR 2000', 'अप्रैल २०००'],
            ['ABT APR 2000', 'लगभग अप्रैल २०००'],
            ['FROM APR 2000', 'अप्रैल २००० से'],
            ['AFT APR 2000', 'अप्रैल २००० के बाद'],
            ['BEF APR 2000', 'अप्रैल २००० से पहले'],
            ['15 MAY 2000', '१५ मई २०००'],
            ['MAY 2000', 'मई २०००'],
            ['ABT MAY 2000', 'लगभग मई २०००'],
            ['FROM MAY 2000', 'मई २००० से'],
            ['AFT MAY 2000', 'मई २००० के बाद'],
            ['BEF MAY 2000', 'मई २००० से पहले'],
            ['15 JUN 2000', '१५ जून २०००'],
            ['JUN 2000', 'जून २०००'],
            ['ABT JUN 2000', 'लगभग जून २०००'],
            ['FROM JUN 2000', 'जून २००० से'],
            ['AFT JUN 2000', 'जून २००० के बाद'],
            ['BEF JUN 2000', 'जून २००० से पहले'],
            ['15 JUL 2000', '१५ जुलाई २०००'],
            ['JUL 2000', 'जुलाई २०००'],
            ['ABT JUL 2000', 'लगभग जुलाई २०००'],
            ['FROM JUL 2000', 'जुलाई २००० से'],
            ['AFT JUL 2000', 'जुलाई २००० के बाद'],
            ['BEF JUL 2000', 'जुलाई २००० से पहले'],
            ['15 AUG 2000', '१५ अगस्त २०००'],
            ['AUG 2000', 'अगस्त २०००'],
            ['ABT AUG 2000', 'लगभग अगस्त २०००'],
            ['FROM AUG 2000', 'अगस्त २००० से'],
            ['AFT AUG 2000', 'अगस्त २००० के बाद'],
            ['BEF AUG 2000', 'अगस्त २००० से पहले'],
            ['15 SEP 2000', '१५ सितंबर २०००'],
            ['SEP 2000', 'सितंबर २०००'],
            ['ABT SEP 2000', 'लगभग सितंबर २०००'],
            ['FROM SEP 2000', 'सितंबर २००० से'],
            ['AFT SEP 2000', 'सितंबर २००० के बाद'],
            ['BEF SEP 2000', 'सितंबर २००० से पहले'],
            ['15 OCT 2000', '१५ अक्टूबर २०००'],
            ['OCT 2000', 'अक्टूबर २०००'],
            ['ABT OCT 2000', 'लगभग अक्टूबर २०००'],
            ['FROM OCT 2000', 'अक्टूबर २००० से'],
            ['AFT OCT 2000', 'अक्टूबर २००० के बाद'],
            ['BEF OCT 2000', 'अक्टूबर २००० से पहले'],
            ['15 NOV 2000', '१५ नवंबर २०००'],
            ['NOV 2000', 'नवंबर २०००'],
            ['ABT NOV 2000', 'लगभग नवंबर २०००'],
            ['FROM NOV 2000', 'नवंबर २००० से'],
            ['AFT NOV 2000', 'नवंबर २००० के बाद'],
            ['BEF NOV 2000', 'नवंबर २००० से पहले'],
            ['15 DEC 2000', '१५ दिसंबर २०००'],
            ['DEC 2000', 'दिसंबर २०००'],
            ['ABT DEC 2000', 'लगभग दिसंबर २०००'],
            ['FROM DEC 2000', 'दिसंबर २००० से'],
            ['AFT DEC 2000', 'दिसंबर २००० के बाद'],
            ['BEF DEC 2000', 'दिसंबर २००० से पहले'],
            ['2000', '२०००'],
            ['ABT 15 JAN 2000', 'लगभग १५ जनवरी २०००'],
            ['CAL 15 JAN 2000', 'परिकलित १५ जनवरी २०००'],
            ['EST 15 JAN 2000', 'अनुमानित १५ जनवरी २०००'],
            ['BEF 15 JAN 2000', '१५ जनवरी २००० से पहले'],
            ['AFT 15 JAN 2000', '१५ जनवरी २००० के बाद'],
            ['FROM 15 JAN 2000', '१५ जनवरी २००० से'],
            ['TO 15 JAN 2000', '१५ जनवरी २००० तक'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '१५ जनवरी २००० और १५ फरवरी २००० के बीच'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '१५ जनवरी २००० से १५ फरवरी २००० तक'],
            ['INT 15 JAN 2000', 'व्याख्यायित १५ जनवरी २०००'],
            ['@#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० सीई'],
            ['@#DJULIAN@ JAN 1700', 'जनवरी १७०० सीई'],
            ['ABT @#DJULIAN@ JAN 1700', 'लगभग जनवरी १७०० सीई'],
            ['FROM @#DJULIAN@ JAN 1700', 'जनवरी १७०० सीई से'],
            ['AFT @#DJULIAN@ JAN 1700', 'जनवरी १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ JAN 1700', 'जनवरी १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 FEB 1700', '१५ फरवरी १७०० सीई'],
            ['@#DJULIAN@ FEB 1700', 'फरवरी १७०० सीई'],
            ['ABT @#DJULIAN@ FEB 1700', 'लगभग फरवरी १७०० सीई'],
            ['FROM @#DJULIAN@ FEB 1700', 'फरवरी १७०० सीई से'],
            ['AFT @#DJULIAN@ FEB 1700', 'फरवरी १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ FEB 1700', 'फरवरी १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 MAR 1700', '१५ मार्च १७०० सीई'],
            ['@#DJULIAN@ MAR 1700', 'मार्च १७०० सीई'],
            ['ABT @#DJULIAN@ MAR 1700', 'लगभग मार्च १७०० सीई'],
            ['FROM @#DJULIAN@ MAR 1700', 'मार्च १७०० सीई से'],
            ['AFT @#DJULIAN@ MAR 1700', 'मार्च १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ MAR 1700', 'मार्च १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 APR 1700', '१५ अप्रैल १७०० सीई'],
            ['@#DJULIAN@ 14 APR 1645/46', '१४ अप्रैल १६४५/४६ सीई'],
            ['@#DJULIAN@ APR 1700', 'अप्रैल १७०० सीई'],
            ['ABT @#DJULIAN@ APR 1700', 'लगभग अप्रैल १७०० सीई'],
            ['FROM @#DJULIAN@ APR 1700', 'अप्रैल १७०० सीई से'],
            ['AFT @#DJULIAN@ APR 1700', 'अप्रैल १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ APR 1700', 'अप्रैल १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 MAY 1700', '१५ मई १७०० सीई'],
            ['@#DJULIAN@ MAY 1700', 'मई १७०० सीई'],
            ['ABT @#DJULIAN@ MAY 1700', 'लगभग मई १७०० सीई'],
            ['FROM @#DJULIAN@ MAY 1700', 'मई १७०० सीई से'],
            ['AFT @#DJULIAN@ MAY 1700', 'मई १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ MAY 1700', 'मई १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 JUN 1700', '१५ जून १७०० सीई'],
            ['@#DJULIAN@ JUN 1700', 'जून १७०० सीई'],
            ['ABT @#DJULIAN@ JUN 1700', 'लगभग जून १७०० सीई'],
            ['FROM @#DJULIAN@ JUN 1700', 'जून १७०० सीई से'],
            ['AFT @#DJULIAN@ JUN 1700', 'जून १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ JUN 1700', 'जून १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 JUL 1700', '१५ जुलाई १७०० सीई'],
            ['@#DJULIAN@ JUL 1700', 'जुलाई १७०० सीई'],
            ['ABT @#DJULIAN@ JUL 1700', 'लगभग जुलाई १७०० सीई'],
            ['FROM @#DJULIAN@ JUL 1700', 'जुलाई १७०० सीई से'],
            ['AFT @#DJULIAN@ JUL 1700', 'जुलाई १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ JUL 1700', 'जुलाई १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 AUG 1700', '१५ अगस्त १७०० सीई'],
            ['@#DJULIAN@ AUG 1700', 'अगस्त १७०० सीई'],
            ['ABT @#DJULIAN@ AUG 1700', 'लगभग अगस्त १७०० सीई'],
            ['FROM @#DJULIAN@ AUG 1700', 'अगस्त १७०० सीई से'],
            ['AFT @#DJULIAN@ AUG 1700', 'अगस्त १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ AUG 1700', 'अगस्त १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 SEP 1700', '१५ सितंबर १७०० सीई'],
            ['@#DJULIAN@ SEP 1700', 'सितंबर १७०० सीई'],
            ['ABT @#DJULIAN@ SEP 1700', 'लगभग सितंबर १७०० सीई'],
            ['FROM @#DJULIAN@ SEP 1700', 'सितंबर १७०० सीई से'],
            ['AFT @#DJULIAN@ SEP 1700', 'सितंबर १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ SEP 1700', 'सितंबर १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 OCT 1700', '१५ अक्टूबर १७०० सीई'],
            ['@#DJULIAN@ OCT 1700', 'अक्टूबर १७०० सीई'],
            ['ABT @#DJULIAN@ OCT 1700', 'लगभग अक्टूबर १७०० सीई'],
            ['FROM @#DJULIAN@ OCT 1700', 'अक्टूबर १७०० सीई से'],
            ['AFT @#DJULIAN@ OCT 1700', 'अक्टूबर १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ OCT 1700', 'अक्टूबर १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 NOV 1700', '१५ नवंबर १७०० सीई'],
            ['@#DJULIAN@ NOV 1700', 'नवंबर १७०० सीई'],
            ['ABT @#DJULIAN@ NOV 1700', 'लगभग नवंबर १७०० सीई'],
            ['FROM @#DJULIAN@ NOV 1700', 'नवंबर १७०० सीई से'],
            ['AFT @#DJULIAN@ NOV 1700', 'नवंबर १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ NOV 1700', 'नवंबर १७०० सीई से पहले'],
            ['@#DJULIAN@ 15 DEC 1700', '१५ दिसंबर १७०० सीई'],
            ['@#DJULIAN@ DEC 1700', 'दिसंबर १७०० सीई'],
            ['ABT @#DJULIAN@ DEC 1700', 'लगभग दिसंबर १७०० सीई'],
            ['FROM @#DJULIAN@ DEC 1700', 'दिसंबर १७०० सीई से'],
            ['AFT @#DJULIAN@ DEC 1700', 'दिसंबर १७०० सीई के बाद'],
            ['BEF @#DJULIAN@ DEC 1700', 'दिसंबर १७०० सीई से पहले'],
            ['@#DJULIAN@ 1700', '१७०० सीई'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'लगभग १५ जनवरी १७०० सीई'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'परिकलित १५ जनवरी १७०० सीई'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'अनुमानित १५ जनवरी १७०० सीई'],
            ['BEF @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० सीई से पहले'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० सीई के बाद'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० सीई से'],
            ['TO @#DJULIAN@ 15 JAN 1700', '१५ जनवरी १७०० सीई तक'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '१५ जनवरी १७०० सीई और १५ फरवरी १७०० सीई के बीच'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '१५ जनवरी १७०० सीई से १५ फरवरी १७०० सीई तक'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'व्याख्यायित १५ जनवरी १७०० सीई'],
            ['@#DHEBREW@ 15 TSH 5765', '१५ तिशरी ५७६५'],
            ['@#DHEBREW@ TSH 5765', 'तिशरी ५७६५'],
            ['ABT @#DHEBREW@ TSH 5765', 'लगभग तिशरी ५७६५'],
            ['FROM @#DHEBREW@ TSH 5765', 'तिशरी ५७६५ से'],
            ['AFT @#DHEBREW@ TSH 5765', 'तिशरी ५७६५ के बाद'],
            ['BEF @#DHEBREW@ TSH 5765', 'तिशरी ५७६५ से पहले'],
            ['@#DHEBREW@ 15 CSH 5765', '१५ चेशवन ५७६५'],
            ['@#DHEBREW@ CSH 5765', 'चेशवन ५७६५'],
            ['ABT @#DHEBREW@ CSH 5765', 'लगभग चेशवन ५७६५'],
            ['FROM @#DHEBREW@ CSH 5765', 'चेशवन ५७६५ से'],
            ['AFT @#DHEBREW@ CSH 5765', 'चेशवन ५७६५ के बाद'],
            ['BEF @#DHEBREW@ CSH 5765', 'चेशवन ५७६५ से पहले'],
            ['@#DHEBREW@ 15 KSL 5765', '१५ किसलेव ५७६५'],
            ['@#DHEBREW@ KSL 5765', 'किसलेव ५७६५'],
            ['ABT @#DHEBREW@ KSL 5765', 'लगभग किसलेव ५७६५'],
            ['FROM @#DHEBREW@ KSL 5765', 'किसलेव ५७६५ से'],
            ['AFT @#DHEBREW@ KSL 5765', 'किसलेव ५७६५ के बाद'],
            ['BEF @#DHEBREW@ KSL 5765', 'किसलेव ५७६५ से पहले'],
            ['@#DHEBREW@ 15 TVT 5765', '१५ तेवत ५७६५'],
            ['@#DHEBREW@ TVT 5765', 'तेवत ५७६५'],
            ['ABT @#DHEBREW@ TVT 5765', 'लगभग तेवत ५७६५'],
            ['FROM @#DHEBREW@ TVT 5765', 'तेवत ५७६५ से'],
            ['AFT @#DHEBREW@ TVT 5765', 'तेवत ५७६५ के बाद'],
            ['BEF @#DHEBREW@ TVT 5765', 'तेवत ५७६५ से पहले'],
            ['@#DHEBREW@ 15 SHV 5765', '१५ शेवत ५७६५'],
            ['@#DHEBREW@ SHV 5765', 'शेवत ५७६५'],
            ['ABT @#DHEBREW@ SHV 5765', 'लगभग शेवत ५७६५'],
            ['FROM @#DHEBREW@ SHV 5765', 'शेवत ५७६५ से'],
            ['AFT @#DHEBREW@ SHV 5765', 'शेवत ५७६५ के बाद'],
            ['BEF @#DHEBREW@ SHV 5765', 'शेवत ५७६५ से पहले'],
            ['@#DHEBREW@ 15 ADR 5765', '१५ अदार अव्वल ५७६५'],
            ['@#DHEBREW@ ADR 5765', 'अदार अव्वल ५७६५'],
            ['ABT @#DHEBREW@ ADR 5765', 'लगभग अदार अव्वल ५७६५'],
            ['FROM @#DHEBREW@ ADR 5765', 'अदार अव्वल ५७६५ से'],
            ['AFT @#DHEBREW@ ADR 5765', 'अदार अव्वल ५७६५ के बाद'],
            ['BEF @#DHEBREW@ ADR 5765', 'अदार अव्वल ५७६५ से पहले'],
            ['@#DHEBREW@ 15 ADS 5765', '१५ अदार दुवम ५७६५'],
            ['@#DHEBREW@ ADS 5765', 'अदार दुवम ५७६५'],
            ['ABT @#DHEBREW@ ADS 5765', 'लगभग अदार दुवम ५७६५'],
            ['FROM @#DHEBREW@ ADS 5765', 'अदार दुवम ५७६५ से'],
            ['AFT @#DHEBREW@ ADS 5765', 'अदार दुवम ५७६५ के बाद'],
            ['BEF @#DHEBREW@ ADS 5765', 'अदार दुवम ५७६५ से पहले'],
            ['@#DHEBREW@ 15 NSN 5765', '१५ निसान ५७६५'],
            ['@#DHEBREW@ NSN 5765', 'निसान ५७६५'],
            ['ABT @#DHEBREW@ NSN 5765', 'लगभग निसान ५७६५'],
            ['FROM @#DHEBREW@ NSN 5765', 'निसान ५७६५ से'],
            ['AFT @#DHEBREW@ NSN 5765', 'निसान ५७६५ के बाद'],
            ['BEF @#DHEBREW@ NSN 5765', 'निसान ५७६५ से पहले'],
            ['@#DHEBREW@ 15 IYR 5765', '१५ यार ५७६५'],
            ['@#DHEBREW@ IYR 5765', 'यार ५७६५'],
            ['ABT @#DHEBREW@ IYR 5765', 'लगभग यार ५७६५'],
            ['FROM @#DHEBREW@ IYR 5765', 'यार ५७६५ से'],
            ['AFT @#DHEBREW@ IYR 5765', 'यार ५७६५ के बाद'],
            ['BEF @#DHEBREW@ IYR 5765', 'यार ५७६५ से पहले'],
            ['@#DHEBREW@ 15 SVN 5765', '१५ सीवान ५७६५'],
            ['@#DHEBREW@ SVN 5765', 'सीवान ५७६५'],
            ['ABT @#DHEBREW@ SVN 5765', 'लगभग सीवान ५७६५'],
            ['FROM @#DHEBREW@ SVN 5765', 'सीवान ५७६५ से'],
            ['AFT @#DHEBREW@ SVN 5765', 'सीवान ५७६५ के बाद'],
            ['BEF @#DHEBREW@ SVN 5765', 'सीवान ५७६५ से पहले'],
            ['@#DHEBREW@ 15 TMZ 5765', '१५ तामुज़ ५७६५'],
            ['@#DHEBREW@ TMZ 5765', 'तामुज़ ५७६५'],
            ['ABT @#DHEBREW@ TMZ 5765', 'लगभग तामुज़ ५७६५'],
            ['FROM @#DHEBREW@ TMZ 5765', 'तामुज़ ५७६५ से'],
            ['AFT @#DHEBREW@ TMZ 5765', 'तामुज़ ५७६५ के बाद'],
            ['BEF @#DHEBREW@ TMZ 5765', 'तामुज़ ५७६५ से पहले'],
            ['@#DHEBREW@ 15 AAV 5765', '१५ आव ५७६५'],
            ['@#DHEBREW@ AAV 5765', 'आव ५७६५'],
            ['ABT @#DHEBREW@ AAV 5765', 'लगभग आव ५७६५'],
            ['FROM @#DHEBREW@ AAV 5765', 'आव ५७६५ से'],
            ['AFT @#DHEBREW@ AAV 5765', 'आव ५७६५ के बाद'],
            ['BEF @#DHEBREW@ AAV 5765', 'आव ५७६५ से पहले'],
            ['@#DHEBREW@ 15 ELL 5765', '१५ एलूल ५७६५'],
            ['@#DHEBREW@ ELL 5765', 'एलूल ५७६५'],
            ['ABT @#DHEBREW@ ELL 5765', 'लगभग एलूल ५७६५'],
            ['FROM @#DHEBREW@ ELL 5765', 'एलूल ५७६५ से'],
            ['AFT @#DHEBREW@ ELL 5765', 'एलूल ५७६५ के बाद'],
            ['BEF @#DHEBREW@ ELL 5765', 'एलूल ५७६५ से पहले'],
            ['@#DHEBREW@ 5765', '५७६५'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'लगभग १५ तिशरी ५७६५'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'परिकलित १५ तिशरी ५७६५'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'अनुमानित १५ तिशरी ५७६५'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '१५ तिशरी ५७६५ से पहले'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '१५ तिशरी ५७६५ के बाद'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '१५ तिशरी ५७६५ से'],
            ['TO @#DHEBREW@ 15 TSH 5765', '१५ तिशरी ५७६५ तक'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '१५ तिशरी ५७६५ और १५ चेशवन ५७६५ के बीच'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '१५ तिशरी ५७६५ से १५ चेशवन ५७६५ तक'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'व्याख्यायित १५ तिशरी ५७६५'],
            ['@#DFRENCH R@ 15 VEND 12', '१५ वेनडेमियर An XII'],
            ['@#DFRENCH R@ VEND 12', 'वेनडेमियर An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'लगभग वेनडेमियर An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'वेनडेमियर An XII से'],
            ['AFT @#DFRENCH R@ VEND 12', 'वेनडेमियर An XII के बाद'],
            ['BEF @#DFRENCH R@ VEND 12', 'वेनडेमियर An XII से पहले'],
            ['@#DFRENCH R@ 15 BRUM 12', '१५ ब्रूमेयर An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ब्रूमेयर An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'लगभग ब्रूमेयर An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'ब्रूमेयर An XII से'],
            ['AFT @#DFRENCH R@ BRUM 12', 'ब्रूमेयर An XII के बाद'],
            ['BEF @#DFRENCH R@ BRUM 12', 'ब्रूमेयर An XII से पहले'],
            ['@#DFRENCH R@ 15 FRIM 12', '१५ फ्रीमैर An XII'],
            ['@#DFRENCH R@ FRIM 12', 'फ्रीमैर An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'लगभग फ्रीमैर An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'फ्रीमैर An XII से'],
            ['AFT @#DFRENCH R@ FRIM 12', 'फ्रीमैर An XII के बाद'],
            ['BEF @#DFRENCH R@ FRIM 12', 'फ्रीमैर An XII से पहले'],
            ['@#DFRENCH R@ 15 NIVO 12', '१५ निवोस An XII'],
            ['@#DFRENCH R@ NIVO 12', 'निवोस An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'लगभग निवोस An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'निवोस An XII से'],
            ['AFT @#DFRENCH R@ NIVO 12', 'निवोस An XII के बाद'],
            ['BEF @#DFRENCH R@ NIVO 12', 'निवोस An XII से पहले'],
            ['@#DFRENCH R@ 15 PLUV 12', '१५ प्लूविओस An XII'],
            ['@#DFRENCH R@ PLUV 12', 'प्लूविओस An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'लगभग प्लूविओस An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'प्लूविओस An XII से'],
            ['AFT @#DFRENCH R@ PLUV 12', 'प्लूविओस An XII के बाद'],
            ['BEF @#DFRENCH R@ PLUV 12', 'प्लूविओस An XII से पहले'],
            ['@#DFRENCH R@ 15 VENT 12', '१५ वेन्टोस An XII'],
            ['@#DFRENCH R@ VENT 12', 'वेन्टोस An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'लगभग वेन्टोस An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'वेन्टोस An XII से'],
            ['AFT @#DFRENCH R@ VENT 12', 'वेन्टोस An XII के बाद'],
            ['BEF @#DFRENCH R@ VENT 12', 'वेन्टोस An XII से पहले'],
            ['@#DFRENCH R@ 15 GERM 12', '१५ जर्मिनल An XII'],
            ['@#DFRENCH R@ GERM 12', 'जर्मिनल An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'लगभग जर्मिनल An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'जर्मिनल An XII से'],
            ['AFT @#DFRENCH R@ GERM 12', 'जर्मिनल An XII के बाद'],
            ['BEF @#DFRENCH R@ GERM 12', 'जर्मिनल An XII से पहले'],
            ['@#DFRENCH R@ 15 FLOR 12', '१५ फ्लोरिअल An XII'],
            ['@#DFRENCH R@ FLOR 12', 'फ्लोरिअल An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'लगभग फ्लोरिअल An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'फ्लोरिअल An XII से'],
            ['AFT @#DFRENCH R@ FLOR 12', 'फ्लोरिअल An XII के बाद'],
            ['BEF @#DFRENCH R@ FLOR 12', 'फ्लोरिअल An XII से पहले'],
            ['@#DFRENCH R@ 15 PRAI 12', '१५ प्रेरिअल An XII'],
            ['@#DFRENCH R@ PRAI 12', 'प्रेरिअल An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'लगभग प्रेरिअल An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'प्रेरिअल An XII से'],
            ['AFT @#DFRENCH R@ PRAI 12', 'प्रेरिअल An XII के बाद'],
            ['BEF @#DFRENCH R@ PRAI 12', 'प्रेरिअल An XII से पहले'],
            ['@#DFRENCH R@ 15 MESS 12', '१५ मेसीडोर An XII'],
            ['@#DFRENCH R@ MESS 12', 'मेसीडोर An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'लगभग मेसीडोर An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'मेसीडोर An XII से'],
            ['AFT @#DFRENCH R@ MESS 12', 'मेसीडोर An XII के बाद'],
            ['BEF @#DFRENCH R@ MESS 12', 'मेसीडोर An XII से पहले'],
            ['@#DFRENCH R@ 15 THER 12', '१५ थर्मिडोर An XII'],
            ['@#DFRENCH R@ THER 12', 'थर्मिडोर An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'लगभग थर्मिडोर An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'थर्मिडोर An XII से'],
            ['AFT @#DFRENCH R@ THER 12', 'थर्मिडोर An XII के बाद'],
            ['BEF @#DFRENCH R@ THER 12', 'थर्मिडोर An XII से पहले'],
            ['@#DFRENCH R@ 15 FRUC 12', '१५ फ्रुक्टिडोर An XII'],
            ['@#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'लगभग फ्रुक्टिडोर An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII से'],
            ['AFT @#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII के बाद'],
            ['BEF @#DFRENCH R@ FRUC 12', 'फ्रुक्टिडोर An XII से पहले'],
            ['@#DFRENCH R@ 15 COMP 12', '१५ जोर्स कम्प्लीमेंटरेस An XII'],
            ['@#DFRENCH R@ COMP 12', 'जोर्स कम्प्लीमेंटरेस An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'लगभग जोर्स कम्प्लीमेंटरेस An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'जोर्स कम्प्लीमेंटरेस An XII से'],
            ['AFT @#DFRENCH R@ COMP 12', 'जोर्स कम्प्लीमेंटरेस An XII के बाद'],
            ['BEF @#DFRENCH R@ COMP 12', 'जोर्स कम्प्लीमेंटरेस An XII से पहले'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'लगभग १५ वेनडेमियर An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'परिकलित १५ वेनडेमियर An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'अनुमानित १५ वेनडेमियर An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', '१५ वेनडेमियर An XII से पहले'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '१५ वेनडेमियर An XII के बाद'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '१५ वेनडेमियर An XII से'],
            ['TO @#DFRENCH R@ 15 VEND 12', '१५ वेनडेमियर An XII तक'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '१५ वेनडेमियर An XII और १५ ब्रूमेयर An XII के बीच'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '१५ वेनडेमियर An XII से १५ ब्रूमेयर An XII तक'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'व्याख्यायित १५ वेनडेमियर An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '१५ मुहर्रम १४२५'],
            ['@#DHIJRI@ MUHAR 1425', 'मुहर्रम १४२५'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'लगभग मुहर्रम १४२५'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'मुहर्रम १४२५ से'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'मुहर्रम १४२५ के बाद'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'मुहर्रम १४२५ से पहले'],
            ['@#DHIJRI@ 15 SAFAR 1425', '१५ सफर १४२५'],
            ['@#DHIJRI@ SAFAR 1425', 'सफर १४२५'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'लगभग सफर १४२५'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'सफर १४२५ से'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'सफर १४२५ के बाद'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'सफर १४२५ से पहले'],
            ['@#DHIJRI@ 15 RABIA 1425', '१५ रबी उल-अव्वल १४२५'],
            ['@#DHIJRI@ RABIA 1425', 'रबी उल-अव्वल १४२५'],
            ['ABT @#DHIJRI@ RABIA 1425', 'लगभग रबी उल-अव्वल १४२५'],
            ['FROM @#DHIJRI@ RABIA 1425', 'रबी उल-अव्वल १४२५ से'],
            ['AFT @#DHIJRI@ RABIA 1425', 'रबी उल-अव्वल १४२५ के बाद'],
            ['BEF @#DHIJRI@ RABIA 1425', 'रबी उल-अव्वल १४२५ से पहले'],
            ['@#DHIJRI@ 15 RABIT 1425', '१५ रबी उल-आख़िर १४२५'],
            ['@#DHIJRI@ RABIT 1425', 'रबी उल-आख़िर १४२५'],
            ['ABT @#DHIJRI@ RABIT 1425', 'लगभग रबी उल-आख़िर १४२५'],
            ['FROM @#DHIJRI@ RABIT 1425', 'रबी उल-आख़िर १४२५ से'],
            ['AFT @#DHIJRI@ RABIT 1425', 'रबी उल-आख़िर १४२५ के बाद'],
            ['BEF @#DHIJRI@ RABIT 1425', 'रबी उल-आख़िर १४२५ से पहले'],
            ['@#DHIJRI@ 15 JUMAA 1425', '१५ जमादिल अव्वल १४२५'],
            ['@#DHIJRI@ JUMAA 1425', 'जमादिल अव्वल १४२५'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'लगभग जमादिल अव्वल १४२५'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'जमादिल अव्वल १४२५ से'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'जमादिल अव्वल १४२५ के बाद'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'जमादिल अव्वल १४२५ से पहले'],
            ['@#DHIJRI@ 15 JUMAT 1425', '१५ जमादिल सानी १४२५'],
            ['@#DHIJRI@ JUMAT 1425', 'जमादिल सानी १४२५'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'लगभग जमादिल सानी १४२५'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'जमादिल सानी १४२५ से'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'जमादिल सानी १४२५ के बाद'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'जमादिल सानी १४२५ से पहले'],
            ['@#DHIJRI@ 15 RAJAB 1425', '१५ रज्जब १४२५'],
            ['@#DHIJRI@ RAJAB 1425', 'रज्जब १४२५'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'लगभग रज्जब १४२५'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'रज्जब १४२५ से'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'रज्जब १४२५ के बाद'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'रज्जब १४२५ से पहले'],
            ['@#DHIJRI@ 15 SHAAB 1425', '१५ शाबान १४२५'],
            ['@#DHIJRI@ SHAAB 1425', 'शाबान १४२५'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'लगभग शाबान १४२५'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'शाबान १४२५ से'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'शाबान १४२५ के बाद'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'शाबान १४२५ से पहले'],
            ['@#DHIJRI@ 15 RAMAD 1425', '१५ रमजान १४२५'],
            ['@#DHIJRI@ RAMAD 1425', 'रमजान १४२५'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'लगभग रमजान १४२५'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'रमजान १४२५ से'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'रमजान १४२५ के बाद'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'रमजान १४२५ से पहले'],
            ['@#DHIJRI@ 15 SHAWW 1425', '१५ शव्वाल १४२५'],
            ['@#DHIJRI@ SHAWW 1425', 'शव्वाल १४२५'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'लगभग शव्वाल १४२५'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'शव्वाल १४२५ से'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'शव्वाल १४२५ के बाद'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'शव्वाल १४२५ से पहले'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '१५ ज़िल क़दा १४२५'],
            ['@#DHIJRI@ DHUAQ 1425', 'ज़िल क़दा १४२५'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'लगभग ज़िल क़दा १४२५'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'ज़िल क़दा १४२५ से'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'ज़िल क़दा १४२५ के बाद'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'ज़िल क़दा १४२५ से पहले'],
            ['@#DHIJRI@ 15 DHUAL 1425', '१४२५'],
            ['@#DHIJRI@ DHUAL 1425', '१४२५'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'लगभग १४२५'],
            ['FROM @#DHIJRI@ DHUAL 1425', '१४२५ से'],
            ['AFT @#DHIJRI@ DHUAL 1425', '१४२५ के बाद'],
            ['BEF @#DHIJRI@ DHUAL 1425', '१४२५ से पहले'],
            ['@#DHIJRI@ 1425', '१४२५'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'लगभग १५ मुहर्रम १४२५'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'परिकलित १५ मुहर्रम १४२५'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'अनुमानित १५ मुहर्रम १४२५'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '१५ मुहर्रम १४२५ से पहले'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '१५ मुहर्रम १४२५ के बाद'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '१५ मुहर्रम १४२५ से'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '१५ मुहर्रम १४२५ तक'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '१५ मुहर्रम १४२५ और १५ सफर १४२५ के बीच'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '१५ मुहर्रम १४२५ से १५ सफर १४२५ तक'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'व्याख्यायित १५ मुहर्रम १४२५'],
            ['@#DJALALI@ 15 FARVA 1384', '१५ फरवरदिन १३८४'],
            ['@#DJALALI@ FARVA 1384', 'फरवरदिन १३८४'],
            ['ABT @#DJALALI@ FARVA 1384', 'लगभग फरवरदिन १३८४'],
            ['FROM @#DJALALI@ FARVA 1384', 'फरवरदिन १३८४ से'],
            ['AFT @#DJALALI@ FARVA 1384', 'फरवरदिन १३८४ के बाद'],
            ['BEF @#DJALALI@ FARVA 1384', 'फरवरदिन १३८४ से पहले'],
            ['@#DJALALI@ 15 ORDIB 1384', '१५ ओर्दिबेहेश्त १३८४'],
            ['@#DJALALI@ ORDIB 1384', 'ओर्दिबेहेश्त १३८४'],
            ['ABT @#DJALALI@ ORDIB 1384', 'लगभग ओर्दिबेहेश्त १३८४'],
            ['FROM @#DJALALI@ ORDIB 1384', 'ओर्दिबेहेश्त १३८४ से'],
            ['AFT @#DJALALI@ ORDIB 1384', 'ओर्दिबेहेश्त १३८४ के बाद'],
            ['BEF @#DJALALI@ ORDIB 1384', 'ओर्दिबेहेश्त १३८४ से पहले'],
            ['@#DJALALI@ 15 KHORD 1384', '१५ खोरदाद १३८४'],
            ['@#DJALALI@ KHORD 1384', 'खोरदाद १३८४'],
            ['ABT @#DJALALI@ KHORD 1384', 'लगभग खोरदाद १३८४'],
            ['FROM @#DJALALI@ KHORD 1384', 'खोरदाद १३८४ से'],
            ['AFT @#DJALALI@ KHORD 1384', 'खोरदाद १३८४ के बाद'],
            ['BEF @#DJALALI@ KHORD 1384', 'खोरदाद १३८४ से पहले'],
            ['@#DJALALI@ 15 TIR 1384', '१५ तीर १३८४'],
            ['@#DJALALI@ TIR 1384', 'तीर १३८४'],
            ['ABT @#DJALALI@ TIR 1384', 'लगभग तीर १३८४'],
            ['FROM @#DJALALI@ TIR 1384', 'तीर १३८४ से'],
            ['AFT @#DJALALI@ TIR 1384', 'तीर १३८४ के बाद'],
            ['BEF @#DJALALI@ TIR 1384', 'तीर १३८४ से पहले'],
            ['@#DJALALI@ 15 MORDA 1384', '१५ मोरदाद १३८४'],
            ['@#DJALALI@ MORDA 1384', 'मोरदाद १३८४'],
            ['ABT @#DJALALI@ MORDA 1384', 'लगभग मोरदाद १३८४'],
            ['FROM @#DJALALI@ MORDA 1384', 'मोरदाद १३८४ से'],
            ['AFT @#DJALALI@ MORDA 1384', 'मोरदाद १३८४ के बाद'],
            ['BEF @#DJALALI@ MORDA 1384', 'मोरदाद १३८४ से पहले'],
            ['@#DJALALI@ 15 SHAHR 1384', '१५ शहरीवर १३८४'],
            ['@#DJALALI@ SHAHR 1384', 'शहरीवर १३८४'],
            ['ABT @#DJALALI@ SHAHR 1384', 'लगभग शहरीवर १३८४'],
            ['FROM @#DJALALI@ SHAHR 1384', 'शहरीवर १३८४ से'],
            ['AFT @#DJALALI@ SHAHR 1384', 'शहरीवर १३८४ के बाद'],
            ['BEF @#DJALALI@ SHAHR 1384', 'शहरीवर १३८४ से पहले'],
            ['@#DJALALI@ 15 MEHR 1384', '१५ मेहर १३८४'],
            ['@#DJALALI@ MEHR 1384', 'मेहर १३८४'],
            ['ABT @#DJALALI@ MEHR 1384', 'लगभग मेहर १३८४'],
            ['FROM @#DJALALI@ MEHR 1384', 'मेहर १३८४ से'],
            ['AFT @#DJALALI@ MEHR 1384', 'मेहर १३८४ के बाद'],
            ['BEF @#DJALALI@ MEHR 1384', 'मेहर १३८४ से पहले'],
            ['@#DJALALI@ 15 ABAN 1384', '१५ आबान १३८४'],
            ['@#DJALALI@ ABAN 1384', 'आबान १३८४'],
            ['ABT @#DJALALI@ ABAN 1384', 'लगभग आबान १३८४'],
            ['FROM @#DJALALI@ ABAN 1384', 'आबान १३८४ से'],
            ['AFT @#DJALALI@ ABAN 1384', 'आबान १३८४ के बाद'],
            ['BEF @#DJALALI@ ABAN 1384', 'आबान १३८४ से पहले'],
            ['@#DJALALI@ 15 AZAR 1384', '१५ आज़र १३८४'],
            ['@#DJALALI@ AZAR 1384', 'आज़र १३८४'],
            ['ABT @#DJALALI@ AZAR 1384', 'लगभग आज़र १३८४'],
            ['FROM @#DJALALI@ AZAR 1384', 'आज़र १३८४ से'],
            ['AFT @#DJALALI@ AZAR 1384', 'आज़र १३८४ के बाद'],
            ['BEF @#DJALALI@ AZAR 1384', 'आज़र १३८४ से पहले'],
            ['@#DJALALI@ 15 DEY 1384', '१५ दे १३८४'],
            ['@#DJALALI@ DEY 1384', 'दे १३८४'],
            ['ABT @#DJALALI@ DEY 1384', 'लगभग दे १३८४'],
            ['FROM @#DJALALI@ DEY 1384', 'दे १३८४ से'],
            ['AFT @#DJALALI@ DEY 1384', 'दे १३८४ के बाद'],
            ['BEF @#DJALALI@ DEY 1384', 'दे १३८४ से पहले'],
            ['@#DJALALI@ 15 BAHMA 1384', '१५ बेहमन १३८४'],
            ['@#DJALALI@ BAHMA 1384', 'बेहमन १३८४'],
            ['ABT @#DJALALI@ BAHMA 1384', 'लगभग बेहमन १३८४'],
            ['FROM @#DJALALI@ BAHMA 1384', 'बेहमन १३८४ से'],
            ['AFT @#DJALALI@ BAHMA 1384', 'बेहमन १३८४ के बाद'],
            ['BEF @#DJALALI@ BAHMA 1384', 'बेहमन १३८४ से पहले'],
            ['@#DJALALI@ 15 ESFAN 1384', '१५ इसफंद १३८४'],
            ['@#DJALALI@ ESFAN 1384', 'इसफंद १३८४'],
            ['ABT @#DJALALI@ ESFAN 1384', 'लगभग इसफंद १३८४'],
            ['FROM @#DJALALI@ ESFAN 1384', 'इसफंद १३८४ से'],
            ['AFT @#DJALALI@ ESFAN 1384', 'इसफंद १३८४ के बाद'],
            ['BEF @#DJALALI@ ESFAN 1384', 'इसफंद १३८४ से पहले'],
            ['@#DJALALI@ 1384', '१३८४'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'लगभग १५ फरवरदिन १३८४'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'परिकलित १५ फरवरदिन १३८४'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'अनुमानित १५ फरवरदिन १३८४'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '१५ फरवरदिन १३८४ से पहले'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '१५ फरवरदिन १३८४ के बाद'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '१५ फरवरदिन १३८४ से'],
            ['TO @#DJALALI@ 15 FARVA 1384', '१५ फरवरदिन १३८४ तक'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '१५ फरवरदिन १३८४ और १५ ओर्दिबेहेश्त १३८४ के बीच'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '१५ फरवरदिन १३८४ से १५ ओर्दिबेहेश्त १३८४ तक'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'व्याख्यायित १५ फरवरदिन १३८४'],
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
        self::assertSame('one और two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two और three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one या two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two या three', $language->formatListOr(['one', 'two', 'three']));
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

        // Wife's brother's wife for मामी
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
        self::assertRelationshipNames('माँ', 'बेटा', [$son, $fm, $wife]);
        self::assertRelationshipNames('पिता', 'बेटा', [$son, $fm, $husband]);
        self::assertRelationshipNames('माँ', 'बेटी', [$daughter, $fm, $wife]);

        // Partners
        self::assertRelationshipNames('पत्नी', 'पति', [$husband, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('बड़ी बहन', 'छोटा भाई', [$son, $fm, $daughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('सास', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('ससुर', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('सास', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('ससुर', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('बहू', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('दामाद', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — husband's siblings
        self::assertRelationshipName('ननद', [$wife, $fm, $husband, $fp, $sisterOfH]);

        // In-laws — wife's siblings
        self::assertRelationshipName('साला', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('साली', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // In-laws — sibling's spouse
        self::assertRelationshipName('भाभी', [$husband, $fp, $elderBroOfH, $febro, $wifeOfElderBro]);
        self::assertRelationshipName('बहनोई', [$husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);

        // Grandparents — paternal
        self::assertRelationshipName('दादी', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('दादा', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Grandparents — maternal
        self::assertRelationshipName('नानी', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('नाना', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('पोता', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('पोती', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts — paternal (बुआ = father's sister)
        self::assertRelationshipName('बुआ', [$son, $fm, $husband, $fp, $sisterOfH]);

        // Aunts — maternal (मौसी = mother's sister)
        self::assertRelationshipName('मौसी', [$son, $fm, $wife, $fw, $sisterOfWife]);

        // Uncles — paternal (चाचा = father's brother)
        self::assertRelationshipName('चाचा', [$son, $fm, $husband, $fp, $elderBroOfH]);
        self::assertRelationshipName('चाचा', [$son, $fm, $husband, $fp, $youngerBroOfH]);

        // Uncles — maternal (मामा = mother's brother)
        self::assertRelationshipName('मामा', [$son, $fm, $wife, $fw, $brotherOfWife]);

        // Uncle/aunt spouses
        self::assertRelationshipName('चाची', [$son, $fm, $husband, $fp, $elderBroOfH, $febro, $wifeOfElderBro]);
        self::assertRelationshipName('फूफा', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);
        self::assertRelationshipName('मामी', [$son, $fm, $wife, $fw, $brotherOfWife, $fbow, $wifeOfBOW]);
        self::assertRelationshipName('मौसा', [$son, $fm, $wife, $fw, $sisterOfWife, $fsow, $husbandOfSOW]);

        // Nieces/Nephews — through brother (भतीजी/भतीजा)
        self::assertRelationshipName('भतीजी', [$husband, $fp, $elderBroOfH, $febro, $nieceFromBro]);
        self::assertRelationshipName('भतीजा', [$husband, $fp, $elderBroOfH, $febro, $nephewFromBro]);

        // Nieces/Nephews — through sister (भांजी/भांजा)
        self::assertRelationshipName('भांजी', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('भांजा', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — paternal uncle's son (चचेरा भाई)
        self::assertRelationshipName('चचेरा भाई', [$son, $fm, $husband, $fp, $elderBroOfH, $febro, $cousinMPat]);

        // Cousins — paternal aunt's daughter (फुफेरी बहन)
        self::assertRelationshipName('फुफेरी बहन', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinFPat]);

        // Great-grandparents (dynamic — पर prefix)
        self::assertRelationshipName('परदादी', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('परदादा', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('बुआ/मौसी बड़ी', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('चाचा/मामा बड़े', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
