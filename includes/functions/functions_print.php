<?php
/**
* Function for printing
*
* Various printing functions used by all scripts and included by the functions.php file.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
*
* Modifications Copyright (c) 2010 Greg Roach
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
* @subpackage Display
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_PRINT_PHP', '');

require_once WT_ROOT.'includes/functions/functions_charts.php';
require_once WT_ROOT.'includes/classes/class_menubar.php';

/**
* print the information for an individual chart box
*
* find and print a given individuals information for a pedigree chart
* @param string $pid the Gedcom Xref ID of the   to print
* @param int $style the style to print the box in, 1 for smaller boxes, 2 for larger boxes
* @param int $count on some charts it is important to keep a count of how many boxes were printed
*/
function print_pedigree_person($pid, $style=1, $count=0, $personcount="1") {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $ZOOM_BOXES, $LINK_ICONS, $GEDCOM;
	global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $USE_SILHOUETTE, $WT_IMAGES, $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	if ($style != 2) $style=1;
	if (empty($show_full)) $show_full = 0;
	if (empty($PEDIGREE_FULL_DETAILS)) $PEDIGREE_FULL_DETAILS = 0;

	if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;
	// NOTE: Start div out-rand()
	$person=WT_Person::getInstance($pid);
	if ($pid==false || empty($person)) {
		echo "<div id=\"out-", rand(), "\" class=\"person_boxNN\" style=\"width: ", $bwidth, "px; height: ", $bheight, "px; padding: 2px; overflow: hidden;\">";
		echo "<br />";
		echo "</div>";
		return false;
	}
	if ($count==0) $count = rand();
	$lbwidth = $bwidth*.75;
	if ($lbwidth < 150) $lbwidth = 150;

	$tmp=array('M'=>'', 'F'=>'F', 'U'=>'NN');
	$isF=$tmp[$person->getSex()];

	$personlinks = "";
	$icons = "";
	$classfacts = "";
	$genderImage = "";
	$BirthDeath = "";
	$outBoxAdd = "";
	$thumbnail = "";
	$showid = "";
	$iconsStyleAdd = "float: right; ";
	if ($TEXT_DIRECTION=="rtl") $iconsStyleAdd="float: left; ";

	$disp=$person->canDisplayDetails();
	$uniqueID = floor(microtime() * 1000000);
	$boxID = $pid.".".$personcount.".".$count.".".$uniqueID;
	$mouseAction1 = "onmouseover=\"clear_family_box_timeout('".$boxID."');\" onmouseout=\"family_box_timeout('".$boxID."');\"";
	$mouseAction2 = " onmouseover=\"expandbox('".$boxID."', $style); return false;\" onmouseout=\"restorebox('".$boxID."', $style); return false;\"";
	$mouseAction3 = " onmousedown=\"expandbox('".$boxID."', $style); return false;\" onmouseup=\"restorebox('".$boxID."', $style); return false;\"";
	$mouseAction4 = " onclick=\"expandbox('".$boxID."', $style); return false;\"";
	if ($person->canDisplayName()) {
		if (empty($SEARCH_SPIDER)) {
			if ($LINK_ICONS!="disabled") {
				//-- draw a box for the family popup
				// NOTE: Start div I.$pid.$personcount.$count.links
				$personlinks .= "<table class=\"person_box$isF\"><tr><td class=\"details1\">";
				// NOTE: Zoom
				$personlinks .= "<a href=\"pedigree.php?rootid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;talloffset={$talloffset}&amp;ged=".rawurlencode($GEDCOM)."\" $mouseAction1><b>".i18n::translate('Pedigree Tree')."</b></a>";

				if (file_exists(WT_ROOT.'modules/googlemap/pedigree_map.php')) {
					$personlinks .= "<br /><a href=\"module.php?mod=googlemap&amp;mod_action=pedigree_map&amp;rootid={$pid}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".i18n::translate('Pedigree Map')."</b></a>";
				}
				$username = WT_USER_NAME;
				if (!empty($username)) {
					$myid=WT_USER_GEDCOM_ID;
					if ($myid && $myid!=$pid) {
						$personlinks .= "<br /><a href=\"relationship.php?show_full={$PEDIGREE_FULL_DETAILS}&amp;pid1={$myid}&amp;pid2={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;pretty=2&amp;followspouse=1&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".i18n::translate('Relationship to me')."</b></a>";
					}
				}

				$personlinks .= "<br /><a href=\"descendancy.php?pid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;generations={$generations}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM)."\" $mouseAction1><b>".i18n::translate('Descendancy chart')."</b></a><br />";

				if (file_exists(WT_ROOT.'ancestry.php')) {
					$personlinks .= "<a href=\"ancestry.php?rootid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;chart_style={$chart_style}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".i18n::translate('Ancestry chart')."</b></a><br />";
				}
				if (file_exists(WT_ROOT.'compact.php')) {
					$personlinks .= "<a href=\"compact.php?rootid={$pid}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".i18n::translate('Compact chart')."</b></a><br />";
				}
				if (file_exists(WT_ROOT.'fanchart.php') and defined("IMG_ARC_PIE") and function_exists("imagettftext")) {
					$personlinks .= "<a href=\"fanchart.php?rootid={$pid}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".i18n::translate('Circle diagram')."</b></a><br />";
				}
				if (file_exists(WT_ROOT.'hourglass.php')) {
					$personlinks .= "<a href=\"hourglass.php?pid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;chart_style={$chart_style}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM)."&amp;show_spouse={$show_spouse}\" ".$mouseAction1."><b>".i18n::translate('Hourglass chart')."</b></a><br />";
				}
				if (file_exists(WT_ROOT.'treenav.php')) {
					$personlinks .= "<a href=\"treenav.php?rootid={$pid}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".i18n::translate('Interactive tree')."</b></a><br />";
				}

				$fams = $person->getSpouseFamilies();
				/* @var $family Family */
				foreach ($fams as $famid=>$family) {
					if (!is_null($family)) {
						$spouse = $family->getSpouse($person);

						$children = $family->getChildren();
						$num = count($children);
						if ((!empty($spouse))||($num>0)) {
							$personlinks .= "<a href=\"".$family->getHtmlUrl()."&amp;show_full=1\" ".$mouseAction1."><b>".i18n::translate('Family with spouse')."</b></a><br />";
							if (!empty($spouse)) {
								$personlinks .= "<a href=\"".$spouse->getHtmlUrl()."\" $mouseAction1>";
								if ($spouse->canDisplayName()) $personlinks .= PrintReady($spouse->getFullName());
								else $personlinks .= i18n::translate('Private');
								$personlinks .= "</a><br />";
							}
						}
						/* @var $child Person */
						foreach ($children as $c=>$child) {
							$personlinks .= "&nbsp;&nbsp;<a href=\"".$child->getHtmlUrl()."\" $mouseAction1>";
							if ($child->canDisplayName()) $personlinks .= PrintReady($child->getFullName());
							else $personlinks .= i18n::translate('Private');
							$personlinks .= "<br /></a>";
						}
					}
				}
				$personlinks .= "</td></tr></table>";
			}
			// NOTE: Start div out-$pid.$personcount.$count
			if ($style==1) $outBoxAdd .= " class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden; z-index:-1;\"";
			else $outBoxAdd .= " class=\"person_box$isF\" style=\"padding: 2px;\"";
			// NOTE: Zoom
			if (($ZOOM_BOXES!="disabled")&&(!$show_full)) {
				if ($ZOOM_BOXES=="mouseover") $outBoxAdd .= $mouseAction2;
				if ($ZOOM_BOXES=="mousedown") $outBoxAdd .= $mouseAction3;
				if (($ZOOM_BOXES=="click")) $outBoxAdd .= $mouseAction4;
			}
			// NOTE: Zoom
			if (($ZOOM_BOXES!="disabled")&&($show_full)) {
				$icons .= "<a href=\"javascript:;\"";
				if ($ZOOM_BOXES=="mouseover") $icons .= $mouseAction2;
				if ($ZOOM_BOXES=="mousedown") $icons .= $mouseAction3;
				if ($ZOOM_BOXES=="click") $icons .= $mouseAction4;
				$icons .= "><img id=\"iconz-$boxID\" src=\"".$WT_IMAGES["zoomin"]."\" width=\"25\" height=\"25\" border=\"0\" alt=\"".i18n::translate('Zoom in/out on this box.')."\" title=\"".i18n::translate('Zoom in/out on this box.')."\" /></a>";
			}
			if ($LINK_ICONS!="disabled") {
				$click_link="javascript:;";
				if (WT_SCRIPT_NAME=='pedigree.php') {
					$click_link="pedigree.php?rootid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;talloffset={$talloffset}&amp;ged=".rawurlencode($GEDCOM);
					$whichID=$pid;
				}

				if (WT_SCRIPT_NAME=='hourglass.php') {
					$click_link="hourglass.php?pid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;generations={$generations}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM);
					$whichID=$pid;
				}

				if (WT_SCRIPT_NAME=='ancestry.php') {
					$click_link="ancestry.php?rootid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;chart_style={$chart_style}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM);
					$whichID=$pid;
				}

				if (WT_SCRIPT_NAME=='descendancy.php') {
					$click_link="descendancy.php?show_full={$PEDIGREE_FULL_DETAILS}&amp;pid={$pid}&amp;generations={$generations}&box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM);
					$whichID=$pid;
				}

				if (WT_SCRIPT_NAME=='family.php' && !empty($famid)) {
					$click_link="family.php?famid={$famid}&amp;show_full=1&amp;ged=".rawurlencode($GEDCOM);
					$whichID=$famid;
				}

				if (WT_SCRIPT_NAME=='individual.php') {
					$click_link="individual.php?pid={$pid}&amp;ged=".rawurlencode($GEDCOM);
					$whichID=$pid;
				}

				$icons .= "<a href=\"$click_link\" ";
				// NOTE: Zoom
				if ($LINK_ICONS=="mouseover") $icons .= "onmouseover=\"show_family_box('".$boxID."', '";
				if ($LINK_ICONS=="click") $icons .= "onclick=\"toggle_family_box('".$boxID."', '";
				if ($style==1) $icons .= "box$pid";
				else $icons .= "relatives";
				$icons .= "');";
				$icons .= " return false;\" ";
				// NOTE: Zoom
				$icons .= "onmouseout=\"family_box_timeout('".$boxID."');";
				$icons .= " return false;\"";
				if (($click_link=="#")&&($LINK_ICONS!="click")) $icons .= "onclick=\"return false;\"";
				$icons .= "><img src=\"".$WT_IMAGES["pedigree"]."\" width=\"25\" border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".i18n::translate('Links to charts, families, and close relatives of this person. Click this icon to view this page, starting at this person.')."\" title=\"".i18n::translate('Links to charts, families, and close relatives of this person. Click this icon to view this page, starting at this person.')."\" /></a>";
			}
		}
		else {
			if ($style==1) {
				$outBoxAdd .= "class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"";
			} else {
				$outBoxAdd .= "class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"";
			}
			// NOTE: Zoom
			if (($ZOOM_BOXES!="disabled")&&(empty($SEARCH_SPIDER))) {
				if ($ZOOM_BOXES=="mouseover") $outBoxAdd .= $mouseAction2;
				if ($ZOOM_BOXES=="mousedown") $outBoxAdd .= $mouseAction3;
				if (($ZOOM_BOXES=="click")) $outBoxAdd .= $mouseAction4;
			}
		}
	}
	else {
		if ($style==1) $outBoxAdd .= "class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; padding: 2px; overflow: hidden;\"";
		else $outBoxAdd .= "class=\"person_box$isF\" style=\"padding: 2px; overflow: hidden;\"";
	}
	//-- find the name
	$name = $person->getFullName();
	if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES) {
		$object = $person->findHighlightedMedia();
		if (!empty($object)) {
			$whichFile = thumb_or_main($object); // Do we send the main image or a thumbnail?
			$size = findImageSize($whichFile);
			$class = "pedigree_image_portrait";
			if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
			if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
			// NOTE: IMG ID
			$imgsize = findImageSize($object["file"]);
			$imgwidth = $imgsize[0]+50;
			$imgheight = $imgsize[1]+150;

			if (WT_USE_LIGHTBOX) {
				$thumbnail .= "<a href=\"" . $object["file"] . "\" rel=\"clearbox[general_2]\" rev=\"" . $object['mid'] . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name)) . "\">";
			} else if (!empty($object['mid']) && $USE_MEDIA_VIEWER) {
				$thumbnail .= "<a href=\"mediaviewer.php?mid=".$object['mid']."\" >";
			} else {
				$thumbnail .= "<a href=\"javascript:;\" onclick=\"return openImage('".rawurlencode($object["file"])."', $imgwidth, $imgheight);\">";
			}
			$thumbnail .= "<img id=\"box-$boxID-thumb\" src=\"".$whichFile."\" vspace=\"0\" hspace=\"0\" class=\"$class\" alt=\"\" title=\"".PrintReady(htmlspecialchars(strip_tags($name)))."\"";
			if (!$show_full) $thumbnail .= " style=\"display: none;\"";
			if ($imgsize) $thumbnail .= " /></a>";
			else $thumbnail .= " />";
		} else if ($USE_SILHOUETTE && isset($WT_IMAGES["default_image_U"])) {
			$class = "pedigree_image_portrait";
			if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
			$sex = $person->getSex();
			$thumbnail = "<img id=\"box-$boxID-thumb\" src=\"";
			if ($sex == 'F') {
				$thumbnail .= $WT_IMAGES["default_image_F"]."\"";
			}
			else if ($sex == 'M') {
				$thumbnail .= $WT_IMAGES["default_image_M"]."\"";
			}
			else {
				$thumbnail .= $WT_IMAGES["default_image_U"]."\"";
			}
			if (!$show_full) $thumbnail .= " style=\"display: none;\"";
			$thumbnail .=" class=\"".$class."\" border=\"none\" alt=\"\" />";
		}
	}
	//-- find additional name
	$addname=$person->getAddName();
	$name = PrintReady($name);

	// add optional CSS style for each fact
	$indirec = $person->getGedcomRecord();
	$cssfacts = array("BIRT", "CHR", "DEAT", "BURI", "CREM", "ADOP", "BAPM", "BARM", "BASM", "BLES", "CHRA", "CONF", "FCOM", "ORDN", "NATU", "EMIG", "IMMI", "CENS", "PROB", "WILL", "GRAD", "RETI", "CAST", "DSCR", "EDUC", "IDNO",
	"NATI", "NCHI", "NMR", "OCCU", "PROP", "RELI", "RESI", "SSN", "TITL", "BAPL", "CONL", "ENDL", "SLGC", "_MILI");
	foreach ($cssfacts as $indexval => $fact) {
		if (strpos($indirec, "1 $fact")!==false) $classfacts .= " $fact";
	}
	if ($PEDIGREE_SHOW_GENDER)
		$genderImage = " ".$person->getSexImage('small', "box-$boxID-gender");
	if (strlen($addname) > 0) {
		$tempStyle = $style;
		if (hasRTLText($addname) && $style=='1') $tempStyle = '2';
		$addname = "<br /><span id=\"addnamedef-$boxID\" class=\"name$tempStyle\"> ".PrintReady($addname)."</span>";
	}
	if ($SHOW_LDS_AT_GLANCE) {
		$addname = ' <span class="details$style">'.get_lds_glance($indirec).'</span>' . $addname;
	}

		if ($show_full) {

			$opt_tags=preg_split('/\W/', $CHART_BOX_TAGS, 0, PREG_SPLIT_NO_EMPTY);

			// Show BIRT or equivalent event
			foreach (explode('|', WT_EVENTS_BIRT) as $birttag) {
			if (!in_array($birttag, $opt_tags)) {
				$event = $person->getFactByType($birttag);
				if (!is_null($event) && $event->canShow()) {
					$BirthDeath .= $event->print_simple_fact(true);
					break;
					}
				}
			}

			// Show optional events (before death)
			foreach ($opt_tags as $key=>$tag) {
			if (!preg_match('/^('.WT_EVENTS_DEAT.')$/', $tag)) {
				$event = $person->getFactByType($tag);
				if (!is_null($event) && $event->canShow()) {
					$BirthDeath .= $event->print_simple_fact(true);
					unset ($opt_tags[$key]);
				}
			}
		}

			// Show DEAT or equivalent event
			foreach (explode('|', WT_EVENTS_DEAT) as $deattag) {
			$event = $person->getFactByType($deattag);
			if (!is_null($event) && $event->canShow()) {
				$BirthDeath .= $event->print_simple_fact(true);
					if (in_array($deattag, $opt_tags)) {
						unset ($opt_tags[array_search($deattag, $opt_tags)]);
					}
					break;
				}
			}

			// Show remaining optional events (after death)
			foreach ($opt_tags as $tag) {
			$event = $person->getFactByType($tag);
			if (!is_null($event) && $event->canShow()) {
				$BirthDeath .= $event->print_simple_fact(true);
				}
			}
		}
	require WT_THEME_DIR.'templates/personbox_template.php';
}

/**
* print out standard HTML header
*
* This function will print out the HTML, HEAD, and BODY tags and will load in the CSS javascript and
* other auxiliary files needed to run PGV.  It will also include the theme specific header file.
* This function should be called by every page, except popups, before anything is output.
*
* Popup pages, because of their different format, should invoke function print_simple_header() instead.
*
* @param string $title the title to put in the <TITLE></TITLE> header tags
*/
function print_header($title) {
	global $bwidth, $BROWSERTYPE, $SEARCH_SPIDER, $view, $cart;
	global $GEDCOM, $GEDCOM_TITLE, $action, $query, $theme_name;
	global $stylesheet, $print_stylesheet, $rtl_stylesheet, $headerfile, $print_headerfile;
	global $WT_IMAGES, $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION;

	header('Content-Type: text/html; charset=UTF-8');

	$META_DESCRIPTION=get_gedcom_setting(WT_GED_ID, 'META_DESCRIPTION');
	$META_ROBOTS=get_gedcom_setting(WT_GED_ID, 'META_ROBOTS');
	$META_TITLE=get_gedcom_setting(WT_GED_ID, 'META_TITLE');

	// The title often includes the names of records, which may have markup
	// that cannot be used in the page title.
	$title=strip_tags($title);

	if ($META_TITLE) {
		$title.=' - '.$META_TITLE;
	}
	$GEDCOM_TITLE = get_gedcom_setting(WT_GED_ID, 'title');
	$javascript=
		'<script type="text/javascript" src="js/jquery/jquery.min.js"></script>'.
		'<script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script>'.
		'<script type="text/javascript" src="js/jquery/jquery.tablesorter.js"></script>'. // Deprecated - use datatables
		'<script type="text/javascript" src="js/jquery/jquery.tablesorter.pager.js"></script>'. // Deprecated - use datatables
		'<script type="text/javascript" src="js/jquery/jquery.jeditable.js"></script>'.
		'<script type="text/javascript" src="js/jquery/jquery.dataTables.min.js"></script>'.
		WT_JS_START.'
		/* setup some javascript variables */
		var textDirection = "'.$TEXT_DIRECTION.'";
		var browserType = "'.$BROWSERTYPE.'";
		var themeName = "'.$theme_name.'";
		var SCRIPT_NAME = "'.WT_SCRIPT_NAME.'";
		/* keep the session id when opening new windows */
		var sessionid = "'.Zend_Session::getId().'";
		var sessionname = "'.WT_SESSION_NAME.'";
		var accesstime = '.WT_DB::prepare("SELECT UNIX_TIMESTAMP(NOW())")->fetchOne().';
		var plusminus = new Array();
		plusminus[0] = new Image();
		plusminus[0].src = "'.$WT_IMAGES["plus"].'";
		plusminus[0].title = "'.i18n::translate('Show Details').'";
		plusminus[1] = new Image();
		plusminus[1].src = "'.$WT_IMAGES["minus"].'";
		plusminus[1].title = "'.i18n::translate('Hide Details').'";
		var zoominout = new Array();
		zoominout[0] = new Image();
		zoominout[0].src = "'.$WT_IMAGES["zoomin"].'";
		zoominout[1] = new Image();
		zoominout[1].src = "'.$WT_IMAGES["zoomout"].'";
		var arrows = new Array();
		arrows[0] = new Image();
		arrows[0].src = "'.$WT_IMAGES["larrow2"].'";
		arrows[1] = new Image();
		arrows[1].src = "'.$WT_IMAGES["rarrow2"].'";
		arrows[2] = new Image();
		arrows[2].src = "'.$WT_IMAGES["uarrow2"].'";
		arrows[3] = new Image();
		arrows[3].src = "'.$WT_IMAGES["darrow2"].'";
	';
	$javascript .= 'function delete_record(pid, linenum, mediaid) {
		if (!mediaid) mediaid="";
		if (confirm(\''.i18n::translate('Are you sure you want to delete this fact?').'\')) {
			window.open(\'edit_interface.php?action=delete&pid=\'+pid+\'&linenum=\'+linenum+\'&mediaid=\'+mediaid+"&"+sessionname+"="+sessionid, \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\');
		}
		return false;
	}

	function deleteperson(pid) {
		if (confirm(\''.i18n::translate('Are you sure you want to delete this person?').'\')) {
			window.open(\'edit_interface.php?action=deleteperson&pid=\'+pid+"&"+sessionname+"="+sessionid, \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\');
		}
		return false;
	}

	function deleterepository(pid) {
		if (confirm(\''.i18n::translate('Are you sure you want to delete this Repository?').'\')) {
			window.open(\'edit_interface.php?action=deleterepo&pid=\'+pid+"&"+sessionname+"="+sessionid, \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\');
		}
		return false;
	}
	';
	$javascript .= '
	function message(username, method, url, subject) {
		if ((!url)||(url=="")) url=\''.addslashes(get_query_url()).'\';
		if ((!subject)||(subject=="")) subject="";
		window.open(\'message.php?to=\'+username+\'&method=\'+method+\'&url=\'+url+\'&subject=\'+subject+"&"+sessionname+"="+sessionid, \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\');
		return false;
	}

	var whichhelp = \'help_'.WT_SCRIPT_NAME.'&action='.$action.'\';
	//-->
	'.WT_JS_END.'<script src="js/webtrees.js" language="JavaScript" type="text/javascript"></script>';
	require WT_ROOT.$headerfile;

	// Allow the browser to format the header/menus while we generate the page
	flush();
}

/**
* print simple HTML header
*
* This function will print out the HTML, HEAD, and BODY tags and will load in the CSS javascript and
* other auxiliary files needed to run PGV.  It does not include any theme specific header files.
* This function should be called by every page before anything is output on popup pages.
*
* @param string $title the title to put in the <TITLE></TITLE> header tags

*/
function print_simple_header($title) {
	global $view;
	$view = 'simple';
	print_header($title);
}

// -- print the html to close the page
function print_footer() {
	global $SHOW_STATS, $footerfile, $printlink, $theme_name, $WT_IMAGES, $TEXT_DIRECTION, $footer_count;

	if (!isset($footer_count)) $footer_count = 1;
	else $footer_count++;
	echo "<!-- begin footer -->";
	require WT_ROOT.$footerfile;
	if (function_exists("load_behaviour")) {
		load_behaviour();  // @see function_print_lists.php
	}
	if (WT_DEBUG_SQL) {
		echo WT_DB::getQueryLog();
	}
	echo clustrmaps();
	echo google_analytics();
	echo '</body></html>';
}

// Page footer for popup/edit windows
function print_simple_footer() {
	global $SHOW_STATS;

	if ($SHOW_STATS || WT_DEBUG) {
		echo execution_stats();
	}
	if (WT_DEBUG_SQL) {
		echo WT_DB::getQueryLog();
	}
	echo '</body></html>';
}

// Generate code for google analytics
function google_analytics() {
	if (defined('WT_GOOGLE_ANALYTICS')) {
		return '<script type="text/javascript">var gaJsHost=(("https:"==document.location.protocol)?"https://ssl.":"http://www.");document.write(unescape("%3Cscript src=\'"+gaJsHost+"google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));</script><script type="text/javascript">var pageTracker=_gat._getTracker("'.WT_GOOGLE_ANALYTICS.'");pageTracker._initData();pageTracker._trackPageview();</script>';
	} else {
		return '';
	}
}

// Generate code for clustrmaps
// Enable by adding
// define('WT_CLUSTRMAPS', 'your website address');
// e.g. define('WT_CLUSTRMAPS', 'http://vidyasridhar.no-ip.org/');
// to the end of your config.php

function clustrmaps() {
	if (defined('WT_CLUSTRMAPS')) {
		return '<a
 href="http://www2.clustrmaps.com/counter/maps.php?url='.WT_CLUSTRMAPS.'"
 id="clustrMapsLink"><img
 src="http://www2.clustrmaps.com/counter/index2.php?url='.WT_CLUSTRMAPS.'"
 style="border: 0px none ;"
 alt="Locations of visitors to this page"
 title="Locations of visitors to this page" id="clustrMapsImg"
 onerror="this.onerror=null; this.src=\'http://clustrmaps.com/images/clustrmaps-back-soon.jpg\'; document.getElementById(\'clustrMapsLink\').href=\'http://clustrmaps.com\';">
</a>';
	} else {
		return '';
	}
}

/**
* Prints Exection Statistics
*
* prints out the execution time and the databse queries
*/
function execution_stats() {
	global $start_time, $PRIVACY_CHECKS;

	return sprintf("<div class=\"execution_stats\">".i18n::translate('Execution time:')." %.3f ".i18n::translate('sec.')." ".i18n::translate('Total Database Queries: ')." %d. ".i18n::translate('Total privacy checks: ')." %d. ".i18n::translate('Total Memory Usage:')." %.0f KB.</div>",
		microtime(true)-$start_time,
		WT_DB::getQueryCount(),
		$PRIVACY_CHECKS,
		version_compare(PHP_VERSION, '5.2.1', '>=') ? (memory_get_peak_usage(true)/1024) : (memory_get_usage()/1024)
	);
}

// Generate a login link
function login_link($extra='') {

	if (WT_SCRIPT_NAME=='login.php') {
		$href='#';
	} else {
		$href=get_site_setting('LOGIN_URL', 'login.php').'?url='.rawurlencode(get_query_url());
	}
	return '<a href="' . $href . '" ' . $extra . ' class="link">' . i18n::translate('Login') . '</a>';
}

// Generate a logout link
function logout_link($extra='') {
	return '<a href="index.php?logout=1" ' . $extra . ' class="link">' . i18n::translate('Logout') . '</a>';
}

// Print a link to allow email/messaging contact with a user
// Optionally specify a method (used for webmaster/genealogy contacts)
function user_contact_link($user_id) {
	$method=get_user_setting($user_id, 'contactmethod');

	$fullname=getUserFullName($user_id);

	switch ($method) {
	case 'none':
		return '';
	case 'mailto':
		$email=getUserEmail($user_id);
		return '<a href="mailto:'.htmlspecialchars($email).'">'.htmlspecialchars($fullname).'</a>';
	default:
		return "<a href='javascript:;' onclick='message(\"".get_user_name($user_id)."\", \"{$method}\");return false;'>{$fullname}</a>";
	}
}

// Print a menu item to allow email/messaging contact with a user
// Optionally specify a method (used for webmaster/genealogy contacts)
function user_contact_menu($user_id) {
	$method=get_user_setting($user_id, 'contactmethod');

	$fullname=getUserFullName($user_id);

	switch ($method) {
	case 'none':
		return array();
	case 'mailto':
		$email=getUserEmail($user_id);
		return array('label'=>$fullname, 'labelpos'=>'right', 'class'=>'submenuitem', 'hoverclass'=>'submenuitem_hover', 'link'=>"mailto:{$email}");
	default:
		return array('label'=>$fullname, 'labelpos'=>'right', 'class'=>'submenuitem', 'hoverclass'=>'submenuitem_hover', 'link'=>'#', 'onclick'=>"message('".get_user_name($user_id)."', '{$method}');return false;");
	}
}

// print links for genealogy and technical contacts
//
// this function will print appropriate links based on the preferred contact methods for the genealogy
// contact user and the technical support contact user
function contact_links($ged_id=WT_GED_ID) {
	$contact_user_id  =get_gedcom_setting($ged_id, 'CONTACT_USER_ID');
	$webmaster_user_id=get_gedcom_setting($ged_id, 'WEBMASTER_USER_ID');
	$supportLink = user_contact_link($webmaster_user_id);
	if ($webmaster_user_id==$contact_user_id) {
		$contactLink = $supportLink;
	} else {
		$contactLink = user_contact_link($contact_user_id);
	}

	if (!$supportLink && !$contactLink) {
		return '';
	}

	if ($supportLink==$contactLink) {
		return '<div class="contact_links">'.i18n::translate('For technical support or genealogy questions, please contact').' '.$supportLink.'</div>';
	} else {
		$returnText = '<div class="contact_links">';
		if ($supportLink) {
			$returnText .= i18n::translate('For technical support and information contact').' '.$supportLink;
			if ($contactLink) {
				$returnText .= '<br />';
			}
		}
		if ($contactLink) {
			$returnText .= i18n::translate('For help with genealogy questions contact').' '.$contactLink;
		}
		$returnText .= '</div>';
		return $returnText;
	}
}

function contact_menus($ged_id=WT_GED_ID) {
	$contact_user_id  =get_gedcom_setting($ged_id, 'CONTACT_USER_ID');
	$webmaster_user_id=get_gedcom_setting($ged_id, 'WEBMASTER_USER_ID');

	$support_menu=user_contact_menu($webmaster_user_id);
	$contact_menu=user_contact_menu($contact_user_id);

	if (!$support_menu) {
		$support_menu=$contact_menu;
	}
	if (!$contact_menu) {
		$contact_menu=$support_menu;
	}
	if (!$support_menu) {
		return array();
	}
	$menuitems=array();
	if ($support_menu==$contact_menu) {
		$support_menu['label']=i18n::translate('Technical help contact');
		$menuitems[]=$support_menu;
	} else {
		$support_menu['label']=i18n::translate('Technical help contact');
		$menuitems[]=$support_menu;
		$contact_menu['label']=i18n::translate('Genealogy contact');
		$menuitems[]=$contact_menu;
	}
	return $menuitems;
}

//-- print user favorites
function print_favorite_selector($option=0) {
	global $GEDCOM;
	global $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION, $WT_IMAGES, $SEARCH_SPIDER;
	global $controller; // Pages with a controller can be added to the favorites

	if (!empty($SEARCH_SPIDER)) {
		return; // show no favorites, because they taint every page that is indexed.
	}
	if (!WT_USER_NAME && $REQUIRE_AUTHENTICATION) return false;

	$currentGedcom = $GEDCOM;

	if (array_key_exists('gedcom_favorites', WT_Module::getActiveModules())) {
		$gedcomfavs = gedcom_favorites_WT_Module::getUserFavorites($GEDCOM);
	} else {
		$gedcomfavs = array();
	}
	if (WT_USER_NAME && array_key_exists('user_favorites', WT_Module::getActiveModules())) {
		$userfavs = user_favorites_WT_Module::getUserFavorites(WT_USER_NAME);
	} else {
		$userfavs = array();
	}

	$gid = '';
	if (WT_USER_NAME && isset($controller)) {
		// Get the right $gid from each supported controller type
		switch (get_class($controller)) {
		case 'WT_Controller_Individual':
			$gid = $controller->pid;
			break;
		case 'WT_Controller_Family':
			$gid = $controller->famid;
			break;
		case 'WT_Controller_Media':
			$gid = $controller->mid;
			break;
		case 'WT_Controller_Source':
			$gid = $controller->sid;
			break;
		case 'WT_Controller_Repository':
			$gid = $controller->rid;
			break;
		default:
			break;
		}
	}

	if (!WT_USER_NAME && count($gedcomfavs)==0) return;
	echo "<div class=\"favorites_form\">";
	switch($option) {
	case 1:
		$menu = new Menu(i18n::translate('Favorites'), "#", "right", "down");
		$menu->addClass("favmenuitem", "favmenuitem_hover", "favsubmenu");
		if (count($userfavs)>0 || $gid!='') {
			$submenu = new Menu("<strong>".i18n::translate('My Favorites')."</strong>", "#", "right");
			$submenu->addClass("favsubmenuitem", "favsubmenuitem_hover");
			$menu->addSubMenu($submenu);

			if ($gid!='') {
				$submenu = new Menu('<em>'.i18n::translate('Add to My Favorites').'</em>', get_query_url(array('action'=>'addfav', 'gid'=>$gid)), "right");
				$submenu->addClass("favsubmenuitem", "favsubmenuitem_hover");
				$menu->addSubMenu($submenu);
			}

			foreach ($userfavs as $key=>$favorite) {
				$GEDCOM = $favorite["file"];
				$submenu = new Menu();
				if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
					$submenu->addLink($favorite["url"]);
					$submenu->addLabel(PrintReady($favorite["title"]), "right");
					$submenu->addClass("favsubmenuitem", "favsubmenuitem_hover");
					$menu->addSubMenu($submenu);
				} else {
					$record=WT_GedcomRecord::getInstance($favorite["gid"]);
					if ($record && $record->canDisplayName()) {
						$submenu->addLink($record->getHtmlUrl());
						$slabel = PrintReady($record->getFullName());
						$submenu->addLabel($slabel,  "right");
						$submenu->addClass("favsubmenuitem", "favsubmenuitem_hover");
						$menu->addSubMenu($submenu);
					}
				}
			}
			if (count($gedcomfavs)>0) {
				$menu->addSeparator();
			}
		}
		if (count($gedcomfavs)>0) {
			$submenu = new Menu("<strong>".i18n::translate('This GEDCOM\'s Favorites')."</strong>", "#", "right");
			$submenu->addClass("favsubmenuitem", "favsubmenuitem_hover");
			$menu->addSubMenu($submenu);
			foreach ($gedcomfavs as $key=>$favorite) {
				$GEDCOM = $favorite["file"];
				$submenu = new Menu();
				if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
					$submenu->addLink($favorite["url"]);
					$submenu->addLabel(PrintReady($favorite["title"]), "right");
					$submenu->addClass("favsubmenuitem", "favsubmenuitem_hover");
					$menu->addSubMenu($submenu);
				} else {
					$record=WT_GedcomRecord::getInstance($favorite["gid"]);
					if ($record && $record->canDisplayName()) {
						$submenu->addLink($record->getHtmlUrl());
						$slabel = PrintReady($record->getFullName());
						$submenu->addLabel($slabel,  "right");
						$submenu->addClass("favsubmenuitem", "favsubmenuitem_hover");
						$menu->addSubMenu($submenu);
					}
				}
			}
		}
		echo $menu->getMenu();
		break;
	default:
		echo '<form class="favorites_form" name="favoriteform" action="', WT_SCRIPT_NAME, '"';
		echo " method=\"post\" onsubmit=\"return false;\">";
		echo "<select name=\"fav_id\" class=\"header_select\" onchange=\"if (document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value!='') window.location=document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value; if (document.favoriteform.fav_id.options[document.favoriteform.fav_id.selectedIndex].value=='add') window.location='", get_query_url(array('action'=>'addfav', 'gid'=>$gid)), "';\">";
		echo "<option value=\"\">", i18n::translate('Favorites'), "</option>";
		if (WT_USER_NAME && array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			if (count($userfavs)>0 || $gid!='') {
				echo "<optgroup label=\"", i18n::translate('My Favorites'), "\">";
			}
			if ($gid!='') {
				echo "<option value=\"add\">- ", i18n::translate('Add to My Favorites'), " -</option>";
			}
			foreach ($userfavs as $key=>$favorite) {
				$GEDCOM = $favorite["file"];
				if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
					echo "<option value=\"", $favorite["url"], "\">", PrintReady($favorite["title"]);
					echo "</option>";
				} else {
					switch ($favorite['type']) {
					case 'INDI':
						$record=WT_Person::getInstance($favorite["gid"]);
						break;
					case 'FAM':
						$record=WT_Family::getInstance($favorite["gid"]);
						break;
					case 'SOUR':
						$record=WT_Source::getInstance($favorite["gid"]);
						break;
					case 'REPO':
						$record=WT_Repository::getInstance($favorite["gid"]);
						break;
					case 'OBJE':
						$record=WT_Media::getInstance($favorite["gid"]);
						break;
					default:
						$record=WT_GedcomRecord::getInstance($favorite["gid"]);
						break;
					}
					if ($record && $record->canDisplayName()) {
						$name=$record->getFullName();
						echo "<option value=\"", $record->getHtmlUrl(), "\">", $name, "</option>";
					}
				}
			}
			if (count($userfavs)>0 || $gid!='') {
				echo "</optgroup>";
			}
		}
		if (count($gedcomfavs)>0) {
			echo "<optgroup label=\"", i18n::translate('This GEDCOM\'s Favorites'), "\">";
			foreach ($gedcomfavs as $key=>$favorite) {
				if ($favorite["type"]=="URL" && !empty($favorite["url"])) {
					echo "<option value=\"", $favorite["url"], "\">", PrintReady($favorite["title"]);
					echo "</option>";
				} else {
					$record=WT_GedcomRecord::getInstance($favorite["gid"]);
					if ($record && $record->canDisplayName()) {
						$name=$record->getFullName();
						echo "<option value=\"", $record->getHtmlUrl(), "\">", $name, "</option>";
					}
				}
			}
			echo "</optgroup>";
		}
		echo "</select></form>";
		break;
	}
	echo "</div>";
	$GEDCOM = $currentGedcom;
}
/**
* print a note record
* @param string $text
* @param int $nlevel the level of the note record
* @param string $nrec the note record to print
* @param bool $textOnly Don't print the "Note: " introduction
* @param boolean $return Print the data or return the data
* @param boolean $npage the data is on note page or not
* @return boolean
*/
function print_note_record($text, $nlevel, $nrec, $textOnly=false, $return=false, $npage=false) {
	global $WT_IMAGES, $EXPAND_SOURCES, $EXPAND_NOTES;

	$elementID = "N-".floor(microtime()*1000000);
	$text = trim($text);

	// Check if Shared Note and if so enable url link on title -------------------
	if (preg_match('/^0 @'.WT_REGEX_XREF.'@ NOTE/', $nrec)) {
		$centitl  = str_replace("~~", "", $text);
		$centitl  = str_replace("<br />", "", $centitl);
		if (preg_match("/@N([0-9])+@/", $nrec, $match_nid)) {
			$nid = str_replace("@", "", $match_nid[0]);
			if (!$npage) {
				$centitl = "<a href=\"note.php?nid=$nid\">".$centitl."</a>";
			}
		}
		if ($textOnly) {
			$text = $centitl;
			return $text;
		} else {
			$text = get_cont($nlevel, $nrec);
		}
	} else {
		$text .= get_cont($nlevel, $nrec);
	}
	$text = str_replace("~~", "<br />", $text);
	$text = trim(expand_urls(stripLRMRLM($text)));
	$data = "";

	if (!empty($text) || !empty($centitl)) {
		$text = PrintReady($text);
		// Check if Formatted Shared Note (using pipe "|" as delimiter ) --------------------
		if (preg_match('/^0 @'.WT_REGEX_XREF.'@ NOTE/', $nrec) && strstr($text, "|") && file_exists(WT_ROOT.'modules/GEDFact_assistant/_CENS/census_note_decode.php') ) {
			require WT_ROOT.'modules/GEDFact_assistant/_CENS/census_note_decode.php';
		// Else if unformatted Shared Note --------------------------------------------------
		} else if (preg_match('/^0 @'.WT_REGEX_XREF.'@ NOTE/', $nrec)) {
			$text=$centitl.$text;
		}
		if ($textOnly) {
			if (!$return) {
				echo $text;
				return true;
			} else {
				return $text;
			}
		}

		$brpos = strpos($text, "<br />");
		if (!$npage) {
			$data .= "<span class=\"label\">";
			if ($brpos !== false) {
				if ($EXPAND_NOTES) $plusminus="minus"; else $plusminus="plus";
				$data .= "<a href=\"javascript:;\" onclick=\"expand_layer('$elementID'); return false;\"><img id=\"{$elementID}_img\" src=\"".$WT_IMAGES[$plusminus]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"";
				if ($plusminus=="plus") $data .= i18n::translate('Show Details')."\" title=\"".i18n::translate('Show Details')."\" /></a> ";
				else $data .= i18n::translate('Hide Details')."\" title=\"".i18n::translate('Hide Details')."\" /></a> ";
			}

			// Check if Shared Note -----------------------------
			if (preg_match('/^0 @'.WT_REGEX_XREF.'@ NOTE/', $nrec)) {
				$data .= i18n::translate('Shared note').": </span> ";
			} else {
				$data .= i18n::translate('Note').": </span>";
			}
		}

		if ($brpos !== false) {
			$data .= substr($text, 0, $brpos);
			if ($npage) {
				$data .= "<br />".substr($text, $brpos + 6);
			} else {
				$data .= "<div id=\"$elementID\"";
				if ($EXPAND_NOTES) $data .= " style=\"display:block\"";
				$data .= " class=\"note_details font11\">";
				$data .= substr($text, $brpos + 6);
				$data .= "</div>";
			}
		} else {
			$data .= $text;
		}

		if (!$return) {
			echo $data;
			return true;
		} else {
			return $data;
		}

	}
	return false;
}

/**
* Print all of the notes in this fact record
* @param string $factrec the factrecord to print the notes from
* @param int $level The level of the factrecord
* @param bool $textOnly Don't print the "Note: " introduction
* @param boolean $return whether to return text or print the data
*/
function print_fact_notes($factrec, $level, $textOnly=false, $return=false) {
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	$data = "";
	$nlevel = $level+1;
	$ct = preg_match_all("/$level NOTE(.*)/", $factrec, $match, PREG_SET_ORDER);
	for ($j=0; $j<$ct; $j++) {
		$spos1 = strpos($factrec, $match[$j][0]);
		$spos2 = strpos($factrec."\n$level", "\n$level", $spos1+1);
		if (!$spos2) $spos2 = strlen($factrec);
		$nrec = substr($factrec, $spos1, $spos2-$spos1);
		if (!isset($match[$j][1])) $match[$j][1]="";
		$nt = preg_match("/@(.*)@/", $match[$j][1], $nmatch);
		$closeSpan = false;
		if ($nt==0) {
			//-- print embedded note records
			$closeSpan = print_note_record($match[$j][1], $nlevel, $nrec, $textOnly, true);
			$data .= $closeSpan."<br />";
		} else {
			$noterec = find_gedcom_record($nmatch[1], $ged_id);
			if (canDisplayRecord($ged_id, $noterec)) {
				//-- print linked note records
				$nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
				$closeSpan = print_note_record(($nt>0)?$n1match[1]:"", 1, $noterec, $textOnly, true);
				$data .= $closeSpan."<br />";
				if (!$textOnly) {
					if (strpos($noterec, "1 SOUR")!==false) {
						$data .= print_fact_sources($noterec, 1, true);
						$data .= "<br />";
					}
				}
			}
		}
		if (!$textOnly) {
			if (strpos($factrec, "$nlevel SOUR")!==false) {
				$data .= "<div class=\"indent\">";
				$data .= print_fact_sources($nrec, $nlevel, true);
				$data .= "</div>";
			}
		}
		/*
		if ($closeSpan) {
			if ($j==$ct-1 || $textOnly==false) {
				$data .= "</span>";
			} else {
				$data .= "</span><br /><br />";
			}
		}
		*/
	}
	if (!$return) echo $data;
	else return $data;
}
/**
* print a gedcom title linked to the gedcom portal
*
* This function will print the HTML to link the current gedcom title back to the
* gedcom Home Page
* @author John Finlay
*/
function print_gedcom_title_link($InHeader=FALSE) {
	global $GEDCOM_TITLE;
	echo "<a href=\"index.php?ctype=gedcom\" class=\"gedcomtitle\">", PrintReady($GEDCOM_TITLE, $InHeader), "</a>";
}

//-- function to print a privacy error with contact method
function print_privacy_error() {
	$user_id=get_gedcom_setting(WT_GED_ID, 'CONTACT_USER_ID');
	$method=get_user_setting($user_id, 'contactmethod');
	$fullname=getUserFullName($user_id);

	echo '<div class="error">', i18n::translate('This information is private and cannot be shown.'), '</div>';
	switch ($method) {
	case 'none':
		break;
	case 'mailto':
		$email=getUserEmail($user_id);
		echo '<div class="error">', i18n::translate('For more information contact'), ' ', '<a href="mailto:'.htmlspecialchars($email).'">'.htmlspecialchars($fullname).'</a>', '</div>';
		break;
	default:
		echo '<div class="error">', i18n::translate('For more information contact'), ' ', "<a href='javascript:;' onclick='message(\"".get_user_name($user_id)."\", \"{$method}\");return false;'>{$fullname}</a>", '</div>';
		break;
	}
}

// Print a link for a popup help window
function help_link($help_topic, $module='') {
	global $WT_USE_HELPIMG, $WT_IMAGES, $SEARCH_SPIDER;

	if ($_SESSION['show_context_help']) {
		return
			'<a class="help" tabindex="-1" href="javascript: '.$help_topic.'" onclick="helpPopup(\''.$help_topic.'\',\''.$module.'\'); return false;">&nbsp;'.
			($WT_USE_HELPIMG ?  '<img src="'.$WT_IMAGES['help'].'" class="icon" width="15" height="15" alt="" />' : i18n::translate('?')).
			'&nbsp;</a>';
	} else {
		return '';
	}
}

// Embed global variables and constants in a string.
// Variables can be specified explicitly with #GLOBALS[variable]# or implicity with #variable#
// This function is used by the blocks.
// TODO: There are potential security risks - authorised users may determine the value of
// site configuration settings.  Also, this logic is legacy, from PGV.  The blocks need better
// handling of I18N.  Perhaps separate texts for each language?
function embed_globals($text) {
	if (preg_match_all('/#GLOBALS\[([A-Za-z_][A-Za-z0-9_]*)\]#/', $text, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			if (isset($GLOBALS[$match[1]])) {
				$text=str_replace($match[0], $GLOBALS[$match[1]], $text);
			}
		}
	}
	if (preg_match_all('/#([A-Za-z_][A-Za-z0-9_]*)#/', $text, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			if (isset($GLOBALS[$match[1]])) {
				$text=str_replace($match[0], $GLOBALS[$match[1]], $text);
			} elseif (defined($match[1])) {
				$text=str_replace($match[0], constant($match[1]), $text);
			}
		}
	}
	return $text;
}

//-------------------------------------------------------------------------------------------------------------
// switches between left and rigth align on chosen text direction
//-------------------------------------------------------------------------------------------------------------
function write_align_with_textdir_check($t_dir, $return=false)
{
	global $TEXT_DIRECTION;
	$out = "";
	if ($t_dir == "left")
	{
		if ($TEXT_DIRECTION == "ltr")
		{
			$out .= " style=\"text-align:left; \" ";
		}
		else
		{
			$out .= " style=\"text-align:right; \" ";
		}
	}
	else
	{
		if ($TEXT_DIRECTION == "ltr")
		{
			$out .= " style=\"text-align:right; \" ";
		}
		else
		{
			$out .= " style=\"text-align:left; \" ";
		}
	}
	if ($return) return $out;
	echo $out;
}

/**
* Prepare text with parenthesis for printing
* Convert & to &amp; for xhtml compliance
*
* @param string $text to be printed
*/
function PrintReady($text, $InHeaders=false, $trim=true) {
	global $action, $firstname, $lastname, $place, $year;
	global $TEXT_DIRECTION_array, $TEXT_DIRECTION, $controller;
	// Check whether Search page highlighting should be done or not
	if (isset($controller) && $controller instanceof WT_Controller_Search && $controller->query) {
		$query=$controller->query;
		$HighlightOK=true;
	} else {
		$query='';
		$HighlightOK=false;
	}
	//-- convert all & to &amp;
	$text = str_replace("&", "&amp;", $text);
	//$text = preg_replace(array("/&/", "/</", "/>/"), array("&amp;", "&lt;", "&gt;"), $text);
	//-- make sure we didn't double convert existing HTML entities like so:  &foo; to &amp;foo;
	$text = preg_replace("/&amp;(\w+);/", "&$1;", $text);
		if ($trim) $text = trim($text);
		//-- if we are on the search page body, then highlight any search hits
		//  In this routine, we will assume that the input string doesn't contain any
		//  \x01 or \x02 characters.  We'll represent the <span class="search_hit"> by \x01
		//  and </span> by \x02.  We will translate these \x01 and \x02 into their true
		//  meaning at the end.
		//
		//  This special handling is required in case the user has submitted a multiple
		//  argument search, in which the second or later arguments can be found in the
		//  <span> or </span> strings.
		if ($HighlightOK) {
			$queries = explode(" ", $query);
			$newtext = $text;
			$hasallhits = true;
			foreach ($queries as $index=>$query1) {
				$query1esc=preg_quote($query1, '/');
				if (@preg_match("/(".$query1esc.")/i", $text)) { // Use @ as user-supplied query might be invalid.
					$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
				}
				else if (@preg_match("/(".utf8_strtoupper($query1esc).")/", utf8_strtoupper($text))) {
					$nlen = strlen($query1);
					$npos = strpos(utf8_strtoupper($text), utf8_strtoupper($query1));
					$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
					$newtext = substr_replace($newtext, "\x01", $npos, 0);
				}
				else $hasallhits = false;
			}
			if ($hasallhits) $text = $newtext;
			if (isset($action) && ($action === "soundex")) {
				if (isset($firstname)) {
					$queries = explode(" ", $firstname);
					$newtext = $text;
					$hasallhits = true;
					foreach ($queries as $index=>$query1) {
						$query1esc=preg_quote($query1, '/');
						if (preg_match("/(".$query1esc.")/i", $text)) {
							$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
						}
						else if (preg_match("/(".utf8_strtoupper($query1esc).")/", utf8_strtoupper($text))) {
							$nlen = strlen($query1);
							$npos = strpos(utf8_strtoupper($text), utf8_strtoupper($query1));
							$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
							$newtext = substr_replace($newtext, "\x01", $npos, 0);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
				if (isset($lastname)) {
					$queries = explode(" ", $lastname);
					$newtext = $text;
					$hasallhits = true;
					foreach ($queries as $index=>$query1) {
						$query1esc=preg_quote($query1, '/');
						if (preg_match("/(".$query1esc.")/i", $text)) {
							$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
						}
						else if (preg_match("/(".utf8_strtoupper($query1esc).")/", utf8_strtoupper($text))) {
							$nlen = strlen($query1);
							$npos = strpos(utf8_strtoupper($text), utf8_strtoupper($query1));
							$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
							$newtext = substr_replace($newtext, "\x01", $npos, 0);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
				if (isset($place)) {
					$queries = explode(" ", $place);
					$newtext = $text;
					$hasallhits = true;
					foreach ($queries as $index=>$query1) {
						$query1esc=preg_quote($query1, '/');
						if (preg_match("/(".$query1esc.")/i", $text)) {
							$newtext = preg_replace("/(".$query1esc.")/i", "\x01$1\x02", $newtext);
						}
						else if (preg_match("/(".utf8_strtoupper($query1esc).")/", utf8_strtoupper($text))) {
							$nlen = strlen($query1);
							$npos = strpos(utf8_strtoupper($text), utf8_strtoupper($query1));
							$newtext = substr_replace($newtext, "\x02", $npos+$nlen, 0);
							$newtext = substr_replace($newtext, "\x01", $npos, 0);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
				if (isset($year)) {
					$queries = explode(" ", $year);
					$newtext = $text;
					$hasallhits = true;
					foreach ($queries as $index=>$query1) {
						$query1=preg_quote($query1, '/');
						if (preg_match("/(".$query1.")/i", $text)) {
							$newtext = preg_replace("/(".$query1.")/i", "\x01$1\x02", $newtext);
						}
						else $hasallhits = false;
					}
					if ($hasallhits) $text = $newtext;
				}
			}
			// All the "Highlight start" and "Highlight end" flags are set:
			//  Delay the final clean-up and insertion of proper <span> and </span>
			//  until parentheses, braces, and brackets have been processed
		}

	// Look for strings enclosed in parentheses, braces, or brackets.
	//
	// Parentheses, braces, and brackets have weak directionality and aren't handled properly
	// when they enclose text whose directionality differs from that of the page.
	//
	// To correct the problem, we need to enclose the parentheses, braces, or brackets with
	// zero-width characters (&lrm; or &rlm;) having a directionality that matches the
	// directionality of the text that is enclosed by the parentheses, etc.
	$charPos = 0;
	$lastChar = strlen($text);
	$newText = "";
	while (true) {
		if ($charPos > $lastChar) break;
		$thisChar = substr($text, $charPos, 1);
		$charPos ++;
		if ($thisChar=="(" || $thisChar=="{" || $thisChar=="[") {
			$tempText = "";
			while (true) {
				$tempChar = "";
				if ($charPos > $lastChar) break;
				$tempChar = substr($text, $charPos, 1);
				$charPos ++;
				if ($tempChar==")" || $tempChar=="}" || $tempChar=="]") break;
				$tempText .= $tempChar;
			}
			if (utf8_direction($tempText)=='rtl') {
				$newText .= getRLM() . $thisChar . $tempText . $tempChar . getRLM();
			} else {
				$newText .= getLRM() . $thisChar . $tempText . $tempChar . getLRM();
			}
		} else {
			$newText .= $thisChar;
		}
	}
	$text = $newText;

	// Parentheses, braces, and brackets have been processed:
	// Finish processing of "Highlight Start and "Highlight end"
	if (!$InHeaders) {
		$text = str_replace(array("\x02\x01", "\x02 \x01", "\x01", "\x02"), array("", " ", "<span class=\"search_hit\">", "</span>"), $text);
	} else {
		$text = str_replace(array("\x02\x01", "\x02 \x01", "\x01", "\x02"), array("", " ", "", ""), $text);
	}
	return $text;
}
/**
* print ASSO RELA information
*
* Ex1:
* <code>1 ASSO @I1@
* 2 RELA Twin</code>
*
* Ex2:
* <code>1 CHR
* 2 ASSO @I1@
* 3 RELA Godfather
* 2 ASSO @I2@
* 3 RELA Godmother</code>
*
* @param string $pid person or family ID
* @param string $factrec the raw gedcom record to print
* @param string $linebr optional linebreak
*/
function print_asso_rela_record($pid, $factrec, $linebr=false, $type='INDI') {
	global $WT_IMAGES;

	// Level 1 ASSO
	if (preg_match('/^1 ASSO @('.WT_REGEX_XREF.')@((\n[2-9].*)*)/', $factrec, $amatch)) {
		$person=WT_Person::getInstance($amatch[1]);
		$sex='';
		if ($person) {
			$name=$person->getFullName();
			$sex=$person->getSex();
			switch ($type) {
			case 'INDI':
				$relationship=get_relationship_name(get_relationship($pid, $amatch[1], true, 4));
				if (!$relationship) {
					$relationship=i18n::translate('Relationship chart');
				}
				$relationship=' - <a href="relationship.php?pid1='.$pid.'&amp;pid2='.$amatch[1].'&amp;ged='.WT_GEDURL.'">'.$relationship.'</a>';
				break;
			case 'FAM':
				$relationship='';
				$famrec = find_family_record($pid, WT_GED_ID);
				if ($famrec) {
					$parents = find_parents_in_record($famrec);
					if ($parents["HUSB"]) {
						$relationship1=get_relationship_name(get_relationship($parents["HUSB"], $amatch[1], true, 4));
						if (!$relationship1) {
							$relationship1=i18n::translate('Relationship chart');
						}
						$relationship.=' - <a href="relationship.php?pid1='.$parents["HUSB"].'&amp;pid2='.$amatch[1].'&amp;ged='.WT_GEDURL.'">'.$relationship1.'<img src="'.$WT_IMAGES['sex_m_9x9'].'" class="gender_image" /></a>';
					}
					if ($parents["WIFE"]) {
						$relationship2=get_relationship_name(get_relationship($parents["WIFE"], $amatch[1], true, 4));
						if (!$relationship2) {
							$relationship2=i18n::translate('Relationship chart');
						}
						$relationship.=' - <a href="relationship.php?pid1='.$parents["WIFE"].'&amp;pid2='.$amatch[1].'&amp;ged='.WT_GEDURL.'">'.$relationship2.'<img src="'.$WT_IMAGES['sex_f_9x9'].'" class="gender_image" /></a>';
					}
				}
				break;
			}
		} else {
			$name=$amatch[1];
			$relationship='';
		}
		if (preg_match('/\n2 RELA (.+)/', $amatch[2], $rmatch)) {
			$label='<br /><span class="label">'.i18n::translate('Relationship').':</span> '.translate_rela($rmatch[1], $sex);
		} else {
			$label='';
		}
		echo '<a href="', $person->getHtmlUrl().'">', $name, $relationship, '</a><br />', $label;
	}

	// Level 2 ASSO
	preg_match_all('/\n2 ASSO @('.WT_REGEX_XREF.')@(\n[3-9].*)*/', $factrec, $amatches, PREG_SET_ORDER);
	foreach ($amatches as $amatch) {
		$person=WT_Person::getInstance($amatch[1]);
		if ($person) $sex=$person->getSex();
		else $sex='';
		if (preg_match('/\n3 RELA (.+)/', $amatch[0], $rmatch)) {
			$label='<span class="label">'.translate_rela($rmatch[1], $sex).':</span> ';
		} else {
			$label='';
		}
		if ($person) {
			$name=$person->getFullName();
			switch ($type) {
			case 'INDI':
				if (preg_match('/^1 _[A-Z]+_[A-Z]+/', $factrec)) {
					// An automatically generated "event of a close relative"
					preg_match('/\n3 RELA (.+)/', $amatch[0], $rmatch);
					$relationship=get_relationship_name_from_path($rmatch[1], $pid, $amatch[1]);
					$label='';
				} else {
					// An naturally occuring ASSO event
					$relationship=get_relationship_name(get_relationship($pid, $amatch[1], true, 4, true));
					if (!$relationship) {
						$relationship=i18n::translate('Relationship chart');
					}
				}
				$relationship=' - <a href="relationship.php?pid1='.$pid.'&amp;pid2='.$amatch[1].'&amp;ged='.WT_GEDURL.'">'.$relationship.'</a>';
				break;
			case 'FAM':
				$relationship='';
				$famrec = find_family_record($pid, WT_GED_ID);
				if ($famrec) {
					$parents = find_parents_in_record($famrec);
					if ($parents["HUSB"]) {
						$relationship1=get_relationship_name(get_relationship($parents["HUSB"], $amatch[1], true, 4));
						if (!$relationship1) {
							$relationship1=i18n::translate('Relationship chart');
						}
						$relationship.=' - <a href="relationship.php?pid1='.$parents["HUSB"].'&amp;pid2='.$amatch[1].'&amp;ged='.WT_GEDURL.'">'.$relationship1.'<img src="'.$WT_IMAGES['sex_m_9x9'].'" class="gender_image" /></a>';
					}
					if ($parents["WIFE"]) {
						$relationship2=get_relationship_name(get_relationship($parents["WIFE"], $amatch[1], true, 4));
						if (!$relationship2) {
							$relationship2=i18n::translate('Relationship chart');
						}
						$relationship.=' - <a href="relationship.php?pid1='.$parents["WIFE"].'&amp;pid2='.$amatch[1].'&amp;ged='.WT_GEDURL.'">'.$relationship2.'<img src="'.$WT_IMAGES['sex_f_9x9'].'" class="gender_image" /></a>';
					}
				}
				break;
			}
			echo '<br/>', $label, '<a href="', $person->getHtmlUrl().'">', $name, '</a>', $relationship;
		} else {
			echo '<br/>', $label, $amatch[1];
		}
	}
}

/**
* Format age of parents in HTML
*
* @param string $pid child ID
*/
function format_parents_age($pid, $birth_date=null) {
	global $SHOW_PARENTS_AGE;

	$html='';
	if ($SHOW_PARENTS_AGE) {
		$person=WT_Person::getInstance($pid);
		$families=$person->getChildFamilies();
		// Where an indi has multiple birth records, we need to know the
		// date of it.  For person boxes, etc., use the default birth date.
		if (is_null($birth_date)) {
			$birth_date=$person->getBirthDate();
		}
		// Multiple sets of parents (e.g. adoption) cause complications, so ignore.
		if ($birth_date->isOK() && count($families)==1) {
			$family=current($families);
			// Allow for same-sex parents
			foreach (array($family->getHusband(), $family->getWife()) as $parent) {
				if ($parent && $age=GedcomDate::GetAgeYears($parent->getBirthDate(), $birth_date)) {
					$deatdate=$parent->getDeathDate();
					$class='';
					switch ($parent->getSex()) {
					case 'F':
						// Highlight mothers who die in childbirth or shortly afterwards
						if ($deatdate->isOK() && $deatdate->MinJD()<$birth_date->MinJD()+90) {
							$class='parentdeath';
							$title=translate_fact('_DEAT_PARE', $parent);
						} else {
							$title=i18n::translate('Mother\'s age');
						}
						break;
					case 'M':
						// Highlight fathers who die before the birth
						if ($deatdate->isOK() && $deatdate->MinJD()<$birth_date->MinJD()) {
							$class='parentdeath';
							$title=translate_fact('_DEAT_PARE', $parent);
						} else {
							$title=i18n::translate('Father\'s age');
						}
						break;
					default:
						$title=i18n::translate('Parent\'s age');
						break;
					}
					if ($class) {
						$html.=' <span class="'.$class.'" title="'.$title.'">'.$parent->getSexImage().$age.'</span>';
					} else {
						$html.=' <span title="'.$title.'">'.$parent->getSexImage().$age.'</span>';
					}
				}
			}
			if ($html) {
				$html='<span class="age">'.$html.'</span>';
			}
		}
	}
	return $html;
}
/**
* print fact DATE TIME
*
* @param Event $eventObj Event to print the date for
* @param boolean $anchor option to print a link to calendar
* @param boolean $time option to print TIME value
*/
function format_fact_date(&$eventObj, $anchor=false, $time=false) {
	global $pid, $SEARCH_SPIDER;
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	if (!is_object($eventObj)) trigger_error("Must use Event object", E_USER_WARNING);
	$factrec = $eventObj->getGedcomRecord();
	$html='';
	// Recorded age
	$fact_age=get_gedcom_value('AGE', 2, $factrec);
	if ($fact_age=='')
		$fact_age=get_gedcom_value('DATE:AGE', 2, $factrec);
	$husb_age=get_gedcom_value('HUSB:AGE', 2, $factrec);
	$wife_age=get_gedcom_value('WIFE:AGE', 2, $factrec);

	// Calculated age
	if (preg_match('/2 DATE (.+)/', $factrec, $match)) {
		$date=new GedcomDate($match[1]);
		$html.=' '.$date->Display($anchor && !$SEARCH_SPIDER);
		// time
		if ($time) {
			$timerec=get_sub_record(2, '2 TIME', $factrec);
			if ($timerec=='') {
				$timerec=get_sub_record(2, '2 DATE', $factrec);
			}
			if (preg_match('/[2-3] TIME (.*)/', $timerec, $tmatch)) {
				$html.=' - <span class="date">'.$tmatch[1].'</span>';
			}
		}
		$fact = $eventObj->getTag();
		$person = $eventObj->getParentObject();
		if (!is_null($person) && $person->getType()=='INDI') {
			// age of parents at child birth
			if ($fact=='BIRT') {
				$html .= format_parents_age($person->getXref(), $date);
			}
			// age at event
			else if ($fact!='CHAN' && $fact!='_TODO') {
				$birth_date=$person->getBirthDate();
				// Can't use getDeathDate(), as this also gives BURI/CREM events, which
				// wouldn't give the correct "days after death" result for people with
				// no DEAT.
				$death_event=$person->getFactByType('DEAT');
				if ($death_event) {
					$death_date=$death_event->getDate();
				} else {
					$death_date=new GedcomDate('');
				}
				$ageText = '';
				if ((GedcomDate::Compare($date, $death_date)<=0 || !$person->isDead()) || $fact=='DEAT') {
					// Before death, print age
					$age=GedcomDate::GetAgeGedcom($birth_date, $date);
					// Only show calculated age if it differs from recorded age
					if ($age!='') {
						if (
							$fact_age!='' && $fact_age!=$age ||
							$fact_age=='' && $husb_age=='' && $wife_age=='' ||
							$husb_age!='' && $person->getSex()=='M' && $husb_age!=$age ||
							$wife_age!='' && $person->getSex()=='F' && $wife_age!=$age
						) {
							if ($age!="0d") {
								$ageText = '('.i18n::translate('Age').' '.get_age_at_event($age, false).')';
							}
						}
					}
				}
				if ($fact!='DEAT' && GedcomDate::Compare($date, $death_date)>=0) {
					// After death, print time since death
					$age=get_age_at_event(GedcomDate::GetAgeGedcom($death_date, $date), true);
					if ($age!='') {
						if (GedcomDate::GetAgeGedcom($death_date, $date)=="0d") {
							$ageText = '('.i18n::translate('on the date of death').')';
						} else {
							$ageText = '('.$age.' '.i18n::translate('after death').')';
						}
					}
				}
				if ($ageText!='') $html .= '<span class="age"> '.PrintReady($ageText).'</span>';
			}
		}
		else if (!is_null($person) && $person->getType()=='FAM') {
			$indirec=find_person_record($pid, $ged_id);
			$indi=new WT_Person($indirec);
			$birth_date=$indi->getBirthDate();
			$death_date=$indi->getDeathDate();
			$ageText = '';
			if (GedcomDate::Compare($date, $death_date)<=0) {
				$age=GedcomDate::GetAgeGedcom($birth_date, $date);
				// Only show calculated age if it differs from recorded age
				if ($age!='' && $age>0) {
					if (
						$fact_age!='' && $fact_age!=$age ||
						$fact_age=='' && $husb_age=='' && $wife_age=='' ||
						$husb_age!='' && $indi->getSex()=='M' && $husb_age!= $age ||
						$wife_age!='' && $indi->getSex()=='F' && $wife_age!=$age
					) {
						$ageText = '('.i18n::translate('Age').' '.get_age_at_event($age, false).')';
					}
				}
			}
			if ($ageText!='') $html .= '<span class="age"> '.PrintReady($ageText).'</span>';
		}
	} else {
		// 1 DEAT Y with no DATE => print YES
		// 1 DEAT N is not allowed
		// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
		$factrec = str_replace("\nWT_OLD\n", '', $factrec);
		$factrec = str_replace("\nWT_NEW\n", '', $factrec);
		$factdetail = explode(' ', trim($factrec));
		if (isset($factdetail)) if (count($factdetail) == 3) if (strtoupper($factdetail[2]) == 'Y') {
			$html.=i18n::translate('Yes');
		}
	}
	// print gedcom ages
	foreach (array(translate_fact('AGE')=>$fact_age, translate_fact('HUSB')=>$husb_age, translate_fact('WIFE')=>$wife_age) as $label=>$age) {
		if ($age!='') {
			$html.=' <span class="label">'.$label.':</span> <span class="age">'.PrintReady(get_age_at_event($age, false)).'</span>';
		}
	}
	return $html;
}
/**
* print fact PLACe TEMPle STATus
*
* @param Event $eventObj gedcom fact record
* @param boolean $anchor option to print a link to placelist
* @param boolean $sub option to print place subrecords
* @param boolean $lds option to print LDS TEMPle and STATus
*/
function format_fact_place(&$eventObj, $anchor=false, $sub=false, $lds=false) {
	global $SHOW_PEDIGREE_PLACES, $TEMPLE_CODES, $SEARCH_SPIDER, $STATUS_CODES;
	if ($eventObj==null) return '';
	if (!is_object($eventObj)) {
		trigger_error("Object was not sent in, please use Event object", E_USER_WARNING);
		$factrec = $eventObj;
	}
	else $factrec = $eventObj->getGedcomRecord();
	$html='';

	$ct = preg_match("/2 PLAC (.*)/", $factrec, $match);
	if ($ct>0) {
		$html.=' ';
		$levels = explode(', ', $match[1]);
		if ($anchor && (empty($SEARCH_SPIDER))) {
			$place = trim($match[1]);
			// reverse the array so that we get the top level first
			$levels = array_reverse($levels);
			$tempURL = "placelist.php?action=show&amp;";
			foreach ($levels as $pindex=>$ppart) {
				$tempURL .= "parent[{$pindex}]=".rawurlencode($ppart).'&amp;';
			}
			$tempURL .= 'level='.count($levels);
			$html .= '<a href="'.$tempURL.'"> '.PrintReady($place).'</a>';
		} else {
			if (!$SEARCH_SPIDER) {
				$html.=' -- ';
			}
			for ($level=0; $level<$SHOW_PEDIGREE_PLACES; $level++) {
				if (!empty($levels[$level])) {
					if ($level>0) {
						$html.=", ";
					}
					$html.=PrintReady($levels[$level]);
				}
			}
		}
	} else {
		$place='???';
	}
	$ctn=0;
	if ($sub) {
		$placerec = get_sub_record(2, '2 PLAC', $factrec);
		if (!empty($placerec)) {
			$cts = preg_match('/\d ROMN (.*)/', $placerec, $match);
			if ($cts>0) {
				if ($ct>0) {
					$html.=" - ";
				}
				$html.=' '.PrintReady($match[1]);
			}
			$cts = preg_match('/\d _HEB (.*)/', $placerec, $match);
			if ($cts>0) {
				if ($ct>0) {
					$html.=' - ';
				}
				$html.=' '.PrintReady($match[1]);
			}
			$map_lati="";
			$cts = preg_match('/\d LATI (.*)/', $placerec, $match);
			if ($cts>0) {
				$map_lati=$match[1];
				$html.='<br /><span class="label">'.translate_fact('LATI').': </span>'.$map_lati;
			}
			$map_long="";
			$cts = preg_match('/\d LONG (.*)/', $placerec, $match);
			if ($cts>0) {
				$map_long=$match[1];
				$html.=' <span class="label">'.translate_fact('LONG').': </span>'.$map_long;
			}
			if ($map_lati && $map_long && empty($SEARCH_SPIDER)) {
				$map_lati=trim(strtr($map_lati, "NSEW,�", " - -. ")); // S5,6789 ==> -5.6789
				$map_long=trim(strtr($map_long, "NSEW,�", " - -. ")); // E3.456� ==> 3.456
				$html.=' <a target="_BLANK" href="'."http://www.mapquest.com/maps/map.adp?searchtype=address&amp;formtype=latlong&amp;latlongtype=decimal&amp;latitude={$map_lati}&amp;longitude={$map_long}".'"><img src="images/mapq.gif" border="0" alt="Mapquest &copy;" title="Mapquest &copy;" /></a>';
				$html.=' <a target="_BLANK" href="'."http://maps.google.com/maps?q={$map_lati},{$map_long}(".rawurlencode($place).")".'"><img src="images/bubble.gif" border="0" alt="Google Maps &copy;" title="Google Maps &copy;" /></a>';
				$html.=' <a target="_BLANK" href="'."http://www.multimap.com/map/browse.cgi?lat={$map_lati}&amp;lon={$map_long}&amp;scale=&amp;icon=x".'"><img src="images/multim.gif" border="0" alt="Multimap &copy;" title="Multimap &copy;" /></a>';
				$html.=' <a target="_BLANK" href="'."http://www.terraserver.com/imagery/image_gx.asp?cpx={$map_long}&amp;cpy={$map_lati}&amp;res=30&amp;provider_id=340".'"><img src="images/terrasrv.gif" border="0" alt="TerraServer &copy;" title="TerraServer &copy;" /></a>';
			}
			if (preg_match('/\d NOTE (.*)/', $placerec, $match)) {
				ob_start();
				print_fact_notes($placerec, 3);
				$html.='<br />'.ob_get_contents();
				ob_end_clean();
			}
		}
	}
	if ($lds) {
		if (preg_match('/2 TEMP (.*)/', $factrec, $match)) {
			$tcode=trim($match[1]);
			if (array_key_exists($tcode, $TEMPLE_CODES)) {
				$html.='<br/>'.i18n::translate('LDS Temple').': '.$TEMPLE_CODES[$tcode];
			} else {
				$html.='<br/>'.i18n::translate('LDS Temple Code:').$tcode;
			}
		}
		if (preg_match('/2 STAT (.*)/', $factrec, $match)) {
			$html.='<br />'.i18n::translate('Status').': '.(array_key_exists($match[1], $STATUS_CODES) ? $STATUS_CODES[$match[1]] : $match[1]);
			if (preg_match('/3 DATE (.*)/', $factrec, $match)) {
				$date=new GedcomDate($match[1]);
				$html.=', '.translate_fact('STAT:DATE').': '.$date->Display(false);
			}
		}
	}
	return $html;
}
/**
* print first major fact for an Individual
*
* @param string $key indi pid
*/
function format_first_major_fact($key, $majorfacts = array("BIRT", "CHR", "BAPM", "DEAT", "BURI", "BAPL", "ADOP")) {
	global $TEXT_DIRECTION;

	$html='';
	$person = WT_GedcomRecord::getInstance($key);
	if (is_null($person)) return;
	foreach ($majorfacts as $indexval => $fact) {
		$event = $person->getFactByType($fact);
		if (!is_null($event) && $event->hasDatePlace() && $event->canShow()) {
			$html.='<span dir="'.$TEXT_DIRECTION.'"><br /><i>';
			$html .= $event->getLabel();
			$html.=' '.format_fact_date($event).format_fact_place($event).'</i></span>';
			break;
		}
	}
	return $html;
}

/**
* Check for facts that may exist only once for a certain record type.
* If the fact already exists in the second array, delete it from the first one.
*/
function CheckFactUnique($uniquefacts, $recfacts, $type) {
	foreach ($recfacts as $indexval => $factarray) {
		$fact=false;
		if (is_object($factarray)) {
			/* @var $factarray Event */
			$fact = $factarray->getTag();
		}
		else {
			if (($type == "SOUR") || ($type == "REPO")) $factrec = $factarray[0];
			if (($type == "FAM") || ($type == "INDI")) $factrec = $factarray[1];

		$ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
		if ($ft>0) {
			$fact = trim($match[1]);
			}
		}
		if ($fact!==false) {
			$key = array_search($fact, $uniquefacts);
			if ($key !== false) unset($uniquefacts[$key]);
		}
	}
	return $uniquefacts;
}

/**
* Print a new fact box on details pages
* @param string $id the id of the person, family, source etc the fact will be added to
* @param array $usedfacts an array of facts already used in this record
* @param string $type the type of record INDI, FAM, SOUR etc
*/
function print_add_new_fact($id, $usedfacts, $type) {
	global $TEXT_DIRECTION;

	// -- Add from clipboard
	if (!empty($_SESSION["clipboard"])) {
		$newRow = true;
		foreach (array_reverse($_SESSION["clipboard"], true) as $key=>$fact) {
			if ($fact["type"]==$type || $fact["type"]=='all') {
				if ($newRow) {
					$newRow = false;
					echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, '">';
					echo help_link('add_from_clipboard');
					echo i18n::translate('Add from clipboard'), '</td>';
					echo '<td class="optionbox wrap"><form method="get" name="newFromClipboard" action="" onsubmit="return false;">';
					echo '<select id="newClipboardFact" name="newClipboardFact">';
				}
				$fact_type=i18n::translate($fact['fact']);
				echo '<option value="clipboard_', $key, '">', $fact_type;
				// TODO use the event class to store/parse the clipboard events
				if (preg_match('/^2 DATE (.+)/m', $fact['factrec'], $match)) {
					$tmp=new GedcomDate($match[1]);
					echo '; ', $tmp->minDate()->Format('%Y');
				}
				if (preg_match('/^2 PLAC ([^,\n]+)/m', $fact['factrec'], $match)) {
					echo '; ', $match[1];
				}
				echo '</option>';
			}
		}
		if (!$newRow) {
			echo '</select>';
			echo '&nbsp;&nbsp;<input type="button" value="', i18n::translate('Add'), "\" onclick=\"addClipboardRecord('$id', 'newClipboardFact');\" /> ";
			echo '</form></td></tr>', "\n";
		}
	}

	// -- Add from pick list
	switch ($type) {
	case "INDI":
		$addfacts   =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'INDI_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "FAM":
		$addfacts   =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_ADD'),     -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_UNIQUE'),  -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'FAM_FACTS_QUICK'),   -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "SOUR":
		$addfacts   =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'SOUR_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "NOTE":
		$addfacts   =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'NOTE_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'NOTE_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'NOTE_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	case "REPO":
		$addfacts   =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_ADD'),    -1, PREG_SPLIT_NO_EMPTY);
		$uniquefacts=preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
		$quickfacts =preg_split("/[, ;:]+/", get_gedcom_setting(WT_GED_ID, 'REPO_FACTS_QUICK'),  -1, PREG_SPLIT_NO_EMPTY);
		break;
	default:
		return;
	}
	$addfacts=array_merge(CheckFactUnique($uniquefacts, $usedfacts, $type), $addfacts);
	$quickfacts=array_intersect($quickfacts, $addfacts);
	$translated_addfacts=array();
	foreach ($addfacts as $addfact) {
		$translated_addfacts[$addfact] = translate_fact($addfact);
	}
	uasort($translated_addfacts, "factsort");
	echo "<tr><td class=\"descriptionbox ", $TEXT_DIRECTION, "\">";
	echo i18n::translate('Add new fact');
	echo help_link('add_facts'), "</td>";
	echo "<td class=\"optionbox wrap ", $TEXT_DIRECTION, "\">";
	echo "<form method=\"get\" name=\"newfactform\" action=\"\" onsubmit=\"return false;\">";
	echo "<select id=\"newfact\" name=\"newfact\">";
	foreach ($translated_addfacts as $fact=>$fact_name) {
		echo '<option value="', $fact, '">', i18n::translate('%1$s [%2$s]', $fact_name, $fact), '</option>';
	}
	if (($type == "INDI") || ($type == "FAM")) echo "<option value=\"EVEN\">", i18n::translate('Custom Event'), " [EVEN]</option>";
	echo "</select>";
	echo "&nbsp;&nbsp;<input type=\"button\" value=\"", i18n::translate('Add'), "\" onclick=\"add_record('$id', 'newfact');\" /> ";
	foreach ($quickfacts as $fact) echo "&nbsp;<small><a href='javascript://$fact' onclick=\"add_new_record('$id', '$fact');return false;\">", translate_fact($fact), "</a></small>&nbsp;";
	echo "</form>";
	echo "</td></tr>";
}

/**
* javascript declaration for calendar popup
*
* @param none
*/
function init_calendar_popup() {
	global $WEEK_START;

	echo
		WT_JS_START,
		'cal_setMonthNames(',
			'"', i18n::translate_c('NOMINATIVE', 'January'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'February'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'March'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'April'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'May'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'June'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'July'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'August'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'September'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'October'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'November'), '",',
			'"', i18n::translate_c('NOMINATIVE', 'December'), '");',
			'cal_setDayHeaders(',
			'"', i18n::translate('Sun'), '",',
			'"', i18n::translate('Mon'), '",',
			'"', i18n::translate('Tue'), '",',
			'"', i18n::translate('Wed'), '",',
			'"', i18n::translate('Thu'), '",',
			'"', i18n::translate('Fri'), '",',
			'"', i18n::translate('Sat'), '");',
			'cal_setWeekStart(', $WEEK_START, ');',
			WT_JS_END;
}

/**
* prints a link to open the Find Special Character window
* @param string $element_id the ID of the element the value will be pasted to
* @param string $indiname the id of the element the name should be pasted to
* @param boolean $asString Whether or not the HTML should be returned as a string or printed
* @param boolean $multiple Whether or not the user will be selecting multiple people
* @param string $ged The GEDCOM to search in
*/
function print_findindi_link($element_id, $indiname, $asString=false, $multiple=false, $ged='', $filter='') {
	global $WT_IMAGES, $GEDCOM;

	$text = i18n::translate('Find individual ID');
	if (empty($ged)) $ged=$GEDCOM;
	if (isset($WT_IMAGES["button_indi"])) $Link = "<img src=\"".$WT_IMAGES["button_indi"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findIndi(document.getElementById('".$element_id."'), document.getElementById('".$indiname."'), '".$multiple."', '".$ged."', '".$filter."'); findtype='individual'; return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findplace_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = i18n::translate('Find Place');
	if (isset($WT_IMAGES["button_place"])) $Link = "<img src=\"".$WT_IMAGES["button_place"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findPlace(document.getElementById('".$element_id."'), '".$ged."'); return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findfamily_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = i18n::translate('Find Family ID');
	if (isset($WT_IMAGES["button_family"])) $Link = "<img src=\"".$WT_IMAGES["button_family"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findFamily(document.getElementById('".$element_id."'), '".$ged."'); return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_specialchar_link($element_id, $vert, $asString=false) {
	global $WT_IMAGES;

	$text = i18n::translate('Find Special Characters');
	if (isset($WT_IMAGES["button_keyboard"])) $Link = "<img id=\"".$element_id."_spec\" name=\"".$element_id."_spec\" src=\"".$WT_IMAGES["button_keyboard"]."\"  alt=\"".$text."\"  title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findSpecialChar(document.getElementById('".$element_id."')); updatewholename(); return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_autopaste_link($element_id, $choices, $concat=1, $name=1, $submit=0) {
	echo "<small>";
	foreach ($choices as $indexval => $choice) {
		echo " &nbsp;<a href=\"javascript:;\" onclick=\"document.getElementById('", $element_id, "').value ";
		if ($concat) echo "+=' "; else echo "='";
		echo $choice, "'; ";
		if ($name) echo " updatewholename();";
		if ($submit) echo " document.forms[0].submit();";
		echo " return false;\" tabindex=\"-1\">", $choice, "</a>";
	}
	echo "</small>";
}

function print_findsource_link($element_id, $sourcename="", $asString=false, $ged='') {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = i18n::translate('Find Source ID');
	if (isset($WT_IMAGES["button_source"])) $Link = "<img src=\"".$WT_IMAGES["button_source"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findSource(document.getElementById('".$element_id."'), document.getElementById('".$sourcename."'), '".$ged."'); findtype='source'; return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findnote_link($element_id, $notename="", $asString=false, $ged='') {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = i18n::translate('Find Shared Note');
	if (isset($WT_IMAGES["button_note"])) $Link = "<img src=\"".$WT_IMAGES["button_note"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findnote(document.getElementById('".$element_id."'), document.getElementById('".$notename."'), '".$ged."'); findtype='note'; return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findrepository_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = i18n::translate('Find Repository');
	if (isset($WT_IMAGES["button_repository"])) $Link = "<img src=\"".$WT_IMAGES["button_repository"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findRepository(document.getElementById('".$element_id."'), '".$ged."'); return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findmedia_link($element_id, $choose="", $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = i18n::translate('Find media');
	if (isset($WT_IMAGES["button_media"])) $Link = "<img src=\"".$WT_IMAGES["button_media"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findMedia(document.getElementById('".$element_id."'), '".$choose."', '".$ged."'); return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findfact_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	$text = i18n::translate('Find fact tag');
	if (empty($ged)) $ged=$GEDCOM;
	if (isset($WT_IMAGES["button_find_facts"])) $Link = "<img src=\"".$WT_IMAGES["button_find_facts"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"top\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findFact(document.getElementById('".$element_id."'), '".$ged."'); return false;\" tabindex=\"-1\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

/**
* get a quick-glance view of current LDS ordinances
* @param string $indirec
* @return string
*/
function get_lds_glance($indirec) {
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);
	$text = "";

	$ord = get_sub_record(1, "1 BAPL", $indirec);
	if ($ord) $text .= "B";
	else $text .= "_";
	$ord = get_sub_record(1, "1 ENDL", $indirec);
	if ($ord) $text .= "E";
	else $text .= "_";
	$found = false;
	$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
	for ($i=0; $i<$ct; $i++) {
		$famrec = find_family_record($match[$i][1], $ged_id);
		if ($famrec) {
			$ord = get_sub_record(1, "1 SLGS", $famrec);
			if ($ord) {
				$found = true;
				break;
			}
		}
	}
	if ($found) $text .= "S";
	else $text .= "_";
	$ord = get_sub_record(1, "1 SLGC", $indirec);
	if ($ord) $text .= "P";
	else $text .= "_";
	return $text;
}

/**
* This function produces a hexadecimal dump of the input string for debugging purposes
*/

function DumpString($input) {
	if (empty($input)) return false;

	$UTF8 = array();
	$hex1L = '';
	$hex1R = '';
	$hex2L = '';
	$hex2R = '';
	$hex3L = '';
	$hex3R = '';
	$hex4L = '';
	$hex4R = '';

	$pos = 0;
	while (true) {
		// Separate the input string into UTF8 characters
		$byte0 = ord(substr($input, $pos, 1));
		$charLen = 1;
		if (($byte0 & 0xE0) == 0xC0) $charLen = 2;  // 2-byte sequence
		if (($byte0 & 0xF0) == 0xE0) $charLen = 3;  // 3-byte sequence
		if (($byte0 & 0xF8) == 0xF0) $charLen = 4;  // 4-byte sequence
		$thisChar = substr($input, $pos, $charLen);
		$UTF8[] = $thisChar;

		// Separate the current UTF8 character into hexadecimal digits
		$byte = bin2hex(substr($thisChar, 0, 1));
		$hex1L .= substr($byte, 0, 1);
		$hex1R .= substr($byte, 1, 1);

		if ($charLen > 1) {
			$byte = bin2hex(substr($thisChar, 1, 1));
			$hex2L .= substr($byte, 0, 1);
			$hex2R .= substr($byte, 1, 1);
		} else {
			$hex2L .= ' ';
			$hex2R .= ' ';
		}

		if ($charLen > 2) {
			$byte = bin2hex(substr($thisChar, 2, 1));
			$hex3L .= substr($byte, 0, 1);
			$hex3R .= substr($byte, 1, 1);
		} else {
			$hex3L .= ' ';
			$hex3R .= ' ';
		}

		if ($charLen > 3) {
			$byte = bin2hex(substr($thisChar, 3, 1));
			$hex4L .= substr($byte, 0, 1);
			$hex4R .= substr($byte, 1, 1);
		} else {
			$hex4L .= ' ';
			$hex4R .= ' ';
		}

		$pos += $charLen;
		if ($pos>=strlen($input)) break;
	}

	$pos = 0;
	$lastPos = count($UTF8);
	$haveByte4 = (trim($hex4L)!='');
	$haveByte3 = (trim($hex3L)!='');
	$haveByte2 = (trim($hex2L)!='');

	// We're ready: now output everything
	echo '<br /><code><span dir="ltr">';
	while (true) {
		$lineLength = $lastPos - $pos;
		if ($lineLength>100) $lineLength = 100;

		// Line 1: ruler
		$thisLine = substr('      '.$pos, -6).' ';
		$thisLine .= substr('........10........20........30........40........50........60........70........80........90.......100', 0, $lineLength);
		echo str_replace(' ', '&nbsp;', $thisLine), '<br />';

		// Line 2: UTF8 character string
		$thisLine = '';
		for ($i=$pos; $i<($pos+$lineLength); $i++) {
			if (ord(substr($UTF8[$i], 0, 1)) < 0x20) {
				$thisChar = "&nbsp;";
			} else {
				$thisChar = $UTF8[$i];
				switch ($thisChar) {
				case '&':
					$thisChar = '&amp;';
					break;
				case '<':
					$thisChar = '&lt;';
					break;
				case ' ':
				case WT_UTF8_LRM:
				case WT_UTF8_RLM:
				case WT_UTF8_LRO:
				case WT_UTF8_RLO:
				case WT_UTF8_LRE:
				case WT_UTF8_RLE:
				case WT_UTF8_PDF:
					$thisChar = '&nbsp;';
					break;
				}
			}
			//$thisLine .= WT_UTF8_LRM;
			$thisLine .= $thisChar;
		}
		//echo '&nbsp;&nbsp;UTF8&nbsp;', $thisLine, '<br />';
		echo '&nbsp;&nbsp;UTF8&nbsp;', WT_UTF8_LRO, $thisLine, WT_UTF8_PDF, '<br />';

		// Line 3:  First hexadecimal byte
		$thisLine = 'Byte 1 ';
		$thisLine .= substr($hex1L, $pos, $lineLength);
		$thisLine .= '<br />';
		$thisLine .= '       ';
		$thisLine .= substr($hex1R, $pos, $lineLength);
		$thisLine .= '<br />';
		echo str_replace(array(' ', '<br&nbsp;/>'), array('&nbsp;', '<br />'), $thisLine);

		// Line 4:  Second hexadecimal byte
		if ($haveByte2) {
			$thisLine = 'Byte 2 ';
			$thisLine .= substr($hex2L, $pos, $lineLength);
			$thisLine .= '<br />';
			$thisLine .= '       ';
			$thisLine .= substr($hex2R, $pos, $lineLength);
			$thisLine .= '<br />';
			echo str_replace(array(' ', '<br&nbsp;/>'), array('&nbsp;', '<br />'), $thisLine);
		}

		// Line 5:  Third hexadecimal byte
		if ($haveByte3) {
			$thisLine = 'Byte 3 ';
			$thisLine .= substr($hex3L, $pos, $lineLength);
			$thisLine .= '<br />';
			$thisLine .= '       ';
			$thisLine .= substr($hex3R, $pos, $lineLength);
			$thisLine .= '<br />';
			echo str_replace(array(' ', '<br&nbsp;/>'), array('&nbsp;', '<br />'), $thisLine);
		}

		// Line 6:  Fourth hexadecimal byte
		if ($haveByte4) {
			$thisLine = 'Byte 4 ';
			$thisLine .= substr($hex4L, $pos, $lineLength);
			$thisLine .= '<br />';
			$thisLine .= '       ';
			$thisLine .= substr($hex4R, $pos, $lineLength);
			$thisLine .= '<br />';
			echo str_replace(array(' ', '<br&nbsp;/>'), array('&nbsp;', '<br />'), $thisLine);
		}
		echo '<br />';
		$pos += $lineLength;
		if ($pos >= $lastPos) break;
	}

	echo '</span></code>';
	return true;
}
