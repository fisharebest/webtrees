<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Relationship;

use function abs;
use function array_key_exists;
use function array_merge;
use function array_reduce;
use function array_slice;
use function count;
use function implode;
use function intdiv;
use function min;
use function preg_match;
use function sprintf;
use function strlen;
use function substr;

/**
 * Names for relationships.
 */
class RelationshipService
{
    private const COMPONENTS = [
        'CHIL' => [
            'CHIL' => Relationship::SIBLINGS,
            'HUSB' => Relationship::PARENTS,
            'WIFE' => Relationship::PARENTS,
        ],
        'HUSB' => [
            'CHIL' => Relationship::CHILDREN,
            'HUSB' => Relationship::SPOUSES,
            'WIFE' => Relationship::SPOUSES,
        ],
        'WIFE' => [
            'CHIL' => Relationship::CHILDREN,
            'HUSB' => Relationship::SPOUSES,
            'WIFE' => Relationship::SPOUSES,
        ],
    ];

    /**
     * For close family relationships, such as the families tab, associates, and the family navigator.
     *
     * @param Individual $individual1
     * @param Individual $individual2
     *
     * @return string
     */
    public function getCloseRelationshipName(Individual $individual1, Individual $individual2): string
    {
        $language = app(ModuleService::class)
            ->findByInterface(ModuleLanguageInterface::class, true)
            ->first(fn (ModuleLanguageInterface $language): bool => $language->locale()->languageTag() === I18N::languageTag());

        $path = $this->getCloseRelationship($individual1, $individual2);

        // No relationship found?
        if ($path === []) {
            return '';
        }

        return $this->nameFromPath($path, $language);
    }

    /**
     * Get relationship between two individuals in the gedcom.  This function
     * takes account of pending changes, so we can display names of newly added
     * relations.
     *
     * @param Individual $individual1
     * @param Individual $individual2
     * @param int        $maxlength
     *
     * @return array<Individual|Family> An array of nodes on the relationship path
     */
    private function getCloseRelationship(Individual $individual1, Individual $individual2, int $maxlength = 4): array
    {
        if ($individual1 === $individual2) {
            return [$individual1];
        }

        // Only examine each individual once
        $visited = [
            $individual1->xref() => true,
        ];

        // Build paths out from the first individual
        $paths = [
            [$individual1],
        ];

        // Loop over paths of length 1, 2, 3, ...
        while ($maxlength >= 0) {
            $maxlength--;

            foreach ($paths as $i => $path) {
                // Try each new relation from the end of the path
                $indi = $path[count($path) - 1];

                // Parents and siblings
                foreach ($indi->childFamilies(Auth::PRIV_HIDE) as $family) {
                    $visited[$family->xref()] = true;
                    foreach ($family->spouses(Auth::PRIV_HIDE) as $spouse) {
                        if (!isset($visited[$spouse->xref()])) {
                            $new_path   = $path;
                            $new_path[] = $family;
                            $new_path[] = $spouse;
                            if ($spouse === $individual2) {
                                return $new_path;
                            }

                            $paths[]                  = $new_path;
                            $visited[$spouse->xref()] = true;
                        }
                    }
                    foreach ($family->children(Auth::PRIV_HIDE) as $child) {
                        if (!isset($visited[$child->xref()])) {
                            $new_path   = $path;
                            $new_path[] = $family;
                            $new_path[] = $child;
                            if ($child === $individual2) {
                                return $new_path;
                            }

                            $paths[]                 = $new_path;
                            $visited[$child->xref()] = true;
                        }
                    }
                }

                // Spouses and children
                foreach ($indi->spouseFamilies(Auth::PRIV_HIDE) as $family) {
                    $visited[$family->xref()] = true;
                    foreach ($family->spouses(Auth::PRIV_HIDE) as $spouse) {
                        if (!isset($visited[$spouse->xref()])) {
                            $new_path   = $path;
                            $new_path[] = $family;
                            $new_path[] = $spouse;
                            if ($spouse === $individual2) {
                                return $new_path;
                            }

                            $paths[]                  = $new_path;
                            $visited[$spouse->xref()] = true;
                        }
                    }
                    foreach ($family->children(Auth::PRIV_HIDE) as $child) {
                        if (!isset($visited[$child->xref()])) {
                            $new_path   = $path;
                            $new_path[] = $family;
                            $new_path[] = $child;
                            if ($child === $individual2) {
                                return $new_path;
                            }

                            $paths[]                 = $new_path;
                            $visited[$child->xref()] = true;
                        }
                    }
                }
                unset($paths[$i]);
            }
        }

        return [];
    }

    /**
     * @param array<Individual|Family> $nodes
     * @param ModuleLanguageInterface  $language
     *
     * @return string
     */
    public function nameFromPath(array $nodes, ModuleLanguageInterface $language): string
    {
        // The relationship matching algorithm could be used for this, but it is more efficient to check it here.
        if (count($nodes) === 1) {
            return $this->reflexivePronoun($nodes[0]);
        }

        // The relationship definitions for the language.
        $relationships = $language->relationships();

        // We don't strictly need this, as all the information is contained in the nodes.
        // But it gives us simpler code and better performance.
        // It is also needed for the legacy algorithm.
        $pattern = $this->components($nodes);

        // No definitions for this language?  Use the legacy algorithm.
        if ($relationships === []) {
            return $this->legacyNameAlgorithm(implode('', $pattern), $nodes[0], $nodes[count($nodes) - 1]);
        }

        // Match the relationship, using a longest-substring algorithm.
        $relationships = $this->matchRelationships($nodes, $pattern, $relationships);

        // Reduce the genitive-nominative chain to a single string.
        return array_reduce($relationships, static function (array $carry, array $item): array {
            return [sprintf($carry[1], $item[0]), sprintf($carry[1], $item[1])];
        }, [0 => '', 1 => '%s'])[0];
    }

    /**
     * Generate a reflexive pronoun for an individual
     *
     * @param Individual $individual
     *
     * @return string
     */
    protected function reflexivePronoun(Individual $individual): string
    {
        switch ($individual->sex()) {
            case 'M':
                /* I18N: reflexive pronoun */
                return I18N::translate('himself');
            case 'F':
                /* I18N: reflexive pronoun */
                return I18N::translate('herself');
            default:
                /* I18N: reflexive pronoun - gender neutral version of himself/herself */
                return I18N::translate('themself');
        }
    }

    /**
     * Convert a relationship path into its component pieces; brother, wife, mother, daughter, etc.
     *
     * @param array<Individual|Family> $nodes - Alternating list of Individual and Family objects
     *
     * @return array<string>
     */
    private function components(array $nodes): array
    {
        $pattern = [];

        $count = count($nodes);

        for ($i = 1; $i < $count; $i += 2) {
            $prev   = $nodes[$i - 1];
            $family = $nodes[$i];
            $next   = $nodes[$i + 1];

            preg_match('/\n1 (HUSB|WIFE|CHIL) @' . $prev->xref() . '@/', $family->gedcom(), $match);
            $rel1 = $match[1] ?? 'xxx';

            preg_match('/\n1 (HUSB|WIFE|CHIL) @' . $next->xref() . '@/', $family->gedcom(), $match);
            $rel2 = $match[1] ?? 'xxx';

            $pattern[] = self::COMPONENTS[$rel1][$rel2][$next->sex()] ?? 'xxx';
        }

        return $pattern;
    }

    /**
     * @param array<Individual|Family> $nodes
     * @param array<string>            $pattern
     * @param array<Relationship>      $relationships
     *
     * @return array<Relationship>
     */
    protected function matchRelationships(array $nodes, array $pattern, array $relationships): array
    {
        $count = count($pattern);

        // Look for the longest matchable series of components
        for ($length = $count; $length > 0; $length--) {
            for ($start = $count - $length; $start >= 0; $start--) {
                foreach ($relationships as $relationship) {
                    $path_slice    = array_slice($nodes, $start * 2, $length * 2 + 1);
                    $pattern_slice = array_slice($pattern, $start, $length);
                    $result        = $relationship->match($path_slice, $pattern_slice);

                    if ($result !== null) {
                        $nodes_before   = array_slice($nodes, 0, $start * 2 + 1);
                        $pattern_before = array_slice($pattern, 0, $start);
                        $result_before  = $this->matchRelationships($nodes_before, $pattern_before, $relationships);

                        $nodes_after   = array_slice($nodes, ($start + $length) * 2);
                        $pattern_after = array_slice($pattern, $start + $length);
                        $result_after  = $this->matchRelationships($nodes_after, $pattern_after, $relationships);

                        return array_merge($result_before, [$result], $result_after);
                    }
                }
            }
        }

        return [];
    }

    /**
     * @param string          $path
     * @param Individual|null $person1
     * @param Individual|null $person2
     *
     * @return string
     *
     * @deprecated This code was originally Functions::getRelationshipNameFromPath
     */
    public function legacyNameAlgorithm(string $path, Individual $person1 = null, Individual $person2 = null): string
    {
        // The path does not include the starting person. In some languages, the
        // translation for a man’s (relative) is different from a woman’s (relative),
        // due to inflection.
        $sex1 = $person1 ? $person1->sex() : 'U';

        // The sex of the last person in the relationship determines the name in
        // many cases. e.g. great-aunt / great-uncle
        if (preg_match('/(fat|hus|son|bro)$/', $path) === 1) {
            $sex2 = 'M';
        } elseif (preg_match('/(mot|wif|dau|sis)$/', $path) === 1) {
            $sex2 = 'F';
        } else {
            $sex2 = 'U';
        }

        switch ($path) {
            case '':
                return I18N::translate('self');
            //  Level One relationships
            case 'mot':
                return I18N::translate('mother');
            case 'fat':
                return I18N::translate('father');
            case 'par':
                return I18N::translate('parent');
            case 'hus':
                if ($person1 instanceof Individual && $person2 instanceof Individual) {
                    // We had the linking family earlier, but lost it.  Find it again.
                    foreach ($person1->spouseFamilies(Auth::PRIV_HIDE) as $family) {
                        if ($person2 === $family->spouse($person1)) {
                            $event = $family->facts(['ANUL', 'DIV', 'ENGA', 'MARR'], true, Auth::PRIV_HIDE, true)->last();

                            if ($event instanceof Fact) {
                                switch ($event->tag()) {
                                    case 'FAM:ANUL':
                                    case 'FAM:DIV':
                                        return I18N::translate('ex-husband');
                                    case 'FAM:MARR':
                                        return I18N::translate('husband');
                                    case 'FAM:ENGA':
                                        return I18N::translate('fiancé');
                                }
                            }
                        }
                    }
                }

                return I18N::translateContext('MALE', 'partner');

            case 'wif':
                if ($person1 instanceof Individual && $person2 instanceof Individual) {
                    // We had the linking family earlier, but lost it.  Find it again.
                    foreach ($person1->spouseFamilies(Auth::PRIV_HIDE) as $family) {
                        if ($person2 === $family->spouse($person1)) {
                            $event = $family->facts(['ANUL', 'DIV', 'ENGA', 'MARR'], true, Auth::PRIV_HIDE, true)->last();

                            if ($event instanceof Fact) {
                                switch ($event->tag()) {
                                    case 'FAM:ANUL':
                                    case 'FAM:DIV':
                                        return I18N::translate('ex-wife');
                                    case 'FAM:MARR':
                                        return I18N::translate('wife');
                                    case 'FAM:ENGA':
                                        return I18N::translate('fiancée');
                                }
                            }
                        }
                    }
                }

                return I18N::translateContext('FEMALE', 'partner');
            case 'spo':
                if ($person1 instanceof Individual && $person2 instanceof Individual) {
                    // We had the linking family earlier, but lost it.  Find it again.
                    foreach ($person1->spouseFamilies(Auth::PRIV_HIDE) as $family) {
                        if ($person2 === $family->spouse($person1)) {
                            $event = $family->facts(['ANUL', 'DIV', 'ENGA', 'MARR'], true, Auth::PRIV_HIDE, true)->last();

                            if ($event instanceof Fact) {
                                switch ($event->tag()) {
                                    case 'FAM:ANUL':
                                    case 'FAM:DIV':
                                        return I18N::translate('ex-spouse');
                                    case 'FAM:MARR':
                                        return I18N::translate('spouse');
                                    case 'FAM:ENGA':
                                        return I18N::translate('fiancé(e)');
                                }
                            }
                        }
                    }
                }

                return I18N::translate('partner');

            case 'son':
                return I18N::translate('son');
            case 'dau':
                return I18N::translate('daughter');
            case 'chi':
                return I18N::translate('child');
            case 'bro':
                if ($person1 && $person2) {
                    $dob1 = $person1->getBirthDate();
                    $dob2 = $person2->getBirthDate();
                    if ($dob1->isOK() && $dob2->isOK()) {
                        if (abs($dob1->julianDay() - $dob2->julianDay()) < 2 && $dob1->minimumDate()->day > 0 && $dob2->minimumDate()->day > 0) {
                            // Exclude BEF, AFT, etc.
                            return I18N::translate('twin brother');
                        }

                        if ($dob1->maximumJulianDay() < $dob2->minimumJulianDay()) {
                            return I18N::translate('younger brother');
                        }

                        if ($dob1->minimumJulianDay() > $dob2->maximumJulianDay()) {
                            return I18N::translate('elder brother');
                        }
                    }
                }

                return I18N::translate('brother');
            case 'sis':
                if ($person1 && $person2) {
                    $dob1 = $person1->getBirthDate();
                    $dob2 = $person2->getBirthDate();
                    if ($dob1->isOK() && $dob2->isOK()) {
                        if (abs($dob1->julianDay() - $dob2->julianDay()) < 2 && $dob1->minimumDate()->day > 0 && $dob2->minimumDate()->day > 0) {
                            // Exclude BEF, AFT, etc.
                            return I18N::translate('twin sister');
                        }

                        if ($dob1->maximumJulianDay() < $dob2->minimumJulianDay()) {
                            return I18N::translate('younger sister');
                        }

                        if ($dob1->minimumJulianDay() > $dob2->maximumJulianDay()) {
                            return I18N::translate('elder sister');
                        }
                    }
                }

                return I18N::translate('sister');
            case 'sib':
                if ($person1 && $person2) {
                    $dob1 = $person1->getBirthDate();
                    $dob2 = $person2->getBirthDate();
                    if ($dob1->isOK() && $dob2->isOK()) {
                        if (abs($dob1->julianDay() - $dob2->julianDay()) < 2 && $dob1->minimumDate()->day > 0 && $dob2->minimumDate()->day > 0) {
                            // Exclude BEF, AFT, etc.
                            return I18N::translate('twin sibling');
                        }

                        if ($dob1->maximumJulianDay() < $dob2->minimumJulianDay()) {
                            return I18N::translate('younger sibling');
                        }

                        if ($dob1->minimumJulianDay() > $dob2->maximumJulianDay()) {
                            return I18N::translate('elder sibling');
                        }
                    }
                }

                return I18N::translate('sibling');

            // Level Two relationships
            case 'brochi':
                return I18N::translateContext('brother’s child', 'nephew/niece');
            case 'brodau':
                return I18N::translateContext('brother’s daughter', 'niece');
            case 'broson':
                return I18N::translateContext('brother’s son', 'nephew');
            case 'browif':
                return I18N::translateContext('brother’s wife', 'sister-in-law');
            case 'chichi':
                return I18N::translateContext('child’s child', 'grandchild');
            case 'chidau':
                return I18N::translateContext('child’s daughter', 'granddaughter');
            case 'chihus':
                return I18N::translateContext('child’s husband', 'son-in-law');
            case 'chison':
                return I18N::translateContext('child’s son', 'grandson');
            case 'chispo':
                return I18N::translateContext('child’s spouse', 'son/daughter-in-law');
            case 'chiwif':
                return I18N::translateContext('child’s wife', 'daughter-in-law');
            case 'dauchi':
                return I18N::translateContext('daughter’s child', 'grandchild');
            case 'daudau':
                return I18N::translateContext('daughter’s daughter', 'granddaughter');
            case 'dauhus':
                return I18N::translateContext('daughter’s husband', 'son-in-law');
            case 'dauson':
                return I18N::translateContext('daughter’s son', 'grandson');
            case 'fatbro':
                return I18N::translateContext('father’s brother', 'uncle');
            case 'fatchi':
                return I18N::translateContext('father’s child', 'half-sibling');
            case 'fatdau':
                return I18N::translateContext('father’s daughter', 'half-sister');
            case 'fatfat':
                return I18N::translateContext('father’s father', 'paternal grandfather');
            case 'fatmot':
                return I18N::translateContext('father’s mother', 'paternal grandmother');
            case 'fatpar':
                return I18N::translateContext('father’s parent', 'paternal grandparent');
            case 'fatsib':
                return I18N::translateContext('father’s sibling', 'aunt/uncle');
            case 'fatsis':
                return I18N::translateContext('father’s sister', 'aunt');
            case 'fatson':
                return I18N::translateContext('father’s son', 'half-brother');
            case 'fatwif':
                return I18N::translateContext('father’s wife', 'step-mother');
            case 'husbro':
                return I18N::translateContext('husband’s brother', 'brother-in-law');
            case 'huschi':
                return I18N::translateContext('husband’s child', 'step-child');
            case 'husdau':
                return I18N::translateContext('husband’s daughter', 'step-daughter');
            case 'husfat':
                return I18N::translateContext('husband’s father', 'father-in-law');
            case 'husmot':
                return I18N::translateContext('husband’s mother', 'mother-in-law');
            case 'hussib':
                return I18N::translateContext('husband’s sibling', 'brother/sister-in-law');
            case 'hussis':
                return I18N::translateContext('husband’s sister', 'sister-in-law');
            case 'husson':
                return I18N::translateContext('husband’s son', 'step-son');
            case 'motbro':
                return I18N::translateContext('mother’s brother', 'uncle');
            case 'motchi':
                return I18N::translateContext('mother’s child', 'half-sibling');
            case 'motdau':
                return I18N::translateContext('mother’s daughter', 'half-sister');
            case 'motfat':
                return I18N::translateContext('mother’s father', 'maternal grandfather');
            case 'mothus':
                return I18N::translateContext('mother’s husband', 'step-father');
            case 'motmot':
                return I18N::translateContext('mother’s mother', 'maternal grandmother');
            case 'motpar':
                return I18N::translateContext('mother’s parent', 'maternal grandparent');
            case 'motsib':
                return I18N::translateContext('mother’s sibling', 'aunt/uncle');
            case 'motsis':
                return I18N::translateContext('mother’s sister', 'aunt');
            case 'motson':
                return I18N::translateContext('mother’s son', 'half-brother');
            case 'parbro':
                return I18N::translateContext('parent’s brother', 'uncle');
            case 'parchi':
                return I18N::translateContext('parent’s child', 'half-sibling');
            case 'pardau':
                return I18N::translateContext('parent’s daughter', 'half-sister');
            case 'parfat':
                return I18N::translateContext('parent’s father', 'grandfather');
            case 'parmot':
                return I18N::translateContext('parent’s mother', 'grandmother');
            case 'parpar':
                return I18N::translateContext('parent’s parent', 'grandparent');
            case 'parsib':
                return I18N::translateContext('parent’s sibling', 'aunt/uncle');
            case 'parsis':
                return I18N::translateContext('parent’s sister', 'aunt');
            case 'parson':
                return I18N::translateContext('parent’s son', 'half-brother');
            case 'parspo':
                return I18N::translateContext('parent’s spouse', 'step-parent');
            case 'sibchi':
                return I18N::translateContext('sibling’s child', 'nephew/niece');
            case 'sibdau':
                return I18N::translateContext('sibling’s daughter', 'niece');
            case 'sibson':
                return I18N::translateContext('sibling’s son', 'nephew');
            case 'sibspo':
                return I18N::translateContext('sibling’s spouse', 'brother/sister-in-law');
            case 'sischi':
                return I18N::translateContext('sister’s child', 'nephew/niece');
            case 'sisdau':
                return I18N::translateContext('sister’s daughter', 'niece');
            case 'sishus':
                return I18N::translateContext('sister’s husband', 'brother-in-law');
            case 'sisson':
                return I18N::translateContext('sister’s son', 'nephew');
            case 'sonchi':
                return I18N::translateContext('son’s child', 'grandchild');
            case 'sondau':
                return I18N::translateContext('son’s daughter', 'granddaughter');
            case 'sonson':
                return I18N::translateContext('son’s son', 'grandson');
            case 'sonwif':
                return I18N::translateContext('son’s wife', 'daughter-in-law');
            case 'spobro':
                return I18N::translateContext('spouse’s brother', 'brother-in-law');
            case 'spochi':
                return I18N::translateContext('spouse’s child', 'step-child');
            case 'spodau':
                return I18N::translateContext('spouse’s daughter', 'step-daughter');
            case 'spofat':
                return I18N::translateContext('spouse’s father', 'father-in-law');
            case 'spomot':
                return I18N::translateContext('spouse’s mother', 'mother-in-law');
            case 'sposis':
                return I18N::translateContext('spouse’s sister', 'sister-in-law');
            case 'sposon':
                return I18N::translateContext('spouse’s son', 'step-son');
            case 'spopar':
                return I18N::translateContext('spouse’s parent', 'mother/father-in-law');
            case 'sposib':
                return I18N::translateContext('spouse’s sibling', 'brother/sister-in-law');
            case 'wifbro':
                return I18N::translateContext('wife’s brother', 'brother-in-law');
            case 'wifchi':
                return I18N::translateContext('wife’s child', 'step-child');
            case 'wifdau':
                return I18N::translateContext('wife’s daughter', 'step-daughter');
            case 'wiffat':
                return I18N::translateContext('wife’s father', 'father-in-law');
            case 'wifmot':
                return I18N::translateContext('wife’s mother', 'mother-in-law');
            case 'wifsib':
                return I18N::translateContext('wife’s sibling', 'brother/sister-in-law');
            case 'wifsis':
                return I18N::translateContext('wife’s sister', 'sister-in-law');
            case 'wifson':
                return I18N::translateContext('wife’s son', 'step-son');

            // Level Three relationships
            case 'brochichi':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s child’s child', 'great-nephew/niece');
                }

                return I18N::translateContext('(a woman’s) brother’s child’s child', 'great-nephew/niece');
            case 'brochidau':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s child’s daughter', 'great-niece');
                }

                return I18N::translateContext('(a woman’s) brother’s child’s daughter', 'great-niece');
            case 'brochison':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s child’s son', 'great-nephew');
                }

                return I18N::translateContext('(a woman’s) brother’s child’s son', 'great-nephew');
            case 'brodauchi':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s daughter’s child', 'great-nephew/niece');
                }

                return I18N::translateContext('(a woman’s) brother’s daughter’s child', 'great-nephew/niece');
            case 'brodaudau':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s daughter’s daughter', 'great-niece');
                }

                return I18N::translateContext('(a woman’s) brother’s daughter’s daughter', 'great-niece');
            case 'brodauhus':
                return I18N::translateContext('brother’s daughter’s husband', 'nephew-in-law');
            case 'brodauson':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s daughter’s son', 'great-nephew');
                }

                return I18N::translateContext('(a woman’s) brother’s daughter’s son', 'great-nephew');
            case 'brosonchi':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s son’s child', 'great-nephew/niece');
                }

                return I18N::translateContext('(a woman’s) brother’s son’s child', 'great-nephew/niece');
            case 'brosondau':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s son’s daughter', 'great-niece');
                }

                return I18N::translateContext('(a woman’s) brother’s son’s daughter', 'great-niece');
            case 'brosonson':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) brother’s son’s son', 'great-nephew');
                }

                return I18N::translateContext('(a woman’s) brother’s son’s son', 'great-nephew');
            case 'brosonwif':
                return I18N::translateContext('brother’s son’s wife', 'niece-in-law');
            case 'browifbro':
                return I18N::translateContext('brother’s wife’s brother', 'brother-in-law');
            case 'browifsib':
                return I18N::translateContext('brother’s wife’s sibling', 'brother/sister-in-law');
            case 'browifsis':
                return I18N::translateContext('brother’s wife’s sister', 'sister-in-law');
            case 'chichichi':
                return I18N::translateContext('child’s child’s child', 'great-grandchild');
            case 'chichidau':
                return I18N::translateContext('child’s child’s daughter', 'great-granddaughter');
            case 'chichison':
                return I18N::translateContext('child’s child’s son', 'great-grandson');
            case 'chidauchi':
                return I18N::translateContext('child’s daughter’s child', 'great-grandchild');
            case 'chidaudau':
                return I18N::translateContext('child’s daughter’s daughter', 'great-granddaughter');
            case 'chidauhus':
                return I18N::translateContext('child’s daughter’s husband', 'granddaughter’s husband');
            case 'chidauson':
                return I18N::translateContext('child’s daughter’s son', 'great-grandson');
            case 'chisonchi':
                return I18N::translateContext('child’s son’s child', 'great-grandchild');
            case 'chisondau':
                return I18N::translateContext('child’s son’s daughter', 'great-granddaughter');
            case 'chisonson':
                return I18N::translateContext('child’s son’s son', 'great-grandson');
            case 'chisonwif':
                return I18N::translateContext('child’s son’s wife', 'grandson’s wife');
            case 'dauchichi':
                return I18N::translateContext('daughter’s child’s child', 'great-grandchild');
            case 'dauchidau':
                return I18N::translateContext('daughter’s child’s daughter', 'great-granddaughter');
            case 'dauchison':
                return I18N::translateContext('daughter’s child’s son', 'great-grandson');
            case 'daudauchi':
                return I18N::translateContext('daughter’s daughter’s child', 'great-grandchild');
            case 'daudaudau':
                return I18N::translateContext('daughter’s daughter’s daughter', 'great-granddaughter');
            case 'daudauhus':
                return I18N::translateContext('daughter’s daughter’s husband', 'granddaughter’s husband');
            case 'daudauson':
                return I18N::translateContext('daughter’s daughter’s son', 'great-grandson');
            case 'dauhusfat':
                return I18N::translateContext('daughter’s husband’s father', 'son-in-law’s father');
            case 'dauhusmot':
                return I18N::translateContext('daughter’s husband’s mother', 'son-in-law’s mother');
            case 'dauhuspar':
                return I18N::translateContext('daughter’s husband’s parent', 'son-in-law’s parent');
            case 'dausonchi':
                return I18N::translateContext('daughter’s son’s child', 'great-grandchild');
            case 'dausondau':
                return I18N::translateContext('daughter’s son’s daughter', 'great-granddaughter');
            case 'dausonson':
                return I18N::translateContext('daughter’s son’s son', 'great-grandson');
            case 'dausonwif':
                return I18N::translateContext('daughter’s son’s wife', 'grandson’s wife');
            case 'fatbrochi':
                return I18N::translateContext('father’s brother’s child', 'first cousin');
            case 'fatbrodau':
                return I18N::translateContext('father’s brother’s daughter', 'first cousin');
            case 'fatbroson':
                return I18N::translateContext('father’s brother’s son', 'first cousin');
            case 'fatbrowif':
                return I18N::translateContext('father’s brother’s wife', 'aunt');
            case 'fatfatbro':
                return I18N::translateContext('father’s father’s brother', 'great-uncle');
            case 'fatfatfat':
                return I18N::translateContext('father’s father’s father', 'great-grandfather');
            case 'fatfatmot':
                return I18N::translateContext('father’s father’s mother', 'great-grandmother');
            case 'fatfatpar':
                return I18N::translateContext('father’s father’s parent', 'great-grandparent');
            case 'fatfatsib':
                return I18N::translateContext('father’s father’s sibling', 'great-aunt/uncle');
            case 'fatfatsis':
                return I18N::translateContext('father’s father’s sister', 'great-aunt');
            case 'fatmotbro':
                return I18N::translateContext('father’s mother’s brother', 'great-uncle');
            case 'fatmotfat':
                return I18N::translateContext('father’s mother’s father', 'great-grandfather');
            case 'fatmotmot':
                return I18N::translateContext('father’s mother’s mother', 'great-grandmother');
            case 'fatmotpar':
                return I18N::translateContext('father’s mother’s parent', 'great-grandparent');
            case 'fatmotsib':
                return I18N::translateContext('father’s mother’s sibling', 'great-aunt/uncle');
            case 'fatmotsis':
                return I18N::translateContext('father’s mother’s sister', 'great-aunt');
            case 'fatparbro':
                return I18N::translateContext('father’s parent’s brother', 'great-uncle');
            case 'fatparfat':
                return I18N::translateContext('father’s parent’s father', 'great-grandfather');
            case 'fatparmot':
                return I18N::translateContext('father’s parent’s mother', 'great-grandmother');
            case 'fatparpar':
                return I18N::translateContext('father’s parent’s parent', 'great-grandparent');
            case 'fatparsib':
                return I18N::translateContext('father’s parent’s sibling', 'great-aunt/uncle');
            case 'fatparsis':
                return I18N::translateContext('father’s parent’s sister', 'great-aunt');
            case 'fatsischi':
                return I18N::translateContext('father’s sister’s child', 'first cousin');
            case 'fatsisdau':
                return I18N::translateContext('father’s sister’s daughter', 'first cousin');
            case 'fatsishus':
                return I18N::translateContext('father’s sister’s husband', 'uncle');
            case 'fatsisson':
                return I18N::translateContext('father’s sister’s son', 'first cousin');
            case 'fatwifchi':
                return I18N::translateContext('father’s wife’s child', 'step-sibling');
            case 'fatwifdau':
                return I18N::translateContext('father’s wife’s daughter', 'step-sister');
            case 'fatwifson':
                return I18N::translateContext('father’s wife’s son', 'step-brother');
            case 'husbrowif':
                return I18N::translateContext('husband’s brother’s wife', 'sister-in-law');
            case 'hussishus':
                return I18N::translateContext('husband’s sister’s husband', 'brother-in-law');
            case 'hussibchi':
                return I18N::translateContext('husband’s sibling’s child', 'nephew/niece');
            case 'hussischi':
                return I18N::translateContext('husband’s sister’s child', 'nephew/niece');
            case 'husbrochi':
                return I18N::translateContext('husband’s brother’s child', 'nephew/niece');
            case 'hussibdau':
                return I18N::translateContext('husband’s sibling’s daughter', 'niece');
            case 'hussisdau':
                return I18N::translateContext('husband’s sister’s daughter', 'niece');
            case 'husbrodau':
                return I18N::translateContext('husband’s brother’s daughter', 'niece');
            case 'hussibson':
                return I18N::translateContext('husband’s sibling’s son', 'nephew');
            case 'hussisson':
                return I18N::translateContext('husband’s sister’s son', 'nephew');
            case 'husbroson':
                return I18N::translateContext('husband’s brother’s son', 'nephew');
            case 'motbrochi':
                return I18N::translateContext('mother’s brother’s child', 'first cousin');
            case 'motbrodau':
                return I18N::translateContext('mother’s brother’s daughter', 'first cousin');
            case 'motbroson':
                return I18N::translateContext('mother’s brother’s son', 'first cousin');
            case 'motbrowif':
                return I18N::translateContext('mother’s brother’s wife', 'aunt');
            case 'motfatbro':
                return I18N::translateContext('mother’s father’s brother', 'great-uncle');
            case 'motfatfat':
                return I18N::translateContext('mother’s father’s father', 'great-grandfather');
            case 'motfatmot':
                return I18N::translateContext('mother’s father’s mother', 'great-grandmother');
            case 'motfatpar':
                return I18N::translateContext('mother’s father’s parent', 'great-grandparent');
            case 'motfatsib':
                return I18N::translateContext('mother’s father’s sibling', 'great-aunt/uncle');
            case 'motfatsis':
                return I18N::translateContext('mother’s father’s sister', 'great-aunt');
            case 'mothuschi':
                return I18N::translateContext('mother’s husband’s child', 'step-sibling');
            case 'mothusdau':
                return I18N::translateContext('mother’s husband’s daughter', 'step-sister');
            case 'mothusson':
                return I18N::translateContext('mother’s husband’s son', 'step-brother');
            case 'motmotbro':
                return I18N::translateContext('mother’s mother’s brother', 'great-uncle');
            case 'motmotfat':
                return I18N::translateContext('mother’s mother’s father', 'great-grandfather');
            case 'motmotmot':
                return I18N::translateContext('mother’s mother’s mother', 'great-grandmother');
            case 'motmotpar':
                return I18N::translateContext('mother’s mother’s parent', 'great-grandparent');
            case 'motmotsib':
                return I18N::translateContext('mother’s mother’s sibling', 'great-aunt/uncle');
            case 'motmotsis':
                return I18N::translateContext('mother’s mother’s sister', 'great-aunt');
            case 'motparbro':
                return I18N::translateContext('mother’s parent’s brother', 'great-uncle');
            case 'motparfat':
                return I18N::translateContext('mother’s parent’s father', 'great-grandfather');
            case 'motparmot':
                return I18N::translateContext('mother’s parent’s mother', 'great-grandmother');
            case 'motparpar':
                return I18N::translateContext('mother’s parent’s parent', 'great-grandparent');
            case 'motparsib':
                return I18N::translateContext('mother’s parent’s sibling', 'great-aunt/uncle');
            case 'motparsis':
                return I18N::translateContext('mother’s parent’s sister', 'great-aunt');
            case 'motsischi':
                return I18N::translateContext('mother’s sister’s child', 'first cousin');
            case 'motsisdau':
                return I18N::translateContext('mother’s sister’s daughter', 'first cousin');
            case 'motsishus':
                return I18N::translateContext('mother’s sister’s husband', 'uncle');
            case 'motsisson':
                return I18N::translateContext('mother’s sister’s son', 'first cousin');
            case 'parbrowif':
                return I18N::translateContext('parent’s brother’s wife', 'aunt');
            case 'parfatbro':
                return I18N::translateContext('parent’s father’s brother', 'great-uncle');
            case 'parfatfat':
                return I18N::translateContext('parent’s father’s father', 'great-grandfather');
            case 'parfatmot':
                return I18N::translateContext('parent’s father’s mother', 'great-grandmother');
            case 'parfatpar':
                return I18N::translateContext('parent’s father’s parent', 'great-grandparent');
            case 'parfatsib':
                return I18N::translateContext('parent’s father’s sibling', 'great-aunt/uncle');
            case 'parfatsis':
                return I18N::translateContext('parent’s father’s sister', 'great-aunt');
            case 'parmotbro':
                return I18N::translateContext('parent’s mother’s brother', 'great-uncle');
            case 'parmotfat':
                return I18N::translateContext('parent’s mother’s father', 'great-grandfather');
            case 'parmotmot':
                return I18N::translateContext('parent’s mother’s mother', 'great-grandmother');
            case 'parmotpar':
                return I18N::translateContext('parent’s mother’s parent', 'great-grandparent');
            case 'parmotsib':
                return I18N::translateContext('parent’s mother’s sibling', 'great-aunt/uncle');
            case 'parmotsis':
                return I18N::translateContext('parent’s mother’s sister', 'great-aunt');
            case 'parparbro':
                return I18N::translateContext('parent’s parent’s brother', 'great-uncle');
            case 'parparfat':
                return I18N::translateContext('parent’s parent’s father', 'great-grandfather');
            case 'parparmot':
                return I18N::translateContext('parent’s parent’s mother', 'great-grandmother');
            case 'parparpar':
                return I18N::translateContext('parent’s parent’s parent', 'great-grandparent');
            case 'parparsib':
                return I18N::translateContext('parent’s parent’s sibling', 'great-aunt/uncle');
            case 'parparsis':
                return I18N::translateContext('parent’s parent’s sister', 'great-aunt');
            case 'parsishus':
                return I18N::translateContext('parent’s sister’s husband', 'uncle');
            case 'parspochi':
                return I18N::translateContext('parent’s spouse’s child', 'step-sibling');
            case 'parspodau':
                return I18N::translateContext('parent’s spouse’s daughter', 'step-sister');
            case 'parsposon':
                return I18N::translateContext('parent’s spouse’s son', 'step-brother');
            case 'sibchichi':
                return I18N::translateContext('sibling’s child’s child', 'great-nephew/niece');
            case 'sibchidau':
                return I18N::translateContext('sibling’s child’s daughter', 'great-niece');
            case 'sibchison':
                return I18N::translateContext('sibling’s child’s son', 'great-nephew');
            case 'sibdauchi':
                return I18N::translateContext('sibling’s daughter’s child', 'great-nephew/niece');
            case 'sibdaudau':
                return I18N::translateContext('sibling’s daughter’s daughter', 'great-niece');
            case 'sibdauhus':
                return I18N::translateContext('sibling’s daughter’s husband', 'nephew-in-law');
            case 'sibdauson':
                return I18N::translateContext('sibling’s daughter’s son', 'great-nephew');
            case 'sibsonchi':
                return I18N::translateContext('sibling’s son’s child', 'great-nephew/niece');
            case 'sibsondau':
                return I18N::translateContext('sibling’s son’s daughter', 'great-niece');
            case 'sibsonson':
                return I18N::translateContext('sibling’s son’s son', 'great-nephew');
            case 'sibsonwif':
                return I18N::translateContext('sibling’s son’s wife', 'niece-in-law');
            case 'sischichi':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s child’s child', 'great-nephew/niece');
                }

                return I18N::translateContext('(a woman’s) sister’s child’s child', 'great-nephew/niece');
            case 'sischidau':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s child’s daughter', 'great-niece');
                }

                return I18N::translateContext('(a woman’s) sister’s child’s daughter', 'great-niece');
            case 'sischison':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s child’s son', 'great-nephew');
                }

                return I18N::translateContext('(a woman’s) sister’s child’s son', 'great-nephew');
            case 'sisdauchi':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s daughter’s child', 'great-nephew/niece');
                }

                return I18N::translateContext('(a woman’s) sister’s daughter’s child', 'great-nephew/niece');
            case 'sisdaudau':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s daughter’s daughter', 'great-niece');
                }

                return I18N::translateContext('(a woman’s) sister’s daughter’s daughter', 'great-niece');
            case 'sisdauhus':
                return I18N::translateContext('sisters’s daughter’s husband', 'nephew-in-law');
            case 'sisdauson':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s daughter’s son', 'great-nephew');
                }

                return I18N::translateContext('(a woman’s) sister’s daughter’s son', 'great-nephew');
            case 'sishusbro':
                return I18N::translateContext('sister’s husband’s brother', 'brother-in-law');
            case 'sishussib':
                return I18N::translateContext('sister’s husband’s sibling', 'brother/sister-in-law');
            case 'sishussis':
                return I18N::translateContext('sister’s husband’s sister', 'sister-in-law');
            case 'sissonchi':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s son’s child', 'great-nephew/niece');
                }

                return I18N::translateContext('(a woman’s) sister’s son’s child', 'great-nephew/niece');
            case 'sissondau':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s son’s daughter', 'great-niece');
                }

                return I18N::translateContext('(a woman’s) sister’s son’s daughter', 'great-niece');
            case 'sissonson':
                if ($sex1 === 'M') {
                    return I18N::translateContext('(a man’s) sister’s son’s son', 'great-nephew');
                }

                return I18N::translateContext('(a woman’s) sister’s son’s son', 'great-nephew');
            case 'sissonwif':
                return I18N::translateContext('sisters’s son’s wife', 'niece-in-law');
            case 'sonchichi':
                return I18N::translateContext('son’s child’s child', 'great-grandchild');
            case 'sonchidau':
                return I18N::translateContext('son’s child’s daughter', 'great-granddaughter');
            case 'sonchison':
                return I18N::translateContext('son’s child’s son', 'great-grandson');
            case 'sondauchi':
                return I18N::translateContext('son’s daughter’s child', 'great-grandchild');
            case 'sondaudau':
                return I18N::translateContext('son’s daughter’s daughter', 'great-granddaughter');
            case 'sondauhus':
                return I18N::translateContext('son’s daughter’s husband', 'granddaughter’s husband');
            case 'sondauson':
                return I18N::translateContext('son’s daughter’s son', 'great-grandson');
            case 'sonsonchi':
                return I18N::translateContext('son’s son’s child', 'great-grandchild');
            case 'sonsondau':
                return I18N::translateContext('son’s son’s daughter', 'great-granddaughter');
            case 'sonsonson':
                return I18N::translateContext('son’s son’s son', 'great-grandson');
            case 'sonsonwif':
                return I18N::translateContext('son’s son’s wife', 'grandson’s wife');
            case 'sonwiffat':
                return I18N::translateContext('son’s wife’s father', 'daughter-in-law’s father');
            case 'sonwifmot':
                return I18N::translateContext('son’s wife’s mother', 'daughter-in-law’s mother');
            case 'sonwifpar':
                return I18N::translateContext('son’s wife’s parent', 'daughter-in-law’s parent');
            case 'wifbrowif':
                return I18N::translateContext('wife’s brother’s wife', 'sister-in-law');
            case 'wifsishus':
                return I18N::translateContext('wife’s sister’s husband', 'brother-in-law');
            case 'wifsibchi':
                return I18N::translateContext('wife’s sibling’s child', 'nephew/niece');
            case 'wifsischi':
                return I18N::translateContext('wife’s sister’s child', 'nephew/niece');
            case 'wifbrochi':
                return I18N::translateContext('wife’s brother’s child', 'nephew/niece');
            case 'wifsibdau':
                return I18N::translateContext('wife’s sibling’s daughter', 'niece');
            case 'wifsisdau':
                return I18N::translateContext('wife’s sister’s daughter', 'niece');
            case 'wifbrodau':
                return I18N::translateContext('wife’s brother’s daughter', 'niece');
            case 'wifsibson':
                return I18N::translateContext('wife’s sibling’s son', 'nephew');
            case 'wifsisson':
                return I18N::translateContext('wife’s sister’s son', 'nephew');
            case 'wifbroson':
                return I18N::translateContext('wife’s brother’s son', 'nephew');

            // Some “special case” level four relationships that have specific names in certain languages
            case 'fatfatbrowif':
                return I18N::translateContext('father’s father’s brother’s wife', 'great-aunt');
            case 'fatfatsibspo':
                return I18N::translateContext('father’s father’s sibling’s spouse', 'great-aunt/uncle');
            case 'fatfatsishus':
                return I18N::translateContext('father’s father’s sister’s husband', 'great-uncle');
            case 'fatmotbrowif':
                return I18N::translateContext('father’s mother’s brother’s wife', 'great-aunt');
            case 'fatmotsibspo':
                return I18N::translateContext('father’s mother’s sibling’s spouse', 'great-aunt/uncle');
            case 'fatmotsishus':
                return I18N::translateContext('father’s mother’s sister’s husband', 'great-uncle');
            case 'fatparbrowif':
                return I18N::translateContext('father’s parent’s brother’s wife', 'great-aunt');
            case 'fatparsibspo':
                return I18N::translateContext('father’s parent’s sibling’s spouse', 'great-aunt/uncle');
            case 'fatparsishus':
                return I18N::translateContext('father’s parent’s sister’s husband', 'great-uncle');
            case 'motfatbrowif':
                return I18N::translateContext('mother’s father’s brother’s wife', 'great-aunt');
            case 'motfatsibspo':
                return I18N::translateContext('mother’s father’s sibling’s spouse', 'great-aunt/uncle');
            case 'motfatsishus':
                return I18N::translateContext('mother’s father’s sister’s husband', 'great-uncle');
            case 'motmotbrowif':
                return I18N::translateContext('mother’s mother’s brother’s wife', 'great-aunt');
            case 'motmotsibspo':
                return I18N::translateContext('mother’s mother’s sibling’s spouse', 'great-aunt/uncle');
            case 'motmotsishus':
                return I18N::translateContext('mother’s mother’s sister’s husband', 'great-uncle');
            case 'motparbrowif':
                return I18N::translateContext('mother’s parent’s brother’s wife', 'great-aunt');
            case 'motparsibspo':
                return I18N::translateContext('mother’s parent’s sibling’s spouse', 'great-aunt/uncle');
            case 'motparsishus':
                return I18N::translateContext('mother’s parent’s sister’s husband', 'great-uncle');
            case 'parfatbrowif':
                return I18N::translateContext('parent’s father’s brother’s wife', 'great-aunt');
            case 'parfatsibspo':
                return I18N::translateContext('parent’s father’s sibling’s spouse', 'great-aunt/uncle');
            case 'parfatsishus':
                return I18N::translateContext('parent’s father’s sister’s husband', 'great-uncle');
            case 'parmotbrowif':
                return I18N::translateContext('parent’s mother’s brother’s wife', 'great-aunt');
            case 'parmotsibspo':
                return I18N::translateContext('parent’s mother’s sibling’s spouse', 'great-aunt/uncle');
            case 'parmotsishus':
                return I18N::translateContext('parent’s mother’s sister’s husband', 'great-uncle');
            case 'parparbrowif':
                return I18N::translateContext('parent’s parent’s brother’s wife', 'great-aunt');
            case 'parparsibspo':
                return I18N::translateContext('parent’s parent’s sibling’s spouse', 'great-aunt/uncle');
            case 'parparsishus':
                return I18N::translateContext('parent’s parent’s sister’s husband', 'great-uncle');
            case 'fatfatbrodau':
                return I18N::translateContext('father’s father’s brother’s daughter', 'first cousin once removed ascending');
            case 'fatfatbroson':
                return I18N::translateContext('father’s father’s brother’s son', 'first cousin once removed ascending');
            case 'fatfatbrochi':
                return I18N::translateContext('father’s father’s brother’s child', 'first cousin once removed ascending');
            case 'fatfatsisdau':
                return I18N::translateContext('father’s father’s sister’s daughter', 'first cousin once removed ascending');
            case 'fatfatsisson':
                return I18N::translateContext('father’s father’s sister’s son', 'first cousin once removed ascending');
            case 'fatfatsischi':
                return I18N::translateContext('father’s father’s sister’s child', 'first cousin once removed ascending');
            case 'fatmotbrodau':
                return I18N::translateContext('father’s mother’s brother’s daughter', 'first cousin once removed ascending');
            case 'fatmotbroson':
                return I18N::translateContext('father’s mother’s brother’s son', 'first cousin once removed ascending');
            case 'fatmotbrochi':
                return I18N::translateContext('father’s mother’s brother’s child', 'first cousin once removed ascending');
            case 'fatmotsisdau':
                return I18N::translateContext('father’s mother’s sister’s daughter', 'first cousin once removed ascending');
            case 'fatmotsisson':
                return I18N::translateContext('father’s mother’s sister’s son', 'first cousin once removed ascending');
            case 'fatmotsischi':
                return I18N::translateContext('father’s mother’s sister’s child', 'first cousin once removed ascending');
            case 'motfatbrodau':
                return I18N::translateContext('mother’s father’s brother’s daughter', 'first cousin once removed ascending');
            case 'motfatbroson':
                return I18N::translateContext('mother’s father’s brother’s son', 'first cousin once removed ascending');
            case 'motfatbrochi':
                return I18N::translateContext('mother’s father’s brother’s child', 'first cousin once removed ascending');
            case 'motfatsisdau':
                return I18N::translateContext('mother’s father’s sister’s daughter', 'first cousin once removed ascending');
            case 'motfatsisson':
                return I18N::translateContext('mother’s father’s sister’s son', 'first cousin once removed ascending');
            case 'motfatsischi':
                return I18N::translateContext('mother’s father’s sister’s child', 'first cousin once removed ascending');
            case 'motmotbrodau':
                return I18N::translateContext('mother’s mother’s brother’s daughter', 'first cousin once removed ascending');
            case 'motmotbroson':
                return I18N::translateContext('mother’s mother’s brother’s son', 'first cousin once removed ascending');
            case 'motmotbrochi':
                return I18N::translateContext('mother’s mother’s brother’s child', 'first cousin once removed ascending');
            case 'motmotsisdau':
                return I18N::translateContext('mother’s mother’s sister’s daughter', 'first cousin once removed ascending');
            case 'motmotsisson':
                return I18N::translateContext('mother’s mother’s sister’s son', 'first cousin once removed ascending');
            case 'motmotsischi':
                return I18N::translateContext('mother’s mother’s sister’s child', 'first cousin once removed ascending');
        }

        // Some “special case” level five relationships that have specific names in certain languages
        if (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandfather’s brother’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandfather’s brother’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandfather’s brother’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandfather’s sister’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandfather’s sister’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandfather’s sister’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandfather’s sibling’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandfather’s sibling’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandfather’s sibling’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motbro(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandmother’s brother’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motbro(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandmother’s brother’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motbro(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandmother’s brother’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motsis(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandmother’s sister’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motsis(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandmother’s sister’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motsis(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandmother’s sister’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motsib(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandmother’s sibling’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motsib(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandmother’s sibling’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)motsib(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandmother’s sibling’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parbro(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandparent’s brother’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parbro(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandparent’s brother’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parbro(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandparent’s brother’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parsis(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandparent’s sister’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parsis(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandparent’s sister’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parsis(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandparent’s sister’s grandchild', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parsib(son|dau|chi)dau$/', $path) === 1) {
            return I18N::translateContext('grandparent’s sibling’s granddaughter', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parsib(son|dau|chi)son$/', $path) === 1) {
            return I18N::translateContext('grandparent’s sibling’s grandson', 'second cousin');
        }

        if (preg_match('/^(mot|fat|par)parsib(son|dau|chi)chi$/', $path) === 1) {
            return I18N::translateContext('grandparent’s sibling’s grandchild', 'second cousin');
        }

        // Look for generic/pattern relationships.
        if (preg_match('/^((?:mot|fat|par)+)(bro|sis|sib)$/', $path, $match) === 1) {
            // siblings of direct ancestors
            $up       = intdiv(strlen($match[1]), 3);
            $bef_last = substr($path, -6, 3);
            switch ($up) {
                case 3:
                    if ($sex2 === 'M') {
                        if ($bef_last === 'fat') {
                            return I18N::translateContext('great-grandfather’s brother', 'great-great-uncle');
                        }

                        if ($bef_last === 'mot') {
                            return I18N::translateContext('great-grandmother’s brother', 'great-great-uncle');
                        }

                        return I18N::translateContext('great-grandparent’s brother', 'great-great-uncle');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great-great-aunt');
                    }

                    return I18N::translate('great-great-aunt/uncle');

                case 4:
                    if ($sex2 === 'M') {
                        if ($bef_last === 'fat') {
                            return I18N::translateContext('great-great-grandfather’s brother', 'great-great-great-uncle');
                        }

                        if ($bef_last === 'mot') {
                            return I18N::translateContext('great-great-grandmother’s brother', 'great-great-great-uncle');
                        }

                        return I18N::translateContext('great-great-grandparent’s brother', 'great-great-great-uncle');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great-great-great-aunt');
                    }

                    return I18N::translate('great-great-great-aunt/uncle');

                case 5:
                    if ($sex2 === 'M') {
                        if ($bef_last === 'fat') {
                            return I18N::translateContext('great-great-great-grandfather’s brother', 'great ×4 uncle');
                        }

                        if ($bef_last === 'mot') {
                            return I18N::translateContext('great-great-great-grandmother’s brother', 'great ×4 uncle');
                        }

                        return I18N::translateContext('great-great-great-grandparent’s brother', 'great ×4 uncle');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×4 aunt');
                    }

                    return I18N::translate('great ×4 aunt/uncle');

                case 6:
                    if ($sex2 === 'M') {
                        if ($bef_last === 'fat') {
                            return I18N::translateContext('great ×4 grandfather’s brother', 'great ×5 uncle');
                        }

                        if ($bef_last === 'mot') {
                            return I18N::translateContext('great ×4 grandmother’s brother', 'great ×5 uncle');
                        }

                        return I18N::translateContext('great ×4 grandparent’s brother', 'great ×5 uncle');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×5 aunt');
                    }

                    return I18N::translate('great ×5 aunt/uncle');

                case 7:
                    if ($sex2 === 'M') {
                        if ($bef_last === 'fat') {
                            return I18N::translateContext('great ×5 grandfather’s brother', 'great ×6 uncle');
                        }

                        if ($bef_last === 'mot') {
                            return I18N::translateContext('great ×5 grandmother’s brother', 'great ×6 uncle');
                        }

                        return I18N::translateContext('great ×5 grandparent’s brother', 'great ×6 uncle');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×6 aunt');
                    }

                    return I18N::translate('great ×6 aunt/uncle');

                case 8:
                    if ($sex2 === 'M') {
                        if ($bef_last === 'fat') {
                            return I18N::translateContext('great ×6 grandfather’s brother', 'great ×7 uncle');
                        }

                        if ($bef_last === 'mot') {
                            return I18N::translateContext('great ×6 grandmother’s brother', 'great ×7 uncle');
                        }

                        return I18N::translateContext('great ×6 grandparent’s brother', 'great ×7 uncle');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×7 aunt');
                    }

                    return I18N::translate('great ×7 aunt/uncle');

                default:
                    // Different languages have different rules for naming generations.
                    // An English great ×12 uncle is a Danish great ×10 uncle.
                    //
                    // Need to find out which languages use which rules.
                    switch (I18N::languageTag()) {
                        case 'da':
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s uncle', I18N::number($up - 4));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s aunt', I18N::number($up - 4));
                            }

                            return I18N::translate('great ×%s aunt/uncle', I18N::number($up - 4));

                        case 'pl':
                            if ($sex2 === 'M') {
                                if ($bef_last === 'fat') {
                                    return I18N::translateContext('great ×(%s-1) grandfather’s brother', 'great ×%s uncle', I18N::number($up - 2));
                                }

                                if ($bef_last === 'mot') {
                                    return I18N::translateContext('great ×(%s-1) grandmother’s brother', 'great ×%s uncle', I18N::number($up - 2));
                                }

                                return I18N::translateContext('great ×(%s-1) grandparent’s brother', 'great ×%s uncle', I18N::number($up - 2));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s aunt', I18N::number($up - 2));
                            }

                            return I18N::translate('great ×%s aunt/uncle', I18N::number($up - 2));

                        case 'ko': // Source : Jeongwan Nam (jeongwann@gmail.com)
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s uncle', I18N::number($up + 1));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s aunt', I18N::number($up + 1));
                            }

                            return I18N::translate('great ×%s aunt/uncle', I18N::number($up + 1));

                        case 'hi': // Source: MrQD
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s uncle', I18N::number($up - 2));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s aunt', I18N::number($up - 2));
                            }

                            return I18N::translate('great ×%s aunt/uncle', I18N::number($up - 2));

                        case 'zh-Hans': // Source: xmlf
                        case 'zh-Hant':
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s uncle', I18N::number($up));
                            }
                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s aunt', I18N::number($up));
                            }

                            return I18N::translate('great ×%s aunt/uncle', I18N::number($up));

                        case 'it': // Source: Michele Locati
                        case 'en_AU':
                        case 'en_GB':
                        case 'en_US':
                        default:
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s uncle', I18N::number($up - 1));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s aunt', I18N::number($up - 1));
                            }

                            return I18N::translate('great ×%s aunt/uncle', I18N::number($up - 1));
                    }
            }
        }
        if (preg_match('/^(?:bro|sis|sib)((?:son|dau|chi)+)$/', $path, $match) === 1) {
            // direct descendants of siblings
            $down  = intdiv(strlen($match[1]), 3) + 1; // Add one, as we count generations from the common ancestor
            $first = substr($path, 0, 3);
            switch ($down) {
                case 4:
                    if ($sex2 === 'M') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great-grandson', 'great-great-nephew');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great-grandson', 'great-great-nephew');
                        }

                        return I18N::translateContext('(a woman’s) great-great-nephew', 'great-great-nephew');
                    }

                    if ($sex2 === 'F') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great-granddaughter', 'great-great-niece');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great-granddaughter', 'great-great-niece');
                        }

                        return I18N::translateContext('(a woman’s) great-great-niece', 'great-great-niece');
                    }

                    if ($first === 'bro' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) brother’s great-grandchild', 'great-great-nephew/niece');
                    }

                    if ($first === 'sis' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) sister’s great-grandchild', 'great-great-nephew/niece');
                    }

                    return I18N::translateContext('(a woman’s) great-great-nephew/niece', 'great-great-nephew/niece');

                case 5:
                    if ($sex2 === 'M') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great-great-grandson', 'great-great-great-nephew');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great-great-grandson', 'great-great-great-nephew');
                        }

                        return I18N::translateContext('(a woman’s) great-great-great-nephew', 'great-great-great-nephew');
                    }

                    if ($sex2 === 'F') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great-great-granddaughter', 'great-great-great-niece');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great-great-granddaughter', 'great-great-great-niece');
                        }

                        return I18N::translateContext('(a woman’s) great-great-great-niece', 'great-great-great-niece');
                    }

                    if ($first === 'bro' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) brother’s great-great-grandchild', 'great-great-great-nephew/niece');
                    }

                    if ($first === 'sis' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) sister’s great-great-grandchild', 'great-great-great-nephew/niece');
                    }

                    return I18N::translateContext('(a woman’s) great-great-great-nephew/niece', 'great-great-great-nephew/niece');

                case 6:
                    if ($sex2 === 'M') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great-great-great-grandson', 'great ×4 nephew');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great-great-great-grandson', 'great ×4 nephew');
                        }

                        return I18N::translateContext('(a woman’s) great ×4 nephew', 'great ×4 nephew');
                    }

                    if ($sex2 === 'F') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great-great-great-granddaughter', 'great ×4 niece');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great-great-great-granddaughter', 'great ×4 niece');
                        }

                        return I18N::translateContext('(a woman’s) great ×4 niece', 'great ×4 niece');
                    }

                    if ($first === 'bro' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) brother’s great-great-great-grandchild', 'great ×4 nephew/niece');
                    }

                    if ($first === 'sis' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) sister’s great-great-great-grandchild', 'great ×4 nephew/niece');
                    }

                    return I18N::translateContext('(a woman’s) great ×4 nephew/niece', 'great ×4 nephew/niece');

                case 7:
                    if ($sex2 === 'M') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great ×4 grandson', 'great ×5 nephew');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great ×4 grandson', 'great ×5 nephew');
                        }

                        return I18N::translateContext('(a woman’s) great ×5 nephew', 'great ×5 nephew');
                    }

                    if ($sex2 === 'F') {
                        if ($first === 'bro' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) brother’s great ×4 granddaughter', 'great ×5 niece');
                        }

                        if ($first === 'sis' && $sex1 === 'M') {
                            return I18N::translateContext('(a man’s) sister’s great ×4 granddaughter', 'great ×5 niece');
                        }

                        return I18N::translateContext('(a woman’s) great ×5 niece', 'great ×5 niece');
                    }

                    if ($first === 'bro' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) brother’s great ×4 grandchild', 'great ×5 nephew/niece');
                    }

                    if ($first === 'sis' && $sex1 === 'M') {
                        return I18N::translateContext('(a man’s) sister’s great ×4 grandchild', 'great ×5 nephew/niece');
                    }

                    return I18N::translateContext('(a woman’s) great ×5 nephew/niece', 'great ×5 nephew/niece');

                default:
                    // Different languages have different rules for naming generations.
                    // An English great ×12 nephew is a Polish great ×11 nephew.
                    //
                    // Need to find out which languages use which rules.
                    switch (I18N::languageTag()) {
                        case 'pl': // Source: Lukasz Wilenski
                            if ($sex2 === 'M') {
                                if ($first === 'bro' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) brother’s great ×(%s-1) grandson', 'great ×%s nephew', I18N::number($down - 3));
                                }

                                if ($first === 'sis' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) sister’s great ×(%s-1) grandson', 'great ×%s nephew', I18N::number($down - 3));
                                }

                                return I18N::translateContext('(a woman’s) great ×%s nephew', 'great ×%s nephew', I18N::number($down - 3));
                            }

                            if ($sex2 === 'F') {
                                if ($first === 'bro' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) brother’s great ×(%s-1) granddaughter', 'great ×%s niece', I18N::number($down - 3));
                                }

                                if ($first === 'sis' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) sister’s great ×(%s-1) granddaughter', 'great ×%s niece', I18N::number($down - 3));
                                }

                                return I18N::translateContext('(a woman’s) great ×%s niece', 'great ×%s niece', I18N::number($down - 3));
                            }

                            if ($first === 'bro' && $sex1 === 'M') {
                                return I18N::translateContext('(a man’s) brother’s great ×(%s-1) grandchild', 'great ×%s nephew/niece', I18N::number($down - 3));
                            }

                            if ($first === 'sis' && $sex1 === 'M') {
                                return I18N::translateContext('(a man’s) sister’s great ×(%s-1) grandchild', 'great ×%s nephew/niece', I18N::number($down - 3));
                            }

                            return I18N::translateContext('(a woman’s) great ×%s nephew/niece', 'great ×%s nephew/niece', I18N::number($down - 3));

                        case 'ko': // Source: Jeongwan Nam (jeongwann@gmail.com)
                            if ($sex2 === 'M') {
                                if ($first === 'bro' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) brother’s great ×(%s-1) grandson', 'great ×%s nephew', I18N::number($down - 0));
                                }

                                if ($first === 'sis' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) sister’s great ×(%s-1) grandson', 'great ×%s nephew', I18N::number($down - 0));
                                }

                                return I18N::translateContext('(a woman’s) great ×%s nephew', 'great ×%s nephew', I18N::number($down - 0));
                            }

                            if ($sex2 === 'F') {
                                if ($first === 'bro' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) brother’s great ×(%s-1) granddaughter', 'great ×%s niece', I18N::number($down - 3));
                                }

                                if ($first === 'sis' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) sister’s great ×(%s-1) granddaughter', 'great ×%s niece', I18N::number($down - 3));
                                }

                                return I18N::translateContext('(a woman’s) great ×%s niece', 'great ×%s niece', I18N::number($down - 3));
                            }

                            if ($first === 'bro' && $sex1 === 'M') {
                                return I18N::translateContext('(a man’s) brother’s great ×(%s-1) grandchild', 'great ×%s nephew/niece', I18N::number($down - 3));
                            }

                            if ($first === 'sis' && $sex1 === 'M') {
                                return I18N::translateContext('(a man’s) sister’s great ×(%s-1) grandchild', 'great ×%s nephew/niece', I18N::number($down - 3));
                            }

                            return I18N::translateContext('(a woman’s) great ×%s nephew/niece', 'great ×%s nephew/niece', I18N::number($down - 3));

                        case 'zh-Hans': // Source: xmlf
                        case 'zh-Hant':
                            if ($sex2 === 'M') {
                                if ($first === 'bro' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) brother’s great ×(%s-1) grandson', 'great ×%s nephew', I18N::number($down - 1));
                                }
                                if ($first === 'sis' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) sister’s great ×(%s-1) grandson', 'great ×%s nephew', I18N::number($down - 1));
                                }

                                return I18N::translateContext('(a woman’s) great ×%s nephew', 'great ×%s nephew', I18N::number($down - 1));
                            }
                            if ($sex2 === 'F') {
                                if ($first === 'bro' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) brother’s great ×(%s-1) granddaughter', 'great ×%s niece', I18N::number($down - 1));
                                }
                                if ($first === 'sis' && $sex1 === 'M') {
                                    return I18N::translateContext('(a man’s) sister’s great ×(%s-1) granddaughter', 'great ×%s niece', I18N::number($down - 1));
                                }

                                return I18N::translateContext('(a woman’s) great ×%s niece', 'great ×%s niece', I18N::number($down - 1));
                            }
                            if ($first === 'bro' && $sex1 === 'M') {
                                return I18N::translateContext('(a man’s) brother’s great ×(%s-1) grandchild', 'great ×%s nephew/niece', I18N::number($down - 1));
                            }
                            if ($first === 'sis' && $sex1 === 'M') {
                                return I18N::translateContext('(a man’s) sister’s great ×(%s-1) grandchild', 'great ×%s nephew/niece', I18N::number($down - 1));
                            }

                            return I18N::translateContext('(a woman’s) great ×%s nephew/niece', 'great ×%s nephew/niece', I18N::number($down - 1));

                        case 'he': // Source: Meliza Amity
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s nephew', I18N::number($down - 1));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s niece', I18N::number($down - 1));
                            }

                            return I18N::translate('great ×%s nephew/niece', I18N::number($down - 1));

                        case 'hi': // Source: MrQD.
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s nephew', I18N::number($down - 3));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s niece', I18N::number($down - 3));
                            }

                            return I18N::translate('great ×%s nephew/niece', I18N::number($down - 3));

                        case 'it': // Source: Michele Locati.
                        case 'en_AU':
                        case 'en_GB':
                        case 'en_US':
                        default:
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s nephew', I18N::number($down - 2));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s niece', I18N::number($down - 2));
                            }

                            return I18N::translate('great ×%s nephew/niece', I18N::number($down - 2));
                    }
            }
        }
        if (preg_match('/^((?:mot|fat|par)*)$/', $path, $match) === 1) {
            // direct ancestors
            $up = intdiv(strlen($match[1]), 3);
            switch ($up) {
                case 4:
                    if ($sex2 === 'M') {
                        return I18N::translate('great-great-grandfather');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great-great-grandmother');
                    }

                    return I18N::translate('great-great-grandparent');

                case 5:
                    if ($sex2 === 'M') {
                        return I18N::translate('great-great-great-grandfather');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great-great-great-grandmother');
                    }

                    return I18N::translate('great-great-great-grandparent');

                case 6:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×4 grandfather');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×4 grandmother');
                    }

                    return I18N::translate('great ×4 grandparent');

                case 7:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×5 grandfather');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×5 grandmother');
                    }

                    return I18N::translate('great ×5 grandparent');

                case 8:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×6 grandfather');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×6 grandmother');
                    }

                    return I18N::translate('great ×6 grandparent');

                case 9:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×7 grandfather');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×7 grandmother');
                    }

                    return I18N::translate('great ×7 grandparent');

                default:
                    // Different languages have different rules for naming generations.
                    // An English great ×12 grandfather is a Danish great ×11 grandfather.
                    //
                    // Need to find out which languages use which rules.
                    switch (I18N::languageTag()) {
                        case 'da': // Source: Patrick Sorensen
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s grandfather', I18N::number($up - 3));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s grandmother', I18N::number($up - 3));
                            }

                            return I18N::translate('great ×%s grandparent', I18N::number($up - 3));

                        case 'it': // Source: Michele Locati
                        case 'zh-Hans': // Source: xmlf
                        case 'zh-Hant':
                        case 'es': // Source: Wes Groleau
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s grandfather', I18N::number($up));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s grandmother', I18N::number($up));
                            }

                            return I18N::translate('great ×%s grandparent', I18N::number($up));

                        case 'fr': // Source: Jacqueline Tetreault
                        case 'fr_CA':
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s grandfather', I18N::number($up - 1));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s grandmother', I18N::number($up - 1));
                            }

                            return I18N::translate('great ×%s grandparent', I18N::number($up - 1));

                        case 'ko': // Source : Jeongwan Nam (jeongwann@gmail.com)
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s grandfather', I18N::number($up + 1));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s grandmother', I18N::number($up + 1));
                            }

                            return I18N::translate('great ×%s grandparent', I18N::number($up + 1));

                        case 'nn': // Source: Hogne Røed Nilsen (https://bugs.launchpad.net/webtrees/+bug/1168553)
                        case 'nb':
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s grandfather', I18N::number($up - 3));
                            }

                            if ($sex2 === 'F') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s grandmother', I18N::number($up - 3));
                            }

                            // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                            return I18N::translate('great ×%s grandparent', I18N::number($up - 3));
                        case 'en_AU':
                        case 'en_GB':
                        case 'en_US':
                        default:
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s grandfather', I18N::number($up - 2));
                            }

                            if ($sex2 === 'F') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s grandmother', I18N::number($up - 2));
                            }

                            // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                            return I18N::translate('great ×%s grandparent', I18N::number($up - 2));
                    }
            }
        }
        if (preg_match('/^((?:son|dau|chi)*)$/', $path, $match) === 1) {
            // direct descendants
            $up = intdiv(strlen($match[1]), 3);
            switch ($up) {
                case 4:
                    if ($sex2 === 'M') {
                        return I18N::translate('great-great-grandson');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great-great-granddaughter');
                    }

                    return I18N::translate('great-great-grandchild');

                case 5:
                    if ($sex2 === 'M') {
                        return I18N::translate('great-great-great-grandson');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great-great-great-granddaughter');
                    }

                    return I18N::translate('great-great-great-grandchild');

                case 6:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×4 grandson');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×4 granddaughter');
                    }

                    return I18N::translate('great ×4 grandchild');

                case 7:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×5 grandson');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×5 granddaughter');
                    }

                    return I18N::translate('great ×5 grandchild');

                case 8:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×6 grandson');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×6 granddaughter');
                    }

                    return I18N::translate('great ×6 grandchild');

                case 9:
                    if ($sex2 === 'M') {
                        return I18N::translate('great ×7 grandson');
                    }

                    if ($sex2 === 'F') {
                        return I18N::translate('great ×7 granddaughter');
                    }

                    return I18N::translate('great ×7 grandchild');

                default:
                    // Different languages have different rules for naming generations.
                    // An English great ×12 grandson is a Danish great ×11 grandson.
                    //
                    // Need to find out which languages use which rules.
                    switch (I18N::languageTag()) {
                        case 'nn': // Source: Hogne Røed Nilsen
                        case 'nb':
                        case 'da': // Source: Patrick Sorensen
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s grandson', I18N::number($up - 3));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s granddaughter', I18N::number($up - 3));
                            }

                            return I18N::translate('great ×%s grandchild', I18N::number($up - 3));

                        case 'ko': // Source : Jeongwan Nam (jeongwann@gmail.com)
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s grandson', I18N::number($up + 1));
                            }

                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s granddaughter', I18N::number($up + 1));
                            }

                            return I18N::translate('great ×%s grandchild', I18N::number($up + 1));

                        case 'zh-Hans': // Source: xmlf
                        case 'zh-Hant':
                            if ($sex2 === 'M') {
                                return I18N::translate('great ×%s grandson', I18N::number($up));
                            }
                            if ($sex2 === 'F') {
                                return I18N::translate('great ×%s granddaughter', I18N::number($up));
                            }

                            return I18N::translate('great ×%s grandchild', I18N::number($up));

                        case 'it':
                            // Source: Michele Locati
                        case 'es':
                            // Source: Wes Groleau (adding doesn’t change behavior, but needs to be better researched)
                        case 'en_AU':
                        case 'en_GB':
                        case 'en_US':
                        default:
                            if ($sex2 === 'M') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s grandson', I18N::number($up - 2));
                            }

                            if ($sex2 === 'F') {
                                // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                                return I18N::translate('great ×%s granddaughter', I18N::number($up - 2));
                            }

                            // I18N: if you need a different number for %s, contact the developers, as a code-change is required
                            return I18N::translate('great ×%s grandchild', I18N::number($up - 2));
                    }
            }
        }
        if (preg_match('/^((?:mot|fat|par)+)(?:bro|sis|sib)((?:son|dau|chi)+)$/', $path, $match) === 1) {
            // cousins in English
            $ascent  = $match[1];
            $descent = $match[2];
            $up      = intdiv(strlen($ascent), 3);
            $down    = intdiv(strlen($descent), 3);
            $cousin  = min($up, $down); // Moved out of switch (en/default case) so that
            $removed = abs($down - $up); // Spanish (and other languages) can use it, too.

            // Different languages have different rules for naming cousins. For example,
            // an English “second cousin once removed” is a Polish “cousin of 7th degree”.
            //
            // Need to find out which languages use which rules.
            switch (I18N::languageTag()) {
                case 'pl': // Source: Lukasz Wilenski
                    return self::legacyCousinName($up + $down + 2, $sex2);
                case 'it':
                    // Source: Michele Locati. See italian_cousins_names.zip
                    // https://webtrees.net/forums/8-translation/1200-great-xn-grandparent?limit=6&start=6
                    return self::legacyCousinName($up + $down - 3, $sex2);
                case 'es':
                    if ($down === $up) {
                        return self::legacyCousinName($cousin, $sex2);
                    }

                    if ($down < $up) {
                        return self::legacyCousinName2($cousin + 1, $sex2, $this->legacyNameAlgorithm('sib' . $descent));
                    }

                    if ($sex2 === 'M') {
                        return self::legacyCousinName2($cousin + 1, $sex2, $this->legacyNameAlgorithm('bro' . $descent));
                    }

                    if ($sex2 === 'F') {
                        return self::legacyCousinName2($cousin + 1, $sex2, $this->legacyNameAlgorithm('sis' . $descent));
                    }

                    return self::legacyCousinName2($cousin + 1, $sex2, $this->legacyNameAlgorithm('sib' . $descent));

                case 'en_AU': // See: https://en.wikipedia.org/wiki/File:CousinTree.svg
                case 'en_GB':
                case 'en_US':
                default:
                    switch ($removed) {
                        case 0:
                            return self::legacyCousinName($cousin, $sex2);
                        case 1:
                            if ($up > $down) {
                                /* I18N: %s=“fifth cousin”, etc. */
                                return I18N::translate('%s once removed ascending', self::legacyCousinName($cousin, $sex2));
                            }

                            /* I18N: %s=“fifth cousin”, etc. */

                            return I18N::translate('%s once removed descending', self::legacyCousinName($cousin, $sex2));
                        case 2:
                            if ($up > $down) {
                                /* I18N: %s=“fifth cousin”, etc. */
                                return I18N::translate('%s twice removed ascending', self::legacyCousinName($cousin, $sex2));
                            }

                            /* I18N: %s=“fifth cousin”, etc. */

                            return I18N::translate('%s twice removed descending', self::legacyCousinName($cousin, $sex2));
                        case 3:
                            if ($up > $down) {
                                /* I18N: %s=“fifth cousin”, etc. */
                                return I18N::translate('%s three times removed ascending', self::legacyCousinName($cousin, $sex2));
                            }

                            /* I18N: %s=“fifth cousin”, etc. */

                            return I18N::translate('%s three times removed descending', self::legacyCousinName($cousin, $sex2));
                        default:
                            if ($up > $down) {
                                /* I18N: %1$s=“fifth cousin”, etc., %2$s>=4 */
                                return I18N::translate('%1$s %2$s times removed ascending', self::legacyCousinName($cousin, $sex2), I18N::number($removed));
                            }

                            /* I18N: %1$s=“fifth cousin”, etc., %2$s>=4 */

                            return I18N::translate('%1$s %2$s times removed descending', self::legacyCousinName($cousin, $sex2), I18N::number($removed));
                    }
            }
        }

        // Split the relationship into sub-relationships, e.g., third-cousin’s great-uncle.
        // Try splitting at every point, and choose the path with the shorted translated name.
        // But before starting to recursively go through all combinations, do a cache look-up

        static $relationshipsCache;
        $relationshipsCache ??= [];
        if (array_key_exists($path, $relationshipsCache)) {
            return $relationshipsCache[$path];
        }

        $relationship = '';
        $path1        = substr($path, 0, 3);
        $path2        = substr($path, 3);
        while ($path2 !== '') {
            // I18N: A complex relationship, such as “third-cousin’s great-uncle”
            $tmp = I18N::translate(
                '%1$s’s %2$s',
                $this->legacyNameAlgorithm($path1),
                $this->legacyNameAlgorithm($path2)
            );
            if ($relationship === '' || strlen($tmp) < strlen($relationship)) {
                $relationship = $tmp;
            }
            $path1 .= substr($path2, 0, 3);
            $path2 = substr($path2, 3);
        }
        // and store the result in the cache
        $relationshipsCache[$path] = $relationship;

        return $relationship;
    }

    /**
     * Calculate the name of a cousin.
     *
     * @param int    $n
     * @param string $sex
     *
     * @return string
     *
     * @deprecated
     */
    private static function legacyCousinName(int $n, string $sex): string
    {
        if ($sex === 'M') {
            switch ($n) {
                case 1:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'first cousin');
                case 2:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'second cousin');
                case 3:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'third cousin');
                case 4:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'fourth cousin');
                case 5:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'fifth cousin');
                case 6:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'sixth cousin');
                case 7:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'seventh cousin');
                case 8:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'eighth cousin');
                case 9:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'ninth cousin');
                case 10:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'tenth cousin');
                case 11:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'eleventh cousin');
                case 12:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'twelfth cousin');
                case 13:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'thirteenth cousin');
                case 14:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'fourteenth cousin');
                case 15:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', 'fifteenth cousin');
                default:
                    /* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language. You only need to translate - you do not need to convert. For other languages, if your cousin rules are different from English, please contact the developers. */
                    return I18N::translateContext('MALE', '%s × cousin', I18N::number($n));
            }
        }

        if ($sex === 'F') {
            switch ($n) {
                case 1:
                    return I18N::translateContext('FEMALE', 'first cousin');
                case 2:
                    return I18N::translateContext('FEMALE', 'second cousin');
                case 3:
                    return I18N::translateContext('FEMALE', 'third cousin');
                case 4:
                    return I18N::translateContext('FEMALE', 'fourth cousin');
                case 5:
                    return I18N::translateContext('FEMALE', 'fifth cousin');
                case 6:
                    return I18N::translateContext('FEMALE', 'sixth cousin');
                case 7:
                    return I18N::translateContext('FEMALE', 'seventh cousin');
                case 8:
                    return I18N::translateContext('FEMALE', 'eighth cousin');
                case 9:
                    return I18N::translateContext('FEMALE', 'ninth cousin');
                case 10:
                    return I18N::translateContext('FEMALE', 'tenth cousin');
                case 11:
                    return I18N::translateContext('FEMALE', 'eleventh cousin');
                case 12:
                    return I18N::translateContext('FEMALE', 'twelfth cousin');
                case 13:
                    return I18N::translateContext('FEMALE', 'thirteenth cousin');
                case 14:
                    return I18N::translateContext('FEMALE', 'fourteenth cousin');
                case 15:
                    return I18N::translateContext('FEMALE', 'fifteenth cousin');
                default:
                    return I18N::translateContext('FEMALE', '%s × cousin', I18N::number($n));
            }
        }

        switch ($n) {
            case 1:
                return I18N::translate('first cousin');
            case 2:
                return I18N::translate('second cousin');
            case 3:
                return I18N::translate('third cousin');
            case 4:
                return I18N::translate('fourth cousin');
            case 5:
                return I18N::translate('fifth cousin');
            case 6:
                return I18N::translate('sixth cousin');
            case 7:
                return I18N::translate('seventh cousin');
            case 8:
                return I18N::translate('eighth cousin');
            case 9:
                return I18N::translate('ninth cousin');
            case 10:
                return I18N::translate('tenth cousin');
            case 11:
                return I18N::translate('eleventh cousin');
            case 12:
                return I18N::translate('twelfth cousin');
            case 13:
                return I18N::translate('thirteenth cousin');
            case 14:
                return I18N::translate('fourteenth cousin');
            case 15:
                return I18N::translate('fifteenth cousin');
            default:
                return I18N::translate('%s × cousin', I18N::number($n));
        }
    }

    /**
     * A variation on cousin_name(), for constructs such as “sixth great-nephew”
     * Currently used only by Spanish relationship names.
     *
     * @param int    $n
     * @param string $sex
     * @param string $relation
     *
     * @return string
     *
     * @deprecated
     */
    private static function legacyCousinName2(int $n, string $sex, string $relation): string
    {
        if ($sex === 'M') {
            switch ($n) {
                case 1:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('MALE', 'first %s', $relation);
                case 2:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('MALE', 'second %s', $relation);
                case 3:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('MALE', 'third %s', $relation);
                case 4:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('MALE', 'fourth %s', $relation);
                case 5:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('MALE', 'fifth %s', $relation);
                default:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('MALE', '%1$s × %2$s', I18N::number($n), $relation);
            }
        }

        if ($sex === 'F') {
            switch ($n) {
                case 1:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('FEMALE', 'first %s', $relation);
                case 2:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('FEMALE', 'second %s', $relation);
                case 3:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('FEMALE', 'third %s', $relation);
                case 4:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('FEMALE', 'fourth %s', $relation);
                case 5:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('FEMALE', 'fifth %s', $relation);
                default:
                    /* I18N: A Spanish relationship name, such as third great-nephew */
                    return I18N::translateContext('FEMALE', '%1$s × %2$s', I18N::number($n), $relation);
            }
        }

        switch ($n) {
            case 1:
                /* I18N: A Spanish relationship name, such as third great-nephew */
                return I18N::translate('first %s', $relation);
            case 2:
                /* I18N: A Spanish relationship name, such as third great-nephew */
                return I18N::translate('second %s', $relation);
            case 3:
                /* I18N: A Spanish relationship name, such as third great-nephew */
                return I18N::translate('third %s', $relation);
            case 4:
                /* I18N: A Spanish relationship name, such as third great-nephew */
                return I18N::translate('fourth %s', $relation);
            case 5:
                /* I18N: A Spanish relationship name, such as third great-nephew */
                return I18N::translate('fifth %s', $relation);
            default:
                /* I18N: A Spanish relationship name, such as third great-nephew */
                return I18N::translate('%1$s × %2$s', I18N::number($n), $relation);
        }
    }
}
