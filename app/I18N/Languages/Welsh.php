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

use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

use function str_repeat;

final readonly class Welsh extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::SixFormsWelsh;

    protected const string    ENDONYM            = 'Cymraeg';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'cy';
    protected const string    LOCALE_CODE        = 'cy_GB@collation=phonebook';
    protected const string    DATE_ABOUT         = 'tua %s';
    protected const string    DATE_AFTER         = 'ar ôl %s';
    protected const string    DATE_BEFORE        = 'cyn %s';
    protected const string    DATE_BETWEEN_AND   = 'rhwng %s a %s';
    protected const string    DATE_CALCULATED    = 'cyfrifwyd %s';
    protected const string    DATE_ESTIMATED     = 'amcangyfrifwyd %s';
    protected const string    DATE_FROM          = 'o %s';
    protected const string    DATE_FROM_TO       = 'o %s hyd %s';
    protected const string    DATE_INTERPRETED   = 'dehonglwyd %s';
    protected const string    DATE_TO            = 'hyd %s';
    protected const string    LIST_SEPARATOR_AND = ' a ';
    protected const string    LIST_SEPARATOR_OR  = ' neu ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Ionawr',
        'Chwefror',
        'Mawrth',
        'Ebrill',
        'Mai',
        'Mehefin',
        'Gorffennaf',
        'Awst',
        'Medi',
        'Hydref',
        'Tachwedd',
        'Rhagfyr',
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
        'Muḥarram',
        'Ṣafar',
        'Rabiʿ al-awwal',
        'Rabiʿ al-thani',
        'Jumādá al-awwal',
        'Jumādá al-thānī',
        'Rajab',
        'Shaʿbān',
        'Ramadan',
        'Shawwal',
        'Dhū al-Qiʿdah',
        'Dhū al-Ḥijjah',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farvardin',
        'Ordibehesht',
        'Khordād',
        'Tīr',
        'Mordād',
        'Shahrīvar',
        'Mehr',
        'Ābān',
        'Āzar',
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
        // Welsh genitive: juxtaposition (possessed + possessor)
        $rel = static fn (string $s): array => [$s, '%s ' . $s];

        // "hen " prefix for great- ancestors (soft mutation applied to base form)
        $hen = static fn (int $n, string $mutated): array => [
            str_repeat('hen ', $n) . $mutated,
            '%s ' . str_repeat('hen ', $n) . $mutated,
        ];

        // "gor" prefix for great- descendants
        $gor = static fn (int $n, string $base): array => [
            str_repeat('gor', $n) . $base,
            '%s ' . str_repeat('gor', $n) . $base,
        ];

        return [
            // Adopted
            Relationship::fixed(...$rel('mam fabwysiedig'))->adoptive()->mother(),
            Relationship::fixed(...$rel('tad mabwysiedig'))->adoptive()->father(),
            Relationship::fixed(...$rel('rhiant mabwysiedig'))->adoptive()->parent(),
            Relationship::fixed(...$rel('merch fabwysiedig'))->adopted()->daughter(),
            Relationship::fixed(...$rel('mab mabwysiedig'))->adopted()->son(),
            Relationship::fixed(...$rel('plentyn mabwysiedig'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$rel('mam faeth'))->fostering()->mother(),
            Relationship::fixed(...$rel('tad maeth'))->fostering()->father(),
            Relationship::fixed(...$rel('rhiant maeth'))->fostering()->parent(),
            Relationship::fixed(...$rel('merch faeth'))->fostered()->daughter(),
            Relationship::fixed(...$rel('mab maeth'))->fostered()->son(),
            Relationship::fixed(...$rel('plentyn maeth'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$rel('mam'))->mother(),
            Relationship::fixed(...$rel('tad'))->father(),
            Relationship::fixed(...$rel('rhiant'))->parent(),
            // Children
            Relationship::fixed(...$rel('merch'))->daughter(),
            Relationship::fixed(...$rel('mab'))->son(),
            Relationship::fixed(...$rel('plentyn'))->child(),
            // Siblings
            Relationship::fixed(...$rel('brawd gefell'))->twin()->brother(),
            Relationship::fixed(...$rel('chwaer efell'))->twin()->sister(),
            Relationship::fixed(...$rel('gefell'))->twin()->sibling(),
            Relationship::fixed(...$rel('brawd hŷn'))->older()->brother(),
            Relationship::fixed(...$rel('chwaer hŷn'))->older()->sister(),
            Relationship::fixed(...$rel('brawd/chwaer hŷn'))->older()->sibling(),
            Relationship::fixed(...$rel('brawd iau'))->younger()->brother(),
            Relationship::fixed(...$rel('chwaer iau'))->younger()->sister(),
            Relationship::fixed(...$rel('brawd/chwaer iau'))->younger()->sibling(),
            Relationship::fixed(...$rel('chwaer'))->sister(),
            Relationship::fixed(...$rel('brawd'))->brother(),
            Relationship::fixed(...$rel('brawd/chwaer'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$rel('hanner chwaer'))->parent()->daughter(),
            Relationship::fixed(...$rel('hanner brawd'))->parent()->son(),
            Relationship::fixed(...$rel('hanner brawd/chwaer'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$rel('llysfam'))->parent()->wife(),
            Relationship::fixed(...$rel('llystad'))->parent()->husband(),
            Relationship::fixed(...$rel('llysriant'))->parent()->married()->spouse(),
            Relationship::fixed(...$rel('llysferch'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('llysfab'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('llysblentyn'))->married()->spouse()->child(),
            Relationship::fixed(...$rel('llyschwer'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$rel('llysfrawd'))->parent()->spouse()->son(),
            Relationship::fixed(...$rel('llysfrawd/llyschwer'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('cyn-wraig'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('cyn-ŵr'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('cyn-bartner'))->divorced()->partner(),
            Relationship::fixed(...$rel('dyweddi'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('dyweddi'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('gwraig'))->wife(),
            Relationship::fixed(...$rel('gŵr'))->husband(),
            Relationship::fixed(...$rel('priod'))->spouse(),
            Relationship::fixed(...$rel('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$rel('mam-yng-nghyfraith'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('tad-yng-nghyfraith'))->married()->spouse()->father(),
            Relationship::fixed(...$rel('rhiant-yng-nghyfraith'))->married()->spouse()->parent(),
            Relationship::fixed(...$rel('merch-yng-nghyfraith'))->child()->wife(),
            Relationship::fixed(...$rel('mab-yng-nghyfraith'))->child()->husband(),
            Relationship::fixed(...$rel('plentyn-yng-nghyfraith'))->child()->married()->spouse(),
            Relationship::fixed(...$rel('chwaer-yng-nghyfraith'))->spouse()->sister(),
            Relationship::fixed(...$rel('brawd-yng-nghyfraith'))->spouse()->brother(),
            Relationship::fixed(...$rel('chwaer-yng-nghyfraith'))->sibling()->wife(),
            Relationship::fixed(...$rel('brawd-yng-nghyfraith'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$rel('mam-gu'))->parent()->mother(),
            Relationship::fixed(...$rel('tad-cu'))->parent()->father(),
            Relationship::fixed(...$rel('taid/nain'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('wyres'))->child()->daughter(),
            Relationship::fixed(...$rel('ŵyr'))->child()->son(),
            Relationship::fixed(...$rel('ŵyr/wyres'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$rel('modryb'))->parent()->sister(),
            Relationship::fixed(...$rel('ewythr'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$rel('nith'))->sibling()->daughter(),
            Relationship::fixed(...$rel('nai'))->sibling()->son(),
            Relationship::fixed(...$rel('nith'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$rel('nai'))->married()->spouse()->sibling()->son(),
            // Cousins (flat — same term for all levels)
            Relationship::fixed(...$rel('cyfnither'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$rel('cefnder'))->parent()->sibling()->son(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $hen($n - 1, 'fodryb'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $hen($n - 1, 'ewythr'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $gor($n - 1, 'nith'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $gor($n - 1, 'nith'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $gor($n - 1, 'nai'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $gor($n - 1, 'nai'))->married()->spouse()->sibling()->descendant()->male(),
            // Dynamic — ancestors
            Relationship::dynamic(static fn (int $n) => $hen($n - 2, 'fam-gu'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $hen($n - 2, 'dad-cu'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $hen($n - 2, 'daid/nain'))->ancestor(),
            // Dynamic — descendants
            Relationship::dynamic(static fn (int $n) => $gor($n - 2, 'wyres'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $gor($n - 2, 'ŵyr'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $gor($n - 2, 'ŵyr/wyres'))->descendant(),
        ];
    }
}
