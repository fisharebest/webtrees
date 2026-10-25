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

use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;

final readonly class Divehi extends AbstractLanguage
{
    protected const string    ENDONYM            = 'ތާނަ';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'dv';
    protected const string    LOCALE_CODE        = 'dv_MV@collation=phonebook';
    protected const Script    SCRIPT             = Script::Thaa;
    protected const Weekday   FIRST_DAY          = Weekday::Friday;
    protected const string    LIST_SEPARATOR     = '، ';
    protected const string    LIST_SEPARATOR_AND = ' އަދި ';
    protected const string    LIST_SEPARATOR_OR  = ' ނުވަތަ ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
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
        'Muharram',
        'Safar',
        'Rabi al-awwal',
        'Rabi al-thani',
        'Jumada al-awwal',
        'Jumada al-thani',
        'Rajab',
        'Sha’aban',
        'Ramadan',
        'Shawwal',
        'Dhu al-Qi’dah',
        'Dhu al-Hijjah',
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
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function dv(string $s): array
    {
        return [$s, $s . 'ގެ %s'];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Parents
            Relationship::fixed(...$this->dv('މަންމަ'))->mother(),
            Relationship::fixed(...$this->dv('ބައްޕަ'))->father(),
            Relationship::fixed(...$this->dv('ބެލެނިވެރި'))->parent(),
            // Children
            Relationship::fixed(...$this->dv('އަންހެން ދަރި'))->daughter(),
            Relationship::fixed(...$this->dv('ފިރިހެން ދަރި'))->son(),
            Relationship::fixed(...$this->dv('ދަރި'))->child(),
            // Siblings
            Relationship::fixed(...$this->dv('ދައްތަ'))->older()->sister(),
            Relationship::fixed(...$this->dv('ބޭބެ'))->older()->brother(),
            Relationship::fixed(...$this->dv('ކޮއްކޮ'))->younger()->sister(),
            Relationship::fixed(...$this->dv('ކޮއްކޮ'))->younger()->brother(),
            Relationship::fixed(...$this->dv('ދައްތަ'))->sister(),
            Relationship::fixed(...$this->dv('ބޭބެ'))->brother(),
            Relationship::fixed(...$this->dv('އެއްބަނޑު'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->dv('އެއްބައްޕަ ދައްތަ'))->parent()->daughter(),
            Relationship::fixed(...$this->dv('އެއްބައްޕަ ބޭބެ'))->parent()->son(),
            Relationship::fixed(...$this->dv('އެއްބައްޕަ'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->dv('ދޮންމަންމަ'))->parent()->wife(),
            Relationship::fixed(...$this->dv('ދޮންބައްޕަ'))->parent()->husband(),
            Relationship::fixed(...$this->dv('ދޮން ބެލެނިވެރި'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->dv('ދޮން އަންހެން ދަރި'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->dv('ދޮން ފިރިހެން ދަރި'))->married()->spouse()->son(),
            Relationship::fixed(...$this->dv('ދޮން ދަރި'))->married()->spouse()->child(),
            Relationship::fixed(...$this->dv('ދޮން ދައްތަ'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->dv('ދޮން ބޭބެ'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->dv('ދޮން އެއްބަނޑު'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->dv('ކުރީގެ ބައިވެރިޔާ'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->dv('ކުރީގެ ބައިވެރިޔާ'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->dv('ކުރީގެ ބައިވެރިޔާ'))->divorced()->partner(),
            Relationship::fixed(...$this->dv('ކައިވެނި ހަމަޖެހިފައި'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->dv('ކައިވެނި ހަމަޖެހިފައި'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->dv('އަނބި'))->wife(),
            Relationship::fixed(...$this->dv('ފިރިމީހާ'))->husband(),
            Relationship::fixed(...$this->dv('ބައިވެރިޔާ'))->spouse(),
            Relationship::fixed(...$this->dv('ޕާޓްނަރ'))->partner(),
            // In-laws
            Relationship::fixed(...$this->dv('މައިދައިތަ'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->dv('ބަފައިކަލުންގެ'))->married()->spouse()->father(),
            Relationship::fixed(...$this->dv('ފިރިމީހާގެ ބެލެނިވެރި'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->dv('ޅީދަރި'))->child()->wife(),
            Relationship::fixed(...$this->dv('ޅީ ފިރިހެން ދަރި'))->child()->husband(),
            Relationship::fixed(...$this->dv('ފަހަރި'))->spouse()->sister(),
            Relationship::fixed(...$this->dv('ޅިޔަނު'))->spouse()->brother(),
            Relationship::fixed(...$this->dv('ފަހަރި'))->sibling()->wife(),
            Relationship::fixed(...$this->dv('ޅިޔަނު'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->dv('މާމަ'))->parent()->mother(),
            Relationship::fixed(...$this->dv('ކާފަ'))->parent()->father(),
            Relationship::fixed(...$this->dv('މާމަ ނުވަތަ ކާފަ'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->dv('މާމަ ދަރި'))->child()->daughter(),
            Relationship::fixed(...$this->dv('ކާފަ ދަރި'))->child()->son(),
            Relationship::fixed(...$this->dv('މާމަ/ކާފަ ދަރި'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->dv('ބޮޑުދައިތަ'))->mother()->sister(),
            Relationship::fixed(...$this->dv('ބޮޑުބޭބެ'))->mother()->brother(),
            Relationship::fixed(...$this->dv('ބޮޑުދައިތަ'))->father()->sister(),
            Relationship::fixed(...$this->dv('ބޮޑުބައްޕަ'))->father()->brother(),
            Relationship::fixed(...$this->dv('ބޮޑުދައިތަ'))->parent()->sister(),
            Relationship::fixed(...$this->dv('ބޮޑުބައްޕަ'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->dv('އެއްބަނޑު މީހެއްގެ އަންހެން ދަރި'))->sibling()->daughter(),
            Relationship::fixed(...$this->dv('އެއްބަނޑު މީހެއްގެ ފިރިހެން ދަރި'))->sibling()->son(),
            Relationship::fixed(...$this->dv('އެއްބަނޑު މީހެއްގެ ދަރި'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->dv('ދެބެއިންގެ ދެ ދަރި'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->dv('ދެބެއިންގެ ދެ ދަރި'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->dv('ދެބެއިންގެ ދެ ދަރި'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'މާމަ',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'މާމަ' . 'ގެ %s',
            ])->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ' . 'ގެ %s',
            ])->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ' . 'ގެ %s',
            ])->ancestor(),
            Relationship::dynamic(fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ދަރި',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ދަރި' . 'ގެ %s',
            ])->descendant(),
        ];
    }
}
