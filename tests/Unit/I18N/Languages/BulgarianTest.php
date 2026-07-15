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
use Fisharebest\Webtrees\I18N\Languages\Bulgarian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Bulgarian::class)]
class BulgarianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Bulgarian();
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
        self::assertSame(['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('bg', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('български', self::language()->endonym());
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
        self::assertRelationshipNames('съпруга', 'съпруг', [$husband, $fm, $wife]);
        self::assertRelationshipNames('бивш съпруг', 'бивша съпруга', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('годеница', 'годеник', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('майка', 'син', [$son, $fm, $wife]);
        self::assertRelationshipNames('баща', 'син', [$son, $fm, $husband]);
        self::assertRelationshipNames('майка', 'дъщеря', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('осиновителка', 'осиновен син', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('осиновител', 'осиновен син', [$adoptedSon, $fd, $exHusband]);

        // Siblings (son born 2000 is older than daughter born 2001)
        self::assertRelationshipNames('по-малка сестра', 'по-голям брат', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('полубрат', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('доведен баща', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('доведена дъщеря', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('тъща', 'зет', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('тъст', 'зет', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('снаха', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('баба', 'внук', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('дядо', 'внук', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) — пра prefix
        self::assertRelationshipName('прадядо', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('прабаба', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('леля', 'племенник', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('чичо', 'племенник', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('племенница', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('племенник', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('братовчедка', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('братовчед', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) — пра prefix
        self::assertRelationshipName('пралеля', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('прачичо', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Януари 2000'],
            ['JAN 2000', 'Януари 2000'],
            ['ABT JAN 2000', 'около Януари 2000'],
            ['FROM JAN 2000', 'from Януари 2000'],
            ['AFT JAN 2000', 'след Януари 2000'],
            ['BEF JAN 2000', 'преди Януари 2000'],
            ['15 FEB 2000', '15 Февруари 2000'],
            ['FEB 2000', 'Февруари 2000'],
            ['ABT FEB 2000', 'около Февруари 2000'],
            ['FROM FEB 2000', 'from Февруари 2000'],
            ['AFT FEB 2000', 'след Февруари 2000'],
            ['BEF FEB 2000', 'преди Февруари 2000'],
            ['15 MAR 2000', '15 Март 2000'],
            ['MAR 2000', 'Март 2000'],
            ['ABT MAR 2000', 'около Март 2000'],
            ['FROM MAR 2000', 'from Март 2000'],
            ['AFT MAR 2000', 'след Март 2000'],
            ['BEF MAR 2000', 'преди Март 2000'],
            ['15 APR 2000', '15 Април 2000'],
            ['APR 2000', 'Април 2000'],
            ['ABT APR 2000', 'около Април 2000'],
            ['FROM APR 2000', 'from Април 2000'],
            ['AFT APR 2000', 'след Април 2000'],
            ['BEF APR 2000', 'преди Април 2000'],
            ['15 MAY 2000', '15 Май 2000'],
            ['MAY 2000', 'Май 2000'],
            ['ABT MAY 2000', 'около Май 2000'],
            ['FROM MAY 2000', 'from Май 2000'],
            ['AFT MAY 2000', 'след Май 2000'],
            ['BEF MAY 2000', 'преди Май 2000'],
            ['15 JUN 2000', '15 Юни 2000'],
            ['JUN 2000', 'Юни 2000'],
            ['ABT JUN 2000', 'около Юни 2000'],
            ['FROM JUN 2000', 'from Юни 2000'],
            ['AFT JUN 2000', 'след Юни 2000'],
            ['BEF JUN 2000', 'преди Юни 2000'],
            ['15 JUL 2000', '15 Юли 2000'],
            ['JUL 2000', 'Юли 2000'],
            ['ABT JUL 2000', 'около Юли 2000'],
            ['FROM JUL 2000', 'from Юли 2000'],
            ['AFT JUL 2000', 'след Юли 2000'],
            ['BEF JUL 2000', 'преди Юли 2000'],
            ['15 AUG 2000', '15 Август 2000'],
            ['AUG 2000', 'Август 2000'],
            ['ABT AUG 2000', 'около Август 2000'],
            ['FROM AUG 2000', 'from Август 2000'],
            ['AFT AUG 2000', 'след Август 2000'],
            ['BEF AUG 2000', 'преди Август 2000'],
            ['15 SEP 2000', '15 Септември 2000'],
            ['SEP 2000', 'Септември 2000'],
            ['ABT SEP 2000', 'около Септември 2000'],
            ['FROM SEP 2000', 'from Септември 2000'],
            ['AFT SEP 2000', 'след Септември 2000'],
            ['BEF SEP 2000', 'преди Септември 2000'],
            ['15 OCT 2000', '15 Октомври 2000'],
            ['OCT 2000', 'Октомври 2000'],
            ['ABT OCT 2000', 'около Октомври 2000'],
            ['FROM OCT 2000', 'from Октомври 2000'],
            ['AFT OCT 2000', 'след Октомври 2000'],
            ['BEF OCT 2000', 'преди Октомври 2000'],
            ['15 NOV 2000', '15 Ноември 2000'],
            ['NOV 2000', 'Ноември 2000'],
            ['ABT NOV 2000', 'около Ноември 2000'],
            ['FROM NOV 2000', 'from Ноември 2000'],
            ['AFT NOV 2000', 'след Ноември 2000'],
            ['BEF NOV 2000', 'преди Ноември 2000'],
            ['15 DEC 2000', '15 Декември 2000'],
            ['DEC 2000', 'Декември 2000'],
            ['ABT DEC 2000', 'около Декември 2000'],
            ['FROM DEC 2000', 'from Декември 2000'],
            ['AFT DEC 2000', 'след Декември 2000'],
            ['BEF DEC 2000', 'преди Декември 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'около 15 Януари 2000'],
            ['CAL 15 JAN 2000', 'calculated 15 Януари 2000'],
            ['EST 15 JAN 2000', 'estimated 15 Януари 2000'],
            ['BEF 15 JAN 2000', 'преди 15 Януари 2000'],
            ['AFT 15 JAN 2000', 'след 15 Януари 2000'],
            ['FROM 15 JAN 2000', 'from 15 Януари 2000'],
            ['TO 15 JAN 2000', 'to 15 Януари 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between 15 Януари 2000 and 15 Февруари 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from 15 Януари 2000 to 15 Февруари 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 Януари 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Януари 1700 новата ера'],
            ['@#DJULIAN@ JAN 1700', 'Януари 1700 новата ера'],
            ['ABT @#DJULIAN@ JAN 1700', 'около Януари 1700 новата ера'],
            ['FROM @#DJULIAN@ JAN 1700', 'from Януари 1700 новата ера'],
            ['AFT @#DJULIAN@ JAN 1700', 'след Януари 1700 новата ера'],
            ['BEF @#DJULIAN@ JAN 1700', 'преди Януари 1700 новата ера'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Февруари 1700 новата ера'],
            ['@#DJULIAN@ FEB 1700', 'Февруари 1700 новата ера'],
            ['ABT @#DJULIAN@ FEB 1700', 'около Февруари 1700 новата ера'],
            ['FROM @#DJULIAN@ FEB 1700', 'from Февруари 1700 новата ера'],
            ['AFT @#DJULIAN@ FEB 1700', 'след Февруари 1700 новата ера'],
            ['BEF @#DJULIAN@ FEB 1700', 'преди Февруари 1700 новата ера'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Март 1700 новата ера'],
            ['@#DJULIAN@ MAR 1700', 'Март 1700 новата ера'],
            ['ABT @#DJULIAN@ MAR 1700', 'около Март 1700 новата ера'],
            ['FROM @#DJULIAN@ MAR 1700', 'from Март 1700 новата ера'],
            ['AFT @#DJULIAN@ MAR 1700', 'след Март 1700 новата ера'],
            ['BEF @#DJULIAN@ MAR 1700', 'преди Март 1700 новата ера'],
            ['@#DJULIAN@ 15 APR 1700', '15 Април 1700 новата ера'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Април 1645/46 новата ера'],
            ['@#DJULIAN@ APR 1700', 'Април 1700 новата ера'],
            ['ABT @#DJULIAN@ APR 1700', 'около Април 1700 новата ера'],
            ['FROM @#DJULIAN@ APR 1700', 'from Април 1700 новата ера'],
            ['AFT @#DJULIAN@ APR 1700', 'след Април 1700 новата ера'],
            ['BEF @#DJULIAN@ APR 1700', 'преди Април 1700 новата ера'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Май 1700 новата ера'],
            ['@#DJULIAN@ MAY 1700', 'Май 1700 новата ера'],
            ['ABT @#DJULIAN@ MAY 1700', 'около Май 1700 новата ера'],
            ['FROM @#DJULIAN@ MAY 1700', 'from Май 1700 новата ера'],
            ['AFT @#DJULIAN@ MAY 1700', 'след Май 1700 новата ера'],
            ['BEF @#DJULIAN@ MAY 1700', 'преди Май 1700 новата ера'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Юни 1700 новата ера'],
            ['@#DJULIAN@ JUN 1700', 'Юни 1700 новата ера'],
            ['ABT @#DJULIAN@ JUN 1700', 'около Юни 1700 новата ера'],
            ['FROM @#DJULIAN@ JUN 1700', 'from Юни 1700 новата ера'],
            ['AFT @#DJULIAN@ JUN 1700', 'след Юни 1700 новата ера'],
            ['BEF @#DJULIAN@ JUN 1700', 'преди Юни 1700 новата ера'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Юли 1700 новата ера'],
            ['@#DJULIAN@ JUL 1700', 'Юли 1700 новата ера'],
            ['ABT @#DJULIAN@ JUL 1700', 'около Юли 1700 новата ера'],
            ['FROM @#DJULIAN@ JUL 1700', 'from Юли 1700 новата ера'],
            ['AFT @#DJULIAN@ JUL 1700', 'след Юли 1700 новата ера'],
            ['BEF @#DJULIAN@ JUL 1700', 'преди Юли 1700 новата ера'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Август 1700 новата ера'],
            ['@#DJULIAN@ AUG 1700', 'Август 1700 новата ера'],
            ['ABT @#DJULIAN@ AUG 1700', 'около Август 1700 новата ера'],
            ['FROM @#DJULIAN@ AUG 1700', 'from Август 1700 новата ера'],
            ['AFT @#DJULIAN@ AUG 1700', 'след Август 1700 новата ера'],
            ['BEF @#DJULIAN@ AUG 1700', 'преди Август 1700 новата ера'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Септември 1700 новата ера'],
            ['@#DJULIAN@ SEP 1700', 'Септември 1700 новата ера'],
            ['ABT @#DJULIAN@ SEP 1700', 'около Септември 1700 новата ера'],
            ['FROM @#DJULIAN@ SEP 1700', 'from Септември 1700 новата ера'],
            ['AFT @#DJULIAN@ SEP 1700', 'след Септември 1700 новата ера'],
            ['BEF @#DJULIAN@ SEP 1700', 'преди Септември 1700 новата ера'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Октомври 1700 новата ера'],
            ['@#DJULIAN@ OCT 1700', 'Октомври 1700 новата ера'],
            ['ABT @#DJULIAN@ OCT 1700', 'около Октомври 1700 новата ера'],
            ['FROM @#DJULIAN@ OCT 1700', 'from Октомври 1700 новата ера'],
            ['AFT @#DJULIAN@ OCT 1700', 'след Октомври 1700 новата ера'],
            ['BEF @#DJULIAN@ OCT 1700', 'преди Октомври 1700 новата ера'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Ноември 1700 новата ера'],
            ['@#DJULIAN@ NOV 1700', 'Ноември 1700 новата ера'],
            ['ABT @#DJULIAN@ NOV 1700', 'около Ноември 1700 новата ера'],
            ['FROM @#DJULIAN@ NOV 1700', 'from Ноември 1700 новата ера'],
            ['AFT @#DJULIAN@ NOV 1700', 'след Ноември 1700 новата ера'],
            ['BEF @#DJULIAN@ NOV 1700', 'преди Ноември 1700 новата ера'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Декември 1700 новата ера'],
            ['@#DJULIAN@ DEC 1700', 'Декември 1700 новата ера'],
            ['ABT @#DJULIAN@ DEC 1700', 'около Декември 1700 новата ера'],
            ['FROM @#DJULIAN@ DEC 1700', 'from Декември 1700 новата ера'],
            ['AFT @#DJULIAN@ DEC 1700', 'след Декември 1700 новата ера'],
            ['BEF @#DJULIAN@ DEC 1700', 'преди Декември 1700 новата ера'],
            ['@#DJULIAN@ 1700', '1700 новата ера'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'около 15 Януари 1700 новата ера'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15 Януари 1700 новата ера'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 Януари 1700 новата ера'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'преди 15 Януари 1700 новата ера'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'след 15 Януари 1700 новата ера'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from 15 Януари 1700 новата ера'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15 Януари 1700 новата ера'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between 15 Януари 1700 новата ера and 15 Февруари 1700 новата ера'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from 15 Януари 1700 новата ера to 15 Февруари 1700 новата ера'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 Януари 1700 новата ера'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'около Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'from Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'след Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'преди Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'около Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'from Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'след Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'преди Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'около Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'from Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'след Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'преди Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'около Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'from Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'след Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'преди Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'около Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'from Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'след Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'преди Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'около Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'from Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'след Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'преди Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'около Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'from Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'след Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'преди Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'около Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'from Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'след Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'преди Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'около Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'from Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'след Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'преди Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'около Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'from Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'след Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'преди Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'около Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'след Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'преди Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'около Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'from Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'след Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'преди Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'около Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'from Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'след Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'преди Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'около 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'преди 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'след 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between 15 Tishrei 5765 and 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from 15 Tishrei 5765 to 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'около Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'след Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'преди Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'около Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'след Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'преди Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'около Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'след Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'преди Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'около Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'след Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'преди Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'около Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'след Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'преди Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'около Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'след Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'преди Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'около Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'след Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'преди Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'около Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'след Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'преди Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'около Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'след Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'преди Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'около Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'след Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'преди Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'около Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'след Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'преди Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'около Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'след Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'преди Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'около jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'след jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'преди jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'около 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'преди 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'след 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between 15 Vendémiaire An XII and 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from 15 Vendémiaire An XII to 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Мухаррам 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Мухаррам 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'около Мухаррам 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from Мухаррам 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'след Мухаррам 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'преди Мухаррам 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Сафар 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Сафар 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'около Сафар 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from Сафар 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'след Сафар 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'преди Сафар 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Раби аль-авваль 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Раби аль-авваль 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'около Раби аль-авваль 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Раби аль-авваль 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'след Раби аль-авваль 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'преди Раби аль-авваль 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Раби ас-сани 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Раби ас-сани 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'около Раби ас-сани 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Раби ас-сани 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'след Раби ас-сани 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'преди Раби ас-сани 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Джумада аль-уля 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Джумада аль-уля 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'около Джумада аль-уля 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from Джумада аль-уля 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'след Джумада аль-уля 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'преди Джумада аль-уля 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Джумада ас-сани 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Джумада ас-сани 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'около Джумада ас-сани 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from Джумада ас-сани 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'след Джумада ас-сани 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'преди Джумада ас-сани 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Раджаб 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Раджаб 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'около Раджаб 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from Раджаб 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'след Раджаб 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'преди Раджаб 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Шаабан 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Шаабан 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'около Шаабан 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Шаабан 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'след Шаабан 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'преди Шаабан 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Рамадан 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Рамадан 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'около Рамадан 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Рамадан 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'след Рамадан 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'преди Рамадан 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Шаввал 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Шаввал 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'около Шаввал 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Шаввал 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'след Шаввал 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'преди Шаввал 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Зу-л-Каада 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Зу-л-Каада 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'около Зу-л-Каада 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Зу-л-Каада 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'след Зу-л-Каада 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'преди Зу-л-Каада 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'около 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'след 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'преди 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'около 15 Мухаррам 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated 15 Мухаррам 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15 Мухаррам 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'преди 15 Мухаррам 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'след 15 Мухаррам 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from 15 Мухаррам 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to 15 Мухаррам 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between 15 Мухаррам 1425 and 15 Сафар 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from 15 Мухаррам 1425 to 15 Сафар 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15 Мухаррам 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'около Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'from Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'след Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'преди Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'около Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'след Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'преди Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'около Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'from Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'след Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'преди Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'около Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'from Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'след Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'преди Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'около Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'from Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'след Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'преди Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'около Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'след Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'преди Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'около Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'from Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'след Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'преди Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'около Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'from Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'след Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'преди Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'около Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'from Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'след Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'преди Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'около Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'from Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'след Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'преди Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'около Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'след Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'преди Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'около Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'след Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'преди Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'около 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'преди 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'след 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between 15 Farvardin 1384 and 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from 15 Farvardin 1384 to 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15 Farvardin 1384'],
        ];
    }
}
