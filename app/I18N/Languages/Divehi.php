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
    public function relationships(): array
    {
        // Divehi genitive: "-ގެ" suffix (ge = of)
        $dv = static fn (string $s): array => [$s, $s . 'ގެ %s'];

        return [
            // Parents
            Relationship::fixed(...$dv('މަންމަ'))->mother(),
            Relationship::fixed(...$dv('ބައްޕަ'))->father(),
            Relationship::fixed(...$dv('ބެލެނިވެރި'))->parent(),
            // Children
            Relationship::fixed(...$dv('އަންހެން ދަރި'))->daughter(),
            Relationship::fixed(...$dv('ފިރިހެން ދަރި'))->son(),
            Relationship::fixed(...$dv('ދަރި'))->child(),
            // Siblings
            Relationship::fixed(...$dv('ދައްތަ'))->older()->sister(),
            Relationship::fixed(...$dv('ބޭބެ'))->older()->brother(),
            Relationship::fixed(...$dv('ކޮއްކޮ'))->younger()->sister(),
            Relationship::fixed(...$dv('ކޮއްކޮ'))->younger()->brother(),
            Relationship::fixed(...$dv('ދައްތަ'))->sister(),
            Relationship::fixed(...$dv('ބޭބެ'))->brother(),
            Relationship::fixed(...$dv('އެއްބަނޑު'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$dv('އެއްބައްޕަ ދައްތަ'))->parent()->daughter(),
            Relationship::fixed(...$dv('އެއްބައްޕަ ބޭބެ'))->parent()->son(),
            Relationship::fixed(...$dv('އެއްބައްޕަ'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$dv('ދޮންމަންމަ'))->parent()->wife(),
            Relationship::fixed(...$dv('ދޮންބައްޕަ'))->parent()->husband(),
            Relationship::fixed(...$dv('ދޮން ބެލެނިވެރި'))->parent()->married()->spouse(),
            Relationship::fixed(...$dv('ދޮން އަންހެން ދަރި'))->married()->spouse()->daughter(),
            Relationship::fixed(...$dv('ދޮން ފިރިހެން ދަރި'))->married()->spouse()->son(),
            Relationship::fixed(...$dv('ދޮން ދަރި'))->married()->spouse()->child(),
            Relationship::fixed(...$dv('ދޮން ދައްތަ'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$dv('ދޮން ބޭބެ'))->parent()->spouse()->son(),
            Relationship::fixed(...$dv('ދޮން އެއްބަނޑު'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$dv('ކުރީގެ ބައިވެރިޔާ'))->divorced()->partner()->female(),
            Relationship::fixed(...$dv('ކުރީގެ ބައިވެރިޔާ'))->divorced()->partner()->male(),
            Relationship::fixed(...$dv('ކުރީގެ ބައިވެރިޔާ'))->divorced()->partner(),
            Relationship::fixed(...$dv('ކައިވެނި ހަމަޖެހިފައި'))->engaged()->partner()->female(),
            Relationship::fixed(...$dv('ކައިވެނި ހަމަޖެހިފައި'))->engaged()->partner()->male(),
            Relationship::fixed(...$dv('އަނބި'))->wife(),
            Relationship::fixed(...$dv('ފިރިމީހާ'))->husband(),
            Relationship::fixed(...$dv('ބައިވެރިޔާ'))->spouse(),
            Relationship::fixed(...$dv('ޕާޓްނަރ'))->partner(),
            // In-laws
            Relationship::fixed(...$dv('މައިދައިތަ'))->married()->spouse()->mother(),
            Relationship::fixed(...$dv('ބަފައިކަލުންގެ'))->married()->spouse()->father(),
            Relationship::fixed(...$dv('ފިރިމީހާގެ ބެލެނިވެރި'))->married()->spouse()->parent(),
            Relationship::fixed(...$dv('ޅީދަރި'))->child()->wife(),
            Relationship::fixed(...$dv('ޅީ ފިރިހެން ދަރި'))->child()->husband(),
            Relationship::fixed(...$dv('ފަހަރި'))->spouse()->sister(),
            Relationship::fixed(...$dv('ޅިޔަނު'))->spouse()->brother(),
            Relationship::fixed(...$dv('ފަހަރި'))->sibling()->wife(),
            Relationship::fixed(...$dv('ޅިޔަނު'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$dv('މާމަ'))->parent()->mother(),
            Relationship::fixed(...$dv('ކާފަ'))->parent()->father(),
            Relationship::fixed(...$dv('މާމަ ނުވަތަ ކާފަ'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$dv('މާމަ ދަރި'))->child()->daughter(),
            Relationship::fixed(...$dv('ކާފަ ދަރި'))->child()->son(),
            Relationship::fixed(...$dv('މާމަ/ކާފަ ދަރި'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$dv('ބޮޑުދައިތަ'))->mother()->sister(),
            Relationship::fixed(...$dv('ބޮޑުބޭބެ'))->mother()->brother(),
            Relationship::fixed(...$dv('ބޮޑުދައިތަ'))->father()->sister(),
            Relationship::fixed(...$dv('ބޮޑުބައްޕަ'))->father()->brother(),
            Relationship::fixed(...$dv('ބޮޑުދައިތަ'))->parent()->sister(),
            Relationship::fixed(...$dv('ބޮޑުބައްޕަ'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$dv('އެއްބަނޑު މީހެއްގެ އަންހެން ދަރި'))->sibling()->daughter(),
            Relationship::fixed(...$dv('އެއްބަނޑު މީހެއްގެ ފިރިހެން ދަރި'))->sibling()->son(),
            Relationship::fixed(...$dv('އެއްބަނޑު މީހެއްގެ ދަރި'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$dv('ދެބެއިންގެ ދެ ދަރި'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$dv('ދެބެއިންގެ ދެ ދަރި'))->parent()->sibling()->son(),
            Relationship::fixed(...$dv('ދެބެއިންގެ ދެ ދަރި'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'މާމަ',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'މާމަ' . 'ގެ %s',
            ])->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ' . 'ގެ %s',
            ])->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ކާފަ' . 'ގެ %s',
            ])->ancestor(),
            Relationship::dynamic(static fn (int $n) => [
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ދަރި',
                'މުނި' . str_repeat('މުނި', $n - 3) . 'ދަރި' . 'ގެ %s',
            ])->descendant(),
        ];
    }
}
