<?php
/**
 * Function for printing facts
 *
 * Various printing functions used to print fact records
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

define('WT_FUNCTIONS_PRINT_FACTS_PHP', '');

require_once WT_ROOT.'includes/classes/class_person.php';

/**
 * Turn URLs in text into HTML links.  Insert breaks into long URLs
 * so that the browser can word-wrap.
 *
 * @param string $text	Text that may or may not contain URLs
 * @return string	The text with URLs replaced by HTML links
 */
function expand_urls($text) {
	// Some versions of RFC3987 have an appendix B which gives the following regex
	// (([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
	// This matches far too much while a "precise" regex is several pages long.
	// This is a compromise.
	$URL_REGEX='((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#[^\s?#<>]+)?';
	
	return preg_replace_callback(
		'/'.addcslashes("(?!>)$URL_REGEX(?!</a>)", '/').'/i',
		create_function( // Insert soft hyphens into the replaced string
			'$m',
			'return "<a href=\"".$m[0]."\" target=\"blank\">".preg_replace("/\b/", "&shy;", $m[0])."</a>";'
		),
		preg_replace("/<(?!br)/i", "&lt;", $text) // no html except br
	);
}

/**
 * print a fact record
 *
 * prints a fact record designed for the personal facts and details page
 * @param Event $eventObj	The Event object to print
 * @param boolean $noedit	Hide or show edit links
 */
function print_fact(&$eventObj, $noedit=false) {
	global $nonfacts;
	global $WT_IMAGE_DIR, $WT_MENUS_AS_LISTS;
	global $GEDCOM;
	global $WORD_WRAPPED_NOTES;
	global $TEXT_DIRECTION;
	global $HIDE_GEDCOM_ERRORS, $SHOW_ID_NUMBERS, $SHOW_FACT_ICONS, $SHOW_MEDIA_FILENAME;
	global $CONTACT_EMAIL, $view;
	global $n_chil, $n_gchi, $n_ggch;
	global $SEARCH_SPIDER;

	$fact = $eventObj->getTag();
	$rawEvent = $eventObj->getDetail();
	$event = htmlspecialchars($rawEvent, ENT_COMPAT, 'UTF-8');
	$factrec = $eventObj->getGedcomRecord();
	$linenum = $eventObj->getLineNumber();
	$parent = $eventObj->getParentObject();
	$pid = "";
	if (!is_null($eventObj->getFamilyId())) {
		$pid = $eventObj->getFamilyId();
	} elseif (!is_null($parent)) {
		$pid = $parent->getXref();
	}

	// Who is this fact about?  Need it to translate fact label correctly
	if (preg_match('/2 ASSO @('.WT_REGEX_XREF.')@/', $factrec, $match)) {
		// Event of close relative
		$label_person=Person::getInstance($match[1]);
	} else if (preg_match('/2 _PGVS @('.WT_REGEX_XREF.')@/', $factrec, $match)) {
		// Event of close relative
		$label_person=Person::getInstance($match[1]);
	} else if ($parent instanceof Family) {
		// Family event
		$husb = $parent->getHusband();
		$wife = $parent->getWife();
		if (empty($wife) && !empty($husb)) $label_person=$husb;
		else if (empty($husb) && !empty($wife)) $label_person=$wife;
		else $label_person=$parent;
	} else {
		// The actual person
		$label_person=$parent;
	}

	if ($fact=="NOTE") return print_main_notes($factrec, 1, $pid, $linenum, $noedit);
	if ($fact=="SOUR") return print_main_sources($factrec, 1, $pid, $linenum, $noedit);
	$styleadd="";
	if (strpos($factrec, "WT_NEW")!==false) $styleadd="change_new";
	if (strpos($factrec, "WT_OLD")!==false) $styleadd="change_old";

	if (($linenum<1) && (!empty($SEARCH_SPIDER)))  return; // don't add relatives for spiders.
	if ($linenum<1) $styleadd="rela"; // not editable
	if ($linenum==-1) $styleadd="histo"; // historical facts
	// -- avoid known non facts
	if (in_array($fact, $nonfacts)) return;
	//-- do not print empty facts
	$lines = explode("\n", trim($factrec));
	if (count($lines)<2 && $event=="") {
		return;
	}
	// See if RESN tag prevents display or edit/delete
	$resn_tag = preg_match("/2 RESN (.+)/", $factrec, $match);
	if ($resn_tag == "1") $resn_value = $match[1];
	// Assume that all recognised tags are translated.
	// -- handle generic facts
	if ($fact!="EVEN" && $fact!="FACT" && $fact!="OBJE") {
		$factref = $fact;
		if (!$eventObj->canShow()) return false;
		if ($styleadd=="") $rowID = "row_".floor(microtime()*1000000);
		else $rowID = "row_".$styleadd;
		echo "\n\t\t<tr class=\"", $rowID, "\">";
		echo "\n\t\t\t<td class=\"descriptionbox $styleadd center width20\">";
		if ($SHOW_FACT_ICONS)
			echo $eventObj->Icon(), ' ';
		echo translate_fact($factref, $label_person);
		if ($fact=="_BIRT_CHIL" and isset($n_chil)) echo "<br />", i18n::translate('#%d', $n_chil++);
		if ($fact=="_BIRT_GCHI" and isset($n_gchi)) echo "<br />", i18n::translate('#%d', $n_gchi++);
		if ($fact=="_BIRT_GGCH" and isset($n_ggch)) echo "<br />", i18n::translate('#%d', $n_ggch++);
		if (!$noedit && WT_USER_CAN_EDIT && $styleadd!="change_old" && $linenum>0 && $view!="preview" && !FactEditRestricted($pid, $factrec)) {
			$menu = new Menu(i18n::translate('Edit'), "#", "right", "down");
			$menu->addOnclick("return edit_record('$pid', $linenum);");
			$menu->addClass("", "", "submenu");
			$submenu = new Menu(i18n::translate('Edit'), "#", "right");
			$submenu->addOnclick("return edit_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('Copy'), "#", "right");
			$submenu->addOnclick("return copy_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('Delete'), "#", "right");
			$submenu->addOnclick("return delete_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			if (!$WT_MENUS_AS_LISTS) {
				echo " <div style=\"width:25px;\">";
				$menu->printMenu();
				echo "</div>";
			} else { 
				echo " <ul>";
				$menu->printMenu();
				echo "</ul>";					
			}
		}
		echo "</td>";
	} else {
		if ($fact == "OBJE") return false;
		if (!showFact("EVEN", $pid)) return false;
		// -- find generic type for each fact
		$ct = preg_match("/2 TYPE (.*)/", $factrec, $match);
		if ($ct>0) $factref = trim($match[1]);
		else $factref = $fact;
		if (!showFact($factref, $pid)) return false;
		if ($styleadd=="") $rowID = "row_".floor(microtime()*1000000);
		else $rowID = "row_".$styleadd;
		echo "\n\t\t<tr class=\"", $rowID, "\">";
		echo "<td class=\"descriptionbox $styleadd center width20\">";
		if ($SHOW_FACT_ICONS)
			echo $eventObj->Icon(), ' ';
		if ($ct>0) {
			if ($factref=='image_size') echo i18n::translate('Image Dimensions');
			else if ($factref=='file_size') echo i18n::translate('File Size');
			else echo $factref;
		} else echo translate_fact($factref, $label_person);
		if (!$noedit && WT_USER_CAN_EDIT && $styleadd!="change_old" && $linenum>0 && $view!="preview" && !FactEditRestricted($pid, $factrec)) {
			$menu = new Menu(i18n::translate('Edit'), "#", "right", "down");
			$menu->addOnclick("return edit_record('$pid', $linenum);");
			$menu->addClass("", "", "submenu");

			$submenu = new Menu(i18n::translate('Edit'), "#", "right");
			$submenu->addOnclick("return edit_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('Delete'), "#", "right");
			$submenu->addOnclick("return delete_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('Copy'), "#", "right");
			$submenu->addOnclick("return copy_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			echo " <div style=\"width:25px;\">";
			$menu->printMenu();
			echo "</div>";
		}
		echo "</td>";
	}
	$align = "";
	echo "<td class=\"optionbox $styleadd wrap\" $align>";
	//echo "<td class=\"facts_value facts_value$styleadd\">";
	if ((showFactDetails($factref, $pid)) && (FactViewRestricted($pid, $factrec))) {
		if (isset($resn_value)) {
			switch($resn_value) {
			case 'privacy':
				echo '<img src="images/RESN_privacy.gif" alt="', i18n::translate('Privacy'), ' title="', i18n::translate('Privacy'), '" />'; break;
			case 'confidential':
				echo '<img src="images/RESN_confidential.gif" alt="', i18n::translate('Confidential'), ' title="', i18n::translate('Confidential'), '" />'; break;
			case 'locked':
				echo '<img src="images/RESN_locked.gif" alt="', i18n::translate('Do not change'), ' title="', i18n::translate('Do not change'), '" />'; break;
			}
			echo help_link('RESN');
		}
	}
	if ((showFactDetails($factref, $pid)) && (!FactViewRestricted($pid, $factrec))) {
		// -- first print TYPE for some facts
		if ($fact!="EVEN" && $fact!="FACT") {
			if (preg_match("/2 TYPE (.*)/", $factrec, $match)) {
				if ($fact=="MARR") {
					echo translate_fact("MARR_".strtoupper($match[1]), $label_person);
				} else {
					echo translate_fact(strtoupper($match[1]), $label_person);
				}
				echo "<br />";
			}
		}
		//-- print spouse name for marriage events
		if (preg_match("/_PGVS @(.*)@/", $factrec, $match)) {
			$spouse=Person::getInstance($match[1]);
			if ($spouse) {
				echo " <a href=\"", encode_url($spouse->getLinkUrl()), "\">";
				if ($spouse->canDisplayName()) {
					echo PrintReady($spouse->getFullName());
				} else {
					echo i18n::translate('Private');
				}
				echo "</a>";
			}
			if ($view!="preview" && $spouse) echo " - ";
			if ($view!="preview" && empty($SEARCH_SPIDER)) {
				echo "<a href=\"", encode_url("family.php?famid={$pid}"), "\">";
				if ($TEXT_DIRECTION == "ltr") echo " ", getLRM();
				else echo " ", getRLM();
				echo "[", i18n::translate('View Family');
				if ($SHOW_ID_NUMBERS) echo " ", getLRM(), "($pid)", getLRM();
				if ($TEXT_DIRECTION == "ltr") echo getLRM(), "]</a>\n";
				else echo getRLM(), "]</a>\n";
				echo "<br />";
			}
		}
		// -- find date for each fact
		echo format_fact_date($eventObj, true, true);
		//-- print other characterizing fact information
		if ($event!="" && $fact!="ASSO") {
			echo " ";
			$ct = preg_match("/@(.*)@/", $event, $match);
			if ($ct>0) {
				$gedrec=GedcomRecord::getInstance($match[1]);
				if (is_object($gedrec)) {
					if ($gedrec->getType()=='INDI') {
						echo '<a href="', encode_url($gedrec->getLinkUrl()), '">', $gedrec->getFullName(), '</a><br />';
					} elseif ($fact=='REPO') {
						print_repository_record($match[1]);
					} else {
						print_submitter_info($match[1]);
					}
				}
			}
			else if ($fact=="ALIA") {
				//-- strip // from ALIA tag for FTM generated gedcoms
				echo preg_replace("'/'", "", $event), "<br />";
			}
			/* -- see the format_fact_date function where this is handled
			else if ($event=="Y") {
				if (get_sub_record(2, "2 DATE", $factrec)=="") {
					echo i18n::translate('Yes'), "<br />";
				}
			}*/
			elseif ($event=="N") {
				if (get_sub_record(2, "2 DATE", $factrec)=="") {
					echo i18n::translate('No');
				}
			} elseif (strstr("URL WWW ", $fact." ")) {
				echo "<a href=\"", $event, "\" target=\"new\">", PrintReady($event), "</a>";
			} elseif (strstr("_EMAIL", $fact)) {
				echo "<a href=\"mailto:", $event, "\">", $event, "</a>";
			} elseif (strstr("AFN", $fact)) {
				echo '<a href="http://www.familysearch.org/Eng/Search/customsearchresults.asp?LDS=0&file_number=', urlencode($event), '" target="new">', $event, '</a>';
			} elseif (strstr('FAX PHON ', $fact.' ')) {
				echo getLRM(), $event, ' ' , getLRM();
			} elseif (strstr('FILE ', $fact.' ')) {
				if ($SHOW_MEDIA_FILENAME || WT_USER_GEDCOM_ADMIN) echo getLRM(), $event, ' ' , getLRM();
			} elseif ($event!='Y') {
				if (!strstr('ADDR _CREM ', substr($fact, 0, 5).' ')) {
					if ($factref=='file_size' || $factref=='image_size') {
						echo PrintReady($rawEvent);
					} else {
						echo PrintReady($event);
					}
				}
			}
			$temp = trim(get_cont(2, $factrec));
			if (strstr("PHON ADDR ", $fact." ")===false && $temp!="") {
				if ($WORD_WRAPPED_NOTES) echo " ";
				echo PrintReady($temp);
			}
		}
		//-- find description for some facts
		$ct = preg_match("/2 DESC (.*)/", $factrec, $match);
		if ($ct>0) echo PrintReady($match[1]);
		// -- print PLACe, TEMPle and STATus
		echo format_fact_place($eventObj, true, true, true);
		if (preg_match("/ (PLAC)|(STAT)|(TEMP)|(SOUR) /", $factrec)>0 || ($event && $fact!="ADDR")) print "<br />\n";
		// -- print BURIal -> CEMEtery
		$ct = preg_match("/2 CEME (.*)/", $factrec, $match);
		if ($ct>0) {
			if ($SHOW_FACT_ICONS && file_exists($WT_IMAGE_DIR."/facts/CEME.gif"))
				//echo $eventObj->Icon(), ' '; // echo incorrect fact icon !!!
				echo "<img src=\"{$WT_IMAGE_DIR}/facts/CEME.gif\" alt=\"".translate_fact('CEME')."\" title=\"".translate_fact('CEME')."\" align=\"middle\" /> ";
			echo "<b>", translate_fact('CEME'), ":</b> ", $match[1], "<br />\n";
		}
		//-- print address structure
		if ($fact!="ADDR") {
			print_address_structure($factrec, 2);
		}
		else {
			print_address_structure($factrec, 1);
		}
		// -- Enhanced ASSOciates > RELAtionship
		print_asso_rela_record($pid, $factrec, true, gedcom_record_type($pid, get_id_from_gedcom($GEDCOM)));
		// -- find _WT_USER field
		$ct = preg_match("/2 _WT_USER (.*)/", $factrec, $match);
		if ($ct>0) echo " - ", translate_fact('_WT_USER'), ": ", $match[1];
		// -- Find RESN tag
		if (isset($resn_value)) {
			switch($resn_value) {
			case 'privacy':
				echo '<img src="images/RESN_privacy.gif" alt="', i18n::translate('Privacy'), ' title="', i18n::translate('Privacy'), '" />'; break;
			case 'confidential':
				echo '<img src="images/RESN_confidential.gif" alt="', i18n::translate('Confidential'), ' title="', i18n::translate('Confidential'), '" />'; break;
			case 'locked':
				echo '<img src="images/RESN_locked.gif" alt="', i18n::translate('Do not change'), ' title="', i18n::translate('Do not change'), '" />'; break;
			}
			echo help_link('RESN');
		}
		if (preg_match("/\n2 FAMC @(.+)@/", $factrec, $match)) {
			echo "<br/><span class=\"label\">", translate_fact('FAMC'), ":</span> ";
			$family=Family::getInstance($match[1]);
			echo "<a href=\"", encode_url($family->getLinkUrl()), "\">", $family->getFullName(), "</a>";
			if (preg_match("/\n3 ADOP (HUSB|WIFE|BOTH)/", utf8_strtoupper($factrec), $match)) {
				echo '<br/><span class="indent"><span class="label">', translate_fact('ADOP'), ':</span> ';
				echo '<span class="field">';
				switch ($match[1]) {
				case 'HUSB':
				case 'WIFE':
					echo translate_fact($match[1]);
					break;
				case 'BOTH':
					echo translate_fact('HUSB'), '+', translate_fact('WIFE');
					break;
				}
				echo '</span></span>';
			}
		}
		// 0 SOUR/1 DATA/2 EVEN/3 DATE/3 PLAC
		$data_rec = get_sub_record(1, "1 DATA", $factrec, 1);
		if (!empty($data_rec)) {
			for ($even_num=1; $even_rec=get_sub_record(2, "2 EVEN", $data_rec, $even_num); ++$even_num) {
				$tmp1=get_gedcom_value('EVEN', 2, $even_rec, $truncate='', $convert=false);
				$tmp2=new GedcomDate(get_gedcom_value('DATE', 3, $even_rec, $truncate='', $convert=false));
				$tmp3=get_gedcom_value('PLAC', 3, $even_rec, $truncate='', $convert=false);
				$fact_string = "";
				if ($even_num>1)
					$fact_string .= "<br />";
				$fact_string .= "<b>";
				foreach (preg_split('/\W+/', $tmp1) as $key=>$value) {
					if ($key>0)
						$fact_string .= ", ";
					$fact_string .= i18n::translate($value);
				}
				$fact_string .= "</b>";
				if ($tmp2->Display(false, '', array())!="&nbsp;") $fact_string .= " - ".$tmp2->Display(false, '', array());
				if ($tmp3!='') $fact_string .= " - ".$tmp3;
				echo $fact_string;
			}
		}
		if ($fact!="ADDR") {
			//-- catch all other facts that could be here
			$special_facts = array("ADDR", "ALIA", "ASSO", "CEME", "CONT", "DATE", "DESC", "EMAIL",
			"FAMC", "FAMS", "FAX", "NOTE", "OBJE", "PHON", "PLAC", "RESN", "RELA", "SOUR", "STAT", "TEMP",
			"TIME", "TYPE", "WWW", "_EMAIL", "_WT_USER", "URL", "AGE", "_PGVS", "_PGVFS");
			$ct = preg_match_all("/\n2 (\w+) (.*)/", $factrec, $match, PREG_SET_ORDER);
			if ($ct>0) echo "<br />";
			for($i=0; $i<$ct; $i++) {
				$factref = $match[$i][1];
				if (!in_array($factref, $special_facts)) {
					$label = translate_fact($fact.':'.$factref, $label_person);
					if ($SHOW_FACT_ICONS && file_exists($WT_IMAGE_DIR."/facts/".$factref.".gif"))
						//echo $eventObj->Icon(), ' '; // print incorrect fact icon !!!
						echo "<img src=\"{$WT_IMAGE_DIR}/facts/", $factref, ".gif\" alt=\"{$label}\" title=\"{$label}\" align=\"middle\" /> ";
					else echo "<span class=\"label\">", $label, ": </span>";
					echo htmlspecialchars($match[$i][2], ENT_COMPAT, 'UTF-8');
					echo "<br />";
				}
			}
		}
		// -- find source for each fact
		print_fact_sources($factrec, 2);
		// -- find notes for each fact
		print_fact_notes($factrec, 2);
		//-- find multimedia objects
		print_media_links($factrec, 2, $pid);
	}
	echo "</td>";
	echo "\n\t\t</tr>";
}
//------------------- end print fact function

/**
 * print a submitter record
 *
 * find and print submitter information
 * @param string $sid  the Gedcom Xref ID of the submitter to print
 */
function print_submitter_info($sid) {
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);
	$srec = find_gedcom_record($sid, $ged_id);
	preg_match("/1 NAME (.*)/", $srec, $match);
	// PAF creates REPO record without a name
	// Check here if REPO NAME exists or not
	if (isset($match[1])) echo "$match[1]<br />";
	print_address_structure($srec, 1);
	print_media_links($srec, 1);
}

/**
 * print a repository record
 *
 * find and print repository information attached to a source
 * @param string $sid  the Gedcom Xref ID of the repository to print
 */
function print_repository_record($sid) {
	global $TEXT_DIRECTION;
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);
	if (displayDetailsById($sid, "REPO")) {
		$source = find_other_record($sid, $ged_id);
		$ct = preg_match("/1 NAME (.*)/", $source, $match);
		if ($ct > 0) {
			$ct2 = preg_match("/0 @(.*)@/", $source, $rmatch);
			if ($ct2>0) $rid = trim($rmatch[1]);
			echo "<span class=\"field\"><a href=\"", encode_url("repo.php?rid={$rid}"), "\"><b>", PrintReady($match[1]), "</b>&nbsp;&nbsp;&nbsp;";
			if ($TEXT_DIRECTION=="rtl") echo getRLM();
			echo "(", $sid, ")";
			if ($TEXT_DIRECTION=="rtl") echo getRLM();
			echo "</a></span><br />";
		}
		print_address_structure($source, 1);
		print_fact_notes($source, 1);
	}
}

/**
 * print a source linked to a fact (2 SOUR)
 *
 * this function is called by the print_fact function and other functions to
 * print any source information attached to the fact
 * @param string $factrec	The fact record to look for sources in
 * @param int $level		The level to look for sources at
 * @param boolean $return	whether to return the data or print the data
 */
function print_fact_sources($factrec, $level, $return=false) {
	global $WT_IMAGE_DIR, $WT_IMAGES, $SHOW_SOURCES, $EXPAND_SOURCES;
	$printDone = false;
	$data = "";
	$nlevel = $level+1;
	if ($SHOW_SOURCES<WT_USER_ACCESS_LEVEL) return "";
	// -- Systems not using source records [ 1046971 ]
	$ct = preg_match_all("/$level SOUR (.*)/", $factrec, $match, PREG_SET_ORDER);
	for($j=0; $j<$ct; $j++) {
		if (strpos($match[$j][1], "@")===false) {
			$srec = get_sub_record($level, "$level SOUR ", $factrec, $j+1);
			$srec = substr($srec, 6); // remove "2 SOUR"
			$srec = str_replace("\n".($level+1)." CONT ", "<br/>", $srec); // remove n+1 CONT
			$data .= "<br /><span class=\"label\">".i18n::translate('Source').":</span> <span class=\"field\">".PrintReady($srec)."</span><br />";
			$printDone = true;
		}
	}
	// -- find source for each fact
	$ct = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	$spos2 = 0;
	for($j=0; $j<$ct; $j++) {
		$sid = $match[$j][1];
		if (displayDetailsById($sid, "SOUR")) {
			$spos1 = strpos($factrec, "$level SOUR @".$sid."@", $spos2);
			$spos2 = strpos($factrec, "\n$level", $spos1);
			if (!$spos2) $spos2 = strlen($factrec);
			$srec = substr($factrec, $spos1, $spos2-$spos1);
			$lt = preg_match_all("/$nlevel \w+/", $srec, $matches);
			$data .= "<br />";
			$data .= "\n\t\t<span class=\"label\">";
			$elementID = $sid."-".floor(microtime()*1000000);
			if ($EXPAND_SOURCES) $plusminus="minus"; else $plusminus="plus";
			if ($lt>0) {
				$data .= "<a href=\"javascript:;\" onclick=\"expand_layer('$elementID'); return false;\"><img id=\"{$elementID}_img\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES[$plusminus]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"";
				if ($plusminus=="plus") $data .= i18n::translate('Show Details')."\" title=\"".i18n::translate('Show Details')."\" /></a> ";
				else $data .= i18n::translate('Hide Details')."\" title=\"".i18n::translate('Hide Details')."\" /></a> ";
			}
			$data .= i18n::translate('Source').":</span> <span class=\"field\">";
			$source=Source::getInstance($sid);
			if ($source) {
				$data .= "<a href=\"".encode_url($source->getLinkUrl())."\">".PrintReady($source->getFullName())."</a>";
			} else {
				$data .= $sid;
			}
			$data .= "</span>";

			$data .= "<div id=\"$elementID\"";
			if ($EXPAND_SOURCES) $data .= " style=\"display:block\"";
			$data .= " class=\"source_citations\">";
			// PUBL
			if ($source) {
				$text = get_gedcom_value("PUBL", "1", $source->getGedcomRecord());
				if (!empty($text)) {
					$data .= "<span class=\"label\">".translate_fact('PUBL').": </span>";
					$data .= $text;
				}
			}
			$data .= printSourceStructure(getSourceStructure($srec));
			$data .= "<div class=\"indent\">";
			ob_start();
			print_media_links($srec, $nlevel);
			$data .= ob_get_clean();
			$data .= print_fact_notes($srec, $nlevel, false, true);
			$data .= "</div>";
			$data .= "</div>";
			$printDone = true;
		}
	}
	if ($printDone) $data .= "<br />";
	if (!$return) echo $data;
	else return $data;
}

//-- Print the links to multi-media objects
function print_media_links($factrec, $level, $pid='') {
	global $MULTI_MEDIA, $TEXT_DIRECTION, $TBLPREFIX;
	global $SEARCH_SPIDER, $view;
	global $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;
	global $LB_URL_WIDTH, $LB_URL_HEIGHT;
	global $GEDCOM, $SHOW_ID_NUMBERS, $MEDIA_TYPES;
	$ged_id=get_id_from_gedcom($GEDCOM);
	if (!$MULTI_MEDIA) return;
	$nlevel = $level+1;
	if ($level==1) $size=50;
	else $size=25;
	if (preg_match_all("/$level OBJE(.*)/", $factrec, $omatch, PREG_SET_ORDER) == 0) return;
	$objectNum = 0;
	while ($objectNum < count($omatch)) {
		$media_id = str_replace("@", "", trim($omatch[$objectNum][1]));
		if (displayDetailsById($media_id, "OBJE")) {
			$row=
				WT_DB::prepare("SELECT m_titl, m_file, m_gedrec FROM {$TBLPREFIX}media where m_media=? AND m_gedfile=?")
				->execute(array($media_id, WT_GED_ID))
				->fetchOneRow(PDO::FETCH_ASSOC);

			// A new record, pending acceptance?
			if (!$row && WT_USER_CAN_EDIT) {
				$mediarec = find_updated_record($media_id, $ged_id);
				$row["m_file"] = get_gedcom_value("FILE", 1, $mediarec);
				$row["m_titl"] = get_gedcom_value("TITL", 1, $mediarec);
				if (empty($row["m_titl"])) $row["m_titl"] = get_gedcom_value("FILE:TITL", 1, $mediarec);
				$row["m_gedrec"] = $mediarec;
			}

			$mainMedia = check_media_depth($row["m_file"], "NOTRUNC");
			$thumbnail = thumbnail_file($mainMedia, true, false, $pid);
			$isExternal = isFileExternal($row["m_file"]);
			$mediaTitle = $row["m_titl"];

			// Determine the size of the mediafile
			$imgsize = findImageSize($mainMedia);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			if (showFactDetails("OBJE", $pid)) {
				if ($objectNum > 0) echo "<br clear=\"all\" />";
				echo "<table><tr><td>";
				if ($isExternal || media_exists($thumbnail)) {

					//LBox --------  change for Lightbox Album --------------------------------------------
					if (WT_USE_LIGHTBOX && preg_match("/\.(jpe?g|gif|png)$/i", $mainMedia)) {
						$name = trim($row["m_titl"]);
						echo "<a href=\"" . $mainMedia . "\" rel=\"clearbox[general_1]\" rev=\"" . $media_id . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "\">" . "\n";
					} else if (WT_USE_LIGHTBOX && preg_match("/\.(pdf|avi|txt)$/i", $mainMedia)) {
						require_once WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
						$name = trim($row["m_titl"]);
						echo "<a href=\"" . $mainMedia . "\" rel='clearbox({$LB_URL_WIDTH}, {$LB_URL_HEIGHT}, click)' rev=\"" . $media_id . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "\">" . "\n";
					// --------------------------------------------------------------------------------------
					} else if ($USE_MEDIA_VIEWER) {
						echo "<a href=\"", encode_url("mediaviewer.php?mid={$media_id}"), "\">";
					} else if (preg_match("/\.(jpe?g|gif|png)$/i", $mainMedia)) {
						echo "<a href=\"javascript:;\" onclick=\"return openImage('", rawurlencode($mainMedia), "', $imgwidth, $imgheight);\">";
					} else {
						echo "<a href=\"", encode_url("mediaviewer.php?mid={$media_id}"), "\">";
					}

					echo "<img src=\"", $thumbnail, "\" border=\"0\" align=\"" , $TEXT_DIRECTION== "rtl"?"right": "left" , "\" class=\"thumbnail\"";
					if ($isExternal) echo " width=\"", $THUMBNAIL_WIDTH, "\"";
					echo " alt=\"" . PrintReady($mediaTitle) . "\"";
					//LBox --------  change for Lightbox Album --------------------------------------------
					if ($row["m_titl"]) {
						echo " title=\"" . $row["m_titl"] . "\"";
					} else {
						echo " title=\"" . basename($row["m_file"]) . "\"";
					}
					// ---------------------------------------------------------------------------------------------
					echo "/>";
					echo "</a>";
				}
				echo "</td><td>";
				if(empty($SEARCH_SPIDER)) {
					echo "<a href=\"", encode_url("mediaviewer.php?mid={$media_id}"), "\">";
				}
				if ($TEXT_DIRECTION=="rtl" && !hasRTLText($mediaTitle)) echo "<i>" , getLRM() ,  PrintReady($mediaTitle), "</i>";
				else echo "<i>", PrintReady($mediaTitle), "</i><br />";
				if(empty($SEARCH_SPIDER)) {
					echo "</a>";
				}
				// NOTE: echo the notes of the media
				echo print_fact_notes($row["m_gedrec"], 1);
				// NOTE: echo the format of the media
				if (!empty($row["m_ext"])) {
					echo "\n\t\t\t<br /><span class=\"label\">", translate_fact('FORM'), ": </span> <span class=\"field\">", $row["m_ext"], "</span>";
					if($imgsize[2]!==false) {
						echo "\n\t\t\t<span class=\"label\"><br />", i18n::translate('Image Dimensions'), ": </span> <span class=\"field\" style=\"direction: ltr;\">" , $imgsize[0] , ($TEXT_DIRECTION =="rtl"?(" " . getRLM() . "x" . getRLM() . " ") : " x ") , $imgsize[1] , "</span>";
					}
				}
				if (preg_match('/2 DATE (.+)/', get_sub_record("FILE", 1, $row["m_gedrec"]), $match)) {
					$media_date=new GedcomDate($match[1]);
					$md = $media_date->Display(true);
					echo "\n\t\t\t<br /><span class=\"label\">", translate_fact('DATE'), ": </span> ", $md;
				}
				$ttype = preg_match("/".($nlevel+1)." TYPE (.*)/", $row["m_gedrec"], $match);
				if ($ttype>0) {
					$mediaType = $match[1];
					$varName = strtolower($mediaType);
					if (array_key_exists($varName, $MEDIA_TYPES)) {
						$mediaType = $MEDIA_TYPES[$varName];
					} else {
						$mediaType = i18n::translate('Other');
					}
					echo "\n\t\t\t<br /><span class=\"label\">", i18n::translate('Type'), ": </span> <span class=\"field\">$mediaType</span>";
				}
				//echo "</span>";
				echo "<br />\n";
				//-- print spouse name for marriage events
				$ct = preg_match("/WT_SPOUSE: (.*)/", $factrec, $match);
				if ($ct>0) {
					$spouse=Person::getInstance($match[1]);
					if ($spouse) {
						echo "<a href=\"", encode_url($spouse->getLinkUrl()), "\">";
						if ($spouse->canDisplayName()) {
							echo PrintReady($spouse->getFullName());
						} else {
							echo i18n::translate('Private');
						}
						echo "</a>";
					}
					if ($view!="preview" && $spouse && empty($SEARCH_SPIDER)) echo " - ";
					if ($view != "preview") {
						$ct = preg_match("/WT_FAMILY_ID: (.*)/", $factrec, $match);
						if ($ct>0) {
							$famid = trim($match[1]);
							if(empty($SEARCH_SPIDER)) {
								echo "<a href=\"", encode_url("family.php?famid={$famid}"), "\">[", i18n::translate('View Family');
								if ($SHOW_ID_NUMBERS) echo " " . getLRM() . "($famid)" . getLRM();
								echo "]</a>\n";
							}
						}
					}
				}
				echo "<br />\n";
				print_fact_notes($row["m_gedrec"], $nlevel);
				print_fact_sources($row["m_gedrec"], $nlevel);
				echo "</td></tr></table>\n";
			}
		}
		$objectNum ++;
	}
}
/**
 * print an address structure
 *
 * takes a gedcom ADDR structure and prints out a human readable version of it.
 * @param string $factrec	The ADDR subrecord
 * @param int $level		The gedcom line level of the main ADDR record
 */
function print_address_structure($factrec, $level) {
	global $POSTAL_CODE;

	//   $POSTAL_CODE = 'false' - before city, 'true' - after city and/or state
	//-- define per gedcom till can do per address countries in address languages
	//-- then this will be the default when country not recognized or does not exist
	//-- both Finland and Suomi are valid for Finland etc.
	//-- see http://www.bitboost.com/ref/international-address-formats.html

	$nlevel = $level+1;
	$ct = preg_match_all("/$level ADDR(.*)/", $factrec, $omatch, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$arec = get_sub_record($level, "$level ADDR", $factrec, $i+1);
		$resultText = "";
		if ($level>1) $resultText .= "\n\t\t<span class=\"label\">".translate_fact('ADDR').": </span><br /><div class=\"indent\">";
		$cn = preg_match("/$nlevel _NAME (.*)/", $arec, $cmatch);
		if ($cn>0) $resultText .= str_replace("/", "", $cmatch[1])."<br />\n";
		$resultText .= PrintReady(trim($omatch[$i][1]));
		$cont = get_cont($nlevel, $arec);
		if (!empty($cont)) $resultText .= str_replace(array(" ", "<br&nbsp;"), array("&nbsp;", "<br "), PrintReady($cont));
		else {
			if (strlen(trim($omatch[$i][1])) > 0) echo "<br />";
			$cs = preg_match("/$nlevel ADR1 (.*)/", $arec, $cmatch);
			if ($cs>0) {
				if ($cn==0) {
					$resultText .= "<br />";
					$cn=0;
				}
				$resultText .= PrintReady($cmatch[1]);
			}
			$cs = preg_match("/$nlevel ADR2 (.*)/", $arec, $cmatch);
			if ($cs>0) {
				if ($cn==0) {
					$resultText .= "<br />";
					$cn=0;
				}
				$resultText .= PrintReady($cmatch[1]);
			}

			if (!$POSTAL_CODE) {
				$cs = preg_match("/$nlevel POST (.*)/", $arec, $cmatch);
				if ($cs>0) {
					$resultText .= "<br />".PrintReady($cmatch[1]);
				}
				$cs = preg_match("/$nlevel CITY (.*)/", $arec, $cmatch);
				if ($cs>0) {
					$resultText .= " ".PrintReady($cmatch[1]);
				}
				$cs = preg_match("/$nlevel STAE (.*)/", $arec, $cmatch);
				if ($cs>0) {
					$resultText .= ", ".PrintReady($cmatch[1]);
				}
			}
			else {
				$cs = preg_match("/$nlevel CITY (.*)/", $arec, $cmatch);
				if ($cs>0) {
					$resultText .= "<br />".PrintReady($cmatch[1]);
				}
				$cs = preg_match("/$nlevel STAE (.*)/", $arec, $cmatch);
				if ($cs>0) {
					$resultText .= ", ".PrintReady($cmatch[1]);
				}
				$cs = preg_match("/$nlevel POST (.*)/", $arec, $cmatch);
				if ($cs>0) {
					$resultText .= " ".PrintReady($cmatch[1]);
				}
			}

			$cs = preg_match("/$nlevel CTRY (.*)/", $arec, $cmatch);
			if ($cs>0) {
				$resultText .= "<br />".PrintReady($cmatch[1]);
			}
		}
		if ($level>1) $resultText .= "</div>\n";
		// Here we can examine the resultant text and remove empty tags
		echo $resultText;
	}
	$resultText = "";
	$resultText .= "<table>";
	$ct = preg_match_all("/$level PHON (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	if ($ct>0) {
		for($i=0; $i<$ct; $i++) {
			$resultText .= "<tr>";
			$resultText .= "\n\t\t<td><span class=\"label\"><b>".translate_fact('PHON').": </b></span></td><td><span class=\"field\">";
			$resultText .= getLRM() . $omatch[$i][1] . getLRM();
			$resultText .= "</span></td></tr>\n";
		}
	}
	$ct = preg_match_all("/$level FAX (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	if ($ct>0) {
		for($i=0; $i<$ct; $i++) {
			$resultText .= "<tr>";
			$resultText .= "\n\t\t<td><span class=\"label\"><b>".translate_fact('FAX').": </b></span></td><td><span class=\"field\">";
			$resultText .= getLRM() . $omatch[$i][1] . getLRM();
			$resultText .= "</span></td></tr>\n";
		}
	}
	$ct = preg_match_all("/$level EMAIL (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	if ($ct>0) {
		for($i=0; $i<$ct; $i++) {
			$resultText .= "<tr>";
			$resultText .= "\n\t\t<td><span class=\"label\"><b>".translate_fact('EMAIL').": </b></span></td><td><span class=\"field\">";
			$resultText .= "<a href=\"mailto:".$omatch[$i][1]."\">".$omatch[$i][1]."</a>\n";
			$resultText .= "</span></td></tr>\n";
		}
	}
	$ct = preg_match_all("/$level (WWW|URL) (.*)/", $factrec, $omatch, PREG_SET_ORDER);
	if ($ct>0) {
		for($i=0; $i<$ct; $i++) {
			$resultText .= "<tr>";
			$resultText .= "\n\t\t<td><span class=\"label\"><b>".translate_fact('URL').": </b></span></td><td><span class=\"field\">";
			$resultText .= "<a href=\"".$omatch[$i][2]."\" target=\"_blank\">".$omatch[$i][2]."</a>\n";
			$resultText .= "</span></td></tr>\n";
		}
	}
	$resultText .= "</table>";
	if ($resultText!="<table></table>") echo $resultText;
}

function print_main_sources($factrec, $level, $pid, $linenum, $noedit=false) {
	global $view;
	global $WT_IMAGE_DIR, $WT_IMAGES, $SHOW_SOURCES;
	if ($SHOW_SOURCES<WT_USER_ACCESS_LEVEL) return;

	$nlevel = $level+1;
	$styleadd="";
	if (strpos($factrec, "WT_NEW")!==false) $styleadd="change_new";
	if (strpos($factrec, "WT_OLD")!==false) $styleadd="change_old";
	// -- find source for each fact
	$ct = preg_match_all("/$level SOUR @(.*)@/", $factrec, $match, PREG_SET_ORDER);
	$spos2 = 0;
	for($j=0; $j<$ct; $j++) {
		$sid = $match[$j][1];
		$spos1 = strpos($factrec, "$level SOUR @".$sid."@", $spos2);
		$spos2 = strpos($factrec, "\n$level", $spos1);
		if (!$spos2) $spos2 = strlen($factrec);
		$srec = substr($factrec, $spos1, $spos2-$spos1);
		if (!showFact("SOUR", $pid) || FactViewRestricted($pid, $factrec)) return false;
		if (displayDetailsById($sid, "SOUR")) {
			if ($level==2) echo "<tr class=\"row_sour2\">";
			else echo "<tr>";
			echo "<td class=\"descriptionbox";
			if ($level==2) echo " rela";
			echo " $styleadd center width20\">";
			if ($level==1) echo "<img class=\"icon\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["source"]["small"], "\" alt=\"\" /><br />";
			$temp = preg_match("/^\d (\w*)/", $factrec, $factname);
			$factlines = explode("\n", $factrec); // 1 BIRT Y\n2 SOUR ...
			$factwords = explode(" ", $factlines[0]); // 1 BIRT Y
			$factname = $factwords[1]; // BIRT
			$parent=GedcomRecord::getInstance($pid);
			if ($factname == "EVEN" || $factname=="FACT") {
				// Add ' EVEN' to provide sensible output for an event with an empty TYPE record
				$ct = preg_match("/2 TYPE (.*)/", $factrec, $ematch);
				if ($ct>0) {
					$factname = trim($ematch[1]);
					echo $factname;
				} else {
					echo translate_fact($factname, $parent);
				}
			} else {
				echo translate_fact($factname, $parent);
			}
			if (!$noedit && WT_USER_CAN_EDIT && !FactEditRestricted($pid, $factrec) && $styleadd!="red" && $view!="preview") {
				$menu = new Menu(i18n::translate('Edit'), "#", "right", "down");
				$menu->addOnclick("return edit_record('$pid', $linenum);");
				$menu->addClass("", "", "submenu");

				$submenu = new Menu(i18n::translate('Edit'), "#", "right");
				$submenu->addOnclick("return edit_record('$pid', $linenum);");
				$submenu->addClass("submenuitem", "submenuitem_hover");
				$menu->addSubMenu($submenu);

				$submenu = new Menu(i18n::translate('Delete'), "#", "right");
				$submenu->addOnclick("return delete_record('$pid', $linenum);");
				$submenu->addClass("submenuitem", "submenuitem_hover");
				$menu->addSubMenu($submenu);

				$submenu = new Menu(i18n::translate('Copy'), "#", "right");
				$submenu->addOnclick("return copy_record('$pid', $linenum);");
				$submenu->addClass("submenuitem", "submenuitem_hover");
				$menu->addSubMenu($submenu);

				echo " <div style=\"width:25px;\">";
				$menu->printMenu();
				echo "</div>";
			}
			echo "</td>";
			echo "\n\t\t\t<td class=\"optionbox $styleadd wrap\">";
			//echo "\n\t\t\t<td class=\"facts_value$styleadd\">";
			$source=Source::getInstance($sid);
			if ($source && showFactDetails("SOUR", $pid)) {
				echo "<a href=\"", encode_url($source->getLinkUrl()), "\">", PrintReady($source->getFullName()), "</a>";
				// PUBL
				$text = get_gedcom_value("PUBL", "1", $source->getGedcomRecord());
				if (!empty($text)) {
					echo "<br /><span class=\"label\">", translate_fact('PUBL'), ": </span>";
					echo $text;
				}
				// See if RESN tag prevents display or edit/delete
				$resn_tag = preg_match("/2 RESN (.*)/", $factrec, $rmatch);
				if ($resn_tag > 0) $resn_value = strtolower(trim($rmatch[1]));
				// -- Find RESN tag
				if (isset($resn_value)) {
					switch($resn_value) {
					case 'privacy':
						echo '<img src="images/RESN_privacy.gif" alt="', i18n::translate('Privacy'), ' title="', i18n::translate('Privacy'), '" />'; break;
					case 'confidential':
						echo '<img src="images/RESN_confidential.gif" alt="', i18n::translate('Confidential'), ' title="', i18n::translate('Confidential'), '" />'; break;
					case 'locked':
						echo '<img src="images/RESN_locked.gif" alt="', i18n::translate('Do not change'), ' title="', i18n::translate('Do not change'), '" />'; break;
					}
					echo help_link('RESN');
				}
				$cs = preg_match("/$nlevel EVEN (.*)/", $srec, $cmatch);
				if ($cs>0) {
					echo "<br /><span class=\"label\">", translate_fact('EVEN'), " </span><span class=\"field\">", $cmatch[1], "</span>";
					$cs = preg_match("/".($nlevel+1)." ROLE (.*)/", $srec, $cmatch);
					if ($cs>0) echo "\n\t\t\t<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">", translate_fact('ROLE'), " </span><span class=\"field\">$cmatch[1]</span>";
				}
				if ($source) {
					echo printSourceStructure(getSourceStructure($srec));
					echo "<div class=\"indent\">";
					print_media_links($srec, $nlevel);
					if ($nlevel==2) {
						print_media_links($source->getGedcomRecord(), 1);
					}
					print_fact_notes($srec, $nlevel);
					if ($nlevel==2) {
						print_fact_notes($source->getGedcomRecord(), 1);
					}
					echo "</div>";
				}
			}
			echo "</td></tr>";
		}
	}
}

/**
 *	Print SOUR structure
 *
 *  This function prints the input array of SOUR sub-records built by the
 *  getSourceStructure() function.
 *
 *  The input array is defined as follows:
 *	$textSOUR["PAGE"] = +1  Source citation
 *	$textSOUR["EVEN"] = +1  Event type
 *	$textSOUR["ROLE"] = +2  Role in event
 *	$textSOUR["DATA"] = +1  place holder (no text in this sub-record)
 *	$textSOUR["DATE"] = +2  Entry recording date
 *	$textSOUR["TEXT"] = +2  (array) Text from source
 *	$textSOUR["QUAY"] = +1  Certainty assessment
 *	$textSOUR["TEXT2"] = +1 (array) Text from source
 */
function printSourceStructure($textSOUR) {
	global $GEDCOM;

	$ged_id=get_id_from_gedcom($GEDCOM);
	$data='';
	$note_data='';
	if ($textSOUR["PAGE"]!="") {
		$data.="<br /><span class=\"label\">".translate_fact('PAGE').":&nbsp;&nbsp;</span><span class=\"field\">".PrintReady(expand_urls($textSOUR["PAGE"]))."</span>";
	}

	if ($textSOUR["EVEN"]!="") {
		$data.="<br /><span class=\"label\">".translate_fact('EVEN').":&nbsp;</span><span class=\"field\">".PrintReady($textSOUR["EVEN"])."</span>";
		if ($textSOUR["ROLE"]!="") {
			$data.="<br />&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"label\">".translate_fact('ROLE').":&nbsp;</span><span class=\"field\">".PrintReady($textSOUR["ROLE"])."</span>";
		}
	}

	if (count($textSOUR["NOTE"])!=0) {
		foreach($textSOUR["NOTE"] as $note) {
			$noterec = find_gedcom_record(str_replace("@", "", $note), $ged_id);
			if (!empty($noterec)) {
				$nt = preg_match("/0 ".$note." NOTE(.*)/", $noterec, $n1match);
				if ($nt==1) {
					$note_data.="&nbsp;&nbsp;<span class=\"field\">".print_note_record($n1match[1], 1, $noterec,  true, true)."</span>";
				}
			} else {
				$note_data.="&nbsp;&nbsp;".$note;
			}
		}
	}
	if ($textSOUR["DATE"]!="" || count($textSOUR["TEXT"])!=0) {
		if ($textSOUR["DATE"]!="") {
			$date=new GedcomDate($textSOUR["DATE"]);
			$data.="<br />&nbsp;&nbsp;<span class=\"label\">".translate_fact('DATA:DATE').":&nbsp;</span><span class=\"field\">".$date->Display(false)."</span>";
		}
		foreach($textSOUR["TEXT"] as $text) {
			$data.="<br />&nbsp;&nbsp;<span class=\"label\">".translate_fact('TEXT').":&nbsp;</span><span class=\"field\">".PrintReady(expand_urls($text))."</span>";
			if (!empty($text) && !empty($note_data)) $data.="<br />";	 
			$data.=$note_data;
		}
	}

	if ($textSOUR["QUAY"]!="") {
		$data.="<br /><span class=\"label\">".translate_fact('QUAY').":&nbsp;</span><span class=\"field\">".PrintReady($textSOUR["QUAY"])."</span>";
	}

	foreach($textSOUR["TEXT2"] as $text) {
		$data.="<br /><span class=\"label\">".translate_fact('TEXT').":&nbsp;</span><span class=\"field\">".PrintReady(expand_urls($text))."</span>";
	}
	return $data;
}

/**
 * Extract SOUR structure from the incoming Source sub-record
 *
 *  The output array is defined as follows:
 *	$textSOUR["PAGE"] = +1  Source citation
 *	$textSOUR["EVEN"] = +1  Event type
 *	$textSOUR["ROLE"] = +2  Role in event
 *	$textSOUR["DATA"] = +1  place holder (no text in this sub-record)
 *	$textSOUR["DATE"] = +2  Entry recording date
 *	$textSOUR["TEXT"] = +2  (array) Text from source
	$textSOUR["NOTE"] = +1  Note
 *	$textSOUR["QUAY"] = +1  Certainty assessment
 *	$textSOUR["TEXT2"] = +1 (array) Text from source
 */
function getSourceStructure($srec) {
	global $WORD_WRAPPED_NOTES;

	// Set up the output array
	$textSOUR = array();
	$textSOUR["PAGE"] = "";
	$textSOUR["EVEN"] = "";
	$textSOUR["ROLE"] = "";
	$textSOUR["DATA"] = "";
	$textSOUR["DATE"] = "";
	$textSOUR["TEXT"] = array();
	$textSOUR["NOTE"] = array();
	$textSOUR["QUAY"] = "";
	$textSOUR["TEXT2"] = array();

	if ($srec=="") return $textSOUR;

	$subrecords = explode("\n", $srec);
	$levelSOUR = substr($subrecords[0], 0, 1);
	for ($i=0; $i<count($subrecords); $i++) {
		$subrecords[$i] = trim($subrecords[$i]);
		$level = substr($subrecords[$i], 0, 1);
		$tag = substr($subrecords[$i], 2, 4);
		$text = substr($subrecords[$i], 7);
		$i++;
		for (; $i<count($subrecords); $i++) {
			$nextTag = substr($subrecords[$i], 2, 4);
			if ($nextTag!="CONT") {
				$i--;
				break;
			}
			if ($nextTag=="CONT") $text .= "<br />";
			$text .= rtrim(substr($subrecords[$i], 7));
		}
		if ($tag=="TEXT") {
			if ($level==($levelSOUR+1)) $textSOUR["TEXT2"][] = $text;
			else $textSOUR["TEXT"][] = $text;
		} else if ($tag=="NOTE") {
			$textSOUR["NOTE"][] = $text;
		} else {
			$textSOUR[$tag] = $text;
		}
	}

	return $textSOUR;
}

/**
 * print main note row
 *
 * this function will print a table row for a fact table for a level 1 note in the main record
 * @param string $factrec	the raw gedcom sub record for this note
 * @param int $level		The start level for this note, usually 1
 * @param string $pid		The gedcom XREF id for the level 0 record that this note is a part of
 * @param int $linenum		The line number in the level 0 record where this record was found.  This is used for online editing.
 * @param boolean $noedit	Whether or not to allow this fact to be edited
 */
function print_main_notes($factrec, $level, $pid, $linenum, $noedit=false) {
	global $GEDCOM;
	global $view;
	global $WT_IMAGE_DIR;
	global $WT_IMAGES;
	global $TEXT_DIRECTION;
	$ged_id=get_id_from_gedcom($GEDCOM);
	$styleadd="";
	if (strpos($factrec, "WT_NEW")!==false) $styleadd="change_new";
	if (strpos($factrec, "WT_OLD")!==false) $styleadd="change_old";
	$nlevel = $level+1;
	$ct = preg_match_all("/$level NOTE(.*)/", $factrec, $match, PREG_SET_ORDER);
	for($j=0; $j<$ct; $j++) {
		$nrec = get_sub_record($level, "$level NOTE", $factrec, $j+1);
		if (!showFact("NOTE", $pid)||FactViewRestricted($pid, $factrec)) return false;
		$nt = preg_match("/\d NOTE @(.*)@/", $match[$j][0], $nmatch);
		if ($nt>0) {
			$nid = $nmatch[1];
			if (empty($styleadd) && find_updated_record($nid, WT_GED_ID)!==null) {
				$styleadd = "change_old";
				$newfactrec = $factrec.="\nWT_NEW";
				print_main_notes($factrec, $level, $pid, $linenum, $noedit);
			}
		}
		if ($level>=2) echo "<tr class=\"row_note2\">";
		else echo "<tr>";
		echo "<td valign=\"top\" class=\"descriptionbox";
		if ($level>=2) echo " rela";
		echo " $styleadd center width20\">";
		if ($level<2) {
			echo "<img class=\"icon\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["notes"]["small"], "\" alt=\"\" />";
			if (strstr($factrec, "1 NOTE @" )) {
				echo "<br />", translate_fact('SHARED_NOTE');
			} else {
				echo "<br />", translate_fact('NOTE');
			}
		} else {
			$factlines = explode("\n", $factrec); // 1 BIRT Y\n2 NOTE ...
			$factwords = explode(" ", $factlines[0]); // 1 BIRT Y
			$factname = $factwords[1]; // BIRT
			$parent=GedcomRecord::getInstance($pid);
			if ($factname == "EVEN" || $factname=="FACT") {
				// Add ' EVEN' to provide sensible output for an event with an empty TYPE record
				$ct = preg_match("/2 TYPE (.*)/", $factrec, $ematch);
				if ($ct>0) {
					$factname = trim($ematch[1]);
					echo $factname;
				} else {
					echo translate_fact($factname, $parent);
				}
			} else {
				echo translate_fact($factname, $parent);
			}
		}
		if (!$noedit && WT_USER_CAN_EDIT && !FactEditRestricted($pid, $factrec) && $styleadd!="change_old" && $view!="preview") {
			$menu = new Menu(i18n::translate('Edit'), "#", "right", "down");
			$menu->addOnclick("return edit_record('$pid', $linenum);");
			$menu->addClass("", "", "submenu");

			$submenu = new Menu(i18n::translate('Edit'), "#", "right");
			$submenu->addOnclick("return edit_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('Delete'), "#", "right");
			$submenu->addOnclick("return delete_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('Copy'), "#", "right");
			$submenu->addOnclick("return copy_record('$pid', $linenum);");
			$submenu->addClass("submenuitem", "submenuitem_hover");
			$menu->addSubMenu($submenu);

			echo " <div style=\"width:25px;\">";
			$menu->printMenu();
			echo "</div>";
		}
		if ($nt==0) {
			//-- print embedded note records
			$text = preg_replace("/~~/", "<br />", trim($match[$j][1]));
			$text .= get_cont($nlevel, $nrec);
			$text = expand_urls($text);
			$text = PrintReady($text);
		}
		else {
			//-- print linked note records
			$noterec = find_gedcom_record($nid, $ged_id, true);
			$nt = preg_match("/0 @$nid@ NOTE (.*)/", $noterec, $n1match);
			$text ="";
			if ($nt>0) {
				// If Census assistant installed, enable hotspot link on shared note title ---------------------
				if (file_exists(WT_ROOT.'modules/GEDFact_assistant/_CENS/census_note_decode.php')) {
					$centitl  = str_replace("~~", "", trim($n1match[1]));
					$centitl  = str_replace("<br />", "", $centitl);
					$centitl  = "<a href=\"note.php?nid=$nid\">".$centitl."</a>";
				}else{
					$text = preg_replace("/~~/", "<br />", trim($n1match[1]));
				}
			}
			$text .= get_cont(1, $noterec);
			$text = expand_urls($text);
			$text = PrintReady($text)." <br />\n";
			// If Census assistant installed, and if Formatted Shared Note (using pipe "|" as delimiter) -------
			if ( strstr($text, "|") && file_exists(WT_ROOT.'modules/GEDFact_assistant/_CENS/census_note_decode.php') ) {
				require WT_ROOT.'modules/GEDFact_assistant/_CENS/census_note_decode.php';
			}else{
				$text = $centitl."".$text; 
			}
		}
		
		$align = "";
		if (!empty($text)) {
			if ($TEXT_DIRECTION=="rtl" && !hasRTLText($text) && hasLTRText($text)) $align=" align=\"left\"";
			if ($TEXT_DIRECTION=="ltr" && !hasLTRText($text) && hasRTLText($text)) $align=" align=\"right\"";
		}
		echo " </td>\n<td class=\"optionbox $styleadd wrap\" $align>";
		if (showFactDetails("NOTE", $pid)) {
			echo $text;
			if (!empty($noterec)) print_fact_sources($noterec, 1);
			// See if RESN tag prevents display or edit/delete
			$resn_tag = preg_match("/2 RESN (.*)/", $factrec, $rmatch);
			if ($resn_tag > 0) $resn_value = strtolower(trim($rmatch[1]));
			// -- Find RESN tag
			if (isset($resn_value)) {
				switch($resn_value) {
				case 'privacy':
					echo '<img src="images/RESN_privacy.gif" alt="', i18n::translate('Privacy'), ' title="', i18n::translate('Privacy'), '" />'; break;
				case 'confidential':
					echo '<img src="images/RESN_confidential.gif" alt="', i18n::translate('Confidential'), ' title="', i18n::translate('Confidential'), '" />'; break;
				case 'locked':
					echo '<img src="images/RESN_locked.gif" alt="', i18n::translate('Do not change'), ' title="', i18n::translate('Do not change'), '" />'; break;
				}
				echo help_link('RESN');
			}
			echo "<br />\n";
			print_fact_sources($nrec, $nlevel);
		}
		echo "</td></tr>";
	}
}

/**
 * Print the links to multi-media objects
 * @param string $pid	The the xref id of the object to find media records related to
 * @param int $level	The level of media object to find
 * @param boolean $related	Whether or not to grab media from related records
 */
function print_main_media($pid, $level=1, $related=false, $noedit=false) {
	global $TBLPREFIX, $GEDCOM, $MEDIATYPE;
	$ged_id=get_id_from_gedcom($GEDCOM);

	if (!showFact("OBJE", $pid)) return false;
	$gedrec = find_gedcom_record($pid, $ged_id, true);
	$ids = array($pid);

	//-- find all of the related ids
	if ($related) {
		$ct = preg_match_all("/1 FAMS @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$ids[] = trim($match[$i][1]);
		}
	}

	//LBox -- if  exists, get a list of the sorted current objects in the indi gedcom record  -  (1 _WT_OBJS @xxx@ .... etc) ----------
	$sort_current_objes = array();
	if ($level>0) $sort_regexp = "/".$level." _WT_OBJS @(.*)@/";
	else $sort_regexp = "/_WT_OBJS @(.*)@/";
	$sort_ct = preg_match_all($sort_regexp, $gedrec, $sort_match, PREG_SET_ORDER);
	for($i=0; $i<$sort_ct; $i++) {
		if (!isset($sort_current_objes[$sort_match[$i][1]])) $sort_current_objes[$sort_match[$i][1]] = 1;
		else $sort_current_objes[$sort_match[$i][1]]++;
		$sort_obje_links[$sort_match[$i][1]][] = $sort_match[$i][0];
	}
	// -----------------------------------------------------------------------------------------------

	// create ORDER BY list from Gedcom sorted records list  ---------------------------
	$orderbylist = 'ORDER BY '; // initialize
	foreach ($sort_match as $id) {
		$orderbylist .= "m_media='$id[1]' DESC, ";
	}
	$orderbylist = rtrim($orderbylist, ', ');
	// -----------------------------------------------------------------------------------------------

	//-- get a list of the current objects in the record
	$current_objes = array();
	if ($level>0) $regexp = "/".$level." OBJE @(.*)@/";
	else $regexp = "/OBJE @(.*)@/";
	$ct = preg_match_all($regexp, $gedrec, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		if (!isset($current_objes[$match[$i][1]])) $current_objes[$match[$i][1]] = 1;
		else $current_objes[$match[$i][1]]++;
		$obje_links[$match[$i][1]][] = $match[$i][0];
	}

	$media_found = false;
	$sqlmm = "SELECT ";
	$sqlmm .= "m_media, m_ext, m_file, m_titl, m_gedfile, m_gedrec, mm_gid, mm_gedrec FROM {$TBLPREFIX}media, {$TBLPREFIX}media_mapping where ";
	$sqlmm .= "mm_gid IN (";
	$vars=array();
	$i=0;
	foreach($ids as $key=>$id) {
		if ($i>0) $sqlmm .= ", ";
		$sqlmm .= "?";
		$vars[]=$id;
		$i++;
	}
	$sqlmm .= ") AND mm_gedfile=? AND mm_media=m_media AND mm_gedfile=m_gedfile ";
	$vars[]=WT_GED_ID;
	//-- for family and source page only show level 1 obje references
	if ($level>0) {
		$sqlmm .= "AND mm_gedrec LIKE ?";
		$vars[]="{$level} OBJE%";
	}

	// LBox --- media sort -------------------------------------
	if ($sort_ct>0) {
		$sqlmm .= $orderbylist;
	}else{
		$sqlmm .= " ORDER BY mm_gid DESC ";
	}
	// ---------------------------------------------------------------

	$rows=WT_DB::prepare($sqlmm)->execute($vars)->fetchAll(PDO::FETCH_ASSOC);

	$foundObjs = array();
	foreach ($rows as $rowm) {
		if (isset($foundObjs[$rowm['m_media']])) {
			if (isset($current_objes[$rowm['m_media']])) $current_objes[$rowm['m_media']]--;
			continue;
		}
		// NOTE: Determine the size of the mediafile
		$imgwidth = 300+40;
		$imgheight = 300+150;
		if (isFileExternal($rowm["m_file"])) {
			if (in_array($rowm["m_ext"], $MEDIATYPE)) {
				$imgwidth = 400+40;
				$imgheight = 500+150;
			}
			else {
				$imgwidth = 800+40;
				$imgheight = 400+150;
			}
		}
		else {
			$imgsize = @findImageSize(check_media_depth($rowm["m_file"], "NOTRUNC"));
			if ($imgsize[0]) {
				$imgwidth = $imgsize[0]+40;
				$imgheight = $imgsize[1]+150;
			}
		}
		$rows=array();

		//-- if there is a change to this media item then get the
		//-- updated media item and show it
		if ($newrec=find_updated_record($rowm["m_media"], $ged_id)) {
			$row = array();
			$row['m_media'] = $rowm["m_media"];
			$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
			$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
			if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
			$row['m_gedrec'] = $newrec;
			$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
			$ext = "";
			if ($et>0) $ext = substr(trim($ematch[1]), 1);
			$row['m_ext'] = $ext;
			$row['mm_gid'] = $pid;
			$row['mm_gedrec'] = $rowm["mm_gedrec"];
			$rows['new'] = $row;
			$rows['old'] = $rowm;
			// $current_objes[$rowm['m_media']]--;
		} else {
			if (!isset($current_objes[$rowm['m_media']]) && ($rowm['mm_gid']==$pid)) {
				$rows['old'] = $rowm;
			} else {
				$rows['normal'] = $rowm;
				if (isset($current_objes[$rowm['m_media']])) {
					$current_objes[$rowm['m_media']]--;
				}
			}
		}
		foreach($rows as $rtype => $rowm) {
			$res = print_main_media_row($rtype, $rowm, $pid);
			$media_found = $media_found || $res;
			$foundObjs[$rowm['m_media']]=true;
		}
		$media_found = true;
	}

	//-- objects are removed from the $current_objes list as they are printed
	//-- any objects left in the list are new objects recently added to the gedcom
	//-- but not yet accepted into the database.  We will print them too.
	foreach($current_objes as $media_id=>$value) {
		while($value>0) {
			$objSubrec = array_pop($obje_links[$media_id]);
			//-- check if we need to get the object from a remote location
			$ct = preg_match("/(.*):(.*)/", $media_id, $match);
			if ($ct>0) {
				require_once WT_ROOT.'includes/classes/class_serviceclient.php';
				$client = ServiceClient::getInstance($match[1]);
				if (!is_null($client)) {
					$newrec = $client->getRemoteRecord($match[2]);
					$row['m_media'] = $media_id;
					$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
					$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
					if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
					$row['m_gedrec'] = $newrec;
					$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
					$ext = "";
					if ($et>0) $ext = substr(trim($ematch[1]), 1);
					$row['m_ext'] = $ext;
					$row['mm_gid'] = $pid;
						$row['mm_gedrec'] = get_sub_record($objSubrec{0}, $objSubrec, $gedrec);
					$res = print_main_media_row('normal', $row, $pid);
					$media_found = $media_found || $res;
				}
			} else {
				$row = array();
				$newrec = find_gedcom_record($media_id, $ged_id, true);
				$row['m_media'] = $media_id;
				$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
				$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
				if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
				$row['m_gedrec'] = $newrec;
				$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
				$ext = "";
				if ($et>0) $ext = substr(trim($ematch[1]), 1);
				$row['m_ext'] = $ext;
				$row['mm_gid'] = $pid;
				$row['mm_gedrec'] = get_sub_record($objSubrec{0}, $objSubrec, $gedrec);
				$res = print_main_media_row('new', $row, $pid);
				$media_found = $media_found || $res;
			}
			$value--;
		}
	}
	if ($media_found) return true;
	else return false;
}

/**
 * print a media row in a table
 * @param string $rtype whether this is a 'new', 'old', or 'normal' media row... this is used to determine if the rows should be printed with an outline color
 * @param array $rowm	An array with the details about this media item
 * @param string $pid	The record id this media item was attached to
 */
function print_main_media_row($rtype, $rowm, $pid) {
	global $WT_IMAGE_DIR, $WT_IMAGES, $view, $TEXT_DIRECTION, $SERVER_URL;
	global $SHOW_ID_NUMBERS, $GEDCOM, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;
	global $SEARCH_SPIDER, $MEDIA_TYPES;

	if (!displayDetailsById($rowm['m_media'], 'OBJE') || FactViewRestricted($rowm['m_media'], $rowm['m_gedrec'])) {
		//echo $rowm['m_media'], " no privacy ";
		return false;
	}

	$styleadd="";
	if ($rtype=='new') $styleadd = "change_new";
	if ($rtype=='old') $styleadd = "change_old";
	// NOTEStart printing the media details
	$thumbnail = thumbnail_file($rowm["m_file"], true, false, $pid);
	$isExternal = isFileExternal($thumbnail);

	$linenum = 0;
	echo "\n\t\t<tr><td class=\"descriptionbox $styleadd center width20\"><img class=\"icon\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["media"]["small"], "\" alt=\"\" /><br />", translate_fact('OBJE');
	if ($rowm['mm_gid']==$pid && WT_USER_CAN_EDIT && (!FactEditRestricted($rowm['m_media'], $rowm['m_gedrec'])) && ($styleadd!="change_old") && ($view!="preview")) {
		$menu = new Menu(i18n::translate('Edit'), "#", "right", "down");
		$menu->addOnclick("return window.open('addmedia.php?action=editmedia&pid={$rowm['m_media']}&linktoid={$rowm['mm_gid']}', '_blank', 'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1');");
		$menu->addClass("", "", "submenu");

		$submenu = new Menu(i18n::translate('Edit'), "#", "right");
		$submenu->addOnclick("return window.open('addmedia.php?action=editmedia&pid={$rowm['m_media']}&linktoid={$rowm['mm_gid']}', '_blank', 'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1');");
		$submenu->addClass("submenuitem", "submenuitem_hover");
		$menu->addSubMenu($submenu);

		$submenu = new Menu(i18n::translate('Delete'), "#", "right");
		$submenu->addOnclick("return delete_record('$pid', 'OBJE', '".$rowm['m_media']."');");
		$submenu->addClass("submenuitem", "submenuitem_hover");
		$menu->addSubMenu($submenu);

		$submenu = new Menu(i18n::translate('Copy'), "#", "right");
		$submenu->addOnclick("return copy_record('".$rowm['m_media']."', 'media');");
		$submenu->addClass("submenuitem", "submenuitem_hover");
		$menu->addSubMenu($submenu);

		echo " <div style=\"width:25px;\">";
		$menu->printMenu();
		echo "</div>";
	}

	// NOTE Print the title of the media
	echo "</td><td class=\"optionbox wrap $styleadd\"><span class=\"field\">";
	if (showFactDetails("OBJE", $pid)) {
		$mediaTitle = $rowm["m_titl"];
		$subtitle = get_gedcom_value("TITL", 2, $rowm["mm_gedrec"]);
		if (!empty($subtitle)) $mediaTitle = $subtitle;
		$mainMedia = check_media_depth($rowm["m_file"], "NOTRUNC");
		if ($mediaTitle=="") $mediaTitle = basename($rowm["m_file"]);

		$imgsize = findImageSize($mainMedia);
		$imgwidth = $imgsize[0]+40;
		$imgheight = $imgsize[1]+150;

		// Check Filetype of media item ( URL, Local or Other )
		if (preg_match("/^https?:\/\//i", $rowm['m_file'])) {
			$file_type = 'url_';
		} else {
			$file_type = 'local_';
		}
		if (preg_match("/\.flv$/i", $rowm['m_file']) && file_exists(WT_ROOT.'modules/JWplayer/flvVideo.php')) {
			$file_type .= 'flv';
		} elseif (preg_match("/\.(jpg|jpeg|gif|png)$/i", $rowm['m_file'])) {
			$file_type .= 'image';
		} elseif (preg_match("/\.(pdf|avi)$/i", $rowm['m_file'])) {
			$file_type .= 'page';
		} elseif (preg_match("/\.mp3$/i", $rowm['m_file'])) {
			$file_type .= 'audio';
		} else {
			$file_type = 'other';
		}

		//Get media item Notes
		$haystack = $rowm["m_gedrec"];
		$needle   = "1 NOTE";
		$before   = substr($haystack, 0, strpos($haystack, $needle));
		$after    = substr(strstr($haystack, $needle), strlen($needle));
		$final    = $before.$needle.$after;
		$notes    = PrintReady(htmlspecialchars(addslashes(print_fact_notes($final, 1, true, true)), ENT_COMPAT, 'UTF-8'));

		$name = trim($rowm['m_titl']);

		// Get info on how to handle this media file
		//$mediaInfo = mediaFileInfo($rowm['m_file'], $thumbnail, $rowm['m_media'], $name, $notes);
		$mediaInfo = mediaFileInfo($mainMedia, $thumbnail, $rowm['m_media'], $name, $notes);

		//-- Thumbnail field
		echo '<a href="', $mediaInfo['url'], '">';
		echo '<img src="', $mediaInfo['thumb'], '" border="none" align="', $TEXT_DIRECTION=="rtl" ? "right":"left", '" class="thumbnail"', $mediaInfo['width'];
		echo ' alt="', PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')), '" title="', PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')), '" /></a>';

		if(empty($SEARCH_SPIDER)) {
			echo "<a href=\"", encode_url("mediaviewer.php?mid={$rowm['m_media']}"), "\">";
		}
		if ($TEXT_DIRECTION=="rtl" && !hasRTLText($mediaTitle)) {
			echo "<i>", getLRM(), PrintReady(htmlspecialchars($mediaTitle, ENT_COMPAT, 'UTF-8')."&nbsp;&nbsp;({$rowm['m_media']})");
		} else {
			echo "<i>", PrintReady(htmlspecialchars($mediaTitle, ENT_COMPAT, 'UTF-8')."&nbsp;&nbsp;({$rowm['m_media']})");
		}
		$addtitle = get_gedcom_value("TITL:_HEB", 2, $rowm["mm_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:_HEB", 2, $rowm["m_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:_HEB", 1, $rowm["m_gedrec"]);
		if (!empty($addtitle)) echo "<br />\n", PrintReady(htmlspecialchars($addtitle, ENT_COMPAT, 'UTF-8'));
		$addtitle = get_gedcom_value("TITL:ROMN", 2, $rowm["mm_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:ROMN", 2, $rowm["m_gedrec"]);
		if (empty($addtitle)) $addtitle = get_gedcom_value("TITL:ROMN", 1, $rowm["m_gedrec"]);
		if (!empty($addtitle)) echo "<br />\n", PrintReady(htmlspecialchars($addtitle, ENT_COMPAT, 'UTF-8'));
		echo "</i>";
		if(empty($SEARCH_SPIDER)) {
			echo "</a>";
		}

		// NOTE: echo the format of the media
		if (!empty($rowm["m_ext"])) {
			echo "\n\t\t\t<br /><span class=\"label\">", translate_fact('FORM'), ": </span> <span class=\"field\">", $rowm["m_ext"], "</span>";
			if(isset($imgsize) and $imgsize[2]!==false) {
				echo "\n\t\t\t<span class=\"label\"><br />", i18n::translate('Image Dimensions'), ": </span> <span class=\"field\" style=\"direction: ltr;\">", $imgsize[0], $TEXT_DIRECTION =="rtl"?(" " . getRLM() . "x" . getRLM(). " ") : " x ", $imgsize[1], "</span>";
			}
		}
		if (preg_match('/2 DATE (.+)/', get_sub_record("FILE", 1, $rowm["m_gedrec"]), $match)) {
			$media_date=new GedcomDate($match[1]);
			$md = $media_date->Display(true);
			echo "\n\t\t\t<br /><span class=\"label\">", translate_fact('DATE'), ": </span> ", $md;
		}
		$ttype = preg_match("/\d TYPE (.*)/", $rowm["m_gedrec"], $match);
		if ($ttype>0) {
			$mediaType = trim($match[1]);
			$varName = strtolower($mediaType);
			if (array_key_exists($varName, $MEDIA_TYPES)) {
				$mediaType = $MEDIA_TYPES[$varName];
			} else {
				$mediaType = i18n::translate('Other');
			}
			echo "\n\t\t\t<br /><span class=\"label\">", i18n::translate('Type'), ": </span> <span class=\"field\">$mediaType</span>";
		}
		echo "</span>";
		echo "<br />\n";
		//-- print spouse name for marriage events
		if ($rowm['mm_gid']!=$pid) {
			$spouse=null;
			$parents = find_parents($rowm['mm_gid']);
			if ($parents) {
				if (!empty($parents['HUSB']) && $parents['HUSB']!=$pid) {
					$spouse = Person::getInstance($parents['HUSB']);
				}
				if (!empty($parents['WIFE']) && $parents['WIFE']!=$pid) {
					$spouse = Person::getInstance($parents['WIFE']);
				}
			}
			if ($spouse) {
				echo "<a href=\"", $spouse->getLinkUrl(), "\">";
				if ($spouse->canDisplayName()) {
					echo PrintReady($spouse->getFullName());
				} else {
					echo i18n::translate('Private');
				}
				echo "</a>";
			}
			if(empty($SEARCH_SPIDER)) {
				if ($view!="preview" && $spouse) echo " - ";
				if ($view!="preview") {
						$famid = $rowm['mm_gid'];
						echo "<a href=\"", encode_url("family.php?famid={$famid}"), "\">[", i18n::translate('View Family');
						if ($SHOW_ID_NUMBERS) echo " " . getLRM() . "($famid)" . getLRM();
						echo "]</a>\n";
				}
			}
			echo "<br />\n";
		}
		//-- don't show _PRIM option to regular users
		if (WT_USER_GEDCOM_ADMIN) {
			$prim = get_gedcom_value("_PRIM", 2, $rowm["mm_gedrec"]);
			if (empty($prim)) $prim = get_gedcom_value("_PRIM", 1, $rowm["m_gedrec"]);
			if (!empty($prim)) {
				echo "<span class=\"label\">", translate_fact('_PRIM'), ":</span> ";
				if ($prim=="Y") echo i18n::translate('Yes'); else echo i18n::translate('No');
				echo "<br />\n";
			}
		}
		//-- don't show _THUM option to regular users
		if (WT_USER_GEDCOM_ADMIN) {
			$thum = get_gedcom_value("_THUM", 2, $rowm["mm_gedrec"]);
			if (empty($thum)) $thum = get_gedcom_value("_THUM", 1, $rowm["m_gedrec"]);
			if (!empty($thum)) {
				echo "<span class=\"label\">", translate_fact('_THUM'), ":</span> ";
				if ($thum=="Y") echo i18n::translate('Yes'); else echo i18n::translate('No');
				echo "<br />\n";
			}
		}
		print_fact_notes($rowm["m_gedrec"], 1);
		print_fact_notes($rowm["mm_gedrec"], 2);
		print_fact_sources($rowm["m_gedrec"], 1);
		print_fact_sources($rowm["mm_gedrec"], 2);
	}
	echo "</td></tr>";
	return true;
}

// -----------------------------------------------------------------------------
//  Extra print_facts_functions for lightbox and reorder media
// -----------------------------------------------------------------------------

if (WT_USE_LIGHTBOX) {
	require_once WT_ROOT.'modules/lightbox/functions/lightbox_print_media.php';
	require_once WT_ROOT.'modules/lightbox/functions/lightbox_print_media_row.php';
}

require_once WT_ROOT.'includes/functions/functions_media_reorder.php';

// -----------------------------------------------------------------------------
//  End extra print_facts_functions for lightbox and reorder media
// -----------------------------------------------------------------------------

?>
