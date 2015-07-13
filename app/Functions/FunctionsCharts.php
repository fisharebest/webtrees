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
namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;

/**
 * Class FunctionsCharts - common functions
 */
class FunctionsCharts {
	/**
	 * print a table cell with sosa number
	 *
	 * @param int $sosa
	 * @param string $pid optional pid
	 * @param string $arrowDirection direction of link arrow
	 */
	public static function printSosaNumber($sosa, $pid = "", $arrowDirection = "up") {
		if (substr($sosa, -1, 1) == ".") {
			$personLabel = substr($sosa, 0, -1);
		} else {
			$personLabel = $sosa;
		}
		if ($arrowDirection == "blank") {
			$visibility = "hidden";
		} else {
			$visibility = "normal";
		}
		echo "<td class=\"subheaders center\" style=\"vertical-align: middle; text-indent: 0px; margin-top: 0px; white-space: nowrap; visibility: ", $visibility, ";\">";
		echo $personLabel;
		if ($sosa != "1" && $pid != "") {
			if ($arrowDirection == "left") {
				$dir = 0;
			} elseif ($arrowDirection == "right") {
				$dir = 1;
			} elseif ($arrowDirection == "down") {
				$dir = 3;
			} else {
				$dir = 2; // either 'blank' or 'up'
			}
			echo '<br>';
			self::printUrlArrow('#' . $pid, $pid, $dir);
		}
		echo '</td>';
	}

	/**
	 * print the parents table for a family
	 *
	 * @param Family $family family gedcom ID
	 * @param int $sosa child sosa number
	 * @param string $label indi label (descendancy booklet)
	 * @param string $parid parent ID (descendancy booklet)
	 * @param string $gparid gd-parent ID (descendancy booklet)
	 * @param int $show_full large or small box
	 */
	public static function printFamilyParents(Family $family, $sosa = 0, $label = '', $parid = '', $gparid = '', $show_full = 1) {

		if ($show_full) {
			$pbheight = Theme::theme()->parameter('chart-box-y') + 14;
		} else {
			$pbheight = Theme::theme()->parameter('compact-chart-box-y') + 14;
		}

		$husb = $family->getHusband();
		if ($husb) {
			echo '<a name="', $husb->getXref(), '"></a>';
		} else {
			$husb = new Individual('M', "0 @M@ INDI\n1 SEX M", null, $family->getTree());
		}
		$wife = $family->getWife();
		if ($wife) {
			echo '<a name="', $wife->getXref(), '"></a>';
		} else {
			$wife = new Individual('F', "0 @F@ INDI\n1 SEX F", null, $family->getTree());
		}

		if ($sosa) {
			echo '<p class="name_head">', $family->getFullName(), '</p>';
		}

		/**
		 * husband side
		 */
		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td rowspan=\"2\">";
		echo "<table border='0'><tr>";

		if ($parid) {
			if ($husb->getXref() == $parid) {
				self::printSosaNumber($label);
			} else {
				self::printSosaNumber($label, "", "blank");
			}
		} elseif ($sosa) {
			self::printSosaNumber($sosa * 2);
		}
		if ($husb->isPendingAddtion()) {
			echo '<td valign="top" class="facts_value new">';
		} elseif ($husb->isPendingDeletion()) {
			echo '<td valign="top" class="facts_value old">';
		} else {
			echo '<td valign="top">';
		}
		FunctionsPrint::printPedigreePerson($husb, $show_full);
		echo "</td></tr></table>";
		echo "</td>";
		// husband’s parents
		$hfam = $husb->getPrimaryChildFamily();
		if ($hfam) {
			// remove the|| test for $sosa
			echo "<td rowspan=\"2\"><img src=\"" . Theme::theme()->parameter('image-hline') . "\" alt=\"\"></td><td rowspan=\"2\"><img src=\"" . Theme::theme()->parameter('image-vline') . "\" width=\"3\" height=\"" . ($pbheight + 9) . "\" alt=\"\"></td>";
			echo "<td><img class=\"line5\" src=\"" . Theme::theme()->parameter('image-hline') . "\" alt=\"\"></td><td>";
			// husband’s father
			if ($hfam && $hfam->getHusband()) {
				echo "<table border='0'><tr>";
				if ($sosa > 0) {
					self::printSosaNumber($sosa * 4, $hfam->getHusband()->getXref(), "down");
				}
				if (!empty($gparid) && $hfam->getHusband()->getXref() == $gparid) {
					self::printSosaNumber(trim(substr($label, 0, -3), ".") . ".");
				}
				echo "<td valign=\"top\">";
				FunctionsPrint::printPedigreePerson($hfam->getHusband(), $show_full);
				echo "</td></tr></table>";
			} elseif ($hfam && !$hfam->getHusband()) {
				// Empty box for grandfather
				echo "<table border='0'><tr>";
				echo '<td valign="top">';
				FunctionsPrint::printPedigreePerson($hfam->getHusband(), $show_full);
				echo '</td></tr></table>';
			}
			echo "</td>";
		}
		if ($hfam && ($sosa != -1)) {
			echo '<td valign="middle" rowspan="2">';
			self::printUrlArrow(($sosa == 0 ? '?famid=' . $hfam->getXref() . '&amp;ged=' . $hfam->getTree()->getNameUrl() : '#' . $hfam->getXref()), $hfam->getXref(), 1);
			echo '</td>';
		}
		if ($hfam) {
			// husband’s mother
			echo "</tr><tr><td><img src=\"" . Theme::theme()->parameter('image-hline') . "\" alt=\"\"></td><td>";
			if ($hfam && $hfam->getWife()) {
				echo "<table border='0'><tr>";
				if ($sosa > 0) {
					self::printSosaNumber($sosa * 4 + 1, $hfam->getWife()->getXref(), "down");
				}
				if (!empty($gparid) && $hfam->getWife()->getXref() == $gparid) {
					self::printSosaNumber(trim(substr($label, 0, -3), ".") . ".");
				}
				echo '<td valign="top">';
				FunctionsPrint::printPedigreePerson($hfam->getWife(), $show_full);
				echo '</td></tr></table>';
			} elseif ($hfam && !$hfam->getWife()) {
				// Empty box for grandmother
				echo "<table border='0'><tr>";
				echo '<td valign="top">';
				FunctionsPrint::printPedigreePerson($hfam->getWife(), $show_full);
				echo '</td></tr></table>';
			}
			echo '</td>';
		}
		echo '</tr></table>';
		if ($sosa && $family->canShow()) {
			foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
				echo '<a href="', $family->getHtmlUrl(), '" class="details1">';
				echo str_repeat('&nbsp;', 10);
				echo $fact->summary();
				echo '</a>';
			}
		} else {
			echo '<br>';
		}

		/**
		 * wife side
		 */
		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td rowspan=\"2\">";
		echo "<table><tr>";
		if ($parid) {
			if ($wife->getXref() == $parid) {
				self::printSosaNumber($label);
			} else {
				self::printSosaNumber($label, "", "blank");
			}
		} elseif ($sosa) {
			self::printSosaNumber($sosa * 2 + 1);
		}
		if ($wife->isPendingAddtion()) {
			echo '<td valign="top" class="facts_value new">';
		} elseif ($wife->isPendingDeletion()) {
			echo '<td valign="top" class="facts_value old">';
		} else {
			echo '<td valign="top">';
		}
		FunctionsPrint::printPedigreePerson($wife, $show_full);
		echo "</td></tr></table>";
		echo "</td>";
		// wife’s parents
		$hfam = $wife->getPrimaryChildFamily();

		if ($hfam) {
			echo "<td rowspan=\"2\"><img src=\"" . Theme::theme()->parameter('image-hline') . "\" alt=\"\"></td><td rowspan=\"2\"><img src=\"" . Theme::theme()->parameter('image-vline') . "\" width=\"3\" height=\"" . ($pbheight + 9) . "\" alt=\"\"></td>";
			echo "<td><img class=\"line5\" src=\"" . Theme::theme()->parameter('image-hline') . "\" alt=\"\"></td><td>";
			// wife’s father
			if ($hfam && $hfam->getHusband()) {
				echo "<table><tr>";
				if ($sosa > 0) {
					self::printSosaNumber($sosa * 4 + 2, $hfam->getHusband()->getXref(), "down");
				}
				if (!empty($gparid) && $hfam->getHusband()->getXref() == $gparid) {
					self::printSosaNumber(trim(substr($label, 0, -3), ".") . ".");
				}
				echo "<td valign=\"top\">";
				FunctionsPrint::printPedigreePerson($hfam->getHusband(), $show_full);
				echo "</td></tr></table>";
			} elseif ($hfam && !$hfam->getHusband()) {
				// Empty box for grandfather
				echo "<table border='0'><tr>";
				echo '<td valign="top">';
				FunctionsPrint::printPedigreePerson($hfam->getHusband(), $show_full);
				echo '</td></tr></table>';
			}
			echo "</td>";
		}
		if ($hfam && ($sosa != -1)) {
			echo '<td valign="middle" rowspan="2">';
			self::printUrlArrow(($sosa == 0 ? '?famid=' . $hfam->getXref() . '&amp;ged=' . $hfam->getTree()->getNameUrl() : '#' . $hfam->getXref()), $hfam->getXref(), 1);
			echo '</td>';
		}
		if ($hfam) {
			// wife’s mother
			echo "</tr><tr><td><img src=\"" . Theme::theme()->parameter('image-hline') . "\" alt=\"\"></td><td>";
			if ($hfam && $hfam->getWife()) {
				echo "<table><tr>";
				if ($sosa > 0) {
					self::printSosaNumber($sosa * 4 + 3, $hfam->getWife()->getXref(), "down");
				}
				if (!empty($gparid) && $hfam->getWife()->getXref() == $gparid) {
					self::printSosaNumber(trim(substr($label, 0, -3), ".") . ".");
				}
				echo "<td valign=\"top\">";
				FunctionsPrint::printPedigreePerson($hfam->getWife(), $show_full);
				echo "</td></tr></table>";
			} elseif ($hfam && !$hfam->getWife()) {
				// Empty box for grandmother
				echo "<table border='0'><tr>";
				echo '<td valign="top">';
				FunctionsPrint::printPedigreePerson($hfam->getWife(), $show_full);
				echo '</td></tr></table>';
			}
			echo '</td>';
		}
		echo "</tr></table>";
	}

	/**
	 * print the children table for a family
	 *
	 * @param Family $family family
	 * @param string $childid child ID
	 * @param int $sosa child sosa number
	 * @param string $label indi label (descendancy booklet)
	 * @param int $show_cousins display cousins on chart
	 * @param int $show_full large or small box
	 */
	public static function printFamilyChildren(Family $family, $childid = '', $sosa = 0, $label = '', $show_cousins = 0, $show_full = 1) {

		if ($show_full) {
			$bheight = Theme::theme()->parameter('chart-box-y');
		} else {
			$bheight = Theme::theme()->parameter('compact-chart-box-y');
		}

		$pbheight = $bheight + 14;

		$children = $family->getChildren();
		$numchil  = count($children);

		echo '<table border="0" cellpadding="0" cellspacing="2"><tr>';
		if ($sosa > 0) {
			echo '<td></td>';
		}
		echo '<td><span class="subheaders">';
		if ($numchil == 0) {
			echo I18N::translate('No children');
		} else {
			echo I18N::plural('%s child', '%s children', $numchil, $numchil);
		}
		echo '</span>';

		if ($sosa == 0 && Auth::isEditor($family->getTree())) {
			echo '<br>';
			echo "<a href=\"#\" onclick=\"return add_child_to_family('", $family->getXref(), "', 'U');\">" . I18N::translate('Add a child to this family') . "</a>";
			echo ' <a class="icon-sex_m_15x15" href="#" onclick="return add_child_to_family(\'', $family->getXref(), '\', \'M\');" title="', I18N::translate('son'), '"></a>';
			echo ' <a class="icon-sex_f_15x15" href="#" onclick="return add_child_to_family(\'', $family->getXref(), '\', \'F\');" title="', I18N::translate('daughter'), '"></a>';
			echo '<br><br>';
		}
		echo '</td>';
		if ($sosa > 0) {
			echo '<td></td><td></td>';
		}
		echo '</tr>';

		$nchi = 1;
		if ($children) {
			foreach ($children as $child) {
				echo '<tr>';
				if ($sosa != 0) {
					if ($child->getXref() == $childid) {
						self::printSosaNumber($sosa, $childid);
					} elseif (empty($label)) {
						self::printSosaNumber("");
					} else {
						self::printSosaNumber($label . ($nchi++) . ".");
					}
				}
				if ($child->isPendingAddtion()) {
					echo '<td valign="middle" class="new">';
				} elseif ($child->isPendingDeletion()) {
					echo '<td valign="middle" class="old">';
				} else {
					echo '<td valign="middle">';
				}
				FunctionsPrint::printPedigreePerson($child, $show_full);
				echo "</td>";
				if ($sosa != 0) {
					// loop for all families where current child is a spouse
					$famids = $child->getSpouseFamilies();

					$maxfam = count($famids) - 1;
					for ($f = 0; $f <= $maxfam; $f++) {
						$famid_child = $famids[$f]->getXref();
						// multiple marriages
						if ($f > 0) {
							echo '</tr><tr><td></td>';
							echo '<td valign="top"';
							if (I18N::direction() === 'rtl') {
								echo ' align="left">';
							} else {
								echo ' align="right">';
							}

							//find out how many cousins there are to establish vertical line on second families
							$fchildren = $famids[$f]->getChildren();
							$kids      = count($fchildren);
							$Pheader   = ($bheight - 1) * $kids;
							$PBadj     = 6; // default
							if ($show_cousins > 0) {
								if ($kids) {
									$PBadj = max(0, $Pheader / 2 + $kids * 4.5);
								}
							}

							if ($f == $maxfam) {
								echo "<img height=\"" . ((($bheight / 2)) + $PBadj) . "px\"";
							} else {
								echo "<img height=\"" . $pbheight . "px\"";
							}
							echo " width=\"3\" src=\"" . Theme::theme()->parameter('image-vline') . "\" alt=\"\">";
							echo "</td>";
						}
						echo "<td class=\"details1\" valign=\"middle\" align=\"center\">";
						$spouse = $famids[$f]->getSpouse($child);

						$marr = $famids[$f]->getFirstFact('MARR');
						$div  = $famids[$f]->getFirstFact('DIV');
						if ($marr) {
							// marriage date
							echo $marr->getDate()->minimumDate()->format('%Y');
							// divorce date
							if ($div) {
								echo '–', $div->getDate()->minimumDate()->format('%Y');
							}
						}
						echo "<br><img width=\"100%\" class=\"line5\" height=\"3\" src=\"" . Theme::theme()->parameter('image-hline') . "\" alt=\"\">";
						echo "</td>";
						// spouse information
						echo "<td style=\"vertical-align: center;";
						if (!empty($divrec)) {
							echo " filter:alpha(opacity=40);opacity:0.4;\">";
						} else {
							echo "\">";
						}
						FunctionsPrint::printPedigreePerson($spouse, $show_full);
						echo "</td>";
						// cousins
						if ($show_cousins) {
							self::printCousins($famid_child, $show_full);
						}
					}
				}
				echo "</tr>";
			}
		} elseif ($sosa < 1) {
			// message 'no children' except for sosa
			if (preg_match('/\n1 NCHI (\d+)/', $family->getGedcom(), $match) && $match[1] == 0) {
				echo '<tr><td><i class="icon-childless"></i> ' . I18N::translate('This family remained childless') . '</td></tr>';
			}
		}
		echo "</table><br>";
	}

	/**
	 * print a family with Sosa-Stradonitz numbering system
	 * ($rootid=1, father=2, mother=3 ...)
	 *
	 * @param string $famid family gedcom ID
	 * @param string $childid tree root ID
	 * @param int $sosa starting sosa number
	 * @param string $label indi label (descendancy booklet)
	 * @param string $parid parent ID (descendancy booklet)
	 * @param string $gparid gd-parent ID (descendancy booklet)
	 * @param int $show_cousins display cousins on chart
	 * @param int $show_full large or small box
	 */
	public static function printSosaFamily($famid, $childid, $sosa, $label = '', $parid = '', $gparid = '', $show_cousins = 0, $show_full = 1) {
		global $WT_TREE;

		echo '<hr>';
		echo '<p style="page-break-before: always;">';
		if (!empty($famid)) {
			echo '<a name="', $famid, '"></a>';
		}
		self::printFamilyParents(Family::getInstance($famid, $WT_TREE), $sosa, $label, $parid, $gparid, $show_full);
		echo '<br>';
		echo '<table><tr><td valign="top">';
		self::printFamilyChildren(Family::getInstance($famid, $WT_TREE), $childid, $sosa, $label, $show_cousins, $show_full);
		echo '</td></tr></table>';
		echo '<br>';
	}

	/**
	 * print an arrow to a new url
	 *
	 * @param string $url target url
	 * @param string $label arrow label
	 * @param int $dir arrow direction 0=left 1=right 2=up 3=down (default=2)
	 */
	public static function printUrlArrow($url, $label, $dir = 2) {
		if ($url === '') {
			return;
		}

		// arrow direction
		$adir = $dir;
		if (I18N::direction() === 'rtl' && $dir === 0) {
			$adir = 1;
		}
		if (I18N::direction() === 'rtl' && $dir === 1) {
			$adir = 0;
		}

		// arrow style     0         1         2         3
		$array_style = array('icon-larrow', 'icon-rarrow', 'icon-uarrow', 'icon-darrow');
		$astyle      = $array_style[$adir];

		// Labels include people’s names, which may contain markup
		echo '<a href="' . $url . '" title="' . strip_tags($label) . '" class="' . $astyle . '"></a>';
	}

	/**
	 * builds and returns sosa relationship name in the active language
	 *
	 * @param string $sosa sosa number
	 *
	 * @return string
	 */
	public static function getSosaName($sosa) {
		$path = '';
		while ($sosa > 1) {
			if ($sosa % 2 == 1) {
				$sosa -= 1;
				$path = 'mot' . $path;
			} else {
				$path = 'fat' . $path;
			}
			$sosa /= 2;
		}

		return Functions::getRelationshipNameFromPath($path, null, null);
	}

	/**
	 * print cousins list
	 *
	 * @param string $famid family ID
	 * @param int $show_full large or small box
	 */
	public static function printCousins($famid, $show_full = 1) {
		global $WT_TREE;

		if ($show_full) {
			$bheight = Theme::theme()->parameter('chart-box-y');
		} else {
			$bheight = Theme::theme()->parameter('compact-chart-box-y');
		}

		$family    = Family::getInstance($famid, $WT_TREE);
		$fchildren = $family->getChildren();

		$kids = count($fchildren);

		echo '<td valign="middle" height="100%">';
		if ($kids) {
			echo '<table cellspacing="0" cellpadding="0" border="0" ><tr valign="middle">';
			if ($kids > 1) {
				echo '<td rowspan="', $kids, '" valign="middle" align="right"><img width="3px" height="', (($bheight + 9) * ($kids - 1)), 'px" src="', Theme::theme()->parameter('image-vline'), '" alt=""></td>';
			}
			$ctkids = count($fchildren);
			$i      = 1;
			foreach ($fchildren as $fchil) {
				if ($i == 1) {
					echo '<td><img width="10px" height="3px" align="top"';
				} else {
					echo '<td><img width="10px" height="3px"';
				}
				if (I18N::direction() === 'ltr') {
					echo ' style="padding-right: 2px;"';
				} else {
					echo ' style="padding-left: 2px;"';
				}
				echo ' src="', Theme::theme()->parameter('image-hline'), '" alt=""></td><td>';
				FunctionsPrint::printPedigreePerson($fchil, $show_full);
				echo '</td></tr>';
				if ($i < $ctkids) {
					echo '<tr>';
					$i++;
				}
			}
			echo '</table>';
		} else {
			// If there is known that there are no children (as opposed to no known children)
			if (preg_match('/\n1 NCHI (\d+)/', $family->getGedcom(), $match) && $match[1] == 0) {
				echo ' <i class="icon-childless" title="', I18N::translate('This family remained childless'), '"></i>';
			}
		}
		echo '</td>';
	}
}
