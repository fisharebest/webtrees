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
use Fisharebest\Webtrees\I18N\Languages\Tatar;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Tatar::class)]
class TatarTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Tatar();
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
        self::assertSame(['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('tt', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('татар', self::language()->endonym());
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
        self::assertSame('-123 456,0789 %', self::language()->percentage(-1234.560789));
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
        self::assertSame('one һәм two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two һәм three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one яки two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two яки three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('хатын', 'ир', [$husband, $fm, $wife]);
        self::assertRelationshipNames('элеккеге тормыш иптәше', 'элеккеге тормыш иптәше', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('яраштырылган', 'яраштырылган', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('әни', 'ул', [$son, $fm, $wife]);
        self::assertRelationshipNames('әти', 'ул', [$son, $fm, $husband]);
        self::assertRelationshipNames('әни', 'кыз', [$daughter, $fm, $wife]);

        // Siblings — elder/younger
        self::assertRelationshipNames('сеңел', 'абый', [$son, $fm, $daughter]);
        self::assertRelationshipNames('абый', 'сеңел', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('үги апа', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('үги әти', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('үги кыз', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('каенана', 'кияү', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('каената', 'кияү', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('килен', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('әби', 'оныч', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('бабай', 'оныч', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipNames('әби', 'оныч', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('бабай', 'оныч', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('бөек бабай', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('бөек әби', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles — paternal
        self::assertRelationshipNames('түти', 'туганның улы', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('абзый', 'туганның улы', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('туганның кызы', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('туганның улы', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('туган', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('туган', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Гыйнвар 2000'],
            ['JAN 2000', 'Гыйнвар 2000'],
            ['ABT JAN 2000', 'about Гыйнвар 2000'],
            ['FROM JAN 2000', 'from Гыйнвар 2000'],
            ['AFT JAN 2000', 'after Гыйнвар 2000'],
            ['BEF JAN 2000', 'before Гыйнвар 2000'],
            ['15 FEB 2000', '15 Февраль 2000'],
            ['FEB 2000', 'Февраль 2000'],
            ['ABT FEB 2000', 'about Февраль 2000'],
            ['FROM FEB 2000', 'from Февраль 2000'],
            ['AFT FEB 2000', 'after Февраль 2000'],
            ['BEF FEB 2000', 'before Февраль 2000'],
            ['15 MAR 2000', '15 Март 2000'],
            ['MAR 2000', 'Март 2000'],
            ['ABT MAR 2000', 'about Март 2000'],
            ['FROM MAR 2000', 'from Март 2000'],
            ['AFT MAR 2000', 'after Март 2000'],
            ['BEF MAR 2000', 'before Март 2000'],
            ['15 APR 2000', '15 Апрель 2000'],
            ['APR 2000', 'Апрель 2000'],
            ['ABT APR 2000', 'about Апрель 2000'],
            ['FROM APR 2000', 'from Апрель 2000'],
            ['AFT APR 2000', 'after Апрель 2000'],
            ['BEF APR 2000', 'before Апрель 2000'],
            ['15 MAY 2000', '15 Май 2000'],
            ['MAY 2000', 'Май 2000'],
            ['ABT MAY 2000', 'about Май 2000'],
            ['FROM MAY 2000', 'from Май 2000'],
            ['AFT MAY 2000', 'after Май 2000'],
            ['BEF MAY 2000', 'before Май 2000'],
            ['15 JUN 2000', '15 Июнь 2000'],
            ['JUN 2000', 'Июнь 2000'],
            ['ABT JUN 2000', 'about Июнь 2000'],
            ['FROM JUN 2000', 'from Июнь 2000'],
            ['AFT JUN 2000', 'after Июнь 2000'],
            ['BEF JUN 2000', 'before Июнь 2000'],
            ['15 JUL 2000', '15 Июль 2000'],
            ['JUL 2000', 'Июль 2000'],
            ['ABT JUL 2000', 'about Июль 2000'],
            ['FROM JUL 2000', 'from Июль 2000'],
            ['AFT JUL 2000', 'after Июль 2000'],
            ['BEF JUL 2000', 'before Июль 2000'],
            ['15 AUG 2000', '15 Август 2000'],
            ['AUG 2000', 'Август 2000'],
            ['ABT AUG 2000', 'about Август 2000'],
            ['FROM AUG 2000', 'from Август 2000'],
            ['AFT AUG 2000', 'after Август 2000'],
            ['BEF AUG 2000', 'before Август 2000'],
            ['15 SEP 2000', '15 Сентябрь 2000'],
            ['SEP 2000', 'Сентябрь 2000'],
            ['ABT SEP 2000', 'about Сентябрь 2000'],
            ['FROM SEP 2000', 'from Сентябрь 2000'],
            ['AFT SEP 2000', 'after Сентябрь 2000'],
            ['BEF SEP 2000', 'before Сентябрь 2000'],
            ['15 OCT 2000', '15 Октябрь 2000'],
            ['OCT 2000', 'Октябрь 2000'],
            ['ABT OCT 2000', 'about Октябрь 2000'],
            ['FROM OCT 2000', 'from Октябрь 2000'],
            ['AFT OCT 2000', 'after Октябрь 2000'],
            ['BEF OCT 2000', 'before Октябрь 2000'],
            ['15 NOV 2000', '15 Ноябрь 2000'],
            ['NOV 2000', 'Ноябрь 2000'],
            ['ABT NOV 2000', 'about Ноябрь 2000'],
            ['FROM NOV 2000', 'from Ноябрь 2000'],
            ['AFT NOV 2000', 'after Ноябрь 2000'],
            ['BEF NOV 2000', 'before Ноябрь 2000'],
            ['15 DEC 2000', '15 Декабрь 2000'],
            ['DEC 2000', 'Декабрь 2000'],
            ['ABT DEC 2000', 'about Декабрь 2000'],
            ['FROM DEC 2000', 'from Декабрь 2000'],
            ['AFT DEC 2000', 'after Декабрь 2000'],
            ['BEF DEC 2000', 'before Декабрь 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'about 15 Гыйнвар 2000'],
            ['CAL 15 JAN 2000', 'calculated 15 Гыйнвар 2000'],
            ['EST 15 JAN 2000', 'estimated 15 Гыйнвар 2000'],
            ['BEF 15 JAN 2000', 'before 15 Гыйнвар 2000'],
            ['AFT 15 JAN 2000', 'after 15 Гыйнвар 2000'],
            ['FROM 15 JAN 2000', 'from 15 Гыйнвар 2000'],
            ['TO 15 JAN 2000', 'to 15 Гыйнвар 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between 15 Гыйнвар 2000 and 15 Февраль 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from 15 Гыйнвар 2000 to 15 Февраль 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 Гыйнвар 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Гыйнвар 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Гыйнвар 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about Гыйнвар 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from Гыйнвар 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after Гыйнвар 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before Гыйнвар 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Февраль 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Февраль 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about Февраль 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from Февраль 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after Февраль 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before Февраль 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Март 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Март 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about Март 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from Март 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after Март 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before Март 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 Апрель 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Апрель 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'Апрель 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about Апрель 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from Апрель 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after Апрель 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before Апрель 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Май 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Май 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about Май 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from Май 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after Май 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before Май 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Июнь 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Июнь 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about Июнь 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from Июнь 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after Июнь 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before Июнь 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Июль 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Июль 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about Июль 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from Июль 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after Июль 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before Июль 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Август 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Август 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about Август 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from Август 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after Август 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before Август 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Сентябрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Сентябрь 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about Сентябрь 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from Сентябрь 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after Сентябрь 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before Сентябрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Октябрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Октябрь 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about Октябрь 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from Октябрь 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after Октябрь 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before Октябрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Ноябрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Ноябрь 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about Ноябрь 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from Ноябрь 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after Ноябрь 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before Ноябрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Декабрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Декабрь 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about Декабрь 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from Декабрь 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after Декабрь 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before Декабрь 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about 15 Гыйнвар 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15 Гыйнвар 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 Гыйнвар 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before 15 Гыйнвар 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after 15 Гыйнвар 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from 15 Гыйнвар 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15 Гыйнвар 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between 15 Гыйнвар 1700 ᴄᴇ and 15 Февраль 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from 15 Гыйнвар 1700 ᴄᴇ to 15 Февраль 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 Гыйнвар 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Тишрей 5765'],
            ['@#DHEBREW@ TSH 5765', 'Тишрей 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'about Тишрей 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'from Тишрей 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'after Тишрей 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'before Тишрей 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Хешван 5765'],
            ['@#DHEBREW@ CSH 5765', 'Хешван 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'about Хешван 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'from Хешван 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'after Хешван 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'before Хешван 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Кислев 5765'],
            ['@#DHEBREW@ KSL 5765', 'Кислев 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'about Кислев 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'from Кислев 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'after Кислев 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'before Кислев 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Тевет 5765'],
            ['@#DHEBREW@ TVT 5765', 'Тевет 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'about Тевет 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'from Тевет 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'after Тевет 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'before Тевет 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Шват 5765'],
            ['@#DHEBREW@ SHV 5765', 'Шват 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'about Шват 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'from Шват 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'after Шват 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'before Шват 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Адар I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Адар I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'about Адар I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'from Адар I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'after Адар I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'before Адар I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Адар II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Адар II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'about Адар II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'from Адар II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'after Адар II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'before Адар II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Нисан 5765'],
            ['@#DHEBREW@ NSN 5765', 'Нисан 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'about Нисан 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'from Нисан 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'after Нисан 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'before Нисан 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Ияр 5765'],
            ['@#DHEBREW@ IYR 5765', 'Ияр 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'about Ияр 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'from Ияр 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'after Ияр 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'before Ияр 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Сиван 5765'],
            ['@#DHEBREW@ SVN 5765', 'Сиван 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'about Сиван 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'from Сиван 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'after Сиван 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'before Сиван 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Тамуз 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Тамуз 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about Тамуз 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from Тамуз 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after Тамуз 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before Тамуз 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Ав 5765'],
            ['@#DHEBREW@ AAV 5765', 'Ав 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'about Ав 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'from Ав 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'after Ав 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'before Ав 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Элул 5765'],
            ['@#DHEBREW@ ELL 5765', 'Элул 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'about Элул 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'from Элул 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'after Элул 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'before Элул 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about 15 Тишрей 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15 Тишрей 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 Тишрей 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before 15 Тишрей 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after 15 Тишрей 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from 15 Тишрей 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15 Тишрей 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between 15 Тишрей 5765 and 15 Хешван 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from 15 Тишрей 5765 to 15 Хешван 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 Тишрей 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Вандемьер An XII'],
            ['@#DFRENCH R@ VEND 12', 'Вандемьер An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about Вандемьер An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from Вандемьер An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after Вандемьер An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before Вандемьер An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Брюмер An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Брюмер An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about Брюмер An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from Брюмер An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after Брюмер An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before Брюмер An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Фример An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Фример An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about Фример An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from Фример An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after Фример An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before Фример An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Нивоз An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Нивоз An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about Нивоз An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from Нивоз An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after Нивоз An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before Нивоз An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Плювиоз An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Плювиоз An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about Плювиоз An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from Плювиоз An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after Плювиоз An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before Плювиоз An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Вантоз An XII'],
            ['@#DFRENCH R@ VENT 12', 'Вантоз An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about Вантоз An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from Вантоз An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after Вантоз An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before Вантоз An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Жерминаль An XII'],
            ['@#DFRENCH R@ GERM 12', 'Жерминаль An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about Жерминаль An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from Жерминаль An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after Жерминаль An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before Жерминаль An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Флореаль An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Флореаль An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about Флореаль An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from Флореаль An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after Флореаль An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before Флореаль An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Прериаль An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Прериаль An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about Прериаль An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from Прериаль An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after Прериаль An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before Прериаль An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Мессидор An XII'],
            ['@#DFRENCH R@ MESS 12', 'Мессидор An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about Мессидор An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from Мессидор An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after Мессидор An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before Мессидор An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Термидор An XII'],
            ['@#DFRENCH R@ THER 12', 'Термидор An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about Термидор An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from Термидор An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after Термидор An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before Термидор An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Фрюктидор An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Фрюктидор An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about Фрюктидор An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from Фрюктидор An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after Фрюктидор An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before Фрюктидор An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 өстәмә көннәр An XII'],
            ['@#DFRENCH R@ COMP 12', 'өстәмә көннәр An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about өстәмә көннәр An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from өстәмә көннәр An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after өстәмә көннәр An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before өстәмә көннәр An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about 15 Вандемьер An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15 Вандемьер An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 Вандемьер An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before 15 Вандемьер An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after 15 Вандемьер An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from 15 Вандемьер An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15 Вандемьер An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between 15 Вандемьер An XII and 15 Брюмер An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from 15 Вандемьер An XII to 15 Брюмер An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 Вандемьер An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Мөхәррәм 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Мөхәррәм 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about Мөхәррәм 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from Мөхәррәм 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after Мөхәррәм 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before Мөхәррәм 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Сәфәр 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Сәфәр 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about Сәфәр 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from Сәфәр 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after Сәфәр 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before Сәфәр 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Рабигыл-әүвәл 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Рабигыл-әүвәл 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about Рабигыл-әүвәл 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Рабигыл-әүвәл 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after Рабигыл-әүвәл 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before Рабигыл-әүвәл 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Рабигыл-ахир 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Рабигыл-ахир 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about Рабигыл-ахир 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Рабигыл-ахир 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after Рабигыл-ахир 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before Рабигыл-ахир 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Жәмадил-әүвәл 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Жәмадил-әүвәл 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about Жәмадил-әүвәл 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from Жәмадил-әүвәл 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after Жәмадил-әүвәл 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before Жәмадил-әүвәл 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Жәмадил-ахир 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Жәмадил-ахир 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about Жәмадил-ахир 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from Жәмадил-ахир 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after Жәмадил-ахир 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before Жәмадил-ахир 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Рәҗәп 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Рәҗәп 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about Рәҗәп 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from Рәҗәп 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after Рәҗәп 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before Рәҗәп 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Шәгъбан 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Шәгъбан 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about Шәгъбан 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Шәгъбан 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after Шәгъбан 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before Шәгъбан 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Рамазан 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Рамазан 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about Рамазан 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Рамазан 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after Рамазан 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before Рамазан 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Шәүвәл 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Шәүвәл 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about Шәүвәл 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Шәүвәл 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after Шәүвәл 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before Шәүвәл 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Зөлкагъдә 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Зөлкагъдә 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about Зөлкагъдә 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Зөлкагъдә 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after Зөлкагъдә 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before Зөлкагъдә 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about 15 Мөхәррәм 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated 15 Мөхәррәм 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15 Мөхәррәм 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before 15 Мөхәррәм 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after 15 Мөхәррәм 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from 15 Мөхәррәм 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to 15 Мөхәррәм 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between 15 Мөхәррәм 1425 and 15 Сәфәр 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from 15 Мөхәррәм 1425 to 15 Сәфәр 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15 Мөхәррәм 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Фарвардин 1384'],
            ['@#DJALALI@ FARVA 1384', 'Фарвардин 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'about Фарвардин 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'from Фарвардин 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'after Фарвардин 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'before Фарвардин 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ордибехешт 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ордибехешт 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about Ордибехешт 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from Ордибехешт 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after Ордибехешт 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before Ордибехешт 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Хордад 1384'],
            ['@#DJALALI@ KHORD 1384', 'Хордад 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'about Хордад 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'from Хордад 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'after Хордад 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'before Хордад 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Тир 1384'],
            ['@#DJALALI@ TIR 1384', 'Тир 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'about Тир 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'from Тир 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'after Тир 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'before Тир 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Мордад 1384'],
            ['@#DJALALI@ MORDA 1384', 'Мордад 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'about Мордад 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'from Мордад 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'after Мордад 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'before Мордад 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Шахривар 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Шахривар 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about Шахривар 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from Шахривар 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after Шахривар 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before Шахривар 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Мехр 1384'],
            ['@#DJALALI@ MEHR 1384', 'Мехр 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'about Мехр 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'from Мехр 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'after Мехр 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'before Мехр 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Абан 1384'],
            ['@#DJALALI@ ABAN 1384', 'Абан 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'about Абан 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'from Абан 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'after Абан 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'before Абан 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Азар 1384'],
            ['@#DJALALI@ AZAR 1384', 'Азар 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'about Азар 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'from Азар 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'after Азар 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'before Азар 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Дей 1384'],
            ['@#DJALALI@ DEY 1384', 'Дей 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'about Дей 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'from Дей 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'after Дей 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'before Дей 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Бахман 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Бахман 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about Бахман 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from Бахман 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after Бахман 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before Бахман 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Эсфанд 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Эсфанд 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about Эсфанд 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from Эсфанд 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after Эсфанд 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before Эсфанд 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about 15 Фарвардин 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated 15 Фарвардин 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15 Фарвардин 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before 15 Фарвардин 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after 15 Фарвардин 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from 15 Фарвардин 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to 15 Фарвардин 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between 15 Фарвардин 1384 and 15 Ордибехешт 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from 15 Фарвардин 1384 to 15 Ордибехешт 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15 Фарвардин 1384'],
        ];
    }
}
