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
 * Class WT_Controller_Hourglass - Controller for the hourglass chart
 */
class WT_Controller_Hourglass extends WT_Controller_Chart {
	var $pid = "";

	var $canedit = false;
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $show_full = 0;
	var $show_spouse = 0;
	var $generations;
	var $dgenerations;
	var $box_width;
	var $name;
	var $bhalfheight;
	private $canLoadJS;
	// Left and right get reversed on RTL pages
	private $left_arrow;
	private $right_arrow;
	//  the following are ajax variables  //
	var $ARID;

	CONST LINK = "<a class='%s' href='%s' data-parms='%s-%s-%s-%s'></a>";
	CONST SWITCH_LINK = "<a href='hourglass.php?rootid=%s&amp;show_spouse=%s&amp;show_full=%s&amp;generations=%s&amp;box_width=%s' class='name1'>%s</a>";

	/**
	 * @param string  $rootid
	 * @param integer $show_full
	 * @param boolean $loadJS
	 */
	function __construct($rootid = '', $show_full = 1, $loadJS = true) {
		global $bheight, $bwidth, $cbwidth, $cbheight, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS;
		global $TEXT_DIRECTION, $show_full;

		parent::__construct();

		// Extract parameters from from
		$this->pid = WT_Filter::get('rootid', WT_REGEX_XREF);
		$this->show_full = WT_Filter::getInteger('show_full', 0, 1, $PEDIGREE_FULL_DETAILS);
		$this->show_spouse = WT_Filter::getInteger('show_spouse', 0, 1, 0);
		$this->generations = WT_Filter::getInteger('generations', 2, $MAX_DESCENDANCY_GENERATIONS, 3);
		$this->box_width = WT_Filter::getInteger('box_width', 50, 300, 100);

		$this->canLoadJS = $loadJS;
		// This is passed as a global.  A parameter would be better...
		$show_full = $this->show_full;

		if (!empty($rootid)) {
			$this->pid = $rootid;
		}

		//-- flip the arrows for RTL languages
		if ($TEXT_DIRECTION == 'ltr') {
			$this->left_arrow = 'icon-larrow';
			$this->right_arrow = 'icon-rarrow';
		} else {
			$this->left_arrow = 'icon-rarrow';
			$this->right_arrow = 'icon-larrow';
		}

		// -- size of the detailed boxes based upon optional width parameter
		$Dbwidth = $this->box_width * $bwidth / 100;
		$Dbheight = $this->box_width * $bheight / 100;
		$bwidth = $Dbwidth;
		$bheight = $Dbheight;

		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $this->box_width * $cbwidth / 100;
			$bheight = $cbheight;
		}

		$this->bhalfheight = (int)($bheight / 2);

		// Validate parameters
		$this->hourPerson = WT_Individual::getInstance($this->pid);
		if (!$this->hourPerson) {
			$this->hourPerson = $this->getSignificantIndividual();
			$this->pid = $this->hourPerson->getXref();
		}

		$this->name = $this->hourPerson->getFullName();

		//Checks how many generations of descendency is for the person for formatting purposes
		$this->dgenerations = $this->maxDescendencyGenerations($this->pid, 0);
		if ($this->dgenerations < 1) {
			$this->dgenerations = 1;
		}

		$this->setPageTitle(/* I18N: %s is an individual’s name */ WT_I18N::translate('Hourglass chart of %s', $this->name));
	}

	/**
	 * Prints pedigree of the person passed in. Which is the descendancy
	 *
	 * @param WT_Individual $person ID of person to print the pedigree for
	 * @param integer       $count  generation count, so it recursively calls itself
	 */
	public function printPersonPedigree(WT_Individual $person, $count) {
		global $WT_IMAGES, $bheight, $bwidth;

		if ($count >= $this->generations) {
			return;
		}

		$genoffset = $this->generations;  // handle pedigree n generations lines

		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		//
		if (count($person->getChildFamilies()) == 0) {
			echo '<table><tr><td><div style="width:', $bwidth, 'px; height:', $bheight, 'px;"></div></td><td>';

			//-- recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr><tr><td><div style="width:', $bwidth, 'px; height:', $bheight, 'px;"></div></td><td>';
			//-- recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr></table>';
		}
		foreach ($person->getChildFamilies() as $family) {
			echo "<table class='hourglassChart'>";
			echo "<tr>";
			echo "<td style='vertical-align:bottom'><img class='line3 pvline' src='{$WT_IMAGES["vline"]}' width='3' alt=''></td>";
			echo "<td><img class='line4' src='{$WT_IMAGES["hline"]}' width='7' height='3' alt=''></td>";
			echo "<td>";
			//-- print the father box
			print_pedigree_person($family->getHusband());
			echo "</td>";
			if ($family->getHusband()) {
				$ARID = $family->getHusband()->getXref();
				echo "<td id=\"td_" . $ARID . "\">";

				//-- print an Ajax arrow on the last generation of the adult male
				if ($count == $this->generations - 1 && $family->getHusband()->getChildFamilies()) {
					printf(self::LINK, $this->right_arrow, $ARID, 'asc', $this->show_full, $this->box_width, $this->show_spouse);
				}
				//-- recursively get the father’s family
				$this->printPersonPedigree($family->getHusband(), $count + 1);
				echo "</td>";
			} else {
				echo '<td>';
				if ($count < $genoffset - 1) {
					echo '<table>';
					for ($i = $count; $i < (pow(2, ($genoffset - 1) - $count) / 2) + 2; $i++) {
						$this->printEmptyBox($bwidth, $bheight);
						echo '</tr>';
						$this->printEmptyBox($bwidth, $bheight);
						echo '</tr>';
					}
					echo '</table>';
				}
			}
			echo
			'</tr><tr>',
			"<td style='vertical-align:top'><img class='pvline' src='{$WT_IMAGES["vline"]}' width='3' alt=''></td>",
				'<td><img class="line4" src="' . $WT_IMAGES["hline"] . '" width="7" height="3" alt=""></td>',
			'<td>';
			//-- print the mother box
			print_pedigree_person($family->getWife());
			echo '</td>';
			if ($family->getWife()) {
				$ARID = $family->getWife()->getXref();
				echo '<td id="td_' . $ARID . '">';

				//-- print an ajax arrow on the last generation of the adult female
				if ($count == $this->generations - 1 && $family->getWife()->getChildFamilies()) {
					printf(self::LINK, $this->right_arrow, $ARID, 'asc', $this->show_full, $this->box_width, $this->show_spouse);
				}
				//-- recursively print the mother’s family
				$this->printPersonPedigree($family->getWife(), $count + 1);
				echo '</td>';
			}
			echo '</tr></table>';
			break;
		}
	}

	/**
	 * Print empty box
	 *
	 * @param integer $bwidth
	 * @param integer $bheight
	 *
	 * @return void
	 */
	private function printEmptyBox($bwidth, $bheight) {
		echo '<tr>',
		'<td>',
		'<div style="width:', $bwidth + 16, 'px; height:', $bheight + 8, 'px;"></div>',
		'</td>',
		'<td>';
	}

	/**
	 * Prints descendency of passed in person
	 *
	 * @param WT_Individual $person  person to print descendency for
	 * @param integer       $count   count of generations to print
	 * @param boolean       $showNav
	 *
	 * @return integer
	 */
	public function printDescendency($person, $count, $showNav = true) {
		global $TEXT_DIRECTION, $WT_IMAGES, $bheight, $bwidth, $lastGenSecondFam;

		if ($count > $this->dgenerations) {
			return;
		}

		$pid = $person->getXref();
		$tablealign = 'right';
		$otablealign = 'left';
		if ($TEXT_DIRECTION == 'rtl') {
			$tablealign = 'left';
			$otablealign = 'right';
		}

		//-- put a space between families on the last generation
		if ($count == $this->dgenerations - 1) {
			if (isset($lastGenSecondFam)) {
				echo '<br>';
			}
			$lastGenSecondFam = true;
		}
		echo "<table id='table_$pid' class='hourglassChart' style='float:$tablealign'>";
		echo '<tr>';
		echo "<td style='text-align:$tablealign'>";
		$numkids = 0;
		$families = $person->getSpouseFamilies();
		$famNum = 0;
		$children = array();
		if ($count < $this->dgenerations) {
			// Put all of the children in a common array
			foreach ($families as $family) {
				$famNum++;
				foreach ($family->getChildren() as $child) {
					$children[] = $child;
				}
			}

			$ct = count($children);
			if ($ct > 0) {
				echo "<table style='position: relative; top: auto; float: $tablealign;'>";
				for ($i = 0; $i < $ct; $i++) {
					$person2 = $children[$i];
					$chil = $person2->getXref();
					echo '<tr>';
					echo "<td id='td_$chil' class='$TEXT_DIRECTION' style='text-align:$otablealign'>";
					$kids = $this->printDescendency($person2, $count + 1);
					$numkids += $kids;
					echo '</td>';

					// Print the lines
					if ($ct > 1) {
						if ($i == 0) {
							// First child
							echo "<td style='vertical-align:bottom'><img alt='' class='line1 tvertline' id='vline_$chil' src='{$WT_IMAGES["vline"]}' width='3'></td>";
						} elseif ($i == $ct - 1) {
							// Last child
							echo "<td style='vertical-align:top'><img alt='' class='bvertline' id='vline_$chil' src='{$WT_IMAGES["vline"]}' width='3'></td>";
						} else {
							// Middle child
							echo '<td style="background: url(\'' . $WT_IMAGES['vline'] . '\');"><img src=\'' . $WT_IMAGES['spacer'] . '\' width="3" alt=""></td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</td>';
			echo '<td width="', $bwidth, '">';
		}

		// Print the descendency expansion arrow
		if ($count == $this->dgenerations) {
			$numkids = 1;
			$tbwidth = $bwidth + 16;
			for ($j = $count; $j < $this->dgenerations; $j++) {
				echo "<div style='width: ", $tbwidth, "px;'><br></div></td><td style='width:{$bwidth}px'>";
			}
			$kcount = 0;
			foreach ($families as $family) {
				$kcount += $family->getNumberOfChildren();
			}
			if ($kcount == 0) {
				echo "&nbsp;</td><td style='width:{$bwidth}px'>";
			} else {
				printf(self::LINK, $this->left_arrow, $pid, 'desc', $this->show_full, $this->box_width, $this->show_spouse);
				//-- move the arrow up to line up with the correct box
				if ($this->show_spouse) {
					echo str_repeat('<br><br><br>', count($families));
				}
				echo "</td><td style='width:{$bwidth}px'>";
			}
		}

		echo "<table id='table2_$pid'><tr><td>";
		print_pedigree_person($person);
		echo "</td><td><img class='line2' src='{$WT_IMAGES["hline"]}' width='7' height='3' alt=''>";

		//----- Print the spouse
		if ($this->show_spouse) {
			foreach ($families as $family) {
				echo "</td></tr><tr><td style='text-align:$otablealign'>";
				//-- shrink the box for the spouses
				$tempw = $bwidth;
				$temph = $bheight;
				$bwidth -= 10;
				$bheight -= 10;
				print_pedigree_person($family->getSpouse($person));
				$bwidth = $tempw;
				$bheight = $temph;
				$numkids += 0.95;
				echo "</td><td></td>";
			}
			//-- add offset divs to make things line up better
			if ($count == $this->dgenerations) {
				echo "<tr><td colspan '2'><div style='height: " . ($this->bhalfheight / 2) . "px; width: " . $bwidth . "px;'><br></div>";
			}
		}
		echo "</td></tr></table>";

		// For the root person, print a down arrow that allows changing the root of tree
		if ($showNav && $count == 1) {
			// NOTE: If statement OK
			if ($person->canShowName()) {
				// -- print left arrow for decendants so that we can move down the tree
				$famids = $person->getSpouseFamilies();
				//-- make sure there is more than 1 child in the family with parents
				$cfamids = $person->getChildFamilies();
				$num = 0;
				foreach ($cfamids as $family) {
					$num += $family->getNumberOfChildren();
				}
				if ($num > 0) {
					echo '<div class="center" id="childarrow" style="position:absolute; width:', $bwidth, 'px;">';
					echo '<a href="#" class="icon-darrow"></a>';
					echo '<div id="childbox">';
					echo '<table class="person_box"><tr><td>';

					foreach ($famids as $family) {
						echo "<span class='name1'>" . WT_I18N::translate('Family') . "</span>";
						$spouse = $family->getSpouse($person);
						if ($spouse) {
							printf(self::SWITCH_LINK, $spouse->getXref(), $this->show_spouse, $this->show_full, $this->generations, $this->box_width, $spouse->getFullName());
						}
						foreach ($family->getChildren() as $child) {
							printf(self::SWITCH_LINK, $child->getXref(), $this->show_spouse, $this->show_full, $this->generations, $this->box_width, $child->getFullName());
						}
					}

					//-- print the siblings
					foreach ($cfamids as $family) {
						if ($family->getHusband() || $family->getWife()) {
							echo "<span class='name1'>" . WT_I18N::translate('Parents') . "</span>";
							$husb = $family->getHusband();
							if ($husb) {
								printf(self::SWITCH_LINK, $husb->getXref(), $this->show_spouse, $this->show_full, $this->generations, $this->box_width, $husb->getFullName());
							}
							$wife = $family->getWife();
							if ($wife) {
								printf(self::SWITCH_LINK, $wife->getXref(), $this->show_spouse, $this->show_full, $this->generations, $this->box_width, $wife->getFullName());
							}
						}

						// filter out root person from children array so only siblings remain
						$siblings = array_filter($family->getChildren(), function (WT_Individual $item) use ($pid) {
							return $item->getXref() != $pid;
						});
						$num = count($siblings);
						if ($num) {
							echo "<span class='name1'>";
							echo $num > 1 ? WT_I18N::translate('Siblings') : WT_I18N::translate('Sibling');
							echo "</span>";
							foreach ($siblings as $child) {
								printf(self::SWITCH_LINK, $child->getXref(), $this->show_spouse, $this->show_full, $this->generations, $this->box_width, $child->getFullName());
							}
						}
					}
					echo '</td></tr></table>';
					echo '</div>';
					echo '</div>';
				}
			}
		}
		echo '</td></tr></table>';

		return $numkids;
	}

	/**
	 * Calculates number of generations a person has
	 *
	 * @param string  $pid   ID of person to see how far down the descendency goes
	 * @param integer $depth Pass in 0 and it calculates how far down descendency goes
	 *
	 * @return integer Number of generations the descendency actually goes
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
	 * setup all of the javascript that is needed for the hourglass chart
	 *
	 */
	function setupJavascript() {
		$js = "
			var WT_HOURGLASS_CHART = (function() {
				function sizeLines() {
					jQuery('.tvertline').each(function(i,e) {
						var pid = e.id.split('_').pop();
						e.style.height = Math.abs(jQuery('#table_' + pid)[0].offsetHeight - (jQuery('#table2_' + pid)[0].offsetTop + {$this->bhalfheight}+5)) + 'px';
					});

					jQuery('.bvertline').each(function(i,e) {
						var pid = e.id.split('_').pop();
						e.style.height = jQuery('#table_' + pid)[0].offsetTop + jQuery('#table2_' + pid)[0].offsetTop + {$this->bhalfheight}+5 + 'px';
					});

					jQuery('.pvline').each(function(i,e) {
						e.style.height = e.parentNode.offsetHeight/2 + 'px';
					});
				}

				jQuery('#childarrow').on('click', '.icon-darrow', function(e) {
					e.preventDefault();
					jQuery('#childbox').slideToggle('fast');
				})
				jQuery('.hourglassChart').on('click', '.icon-larrow, .icon-rarrow', function(e){
					e.preventDefault();
					e.stopPropagation();
					var self = jQuery(this),
						parms = self.data('parms').split('-'),
						id = self.attr('href');
					jQuery('#td_'+id).load('hourglass_ajax.php?rootid='+ id +'&generations=1&type='+parms[0]+'&show_full='+parms[1]+'&box_width='+parms[2]+'&show_spouse='+parms[3], function(){
						sizeLines();
					});
				});

				sizeLines();
				return '" . strip_tags($this->name) . "';
			})();
		";

		if ($this->canLoadJS) {
			$this->addInlineJavascript($js);
		} else {
			return $js;
		}
	}
}
