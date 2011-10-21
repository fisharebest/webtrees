<?php
// Function for printing
//
// Various printing functions used by all scripts and included by the functions.php file.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

/**
* print the information for an individual chart box
*
* find and print a given individuals information for a pedigree chart
* @param string $pid the Gedcom Xref ID of the   to print
* @param int $style the style to print the box in, 1 for smaller boxes, 2 for larger boxes
* @param int $count on some charts it is important to keep a count of how many boxes were printed
*/
function print_pedigree_person($person, $style=1, $count=0, $personcount="1") {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $ZOOM_BOXES, $LINK_ICONS, $GEDCOM;
	global $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT;
	global $WT_IMAGES, $ABBREVIATE_CHART_LABELS;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	if ($style != 2) $style=1;
	if (empty($show_full)) $show_full = 0;
	if (empty($PEDIGREE_FULL_DETAILS)) $PEDIGREE_FULL_DETAILS = 0;

	if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;
	// NOTE: Start div out-rand()
	if (!$person) {
		echo "<div id=\"out-", rand(), "\" class=\"person_boxNN\" style=\"width: ", $bwidth, "px; height: ", $bheight, "px; overflow: hidden;\">";
		echo "<br />";
		echo "</div>";
		return false;
	}
	$pid=$person->getXref();
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
				$personlinks .= "<a href=\"pedigree.php?rootid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;talloffset={$talloffset}&amp;ged=".rawurlencode($GEDCOM)."\" $mouseAction1><b>".WT_I18N::translate('Pedigree')."</b></a>";

				if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
					$personlinks .= '<br /><a href="module.php?mod=googlemap&amp;mod_action=pedigree_map&amp;rootid='.$pid.'&amp;ged='.WT_GEDURL.'" '.$mouseAction1.'><b>'.WT_I18N::translate('Pedigree map').'</b></a>';
				}
				if (WT_USER_GEDCOM_ID && WT_USER_GEDCOM_ID!=$pid) {
					$personlinks .= '<br /><a href="relationship.php?show_full='.$PEDIGREE_FULL_DETAILS.'&amp;pid1='.WT_USER_GEDCOM_ID.'&amp;pid2='.$pid.'&amp;show_full='.$PEDIGREE_FULL_DETAILS.'&amp;pretty=2&amp;followspouse=1&amp;ged='.WT_GEDURL.'" '.$mouseAction1.'><b>'.WT_I18N::translate('Relationship to me').'</b></a>';
				}
				$personlinks .= "<br /><a href=\"descendancy.php?pid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;generations={$generations}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM)."\" $mouseAction1><b>".WT_I18N::translate('Descendants')."</b></a><br />";
				$personlinks .= "<a href=\"ancestry.php?rootid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;chart_style={$chart_style}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".WT_I18N::translate('Ancestors')."</b></a><br />";
				$personlinks .= "<a href=\"compact.php?rootid={$pid}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".WT_I18N::translate('Compact tree')."</b></a><br />";
				if (function_exists("imagettftext")) {
					$personlinks .= "<a href=\"fanchart.php?rootid={$pid}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;ged=".rawurlencode($GEDCOM)."\" ".$mouseAction1."><b>".WT_I18N::translate('Fan chart')."</b></a><br />";
				}
				$personlinks .= "<a href=\"hourglass.php?pid={$pid}&amp;show_full={$PEDIGREE_FULL_DETAILS}&amp;chart_style={$chart_style}&amp;PEDIGREE_GENERATIONS={$OLD_PGENS}&amp;box_width={$box_width}&amp;ged=".rawurlencode($GEDCOM)."&amp;show_spouse={$show_spouse}\" ".$mouseAction1."><b>".WT_I18N::translate('Hourglass chart')."</b></a><br />";
				if (array_key_exists('tree', WT_Module::getActiveModules())) {
					$personlinks .= '<a href="module.php?mod=tree&amp;mod_action=treeview&amp;ged='.WT_GEDURL.'&amp;rootid='.$pid.'" '.$mouseAction1.'><b>'.WT_I18N::translate('Interactive tree').'</b></a><br />';
				}

				foreach ($person->getSpouseFamilies() as $family) {
					$spouse = $family->getSpouse($person);

					$children = $family->getChildren();
					$num = count($children);
					if ((!empty($spouse))||($num>0)) {
						$personlinks .= "<a href=\"".$family->getHtmlUrl()."&amp;show_full=1\" ".$mouseAction1."><b>".WT_I18N::translate('Family with spouse')."</b></a><br />";
						if (!empty($spouse)) {
							$personlinks .= "<a href=\"".$spouse->getHtmlUrl()."\" $mouseAction1>";
							$personlinks .= $spouse->getFullName();
							$personlinks .= "</a><br />";
						}
					}
					foreach ($children as $child) {
						$personlinks .= "&nbsp;&nbsp;<a href=\"".$child->getHtmlUrl()."\" $mouseAction1>";
						$personlinks .= $child->getFullName();
						$personlinks .= "<br /></a>";
					}
				}
				$personlinks .= "</td></tr></table>";
			}
			// NOTE: Start div out-$pid.$personcount.$count
			if ($style==1) $outBoxAdd .= " class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; overflow: hidden; z-index:-1;\"";
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
				$icons .= "><img id=\"iconz-$boxID\" src=\"".$WT_IMAGES["zoomin"]."\" border=\"0\" alt=\"".WT_I18N::translate('Zoom in/out on this box.')."\" title=\"".WT_I18N::translate('Zoom in/out on this box.')."\" /></a>";
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
				$icons .= "><img src=\"".$WT_IMAGES["pedigree"]."\" border=\"0\" vspace=\"0\" hspace=\"0\" alt=\"".WT_I18N::translate('Links to charts, families, and close relatives of this person. Click this icon to view this page, starting at this person.')."\" title=\"".WT_I18N::translate('Links to charts, families, and close relatives of this person. Click this icon to view this page, starting at this person.')."\" /></a>";
			}
		}
		else {
			if ($style==1) {
				$outBoxAdd .= "class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; overflow: hidden;\"";
			} else {
				$outBoxAdd .= "class=\"person_box$isF\" style=\"overflow: hidden;\"";
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
		if ($style==1) $outBoxAdd .= "class=\"person_box$isF\" style=\"width: ".$bwidth."px; height: ".$bheight."px; overflow: hidden;\"";
		else $outBoxAdd .= "class=\"person_box$isF\" style=\" overflow: hidden;\"";
	}
	//-- find the name
	$name = $person->getFullName();
	if ($SHOW_HIGHLIGHT_IMAGES) {
		$object=$person->findHighlightedMedia();
		$img_id='box-'.$boxID.'.-thumb';
		if (!empty($object)) {
			$mediaobject=WT_Media::getInstance($object['mid']);
			$thumbnail=$mediaobject->displayMedia(array('display_type'=>'pedigree_person','img_id'=>$img_id,'img_title'=>$name,'show_full'=>$show_full));
		} else {
			$thumbnail=display_silhouette(array('sex'=>$person->getSex(),'display_type'=>'pedigree_person','img_id'=>$img_id,'img_title'=>$name,'show_full'=>$show_full)); // may return ''
		}
	}
	//-- find additional name
	$addname=$person->getAddName();

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
				if (!is_null($event) && ($event->getDate()->isOK() || $event->getPlace()) && $event->canShow()) {
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
			if (!is_null($event) && ($event->getDate()->isOK() || $event->getPlace()) && $event->canShow()) {
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
* other auxiliary files needed to run webtrees.  It will also include the theme specific header file.
* This function should be called by every page, except popups, before anything is output.
*
* Popup pages, because of their different format, should invoke function print_simple_header() instead.
*
* @param string $title the title to put in the <TITLE></TITLE> header tags
*/
function print_header($title, $view='full') {
	global $bwidth, $BROWSERTYPE, $SEARCH_SPIDER, $cart;
	global $GEDCOM, $GEDCOM_TITLE, $action, $query;
	global $stylesheet, $print_stylesheet, $headerfile, $print_headerfile;
	global $WT_IMAGES, $TEXT_DIRECTION, $REQUIRE_AUTHENTICATION;
	global $controller;

	$GEDCOM_TITLE = get_gedcom_setting(WT_GED_ID, 'title');
	$META_DESCRIPTION='';
	$META_ROBOTS='noindex,nofollow';
	$META_GENERATOR='';
	$LINK_CANONICAL='';

	$index_this_page=false;
	$access_denied=false;

	if (isset($controller) && (in_array(WT_SCRIPT_NAME, array('family.php', 'individual.php', 'note.php', 'repo.php', 'source.php' )))) {
		switch (get_class($controller)) {
		case 'WT_Controller_Family':
			// family.php
			if ($controller->family) {
				$LINK_CANONICAL=$controller->family->getHtmlUrl();
				if ($controller->family->canDisplayDetails()) $index_this_page=true; else $access_denied=true; 
			}
			break;
		case 'WT_Controller_Individual':
			// individual.php
			if ($controller->indi) {
				$LINK_CANONICAL=$controller->indi->getHtmlUrl();
				if ($controller->indi->canDisplayDetails()) $index_this_page=true; else $access_denied=true;
			}
			break;
		case 'WT_Controller_Note':
			// note.php
			if ($controller->note) {
				$LINK_CANONICAL=$controller->note->getHtmlUrl();
				if ($controller->note->canDisplayDetails()) $index_this_page=true; else $access_denied=true;
			}
			break;
		case 'WT_Controller_Repository':
			// repo.php
			if ($controller->repository) {
				$LINK_CANONICAL=$controller->repository->getHtmlUrl();
				if ($controller->repository->canDisplayDetails()) $index_this_page=true; else $access_denied=true;
			}
			break;
		case 'WT_Controller_Source':
			// source.php
			if ($controller->source) {
				$LINK_CANONICAL=$controller->source->getHtmlUrl();
				if ($controller->source->canDisplayDetails()) $index_this_page=true; else $access_denied=true;
			}
			break;
		default:
			break;
		}
	}
	
	if (WT_SCRIPT_NAME=='index.php') {
		$LINK_CANONICAL='index.php?ctype=gedcom&amp;ged='.WT_GEDURL;
		$index_this_page=true;
	}

	// listing pages 
	if (in_array(WT_SCRIPT_NAME, array('famlist.php', 'indilist.php', 'notelist.php', 'repolist.php', 'sourcelist.php'))) {
		$index_this_page=true;
	}

	if ($index_this_page) {
		$META_ROBOTS='index,follow';
	}

	if ($view=='full') {
		$META_DESCRIPTION=get_gedcom_setting(WT_GED_ID, 'META_DESCRIPTION');
		if (empty($META_DESCRIPTION)) {
			$META_DESCRIPTION=$GEDCOM_TITLE;
		}
		$META_GENERATOR=WT_WEBTREES.' - '.WT_WEBTREES_URL;
	}

	header('Content-Type: text/html; charset=UTF-8');
	if ($access_denied) {
		header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden');
	}

	// The title often includes the names of records, which may have markup
	// that cannot be used in the page title.
	$title=htmlspecialchars_decode(strip_tags($title));

	$META_TITLE=get_gedcom_setting(WT_GED_ID, 'META_TITLE');
	if ($META_TITLE) {
		$title.=' - '.$META_TITLE;
	}
	$javascript=
		'<script type="text/javascript" src="'.WT_JQUERY_URL.'"></script>'.
		'<script type="text/javascript" src="'.WT_JQUERYUI_URL.'"></script>'.
		'<script type="text/javascript" src="'.WT_STATIC_URL.'js/jquery/jquery.jeditable.min.js"></script>'.
		'<script type="text/javascript" src="'.WT_STATIC_URL.'js/jquery/jquery.dataTables.min.js"></script>'.
		'<script type="text/javascript" src="'.WT_STATIC_URL.'js/jquery/jquery.cookie.js"></script>'.
		WT_JS_START.'
		// Give JavaScript access to some PHP constants
		var WT_STATIC_URL  = "'.WT_STATIC_URL.'";
		var WT_THEME_DIR   = "'.WT_THEME_DIR.'";
		var WT_MODULES_DIR = "'.WT_MODULES_DIR.'";
		var textDirection = "'.$TEXT_DIRECTION.'";
		var browserType = "'.$BROWSERTYPE.'";
		var WT_SCRIPT_NAME = "'.WT_SCRIPT_NAME.'";
		var WT_LOCALE = "'.WT_LOCALE.'";
		/* keep the session id when opening new windows */
		var sessionid = "'.Zend_Session::getId().'";
		var sessionname = "'.WT_SESSION_NAME.'";
		var accesstime = '.WT_DB::prepare("SELECT UNIX_TIMESTAMP(NOW())")->fetchOne().';
		var plusminus = new Array();
		plusminus[0] = new Image();
		plusminus[0].src = "'.$WT_IMAGES["plus"].'";
		plusminus[0].title = "'.WT_I18N::translate('Show Details').'";
		plusminus[1] = new Image();
		plusminus[1].src = "'.$WT_IMAGES["minus"].'";
		plusminus[1].title = "'.WT_I18N::translate('Hide Details').'";
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
	
	function delete_record(pid, linenum, mediaid) {
		if (!mediaid) mediaid="";
		if (confirm(\''.WT_I18N::translate('Are you sure you want to delete this fact?').'\')) {
			window.open(\'edit_interface.php?action=delete&pid=\'+pid+\'&linenum=\'+linenum+\'&mediaid=\'+mediaid+"&"+sessionname+"="+sessionid, \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\');
		}
		return false;
	}

	function message(username, method, url, subject) {
		if ((!url)||(url=="")) url=\''.addslashes(urlencode(get_query_url())).'\';
		if ((!subject)||(subject=="")) subject="";
		window.open(\'message.php?to=\'+username+\'&method=\'+method+\'&url=\'+url+\'&subject=\'+subject+"&"+sessionname+"="+sessionid, \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\');
		return false;
	}

	var whichhelp = \'help_'.WT_SCRIPT_NAME.'&action='.$action.'\';
	'.
	WT_JS_END.
	'<script src="'.WT_STATIC_URL.'js/webtrees.js" type="text/javascript"></script>';
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
	print_header($title, 'simple');
}

// -- print the html to close the page
function print_footer($view='full') {
	global $footerfile, $printlink, $WT_IMAGES, $TEXT_DIRECTION, $footer_count;

	// If the main script hasn't closed its session, do it now.
	// If we rely on PHP to close the session, it may not do it
	// until after it has closed the DB connection - which it needs!
	Zend_Session::writeClose();

	if ($view=='full') {
		if (!isset($footer_count)) $footer_count = 1;
		else $footer_count++;
		echo "<!-- begin footer -->";
		require WT_ROOT.$footerfile;
		if (function_exists("load_behaviour")) {
			load_behaviour();  // @see function_print_lists.php
		}
	} else {
		if (WT_DEBUG || get_gedcom_setting(WT_GED_ID, 'SHOW_STATS')) {
			echo execution_stats();
		}
	}

	if (WT_DEBUG_SQL) {
		echo WT_DB::getQueryLog();
	}
	echo WT_JS::render();
	echo '</body></html>';
}

// print HTML header meta links 
// previously identical code in each theme's header.php file
// now added as a function here.

function header_links($META_DESCRIPTION, $META_ROBOTS, $META_GENERATOR, $LINK_CANONICAL) {
	$header_links=
		// Strict validation requires a default type for inline <script> and <style>
		'<meta http-equiv="Content-Style-Type" content="text/css" />'.
		'<meta http-equiv="Content-Script-Type" content="text/javascript" />';
	if (!empty($LINK_CANONICAL)) {
		$header_links.= '<link rel="canonical" href="'. $LINK_CANONICAL. '" />';
	}
	if (!empty($META_DESCRIPTION)) {
		$header_links.= '<meta name="description" content="'. htmlspecialchars($META_DESCRIPTION). '" />';
	}
	$header_links.= '<meta name="robots" content="'. $META_ROBOTS. '" />';
	if (!empty($META_GENERATOR)) {
		$header_links.= '<meta name="generator" content="'. $META_GENERATOR. '" />';
	}
	return $header_links;
}

// Page footer for popup/edit windows
function print_simple_footer() {
	print_footer('simple');
}

/**
* Prints Exection Statistics
*
* prints out the execution time and the databse queries
*/
function execution_stats() {
	global $start_time, $PRIVACY_CHECKS;

	return
		'<div class="execution_stats">'.
		WT_I18N::translate(
			'Execution time: %1$s seconds. Database queries: %2$s. Privacy checks: %3$s. Memory usage: %4$s KB.',
			WT_I18N::number(microtime(true)-$start_time, 3),
			WT_I18N::number(WT_DB::getQueryCount()),
			WT_I18N::number($PRIVACY_CHECKS),
			WT_I18N::number(version_compare(PHP_VERSION, '5.2.1', '>=') ? (memory_get_peak_usage(true)/1024) : (memory_get_usage()/1024))
		).
		'</div>';
}

// Generate a login link
function login_link($extra='') {
	global $SEARCH_SPIDER;

	if ($SEARCH_SPIDER) {
		return '';
	} else {
		if (WT_SCRIPT_NAME=='login.php') {
			$href='#';
		} else {
			$href=get_site_setting('LOGIN_URL', 'login.php').'?url='.rawurlencode(get_query_url());
		}
		return '<a href="' . $href . '" ' . $extra . ' class="link">' . WT_I18N::translate('Login') . '</a>';
	}
}

// Generate a logout link
function logout_link($extra='') {
	global $SEARCH_SPIDER;

	if ($SEARCH_SPIDER) {
		return '';
	} else {
		return '<a href="index.php?logout=1" ' . $extra . ' class="link">' . WT_I18N::translate('Logout') . '</a>';
	}
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
		return '<div class="contact_links">'.WT_I18N::translate('For technical support or genealogy questions, please contact').' '.$supportLink.'</div>';
	} else {
		$returnText = '<div class="contact_links">';
		if ($supportLink) {
			$returnText .= WT_I18N::translate('For technical support and information contact').' '.$supportLink;
			if ($contactLink) {
				$returnText .= '<br />';
			}
		}
		if ($contactLink) {
			$returnText .= WT_I18N::translate('For help with genealogy questions contact').' '.$contactLink;
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
		$support_menu['label']=WT_I18N::translate('Technical help contact');
		$menuitems['menu-help-technical']=$support_menu;
	} else {
		$support_menu['label']=WT_I18N::translate('Technical help contact');
		$menuitems['menu-help-technical']=$support_menu;
		$contact_menu['label']=WT_I18N::translate('Genealogy contact');
		$menuitems['menu-help-genealogy']=$contact_menu;
	}
	return $menuitems;
}

//-- print user favorites
function print_favorite_selector($option=0) {
	$menu=WT_MenuBar::getFavoritesMenu();

	if ($menu) {
		echo '<div class="favorites_form">';
		switch($option) {
		case 1:
			echo WT_MenuBar::getFavoritesMenu()->getMenu();
			break;
		default:
			echo '<form class="favorites_form">';
			echo WT_MenuBar::getFavoritesMenu()->getMenuAsDropdown();
			echo '</form>';
			break;
		}
		echo '</div>';
	}
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

	$elementID = 'N-'.floor(microtime()*1000000);
	$text = trim($text);

	// Check if Shared Note and if so enable url link on title -------------------
	if (preg_match('/^0 @'.WT_REGEX_XREF.'@ NOTE/', $nrec)) {
		$centitl  = str_replace('~~', '', $text);
		$centitl  = str_replace('<br />', '', $centitl);
		if (preg_match('/@N([0-9])+@/', $nrec, $match_nid)) {
			$nid = str_replace('@', '', $match_nid[0]);
			if (!$npage) {
				$centitl = '<a href="note.php?nid='.$nid.'">'.$centitl.'</a>';
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
	$text = str_replace('~~', '<br />', $text);
	$text = trim(expand_urls(stripLRMRLM($text)));
	$data = '';

	if (!empty($text) || !empty($centitl)) {
		$text = PrintReady($text);
		// Check if Formatted Shared Note (using pipe "|" as delimiter ) --------------------
		if (preg_match('/^0 @'.WT_REGEX_XREF.'@ NOTE/', $nrec) && strstr($text, "|") && array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
			require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_note_decode.php';
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

		$brpos = strpos($text, '<br />');
		if (!$npage) {
			$data .= '<div class="fact_NOTE"><span class="label">';
			if ($brpos !== false) {
				if ($EXPAND_NOTES) $plusminus='minus'; else $plusminus='plus';
				$data .= '<a href="javascript:;" onclick="expand_layer(\''.$elementID.'\'); return false;"><img id="'.$elementID.'_img" src="'.$WT_IMAGES[$plusminus].'" border="0" width="11" height="11" alt="';
				if ($plusminus=='plus') $data .= WT_I18N::translate('Show Details').'" title="'.WT_I18N::translate('Show Details').'" /></a> ';
				else $data .= WT_I18N::translate('Hide Details').'" title="'.WT_I18N::translate('Hide Details').'" /></a> ';
			}

			// Check if Shared Note -----------------------------
			if (preg_match('/^0 @'.WT_REGEX_XREF.'@ NOTE/', $nrec)) {
				$data .= WT_I18N::translate('Shared note').': </span> ';
			} else {
				$data .= WT_I18N::translate('Note').': </span>';
			}
		}

		if ($brpos !== false) {
			$data .= '<span class="field">'.substr($text, 0, $brpos).'</span>';
			if ($npage) {
				$data .= substr($text, $brpos + 6) . "</div>";
			} else {
				$data .= '<div id="'.$elementID.'"';
				if ($EXPAND_NOTES) $data .= ' style="display:block"';
				$data .= ' class="note_details font11">';
				$data .= substr($text, $brpos + 6);
				$data .= '</div>';
			}
		} else {
			$data .= '<span class="field">'.$text. '</span>';
		}
			$data .= "</div>";

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
			$data .= $closeSpan;
		} else {
			$note=WT_Note::getInstance($nmatch[1]);
			if ($note->canDisplayDetails()) {
				$noterec = $note->getGedcomRecord();
				//-- print linked note records
				$nt = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
				$closeSpan = print_note_record(($nt>0)?$n1match[1]:"", 1, $noterec, $textOnly, true);
				$data .= $closeSpan;
				if (!$textOnly) {
					if (strpos($noterec, "1 SOUR")!==false) {
						require_once WT_ROOT.'includes/functions/functions_print_facts.php';
						$data .= print_fact_sources($noterec, 1, true);
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

	echo '<div class="error">', WT_I18N::translate('This information is private and cannot be shown.'), '</div>';
	switch ($method) {
	case 'none':
		break;
	case 'mailto':
		$email=getUserEmail($user_id);
		echo '<div class="error">', WT_I18N::translate('For more information contact'), ' ', '<a href="mailto:'.htmlspecialchars($email).'">'.htmlspecialchars($fullname).'</a>', '</div>';
		break;
	default:
		echo '<div class="error">', WT_I18N::translate('For more information contact'), ' ', "<a href='javascript:;' onclick='message(\"".get_user_name($user_id)."\", \"{$method}\");return false;'>{$fullname}</a>", '</div>';
		break;
	}
}

// Print a link for a popup help window
function help_link($help_topic, $module='') {
	global $WT_USE_HELPIMG, $WT_IMAGES;

	if ($_SESSION['show_context_help']) {
		return
			'<a class="help icon-help-15" href="javascript: '.$help_topic.'" onclick="helpPopup(\''.$help_topic.'\',\''.$module.'\'); return false;">&nbsp;'.
			($WT_USE_HELPIMG ?  '<img src="'.$WT_IMAGES['help'].'" class="icon" width="15" height="15" alt="" />' : WT_I18N::translate('?')).
			'&nbsp;</a>';
	} else {
		return '';
	}
}

// Print an external help link to the wiki site, in a new window
function wiki_help_link($topic) {
	global $WT_USE_HELPIMG, $WT_IMAGES;

	if ($_SESSION['show_context_help']) {
		return
			'<a class="help icon-help-15" href="'.WT_WEBTREES_WIKI.$topic.'" target="_new">&nbsp;'.
			($WT_USE_HELPIMG ?  '<img src="'.$WT_IMAGES['help'].'" class="icon" width="15" height="15" alt="" />' : WT_I18N::translate('?')).
			'&nbsp;</a>';
	} else {
		return '';
	}
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

// When a user has searched for text, highlight any matches in
// the displayed string.
function highlight_search_hits($string) {
	global $controller;
	if ($controller instanceof WT_Controller_Search) {
		// TODO: when a search contains multiple words, we search independently.
		// e.g. searching for "FOO BAR" will find records containing both FOO and BAR.
		// However, we only highlight the original search string, not the search terms.
		// The controller needs to provide its "query_terms" array.
		$regex=array();
		foreach (array($controller->query) as $search_term) {
			$regex[]=preg_quote($search_term, '/');
		}
		// Match these strings, provided they do not occur inside HTML tags
		$regex='('.implode('|', $regex).')(?![^<]*>)';
		return preg_replace('/'.$regex.'/i', '<span class="search_hit">$1</span>', $string);
	} else {
		return $string;
	}
}

/**
* Prepare text with parenthesis for printing
* Convert & to &amp; for xhtml compliance
*
* @param string $text to be printed
*/
function PrintReady($text, $InHeaders=false, $trim=true) {
	global $action, $firstname, $lastname, $place, $year;
	global $TEXT_DIRECTION_array, $TEXT_DIRECTION;

	//-- convert all & to &amp;
	$text = str_replace("&", "&amp;", $text);
	//$text = preg_replace(array("/&/", "/</", "/>/"), array("&amp;", "&lt;", "&gt;"), $text);
	//-- make sure we didn't double convert existing HTML entities like so:  &foo; to &amp;foo;
	$text = preg_replace("/&amp;(\w+);/", "&$1;", $text);
	if ($trim) $text = trim($text);

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

	return $text;
}

// Print the associations from the associated individuals in $event to the individuals in $record
function print_asso_rela_record(WT_Event $event, WT_GedcomRecord $record) {
	global $SEARCH_SPIDER;

	// To whom is this record an assocate?
	if ($record instanceof WT_Person) {
		// On an individual page, we just show links to the person
		$associates=array($record);
	} elseif ($record instanceof WT_Family) {
		// On a family page, we show links to both spouses
		$associates=$record->getSpouses();
	} else {
		// On other pages, it does not make sense to show associates
		return;
	}

	preg_match_all('/^1 ASSO @('.WT_REGEX_XREF.')@((\n[2-9].*)*)/', $event->getGedcomRecord(), $amatches1, PREG_SET_ORDER);
	preg_match_all('/\n2 ASSO @('.WT_REGEX_XREF.')@((\n[3-9].*)*)/', $event->getGedcomRecord(), $amatches2, PREG_SET_ORDER);
	// For each ASSO record
	foreach (array_merge($amatches1, $amatches2) as $amatch) {
		$person=WT_Person::getInstance($amatch[1]);
		if (!$person) {
			// If the target of the ASSO does not exist, create a dummy person, so
			// the user can see that something is present.
			$person=new WT_Person('');
		}
		if (preg_match('/\n[23] RELA (.+)/', $amatch[2], $rmatch)) {
			$rela=$rmatch[1];
		} else {
			$rela='';
		}
		$html=array();
		foreach ($associates as $associate) {
			if ($associate) {
				if ($rela) {
					$label=WT_Gedcom_Code_Rela::getValue($rela, $person);
				} else {
					// Generate an automatic RELA
					$label=get_relationship_name(get_relationship($associate->getXref(), $person->getXref(), true, 4, true));
				}
				if (!$label) {
					$label=WT_I18N::translate('Relationships');
				}
				// For family records (e.g. MARR), identify the spouse with a sex icon
				if ($record instanceof WT_Family) {
					$label=$associate->getSexImage().$label;
				}
				if ($SEARCH_SPIDER) {
					$html[]=$label; // Search engines cannot use the relationship chart.
				} else {
					$html[]='<a href="relationship.php?pid1='.$associate->getXref().'&amp;pid2='.$person->getXref().'&amp;ged='.WT_GEDURL.'">'.$label.'</a>';
				}
			}
		}
		$html=array_unique($html);
		echo
			'<div class="fact_ASSO">',
			'<a href="', $person->getHtmlUrl().'">', $person->getFullName(), '</a>',
			' - ',
			implode(', ', $html);
			echo '</div>';
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
				if ($parent && $age=WT_Date::GetAgeYears($parent->getBirthDate(), $birth_date)) {
					$deatdate=$parent->getDeathDate();
					$class='';
					switch ($parent->getSex()) {
					case 'F':
						// Highlight mothers who die in childbirth or shortly afterwards
						if ($deatdate->isOK() && $deatdate->MinJD()<$birth_date->MinJD()+90) {
							$class='parentdeath';
							$title=WT_Gedcom_Tag::getLabel('_DEAT_PARE', $parent);
						} else {
							$title=WT_I18N::translate('Mother\'s age');
						}
						break;
					case 'M':
						// Highlight fathers who die before the birth
						if ($deatdate->isOK() && $deatdate->MinJD()<$birth_date->MinJD()) {
							$class='parentdeath';
							$title=WT_Gedcom_Tag::getLabel('_DEAT_PARE', $parent);
						} else {
							$title=WT_I18N::translate('Father\'s age');
						}
						break;
					default:
						$title=WT_I18N::translate('Parent\'s age');
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

// print fact DATE TIME
//
// $event - event containing the date/age
// $record - the person (or couple) whose ages should be printed
// $anchor option to print a link to calendar
// $time option to print TIME value
function format_fact_date(WT_Event $event, WT_GedcomRecord $record, $anchor=false, $time=false) {
	global $pid, $SEARCH_SPIDER;
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	$factrec = $event->getGedcomRecord();
	$html='';
	// Recorded age
	$fact_age=get_gedcom_value('AGE', 2, $factrec);
	if ($fact_age=='')
		$fact_age=get_gedcom_value('DATE:AGE', 2, $factrec);
	$husb_age=get_gedcom_value('HUSB:AGE', 2, $factrec);
	$wife_age=get_gedcom_value('WIFE:AGE', 2, $factrec);

	// Calculated age
	if (preg_match('/2 DATE (.+)/', $factrec, $match)) {
		$date=new WT_Date($match[1]);
		$html.=' '.$date->Display($anchor && !$SEARCH_SPIDER);
		// time
		if ($time) {
			$timerec=get_sub_record(2, '2 TIME', $factrec);
			if ($timerec=='') {
				$timerec=get_sub_record(2, '2 DATE', $factrec);
			}
			if (preg_match('/[2-3] TIME (.*)/', $timerec, $tmatch)) {
				$html.='<span class="date"> - '.$tmatch[1].'</span>';
			}
		}
		$fact = $event->getTag();
		if ($record instanceof WT_Person) {
			// age of parents at child birth
			if ($fact=='BIRT') {
				$html .= format_parents_age($record->getXref(), $date);
			}
			// age at event
			else if ($fact!='CHAN' && $fact!='_TODO') {
				$birth_date=$record->getBirthDate();
				// Can't use getDeathDate(), as this also gives BURI/CREM events, which
				// wouldn't give the correct "days after death" result for people with
				// no DEAT.
				$death_event=$record->getFactByType('DEAT');
				if ($death_event) {
					$death_date=$death_event->getDate();
				} else {
					$death_date=new WT_Date('');
				}
				$ageText = '';
				if ((WT_Date::Compare($date, $death_date)<=0 || !$record->isDead()) || $fact=='DEAT') {
					// Before death, print age
					$age=WT_Date::GetAgeGedcom($birth_date, $date);
					// Only show calculated age if it differs from recorded age
					if ($age!='') {
						if (
							$fact_age!='' && $fact_age!=$age ||
							$fact_age=='' && $husb_age=='' && $wife_age=='' ||
							$husb_age!='' && $record->getSex()=='M' && $husb_age!=$age ||
							$wife_age!='' && $record->getSex()=='F' && $wife_age!=$age
						) {
							if ($age!="0d") {
								$ageText = '('.WT_I18N::translate('Age').' '.get_age_at_event($age, false).')';
							}
						}
					}
				}
				if ($fact!='DEAT' && WT_Date::Compare($date, $death_date)>=0) {
					// After death, print time since death
					$age=get_age_at_event(WT_Date::GetAgeGedcom($death_date, $date), true);
					if ($age!='') {
						if (WT_Date::GetAgeGedcom($death_date, $date)=="0d") {
							$ageText = '('.WT_I18N::translate('on the date of death').')';
						} else {
							$ageText = '('.$age.' '.WT_I18N::translate('after death').')';
						}
					}
				}
				if ($ageText!='') $html .= '<span class="age"> '.PrintReady($ageText).'</span>';
			}
		} elseif ($record instanceof WT_Family) {
			$indirec=find_person_record($pid, $ged_id);
			$indi=new WT_Person($indirec);
			$birth_date=$indi->getBirthDate();
			$death_date=$indi->getDeathDate();
			$ageText = '';
			if (WT_Date::Compare($date, $death_date)<=0) {
				$age=WT_Date::GetAgeGedcom($birth_date, $date);
				// Only show calculated age if it differs from recorded age
				if ($age!='' && $age>0) {
					if (
						$fact_age!='' && $fact_age!=$age ||
						$fact_age=='' && $husb_age=='' && $wife_age=='' ||
						$husb_age!='' && $indi->getSex()=='M' && $husb_age!= $age ||
						$wife_age!='' && $indi->getSex()=='F' && $wife_age!=$age
					) {
						$ageText = '('.WT_I18N::translate('Age').' '.get_age_at_event($age, false).')';
					}
				}
			}
			if ($ageText!='') $html .= '<span class="age"> '.PrintReady($ageText).'</span>';
		}
	} else {
		// 1 DEAT Y with no DATE => print YES
		// 1 BIRT 2 SOUR @S1@ => print YES
		// 1 DEAT N is not allowed
		// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
		$factrec = str_replace("\nWT_OLD\n", '', $factrec);
		$factrec = str_replace("\nWT_NEW\n", '', $factrec);
		$factdetail = explode(' ', trim($factrec));
		if (isset($factdetail) && (count($factdetail) == 3 && strtoupper($factdetail[2]) == 'Y') || (count($factdetail) == 4 && $factdetail[2] == 'SOUR')) {
			$html.=WT_I18N::translate('yes');
		}
	}
	// print gedcom ages
	foreach (array(WT_Gedcom_Tag::getLabel('AGE')=>$fact_age, WT_Gedcom_Tag::getLabel('HUSB')=>$husb_age, WT_Gedcom_Tag::getLabel('WIFE')=>$wife_age) as $label=>$age) {
		if ($age!='') {
			$html.=' <span class="label">'.$label.':</span> <span class="age">'.PrintReady(get_age_at_event($age, false)).'</span>';
		}
	}
	return $html;
}
/**
* print fact PLACe TEMPle STATus
*
* @param Event $event gedcom fact record
* @param boolean $anchor option to print a link to placelist
* @param boolean $sub option to print place subrecords
* @param boolean $lds option to print LDS TEMPle and STATus
*/
function format_fact_place(WT_Event $event, $anchor=false, $sub=false, $lds=false) {
	global $SHOW_PEDIGREE_PLACES, $SEARCH_SPIDER;

	$factrec = $event->getGedcomRecord();
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
				$html.='<br /><span class="label">'.WT_Gedcom_Tag::getLabel('LATI').': </span>'.$map_lati;
			}
			$map_long="";
			$cts = preg_match('/\d LONG (.*)/', $placerec, $match);
			if ($cts>0) {
				$map_long=$match[1];
				$html.=' <span class="label">'.WT_Gedcom_Tag::getLabel('LONG').': </span>'.$map_long;
			}
			if ($map_lati && $map_long && empty($SEARCH_SPIDER)) {
				$map_lati=trim(strtr($map_lati, "NSEW,�", " - -. ")); // S5,6789 ==> -5.6789
				$map_long=trim(strtr($map_long, "NSEW,�", " - -. ")); // E3.456� ==> 3.456
				$html.=' <a target="_BLANK" href="'."http://www.mapquest.com/maps/map.adp?searchtype=address&amp;formtype=latlong&amp;latlongtype=decimal&amp;latitude={$map_lati}&amp;longitude={$map_long}".'"><img src="images/mapq.gif" border="0" alt="Mapquest" title="Mapquest" /></a>';
				$html.=' <a target="_BLANK" href="'."http://maps.google.com/maps?q={$map_lati},{$map_long}(".rawurlencode($place).")".'"><img src="images/bubble.gif" border="0" alt="'.WT_I18N::translate('Google Maps™').'" title="'.WT_I18N::translate('Google Maps™').'" /></a>';
				$html.=' <a target="_BLANK" href="'."http://www.multimap.com/map/browse.cgi?lat={$map_lati}&amp;lon={$map_long}&amp;scale=&amp;icon=x".'"><img src="images/multim.gif" border="0" alt="Multimap" title="Multimap" /></a>';
				$html.=' <a target="_BLANK" href="'."http://www.terraserver.com/imagery/image_gx.asp?cpx={$map_long}&amp;cpy={$map_lati}&amp;res=30&amp;provider_id=340".'"><img src="images/terrasrv.gif" border="0" alt="TerraServer" title="TerraServer" /></a>';
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
			$html.='<br/>'.WT_I18N::translate('LDS Temple').': '.WT_Gedcom_Code_Temp::templeName($match[1]);
		}
		if (preg_match('/2 STAT (.*)/', $factrec, $match)) {
			$html.='<br />'.WT_I18N::translate('Status').': '.WT_Gedcom_Code_Stat::statusName($match[1]);
			if (preg_match('/3 DATE (.*)/', $factrec, $match)) {
				$date=new WT_Date($match[1]);
				$html.=', '.WT_Gedcom_Tag::getLabel('STAT:DATE').': '.$date->Display(false);
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
			$html.='<span dir="'.$TEXT_DIRECTION.'"><br /><em>';
			$html .= $event->getLabel();
			$html.=' '.format_fact_date($event, $person, false, false).format_fact_place($event).'</em></span>';
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
					echo WT_I18N::translate('Add from clipboard'), '</td>';
					echo '<td class="optionbox wrap"><form method="get" name="newFromClipboard" action="" onsubmit="return false;">';
					echo '<select id="newClipboardFact" name="newClipboardFact">';
				}
				$fact_type=WT_I18N::translate($fact['fact']);
				echo '<option value="clipboard_', $key, '">', $fact_type;
				// TODO use the event class to store/parse the clipboard events
				if (preg_match('/^2 DATE (.+)/m', $fact['factrec'], $match)) {
					$tmp=new WT_Date($match[1]);
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
			echo '&nbsp;&nbsp;<input type="button" value="', WT_I18N::translate('Add'), "\" onclick=\"addClipboardRecord('$id', 'newClipboardFact');\" /> ";
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
		$translated_addfacts[$addfact] = WT_Gedcom_Tag::getLabel($addfact);
	}
	uasort($translated_addfacts, 'factsort');
	echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, '">';
	echo WT_I18N::translate('Add new fact');
	echo help_link('add_facts'), '</td>';
	echo '<td class="optionbox wrap ', $TEXT_DIRECTION, '">';
	echo "<form method=\"get\" name=\"newfactform\" action=\"\" onsubmit=\"return false;\">";
	echo '<select id="newfact" name="newfact">';
	foreach ($translated_addfacts as $fact=>$fact_name) {
		echo '<option value="', $fact, '">', $fact_name, '</option>';
	}
	if ($type == 'INDI' || $type == 'FAM') {
		echo '<option value="EVEN">', WT_I18N::translate('Custom Event'), '</option>';
	}
	echo '</select>';
	echo "&nbsp;&nbsp;<input type=\"button\" value=\"", WT_I18N::translate('Add'), "\" onclick=\"add_record('$id', 'newfact');\" /> ";
	foreach ($quickfacts as $fact) echo "&nbsp;<small><a href='javascript://$fact' onclick=\"add_new_record('$id', '$fact');return false;\">", WT_Gedcom_Tag::getLabel($fact), "</a></small>&nbsp;";
	echo '</form>';
	echo '</td></tr>';
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
			'"', WT_I18N::translate_c('NOMINATIVE', 'January'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'February'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'March'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'April'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'May'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'June'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'July'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'August'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'September'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'October'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'November'), '",',
			'"', WT_I18N::translate_c('NOMINATIVE', 'December'), '");',
			'cal_setDayHeaders(',
			'"', WT_I18N::translate('Sun'), '",',
			'"', WT_I18N::translate('Mon'), '",',
			'"', WT_I18N::translate('Tue'), '",',
			'"', WT_I18N::translate('Wed'), '",',
			'"', WT_I18N::translate('Thu'), '",',
			'"', WT_I18N::translate('Fri'), '",',
			'"', WT_I18N::translate('Sat'), '");',
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

	$text = WT_I18N::translate('Find individual ID');
	if (empty($ged)) $ged=$GEDCOM;
	if (isset($WT_IMAGES["button_indi"])) $Link = "<img src=\"".$WT_IMAGES["button_indi"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findIndi(document.getElementById('".$element_id."'), document.getElementById('".$indiname."'), '".$multiple."', '".$ged."', '".$filter."'); findtype='individual'; return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findplace_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = WT_I18N::translate('Find Place');
	if (isset($WT_IMAGES["button_place"])) $Link = "<img src=\"".$WT_IMAGES["button_place"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findPlace(document.getElementById('".$element_id."'), '".$ged."'); return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findfamily_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = WT_I18N::translate('Find Family ID');
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

	$text = WT_I18N::translate('Find Special Characters');
	if (isset($WT_IMAGES["button_keyboard"])) $Link = "<img id=\"".$element_id."_spec\" name=\"".$element_id."_spec\" src=\"".$WT_IMAGES["button_keyboard"]."\"  alt=\"".$text."\"  title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findSpecialChar(document.getElementById('".$element_id."')); updatewholename(); return false;\">";
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
		echo " return false;\">", $choice, "</a>";
	}
	echo "</small>";
}

function print_findsource_link($element_id, $sourcename="", $asString=false, $ged='') {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = WT_I18N::translate('Find Source ID');
	if (isset($WT_IMAGES["button_source"])) $Link = "<img src=\"".$WT_IMAGES["button_source"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findSource(document.getElementById('".$element_id."'), document.getElementById('".$sourcename."'), '".$ged."'); findtype='source'; return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findnote_link($element_id, $notename="", $asString=false, $ged='') {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = WT_I18N::translate('Find Shared Note');
	if (isset($WT_IMAGES["button_note"])) $Link = "<img src=\"".$WT_IMAGES["button_note"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findnote(document.getElementById('".$element_id."'), document.getElementById('".$notename."'), '".$ged."'); findtype='note'; return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findrepository_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = WT_I18N::translate('Find Repository');
	if (isset($WT_IMAGES["button_repository"])) $Link = "<img src=\"".$WT_IMAGES["button_repository"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findRepository(document.getElementById('".$element_id."'), '".$ged."'); return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findmedia_link($element_id, $choose="", $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	if (empty($ged)) $ged=$GEDCOM;
	$text = WT_I18N::translate('Find media');
	if (isset($WT_IMAGES["button_media"])) $Link = "<img src=\"".$WT_IMAGES["button_media"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findMedia(document.getElementById('".$element_id."'), '".$choose."', '".$ged."'); return false;\">";
	$out .= $Link;
	$out .= "</a>";
	if ($asString) return $out;
	echo $out;
}

function print_findfact_link($element_id, $ged='', $asString=false) {
	global $WT_IMAGES, $GEDCOM;

	$text = WT_I18N::translate('Find fact tag');
	if (empty($ged)) $ged=$GEDCOM;
	if (isset($WT_IMAGES["button_find_facts"])) $Link = "<img src=\"".$WT_IMAGES["button_find_facts"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"top\" />";
	else $Link = $text;
	$out = " <a href=\"javascript:;\" onclick=\"findFact(document.getElementById('".$element_id."'), '".$ged."'); return false;\">";
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
