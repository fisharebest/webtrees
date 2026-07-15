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
use Fisharebest\Webtrees\I18N\Languages\Catalan;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Catalan::class)]
class CatalanTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Catalan();
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
        self::assertSame('ca', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('catal', self::language()->endonym());
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




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 Gener 2000'],
            ['JAN 2000', 'Gener 2000'],
            ['ABT JAN 2000', 'sobre Gener 2000'],
            ['FROM JAN 2000', 'des de Gener 2000'],
            ['AFT JAN 2000', 'després de Gener 2000'],
            ['BEF JAN 2000', 'abans de Gener 2000'],
            ['15 FEB 2000', '15 Febrer 2000'],
            ['FEB 2000', 'Febrer 2000'],
            ['ABT FEB 2000', 'sobre Febrer 2000'],
            ['FROM FEB 2000', 'des de Febrer 2000'],
            ['AFT FEB 2000', 'després de Febrer 2000'],
            ['BEF FEB 2000', 'abans de Febrer 2000'],
            ['15 MAR 2000', '15 Març 2000'],
            ['MAR 2000', 'Març 2000'],
            ['ABT MAR 2000', 'sobre Març 2000'],
            ['FROM MAR 2000', 'des de Març 2000'],
            ['AFT MAR 2000', 'després de Març 2000'],
            ['BEF MAR 2000', 'abans de Març 2000'],
            ['15 APR 2000', '15 Abril 2000'],
            ['APR 2000', 'Abril 2000'],
            ['ABT APR 2000', 'sobre Abril 2000'],
            ['FROM APR 2000', 'des de Abril 2000'],
            ['AFT APR 2000', 'després de Abril 2000'],
            ['BEF APR 2000', 'abans de Abril 2000'],
            ['15 MAY 2000', '15 Maig 2000'],
            ['MAY 2000', 'Maig 2000'],
            ['ABT MAY 2000', 'sobre Maig 2000'],
            ['FROM MAY 2000', 'des de Maig 2000'],
            ['AFT MAY 2000', 'després de Maig 2000'],
            ['BEF MAY 2000', 'abans de Maig 2000'],
            ['15 JUN 2000', '15 Juny 2000'],
            ['JUN 2000', 'Juny 2000'],
            ['ABT JUN 2000', 'sobre Juny 2000'],
            ['FROM JUN 2000', 'des de Juny 2000'],
            ['AFT JUN 2000', 'després de Juny 2000'],
            ['BEF JUN 2000', 'abans de Juny 2000'],
            ['15 JUL 2000', '15 Juliol 2000'],
            ['JUL 2000', 'Juliol 2000'],
            ['ABT JUL 2000', 'sobre Juliol 2000'],
            ['FROM JUL 2000', 'des de Juliol 2000'],
            ['AFT JUL 2000', 'després de Juliol 2000'],
            ['BEF JUL 2000', 'abans de Juliol 2000'],
            ['15 AUG 2000', '15 Agost 2000'],
            ['AUG 2000', 'Agost 2000'],
            ['ABT AUG 2000', 'sobre Agost 2000'],
            ['FROM AUG 2000', 'des de Agost 2000'],
            ['AFT AUG 2000', 'després de Agost 2000'],
            ['BEF AUG 2000', 'abans de Agost 2000'],
            ['15 SEP 2000', '15 Setembre 2000'],
            ['SEP 2000', 'Setembre 2000'],
            ['ABT SEP 2000', 'sobre Setembre 2000'],
            ['FROM SEP 2000', 'des de Setembre 2000'],
            ['AFT SEP 2000', 'després de Setembre 2000'],
            ['BEF SEP 2000', 'abans de Setembre 2000'],
            ['15 OCT 2000', '15 Octubre 2000'],
            ['OCT 2000', 'Octubre 2000'],
            ['ABT OCT 2000', 'sobre Octubre 2000'],
            ['FROM OCT 2000', 'des de Octubre 2000'],
            ['AFT OCT 2000', 'després de Octubre 2000'],
            ['BEF OCT 2000', 'abans de Octubre 2000'],
            ['15 NOV 2000', '15 Novembre 2000'],
            ['NOV 2000', 'Novembre 2000'],
            ['ABT NOV 2000', 'sobre Novembre 2000'],
            ['FROM NOV 2000', 'des de Novembre 2000'],
            ['AFT NOV 2000', 'després de Novembre 2000'],
            ['BEF NOV 2000', 'abans de Novembre 2000'],
            ['15 DEC 2000', '15 Desembre 2000'],
            ['DEC 2000', 'Desembre 2000'],
            ['ABT DEC 2000', 'sobre Desembre 2000'],
            ['FROM DEC 2000', 'des de Desembre 2000'],
            ['AFT DEC 2000', 'després de Desembre 2000'],
            ['BEF DEC 2000', 'abans de Desembre 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'sobre 15 Gener 2000'],
            ['CAL 15 JAN 2000', 'calculat 15 Gener 2000'],
            ['EST 15 JAN 2000', 'estimat 15 Gener 2000'],
            ['BEF 15 JAN 2000', 'abans de 15 Gener 2000'],
            ['AFT 15 JAN 2000', 'després de 15 Gener 2000'],
            ['FROM 15 JAN 2000', 'des de 15 Gener 2000'],
            ['TO 15 JAN 2000', 'a 15 Gener 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'entre 15 Gener 2000 i 15 Febrer 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'de 15 Gener 2000 a 15 Febrer 2000'],
            ['INT 15 JAN 2000', 'interpretat 15 Gener 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Gener 1700 EC'],
            ['@#DJULIAN@ JAN 1700', 'Gener 1700 EC'],
            ['ABT @#DJULIAN@ JAN 1700', 'sobre Gener 1700 EC'],
            ['FROM @#DJULIAN@ JAN 1700', 'des de Gener 1700 EC'],
            ['AFT @#DJULIAN@ JAN 1700', 'després de Gener 1700 EC'],
            ['BEF @#DJULIAN@ JAN 1700', 'abans de Gener 1700 EC'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Febrer 1700 EC'],
            ['@#DJULIAN@ FEB 1700', 'Febrer 1700 EC'],
            ['ABT @#DJULIAN@ FEB 1700', 'sobre Febrer 1700 EC'],
            ['FROM @#DJULIAN@ FEB 1700', 'des de Febrer 1700 EC'],
            ['AFT @#DJULIAN@ FEB 1700', 'després de Febrer 1700 EC'],
            ['BEF @#DJULIAN@ FEB 1700', 'abans de Febrer 1700 EC'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Març 1700 EC'],
            ['@#DJULIAN@ MAR 1700', 'Març 1700 EC'],
            ['ABT @#DJULIAN@ MAR 1700', 'sobre Març 1700 EC'],
            ['FROM @#DJULIAN@ MAR 1700', 'des de Març 1700 EC'],
            ['AFT @#DJULIAN@ MAR 1700', 'després de Març 1700 EC'],
            ['BEF @#DJULIAN@ MAR 1700', 'abans de Març 1700 EC'],
            ['@#DJULIAN@ 15 APR 1700', '15 Abril 1700 EC'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Abril 1645/46 EC'],
            ['@#DJULIAN@ APR 1700', 'Abril 1700 EC'],
            ['ABT @#DJULIAN@ APR 1700', 'sobre Abril 1700 EC'],
            ['FROM @#DJULIAN@ APR 1700', 'des de Abril 1700 EC'],
            ['AFT @#DJULIAN@ APR 1700', 'després de Abril 1700 EC'],
            ['BEF @#DJULIAN@ APR 1700', 'abans de Abril 1700 EC'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Maig 1700 EC'],
            ['@#DJULIAN@ MAY 1700', 'Maig 1700 EC'],
            ['ABT @#DJULIAN@ MAY 1700', 'sobre Maig 1700 EC'],
            ['FROM @#DJULIAN@ MAY 1700', 'des de Maig 1700 EC'],
            ['AFT @#DJULIAN@ MAY 1700', 'després de Maig 1700 EC'],
            ['BEF @#DJULIAN@ MAY 1700', 'abans de Maig 1700 EC'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Juny 1700 EC'],
            ['@#DJULIAN@ JUN 1700', 'Juny 1700 EC'],
            ['ABT @#DJULIAN@ JUN 1700', 'sobre Juny 1700 EC'],
            ['FROM @#DJULIAN@ JUN 1700', 'des de Juny 1700 EC'],
            ['AFT @#DJULIAN@ JUN 1700', 'després de Juny 1700 EC'],
            ['BEF @#DJULIAN@ JUN 1700', 'abans de Juny 1700 EC'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Juliol 1700 EC'],
            ['@#DJULIAN@ JUL 1700', 'Juliol 1700 EC'],
            ['ABT @#DJULIAN@ JUL 1700', 'sobre Juliol 1700 EC'],
            ['FROM @#DJULIAN@ JUL 1700', 'des de Juliol 1700 EC'],
            ['AFT @#DJULIAN@ JUL 1700', 'després de Juliol 1700 EC'],
            ['BEF @#DJULIAN@ JUL 1700', 'abans de Juliol 1700 EC'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Agost 1700 EC'],
            ['@#DJULIAN@ AUG 1700', 'Agost 1700 EC'],
            ['ABT @#DJULIAN@ AUG 1700', 'sobre Agost 1700 EC'],
            ['FROM @#DJULIAN@ AUG 1700', 'des de Agost 1700 EC'],
            ['AFT @#DJULIAN@ AUG 1700', 'després de Agost 1700 EC'],
            ['BEF @#DJULIAN@ AUG 1700', 'abans de Agost 1700 EC'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Setembre 1700 EC'],
            ['@#DJULIAN@ SEP 1700', 'Setembre 1700 EC'],
            ['ABT @#DJULIAN@ SEP 1700', 'sobre Setembre 1700 EC'],
            ['FROM @#DJULIAN@ SEP 1700', 'des de Setembre 1700 EC'],
            ['AFT @#DJULIAN@ SEP 1700', 'després de Setembre 1700 EC'],
            ['BEF @#DJULIAN@ SEP 1700', 'abans de Setembre 1700 EC'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Octubre 1700 EC'],
            ['@#DJULIAN@ OCT 1700', 'Octubre 1700 EC'],
            ['ABT @#DJULIAN@ OCT 1700', 'sobre Octubre 1700 EC'],
            ['FROM @#DJULIAN@ OCT 1700', 'des de Octubre 1700 EC'],
            ['AFT @#DJULIAN@ OCT 1700', 'després de Octubre 1700 EC'],
            ['BEF @#DJULIAN@ OCT 1700', 'abans de Octubre 1700 EC'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Novembre 1700 EC'],
            ['@#DJULIAN@ NOV 1700', 'Novembre 1700 EC'],
            ['ABT @#DJULIAN@ NOV 1700', 'sobre Novembre 1700 EC'],
            ['FROM @#DJULIAN@ NOV 1700', 'des de Novembre 1700 EC'],
            ['AFT @#DJULIAN@ NOV 1700', 'després de Novembre 1700 EC'],
            ['BEF @#DJULIAN@ NOV 1700', 'abans de Novembre 1700 EC'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Desembre 1700 EC'],
            ['@#DJULIAN@ DEC 1700', 'Desembre 1700 EC'],
            ['ABT @#DJULIAN@ DEC 1700', 'sobre Desembre 1700 EC'],
            ['FROM @#DJULIAN@ DEC 1700', 'des de Desembre 1700 EC'],
            ['AFT @#DJULIAN@ DEC 1700', 'després de Desembre 1700 EC'],
            ['BEF @#DJULIAN@ DEC 1700', 'abans de Desembre 1700 EC'],
            ['@#DJULIAN@ 1700', '1700 EC'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'sobre 15 Gener 1700 EC'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculat 15 Gener 1700 EC'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimat 15 Gener 1700 EC'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'abans de 15 Gener 1700 EC'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'després de 15 Gener 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'des de 15 Gener 1700 EC'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'a 15 Gener 1700 EC'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'entre 15 Gener 1700 EC i 15 Febrer 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'de 15 Gener 1700 EC a 15 Febrer 1700 EC'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretat 15 Gener 1700 EC'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tixrí 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tixrí 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'sobre Tixrí 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'des de Tixrí 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'després de Tixrí 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'abans de Tixrí 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Heixvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Heixvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'sobre Heixvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'des de Heixvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'després de Heixvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'abans de Heixvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Quisleu 5765'],
            ['@#DHEBREW@ KSL 5765', 'Quisleu 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'sobre Quisleu 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'des de Quisleu 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'després de Quisleu 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'abans de Quisleu 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'sobre Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'des de Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'després de Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'abans de Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Xevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Xevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'sobre Xevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'des de Xevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'després de Xevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'abans de Xevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'sobre Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'des de Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'després de Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'abans de Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'sobre Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'des de Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'després de Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'abans de Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'sobre Nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'des de Nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'després de Nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'abans de Nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'sobre Iar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'des de Iar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'després de Iar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'abans de Iar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'sobre Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'des de Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'després de Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'abans de Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tammuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tammuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'sobre Tammuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'des de Tammuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'després de Tammuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'abans de Tammuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'sobre Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'des de Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'després de Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'abans de Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'sobre Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'des de Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'després de Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'abans de Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'sobre 15 Tixrí 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculat 15 Tixrí 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimat 15 Tixrí 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'abans de 15 Tixrí 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'després de 15 Tixrí 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'des de 15 Tixrí 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'a 15 Tixrí 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'entre 15 Tixrí 5765 i 15 Heixvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'de 15 Tixrí 5765 a 15 Heixvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretat 15 Tixrí 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vendemiari An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vendemiari An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'sobre Vendemiari An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'des de Vendemiari An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'després de Vendemiari An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'abans de Vendemiari An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Bromari An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Bromari An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'sobre Bromari An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'des de Bromari An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'després de Bromari An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'abans de Bromari An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Rufolari An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Rufolari An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'sobre Rufolari An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'des de Rufolari An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'després de Rufolari An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'abans de Rufolari An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivós An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivós An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'sobre Nivós An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'des de Nivós An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'després de Nivós An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'abans de Nivós An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluviós An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluviós An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'sobre Pluviós An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'des de Pluviós An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'després de Pluviós An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'abans de Pluviós An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventós An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventós An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'sobre Ventós An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'des de Ventós An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'després de Ventós An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'abans de Ventós An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'sobre Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'des de Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'després de Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'abans de Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Floral An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Floral An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'sobre Floral An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'des de Floral An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'després de Floral An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'abans de Floral An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Pradal An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Pradal An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'sobre Pradal An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'des de Pradal An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'després de Pradal An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'abans de Pradal An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'sobre Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'des de Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'després de Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'abans de Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Termidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Termidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'sobre Termidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'des de Termidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'després de Termidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'abans de Termidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'sobre Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'des de Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'després de Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'abans de Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 dies complementaris An XII'],
            ['@#DFRENCH R@ COMP 12', 'dies complementaris An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'sobre dies complementaris An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'des de dies complementaris An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'després de dies complementaris An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'abans de dies complementaris An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'sobre 15 Vendemiari An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculat 15 Vendemiari An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimat 15 Vendemiari An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'abans de 15 Vendemiari An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'després de 15 Vendemiari An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'des de 15 Vendemiari An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'a 15 Vendemiari An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'entre 15 Vendemiari An XII i 15 Bromari An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'de 15 Vendemiari An XII a 15 Bromari An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretat 15 Vendemiari An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muhàrram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muhàrram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'sobre Muhàrram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'des de Muhàrram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'després de Muhàrram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'abans de Muhàrram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Sàfar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Sàfar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'sobre Sàfar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'des de Sàfar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'després de Sàfar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'abans de Sàfar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'sobre Rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'des de Rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'després de Rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'abans de Rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'sobre Rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'des de Rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'després de Rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'abans de Rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-ula 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-ula 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'sobre Jumada al-ula 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'des de Jumada al-ula 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'després de Jumada al-ula 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'abans de Jumada al-ula 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-àkhira 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-àkhira 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'sobre Jumada al-àkhira 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'des de Jumada al-àkhira 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'després de Jumada al-àkhira 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'abans de Jumada al-àkhira 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Ràjab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Ràjab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'sobre Ràjab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'des de Ràjab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'després de Ràjab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'abans de Ràjab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Xaban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Xaban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'sobre Xaban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'des de Xaban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'després de Xaban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'abans de Xaban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadà 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadà 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'sobre Ramadà 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'des de Ramadà 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'després de Ramadà 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'abans de Ramadà 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Xawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Xawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'sobre Xawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'des de Xawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'després de Xawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'abans de Xawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu-l-qada 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu-l-qada 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'sobre Dhu-l-qada 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'des de Dhu-l-qada 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'després de Dhu-l-qada 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'abans de Dhu-l-qada 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'sobre 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'des de 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'després de 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'abans de 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'sobre 15 Muhàrram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculat 15 Muhàrram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimat 15 Muhàrram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'abans de 15 Muhàrram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'després de 15 Muhàrram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'des de 15 Muhàrram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'a 15 Muhàrram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'entre 15 Muhàrram 1425 i 15 Sàfar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'de 15 Muhàrram 1425 a 15 Sàfar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretat 15 Muhàrram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardín 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardín 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'sobre Farvardín 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'des de Farvardín 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'després de Farvardín 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'abans de Farvardín 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'sobre Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'des de Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'després de Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'abans de Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'sobre Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'des de Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'després de Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'abans de Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'sobre Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'des de Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'després de Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'abans de Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'sobre Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'des de Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'després de Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'abans de Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'sobre Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'des de Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'després de Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'abans de Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'sobre Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'des de Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'després de Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'abans de Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'sobre Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'des de Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'després de Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'abans de Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'sobre Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'des de Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'després de Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'abans de Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'sobre Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'des de Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'després de Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'abans de Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'sobre Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'des de Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'després de Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'abans de Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'sobre Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'des de Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'després de Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'abans de Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'sobre 15 Farvardín 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculat 15 Farvardín 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimat 15 Farvardín 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'abans de 15 Farvardín 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'després de 15 Farvardín 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'des de 15 Farvardín 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'a 15 Farvardín 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'entre 15 Farvardín 1384 i 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'de 15 Farvardín 1384 a 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretat 15 Farvardín 1384'],
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
        self::assertSame('one i two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two i three', $language->formatListAnd(['one', 'two', 'three']));

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
        $child = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fosterSon = self::male('fs', "1 FAMC @fd@\n2 PEDI foster");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");
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

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fs@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $stepDaughter, $fosterSon,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('esposa', 'espòs', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-espòs', 'ex-esposa', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('promesa', 'promès', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mare', 'fill', [$son, $fm, $wife]);
        self::assertRelationshipNames('pare', 'fill', [$son, $fm, $husband]);
        self::assertRelationshipNames('mare', 'filla', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('mare adoptiva', 'fill adoptiu', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('pare adoptiu', 'fill adoptiu', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames("mare d'acollida", "fill d'acollida", [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames("pare d'acollida", "fill d'acollida", [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('germana petita', 'germà gran', [$son, $fm, $daughter]);
        self::assertRelationshipNames('germà gran', 'germana petita', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipNames('germanastre', 'germanastra', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('padrastre', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('fillastra', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('sogra', 'gendre', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('sogre', 'gendre', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('nora', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('àvia', 'nét', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('avi', 'nét', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('néta', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('besavi', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('besàvia', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tia', 'nebot', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('oncle', 'nebot', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('neboda', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('nebot', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('cosina', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('cosí', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('bestia', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('besoncle', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
