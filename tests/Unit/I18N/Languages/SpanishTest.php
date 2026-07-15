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
use Fisharebest\Webtrees\I18N\Languages\Spanish;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Spanish::class)]
class SpanishTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Spanish();
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
        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('es', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('espaol', self::language()->endonym());
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
            ['15 JAN 2000', '15 enero 2000'],
            ['JAN 2000', 'enero 2000'],
            ['ABT JAN 2000', 'sobre enero 2000'],
            ['FROM JAN 2000', 'desde enero 2000'],
            ['AFT JAN 2000', 'después de enero 2000'],
            ['BEF JAN 2000', 'antes de enero 2000'],
            ['15 FEB 2000', '15 febrero 2000'],
            ['FEB 2000', 'febrero 2000'],
            ['ABT FEB 2000', 'sobre febrero 2000'],
            ['FROM FEB 2000', 'desde febrero 2000'],
            ['AFT FEB 2000', 'después de febrero 2000'],
            ['BEF FEB 2000', 'antes de febrero 2000'],
            ['15 MAR 2000', '15 marzo 2000'],
            ['MAR 2000', 'marzo 2000'],
            ['ABT MAR 2000', 'sobre marzo 2000'],
            ['FROM MAR 2000', 'desde marzo 2000'],
            ['AFT MAR 2000', 'después de marzo 2000'],
            ['BEF MAR 2000', 'antes de marzo 2000'],
            ['15 APR 2000', '15 abril 2000'],
            ['APR 2000', 'abril 2000'],
            ['ABT APR 2000', 'sobre abril 2000'],
            ['FROM APR 2000', 'desde abril 2000'],
            ['AFT APR 2000', 'después de abril 2000'],
            ['BEF APR 2000', 'antes de abril 2000'],
            ['15 MAY 2000', '15 mayo 2000'],
            ['MAY 2000', 'mayo 2000'],
            ['ABT MAY 2000', 'sobre mayo 2000'],
            ['FROM MAY 2000', 'desde mayo 2000'],
            ['AFT MAY 2000', 'después de mayo 2000'],
            ['BEF MAY 2000', 'antes de mayo 2000'],
            ['15 JUN 2000', '15 junio 2000'],
            ['JUN 2000', 'junio 2000'],
            ['ABT JUN 2000', 'sobre junio 2000'],
            ['FROM JUN 2000', 'desde junio 2000'],
            ['AFT JUN 2000', 'después de junio 2000'],
            ['BEF JUN 2000', 'antes de junio 2000'],
            ['15 JUL 2000', '15 julio 2000'],
            ['JUL 2000', 'julio 2000'],
            ['ABT JUL 2000', 'sobre julio 2000'],
            ['FROM JUL 2000', 'desde julio 2000'],
            ['AFT JUL 2000', 'después de julio 2000'],
            ['BEF JUL 2000', 'antes de julio 2000'],
            ['15 AUG 2000', '15 agosto 2000'],
            ['AUG 2000', 'agosto 2000'],
            ['ABT AUG 2000', 'sobre agosto 2000'],
            ['FROM AUG 2000', 'desde agosto 2000'],
            ['AFT AUG 2000', 'después de agosto 2000'],
            ['BEF AUG 2000', 'antes de agosto 2000'],
            ['15 SEP 2000', '15 septiembre 2000'],
            ['SEP 2000', 'septiembre 2000'],
            ['ABT SEP 2000', 'sobre septiembre 2000'],
            ['FROM SEP 2000', 'desde septiembre 2000'],
            ['AFT SEP 2000', 'después de septiembre 2000'],
            ['BEF SEP 2000', 'antes de septiembre 2000'],
            ['15 OCT 2000', '15 octubre 2000'],
            ['OCT 2000', 'octubre 2000'],
            ['ABT OCT 2000', 'sobre octubre 2000'],
            ['FROM OCT 2000', 'desde octubre 2000'],
            ['AFT OCT 2000', 'después de octubre 2000'],
            ['BEF OCT 2000', 'antes de octubre 2000'],
            ['15 NOV 2000', '15 noviembre 2000'],
            ['NOV 2000', 'noviembre 2000'],
            ['ABT NOV 2000', 'sobre noviembre 2000'],
            ['FROM NOV 2000', 'desde noviembre 2000'],
            ['AFT NOV 2000', 'después de noviembre 2000'],
            ['BEF NOV 2000', 'antes de noviembre 2000'],
            ['15 DEC 2000', '15 diciembre 2000'],
            ['DEC 2000', 'diciembre 2000'],
            ['ABT DEC 2000', 'sobre diciembre 2000'],
            ['FROM DEC 2000', 'desde diciembre 2000'],
            ['AFT DEC 2000', 'después de diciembre 2000'],
            ['BEF DEC 2000', 'antes de diciembre 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'sobre 15 enero 2000'],
            ['CAL 15 JAN 2000', '15 enero 2000 calculadas'],
            ['EST 15 JAN 2000', '15 enero 2000 estimadas'],
            ['BEF 15 JAN 2000', 'antes de 15 enero 2000'],
            ['AFT 15 JAN 2000', 'después de 15 enero 2000'],
            ['FROM 15 JAN 2000', 'desde 15 enero 2000'],
            ['TO 15 JAN 2000', 'hasta 15 enero 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'entre 15 enero 2000 y 15 febrero 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'desde 15 enero 2000 hasta 15 febrero 2000'],
            ['INT 15 JAN 2000', '15 enero 2000 interpretadas'],
            ['@#DJULIAN@ 15 JAN 1700', '15 enero 1700 EC'],
            ['@#DJULIAN@ JAN 1700', 'enero 1700 EC'],
            ['ABT @#DJULIAN@ JAN 1700', 'sobre enero 1700 EC'],
            ['FROM @#DJULIAN@ JAN 1700', 'desde enero 1700 EC'],
            ['AFT @#DJULIAN@ JAN 1700', 'después de enero 1700 EC'],
            ['BEF @#DJULIAN@ JAN 1700', 'antes de enero 1700 EC'],
            ['@#DJULIAN@ 15 FEB 1700', '15 febrero 1700 EC'],
            ['@#DJULIAN@ FEB 1700', 'febrero 1700 EC'],
            ['ABT @#DJULIAN@ FEB 1700', 'sobre febrero 1700 EC'],
            ['FROM @#DJULIAN@ FEB 1700', 'desde febrero 1700 EC'],
            ['AFT @#DJULIAN@ FEB 1700', 'después de febrero 1700 EC'],
            ['BEF @#DJULIAN@ FEB 1700', 'antes de febrero 1700 EC'],
            ['@#DJULIAN@ 15 MAR 1700', '15 marzo 1700 EC'],
            ['@#DJULIAN@ MAR 1700', 'marzo 1700 EC'],
            ['ABT @#DJULIAN@ MAR 1700', 'sobre marzo 1700 EC'],
            ['FROM @#DJULIAN@ MAR 1700', 'desde marzo 1700 EC'],
            ['AFT @#DJULIAN@ MAR 1700', 'después de marzo 1700 EC'],
            ['BEF @#DJULIAN@ MAR 1700', 'antes de marzo 1700 EC'],
            ['@#DJULIAN@ 15 APR 1700', '15 abril 1700 EC'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 abril 1645/46 EC'],
            ['@#DJULIAN@ APR 1700', 'abril 1700 EC'],
            ['ABT @#DJULIAN@ APR 1700', 'sobre abril 1700 EC'],
            ['FROM @#DJULIAN@ APR 1700', 'desde abril 1700 EC'],
            ['AFT @#DJULIAN@ APR 1700', 'después de abril 1700 EC'],
            ['BEF @#DJULIAN@ APR 1700', 'antes de abril 1700 EC'],
            ['@#DJULIAN@ 15 MAY 1700', '15 mayo 1700 EC'],
            ['@#DJULIAN@ MAY 1700', 'mayo 1700 EC'],
            ['ABT @#DJULIAN@ MAY 1700', 'sobre mayo 1700 EC'],
            ['FROM @#DJULIAN@ MAY 1700', 'desde mayo 1700 EC'],
            ['AFT @#DJULIAN@ MAY 1700', 'después de mayo 1700 EC'],
            ['BEF @#DJULIAN@ MAY 1700', 'antes de mayo 1700 EC'],
            ['@#DJULIAN@ 15 JUN 1700', '15 junio 1700 EC'],
            ['@#DJULIAN@ JUN 1700', 'junio 1700 EC'],
            ['ABT @#DJULIAN@ JUN 1700', 'sobre junio 1700 EC'],
            ['FROM @#DJULIAN@ JUN 1700', 'desde junio 1700 EC'],
            ['AFT @#DJULIAN@ JUN 1700', 'después de junio 1700 EC'],
            ['BEF @#DJULIAN@ JUN 1700', 'antes de junio 1700 EC'],
            ['@#DJULIAN@ 15 JUL 1700', '15 julio 1700 EC'],
            ['@#DJULIAN@ JUL 1700', 'julio 1700 EC'],
            ['ABT @#DJULIAN@ JUL 1700', 'sobre julio 1700 EC'],
            ['FROM @#DJULIAN@ JUL 1700', 'desde julio 1700 EC'],
            ['AFT @#DJULIAN@ JUL 1700', 'después de julio 1700 EC'],
            ['BEF @#DJULIAN@ JUL 1700', 'antes de julio 1700 EC'],
            ['@#DJULIAN@ 15 AUG 1700', '15 agosto 1700 EC'],
            ['@#DJULIAN@ AUG 1700', 'agosto 1700 EC'],
            ['ABT @#DJULIAN@ AUG 1700', 'sobre agosto 1700 EC'],
            ['FROM @#DJULIAN@ AUG 1700', 'desde agosto 1700 EC'],
            ['AFT @#DJULIAN@ AUG 1700', 'después de agosto 1700 EC'],
            ['BEF @#DJULIAN@ AUG 1700', 'antes de agosto 1700 EC'],
            ['@#DJULIAN@ 15 SEP 1700', '15 septiembre 1700 EC'],
            ['@#DJULIAN@ SEP 1700', 'septiembre 1700 EC'],
            ['ABT @#DJULIAN@ SEP 1700', 'sobre septiembre 1700 EC'],
            ['FROM @#DJULIAN@ SEP 1700', 'desde septiembre 1700 EC'],
            ['AFT @#DJULIAN@ SEP 1700', 'después de septiembre 1700 EC'],
            ['BEF @#DJULIAN@ SEP 1700', 'antes de septiembre 1700 EC'],
            ['@#DJULIAN@ 15 OCT 1700', '15 octubre 1700 EC'],
            ['@#DJULIAN@ OCT 1700', 'octubre 1700 EC'],
            ['ABT @#DJULIAN@ OCT 1700', 'sobre octubre 1700 EC'],
            ['FROM @#DJULIAN@ OCT 1700', 'desde octubre 1700 EC'],
            ['AFT @#DJULIAN@ OCT 1700', 'después de octubre 1700 EC'],
            ['BEF @#DJULIAN@ OCT 1700', 'antes de octubre 1700 EC'],
            ['@#DJULIAN@ 15 NOV 1700', '15 noviembre 1700 EC'],
            ['@#DJULIAN@ NOV 1700', 'noviembre 1700 EC'],
            ['ABT @#DJULIAN@ NOV 1700', 'sobre noviembre 1700 EC'],
            ['FROM @#DJULIAN@ NOV 1700', 'desde noviembre 1700 EC'],
            ['AFT @#DJULIAN@ NOV 1700', 'después de noviembre 1700 EC'],
            ['BEF @#DJULIAN@ NOV 1700', 'antes de noviembre 1700 EC'],
            ['@#DJULIAN@ 15 DEC 1700', '15 diciembre 1700 EC'],
            ['@#DJULIAN@ DEC 1700', 'diciembre 1700 EC'],
            ['ABT @#DJULIAN@ DEC 1700', 'sobre diciembre 1700 EC'],
            ['FROM @#DJULIAN@ DEC 1700', 'desde diciembre 1700 EC'],
            ['AFT @#DJULIAN@ DEC 1700', 'después de diciembre 1700 EC'],
            ['BEF @#DJULIAN@ DEC 1700', 'antes de diciembre 1700 EC'],
            ['@#DJULIAN@ 1700', '1700 EC'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'sobre 15 enero 1700 EC'],
            ['CAL @#DJULIAN@ 15 JAN 1700', '15 enero 1700 EC calculadas'],
            ['EST @#DJULIAN@ 15 JAN 1700', '15 enero 1700 EC estimadas'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'antes de 15 enero 1700 EC'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'después de 15 enero 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'desde 15 enero 1700 EC'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'hasta 15 enero 1700 EC'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'entre 15 enero 1700 EC y 15 febrero 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'desde 15 enero 1700 EC hasta 15 febrero 1700 EC'],
            ['INT @#DJULIAN@ 15 JAN 1700', '15 enero 1700 EC interpretadas'],
            ['@#DHEBREW@ 15 TSH 5765', '15 tishrei 5765'],
            ['@#DHEBREW@ TSH 5765', 'tishrei 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'sobre tishrei 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'desde tishrei 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'después de tishrei 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'antes de tishrei 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 jeshván 5765'],
            ['@#DHEBREW@ CSH 5765', 'jeshván 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'sobre jeshván 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'desde jeshván 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'después de jeshván 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'antes de jeshván 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'sobre kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'desde kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'después de kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'antes de kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'sobre tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'desde tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'después de tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'antes de tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 shevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'shevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'sobre shevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'desde shevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'después de shevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'antes de shevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'sobre adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'desde adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'después de adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'antes de adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'sobre adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'desde adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'después de adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'antes de adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 nisán 5765'],
            ['@#DHEBREW@ NSN 5765', 'nisán 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'sobre nisán 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'desde nisán 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'después de nisán 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'antes de nisán 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'sobre iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'desde iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'después de iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'antes de iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 siván 5765'],
            ['@#DHEBREW@ SVN 5765', 'siván 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'sobre siván 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'desde siván 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'después de siván 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'antes de siván 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 tamuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tamuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'sobre tamuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'desde tamuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'después de tamuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'antes de tamuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 av 5765'],
            ['@#DHEBREW@ AAV 5765', 'av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'sobre av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'desde av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'después de av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'antes de av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'sobre elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'desde elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'después de elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'antes de elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'sobre 15 tishrei 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', '15 tishrei 5765 calculadas'],
            ['EST @#DHEBREW@ 15 TSH 5765', '15 tishrei 5765 estimadas'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'antes de 15 tishrei 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'después de 15 tishrei 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'desde 15 tishrei 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'hasta 15 tishrei 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'entre 15 tishrei 5765 y 15 jeshván 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'desde 15 tishrei 5765 hasta 15 jeshván 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', '15 tishrei 5765 interpretadas'],
            ['@#DFRENCH R@ 15 VEND 12', '15 vendimiario An XII'],
            ['@#DFRENCH R@ VEND 12', 'vendimiario An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'sobre vendimiario An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'desde vendimiario An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'después de vendimiario An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'antes de vendimiario An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 brumario An XII'],
            ['@#DFRENCH R@ BRUM 12', 'brumario An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'sobre brumario An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'desde brumario An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'después de brumario An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'antes de brumario An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 frimario An XII'],
            ['@#DFRENCH R@ FRIM 12', 'frimario An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'sobre frimario An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'desde frimario An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'después de frimario An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'antes de frimario An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 nivoso An XII'],
            ['@#DFRENCH R@ NIVO 12', 'nivoso An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'sobre nivoso An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'desde nivoso An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'después de nivoso An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'antes de nivoso An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 pluvioso An XII'],
            ['@#DFRENCH R@ PLUV 12', 'pluvioso An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'sobre pluvioso An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'desde pluvioso An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'después de pluvioso An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'antes de pluvioso An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ventoso An XII'],
            ['@#DFRENCH R@ VENT 12', 'ventoso An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'sobre ventoso An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'desde ventoso An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'después de ventoso An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'antes de ventoso An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'sobre germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'desde germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'después de germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'antes de germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 floreal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'floreal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'sobre floreal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'desde floreal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'después de floreal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'antes de floreal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 pradial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'pradial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'sobre pradial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'desde pradial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'después de pradial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'antes de pradial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'sobre messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'desde messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'después de messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'antes de messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 termidor An XII'],
            ['@#DFRENCH R@ THER 12', 'termidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'sobre termidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'desde termidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'después de termidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'antes de termidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'sobre fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'desde fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'después de fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'antes de fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 días complementarios An XII'],
            ['@#DFRENCH R@ COMP 12', 'días complementarios An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'sobre días complementarios An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'desde días complementarios An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'después de días complementarios An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'antes de días complementarios An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'sobre 15 vendimiario An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', '15 vendimiario An XII calculadas'],
            ['EST @#DFRENCH R@ 15 VEND 12', '15 vendimiario An XII estimadas'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'antes de 15 vendimiario An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'después de 15 vendimiario An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'desde 15 vendimiario An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'hasta 15 vendimiario An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'entre 15 vendimiario An XII y 15 brumario An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'desde 15 vendimiario An XII hasta 15 brumario An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', '15 vendimiario An XII interpretadas'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Muharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'sobre Muharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'desde Muharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'después de Muharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'antes de Muharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'sobre Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'desde Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'después de Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'antes de Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabi al-Awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabi al-Awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'sobre Rabi al-Awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'desde Rabi al-Awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'después de Rabi al-Awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'antes de Rabi al-Awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabi al-Thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabi al-Thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'sobre Rabi al-Thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'desde Rabi al-Thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'después de Rabi al-Thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'antes de Rabi al-Thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Jumada I-Üla 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Jumada I-Üla 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'sobre Jumada I-Üla 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'desde Jumada I-Üla 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'después de Jumada I-Üla 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'antes de Jumada I-Üla 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Jumada I-Akhira 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Jumada I-Akhira 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'sobre Jumada I-Akhira 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'desde Jumada I-Akhira 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'después de Jumada I-Akhira 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'antes de Jumada I-Akhira 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'sobre Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'desde Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'después de Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'antes de Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Shaabán 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Shaabán 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'sobre Shaabán 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'desde Shaabán 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'después de Shaabán 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'antes de Shaabán 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadán 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadán 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'sobre Ramadán 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'desde Ramadán 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'después de Ramadán 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'antes de Ramadán 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Shawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Shawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'sobre Shawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'desde Shawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'después de Shawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'antes de Shawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Zu I-Qada 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Zu I-Qada 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'sobre Zu I-Qada 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'desde Zu I-Qada 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'después de Zu I-Qada 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'antes de Zu I-Qada 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'sobre 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'desde 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'después de 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'antes de 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'sobre 15 Muharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425 calculadas'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425 estimadas'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'antes de 15 Muharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'después de 15 Muharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'desde 15 Muharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'hasta 15 Muharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'entre 15 Muharram 1425 y 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'desde 15 Muharram 1425 hasta 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', '15 Muharram 1425 interpretadas'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'sobre Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'desde Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'después de Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'antes de Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'sobre Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'desde Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'después de Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'antes de Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'sobre Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'desde Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'después de Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'antes de Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'sobre Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'desde Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'después de Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'antes de Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'sobre Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'desde Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'después de Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'antes de Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'sobre Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'desde Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'después de Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'antes de Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'sobre Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'desde Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'después de Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'antes de Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'Aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'sobre Aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'desde Aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'después de Aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'antes de Aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'sobre Azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'desde Azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'después de Azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'antes de Azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'sobre Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'desde Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'después de Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'antes de Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'sobre Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'desde Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'después de Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'antes de Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'sobre Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'desde Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'después de Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'antes de Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'sobre 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384 calculadas'],
            ['EST @#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384 estimadas'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'antes de 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'después de 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'desde 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'hasta 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'entre 15 Farvardin 1384 y 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'desde 15 Farvardin 1384 hasta 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384 interpretadas'],
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
        self::assertSame('one y two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two y three', $language->formatListAnd(['one', 'two', 'three']));

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
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $grandsonSon = self::male('gs', "1 FAMC @fson@");
        $granddaughterSon = self::female('gd', "1 FAMC @fson@");
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
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@\n1 CHIL @gs@\n1 CHIL @gd@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $stepDaughter, $fosterSon,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $grandsonSon, $granddaughterSon, $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('esposa', 'esposo', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-esposo', 'ex-esposa', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('prometida', 'prometido', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('madre', 'hijo', [$son, $fm, $wife]);
        self::assertRelationshipNames('padre', 'hijo', [$son, $fm, $husband]);
        self::assertRelationshipNames('madre', 'hija', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('madre adoptiva', 'hijo adoptivo', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('padre adoptivo', 'hijo adoptivo', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('madre de acogida', 'hijo de acogida', [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames('padre de acogida', 'hijo de acogida', [$fosterSon, $fd, $exHusband]);

        // Siblings
        self::assertRelationshipNames('hermana menor', 'hermano mayor', [$son, $fm, $daughter]);
        self::assertRelationshipNames('hermano mayor', 'hermana menor', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipNames('medio hermano', 'media hermana', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('padrastro', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('hijastra', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('suegra', 'yerno', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('suegro', 'yerno', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('nuera', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('abuela', 'nieto', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('abuelo', 'nieto', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('nieta', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic) - 4 generations up, n-1=2 → "tatara"
        self::assertRelationshipName('tataraabuelo', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('tataraabuela', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tía', 'sobrino', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('tío', 'sobrino', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('sobrina', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('sobrino', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('prima', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('primo', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) - 2 ancestor steps then sister, n=2, great(n-1=1) → "bis"
        self::assertRelationshipName('bistía', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('bistío', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}
