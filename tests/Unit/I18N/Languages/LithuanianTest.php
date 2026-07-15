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
use Fisharebest\Webtrees\I18N\Languages\Lithuanian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Lithuanian::class)]
class LithuanianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Lithuanian();
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
        self::assertSame(['A', 'Ą', 'B', 'C', 'Č', 'D', 'E', 'Ę', 'Ė', 'F', 'G', 'H', 'I', 'Y', 'Į', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'Š', 'T', 'U', 'Ų', 'Ū', 'V', 'Z', 'Ž'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('lt', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('lietuvių', self::language()->endonym());
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
        self::assertSame('−123 456,0789 %', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'YMD';
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
        self::assertSame('one ir two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two ir three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one arba two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two arba three', $language->formatListOr(['one', 'two', 'three']));
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
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
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

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cm@\n1 CHIL @cf@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");
        $fad = self::family('fad', "0 @fad@ FAM\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @ad@");
        $ffo = self::family('ffo', "0 @ffo@ FAM\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @fs@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $cousinMale, $cousinFemale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance,
             $adoptedSon, $adoptedDaughter, $fosterSon],
            [$fm, $fd, $fp, $fw, $fbro, $fgp, $fe, $fad, $ffo]
        );

        // Partners
        self::assertRelationshipNames('žmona', 'vyras', [$husband, $fm, $wife]);
        self::assertRelationshipNames('buvęs vyras', 'buvusi žmona', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('sužadėtinė', 'sužadėtinis', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('motina', 'sūnus', [$son, $fm, $wife]);
        self::assertRelationshipNames('tėvas', 'sūnus', [$son, $fm, $husband]);
        self::assertRelationshipNames('motina', 'dukra', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipName('sesuo', [$son, $fm, $daughter]);
        self::assertRelationshipName('brolis', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('pusbrolis', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('patėvis', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('podukra', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // Adopted
        self::assertRelationshipName('įsūnis', [$husband, $fad, $adoptedSon]);
        self::assertRelationshipName('įdukra', [$husband, $fad, $adoptedDaughter]);
        self::assertRelationshipName('įtėvis', [$adoptedSon, $fad, $husband]);
        self::assertRelationshipName('įmotė', [$adoptedSon, $fad, $wife]);

        // Foster
        self::assertRelationshipName('globotinis', [$husband, $ffo, $fosterSon]);
        self::assertRelationshipName('globėjas', [$fosterSon, $ffo, $husband]);
        self::assertRelationshipName('globėja', [$fosterSon, $ffo, $wife]);

        // In-laws — wife's parents
        self::assertRelationshipName('uošvė', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('uošvis', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // In-laws — husband's parents
        self::assertRelationshipName('anyta', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('šešuras', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // Children-in-law
        self::assertRelationshipName('marti', [$fatherOfH, $fp, $husband, $fm, $wife]);
        self::assertRelationshipName('žentas', [$motherOfW, $fw, $wife, $fm, $husband]);

        // Siblings-in-law
        self::assertRelationshipName('svainė', [$wife, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('svainis', [$wife, $fm, $husband, $fp, $brotherOfH]);

        // Grandparents
        self::assertRelationshipNames('senelė', 'anūkas', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('senelis', 'anūkas', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic — pro- prefix)
        self::assertRelationshipName('prosenelė', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('prosenelis', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);

        // Aunts and uncles
        self::assertRelationshipNames('teta', 'sūnėnas', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('dėdė', 'sūnėnas', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('dukterėčia', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('sūnėnas', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('pusbrolis', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
        self::assertRelationshipName('pusseserė', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);

        // Great-aunt/uncle (dynamic — pro- prefix)
        self::assertRelationshipName('proteta', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('prodėdė', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '2000 sausio 15'],
            ['JAN 2000', '2000 sausis'],
            ['ABT JAN 2000', 'apie 2000 sausio'],
            ['FROM JAN 2000', 'iš 2000 sausio'],
            ['AFT JAN 2000', 'po 2000 sausis'],
            ['BEF JAN 2000', 'prieš 2000 sausis'],
            ['15 FEB 2000', '2000 vasario 15'],
            ['FEB 2000', '2000 vasaris'],
            ['ABT FEB 2000', 'apie 2000 vasario'],
            ['FROM FEB 2000', 'iš 2000 vasario'],
            ['AFT FEB 2000', 'po 2000 vasaris'],
            ['BEF FEB 2000', 'prieš 2000 vasaris'],
            ['15 MAR 2000', '2000 kovo 15'],
            ['MAR 2000', '2000 kovas'],
            ['ABT MAR 2000', 'apie 2000 kovo'],
            ['FROM MAR 2000', 'iš 2000 kovo'],
            ['AFT MAR 2000', 'po 2000 kovas'],
            ['BEF MAR 2000', 'prieš 2000 kovas'],
            ['15 APR 2000', '2000 balandžio 15'],
            ['APR 2000', '2000 balandis'],
            ['ABT APR 2000', 'apie 2000 balandžio'],
            ['FROM APR 2000', 'iš 2000 balandžio'],
            ['AFT APR 2000', 'po 2000 balandis'],
            ['BEF APR 2000', 'prieš 2000 balandis'],
            ['15 MAY 2000', '2000 gegužės 15'],
            ['MAY 2000', '2000 gegužė'],
            ['ABT MAY 2000', 'apie 2000 gegužės'],
            ['FROM MAY 2000', 'iš 2000 gegužės'],
            ['AFT MAY 2000', 'po 2000 gegužė'],
            ['BEF MAY 2000', 'prieš 2000 gegužė'],
            ['15 JUN 2000', '2000 birželio 15'],
            ['JUN 2000', '2000 birželis'],
            ['ABT JUN 2000', 'apie 2000 birželio'],
            ['FROM JUN 2000', 'iš 2000 birželio'],
            ['AFT JUN 2000', 'po 2000 birželis'],
            ['BEF JUN 2000', 'prieš 2000 birželis'],
            ['15 JUL 2000', '2000 liepos 15'],
            ['JUL 2000', '2000 liepa'],
            ['ABT JUL 2000', 'apie 2000 liepos'],
            ['FROM JUL 2000', 'iš 2000 liepos'],
            ['AFT JUL 2000', 'po 2000 liepa'],
            ['BEF JUL 2000', 'prieš 2000 liepa'],
            ['15 AUG 2000', '2000 rugpjūčio 15'],
            ['AUG 2000', '2000 rugpjūtis'],
            ['ABT AUG 2000', 'apie 2000 rugpjūčio'],
            ['FROM AUG 2000', 'iš 2000 rugpjūčio'],
            ['AFT AUG 2000', 'po 2000 rugpjūtis'],
            ['BEF AUG 2000', 'prieš 2000 rugpjūtis'],
            ['15 SEP 2000', '2000 rugsėjo 15'],
            ['SEP 2000', '2000 rugsėjis'],
            ['ABT SEP 2000', 'apie 2000 rugsėjo'],
            ['FROM SEP 2000', 'iš 2000 rugsėjo'],
            ['AFT SEP 2000', 'po 2000 rugsėjis'],
            ['BEF SEP 2000', 'prieš 2000 rugsėjis'],
            ['15 OCT 2000', '2000 spalio 15'],
            ['OCT 2000', '2000 spalis'],
            ['ABT OCT 2000', 'apie 2000 spalio'],
            ['FROM OCT 2000', 'iš 2000 spalio'],
            ['AFT OCT 2000', 'po 2000 spalis'],
            ['BEF OCT 2000', 'prieš 2000 spalis'],
            ['15 NOV 2000', '2000 lapkričio 15'],
            ['NOV 2000', '2000 lapkritis'],
            ['ABT NOV 2000', 'apie 2000 lapkričio'],
            ['FROM NOV 2000', 'iš 2000 lapkričio'],
            ['AFT NOV 2000', 'po 2000 lapkritis'],
            ['BEF NOV 2000', 'prieš 2000 lapkritis'],
            ['15 DEC 2000', '2000 gruodžio 15'],
            ['DEC 2000', '2000 gruodis'],
            ['ABT DEC 2000', 'apie 2000 gruodžio'],
            ['FROM DEC 2000', 'iš 2000 gruodžio'],
            ['AFT DEC 2000', 'po 2000 gruodis'],
            ['BEF DEC 2000', 'prieš 2000 gruodis'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'apie 2000 sausio 15'],
            ['CAL 15 JAN 2000', 'apskaičiuota 2000 sausio 15'],
            ['EST 15 JAN 2000', 'liko 2000 sausio 15'],
            ['BEF 15 JAN 2000', 'prieš 2000 sausio 15'],
            ['AFT 15 JAN 2000', 'po 2000 sausio 15'],
            ['FROM 15 JAN 2000', 'iš 2000 sausio 15'],
            ['TO 15 JAN 2000', 'į 2000 sausio 15'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'tarp 2000 sausio 15 ir 2000 vasario 15'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'nuo 2000 sausio 15 iki 2000 vasario 15'],
            ['INT 15 JAN 2000', 'nutraukta 2000 sausio 15'],
            ['@#DJULIAN@ 15 JAN 1700', '1700 ᴄᴇ sausio 15'],
            ['@#DJULIAN@ JAN 1700', '1700 ᴄᴇ sausis'],
            ['ABT @#DJULIAN@ JAN 1700', 'apie 1700 ᴄᴇ sausio'],
            ['FROM @#DJULIAN@ JAN 1700', 'iš 1700 ᴄᴇ sausio'],
            ['AFT @#DJULIAN@ JAN 1700', 'po 1700 ᴄᴇ sausis'],
            ['BEF @#DJULIAN@ JAN 1700', 'prieš 1700 ᴄᴇ sausis'],
            ['@#DJULIAN@ 15 FEB 1700', '1700 ᴄᴇ vasario 15'],
            ['@#DJULIAN@ FEB 1700', '1700 ᴄᴇ vasaris'],
            ['ABT @#DJULIAN@ FEB 1700', 'apie 1700 ᴄᴇ vasario'],
            ['FROM @#DJULIAN@ FEB 1700', 'iš 1700 ᴄᴇ vasario'],
            ['AFT @#DJULIAN@ FEB 1700', 'po 1700 ᴄᴇ vasaris'],
            ['BEF @#DJULIAN@ FEB 1700', 'prieš 1700 ᴄᴇ vasaris'],
            ['@#DJULIAN@ 15 MAR 1700', '1700 ᴄᴇ kovo 15'],
            ['@#DJULIAN@ MAR 1700', '1700 ᴄᴇ kovas'],
            ['ABT @#DJULIAN@ MAR 1700', 'apie 1700 ᴄᴇ kovo'],
            ['FROM @#DJULIAN@ MAR 1700', 'iš 1700 ᴄᴇ kovo'],
            ['AFT @#DJULIAN@ MAR 1700', 'po 1700 ᴄᴇ kovas'],
            ['BEF @#DJULIAN@ MAR 1700', 'prieš 1700 ᴄᴇ kovas'],
            ['@#DJULIAN@ 15 APR 1700', '1700 ᴄᴇ balandžio 15'],
            ['@#DJULIAN@ 14 APR 1645/46', '1645/46 ᴄᴇ balandžio 14'],
            ['@#DJULIAN@ APR 1700', '1700 ᴄᴇ balandis'],
            ['ABT @#DJULIAN@ APR 1700', 'apie 1700 ᴄᴇ balandžio'],
            ['FROM @#DJULIAN@ APR 1700', 'iš 1700 ᴄᴇ balandžio'],
            ['AFT @#DJULIAN@ APR 1700', 'po 1700 ᴄᴇ balandis'],
            ['BEF @#DJULIAN@ APR 1700', 'prieš 1700 ᴄᴇ balandis'],
            ['@#DJULIAN@ 15 MAY 1700', '1700 ᴄᴇ gegužės 15'],
            ['@#DJULIAN@ MAY 1700', '1700 ᴄᴇ gegužė'],
            ['ABT @#DJULIAN@ MAY 1700', 'apie 1700 ᴄᴇ gegužės'],
            ['FROM @#DJULIAN@ MAY 1700', 'iš 1700 ᴄᴇ gegužės'],
            ['AFT @#DJULIAN@ MAY 1700', 'po 1700 ᴄᴇ gegužė'],
            ['BEF @#DJULIAN@ MAY 1700', 'prieš 1700 ᴄᴇ gegužė'],
            ['@#DJULIAN@ 15 JUN 1700', '1700 ᴄᴇ birželio 15'],
            ['@#DJULIAN@ JUN 1700', '1700 ᴄᴇ birželis'],
            ['ABT @#DJULIAN@ JUN 1700', 'apie 1700 ᴄᴇ birželio'],
            ['FROM @#DJULIAN@ JUN 1700', 'iš 1700 ᴄᴇ birželio'],
            ['AFT @#DJULIAN@ JUN 1700', 'po 1700 ᴄᴇ birželis'],
            ['BEF @#DJULIAN@ JUN 1700', 'prieš 1700 ᴄᴇ birželis'],
            ['@#DJULIAN@ 15 JUL 1700', '1700 ᴄᴇ liepos 15'],
            ['@#DJULIAN@ JUL 1700', '1700 ᴄᴇ liepa'],
            ['ABT @#DJULIAN@ JUL 1700', 'apie 1700 ᴄᴇ liepos'],
            ['FROM @#DJULIAN@ JUL 1700', 'iš 1700 ᴄᴇ liepos'],
            ['AFT @#DJULIAN@ JUL 1700', 'po 1700 ᴄᴇ liepa'],
            ['BEF @#DJULIAN@ JUL 1700', 'prieš 1700 ᴄᴇ liepa'],
            ['@#DJULIAN@ 15 AUG 1700', '1700 ᴄᴇ rugpjūčio 15'],
            ['@#DJULIAN@ AUG 1700', '1700 ᴄᴇ rugpjūtis'],
            ['ABT @#DJULIAN@ AUG 1700', 'apie 1700 ᴄᴇ rugpjūčio'],
            ['FROM @#DJULIAN@ AUG 1700', 'iš 1700 ᴄᴇ rugpjūčio'],
            ['AFT @#DJULIAN@ AUG 1700', 'po 1700 ᴄᴇ rugpjūtis'],
            ['BEF @#DJULIAN@ AUG 1700', 'prieš 1700 ᴄᴇ rugpjūtis'],
            ['@#DJULIAN@ 15 SEP 1700', '1700 ᴄᴇ rugsėjo 15'],
            ['@#DJULIAN@ SEP 1700', '1700 ᴄᴇ rugsėjis'],
            ['ABT @#DJULIAN@ SEP 1700', 'apie 1700 ᴄᴇ rugsėjo'],
            ['FROM @#DJULIAN@ SEP 1700', 'iš 1700 ᴄᴇ rugsėjo'],
            ['AFT @#DJULIAN@ SEP 1700', 'po 1700 ᴄᴇ rugsėjis'],
            ['BEF @#DJULIAN@ SEP 1700', 'prieš 1700 ᴄᴇ rugsėjis'],
            ['@#DJULIAN@ 15 OCT 1700', '1700 ᴄᴇ spalio 15'],
            ['@#DJULIAN@ OCT 1700', '1700 ᴄᴇ spalis'],
            ['ABT @#DJULIAN@ OCT 1700', 'apie 1700 ᴄᴇ spalio'],
            ['FROM @#DJULIAN@ OCT 1700', 'iš 1700 ᴄᴇ spalio'],
            ['AFT @#DJULIAN@ OCT 1700', 'po 1700 ᴄᴇ spalis'],
            ['BEF @#DJULIAN@ OCT 1700', 'prieš 1700 ᴄᴇ spalis'],
            ['@#DJULIAN@ 15 NOV 1700', '1700 ᴄᴇ lapkričio 15'],
            ['@#DJULIAN@ NOV 1700', '1700 ᴄᴇ lapkritis'],
            ['ABT @#DJULIAN@ NOV 1700', 'apie 1700 ᴄᴇ lapkričio'],
            ['FROM @#DJULIAN@ NOV 1700', 'iš 1700 ᴄᴇ lapkričio'],
            ['AFT @#DJULIAN@ NOV 1700', 'po 1700 ᴄᴇ lapkritis'],
            ['BEF @#DJULIAN@ NOV 1700', 'prieš 1700 ᴄᴇ lapkritis'],
            ['@#DJULIAN@ 15 DEC 1700', '1700 ᴄᴇ gruodžio 15'],
            ['@#DJULIAN@ DEC 1700', '1700 ᴄᴇ gruodis'],
            ['ABT @#DJULIAN@ DEC 1700', 'apie 1700 ᴄᴇ gruodžio'],
            ['FROM @#DJULIAN@ DEC 1700', 'iš 1700 ᴄᴇ gruodžio'],
            ['AFT @#DJULIAN@ DEC 1700', 'po 1700 ᴄᴇ gruodis'],
            ['BEF @#DJULIAN@ DEC 1700', 'prieš 1700 ᴄᴇ gruodis'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'apie 1700 ᴄᴇ sausio 15'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'apskaičiuota 1700 ᴄᴇ sausio 15'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'liko 1700 ᴄᴇ sausio 15'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'prieš 1700 ᴄᴇ sausio 15'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'po 1700 ᴄᴇ sausio 15'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'iš 1700 ᴄᴇ sausio 15'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'į 1700 ᴄᴇ sausio 15'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'tarp 1700 ᴄᴇ sausio 15 ir 1700 ᴄᴇ vasario 15'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'nuo 1700 ᴄᴇ sausio 15 iki 1700 ᴄᴇ vasario 15'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'nutraukta 1700 ᴄᴇ sausio 15'],
            ['@#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15'],
            ['@#DHEBREW@ TSH 5765', '5765 Tishrei'],
            ['ABT @#DHEBREW@ TSH 5765', 'apie 5765 Tishrei'],
            ['FROM @#DHEBREW@ TSH 5765', 'iš 5765 Tishrei'],
            ['AFT @#DHEBREW@ TSH 5765', 'po 5765 Tishrei'],
            ['BEF @#DHEBREW@ TSH 5765', 'prieš 5765 Tishrei'],
            ['@#DHEBREW@ 15 CSH 5765', '5765 Heshvan 15'],
            ['@#DHEBREW@ CSH 5765', '5765 Heshvan'],
            ['ABT @#DHEBREW@ CSH 5765', 'apie 5765 Heshvan'],
            ['FROM @#DHEBREW@ CSH 5765', 'iš 5765 Heshvan'],
            ['AFT @#DHEBREW@ CSH 5765', 'po 5765 Heshvan'],
            ['BEF @#DHEBREW@ CSH 5765', 'prieš 5765 Heshvan'],
            ['@#DHEBREW@ 15 KSL 5765', '5765 Kislev 15'],
            ['@#DHEBREW@ KSL 5765', '5765 Kislev'],
            ['ABT @#DHEBREW@ KSL 5765', 'apie 5765 Kislev'],
            ['FROM @#DHEBREW@ KSL 5765', 'iš 5765 Kislev'],
            ['AFT @#DHEBREW@ KSL 5765', 'po 5765 Kislev'],
            ['BEF @#DHEBREW@ KSL 5765', 'prieš 5765 Kislev'],
            ['@#DHEBREW@ 15 TVT 5765', '5765 Tevet 15'],
            ['@#DHEBREW@ TVT 5765', '5765 Tevet'],
            ['ABT @#DHEBREW@ TVT 5765', 'apie 5765 Tevet'],
            ['FROM @#DHEBREW@ TVT 5765', 'iš 5765 Tevet'],
            ['AFT @#DHEBREW@ TVT 5765', 'po 5765 Tevet'],
            ['BEF @#DHEBREW@ TVT 5765', 'prieš 5765 Tevet'],
            ['@#DHEBREW@ 15 SHV 5765', '5765 Shevat 15'],
            ['@#DHEBREW@ SHV 5765', '5765 Shevat'],
            ['ABT @#DHEBREW@ SHV 5765', 'apie 5765 Shevat'],
            ['FROM @#DHEBREW@ SHV 5765', 'iš 5765 Shevat'],
            ['AFT @#DHEBREW@ SHV 5765', 'po 5765 Shevat'],
            ['BEF @#DHEBREW@ SHV 5765', 'prieš 5765 Shevat'],
            ['@#DHEBREW@ 15 ADR 5765', '5765 Adar I 15'],
            ['@#DHEBREW@ ADR 5765', '5765 Adar I'],
            ['ABT @#DHEBREW@ ADR 5765', 'apie 5765 Adar I'],
            ['FROM @#DHEBREW@ ADR 5765', 'iš 5765 Adar I'],
            ['AFT @#DHEBREW@ ADR 5765', 'po 5765 Adar I'],
            ['BEF @#DHEBREW@ ADR 5765', 'prieš 5765 Adar I'],
            ['@#DHEBREW@ 15 ADS 5765', '5765 Adar II 15'],
            ['@#DHEBREW@ ADS 5765', '5765 Adar II'],
            ['ABT @#DHEBREW@ ADS 5765', 'apie 5765 Adar II'],
            ['FROM @#DHEBREW@ ADS 5765', 'iš 5765 Adar II'],
            ['AFT @#DHEBREW@ ADS 5765', 'po 5765 Adar II'],
            ['BEF @#DHEBREW@ ADS 5765', 'prieš 5765 Adar II'],
            ['@#DHEBREW@ 15 NSN 5765', '5765 Nissan 15'],
            ['@#DHEBREW@ NSN 5765', '5765 Nissan'],
            ['ABT @#DHEBREW@ NSN 5765', 'apie 5765 Nissan'],
            ['FROM @#DHEBREW@ NSN 5765', 'iš 5765 Nissan'],
            ['AFT @#DHEBREW@ NSN 5765', 'po 5765 Nissan'],
            ['BEF @#DHEBREW@ NSN 5765', 'prieš 5765 Nissan'],
            ['@#DHEBREW@ 15 IYR 5765', '5765 Iyar 15'],
            ['@#DHEBREW@ IYR 5765', '5765 Iyar'],
            ['ABT @#DHEBREW@ IYR 5765', 'apie 5765 Iyar'],
            ['FROM @#DHEBREW@ IYR 5765', 'iš 5765 Iyar'],
            ['AFT @#DHEBREW@ IYR 5765', 'po 5765 Iyar'],
            ['BEF @#DHEBREW@ IYR 5765', 'prieš 5765 Iyar'],
            ['@#DHEBREW@ 15 SVN 5765', '5765 Sivan 15'],
            ['@#DHEBREW@ SVN 5765', '5765 Sivan'],
            ['ABT @#DHEBREW@ SVN 5765', 'apie 5765 Sivan'],
            ['FROM @#DHEBREW@ SVN 5765', 'iš 5765 Sivan'],
            ['AFT @#DHEBREW@ SVN 5765', 'po 5765 Sivan'],
            ['BEF @#DHEBREW@ SVN 5765', 'prieš 5765 Sivan'],
            ['@#DHEBREW@ 15 TMZ 5765', '5765 Tamuz 15'],
            ['@#DHEBREW@ TMZ 5765', '5765 Tamuz'],
            ['ABT @#DHEBREW@ TMZ 5765', 'apie 5765 Tamuz'],
            ['FROM @#DHEBREW@ TMZ 5765', 'iš 5765 Tamuz'],
            ['AFT @#DHEBREW@ TMZ 5765', 'po 5765 Tamuz'],
            ['BEF @#DHEBREW@ TMZ 5765', 'prieš 5765 Tamuz'],
            ['@#DHEBREW@ 15 AAV 5765', '5765 Av 15'],
            ['@#DHEBREW@ AAV 5765', '5765 Av'],
            ['ABT @#DHEBREW@ AAV 5765', 'apie 5765 Av'],
            ['FROM @#DHEBREW@ AAV 5765', 'iš 5765 Av'],
            ['AFT @#DHEBREW@ AAV 5765', 'po 5765 Av'],
            ['BEF @#DHEBREW@ AAV 5765', 'prieš 5765 Av'],
            ['@#DHEBREW@ 15 ELL 5765', '5765 Elul 15'],
            ['@#DHEBREW@ ELL 5765', '5765 Elul'],
            ['ABT @#DHEBREW@ ELL 5765', 'apie 5765 Elul'],
            ['FROM @#DHEBREW@ ELL 5765', 'iš 5765 Elul'],
            ['AFT @#DHEBREW@ ELL 5765', 'po 5765 Elul'],
            ['BEF @#DHEBREW@ ELL 5765', 'prieš 5765 Elul'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'apie 5765 Tishrei 15'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'apskaičiuota 5765 Tishrei 15'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'liko 5765 Tishrei 15'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'prieš 5765 Tishrei 15'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'po 5765 Tishrei 15'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'iš 5765 Tishrei 15'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'į 5765 Tishrei 15'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'tarp 5765 Tishrei 15 ir 5765 Heshvan 15'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'nuo 5765 Tishrei 15 iki 5765 Heshvan 15'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'nutraukta 5765 Tishrei 15'],
            ['@#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15'],
            ['@#DFRENCH R@ VEND 12', 'An XII Vendémiaire'],
            ['ABT @#DFRENCH R@ VEND 12', 'apie An XII Vendémiaire'],
            ['FROM @#DFRENCH R@ VEND 12', 'iš An XII Vendémiaire'],
            ['AFT @#DFRENCH R@ VEND 12', 'po An XII Vendémiaire'],
            ['BEF @#DFRENCH R@ VEND 12', 'prieš An XII Vendémiaire'],
            ['@#DFRENCH R@ 15 BRUM 12', 'An XII Brumaire 15'],
            ['@#DFRENCH R@ BRUM 12', 'An XII Brumaire'],
            ['ABT @#DFRENCH R@ BRUM 12', 'apie An XII Brumaire'],
            ['FROM @#DFRENCH R@ BRUM 12', 'iš An XII Brumaire'],
            ['AFT @#DFRENCH R@ BRUM 12', 'po An XII Brumaire'],
            ['BEF @#DFRENCH R@ BRUM 12', 'prieš An XII Brumaire'],
            ['@#DFRENCH R@ 15 FRIM 12', 'An XII Frimaire 15'],
            ['@#DFRENCH R@ FRIM 12', 'An XII Frimaire'],
            ['ABT @#DFRENCH R@ FRIM 12', 'apie An XII Frimaire'],
            ['FROM @#DFRENCH R@ FRIM 12', 'iš An XII Frimaire'],
            ['AFT @#DFRENCH R@ FRIM 12', 'po An XII Frimaire'],
            ['BEF @#DFRENCH R@ FRIM 12', 'prieš An XII Frimaire'],
            ['@#DFRENCH R@ 15 NIVO 12', 'An XII Nivôse 15'],
            ['@#DFRENCH R@ NIVO 12', 'An XII Nivôse'],
            ['ABT @#DFRENCH R@ NIVO 12', 'apie An XII Nivôse'],
            ['FROM @#DFRENCH R@ NIVO 12', 'iš An XII Nivôse'],
            ['AFT @#DFRENCH R@ NIVO 12', 'po An XII Nivôse'],
            ['BEF @#DFRENCH R@ NIVO 12', 'prieš An XII Nivôse'],
            ['@#DFRENCH R@ 15 PLUV 12', 'An XII Pluviôse 15'],
            ['@#DFRENCH R@ PLUV 12', 'An XII Pluviôse'],
            ['ABT @#DFRENCH R@ PLUV 12', 'apie An XII Pluviôse'],
            ['FROM @#DFRENCH R@ PLUV 12', 'iš An XII Pluviôse'],
            ['AFT @#DFRENCH R@ PLUV 12', 'po An XII Pluviôse'],
            ['BEF @#DFRENCH R@ PLUV 12', 'prieš An XII Pluviôse'],
            ['@#DFRENCH R@ 15 VENT 12', 'An XII Ventôse 15'],
            ['@#DFRENCH R@ VENT 12', 'An XII Ventôse'],
            ['ABT @#DFRENCH R@ VENT 12', 'apie An XII Ventôse'],
            ['FROM @#DFRENCH R@ VENT 12', 'iš An XII Ventôse'],
            ['AFT @#DFRENCH R@ VENT 12', 'po An XII Ventôse'],
            ['BEF @#DFRENCH R@ VENT 12', 'prieš An XII Ventôse'],
            ['@#DFRENCH R@ 15 GERM 12', 'An XII Germinal 15'],
            ['@#DFRENCH R@ GERM 12', 'An XII Germinal'],
            ['ABT @#DFRENCH R@ GERM 12', 'apie An XII Germinal'],
            ['FROM @#DFRENCH R@ GERM 12', 'iš An XII Germinal'],
            ['AFT @#DFRENCH R@ GERM 12', 'po An XII Germinal'],
            ['BEF @#DFRENCH R@ GERM 12', 'prieš An XII Germinal'],
            ['@#DFRENCH R@ 15 FLOR 12', 'An XII Floréal 15'],
            ['@#DFRENCH R@ FLOR 12', 'An XII Floréal'],
            ['ABT @#DFRENCH R@ FLOR 12', 'apie An XII Floréal'],
            ['FROM @#DFRENCH R@ FLOR 12', 'iš An XII Floréal'],
            ['AFT @#DFRENCH R@ FLOR 12', 'po An XII Floréal'],
            ['BEF @#DFRENCH R@ FLOR 12', 'prieš An XII Floréal'],
            ['@#DFRENCH R@ 15 PRAI 12', 'An XII Prairial 15'],
            ['@#DFRENCH R@ PRAI 12', 'An XII Prairial'],
            ['ABT @#DFRENCH R@ PRAI 12', 'apie An XII Prairial'],
            ['FROM @#DFRENCH R@ PRAI 12', 'iš An XII Prairial'],
            ['AFT @#DFRENCH R@ PRAI 12', 'po An XII Prairial'],
            ['BEF @#DFRENCH R@ PRAI 12', 'prieš An XII Prairial'],
            ['@#DFRENCH R@ 15 MESS 12', 'An XII Messidor 15'],
            ['@#DFRENCH R@ MESS 12', 'An XII Messidor'],
            ['ABT @#DFRENCH R@ MESS 12', 'apie An XII Messidor'],
            ['FROM @#DFRENCH R@ MESS 12', 'iš An XII Messidor'],
            ['AFT @#DFRENCH R@ MESS 12', 'po An XII Messidor'],
            ['BEF @#DFRENCH R@ MESS 12', 'prieš An XII Messidor'],
            ['@#DFRENCH R@ 15 THER 12', 'An XII Thermidor 15'],
            ['@#DFRENCH R@ THER 12', 'An XII Thermidor'],
            ['ABT @#DFRENCH R@ THER 12', 'apie An XII Thermidor'],
            ['FROM @#DFRENCH R@ THER 12', 'iš An XII Thermidor'],
            ['AFT @#DFRENCH R@ THER 12', 'po An XII Thermidor'],
            ['BEF @#DFRENCH R@ THER 12', 'prieš An XII Thermidor'],
            ['@#DFRENCH R@ 15 FRUC 12', 'An XII Fructidor 15'],
            ['@#DFRENCH R@ FRUC 12', 'An XII Fructidor'],
            ['ABT @#DFRENCH R@ FRUC 12', 'apie An XII Fructidor'],
            ['FROM @#DFRENCH R@ FRUC 12', 'iš An XII Fructidor'],
            ['AFT @#DFRENCH R@ FRUC 12', 'po An XII Fructidor'],
            ['BEF @#DFRENCH R@ FRUC 12', 'prieš An XII Fructidor'],
            ['@#DFRENCH R@ 15 COMP 12', 'An XII jours complémentaires 15'],
            ['@#DFRENCH R@ COMP 12', 'An XII jours complémentaires'],
            ['ABT @#DFRENCH R@ COMP 12', 'apie An XII jours complémentaires'],
            ['FROM @#DFRENCH R@ COMP 12', 'iš An XII jours complémentaires'],
            ['AFT @#DFRENCH R@ COMP 12', 'po An XII jours complémentaires'],
            ['BEF @#DFRENCH R@ COMP 12', 'prieš An XII jours complémentaires'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'apie An XII Vendémiaire 15'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'apskaičiuota An XII Vendémiaire 15'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'liko An XII Vendémiaire 15'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'prieš An XII Vendémiaire 15'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'po An XII Vendémiaire 15'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'iš An XII Vendémiaire 15'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'į An XII Vendémiaire 15'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'tarp An XII Vendémiaire 15 ir An XII Brumaire 15'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'nuo An XII Vendémiaire 15 iki An XII Brumaire 15'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'nutraukta An XII Vendémiaire 15'],
            ['@#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15'],
            ['@#DHIJRI@ MUHAR 1425', '1425 Muharram'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'apie 1425 Muharram'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'iš 1425 Muharram'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'po 1425 Muharram'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'prieš 1425 Muharram'],
            ['@#DHIJRI@ 15 SAFAR 1425', '1425 Safar 15'],
            ['@#DHIJRI@ SAFAR 1425', '1425 Safar'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'apie 1425 Safar'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'iš 1425 Safar'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'po 1425 Safar'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'prieš 1425 Safar'],
            ['@#DHIJRI@ 15 RABIA 1425', '1425 Rabi’ al-awwal 15'],
            ['@#DHIJRI@ RABIA 1425', '1425 Rabi’ al-awwal'],
            ['ABT @#DHIJRI@ RABIA 1425', 'apie 1425 Rabi’ al-awwal'],
            ['FROM @#DHIJRI@ RABIA 1425', 'iš 1425 Rabi’ al-awwal'],
            ['AFT @#DHIJRI@ RABIA 1425', 'po 1425 Rabi’ al-awwal'],
            ['BEF @#DHIJRI@ RABIA 1425', 'prieš 1425 Rabi’ al-awwal'],
            ['@#DHIJRI@ 15 RABIT 1425', '1425 Rabi’ al-thani 15'],
            ['@#DHIJRI@ RABIT 1425', '1425 Rabi’ al-thani'],
            ['ABT @#DHIJRI@ RABIT 1425', 'apie 1425 Rabi’ al-thani'],
            ['FROM @#DHIJRI@ RABIT 1425', 'iš 1425 Rabi’ al-thani'],
            ['AFT @#DHIJRI@ RABIT 1425', 'po 1425 Rabi’ al-thani'],
            ['BEF @#DHIJRI@ RABIT 1425', 'prieš 1425 Rabi’ al-thani'],
            ['@#DHIJRI@ 15 JUMAA 1425', '1425 Jumada al-awwal 15'],
            ['@#DHIJRI@ JUMAA 1425', '1425 Jumada al-awwal'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'apie 1425 Jumada al-awwal'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'iš 1425 Jumada al-awwal'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'po 1425 Jumada al-awwal'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'prieš 1425 Jumada al-awwal'],
            ['@#DHIJRI@ 15 JUMAT 1425', '1425 Jumada al-thani 15'],
            ['@#DHIJRI@ JUMAT 1425', '1425 Jumada al-thani'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'apie 1425 Jumada al-thani'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'iš 1425 Jumada al-thani'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'po 1425 Jumada al-thani'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'prieš 1425 Jumada al-thani'],
            ['@#DHIJRI@ 15 RAJAB 1425', '1425 Rajab 15'],
            ['@#DHIJRI@ RAJAB 1425', '1425 Rajab'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'apie 1425 Rajab'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'iš 1425 Rajab'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'po 1425 Rajab'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'prieš 1425 Rajab'],
            ['@#DHIJRI@ 15 SHAAB 1425', '1425 Sha’aban 15'],
            ['@#DHIJRI@ SHAAB 1425', '1425 Sha’aban'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'apie 1425 Sha’aban'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'iš 1425 Sha’aban'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'po 1425 Sha’aban'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'prieš 1425 Sha’aban'],
            ['@#DHIJRI@ 15 RAMAD 1425', '1425 Ramadan 15'],
            ['@#DHIJRI@ RAMAD 1425', '1425 Ramadan'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'apie 1425 Ramadan'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'iš 1425 Ramadan'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'po 1425 Ramadan'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'prieš 1425 Ramadan'],
            ['@#DHIJRI@ 15 SHAWW 1425', '1425 Shawwal 15'],
            ['@#DHIJRI@ SHAWW 1425', '1425 Shawwal'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'apie 1425 Shawwal'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'iš 1425 Shawwal'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'po 1425 Shawwal'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'prieš 1425 Shawwal'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '1425 Dhu al-Qi’dah 15'],
            ['@#DHIJRI@ DHUAQ 1425', '1425 Dhu al-Qi’dah'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'apie 1425 Dhu al-Qi’dah'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'iš 1425 Dhu al-Qi’dah'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'po 1425 Dhu al-Qi’dah'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'prieš 1425 Dhu al-Qi’dah'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'apie 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'iš 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'po 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'prieš 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'apie 1425 Muharram 15'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'apskaičiuota 1425 Muharram 15'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'liko 1425 Muharram 15'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'prieš 1425 Muharram 15'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'po 1425 Muharram 15'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'iš 1425 Muharram 15'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'į 1425 Muharram 15'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'tarp 1425 Muharram 15 ir 1425 Safar 15'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'nuo 1425 Muharram 15 iki 1425 Safar 15'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'nutraukta 1425 Muharram 15'],
            ['@#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15'],
            ['@#DJALALI@ FARVA 1384', '1384 Farvardin'],
            ['ABT @#DJALALI@ FARVA 1384', 'apie 1384 Farvardin'],
            ['FROM @#DJALALI@ FARVA 1384', 'iš 1384 Farvardin'],
            ['AFT @#DJALALI@ FARVA 1384', 'po 1384 Farvardin'],
            ['BEF @#DJALALI@ FARVA 1384', 'prieš 1384 Farvardin'],
            ['@#DJALALI@ 15 ORDIB 1384', '1384 Ordibehesht 15'],
            ['@#DJALALI@ ORDIB 1384', '1384 Ordibehesht'],
            ['ABT @#DJALALI@ ORDIB 1384', 'apie 1384 Ordibehesht'],
            ['FROM @#DJALALI@ ORDIB 1384', 'iš 1384 Ordibehesht'],
            ['AFT @#DJALALI@ ORDIB 1384', 'po 1384 Ordibehesht'],
            ['BEF @#DJALALI@ ORDIB 1384', 'prieš 1384 Ordibehesht'],
            ['@#DJALALI@ 15 KHORD 1384', '1384 Khordad 15'],
            ['@#DJALALI@ KHORD 1384', '1384 Khordad'],
            ['ABT @#DJALALI@ KHORD 1384', 'apie 1384 Khordad'],
            ['FROM @#DJALALI@ KHORD 1384', 'iš 1384 Khordad'],
            ['AFT @#DJALALI@ KHORD 1384', 'po 1384 Khordad'],
            ['BEF @#DJALALI@ KHORD 1384', 'prieš 1384 Khordad'],
            ['@#DJALALI@ 15 TIR 1384', '1384 Tir 15'],
            ['@#DJALALI@ TIR 1384', '1384 Tir'],
            ['ABT @#DJALALI@ TIR 1384', 'apie 1384 Tir'],
            ['FROM @#DJALALI@ TIR 1384', 'iš 1384 Tir'],
            ['AFT @#DJALALI@ TIR 1384', 'po 1384 Tir'],
            ['BEF @#DJALALI@ TIR 1384', 'prieš 1384 Tir'],
            ['@#DJALALI@ 15 MORDA 1384', '1384 Mordad 15'],
            ['@#DJALALI@ MORDA 1384', '1384 Mordad'],
            ['ABT @#DJALALI@ MORDA 1384', 'apie 1384 Mordad'],
            ['FROM @#DJALALI@ MORDA 1384', 'iš 1384 Mordad'],
            ['AFT @#DJALALI@ MORDA 1384', 'po 1384 Mordad'],
            ['BEF @#DJALALI@ MORDA 1384', 'prieš 1384 Mordad'],
            ['@#DJALALI@ 15 SHAHR 1384', '1384 Shahrivar 15'],
            ['@#DJALALI@ SHAHR 1384', '1384 Shahrivar'],
            ['ABT @#DJALALI@ SHAHR 1384', 'apie 1384 Shahrivar'],
            ['FROM @#DJALALI@ SHAHR 1384', 'iš 1384 Shahrivar'],
            ['AFT @#DJALALI@ SHAHR 1384', 'po 1384 Shahrivar'],
            ['BEF @#DJALALI@ SHAHR 1384', 'prieš 1384 Shahrivar'],
            ['@#DJALALI@ 15 MEHR 1384', '1384 Mehr 15'],
            ['@#DJALALI@ MEHR 1384', '1384 Mehr'],
            ['ABT @#DJALALI@ MEHR 1384', 'apie 1384 Mehr'],
            ['FROM @#DJALALI@ MEHR 1384', 'iš 1384 Mehr'],
            ['AFT @#DJALALI@ MEHR 1384', 'po 1384 Mehr'],
            ['BEF @#DJALALI@ MEHR 1384', 'prieš 1384 Mehr'],
            ['@#DJALALI@ 15 ABAN 1384', '1384 Aban 15'],
            ['@#DJALALI@ ABAN 1384', '1384 Aban'],
            ['ABT @#DJALALI@ ABAN 1384', 'apie 1384 Aban'],
            ['FROM @#DJALALI@ ABAN 1384', 'iš 1384 Aban'],
            ['AFT @#DJALALI@ ABAN 1384', 'po 1384 Aban'],
            ['BEF @#DJALALI@ ABAN 1384', 'prieš 1384 Aban'],
            ['@#DJALALI@ 15 AZAR 1384', '1384 Azar 15'],
            ['@#DJALALI@ AZAR 1384', '1384 Azar'],
            ['ABT @#DJALALI@ AZAR 1384', 'apie 1384 Azar'],
            ['FROM @#DJALALI@ AZAR 1384', 'iš 1384 Azar'],
            ['AFT @#DJALALI@ AZAR 1384', 'po 1384 Azar'],
            ['BEF @#DJALALI@ AZAR 1384', 'prieš 1384 Azar'],
            ['@#DJALALI@ 15 DEY 1384', '1384 Dey 15'],
            ['@#DJALALI@ DEY 1384', '1384 Dey'],
            ['ABT @#DJALALI@ DEY 1384', 'apie 1384 Dey'],
            ['FROM @#DJALALI@ DEY 1384', 'iš 1384 Dey'],
            ['AFT @#DJALALI@ DEY 1384', 'po 1384 Dey'],
            ['BEF @#DJALALI@ DEY 1384', 'prieš 1384 Dey'],
            ['@#DJALALI@ 15 BAHMA 1384', '1384 Bahman 15'],
            ['@#DJALALI@ BAHMA 1384', '1384 Bahman'],
            ['ABT @#DJALALI@ BAHMA 1384', 'apie 1384 Bahman'],
            ['FROM @#DJALALI@ BAHMA 1384', 'iš 1384 Bahman'],
            ['AFT @#DJALALI@ BAHMA 1384', 'po 1384 Bahman'],
            ['BEF @#DJALALI@ BAHMA 1384', 'prieš 1384 Bahman'],
            ['@#DJALALI@ 15 ESFAN 1384', '1384 Esfand 15'],
            ['@#DJALALI@ ESFAN 1384', '1384 Esfand'],
            ['ABT @#DJALALI@ ESFAN 1384', 'apie 1384 Esfand'],
            ['FROM @#DJALALI@ ESFAN 1384', 'iš 1384 Esfand'],
            ['AFT @#DJALALI@ ESFAN 1384', 'po 1384 Esfand'],
            ['BEF @#DJALALI@ ESFAN 1384', 'prieš 1384 Esfand'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'apie 1384 Farvardin 15'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'apskaičiuota 1384 Farvardin 15'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'liko 1384 Farvardin 15'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'prieš 1384 Farvardin 15'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'po 1384 Farvardin 15'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'iš 1384 Farvardin 15'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'į 1384 Farvardin 15'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'tarp 1384 Farvardin 15 ir 1384 Ordibehesht 15'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'nuo 1384 Farvardin 15 iki 1384 Ordibehesht 15'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'nutraukta 1384 Farvardin 15'],
        ];
    }
}
