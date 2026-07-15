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
use Fisharebest\Webtrees\I18N\Languages\Serbian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Serbian::class)]
class SerbianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Serbian();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Cyrl, self::language()->script());
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
        self::assertSame(['А', 'Б', 'В', 'Г', 'Д', 'Ђ', 'Е', 'Ж', 'З', 'И', 'Ј', 'К', 'Л', 'Љ', 'М', 'Н', 'Њ', 'О', 'П', 'Р', 'С', 'Т', 'Ћ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('sr', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('српски', self::language()->endonym());
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
        self::assertSame('one и two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two и three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one или two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two или three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('супруга', 'супруг', [$husband, $fm, $wife]);
        self::assertRelationshipNames('бивши супруг', 'бивша супруга', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('вереница', 'вереник', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('мајка', 'син', [$son, $fm, $wife]);
        self::assertRelationshipNames('отац', 'син', [$son, $fm, $husband]);
        self::assertRelationshipNames('мајка', 'ћерка', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('усвојитељка', 'усвојени син', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('усвојитељ', 'усвојени син', [$adoptedSon, $fd, $exHusband]);

        // Siblings (son born 2000 is older than daughter born 2001)
        self::assertRelationshipNames('млађа сестра', 'старији брат', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('полубрат', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('очух', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('пасторка', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('ташта', 'зет', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('таст', 'зет', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('снаха', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('бака', 'унук', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('деда', 'унук', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) — пра prefix
        self::assertRelationshipName('прадеда', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('прабака', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('тетка', 'нећак', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('стриц', 'нећак', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('нећакиња', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('нећак', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('сестрична', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('братић', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) — пра prefix
        self::assertRelationshipName('пратетка', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('прастриц', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15. јануара 2000'],
            ['JAN 2000', 'Јануар 2000'],
            ['ABT JAN 2000', 'about јануара 2000'],
            ['FROM JAN 2000', 'from јануара 2000'],
            ['AFT JAN 2000', 'after јануару 2000'],
            ['BEF JAN 2000', 'before јануара 2000'],
            ['15 FEB 2000', '15. фебруара 2000'],
            ['FEB 2000', 'Фебруар 2000'],
            ['ABT FEB 2000', 'about фебруара 2000'],
            ['FROM FEB 2000', 'from фебруара 2000'],
            ['AFT FEB 2000', 'after фебруару 2000'],
            ['BEF FEB 2000', 'before фебруара 2000'],
            ['15 MAR 2000', '15. марта 2000'],
            ['MAR 2000', 'Март 2000'],
            ['ABT MAR 2000', 'about марта 2000'],
            ['FROM MAR 2000', 'from марта 2000'],
            ['AFT MAR 2000', 'after марту 2000'],
            ['BEF MAR 2000', 'before марта 2000'],
            ['15 APR 2000', '15. априла 2000'],
            ['APR 2000', 'Април 2000'],
            ['ABT APR 2000', 'about априла 2000'],
            ['FROM APR 2000', 'from априла 2000'],
            ['AFT APR 2000', 'after априлу 2000'],
            ['BEF APR 2000', 'before априла 2000'],
            ['15 MAY 2000', '15. маја 2000'],
            ['MAY 2000', 'Мај 2000'],
            ['ABT MAY 2000', 'about маја 2000'],
            ['FROM MAY 2000', 'from маја 2000'],
            ['AFT MAY 2000', 'after мају 2000'],
            ['BEF MAY 2000', 'before мајем 2000'],
            ['15 JUN 2000', '15. јуна 2000'],
            ['JUN 2000', 'Јун 2000'],
            ['ABT JUN 2000', 'about јуна 2000'],
            ['FROM JUN 2000', 'from јуна 2000'],
            ['AFT JUN 2000', 'after јуну 2000'],
            ['BEF JUN 2000', 'before јуна 2000'],
            ['15 JUL 2000', '15. јула 2000'],
            ['JUL 2000', 'Јул 2000'],
            ['ABT JUL 2000', 'about јула 2000'],
            ['FROM JUL 2000', 'from јула 2000'],
            ['AFT JUL 2000', 'after јулу 2000'],
            ['BEF JUL 2000', 'before јула 2000'],
            ['15 AUG 2000', '15. августа 2000'],
            ['AUG 2000', 'Август 2000'],
            ['ABT AUG 2000', 'about августа 2000'],
            ['FROM AUG 2000', 'from августа 2000'],
            ['AFT AUG 2000', 'after августу 2000'],
            ['BEF AUG 2000', 'before августа 2000'],
            ['15 SEP 2000', '15. септембра 2000'],
            ['SEP 2000', 'Септембар 2000'],
            ['ABT SEP 2000', 'about септембра 2000'],
            ['FROM SEP 2000', 'from септембра 2000'],
            ['AFT SEP 2000', 'after септембру 2000'],
            ['BEF SEP 2000', 'before септембра 2000'],
            ['15 OCT 2000', '15. октобра 2000'],
            ['OCT 2000', 'Октобар 2000'],
            ['ABT OCT 2000', 'about октобра 2000'],
            ['FROM OCT 2000', 'from октобра 2000'],
            ['AFT OCT 2000', 'after октобру 2000'],
            ['BEF OCT 2000', 'before октобра 2000'],
            ['15 NOV 2000', '15. новембра 2000'],
            ['NOV 2000', 'Новембар 2000'],
            ['ABT NOV 2000', 'about новембра 2000'],
            ['FROM NOV 2000', 'from новембра 2000'],
            ['AFT NOV 2000', 'after новембру 2000'],
            ['BEF NOV 2000', 'before новембра 2000'],
            ['15 DEC 2000', '15. децембра 2000'],
            ['DEC 2000', 'Децембар 2000'],
            ['ABT DEC 2000', 'about децембра 2000'],
            ['FROM DEC 2000', 'from децембра 2000'],
            ['AFT DEC 2000', 'after децембру 2000'],
            ['BEF DEC 2000', 'before децембра 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'about 15. јануара 2000'],
            ['CAL 15 JAN 2000', 'calculated 15. јануара 2000'],
            ['EST 15 JAN 2000', 'estimated 15. јануара 2000'],
            ['BEF 15 JAN 2000', 'before 15. јануара 2000'],
            ['AFT 15 JAN 2000', 'after 15. јануара 2000'],
            ['FROM 15 JAN 2000', 'from 15. јануара 2000'],
            ['TO 15 JAN 2000', 'to 15. јануара 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'између 15. јануара 2000 и 15. фебруара 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from 15. јануара 2000 to 15. фебруара 2000'],
            ['INT 15 JAN 2000', 'interpreted 15. јануара 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. јануара 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Јануар 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about јануара 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from јануара 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after јануару 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before јануара 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15. фебруара 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Фебруар 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about фебруара 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from фебруара 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after фебруару 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before фебруара 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15. марта 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Март 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about марта 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from марта 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after марту 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before марта 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15. априла 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. априла 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'Април 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about априла 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from априла 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after априлу 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before априла 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15. маја 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Мај 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about маја 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from маја 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after мају 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before мајем 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15. јуна 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Јун 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about јуна 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from јуна 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after јуну 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before јуна 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15. јула 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Јул 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about јула 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from јула 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after јулу 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before јула 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15. августа 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Август 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about августа 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from августа 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after августу 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before августа 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15. септембра 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Септембар 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about септембра 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from септембра 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after септембру 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before септембра 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15. октобра 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Октобар 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about октобра 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from октобра 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after октобру 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before октобра 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15. новембра 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Новембар 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about новембра 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from новембра 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after новембру 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before новембра 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15. децембра 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Децембар 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about децембра 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from децембра 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after децембру 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before децембра 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about 15. јануара 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15. јануара 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15. јануара 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before 15. јануара 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after 15. јануара 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from 15. јануара 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15. јануара 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'између 15. јануара 1700 ᴄᴇ и 15. фебруара 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from 15. јануара 1700 ᴄᴇ to 15. фебруара 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15. јануара 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15. Тишреј 5765'],
            ['@#DHEBREW@ TSH 5765', 'Тишреј 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'about Тишреј 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'from Тишреј 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'after Тишреј 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'before Тишреј 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. Хешван 5765'],
            ['@#DHEBREW@ CSH 5765', 'Хешван 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'about Хешван 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'from Хешван 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'after Хешван 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'before Хешван 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. Кислев 5765'],
            ['@#DHEBREW@ KSL 5765', 'Кислев 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'about Кислев 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'from Кислев 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'after Кислев 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'before Кислев 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. Тевет 5765'],
            ['@#DHEBREW@ TVT 5765', 'Тевет 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'about Тевет 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'from Тевет 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'after Тевет 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'before Тевет 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. Шеват 5765'],
            ['@#DHEBREW@ SHV 5765', 'Шеват 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'about Шеват 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'from Шеват 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'after Шеват 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'before Шеват 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Адар I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Адар I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'about Адар I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'from Адар I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'after Адар I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'before Адар I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Адар II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Адар II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'about Адар II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'from Адар II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'after Адар II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'before Адар II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. Нисан 5765'],
            ['@#DHEBREW@ NSN 5765', 'Нисан 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'about Нисан 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'from Нисан 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'after Нисан 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'before Нисан 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Ијар 5765'],
            ['@#DHEBREW@ IYR 5765', 'Ијар 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'about Ијар 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'from Ијар 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'after Ијар 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'before Ијар 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. Сиван 5765'],
            ['@#DHEBREW@ SVN 5765', 'Сиван 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'about Сиван 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'from Сиван 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'after Сиван 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'before Сиван 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. Тамуз 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Тамуз 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about Тамуз 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from Тамуз 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after Тамуз 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before Тамуз 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Ав 5765'],
            ['@#DHEBREW@ AAV 5765', 'Ав 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'about Ав 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'from Ав 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'after Ав 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'before Ав 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. Елул 5765'],
            ['@#DHEBREW@ ELL 5765', 'Елул 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'about Елул 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'from Елул 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'after Елул 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'before Елул 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about 15. Тишреј 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15. Тишреј 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15. Тишреј 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before 15. Тишреј 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after 15. Тишреј 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from 15. Тишреј 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15. Тишреј 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'између 15. Тишреј 5765 и 15. Хешван 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from 15. Тишреј 5765 to 15. Хешван 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15. Тишреј 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Вандемијер An XII'],
            ['@#DFRENCH R@ VEND 12', 'Вандемијер An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about Вандемијер An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from Вандемијер An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after Вандемијер An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before Вандемијер An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Бример An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Бример An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about Бример An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from Бример An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after Бример An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before Бример An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Фример An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Фример An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about Фример An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from Фример An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after Фример An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before Фример An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Нивоз An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Нивоз An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about Нивоз An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from Нивоз An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after Нивоз An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before Нивоз An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Пливиоз An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Пливиоз An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about Пливиоз An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from Пливиоз An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after Пливиоз An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before Пливиоз An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Вантоз An XII'],
            ['@#DFRENCH R@ VENT 12', 'Вантоз An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about Вантоз An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from Вантоз An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after Вантоз An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before Вантоз An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Жерминал An XII'],
            ['@#DFRENCH R@ GERM 12', 'Жерминал An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about Жерминал An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from Жерминал An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after Жерминал An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before Жерминал An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Флореал An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Флореал An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about Флореал An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from Флореал An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after Флореал An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before Флореал An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Преријал An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Преријал An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about Преријал An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from Преријал An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after Преријал An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before Преријал An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Месидор An XII'],
            ['@#DFRENCH R@ MESS 12', 'Месидор An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about Месидор An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from Месидор An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after Месидор An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before Месидор An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Термидор An XII'],
            ['@#DFRENCH R@ THER 12', 'Термидор An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about Термидор An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from Термидор An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after Термидор An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before Термидор An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Фруктидор An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Фруктидор An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about Фруктидор An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from Фруктидор An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after Фруктидор An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before Фруктидор An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. допунски дани An XII'],
            ['@#DFRENCH R@ COMP 12', 'допунски дани An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about допунски дани An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from допунски дани An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after допунски дани An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before допунски дани An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about 15. Вандемијер An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15. Вандемијер An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15. Вандемијер An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before 15. Вандемијер An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after 15. Вандемијер An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from 15. Вандемијер An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15. Вандемијер An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'између 15. Вандемијер An XII и 15. Бример An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from 15. Вандемијер An XII to 15. Бример An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15. Вандемијер An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. Мухарем 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Мухарем 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about Мухарем 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from Мухарем 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after Мухарем 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before Мухарем 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. Сафар 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Сафар 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about Сафар 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from Сафар 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after Сафар 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before Сафар 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Реби-ул-евел 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Реби-ул-евел 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about Реби-ул-евел 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Реби-ул-евел 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after Реби-ул-евел 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before Реби-ул-евел 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Реби-ул-ахир 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Реби-ул-ахир 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about Реби-ул-ахир 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Реби-ул-ахир 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after Реби-ул-ахир 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before Реби-ул-ахир 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. Џумадел-ула 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Џумадел-ула 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about Џумадел-ула 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from Џумадел-ула 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after Џумадел-ула 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before Џумадел-ула 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. Џумадел-ахира 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Џумадел-ахира 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about Џумадел-ахира 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from Џумадел-ахира 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after Џумадел-ахира 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before Џумадел-ахира 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. Реџеб 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Реџеб 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about Реџеб 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from Реџеб 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after Реџеб 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before Реџеб 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. Шабан 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Шабан 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about Шабан 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Шабан 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after Шабан 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before Шабан 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. Рамазан 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Рамазан 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about Рамазан 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Рамазан 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after Рамазан 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before Рамазан 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. Шевал 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Шевал 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about Шевал 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Шевал 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after Шевал 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before Шевал 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. Зулкаде 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Зулкаде 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about Зулкаде 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Зулкаде 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after Зулкаде 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before Зулкаде 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about 15. Мухарем 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated 15. Мухарем 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15. Мухарем 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before 15. Мухарем 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after 15. Мухарем 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from 15. Мухарем 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to 15. Мухарем 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'између 15. Мухарем 1425 и 15. Сафар 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from 15. Мухарем 1425 to 15. Сафар 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15. Мухарем 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Фарвардин 1384'],
            ['@#DJALALI@ FARVA 1384', 'Фарвардин 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'about Фарвардин 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'from Фарвардин 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'after Фарвардин 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'before Фарвардин 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ордибехешт 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ордибехешт 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about Ордибехешт 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from Ордибехешт 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after Ордибехешт 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before Ордибехешт 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Хордад 1384'],
            ['@#DJALALI@ KHORD 1384', 'Хордад 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'about Хордад 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'from Хордад 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'after Хордад 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'before Хордад 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Тир 1384'],
            ['@#DJALALI@ TIR 1384', 'Тир 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'about Тир 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'from Тир 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'after Тир 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'before Тир 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Мордад 1384'],
            ['@#DJALALI@ MORDA 1384', 'Мордад 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'about Мордад 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'from Мордад 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'after Мордад 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'before Мордад 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Шахривар 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Шахривар 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about Шахривар 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from Шахривар 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after Шахривар 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before Шахривар 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Мехр 1384'],
            ['@#DJALALI@ MEHR 1384', 'Мехр 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'about Мехр 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'from Мехр 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'after Мехр 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'before Мехр 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Абан 1384'],
            ['@#DJALALI@ ABAN 1384', 'Абан 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'about Абан 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'from Абан 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'after Абан 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'before Абан 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Азар 1384'],
            ['@#DJALALI@ AZAR 1384', 'Азар 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'about Азар 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'from Азар 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'after Азар 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'before Азар 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Деј 1384'],
            ['@#DJALALI@ DEY 1384', 'Деј 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'about Деј 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'from Деј 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'after Деј 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'before Деј 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Бахман 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Бахман 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about Бахман 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from Бахман 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after Бахман 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before Бахман 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Есфанд 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Есфанд 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about Есфанд 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from Есфанд 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after Есфанд 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before Есфанд 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about 15. Фарвардин 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated 15. Фарвардин 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15. Фарвардин 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before 15. Фарвардин 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after 15. Фарвардин 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from 15. Фарвардин 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to 15. Фарвардин 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'између 15. Фарвардин 1384 и 15. Ордибехешт 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from 15. Фарвардин 1384 to 15. Ордибехешт 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15. Фарвардин 1384'],
        ];
    }
}
