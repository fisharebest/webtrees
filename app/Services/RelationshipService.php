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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\LanguageInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Relationship;

use function array_merge;
use function array_reduce;
use function array_slice;
use function count;
use function preg_match;
use function sprintf;

/**
 * Names for relationships.
 */
class RelationshipService
{
    private const array COMPONENTS = [
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
     */
    public function getCloseRelationshipName(Individual $individual1, Individual $individual2): string
    {
        $path = $this->getCloseRelationship($individual1, $individual2);

        // No relationship found?
        if ($path === []) {
            return '';
        }

        return $this->nameFromPath($path, I18N::language());
    }

    /**
     * Get relationship between two individuals in the gedcom.  This function
     * takes account of pending changes, so we can display names of newly added
     * relations.
     *
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
     */
    public function nameFromPath(array $nodes, LanguageInterface $language): string
    {
        // The relationship matching algorithm could be used for this, but it is more efficient to check it here.
        if (count($nodes) === 1) {
            return $this->reflexivePronoun($nodes[0]);
        }

        // The relationship definitions for the language.
        $relationships = $language->relationships();

        // We don't strictly need this, as all the information is contained in the nodes.
        // But it gives us simpler code and better performance.
        $pattern = $this->components($nodes);

        // Match the relationship, using a longest-substring algorithm.
        $relationships = $this->matchRelationships($nodes, $pattern, $relationships);

        // Reduce the genitive-nominative chain to a single string.
        return array_reduce($relationships, static fn (array $carry, array $item): array => [sprintf($carry[1], $item[0]), sprintf($carry[1], $item[1])], ['', '%s'])[0];
    }

    /**
     * Generate a reflexive pronoun for an individual
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
     * @param array<Individual|Family> $nodes Alternating list of Individual and Family objects
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
     * @return array<array{string,string}>
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
}
