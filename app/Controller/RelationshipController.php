<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Algorithm\Dijkstra;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;

/**
 * Controller for the relationships calculations
 */
class RelationshipController extends PageController
{
    /**
     * Calculate the shortest paths - or all paths - between two individuals.
     *
     * @param Individual $individual1
     * @param Individual $individual2
     * @param int        $recursion   How many levels of recursion to use
     * @param bool       $ancestor    Restrict to relationships via a common ancestor
     *
     * @return string[][]
     */
    public function calculateRelationships(Individual $individual1, Individual $individual2, $recursion, $ancestor = false)
    {
        $rows = Database::prepare(
            "SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC')"
        )->execute(array(
            'tree_id' => $individual1->getTree()->getTreeId(),
        ))->fetchAll();

        // Optionally restrict the graph to the ancestors of the individuals.
        if ($ancestor) {
            $ancestors = $this->allAncestors($individual1->getXref(), $individual2->getXref(), $individual1->getTree()->getTreeId());
            $exclude   = $this->excludeFamilies($individual1->getXref(), $individual2->getXref(), $individual1->getTree()->getTreeId());
        } else {
            $ancestors = array();
            $exclude   = array();
        }

        $graph = array();

        foreach ($rows as $row) {
            if (!$ancestors || in_array($row->l_from, $ancestors) && !in_array($row->l_to, $exclude)) {
                $graph[$row->l_from][$row->l_to] = 1;
                $graph[$row->l_to][$row->l_from] = 1;
            }
        }

        $xref1    = $individual1->getXref();
        $xref2    = $individual2->getXref();
        $dijkstra = new Dijkstra($graph);
        $paths    = $dijkstra->shortestPaths($xref1, $xref2);

        // Only process each exclusion list once;
        $excluded = array();

        $queue = array();
        foreach ($paths as $path) {
            // Insert the paths into the queue, with an exclusion list.
            $queue[] = array('path' => $path, 'exclude' => array());
            // While there are un-extended paths
            for ($next = current($queue); $next !== false; $next = next($queue)) {
                // For each family on the path
                for ($n = count($next['path']) - 2; $n >= 1; $n -= 2) {
                    $exclude = $next['exclude'];
                    if (count($exclude) >= $recursion) {
                        continue;
                    }
                    $exclude[] = $next['path'][$n];
                    sort($exclude);
                    $tmp = implode('-', $exclude);
                    if (in_array($tmp, $excluded)) {
                        continue;
                    } else {
                        $excluded[] = $tmp;
                    }
                    // Add any new path to the queue
                    foreach ($dijkstra->shortestPaths($xref1, $xref2, $exclude) as $new_path) {
                        $queue[] = array('path' => $new_path, 'exclude' => $exclude);
                    }
                }
            }
        }
        // Extract the paths from the queue, removing duplicates.
        $paths = array();
        foreach ($queue as $next) {
            $paths[implode('-', $next['path'])] = $next['path'];
        }

        return $paths;
    }

    /**
     * Convert a path (list of XREFs) to an "old-style" string of relationships.
     *
     * Return an empty array, if privacy rules prevent us viewing any node.
     *
     * @param GedcomRecord[] $path Alternately Individual / Family
     *
     * @return string[]
     */
    public function oldStyleRelationshipPath(array $path)
    {
        global $WT_TREE;

        $spouse_codes  = array('M' => 'hus', 'F' => 'wif', 'U' => 'spo');
        $parent_codes  = array('M' => 'fat', 'F' => 'mot', 'U' => 'par');
        $child_codes   = array('M' => 'son', 'F' => 'dau', 'U' => 'chi');
        $sibling_codes = array('M' => 'bro', 'F' => 'sis', 'U' => 'sib');
        $relationships = array();

        for ($i = 1; $i < count($path); $i += 2) {
            $family = Family::getInstance($path[$i], $WT_TREE);
            $prev   = Individual::getInstance($path[$i - 1], $WT_TREE);
            $next   = Individual::getInstance($path[$i + 1], $WT_TREE);
            if (preg_match('/\n\d (HUSB|WIFE|CHIL) @' . $prev->getXref() . '@/', $family->getGedcom(), $match)) {
                $rel1 = $match[1];
            } else {
                return array();
            }
            if (preg_match('/\n\d (HUSB|WIFE|CHIL) @' . $next->getXref() . '@/', $family->getGedcom(), $match)) {
                $rel2 = $match[1];
            } else {
                return array();
            }
            if (($rel1 === 'HUSB' || $rel1 === 'WIFE') && ($rel2 === 'HUSB' || $rel2 === 'WIFE')) {
                $relationships[$i] = $spouse_codes[$next->getSex()];
            } elseif (($rel1 === 'HUSB' || $rel1 === 'WIFE') && $rel2 === 'CHIL') {
                $relationships[$i] = $child_codes[$next->getSex()];
            } elseif ($rel1 === 'CHIL' && ($rel2 === 'HUSB' || $rel2 === 'WIFE')) {
                $relationships[$i] = $parent_codes[$next->getSex()];
            } elseif ($rel1 === 'CHIL' && $rel2 === 'CHIL') {
                $relationships[$i] = $sibling_codes[$next->getSex()];
            }
        }

        return $relationships;
    }

    /**
     * Find all ancestors of a list of individuals
     *
     * @param string $xref1
     * @param string $xref2
     * @param int    $tree_id
     *
     * @return array
     */
    private function allAncestors($xref1, $xref2, $tree_id)
    {
        $ancestors = array($xref1, $xref2);

        $queue = array($xref1, $xref2);
        while (!empty($queue)) {
            $placeholders = implode(',', array_fill(0, count($queue), '?'));
            $parameters = $queue;
            $parameters[] = $tree_id;

            $parents = Database::prepare(
                "SELECT l2.l_from" .
                " FROM `##link` AS l1" .
                " JOIN `##link` AS l2 USING (l_to, l_file) " .
                " WHERE l1.l_type = 'FAMC' AND l2.l_type = 'FAMS' AND l1.l_from IN (" . $placeholders . ") AND l_file = ?"
            )->execute(
                $parameters
            )->fetchOneColumn();

            $queue = array();
            foreach ($parents as $parent) {
                if (!in_array($parent, $ancestors)) {
                    $ancestors[] = $parent;
                    $queue[]     = $parent;
                }
            }
        }

        return $ancestors;
    }

    /**
     * Find all families of two individuals
     *
     * @param string $xref1
     * @param string $xref2
     * @param int    $tree_id
     *
     * @return array
     */
    private function excludeFamilies($xref1, $xref2, $tree_id)
    {
        return Database::prepare(
            "SELECT l_to" .
            " FROM `##link` AS l1" .
            " JOIN `##link` AS l2 USING (l_type, l_to, l_file) " .
            " WHERE l_type = 'FAMS' AND l1.l_from = :spouse1 AND l2.l_from = :spouse2 AND l_file = :tree_id"
        )->execute(array(
            'spouse1' => $xref1,
            'spouse2' => $xref2,
            'tree_id' => $tree_id,
        ))->fetchOneColumn();
    }
}
