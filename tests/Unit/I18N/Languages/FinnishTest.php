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
use Fisharebest\Webtrees\I18N\Languages\Finnish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Finnish::class)]
class FinnishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Finnish();
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
        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Å', 'Ä', 'Ö'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('fi', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('suomi', self::language()->endonym());
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
        self::assertSame('−123 456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('−123 456,0789 %', self::language()->percentage(-1234.560789));
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
        self::assertSame('one ja two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two ja three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one tai two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two tai three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('vaimo', 'aviomies', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-aviomies', 'ex-vaimo', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('morsian', 'sulhanen', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('äiti', 'poika', [$son, $fm, $wife]);
        self::assertRelationshipNames('isä', 'poika', [$son, $fm, $husband]);
        self::assertRelationshipNames('äiti', 'tytär', [$daughter, $fm, $wife]);

        // Siblings (son born 2000 is older than daughter born 2001)
        self::assertRelationshipNames('pikkusisko', 'isoveli', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('velipuoli', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('isäpuoli', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('tytärpuoli', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('anoppi', 'vävy', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('appi', 'vävy', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('miniä', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('isoäiti', 'pojanpoika', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('isoisä', 'pojanpoika', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) — iso prefix
        self::assertRelationshipName('isoisoisä', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('isoisoäiti', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('täti', 'veljenpoika', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('setä', 'veljenpoika', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('veljentytär', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('veljenpoika', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('serkku', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('serkku', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) — iso prefix
        self::assertRelationshipName('isotäti', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('isosetä', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15. tammikuuta 2000'],
            ['JAN 2000', 'tammikuu 2000'],
            ['ABT JAN 2000', 'noin tammikuuta 2000'],
            ['FROM JAN 2000', 'tammikuuta 2000 alkaen'],
            ['AFT JAN 2000', 'tammikuussa 2000 jälkeen'],
            ['BEF JAN 2000', 'ennen tammikuun 2000'],
            ['15 FEB 2000', '15. helmikuuta 2000'],
            ['FEB 2000', 'helmikuu 2000'],
            ['ABT FEB 2000', 'noin helmikuuta 2000'],
            ['FROM FEB 2000', 'helmikuuta 2000 alkaen'],
            ['AFT FEB 2000', 'helmikuussa 2000 jälkeen'],
            ['BEF FEB 2000', 'ennen helmikuun 2000'],
            ['15 MAR 2000', '15. maaliskuuta 2000'],
            ['MAR 2000', 'maaliskuu 2000'],
            ['ABT MAR 2000', 'noin maaliskuuta 2000'],
            ['FROM MAR 2000', 'maaliskuuta 2000 alkaen'],
            ['AFT MAR 2000', 'maaliskuussa 2000 jälkeen'],
            ['BEF MAR 2000', 'ennen maaliskuun 2000'],
            ['15 APR 2000', '15. huhtikuuta 2000'],
            ['APR 2000', 'huhtikuu 2000'],
            ['ABT APR 2000', 'noin huhtikuuta 2000'],
            ['FROM APR 2000', 'huhtikuuta 2000 alkaen'],
            ['AFT APR 2000', 'huhtikuussa 2000 jälkeen'],
            ['BEF APR 2000', 'ennen huhtikuun 2000'],
            ['15 MAY 2000', '15. toukokuuta 2000'],
            ['MAY 2000', 'toukokuu 2000'],
            ['ABT MAY 2000', 'noin toukokuuta 2000'],
            ['FROM MAY 2000', 'toukokuuta 2000 alkaen'],
            ['AFT MAY 2000', 'toukokuussa 2000 jälkeen'],
            ['BEF MAY 2000', 'ennen toukokuun 2000'],
            ['15 JUN 2000', '15. kesäkuuta 2000'],
            ['JUN 2000', 'kesäkuu 2000'],
            ['ABT JUN 2000', 'noin kesäkuuta 2000'],
            ['FROM JUN 2000', 'kesäkuuta 2000 alkaen'],
            ['AFT JUN 2000', 'kesäkuussa 2000 jälkeen'],
            ['BEF JUN 2000', 'ennen kesäkuun 2000'],
            ['15 JUL 2000', '15. heinäkuuta 2000'],
            ['JUL 2000', 'heinäkuu 2000'],
            ['ABT JUL 2000', 'noin heinäkuuta 2000'],
            ['FROM JUL 2000', 'heinäkuuta 2000 alkaen'],
            ['AFT JUL 2000', 'heinäkuussa 2000 jälkeen'],
            ['BEF JUL 2000', 'ennen heinäkuun 2000'],
            ['15 AUG 2000', '15. elokuuta 2000'],
            ['AUG 2000', 'elokuu 2000'],
            ['ABT AUG 2000', 'noin elokuuta 2000'],
            ['FROM AUG 2000', 'elokuuta 2000 alkaen'],
            ['AFT AUG 2000', 'elokuussa 2000 jälkeen'],
            ['BEF AUG 2000', 'ennen elokuun 2000'],
            ['15 SEP 2000', '15. syyskuuta 2000'],
            ['SEP 2000', 'syyskuu 2000'],
            ['ABT SEP 2000', 'noin syyskuuta 2000'],
            ['FROM SEP 2000', 'syyskuuta 2000 alkaen'],
            ['AFT SEP 2000', 'syyskuussa 2000 jälkeen'],
            ['BEF SEP 2000', 'ennen syyskuun 2000'],
            ['15 OCT 2000', '15. lokakuuta 2000'],
            ['OCT 2000', 'lokakuu 2000'],
            ['ABT OCT 2000', 'noin lokakuuta 2000'],
            ['FROM OCT 2000', 'lokakuuta 2000 alkaen'],
            ['AFT OCT 2000', 'lokakuussa 2000 jälkeen'],
            ['BEF OCT 2000', 'ennen lokakuun 2000'],
            ['15 NOV 2000', '15. marraskuuta 2000'],
            ['NOV 2000', 'marraskuu 2000'],
            ['ABT NOV 2000', 'noin marraskuuta 2000'],
            ['FROM NOV 2000', 'marraskuuta 2000 alkaen'],
            ['AFT NOV 2000', 'marraskuussa 2000 jälkeen'],
            ['BEF NOV 2000', 'ennen marraskuun 2000'],
            ['15 DEC 2000', '15. joulukuuta 2000'],
            ['DEC 2000', 'joulukuu 2000'],
            ['ABT DEC 2000', 'noin joulukuuta 2000'],
            ['FROM DEC 2000', 'joulukuuta 2000 alkaen'],
            ['AFT DEC 2000', 'joulukuussa 2000 jälkeen'],
            ['BEF DEC 2000', 'ennen joulukuun 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'noin 15. tammikuuta 2000'],
            ['CAL 15 JAN 2000', 'todennäköisesti 15. tammikuuta 2000'],
            ['EST 15 JAN 2000', 'arviolta 15. tammikuuta 2000'],
            ['BEF 15 JAN 2000', 'ennen 15. tammikuuta 2000'],
            ['AFT 15 JAN 2000', '15. tammikuuta 2000 jälkeen'],
            ['FROM 15 JAN 2000', '15. tammikuuta 2000 alkaen'],
            ['TO 15 JAN 2000', '15. tammikuuta 2000 asti'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '15. tammikuuta 2000 - 15. helmikuuta 2000 välillä'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '15. tammikuuta 2000 - 15. helmikuuta 2000 asti'],
            ['INT 15 JAN 2000', 'tulkittu 15. tammikuuta 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. tammikuuta 1700 JAA'],
            ['@#DJULIAN@ JAN 1700', 'tammikuu 1700 JAA'],
            ['ABT @#DJULIAN@ JAN 1700', 'noin tammikuuta 1700 JAA'],
            ['FROM @#DJULIAN@ JAN 1700', 'tammikuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ JAN 1700', 'tammikuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ JAN 1700', 'ennen tammikuun 1700 JAA'],
            ['@#DJULIAN@ 15 FEB 1700', '15. helmikuuta 1700 JAA'],
            ['@#DJULIAN@ FEB 1700', 'helmikuu 1700 JAA'],
            ['ABT @#DJULIAN@ FEB 1700', 'noin helmikuuta 1700 JAA'],
            ['FROM @#DJULIAN@ FEB 1700', 'helmikuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ FEB 1700', 'helmikuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ FEB 1700', 'ennen helmikuun 1700 JAA'],
            ['@#DJULIAN@ 15 MAR 1700', '15. maaliskuuta 1700 JAA'],
            ['@#DJULIAN@ MAR 1700', 'maaliskuu 1700 JAA'],
            ['ABT @#DJULIAN@ MAR 1700', 'noin maaliskuuta 1700 JAA'],
            ['FROM @#DJULIAN@ MAR 1700', 'maaliskuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ MAR 1700', 'maaliskuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ MAR 1700', 'ennen maaliskuun 1700 JAA'],
            ['@#DJULIAN@ 15 APR 1700', '15. huhtikuuta 1700 JAA'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. huhtikuuta 1645/46 JAA'],
            ['@#DJULIAN@ APR 1700', 'huhtikuu 1700 JAA'],
            ['ABT @#DJULIAN@ APR 1700', 'noin huhtikuuta 1700 JAA'],
            ['FROM @#DJULIAN@ APR 1700', 'huhtikuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ APR 1700', 'huhtikuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ APR 1700', 'ennen huhtikuun 1700 JAA'],
            ['@#DJULIAN@ 15 MAY 1700', '15. toukokuuta 1700 JAA'],
            ['@#DJULIAN@ MAY 1700', 'toukokuu 1700 JAA'],
            ['ABT @#DJULIAN@ MAY 1700', 'noin toukokuuta 1700 JAA'],
            ['FROM @#DJULIAN@ MAY 1700', 'toukokuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ MAY 1700', 'toukokuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ MAY 1700', 'ennen toukokuun 1700 JAA'],
            ['@#DJULIAN@ 15 JUN 1700', '15. kesäkuuta 1700 JAA'],
            ['@#DJULIAN@ JUN 1700', 'kesäkuu 1700 JAA'],
            ['ABT @#DJULIAN@ JUN 1700', 'noin kesäkuuta 1700 JAA'],
            ['FROM @#DJULIAN@ JUN 1700', 'kesäkuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ JUN 1700', 'kesäkuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ JUN 1700', 'ennen kesäkuun 1700 JAA'],
            ['@#DJULIAN@ 15 JUL 1700', '15. heinäkuuta 1700 JAA'],
            ['@#DJULIAN@ JUL 1700', 'heinäkuu 1700 JAA'],
            ['ABT @#DJULIAN@ JUL 1700', 'noin heinäkuuta 1700 JAA'],
            ['FROM @#DJULIAN@ JUL 1700', 'heinäkuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ JUL 1700', 'heinäkuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ JUL 1700', 'ennen heinäkuun 1700 JAA'],
            ['@#DJULIAN@ 15 AUG 1700', '15. elokuuta 1700 JAA'],
            ['@#DJULIAN@ AUG 1700', 'elokuu 1700 JAA'],
            ['ABT @#DJULIAN@ AUG 1700', 'noin elokuuta 1700 JAA'],
            ['FROM @#DJULIAN@ AUG 1700', 'elokuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ AUG 1700', 'elokuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ AUG 1700', 'ennen elokuun 1700 JAA'],
            ['@#DJULIAN@ 15 SEP 1700', '15. syyskuuta 1700 JAA'],
            ['@#DJULIAN@ SEP 1700', 'syyskuu 1700 JAA'],
            ['ABT @#DJULIAN@ SEP 1700', 'noin syyskuuta 1700 JAA'],
            ['FROM @#DJULIAN@ SEP 1700', 'syyskuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ SEP 1700', 'syyskuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ SEP 1700', 'ennen syyskuun 1700 JAA'],
            ['@#DJULIAN@ 15 OCT 1700', '15. lokakuuta 1700 JAA'],
            ['@#DJULIAN@ OCT 1700', 'lokakuu 1700 JAA'],
            ['ABT @#DJULIAN@ OCT 1700', 'noin lokakuuta 1700 JAA'],
            ['FROM @#DJULIAN@ OCT 1700', 'lokakuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ OCT 1700', 'lokakuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ OCT 1700', 'ennen lokakuun 1700 JAA'],
            ['@#DJULIAN@ 15 NOV 1700', '15. marraskuuta 1700 JAA'],
            ['@#DJULIAN@ NOV 1700', 'marraskuu 1700 JAA'],
            ['ABT @#DJULIAN@ NOV 1700', 'noin marraskuuta 1700 JAA'],
            ['FROM @#DJULIAN@ NOV 1700', 'marraskuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ NOV 1700', 'marraskuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ NOV 1700', 'ennen marraskuun 1700 JAA'],
            ['@#DJULIAN@ 15 DEC 1700', '15. joulukuuta 1700 JAA'],
            ['@#DJULIAN@ DEC 1700', 'joulukuu 1700 JAA'],
            ['ABT @#DJULIAN@ DEC 1700', 'noin joulukuuta 1700 JAA'],
            ['FROM @#DJULIAN@ DEC 1700', 'joulukuuta 1700 JAA alkaen'],
            ['AFT @#DJULIAN@ DEC 1700', 'joulukuussa 1700 JAA jälkeen'],
            ['BEF @#DJULIAN@ DEC 1700', 'ennen joulukuun 1700 JAA'],
            ['@#DJULIAN@ 1700', '1700 JAA'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'noin 15. tammikuuta 1700 JAA'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'todennäköisesti 15. tammikuuta 1700 JAA'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'arviolta 15. tammikuuta 1700 JAA'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'ennen 15. tammikuuta 1700 JAA'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '15. tammikuuta 1700 JAA jälkeen'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '15. tammikuuta 1700 JAA alkaen'],
            ['TO @#DJULIAN@ 15 JAN 1700', '15. tammikuuta 1700 JAA asti'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '15. tammikuuta 1700 JAA - 15. helmikuuta 1700 JAA välillä'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '15. tammikuuta 1700 JAA - 15. helmikuuta 1700 JAA asti'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'tulkittu 15. tammikuuta 1700 JAA'],
            ['@#DHEBREW@ 15 TSH 5765', '15. tishrei-kuuta 5765'],
            ['@#DHEBREW@ TSH 5765', 'tishrei-kuu 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'noin tishrei-kuuta 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'tishrei-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ TSH 5765', 'tishrei-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ TSH 5765', 'ennen tishrei-kuun 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. heshvan-kuuta 5765'],
            ['@#DHEBREW@ CSH 5765', 'heshvan-kuu 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'noin heshvan-kuuta 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'heshvan-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ CSH 5765', 'heshvan-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ CSH 5765', 'ennen heshvan-kuun 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. kislev-kuuta 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislev-kuu 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'noin kislev-kuuta 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'kislev-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ KSL 5765', 'kislev-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ KSL 5765', 'ennen kislev-kuun 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. tevet-kuuta 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet-kuu 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'noin tevet-kuuta 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'tevet-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ TVT 5765', 'tevet-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ TVT 5765', 'ennen tevet-kuun 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. shvat-kuuta 5765'],
            ['@#DHEBREW@ SHV 5765', 'shvat-kuu 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'noin shvat-kuuta 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'shvat-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ SHV 5765', 'shvat-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ SHV 5765', 'ennen shvat-kuun 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. adar I-kuuta 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar I-kuu 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'noin adar I-kuuta 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'adar I-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ ADR 5765', 'adar I-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ ADR 5765', 'ennen adar I-kuun 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. adar II-kuuta 5765'],
            ['@#DHEBREW@ ADS 5765', 'adar II-kuu 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'noin adar II-kuuta 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'adar II-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ ADS 5765', 'adar II-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ ADS 5765', 'ennen adar II-kuun 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. nisan-kuuta 5765'],
            ['@#DHEBREW@ NSN 5765', 'nisan-kuu 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'noin nisan-kuuta 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'nisan-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ NSN 5765', 'nisan-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ NSN 5765', 'ennen nisan-kuun 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. ijar-kuuta 5765'],
            ['@#DHEBREW@ IYR 5765', 'ijar-kuu 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'noin ijar-kuuta 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'ijar-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ IYR 5765', 'ijar-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ IYR 5765', 'ennen ijar-kuun 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. sivan-kuuta 5765'],
            ['@#DHEBREW@ SVN 5765', 'sivan-kuu 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'noin sivan-kuuta 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'sivan-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ SVN 5765', 'sivan-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ SVN 5765', 'ennen sivan-kuun 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. tamuz-kuuta 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz-kuu 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'noin tamuz-kuuta 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'tamuz-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ TMZ 5765', 'tamuz-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ TMZ 5765', 'ennen tamuz-kuun 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. av-kuuta 5765'],
            ['@#DHEBREW@ AAV 5765', 'av-kuu 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'noin av-kuuta 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'av-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ AAV 5765', 'av-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ AAV 5765', 'ennen av-kuun 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. elul-kuuta 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul-kuu 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'noin elul-kuuta 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'elul-kuuta 5765 alkaen'],
            ['AFT @#DHEBREW@ ELL 5765', 'elul-kuussa 5765 jälkeen'],
            ['BEF @#DHEBREW@ ELL 5765', 'ennen elul-kuun 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'noin 15. tishrei-kuuta 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'todennäköisesti 15. tishrei-kuuta 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'arviolta 15. tishrei-kuuta 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'ennen 15. tishrei-kuuta 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '15. tishrei-kuuta 5765 jälkeen'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '15. tishrei-kuuta 5765 alkaen'],
            ['TO @#DHEBREW@ 15 TSH 5765', '15. tishrei-kuuta 5765 asti'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '15. tishrei-kuuta 5765 - 15. heshvan-kuuta 5765 välillä'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '15. tishrei-kuuta 5765 - 15. heshvan-kuuta 5765 asti'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'tulkittu 15. tishrei-kuuta 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'noin Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'Vendémiaire An XII alkaen'],
            ['AFT @#DFRENCH R@ VEND 12', 'Vendémiaire An XII jälkeen'],
            ['BEF @#DFRENCH R@ VEND 12', 'ennen Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'noin Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'Brumaire An XII alkaen'],
            ['AFT @#DFRENCH R@ BRUM 12', 'Brumaire An XII jälkeen'],
            ['BEF @#DFRENCH R@ BRUM 12', 'ennen Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'noin Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'Frimaire An XII alkaen'],
            ['AFT @#DFRENCH R@ FRIM 12', 'Frimaire An XII jälkeen'],
            ['BEF @#DFRENCH R@ FRIM 12', 'ennen Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'noin Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'Nivôse An XII alkaen'],
            ['AFT @#DFRENCH R@ NIVO 12', 'Nivôse An XII jälkeen'],
            ['BEF @#DFRENCH R@ NIVO 12', 'ennen Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'noin Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'Pluviôse An XII alkaen'],
            ['AFT @#DFRENCH R@ PLUV 12', 'Pluviôse An XII jälkeen'],
            ['BEF @#DFRENCH R@ PLUV 12', 'ennen Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'noin Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'Ventôse An XII alkaen'],
            ['AFT @#DFRENCH R@ VENT 12', 'Ventôse An XII jälkeen'],
            ['BEF @#DFRENCH R@ VENT 12', 'ennen Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'noin Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'Germinal An XII alkaen'],
            ['AFT @#DFRENCH R@ GERM 12', 'Germinal An XII jälkeen'],
            ['BEF @#DFRENCH R@ GERM 12', 'ennen Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'noin Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'Floréal An XII alkaen'],
            ['AFT @#DFRENCH R@ FLOR 12', 'Floréal An XII jälkeen'],
            ['BEF @#DFRENCH R@ FLOR 12', 'ennen Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'noin Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'Prairial An XII alkaen'],
            ['AFT @#DFRENCH R@ PRAI 12', 'Prairial An XII jälkeen'],
            ['BEF @#DFRENCH R@ PRAI 12', 'ennen Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'noin Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'Messidor An XII alkaen'],
            ['AFT @#DFRENCH R@ MESS 12', 'Messidor An XII jälkeen'],
            ['BEF @#DFRENCH R@ MESS 12', 'ennen Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'noin Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'Thermidor An XII alkaen'],
            ['AFT @#DFRENCH R@ THER 12', 'Thermidor An XII jälkeen'],
            ['BEF @#DFRENCH R@ THER 12', 'ennen Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'noin Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'Fructidor An XII alkaen'],
            ['AFT @#DFRENCH R@ FRUC 12', 'Fructidor An XII jälkeen'],
            ['BEF @#DFRENCH R@ FRUC 12', 'ennen Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'noin jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'jours complémentaires An XII alkaen'],
            ['AFT @#DFRENCH R@ COMP 12', 'jours complémentaires An XII jälkeen'],
            ['BEF @#DFRENCH R@ COMP 12', 'ennen jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'noin 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'todennäköisesti 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'arviolta 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'ennen 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII jälkeen'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII alkaen'],
            ['TO @#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII asti'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '15. Vendémiaire An XII - 15. Brumaire An XII välillä'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '15. Vendémiaire An XII - 15. Brumaire An XII asti'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'tulkittu 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'noin muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'muharram 1425 alkaen'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'muharram 1425 jälkeen'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'ennen muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'noin safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'safar 1425 alkaen'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'safar 1425 jälkeen'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'ennen safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. rabi al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'rabi al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'noin rabi al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'rabi al-awwal 1425 alkaen'],
            ['AFT @#DHIJRI@ RABIA 1425', 'rabi al-awwal 1425 jälkeen'],
            ['BEF @#DHIJRI@ RABIA 1425', 'ennen rabi al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. rabi al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'rabi al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'noin rabi al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'rabi al-thani 1425 alkaen'],
            ['AFT @#DHIJRI@ RABIT 1425', 'rabi al-thani 1425 jälkeen'],
            ['BEF @#DHIJRI@ RABIT 1425', 'ennen rabi al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. jumada-al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'jumada-al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'noin jumada-al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'jumada-al-awwal 1425 alkaen'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'jumada-al-awwal 1425 jälkeen'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'ennen jumada-al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. jumada-al-sani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'jumada-al-sani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'noin jumada-al-sani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'jumada-al-sani 1425 alkaen'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'jumada-al-sani 1425 jälkeen'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'ennen jumada-al-sani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'noin rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'rajab 1425 alkaen'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'rajab 1425 jälkeen'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'ennen rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. sha`ban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'sha`ban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'noin sha`ban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'sha`ban 1425 alkaen'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'sha`ban 1425 jälkeen'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'ennen sha`ban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'noin ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'ramadan 1425 alkaen'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'ramadan 1425 jälkeen'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'ennen ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'noin shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'shawwal 1425 alkaen'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'shawwal 1425 jälkeen'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'ennen shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. dhul-qa`da 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'dhul-qa`da 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'noin dhul-qa`da 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'dhul-qa`da 1425 alkaen'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'dhul-qa`da 1425 jälkeen'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'ennen dhul-qa`da 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'noin 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', '1425 alkaen'],
            ['AFT @#DHIJRI@ DHUAL 1425', '1425 jälkeen'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'ennen 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'noin 15. muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'todennäköisesti 15. muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'arviolta 15. muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'ennen 15. muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '15. muharram 1425 jälkeen'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '15. muharram 1425 alkaen'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '15. muharram 1425 asti'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '15. muharram 1425 - 15. safar 1425 välillä'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '15. muharram 1425 - 15. safar 1425 asti'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'tulkittu 15. muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'noin Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'Farvardin 1384 alkaen'],
            ['AFT @#DJALALI@ FARVA 1384', 'Farvardin 1384 jälkeen'],
            ['BEF @#DJALALI@ FARVA 1384', 'ennen Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'noin Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'Ordibehesht 1384 alkaen'],
            ['AFT @#DJALALI@ ORDIB 1384', 'Ordibehesht 1384 jälkeen'],
            ['BEF @#DJALALI@ ORDIB 1384', 'ennen Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'noin Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'Khordad 1384 alkaen'],
            ['AFT @#DJALALI@ KHORD 1384', 'Khordad 1384 jälkeen'],
            ['BEF @#DJALALI@ KHORD 1384', 'ennen Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'noin Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'Tir 1384 alkaen'],
            ['AFT @#DJALALI@ TIR 1384', 'Tir 1384 jälkeen'],
            ['BEF @#DJALALI@ TIR 1384', 'ennen Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'noin Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'Mordad 1384 alkaen'],
            ['AFT @#DJALALI@ MORDA 1384', 'Mordad 1384 jälkeen'],
            ['BEF @#DJALALI@ MORDA 1384', 'ennen Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'noin Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'Shahrivar 1384 alkaen'],
            ['AFT @#DJALALI@ SHAHR 1384', 'Shahrivar 1384 jälkeen'],
            ['BEF @#DJALALI@ SHAHR 1384', 'ennen Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'noin Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'Mehr 1384 alkaen'],
            ['AFT @#DJALALI@ MEHR 1384', 'Mehr 1384 jälkeen'],
            ['BEF @#DJALALI@ MEHR 1384', 'ennen Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'noin Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'Aban 1384 alkaen'],
            ['AFT @#DJALALI@ ABAN 1384', 'Aban 1384 jälkeen'],
            ['BEF @#DJALALI@ ABAN 1384', 'ennen Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'noin Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'Azar 1384 alkaen'],
            ['AFT @#DJALALI@ AZAR 1384', 'Azar 1384 jälkeen'],
            ['BEF @#DJALALI@ AZAR 1384', 'ennen Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'noin Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'Dey 1384 alkaen'],
            ['AFT @#DJALALI@ DEY 1384', 'Dey 1384 jälkeen'],
            ['BEF @#DJALALI@ DEY 1384', 'ennen Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'noin Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'Bahman 1384 alkaen'],
            ['AFT @#DJALALI@ BAHMA 1384', 'Bahman 1384 jälkeen'],
            ['BEF @#DJALALI@ BAHMA 1384', 'ennen Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'noin Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'Esfand 1384 alkaen'],
            ['AFT @#DJALALI@ ESFAN 1384', 'Esfand 1384 jälkeen'],
            ['BEF @#DJALALI@ ESFAN 1384', 'ennen Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'noin 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'todennäköisesti 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'arviolta 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'ennen 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384 jälkeen'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384 alkaen'],
            ['TO @#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384 asti'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '15. Farvardin 1384 - 15. Ordibehesht 1384 välillä'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '15. Farvardin 1384 - 15. Ordibehesht 1384 asti'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'tulkittu 15. Farvardin 1384'],
        ];
    }
}
