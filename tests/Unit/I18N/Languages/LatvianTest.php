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
use Fisharebest\Webtrees\I18N\Languages\Latvian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Latvian::class)]
class LatvianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Latvian();
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
        self::assertSame('lv', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('latviešu', self::language()->endonym());
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
        self::assertSame('one un two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two un three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one vai two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two vai three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMS @fad@\n1 FAMS @ffo@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMS @fad@\n1 FAMS @ffo@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@");
        $daughter = self::female('d', "1 FAMC @fm@");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@\n1 FAMS @fbro@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@\n1 FAMS @fsis@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $cousinFemale = self::female('cf', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");
        $adoptedSon = self::male('as', "1 FAMC @fad@\n2 PEDI adopted");
        $adoptedDaughter = self::female('ad', "1 FAMC @fad@\n2 PEDI adopted");
        $fosterSon = self::male('fs', "1 FAMC @ffo@\n2 PEDI foster");
        $sisterHusband = self::male('shusb', "1 FAMS @fsis@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cm@\n1 CHIL @cf@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 MARR Y\n1 HUSB @shusb@\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");
        $fad = self::family('fad', "0 @fad@ FAM\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @ad@");
        $ffo = self::family('ffo', "0 @ffo@ FAM\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @fs@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinMale, $cousinFemale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance,
             $adoptedSon, $adoptedDaughter, $fosterSon, $sisterHusband],
            [$fm, $fd, $fp, $fw, $fbro, $fsis, $fgp, $fe, $fad, $ffo]
        );

        // Partners
        self::assertRelationshipNames('sieva', 'vīrs', [$husband, $fm, $wife]);
        self::assertRelationshipNames('bijušais vīrs', 'bijusī sieva', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('līgava', 'līgavainis', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('māte', 'dēls', [$son, $fm, $wife]);
        self::assertRelationshipNames('tēvs', 'dēls', [$son, $fm, $husband]);
        self::assertRelationshipNames('māte', 'meita', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipName('māsa', [$son, $fm, $daughter]);
        self::assertRelationshipName('brālis', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('pusbrālis', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('patēvs', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('pameita', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // Adopted
        self::assertRelationshipName('adoptētais dēls', [$husband, $fad, $adoptedSon]);
        self::assertRelationshipName('adoptētā meita', [$husband, $fad, $adoptedDaughter]);
        self::assertRelationshipName('adoptētājs tēvs', [$adoptedSon, $fad, $husband]);
        self::assertRelationshipName('adoptētāja māte', [$adoptedSon, $fad, $wife]);

        // Foster
        self::assertRelationshipName('audžudēls', [$husband, $ffo, $fosterSon]);
        self::assertRelationshipName('audžutēvs', [$fosterSon, $ffo, $husband]);
        self::assertRelationshipName('audžumāte', [$fosterSon, $ffo, $wife]);

        // In-laws — wife's parents
        self::assertRelationshipName('sievasmāte', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('sievastēvs', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // In-laws — husband's parents
        self::assertRelationshipName('vīramāte', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('vīratēvs', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // Children-in-law
        self::assertRelationshipName('vedekla', [$fatherOfH, $fp, $husband, $fm, $wife]);
        self::assertRelationshipName('znots', [$motherOfW, $fw, $wife, $fm, $husband]);

        // Siblings-in-law
        self::assertRelationshipName('svaine', [$wife, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('svainis', [$wife, $fm, $husband, $fp, $brotherOfH]);
        // Grandparents
        self::assertRelationshipNames('vecāmāte', 'mazdēls', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('vectēvs', 'mazdēls', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic — vec- prefix)
        self::assertRelationshipName('vecvecāmāte', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('vecvectēvs', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);

        // Aunts and uncles
        self::assertRelationshipNames('tante', 'brāļadēls', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('tēvocis', 'brāļadēls', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews (from brother)
        self::assertRelationshipName('brāļameita', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('brāļadēls', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Nieces and nephews (from sister)
        self::assertRelationshipName('māsasmeita', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('māsasdēls', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Siblings-in-law (sibling's spouses)
        self::assertRelationshipName('māsasvīrs', [$husband, $fp, $sisterOfH, $fsis, $sisterHusband]);

        // Cousins
        self::assertRelationshipName('brālēns', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
        self::assertRelationshipName('māsīca', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);

        // Great-aunt/uncle (dynamic — vec- prefix)
        self::assertRelationshipName('vectante', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('vectēvocis', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 janvāra 2000'],
            ['JAN 2000', 'janvāris 2000'],
            ['ABT JAN 2000', 'about janvāra 2000'],
            ['FROM JAN 2000', 'from janvāra 2000'],
            ['AFT JAN 2000', 'after janvārī 2000'],
            ['BEF JAN 2000', 'before janvāri 2000'],
            ['15 FEB 2000', '15 februāra 2000'],
            ['FEB 2000', 'februāris 2000'],
            ['ABT FEB 2000', 'about februāra 2000'],
            ['FROM FEB 2000', 'from februāra 2000'],
            ['AFT FEB 2000', 'after februārī 2000'],
            ['BEF FEB 2000', 'before februāri 2000'],
            ['15 MAR 2000', '15 marta 2000'],
            ['MAR 2000', 'marts 2000'],
            ['ABT MAR 2000', 'about marta 2000'],
            ['FROM MAR 2000', 'from marta 2000'],
            ['AFT MAR 2000', 'after martā 2000'],
            ['BEF MAR 2000', 'before martu 2000'],
            ['15 APR 2000', '15 aprīļa 2000'],
            ['APR 2000', 'aprīlis 2000'],
            ['ABT APR 2000', 'about aprīļa 2000'],
            ['FROM APR 2000', 'from aprīļa 2000'],
            ['AFT APR 2000', 'after aprīlī 2000'],
            ['BEF APR 2000', 'before aprīli 2000'],
            ['15 MAY 2000', '15 maija 2000'],
            ['MAY 2000', 'maijs 2000'],
            ['ABT MAY 2000', 'about maija 2000'],
            ['FROM MAY 2000', 'from maija 2000'],
            ['AFT MAY 2000', 'after maijā 2000'],
            ['BEF MAY 2000', 'before maiju 2000'],
            ['15 JUN 2000', '15 jūnija 2000'],
            ['JUN 2000', 'jūnijs 2000'],
            ['ABT JUN 2000', 'about jūnija 2000'],
            ['FROM JUN 2000', 'from jūnija 2000'],
            ['AFT JUN 2000', 'after jūnijā 2000'],
            ['BEF JUN 2000', 'before jūniju 2000'],
            ['15 JUL 2000', '15 jūlija 2000'],
            ['JUL 2000', 'jūlijs 2000'],
            ['ABT JUL 2000', 'about jūlija 2000'],
            ['FROM JUL 2000', 'from jūlija 2000'],
            ['AFT JUL 2000', 'after jūlijā 2000'],
            ['BEF JUL 2000', 'before jūliju 2000'],
            ['15 AUG 2000', '15 augusta 2000'],
            ['AUG 2000', 'augusts 2000'],
            ['ABT AUG 2000', 'about augusta 2000'],
            ['FROM AUG 2000', 'from augusta 2000'],
            ['AFT AUG 2000', 'after augustā 2000'],
            ['BEF AUG 2000', 'before augustu 2000'],
            ['15 SEP 2000', '15 septembra 2000'],
            ['SEP 2000', 'septembris 2000'],
            ['ABT SEP 2000', 'about septembra 2000'],
            ['FROM SEP 2000', 'from septembra 2000'],
            ['AFT SEP 2000', 'after septembrī 2000'],
            ['BEF SEP 2000', 'before septembri 2000'],
            ['15 OCT 2000', '15 oktobra 2000'],
            ['OCT 2000', 'oktobris 2000'],
            ['ABT OCT 2000', 'about oktobra 2000'],
            ['FROM OCT 2000', 'from oktobra 2000'],
            ['AFT OCT 2000', 'after oktobrī 2000'],
            ['BEF OCT 2000', 'before oktobri 2000'],
            ['15 NOV 2000', '15 novembra 2000'],
            ['NOV 2000', 'novembris 2000'],
            ['ABT NOV 2000', 'about novembra 2000'],
            ['FROM NOV 2000', 'from novembra 2000'],
            ['AFT NOV 2000', 'after novembrī 2000'],
            ['BEF NOV 2000', 'before novembri 2000'],
            ['15 DEC 2000', '15 decembra 2000'],
            ['DEC 2000', 'decembris 2000'],
            ['ABT DEC 2000', 'about decembra 2000'],
            ['FROM DEC 2000', 'from decembra 2000'],
            ['AFT DEC 2000', 'after decembrī 2000'],
            ['BEF DEC 2000', 'before decembri 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'about 15 janvāra 2000'],
            ['CAL 15 JAN 2000', 'calculated 15 janvāra 2000'],
            ['EST 15 JAN 2000', 'estimated 15 janvāra 2000'],
            ['BEF 15 JAN 2000', 'before 15 janvāra 2000'],
            ['AFT 15 JAN 2000', 'after 15 janvāra 2000'],
            ['FROM 15 JAN 2000', 'from 15 janvāra 2000'],
            ['TO 15 JAN 2000', 'to 15 janvāra 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between 15 janvāra 2000 and 15 februāra 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from 15 janvāra 2000 to 15 februāra 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 janvāra 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 janvāra 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'janvāris 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about janvāra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from janvāra 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after janvārī 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before janvāri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 februāra 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'februāris 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about februāra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from februāra 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after februārī 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before februāri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 marta 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'marts 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about marta 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from marta 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after martā 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before martu 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 aprīļa 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 aprīļa 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'aprīlis 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about aprīļa 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from aprīļa 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after aprīlī 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before aprīli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 maija 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'maijs 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about maija 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from maija 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after maijā 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before maiju 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 jūnija 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'jūnijs 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about jūnija 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from jūnija 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after jūnijā 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before jūniju 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 jūlija 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'jūlijs 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about jūlija 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from jūlija 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after jūlijā 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before jūliju 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 augusta 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'augusts 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about augusta 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from augusta 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after augustā 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before augustu 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 septembra 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'septembris 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about septembra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from septembra 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after septembrī 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before septembri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 oktobra 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'oktobris 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about oktobra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from oktobra 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after oktobrī 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before oktobri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 novembra 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'novembris 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about novembra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from novembra 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after novembrī 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before novembri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 decembra 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'decembris 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about decembra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from decembra 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after decembrī 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before decembri 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about 15 janvāra 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated 15 janvāra 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 janvāra 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before 15 janvāra 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after 15 janvāra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from 15 janvāra 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to 15 janvāra 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between 15 janvāra 1700 ᴄᴇ and 15 februāra 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from 15 janvāra 1700 ᴄᴇ to 15 februāra 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 janvāra 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'about Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'from Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'after Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'before Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'about Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'from Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'after Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'before Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'about Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'from Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'after Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'before Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'about Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'from Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'after Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'before Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'about Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'from Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'after Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'before Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'about Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'from Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'after Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'before Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'about Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'from Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'after Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'before Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'about Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'from Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'after Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'before Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'about Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'from Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'after Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'before Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'about Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'from Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'after Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'before Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'about Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'from Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'after Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'before Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'about Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'from Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'after Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'before Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between 15 Tishrei 5765 and 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from 15 Tishrei 5765 to 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between 15 Vendémiaire An XII and 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from 15 Vendémiaire An XII to 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated 15 Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between 15 Muharram 1425 and 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from 15 Muharram 1425 to 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'about Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'from Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'after Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'before Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'about Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'from Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'after Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'before Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'about Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'from Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'after Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'before Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'about Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'from Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'after Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'before Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'about Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'from Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'after Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'before Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'about Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'from Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'after Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'before Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'about Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'from Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'after Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'before Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'about Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'from Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'after Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'before Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between 15 Farvardin 1384 and 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from 15 Farvardin 1384 to 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15 Farvardin 1384'],
        ];
    }
}
