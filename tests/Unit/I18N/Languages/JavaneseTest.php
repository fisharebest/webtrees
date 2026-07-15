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
use Fisharebest\Webtrees\I18N\Languages\Javanese;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Javanese::class)]
class JavaneseTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Javanese();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Java, self::language()->script());
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
        self::assertSame('jv', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Jawa', self::language()->endonym());
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
        self::assertSame('-꧑꧒꧓,꧔꧕꧖.꧐꧗꧘꧙', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('-꧑꧒꧓.꧔꧕꧖,꧐꧗꧘꧙', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-꧑꧒꧓.꧔꧕꧖,꧐꧗꧘꧙%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '꧑꧕ Januari ꧒꧐꧐꧐'],
            ['JAN 2000', 'Januari ꧒꧐꧐꧐'],
            ['ABT JAN 2000', 'about Januari ꧒꧐꧐꧐'],
            ['FROM JAN 2000', 'from Januari ꧒꧐꧐꧐'],
            ['AFT JAN 2000', 'after Januari ꧒꧐꧐꧐'],
            ['BEF JAN 2000', 'before Januari ꧒꧐꧐꧐'],
            ['15 FEB 2000', '꧑꧕ Februari ꧒꧐꧐꧐'],
            ['FEB 2000', 'Februari ꧒꧐꧐꧐'],
            ['ABT FEB 2000', 'about Februari ꧒꧐꧐꧐'],
            ['FROM FEB 2000', 'from Februari ꧒꧐꧐꧐'],
            ['AFT FEB 2000', 'after Februari ꧒꧐꧐꧐'],
            ['BEF FEB 2000', 'before Februari ꧒꧐꧐꧐'],
            ['15 MAR 2000', '꧑꧕ Maret ꧒꧐꧐꧐'],
            ['MAR 2000', 'Maret ꧒꧐꧐꧐'],
            ['ABT MAR 2000', 'about Maret ꧒꧐꧐꧐'],
            ['FROM MAR 2000', 'from Maret ꧒꧐꧐꧐'],
            ['AFT MAR 2000', 'after Maret ꧒꧐꧐꧐'],
            ['BEF MAR 2000', 'before Maret ꧒꧐꧐꧐'],
            ['15 APR 2000', '꧑꧕ April ꧒꧐꧐꧐'],
            ['APR 2000', 'April ꧒꧐꧐꧐'],
            ['ABT APR 2000', 'about April ꧒꧐꧐꧐'],
            ['FROM APR 2000', 'from April ꧒꧐꧐꧐'],
            ['AFT APR 2000', 'after April ꧒꧐꧐꧐'],
            ['BEF APR 2000', 'before April ꧒꧐꧐꧐'],
            ['15 MAY 2000', '꧑꧕ Mei ꧒꧐꧐꧐'],
            ['MAY 2000', 'Mei ꧒꧐꧐꧐'],
            ['ABT MAY 2000', 'about Mei ꧒꧐꧐꧐'],
            ['FROM MAY 2000', 'from Mei ꧒꧐꧐꧐'],
            ['AFT MAY 2000', 'after Mei ꧒꧐꧐꧐'],
            ['BEF MAY 2000', 'before Mei ꧒꧐꧐꧐'],
            ['15 JUN 2000', '꧑꧕ Juni ꧒꧐꧐꧐'],
            ['JUN 2000', 'Juni ꧒꧐꧐꧐'],
            ['ABT JUN 2000', 'about Juni ꧒꧐꧐꧐'],
            ['FROM JUN 2000', 'from Juni ꧒꧐꧐꧐'],
            ['AFT JUN 2000', 'after Juni ꧒꧐꧐꧐'],
            ['BEF JUN 2000', 'before Juni ꧒꧐꧐꧐'],
            ['15 JUL 2000', '꧑꧕ Juli ꧒꧐꧐꧐'],
            ['JUL 2000', 'Juli ꧒꧐꧐꧐'],
            ['ABT JUL 2000', 'about Juli ꧒꧐꧐꧐'],
            ['FROM JUL 2000', 'from Juli ꧒꧐꧐꧐'],
            ['AFT JUL 2000', 'after Juli ꧒꧐꧐꧐'],
            ['BEF JUL 2000', 'before Juli ꧒꧐꧐꧐'],
            ['15 AUG 2000', '꧑꧕ Agustus ꧒꧐꧐꧐'],
            ['AUG 2000', 'Agustus ꧒꧐꧐꧐'],
            ['ABT AUG 2000', 'about Agustus ꧒꧐꧐꧐'],
            ['FROM AUG 2000', 'from Agustus ꧒꧐꧐꧐'],
            ['AFT AUG 2000', 'after Agustus ꧒꧐꧐꧐'],
            ['BEF AUG 2000', 'before Agustus ꧒꧐꧐꧐'],
            ['15 SEP 2000', '꧑꧕ September ꧒꧐꧐꧐'],
            ['SEP 2000', 'September ꧒꧐꧐꧐'],
            ['ABT SEP 2000', 'about September ꧒꧐꧐꧐'],
            ['FROM SEP 2000', 'from September ꧒꧐꧐꧐'],
            ['AFT SEP 2000', 'after September ꧒꧐꧐꧐'],
            ['BEF SEP 2000', 'before September ꧒꧐꧐꧐'],
            ['15 OCT 2000', '꧑꧕ Oktober ꧒꧐꧐꧐'],
            ['OCT 2000', 'Oktober ꧒꧐꧐꧐'],
            ['ABT OCT 2000', 'about Oktober ꧒꧐꧐꧐'],
            ['FROM OCT 2000', 'from Oktober ꧒꧐꧐꧐'],
            ['AFT OCT 2000', 'after Oktober ꧒꧐꧐꧐'],
            ['BEF OCT 2000', 'before Oktober ꧒꧐꧐꧐'],
            ['15 NOV 2000', '꧑꧕ November ꧒꧐꧐꧐'],
            ['NOV 2000', 'November ꧒꧐꧐꧐'],
            ['ABT NOV 2000', 'about November ꧒꧐꧐꧐'],
            ['FROM NOV 2000', 'from November ꧒꧐꧐꧐'],
            ['AFT NOV 2000', 'after November ꧒꧐꧐꧐'],
            ['BEF NOV 2000', 'before November ꧒꧐꧐꧐'],
            ['15 DEC 2000', '꧑꧕ Desember ꧒꧐꧐꧐'],
            ['DEC 2000', 'Desember ꧒꧐꧐꧐'],
            ['ABT DEC 2000', 'about Desember ꧒꧐꧐꧐'],
            ['FROM DEC 2000', 'from Desember ꧒꧐꧐꧐'],
            ['AFT DEC 2000', 'after Desember ꧒꧐꧐꧐'],
            ['BEF DEC 2000', 'before Desember ꧒꧐꧐꧐'],
            ['2000', '꧒꧐꧐꧐'],
            ['ABT 15 JAN 2000', 'about ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['CAL 15 JAN 2000', 'calculated ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['EST 15 JAN 2000', 'estimated ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['BEF 15 JAN 2000', 'before ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['AFT 15 JAN 2000', 'after ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['FROM 15 JAN 2000', 'from ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['TO 15 JAN 2000', 'to ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between ꧑꧕ Januari ꧒꧐꧐꧐ and ꧑꧕ Februari ꧒꧐꧐꧐'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from ꧑꧕ Januari ꧒꧐꧐꧐ to ꧑꧕ Februari ꧒꧐꧐꧐'],
            ['INT 15 JAN 2000', 'interpreted ꧑꧕ Januari ꧒꧐꧐꧐'],
            ['@#DJULIAN@ 15 JAN 1700', '꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '꧑꧕ Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '꧑꧕ Maret ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Maret ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about Maret ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from Maret ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after Maret ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before Maret ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '꧑꧕ April ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '꧑꧔ April ꧑꧖꧔꧕/꧔꧖ ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'April ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about April ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from April ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after April ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before April ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '꧑꧕ Mei ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Mei ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about Mei ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from Mei ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after Mei ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before Mei ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '꧑꧕ Juni ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Juni ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about Juni ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from Juni ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after Juni ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before Juni ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '꧑꧕ Juli ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Juli ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about Juli ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from Juli ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after Juli ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before Juli ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '꧑꧕ Agustus ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Agustus ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about Agustus ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from Agustus ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after Agustus ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before Agustus ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '꧑꧕ September ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'September ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about September ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from September ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after September ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before September ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '꧑꧕ Oktober ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Oktober ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about Oktober ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from Oktober ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after Oktober ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before Oktober ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '꧑꧕ November ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'November ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about November ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from November ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after November ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before November ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '꧑꧕ Desember ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Desember ꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about Desember ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from Desember ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after Desember ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before Desember ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DJULIAN@ 1700', '꧑꧗꧐꧐ ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ and ꧑꧕ Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ to ꧑꧕ Februari ꧑꧗꧐꧐ ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted ꧑꧕ Januari ꧑꧗꧐꧐ ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ TSH 5765', 'about Tishrei ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ TSH 5765', 'from Tishrei ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ TSH 5765', 'after Tishrei ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ TSH 5765', 'before Tishrei ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 CSH 5765', '꧑꧕ Heshvan ꧕꧗꧖꧕'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ CSH 5765', 'about Heshvan ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ CSH 5765', 'from Heshvan ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ CSH 5765', 'after Heshvan ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ CSH 5765', 'before Heshvan ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 KSL 5765', '꧑꧕ Kislev ꧕꧗꧖꧕'],
            ['@#DHEBREW@ KSL 5765', 'Kislev ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ KSL 5765', 'about Kislev ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ KSL 5765', 'from Kislev ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ KSL 5765', 'after Kislev ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ KSL 5765', 'before Kislev ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 TVT 5765', '꧑꧕ Tevet ꧕꧗꧖꧕'],
            ['@#DHEBREW@ TVT 5765', 'Tevet ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ TVT 5765', 'about Tevet ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ TVT 5765', 'from Tevet ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ TVT 5765', 'after Tevet ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ TVT 5765', 'before Tevet ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 SHV 5765', '꧑꧕ Shevat ꧕꧗꧖꧕'],
            ['@#DHEBREW@ SHV 5765', 'Shevat ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ SHV 5765', 'about Shevat ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ SHV 5765', 'from Shevat ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ SHV 5765', 'after Shevat ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ SHV 5765', 'before Shevat ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 ADR 5765', '꧑꧕ Adar I ꧕꧗꧖꧕'],
            ['@#DHEBREW@ ADR 5765', 'Adar I ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ ADR 5765', 'about Adar I ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ ADR 5765', 'from Adar I ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ ADR 5765', 'after Adar I ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ ADR 5765', 'before Adar I ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 ADS 5765', '꧑꧕ Adar II ꧕꧗꧖꧕'],
            ['@#DHEBREW@ ADS 5765', 'Adar II ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ ADS 5765', 'about Adar II ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ ADS 5765', 'from Adar II ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ ADS 5765', 'after Adar II ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ ADS 5765', 'before Adar II ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 NSN 5765', '꧑꧕ Nissan ꧕꧗꧖꧕'],
            ['@#DHEBREW@ NSN 5765', 'Nissan ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ NSN 5765', 'about Nissan ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ NSN 5765', 'from Nissan ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ NSN 5765', 'after Nissan ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ NSN 5765', 'before Nissan ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 IYR 5765', '꧑꧕ Iyar ꧕꧗꧖꧕'],
            ['@#DHEBREW@ IYR 5765', 'Iyar ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ IYR 5765', 'about Iyar ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ IYR 5765', 'from Iyar ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ IYR 5765', 'after Iyar ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ IYR 5765', 'before Iyar ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 SVN 5765', '꧑꧕ Sivan ꧕꧗꧖꧕'],
            ['@#DHEBREW@ SVN 5765', 'Sivan ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ SVN 5765', 'about Sivan ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ SVN 5765', 'from Sivan ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ SVN 5765', 'after Sivan ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ SVN 5765', 'before Sivan ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 TMZ 5765', '꧑꧕ Tamuz ꧕꧗꧖꧕'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about Tamuz ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from Tamuz ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after Tamuz ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before Tamuz ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 AAV 5765', '꧑꧕ Av ꧕꧗꧖꧕'],
            ['@#DHEBREW@ AAV 5765', 'Av ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ AAV 5765', 'about Av ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ AAV 5765', 'from Av ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ AAV 5765', 'after Av ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ AAV 5765', 'before Av ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 15 ELL 5765', '꧑꧕ Elul ꧕꧗꧖꧕'],
            ['@#DHEBREW@ ELL 5765', 'Elul ꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ ELL 5765', 'about Elul ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ ELL 5765', 'from Elul ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ ELL 5765', 'after Elul ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ ELL 5765', 'before Elul ꧕꧗꧖꧕'],
            ['@#DHEBREW@ 5765', '꧕꧗꧖꧕'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between ꧑꧕ Tishrei ꧕꧗꧖꧕ and ꧑꧕ Heshvan ꧕꧗꧖꧕'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from ꧑꧕ Tishrei ꧕꧗꧖꧕ to ꧑꧕ Heshvan ꧕꧗꧖꧕'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted ꧑꧕ Tishrei ꧕꧗꧖꧕'],
            ['@#DFRENCH R@ 15 VEND 12', '꧑꧕ Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '꧑꧕ Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '꧑꧕ Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '꧑꧕ Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '꧑꧕ Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '꧑꧕ Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '꧑꧕ Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '꧑꧕ Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '꧑꧕ Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '꧑꧕ Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '꧑꧕ Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '꧑꧕ Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '꧑꧕ jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about ꧑꧕ Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated ꧑꧕ Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated ꧑꧕ Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before ꧑꧕ Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after ꧑꧕ Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from ꧑꧕ Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to ꧑꧕ Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between ꧑꧕ Vendémiaire An XII and ꧑꧕ Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from ꧑꧕ Vendémiaire An XII to ꧑꧕ Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted ꧑꧕ Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about Muharram ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from Muharram ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after Muharram ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before Muharram ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 SAFAR 1425', '꧑꧕ Safar ꧑꧔꧒꧕'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about Safar ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from Safar ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after Safar ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before Safar ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 RABIA 1425', '꧑꧕ Rabi’ al-awwal ꧑꧔꧒꧕'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about Rabi’ al-awwal ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Rabi’ al-awwal ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after Rabi’ al-awwal ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before Rabi’ al-awwal ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 RABIT 1425', '꧑꧕ Rabi’ al-thani ꧑꧔꧒꧕'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about Rabi’ al-thani ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Rabi’ al-thani ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after Rabi’ al-thani ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before Rabi’ al-thani ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 JUMAA 1425', '꧑꧕ Jumada al-awwal ꧑꧔꧒꧕'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about Jumada al-awwal ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from Jumada al-awwal ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after Jumada al-awwal ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before Jumada al-awwal ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 JUMAT 1425', '꧑꧕ Jumada al-thani ꧑꧔꧒꧕'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about Jumada al-thani ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from Jumada al-thani ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after Jumada al-thani ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before Jumada al-thani ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 RAJAB 1425', '꧑꧕ Rajab ꧑꧔꧒꧕'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about Rajab ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from Rajab ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after Rajab ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before Rajab ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 SHAAB 1425', '꧑꧕ Sha’aban ꧑꧔꧒꧕'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about Sha’aban ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Sha’aban ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after Sha’aban ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before Sha’aban ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 RAMAD 1425', '꧑꧕ Ramadan ꧑꧔꧒꧕'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about Ramadan ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Ramadan ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after Ramadan ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before Ramadan ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 SHAWW 1425', '꧑꧕ Shawwal ꧑꧔꧒꧕'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about Shawwal ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Shawwal ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after Shawwal ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before Shawwal ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '꧑꧕ Dhu al-Qi’dah ꧑꧔꧒꧕'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah ꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about Dhu al-Qi’dah ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Dhu al-Qi’dah ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after Dhu al-Qi’dah ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before Dhu al-Qi’dah ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 15 DHUAL 1425', '꧑꧔꧒꧕'],
            ['@#DHIJRI@ DHUAL 1425', '꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before ꧑꧔꧒꧕'],
            ['@#DHIJRI@ 1425', '꧑꧔꧒꧕'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between ꧑꧕ Muharram ꧑꧔꧒꧕ and ꧑꧕ Safar ꧑꧔꧒꧕'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from ꧑꧕ Muharram ꧑꧔꧒꧕ to ꧑꧕ Safar ꧑꧔꧒꧕'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted ꧑꧕ Muharram ꧑꧔꧒꧕'],
            ['@#DJALALI@ 15 FARVA 1384', '꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ FARVA 1384', 'about Farvardin ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ FARVA 1384', 'from Farvardin ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ FARVA 1384', 'after Farvardin ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ FARVA 1384', 'before Farvardin ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 ORDIB 1384', '꧑꧕ Ordibehesht ꧑꧓꧘꧔'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about Ordibehesht ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from Ordibehesht ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after Ordibehesht ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before Ordibehesht ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 KHORD 1384', '꧑꧕ Khordad ꧑꧓꧘꧔'],
            ['@#DJALALI@ KHORD 1384', 'Khordad ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ KHORD 1384', 'about Khordad ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ KHORD 1384', 'from Khordad ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ KHORD 1384', 'after Khordad ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ KHORD 1384', 'before Khordad ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 TIR 1384', '꧑꧕ Tir ꧑꧓꧘꧔'],
            ['@#DJALALI@ TIR 1384', 'Tir ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ TIR 1384', 'about Tir ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ TIR 1384', 'from Tir ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ TIR 1384', 'after Tir ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ TIR 1384', 'before Tir ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 MORDA 1384', '꧑꧕ Mordad ꧑꧓꧘꧔'],
            ['@#DJALALI@ MORDA 1384', 'Mordad ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ MORDA 1384', 'about Mordad ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ MORDA 1384', 'from Mordad ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ MORDA 1384', 'after Mordad ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ MORDA 1384', 'before Mordad ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 SHAHR 1384', '꧑꧕ Shahrivar ꧑꧓꧘꧔'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about Shahrivar ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from Shahrivar ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after Shahrivar ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before Shahrivar ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 MEHR 1384', '꧑꧕ Mehr ꧑꧓꧘꧔'],
            ['@#DJALALI@ MEHR 1384', 'Mehr ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ MEHR 1384', 'about Mehr ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ MEHR 1384', 'from Mehr ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ MEHR 1384', 'after Mehr ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ MEHR 1384', 'before Mehr ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 ABAN 1384', '꧑꧕ Aban ꧑꧓꧘꧔'],
            ['@#DJALALI@ ABAN 1384', 'Aban ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ ABAN 1384', 'about Aban ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ ABAN 1384', 'from Aban ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ ABAN 1384', 'after Aban ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ ABAN 1384', 'before Aban ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 AZAR 1384', '꧑꧕ Azar ꧑꧓꧘꧔'],
            ['@#DJALALI@ AZAR 1384', 'Azar ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ AZAR 1384', 'about Azar ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ AZAR 1384', 'from Azar ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ AZAR 1384', 'after Azar ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ AZAR 1384', 'before Azar ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 DEY 1384', '꧑꧕ Dey ꧑꧓꧘꧔'],
            ['@#DJALALI@ DEY 1384', 'Dey ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ DEY 1384', 'about Dey ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ DEY 1384', 'from Dey ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ DEY 1384', 'after Dey ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ DEY 1384', 'before Dey ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 BAHMA 1384', '꧑꧕ Bahman ꧑꧓꧘꧔'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about Bahman ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from Bahman ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after Bahman ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before Bahman ꧑꧓꧘꧔'],
            ['@#DJALALI@ 15 ESFAN 1384', '꧑꧕ Esfand ꧑꧓꧘꧔'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand ꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about Esfand ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from Esfand ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after Esfand ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before Esfand ꧑꧓꧘꧔'],
            ['@#DJALALI@ 1384', '꧑꧓꧘꧔'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about ꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated ꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated ꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before ꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after ꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from ꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to ꧑꧕ Farvardin ꧑꧓꧘꧔'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between ꧑꧕ Farvardin ꧑꧓꧘꧔ and ꧑꧕ Ordibehesht ꧑꧓꧘꧔'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from ꧑꧕ Farvardin ꧑꧓꧘꧔ to ꧑꧕ Ordibehesht ꧑꧓꧘꧔'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted ꧑꧕ Farvardin ꧑꧓꧘꧔'],
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
        self::assertSame('one lan two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two lan three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one utawa two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two utawa three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Core family: husband + wife with son (born 2000) and daughter (born 1998)
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 1 JAN 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 1 JAN 1998");
        $child = self::unknown('c', "1 FAMC @fm@");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $fosterDaughter = self::female('fsd', "1 FAMC @fd@\n2 PEDI foster");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");

        // Husband's family
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $brotherOfH = self::male('bh', "1 FAMS @fbro@\n1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMS @fsis@\n1 FAMC @fp@");

        // Wife's family
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfW = self::male('bw', "1 FAMC @fw@");
        $sisterOfW = self::female('sw', "1 FAMC @fw@");

        // Children's spouses
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");

        // Nieces/nephews
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $nieceFromSis = self::female('ns', "1 FAMC @fsis@");
        $nephewFromSis = self::male('nps', "1 FAMC @fsis@");

        // Cousins
        $cousinM = self::male('cm', "1 FAMC @fbro@");
        $cousinF = self::female('cf', "1 FAMC @fsis@");

        // Great-grandparents
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");

        // Engaged
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        // Families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fsd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @bw@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cm@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 WIFE @sh@\n1 CHIL @ns@\n1 CHIL @nps@\n1 CHIL @cf@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @mh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $fosterDaughter, $stepDaughter,
             $fatherOfH, $motherOfH, $brotherOfH, $sisterOfH,
             $fatherOfW, $motherOfW, $brotherOfW, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $nieceFromSis, $nephewFromSis,
             $cousinM, $cousinF,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fsis, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('bojo wadon', 'bojo lanang', [$husband, $fm, $wife]);
        self::assertRelationshipNames('tilas bojo lanang', 'tilas bojo wadon', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('pacangan wadon', 'pacangan lanang', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('ibu', 'anak lanang', [$son, $fm, $wife]);
        self::assertRelationshipNames('bapak', 'anak lanang', [$son, $fm, $husband]);
        self::assertRelationshipNames('ibu', 'anak wadon', [$daughter, $fm, $wife]);

        // Adopted / Fostered
        self::assertRelationshipNames('ibu angkat', 'anak lanang angkat', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('ibu asuh', 'anak wadon asuh', [$fosterDaughter, $fd, $wife]);

        // Siblings — elder/younger (daughter born 1998 is older than son born 2000)
        self::assertRelationshipNames('mbakyu', 'adhik lanang', [$son, $fm, $daughter]);
        self::assertRelationshipNames('adhik lanang', 'mbakyu', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('bapak kuwalon', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('anak kuwalon wadon', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('maratuwa wadon', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('maratuwa lanang', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('maratuwa wadon', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('maratuwa lanang', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('mantu wadon', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('mantu lanang', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('ipe lanang', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('ipe wadon', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents
        self::assertRelationshipName('simbah putri', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('simbah kakung', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('simbah putri', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('simbah kakung', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('putu lanang', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('putu wadon', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts/Uncles
        self::assertRelationshipName('bulik', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('paklik', [$son, $fm, $husband, $fp, $brotherOfH]);
        self::assertRelationshipName('bulik', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('paklik', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces/Nephews
        self::assertRelationshipName('keponakan wadon', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('keponakan lanang', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        self::assertRelationshipName('keponakan wadon', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('keponakan lanang', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins
        self::assertRelationshipName('misanan lanang', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinM]);
        self::assertRelationshipName('misanan wadon', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinF]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('simbah buyut putri', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('simbah buyut kakung', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic — grandparent's sibling, n=2)
        self::assertRelationshipName('bulik', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('paklik', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
