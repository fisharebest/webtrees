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
     * Generate nominative and genitive forms using Welsh juxtaposition.
     *
     * @return array{string, string}
     */
    private function rel(string $nominative): array
    {
        return [$nominative, '%s ' . $nominative];
    }

    /**
     * Generate nominative and genitive forms for a dynamic ancestor relationship
     * using the repeated "hen " prefix (with soft mutation on the base form).
     *
     * mam-gu → hen fam-gu → hen hen fam-gu
     *
     * @return array{string, string}
     */
    private function hen(int $n, string $mutated): array
    {
        return [
            str_repeat('hen ', $n) . $mutated,
            '%s ' . str_repeat('hen ', $n) . $mutated,
        ];
    }

    /**
     * Generate nominative and genitive forms for a dynamic descendant relationship
     * using the repeated "gor" prefix.
     *
     * wyres → gorwyres → gorgorwyres
     *
     * @return array{string, string}
     */
    private function gor(int $n, string $base): array
    {
        return [
            str_repeat('gor', $n) . $base,
            '%s ' . str_repeat('gor', $n) . $base,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->rel('mam fabwysiedig'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('tad mabwysiedig'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('rhiant mabwysiedig'))->adoptive()->parent(),
            Relationship::fixed(...$this->rel('merch fabwysiedig'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('mab mabwysiedig'))->adopted()->son(),
            Relationship::fixed(...$this->rel('plentyn mabwysiedig'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->rel('mam faeth'))->fostering()->mother(),
            Relationship::fixed(...$this->rel('tad maeth'))->fostering()->father(),
            Relationship::fixed(...$this->rel('rhiant maeth'))->fostering()->parent(),
            Relationship::fixed(...$this->rel('merch faeth'))->fostered()->daughter(),
            Relationship::fixed(...$this->rel('mab maeth'))->fostered()->son(),
            Relationship::fixed(...$this->rel('plentyn maeth'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->rel('mam'))->mother(),
            Relationship::fixed(...$this->rel('tad'))->father(),
            Relationship::fixed(...$this->rel('rhiant'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('merch'))->daughter(),
            Relationship::fixed(...$this->rel('mab'))->son(),
            Relationship::fixed(...$this->rel('plentyn'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('brawd gefell'))->multiple()->brother(),
            Relationship::fixed(...$this->rel('chwaer efell'))->multiple()->sister(),
            Relationship::fixed(...$this->rel('gefell'))->multiple()->sibling(),
            Relationship::fixed(...$this->rel('brawd hŷn'))->older()->brother(),
            Relationship::fixed(...$this->rel('chwaer hŷn'))->older()->sister(),
            Relationship::fixed(...$this->rel('brawd/chwaer hŷn'))->older()->sibling(),
            Relationship::fixed(...$this->rel('brawd iau'))->younger()->brother(),
            Relationship::fixed(...$this->rel('chwaer iau'))->younger()->sister(),
            Relationship::fixed(...$this->rel('brawd/chwaer iau'))->younger()->sibling(),
            Relationship::fixed(...$this->rel('chwaer'))->sister(),
            Relationship::fixed(...$this->rel('brawd'))->brother(),
            Relationship::fixed(...$this->rel('brawd/chwaer'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('hanner chwaer'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('hanner brawd'))->parent()->son(),
            Relationship::fixed(...$this->rel('hanner brawd/chwaer'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('llysfam'))->parent()->wife(),
            Relationship::fixed(...$this->rel('llystad'))->parent()->husband(),
            Relationship::fixed(...$this->rel('llysriant'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('llysferch'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('llysfab'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('llysblentyn'))->married()->spouse()->child(),
            Relationship::fixed(...$this->rel('llyschwer'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('llysfrawd'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->rel('llysfrawd/llyschwer'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('cyn-wraig'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('cyn-ŵr'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('cyn-bartner'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('dyweddi'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('dyweddi'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('gwraig'))->wife(),
            Relationship::fixed(...$this->rel('gŵr'))->husband(),
            Relationship::fixed(...$this->rel('priod'))->spouse(),
            Relationship::fixed(...$this->rel('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$this->rel('mam-yng-nghyfraith'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('tad-yng-nghyfraith'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('rhiant-yng-nghyfraith'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->rel('merch-yng-nghyfraith'))->child()->wife(),
            Relationship::fixed(...$this->rel('mab-yng-nghyfraith'))->child()->husband(),
            Relationship::fixed(...$this->rel('plentyn-yng-nghyfraith'))->child()->married()->spouse(),
            Relationship::fixed(...$this->rel('chwaer-yng-nghyfraith'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('brawd-yng-nghyfraith'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('chwaer-yng-nghyfraith'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('brawd-yng-nghyfraith'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('mam-gu'))->parent()->mother(),
            Relationship::fixed(...$this->rel('tad-cu'))->parent()->father(),
            Relationship::fixed(...$this->rel('taid/nain'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('wyres'))->child()->daughter(),
            Relationship::fixed(...$this->rel('ŵyr'))->child()->son(),
            Relationship::fixed(...$this->rel('ŵyr/wyres'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('modryb'))->parent()->sister(),
            Relationship::fixed(...$this->rel('ewythr'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('nith'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('nai'))->sibling()->son(),
            Relationship::fixed(...$this->rel('nith'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('nai'))->married()->spouse()->sibling()->son(),
            // Cousins (flat — same term for all levels)
            Relationship::fixed(...$this->rel('cyfnither'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('cefnder'))->parent()->sibling()->son(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->hen($n - 1, 'fodryb'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->hen($n - 1, 'ewythr'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->gor($n - 1, 'nith'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->gor($n - 1, 'nith'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->gor($n - 1, 'nai'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->gor($n - 1, 'nai'))->married()->spouse()->sibling()->descendant()->male(),
            // Dynamic — ancestors
            Relationship::dynamic(fn (int $n) => $this->hen($n - 2, 'fam-gu'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->hen($n - 2, 'dad-cu'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->hen($n - 2, 'daid/nain'))->ancestor(),
            // Dynamic — descendants
            Relationship::dynamic(fn (int $n) => $this->gor($n - 2, 'wyres'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->gor($n - 2, 'ŵyr'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->gor($n - 2, 'ŵyr/wyres'))->descendant(),
        ];
    }
}
