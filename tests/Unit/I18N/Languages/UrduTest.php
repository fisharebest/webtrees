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
use Fisharebest\Webtrees\I18N\Languages\Urdu;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Urdu::class)]
class UrduTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Urdu();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Arab, self::language()->script());
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
        self::assertSame(TextDirection::RTL, self::language()->textDirection());
    }

    public function testAlphabet(): void
    {
        self::assertSame(['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي', 'آ', 'ة', 'ى', 'ی'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('ur', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('اردو', self::language()->endonym());
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
        self::assertSame('-١٢٣,٤٥٦.٠٧٨٩', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('‎-١,٢٣,٤٥٦.٠٧٨٩', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('‎-١,٢٣,٤٥٦.٠٧٨٩%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
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
        self::assertSame('one اور two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one، two اور three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one یا two', $language->formatListOr(['one', 'two']));
        self::assertSame('one، two یا three', $language->formatListOr(['one', 'two', 'three']));
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

        // Wife's brother's wife and sister's husband
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
        self::assertRelationshipNames('امّی', 'بیٹا', [$son, $fm, $wife]);
        self::assertRelationshipNames('ابّو', 'بیٹا', [$son, $fm, $husband]);
        self::assertRelationshipNames('امّی', 'بیٹی', [$daughter, $fm, $wife]);

        // Partners
        self::assertRelationshipNames('بیوی', 'شوہر', [$husband, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('بڑی بہن', 'چھوٹا بھائی', [$son, $fm, $daughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('ساس', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('سسر', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('ساس', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('سسر', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('بہو', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('داماد', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — husband's siblings
        self::assertRelationshipName('ننّد', [$wife, $fm, $husband, $fp, $sisterOfH]);

        // In-laws — wife's siblings
        self::assertRelationshipName('سالا', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('سالی', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // In-laws — sibling's spouse
        self::assertRelationshipName('بھابھی', [$husband, $fp, $elderBroOfH, $febro, $wifeOfElderBro]);
        self::assertRelationshipName('بہنوئی', [$husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);

        // Grandparents — paternal
        self::assertRelationshipName('دادی', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('دادا', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Grandparents — maternal
        self::assertRelationshipName('نانی', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('نانا', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('پوتا', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('پوتی', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts — paternal (پھوپھی = father's sister)
        self::assertRelationshipName('پھوپھی', [$son, $fm, $husband, $fp, $sisterOfH]);

        // Aunts — maternal (خالہ = mother's sister)
        self::assertRelationshipName('خالہ', [$son, $fm, $wife, $fw, $sisterOfWife]);

        // Uncles — paternal (چچا = father's brother)
        self::assertRelationshipName('چچا', [$son, $fm, $husband, $fp, $elderBroOfH]);
        self::assertRelationshipName('چچا', [$son, $fm, $husband, $fp, $youngerBroOfH]);

        // Uncles — maternal (ماموں = mother's brother)
        self::assertRelationshipName('ماموں', [$son, $fm, $wife, $fw, $brotherOfWife]);

        // Uncle/aunt spouses
        self::assertRelationshipName('چچی', [$son, $fm, $husband, $fp, $elderBroOfH, $febro, $wifeOfElderBro]);
        self::assertRelationshipName('پھوپھا', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $husbandOfSis]);
        self::assertRelationshipName('ممانی', [$son, $fm, $wife, $fw, $brotherOfWife, $fbow, $wifeOfBOW]);
        self::assertRelationshipName('خالو', [$son, $fm, $wife, $fw, $sisterOfWife, $fsow, $husbandOfSOW]);

        // Nieces/Nephews — through brother (بھتیجی/بھتیجا)
        self::assertRelationshipName('بھتیجی', [$husband, $fp, $elderBroOfH, $febro, $nieceFromBro]);
        self::assertRelationshipName('بھتیجا', [$husband, $fp, $elderBroOfH, $febro, $nephewFromBro]);

        // Nieces/Nephews — through sister (بھانجی/بھانجا)
        self::assertRelationshipName('بھانجی', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('بھانجا', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins — paternal uncle's son (چچا زاد بھائی)
        self::assertRelationshipName('چچا زاد بھائی', [$son, $fm, $husband, $fp, $elderBroOfH, $febro, $cousinMPat]);

        // Cousins — paternal aunt's daughter (پھوپھی زاد بہن)
        self::assertRelationshipName('پھوپھی زاد بہن', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinFPat]);

        // Great-grandparents (dynamic — پر prefix)
        self::assertRelationshipName('پردادی', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('پردادا', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('پھوپھی/خالہ بڑی', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('چچا/ماموں بڑے', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '١٥ جنوری ٢٠٠٠'],
            ['JAN 2000', 'جنوری ٢٠٠٠'],
            ['ABT JAN 2000', 'تقریباً جنوری ٢٠٠٠'],
            ['FROM JAN 2000', 'جنوری ٢٠٠٠ سے'],
            ['AFT JAN 2000', 'بعد از جنوری ٢٠٠٠'],
            ['BEF JAN 2000', 'قبل از جنوری ٢٠٠٠'],
            ['15 FEB 2000', '١٥ فروری ٢٠٠٠'],
            ['FEB 2000', 'فروری ٢٠٠٠'],
            ['ABT FEB 2000', 'تقریباً فروری ٢٠٠٠'],
            ['FROM FEB 2000', 'فروری ٢٠٠٠ سے'],
            ['AFT FEB 2000', 'بعد از فروری ٢٠٠٠'],
            ['BEF FEB 2000', 'قبل از فروری ٢٠٠٠'],
            ['15 MAR 2000', '١٥ مارچ ٢٠٠٠'],
            ['MAR 2000', 'مارچ ٢٠٠٠'],
            ['ABT MAR 2000', 'تقریباً مارچ ٢٠٠٠'],
            ['FROM MAR 2000', 'مارچ ٢٠٠٠ سے'],
            ['AFT MAR 2000', 'بعد از مارچ ٢٠٠٠'],
            ['BEF MAR 2000', 'قبل از مارچ ٢٠٠٠'],
            ['15 APR 2000', '١٥ اپریل ٢٠٠٠'],
            ['APR 2000', 'اپریل ٢٠٠٠'],
            ['ABT APR 2000', 'تقریباً اپریل ٢٠٠٠'],
            ['FROM APR 2000', 'اپریل ٢٠٠٠ سے'],
            ['AFT APR 2000', 'بعد از اپریل ٢٠٠٠'],
            ['BEF APR 2000', 'قبل از اپریل ٢٠٠٠'],
            ['15 MAY 2000', '١٥ مئی ٢٠٠٠'],
            ['MAY 2000', 'مئی ٢٠٠٠'],
            ['ABT MAY 2000', 'تقریباً مئی ٢٠٠٠'],
            ['FROM MAY 2000', 'مئی ٢٠٠٠ سے'],
            ['AFT MAY 2000', 'بعد از مئی ٢٠٠٠'],
            ['BEF MAY 2000', 'قبل از مئی ٢٠٠٠'],
            ['15 JUN 2000', '١٥ جون ٢٠٠٠'],
            ['JUN 2000', 'جون ٢٠٠٠'],
            ['ABT JUN 2000', 'تقریباً جون ٢٠٠٠'],
            ['FROM JUN 2000', 'جون ٢٠٠٠ سے'],
            ['AFT JUN 2000', 'بعد از جون ٢٠٠٠'],
            ['BEF JUN 2000', 'قبل از جون ٢٠٠٠'],
            ['15 JUL 2000', '١٥ جولائی ٢٠٠٠'],
            ['JUL 2000', 'جولائی ٢٠٠٠'],
            ['ABT JUL 2000', 'تقریباً جولائی ٢٠٠٠'],
            ['FROM JUL 2000', 'جولائی ٢٠٠٠ سے'],
            ['AFT JUL 2000', 'بعد از جولائی ٢٠٠٠'],
            ['BEF JUL 2000', 'قبل از جولائی ٢٠٠٠'],
            ['15 AUG 2000', '١٥ اگست ٢٠٠٠'],
            ['AUG 2000', 'اگست ٢٠٠٠'],
            ['ABT AUG 2000', 'تقریباً اگست ٢٠٠٠'],
            ['FROM AUG 2000', 'اگست ٢٠٠٠ سے'],
            ['AFT AUG 2000', 'بعد از اگست ٢٠٠٠'],
            ['BEF AUG 2000', 'قبل از اگست ٢٠٠٠'],
            ['15 SEP 2000', '١٥ ستمبر ٢٠٠٠'],
            ['SEP 2000', 'ستمبر ٢٠٠٠'],
            ['ABT SEP 2000', 'تقریباً ستمبر ٢٠٠٠'],
            ['FROM SEP 2000', 'ستمبر ٢٠٠٠ سے'],
            ['AFT SEP 2000', 'بعد از ستمبر ٢٠٠٠'],
            ['BEF SEP 2000', 'قبل از ستمبر ٢٠٠٠'],
            ['15 OCT 2000', '١٥ اکتوبر ٢٠٠٠'],
            ['OCT 2000', 'اکتوبر ٢٠٠٠'],
            ['ABT OCT 2000', 'تقریباً اکتوبر ٢٠٠٠'],
            ['FROM OCT 2000', 'اکتوبر ٢٠٠٠ سے'],
            ['AFT OCT 2000', 'بعد از اکتوبر ٢٠٠٠'],
            ['BEF OCT 2000', 'قبل از اکتوبر ٢٠٠٠'],
            ['15 NOV 2000', '١٥ نومبر ٢٠٠٠'],
            ['NOV 2000', 'نومبر ٢٠٠٠'],
            ['ABT NOV 2000', 'تقریباً نومبر ٢٠٠٠'],
            ['FROM NOV 2000', 'نومبر ٢٠٠٠ سے'],
            ['AFT NOV 2000', 'بعد از نومبر ٢٠٠٠'],
            ['BEF NOV 2000', 'قبل از نومبر ٢٠٠٠'],
            ['15 DEC 2000', '١٥ دسمبر ٢٠٠٠'],
            ['DEC 2000', 'دسمبر ٢٠٠٠'],
            ['ABT DEC 2000', 'تقریباً دسمبر ٢٠٠٠'],
            ['FROM DEC 2000', 'دسمبر ٢٠٠٠ سے'],
            ['AFT DEC 2000', 'بعد از دسمبر ٢٠٠٠'],
            ['BEF DEC 2000', 'قبل از دسمبر ٢٠٠٠'],
            ['2000', '٢٠٠٠'],
            ['ABT 15 JAN 2000', 'تقریباً ١٥ جنوری ٢٠٠٠'],
            ['CAL 15 JAN 2000', 'بالحساب ١٥ جنوری ٢٠٠٠'],
            ['EST 15 JAN 2000', 'اندازاً ١٥ جنوری ٢٠٠٠'],
            ['BEF 15 JAN 2000', 'قبل از ١٥ جنوری ٢٠٠٠'],
            ['AFT 15 JAN 2000', 'بعد از ١٥ جنوری ٢٠٠٠'],
            ['FROM 15 JAN 2000', '١٥ جنوری ٢٠٠٠ سے'],
            ['TO 15 JAN 2000', '١٥ جنوری ٢٠٠٠ تک'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '١٥ جنوری ٢٠٠٠ اور ١٥ فروری ٢٠٠٠ کے درمیان'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '١٥ جنوری ٢٠٠٠ سے ١٥ فروری ٢٠٠٠ تک'],
            ['INT 15 JAN 2000', 'تشریحی ١٥ جنوری ٢٠٠٠'],
            ['@#DJULIAN@ 15 JAN 1700', '١٥ جنوری ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ JAN 1700', 'جنوری ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ JAN 1700', 'تقریباً جنوری ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ JAN 1700', 'جنوری ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ JAN 1700', 'بعد از جنوری ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ JAN 1700', 'قبل از جنوری ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 FEB 1700', '١٥ فروری ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ FEB 1700', 'فروری ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ FEB 1700', 'تقریباً فروری ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ FEB 1700', 'فروری ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ FEB 1700', 'بعد از فروری ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ FEB 1700', 'قبل از فروری ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 MAR 1700', '١٥ مارچ ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ MAR 1700', 'مارچ ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ MAR 1700', 'تقریباً مارچ ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ MAR 1700', 'مارچ ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ MAR 1700', 'بعد از مارچ ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ MAR 1700', 'قبل از مارچ ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 APR 1700', '١٥ اپریل ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 14 APR 1645/46', '١٤ اپریل ١٦٤٥/٤٦ عیسوی'],
            ['@#DJULIAN@ APR 1700', 'اپریل ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ APR 1700', 'تقریباً اپریل ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ APR 1700', 'اپریل ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ APR 1700', 'بعد از اپریل ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ APR 1700', 'قبل از اپریل ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 MAY 1700', '١٥ مئی ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ MAY 1700', 'مئی ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ MAY 1700', 'تقریباً مئی ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ MAY 1700', 'مئی ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ MAY 1700', 'بعد از مئی ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ MAY 1700', 'قبل از مئی ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 JUN 1700', '١٥ جون ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ JUN 1700', 'جون ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ JUN 1700', 'تقریباً جون ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ JUN 1700', 'جون ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ JUN 1700', 'بعد از جون ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ JUN 1700', 'قبل از جون ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 JUL 1700', '١٥ جولائی ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ JUL 1700', 'جولائی ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ JUL 1700', 'تقریباً جولائی ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ JUL 1700', 'جولائی ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ JUL 1700', 'بعد از جولائی ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ JUL 1700', 'قبل از جولائی ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 AUG 1700', '١٥ اگست ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ AUG 1700', 'اگست ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ AUG 1700', 'تقریباً اگست ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ AUG 1700', 'اگست ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ AUG 1700', 'بعد از اگست ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ AUG 1700', 'قبل از اگست ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 SEP 1700', '١٥ ستمبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ SEP 1700', 'ستمبر ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ SEP 1700', 'تقریباً ستمبر ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ SEP 1700', 'ستمبر ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ SEP 1700', 'بعد از ستمبر ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ SEP 1700', 'قبل از ستمبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 OCT 1700', '١٥ اکتوبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ OCT 1700', 'اکتوبر ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ OCT 1700', 'تقریباً اکتوبر ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ OCT 1700', 'اکتوبر ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ OCT 1700', 'بعد از اکتوبر ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ OCT 1700', 'قبل از اکتوبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 NOV 1700', '١٥ نومبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ NOV 1700', 'نومبر ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ NOV 1700', 'تقریباً نومبر ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ NOV 1700', 'نومبر ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ NOV 1700', 'بعد از نومبر ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ NOV 1700', 'قبل از نومبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 15 DEC 1700', '١٥ دسمبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ DEC 1700', 'دسمبر ١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ DEC 1700', 'تقریباً دسمبر ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ DEC 1700', 'دسمبر ١٧٠٠ عیسوی سے'],
            ['AFT @#DJULIAN@ DEC 1700', 'بعد از دسمبر ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ DEC 1700', 'قبل از دسمبر ١٧٠٠ عیسوی'],
            ['@#DJULIAN@ 1700', '١٧٠٠ عیسوی'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'تقریباً ١٥ جنوری ١٧٠٠ عیسوی'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'بالحساب ١٥ جنوری ١٧٠٠ عیسوی'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'اندازاً ١٥ جنوری ١٧٠٠ عیسوی'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'قبل از ١٥ جنوری ١٧٠٠ عیسوی'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'بعد از ١٥ جنوری ١٧٠٠ عیسوی'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '١٥ جنوری ١٧٠٠ عیسوی سے'],
            ['TO @#DJULIAN@ 15 JAN 1700', '١٥ جنوری ١٧٠٠ عیسوی تک'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '١٥ جنوری ١٧٠٠ عیسوی اور ١٥ فروری ١٧٠٠ عیسوی کے درمیان'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '١٥ جنوری ١٧٠٠ عیسوی سے ١٥ فروری ١٧٠٠ عیسوی تک'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'تشریحی ١٥ جنوری ١٧٠٠ عیسوی'],
            ['@#DHEBREW@ 15 TSH 5765', '١٥ تشیری ٥٧٦٥'],
            ['@#DHEBREW@ TSH 5765', 'تشیری ٥٧٦٥'],
            ['ABT @#DHEBREW@ TSH 5765', 'تقریباً تشیری ٥٧٦٥'],
            ['FROM @#DHEBREW@ TSH 5765', 'تشیری ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ TSH 5765', 'بعد از تشیری ٥٧٦٥'],
            ['BEF @#DHEBREW@ TSH 5765', 'قبل از تشیری ٥٧٦٥'],
            ['@#DHEBREW@ 15 CSH 5765', '١٥ ہشیوان ٥٧٦٥'],
            ['@#DHEBREW@ CSH 5765', 'ہشیوان ٥٧٦٥'],
            ['ABT @#DHEBREW@ CSH 5765', 'تقریباً ہشیوان ٥٧٦٥'],
            ['FROM @#DHEBREW@ CSH 5765', 'ہشیوان ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ CSH 5765', 'بعد از ہشیوان ٥٧٦٥'],
            ['BEF @#DHEBREW@ CSH 5765', 'قبل از ہشیوان ٥٧٦٥'],
            ['@#DHEBREW@ 15 KSL 5765', '١٥ کِیسلو ٥٧٦٥'],
            ['@#DHEBREW@ KSL 5765', 'کِیسلو ٥٧٦٥'],
            ['ABT @#DHEBREW@ KSL 5765', 'تقریباً کِیسلو ٥٧٦٥'],
            ['FROM @#DHEBREW@ KSL 5765', 'کِیسلو ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ KSL 5765', 'بعد از کِیسلو ٥٧٦٥'],
            ['BEF @#DHEBREW@ KSL 5765', 'قبل از کِیسلو ٥٧٦٥'],
            ['@#DHEBREW@ 15 TVT 5765', '١٥ تیوت ٥٧٦٥'],
            ['@#DHEBREW@ TVT 5765', 'تیوت ٥٧٦٥'],
            ['ABT @#DHEBREW@ TVT 5765', 'تقریباً تیوت ٥٧٦٥'],
            ['FROM @#DHEBREW@ TVT 5765', 'تیوت ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ TVT 5765', 'بعد از تیوت ٥٧٦٥'],
            ['BEF @#DHEBREW@ TVT 5765', 'قبل از تیوت ٥٧٦٥'],
            ['@#DHEBREW@ 15 SHV 5765', '١٥ شوات ٥٧٦٥'],
            ['@#DHEBREW@ SHV 5765', 'شوات ٥٧٦٥'],
            ['ABT @#DHEBREW@ SHV 5765', 'تقریباً شوات ٥٧٦٥'],
            ['FROM @#DHEBREW@ SHV 5765', 'شوات ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ SHV 5765', 'بعد از شوات ٥٧٦٥'],
            ['BEF @#DHEBREW@ SHV 5765', 'قبل از شوات ٥٧٦٥'],
            ['@#DHEBREW@ 15 ADR 5765', '١٥ ادار اول ٥٧٦٥'],
            ['@#DHEBREW@ ADR 5765', 'ادار اول ٥٧٦٥'],
            ['ABT @#DHEBREW@ ADR 5765', 'تقریباً ادار اول ٥٧٦٥'],
            ['FROM @#DHEBREW@ ADR 5765', 'ادار اول ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ ADR 5765', 'بعد از ادار اول ٥٧٦٥'],
            ['BEF @#DHEBREW@ ADR 5765', 'قبل از ادار اول ٥٧٦٥'],
            ['@#DHEBREW@ 15 ADS 5765', '١٥ ادار دوم ٥٧٦٥'],
            ['@#DHEBREW@ ADS 5765', 'ادار دوم ٥٧٦٥'],
            ['ABT @#DHEBREW@ ADS 5765', 'تقریباً ادار دوم ٥٧٦٥'],
            ['FROM @#DHEBREW@ ADS 5765', 'ادار دوم ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ ADS 5765', 'بعد از ادار دوم ٥٧٦٥'],
            ['BEF @#DHEBREW@ ADS 5765', 'قبل از ادار دوم ٥٧٦٥'],
            ['@#DHEBREW@ 15 NSN 5765', '١٥ نسان ٥٧٦٥'],
            ['@#DHEBREW@ NSN 5765', 'نسان ٥٧٦٥'],
            ['ABT @#DHEBREW@ NSN 5765', 'تقریباً نسان ٥٧٦٥'],
            ['FROM @#DHEBREW@ NSN 5765', 'نسان ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ NSN 5765', 'بعد از نسان ٥٧٦٥'],
            ['BEF @#DHEBREW@ NSN 5765', 'قبل از نسان ٥٧٦٥'],
            ['@#DHEBREW@ 15 IYR 5765', '١٥ ایار ٥٧٦٥'],
            ['@#DHEBREW@ IYR 5765', 'ایار ٥٧٦٥'],
            ['ABT @#DHEBREW@ IYR 5765', 'تقریباً ایار ٥٧٦٥'],
            ['FROM @#DHEBREW@ IYR 5765', 'ایار ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ IYR 5765', 'بعد از ایار ٥٧٦٥'],
            ['BEF @#DHEBREW@ IYR 5765', 'قبل از ایار ٥٧٦٥'],
            ['@#DHEBREW@ 15 SVN 5765', '١٥ سیوان ٥٧٦٥'],
            ['@#DHEBREW@ SVN 5765', 'سیوان ٥٧٦٥'],
            ['ABT @#DHEBREW@ SVN 5765', 'تقریباً سیوان ٥٧٦٥'],
            ['FROM @#DHEBREW@ SVN 5765', 'سیوان ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ SVN 5765', 'بعد از سیوان ٥٧٦٥'],
            ['BEF @#DHEBREW@ SVN 5765', 'قبل از سیوان ٥٧٦٥'],
            ['@#DHEBREW@ 15 TMZ 5765', '١٥ تموز ٥٧٦٥'],
            ['@#DHEBREW@ TMZ 5765', 'تموز ٥٧٦٥'],
            ['ABT @#DHEBREW@ TMZ 5765', 'تقریباً تموز ٥٧٦٥'],
            ['FROM @#DHEBREW@ TMZ 5765', 'تموز ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ TMZ 5765', 'بعد از تموز ٥٧٦٥'],
            ['BEF @#DHEBREW@ TMZ 5765', 'قبل از تموز ٥٧٦٥'],
            ['@#DHEBREW@ 15 AAV 5765', '١٥ آو ٥٧٦٥'],
            ['@#DHEBREW@ AAV 5765', 'آو ٥٧٦٥'],
            ['ABT @#DHEBREW@ AAV 5765', 'تقریباً آو ٥٧٦٥'],
            ['FROM @#DHEBREW@ AAV 5765', 'آو ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ AAV 5765', 'بعد از آو ٥٧٦٥'],
            ['BEF @#DHEBREW@ AAV 5765', 'قبل از آو ٥٧٦٥'],
            ['@#DHEBREW@ 15 ELL 5765', '١٥ ایلول ٥٧٦٥'],
            ['@#DHEBREW@ ELL 5765', 'ایلول ٥٧٦٥'],
            ['ABT @#DHEBREW@ ELL 5765', 'تقریباً ایلول ٥٧٦٥'],
            ['FROM @#DHEBREW@ ELL 5765', 'ایلول ٥٧٦٥ سے'],
            ['AFT @#DHEBREW@ ELL 5765', 'بعد از ایلول ٥٧٦٥'],
            ['BEF @#DHEBREW@ ELL 5765', 'قبل از ایلول ٥٧٦٥'],
            ['@#DHEBREW@ 5765', '٥٧٦٥'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'تقریباً ١٥ تشیری ٥٧٦٥'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'بالحساب ١٥ تشیری ٥٧٦٥'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'اندازاً ١٥ تشیری ٥٧٦٥'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'قبل از ١٥ تشیری ٥٧٦٥'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'بعد از ١٥ تشیری ٥٧٦٥'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '١٥ تشیری ٥٧٦٥ سے'],
            ['TO @#DHEBREW@ 15 TSH 5765', '١٥ تشیری ٥٧٦٥ تک'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '١٥ تشیری ٥٧٦٥ اور ١٥ ہشیوان ٥٧٦٥ کے درمیان'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '١٥ تشیری ٥٧٦٥ سے ١٥ ہشیوان ٥٧٦٥ تک'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'تشریحی ١٥ تشیری ٥٧٦٥'],
            ['@#DFRENCH R@ 15 VEND 12', '١٥ وینڈیمیئر An XII'],
            ['@#DFRENCH R@ VEND 12', 'وینڈیمیئر An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'تقریباً وینڈیمیئر An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'وینڈیمیئر An XII سے'],
            ['AFT @#DFRENCH R@ VEND 12', 'بعد از وینڈیمیئر An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'قبل از وینڈیمیئر An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '١٥ بروومیر An XII'],
            ['@#DFRENCH R@ BRUM 12', 'بروومیر An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'تقریباً بروومیر An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'بروومیر An XII سے'],
            ['AFT @#DFRENCH R@ BRUM 12', 'بعد از بروومیر An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'قبل از بروومیر An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '١٥ فریمیر An XII'],
            ['@#DFRENCH R@ FRIM 12', 'فریمیر An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'تقریباً فریمیر An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'فریمیر An XII سے'],
            ['AFT @#DFRENCH R@ FRIM 12', 'بعد از فریمیر An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'قبل از فریمیر An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '١٥ نیووز An XII'],
            ['@#DFRENCH R@ NIVO 12', 'نیووز An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'تقریباً نیووز An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'نیووز An XII سے'],
            ['AFT @#DFRENCH R@ NIVO 12', 'بعد از نیووز An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'قبل از نیووز An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '١٥ پلویوز An XII'],
            ['@#DFRENCH R@ PLUV 12', 'پلویوز An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'تقریباً پلویوز An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'پلویوز An XII سے'],
            ['AFT @#DFRENCH R@ PLUV 12', 'بعد از پلویوز An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'قبل از پلویوز An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '١٥ وینٹوز An XII'],
            ['@#DFRENCH R@ VENT 12', 'وینٹوز An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'تقریباً وینٹوز An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'وینٹوز An XII سے'],
            ['AFT @#DFRENCH R@ VENT 12', 'بعد از وینٹوز An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'قبل از وینٹوز An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '١٥ جرمنل An XII'],
            ['@#DFRENCH R@ GERM 12', 'جرمنل An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'تقریباً جرمنل An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'جرمنل An XII سے'],
            ['AFT @#DFRENCH R@ GERM 12', 'بعد از جرمنل An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'قبل از جرمنل An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '١٥ فلوریل An XII'],
            ['@#DFRENCH R@ FLOR 12', 'فلوریل An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'تقریباً فلوریل An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'فلوریل An XII سے'],
            ['AFT @#DFRENCH R@ FLOR 12', 'بعد از فلوریل An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'قبل از فلوریل An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '١٥ پریریئل An XII'],
            ['@#DFRENCH R@ PRAI 12', 'پریریئل An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'تقریباً پریریئل An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'پریریئل An XII سے'],
            ['AFT @#DFRENCH R@ PRAI 12', 'بعد از پریریئل An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'قبل از پریریئل An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '١٥ میسڈر An XII'],
            ['@#DFRENCH R@ MESS 12', 'میسڈر An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'تقریباً میسڈر An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'میسڈر An XII سے'],
            ['AFT @#DFRENCH R@ MESS 12', 'بعد از میسڈر An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'قبل از میسڈر An XII'],
            ['@#DFRENCH R@ 15 THER 12', '١٥ تھرمائڈور An XII'],
            ['@#DFRENCH R@ THER 12', 'تھرمائڈور An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'تقریباً تھرمائڈور An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'تھرمائڈور An XII سے'],
            ['AFT @#DFRENCH R@ THER 12', 'بعد از تھرمائڈور An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'قبل از تھرمائڈور An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '١٥ فروٹڈور An XII'],
            ['@#DFRENCH R@ FRUC 12', 'فروٹڈور An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'تقریباً فروٹڈور An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'فروٹڈور An XII سے'],
            ['AFT @#DFRENCH R@ FRUC 12', 'بعد از فروٹڈور An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'قبل از فروٹڈور An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '١٥ جورس کمپلیمینٹریس An XII'],
            ['@#DFRENCH R@ COMP 12', 'جورس کمپلیمینٹریس An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'تقریباً جورس کمپلیمینٹریس An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'جورس کمپلیمینٹریس An XII سے'],
            ['AFT @#DFRENCH R@ COMP 12', 'بعد از جورس کمپلیمینٹریس An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'قبل از جورس کمپلیمینٹریس An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'تقریباً ١٥ وینڈیمیئر An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'بالحساب ١٥ وینڈیمیئر An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'اندازاً ١٥ وینڈیمیئر An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'قبل از ١٥ وینڈیمیئر An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'بعد از ١٥ وینڈیمیئر An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '١٥ وینڈیمیئر An XII سے'],
            ['TO @#DFRENCH R@ 15 VEND 12', '١٥ وینڈیمیئر An XII تک'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '١٥ وینڈیمیئر An XII اور ١٥ بروومیر An XII کے درمیان'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '١٥ وینڈیمیئر An XII سے ١٥ بروومیر An XII تک'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'تشریحی ١٥ وینڈیمیئر An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '١٥ محرم ١٤٢٥'],
            ['@#DHIJRI@ MUHAR 1425', 'محرم ١٤٢٥'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'تقریباً محرم ١٤٢٥'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'محرم ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'بعد از محرم ١٤٢٥'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'قبل از محرم ١٤٢٥'],
            ['@#DHIJRI@ 15 SAFAR 1425', '١٥ صفر ١٤٢٥'],
            ['@#DHIJRI@ SAFAR 1425', 'صفر ١٤٢٥'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'تقریباً صفر ١٤٢٥'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'صفر ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'بعد از صفر ١٤٢٥'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'قبل از صفر ١٤٢٥'],
            ['@#DHIJRI@ 15 RABIA 1425', '١٥ ربیع الاول ١٤٢٥'],
            ['@#DHIJRI@ RABIA 1425', 'ربیع الاول ١٤٢٥'],
            ['ABT @#DHIJRI@ RABIA 1425', 'تقریباً ربیع الاول ١٤٢٥'],
            ['FROM @#DHIJRI@ RABIA 1425', 'ربیع الاول ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ RABIA 1425', 'بعد از ربیع الاول ١٤٢٥'],
            ['BEF @#DHIJRI@ RABIA 1425', 'قبل از ربیع الاول ١٤٢٥'],
            ['@#DHIJRI@ 15 RABIT 1425', '١٥ ربیع الثانی ١٤٢٥'],
            ['@#DHIJRI@ RABIT 1425', 'ربیع الثانی ١٤٢٥'],
            ['ABT @#DHIJRI@ RABIT 1425', 'تقریباً ربیع الثانی ١٤٢٥'],
            ['FROM @#DHIJRI@ RABIT 1425', 'ربیع الثانی ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ RABIT 1425', 'بعد از ربیع الثانی ١٤٢٥'],
            ['BEF @#DHIJRI@ RABIT 1425', 'قبل از ربیع الثانی ١٤٢٥'],
            ['@#DHIJRI@ 15 JUMAA 1425', '١٥ جمادی الاول ١٤٢٥'],
            ['@#DHIJRI@ JUMAA 1425', 'جمادی الاول ١٤٢٥'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'تقریباً جمادی الاول ١٤٢٥'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'جمادی الاول ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'بعد از جمادی الاول ١٤٢٥'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'قبل از جمادی الاول ١٤٢٥'],
            ['@#DHIJRI@ 15 JUMAT 1425', '١٥ جمادی الثانی ١٤٢٥'],
            ['@#DHIJRI@ JUMAT 1425', 'جمادی الثانی ١٤٢٥'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'تقریباً جمادی الثانی ١٤٢٥'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'جمادی الثانی ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'بعد از جمادی الثانی ١٤٢٥'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'قبل از جمادی الثانی ١٤٢٥'],
            ['@#DHIJRI@ 15 RAJAB 1425', '١٥ رجب ١٤٢٥'],
            ['@#DHIJRI@ RAJAB 1425', 'رجب ١٤٢٥'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'تقریباً رجب ١٤٢٥'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'رجب ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'بعد از رجب ١٤٢٥'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'قبل از رجب ١٤٢٥'],
            ['@#DHIJRI@ 15 SHAAB 1425', '١٥ شعبان ١٤٢٥'],
            ['@#DHIJRI@ SHAAB 1425', 'شعبان ١٤٢٥'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'تقریباً شعبان ١٤٢٥'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'شعبان ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'بعد از شعبان ١٤٢٥'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'قبل از شعبان ١٤٢٥'],
            ['@#DHIJRI@ 15 RAMAD 1425', '١٥ رمضان ١٤٢٥'],
            ['@#DHIJRI@ RAMAD 1425', 'رمضان ١٤٢٥'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'تقریباً رمضان ١٤٢٥'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'رمضان ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'بعد از رمضان ١٤٢٥'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'قبل از رمضان ١٤٢٥'],
            ['@#DHIJRI@ 15 SHAWW 1425', '١٥ شوال ١٤٢٥'],
            ['@#DHIJRI@ SHAWW 1425', 'شوال ١٤٢٥'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'تقریباً شوال ١٤٢٥'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'شوال ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'بعد از شوال ١٤٢٥'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'قبل از شوال ١٤٢٥'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '١٥ ذوالقعدہ ١٤٢٥'],
            ['@#DHIJRI@ DHUAQ 1425', 'ذوالقعدہ ١٤٢٥'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'تقریباً ذوالقعدہ ١٤٢٥'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'ذوالقعدہ ١٤٢٥ سے'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'بعد از ذوالقعدہ ١٤٢٥'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'قبل از ذوالقعدہ ١٤٢٥'],
            ['@#DHIJRI@ 15 DHUAL 1425', '١٤٢٥'],
            ['@#DHIJRI@ DHUAL 1425', '١٤٢٥'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'تقریباً ١٤٢٥'],
            ['FROM @#DHIJRI@ DHUAL 1425', '١٤٢٥ سے'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'بعد از ١٤٢٥'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'قبل از ١٤٢٥'],
            ['@#DHIJRI@ 1425', '١٤٢٥'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'تقریباً ١٥ محرم ١٤٢٥'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'بالحساب ١٥ محرم ١٤٢٥'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'اندازاً ١٥ محرم ١٤٢٥'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'قبل از ١٥ محرم ١٤٢٥'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'بعد از ١٥ محرم ١٤٢٥'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '١٥ محرم ١٤٢٥ سے'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '١٥ محرم ١٤٢٥ تک'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '١٥ محرم ١٤٢٥ اور ١٥ صفر ١٤٢٥ کے درمیان'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '١٥ محرم ١٤٢٥ سے ١٥ صفر ١٤٢٥ تک'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'تشریحی ١٥ محرم ١٤٢٥'],
            ['@#DJALALI@ 15 FARVA 1384', '١٥ فروردین ١٣٨٤'],
            ['@#DJALALI@ FARVA 1384', 'فروردین ١٣٨٤'],
            ['ABT @#DJALALI@ FARVA 1384', 'تقریباً فروردین ١٣٨٤'],
            ['FROM @#DJALALI@ FARVA 1384', 'فروردین ١٣٨٤ سے'],
            ['AFT @#DJALALI@ FARVA 1384', 'بعد از فروردین ١٣٨٤'],
            ['BEF @#DJALALI@ FARVA 1384', 'قبل از فروردین ١٣٨٤'],
            ['@#DJALALI@ 15 ORDIB 1384', '١٥ اردی بہشت ١٣٨٤'],
            ['@#DJALALI@ ORDIB 1384', 'اردی بہشت ١٣٨٤'],
            ['ABT @#DJALALI@ ORDIB 1384', 'تقریباً اردی بہشت ١٣٨٤'],
            ['FROM @#DJALALI@ ORDIB 1384', 'اردی بہشت ١٣٨٤ سے'],
            ['AFT @#DJALALI@ ORDIB 1384', 'بعد از اردی بہشت ١٣٨٤'],
            ['BEF @#DJALALI@ ORDIB 1384', 'قبل از اردی بہشت ١٣٨٤'],
            ['@#DJALALI@ 15 KHORD 1384', '١٥ خرداد ١٣٨٤'],
            ['@#DJALALI@ KHORD 1384', 'خرداد ١٣٨٤'],
            ['ABT @#DJALALI@ KHORD 1384', 'تقریباً خرداد ١٣٨٤'],
            ['FROM @#DJALALI@ KHORD 1384', 'خرداد ١٣٨٤ سے'],
            ['AFT @#DJALALI@ KHORD 1384', 'بعد از خرداد ١٣٨٤'],
            ['BEF @#DJALALI@ KHORD 1384', 'قبل از خرداد ١٣٨٤'],
            ['@#DJALALI@ 15 TIR 1384', '١٥ تیر ١٣٨٤'],
            ['@#DJALALI@ TIR 1384', 'تیر ١٣٨٤'],
            ['ABT @#DJALALI@ TIR 1384', 'تقریباً تیر ١٣٨٤'],
            ['FROM @#DJALALI@ TIR 1384', 'تیر ١٣٨٤ سے'],
            ['AFT @#DJALALI@ TIR 1384', 'بعد از تیر ١٣٨٤'],
            ['BEF @#DJALALI@ TIR 1384', 'قبل از تیر ١٣٨٤'],
            ['@#DJALALI@ 15 MORDA 1384', '١٥ مرداد ١٣٨٤'],
            ['@#DJALALI@ MORDA 1384', 'مرداد ١٣٨٤'],
            ['ABT @#DJALALI@ MORDA 1384', 'تقریباً مرداد ١٣٨٤'],
            ['FROM @#DJALALI@ MORDA 1384', 'مرداد ١٣٨٤ سے'],
            ['AFT @#DJALALI@ MORDA 1384', 'بعد از مرداد ١٣٨٤'],
            ['BEF @#DJALALI@ MORDA 1384', 'قبل از مرداد ١٣٨٤'],
            ['@#DJALALI@ 15 SHAHR 1384', '١٥ شہریور ١٣٨٤'],
            ['@#DJALALI@ SHAHR 1384', 'شہریور ١٣٨٤'],
            ['ABT @#DJALALI@ SHAHR 1384', 'تقریباً شہریور ١٣٨٤'],
            ['FROM @#DJALALI@ SHAHR 1384', 'شہریور ١٣٨٤ سے'],
            ['AFT @#DJALALI@ SHAHR 1384', 'بعد از شہریور ١٣٨٤'],
            ['BEF @#DJALALI@ SHAHR 1384', 'قبل از شہریور ١٣٨٤'],
            ['@#DJALALI@ 15 MEHR 1384', '١٥ مہر ١٣٨٤'],
            ['@#DJALALI@ MEHR 1384', 'مہر ١٣٨٤'],
            ['ABT @#DJALALI@ MEHR 1384', 'تقریباً مہر ١٣٨٤'],
            ['FROM @#DJALALI@ MEHR 1384', 'مہر ١٣٨٤ سے'],
            ['AFT @#DJALALI@ MEHR 1384', 'بعد از مہر ١٣٨٤'],
            ['BEF @#DJALALI@ MEHR 1384', 'قبل از مہر ١٣٨٤'],
            ['@#DJALALI@ 15 ABAN 1384', '١٥ آبان ١٣٨٤'],
            ['@#DJALALI@ ABAN 1384', 'آبان ١٣٨٤'],
            ['ABT @#DJALALI@ ABAN 1384', 'تقریباً آبان ١٣٨٤'],
            ['FROM @#DJALALI@ ABAN 1384', 'آبان ١٣٨٤ سے'],
            ['AFT @#DJALALI@ ABAN 1384', 'بعد از آبان ١٣٨٤'],
            ['BEF @#DJALALI@ ABAN 1384', 'قبل از آبان ١٣٨٤'],
            ['@#DJALALI@ 15 AZAR 1384', '١٥ آذر ١٣٨٤'],
            ['@#DJALALI@ AZAR 1384', 'آذر ١٣٨٤'],
            ['ABT @#DJALALI@ AZAR 1384', 'تقریباً آذر ١٣٨٤'],
            ['FROM @#DJALALI@ AZAR 1384', 'آذر ١٣٨٤ سے'],
            ['AFT @#DJALALI@ AZAR 1384', 'بعد از آذر ١٣٨٤'],
            ['BEF @#DJALALI@ AZAR 1384', 'قبل از آذر ١٣٨٤'],
            ['@#DJALALI@ 15 DEY 1384', '١٥ دے ١٣٨٤'],
            ['@#DJALALI@ DEY 1384', 'دے ١٣٨٤'],
            ['ABT @#DJALALI@ DEY 1384', 'تقریباً دے ١٣٨٤'],
            ['FROM @#DJALALI@ DEY 1384', 'دے ١٣٨٤ سے'],
            ['AFT @#DJALALI@ DEY 1384', 'بعد از دے ١٣٨٤'],
            ['BEF @#DJALALI@ DEY 1384', 'قبل از دے ١٣٨٤'],
            ['@#DJALALI@ 15 BAHMA 1384', '١٥ بہمن ١٣٨٤'],
            ['@#DJALALI@ BAHMA 1384', 'بہمن ١٣٨٤'],
            ['ABT @#DJALALI@ BAHMA 1384', 'تقریباً بہمن ١٣٨٤'],
            ['FROM @#DJALALI@ BAHMA 1384', 'بہمن ١٣٨٤ سے'],
            ['AFT @#DJALALI@ BAHMA 1384', 'بعد از بہمن ١٣٨٤'],
            ['BEF @#DJALALI@ BAHMA 1384', 'قبل از بہمن ١٣٨٤'],
            ['@#DJALALI@ 15 ESFAN 1384', '١٥ اسفند ١٣٨٤'],
            ['@#DJALALI@ ESFAN 1384', 'اسفند ١٣٨٤'],
            ['ABT @#DJALALI@ ESFAN 1384', 'تقریباً اسفند ١٣٨٤'],
            ['FROM @#DJALALI@ ESFAN 1384', 'اسفند ١٣٨٤ سے'],
            ['AFT @#DJALALI@ ESFAN 1384', 'بعد از اسفند ١٣٨٤'],
            ['BEF @#DJALALI@ ESFAN 1384', 'قبل از اسفند ١٣٨٤'],
            ['@#DJALALI@ 1384', '١٣٨٤'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'تقریباً ١٥ فروردین ١٣٨٤'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'بالحساب ١٥ فروردین ١٣٨٤'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'اندازاً ١٥ فروردین ١٣٨٤'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'قبل از ١٥ فروردین ١٣٨٤'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'بعد از ١٥ فروردین ١٣٨٤'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '١٥ فروردین ١٣٨٤ سے'],
            ['TO @#DJALALI@ 15 FARVA 1384', '١٥ فروردین ١٣٨٤ تک'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '١٥ فروردین ١٣٨٤ اور ١٥ اردی بہشت ١٣٨٤ کے درمیان'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '١٥ فروردین ١٣٨٤ سے ١٥ اردی بہشت ١٣٨٤ تک'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'تشریحی ١٥ فروردین ١٣٨٤'],
        ];
    }
}
