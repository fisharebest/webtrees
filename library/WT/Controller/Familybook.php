<?php
//	Controller for the familybook chart
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

class WT_Controller_Familybook extends WT_Controller_Chart {
	// Data for the view
	public $pid		 =" ";
	public $show_full  =null;
	public $show_spouse=null;
	public $descent    =null;
	public $generations=null;
	public $box_width  =null;
	public $rootid     =null;

	// Data for the controller
	private $dgenerations=null;
	private $loopcnt = 0;

	public function __construct() {
		parent::__construct();

		$PEDIGREE_FULL_DETAILS      =get_gedcom_setting(WT_GED_ID, 'PEDIGREE_FULL_DETAILS');
		$MAX_DESCENDANCY_GENERATIONS=get_gedcom_setting(WT_GED_ID, 'MAX_DESCENDANCY_GENERATIONS');

		// Extract the request parameters
		$this->pid        =safe_GET_xref('rootid');
		$this->show_full  =safe_GET('show_full',     array('0', '1'), $PEDIGREE_FULL_DETAILS);
		$this->show_spouse=safe_GET('show_spouse',   '1', '0');
		$this->descent    =safe_GET_integer('descent',       0, 9, 5);
		$this->generations=safe_GET_integer('generations',   2, $MAX_DESCENDANCY_GENERATIONS, 2);
		$this->box_width  =safe_GET_integer('box_width',     50, 300, 100);

		// Box sizes are set globally in the theme.  Modify them here.
		global $bwidth, $bheight, $cbwidth, $cbheight, $Dbwidth, $bhalfheight, $Dbheight; 
		$Dbwidth =$this->box_width * $bwidth  / 100;
		$Dbheight=$this->box_width * $bheight / 100;
		$bwidth  =$Dbwidth;
		$bheight =$Dbheight;
		
		// Validate parameters
		if (!empty($rootid)) $this->pid = $rootid;
		$rootid=$this->rootid;
		$this->hourPerson = WT_Person::getInstance($this->pid);
		if (!$this->hourPerson) {
			$this->hourPerson=$this->getSignificantIndividual();
			$this->pid=$this->hourPerson->getXref();
		}
		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $cbwidth;
			$bheight = $cbheight;
		}
		$bhalfheight = $bheight / 2;
		if ($this->root && $this->root->canDisplayName()) {
			$this->setPageTitle(
				/* I18N: %s is a person's name */
				WT_I18N::translate('Family book of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(WT_I18N::translate('Family book'));
		}
		//Checks how many generations of descendency is for the person for formatting purposes
		$this->dgenerations = $this->max_descendency_generations($this->pid, 0);
		if ($this->dgenerations<1) $this->dgenerations=1;
	}
	
	/**
	* Prints descendency of passed in person
	*
	* @param mixed $pid ID of person to print descendency for
	* @param mixed $count count of generations to print
	* @access public
	* @return void
	*/
	function print_descendency($person, $count) {
		global $TEXT_DIRECTION, $WT_IMAGES, $bwidth, $bheight, $bhalfheight;
		
		if ($count>$this->dgenerations) return 0;
		if (!$person) return;
		$pid=$person->getXref();
		// print_pedigree_person() requires these globals.
		global $show_full, $box_width;
		$show_full=$this->show_full;
		$box_width=$this->box_width;
		//-- put a space between families on the last generation
		if ($count==$this->dgenerations-1) {
			if (isset($lastGenSecondFam)) echo "<br>";
			$lastGenSecondFam = true;
		}
		$this->loopcnt++;
		echo '<table id="table2_', $this->loopcnt, '" cellspacing="0" cellpadding="0" border="0">';
		echo '<tr>';
		echo '<td width="', ($bwidth-2), '">';
		$gencount = 0;
		$numkids = 0;
		$familycount = 0;
		$kids = 0;
		$famNum = 0;
		$lh = 0;
		$sfamilies=$person->getSpouseFamilies();
		$children = array();
		if ($count < $this->dgenerations) {
			//-- put all of the children in a common array
			foreach ($sfamilies as $family) {
				$famNum ++;
				$chs = $family->getChildren();
				foreach ($chs as $c=>$child) $children[] = $child;
			}
			$ct = count($children);

			if ($ct>0) {
				echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
				for ($i=0; $i<$ct; $i++) {
					//if (($i>0)&&($i<$ct-1)) $rowspan=1;
					/* @var $person2 Person */
					$person2 = $children[$i];
					$chil = $person2->getXref();
					echo "<tr>";
					echo "<td id=\"td_$chil\">";
					$kids = $this->print_descendency($person2, $count+1);
					$numkids += $kids;
					echo "</td>";

					//-- print the lines
					$twidth = 7;
					if ($ct==1) $twidth+=3;
					if ($ct>1) {
						if ($i==0) {
							//-- adjust for the number of kids
							$h= ((($bheight+7)*$kids) /2);
							echo "<td valign=\"bottom\"><img class=\"line1\" name=\"tvertline\" id=\"vline_$chil\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$h\" alt=\"\"></td>";
						} else if ($i==$ct-1) {
							$h= ((($bheight+7)*$kids) /2);
							if ($count<$this->dgenerations-1) {
								if ($this->show_spouse) $h-=6;
								else $h +=6;
							}

							echo "<td valign=\"top\"><img name=\"bvertline\" id=\"vline_$chil\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"".$h."\" alt=\"\"></td>";
						} else {
							echo "<td style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\"></td>";
						}
					}
					echo "</tr>";
				}
				echo "</table>";
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
		echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td>";
		print_pedigree_person($person);
		echo "</td><td><img class=\"line4\" src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\">";
		//----- Print the spouse
		if ($count==1 ) { 
			if ($this->show_spouse) {
				foreach ($sfamilies as $family) {
					if (!is_null($family)) {
						$spouse = $family->getSpouse($person);
						if ($spouse!=null) {
							echo "</td></tr><tr><td>";
							//-- shrink the box for the spouses
							$tempw = $bwidth;
							$temph = $bheight;
							$bwidth -= 10;
							$bheight -= 10;
							print_pedigree_person($spouse);
							$bwidth = $tempw;
							$bheight = $temph;
							$numkids += 0.95;
							echo "</td><td></td>";
						}
					}
				}
			}
		}
		echo "</td></tr></table>";
		echo '</td></tr>';
		echo '</table>';
		return $numkids;
	}
	
	/**
	* Prints pedigree of the person passed in. Which is the descendancy
	* @param mixed $pid ID of person to print the pedigree for
	* @access public
	*/
	function print_person_pedigree($person, $count) {
		global $SHOW_EMPTY_BOXES, $WT_IMAGES, $bhalfheight;

		if ($count>=$this->generations) return;
		if (!$person) return;

		//-- calculate how tall the lines should be
		$lh = ($bhalfheight+3) * pow(2, ($this->generations-$count-1));

		foreach ($person->getChildFamilies() as $family) {
			echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">";
			$height="100%";
			echo "<tr>";
			echo "<td valign=\"bottom\"><img class=\"line3\" name=\"pvline\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$lh\" alt=\"\"></td>";
			echo "<td><img class=\"line4\" src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\"></td>";
			echo "<td>";
			//-- print the father box
			print_pedigree_person($family->getHusband());
			echo "</td>";
			if ($family->getHusband()) {
				$ARID = $family->getHusband()->getXref();
				echo "<td id=\"td_".$ARID."\">";
				
				//-- recursively get the father's family
				$this->print_person_pedigree($family->getHusband(), $count+1);
				echo "</td>";
			}
			echo "</tr><tr>";
			echo "<td valign=\"top\"><img name=\"pvline\" src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"$lh\" alt=\"\"></td>";
			echo "<td><img class=\"line4\" src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\"></td>";
			echo "<td>";
			
			//-- print the mother box
			print_pedigree_person($family->getWife());
			echo "</td>";
			if ($family->getWife()) {
				$ARID = $family->getWife()->getXref();
				echo "<td id=\"td_".$ARID."\">";
				//-- recursively print the mother's family
				$this->print_person_pedigree($family->getWife(), $count+1);
				echo "</td>";
			}
			echo "</tr>";
			echo "</table>";
			break;
		}
	}
		/**
	 * Calculates number of generations a person has
	 *
	 * @param mixed $pid ID of person to see how far down the descendency goes
	 * @param mixed $depth Pass in 0 and it calculates how far down descendency goes
	 * @access public
	 * @return maxdc Amount of generations the descendency actually goes
	 */
	function max_descendency_generations($pid, $depth) {
		if ($depth > $this->generations) return $depth;
		$person = WT_Person::getInstance($pid);
		if (is_null($person)) return $depth;
		$maxdc = $depth;
		foreach ($person->getSpouseFamilies() as $family) {
			foreach ($family->getChildren() as $child) {
				$dc = $this->max_descendency_generations($child->getXref(), $depth+1);
				if ($dc >= $this->generations) return $dc;
				if ($dc > $maxdc) $maxdc = $dc;
			}
		}
		$maxdc++;
		if ($maxdc==1) $maxdc++;
		return $maxdc;
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
				'</h2><table class="t0" cellspacing="0" cellpadding="0" border="0"><tr><td valign="middle">';
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
