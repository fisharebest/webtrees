<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Algorithm\Dijkstra;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart showing the relationship(s) between two individuals.
 */
class RelationshipsChartController extends AbstractChartController {
	/**
	 * A form to request the chart parameters.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function page(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'relationships_chart');

		$xref1       = $request->get('xref1');
		$individual1 = Individual::getInstance($xref1, $tree);
		$xref2       = $request->get('xref2');
		$individual2 = Individual::getInstance($xref2, $tree);

		$recursion = (int) $request->get('recursion', '0');
		$ancestors = (bool) $request->get('ancestors', '0');

		$ancestors_only = (bool) $tree->getPreference('RELATIONSHIP_ANCESTORS', RelationshipsChartModule::DEFAULT_ANCESTORS);
		$max_recursion  = (int) $tree->getPreference('RELATIONSHIP_RECURSION', RelationshipsChartModule::DEFAULT_RECURSION);

		$recursion = min($recursion, $max_recursion);

		if ($individual1 && $individual2) {
			$title = I18N::translate(/* I18N: %s are individual’s names */
				'Relationships between %1$s and %2$s', $individual1->getFullName(), $individual2->getFullName());
		} else {
			$title = I18N::translate('Relationships');
		}

		return $this->viewResponse('relationships-page', [
			'ancestors'         => $ancestors,
			'ancestors_only'    => $ancestors_only,
			'ancestors_options' => $this->ancestorsOptions(),
			'individual1'       => $individual1,
			'individual2'       => $individual2,
			'max_recursion'     => $max_recursion,
			'recursion'         => $recursion,
			'recursion_options' => $this->recursionOptions($max_recursion),
			'title'             => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chart(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'relationships_chart');

		$xref1       = $request->get('xref1');
		$individual1 = Individual::getInstance($xref1, $tree);
		$xref2       = $request->get('xref2');
		$individual2 = Individual::getInstance($xref2, $tree);

		$this->checkIndividualAccess($individual1);
		$this->checkIndividualAccess($individual2);

		$recursion = (int) $request->get('recursion', '0');
		$ancestors = (bool) $request->get('ancestors', '0');

		$max_recursion  = (int) $tree->getPreference('RELATIONSHIP_RECURSION', RelationshipsChartModule::DEFAULT_RECURSION);

		$recursion = min($recursion, $max_recursion);

		$paths = $this->calculateRelationships($individual1, $individual2, $recursion, (bool) $ancestors);

		// @TODO - convert to views
		ob_start();
		if ($individual1 && $individual2) {
			if (I18N::direction() === 'ltr') {
				$diagonal1 = Theme::theme()->parameter('image-dline');
				$diagonal2 = Theme::theme()->parameter('image-dline2');
			} else {
				$diagonal1 = Theme::theme()->parameter('image-dline2');
				$diagonal2 = Theme::theme()->parameter('image-dline');
			}

			$num_paths = 0;
			foreach ($paths as $path) {
				// Extract the relationship names between pairs of individuals
				$relationships = $this->oldStyleRelationshipPath($path);
				if (empty($relationships)) {
					// Cannot see one of the families/individuals, due to privacy;
					continue;
				}
				echo '<h3>', I18N::translate('Relationship: %s', Functions::getRelationshipNameFromPath(implode('', $relationships), $individual1, $individual2)), '</h3>';
				$num_paths++;

				// Use a table/grid for layout.
				$table = [];
				// Current position in the grid.
				$x = 0;
				$y = 0;
				// Extent of the grid.
				$min_y = 0;
				$max_y = 0;
				$max_x = 0;
				// For each node in the path.
				foreach ($path as $n => $xref) {
					if ($n % 2 === 1) {
						switch ($relationships[$n]) {
							case 'hus':
							case 'wif':
							case 'spo':
							case 'bro':
							case 'sis':
							case 'sib':
								$table[$x + 1][$y] = '<div style="background:url(' . Theme::theme()->parameter('image-hline') . ') repeat-x center;  width: 94px; text-align: center"><div class="hline-text" style="height: 32px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $tree), Individual::getInstance($path[$n + 1], $tree)) . '</div><div style="height: 32px;">' . FontAwesome::decorativeIcon('arrow-end') . '</div></div>';
								$x                 += 2;
								break;
							case 'son':
							case 'dau':
							case 'chi':
								if ($n > 2 && preg_match('/fat|mot|par/', $relationships[$n - 2])) {
									$table[$x + 1][$y - 1] = '<div style="background:url(' . $diagonal2 . '); width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: end;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $tree), Individual::getInstance($path[$n + 1], $tree)) . '</div><div style="height: 32px; text-align: start;">' . FontAwesome::decorativeIcon('arrow-down') . '</div></div>';
									$x                     += 2;
								} else {
									$table[$x][$y - 1] = '<div style="background:url(' . Theme::theme()
											->parameter('image-vline') . ') repeat-y center; height: 64px; text-align: center;"><div class="vline-text" style="display: inline-block; width:50%; line-height: 64px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $tree), Individual::getInstance($path[$n + 1], $tree)) . '</div><div style="display: inline-block; width:50%; line-height: 64px;">' . FontAwesome::decorativeIcon('arrow-down') . '</div></div>';
								}
								$y -= 2;
								break;
							case 'fat':
							case 'mot':
							case 'par':
								if ($n > 2 && preg_match('/son|dau|chi/', $relationships[$n - 2])) {
									$table[$x + 1][$y + 1] = '<div style="background:url(' . $diagonal1 . '); background-position: top right; width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: start;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $tree), Individual::getInstance($path[$n + 1], $tree)) . '</div><div style="height: 32px; text-align: end;">' . FontAwesome::decorativeIcon('arrow-down') . '</div></div>';
									$x                     += 2;
								} else {
									$table[$x][$y + 1] = '<div style="background:url(' . Theme::theme()
											->parameter('image-vline') . ') repeat-y center; height: 64px; text-align:center; "><div class="vline-text" style="display: inline-block; width: 50%; line-height: 32px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $tree), Individual::getInstance($path[$n + 1], $tree)) . '</div><div style="display: inline-block; width: 50%; line-height: 32px">' . FontAwesome::decorativeIcon('arrow-up') . '</div></div>';
								}
								$y += 2;
								break;
						}
						$max_x = max($max_x, $x);
						$min_y = min($min_y, $y);
						$max_y = max($max_y, $y);
					} else {
						$individual = Individual::getInstance($xref, $tree);
						ob_start();
						FunctionsPrint::printPedigreePerson($individual);
						$table[$x][$y] = ob_get_clean();
					}
				}
				echo '<div class="wt-chart wt-relationship-chart">';
				echo '<table style="border-collapse: collapse; margin: 20px 50px;">';
				for ($y = $max_y; $y >= $min_y; --$y) {
					echo '<tr>';
					for ($x = 0; $x <= $max_x; ++$x) {
						echo '<td style="padding: 0;">';
						if (isset($table[$x][$y])) {
							echo $table[$x][$y];
						}
						echo '</td>';
					}
					echo '</tr>';
				}
				echo '</table>';
				echo '</div>';
			}

			if (!$num_paths) {
				echo '<p>', I18N::translate('No link between the two individuals could be found.'), '</p>';
			}
		}

		$html = ob_get_clean();

		return new Response($html);
	}

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
	private function calculateRelationships(Individual $individual1, Individual $individual2, $recursion, $ancestor = false) {
		$rows = Database::prepare(
			"SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC')"
		)->execute([
			'tree_id' => $individual1->getTree()->getTreeId(),
		])->fetchAll();

		// Optionally restrict the graph to the ancestors of the individuals.
		if ($ancestor) {
			$ancestors = $this->allAncestors($individual1->getXref(), $individual2->getXref(), $individual1->getTree()->getTreeId());
			$exclude   = $this->excludeFamilies($individual1->getXref(), $individual2->getXref(), $individual1->getTree()->getTreeId());
		} else {
			$ancestors = [];
			$exclude   = [];
		}

		$graph = [];

		foreach ($rows as $row) {
			if (empty($ancestors) || in_array($row->l_from, $ancestors) && !in_array($row->l_to, $exclude)) {
				$graph[$row->l_from][$row->l_to] = 1;
				$graph[$row->l_to][$row->l_from] = 1;
			}
		}

		$xref1    = $individual1->getXref();
		$xref2    = $individual2->getXref();
		$dijkstra = new Dijkstra($graph);
		$paths    = $dijkstra->shortestPaths($xref1, $xref2);

		// Only process each exclusion list once;
		$excluded = [];

		$queue = [];
		foreach ($paths as $path) {
			// Insert the paths into the queue, with an exclusion list.
			$queue[] = ['path' => $path, 'exclude' => []];
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
						$queue[] = ['path' => $new_path, 'exclude' => $exclude];
					}
				}
			}
		}
		// Extract the paths from the queue, removing duplicates.
		$paths = [];
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
	private function oldStyleRelationshipPath(array $path) {
		$spouse_codes  = ['M' => 'hus', 'F' => 'wif', 'U' => 'spo'];
		$parent_codes  = ['M' => 'fat', 'F' => 'mot', 'U' => 'par'];
		$child_codes   = ['M' => 'son', 'F' => 'dau', 'U' => 'chi'];
		$sibling_codes = ['M' => 'bro', 'F' => 'sis', 'U' => 'sib'];
		$relationships = [];

		for ($i = 1, $count = count($path); $i < $count; $i += 2) {
			$family = Family::getInstance($path[$i], $this->tree());
			$prev   = Individual::getInstance($path[$i - 1], $this->tree());
			$next   = Individual::getInstance($path[$i + 1], $this->tree());
			if (preg_match('/\n\d (HUSB|WIFE|CHIL) @' . $prev->getXref() . '@/', $family->getGedcom(), $match)) {
				$rel1 = $match[1];
			} else {
				return [];
			}
			if (preg_match('/\n\d (HUSB|WIFE|CHIL) @' . $next->getXref() . '@/', $family->getGedcom(), $match)) {
				$rel2 = $match[1];
			} else {
				return [];
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
	 * @return string[]
	 */
	private function allAncestors($xref1, $xref2, $tree_id) {
		$ancestors = [$xref1, $xref2];

		$queue = [$xref1, $xref2];
		while (!empty($queue)) {
			$placeholders = implode(',', array_fill(0, count($queue), '?'));
			$parameters   = $queue;
			$parameters[] = $tree_id;

			$parents = Database::prepare(
				"SELECT l2.l_from" .
				" FROM `##link` AS l1" .
				" JOIN `##link` AS l2 USING (l_to, l_file) " .
				" WHERE l1.l_type = 'FAMC' AND l2.l_type = 'FAMS' AND l1.l_from IN (" . $placeholders . ") AND l_file = ?"
			)->execute(
				$parameters
			)->fetchOneColumn();

			$queue = [];
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
	 * @return string[]
	 */
	private function excludeFamilies($xref1, $xref2, $tree_id) {
		return Database::prepare(
			"SELECT l_to" .
			" FROM `##link` AS l1" .
			" JOIN `##link` AS l2 USING (l_type, l_to, l_file) " .
			" WHERE l_type = 'FAMS' AND l1.l_from = :spouse1 AND l2.l_from = :spouse2 AND l_file = :tree_id"
		)->execute([
			'spouse1' => $xref1,
			'spouse2' => $xref2,
			'tree_id' => $tree_id,
		])->fetchOneColumn();
	}

	/**
	 * Possible options for the ancestors option
	 *
	 * @return string[]
	 */
	private function ancestorsOptions(): array {
		return [
			0 => I18N::translate('Find any relationship'),
			1 => I18N::translate('Find relationships via ancestors'),
		];
	}

	/**
	 * Possible options for the recursion option
	 *
	 * @param int $max_recursion
	 *
	 * @return array
	 */
	private function recursionOptions(int $max_recursion): array {
		if ($max_recursion === RelationshipsChartModule::UNLIMITED_RECURSION) {
			$text = I18N::translate('Find all possible relationships');
		} else {
			$text = I18N::translate('Find other relationships');
		}

		return [
			'0'            => I18N::translate('Find the closest relationships'),
			$max_recursion => $text,
		];
	}
}
