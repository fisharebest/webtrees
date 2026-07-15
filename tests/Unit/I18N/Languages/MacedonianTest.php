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
use Fisharebest\Webtrees\I18N\Languages\Macedonian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Macedonian::class)]
class MacedonianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Macedonian();
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
        self::assertSame(['А', 'Б', 'В', 'Г', 'Д', 'Ѓ', 'Е', 'Ж', 'З', 'Ѕ', 'И', 'Й', 'К', 'Л', 'Љ', 'М', 'Н', 'Њ', 'О', 'П', 'Р', 'С', 'Т', 'Ќ', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Џ', 'Ш'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('mk', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('македонски', self::language()->endonym());
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
        self::assertSame('-123.456,0789 %', self::language()->percentage(-1234.560789));
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
        self::assertRelationshipNames('сопруга', 'сопруг', [$husband, $fm, $wife]);
        self::assertRelationshipNames('поранешен сопруг', 'поранешна сопруга', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('свршеница', 'свршеник', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('мајка', 'син', [$son, $fm, $wife]);
        self::assertRelationshipNames('татко', 'син', [$son, $fm, $husband]);
        self::assertRelationshipNames('мајка', 'ќерка', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('посвоителка', 'посвоен син', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('посвоител', 'посвоен син', [$adoptedSon, $fd, $exHusband]);

        // Siblings (son born 2000 is older than daughter born 2001)
        self::assertRelationshipNames('помлада сестра', 'постар брат', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('полубрат', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('очув', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('паштерка', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('тешта', 'зет', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('тест', 'зет', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('снаа', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('баба', 'внук', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('дедо', 'внук', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) — пра prefix
        self::assertRelationshipName('прадедо', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('прабаба', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('тетка', 'нетјак', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('чичко', 'нетјак', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('нетјакиња', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('нетјак', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('братучетка', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('братучет', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) — пра prefix
        self::assertRelationshipName('пратетка', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('прачичко', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Januari 2000'],
            ['JAN 2000', 'Januari 2000'],
            ['ABT JAN 2000', 'okolu Januari 2000'],
            ['FROM JAN 2000', 'od Januari 2000'],
            ['AFT JAN 2000', 'posle Januari 2000'],
            ['BEF JAN 2000', 'pred Januari 2000'],
            ['15 FEB 2000', '15 Fevruari 2000'],
            ['FEB 2000', 'Fevruari 2000'],
            ['ABT FEB 2000', 'okolu Fevruari 2000'],
            ['FROM FEB 2000', 'od Fevruari 2000'],
            ['AFT FEB 2000', 'posle Fevruari 2000'],
            ['BEF FEB 2000', 'pred Fevruari 2000'],
            ['15 MAR 2000', '15 Mart 2000'],
            ['MAR 2000', 'Mart 2000'],
            ['ABT MAR 2000', 'okolu Mart 2000'],
            ['FROM MAR 2000', 'od Mart 2000'],
            ['AFT MAR 2000', 'posle Mart 2000'],
            ['BEF MAR 2000', 'pred Mart 2000'],
            ['15 APR 2000', '15 April 2000'],
            ['APR 2000', 'April 2000'],
            ['ABT APR 2000', 'okolu April 2000'],
            ['FROM APR 2000', 'od April 2000'],
            ['AFT APR 2000', 'posle April 2000'],
            ['BEF APR 2000', 'pred April 2000'],
            ['15 MAY 2000', '15 Maj 2000'],
            ['MAY 2000', 'Maj 2000'],
            ['ABT MAY 2000', 'okolu Maj 2000'],
            ['FROM MAY 2000', 'od Maj 2000'],
            ['AFT MAY 2000', 'posle Maj 2000'],
            ['BEF MAY 2000', 'pred Maj 2000'],
            ['15 JUN 2000', '15 Juni 2000'],
            ['JUN 2000', 'Juni 2000'],
            ['ABT JUN 2000', 'okolu Juni 2000'],
            ['FROM JUN 2000', 'od Juni 2000'],
            ['AFT JUN 2000', 'posle Juni 2000'],
            ['BEF JUN 2000', 'pred Juni 2000'],
            ['15 JUL 2000', '15 Juli 2000'],
            ['JUL 2000', 'Juli 2000'],
            ['ABT JUL 2000', 'okolu Juli 2000'],
            ['FROM JUL 2000', 'od Juli 2000'],
            ['AFT JUL 2000', 'posle Juli 2000'],
            ['BEF JUL 2000', 'pred Juli 2000'],
            ['15 AUG 2000', '15 Avgust 2000'],
            ['AUG 2000', 'Avgust 2000'],
            ['ABT AUG 2000', 'okolu Avgust 2000'],
            ['FROM AUG 2000', 'od Avgust 2000'],
            ['AFT AUG 2000', 'posle Avgust 2000'],
            ['BEF AUG 2000', 'pred Avgust 2000'],
            ['15 SEP 2000', '15 Septemvri 2000'],
            ['SEP 2000', 'Septemvri 2000'],
            ['ABT SEP 2000', 'okolu Septemvri 2000'],
            ['FROM SEP 2000', 'od Septemvri 2000'],
            ['AFT SEP 2000', 'posle Septemvri 2000'],
            ['BEF SEP 2000', 'pred Septemvri 2000'],
            ['15 OCT 2000', '15 Oktomvri 2000'],
            ['OCT 2000', 'Oktomvri 2000'],
            ['ABT OCT 2000', 'okolu Oktomvri 2000'],
            ['FROM OCT 2000', 'od Oktomvri 2000'],
            ['AFT OCT 2000', 'posle Oktomvri 2000'],
            ['BEF OCT 2000', 'pred Oktomvri 2000'],
            ['15 NOV 2000', '15 Noemvri 2000'],
            ['NOV 2000', 'Noemvri 2000'],
            ['ABT NOV 2000', 'okolu Noemvri 2000'],
            ['FROM NOV 2000', 'od Noemvri 2000'],
            ['AFT NOV 2000', 'posle Noemvri 2000'],
            ['BEF NOV 2000', 'pred Noemvri 2000'],
            ['15 DEC 2000', '15 Dekemvri 2000'],
            ['DEC 2000', 'Dekemvri 2000'],
            ['ABT DEC 2000', 'okolu Dekemvri 2000'],
            ['FROM DEC 2000', 'od Dekemvri 2000'],
            ['AFT DEC 2000', 'posle Dekemvri 2000'],
            ['BEF DEC 2000', 'pred Dekemvri 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'okolu 15 Januari 2000'],
            ['CAL 15 JAN 2000', 'presmetano 15 Januari 2000'],
            ['EST 15 JAN 2000', 'proceneto 15 Januari 2000'],
            ['BEF 15 JAN 2000', 'pred 15 Januari 2000'],
            ['AFT 15 JAN 2000', 'posle 15 Januari 2000'],
            ['FROM 15 JAN 2000', 'od 15 Januari 2000'],
            ['TO 15 JAN 2000', 'do 15 Januari 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'pomegju 15 Januari 2000 i 15 Fevruari 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'od 15 Januari 2000 do 15 Fevruari 2000'],
            ['INT 15 JAN 2000', 'protolkuvani 15 Januari 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Januari 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Januari 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'okolu Januari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'od Januari 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'posle Januari 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'pred Januari 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Fevruari 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Fevruari 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'okolu Fevruari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'od Fevruari 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'posle Fevruari 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'pred Fevruari 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Mart 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Mart 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'okolu Mart 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'od Mart 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'posle Mart 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'pred Mart 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 April 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 April 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'April 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'okolu April 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'od April 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'posle April 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'pred April 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Maj 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Maj 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'okolu Maj 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'od Maj 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'posle Maj 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'pred Maj 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Juni 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Juni 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'okolu Juni 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'od Juni 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'posle Juni 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'pred Juni 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Juli 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Juli 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'okolu Juli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'od Juli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'posle Juli 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'pred Juli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Avgust 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Avgust 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'okolu Avgust 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'od Avgust 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'posle Avgust 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'pred Avgust 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Septemvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Septemvri 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'okolu Septemvri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'od Septemvri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'posle Septemvri 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'pred Septemvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Oktomvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Oktomvri 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'okolu Oktomvri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'od Oktomvri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'posle Oktomvri 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'pred Oktomvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Noemvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Noemvri 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'okolu Noemvri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'od Noemvri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'posle Noemvri 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'pred Noemvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Dekemvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Dekemvri 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'okolu Dekemvri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'od Dekemvri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'posle Dekemvri 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'pred Dekemvri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'okolu 15 Januari 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'presmetano 15 Januari 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'proceneto 15 Januari 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'pred 15 Januari 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'posle 15 Januari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'od 15 Januari 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'do 15 Januari 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'pomegju 15 Januari 1700 ᴄᴇ i 15 Fevruari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'od 15 Januari 1700 ᴄᴇ do 15 Fevruari 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'protolkuvani 15 Januari 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'okolu Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'od Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'posle Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'pred Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'okolu Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'od Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'posle Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'pred Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'okolu Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'od Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'posle Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'pred Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'okolu Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'od Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'posle Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'pred Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'okolu Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'od Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'posle Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'pred Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'okolu Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'od Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'posle Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'pred Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'okolu Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'od Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'posle Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'pred Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'okolu Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'od Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'posle Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'pred Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'okolu Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'od Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'posle Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'pred Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'okolu Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'od Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'posle Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'pred Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'okolu Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'od Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'posle Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'pred Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'okolu Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'od Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'posle Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'pred Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'okolu Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'od Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'posle Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'pred Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'okolu 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'presmetano 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'proceneto 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'pred 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'posle 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'od 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'do 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'pomegju 15 Tishrei 5765 i 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'od 15 Tishrei 5765 do 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'protolkuvani 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendmiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendmiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'okolu Vendmiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'od Vendmiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'posle Vendmiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'pred Vendmiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumer An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumer An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'okolu Brumer An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'od Brumer An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'posle Brumer An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'pred Brumer An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimer An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimer An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'okolu Frimer An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'od Frimer An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'posle Frimer An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'pred Frimer An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'okolu Nivse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'od Nivse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'posle Nivse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'pred Nivse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluvise An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluvise An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'okolu Pluvise An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'od Pluvise An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'posle Pluvise An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'pred Pluvise An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'okolu Ventse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'od Ventse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'posle Ventse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'pred Ventse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinalen An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinalen An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'okolu Germinalen An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'od Germinalen An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'posle Germinalen An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'pred Germinalen An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floral An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floral An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'okolu Floral An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'od Floral An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'posle Floral An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'pred Floral An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairalen An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairalen An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'okolu Prairalen An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'od Prairalen An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'posle Prairalen An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'pred Prairalen An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidoren An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidoren An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'okolu Messidoren An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'od Messidoren An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'posle Messidoren An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'pred Messidoren An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidoren An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidoren An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'okolu Thermidoren An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'od Thermidoren An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'posle Thermidoren An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'pred Thermidoren An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fuctidoren An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fuctidoren An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'okolu Fuctidoren An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'od Fuctidoren An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'posle Fuctidoren An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'pred Fuctidoren An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complmentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complmentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'okolu jours complmentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'od jours complmentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'posle jours complmentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'pred jours complmentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'okolu 15 Vendmiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'presmetano 15 Vendmiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'proceneto 15 Vendmiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'pred 15 Vendmiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'posle 15 Vendmiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'od 15 Vendmiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'do 15 Vendmiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'pomegju 15 Vendmiaire An XII i 15 Brumer An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'od 15 Vendmiaire An XII do 15 Brumer An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'protolkuvani 15 Vendmiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Мухарем 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Мухарем 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'okolu Мухарем 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'od Мухарем 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'posle Мухарем 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'pred Мухарем 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Сафар 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Сафар 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'okolu Сафар 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'od Сафар 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'posle Сафар 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'pred Сафар 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Реби ул-евел 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Реби ул-евел 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'okolu Реби ул-евел 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'od Реби ул-евел 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'posle Реби ул-евел 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'pred Реби ул-евел 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Реби ул-ахир 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Реби ул-ахир 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'okolu Реби ул-ахир 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'od Реби ул-ахир 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'posle Реби ул-ахир 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'pred Реби ул-ахир 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Џумадел-ула 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Џумадел-ула 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'okolu Џумадел-ула 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'od Џумадел-ула 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'posle Џумадел-ула 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'pred Џумадел-ула 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Џумадел-ахира 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Џумадел-ахира 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'okolu Џумадел-ахира 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'od Џумадел-ахира 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'posle Џумадел-ахира 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'pred Џумадел-ахира 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Реџеб 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Реџеб 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'okolu Реџеб 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'od Реџеб 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'posle Реџеб 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'pred Реџеб 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Шабан 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Шабан 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'okolu Шабан 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'od Шабан 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'posle Шабан 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'pred Шабан 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Рамазан 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Рамазан 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'okolu Рамазан 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'od Рамазан 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'posle Рамазан 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'pred Рамазан 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Шевал 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Шевал 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'okolu Шевал 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'od Шевал 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'posle Шевал 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'pred Шевал 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Зулкаде 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Зулкаде 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'okolu Зулкаде 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'od Зулкаде 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'posle Зулкаде 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'pred Зулкаде 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'okolu 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'od 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'posle 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'pred 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'okolu 15 Мухарем 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'presmetano 15 Мухарем 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'proceneto 15 Мухарем 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'pred 15 Мухарем 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'posle 15 Мухарем 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'od 15 Мухарем 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'do 15 Мухарем 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'pomegju 15 Мухарем 1425 i 15 Сафар 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'od 15 Мухарем 1425 do 15 Сафар 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'protolkuvani 15 Мухарем 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'okolu Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'od Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'posle Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'pred Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'okolu Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'od Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'posle Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'pred Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'okolu Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'od Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'posle Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'pred Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'okolu Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'od Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'posle Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'pred Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'okolu Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'od Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'posle Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'pred Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'okolu Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'od Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'posle Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'pred Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'okolu Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'od Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'posle Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'pred Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'okolu Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'od Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'posle Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'pred Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'okolu Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'od Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'posle Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'pred Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'okolu Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'od Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'posle Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'pred Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'okolu Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'od Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'posle Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'pred Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'okolu Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'od Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'posle Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'pred Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'okolu 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'presmetano 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'proceneto 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'pred 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'posle 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'od 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'do 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'pomegju 15 Farvardin 1384 i 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'od 15 Farvardin 1384 do 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'protolkuvani 15 Farvardin 1384'],
        ];
    }
}
