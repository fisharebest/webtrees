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
use Fisharebest\Webtrees\I18N\Languages\Lingala;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Lingala::class)]
class LingalaTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Lingala();
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
        self::assertSame('ln', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('lingla', self::language()->endonym());
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
        self::assertSame('one na two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two na three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one to two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two to three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('mwasi', 'mobali', [$husband, $fm, $wife]);
        self::assertRelationshipNames('molongani ya kala', 'molongani ya kala', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('mobalani', 'mobalani', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('mama', 'mwana mobali', [$son, $fm, $wife]);
        self::assertRelationshipNames('tata', 'mwana mobali', [$son, $fm, $husband]);
        self::assertRelationshipNames('mama', 'mwana mwasi', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('ndeko mwasi', 'ndeko mobali', [$son, $fm, $daughter]);
        self::assertRelationshipNames('ndeko mobali', 'ndeko mwasi', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('ndeko mwasi ya ndámbo', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('tata ya kobɔkɔla', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('mwana mwasi ya kobɔkɔla', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('bokilo mwasi', 'bɔkɛli mobali', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('bokilo mobali', 'bɔkɛli mobali', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('bɔkɛli mwasi', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('nkɔkɔ mwasi', 'nkɔkɔ mobali', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('nkɔkɔ mobali', 'nkɔkɔ mobali', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipNames('nkɔkɔ mwasi', 'nkɔkɔ mobali', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('nkɔkɔ mobali', 'nkɔkɔ mobali', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('nkɔkɔ mobali ya molɔ́ngɔ́ ya 2', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('nkɔkɔ mwasi ya molɔ́ngɔ́ ya 2', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles — paternal
        self::assertRelationshipNames('tántí', 'mwana ya ndeko mobali', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('nkɔ́kɔ', 'mwana ya ndeko mobali', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('mwana ya ndeko mwasi', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('mwana ya ndeko mobali', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('ndeko ya mbɔ́ka', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('ndeko ya mbɔ́ka', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Yanwáli 2000'],
            ['JAN 2000', 'Yanwáli 2000'],
            ['ABT JAN 2000', 'likoló na Yanwáli 2000'],
            ['FROM JAN 2000', 'útá Yanwáli 2000'],
            ['AFT JAN 2000', 'nsima ya Yanwáli 2000'],
            ['BEF JAN 2000', 'libosó ya Yanwáli 2000'],
            ['15 FEB 2000', '15 Febwáli 2000'],
            ['FEB 2000', 'Febwáli 2000'],
            ['ABT FEB 2000', 'likoló na Febwáli 2000'],
            ['FROM FEB 2000', 'útá Febwáli 2000'],
            ['AFT FEB 2000', 'nsima ya Febwáli 2000'],
            ['BEF FEB 2000', 'libosó ya Febwáli 2000'],
            ['15 MAR 2000', '15 Mársi 2000'],
            ['MAR 2000', 'Mársi 2000'],
            ['ABT MAR 2000', 'likoló na Mársi 2000'],
            ['FROM MAR 2000', 'útá Mársi 2000'],
            ['AFT MAR 2000', 'nsima ya Mársi 2000'],
            ['BEF MAR 2000', 'libosó ya Mársi 2000'],
            ['15 APR 2000', '15 Apríli 2000'],
            ['APR 2000', 'Apríli 2000'],
            ['ABT APR 2000', 'likoló na Apríli 2000'],
            ['FROM APR 2000', 'útá Apríli 2000'],
            ['AFT APR 2000', 'nsima ya Apríli 2000'],
            ['BEF APR 2000', 'libosó ya Apríli 2000'],
            ['15 MAY 2000', '15 Máyí 2000'],
            ['MAY 2000', 'Máyí 2000'],
            ['ABT MAY 2000', 'likoló na Máyí 2000'],
            ['FROM MAY 2000', 'útá Máyí 2000'],
            ['AFT MAY 2000', 'nsima ya Máyí 2000'],
            ['BEF MAY 2000', 'libosó ya Máyí 2000'],
            ['15 JUN 2000', '15 Yuni 2000'],
            ['JUN 2000', 'Yuni 2000'],
            ['ABT JUN 2000', 'likoló na Yuni 2000'],
            ['FROM JUN 2000', 'útá Yuni 2000'],
            ['AFT JUN 2000', 'nsima ya Yuni 2000'],
            ['BEF JUN 2000', 'libosó ya Yuni 2000'],
            ['15 JUL 2000', '15 Yúli 2000'],
            ['JUL 2000', 'Yúli 2000'],
            ['ABT JUL 2000', 'likoló na Yúli 2000'],
            ['FROM JUL 2000', 'útá Yúli 2000'],
            ['AFT JUL 2000', 'nsima ya Yúli 2000'],
            ['BEF JUL 2000', 'libosó ya Yúli 2000'],
            ['15 AUG 2000', '15 Augústo 2000'],
            ['AUG 2000', 'Augústo 2000'],
            ['ABT AUG 2000', 'likoló na Augústo 2000'],
            ['FROM AUG 2000', 'útá Augústo 2000'],
            ['AFT AUG 2000', 'nsima ya Augústo 2000'],
            ['BEF AUG 2000', 'libosó ya Augústo 2000'],
            ['15 SEP 2000', '15 Sɛtɛ́mbɛ 2000'],
            ['SEP 2000', 'Sɛtɛ́mbɛ 2000'],
            ['ABT SEP 2000', 'likoló na Sɛtɛ́mbɛ 2000'],
            ['FROM SEP 2000', 'útá Sɛtɛ́mbɛ 2000'],
            ['AFT SEP 2000', 'nsima ya Sɛtɛ́mbɛ 2000'],
            ['BEF SEP 2000', 'libosó ya Sɛtɛ́mbɛ 2000'],
            ['15 OCT 2000', '15 Ɔkɔtɔbɛ 2000'],
            ['OCT 2000', 'Ɔkɔtɔbɛ 2000'],
            ['ABT OCT 2000', 'likoló na Ɔkɔtɔbɛ 2000'],
            ['FROM OCT 2000', 'útá Ɔkɔtɔbɛ 2000'],
            ['AFT OCT 2000', 'nsima ya Ɔkɔtɔbɛ 2000'],
            ['BEF OCT 2000', 'libosó ya Ɔkɔtɔbɛ 2000'],
            ['15 NOV 2000', '15 Novɛ́mbɛ 2000'],
            ['NOV 2000', 'Novɛ́mbɛ 2000'],
            ['ABT NOV 2000', 'likoló na Novɛ́mbɛ 2000'],
            ['FROM NOV 2000', 'útá Novɛ́mbɛ 2000'],
            ['AFT NOV 2000', 'nsima ya Novɛ́mbɛ 2000'],
            ['BEF NOV 2000', 'libosó ya Novɛ́mbɛ 2000'],
            ['15 DEC 2000', '15 Dɛsɛ́mbɛ 2000'],
            ['DEC 2000', 'Dɛsɛ́mbɛ 2000'],
            ['ABT DEC 2000', 'likoló na Dɛsɛ́mbɛ 2000'],
            ['FROM DEC 2000', 'útá Dɛsɛ́mbɛ 2000'],
            ['AFT DEC 2000', 'nsima ya Dɛsɛ́mbɛ 2000'],
            ['BEF DEC 2000', 'libosó ya Dɛsɛ́mbɛ 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'likoló na 15 Yanwáli 2000'],
            ['CAL 15 JAN 2000', '15 Yanwáli 2000 etángámí'],
            ['EST 15 JAN 2000', 'estimated 15 Yanwáli 2000'],
            ['BEF 15 JAN 2000', 'libosó ya 15 Yanwáli 2000'],
            ['AFT 15 JAN 2000', 'nsima ya 15 Yanwáli 2000'],
            ['FROM 15 JAN 2000', 'útá 15 Yanwáli 2000'],
            ['TO 15 JAN 2000', 'na 15 Yanwáli 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'káti na 15 Yanwáli 2000 mpé 15 Febwáli 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'útá 15 Yanwáli 2000 kín’o 15 Febwáli 2000'],
            ['INT 15 JAN 2000', 'interpreted 15 Yanwáli 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Yanwáli 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Yanwáli 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'likoló na Yanwáli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'útá Yanwáli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'nsima ya Yanwáli 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'libosó ya Yanwáli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Febwáli 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Febwáli 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'likoló na Febwáli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'útá Febwáli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'nsima ya Febwáli 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'libosó ya Febwáli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Mársi 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Mársi 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'likoló na Mársi 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'útá Mársi 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'nsima ya Mársi 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'libosó ya Mársi 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '15 Apríli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Apríli 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'Apríli 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'likoló na Apríli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'útá Apríli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'nsima ya Apríli 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'libosó ya Apríli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Máyí 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Máyí 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'likoló na Máyí 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'útá Máyí 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'nsima ya Máyí 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'libosó ya Máyí 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Yuni 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Yuni 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'likoló na Yuni 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'útá Yuni 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'nsima ya Yuni 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'libosó ya Yuni 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Yúli 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Yúli 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'likoló na Yúli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'útá Yúli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'nsima ya Yúli 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'libosó ya Yúli 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Augústo 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Augústo 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'likoló na Augústo 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'útá Augústo 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'nsima ya Augústo 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'libosó ya Augústo 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Sɛtɛ́mbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Sɛtɛ́mbɛ 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'likoló na Sɛtɛ́mbɛ 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'útá Sɛtɛ́mbɛ 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'nsima ya Sɛtɛ́mbɛ 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'libosó ya Sɛtɛ́mbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Ɔkɔtɔbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Ɔkɔtɔbɛ 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'likoló na Ɔkɔtɔbɛ 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'útá Ɔkɔtɔbɛ 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'nsima ya Ɔkɔtɔbɛ 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'libosó ya Ɔkɔtɔbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Novɛ́mbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Novɛ́mbɛ 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'likoló na Novɛ́mbɛ 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'útá Novɛ́mbɛ 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'nsima ya Novɛ́mbɛ 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'libosó ya Novɛ́mbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Dɛsɛ́mbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Dɛsɛ́mbɛ 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'likoló na Dɛsɛ́mbɛ 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'útá Dɛsɛ́mbɛ 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'nsima ya Dɛsɛ́mbɛ 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'libosó ya Dɛsɛ́mbɛ 1700 ᴄᴇ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'likoló na 15 Yanwáli 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', '15 Yanwáli 1700 ᴄᴇ etángámí'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated 15 Yanwáli 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'libosó ya 15 Yanwáli 1700 ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'nsima ya 15 Yanwáli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'útá 15 Yanwáli 1700 ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'na 15 Yanwáli 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'káti na 15 Yanwáli 1700 ᴄᴇ mpé 15 Febwáli 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'útá 15 Yanwáli 1700 ᴄᴇ kín’o 15 Febwáli 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted 15 Yanwáli 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'likoló na Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'útá Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'nsima ya Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'libosó ya Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'likoló na Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'útá Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'nsima ya Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'libosó ya Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'likoló na Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'útá Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'nsima ya Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'libosó ya Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'likoló na Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'útá Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'nsima ya Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'libosó ya Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'likoló na Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'útá Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'nsima ya Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'libosó ya Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'likoló na Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'útá Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'nsima ya Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'libosó ya Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'likoló na Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'útá Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'nsima ya Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'libosó ya Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'likoló na Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'útá Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'nsima ya Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'libosó ya Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'likoló na Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'útá Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'nsima ya Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'libosó ya Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'likoló na Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'útá Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'nsima ya Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'libosó ya Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'likoló na Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'útá Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'nsima ya Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'libosó ya Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'likoló na Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'útá Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'nsima ya Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'libosó ya Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'likoló na Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'útá Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'nsima ya Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'libosó ya Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'likoló na 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765 etángámí'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'libosó ya 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'nsima ya 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'útá 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'na 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'káti na 15 Tishrei 5765 mpé 15 Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'útá 15 Tishrei 5765 kín’o 15 Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'likoló na Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'útá Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'nsima ya Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'libosó ya Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'likoló na Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'útá Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'nsima ya Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'libosó ya Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'likoló na Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'útá Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'nsima ya Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'libosó ya Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'likoló na Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'útá Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'nsima ya Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'libosó ya Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'likoló na Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'útá Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'nsima ya Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'libosó ya Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'likoló na Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'útá Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'nsima ya Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'libosó ya Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'likoló na Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'útá Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'nsima ya Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'libosó ya Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'likoló na Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'útá Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'nsima ya Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'libosó ya Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'likoló na Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'útá Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'nsima ya Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'libosó ya Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'likoló na Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'útá Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'nsima ya Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'libosó ya Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'likoló na Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'útá Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'nsima ya Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'libosó ya Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'likoló na Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'útá Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'nsima ya Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'libosó ya Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 mikɔlɔ mya kobakisa An XII'],
            ['@#DFRENCH R@ COMP 12', 'mikɔlɔ mya kobakisa An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'likoló na mikɔlɔ mya kobakisa An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'útá mikɔlɔ mya kobakisa An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'nsima ya mikɔlɔ mya kobakisa An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'libosó ya mikɔlɔ mya kobakisa An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'likoló na 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII etángámí'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'libosó ya 15 Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'nsima ya 15 Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'útá 15 Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'na 15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'káti na 15 Vendémiaire An XII mpé 15 Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'útá 15 Vendémiaire An XII kín’o 15 Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'likoló na Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'útá Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'nsima ya Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'libosó ya Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'likoló na Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'útá Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'nsima ya Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'libosó ya Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'likoló na Rabi al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'útá Rabi al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'nsima ya Rabi al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'libosó ya Rabi al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'likoló na Rabi al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'útá Rabi al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'nsima ya Rabi al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'libosó ya Rabi al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'likoló na Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'útá Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'nsima ya Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'libosó ya Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'likoló na Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'útá Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'nsima ya Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'libosó ya Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'likoló na Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'útá Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'nsima ya Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'libosó ya Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'likoló na Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'útá Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'nsima ya Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'libosó ya Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'likoló na Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'útá Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'nsima ya Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'libosó ya Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'likoló na Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'útá Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'nsima ya Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'libosó ya Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'likoló na Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'útá Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'nsima ya Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'libosó ya Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'likoló na 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'útá 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'nsima ya 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'libosó ya 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'likoló na 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425 etángámí'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'libosó ya 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'nsima ya 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'útá 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'na 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'káti na 15 Muharram 1425 mpé 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'útá 15 Muharram 1425 kín’o 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'likoló na Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'útá Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'nsima ya Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'libosó ya Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'likoló na Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'útá Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'nsima ya Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'libosó ya Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'likoló na Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'útá Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'nsima ya Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'libosó ya Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'likoló na Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'útá Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'nsima ya Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'libosó ya Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'likoló na Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'útá Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'nsima ya Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'libosó ya Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'likoló na Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'útá Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'nsima ya Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'libosó ya Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'likoló na Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'útá Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'nsima ya Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'libosó ya Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'likoló na Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'útá Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'nsima ya Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'libosó ya Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'likoló na Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'útá Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'nsima ya Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'libosó ya Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'likoló na Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'útá Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'nsima ya Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'libosó ya Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'likoló na Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'útá Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'nsima ya Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'libosó ya Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'likoló na Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'útá Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'nsima ya Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'libosó ya Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'likoló na 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384 etángámí'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'libosó ya 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'nsima ya 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'útá 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'na 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'káti na 15 Farvardin 1384 mpé 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'útá 15 Farvardin 1384 kín’o 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted 15 Farvardin 1384'],
        ];
    }
}
