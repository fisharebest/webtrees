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

final readonly class Kazhak extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'қазақ тілі';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'kk';
    protected const string    LOCALE_CODE        = 'kk_KZ@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Cyrl;
    protected const string    DATE_ABOUT         = 'шамамен %s';
    protected const string    DATE_AFTER         = 'кейін %s';
    protected const string    DATE_BEFORE        = '%s дейін';
    protected const string    DATE_BETWEEN_AND   = '%s және %s арасында';
    protected const string    DATE_CALCULATED    = 'есептелген %s';
    protected const string    DATE_ESTIMATED     = 'бағалау %s';
    protected const string    DATE_FROM          = '%s-ден бастап';
    protected const string    DATE_FROM_TO       = '%s-дан %s-ге дейін';
    protected const string    DATE_INTERPRETED   = 'интерпретацияланған %s';
    protected const string    DATE_TO            = '%s дейін';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'ЖДБ';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'ЖД';
    protected const string    LIST_SEPARATOR_AND = ' және ';
    protected const string    LIST_SEPARATOR_OR  = ' немесе ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Қаңтар',
        'Ақпан',
        'Наурыз',
        'Сәуір',
        'Мамыр',
        'Маусым',
        'Шілде',
        'Тамыз',
        'Қыркүйек',
        'Қазан',
        'Қараша',
        'Желтоқсан',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Тишрей',
        'Хешван',
        'Кислев',
        'Тевет',
        'Шеват',
        'Адар I',
        'Адар II',
        'Адар',
        'Ниссан',
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
        'Преріаль',
        'Мессидор',
        'Термидор',
        'Фрюктидор',
        'қосымша күндер',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Мухаррам',
        'Сафар',
        'Раби аль-авваль',
        'Раби ас-сани',
        'Жұмада әл-авалал',
        'Жұмада әл-Тани',
        'Ражаб',
        'Шаабан',
        'Рамадан',
        'Шавваль',
        'Зуль әл-Қида',
        'Зуль әл-Хижжа',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Фарварден',
        'Ордибеешт',
        'Хордад',
        'Тир',
        'Мордәд',
        'Шахривар',
        'Мехр',
        'Абан',
        'Азар',
        'Дей',
        'Бахман',
        'Есфанд',
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
        // Kazakh genitive: possessive suffix "-ның/-нің" (vowel harmony)
        // "арғы" prefix for great-grandparents
        $great = static function (int $n, string $nom, string $gen): array {
            $prefix = str_repeat('арғы ', $n);

            return [$prefix . $nom, '%s ' . $prefix . $gen];
        };

        return [
            // Parents
            Relationship::fixed('ана', '%s ананың')->mother(),
            Relationship::fixed('әке', '%s әкенің')->father(),
            Relationship::fixed('ата-ана', '%s ата-ананың')->parent(),
            // Children
            Relationship::fixed('қыз', '%s қыздың')->daughter(),
            Relationship::fixed('ұл', '%s ұлдың')->son(),
            Relationship::fixed('бала', '%s баланың')->child(),
            // Siblings — elder/younger
            Relationship::fixed('әпке', '%s әпкенің')->older()->sister(),
            Relationship::fixed('аға', '%s ағаның')->older()->brother(),
            Relationship::fixed('сіңілі', '%s сіңілінің')->younger()->sister(),
            Relationship::fixed('іні', '%s інінің')->younger()->brother(),
            Relationship::fixed('әпке', '%s әпкенің')->sister(),
            Relationship::fixed('аға', '%s ағаның')->brother(),
            Relationship::fixed('бауыр', '%s бауырдың')->sibling(),
            // Half-siblings
            Relationship::fixed('өгей әпке', '%s өгей әпкенің')->parent()->daughter(),
            Relationship::fixed('өгей аға', '%s өгей ағаның')->parent()->son(),
            Relationship::fixed('өгей бауыр', '%s өгей бауырдың')->parent()->child(),
            // Stepfamily
            Relationship::fixed('өгей ана', '%s өгей ананың')->parent()->wife(),
            Relationship::fixed('өгей әке', '%s өгей әкенің')->parent()->husband(),
            Relationship::fixed('өгей ата-ана', '%s өгей ата-ананың')->parent()->married()->spouse(),
            Relationship::fixed('өгей қыз', '%s өгей қыздың')->married()->spouse()->daughter(),
            Relationship::fixed('өгей ұл', '%s өгей ұлдың')->married()->spouse()->son(),
            Relationship::fixed('өгей бала', '%s өгей баланың')->married()->spouse()->child(),
            Relationship::fixed('өгей әпке', '%s өгей әпкенің')->parent()->spouse()->daughter(),
            Relationship::fixed('өгей аға', '%s өгей ағаның')->parent()->spouse()->son(),
            Relationship::fixed('өгей бауыр', '%s өгей бауырдың')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('бұрынғы жұбайы', '%s бұрынғы жұбайының')->divorced()->partner()->female(),
            Relationship::fixed('бұрынғы жұбайы', '%s бұрынғы жұбайының')->divorced()->partner()->male(),
            Relationship::fixed('бұрынғы жұбайы', '%s бұрынғы жұбайының')->divorced()->partner(),
            Relationship::fixed('атастырылған', '%s атастырылғанның')->engaged()->partner()->female(),
            Relationship::fixed('атастырылған', '%s атастырылғанның')->engaged()->partner()->male(),
            Relationship::fixed('әйел', '%s әйелдің')->wife(),
            Relationship::fixed('күйеу', '%s күйеудің')->husband(),
            Relationship::fixed('жұбайы', '%s жұбайының')->spouse(),
            Relationship::fixed('серіктес', '%s серіктестің')->partner(),
            // In-laws
            Relationship::fixed('қайын ана', '%s қайын ананың')->married()->spouse()->mother(),
            Relationship::fixed('қайын ата', '%s қайын атаның')->married()->spouse()->father(),
            Relationship::fixed('қайын ата-ана', '%s қайын ата-ананың')->married()->spouse()->parent(),
            Relationship::fixed('келін', '%s келіннің')->child()->wife(),
            Relationship::fixed('күйеу бала', '%s күйеу баланың')->child()->husband(),
            Relationship::fixed('балдыз', '%s балдыздың')->spouse()->sister(),
            Relationship::fixed('қайын', '%s қайынның')->spouse()->brother(),
            Relationship::fixed('келін', '%s келіннің')->sibling()->wife(),
            Relationship::fixed('қайын', '%s қайынның')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('әже', '%s әженің')->parent()->mother(),
            Relationship::fixed('ата', '%s атаның')->parent()->father(),
            Relationship::fixed('әже не ата', '%s әже не атаның')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('немере', '%s немеренің')->child()->daughter(),
            Relationship::fixed('немере', '%s немеренің')->child()->son(),
            Relationship::fixed('немере', '%s немеренің')->child()->child(),
            // Aunts and uncles
            Relationship::fixed('нағашы апа', '%s нағашы апаның')->mother()->sister(),
            Relationship::fixed('нағашы', '%s нағашының')->mother()->brother(),
            Relationship::fixed('апа', '%s апаның')->father()->sister(),
            Relationship::fixed('аға', '%s ағаның')->father()->brother(),
            Relationship::fixed('апа', '%s апаның')->parent()->sister(),
            Relationship::fixed('аға', '%s ағаның')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('жиен қыз', '%s жиен қыздың')->sibling()->daughter(),
            Relationship::fixed('жиен ұл', '%s жиен ұлдың')->sibling()->son(),
            Relationship::fixed('жиен', '%s жиеннің')->sibling()->child(),
            // Cousins
            Relationship::fixed('жиен', '%s жиеннің')->parent()->sibling()->daughter(),
            Relationship::fixed('жиен', '%s жиеннің')->parent()->sibling()->son(),
            Relationship::fixed('жиен', '%s жиеннің')->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'әже', 'әженің'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'ата', 'атаның'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'ата-ана', 'ата-ананың'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'немере', 'немеренің'))->descendant(),
        ];
    }
}
