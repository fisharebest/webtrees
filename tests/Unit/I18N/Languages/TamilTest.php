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
use Fisharebest\Webtrees\I18N\Languages\Tamil;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Tamil::class)]
class TamilTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Tamil();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Taml, self::language()->script());
    }

    public function testFirstDay(): void
    {
        self::assertSame(Weekday::Sunday, self::language()->firstDay());
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
        self::assertSame('ta', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('தமிழ்', self::language()->endonym());
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
        self::assertSame('-௧௨௩,௪௫௬.௦௭௮௯', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('-௧,௨௩,௪௫௬.௦௭௮௯', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-௧,௨௩,௪௫௬.௦௭௮௯%', self::language()->percentage(-1234.560789));
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
        self::assertSame('one மற்றும் two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two மற்றும் three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one அல்லது two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two அல்லது three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('மனைவி', 'கணவன்', [$husband, $fm, $wife]);
        self::assertRelationshipNames('முன்னாள் துணை', 'முன்னாள் துணை', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('நிச்சயதார்த்தம்', 'நிச்சயதார்த்தம்', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('அம்மா', 'மகன்', [$son, $fm, $wife]);
        self::assertRelationshipNames('அப்பா', 'மகன்', [$son, $fm, $husband]);
        self::assertRelationshipNames('அம்மா', 'மகள்', [$daughter, $fm, $wife]);

        // Siblings — elder/younger
        self::assertRelationshipNames('தங்கை', 'அண்ணன்', [$son, $fm, $daughter]);
        self::assertRelationshipNames('அண்ணன்', 'தங்கை', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('ஒன்றுவிட்ட அக்கா', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('மாற்றான் அப்பா', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('வளர்ப்பு மகள்', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('மாமியார்', 'மருமகன்', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('மாமனார்', 'மருமகன்', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('மருமகள்', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('பாட்டி', 'பேரன்', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('தாத்தா', 'பேரன்', [$son, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipNames('பாட்டி', 'பேரன்', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('தாத்தா', 'பேரன்', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('கொள்ளுதாத்தா', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('கொள்ளுபாட்டி', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles — paternal
        self::assertRelationshipNames('அத்தை', 'மருமகன்', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('சித்தப்பா', 'மருமகன்', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('மருமகள்', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('மருமகன்', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('உறவினர்', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('உறவினர்', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }

    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '௧௫ ஜனவரி ௨௦௦௦'],
            ['JAN 2000', 'ஜனவரி ௨௦௦௦'],
            ['ABT JAN 2000', 'about ஜனவரி ௨௦௦௦'],
            ['FROM JAN 2000', 'ஜனவரி ௨௦௦௦ இலிருந்து'],
            ['AFT JAN 2000', 'after ஜனவரி ௨௦௦௦'],
            ['BEF JAN 2000', 'before ஜனவரி ௨௦௦௦'],
            ['15 FEB 2000', '௧௫ பிப்ரவரி ௨௦௦௦'],
            ['FEB 2000', 'பிப்ரவரி ௨௦௦௦'],
            ['ABT FEB 2000', 'about பிப்ரவரி ௨௦௦௦'],
            ['FROM FEB 2000', 'பிப்ரவரி ௨௦௦௦ இலிருந்து'],
            ['AFT FEB 2000', 'after பிப்ரவரி ௨௦௦௦'],
            ['BEF FEB 2000', 'before பிப்ரவரி ௨௦௦௦'],
            ['15 MAR 2000', '௧௫ மார்ச் ௨௦௦௦'],
            ['MAR 2000', 'மார்ச் ௨௦௦௦'],
            ['ABT MAR 2000', 'about மார்ச் ௨௦௦௦'],
            ['FROM MAR 2000', 'மார்ச் ௨௦௦௦ இலிருந்து'],
            ['AFT MAR 2000', 'after மார்ச் ௨௦௦௦'],
            ['BEF MAR 2000', 'before மார்ச் ௨௦௦௦'],
            ['15 APR 2000', '௧௫ ஏப்ரல் ௨௦௦௦'],
            ['APR 2000', 'ஏப்ரல் ௨௦௦௦'],
            ['ABT APR 2000', 'about ஏப்ரல் ௨௦௦௦'],
            ['FROM APR 2000', 'ஏப்ரல் ௨௦௦௦ இலிருந்து'],
            ['AFT APR 2000', 'after ஏப்ரல் ௨௦௦௦'],
            ['BEF APR 2000', 'before ஏப்ரல் ௨௦௦௦'],
            ['15 MAY 2000', '௧௫ மே ௨௦௦௦'],
            ['MAY 2000', 'மே ௨௦௦௦'],
            ['ABT MAY 2000', 'about மே ௨௦௦௦'],
            ['FROM MAY 2000', 'மே ௨௦௦௦ இலிருந்து'],
            ['AFT MAY 2000', 'after மே ௨௦௦௦'],
            ['BEF MAY 2000', 'before மே ௨௦௦௦'],
            ['15 JUN 2000', '௧௫ ஜூன் ௨௦௦௦'],
            ['JUN 2000', 'ஜூன் ௨௦௦௦'],
            ['ABT JUN 2000', 'about ஜூன் ௨௦௦௦'],
            ['FROM JUN 2000', 'ஜூன் ௨௦௦௦ இலிருந்து'],
            ['AFT JUN 2000', 'after ஜூன் ௨௦௦௦'],
            ['BEF JUN 2000', 'before ஜூன் ௨௦௦௦'],
            ['15 JUL 2000', '௧௫ ஜூலை ௨௦௦௦'],
            ['JUL 2000', 'ஜூலை ௨௦௦௦'],
            ['ABT JUL 2000', 'about ஜூலை ௨௦௦௦'],
            ['FROM JUL 2000', 'ஜூலை ௨௦௦௦ இலிருந்து'],
            ['AFT JUL 2000', 'after ஜூலை ௨௦௦௦'],
            ['BEF JUL 2000', 'before ஜூலை ௨௦௦௦'],
            ['15 AUG 2000', '௧௫ ஆகஸ்ட் ௨௦௦௦'],
            ['AUG 2000', 'ஆகஸ்ட் ௨௦௦௦'],
            ['ABT AUG 2000', 'about ஆகஸ்ட் ௨௦௦௦'],
            ['FROM AUG 2000', 'ஆகஸ்ட் ௨௦௦௦ இலிருந்து'],
            ['AFT AUG 2000', 'after ஆகஸ்ட் ௨௦௦௦'],
            ['BEF AUG 2000', 'before ஆகஸ்ட் ௨௦௦௦'],
            ['15 SEP 2000', '௧௫ செப்டம்பர் ௨௦௦௦'],
            ['SEP 2000', 'செப்டம்பர் ௨௦௦௦'],
            ['ABT SEP 2000', 'about செப்டம்பர் ௨௦௦௦'],
            ['FROM SEP 2000', 'செப்டம்பர் ௨௦௦௦ இலிருந்து'],
            ['AFT SEP 2000', 'after செப்டம்பர் ௨௦௦௦'],
            ['BEF SEP 2000', 'before செப்டம்பர் ௨௦௦௦'],
            ['15 OCT 2000', '௧௫ அக்டோபர் ௨௦௦௦'],
            ['OCT 2000', 'அக்டோபர் ௨௦௦௦'],
            ['ABT OCT 2000', 'about அக்டோபர் ௨௦௦௦'],
            ['FROM OCT 2000', 'அக்டோபர் ௨௦௦௦ இலிருந்து'],
            ['AFT OCT 2000', 'after அக்டோபர் ௨௦௦௦'],
            ['BEF OCT 2000', 'before அக்டோபர் ௨௦௦௦'],
            ['15 NOV 2000', '௧௫ நவம்பர் ௨௦௦௦'],
            ['NOV 2000', 'நவம்பர் ௨௦௦௦'],
            ['ABT NOV 2000', 'about நவம்பர் ௨௦௦௦'],
            ['FROM NOV 2000', 'நவம்பர் ௨௦௦௦ இலிருந்து'],
            ['AFT NOV 2000', 'after நவம்பர் ௨௦௦௦'],
            ['BEF NOV 2000', 'before நவம்பர் ௨௦௦௦'],
            ['15 DEC 2000', '௧௫ டிசம்பர் ௨௦௦௦'],
            ['DEC 2000', 'டிசம்பர் ௨௦௦௦'],
            ['ABT DEC 2000', 'about டிசம்பர் ௨௦௦௦'],
            ['FROM DEC 2000', 'டிசம்பர் ௨௦௦௦ இலிருந்து'],
            ['AFT DEC 2000', 'after டிசம்பர் ௨௦௦௦'],
            ['BEF DEC 2000', 'before டிசம்பர் ௨௦௦௦'],
            ['2000', '௨௦௦௦'],
            ['ABT 15 JAN 2000', 'about ௧௫ ஜனவரி ௨௦௦௦'],
            ['CAL 15 JAN 2000', 'calculated ௧௫ ஜனவரி ௨௦௦௦'],
            ['EST 15 JAN 2000', 'estimated ௧௫ ஜனவரி ௨௦௦௦'],
            ['BEF 15 JAN 2000', 'before ௧௫ ஜனவரி ௨௦௦௦'],
            ['AFT 15 JAN 2000', 'after ௧௫ ஜனவரி ௨௦௦௦'],
            ['FROM 15 JAN 2000', '௧௫ ஜனவரி ௨௦௦௦ இலிருந்து'],
            ['TO 15 JAN 2000', 'to ௧௫ ஜனவரி ௨௦௦௦'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between ௧௫ ஜனவரி ௨௦௦௦ and ௧௫ பிப்ரவரி ௨௦௦௦'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from ௧௫ ஜனவரி ௨௦௦௦ to ௧௫ பிப்ரவரி ௨௦௦௦'],
            ['INT 15 JAN 2000', 'interpreted ௧௫ ஜனவரி ௨௦௦௦'],
            ['@#DJULIAN@ 15 JAN 1700', '௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'ஜனவரி ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ JAN 1700', 'after ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '௧௫ பிப்ரவரி ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'பிப்ரவரி ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about பிப்ரவரி ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'பிப்ரவரி ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ FEB 1700', 'after பிப்ரவரி ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before பிப்ரவரி ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '௧௫ மார்ச் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'மார்ச் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about மார்ச் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'மார்ச் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ MAR 1700', 'after மார்ச் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before மார்ச் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '௧௫ ஏப்ரல் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '௧௪ ஏப்ரல் ௧௬௪௫/௪௬ ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'ஏப்ரல் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about ஏப்ரல் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'ஏப்ரல் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ APR 1700', 'after ஏப்ரல் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before ஏப்ரல் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '௧௫ மே ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'மே ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about மே ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'மே ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ MAY 1700', 'after மே ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before மே ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '௧௫ ஜூன் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'ஜூன் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about ஜூன் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'ஜூன் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ JUN 1700', 'after ஜூன் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before ஜூன் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '௧௫ ஜூலை ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'ஜூலை ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about ஜூலை ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'ஜூலை ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ JUL 1700', 'after ஜூலை ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before ஜூலை ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '௧௫ ஆகஸ்ட் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'ஆகஸ்ட் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about ஆகஸ்ட் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'ஆகஸ்ட் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ AUG 1700', 'after ஆகஸ்ட் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before ஆகஸ்ட் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '௧௫ செப்டம்பர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'செப்டம்பர் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about செப்டம்பர் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'செப்டம்பர் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ SEP 1700', 'after செப்டம்பர் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before செப்டம்பர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '௧௫ அக்டோபர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'அக்டோபர் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about அக்டோபர் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'அக்டோபர் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ OCT 1700', 'after அக்டோபர் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before அக்டோபர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '௧௫ நவம்பர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'நவம்பர் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about நவம்பர் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'நவம்பர் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ NOV 1700', 'after நவம்பர் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before நவம்பர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '௧௫ டிசம்பர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'டிசம்பர் ௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about டிசம்பர் ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'டிசம்பர் ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['AFT @#DJULIAN@ DEC 1700', 'after டிசம்பர் ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before டிசம்பர் ௧௭௦௦ ᴄᴇ'],
            ['@#DJULIAN@ 1700', '௧௭௦௦ ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ இலிருந்து'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ and ௧௫ பிப்ரவரி ௧௭௦௦ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ to ௧௫ பிப்ரவரி ௧௭௦௦ ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted ௧௫ ஜனவரி ௧௭௦௦ ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '௧௫ திஷ்ரி ௫௭௬௫'],
            ['@#DHEBREW@ TSH 5765', 'திஷ்ரி ௫௭௬௫'],
            ['ABT @#DHEBREW@ TSH 5765', 'about திஷ்ரி ௫௭௬௫'],
            ['FROM @#DHEBREW@ TSH 5765', 'திஷ்ரி ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ TSH 5765', 'after திஷ்ரி ௫௭௬௫'],
            ['BEF @#DHEBREW@ TSH 5765', 'before திஷ்ரி ௫௭௬௫'],
            ['@#DHEBREW@ 15 CSH 5765', '௧௫ ஹெஷ்வான் ௫௭௬௫'],
            ['@#DHEBREW@ CSH 5765', 'ஹெஷ்வான் ௫௭௬௫'],
            ['ABT @#DHEBREW@ CSH 5765', 'about ஹெஷ்வான் ௫௭௬௫'],
            ['FROM @#DHEBREW@ CSH 5765', 'ஹெஷ்வான் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ CSH 5765', 'after ஹெஷ்வான் ௫௭௬௫'],
            ['BEF @#DHEBREW@ CSH 5765', 'before ஹெஷ்வான் ௫௭௬௫'],
            ['@#DHEBREW@ 15 KSL 5765', '௧௫ கிஸ்லேவ் ௫௭௬௫'],
            ['@#DHEBREW@ KSL 5765', 'கிஸ்லேவ் ௫௭௬௫'],
            ['ABT @#DHEBREW@ KSL 5765', 'about கிஸ்லேவ் ௫௭௬௫'],
            ['FROM @#DHEBREW@ KSL 5765', 'கிஸ்லேவ் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ KSL 5765', 'after கிஸ்லேவ் ௫௭௬௫'],
            ['BEF @#DHEBREW@ KSL 5765', 'before கிஸ்லேவ் ௫௭௬௫'],
            ['@#DHEBREW@ 15 TVT 5765', '௧௫ தேவேத் ௫௭௬௫'],
            ['@#DHEBREW@ TVT 5765', 'தேவேத் ௫௭௬௫'],
            ['ABT @#DHEBREW@ TVT 5765', 'about தேவேத் ௫௭௬௫'],
            ['FROM @#DHEBREW@ TVT 5765', 'தேவேத் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ TVT 5765', 'after தேவேத் ௫௭௬௫'],
            ['BEF @#DHEBREW@ TVT 5765', 'before தேவேத் ௫௭௬௫'],
            ['@#DHEBREW@ 15 SHV 5765', '௧௫ ஷெவாத் ௫௭௬௫'],
            ['@#DHEBREW@ SHV 5765', 'ஷெவாத் ௫௭௬௫'],
            ['ABT @#DHEBREW@ SHV 5765', 'about ஷெவாத் ௫௭௬௫'],
            ['FROM @#DHEBREW@ SHV 5765', 'ஷெவாத் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ SHV 5765', 'after ஷெவாத் ௫௭௬௫'],
            ['BEF @#DHEBREW@ SHV 5765', 'before ஷெவாத் ௫௭௬௫'],
            ['@#DHEBREW@ 15 ADR 5765', '௧௫ ஆதார் ௧ ௫௭௬௫'],
            ['@#DHEBREW@ ADR 5765', 'ஆதார் ௧ ௫௭௬௫'],
            ['ABT @#DHEBREW@ ADR 5765', 'about ஆதார் ௧ ௫௭௬௫'],
            ['FROM @#DHEBREW@ ADR 5765', 'ஆதார் ௧ ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ ADR 5765', 'after ஆதார் ௧ ௫௭௬௫'],
            ['BEF @#DHEBREW@ ADR 5765', 'before ஆதார் ௧ ௫௭௬௫'],
            ['@#DHEBREW@ 15 ADS 5765', '௧௫ ஆதார் ௨ ௫௭௬௫'],
            ['@#DHEBREW@ ADS 5765', 'ஆதார் ௨ ௫௭௬௫'],
            ['ABT @#DHEBREW@ ADS 5765', 'about ஆதார் ௨ ௫௭௬௫'],
            ['FROM @#DHEBREW@ ADS 5765', 'ஆதார் ௨ ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ ADS 5765', 'after ஆதார் ௨ ௫௭௬௫'],
            ['BEF @#DHEBREW@ ADS 5765', 'before ஆதார் ௨ ௫௭௬௫'],
            ['@#DHEBREW@ 15 NSN 5765', '௧௫ நிசான் ௫௭௬௫'],
            ['@#DHEBREW@ NSN 5765', 'நிசான் ௫௭௬௫'],
            ['ABT @#DHEBREW@ NSN 5765', 'about நிசான் ௫௭௬௫'],
            ['FROM @#DHEBREW@ NSN 5765', 'நிசான் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ NSN 5765', 'after நிசான் ௫௭௬௫'],
            ['BEF @#DHEBREW@ NSN 5765', 'before நிசான் ௫௭௬௫'],
            ['@#DHEBREW@ 15 IYR 5765', '௧௫ இயார் ௫௭௬௫'],
            ['@#DHEBREW@ IYR 5765', 'இயார் ௫௭௬௫'],
            ['ABT @#DHEBREW@ IYR 5765', 'about இயார் ௫௭௬௫'],
            ['FROM @#DHEBREW@ IYR 5765', 'இயார் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ IYR 5765', 'after இயார் ௫௭௬௫'],
            ['BEF @#DHEBREW@ IYR 5765', 'before இயார் ௫௭௬௫'],
            ['@#DHEBREW@ 15 SVN 5765', '௧௫ சிவான் ௫௭௬௫'],
            ['@#DHEBREW@ SVN 5765', 'சிவான் ௫௭௬௫'],
            ['ABT @#DHEBREW@ SVN 5765', 'about சிவான் ௫௭௬௫'],
            ['FROM @#DHEBREW@ SVN 5765', 'சிவான் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ SVN 5765', 'after சிவான் ௫௭௬௫'],
            ['BEF @#DHEBREW@ SVN 5765', 'before சிவான் ௫௭௬௫'],
            ['@#DHEBREW@ 15 TMZ 5765', '௧௫ தமூஸ் ௫௭௬௫'],
            ['@#DHEBREW@ TMZ 5765', 'தமூஸ் ௫௭௬௫'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about தமூஸ் ௫௭௬௫'],
            ['FROM @#DHEBREW@ TMZ 5765', 'தமூஸ் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after தமூஸ் ௫௭௬௫'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before தமூஸ் ௫௭௬௫'],
            ['@#DHEBREW@ 15 AAV 5765', '௧௫ ஆவ் ௫௭௬௫'],
            ['@#DHEBREW@ AAV 5765', 'ஆவ் ௫௭௬௫'],
            ['ABT @#DHEBREW@ AAV 5765', 'about ஆவ் ௫௭௬௫'],
            ['FROM @#DHEBREW@ AAV 5765', 'ஆவ் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ AAV 5765', 'after ஆவ் ௫௭௬௫'],
            ['BEF @#DHEBREW@ AAV 5765', 'before ஆவ் ௫௭௬௫'],
            ['@#DHEBREW@ 15 ELL 5765', '௧௫ எலுல் ௫௭௬௫'],
            ['@#DHEBREW@ ELL 5765', 'எலுல் ௫௭௬௫'],
            ['ABT @#DHEBREW@ ELL 5765', 'about எலுல் ௫௭௬௫'],
            ['FROM @#DHEBREW@ ELL 5765', 'எலுல் ௫௭௬௫ இலிருந்து'],
            ['AFT @#DHEBREW@ ELL 5765', 'after எலுல் ௫௭௬௫'],
            ['BEF @#DHEBREW@ ELL 5765', 'before எலுல் ௫௭௬௫'],
            ['@#DHEBREW@ 5765', '௫௭௬௫'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about ௧௫ திஷ்ரி ௫௭௬௫'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated ௧௫ திஷ்ரி ௫௭௬௫'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated ௧௫ திஷ்ரி ௫௭௬௫'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before ௧௫ திஷ்ரி ௫௭௬௫'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after ௧௫ திஷ்ரி ௫௭௬௫'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '௧௫ திஷ்ரி ௫௭௬௫ இலிருந்து'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to ௧௫ திஷ்ரி ௫௭௬௫'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between ௧௫ திஷ்ரி ௫௭௬௫ and ௧௫ ஹெஷ்வான் ௫௭௬௫'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from ௧௫ திஷ்ரி ௫௭௬௫ to ௧௫ ஹெஷ்வான் ௫௭௬௫'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted ௧௫ திஷ்ரி ௫௭௬௫'],
            ['@#DFRENCH R@ 15 VEND 12', '௧௫ வாண்டெமியேர் An XII'],
            ['@#DFRENCH R@ VEND 12', 'வாண்டெமியேர் An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about வாண்டெமியேர் An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'வாண்டெமியேர் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ VEND 12', 'after வாண்டெமியேர் An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before வாண்டெமியேர் An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '௧௫ ப்ரூமேர் An XII'],
            ['@#DFRENCH R@ BRUM 12', 'ப்ரூமேர் An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about ப்ரூமேர் An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'ப்ரூமேர் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after ப்ரூமேர் An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before ப்ரூமேர் An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '௧௫ ஃப்ரிமேர் An XII'],
            ['@#DFRENCH R@ FRIM 12', 'ஃப்ரிமேர் An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about ஃப்ரிமேர் An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'ஃப்ரிமேர் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after ஃப்ரிமேர் An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before ஃப்ரிமேர் An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '௧௫ நிவோஸ் An XII'],
            ['@#DFRENCH R@ NIVO 12', 'நிவோஸ் An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about நிவோஸ் An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'நிவோஸ் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after நிவோஸ் An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before நிவோஸ் An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '௧௫ ப்ளூவியோஸ் An XII'],
            ['@#DFRENCH R@ PLUV 12', 'ப்ளூவியோஸ் An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about ப்ளூவியோஸ் An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'ப்ளூவியோஸ் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after ப்ளூவியோஸ் An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before ப்ளூவியோஸ் An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '௧௫ வாண்டோஸ் An XII'],
            ['@#DFRENCH R@ VENT 12', 'வாண்டோஸ் An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about வாண்டோஸ் An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'வாண்டோஸ் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ VENT 12', 'after வாண்டோஸ் An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before வாண்டோஸ் An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '௧௫ ஜெர்மினல் An XII'],
            ['@#DFRENCH R@ GERM 12', 'ஜெர்மினல் An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about ஜெர்மினல் An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'ஜெர்மினல் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ GERM 12', 'after ஜெர்மினல் An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before ஜெர்மினல் An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '௧௫ ஃப்ளோரியல் An XII'],
            ['@#DFRENCH R@ FLOR 12', 'ஃப்ளோரியல் An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about ஃப்ளோரியல் An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'ஃப்ளோரியல் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after ஃப்ளோரியல் An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before ஃப்ளோரியல் An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '௧௫ ப்ரேரியல் An XII'],
            ['@#DFRENCH R@ PRAI 12', 'ப்ரேரியல் An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about ப்ரேரியல் An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'ப்ரேரியல் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after ப்ரேரியல் An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before ப்ரேரியல் An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '௧௫ மெசிடோர் An XII'],
            ['@#DFRENCH R@ MESS 12', 'மெசிடோர் An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about மெசிடோர் An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'மெசிடோர் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ MESS 12', 'after மெசிடோர் An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before மெசிடோர் An XII'],
            ['@#DFRENCH R@ 15 THER 12', '௧௫ தெர்மிடோர் An XII'],
            ['@#DFRENCH R@ THER 12', 'தெர்மிடோர் An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about தெர்மிடோர் An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'தெர்மிடோர் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ THER 12', 'after தெர்மிடோர் An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before தெர்மிடோர் An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '௧௫ ஃப்ரக்டிடோர் An XII'],
            ['@#DFRENCH R@ FRUC 12', 'ஃப்ரக்டிடோர் An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about ஃப்ரக்டிடோர் An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'ஃப்ரக்டிடோர் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after ஃப்ரக்டிடோர் An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before ஃப்ரக்டிடோர் An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '௧௫ நிரப்பு நாட்கள் An XII'],
            ['@#DFRENCH R@ COMP 12', 'நிரப்பு நாட்கள் An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about நிரப்பு நாட்கள் An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'நிரப்பு நாட்கள் An XII இலிருந்து'],
            ['AFT @#DFRENCH R@ COMP 12', 'after நிரப்பு நாட்கள் An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before நிரப்பு நாட்கள் An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about ௧௫ வாண்டெமியேர் An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated ௧௫ வாண்டெமியேர் An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated ௧௫ வாண்டெமியேர் An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before ௧௫ வாண்டெமியேர் An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after ௧௫ வாண்டெமியேர் An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '௧௫ வாண்டெமியேர் An XII இலிருந்து'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to ௧௫ வாண்டெமியேர் An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between ௧௫ வாண்டெமியேர் An XII and ௧௫ ப்ரூமேர் An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from ௧௫ வாண்டெமியேர் An XII to ௧௫ ப்ரூமேர் An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted ௧௫ வாண்டெமியேர் An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '௧௫ முஹர்ரம் ௧௪௨௫'],
            ['@#DHIJRI@ MUHAR 1425', 'முஹர்ரம் ௧௪௨௫'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about முஹர்ரம் ௧௪௨௫'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'முஹர்ரம் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after முஹர்ரம் ௧௪௨௫'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before முஹர்ரம் ௧௪௨௫'],
            ['@#DHIJRI@ 15 SAFAR 1425', '௧௫ சஃபர் ௧௪௨௫'],
            ['@#DHIJRI@ SAFAR 1425', 'சஃபர் ௧௪௨௫'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about சஃபர் ௧௪௨௫'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'சஃபர் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after சஃபர் ௧௪௨௫'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before சஃபர் ௧௪௨௫'],
            ['@#DHIJRI@ 15 RABIA 1425', '௧௫ ரபீஉல் அவ்வல் ௧௪௨௫'],
            ['@#DHIJRI@ RABIA 1425', 'ரபீஉல் அவ்வல் ௧௪௨௫'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about ரபீஉல் அவ்வல் ௧௪௨௫'],
            ['FROM @#DHIJRI@ RABIA 1425', 'ரபீஉல் அவ்வல் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after ரபீஉல் அவ்வல் ௧௪௨௫'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before ரபீஉல் அவ்வல் ௧௪௨௫'],
            ['@#DHIJRI@ 15 RABIT 1425', '௧௫ ரபீஉல் ஆகிர் ௧௪௨௫'],
            ['@#DHIJRI@ RABIT 1425', 'ரபீஉல் ஆகிர் ௧௪௨௫'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about ரபீஉல் ஆகிர் ௧௪௨௫'],
            ['FROM @#DHIJRI@ RABIT 1425', 'ரபீஉல் ஆகிர் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after ரபீஉல் ஆகிர் ௧௪௨௫'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before ரபீஉல் ஆகிர் ௧௪௨௫'],
            ['@#DHIJRI@ 15 JUMAA 1425', '௧௫ ஜுமாதல் அவ்வல் ௧௪௨௫'],
            ['@#DHIJRI@ JUMAA 1425', 'ஜுமாதல் அவ்வல் ௧௪௨௫'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about ஜுமாதல் அவ்வல் ௧௪௨௫'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'ஜுமாதல் அவ்வல் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after ஜுமாதல் அவ்வல் ௧௪௨௫'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before ஜுமாதல் அவ்வல் ௧௪௨௫'],
            ['@#DHIJRI@ 15 JUMAT 1425', '௧௫ ஜுமாதல் ஆகிர் ௧௪௨௫'],
            ['@#DHIJRI@ JUMAT 1425', 'ஜுமாதல் ஆகிர் ௧௪௨௫'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about ஜுமாதல் ஆகிர் ௧௪௨௫'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'ஜுமாதல் ஆகிர் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after ஜுமாதல் ஆகிர் ௧௪௨௫'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before ஜுமாதல் ஆகிர் ௧௪௨௫'],
            ['@#DHIJRI@ 15 RAJAB 1425', '௧௫ ரஜப் ௧௪௨௫'],
            ['@#DHIJRI@ RAJAB 1425', 'ரஜப் ௧௪௨௫'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about ரஜப் ௧௪௨௫'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'ரஜப் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after ரஜப் ௧௪௨௫'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before ரஜப் ௧௪௨௫'],
            ['@#DHIJRI@ 15 SHAAB 1425', '௧௫ ஷஃபான் ௧௪௨௫'],
            ['@#DHIJRI@ SHAAB 1425', 'ஷஃபான் ௧௪௨௫'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about ஷஃபான் ௧௪௨௫'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'ஷஃபான் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after ஷஃபான் ௧௪௨௫'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before ஷஃபான் ௧௪௨௫'],
            ['@#DHIJRI@ 15 RAMAD 1425', '௧௫ ரமலான் ௧௪௨௫'],
            ['@#DHIJRI@ RAMAD 1425', 'ரமலான் ௧௪௨௫'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about ரமலான் ௧௪௨௫'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'ரமலான் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after ரமலான் ௧௪௨௫'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before ரமலான் ௧௪௨௫'],
            ['@#DHIJRI@ 15 SHAWW 1425', '௧௫ ஷவ்வால் ௧௪௨௫'],
            ['@#DHIJRI@ SHAWW 1425', 'ஷவ்வால் ௧௪௨௫'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about ஷவ்வால் ௧௪௨௫'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'ஷவ்வால் ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after ஷவ்வால் ௧௪௨௫'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before ஷவ்வால் ௧௪௨௫'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '௧௫ துல்கஃதா ௧௪௨௫'],
            ['@#DHIJRI@ DHUAQ 1425', 'துல்கஃதா ௧௪௨௫'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about துல்கஃதா ௧௪௨௫'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'துல்கஃதா ௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after துல்கஃதா ௧௪௨௫'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before துல்கஃதா ௧௪௨௫'],
            ['@#DHIJRI@ 15 DHUAL 1425', '௧௪௨௫'],
            ['@#DHIJRI@ DHUAL 1425', '௧௪௨௫'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about ௧௪௨௫'],
            ['FROM @#DHIJRI@ DHUAL 1425', '௧௪௨௫ இலிருந்து'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after ௧௪௨௫'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before ௧௪௨௫'],
            ['@#DHIJRI@ 1425', '௧௪௨௫'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about ௧௫ முஹர்ரம் ௧௪௨௫'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated ௧௫ முஹர்ரம் ௧௪௨௫'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated ௧௫ முஹர்ரம் ௧௪௨௫'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before ௧௫ முஹர்ரம் ௧௪௨௫'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after ௧௫ முஹர்ரம் ௧௪௨௫'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '௧௫ முஹர்ரம் ௧௪௨௫ இலிருந்து'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to ௧௫ முஹர்ரம் ௧௪௨௫'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between ௧௫ முஹர்ரம் ௧௪௨௫ and ௧௫ சஃபர் ௧௪௨௫'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from ௧௫ முஹர்ரம் ௧௪௨௫ to ௧௫ சஃபர் ௧௪௨௫'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted ௧௫ முஹர்ரம் ௧௪௨௫'],
            ['@#DJALALI@ 15 FARVA 1384', '௧௫ ஃபர்வர்தின் ௧௩௮௪'],
            ['@#DJALALI@ FARVA 1384', 'ஃபர்வர்தின் ௧௩௮௪'],
            ['ABT @#DJALALI@ FARVA 1384', 'about ஃபர்வர்தின் ௧௩௮௪'],
            ['FROM @#DJALALI@ FARVA 1384', 'ஃபர்வர்தின் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ FARVA 1384', 'after ஃபர்வர்தின் ௧௩௮௪'],
            ['BEF @#DJALALI@ FARVA 1384', 'before ஃபர்வர்தின் ௧௩௮௪'],
            ['@#DJALALI@ 15 ORDIB 1384', '௧௫ ஆர்திபஹெஷ்த் ௧௩௮௪'],
            ['@#DJALALI@ ORDIB 1384', 'ஆர்திபஹெஷ்த் ௧௩௮௪'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about ஆர்திபஹெஷ்த் ௧௩௮௪'],
            ['FROM @#DJALALI@ ORDIB 1384', 'ஆர்திபஹெஷ்த் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after ஆர்திபஹெஷ்த் ௧௩௮௪'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before ஆர்திபஹெஷ்த் ௧௩௮௪'],
            ['@#DJALALI@ 15 KHORD 1384', '௧௫ கோர்தாத் ௧௩௮௪'],
            ['@#DJALALI@ KHORD 1384', 'கோர்தாத் ௧௩௮௪'],
            ['ABT @#DJALALI@ KHORD 1384', 'about கோர்தாத் ௧௩௮௪'],
            ['FROM @#DJALALI@ KHORD 1384', 'கோர்தாத் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ KHORD 1384', 'after கோர்தாத் ௧௩௮௪'],
            ['BEF @#DJALALI@ KHORD 1384', 'before கோர்தாத் ௧௩௮௪'],
            ['@#DJALALI@ 15 TIR 1384', '௧௫ தீர் ௧௩௮௪'],
            ['@#DJALALI@ TIR 1384', 'தீர் ௧௩௮௪'],
            ['ABT @#DJALALI@ TIR 1384', 'about தீர் ௧௩௮௪'],
            ['FROM @#DJALALI@ TIR 1384', 'தீர் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ TIR 1384', 'after தீர் ௧௩௮௪'],
            ['BEF @#DJALALI@ TIR 1384', 'before தீர் ௧௩௮௪'],
            ['@#DJALALI@ 15 MORDA 1384', '௧௫ மோர்தாத் ௧௩௮௪'],
            ['@#DJALALI@ MORDA 1384', 'மோர்தாத் ௧௩௮௪'],
            ['ABT @#DJALALI@ MORDA 1384', 'about மோர்தாத் ௧௩௮௪'],
            ['FROM @#DJALALI@ MORDA 1384', 'மோர்தாத் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ MORDA 1384', 'after மோர்தாத் ௧௩௮௪'],
            ['BEF @#DJALALI@ MORDA 1384', 'before மோர்தாத் ௧௩௮௪'],
            ['@#DJALALI@ 15 SHAHR 1384', '௧௫ ஷஹ்ரிவர் ௧௩௮௪'],
            ['@#DJALALI@ SHAHR 1384', 'ஷஹ்ரிவர் ௧௩௮௪'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about ஷஹ்ரிவர் ௧௩௮௪'],
            ['FROM @#DJALALI@ SHAHR 1384', 'ஷஹ்ரிவர் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after ஷஹ்ரிவர் ௧௩௮௪'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before ஷஹ்ரிவர் ௧௩௮௪'],
            ['@#DJALALI@ 15 MEHR 1384', '௧௫ மெஹ்ர் ௧௩௮௪'],
            ['@#DJALALI@ MEHR 1384', 'மெஹ்ர் ௧௩௮௪'],
            ['ABT @#DJALALI@ MEHR 1384', 'about மெஹ்ர் ௧௩௮௪'],
            ['FROM @#DJALALI@ MEHR 1384', 'மெஹ்ர் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ MEHR 1384', 'after மெஹ்ர் ௧௩௮௪'],
            ['BEF @#DJALALI@ MEHR 1384', 'before மெஹ்ர் ௧௩௮௪'],
            ['@#DJALALI@ 15 ABAN 1384', '௧௫ அபான் ௧௩௮௪'],
            ['@#DJALALI@ ABAN 1384', 'அபான் ௧௩௮௪'],
            ['ABT @#DJALALI@ ABAN 1384', 'about அபான் ௧௩௮௪'],
            ['FROM @#DJALALI@ ABAN 1384', 'அபான் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ ABAN 1384', 'after அபான் ௧௩௮௪'],
            ['BEF @#DJALALI@ ABAN 1384', 'before அபான் ௧௩௮௪'],
            ['@#DJALALI@ 15 AZAR 1384', '௧௫ ஆசர் ௧௩௮௪'],
            ['@#DJALALI@ AZAR 1384', 'ஆசர் ௧௩௮௪'],
            ['ABT @#DJALALI@ AZAR 1384', 'about ஆசர் ௧௩௮௪'],
            ['FROM @#DJALALI@ AZAR 1384', 'ஆசர் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ AZAR 1384', 'after ஆசர் ௧௩௮௪'],
            ['BEF @#DJALALI@ AZAR 1384', 'before ஆசர் ௧௩௮௪'],
            ['@#DJALALI@ 15 DEY 1384', '௧௫ தே ௧௩௮௪'],
            ['@#DJALALI@ DEY 1384', 'தே ௧௩௮௪'],
            ['ABT @#DJALALI@ DEY 1384', 'about தே ௧௩௮௪'],
            ['FROM @#DJALALI@ DEY 1384', 'தே ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ DEY 1384', 'after தே ௧௩௮௪'],
            ['BEF @#DJALALI@ DEY 1384', 'before தே ௧௩௮௪'],
            ['@#DJALALI@ 15 BAHMA 1384', '௧௫ பஹ்மன் ௧௩௮௪'],
            ['@#DJALALI@ BAHMA 1384', 'பஹ்மன் ௧௩௮௪'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about பஹ்மன் ௧௩௮௪'],
            ['FROM @#DJALALI@ BAHMA 1384', 'பஹ்மன் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after பஹ்மன் ௧௩௮௪'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before பஹ்மன் ௧௩௮௪'],
            ['@#DJALALI@ 15 ESFAN 1384', '௧௫ எஸ்ஃபண்ட் ௧௩௮௪'],
            ['@#DJALALI@ ESFAN 1384', 'எஸ்ஃபண்ட் ௧௩௮௪'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about எஸ்ஃபண்ட் ௧௩௮௪'],
            ['FROM @#DJALALI@ ESFAN 1384', 'எஸ்ஃபண்ட் ௧௩௮௪ இலிருந்து'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after எஸ்ஃபண்ட் ௧௩௮௪'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before எஸ்ஃபண்ட் ௧௩௮௪'],
            ['@#DJALALI@ 1384', '௧௩௮௪'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about ௧௫ ஃபர்வர்தின் ௧௩௮௪'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated ௧௫ ஃபர்வர்தின் ௧௩௮௪'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated ௧௫ ஃபர்வர்தின் ௧௩௮௪'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before ௧௫ ஃபர்வர்தின் ௧௩௮௪'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after ௧௫ ஃபர்வர்தின் ௧௩௮௪'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '௧௫ ஃபர்வர்தின் ௧௩௮௪ இலிருந்து'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to ௧௫ ஃபர்வர்தின் ௧௩௮௪'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between ௧௫ ஃபர்வர்தின் ௧௩௮௪ and ௧௫ ஆர்திபஹெஷ்த் ௧௩௮௪'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from ௧௫ ஃபர்வர்தின் ௧௩௮௪ to ௧௫ ஆர்திபஹெஷ்த் ௧௩௮௪'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted ௧௫ ஃபர்வர்தின் ௧௩௮௪'],
        ];
    }
}
