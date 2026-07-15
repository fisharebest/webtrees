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
use Fisharebest\Webtrees\I18N\Languages\Sundanese;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Sundanese::class)]
class SundaneseTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Sundanese();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Sund, self::language()->script());
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
        self::assertSame('su', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Basa Sunda', self::language()->endonym());
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
        self::assertSame('-᮱᮲᮳,᮴᮵᮶.᮰᮷᮸᮹', self::language()->digits('-123,456.0789'));
    }
    public function testNumber(): void
    {
        self::assertSame('-᮱᮲᮳.᮴᮵᮶,᮰᮷᮸᮹', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-᮱᮲᮳.᮴᮵᮶,᮰᮷᮸᮹%', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '᮱᮵ Januari ᮲᮰᮰᮰'],
            ['JAN 2000', 'Januari ᮲᮰᮰᮰'],
            ['ABT JAN 2000', 'about Januari ᮲᮰᮰᮰'],
            ['FROM JAN 2000', 'from Januari ᮲᮰᮰᮰'],
            ['AFT JAN 2000', 'after Januari ᮲᮰᮰᮰'],
            ['BEF JAN 2000', 'before Januari ᮲᮰᮰᮰'],
            ['15 FEB 2000', '᮱᮵ Pébruari ᮲᮰᮰᮰'],
            ['FEB 2000', 'Pébruari ᮲᮰᮰᮰'],
            ['ABT FEB 2000', 'about Pébruari ᮲᮰᮰᮰'],
            ['FROM FEB 2000', 'from Pébruari ᮲᮰᮰᮰'],
            ['AFT FEB 2000', 'after Pébruari ᮲᮰᮰᮰'],
            ['BEF FEB 2000', 'before Pébruari ᮲᮰᮰᮰'],
            ['15 MAR 2000', '᮱᮵ Maret ᮲᮰᮰᮰'],
            ['MAR 2000', 'Maret ᮲᮰᮰᮰'],
            ['ABT MAR 2000', 'about Maret ᮲᮰᮰᮰'],
            ['FROM MAR 2000', 'from Maret ᮲᮰᮰᮰'],
            ['AFT MAR 2000', 'after Maret ᮲᮰᮰᮰'],
            ['BEF MAR 2000', 'before Maret ᮲᮰᮰᮰'],
            ['15 APR 2000', '᮱᮵ April ᮲᮰᮰᮰'],
            ['APR 2000', 'April ᮲᮰᮰᮰'],
            ['ABT APR 2000', 'about April ᮲᮰᮰᮰'],
            ['FROM APR 2000', 'from April ᮲᮰᮰᮰'],
            ['AFT APR 2000', 'after April ᮲᮰᮰᮰'],
            ['BEF APR 2000', 'before April ᮲᮰᮰᮰'],
            ['15 MAY 2000', '᮱᮵ Méi ᮲᮰᮰᮰'],
            ['MAY 2000', 'Méi ᮲᮰᮰᮰'],
            ['ABT MAY 2000', 'about Méi ᮲᮰᮰᮰'],
            ['FROM MAY 2000', 'from Méi ᮲᮰᮰᮰'],
            ['AFT MAY 2000', 'after Méi ᮲᮰᮰᮰'],
            ['BEF MAY 2000', 'before Méi ᮲᮰᮰᮰'],
            ['15 JUN 2000', '᮱᮵ Juni ᮲᮰᮰᮰'],
            ['JUN 2000', 'Juni ᮲᮰᮰᮰'],
            ['ABT JUN 2000', 'about Juni ᮲᮰᮰᮰'],
            ['FROM JUN 2000', 'from Juni ᮲᮰᮰᮰'],
            ['AFT JUN 2000', 'after Juni ᮲᮰᮰᮰'],
            ['BEF JUN 2000', 'before Juni ᮲᮰᮰᮰'],
            ['15 JUL 2000', '᮱᮵ Juli ᮲᮰᮰᮰'],
            ['JUL 2000', 'Juli ᮲᮰᮰᮰'],
            ['ABT JUL 2000', 'about Juli ᮲᮰᮰᮰'],
            ['FROM JUL 2000', 'from Juli ᮲᮰᮰᮰'],
            ['AFT JUL 2000', 'after Juli ᮲᮰᮰᮰'],
            ['BEF JUL 2000', 'before Juli ᮲᮰᮰᮰'],
            ['15 AUG 2000', '᮱᮵ Agustus ᮲᮰᮰᮰'],
            ['AUG 2000', 'Agustus ᮲᮰᮰᮰'],
            ['ABT AUG 2000', 'about Agustus ᮲᮰᮰᮰'],
            ['FROM AUG 2000', 'from Agustus ᮲᮰᮰᮰'],
            ['AFT AUG 2000', 'after Agustus ᮲᮰᮰᮰'],
            ['BEF AUG 2000', 'before Agustus ᮲᮰᮰᮰'],
            ['15 SEP 2000', '᮱᮵ Séptémber ᮲᮰᮰᮰'],
            ['SEP 2000', 'Séptémber ᮲᮰᮰᮰'],
            ['ABT SEP 2000', 'about Séptémber ᮲᮰᮰᮰'],
            ['FROM SEP 2000', 'from Séptémber ᮲᮰᮰᮰'],
            ['AFT SEP 2000', 'after Séptémber ᮲᮰᮰᮰'],
            ['BEF SEP 2000', 'before Séptémber ᮲᮰᮰᮰'],
            ['15 OCT 2000', '᮱᮵ Oktober ᮲᮰᮰᮰'],
            ['OCT 2000', 'Oktober ᮲᮰᮰᮰'],
            ['ABT OCT 2000', 'about Oktober ᮲᮰᮰᮰'],
            ['FROM OCT 2000', 'from Oktober ᮲᮰᮰᮰'],
            ['AFT OCT 2000', 'after Oktober ᮲᮰᮰᮰'],
            ['BEF OCT 2000', 'before Oktober ᮲᮰᮰᮰'],
            ['15 NOV 2000', '᮱᮵ Nopémber ᮲᮰᮰᮰'],
            ['NOV 2000', 'Nopémber ᮲᮰᮰᮰'],
            ['ABT NOV 2000', 'about Nopémber ᮲᮰᮰᮰'],
            ['FROM NOV 2000', 'from Nopémber ᮲᮰᮰᮰'],
            ['AFT NOV 2000', 'after Nopémber ᮲᮰᮰᮰'],
            ['BEF NOV 2000', 'before Nopémber ᮲᮰᮰᮰'],
            ['15 DEC 2000', '᮱᮵ Désémber ᮲᮰᮰᮰'],
            ['DEC 2000', 'Désémber ᮲᮰᮰᮰'],
            ['ABT DEC 2000', 'about Désémber ᮲᮰᮰᮰'],
            ['FROM DEC 2000', 'from Désémber ᮲᮰᮰᮰'],
            ['AFT DEC 2000', 'after Désémber ᮲᮰᮰᮰'],
            ['BEF DEC 2000', 'before Désémber ᮲᮰᮰᮰'],
            ['2000', '᮲᮰᮰᮰'],
            ['ABT 15 JAN 2000', 'about ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['CAL 15 JAN 2000', 'calculated ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['EST 15 JAN 2000', 'estimated ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['BEF 15 JAN 2000', 'before ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['AFT 15 JAN 2000', 'after ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['FROM 15 JAN 2000', 'from ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['TO 15 JAN 2000', 'to ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'between ᮱᮵ Januari ᮲᮰᮰᮰ and ᮱᮵ Pébruari ᮲᮰᮰᮰'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'from ᮱᮵ Januari ᮲᮰᮰᮰ to ᮱᮵ Pébruari ᮲᮰᮰᮰'],
            ['INT 15 JAN 2000', 'interpreted ᮱᮵ Januari ᮲᮰᮰᮰'],
            ['@#DJULIAN@ 15 JAN 1700', '᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ JAN 1700', 'Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ JAN 1700', 'about Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ JAN 1700', 'from Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ JAN 1700', 'after Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ JAN 1700', 'before Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 FEB 1700', '᮱᮵ Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ FEB 1700', 'Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ FEB 1700', 'about Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ FEB 1700', 'from Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ FEB 1700', 'after Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ FEB 1700', 'before Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAR 1700', '᮱᮵ Maret ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ MAR 1700', 'Maret ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAR 1700', 'about Maret ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAR 1700', 'from Maret ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ MAR 1700', 'after Maret ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAR 1700', 'before Maret ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 APR 1700', '᮱᮵ April ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 14 APR 1645/46', '᮱᮴ April ᮱᮶᮴᮵/᮴᮶ ᴄᴇ'],
            ['@#DJULIAN@ APR 1700', 'April ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ APR 1700', 'about April ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ APR 1700', 'from April ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ APR 1700', 'after April ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ APR 1700', 'before April ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 MAY 1700', '᮱᮵ Méi ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ MAY 1700', 'Méi ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ MAY 1700', 'about Méi ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ MAY 1700', 'from Méi ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ MAY 1700', 'after Méi ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ MAY 1700', 'before Méi ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUN 1700', '᮱᮵ Juni ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ JUN 1700', 'Juni ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUN 1700', 'about Juni ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUN 1700', 'from Juni ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ JUN 1700', 'after Juni ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUN 1700', 'before Juni ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 JUL 1700', '᮱᮵ Juli ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ JUL 1700', 'Juli ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ JUL 1700', 'about Juli ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ JUL 1700', 'from Juli ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ JUL 1700', 'after Juli ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ JUL 1700', 'before Juli ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 AUG 1700', '᮱᮵ Agustus ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ AUG 1700', 'Agustus ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ AUG 1700', 'about Agustus ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ AUG 1700', 'from Agustus ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ AUG 1700', 'after Agustus ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ AUG 1700', 'before Agustus ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 SEP 1700', '᮱᮵ Séptémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ SEP 1700', 'Séptémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ SEP 1700', 'about Séptémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ SEP 1700', 'from Séptémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ SEP 1700', 'after Séptémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ SEP 1700', 'before Séptémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 OCT 1700', '᮱᮵ Oktober ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ OCT 1700', 'Oktober ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ OCT 1700', 'about Oktober ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ OCT 1700', 'from Oktober ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ OCT 1700', 'after Oktober ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ OCT 1700', 'before Oktober ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 NOV 1700', '᮱᮵ Nopémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ NOV 1700', 'Nopémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ NOV 1700', 'about Nopémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ NOV 1700', 'from Nopémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ NOV 1700', 'after Nopémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ NOV 1700', 'before Nopémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 15 DEC 1700', '᮱᮵ Désémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ DEC 1700', 'Désémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ DEC 1700', 'about Désémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ DEC 1700', 'from Désémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ DEC 1700', 'after Désémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ DEC 1700', 'before Désémber ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DJULIAN@ 1700', '᮱᮷᮰᮰ ᴄᴇ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'about ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculated ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimated ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'before ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'after ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'from ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'to ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'between ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ and ᮱᮵ Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'from ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ to ᮱᮵ Pébruari ᮱᮷᮰᮰ ᴄᴇ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpreted ᮱᮵ Januari ᮱᮷᮰᮰ ᴄᴇ'],
            ['@#DHEBREW@ 15 TSH 5765', '᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ TSH 5765', 'about Tishrei ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ TSH 5765', 'from Tishrei ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ TSH 5765', 'after Tishrei ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ TSH 5765', 'before Tishrei ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 CSH 5765', '᮱᮵ Heshvan ᮵᮷᮶᮵'],
            ['@#DHEBREW@ CSH 5765', 'Heshvan ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ CSH 5765', 'about Heshvan ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ CSH 5765', 'from Heshvan ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ CSH 5765', 'after Heshvan ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ CSH 5765', 'before Heshvan ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 KSL 5765', '᮱᮵ Kislev ᮵᮷᮶᮵'],
            ['@#DHEBREW@ KSL 5765', 'Kislev ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ KSL 5765', 'about Kislev ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ KSL 5765', 'from Kislev ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ KSL 5765', 'after Kislev ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ KSL 5765', 'before Kislev ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 TVT 5765', '᮱᮵ Tevet ᮵᮷᮶᮵'],
            ['@#DHEBREW@ TVT 5765', 'Tevet ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ TVT 5765', 'about Tevet ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ TVT 5765', 'from Tevet ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ TVT 5765', 'after Tevet ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ TVT 5765', 'before Tevet ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 SHV 5765', '᮱᮵ Shevat ᮵᮷᮶᮵'],
            ['@#DHEBREW@ SHV 5765', 'Shevat ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ SHV 5765', 'about Shevat ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ SHV 5765', 'from Shevat ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ SHV 5765', 'after Shevat ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ SHV 5765', 'before Shevat ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 ADR 5765', '᮱᮵ Adar I ᮵᮷᮶᮵'],
            ['@#DHEBREW@ ADR 5765', 'Adar I ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ ADR 5765', 'about Adar I ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ ADR 5765', 'from Adar I ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ ADR 5765', 'after Adar I ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ ADR 5765', 'before Adar I ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 ADS 5765', '᮱᮵ Adar II ᮵᮷᮶᮵'],
            ['@#DHEBREW@ ADS 5765', 'Adar II ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ ADS 5765', 'about Adar II ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ ADS 5765', 'from Adar II ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ ADS 5765', 'after Adar II ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ ADS 5765', 'before Adar II ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 NSN 5765', '᮱᮵ Nissan ᮵᮷᮶᮵'],
            ['@#DHEBREW@ NSN 5765', 'Nissan ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ NSN 5765', 'about Nissan ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ NSN 5765', 'from Nissan ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ NSN 5765', 'after Nissan ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ NSN 5765', 'before Nissan ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 IYR 5765', '᮱᮵ Iyar ᮵᮷᮶᮵'],
            ['@#DHEBREW@ IYR 5765', 'Iyar ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ IYR 5765', 'about Iyar ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ IYR 5765', 'from Iyar ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ IYR 5765', 'after Iyar ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ IYR 5765', 'before Iyar ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 SVN 5765', '᮱᮵ Sivan ᮵᮷᮶᮵'],
            ['@#DHEBREW@ SVN 5765', 'Sivan ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ SVN 5765', 'about Sivan ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ SVN 5765', 'from Sivan ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ SVN 5765', 'after Sivan ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ SVN 5765', 'before Sivan ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 TMZ 5765', '᮱᮵ Tamuz ᮵᮷᮶᮵'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ TMZ 5765', 'about Tamuz ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ TMZ 5765', 'from Tamuz ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ TMZ 5765', 'after Tamuz ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ TMZ 5765', 'before Tamuz ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 AAV 5765', '᮱᮵ Av ᮵᮷᮶᮵'],
            ['@#DHEBREW@ AAV 5765', 'Av ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ AAV 5765', 'about Av ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ AAV 5765', 'from Av ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ AAV 5765', 'after Av ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ AAV 5765', 'before Av ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 15 ELL 5765', '᮱᮵ Elul ᮵᮷᮶᮵'],
            ['@#DHEBREW@ ELL 5765', 'Elul ᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ ELL 5765', 'about Elul ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ ELL 5765', 'from Elul ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ ELL 5765', 'after Elul ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ ELL 5765', 'before Elul ᮵᮷᮶᮵'],
            ['@#DHEBREW@ 5765', '᮵᮷᮶᮵'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'about ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculated ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimated ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'before ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'after ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'from ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'to ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'between ᮱᮵ Tishrei ᮵᮷᮶᮵ and ᮱᮵ Heshvan ᮵᮷᮶᮵'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'from ᮱᮵ Tishrei ᮵᮷᮶᮵ to ᮱᮵ Heshvan ᮵᮷᮶᮵'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpreted ᮱᮵ Tishrei ᮵᮷᮶᮵'],
            ['@#DFRENCH R@ 15 VEND 12', '᮱᮵ Vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'about Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'from Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'after Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'before Vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '᮱᮵ Brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'about Brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'from Brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'after Brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'before Brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '᮱᮵ Frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'about Frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'from Frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'after Frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'before Frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '᮱᮵ Nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'about Nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'from Nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'after Nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'before Nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '᮱᮵ Pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'about Pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'from Pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'after Pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'before Pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '᮱᮵ Ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'about Ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'from Ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'after Ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'before Ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '᮱᮵ Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'about Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'from Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'after Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'before Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '᮱᮵ Floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'about Floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'from Floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'after Floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'before Floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '᮱᮵ Prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'about Prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'from Prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'after Prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'before Prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '᮱᮵ Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'about Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'from Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'after Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'before Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '᮱᮵ Thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'about Thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'from Thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'after Thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'before Thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '᮱᮵ Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'about Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'from Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'after Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'before Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '᮱᮵ jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'about jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'from jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'after jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'before jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'about ᮱᮵ Vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculated ᮱᮵ Vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimated ᮱᮵ Vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'before ᮱᮵ Vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'after ᮱᮵ Vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'from ᮱᮵ Vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'to ᮱᮵ Vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'between ᮱᮵ Vendémiaire An XII and ᮱᮵ Brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'from ᮱᮵ Vendémiaire An XII to ᮱᮵ Brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpreted ᮱᮵ Vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'about Muharram ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'from Muharram ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'after Muharram ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'before Muharram ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 SAFAR 1425', '᮱᮵ Safar ᮱᮴᮲᮵'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'about Safar ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'from Safar ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'after Safar ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'before Safar ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 RABIA 1425', '᮱᮵ Rabi’ al-awwal ᮱᮴᮲᮵'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ RABIA 1425', 'about Rabi’ al-awwal ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ RABIA 1425', 'from Rabi’ al-awwal ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ RABIA 1425', 'after Rabi’ al-awwal ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ RABIA 1425', 'before Rabi’ al-awwal ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 RABIT 1425', '᮱᮵ Rabi’ al-thani ᮱᮴᮲᮵'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ RABIT 1425', 'about Rabi’ al-thani ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ RABIT 1425', 'from Rabi’ al-thani ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ RABIT 1425', 'after Rabi’ al-thani ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ RABIT 1425', 'before Rabi’ al-thani ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 JUMAA 1425', '᮱᮵ Jumada al-awwal ᮱᮴᮲᮵'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'about Jumada al-awwal ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'from Jumada al-awwal ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'after Jumada al-awwal ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'before Jumada al-awwal ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 JUMAT 1425', '᮱᮵ Jumada al-thani ᮱᮴᮲᮵'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'about Jumada al-thani ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'from Jumada al-thani ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'after Jumada al-thani ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'before Jumada al-thani ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 RAJAB 1425', '᮱᮵ Rajab ᮱᮴᮲᮵'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'about Rajab ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'from Rajab ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'after Rajab ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'before Rajab ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 SHAAB 1425', '᮱᮵ Sha’aban ᮱᮴᮲᮵'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'about Sha’aban ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'from Sha’aban ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'after Sha’aban ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'before Sha’aban ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 RAMAD 1425', '᮱᮵ Ramadan ᮱᮴᮲᮵'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'about Ramadan ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'from Ramadan ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'after Ramadan ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'before Ramadan ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 SHAWW 1425', '᮱᮵ Shawwal ᮱᮴᮲᮵'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'about Shawwal ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'from Shawwal ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'after Shawwal ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'before Shawwal ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '᮱᮵ Dhu al-Qi’dah ᮱᮴᮲᮵'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah ᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'about Dhu al-Qi’dah ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'from Dhu al-Qi’dah ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'after Dhu al-Qi’dah ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'before Dhu al-Qi’dah ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 15 DHUAL 1425', '᮱᮴᮲᮵'],
            ['@#DHIJRI@ DHUAL 1425', '᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'about ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'from ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'after ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'before ᮱᮴᮲᮵'],
            ['@#DHIJRI@ 1425', '᮱᮴᮲᮵'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'about ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculated ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimated ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'before ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'after ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'from ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'to ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'between ᮱᮵ Muharram ᮱᮴᮲᮵ and ᮱᮵ Safar ᮱᮴᮲᮵'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'from ᮱᮵ Muharram ᮱᮴᮲᮵ to ᮱᮵ Safar ᮱᮴᮲᮵'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpreted ᮱᮵ Muharram ᮱᮴᮲᮵'],
            ['@#DJALALI@ 15 FARVA 1384', '᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ FARVA 1384', 'about Farvardin ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ FARVA 1384', 'from Farvardin ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ FARVA 1384', 'after Farvardin ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ FARVA 1384', 'before Farvardin ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 ORDIB 1384', '᮱᮵ Ordibehesht ᮱᮳᮸᮴'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ ORDIB 1384', 'about Ordibehesht ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ ORDIB 1384', 'from Ordibehesht ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ ORDIB 1384', 'after Ordibehesht ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ ORDIB 1384', 'before Ordibehesht ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 KHORD 1384', '᮱᮵ Khordad ᮱᮳᮸᮴'],
            ['@#DJALALI@ KHORD 1384', 'Khordad ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ KHORD 1384', 'about Khordad ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ KHORD 1384', 'from Khordad ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ KHORD 1384', 'after Khordad ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ KHORD 1384', 'before Khordad ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 TIR 1384', '᮱᮵ Tir ᮱᮳᮸᮴'],
            ['@#DJALALI@ TIR 1384', 'Tir ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ TIR 1384', 'about Tir ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ TIR 1384', 'from Tir ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ TIR 1384', 'after Tir ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ TIR 1384', 'before Tir ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 MORDA 1384', '᮱᮵ Mordad ᮱᮳᮸᮴'],
            ['@#DJALALI@ MORDA 1384', 'Mordad ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ MORDA 1384', 'about Mordad ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ MORDA 1384', 'from Mordad ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ MORDA 1384', 'after Mordad ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ MORDA 1384', 'before Mordad ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 SHAHR 1384', '᮱᮵ Shahrivar ᮱᮳᮸᮴'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ SHAHR 1384', 'about Shahrivar ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ SHAHR 1384', 'from Shahrivar ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ SHAHR 1384', 'after Shahrivar ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ SHAHR 1384', 'before Shahrivar ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 MEHR 1384', '᮱᮵ Mehr ᮱᮳᮸᮴'],
            ['@#DJALALI@ MEHR 1384', 'Mehr ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ MEHR 1384', 'about Mehr ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ MEHR 1384', 'from Mehr ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ MEHR 1384', 'after Mehr ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ MEHR 1384', 'before Mehr ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 ABAN 1384', '᮱᮵ Aban ᮱᮳᮸᮴'],
            ['@#DJALALI@ ABAN 1384', 'Aban ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ ABAN 1384', 'about Aban ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ ABAN 1384', 'from Aban ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ ABAN 1384', 'after Aban ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ ABAN 1384', 'before Aban ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 AZAR 1384', '᮱᮵ Azar ᮱᮳᮸᮴'],
            ['@#DJALALI@ AZAR 1384', 'Azar ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ AZAR 1384', 'about Azar ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ AZAR 1384', 'from Azar ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ AZAR 1384', 'after Azar ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ AZAR 1384', 'before Azar ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 DEY 1384', '᮱᮵ Dey ᮱᮳᮸᮴'],
            ['@#DJALALI@ DEY 1384', 'Dey ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ DEY 1384', 'about Dey ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ DEY 1384', 'from Dey ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ DEY 1384', 'after Dey ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ DEY 1384', 'before Dey ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 BAHMA 1384', '᮱᮵ Bahman ᮱᮳᮸᮴'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ BAHMA 1384', 'about Bahman ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ BAHMA 1384', 'from Bahman ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ BAHMA 1384', 'after Bahman ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ BAHMA 1384', 'before Bahman ᮱᮳᮸᮴'],
            ['@#DJALALI@ 15 ESFAN 1384', '᮱᮵ Esfand ᮱᮳᮸᮴'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand ᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ ESFAN 1384', 'about Esfand ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ ESFAN 1384', 'from Esfand ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ ESFAN 1384', 'after Esfand ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ ESFAN 1384', 'before Esfand ᮱᮳᮸᮴'],
            ['@#DJALALI@ 1384', '᮱᮳᮸᮴'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'about ᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculated ᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimated ᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'before ᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'after ᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'from ᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'to ᮱᮵ Farvardin ᮱᮳᮸᮴'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'between ᮱᮵ Farvardin ᮱᮳᮸᮴ and ᮱᮵ Ordibehesht ᮱᮳᮸᮴'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'from ᮱᮵ Farvardin ᮱᮳᮸᮴ to ᮱᮵ Ordibehesht ᮱᮳᮸᮴'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpreted ᮱᮵ Farvardin ᮱᮳᮸᮴'],
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
        self::assertSame('one sareng two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two sareng three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one atanapi two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two atanapi three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('pamajikan', 'salaki', [$husband, $fm, $wife]);
        self::assertRelationshipNames('urut salaki', 'urut pamajikan', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('tunangan awéwé', 'tunangan lalaki', [$fiance, $fe, $engaged]);

        // Parents / Children
        self::assertRelationshipNames('indung', 'anak lalaki', [$son, $fm, $wife]);
        self::assertRelationshipNames('bapa', 'anak lalaki', [$son, $fm, $husband]);
        self::assertRelationshipNames('indung', 'anak awéwé', [$daughter, $fm, $wife]);

        // Adopted / Fostered
        self::assertRelationshipNames('indung angkat', 'anak lalaki angkat', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('indung asuh', 'anak awéwé asuh', [$fosterDaughter, $fd, $wife]);

        // Siblings — elder/younger (daughter born 1998 is older than son born 2000)
        self::assertRelationshipNames('lanceuk awéwé', 'adi lalaki', [$son, $fm, $daughter]);
        self::assertRelationshipNames('adi lalaki', 'lanceuk awéwé', [$daughter, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('bapa téré', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('anak téré awéwé', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents
        self::assertRelationshipName('mitoha awéwé', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('mitoha lalaki', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('mitoha awéwé', [$wife, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('mitoha lalaki', [$wife, $fm, $husband, $fp, $fatherOfH]);

        // In-laws — child's spouse
        self::assertRelationshipName('minantu awéwé', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('minantu lalaki', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings
        self::assertRelationshipName('ipar lalaki', [$husband, $fm, $wife, $fw, $brotherOfW]);
        self::assertRelationshipName('ipar awéwé', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents
        self::assertRelationshipName('nini', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipName('aki', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('nini', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('aki', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Grandchildren
        self::assertRelationshipName('incu lalaki', [$fatherOfH, $fp, $husband, $fm, $son]);
        self::assertRelationshipName('incu awéwé', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Aunts/Uncles
        self::assertRelationshipName('bibi', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('mamang', [$son, $fm, $husband, $fp, $brotherOfH]);
        self::assertRelationshipName('bibi', [$son, $fm, $wife, $fw, $sisterOfW]);
        self::assertRelationshipName('mamang', [$son, $fm, $wife, $fw, $brotherOfW]);

        // Nieces/Nephews
        self::assertRelationshipName('kaponakan awéwé', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('kaponakan lalaki', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);
        self::assertRelationshipName('kaponakan awéwé', [$husband, $fp, $sisterOfH, $fsis, $nieceFromSis]);
        self::assertRelationshipName('kaponakan lalaki', [$husband, $fp, $sisterOfH, $fsis, $nephewFromSis]);

        // Cousins
        self::assertRelationshipName('dulur misan lalaki', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinM]);
        self::assertRelationshipName('dulur misan awéwé', [$son, $fm, $husband, $fp, $sisterOfH, $fsis, $cousinF]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('nini buyut', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('aki buyut', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $paternalGF]);

        // Great-aunt/uncle (dynamic — grandparent's sibling, n=2)
        self::assertRelationshipName('bibi', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('mamang', [$son, $fm, $husband, $fp, $motherOfH, $fgp, $greatUncle]);
    }
}
