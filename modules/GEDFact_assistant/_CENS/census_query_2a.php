<?php
/**
 * Census Assistant Control module for phpGedView
 *
 * Census information about an individual
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
 * @subpackage Census Assistant
 * @version $Id$
 */
 
 global $TEXT_DIRECTION, $CensDate;
 
 	$CensDate="yes"; 
 
 	require WT_ROOT.'includes/functions/functions_print_lists.php';  //  *** NOTE THIS is necessary for the function below ***
	
// ***  LINKS tags --- This will be added later **** =========================================
/*
	echo "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
		echo help_link("edit_add_SHARED_NOTE");
		echo "Links: <br />";
	echo "</td><td class=\"optionbox wrap\">\n";
		// print_indi_list(fetch_linked_indi('N1', "NOTE", "1"));  //  *** NOTE THIS needs fixing to replace "N1" with a variable, or use another function ***
		// include ('modules/GEDFact_assistant/_CENS/census_query_1b.php');
		echo "*** Not implemented yet ***";
	echo "</td></tr>\n";
	echo "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
		echo help_link("edit_add_SHARED_NOTE");
		echo "Add Links: <br />";
	echo "</td><td class=\"optionbox wrap\">\n";
		// include ('modules/GEDFact_assistant/_CENS/census_query1.php');
		echo "*** Not implemented yet ***";
	echo "</td></tr>\n";
*/


// OLD CODE FROM HERE ON, NOT SURE WHETHER IT WILL BE NEEDED ================================
/*
	$linksAddLinks  =	 "";
	$linksAddLinks .=	 "<tr><td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
	// $linksAddLinks .=	 help_link("edit_add_SHARED_NOTE");
	$linksAddLinks .=	 "Currently Linked to: ";
	$linksAddLinks .=	 "</td>";
	$linksAddLinks .=	 "<td class=\"optionbox wrap\">\n";
	// $linksAddLinks .=	include('includes/functions/functions_print_lists.php');              //  *** NOTE THIS is necessary for the function below *** 
	// $linksAddLinks .=	print_indi_list(fetch_linked_indi('N1', "NOTE", "1"));                //  *** NOTE THIS needs fixing to replace "N1" with a variable, or use another function ***
	// $linksAddLinks .=	// include ('modules/GEDFact_assistant/_CENS/census_query_1b.php');
	$linksAddLinks .=	 "xxx</td>";
	$linksAddLinks .=	 "</tr>";
	
	$linksAddLinks .=	 "<tr>";
	$linksAddLinks .=	 "<td class=\"descriptionbox ".$TEXT_DIRECTION." wrap width25\">";
	// $linksAddLinks .=	help_link("edit_add_SHARED_NOTE");
	$linksAddLinks .=	 "Add Other Links: ";
	$linksAddLinks .= "</td><td class=\"optionbox wrap\">\n";
	// $linksAddLinks .=	// include ('modules/GEDFact_assistant/_CENS/census_query1.php');
	$linksAddLinks .= "</td></tr>";
	
	echo $linksAddLinks;
*/


// GEDFact\-assistant  ================================
/**
 * print a list of individuals
 *
 * REQUIRES('includes/functions/functions_print_lists.php'); 
 *
 * @param array $datalist contain individuals that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
 
/*
function print_indi_list($datalist, $legend="", $option="") {
	global $GEDCOM, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $SHOW_MARRIED_NAMES, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER, $SHOW_EST_LIST_DATES;

	if ($option=="MARR_PLAC") return;
	if (count($datalist)<1) return;
	$tiny = (count($datalist)<=500);
	$name_subtags = array("", "_AKA", "_HEB", "ROMN");
	if ($SHOW_MARRIED_NAMES) $name_subtags[] = "_MARNM";
	require_once 'js/sorttable.js.htm';
	require_once 'includes/classes/class_stats.php';
	$stats = new stats($GEDCOM);
	$max_age = $stats->LongestLifeAge()+1;
	//-- init chart data
	for ($age=0; $age<=$max_age; $age++) $deat_by_age[$age]="";
	for ($year=1550; $year<2030; $year+=10) $birt_by_decade[$year]="";
	for ($year=1550; $year<2030; $year+=10) $deat_by_decade[$year]="";
	
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	echo '<div id="'.$table_id.'-table" class="center">';
	echo "<table id=\"".$table_id."\" >";
	
	//-- table header
	echo "<thead><tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">&nbsp; INDI &nbsp;</th>";
	echo '<th class="list_label">'.i18n::translate('NAME').'</th>';
	echo "<th class=\"list_label\">&nbsp;".i18n::translate('Add')."&nbsp; </th>";
	echo "</tr></thead>\n";

	//-- table body
	echo "<tbody>";
	$hidden = 0;
	$n = 0;
	$d100y=new GedcomDate(date('Y')-100);  // 100 years ago
	$dateY = date("Y");
	$unique_indis=array(); // Don't double-count indis with multiple names.
	foreach($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$person=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$person = Person::getInstance($value);
		} else { // Array of search results
			$gid = $key;
			if (isset($value["gid"])) $gid = $value["gid"]; // from indilist
			if (isset($value[4])) $gid = $value[4]; // from indilist ALL
			$person = Person::getInstance($gid);
		}
		// @var $person Person 
		if (is_null($person)) continue;
		if ($person->getType() !== "INDI") continue;
		if (!$person->canDisplayName()) {
			$hidden++;
			continue;
		}
		$unique_indis[$person->getXref()]=true;
		//-- place filtering
		if ($option=="BIRT_PLAC" && strstr($person->getBirthPlace(), $filter)===false) continue;
		if ($option=="DEAT_PLAC" && strstr($person->getDeathPlace(), $filter)===false) continue;
		

		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">".++$n."</td>";
		
		//-- Gedcom ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">'.$person->getXref("_blank").'</td>';
			
		//-- Indi name(s)
		$tdclass = "list_value_wrap";
		if (!$person->isDead()) $tdclass .= " alive";
		if (!$person->getChildFamilyIds()) $tdclass .= " patriarch";
		echo "<td class=\"".$tdclass."\" align=\"".get_align($person->getListName())."\">";
		$names_html=array();
		list($surn, $givn)=explode(',', $person->getSortName());
		// If we're showing search results, then the highlighted name is not
		// necessarily the person's primary name.
		$primary=$person->getPrimaryName();
		$names=$person->getAllNames();
		foreach ($names as $num=>$name) {
			// Exclude duplicate names, which can occur when individuals have
			// multiple surnames, such as in Spain/Portugal
			$dupe_found=false;
			foreach ($names as $dupe_num=>$dupe_name) {
				if ($dupe_num>$num && $dupe_name['type']==$name['type'] && $dupe_name['full']==$name['full']) {
					// Take care not to skip the "primary" name
					if ($num==$primary) {
						$primary=$dupe_num;
					}
					$dupe_found=true;
					break;
				}
			}
			if ($dupe_found) {
				continue;
			}
			if ($title=$name['type']=='_MARNM') {
				$title='title="'.i18n::translate('_MARNM').'"';
			} else {
				$title='';
			}
			if ($num==$primary) {
				$class='list_item name2';
				$sex_image=$person->getSexImage();
				list($surn, $givn)=explode(',', $name['sort']);
			} else {
				$class='list_item';
				$sex_image='';
			}
			$names_html[]='<span '.$title.' class="'.$class.'">'.PrintReady($name['list']).'</span>'.$sex_image;
			}
		echo implode('<br/>', $names_html);

		//-- Birth date
		echo "<td class=\"list_value_wrap rela\">";
		echo "[x]";
		echo "</td";
		
		echo "</tr>\n";
	}
	echo "</tbody>";
	echo "</table>\n";
	echo "</div>";
	
}
*/
// ==========================================================


?>
