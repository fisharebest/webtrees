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
use Fisharebest\Webtrees\Enums\PluralRule;
use Fisharebest\Webtrees\Relationship;

abstract readonly class AbstractEnglish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const array ALPHABET = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    ];

    protected const string DATE_CALCULATED    = 'calculated %s';
    protected const string DATE_ESTIMATED     = 'estimated %s';
    protected const string DATE_INTERPRETED   = 'interpreted %s';
    protected const string ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'BC';
    protected const string LIST_SEPARATOR_AND = ' and ';
    protected const string LIST_SEPARATOR_OR  = ' or ';


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


    protected const array COUSIN = [
        'sibling',
        'first cousin',
        'second cousin',
        'third cousin',
        'fourth cousin',
        'fifth cousin',
        'sixth cousin',
        'seventh cousin',
        'eighth cousin',
        'ninth cousin',
        'tenth cousin',
        'eleventh cousin',
        'twelfth cousin',
        'thirteenth cousin',
        'fourteenth cousin',
        'fifteenth cousin',
        'sixteenth cousin',
        'seventeenth cousin',
        'eighteenth cousin',
        'nineteenth cousin',
        'twentieth cousin',
        'twenty-first cousin',
        'twenty-second cousin',
        'twenty-third cousin',
        'twenty-fourth cousin',
        'twenty-fifth cousin',
        'twenty-sixth cousin',
        'twenty-seventh cousin',
        'twenty-eighth cousin',
        'twenty-ninth cousin',
        'thirtieth cousin',
    ];

    // American English changes "thrice" to "three-times"
    protected const array REMOVED = [
        '',
        ' once removed',
        ' twice removed',
        ' three times removed',
        ' four times removed',
        ' five times removed',
        ' six times removed',
        ' seven times removed',
        ' eight times removed',
        ' nine times removed',
        ' ten times removed',
        ' eleven removed',
        ' twelve removed',
        ' thirteen removed',
        ' fourteen times removed',
        ' fifteen times removed',
        ' sixteen times removed',
        ' seventeen times removed',
        ' eighteen times removed',
        ' nineteen times removed',
        ' twenty times removed',
        ' twenty-one times removed',
        ' twenty-two times removed',
        ' twenty-three times removed',
        ' twenty-four times removed',
        ' twenty-five times removed',
        ' twenty-six times removed',
        ' twenty-seven times removed',
        ' twenty-eight times removed',
        ' twenty-nine times removed',
    ];

    protected const array DIRECTION = [
        -1 => ' descending',
        0  => '',
        1  => ' ascending',
    ];


    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed('adoptive-mother', 'adoptive-mother’s %s')->adoptive()->mother(),
            Relationship::fixed('adoptive-father', 'adoptive-father’s %s')->adoptive()->father(),
            Relationship::fixed('adoptive-parent', 'adoptive-parent’s %s')->adoptive()->parent(),
            Relationship::fixed('adopted-daughter', 'adopted-daughter’s %s')->adopted()->daughter(),
            Relationship::fixed('adopted-son', 'adopted-son’s %s')->adopted()->son(),
            Relationship::fixed('adopted-child', 'adopted-child’s %s')->adopted()->child(),
            // Fostered
            Relationship::fixed('foster-mother', 'foster-mother’s %s')->fostering()->mother(),
            Relationship::fixed('foster-father', 'foster-father’s %s')->fostering()->father(),
            Relationship::fixed('foster-parent', 'foster-parent’s %s')->fostering()->parent(),
            Relationship::fixed('foster-daughter', 'foster-daughter’s %s')->fostered()->daughter(),
            Relationship::fixed('foster-son', 'foster-son’s %s')->fostered()->son(),
            Relationship::fixed('foster-child', 'foster-child’s %s')->fostered()->child(),
            // Parents
            Relationship::fixed('mother', 'mother’s %s')->mother(),
            Relationship::fixed('father', 'father’s %s')->father(),
            Relationship::fixed('parent', 'parent’s %s')->parent(),
            // Children
            Relationship::fixed('daughter', 'daughter’s %s')->daughter(),
            Relationship::fixed('son', 'son’s %s')->son(),
            Relationship::fixed('child', 'child’s %s')->child(),
            // Siblings
            Relationship::fixed('twin sister', 'twin sister’s %s')->twin()->sister(),
            Relationship::fixed('twin brother', 'twin brother’s %s')->twin()->brother(),
            Relationship::fixed('twin sibling', 'twin sibling’s %s')->twin()->sibling(),
            Relationship::fixed('elder sister', 'elder sister’s %s')->older()->sister(),
            Relationship::fixed('elder brother', 'elder brother’s %s')->older()->brother(),
            Relationship::fixed('elder sibling', 'elder sibling’s %s')->older()->sibling(),
            Relationship::fixed('younger sister', 'younger sister’s %s')->younger()->sister(),
            Relationship::fixed('younger brother', 'younger brother’s %s')->younger()->brother(),
            Relationship::fixed('younger sibling', 'younger sibling’s %s')->younger()->sibling(),
            Relationship::fixed('sister', 'sister’s %s')->sister(),
            Relationship::fixed('brother', 'brother’s %s')->brother(),
            Relationship::fixed('sibling', 'sibling’s %s')->sibling(),
            // Half-siblings
            Relationship::fixed('half-sister', 'half-sister’s %s')->parent()->daughter(),
            Relationship::fixed('half-brother', 'half-brother’s %s')->parent()->son(),
            Relationship::fixed('half-sibling', 'half-sibling’s %s')->parent()->child(),
            // Stepfamily
            Relationship::fixed('stepmother', 'stepmother’s %s')->parent()->wife(),
            Relationship::fixed('stepfather', 'stepfather’s %s')->parent()->husband(),
            Relationship::fixed('stepparent', 'stepparent’s %s')->parent()->married()->spouse(),
            Relationship::fixed('stepdaughter', 'stepdaughter’s %s')->married()->spouse()->daughter(),
            Relationship::fixed('stepson', 'stepson’s %s')->married()->spouse()->son(),
            Relationship::fixed('stepchild', 'stepchild’s %s')->married()->spouse()->child(),
            Relationship::fixed('stepsister', 'stepsister’s %s')->parent()->spouse()->daughter(),
            Relationship::fixed('stepbrother', 'stepbrother’s %s')->parent()->spouse()->son(),
            Relationship::fixed('stepsibling', 'stepsibling’s %s')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('ex-wife', 'ex-wife’s %s')->divorced()->partner()->female(),
            Relationship::fixed('ex-husband', 'ex-husband’s %s')->divorced()->partner()->male(),
            Relationship::fixed('ex-spouse', 'ex-spouse’s %s')->divorced()->partner(),
            Relationship::fixed('fiancée', 'fiancée’s %s')->engaged()->partner()->female(),
            Relationship::fixed('fiancé', 'fiancé’s %s')->engaged()->partner()->male(),
            Relationship::fixed('wife', 'wife’s %s')->wife(),
            Relationship::fixed('husband', 'husband’s %s')->husband(),
            Relationship::fixed('spouse', 'spouse’s %s')->spouse(),
            Relationship::fixed('partner', 'partner’s %s')->partner(),
            // In-laws
            Relationship::fixed('mother-in-law', 'mother-in-law’s %s')->married()->spouse()->mother(),
            Relationship::fixed('father-in-law', 'father-in-law’s %s')->married()->spouse()->father(),
            Relationship::fixed('parent-in-law', 'parent-in-law’s %s')->married()->spouse()->parent(),
            Relationship::fixed('daughter-in-law', 'daughter-in-law’s %s')->child()->wife(),
            Relationship::fixed('son-in-law', 'son-in-law’s %s')->child()->husband(),
            Relationship::fixed('child-in-law', 'child-in-law’s %s')->child()->married()->spouse(),
            Relationship::fixed('sister-in-law', 'sister-in-law’s %s')->sibling()->spouse()->sister(),
            Relationship::fixed('brother-in-law', 'brother-in-law’s %s')->sibling()->spouse()->brother(),
            Relationship::fixed('sibling-in-law', 'sibling-in-law’s %s')->sibling()->spouse()->sibling(),
            Relationship::fixed('sister-in-law', 'sister-in-law’s %s')->spouse()->sister(),
            Relationship::fixed('brother-in-law', 'brother-in-law’s %s')->spouse()->brother(),
            Relationship::fixed('sibling-in-law', 'sibling-in-law’s %s')->spouse()->sibling(),
            Relationship::fixed('sister-in-law', 'sister-in-law’s %s')->sibling()->wife(),
            Relationship::fixed('brother-in-law', 'brother-in-law’s %s')->sibling()->husband(),
            Relationship::fixed('sibling-in-law', 'sibling-in-law’s %s')->sibling()->spouse(),
            // Grandparents
            Relationship::fixed('maternal grandmother', 'maternal grandmother’s %s')->mother()->mother(),
            Relationship::fixed('maternal grandfather', 'maternal grandfather’s %s')->mother()->father(),
            Relationship::fixed('maternal grandparent', 'maternal grandfather’s %s')->mother()->parent(),
            Relationship::fixed('paternal grandmother', 'paternal grandmother’s %s')->father()->mother(),
            Relationship::fixed('paternal grandfather', 'paternal grandfather’s %s')->father()->father(),
            Relationship::fixed('paternal grandparent', 'paternal grandfather’s %s')->father()->parent(),
            Relationship::fixed('grandmother', 'grandmother’s %s')->parent()->mother(),
            Relationship::fixed('grandfather', 'grandfather’s %s')->parent()->father(),
            Relationship::fixed('grandparent', 'grandparent’s %s')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('granddaughter', 'granddaughter’s %s')->child()->daughter(),
            Relationship::fixed('grandson', 'grandson’s %s')->child()->son(),
            Relationship::fixed('grandchild', 'grandchild’s %s')->child()->child(),
            // Relationships with dynamically generated names
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'aunt'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'aunt'))->ancestor()->sibling()->wife(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'uncle'))->ancestor()->brother(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'uncle'))->ancestor()->sibling()->husband(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'niece'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'niece'))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'nephew'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'nephew'))->married()->spouse()->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, 'maternal ', 'grandmother'))->mother()->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, 'maternal ', 'grandfather'))->mother()->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, 'paternal ', 'grandmother'))->father()->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, 'paternal ', 'grandfather'))->father()->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 1, '', 'grandparent'))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 2, '', 'granddaughter'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 2, '', 'grandson'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->relationshipsGreat($n - 2, '', 'grandchild'))->descendant(),
            Relationship::dynamic($this->relationshipsCousin(...))->ancestor()->sibling()->descendant(),
        ];
    }

    /**
     * @return array{string,string}
     */
    private function relationshipsCousin(int $up, int $down): array
    {
        $nominative = (static::COUSIN[min($up, $down)] ?? 'distant cousin') .
            (static::REMOVED[abs($up - $down)] ?? ' many times removed') .
            static::DIRECTION[$up <=> $down];

        return [$nominative, $this->relationshipsGenitive($nominative)];
    }

    /*
     * Genitive forms in English are simple/regular, as no relationship name ends in "s".
     */
    private function relationshipsGenitive(string $nominative): string
    {
        return $nominative . '’s %s';
    }

    /**
     * @param int    $n      - number of "greats"
     * @param string $prefix - e.g. "maternal ", "paternal "
     * @param string $suffix - e.g. "grandmother", "grandson"
     *
     * @return array{string,string}
     */
    private function relationshipsGreat(int $n, string $prefix, string $suffix): array
    {
        if ($n > 3) {
            $term = $prefix . 'great ×' . $n . ' ' . $suffix;
        } else {
            $term = $prefix . str_repeat('great-', $n) . $suffix;
        }

        return [$term, $this->relationshipsGenitive($term)];
    }
}
