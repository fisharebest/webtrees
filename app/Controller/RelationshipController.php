<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
class RelationshipController extends PageController {
	/**
	 * Calculate the shortest paths - or all paths - between two individuals.
	 *
	 * @param Individual $individual1
	 * @param Individual $individual2
	 * @param bool       $all
	 *
	 * @return string[][]
	 */
	public function calculateRelationships(Individual $individual1, Individual $individual2, $all) {
		$rows = Database::prepare(
			"SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC', 'CHIL', 'HUSB', 'WIFE')"
		)->execute(array(
			'tree_id' => $individual1->getTree()->getTreeId(),
		))->fetchAll();

		$graph = array();
		foreach ($rows as $row) {
			$graph[$row->l_from][$row->l_to] = 1;
		}

		$xref1    = $individual1->getXref();
		$xref2    = $individual2->getXref();
		$dijkstra = new Dijkstra($graph);
		$paths    = $dijkstra->shortestPaths($xref1, $xref2);

		if ($all) {
			// Only process each exclusion list once;
			$excluded = array();

			$queue = array();
			foreach ($paths as $path) {
				// Insert the paths into the queue, with an exclusion list.
				$queue[] = array('path' => $path, 'exclude' => array());
				// While there are un-extended paths
				while (list(, $next) = each($queue)) {
					// For each family on the path
					for ($n = count($next['path']) - 2; $n >= 1; $n -= 2) {
						$exclude   = $next['exclude'];
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
	public function oldStyleRelationshipPath(array $path) {
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
}
