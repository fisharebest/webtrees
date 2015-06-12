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

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;

/**
 * Controller for the familybook chart
 */
class FamilyBookController extends ChartController {
	/** @var int Whether to show spouse details */
	public $show_spouse;

	/** @var int Number of descendancy generations to show */
	public $descent;

	/** @var int Number of ascendancy generations to show */
	public $generations;

	/** @var int Number of descendancy generations that exist */
	private $dgenerations;

	/** @var int Half height of personbox */
	public $bhalfheight;

	/**
	 * Create a family-book controller
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();

		// Extract the request parameters
		$this->show_spouse = Filter::getInteger('show_spouse', 0, 1);
		$this->descent     = Filter::getInteger('descent', 0, 9, 5);
		$this->generations = Filter::getInteger('generations', 2, $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS'), 2);

		$this->bhalfheight = $this->getBoxDimensions()->height / 2;
		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				I18N::translate('Family book of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(I18N::translate('Family book'));
		}
		//Checks how many generations of descendency is for the person for formatting purposes
		$this->dgenerations = $this->maxDescendencyGenerations($this->root->getXref(), 0);

		if ($this->dgenerations < 1) {
			$this->dgenerations = 1;
		}
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

		echo '<table><tr><td>';
		$numkids = 0;

		// Load children
		$children = array();
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
			if ($children) {
				// real people
				echo '<table>';
				foreach ($children as $i => $child) {
					echo '<tr><td>';
					$kids = $this->printDescendency($child, $generation + 1);
					$numkids += $kids;
					echo '</td>';
					// Print the lines
					if (count($children) > 1) {
						if ($i === 0) {
							// Adjust for the first column on left
							$h = round(((($this->getBoxDimensions()->height) * $kids) + 8) / 2); // Assumes border = 1 and padding = 3
							//  Adjust for other vertical columns
							if ($kids > 1) {
								$h = ($kids - 1) * 4 + $h;
							}
							echo '<td class="tdbot">',
							'<img class="tvertline" id="vline_', $child->getXref(), '" src="', Theme::theme()->parameter('image-vline'), '"  height="', $h - 1, '" alt=""></td>';
						} elseif ($i === count($children) - 1) {
							// Adjust for the first column on left
							$h = round(((($this->getBoxDimensions()->height) * $kids) + 8) / 2);
							// Adjust for other vertical columns
							if ($kids > 1) {
								$h = ($kids - 1) * 4 + $h;
							}
							echo '<td class="tdtop">',
							'<img class="bvertline" id="vline_', $child->getXref(), '" src="', Theme::theme()->parameter('image-vline'), '" height="', $h + 1, '" alt=""></td>';
						} else {
							echo '<td style="background: url(', Theme::theme()->parameter('image-vline'), ');">',
							'<img class="spacer" src="', Theme::theme()->parameter('image-spacer'), '" alt=""></td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			} else {
				// Hidden/empty boxes - to preserve the layout
				echo '<table><tr><td>';
				$numkids += $this->printDescendency(null, $generation + 1);
				echo '</td></tr></table>';
			}
			echo '</td>';
			echo '<td>';
		}

		if ($numkids === 0) {
			$numkids = 1;
		}
		echo '<table><tr><td>';
		if ($person) {
			FunctionsPrint::printPedigreePerson($person, $this->showFull());
			echo '</td><td>',
			'<img class="line2" src="', Theme::theme()->parameter('image-hline'), '" width="8" height="3" alt="">';
		} else {
			echo '<div style="width:', $this->getBoxDimensions()->width + 19, 'px; height:', $this->getBoxDimensions()->height + 8, 'px;"></div>',
			'</td><td>';
		}

		// Print the spouse
		if ($generation === 1) {
			if ($this->show_spouse) {
				foreach ($person->getSpouseFamilies() as $family) {
					$spouse = $family->getSpouse($person);
					echo '</td></tr><tr><td>';
					FunctionsPrint::printPedigreePerson($spouse, $this->showFull());
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
		$lh = ($this->bhalfheight + 4) * pow(2, ($genoffset - $count - 1));
		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		if (count($person->getChildFamilies()) == 0) {
			echo '<table>';
			$this->printEmptyBox($this->getBoxDimensions()->width, $this->getBoxDimensions()->height);

			//-- recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr>';
			$this->printEmptyBox($this->getBoxDimensions()->width, $this->getBoxDimensions()->height);

			//-- recursively get the mother’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr></table>';
		}

		// Empty box section done, now for regular pedigree
		foreach ($person->getChildFamilies() as $family) {
			echo '<table><tr><td class="tdbot">';
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
					$tblheight = $this->getBoxDimensions()->height + 8;
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
				$lh = (($famcount - 1) * ($this->getBoxDimensions()->height + 8) - ($linefactor));
				if ($genoffset > 5) {
					$lh = $savlh;
				}
			}
			echo '<img class="line3 pvline"  src="', Theme::theme()->parameter('image-vline'), '" height="', $lh - 1, '" alt=""></td>',
			'<td>',
			'<img class="line4" src="', Theme::theme()->parameter('image-hline'), '" height="3" alt=""></td>',
			'<td>';
			$lh = $savlh; // restore original line height
			//-- print the father box
			FunctionsPrint::printPedigreePerson($family->getHusband(), $this->showFull());
			echo '</td>';
			if ($family->getHusband()) {
				echo '<td>';
				//-- recursively get the father’s family
				$this->printPersonPedigree($family->getHusband(), $count + 1);
				echo '</td>';
			} else {
				echo '<td>';
				if ($genoffset > $count) {
					echo '<table>';
					for ($i = 1; $i < (pow(2, ($genoffset) - $count) / 2); $i++) {
						$this->printEmptyBox($this->getBoxDimensions()->width, $this->getBoxDimensions()->height);
						echo '</tr>';
					}
					echo '</table>';
				}
			}
			echo '</tr><tr>',
			'<td class="tdtop"><img class="pvline" src="', Theme::theme()->parameter('image-vline'), '" height="', $lh + 1, '"></td>',
			'<td><img class="line4" src="', Theme::theme()->parameter('image-hline'), '" height="3"></td>',
			'<td>';
			//-- print the mother box
			FunctionsPrint::printPedigreePerson($family->getWife(), $this->showFull());
			echo '</td>';
			if ($family->getWife()) {
				echo '<td>';
				//-- recursively print the mother’s family
				$this->printPersonPedigree($family->getWife(), $count + 1);
				echo '</td>';
			} else {
				echo '<td>';
				if ($count < $genoffset - 1) {
					echo '<table>';
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
		global $WT_TREE;

		if ($depth > $this->generations) {
			return $depth;
		}
		$person = Individual::getInstance($pid, $WT_TREE);
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
		echo $this->showFull() ? Theme::theme()->individualBoxEmpty() : Theme::theme()->individualBoxSmallEmpty();
	}

	/**
	 * Print a “Family Book” for an individual
	 *
	 * @param Individual $person
	 * @param int    $descent_steps
	 */
	public function printFamilyBook(Individual $person, $descent_steps) {
		if ($descent_steps == 0 || !$person->canShowName()) {
			return;
		}
		$families = $person->getSpouseFamilies();
		if ($families) {
			echo
			'<h3>',
			/* I18N: A title/heading. %s is an individual’s name */ I18N::translate('Family of %s', $person->getFullName()),
			'</h3>',
			'<table class="t0"><tr><td class="tdmid">';
			$this->dgenerations = $this->generations;
			$this->printDescendency($person, 1);
			echo '</td><td class="tdmid">';
			$this->printPersonPedigree($person, 1);
			echo '</td></tr></table><br><br><hr style="page-break-after:always;"><br><br>';
			foreach ($families as $family) {
				foreach ($family->getChildren() as $child) {
					$this->printFamilyBook($child, $descent_steps - 1);
				}
			}
		}
	}
}
