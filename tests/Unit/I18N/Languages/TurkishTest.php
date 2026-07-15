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
use Fisharebest\Webtrees\I18N\Languages\Turkish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Turkish::class)]
class TurkishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Turkish();
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
        self::assertSame(['A', 'B', 'C', 'Ç', 'D', 'E', 'F', 'G', 'Ğ', 'H', 'I', 'İ', 'J', 'K', 'L', 'M', 'N', 'O', 'Ö', 'P', 'R', 'S', 'Ş', 'T', 'U', 'Ü', 'V', 'Y', 'Z'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('tr', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Trke', self::language()->endonym());
    }



    public function testStrtolower(): void
    {
        self::assertSame('abc', self::language()->strtolower('Abc'));
        self::assertSame('école', self::language()->strtolower('ÉCOLE'));
        self::assertSame('ı', self::language()->strtolower('I'));
        self::assertSame('i', self::language()->strtolower('İ'));
    }

    public function testStrtoupper(): void
    {
        self::assertSame('ABC', self::language()->strtoupper('Abc'));
        self::assertSame('ÉCOLE', self::language()->strtoupper('école'));
        self::assertSame('I', self::language()->strtoupper('ı'));
        self::assertSame('İ', self::language()->strtoupper('i'));
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
        self::assertSame('one ve two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two ve three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one veya two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two veya three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('eş', 'eş', [$husband, $fm, $wife]);
        self::assertRelationshipNames('eski eş', 'eski eş', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('nişanlı', 'nişanlı', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('anne', 'oğul', [$son, $fm, $wife]);
        self::assertRelationshipNames('baba', 'oğul', [$son, $fm, $husband]);
        self::assertRelationshipNames('anne', 'kız', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('kız kardeş', 'ağabey', [$son, $fm, $daughter]);
        self::assertRelationshipNames('ağabey', 'kız kardeş', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('üvey kız kardeş', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('üvey baba', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('üvey kız', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('kayınvalide', 'damat', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('kayınpeder', 'damat', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('gelin', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents — maternal
        self::assertRelationshipNames('anneanne', 'torun', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('dede', 'torun', [$son, $fm, $wife, $fw, $fatherOfW]);
        // Grandparents — paternal
        self::assertRelationshipNames('babaanne', 'torun', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('dede', 'torun', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('büyük büyükbaba', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('büyük büyükanne', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles — paternal
        self::assertRelationshipNames('hala', 'yeğen', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('amca', 'yeğen', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('yeğen', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('yeğen', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('kuzen', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('kuzen', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Ocak 2000'],
            ['JAN 2000', 'Ocak 2000'],
            ['ABT JAN 2000', 'yaklaşık Ocak 2000'],
            ['FROM JAN 2000', 'Ocak 2000 tarihinden'],
            ['AFT JAN 2000', 'Ocak 2000 sonrası'],
            ['BEF JAN 2000', 'Ocak 2000 öncesi'],
            ['15 FEB 2000', '15 Şubat 2000'],
            ['FEB 2000', 'Şubat 2000'],
            ['ABT FEB 2000', 'yaklaşık Şubat 2000'],
            ['FROM FEB 2000', 'Şubat 2000 tarihinden'],
            ['AFT FEB 2000', 'Şubat 2000 sonrası'],
            ['BEF FEB 2000', 'Şubat 2000 öncesi'],
            ['15 MAR 2000', '15 Mart 2000'],
            ['MAR 2000', 'Mart 2000'],
            ['ABT MAR 2000', 'yaklaşık Mart 2000'],
            ['FROM MAR 2000', 'Mart 2000 tarihinden'],
            ['AFT MAR 2000', 'Mart 2000 sonrası'],
            ['BEF MAR 2000', 'Mart 2000 öncesi'],
            ['15 APR 2000', '15 Nisan 2000'],
            ['APR 2000', 'Nisan 2000'],
            ['ABT APR 2000', 'yaklaşık Nisan 2000'],
            ['FROM APR 2000', 'Nisan 2000 tarihinden'],
            ['AFT APR 2000', 'Nisan 2000 sonrası'],
            ['BEF APR 2000', 'Nisan 2000 öncesi'],
            ['15 MAY 2000', '15 Mayıs 2000'],
            ['MAY 2000', 'Mayıs 2000'],
            ['ABT MAY 2000', 'yaklaşık Mayıs 2000'],
            ['FROM MAY 2000', 'Mayıs 2000 tarihinden'],
            ['AFT MAY 2000', 'Mayıs 2000 sonrası'],
            ['BEF MAY 2000', 'Mayıs 2000 öncesi'],
            ['15 JUN 2000', '15 Haziran 2000'],
            ['JUN 2000', 'Haziran 2000'],
            ['ABT JUN 2000', 'yaklaşık Haziran 2000'],
            ['FROM JUN 2000', 'Haziran 2000 tarihinden'],
            ['AFT JUN 2000', 'Haziran 2000 sonrası'],
            ['BEF JUN 2000', 'Haziran 2000 öncesi'],
            ['15 JUL 2000', '15 Temmuz 2000'],
            ['JUL 2000', 'Temmuz 2000'],
            ['ABT JUL 2000', 'yaklaşık Temmuz 2000'],
            ['FROM JUL 2000', 'Temmuz 2000 tarihinden'],
            ['AFT JUL 2000', 'Temmuz 2000 sonrası'],
            ['BEF JUL 2000', 'Temmuz 2000 öncesi'],
            ['15 AUG 2000', '15 Ağustos 2000'],
            ['AUG 2000', 'Ağustos 2000'],
            ['ABT AUG 2000', 'yaklaşık Ağustos 2000'],
            ['FROM AUG 2000', 'Ağustos 2000 tarihinden'],
            ['AFT AUG 2000', 'Ağustos 2000 sonrası'],
            ['BEF AUG 2000', 'Ağustos 2000 öncesi'],
            ['15 SEP 2000', '15 Eylül 2000'],
            ['SEP 2000', 'Eylül 2000'],
            ['ABT SEP 2000', 'yaklaşık Eylül 2000'],
            ['FROM SEP 2000', 'Eylül 2000 tarihinden'],
            ['AFT SEP 2000', 'Eylül 2000 sonrası'],
            ['BEF SEP 2000', 'Eylül 2000 öncesi'],
            ['15 OCT 2000', '15 Ekim 2000'],
            ['OCT 2000', 'Ekim 2000'],
            ['ABT OCT 2000', 'yaklaşık Ekim 2000'],
            ['FROM OCT 2000', 'Ekim 2000 tarihinden'],
            ['AFT OCT 2000', 'Ekim 2000 sonrası'],
            ['BEF OCT 2000', 'Ekim 2000 öncesi'],
            ['15 NOV 2000', '15 Kasım 2000'],
            ['NOV 2000', 'Kasım 2000'],
            ['ABT NOV 2000', 'yaklaşık Kasım 2000'],
            ['FROM NOV 2000', 'Kasım 2000 tarihinden'],
            ['AFT NOV 2000', 'Kasım 2000 sonrası'],
            ['BEF NOV 2000', 'Kasım 2000 öncesi'],
            ['15 DEC 2000', '15 Aralık 2000'],
            ['DEC 2000', 'Aralık 2000'],
            ['ABT DEC 2000', 'yaklaşık Aralık 2000'],
            ['FROM DEC 2000', 'Aralık 2000 tarihinden'],
            ['AFT DEC 2000', 'Aralık 2000 sonrası'],
            ['BEF DEC 2000', 'Aralık 2000 öncesi'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'yaklaşık 15 Ocak 2000'],
            ['CAL 15 JAN 2000', 'hesaplanan 15 Ocak 2000'],
            ['EST 15 JAN 2000', 'tahmini 15 Ocak 2000'],
            ['BEF 15 JAN 2000', '15 Ocak 2000 öncesi'],
            ['AFT 15 JAN 2000', '15 Ocak 2000 sonrası'],
            ['FROM 15 JAN 2000', '15 Ocak 2000 tarihinden'],
            ['TO 15 JAN 2000', '15 Ocak 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '15 Ocak 2000 ile 15 Şubat 2000 arasında'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '15 Ocak 2000 tarihinden 15 Şubat 2000 tarihine'],
            ['INT 15 JAN 2000', 'çevrilmiş 15 Ocak 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Ocak M.S. 1700'],
            ['@#DJULIAN@ JAN 1700', 'Ocak M.S. 1700'],
            ['ABT @#DJULIAN@ JAN 1700', 'yaklaşık Ocak M.S. 1700'],
            ['FROM @#DJULIAN@ JAN 1700', 'Ocak M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ JAN 1700', 'Ocak M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ JAN 1700', 'Ocak M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Şubat M.S. 1700'],
            ['@#DJULIAN@ FEB 1700', 'Şubat M.S. 1700'],
            ['ABT @#DJULIAN@ FEB 1700', 'yaklaşık Şubat M.S. 1700'],
            ['FROM @#DJULIAN@ FEB 1700', 'Şubat M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ FEB 1700', 'Şubat M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ FEB 1700', 'Şubat M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Mart M.S. 1700'],
            ['@#DJULIAN@ MAR 1700', 'Mart M.S. 1700'],
            ['ABT @#DJULIAN@ MAR 1700', 'yaklaşık Mart M.S. 1700'],
            ['FROM @#DJULIAN@ MAR 1700', 'Mart M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ MAR 1700', 'Mart M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ MAR 1700', 'Mart M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 APR 1700', '15 Nisan M.S. 1700'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Nisan M.S. 1645/46'],
            ['@#DJULIAN@ APR 1700', 'Nisan M.S. 1700'],
            ['ABT @#DJULIAN@ APR 1700', 'yaklaşık Nisan M.S. 1700'],
            ['FROM @#DJULIAN@ APR 1700', 'Nisan M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ APR 1700', 'Nisan M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ APR 1700', 'Nisan M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Mayıs M.S. 1700'],
            ['@#DJULIAN@ MAY 1700', 'Mayıs M.S. 1700'],
            ['ABT @#DJULIAN@ MAY 1700', 'yaklaşık Mayıs M.S. 1700'],
            ['FROM @#DJULIAN@ MAY 1700', 'Mayıs M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ MAY 1700', 'Mayıs M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ MAY 1700', 'Mayıs M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Haziran M.S. 1700'],
            ['@#DJULIAN@ JUN 1700', 'Haziran M.S. 1700'],
            ['ABT @#DJULIAN@ JUN 1700', 'yaklaşık Haziran M.S. 1700'],
            ['FROM @#DJULIAN@ JUN 1700', 'Haziran M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ JUN 1700', 'Haziran M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ JUN 1700', 'Haziran M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Temmuz M.S. 1700'],
            ['@#DJULIAN@ JUL 1700', 'Temmuz M.S. 1700'],
            ['ABT @#DJULIAN@ JUL 1700', 'yaklaşık Temmuz M.S. 1700'],
            ['FROM @#DJULIAN@ JUL 1700', 'Temmuz M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ JUL 1700', 'Temmuz M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ JUL 1700', 'Temmuz M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Ağustos M.S. 1700'],
            ['@#DJULIAN@ AUG 1700', 'Ağustos M.S. 1700'],
            ['ABT @#DJULIAN@ AUG 1700', 'yaklaşık Ağustos M.S. 1700'],
            ['FROM @#DJULIAN@ AUG 1700', 'Ağustos M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ AUG 1700', 'Ağustos M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ AUG 1700', 'Ağustos M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Eylül M.S. 1700'],
            ['@#DJULIAN@ SEP 1700', 'Eylül M.S. 1700'],
            ['ABT @#DJULIAN@ SEP 1700', 'yaklaşık Eylül M.S. 1700'],
            ['FROM @#DJULIAN@ SEP 1700', 'Eylül M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ SEP 1700', 'Eylül M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ SEP 1700', 'Eylül M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Ekim M.S. 1700'],
            ['@#DJULIAN@ OCT 1700', 'Ekim M.S. 1700'],
            ['ABT @#DJULIAN@ OCT 1700', 'yaklaşık Ekim M.S. 1700'],
            ['FROM @#DJULIAN@ OCT 1700', 'Ekim M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ OCT 1700', 'Ekim M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ OCT 1700', 'Ekim M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Kasım M.S. 1700'],
            ['@#DJULIAN@ NOV 1700', 'Kasım M.S. 1700'],
            ['ABT @#DJULIAN@ NOV 1700', 'yaklaşık Kasım M.S. 1700'],
            ['FROM @#DJULIAN@ NOV 1700', 'Kasım M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ NOV 1700', 'Kasım M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ NOV 1700', 'Kasım M.S. 1700 öncesi'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Aralık M.S. 1700'],
            ['@#DJULIAN@ DEC 1700', 'Aralık M.S. 1700'],
            ['ABT @#DJULIAN@ DEC 1700', 'yaklaşık Aralık M.S. 1700'],
            ['FROM @#DJULIAN@ DEC 1700', 'Aralık M.S. 1700 tarihinden'],
            ['AFT @#DJULIAN@ DEC 1700', 'Aralık M.S. 1700 sonrası'],
            ['BEF @#DJULIAN@ DEC 1700', 'Aralık M.S. 1700 öncesi'],
            ['@#DJULIAN@ 1700', 'M.S. 1700'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'yaklaşık 15 Ocak M.S. 1700'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'hesaplanan 15 Ocak M.S. 1700'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'tahmini 15 Ocak M.S. 1700'],
            ['BEF @#DJULIAN@ 15 JAN 1700', '15 Ocak M.S. 1700 öncesi'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '15 Ocak M.S. 1700 sonrası'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '15 Ocak M.S. 1700 tarihinden'],
            ['TO @#DJULIAN@ 15 JAN 1700', '15 Ocak M.S. 1700'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '15 Ocak M.S. 1700 ile 15 Şubat M.S. 1700 arasında'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '15 Ocak M.S. 1700 tarihinden 15 Şubat M.S. 1700 tarihine'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'çevrilmiş 15 Ocak M.S. 1700'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tişri 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tişri 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'yaklaşık Tişri 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'Tişri 5765 tarihinden'],
            ['AFT @#DHEBREW@ TSH 5765', 'Tişri 5765 sonrası'],
            ['BEF @#DHEBREW@ TSH 5765', 'Tişri 5765 öncesi'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heşvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heşvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'yaklaşık Heşvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'Heşvan 5765 tarihinden'],
            ['AFT @#DHEBREW@ CSH 5765', 'Heşvan 5765 sonrası'],
            ['BEF @#DHEBREW@ CSH 5765', 'Heşvan 5765 öncesi'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'yaklaşık Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'Kislev 5765 tarihinden'],
            ['AFT @#DHEBREW@ KSL 5765', 'Kislev 5765 sonrası'],
            ['BEF @#DHEBREW@ KSL 5765', 'Kislev 5765 öncesi'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'yaklaşık Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'Tevet 5765 tarihinden'],
            ['AFT @#DHEBREW@ TVT 5765', 'Tevet 5765 sonrası'],
            ['BEF @#DHEBREW@ TVT 5765', 'Tevet 5765 öncesi'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Şevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Şevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'yaklaşık Şevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'Şevat 5765 tarihinden'],
            ['AFT @#DHEBREW@ SHV 5765', 'Şevat 5765 sonrası'],
            ['BEF @#DHEBREW@ SHV 5765', 'Şevat 5765 öncesi'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'yaklaşık Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'Adar I 5765 tarihinden'],
            ['AFT @#DHEBREW@ ADR 5765', 'Adar I 5765 sonrası'],
            ['BEF @#DHEBREW@ ADR 5765', 'Adar I 5765 öncesi'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'yaklaşık Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'Adar II 5765 tarihinden'],
            ['AFT @#DHEBREW@ ADS 5765', 'Adar II 5765 sonrası'],
            ['BEF @#DHEBREW@ ADS 5765', 'Adar II 5765 öncesi'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'yaklaşık Nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'Nisan 5765 tarihinden'],
            ['AFT @#DHEBREW@ NSN 5765', 'Nisan 5765 sonrası'],
            ['BEF @#DHEBREW@ NSN 5765', 'Nisan 5765 öncesi'],
            ['@#DHEBREW@ 15 IYR 5765', '15 İyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'İyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'yaklaşık İyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'İyar 5765 tarihinden'],
            ['AFT @#DHEBREW@ IYR 5765', 'İyar 5765 sonrası'],
            ['BEF @#DHEBREW@ IYR 5765', 'İyar 5765 öncesi'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'yaklaşık Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'Sivan 5765 tarihinden'],
            ['AFT @#DHEBREW@ SVN 5765', 'Sivan 5765 sonrası'],
            ['BEF @#DHEBREW@ SVN 5765', 'Sivan 5765 öncesi'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'yaklaşık Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'Tamuz 5765 tarihinden'],
            ['AFT @#DHEBREW@ TMZ 5765', 'Tamuz 5765 sonrası'],
            ['BEF @#DHEBREW@ TMZ 5765', 'Tamuz 5765 öncesi'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'yaklaşık Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'Av 5765 tarihinden'],
            ['AFT @#DHEBREW@ AAV 5765', 'Av 5765 sonrası'],
            ['BEF @#DHEBREW@ AAV 5765', 'Av 5765 öncesi'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'yaklaşık Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'Elul 5765 tarihinden'],
            ['AFT @#DHEBREW@ ELL 5765', 'Elul 5765 sonrası'],
            ['BEF @#DHEBREW@ ELL 5765', 'Elul 5765 öncesi'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'yaklaşık 15 Tişri 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'hesaplanan 15 Tişri 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'tahmini 15 Tişri 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '15 Tişri 5765 öncesi'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '15 Tişri 5765 sonrası'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '15 Tişri 5765 tarihinden'],
            ['TO @#DHEBREW@ 15 TSH 5765', '15 Tişri 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '15 Tişri 5765 ile 15 Heşvan 5765 arasında'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '15 Tişri 5765 tarihinden 15 Heşvan 5765 tarihine'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'çevrilmiş 15 Tişri 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'yaklaşık Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'Vendémiaire An XII tarihinden'],
            ['AFT @#DFRENCH R@ VEND 12', 'Vendémiaire An XII sonrası'],
            ['BEF @#DFRENCH R@ VEND 12', 'Vendémiaire An XII öncesi'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'yaklaşık Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'Brumaire An XII tarihinden'],
            ['AFT @#DFRENCH R@ BRUM 12', 'Brumaire An XII sonrası'],
            ['BEF @#DFRENCH R@ BRUM 12', 'Brumaire An XII öncesi'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'yaklaşık Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'Frimaire An XII tarihinden'],
            ['AFT @#DFRENCH R@ FRIM 12', 'Frimaire An XII sonrası'],
            ['BEF @#DFRENCH R@ FRIM 12', 'Frimaire An XII öncesi'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'yaklaşık Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'Nivôse An XII tarihinden'],
            ['AFT @#DFRENCH R@ NIVO 12', 'Nivôse An XII sonrası'],
            ['BEF @#DFRENCH R@ NIVO 12', 'Nivôse An XII öncesi'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'yaklaşık Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'Pluviôse An XII tarihinden'],
            ['AFT @#DFRENCH R@ PLUV 12', 'Pluviôse An XII sonrası'],
            ['BEF @#DFRENCH R@ PLUV 12', 'Pluviôse An XII öncesi'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'yaklaşık Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'Ventôse An XII tarihinden'],
            ['AFT @#DFRENCH R@ VENT 12', 'Ventôse An XII sonrası'],
            ['BEF @#DFRENCH R@ VENT 12', 'Ventôse An XII öncesi'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'yaklaşık Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'Germinal An XII tarihinden'],
            ['AFT @#DFRENCH R@ GERM 12', 'Germinal An XII sonrası'],
            ['BEF @#DFRENCH R@ GERM 12', 'Germinal An XII öncesi'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'yaklaşık Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'Floréal An XII tarihinden'],
            ['AFT @#DFRENCH R@ FLOR 12', 'Floréal An XII sonrası'],
            ['BEF @#DFRENCH R@ FLOR 12', 'Floréal An XII öncesi'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'yaklaşık Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'Prairial An XII tarihinden'],
            ['AFT @#DFRENCH R@ PRAI 12', 'Prairial An XII sonrası'],
            ['BEF @#DFRENCH R@ PRAI 12', 'Prairial An XII öncesi'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'yaklaşık Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'Messidor An XII tarihinden'],
            ['AFT @#DFRENCH R@ MESS 12', 'Messidor An XII sonrası'],
            ['BEF @#DFRENCH R@ MESS 12', 'Messidor An XII öncesi'],
            ['@#DFRENCH R@ 15 THER 12', '15 Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'yaklaşık Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'Thermidor An XII tarihinden'],
            ['AFT @#DFRENCH R@ THER 12', 'Thermidor An XII sonrası'],
            ['BEF @#DFRENCH R@ THER 12', 'Thermidor An XII öncesi'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'yaklaşık Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'Fructidor An XII tarihinden'],
            ['AFT @#DFRENCH R@ FRUC 12', 'Fructidor An XII sonrası'],
            ['BEF @#DFRENCH R@ FRUC 12', 'Fructidor An XII öncesi'],
            ['@#DFRENCH R@ 15 COMP 12', '15 ek günler An XII'],
            ['@#DFRENCH R@ COMP 12', 'ek günler An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'yaklaşık ek günler An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'ek günler An XII tarihinden'],
            ['AFT @#DFRENCH R@ COMP 12', 'ek günler An XII sonrası'],
            ['BEF @#DFRENCH R@ COMP 12', 'ek günler An XII öncesi'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'yaklaşık 15 Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'hesaplanan 15 Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'tahmini 15 Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII öncesi'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII sonrası'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII tarihinden'],
            ['TO @#DFRENCH R@ 15 VEND 12', '15 Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '15 Vendémiaire An XII ile 15 Brumaire An XII arasında'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '15 Vendémiaire An XII tarihinden 15 Brumaire An XII tarihine'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'çevrilmiş 15 Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharrem 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharrem 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'yaklaşık Muharrem 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'Muharrem 1425 tarihinden'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'Muharrem 1425 sonrası'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'Muharrem 1425 öncesi'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safer 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safer 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'yaklaşık Safer 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'Safer 1425 tarihinden'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'Safer 1425 sonrası'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'Safer 1425 öncesi'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rebiülevvel 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rebiülevvel 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'yaklaşık Rebiülevvel 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'Rebiülevvel 1425 tarihinden'],
            ['AFT @#DHIJRI@ RABIA 1425', 'Rebiülevvel 1425 sonrası'],
            ['BEF @#DHIJRI@ RABIA 1425', 'Rebiülevvel 1425 öncesi'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rebiülahir 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rebiülahir 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'yaklaşık Rebiülahir 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'Rebiülahir 1425 tarihinden'],
            ['AFT @#DHIJRI@ RABIT 1425', 'Rebiülahir 1425 sonrası'],
            ['BEF @#DHIJRI@ RABIT 1425', 'Rebiülahir 1425 öncesi'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Cemaziyelevvel 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Cemaziyelevvel 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'yaklaşık Cemaziyelevvel 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'Cemaziyelevvel 1425 tarihinden'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'Cemaziyelevvel 1425 sonrası'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'Cemaziyelevvel 1425 öncesi'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Cemaziyelahir 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Cemaziyelahir 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'yaklaşık Cemaziyelahir 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'Cemaziyelahir 1425 tarihinden'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'Cemaziyelahir 1425 sonrası'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'Cemaziyelahir 1425 öncesi'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Receb 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Receb 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'yaklaşık Receb 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'Receb 1425 tarihinden'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'Receb 1425 sonrası'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'Receb 1425 öncesi'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Şaban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Şaban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'yaklaşık Şaban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'Şaban 1425 tarihinden'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'Şaban 1425 sonrası'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'Şaban 1425 öncesi'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramazan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramazan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'yaklaşık Ramazan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'Ramazan 1425 tarihinden'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'Ramazan 1425 sonrası'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'Ramazan 1425 öncesi'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Şevval 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Şevval 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'yaklaşık Şevval 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'Şevval 1425 tarihinden'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'Şevval 1425 sonrası'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'Şevval 1425 öncesi'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Zilkade 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Zilkade 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'yaklaşık Zilkade 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'Zilkade 1425 tarihinden'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'Zilkade 1425 sonrası'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'Zilkade 1425 öncesi'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'yaklaşık 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', '1425 tarihinden'],
            ['AFT @#DHIJRI@ DHUAL 1425', '1425 sonrası'],
            ['BEF @#DHIJRI@ DHUAL 1425', '1425 öncesi'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'yaklaşık 15 Muharrem 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'hesaplanan 15 Muharrem 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'tahmini 15 Muharrem 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '15 Muharrem 1425 öncesi'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '15 Muharrem 1425 sonrası'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '15 Muharrem 1425 tarihinden'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '15 Muharrem 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '15 Muharrem 1425 ile 15 Safer 1425 arasında'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '15 Muharrem 1425 tarihinden 15 Safer 1425 tarihine'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'çevrilmiş 15 Muharrem 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Ferverdin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Ferverdin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'yaklaşık Ferverdin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'Ferverdin 1384 tarihinden'],
            ['AFT @#DJALALI@ FARVA 1384', 'Ferverdin 1384 sonrası'],
            ['BEF @#DJALALI@ FARVA 1384', 'Ferverdin 1384 öncesi'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehişt 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehişt 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'yaklaşık Ordibehişt 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'Ordibehişt 1384 tarihinden'],
            ['AFT @#DJALALI@ ORDIB 1384', 'Ordibehişt 1384 sonrası'],
            ['BEF @#DJALALI@ ORDIB 1384', 'Ordibehişt 1384 öncesi'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Hordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Hordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'yaklaşık Hordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'Hordad 1384 tarihinden'],
            ['AFT @#DJALALI@ KHORD 1384', 'Hordad 1384 sonrası'],
            ['BEF @#DJALALI@ KHORD 1384', 'Hordad 1384 öncesi'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'yaklaşık Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'Tir 1384 tarihinden'],
            ['AFT @#DJALALI@ TIR 1384', 'Tir 1384 sonrası'],
            ['BEF @#DJALALI@ TIR 1384', 'Tir 1384 öncesi'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'yaklaşık Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'Mordad 1384 tarihinden'],
            ['AFT @#DJALALI@ MORDA 1384', 'Mordad 1384 sonrası'],
            ['BEF @#DJALALI@ MORDA 1384', 'Mordad 1384 öncesi'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Şehriver 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Şehriver 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'yaklaşık Şehriver 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'Şehriver 1384 tarihinden'],
            ['AFT @#DJALALI@ SHAHR 1384', 'Şehriver 1384 sonrası'],
            ['BEF @#DJALALI@ SHAHR 1384', 'Şehriver 1384 öncesi'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'yaklaşık Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'Mehr 1384 tarihinden'],
            ['AFT @#DJALALI@ MEHR 1384', 'Mehr 1384 sonrası'],
            ['BEF @#DJALALI@ MEHR 1384', 'Mehr 1384 öncesi'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'yaklaşık Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'Aban 1384 tarihinden'],
            ['AFT @#DJALALI@ ABAN 1384', 'Aban 1384 sonrası'],
            ['BEF @#DJALALI@ ABAN 1384', 'Aban 1384 öncesi'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azer 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azer 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'yaklaşık Azer 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'Azer 1384 tarihinden'],
            ['AFT @#DJALALI@ AZAR 1384', 'Azer 1384 sonrası'],
            ['BEF @#DJALALI@ AZAR 1384', 'Azer 1384 öncesi'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'yaklaşık Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'Dey 1384 tarihinden'],
            ['AFT @#DJALALI@ DEY 1384', 'Dey 1384 sonrası'],
            ['BEF @#DJALALI@ DEY 1384', 'Dey 1384 öncesi'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Behmen 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Behmen 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'yaklaşık Behmen 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'Behmen 1384 tarihinden'],
            ['AFT @#DJALALI@ BAHMA 1384', 'Behmen 1384 sonrası'],
            ['BEF @#DJALALI@ BAHMA 1384', 'Behmen 1384 öncesi'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfend 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfend 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'yaklaşık Esfend 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'Esfend 1384 tarihinden'],
            ['AFT @#DJALALI@ ESFAN 1384', 'Esfend 1384 sonrası'],
            ['BEF @#DJALALI@ ESFAN 1384', 'Esfend 1384 öncesi'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'yaklaşık 15 Ferverdin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'hesaplanan 15 Ferverdin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'tahmini 15 Ferverdin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '15 Ferverdin 1384 öncesi'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '15 Ferverdin 1384 sonrası'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '15 Ferverdin 1384 tarihinden'],
            ['TO @#DJALALI@ 15 FARVA 1384', '15 Ferverdin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '15 Ferverdin 1384 ile 15 Ordibehişt 1384 arasında'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '15 Ferverdin 1384 tarihinden 15 Ordibehişt 1384 tarihine'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'çevrilmiş 15 Ferverdin 1384'],
        ];
    }
}
