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

use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A series of "mini" hourglass charts for the descendants of an individual.
 */
class FamilyBookChartController extends AbstractChartController {
	// Defaults
	const DEFAULT_GENERATIONS            = 2;
	const DEFAULT_DESCENDANT_GENERATIONS = 5;
	const DEFAULT_MAXIMUM_GENERATIONS    = 9;

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

		$this->checkModuleIsActive($tree, 'family_book_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$minimum_generations = 2;
		$maximum_generations = (int) $tree->getPreference('MAX_DESCENDANCY_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$show_spouse = (bool) $request->get('show_spouse');
		$generations = (int) $request->get('generations', $default_generations);
		$generations = min($generations, $maximum_generations);
		$generations = max($generations, $minimum_generations);

		// Generations of ancestors/descendants in each mini-tree.
		$book_size = (int) $request->get('book_size', 2);
		$book_size = min($book_size, 5);
		$book_size = max($book_size, 2);

		$title
			= /* I18N: %s is an individual’s name */
			I18N::translate('Family book of %s', $individual->getFullName());

		return $this->viewResponse('family-book-page', [
			'book_size'           => $book_size,
			'generations'         => $generations,
			'individual'          => $individual,
			'maximum_generations' => $maximum_generations,
			'minimum_generations' => $minimum_generations,
			'show_spouse'         => $show_spouse,
			'title'               => $title,
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

		$this->checkModuleIsActive($tree, 'family_book_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$minimum_generations = 2;
		$maximum_generations = (int) $tree->getPreference('MAX_PEDIGREE_GENERATIONS', self::DEFAULT_MAXIMUM_GENERATIONS);
		$default_generations = (int) $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS', self::DEFAULT_GENERATIONS);

		$book_size   = (int) $request->get('book_size', 2);
		$show_spouse = (bool) $request->get('show_spouse');

		$generations = (int) $request->get('generations', $default_generations);
		$generations = min($generations, $maximum_generations);
		$generations = max($generations, $minimum_generations);
		$descendants = $this->descendants($individual, $generations, []);

		// @TODO - this is just a wrapper around the old code.
		ob_start();
		$this->box = (object) [
			'width'  => Theme::theme()->parameter('chart-box-x'),
			'height' => Theme::theme()->parameter('chart-box-y'),
		];

		$this->show_spouse = $show_spouse;
		$this->descent     = $generations;
		$this->generations = $book_size;

		$this->bhalfheight  = $this->box->height / 2;
		$this->dgenerations = $this->maxDescendencyGenerations($individual->getXref(), 0);

		if ($this->dgenerations < 1) {
			$this->dgenerations = 1;
		}

		$this->printFamilyBook($individual, $generations);

		$html = ob_get_clean();

		return new Response($html);
	}

	/**
	 * Prints descendency of passed in person
	 *
	 * @param Individual|null $person
	 * @param int             $generation
	 *
	 * @return int
	 */
	private function printDescendency(Individual $person = null, $generation) {
		if ($generation > $this->dgenerations) {
			return 0;
		}

		echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td>';
		$numkids = 0;

		// Load children
		$children = [];
		if ($person) {
			// Count is position from center to left, dgenerations is number of generations
			if ($generation < $this->dgenerations) {
				// All children, from all partners
				foreach ($person->getSpouseFamilies() as $family) {
					foreach ($family->getChildren() as $child) {
						$children[] = $child;
					}
				}
			}
		}
		if ($generation < $this->dgenerations) {
			if (!empty($children)) {
				// real people
				echo '<table cellspacing="0" cellpadding="0" border="0" >';
				foreach ($children as $i => $child) {
					echo '<tr><td>';
					$kids    = $this->printDescendency($child, $generation + 1);
					$numkids += $kids;
					echo '</td>';
					// Print the lines
					if (count($children) > 1) {
						if ($i === 0) {
							// Adjust for the first column on left
							$h = round(((($this->box->height) * $kids) + 8) / 2); // Assumes border = 1 and padding = 3
							//  Adjust for other vertical columns
							if ($kids > 1) {
								$h = ($kids - 1) * 4 + $h;
							}
							echo '<td class="align-bottom">',
							'<img id="vline_', $child->getXref(), '" src="', Theme::theme()->parameter('image-vline'), '" width="3" height="', $h - 4, '"></td>';
						} elseif ($i === count($children) - 1) {
							// Adjust for the first column on left
							$h = round(((($this->box->height) * $kids) + 8) / 2);
							// Adjust for other vertical columns
							if ($kids > 1) {
								$h = ($kids - 1) * 4 + $h;
							}
							echo '<td class="align-top">',
							'<img class="bvertline" width="3" id="vline_', $child->getXref(), '" src="', Theme::theme()->parameter('image-vline'), '" height="', $h - 2, '"></td>';
						} else {
							echo '<td class="align-bottomm"style="background: url(', Theme::theme()->parameter('image-vline'), ');">',
							'<img class="spacer"  width="3" src="', Theme::theme()->parameter('image-spacer'), '"></td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			} else {
				// Hidden/empty boxes - to preserve the layout
				echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td>';
				$numkids += $this->printDescendency(null, $generation + 1);
				echo '</td></tr></table>';
			}
			echo '</td>';
			echo '<td>';
		}

		if ($numkids === 0) {
			$numkids = 1;
		}
		echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td>';
		if ($person) {
			FunctionsPrint::printPedigreePerson($person);
			echo '</td><td>',
			'<img class="linef1" src="', Theme::theme()->parameter('image-hline'), '" width="8" height="3">';
		} else {
			echo '<div style="width:', $this->box->width + 19, 'px; height:', $this->box->height + 8, 'px;"></div>',
			'</td><td>';
		}

		// Print the spouse
		if ($generation === 1) {
			if ($this->show_spouse) {
				foreach ($person->getSpouseFamilies() as $family) {
					$spouse = $family->getSpouse($person);
					echo '</td></tr><tr><td>';
					FunctionsPrint::printPedigreePerson($spouse);
					$numkids += 0.95;
					echo '</td><td>';
				}
			}
		}
		echo '</td></tr></table>';
		echo '</td></tr>';
		echo '</table>';

		return $numkids;
	}

	/**
	 * Prints pedigree of the person passed in
	 *
	 * @param Individual $person
	 * @param int        $count
	 */
	private function printPersonPedigree($person, $count) {
		if ($count >= $this->generations) {
			return;
		}

		$genoffset = $this->generations; // handle pedigree n generations lines
		//-- calculate how tall the lines should be
		$lh = ($this->bhalfheight) * pow(2, ($genoffset - $count - 1));
		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		if (count($person->getChildFamilies()) == 0) {
			echo '<table cellspacing="0" cellpadding="0" border="0" >';
			$this->printEmptyBox();

			//-- recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr>';
			$this->printEmptyBox();

			//-- recursively get the mother’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr></table>';
		}

		// Empty box section done, now for regular pedigree
		foreach ($person->getChildFamilies() as $family) {
			echo '<table cellspacing="0" cellpadding="0" border="0" ><tr><td class="align-bottom">';
			// Determine line height for two or more spouces
			// And then adjust the vertical line for the root person only
			$famcount = 0;
			if ($this->show_spouse) {
				// count number of spouses
				$famcount += count($person->getSpouseFamilies());
			}
			$savlh = $lh; // Save current line height
			if ($count == 1 && $genoffset <= $famcount) {
				$linefactor = 0;
				// genoffset of 2 needs no adjustment
				if ($genoffset > 2) {
					$tblheight = $this->box->height + 8;
					if ($genoffset == 3) {
						if ($famcount == 3) {
							$linefactor = $tblheight / 2;
						} elseif ($famcount > 3) {
							$linefactor = $tblheight;
						}
					}
					if ($genoffset == 4) {
						if ($famcount == 4) {
							$linefactor = $tblheight;
						} elseif ($famcount > 4) {
							$linefactor = ($famcount - $genoffset) * ($tblheight * 1.5);
						}
					}
					if ($genoffset == 5) {
						if ($famcount == 5) {
							$linefactor = 0;
						} elseif ($famcount > 5) {
							$linefactor = $tblheight * ($famcount - $genoffset);
						}
					}
				}
				$lh = (($famcount - 1) * ($this->box->height) - ($linefactor));
				if ($genoffset > 5) {
					$lh = $savlh;
				}
			}
			echo '<img class="line3 pvline"  src="', Theme::theme()->parameter('image-vline'), '" width="3" height="', $lh, '"></td>',
			'<td>',
			'<img class="linef2" src="', Theme::theme()->parameter('image-hline'), '" height="3"></td>',
			'<td>';
			$lh = $savlh; // restore original line height
			//-- print the father box
			FunctionsPrint::printPedigreePerson($family->getHusband());
			echo '</td>';
			if ($family->getHusband()) {
				echo '<td>';
				//-- recursively get the father’s family
				$this->printPersonPedigree($family->getHusband(), $count + 1);
				echo '</td>';
			} else {
				echo '<td>';
				if ($genoffset > $count) {
					echo '<table cellspacing="0" cellpadding="0" border="0" >';
					for ($i = 1; $i < (pow(2, ($genoffset) - $count) / 2); $i++) {
						$this->printEmptyBox();
						echo '</tr>';
					}
					echo '</table>';
				}
			}
			echo '</tr><tr>',
			'<td class="align-top"><img class="pvline" src="', Theme::theme()->parameter('image-vline'), '" width="3" height="', $lh, '"></td>',
			'<td><img class="linef3" src="', Theme::theme()->parameter('image-hline'), '" height="3"></td>',
			'<td>';
			//-- print the mother box
			FunctionsPrint::printPedigreePerson($family->getWife());
			echo '</td>';
			if ($family->getWife()) {
				echo '<td>';
				//-- recursively print the mother’s family
				$this->printPersonPedigree($family->getWife(), $count + 1);
				echo '</td>';
			} else {
				echo '<td>';
				if ($count < $genoffset - 1) {
					echo '<table cellspacing="0" cellpadding="0" border="0" >';
					for ($i = 1; $i < (pow(2, ($genoffset - 1) - $count) / 2) + 1; $i++) {
						$this->printEmptyBox();
						echo '</tr>';
						$this->printEmptyBox();
						echo '</tr>';
					}
					echo '</table>';
				}
			}
			echo '</tr>',
			'</table>';
			break;
		}
	}

	/**
	 * Calculates number of generations a person has
	 *
	 * @param string $pid
	 * @param int    $depth
	 *
	 * @return int
	 */
	private function maxDescendencyGenerations($pid, $depth) {
		if ($depth > $this->generations) {
			return $depth;
		}
		$person = Individual::getInstance($pid, $this->tree());
		if (is_null($person)) {
			return $depth;
		}
		$maxdc = $depth;
		foreach ($person->getSpouseFamilies() as $family) {
			foreach ($family->getChildren() as $child) {
				$dc = $this->maxDescendencyGenerations($child->getXref(), $depth + 1);
				if ($dc >= $this->generations) {
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

	/**
	 * Print empty box
	 */

	private function printEmptyBox() {
		echo Theme::theme()->individualBoxEmpty();
	}

	/**
	 * Print a “Family Book” for an individual
	 *
	 * @param Individual $person
	 * @param int        $descent_steps
	 */
	private function printFamilyBook(Individual $person, $descent_steps) {
		if ($descent_steps == 0) {
			return;
		}

		$families = $person->getSpouseFamilies();
		if (1 || !empty($families)) {
			echo
			'<h3>',
				/* I18N: %s is an individual’s name */
			I18N::translate('Family of %s', $person->getFullName()),
			'</h3>',
			'<table cellspacing="0" cellpadding="0" border="0" ><tr><td class="align-middle">';
			$this->dgenerations = $this->generations;
			$this->printDescendency($person, 1);
			echo '</td><td class="align-middle">';
			$this->printPersonPedigree($person, 1);
			echo '</td></tr></table><br><br><hr class="family-break"><br><br>';
			foreach ($families as $family) {
				foreach ($family->getChildren() as $child) {
					$this->printFamilyBook($child, $descent_steps - 1);
				}
			}
		}
	}
}
