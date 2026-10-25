<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU general Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU general Public License for more details.
 * You should have received a copy of the GNU general Public License
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
            ['15 JAN 2000', '15 gener 2000'],
            ['JAN 2000', 'gener 2000'],
            ['ABT JAN 2000', 'sobre gener 2000'],
            ['FROM JAN 2000', 'des de gener 2000'],
            ['AFT JAN 2000', 'després de gener 2000'],
            ['BEF JAN 2000', 'abans de gener 2000'],
            ['15 FEB 2000', '15 febrer 2000'],
            ['FEB 2000', 'febrer 2000'],
            ['ABT FEB 2000', 'sobre febrer 2000'],
            ['FROM FEB 2000', 'des de febrer 2000'],
            ['AFT FEB 2000', 'després de febrer 2000'],
            ['BEF FEB 2000', 'abans de febrer 2000'],
            ['15 MAR 2000', '15 març 2000'],
            ['MAR 2000', 'març 2000'],
            ['ABT MAR 2000', 'sobre març 2000'],
            ['FROM MAR 2000', 'des de març 2000'],
            ['AFT MAR 2000', 'després de març 2000'],
            ['BEF MAR 2000', 'abans de març 2000'],
            ['15 APR 2000', '15 abril 2000'],
            ['APR 2000', 'abril 2000'],
            ['ABT APR 2000', 'sobre abril 2000'],
            ['FROM APR 2000', 'des d’abril 2000'],
            ['AFT APR 2000', 'després d’abril 2000'],
            ['BEF APR 2000', 'abans d’abril 2000'],
            ['15 MAY 2000', '15 maig 2000'],
            ['MAY 2000', 'maig 2000'],
            ['ABT MAY 2000', 'sobre maig 2000'],
            ['FROM MAY 2000', 'des de maig 2000'],
            ['AFT MAY 2000', 'després de maig 2000'],
            ['BEF MAY 2000', 'abans de maig 2000'],
            ['15 JUN 2000', '15 juny 2000'],
            ['JUN 2000', 'juny 2000'],
            ['ABT JUN 2000', 'sobre juny 2000'],
            ['FROM JUN 2000', 'des de juny 2000'],
            ['AFT JUN 2000', 'després de juny 2000'],
            ['BEF JUN 2000', 'abans de juny 2000'],
            ['15 JUL 2000', '15 juliol 2000'],
            ['JUL 2000', 'juliol 2000'],
            ['ABT JUL 2000', 'sobre juliol 2000'],
            ['FROM JUL 2000', 'des de juliol 2000'],
            ['AFT JUL 2000', 'després de juliol 2000'],
            ['BEF JUL 2000', 'abans de juliol 2000'],
            ['15 AUG 2000', '15 agost 2000'],
            ['AUG 2000', 'agost 2000'],
            ['ABT AUG 2000', 'sobre agost 2000'],
            ['FROM AUG 2000', 'des d’agost 2000'],
            ['AFT AUG 2000', 'després d’agost 2000'],
            ['BEF AUG 2000', 'abans d’agost 2000'],
            ['15 SEP 2000', '15 setembre 2000'],
            ['SEP 2000', 'setembre 2000'],
            ['ABT SEP 2000', 'sobre setembre 2000'],
            ['FROM SEP 2000', 'des de setembre 2000'],
            ['AFT SEP 2000', 'després de setembre 2000'],
            ['BEF SEP 2000', 'abans de setembre 2000'],
            ['15 OCT 2000', '15 octubre 2000'],
            ['OCT 2000', 'octubre 2000'],
            ['ABT OCT 2000', 'sobre octubre 2000'],
            ['FROM OCT 2000', 'des d’octubre 2000'],
            ['AFT OCT 2000', 'després d’octubre 2000'],
            ['BEF OCT 2000', 'abans d’octubre 2000'],
            ['15 NOV 2000', '15 novembre 2000'],
            ['NOV 2000', 'novembre 2000'],
            ['ABT NOV 2000', 'sobre novembre 2000'],
            ['FROM NOV 2000', 'des de novembre 2000'],
            ['AFT NOV 2000', 'després de novembre 2000'],
            ['BEF NOV 2000', 'abans de novembre 2000'],
            ['15 DEC 2000', '15 desembre 2000'],
            ['DEC 2000', 'desembre 2000'],
            ['ABT DEC 2000', 'sobre desembre 2000'],
            ['FROM DEC 2000', 'des de desembre 2000'],
            ['AFT DEC 2000', 'després de desembre 2000'],
            ['BEF DEC 2000', 'abans de desembre 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'sobre 15 gener 2000'],
            ['CAL 15 JAN 2000', 'calculat 15 gener 2000'],
            ['EST 15 JAN 2000', 'estimat 15 gener 2000'],
            ['BEF 15 JAN 2000', 'abans de 15 gener 2000'],
            ['AFT 15 JAN 2000', 'després de 15 gener 2000'],
            ['FROM 15 JAN 2000', 'des de 15 gener 2000'],
            ['TO 15 JAN 2000', 'a 15 gener 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'entre 15 gener 2000 i 15 febrer 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'de 15 gener 2000 a 15 febrer 2000'],
            ['INT 15 JAN 2000', 'interpretat 15 gener 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 gener 1700 EC'],
            ['@#DJULIAN@ JAN 1700', 'gener 1700 EC'],
            ['ABT @#DJULIAN@ JAN 1700', 'sobre gener 1700 EC'],
            ['FROM @#DJULIAN@ JAN 1700', 'des de gener 1700 EC'],
            ['AFT @#DJULIAN@ JAN 1700', 'després de gener 1700 EC'],
            ['BEF @#DJULIAN@ JAN 1700', 'abans de gener 1700 EC'],
            ['@#DJULIAN@ 15 FEB 1700', '15 febrer 1700 EC'],
            ['@#DJULIAN@ FEB 1700', 'febrer 1700 EC'],
            ['ABT @#DJULIAN@ FEB 1700', 'sobre febrer 1700 EC'],
            ['FROM @#DJULIAN@ FEB 1700', 'des de febrer 1700 EC'],
            ['AFT @#DJULIAN@ FEB 1700', 'després de febrer 1700 EC'],
            ['BEF @#DJULIAN@ FEB 1700', 'abans de febrer 1700 EC'],
            ['@#DJULIAN@ 15 MAR 1700', '15 març 1700 EC'],
            ['@#DJULIAN@ MAR 1700', 'març 1700 EC'],
            ['ABT @#DJULIAN@ MAR 1700', 'sobre març 1700 EC'],
            ['FROM @#DJULIAN@ MAR 1700', 'des de març 1700 EC'],
            ['AFT @#DJULIAN@ MAR 1700', 'després de març 1700 EC'],
            ['BEF @#DJULIAN@ MAR 1700', 'abans de març 1700 EC'],
            ['@#DJULIAN@ 15 APR 1700', '15 abril 1700 EC'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 abril 1645/46 EC'],
            ['@#DJULIAN@ APR 1700', 'abril 1700 EC'],
            ['ABT @#DJULIAN@ APR 1700', 'sobre abril 1700 EC'],
            ['FROM @#DJULIAN@ APR 1700', 'des d’abril 1700 EC'],
            ['AFT @#DJULIAN@ APR 1700', 'després d’abril 1700 EC'],
            ['BEF @#DJULIAN@ APR 1700', 'abans d’abril 1700 EC'],
            ['@#DJULIAN@ 15 MAY 1700', '15 maig 1700 EC'],
            ['@#DJULIAN@ MAY 1700', 'maig 1700 EC'],
            ['ABT @#DJULIAN@ MAY 1700', 'sobre maig 1700 EC'],
            ['FROM @#DJULIAN@ MAY 1700', 'des de maig 1700 EC'],
            ['AFT @#DJULIAN@ MAY 1700', 'després de maig 1700 EC'],
            ['BEF @#DJULIAN@ MAY 1700', 'abans de maig 1700 EC'],
            ['@#DJULIAN@ 15 JUN 1700', '15 juny 1700 EC'],
            ['@#DJULIAN@ JUN 1700', 'juny 1700 EC'],
            ['ABT @#DJULIAN@ JUN 1700', 'sobre juny 1700 EC'],
            ['FROM @#DJULIAN@ JUN 1700', 'des de juny 1700 EC'],
            ['AFT @#DJULIAN@ JUN 1700', 'després de juny 1700 EC'],
            ['BEF @#DJULIAN@ JUN 1700', 'abans de juny 1700 EC'],
            ['@#DJULIAN@ 15 JUL 1700', '15 juliol 1700 EC'],
            ['@#DJULIAN@ JUL 1700', 'juliol 1700 EC'],
            ['ABT @#DJULIAN@ JUL 1700', 'sobre juliol 1700 EC'],
            ['FROM @#DJULIAN@ JUL 1700', 'des de juliol 1700 EC'],
            ['AFT @#DJULIAN@ JUL 1700', 'després de juliol 1700 EC'],
            ['BEF @#DJULIAN@ JUL 1700', 'abans de juliol 1700 EC'],
            ['@#DJULIAN@ 15 AUG 1700', '15 agost 1700 EC'],
            ['@#DJULIAN@ AUG 1700', 'agost 1700 EC'],
            ['ABT @#DJULIAN@ AUG 1700', 'sobre agost 1700 EC'],
            ['FROM @#DJULIAN@ AUG 1700', 'des d’agost 1700 EC'],
            ['AFT @#DJULIAN@ AUG 1700', 'després d’agost 1700 EC'],
            ['BEF @#DJULIAN@ AUG 1700', 'abans d’agost 1700 EC'],
            ['@#DJULIAN@ 15 SEP 1700', '15 setembre 1700 EC'],
            ['@#DJULIAN@ SEP 1700', 'setembre 1700 EC'],
            ['ABT @#DJULIAN@ SEP 1700', 'sobre setembre 1700 EC'],
            ['FROM @#DJULIAN@ SEP 1700', 'des de setembre 1700 EC'],
            ['AFT @#DJULIAN@ SEP 1700', 'després de setembre 1700 EC'],
            ['BEF @#DJULIAN@ SEP 1700', 'abans de setembre 1700 EC'],
            ['@#DJULIAN@ 15 OCT 1700', '15 octubre 1700 EC'],
            ['@#DJULIAN@ OCT 1700', 'octubre 1700 EC'],
            ['ABT @#DJULIAN@ OCT 1700', 'sobre octubre 1700 EC'],
            ['FROM @#DJULIAN@ OCT 1700', 'des d’octubre 1700 EC'],
            ['AFT @#DJULIAN@ OCT 1700', 'després d’octubre 1700 EC'],
            ['BEF @#DJULIAN@ OCT 1700', 'abans d’octubre 1700 EC'],
            ['@#DJULIAN@ 15 NOV 1700', '15 novembre 1700 EC'],
            ['@#DJULIAN@ NOV 1700', 'novembre 1700 EC'],
            ['ABT @#DJULIAN@ NOV 1700', 'sobre novembre 1700 EC'],
            ['FROM @#DJULIAN@ NOV 1700', 'des de novembre 1700 EC'],
            ['AFT @#DJULIAN@ NOV 1700', 'després de novembre 1700 EC'],
            ['BEF @#DJULIAN@ NOV 1700', 'abans de novembre 1700 EC'],
            ['@#DJULIAN@ 15 DEC 1700', '15 desembre 1700 EC'],
            ['@#DJULIAN@ DEC 1700', 'desembre 1700 EC'],
            ['ABT @#DJULIAN@ DEC 1700', 'sobre desembre 1700 EC'],
            ['FROM @#DJULIAN@ DEC 1700', 'des de desembre 1700 EC'],
            ['AFT @#DJULIAN@ DEC 1700', 'després de desembre 1700 EC'],
            ['BEF @#DJULIAN@ DEC 1700', 'abans de desembre 1700 EC'],
            ['@#DJULIAN@ 1700', '1700 EC'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'sobre 15 gener 1700 EC'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculat 15 gener 1700 EC'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimat 15 gener 1700 EC'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'abans de 15 gener 1700 EC'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'després de 15 gener 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'des de 15 gener 1700 EC'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'a 15 gener 1700 EC'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'entre 15 gener 1700 EC i 15 febrer 1700 EC'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'de 15 gener 1700 EC a 15 febrer 1700 EC'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interpretat 15 gener 1700 EC'],
            ['@#DHEBREW@ 15 TSH 5765', '15 tixrí 5765'],
            ['@#DHEBREW@ TSH 5765', 'tixrí 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'sobre tixrí 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'des de tixrí 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'després de tixrí 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'abans de tixrí 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 heixvan 5765'],
            ['@#DHEBREW@ CSH 5765', 'heixvan 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'sobre heixvan 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'des de heixvan 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'després de heixvan 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'abans de heixvan 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 quisleu 5765'],
            ['@#DHEBREW@ KSL 5765', 'quisleu 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'sobre quisleu 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'des de quisleu 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'després de quisleu 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'abans de quisleu 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 tevet 5765'],
            ['@#DHEBREW@ TVT 5765', 'tevet 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'sobre tevet 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'des de tevet 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'després de tevet 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'abans de tevet 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 xevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'xevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'sobre xevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'des de xevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'després de xevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'abans de xevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'sobre adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'des d’adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'després d’adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'abans d’adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'sobre adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'des d’adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'després d’adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'abans d’adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 nisan 5765'],
            ['@#DHEBREW@ NSN 5765', 'nisan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'sobre nisan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'des de nisan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'després de nisan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'abans de nisan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 iar 5765'],
            ['@#DHEBREW@ IYR 5765', 'iar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'sobre iar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'des d’iar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'després d’iar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'abans d’iar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 sivan 5765'],
            ['@#DHEBREW@ SVN 5765', 'sivan 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'sobre sivan 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'des de sivan 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'després de sivan 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'abans de sivan 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 tammuz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'tammuz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'sobre tammuz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'des de tammuz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'després de tammuz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'abans de tammuz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 av 5765'],
            ['@#DHEBREW@ AAV 5765', 'av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'sobre av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'des d’av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'després d’av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'abans d’av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 elul 5765'],
            ['@#DHEBREW@ ELL 5765', 'elul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'sobre elul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'des d’elul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'després d’elul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'abans d’elul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'sobre 15 tixrí 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculat 15 tixrí 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimat 15 tixrí 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'abans de 15 tixrí 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'després de 15 tixrí 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'des de 15 tixrí 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'a 15 tixrí 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'entre 15 tixrí 5765 i 15 heixvan 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'de 15 tixrí 5765 a 15 heixvan 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interpretat 15 tixrí 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 vendemiari An XII'],
            ['@#DFRENCH R@ VEND 12', 'vendemiari An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'sobre vendemiari An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'des de vendemiari An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'després de vendemiari An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'abans de vendemiari An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 bromari An XII'],
            ['@#DFRENCH R@ BRUM 12', 'bromari An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'sobre bromari An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'des de bromari An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'després de bromari An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'abans de bromari An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 rufolari An XII'],
            ['@#DFRENCH R@ FRIM 12', 'rufolari An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'sobre rufolari An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'des de rufolari An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'després de rufolari An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'abans de rufolari An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 nivós An XII'],
            ['@#DFRENCH R@ NIVO 12', 'nivós An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'sobre nivós An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'des de nivós An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'després de nivós An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'abans de nivós An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 pluviós An XII'],
            ['@#DFRENCH R@ PLUV 12', 'pluviós An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'sobre pluviós An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'des de pluviós An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'després de pluviós An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'abans de pluviós An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ventós An XII'],
            ['@#DFRENCH R@ VENT 12', 'ventós An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'sobre ventós An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'des de ventós An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'després de ventós An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'abans de ventós An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'sobre germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'des de germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'després de germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'abans de germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 floral An XII'],
            ['@#DFRENCH R@ FLOR 12', 'floral An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'sobre floral An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'des de floral An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'després de floral An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'abans de floral An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 pradal An XII'],
            ['@#DFRENCH R@ PRAI 12', 'pradal An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'sobre pradal An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'des de pradal An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'després de pradal An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'abans de pradal An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'sobre messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'des de messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'després de messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'abans de messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 termidor An XII'],
            ['@#DFRENCH R@ THER 12', 'termidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'sobre termidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'des de termidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'després de termidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'abans de termidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'sobre fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'des de fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'després de fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'abans de fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 dies complementaris An XII'],
            ['@#DFRENCH R@ COMP 12', 'dies complementaris An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'sobre dies complementaris An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'des de dies complementaris An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'després de dies complementaris An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'abans de dies complementaris An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'sobre 15 vendemiari An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculat 15 vendemiari An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimat 15 vendemiari An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'abans de 15 vendemiari An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'després de 15 vendemiari An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'des de 15 vendemiari An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'a 15 vendemiari An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'entre 15 vendemiari An XII i 15 bromari An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'de 15 vendemiari An XII a 15 bromari An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interpretat 15 vendemiari An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 muhàrram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'muhàrram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'sobre muhàrram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'des de muhàrram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'després de muhàrram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'abans de muhàrram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 sàfar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'sàfar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'sobre sàfar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'des de sàfar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'després de sàfar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'abans de sàfar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 rabi’ al-awwal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'rabi’ al-awwal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'sobre rabi’ al-awwal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'des de rabi’ al-awwal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'després de rabi’ al-awwal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'abans de rabi’ al-awwal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 rabi’ al-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'rabi’ al-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'sobre rabi’ al-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'des de rabi’ al-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'després de rabi’ al-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'abans de rabi’ al-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 jumada al-ula 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'jumada al-ula 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'sobre jumada al-ula 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'des de jumada al-ula 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'després de jumada al-ula 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'abans de jumada al-ula 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 jumada al-àkhira 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'jumada al-àkhira 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'sobre jumada al-àkhira 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'des de jumada al-àkhira 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'després de jumada al-àkhira 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'abans de jumada al-àkhira 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 ràjab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'ràjab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'sobre ràjab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'des de ràjab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'després de ràjab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'abans de ràjab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 xaban 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'xaban 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'sobre xaban 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'des de xaban 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'després de xaban 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'abans de xaban 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 ramadà 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'ramadà 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'sobre ramadà 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'des de ramadà 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'després de ramadà 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'abans de ramadà 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 xawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'xawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'sobre xawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'des de xawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'després de xawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'abans de xawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 dhu-l-qada 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'dhu-l-qada 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'sobre dhu-l-qada 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'des de dhu-l-qada 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'després de dhu-l-qada 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'abans de dhu-l-qada 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'sobre 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'des de 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'després de 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'abans de 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'sobre 15 muhàrram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculat 15 muhàrram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimat 15 muhàrram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'abans de 15 muhàrram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'després de 15 muhàrram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'des de 15 muhàrram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'a 15 muhàrram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'entre 15 muhàrram 1425 i 15 sàfar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'de 15 muhàrram 1425 a 15 sàfar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interpretat 15 muhàrram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 farvardín 1384'],
            ['@#DJALALI@ FARVA 1384', 'farvardín 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'sobre farvardín 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'des de farvardín 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'després de farvardín 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'abans de farvardín 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'sobre ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'des d’ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'després d’ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'abans d’ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'sobre khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'des de khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'després de khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'abans de khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 tir 1384'],
            ['@#DJALALI@ TIR 1384', 'tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'sobre tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'des de tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'després de tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'abans de tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'sobre mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'des de mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'després de mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'abans de mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'sobre shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'des de shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'després de shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'abans de shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'sobre mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'des de mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'després de mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'abans de mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 aban 1384'],
            ['@#DJALALI@ ABAN 1384', 'aban 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'sobre aban 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'des d’aban 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'després d’aban 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'abans d’aban 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 azar 1384'],
            ['@#DJALALI@ AZAR 1384', 'azar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'sobre azar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'des d’azar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'després d’azar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'abans d’azar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 dey 1384'],
            ['@#DJALALI@ DEY 1384', 'dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'sobre dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'des de dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'després de dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'abans de dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'sobre bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'des de bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'després de bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'abans de bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'sobre esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'des d’esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'després d’esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'abans d’esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'sobre 15 farvardín 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculat 15 farvardín 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimat 15 farvardín 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'abans de 15 farvardín 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'després de 15 farvardín 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'des de 15 farvardín 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'a 15 farvardín 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'entre 15 farvardín 1384 i 15 ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'de 15 farvardín 1384 a 15 ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interpretat 15 farvardín 1384'],
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
