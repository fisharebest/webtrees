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
use Fisharebest\Webtrees\I18N\Languages\Slovenian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Slovenian::class)]
class SlovenianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Slovenian();
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
        self::assertSame(['A', 'B', 'C', 'Č', 'Ć', 'D', 'Đ', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ž'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('sl', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('slovenščina', self::language()->endonym());
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
        self::assertSame('−123.456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('−123.456,0789 %', self::language()->percentage(-1234.560789));
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
        self::assertSame('one in two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two in three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one ali two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two ali three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('žena', 'mož', [$husband, $fm, $wife]);
        self::assertRelationshipNames('bivši mož', 'bivša žena', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('zaročenka', 'zaročenec', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mati', 'sin', [$son, $fm, $wife]);
        self::assertRelationshipNames('oče', 'sin', [$son, $fm, $husband]);
        self::assertRelationshipNames('mati', 'hči', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('posvojiteljica', 'posvojeni sin', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('posvojitelj', 'posvojeni sin', [$adoptedSon, $fd, $exHusband]);

        // Siblings (son born 2000 is older than daughter born 2001)
        self::assertRelationshipNames('mlajša sestra', 'starejši brat', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('polbrat', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('očim', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('pastorka', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('tašča', 'zet', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('tast', 'zet', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('snaha', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('babica', 'vnuk', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('dedek', 'vnuk', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) — pra prefix
        self::assertRelationshipName('pradedek', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('prababica', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('teta', 'nečak', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('stric', 'nečak', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('nečakinja', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nečak', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('sestrična', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('bratranec', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) — pra prefix
        self::assertRelationshipName('prateta', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('prastric', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15. januarja 2000'],
            ['JAN 2000', 'januar 2000'],
            ['ABT JAN 2000', 'okoli januarja 2000'],
            ['FROM JAN 2000', 'od januarja 2000'],
            ['AFT JAN 2000', 'po januarju 2000'],
            ['BEF JAN 2000', 'pred januarjem 2000'],
            ['15 FEB 2000', '15. februarja 2000'],
            ['FEB 2000', 'februar 2000'],
            ['ABT FEB 2000', 'okoli februarja 2000'],
            ['FROM FEB 2000', 'od februarja 2000'],
            ['AFT FEB 2000', 'po februarju 2000'],
            ['BEF FEB 2000', 'pred februarjem 2000'],
            ['15 MAR 2000', '15. marca 2000'],
            ['MAR 2000', 'marec 2000'],
            ['ABT MAR 2000', 'okoli marca 2000'],
            ['FROM MAR 2000', 'od marca 2000'],
            ['AFT MAR 2000', 'po marcu 2000'],
            ['BEF MAR 2000', 'pred marcem 2000'],
            ['15 APR 2000', '15. aprila 2000'],
            ['APR 2000', 'april 2000'],
            ['ABT APR 2000', 'okoli aprila 2000'],
            ['FROM APR 2000', 'od aprila 2000'],
            ['AFT APR 2000', 'po aprilu 2000'],
            ['BEF APR 2000', 'pred aprilom 2000'],
            ['15 MAY 2000', '15. maja 2000'],
            ['MAY 2000', 'maj 2000'],
            ['ABT MAY 2000', 'okoli maja 2000'],
            ['FROM MAY 2000', 'od maja 2000'],
            ['AFT MAY 2000', 'po maju 2000'],
            ['BEF MAY 2000', 'pred majem 2000'],
            ['15 JUN 2000', '15. junija 2000'],
            ['JUN 2000', 'junij 2000'],
            ['ABT JUN 2000', 'okoli junija 2000'],
            ['FROM JUN 2000', 'od junija 2000'],
            ['AFT JUN 2000', 'po juniju 2000'],
            ['BEF JUN 2000', 'pred junijem 2000'],
            ['15 JUL 2000', '15. julija 2000'],
            ['JUL 2000', 'julij 2000'],
            ['ABT JUL 2000', 'okoli julija 2000'],
            ['FROM JUL 2000', 'od julija 2000'],
            ['AFT JUL 2000', 'po juliju 2000'],
            ['BEF JUL 2000', 'pred julijem 2000'],
            ['15 AUG 2000', '15. avgusta 2000'],
            ['AUG 2000', 'avgust 2000'],
            ['ABT AUG 2000', 'okoli avgusta 2000'],
            ['FROM AUG 2000', 'od avgusta 2000'],
            ['AFT AUG 2000', 'po avgustu 2000'],
            ['BEF AUG 2000', 'pred avgustom 2000'],
            ['15 SEP 2000', '15. septembra 2000'],
            ['SEP 2000', 'september 2000'],
            ['ABT SEP 2000', 'okoli septembra 2000'],
            ['FROM SEP 2000', 'od septembra 2000'],
            ['AFT SEP 2000', 'po septembru 2000'],
            ['BEF SEP 2000', 'pred septembrom 2000'],
            ['15 OCT 2000', '15. oktobra 2000'],
            ['OCT 2000', 'oktober 2000'],
            ['ABT OCT 2000', 'okoli oktobra 2000'],
            ['FROM OCT 2000', 'od oktobra 2000'],
            ['AFT OCT 2000', 'po oktobru 2000'],
            ['BEF OCT 2000', 'pred oktobrom 2000'],
            ['15 NOV 2000', '15. novembra 2000'],
            ['NOV 2000', 'november 2000'],
            ['ABT NOV 2000', 'okoli novembra 2000'],
            ['FROM NOV 2000', 'od novembra 2000'],
            ['AFT NOV 2000', 'po novembru 2000'],
            ['BEF NOV 2000', 'pred novembrom 2000'],
            ['15 DEC 2000', '15. decembra 2000'],
            ['DEC 2000', 'december 2000'],
            ['ABT DEC 2000', 'okoli decembra 2000'],
            ['FROM DEC 2000', 'od decembra 2000'],
            ['AFT DEC 2000', 'po decembru 2000'],
            ['BEF DEC 2000', 'pred decembrom 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'okoli 15. januarja 2000'],
            ['CAL 15 JAN 2000', 'izračunano 15. januarja 2000'],
            ['EST 15 JAN 2000', 'ocenjeno 15. januarja 2000'],
            ['BEF 15 JAN 2000', 'pred 15. januarja 2000'],
            ['AFT 15 JAN 2000', 'po 15. januarja 2000'],
            ['FROM 15 JAN 2000', 'od 15. januarja 2000'],
            ['TO 15 JAN 2000', 'do 15. januarja 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'med 15. januarja 2000 in 15. februarja 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'od 15. januarja 2000 do 15. februarja 2000'],
            ['INT 15 JAN 2000', 'interpretirano kot 15. januarja 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15. januarja 1700 po K.'],
            ['@#DJULIAN@ JAN 1700', 'januar 1700 po K.'],
            ['ABT @#DJULIAN@ JAN 1700', 'okoli januarja 1700 po K.'],
            ['FROM @#DJULIAN@ JAN 1700', 'od januarja 1700 po K.'],
            ['AFT @#DJULIAN@ JAN 1700', 'po januarju 1700 po K.'],
            ['BEF @#DJULIAN@ JAN 1700', 'pred januarjem 1700 po K.'],
            ['@#DJULIAN@ 15 FEB 1700', '15. februarja 1700 po K.'],
            ['@#DJULIAN@ FEB 1700', 'februar 1700 po K.'],
            ['ABT @#DJULIAN@ FEB 1700', 'okoli februarja 1700 po K.'],
            ['FROM @#DJULIAN@ FEB 1700', 'od februarja 1700 po K.'],
            ['AFT @#DJULIAN@ FEB 1700', 'po februarju 1700 po K.'],
            ['BEF @#DJULIAN@ FEB 1700', 'pred februarjem 1700 po K.'],
            ['@#DJULIAN@ 15 MAR 1700', '15. marca 1700 po K.'],
            ['@#DJULIAN@ MAR 1700', 'marec 1700 po K.'],
            ['ABT @#DJULIAN@ MAR 1700', 'okoli marca 1700 po K.'],
            ['FROM @#DJULIAN@ MAR 1700', 'od marca 1700 po K.'],
            ['AFT @#DJULIAN@ MAR 1700', 'po marcu 1700 po K.'],
            ['BEF @#DJULIAN@ MAR 1700', 'pred marcem 1700 po K.'],
            ['@#DJULIAN@ 15 APR 1700', '15. aprila 1700 po K.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14. aprila 1645/46 po K.'],
            ['@#DJULIAN@ APR 1700', 'april 1700 po K.'],
            ['ABT @#DJULIAN@ APR 1700', 'okoli aprila 1700 po K.'],
            ['FROM @#DJULIAN@ APR 1700', 'od aprila 1700 po K.'],
            ['AFT @#DJULIAN@ APR 1700', 'po aprilu 1700 po K.'],
            ['BEF @#DJULIAN@ APR 1700', 'pred aprilom 1700 po K.'],
            ['@#DJULIAN@ 15 MAY 1700', '15. maja 1700 po K.'],
            ['@#DJULIAN@ MAY 1700', 'maj 1700 po K.'],
            ['ABT @#DJULIAN@ MAY 1700', 'okoli maja 1700 po K.'],
            ['FROM @#DJULIAN@ MAY 1700', 'od maja 1700 po K.'],
            ['AFT @#DJULIAN@ MAY 1700', 'po maju 1700 po K.'],
            ['BEF @#DJULIAN@ MAY 1700', 'pred majem 1700 po K.'],
            ['@#DJULIAN@ 15 JUN 1700', '15. junija 1700 po K.'],
            ['@#DJULIAN@ JUN 1700', 'junij 1700 po K.'],
            ['ABT @#DJULIAN@ JUN 1700', 'okoli junija 1700 po K.'],
            ['FROM @#DJULIAN@ JUN 1700', 'od junija 1700 po K.'],
            ['AFT @#DJULIAN@ JUN 1700', 'po juniju 1700 po K.'],
            ['BEF @#DJULIAN@ JUN 1700', 'pred junijem 1700 po K.'],
            ['@#DJULIAN@ 15 JUL 1700', '15. julija 1700 po K.'],
            ['@#DJULIAN@ JUL 1700', 'julij 1700 po K.'],
            ['ABT @#DJULIAN@ JUL 1700', 'okoli julija 1700 po K.'],
            ['FROM @#DJULIAN@ JUL 1700', 'od julija 1700 po K.'],
            ['AFT @#DJULIAN@ JUL 1700', 'po juliju 1700 po K.'],
            ['BEF @#DJULIAN@ JUL 1700', 'pred julijem 1700 po K.'],
            ['@#DJULIAN@ 15 AUG 1700', '15. avgusta 1700 po K.'],
            ['@#DJULIAN@ AUG 1700', 'avgust 1700 po K.'],
            ['ABT @#DJULIAN@ AUG 1700', 'okoli avgusta 1700 po K.'],
            ['FROM @#DJULIAN@ AUG 1700', 'od avgusta 1700 po K.'],
            ['AFT @#DJULIAN@ AUG 1700', 'po avgustu 1700 po K.'],
            ['BEF @#DJULIAN@ AUG 1700', 'pred avgustom 1700 po K.'],
            ['@#DJULIAN@ 15 SEP 1700', '15. septembra 1700 po K.'],
            ['@#DJULIAN@ SEP 1700', 'september 1700 po K.'],
            ['ABT @#DJULIAN@ SEP 1700', 'okoli septembra 1700 po K.'],
            ['FROM @#DJULIAN@ SEP 1700', 'od septembra 1700 po K.'],
            ['AFT @#DJULIAN@ SEP 1700', 'po septembru 1700 po K.'],
            ['BEF @#DJULIAN@ SEP 1700', 'pred septembrom 1700 po K.'],
            ['@#DJULIAN@ 15 OCT 1700', '15. oktobra 1700 po K.'],
            ['@#DJULIAN@ OCT 1700', 'oktober 1700 po K.'],
            ['ABT @#DJULIAN@ OCT 1700', 'okoli oktobra 1700 po K.'],
            ['FROM @#DJULIAN@ OCT 1700', 'od oktobra 1700 po K.'],
            ['AFT @#DJULIAN@ OCT 1700', 'po oktobru 1700 po K.'],
            ['BEF @#DJULIAN@ OCT 1700', 'pred oktobrom 1700 po K.'],
            ['@#DJULIAN@ 15 NOV 1700', '15. novembra 1700 po K.'],
            ['@#DJULIAN@ NOV 1700', 'november 1700 po K.'],
            ['ABT @#DJULIAN@ NOV 1700', 'okoli novembra 1700 po K.'],
            ['FROM @#DJULIAN@ NOV 1700', 'od novembra 1700 po K.'],
            ['AFT @#DJULIAN@ NOV 1700', 'po novembru 1700 po K.'],
            ['BEF @#DJULIAN@ NOV 1700', 'pred novembrom 1700 po K.'],
            ['@#DJULIAN@ 15 DEC 1700', '15. decembra 1700 po K.'],
            ['@#DJULIAN@ DEC 1700', 'december 1700 po K.'],
            ['ABT @#DJULIAN@ DEC 1700', 'okoli decembra 1700 po K.'],
            ['FROM @#DJULIAN@ DEC 1700', 'od decembra 1700 po K.'],
            ['AFT @#DJULIAN@ DEC 1700', 'po decembru 1700 po K.'],
            ['BEF @#DJULIAN@ DEC 1700', 'pred decembrom 1700 po K.'],
            ['@#DJULIAN@ 1700', '1700 po K.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'okoli 15. januarja 1700 po K.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'izračunano 15. januarja 1700 po K.'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'ocenjeno 15. januarja 1700 po K.'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'pred 15. januarja 1700 po K.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'po 15. januarja 1700 po K.'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'od 15. januarja 1700 po K.'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'do 15. januarja 1700 po K.'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'med 15. januarja 1700 po K. in 15. februarja 1700 po K.'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'od 15. januarja 1700 po K. do 15. februarja 1700 po K.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretirano kot 15. januarja 1700 po K.'],
            ['@#DHEBREW@ 15 TSH 5765', '15. tišri 5765'],
            ['@#DHEBREW@ TSH 5765', 'tišri 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'okoli tišri 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'od tišri 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'po tišri 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'pred tišri 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15. ešvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'ešvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'okoli ešvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'od ešvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'po ešvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'pred ešvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15. kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'okoli kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'od kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'po kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'pred kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15. tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'okoli tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'od tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'po tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'pred tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15. šebat 5765'],
            ['@#DHEBREW@ SHV 5765', 'šebat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'okoli šebat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'od šebat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'po šebat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'pred šebat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15. adar 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'okoli adar 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'od adar 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'po adar 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'pred adar 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15. beadar 5765'],
            ['@#DHEBREW@ ADS 5765', 'beadar 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'okoli beadar 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'od beadar 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'po beadar 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'pred beadar 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15. nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'okoli nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'od nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'po nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'pred nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15. ijar 5765'],
            ['@#DHEBREW@ IYR 5765', 'ijar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'okoli ijar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'od ijar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'po ijar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'pred ijar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15. sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'okoli sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'od sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'po sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'pred sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15. tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'okoli tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'od tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'po tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'pred tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15. av 5765'],
            ['@#DHEBREW@ AAV 5765', 'av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'okoli av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'od av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'po av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'pred av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15. elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'okoli elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'od elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'po elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'pred elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'okoli 15. tišri 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'izračunano 15. tišri 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'ocenjeno 15. tišri 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'pred 15. tišri 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'po 15. tišri 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'od 15. tišri 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'do 15. tišri 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'med 15. tišri 5765 in 15. ešvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'od 15. tišri 5765 do 15. ešvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretirano kot 15. tišri 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15. vendemiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'vendemiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'okoli vendemiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'od vendemiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'po vendemiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'pred vendemiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15. brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'okoli brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'od brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'po brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'pred brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15. frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'okoli frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'od frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'po frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'pred frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15. nivose An XII'],
            ['@#DFRENCH R@ NIVO 12', 'nivose An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'okoli nivose An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'od nivose An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'po nivose An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'pred nivose An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15. pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'okoli pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'od pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'po pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'pred pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15. ventose An XII'],
            ['@#DFRENCH R@ VENT 12', 'ventose An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'okoli ventose An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'od ventose An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'po ventose An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'pred ventose An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15. germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'okoli germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'od germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'po germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'pred germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15. floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'okoli floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'od floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'po floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'pred floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15. prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'okoli prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'od prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'po prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'pred prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15. messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'okoli messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'od messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'po messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'pred messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15. thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'okoli thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'od thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'po thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'pred thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15. fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'okoli fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'od fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'po fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'pred fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15. dodatni dnevi An XII'],
            ['@#DFRENCH R@ COMP 12', 'dodatni dnevi An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'okoli dodatni dnevi An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'od dodatni dnevi An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'po dodatni dnevi An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'pred dodatni dnevi An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'okoli 15. vendemiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'izračunano 15. vendemiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'ocenjeno 15. vendemiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'pred 15. vendemiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'po 15. vendemiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'od 15. vendemiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'do 15. vendemiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'med 15. vendemiaire An XII in 15. brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'od 15. vendemiaire An XII do 15. brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretirano kot 15. vendemiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15. muharrem 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'muharrem 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'okoli muharrem 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'od muharrem 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'po muharrem 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'pred muharrem 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15. safer 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'safer 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'okoli safer 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'od safer 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'po safer 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'pred safer 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15. Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'okoli Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'od Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'po Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'pred Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15. Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'okoli Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'od Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'po Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'pred Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15. džumadel-ula 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'džumadel-ula 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'okoli džumadel-ula 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'od džumadel-ula 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'po džumadel-ula 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'pred džumadel-ula 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15. džumadel-uhra 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'džumadel-uhra 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'okoli džumadel-uhra 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'od džumadel-uhra 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'po džumadel-uhra 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'pred džumadel-uhra 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15. redžeb 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'redžeb 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'okoli redžeb 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'od redžeb 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'po redžeb 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'pred redžeb 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15. šaban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'šaban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'okoli šaban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'od šaban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'po šaban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'pred šaban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15. ramazan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ramazan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'okoli ramazan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'od ramazan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'po ramazan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'pred ramazan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15. šewal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'šewal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'okoli šewal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'od šewal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'po šewal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'pred šewal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15. zul-ka’de 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'zul-ka’de 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'okoli zul-ka’de 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'od zul-ka’de 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'po zul-ka’de 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'pred zul-ka’de 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'okoli 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'od 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'po 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'pred 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'okoli 15. muharrem 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'izračunano 15. muharrem 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'ocenjeno 15. muharrem 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'pred 15. muharrem 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'po 15. muharrem 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'od 15. muharrem 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'do 15. muharrem 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'med 15. muharrem 1425 in 15. safer 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'od 15. muharrem 1425 do 15. safer 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretirano kot 15. muharrem 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15. farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'okoli farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'od farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'po farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'pred farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15. ordibehešt 1384'],
            ['@#DJALALI@ ORDIB 1384', 'ordibehešt 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'okoli ordibehešt 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'od ordibehešt 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'po ordibehešt 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'pred ordibehešt 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15. kordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'kordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'okoli kordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'od kordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'po kordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'pred kordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15. tir 1384'],
            ['@#DJALALI@ TIR 1384', 'tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'okoli tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'od tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'po tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'pred tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15. mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'okoli mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'od mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'po mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'pred mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15. šarivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'šarivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'okoli šarivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'od šarivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'po šarivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'pred šarivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15. mer 1384'],
            ['@#DJALALI@ MEHR 1384', 'mer 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'okoli mer 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'od mer 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'po mer 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'pred mer 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15. aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'okoli aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'od aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'po aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'pred aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15. azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'okoli azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'od azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'po azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'pred azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15. dej 1384'],
            ['@#DJALALI@ DEY 1384', 'dej 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'okoli dej 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'od dej 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'po dej 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'pred dej 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15. bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'okoli bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'od bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'po bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'pred bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15. esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'okoli esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'od esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'po esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'pred esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'okoli 15. farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'izračunano 15. farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'ocenjeno 15. farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'pred 15. farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'po 15. farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'od 15. farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'do 15. farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'med 15. farvardin 1384 in 15. ordibehešt 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'od 15. farvardin 1384 do 15. ordibehešt 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretirano kot 15. farvardin 1384'],
        ];
    }
}
