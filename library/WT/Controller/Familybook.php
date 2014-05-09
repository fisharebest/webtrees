<?php
//	Controller for the familybook chart
//
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Controller_Familybook extends WT_Controller_Chart {
	// Data for the view
	public $pid	       =null;
	public $show_full  =null;
	public $show_spouse=null;
	public $descent    =null;
	public $generations=null;
	public $box_width  =null;
	public $rootid     =null;

	// Data for the controller
	private $dgenerations=null;
	public function __construct() {
		parent::__construct();

		$PEDIGREE_FULL_DETAILS      =get_gedcom_setting(WT_GED_ID, 'PEDIGREE_FULL_DETAILS');
		$MAX_DESCENDANCY_GENERATIONS=get_gedcom_setting(WT_GED_ID, 'MAX_DESCENDANCY_GENERATIONS');

		// Extract the request parameters
		$this->show_full   = WT_Filter::getInteger('show_full',   0, 1, $PEDIGREE_FULL_DETAILS);
		$this->show_spouse = WT_Filter::getInteger('show_spouse', 0, 1);
		$this->descent     = WT_Filter::getInteger('descent',     0, 9, 5);
		$this->generations = WT_Filter::getInteger('generations', 2, $MAX_DESCENDANCY_GENERATIONS, 2);
		$this->box_width   = WT_Filter::getInteger('box_width',   50, 300, 100);

		// Box sizes are set globally in the theme.  Modify them here.
		global $bwidth, $bheight, $cbwidth, $cbheight, $Dbwidth, $bhalfheight, $Dbheight;
		$Dbwidth =$this->box_width * $bwidth  / 100;
		$Dbheight=$this->box_width * $bheight / 100;
		$bwidth  =$Dbwidth;
		$bheight =$Dbheight;

		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $this->box_width * $cbwidth  / 100;
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
		$this->dgenerations = $this->max_descendency_generations($this->pid, 0);
		if ($this->dgenerations<1) $this->dgenerations=1;
	}

	/**
	 * Prints descendency of passed in person
	 */
	function print_descendency($person, $count) {
		global $TEXT_DIRECTION, $WT_IMAGES, $bwidth, $bheight, $bhalfheight;
		global $show_full, $box_width; // print_pedigree_person() requires these globals.
		if ($count>$this->dgenerations) return 0;
		$show_full=$this->show_full;
		$box_width=$this->box_width;
		echo '<table>';
		echo '<tr>';
		echo '<td width="', ($bwidth), '">';
		$gencount = 0;
		$numkids = 0;
		$familycount = 0;
		$kids = 0;
		$famNum = 0;
		$lh = 0;
		// if real person load child array
		if ($person) {
			$sfamilies=$person->getSpouseFamilies();
			$children = array();
			//count is position from center to left, dgenerations is number of generations
			if ($count < $this->dgenerations) {
				//-- put all of the children in a common array
				foreach ($sfamilies as $family) {
					$famNum ++;
					$chs = $family->getChildren();
					foreach ($chs as $c=>$child) $children[] = $child;
				}
				$ct = count($children);
			}
		} else {
				$ct = 0; // set to 0 for empty boxes
		}
		if ($count < $this->dgenerations) {
			if ($ct==0 ) { // empty boxes
				echo '<table>';
				for ($i=0; $i<1; $i++) { //set to 2 for two child boxes
					echo '<tr>',
						 '<td>';
					 $person2=null;
					 $kids = $this->print_descendency($person2, $count+1);
					$numkids += $kids;
					echo '</td>';
					//Adjust for lines
					if ($i==0) {
						//-- adjust for the first column on left
						$h= round(((($bheight)*$kids)/2)-1);
						//-- adjust for other vertical columns
						if ($kids>1) $h = ((($kids-1)*4)+$h);
							//echo '<td class="tdbot">',
								// '<img class="tvertline" src="',$WT_IMAGES["vline"],'" width="3" height="',$h,'" alt=""></td>';
					} else if ($i==1) {
							//-- adjust for the first column on left
							$h= round(((($bheight)*$kids)/2)+10);
							//-- adjust for other vertical columns
							if ($kids>1) $h = ((($kids-1)*4)+$h);

							//echo '<td class="tdtop">',
							//	 '<img class="bvertline"  src="',$WT_IMAGES["vline"],'" width="3" height="',$h,'" alt=""></td>';
					} else {
						echo '<td style="background: url(',$WT_IMAGES["vline"],');">',
							 '<img class="spacer" src="',$WT_IMAGES["spacer"],'" alt=""></td>';
					}
					echo '</tr>';
				}
				echo '</table>';
			}
			if ($ct>0) { // real people
				echo '<table>';
				for ($i=0; $i<$ct; $i++) {
					$person2 = $children[$i];
					$chil = $person2->getXref();
					echo '<tr>',
						 '<td>';
						 //recursive call to print descendents
					$kids = $this->print_descendency($person2, $count+1);
					$numkids += $kids;
					echo '</td>';
					//-- print the lines
					$twidth = 7;
					if ($ct==1) $twidth+=3;
					if ($ct>1) {
						if ($i==0) {
							//-- adjust for the first column on left
							//$h= round(((($bheight)*$kids)/2)-1);
							$h= round(((($bheight)*$kids)+8)/2);  // Assumes border = 1 and padding = 3
							//-- adjust for other vertical columns
							if ($kids>1) $h = ((($kids-1)*4)+$h);
							echo '<td class="tdbot">',
								 '<img class="tvertline" id="vline_',$chil,'" src="',$WT_IMAGES["vline"],'"  height="',$h-1,'" alt=""></td>';
						} else if ($i==$ct-1) {
							//-- adjust for the first column on left
							$h= round(((($bheight)*$kids)+8)/2);
							//-- adjust for other vertical columns
							if ($kids>1) $h = ((($kids-1)*4)+$h);
							echo '<td class="tdtop">',
								 '<img class="bvertline" id="vline_',$chil,'" src="',$WT_IMAGES["vline"],'" height="',$h+1,'" alt=""></td>';
						} else {
							echo '<td style="background: url(',$WT_IMAGES["vline"],');">',
								 '<img class="spacer" src="',$WT_IMAGES["spacer"],'" alt=""></td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</td>';
			echo '<td width="',$bwidth,'">';
		}

		if ($numkids==0) {
			$numkids = 1;
		}
		echo '<table><tr><td>';
		if ($person) {
			print_pedigree_person($person);
					echo '</td><td>',
			 '<img class="line2" src="',$WT_IMAGES["hline"],'" width="8" height="3" alt="">';
		} else  if ($kids==1 ) 
			echo '<div style="width:',$bwidth+19,'px; height:',$bheight+8,'px;"></div>',
				 '</td><td>';

		//----- Print the spouse
		if ($count==1 ) {
			if ($this->show_spouse) {
				foreach ($sfamilies as $family) {
					$spouse = $family->getSpouse($person);
					echo '</td></tr><tr><td>';
					//-- shrink the box for the spouses
					$tempw = $bwidth;
					$temph = $bheight;
					$bwidth -= 10;
					$bheight -= 10;
					print_pedigree_person($spouse);
					$bwidth = $tempw;
					$bheight = $temph;
					$numkids += 0.95;
					echo '</td><td></td>';
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
	 */
	function print_person_pedigree($person, $count) {
		global $WT_IMAGES, $bheight, $bwidth, $bhalfheight;
		if ($count>=$this->generations) return;
		//if (!$person) return;
		$genoffset = $this->generations;  // handle pedigree n generations lines
		//-- calculate how tall the lines should be
		$lh = ($bhalfheight+4) * pow(2, ($genoffset-$count-1));
		//
		//Prints empty table columns for children w/o parents up to the max generation
		//This allows vertical line spacing to be consistent
		//
		if (count($person->getChildFamilies())==0) {
			echo '<table>';
			$this->printEmptyBox($bwidth, $bheight);

			//-- recursively get the father’s family
			$this->print_person_pedigree($person, $count+1);
			echo '</td>',
				 '<td>',
				 '</tr>';
			$this->printEmptyBox($bwidth, $bheight);

			//-- recursively get the mother’s family
			$this->print_person_pedigree($person, $count+1);
			echo '</td>',
				 '<td>',
				 '</tr></table>';
		}

		//Empty box section done, now for regular pedigree
		foreach ($person->getChildFamilies() as $family) {
			echo '<table>',
				 '<tr>',
				 '<td class="tdbot">',
				 '<img class="line3 pvline"  src="',$WT_IMAGES["vline"],'" height="',$lh-1,'" alt=""></td>',
				 '<td>',
				 '<img class="line4" src="',$WT_IMAGES["hline"],'" height="3" alt=""></td>',
				 '<td>';
			//-- print the father box
			print_pedigree_person($family->getHusband());
			echo '</td>';
			if ($family->getHusband()) {
				echo '<td>';
				//-- recursively get the father’s family
				$this->print_person_pedigree($family->getHusband(), $count+1);
				echo '</td>';
			} else {
				echo '<td>';
				for ($i=$count; $i<$genoffset-1; $i++) {
					echo '<table>';
					$this->printEmptyBox($bwidth, $bheight);
					echo '</tr>';
					$this->printEmptyBox($bwidth, $bheight);
					echo '</tr></table>';
				}
			}
			echo '</tr><tr>',
				 '<td class="tdtop"><img class="pvline" src="',$WT_IMAGES["vline"],'" height="',$lh+1,'" alt=""></td>',
				 '<td><img class="line4" src="',$WT_IMAGES["hline"],'" height="3" alt=""></td>',
				 '<td>';
			//-- print the mother box
			print_pedigree_person($family->getWife());
			echo '</td>';
			if ($family->getWife()) {
				echo '<td>';
				//-- recursively print the mother’s family
				$this->print_person_pedigree($family->getWife(), $count+1);
				echo '</td>';
			} else {
				echo '<td>';
				for ($i=$count; $i<$genoffset-1; $i++) {
					echo '<table>';
					$this->printEmptyBox($bwidth, $bheight);
					echo '</tr>';
					$this->printEmptyBox($bwidth, $bheight);
					echo '</tr></table>';
				}
			}
			echo '</tr>',
				 '</table>';
			break;
		}
	}

	/**
	 * Calculates number of generations a person has
	 */
	function max_descendency_generations($pid, $depth) {
		if ($depth > $this->generations) return $depth;
		$person = WT_Individual::getInstance($pid);
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
	/**
	 * Print empty box
	 */
	function printEmptyBox($bwidth, $bheight){
	echo '<tr>',
		 '<td>',
		 '<div style="width:',$bwidth+16,'px; height:',$bheight+8,'px;"></div>',
		 '</td>',
		 '<td>';
	}
	function print_family_book($person, $descent) {
		global $firstrun;
		if ($descent==0 || !$person->canShowName()) {
			return;
		}
		$families=$person->getSpouseFamilies();
		if (count($families)>0 || empty($firstrun)) {
			$firstrun=true;
			echo
				'<h3>',
					/* I18N: A title/heading. %s is an individual’s name */ WT_I18N::translate('Family of %s', $person->getFullName()),
				'</h3>
				<table class="t0"><tr><td class="tdmid">';
			$this->dgenerations = $this->generations;
			$this->print_descendency($person, 1);
			echo '</td><td class="tdmid">';
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
