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

final readonly class Serbian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsSlavic;

    protected const string    ENDONYM            = 'српски';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'sr';
    protected const string    LOCALE_CODE        = 'sr_RS@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Cyrl;
    protected const string    DATE_BETWEEN_AND   = 'између %s и %s';
    protected const string    LIST_SEPARATOR_AND = ' и ';
    protected const string    LIST_SEPARATOR_OR  = ' или ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Јануар',
        'Фебруар',
        'Март',
        'Април',
        'Мај',
        'Јун',
        'Јул',
        'Август',
        'Септембар',
        'Октобар',
        'Новембар',
        'Децембар',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'јануара',
        'фебруара',
        'марта',
        'априла',
        'маја',
        'јуна',
        'јула',
        'августа',
        'септембра',
        'октобра',
        'новембра',
        'децембра',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'јануару',
        'фебруару',
        'марту',
        'априлу',
        'мају',
        'јуну',
        'јулу',
        'августу',
        'септембру',
        'октобру',
        'новембру',
        'децембру',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'јануара',
        'фебруара',
        'марта',
        'априла',
        'мајем',
        'јуна',
        'јула',
        'августа',
        'септембра',
        'октобра',
        'новембра',
        'децембра',
    ];

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Тишреј',
        'Хешван',
        'Кислев',
        'Тевет',
        'Шеват',
        'Адар I',
        'Адар II',
        'Адар',
        'Нисан',
        'Ијар',
        'Сиван',
        'Тамуз',
        'Ав',
        'Елул',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Вандемијер',
        'Бример',
        'Фример',
        'Нивоз',
        'Пливиоз',
        'Вантоз',
        'Жерминал',
        'Флореал',
        'Преријал',
        'Месидор',
        'Термидор',
        'Фруктидор',
        'допунски дани',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Мухарем',
        'Сафар',
        'Реби-ул-евел',
        'Реби-ул-ахир',
        'Џумадел-ула',
        'Џумадел-ахира',
        'Реџеб',
        'Шабан',
        'Рамазан',
        'Шевал',
        'Зулкаде',
        'Зулхиџе',
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
        'Деј',
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
        UTF8::CYRILLIC_CAPITAL_LETTER_DJE,
        UTF8::CYRILLIC_CAPITAL_LETTER_IE,
        UTF8::CYRILLIC_CAPITAL_LETTER_ZHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_ZE,
        UTF8::CYRILLIC_CAPITAL_LETTER_I,
        UTF8::CYRILLIC_CAPITAL_LETTER_JE,
        UTF8::CYRILLIC_CAPITAL_LETTER_KA,
        UTF8::CYRILLIC_CAPITAL_LETTER_EL,
        UTF8::CYRILLIC_CAPITAL_LETTER_LJE,
        UTF8::CYRILLIC_CAPITAL_LETTER_EM,
        UTF8::CYRILLIC_CAPITAL_LETTER_EN,
        UTF8::CYRILLIC_CAPITAL_LETTER_NJE,
        UTF8::CYRILLIC_CAPITAL_LETTER_O,
        UTF8::CYRILLIC_CAPITAL_LETTER_PE,
        UTF8::CYRILLIC_CAPITAL_LETTER_ER,
        UTF8::CYRILLIC_CAPITAL_LETTER_ES,
        UTF8::CYRILLIC_CAPITAL_LETTER_TE,
        UTF8::CYRILLIC_CAPITAL_LETTER_TSHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_U,
        UTF8::CYRILLIC_CAPITAL_LETTER_EF,
        UTF8::CYRILLIC_CAPITAL_LETTER_HA,
        UTF8::CYRILLIC_CAPITAL_LETTER_TSE,
        UTF8::CYRILLIC_CAPITAL_LETTER_CHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_DZHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_SHA,
    ];

    protected function assembleDate(string $day, string $month, string $year): string
    {
        return $this->assembleDateDdotMY($day, $month, $year);
    }

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function rel(string $nom, string $gen): array
    {
        return [$nom, '%s ' . $gen];
    }

    /** @return array{string, string} */
    private function pra(int $n, string $nom, string $gen): array
    {
        return [
            str_repeat('пра', $n) . $nom,
            '%s ' . str_repeat('пра', $n) . $gen,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->rel('усвојитељка', 'усвојитељке'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('усвојитељ', 'усвојитеља'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('усвојитељ', 'усвојитеља'))->adoptive()->parent(),
            Relationship::fixed(...$this->rel('усвојена ћерка', 'усвојене ћерке'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('усвојени син', 'усвојеног сина'))->adopted()->son(),
            Relationship::fixed(...$this->rel('усвојено дете', 'усвојеног детета'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->rel('мајка', 'мајке'))->mother(),
            Relationship::fixed(...$this->rel('отац', 'оца'))->father(),
            Relationship::fixed(...$this->rel('родитељ', 'родитеља'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('ћерка', 'ћерке'))->daughter(),
            Relationship::fixed(...$this->rel('син', 'сина'))->son(),
            Relationship::fixed(...$this->rel('дете', 'детета'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('сестра близнакиња', 'сестре близнакиње'))->multiple()->sister(),
            Relationship::fixed(...$this->rel('брат близанац', 'брата близанца'))->multiple()->brother(),
            Relationship::fixed(...$this->rel('близанац', 'близанца'))->multiple()->sibling(),
            Relationship::fixed(...$this->rel('старија сестра', 'старије сестре'))->older()->sister(),
            Relationship::fixed(...$this->rel('старији брат', 'старијег брата'))->older()->brother(),
            Relationship::fixed(...$this->rel('млађа сестра', 'млађе сестре'))->younger()->sister(),
            Relationship::fixed(...$this->rel('млађи брат', 'млађег брата'))->younger()->brother(),
            Relationship::fixed(...$this->rel('сестра', 'сестре'))->sister(),
            Relationship::fixed(...$this->rel('брат', 'брата'))->brother(),
            Relationship::fixed(...$this->rel('брат/сестра', 'брата/сестре'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('полусестра', 'полусестре'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('полубрат', 'полубрата'))->parent()->son(),
            Relationship::fixed(...$this->rel('полубрат/полусестра', 'полубрата/полусестре'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('маћеха', 'маћехе'))->parent()->wife(),
            Relationship::fixed(...$this->rel('очух', 'очуха'))->parent()->husband(),
            Relationship::fixed(...$this->rel('поочим', 'поочима'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('пасторка', 'пасторке'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('пасторак', 'пасторка'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('пасторче', 'пасторчета'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('бивша супруга', 'бивше супруге'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('бивши супруг', 'бившег супруга'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('бивши партнер', 'бившег партнера'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('вереница', 'веренице'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('вереник', 'вереника'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('супруга', 'супруге'))->wife(),
            Relationship::fixed(...$this->rel('супруг', 'супруга'))->husband(),
            Relationship::fixed(...$this->rel('супружник', 'супружника'))->spouse(),
            Relationship::fixed(...$this->rel('партнер', 'партнера'))->partner(),
            // In-laws (wife's parents — ташта/таст)
            Relationship::fixed(...$this->rel('ташта', 'таште'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('таст', 'таста'))->married()->spouse()->father(),
            // In-laws (husband's parents — свекрва/свекар)
            Relationship::fixed(...$this->rel('свекрва', 'свекрве'))->spouse()->mother(),
            Relationship::fixed(...$this->rel('свекар', 'свекра'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$this->rel('снаха', 'снахе'))->child()->wife(),
            Relationship::fixed(...$this->rel('зет', 'зета'))->child()->husband(),
            Relationship::fixed(...$this->rel('зет/снаха', 'зета/снахе'))->child()->married()->spouse(),
            // Siblings-in-law
            Relationship::fixed(...$this->rel('заова', 'заове'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('девер', 'девера'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('свастика', 'свастике'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('шурак', 'шурака'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('бака', 'баке'))->parent()->mother(),
            Relationship::fixed(...$this->rel('деда', 'деде'))->parent()->father(),
            Relationship::fixed(...$this->rel('бака/деда', 'баке/деде'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('унука', 'унуке'))->child()->daughter(),
            Relationship::fixed(...$this->rel('унук', 'унука'))->child()->son(),
            Relationship::fixed(...$this->rel('унук/унука', 'унука/унуке'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('тетка', 'тетке'))->parent()->sister(),
            Relationship::fixed(...$this->rel('ујак', 'ујака'))->mother()->brother(),
            Relationship::fixed(...$this->rel('стриц', 'стрица'))->father()->brother(),
            Relationship::fixed(...$this->rel('стриц', 'стрица'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('нећакиња', 'нећакиње'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('нећак', 'нећака'))->sibling()->son(),
            Relationship::fixed(...$this->rel('нећак/нећакиња', 'нећака/нећакиње'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->rel('сестрична', 'сестричне'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('братић', 'братића'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('братић/сестрична', 'братића/сестричне'))->parent()->sibling()->child(),
            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'бака', 'баке'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'деда', 'деде'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'бака/деда', 'баке/деде'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'унука', 'унуке'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'унук', 'унука'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'унук/унука', 'унука/унуке'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'тетка', 'тетке'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'стриц', 'стрица'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'нећакиња', 'нећакиње'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'нећак', 'нећака'))->sibling()->descendant()->male(),
        ];
    }
}
