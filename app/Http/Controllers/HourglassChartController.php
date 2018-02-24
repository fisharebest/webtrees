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

use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * A chart of ancestors and descendants of an individual.
 */
class HourglassChartController extends AbstractChartController {
	// Defaults
	const DEFAULT_GENERATIONS         = 3;
	const DEFAULT_MAXIMUM_GENERATIONS = 9;

	// Limits
	const MINIMUM_GENERATIONS = 2;

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

		$this->checkModuleIsActive($tree, 'hourglass_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$maximum_generations = (int) $tree->getPreference('MAX_DESCENDANCY_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$generations  = (int) $request->get('generations', $default_generations);

		$generations = min($generations, $maximum_generations);
		$generations = max($generations, self::MINIMUM_GENERATIONS);

		$show_spouse = (bool) $request->get('show_spouse');

		$title = /* I18N: %s is an individual’s name */ I18N::translate('Hourglass chart of %s', $individual->getFullName());

		return $this->viewResponse('hourglass-page', [
			'generations'         => $generations,
			'individual'          => $individual,
			'maximum_generations' => $maximum_generations,
			'minimum_generations' => self::MINIMUM_GENERATIONS,
			'show_spouse'         => $show_spouse,
			'title'               => $title,
			'tree'                => $tree,
		]);
	}

	/**
	 * Generate the initial generations of the chart
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chart(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'hourglass_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$maximum_generations = (int) $tree->getPreference('MAX_DESCENDANCY_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$generations = (int) $request->get('generations', $default_generations);
		$generations = min($generations, $maximum_generations);
		$generations = max($generations, self::MINIMUM_GENERATIONS);

		$show_spouse = (bool) $request->get('show_spouse');

		//Checks how many generations of descendency is for the person for formatting purposes
		$dgenerations = $this->maxDescendencyGenerations($individual, 0, $generations);
		if ($dgenerations < 1) {
			$dgenerations = 1;
		}

		ob_start();
		$this->printDescendency($individual, 1, $dgenerations, $generations, $show_spouse, true);
		$descendants = ob_get_clean();

		ob_start();
		$this->printPersonPedigree($individual, 1, $generations + 1, $show_spouse);
		$ancestors = ob_get_clean();

		return new Response(view('hourglass-chart', [
			'descendants' => $descendants,
			'ancestors'   => $ancestors,
			'bhalfheight' => (int) (Theme::theme()->parameter('chart-box-y') / 2),
		]));
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chartAddAncestor(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'hourglass_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$show_spouse = (bool) $request->get('show_spouse');

		ob_start();
		$this->printPersonPedigree($individual, 0, 1, $show_spouse);
		$html = ob_get_clean();

		return new Response($html);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chartAddDescendant(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'hourglass_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$show_spouse = (bool) $request->get('show_spouse');

		ob_start();
		$this->printDescendency($individual, 1, 1, 1, $show_spouse, false);
		$html = ob_get_clean();

		return new Response($html);


	}

	/**
	 * Prints descendency of passed in person
	 *
	 * @param Individual $person       person to print descendency for
	 * @param int        $count        count of generations to print
	 * @param int        $dgenerations Number of genreations of ancestors
	 * @param int        $generations  Number of generations of descendants
	 * @param bool       $show_spouse
	 * @param bool       $show_menu
	 *
	 * @return int
	 */
	private function printDescendency($person, $count, int $dgenerations, int $generations, bool $show_spouse, bool $show_menu) {
		global $lastGenSecondFam;

		if ($count > $dgenerations) {
			return 0;
		}
		$pid         = $person->getXref();
		$tablealign  = 'right';
		$otablealign = 'left';
		if (I18N::direction() === 'rtl') {
			$tablealign  = 'left';
			$otablealign = 'right';
		}

		//-- put a space between families on the last generation
		if ($count == $dgenerations - 1) {
			if (isset($lastGenSecondFam)) {
				echo '<br>';
			}
			$lastGenSecondFam = true;
		}
		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id='table_$pid' class='hourglassChart' style='float:$tablealign'>";
		echo '<tr>';
		echo "<td style='text-align:$tablealign'>";
		$numkids  = 0;
		$families = $person->getSpouseFamilies();
		$famNum   = 0;
		$children = [];
		if ($count < $dgenerations) {
			// Put all of the children in a common array
			foreach ($families as $family) {
				$famNum++;
				foreach ($family->getChildren() as $child) {
					$children[] = $child;
				}
			}

			$ct = count($children);
			if ($ct > 0) {
				echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style='position: relative; top: auto; float: $tablealign;'>";
				for ($i = 0; $i < $ct; $i++) {
					$person2 = $children[$i];
					$chil    = $person2->getXref();
					echo '<tr>';
					echo '<td id="td_', $chil, '" class="', I18N::direction(), '" style="text-align:', $otablealign, '">';
					$kids = $this->printDescendency($person2, $count + 1, $dgenerations, $generations, $show_spouse, false);
					$numkids += $kids;
					echo '</td>';

					// Print the lines
					if ($ct > 1) {
						if ($i == 0) {
							// First child
							echo "<td style='vertical-align:bottom'><img alt='' class='line1 tvertline' id='vline_$chil' src='" . Theme::theme()->parameter('image-vline') . "' width='3'></td>";
						} elseif ($i == $ct - 1) {
							// Last child
							echo "<td style='vertical-align:top'><img alt='' class='bvertline' id='vline_$chil' src='" . Theme::theme()->parameter('image-vline') . "' width='3'></td>";
						} else {
							// Middle child
							echo '<td style="background: url(\'' . Theme::theme()->parameter('image-vline') . '\');"><img src=\'' . Theme::theme()->parameter('image-spacer') . '\' width="3"></td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</td>';
			echo '<td class="myCharts" width="', Theme::theme()->parameter('chart-box-x'), '">';
		}

		// Print the descendency expansion arrow
		if ($count == $dgenerations) {
			$numkids = 1;
			$tbwidth = Theme::theme()->parameter('chart-box-x') + 16;
			for ($j = $count; $j < $dgenerations; $j++) {
				echo "<div style='width: ", $tbwidth, "px;'><br></div></td><td style='width:", Theme::theme()->parameter('chart-box-x'), "px'>";
			}
			$kcount = 0;
			foreach ($families as $family) {
				$kcount += $family->getNumberOfChildren();
			}
			if ($kcount == 0) {
				echo "</td><td style='width:", Theme::theme()->parameter('chart-box-x'), "px'>";
			} else {
				echo FontAwesome::linkIcon('arrow-start', I18N::translate('Children'), [
					'href'         => '#',
					'data-route'   => 'hourglass-add-desc',
					'data-xref'    => $pid,
					'data-spouses' => $show_spouse,
					'data_tree'    => $person->getTree()->getName(),
				]);

				//-- move the arrow up to line up with the correct box
				if ($show_spouse) {
					echo str_repeat('<br><br><br>', count($families));
				}
				echo "</td><td style='width:", Theme::theme()->parameter('chart-box-x'), "px'>";
			}
		}

		echo '<table cellspacing="0" cellpadding="0" border="0" id="table2_' . $pid . '"><tr><td> ';
		FunctionsPrint::printPedigreePerson($person);
		echo '</td><td> <img class="lineh1" src="' . Theme::theme()->parameter('image-hline') . '" width="7" height="3">';

		//----- Print the spouse
		if ($show_spouse) {
			foreach ($families as $family) {
				echo "</td></tr><tr><td style='text-align:$otablealign'>";
				FunctionsPrint::printPedigreePerson($family->getSpouse($person));
				$numkids++;
				echo '</td><td> </td>';
			}
			//-- add offset divs to make things line up better
			if ($count == $dgenerations) {
				echo "<tr><td colspan '2'><div style='height:", (Theme::theme()->parameter('chart-box-y') / 4), 'px; width:', Theme::theme()->parameter('chart-box-x'), "px;'><br></div>";
			}
		}
		echo '</td></tr></table>';

		// For the root person, print a down arrow that allows changing the root of tree
		if ($show_menu && $count == 1) {
			// -- print left arrow for decendants so that we can move down the tree
			$famids = $person->getSpouseFamilies();
			//-- make sure there is more than 1 child in the family with parents
			$cfamids = $person->getChildFamilies();
			$num     = 0;
			foreach ($cfamids as $family) {
				$num += $family->getNumberOfChildren();
			}
			if ($num > 0) {
				echo '<div class="center" id="childarrow" style="position:absolute; width:', Theme::theme()->parameter('chart-box-x'), 'px;">';
				echo FontAwesome::linkIcon('arrow-down', I18N::translate('Family'), ['href' => '#', 'id' => 'spouse-child-links']);
				echo '<div id="childbox">';
				echo '<table cellspacing="0" cellpadding="0" border="0" class="person_box"><tr><td> ';

				foreach ($famids as $family) {
					echo "<span class='name1'>" . I18N::translate('Family') . '</span>';
					$spouse = $family->getSpouse($person);
					if ($spouse !== null) {
						echo '<a href="' . e(route('hourglass', ['xref' => $spouse->getXref(), 'generations' => $generations, 'show_spouses' => (int) $show_spouse, 'ged' => $spouse->getTree()->getName()])) . '" class="name1">' . $spouse->getFullName() . '</a>';
					}
					foreach ($family->getChildren() as $child) {
						echo '<a href="' . e(route('hourglass', ['xref' => $child->getXref(), 'generations' => $generations, 'show_spouses' => (int) $show_spouse, 'ged' => $child->getTree()->getName()])) . '" class="name1">' . $child->getFullName() . '</a>';
					}
				}

				//-- print the siblings
				foreach ($cfamids as $family) {
					if ($family->getHusband() || $family->getWife()) {
						echo "<span class='name1'>" . I18N::translate('Parents') . '</span>';
						$husb = $family->getHusband();
						if ($husb) {
							echo '<a href="' . e(route('hourglass', ['xref' => $husb->getXref(), 'generations' => $generations, 'show_spouses' => (int) $show_spouse, 'ged' => $husb->getTree()->getName()])) . '" class="name1">' . $husb->getFullName() . '</a>';
						}
						$wife = $family->getWife();
						if ($wife) {
							echo '<a href="' . e(route('hourglass', ['xref' => $wife->getXref(), 'generations' => $generations, 'show_spouses' => (int) $show_spouse, 'ged' => $wife->getTree()->getName()])) . '" class="name1">' . $wife->getFullName() . '</a>';
						}
					}

					// filter out root person from children array so only siblings remain
					$siblings = array_filter($family->getChildren(), function (Individual $item) use ($pid) {
						return $item->getXref() != $pid;
					});
					$num = count($siblings);
					if ($num) {
						echo "<span class='name1'>";
						echo $num > 1 ? I18N::translate('Siblings') : I18N::translate('Sibling');
						echo '</span>';
						foreach ($siblings as $child) {
							echo '<a href="' . e(route('hourglass', ['xref' => $child->getXref(), 'generations' => $generations, 'show_spouses' => (int) $show_spouse, 'ged' => $child->getTree()->getName()])) . '" class="name1">' . $child->getFullName() . '</a>';
						}
					}
				}
				echo '</td></tr></table>';
				echo '</div>';
				echo '</div>';
			}
		}
		echo '</td></tr></table>';

		return $numkids;
	}

	/**
	 * Prints pedigree of the person passed in. Which is the descendancy
	 *
	 * @param Individual $person ID of person to print the pedigree for
	 * @param int        $count  generation count, so it recursively calls itself
	 * @param int        $generations
	 * @param bool       $show_spouse
	 */
	private function printPersonPedigree(Individual $person, int $count, int $generations, bool $show_spouse) {
		if ($count >= $generations) {
			return;
		}

		$genoffset = $generations; // handle pedigree n generations lines

		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		//
		if (count($person->getChildFamilies()) == 0) {
			echo '<table><tr><td> ' . Theme::theme()->individualBoxEmpty() . '</td>';
			echo '<td> ';
			// Recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1, $generations, $show_spouse);
			echo '</td></tr>';
			echo '<tr><td> ' . Theme::theme()->individualBoxEmpty() . '</td>';
			echo '<td> ';
			// Recursively get the mother’s family
			$this->printPersonPedigree($person, $count + 1, $generations, $show_spouse);
			echo '</td><td> </tr></table>';
		}
		foreach ($person->getChildFamilies() as $family) {
			echo '<table cellspacing="0" cellpadding="0" border="0"  class="hourglassChart">';
			echo '<tr>';
			echo '<td style="vertical-align:bottom"><img class="line3 pvline" src="' . Theme::theme()->parameter('image-vline') . '" width="3"></td>';
			echo '<td> <img class="lineh2" src="' . Theme::theme()->parameter('image-hline') . '" width="7" height="3"></td>';
			echo '<td class="myCharts"> ';
			//-- print the father box
			FunctionsPrint::printPedigreePerson($family->getHusband());
			echo '</td>';
			if ($family->getHusband()) {
				$ARID = $family->getHusband()->getXref();
				echo '<td id="td_' . $ARID . '">';

				//-- print an Ajax arrow on the last generation of the adult male
				if ($count == $generations - 1 && $family->getHusband()->getChildFamilies()) {
					echo FontAwesome::linkIcon('arrow-end', I18N::translate('Parents'), [
						'href'         => '#',
						'data-route'   => 'hourglass-add-asc',
						'data-xref'    => $ARID,
						'data-spouses' => (int) $show_spouse,
						'data_tree'    => $family->getHusband()->getTree()->getName(),
					]);
				}
				//-- recursively get the father’s family
				$this->printPersonPedigree($family->getHusband(), $count + 1, $generations, $show_spouse);
				echo '</td>';
			} else {
				echo '<td> ';
				if ($count < $genoffset - 1) {
					echo '<table>';
					for ($i = $count; $i < (pow(2, ($genoffset - 1) - $count) / 2) + 2; $i++) {
						echo Theme::theme()->individualBoxEmpty();
						echo '</tr>';
						echo Theme::theme()->individualBoxEmpty();
						echo '</tr>';
					}
					echo '</table>';
				}
			}
			echo
			'</tr><tr>',
				"<td style='vertical-align:top'><img class='pvline' src='" . Theme::theme()->parameter('image-vline') . "' width='3' alt=''></td>",
				'<td> <img class="lineh3" src="' . Theme::theme()->parameter('image-hline') . '" width="7" height="3"></td>',
			'<td class="myCharts"> ';
			//-- print the mother box
			FunctionsPrint::printPedigreePerson($family->getWife());
			echo '</td>';
			if ($family->getWife()) {
				$ARID = $family->getWife()->getXref();
				echo '<td id="td_' . $ARID . '">';

				//-- print an ajax arrow on the last generation of the adult female
				if ($count == $generations - 1 && $family->getWife()->getChildFamilies()) {
					echo FontAwesome::linkIcon('arrow-end', I18N::translate('Parents'), [
						'href'         => '#',
						'data-route'   => 'hourglass-add-asc',
						'data-xref'    => $ARID,
						'data-spouses' => (int) $show_spouse,
						'data_tree'    => $family->getWife()->getTree()->getName(),
					]);
				}
				//-- recursively print the mother’s family
				$this->printPersonPedigree($family->getWife(), $count + 1, $generations, $show_spouse);
				echo '</td>';
			}
			echo '</tr></table>';
			break;
		}
	}

	/**
	 * Calculates number of generations a person has
	 *
	 * @param Individual $individual Start individual
	 * @param int        $depth      Pass in 0 and it calculates how far down descendency goes
	 *
	 * @return int Number of generations the descendency actually goes
	 */
	private function maxDescendencyGenerations(Individual $individual, int $depth, int $max) {
		if ($depth > $max) {
			return $depth;
		}
		$maxdc = $depth;
		foreach ($individual->getSpouseFamilies() as $family) {
			foreach ($family->getChildren() as $child) {
				$dc = $this->maxDescendencyGenerations($child, $depth + 1, $max);
				if ($dc >= $max) {
					return $dc;
				}
				if ($dc > $maxdc) {
					$maxdc = $dc;
				}
			}
		}

		$maxdc++;
		if ($maxdc == 1) {
			$maxdc++;
		}

		return $maxdc;
	}
}
