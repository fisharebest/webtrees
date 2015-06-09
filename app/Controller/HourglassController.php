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
 * Controller for the hourglass chart
 */
class HourglassController extends ChartController {
	/** @var int Whether to show spouse details. */
	public $show_spouse;

	/** @var int Number of ascendancy generations to show. */
	public $generations;

	/** @var int Number of descendancy generations that exist. */
	private $dgenerations;

	/** @var int Half height of personbox. */
	public $bhalfheight;

	/** @var string An arrow that points to the start of the line */
	private $left_arrow;

	/** @var string An arrow that points to the end of the line. */
	private $right_arrow;

	/** @var bool Can the Javascript be loaded by the controller. */
	private $canLoadJS;

	const LINK        = "<a class='%s' href='%s' data-parms='%s-%s-%s'></a>";
	const SWITCH_LINK = "<a href='hourglass.php?rootid=%s&amp;show_spouse=%s&amp;show_full=%s&amp;generations=%s' class='name1'>%s</a>";

	/**
	 * Create the hourglass controller.
	 *
	 * @param string $rootid
	 * @param int    $show_full
	 * @param bool   $loadJS
	 */
	public function __construct($rootid = '', $show_full = 1, $loadJS = true) {
		global $WT_TREE;

		parent::__construct($show_full);

		// Extract parameters from
		$this->show_spouse = Filter::getInteger('show_spouse', 0, 1, 0);
		$this->generations = Filter::getInteger('generations', 2, $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS'), 3);

		$this->canLoadJS = $loadJS;

		//-- flip the arrows for RTL languages
		if (I18N::direction() === 'ltr') {
			$this->left_arrow  = 'icon-larrow';
			$this->right_arrow = 'icon-rarrow';
		} else {
			$this->left_arrow  = 'icon-rarrow';
			$this->right_arrow = 'icon-larrow';
		}

		$this->bhalfheight = (int) ($this->getBoxDimensions()->height / 2);

		//Checks how many generations of descendency is for the person for formatting purposes
		$this->dgenerations = $this->maxDescendencyGenerations($this->root, 0);
		if ($this->dgenerations < 1) {
			$this->dgenerations = 1;
		}

		$this->setPageTitle(/* I18N: %s is an individual’s name */ I18N::translate('Hourglass chart of %s', $this->root->getFullName()));
	}

	/**
	 * Prints pedigree of the person passed in. Which is the descendancy
	 *
	 * @param Individual $person ID of person to print the pedigree for
	 * @param int        $count  generation count, so it recursively calls itself
	 */
	public function printPersonPedigree(Individual $person, $count) {

		if ($count >= $this->generations) {
			return;
		}

		$genoffset = $this->generations; // handle pedigree n generations lines

		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		//
		if (count($person->getChildFamilies()) == 0) {
			echo '<table class="xyz"><tr><td>' . $this->printEmptyBox() . '</td>';
			echo '<td>';
			//-- recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td></tr>';
			echo '<tr><td>' . $this->printEmptyBox() . '</td>';
			echo '<td>';
			//-- recursively get the father’s family
			$this->printPersonPedigree($person, $count + 1);
			echo '</td><td></tr></table>';
		}
		foreach ($person->getChildFamilies() as $family) {
			echo '<table class="hourglassChart">';
			echo '<tr>';
			echo '<td style="vertical-align:bottom"><img class="line3 pvline" src="' . Theme::theme()->parameter('image-vline') . '" width="3"></td>';
			echo '<td><img class="line4" src="' . Theme::theme()->parameter('image-hline') . '" width="7" height="3"></td>';
			echo '<td>';
			//-- print the father box
			FunctionsPrint::printPedigreePerson($family->getHusband(), $this->showFull());
			echo "</td>";
			if ($family->getHusband()) {
				$ARID = $family->getHusband()->getXref();
				echo "<td id=\"td_" . $ARID . "\">";

				//-- print an Ajax arrow on the last generation of the adult male
				if ($count == $this->generations - 1 && $family->getHusband()->getChildFamilies()) {
					printf(self::LINK, $this->right_arrow, $ARID, 'asc', $this->showFull(), $this->show_spouse);
				}
				//-- recursively get the father’s family
				$this->printPersonPedigree($family->getHusband(), $count + 1);
				echo "</td>";
			} else {
				echo '<td>';
				if ($count < $genoffset - 1) {
					echo '<table>';
					for ($i = $count; $i < (pow(2, ($genoffset - 1) - $count) / 2) + 2; $i++) {
						$this->printEmptyBox();
						echo '</tr>';
						$this->printEmptyBox();
						echo '</tr>';
					}
					echo '</table>';
				}
			}
			echo
			'</tr><tr>',
			"<td style='vertical-align:top'><img class='pvline' src='" . Theme::theme()->parameter('image-vline') . "' width='3' alt=''></td>",
				'<td><img class="line4" src="' . Theme::theme()->parameter('image-hline') . '" width="7" height="3" alt=""></td>',
			'<td>';
			//-- print the mother box
			FunctionsPrint::printPedigreePerson($family->getWife(), $this->showFull());
			echo '</td>';
			if ($family->getWife()) {
				$ARID = $family->getWife()->getXref();
				echo '<td id="td_' . $ARID . '">';

				//-- print an ajax arrow on the last generation of the adult female
				if ($count == $this->generations - 1 && $family->getWife()->getChildFamilies()) {
					printf(self::LINK, $this->right_arrow, $ARID, 'asc', $this->showFull(), $this->show_spouse);
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
	 * @return string
	 */

	private function printEmptyBox() {
		return $this->showFull() ? Theme::theme()->individualBoxEmpty() : Theme::theme()->individualBoxSmallEmpty();
	}

	/**
	 * Prints descendency of passed in person
	 *
	 * @param Individual $person  person to print descendency for
	 * @param int        $count   count of generations to print
	 * @param bool       $showNav
	 *
	 * @return int
	 */
	public function printDescendency($person, $count, $showNav = true) {
		global $lastGenSecondFam;

		if ($count > $this->dgenerations) {
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
		if ($count == $this->dgenerations - 1) {
			if (isset($lastGenSecondFam)) {
				echo '<br>';
			}
			$lastGenSecondFam = true;
		}
		echo "<table id='table_$pid' class='hourglassChart' style='float:$tablealign'>";
		echo '<tr>';
		echo "<td style='text-align:$tablealign'>";
		$numkids  = 0;
		$families = $person->getSpouseFamilies();
		$famNum   = 0;
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
					$chil    = $person2->getXref();
					echo '<tr>';
					echo '<td id="td_', $chil, '" class="', I18N::direction(), '" style="text-align:', $otablealign, '">';
					$kids = $this->printDescendency($person2, $count + 1);
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
							echo '<td style="background: url(\'' . Theme::theme()->parameter('image-vline') . '\');"><img src=\'' . Theme::theme()->parameter('image-spacer') . '\' width="3" alt=""></td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</td>';
			echo '<td width="', $this->getBoxDimensions()->width, '">';
		}

		// Print the descendency expansion arrow
		if ($count == $this->dgenerations) {
			$numkids = 1;
			$tbwidth = $this->getBoxDimensions()->width + 16;
			for ($j = $count; $j < $this->dgenerations; $j++) {
				echo "<div style='width: ", $tbwidth, "px;'><br></div></td><td style='width:", $this->getBoxDimensions()->width, "px'>";
			}
			$kcount = 0;
			foreach ($families as $family) {
				$kcount += $family->getNumberOfChildren();
			}
			if ($kcount == 0) {
				echo "</td><td style='width:", $this->getBoxDimensions()->width, "px'>";
			} else {
				printf(self::LINK, $this->left_arrow, $pid, 'desc', $this->showFull(), $this->show_spouse);
				//-- move the arrow up to line up with the correct box
				if ($this->show_spouse) {
					echo str_repeat('<br><br><br>', count($families));
				}
				echo "</td><td style='width:", $this->getBoxDimensions()->width, "px'>";
			}
		}

		echo '<table id="table2_' . $pid . '"><tr><td>';
		FunctionsPrint::printPedigreePerson($person, $this->showFull());
		echo '</td><td><img class="line2" src="' . Theme::theme()->parameter('image-hline') . '" width="7" height="3">';

		//----- Print the spouse
		if ($this->show_spouse) {
			foreach ($families as $family) {
				echo "</td></tr><tr><td style='text-align:$otablealign'>";
				//-- shrink the box for the spouses
				$tempw = $this->getBoxDimensions()->width;
				$temph = $this->getBoxDimensions()->height;
				$this->getBoxDimensions()->width -= 10;
				$this->getBoxDimensions()->height -= 10;
				FunctionsPrint::printPedigreePerson($family->getSpouse($person), $this->showFull());
				$this->getBoxDimensions()->width  = $tempw;
				$this->getBoxDimensions()->height = $temph;
				$numkids += 0.95;
				echo "</td><td></td>";
			}
			//-- add offset divs to make things line up better
			if ($count == $this->dgenerations) {
				echo "<tr><td colspan '2'><div style='height:", ($this->bhalfheight / 2), "px; width:", $this->getBoxDimensions()->width, "px;'><br></div>";
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
				$num     = 0;
				foreach ($cfamids as $family) {
					$num += $family->getNumberOfChildren();
				}
				if ($num > 0) {
					echo '<div class="center" id="childarrow" style="position:absolute; width:', $this->getBoxDimensions()->width, 'px;">';
					echo '<a href="#" class="icon-darrow"></a>';
					echo '<div id="childbox">';
					echo '<table class="person_box"><tr><td>';

					foreach ($famids as $family) {
						echo "<span class='name1'>" . I18N::translate('Family') . "</span>";
						$spouse = $family->getSpouse($person);
						if ($spouse) {
							printf(self::SWITCH_LINK, $spouse->getXref(), $this->show_spouse, $this->showFull(), $this->generations, $spouse->getFullName());
						}
						foreach ($family->getChildren() as $child) {
							printf(self::SWITCH_LINK, $child->getXref(), $this->show_spouse, $this->showFull(), $this->generations, $child->getFullName());
						}
					}

					//-- print the siblings
					foreach ($cfamids as $family) {
						if ($family->getHusband() || $family->getWife()) {
							echo "<span class='name1'>" . I18N::translate('Parents') . "</span>";
							$husb = $family->getHusband();
							if ($husb) {
								printf(self::SWITCH_LINK, $husb->getXref(), $this->show_spouse, $this->showFull(), $this->generations, $husb->getFullName());
							}
							$wife = $family->getWife();
							if ($wife) {
								printf(self::SWITCH_LINK, $wife->getXref(), $this->show_spouse, $this->showFull(), $this->generations, $wife->getFullName());
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
							echo "</span>";
							foreach ($siblings as $child) {
								printf(self::SWITCH_LINK, $child->getXref(), $this->show_spouse, $this->showFull(), $this->generations, $child->getFullName());
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
	 * @param Individual $individual Start individual
	 * @param int        $depth      Pass in 0 and it calculates how far down descendency goes
	 *
	 * @return int Number of generations the descendency actually goes
	 */
	private function maxDescendencyGenerations(Individual $individual, $depth) {
		if ($depth > $this->generations) {
			return $depth;
		}
		$maxdc = $depth;
		foreach ($individual->getSpouseFamilies() as $family) {
			foreach ($family->getChildren() as $child) {
				$dc = $this->maxDescendencyGenerations($child, $depth + 1);
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
	 */
	public function setupJavascript() {
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
						var el = jQuery(e);
						el.height(Math.floor(el.parent().height()/2));
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
					jQuery('#td_'+id).load('hourglass_ajax.php?rootid='+ id +'&generations=1&type='+parms[0]+'&show_full='+(parms[1] ? 1:0) +'&show_spouse='+(parms[3] ? 1:0), function(){
						sizeLines();
					});
				});

				sizeLines();
			})();
		";

		if ($this->canLoadJS) {
			$this->addInlineJavascript($js);
		} else {
			return $js;
		}
	}
}
