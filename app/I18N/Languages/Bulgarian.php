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

final readonly class Bulgarian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'български';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'bg';
    protected const string    LOCALE_CODE        = 'bg_BG@collation=phonebook';
    protected const int       MINIMUM_GROUPING_DIGITS = 2;
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Cyrl;
    protected const string    DATE_ABOUT         = 'около %s';
    protected const string    DATE_AFTER         = 'след %s';
    protected const string    DATE_BEFORE        = 'преди %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'преди новата ера';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'новата ера';
    protected const string    LIST_SEPARATOR_AND = ' и ';
    protected const string    LIST_SEPARATOR_OR  = ' или ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Януари',
        'Февруари',
        'Март',
        'Април',
        'Май',
        'Юни',
        'Юли',
        'Август',
        'Септември',
        'Октомври',
        'Ноември',
        'Декември',
    ];

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
        'Vendémiaire',
        'Brumaire',
        'Frimaire',
        'Nivôse',
        'Pluviôse',
        'Ventôse',
        'Germinal',
        'Floréal',
        'Prairial',
        'Messidor',
        'Thermidor',
        'Fructidor',
        'jours complémentaires',
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
        'Джумада аль-уля',
        'Джумада ас-сани',
        'Раджаб',
        'Шаабан',
        'Рамадан',
        'Шаввал',
        'Зу-л-Каада',
        'Зу-л-Хиджа',
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
        UTF8::CYRILLIC_CAPITAL_LETTER_IE,
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
            UTF8::CYRILLIC_CAPITAL_LETTER_I . UTF8::COMBINING_BREVE => UTF8::CYRILLIC_CAPITAL_LETTER_SHORT_I,
            UTF8::CYRILLIC_SMALL_LETTER_I . UTF8::COMBINING_BREVE   => UTF8::CYRILLIC_SMALL_LETTER_SHORT_I,
        ];
    }

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
            Relationship::fixed(...$this->rel('осиновителка', 'осиновителката'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('осиновител', 'осиновителя'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('осиновител', 'осиновителя'))->adoptive()->parent(),
            Relationship::fixed(...$this->rel('осиновена дъщеря', 'осиновената дъщеря'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('осиновен син', 'осиновения син'))->adopted()->son(),
            Relationship::fixed(...$this->rel('осиновено дете', 'осиновеното дете'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->rel('майка', 'майката'))->mother(),
            Relationship::fixed(...$this->rel('баща', 'бащата'))->father(),
            Relationship::fixed(...$this->rel('родител', 'родителя'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('дъщеря', 'дъщерята'))->daughter(),
            Relationship::fixed(...$this->rel('син', 'сина'))->son(),
            Relationship::fixed(...$this->rel('дете', 'детето'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('сестра-близначка', 'сестрата-близначка'))->multiple()->sister(),
            Relationship::fixed(...$this->rel('брат-близнак', 'брата-близнак'))->multiple()->brother(),
            Relationship::fixed(...$this->rel('близнак', 'близнака'))->multiple()->sibling(),
            Relationship::fixed(...$this->rel('по-голяма сестра', 'по-голямата сестра'))->older()->sister(),
            Relationship::fixed(...$this->rel('по-голям брат', 'по-големия брат'))->older()->brother(),
            Relationship::fixed(...$this->rel('по-малка сестра', 'по-малката сестра'))->younger()->sister(),
            Relationship::fixed(...$this->rel('по-малък брат', 'по-малкия брат'))->younger()->brother(),
            Relationship::fixed(...$this->rel('сестра', 'сестрата'))->sister(),
            Relationship::fixed(...$this->rel('брат', 'брата'))->brother(),
            Relationship::fixed(...$this->rel('брат/сестра', 'брата/сестрата'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('полусестра', 'полусестрата'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('полубрат', 'полубрата'))->parent()->son(),
            Relationship::fixed(...$this->rel('полубрат/полусестра', 'полубрата/полусестрата'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('мащеха', 'мащехата'))->parent()->wife(),
            Relationship::fixed(...$this->rel('доведен баща', 'доведения баща'))->parent()->husband(),
            Relationship::fixed(...$this->rel('доведен родител', 'доведения родител'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('доведена дъщеря', 'доведената дъщеря'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('доведен син', 'доведения син'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('доведено дете', 'доведеното дете'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('бивша съпруга', 'бившата съпруга'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('бивш съпруг', 'бившия съпруг'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('бивш партньор', 'бившия партньор'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('годеница', 'годеницата'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('годеник', 'годеника'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('съпруга', 'съпругата'))->wife(),
            Relationship::fixed(...$this->rel('съпруг', 'съпруга'))->husband(),
            Relationship::fixed(...$this->rel('съпруг/а', 'съпруга/та'))->spouse(),
            Relationship::fixed(...$this->rel('партньор', 'партньора'))->partner(),
            // In-laws
            Relationship::fixed(...$this->rel('тъща', 'тъщата'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('тъст', 'тъста'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('свекърва', 'свекървата'))->spouse()->mother(),
            Relationship::fixed(...$this->rel('свекър', 'свекъра'))->spouse()->father(),
            Relationship::fixed(...$this->rel('снаха', 'снахата'))->child()->wife(),
            Relationship::fixed(...$this->rel('зет', 'зета'))->child()->husband(),
            Relationship::fixed(...$this->rel('зет/снаха', 'зета/снахата'))->child()->married()->spouse(),
            Relationship::fixed(...$this->rel('зълва', 'зълвата'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('шурей', 'шурея'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('снаха', 'снахата'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('зет', 'зета'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('баба', 'бабата'))->parent()->mother(),
            Relationship::fixed(...$this->rel('дядо', 'дядото'))->parent()->father(),
            Relationship::fixed(...$this->rel('баба/дядо', 'бабата/дядото'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('внучка', 'внучката'))->child()->daughter(),
            Relationship::fixed(...$this->rel('внук', 'внука'))->child()->son(),
            Relationship::fixed(...$this->rel('внук/внучка', 'внука/внучката'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('леля', 'лелята'))->parent()->sister(),
            Relationship::fixed(...$this->rel('чичо', 'чичото'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('племенница', 'племенницата'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('племенник', 'племенника'))->sibling()->son(),
            Relationship::fixed(...$this->rel('племенник/ца', 'племенника/цата'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->rel('братовчедка', 'братовчедката'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('братовчед', 'братовчеда'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('братовчед/ка', 'братовчеда/ката'))->parent()->sibling()->child(),
            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'баба', 'бабата'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'дядо', 'дядото'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'баба/дядо', 'бабата/дядото'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'внучка', 'внучката'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'внук', 'внука'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 2, 'внук/внучка', 'внука/внучката'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'леля', 'лелята'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'чичо', 'чичото'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'племенница', 'племенницата'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->pra($n - 1, 'племенник', 'племенника'))->sibling()->descendant()->male(),
        ];
    }
}
