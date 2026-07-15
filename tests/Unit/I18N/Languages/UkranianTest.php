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
use Fisharebest\Webtrees\I18N\Languages\Ukranian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Ukranian::class)]
class UkranianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Ukranian();
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
        self::assertSame('uk', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('українська', self::language()->endonym());
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
        self::assertSame('one та two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two та three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one або two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two або three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('дружина', 'чоловік', [$husband, $fm, $wife]);
        self::assertRelationshipNames('колишній чоловік', 'колишня дружина', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('наречений', 'наречена', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('мати', 'син', [$son, $fm, $wife]);
        self::assertRelationshipNames('батько', 'син', [$son, $fm, $husband]);
        self::assertRelationshipNames('мати', 'дочка', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('молодша сестра', 'старший брат', [$son, $fm, $daughter]);
        self::assertRelationshipNames('старший брат', 'молодша сестра', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('зведена сестра', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('вітчим', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('падчерка', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipName('теща', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('тесть', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('невістка', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('бабуся', 'внук', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('дідусь', 'внук', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('прадідусь', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('прабабуся', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('тітка', 'племінник', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('дядько', 'племінник', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('племінниця', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('племінник', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('двоюрідна сестра', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('двоюрідний брат', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Січня 2000'],
            ['JAN 2000', 'Січень 2000'],
            ['ABT JAN 2000', 'близько Січня 2000'],
            ['FROM JAN 2000', 'з Січня 2000'],
            ['AFT JAN 2000', 'після Січня 2000'],
            ['BEF JAN 2000', 'перед Січнем 2000'],
            ['15 FEB 2000', '15 Лютого 2000'],
            ['FEB 2000', 'Лютий 2000'],
            ['ABT FEB 2000', 'близько Лютого 2000'],
            ['FROM FEB 2000', 'з Лютого 2000'],
            ['AFT FEB 2000', 'після Лютого 2000'],
            ['BEF FEB 2000', 'перед Лютим 2000'],
            ['15 MAR 2000', '15 Березня 2000'],
            ['MAR 2000', 'Березень 2000'],
            ['ABT MAR 2000', 'близько Березня 2000'],
            ['FROM MAR 2000', 'з Березня 2000'],
            ['AFT MAR 2000', 'після Березня 2000'],
            ['BEF MAR 2000', 'перед Березнем 2000'],
            ['15 APR 2000', '15 Квітня 2000'],
            ['APR 2000', 'Квітень 2000'],
            ['ABT APR 2000', 'близько Квітня 2000'],
            ['FROM APR 2000', 'з Квітня 2000'],
            ['AFT APR 2000', 'після Квітня 2000'],
            ['BEF APR 2000', 'перед Квітнем 2000'],
            ['15 MAY 2000', '15 Травня 2000'],
            ['MAY 2000', 'Травень 2000'],
            ['ABT MAY 2000', 'близько Травня 2000'],
            ['FROM MAY 2000', 'з Травня 2000'],
            ['AFT MAY 2000', 'після Травня 2000'],
            ['BEF MAY 2000', 'перед Травнем 2000'],
            ['15 JUN 2000', '15 Червня 2000'],
            ['JUN 2000', 'Червень 2000'],
            ['ABT JUN 2000', 'близько Червня 2000'],
            ['FROM JUN 2000', 'з Червня 2000'],
            ['AFT JUN 2000', 'після Червня 2000'],
            ['BEF JUN 2000', 'перед Червнем 2000'],
            ['15 JUL 2000', '15 Липня 2000'],
            ['JUL 2000', 'Липень 2000'],
            ['ABT JUL 2000', 'близько Липня 2000'],
            ['FROM JUL 2000', 'з Липня 2000'],
            ['AFT JUL 2000', 'після Липня 2000'],
            ['BEF JUL 2000', 'перед Липнем 2000'],
            ['15 AUG 2000', '15 Серпня 2000'],
            ['AUG 2000', 'Серпень 2000'],
            ['ABT AUG 2000', 'близько Серпня 2000'],
            ['FROM AUG 2000', 'з Серпня 2000'],
            ['AFT AUG 2000', 'після Серпня 2000'],
            ['BEF AUG 2000', 'перед Серпнем 2000'],
            ['15 SEP 2000', '15 Вересня 2000'],
            ['SEP 2000', 'Вересень 2000'],
            ['ABT SEP 2000', 'близько Вересня 2000'],
            ['FROM SEP 2000', 'з Вересня 2000'],
            ['AFT SEP 2000', 'після Вересня 2000'],
            ['BEF SEP 2000', 'перед Вереснем 2000'],
            ['15 OCT 2000', '15 Жовтня 2000'],
            ['OCT 2000', 'Жовтень 2000'],
            ['ABT OCT 2000', 'близько Жовтня 2000'],
            ['FROM OCT 2000', 'з Жовтня 2000'],
            ['AFT OCT 2000', 'після Жовтня 2000'],
            ['BEF OCT 2000', 'перед Жовтнем 2000'],
            ['15 NOV 2000', '15 Листопада 2000'],
            ['NOV 2000', 'Листопад 2000'],
            ['ABT NOV 2000', 'близько Листопада 2000'],
            ['FROM NOV 2000', 'з Листопада 2000'],
            ['AFT NOV 2000', 'після Листопада 2000'],
            ['BEF NOV 2000', 'перед Листопадом 2000'],
            ['15 DEC 2000', '15 Грудня 2000'],
            ['DEC 2000', 'Грудень 2000'],
            ['ABT DEC 2000', 'близько Грудня 2000'],
            ['FROM DEC 2000', 'з Грудня 2000'],
            ['AFT DEC 2000', 'після Грудня 2000'],
            ['BEF DEC 2000', 'перед Груднем 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'близько 15 Січня 2000'],
            ['CAL 15 JAN 2000', 'обчислено 15 Січня 2000'],
            ['EST 15 JAN 2000', 'передбачувано 15 Січня 2000'],
            ['BEF 15 JAN 2000', 'перед 15 Січня 2000'],
            ['AFT 15 JAN 2000', 'після 15 Січня 2000'],
            ['FROM 15 JAN 2000', 'з 15 Січня 2000'],
            ['TO 15 JAN 2000', 'до 15 Січня 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'між 15 Січня 2000 та 15 Лютого 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'з 15 Січня 2000 до 15 Лютого 2000'],
            ['INT 15 JAN 2000', 'розпізнано як 15 Січня 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Січня 1700 н.е.'],
            ['@#DJULIAN@ JAN 1700', 'Січень 1700 н.е.'],
            ['ABT @#DJULIAN@ JAN 1700', 'близько Січня 1700 н.е.'],
            ['FROM @#DJULIAN@ JAN 1700', 'з Січня 1700 н.е.'],
            ['AFT @#DJULIAN@ JAN 1700', 'після Січня 1700 н.е.'],
            ['BEF @#DJULIAN@ JAN 1700', 'перед Січнем 1700 н.е.'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Лютого 1700 н.е.'],
            ['@#DJULIAN@ FEB 1700', 'Лютий 1700 н.е.'],
            ['ABT @#DJULIAN@ FEB 1700', 'близько Лютого 1700 н.е.'],
            ['FROM @#DJULIAN@ FEB 1700', 'з Лютого 1700 н.е.'],
            ['AFT @#DJULIAN@ FEB 1700', 'після Лютого 1700 н.е.'],
            ['BEF @#DJULIAN@ FEB 1700', 'перед Лютим 1700 н.е.'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Березня 1700 н.е.'],
            ['@#DJULIAN@ MAR 1700', 'Березень 1700 н.е.'],
            ['ABT @#DJULIAN@ MAR 1700', 'близько Березня 1700 н.е.'],
            ['FROM @#DJULIAN@ MAR 1700', 'з Березня 1700 н.е.'],
            ['AFT @#DJULIAN@ MAR 1700', 'після Березня 1700 н.е.'],
            ['BEF @#DJULIAN@ MAR 1700', 'перед Березнем 1700 н.е.'],
            ['@#DJULIAN@ 15 APR 1700', '15 Квітня 1700 н.е.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Квітня 1645/46 н.е.'],
            ['@#DJULIAN@ APR 1700', 'Квітень 1700 н.е.'],
            ['ABT @#DJULIAN@ APR 1700', 'близько Квітня 1700 н.е.'],
            ['FROM @#DJULIAN@ APR 1700', 'з Квітня 1700 н.е.'],
            ['AFT @#DJULIAN@ APR 1700', 'після Квітня 1700 н.е.'],
            ['BEF @#DJULIAN@ APR 1700', 'перед Квітнем 1700 н.е.'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Травня 1700 н.е.'],
            ['@#DJULIAN@ MAY 1700', 'Травень 1700 н.е.'],
            ['ABT @#DJULIAN@ MAY 1700', 'близько Травня 1700 н.е.'],
            ['FROM @#DJULIAN@ MAY 1700', 'з Травня 1700 н.е.'],
            ['AFT @#DJULIAN@ MAY 1700', 'після Травня 1700 н.е.'],
            ['BEF @#DJULIAN@ MAY 1700', 'перед Травнем 1700 н.е.'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Червня 1700 н.е.'],
            ['@#DJULIAN@ JUN 1700', 'Червень 1700 н.е.'],
            ['ABT @#DJULIAN@ JUN 1700', 'близько Червня 1700 н.е.'],
            ['FROM @#DJULIAN@ JUN 1700', 'з Червня 1700 н.е.'],
            ['AFT @#DJULIAN@ JUN 1700', 'після Червня 1700 н.е.'],
            ['BEF @#DJULIAN@ JUN 1700', 'перед Червнем 1700 н.е.'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Липня 1700 н.е.'],
            ['@#DJULIAN@ JUL 1700', 'Липень 1700 н.е.'],
            ['ABT @#DJULIAN@ JUL 1700', 'близько Липня 1700 н.е.'],
            ['FROM @#DJULIAN@ JUL 1700', 'з Липня 1700 н.е.'],
            ['AFT @#DJULIAN@ JUL 1700', 'після Липня 1700 н.е.'],
            ['BEF @#DJULIAN@ JUL 1700', 'перед Липнем 1700 н.е.'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Серпня 1700 н.е.'],
            ['@#DJULIAN@ AUG 1700', 'Серпень 1700 н.е.'],
            ['ABT @#DJULIAN@ AUG 1700', 'близько Серпня 1700 н.е.'],
            ['FROM @#DJULIAN@ AUG 1700', 'з Серпня 1700 н.е.'],
            ['AFT @#DJULIAN@ AUG 1700', 'після Серпня 1700 н.е.'],
            ['BEF @#DJULIAN@ AUG 1700', 'перед Серпнем 1700 н.е.'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Вересня 1700 н.е.'],
            ['@#DJULIAN@ SEP 1700', 'Вересень 1700 н.е.'],
            ['ABT @#DJULIAN@ SEP 1700', 'близько Вересня 1700 н.е.'],
            ['FROM @#DJULIAN@ SEP 1700', 'з Вересня 1700 н.е.'],
            ['AFT @#DJULIAN@ SEP 1700', 'після Вересня 1700 н.е.'],
            ['BEF @#DJULIAN@ SEP 1700', 'перед Вереснем 1700 н.е.'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Жовтня 1700 н.е.'],
            ['@#DJULIAN@ OCT 1700', 'Жовтень 1700 н.е.'],
            ['ABT @#DJULIAN@ OCT 1700', 'близько Жовтня 1700 н.е.'],
            ['FROM @#DJULIAN@ OCT 1700', 'з Жовтня 1700 н.е.'],
            ['AFT @#DJULIAN@ OCT 1700', 'після Жовтня 1700 н.е.'],
            ['BEF @#DJULIAN@ OCT 1700', 'перед Жовтнем 1700 н.е.'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Листопада 1700 н.е.'],
            ['@#DJULIAN@ NOV 1700', 'Листопад 1700 н.е.'],
            ['ABT @#DJULIAN@ NOV 1700', 'близько Листопада 1700 н.е.'],
            ['FROM @#DJULIAN@ NOV 1700', 'з Листопада 1700 н.е.'],
            ['AFT @#DJULIAN@ NOV 1700', 'після Листопада 1700 н.е.'],
            ['BEF @#DJULIAN@ NOV 1700', 'перед Листопадом 1700 н.е.'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Грудня 1700 н.е.'],
            ['@#DJULIAN@ DEC 1700', 'Грудень 1700 н.е.'],
            ['ABT @#DJULIAN@ DEC 1700', 'близько Грудня 1700 н.е.'],
            ['FROM @#DJULIAN@ DEC 1700', 'з Грудня 1700 н.е.'],
            ['AFT @#DJULIAN@ DEC 1700', 'після Грудня 1700 н.е.'],
            ['BEF @#DJULIAN@ DEC 1700', 'перед Груднем 1700 н.е.'],
            ['@#DJULIAN@ 1700', '1700 н.е.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'близько 15 Січня 1700 н.е.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'обчислено 15 Січня 1700 н.е.'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'передбачувано 15 Січня 1700 н.е.'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'перед 15 Січня 1700 н.е.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'після 15 Січня 1700 н.е.'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'з 15 Січня 1700 н.е.'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'до 15 Січня 1700 н.е.'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'між 15 Січня 1700 н.е. та 15 Лютого 1700 н.е.'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'з 15 Січня 1700 н.е. до 15 Лютого 1700 н.е.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'розпізнано як 15 Січня 1700 н.е.'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Тішрея 5765'],
            ['@#DHEBREW@ TSH 5765', 'Тішрей 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'близько Тішрея 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'з Тішрея 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'після Тішрея 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'перед Тишреем 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Хешвана 5765'],
            ['@#DHEBREW@ CSH 5765', 'Хешван 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'близько Хешвана 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'з Хешвана 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'після Хешвана 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'перед Хешваном 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Кіслева 5765'],
            ['@#DHEBREW@ KSL 5765', 'Кіслев 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'близько Кіслева 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'з Кіслева 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'після Кіслева 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'перед Кіслевом 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Тевета 5765'],
            ['@#DHEBREW@ TVT 5765', 'Тевет 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'близько Тевета 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'з Тевета 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'після Тевета 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'перед Теветом 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Швата 5765'],
            ['@#DHEBREW@ SHV 5765', 'Шват 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'близько Швата 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'з Швата 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'після Швата 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'перед Шватам 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Адара I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Адар I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'близько Адара I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'з Адара I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'після Адара I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'перед Адаром I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Адара II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Адар II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'близько Адара II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'з Адара II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'після Адара II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'перед Адаром II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Нісана 5765'],
            ['@#DHEBREW@ NSN 5765', 'Нісан 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'близько Нісана 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'з Нісана 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'після Нісана 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'перед Нісаном 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Іяра 5765'],
            ['@#DHEBREW@ IYR 5765', 'Іяр 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'близько Іяра 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'з Іяра 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'після Іяра 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'перед Іяром 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Сівана 5765'],
            ['@#DHEBREW@ SVN 5765', 'Сіван 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'близько Сівана 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'з Сівана 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'після Сівана 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'перед Сіваном 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Тамуза 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Тамуз 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'близько Тамуза 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'з Тамуза 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'після Тамуза 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'перед Тамузом 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Ава 5765'],
            ['@#DHEBREW@ AAV 5765', 'Ав 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'близько Ава 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'з Ава 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'після Ава 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'перед Авом 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Елула 5765'],
            ['@#DHEBREW@ ELL 5765', 'Елул 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'близько Елула 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'з Елула 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'після Елула 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'перед Елулом 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'близько 15 Тішрея 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'обчислено 15 Тішрея 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'передбачувано 15 Тішрея 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'перед 15 Тішрея 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'після 15 Тішрея 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'з 15 Тішрея 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'до 15 Тішрея 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'між 15 Тішрея 5765 та 15 Хешвана 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'з 15 Тішрея 5765 до 15 Хешвана 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'розпізнано як 15 Тішрея 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Вандем’єр An XII'],
            ['@#DFRENCH R@ VEND 12', 'Вандем’єр An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'близько Вандем’єр An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'з Вандем’єр An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'після Вандем’єр An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'перед Вандем’єр An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Брюмер An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Брюмер An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'близько Брюмер An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'з Брюмер An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'після Брюмере An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'перед Брюмером An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Фрімер An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Фрімер An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'близько Фрімер An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'з Фрімер An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'після Фрімере An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'перед Фрімером An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Нівоз An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Нівоз An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'близько Нівоз An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'з Нівоз An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'після Нівоз An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'перед Нівоз An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Плювіоз An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Плювіоз An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'близько Плювіоз An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'з Плювіоз An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'після Плювіоз An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'перед Плювіоз An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Вантоз An XII'],
            ['@#DFRENCH R@ VENT 12', 'Вантоз An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'близько Вантоз An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'з Вантоз An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'після Вантоз An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'перед Вантоз An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Жерміналя An XII'],
            ['@#DFRENCH R@ GERM 12', 'Жерміналь An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'близько Жерміналя An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'з Жерміналя An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'після Жермінале An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'перед Жерміналем An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Флореаль An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Флореаль An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'близько Флореаль An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'з Флореаль An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'після Флореаль An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'перед Флореаль An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Преріаля An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Преріаль An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'близько Преріаля An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'з Преріаля An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'після Преріале An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'перед Преріалем An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Мессідора An XII'],
            ['@#DFRENCH R@ MESS 12', 'Мессідор An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'близько Мессідора An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'з Мессідора An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'після Мессідоре An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'перед Мессідором An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Термідора An XII'],
            ['@#DFRENCH R@ THER 12', 'Термідор An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'близько Термідора An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'з Термідора An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'після Термідоре An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'перед Термідором An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Фрюктідора An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Фрюктідор An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'близько Фрюктідора An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'з Фрюктідора An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'після Фрюктідоре An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'перед Фрюктідором An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 додаткові дні An XII'],
            ['@#DFRENCH R@ COMP 12', 'додаткові дні An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'близько додаткові дні An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'з додаткові дні An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'після додаткові дні An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'перед додаткові дні An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'близько 15 Вандем’єр An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'обчислено 15 Вандем’єр An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'передбачувано 15 Вандем’єр An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'перед 15 Вандем’єр An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'після 15 Вандем’єр An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'з 15 Вандем’єр An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'до 15 Вандем’єр An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'між 15 Вандем’єр An XII та 15 Брюмер An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'з 15 Вандем’єр An XII до 15 Брюмер An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'розпізнано як 15 Вандем’єр An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Мухаррам 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Мухаррам 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'близько Мухаррам 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'з Мухаррам 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'після Мухаррам 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'перед Мухаррам 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Сафар 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Сафар 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'близько Сафар 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'з Сафар 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'після Сафар 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'перед Сафар 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Рабі аль-авваль 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Рабі аль-авваль 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'близько Рабі аль-авваль 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'з Рабі аль-авваль 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'після Рабі аль-авваль 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'перед Рабі аль-авваль 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Рабі ас-сані 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Рабі ас-сані 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'близько Рабі ас-сані 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'з Рабі ас-сані 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'після Рабі ас-сані 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'перед Рабі ас-сані 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Джумада аль-уля 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Джумада аль-уля 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'близько Джумада аль-уля 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'з Джумада аль-уля 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'після Джумада аль-уля 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'перед Джумада аль-уля 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Джумада ас-сани 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Джумада ас-сани 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'близько Джумада ас-сани 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'з Джумада ас-сани 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'після Джумада ас-сани 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'перед Джумада ас-сани 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Раджаб 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Раджаб 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'близько Раджаб 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'з Раджаб 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'після Раджаб 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'перед Раджаб 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Шаабан 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Шаабан 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'близько Шаабан 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'з Шаабан 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'після Шаабан 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'перед Шаабан 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Рамадан 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Рамадан 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'близько Рамадан 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'з Рамадан 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'після Рамадан 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'перед Рамадан 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Шавваль 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Шавваль 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'близько Шавваль 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'з Шавваль 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'після Шавваль 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'перед Шавваль 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Зулькада 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Зулькада 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'близько Зулькада 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'з Зулькада 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'після Зулькада 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'перед Зулькада 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'близько 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'з 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'після 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'перед 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'близько 15 Мухаррам 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'обчислено 15 Мухаррам 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'передбачувано 15 Мухаррам 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'перед 15 Мухаррам 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'після 15 Мухаррам 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'з 15 Мухаррам 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'до 15 Мухаррам 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'між 15 Мухаррам 1425 та 15 Сафар 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'з 15 Мухаррам 1425 до 15 Сафар 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'розпізнано як 15 Мухаррам 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Фарвардін 1384'],
            ['@#DJALALI@ FARVA 1384', 'Фарвардін 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'близько Фарвардін 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'з Фарвардін 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'після Фарвардіне 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'перед Фарвардіном 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ордібехешт 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ордібехешт 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'близько Ордібехешт 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'з Ордібехешт 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'після Ордібехеште 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'перед Ордібехештом 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Хордада 1384'],
            ['@#DJALALI@ KHORD 1384', 'Хордад 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'близько Хордада 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'з Хордада 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'після Хордаде 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'перед Хордадом 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Тира 1384'],
            ['@#DJALALI@ TIR 1384', 'Тир 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'близько Тира 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'з Тира 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'після Тире 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'перед Тиром 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Мордада 1384'],
            ['@#DJALALI@ MORDA 1384', 'Мордад 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'близько Мордада 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'з Мордада 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'після Мордаде 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'перед Мордадом 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Шахрівара 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Шахрівар 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'близько Шахрівара 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'з Шахрівара 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'після Шахріваре 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'перед Шахріваром 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Мехра 1384'],
            ['@#DJALALI@ MEHR 1384', 'Мехр 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'близько Мехра 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'з Мехра 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'після Мехре 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'перед Мехром 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Абана 1384'],
            ['@#DJALALI@ ABAN 1384', 'Абан 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'близько Абана 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'з Абана 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'після Абане 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'перед Абаном 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Азара 1384'],
            ['@#DJALALI@ AZAR 1384', 'Азар 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'близько Азара 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'з Азара 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'після Азаре 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'перед Азаром 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Дея 1384'],
            ['@#DJALALI@ DEY 1384', 'Дей 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'близько Дея 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'з Дея 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'після Дее 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'перед Деем 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Бахмана 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Бахман 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'близько Бахмана 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'з Бахмана 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'після Бахмане 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'перед Бахманом 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Есфанда 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Есфанд 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'близько Есфанда 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'з Есфанда 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'після Есфанде 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'перед Есфандом 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'близько 15 Фарвардін 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'обчислено 15 Фарвардін 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'передбачувано 15 Фарвардін 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'перед 15 Фарвардін 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'після 15 Фарвардін 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'з 15 Фарвардін 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'до 15 Фарвардін 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'між 15 Фарвардін 1384 та 15 Ордібехешт 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'з 15 Фарвардін 1384 до 15 Ордібехешт 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'розпізнано як 15 Фарвардін 1384'],
        ];
    }
}
