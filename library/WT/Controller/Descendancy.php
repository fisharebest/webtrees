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

use Rhumsaa\Uuid\Uuid;

/**
 * Class WT_Controller_Descendancy - Controller for the descendancy chart
 */
class WT_Controller_Descendancy extends WT_Controller_Chart {
	var $descPerson = null;

	var $diffindi = null;
	var $NAME_LINENUM = 1;
	var $canedit = false;
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $show_full;
	var $chart_style;
	var $generations;
	var $box_width;
	var $Dbwidth;
	var $Dbheight;
	var $pbwidth;
	var $pbheight;
	// d'Aboville numbering system [ http://www.saintclair.org/numbers/numdob.html ]
	var $dabo_num=array();
	var $dabo_sex=array();
	var $name;
	var $cellwidth;
	var $show_cousins;

	/**
	 * Create the descendancy controller
	 */
	function __construct() {
		global $bwidth, $bheight, $cbwidth, $cbheight, $pbwidth, $pbheight, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS, $show_full;

		parent::__construct();

		// Extract parameters from form
		$this->show_full   = WT_Filter::getInteger('show_full',   0, 1, $PEDIGREE_FULL_DETAILS);
		$this->chart_style = WT_Filter::getInteger('chart_style', 0, 3, 0);
		$this->generations = WT_Filter::getInteger('generations', 2, $MAX_DESCENDANCY_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS);
		$this->box_width   = WT_Filter::getInteger('box_width',   50, 300, 100);

		// This is passed as a global.  A parameter would be better...
		$show_full=$this->show_full;

		if (!isset($this->personcount)) $this->personcount = 1;

		// -- size of the detailed boxes based upon optional width parameter
		$Dbwidth  = ($this->box_width*$bwidth)/100;
		$Dbheight = ($this->box_width*$bheight)/100;
		$bwidth   = $Dbwidth;
		$bheight  = $Dbheight;

		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth  = $cbwidth;
			$bheight = $cbheight;
		}

		$pbwidth  = $bwidth+12;
		$pbheight = $bheight+14;

		// Validate form variables
		if (strlen($this->name)<30) {
			$this->cellwidth=420;
		} else {
			$this->cellwidth=(strlen($this->name)*14);
		}

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				WT_I18N::translate('Descendants of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(WT_I18N::translate('Descendants'));
		}
	}

	/**
	 * Print a child family
	 *
	 * @param WT_Individual $person
	 * @param integer       $depth the descendancy depth to show
	 * @param string        $label
	 * @param string        $gpid
	 *
	 * @return void
	 */
	public function printChildFamily(WT_Individual $person, $depth, $label='1.', $gpid='') {

		if ($depth<2) return;
		foreach ($person->getSpouseFamilies() as $family) {
			print_sosa_family($family->getXref(), '', -1, $label, $person->getXref(), $gpid);
			$i=1;
			foreach ($family->getChildren() as $child) {
				$this->printChildFamily($child, $depth-1, $label.($i++).'.', $person->getXref());
			}
		}
	}

	/**
	 * print a child descendancy
	 *
	 * @param WT_Individual $person
	 * @param integer       $depth the descendancy depth to show
	 *
	 * @return void
	 */
	public function printChildDescendancy(WT_Individual $person, $depth) {
		global $WT_IMAGES, $Dindent;

		echo "<li>";
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
		if ($depth==$this->generations) echo "<img src=\"".$WT_IMAGES["spacer"]."\" height=\"3\" width=\"$Dindent\" alt=\"\"></td><td>";
		else {
			echo "<img src=\"".$WT_IMAGES["spacer"]."\" height=\"3\" width=\"3\" alt=\"\">";
			echo "<img src=\"".$WT_IMAGES["hline"]."\" height=\"3\" width=\"".($Dindent-3)."\" alt=\"\"></td><td>";
		}
		print_pedigree_person($person);
		echo '</td>';

		// check if child has parents and add an arrow
		echo '<td>&nbsp;</td>';
		echo '<td>';
		foreach ($person->getChildFamilies() as $cfamily) {
			foreach ($cfamily->getSpouses() as $parent) {
				print_url_arrow('?rootid='.$parent->getXref().'&amp;generations='.$this->generations.'&amp;chart_style='.$this->chart_style.'&amp;show_full='.$this->show_full.'&amp;box_width='.$this->box_width.'&amp;ged='.WT_GEDURL, WT_I18N::translate('Start at parents'), 2);
				// only show the arrow for one of the parents
				break;
			}
		}

		// d'Aboville child number
		$level =$this->generations-$depth;
		if ($this->show_full) echo '<br><br>&nbsp;';
		echo '<span dir="ltr">'; //needed so that RTL languages will display this properly
		if (!isset($this->dabo_num[$level])) $this->dabo_num[$level]=0;
		$this->dabo_num[$level]++;
		$this->dabo_num[$level+1]=0;
		$this->dabo_sex[$level]=$person->getSex();
		for ($i=0; $i<=$level;$i++) {
			$isf=$this->dabo_sex[$i];
			if ($isf=="M") $isf="";
			if ($isf=="U") $isf="NN";
			echo "<span class=\"person_box".$isf."\">&nbsp;".$this->dabo_num[$i]."&nbsp;</span>";
			if ($i<$level) echo ".";
		}
		echo "</span>";
		echo "</td></tr>";
		echo "</table>";
		echo "</li>";

		// loop for each spouse
		foreach ($person->getSpouseFamilies() as $family) {
			$this->printFamilyDescendancy($person, $family, $depth);
		}
	}

	/**
	 * print a family descendancy
	 *
	 * @param WT_Individual $person
	 * @param WT_Family     $family
	 * @param integer       $depth the descendancy depth to show
	 *
	 * @return void
	 */
	private function printFamilyDescendancy(WT_Individual $person, WT_Family $family, $depth) {
		global $WT_IMAGES, $Dindent;

		$uid = Uuid::uuid4(); // create a unique ID
		// print marriage info
		echo '<li>';
		echo '<img src="', $WT_IMAGES['spacer'], '" height="2" width="', ($Dindent+4), '" alt="">';
		echo '<span class="details1" style="white-space:nowrap;">';
		echo "<a href=\"#\" onclick=\"expand_layer('".$uid."'); return false;\" class=\"top\"><i id=\"".$uid."_img\" class=\"icon-minus\" title=\"".WT_I18N::translate('View family')."\"></i></a>";
		if ($family->canShow()) {
			foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
				echo ' <a href="', $family->getHtmlUrl(), '" class="details1">', $fact->summary(), '</a>';
			}
		}
		echo '</span>';

		// print spouse
		$spouse=$family->getSpouse($person);
		echo '<ul style="list-style:none; display:block;" id="'.$uid.'">';
		echo '<li>';
		echo '<table border="0" cellpadding="0" cellspacing="0"><tr><td>';
		print_pedigree_person($spouse);
		echo '</td>';

		// check if spouse has parents and add an arrow
		echo '<td>&nbsp;</td>';
		echo '<td>';
		if ($spouse) {
			foreach ($spouse->getChildFamilies() as $cfamily) {
				foreach ($cfamily->getSpouses() as $parent) {
					print_url_arrow('?rootid='.$parent->getXref().'&amp;generations='.$this->generations.'&amp;chart_style='.$this->chart_style.'&amp;show_full='.$this->show_full.'&amp;box_width='.$this->box_width.'&amp;ged='.WT_GEDURL, WT_I18N::translate('Start at parents'), 2);
					// only show the arrow for one of the parents
					break;
				}
			}
		}
		if ($this->show_full) echo '<br><br>&nbsp;';
		echo '</td></tr>';

		// children
		$children = $family->getChildren();
		echo '<tr><td colspan="3" class="details1" >&nbsp;&nbsp;';
		if ($children) {
			echo WT_Gedcom_Tag::getLabel('NCHI').': '.count($children);
		} else {
			// Distinguish between no children (NCHI 0) and no recorded
			// children (no CHIL records)
			if (strpos($family->getGedcom(), '\n1 NCHI 0')) {
				echo WT_Gedcom_Tag::getLabel('NCHI').': '.count($children);
			} else {
				echo WT_I18N::translate('No children');
			}
		}
		echo '</td></tr></table>';
		echo '</li>';
		if ($depth>1) foreach ($children as $child) {
			$this->printChildDescendancy($child, $depth-1);
		}
		echo '</ul>';
		echo '</li>';
	}

	/**
	 * Find all the individuals that are descended from an individual.
	 *
	 * @param WT_Individual   $person
	 * @param integer         $n
	 * @param WT_Individual[] $array
	 *
	 * @return WT_Individual[]
	 */
	public function individualDescendancy(WT_Individual $person, $n, $array) {
		if ($n < 1) {
			return $array;
		}
		$array[$person->getXref()]=$person;
		foreach ($person->getSpouseFamilies() as $family) {
			$spouse = $family->getSpouse($person);
			if ($spouse) {
				$array[$spouse->getXref()] = $spouse;
			}
			foreach ($family->getChildren() as $child) {
				$array = $this->individualDescendancy($child, $n-1, $array);
			}
		}
		return $array;
	}

	/**
	 * Find all the families that are descended from an individual.
	 *
	 * @param WT_Individual $person
	 * @param integer       $n
	 * @param WT_Family[]   $array
	 *
	 * @return WT_Family[]
	 */
	public function familyDescendancy($person, $n, $array) {
		if ($n < 1) {
			return $array;
		}
		foreach ($person->getSpouseFamilies() as $family) {
			$array[$family->getXref()]=$family;
			foreach ($family->getChildren() as $child) {
				$array = $this->familyDescendancy($child, $n-1, $array);
			}
		}
		return $array;
	}
}
