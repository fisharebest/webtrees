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
use Fisharebest\Webtrees\I18N\Languages\Estonian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Estonian::class)]
class EstonianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Estonian();
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
        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'Z', 'Ž', 'T', 'U', 'V', 'W', 'Õ', 'Ä', 'Ö', 'Ü', 'X', 'Y'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('et', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('eesti', self::language()->endonym());
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
        self::assertSame('−123 456,0789%', self::language()->percentage(-1234.560789));
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
        self::assertSame('one või two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two või three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@");
        $daughter = self::female('d', "1 FAMC @fm@");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@\n1 FAMS @fbro@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('naine', 'mees', [$husband, $fm, $wife]);
        self::assertRelationshipNames('endine mees', 'endine naine', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('kihlatu', 'kihlatu', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('ema', 'poeg', [$son, $fm, $wife]);
        self::assertRelationshipNames('isa', 'poeg', [$son, $fm, $husband]);
        self::assertRelationshipNames('ema', 'tütar', [$daughter, $fm, $wife]);

        // Half-siblings
        self::assertRelationshipName('poolvend', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('kasuisa', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('kasutütar', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('ämm', 'väimees', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('äi', 'väimees', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('minia', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Siblings-in-law
        self::assertRelationshipName('käli', [$wife, $fm, $husband, $fp, $sisterOfH]);

        // Grandparents
        self::assertRelationshipNames('vanaema', 'lapselaps', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('vanaisa', 'lapselaps', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic — vana prefix)
        self::assertRelationshipName('vanavanaisa', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('vanavanaema', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tädi', 'vennapoeg', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('onu', 'vennapoeg', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('vennatütar', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('vennapoeg', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('nõbu', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic — vana prefix)
        self::assertRelationshipName('vanatädi', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('vanaonu', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15. jaanuari 2000'],
            ['JAN 2000', 'jaanuar 2000'],
            ['ABT JAN 2000', 'umbes jaanuari 2000'],
            ['FROM JAN 2000', 'järgneva poolt jaanuari 2000'],
            ['AFT JAN 2000', 'pärast jaanuaris 2000'],
            ['BEF JAN 2000', 'enne jaanuar 2000'],
            ['15 FEB 2000', '15. veebruari 2000'],
            ['FEB 2000', 'veebruar 2000'],
            ['ABT FEB 2000', 'umbes veebruari 2000'],
            ['FROM FEB 2000', 'järgneva poolt veebruari 2000'],
            ['AFT FEB 2000', 'pärast veebruaris 2000'],
            ['BEF FEB 2000', 'enne veebruar 2000'],
            ['15 MAR 2000', '15. märtsi 2000'],
            ['MAR 2000', 'märts 2000'],
            ['ABT MAR 2000', 'umbes märtsi 2000'],
            ['FROM MAR 2000', 'järgneva poolt märtsi 2000'],
            ['AFT MAR 2000', 'pärast märtsis 2000'],
            ['BEF MAR 2000', 'enne märts 2000'],
            ['15 APR 2000', '15. aprilli 2000'],
            ['APR 2000', 'aprill 2000'],
            ['ABT APR 2000', 'umbes aprilli 2000'],
            ['FROM APR 2000', 'järgneva poolt aprilli 2000'],
            ['AFT APR 2000', 'pärast aprillis 2000'],
            ['BEF APR 2000', 'enne aprill 2000'],
            ['15 MAY 2000', '15. mai 2000'],
            ['MAY 2000', 'mai 2000'],
            ['ABT MAY 2000', 'umbes mai 2000'],
            ['FROM MAY 2000', 'järgneva poolt mai 2000'],
            ['AFT MAY 2000', 'pärast mais 2000'],
            ['BEF MAY 2000', 'enne mai 2000'],
            ['15 JUN 2000', '15. juuni 2000'],
            ['JUN 2000', 'juuni 2000'],
            ['ABT JUN 2000', 'umbes juuni 2000'],
            ['FROM JUN 2000', 'järgneva poolt juuni 2000'],
            ['AFT JUN 2000', 'pärast juunis 2000'],
            ['BEF JUN 2000', 'enne juuni 2000'],
            ['15 JUL 2000', '15. juuli 2000'],
            ['JUL 2000', 'juuli 2000'],
            ['ABT JUL 2000', 'umbes juuli 2000'],
            ['FROM JUL 2000', 'järgneva poolt juuli 2000'],
            ['AFT JUL 2000', 'pärast juulis 2000'],
            ['BEF JUL 2000', 'enne juuli 2000'],
            ['15 AUG 2000', '15. augusti 2000'],
            ['AUG 2000', 'august 2000'],
            ['ABT AUG 2000', 'umbes augusti 2000'],
            ['FROM AUG 2000', 'järgneva poolt augusti 2000'],
            ['AFT AUG 2000', 'pärast augustis 2000'],
            ['BEF AUG 2000', 'enne august 2000'],
            ['15 SEP 2000', '15. septembri 2000'],
            ['SEP 2000', 'september 2000'],
            ['ABT SEP 2000', 'umbes septembri 2000'],
            ['FROM SEP 2000', 'järgneva poolt septembri 2000'],
            ['AFT SEP 2000', 'pärast septembris 2000'],
            ['BEF SEP 2000', 'enne september 2000'],
            ['15 OCT 2000', '15. oktoobri 2000'],
            ['OCT 2000', 'oktoober 2000'],
            ['ABT OCT 2000', 'umbes oktoobri 2000'],
            ['FROM OCT 2000', 'järgneva poolt oktoobri 2000'],
            ['AFT OCT 2000', 'pärast oktoobris 2000'],
            ['BEF OCT 2000', 'enne oktoober 2000'],
            ['15 NOV 2000', '15. novembri 2000'],
            ['NOV 2000', 'november 2000'],
            ['ABT NOV 2000', 'umbes novembri 2000'],
            ['FROM NOV 2000', 'järgneva poolt novembri 2000'],
            ['AFT NOV 2000', 'pärast novembris 2000'],
            ['BEF NOV 2000', 'enne november 2000'],
            ['15 DEC 2000', '15. detsembri 2000'],
            ['DEC 2000', 'detsember 2000'],
            ['ABT DEC 2000', 'umbes detsembri 2000'],
            ['FROM DEC 2000', 'järgneva poolt detsembri 2000'],
            ['AFT DEC 2000', 'pärast detsembris 2000'],
            ['BEF DEC 2000', 'enne detsember 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'umbes 15. jaanuari 2000'],
            ['CAL 15 JAN 2000', 'arvutatud 15. jaanuari 2000'],
            ['EST 15 JAN 2000', 'arvestatavalt 15. jaanuari 2000'],
            ['BEF 15 JAN 2000', 'enne 15. jaanuari 2000'],
            ['AFT 15 JAN 2000', 'pärast 15. jaanuari 2000'],
            ['FROM 15 JAN 2000', 'järgneva poolt 15. jaanuari 2000'],
            ['TO 15 JAN 2000', '15. jaanuari 2000\'le'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'ajavahemikul 15. jaanuari 2000 ja 15. veebruari 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '15. jaanuari 2000-lt 15. veebruari 2000-le'],
            ['INT 15 JAN 2000', 'tõlgendatud 15. jaanuari 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. jaanuari 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'jaanuar 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'umbes jaanuari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'järgneva poolt jaanuari 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'pärast jaanuaris 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'enne jaanuar 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15. veebruari 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'veebruar 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'umbes veebruari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'järgneva poolt veebruari 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'pärast veebruaris 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'enne veebruar 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15. märtsi 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'märts 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'umbes märtsi 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'järgneva poolt märtsi 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'pärast märtsis 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'enne märts 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15. aprilli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. aprilli 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'aprill 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'umbes aprilli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'järgneva poolt aprilli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'pärast aprillis 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'enne aprill 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15. mai 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'mai 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'umbes mai 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'järgneva poolt mai 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'pärast mais 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'enne mai 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15. juuni 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'juuni 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'umbes juuni 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'järgneva poolt juuni 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'pärast juunis 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'enne juuni 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15. juuli 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'juuli 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'umbes juuli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'järgneva poolt juuli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'pärast juulis 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'enne juuli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15. augusti 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'august 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'umbes augusti 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'järgneva poolt augusti 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'pärast augustis 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'enne august 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15. septembri 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'september 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'umbes septembri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'järgneva poolt septembri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'pärast septembris 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'enne september 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15. oktoobri 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'oktoober 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'umbes oktoobri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'järgneva poolt oktoobri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'pärast oktoobris 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'enne oktoober 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15. novembri 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'november 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'umbes novembri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'järgneva poolt novembri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'pärast novembris 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'enne november 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15. detsembri 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'detsember 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'umbes detsembri 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'järgneva poolt detsembri 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'pärast detsembris 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'enne detsember 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'umbes 15. jaanuari 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'arvutatud 15. jaanuari 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'arvestatavalt 15. jaanuari 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'enne 15. jaanuari 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'pärast 15. jaanuari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'järgneva poolt 15. jaanuari 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', '15. jaanuari 1700 ᴄᴇ\'le'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'ajavahemikul 15. jaanuari 1700 ᴄᴇ ja 15. veebruari 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '15. jaanuari 1700 ᴄᴇ-lt 15. veebruari 1700 ᴄᴇ-le'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'tõlgendatud 15. jaanuari 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15. Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'umbes Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'järgneva poolt Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'pärast Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'enne Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'umbes Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'järgneva poolt Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'pärast Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'enne Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'umbes Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'järgneva poolt Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'pärast Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'enne Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'umbes Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'järgneva poolt Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'pärast Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'enne Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'umbes Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'järgneva poolt Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'pärast Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'enne Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'umbes Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'järgneva poolt Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'pärast Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'enne Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'umbes Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'järgneva poolt Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'pärast Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'enne Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'umbes Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'järgneva poolt Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'pärast Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'enne Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'umbes Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'järgneva poolt Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'pärast Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'enne Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'umbes Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'järgneva poolt Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'pärast Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'enne Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'umbes Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'järgneva poolt Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'pärast Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'enne Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'umbes Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'järgneva poolt Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'pärast Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'enne Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'umbes Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'järgneva poolt Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'pärast Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'enne Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'umbes 15. Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'arvutatud 15. Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'arvestatavalt 15. Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'enne 15. Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'pärast 15. Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'järgneva poolt 15. Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', '15. Tishrei 5765\'le'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'ajavahemikul 15. Tishrei 5765 ja 15. Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '15. Tishrei 5765-lt 15. Heshvan 5765-le'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'tõlgendatud 15. Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'umbes Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'järgneva poolt Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'pärast Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'enne Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'umbes Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'järgneva poolt Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'pärast Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'enne Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'umbes Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'järgneva poolt Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'pärast Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'enne Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'umbes Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'järgneva poolt Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'pärast Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'enne Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'umbes Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'järgneva poolt Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'pärast Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'enne Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'umbes Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'järgneva poolt Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'pärast Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'enne Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'umbes Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'järgneva poolt Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'pärast Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'enne Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'umbes Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'järgneva poolt Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'pärast Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'enne Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'umbes Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'järgneva poolt Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'pärast Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'enne Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'umbes Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'järgneva poolt Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'pärast Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'enne Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'umbes Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'järgneva poolt Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'pärast Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'enne Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'umbes Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'järgneva poolt Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'pärast Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'enne Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'umbes jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'järgneva poolt jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'pärast jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'enne jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'umbes 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'arvutatud 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'arvestatavalt 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'enne 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'pärast 15. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'järgneva poolt 15. Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII\'le'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'ajavahemikul 15. Vendémiaire An XII ja 15. Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '15. Vendémiaire An XII-lt 15. Brumaire An XII-le'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'tõlgendatud 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'umbes Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'järgneva poolt Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'pärast Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'enne Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'umbes Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'järgneva poolt Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'pärast Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'enne Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'umbes Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'järgneva poolt Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'pärast Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'enne Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'umbes Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'järgneva poolt Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'pärast Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'enne Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'umbes Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'järgneva poolt Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'pärast Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'enne Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'umbes Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'järgneva poolt Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'pärast Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'enne Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'umbes Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'järgneva poolt Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'pärast Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'enne Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'umbes Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'järgneva poolt Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'pärast Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'enne Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'umbes Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'järgneva poolt Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'pärast Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'enne Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'umbes Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'järgneva poolt Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'pärast Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'enne Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'umbes Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'järgneva poolt Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'pärast Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'enne Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'umbes 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'järgneva poolt 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'pärast 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'enne 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'umbes 15. Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'arvutatud 15. Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'arvestatavalt 15. Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'enne 15. Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'pärast 15. Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'järgneva poolt 15. Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '15. Muharram 1425\'le'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'ajavahemikul 15. Muharram 1425 ja 15. Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '15. Muharram 1425-lt 15. Safar 1425-le'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'tõlgendatud 15. Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'umbes Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'järgneva poolt Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'pärast Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'enne Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'umbes Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'järgneva poolt Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'pärast Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'enne Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'umbes Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'järgneva poolt Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'pärast Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'enne Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'umbes Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'järgneva poolt Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'pärast Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'enne Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'umbes Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'järgneva poolt Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'pärast Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'enne Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'umbes Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'järgneva poolt Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'pärast Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'enne Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'umbes Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'järgneva poolt Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'pärast Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'enne Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'umbes Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'järgneva poolt Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'pärast Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'enne Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'umbes Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'järgneva poolt Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'pärast Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'enne Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'umbes Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'järgneva poolt Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'pärast Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'enne Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'umbes Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'järgneva poolt Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'pärast Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'enne Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'umbes Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'järgneva poolt Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'pärast Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'enne Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'umbes 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'arvutatud 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'arvestatavalt 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'enne 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'pärast 15. Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'järgneva poolt 15. Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384\'le'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'ajavahemikul 15. Farvardin 1384 ja 15. Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '15. Farvardin 1384-lt 15. Ordibehesht 1384-le'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'tõlgendatud 15. Farvardin 1384'],
        ];
    }
}
