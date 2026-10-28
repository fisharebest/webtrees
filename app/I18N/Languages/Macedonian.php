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
        /** @return array{string, string} */
    private function rel(string $nom, string $gen): array
    {
        return [$nom, '%s на ' . $gen];
    }

    /** @return array{string, string} */
    private function pra(int $n, string $nom, string $gen): array
    {
        return [
            str_repeat('пра', $n) . $nom,
            '%s на ' . str_repeat('пра', $n) . $gen,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->rel('посвоителка', 'посвоителката'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('посвоител', 'посвоителот'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('посвоител', 'посвоителот'))->adoptive()->parent(),
            Relationship::fixed(...$this->rel('посвоена ќерка', 'посвоената ќерка'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('посвоен син', 'посвоениот син'))->adopted()->son(),
            Relationship::fixed(...$this->rel('посвоено дете', 'посвоеното дете'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->rel('мајка', 'мајката'))->mother(),
            Relationship::fixed(...$this->rel('татко', 'таткото'))->father(),
            Relationship::fixed(...$this->rel('родител', 'родителот'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('ќерка', 'ќерката'))->daughter(),
            Relationship::fixed(...$this->rel('син', 'синот'))->son(),
            Relationship::fixed(...$this->rel('дете', 'детето'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('сестра-близначка', 'сестрата-близначка'))->multiple()->sister(),
            Relationship::fixed(...$this->rel('брат-близнак', 'братот-близнак'))->multiple()->brother(),
            Relationship::fixed(...$this->rel('близнак', 'близнакот'))->multiple()->sibling(),
            Relationship::fixed(...$this->rel('постара сестра', 'постарата сестра'))->older()->sister(),
            Relationship::fixed(...$this->rel('постар брат', 'постариот брат'))->older()->brother(),
            Relationship::fixed(...$this->rel('помлада сестра', 'помладата сестра'))->younger()->sister(),
            Relationship::fixed(...$this->rel('помлад брат', 'помладиот брат'))->younger()->brother(),
            Relationship::fixed(...$this->rel('сестра', 'сестрата'))->sister(),
            Relationship::fixed(...$this->rel('брат', 'братот'))->brother(),
            Relationship::fixed(...$this->rel('брат/сестра', 'братот/сестрата'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('полусестра', 'полусестрата'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('полубрат', 'полубратот'))->parent()->son(),
            Relationship::fixed(...$this->rel('полубрат/полусестра', 'полубратот/полусестрата'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('маќеа', 'маќеата'))->parent()->wife(),
            Relationship::fixed(...$this->rel('очув', 'очувот'))->parent()->husband(),
            Relationship::fixed(...$this->rel('очув/маќеа', 'очувот/маќеата'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('паштерка', 'паштерката'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('пасинок', 'пасинокот'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('пасиноче', 'пасиночето'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('поранешна сопруга', 'поранешната сопруга'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('поранешен сопруг', 'поранешниот сопруг'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('поранешен партнер', 'поранешниот партнер'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('свршеница', 'свршеницата'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('свршеник', 'свршеникот'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('сопруга', 'сопругата'))->wife(),
            Relationship::fixed(...$this->rel('сопруг', 'сопругот'))->husband(),
            Relationship::fixed(...$this->rel('сопруг/а', 'сопругот/ата'))->spouse(),
            Relationship::fixed(...$this->rel('партнер', 'партнерот'))->partner(),
            // In-laws (wife's parents from husband's perspective)
            Relationship::fixed(...$this->rel('тешта', 'тештата'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('тест', 'тестот'))->married()->spouse()->father(),
            // In-laws (husband's parents from wife's perspective)
            Relationship::fixed(...$this->rel('свекрва', 'свекрвата'))->spouse()->mother(),
            Relationship::fixed(...$this->rel('свекор', 'свекорот'))->spouse()->father(),
            // In-laws (children's spouses)
            Relationship::fixed(...$this->rel('снаа', 'снаата'))->child()->wife(),
            Relationship::fixed(...$this->rel('зет', 'зетот'))->child()->husband(),
            Relationship::fixed(...$this->rel('зет/снаа', 'зетот/снаата'))->child()->married()->spouse(),
            // In-laws (sibling's spouses and spouse's siblings)
            Relationship::fixed(...$this->rel('золва', 'золвата'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('девер', 'деверот'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('снаа', 'снаата'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('зет', 'зетот'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('баба', 'бабата'))->parent()->mother(),
            Relationship::fixed(...$this->rel('дедо', 'дедото'))->parent()->father(),
            Relationship::fixed(...$this->rel('баба/дедо', 'бабата/дедото'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('внучка', 'внучката'))->child()->daughter(),
            Relationship::fixed(...$this->rel('внук', 'внукот'))->child()->son(),
            Relationship::fixed(...$this->rel('внук/внучка', 'внукот/внучката'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('тетка', 'тетката'))->parent()->sister(),
            Relationship::fixed(...$this->rel('чичко', 'чичкото'))->father()->brother(),
            Relationship::fixed(...$this->rel('вујко', 'вујкото'))->mother()->brother(),
            Relationship::fixed(...$this->rel('чичко/вујко', 'чичкото/вујкото'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('нетјакиња', 'нетјакињата'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('нетјак', 'нетјакот'))->sibling()->son(),
            Relationship::fixed(...$this->rel('нетјак/иња', 'нетјакот/ињата'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->rel('братучетка', 'братучетката'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('братучет', 'братучетот'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('братучет/ка', 'братучетот/ката'))->parent()->sibling()->child(),
            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'баба', 'бабата'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'дедо', 'дедото'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'баба/дедо', 'бабата/дедото'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'внучка', 'внучката'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'внук', 'внукот'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'внук/внучка', 'внукот/внучката'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'тетка', 'тетката'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'чичко', 'чичкото'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'нетјакиња', 'нетјакињата'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'нетјак', 'нетјакот'))->sibling()->descendant()->male(),
        ];
    }
}
