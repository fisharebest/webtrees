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
use Fisharebest\Webtrees\I18N\Languages\Italian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Italian::class)]
class ItalianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Italian();
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
        self::assertSame('it', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('italiano', self::language()->endonym());
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




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 gennaio 2000'],
            ['JAN 2000', 'gennaio 2000'],
            ['ABT JAN 2000', 'circa gennaio 2000'],
            ['FROM JAN 2000', 'dal gennaio 2000'],
            ['AFT JAN 2000', 'dopo il gennaio 2000'],
            ['BEF JAN 2000', 'prima del gennaio 2000'],
            ['15 FEB 2000', '15 febbraio 2000'],
            ['FEB 2000', 'febbraio 2000'],
            ['ABT FEB 2000', 'circa febbraio 2000'],
            ['FROM FEB 2000', 'dal febbraio 2000'],
            ['AFT FEB 2000', 'dopo il febbraio 2000'],
            ['BEF FEB 2000', 'prima del febbraio 2000'],
            ['15 MAR 2000', '15 marzo 2000'],
            ['MAR 2000', 'marzo 2000'],
            ['ABT MAR 2000', 'circa marzo 2000'],
            ['FROM MAR 2000', 'dal marzo 2000'],
            ['AFT MAR 2000', 'dopo il marzo 2000'],
            ['BEF MAR 2000', 'prima del marzo 2000'],
            ['15 APR 2000', '15 aprile 2000'],
            ['APR 2000', 'aprile 2000'],
            ['ABT APR 2000', 'circa aprile 2000'],
            ['FROM APR 2000', 'dal aprile 2000'],
            ['AFT APR 2000', 'dopo il aprile 2000'],
            ['BEF APR 2000', 'prima del aprile 2000'],
            ['15 MAY 2000', '15 maggio 2000'],
            ['MAY 2000', 'maggio 2000'],
            ['ABT MAY 2000', 'circa maggio 2000'],
            ['FROM MAY 2000', 'dal maggio 2000'],
            ['AFT MAY 2000', 'dopo il maggio 2000'],
            ['BEF MAY 2000', 'prima del maggio 2000'],
            ['15 JUN 2000', '15 giugno 2000'],
            ['JUN 2000', 'giugno 2000'],
            ['ABT JUN 2000', 'circa giugno 2000'],
            ['FROM JUN 2000', 'dal giugno 2000'],
            ['AFT JUN 2000', 'dopo il giugno 2000'],
            ['BEF JUN 2000', 'prima del giugno 2000'],
            ['15 JUL 2000', '15 luglio 2000'],
            ['JUL 2000', 'luglio 2000'],
            ['ABT JUL 2000', 'circa luglio 2000'],
            ['FROM JUL 2000', 'dal luglio 2000'],
            ['AFT JUL 2000', 'dopo il luglio 2000'],
            ['BEF JUL 2000', 'prima del luglio 2000'],
            ['15 AUG 2000', '15 agosto 2000'],
            ['AUG 2000', 'agosto 2000'],
            ['ABT AUG 2000', 'circa agosto 2000'],
            ['FROM AUG 2000', 'dal agosto 2000'],
            ['AFT AUG 2000', 'dopo il agosto 2000'],
            ['BEF AUG 2000', 'prima del agosto 2000'],
            ['15 SEP 2000', '15 settembre 2000'],
            ['SEP 2000', 'settembre 2000'],
            ['ABT SEP 2000', 'circa settembre 2000'],
            ['FROM SEP 2000', 'dal settembre 2000'],
            ['AFT SEP 2000', 'dopo il settembre 2000'],
            ['BEF SEP 2000', 'prima del settembre 2000'],
            ['15 OCT 2000', '15 ottobre 2000'],
            ['OCT 2000', 'ottobre 2000'],
            ['ABT OCT 2000', 'circa ottobre 2000'],
            ['FROM OCT 2000', 'dal ottobre 2000'],
            ['AFT OCT 2000', 'dopo il ottobre 2000'],
            ['BEF OCT 2000', 'prima del ottobre 2000'],
            ['15 NOV 2000', '15 novembre 2000'],
            ['NOV 2000', 'novembre 2000'],
            ['ABT NOV 2000', 'circa novembre 2000'],
            ['FROM NOV 2000', 'dal novembre 2000'],
            ['AFT NOV 2000', 'dopo il novembre 2000'],
            ['BEF NOV 2000', 'prima del novembre 2000'],
            ['15 DEC 2000', '15 dicembre 2000'],
            ['DEC 2000', 'dicembre 2000'],
            ['ABT DEC 2000', 'circa dicembre 2000'],
            ['FROM DEC 2000', 'dal dicembre 2000'],
            ['AFT DEC 2000', 'dopo il dicembre 2000'],
            ['BEF DEC 2000', 'prima del dicembre 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'circa 15 gennaio 2000'],
            ['CAL 15 JAN 2000', '15 gennaio 2000 (calcolata)'],
            ['EST 15 JAN 2000', '15 gennaio 2000 (stimata)'],
            ['BEF 15 JAN 2000', 'prima del 15 gennaio 2000'],
            ['AFT 15 JAN 2000', 'dopo il 15 gennaio 2000'],
            ['FROM 15 JAN 2000', 'dal 15 gennaio 2000'],
            ['TO 15 JAN 2000', 'fino al 15 gennaio 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'tra il 15 gennaio 2000 e il 15 febbraio 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'dal 15 gennaio 2000 al 15 febbraio 2000'],
            ['INT 15 JAN 2000', 'interpretato 15 gennaio 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 gennaio 1700 d.C.'],
            ['@#DJULIAN@ JAN 1700', 'gennaio 1700 d.C.'],
            ['ABT @#DJULIAN@ JAN 1700', 'circa gennaio 1700 d.C.'],
            ['FROM @#DJULIAN@ JAN 1700', 'dal gennaio 1700 d.C.'],
            ['AFT @#DJULIAN@ JAN 1700', 'dopo il gennaio 1700 d.C.'],
            ['BEF @#DJULIAN@ JAN 1700', 'prima del gennaio 1700 d.C.'],
            ['@#DJULIAN@ 15 FEB 1700', '15 febbraio 1700 d.C.'],
            ['@#DJULIAN@ FEB 1700', 'febbraio 1700 d.C.'],
            ['ABT @#DJULIAN@ FEB 1700', 'circa febbraio 1700 d.C.'],
            ['FROM @#DJULIAN@ FEB 1700', 'dal febbraio 1700 d.C.'],
            ['AFT @#DJULIAN@ FEB 1700', 'dopo il febbraio 1700 d.C.'],
            ['BEF @#DJULIAN@ FEB 1700', 'prima del febbraio 1700 d.C.'],
            ['@#DJULIAN@ 15 MAR 1700', '15 marzo 1700 d.C.'],
            ['@#DJULIAN@ MAR 1700', 'marzo 1700 d.C.'],
            ['ABT @#DJULIAN@ MAR 1700', 'circa marzo 1700 d.C.'],
            ['FROM @#DJULIAN@ MAR 1700', 'dal marzo 1700 d.C.'],
            ['AFT @#DJULIAN@ MAR 1700', 'dopo il marzo 1700 d.C.'],
            ['BEF @#DJULIAN@ MAR 1700', 'prima del marzo 1700 d.C.'],
            ['@#DJULIAN@ 15 APR 1700', '15 aprile 1700 d.C.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 aprile 1645/46 d.C.'],
            ['@#DJULIAN@ APR 1700', 'aprile 1700 d.C.'],
            ['ABT @#DJULIAN@ APR 1700', 'circa aprile 1700 d.C.'],
            ['FROM @#DJULIAN@ APR 1700', 'dal aprile 1700 d.C.'],
            ['AFT @#DJULIAN@ APR 1700', 'dopo il aprile 1700 d.C.'],
            ['BEF @#DJULIAN@ APR 1700', 'prima del aprile 1700 d.C.'],
            ['@#DJULIAN@ 15 MAY 1700', '15 maggio 1700 d.C.'],
            ['@#DJULIAN@ MAY 1700', 'maggio 1700 d.C.'],
            ['ABT @#DJULIAN@ MAY 1700', 'circa maggio 1700 d.C.'],
            ['FROM @#DJULIAN@ MAY 1700', 'dal maggio 1700 d.C.'],
            ['AFT @#DJULIAN@ MAY 1700', 'dopo il maggio 1700 d.C.'],
            ['BEF @#DJULIAN@ MAY 1700', 'prima del maggio 1700 d.C.'],
            ['@#DJULIAN@ 15 JUN 1700', '15 giugno 1700 d.C.'],
            ['@#DJULIAN@ JUN 1700', 'giugno 1700 d.C.'],
            ['ABT @#DJULIAN@ JUN 1700', 'circa giugno 1700 d.C.'],
            ['FROM @#DJULIAN@ JUN 1700', 'dal giugno 1700 d.C.'],
            ['AFT @#DJULIAN@ JUN 1700', 'dopo il giugno 1700 d.C.'],
            ['BEF @#DJULIAN@ JUN 1700', 'prima del giugno 1700 d.C.'],
            ['@#DJULIAN@ 15 JUL 1700', '15 luglio 1700 d.C.'],
            ['@#DJULIAN@ JUL 1700', 'luglio 1700 d.C.'],
            ['ABT @#DJULIAN@ JUL 1700', 'circa luglio 1700 d.C.'],
            ['FROM @#DJULIAN@ JUL 1700', 'dal luglio 1700 d.C.'],
            ['AFT @#DJULIAN@ JUL 1700', 'dopo il luglio 1700 d.C.'],
            ['BEF @#DJULIAN@ JUL 1700', 'prima del luglio 1700 d.C.'],
            ['@#DJULIAN@ 15 AUG 1700', '15 agosto 1700 d.C.'],
            ['@#DJULIAN@ AUG 1700', 'agosto 1700 d.C.'],
            ['ABT @#DJULIAN@ AUG 1700', 'circa agosto 1700 d.C.'],
            ['FROM @#DJULIAN@ AUG 1700', 'dal agosto 1700 d.C.'],
            ['AFT @#DJULIAN@ AUG 1700', 'dopo il agosto 1700 d.C.'],
            ['BEF @#DJULIAN@ AUG 1700', 'prima del agosto 1700 d.C.'],
            ['@#DJULIAN@ 15 SEP 1700', '15 settembre 1700 d.C.'],
            ['@#DJULIAN@ SEP 1700', 'settembre 1700 d.C.'],
            ['ABT @#DJULIAN@ SEP 1700', 'circa settembre 1700 d.C.'],
            ['FROM @#DJULIAN@ SEP 1700', 'dal settembre 1700 d.C.'],
            ['AFT @#DJULIAN@ SEP 1700', 'dopo il settembre 1700 d.C.'],
            ['BEF @#DJULIAN@ SEP 1700', 'prima del settembre 1700 d.C.'],
            ['@#DJULIAN@ 15 OCT 1700', '15 ottobre 1700 d.C.'],
            ['@#DJULIAN@ OCT 1700', 'ottobre 1700 d.C.'],
            ['ABT @#DJULIAN@ OCT 1700', 'circa ottobre 1700 d.C.'],
            ['FROM @#DJULIAN@ OCT 1700', 'dal ottobre 1700 d.C.'],
            ['AFT @#DJULIAN@ OCT 1700', 'dopo il ottobre 1700 d.C.'],
            ['BEF @#DJULIAN@ OCT 1700', 'prima del ottobre 1700 d.C.'],
            ['@#DJULIAN@ 15 NOV 1700', '15 novembre 1700 d.C.'],
            ['@#DJULIAN@ NOV 1700', 'novembre 1700 d.C.'],
            ['ABT @#DJULIAN@ NOV 1700', 'circa novembre 1700 d.C.'],
            ['FROM @#DJULIAN@ NOV 1700', 'dal novembre 1700 d.C.'],
            ['AFT @#DJULIAN@ NOV 1700', 'dopo il novembre 1700 d.C.'],
            ['BEF @#DJULIAN@ NOV 1700', 'prima del novembre 1700 d.C.'],
            ['@#DJULIAN@ 15 DEC 1700', '15 dicembre 1700 d.C.'],
            ['@#DJULIAN@ DEC 1700', 'dicembre 1700 d.C.'],
            ['ABT @#DJULIAN@ DEC 1700', 'circa dicembre 1700 d.C.'],
            ['FROM @#DJULIAN@ DEC 1700', 'dal dicembre 1700 d.C.'],
            ['AFT @#DJULIAN@ DEC 1700', 'dopo il dicembre 1700 d.C.'],
            ['BEF @#DJULIAN@ DEC 1700', 'prima del dicembre 1700 d.C.'],
            ['@#DJULIAN@ 1700', '1700 d.C.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'circa 15 gennaio 1700 d.C.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', '15 gennaio 1700 d.C. (calcolata)'],
            ['EST @#DJULIAN@ 15 JAN 1700', '15 gennaio 1700 d.C. (stimata)'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'prima del 15 gennaio 1700 d.C.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'dopo il 15 gennaio 1700 d.C.'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'dal 15 gennaio 1700 d.C.'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'fino al 15 gennaio 1700 d.C.'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'tra il 15 gennaio 1700 d.C. e il 15 febbraio 1700 d.C.'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'dal 15 gennaio 1700 d.C. al 15 febbraio 1700 d.C.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretato 15 gennaio 1700 d.C.'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrì 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrì 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'circa Tishrì 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'dal Tishrì 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'dopo il Tishrì 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'prima del Tishrì 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Cheshvàn 5765'],
            ['@#DHEBREW@ CSH 5765', 'Cheshvàn 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'circa Cheshvàn 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'dal Cheshvàn 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'dopo il Cheshvàn 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'prima del Cheshvàn 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislèv 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislèv 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'circa Kislèv 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'dal Kislèv 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'dopo il Kislèv 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'prima del Kislèv 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevèt 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevèt 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'circa Tevèt 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'dal Tevèt 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'dopo il Tevèt 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'prima del Tevèt 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevàt 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevàt 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'circa Shevàt 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'dal Shevàt 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'dopo il Shevàt 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'prima del Shevàt 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adàr I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adàr I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'circa Adàr I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'dal Adàr I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'dopo il Adàr I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'prima del Adàr I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adr II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adr II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'circa Adr II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'dal Adr II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'dopo il Adr II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'prima del Adr II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nisàn 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nisàn 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'circa Nisàn 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'dal Nisàn 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'dopo il Nisàn 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'prima del Nisàn 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyàr 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyàr 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'circa Iyàr 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'dal Iyàr 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'dopo il Iyàr 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'prima del Iyàr 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivàn 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivàn 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'circa Sivàn 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'dal Sivàn 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'dopo il Sivàn 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'prima del Sivàn 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamùz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamùz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'circa Tamùz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'dal Tamùz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'dopo il Tamùz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'prima del Tamùz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'circa Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'dal Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'dopo il Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'prima del Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elùl 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elùl 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'circa Elùl 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'dal Elùl 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'dopo il Elùl 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'prima del Elùl 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'circa 15 Tishrì 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', '15 Tishrì 5765 (calcolata)'],
            ['EST @#DHEBREW@ 15 TSH 5765', '15 Tishrì 5765 (stimata)'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'prima del 15 Tishrì 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'dopo il 15 Tishrì 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'dal 15 Tishrì 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'fino al 15 Tishrì 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'tra il 15 Tishrì 5765 e il 15 Cheshvàn 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'dal 15 Tishrì 5765 al 15 Cheshvàn 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretato 15 Tishrì 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendemmiaio An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendemmiaio An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'circa Vendemmiaio An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'dal Vendemmiaio An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'dopo il Vendemmiaio An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'prima del Vendemmiaio An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumaio An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumaio An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'circa Brumaio An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'dal Brumaio An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'dopo il Brumaio An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'prima del Brumaio An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimaio An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimaio An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'circa Frimaio An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'dal Frimaio An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'dopo il Frimaio An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'prima del Frimaio An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nevoso An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nevoso An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'circa Nevoso An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'dal Nevoso An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'dopo il Nevoso An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'prima del Nevoso An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Piovoso An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Piovoso An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'circa Piovoso An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'dal Piovoso An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'dopo il Piovoso An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'prima del Piovoso An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventoso An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventoso An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'circa Ventoso An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'dal Ventoso An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'dopo il Ventoso An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'prima del Ventoso An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinale An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinale An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'circa Germinale An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'dal Germinale An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'dopo il Germinale An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'prima del Germinale An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floreale An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floreale An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'circa Floreale An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'dal Floreale An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'dopo il Floreale An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'prima del Floreale An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Pratile An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Pratile An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'circa Pratile An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'dal Pratile An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'dopo il Pratile An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'prima del Pratile An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidoro An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidoro An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'circa Messidoro An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'dal Messidoro An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'dopo il Messidoro An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'prima del Messidoro An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Termidoro An XII'],
            ['@#DFRENCH R@ THER 12', 'Termidoro An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'circa Termidoro An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'dal Termidoro An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'dopo il Termidoro An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'prima del Termidoro An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fruttidoro An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fruttidoro An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'circa Fruttidoro An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'dal Fruttidoro An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'dopo il Fruttidoro An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'prima del Fruttidoro An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 giorni complementari An XII'],
            ['@#DFRENCH R@ COMP 12', 'giorni complementari An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'circa giorni complementari An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'dal giorni complementari An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'dopo il giorni complementari An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'prima del giorni complementari An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'circa 15 Vendemmiaio An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', '15 Vendemmiaio An XII (calcolata)'],
            ['EST @#DFRENCH R@ 15 VEND 12', '15 Vendemmiaio An XII (stimata)'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'prima del 15 Vendemmiaio An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'dopo il 15 Vendemmiaio An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'dal 15 Vendemmiaio An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'fino al 15 Vendemmiaio An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'tra il 15 Vendemmiaio An XII e il 15 Brumaio An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'dal 15 Vendemmiaio An XII al 15 Brumaio An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretato 15 Vendemmiaio An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'circa Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'dal Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'dopo il Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'prima del Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'circa Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'dal Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'dopo il Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'prima del Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi al-Awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi al-Awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'circa Rabi al-Awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'dal Rabi al-Awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'dopo il Rabi al-Awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'prima del Rabi al-Awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi al-Thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi al-Thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'circa Rabi al-Thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'dal Rabi al-Thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'dopo il Rabi al-Thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'prima del Rabi al-Thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-Awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-Awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'circa Jumada al-Awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'dal Jumada al-Awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'dopo il Jumada al-Awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'prima del Jumada al-Awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-Thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-Thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'circa Jumada al-Thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'dal Jumada al-Thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'dopo il Jumada al-Thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'prima del Jumada al-Thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'circa Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'dal Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'dopo il Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'prima del Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Shaaban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Shaaban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'circa Shaaban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'dal Shaaban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'dopo il Shaaban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'prima del Shaaban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'circa Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'dal Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'dopo il Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'prima del Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'circa Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'dal Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'dopo il Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'prima del Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qida 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qida 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'circa Dhu al-Qida 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'dal Dhu al-Qida 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'dopo il Dhu al-Qida 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'prima del Dhu al-Qida 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'circa 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'dal 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'dopo il 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'prima del 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'circa 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425 (calcolata)'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425 (stimata)'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'prima del 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'dopo il 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'dal 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'fino al 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'tra il 15 Muharram 1425 e il 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'dal 15 Muharram 1425 al 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretato 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'circa farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'dal farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'dopo il farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'prima del farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'circa ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'dal ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'dopo il ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'prima del ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'circa khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'dal khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'dopo il khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'prima del khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 tir 1384'],
            ['@#DJALALI@ TIR 1384', 'tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'circa tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'dal tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'dopo il tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'prima del tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'circa mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'dal mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'dopo il mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'prima del mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'circa shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'dal shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'dopo il shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'prima del shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'circa mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'dal mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'dopo il mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'prima del mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'circa aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'dal aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'dopo il aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'prima del aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'circa azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'dal azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'dopo il azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'prima del azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 dey 1384'],
            ['@#DJALALI@ DEY 1384', 'dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'circa dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'dal dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'dopo il dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'prima del dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'circa bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'dal bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'dopo il bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'prima del bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'circa esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'dal esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'dopo il esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'prima del esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'circa 15 farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', '15 farvardin 1384 (calcolata)'],
            ['EST @#DJALALI@ 15 FARVA 1384', '15 farvardin 1384 (stimata)'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'prima del 15 farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'dopo il 15 farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'dal 15 farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'fino al 15 farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'tra il 15 farvardin 1384 e il 15 ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'dal 15 farvardin 1384 al 15 ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretato 15 farvardin 1384'],
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
        self::assertSame('one e two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two e three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one o two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two o three', $language->formatListOr(['one', 'two', 'three']));
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
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
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
        self::assertRelationshipNames('moglie', 'marito', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-marito', 'ex-moglie', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('fidanzata', 'fidanzato', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('madre', 'figlio', [$son, $fm, $wife]);
        self::assertRelationshipNames('padre', 'figlio', [$son, $fm, $husband]);
        self::assertRelationshipNames('madre', 'figlia', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('madre adottiva', 'figlio adottivo', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('padre adottivo', 'figlio adottivo', [$adoptedSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('sorella minore', 'fratello maggiore', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('fratellastro', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('patrigno', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('figliastra', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('suocera', 'genero', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('suocero', 'genero', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('nuora', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('nonna', 'nipote', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('nonno', 'nipote', [$son, $fm, $husband, $fp, $fatherOfH]);

        // Great-grandparents (dynamic) - n-1=2 → "tris"
        self::assertRelationshipName('trisnonno', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('trisnonna', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('zia', 'nipote', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('zio', 'nipote', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('nipote', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nipote', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('cugina', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('cugino', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) - n=2, great(n-1=1) → "bis"
        self::assertRelationshipName('biszia', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('biszio', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
