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
use Fisharebest\Webtrees\I18N\Languages\Kazhak;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Kazhak::class)]
class KazhakTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Kazhak();
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
        self::assertSame('kk', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('қазақ тілі', self::language()->endonym());
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
        self::assertSame('one және two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two және three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one немесе two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two немесе three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('әйел', 'күйеу', [$husband, $fm, $wife]);
        self::assertRelationshipNames('бұрынғы жұбайы', 'бұрынғы жұбайы', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('атастырылған', 'атастырылған', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('ана', 'ұл', [$son, $fm, $wife]);
        self::assertRelationshipNames('әке', 'ұл', [$son, $fm, $husband]);
        self::assertRelationshipNames('ана', 'қыз', [$daughter, $fm, $wife]);

        // Siblings — elder/younger
        self::assertRelationshipNames('сіңілі', 'аға', [$son, $fm, $daughter]);
        self::assertRelationshipNames('аға', 'сіңілі', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('өгей әпке', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('өгей әке', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('өгей қыз', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('қайын ана', 'күйеу бала', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('қайын ата', 'күйеу бала', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('келін', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('әже', 'немере', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('ата', 'немере', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipNames('әже', 'немере', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('ата', 'немере', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('арғы ата', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('арғы әже', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles — paternal
        self::assertRelationshipNames('апа', 'жиен ұл', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('аға', 'жиен ұл', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('жиен қыз', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('жиен ұл', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('жиен', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('жиен', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Қаңтар 2000'],
            ['JAN 2000', 'Қаңтар 2000'],
            ['ABT JAN 2000', 'шамамен Қаңтар 2000'],
            ['FROM JAN 2000', 'Қаңтар 2000-ден бастап'],
            ['AFT JAN 2000', 'кейін Қаңтар 2000'],
            ['BEF JAN 2000', 'Қаңтар 2000 дейін'],
            ['15 FEB 2000', '15 Ақпан 2000'],
            ['FEB 2000', 'Ақпан 2000'],
            ['ABT FEB 2000', 'шамамен Ақпан 2000'],
            ['FROM FEB 2000', 'Ақпан 2000-ден бастап'],
            ['AFT FEB 2000', 'кейін Ақпан 2000'],
            ['BEF FEB 2000', 'Ақпан 2000 дейін'],
            ['15 MAR 2000', '15 Наурыз 2000'],
            ['MAR 2000', 'Наурыз 2000'],
            ['ABT MAR 2000', 'шамамен Наурыз 2000'],
            ['FROM MAR 2000', 'Наурыз 2000-ден бастап'],
            ['AFT MAR 2000', 'кейін Наурыз 2000'],
            ['BEF MAR 2000', 'Наурыз 2000 дейін'],
            ['15 APR 2000', '15 Сәуір 2000'],
            ['APR 2000', 'Сәуір 2000'],
            ['ABT APR 2000', 'шамамен Сәуір 2000'],
            ['FROM APR 2000', 'Сәуір 2000-ден бастап'],
            ['AFT APR 2000', 'кейін Сәуір 2000'],
            ['BEF APR 2000', 'Сәуір 2000 дейін'],
            ['15 MAY 2000', '15 Мамыр 2000'],
            ['MAY 2000', 'Мамыр 2000'],
            ['ABT MAY 2000', 'шамамен Мамыр 2000'],
            ['FROM MAY 2000', 'Мамыр 2000-ден бастап'],
            ['AFT MAY 2000', 'кейін Мамыр 2000'],
            ['BEF MAY 2000', 'Мамыр 2000 дейін'],
            ['15 JUN 2000', '15 Маусым 2000'],
            ['JUN 2000', 'Маусым 2000'],
            ['ABT JUN 2000', 'шамамен Маусым 2000'],
            ['FROM JUN 2000', 'Маусым 2000-ден бастап'],
            ['AFT JUN 2000', 'кейін Маусым 2000'],
            ['BEF JUN 2000', 'Маусым 2000 дейін'],
            ['15 JUL 2000', '15 Шілде 2000'],
            ['JUL 2000', 'Шілде 2000'],
            ['ABT JUL 2000', 'шамамен Шілде 2000'],
            ['FROM JUL 2000', 'Шілде 2000-ден бастап'],
            ['AFT JUL 2000', 'кейін Шілде 2000'],
            ['BEF JUL 2000', 'Шілде 2000 дейін'],
            ['15 AUG 2000', '15 Тамыз 2000'],
            ['AUG 2000', 'Тамыз 2000'],
            ['ABT AUG 2000', 'шамамен Тамыз 2000'],
            ['FROM AUG 2000', 'Тамыз 2000-ден бастап'],
            ['AFT AUG 2000', 'кейін Тамыз 2000'],
            ['BEF AUG 2000', 'Тамыз 2000 дейін'],
            ['15 SEP 2000', '15 Қыркүйек 2000'],
            ['SEP 2000', 'Қыркүйек 2000'],
            ['ABT SEP 2000', 'шамамен Қыркүйек 2000'],
            ['FROM SEP 2000', 'Қыркүйек 2000-ден бастап'],
            ['AFT SEP 2000', 'кейін Қыркүйек 2000'],
            ['BEF SEP 2000', 'Қыркүйек 2000 дейін'],
            ['15 OCT 2000', '15 Қазан 2000'],
            ['OCT 2000', 'Қазан 2000'],
            ['ABT OCT 2000', 'шамамен Қазан 2000'],
            ['FROM OCT 2000', 'Қазан 2000-ден бастап'],
            ['AFT OCT 2000', 'кейін Қазан 2000'],
            ['BEF OCT 2000', 'Қазан 2000 дейін'],
            ['15 NOV 2000', '15 Қараша 2000'],
            ['NOV 2000', 'Қараша 2000'],
            ['ABT NOV 2000', 'шамамен Қараша 2000'],
            ['FROM NOV 2000', 'Қараша 2000-ден бастап'],
            ['AFT NOV 2000', 'кейін Қараша 2000'],
            ['BEF NOV 2000', 'Қараша 2000 дейін'],
            ['15 DEC 2000', '15 Желтоқсан 2000'],
            ['DEC 2000', 'Желтоқсан 2000'],
            ['ABT DEC 2000', 'шамамен Желтоқсан 2000'],
            ['FROM DEC 2000', 'Желтоқсан 2000-ден бастап'],
            ['AFT DEC 2000', 'кейін Желтоқсан 2000'],
            ['BEF DEC 2000', 'Желтоқсан 2000 дейін'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'шамамен 15 Қаңтар 2000'],
            ['CAL 15 JAN 2000', 'есептелген 15 Қаңтар 2000'],
            ['EST 15 JAN 2000', 'бағалау 15 Қаңтар 2000'],
            ['BEF 15 JAN 2000', '15 Қаңтар 2000 дейін'],
            ['AFT 15 JAN 2000', 'кейін 15 Қаңтар 2000'],
            ['FROM 15 JAN 2000', '15 Қаңтар 2000-ден бастап'],
            ['TO 15 JAN 2000', '15 Қаңтар 2000 дейін'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '15 Қаңтар 2000 және 15 Ақпан 2000 арасында'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '15 Қаңтар 2000-дан 15 Ақпан 2000-ге дейін'],
            ['INT 15 JAN 2000', 'интерпретацияланған 15 Қаңтар 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Қаңтар 1700 ЖД'],
            ['@#DJULIAN@ JAN 1700', 'Қаңтар 1700 ЖД'],
            ['ABT @#DJULIAN@ JAN 1700', 'шамамен Қаңтар 1700 ЖД'],
            ['FROM @#DJULIAN@ JAN 1700', 'Қаңтар 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ JAN 1700', 'кейін Қаңтар 1700 ЖД'],
            ['BEF @#DJULIAN@ JAN 1700', 'Қаңтар 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Ақпан 1700 ЖД'],
            ['@#DJULIAN@ FEB 1700', 'Ақпан 1700 ЖД'],
            ['ABT @#DJULIAN@ FEB 1700', 'шамамен Ақпан 1700 ЖД'],
            ['FROM @#DJULIAN@ FEB 1700', 'Ақпан 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ FEB 1700', 'кейін Ақпан 1700 ЖД'],
            ['BEF @#DJULIAN@ FEB 1700', 'Ақпан 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Наурыз 1700 ЖД'],
            ['@#DJULIAN@ MAR 1700', 'Наурыз 1700 ЖД'],
            ['ABT @#DJULIAN@ MAR 1700', 'шамамен Наурыз 1700 ЖД'],
            ['FROM @#DJULIAN@ MAR 1700', 'Наурыз 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ MAR 1700', 'кейін Наурыз 1700 ЖД'],
            ['BEF @#DJULIAN@ MAR 1700', 'Наурыз 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 APR 1700', '15 Сәуір 1700 ЖД'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Сәуір 1645/46 ЖД'],
            ['@#DJULIAN@ APR 1700', 'Сәуір 1700 ЖД'],
            ['ABT @#DJULIAN@ APR 1700', 'шамамен Сәуір 1700 ЖД'],
            ['FROM @#DJULIAN@ APR 1700', 'Сәуір 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ APR 1700', 'кейін Сәуір 1700 ЖД'],
            ['BEF @#DJULIAN@ APR 1700', 'Сәуір 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Мамыр 1700 ЖД'],
            ['@#DJULIAN@ MAY 1700', 'Мамыр 1700 ЖД'],
            ['ABT @#DJULIAN@ MAY 1700', 'шамамен Мамыр 1700 ЖД'],
            ['FROM @#DJULIAN@ MAY 1700', 'Мамыр 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ MAY 1700', 'кейін Мамыр 1700 ЖД'],
            ['BEF @#DJULIAN@ MAY 1700', 'Мамыр 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Маусым 1700 ЖД'],
            ['@#DJULIAN@ JUN 1700', 'Маусым 1700 ЖД'],
            ['ABT @#DJULIAN@ JUN 1700', 'шамамен Маусым 1700 ЖД'],
            ['FROM @#DJULIAN@ JUN 1700', 'Маусым 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ JUN 1700', 'кейін Маусым 1700 ЖД'],
            ['BEF @#DJULIAN@ JUN 1700', 'Маусым 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Шілде 1700 ЖД'],
            ['@#DJULIAN@ JUL 1700', 'Шілде 1700 ЖД'],
            ['ABT @#DJULIAN@ JUL 1700', 'шамамен Шілде 1700 ЖД'],
            ['FROM @#DJULIAN@ JUL 1700', 'Шілде 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ JUL 1700', 'кейін Шілде 1700 ЖД'],
            ['BEF @#DJULIAN@ JUL 1700', 'Шілде 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Тамыз 1700 ЖД'],
            ['@#DJULIAN@ AUG 1700', 'Тамыз 1700 ЖД'],
            ['ABT @#DJULIAN@ AUG 1700', 'шамамен Тамыз 1700 ЖД'],
            ['FROM @#DJULIAN@ AUG 1700', 'Тамыз 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ AUG 1700', 'кейін Тамыз 1700 ЖД'],
            ['BEF @#DJULIAN@ AUG 1700', 'Тамыз 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Қыркүйек 1700 ЖД'],
            ['@#DJULIAN@ SEP 1700', 'Қыркүйек 1700 ЖД'],
            ['ABT @#DJULIAN@ SEP 1700', 'шамамен Қыркүйек 1700 ЖД'],
            ['FROM @#DJULIAN@ SEP 1700', 'Қыркүйек 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ SEP 1700', 'кейін Қыркүйек 1700 ЖД'],
            ['BEF @#DJULIAN@ SEP 1700', 'Қыркүйек 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Қазан 1700 ЖД'],
            ['@#DJULIAN@ OCT 1700', 'Қазан 1700 ЖД'],
            ['ABT @#DJULIAN@ OCT 1700', 'шамамен Қазан 1700 ЖД'],
            ['FROM @#DJULIAN@ OCT 1700', 'Қазан 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ OCT 1700', 'кейін Қазан 1700 ЖД'],
            ['BEF @#DJULIAN@ OCT 1700', 'Қазан 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Қараша 1700 ЖД'],
            ['@#DJULIAN@ NOV 1700', 'Қараша 1700 ЖД'],
            ['ABT @#DJULIAN@ NOV 1700', 'шамамен Қараша 1700 ЖД'],
            ['FROM @#DJULIAN@ NOV 1700', 'Қараша 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ NOV 1700', 'кейін Қараша 1700 ЖД'],
            ['BEF @#DJULIAN@ NOV 1700', 'Қараша 1700 ЖД дейін'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Желтоқсан 1700 ЖД'],
            ['@#DJULIAN@ DEC 1700', 'Желтоқсан 1700 ЖД'],
            ['ABT @#DJULIAN@ DEC 1700', 'шамамен Желтоқсан 1700 ЖД'],
            ['FROM @#DJULIAN@ DEC 1700', 'Желтоқсан 1700 ЖД-ден бастап'],
            ['AFT @#DJULIAN@ DEC 1700', 'кейін Желтоқсан 1700 ЖД'],
            ['BEF @#DJULIAN@ DEC 1700', 'Желтоқсан 1700 ЖД дейін'],
            ['@#DJULIAN@ 1700', '1700 ЖД'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'шамамен 15 Қаңтар 1700 ЖД'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'есептелген 15 Қаңтар 1700 ЖД'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'бағалау 15 Қаңтар 1700 ЖД'],
            ['BEF @#DJULIAN@ 15 JAN 1700', '15 Қаңтар 1700 ЖД дейін'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'кейін 15 Қаңтар 1700 ЖД'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '15 Қаңтар 1700 ЖД-ден бастап'],
            ['TO @#DJULIAN@ 15 JAN 1700', '15 Қаңтар 1700 ЖД дейін'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '15 Қаңтар 1700 ЖД және 15 Ақпан 1700 ЖД арасында'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '15 Қаңтар 1700 ЖД-дан 15 Ақпан 1700 ЖД-ге дейін'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'интерпретацияланған 15 Қаңтар 1700 ЖД'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Тишрей 5765'],
            ['@#DHEBREW@ TSH 5765', 'Тишрей 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'шамамен Тишрей 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'Тишрей 5765-ден бастап'],
            ['AFT @#DHEBREW@ TSH 5765', 'кейін Тишрей 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'Тишрей 5765 дейін'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Хешван 5765'],
            ['@#DHEBREW@ CSH 5765', 'Хешван 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'шамамен Хешван 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'Хешван 5765-ден бастап'],
            ['AFT @#DHEBREW@ CSH 5765', 'кейін Хешван 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'Хешван 5765 дейін'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Кислев 5765'],
            ['@#DHEBREW@ KSL 5765', 'Кислев 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'шамамен Кислев 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'Кислев 5765-ден бастап'],
            ['AFT @#DHEBREW@ KSL 5765', 'кейін Кислев 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'Кислев 5765 дейін'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Тевет 5765'],
            ['@#DHEBREW@ TVT 5765', 'Тевет 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'шамамен Тевет 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'Тевет 5765-ден бастап'],
            ['AFT @#DHEBREW@ TVT 5765', 'кейін Тевет 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'Тевет 5765 дейін'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Шеват 5765'],
            ['@#DHEBREW@ SHV 5765', 'Шеват 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'шамамен Шеват 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'Шеват 5765-ден бастап'],
            ['AFT @#DHEBREW@ SHV 5765', 'кейін Шеват 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'Шеват 5765 дейін'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Адар I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Адар I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'шамамен Адар I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'Адар I 5765-ден бастап'],
            ['AFT @#DHEBREW@ ADR 5765', 'кейін Адар I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'Адар I 5765 дейін'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Адар II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Адар II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'шамамен Адар II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'Адар II 5765-ден бастап'],
            ['AFT @#DHEBREW@ ADS 5765', 'кейін Адар II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'Адар II 5765 дейін'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Ниссан 5765'],
            ['@#DHEBREW@ NSN 5765', 'Ниссан 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'шамамен Ниссан 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'Ниссан 5765-ден бастап'],
            ['AFT @#DHEBREW@ NSN 5765', 'кейін Ниссан 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'Ниссан 5765 дейін'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Ияр 5765'],
            ['@#DHEBREW@ IYR 5765', 'Ияр 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'шамамен Ияр 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'Ияр 5765-ден бастап'],
            ['AFT @#DHEBREW@ IYR 5765', 'кейін Ияр 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'Ияр 5765 дейін'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Сиван 5765'],
            ['@#DHEBREW@ SVN 5765', 'Сиван 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'шамамен Сиван 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'Сиван 5765-ден бастап'],
            ['AFT @#DHEBREW@ SVN 5765', 'кейін Сиван 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'Сиван 5765 дейін'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Тамуз 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Тамуз 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'шамамен Тамуз 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'Тамуз 5765-ден бастап'],
            ['AFT @#DHEBREW@ TMZ 5765', 'кейін Тамуз 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'Тамуз 5765 дейін'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Ав 5765'],
            ['@#DHEBREW@ AAV 5765', 'Ав 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'шамамен Ав 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'Ав 5765-ден бастап'],
            ['AFT @#DHEBREW@ AAV 5765', 'кейін Ав 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'Ав 5765 дейін'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Элул 5765'],
            ['@#DHEBREW@ ELL 5765', 'Элул 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'шамамен Элул 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'Элул 5765-ден бастап'],
            ['AFT @#DHEBREW@ ELL 5765', 'кейін Элул 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'Элул 5765 дейін'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'шамамен 15 Тишрей 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'есептелген 15 Тишрей 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'бағалау 15 Тишрей 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '15 Тишрей 5765 дейін'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'кейін 15 Тишрей 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '15 Тишрей 5765-ден бастап'],
            ['TO @#DHEBREW@ 15 TSH 5765', '15 Тишрей 5765 дейін'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '15 Тишрей 5765 және 15 Хешван 5765 арасында'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '15 Тишрей 5765-дан 15 Хешван 5765-ге дейін'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'интерпретацияланған 15 Тишрей 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Вандемьер An XII'],
            ['@#DFRENCH R@ VEND 12', 'Вандемьер An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'шамамен Вандемьер An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'Вандемьер An XII-ден бастап'],
            ['AFT @#DFRENCH R@ VEND 12', 'кейін Вандемьер An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'Вандемьер An XII дейін'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Брюмер An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Брюмер An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'шамамен Брюмер An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'Брюмер An XII-ден бастап'],
            ['AFT @#DFRENCH R@ BRUM 12', 'кейін Брюмер An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'Брюмер An XII дейін'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Фример An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Фример An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'шамамен Фример An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'Фример An XII-ден бастап'],
            ['AFT @#DFRENCH R@ FRIM 12', 'кейін Фример An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'Фример An XII дейін'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Нивоз An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Нивоз An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'шамамен Нивоз An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'Нивоз An XII-ден бастап'],
            ['AFT @#DFRENCH R@ NIVO 12', 'кейін Нивоз An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'Нивоз An XII дейін'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Плювиоз An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Плювиоз An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'шамамен Плювиоз An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'Плювиоз An XII-ден бастап'],
            ['AFT @#DFRENCH R@ PLUV 12', 'кейін Плювиоз An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'Плювиоз An XII дейін'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Вантоз An XII'],
            ['@#DFRENCH R@ VENT 12', 'Вантоз An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'шамамен Вантоз An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'Вантоз An XII-ден бастап'],
            ['AFT @#DFRENCH R@ VENT 12', 'кейін Вантоз An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'Вантоз An XII дейін'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Жерминаль An XII'],
            ['@#DFRENCH R@ GERM 12', 'Жерминаль An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'шамамен Жерминаль An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'Жерминаль An XII-ден бастап'],
            ['AFT @#DFRENCH R@ GERM 12', 'кейін Жерминаль An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'Жерминаль An XII дейін'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Флореаль An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Флореаль An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'шамамен Флореаль An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'Флореаль An XII-ден бастап'],
            ['AFT @#DFRENCH R@ FLOR 12', 'кейін Флореаль An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'Флореаль An XII дейін'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Преріаль An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Преріаль An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'шамамен Преріаль An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'Преріаль An XII-ден бастап'],
            ['AFT @#DFRENCH R@ PRAI 12', 'кейін Преріаль An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'Преріаль An XII дейін'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Мессидор An XII'],
            ['@#DFRENCH R@ MESS 12', 'Мессидор An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'шамамен Мессидор An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'Мессидор An XII-ден бастап'],
            ['AFT @#DFRENCH R@ MESS 12', 'кейін Мессидор An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'Мессидор An XII дейін'],
            ['@#DFRENCH R@ 15 THER 12', '15 Термидор An XII'],
            ['@#DFRENCH R@ THER 12', 'Термидор An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'шамамен Термидор An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'Термидор An XII-ден бастап'],
            ['AFT @#DFRENCH R@ THER 12', 'кейін Термидор An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'Термидор An XII дейін'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Фрюктидор An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Фрюктидор An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'шамамен Фрюктидор An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'Фрюктидор An XII-ден бастап'],
            ['AFT @#DFRENCH R@ FRUC 12', 'кейін Фрюктидор An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'Фрюктидор An XII дейін'],
            ['@#DFRENCH R@ 15 COMP 12', '15 қосымша күндер An XII'],
            ['@#DFRENCH R@ COMP 12', 'қосымша күндер An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'шамамен қосымша күндер An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'қосымша күндер An XII-ден бастап'],
            ['AFT @#DFRENCH R@ COMP 12', 'кейін қосымша күндер An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'қосымша күндер An XII дейін'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'шамамен 15 Вандемьер An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'есептелген 15 Вандемьер An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'бағалау 15 Вандемьер An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', '15 Вандемьер An XII дейін'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'кейін 15 Вандемьер An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '15 Вандемьер An XII-ден бастап'],
            ['TO @#DFRENCH R@ 15 VEND 12', '15 Вандемьер An XII дейін'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '15 Вандемьер An XII және 15 Брюмер An XII арасында'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '15 Вандемьер An XII-дан 15 Брюмер An XII-ге дейін'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'интерпретацияланған 15 Вандемьер An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Мухаррам 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Мухаррам 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'шамамен Мухаррам 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'Мухаррам 1425-ден бастап'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'кейін Мухаррам 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'Мухаррам 1425 дейін'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Сафар 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Сафар 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'шамамен Сафар 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'Сафар 1425-ден бастап'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'кейін Сафар 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'Сафар 1425 дейін'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Раби аль-авваль 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Раби аль-авваль 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'шамамен Раби аль-авваль 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'Раби аль-авваль 1425-ден бастап'],
            ['AFT @#DHIJRI@ RABIA 1425', 'кейін Раби аль-авваль 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'Раби аль-авваль 1425 дейін'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Раби ас-сани 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Раби ас-сани 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'шамамен Раби ас-сани 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'Раби ас-сани 1425-ден бастап'],
            ['AFT @#DHIJRI@ RABIT 1425', 'кейін Раби ас-сани 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'Раби ас-сани 1425 дейін'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Жұмада әл-авалал 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Жұмада әл-авалал 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'шамамен Жұмада әл-авалал 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'Жұмада әл-авалал 1425-ден бастап'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'кейін Жұмада әл-авалал 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'Жұмада әл-авалал 1425 дейін'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Жұмада әл-Тани 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Жұмада әл-Тани 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'шамамен Жұмада әл-Тани 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'Жұмада әл-Тани 1425-ден бастап'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'кейін Жұмада әл-Тани 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'Жұмада әл-Тани 1425 дейін'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Ражаб 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Ражаб 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'шамамен Ражаб 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'Ражаб 1425-ден бастап'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'кейін Ражаб 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'Ражаб 1425 дейін'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Шаабан 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Шаабан 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'шамамен Шаабан 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'Шаабан 1425-ден бастап'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'кейін Шаабан 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'Шаабан 1425 дейін'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Рамадан 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Рамадан 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'шамамен Рамадан 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'Рамадан 1425-ден бастап'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'кейін Рамадан 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'Рамадан 1425 дейін'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Шавваль 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Шавваль 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'шамамен Шавваль 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'Шавваль 1425-ден бастап'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'кейін Шавваль 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'Шавваль 1425 дейін'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Зуль әл-Қида 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Зуль әл-Қида 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'шамамен Зуль әл-Қида 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'Зуль әл-Қида 1425-ден бастап'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'кейін Зуль әл-Қида 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'Зуль әл-Қида 1425 дейін'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'шамамен 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', '1425-ден бастап'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'кейін 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', '1425 дейін'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'шамамен 15 Мухаррам 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'есептелген 15 Мухаррам 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'бағалау 15 Мухаррам 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '15 Мухаррам 1425 дейін'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'кейін 15 Мухаррам 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '15 Мухаррам 1425-ден бастап'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '15 Мухаррам 1425 дейін'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '15 Мухаррам 1425 және 15 Сафар 1425 арасында'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '15 Мухаррам 1425-дан 15 Сафар 1425-ге дейін'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'интерпретацияланған 15 Мухаррам 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Фарварден 1384'],
            ['@#DJALALI@ FARVA 1384', 'Фарварден 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'шамамен Фарварден 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'Фарварден 1384-ден бастап'],
            ['AFT @#DJALALI@ FARVA 1384', 'кейін Фарварден 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'Фарварден 1384 дейін'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ордибеешт 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ордибеешт 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'шамамен Ордибеешт 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'Ордибеешт 1384-ден бастап'],
            ['AFT @#DJALALI@ ORDIB 1384', 'кейін Ордибеешт 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'Ордибеешт 1384 дейін'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Хордад 1384'],
            ['@#DJALALI@ KHORD 1384', 'Хордад 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'шамамен Хордад 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'Хордад 1384-ден бастап'],
            ['AFT @#DJALALI@ KHORD 1384', 'кейін Хордад 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'Хордад 1384 дейін'],
            ['@#DJALALI@ 15 TIR 1384', '15 Тир 1384'],
            ['@#DJALALI@ TIR 1384', 'Тир 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'шамамен Тир 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'Тир 1384-ден бастап'],
            ['AFT @#DJALALI@ TIR 1384', 'кейін Тир 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'Тир 1384 дейін'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Мордәд 1384'],
            ['@#DJALALI@ MORDA 1384', 'Мордәд 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'шамамен Мордәд 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'Мордәд 1384-ден бастап'],
            ['AFT @#DJALALI@ MORDA 1384', 'кейін Мордәд 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'Мордәд 1384 дейін'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Шахривар 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Шахривар 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'шамамен Шахривар 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'Шахривар 1384-ден бастап'],
            ['AFT @#DJALALI@ SHAHR 1384', 'кейін Шахривар 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'Шахривар 1384 дейін'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Мехр 1384'],
            ['@#DJALALI@ MEHR 1384', 'Мехр 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'шамамен Мехр 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'Мехр 1384-ден бастап'],
            ['AFT @#DJALALI@ MEHR 1384', 'кейін Мехр 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'Мехр 1384 дейін'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Абан 1384'],
            ['@#DJALALI@ ABAN 1384', 'Абан 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'шамамен Абан 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'Абан 1384-ден бастап'],
            ['AFT @#DJALALI@ ABAN 1384', 'кейін Абан 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'Абан 1384 дейін'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Азар 1384'],
            ['@#DJALALI@ AZAR 1384', 'Азар 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'шамамен Азар 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'Азар 1384-ден бастап'],
            ['AFT @#DJALALI@ AZAR 1384', 'кейін Азар 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'Азар 1384 дейін'],
            ['@#DJALALI@ 15 DEY 1384', '15 Дей 1384'],
            ['@#DJALALI@ DEY 1384', 'Дей 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'шамамен Дей 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'Дей 1384-ден бастап'],
            ['AFT @#DJALALI@ DEY 1384', 'кейін Дей 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'Дей 1384 дейін'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Бахман 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Бахман 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'шамамен Бахман 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'Бахман 1384-ден бастап'],
            ['AFT @#DJALALI@ BAHMA 1384', 'кейін Бахман 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'Бахман 1384 дейін'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Есфанд 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Есфанд 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'шамамен Есфанд 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'Есфанд 1384-ден бастап'],
            ['AFT @#DJALALI@ ESFAN 1384', 'кейін Есфанд 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'Есфанд 1384 дейін'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'шамамен 15 Фарварден 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'есептелген 15 Фарварден 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'бағалау 15 Фарварден 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '15 Фарварден 1384 дейін'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'кейін 15 Фарварден 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '15 Фарварден 1384-ден бастап'],
            ['TO @#DJALALI@ 15 FARVA 1384', '15 Фарварден 1384 дейін'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '15 Фарварден 1384 және 15 Ордибеешт 1384 арасында'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '15 Фарварден 1384-дан 15 Ордибеешт 1384-ге дейін'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'интерпретацияланған 15 Фарварден 1384'],
        ];
    }
}
