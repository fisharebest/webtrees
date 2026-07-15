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
use Fisharebest\Webtrees\I18N\Languages\Icelandic;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Icelandic::class)]
class IcelandicTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Icelandic();
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
        self::assertSame('is', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('slenska', self::language()->endonym());
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




    public function testFormatList(): void
    {
        $language = static::language();

        self::assertSame('', $language->formatList([]));
        self::assertSame('one', $language->formatList(['one']));
        self::assertSame('one, two', $language->formatList(['one', 'two']));
        self::assertSame('one, two, three', $language->formatList(['one', 'two', 'three']));

        self::assertSame('', $language->formatListAnd([]));
        self::assertSame('one', $language->formatListAnd(['one']));
        self::assertSame('one og two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two og three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one eða two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two eða three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@\n1 FAMS @fbro@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
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

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $adoptedSon, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('eiginkona', 'eiginmaður', [$husband, $fm, $wife]);
        self::assertRelationshipNames('fyrrverandi eiginmaður', 'fyrrverandi eiginkona', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('unnusta', 'unnusti', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('móðir', 'sonur', [$son, $fm, $wife]);
        self::assertRelationshipNames('faðir', 'sonur', [$son, $fm, $husband]);
        self::assertRelationshipNames('móðir', 'dóttir', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('fósturmóðir', 'fóstursonur', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('fósturfaðir', 'fóstursonur', [$adoptedSon, $fd, $exHusband]);

        // Siblings (son born 2000 is older than daughter born 2001)
        self::assertRelationshipNames('yngri systir', 'eldri bróðir', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('hálfbróðir', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('stjúpfaðir', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('stjúpdóttir', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('tengdamóðir', 'tengdasonur', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('tengdafaðir', 'tengdasonur', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('tengdadóttir', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('amma', 'sonarsonur', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('afi', 'sonarsonur', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) — lang prefix
        self::assertRelationshipName('langafi', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('langamma', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('föðursystir', 'bróðursonur', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('föðurbróðir', 'bróðursonur', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('bróðurdóttir', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('bróðursonur', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('frænka', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('frændi', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) — lang prefix
        self::assertRelationshipName('langföðursystir', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('langföðurbróðir', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15. janúar 2000'],
            ['JAN 2000', 'janúar 2000'],
            ['ABT JAN 2000', 'um janúar 2000'],
            ['FROM JAN 2000', 'frá janúar 2000'],
            ['AFT JAN 2000', 'eftir janúar 2000'],
            ['BEF JAN 2000', 'fyrir janúar 2000'],
            ['15 FEB 2000', '15. febrúar 2000'],
            ['FEB 2000', 'febrúar 2000'],
            ['ABT FEB 2000', 'um febrúar 2000'],
            ['FROM FEB 2000', 'frá febrúar 2000'],
            ['AFT FEB 2000', 'eftir febrúar 2000'],
            ['BEF FEB 2000', 'fyrir febrúar 2000'],
            ['15 MAR 2000', '15. mars 2000'],
            ['MAR 2000', 'mars 2000'],
            ['ABT MAR 2000', 'um mars 2000'],
            ['FROM MAR 2000', 'frá mars 2000'],
            ['AFT MAR 2000', 'eftir mars 2000'],
            ['BEF MAR 2000', 'fyrir mars 2000'],
            ['15 APR 2000', '15. apríl 2000'],
            ['APR 2000', 'apríl 2000'],
            ['ABT APR 2000', 'um apríl 2000'],
            ['FROM APR 2000', 'frá apríl 2000'],
            ['AFT APR 2000', 'eftir apríl 2000'],
            ['BEF APR 2000', 'fyrir apríl 2000'],
            ['15 MAY 2000', '15. maí 2000'],
            ['MAY 2000', 'maí 2000'],
            ['ABT MAY 2000', 'um maí 2000'],
            ['FROM MAY 2000', 'frá maí 2000'],
            ['AFT MAY 2000', 'eftir maí 2000'],
            ['BEF MAY 2000', 'fyrir maí 2000'],
            ['15 JUN 2000', '15. júní 2000'],
            ['JUN 2000', 'júní 2000'],
            ['ABT JUN 2000', 'um júní 2000'],
            ['FROM JUN 2000', 'frá júní 2000'],
            ['AFT JUN 2000', 'eftir júní 2000'],
            ['BEF JUN 2000', 'fyrir júní 2000'],
            ['15 JUL 2000', '15. júlí 2000'],
            ['JUL 2000', 'júlí 2000'],
            ['ABT JUL 2000', 'um júlí 2000'],
            ['FROM JUL 2000', 'frá júlí 2000'],
            ['AFT JUL 2000', 'eftir júlí 2000'],
            ['BEF JUL 2000', 'fyrir júlí 2000'],
            ['15 AUG 2000', '15. ágúst 2000'],
            ['AUG 2000', 'ágúst 2000'],
            ['ABT AUG 2000', 'um ágúst 2000'],
            ['FROM AUG 2000', 'frá ágúst 2000'],
            ['AFT AUG 2000', 'eftir ágúst 2000'],
            ['BEF AUG 2000', 'fyrir ágúst 2000'],
            ['15 SEP 2000', '15. september 2000'],
            ['SEP 2000', 'september 2000'],
            ['ABT SEP 2000', 'um september 2000'],
            ['FROM SEP 2000', 'frá september 2000'],
            ['AFT SEP 2000', 'eftir september 2000'],
            ['BEF SEP 2000', 'fyrir september 2000'],
            ['15 OCT 2000', '15. október 2000'],
            ['OCT 2000', 'október 2000'],
            ['ABT OCT 2000', 'um október 2000'],
            ['FROM OCT 2000', 'frá október 2000'],
            ['AFT OCT 2000', 'eftir október 2000'],
            ['BEF OCT 2000', 'fyrir október 2000'],
            ['15 NOV 2000', '15. nóvember 2000'],
            ['NOV 2000', 'nóvember 2000'],
            ['ABT NOV 2000', 'um nóvember 2000'],
            ['FROM NOV 2000', 'frá nóvember 2000'],
            ['AFT NOV 2000', 'eftir nóvember 2000'],
            ['BEF NOV 2000', 'fyrir nóvember 2000'],
            ['15 DEC 2000', '15. desember 2000'],
            ['DEC 2000', 'desember 2000'],
            ['ABT DEC 2000', 'um desember 2000'],
            ['FROM DEC 2000', 'frá desember 2000'],
            ['AFT DEC 2000', 'eftir desember 2000'],
            ['BEF DEC 2000', 'fyrir desember 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'um 15. janúar 2000'],
            ['CAL 15 JAN 2000', 'reiknað 15. janúar 2000'],
            ['EST 15 JAN 2000', 'áætlað 15. janúar 2000'],
            ['BEF 15 JAN 2000', 'fyrir 15. janúar 2000'],
            ['AFT 15 JAN 2000', 'eftir 15. janúar 2000'],
            ['FROM 15 JAN 2000', 'frá 15. janúar 2000'],
            ['TO 15 JAN 2000', 'til 15. janúar 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'á milli 15. janúar 2000 og 15. febrúar 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'frá 15. janúar 2000 til 15. febrúar 2000'],
            ['INT 15 JAN 2000', 'túlkað 15. janúar 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. janúar 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ JAN 1700', 'janúar 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ JAN 1700', 'um janúar 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ JAN 1700', 'frá janúar 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ JAN 1700', 'eftir janúar 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ JAN 1700', 'fyrir janúar 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 FEB 1700', '15. febrúar 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ FEB 1700', 'febrúar 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ FEB 1700', 'um febrúar 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ FEB 1700', 'frá febrúar 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ FEB 1700', 'eftir febrúar 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ FEB 1700', 'fyrir febrúar 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 MAR 1700', '15. mars 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ MAR 1700', 'mars 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ MAR 1700', 'um mars 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ MAR 1700', 'frá mars 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ MAR 1700', 'eftir mars 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ MAR 1700', 'fyrir mars 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 APR 1700', '15. apríl 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. apríl 1645/46 eftir okkar tímatali'],
            ['@#DJULIAN@ APR 1700', 'apríl 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ APR 1700', 'um apríl 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ APR 1700', 'frá apríl 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ APR 1700', 'eftir apríl 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ APR 1700', 'fyrir apríl 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 MAY 1700', '15. maí 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ MAY 1700', 'maí 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ MAY 1700', 'um maí 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ MAY 1700', 'frá maí 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ MAY 1700', 'eftir maí 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ MAY 1700', 'fyrir maí 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 JUN 1700', '15. júní 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ JUN 1700', 'júní 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ JUN 1700', 'um júní 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ JUN 1700', 'frá júní 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ JUN 1700', 'eftir júní 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ JUN 1700', 'fyrir júní 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 JUL 1700', '15. júlí 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ JUL 1700', 'júlí 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ JUL 1700', 'um júlí 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ JUL 1700', 'frá júlí 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ JUL 1700', 'eftir júlí 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ JUL 1700', 'fyrir júlí 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 AUG 1700', '15. ágúst 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ AUG 1700', 'ágúst 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ AUG 1700', 'um ágúst 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ AUG 1700', 'frá ágúst 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ AUG 1700', 'eftir ágúst 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ AUG 1700', 'fyrir ágúst 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 SEP 1700', '15. september 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ SEP 1700', 'september 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ SEP 1700', 'um september 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ SEP 1700', 'frá september 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ SEP 1700', 'eftir september 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ SEP 1700', 'fyrir september 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 OCT 1700', '15. október 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ OCT 1700', 'október 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ OCT 1700', 'um október 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ OCT 1700', 'frá október 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ OCT 1700', 'eftir október 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ OCT 1700', 'fyrir október 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 NOV 1700', '15. nóvember 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ NOV 1700', 'nóvember 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ NOV 1700', 'um nóvember 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ NOV 1700', 'frá nóvember 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ NOV 1700', 'eftir nóvember 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ NOV 1700', 'fyrir nóvember 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 15 DEC 1700', '15. desember 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ DEC 1700', 'desember 1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ DEC 1700', 'um desember 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ DEC 1700', 'frá desember 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ DEC 1700', 'eftir desember 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ DEC 1700', 'fyrir desember 1700 eftir okkar tímatali'],
            ['@#DJULIAN@ 1700', '1700 eftir okkar tímatali'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'um 15. janúar 1700 eftir okkar tímatali'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'reiknað 15. janúar 1700 eftir okkar tímatali'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'áætlað 15. janúar 1700 eftir okkar tímatali'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'fyrir 15. janúar 1700 eftir okkar tímatali'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'eftir 15. janúar 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'frá 15. janúar 1700 eftir okkar tímatali'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'til 15. janúar 1700 eftir okkar tímatali'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'á milli 15. janúar 1700 eftir okkar tímatali og 15. febrúar 1700 eftir okkar tímatali'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'frá 15. janúar 1700 eftir okkar tímatali til 15. febrúar 1700 eftir okkar tímatali'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'túlkað 15. janúar 1700 eftir okkar tímatali'],
            ['@#DHEBREW@ 15 TSH 5765', '15. tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'um tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'frá tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'eftir tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'fyrir tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'um heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'frá heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'eftir heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'fyrir heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'um kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'frá kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'eftir kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'fyrir kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'um tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'frá tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'eftir tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'fyrir tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'um shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'frá shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'eftir shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'fyrir shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'um Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'frá Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'eftir Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'fyrir Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'um Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'frá Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'eftir Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'fyrir Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'um nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'frá nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'eftir nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'fyrir nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'um Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'frá Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'eftir Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'fyrir Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'um sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'frá sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'eftir sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'fyrir sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'um tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'frá tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'eftir tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'fyrir tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'um Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'frá Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'eftir Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'fyrir Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'um elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'frá elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'eftir elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'fyrir elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'um 15. tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'reiknað 15. tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'áætlað 15. tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'fyrir 15. tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'eftir 15. tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'frá 15. tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'til 15. tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'á milli 15. tishrei 5765 og 15. heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'frá 15. tishrei 5765 til 15. heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'túlkað 15. tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'um Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'frá Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'eftir Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'fyrir Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'um Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'frá Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'eftir Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'fyrir Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'um Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'frá Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'eftir Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'fyrir Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'um Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'frá Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'eftir Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'fyrir Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'um Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'frá Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'eftir Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'fyrir Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'um Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'frá Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'eftir Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'fyrir Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'um Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'frá Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'eftir Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'fyrir Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'um Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'frá Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'eftir Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'fyrir Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'um Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'frá Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'eftir Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'fyrir Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'um Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'frá Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'eftir Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'fyrir Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'um Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'frá Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'eftir Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'fyrir Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'um Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'frá Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'eftir Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'fyrir Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'um jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'frá jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'eftir jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'fyrir jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'um 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'reiknað 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'áætlað 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'fyrir 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'eftir 15. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'frá 15. Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'til 15. Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'á milli 15. Vendémiaire An XII og 15. Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'frá 15. Vendémiaire An XII til 15. Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'túlkað 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'um Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'frá Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'eftir Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'fyrir Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'um Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'frá Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'eftir Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'fyrir Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'um Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'frá Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'eftir Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'fyrir Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'um Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'frá Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'eftir Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'fyrir Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'um Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'frá Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'eftir Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'fyrir Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'um Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'frá Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'eftir Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'fyrir Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'um Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'frá Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'eftir Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'fyrir Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'um Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'frá Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'eftir Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'fyrir Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'um Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'frá Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'eftir Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'fyrir Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'um Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'frá Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'eftir Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'fyrir Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'um Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'frá Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'eftir Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'fyrir Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'um 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'frá 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'eftir 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'fyrir 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'um 15. Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'reiknað 15. Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'áætlað 15. Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'fyrir 15. Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'eftir 15. Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'frá 15. Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'til 15. Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'á milli 15. Muharram 1425 og 15. Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'frá 15. Muharram 1425 til 15. Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'túlkað 15. Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'um Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'frá Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'eftir Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'fyrir Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'um Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'frá Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'eftir Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'fyrir Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'um Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'frá Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'eftir Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'fyrir Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'um Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'frá Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'eftir Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'fyrir Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'um Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'frá Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'eftir Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'fyrir Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'um Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'frá Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'eftir Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'fyrir Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'um Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'frá Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'eftir Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'fyrir Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'um Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'frá Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'eftir Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'fyrir Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'um Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'frá Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'eftir Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'fyrir Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'um Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'frá Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'eftir Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'fyrir Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'um Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'frá Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'eftir Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'fyrir Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'um Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'frá Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'eftir Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'fyrir Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'um 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'reiknað 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'áætlað 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'fyrir 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'eftir 15. Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'frá 15. Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'til 15. Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'á milli 15. Farvardin 1384 og 15. Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'frá 15. Farvardin 1384 til 15. Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'túlkað 15. Farvardin 1384'],
        ];
    }
}
