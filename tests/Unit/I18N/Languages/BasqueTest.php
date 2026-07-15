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
use Fisharebest\Webtrees\I18N\Languages\Basque;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Basque::class)]
class BasqueTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Basque();
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
        self::assertSame('eu', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('euskara', self::language()->endonym());
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
        self::assertSame('−123.456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('% −123.456,0789', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '2000 Urtarrila 15'],
            ['JAN 2000', '2000 Urtarrila'],
            ['ABT JAN 2000', '2000 Urtarrilaaren inguruan'],
            ['FROM JAN 2000', '2000 Urtarrilatik hasita'],
            ['AFT JAN 2000', '2000 Urtarrilaren ondotik'],
            ['BEF JAN 2000', '2000 Urtarrilaaren aitzinetik'],
            ['15 FEB 2000', '2000 Otsaila 15'],
            ['FEB 2000', '2000 Otsaila'],
            ['ABT FEB 2000', '2000 Otsailaaren inguruan'],
            ['FROM FEB 2000', '2000 Otsailatik hasita'],
            ['AFT FEB 2000', '2000 Otsailaren ondotik'],
            ['BEF FEB 2000', '2000 Otsailaaren aitzinetik'],
            ['15 MAR 2000', '2000 Martxoa 15'],
            ['MAR 2000', '2000 Martxoa'],
            ['ABT MAR 2000', '2000 Martxoaaren inguruan'],
            ['FROM MAR 2000', '2000 Martxoatik hasita'],
            ['AFT MAR 2000', '2000 Martxoaren ondotik'],
            ['BEF MAR 2000', '2000 Martxoaaren aitzinetik'],
            ['15 APR 2000', '2000 Apirila 15'],
            ['APR 2000', '2000 Apirila'],
            ['ABT APR 2000', '2000 Apirilaaren inguruan'],
            ['FROM APR 2000', '2000 Apirilatik hasita'],
            ['AFT APR 2000', '2000 Apirilaren ondotik'],
            ['BEF APR 2000', '2000 Apirilaaren aitzinetik'],
            ['15 MAY 2000', '2000 Maiatza 15'],
            ['MAY 2000', '2000 Maiatza'],
            ['ABT MAY 2000', '2000 Maiatzaaren inguruan'],
            ['FROM MAY 2000', '2000 Maiatzatik hasita'],
            ['AFT MAY 2000', '2000 Maiatzaren ondotik'],
            ['BEF MAY 2000', '2000 Maiatzaaren aitzinetik'],
            ['15 JUN 2000', '2000 Ekaina 15'],
            ['JUN 2000', '2000 Ekaina'],
            ['ABT JUN 2000', '2000 Ekainaaren inguruan'],
            ['FROM JUN 2000', '2000 Ekainatik hasita'],
            ['AFT JUN 2000', '2000 Ekainaren ondotik'],
            ['BEF JUN 2000', '2000 Ekainaaren aitzinetik'],
            ['15 JUL 2000', '2000 Uztaila 15'],
            ['JUL 2000', '2000 Uztaila'],
            ['ABT JUL 2000', '2000 Uztailaaren inguruan'],
            ['FROM JUL 2000', '2000 Uztailatik hasita'],
            ['AFT JUL 2000', '2000 Uztailaren ondotik'],
            ['BEF JUL 2000', '2000 Uztailaaren aitzinetik'],
            ['15 AUG 2000', '2000 Abuztua 15'],
            ['AUG 2000', '2000 Abuztua'],
            ['ABT AUG 2000', '2000 Abuztuaaren inguruan'],
            ['FROM AUG 2000', '2000 Abuztuatik hasita'],
            ['AFT AUG 2000', '2000 Abuztuaren ondotik'],
            ['BEF AUG 2000', '2000 Abuztuaaren aitzinetik'],
            ['15 SEP 2000', '2000 Iraila 15'],
            ['SEP 2000', '2000 Iraila'],
            ['ABT SEP 2000', '2000 Irailaaren inguruan'],
            ['FROM SEP 2000', '2000 Irailatik hasita'],
            ['AFT SEP 2000', '2000 Irailaren ondotik'],
            ['BEF SEP 2000', '2000 Irailaaren aitzinetik'],
            ['15 OCT 2000', '2000 Urria 15'],
            ['OCT 2000', '2000 Urria'],
            ['ABT OCT 2000', '2000 Urriaaren inguruan'],
            ['FROM OCT 2000', '2000 Urriatik hasita'],
            ['AFT OCT 2000', '2000 Urriaren ondotik'],
            ['BEF OCT 2000', '2000 Urriaaren aitzinetik'],
            ['15 NOV 2000', '2000 Azaroa 15'],
            ['NOV 2000', '2000 Azaroa'],
            ['ABT NOV 2000', '2000 Azaroaaren inguruan'],
            ['FROM NOV 2000', '2000 Azaroatik hasita'],
            ['AFT NOV 2000', '2000 Azaroaren ondotik'],
            ['BEF NOV 2000', '2000 Azaroaaren aitzinetik'],
            ['15 DEC 2000', '2000 Abendua 15'],
            ['DEC 2000', '2000 Abendua'],
            ['ABT DEC 2000', '2000 Abenduaaren inguruan'],
            ['FROM DEC 2000', '2000 Abenduatik hasita'],
            ['AFT DEC 2000', '2000 Abenduaren ondotik'],
            ['BEF DEC 2000', '2000 Abenduaaren aitzinetik'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', '2000 Urtarrila 15aren inguruan'],
            ['CAL 15 JAN 2000', '2000 Urtarrila 15 kalkulatuak'],
            ['EST 15 JAN 2000', '2000 Urtarrila 15 guti gora behera'],
            ['BEF 15 JAN 2000', '2000 Urtarrila 15aren aitzinetik'],
            ['AFT 15 JAN 2000', '2000 Urtarrila 15ren ondotik'],
            ['FROM 15 JAN 2000', '2000 Urtarrila 15tik hasita'],
            ['TO 15 JAN 2000', '2000 Urtarrila 15 arte'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', '2000 Urtarrila 15 eta 2000 Otsaila 15ren artean'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', '2000 Urtarrila 15tik hasita 2000 Otsaila 15(e)ra'],
            ['INT 15 JAN 2000', '2000 Urtarrila 15 interpretatuak'],
            ['@#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15'],
            ['@#DJULIAN@ JAN 1700', '1700 EC Urtarrila'],
            ['ABT @#DJULIAN@ JAN 1700', '1700 EC Urtarrilaaren inguruan'],
            ['FROM @#DJULIAN@ JAN 1700', '1700 EC Urtarrilatik hasita'],
            ['AFT @#DJULIAN@ JAN 1700', '1700 EC Urtarrilaren ondotik'],
            ['BEF @#DJULIAN@ JAN 1700', '1700 EC Urtarrilaaren aitzinetik'],
            ['@#DJULIAN@ 15 FEB 1700', '1700 EC Otsaila 15'],
            ['@#DJULIAN@ FEB 1700', '1700 EC Otsaila'],
            ['ABT @#DJULIAN@ FEB 1700', '1700 EC Otsailaaren inguruan'],
            ['FROM @#DJULIAN@ FEB 1700', '1700 EC Otsailatik hasita'],
            ['AFT @#DJULIAN@ FEB 1700', '1700 EC Otsailaren ondotik'],
            ['BEF @#DJULIAN@ FEB 1700', '1700 EC Otsailaaren aitzinetik'],
            ['@#DJULIAN@ 15 MAR 1700', '1700 EC Martxoa 15'],
            ['@#DJULIAN@ MAR 1700', '1700 EC Martxoa'],
            ['ABT @#DJULIAN@ MAR 1700', '1700 EC Martxoaaren inguruan'],
            ['FROM @#DJULIAN@ MAR 1700', '1700 EC Martxoatik hasita'],
            ['AFT @#DJULIAN@ MAR 1700', '1700 EC Martxoaren ondotik'],
            ['BEF @#DJULIAN@ MAR 1700', '1700 EC Martxoaaren aitzinetik'],
            ['@#DJULIAN@ 15 APR 1700', '1700 EC Apirila 15'],
            ['@#DJULIAN@ 14 APR 1645/46', '1645/46 EC Apirila 14'],
            ['@#DJULIAN@ APR 1700', '1700 EC Apirila'],
            ['ABT @#DJULIAN@ APR 1700', '1700 EC Apirilaaren inguruan'],
            ['FROM @#DJULIAN@ APR 1700', '1700 EC Apirilatik hasita'],
            ['AFT @#DJULIAN@ APR 1700', '1700 EC Apirilaren ondotik'],
            ['BEF @#DJULIAN@ APR 1700', '1700 EC Apirilaaren aitzinetik'],
            ['@#DJULIAN@ 15 MAY 1700', '1700 EC Maiatza 15'],
            ['@#DJULIAN@ MAY 1700', '1700 EC Maiatza'],
            ['ABT @#DJULIAN@ MAY 1700', '1700 EC Maiatzaaren inguruan'],
            ['FROM @#DJULIAN@ MAY 1700', '1700 EC Maiatzatik hasita'],
            ['AFT @#DJULIAN@ MAY 1700', '1700 EC Maiatzaren ondotik'],
            ['BEF @#DJULIAN@ MAY 1700', '1700 EC Maiatzaaren aitzinetik'],
            ['@#DJULIAN@ 15 JUN 1700', '1700 EC Ekaina 15'],
            ['@#DJULIAN@ JUN 1700', '1700 EC Ekaina'],
            ['ABT @#DJULIAN@ JUN 1700', '1700 EC Ekainaaren inguruan'],
            ['FROM @#DJULIAN@ JUN 1700', '1700 EC Ekainatik hasita'],
            ['AFT @#DJULIAN@ JUN 1700', '1700 EC Ekainaren ondotik'],
            ['BEF @#DJULIAN@ JUN 1700', '1700 EC Ekainaaren aitzinetik'],
            ['@#DJULIAN@ 15 JUL 1700', '1700 EC Uztaila 15'],
            ['@#DJULIAN@ JUL 1700', '1700 EC Uztaila'],
            ['ABT @#DJULIAN@ JUL 1700', '1700 EC Uztailaaren inguruan'],
            ['FROM @#DJULIAN@ JUL 1700', '1700 EC Uztailatik hasita'],
            ['AFT @#DJULIAN@ JUL 1700', '1700 EC Uztailaren ondotik'],
            ['BEF @#DJULIAN@ JUL 1700', '1700 EC Uztailaaren aitzinetik'],
            ['@#DJULIAN@ 15 AUG 1700', '1700 EC Abuztua 15'],
            ['@#DJULIAN@ AUG 1700', '1700 EC Abuztua'],
            ['ABT @#DJULIAN@ AUG 1700', '1700 EC Abuztuaaren inguruan'],
            ['FROM @#DJULIAN@ AUG 1700', '1700 EC Abuztuatik hasita'],
            ['AFT @#DJULIAN@ AUG 1700', '1700 EC Abuztuaren ondotik'],
            ['BEF @#DJULIAN@ AUG 1700', '1700 EC Abuztuaaren aitzinetik'],
            ['@#DJULIAN@ 15 SEP 1700', '1700 EC Iraila 15'],
            ['@#DJULIAN@ SEP 1700', '1700 EC Iraila'],
            ['ABT @#DJULIAN@ SEP 1700', '1700 EC Irailaaren inguruan'],
            ['FROM @#DJULIAN@ SEP 1700', '1700 EC Irailatik hasita'],
            ['AFT @#DJULIAN@ SEP 1700', '1700 EC Irailaren ondotik'],
            ['BEF @#DJULIAN@ SEP 1700', '1700 EC Irailaaren aitzinetik'],
            ['@#DJULIAN@ 15 OCT 1700', '1700 EC Urria 15'],
            ['@#DJULIAN@ OCT 1700', '1700 EC Urria'],
            ['ABT @#DJULIAN@ OCT 1700', '1700 EC Urriaaren inguruan'],
            ['FROM @#DJULIAN@ OCT 1700', '1700 EC Urriatik hasita'],
            ['AFT @#DJULIAN@ OCT 1700', '1700 EC Urriaren ondotik'],
            ['BEF @#DJULIAN@ OCT 1700', '1700 EC Urriaaren aitzinetik'],
            ['@#DJULIAN@ 15 NOV 1700', '1700 EC Azaroa 15'],
            ['@#DJULIAN@ NOV 1700', '1700 EC Azaroa'],
            ['ABT @#DJULIAN@ NOV 1700', '1700 EC Azaroaaren inguruan'],
            ['FROM @#DJULIAN@ NOV 1700', '1700 EC Azaroatik hasita'],
            ['AFT @#DJULIAN@ NOV 1700', '1700 EC Azaroaren ondotik'],
            ['BEF @#DJULIAN@ NOV 1700', '1700 EC Azaroaaren aitzinetik'],
            ['@#DJULIAN@ 15 DEC 1700', '1700 EC Abendua 15'],
            ['@#DJULIAN@ DEC 1700', '1700 EC Abendua'],
            ['ABT @#DJULIAN@ DEC 1700', '1700 EC Abenduaaren inguruan'],
            ['FROM @#DJULIAN@ DEC 1700', '1700 EC Abenduatik hasita'],
            ['AFT @#DJULIAN@ DEC 1700', '1700 EC Abenduaren ondotik'],
            ['BEF @#DJULIAN@ DEC 1700', '1700 EC Abenduaaren aitzinetik'],
            ['@#DJULIAN@ 1700', '1700 EC'],
            ['ABT @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15aren inguruan'],
            ['CAL @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15 kalkulatuak'],
            ['EST @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15 guti gora behera'],
            ['BEF @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15aren aitzinetik'],
            ['AFT @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15ren ondotik'],
            ['FROM @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15tik hasita'],
            ['TO @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15 arte'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', '1700 EC Urtarrila 15 eta 1700 EC Otsaila 15ren artean'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', '1700 EC Urtarrila 15tik hasita 1700 EC Otsaila 15(e)ra'],
            ['INT @#DJULIAN@ 15 JAN 1700', '1700 EC Urtarrila 15 interpretatuak'],
            ['@#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15'],
            ['@#DHEBREW@ TSH 5765', '5765 Tishrei'],
            ['ABT @#DHEBREW@ TSH 5765', '5765 Tishreiaren inguruan'],
            ['FROM @#DHEBREW@ TSH 5765', '5765 Tishreitik hasita'],
            ['AFT @#DHEBREW@ TSH 5765', '5765 Tishreiren ondotik'],
            ['BEF @#DHEBREW@ TSH 5765', '5765 Tishreiaren aitzinetik'],
            ['@#DHEBREW@ 15 CSH 5765', '5765 Heshvan 15'],
            ['@#DHEBREW@ CSH 5765', '5765 Heshvan'],
            ['ABT @#DHEBREW@ CSH 5765', '5765 Heshvanaren inguruan'],
            ['FROM @#DHEBREW@ CSH 5765', '5765 Heshvantik hasita'],
            ['AFT @#DHEBREW@ CSH 5765', '5765 Heshvanren ondotik'],
            ['BEF @#DHEBREW@ CSH 5765', '5765 Heshvanaren aitzinetik'],
            ['@#DHEBREW@ 15 KSL 5765', '5765 Kislev 15'],
            ['@#DHEBREW@ KSL 5765', '5765 Kislev'],
            ['ABT @#DHEBREW@ KSL 5765', '5765 Kislevaren inguruan'],
            ['FROM @#DHEBREW@ KSL 5765', '5765 Kislevtik hasita'],
            ['AFT @#DHEBREW@ KSL 5765', '5765 Kislevren ondotik'],
            ['BEF @#DHEBREW@ KSL 5765', '5765 Kislevaren aitzinetik'],
            ['@#DHEBREW@ 15 TVT 5765', '5765 Tevet 15'],
            ['@#DHEBREW@ TVT 5765', '5765 Tevet'],
            ['ABT @#DHEBREW@ TVT 5765', '5765 Tevetaren inguruan'],
            ['FROM @#DHEBREW@ TVT 5765', '5765 Tevettik hasita'],
            ['AFT @#DHEBREW@ TVT 5765', '5765 Tevetren ondotik'],
            ['BEF @#DHEBREW@ TVT 5765', '5765 Tevetaren aitzinetik'],
            ['@#DHEBREW@ 15 SHV 5765', '5765 Shevat 15'],
            ['@#DHEBREW@ SHV 5765', '5765 Shevat'],
            ['ABT @#DHEBREW@ SHV 5765', '5765 Shevataren inguruan'],
            ['FROM @#DHEBREW@ SHV 5765', '5765 Shevattik hasita'],
            ['AFT @#DHEBREW@ SHV 5765', '5765 Shevatren ondotik'],
            ['BEF @#DHEBREW@ SHV 5765', '5765 Shevataren aitzinetik'],
            ['@#DHEBREW@ 15 ADR 5765', '5765 Adar I 15'],
            ['@#DHEBREW@ ADR 5765', '5765 Adar I'],
            ['ABT @#DHEBREW@ ADR 5765', '5765 Adar Iaren inguruan'],
            ['FROM @#DHEBREW@ ADR 5765', '5765 Adar Itik hasita'],
            ['AFT @#DHEBREW@ ADR 5765', '5765 Adar Iren ondotik'],
            ['BEF @#DHEBREW@ ADR 5765', '5765 Adar Iaren aitzinetik'],
            ['@#DHEBREW@ 15 ADS 5765', '5765 Adar II 15'],
            ['@#DHEBREW@ ADS 5765', '5765 Adar II'],
            ['ABT @#DHEBREW@ ADS 5765', '5765 Adar IIaren inguruan'],
            ['FROM @#DHEBREW@ ADS 5765', '5765 Adar IItik hasita'],
            ['AFT @#DHEBREW@ ADS 5765', '5765 Adar IIren ondotik'],
            ['BEF @#DHEBREW@ ADS 5765', '5765 Adar IIaren aitzinetik'],
            ['@#DHEBREW@ 15 NSN 5765', '5765 Nissan 15'],
            ['@#DHEBREW@ NSN 5765', '5765 Nissan'],
            ['ABT @#DHEBREW@ NSN 5765', '5765 Nissanaren inguruan'],
            ['FROM @#DHEBREW@ NSN 5765', '5765 Nissantik hasita'],
            ['AFT @#DHEBREW@ NSN 5765', '5765 Nissanren ondotik'],
            ['BEF @#DHEBREW@ NSN 5765', '5765 Nissanaren aitzinetik'],
            ['@#DHEBREW@ 15 IYR 5765', '5765 Iyar 15'],
            ['@#DHEBREW@ IYR 5765', '5765 Iyar'],
            ['ABT @#DHEBREW@ IYR 5765', '5765 Iyararen inguruan'],
            ['FROM @#DHEBREW@ IYR 5765', '5765 Iyartik hasita'],
            ['AFT @#DHEBREW@ IYR 5765', '5765 Iyarren ondotik'],
            ['BEF @#DHEBREW@ IYR 5765', '5765 Iyararen aitzinetik'],
            ['@#DHEBREW@ 15 SVN 5765', '5765 Sivan 15'],
            ['@#DHEBREW@ SVN 5765', '5765 Sivan'],
            ['ABT @#DHEBREW@ SVN 5765', '5765 Sivanaren inguruan'],
            ['FROM @#DHEBREW@ SVN 5765', '5765 Sivantik hasita'],
            ['AFT @#DHEBREW@ SVN 5765', '5765 Sivanren ondotik'],
            ['BEF @#DHEBREW@ SVN 5765', '5765 Sivanaren aitzinetik'],
            ['@#DHEBREW@ 15 TMZ 5765', '5765 Tamuz 15'],
            ['@#DHEBREW@ TMZ 5765', '5765 Tamuz'],
            ['ABT @#DHEBREW@ TMZ 5765', '5765 Tamuzaren inguruan'],
            ['FROM @#DHEBREW@ TMZ 5765', '5765 Tamuztik hasita'],
            ['AFT @#DHEBREW@ TMZ 5765', '5765 Tamuzren ondotik'],
            ['BEF @#DHEBREW@ TMZ 5765', '5765 Tamuzaren aitzinetik'],
            ['@#DHEBREW@ 15 AAV 5765', '5765 Av 15'],
            ['@#DHEBREW@ AAV 5765', '5765 Av'],
            ['ABT @#DHEBREW@ AAV 5765', '5765 Avaren inguruan'],
            ['FROM @#DHEBREW@ AAV 5765', '5765 Avtik hasita'],
            ['AFT @#DHEBREW@ AAV 5765', '5765 Avren ondotik'],
            ['BEF @#DHEBREW@ AAV 5765', '5765 Avaren aitzinetik'],
            ['@#DHEBREW@ 15 ELL 5765', '5765 Elul 15'],
            ['@#DHEBREW@ ELL 5765', '5765 Elul'],
            ['ABT @#DHEBREW@ ELL 5765', '5765 Elularen inguruan'],
            ['FROM @#DHEBREW@ ELL 5765', '5765 Elultik hasita'],
            ['AFT @#DHEBREW@ ELL 5765', '5765 Elulren ondotik'],
            ['BEF @#DHEBREW@ ELL 5765', '5765 Elularen aitzinetik'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15aren inguruan'],
            ['CAL @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15 kalkulatuak'],
            ['EST @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15 guti gora behera'],
            ['BEF @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15aren aitzinetik'],
            ['AFT @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15ren ondotik'],
            ['FROM @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15tik hasita'],
            ['TO @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15 arte'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', '5765 Tishrei 15 eta 5765 Heshvan 15ren artean'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', '5765 Tishrei 15tik hasita 5765 Heshvan 15(e)ra'],
            ['INT @#DHEBREW@ 15 TSH 5765', '5765 Tishrei 15 interpretatuak'],
            ['@#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15'],
            ['@#DFRENCH R@ VEND 12', 'An XII Vendémiaire'],
            ['ABT @#DFRENCH R@ VEND 12', 'An XII Vendémiairearen inguruan'],
            ['FROM @#DFRENCH R@ VEND 12', 'An XII Vendémiairetik hasita'],
            ['AFT @#DFRENCH R@ VEND 12', 'An XII Vendémiaireren ondotik'],
            ['BEF @#DFRENCH R@ VEND 12', 'An XII Vendémiairearen aitzinetik'],
            ['@#DFRENCH R@ 15 BRUM 12', 'An XII Brumaire 15'],
            ['@#DFRENCH R@ BRUM 12', 'An XII Brumaire'],
            ['ABT @#DFRENCH R@ BRUM 12', 'An XII Brumairearen inguruan'],
            ['FROM @#DFRENCH R@ BRUM 12', 'An XII Brumairetik hasita'],
            ['AFT @#DFRENCH R@ BRUM 12', 'An XII Brumaireren ondotik'],
            ['BEF @#DFRENCH R@ BRUM 12', 'An XII Brumairearen aitzinetik'],
            ['@#DFRENCH R@ 15 FRIM 12', 'An XII Frimaire 15'],
            ['@#DFRENCH R@ FRIM 12', 'An XII Frimaire'],
            ['ABT @#DFRENCH R@ FRIM 12', 'An XII Frimairearen inguruan'],
            ['FROM @#DFRENCH R@ FRIM 12', 'An XII Frimairetik hasita'],
            ['AFT @#DFRENCH R@ FRIM 12', 'An XII Frimaireren ondotik'],
            ['BEF @#DFRENCH R@ FRIM 12', 'An XII Frimairearen aitzinetik'],
            ['@#DFRENCH R@ 15 NIVO 12', 'An XII Nivôse 15'],
            ['@#DFRENCH R@ NIVO 12', 'An XII Nivôse'],
            ['ABT @#DFRENCH R@ NIVO 12', 'An XII Nivôsearen inguruan'],
            ['FROM @#DFRENCH R@ NIVO 12', 'An XII Nivôsetik hasita'],
            ['AFT @#DFRENCH R@ NIVO 12', 'An XII Nivôseren ondotik'],
            ['BEF @#DFRENCH R@ NIVO 12', 'An XII Nivôsearen aitzinetik'],
            ['@#DFRENCH R@ 15 PLUV 12', 'An XII Pluviôse 15'],
            ['@#DFRENCH R@ PLUV 12', 'An XII Pluviôse'],
            ['ABT @#DFRENCH R@ PLUV 12', 'An XII Pluviôsearen inguruan'],
            ['FROM @#DFRENCH R@ PLUV 12', 'An XII Pluviôsetik hasita'],
            ['AFT @#DFRENCH R@ PLUV 12', 'An XII Pluviôseren ondotik'],
            ['BEF @#DFRENCH R@ PLUV 12', 'An XII Pluviôsearen aitzinetik'],
            ['@#DFRENCH R@ 15 VENT 12', 'An XII Ventôse 15'],
            ['@#DFRENCH R@ VENT 12', 'An XII Ventôse'],
            ['ABT @#DFRENCH R@ VENT 12', 'An XII Ventôsearen inguruan'],
            ['FROM @#DFRENCH R@ VENT 12', 'An XII Ventôsetik hasita'],
            ['AFT @#DFRENCH R@ VENT 12', 'An XII Ventôseren ondotik'],
            ['BEF @#DFRENCH R@ VENT 12', 'An XII Ventôsearen aitzinetik'],
            ['@#DFRENCH R@ 15 GERM 12', 'An XII Germinal 15'],
            ['@#DFRENCH R@ GERM 12', 'An XII Germinal'],
            ['ABT @#DFRENCH R@ GERM 12', 'An XII Germinalaren inguruan'],
            ['FROM @#DFRENCH R@ GERM 12', 'An XII Germinaltik hasita'],
            ['AFT @#DFRENCH R@ GERM 12', 'An XII Germinalren ondotik'],
            ['BEF @#DFRENCH R@ GERM 12', 'An XII Germinalaren aitzinetik'],
            ['@#DFRENCH R@ 15 FLOR 12', 'An XII Floréal 15'],
            ['@#DFRENCH R@ FLOR 12', 'An XII Floréal'],
            ['ABT @#DFRENCH R@ FLOR 12', 'An XII Floréalaren inguruan'],
            ['FROM @#DFRENCH R@ FLOR 12', 'An XII Floréaltik hasita'],
            ['AFT @#DFRENCH R@ FLOR 12', 'An XII Floréalren ondotik'],
            ['BEF @#DFRENCH R@ FLOR 12', 'An XII Floréalaren aitzinetik'],
            ['@#DFRENCH R@ 15 PRAI 12', 'An XII Prairial 15'],
            ['@#DFRENCH R@ PRAI 12', 'An XII Prairial'],
            ['ABT @#DFRENCH R@ PRAI 12', 'An XII Prairialaren inguruan'],
            ['FROM @#DFRENCH R@ PRAI 12', 'An XII Prairialtik hasita'],
            ['AFT @#DFRENCH R@ PRAI 12', 'An XII Prairialren ondotik'],
            ['BEF @#DFRENCH R@ PRAI 12', 'An XII Prairialaren aitzinetik'],
            ['@#DFRENCH R@ 15 MESS 12', 'An XII Messidor 15'],
            ['@#DFRENCH R@ MESS 12', 'An XII Messidor'],
            ['ABT @#DFRENCH R@ MESS 12', 'An XII Messidoraren inguruan'],
            ['FROM @#DFRENCH R@ MESS 12', 'An XII Messidortik hasita'],
            ['AFT @#DFRENCH R@ MESS 12', 'An XII Messidorren ondotik'],
            ['BEF @#DFRENCH R@ MESS 12', 'An XII Messidoraren aitzinetik'],
            ['@#DFRENCH R@ 15 THER 12', 'An XII Termidor 15'],
            ['@#DFRENCH R@ THER 12', 'An XII Termidor'],
            ['ABT @#DFRENCH R@ THER 12', 'An XII Termidoraren inguruan'],
            ['FROM @#DFRENCH R@ THER 12', 'An XII Termidortik hasita'],
            ['AFT @#DFRENCH R@ THER 12', 'An XII Termidorren ondotik'],
            ['BEF @#DFRENCH R@ THER 12', 'An XII Termidoraren aitzinetik'],
            ['@#DFRENCH R@ 15 FRUC 12', 'An XII Fructidor 15'],
            ['@#DFRENCH R@ FRUC 12', 'An XII Fructidor'],
            ['ABT @#DFRENCH R@ FRUC 12', 'An XII Fructidoraren inguruan'],
            ['FROM @#DFRENCH R@ FRUC 12', 'An XII Fructidortik hasita'],
            ['AFT @#DFRENCH R@ FRUC 12', 'An XII Fructidorren ondotik'],
            ['BEF @#DFRENCH R@ FRUC 12', 'An XII Fructidoraren aitzinetik'],
            ['@#DFRENCH R@ 15 COMP 12', 'An XII egun osagarriak 15'],
            ['@#DFRENCH R@ COMP 12', 'An XII egun osagarriak'],
            ['ABT @#DFRENCH R@ COMP 12', 'An XII egun osagarriakaren inguruan'],
            ['FROM @#DFRENCH R@ COMP 12', 'An XII egun osagarriaktik hasita'],
            ['AFT @#DFRENCH R@ COMP 12', 'An XII egun osagarriakren ondotik'],
            ['BEF @#DFRENCH R@ COMP 12', 'An XII egun osagarriakaren aitzinetik'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15aren inguruan'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15 kalkulatuak'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15 guti gora behera'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15aren aitzinetik'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15ren ondotik'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15tik hasita'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15 arte'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'An XII Vendémiaire 15 eta An XII Brumaire 15ren artean'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'An XII Vendémiaire 15tik hasita An XII Brumaire 15(e)ra'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'An XII Vendémiaire 15 interpretatuak'],
            ['@#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15'],
            ['@#DHIJRI@ MUHAR 1425', '1425 Muharram'],
            ['ABT @#DHIJRI@ MUHAR 1425', '1425 Muharramaren inguruan'],
            ['FROM @#DHIJRI@ MUHAR 1425', '1425 Muharramtik hasita'],
            ['AFT @#DHIJRI@ MUHAR 1425', '1425 Muharramren ondotik'],
            ['BEF @#DHIJRI@ MUHAR 1425', '1425 Muharramaren aitzinetik'],
            ['@#DHIJRI@ 15 SAFAR 1425', '1425 Safar 15'],
            ['@#DHIJRI@ SAFAR 1425', '1425 Safar'],
            ['ABT @#DHIJRI@ SAFAR 1425', '1425 Safararen inguruan'],
            ['FROM @#DHIJRI@ SAFAR 1425', '1425 Safartik hasita'],
            ['AFT @#DHIJRI@ SAFAR 1425', '1425 Safarren ondotik'],
            ['BEF @#DHIJRI@ SAFAR 1425', '1425 Safararen aitzinetik'],
            ['@#DHIJRI@ 15 RABIA 1425', '1425 Rabi’ al-awwal 15'],
            ['@#DHIJRI@ RABIA 1425', '1425 Rabi’ al-awwal'],
            ['ABT @#DHIJRI@ RABIA 1425', '1425 Rabi’ al-awwalaren inguruan'],
            ['FROM @#DHIJRI@ RABIA 1425', '1425 Rabi’ al-awwaltik hasita'],
            ['AFT @#DHIJRI@ RABIA 1425', '1425 Rabi’ al-awwalren ondotik'],
            ['BEF @#DHIJRI@ RABIA 1425', '1425 Rabi’ al-awwalaren aitzinetik'],
            ['@#DHIJRI@ 15 RABIT 1425', '1425 Rabi’ al-thani 15'],
            ['@#DHIJRI@ RABIT 1425', '1425 Rabi’ al-thani'],
            ['ABT @#DHIJRI@ RABIT 1425', '1425 Rabi’ al-thaniaren inguruan'],
            ['FROM @#DHIJRI@ RABIT 1425', '1425 Rabi’ al-thanitik hasita'],
            ['AFT @#DHIJRI@ RABIT 1425', '1425 Rabi’ al-thaniren ondotik'],
            ['BEF @#DHIJRI@ RABIT 1425', '1425 Rabi’ al-thaniaren aitzinetik'],
            ['@#DHIJRI@ 15 JUMAA 1425', '1425 Jumada al-awwal 15'],
            ['@#DHIJRI@ JUMAA 1425', '1425 Jumada al-awwal'],
            ['ABT @#DHIJRI@ JUMAA 1425', '1425 Jumada al-awwalaren inguruan'],
            ['FROM @#DHIJRI@ JUMAA 1425', '1425 Jumada al-awwaltik hasita'],
            ['AFT @#DHIJRI@ JUMAA 1425', '1425 Jumada al-awwalren ondotik'],
            ['BEF @#DHIJRI@ JUMAA 1425', '1425 Jumada al-awwalaren aitzinetik'],
            ['@#DHIJRI@ 15 JUMAT 1425', '1425 Jumada al-thani 15'],
            ['@#DHIJRI@ JUMAT 1425', '1425 Jumada al-thani'],
            ['ABT @#DHIJRI@ JUMAT 1425', '1425 Jumada al-thaniaren inguruan'],
            ['FROM @#DHIJRI@ JUMAT 1425', '1425 Jumada al-thanitik hasita'],
            ['AFT @#DHIJRI@ JUMAT 1425', '1425 Jumada al-thaniren ondotik'],
            ['BEF @#DHIJRI@ JUMAT 1425', '1425 Jumada al-thaniaren aitzinetik'],
            ['@#DHIJRI@ 15 RAJAB 1425', '1425 Rajab 15'],
            ['@#DHIJRI@ RAJAB 1425', '1425 Rajab'],
            ['ABT @#DHIJRI@ RAJAB 1425', '1425 Rajabaren inguruan'],
            ['FROM @#DHIJRI@ RAJAB 1425', '1425 Rajabtik hasita'],
            ['AFT @#DHIJRI@ RAJAB 1425', '1425 Rajabren ondotik'],
            ['BEF @#DHIJRI@ RAJAB 1425', '1425 Rajabaren aitzinetik'],
            ['@#DHIJRI@ 15 SHAAB 1425', '1425 Sha’aban 15'],
            ['@#DHIJRI@ SHAAB 1425', '1425 Sha’aban'],
            ['ABT @#DHIJRI@ SHAAB 1425', '1425 Sha’abanaren inguruan'],
            ['FROM @#DHIJRI@ SHAAB 1425', '1425 Sha’abantik hasita'],
            ['AFT @#DHIJRI@ SHAAB 1425', '1425 Sha’abanren ondotik'],
            ['BEF @#DHIJRI@ SHAAB 1425', '1425 Sha’abanaren aitzinetik'],
            ['@#DHIJRI@ 15 RAMAD 1425', '1425 Ramadan 15'],
            ['@#DHIJRI@ RAMAD 1425', '1425 Ramadan'],
            ['ABT @#DHIJRI@ RAMAD 1425', '1425 Ramadanaren inguruan'],
            ['FROM @#DHIJRI@ RAMAD 1425', '1425 Ramadantik hasita'],
            ['AFT @#DHIJRI@ RAMAD 1425', '1425 Ramadanren ondotik'],
            ['BEF @#DHIJRI@ RAMAD 1425', '1425 Ramadanaren aitzinetik'],
            ['@#DHIJRI@ 15 SHAWW 1425', '1425 Shawwal 15'],
            ['@#DHIJRI@ SHAWW 1425', '1425 Shawwal'],
            ['ABT @#DHIJRI@ SHAWW 1425', '1425 Shawwalaren inguruan'],
            ['FROM @#DHIJRI@ SHAWW 1425', '1425 Shawwaltik hasita'],
            ['AFT @#DHIJRI@ SHAWW 1425', '1425 Shawwalren ondotik'],
            ['BEF @#DHIJRI@ SHAWW 1425', '1425 Shawwalaren aitzinetik'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '1425 Dhu al-Qi’dah 15'],
            ['@#DHIJRI@ DHUAQ 1425', '1425 Dhu al-Qi’dah'],
            ['ABT @#DHIJRI@ DHUAQ 1425', '1425 Dhu al-Qi’daharen inguruan'],
            ['FROM @#DHIJRI@ DHUAQ 1425', '1425 Dhu al-Qi’dahtik hasita'],
            ['AFT @#DHIJRI@ DHUAQ 1425', '1425 Dhu al-Qi’dahren ondotik'],
            ['BEF @#DHIJRI@ DHUAQ 1425', '1425 Dhu al-Qi’daharen aitzinetik'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', '1425aren inguruan'],
            ['FROM @#DHIJRI@ DHUAL 1425', '1425tik hasita'],
            ['AFT @#DHIJRI@ DHUAL 1425', '1425ren ondotik'],
            ['BEF @#DHIJRI@ DHUAL 1425', '1425aren aitzinetik'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15aren inguruan'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15 kalkulatuak'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15 guti gora behera'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15aren aitzinetik'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15ren ondotik'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15tik hasita'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15 arte'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', '1425 Muharram 15 eta 1425 Safar 15ren artean'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', '1425 Muharram 15tik hasita 1425 Safar 15(e)ra'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', '1425 Muharram 15 interpretatuak'],
            ['@#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15'],
            ['@#DJALALI@ FARVA 1384', '1384 Farvardin'],
            ['ABT @#DJALALI@ FARVA 1384', '1384 Farvardinaren inguruan'],
            ['FROM @#DJALALI@ FARVA 1384', '1384 Farvardintik hasita'],
            ['AFT @#DJALALI@ FARVA 1384', '1384 Farvardinren ondotik'],
            ['BEF @#DJALALI@ FARVA 1384', '1384 Farvardinaren aitzinetik'],
            ['@#DJALALI@ 15 ORDIB 1384', '1384 Ordibehesht 15'],
            ['@#DJALALI@ ORDIB 1384', '1384 Ordibehesht'],
            ['ABT @#DJALALI@ ORDIB 1384', '1384 Ordibeheshtaren inguruan'],
            ['FROM @#DJALALI@ ORDIB 1384', '1384 Ordibeheshttik hasita'],
            ['AFT @#DJALALI@ ORDIB 1384', '1384 Ordibeheshtren ondotik'],
            ['BEF @#DJALALI@ ORDIB 1384', '1384 Ordibeheshtaren aitzinetik'],
            ['@#DJALALI@ 15 KHORD 1384', '1384 Khordad 15'],
            ['@#DJALALI@ KHORD 1384', '1384 Khordad'],
            ['ABT @#DJALALI@ KHORD 1384', '1384 Khordadaren inguruan'],
            ['FROM @#DJALALI@ KHORD 1384', '1384 Khordadtik hasita'],
            ['AFT @#DJALALI@ KHORD 1384', '1384 Khordadren ondotik'],
            ['BEF @#DJALALI@ KHORD 1384', '1384 Khordadaren aitzinetik'],
            ['@#DJALALI@ 15 TIR 1384', '1384 Tir 15'],
            ['@#DJALALI@ TIR 1384', '1384 Tir'],
            ['ABT @#DJALALI@ TIR 1384', '1384 Tiraren inguruan'],
            ['FROM @#DJALALI@ TIR 1384', '1384 Tirtik hasita'],
            ['AFT @#DJALALI@ TIR 1384', '1384 Tirren ondotik'],
            ['BEF @#DJALALI@ TIR 1384', '1384 Tiraren aitzinetik'],
            ['@#DJALALI@ 15 MORDA 1384', '1384 Mordad 15'],
            ['@#DJALALI@ MORDA 1384', '1384 Mordad'],
            ['ABT @#DJALALI@ MORDA 1384', '1384 Mordadaren inguruan'],
            ['FROM @#DJALALI@ MORDA 1384', '1384 Mordadtik hasita'],
            ['AFT @#DJALALI@ MORDA 1384', '1384 Mordadren ondotik'],
            ['BEF @#DJALALI@ MORDA 1384', '1384 Mordadaren aitzinetik'],
            ['@#DJALALI@ 15 SHAHR 1384', '1384 Shahrivar 15'],
            ['@#DJALALI@ SHAHR 1384', '1384 Shahrivar'],
            ['ABT @#DJALALI@ SHAHR 1384', '1384 Shahrivararen inguruan'],
            ['FROM @#DJALALI@ SHAHR 1384', '1384 Shahrivartik hasita'],
            ['AFT @#DJALALI@ SHAHR 1384', '1384 Shahrivarren ondotik'],
            ['BEF @#DJALALI@ SHAHR 1384', '1384 Shahrivararen aitzinetik'],
            ['@#DJALALI@ 15 MEHR 1384', '1384 Mehr 15'],
            ['@#DJALALI@ MEHR 1384', '1384 Mehr'],
            ['ABT @#DJALALI@ MEHR 1384', '1384 Mehraren inguruan'],
            ['FROM @#DJALALI@ MEHR 1384', '1384 Mehrtik hasita'],
            ['AFT @#DJALALI@ MEHR 1384', '1384 Mehrren ondotik'],
            ['BEF @#DJALALI@ MEHR 1384', '1384 Mehraren aitzinetik'],
            ['@#DJALALI@ 15 ABAN 1384', '1384 Aban 15'],
            ['@#DJALALI@ ABAN 1384', '1384 Aban'],
            ['ABT @#DJALALI@ ABAN 1384', '1384 Abanaren inguruan'],
            ['FROM @#DJALALI@ ABAN 1384', '1384 Abantik hasita'],
            ['AFT @#DJALALI@ ABAN 1384', '1384 Abanren ondotik'],
            ['BEF @#DJALALI@ ABAN 1384', '1384 Abanaren aitzinetik'],
            ['@#DJALALI@ 15 AZAR 1384', '1384 Azar 15'],
            ['@#DJALALI@ AZAR 1384', '1384 Azar'],
            ['ABT @#DJALALI@ AZAR 1384', '1384 Azararen inguruan'],
            ['FROM @#DJALALI@ AZAR 1384', '1384 Azartik hasita'],
            ['AFT @#DJALALI@ AZAR 1384', '1384 Azarren ondotik'],
            ['BEF @#DJALALI@ AZAR 1384', '1384 Azararen aitzinetik'],
            ['@#DJALALI@ 15 DEY 1384', '1384 Dey 15'],
            ['@#DJALALI@ DEY 1384', '1384 Dey'],
            ['ABT @#DJALALI@ DEY 1384', '1384 Deyaren inguruan'],
            ['FROM @#DJALALI@ DEY 1384', '1384 Deytik hasita'],
            ['AFT @#DJALALI@ DEY 1384', '1384 Deyren ondotik'],
            ['BEF @#DJALALI@ DEY 1384', '1384 Deyaren aitzinetik'],
            ['@#DJALALI@ 15 BAHMA 1384', '1384 Bahman 15'],
            ['@#DJALALI@ BAHMA 1384', '1384 Bahman'],
            ['ABT @#DJALALI@ BAHMA 1384', '1384 Bahmanaren inguruan'],
            ['FROM @#DJALALI@ BAHMA 1384', '1384 Bahmantik hasita'],
            ['AFT @#DJALALI@ BAHMA 1384', '1384 Bahmanren ondotik'],
            ['BEF @#DJALALI@ BAHMA 1384', '1384 Bahmanaren aitzinetik'],
            ['@#DJALALI@ 15 ESFAN 1384', '1384 Esfand 15'],
            ['@#DJALALI@ ESFAN 1384', '1384 Esfand'],
            ['ABT @#DJALALI@ ESFAN 1384', '1384 Esfandaren inguruan'],
            ['FROM @#DJALALI@ ESFAN 1384', '1384 Esfandtik hasita'],
            ['AFT @#DJALALI@ ESFAN 1384', '1384 Esfandren ondotik'],
            ['BEF @#DJALALI@ ESFAN 1384', '1384 Esfandaren aitzinetik'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15aren inguruan'],
            ['CAL @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15 kalkulatuak'],
            ['EST @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15 guti gora behera'],
            ['BEF @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15aren aitzinetik'],
            ['AFT @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15ren ondotik'],
            ['FROM @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15tik hasita'],
            ['TO @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15 arte'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', '1384 Farvardin 15 eta 1384 Ordibehesht 15ren artean'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', '1384 Farvardin 15tik hasita 1384 Ordibehesht 15(e)ra'],
            ['INT @#DJALALI@ 15 FARVA 1384', '1384 Farvardin 15 interpretatuak'],
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
        self::assertSame('one eta two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two eta three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one edo two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two edo three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@\n1 BIRT\n2 DATE 1970");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@\n1 BIRT\n2 DATE 1971");
        $son = self::male('s', "1 FAMC @fm@\n1 FAMS @fson@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 FAMS @fdau@\n1 BIRT\n2 DATE 2001");
        $child = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fosterSon = self::male('fs', "1 FAMC @fd@\n2 PEDI foster");
        $fatherOfH = self::male('fh', "1 FAMS @fp@\n1 FAMC @fgp@\n1 BIRT\n2 DATE 1943");
        $motherOfH = self::female('mh', "1 FAMS @fp@\n1 BIRT\n2 DATE 1945");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@\n1 FAMS @fbro@\n1 BIRT\n2 DATE 1973");
        $sisterOfH = self::female('sh', "1 FAMC @fp@\n1 BIRT\n2 DATE 1968");
        $sisterOfW = self::female('sw', "1 FAMC @fw@\n1 BIRT\n2 DATE 1975");
        $wifeOfSon = self::female('ws', "1 FAMS @fson@");
        $husbandOfDaughter = self::male('hd', "1 FAMS @fdau@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $cousinFemale = self::female('cf', "1 FAMC @fbro@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@\n1 FAMC @fggp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");
        $greatGreatGM = self::female('gggm', "1 FAMS @fggp@");
        $greatGreatGF = self::male('gggf', "1 FAMS @fggp@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@\n1 CHIL @fs@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@\n1 CHIL @sw@");
        $fson = self::family('fson', "0 @fson@ FAM\n1 MARR Y\n1 HUSB @s@\n1 WIFE @ws@");
        $fdau = self::family('fdau', "0 @fdau@ FAM\n1 MARR Y\n1 HUSB @hd@\n1 WIFE @d@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fggp = self::family('fggp', "0 @fggp@ FAM\n1 HUSB @gggf@\n1 WIFE @gggm@\n1 CHIL @pgf@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $stepDaughter, $fosterSon,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH, $sisterOfW,
             $wifeOfSon, $husbandOfDaughter,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle,
             $greatGreatGM, $greatGreatGF],
            [$fm, $fd, $fp, $fw, $fson, $fdau, $fbro, $fgp, $fggp]
        );

        // Partners
        self::assertRelationshipNames('emazte', 'senar', [$husband, $fm, $wife]);
        self::assertRelationshipNames('senar ohia', 'emazte ohia', [$wife, $fd, $exHusband]);

        // Parents
        self::assertRelationshipNames('ama', 'seme', [$son, $fm, $wife]);
        self::assertRelationshipNames('aita', 'seme', [$son, $fm, $husband]);
        self::assertRelationshipNames('ama', 'alaba', [$daughter, $fm, $wife]);
        self::assertRelationshipName('ama', [$child, $fm, $wife]);

        // Children
        self::assertRelationshipName('alaba', [$husband, $fm, $daughter]);
        self::assertRelationshipName('seme', [$husband, $fm, $son]);
        self::assertRelationshipName('ume', [$husband, $fm, $child]);

        // Adopted
        self::assertRelationshipNames('ama adoptatzaile', 'seme adoptatua', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('aita adoptatzaile', 'seme adoptatua', [$adoptedSon, $fd, $exHusband]);

        // Fostered
        self::assertRelationshipNames('harrera-ama', 'harrera-seme', [$fosterSon, $fd, $wife]);
        self::assertRelationshipNames('harrera-aita', 'harrera-seme', [$fosterSon, $fd, $exHusband]);

        // Siblings — ego-relative terms
        // Male ego (husband) looking at siblings
        self::assertRelationshipName('arreba', [$husband, $fp, $sisterOfH]);   // male ego's sister = arreba
        self::assertRelationshipName('neba', [$husband, $fp, $brotherOfH]);    // male ego's brother = neba
        // Female ego (sisterOfH) looking at siblings
        self::assertRelationshipName('anaia', [$sisterOfH, $fp, $husband]);    // female ego's brother = anaia
        self::assertRelationshipName('anaia', [$sisterOfH, $fp, $brotherOfH]); // female ego's brother = anaia
        // Female ego looking at sister (wife looking at sisterOfW)
        self::assertRelationshipName('ahizpa', [$wife, $fw, $sisterOfW]);      // female ego's sister = ahizpa

        // Half-siblings
        self::assertRelationshipName('amaren seme', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('aitaordeko', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('alabaordeko', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws — spouse's parents
        self::assertRelationshipNames('amaginarreba', 'suhi', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('aitaginarreba', 'suhi', [$husband, $fm, $wife, $fw, $fatherOfW]);

        // In-laws — child's spouse
        self::assertRelationshipName('erraina', [$husband, $fm, $son, $fson, $wifeOfSon]);
        self::assertRelationshipName('suhi', [$husband, $fm, $daughter, $fdau, $husbandOfDaughter]);

        // In-laws — spouse's siblings (koinata/koinatu)
        self::assertRelationshipName('koinata', [$husband, $fm, $wife, $fw, $sisterOfW]);

        // Grandparents
        self::assertRelationshipNames('amona', 'biloba', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('aitona', 'biloba', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('biloba', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents
        self::assertRelationshipName('biramona', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);
        self::assertRelationshipName('biraitona', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);

        // Great-great-grandparents (dynamic 4th gen)
        self::assertRelationshipName('4. belaunaldiko amona', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF, $fggp, $greatGreatGM]);
        self::assertRelationshipName('4. belaunaldiko aitona', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF, $fggp, $greatGreatGF]);

        // Great-grandchildren
        self::assertRelationshipName('birbiloba', [$paternalGF, $fgp, $fatherOfH, $fp, $husband, $fm, $son]);

        // Aunts and uncles
        self::assertRelationshipNames('izeba', 'iloba', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('osaba', 'iloba', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('iloba', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('iloba', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('lehengusina', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('lehengusu', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic)
        self::assertRelationshipName('2. belaunaldiko izeba', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('2. belaunaldiko osaba', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);

        // In-laws — child's spouse (daughter-in-law)
        self::assertRelationshipName('erraina', [$fatherOfH, $fp, $husband, $fm, $wife]);
    }
}
