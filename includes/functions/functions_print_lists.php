<?php
/**
 * Functions for printing lists
 *
 * Various printing functions for printing lists
 * used on the indilist, famlist, find, and search pages.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_FUNCTIONS_PRINT_LISTS_PHP', '');

require_once WT_ROOT.'includes/classes/class_person.php';
require_once WT_ROOT.'includes/functions/functions_places.php';
require_once WT_ROOT.'includes/cssparser.inc.php';

/**
 * print a sortable table of individuals
 *
 * @param array $datalist contain individuals that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_indi_table($datalist, $legend="", $option="") {
	global $GEDCOM, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER, $SHOW_EST_LIST_DATES, $MAX_ALIVE_AGE;

	if ($option=="MARR_PLAC") return;
	if (count($datalist)<1) return;
	$tiny = (count($datalist)<=500);
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_stats.php';
	$stats = new stats($GEDCOM);

	// Bad data can cause "longest life" to be huge, blowing memory limits
	$max_age = min($MAX_ALIVE_AGE, $stats->LongestLifeAge())+1;

	//-- init chart data
	for ($age=0; $age<=$max_age; $age++) $deat_by_age[$age]="";
	for ($year=1550; $year<2030; $year+=10) $birt_by_decade[$year]="";
	for ($year=1550; $year<2030; $year+=10) $deat_by_decade[$year]="";
	//-- fieldset
	if ($option=="BIRT_PLAC" || $option=="DEAT_PLAC") {
		$filter=$legend;
		$legend=translate_fact(substr($option, 0, 4))." @ ".$legend;
	}
	if ($legend == "") $legend = i18n::translate('Individuals');
	$legend = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["small"]."\" alt=\"\" align=\"middle\" /> ".$legend;
	echo "<fieldset><legend>", $legend, "</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	echo '<div id="', $table_id, '-table" class="center">';
	//-- filter buttons
	echo "<button type=\"button\" class=\"SEX_M\" title=\"", i18n::translate('Show only males.'), "\" >";
	echo Person::sexImage('M', 'large'), "&nbsp;</button> ";
	echo "<button type=\"button\" class=\"SEX_F\" title=\"", i18n::translate('Show only females.'), "\" >";
	echo Person::sexImage('F', 'large'), "&nbsp;</button> ";
	echo "<button type=\"button\" class=\"SEX_U\" title=\"", i18n::translate('Show only persons of whom the gender is not known.'), "\" >";
	echo Person::sexImage('U', 'large'), "&nbsp;</button> ";
	echo " <input type=\"text\" size=\"4\" id=\"aliveyear\" value=\"", date('Y'), "\" /> ";
	echo "<button type=\"button\" class=\"alive_in_year\" title=\"", i18n::translate('Show persons alive in the indicated year.'), "\" >";
	echo i18n::translate('Alive in Year'), "</button> ";
	echo "<button type=\"button\" class=\"DEAT_N\" title=\"", i18n::translate('Show people who are alive or couples where both partners are alive.'), "\" >";
	echo i18n::translate('Alive '), "</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y\" title=\"", i18n::translate('Show people who are dead or couples where both partners are deceased.'), "\" >";
	echo i18n::translate('Dead '), "</button> ";
	echo "<button type=\"button\" class=\"TREE_R\" title=\"", i18n::translate('Show «roots» couples or individuals.  These people may also be called «patriarchs».  They are individuals who have no parents recorded in the database.'), "\" >";
	echo i18n::translate('Roots'), "</button> ";
	echo "<button type=\"button\" class=\"TREE_L\" title=\"", i18n::translate('Show «leaves» couples or individuals.  These are individuals who are alive but have no children recorded in the database.'), "\" >";
	echo i18n::translate('Leaves'), "</button> ";
	echo "<br />";
	echo "<button type=\"button\" class=\"BIRT_YES\" title=\"", i18n::translate('Show persons born more than 100 years ago.'), "\" >";
	echo translate_fact('BIRT'), "&gt;100</button> ";
	echo "<button type=\"button\" class=\"BIRT_Y100\" title=\"", i18n::translate('Show persons born within the last 100 years.'), "\" >";
	echo translate_fact('BIRT'), "&lt;=100</button> ";
	echo "<button type=\"button\" class=\"DEAT_YES\" title=\"", i18n::translate('Show people who died more than 100 years ago.'), "\" >";
	echo translate_fact('DEAT'), "&gt;100</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y100\" title=\"", i18n::translate('Show people who died within the last 100 years.'), "\" >";
	echo translate_fact('DEAT'), "&lt;=100</button> ";
	echo "<button type=\"button\" class=\"reset\" title=\"", i18n::translate('Reset to the list defaults.'), "\" >";
	echo i18n::translate('Reset'), "</button> ";
	//-- table header
	echo "<table id=\"", $table_id, "\" class=\"sortable list_table\">";
	echo "<thead><tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">INDI</th>";
	echo '<th class="list_label"><a href="javascript:;" onclick="sortByOtherCol(this, 2)">', translate_fact('NAME'), '</a></th>';
	echo "<th class=\"list_label\" style=\"display:none\">GIVN</th>";
	echo "<th class=\"list_label\" style=\"display:none\">SURN</th>";
	if ($option=="sosa") echo "<th class=\"list_label\">Sosa</th>";
	echo "<th class=\"list_label\">", translate_fact('BIRT'), "</th>";
	if ($tiny) echo "<td class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"", i18n::translate('Anniversary'), "\" title=\"", i18n::translate('Anniversary'), "\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">", translate_fact('PLAC'), "</th>";
	if ($tiny) echo "<th class=\"list_label\"><img src=\"./images/children.gif\" alt=\"", i18n::translate('Children'), "\" title=\"", i18n::translate('Children'), "\" border=\"0\" /></th>";
	echo "<th class=\"list_label\">", translate_fact('DEAT'), "</th>";
	if ($tiny) echo "<td class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"", i18n::translate('Anniversary'), "\" title=\"", i18n::translate('Anniversary'), "\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">", translate_fact('AGE'), "</th>";
	echo "<th class=\"list_label\">", translate_fact('PLAC'), "</th>";
	if ($tiny && $SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">", translate_fact('CHAN'), "</th>";
	echo "<th class=\"list_label\" style=\"display:none\">SEX</th>";
	echo "<th class=\"list_label\" style=\"display:none\">BIRT</th>";
	echo "<th class=\"list_label\" style=\"display:none\">DEAT</th>";
	echo "<th class=\"list_label\" style=\"display:none\">TREE</th>";
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
		/* @var $person Person */
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
		echo "<td class=\"list_value_wrap rela list_item\">", ++$n, "</td>";
		//-- Gedcom ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">', $person->getXrefLink("_blank"), '</td>';
		//-- Indi name(s)
		$tdclass = "list_value_wrap";
		if (!$person->isDead()) $tdclass .= " alive";
		if (!$person->getChildFamilyIds()) $tdclass .= " patriarch";
		echo "<td class=\"", $tdclass, "\" align=\"", get_align($person->getListName()), "\">";
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
				$title='title="'.translate_fact('_MARNM').'"';
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
			echo '<a ', $title, ' href="', encode_url($person->getLinkUrl()), '" class="', $class, '">', PrintReady($name['list']), '</a>', $sex_image, "<br/>";
		}
		// Indi parents
		echo $person->getPrimaryParentsNames("parents_$table_id details1", 'none');
		echo '</td>';
		//-- GIVN/SURN
		echo '<td style="display:none">', $givn, ',', $surn, '</td>';
		echo '<td style="display:none">', $surn, ',', $givn, '</td>';
		//-- SOSA
		if ($option=='sosa') {
			echo
				'<td class="list_value_wrap"><a href="',
				encode_url('relationship.php?pid1='.$datalist[1].'&pid2='.$person->getXref()),
				'" title="', i18n::translate('Relationship Chart'), '"',
				' name="', $key, '" class="list_item name2">', $key, '</a></td>';
		}
		//-- Birth date
		echo '<td class="list_value_wrap">';
		if ($birth_dates=$person->getAllBirthDates()) {
			foreach ($birth_dates as $num=>$birth_date) {
				if ($num) {
					echo '<div>', $birth_date->Display(!$SEARCH_SPIDER), '</div>';
				} else {
					echo '<div>', str_replace('<a', '<a name="'.$birth_date->MinJD().'"', $birth_date->Display(!$SEARCH_SPIDER)), '</div>';
				}
			}
			if ($birth_dates[0]->gregorianYear()>=1550 && $birth_dates[0]->gregorianYear()<2030) {
				$birt_by_decade[floor($birth_dates[0]->gregorianYear()/10)*10] .= $person->getSex();
			}
		} else {
			$birth_date=$person->getEstimatedBirthDate();
			$birth_jd=$birth_date->JD();
			if ($SHOW_EST_LIST_DATES) {
				echo '<div>', str_replace('<a', '<a name="'.$birth_jd.'"', $birth_date->Display(!$SEARCH_SPIDER)), '</div>';
			} else {
				echo '<span class="date"><a name="', $birth_jd, '"/>&nbsp;</span>'; // span needed for alive-in-year filter
			}
			$birth_dates[0]=new GedcomDate('');
		}
		echo '</td>';
		//-- Birth anniversary
		if ($tiny) {
			echo '<td class="list_value_wrap rela">';
			$bage =GedcomDate::GetAgeYears($birth_dates[0]);
			if (empty($bage)) {
				echo "&nbsp;";
			} else {
				echo '<span class="age">', $bage, '</span>';
			}
			echo '</td>';
		}
		//-- Birth place
		echo '<td class="list_value_wrap">';
		if ($birth_places=$person->getAllBirthPlaces()) {
			foreach ($birth_places as $birth_place) {
				if ($SEARCH_SPIDER) {
					echo get_place_short($birth_place), ' ';
				} else {
					echo '<div align="', get_align($birth_place), '">';
					echo '<a href="', encode_url(get_place_url($birth_place)), '" class="list_item" title="', $birth_place, '">';
					echo PrintReady(get_place_short($birth_place)), '</a>';
					echo '</div>';
				}
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Number of children
		if ($tiny) {
			echo "<td class=\"list_value_wrap\">";
			if (showFactDetails('NCHI', $person->getXref(), 'INDI')) {
				if($SEARCH_SPIDER) {
					echo $person->getNumberOfChildren();
				} else {
					echo "<a href=\"", encode_url($person->getLinkUrl()), "\" class=\"list_item\" name=\"", $person->getNumberOfChildren(), "\">", $person->getNumberOfChildren(), "</a>";
				}
			} else {
				echo '&nbsp;';
			}
			echo "</td>";
		}
		//-- Death date
		echo "<td class=\"list_value_wrap\">";
		if ($death_dates=$person->getAllDeathDates()) {
			foreach ($death_dates as $num=>$death_date) {
				if ($num) {
					echo '<div>', $death_date->Display(!$SEARCH_SPIDER), '</div>';
				} else if ($death_date->MinJD()!=0) {
					echo '<div>', str_replace('<a', '<a name="'.$death_date->MinJD().'"', $death_date->Display(!$SEARCH_SPIDER)), '</div>';
				}
			}
			if ($death_dates[0]->gregorianYear()>=1550 && $death_dates[0]->gregorianYear()<2030) {
				$deat_by_decade[floor($death_dates[0]->gregorianYear()/10)*10] .= $person->getSex();
			}
		} else {
			$death_date=$person->getEstimatedDeathDate();
			$death_jd=$death_date->JD();
			if ($SHOW_EST_LIST_DATES) {
				echo '<div>', str_replace('<a', '<a name="'.$death_jd.'"', $death_date->Display(!$SEARCH_SPIDER)), '</div>';
			} else if ($person->isDead()) {
				echo '<div>', i18n::translate('Yes'), '<a name="9d', $death_jd, '"></a></div>';
			} else {
				echo '<span class="date"><a name="', $death_jd, '">&nbsp;</span>'; // span needed for alive-in-year filter
			}
			$death_dates[0]=new GedcomDate('');
		}
		echo "</td>";
		//-- Death anniversary
		if ($tiny) {
			print "<td class=\"list_value_wrap rela\">";
			if ($death_dates[0]->isOK())
				echo "<span class=\"age\">", GedcomDate::GetAgeYears($death_dates[0]), "</span>";
			else
				echo "&nbsp;";
			print '</td>';
		}
		//-- Age at death
		print "<td class=\"list_value_wrap\">";
		if ($birth_dates[0]->isOK() && $death_dates[0]->isOK()) {
			$age = GedcomDate::GetAgeYears($birth_dates[0], $death_dates[0]);
			$age_jd = $death_dates[0]->MinJD()-$birth_dates[0]->MinJD();
			echo '<a name="', $age_jd, '" class="list_item age">', $age, '</a>';
			$deat_by_age[max(0, min($max_age, $age))] .= $person->getSex();
		} else {
			echo '<a name="-1">&nbsp;</a>';
		}
		echo "</td>";
		//-- Death place
		echo '<td class="list_value_wrap">';
		if ($death_places=$person->getAllDeathPlaces()) {
			foreach ($death_places as $death_place) {
				if ($SEARCH_SPIDER) {
					echo get_place_short($death_place), ' ';
				} else {
					echo '<div align="', get_align($death_place), '">';
					echo '<a href="', encode_url(get_place_url($death_place)), '" class="list_item" title="', $death_place, '">';
					echo PrintReady(get_place_short($death_place)), '</a>';
					echo '</div>';
				}
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Last change
		if ($tiny && $SHOW_LAST_CHANGE) {
			echo "<td class=\"list_value_wrap rela\">", $person->LastChangeTimestamp(empty($SEARCH_SPIDER)), "</td>";
		}
		//-- Sorting by gender
		echo "<td style=\"display:none\">";
		echo $person->getSex();
		echo "</td>";
		//-- Filtering by birth date
		echo "<td style=\"display:none\">";
		if (!$person->canDisplayDetails() || GedcomDate::Compare($birth_dates[0], $d100y)>0) {
			echo "Y100";
		} else {
			echo "YES";
		}
		echo "</td>";
		//-- Filtering by death date
		echo "<td style=\"display:none\">";
		if ($person->isDead()) {
			if (GedcomDate::Compare($death_dates[0], $d100y)>0) {
				echo "Y100";
			} else {
				echo "YES";
			}
		} else {
			echo "N";
		}
		echo "</td>";
		//-- Roots or Leaves ?
		echo "<td style=\"display:none\">";
		if (!$person->getChildFamilyIds()) {
			echo "R"; // roots
		} elseif (!$person->isDead() && $person->getNumberOfChildren()<1) {
			echo "L"; // leaves
		}
		echo "</td>";
		echo "</tr>\n";
	}
	echo "</tbody>";
	//-- table footer
	echo "<tfoot><tr class=\"sortbottom\">";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>"; // INDI:ID
	echo "<td class=\"list_label\">"; // NAME
	if (count($unique_indis)>1) {
		echo '<a href="javascript:;" onclick="sortByOtherCol(this, 1)"><img src="images/topdown.gif" alt="" border="0" /> ', translate_fact('GIVN'), '</a><br />';
	}
	echo "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', 'parents_$table_id');\" /><label for=\"cb_parents_$table_id\">", i18n::translate('Show parents'), "</label><br />";
	echo i18n::translate('Total individuals'), ' : ', count($unique_indis);
	if ($n!=count($unique_indis)) {
		echo '<br/>', i18n::translate('Total Names'), ' : ', $n;
	}
	if ($hidden) echo "<br /><span class=\"warning\">", i18n::translate('Hidden'), " : ", $hidden, "</span>";
	echo "</td>";
	echo "<td style=\"display:none\">GIVN</td>";
	echo "<td style=\"display:none\">SURN</td>";
	if ($option=="sosa") echo "<td></td>"; // SOSA
	echo "<td></td>"; // BIRT:DATE
	if ($tiny) echo "<td></td>"; // BIRT:Reminder
	echo "<td></td>"; // BIRT:PLAC
	if ($tiny) echo "<td></td>"; // Children
	echo "<td class=\"list_label\" colspan=\"3\">";
	echo "<input id=\"charts_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', '$table_id-charts');\" /><label for=\"charts_$table_id\">", i18n::translate('Show statistics charts'), "</label></td>"; //DEAT:DATE, DEAT:Reminder, DEAT:AGE
	echo "<td></td>"; // DEAT:PLAC
	if ($tiny && $SHOW_LAST_CHANGE) echo "<td></td>"; // CHAN
	echo "<td style=\"display:none\">SEX</td>";
	echo "<td style=\"display:none\">BIRT</td>";
	echo "<td style=\"display:none\">DEAT</td>";
	echo "<td style=\"display:none\">TREE</td>";
	echo "</tr>";
	echo "</tfoot>";
	echo "</table>\n";
	echo "</div>";
	//-- charts
	echo "<div class=\"", $table_id, "-charts\" style=\"display:none\">";
	echo "<table class=\"list_table center\">";
	echo "<tr><td class=\"list_value_wrap\">";
	print_chart_by_decade($birt_by_decade, i18n::translate('Decade of birth'));
	echo "</td><td class=\"list_value_wrap\">";
	print_chart_by_decade($deat_by_decade, i18n::translate('Decade of death'));
	echo "</td></tr><tr><td colspan=\"2\" class=\"list_value_wrap\">";
	print_chart_by_age($deat_by_age, i18n::translate('Age related to death year'));
	echo "</td></tr></table>";
	echo "</div>";
	echo "</fieldset>\n";
}

/**
 * print a sortable table of families
 *
 * @param array $datalist contain families that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_fam_table($datalist, $legend="", $option="") {
	global $GEDCOM, $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER;

	if ($option=="BIRT_PLAC" || $option=="DEAT_PLAC") return;
	if (count($datalist)<1) return;
	$tiny = (count($datalist)<=500);
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_family.php';
	require_once WT_ROOT.'includes/classes/class_stats.php';
	$stats = new stats($GEDCOM);
	$max_age = max($stats->oldestMarriageMaleAge(), $stats->oldestMarriageFemaleAge())+1;
	//-- init chart data
	for ($age=0; $age<=$max_age; $age++) $marr_by_age[$age]="";
	for ($year=1550; $year<2030; $year+=10) $birt_by_decade[$year]="";
	for ($year=1550; $year<2030; $year+=10) $marr_by_decade[$year]="";
	//-- fieldset
	if ($option=="MARR_PLAC") {
		$filter=$legend;
		$legend=translate_fact('MARR')." @ ".$legend;
	}
	if ($legend == "") $legend = i18n::translate('Families');
	$legend = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["sfamily"]["small"]."\" alt=\"\" align=\"middle\" /> ".$legend;
	echo "<fieldset><legend>", $legend, "</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	echo '<div id="', $table_id, '-table" class="center">';
	//-- filter buttons
	echo "<button type=\"button\" class=\"DEAT_N\" title=\"", i18n::translate('Show people who are alive or couples where both partners are alive.'), "\" >";
	echo i18n::translate('Both alive '), "</button> ";
	echo "<button type=\"button\" class=\"DEAT_W\" title=\"", i18n::translate('Show couples where only the female partner is deceased.'), "\" >";
	echo i18n::translate('Widower'), "</button> ";
	echo "<button type=\"button\" class=\"DEAT_H\" title=\"", i18n::translate('Show couples where only the male partner is deceased.'), "\" >";
	echo i18n::translate('Widow'), "</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y\" title=\"", i18n::translate('Show people who are dead or couples where both partners are deceased.'), "\" >";
	echo i18n::translate('Both dead '), "</button> ";
	echo "<button type=\"button\" class=\"TREE_R\" title=\"", i18n::translate('Show «roots» couples or individuals.  These people may also be called «patriarchs».  They are individuals who have no parents recorded in the database.'), "\" >";
	echo i18n::translate('Roots'), "</button> ";
	echo "<button type=\"button\" class=\"TREE_L\" title=\"", i18n::translate('Show «leaves» couples or individuals.  These are individuals who are alive but have no children recorded in the database.'), "\" >";
	echo i18n::translate('Leaves'), "</button> ";
	echo "<br />";
	echo "<button type=\"button\" class=\"MARR_U\" title=\"", i18n::translate('Show couples with an unknown marriage date.'), "\" >";
	echo translate_fact('MARR'), " ?</button> ";
	echo "<button type=\"button\" class=\"MARR_YES\" title=\"", i18n::translate('Show couples who married more than 100 years ago.'), "\" >";
	echo translate_fact('MARR'), "&gt;100</button> ";
	echo "<button type=\"button\" class=\"MARR_Y100\" title=\"", i18n::translate('Show couples who married within the last 100 years.'), "\" >";
	echo translate_fact('MARR'), "&lt;=100</button> ";
	echo "<button type=\"button\" class=\"MARR_DIV\" title=\"", i18n::translate('Show divorced couples.'), "\" >";
	echo translate_fact('DIV'), "</button> ";
	echo "<button type=\"button\" class=\"reset\" title=\"", i18n::translate('Reset to the list defaults.'), "\" >";
	echo i18n::translate('Reset'), "</button> ";
	//-- table header
	echo "<table id=\"", $table_id, "\" class=\"sortable list_table center\">";
	echo "<thead><tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">FAM</th>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">INDI</th>";
	echo "<th class=\"list_label\">", translate_fact('NAME'), "</th>";
	echo "<th style=\"display:none\">HUSB:GIVN</th>";
	echo "<th class=\"list_label\">", translate_fact('AGE'), "</th>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">INDI</th>";
	echo "<th class=\"list_label\">", translate_fact('NAME'), "</th>";
	echo "<th style=\"display:none\">WIFE:GIVN</th>";
	echo "<th class=\"list_label\">", translate_fact('AGE'), "</th>";
	echo "<th class=\"list_label\">", translate_fact('MARR'), "</th>";
	if ($tiny) echo "<td class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"", i18n::translate('Anniversary'), "\" title=\"", i18n::translate('Anniversary'), "\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">", translate_fact('PLAC'), "</th>";
	if ($tiny) echo "<th class=\"list_label\"><img src=\"./images/children.gif\" alt=\"", i18n::translate('Children'), "\" title=\"", i18n::translate('Children'), "\" border=\"0\" /></th>";
	if ($tiny && $SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">", translate_fact('CHAN'), "</th>";
	echo "<th style=\"display:none\">MARR</th>";
	echo "<th style=\"display:none\">DEAT</th>";
	echo "<th style=\"display:none\">TREE</th>";
	echo "</tr></thead>\n";
	//-- table body
	echo "<tbody>\n";
	$hidden = 0;
	$num = 0;
	$d100y=new GedcomDate(date('Y')-100);  // 100 years ago
	foreach($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$family=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$family=Family::getInstance($value);
		} else { // Array of search results
			$gid = "";
			if (isset($value["gid"])) $gid = $value["gid"];
			if (isset($value["gedcom"])) $family = new Family($value["gedcom"]);
			else $family = Family::getInstance($gid);
		}
		if (is_null($family)) continue;
		if ($family->getType() !== "FAM") continue;
		//-- Retrieve husband and wife
		$husb = $family->getHusband();
		if (is_null($husb)) $husb = new Person('');
		$wife = $family->getWife();
		if (is_null($wife)) $wife = new Person('');
		if (!$husb->canDisplayName() || !$wife->canDisplayName()) {
			$hidden++;
			continue;
		}
		//-- place filtering
		if ($option=="MARR_PLAC" && strstr($family->getMarriagePlace(), $filter)===false) continue;
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">", ++$num, "</td>";
		//-- Family ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">', $family->getXrefLink("_blank"), '</td>';
		//-- Husband ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">', $husb->getXrefLink("_blank"), '</td>';
		//-- Husband name(s)
		list($husb_name, $wife_name)=explode(' + ', $family->getSortName());
		$names=$husb->getAllNames();
		// The husband's primary/secondary name might not be the family's primary name
		foreach ($names as $n=>$name) {
			if ($name['sort']==$husb_name) {
				$husb->setPrimaryName($n);
				break;
			}
		}
		$n1=$husb->getPrimaryName();
		$n2=$husb->getSecondaryName();
		$tdclass = "list_value_wrap";
		if (!$husb->isDead()) $tdclass .= " alive";
		if (!$husb->getChildFamilyIds()) $tdclass .= " patriarch";
		echo "<td class=\"", $tdclass, "\" align=\"", get_align($names[$n1]['list']), "\">";
		echo "<a href=\"", encode_url($family->getLinkUrl()), "\" class=\"list_item name2\" dir=\"", $TEXT_DIRECTION, "\">", PrintReady($names[$n1]['list']), "</a>";
		if ($tiny) echo $husb->getSexImage();
		if ($n1!=$n2) {
			echo "<br /><a href=\"", encode_url($family->getLinkUrl()), "\" class=\"list_item\">", PrintReady($names[$n2]['list']), "</a>";
		}
		// Husband parents
		echo $husb->getPrimaryParentsNames("parents_$table_id details1", "none");
		echo "</td>";
		//-- Husb GIVN
		list($surn, $givn)=explode(',', $husb->getSortName());
		echo '<td style="display:none">', $givn, '</td>';
		$mdate=$family->getMarriageDate();
		//-- Husband age
		echo "<td class=\"list_value_wrap\">";
		$hdate=$husb->getBirthDate();
		if ($hdate->isOK()) {
			if ($hdate->gregorianYear()>=1550 && $hdate->gregorianYear()<2030) {
				$birt_by_decade[floor($hdate->gregorianYear()/10)*10] .= $husb->getSex();
			}
			if ($mdate->isOK()) {
				$hage=GedcomDate::GetAgeYears($hdate, $mdate);
				$hage_jd = $mdate->MinJD()-$hdate->MinJD();
				echo '<a name="', $hage_jd, '" class="list_item age">', $hage, '</a>';
				$marr_by_age[max(0, min($max_age, $hage))] .= $husb->getSex();
			} else {
				echo '&nbsp;';
			}
		} else {
			echo '&nbsp;';
		}
		echo "</td>";
		//-- Wife ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">', $wife->getXrefLink("_blank"), '</td>';
		//-- Wife name(s)
		$names=$wife->getAllNames();
		// The husband's primary/secondary name might not be the family's primary name
		foreach ($names as $n=>$name) {
			if ($name['sort']==$wife_name) {
				$wife->setPrimaryName($n);
				break;
			}
		}
		$n1=$wife->getPrimaryName();
		$n2=$wife->getSecondaryName();
		$tdclass = "list_value_wrap";
		if (!$wife->isDead()) $tdclass .= " alive";
		if (!$wife->getChildFamilyIds()) $tdclass .= " patriarch";
		echo "<td class=\"", $tdclass, "\" align=\"", get_align($names[$n1]['list']), "\">";
		echo "<a href=\"", encode_url($family->getLinkUrl()), "\" class=\"list_item name2\" dir=\"", $TEXT_DIRECTION, "\">", PrintReady($names[$n1]['list']), "</a>";
		if ($tiny) echo $wife->getSexImage();
		if ($n1!=$n2) {
			echo "<br /><a href=\"", encode_url($family->getLinkUrl()), "\" class=\"list_item\">", PrintReady($names[$n2]['list']), "</a>";
		}
		// Wife parents
		echo $wife->getPrimaryParentsNames("parents_$table_id details1", "none");
		echo "</td>";
		//-- Wife GIVN
		list($surn, $givn)=explode(',', $wife->getSortName());
		echo '<td style="display:none">', $givn, '</td>';
		$mdate=$family->getMarriageDate();
		//-- Wife age
		echo "<td class=\"list_value_wrap\">";
		$wdate=$wife->getBirthDate();
		if ($wdate->isOK()) {
			if ($wdate->gregorianYear()>=1550 && $wdate->gregorianYear()<2030) {
				$birt_by_decade[floor($wdate->gregorianYear()/10)*10] .= $wife->getSex();
			}
			if ($mdate->isOK()) {
				$wage=GedcomDate::GetAgeYears($wdate, $mdate);
				$wage_jd = $mdate->MinJD()-$wdate->MinJD();
				echo '<a name="', $wage_jd, '" class="list_item age">', $wage, '</a>';
				$marr_by_age[max(0, min($max_age, $wage))] .= $wife->getSex();
			} else {
				print "&nbsp;";
			}
		} else {
			print "&nbsp;";
		}
		echo "</td>";
		//-- Marriage date
		echo "<td class=\"list_value_wrap\">";
		if ($marriage_dates=$family->getAllMarriageDates()) {
			foreach ($marriage_dates as $n=>$marriage_date) {
				if ($n) {
					echo '<div>', $marriage_date->Display(!$SEARCH_SPIDER), '</div>';
				} else if ($marriage_date->MinJD()!=0) {
					echo '<div>', str_replace('<a', '<a name="'.$marriage_date->MinJD().'"', $marriage_date->Display(!$SEARCH_SPIDER)), '</div>';
				}
			}
			if ($marriage_dates[0]->gregorianYear()>=1550 && $marriage_dates[0]->gregorianYear()<2030) {
				$marr_by_decade[floor($marriage_dates[0]->gregorianYear()/10)*10] .= $husb->getSex().$wife->getSex();
			}
		} else if (get_sub_record(1, "1 _NMR", $family->getGedcomRecord())) {
			$hus = $family->getHusband();
			$wif = $family->getWife();
			if (empty($wif) && !empty($hus)) echo translate_fact('_NMR', $hus);
			else if (empty($hus) && !empty($wif)) echo translate_fact('_NMR', $wif);
			else echo translate_fact('_NMR');
		} else if (get_sub_record(1, "1 _NMAR", $family->getGedcomRecord())) {
			$hus = $family->getHusband();
			$wif = $family->getWife();
			if (empty($wif) && !empty($hus)) echo translate_fact('_NMAR', $hus);
			else if (empty($hus) && !empty($wif)) echo translate_fact('_NMAR', $wif);
			else echo translate_fact('_NMAR');
		} else {
			$factdetail = explode(' ', trim($family->getMarriageRecord()));
			if (isset($factdetail)) {
				if (count($factdetail) >= 3) {
					if (strtoupper($factdetail[2]) != "N")
						echo '<div>', i18n::translate('Yes'), '<a name="9999998"></a></div>';
					else
						echo '<div>', i18n::translate('No'), '<a name="9999999"></a></div>';
				}
				else echo '&nbsp;';
			}
		}
		echo "</td>";
		//-- Marriage anniversary
		if ($tiny) {
			echo "<td class=\"list_value_wrap rela\">";
			$mage=GedcomDate::GetAgeYears($mdate);
			if (empty($mage)) echo "&nbsp;";
			else echo "<span class=\"age\">", $mage, "</span>";
			echo "</td>";
		}
		//-- Marriage place
		echo '<td class="list_value_wrap">';
		if ($marriage_places=$family->getAllMarriagePlaces()) {
			foreach ($marriage_places as $marriage_place) {
				if ($SEARCH_SPIDER) {
					echo get_place_short($marriage_place), ' ';
				} else {
					echo '<div align="', get_align($marriage_place), '">';
					echo '<a href="', encode_url(get_place_url($marriage_place)), '" class="list_item" title="', $marriage_place, '">';
					echo PrintReady(get_place_short($marriage_place)), '</a>';
					echo '</div>';
				}
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Number of children
		if ($tiny) {
			echo "<td class=\"list_value_wrap\">";
			if (showFactDetails('NCHI', $family->getXref(), 'FAM')) {
				if($SEARCH_SPIDER) {
					echo $family->getNumberOfChildren();
				} else {
					echo "<a href=\"", encode_url($family->getLinkUrl()), "\" class=\"list_item\" name=\"", $family->getNumberOfChildren(), "\">", $family->getNumberOfChildren(), "</a>";
				}
			} else {
				echo '&nbsp;';
			}
			echo "</td>";
		}
		//-- Last change
		if ($tiny && $SHOW_LAST_CHANGE)
			echo '<td class="list_value_wrap rela">', $family->LastChangeTimestamp(empty($SEARCH_SPIDER)), '</td>';
		//-- Sorting by marriage date
		echo "<td style=\"display:none\">";
		if (!$family->canDisplayDetails() || !$mdate->isOK()) {
			echo "U";
		} else {
			if (GedcomDate::Compare($mdate, $d100y)>0) {
				echo "Y100";
			} else {
				echo "YES";
			}
		}
		if ($family->isDivorced())
			echo " DIV";
		echo "</td>";
		//-- Sorting alive/dead
		echo "<td style=\"display:none\">";
		if ($husb->isDead() && $wife->isDead()) echo "Y";
		if ($husb->isDead() && !$wife->isDead()) {
			if ($wife->getSex()=="F") echo "H";
			if ($wife->getSex()=="M") echo "W"; // male partners
		}
		if (!$husb->isDead() && $wife->isDead()) {
			if ($husb->getSex()=="M") echo "W";
			if ($husb->getSex()=="F") echo "H"; // female partners
		}
		if (!$husb->isDead() && !$wife->isDead()) echo "N";
		echo "</td>";
		//-- Roots or Leaves
		echo "<td style=\"display:none\">";
		if (!$husb->getChildFamilyIds() && !$wife->getChildFamilyIds()) {
			echo "R"; // roots
		} elseif (!$husb->isDead() && !$wife->isDead() && $family->getNumberOfChildren()<1) {
			echo "L"; // leaves
		}
		echo "</td>";
		echo "</tr>\n";
	}
	echo "</tbody>";
	//-- table footer
	echo "<tfoot><tr class=\"sortbottom\">";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>"; // FAM:ID
	if ($SHOW_ID_NUMBERS) echo "<td></td>"; // HUSB:ID
	echo "<td class=\"list_label\">"; // HUSB:NAME
	if ($num>1) {
		echo '<a href="javascript:;" onclick="sortByOtherCol(this, 1)"><img src="images/topdown.gif" alt="" border="0" /> ', translate_fact('GIVN'), '</a><br />';
	}
	echo "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', 'parents_$table_id');\" /><label for=\"cb_parents_$table_id\">", i18n::translate('Show parents'), "</label><br />";
	echo i18n::translate('Total families'), " : ", $num;
	if ($hidden) echo "<br /><span class=\"warning\">", i18n::translate('Hidden'), " : ", $hidden, "</span>";
	echo "</td>";
	echo "<td style=\"display:none\">HUSB:GIVN</td>";
	echo "<td></td>"; // HUSB:AGE
	if ($SHOW_ID_NUMBERS) echo "<td></td>"; // WIFE:ID
	echo "<td class=\"list_label\" style=\"vertical-align: top;\">"; // WIFE:NAME
	echo '<a href="javascript:;" onclick="sortByOtherCol(this, 1)"><img src="images/topdown.gif" alt="" border="0" /> ', translate_fact('GIVN'), '</a><br />';
	echo "</td>";
	echo "<td style=\"display:none\">WIFE:GIVN</td>";
	echo "<td></td>"; // WIFE:AGE
	echo "<td class=\"list_label\" colspan=\"3\">";
	echo "<input id=\"charts_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', '$table_id-charts');\" /><label for=\"charts_$table_id\">", i18n::translate('Show statistics charts'), "</label></td>"; // MARR:DATE, MARR:Reminder, MARR:PLAC
	if ($tiny) echo "<td></td>"; // FAM:ChildrenCount
	if ($tiny && $SHOW_LAST_CHANGE) echo "<td></td>"; // FAM:CHAN
	echo "<td style=\"display:none\">MARR</td>";
	echo "<td style=\"display:none\">DEAT</td>";
	echo "<td style=\"display:none\">TREE</td>";
	echo "</tr></tfoot>";
	echo "</table>\n";
	echo "</div>";
	//-- charts
	echo "<div class=\"", $table_id, "-charts\" style=\"display:none\">";
	echo "<table class=\"list_table center\">";
	echo "<tr><td class=\"list_value_wrap\">";
	print_chart_by_decade($birt_by_decade, i18n::translate('Decade of birth'));
	echo "</td><td class=\"list_value_wrap\">";
	print_chart_by_decade($marr_by_decade, i18n::translate('Decade of marriage'));
	echo "</td></tr><tr><td colspan=\"2\" class=\"list_value_wrap\">";
	print_chart_by_age($marr_by_age, i18n::translate('Age in year of marriage'));
	echo "</td></tr></table>";
	echo "</div>";
	echo "</fieldset>\n";
}

/**
 * print a sortable table of sources
 *
 * @param array $datalist contain sources that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_sour_table($datalist, $legend=null) {
	global $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES;

	if (count($datalist)<1) {
		return;
	}
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_source.php';

	echo '<fieldset><legend><img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES['source']['small'], '" align="middle" alt="" /> ';
	if ($legend) {
		echo $legend;
	} else {
		echo i18n::translate('Sources');
	}
	echo '</legend>';
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo '<table id="', $table_id, '" class="sortable list_table center"><tr><td></td>';
	if ($SHOW_ID_NUMBERS) {
		echo '<th class="list_label rela">SOUR</th>';
	}
	echo '<th class="list_label">', translate_fact('TITL'), '</th>';
	echo '<td class="list_label t2" style="display:none;">', translate_fact('TITL'), ' 2</td>';
	echo '<th class="list_label">', translate_fact('AUTH'), '</th>';
	echo '<th class="list_label">', i18n::translate('Individuals'), '</th>';
	echo '<th class="list_label">', i18n::translate('Families'), '</th>';
	echo '<th class="list_label">', i18n::translate('Media'), '</th>';
	echo '<th class="list_label">', i18n::translate('Shared Notes'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th class="list_label rela">', translate_fact('CHAN'), '</th>';
	}
	echo '</tr>';
	//-- table body
	$t2=false;
	$n=0;
	foreach ($datalist as $key=>$value) {
		if (is_object($value)) { // Array of objects
			$source=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$source=Source::getInstance($key); // from placelist
			if (is_null($source)) {
				$source=Source::getInstance($value);
			}
			unset($value);
		} else { // Array of search results
			$gid='';
			if (isset($value['gid'])) {
				$gid=$value['gid'];
			}
			if (isset($value['gedcom'])) {
				$source=new Source($value['gedcom']);
			} else {
				$source=Source::getInstance($gid);
			}
		}
		if (!$source || !$source->canDisplayDetails()) {
			continue;
		}
		$link_url=encode_url($source->getLinkUrl());
		//-- Counter
		echo '<tr><td class="list_value_wrap rela list_item">', ++$n, '</td>';
		//-- Source ID
		if ($SHOW_ID_NUMBERS) {
			echo '<td class="list_value_wrap rela">', $source->getXrefLink(), '</td>';
		}
		//-- Source name(s)
		$tmp=$source->getFullName();
		echo '<td class="list_value_wrap" align="', get_align($tmp), '"><a href="', $link_url, '" class="list_item name2">', PrintReady(htmlspecialchars($tmp)), '</a></td>';
		// alternate title in a new column
		$tmp=$source->getAddName();
		if ($tmp) {
			echo '<td class="list_value_wrap t2" style="display:none;" align="', get_align($tmp), '"><a href="', $link_url, '" class="list_item">', PrintReady(htmlspecialchars($tmp)), '</a></td>';
			$t2=true;
		} else {
			echo '<td class="list_value_wrap t2" style="display:none;">&nbsp;</td>';
		}
		//-- Author
		$tmp=$source->getAuth();
		if ($tmp) {
			echo '<td class="list_value_wrap" align="', get_align($tmp), '"><a href="', $link_url, '" class="list_item">', PrintReady(htmlspecialchars($tmp)), '</a></td>';
		} else {
			echo '<td class="list_value_wrap">&nbsp;</td>';
		}
		//-- Linked INDIs
		$tmp=$source->countLinkedIndividuals();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked FAMs
		$tmp=$source->countLinkedfamilies();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked OBJEcts
		$tmp=$source->countLinkedMedia();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked NOTEs
		$tmp=$source->countLinkedNotes();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			print '<td class="list_value_wrap rela">'.$source->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
		}
		echo "</tr>\n";
	}
	//-- table footer
	echo '<tr class="sortbottom"><td></td>';
	if ($SHOW_ID_NUMBERS) {
		echo '<td></td>';
	}
	echo '<td class="list_label">', i18n::translate('Total Sources'), ' : ', $n,  '</td><td></td><td class="t2" style="display:none;"></td><td></td><td></td><td></td><td></td>';
	if ($SHOW_LAST_CHANGE) {
		echo '<td></td>';
	}
	echo '</tr></table></fieldset>';
	// show TITLE2 col if not empty
	if ($t2) {
		echo <<< T2
		<script type="text/javascript">
			var table = document.getElementById("$table_id");
			cells = table.getElementsByTagName('td');
			for (i=0;i<cells.length;i++) {
				if (cells[i].className && (cells[i].className.indexOf('t2') != -1)) {
					cells[i].style.display="";
				}
			}
		</script>
T2;
	}
}


// BH print a sortable list of Shared Notes
/**
 * print a sortable table of shared notes
 *
 * @param array $datalist contain shared notes that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_note_table($datalist, $legend=null) {
	global $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES;

	if (count($datalist)<1) {
		return;
	}
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_note.php';

	if (!empty($WT_IMAGES["menu_note"]["small"])) {
		echo '<fieldset><legend><img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["menu_note"]["small"], '" align="middle" alt="" /> ';
	} else {
		echo '<fieldset><legend><img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES['notes']['small'], '" align="middle" alt="" /> ';
	}
	if ($legend) {
		echo $legend;
	} else {
		echo i18n::translate('Shared Notes');
	}
	echo '</legend>';
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo '<table id="', $table_id, '" class="sortable list_table center" ><tr><td></td>';
	if ($SHOW_ID_NUMBERS) {
		echo '<th class="list_label rela">NOTE</th>';
	}
	echo '<th class="list_label">', translate_fact('TITL'), '</th>';
	echo '<th class="list_label">', i18n::translate('Individuals'), '</th>';
	echo '<th class="list_label">', i18n::translate('Families'), '</th>';
	echo '<th class="list_label">', i18n::translate('Media'), '</th>';
	echo '<th class="list_label">', i18n::translate('Sources'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th class="list_label rela">', translate_fact('CHAN'), '</th>';
	}
	echo '</tr>';
	//-- table body
	$n=0;
	foreach ($datalist as $note) {
		if (!$note->canDisplayDetails()) {
			continue;
		}
		$link_url=encode_url($note->getLinkUrl());
		//-- Counter
		echo '<tr><td class="list_value_wrap rela list_item">', ++$n, '</td>';
		//-- Shared Note ID
		if ($SHOW_ID_NUMBERS) {
			echo '<td class="list_value_wrap rela">', $note->getXrefLink(), '</td>';
		}
		//-- Shared Note name(s)
		$tmp=$note->getFullName();
		echo '<td class="list_value_wrap" align="', get_align($tmp), '"><a href="', $link_url, '" class="list_item name2">', PrintReady($tmp), '</a></td>';
		//-- Linked INDIs
		$tmp=$note->countLinkedIndividuals();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked FAMs
		$tmp=$note->countLinkedfamilies();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked OBJEcts
		$tmp=$note->countLinkedMedia();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked SOURs
		$tmp=$note->countLinkedSources();
		echo '<td class="list_value_wrap"><a href="', $link_url, '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			print '<td class="list_value_wrap rela">'.$note->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
		}
		echo "</tr>\n";
	}
	//-- table footer
	echo '<tr class="sortbottom"><td></td>';
	if ($SHOW_ID_NUMBERS) {
		echo '<td></td>';
	}
	echo '<td class="list_label">', i18n::translate('Total Shared Notes'), ' : ', $n,  '</td><td></td><td class="t2" style="display:none;"></td><td></td><td></td><td></td>';
	if ($SHOW_LAST_CHANGE) {
		echo '<td></td>';
	}
	echo '</tr></table></fieldset>';
}

/**
 * print a sortable table of repositories
 *
 * @param array $datalist contain repositories that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_repo_table($repos, $legend='') {
	global $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER;

	if (!$repos) {
		return;
	}
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_repository.php';

	echo '<fieldset><legend><img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES['repository']['small'], '" align="middle" alt="" />';
	if ($legend) {
		echo htmlspecialchars($legend);
	} else {
		echo i18n::translate('Repositories found');
	}
	echo '</legend>';
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo '<table id="', $table_id, '" class="sortable list_table center"><tr><td></td>';
	if ($SHOW_ID_NUMBERS) {
		echo '<th class="list_label rela">REPO</th>';
	}
	echo '<th class="list_label">', translate_fact('NAME'), '</th>';
	echo '<th class="list_label">', i18n::translate('Sources'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th class="list_label rela">', translate_fact('CHAN'), '</th>';
	}
	echo '</tr>';
	//-- table body
	$n=0;
	foreach ($repos as $repo) {
		//-- Counter
		echo '<tr><td class="list_value_wrap rela list_item">', ++$n, '</td>';
		//-- REPO ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">', $repo->getXrefLink(), '</td>';
		//-- Repository name(s)
		$name = $repo->getFullName();
		echo '<td class="list_value_wrap" align="', get_align($name), '"><a href="', encode_url($repo->getLinkUrl()), '" class="list_item name2">', PrintReady(htmlspecialchars($name)), '</a>';
		$addname=$repo->getAddName();
		if ($addname) {
			echo '<br /><a href="', encode_url($repo->getLinkUrl()), '" class="list_item">', PrintReady(htmlspecialchars($addname)), '</a>';
		}
		echo '</td>';
		//-- Linked SOURces
		$tmp=$repo->countLinkedSources();
		echo '<td class="list_value_wrap"><a href="', encode_url($repo->getLinkUrl()), '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td class="list_value_wrap rela">', $repo->LastChangeTimestamp(!$SEARCH_SPIDER), '</td>';
		}
		echo '</tr>';
	}
	echo '</table></fieldset>';
}

/**
 * print a sortable table of media objects
 *
 * @param array $datalist contain media objects that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_media_table($datalist, $legend="") {
	global $SHOW_ID_NUMBERS, $SHOW_LAST_CHANGE, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $SHOW_MEDIA_FILENAME;

	if (count($datalist)<1) return;
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_media.php';

	if ($legend == "") $legend = i18n::translate('Media');
	$legend = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["media"]["small"]."\" alt=\"\" align=\"middle\" /> ".$legend;
	echo "<fieldset><legend>", $legend, "</legend>";
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table width=\"100%\" id=\"", $table_id, "\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">OBJE</th>";
	echo "<th class=\"list_label\">", translate_fact('TITL'), "</th>";
	echo "<th class=\"list_label\">", i18n::translate('Individuals'), "</th>";
	echo "<th class=\"list_label\">", i18n::translate('Families'), "</th>";
	echo "<th class=\"list_label\">", i18n::translate('Sources'), "</th>";
	if ($SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">", translate_fact('CHAN'), "</th>";
	echo "</tr>\n";
	//-- table body
	$n = 0;
	foreach ($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$media=$value;
		} else {
			$media = new Media($value["GEDCOM"]);
			if (is_null($media)) $media = Media::getInstance($key);
			if (is_null($media)) continue;
		}
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">", ++$n, "</td>";
		//-- Object ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">', $media->getXrefLink(), '</td>';
		//-- Object name(s)
		$name = $media->getFullName();
		echo "<td class=\"list_value_wrap\" align=\"", get_align($name), "\">";
		echo "<a href=\"", encode_url($media->getLinkUrl()), "\" class=\"list_item name2\">", PrintReady($name), "</a>";
		if ($SHOW_MEDIA_FILENAME || WT_USER_IS_ADMIN)
			echo "<br /><a href=\"", encode_url($media->getLinkUrl()), "\">", basename($media->file), "</a>";
		//echo "<br />", $media->getFiletype();
		//echo "&nbsp;&nbsp;", $media->width, "x", $media->height;
		//echo "&nbsp;&nbsp;", $media->getFilesize(), "kB";
		print_fact_notes("1 NOTE ".$media->getNote(), 1);
		echo "</td>";

		//-- Linked INDIs
		$tmp=$media->countLinkedIndividuals();
		echo '<td class="list_value_wrap"><a href="', encode_url($media->getLinkUrl()), '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked FAMs
		$tmp=$media->countLinkedfamilies();
		echo '<td class="list_value_wrap"><a href="', encode_url($media->getLinkUrl()), '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
		//-- Linked SOURces
		$tmp=$media->countLinkedSources();
		echo '<td class="list_value_wrap"><a href="', encode_url($media->getLinkUrl()), '" class="list_item" name="', $tmp, '">', $tmp, '</a></td>';
/*
		//-- Linked records
		foreach (array("INDI", "FAM", "SOUR") as $rectype) {
			$resu = array();
			foreach ($value["LINKS"] as $k=>$v) {
			  if ($v!=$rectype) continue;
				$record = GedcomRecord::getInstance($k);
				$txt = $record->getListName();
				if ($SHOW_ID_NUMBERS) $txt .= " (".$k.")";
				$resu[] = $txt;
			}
			sort($resu);
			echo "<td class=\"list_value_wrap\" align=\"", get_align(@$resu[0]), "\">";
			foreach ($resu as $txt) echo "<a href=\"", encode_url($record->getLinkUrl()), "\" class=\"list_item\">", PrintReady("&bull; ".$txt), "</a><br />";
			echo "</td>";
		}
*/
		//-- Last change
		if ($SHOW_LAST_CHANGE)
			print "<td class=\"list_value_wrap rela\">".$media->LastChangeTimestamp(empty($SEARCH_SPIDER))."</td>";
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "</fieldset>\n";
}

/**
 * print a tag cloud of surnames
 * print a sortable table of surnames
 *
 * @param array $datalist contain records that were extracted from the database.
 * @param string $target where to go after clicking a surname : INDI page or FAM page
 * @param string $listFormat presentation style: "style2 = sortable list, "style3" = cloud
 */
function print_surn_table($datalist, $target="INDI", $listFormat="") {
	global $GEDCOM, $TEXT_DIRECTION, $COMMON_NAMES_THRESHOLD;
	global $SURNAME_LIST_STYLE;
	if (count($datalist)<1) return;

	if (empty($listFormat)) $listFormat = $SURNAME_LIST_STYLE;

	if ($listFormat=="style3") {
	// Requested style is "cloud", where the surnames are a list of names (with links),
	// and the font size used for each name depends on the number of occurrences of this name
	// in the database - generally known as a 'tag cloud'.
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"", $table_id, "\" class=\"tag_cloud_table\">";
	//-- table body
	echo "<tr>";
	echo "<td class=\"tag_cloud\">";
	//-- Calculate range for font sizing
	$max_tag = 0;
	$font_tag = 0;
	foreach($datalist as $key => $value) {
		if (!isset($value["name"])) break;
		if ($value["match"]>$max_tag)
			$max_tag = $value["match"];
	}
	$font_tag = $max_tag / 6;
	//-- Print each name
	foreach($datalist as $key => $value) {
		if (!isset($value["name"])) break;
		$surn = $value["name"];
		$url = ($target=="FAM") ? 'fam' : 'indi';
		$url .= "list.php?ged={$GEDCOM}&surname={$surn}";
		$url = encode_url($url);
		if (empty($surn) || trim("@".$surn, "_")=="@" || $surn=="@N.N.") $surn = i18n::translate('(unknown)');
		$fontsize = ceil($value["match"]/$font_tag);
		if ($TEXT_DIRECTION=="ltr") {
			$title = PrintReady($surn." (".$value["match"].")");
			$tag = PrintReady("<font size=\"".$fontsize."\">".$surn."</font><span class=\"tag_cloud_sub\">&nbsp;(".$value["match"].")</span>");
		} else {
			$title = PrintReady("(".$value["match"].") ".$surn);
			$tag = PrintReady("<span class=\"tag_cloud_sub\">(".$value["match"].")&nbsp;</span><font size=\"".$fontsize."\">".$surn."</font>");
		}

		echo "<a href=\"{$url}\" class=\"list_item\" title=\"{$title}\">{$tag}</a>&nbsp;&nbsp; ";
	}
	echo "</td>";
	echo "</tr>\n";
	//-- table footer
	echo "</table>\n";
	return;
	}

	// Requested style isn't "cloud".  In this case, we'll produce a sortable list.
	require_once WT_ROOT.'js/sorttable.js.htm';
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"", $table_id, "\" class=\"sortable list_table center\">";
	echo "<tr>";
	echo "<td></td>";
	echo "<th class=\"list_label\">", translate_fact('SURN'), "</th>";
	echo "<th class=\"list_label\">";
	if ($target=="FAM") echo i18n::translate('Spouses'); else echo i18n::translate('Individuals');
	echo "</th>";
	echo "</tr>\n";
	//-- table body
	$total = 0;
	$n = 0;
	foreach($datalist as $key => $value) {
		if (!isset($value["name"])) break;
		$surn = $value["name"];
		$url = ($target=="FAM") ? 'fam' : 'indi';
		$url .= "list.php?ged={$GEDCOM}&surname={$surn}";
		$url = encode_url($url);
		//-- Counter
		echo "<tr>";
		echo "<td class=\"list_value_wrap rela list_item\">", ++$n, "</td>";
		//-- Surname
		if (empty($surn) or trim("@".$surn, "_")=="@" or $surn=="@N.N.") $surn = i18n::translate('(unknown)');
		echo "<td class=\"list_value_wrap\" align=\"", get_align($surn), "\">";
		echo "<a href=\"", $url, "\" class=\"list_item name1\">", PrintReady($surn), "</a>";
		echo "&nbsp;</td>";
		//-- Surname count
		echo "<td class=\"list_value_wrap\">";
		echo "<a href=\"{$url}\" class=\"list_item name2\" name=\"{$value['match']}\">{$value["match"]}</a>";
		echo "</td>";
		$total += $value["match"];

		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	echo "<td class=\"list_item\">&nbsp;</td>";
	echo "<td class=\"list_item\">&nbsp;</td>";
	echo "<td class=\"list_label name2\">", $total, "</td>";
	echo "</tr>\n";
	echo "</table>\n";
}

// Print a table of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $type string, indilist or famlist
function format_surname_table($surnames, $type) {
	global $GEDCOM;

	require_once WT_ROOT.'js/sorttable.js.htm';
	$table_id ='ID'.floor(microtime()*1000000); // sorttable requires a unique ID
	$html='<table id="'.$table_id.'" class="sortable list_table center">';
	$html.='<tr><th></th>';
	$html.='<th class="list_label"><a href="javascript:;" onclick="sortByOtherCol(this, 1)">'.translate_fact('SURN').'</a></th>';
	$html.='<th style="display:none;">SURN</th>'; // hidden column for sorting surnames
	$html.='<th class="list_label">';
	if ($type=='famlist') {
		$html.=i18n::translate('Spouses');
	} else {
		$html.=i18n::translate('Individuals');
	}
	$html.='</th></tr>';

	$unique_surn=array();
	$unique_indi=array();
	$row_num=0;
	foreach ($surnames as $surn=>$surns) {
		// Each surname links back to the indi/fam surname list
		if ($surn) {
			$url=$type.'.php?surname='.urlencode($surn).'&amp;ged='.urlencode($GEDCOM);
		} else {
			$url=$type.'.php?alpha=,&amp;ged='.urlencode($GEDCOM);
		}
		// Row counter
		++$row_num;
		$html.='<tr><td class="list_value_wrap rela list_item">'.$row_num.'</td>';
		// Surname
		$html.='<td class="list_value_wrap" align="'.get_align($surn).'">';
		// Uncomment this block if you want SMITH/Smith/smith merged.  Note that when
		// such case variants exist, the actual one displayed is undefined.
		//$first_spfxsurn=null;
		//foreach ($surns as $spfxsurn=>$indis) {
		//	if ($first_spfxsurn) {
		//		if (utf8_strtoupper($spfxsurn)==utf8_strtoupper($first_spfxsurn)) {
		//			$surns[$first_spfxsurn]=array_merge($surns[$first_spfxsurn], $surns[$spfxsurn]);
		//			unset ($surns[$spfxsurn]);
		//		}
		//	} else {
		//		$first_spfxsurn=$spfxsurn;
		//	}
		//}
		if (count($surns)==1) {
			// Single surname variant
			foreach ($surns as $spfxsurn=>$indis) {
				$html.='<a href="'.$url.'" class="list_item name1">'.PrintReady($spfxsurn).'</a>';
				$unique_surn[$spfxsurn]=true;
				foreach (array_keys($indis) as $pid) {
					$unique_indi[$pid]=true;
				}
			}
		} else {
			// Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc.
			foreach ($surns as $spfxsurn=>$indis) {
				$html.='<a href="'.$url.'" class="list_item name1">'.PrintReady($spfxsurn).'</a><br />';
				$unique_surn[$spfxsurn]=true;
				foreach (array_keys($indis) as $pid) {
					$unique_indi[$pid]=true;
				}
			}
		}
		$html.='</td>';
		// Hidden column for sorting surnames
		$html.='<td style="display:none;">'.htmlspecialchars($surn, ENT_COMPAT, 'UTF-8').'</td>';
		// Surname count
		$html.='<td class="list_value_wrap">';
		if (count($surns)==1) {
			// Single surname variant
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal=count($indis);
				$html.='<a name="'.$subtotal.'">'.$subtotal.'</a>';
			}
		} else {
			// Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc.
			$subtotal=0;
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal+=count($indis);
				$html.=count($indis).'<br />';
			}
			$html.='<a name="'.$subtotal.'">'.$subtotal.'</a>';
		}
		$html.='</td></tr>';
	}
	//-- table footer
	$html.='<tr class="sortbottom"><td class="list_item">&nbsp;</td>';
	$html.='<td class="list_item">&nbsp;</td>';
	$html.='<td style="display:none;">&nbsp;</td>'; // hidden column for sorting surnames
	$html.='<td class="list_label name2">'.i18n::translate('Total individuals').': '.count($unique_indi);
	$html.='<br/>'.i18n::translate('Total Names').': '.count($unique_surn).'</td></tr></table>';
	return $html;
}

// Print a tagcloud of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $type string, indilist or famlist
// @param $totals, boolean, show totals after each name
function format_surname_tagcloud($surnames, $type, $totals) {
	global $TEXT_DIRECTION, $GEDCOM;

	// Requested style is "cloud", where the surnames are a list of names (with links),
	// and the font size used for each name depends on the number of occurrences of this name
	// in the database - generally known as a 'tag cloud'.
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	$html='<table id="'.$table_id.'" class="tag_cloud_table"><tr><td class="tag_cloud">';
	//-- Calculate range for font sizing
	$max_tag = 0;
	$font_tag = 0;

	foreach ($surnames as $surn=>$surns) {
		foreach ($surns as $spfxsurn=>$indis) {
			$max_tag=max($max_tag, count($indis));
		}
	}
	$font_tag = $max_tag / 6;
	//-- Print each name
	foreach ($surnames as $surn=>$surns) {
		// Each surname links back to the indi/fam surname list
		if ($surn) {
			$url=$type.'.php?surname='.urlencode($surn).'&amp;ged='.urlencode($GEDCOM);
		} else {
			$url=$type.'.php?alpha=,&amp;ged='.urlencode($GEDCOM);
		}
		// If all the surnames are just case variants, then merge them into one
		// Comment out this block if you want SMITH listed separately from Smith
		$first_spfxsurn=null;
		foreach ($surns as $spfxsurn=>$indis) {
			if ($first_spfxsurn) {
				if (utf8_strtoupper($spfxsurn)==utf8_strtoupper($first_spfxsurn)) {
					$surns[$first_spfxsurn]=array_merge($surns[$first_spfxsurn], $surns[$spfxsurn]);
					unset ($surns[$spfxsurn]);
				}
			} else {
				$first_spfxsurn=$spfxsurn;
			}
		}
		foreach ($surns as $spfxsurn=>$indis) {
			$count=count($indis);
			$fontsize = ceil($count/$font_tag);
			if ($totals) {
				$total='('.$count.')';
			} else {
				$total='';
			}
			if ($TEXT_DIRECTION=="ltr") {
				$tag = "<font size=\"".$fontsize."\">".PrintReady($spfxsurn)."</font><span class=\"tag_cloud_sub\">&nbsp;".$total."</span>";
			} else {
				$tag = PrintReady("<span class=\"tag_cloud_sub\">".getRLM().$total.getRLM()."&nbsp;</span><font size=\"".$fontsize."\">".$spfxsurn."</font>");
			}
			$html.='<a href="'.$url.'" class="list_item">'.$tag.'</a> ';
		}
	}
	$html.='</td></tr></table>';
	return $html;
}

// Print a list of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $style, 1=bullet list, 2=semicolon-separated list
// @param $totals, boolean, show totals after each name
function format_surname_list($surnames, $style, $totals) {
	global $TEXT_DIRECTION, $GEDCOM;

	$html=array();
	foreach ($surnames as $surn=>$surns) {
		// Each surname links back to the indilist
		if ($surn) {
			$url='indilist.php?surname='.urlencode($surn).'&amp;ged='.urlencode($GEDCOM);
		} else {
			$url='indilist.php?alpha=,&amp;ged='.urlencode($GEDCOM);
		}
		// If all the surnames are just case variants, then merge them into one
		// Comment out this block if you want SMITH listed separately from Smith
		$first_spfxsurn=null;
		foreach ($surns as $spfxsurn=>$indis) {
			if ($first_spfxsurn) {
				if (utf8_strtoupper($spfxsurn)==utf8_strtoupper($first_spfxsurn)) {
					$surns[$first_spfxsurn]=array_merge($surns[$first_spfxsurn], $surns[$spfxsurn]);
					unset ($surns[$spfxsurn]);
				}
			} else {
				$first_spfxsurn=$spfxsurn;
			}
		}
		$subhtml='<a href="'.$url.'">'.implode(', ', array_keys($surns)).'</a>';

		if ($totals) {
			$subtotal=0;
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal+=count($indis);
			}
			$subhtml.=' ['.$subtotal.']';
		}
		$html[]=PrintReady($subhtml);

	}
	switch ($style) {
	case 1:
		return '<ul><li>'.implode('</li><li>', $html).'</li></ul>';
	case 2:
		return implode('; ', $html);
	}
}


/**
 * print a sortable table of recent changes
 * also called by mediaviewer to list records linked to a media
 *
 * @param array $datalist contain records that were extracted from the database.
 */
function print_changes_table($datalist, $showChange=true, $total='', $show_WT_USER=true) {
	global $SHOW_ID_NUMBERS, $SHOW_MARRIED_NAMES, $TEXT_DIRECTION;
	if (count($datalist)<1) return;
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';
	if (empty($total)) $total = i18n::translate('Total changes');
	$indi = false;
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID
	//-- table header
	echo "<table id=\"", $table_id, "\" class=\"sortable list_table center\">";
	echo "<tr>";
	//echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<th class=\"list_label rela\">", i18n::translate('ID'), "</th>";
	echo "<th class=\"list_label\">", i18n::translate('Record'), "</th>";
	echo "<th style=\"display:none\">GIVN</th>";
	if ($showChange) {
		echo "<th class=\"list_label\">", translate_fact('CHAN'), "</th>";
		if ($show_WT_USER) {
			echo "<th class=\"list_label\">", translate_fact('_WT_USER'), "</th>";
		}
	}
	echo "</tr>\n";
	//-- table body
	$hidden = 0;
	$n = 0;
	$NMAX = 1000;
	foreach($datalist as $key => $value) {
		if ($n>=$NMAX) break;
		$record=GedcomRecord::getInstance($value);
		if (!$record) {
			$record=GedcomRecord::getInstance($key);
			if (!$record) {
				continue;
			}
		}
		// Privacy
		if (!$record->canDisplayDetails()) {
			$hidden++;
			continue;
		}
		//-- Counter
		echo "<tr>";
		//echo "<td class=\"list_value_wrap rela list_item\">", ++$n, "</td>";
		++$n;
		//-- Record ID
		if ($SHOW_ID_NUMBERS)
			echo '<td class="list_value_wrap rela">', $record->getXrefLink(), '</td>';
		//-- Record name(s)
		$name = $record->getFullName();
		echo "<td class=\"list_value_wrap\" align=\"", get_align($name), "\">";
		echo "<a href=\"", encode_url($record->getLinkUrl()), "\" class=\"list_item name2\" dir=\"", $TEXT_DIRECTION, "\">", PrintReady($name), "</a>";
		if ($record->getType()=="INDI") {
			echo $record->getSexImage();
			$indi=true;
		}
		$addname=$record->getAddName();
		if ($addname) {
			echo "<br /><a href=\"", encode_url($record->getLinkUrl()), "\" class=\"list_item\">", PrintReady($addname), "</a>";
		}
		if ($record->getType()=='INDI') {
			if ($SHOW_MARRIED_NAMES) {
				foreach ($record->getAllNames() as $name) {
					if ($name['type']=='_MARNM') {
						echo "<br /><a title=\"_MARNM\" href=\"", encode_url($record->getLinkUrl()), "\" class=\"list_item\">", PrintReady($name['full']), "</a>";
					}
				}
			}
			echo $record->getPrimaryParentsNames("parents_$table_id details1", "none");
		}
		echo "</td>";
		//-- GIVN
		echo "<td style=\"display:none\">";
		$exp = explode(",", str_replace('<', ',', $name).",");
		echo $exp[1];
		echo "</td>";
		if ($showChange) {
			//-- Last change date/time
			print "<td class=\"list_value_wrap rela\">".$record->LastChangeTimestamp(empty($SEARCH_SPIDER))."</td>";
			if ($show_WT_USER) {
				//-- Last change user
				print "<td class=\"list_value_wrap rela\">".$record->LastChangeUser(empty($SEARCH_SPIDER))."</td>";
			}
		}
		echo "</tr>\n";
	}
	//-- table footer
	echo "<tr class=\"sortbottom\">";
	//echo "<td></td>";
	if ($SHOW_ID_NUMBERS) echo "<td></td>";
	echo "<td class=\"list_label\">";
	if ($n>1 && $indi) {
		echo '<a href="javascript:;" onclick="sortByOtherCol(this, 1)"><img src="images/topdown.gif" alt="" border="0" /> ', translate_fact('GIVN'), '</a><br />';
	}
	if ($indi) {
		echo "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', 'parents_$table_id');\" /><label for=\"cb_parents_$table_id\">", i18n::translate('Show parents'), "</label><br />";
	}
	echo $total, ": ", $n;
	if ($hidden) echo "<br /><span class=\"warning\">", i18n::translate('Hidden'), " : ", $hidden, "</span>";
	if ($n>=$NMAX) echo "<br /><span class=\"warning\">", i18n::translate('Recent Changes'), " &gt; ", $NMAX, "</span>";
	echo "</td>";
	echo "<td style=\"display:none\">GIVN</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "</tr>";
	echo "</table>\n";
}

/**
 * print a sortable table of events
 * and generates hCalendar records
 * @see http://microformats.org/
 *
 * @param array $datalist contain records that were extracted from the database.
 */
function print_events_table($startjd, $endjd, $events='BIRT MARR DEAT', $only_living=false, $allow_download=false, $sort_by_event=false) {
	global $TEXT_DIRECTION;
	require_once WT_ROOT.'js/sorttable.js.htm';
	require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';
	$table_id = "ID".floor(microtime()*1000000); // sorttable requires a unique ID

	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;
	$private = 0;

	$return = '';

	$filtered_events = array();

	foreach (get_events_list($startjd, $endjd, $events) as $value) {
		$record=$value['record'];
		//-- only living people ?
		if ($only_living) {
			if ($record->getType()=="INDI" && $record->isDead()) {
				$filter ++;
				continue;
			}
			if ($record->getType()=="FAM") {
				$husb = $record->getHusband();
				if (is_null($husb) || $husb->isDead()) {
					$filter ++;
					continue;
				}
				$wife = $record->getWife();
				if (is_null($wife) || $wife->isDead()) {
					$filter ++;
					continue;
				}
			}
		}

		// Privacy
		if (!$record->canDisplayDetails() || !showFactDetails($value['fact'], $value['id']) || FactViewRestricted($value['id'], $value['factrec'])) {
			$private ++;
			continue;
		}
		//-- Counter
		$output ++;

		if ($output==1) {
			//-- First table row:  start table headers, etc. first
			$return .= "<table id=\"".$table_id."\" class=\"sortable list_table center\">";
			$return .= "<tr>";
			$return .= "<th class=\"list_label\">".i18n::translate('Record')."</th>";
			$return .= "<th style=\"display:none\">GIVN</th>";
			$return .= "<th class=\"list_label\">".translate_fact('DATE')."</th>";
			$return .= "<th class=\"list_label\"><img src=\"./images/reminder.gif\" alt=\"".i18n::translate('Anniversary')."\" title=\"".i18n::translate('Anniversary')."\" border=\"0\" /></th>";
			$return .= "<th class=\"list_label\">".translate_fact('EVEN')."</th>";
			$return .= "</tr>\n";
		}

		$value['name'] = $record->getListName();
		$value['url'] = $record->getLinkUrl();
		if ($record->getType()=="INDI") {
			$value['sex'] = $record->getSexImage();
		} else {
			$value['sex'] = '';
		}
		$filtered_events[] = $value;
	}

	// Now we've filtered the list, we can sort by event, if required
	if ($sort_by_event=="anniv") {
		uasort($filtered_events, 'event_sort');
	} elseif ($sort_by_event) {
		uasort($filtered_events, 'event_sort_name');
	}

	foreach($filtered_events as $value) {
		$return .= "<tr class=\"vevent\">"; // hCalendar:vevent
		//-- Record name(s)
		$name = $value['name'];
		if ($value['record']->getType()=="FAM") {
			$exp = explode("<br />", $name);
			$husb = $value['record']->getHusband();
			if ($husb) $exp[0] .= $husb->getPrimaryParentsNames("parents_$table_id details1", "none");
			$wife = $value['record']->getWife();
			if ($wife) $exp[1] .= $wife->getPrimaryParentsNames("parents_$table_id details1", "none");
			$name = implode("<div></div>", $exp); // <div></div> is better here than <br />
		}
		$return .= "<td class=\"list_value_wrap\" align=\"".get_align($name)."\">";
		$return .= "<a href=\"".encode_url($value['url'])."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($name)."</a>";
		if ($value['record']->getType()=="INDI") {
			$return .= $value['sex'];
			$return .= $value['record']->getPrimaryParentsNames("parents_$table_id details1", "none");
		}
		$return .= "</td>";
		//-- GIVN
		$return .= "<td style=\"display:none\">";
		$exp = explode(",", str_replace('<', ',', $name).",");
		$return .= $exp[1];
		$return .= "</td>";
		//-- Event date
		$return .= "<td class=\"list_value_wrap\">";
		$return .= str_replace('<a', '<a name="'.$value['jd'].'"', $value['date']->Display(empty($SEARCH_SPIDER)));
		$return .= "</td>";
		//-- Anniversary
		$return .= "<td class=\"list_value_wrap rela\">";
		$anniv = $value['anniv'];
		if ($anniv==0) $return .= '<a name="-1">&nbsp;</a>';
		else $return .= "<a name=\"{$anniv}\">{$anniv}</a>";
		if ($allow_download) {
			// hCalendar:dtstart and hCalendar:summary
			$return .= "<abbr class=\"dtstart\" title=\"".strip_tags($value['date']->Display(false, 'Ymd', array()))."\"></abbr>";
			$return .= "<abbr class=\"summary\" title=\"".i18n::translate('Anniversary')." #$anniv ".i18n::translate($value['fact'])." : ".PrintReady(strip_tags($record->getFullName()))."\"></abbr>";
		}
		$return .= "</td>";
		//-- Event name
		$return .= "<td class=\"list_value_wrap\">";
		$return .= "<a href=\"".encode_url($value['url'])."\" class=\"list_item url\">".translate_fact($value['fact'])."</a>"; // hCalendar:url
		$return .= "&nbsp;</td>";

		$return .= "</tr>\n";
	}

	if ($output!=0) {
		//-- table footer
		$return .= "<tr class=\"sortbottom\">";
		$return .= "<td class=\"list_label\">";
		$return .= "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', 'parents_$table_id');\" /><label for=\"cb_parents_$table_id\">&nbsp;&nbsp;".i18n::translate('Show parents')."</label><br />";
		$return .= "</td><td class=\"list_label\" colspan=\"3\">";
		$return .= i18n::translate('Total events').": ".$output;
		if ($allow_download) {
			$uri = WT_SERVER_NAME.WT_SCRIPT_PATH.basename($_SERVER["REQUEST_URI"]);
			$title = i18n::translate('Download file %s', 'hCal-events.ics');
			$return .= "<br /><a href=\"".encode_url("http://feeds.technorati.com/events/{$uri}")."\"><img src=\"images/hcal.png\" border=\"0\" alt=\"".$title."\" title=\"".$title."\" /></a>";
		}
		$return .= "</td>";
		$return .= "<td></td>";
		$return .= "<td></td>";
		$return .= "</tr>";
		$return .= "</table>\n";
	}

	// Print a final summary message about restricted/filtered facts
	$summary = "";
	if ($endjd==client_jd()) {
		// We're dealing with the Today's Events block
		if ($private!=0) {
			// We lost some output due to Privacy restrictions
			if ($output!=0) {
				$summary = i18n::translate('More events exist for today, but privacy restrictions prevent you from seeing them.');
			} else {
				$summary = i18n::translate('Events exist for today, but privacy restrictions prevent you from seeing them.');
			}
		} else if ($filter!=0) {
			// We lost some output due to filtering for living people
			if ($output==0) {
				$summary = i18n::translate('No events for living people exist for today.');
			}
		} else {
			if ($output==0) {
				$summary = i18n::translate('No events exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($private!=0) {
			// We lost some output due to Privacy restrictions
			if ($output!=0) {
				if ($endjd==$startjd) {
					$summary = i18n::translate('More events exist for tomorrow, but privacy restrictions prevent you from seeing them.');
				} else {
					$summary = i18n::translate('More events exist for the next %s days, but privacy restrictions prevent you from seeing them.', $endjd-$startjd+1);
				}
			} else {
				if ($endjd==$startjd) {
					$summary = i18n::translate('Events exist for tomorrow, but privacy restrictions prevent you from seeing them.');
				} else {
					$summary = i18n::translate('Events exist for the next %s days, but privacy restrictions prevent you from seeing them.', $endjd-$startjd+1);
				}
			}
		} else if ($filter!=0) {
			// We lost some output due to filtering for living people
			if ($output==0) {
				if ($endjd==$startjd) {
					$summary = i18n::translate('No events for living people exist for tomorrow.');
				} else {
					$summary = i18n::translate('No events for living people exist for the next %s days.', $endjd-$startjd+1);
				}
			}
		} else {
			if ($output==0) {
				if ($endjd==$startjd) {
					$summary = i18n::translate('No events exist for tomorrow.');
				} else {
					$summary = i18n::translate('No events exist for the next %s days.', $endjd-$startjd+1);
				}
			}
		}
	}
	if ($summary!="") {
		$return .= "<b>". $summary. "</b>";
	}

	return $return;
}

/**
 * print a list of events
 *
 * This performs the same function as print_events_table(), but formats the output differently.
 */
function print_events_list($startjd, $endjd, $events='BIRT MARR DEAT', $only_living=false, $sort_by_event=false) {
	global $TEXT_DIRECTION;

	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;
	$private = 0;

	$return = '';

	$filtered_events = array();

	foreach (get_events_list($startjd, $endjd, $events) as $value) {
		$record = GedcomRecord::getInstance($value['id']);
		//-- only living people ?
		if ($only_living) {
			if ($record->getType()=="INDI" && $record->isDead()) {
				$filter ++;
				continue;
			}
			if ($record->getType()=="FAM") {
				$husb = $record->getHusband();
				if (is_null($husb) || $husb->isDead()) {
					$filter ++;
					continue;
				}
				$wife = $record->getWife();
				if (is_null($wife) || $wife->isDead()) {
					$filter ++;
					continue;
				}
			}
		}

		// Privacy
		if (!$record->canDisplayDetails() || !showFactDetails($value['fact'], $value['id']) || FactViewRestricted($value['id'], $value['factrec'])) {
			$private ++;
			continue;
		}
		$output ++;

		$value['name'] = $record->getListName();
		$value['url'] = $record->getLinkUrl();
		if ($record->getType()=="INDI") {
			$value['sex'] = $record->getSexImage();
		} else {
			$value['sex'] = '';
		}
		$filtered_events[] = $value;
	}

	// Now we've filtered the list, we can sort by event, if required
	if ($sort_by_event=="anniv") {
		uasort($filtered_events, 'event_sort');
	} elseif ($sort_by_event) {
		uasort($filtered_events, 'event_sort_name');
	}

	foreach($filtered_events as $value) {
		$return .= "<a href=\"".encode_url($value['url'])."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".PrintReady($value['name'])."</a>".$value['sex'];
		$return .= "<br /><div class=\"indent\">";
		$return .= i18n::translate($value['fact']).' - '.$value['date']->Display(true);
		if ($value['anniv']!=0) $return .= " (" . i18n::translate('%s year anniversary', $value['anniv']).")";
		if (!empty($value['plac'])) $return .= " - <a href=\"".encode_url(get_place_url($value['plac']))."\">".$value['plac']."</a>";
		$return .= "</div>";
	}

	// Print a final summary message about restricted/filtered facts
	$summary = "";
	if ($endjd==client_jd()) {
		// We're dealing with the Today's Events block
		if ($private!=0) {
			// We lost some output due to Privacy restrictions
			if ($output!=0) {
				$summary = i18n::translate('More events exist for today, but privacy restrictions prevent you from seeing them.');
			} else {
				$summary = i18n::translate('Events exist for today, but privacy restrictions prevent you from seeing them.');
			}
		} else if ($filter!=0) {
			// We lost some output due to filtering for living people
			if ($output==0) {
				$summary = i18n::translate('No events for living people exist for today.');
			}
		} else {
			if ($output==0) {
				$summary = i18n::translate('No events exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($private!=0) {
			// We lost some output due to Privacy restrictions
			if ($output!=0) {
				if ($endjd==$startjd) {
					$summary = i18n::translate('More events exist for tomorrow, but privacy restrictions prevent you from seeing them.');
				} else {
					$summary = i18n::translate('More events exist for the next %s days, but privacy restrictions prevent you from seeing them.', $endjd-$startjd+1);
				}
			} else {
				if ($endjd==$startjd) {
					$summary = i18n::translate('Events exist for tomorrow, but privacy restrictions prevent you from seeing them.');
				} else {
					$summary = i18n::translate('Events exist for the next %s days, but privacy restrictions prevent you from seeing them.', $endjd-$startjd+1);
				}
			}
		} else if ($filter!=0) {
			// We lost some output due to filtering for living people
			if ($output==0) {
				if ($endjd==$startjd) {
					$summary = i18n::translate('No events for living people exist for tomorrow.');
				} else {
					$summary = i18n::translate('No events for living people exist for the next %s days.', $endjd-$startjd+1);
				}
			}
		} else {
			if ($output==0) {
				if ($endjd==$startjd) {
					$summary = i18n::translate('No events exist for tomorrow.');
				} else {
					$summary = i18n::translate('No events exist for the next %s days.', $endjd-$startjd+1);
				}
			}
		}
	}
	if ($summary!="") {
		$return .= "<b>". $summary. "</b>";
	}

	return $return;
}

/**
 * print a chart by age using Google chart API
 *
 * @param array $data
 * @param string $title
 */
function print_chart_by_age($data, $title) {
	global $GEDCOM;
	global $view, $stylesheet, $print_stylesheet;

	$css = new cssparser(false);
	if ($view=="preview") {
		$css->Parse($print_stylesheet);
	} else {
		$css->Parse($stylesheet);
	}
	$color = $css->Get("body", "background-color");
	$color = str_replace("#", "", $color);
	switch(strtoupper($color)) {
	case "": case "FFFFFF": case "WHITE":
	case "002540": case "004025": case "400025": // simply themes needs bright backgound
		$color="FFFFFF99"; // opacity
		break;
	}
	$count = 0;
	$agemax = 0;
	$vmax = 0;
	$avg = 0;
	foreach ($data as $age=>$v) {
		$n = strlen($v);
		$vmax = max($vmax, $n);
		$agemax = max($agemax, $age);
		$count += $n;
		$avg += $age*$n;
	}
	if ($count<1) return;
	$avg = round($avg/$count);
	$chart_url = "http://chart.apis.google.com/chart?cht=bvs"; // chart type
	$chart_url .= "&amp;chs=725x150"; // size
	$chart_url .= "&amp;chbh=3,2,2"; // bvg : 4,1,2
	$chart_url .= "&amp;chf=bg,s,".$color; //background color
	$chart_url .= "&amp;chco=0000FF,FFA0CB,FF0000"; // bar color
	$chart_url .= "&amp;chdl=".i18n::translate('Males')."|".i18n::translate('Females')."|".i18n::translate('Average age').": ".$avg; // legend & average age
	$chart_url .= "&amp;chtt=".urlencode($title); // title
	$chart_url .= "&amp;chxt=x,y,r"; // axis labels specification
	$chart_url .= "&amp;chm=V,FF0000,0,".($avg-0.3).",1"; // average age line marker
	$chart_url .= "&amp;chxl=0:|"; // label
	for ($age=0; $age<=$agemax; $age+=5) {
		$chart_url .= $age."|||||"; // x axis
	}
	$chart_url .= "|1:||".sprintf("%1.0f", $vmax/$count*100)." %"; // y axis
	$chart_url .= "|2:||";
	$step = $vmax;
	for ($d=floor($vmax); $d>0; $d--) {
		if ($vmax<($d*10+1) && fmod($vmax, $d)==0) $step = $d;
	}
	if ($step==floor($vmax)) {
		for ($d=floor($vmax-1); $d>0; $d--) {
			if (($vmax-1)<($d*10+1) && fmod(($vmax-1), $d)==0) $step = $d;
		}
	}
	for ($n=$step; $n<$vmax; $n+=$step) {
		$chart_url .= $n."|";
	}
	$chart_url .= $vmax." / ".$count; // r axis
	$chart_url .= "&amp;chg=100,".round(100*$step/$vmax, 1).",1,5"; // grid
	$chart_url .= "&amp;chd=s:"; // data : simple encoding from A=0 to 9=61
	$CHART_ENCODING61 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for ($age=0; $age<=$agemax; $age++) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$age], "M")*61/$vmax)];
	}
	$chart_url .= ",";
	for ($age=0; $age<=$agemax; $age++) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$age], "F")*61/$vmax)];
	}
	echo "<img src=\"", $chart_url, "\" alt=\"", $title, "\" title=\"", $title, "\" class=\"gchart\" />";
}

/**
 * print a chart by decade using Google chart API
 *
 * @param array $data
 * @param string $title
 */
function print_chart_by_decade($data, $title) {
	global $view, $stylesheet, $print_stylesheet;

	$css = new cssparser(false);
	if ($view=="preview") $css->Parse($print_stylesheet);
	else $css->Parse($stylesheet);
	$color = $css->Get("body", "background-color");
	$color = str_replace("#", "", $color);
	switch(strtoupper($color)) {
	case "": case "FFFFFF": case "WHITE":
	case "002540": case "004025": case "400025": // simply themes needs bright backgound
		$color="FFFFFF99"; // opacity
		break;
	}
	$count = 0;
	$vmax = 0;
	foreach ($data as $age=>$v) {
		$n = strlen($v);
		$vmax = max($vmax, $n);
		$count += $n;
	}
	if ($count<1) return;
	$chart_url = "http://chart.apis.google.com/chart?cht=bvs"; // chart type
	$chart_url .= "&amp;chs=360x150"; // size
	$chart_url .= "&amp;chbh=3,3"; // bvg : 4,1,2
	$chart_url .= "&amp;chf=bg,s,".$color; //background color
	$chart_url .= "&amp;chco=0000FF,FFA0CB"; // bar color
	$chart_url .= "&amp;chtt=".urlencode($title); // title
	$chart_url .= "&amp;chxt=x,y,r"; // axis labels specification
	$chart_url .= "&amp;chxl=0:|&lt;|||"; // <1570
	for ($y=1600; $y<2030; $y+=50) {
		$chart_url .= $y."|||||"; // x axis
	}
	$chart_url .= "|1:||".sprintf("%1.0f", $vmax/$count*100)." %"; // y axis
	$chart_url .= "|2:||";
	$step = $vmax;
	for ($d=floor($vmax); $d>0; $d--) {
		if ($vmax<($d*10+1) && fmod($vmax, $d)==0) $step = $d;
	}
	if ($step==floor($vmax)) {
		for ($d=floor($vmax-1); $d>0; $d--) {
			if (($vmax-1)<($d*10+1) && fmod(($vmax-1), $d)==0) $step = $d;
		}
	}
	for ($n=$step; $n<$vmax; $n+=$step) {
		$chart_url .= $n."|";
	}
	$chart_url .= $vmax." / ".$count; // r axis
	$chart_url .= "&amp;chg=100,".round(100*$step/$vmax, 1).",1,5"; // grid
	$chart_url .= "&amp;chd=s:"; // data : simple encoding from A=0 to 9=61
	$CHART_ENCODING61 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for ($y=1570; $y<2030; $y+=10) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$y], "M")*61/$vmax)];
	}
	$chart_url .= ",";
	for ($y=1570; $y<2030; $y+=10) {
		$chart_url .= $CHART_ENCODING61[floor(substr_count($data[$y], "F")*61/$vmax)];
	}
	echo "<img src=\"", $chart_url, "\" alt=\"", $title, "\" title=\"", $title, "\" class=\"gchart\" />";
}

/**
 * check string align direction depending on language and rtl config
 *
 * @param string $txt string argument
 * @return string left|right
 */
function get_align($txt) {
		global $TEXT_DIRECTION;

		if (!empty($txt)) {
			if ($TEXT_DIRECTION=="rtl" && !hasRTLText($txt) && hasLTRText($txt)) return "left";
			if ($TEXT_DIRECTION=="ltr" && hasRTLText($txt) && !hasLTRText($txt)) return "right";
		}
		if ($TEXT_DIRECTION=="rtl") return "right";
		return "left";
}

/**
 * load behaviour js data
 * to be called at the end just before </body> tag
 *
 * @see http://bennolan.com/behaviour/
 * @param none
 */
function load_behaviour() {
	require_once WT_ROOT.'js/prototype.js.htm';
	require_once WT_ROOT.'js/behaviour.js.htm';
	require_once WT_ROOT.'js/overlib.js.htm';
?>
	<script type="text/javascript">
	// <![CDATA[
	var myrules = {
		'fieldset button' : function(element) {
			element.onmouseover = function() { // show helptext
				helptext = this.title;
				if (helptext=='') helptext = this.value;
				if (helptext=='' || helptext==undefined) helptext = 'Help text : button_'+this.className;
				this.title = helptext; if (document.all) return; // IE = title
				this.value = helptext; this.title = ''; // Firefox = value
				return overlib(helptext, BGCOLOR, "#000000", FGCOLOR, "#FFFFE0");
			}
			element.onmouseout = nd; // hide helptext
			element.onmousedown = function() { // show active button
				var buttons = this.parentNode.getElementsByTagName("button");
				for (var i=0; i<buttons.length; i++) buttons[i].style.opacity = 1;
				this.style.opacity = 0.67;
			}
			element.onclick = function() { // apply filter
				var temp = this.parentNode.getElementsByTagName("table")[0];
				if (!temp) return true;
				var table = temp.id;
				var args = this.className.split('_'); // eg: BIRT_YES
				if (args[0]=="alive") return table_filter_alive(table);
				if (args[0]=="reset") return table_filter(table, "", "");
				if (args[1].length) return table_filter(table, args[0], args[1]);
				return false;
			}
		}/**,
		'.sortable th' : function(element) {
			element.onmouseout = nd; // hide helptext
			element.onmouseover = function() { // show helptext
				helptext = this.title;
				if (helptext=='') helptext = this.value;
				if (helptext=='' || helptext==undefined) helptext = <?php echo "'", i18n::translate('Sort by this column.'), "'"; ?>;
				this.title = helptext; if (document.all) return; // IE = title
				this.value = helptext; this.title = ''; // Firefox = value
				return overlib(helptext, BGCOLOR, "#000000", FGCOLOR, "#FFFFE0");
			}
		}**/
	}
	Behaviour.register(myrules);
	// ]]>
	</script>
<?php
}

?>
