<?php
/**
 * Controller for the Descendancy Page
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009	PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Charts
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_DESCENDANCY_PHP', '');

require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

require_once WT_ROOT.'includes/classes/class_person.php';

// -- array of GEDCOM elements that will be found but should not be displayed
$nonfacts[] = "FAMS";
$nonfacts[] = "FAMC";
$nonfacts[] = "MAY";
$nonfacts[] = "BLOB";
$nonfacts[] = "CHIL";
$nonfacts[] = "HUSB";
$nonfacts[] = "WIFE";
$nonfacts[] = "RFN";
$nonfacts[] = "";
$nonfamfacts[] = "UID";
$nonfamfacts[] = "";
/**
 * Main controller class for the individual page.
 */
class DescendancyControllerRoot extends BaseController {
	var $pid = "";
	var $descPerson = null;

	var $diffindi = null;
	var $NAME_LINENUM = 1;
	var $accept_success = false;
	var $visibility = "visible";
	var $position = "relative";
	var $display = "block";
	var $canedit = false;
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $show_full;
	var $chart_style;
	var $sexarray = array();
	var $generations;
	var $personcount;
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
	 * constructor
	 */
	function DescendancyRootController() {
		parent::BaseController();
	}
	/**
	 * Initialization function
	 */
	function init() {
	global $USE_RIN, $MAX_ALIVE_AGE, $bwidth, $bheight, $pbwidth, $pbheight, $GEDCOM, $PEDIGREE_FULL_DETAILS, $MAX_DESCENDANCY_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS, $show_full;

	$this->sexarray["M"] = i18n::translate('Male');
	$this->sexarray["F"] = i18n::translate('Female');
	$this->sexarray["U"] = i18n::translate('unknown');

	// Extract parameters from form
	$this->pid        =safe_GET_xref('pid');
	$this->show_full  =safe_GET('show_full', array('0', '1'), $PEDIGREE_FULL_DETAILS);
	$this->chart_style=safe_GET_integer('chart_style', 0, 3, 0);
	$this->generations=safe_GET_integer('generations', 2, $MAX_DESCENDANCY_GENERATIONS, $DEFAULT_PEDIGREE_GENERATIONS);
	$this->box_width  =safe_GET_integer('box_width',   50, 300, 100);

	// This is passed as a global.  A parameter would be better...
	$show_full=$this->show_full;

	if (!isset($this->view)) $this->view="";
	if (!isset($this->personcount)) $this->personcount = 1;

	$this->Dbwidth*=$this->box_width/100;

	if (!$this->show_full) {
		$bwidth *= $this->box_width / 150;
	} else {
		$bwidth*=$this->box_width/100;
	}

	if (!$this->show_full) {
		$bheight = $bheight / 1.5;
	}

	$pbwidth = $bwidth+12;
	$pbheight = $bheight+14;

	$this->show_changes=safe_GET('show_changes');
	$this->action      =safe_GET('action');

	// Validate form variables
	$this->pid=check_rootid($this->pid);

	if (strlen($this->name)<30) $this->cellwidth="420";
	else $this->cellwidth=(strlen($this->name)*14);

	$this->descPerson = Person::getInstance($this->pid);
	$this->name=$this->descPerson->getFullName();

	//-- if the person is from another gedcom then forward to the correct site
	/*
	if ($this->indi->isRemote()) {
		header('Location: '.encode_url(decode_url($this->indi->getLinkUrl(), false)));
		exit;
	}
	*/
	if (!$this->isPrintPreview()) {
		$this->visibility = "hidden";
		$this->position = "absolute";
		$this->display = "none";
	}
	}

	/**
	 * print a child family
	 *
	 * @param string $pid individual Gedcom Id
	 * @param int $depth the descendancy depth to show
	 */
	function print_child_family(&$person, $depth, $label="1.", $gpid="") {
		global $personcount;

		if (is_null($person)) return;
		$families = $person->getSpouseFamilies();
		if ($depth<1) return;
		foreach($families as $famid => $family) {
			print_sosa_family($family->getXref(), "", -1, $label, $person->getXref(), $gpid, $personcount);
			$personcount++;
			$children = $family->getChildren();
			$i=1;
			foreach ($children as $child) {
				$this->print_child_family($child, $depth-1, $label.($i++).".", $person->getXref());
			}
		}
	}

/**
 * print a child descendancy
 *
 * @param string $pid individual Gedcom Id
 * @param int $depth the descendancy depth to show
 */
function print_child_descendancy(&$person, $depth) {
	global $WT_IMAGE_DIR, $WT_IMAGES, $Dindent;
	global $personcount;

	if (is_null($person)) return;
	//print_r($person);
	print "<li>";
	print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
	if ($depth==$this->generations) print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" height=\"3\" width=\"$Dindent\" border=\"0\" alt=\"\" /></td><td>\n";
	else {
		print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" height=\"3\" width=\"3\" border=\"0\" alt=\"\" />";
		print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]."\" height=\"3\" width=\"".($Dindent-3)."\" border=\"0\" alt=\"\" /></td><td>\n";
	}
	print_pedigree_person($person->getXref(), 1, $this->view!="preview",'',$personcount);
	print "</td>";

	// check if child has parents and add an arrow
	print "<td>&nbsp;</td>";
	print "<td>";
	$sfamids = $person->getChildFamilies();
	foreach($sfamids as $famid => $family) {
		$parents = find_parents($famid);
		if ($parents) {
			$parid=$parents["HUSB"];
			if ($parid=="") $parid=$parents["WIFE"];
			if ($parid!="") {
				print_url_arrow($parid.$personcount.$person->getXref(), encode_url("?pid={$parid}&generations={$this->generations}&chart_style={$this->chart_style}&show_full={$this->show_full}&box_width={$this->box_width}"), i18n::translate('Start at parents'), 2);
				$personcount++;
			}
		}
	}

	// d'Aboville child number
	$level =$this->generations-$depth;
	if ($this->show_full) print "<br /><br />&nbsp;";
	print "<span dir=\"ltr\">"; //needed so that RTL languages will display this properly
	if (!isset($this->dabo_num[$level])) $this->dabo_num[$level]=0;
	$this->dabo_num[$level]++;
	$this->dabo_num[$level+1]=0;
	$this->dabo_sex[$level]=$person->getSex();
	for ($i=0; $i<=$level;$i++) {
		$isf=$this->dabo_sex[$i];
		if ($isf=="M") $isf="";
		if ($isf=="U") $isf="NN";
		print "<span class=\"person_box".$isf."\">&nbsp;".$this->dabo_num[$i]."&nbsp;</span>";
		if ($i<$level) echo ".";
	}
	print "</span>";
	print "</td></tr>";
	print "</table>";
	print "</li>\r\n";

	// loop for each spouse
	$sfam = $person->getSpouseFamilies();
	foreach ($sfam as $famid => $family) {
		$personcount++;
		$this->print_family_descendancy($person, $family, $depth);
	}
}

/**
 * print a family descendancy
 *
 * @param string $pid individual Gedcom Id
 * @param Family $famid family record
 * @param int $depth the descendancy depth to show
 */
function print_family_descendancy(&$person, &$family, $depth) {
	global $GEDCOM, $WT_IMAGE_DIR, $WT_IMAGES, $Dindent, $personcount;

	if (is_null($family)) return;
	if (is_null($person)) return;

	$famrec = $family->getGedcomRecord();
	$famid = $family->getXref();
	$parents = find_parents($famid);
	if ($parents) {

		// spouse id
		$id = $parents["WIFE"];
		if ($id==$person->getXref()) $id = $parents["HUSB"];

		// print marriage info
		print "<li>";
		print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" height=\"2\" width=\"".($Dindent+4)."\" border=\"0\" alt=\"\" />";
		print "<span class=\"details1\" style=\"white-space: nowrap; \" >";
		print "<a href=\"#\" onclick=\"expand_layer('".$famid.$personcount."'); return false;\" class=\"top\"><img id=\"".$famid.$personcount."_img\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["minus"]["other"]."\" align=\"middle\" hspace=\"0\" vspace=\"3\" border=\"0\" alt=\"".i18n::translate('View Family')."\" /></a>";
		$marriage = $family->getMarriage();
		if ($marriage->canShow()) {
			echo ' <a href="', encode_url($family->getLinkUrl()), '" class="details1">';
			$marriage->print_simple_fact();
			echo '</a>';
		}
		print '</span>';

		// print spouse
		print "<ul style=\"list-style: none; display: block;\" id=\"".$famid.$personcount."\">";
		print "<li>";
		print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
		print_pedigree_person($id, 1, $this->view!="preview",''.$personcount);
		print "</td>";

		// check if spouse has parents and add an arrow
		print "<td>&nbsp;</td>";
		print "<td>";
		$sfamids = find_family_ids($id);
		foreach($sfamids as $indexval => $sfamid) {
			$parents = find_parents($sfamid);
			if ($parents) {
				$parid=$parents["HUSB"];
				if ($parid=="") $parid=$parents["WIFE"];
				if ($parid!="") {
					print_url_arrow($parid.$personcount.$person->getXref(), encode_url("?pid={$parid}&generations={$this->generations}&show_full={$this->show_full}&box_width={$this->box_width}"), i18n::translate('Start at parents'), 2);
					$personcount++;
				}
			}
		}
		if ($this->show_full) print "<br /><br />&nbsp;";
		print "</td></tr>";

		// children
		$children = $family->getChildren();
		print "<tr><td colspan=\"3\" class=\"details1\" >&nbsp;&nbsp;";
		if ($children) {
			print i18n::translate('NCHI').": ".count($children);
		} else {
			// Distinguish between no children (NCHI 0) and no recorded
			// children (no CHIL records)
			if (strpos($family->getGedcomRecord(), "\n1 NCHI 0")) {
				print i18n::translate('NCHI').": ".count($children);
			} else {
				print i18n::translate('No children');
			}
		}
		print "</td></tr></table>";
		print "</li>\r\n";
		if ($depth>0) foreach ($children as $child) {
			$personcount++;
			$this->print_child_descendancy($child, $depth-1);
		}
		print "</ul>\r\n";
		print "</li>\r\n";
	}
}

}

// -- end of class
//-- load a user extended class if one exists
if (file_exists(WT_ROOT.'includes/controllers/descendancy_ctrl_user.php'))
{
	require_once WT_ROOT.'includes/controllers/descendancy_ctrl_user.php';
}
else
{
	class DescendancyController extends DescendancyControllerRoot
	{
	}
}

?>
