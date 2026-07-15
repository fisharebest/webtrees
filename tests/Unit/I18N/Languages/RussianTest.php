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
use Fisharebest\Webtrees\I18N\Languages\Russian;
use Fisharebest\Webtrees\Report\PaperSize;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Russian::class)]
class RussianTest extends AbstractLanguageTestCase
{
    protected static function language(): LanguageInterface
    {
        return new Russian();
    }

    public function testScript(): void
    {
        self::assertSame(Script::Cyrl, self::language()->script());
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
        self::assertSame(['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'], self::language()->alphabet());
    }
    public function testLanguageTag(): void
    {
        self::assertSame('ru', self::language()->languageTag());
    }
    public function testEndonym(): void
    {
        self::assertSame('русский', self::language()->endonym());
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
        self::assertSame('-123 456,0789', self::language()->number(-123456.0789));
    }

    public function testPercentage(): void
    {
        self::assertSame('-123 456,0789 %', self::language()->percentage(-1234.560789));
    }

    protected static function expectedDateOrder(): string
    {
        return 'DMY';
    }




    public static function dateProvider(): array
    {
        return [
            ['15 JAN 2000', '15 января 2000'],
            ['JAN 2000', 'январь 2000'],
            ['ABT JAN 2000', 'около января 2000'],
            ['FROM JAN 2000', 'с января 2000'],
            ['AFT JAN 2000', 'после января 2000'],
            ['BEF JAN 2000', 'перед январём 2000'],
            ['15 FEB 2000', '15 февраля 2000'],
            ['FEB 2000', 'февраль 2000'],
            ['ABT FEB 2000', 'около февраля 2000'],
            ['FROM FEB 2000', 'с февраля 2000'],
            ['AFT FEB 2000', 'после февраля 2000'],
            ['BEF FEB 2000', 'перед февралём 2000'],
            ['15 MAR 2000', '15 марта 2000'],
            ['MAR 2000', 'март 2000'],
            ['ABT MAR 2000', 'около марта 2000'],
            ['FROM MAR 2000', 'с марта 2000'],
            ['AFT MAR 2000', 'после марта 2000'],
            ['BEF MAR 2000', 'перед мартом 2000'],
            ['15 APR 2000', '15 апреля 2000'],
            ['APR 2000', 'апрель 2000'],
            ['ABT APR 2000', 'около апреля 2000'],
            ['FROM APR 2000', 'с апреля 2000'],
            ['AFT APR 2000', 'после апреля 2000'],
            ['BEF APR 2000', 'перед апрелем 2000'],
            ['15 MAY 2000', '15 мая 2000'],
            ['MAY 2000', 'май 2000'],
            ['ABT MAY 2000', 'около мая 2000'],
            ['FROM MAY 2000', 'с мая 2000'],
            ['AFT MAY 2000', 'после мая 2000'],
            ['BEF MAY 2000', 'перед маем 2000'],
            ['15 JUN 2000', '15 июня 2000'],
            ['JUN 2000', 'июнь 2000'],
            ['ABT JUN 2000', 'около июня 2000'],
            ['FROM JUN 2000', 'с июня 2000'],
            ['AFT JUN 2000', 'после июня 2000'],
            ['BEF JUN 2000', 'перед июнем 2000'],
            ['15 JUL 2000', '15 июля 2000'],
            ['JUL 2000', 'июль 2000'],
            ['ABT JUL 2000', 'около июля 2000'],
            ['FROM JUL 2000', 'с июля 2000'],
            ['AFT JUL 2000', 'после июля 2000'],
            ['BEF JUL 2000', 'перед июлем 2000'],
            ['15 AUG 2000', '15 августа 2000'],
            ['AUG 2000', 'август 2000'],
            ['ABT AUG 2000', 'около августа 2000'],
            ['FROM AUG 2000', 'с августа 2000'],
            ['AFT AUG 2000', 'после августа 2000'],
            ['BEF AUG 2000', 'перед августом 2000'],
            ['15 SEP 2000', '15 сентября 2000'],
            ['SEP 2000', 'сентябрь 2000'],
            ['ABT SEP 2000', 'около сентября 2000'],
            ['FROM SEP 2000', 'с сентября 2000'],
            ['AFT SEP 2000', 'после сентября 2000'],
            ['BEF SEP 2000', 'перед сентябрём 2000'],
            ['15 OCT 2000', '15 октября 2000'],
            ['OCT 2000', 'октябрь 2000'],
            ['ABT OCT 2000', 'около октября 2000'],
            ['FROM OCT 2000', 'с октября 2000'],
            ['AFT OCT 2000', 'после октября 2000'],
            ['BEF OCT 2000', 'перед октябрём 2000'],
            ['15 NOV 2000', '15 ноября 2000'],
            ['NOV 2000', 'ноябрь 2000'],
            ['ABT NOV 2000', 'около ноября 2000'],
            ['FROM NOV 2000', 'с ноября 2000'],
            ['AFT NOV 2000', 'после ноября 2000'],
            ['BEF NOV 2000', 'перед ноябрём 2000'],
            ['15 DEC 2000', '15 декабря 2000'],
            ['DEC 2000', 'декабрь 2000'],
            ['ABT DEC 2000', 'около декабря 2000'],
            ['FROM DEC 2000', 'с декабря 2000'],
            ['AFT DEC 2000', 'после декабря 2000'],
            ['BEF DEC 2000', 'перед декабрём 2000'],
            ['2000', '2000'],
            ['ABT 15 JAN 2000', 'около 15 января 2000'],
            ['CAL 15 JAN 2000', 'вычислено 15 января 2000'],
            ['EST 15 JAN 2000', 'предполагаемо в 15 января 2000 г'],
            ['BEF 15 JAN 2000', 'перед 15 января 2000'],
            ['AFT 15 JAN 2000', 'после 15 января 2000'],
            ['FROM 15 JAN 2000', 'с 15 января 2000'],
            ['TO 15 JAN 2000', 'до 15 января 2000'],
            ['BET 15 JAN 2000 AND 15 FEB 2000', 'между 15 января 2000 и 15 февраля 2000'],
            ['FROM 15 JAN 2000 TO 15 FEB 2000', 'с 15 января 2000 до 15 февраля 2000'],
            ['INT 15 JAN 2000', 'распознано как 15 января 2000'],
            ['@#DJULIAN@ 15 JAN 1700', '15 января 1700 н. э.'],
            ['@#DJULIAN@ JAN 1700', 'январь 1700 н. э.'],
            ['ABT @#DJULIAN@ JAN 1700', 'около января 1700 н. э.'],
            ['FROM @#DJULIAN@ JAN 1700', 'с января 1700 н. э.'],
            ['AFT @#DJULIAN@ JAN 1700', 'после января 1700 н. э.'],
            ['BEF @#DJULIAN@ JAN 1700', 'перед январём 1700 н. э.'],
            ['@#DJULIAN@ 15 FEB 1700', '15 февраля 1700 н. э.'],
            ['@#DJULIAN@ FEB 1700', 'февраль 1700 н. э.'],
            ['ABT @#DJULIAN@ FEB 1700', 'около февраля 1700 н. э.'],
            ['FROM @#DJULIAN@ FEB 1700', 'с февраля 1700 н. э.'],
            ['AFT @#DJULIAN@ FEB 1700', 'после февраля 1700 н. э.'],
            ['BEF @#DJULIAN@ FEB 1700', 'перед февралём 1700 н. э.'],
            ['@#DJULIAN@ 15 MAR 1700', '15 марта 1700 н. э.'],
            ['@#DJULIAN@ MAR 1700', 'март 1700 н. э.'],
            ['ABT @#DJULIAN@ MAR 1700', 'около марта 1700 н. э.'],
            ['FROM @#DJULIAN@ MAR 1700', 'с марта 1700 н. э.'],
            ['AFT @#DJULIAN@ MAR 1700', 'после марта 1700 н. э.'],
            ['BEF @#DJULIAN@ MAR 1700', 'перед мартом 1700 н. э.'],
            ['@#DJULIAN@ 15 APR 1700', '15 апреля 1700 н. э.'],
            ['@#DJULIAN@ 14 APR 1645/46', '14 апреля 1645/46 н. э.'],
            ['@#DJULIAN@ APR 1700', 'апрель 1700 н. э.'],
            ['ABT @#DJULIAN@ APR 1700', 'около апреля 1700 н. э.'],
            ['FROM @#DJULIAN@ APR 1700', 'с апреля 1700 н. э.'],
            ['AFT @#DJULIAN@ APR 1700', 'после апреля 1700 н. э.'],
            ['BEF @#DJULIAN@ APR 1700', 'перед апрелем 1700 н. э.'],
            ['@#DJULIAN@ 15 MAY 1700', '15 мая 1700 н. э.'],
            ['@#DJULIAN@ MAY 1700', 'май 1700 н. э.'],
            ['ABT @#DJULIAN@ MAY 1700', 'около мая 1700 н. э.'],
            ['FROM @#DJULIAN@ MAY 1700', 'с мая 1700 н. э.'],
            ['AFT @#DJULIAN@ MAY 1700', 'после мая 1700 н. э.'],
            ['BEF @#DJULIAN@ MAY 1700', 'перед маем 1700 н. э.'],
            ['@#DJULIAN@ 15 JUN 1700', '15 июня 1700 н. э.'],
            ['@#DJULIAN@ JUN 1700', 'июнь 1700 н. э.'],
            ['ABT @#DJULIAN@ JUN 1700', 'около июня 1700 н. э.'],
            ['FROM @#DJULIAN@ JUN 1700', 'с июня 1700 н. э.'],
            ['AFT @#DJULIAN@ JUN 1700', 'после июня 1700 н. э.'],
            ['BEF @#DJULIAN@ JUN 1700', 'перед июнем 1700 н. э.'],
            ['@#DJULIAN@ 15 JUL 1700', '15 июля 1700 н. э.'],
            ['@#DJULIAN@ JUL 1700', 'июль 1700 н. э.'],
            ['ABT @#DJULIAN@ JUL 1700', 'около июля 1700 н. э.'],
            ['FROM @#DJULIAN@ JUL 1700', 'с июля 1700 н. э.'],
            ['AFT @#DJULIAN@ JUL 1700', 'после июля 1700 н. э.'],
            ['BEF @#DJULIAN@ JUL 1700', 'перед июлем 1700 н. э.'],
            ['@#DJULIAN@ 15 AUG 1700', '15 августа 1700 н. э.'],
            ['@#DJULIAN@ AUG 1700', 'август 1700 н. э.'],
            ['ABT @#DJULIAN@ AUG 1700', 'около августа 1700 н. э.'],
            ['FROM @#DJULIAN@ AUG 1700', 'с августа 1700 н. э.'],
            ['AFT @#DJULIAN@ AUG 1700', 'после августа 1700 н. э.'],
            ['BEF @#DJULIAN@ AUG 1700', 'перед августом 1700 н. э.'],
            ['@#DJULIAN@ 15 SEP 1700', '15 сентября 1700 н. э.'],
            ['@#DJULIAN@ SEP 1700', 'сентябрь 1700 н. э.'],
            ['ABT @#DJULIAN@ SEP 1700', 'около сентября 1700 н. э.'],
            ['FROM @#DJULIAN@ SEP 1700', 'с сентября 1700 н. э.'],
            ['AFT @#DJULIAN@ SEP 1700', 'после сентября 1700 н. э.'],
            ['BEF @#DJULIAN@ SEP 1700', 'перед сентябрём 1700 н. э.'],
            ['@#DJULIAN@ 15 OCT 1700', '15 октября 1700 н. э.'],
            ['@#DJULIAN@ OCT 1700', 'октябрь 1700 н. э.'],
            ['ABT @#DJULIAN@ OCT 1700', 'около октября 1700 н. э.'],
            ['FROM @#DJULIAN@ OCT 1700', 'с октября 1700 н. э.'],
            ['AFT @#DJULIAN@ OCT 1700', 'после октября 1700 н. э.'],
            ['BEF @#DJULIAN@ OCT 1700', 'перед октябрём 1700 н. э.'],
            ['@#DJULIAN@ 15 NOV 1700', '15 ноября 1700 н. э.'],
            ['@#DJULIAN@ NOV 1700', 'ноябрь 1700 н. э.'],
            ['ABT @#DJULIAN@ NOV 1700', 'около ноября 1700 н. э.'],
            ['FROM @#DJULIAN@ NOV 1700', 'с ноября 1700 н. э.'],
            ['AFT @#DJULIAN@ NOV 1700', 'после ноября 1700 н. э.'],
            ['BEF @#DJULIAN@ NOV 1700', 'перед ноябрём 1700 н. э.'],
            ['@#DJULIAN@ 15 DEC 1700', '15 декабря 1700 н. э.'],
            ['@#DJULIAN@ DEC 1700', 'декабрь 1700 н. э.'],
            ['ABT @#DJULIAN@ DEC 1700', 'около декабря 1700 н. э.'],
            ['FROM @#DJULIAN@ DEC 1700', 'с декабря 1700 н. э.'],
            ['AFT @#DJULIAN@ DEC 1700', 'после декабря 1700 н. э.'],
            ['BEF @#DJULIAN@ DEC 1700', 'перед декабрём 1700 н. э.'],
            ['@#DJULIAN@ 1700', '1700 н. э.'],
            ['ABT @#DJULIAN@ 15 JAN 1700', 'около 15 января 1700 н. э.'],
            ['CAL @#DJULIAN@ 15 JAN 1700', 'вычислено 15 января 1700 н. э.'],
            ['EST @#DJULIAN@ 15 JAN 1700', 'предполагаемо в 15 января 1700 н. э. г'],
            ['BEF @#DJULIAN@ 15 JAN 1700', 'перед 15 января 1700 н. э.'],
            ['AFT @#DJULIAN@ 15 JAN 1700', 'после 15 января 1700 н. э.'],
            ['FROM @#DJULIAN@ 15 JAN 1700', 'с 15 января 1700 н. э.'],
            ['TO @#DJULIAN@ 15 JAN 1700', 'до 15 января 1700 н. э.'],
            ['BET @#DJULIAN@ 15 JAN 1700 AND @#DJULIAN@ 15 FEB 1700', 'между 15 января 1700 н. э. и 15 февраля 1700 н. э.'],
            ['FROM @#DJULIAN@ 15 JAN 1700 TO @#DJULIAN@ 15 FEB 1700', 'с 15 января 1700 н. э. до 15 февраля 1700 н. э.'],
            ['INT @#DJULIAN@ 15 JAN 1700', 'распознано как 15 января 1700 н. э.'],
            ['@#DHEBREW@ 15 TSH 5765', '15 тишрея 5765'],
            ['@#DHEBREW@ TSH 5765', 'тишрей 5765'],
            ['ABT @#DHEBREW@ TSH 5765', 'около тишрея 5765'],
            ['FROM @#DHEBREW@ TSH 5765', 'с тишрея 5765'],
            ['AFT @#DHEBREW@ TSH 5765', 'после тишрея 5765'],
            ['BEF @#DHEBREW@ TSH 5765', 'перед тишреем 5765'],
            ['@#DHEBREW@ 15 CSH 5765', '15 хешвана 5765'],
            ['@#DHEBREW@ CSH 5765', 'хешван 5765'],
            ['ABT @#DHEBREW@ CSH 5765', 'около хешвана 5765'],
            ['FROM @#DHEBREW@ CSH 5765', 'с хешвана 5765'],
            ['AFT @#DHEBREW@ CSH 5765', 'после хешвана 5765'],
            ['BEF @#DHEBREW@ CSH 5765', 'перед хешваном 5765'],
            ['@#DHEBREW@ 15 KSL 5765', '15 кислева 5765'],
            ['@#DHEBREW@ KSL 5765', 'кислев 5765'],
            ['ABT @#DHEBREW@ KSL 5765', 'около кислева 5765'],
            ['FROM @#DHEBREW@ KSL 5765', 'с кислева 5765'],
            ['AFT @#DHEBREW@ KSL 5765', 'после кислева 5765'],
            ['BEF @#DHEBREW@ KSL 5765', 'перед кислевом 5765'],
            ['@#DHEBREW@ 15 TVT 5765', '15 тевета 5765'],
            ['@#DHEBREW@ TVT 5765', 'тевет 5765'],
            ['ABT @#DHEBREW@ TVT 5765', 'около тевета 5765'],
            ['FROM @#DHEBREW@ TVT 5765', 'с тевета 5765'],
            ['AFT @#DHEBREW@ TVT 5765', 'после тевета 5765'],
            ['BEF @#DHEBREW@ TVT 5765', 'перед теветом 5765'],
            ['@#DHEBREW@ 15 SHV 5765', '15 швата 5765'],
            ['@#DHEBREW@ SHV 5765', 'шват 5765'],
            ['ABT @#DHEBREW@ SHV 5765', 'около швата 5765'],
            ['FROM @#DHEBREW@ SHV 5765', 'с швата 5765'],
            ['AFT @#DHEBREW@ SHV 5765', 'после швата 5765'],
            ['BEF @#DHEBREW@ SHV 5765', 'перед шватом 5765'],
            ['@#DHEBREW@ 15 ADR 5765', '15 адара I 5765'],
            ['@#DHEBREW@ ADR 5765', 'адар I 5765'],
            ['ABT @#DHEBREW@ ADR 5765', 'около адара I 5765'],
            ['FROM @#DHEBREW@ ADR 5765', 'с адара I 5765'],
            ['AFT @#DHEBREW@ ADR 5765', 'после адара I 5765'],
            ['BEF @#DHEBREW@ ADR 5765', 'перед адаром I 5765'],
            ['@#DHEBREW@ 15 ADS 5765', '15 адара II 5765'],
            ['@#DHEBREW@ ADS 5765', 'адар II 5765'],
            ['ABT @#DHEBREW@ ADS 5765', 'около адара II 5765'],
            ['FROM @#DHEBREW@ ADS 5765', 'с адара II 5765'],
            ['AFT @#DHEBREW@ ADS 5765', 'после адара II 5765'],
            ['BEF @#DHEBREW@ ADS 5765', 'перед адаром II 5765'],
            ['@#DHEBREW@ 15 NSN 5765', '15 нисана 5765'],
            ['@#DHEBREW@ NSN 5765', 'нисан 5765'],
            ['ABT @#DHEBREW@ NSN 5765', 'около нисана 5765'],
            ['FROM @#DHEBREW@ NSN 5765', 'с нисана 5765'],
            ['AFT @#DHEBREW@ NSN 5765', 'после нисана 5765'],
            ['BEF @#DHEBREW@ NSN 5765', 'перед нисаном 5765'],
            ['@#DHEBREW@ 15 IYR 5765', '15 ияра 5765'],
            ['@#DHEBREW@ IYR 5765', 'ияр 5765'],
            ['ABT @#DHEBREW@ IYR 5765', 'около ияра 5765'],
            ['FROM @#DHEBREW@ IYR 5765', 'с ияра 5765'],
            ['AFT @#DHEBREW@ IYR 5765', 'после ияра 5765'],
            ['BEF @#DHEBREW@ IYR 5765', 'перед ияром 5765'],
            ['@#DHEBREW@ 15 SVN 5765', '15 сивана 5765'],
            ['@#DHEBREW@ SVN 5765', 'сиван 5765'],
            ['ABT @#DHEBREW@ SVN 5765', 'около сивана 5765'],
            ['FROM @#DHEBREW@ SVN 5765', 'с сивана 5765'],
            ['AFT @#DHEBREW@ SVN 5765', 'после сивана 5765'],
            ['BEF @#DHEBREW@ SVN 5765', 'перед сиваном 5765'],
            ['@#DHEBREW@ 15 TMZ 5765', '15 тамуза 5765'],
            ['@#DHEBREW@ TMZ 5765', 'тамуз 5765'],
            ['ABT @#DHEBREW@ TMZ 5765', 'около тамуза 5765'],
            ['FROM @#DHEBREW@ TMZ 5765', 'с тамуза 5765'],
            ['AFT @#DHEBREW@ TMZ 5765', 'после тамуза 5765'],
            ['BEF @#DHEBREW@ TMZ 5765', 'перед тамузом 5765'],
            ['@#DHEBREW@ 15 AAV 5765', '15 ава 5765'],
            ['@#DHEBREW@ AAV 5765', 'ав 5765'],
            ['ABT @#DHEBREW@ AAV 5765', 'около ава 5765'],
            ['FROM @#DHEBREW@ AAV 5765', 'с ава 5765'],
            ['AFT @#DHEBREW@ AAV 5765', 'после ава 5765'],
            ['BEF @#DHEBREW@ AAV 5765', 'перед авом 5765'],
            ['@#DHEBREW@ 15 ELL 5765', '15 элула 5765'],
            ['@#DHEBREW@ ELL 5765', 'элул 5765'],
            ['ABT @#DHEBREW@ ELL 5765', 'около элула 5765'],
            ['FROM @#DHEBREW@ ELL 5765', 'с элула 5765'],
            ['AFT @#DHEBREW@ ELL 5765', 'после элула 5765'],
            ['BEF @#DHEBREW@ ELL 5765', 'перед элулом 5765'],
            ['@#DHEBREW@ 5765', '5765'],
            ['ABT @#DHEBREW@ 15 TSH 5765', 'около 15 тишрея 5765'],
            ['CAL @#DHEBREW@ 15 TSH 5765', 'вычислено 15 тишрея 5765'],
            ['EST @#DHEBREW@ 15 TSH 5765', 'предполагаемо в 15 тишрея 5765 г'],
            ['BEF @#DHEBREW@ 15 TSH 5765', 'перед 15 тишрея 5765'],
            ['AFT @#DHEBREW@ 15 TSH 5765', 'после 15 тишрея 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765', 'с 15 тишрея 5765'],
            ['TO @#DHEBREW@ 15 TSH 5765', 'до 15 тишрея 5765'],
            ['BET @#DHEBREW@ 15 TSH 5765 AND @#DHEBREW@ 15 CSH 5765', 'между 15 тишрея 5765 и 15 хешвана 5765'],
            ['FROM @#DHEBREW@ 15 TSH 5765 TO @#DHEBREW@ 15 CSH 5765', 'с 15 тишрея 5765 до 15 хешвана 5765'],
            ['INT @#DHEBREW@ 15 TSH 5765', 'распознано как 15 тишрея 5765'],
            ['@#DFRENCH R@ 15 VEND 12', '15 Вандемьера An XII'],
            ['@#DFRENCH R@ VEND 12', 'Вандемьер An XII'],
            ['ABT @#DFRENCH R@ VEND 12', 'около Вандемьера An XII'],
            ['FROM @#DFRENCH R@ VEND 12', 'с Вандемьера An XII'],
            ['AFT @#DFRENCH R@ VEND 12', 'после Вандемьере An XII'],
            ['BEF @#DFRENCH R@ VEND 12', 'перед Вандемьером An XII'],
            ['@#DFRENCH R@ 15 BRUM 12', '15 Брюмера An XII'],
            ['@#DFRENCH R@ BRUM 12', 'Брюмер An XII'],
            ['ABT @#DFRENCH R@ BRUM 12', 'около Брюмера An XII'],
            ['FROM @#DFRENCH R@ BRUM 12', 'с Брюмера An XII'],
            ['AFT @#DFRENCH R@ BRUM 12', 'после Брюмере An XII'],
            ['BEF @#DFRENCH R@ BRUM 12', 'перед Брюмером An XII'],
            ['@#DFRENCH R@ 15 FRIM 12', '15 Фримера An XII'],
            ['@#DFRENCH R@ FRIM 12', 'Фример An XII'],
            ['ABT @#DFRENCH R@ FRIM 12', 'около Фримера An XII'],
            ['FROM @#DFRENCH R@ FRIM 12', 'с Фримера An XII'],
            ['AFT @#DFRENCH R@ FRIM 12', 'после Фримере An XII'],
            ['BEF @#DFRENCH R@ FRIM 12', 'перед Фримером An XII'],
            ['@#DFRENCH R@ 15 NIVO 12', '15 Нивоза An XII'],
            ['@#DFRENCH R@ NIVO 12', 'Нивоз An XII'],
            ['ABT @#DFRENCH R@ NIVO 12', 'около Нивоза An XII'],
            ['FROM @#DFRENCH R@ NIVO 12', 'с Нивоза An XII'],
            ['AFT @#DFRENCH R@ NIVO 12', 'после Нивозе An XII'],
            ['BEF @#DFRENCH R@ NIVO 12', 'перед Нивозом An XII'],
            ['@#DFRENCH R@ 15 PLUV 12', '15 Плювиоза An XII'],
            ['@#DFRENCH R@ PLUV 12', 'Плювиоз An XII'],
            ['ABT @#DFRENCH R@ PLUV 12', 'около Плювиоза An XII'],
            ['FROM @#DFRENCH R@ PLUV 12', 'с Плювиоза An XII'],
            ['AFT @#DFRENCH R@ PLUV 12', 'после Плювиозе An XII'],
            ['BEF @#DFRENCH R@ PLUV 12', 'перед Плювиозом An XII'],
            ['@#DFRENCH R@ 15 VENT 12', '15 Вантоза An XII'],
            ['@#DFRENCH R@ VENT 12', 'Вантоз An XII'],
            ['ABT @#DFRENCH R@ VENT 12', 'около Вантоза An XII'],
            ['FROM @#DFRENCH R@ VENT 12', 'с Вантоза An XII'],
            ['AFT @#DFRENCH R@ VENT 12', 'после Вантозе An XII'],
            ['BEF @#DFRENCH R@ VENT 12', 'перед Вантозом An XII'],
            ['@#DFRENCH R@ 15 GERM 12', '15 Жерминаля An XII'],
            ['@#DFRENCH R@ GERM 12', 'Жерминаль An XII'],
            ['ABT @#DFRENCH R@ GERM 12', 'около Жерминаля An XII'],
            ['FROM @#DFRENCH R@ GERM 12', 'с Жерминаля An XII'],
            ['AFT @#DFRENCH R@ GERM 12', 'после Жерминале An XII'],
            ['BEF @#DFRENCH R@ GERM 12', 'перед Жерминалем An XII'],
            ['@#DFRENCH R@ 15 FLOR 12', '15 Флореаля An XII'],
            ['@#DFRENCH R@ FLOR 12', 'Флореаль An XII'],
            ['ABT @#DFRENCH R@ FLOR 12', 'около Флореаля An XII'],
            ['FROM @#DFRENCH R@ FLOR 12', 'с Флореаля An XII'],
            ['AFT @#DFRENCH R@ FLOR 12', 'после Флореале An XII'],
            ['BEF @#DFRENCH R@ FLOR 12', 'перед Флореалем An XII'],
            ['@#DFRENCH R@ 15 PRAI 12', '15 Прериаля An XII'],
            ['@#DFRENCH R@ PRAI 12', 'Прериаль An XII'],
            ['ABT @#DFRENCH R@ PRAI 12', 'около Прериаля An XII'],
            ['FROM @#DFRENCH R@ PRAI 12', 'с Прериаля An XII'],
            ['AFT @#DFRENCH R@ PRAI 12', 'после Прериале An XII'],
            ['BEF @#DFRENCH R@ PRAI 12', 'перед Прериалем An XII'],
            ['@#DFRENCH R@ 15 MESS 12', '15 Мессидора An XII'],
            ['@#DFRENCH R@ MESS 12', 'Мессидор An XII'],
            ['ABT @#DFRENCH R@ MESS 12', 'около Мессидора An XII'],
            ['FROM @#DFRENCH R@ MESS 12', 'с Мессидора An XII'],
            ['AFT @#DFRENCH R@ MESS 12', 'после Мессидоре An XII'],
            ['BEF @#DFRENCH R@ MESS 12', 'перед Мессидором An XII'],
            ['@#DFRENCH R@ 15 THER 12', '15 Термидора An XII'],
            ['@#DFRENCH R@ THER 12', 'Термидор An XII'],
            ['ABT @#DFRENCH R@ THER 12', 'около Термидора An XII'],
            ['FROM @#DFRENCH R@ THER 12', 'с Термидора An XII'],
            ['AFT @#DFRENCH R@ THER 12', 'после Термидоре An XII'],
            ['BEF @#DFRENCH R@ THER 12', 'перед Термидором An XII'],
            ['@#DFRENCH R@ 15 FRUC 12', '15 Фрюктидора An XII'],
            ['@#DFRENCH R@ FRUC 12', 'Фрюктидор An XII'],
            ['ABT @#DFRENCH R@ FRUC 12', 'около Фрюктидора An XII'],
            ['FROM @#DFRENCH R@ FRUC 12', 'с Фрюктидора An XII'],
            ['AFT @#DFRENCH R@ FRUC 12', 'после Фрюктидоре An XII'],
            ['BEF @#DFRENCH R@ FRUC 12', 'перед Фрюктидором An XII'],
            ['@#DFRENCH R@ 15 COMP 12', '15 дополнительных дней An XII'],
            ['@#DFRENCH R@ COMP 12', 'дополнительные дни An XII'],
            ['ABT @#DFRENCH R@ COMP 12', 'около дополнительных дней An XII'],
            ['FROM @#DFRENCH R@ COMP 12', 'с дополнительных дней An XII'],
            ['AFT @#DFRENCH R@ COMP 12', 'после дополнительных днях An XII'],
            ['BEF @#DFRENCH R@ COMP 12', 'перед дополнительными днями An XII'],
            ['@#DFRENCH R@ 12', 'An XII'],
            ['ABT @#DFRENCH R@ 15 VEND 12', 'около 15 Вандемьера An XII'],
            ['CAL @#DFRENCH R@ 15 VEND 12', 'вычислено 15 Вандемьера An XII'],
            ['EST @#DFRENCH R@ 15 VEND 12', 'предполагаемо в 15 Вандемьера An XII г'],
            ['BEF @#DFRENCH R@ 15 VEND 12', 'перед 15 Вандемьера An XII'],
            ['AFT @#DFRENCH R@ 15 VEND 12', 'после 15 Вандемьера An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12', 'с 15 Вандемьера An XII'],
            ['TO @#DFRENCH R@ 15 VEND 12', 'до 15 Вандемьера An XII'],
            ['BET @#DFRENCH R@ 15 VEND 12 AND @#DFRENCH R@ 15 BRUM 12', 'между 15 Вандемьера An XII и 15 Брюмера An XII'],
            ['FROM @#DFRENCH R@ 15 VEND 12 TO @#DFRENCH R@ 15 BRUM 12', 'с 15 Вандемьера An XII до 15 Брюмера An XII'],
            ['INT @#DFRENCH R@ 15 VEND 12', 'распознано как 15 Вандемьера An XII'],
            ['@#DHIJRI@ 15 MUHAR 1425', '15 Мухаррам 1425'],
            ['@#DHIJRI@ MUHAR 1425', 'Мухаррам 1425'],
            ['ABT @#DHIJRI@ MUHAR 1425', 'около Мухаррам 1425'],
            ['FROM @#DHIJRI@ MUHAR 1425', 'с Мухаррам 1425'],
            ['AFT @#DHIJRI@ MUHAR 1425', 'после Мухаррам 1425'],
            ['BEF @#DHIJRI@ MUHAR 1425', 'перед Мухаррам 1425'],
            ['@#DHIJRI@ 15 SAFAR 1425', '15 Сафар 1425'],
            ['@#DHIJRI@ SAFAR 1425', 'Сафар 1425'],
            ['ABT @#DHIJRI@ SAFAR 1425', 'около Сафар 1425'],
            ['FROM @#DHIJRI@ SAFAR 1425', 'с Сафар 1425'],
            ['AFT @#DHIJRI@ SAFAR 1425', 'после Сафар 1425'],
            ['BEF @#DHIJRI@ SAFAR 1425', 'перед Сафар 1425'],
            ['@#DHIJRI@ 15 RABIA 1425', '15 Раби аль-авваль 1425'],
            ['@#DHIJRI@ RABIA 1425', 'Раби аль-авваль 1425'],
            ['ABT @#DHIJRI@ RABIA 1425', 'около Раби аль-авваль 1425'],
            ['FROM @#DHIJRI@ RABIA 1425', 'с Раби аль-авваль 1425'],
            ['AFT @#DHIJRI@ RABIA 1425', 'после Раби аль-авваль 1425'],
            ['BEF @#DHIJRI@ RABIA 1425', 'перед Раби аль-авваль 1425'],
            ['@#DHIJRI@ 15 RABIT 1425', '15 Раби ас-сани 1425'],
            ['@#DHIJRI@ RABIT 1425', 'Раби ас-сани 1425'],
            ['ABT @#DHIJRI@ RABIT 1425', 'около Раби ас-сани 1425'],
            ['FROM @#DHIJRI@ RABIT 1425', 'с Раби ас-сани 1425'],
            ['AFT @#DHIJRI@ RABIT 1425', 'после Раби ас-сани 1425'],
            ['BEF @#DHIJRI@ RABIT 1425', 'перед Раби ас-сани 1425'],
            ['@#DHIJRI@ 15 JUMAA 1425', '15 Джумада аль-уля 1425'],
            ['@#DHIJRI@ JUMAA 1425', 'Джумада аль-уля 1425'],
            ['ABT @#DHIJRI@ JUMAA 1425', 'около Джумада аль-уля 1425'],
            ['FROM @#DHIJRI@ JUMAA 1425', 'с Джумада аль-уля 1425'],
            ['AFT @#DHIJRI@ JUMAA 1425', 'после Джумада аль-уля 1425'],
            ['BEF @#DHIJRI@ JUMAA 1425', 'перед Джумада аль-уля 1425'],
            ['@#DHIJRI@ 15 JUMAT 1425', '15 Джумада ас-сани 1425'],
            ['@#DHIJRI@ JUMAT 1425', 'Джумада ас-сани 1425'],
            ['ABT @#DHIJRI@ JUMAT 1425', 'около Джумада ас-сани 1425'],
            ['FROM @#DHIJRI@ JUMAT 1425', 'с Джумада ас-сани 1425'],
            ['AFT @#DHIJRI@ JUMAT 1425', 'после Джумада ас-сани 1425'],
            ['BEF @#DHIJRI@ JUMAT 1425', 'перед Джумада ас-сани 1425'],
            ['@#DHIJRI@ 15 RAJAB 1425', '15 Раджаб 1425'],
            ['@#DHIJRI@ RAJAB 1425', 'Раджаб 1425'],
            ['ABT @#DHIJRI@ RAJAB 1425', 'около Раджаб 1425'],
            ['FROM @#DHIJRI@ RAJAB 1425', 'с Раджаб 1425'],
            ['AFT @#DHIJRI@ RAJAB 1425', 'после Раджаб 1425'],
            ['BEF @#DHIJRI@ RAJAB 1425', 'перед Раджаб 1425'],
            ['@#DHIJRI@ 15 SHAAB 1425', '15 Шаабан 1425'],
            ['@#DHIJRI@ SHAAB 1425', 'Шаабан 1425'],
            ['ABT @#DHIJRI@ SHAAB 1425', 'около Шаабан 1425'],
            ['FROM @#DHIJRI@ SHAAB 1425', 'с Шаабан 1425'],
            ['AFT @#DHIJRI@ SHAAB 1425', 'после Шаабан 1425'],
            ['BEF @#DHIJRI@ SHAAB 1425', 'перед Шаабан 1425'],
            ['@#DHIJRI@ 15 RAMAD 1425', '15 Рамадан 1425'],
            ['@#DHIJRI@ RAMAD 1425', 'Рамадан 1425'],
            ['ABT @#DHIJRI@ RAMAD 1425', 'около Рамадан 1425'],
            ['FROM @#DHIJRI@ RAMAD 1425', 'с Рамадан 1425'],
            ['AFT @#DHIJRI@ RAMAD 1425', 'после Рамадан 1425'],
            ['BEF @#DHIJRI@ RAMAD 1425', 'перед Рамадан 1425'],
            ['@#DHIJRI@ 15 SHAWW 1425', '15 Шавваль 1425'],
            ['@#DHIJRI@ SHAWW 1425', 'Шавваль 1425'],
            ['ABT @#DHIJRI@ SHAWW 1425', 'около Шавваль 1425'],
            ['FROM @#DHIJRI@ SHAWW 1425', 'с Шавваль 1425'],
            ['AFT @#DHIJRI@ SHAWW 1425', 'после Шавваль 1425'],
            ['BEF @#DHIJRI@ SHAWW 1425', 'перед Шавваль 1425'],
            ['@#DHIJRI@ 15 DHUAQ 1425', '15 Зулькада 1425'],
            ['@#DHIJRI@ DHUAQ 1425', 'Зулькада 1425'],
            ['ABT @#DHIJRI@ DHUAQ 1425', 'около Зулькада 1425'],
            ['FROM @#DHIJRI@ DHUAQ 1425', 'с Зулькада 1425'],
            ['AFT @#DHIJRI@ DHUAQ 1425', 'после Зулькада 1425'],
            ['BEF @#DHIJRI@ DHUAQ 1425', 'перед Зулькада 1425'],
            ['@#DHIJRI@ 15 DHUAL 1425', '1425'],
            ['@#DHIJRI@ DHUAL 1425', '1425'],
            ['ABT @#DHIJRI@ DHUAL 1425', 'около 1425'],
            ['FROM @#DHIJRI@ DHUAL 1425', 'с 1425'],
            ['AFT @#DHIJRI@ DHUAL 1425', 'после 1425'],
            ['BEF @#DHIJRI@ DHUAL 1425', 'перед 1425'],
            ['@#DHIJRI@ 1425', '1425'],
            ['ABT @#DHIJRI@ 15 MUHAR 1425', 'около 15 Мухаррам 1425'],
            ['CAL @#DHIJRI@ 15 MUHAR 1425', 'вычислено 15 Мухаррам 1425'],
            ['EST @#DHIJRI@ 15 MUHAR 1425', 'предполагаемо в 15 Мухаррам 1425 г'],
            ['BEF @#DHIJRI@ 15 MUHAR 1425', 'перед 15 Мухаррам 1425'],
            ['AFT @#DHIJRI@ 15 MUHAR 1425', 'после 15 Мухаррам 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425', 'с 15 Мухаррам 1425'],
            ['TO @#DHIJRI@ 15 MUHAR 1425', 'до 15 Мухаррам 1425'],
            ['BET @#DHIJRI@ 15 MUHAR 1425 AND @#DHIJRI@ 15 SAFAR 1425', 'между 15 Мухаррам 1425 и 15 Сафар 1425'],
            ['FROM @#DHIJRI@ 15 MUHAR 1425 TO @#DHIJRI@ 15 SAFAR 1425', 'с 15 Мухаррам 1425 до 15 Сафар 1425'],
            ['INT @#DHIJRI@ 15 MUHAR 1425', 'распознано как 15 Мухаррам 1425'],
            ['@#DJALALI@ 15 FARVA 1384', '15 Фарвардина 1384'],
            ['@#DJALALI@ FARVA 1384', 'Фарвардин 1384'],
            ['ABT @#DJALALI@ FARVA 1384', 'около Фарвардина 1384'],
            ['FROM @#DJALALI@ FARVA 1384', 'с Фарвардина 1384'],
            ['AFT @#DJALALI@ FARVA 1384', 'после Фарвардине 1384'],
            ['BEF @#DJALALI@ FARVA 1384', 'перед Фарвардином 1384'],
            ['@#DJALALI@ 15 ORDIB 1384', '15 Ордибехешта 1384'],
            ['@#DJALALI@ ORDIB 1384', 'Ордибехешт 1384'],
            ['ABT @#DJALALI@ ORDIB 1384', 'около Ордибехешта 1384'],
            ['FROM @#DJALALI@ ORDIB 1384', 'с Ордибехешта 1384'],
            ['AFT @#DJALALI@ ORDIB 1384', 'после Ордибехеште 1384'],
            ['BEF @#DJALALI@ ORDIB 1384', 'перед Ордибехештом 1384'],
            ['@#DJALALI@ 15 KHORD 1384', '15 Хордада 1384'],
            ['@#DJALALI@ KHORD 1384', 'Хордад 1384'],
            ['ABT @#DJALALI@ KHORD 1384', 'около Хордада 1384'],
            ['FROM @#DJALALI@ KHORD 1384', 'с Хордада 1384'],
            ['AFT @#DJALALI@ KHORD 1384', 'после Хордаде 1384'],
            ['BEF @#DJALALI@ KHORD 1384', 'перед Хордадом 1384'],
            ['@#DJALALI@ 15 TIR 1384', '15 Тира 1384'],
            ['@#DJALALI@ TIR 1384', 'Тир 1384'],
            ['ABT @#DJALALI@ TIR 1384', 'около Тира 1384'],
            ['FROM @#DJALALI@ TIR 1384', 'с Тира 1384'],
            ['AFT @#DJALALI@ TIR 1384', 'после Тире 1384'],
            ['BEF @#DJALALI@ TIR 1384', 'перед Тиром 1384'],
            ['@#DJALALI@ 15 MORDA 1384', '15 Мордада 1384'],
            ['@#DJALALI@ MORDA 1384', 'Мордад 1384'],
            ['ABT @#DJALALI@ MORDA 1384', 'около Мордада 1384'],
            ['FROM @#DJALALI@ MORDA 1384', 'с Мордада 1384'],
            ['AFT @#DJALALI@ MORDA 1384', 'после Мордаде 1384'],
            ['BEF @#DJALALI@ MORDA 1384', 'перед Мордадом 1384'],
            ['@#DJALALI@ 15 SHAHR 1384', '15 Шахривара 1384'],
            ['@#DJALALI@ SHAHR 1384', 'Шахривар 1384'],
            ['ABT @#DJALALI@ SHAHR 1384', 'около Шахривара 1384'],
            ['FROM @#DJALALI@ SHAHR 1384', 'с Шахривара 1384'],
            ['AFT @#DJALALI@ SHAHR 1384', 'после Шахриваре 1384'],
            ['BEF @#DJALALI@ SHAHR 1384', 'перед Шахриваром 1384'],
            ['@#DJALALI@ 15 MEHR 1384', '15 Мехра 1384'],
            ['@#DJALALI@ MEHR 1384', 'Мехр 1384'],
            ['ABT @#DJALALI@ MEHR 1384', 'около Мехра 1384'],
            ['FROM @#DJALALI@ MEHR 1384', 'с Мехра 1384'],
            ['AFT @#DJALALI@ MEHR 1384', 'после Мехре 1384'],
            ['BEF @#DJALALI@ MEHR 1384', 'перед Мехром 1384'],
            ['@#DJALALI@ 15 ABAN 1384', '15 Абана 1384'],
            ['@#DJALALI@ ABAN 1384', 'Абан 1384'],
            ['ABT @#DJALALI@ ABAN 1384', 'около Абана 1384'],
            ['FROM @#DJALALI@ ABAN 1384', 'с Абана 1384'],
            ['AFT @#DJALALI@ ABAN 1384', 'после Абане 1384'],
            ['BEF @#DJALALI@ ABAN 1384', 'перед Абаном 1384'],
            ['@#DJALALI@ 15 AZAR 1384', '15 Азара 1384'],
            ['@#DJALALI@ AZAR 1384', 'Азар 1384'],
            ['ABT @#DJALALI@ AZAR 1384', 'около Азара 1384'],
            ['FROM @#DJALALI@ AZAR 1384', 'с Азара 1384'],
            ['AFT @#DJALALI@ AZAR 1384', 'после Азаре 1384'],
            ['BEF @#DJALALI@ AZAR 1384', 'перед Азаром 1384'],
            ['@#DJALALI@ 15 DEY 1384', '15 Дея 1384'],
            ['@#DJALALI@ DEY 1384', 'Дей 1384'],
            ['ABT @#DJALALI@ DEY 1384', 'около Дея 1384'],
            ['FROM @#DJALALI@ DEY 1384', 'с Дея 1384'],
            ['AFT @#DJALALI@ DEY 1384', 'после Дее 1384'],
            ['BEF @#DJALALI@ DEY 1384', 'перед Деем 1384'],
            ['@#DJALALI@ 15 BAHMA 1384', '15 Бахмана 1384'],
            ['@#DJALALI@ BAHMA 1384', 'Бахман 1384'],
            ['ABT @#DJALALI@ BAHMA 1384', 'около Бахмана 1384'],
            ['FROM @#DJALALI@ BAHMA 1384', 'с Бахмана 1384'],
            ['AFT @#DJALALI@ BAHMA 1384', 'после Бахмане 1384'],
            ['BEF @#DJALALI@ BAHMA 1384', 'перед Бахманом 1384'],
            ['@#DJALALI@ 15 ESFAN 1384', '15 Эсфанда 1384'],
            ['@#DJALALI@ ESFAN 1384', 'Эсфанд 1384'],
            ['ABT @#DJALALI@ ESFAN 1384', 'около Эсфанда 1384'],
            ['FROM @#DJALALI@ ESFAN 1384', 'с Эсфанда 1384'],
            ['AFT @#DJALALI@ ESFAN 1384', 'после Эсфанде 1384'],
            ['BEF @#DJALALI@ ESFAN 1384', 'перед Эсфандом 1384'],
            ['@#DJALALI@ 1384', '1384'],
            ['ABT @#DJALALI@ 15 FARVA 1384', 'около 15 Фарвардина 1384'],
            ['CAL @#DJALALI@ 15 FARVA 1384', 'вычислено 15 Фарвардина 1384'],
            ['EST @#DJALALI@ 15 FARVA 1384', 'предполагаемо в 15 Фарвардина 1384 г'],
            ['BEF @#DJALALI@ 15 FARVA 1384', 'перед 15 Фарвардина 1384'],
            ['AFT @#DJALALI@ 15 FARVA 1384', 'после 15 Фарвардина 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384', 'с 15 Фарвардина 1384'],
            ['TO @#DJALALI@ 15 FARVA 1384', 'до 15 Фарвардина 1384'],
            ['BET @#DJALALI@ 15 FARVA 1384 AND @#DJALALI@ 15 ORDIB 1384', 'между 15 Фарвардина 1384 и 15 Ордибехешта 1384'],
            ['FROM @#DJALALI@ 15 FARVA 1384 TO @#DJALALI@ 15 ORDIB 1384', 'с 15 Фарвардина 1384 до 15 Ордибехешта 1384'],
            ['INT @#DJALALI@ 15 FARVA 1384', 'распознано как 15 Фарвардина 1384'],
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
        self::assertSame('one и two', $language->formatListAnd(['one', 'two']));
        self::assertSame('one, two и three', $language->formatListAnd(['one', 'two', 'three']));

        self::assertSame('', $language->formatListOr([]));
        self::assertSame('one', $language->formatListOr(['one']));
        self::assertSame('one или two', $language->formatListOr(['one', 'two']));
        self::assertSame('one, two или three', $language->formatListOr(['one', 'two', 'three']));
    }


    public function testRelationships(): void
    {
        self::initFactories();

        // Create individuals
        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $child = self::unknown('c', "1 FAMC @fm@\n1 BIRT\n2 DATE 2002");
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
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");
        // For husband's sister's husband (деверь path)
        $sisterHusband = self::male('shh', "1 FAMS @fsis@");

        // Create families
        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@\n1 CHIL @c@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");
        $fsis = self::family('fsis', "0 @fsis@ FAM\n1 MARR Y\n1 HUSB @shh@\n1 WIFE @sh@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $child, $exHusband, $adoptedSon, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $engaged, $fiance, $sisterHusband],
            [$fm, $fd, $fp, $fw, $fbro, $fgp, $fe, $fsis]
        );

        // Partners
        self::assertRelationshipNames('жена', 'муж', [$husband, $fm, $wife]);
        self::assertRelationshipNames('бывший муж', 'бывшая жена', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('невеста', 'жених', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('мать', 'сын', [$son, $fm, $wife]);
        self::assertRelationshipNames('отец', 'сын', [$son, $fm, $husband]);
        self::assertRelationshipNames('мать', 'дочь', [$daughter, $fm, $wife]);
        self::assertRelationshipNames('отец', 'ребёнок', [$child, $fm, $husband]);

        // Adopted
        self::assertRelationshipNames('приёмная мать', 'приёмный сын', [$adoptedSon, $fd, $wife]);
        self::assertRelationshipNames('приёмный отец', 'приёмный сын', [$adoptedSon, $fd, $exHusband]);

        // Siblings (son born 2000, daughter born 2001, child born 2002)
        self::assertRelationshipNames('младшая сестра', 'старший брат', [$son, $fm, $daughter]);
        self::assertRelationshipNames('старший брат', 'младшая сестра', [$daughter, $fm, $son]);
        self::assertRelationshipNames('младший сиблинг', 'старший брат', [$son, $fm, $child]);
        self::assertRelationshipName('брат', [$stepDaughter, $fd, $adoptedSon]);
        self::assertRelationshipName('сестра', [$adoptedSon, $fd, $stepDaughter]);

        // Half-siblings
        self::assertRelationshipName('сводный брат', [$stepDaughter, $fd, $wife, $fm, $son]);
        self::assertRelationshipName('сводная сестра', [$son, $fm, $wife, $fd, $stepDaughter]);

        // Stepfamily
        self::assertRelationshipName('отчим', [$stepDaughter, $fd, $wife, $fm, $husband]);

        // In-laws (Russian distinguishes wife's vs husband's parents)
        self::assertRelationshipName('тёща', [$husband, $fm, $wife, $fw, $motherOfW]);  // wife's mother
        self::assertRelationshipName('тесть', [$husband, $fm, $wife, $fw, $fatherOfW]); // wife's father
        self::assertRelationshipName('свекровь', [$wife, $fm, $husband, $fp, $motherOfH]); // husband's mother
        self::assertRelationshipName('свёкор', [$wife, $fm, $husband, $fp, $fatherOfH]);   // husband's father
        self::assertRelationshipName('невестка', [$fatherOfH, $fp, $husband, $fm, $wife]); // son's wife
        self::assertRelationshipName('зять', [$motherOfW, $fw, $wife, $fm, $husband]);     // daughter's husband

        // Husband's siblings in-law terms
        self::assertRelationshipName('золовка', [$wife, $fm, $husband, $fp, $sisterOfH]); // husband's sister
        self::assertRelationshipName('деверь', [$wife, $fm, $husband, $fp, $brotherOfH]); // husband's brother

        // Grandparents
        self::assertRelationshipNames('бабушка', 'внук', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('дедушка', 'внук', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('внучка', [$fatherOfH, $fp, $husband, $fm, $daughter]);
        self::assertRelationshipName('внук/внучка', [$fatherOfH, $fp, $husband, $fm, $child]);

        // Great-grandparents
        self::assertRelationshipName('прадедушка', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('прабабушка', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipName('тётя', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipName('дядя', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('племянница', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('племянник', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('двоюродная сестра', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('двоюродный брат', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);
    }
}
