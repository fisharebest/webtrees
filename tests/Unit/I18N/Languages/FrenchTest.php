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
use Fisharebest\Webtrees\I18N\Languages\French;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(French::class)]
class FrenchTest extends AbstractFrenchTestCase
{
    protected static function language(): LanguageInterface
    {
        return new French();
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
        self::assertSame('fr', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('français', self::language()->endonym());
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
        self::assertSame('-123 456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-123 456,0789 %', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 janvier 2000'],
            ['JAN 2000', 'janvier 2000'],
            ['ABT JAN 2000', 'vers janvier 2000'],
            ['FROM JAN 2000', 'de janvier 2000'],
            ['AFT JAN 2000', 'après janvier 2000'],
            ['BEF JAN 2000', 'avant janvier 2000'],
            ['15 FEB 2000', '15 février 2000'],
            ['FEB 2000', 'février 2000'],
            ['ABT FEB 2000', 'vers février 2000'],
            ['FROM FEB 2000', 'de février 2000'],
            ['AFT FEB 2000', 'après février 2000'],
            ['BEF FEB 2000', 'avant février 2000'],
            ['15 MAR 2000', '15 mars 2000'],
            ['MAR 2000', 'mars 2000'],
            ['ABT MAR 2000', 'vers mars 2000'],
            ['FROM MAR 2000', 'de mars 2000'],
            ['AFT MAR 2000', 'après mars 2000'],
            ['BEF MAR 2000', 'avant mars 2000'],
            ['15 APR 2000', '15 avril 2000'],
            ['APR 2000', 'avril 2000'],
            ['ABT APR 2000', 'vers avril 2000'],
            ['FROM APR 2000', 'de avril 2000'],
            ['AFT APR 2000', 'après avril 2000'],
            ['BEF APR 2000', 'avant avril 2000'],
            ['15 MAY 2000', '15 mai 2000'],
            ['MAY 2000', 'mai 2000'],
            ['ABT MAY 2000', 'vers mai 2000'],
            ['FROM MAY 2000', 'de mai 2000'],
            ['AFT MAY 2000', 'après mai 2000'],
            ['BEF MAY 2000', 'avant mai 2000'],
            ['15 JUN 2000', '15 juin 2000'],
            ['JUN 2000', 'juin 2000'],
            ['ABT JUN 2000', 'vers juin 2000'],
            ['FROM JUN 2000', 'de juin 2000'],
            ['AFT JUN 2000', 'après juin 2000'],
            ['BEF JUN 2000', 'avant juin 2000'],
            ['15 JUL 2000', '15 juillet 2000'],
            ['JUL 2000', 'juillet 2000'],
            ['ABT JUL 2000', 'vers juillet 2000'],
            ['FROM JUL 2000', 'de juillet 2000'],
            ['AFT JUL 2000', 'après juillet 2000'],
            ['BEF JUL 2000', 'avant juillet 2000'],
            ['15 AUG 2000', '15 août 2000'],
            ['AUG 2000', 'août 2000'],
            ['ABT AUG 2000', 'vers août 2000'],
            ['FROM AUG 2000', 'de août 2000'],
            ['AFT AUG 2000', 'après août 2000'],
            ['BEF AUG 2000', 'avant août 2000'],
            ['15 SEP 2000', '15 septembre 2000'],
            ['SEP 2000', 'septembre 2000'],
            ['ABT SEP 2000', 'vers septembre 2000'],
            ['FROM SEP 2000', 'de septembre 2000'],
            ['AFT SEP 2000', 'après septembre 2000'],
            ['BEF SEP 2000', 'avant septembre 2000'],
            ['15 OCT 2000', '15 octobre 2000'],
            ['OCT 2000', 'octobre 2000'],
            ['ABT OCT 2000', 'vers octobre 2000'],
            ['FROM OCT 2000', 'de octobre 2000'],
            ['AFT OCT 2000', 'après octobre 2000'],
            ['BEF OCT 2000', 'avant octobre 2000'],
            ['15 NOV 2000', '15 novembre 2000'],
            ['NOV 2000', 'novembre 2000'],
            ['ABT NOV 2000', 'vers novembre 2000'],
            ['FROM NOV 2000', 'de novembre 2000'],
            ['AFT NOV 2000', 'après novembre 2000'],
            ['BEF NOV 2000', 'avant novembre 2000'],
            ['15 DEC 2000', '15 décembre 2000'],
            ['DEC 2000', 'décembre 2000'],
            ['ABT DEC 2000', 'vers décembre 2000'],
            ['FROM DEC 2000', 'de décembre 2000'],
            ['AFT DEC 2000', 'après décembre 2000'],
            ['BEF DEC 2000', 'avant décembre 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'vers 15 janvier 2000'],
            ['CAL 15 JAN 2000', 'calculé 15 janvier 2000'],
            ['EST 15 JAN 2000', 'estimé 15 janvier 2000'],
            ['BEF 15 JAN 2000', 'avant 15 janvier 2000'],
            ['AFT 15 JAN 2000', 'après 15 janvier 2000'],
            ['FROM 15 JAN 2000', 'de 15 janvier 2000'],
            ['TO 15 JAN 2000', 'à 15 janvier 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'entre 15 janvier 2000 et 15 février 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'de 15 janvier 2000 à 15 février 2000'],
            ['INT 15 JAN 2000', 'interprété 15 janvier 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 janvier 1700 de notre ère'],
            ['@#DJULIAN@ JAN 1700', 'janvier 1700 de notre ère'],
            ['ABT @#DJULIAN@ JAN 1700', 'vers janvier 1700 de notre ère'],
            ['FROM @#DJULIAN@ JAN 1700', 'de janvier 1700 de notre ère'],
            ['AFT @#DJULIAN@ JAN 1700', 'après janvier 1700 de notre ère'],
            ['BEF @#DJULIAN@ JAN 1700', 'avant janvier 1700 de notre ère'],
            ['@#DJULIAN@ 15 FEB 1700', '15 février 1700 de notre ère'],
            ['@#DJULIAN@ FEB 1700', 'février 1700 de notre ère'],
            ['ABT @#DJULIAN@ FEB 1700', 'vers février 1700 de notre ère'],
            ['FROM @#DJULIAN@ FEB 1700', 'de février 1700 de notre ère'],
            ['AFT @#DJULIAN@ FEB 1700', 'après février 1700 de notre ère'],
            ['BEF @#DJULIAN@ FEB 1700', 'avant février 1700 de notre ère'],
            ['@#DJULIAN@ 15 MAR 1700', '15 mars 1700 de notre ère'],
            ['@#DJULIAN@ MAR 1700', 'mars 1700 de notre ère'],
            ['ABT @#DJULIAN@ MAR 1700', 'vers mars 1700 de notre ère'],
            ['FROM @#DJULIAN@ MAR 1700', 'de mars 1700 de notre ère'],
            ['AFT @#DJULIAN@ MAR 1700', 'après mars 1700 de notre ère'],
            ['BEF @#DJULIAN@ MAR 1700', 'avant mars 1700 de notre ère'],
            ['@#DJULIAN@ 15 APR 1700', '15 avril 1700 de notre ère'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 avril 1645/46 de notre ère'],
            ['@#DJULIAN@ APR 1700', 'avril 1700 de notre ère'],
            ['ABT @#DJULIAN@ APR 1700', 'vers avril 1700 de notre ère'],
            ['FROM @#DJULIAN@ APR 1700', 'de avril 1700 de notre ère'],
            ['AFT @#DJULIAN@ APR 1700', 'après avril 1700 de notre ère'],
            ['BEF @#DJULIAN@ APR 1700', 'avant avril 1700 de notre ère'],
            ['@#DJULIAN@ 15 MAY 1700', '15 mai 1700 de notre ère'],
            ['@#DJULIAN@ MAY 1700', 'mai 1700 de notre ère'],
            ['ABT @#DJULIAN@ MAY 1700', 'vers mai 1700 de notre ère'],
            ['FROM @#DJULIAN@ MAY 1700', 'de mai 1700 de notre ère'],
            ['AFT @#DJULIAN@ MAY 1700', 'après mai 1700 de notre ère'],
            ['BEF @#DJULIAN@ MAY 1700', 'avant mai 1700 de notre ère'],
            ['@#DJULIAN@ 15 JUN 1700', '15 juin 1700 de notre ère'],
            ['@#DJULIAN@ JUN 1700', 'juin 1700 de notre ère'],
            ['ABT @#DJULIAN@ JUN 1700', 'vers juin 1700 de notre ère'],
            ['FROM @#DJULIAN@ JUN 1700', 'de juin 1700 de notre ère'],
            ['AFT @#DJULIAN@ JUN 1700', 'après juin 1700 de notre ère'],
            ['BEF @#DJULIAN@ JUN 1700', 'avant juin 1700 de notre ère'],
            ['@#DJULIAN@ 15 JUL 1700', '15 juillet 1700 de notre ère'],
            ['@#DJULIAN@ JUL 1700', 'juillet 1700 de notre ère'],
            ['ABT @#DJULIAN@ JUL 1700', 'vers juillet 1700 de notre ère'],
            ['FROM @#DJULIAN@ JUL 1700', 'de juillet 1700 de notre ère'],
            ['AFT @#DJULIAN@ JUL 1700', 'après juillet 1700 de notre ère'],
            ['BEF @#DJULIAN@ JUL 1700', 'avant juillet 1700 de notre ère'],
            ['@#DJULIAN@ 15 AUG 1700', '15 août 1700 de notre ère'],
            ['@#DJULIAN@ AUG 1700', 'août 1700 de notre ère'],
            ['ABT @#DJULIAN@ AUG 1700', 'vers août 1700 de notre ère'],
            ['FROM @#DJULIAN@ AUG 1700', 'de août 1700 de notre ère'],
            ['AFT @#DJULIAN@ AUG 1700', 'après août 1700 de notre ère'],
            ['BEF @#DJULIAN@ AUG 1700', 'avant août 1700 de notre ère'],
            ['@#DJULIAN@ 15 SEP 1700', '15 septembre 1700 de notre ère'],
            ['@#DJULIAN@ SEP 1700', 'septembre 1700 de notre ère'],
            ['ABT @#DJULIAN@ SEP 1700', 'vers septembre 1700 de notre ère'],
            ['FROM @#DJULIAN@ SEP 1700', 'de septembre 1700 de notre ère'],
            ['AFT @#DJULIAN@ SEP 1700', 'après septembre 1700 de notre ère'],
            ['BEF @#DJULIAN@ SEP 1700', 'avant septembre 1700 de notre ère'],
            ['@#DJULIAN@ 15 OCT 1700', '15 octobre 1700 de notre ère'],
            ['@#DJULIAN@ OCT 1700', 'octobre 1700 de notre ère'],
            ['ABT @#DJULIAN@ OCT 1700', 'vers octobre 1700 de notre ère'],
            ['FROM @#DJULIAN@ OCT 1700', 'de octobre 1700 de notre ère'],
            ['AFT @#DJULIAN@ OCT 1700', 'après octobre 1700 de notre ère'],
            ['BEF @#DJULIAN@ OCT 1700', 'avant octobre 1700 de notre ère'],
            ['@#DJULIAN@ 15 NOV 1700', '15 novembre 1700 de notre ère'],
            ['@#DJULIAN@ NOV 1700', 'novembre 1700 de notre ère'],
            ['ABT @#DJULIAN@ NOV 1700', 'vers novembre 1700 de notre ère'],
            ['FROM @#DJULIAN@ NOV 1700', 'de novembre 1700 de notre ère'],
            ['AFT @#DJULIAN@ NOV 1700', 'après novembre 1700 de notre ère'],
            ['BEF @#DJULIAN@ NOV 1700', 'avant novembre 1700 de notre ère'],
            ['@#DJULIAN@ 15 DEC 1700', '15 décembre 1700 de notre ère'],
            ['@#DJULIAN@ DEC 1700', 'décembre 1700 de notre ère'],
            ['ABT @#DJULIAN@ DEC 1700', 'vers décembre 1700 de notre ère'],
            ['FROM @#DJULIAN@ DEC 1700', 'de décembre 1700 de notre ère'],
            ['AFT @#DJULIAN@ DEC 1700', 'après décembre 1700 de notre ère'],
            ['BEF @#DJULIAN@ DEC 1700', 'avant décembre 1700 de notre ère'],
            ['@#DJULIAN@ 1700', '1700 de notre ère'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'vers 15 janvier 1700 de notre ère'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'calculé 15 janvier 1700 de notre ère'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'estimé 15 janvier 1700 de notre ère'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'avant 15 janvier 1700 de notre ère'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'après 15 janvier 1700 de notre ère'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'de 15 janvier 1700 de notre ère'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'à 15 janvier 1700 de notre ère'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'entre 15 janvier 1700 de notre ère et 15 février 1700 de notre ère'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'de 15 janvier 1700 de notre ère à 15 février 1700 de notre ère'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'interprété 15 janvier 1700 de notre ère'],
            ['@#DHEBREW@ 15 TSH 5765', '15 Tichri 5765'],
            ['@#DHEBREW@ TSH 5765', 'Tichri 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'vers Tichri 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'de Tichri 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'après Tichri 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'avant Tichri 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 Hechvane 5765'],
            ['@#DHEBREW@ CSH 5765', 'Hechvane 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'vers Hechvane 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'de Hechvane 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'après Hechvane 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'avant Hechvane 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 Kislev 5765'],
            ['@#DHEBREW@ KSL 5765', 'Kislev 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'vers Kislev 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'de Kislev 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'après Kislev 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'avant Kislev 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 Téveth 5765'],
            ['@#DHEBREW@ TVT 5765', 'Téveth 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'vers Téveth 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'de Téveth 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'après Téveth 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'avant Téveth 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 Chevat 5765'],
            ['@#DHEBREW@ SHV 5765', 'Chevat 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'vers Chevat 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'de Chevat 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'après Chevat 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'avant Chevat 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 Adar I 5765'],
            ['@#DHEBREW@ ADR 5765', 'Adar I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'vers Adar I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'de Adar I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'après Adar I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'avant Adar I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 Adar II 5765'],
            ['@#DHEBREW@ ADS 5765', 'Adar II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'vers Adar II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'de Adar II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'après Adar II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'avant Adar II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 Nissan 5765'],
            ['@#DHEBREW@ NSN 5765', 'Nissan 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'vers Nissan 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'de Nissan 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'après Nissan 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'avant Nissan 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 Iyar 5765'],
            ['@#DHEBREW@ IYR 5765', 'Iyar 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'vers Iyar 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'de Iyar 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'après Iyar 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'avant Iyar 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 Sivane 5765'],
            ['@#DHEBREW@ SVN 5765', 'Sivane 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'vers Sivane 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'de Sivane 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'après Sivane 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'avant Sivane 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 Tammouz 5765'],
            ['@#DHEBREW@ TMZ 5765', 'Tammouz 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'vers Tammouz 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'de Tammouz 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'après Tammouz 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'avant Tammouz 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 Av 5765'],
            ['@#DHEBREW@ AAV 5765', 'Av 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'vers Av 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'de Av 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'après Av 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'avant Av 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 Eloul 5765'],
            ['@#DHEBREW@ ELL 5765', 'Eloul 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'vers Eloul 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'de Eloul 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'après Eloul 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'avant Eloul 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'vers 15 Tichri 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'calculé 15 Tichri 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'estimé 15 Tichri 5765'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'avant 15 Tichri 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'après 15 Tichri 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'de 15 Tichri 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'à 15 Tichri 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'entre 15 Tichri 5765 et 15 Hechvane 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'de 15 Tichri 5765 à 15 Hechvane 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'interprété 15 Tichri 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 vendémiaire An XII'],
            ['@#DFRENCH R@ VEND 12', 'vendémiaire An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'vers vendémiaire An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'de vendémiaire An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'après vendémiaire An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'avant vendémiaire An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 brumaire An XII'],
            ['@#DFRENCH R@ BRUM 12', 'brumaire An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'vers brumaire An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'de brumaire An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'après brumaire An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'avant brumaire An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 frimaire An XII'],
            ['@#DFRENCH R@ FRIM 12', 'frimaire An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'vers frimaire An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'de frimaire An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'après frimaire An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'avant frimaire An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 nivôse An XII'],
            ['@#DFRENCH R@ NIVO 12', 'nivôse An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'vers nivôse An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'de nivôse An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'après nivôse An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'avant nivôse An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 pluviôse An XII'],
            ['@#DFRENCH R@ PLUV 12', 'pluviôse An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'vers pluviôse An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'de pluviôse An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'après pluviôse An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'avant pluviôse An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 ventôse An XII'],
            ['@#DFRENCH R@ VENT 12', 'ventôse An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'vers ventôse An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'de ventôse An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'après ventôse An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'avant ventôse An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 germinal An XII'],
            ['@#DFRENCH R@ GERM 12', 'germinal An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'vers germinal An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'de germinal An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'après germinal An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'avant germinal An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 floréal An XII'],
            ['@#DFRENCH R@ FLOR 12', 'floréal An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'vers floréal An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'de floréal An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'après floréal An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'avant floréal An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 prairial An XII'],
            ['@#DFRENCH R@ PRAI 12', 'prairial An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'vers prairial An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'de prairial An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'après prairial An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'avant prairial An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 messidor An XII'],
            ['@#DFRENCH R@ MESS 12', 'messidor An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'vers messidor An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'de messidor An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'après messidor An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'avant messidor An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 thermidor An XII'],
            ['@#DFRENCH R@ THER 12', 'thermidor An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'vers thermidor An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'de thermidor An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'après thermidor An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'avant thermidor An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 fructidor An XII'],
            ['@#DFRENCH R@ FRUC 12', 'fructidor An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'vers fructidor An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'de fructidor An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'après fructidor An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'avant fructidor An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 jours complémentaires An XII'],
            ['@#DFRENCH R@ COMP 12', 'jours complémentaires An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'vers jours complémentaires An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'de jours complémentaires An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'après jours complémentaires An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'avant jours complémentaires An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'vers 15 vendémiaire An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'calculé 15 vendémiaire An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'estimé 15 vendémiaire An XII'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'avant 15 vendémiaire An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'après 15 vendémiaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'de 15 vendémiaire An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'à 15 vendémiaire An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'entre 15 vendémiaire An XII et 15 brumaire An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'de 15 vendémiaire An XII à 15 brumaire An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'interprété 15 vendémiaire An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Mouharram 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Mouharram 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'vers Mouharram 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'de Mouharram 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'après Mouharram 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'avant Mouharram 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Safar 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Safar 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'vers Safar 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'de Safar 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'après Safar 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'avant Safar 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Rabia al-awal 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Rabia al-awal 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'vers Rabia al-awal 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'de Rabia al-awal 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'après Rabia al-awal 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'avant Rabia al-awal 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Rabia ath-thani 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Rabia ath-thani 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'vers Rabia ath-thani 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'de Rabia ath-thani 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'après Rabia ath-thani 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'avant Rabia ath-thani 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Joumada al-oula 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Joumada al-oula 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'vers Joumada al-oula 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'de Joumada al-oula 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'après Joumada al-oula 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'avant Joumada al-oula 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Joumada ath-thania 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Joumada ath-thania 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'vers Joumada ath-thania 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'de Joumada ath-thania 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'après Joumada ath-thania 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'avant Joumada ath-thania 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Rajab 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Rajab 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'vers Rajab 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'de Rajab 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'après Rajab 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'avant Rajab 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Chaabane 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Chaabane 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'vers Chaabane 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'de Chaabane 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'après Chaabane 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'avant Chaabane 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Ramadan 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Ramadan 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'vers Ramadan 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'de Ramadan 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'après Ramadan 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'avant Ramadan 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Chawwal 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Chawwal 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'vers Chawwal 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'de Chawwal 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'après Chawwal 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'avant Chawwal 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Dhou al-qi’da 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Dhou al-qi’da 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'vers Dhou al-qi’da 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'de Dhou al-qi’da 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'après Dhou al-qi’da 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'avant Dhou al-qi’da 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'vers 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'de 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'après 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'avant 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'vers 15 Mouharram 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'calculé 15 Mouharram 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'estimé 15 Mouharram 1425'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'avant 15 Mouharram 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'après 15 Mouharram 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'de 15 Mouharram 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'à 15 Mouharram 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'entre 15 Mouharram 1425 et 15 Safar 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'de 15 Mouharram 1425 à 15 Safar 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'interprété 15 Mouharram 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Farvardin 1384'],
            ['@#DJALALI@ FARVA 1384', 'Farvardin 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'vers Farvardin 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'de Farvardin 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'après Farvardin 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'avant Farvardin 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ordibehesht 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ordibehesht 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'vers Ordibehesht 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'de Ordibehesht 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'après Ordibehesht 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'avant Ordibehesht 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Khordad 1384'],
            ['@#DJALALI@ KHORD 1384', 'Khordad 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'vers Khordad 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'de Khordad 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'après Khordad 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'avant Khordad 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Tir 1384'],
            ['@#DJALALI@ TIR 1384', 'Tir 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'vers Tir 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'de Tir 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'après Tir 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'avant Tir 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Mordad 1384'],
            ['@#DJALALI@ MORDA 1384', 'Mordad 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'vers Mordad 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'de Mordad 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'après Mordad 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'avant Mordad 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Shahrivar 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Shahrivar 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'vers Shahrivar 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'de Shahrivar 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'après Shahrivar 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'avant Shahrivar 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Mehr 1384'],
            ['@#DJALALI@ MEHR 1384', 'Mehr 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'vers Mehr 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'de Mehr 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'après Mehr 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'avant Mehr 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Âbân 1384'],
            ['@#DJALALI@ ABAN 1384', 'Âbân 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'vers Âbân 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'de Âbân 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'après Âbân 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'avant Âbân 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Âzar 1384'],
            ['@#DJALALI@ AZAR 1384', 'Âzar 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'vers Âzar 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'de Âzar 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'après Âzar 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'avant Âzar 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Dey 1384'],
            ['@#DJALALI@ DEY 1384', 'Dey 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'vers Dey 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'de Dey 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'après Dey 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'avant Dey 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Bahman 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Bahman 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'vers Bahman 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'de Bahman 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'après Bahman 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'avant Bahman 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Esfand 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Esfand 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'vers Esfand 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'de Esfand 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'après Esfand 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'avant Esfand 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'vers 15 Farvardin 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'calculé 15 Farvardin 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'estimé 15 Farvardin 1384'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'avant 15 Farvardin 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'après 15 Farvardin 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'de 15 Farvardin 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'à 15 Farvardin 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'entre 15 Farvardin 1384 et 15 Ordibehesht 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'de 15 Farvardin 1384 à 15 Ordibehesht 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'interprété 15 Farvardin 1384'],
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
        self::assertSame('one et two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two et three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one ou two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two ou three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@");
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
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('épouse', 'époux', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-époux', 'ex-épouse', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('fiancée', 'fiancé', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mère', 'fils', [$son, $fm, $wife]);
        self::assertRelationshipNames('père', 'fils', [$son, $fm, $husband]);
        self::assertRelationshipNames('mère', 'fille', [$daughter, $fm, $wife]);

        // Siblings
        self::assertRelationshipNames('petite sœur', 'grand frère', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('demi-frère', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('beau-père', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('belle-fille', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipName('belle-mère', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipName('beau-père', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('bru', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents (dynamic, paternal/maternal)
        self::assertRelationshipName('grand-père paternel', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('grand-mère maternelle', [$son, $fm, $wife, $fw, $motherOfW]);

        // Great-grandparents (dynamic)
        self::assertRelationshipName('arrière-grand-père paternel', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('arrière-grand-mère paternelle', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles (dynamic)
        self::assertRelationshipName('tante', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('oncle', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('grand-tante', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('grand-oncle', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);

        // Nieces and nephews
        self::assertRelationshipName('nièce', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('neveu', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins (canon law: first cousin = cousin germain)
        self::assertRelationshipName('cousine germaine', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('cousin germain', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }
}
