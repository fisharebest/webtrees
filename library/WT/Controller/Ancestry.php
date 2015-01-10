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
 * Class WT_Controller_Ancestry - Controller for the ancestry chart
 */
class WT_Controller_Ancestry extends WT_Controller_Chart {
	var $pid = '';
	var $user = false;
	var $show_cousins;
	var $rootid;
	var $name;
	var $addname;
	var $OLD_PGENS;
	var $chart_style;
	var $show_full;
	var $cellwidth;

	/**
	 * Startup activity
	 */
	function __construct() {
		global $bwidth, $bheight, $cbwidth, $cbheight, $pbwidth, $pbheight, $PEDIGREE_FULL_DETAILS;
		global $DEFAULT_PEDIGREE_GENERATIONS, $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $OLD_PGENS, $box_width, $Dbwidth, $Dbheight;
		global $show_full;

		parent::__construct();

		// Extract form parameters
		$this->show_full      = WT_Filter::getInteger('show_full',            0, 1, $PEDIGREE_FULL_DETAILS);
		$this->show_cousins   = WT_Filter::getInteger('show_cousins',         0, 1);
		$this->chart_style    = WT_Filter::getInteger('chart_style',          0, 3);
		$box_width            = WT_Filter::getInteger('box_width',            50, 300, 100);
		$PEDIGREE_GENERATIONS = WT_Filter::getInteger('PEDIGREE_GENERATIONS', 2, $MAX_PEDIGREE_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS);

		// This is passed as a global.  A parameter would be better...
		$show_full=$this->show_full;

		$OLD_PGENS = $PEDIGREE_GENERATIONS;

		// -- size of the detailed boxes based upon optional width parameter
		$Dbwidth=($box_width*$bwidth)/100;
		$Dbheight=($box_width*$bheight)/100;
		$bwidth=$Dbwidth;
		$bheight=$Dbheight;

		// -- adjust size of the compact box
		if (!$this->show_full) {
			$bwidth = $cbwidth;
			$bheight = $cbheight;
		}

		$pbwidth = $bwidth+12;
		$pbheight = $bheight+14;

		if ($this->root && $this->root->canShowName()) {
			$this->setPageTitle(
				/* I18N: %s is an individual’s name */
				WT_I18N::translate('Ancestors of %s', $this->root->getFullName())
			);
		} else {
			$this->setPageTitle(WT_I18N::translate('Ancestors'));
		}

		if (strlen($this->name)<30) $this->cellwidth="420";
		else $this->cellwidth=(strlen($this->name)*14);
	}

	/**
	 * print a child ascendancy
	 *
	 * @param         $person
	 * @param integer $sosa  child sosa number
	 * @param integer $depth the ascendancy depth to show
	 */
	public function printChildAscendancy($person, $sosa, $depth) {
		global $OLD_PGENS, $WT_IMAGES, $Dindent, $pidarr, $box_width;

		if ($person) {
			$pid=$person->getXref();
			$label=WT_I18N::translate('Ancestors of %s', $person->getFullName());
		} else {
			$pid='';
			$label='';
		}
		// child
		echo '<li>';
		echo '<table border="0" cellpadding="0" cellspacing="0"><tr><td><a name="sosa', $sosa, '"></a>';
		if ($sosa==1) {
			echo '<img src="', $WT_IMAGES['spacer'], '" height="3" width="', $Dindent, '" alt=""></td><td>';
		} else {
			echo '<img src="', $WT_IMAGES['spacer'], '" height="3" width="2" alt="">';
			echo '<img src="', $WT_IMAGES['hline'], '" height="3" width="', ($Dindent-2), '" alt=""></td><td>';
		}
		print_pedigree_person($person);
		echo '</td>';
		echo '<td>';
		if ($sosa>1) {
			print_url_arrow('?rootid='.$pid.'&amp;PEDIGREE_GENERATIONS='.$OLD_PGENS.'&amp;show_full='.$this->show_full.'&amp;box_width='.$box_width.'&amp;chart_style='.$this->chart_style.'&amp;ged='.WT_GEDURL, $label, 3);
		}
		echo '</td>';
		echo '<td class="details1">&nbsp;<span dir="ltr" class="person_box'. (($sosa==1)?'NN':(($sosa%2)?'F':'')) . '">&nbsp;', $sosa, '&nbsp;</span>&nbsp;';
		echo '</td><td class="details1">';
		$relation ='';
		$new=($pid=='' or !isset($pidarr[$pid]));
		if (!$new) $relation = '<br>[=<a href="#sosa'.$pidarr[$pid].'">'.$pidarr[$pid].'</a> - '.get_sosa_name($pidarr[$pid]).']';
		else $pidarr[$pid]=$sosa;
		echo get_sosa_name($sosa).$relation;
		echo '</td>';
		echo '</tr></table>';

		if (is_null($person)) {
			echo '</li>';
			return;
		}
		// parents
		$family=$person->getPrimaryChildFamily();

		if ($family && $new && $depth>0) {
			// print marriage info
			echo '<span class="details1" style="white-space: nowrap;" >';
			echo '<img src="', $WT_IMAGES['spacer'], '" height="2" width="', $Dindent, '" align="middle" alt=""><a href="#" onclick="return expand_layer(\'sosa_', $sosa, '\');" class="top"><i id="sosa_', $sosa, '_img" class="icon-minus" title="', WT_I18N::translate('View family'), '"></i></a>';
			echo '&nbsp;<span dir="ltr" class="person_box">&nbsp;', ($sosa*2), '&nbsp;</span>&nbsp;', WT_I18N::translate('and');
			echo '&nbsp;<span dir="ltr" class="person_boxF">&nbsp;', ($sosa*2+1), '&nbsp;</span>&nbsp;';
			if ($family->canShow()) {
				foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
					echo ' <a href="', $family->getHtmlUrl(), '" class="details1">', $fact->summary(), '</a>';
				}
			}
			echo '</span>';
			// display parents recursively - or show empty boxes
			echo '<ul style="list-style: none; display: block;" id="sosa_', $sosa, '">';
			$this->printChildAscendancy($family->getHusband(), $sosa*2, $depth-1);
			$this->printChildAscendancy($family->getWife(), $sosa*2+1, $depth-1);
			echo '</ul>';
		}
		echo '</li>';
	}
}
