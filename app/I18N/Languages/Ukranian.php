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

namespace Fisharebest\Webtrees\I18N\Languages;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

use function str_repeat;

final readonly class Ukranian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsSlavic;

    protected const string    ENDONYM            = 'українська';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'uk';
    protected const string    LOCALE_CODE        = 'uk_UA@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Cyrl;
    protected const string    DATE_ABOUT         = 'близько %s';
    protected const string    DATE_AFTER         = 'після %s';
    protected const string    DATE_BEFORE        = 'перед %s';
    protected const string    DATE_BETWEEN_AND   = 'між %s та %s';
    protected const string    DATE_CALCULATED    = 'обчислено %s';
    protected const string    DATE_ESTIMATED     = 'передбачувано %s';
    protected const string    DATE_FROM          = 'з %s';
    protected const string    DATE_FROM_TO       = 'з %s до %s';
    protected const string    DATE_INTERPRETED   = 'розпізнано як %s';
    protected const string    DATE_TO            = 'до %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . ' до н.е.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'н.е.';
    protected const string    LIST_SEPARATOR_AND = ' та ';
    protected const string    LIST_SEPARATOR_OR  = ' або ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Січень',
        'Лютий',
        'Березень',
        'Квітень',
        'Травень',
        'Червень',
        'Липень',
        'Серпень',
        'Вересень',
        'Жовтень',
        'Листопад',
        'Грудень',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'Січня',
        'Лютого',
        'Березня',
        'Квітня',
        'Травня',
        'Червня',
        'Липня',
        'Серпня',
        'Вересня',
        'Жовтня',
        'Листопада',
        'Грудня',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'Січня',
        'Лютого',
        'Березня',
        'Квітня',
        'Травня',
        'Червня',
        'Липня',
        'Серпня',
        'Вересня',
        'Жовтня',
        'Листопада',
        'Грудня',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'Січнем',
        'Лютим',
        'Березнем',
        'Квітнем',
        'Травнем',
        'Червнем',
        'Липнем',
        'Серпнем',
        'Вереснем',
        'Жовтнем',
        'Листопадом',
        'Груднем',
    ];

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Тішрей',
        'Хешван',
        'Кіслев',
        'Тевет',
        'Шват',
        'Адар I',
        'Адар II',
        'Адар',
        'Нісан',
        'Іяр',
        'Сіван',
        'Тамуз',
        'Ав',
        'Елул',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = [
        '',
        'Тішрея',
        'Хешвана',
        'Кіслева',
        'Тевета',
        'Швата',
        'Адара I',
        'Адара II',
        'Адара',
        'Нісана',
        'Іяра',
        'Сівана',
        'Тамуза',
        'Ава',
        'Елула',
    ];

    protected const array JEWISH_MONTHS_LOCATIVE = [
        '',
        'Тішрея',
        'Хешвана',
        'Кіслева',
        'Тевета',
        'Швата',
        'Адара I',
        'Адара II',
        'Адар',
        'Нісана',
        'Іяра',
        'Сівана',
        'Тамуза',
        'Ава',
        'Елула',
    ];

    protected const array JEWISH_MONTHS_INSTRUMENTAL = [
        '',
        'Тишреем',
        'Хешваном',
        'Кіслевом',
        'Теветом',
        'Шватам',
        'Адаром I',
        'Адаром II',
        'Адаром',
        'Нісаном',
        'Іяром',
        'Сіваном',
        'Тамузом',
        'Авом',
        'Елулом',
    ];

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Вандем’єр',
        'Брюмер',
        'Фрімер',
        'Нівоз',
        'Плювіоз',
        'Вантоз',
        'Жерміналь',
        'Флореаль',
        'Преріаль',
        'Мессідор',
        'Термідор',
        'Фрюктідор',
        'додаткові дні',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = [
        '',
        'Вандем’єр',
        'Брюмер',
        'Фрімер',
        'Нівоз',
        'Плювіоз',
        'Вантоз',
        'Жерміналя',
        'Флореаль',
        'Преріаля',
        'Мессідора',
        'Термідора',
        'Фрюктідора',
        'додаткові дні',
    ];

    protected const array FRENCH_MONTHS_LOCATIVE = [
        '',
        'Вандем’єр',
        'Брюмере',
        'Фрімере',
        'Нівоз',
        'Плювіоз',
        'Вантоз',
        'Жермінале',
        'Флореаль',
        'Преріале',
        'Мессідоре',
        'Термідоре',
        'Фрюктідоре',
        'додаткові дні',
    ];

    protected const array FRENCH_MONTHS_INSTRUMENTAL = [
        '',
        'Вандем’єр',
        'Брюмером',
        'Фрімером',
        'Нівоз',
        'Плювіоз',
        'Вантоз',
        'Жерміналем',
        'Флореаль',
        'Преріалем',
        'Мессідором',
        'Термідором',
        'Фрюктідором',
        'додаткові дні',
    ];

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Мухаррам',
        'Сафар',
        'Рабі аль-авваль',
        'Рабі ас-сані',
        'Джумада аль-уля',
        'Джумада ас-сани',
        'Раджаб',
        'Шаабан',
        'Рамадан',
        'Шавваль',
        'Зулькада',
        'Зульхиджа',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Фарвардін',
        'Ордібехешт',
        'Хордад',
        'Тир',
        'Мордад',
        'Шахрівар',
        'Мехр',
        'Абан',
        'Азар',
        'Дей',
        'Бахман',
        'Есфанд',
    ];

    protected const array JALALI_MONTHS_GENITIVE = [
        '',
        'Фарвардін',
        'Ордібехешт',
        'Хордада',
        'Тира',
        'Мордада',
        'Шахрівара',
        'Мехра',
        'Абана',
        'Азара',
        'Дея',
        'Бахмана',
        'Есфанда',
    ];

    protected const array JALALI_MONTHS_LOCATIVE = [
        '',
        'Фарвардіне',
        'Ордібехеште',
        'Хордаде',
        'Тире',
        'Мордаде',
        'Шахріваре',
        'Мехре',
        'Абане',
        'Азаре',
        'Дее',
        'Бахмане',
        'Есфанде',
    ];

    protected const array JALALI_MONTHS_INSTRUMENTAL = [
        '',
        'Фарвардіном',
        'Ордібехештом',
        'Хордадом',
        'Тиром',
        'Мордадом',
        'Шахріваром',
        'Мехром',
        'Абаном',
        'Азаром',
        'Деем',
        'Бахманом',
        'Есфандом',
    ];

    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        UTF8::CYRILLIC_CAPITAL_LETTER_A,
        UTF8::CYRILLIC_CAPITAL_LETTER_BE,
        UTF8::CYRILLIC_CAPITAL_LETTER_VE,
        UTF8::CYRILLIC_CAPITAL_LETTER_GHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_DE,
        UTF8::CYRILLIC_CAPITAL_LETTER_IE,
        UTF8::CYRILLIC_CAPITAL_LETTER_IO,
        UTF8::CYRILLIC_CAPITAL_LETTER_ZHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_ZE,
        UTF8::CYRILLIC_CAPITAL_LETTER_I,
        UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_I,
        UTF8::CYRILLIC_CAPITAL_LETTER_KA,
        UTF8::CYRILLIC_CAPITAL_LETTER_EL,
        UTF8::CYRILLIC_CAPITAL_LETTER_EM,
        UTF8::CYRILLIC_CAPITAL_LETTER_EN,
        UTF8::CYRILLIC_CAPITAL_LETTER_O,
        UTF8::CYRILLIC_CAPITAL_LETTER_PE,
        UTF8::CYRILLIC_CAPITAL_LETTER_ER,
        UTF8::CYRILLIC_CAPITAL_LETTER_ES,
        UTF8::CYRILLIC_CAPITAL_LETTER_TE,
        UTF8::CYRILLIC_CAPITAL_LETTER_U,
        UTF8::CYRILLIC_CAPITAL_LETTER_EF,
        UTF8::CYRILLIC_CAPITAL_LETTER_HA,
        UTF8::CYRILLIC_CAPITAL_LETTER_TSE,
        UTF8::CYRILLIC_CAPITAL_LETTER_CHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_SHA,
        UTF8::CYRILLIC_CAPITAL_LETTER_SHCHA,
        UTF8::CYRILLIC_CAPITAL_LETTER_HARD_SIGN,
        UTF8::CYRILLIC_CAPITAL_LETTER_YERU,
        UTF8::CYRILLIC_CAPITAL_LETTER_SOFT_SIGN,
        UTF8::CYRILLIC_CAPITAL_LETTER_E,
        UTF8::CYRILLIC_CAPITAL_LETTER_YU,
        UTF8::CYRILLIC_CAPITAL_LETTER_YA,
    ];

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            UTF8::CYRILLIC_CAPITAL_LETTER_IE . UTF8::COMBINING_DIAERESIS => UTF8::CYRILLIC_CAPITAL_LETTER_IO,
            UTF8::CYRILLIC_SMALL_LETTER_IE . UTF8::COMBINING_DIAERESIS   => UTF8::CYRILLIC_SMALL_LETTER_IO,
            UTF8::CYRILLIC_CAPITAL_LETTER_I . UTF8::COMBINING_BREVE      => UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_I,
            UTF8::CYRILLIC_SMALL_LETTER_I . UTF8::COMBINING_BREVE        => UTF8::CYRILLIC_SMALL_LETTER_SHORT_I,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Ukrainian "пра-" prefix for great-grandparents, repeating for each generation
        $pra = static fn (int $n, string $nominative, string $genitive): array => [
            ($n > 3 ? 'пра×' . $n . '-' : str_repeat('пра', $n)) . $nominative,
            ($n > 3 ? 'пра×' . $n . '-' : str_repeat('пра', $n)) . $genitive,
        ];

        return [
            // Parents
            Relationship::fixed('мати', '%s матері')->mother(),
            Relationship::fixed('батько', '%s батька')->father(),
            Relationship::fixed('батько/мати', '%s батька/матері')->parent(),
            // Children
            Relationship::fixed('дочка', '%s доньки')->daughter(),
            Relationship::fixed('син', '%s сина')->son(),
            Relationship::fixed('дитина', '%s дитини')->child(),
            // Siblings
            Relationship::fixed('сестра-близнючка', '%s сестри-близнючки')->twin()->sister(),
            Relationship::fixed('брат-близнюк', '%s брата-близнюка')->twin()->brother(),
            Relationship::fixed('близнюк', '%s близнюка')->twin()->sibling(),
            Relationship::fixed('старша сестра', '%s старшої сестри')->older()->sister(),
            Relationship::fixed('старший брат', '%s старшого брата')->older()->brother(),
            Relationship::fixed('старший сиблінг', '%s старшого сиблінга')->older()->sibling(),
            Relationship::fixed('молодша сестра', '%s молодшої сестри')->younger()->sister(),
            Relationship::fixed('молодший брат', '%s молодшого брата')->younger()->brother(),
            Relationship::fixed('молодший сиблінг', '%s молодшого сиблінга')->younger()->sibling(),
            Relationship::fixed('сестра', '%s сестри')->sister(),
            Relationship::fixed('брат', '%s брата')->brother(),
            Relationship::fixed('брат/сестра', '%s брата/сестри')->sibling(),
            // Half-siblings
            Relationship::fixed('зведена сестра', '%s зведеної сестри')->parent()->daughter(),
            Relationship::fixed('зведений брат', '%s зведеного брата')->parent()->son(),
            Relationship::fixed('зведений брат/сестра', '%s зведеного брата/сестри')->parent()->child(),
            // Stepfamily
            Relationship::fixed('мачуха', '%s мачухи')->parent()->wife(),
            Relationship::fixed('вітчим', '%s вітчима')->parent()->husband(),
            Relationship::fixed('падчерка', '%s падчерки')->married()->spouse()->daughter(),
            Relationship::fixed('пасинок', '%s пасинка')->married()->spouse()->son(),
            Relationship::fixed('пасинок/падчерка', '%s пасинка/падчерки')->married()->spouse()->child(),
            // Partners
            Relationship::fixed('колишня дружина', '%s колишньої дружини')->divorced()->partner()->female(),
            Relationship::fixed('колишній чоловік', '%s колишнього чоловіка')->divorced()->partner()->male(),
            Relationship::fixed('колишній партнер', '%s колишнього партнера')->divorced()->partner(),
            Relationship::fixed('наречена', '%s нареченої')->engaged()->partner()->female(),
            Relationship::fixed('наречений', '%s нареченого')->engaged()->partner()->male(),
            Relationship::fixed('дружина', '%s дружини')->wife(),
            Relationship::fixed('чоловік', '%s чоловіка')->husband(),
            Relationship::fixed('дружина/чоловік', '%s дружини/чоловіка')->spouse(),
            Relationship::fixed('партнерка', '%s партнерки')->partner()->female(),
            Relationship::fixed('партнер', '%s партнера')->partner(),
            // In-laws (Ukrainian distinguishes husband's vs wife's parents)
            Relationship::fixed('теща', '%s тещі')->wife()->mother(),
            Relationship::fixed('тесть', '%s тестя')->wife()->father(),
            Relationship::fixed('свекруха', '%s свекрухи')->husband()->mother(),
            Relationship::fixed('свекор', '%s свекра')->husband()->father(),
            Relationship::fixed('свекруха/теща', '%s свекрухи/тещі')->married()->spouse()->mother(),
            Relationship::fixed('свекор/тесть', '%s свекра/тестя')->married()->spouse()->father(),
            Relationship::fixed('невістка', '%s невістки')->son()->wife(),
            Relationship::fixed('зять', '%s зятя')->daughter()->husband(),
            Relationship::fixed('невістка', '%s невістки')->child()->wife(),
            Relationship::fixed('зять', '%s зятя')->child()->husband(),
            Relationship::fixed('зовиця', '%s зовиці')->husband()->sister(),
            Relationship::fixed('дівер', '%s дівера')->husband()->brother(),
            Relationship::fixed('свояченица', '%s свояченици')->wife()->sister(),
            Relationship::fixed('шурин', '%s шурина')->wife()->brother(),
            Relationship::fixed('свояченица/зовиця', '%s свояченици/зовиці')->spouse()->sister(),
            Relationship::fixed('шурин/дівер', '%s шурина/дівера')->spouse()->brother(),
            Relationship::fixed('невістка', '%s невістки')->sibling()->wife(),
            Relationship::fixed('зять', '%s зятя')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('бабуся', '%s бабусі')->parent()->mother(),
            Relationship::fixed('дідусь', '%s дідуся')->parent()->father(),
            Relationship::fixed('дідусь/бабуся', '%s дідуся/бабусі')->parent()->parent(),
            // Great-grandparents
            Relationship::fixed('прабабуся', '%s прабабусі')->parent()->parent()->mother(),
            Relationship::fixed('прадідусь', '%s прадідуся')->parent()->parent()->father(),
            Relationship::fixed('прадідусь/прабабуся', '%s прадідуся/прабабусі')->parent()->parent()->parent(),
            // Grandchildren
            Relationship::fixed('внучка', '%s внучки')->child()->daughter(),
            Relationship::fixed('внук', '%s внука')->child()->son(),
            Relationship::fixed('внук/внучка', '%s внука/внучки')->child()->child(),
            // Great-grandchildren
            Relationship::fixed('правнучка', '%s правнучки')->child()->child()->daughter(),
            Relationship::fixed('правнук', '%s правнука')->child()->child()->son(),
            Relationship::fixed('правнук/правнучка', '%s правнука/правнучки')->child()->child()->child(),
            // Aunts and uncles
            Relationship::fixed('тітка', '%s тітки')->parent()->sister(),
            Relationship::fixed('дядько', '%s дядька')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('племінниця', '%s племінниці')->sibling()->daughter(),
            Relationship::fixed('племінник', '%s племінника')->sibling()->son(),
            // Cousins
            Relationship::fixed('двоюрідна сестра', '%s двоюрідної сестри')->parent()->sibling()->daughter(),
            Relationship::fixed('двоюрідний брат', '%s двоюрідного брата')->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'бабуся', '%s бабусі'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'дідусь', '%s дідуся'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'дідусь/бабуся', '%s дідуся/бабусі'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'внучка', '%s внучки'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'внук', '%s внука'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'внук/внучка', '%s внука/внучки'))->descendant(),
        ];
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */
    /**
     * French Republican month names — case-inflected.
     *
     * @return array<int,string>
     */
    /**
     * Jalali/Persian month names — case-inflected.
     *
     * @return array<int,string>
     */
}
