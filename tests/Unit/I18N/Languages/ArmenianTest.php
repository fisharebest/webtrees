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
use Fisharebest\Webtrees\I18N\Languages\Armenian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Armenian::class)]
class ArmenianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Armenian();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Armn, self::language()->script());
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
        self::assertSame('hy', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('հայերեն', self::language()->endonym());
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




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 հունվար 2000'],
            ['JAN 2000', 'հունվար 2000'],
            ['ABT JAN 2000', 'մոտ հունվար 2000'],
            ['FROM JAN 2000', 'հունվար 2000 - ից'],
            ['AFT JAN 2000', 'հունվար 2000 - ից հետո'],
            ['BEF JAN 2000', 'հունվար 2000 - ից առաջ'],
            ['15 FEB 2000', '15 փետրվար 2000'],
            ['FEB 2000', 'փետրվար 2000'],
            ['ABT FEB 2000', 'մոտ փետրվար 2000'],
            ['FROM FEB 2000', 'փետրվար 2000 - ից'],
            ['AFT FEB 2000', 'փետրվար 2000 - ից հետո'],
            ['BEF FEB 2000', 'փետրվար 2000 - ից առաջ'],
            ['15 MAR 2000', '15 մարտ 2000'],
            ['MAR 2000', 'մարտ 2000'],
            ['ABT MAR 2000', 'մոտ մարտ 2000'],
            ['FROM MAR 2000', 'մարտ 2000 - ից'],
            ['AFT MAR 2000', 'մարտ 2000 - ից հետո'],
            ['BEF MAR 2000', 'մարտ 2000 - ից առաջ'],
            ['15 APR 2000', '15 ապրիլ 2000'],
            ['APR 2000', 'ապրիլ 2000'],
            ['ABT APR 2000', 'մոտ ապրիլ 2000'],
            ['FROM APR 2000', 'ապրիլ 2000 - ից'],
            ['AFT APR 2000', 'ապրիլ 2000 - ից հետո'],
            ['BEF APR 2000', 'ապրիլ 2000 - ից առաջ'],
            ['15 MAY 2000', '15 մայիս 2000'],
            ['MAY 2000', 'մայիս 2000'],
            ['ABT MAY 2000', 'մոտ մայիս 2000'],
            ['FROM MAY 2000', 'մայիս 2000 - ից'],
            ['AFT MAY 2000', 'մայիս 2000 - ից հետո'],
            ['BEF MAY 2000', 'մայիս 2000 - ից առաջ'],
            ['15 JUN 2000', '15 հունիս 2000'],
            ['JUN 2000', 'հունիս 2000'],
            ['ABT JUN 2000', 'մոտ հունիս 2000'],
            ['FROM JUN 2000', 'հունիս 2000 - ից'],
            ['AFT JUN 2000', 'հունիս 2000 - ից հետո'],
            ['BEF JUN 2000', 'հունիս 2000 - ից առաջ'],
            ['15 JUL 2000', '15 հուլիս 2000'],
            ['JUL 2000', 'հուլիս 2000'],
            ['ABT JUL 2000', 'մոտ հուլիս 2000'],
            ['FROM JUL 2000', 'հուլիս 2000 - ից'],
            ['AFT JUL 2000', 'հուլիս 2000 - ից հետո'],
            ['BEF JUL 2000', 'հուլիս 2000 - ից առաջ'],
            ['15 AUG 2000', '15 օգոստոս 2000'],
            ['AUG 2000', 'օգոստոս 2000'],
            ['ABT AUG 2000', 'մոտ օգոստոս 2000'],
            ['FROM AUG 2000', 'օգոստոս 2000 - ից'],
            ['AFT AUG 2000', 'օգոստոս 2000 - ից հետո'],
            ['BEF AUG 2000', 'օգոստոս 2000 - ից առաջ'],
            ['15 SEP 2000', '15 սեպտեմբեր 2000'],
            ['SEP 2000', 'սեպտեմբեր 2000'],
            ['ABT SEP 2000', 'մոտ սեպտեմբեր 2000'],
            ['FROM SEP 2000', 'սեպտեմբեր 2000 - ից'],
            ['AFT SEP 2000', 'սեպտեմբեր 2000 - ից հետո'],
            ['BEF SEP 2000', 'սեպտեմբեր 2000 - ից առաջ'],
            ['15 OCT 2000', '15 հոկտեմբեր 2000'],
            ['OCT 2000', 'հոկտեմբեր 2000'],
            ['ABT OCT 2000', 'մոտ հոկտեմբեր 2000'],
            ['FROM OCT 2000', 'հոկտեմբեր 2000 - ից'],
            ['AFT OCT 2000', 'հոկտեմբեր 2000 - ից հետո'],
            ['BEF OCT 2000', 'հոկտեմբեր 2000 - ից առաջ'],
            ['15 NOV 2000', '15 նոյեմբեր 2000'],
            ['NOV 2000', 'նոյեմբեր 2000'],
            ['ABT NOV 2000', 'մոտ նոյեմբեր 2000'],
            ['FROM NOV 2000', 'նոյեմբեր 2000 - ից'],
            ['AFT NOV 2000', 'նոյեմբեր 2000 - ից հետո'],
            ['BEF NOV 2000', 'նոյեմբեր 2000 - ից առաջ'],
            ['15 DEC 2000', '15 դեկտեմբեր 2000'],
            ['DEC 2000', 'դեկտեմբեր 2000'],
            ['ABT DEC 2000', 'մոտ դեկտեմբեր 2000'],
            ['FROM DEC 2000', 'դեկտեմբեր 2000 - ից'],
            ['AFT DEC 2000', 'դեկտեմբեր 2000 - ից հետո'],
            ['BEF DEC 2000', 'դեկտեմբեր 2000 - ից առաջ'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'մոտ 15 հունվար 2000'],
            ['CAL 15 JAN 2000', 'հաշվարկված 15 հունվար 2000'],
            ['EST 15 JAN 2000', 'գնահատված 15 հունվար 2000'],
            ['BEF 15 JAN 2000', '15 հունվար 2000 - ից առաջ'],
            ['AFT 15 JAN 2000', '15 հունվար 2000 - ից հետո'],
            ['FROM 15 JAN 2000', '15 հունվար 2000 - ից'],
            ['TO 15 JAN 2000', 'դեպի 15 հունվար 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '15 հունվար 2000 - ի և 15 փետրվար 2000 -ի միջև'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '15 հունվար 2000 - ից մինչև 15 փետրվար 2000'],
            ['INT 15 JAN 2000', 'մեկնաբանված 15 հունվար 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 հունվար 1700 ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'հունվար 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'մոտ հունվար 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'հունվար 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ JAN 1700', 'հունվար 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ JAN 1700', 'հունվար 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 փետրվար 1700 ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'փետրվար 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'մոտ փետրվար 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'փետրվար 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ FEB 1700', 'փետրվար 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ FEB 1700', 'փետրվար 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 մարտ 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'մարտ 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'մոտ մարտ 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'մարտ 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ MAR 1700', 'մարտ 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ MAR 1700', 'մարտ 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 APR 1700', '15 ապրիլ 1700 ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 ապրիլ 1645/46 ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'ապրիլ 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'մոտ ապրիլ 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'ապրիլ 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ APR 1700', 'ապրիլ 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ APR 1700', 'ապրիլ 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 մայիս 1700 ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'մայիս 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'մոտ մայիս 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'մայիս 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ MAY 1700', 'մայիս 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ MAY 1700', 'մայիս 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 հունիս 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'հունիս 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'մոտ հունիս 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'հունիս 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ JUN 1700', 'հունիս 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ JUN 1700', 'հունիս 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 հուլիս 1700 ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'հուլիս 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'մոտ հուլիս 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'հուլիս 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ JUL 1700', 'հուլիս 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ JUL 1700', 'հուլիս 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 օգոստոս 1700 ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'օգոստոս 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'մոտ օգոստոս 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'օգոստոս 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ AUG 1700', 'օգոստոս 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ AUG 1700', 'օգոստոս 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 սեպտեմբեր 1700 ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'սեպտեմբեր 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'մոտ սեպտեմբեր 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'սեպտեմբեր 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ SEP 1700', 'սեպտեմբեր 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ SEP 1700', 'սեպտեմբեր 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 հոկտեմբեր 1700 ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'հոկտեմբեր 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'մոտ հոկտեմբեր 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'հոկտեմբեր 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ OCT 1700', 'հոկտեմբեր 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ OCT 1700', 'հոկտեմբեր 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 նոյեմբեր 1700 ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'նոյեմբեր 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'մոտ նոյեմբեր 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'նոյեմբեր 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ NOV 1700', 'նոյեմբեր 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ NOV 1700', 'նոյեմբեր 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 դեկտեմբեր 1700 ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'դեկտեմբեր 1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'մոտ դեկտեմբեր 1700 ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'դեկտեմբեր 1700 ᴄᴇ - ից'],
            ['AFT @#DJULIAN@ DEC 1700', 'դեկտեմբեր 1700 ᴄᴇ - ից հետո'],
            ['BEF @#DJULIAN@ DEC 1700', 'դեկտեմբեր 1700 ᴄᴇ - ից առաջ'],
            ['@#DJULIAN@ 1700', '1700 ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'մոտ 15 հունվար 1700 ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'հաշվարկված 15 հունվար 1700 ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'գնահատված 15 հունվար 1700 ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', '15 հունվար 1700 ᴄᴇ - ից առաջ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '15 հունվար 1700 ᴄᴇ - ից հետո'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '15 հունվար 1700 ᴄᴇ - ից'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'դեպի 15 հունվար 1700 ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '15 հունվար 1700 ᴄᴇ - ի և 15 փետրվար 1700 ᴄᴇ -ի միջև'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '15 հունվար 1700 ᴄᴇ - ից մինչև 15 փետրվար 1700 ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'մեկնաբանված 15 հունվար 1700 ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Տիշրեյ 5765'],
            ['@#DHEBREW@ TSH 5765', 'Տիշրեյ 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'մոտ Տիշրեյ 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'Տիշրեյ 5765 - ից'],
            ['AFT @#DHEBREW@ TSH 5765', 'Տիշրեյ 5765 - ից հետո'],
            ['BEF @#DHEBREW@ TSH 5765', 'Տիշրեյ 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Հեշվան 5765'],
            ['@#DHEBREW@ CSH 5765', 'Հեշվան 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'մոտ Հեշվան 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'Հեշվան 5765 - ից'],
            ['AFT @#DHEBREW@ CSH 5765', 'Հեշվան 5765 - ից հետո'],
            ['BEF @#DHEBREW@ CSH 5765', 'Հեշվան 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Կիսլև 5765'],
            ['@#DHEBREW@ KSL 5765', 'Կիսլև 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'մոտ Կիսլև 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'Կիսլև 5765 - ից'],
            ['AFT @#DHEBREW@ KSL 5765', 'Կիսլև 5765 - ից հետո'],
            ['BEF @#DHEBREW@ KSL 5765', 'Կիսլև 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Տեւետ 5765'],
            ['@#DHEBREW@ TVT 5765', 'Տեւետ 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'մոտ Տեւետ 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'Տեւետ 5765 - ից'],
            ['AFT @#DHEBREW@ TVT 5765', 'Տեւետ 5765 - ից հետո'],
            ['BEF @#DHEBREW@ TVT 5765', 'Տեւետ 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Շևաթ 5765'],
            ['@#DHEBREW@ SHV 5765', 'Շևաթ 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'մոտ Շևաթ 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'Շևաթ 5765 - ից'],
            ['AFT @#DHEBREW@ SHV 5765', 'Շևաթ 5765 - ից հետո'],
            ['BEF @#DHEBREW@ SHV 5765', 'Շևաթ 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Ադար Ա 5765'],
            ['@#DHEBREW@ ADR 5765', 'Ադար Ա 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'մոտ Ադար Ա 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'Ադար Ա 5765 - ից'],
            ['AFT @#DHEBREW@ ADR 5765', 'Ադար Ա 5765 - ից հետո'],
            ['BEF @#DHEBREW@ ADR 5765', 'Ադար Ա 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Ադար Բ 5765'],
            ['@#DHEBREW@ ADS 5765', 'Ադար Բ 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'մոտ Ադար Բ 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'Ադար Բ 5765 - ից'],
            ['AFT @#DHEBREW@ ADS 5765', 'Ադար Բ 5765 - ից հետո'],
            ['BEF @#DHEBREW@ ADS 5765', 'Ադար Բ 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Նիսան 5765'],
            ['@#DHEBREW@ NSN 5765', 'Նիսան 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'մոտ Նիսան 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'Նիսան 5765 - ից'],
            ['AFT @#DHEBREW@ NSN 5765', 'Նիսան 5765 - ից հետո'],
            ['BEF @#DHEBREW@ NSN 5765', 'Նիսան 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Իյար 5765'],
            ['@#DHEBREW@ IYR 5765', 'Իյար 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'մոտ Իյար 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'Իյար 5765 - ից'],
            ['AFT @#DHEBREW@ IYR 5765', 'Իյար 5765 - ից հետո'],
            ['BEF @#DHEBREW@ IYR 5765', 'Իյար 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Սիվան 5765'],
            ['@#DHEBREW@ SVN 5765', 'Սիվան 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'մոտ Սիվան 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'Սիվան 5765 - ից'],
            ['AFT @#DHEBREW@ SVN 5765', 'Սիվան 5765 - ից հետո'],
            ['BEF @#DHEBREW@ SVN 5765', 'Սիվան 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Թամուզ 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Թամուզ 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'մոտ Թամուզ 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'Թամուզ 5765 - ից'],
            ['AFT @#DHEBREW@ TMZ 5765', 'Թամուզ 5765 - ից հետո'],
            ['BEF @#DHEBREW@ TMZ 5765', 'Թամուզ 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Ավ 5765'],
            ['@#DHEBREW@ AAV 5765', 'Ավ 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'մոտ Ավ 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'Ավ 5765 - ից'],
            ['AFT @#DHEBREW@ AAV 5765', 'Ավ 5765 - ից հետո'],
            ['BEF @#DHEBREW@ AAV 5765', 'Ավ 5765 - ից առաջ'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Էլուլ 5765'],
            ['@#DHEBREW@ ELL 5765', 'Էլուլ 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'մոտ Էլուլ 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'Էլուլ 5765 - ից'],
            ['AFT @#DHEBREW@ ELL 5765', 'Էլուլ 5765 - ից հետո'],
            ['BEF @#DHEBREW@ ELL 5765', 'Էլուլ 5765 - ից առաջ'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'մոտ 15 Տիշրեյ 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'հաշվարկված 15 Տիշրեյ 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'գնահատված 15 Տիշրեյ 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '15 Տիշրեյ 5765 - ից առաջ'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '15 Տիշրեյ 5765 - ից հետո'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '15 Տիշրեյ 5765 - ից'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'դեպի 15 Տիշրեյ 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '15 Տիշրեյ 5765 - ի և 15 Հեշվան 5765 -ի միջև'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '15 Տիշրեյ 5765 - ից մինչև 15 Հեշվան 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'մեկնաբանված 15 Տիշրեյ 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Վանդեմիաիր An XII'],
            ['@#DFRENCH R@ VEND 12', 'Վանդեմիաիր An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'մոտ Վանդեմիաիր An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'Վանդեմիաիր An XII - ից'],
            ['AFT @#DFRENCH R@ VEND 12', 'Վանդեմիաիր An XII - ից հետո'],
            ['BEF @#DFRENCH R@ VEND 12', 'Վանդեմիաիր An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Բրումեյր An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Բրումեյր An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'մոտ Բրումեյր An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'Բրումեյր An XII - ից'],
            ['AFT @#DFRENCH R@ BRUM 12', 'Բրումեյր An XII - ից հետո'],
            ['BEF @#DFRENCH R@ BRUM 12', 'Բրումեյր An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Ֆրիմեր An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Ֆրիմեր An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'մոտ Ֆրիմեր An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'Ֆրիմեր An XII - ից'],
            ['AFT @#DFRENCH R@ FRIM 12', 'Ֆրիմեր An XII - ից հետո'],
            ['BEF @#DFRENCH R@ FRIM 12', 'Ֆրիմեր An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Նիվոզե An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Նիվոզե An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'մոտ Նիվոզե An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'Նիվոզե An XII - ից'],
            ['AFT @#DFRENCH R@ NIVO 12', 'Նիվոզե An XII - ից հետո'],
            ['BEF @#DFRENCH R@ NIVO 12', 'Նիվոզե An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Պլյուվիոզ An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Պլյուվիոզ An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'մոտ Պլյուվիոզ An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'Պլյուվիոզ An XII - ից'],
            ['AFT @#DFRENCH R@ PLUV 12', 'Պլյուվիոզ An XII - ից հետո'],
            ['BEF @#DFRENCH R@ PLUV 12', 'Պլյուվիոզ An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Վենտոզե An XII'],
            ['@#DFRENCH R@ VENT 12', 'Վենտոզե An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'մոտ Վենտոզե An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'Վենտոզե An XII - ից'],
            ['AFT @#DFRENCH R@ VENT 12', 'Վենտոզե An XII - ից հետո'],
            ['BEF @#DFRENCH R@ VENT 12', 'Վենտոզե An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Ջերմինալ An XII'],
            ['@#DFRENCH R@ GERM 12', 'Ջերմինալ An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'մոտ Ջերմինալ An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'Ջերմինալ An XII - ից'],
            ['AFT @#DFRENCH R@ GERM 12', 'Ջերմինալ An XII - ից հետո'],
            ['BEF @#DFRENCH R@ GERM 12', 'Ջերմինալ An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Ֆլորեալ An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Ֆլորեալ An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'մոտ Ֆլորեալ An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'Ֆլորեալ An XII - ից'],
            ['AFT @#DFRENCH R@ FLOR 12', 'Ֆլորեալ An XII - ից հետո'],
            ['BEF @#DFRENCH R@ FLOR 12', 'Ֆլորեալ An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Պրայրիալ An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Պրայրիալ An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'մոտ Պրայրիալ An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'Պրայրիալ An XII - ից'],
            ['AFT @#DFRENCH R@ PRAI 12', 'Պրայրիալ An XII - ից հետո'],
            ['BEF @#DFRENCH R@ PRAI 12', 'Պրայրիալ An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Մեսիդոր An XII'],
            ['@#DFRENCH R@ MESS 12', 'Մեսիդոր An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'մոտ Մեսիդոր An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'Մեսիդոր An XII - ից'],
            ['AFT @#DFRENCH R@ MESS 12', 'Մեսիդոր An XII - ից հետո'],
            ['BEF @#DFRENCH R@ MESS 12', 'Մեսիդոր An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 THER 12', '15 Թերմիդոր An XII'],
            ['@#DFRENCH R@ THER 12', 'Թերմիդոր An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'մոտ Թերմիդոր An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'Թերմիդոր An XII - ից'],
            ['AFT @#DFRENCH R@ THER 12', 'Թերմիդոր An XII - ից հետո'],
            ['BEF @#DFRENCH R@ THER 12', 'Թերմիդոր An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Ֆրուկտիդոր An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Ֆրուկտիդոր An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'մոտ Ֆրուկտիդոր An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'Ֆրուկտիդոր An XII - ից'],
            ['AFT @#DFRENCH R@ FRUC 12', 'Ֆրուկտիդոր An XII - ից հետո'],
            ['BEF @#DFRENCH R@ FRUC 12', 'Ֆրուկտիդոր An XII - ից առաջ'],
            ['@#DFRENCH R@ 15 COMP 12', '15 լրացնող օրեր An XII'],
            ['@#DFRENCH R@ COMP 12', 'լրացնող օրեր An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'մոտ լրացնող օրեր An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'լրացնող օրեր An XII - ից'],
            ['AFT @#DFRENCH R@ COMP 12', 'լրացնող օրեր An XII - ից հետո'],
            ['BEF @#DFRENCH R@ COMP 12', 'լրացնող օրեր An XII - ից առաջ'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'մոտ 15 Վանդեմիաիր An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'հաշվարկված 15 Վանդեմիաիր An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'գնահատված 15 Վանդեմիաիր An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', '15 Վանդեմիաիր An XII - ից առաջ'],
            ['AFT @#DFRENCH R@ 15 VEND 12', '15 Վանդեմիաիր An XII - ից հետո'],
            ['FROM @#DFRENCH R@ 15 VEND 12', '15 Վանդեմիաիր An XII - ից'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'դեպի 15 Վանդեմիաիր An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', '15 Վանդեմիաիր An XII - ի և 15 Բրումեյր An XII -ի միջև'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', '15 Վանդեմիաիր An XII - ից մինչև 15 Բրումեյր An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'մեկնաբանված 15 Վանդեմիաիր An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Մուհարամ 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Մուհարամ 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'մոտ Մուհարամ 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'Մուհարամ 1425 - ից'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'Մուհարամ 1425 - ից հետո'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'Մուհարամ 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Սաֆար 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Սաֆար 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'մոտ Սաֆար 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'Սաֆար 1425 - ից'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'Սաֆար 1425 - ից հետո'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'Սաֆար 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Ռաբի ալ-Ավվալ 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Ռաբի ալ-Ավվալ 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'մոտ Ռաբի ալ-Ավվալ 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'Ռաբի ալ-Ավվալ 1425 - ից'],
            ['AFT @#DHIJRI@ RABIA 1425', 'Ռաբի ալ-Ավվալ 1425 - ից հետո'],
            ['BEF @#DHIJRI@ RABIA 1425', 'Ռաբի ալ-Ավվալ 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Ռաբի աս-Սանի 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Ռաբի աս-Սանի 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'մոտ Ռաբի աս-Սանի 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'Ռաբի աս-Սանի 1425 - ից'],
            ['AFT @#DHIJRI@ RABIT 1425', 'Ռաբի աս-Սանի 1425 - ից հետո'],
            ['BEF @#DHIJRI@ RABIT 1425', 'Ռաբի աս-Սանի 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Ջումադա ալ-Ավվալ 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Ջումադա ալ-Ավվալ 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'մոտ Ջումադա ալ-Ավվալ 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'Ջումադա ալ-Ավվալ 1425 - ից'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'Ջումադա ալ-Ավվալ 1425 - ից հետո'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'Ջումադա ալ-Ավվալ 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Ջումադա աս-Սանի 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Ջումադա աս-Սանի 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'մոտ Ջումադա աս-Սանի 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'Ջումադա աս-Սանի 1425 - ից'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'Ջումադա աս-Սանի 1425 - ից հետո'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'Ջումադա աս-Սանի 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Ռաջաբ 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Ռաջաբ 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'մոտ Ռաջաբ 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'Ռաջաբ 1425 - ից'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'Ռաջաբ 1425 - ից հետո'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'Ռաջաբ 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Շաաբան 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Շաաբան 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'մոտ Շաաբան 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'Շաաբան 1425 - ից'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'Շաաբան 1425 - ից հետո'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'Շաաբան 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ռամադան 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ռամադան 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'մոտ Ռամադան 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'Ռամադան 1425 - ից'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'Ռամադան 1425 - ից հետո'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'Ռամադան 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Շավվալ 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Շավվալ 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'մոտ Շավվալ 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'Շավվալ 1425 - ից'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'Շավվալ 1425 - ից հետո'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'Շավվալ 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Զուլ-Քաադա 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Զուլ-Քաադա 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'մոտ Զուլ-Քաադա 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'Զուլ-Քաադա 1425 - ից'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'Զուլ-Քաադա 1425 - ից հետո'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'Զուլ-Քաադա 1425 - ից առաջ'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'մոտ 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', '1425 - ից'],
            ['AFT @#DHIJRI@ DHUAL 1425', '1425 - ից հետո'],
            ['BEF @#DHIJRI@ DHUAL 1425', '1425 - ից առաջ'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'մոտ 15 Մուհարամ 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'հաշվարկված 15 Մուհարամ 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'գնահատված 15 Մուհարամ 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '15 Մուհարամ 1425 - ից առաջ'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '15 Մուհարամ 1425 - ից հետո'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '15 Մուհարամ 1425 - ից'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'դեպի 15 Մուհարամ 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '15 Մուհարամ 1425 - ի և 15 Սաֆար 1425 -ի միջև'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '15 Մուհարամ 1425 - ից մինչև 15 Սաֆար 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'մեկնաբանված 15 Մուհարամ 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Ֆարվարդին 1384'],
            ['@#DJALALI@ FARVA 1384', 'Ֆարվարդին 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'մոտ Ֆարվարդին 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'Ֆարվարդին 1384 - ից'],
            ['AFT @#DJALALI@ FARVA 1384', 'Ֆարվարդին 1384 - ից հետո'],
            ['BEF @#DJALALI@ FARVA 1384', 'Ֆարվարդին 1384 - ից առաջ'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Օրդիբեհեշթ 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Օրդիբեհեշթ 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'մոտ Օրդիբեհեշթ 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'Օրդիբեհեշթ 1384 - ից'],
            ['AFT @#DJALALI@ ORDIB 1384', 'Օրդիբեհեշթ 1384 - ից հետո'],
            ['BEF @#DJALALI@ ORDIB 1384', 'Օրդիբեհեշթ 1384 - ից առաջ'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Խորդադ 1384'],
            ['@#DJALALI@ KHORD 1384', 'Խորդադ 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'մոտ Խորդադ 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'Խորդադ 1384 - ից'],
            ['AFT @#DJALALI@ KHORD 1384', 'Խորդադ 1384 - ից հետո'],
            ['BEF @#DJALALI@ KHORD 1384', 'Խորդադ 1384 - ից առաջ'],
            ['@#DJALALI@ 15 TIR 1384', '15 Տիր 1384'],
            ['@#DJALALI@ TIR 1384', 'Տիր 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'մոտ Տիր 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'Տիր 1384 - ից'],
            ['AFT @#DJALALI@ TIR 1384', 'Տիր 1384 - ից հետո'],
            ['BEF @#DJALALI@ TIR 1384', 'Տիր 1384 - ից առաջ'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Մորդադ 1384'],
            ['@#DJALALI@ MORDA 1384', 'Մորդադ 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'մոտ Մորդադ 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'Մորդադ 1384 - ից'],
            ['AFT @#DJALALI@ MORDA 1384', 'Մորդադ 1384 - ից հետո'],
            ['BEF @#DJALALI@ MORDA 1384', 'Մորդադ 1384 - ից առաջ'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Շահրիվար 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Շահրիվար 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'մոտ Շահրիվար 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'Շահրիվար 1384 - ից'],
            ['AFT @#DJALALI@ SHAHR 1384', 'Շահրիվար 1384 - ից հետո'],
            ['BEF @#DJALALI@ SHAHR 1384', 'Շահրիվար 1384 - ից առաջ'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Մեհր 1384'],
            ['@#DJALALI@ MEHR 1384', 'Մեհր 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'մոտ Մեհր 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'Մեհր 1384 - ից'],
            ['AFT @#DJALALI@ MEHR 1384', 'Մեհր 1384 - ից հետո'],
            ['BEF @#DJALALI@ MEHR 1384', 'Մեհր 1384 - ից առաջ'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Աբան 1384'],
            ['@#DJALALI@ ABAN 1384', 'Աբան 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'մոտ Աբան 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'Աբան 1384 - ից'],
            ['AFT @#DJALALI@ ABAN 1384', 'Աբան 1384 - ից հետո'],
            ['BEF @#DJALALI@ ABAN 1384', 'Աբան 1384 - ից առաջ'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Ազար 1384'],
            ['@#DJALALI@ AZAR 1384', 'Ազար 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'մոտ Ազար 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'Ազար 1384 - ից'],
            ['AFT @#DJALALI@ AZAR 1384', 'Ազար 1384 - ից հետո'],
            ['BEF @#DJALALI@ AZAR 1384', 'Ազար 1384 - ից առաջ'],
            ['@#DJALALI@ 15 DEY 1384', '15 Դեյ 1384'],
            ['@#DJALALI@ DEY 1384', 'Դեյ 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'մոտ Դեյ 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'Դեյ 1384 - ից'],
            ['AFT @#DJALALI@ DEY 1384', 'Դեյ 1384 - ից հետո'],
            ['BEF @#DJALALI@ DEY 1384', 'Դեյ 1384 - ից առաջ'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Բահման 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Բահման 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'մոտ Բահման 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'Բահման 1384 - ից'],
            ['AFT @#DJALALI@ BAHMA 1384', 'Բահման 1384 - ից հետո'],
            ['BEF @#DJALALI@ BAHMA 1384', 'Բահման 1384 - ից առաջ'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Էսֆանդ 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Էսֆանդ 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'մոտ Էսֆանդ 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'Էսֆանդ 1384 - ից'],
            ['AFT @#DJALALI@ ESFAN 1384', 'Էսֆանդ 1384 - ից հետո'],
            ['BEF @#DJALALI@ ESFAN 1384', 'Էսֆանդ 1384 - ից առաջ'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'մոտ 15 Ֆարվարդին 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'հաշվարկված 15 Ֆարվարդին 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'գնահատված 15 Ֆարվարդին 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '15 Ֆարվարդին 1384 - ից առաջ'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '15 Ֆարվարդին 1384 - ից հետո'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '15 Ֆարվարդին 1384 - ից'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'դեպի 15 Ֆարվարդին 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '15 Ֆարվարդին 1384 - ի և 15 Օրդիբեհեշթ 1384 -ի միջև'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '15 Ֆարվարդին 1384 - ից մինչև 15 Օրդիբեհեշթ 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'մեկնաբանված 15 Ֆարվարդին 1384'],
        ];
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
        self::assertSame('one և two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two և three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one կամ two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two կամ three', $language->formatListOr(['one', 'two', 'three']));
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
        $fosterDaughter = self::female('fsd', "1 FAMC @fd@\n2 PEDI foster");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");

        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMS @fbro@\n1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        $cousinF = self::female('cf', "1 FAMC @fbro@");
        $cousinM = self::male('cm', "1 FAMC @fbro@");

        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");

        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fsd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinF, $cousinM,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('կին', 'ամուսին', [$husband, $fm, $wife]);
        self::assertRelationshipNames('նախկին ամուսին', 'նախկին կին', [$wife, $fd, $exHusband]);

        // Parents & children
        self::assertRelationshipNames('մայր', 'որդի', [$son, $fm, $wife]);
        self::assertRelationshipNames('հայր', 'որդի', [$son, $fm, $husband]);
        self::assertRelationshipNames('մայր', 'դուստր', [$daughter, $fm, $wife]);

        // Adopted & fostered
        self::assertRelationshipNames('խորթ մայր', 'որդեգրուհի որդի', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('խնամայր', 'խնամի դուստր', [$fosterDaughter, $fd, $wife]);

        // Stepfamily
        self::assertRelationshipName('խորթ հայր', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('խորթ դուստր', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws (spouse's parents)
        self::assertRelationshipName('զոքանչ', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('աներ', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('սկեսուր', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('սկեսրայր', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws (child's spouse)
        self::assertRelationshipName('հարս', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('փեսա', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws (spouse's siblings)
        self::assertRelationshipName('տագեր', [$wife, $fm, $husband, $fp, $brotherOfH]);
        self::assertRelationshipName('տակերոջ', [$wife, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('աներոջ', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('քենի', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents & grandchildren
        self::assertRelationshipNames('տատիկ', 'թոռ', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('պապիկ', 'թոռ', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Aunts & uncles
        self::assertRelationshipName('հորաքույր', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('մորաքույր', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('հորեղբայր', [$son, $fm, $husband, $fp, $brotherOfH]);
        self::assertRelationshipName('քեռի', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces & nephews
        self::assertRelationshipName('եղբոր դուստր', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('եղբոր որդի', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        self::assertRelationshipName('քուրոջ դուստր', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('քուրոջ որդի', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins
        self::assertRelationshipName('զարմիկ քույր', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinF]);
        self::assertRelationshipName('զարմիկ եղբայր', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinM]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('մեծ տատիկ', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('մեծ պապիկ', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('մեծ հորաքույր/մորաքույր', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('մեծ հորեղբայր/քեռի', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
