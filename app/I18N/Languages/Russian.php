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

final readonly class Russian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsSlavic;

    protected const string    ENDONYM            = 'русский';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ru';
    protected const string    LOCALE_CODE        = 'ru_RU@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Cyrl;
    protected const string    DATE_ABOUT         = 'около %s';
    protected const string    DATE_AFTER         = 'после %s';
    protected const string    DATE_BEFORE        = 'перед %s';
    protected const string    DATE_BETWEEN_AND   = 'между %s и %s';
    protected const string    DATE_CALCULATED    = 'вычислено %s';
    protected const string    DATE_ESTIMATED     = 'предполагаемо в %s г';
    protected const string    DATE_FROM          = 'с %s';
    protected const string    DATE_FROM_TO       = 'с %s до %s';
    protected const string    DATE_INTERPRETED   = 'распознано как %s';
    protected const string    DATE_TO            = 'до %s';
    protected const string    ERA_BCE            = '%s до н.э.';
    protected const string    ERA_CE             = '%s н. э.';
    protected const string    LIST_SEPARATOR_AND = ' и ';
    protected const string    LIST_SEPARATOR_OR  = ' или ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'январь',
        'февраль',
        'март',
        'апрель',
        'май',
        'июнь',
        'июль',
        'август',
        'сентябрь',
        'октябрь',
        'ноябрь',
        'декабрь',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'января',
        'февраля',
        'марта',
        'апреля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'января',
        'февраля',
        'марта',
        'апреля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'январём',
        'февралём',
        'мартом',
        'апрелем',
        'маем',
        'июнем',
        'июлем',
        'августом',
        'сентябрём',
        'октябрём',
        'ноябрём',
        'декабрём',
    ];

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'тишрей',
        'хешван',
        'кислев',
        'тевет',
        'шват',
        'адар I',
        'адар II',
        'адар',
        'нисан',
        'ияр',
        'сиван',
        'тамуз',
        'ав',
        'элул',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = [
        '',
        'тишрея',
        'хешвана',
        'кислева',
        'тевета',
        'швата',
        'адара I',
        'адара II',
        'адара',
        'нисана',
        'ияра',
        'сивана',
        'тамуза',
        'ава',
        'элула',
    ];

    protected const array JEWISH_MONTHS_LOCATIVE = [
        '',
        'тишрея',
        'хешвана',
        'кислева',
        'тевета',
        'швата',
        'адара I',
        'адара II',
        'адара',
        'нисана',
        'ияра',
        'сивана',
        'тамуза',
        'ава',
        'элула',
    ];

    protected const array JEWISH_MONTHS_INSTRUMENTAL = [
        '',
        'тишреем',
        'хешваном',
        'кислевом',
        'теветом',
        'шватом',
        'адаром I',
        'адаром II',
        'адаром',
        'нисаном',
        'ияром',
        'сиваном',
        'тамузом',
        'авом',
        'элулом',
    ];

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
        'дополнительные дни',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = [
        '',
        'Вандемьера',
        'Брюмера',
        'Фримера',
        'Нивоза',
        'Плювиоза',
        'Вантоза',
        'Жерминаля',
        'Флореаля',
        'Прериаля',
        'Мессидора',
        'Термидора',
        'Фрюктидора',
        'дополнительных дней',
    ];

    protected const array FRENCH_MONTHS_LOCATIVE = [
        '',
        'Вандемьере',
        'Брюмере',
        'Фримере',
        'Нивозе',
        'Плювиозе',
        'Вантозе',
        'Жерминале',
        'Флореале',
        'Прериале',
        'Мессидоре',
        'Термидоре',
        'Фрюктидоре',
        'дополнительных днях',
    ];

    protected const array FRENCH_MONTHS_INSTRUMENTAL = [
        '',
        'Вандемьером',
        'Брюмером',
        'Фримером',
        'Нивозом',
        'Плювиозом',
        'Вантозом',
        'Жерминалем',
        'Флореалем',
        'Прериалем',
        'Мессидором',
        'Термидором',
        'Фрюктидором',
        'дополнительными днями',
    ];

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Мухаррам',
        'Сафар',
        'Раби аль-авваль',
        'Раби ас-сани',
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

    protected const array JALALI_MONTHS_GENITIVE = [
        '',
        'Фарвардина',
        'Ордибехешта',
        'Хордада',
        'Тира',
        'Мордада',
        'Шахривара',
        'Мехра',
        'Абана',
        'Азара',
        'Дея',
        'Бахмана',
        'Эсфанда',
    ];

    protected const array JALALI_MONTHS_LOCATIVE = [
        '',
        'Фарвардине',
        'Ордибехеште',
        'Хордаде',
        'Тире',
        'Мордаде',
        'Шахриваре',
        'Мехре',
        'Абане',
        'Азаре',
        'Дее',
        'Бахмане',
        'Эсфанде',
    ];

    protected const array JALALI_MONTHS_INSTRUMENTAL = [
        '',
        'Фарвардином',
        'Ордибехештом',
        'Хордадом',
        'Тиром',
        'Мордадом',
        'Шахриваром',
        'Мехром',
        'Абаном',
        'Азаром',
        'Деем',
        'Бахманом',
        'Эсфандом',
    ];

    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        'А',
        'Б',
        'В',
        'Г',
        'Д',
        'Е',
        'Ё',
        'Ж',
        'З',
        'И',
        'Й',
        'К',
        'Л',
        'М',
        'Н',
        'О',
        'П',
        'Р',
        'С',
        'Т',
        'У',
        'Ф',
        'Х',
        'Ц',
        'Ч',
        'Ш',
        'Щ',
        'Ъ',
        'Ы',
        'Ь',
        'Э',
        'Ю',
        'Я',
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

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Russian genitive: nominative, genitive form with %s placeholder
        $pra = static fn (int $n, string $nominative, string $genitive): array => [
            ($n > 3 ? 'пра×' . $n . '-' : str_repeat('пра', $n)) . $nominative,
            ($n > 3 ? 'пра×' . $n . '-' : str_repeat('пра', $n)) . $genitive,
        ];

        return [
            // Adopted
            Relationship::fixed('приёмная мать', '%s приёмной матери')->adoptive()->mother(),
            Relationship::fixed('приёмный отец', '%s приёмного отца')->adoptive()->father(),
            Relationship::fixed('приёмный родитель', '%s приёмного родителя')->adoptive()->parent(),
            Relationship::fixed('приёмная дочь', '%s приёмной дочери')->adopted()->daughter(),
            Relationship::fixed('приёмный сын', '%s приёмного сына')->adopted()->son(),
            Relationship::fixed('приёмный ребёнок', '%s приёмного ребёнка')->adopted()->child(),
            // Parents
            Relationship::fixed('мать', '%s матери')->mother(),
            Relationship::fixed('отец', '%s отца')->father(),
            Relationship::fixed('родитель', '%s родителя')->parent(),
            // Children
            Relationship::fixed('дочь', '%s дочери')->daughter(),
            Relationship::fixed('сын', '%s сына')->son(),
            Relationship::fixed('ребёнок', '%s ребёнка')->child(),
            // Siblings
            Relationship::fixed('сестра-близнец', '%s сестры-близнеца')->twin()->sister(),
            Relationship::fixed('брат-близнец', '%s брата-близнеца')->twin()->brother(),
            Relationship::fixed('близнец', '%s близнеца')->twin()->sibling(),
            Relationship::fixed('старшая сестра', '%s старшей сестры')->older()->sister(),
            Relationship::fixed('старший брат', '%s старшего брата')->older()->brother(),
            Relationship::fixed('старший сиблинг', '%s старшего сиблинга')->older()->sibling(),
            Relationship::fixed('младшая сестра', '%s младшей сестры')->younger()->sister(),
            Relationship::fixed('младший брат', '%s младшего брата')->younger()->brother(),
            Relationship::fixed('младший сиблинг', '%s младшего сиблинга')->younger()->sibling(),
            Relationship::fixed('сестра', '%s сестры')->sister(),
            Relationship::fixed('брат', '%s брата')->brother(),
            Relationship::fixed('брат/сестра', '%s брата/сестры')->sibling(),
            // Half-siblings
            Relationship::fixed('сводная сестра', '%s сводной сестры')->parent()->daughter(),
            Relationship::fixed('сводный брат', '%s сводного брата')->parent()->son(),
            Relationship::fixed('сводный брат/сестра', '%s сводного брата/сестры')->parent()->child(),
            // Stepfamily
            Relationship::fixed('мачеха', '%s мачехи')->parent()->wife(),
            Relationship::fixed('отчим', '%s отчима')->parent()->husband(),
            Relationship::fixed('падчерица', '%s падчерицы')->married()->spouse()->daughter(),
            Relationship::fixed('пасынок', '%s пасынка')->married()->spouse()->son(),
            Relationship::fixed('пасынок/падчерица', '%s пасынка/падчерицы')->married()->spouse()->child(),
            // Partners
            Relationship::fixed('бывшая жена', '%s бывшей жены')->divorced()->partner()->female(),
            Relationship::fixed('бывший муж', '%s бывшего мужа')->divorced()->partner()->male(),
            Relationship::fixed('бывший супруг', '%s бывшего супруга')->divorced()->partner(),
            Relationship::fixed('невеста', '%s невесты')->engaged()->partner()->female(),
            Relationship::fixed('жених', '%s жениха')->engaged()->partner()->male(),
            Relationship::fixed('жена', '%s жены')->wife(),
            Relationship::fixed('муж', '%s мужа')->husband(),
            Relationship::fixed('супруга', '%s супруги')->spouse()->female(),
            Relationship::fixed('супруг', '%s супруга')->spouse(),
            Relationship::fixed('партнёрша', '%s партнёрши')->partner()->female(),
            Relationship::fixed('партнёр', '%s партнёра')->partner(),
            // In-laws (Russian distinguishes husband's vs wife's parents)
            Relationship::fixed('тёща', '%s тёщи')->wife()->mother(),
            Relationship::fixed('тесть', '%s тестя')->wife()->father(),
            Relationship::fixed('свекровь', '%s свекрови')->husband()->mother(),
            Relationship::fixed('свёкор', '%s свёкра')->husband()->father(),
            Relationship::fixed('свекровь/тёща', '%s свекрови/тёщи')->married()->spouse()->mother(),
            Relationship::fixed('свёкор/тесть', '%s свёкра/тестя')->married()->spouse()->father(),
            Relationship::fixed('невестка', '%s невестки')->son()->wife(),
            Relationship::fixed('зять', '%s зятя')->daughter()->husband(),
            Relationship::fixed('невестка', '%s невестки')->child()->wife(),
            Relationship::fixed('зять', '%s зятя')->child()->husband(),
            Relationship::fixed('золовка', '%s золовки')->husband()->sister(),
            Relationship::fixed('деверь', '%s деверя')->husband()->brother(),
            Relationship::fixed('свояченица', '%s свояченицы')->wife()->sister(),
            Relationship::fixed('шурин', '%s шурина')->wife()->brother(),
            Relationship::fixed('свояченица/золовка', '%s свояченицы/золовки')->spouse()->sister(),
            Relationship::fixed('шурин/деверь', '%s шурина/деверя')->spouse()->brother(),
            Relationship::fixed('невестка', '%s невестки')->sibling()->wife(),
            Relationship::fixed('зять', '%s зятя')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('бабушка', '%s бабушки')->parent()->mother(),
            Relationship::fixed('дедушка', '%s дедушки')->parent()->father(),
            Relationship::fixed('дедушка/бабушка', '%s дедушки/бабушки')->parent()->parent(),
            // Great-grandparents
            Relationship::fixed('прабабушка', '%s прабабушки')->parent()->parent()->mother(),
            Relationship::fixed('прадедушка', '%s прадедушки')->parent()->parent()->father(),
            Relationship::fixed('прадедушка/прабабушка', '%s прадедушки/прабабушки')->parent()->parent()->parent(),
            // Grandchildren
            Relationship::fixed('внучка', '%s внучки')->child()->daughter(),
            Relationship::fixed('внук', '%s внука')->child()->son(),
            Relationship::fixed('внук/внучка', '%s внука/внучки')->child()->child(),
            // Great-grandchildren
            Relationship::fixed('правнучка', '%s правнучки')->child()->child()->daughter(),
            Relationship::fixed('правнук', '%s правнука')->child()->child()->son(),
            Relationship::fixed('правнук/правнучка', '%s правнука/правнучки')->child()->child()->child(),
            // Aunts and uncles
            Relationship::fixed('тётя', '%s тёти')->parent()->sister(),
            Relationship::fixed('дядя', '%s дяди')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('племянница', '%s племянницы')->sibling()->daughter(),
            Relationship::fixed('племянник', '%s племянника')->sibling()->son(),
            Relationship::fixed('племянница', '%s племянницы')->married()->spouse()->sibling()->daughter(),
            Relationship::fixed('племянник', '%s племянника')->married()->spouse()->sibling()->son(),
            // Cousins (двоюродный = first cousin, i.e. "2nd degree")
            Relationship::fixed('двоюродная сестра', '%s двоюродной сестры')->parent()->sibling()->daughter(),
            Relationship::fixed('двоюродный брат', '%s двоюродного брата')->parent()->sibling()->son(),
            // Dynamic relationships
            // Great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'тётя', '%s тёти'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'дядя', '%s дяди'))->ancestor()->brother(),
            // Great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'племянница', '%s племянницы'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'племянница', '%s племянницы'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'племянник', '%s племянника'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'племянник', '%s племянника'))->married()->spouse()->sibling()->descendant()->male(),
            // Ancestors
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'бабушка', '%s бабушки'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'дедушка', '%s дедушки'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'дедушка/бабушка', '%s дедушки/бабушки'))->ancestor(),
            // Descendants
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'внучка', '%s внучки'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'внук', '%s внука'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'внук/внучка', '%s внука/внучки'))->descendant(),
        ];
    }
}
