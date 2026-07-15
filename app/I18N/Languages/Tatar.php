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

final readonly class Tatar extends AbstractLanguage
{
    protected const string    ENDONYM            = 'татар';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'tt';
    protected const string    LOCALE_CODE        = 'tt_RU@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Cyrl;
    protected const string    LIST_SEPARATOR_AND = ' һәм ';
    protected const string    LIST_SEPARATOR_OR  = ' яки ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Гыйнвар',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';


    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Тишрей',
        'Хешван',
        'Кислев',
        'Тевет',
        'Шват',
        'Адар I',
        'Адар II',
        'Адар',
        'Нисан',
        'Ияр',
        'Сиван',
        'Тамуз',
        'Ав',
        'Элул',
    ];


    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Вандемьер',
        'Брюмер',
        'Фример',
        'Нивоз',
        'Плювиоз',
        'Вантоз',
        'Жерминаль',
        'Флореаль',
        'Прериаль',
        'Мессидор',
        'Термидор',
        'Фрюктидор',
        'өстәмә көннәр',
    ];


    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Мөхәррәм',
        'Сәфәр',
        'Рабигыл-әүвәл',
        'Рабигыл-ахир',
        'Жәмадил-әүвәл',
        'Жәмадил-ахир',
        'Рәҗәп',
        'Шәгъбан',
        'Рамазан',
        'Шәүвәл',
        'Зөлкагъдә',
        'Зөлхиҗҗә',
    ];


    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Фарвардин',
        'Ордибехешт',
        'Хордад',
        'Тир',
        'Мордад',
        'Шахривар',
        'Мехр',
        'Абан',
        'Азар',
        'Дей',
        'Бахман',
        'Эсфанд',
    ];


    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
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
        // Tatar genitive: possessive suffix "-ның/-нең" (vowel harmony)
        // "бөек" (great) prefix for great-grandparents
        $great = static function (int $n, string $nom, string $gen): array {
            $prefix = str_repeat('бөек ', $n);

            return [$prefix . $nom, '%s ' . $prefix . $gen];
        };

        return [
            // Parents
            Relationship::fixed('әни', '%s әнинең')->mother(),
            Relationship::fixed('әти', '%s әтинең')->father(),
            Relationship::fixed('ата-ана', '%s ата-ананың')->parent(),
            // Children
            Relationship::fixed('кыз', '%s кызның')->daughter(),
            Relationship::fixed('ул', '%s улның')->son(),
            Relationship::fixed('бала', '%s баланың')->child(),
            // Siblings — elder/younger
            Relationship::fixed('апа', '%s апаның')->older()->sister(),
            Relationship::fixed('абый', '%s абыйның')->older()->brother(),
            Relationship::fixed('сеңел', '%s сеңелнең')->younger()->sister(),
            Relationship::fixed('эне', '%s энинең')->younger()->brother(),
            Relationship::fixed('апа', '%s апаның')->sister(),
            Relationship::fixed('абый', '%s абыйның')->brother(),
            Relationship::fixed('туган', '%s туганның')->sibling(),
            // Half-siblings
            Relationship::fixed('үги апа', '%s үги апаның')->parent()->daughter(),
            Relationship::fixed('үги абый', '%s үги абыйның')->parent()->son(),
            Relationship::fixed('үги туган', '%s үги туганның')->parent()->child(),
            // Stepfamily
            Relationship::fixed('үги әни', '%s үги әнинең')->parent()->wife(),
            Relationship::fixed('үги әти', '%s үги әтинең')->parent()->husband(),
            Relationship::fixed('үги ата-ана', '%s үги ата-ананың')->parent()->married()->spouse(),
            Relationship::fixed('үги кыз', '%s үги кызның')->married()->spouse()->daughter(),
            Relationship::fixed('үги ул', '%s үги улның')->married()->spouse()->son(),
            Relationship::fixed('үги бала', '%s үги баланың')->married()->spouse()->child(),
            Relationship::fixed('үги апа', '%s үги апаның')->parent()->spouse()->daughter(),
            Relationship::fixed('үги абый', '%s үги абыйның')->parent()->spouse()->son(),
            Relationship::fixed('үги туган', '%s үги туганның')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('элеккеге тормыш иптәше', '%s элеккеге тормыш иптәшенең')->divorced()->partner()->female(),
            Relationship::fixed('элеккеге тормыш иптәше', '%s элеккеге тормыш иптәшенең')->divorced()->partner()->male(),
            Relationship::fixed('элеккеге тормыш иптәше', '%s элеккеге тормыш иптәшенең')->divorced()->partner(),
            Relationship::fixed('яраштырылган', '%s яраштырылганның')->engaged()->partner()->female(),
            Relationship::fixed('яраштырылган', '%s яраштырылганның')->engaged()->partner()->male(),
            Relationship::fixed('хатын', '%s хатынның')->wife(),
            Relationship::fixed('ир', '%s ирнең')->husband(),
            Relationship::fixed('тормыш иптәше', '%s тормыш иптәшенең')->spouse(),
            Relationship::fixed('партнёр', '%s партнёрның')->partner(),
            // In-laws
            Relationship::fixed('каенана', '%s каенананың')->married()->spouse()->mother(),
            Relationship::fixed('каената', '%s каенатаның')->married()->spouse()->father(),
            Relationship::fixed('каен ата-ана', '%s каен ата-ананың')->married()->spouse()->parent(),
            Relationship::fixed('килен', '%s киленнең')->child()->wife(),
            Relationship::fixed('кияү', '%s кияүнең')->child()->husband(),
            Relationship::fixed('балдыз', '%s балдызның')->spouse()->sister(),
            Relationship::fixed('каен', '%s каеннең')->spouse()->brother(),
            Relationship::fixed('килен', '%s киленнең')->sibling()->wife(),
            Relationship::fixed('каен', '%s каеннең')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('әби', '%s әбинең')->parent()->mother(),
            Relationship::fixed('бабай', '%s бабайның')->parent()->father(),
            Relationship::fixed('әби яки бабай', '%s әби яки бабайның')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('оныч', '%s онычның')->child()->daughter(),
            Relationship::fixed('оныч', '%s онычның')->child()->son(),
            Relationship::fixed('оныч', '%s онычның')->child()->child(),
            // Aunts and uncles
            Relationship::fixed('түти', '%s түтинең')->mother()->sister(),
            Relationship::fixed('дәү әти', '%s дәү әтинең')->mother()->brother(),
            Relationship::fixed('түти', '%s түтинең')->father()->sister(),
            Relationship::fixed('абзый', '%s абзыйның')->father()->brother(),
            Relationship::fixed('түти', '%s түтинең')->parent()->sister(),
            Relationship::fixed('абзый', '%s абзыйның')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('туганның кызы', '%s туганның кызының')->sibling()->daughter(),
            Relationship::fixed('туганның улы', '%s туганның улының')->sibling()->son(),
            Relationship::fixed('туганның баласы', '%s туганның баласының')->sibling()->child(),
            // Cousins
            Relationship::fixed('туган', '%s туганның')->parent()->sibling()->daughter(),
            Relationship::fixed('туган', '%s туганның')->parent()->sibling()->son(),
            Relationship::fixed('туган', '%s туганның')->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'әби', 'әбинең'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'бабай', 'бабайның'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'әби яки бабай', 'әби яки бабайның'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'оныч', 'онычның'))->descendant(),
        ];
    }
}
