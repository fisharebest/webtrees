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
use Fisharebest\Webtrees\I18N\Languages\PortugueseBrazil;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PortugueseBrazil::class)]
class PortugueseBrazilTest extends AbstractPortugueseTestCase
{
    protected static function language(): LanguageInterface
    {
        return new PortugueseBrazil();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Latn, self::language()->script());
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
        self::assertSame('pt-BR', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('portugus do Brasil', self::language()->endonym());
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
            ['15 JAN 2000', '15 Janeiro 2000'],
            ['JAN 2000', 'Janeiro 2000'],
            ['ABT JAN 2000', 'por volta de Janeiro 2000'],
            ['FROM JAN 2000', 'de Janeiro 2000'],
            ['AFT JAN 2000', 'depois de Janeiro 2000'],
            ['BEF JAN 2000', 'antes de Janeiro 2000'],
            ['15 FEB 2000', '15 Fevereiro 2000'],
            ['FEB 2000', 'Fevereiro 2000'],
            ['ABT FEB 2000', 'por volta de Fevereiro 2000'],
            ['FROM FEB 2000', 'de Fevereiro 2000'],
            ['AFT FEB 2000', 'depois de Fevereiro 2000'],
            ['BEF FEB 2000', 'antes de Fevereiro 2000'],
            ['15 MAR 2000', '15 Março 2000'],
            ['MAR 2000', 'Março 2000'],
            ['ABT MAR 2000', 'por volta de Março 2000'],
            ['FROM MAR 2000', 'de Março 2000'],
            ['AFT MAR 2000', 'depois de Março 2000'],
            ['BEF MAR 2000', 'antes de Março 2000'],
            ['15 APR 2000', '15 Abril 2000'],
            ['APR 2000', 'Abril 2000'],
            ['ABT APR 2000', 'por volta de Abril 2000'],
            ['FROM APR 2000', 'de Abril 2000'],
            ['AFT APR 2000', 'depois de Abril 2000'],
            ['BEF APR 2000', 'antes de Abril 2000'],
            ['15 MAY 2000', '15 Maio 2000'],
            ['MAY 2000', 'Maio 2000'],
            ['ABT MAY 2000', 'por volta de Maio 2000'],
            ['FROM MAY 2000', 'de Maio 2000'],
            ['AFT MAY 2000', 'depois de Maio 2000'],
            ['BEF MAY 2000', 'antes de Maio 2000'],
            ['15 JUN 2000', '15 Junho 2000'],
            ['JUN 2000', 'Junho 2000'],
            ['ABT JUN 2000', 'por volta de Junho 2000'],
            ['FROM JUN 2000', 'de Junho 2000'],
            ['AFT JUN 2000', 'depois de Junho 2000'],
            ['BEF JUN 2000', 'antes de Junho 2000'],
            ['15 JUL 2000', '15 Julho 2000'],
            ['JUL 2000', 'Julho 2000'],
            ['ABT JUL 2000', 'por volta de Julho 2000'],
            ['FROM JUL 2000', 'de Julho 2000'],
            ['AFT JUL 2000', 'depois de Julho 2000'],
            ['BEF JUL 2000', 'antes de Julho 2000'],
            ['15 AUG 2000', '15 Agosto 2000'],
            ['AUG 2000', 'Agosto 2000'],
            ['ABT AUG 2000', 'por volta de Agosto 2000'],
            ['FROM AUG 2000', 'de Agosto 2000'],
            ['AFT AUG 2000', 'depois de Agosto 2000'],
            ['BEF AUG 2000', 'antes de Agosto 2000'],
            ['15 SEP 2000', '15 Setembro 2000'],
            ['SEP 2000', 'Setembro 2000'],
            ['ABT SEP 2000', 'por volta de Setembro 2000'],
            ['FROM SEP 2000', 'de Setembro 2000'],
            ['AFT SEP 2000', 'depois de Setembro 2000'],
            ['BEF SEP 2000', 'antes de Setembro 2000'],
            ['15 OCT 2000', '15 Outubro 2000'],
            ['OCT 2000', 'Outubro 2000'],
            ['ABT OCT 2000', 'por volta de Outubro 2000'],
            ['FROM OCT 2000', 'de Outubro 2000'],
            ['AFT OCT 2000', 'depois de Outubro 2000'],
            ['BEF OCT 2000', 'antes de Outubro 2000'],
            ['15 NOV 2000', '15 Novembro 2000'],
            ['NOV 2000', 'Novembro 2000'],
            ['ABT NOV 2000', 'por volta de Novembro 2000'],
            ['FROM NOV 2000', 'de Novembro 2000'],
            ['AFT NOV 2000', 'depois de Novembro 2000'],
            ['BEF NOV 2000', 'antes de Novembro 2000'],
            ['15 DEC 2000', '15 Dezembro 2000'],
            ['DEC 2000', 'Dezembro 2000'],
            ['ABT DEC 2000', 'por volta de Dezembro 2000'],
            ['FROM DEC 2000', 'de Dezembro 2000'],
            ['AFT DEC 2000', 'depois de Dezembro 2000'],
            ['BEF DEC 2000', 'antes de Dezembro 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'por volta de 15 Janeiro 2000'],
            ['CAL 15 JAN 2000', 'calculado em 15 Janeiro 2000'],
            ['EST 15 JAN 2000', 'estimado em 15 Janeiro 2000'],
            ['BEF 15 JAN 2000', 'antes de 15 Janeiro 2000'],
            ['AFT 15 JAN 2000', 'depois de 15 Janeiro 2000'],
            ['FROM 15 JAN 2000', 'de 15 Janeiro 2000'],
            ['TO 15 JAN 2000', 'até 15 Janeiro 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'entre 15 Janeiro 2000 e 15 Fevereiro 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'de 15 Janeiro 2000 até 15 Fevereiro 2000'],
            ['INT 15 JAN 2000', 'interpretado em 15 Janeiro 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Janeiro 1700 EC'],
            ['@#DJULIAN@ JAN 1700', 'Janeiro 1700 EC'],
            ['ABT @#DJULIAN@ JAN 1700', 'por volta de Janeiro 1700 EC'],
            ['FROM @#DJULIAN@ JAN 1700', 'de Janeiro 1700 EC'],
            ['AFT @#DJULIAN@ JAN 1700', 'depois de Janeiro 1700 EC'],
            ['BEF @#DJULIAN@ JAN 1700', 'antes de Janeiro 1700 EC'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Fevereiro 1700 EC'],
            ['@#DJULIAN@ FEB 1700', 'Fevereiro 1700 EC'],
            ['ABT @#DJULIAN@ FEB 1700', 'por volta de Fevereiro 1700 EC'],
            ['FROM @#DJULIAN@ FEB 1700', 'de Fevereiro 1700 EC'],
            ['AFT @#DJULIAN@ FEB 1700', 'depois de Fevereiro 1700 EC'],
            ['BEF @#DJULIAN@ FEB 1700', 'antes de Fevereiro 1700 EC'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Março 1700 EC'],
            ['@#DJULIAN@ MAR 1700', 'Março 1700 EC'],
            ['ABT @#DJULIAN@ MAR 1700', 'por volta de Março 1700 EC'],
            ['FROM @#DJULIAN@ MAR 1700', 'de Março 1700 EC'],
            ['AFT @#DJULIAN@ MAR 1700', 'depois de Março 1700 EC'],
            ['BEF @#DJULIAN@ MAR 1700', 'antes de Março 1700 EC'],
            ['@#DJULIAN@ 15 APR 1700', '15 Abril 1700 EC'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Abril 1645/46 EC'],
            ['@#DJULIAN@ APR 1700', 'Abril 1700 EC'],
            ['ABT @#DJULIAN@ APR 1700', 'por volta de Abril 1700 EC'],
            ['FROM @#DJULIAN@ APR 1700', 'de Abril 1700 EC'],
            ['AFT @#DJULIAN@ APR 1700', 'depois de Abril 1700 EC'],
            ['BEF @#DJULIAN@ APR 1700', 'antes de Abril 1700 EC'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Maio 1700 EC'],
            ['@#DJULIAN@ MAY 1700', 'Maio 1700 EC'],
            ['ABT @#DJULIAN@ MAY 1700', 'por volta de Maio 1700 EC'],
            ['FROM @#DJULIAN@ MAY 1700', 'de Maio 1700 EC'],
            ['AFT @#DJULIAN@ MAY 1700', 'depois de Maio 1700 EC'],
            ['BEF @#DJULIAN@ MAY 1700', 'antes de Maio 1700 EC'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Junho 1700 EC'],
            ['@#DJULIAN@ JUN 1700', 'Junho 1700 EC'],
            ['ABT @#DJULIAN@ JUN 1700', 'por volta de Junho 1700 EC'],
            ['FROM @#DJULIAN@ JUN 1700', 'de Junho 1700 EC'],
            ['AFT @#DJULIAN@ JUN 1700', 'depois de Junho 1700 EC'],
            ['BEF @#DJULIAN@ JUN 1700', 'antes de Junho 1700 EC'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Julho 1700 EC'],
            ['@#DJULIAN@ JUL 1700', 'Julho 1700 EC'],
            ['ABT @#DJULIAN@ JUL 1700', 'por volta de Julho 1700 EC'],
            ['FROM @#DJULIAN@ JUL 1700', 'de Julho 1700 EC'],
            ['AFT @#DJULIAN@ JUL 1700', 'depois de Julho 1700 EC'],
            ['BEF @#DJULIAN@ JUL 1700', 'antes de Julho 1700 EC'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Agosto 1700 EC'],
            ['@#DJULIAN@ AUG 1700', 'Agosto 1700 EC'],
            ['ABT @#DJULIAN@ AUG 1700', 'por volta de Agosto 1700 EC'],
            ['FROM @#DJULIAN@ AUG 1700', 'de Agosto 1700 EC'],
            ['AFT @#DJULIAN@ AUG 1700', 'depois de Agosto 1700 EC'],
            ['BEF @#DJULIAN@ AUG 1700', 'antes de Agosto 1700 EC'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Setembro 1700 EC'],
            ['@#DJULIAN@ SEP 1700', 'Setembro 1700 EC'],
            ['ABT @#DJULIAN@ SEP 1700', 'por volta de Setembro 1700 EC'],
            ['FROM @#DJULIAN@ SEP 1700', 'de Setembro 1700 EC'],
            ['AFT @#DJULIAN@ SEP 1700', 'depois de Setembro 1700 EC'],
            ['BEF @#DJULIAN@ SEP 1700', 'antes de Setembro 1700 EC'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Outubro 1700 EC'],
            ['@#DJULIAN@ OCT 1700', 'Outubro 1700 EC'],
            ['ABT @#DJULIAN@ OCT 1700', 'por volta de Outubro 1700 EC'],
            ['FROM @#DJULIAN@ OCT 1700', 'de Outubro 1700 EC'],
            ['AFT @#DJULIAN@ OCT 1700', 'depois de Outubro 1700 EC'],
            ['BEF @#DJULIAN@ OCT 1700', 'antes de Outubro 1700 EC'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Novembro 1700 EC'],
            ['@#DJULIAN@ NOV 1700', 'Novembro 1700 EC'],
            ['ABT @#DJULIAN@ NOV 1700', 'por volta de Novembro 1700 EC'],
            ['FROM @#DJULIAN@ NOV 1700', 'de Novembro 1700 EC'],
            ['AFT @#DJULIAN@ NOV 1700', 'depois de Novembro 1700 EC'],
            ['BEF @#DJULIAN@ NOV 1700', 'antes de Novembro 1700 EC'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Dezembro 1700 EC'],
            ['@#DJULIAN@ DEC 1700', 'Dezembro 1700 EC'],
            ['ABT @#DJULIAN@ DEC 1700', 'por volta de Dezembro 1700 EC'],
            ['FROM @#DJULIAN@ DEC 1700', 'de Dezembro 1700 EC'],
            ['AFT @#DJULIAN@ DEC 1700', 'depois de Dezembro 1700 EC'],
            ['BEF @#DJULIAN@ DEC 1700', 'antes de Dezembro 1700 EC'],
            ['@#DJULIAN@ 1700', '1700 EC'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'por volta de 15 Janeiro 1700 EC'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculado em 15 Janeiro 1700 EC'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimado em 15 Janeiro 1700 EC'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'antes de 15 Janeiro 1700 EC'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'depois de 15 Janeiro 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'de 15 Janeiro 1700 EC'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'até 15 Janeiro 1700 EC'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'entre 15 Janeiro 1700 EC e 15 Fevereiro 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'de 15 Janeiro 1700 EC até 15 Fevereiro 1700 EC'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretado em 15 Janeiro 1700 EC'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'por volta de Tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'de Tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'depois de Tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'antes de Tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Cheshvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'Cheshvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'por volta de Cheshvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'de Cheshvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'depois de Cheshvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'antes de Cheshvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'por volta de Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'de Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'depois de Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'antes de Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'Tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'por volta de Tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'de Tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'depois de Tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'antes de Tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'por volta de Shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'de Shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'depois de Shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'antes de Shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'por volta de Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'de Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'depois de Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'antes de Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'por volta de Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'de Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'depois de Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'antes de Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'por volta de Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'de Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'depois de Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'antes de Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'por volta de Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'de Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'depois de Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'antes de Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'por volta de Sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'de Sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'depois de Sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'antes de Sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'por volta de Tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'de Tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'depois de Tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'antes de Tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'por volta de Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'de Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'depois de Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'antes de Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'por volta de Elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'de Elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'depois de Elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'antes de Elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'por volta de 15 Tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculado em 15 Tishrei 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimado em 15 Tishrei 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'antes de 15 Tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'depois de 15 Tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'de 15 Tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'até 15 Tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'entre 15 Tishrei 5765 e 15 Cheshvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'de 15 Tishrei 5765 até 15 Cheshvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretado em 15 Tishrei 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Vindimiário An XII'],
            ['@#DFRENCH R@ VEND 12', 'Vindimiário An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'por volta de Vindimiário An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'de Vindimiário An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'depois de Vindimiário An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'antes de Vindimiário An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Brumário An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Brumário An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'por volta de Brumário An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'de Brumário An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'depois de Brumário An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'antes de Brumário An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Frimário An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Frimário An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'por volta de Frimário An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'de Frimário An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'depois de Frimário An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'antes de Frimário An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Nivoso An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Nivoso An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'por volta de Nivoso An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'de Nivoso An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'depois de Nivoso An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'antes de Nivoso An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Pluvioso An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Pluvioso An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'por volta de Pluvioso An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'de Pluvioso An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'depois de Pluvioso An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'antes de Pluvioso An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Ventoso An XII'],
            ['@#DFRENCH R@ VENT 12', 'Ventoso An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'por volta de Ventoso An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'de Ventoso An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'depois de Ventoso An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'antes de Ventoso An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'Germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'por volta de Germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'de Germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'depois de Germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'antes de Germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Florial An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Florial An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'por volta de Florial An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'de Florial An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'depois de Florial An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'antes de Florial An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Pradial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Pradial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'por volta de Pradial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'de Pradial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'depois de Pradial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'antes de Pradial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'Messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'por volta de Messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'de Messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'depois de Messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'antes de Messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Termidor An XII'],
            ['@#DFRENCH R@ THER 12', 'Termidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'por volta de Termidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'de Termidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'depois de Termidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'antes de Termidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'por volta de Fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'de Fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'depois de Fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'antes de Fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 dias complementares An XII'],
            ['@#DFRENCH R@ COMP 12', 'dias complementares An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'por volta de dias complementares An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'de dias complementares An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'depois de dias complementares An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'antes de dias complementares An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'por volta de 15 Vindimiário An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculado em 15 Vindimiário An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimado em 15 Vindimiário An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'antes de 15 Vindimiário An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'depois de 15 Vindimiário An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'de 15 Vindimiário An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'até 15 Vindimiário An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'entre 15 Vindimiário An XII e 15 Brumário An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'de 15 Vindimiário An XII até 15 Brumário An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretado em 15 Vindimiário An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'por volta de Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'de Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'depois de Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'antes de Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'por volta de Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'de Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'depois de Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'antes de Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'por volta de Rabi al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'de Rabi al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'depois de Rabi al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'antes de Rabi al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'por volta de Rabi al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'de Rabi al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'depois de Rabi al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'antes de Rabi al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada al-awwal 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada al-awwal 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'por volta de Jumada al-awwal 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'de Jumada al-awwal 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'depois de Jumada al-awwal 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'antes de Jumada al-awwal 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada al-thani 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada al-thani 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'por volta de Jumada al-thani 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'de Jumada al-thani 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'depois de Jumada al-thani 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'antes de Jumada al-thani 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'por volta de Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'de Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'depois de Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'antes de Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Sha’aban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Sha’aban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'por volta de Sha’aban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'de Sha’aban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'depois de Sha’aban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'antes de Sha’aban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'por volta de Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'de Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'depois de Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'antes de Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'por volta de Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'de Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'depois de Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'antes de Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhu al-Qi’dah 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'por volta de Dhu al-Qi’dah 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'de Dhu al-Qi’dah 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'depois de Dhu al-Qi’dah 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'antes de Dhu al-Qi’dah 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'por volta de 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'de 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'depois de 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'antes de 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'por volta de 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculado em 15 Muharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimado em 15 Muharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'antes de 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'depois de 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'de 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'até 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'entre 15 Muharram 1425 e 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'de 15 Muharram 1425 até 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretado em 15 Muharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'por volta de Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'de Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'depois de Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'antes de Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'por volta de Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'de Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'depois de Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'antes de Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'por volta de Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'de Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'depois de Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'antes de Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'por volta de Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'de Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'depois de Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'antes de Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'por volta de Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'de Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'depois de Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'antes de Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'por volta de Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'de Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'depois de Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'antes de Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'por volta de Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'de Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'depois de Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'antes de Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'por volta de Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'de Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'depois de Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'antes de Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'por volta de Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'de Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'depois de Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'antes de Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'por volta de Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'de Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'depois de Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'antes de Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'por volta de Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'de Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'depois de Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'antes de Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'por volta de Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'de Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'depois de Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'antes de Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'por volta de 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculado em 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimado em 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'antes de 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'depois de 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'de 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'até 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'entre 15 Farvardin 1384 e 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'de 15 Farvardin 1384 até 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretado em 15 Farvardin 1384'],
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
        self::assertSame('one, two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two, three', $language->formatListOr(['one', 'two', 'three']));
    }
}
