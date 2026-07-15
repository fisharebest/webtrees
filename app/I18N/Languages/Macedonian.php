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

final readonly class Macedonian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsMacedonian;

    protected const string    ENDONYM            = 'македонски';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'mk';
    protected const string    LOCALE_CODE        = 'mk_MK@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Cyrl;
    protected const string    DATE_ABOUT         = 'okolu %s';
    protected const string    DATE_AFTER         = 'posle %s';
    protected const string    DATE_BEFORE        = 'pred %s';
    protected const string    DATE_BETWEEN_AND   = 'pomegju %s i %s';
    protected const string    DATE_CALCULATED    = 'presmetano %s';
    protected const string    DATE_ESTIMATED     = 'proceneto %s';
    protected const string    DATE_FROM          = 'od %s';
    protected const string    DATE_FROM_TO       = 'od %s do %s';
    protected const string    DATE_INTERPRETED   = 'protolkuvani %s';
    protected const string    DATE_TO            = 'do %s';
    protected const string    LIST_SEPARATOR_AND = ' и ';
    protected const string    LIST_SEPARATOR_OR  = ' или ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januari',
        'Fevruari',
        'Mart',
        'April',
        'Maj',
        'Juni',
        'Juli',
        'Avgust',
        'Septemvri',
        'Oktomvri',
        'Noemvri',
        'Dekemvri',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tishrei',
        'Heshvan',
        'Kislev',
        'Tevet',
        'Shevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nissan',
        'Iyar',
        'Sivan',
        'Tamuz',
        'Av',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vendmiaire',
        'Brumer',
        'Frimer',
        'Nivse',
        'Pluvise',
        'Ventse',
        'Germinalen',
        'Floral',
        'Prairalen',
        'Messidoren',
        'Thermidoren',
        'Fuctidoren',
        'jours complmentaires',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Мухарем',
        'Сафар',
        'Реби ул-евел',
        'Реби ул-ахир',
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
        'Farvardin',
        'Ordibehesht',
        'Khordad',
        'Tir',
        'Mordad',
        'Shahrivar',
        'Mehr',
        'Aban',
        'Azar',
        'Dey',
        'Bahman',
        'Esfand',
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
        UTF8::CYRILLIC_CAPITAL_LETTER_GJE,
        UTF8::CYRILLIC_CAPITAL_LETTER_IE,
        UTF8::CYRILLIC_CAPITAL_LETTER_ZHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_ZE,
        UTF8::CYRILLIC_CAPITAL_LETTER_DZE,
        UTF8::CYRILLIC_CAPITAL_LETTER_I,
        UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_I,
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
        UTF8::CYRILLIC_CAPITAL_LETTER_KJE,
        UTF8::CYRILLIC_CAPITAL_LETTER_U,
        UTF8::CYRILLIC_CAPITAL_LETTER_EF,
        UTF8::CYRILLIC_CAPITAL_LETTER_HA,
        UTF8::CYRILLIC_CAPITAL_LETTER_TSE,
        UTF8::CYRILLIC_CAPITAL_LETTER_CHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_DZHE,
        UTF8::CYRILLIC_CAPITAL_LETTER_SHA,
    ];

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Macedonian genitive helper: "на" + noun
        $rel = static fn (string $nom, string $gen): array => [$nom, '%s на ' . $gen];

        // Dynamic "пра-" prefix for great-grandparents
        $pra = static fn (int $n, string $nom, string $gen): array => [
            str_repeat('пра', $n) . $nom,
            '%s на ' . str_repeat('пра', $n) . $gen,
        ];

        return [
            // Adopted
            Relationship::fixed(...$rel('посвоителка', 'посвоителката'))->adoptive()->mother(),
            Relationship::fixed(...$rel('посвоител', 'посвоителот'))->adoptive()->father(),
            Relationship::fixed(...$rel('посвоител', 'посвоителот'))->adoptive()->parent(),
            Relationship::fixed(...$rel('посвоена ќерка', 'посвоената ќерка'))->adopted()->daughter(),
            Relationship::fixed(...$rel('посвоен син', 'посвоениот син'))->adopted()->son(),
            Relationship::fixed(...$rel('посвоено дете', 'посвоеното дете'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$rel('мајка', 'мајката'))->mother(),
            Relationship::fixed(...$rel('татко', 'таткото'))->father(),
            Relationship::fixed(...$rel('родител', 'родителот'))->parent(),
            // Children
            Relationship::fixed(...$rel('ќерка', 'ќерката'))->daughter(),
            Relationship::fixed(...$rel('син', 'синот'))->son(),
            Relationship::fixed(...$rel('дете', 'детето'))->child(),
            // Siblings
            Relationship::fixed(...$rel('сестра-близначка', 'сестрата-близначка'))->twin()->sister(),
            Relationship::fixed(...$rel('брат-близнак', 'братот-близнак'))->twin()->brother(),
            Relationship::fixed(...$rel('близнак', 'близнакот'))->twin()->sibling(),
            Relationship::fixed(...$rel('постара сестра', 'постарата сестра'))->older()->sister(),
            Relationship::fixed(...$rel('постар брат', 'постариот брат'))->older()->brother(),
            Relationship::fixed(...$rel('помлада сестра', 'помладата сестра'))->younger()->sister(),
            Relationship::fixed(...$rel('помлад брат', 'помладиот брат'))->younger()->brother(),
            Relationship::fixed(...$rel('сестра', 'сестрата'))->sister(),
            Relationship::fixed(...$rel('брат', 'братот'))->brother(),
            Relationship::fixed(...$rel('брат/сестра', 'братот/сестрата'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$rel('полусестра', 'полусестрата'))->parent()->daughter(),
            Relationship::fixed(...$rel('полубрат', 'полубратот'))->parent()->son(),
            Relationship::fixed(...$rel('полубрат/полусестра', 'полубратот/полусестрата'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$rel('маќеа', 'маќеата'))->parent()->wife(),
            Relationship::fixed(...$rel('очув', 'очувот'))->parent()->husband(),
            Relationship::fixed(...$rel('очув/маќеа', 'очувот/маќеата'))->parent()->married()->spouse(),
            Relationship::fixed(...$rel('паштерка', 'паштерката'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('пасинок', 'пасинокот'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('пасиноче', 'пасиночето'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('поранешна сопруга', 'поранешната сопруга'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('поранешен сопруг', 'поранешниот сопруг'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('поранешен партнер', 'поранешниот партнер'))->divorced()->partner(),
            Relationship::fixed(...$rel('свршеница', 'свршеницата'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('свршеник', 'свршеникот'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('сопруга', 'сопругата'))->wife(),
            Relationship::fixed(...$rel('сопруг', 'сопругот'))->husband(),
            Relationship::fixed(...$rel('сопруг/а', 'сопругот/ата'))->spouse(),
            Relationship::fixed(...$rel('партнер', 'партнерот'))->partner(),
            // In-laws (wife's parents from husband's perspective)
            Relationship::fixed(...$rel('тешта', 'тештата'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('тест', 'тестот'))->married()->spouse()->father(),
            // In-laws (husband's parents from wife's perspective)
            Relationship::fixed(...$rel('свекрва', 'свекрвата'))->spouse()->mother(),
            Relationship::fixed(...$rel('свекор', 'свекорот'))->spouse()->father(),
            // In-laws (children's spouses)
            Relationship::fixed(...$rel('снаа', 'снаата'))->child()->wife(),
            Relationship::fixed(...$rel('зет', 'зетот'))->child()->husband(),
            Relationship::fixed(...$rel('зет/снаа', 'зетот/снаата'))->child()->married()->spouse(),
            // In-laws (sibling's spouses and spouse's siblings)
            Relationship::fixed(...$rel('золва', 'золвата'))->spouse()->sister(),
            Relationship::fixed(...$rel('девер', 'деверот'))->spouse()->brother(),
            Relationship::fixed(...$rel('снаа', 'снаата'))->sibling()->wife(),
            Relationship::fixed(...$rel('зет', 'зетот'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$rel('баба', 'бабата'))->parent()->mother(),
            Relationship::fixed(...$rel('дедо', 'дедото'))->parent()->father(),
            Relationship::fixed(...$rel('баба/дедо', 'бабата/дедото'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('внучка', 'внучката'))->child()->daughter(),
            Relationship::fixed(...$rel('внук', 'внукот'))->child()->son(),
            Relationship::fixed(...$rel('внук/внучка', 'внукот/внучката'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$rel('тетка', 'тетката'))->parent()->sister(),
            Relationship::fixed(...$rel('чичко', 'чичкото'))->father()->brother(),
            Relationship::fixed(...$rel('вујко', 'вујкото'))->mother()->brother(),
            Relationship::fixed(...$rel('чичко/вујко', 'чичкото/вујкото'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$rel('нетјакиња', 'нетјакињата'))->sibling()->daughter(),
            Relationship::fixed(...$rel('нетјак', 'нетјакот'))->sibling()->son(),
            Relationship::fixed(...$rel('нетјак/иња', 'нетјакот/ињата'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$rel('братучетка', 'братучетката'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$rel('братучет', 'братучетот'))->parent()->sibling()->son(),
            Relationship::fixed(...$rel('братучет/ка', 'братучетот/ката'))->parent()->sibling()->child(),
            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'баба', 'бабата'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'дедо', 'дедото'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'баба/дедо', 'бабата/дедото'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'внучка', 'внучката'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'внук', 'внукот'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'внук/внучка', 'внукот/внучката'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'тетка', 'тетката'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'чичко', 'чичкото'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'нетјакиња', 'нетјакињата'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'нетјак', 'нетјакот'))->sibling()->descendant()->male(),
        ];
    }
}
