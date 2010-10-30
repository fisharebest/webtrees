<?php
// Controller for the Ancestry Page
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_ANCESTRY_CTRL_PHP', '');

require_once WT_ROOT.'includes/functions/functions_charts.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';

class AncestryController extends BaseController {
	var $pid = "";
	var $user = false;
	var $accept_success = false;
	var $show_cousins;
	var $rootid;
	var $name;
	var $addname;
	var $OLD_PGENS;
	var $chart_style;
	var $show_full;
	var $cellwidth;

	/**
	 * Initialization function
	 */
	function init() {
		global $USE_RIN, $MAX_ALIVE_AGE, $GEDCOM, $bwidth, $bheight, $pbwidth, $pbheight, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS;
		global $DEFAULT_PEDIGREE_GENERATIONS, $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $OLD_PGENS, $box_width, $Dbwidth, $Dbheight;
		global $show_full;

		// Extract form parameters
		$this->rootid        =safe_GET_xref('rootid');
		$this->show_full     =safe_GET('show_full',    array('0', '1'), $PEDIGREE_FULL_DETAILS);
		$this->show_cousins  =safe_GET('show_cousins', array('0', '1'), '0');
		$this->chart_style   =safe_GET_integer('chart_style',          0, 3, 0);
		$box_width           =safe_GET_integer('box_width',            50, 300, 100);
		$PEDIGREE_GENERATIONS=safe_GET_integer('PEDIGREE_GENERATIONS', 2, $MAX_PEDIGREE_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS);

		// This is passed as a global.  A parameter would be better...
		$show_full=$this->show_full;

		$OLD_PGENS = $PEDIGREE_GENERATIONS;

		// Validate form parameters
		$this->rootid = check_rootid($this->rootid);

		// -- size of the boxes
		$Dbwidth*=$box_width/100;
		$bwidth=$Dbwidth;
		if (!$this->show_full) {
			$bwidth = $bwidth / 1.5;
		}
		$bheight=$Dbheight;
		if (!$this->show_full) {
			$bheight = $bheight / 1.5;
		}

		$pbwidth = $bwidth+12;
		$pbheight = $bheight+14;

		$this->ancestry = Person::getInstance($this->rootid);
		$this->name     = $this->ancestry->getFullName();
		$this->addname  = $this->ancestry->getAddName();

		if (strlen($this->name)<30) $this->cellwidth="420";
		else $this->cellwidth=(strlen($this->name)*14);
	}

	/**
	 * print a child ascendancy
	 *
	 * @param string $pid individual Gedcom Id
	 * @param int $sosa child sosa number
	 * @param int $depth the ascendancy depth to show
	 */
	function print_child_ascendancy($pid, $sosa, $depth) {
		global $TEXT_DIRECTION, $OLD_PGENS, $WT_IMAGES, $Dindent, $SHOW_EMPTY_BOXES, $pidarr, $box_width;

		$person = Person::getInstance($pid);
		// child
		echo "<li>";
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><a name=\"sosa".$sosa."\"></a>";
		$new=($pid=="" or !isset($pidarr["$pid"]));
		if ($sosa==1) echo "<img src=\"".$WT_IMAGES["spacer"]."\" height=\"3\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>";
		else {
			echo "<img src=\"".$WT_IMAGES["spacer"]."\" height=\"3\" width=\"2\" border=\"0\" alt=\"\" />";
			echo "<img src=\"".$WT_IMAGES["hline"]."\" height=\"3\" width=\"".($Dindent-2)."\" border=\"0\" alt=\"\" /></td><td>";
		}
		print_pedigree_person($pid, 1);
		echo "</td>";
		echo "<td>";
		if ($TEXT_DIRECTION=="ltr") {
			$label = i18n::translate('Ancestry chart').": ".$pid;
		} else {
			$label = $pid." :".i18n::translate('Ancestry chart');
		}
		if ($sosa>1) print_url_arrow($pid, "?rootid={$pid}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;show_full={$this->show_full}&amp;box_width={$box_width}&amp;chart_style={$this->chart_style}", $label, 3);
		echo "</td>";
		echo "<td class=\"details1\">&nbsp;<span dir=\"ltr\" class=\"person_box". (($sosa==1)?"NN":(($sosa%2)?"F":"")) . "\">&nbsp;$sosa&nbsp;</span>&nbsp;";
		echo "</td><td class=\"details1\">";
		$relation ="";
		if (!$new) $relation = "<br />[=<a href=\"#sosa".$pidarr["$pid"]."\">".$pidarr["$pid"]."</a> - ".get_sosa_name($pidarr["$pid"])."]";
		else $pidarr["$pid"]=$sosa;
		echo get_sosa_name($sosa).$relation;
		echo "</td>";
		echo "</tr></table>";

		if (is_null($person)) {
			echo "</li>";
			return;
		}
		// parents
		$famids = $person->getChildFamilies();
		$parents = false;
		$famrec = "";
		$famid = "";
		foreach ($famids as $famid=>$family) {
			if (!is_null($family)) {
				$famrec = $family->getGedcomRecord();
				$parents = find_parents_in_record($famrec);
				if ($parents) break;
			}
		}

		if (($parents || $SHOW_EMPTY_BOXES) && $new && $depth>0) {
			// print marriage info
			echo "<span class=\"details1\" style=\"white-space: nowrap;\" >";
			echo "<img src=\"".$WT_IMAGES["spacer"]."\" height=\"2\" width=\"$Dindent\" border=\"0\" align=\"middle\" alt=\"\" /><a href=\"javascript: ".i18n::translate('View Family')."\" onclick=\"expand_layer('sosa_".$sosa."'); return false;\" class=\"top\"><img id=\"sosa_".$sosa."_img\" src=\"".$WT_IMAGES["minus"]."\" align=\"middle\" hspace=\"0\" vspace=\"3\" border=\"0\" alt=\"".i18n::translate('View Family')."\" /></a> ";
			echo "&nbsp;<span class=\"person_box\">&nbsp;".($sosa*2)."&nbsp;</span>&nbsp;".i18n::translate('and');
			echo "&nbsp;<span class=\"person_boxF\">&nbsp;".($sosa*2+1)." </span>&nbsp;";
			if (!empty($family)) {
				$marriage = $family->getMarriage();
				if ($marriage->canShow()) $marriage->print_simple_fact(); else echo i18n::translate('Private');
			}
			echo "</span>";
			// display parents recursively
			echo "<ul style=\"list-style: none; display: block;\" id=\"sosa_$sosa\">";
			$this->print_child_ascendancy($parents["HUSB"], $sosa*2, $depth-1);
			$this->print_child_ascendancy($parents["WIFE"], $sosa*2+1, $depth-1);
			echo "</ul>";
		}
		echo "</li>";
	}
}
