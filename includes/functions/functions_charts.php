<?php
/**
 * Functions used for charts
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

define('WT_FUNCTIONS_CHARTS_PHP', '');

require_once WT_ROOT.'includes/classes/class_person.php';

/**
 * print a table cell with sosa number
 *
 * @param int $sosa
 * @param string $pid optional pid
 * @param string $arrowDirection   direction of link arrow
 */
function print_sosa_number($sosa, $pid = "", $arrowDirection = "up") {
	global $pbwidth, $pbheight, $WT_IMAGES;

	if (substr($sosa,-1,1)==".") {
		$personLabel = substr($sosa,0,-1);
	} else {
		$personLabel = $sosa;
	}
	if ($arrowDirection=="blank") {
		$visibility = "hidden";
	} else {
		$visibility = "normal";
	}
	echo "<td class=\"subheaders center\" style=\"vertical-align: middle; text-indent: 0px; margin-top: 0px; white-space: nowrap; visibility: ", $visibility, ";\">";
	echo getLRM(), $personLabel, getLRM();
	if ($sosa != "1" && $pid != "") {
		if ($arrowDirection=="left") {
			$dir = 0;
		} elseif ($arrowDirection=="right") {
			$dir = 1;
		} elseif ($arrowDirection== "down") {
			$dir = 3;
		} else {
			$dir = 2; // either "blank" or "up"
		}
		echo "<br />";
		print_url_arrow($pid, "#$pid", "$pid", $dir);
	}
	echo "</td>";
}

/**
 * print family header
 *
 * @param string $famid family gedcom ID
 */
function print_family_header($famid) {
	$family=Family::getInstance($famid);
	if ($family) {
		echo '<p class="name_head">', PrintReady($family->getFullName()), '</p>';
	}
}

/**
 * print the parents table for a family
 *
 * @param string $famid family gedcom ID
 * @param int $sosa optional child sosa number
 * @param string $label optional indi label (descendancy booklet)
 * @param string $parid optional parent ID (descendancy booklet)
 * @param string $gparid optional gd-parent ID (descendancy booklet)
 */
function print_family_parents($famid, $sosa = 0, $label="", $parid="", $gparid="", $personcount="1") {
	global $show_full, $TEXT_DIRECTION, $SHOW_EMPTY_BOXES, $pbwidth, $pbheight, $WT_IMAGES, $show_changes, $GEDCOM;

	$ged_id=get_id_from_gedcom($GEDCOM);

	$family = Family::getInstance($famid);
	if (is_null($family)) return;

	$husb = $family->getHusband();
	if (is_null($husb)) $husb = new Person('');
	$wife = $family->getWife();
	if (is_null($wife)) $wife = new Person('');

	if (!is_null($husb)) {
		$tempID = $husb->getXref();
		if (!empty($tempID)) echo "<a name=\"{$tempID}\"></a>";
	}
	if (!is_null($wife)) {
		$tempID = $wife->getXref();
		if (!empty($tempID)) echo "<a name=\"{$tempID}\"></a>";
	}
	if ($sosa != 0) {
		print_family_header($famid);
	}
	// -- get the new record and parents if in editing show changes mode
	if (find_gedcom_record($famid, $ged_id) != find_gedcom_record($famid, $ged_id, WT_USER_CAN_EDIT)) {
		$newrec = find_gedcom_record($famid, $ged_id, true);
		$newparents = find_parents_in_record($newrec);
	}

	/**
	 * husband side
	 */
	echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td rowspan=\"2\">";
	echo "<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\" border=\"0\"><tr>";
	if ($parid) {
		if ($husb->getXref()==$parid) print_sosa_number($label);
		else print_sosa_number($label, "", "blank");
	}
	else if ($sosa > 0) print_sosa_number($sosa * 2);
	if (isset($newparents) && $husb->getXref() != $newparents["HUSB"]) {
		echo "<td valign=\"top\" class=\"facts_valueblue\">";
		print_pedigree_person($newparents['HUSB'], 1, 2, $personcount);
	} else {
		echo "<td valign=\"top\">";
		print_pedigree_person($husb->getXref(), 1, 2, $personcount);
	}
	echo "</td></tr></table>";
	echo "</td>";
	// husband's parents
	$hfams = $husb->getChildFamilies();
	$hparents = false;
	$upfamid = "";
	if (count($hfams) > 0 or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		echo "<td rowspan=\"2\"><img src=\"".$WT_IMAGES["hline"]."\" alt=\"\" /></td><td rowspan=\"2\"><img src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"" . ($pbheight) . "\" alt=\"\" /></td>";
		echo "<td><img src=\"".$WT_IMAGES["hline"]."\" alt=\"\" /></td><td>";
		$hparents = false;
		foreach ($hfams as $hfamid=>$hfamily) {
			if (!is_null($hfamily)) {
				$hparents = find_parents_in_record($hfamily->getGedcomRecord());
				$upfamid = $hfamid;
				break;
			}
		}
		if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
			// husband's father
			echo "<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\" border=\"0\"><tr>";
			if ($sosa > 0) print_sosa_number($sosa * 4, $hparents['HUSB'], "down");
			if (!empty($gparid) and $hparents['HUSB']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
			echo "<td valign=\"top\">";
			print_pedigree_person($hparents['HUSB'], 1, 4, $personcount);
			echo "</td></tr></table>";
		}
		echo "</td>";
	}
	if (!empty($upfamid) and ($sosa!=-1)) {
		echo "<td valign=\"middle\" rowspan=\"2\">";
		print_url_arrow($upfamid, ($sosa==0 ? "?famid=$upfamid&amp;show_full=$show_full" : "#$upfamid"), "$upfamid", 1);
		echo "</td>";
	}
	if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		// husband's mother
		echo "</tr><tr><td><img src=\"".$WT_IMAGES["hline"]."\" alt=\"\" /></td><td>";
		echo "<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\" border=\"0\"><tr>";
		if ($sosa > 0) print_sosa_number($sosa * 4 + 1, $hparents['WIFE'], "down");
		if (!empty($gparid) and $hparents['WIFE']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
		echo "<td valign=\"top\">";
		print_pedigree_person($hparents['WIFE'], 1, 5, $personcount);
		echo "</td></tr></table>";
		echo "</td>";
	}
	echo "</tr></table>";
	if ($sosa!=0) {
		echo '<a href="', $family->getHtmlUrl(), '" class="details1">';
		echo str_repeat("&nbsp;", 10);
		$marriage = $family->getMarriage();
		if ($marriage->canShow()) {
			$marriage->print_simple_fact();
		} else echo i18n::translate('Private');
		echo "</a>";
	}
	else echo "<br />";

	/**
	 * wife side
	 */
	echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td rowspan=\"2\">";
	echo "<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\"><tr>";
	if ($parid) {
		if ($wife->getXref()==$parid) print_sosa_number($label);
		else print_sosa_number($label, "", "blank");
	}
	else if ($sosa > 0) print_sosa_number($sosa * 2 + 1);
	if (isset($newparents) && $wife->getXref() != $newparents["WIFE"]) {
		echo "<td valign=\"top\" class=\"facts_valueblue\">";
		print_pedigree_person($newparents['WIFE'], 1, 3, $personcount);
	} else {
		echo "<td valign=\"top\">";
		print_pedigree_person($wife->getXref(), 1, 3, $personcount);
	}
	echo "</td></tr></table>";
	echo "</td>";
	// wife's parents
	$hfams = $wife->getChildFamilies();
	$hparents = false;
	$upfamid = "";
	if (count($hfams) > 0 or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		echo "<td rowspan=\"2\"><img src=\"".$WT_IMAGES["hline"]."\" alt=\"\" /></td><td rowspan=\"2\"><img src=\"".$WT_IMAGES["vline"]."\" width=\"3\" height=\"" . ($pbheight) . "\" alt=\"\" /></td>";
		echo "<td><img src=\"".$WT_IMAGES["hline"]."\" alt=\"\" /></td><td>";
		$j = 0;
		foreach ($hfams as $hfamid=>$hfamily) {
			if (!is_null($hfamily)) {
				$hparents = find_parents_in_record($hfamily->getGedcomRecord());
				$upfamid = $hfamid;
				break;
			}
		}
		if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
			// wife's father
			echo "<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\"><tr>";
			if ($sosa > 0) print_sosa_number($sosa * 4 + 2, $hparents['HUSB'], "down");
			if (!empty($gparid) and $hparents['HUSB']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
			echo "<td valign=\"top\">";
			print_pedigree_person($hparents['HUSB'], 1, 6, $personcount);
			echo "</td></tr></table>";
		}
		echo "</td>";
	}
	if (!empty($upfamid) and ($sosa!=-1)) {
		echo "<td valign=\"middle\" rowspan=\"2\">";
		print_url_arrow($upfamid, ($sosa==0 ? "?famid=$upfamid&amp;show_full=$show_full" : "#$upfamid"), "$upfamid", 1);
		echo "</td>";
	}
	if ($hparents or ($sosa != 0 and $SHOW_EMPTY_BOXES)) {
		// wife's mother
		echo "</tr><tr><td><img src=\"".$WT_IMAGES["hline"]."\" alt=\"\" /></td><td>";
		echo "<table style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\"><tr>";
		if ($sosa > 0) print_sosa_number($sosa * 4 + 3, $hparents['WIFE'], "down");
		if (!empty($gparid) and $hparents['WIFE']==$gparid) print_sosa_number(trim(substr($label,0,-3),".").".");
		echo "<td valign=\"top\">";
		print_pedigree_person($hparents['WIFE'], 1, 7, $personcount);
		echo "</td></tr></table>";
		echo "</td>";
	}
	echo "</tr></table>";
}

/**
 * print the children table for a family
 *
 * @param string $famid family gedcom ID
 * @param string $childid optional child ID
 * @param int $sosa optional child sosa number
 * @param string $label optional indi label (descendancy booklet)
 */
function print_family_children($famid, $childid = "", $sosa = 0, $label="", $personcount="1") {
	global $pbwidth, $pbheight, $show_cousins, $WT_IMAGES, $show_changes, $GEDCOM, $TEXT_DIRECTION;

	$ged_id=get_id_from_gedcom($GEDCOM);

	$family=Family::getInstance($famid);
	$children=$family->getChildrenIds();
	$numchil=$family->getNumberOfChildren();
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"2\"><tr>";
	if ($sosa>0) echo "<td></td>";
	echo "<td><span class=\"subheaders\">".i18n::translate('Children')."</span>";
	echo '<span class="font11">&nbsp;&nbsp;', getLRM(), '(';
	if ($numchil==0) {
		echo i18n::translate('No children');
	} else if ($numchil==1) {
		echo i18n::translate('1 child');
	} else {
		echo $numchil, '&nbsp;', i18n::translate('children');
	}
	echo ')', getLRM(), '</span>';
	echo "<br />";
	// moved to top of list, changed from style to class, and font12 added by Nigel
	if ($sosa==0 && WT_USER_CAN_EDIT) {
		echo "<br />";
		echo "<span class='nowrap font12'>";
		echo "<a href=\"javascript:;\" onclick=\"return addnewchild('$famid','');\">" . i18n::translate('Add a child to this family') . "</a>";
		echo " <a href=\"javascript:;\" onclick=\"return addnewchild('$famid','M');\">[".Person::sexImage('M', 'small', i18n::translate('Son'     ))."]</a>";
		echo " <a href=\"javascript:;\" onclick=\"return addnewchild('$famid','F');\">[".Person::sexImage('F', 'small', i18n::translate('Daughter'))."]</a>";
		echo help_link('add_child');
		echo "</span>";
		echo "<br /><br />";
	}
	echo "</td>";
	if ($sosa>0) echo "<td></td><td></td>";
	echo "</tr>";

	$newchildren = array();
	$oldchildren = array();
	if (WT_USER_CAN_EDIT) {
		if (!isset($_REQUEST['show_changes']) || $_REQUEST['show_changes']=='yes') {
			$newrec = find_gedcom_record($famid, $ged_id, true);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $newrec, $match, PREG_SET_ORDER);
			if ($ct > 0) {
				$oldchil = array();
				for ($i = 0; $i < $ct; $i++) {
					if (!in_array($match[$i][1], $children)) $newchildren[] = $match[$i][1];
					else $oldchil[] = $match[$i][1];
				}
				foreach ($children as $indexval => $chil) {
					if (!in_array($chil, $oldchil)) $oldchildren[] = $chil;
				}
				//-- if there are no old or new children then the children were reordered
				if ((count($newchildren)==0)&&(count($oldchildren)==0)) {
					$children = array();
					for ($i = 0; $i < $ct; $i++) {
						$children[] = $match[$i][1];
					}
				}
			}
		}
	}
	$nchi=1;
	if ((count($children) > 0) || (count($newchildren) > 0) || (count($oldchildren) > 0)) {
		foreach ($children as $indexval => $chil) {
			if (!in_array($chil, $oldchildren)) {
				echo "<tr>";
				if ($sosa != 0) {
					if ($chil == $childid) {
						print_sosa_number($sosa, $childid);
					} elseif (empty($label)) {
						print_sosa_number("");
					} else {
						print_sosa_number($label.($nchi++).".");
					}
				}
				echo "<td valign=\"middle\" >";
				print_pedigree_person($chil, 1, 8, $personcount);
				$personcount++;
				echo "</td>";
				if ($sosa != 0) {
					// loop for all families where current child is a spouse
					$famids = find_sfamily_ids($chil);
					$maxfam = count($famids)-1;
					for ($f=0; $f<=$maxfam; $f++) {
						$famid_child = $famids[$f];
						if (!$famid_child) continue;
						$parents = find_parents($famid_child);
						if (!$parents) continue;
						if ($parents["HUSB"] == $chil) $spouse = $parents["WIFE"];
						else $spouse =  $parents["HUSB"];
						// multiple marriages
						if ($f>0) {
							echo "</tr><tr><td>&nbsp;</td>";
							echo "<td valign=\"top\"";
							if ($TEXT_DIRECTION == "rtl") echo " align=\"left\">";
							else echo " align=\"right\">";
							//if ($f==$maxfam) echo "<img height=\"50%\"";
							//else echo "<img height=\"100%\"";
							if ($f==$maxfam) echo "<img height=\"".($pbheight/2-3)."px\"";
							else echo "<img height=\"".$pbheight."px\"";
							echo " width=\"3\" src=\"".$WT_IMAGES["vline"]."\" alt=\"\" />";
							echo "</td>";
						}
						echo "<td class=\"details1\" valign=\"middle\" align=\"center\">";
						$famrec = find_family_record($famid_child, $ged_id);
						$marrec = get_sub_record(1, "1 MARR", $famrec);
						$divrec = get_sub_record(1, "1 DIV",  $famrec);
						if (canDisplayFact($famid_child, $ged_id, $marrec)) {
							// marriage date
							$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $marrec, $match);
							if ($ct>0) echo "<span class=\"date\">".trim($match[1])."</span>";
							// divorce date
							$ct = preg_match("/2 DATE.*(\d\d\d\d)/", $divrec, $match);
							if ($ct>0) echo "-<span class=\"date\">".trim($match[1])."</span>";
						}
						echo "<br /><img width=\"100%\" height=\"3\" src=\"".$WT_IMAGES["hline"]."\" alt=\"\" />";
						// family link
						if ($famid_child) {
							$family_child = Family::getInstance($famid_child);
							if ($family_child) {
								echo "<br />";
								echo '<a class="details1" href="', $family_child->getHtmlUrl(), '">';
								// TODO: shouldn't there be something inside this <a></a>
								echo "</a>";
							}
						}
						echo "</td>";
						// spouse information
						echo "<td style=\"vertical-align: center;";
						if (!empty($divrec)) echo " filter:alpha(opacity=40);-moz-opacity:0.4\">";
						else echo "\">";
						print_pedigree_person($spouse, 1, 9, $personcount);
						$personcount++;
						echo "</td>";
						// cousins
						if ($show_cousins) {
							print_cousins($famid_child, $personcount);
							$personcount++;
						}
					}
				}
				echo "</tr>";
			}
		}
		foreach ($newchildren as $indexval => $chil) {
			echo "<tr >";
			echo "<td valign=\"top\" class=\"facts_valueblue\" style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\">";
			print_pedigree_person($chil, 1, 0, $personcount);
			$personcount++;
			echo "</td></tr>";
		}
		foreach ($oldchildren as $indexval => $chil) {
			echo "<tr >";
			echo "<td valign=\"top\" class=\"facts_valuered\" style=\"width: " . ($pbwidth) . "px; height: " . $pbheight . "px;\">";
			print_pedigree_person($chil, 1, 0, $personcount);
			$personcount++;
			echo "</td></tr>";
		}
		// message 'no children' except for sosa
	}
	else if ($sosa<1) {
		echo "<tr><td valign=\"top\" >";

		$nchi = "";
		$famrec = find_gedcom_record($famid, $ged_id, true);
		$ct = preg_match("/1 NCHI (\w+)/", $famrec, $match);
		if ($ct>0) $nchi = $match[1];
		else {
			$famrec = find_family_record($famid, $ged_id);
			$ct = preg_match("/1 NCHI (\w+)/", $famrec, $match);
			if ($ct>0) $nchi = $match[1];
		}
		if ($nchi=="0") echo "<img src=\"images/small/childless.gif\" alt=\"".i18n::translate('This family remained childless')."\" title=\"".i18n::translate('This family remained childless')."\" /> ".i18n::translate('This family remained childless');
		//else echo i18n::translate('No children');
		echo "</td></tr>";
	}
	else {
		echo "<tr>";
		print_sosa_number($sosa, $childid);
		echo "<td valign=\"top\">";
		print_pedigree_person($childid, 1, 0, $personcount);
		$personcount++;
		echo "</td></tr>";
	}
	echo "</table><br />";
}
/**
 * print the facts table for a family
 *
 * @param string $famid family gedcom ID
 * @param int $sosa optional child sosa number
 */
function print_family_facts(&$family, $sosa = 0) {
	global $pbwidth, $pbheight;
	global $nonfacts;
	global $TEXT_DIRECTION, $GEDCOM;
	global $show_changes;
	global $linkToID;

	$famid=$family->getXref();
	// -- if both parents are displayable then print the marriage facts
	if ($family->canDisplayDetails()) {
		$linkToID = $famid; // -- Tell addmedia.php what to link to
		// -- array of GEDCOM elements that will be found but should not be displayed
		$nonfacts = array("FAMS", "FAMC", "MAY", "BLOB", "HUSB", "WIFE", "CHIL", "");

		// -- find all the fact information
		$indifacts = $family->getFacts();

		if (count($indifacts) > 0) {
			sort_facts($indifacts);
			echo "<span class=\"subheaders\">" . i18n::translate('Family Group Information');
			echo "</span><br /><table class=\"facts_table\">";
			/* @var $value Event */
			foreach ($indifacts as $key => $value) {
				if ($value->getTag()!="SOUR" && $value->getTag()!="OBJE" && $value->getTag()!="NOTE")
					print_fact($value);
			}
			// do not print otheritems for sosa
			if ($sosa == 0) {
				foreach ($indifacts as $key => $value) {
					$fact = $value->getTag();
					// -- handle special source fact case
					if ($fact == "SOUR") {
						print_main_sources($value->getGedComRecord(), 1, $famid, $value->getLineNumber());
					}
					// -- handle special note fact case
					else if ($fact == "NOTE") {
						print_main_notes($value->getGedComRecord(), 1, $famid, $value->getLineNumber());
					}
				}
				// NOTE: Print the media
				print_main_media($famid);
			}
		}
		else {
			if ($sosa==0) {
				echo "<span class=\"subheaders\">" . i18n::translate('Family Group Information');
				echo "</span><br />";
			}
			echo "<table class=\"facts_table\">";
			if ($sosa == 0) {
				echo "<tr><td class=\"messagebox\" colspan=\"2\">";
				echo i18n::translate('No facts for this family.');
				echo "</td></tr>";
			}
		}
		// -- new fact link
		if ($sosa==0 && WT_USER_CAN_EDIT) {
			print_add_new_fact($famid, $indifacts, "FAM");

			// -- new note
			echo "<tr><td class=\"descriptionbox\">";
			echo i18n::translate('Add Note');
			echo help_link('add_note');
			echo "</td><td class=\"optionbox\">";
			echo "<a href=\"javascript:;\" onclick=\"return add_new_record('$famid','NOTE');\">" . i18n::translate('Add a new note') . "</a>";
			echo "<br />";
			echo "</td></tr>";

			// -- new shared note
			echo "<tr><td class=\"descriptionbox\">";
			echo i18n::translate('Add Shared Note');
			echo help_link('add_shared_note');
			echo "</td><td class=\"optionbox\">";
			echo "<a href=\"javascript:;\" onclick=\"return add_new_record('$famid','SHARED_NOTE');\">" . i18n::translate('Add a new shared note') . "</a>";
			echo "<br />";
			echo "</td></tr>";

			// -- new media
			echo "<tr><td class=\"descriptionbox\">";
			echo i18n::translate('Add media');
			echo help_link('add_media');
			echo "</td><td class=\"optionbox\">";
			echo "<a href=\"javascript: ".i18n::translate('Add media')."\" onclick=\"window.open('addmedia.php?action=showmediaform&linktoid={$famid}', '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1'); return false;\">".i18n::translate('Add a new media item')."</a>";
			echo "<br />";
			echo "<a href=\"javascript:;\" onclick=\"window.open('inverselink.php?linktoid={$famid}&linkto=family', '_blank', 'top=50,left=50,width=400,height=300,resizable=1,scrollbars=1'); return false;\">".i18n::translate('Link to an existing Media item')."</a>";
			echo "</td></tr>";

			// -- new source citation
			echo "<tr><td class=\"descriptionbox\">";
			echo i18n::translate('Add Source Citation');
			echo help_link('add_source');
			echo "</td><td class=\"optionbox\">";
			echo "<a href=\"javascript:;\" onclick=\"return add_new_record('$famid','SOUR');\">" . i18n::translate('Add a new source citation') . "</a>";
			echo "<br />";
			echo "</td></tr>";
			// -- end new objects
		}
		echo "</table>";
	}
}

/**
 * print a family with Sosa-Stradonitz numbering system
 * ($rootid=1, father=2, mother=3 ...)
 *
 * @param string $famid family gedcom ID
 * @param string $childid tree root ID
 * @param string $sosa starting sosa number
 * @param string $label optional indi label (descendancy booklet)
 * @param string $parid optional parent ID (descendancy booklet)
 * @param string $gparid optional gd-parent ID (descendancy booklet)
 */
function print_sosa_family($famid, $childid, $sosa, $label="", $parid="", $gparid="", $personcount="1") {
	global $pbwidth, $pbheight;

	echo "<hr />";
	echo "<p style='page-break-before:always' />";
	if (!empty($famid)) echo "<a name=\"{$famid}\"></a>";
	print_family_parents($famid, $sosa, $label, $parid, $gparid, $personcount);
	$personcount++;
	echo "<br />";
	echo "<table width=\"95%\"><tr><td valign=\"top\" style=\"width: " . ($pbwidth) . "px;\">";
	print_family_children($famid, $childid, $sosa, $label, $personcount);
	echo "</td></tr></table>";
	echo "<br />";
}
/**
 * check root id for pedigree tree
 *
 * @param string $rootid root ID
 * @return string $rootid validated root ID
 */
function check_rootid($rootid) {
	global $PEDIGREE_ROOT_ID, $USE_RIN;
	// -- if the $rootid is not already there then find the first person in the file and make him the root
	if (!find_person_record($rootid, WT_GED_ID)) {
		if (find_person_record(WT_USER_ROOT_ID, WT_GED_ID)) {
			$rootid=WT_USER_ROOT_ID;
		} else {
			if (find_person_record(WT_USER_GEDCOM_ID, WT_GED_ID)) {
				$rootid=WT_USER_GEDCOM_ID;
			} else {
				if (find_person_record($PEDIGREE_ROOT_ID, WT_GED_ID)) {
					$rootid=trim($PEDIGREE_ROOT_ID);
				} else {
					$rootid=get_first_xref('INDI', WT_GED_ID);
					// If there are no users in the gedcom, do something.
					if (!$rootid) {
						$rootid='I1';
					}
				}
			}
		}
	}

	if ($USE_RIN) {
		$indirec = find_person_record($rootid, WT_GED_ID);
		if ($indirec == false) $rootid = find_rin_id($rootid);
	}

	return $rootid;
}

/**
 * creates an array with all of the individual ids to be displayed on an ascendancy chart
 *
 * the id in position 1 is the root person.  The other positions are filled according to the following algorithm
 * if an individual is at position $i then individual $i's father will occupy position ($i*2) and $i's mother
 * will occupy ($i*2)+1
 *
 * @param string $rootid
 * @return array $treeid
 */
function ancestry_array($rootid, $maxgen=0) {
	global $PEDIGREE_GENERATIONS, $SHOW_EMPTY_BOXES;
	// -- maximum size of the id array
	if ($maxgen==0) $maxgen = $PEDIGREE_GENERATIONS;
	$treesize = pow(2, ($maxgen));

	$treeid = array();
	$treeid[0] = "";
	$treeid[1] = $rootid;
	// -- fill in the id array
	for ($i = 1; $i < ($treesize / 2); $i++) {
		$treeid[($i * 2)] = false; // -- father
		$treeid[($i * 2) + 1] = false; // -- mother
		if (!empty($treeid[$i])) {
			$person = Person::getInstance($treeid[$i]);
			$family = $person->getPrimaryChildFamily();
			// Store the prefered parents
			if (!empty($family)) {
				$treeid[($i * 2)] = $family->getHusbId();
				$treeid[($i * 2) + 1] = $family->getWifeId();
			}
		}
	}
	return $treeid;
}

/**
 * print an arrow to a new url
 *
 * @param string $id Id used for arrow img name (must be unique on the page)
 * @param string $url target url
 * @param string $label arrow label
 * @param string $dir arrow direction 0=left 1=right 2=up 3=down (default=2)
 */
function print_url_arrow($id, $url, $label, $dir=2) {
	global $WT_IMAGES, $TEXT_DIRECTION;

	if ($id=="" or $url=="") return;

	// arrow direction
	$adir=$dir;
	if ($TEXT_DIRECTION=="rtl" and $dir==0) $adir=1;
	if ($TEXT_DIRECTION=="rtl" and $dir==1) $adir=0;

	// arrow style     0         1         2         3
	$array_style=array("larrow", "rarrow", "uarrow", "darrow");
	$astyle=$array_style[$adir];

	echo "<a href=\"$url\" onmouseover=\"swap_image('".$astyle.$id."',$adir); window.status ='" . $label . "'; return true; \" onmouseout=\"swap_image('".$astyle.$id."',$adir); window.status=''; return true; \"><img id=\"".$astyle.$id."\" src=\"".$WT_IMAGES[$astyle]."\" hspace=\"0\" vspace=\"0\" border=\"0\" alt=\"$label\" title=\"$label\" /></a>";
}

/**
 * builds and returns sosa relationship name in the active language
 *
 * @param string $sosa sosa number
 */
function get_sosa_name($sosa) {
	$relations=array();
	while ($sosa>1) {
		if ($sosa%2==1) {
			$sosa-=1;
			array_unshift($relations, 'mother');
		} else {
			array_unshift($relations, 'father');
		}
		$sosa/=2;
	}
	array_unshift($relations, 'self');
	$path=array('relations'=>$relations, 'path'=>$relations); // path is just a dummy
	return get_relationship_name($path);
}


/**
 * find last family ID where this person is a spouse
 *
 * @param string $pid individual ID
 * @return string last sfam ID
 */
function find_last_sfam($pid) {
	$famids = find_sfamily_ids($pid);
	$f = count($famids);
	if ($f<1) return false;
	else return $famids[$f-1];
}

/**
 * find last spouse for this person
 *
 * @param string $pid individual ID
 * @return string last spouse ID
 */
function find_last_spouse($pid) {
	$famid = find_last_sfam($pid);
	if (!$famid) return false;
	$parents = find_parents($famid);
	if (!$parents) return false;
	if ($parents["HUSB"] == $pid) return $parents["WIFE"];
	else return $parents["HUSB"];
}

/**
 * print cousins list
 *
 * @param string $famid family ID
 */
function print_cousins($famid, $personcount="1") {
	global $show_full, $bheight, $bwidth, $WT_IMAGES, $TEXT_DIRECTION, $GEDCOM;

	$ged_id=get_id_from_gedcom($GEDCOM);

	$family=Family::getInstance($famid);
	$fchildren=$family->getChildrenIds();

	$kids = count($fchildren);
	$save_show_full = $show_full;
	if ($save_show_full) {
		$bheight/=4;
		$bwidth-=40;
	}
	$show_full = false;
	echo "<td valign=\"middle\" height=\"100%\">";
	if ($kids) {
		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" ><tr valign=\"middle\">";
		if ($kids>1) echo "<td rowspan=\"".$kids."\" valign=\"middle\" align=\"right\"><img width=\"3px\" height=\"". (($bheight+5) * ($kids-1)) ."px\" src=\"".$WT_IMAGES["vline"]."\" alt=\"\" /></td>";
		$ctkids = count($fchildren);
		$i = 1;
		foreach ($fchildren as $indexval => $fchil) {
			echo "<td><img width=\"10px\" height=\"3px\" style=\"padding-";
			if ($TEXT_DIRECTION=="ltr") echo "right";
			else echo "left";
			echo ": 2px;\" src=\"".$WT_IMAGES["hline"]."\" alt=\"\" /></td><td>";
			print_pedigree_person($fchil, 1 , 0, $personcount);
			$personcount++;
			echo "</td></tr>";
			if ($i < $ctkids) {
				echo "<tr>";
				$i++;
			}
		}
		echo "</table>";
	}
	else {
		$famrec = find_family_record($famid, $ged_id);
		$ct = preg_match("/1 NCHI (\w+)/", $famrec, $match);
		if ($ct>0) $nchi = $match[1];
		else $nchi = "";
		if ($nchi=="0") echo "&nbsp;<img src=\"images/small/childless.gif\" alt=\"".i18n::translate('This family remained childless')."\" title=\"".i18n::translate('This family remained childless')."\" />";
	}
	$show_full = $save_show_full;
	if ($save_show_full) {
		$bheight*=4;
		$bwidth+=40;
	}
	echo "</td>";
}
