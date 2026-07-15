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
use Fisharebest\Webtrees\I18N\Languages\Kurdish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Kurdish::class)]
class KurdishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Kurdish();
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
        self::assertSame('ku', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('kurd', self::language()->endonym());
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
        self::assertSame('%-123.456,0789', self::language()->percentage(-1234.560789));
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
        self::assertSame('one û two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two û three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one an two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two an three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband           = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife              = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son               = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter          = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $child             = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
        $exHusband         = self::male('ex', "1 FAMS @fd@");
        $stepDaughter      = self::female('sd', "1 FAMC @fd@");
        $fatherOfH         = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH         = self::female('mh', "1 FAMS @fp@");
        $fatherOfW         = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW         = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH        = self::male('bh', "1 FAMC @fp@\n1 BIRT\n2 DATE 1968");
        $sisterOfH         = self::female('sh', "1 FAMC @fp@");
        $wifeOfSon         = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");
        $nieceFromBro      = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro     = self::male('npb', "1 FAMC @fbro@");
        $cousinFemale      = self::female('cf', "1 FAMC @fbro@");
        $cousinMale        = self::male('cm', "1 FAMC @fbro@");
        $paternalGF        = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM        = self::female('pgm', "1 FAMS @fgp@");
        $engaged           = self::female('eng', "1 FAMS @fe@");
        $fiance            = self::male('fan', "1 FAMS @fe@");

        $fm   = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd   = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp   = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw   = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp  = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@");
        $fe   = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe],
        );

        // Partners
        self::assertRelationshipNames('jin', 'mêr', [$husband, $fm, $wife]);
        self::assertRelationshipNames('hevjînê berê', 'hevjîna berê', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('destgirtî', 'destgirtî', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('dayik', 'kur', [$son, $fm, $wife]);
        self::assertRelationshipNames('bav', 'kur', [$son, $fm, $husband]);
        self::assertRelationshipNames('dayik', 'keç', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('xwişk', 'bira', [$son, $fm, $daughter]);
        self::assertRelationshipNames('bira', 'xwişk', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('nîvxwişk', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('bavê', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('keça zincîrî', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('xesû', 'zava', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('xezûr', 'zava', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('bûk', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('dapîr', 'nevî', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('bapîr', 'nevî', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipNames('dapîr', 'nevî', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('bapîr', 'nevî', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('kalbapîr', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('kaldapîr', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles — paternal
        self::assertRelationshipNames('met', 'kurê xwişk/birayî', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('ap', 'kurê xwişk/birayî', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('keça xwişk/birayî', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('kurê xwişk/birayî', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('pismam', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('pismam', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Rêbendan 2000'],
            ['JAN 2000', 'Rêbendan 2000'],
            ['ABT JAN 2000', 'Der dorê Rêbendan 2000'],
            ['FROM JAN 2000', 'ji Rêbendan 2000'],
            ['AFT JAN 2000', 'paşê Çile 2000'],
            ['BEF JAN 2000', 'Berya/berê Rêbendan 2000'],
            ['15 FEB 2000', '15 Reşemî 2000'],
            ['FEB 2000', 'Reşemî 2000'],
            ['ABT FEB 2000', 'Der dorê Reşemî 2000'],
            ['FROM FEB 2000', 'ji Reşemî 2000'],
            ['AFT FEB 2000', 'paşê Sibat 2000'],
            ['BEF FEB 2000', 'Berya/berê Reşemî 2000'],
            ['15 MAR 2000', '15 Adar 2000'],
            ['MAR 2000', 'Adar 2000'],
            ['ABT MAR 2000', 'Der dorê Adar 2000'],
            ['FROM MAR 2000', 'ji Adar 2000'],
            ['AFT MAR 2000', 'paşê Adar 2000'],
            ['BEF MAR 2000', 'Berya/berê Adar 2000'],
            ['15 APR 2000', '15 Avrêl 2000'],
            ['APR 2000', 'Avrêl 2000'],
            ['ABT APR 2000', 'Der dorê Avrêl 2000'],
            ['FROM APR 2000', 'ji Avrêl 2000'],
            ['AFT APR 2000', 'paşê Nîsan 2000'],
            ['BEF APR 2000', 'Berya/berê Nîsan 2000'],
            ['15 MAY 2000', '15 Gulan 2000'],
            ['MAY 2000', 'Gulan 2000'],
            ['ABT MAY 2000', 'Der dorê Gulan 2000'],
            ['FROM MAY 2000', 'ji Gulan 2000'],
            ['AFT MAY 2000', 'paşê Gulan 2000'],
            ['BEF MAY 2000', 'Berya/berê Gulan 2000'],
            ['15 JUN 2000', '15 Pûşper 2000'],
            ['JUN 2000', 'Pûşper 2000'],
            ['ABT JUN 2000', 'Der dorê Pûşper 2000'],
            ['FROM JUN 2000', 'ji Pûşper 2000'],
            ['AFT JUN 2000', 'paşê Hezîran 2000'],
            ['BEF JUN 2000', 'Berya/berê Pûşper 2000'],
            ['15 JUL 2000', '15 Tîrmeh 2000'],
            ['JUL 2000', 'Tîrmeh 2000'],
            ['ABT JUL 2000', 'Der dorê Tîrmeh 2000'],
            ['FROM JUL 2000', 'ji Tîrmeh 2000'],
            ['AFT JUL 2000', 'paşê Tîrmeh 2000'],
            ['BEF JUL 2000', 'Berya/berê Tîrmeh 2000'],
            ['15 AUG 2000', '15 Gelawêj 2000'],
            ['AUG 2000', 'Gelawêj 2000'],
            ['ABT AUG 2000', 'Der dorê Gelawêj 2000'],
            ['FROM AUG 2000', 'ji Gelawêj 2000'],
            ['AFT AUG 2000', 'paşê Gelawêj 2000'],
            ['BEF AUG 2000', 'Berya/berê Gelawêj 2000'],
            ['15 SEP 2000', '15 Rezber 2000'],
            ['SEP 2000', 'Rezber 2000'],
            ['ABT SEP 2000', 'Der dorê Rezber 2000'],
            ['FROM SEP 2000', 'ji Rezber 2000'],
            ['AFT SEP 2000', 'paşê Îlon 2000'],
            ['BEF SEP 2000', 'Berya/berê Îlon 2000'],
            ['15 OCT 2000', '15 Kewçêr 2000'],
            ['OCT 2000', 'Kewçêr 2000'],
            ['ABT OCT 2000', 'Der dorê Kewçêr 2000'],
            ['FROM OCT 2000', 'ji Kewçêr 2000'],
            ['AFT OCT 2000', 'paşê Cotmeh 2000'],
            ['BEF OCT 2000', 'Berya/berê Cotmeh 2000'],
            ['15 NOV 2000', '15 Sermawez 2000'],
            ['NOV 2000', 'Sermawez 2000'],
            ['ABT NOV 2000', 'Der dorê Sermawez 2000'],
            ['FROM NOV 2000', 'ji Sermawez 2000'],
            ['AFT NOV 2000', 'paşê Sermawez 2000'],
            ['BEF NOV 2000', 'Berya/berê Sermawez 2000'],
            ['15 DEC 2000', '15 Berfanbar 2000'],
            ['DEC 2000', 'Berfanbar 2000'],
            ['ABT DEC 2000', 'Der dorê Berfanbar 2000'],
            ['FROM DEC 2000', 'ji Berfanbar 2000'],
            ['AFT DEC 2000', 'paşê Berfanbar 2000'],
            ['BEF DEC 2000', 'Berya/berê Berfanbar 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'Der dorê 15 Rêbendan 2000'],
            ['CAL 15 JAN 2000', 'Çortik 15 Rêbendan 2000'],
            ['EST 15 JAN 2000', 'Texmînî 15 Rêbendan 2000'],
            ['BEF 15 JAN 2000', 'Berya/berê 15 Rêbendan 2000'],
            ['AFT 15 JAN 2000', 'paşê 15 Rêbendan 2000'],
            ['FROM 15 JAN 2000', 'ji 15 Rêbendan 2000'],
            ['TO 15 JAN 2000', 'heya/ ta 15 Rêbendan 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'Navbera 15 Rêbendan 2000 û 15 Reşemî 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'Ji 15 Rêbendan 2000 heya 15 Reşemî 2000'],
            ['INT 15 JAN 2000', 'Şirovekirinî 15 Rêbendan 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Rêbendan 1700 PZ'],
            ['@#DJULIAN@ JAN 1700', 'Rêbendan 1700 PZ'],
            ['ABT @#DJULIAN@ JAN 1700', 'Der dorê Rêbendan 1700 PZ'],
            ['FROM @#DJULIAN@ JAN 1700', 'ji Rêbendan 1700 PZ'],
            ['AFT @#DJULIAN@ JAN 1700', 'paşê Çile 1700 PZ'],
            ['BEF @#DJULIAN@ JAN 1700', 'Berya/berê Rêbendan 1700 PZ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Reşemî 1700 PZ'],
            ['@#DJULIAN@ FEB 1700', 'Reşemî 1700 PZ'],
            ['ABT @#DJULIAN@ FEB 1700', 'Der dorê Reşemî 1700 PZ'],
            ['FROM @#DJULIAN@ FEB 1700', 'ji Reşemî 1700 PZ'],
            ['AFT @#DJULIAN@ FEB 1700', 'paşê Sibat 1700 PZ'],
            ['BEF @#DJULIAN@ FEB 1700', 'Berya/berê Reşemî 1700 PZ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Adar 1700 PZ'],
            ['@#DJULIAN@ MAR 1700', 'Adar 1700 PZ'],
            ['ABT @#DJULIAN@ MAR 1700', 'Der dorê Adar 1700 PZ'],
            ['FROM @#DJULIAN@ MAR 1700', 'ji Adar 1700 PZ'],
            ['AFT @#DJULIAN@ MAR 1700', 'paşê Adar 1700 PZ'],
            ['BEF @#DJULIAN@ MAR 1700', 'Berya/berê Adar 1700 PZ'],
            ['@#DJULIAN@ 15 APR 1700', '15 Avrêl 1700 PZ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Avrêl 1645/46 PZ'],
            ['@#DJULIAN@ APR 1700', 'Avrêl 1700 PZ'],
            ['ABT @#DJULIAN@ APR 1700', 'Der dorê Avrêl 1700 PZ'],
            ['FROM @#DJULIAN@ APR 1700', 'ji Avrêl 1700 PZ'],
            ['AFT @#DJULIAN@ APR 1700', 'paşê Nîsan 1700 PZ'],
            ['BEF @#DJULIAN@ APR 1700', 'Berya/berê Nîsan 1700 PZ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Gulan 1700 PZ'],
            ['@#DJULIAN@ MAY 1700', 'Gulan 1700 PZ'],
            ['ABT @#DJULIAN@ MAY 1700', 'Der dorê Gulan 1700 PZ'],
            ['FROM @#DJULIAN@ MAY 1700', 'ji Gulan 1700 PZ'],
            ['AFT @#DJULIAN@ MAY 1700', 'paşê Gulan 1700 PZ'],
            ['BEF @#DJULIAN@ MAY 1700', 'Berya/berê Gulan 1700 PZ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Pûşper 1700 PZ'],
            ['@#DJULIAN@ JUN 1700', 'Pûşper 1700 PZ'],
            ['ABT @#DJULIAN@ JUN 1700', 'Der dorê Pûşper 1700 PZ'],
            ['FROM @#DJULIAN@ JUN 1700', 'ji Pûşper 1700 PZ'],
            ['AFT @#DJULIAN@ JUN 1700', 'paşê Hezîran 1700 PZ'],
            ['BEF @#DJULIAN@ JUN 1700', 'Berya/berê Pûşper 1700 PZ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Tîrmeh 1700 PZ'],
            ['@#DJULIAN@ JUL 1700', 'Tîrmeh 1700 PZ'],
            ['ABT @#DJULIAN@ JUL 1700', 'Der dorê Tîrmeh 1700 PZ'],
            ['FROM @#DJULIAN@ JUL 1700', 'ji Tîrmeh 1700 PZ'],
            ['AFT @#DJULIAN@ JUL 1700', 'paşê Tîrmeh 1700 PZ'],
            ['BEF @#DJULIAN@ JUL 1700', 'Berya/berê Tîrmeh 1700 PZ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Gelawêj 1700 PZ'],
            ['@#DJULIAN@ AUG 1700', 'Gelawêj 1700 PZ'],
            ['ABT @#DJULIAN@ AUG 1700', 'Der dorê Gelawêj 1700 PZ'],
            ['FROM @#DJULIAN@ AUG 1700', 'ji Gelawêj 1700 PZ'],
            ['AFT @#DJULIAN@ AUG 1700', 'paşê Gelawêj 1700 PZ'],
            ['BEF @#DJULIAN@ AUG 1700', 'Berya/berê Gelawêj 1700 PZ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Rezber 1700 PZ'],
            ['@#DJULIAN@ SEP 1700', 'Rezber 1700 PZ'],
            ['ABT @#DJULIAN@ SEP 1700', 'Der dorê Rezber 1700 PZ'],
            ['FROM @#DJULIAN@ SEP 1700', 'ji Rezber 1700 PZ'],
            ['AFT @#DJULIAN@ SEP 1700', 'paşê Îlon 1700 PZ'],
            ['BEF @#DJULIAN@ SEP 1700', 'Berya/berê Îlon 1700 PZ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Kewçêr 1700 PZ'],
            ['@#DJULIAN@ OCT 1700', 'Kewçêr 1700 PZ'],
            ['ABT @#DJULIAN@ OCT 1700', 'Der dorê Kewçêr 1700 PZ'],
            ['FROM @#DJULIAN@ OCT 1700', 'ji Kewçêr 1700 PZ'],
            ['AFT @#DJULIAN@ OCT 1700', 'paşê Cotmeh 1700 PZ'],
            ['BEF @#DJULIAN@ OCT 1700', 'Berya/berê Cotmeh 1700 PZ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Sermawez 1700 PZ'],
            ['@#DJULIAN@ NOV 1700', 'Sermawez 1700 PZ'],
            ['ABT @#DJULIAN@ NOV 1700', 'Der dorê Sermawez 1700 PZ'],
            ['FROM @#DJULIAN@ NOV 1700', 'ji Sermawez 1700 PZ'],
            ['AFT @#DJULIAN@ NOV 1700', 'paşê Sermawez 1700 PZ'],
            ['BEF @#DJULIAN@ NOV 1700', 'Berya/berê Sermawez 1700 PZ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Berfanbar 1700 PZ'],
            ['@#DJULIAN@ DEC 1700', 'Berfanbar 1700 PZ'],
            ['ABT @#DJULIAN@ DEC 1700', 'Der dorê Berfanbar 1700 PZ'],
            ['FROM @#DJULIAN@ DEC 1700', 'ji Berfanbar 1700 PZ'],
            ['AFT @#DJULIAN@ DEC 1700', 'paşê Berfanbar 1700 PZ'],
            ['BEF @#DJULIAN@ DEC 1700', 'Berya/berê Berfanbar 1700 PZ'],
            ['@#DJULIAN@ 1700', '1700 PZ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'Der dorê 15 Rêbendan 1700 PZ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'Çortik 15 Rêbendan 1700 PZ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'Texmînî 15 Rêbendan 1700 PZ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'Berya/berê 15 Rêbendan 1700 PZ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'paşê 15 Rêbendan 1700 PZ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'ji 15 Rêbendan 1700 PZ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'heya/ ta 15 Rêbendan 1700 PZ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'Navbera 15 Rêbendan 1700 PZ û 15 Reşemî 1700 PZ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'Ji 15 Rêbendan 1700 PZ heya 15 Reşemî 1700 PZ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'Şirovekirinî 15 Rêbendan 1700 PZ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'Der dorê Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'ji Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'paşê Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'Berya/berê Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'Der dorê Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'ji Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'paşê Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'Berya/berê Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'Der dorê Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'ji Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'paşê Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'Berya/berê Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'Der dorê Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'ji Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'paşê Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'Berya/berê Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'Der dorê Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'ji Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'paşê Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'Berya/berê Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'Der dorê Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'ji Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'paşê Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'Berya/berê Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'Der dorê Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'ji Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'paşê Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'Berya/berê Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'Der dorê Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'ji Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'paşê Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'Berya/berê Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'Der dorê Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'ji Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'paşê Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'Berya/berê Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'Der dorê Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'ji Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'paşê Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'Berya/berê Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'Der dorê Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'ji Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'paşê Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'Berya/berê Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'Der dorê Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'ji Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'paşê Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'Berya/berê Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'Der dorê Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'ji Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'paşê Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'Berya/berê Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'Der dorê 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'Çortik 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'Texmînî 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'Berya/berê 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'paşê 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'ji 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'heya/ ta 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'Navbera 15 Tishrei 5765 û 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'Ji 15 Tishrei 5765 heya 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'Şirovekirinî 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'Der dorê Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'ji Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'paşê Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'Berya/berê Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'Der dorê Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'ji Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'paşê Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'Berya/berê Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'Der dorê Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'ji Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'paşê Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'Berya/berê Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'Der dorê Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'ji Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'paşê Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'Berya/berê Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'Der dorê Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'ji Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'paşê Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'Berya/berê Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'Der dorê Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'ji Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'paşê Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'Berya/berê Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'Der dorê Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'ji Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'paşê Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'Berya/berê Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'Der dorê Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'ji Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'paşê Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'Berya/berê Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'Der dorê Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'ji Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'paşê Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'Berya/berê Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'Der dorê Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'ji Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'paşê Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'Berya/berê Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'Der dorê Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'ji Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'paşê Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'Berya/berê Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'Der dorê Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'ji Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'paşê Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'Berya/berê Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 sertayê rojan An XII'],
            ['@#DFRENCH R@ COMP 12', 'sertayê rojan An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'Der dorê sertayê rojan An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'ji sertayê rojan An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'paşê sertayê rojan An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'Berya/berê sertayê rojan An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'Der dorê 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'Çortik 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'Texmînî 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'Berya/berê 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'paşê 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'ji 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'heya/ ta 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'Navbera 15 Vendémiaire An XII û 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'Ji 15 Vendémiaire An XII heya 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'Şirovekirinî 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muherrem 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muherrem 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'Der dorê Muherrem 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'ji Muherrem 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'paşê Muherrem 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'Berya/berê Muherrem 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Sefer 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Sefer 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'Der dorê Sefer 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'ji Sefer 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'paşê Sefer 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'Berya/berê Sefer 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rebîûl-ewwel 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rebîûl-ewwel 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'Der dorê Rebîûl-ewwel 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'ji Rebîûl-ewwel 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'paşê Rebîûl-ewwel 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'Berya/berê Rebîûl-ewwel 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rebîûl-axir 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rebîûl-axir 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'Der dorê Rebîûl-axir 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'ji Rebîûl-axir 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'paşê Rebîûl-axir 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'Berya/berê Rebîûl-axir 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Cemazîyel-ewwel 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Cemazîyel-ewwel 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'Der dorê Cemazîyel-ewwel 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'ji Cemazîyel-ewwel 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'paşê Cemazîyel-ewwel 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'Berya/berê Cemazîyel-ewwel 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Cemazîyel-axir 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Cemazîyel-axir 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'Der dorê Cemazîyel-axir 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'ji Cemazîyel-axir 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'paşê Cemazîyel-axir 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'Berya/berê Cemazîyel-axir 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Receb 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Receb 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'Der dorê Receb 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'ji Receb 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'paşê Receb 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'Berya/berê Receb 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Şeban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Şeban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'Der dorê Şeban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'ji Şeban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'paşê Şeban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'Berya/berê Şeban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramazan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramazan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'Der dorê Ramazan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'ji Ramazan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'paşê Ramazan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'Berya/berê Ramazan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Şewwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Şewwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'Der dorê Şewwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'ji Şewwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'paşê Şewwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'Berya/berê Şewwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Zîlqade 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Zîlqade 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'Der dorê Zîlqade 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'ji Zîlqade 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'paşê Zîlqade 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'Berya/berê Zîlqade 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'Der dorê 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'ji 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'paşê 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'Berya/berê 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'Der dorê 15 Muherrem 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'Çortik 15 Muherrem 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'Texmînî 15 Muherrem 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'Berya/berê 15 Muherrem 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'paşê 15 Muherrem 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'ji 15 Muherrem 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'heya/ ta 15 Muherrem 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'Navbera 15 Muherrem 1425 û 15 Sefer 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'Ji 15 Muherrem 1425 heya 15 Sefer 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'Şirovekirinî 15 Muherrem 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'Der dorê Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'ji Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'paşê Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'Berya/berê Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'Der dorê Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'ji Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'paşê Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'Berya/berê Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'Der dorê Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'ji Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'paşê Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'Berya/berê Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'Der dorê Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'ji Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'paşê Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'Berya/berê Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'Der dorê Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'ji Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'paşê Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'Berya/berê Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'Der dorê Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'ji Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'paşê Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'Berya/berê Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'Der dorê Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'ji Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'paşê Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'Berya/berê Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'Der dorê Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'ji Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'paşê Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'Berya/berê Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'Der dorê Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'ji Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'paşê Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'Berya/berê Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'Der dorê Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'ji Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'paşê Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'Berya/berê Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'Der dorê Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'ji Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'paşê Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'Berya/berê Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'Der dorê Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'ji Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'paşê Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'Berya/berê Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'Der dorê 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'Çortik 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'Texmînî 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'Berya/berê 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'paşê 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'ji 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'heya/ ta 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'Navbera 15 Farvardin 1384 û 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'Ji 15 Farvardin 1384 heya 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'Şirovekirinî 15 Farvardin 1384'],
        ];
    }
}
