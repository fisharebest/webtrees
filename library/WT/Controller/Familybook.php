<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class WT_Controller_Familybook - Controller for the familybook chart
 */
class WT_Controller_Familybook extends WT_Controller_Chart {
	// Data for the view
	public $pid = null;
	public $show_full = null;
	public $show_spouse = null;
	public $descent = null;
	public $generations = null;
	public $box_width = null;
	public $rootid = null;

	// Data for the controller
	private $dgenerations = null;

	/**
	 * Create a family-book controller
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();

		$PEDIGREE_FULL_DETAILS = $WT_TREE->getPreference('PEDIGREE_FULL_DETAILS');
		$MAX_DESCENDANCY_GENERATIONS = $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS');

		// Extract the request parameters
		$this->show_full = WT_Filter::getInteger('show_full', 0, 1, $PEDIGREE_FULL_DETAILS);
		$this->show_spouse = WT_Filter::getInteger('show_spouse', 0, 1);
		$this->descent = WT_Filter::getInteger('descent', 0, 9, 5);
		$this->generations = WT_Filter::getInteger('generations', 2, $MAX_DESCENDANCY_GENERATIONS, 2);
		$this->box_width = WT_Filter::getInteger('box_width', 50, 300, 100);

		// Box sizes are set globally in the theme.  Modify them here.
		global $bwidth, $bheight, $cbwidth, $cbheight, $Dbwidth, $bhalfheight, $Dbheight;
		$Dbwidth = $this->box_width * $bwidth / 100;
		$bwidth = $Dbwidth;
		$bheight = $Dbheight;

		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $this->box_width * $cbwidth / 100;
			$bheight = $cbheight;
		}
		$bhalfheight = $bheight / 2;
		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				WT_I18N::translate('Family book of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(WT_I18N::translate('Family book'));
		}
		//Checks how many generations of descendency is for the person for formatting purposes
		$this->dgenerations = $this->maxDescendencyGenerations($this->pid, 0);
		if ($this->dgenerations < 1) {
			$this->dgenerations = 1;
		}
	}

	/**
	 * Prints descendency of passed in person
	 *
	 * @param WT_Individual|null $person
	 * @param integer            $count
	 *
	 * @return integer
	 */
	private function printDescendency(WT_Individual $person = null, $count) {
		global $WT_IMAGES, $bwidth, $bheight, $show_full, $box_width; // print_pedigree_person() requires these globals.

		if ($count > $this->dgenerations) {
			return 0;
		}
		$show_full = $this->show_full;
		$box_width = $this->box_width;
		echo '<table><tr><td width="', $bwidth, '">';
		$numkids = 0;
		$famNum = 0;

		// if real person load child array
		if ($person) {
			$sfamilies = $person->getSpouseFamilies();
			$children = array();
			//count is position from center to left, dgenerations is number of generations
			if ($count < $this->dgenerations) {
				//-- put all of the children in a common array
				foreach ($sfamilies as $family) {
					$famNum++;
					$chs = $family->getChildren();
					foreach ($chs as $child) {
						$children[] = $child;
					}
				}
				$ct = count($children);
			}
		} else {
			$ct = 0; // set to 0 for empty boxes
		}
		if ($count < $this->dgenerations) {
			if ($ct == 0) { // empty boxes
				echo '<table><tr><td>';
				$person2 = null;
				$kids = $this->printDescendency($person2, $count + 1);
				$numkids += $kids;
				echo '</td></tr></table>';
			}
			if ($ct > 0) { // real people
				echo '<table>';
				for ($i = 0; $i < $ct; $i++) {
					$person2 = $children[$i];
					$chil = $person2->getXref();
					echo '<tr><td>';
					$kids = $this->printDescendency($person2, $count + 1);
					$numkids += $kids;
					echo '</td>';
					//-- print the lines
					if ($ct > 1) {
						if ($i == 0) {
							//-- adjust for the first column on left
							$h = round(((($bheight) * $kids) + 8) / 2);  // Assumes border = 1 and padding = 3
							//-- adjust for other vertical columns
							if ($kids > 1) {
								$h = ($kids - 1) * 4 + $h;
							}
							echo '<td class="tdbot">',
							'<img class="tvertline" id="vline_', $chil, '" src="', $WT_IMAGES["vline"], '"  height="', $h - 1, '" alt=""></td>';
						} else if ($i == $ct - 1) {
							//-- adjust for the first column on left
							$h = round(((($bheight) * $kids) + 8) / 2);
							//-- adjust for other vertical columns
							if ($kids > 1) {
								$h = ((($kids - 1) * 4) + $h);
							}
							echo '<td class="tdtop">',
							'<img class="bvertline" id="vline_', $chil, '" src="', $WT_IMAGES["vline"], '" height="', $h + 1, '" alt=""></td>';
						} else {
							echo '<td style="background: url(', $WT_IMAGES["vline"], ');">',
							'<img class="spacer" src="', $WT_IMAGES["spacer"], '" alt=""></td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</td>';
			echo '<td width="', $bwidth, '">';
		}

		if ($numkids == 0) {
			$numkids = 1;
		}
		echo '<table><tr><td>';
		if ($person) {
			print_pedigree_person($person);
			echo '</td><td>',
			'<img class="line2" src="', $WT_IMAGES["hline"], '" width="8" height="3" alt="">';
		} else {
			echo '<div style="width:', $bwidth + 19, 'px; height:', $bheight + 8, 'px;"></div>',
			'</td><td>';
		}

		// Print the spouse
		if ($count == 1) {
			if ($this->show_spouse) {
				foreach ($sfamilies as $family) {
					$spouse = $family->getSpouse($person);
					echo '</td></tr><tr><td>';
					//-- shrink the box for the spouses
					$tempw = $bwidth;
					$temph = $bheight;
					$bwidth -= 5;
					print_pedigree_person($spouse);
					$bwidth = $tempw;
					$bheight = $temph;
					$numkids += 0.95;
					echo '</td><td>';
				}
			}
		}
		echo "</td></tr></table>";
		echo '</td></tr>';
		echo '</table>';

		return $numkids;
	}

	/**
	 * Prints pedigree of the person passed in
	 *
	 * @param WT_Individual $person
	 * @param integer       $count
	 */
	private function printPersonPedigree($person, $count) {
		global $WT_IMAGES, $bheight, $bwidth, $bhalfheight;
		if ($count >= $this->generations) {
			return;
		}

		$genoffset = $this->generations;  // handle pedigree n generations lines
		//-- calculate how tall the lines should be
		$lh = ($bhalfheight + 4) * pow(2, ($genoffset - $count - 1));
		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		if (count($person->getChildFamilies()) == 0) {
			echo '<table>';
			$this->printEmptyBox($bwidth, $bheight);

			//-- recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr>';
			$this->printEmptyBox($bwidth, $bheight);

			//-- recursively get the mother’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr></table>';
		}

		//Empty box section done, now for regular pedigree
		foreach ($person->getChildFamilies() as $family) {
			echo '<table><tr><td class="tdbot">';
			//
			//Determine line height for two or more spouces
			//And then adjust the vertical line for the root person only
			//
			$sfamilies = $person->getSpouseFamilies();
			$famcount = 0;
			if ($this->show_spouse) { // count number of spouses
				$famcount += count($sfamilies);
			}
			$savlh = $lh; // Save current line height
			if ($count == 1 && $genoffset <= $famcount) {
				$linefactor = 0;
				if ($genoffset > 2) { // genoffset of 2 needs no adjustment
					$tblheight = $bheight + 8;
					if ($genoffset == 3) {
						if ($famcount == 3) {
							$linefactor = $tblheight / 2;
						} else if ($famcount > 3) {
							$linefactor = $tblheight;
						}
					}
					if ($genoffset == 4) {
						if ($famcount == 4) {
							$linefactor = $tblheight;
						} else if ($famcount > 4) {
							$linefactor = ($famcount - $genoffset) * ($tblheight * 1.5);
						}
					}
					if ($genoffset == 5) {
						if ($famcount == 5) {
							$linefactor = 0;
						} else if ($famcount > 5) {
							$linefactor = $tblheight * ($famcount - $genoffset);
						}
					}
				}
				$lh = (($famcount - 1) * ($bheight + 8) - ($linefactor));
				if ($genoffset > 5) {
					$lh = $savlh;
				}
			}
			echo '<img class="line3 pvline"  src="', $WT_IMAGES["vline"], '" height="', $lh - 1, '" alt=""></td>',
			'<td>',
			'<img class="line4" src="', $WT_IMAGES["hline"], '" height="3" alt=""></td>',
			'<td>';
			$lh = $savlh; // restore original line height
			//-- print the father box
			print_pedigree_person($family->getHusband());
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
						$this->printEmptyBox($bwidth, $bheight);
						echo '</tr>';
					}
					echo '</table>';
				}
			}
			echo '</tr><tr>',
			'<td class="tdtop"><img class="pvline" src="', $WT_IMAGES["vline"], '" height="', $lh + 1, '" alt=""></td>',
			'<td><img class="line4" src="', $WT_IMAGES["hline"], '" height="3" alt=""></td>',
			'<td>';
			//-- print the mother box
			print_pedigree_person($family->getWife());
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
						$this->printEmptyBox($bwidth, $bheight);
						echo '</tr>';
						$this->printEmptyBox($bwidth, $bheight);
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
	 * @param string  $pid
	 * @param integer $depth
	 *
	 * @return integer
	 */
	private function maxDescendencyGenerations($pid, $depth) {
		if ($depth > $this->generations) {
			return $depth;
		}
		$person = WT_Individual::getInstance($pid);
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
	 *
	 * @param integer $bwidth
	 * @param integer $bheight
	 */
	private function printEmptyBox($bwidth, $bheight) {
		echo '<tr><td><div style="width:', $bwidth + 16, 'px; height:', $bheight + 8, 'px;"></div></td><td>';
	}

	/**
	 * Print a “Family Book” for an individual
	 *
	 * @param WT_Individual $person
	 * @param integer       $descent_steps
	 */
	public function printFamilyBook(WT_Individual $person, $descent_steps) {
		global $first_run;

		if ($descent_steps == 0 || !$person->canShowName()) {
			return;
		}
		$families = $person->getSpouseFamilies();
		if (count($families) > 0 || empty($first_run)) {
			$first_run = true;
			echo
			'<h3>',
			/* I18N: A title/heading. %s is an individual’s name */ WT_I18N::translate('Family of %s', $person->getFullName()),
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
