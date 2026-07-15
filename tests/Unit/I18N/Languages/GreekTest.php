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
use Fisharebest\Webtrees\I18N\Languages\Greek;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Greek::class)]
class GreekTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Greek();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Grek, self::language()->script());
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
        self::assertSame(['Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('el', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('Ελληνικά', self::language()->endonym());
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
            ['15 JAN 2000', '15 Ιανουαρίου 2000'],
            ['JAN 2000', 'Ιανουάριος 2000'],
            ['ABT JAN 2000', 'σχετικά με Ιανουαρίου 2000'],
            ['FROM JAN 2000', 'από Ιανουαρίου 2000'],
            ['AFT JAN 2000', 'μετά Ιανουάριος 2000'],
            ['BEF JAN 2000', 'πριν Ιανουάριος 2000'],
            ['15 FEB 2000', '15 Φεβρουαρίου 2000'],
            ['FEB 2000', 'Φεβρουάριος 2000'],
            ['ABT FEB 2000', 'σχετικά με Φεβρουαρίου 2000'],
            ['FROM FEB 2000', 'από Φεβρουαρίου 2000'],
            ['AFT FEB 2000', 'μετά Φεβρουάριος 2000'],
            ['BEF FEB 2000', 'πριν Φεβρουάριος 2000'],
            ['15 MAR 2000', '15 Μαρτίου 2000'],
            ['MAR 2000', 'Μάρτιος 2000'],
            ['ABT MAR 2000', 'σχετικά με Μαρτίου 2000'],
            ['FROM MAR 2000', 'από Μαρτίου 2000'],
            ['AFT MAR 2000', 'μετά Μάρτιος 2000'],
            ['BEF MAR 2000', 'πριν Μάρτιος 2000'],
            ['15 APR 2000', '15 Απριλίου 2000'],
            ['APR 2000', 'Απρίλιος 2000'],
            ['ABT APR 2000', 'σχετικά με Απριλίου 2000'],
            ['FROM APR 2000', 'από Απριλίου 2000'],
            ['AFT APR 2000', 'μετά Απρίλιος 2000'],
            ['BEF APR 2000', 'πριν Απρίλιος 2000'],
            ['15 MAY 2000', '15 Μαΐου 2000'],
            ['MAY 2000', 'Μάιος 2000'],
            ['ABT MAY 2000', 'σχετικά με Μαΐου 2000'],
            ['FROM MAY 2000', 'από Μαΐου 2000'],
            ['AFT MAY 2000', 'μετά Μάιος 2000'],
            ['BEF MAY 2000', 'πριν Μάιος 2000'],
            ['15 JUN 2000', '15 Ιουνίου 2000'],
            ['JUN 2000', 'Ιούνιος 2000'],
            ['ABT JUN 2000', 'σχετικά με Ιουνίου 2000'],
            ['FROM JUN 2000', 'από Ιουνίου 2000'],
            ['AFT JUN 2000', 'μετά Ιούνιος 2000'],
            ['BEF JUN 2000', 'πριν Ιούνιος 2000'],
            ['15 JUL 2000', '15 Ιουλίου 2000'],
            ['JUL 2000', 'Ιούλιος 2000'],
            ['ABT JUL 2000', 'σχετικά με Ιουλίου 2000'],
            ['FROM JUL 2000', 'από Ιουλίου 2000'],
            ['AFT JUL 2000', 'μετά Ιούλιος 2000'],
            ['BEF JUL 2000', 'πριν Ιούλιος 2000'],
            ['15 AUG 2000', '15 Αυγούστου 2000'],
            ['AUG 2000', 'Αύγουστος 2000'],
            ['ABT AUG 2000', 'σχετικά με Αυγούστου 2000'],
            ['FROM AUG 2000', 'από Αυγούστου 2000'],
            ['AFT AUG 2000', 'μετά Αύγουστος 2000'],
            ['BEF AUG 2000', 'πριν Αύγουστος 2000'],
            ['15 SEP 2000', '15 Σεπτεμβρίου 2000'],
            ['SEP 2000', 'Σεπτέμβριος 2000'],
            ['ABT SEP 2000', 'σχετικά με Σεπτεμβρίου 2000'],
            ['FROM SEP 2000', 'από Σεπτεμβρίου 2000'],
            ['AFT SEP 2000', 'μετά Σεπτέμβριος 2000'],
            ['BEF SEP 2000', 'πριν Σεπτέμβριος 2000'],
            ['15 OCT 2000', '15 Οκτωβρίου 2000'],
            ['OCT 2000', 'Οκτώβριος 2000'],
            ['ABT OCT 2000', 'σχετικά με Οκτωβρίου 2000'],
            ['FROM OCT 2000', 'από Οκτωβρίου 2000'],
            ['AFT OCT 2000', 'μετά Οκτώβριος 2000'],
            ['BEF OCT 2000', 'πριν Οκτώβριος 2000'],
            ['15 NOV 2000', '15 Νοεμβρίου 2000'],
            ['NOV 2000', 'Νοέμβριος 2000'],
            ['ABT NOV 2000', 'σχετικά με Νοεμβρίου 2000'],
            ['FROM NOV 2000', 'από Νοεμβρίου 2000'],
            ['AFT NOV 2000', 'μετά Νοέμβριος 2000'],
            ['BEF NOV 2000', 'πριν Νοέμβριος 2000'],
            ['15 DEC 2000', '15 Δεκεμβρίου 2000'],
            ['DEC 2000', 'Δεκέμβριος 2000'],
            ['ABT DEC 2000', 'σχετικά με Δεκεμβρίου 2000'],
            ['FROM DEC 2000', 'από Δεκεμβρίου 2000'],
            ['AFT DEC 2000', 'μετά Δεκέμβριος 2000'],
            ['BEF DEC 2000', 'πριν Δεκέμβριος 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'σχετικά με 15 Ιανουαρίου 2000'],
            ['CAL 15 JAN 2000', 'υπολογίστηκε 15 Ιανουαρίου 2000'],
            ['EST 15 JAN 2000', 'εκτιμώμενη 15 Ιανουαρίου 2000'],
            ['BEF 15 JAN 2000', 'πριν 15 Ιανουαρίου 2000'],
            ['AFT 15 JAN 2000', 'μετά 15 Ιανουαρίου 2000'],
            ['FROM 15 JAN 2000', 'από 15 Ιανουαρίου 2000'],
            ['TO 15 JAN 2000', 'έως 15 Ιανουαρίου 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'μεταξύ 15 Ιανουαρίου 2000 και 15 Φεβρουαρίου 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'Από 15 Ιανουαρίου 2000 εώς 15 Φεβρουαρίου 2000'],
            ['INT 15 JAN 2000', 'ερμηνεύεται 15 Ιανουαρίου 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 Ιανουαρίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ JAN 1700', 'Ιανουάριος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ JAN 1700', 'σχετικά με Ιανουαρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ JAN 1700', 'από Ιανουαρίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ JAN 1700', 'μετά Ιανουάριος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ JAN 1700', 'πριν Ιανουάριος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 FEB 1700', '15 Φεβρουαρίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ FEB 1700', 'Φεβρουάριος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ FEB 1700', 'σχετικά με Φεβρουαρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ FEB 1700', 'από Φεβρουαρίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ FEB 1700', 'μετά Φεβρουάριος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ FEB 1700', 'πριν Φεβρουάριος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 MAR 1700', '15 Μαρτίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ MAR 1700', 'Μάρτιος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ MAR 1700', 'σχετικά με Μαρτίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ MAR 1700', 'από Μαρτίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ MAR 1700', 'μετά Μάρτιος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ MAR 1700', 'πριν Μάρτιος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 APR 1700', '15 Απριλίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 Απριλίου 1645/46 ΜΚΧ'],
            ['@#DJULIAN@ APR 1700', 'Απρίλιος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ APR 1700', 'σχετικά με Απριλίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ APR 1700', 'από Απριλίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ APR 1700', 'μετά Απρίλιος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ APR 1700', 'πριν Απρίλιος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 MAY 1700', '15 Μαΐου 1700 ΜΚΧ'],
            ['@#DJULIAN@ MAY 1700', 'Μάιος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ MAY 1700', 'σχετικά με Μαΐου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ MAY 1700', 'από Μαΐου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ MAY 1700', 'μετά Μάιος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ MAY 1700', 'πριν Μάιος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 JUN 1700', '15 Ιουνίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ JUN 1700', 'Ιούνιος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ JUN 1700', 'σχετικά με Ιουνίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ JUN 1700', 'από Ιουνίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ JUN 1700', 'μετά Ιούνιος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ JUN 1700', 'πριν Ιούνιος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 JUL 1700', '15 Ιουλίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ JUL 1700', 'Ιούλιος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ JUL 1700', 'σχετικά με Ιουλίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ JUL 1700', 'από Ιουλίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ JUL 1700', 'μετά Ιούλιος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ JUL 1700', 'πριν Ιούλιος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 AUG 1700', '15 Αυγούστου 1700 ΜΚΧ'],
            ['@#DJULIAN@ AUG 1700', 'Αύγουστος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ AUG 1700', 'σχετικά με Αυγούστου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ AUG 1700', 'από Αυγούστου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ AUG 1700', 'μετά Αύγουστος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ AUG 1700', 'πριν Αύγουστος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 SEP 1700', '15 Σεπτεμβρίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ SEP 1700', 'Σεπτέμβριος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ SEP 1700', 'σχετικά με Σεπτεμβρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ SEP 1700', 'από Σεπτεμβρίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ SEP 1700', 'μετά Σεπτέμβριος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ SEP 1700', 'πριν Σεπτέμβριος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 OCT 1700', '15 Οκτωβρίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ OCT 1700', 'Οκτώβριος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ OCT 1700', 'σχετικά με Οκτωβρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ OCT 1700', 'από Οκτωβρίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ OCT 1700', 'μετά Οκτώβριος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ OCT 1700', 'πριν Οκτώβριος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 NOV 1700', '15 Νοεμβρίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ NOV 1700', 'Νοέμβριος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ NOV 1700', 'σχετικά με Νοεμβρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ NOV 1700', 'από Νοεμβρίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ NOV 1700', 'μετά Νοέμβριος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ NOV 1700', 'πριν Νοέμβριος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 15 DEC 1700', '15 Δεκεμβρίου 1700 ΜΚΧ'],
            ['@#DJULIAN@ DEC 1700', 'Δεκέμβριος 1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ DEC 1700', 'σχετικά με Δεκεμβρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ DEC 1700', 'από Δεκεμβρίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ DEC 1700', 'μετά Δεκέμβριος 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ DEC 1700', 'πριν Δεκέμβριος 1700 ΜΚΧ'],
            ['@#DJULIAN@ 1700', '1700 ΜΚΧ'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'σχετικά με 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'υπολογίστηκε 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'εκτιμώμενη 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'πριν 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'μετά 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'από 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'έως 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'μεταξύ 15 Ιανουαρίου 1700 ΜΚΧ και 15 Φεβρουαρίου 1700 ΜΚΧ'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'Από 15 Ιανουαρίου 1700 ΜΚΧ εώς 15 Φεβρουαρίου 1700 ΜΚΧ'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'ερμηνεύεται 15 Ιανουαρίου 1700 ΜΚΧ'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Τισρί 5765'],
            ['@#DHEBREW@ TSH 5765', 'Τισρί 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'σχετικά με Τισρί 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'από Τισρί 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'μετά Τισρί 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'πριν Τισρί 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Χεσβάν 5765'],
            ['@#DHEBREW@ CSH 5765', 'Χεσβάν 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'σχετικά με Χεσβάν 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'από Χεσβάν 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'μετά Χεσβάν 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'πριν Χεσβάν 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Κισλέβ 5765'],
            ['@#DHEBREW@ KSL 5765', 'Κισλέβ 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'σχετικά με Κισλέβ 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'από Κισλέβ 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'μετά Κισλέβ 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'πριν Κισλέβ 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Τεβέτ 5765'],
            ['@#DHEBREW@ TVT 5765', 'Τεβέτ 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'σχετικά με Τεβέτ 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'από Τεβέτ 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'μετά Τεβέτ 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'πριν Τεβέτ 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Σεβάτ 5765'],
            ['@#DHEBREW@ SHV 5765', 'Σεβάτ 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'σχετικά με Σεβάτ 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'από Σεβάτ 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'μετά Σεβάτ 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'πριν Σεβάτ 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Αδάρ Αʹ 5765'],
            ['@#DHEBREW@ ADR 5765', 'Αδάρ Αʹ 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'σχετικά με Αδάρ Αʹ 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'από Αδάρ Αʹ 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'μετά Αδάρ Αʹ 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'πριν Αδάρ Αʹ 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Αδάρ Βʹ 5765'],
            ['@#DHEBREW@ ADS 5765', 'Αδάρ Βʹ 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'σχετικά με Αδάρ Βʹ 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'από Αδάρ Βʹ 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'μετά Αδάρ Βʹ 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'πριν Αδάρ Βʹ 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Νισάν 5765'],
            ['@#DHEBREW@ NSN 5765', 'Νισάν 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'σχετικά με Νισάν 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'από Νισάν 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'μετά Νισάν 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'πριν Νισάν 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Ιγιάρ 5765'],
            ['@#DHEBREW@ IYR 5765', 'Ιγιάρ 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'σχετικά με Ιγιάρ 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'από Ιγιάρ 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'μετά Ιγιάρ 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'πριν Ιγιάρ 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Σιβάν 5765'],
            ['@#DHEBREW@ SVN 5765', 'Σιβάν 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'σχετικά με Σιβάν 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'από Σιβάν 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'μετά Σιβάν 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'πριν Σιβάν 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Ταμούζ 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Ταμούζ 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'σχετικά με Ταμούζ 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'από Ταμούζ 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'μετά Ταμούζ 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'πριν Ταμούζ 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Αβ 5765'],
            ['@#DHEBREW@ AAV 5765', 'Αβ 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'σχετικά με Αβ 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'από Αβ 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'μετά Αβ 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'πριν Αβ 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Ελούλ 5765'],
            ['@#DHEBREW@ ELL 5765', 'Ελούλ 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'σχετικά με Ελούλ 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'από Ελούλ 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'μετά Ελούλ 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'πριν Ελούλ 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'σχετικά με 15 Τισρί 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'υπολογίστηκε 15 Τισρί 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'εκτιμώμενη 15 Τισρί 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'πριν 15 Τισρί 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'μετά 15 Τισρί 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'από 15 Τισρί 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'έως 15 Τισρί 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'μεταξύ 15 Τισρί 5765 και 15 Χεσβάν 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'Από 15 Τισρί 5765 εώς 15 Χεσβάν 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'ερμηνεύεται 15 Τισρί 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Βαντεμιαίρ An XII'],
            ['@#DFRENCH R@ VEND 12', 'Βαντεμιαίρ An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'σχετικά με Βαντεμιαίρ An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'από Βαντεμιαίρ An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'μετά Βαντεμιαίρ An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'πριν Βαντεμιαίρ An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Μπρυμαίρ An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Μπρυμαίρ An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'σχετικά με Μπρυμαίρ An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'από Μπρυμαίρ An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'μετά Μπρυμαίρ An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'πριν Μπρυμαίρ An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Φριμαίρ An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Φριμαίρ An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'σχετικά με Φριμαίρ An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'από Φριμαίρ An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'μετά Φριμαίρ An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'πριν Φριμαίρ An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Νιβόζ An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Νιβόζ An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'σχετικά με Νιβόζ An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'από Νιβόζ An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'μετά Νιβόζ An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'πριν Νιβόζ An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Πλυβιόζ An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Πλυβιόζ An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'σχετικά με Πλυβιόζ An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'από Πλυβιόζ An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'μετά Πλυβιόζ An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'πριν Πλυβιόζ An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Βαντόζ An XII'],
            ['@#DFRENCH R@ VENT 12', 'Βαντόζ An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'σχετικά με Βαντόζ An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'από Βαντόζ An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'μετά Βαντόζ An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'πριν Βαντόζ An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Ζερμινάλ An XII'],
            ['@#DFRENCH R@ GERM 12', 'Ζερμινάλ An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'σχετικά με Ζερμινάλ An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'από Ζερμινάλ An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'μετά Ζερμινάλ An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'πριν Ζερμινάλ An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Φλοραίαλ An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Φλοραίαλ An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'σχετικά με Φλοραίαλ An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'από Φλοραίαλ An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'μετά Φλοραίαλ An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'πριν Φλοραίαλ An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Πραιριάλ An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Πραιριάλ An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'σχετικά με Πραιριάλ An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'από Πραιριάλ An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'μετά Πραιριάλ An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'πριν Πραιριάλ An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Μεσιδόρ An XII'],
            ['@#DFRENCH R@ MESS 12', 'Μεσιδόρ An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'σχετικά με Μεσιδόρ An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'από Μεσιδόρ An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'μετά Μεσιδόρ An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'πριν Μεσιδόρ An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Θερμιδόρ An XII'],
            ['@#DFRENCH R@ THER 12', 'Θερμιδόρ An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'σχετικά με Θερμιδόρ An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'από Θερμιδόρ An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'μετά Θερμιδόρ An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'πριν Θερμιδόρ An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Φρυκτιδόρ An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Φρυκτιδόρ An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'σχετικά με Φρυκτιδόρ An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'από Φρυκτιδόρ An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'μετά Φρυκτιδόρ An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'πριν Φρυκτιδόρ An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 συμπληρωματικές ημέρες An XII'],
            ['@#DFRENCH R@ COMP 12', 'συμπληρωματικές ημέρες An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'σχετικά με συμπληρωματικές ημέρες An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'από συμπληρωματικές ημέρες An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'μετά συμπληρωματικές ημέρες An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'πριν συμπληρωματικές ημέρες An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'σχετικά με 15 Βαντεμιαίρ An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'υπολογίστηκε 15 Βαντεμιαίρ An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'εκτιμώμενη 15 Βαντεμιαίρ An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'πριν 15 Βαντεμιαίρ An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'μετά 15 Βαντεμιαίρ An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'από 15 Βαντεμιαίρ An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'έως 15 Βαντεμιαίρ An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'μεταξύ 15 Βαντεμιαίρ An XII και 15 Μπρυμαίρ An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'Από 15 Βαντεμιαίρ An XII εώς 15 Μπρυμαίρ An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'ερμηνεύεται 15 Βαντεμιαίρ An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Μουχάραμ 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Μουχάραμ 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'σχετικά με Μουχάραμ 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'από Μουχάραμ 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'μετά Μουχάραμ 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'πριν Μουχάραμ 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Σαφάρ 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Σαφάρ 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'σχετικά με Σαφάρ 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'από Σαφάρ 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'μετά Σαφάρ 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'πριν Σαφάρ 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Ραμπί αλ-Αουάλ 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Ραμπί αλ-Αουάλ 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'σχετικά με Ραμπί αλ-Αουάλ 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'από Ραμπί αλ-Αουάλ 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'μετά Ραμπί αλ-Αουάλ 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'πριν Ραμπί αλ-Αουάλ 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Ραμπί αλ-Θάνι 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Ραμπί αλ-Θάνι 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'σχετικά με Ραμπί αλ-Θάνι 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'από Ραμπί αλ-Θάνι 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'μετά Ραμπί αλ-Θάνι 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'πριν Ραμπί αλ-Θάνι 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Τζουμάντα αλ-Αουάλ 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Τζουμάντα αλ-Αουάλ 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'σχετικά με Τζουμάντα αλ-Αουάλ 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'από Τζουμάντα αλ-Αουάλ 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'μετά Τζουμάντα αλ-Αουάλ 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'πριν Τζουμάντα αλ-Αουάλ 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Τζουμάντα αλ-Θάνι 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Τζουμάντα αλ-Θάνι 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'σχετικά με Τζουμάντα αλ-Θάνι 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'από Τζουμάντα αλ-Θάνι 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'μετά Τζουμάντα αλ-Θάνι 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'πριν Τζουμάντα αλ-Θάνι 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Ρατζάμπ 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Ρατζάμπ 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'σχετικά με Ρατζάμπ 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'από Ρατζάμπ 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'μετά Ρατζάμπ 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'πριν Ρατζάμπ 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Σααμπάν 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Σααμπάν 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'σχετικά με Σααμπάν 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'από Σααμπάν 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'μετά Σααμπάν 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'πριν Σααμπάν 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ραμαζάνι 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ραμαζάνι 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'σχετικά με Ραμαζάνι 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'από Ραμαζάνι 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'μετά Ραμαζάνι 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'πριν Ραμαζάνι 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Σαουάλ 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Σαουάλ 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'σχετικά με Σαουάλ 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'από Σαουάλ 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'μετά Σαουάλ 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'πριν Σαουάλ 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Ντου αλ-Καντά 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Ντου αλ-Καντά 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'σχετικά με Ντου αλ-Καντά 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'από Ντου αλ-Καντά 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'μετά Ντου αλ-Καντά 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'πριν Ντου αλ-Καντά 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'σχετικά με 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'από 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'μετά 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'πριν 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'σχετικά με 15 Μουχάραμ 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'υπολογίστηκε 15 Μουχάραμ 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'εκτιμώμενη 15 Μουχάραμ 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'πριν 15 Μουχάραμ 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'μετά 15 Μουχάραμ 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'από 15 Μουχάραμ 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'έως 15 Μουχάραμ 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'μεταξύ 15 Μουχάραμ 1425 και 15 Σαφάρ 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'Από 15 Μουχάραμ 1425 εώς 15 Σαφάρ 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'ερμηνεύεται 15 Μουχάραμ 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Φαρβαρντίν 1384'],
            ['@#DJALALI@ FARVA 1384', 'Φαρβαρντίν 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'σχετικά με Φαρβαρντίν 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'από Φαρβαρντίν 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'μετά Φαρβαρντίν 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'πριν Φαρβαρντίν 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ορντιμπεχέστ 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ορντιμπεχέστ 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'σχετικά με Ορντιμπεχέστ 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'από Ορντιμπεχέστ 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'μετά Ορντιμπεχέστ 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'πριν Ορντιμπεχέστ 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Χορντάντ 1384'],
            ['@#DJALALI@ KHORD 1384', 'Χορντάντ 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'σχετικά με Χορντάντ 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'από Χορντάντ 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'μετά Χορντάντ 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'πριν Χορντάντ 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Τιρ 1384'],
            ['@#DJALALI@ TIR 1384', 'Τιρ 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'σχετικά με Τιρ 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'από Τιρ 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'μετά Τιρ 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'πριν Τιρ 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Μορντάντ 1384'],
            ['@#DJALALI@ MORDA 1384', 'Μορντάντ 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'σχετικά με Μορντάντ 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'από Μορντάντ 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'μετά Μορντάντ 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'πριν Μορντάντ 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Σαχριβάρ 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Σαχριβάρ 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'σχετικά με Σαχριβάρ 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'από Σαχριβάρ 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'μετά Σαχριβάρ 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'πριν Σαχριβάρ 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Μεχρ 1384'],
            ['@#DJALALI@ MEHR 1384', 'Μεχρ 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'σχετικά με Μεχρ 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'από Μεχρ 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'μετά Μεχρ 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'πριν Μεχρ 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Αμπάν 1384'],
            ['@#DJALALI@ ABAN 1384', 'Αμπάν 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'σχετικά με Αμπάν 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'από Αμπάν 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'μετά Αμπάν 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'πριν Αμπάν 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Αζάρ 1384'],
            ['@#DJALALI@ AZAR 1384', 'Αζάρ 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'σχετικά με Αζάρ 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'από Αζάρ 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'μετά Αζάρ 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'πριν Αζάρ 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Ντέι 1384'],
            ['@#DJALALI@ DEY 1384', 'Ντέι 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'σχετικά με Ντέι 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'από Ντέι 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'μετά Ντέι 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'πριν Ντέι 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Μπάχμαν 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Μπάχμαν 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'σχετικά με Μπάχμαν 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'από Μπάχμαν 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'μετά Μπάχμαν 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'πριν Μπάχμαν 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Εσφάντ 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Εσφάντ 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'σχετικά με Εσφάντ 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'από Εσφάντ 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'μετά Εσφάντ 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'πριν Εσφάντ 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'σχετικά με 15 Φαρβαρντίν 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'υπολογίστηκε 15 Φαρβαρντίν 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'εκτιμώμενη 15 Φαρβαρντίν 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'πριν 15 Φαρβαρντίν 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'μετά 15 Φαρβαρντίν 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'από 15 Φαρβαρντίν 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'έως 15 Φαρβαρντίν 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'μεταξύ 15 Φαρβαρντίν 1384 και 15 Ορντιμπεχέστ 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'Από 15 Φαρβαρντίν 1384 εώς 15 Ορντιμπεχέστ 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'ερμηνεύεται 15 Φαρβαρντίν 1384'],
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
        self::assertSame('one και two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two και three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one ή two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two ή three', $language->formatListOr(['one', 'two', 'three']));
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
        self::assertRelationshipNames('σύζυγος', 'σύζυγος', [$husband, $fm, $wife]);
        self::assertRelationshipNames('πρώην σύζυγος', 'πρώην σύζυγος', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('αρραβωνιαστικός', 'αρραβωνιαστικιά', [$engaged, $fe, $fiance]);

        // Parents
        self::assertRelationshipNames('μητέρα', 'γιος', [$son, $fm, $wife]);
        self::assertRelationshipNames('πατέρας', 'γιος', [$son, $fm, $husband]);
        self::assertRelationshipNames('μητέρα', 'κόρη', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('αδελφή', 'αδελφός', [$son, $fm, $daughter]);
        self::assertRelationshipNames('αδελφός', 'αδελφή', [$daughter, $fm, $son]);

        // Half-siblings
        self::assertRelationshipName('ετεροθαλής αδελφή', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('πατριός', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('θετή κόρη', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('πεθερά', 'γαμπρός', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('πεθερός', 'γαμπρός', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('νύφη', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('γιαγιά', 'εγγονός', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('παππούς', 'εγγονός', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipNames('γιαγιά', 'εγγονός', [$son, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('παππούς', 'εγγονός', [$son, $fm, $wife, $fw, $fatherOfW]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('προπαππούς', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('προγιαγιά', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('θεία', 'ανιψιός', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('θείος', 'ανιψιός', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('ανιψιά', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('ανιψιός', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('ξαδέλφη', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('ξάδελφος', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }
}
