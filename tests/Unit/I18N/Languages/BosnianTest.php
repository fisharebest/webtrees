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
use Fisharebest\Webtrees\I18N\Languages\Bosnian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Bosnian::class)]
class BosnianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Bosnian();
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
        self::assertSame('bs', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('bosanski', self::language()->endonym());
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
        self::assertSame('one i two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two i three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one ili two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two ili three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('supruga', 'suprug', [$husband, $fm, $wife]);
        self::assertRelationshipNames('bivši suprug', 'bivša supruga', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('vjerenica', 'vjerenik', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('majka', 'sin', [$son, $fm, $wife]);
        self::assertRelationshipNames('otac', 'sin', [$son, $fm, $husband]);
        self::assertRelationshipNames('majka', 'kći', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('posvojiteljica', 'posvojeni sin', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('posvojitelj', 'posvojeni sin', [$adoptedSon, $fd, $exHusband]);

        // Siblings (son born 2000 is older than daughter born 2001)
        self::assertRelationshipNames('mlađa sestra', 'stariji brat', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('polubrat', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('očuh', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('pastorka', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('punica', 'zet', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('punac', 'zet', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('snaha', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('nana', 'unuk', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('dedo', 'unuk', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) — pra prefix
        self::assertRelationshipName('pradedo', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('pranana', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tetka', 'nećak', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('amidža', 'nećak', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('nećakinja', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nećak', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('sestrična', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('bratić', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) — pra prefix
        self::assertRelationshipName('pratetka', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('praamidža', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15. januara 2000'],
            ['JAN 2000', 'Januar 2000'],
            ['ABT JAN 2000', 'o januara 2000'],
            ['FROM JAN 2000', 'iz januara 2000'],
            ['AFT JAN 2000', 'poslije januaru 2000'],
            ['BEF JAN 2000', 'prije januarom 2000'],
            ['15 FEB 2000', '15. februara 2000'],
            ['FEB 2000', 'Februar 2000'],
            ['ABT FEB 2000', 'o februara 2000'],
            ['FROM FEB 2000', 'iz februara 2000'],
            ['AFT FEB 2000', 'poslije februaru 2000'],
            ['BEF FEB 2000', 'prije februarom 2000'],
            ['15 MAR 2000', '15. marta 2000'],
            ['MAR 2000', 'Mart 2000'],
            ['ABT MAR 2000', 'o marta 2000'],
            ['FROM MAR 2000', 'iz marta 2000'],
            ['AFT MAR 2000', 'poslije martu 2000'],
            ['BEF MAR 2000', 'prije martom 2000'],
            ['15 APR 2000', '15. aprila 2000'],
            ['APR 2000', 'April 2000'],
            ['ABT APR 2000', 'o aprila 2000'],
            ['FROM APR 2000', 'iz aprila 2000'],
            ['AFT APR 2000', 'poslije aprilu 2000'],
            ['BEF APR 2000', 'prije aprilom 2000'],
            ['15 MAY 2000', '15. maja 2000'],
            ['MAY 2000', 'Maj 2000'],
            ['ABT MAY 2000', 'o maja 2000'],
            ['FROM MAY 2000', 'iz maja 2000'],
            ['AFT MAY 2000', 'poslije maju 2000'],
            ['BEF MAY 2000', 'prije majem 2000'],
            ['15 JUN 2000', '15. juna 2000'],
            ['JUN 2000', 'Jun 2000'],
            ['ABT JUN 2000', 'o juna 2000'],
            ['FROM JUN 2000', 'iz juna 2000'],
            ['AFT JUN 2000', 'poslije junu 2000'],
            ['BEF JUN 2000', 'prije junom 2000'],
            ['15 JUL 2000', '15. jula 2000'],
            ['JUL 2000', 'Jul 2000'],
            ['ABT JUL 2000', 'o jula 2000'],
            ['FROM JUL 2000', 'iz jula 2000'],
            ['AFT JUL 2000', 'poslije julu 2000'],
            ['BEF JUL 2000', 'prije julom 2000'],
            ['15 AUG 2000', '15. avgusta 2000'],
            ['AUG 2000', 'Avgust 2000'],
            ['ABT AUG 2000', 'o avgusta 2000'],
            ['FROM AUG 2000', 'iz avgusta 2000'],
            ['AFT AUG 2000', 'poslije avgustu 2000'],
            ['BEF AUG 2000', 'prije avgustom 2000'],
            ['15 SEP 2000', '15. septembara 2000'],
            ['SEP 2000', 'Septembar 2000'],
            ['ABT SEP 2000', 'o septembara 2000'],
            ['FROM SEP 2000', 'iz septembara 2000'],
            ['AFT SEP 2000', 'poslije septembru 2000'],
            ['BEF SEP 2000', 'prije septembrom 2000'],
            ['15 OCT 2000', '15. oktobara 2000'],
            ['OCT 2000', 'Oktobar 2000'],
            ['ABT OCT 2000', 'o oktobara 2000'],
            ['FROM OCT 2000', 'iz oktobara 2000'],
            ['AFT OCT 2000', 'poslije oktobru 2000'],
            ['BEF OCT 2000', 'prije oktobrom 2000'],
            ['15 NOV 2000', '15. novembara 2000'],
            ['NOV 2000', 'Novembar 2000'],
            ['ABT NOV 2000', 'o novembara 2000'],
            ['FROM NOV 2000', 'iz novembara 2000'],
            ['AFT NOV 2000', 'poslije novembru 2000'],
            ['BEF NOV 2000', 'prije novembrom 2000'],
            ['15 DEC 2000', '15. decembra 2000'],
            ['DEC 2000', 'Decembar 2000'],
            ['ABT DEC 2000', 'o decembra 2000'],
            ['FROM DEC 2000', 'iz decembra 2000'],
            ['AFT DEC 2000', 'poslije decembru 2000'],
            ['BEF DEC 2000', 'prije decembrom 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'o 15. januara 2000'],
            ['CAL 15 JAN 2000', 'izračunato 15. januara 2000'],
            ['EST 15 JAN 2000', 'procjenjeno 15. januara 2000'],
            ['BEF 15 JAN 2000', 'prije 15. januara 2000'],
            ['AFT 15 JAN 2000', 'poslije 15. januara 2000'],
            ['FROM 15 JAN 2000', 'iz 15. januara 2000'],
            ['TO 15 JAN 2000', 'u 15. januara 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'između 15. januara 2000 i 15. februara 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'od 15. januara 2000 do 15. februara 2000'],
            ['INT 15 JAN 2000', 'interpretirano 15. januara 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. januara 1700 n.e'],
            ['@#DJULIAN@ JAN 1700', 'Januar 1700 n.e'],
            ['ABT @#DJULIAN@ JAN 1700', 'o januara 1700 n.e'],
            ['FROM @#DJULIAN@ JAN 1700', 'iz januara 1700 n.e'],
            ['AFT @#DJULIAN@ JAN 1700', 'poslije januaru 1700 n.e'],
            ['BEF @#DJULIAN@ JAN 1700', 'prije januarom 1700 n.e'],
            ['@#DJULIAN@ 15 FEB 1700', '15. februara 1700 n.e'],
            ['@#DJULIAN@ FEB 1700', 'Februar 1700 n.e'],
            ['ABT @#DJULIAN@ FEB 1700', 'o februara 1700 n.e'],
            ['FROM @#DJULIAN@ FEB 1700', 'iz februara 1700 n.e'],
            ['AFT @#DJULIAN@ FEB 1700', 'poslije februaru 1700 n.e'],
            ['BEF @#DJULIAN@ FEB 1700', 'prije februarom 1700 n.e'],
            ['@#DJULIAN@ 15 MAR 1700', '15. marta 1700 n.e'],
            ['@#DJULIAN@ MAR 1700', 'Mart 1700 n.e'],
            ['ABT @#DJULIAN@ MAR 1700', 'o marta 1700 n.e'],
            ['FROM @#DJULIAN@ MAR 1700', 'iz marta 1700 n.e'],
            ['AFT @#DJULIAN@ MAR 1700', 'poslije martu 1700 n.e'],
            ['BEF @#DJULIAN@ MAR 1700', 'prije martom 1700 n.e'],
            ['@#DJULIAN@ 15 APR 1700', '15. aprila 1700 n.e'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. aprila 1645/46 n.e'],
            ['@#DJULIAN@ APR 1700', 'April 1700 n.e'],
            ['ABT @#DJULIAN@ APR 1700', 'o aprila 1700 n.e'],
            ['FROM @#DJULIAN@ APR 1700', 'iz aprila 1700 n.e'],
            ['AFT @#DJULIAN@ APR 1700', 'poslije aprilu 1700 n.e'],
            ['BEF @#DJULIAN@ APR 1700', 'prije aprilom 1700 n.e'],
            ['@#DJULIAN@ 15 MAY 1700', '15. maja 1700 n.e'],
            ['@#DJULIAN@ MAY 1700', 'Maj 1700 n.e'],
            ['ABT @#DJULIAN@ MAY 1700', 'o maja 1700 n.e'],
            ['FROM @#DJULIAN@ MAY 1700', 'iz maja 1700 n.e'],
            ['AFT @#DJULIAN@ MAY 1700', 'poslije maju 1700 n.e'],
            ['BEF @#DJULIAN@ MAY 1700', 'prije majem 1700 n.e'],
            ['@#DJULIAN@ 15 JUN 1700', '15. juna 1700 n.e'],
            ['@#DJULIAN@ JUN 1700', 'Jun 1700 n.e'],
            ['ABT @#DJULIAN@ JUN 1700', 'o juna 1700 n.e'],
            ['FROM @#DJULIAN@ JUN 1700', 'iz juna 1700 n.e'],
            ['AFT @#DJULIAN@ JUN 1700', 'poslije junu 1700 n.e'],
            ['BEF @#DJULIAN@ JUN 1700', 'prije junom 1700 n.e'],
            ['@#DJULIAN@ 15 JUL 1700', '15. jula 1700 n.e'],
            ['@#DJULIAN@ JUL 1700', 'Jul 1700 n.e'],
            ['ABT @#DJULIAN@ JUL 1700', 'o jula 1700 n.e'],
            ['FROM @#DJULIAN@ JUL 1700', 'iz jula 1700 n.e'],
            ['AFT @#DJULIAN@ JUL 1700', 'poslije julu 1700 n.e'],
            ['BEF @#DJULIAN@ JUL 1700', 'prije julom 1700 n.e'],
            ['@#DJULIAN@ 15 AUG 1700', '15. avgusta 1700 n.e'],
            ['@#DJULIAN@ AUG 1700', 'Avgust 1700 n.e'],
            ['ABT @#DJULIAN@ AUG 1700', 'o avgusta 1700 n.e'],
            ['FROM @#DJULIAN@ AUG 1700', 'iz avgusta 1700 n.e'],
            ['AFT @#DJULIAN@ AUG 1700', 'poslije avgustu 1700 n.e'],
            ['BEF @#DJULIAN@ AUG 1700', 'prije avgustom 1700 n.e'],
            ['@#DJULIAN@ 15 SEP 1700', '15. septembara 1700 n.e'],
            ['@#DJULIAN@ SEP 1700', 'Septembar 1700 n.e'],
            ['ABT @#DJULIAN@ SEP 1700', 'o septembara 1700 n.e'],
            ['FROM @#DJULIAN@ SEP 1700', 'iz septembara 1700 n.e'],
            ['AFT @#DJULIAN@ SEP 1700', 'poslije septembru 1700 n.e'],
            ['BEF @#DJULIAN@ SEP 1700', 'prije septembrom 1700 n.e'],
            ['@#DJULIAN@ 15 OCT 1700', '15. oktobara 1700 n.e'],
            ['@#DJULIAN@ OCT 1700', 'Oktobar 1700 n.e'],
            ['ABT @#DJULIAN@ OCT 1700', 'o oktobara 1700 n.e'],
            ['FROM @#DJULIAN@ OCT 1700', 'iz oktobara 1700 n.e'],
            ['AFT @#DJULIAN@ OCT 1700', 'poslije oktobru 1700 n.e'],
            ['BEF @#DJULIAN@ OCT 1700', 'prije oktobrom 1700 n.e'],
            ['@#DJULIAN@ 15 NOV 1700', '15. novembara 1700 n.e'],
            ['@#DJULIAN@ NOV 1700', 'Novembar 1700 n.e'],
            ['ABT @#DJULIAN@ NOV 1700', 'o novembara 1700 n.e'],
            ['FROM @#DJULIAN@ NOV 1700', 'iz novembara 1700 n.e'],
            ['AFT @#DJULIAN@ NOV 1700', 'poslije novembru 1700 n.e'],
            ['BEF @#DJULIAN@ NOV 1700', 'prije novembrom 1700 n.e'],
            ['@#DJULIAN@ 15 DEC 1700', '15. decembra 1700 n.e'],
            ['@#DJULIAN@ DEC 1700', 'Decembar 1700 n.e'],
            ['ABT @#DJULIAN@ DEC 1700', 'o decembra 1700 n.e'],
            ['FROM @#DJULIAN@ DEC 1700', 'iz decembra 1700 n.e'],
            ['AFT @#DJULIAN@ DEC 1700', 'poslije decembru 1700 n.e'],
            ['BEF @#DJULIAN@ DEC 1700', 'prije decembrom 1700 n.e'],
            ['@#DJULIAN@ 1700', '1700 n.e'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'o 15. januara 1700 n.e'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'izračunato 15. januara 1700 n.e'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'procjenjeno 15. januara 1700 n.e'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'prije 15. januara 1700 n.e'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'poslije 15. januara 1700 n.e'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'iz 15. januara 1700 n.e'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'u 15. januara 1700 n.e'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'između 15. januara 1700 n.e i 15. februara 1700 n.e'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'od 15. januara 1700 n.e do 15. februara 1700 n.e'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretirano 15. januara 1700 n.e'],
            ['@#DHEBREW@ 15 TSH 5765', '15. Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'o Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'iz Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'poslije Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'prije Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. Heshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'o Heshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'iz Heshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'poslije Heshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'prije Heshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'o Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'iz Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'poslije Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'prije Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'o Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'iz Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'poslije Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'prije Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'o Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'iz Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'poslije Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'prije Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'o Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'iz Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'poslije Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'prije Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'o Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'iz Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'poslije Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'prije Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'o Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'iz Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'poslije Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'prije Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'o Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'iz Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'poslije Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'prije Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'o Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'iz Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'poslije Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'prije Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'o Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'iz Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'poslije Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'prije Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'o Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'iz Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'poslije Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'prije Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'o Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'iz Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'poslije Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'prije Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'o 15. Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'izračunato 15. Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'procjenjeno 15. Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'prije 15. Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'poslije 15. Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'iz 15. Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'u 15. Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'između 15. Tishrei 5765 i 15. Heshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'od 15. Tishrei 5765 do 15. Heshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretirano 15. Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'o Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'iz Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'poslije Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'prije Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'o Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'iz Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'poslije Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'prije Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'o Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'iz Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'poslije Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'prije Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'o Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'iz Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'poslije Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'prije Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'o Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'iz Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'poslije Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'prije Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'o Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'iz Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'poslije Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'prije Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'o Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'iz Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'poslije Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'prije Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'o Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'iz Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'poslije Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'prije Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'o Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'iz Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'poslije Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'prije Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'o Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'iz Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'poslije Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'prije Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'o Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'iz Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'poslije Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'prije Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'o Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'iz Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'poslije Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'prije Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'o jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'iz jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'poslije jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'prije jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'o 15. Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'izračunato 15. Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'procjenjeno 15. Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'prije 15. Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'poslije 15. Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'iz 15. Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'u 15. Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'između 15. Vendémiaire An XII i 15. Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'od 15. Vendémiaire An XII do 15. Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretirano 15. Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. Muharrem 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharrem 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'o Muharrem 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'iz Muharrem 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'poslije Muharrem 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'prije Muharrem 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. Safera 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safer 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'o Safera 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'iz Safera 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'poslije Saferu 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'prije Saferom 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rebiul-evvela 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rebiul-evvel 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'o Rebiul-evvela 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'iz Rebiul-evvela 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'poslije Rebiul-evvelu 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'prije Rebiul-evvelom 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rebiul-ahira 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rebiul-ahir 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'o Rebiul-ahira 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'iz Rebiul-ahira 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'poslije Rebiul-ahiru 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'prije Rebiul-ahirom 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. Džumade-l-ula 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Džumade-l-ula 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'o Džumade-l-ula 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'iz Džumade-l-ula 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'poslije Džumade-l-ulau 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'prije Džumade-l-ulaom 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. Džumade-l-uhra 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Džumade-l-uhra 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'o Džumade-l-uhra 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'iz Džumade-l-uhra 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'poslije Džumade-l-uhrau 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'prije Džumade-l-uhraom 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. Redžeba 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Redžeb 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'o Redžeba 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'iz Redžeba 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'poslije Redžebu 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'prije Redžebom 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. Ša’bana 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Ša’ban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'o Ša’bana 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'iz Ša’bana 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'poslije Ša’banu 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'prije Ša’banom 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. Ramazana 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramazan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'o Ramazana 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'iz Ramazana 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'poslije Ramazanu 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'prije Ramazanom 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. Ševvala 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Ševval 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'o Ševvala 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'iz Ševvala 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'poslije Ševvalu 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'prije Ševvalom 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. Zu-l-ka’dea 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Zu-l-ka’de 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'o Zu-l-ka’dea 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'iz Zu-l-ka’dea 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'poslije Zu-l-ka’deu 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'prije Zu-l-ka’deom 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'o 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'iz 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'poslije 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'prije 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'o 15. Muharrem 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'izračunato 15. Muharrem 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'procjenjeno 15. Muharrem 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'prije 15. Muharrem 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'poslije 15. Muharrem 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'iz 15. Muharrem 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'u 15. Muharrem 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'između 15. Muharrem 1425 i 15. Safera 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'od 15. Muharrem 1425 do 15. Safera 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretirano 15. Muharrem 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'o Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'iz Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'poslije Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'prije Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'o Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'iz Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'poslije Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'prije Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'o Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'iz Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'poslije Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'prije Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'o Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'iz Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'poslije Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'prije Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'o Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'iz Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'poslije Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'prije Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'o Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'iz Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'poslije Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'prije Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'o Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'iz Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'poslije Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'prije Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Ābān 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'o Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'iz Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'poslije Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'prije Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'o Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'iz Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'poslije Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'prije Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'o Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'iz Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'poslije Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'prije Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'o Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'iz Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'poslije Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'prije Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'o Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'iz Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'poslije Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'prije Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'o 15. Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'izračunato 15. Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'procjenjeno 15. Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'prije 15. Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'poslije 15. Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'iz 15. Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'u 15. Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'između 15. Farvardin 1384 i 15. Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'od 15. Farvardin 1384 do 15. Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretirano 15. Farvardin 1384'],
        ];
    }
}
