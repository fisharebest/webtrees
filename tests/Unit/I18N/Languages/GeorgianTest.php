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
use Fisharebest\Webtrees\I18N\Languages\Georgian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Georgian::class)]
class GeorgianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Georgian();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Geor, self::language()->script());
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
        self::assertSame('ka', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('ქართული', self::language()->endonym());
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
        self::assertSame('-123 456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-123 456,0789%', self::language()->percentage(-1234.560789));
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
        self::assertSame('one და two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two და three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one ან two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two ან three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $child = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@\n1 BIRT\n2 DATE 1968");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $cousinFemale = self::female('cf', "1 FAMC @fbro@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('ცოლი', 'ქმარი', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ყოფილი მეუღლე', 'ყოფილი მეუღლე', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('ნიშანდებული', 'ნიშანდებული', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('დედა', 'ვაჟიშვილი', [$son, $fm, $wife]);
        self::assertRelationshipNames('მამა', 'ვაჟიშვილი', [$son, $fm, $husband]);
        self::assertRelationshipNames('დედა', 'ქალიშვილი', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('და', 'ძმა', [$son, $fm, $daughter]);
        self::assertRelationshipNames('ძმა', 'და', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('ნახევარ და', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('მამინაცვალი', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('გერი ქალიშვილი', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('სიდედრი', 'სიძე', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('სიმამრი', 'სიძე', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('რძალი', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('ბებია', 'შვილიშვილი', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('ბაბუა', 'შვილიშვილი', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipNames('ბებია', 'შვილიშვილი', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('ბაბუა', 'შვილიშვილი', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('დიდი ბაბუა', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('დიდი ბებია', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles — paternal
        self::assertRelationshipNames('დეიდა', 'ძმისწული', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('ბიძა', 'ძმისწული', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('ძმისწული', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('ძმისწული', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('ბიძაშვილი', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('ბიძაშვილი', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 იანვარი 2000'],
            ['JAN 2000', 'იანვარი 2000'],
            ['ABT JAN 2000', 'მიახლოებით იანვარი 2000'],
            ['FROM JAN 2000', 'დან იანვარი 2000'],
            ['AFT JAN 2000', 'იანვარი 2000 შემდეგ'],
            ['BEF JAN 2000', 'перед იანვარი 2000'],
            ['15 FEB 2000', '15 თებერვალი 2000'],
            ['FEB 2000', 'თებერვალი 2000'],
            ['ABT FEB 2000', 'მიახლოებით თებერვალი 2000'],
            ['FROM FEB 2000', 'დან თებერვალი 2000'],
            ['AFT FEB 2000', 'თებერვალი 2000 შემდეგ'],
            ['BEF FEB 2000', 'перед თებერვალი 2000'],
            ['15 MAR 2000', '15 მარტი 2000'],
            ['MAR 2000', 'მარტი 2000'],
            ['ABT MAR 2000', 'მიახლოებით მარტი 2000'],
            ['FROM MAR 2000', 'დან მარტი 2000'],
            ['AFT MAR 2000', 'მარტი 2000 შემდეგ'],
            ['BEF MAR 2000', 'перед მარტი 2000'],
            ['15 APR 2000', '15 აპრილი 2000'],
            ['APR 2000', 'აპრილი 2000'],
            ['ABT APR 2000', 'მიახლოებით აპრილი 2000'],
            ['FROM APR 2000', 'დან აპრილი 2000'],
            ['AFT APR 2000', 'აპრილი 2000 შემდეგ'],
            ['BEF APR 2000', 'перед აპრილი 2000'],
            ['15 MAY 2000', '15 მაი 2000'],
            ['MAY 2000', 'მაი 2000'],
            ['ABT MAY 2000', 'მიახლოებით მაი 2000'],
            ['FROM MAY 2000', 'დან მაი 2000'],
            ['AFT MAY 2000', 'მაი 2000 შემდეგ'],
            ['BEF MAY 2000', 'перед მაი 2000'],
            ['15 JUN 2000', '15 ივნისი 2000'],
            ['JUN 2000', 'ივნისი 2000'],
            ['ABT JUN 2000', 'მიახლოებით ივნისი 2000'],
            ['FROM JUN 2000', 'დან ივნისი 2000'],
            ['AFT JUN 2000', 'ივნისი 2000 შემდეგ'],
            ['BEF JUN 2000', 'перед ივნისი 2000'],
            ['15 JUL 2000', '15 ივლისი 2000'],
            ['JUL 2000', 'ივლისი 2000'],
            ['ABT JUL 2000', 'მიახლოებით ივლისი 2000'],
            ['FROM JUL 2000', 'დან ივლისი 2000'],
            ['AFT JUL 2000', 'ივლისი 2000 შემდეგ'],
            ['BEF JUL 2000', 'перед ივლისი 2000'],
            ['15 AUG 2000', '15 აგვისტო 2000'],
            ['AUG 2000', 'აგვისტო 2000'],
            ['ABT AUG 2000', 'მიახლოებით აგვისტო 2000'],
            ['FROM AUG 2000', 'დან აგვისტო 2000'],
            ['AFT AUG 2000', 'აგვისტო 2000 შემდეგ'],
            ['BEF AUG 2000', 'перед აგვისტო 2000'],
            ['15 SEP 2000', '15 სექტემბერი 2000'],
            ['SEP 2000', 'სექტემბერი 2000'],
            ['ABT SEP 2000', 'მიახლოებით სექტემბერი 2000'],
            ['FROM SEP 2000', 'დან სექტემბერი 2000'],
            ['AFT SEP 2000', 'სექტემბერი 2000 შემდეგ'],
            ['BEF SEP 2000', 'перед სექტემბერი 2000'],
            ['15 OCT 2000', '15 ოქტომბერი 2000'],
            ['OCT 2000', 'ოქტომბერი 2000'],
            ['ABT OCT 2000', 'მიახლოებით ოქტომბერი 2000'],
            ['FROM OCT 2000', 'დან ოქტომბერი 2000'],
            ['AFT OCT 2000', 'ოქტომბერი 2000 შემდეგ'],
            ['BEF OCT 2000', 'перед ოქტომბერი 2000'],
            ['15 NOV 2000', '15 ნოემბერი 2000'],
            ['NOV 2000', 'ნოემბერი 2000'],
            ['ABT NOV 2000', 'მიახლოებით ნოემბერი 2000'],
            ['FROM NOV 2000', 'დან ნოემბერი 2000'],
            ['AFT NOV 2000', 'ნოემბერი 2000 შემდეგ'],
            ['BEF NOV 2000', 'перед ნოემბერი 2000'],
            ['15 DEC 2000', '15 დეკემბერი 2000'],
            ['DEC 2000', 'დეკემბერი 2000'],
            ['ABT DEC 2000', 'მიახლოებით დეკემბერი 2000'],
            ['FROM DEC 2000', 'დან დეკემბერი 2000'],
            ['AFT DEC 2000', 'დეკემბერი 2000 შემდეგ'],
            ['BEF DEC 2000', 'перед დეკემბერი 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'მიახლოებით 15 იანვარი 2000'],
            ['CAL 15 JAN 2000', 'გამოთვლილია 15 იანვარი 2000'],
            ['EST 15 JAN 2000', 'სავარაუდოდ 15 იანვარი 2000'],
            ['BEF 15 JAN 2000', 'перед 15 იანვარი 2000'],
            ['AFT 15 JAN 2000', '15 იანვარი 2000 შემდეგ'],
            ['FROM 15 JAN 2000', 'დან 15 იანვარი 2000'],
            ['TO 15 JAN 2000', '15 იანვარი 2000 მდე'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '15 იანვარი 2000 და 15 თებერვალი 2000 შორის'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'დან 15 იანვარი 2000 ადრე 15 თებერვალი 2000'],
            ['INT 15 JAN 2000', 'ამოცნობილია როგორც 15 იანვარი 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 იანვარი 1700 н. э.'],
            ['@#DJULIAN@ JAN 1700', 'იანვარი 1700 н. э.'],
            ['ABT @#DJULIAN@ JAN 1700', 'მიახლოებით იანვარი 1700 н. э.'],
            ['FROM @#DJULIAN@ JAN 1700', 'დან იანვარი 1700 н. э.'],
            ['AFT @#DJULIAN@ JAN 1700', 'იანვარი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ JAN 1700', 'перед იანვარი 1700 н. э.'],
            ['@#DJULIAN@ 15 FEB 1700', '15 თებერვალი 1700 н. э.'],
            ['@#DJULIAN@ FEB 1700', 'თებერვალი 1700 н. э.'],
            ['ABT @#DJULIAN@ FEB 1700', 'მიახლოებით თებერვალი 1700 н. э.'],
            ['FROM @#DJULIAN@ FEB 1700', 'დან თებერვალი 1700 н. э.'],
            ['AFT @#DJULIAN@ FEB 1700', 'თებერვალი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ FEB 1700', 'перед თებერვალი 1700 н. э.'],
            ['@#DJULIAN@ 15 MAR 1700', '15 მარტი 1700 н. э.'],
            ['@#DJULIAN@ MAR 1700', 'მარტი 1700 н. э.'],
            ['ABT @#DJULIAN@ MAR 1700', 'მიახლოებით მარტი 1700 н. э.'],
            ['FROM @#DJULIAN@ MAR 1700', 'დან მარტი 1700 н. э.'],
            ['AFT @#DJULIAN@ MAR 1700', 'მარტი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ MAR 1700', 'перед მარტი 1700 н. э.'],
            ['@#DJULIAN@ 15 APR 1700', '15 აპრილი 1700 н. э.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 აპრილი 1645/46 н. э.'],
            ['@#DJULIAN@ APR 1700', 'აპრილი 1700 н. э.'],
            ['ABT @#DJULIAN@ APR 1700', 'მიახლოებით აპრილი 1700 н. э.'],
            ['FROM @#DJULIAN@ APR 1700', 'დან აპრილი 1700 н. э.'],
            ['AFT @#DJULIAN@ APR 1700', 'აპრილი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ APR 1700', 'перед აპრილი 1700 н. э.'],
            ['@#DJULIAN@ 15 MAY 1700', '15 მაი 1700 н. э.'],
            ['@#DJULIAN@ MAY 1700', 'მაი 1700 н. э.'],
            ['ABT @#DJULIAN@ MAY 1700', 'მიახლოებით მაი 1700 н. э.'],
            ['FROM @#DJULIAN@ MAY 1700', 'დან მაი 1700 н. э.'],
            ['AFT @#DJULIAN@ MAY 1700', 'მაი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ MAY 1700', 'перед მაი 1700 н. э.'],
            ['@#DJULIAN@ 15 JUN 1700', '15 ივნისი 1700 н. э.'],
            ['@#DJULIAN@ JUN 1700', 'ივნისი 1700 н. э.'],
            ['ABT @#DJULIAN@ JUN 1700', 'მიახლოებით ივნისი 1700 н. э.'],
            ['FROM @#DJULIAN@ JUN 1700', 'დან ივნისი 1700 н. э.'],
            ['AFT @#DJULIAN@ JUN 1700', 'ივნისი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ JUN 1700', 'перед ივნისი 1700 н. э.'],
            ['@#DJULIAN@ 15 JUL 1700', '15 ივლისი 1700 н. э.'],
            ['@#DJULIAN@ JUL 1700', 'ივლისი 1700 н. э.'],
            ['ABT @#DJULIAN@ JUL 1700', 'მიახლოებით ივლისი 1700 н. э.'],
            ['FROM @#DJULIAN@ JUL 1700', 'დან ივლისი 1700 н. э.'],
            ['AFT @#DJULIAN@ JUL 1700', 'ივლისი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ JUL 1700', 'перед ივლისი 1700 н. э.'],
            ['@#DJULIAN@ 15 AUG 1700', '15 აგვისტო 1700 н. э.'],
            ['@#DJULIAN@ AUG 1700', 'აგვისტო 1700 н. э.'],
            ['ABT @#DJULIAN@ AUG 1700', 'მიახლოებით აგვისტო 1700 н. э.'],
            ['FROM @#DJULIAN@ AUG 1700', 'დან აგვისტო 1700 н. э.'],
            ['AFT @#DJULIAN@ AUG 1700', 'აგვისტო 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ AUG 1700', 'перед აგვისტო 1700 н. э.'],
            ['@#DJULIAN@ 15 SEP 1700', '15 სექტემბერი 1700 н. э.'],
            ['@#DJULIAN@ SEP 1700', 'სექტემბერი 1700 н. э.'],
            ['ABT @#DJULIAN@ SEP 1700', 'მიახლოებით სექტემბერი 1700 н. э.'],
            ['FROM @#DJULIAN@ SEP 1700', 'დან სექტემბერი 1700 н. э.'],
            ['AFT @#DJULIAN@ SEP 1700', 'სექტემბერი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ SEP 1700', 'перед სექტემბერი 1700 н. э.'],
            ['@#DJULIAN@ 15 OCT 1700', '15 ოქტომბერი 1700 н. э.'],
            ['@#DJULIAN@ OCT 1700', 'ოქტომბერი 1700 н. э.'],
            ['ABT @#DJULIAN@ OCT 1700', 'მიახლოებით ოქტომბერი 1700 н. э.'],
            ['FROM @#DJULIAN@ OCT 1700', 'დან ოქტომბერი 1700 н. э.'],
            ['AFT @#DJULIAN@ OCT 1700', 'ოქტომბერი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ OCT 1700', 'перед ოქტომბერი 1700 н. э.'],
            ['@#DJULIAN@ 15 NOV 1700', '15 ნოემბერი 1700 н. э.'],
            ['@#DJULIAN@ NOV 1700', 'ნოემბერი 1700 н. э.'],
            ['ABT @#DJULIAN@ NOV 1700', 'მიახლოებით ნოემბერი 1700 н. э.'],
            ['FROM @#DJULIAN@ NOV 1700', 'დან ნოემბერი 1700 н. э.'],
            ['AFT @#DJULIAN@ NOV 1700', 'ნოემბერი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ NOV 1700', 'перед ნოემბერი 1700 н. э.'],
            ['@#DJULIAN@ 15 DEC 1700', '15 დეკემბერი 1700 н. э.'],
            ['@#DJULIAN@ DEC 1700', 'დეკემბერი 1700 н. э.'],
            ['ABT @#DJULIAN@ DEC 1700', 'მიახლოებით დეკემბერი 1700 н. э.'],
            ['FROM @#DJULIAN@ DEC 1700', 'დან დეკემბერი 1700 н. э.'],
            ['AFT @#DJULIAN@ DEC 1700', 'დეკემბერი 1700 н. э. შემდეგ'],
            ['BEF @#DJULIAN@ DEC 1700', 'перед დეკემბერი 1700 н. э.'],
            ['@#DJULIAN@ 1700', '1700 н. э.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'მიახლოებით 15 იანვარი 1700 н. э.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'გამოთვლილია 15 იანვარი 1700 н. э.'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'სავარაუდოდ 15 იანვარი 1700 н. э.'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'перед 15 იანვარი 1700 н. э.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '15 იანვარი 1700 н. э. შემდეგ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'დან 15 იანვარი 1700 н. э.'],
            ['TO @#DJULIAN@ 15 JAN 1700', '15 იანვარი 1700 н. э. მდე'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '15 იანვარი 1700 н. э. და 15 თებერვალი 1700 н. э. შორის'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'დან 15 იანვარი 1700 н. э. ადრე 15 თებერვალი 1700 н. э.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'ამოცნობილია როგორც 15 იანვარი 1700 н. э.'],
            ['@#DHEBREW@ 15 TSH 5765', '15 თიშრეი 5765'],
            ['@#DHEBREW@ TSH 5765', 'თიშრეი 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'მიახლოებით თიშრეი 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'დან თიშრეი 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'თიშრეი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ TSH 5765', 'перед თიშრეი 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 ხეშვანი 5765'],
            ['@#DHEBREW@ CSH 5765', 'ხეშვანი 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'მიახლოებით ხეშვანი 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'დან ხეშვანი 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'ხეშვანი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ CSH 5765', 'перед ხეშვანი 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 ქისლევი 5765'],
            ['@#DHEBREW@ KSL 5765', 'ქისლევი 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'მიახლოებით ქისლევი 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'დან ქისლევი 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'ქისლევი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ KSL 5765', 'перед ქისლევი 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 ტევეთი 5765'],
            ['@#DHEBREW@ TVT 5765', 'ტევეთი 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'მიახლოებით ტევეთი 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'დან ტევეთი 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'ტევეთი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ TVT 5765', 'перед ტევეთი 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 შვატი 5765'],
            ['@#DHEBREW@ SHV 5765', 'შვატი 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'მიახლოებით შვატი 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'დან შვატი 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'შვატი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ SHV 5765', 'перед შვატი 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 ადარ I 5765'],
            ['@#DHEBREW@ ADR 5765', 'ადარ I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'მიახლოებით ადარ I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'დან ადარ I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'ადარ I 5765 შემდეგ'],
            ['BEF @#DHEBREW@ ADR 5765', 'перед ადარ I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 ადარ II 5765'],
            ['@#DHEBREW@ ADS 5765', 'ადარ II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'მიახლოებით ადარ II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'დან ადარ II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'ადარ II 5765 შემდეგ'],
            ['BEF @#DHEBREW@ ADS 5765', 'перед ადარ II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 ნისანი 5765'],
            ['@#DHEBREW@ NSN 5765', 'ნისანი 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'მიახლოებით ნისანი 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'დან ნისანი 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'ნისანი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ NSN 5765', 'перед ნისანი 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 იარი 5765'],
            ['@#DHEBREW@ IYR 5765', 'იარი 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'მიახლოებით იარი 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'დან იარი 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'იარი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ IYR 5765', 'перед იარი 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 სივანი 5765'],
            ['@#DHEBREW@ SVN 5765', 'სივანი 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'მიახლოებით სივანი 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'დან სივანი 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'სივანი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ SVN 5765', 'перед სივანი 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 თამუზი 5765'],
            ['@#DHEBREW@ TMZ 5765', 'თამუზი 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'მიახლოებით თამუზი 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'დან თამუზი 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'თამუზი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ TMZ 5765', 'перед თამუზი 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 ავი 5765'],
            ['@#DHEBREW@ AAV 5765', 'ავი 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'მიახლოებით ავი 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'დან ავი 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'ავი 5765 შემდეგ'],
            ['BEF @#DHEBREW@ AAV 5765', 'перед ავი 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 ელული 5765'],
            ['@#DHEBREW@ ELL 5765', 'ელული 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'მიახლოებით ელული 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'დან ელული 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'ელული 5765 შემდეგ'],
            ['BEF @#DHEBREW@ ELL 5765', 'перед ელული 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'მიახლოებით 15 თიშრეი 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'გამოთვლილია 15 თიშრეი 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'სავარაუდოდ 15 თიშრეი 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'перед 15 თიშრეი 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '15 თიშრეი 5765 შემდეგ'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'დან 15 თიშრეი 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', '15 თიშრეი 5765 მდე'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '15 თიშრეი 5765 და 15 ხეშვანი 5765 შორის'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'დან 15 თიშრეი 5765 ადრე 15 ხეშვანი 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'ამოცნობილია როგორც 15 თიშრეი 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 ვანდემიერი An XII'],
            ['@#DFRENCH R@ VEND 12', 'ვანდემიერი An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'მიახლოებით ვანდემიერი An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'დან ვანდემიერი An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'ვანდემიერი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ VEND 12', 'перед ვანდემიერი An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 ბრიუმერი An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ბრიუმერი An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'მიახლოებით ბრიუმერი An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'დან ბრიუმერი An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'ბრიუმერი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ BRUM 12', 'перед ბრიუმერი An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 ფრიმერი An XII'],
            ['@#DFRENCH R@ FRIM 12', 'ფრიმერი An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'მიახლოებით ფრიმერი An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'დან ფრიმერი An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'ფრიმერი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ FRIM 12', 'перед ფრიმერი An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 ნივოზი An XII'],
            ['@#DFRENCH R@ NIVO 12', 'ნივოზი An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'მიახლოებით ნივოზი An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'დან ნივოზი An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'ნივოზი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ NIVO 12', 'перед ნივოზი An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 პლიუვიოზი An XII'],
            ['@#DFRENCH R@ PLUV 12', 'პლიუვიოზი An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'მიახლოებით პლიუვიოზი An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'დან პლიუვიოზი An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'პლიუვიოზი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ PLUV 12', 'перед პლიუვიოზი An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ვანტოზი An XII'],
            ['@#DFRENCH R@ VENT 12', 'ვანტოზი An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'მიახლოებით ვანტოზი An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'დან ვანტოზი An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'ვანტოზი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ VENT 12', 'перед ვანტოზი An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 ჟერმინალი An XII'],
            ['@#DFRENCH R@ GERM 12', 'ჟერმინალი An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'მიახლოებით ჟერმინალი An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'დან ჟერმინალი An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'ჟერმინალი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ GERM 12', 'перед ჟერმინალი An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 ფლორეალი An XII'],
            ['@#DFRENCH R@ FLOR 12', 'ფლორეალი An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'მიახლოებით ფლორეალი An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'დან ფლორეალი An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'ფლორეალი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ FLOR 12', 'перед ფლორეალი An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 პრერიალი An XII'],
            ['@#DFRENCH R@ PRAI 12', 'პრერიალი An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'მიახლოებით პრერიალი An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'დან პრერიალი An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'პრერიალი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ PRAI 12', 'перед პრერიალი An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 მესიდორი An XII'],
            ['@#DFRENCH R@ MESS 12', 'მესიდორი An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'მიახლოებით მესიდორი An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'დან მესიდორი An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'მესიდორი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ MESS 12', 'перед მესიდორი An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 თერმიდორი An XII'],
            ['@#DFRENCH R@ THER 12', 'თერმიდორი An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'მიახლოებით თერმიდორი An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'დან თერმიდორი An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'თერმიდორი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ THER 12', 'перед თერმიდორი An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 ფრიუქტიდორი An XII'],
            ['@#DFRENCH R@ FRUC 12', 'ფრიუქტიდორი An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'მიახლოებით ფრიუქტიდორი An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'დან ფრიუქტიდორი An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'ფრიუქტიდორი An XII შემდეგ'],
            ['BEF @#DFRENCH R@ FRUC 12', 'перед ფრიუქტიდორი An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 დამატებითი დღეები An XII'],
            ['@#DFRENCH R@ COMP 12', 'დამატებითი დღეები An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'მიახლოებით დამატებითი დღეები An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'დან დამატებითი დღეები An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'დამატებითი დღეები An XII შემდეგ'],
            ['BEF @#DFRENCH R@ COMP 12', 'перед დამატებითი დღეები An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'მიახლოებით 15 ვანდემიერი An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'გამოთვლილია 15 ვანდემიერი An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'სავარაუდოდ 15 ვანდემიერი An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'перед 15 ვანდემიერი An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '15 ვანდემიერი An XII შემდეგ'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'დან 15 ვანდემიერი An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', '15 ვანდემიერი An XII მდე'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '15 ვანდემიერი An XII და 15 ბრიუმერი An XII შორის'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'დან 15 ვანდემიერი An XII ადრე 15 ბრიუმერი An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'ამოცნობილია როგორც 15 ვანდემიერი An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 მუჰარამი 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'მუჰარამი 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'მიახლოებით მუჰარამი 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'დან მუჰარამი 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'მუჰარამი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'перед მუჰარამი 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 საფარი 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'საფარი 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'მიახლოებით საფარი 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'დან საფარი 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'საფარი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'перед საფარი 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 რაბი ალ-ავალი 1425'],
            ['@#DHIJRI@ RABIA 1425', 'რაბი ალ-ავალი 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'მიახლოებით რაბი ალ-ავალი 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'დან რაბი ალ-ავალი 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'რაბი ალ-ავალი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ RABIA 1425', 'перед რაბი ალ-ავალი 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 რაბი ას-სანი 1425'],
            ['@#DHIJRI@ RABIT 1425', 'რაბი ას-სანი 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'მიახლოებით რაბი ას-სანი 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'დან რაბი ას-სანი 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'რაბი ას-სანი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ RABIT 1425', 'перед რაბი ას-სანი 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 ჯუმადა ალ-ულა 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'ჯუმადა ალ-ულა 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'მიახლოებით ჯუმადა ალ-ულა 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'დან ჯუმადა ალ-ულა 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'ჯუმადა ალ-ულა 1425 შემდეგ'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'перед ჯუმადა ალ-ულა 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 ჯუმადა ას-სანი 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'ჯუმადა ას-სანი 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'მიახლოებით ჯუმადა ას-სანი 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'დან ჯუმადა ას-სანი 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'ჯუმადა ას-სანი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'перед ჯუმადა ას-სანი 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 რაჯაბი 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'რაჯაბი 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'მიახლოებით რაჯაბი 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'დან რაჯაბი 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'რაჯაბი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'перед რაჯაბი 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 შააბანი 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'შააბანი 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'მიახლოებით შააბანი 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'დან შააბანი 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'შააბანი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'перед შააბანი 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 რამადანი 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'რამადანი 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'მიახლოებით რამადანი 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'დან რამადანი 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'რამადანი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'перед რამადანი 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 შავალი 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'შავალი 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'მიახლოებით შავალი 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'დან შავალი 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'შავალი 1425 შემდეგ'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'перед შავალი 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 ზულ-ქაადა 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'ზულ-ქაადა 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'მიახლოებით ზულ-ქაადა 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'დან ზულ-ქაადა 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'ზულ-ქაადა 1425 შემდეგ'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'перед ზულ-ქაადა 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'მიახლოებით 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'დან 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', '1425 შემდეგ'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'перед 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'მიახლოებით 15 მუჰარამი 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'გამოთვლილია 15 მუჰარამი 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'სავარაუდოდ 15 მუჰარამი 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'перед 15 მუჰარამი 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '15 მუჰარამი 1425 შემდეგ'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'დან 15 მუჰარამი 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '15 მუჰარამი 1425 მდე'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '15 მუჰარამი 1425 და 15 საფარი 1425 შორის'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'დან 15 მუჰარამი 1425 ადრე 15 საფარი 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'ამოცნობილია როგორც 15 მუჰარამი 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 ფარვარდინი 1384'],
            ['@#DJALALI@ FARVA 1384', 'ფარვარდინი 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'მიახლოებით ფარვარდინი 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'დან ფარვარდინი 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'ფარვარდინი 1384 შემდეგ'],
            ['BEF @#DJALALI@ FARVA 1384', 'перед ფარვარდინი 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 ორდიბეჰეშთი 1384'],
            ['@#DJALALI@ ORDIB 1384', 'ორდიბეჰეშთი 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'მიახლოებით ორდიბეჰეშთი 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'დან ორდიბეჰეშთი 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'ორდიბეჰეშთი 1384 შემდეგ'],
            ['BEF @#DJALALI@ ORDIB 1384', 'перед ორდიბეჰეშთი 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 ხორდადი 1384'],
            ['@#DJALALI@ KHORD 1384', 'ხორდადი 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'მიახლოებით ხორდადი 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'დან ხორდადი 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'ხორდადი 1384 შემდეგ'],
            ['BEF @#DJALALI@ KHORD 1384', 'перед ხორდადი 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 თირი 1384'],
            ['@#DJALALI@ TIR 1384', 'თირი 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'მიახლოებით თირი 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'დან თირი 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'თირი 1384 შემდეგ'],
            ['BEF @#DJALALI@ TIR 1384', 'перед თირი 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 მორდადი 1384'],
            ['@#DJALALI@ MORDA 1384', 'მორდადი 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'მიახლოებით მორდადი 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'დან მორდადი 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'მორდადი 1384 შემდეგ'],
            ['BEF @#DJALALI@ MORDA 1384', 'перед მორდადი 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 შაჰრივარი 1384'],
            ['@#DJALALI@ SHAHR 1384', 'შაჰრივარი 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'მიახლოებით შაჰრივარი 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'დან შაჰრივარი 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'შაჰრივარი 1384 შემდეგ'],
            ['BEF @#DJALALI@ SHAHR 1384', 'перед შაჰრივარი 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 მეჰრი 1384'],
            ['@#DJALALI@ MEHR 1384', 'მეჰრი 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'მიახლოებით მეჰრი 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'დან მეჰრი 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'მეჰრი 1384 შემდეგ'],
            ['BEF @#DJALALI@ MEHR 1384', 'перед მეჰრი 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 აბანი 1384'],
            ['@#DJALALI@ ABAN 1384', 'აბანი 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'მიახლოებით აბანი 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'დან აბანი 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'აბანი 1384 შემდეგ'],
            ['BEF @#DJALALI@ ABAN 1384', 'перед აბანი 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 აზარი 1384'],
            ['@#DJALALI@ AZAR 1384', 'აზარი 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'მიახლოებით აზარი 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'დან აზარი 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'აზარი 1384 შემდეგ'],
            ['BEF @#DJALALI@ AZAR 1384', 'перед აზარი 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 დეი 1384'],
            ['@#DJALALI@ DEY 1384', 'დეი 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'მიახლოებით დეი 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'დან დეი 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'დეი 1384 შემდეგ'],
            ['BEF @#DJALALI@ DEY 1384', 'перед დეი 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 ბაჰმანი 1384'],
            ['@#DJALALI@ BAHMA 1384', 'ბაჰმანი 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'მიახლოებით ბაჰმანი 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'დან ბაჰმანი 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'ბაჰმანი 1384 შემდეგ'],
            ['BEF @#DJALALI@ BAHMA 1384', 'перед ბაჰმანი 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 ესფანდი 1384'],
            ['@#DJALALI@ ESFAN 1384', 'ესფანდი 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'მიახლოებით ესფანდი 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'დან ესფანდი 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'ესფანდი 1384 შემდეგ'],
            ['BEF @#DJALALI@ ESFAN 1384', 'перед ესფანდი 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'მიახლოებით 15 ფარვარდინი 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'გამოთვლილია 15 ფარვარდინი 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'სავარაუდოდ 15 ფარვარდინი 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'перед 15 ფარვარდინი 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '15 ფარვარდინი 1384 შემდეგ'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'დან 15 ფარვარდინი 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', '15 ფარვარდინი 1384 მდე'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '15 ფარვარდინი 1384 და 15 ორდიბეჰეშთი 1384 შორის'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'დან 15 ფარვარდინი 1384 ადრე 15 ორდიბეჰეშთი 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'ამოცნობილია როგორც 15 ფარვარდინი 1384'],
        ];
    }
}
