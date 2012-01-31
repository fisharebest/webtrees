<?php
//	Controller for the compact chart
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_charts.php';

class WT_Controller_Familybook extends WT_Controller_Chart {
	// Data for the view
	public $show_full  =null;
	public $show_spouse=null;
	public $descent    =null;
	public $generations=null;
	public $box_width  =null;

	// Data for the controller
	private $dgenerations=null;

	public function __construct() {
		parent::__construct();

		$PEDIGREE_FULL_DETAILS      =get_gedcom_setting(WT_GED_ID, 'PEDIGREE_FULL_DETAILS');
		$MAX_DESCENDANCY_GENERATIONS=get_gedcom_setting(WT_GED_ID, 'MAX_DESCENDANCY_GENERATIONS');

		// Extract the request parameters
		$this->show_full  =safe_GET('show_full',     array('0', '1'), $PEDIGREE_FULL_DETAILS);
		$this->show_spouse=safe_GET('show_spouse',   '1', '0');
		$this->descent    =safe_GET_integer('descent',       0, 9, 5);
		$this->generations=safe_GET_integer('generations',   2, $MAX_DESCENDANCY_GENERATIONS, 2);
		$this->box_width  =safe_GET_integer('box_width',     50, 300, 100);

		// Box sizes are set globally in the theme.  Modify them here.
		global $bwidth, $bheight, $cbwidth, $cbheight, $Dbwidth, $Dbheight;
		$Dbwidth =$this->box_width * $bwidth  / 100;
		$Dbheight=$this->box_width * $bheight / 100;
		$bwidth  =$Dbwidth;
		$bheight =$Dbheight;
		
		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $cbwidth;
			$bheight = $cbheight;
		}

		if ($this->root && $this->root->canDisplayName()) {
			$this->setPageTitle(
				/* I18N: %s is a person's name */
				WT_I18N::translate('Family book of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(WT_I18N::translate('Family book'));
		}
	}

	function print_descendency($person, $count) {
		global $bwidth, $bheight, $bhalfheight;
		global $TEXT_DIRECTION, $WT_IMAGES;

		// print_pedigree_person() requires these globals.
		global $show_full, $box_width;
		$show_full=$this->show_full;
		$box_width=$this->box_width;

		if ($count>=$this->dgenerations) {
			return 0;
		}

		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
		echo "<tr>";
		echo "<td width=\"".($bwidth-2)."\">";
		$numkids = 0;
		$sfamilies=$person->getSpouseFamilies();
		if (count($sfamilies)>0) {
			$firstkids = 0;
			foreach ($sfamilies as $family) {
				$children=$family->getChildren();
				if (count($children)>0) {
					echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
					foreach ($children as $i=>$child) {
						$rowspan = 2;
						if ($i>0 && $i<count($children)-1) {
							$rowspan=1;
						}
						echo "<tr><td rowspan=\"$rowspan\" width=\"$bwidth\" style=\"padding-top: 2px;\">";
						if ($count < $this->dgenerations-1) {
							$kids = $this->print_descendency($child, $count+1);
							if ($i==0) $firstkids = $kids;
							$numkids += $kids;
						} else {
							print_pedigree_person($child);
							$numkids++;
						}
						echo "</td>";
						$twidth = 7;
						if (count($children)==1) {
							$twidth+=3;
						}
						echo "<td rowspan=\"$rowspan\"><img class=\"line4\" src=\"".$WT_IMAGES["hline"]."\" width=\"$twidth\" height=\"3\" alt=\"\"></td>";
						if (count($children)>1) {
							if ($i==0) {
								echo "<td height=\"".($bhalfheight+3)."\"><img class=\"lineb\" src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td></tr>";
								echo "<tr><td height=\"".($bhalfheight+3)."\" style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td>";
							}
							else if ($i==count($children)-1) {
								echo "<td height=\"".($bhalfheight+4)."\" style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td></tr>";
								echo "<tr><td height=\"".($bhalfheight+4)."\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td>";
							}
							else {
								echo "<td style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td>";
							}
						}
						echo "</tr>";
					}
					echo "</table>";
				}
			}
			echo "</td>";
			echo "<td width=\"$bwidth\">";
		}

		if ($numkids==0) {
			$numkids = 1;
			$tbwidth = $bwidth+16;
			for ($j=$count; $j<$this->dgenerations; $j++) {
				echo "</td><td width=\"$bwidth\">";
			}
		}
		//-- add offset divs to make things line up better
		if ($this->show_spouse) {
			foreach ($sfamilies as $family) {
				echo '<br><div style="height:', $bheight, 'px;width:', $bwidth, 'px;"><br></div>';
			}
		}
		print_pedigree_person($person);

		if ($this->show_spouse) {
			foreach ($sfamilies as $family) {
				echo '<br>', $family->getMarriage()->print_simple_fact();
				print_pedigree_person($family->getSpouse($person));
			}
		}

		echo "</td></tr>";
		echo "</table>";
		return $numkids;
	}

	function print_person_pedigree($person, $count) {
		global $SHOW_EMPTY_BOXES, $WT_IMAGES, $bheight, $bhalfheight;
		if ($count>=$this->generations || !$person) return;
		$hheight = ($bhalfheight+3) * pow(2,($this->generations-$count-1));
		foreach ($person->getChildFamilies() as $family) {
			echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">";
			$height="100%";
			echo "<tr>";
			if ($count<$this->generations-1) {
				echo "<td height=\"".$hheight."\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td>";
				echo "<td rowspan=\"2\"><img src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\"></td>";
			}
			echo "<td rowspan=\"2\">";
			print_pedigree_person($family->getHusband());
			echo "</td>";
			echo "<td rowspan=\"2\">";
			$this->print_person_pedigree($family->getHusband(), $count+1);
			echo "</td>";
			echo "</tr><tr><td height=\"".$hheight."\"";
			if ($count<$this->generations-1) {
				echo " style=\"background: url('".$WT_IMAGES["vline"]."');\" ";
			}
			echo "><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td></tr><tr>";
			if ($count<$this->generations-1) {
				echo "<td height=\"".$hheight."\" style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td>";
				echo "<td rowspan=\"2\"><img src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\"></td>";
			}
			echo "<td rowspan=\"2\">";
			print_pedigree_person($family->getWife());
			echo "</td>";
			echo "<td rowspan=\"2\">";
			$this->print_person_pedigree($family->getWife(), $count+1);
			echo "</td>";
			echo "</tr>";
			if ($count<$this->generations-1) {
				echo "<tr><td height=\"".$hheight."\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td></tr>";
			}
			echo "</table>";
		}
	}

	function print_family_book($person, $descent) {
		global $firstrun;

		if ($descent==0 || !$person->canDisplayName()) {
			return;
		}
		$families=$person->getSpouseFamilies();
		if (count($families)>0 || empty($firstrun)) {
			$firstrun=true;
			echo
				'<h2 style="text-align:center">',
				/* I18N: A title/heading. %s is a person's name */ WT_I18N::translate('Family of %s', $person->getFullName()),
				'</h2><table cellspacing="0" cellpadding="0" border="0"><tr><td valign="middle">';
			$this->dgenerations = $this->generations;
			$this->print_descendency($person, 1);
			echo '</td><td valign="middle">';
			$this->print_person_pedigree($person, 1);
			echo '</td></tr></table><br><br><hr style="page-break-after:always;"><br><br>';

			foreach ($families as $family) {
				foreach ($family->getChildren() as $child) {
					$this->print_family_book($child, $descent-1);
				}
			}
		}
	}
}
