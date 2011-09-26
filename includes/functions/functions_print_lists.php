<?php
// Functions for printing lists
//
// Various printing functions for printing lists
// used on the indilist, famlist, find, and search pages.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

require_once WT_ROOT.'includes/functions/functions_places.php';

/**
 * print a sortable table of individuals
 *
 * @param array $datalist contain individuals that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_indi_table($datalist, $legend="", $option="") {
	global $GEDCOM, $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER, $MAX_ALIVE_AGE;

	$SHOW_EST_LIST_DATES=get_gedcom_setting(WT_GED_ID, 'SHOW_EST_LIST_DATES');

	if ($option=="MARR_PLAC") return;
	if (count($datalist)<1) return;
	$tiny = (count($datalist)<=500);
	require_once WT_ROOT.'js/sorttable.js.htm';
	$stats = new WT_Stats($GEDCOM);

	// Bad data can cause "longest life" to be huge, blowing memory limits
	$max_age = min($MAX_ALIVE_AGE, $stats->LongestLifeAge())+1;

	//-- init chart data
	for ($age=0; $age<=$max_age; $age++) $deat_by_age[$age]="";
	for ($year=1550; $year<2030; $year+=10) $birt_by_decade[$year]="";
	for ($year=1550; $year<2030; $year+=10) $deat_by_decade[$year]="";
	//-- fieldset
	if ($option=="BIRT_PLAC" || $option=="DEAT_PLAC") {
		$filter=$legend;
		$legend=WT_Gedcom_Tag::getLabel(substr($option, 0, 4))." @ ".$legend;
	}
	if ($legend == "") $legend = WT_I18N::translate('Individuals');
	if (isset($WT_IMAGES["indis"])) $legend = "<img src=\"".$WT_IMAGES["indis"]."\" alt=\"\" align=\"middle\" /> ".$legend;
	echo '<fieldset><legend>', $legend, '</legend>';
	$table_id = 'ID'.floor(microtime()*1000000); // sorttable requires a unique ID
	echo '<div id="', $table_id, '-table" class="center">';
	//-- filter buttons
	echo "<button type=\"button\" class=\"SEX_M\" title=\"", WT_I18N::translate('Show only males.'), "\" >";
	echo WT_Person::sexImage('M', 'large'), "&nbsp;</button> ";
	echo "<button type=\"button\" class=\"SEX_F\" title=\"", WT_I18N::translate('Show only females.'), "\" >";
	echo WT_Person::sexImage('F', 'large'), "&nbsp;</button> ";
	echo "<button type=\"button\" class=\"SEX_U\" title=\"", WT_I18N::translate('Show only persons of whom the gender is not known.'), "\" >";
	echo WT_Person::sexImage('U', 'large'), "&nbsp;</button> ";
	echo " <input type=\"text\" size=\"4\" id=\"aliveyear\" value=\"", date('Y'), "\" /> ";
	echo "<button type=\"button\" class=\"alive_in_year\" title=\"", WT_I18N::translate('Show persons alive in the indicated year.'), "\" >";
	echo WT_I18N::translate('Alive in Year'), "</button> ";
	echo "<button type=\"button\" class=\"DEAT_N\" title=\"", WT_I18N::translate('Show people who are alive or couples where both partners are alive.'), "\" >";
	echo WT_I18N::translate('Alive '), "</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y\" title=\"", WT_I18N::translate('Show people who are dead or couples where both partners are deceased.'), "\" >";
	echo WT_I18N::translate('Dead '), "</button> ";
	echo "<button type=\"button\" class=\"TREE_R\" title=\"", WT_I18N::translate('Show «roots» couples or individuals.  These people may also be called «patriarchs».  They are individuals who have no parents recorded in the database.'), "\" >";
	echo WT_I18N::translate('Roots'), "</button> ";
	echo "<button type=\"button\" class=\"TREE_L\" title=\"", WT_I18N::translate('Show «leaves» couples or individuals.  These are individuals who are alive but have no children recorded in the database.'), "\" >";
	echo WT_I18N::translate('Leaves'), "</button> ";
	echo "<br />";
	echo "<button type=\"button\" class=\"BIRT_YES\" title=\"", WT_I18N::translate('Show persons born more than 100 years ago.'), "\" >";
	echo WT_Gedcom_Tag::getLabel('BIRT'), "&gt;100</button> ";
	echo "<button type=\"button\" class=\"BIRT_Y100\" title=\"", WT_I18N::translate('Show persons born within the last 100 years.'), "\" >";
	echo WT_Gedcom_Tag::getLabel('BIRT'), "&lt;=100</button> ";
	echo "<button type=\"button\" class=\"DEAT_YES\" title=\"", WT_I18N::translate('Show people who died more than 100 years ago.'), "\" >";
	echo WT_Gedcom_Tag::getLabel('DEAT'), "&gt;100</button> ";
	echo "<button type=\"button\" class=\"DEAT_Y100\" title=\"", WT_I18N::translate('Show people who died within the last 100 years.'), "\" >";
	echo WT_Gedcom_Tag::getLabel('DEAT'), "&lt;=100</button> ";
	echo "<button type=\"button\" class=\"reset\" title=\"", WT_I18N::translate('Reset to the list defaults.'), "\" >";
	echo WT_I18N::translate('Reset'), "</button> ";
	//-- table header
	echo '<table id="', $table_id, '" class="sortable list_table">';
	echo '<thead><tr>';
	echo '<td></td>';
	echo '<th class="list_label"><a href="javascript:;" onclick="sortByOtherCol(this, 2)">', WT_Gedcom_Tag::getLabel('NAME'), '</a></th>';
	echo '<th class="list_label" style="display:none">GIVN</th>';
	echo '<th class="list_label" style="display:none">SURN</th>';
	if ($option=="sosa") echo '<th class="list_label">', /* I18N: Abbreviation for "Sosa-Stradonitz number".  This is a person's surname, so may need transliterating into non-latin alphabets. */ WT_I18N::translate('Sosa'), '</th>';
	echo "<th class=\"list_label\">", WT_Gedcom_Tag::getLabel('BIRT'), "</th>";
	if ($tiny) echo "<td class=\"list_label\"><img src=\"".$WT_IMAGES["reminder"]."\" alt=\"", WT_I18N::translate('Anniversary'), "\" title=\"", WT_I18N::translate('Anniversary'), "\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">", WT_Gedcom_Tag::getLabel('PLAC'), "</th>";
	if ($tiny) echo "<th class=\"list_label\"><img src=\"".$WT_IMAGES["children"]."\" alt=\"", WT_I18N::translate('Children'), "\" title=\"", WT_I18N::translate('Children'), "\" border=\"0\" /></th>";
	echo "<th class=\"list_label\">", WT_Gedcom_Tag::getLabel('DEAT'), "</th>";
	if ($tiny) echo "<td class=\"list_label\"><img src=\"".$WT_IMAGES["reminder"]."\" alt=\"", WT_I18N::translate('Anniversary'), "\" title=\"", WT_I18N::translate('Anniversary'), "\" border=\"0\" /></td>";
	echo "<th class=\"list_label\">", WT_Gedcom_Tag::getLabel('AGE'), "</th>";
	echo "<th class=\"list_label\">", WT_Gedcom_Tag::getLabel('PLAC'), "</th>";
	if ($tiny && $SHOW_LAST_CHANGE) echo "<th class=\"list_label rela\">", WT_Gedcom_Tag::getLabel('CHAN'), "</th>";
	echo "<th class=\"list_label\" style=\"display:none\">SEX</th>";
	echo "<th class=\"list_label\" style=\"display:none\">BIRT</th>";
	echo "<th class=\"list_label\" style=\"display:none\">DEAT</th>";
	echo "<th class=\"list_label\" style=\"display:none\">TREE</th>";
	echo "</tr></thead>";
	//-- table body
	echo "<tbody>";
	$n = 0;
	$d100y=new WT_Date(date('Y')-100);  // 100 years ago
	$dateY = date("Y");
	$unique_indis=array(); // Don't double-count indis with multiple names.
	foreach ($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$person=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$person = WT_Person::getInstance($value);
		} else { // Array of search results
			$gid = $key;
			if (isset($value["gid"])) $gid = $value["gid"]; // from indilist
			if (isset($value[4])) $gid = $value[4]; // from indilist ALL
			$person = WT_Person::getInstance($gid);
		}
		/* @var $person Person */
		if (is_null($person)) continue;
		if ($person->getType() !== "INDI") continue;
		if (!$person->canDisplayName()) {
			continue;
		}
		//-- place filtering
		if ($option=="BIRT_PLAC" && strstr($person->getBirthPlace(), $filter)===false) continue;
		if ($option=="DEAT_PLAC" && strstr($person->getDeathPlace(), $filter)===false) continue;
		//-- Counter
		echo '<tr>';
		echo '<td class="rela list_item">', ++$n, '</td>';
		//-- Indi name(s)
		$tdclass = 'list_value_wrap';
		if (!$person->isDead()) $tdclass .= ' alive';
		if (!$person->getChildFamilies()) $tdclass .= ' patriarch';
		echo '<td class="', $tdclass, '" align="', get_align($person->getFullName()), '">';
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
			if ($name['type']!='NAME') {
				$title='title="'.WT_Gedcom_Tag::getLabel($name['type'], $person).'"';
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
			echo '<a ', $title, ' href="', $person->getHtmlUrl(), '" class="', $class, '">', highlight_search_hits($name['full']), '</a>', $sex_image, '<br/>';
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
				'relationship.php?pid1=', $datalist[1], '&amp;pid2=', $person->getXref(),
				'" title="', WT_I18N::translate('Relationships'), '"',
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
			if ($birth_dates[0]->gregorianYear()>=1550 && $birth_dates[0]->gregorianYear()<2030 && !isset($unique_indis[$person->getXref()])) {
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
			$birth_dates[0]=new WT_Date('');
		}
		echo '</td>';
		//-- Birth anniversary
		if ($tiny) {
			echo '<td class="rela">';
			$bage =WT_Date::GetAgeYears($birth_dates[0]);
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
					echo '<a href="', get_place_url($birth_place), '" class="list_item" title="', $birth_place, '">';
					echo highlight_search_hits(get_place_short($birth_place)), '</a>';
					echo '</div>';
				}
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Number of children
		if ($tiny) {
			echo '<td class="list_value_wrap">';
			echo '<a href="', $person->getHtmlUrl(), '" class="list_item" name="', $person->getNumberOfChildren(), '">', $person->getNumberOfChildren(), '</a>';
			echo '</td>';
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
			if ($death_dates[0]->gregorianYear()>=1550 && $death_dates[0]->gregorianYear()<2030 && !isset($unique_indis[$person->getXref()])) {
				$deat_by_decade[floor($death_dates[0]->gregorianYear()/10)*10] .= $person->getSex();
			}
		} else {
			$death_date=$person->getEstimatedDeathDate();
			$death_jd=$death_date->JD();
			if ($SHOW_EST_LIST_DATES) {
				echo '<div>', str_replace('<a', '<a name="'.$death_jd.'"', $death_date->Display(!$SEARCH_SPIDER)), '</div>';
			} else if ($person->isDead()) {
				echo '<div>', WT_I18N::translate('Yes'), '<a name="9d', $death_jd, '"></a></div>';
			} else {
				echo '<span class="date"><a name="', $death_jd, '">&nbsp;</span>'; // span needed for alive-in-year filter
			}
			$death_dates[0]=new WT_Date('');
		}
		echo "</td>";
		//-- Death anniversary
		if ($tiny) {
			echo '<td class="rela">';
			if ($death_dates[0]->isOK())
				echo '<span class="age">', WT_Date::GetAgeYears($death_dates[0]), '</span>';
			else
				echo '&nbsp;';
			echo '</td>';
		}
		//-- Age at death
		echo "<td class=\"list_value_wrap\">";
		if ($birth_dates[0]->isOK() && $death_dates[0]->isOK()) {
			$age = WT_Date::GetAgeYears($birth_dates[0], $death_dates[0]);
			$age_jd = $death_dates[0]->MinJD()-$birth_dates[0]->MinJD();
			echo '<a name="', $age_jd, '" class="list_item age">', $age, '</a>';
			if (!isset($unique_indis[$person->getXref()])) {
				$deat_by_age[max(0, min($max_age, $age))] .= $person->getSex();
			}
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
					echo '<a href="', get_place_url($death_place), '" class="list_item" title="', $death_place, '">';
					echo highlight_search_hits(get_place_short($death_place)), '</a>';
					echo '</div>';
				}
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Last change
		if ($tiny && $SHOW_LAST_CHANGE) {
			echo '<td class="rela">', $person->LastChangeTimestamp(empty($SEARCH_SPIDER)), '</td>';
		}
		//-- Sorting by gender
		echo '<td style="display:none">';
		echo $person->getSex();
		echo '</td>';
		//-- Filtering by birth date
		echo '<td style="display:none">';
		if (!$person->canDisplayDetails() || WT_Date::Compare($birth_dates[0], $d100y)>0) {
			echo 'Y100';
		} else {
			echo 'YES';
		}
		echo '</td>';
		//-- Filtering by death date
		echo '<td style="display:none">';
		if ($person->isDead()) {
			if (WT_Date::Compare($death_dates[0], $d100y)>0) {
				echo 'Y100';
			} else {
				echo 'YES';
			}
		} else {
			echo 'N';
		}
		echo '</td>';
		//-- Roots or Leaves ?
		echo '<td style="display:none">';
		if (!$person->getChildFamilies()) {
			echo 'R'; // roots
		} elseif (!$person->isDead() && $person->getNumberOfChildren()<1) {
			echo 'L'; // leaves
		}
		echo '</td>';
		echo '</tr>', "\n";
		$unique_indis[$person->getXref()]=true;
	}
	echo '</tbody>', "\n";
	//-- table footer
	echo "<tfoot><tr class=\"sortbottom\">";
	echo "<td></td>";
	echo "<td class=\"list_label\">"; // NAME
	if (count($unique_indis)>1) {
		echo '<a href="javascript:;" onclick="sortByOtherCol(this, 1)"><img src="images/topdown.gif" alt="" border="0" /> ', WT_Gedcom_Tag::getLabel('GIVN'), '</a><br />';
	}
	echo "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', 'parents_$table_id');\" /><label for=\"cb_parents_$table_id\">", WT_I18N::translate('Show parents'), "</label><br />";
	echo WT_I18N::translate('Total individuals: %s', WT_I18N::number(count($unique_indis)));
	if ($n!=count($unique_indis)) {
		echo '<br/>', WT_I18N::translate('Total surnames: %s', WT_I18N::number($n));
	}
	echo "</td>";
	echo "<td style=\"display:none\">GIVN</td>";
	echo "<td style=\"display:none\">SURN</td>";
	if ($option=="sosa") echo "<td></td>"; // SOSA
	echo "<td></td>"; // BIRT:DATE
	if ($tiny) echo "<td></td>"; // BIRT:Reminder
	echo "<td></td>"; // BIRT:PLAC
	if ($tiny) echo "<td></td>"; // Children
	echo "<td class=\"list_label\" colspan=\"3\">";
	echo "<input id=\"charts_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', '$table_id-charts');\" /><label for=\"charts_$table_id\">", WT_I18N::translate('Show statistics charts'), "</label></td>"; //DEAT:DATE, DEAT:Reminder, DEAT:AGE
	echo "<td></td>"; // DEAT:PLAC
	if ($tiny && $SHOW_LAST_CHANGE) echo "<td></td>"; // CHAN
	echo "<td style=\"display:none\">SEX</td>";
	echo "<td style=\"display:none\">BIRT</td>";
	echo "<td style=\"display:none\">DEAT</td>";
	echo "<td style=\"display:none\">TREE</td>";
	echo '</tr>', "\n";
	echo "</tfoot>";
	echo "</table>", "\n";
	echo "</div>";
	//-- charts
	echo "<div class=\"", $table_id, "-charts\" style=\"display:none\">";
	echo "<table class=\"list_table center\">";
	echo "<tr><td class=\"list_value_wrap\">";
	print_chart_by_decade($birt_by_decade, WT_I18N::translate('Decade of birth'));
	echo "</td><td class=\"list_value_wrap\">";
	print_chart_by_decade($deat_by_decade, WT_I18N::translate('Decade of death'));
	echo "</td></tr><tr><td colspan=\"2\" class=\"list_value_wrap\">";
	print_chart_by_age($deat_by_age, WT_I18N::translate('Age related to death year'));
	echo "</td></tr></table>";
	echo "</div>";
	echo "</fieldset>";
}

/**
 * print a sortable table of families
 *
 * @param array $datalist contain families that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_fam_table($datalist, $legend='', $option='') {
	global $GEDCOM, $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER;

	if ($option=='BIRT_PLAC' || $option=='DEAT_PLAC') return;
	if (count($datalist)<1) return;
	$tiny = (count($datalist)<=500);
	require_once WT_ROOT.'js/sorttable.js.htm';
	$stats = new WT_Stats($GEDCOM);
	$max_age = max($stats->oldestMarriageMaleAge(), $stats->oldestMarriageFemaleAge())+1;
	//-- init chart data
	for ($age=0; $age<=$max_age; $age++) $marr_by_age[$age]='';
	for ($year=1550; $year<2030; $year+=10) $birt_by_decade[$year]='';
	for ($year=1550; $year<2030; $year+=10) $marr_by_decade[$year]='';
	//-- fieldset
	if ($option=='MARR_PLAC') {
		$filter=$legend;
		$legend=WT_Gedcom_Tag::getLabel('MARR').' @ '.$legend;
	}
	if ($legend == "") $legend = WT_I18N::translate('Families');
	$legend = '<img src="'.$WT_IMAGES['sfamily'].'" alt="" align="middle" /> '.$legend;
	echo '<fieldset><legend>', $legend, '</legend>';
	$table_id = 'ID'.floor(microtime()*1000000); // sorttable requires a unique ID
	echo '<div id="', $table_id, '-table" class="center">';
	//-- filter buttons
	echo '<button type="button" class="DEAT_N" title="', WT_I18N::translate('Show people who are alive or couples where both partners are alive.'), '" >';
	echo WT_I18N::translate('Both alive '), '</button> ';
	echo '<button type="button" class="DEAT_W" title="', WT_I18N::translate('Show couples where only the female partner is deceased.'), '" >';
	echo WT_I18N::translate('Widower'), '</button> ';
	echo '<button type="button" class="DEAT_H" title="', WT_I18N::translate('Show couples where only the male partner is deceased.'), '" >';
	echo WT_I18N::translate('Widow'), '</button> ';
	echo '<button type="button" class="DEAT_Y" title="', WT_I18N::translate('Show people who are dead or couples where both partners are deceased.'), '" >';
	echo WT_I18N::translate('Both dead '), '</button> ';
	echo '<button type="button" class="TREE_R" title="', WT_I18N::translate('Show «roots» couples or individuals.  These people may also be called «patriarchs».  They are individuals who have no parents recorded in the database.'), '" >';
	echo WT_I18N::translate('Roots'), '</button> ';
	echo '<button type="button" class="TREE_L" title="', WT_I18N::translate('Show «leaves» couples or individuals.  These are individuals who are alive but have no children recorded in the database.'), '" >';
	echo WT_I18N::translate('Leaves'), '</button> ';
	echo '<br />';
	echo '<button type="button" class="MARR_U" title="', WT_I18N::translate('Show couples with an unknown marriage date.'), '" >';
	echo WT_Gedcom_Tag::getLabel('MARR'), ' ?</button> ';
	echo '<button type="button" class="MARR_YES" title="', WT_I18N::translate('Show couples who married more than 100 years ago.'), '" >';
	echo WT_Gedcom_Tag::getLabel('MARR'), '&gt;100</button> ';
	echo '<button type="button" class="MARR_Y100" title="', WT_I18N::translate('Show couples who married within the last 100 years.'), '" >';
	echo WT_Gedcom_Tag::getLabel('MARR'), '&lt;=100</button> ';
	echo '<button type="button" class="MARR_DIV" title="', WT_I18N::translate('Show divorced couples.'), '" >';
	echo WT_Gedcom_Tag::getLabel('DIV'), '</button> ';
	echo '<button type="button" class="reset" title="', WT_I18N::translate('Reset to the list defaults.'), '" >';
	echo WT_I18N::translate('Reset'), '</button> ';
	//-- table header
	echo '<table id="', $table_id, '" class="sortable list_table center">';
	echo '<thead><tr>';
	echo '<td></td>';
	echo '<th class="list_label"><a href="javascript:;" onclick="sortByOtherCol(this, 2)">', WT_Gedcom_Tag::getLabel('NAME'), '</a></th>';
	echo '<th style="display:none">HUSB:GIVN_SURN</th>';
	echo '<th style="display:none">HUSB:SURN_GIVN</th>';
	echo '<th class="list_label">', WT_Gedcom_Tag::getLabel('AGE'), '</th>';
	echo '<th class="list_label"><a href="javascript:;" onclick="sortByOtherCol(this, 2)">', WT_Gedcom_Tag::getLabel('NAME'), '</a></th>';
	echo '<th style="display:none">WIFE:GIVN_SURN</th>';
	echo '<th style="display:none">WIFE:SURN_GIVN</th>';
	echo '<th class="list_label">', WT_Gedcom_Tag::getLabel('AGE'), '</th>';
	echo '<th class="list_label">', WT_Gedcom_Tag::getLabel('MARR'), '</th>';
	if ($tiny) echo '<td class="list_label"><img src="', $WT_IMAGES['reminder'], '" alt="', WT_I18N::translate('Anniversary'), '" title="', WT_I18N::translate('Anniversary'), '" border="0" /></td>';
	echo '<th class="list_label">', WT_Gedcom_Tag::getLabel('PLAC'), '</th>';
	if ($tiny) echo '<th class="list_label"><img src="', $WT_IMAGES['children'], '" alt="', WT_I18N::translate('Children'), '" title="', WT_I18N::translate('Children'), '" border="0" /></th>';
	if ($tiny && $SHOW_LAST_CHANGE) echo'<th class="list_label rela">', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	echo '<th style="display:none">MARR</th>';
	echo '<th style="display:none">DEAT</th>';
	echo '<th style="display:none">TREE</th>';
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$num = 0;
	$d100y=new WT_Date(date('Y')-100);  // 100 years ago
	foreach ($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$family=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$family=WT_Family::getInstance($value);
		} else { // Array of search results
			$gid = "";
			if (isset($value['gid'])) $gid = $value['gid'];
			if (isset($value['gedcom'])) $family = new WT_Family($value['gedcom']);
			else $family = WT_Family::getInstance($gid);
		}
		if (is_null($family)) continue;
		if ($family->getType() !== 'FAM') continue;
		//-- Retrieve husband and wife
		$husb = $family->getHusband();
		if (is_null($husb)) $husb = new WT_Person('');
		$wife = $family->getWife();
		if (is_null($wife)) $wife = new WT_Person('');
		if (!$family->canDisplayDetails()) {
			continue;
		}
		//-- place filtering
		if ($option=='MARR_PLAC' && strstr($family->getMarriagePlace(), $filter)===false) continue;
		//-- Counter
		echo '<tr>';
		echo '<td class="rela list_item">', ++$num, '</td>';
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
		$tdclass = 'list_value_wrap';
		if (!$husb->isDead()) $tdclass .= ' alive';
		if (!$husb->getChildFamilies()) $tdclass .= ' patriarch';
		echo '<td class="', $tdclass, '" align="', get_align($names[$n1]['full']), '">';
		echo '<a href="', $family->getHtmlUrl(), '" class="list_item name2" dir="', $TEXT_DIRECTION, '">', highlight_search_hits($names[$n1]['full']), '</a>';
		if ($tiny) echo $husb->getSexImage();
		if ($n1!=$n2) {
			echo '<br /><a href="', $family->getHtmlUrl(), '" class="list_item">', highlight_search_hits($names[$n2]['full']), '</a>';
		}
		// Husband parents
		echo $husb->getPrimaryParentsNames('parents_'.$table_id.' details1', 'none');
		echo '</td>';
		//-- Husb GIVN
		list($surn, $givn)=explode(',', $husb->getSortName());
		echo '<td style="display:none">', $givn, ',', $surn, '</td>';
		echo '<td style="display:none">', $surn, ',', $givn, '</td>';
		$mdate=$family->getMarriageDate();
		//-- Husband age
		echo '<td class="list_value_wrap">';
		$hdate=$husb->getBirthDate();
		if ($hdate->isOK()) {
			if ($hdate->gregorianYear()>=1550 && $hdate->gregorianYear()<2030) {
				$birt_by_decade[floor($hdate->gregorianYear()/10)*10] .= $husb->getSex();
			}
			if ($mdate->isOK()) {
				$hage=WT_Date::GetAgeYears($hdate, $mdate);
				$hage_jd = $mdate->MinJD()-$hdate->MinJD();
				echo '<a name="', $hage_jd, '" class="list_item age">', $hage, '</a>';
				$marr_by_age[max(0, min($max_age, $hage))] .= $husb->getSex();
			} else {
				echo '&nbsp;';
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
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
		$tdclass = 'list_value_wrap';
		if (!$wife->isDead()) $tdclass .= ' alive';
		if (!$wife->getChildFamilies()) $tdclass .= ' patriarch';
		echo '<td class="', $tdclass, '" align="', get_align($names[$n1]['full']), '">';
		echo '<a href="', $family->getHtmlUrl(), '" class="list_item name2" dir="', $TEXT_DIRECTION, '">', highlight_search_hits($names[$n1]['full']), '</a>';
		if ($tiny) echo $wife->getSexImage();
		if ($n1!=$n2) {
			echo '<br /><a href="', $family->getHtmlUrl(), '" class="list_item">', highlight_search_hits($names[$n2]['full']), '</a>';
		}
		// Wife parents
		echo $wife->getPrimaryParentsNames('parents_'.$table_id.' details1', 'none');
		echo '</td>';
		//-- Wife GIVN
		list($surn, $givn)=explode(',', $wife->getSortName());
		echo '<td style="display:none">', $givn, ',', $surn, '</td>';
		echo '<td style="display:none">', $surn, ',', $givn, '</td>';
		$mdate=$family->getMarriageDate();
		//-- Wife age
		echo '<td class="list_value_wrap">';
		$wdate=$wife->getBirthDate();
		if ($wdate->isOK()) {
			if ($wdate->gregorianYear()>=1550 && $wdate->gregorianYear()<2030) {
				$birt_by_decade[floor($wdate->gregorianYear()/10)*10] .= $wife->getSex();
			}
			if ($mdate->isOK()) {
				$wage=WT_Date::GetAgeYears($wdate, $mdate);
				$wage_jd = $mdate->MinJD()-$wdate->MinJD();
				echo '<a name="', $wage_jd, '" class="list_item age">', $wage, '</a>';
				$marr_by_age[max(0, min($max_age, $wage))] .= $wife->getSex();
			} else {
				echo '&nbsp;';
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Marriage date
		echo '<td class="list_value_wrap">';
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
		} else if (get_sub_record(1, '1 _NMR', $family->getGedcomRecord())) {
			$hus = $family->getHusband();
			$wif = $family->getWife();
			if (empty($wif) && !empty($hus)) echo WT_Gedcom_Tag::getLabel('_NMR', $hus);
			else if (empty($hus) && !empty($wif)) echo WT_Gedcom_Tag::getLabel('_NMR', $wif);
			else echo WT_Gedcom_Tag::getLabel('_NMR');
		} else if (get_sub_record(1, '1 _NMAR', $family->getGedcomRecord())) {
			$hus = $family->getHusband();
			$wif = $family->getWife();
			if (empty($wif) && !empty($hus)) echo WT_Gedcom_Tag::getLabel('_NMAR', $hus);
			else if (empty($hus) && !empty($wif)) echo WT_Gedcom_Tag::getLabel('_NMAR', $wif);
			else echo WT_Gedcom_Tag::getLabel('_NMAR');
		} else {
			$factdetail = explode(' ', trim($family->getMarriageRecord()));
			if (isset($factdetail)) {
				if (count($factdetail) >= 3) {
					if (strtoupper($factdetail[2]) != "N")
						echo '<div>', WT_I18N::translate('Yes'), '<a name="9999998"></a></div>';
					else
						echo '<div>', WT_I18N::translate('No'), '<a name="9999999"></a></div>';
				}
				else echo '&nbsp;';
			}
		}
		echo '</td>';
		//-- Marriage anniversary
		if ($tiny) {
			echo '<td class="rela">';
			$mage=WT_Date::GetAgeYears($mdate);
			if (empty($mage)) echo '&nbsp;';
			else echo '<span class="age">', $mage, '</span>';
			echo '</td>';
		}
		//-- Marriage place
		echo '<td class="list_value_wrap">';
		if ($marriage_places=$family->getAllMarriagePlaces()) {
			foreach ($marriage_places as $marriage_place) {
				if ($SEARCH_SPIDER) {
					echo get_place_short($marriage_place), ' ';
				} else {
					echo '<div align="', get_align($marriage_place), '">';
					echo '<a href="', get_place_url($marriage_place), '" class="list_item" title="', $marriage_place, '">';
					echo highlight_search_hits(get_place_short($marriage_place)), '</a>';
					echo '</div>';
				}
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
		//-- Number of children
		if ($tiny) {
			echo '<td class="list_value_wrap">';
			echo '<a href="', $family->getHtmlUrl(), '" class="list_item" name="', $family->getNumberOfChildren(), '">', $family->getNumberOfChildren(), '</a>';
			echo "</td>";
		}
		//-- Last change
		if ($tiny && $SHOW_LAST_CHANGE)
			echo '<td class="rela">', $family->LastChangeTimestamp(empty($SEARCH_SPIDER)), '</td>';
		//-- Sorting by marriage date
		echo '<td style="display:none">';
		if (!$family->canDisplayDetails() || !$mdate->isOK()) {
			echo 'U';
		} else {
			if (WT_Date::Compare($mdate, $d100y)>0) {
				echo 'Y100';
			} else {
				echo 'YES';
			}
		}
		if ($family->isDivorced())
			echo ' DIV';
		echo '</td>';
		//-- Sorting alive/dead
		echo '<td style="display:none">';
		if ($husb->isDead() && $wife->isDead()) echo 'Y';
		if ($husb->isDead() && !$wife->isDead()) {
			if ($wife->getSex()=='F') echo 'H';
			if ($wife->getSex()=='M') echo 'W'; // male partners
		}
		if (!$husb->isDead() && $wife->isDead()) {
			if ($husb->getSex()=='M') echo 'W';
			if ($husb->getSex()=='F') echo 'H'; // female partners
		}
		if (!$husb->isDead() && !$wife->isDead()) echo 'N';
		echo '</td>';
		//-- Roots or Leaves
		echo '<td style="display:none">';
		if (!$husb->getChildFamilies() && !$wife->getChildFamilies()) {
			echo 'R'; // roots
		} elseif (!$husb->isDead() && !$wife->isDead() && $family->getNumberOfChildren()<1) {
			echo 'L'; // leaves
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	//-- table footer
	echo '<tfoot><tr class="sortbottom">';
	echo '<td></td>';
	echo '<td class="list_label">'; // HUSB:NAME
	if ($num>1) {
		echo '<a href="javascript:;" onclick="sortByOtherCol(this, 1)"><img src="images/topdown.gif" alt="" border="0" /> ', WT_Gedcom_Tag::getLabel('GIVN'), '</a><br />';
	}
	echo "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', 'parents_$table_id');\" /><label for=\"cb_parents_$table_id\">", WT_I18N::translate('Show parents'), '</label><br />';
	echo  /* I18N: A count of families */ WT_I18N::translate('Total families: %s', WT_I18N::number($num));
	echo '</td>';
	echo '<td style="display:none">HUSB:GIVN</td>';
	echo '<td></td>'; // HUSB:AGE
	echo '<td class="list_label" style="vertical-align: top;">'; // WIFE:NAME
	echo '<a href="javascript:;" onclick="sortByOtherCol(this, 2)"><img src="images/topdown.gif" alt="" border="0" /> ', WT_Gedcom_Tag::getLabel('GIVN'), '</a><br />';
	echo '</td>';
	echo '<td style="display:none">WIFE:GIVN</td>';
	echo '<td></td>'; // WIFE:AGE
	echo '<td class="list_label" colspan="3">';
	echo "<input id=\"charts_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', '$table_id-charts');\" /><label for=\"charts_$table_id\">", WT_I18N::translate('Show statistics charts'), '</label></td>'; // MARR:DATE, MARR:Reminder, MARR:PLAC
	if ($tiny) echo '<td></td>'; // FAM:ChildrenCount
	if ($tiny && $SHOW_LAST_CHANGE) echo '<td></td>'; // FAM:CHAN
	echo '<td style="display:none">MARR</td>';
	echo '<td style="display:none">DEAT</td>';
	echo '<td style="display:none">TREE</td>';
	echo '</tr></tfoot>';
	echo '</table>';
	echo '</div>';
	//-- charts
	echo '<div class="', $table_id, '-charts" style="display:none">';
	echo '<table class="list_table center">';
	echo '<tr><td class="list_value_wrap">';
	print_chart_by_decade($birt_by_decade, WT_I18N::translate('Decade of birth'));
	echo '</td><td class="list_value_wrap">';
	print_chart_by_decade($marr_by_decade, WT_I18N::translate('Decade of marriage'));
	echo '</td></tr><tr><td colspan="2" class="list_value_wrap">';
	print_chart_by_age($marr_by_age, WT_I18N::translate('Age in year of marriage'));
	echo '</td></tr></table>';
	echo '</div>';
	echo '</fieldset>';
}

/**
 * print a sortable table of sources
 *
 * @param array $datalist contain sources that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_sour_table($datalist, $legend=null) {
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES;

	$table_id = "ID".floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	echo WT_JS_START;?>
	jQuery(document).ready(function(){
		jQuery('#<?php echo $table_id; ?>').dataTable( {
			"sDom": '<"H"pl<"dt-clear">irf>t<"F"pl>',
			"oLanguage": {
				"sLengthMenu": '<?php echo /* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value="10">10<option value="20">20</option><option value="30">30</option><option value="50">50</option><option value="100">100</option><option value="-1">'.WT_I18N::translate('All').'</option></select>'); ?>',
				"sZeroRecords": '<?php echo WT_I18N::translate('No records to display');?>',
				"sInfo": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_'); ?>',
				"sInfoEmpty": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0'); ?>',
				"sInfoFiltered": '<?php echo /* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_'); ?>',
				"sProcessing": '<?php echo WT_I18N::translate('Loading...');?>',
				"sSearch": '<?php echo WT_I18N::translate('Search');?>',
				"oPaginate": {
					"sFirst":    '<?php echo /* I18N: button label, first page    */ WT_I18N::translate('first');    ?>',
					"sLast":     '<?php echo /* I18N: button label, last page     */ WT_I18N::translate('last');     ?>',
					"sNext":     '<?php echo /* I18N: button label, next page     */ WT_I18N::translate('next');     ?>',
					"sPrevious": '<?php echo /* I18N: button label, previous page */ WT_I18N::translate('previous'); ?>'
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"bProcessing": true,
			"bStateSave": true,
			"aoColumnDefs": [
				{"bSortable": false, "aTargets": [ 8 ]},
				{"sType": "numeric", "aTargets": [3, 4, 5, 6]}
			],
			"iDisplayLength": 20,
			"sPaginationType": "full_numbers"
	   });
	   	jQuery("#source-list").css('visibility', 'visible');
	});
	<?php echo WT_JS_END;
	//--table wrapper
	echo '<div id="source-list">';
	echo '<fieldset><legend><img src="', $WT_IMAGES['source'], '" align="middle" alt="" /> ';
	if ($legend) {
		echo $legend;
	} else {
		echo WT_I18N::translate('Sources');
	}
	echo '</legend>';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_Gedcom_Tag::getLabel('TITL'), '</th>';
	echo '<th class="t2" style="display:none;">', WT_Gedcom_Tag::getLabel('TITL'), ' 2</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('AUTH'), '</th>';
	echo '<th>', WT_I18N::translate('Individuals'), '</th>';
	echo '<th>', WT_I18N::translate('Families'), '</th>';
	echo '<th>', WT_I18N::translate('Media objects'), '</th>';
	echo '<th>', WT_I18N::translate('Shared notes'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	} else {
		echo '<th style="display:none;"></th>';
	}
	if (WT_USER_GEDCOM_ADMIN) {
		echo '<th>&nbsp;</th>';//delete
	} else {
		echo '<th style="display:none;"></th>';
	}
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$t2=false;
	$n=0;
	foreach ($datalist as $key=>$value) {
		if (is_object($value)) { // Array of objects
			$source=$value;
		} elseif (!is_array($value)) { // Array of IDs
			$source=WT_Source::getInstance($key); // from placelist
			if (is_null($source)) {
				$source=WT_Source::getInstance($value);
			}
			unset($value);
		} else { // Array of search results
			$gid='';
			if (isset($value['gid'])) {
				$gid=$value['gid'];
			}
			if (isset($value['gedcom'])) {
				$source=new WT_Source($value['gedcom']);
			} else {
				$source=WT_Source::getInstance($gid);
			}
		}
		if (!$source || !$source->canDisplayDetails()) {
			continue;
		}
		$link_url=$source->getHtmlUrl();
		echo '<tr>';
		//-- Source name(s)
		$tmp=$source->getFullName();
		echo '<td align="', get_align($tmp), '"><a href="', $link_url, '">', highlight_search_hits($tmp), '</a></td>';
		// alternate title in a new column
		$tmp=$source->getAddName();
		if ($tmp) {
			echo '<td class="t2" style="display:none;" align="', get_align($tmp), '"><a href="', $link_url, '">', highlight_search_hits($tmp), '</a></td>';
			$t2=true;
		} else {
			echo '<td class="t2" style="display:none;">&nbsp;</td>';
		}
		//-- Author
		$tmp=$source->getAuth();
		if ($tmp) {
			echo '<td align="', get_align($tmp), '">', highlight_search_hits(htmlspecialchars($tmp)), '</td>';
		} else {
			echo '<td>&nbsp;</td>';
		}
		//-- Linked INDIs
		$tmp=$source->countLinkedIndividuals();
		echo '<td>', $tmp, '</td>';
		//-- Linked FAMs
		$tmp=$source->countLinkedfamilies();
		echo '<td>', $tmp, '</td>';
		//-- Linked OBJEcts
		$tmp=$source->countLinkedMedia();
		echo '<td>', $tmp, '</td>';
		//-- Linked NOTEs
		$tmp=$source->countLinkedNotes();
		echo '<td>', $tmp, '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>'.$source->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
		} else {
			echo '<td style="display:none;"></td>';
		}
		//-- Delete 
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<td><div title="', WT_I18N::translate('Delete'), '" class="deleteicon" onclick="if (confirm(\'', addslashes(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($source->getFullName()))), '\')) return delete_source(\'', $source->getXref(),'\'); else return false;"><span class="link_text">', WT_I18N::translate('Delete'), '</span></div></td>';
		} else {
			echo '<td style="display:none;"></td>';
		}
		echo "</tr>";
	}
	echo '</tbody>';
	echo '</table></fieldset>';
	echo '</div>';
	// show TITLE2 col if not empty
	if ($t2) {
		echo <<< T2
		<script type="text/javascript">
			var table = document.getElementById("#source_list_table");
			cells = table.getElementsByTagName('td, th');
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
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES;

	if (count($datalist)<1) {
		return;
	}
	$table_id = "ID".floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	echo WT_JS_START;?>
	jQuery(document).ready(function(){
		jQuery('#<?php echo $table_id; ?>').dataTable( {
			"sDom": '<"H"prf>t<"F"li>',
			"oLanguage": {
				"sLengthMenu": '<?php echo /* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value="10">10<option value="20">20</option><option value="30">30</option><option value="50">50</option><option value="100">100</option><option value="-1">'.WT_I18N::translate('All').'</option></select>'); ?>',
				"sZeroRecords": '<?php echo WT_I18N::translate('No records to display');?>',
				"sInfo": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_'); ?>',
				"sInfoEmpty": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0'); ?>',
				"sInfoFiltered": '<?php echo /* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_'); ?>',
				"sProcessing": '<?php echo WT_I18N::translate('Loading...');?>',
				"sSearch": '<?php echo WT_I18N::translate('Search');?>',
				"oPaginate": {
					"sFirst":    '<?php echo /* I18N: button label, first page    */ WT_I18N::translate('first');    ?>',
					"sLast":     '<?php echo /* I18N: button label, last page     */ WT_I18N::translate('last');     ?>',
					"sNext":     '<?php echo /* I18N: button label, next page     */ WT_I18N::translate('next');     ?>',
					"sPrevious": '<?php echo /* I18N: button label, previous page */ WT_I18N::translate('previous'); ?>'
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"bProcessing": true,
			"bStateSave": true,
			"aoColumnDefs": [
				{"bSortable": false, "aTargets": [ 6 ]},
				{"sType": "numeric", "aTargets": [1, 2, 3, 4]}
			],
			"iDisplayLength": 20,
			"sPaginationType": "full_numbers"
	   });
	   	jQuery("#note-list").css('visibility', 'visible');
	});
	<?php echo WT_JS_END;
	//--table wrapper
	echo '<div id="note-list">';
	echo '<fieldset><legend><img src="', $WT_IMAGES['note'], '" align="middle" alt="" /> ';
	if ($legend) {
		echo $legend;
	} else {
		echo WT_I18N::translate('Shared notes');
	}
	echo '</legend>';
	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_Gedcom_Tag::getLabel('TITL'), '</th>';
	echo '<th>', WT_I18N::translate('Individuals'), '</th>';
	echo '<th>', WT_I18N::translate('Families'), '</th>';
	echo '<th>', WT_I18N::translate('Media objects'), '</th>';
	echo '<th>', WT_I18N::translate('Sources'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	} else {
		echo '<th style="display:none;"></th>';
	}
	if (WT_USER_GEDCOM_ADMIN) {
		echo '<th>&nbsp;</th>';//delete
	} else {
		echo '<th style="display:none;"></th>';
	}
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$n=0;
	foreach ($datalist as $note) {
		if (!$note->canDisplayDetails()) {
			continue;
		}
		echo '<tr>';
		$link_url=$note->getHtmlUrl();
		//-- Shared Note name(s)
		$tmp=$note->getFullName();
		echo '<td align="', get_align($tmp), '"><a href="', $link_url, '" class="list_item name2">', highlight_search_hits($tmp), '</a></td>';
		//-- Linked INDIs
		$tmp=$note->countLinkedIndividuals();
		echo '<td>', $tmp, '</td>';
		//-- Linked FAMs
		$tmp=$note->countLinkedfamilies();
		echo '<td>', $tmp, '</td>';
		//-- Linked OBJEcts
		$tmp=$note->countLinkedMedia();
		echo '<td>', $tmp, '</td>';
		//-- Linked SOURs
		$tmp=$note->countLinkedSources();
		echo '<td>', $tmp, '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>'.$note->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
		} else {
			echo '<td style="display:none;"></td>';
		}
		//-- Delete 
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<td><div title="', WT_I18N::translate('Delete'), '" class="deleteicon" onclick="if (confirm(\'', addslashes(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($note->getFullName()))), '\')) return delete_note(\'', $note->getXref(),'\'); else return false;"><span class="link_text">', WT_I18N::translate('Delete'), '</span></div></td>';
		} else {
			echo '<td style="display:none;"></td>';
		}
		echo "</tr>";
	}
	echo '</tbody>';
	echo '</table></fieldset>';
	echo '</div>';
}

/**
 * print a sortable table of repositories
 *
 * @param array $datalist contain repositories that were extracted from the database.
 * @param string $legend optional legend of the fieldset
 */
function print_repo_table($repos, $legend='') {
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER;

	if (!$repos) {
		return;
	}
	$table_id = "ID".floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	echo WT_JS_START;?>
	jQuery(document).ready(function(){
		jQuery('#<?php echo $table_id; ?>').dataTable( {
			"sDom": '<"H"prf>t<"F"li>',
			"oLanguage": {
				"sLengthMenu": '<?php echo /* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value="10">10<option value="20">20</option><option value="30">30</option><option value="50">50</option><option value="100">100</option><option value="-1">'.WT_I18N::translate('All').'</option></select>'); ?>',
				"sZeroRecords": '<?php echo WT_I18N::translate('No records to display');?>',
				"sInfo": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_'); ?>',
				"sInfoEmpty": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0'); ?>',
				"sInfoFiltered": '<?php echo /* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_'); ?>',
				"sProcessing": '<?php echo WT_I18N::translate('Loading...');?>',
				"sSearch": '<?php echo WT_I18N::translate('Search');?>',
				"oPaginate": {
					"sFirst":    '<?php echo /* I18N: button label, first page    */ WT_I18N::translate('first');    ?>',
					"sLast":     '<?php echo /* I18N: button label, last page     */ WT_I18N::translate('last');     ?>',
					"sNext":     '<?php echo /* I18N: button label, next page     */ WT_I18N::translate('next');     ?>',
					"sPrevious": '<?php echo /* I18N: button label, previous page */ WT_I18N::translate('previous'); ?>'
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"bProcessing": true,
			"bStateSave": true,
			"aoColumnDefs": [
				{"bSortable": false, "aTargets": [ 3 ]},
				{"sType": "numeric", "aTargets": [ 1 ]}
			],
			"iDisplayLength": 20,
			"sPaginationType": "full_numbers"
	   });
	   	jQuery("#repo-list").css('visibility', 'visible');
	});
	<?php echo WT_JS_END;
	//--table wrapper
	echo '<div id="repo-list">';
	echo '<fieldset><legend><img src="', $WT_IMAGES['repository'], '" align="middle" alt="" /> ';
	if ($legend) {
		echo $legend;
	} else {
		echo WT_I18N::translate('Repositories');
	}
	echo '</legend>';

	//-- table header
	echo '<table id="', $table_id, '"><thead><tr>';
	echo '<th>', WT_I18N::translate('Repository name'), '</th>';
	echo '<th>', WT_I18N::translate('Sources'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	} else {
		echo '<th style="display:none;"></th>';
	}
	if (WT_USER_GEDCOM_ADMIN) {
		echo '<th>&nbsp;</th>';//delete
	} else {
		echo '<th style="display:none;"></th>';
	}
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$n=0;
	foreach ($repos as $repo) {
		if (!$repo->canDisplayDetails()) {
			continue;
		}
		echo '<tr>';
		//-- Repository name(s)
		$name = $repo->getFullName();
		echo '<td align="', get_align($name), '"><a href="', $repo->getHtmlUrl(), '" class="list_item name2">', highlight_search_hits(htmlspecialchars($name)), '</a>';
		$addname=$repo->getAddName();
		if ($addname) {
			echo '<br /><a href="', $repo->getHtmlUrl(), '" class="list_item">', highlight_search_hits($addname), '</a>';
		}
		echo '</td>';
		//-- Linked SOURces
		$tmp=$repo->countLinkedSources();
		echo '<td>', $tmp, '</td>';
		//-- Last change
		if ($SHOW_LAST_CHANGE) {
			echo '<td>', $repo->LastChangeTimestamp(!$SEARCH_SPIDER), '</td>';
		} else {
			echo '<td style="display:none;"></td>';
		}
		//-- Delete 
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<td><div title="', WT_I18N::translate('Delete'), '" class="deleteicon" onclick="if (confirm(\'', addslashes(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($repo->getFullName()))), '\')) return delete_repository(\'', $repo->getXref(),'\'); else return false;"><span class="link_text">', WT_I18N::translate('Delete'), '</span></div></td>';
		} else {
			echo '<td style="display:none;"></td>';
		}
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table></fieldset>';
	echo '</div>';
}

/**
 * print a sortable table of media objects
 *
 * @param array $datalist contain media objects that were extracted from the database.
 * @param string $legend legend of the fieldset
 */
function print_media_table($datalist, $legend) {
	global $SHOW_LAST_CHANGE, $TEXT_DIRECTION, $WT_IMAGES;

	if (count($datalist)<1) return;
	$table_id = "ID".floor(microtime()*1000000); // lists requires a unique ID in case there are multiple lists per page
	echo WT_JS_START;?>
	jQuery(document).ready(function(){
		jQuery('#<?php echo $table_id; ?>').dataTable( {
			"sDom": '<"H"prf>t<"F"li>',
			"oLanguage": {
				"sLengthMenu": '<?php echo /* I18N: Display %s [records per page], %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value="10">10<option value="20">20</option><option value="30">30</option><option value="50">50</option><option value="100">100</option><option value="-1">'.WT_I18N::translate('All').'</option></select>'); ?>',
				"sZeroRecords": '<?php echo WT_I18N::translate('No records to display');?>',
				"sInfo": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_'); ?>',
				"sInfoEmpty": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0'); ?>',
				"sInfoFiltered": '<?php echo /* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_'); ?>',
				"sProcessing": '<?php echo WT_I18N::translate('Loading...');?>',
				"sSearch": '<?php echo WT_I18N::translate('Search');?>',
				"oPaginate": {
					"sFirst":    '<?php echo /* I18N: button label, first page    */ WT_I18N::translate('first');    ?>',
					"sLast":     '<?php echo /* I18N: button label, last page     */ WT_I18N::translate('last');     ?>',
					"sNext":     '<?php echo /* I18N: button label, next page     */ WT_I18N::translate('next');     ?>',
					"sPrevious": '<?php echo /* I18N: button label, previous page */ WT_I18N::translate('previous'); ?>'
				}
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"bProcessing": true,
			"bStateSave": true,
			"aoColumnDefs": [
				{"bSortable": false, "aTargets": [ 0 ]},
				{"sType": "numeric", "aTargets": [2, 3, 4]}
			],
			"iDisplayLength": 20,
			"sPaginationType": "full_numbers"
	   });
	   	jQuery("#media-list").css('visibility', 'visible');
	});
	<?php echo WT_JS_END;
	//--table wrapper
	echo '<div id="media-list">';
	//-- table header
	echo '<table id="media_list_table"><thead><tr>';
	echo '<th>', WT_I18N::translate('Media'), '</th>';
	echo '<th>', WT_Gedcom_Tag::getLabel('TITL'), '</th>';
	echo '<th>', WT_I18N::translate('Individuals'), '</th>';
	echo '<th>', WT_I18N::translate('Families'), '</th>';
	echo '<th>', WT_I18N::translate('Sources'), '</th>';
	if ($SHOW_LAST_CHANGE) {
		echo '<th>', WT_Gedcom_Tag::getLabel('CHAN'), '</th>';
	} else {
		echo '<th style="display:none;"></th>';
	}
	echo '</tr></thead>';
	//-- table body
	echo '<tbody>';
	$n = 0;
	foreach ($datalist as $key => $value) {
		if (is_object($value)) { // Array of objects
			$media=$value;
		} else {
			$media = new WT_Media($value["GEDCOM"]);
			if (is_null($media)) $media = WT_Media::getInstance($key);
			if (is_null($media)) continue;
		}
		if ($media->canDisplayDetails()) {
			$name = $media->getFullName();
			echo "<tr>";
			//-- Object thumbnail
			echo '<td><img src="', $media->getThumbnail(), '" alt="', $name, '" /></td>';
			//-- Object name(s)
			echo '<td align="', get_align($name), '">';
			echo '<a href="', $media->getHtmlUrl(), '" class="list_item name2">';
			echo highlight_search_hits($name), '</a>';
			if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT)
				echo '<br /><a href="', $media->getHtmlUrl(), '">', basename($media->getFilename()), '</a>';
			if ($media->getNote()) echo '<br />', print_fact_notes('1 NOTE '.$media->getNote(), 1);
			echo '</td>';

			//-- Linked INDIs
			$tmp=$media->countLinkedIndividuals();
			echo '<td>', $tmp, '</td>';
			//-- Linked FAMs
			$tmp=$media->countLinkedfamilies();
			echo '<td>', $tmp, '</td>';
			//-- Linked SOURces
			$tmp=$media->countLinkedSources();
			echo '<td>', $tmp, '</td>';
			//-- Last change
			if ($SHOW_LAST_CHANGE) {
				echo '<td>'.$media->LastChangeTimestamp(empty($SEARCH_SPIDER)).'</td>';
			} else {
				echo '<td style="display:none;"></td>';
			}
			echo '</tr>';
		}
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}

// Print a table of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $type string, indilist or famlist
function format_surname_table($surnames, $type) {
	global $GEDCOM;

	$html='<table class="sortable list_table center">';
	$html.='<th>&nbsp;</th>';
	$html.='<th class="list_label">'.WT_Gedcom_Tag::getLabel('SURN').'</th>';
	$html.='<th class="list_label">';
	if ($type=='famlist') {
		$html.=WT_I18N::translate('Spouses');
	} else {
		$html.=WT_I18N::translate('Individuals');
	}
	$html.='</th></tr>';

	$unique_surn=array();
	$unique_indi=array();
	$row_num=0;
	foreach ($surnames as $surn=>$surns) {
		// Each surname links back to the indi/fam surname list
		if ($surn) {
			$url=$type.'.php?surname='.urlencode($surn).'&amp;ged='.WT_GEDURL;
		} else {
			$url=$type.'.php?alpha=,&amp;ged='.WT_GEDURL;
		}
		// Row counter
		++$row_num;
		$html.='<tr><td class="rela list_item">'.$row_num.'</td>';
		// Surname
		$html.='<td class="list_value_wrap" align="'.get_align($surn).'">';
		if (count($surns)==1) {
			// Single surname variant
			foreach ($surns as $spfxsurn=>$indis) {
				$html.='<a href="'.$url.'" class="list_item name1">'.htmlspecialchars($spfxsurn).'</a>';
				$unique_surn[$spfxsurn]=true;
				foreach (array_keys($indis) as $pid) {
					$unique_indi[$pid]=true;
				}
			}
		} else {
			// Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc.
			foreach ($surns as $spfxsurn=>$indis) {
				$html.='<a href="'.$url.'" class="list_item name1">'.htmlspecialchars($spfxsurn).'</a><br />';
				$unique_surn[$spfxsurn]=true;
				foreach (array_keys($indis) as $pid) {
					$unique_indi[$pid]=true;
				}
			}
		}
		$html.='</td>';
		// Surname count
		$html.='<td class="list_value_wrap">';
		if (count($surns)==1) {
			// Single surname variant
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal=count($indis);
				$html.='<a name="'.$subtotal.'">'.WT_I18N::number($subtotal).'</a>';
			}
		} else {
			// Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc.
			$subtotal=0;
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal+=count($indis);
				$html.=WT_I18N::number(count($indis)).'<br />';
			}
			$html.='<a name="'.$subtotal.'">'.WT_I18N::number($subtotal).'</a>';
		}
		$html.='</td></tr>';
	}
	//-- table footer
	$html.='<tr class="sortbottom"><td class="list_item">&nbsp;</td>';
	$html.='<td class="list_item">&nbsp;</td>';
	$html.='<td class="list_label name2">'. /* I18N: A count of individuals */ WT_I18N::translate('Total individuals: %s', WT_I18N::number(count($unique_indi)));
	$html.='<br/>'. /* I18N: A count of surnames */ WT_I18N::translate('Total surnames: %s', WT_I18N::number(count($unique_surn))).'</td></tr></table>';
	return $html;
}

// Print a tagcloud of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $type string, indilist or famlist
// @param $totals, boolean, show totals after each name
function format_surname_tagcloud($surnames, $type, $totals) {
	$cloud=new Zend_Tag_Cloud(
		array(
			'tagDecorator'=>array(
				'decorator'=>'HtmlTag',
				'options'=>array(
					'htmlTags'=>array(),
					'fontSizeUnit'=>'%',
					'minFontSize'=>80,
					'maxFontSize'=>250
				)
			),
			'cloudDecorator'=>array(
				'decorator'=>'HtmlCloud',
				'options'=>array(
					'htmlTags'=>array(
						'div'=>array(
							'class'=>'tag_cloud'
						)
					)
				)
			)
		)
	);
	foreach ($surnames as $surn=>$surns) {
		foreach ($surns as $spfxsurn=>$indis) {
			$cloud->appendTag(array(
				'title'=>$totals ? WT_I18N::translate('%1$s (%2$d)', $spfxsurn, count($indis)) : $spfxsurn,
				'weight'=>count($indis),
				'params'=>array(
					'url'=>$surn ?
						$type.'.php?surname='.urlencode($surn).'&amp;ged='.WT_GEDURL :
						$type.'.php?alpha=,&amp;ged='.WT_GEDURL
				)
			));
		}
	}
	return (string)$cloud;
}

// Print a list of surnames.
// @param $surnames array (of SURN, of array of SPFX_SURN, of array of PID)
// @param $style, 1=bullet list, 2=semicolon-separated list, 3=tabulated list with up to 4 columns
// @param $totals, boolean, show totals after each name
// @param $type string, indilist or famlist
function format_surname_list($surnames, $style, $totals, $type) {
	global $TEXT_DIRECTION, $GEDCOM;

	$html=array();
	foreach ($surnames as $surn=>$surns) {
		// Each surname links back to the indilist
		if ($surn) {
			$url=$type.'.php?surname='.urlencode($surn).'&amp;ged='.rawurlencode($GEDCOM);
		} else {
			$url=$type.'.php?alpha=,&amp;ged='.rawurlencode($GEDCOM);
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
		$subhtml='<a href="'.$url.'">'.htmlspecialchars(implode(', ', array_keys($surns))).'</a>';

		if ($totals) {
			$subtotal=0;
			foreach ($surns as $spfxsurn=>$indis) {
				$subtotal+=count($indis);
			}
			$subhtml.=' ['.$subtotal.']';
		}
		$html[]=$subhtml;

	}
	switch ($style) {
	case 1:
		return '<ul><li>'.implode('</li><li>', $html).'</li></ul>';
	case 2:
		return implode('; ', $html);
	case 3:
		$i = 0;
		$count = count($html);
		$count_indi = 0;
		$col = 1;
		if ($count>36) $col=4;
		else if ($count>18) $col=3;
		else if ($count>6) $col=2;
		$newcol=ceil($count/$col);
		$html2 ='<table class="list_table"><tr>';
		$html2.='<td class="list_value" style="padding: 14px;">';

		foreach ($html as $surn=>$surns) {
			$html2.= $surns.'<br />';
			$i++;
			if ($i==$newcol && $i<$count) {
				$html2.='</td><td class="list_value" style="padding: 14px;">';
				$newcol=$i+ceil($count/$col);
			}
		}
		$html2.='</td></tr></table>';

		return $html2;
	}
}


/**
 * print a list of recent changes
 *
 * @param array $change_ids contain records that were extracted from the database.
 * @param string $sort determines what to sort
 * @param bool $show_parents
 */
function print_changes_list($change_ids, $sort, $show_parents=false) {
	global $SHOW_MARRIED_NAMES;
	$n = 0;
	$arr=array();
	foreach ($change_ids as $change_id) {
		$record = WT_GedcomRecord::getInstance($change_id);
		if (!$record || !$record->canDisplayDetails()) {
			continue;
		}
		// setup sorting parameters
		$arr[$n]['record'] = $record;
		$arr[$n]['jd'] = ($sort == 'name') ? 1 : $n;
		$arr[$n]['anniv'] = $record->LastChangeTimestamp(false, true);
		$arr[$n++]['fact'] = $record->getSortName(); // in case two changes have same timestamp
	}

	switch ($sort) {
	case 'name':
		uasort($arr, 'event_sort_name');
		break;
	case 'date_asc':
		uasort($arr, 'event_sort');
		$arr = array_reverse($arr);
		break;
	case 'date_desc':
		uasort($arr, 'event_sort');
	}
	$return = '';
	foreach ($arr as $value) {
		$return .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item name2">' . $value['record']->getFullName() . '</a>';
		$return .= '<div class="indent" style="margin-bottom:5px">';
		if ($value['record']->getType() == 'INDI') {
			if ($value['record']->getAddName()) {
				$return .= '<a href="' . $value['record']->getHtmlUrl() . '" class="list_item">' . $value['record']->getAddName() . '</a>';
			}
			if ($SHOW_MARRIED_NAMES) {
				foreach ($value['record']->getAllNames() as $name) {
					if ($name['type'] == '_MARNM') {
						$return .= '<div><a title="' . WT_Gedcom_Tag::getLabel('_MARNM') . '" href="' . $value['record']->getHtmlUrl() . '" class="list_item">' . $name['full'] . '</a></div>';
					}
				}
			}
			if ($show_parents) {
				$return .= $value['record']->getPrimaryParentsNames('details1');
			}
		}
		$return .= /* I18N: [a record was] Changed on <date/time> by <user> */ WT_I18N::translate('Changed on %1$s by %2$s', $value['record']->LastChangeTimestamp(false), $value['record']->LastChangeUser());
		$return .= '</div>';
	}
	return $return;
}

/**
 * print a sortable table of recent changes
 *
 * @param array $change_ids contain records that were extracted from the database.
 * @param string $sort determines what to sort
 * @param bool $show_parents
 */
function print_changes_table($change_ids, $sort, $show_parents=false) {
    global $SHOW_MARRIED_NAMES, $TEXT_DIRECTION, $WT_IMAGES;
    $return = '';
    $n = 0;
    $table_id = "ID" . floor(microtime() * 1000000); // create a unique ID
    switch ($sort) {
        case 'name':        //name
            $aaSorting = "[5,'asc'], [4,'desc']";
            break;
        case 'date_asc':    //date ascending
            $aaSorting = "[4,'asc'], [5,'asc']";
            break;
        case 'date_desc':   //date descending
            $aaSorting = "[4,'desc'], [5,'asc']";
    }
?>
	<script type="text/javascript" src="js/jquery/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function(){
				jQuery('#<?php echo $table_id; ?>').dataTable( {
                "bAutoWidth":false,
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": false,
                "bInfo": false,
				"oLanguage": {
					"sZeroRecords": '<?php echo WT_I18N::translate('No records to display');?>',
				},
                "bJQueryUI": false,
                "aaSorting": [<?php echo $aaSorting; ?>],
                "aoColumns": [
                    /* 0-Sex */     { "bSortable" : false },
                    /* 1-Record */  { "iDataSort" : 5 },
                    /* 2-Change */  { "iDataSort" : 4 },
                    /* 3=By */      null,
                    /* 4-DATE */    { "bVisible" : false },
                    /* 5-SORTNAME */{ "bVisible" : false }
                ]
            });
        });
    </script>
<?php
    //-- table header
    $return .= "<table id='" . $table_id . "' class='list_table center width100'>";
    $return .= "<thead><tr>";
    $return .= "<th class='list_label'></th>";
    $return .= "<th style='cursor:pointer;' class='list_label'>" . WT_I18N::translate('Record') . "</th>";
    $return .= "<th style='cursor:pointer;' class='list_label'>" . WT_Gedcom_Tag::getLabel('CHAN') . "</th>";
    $return .= "<th style='cursor:pointer;' class='list_label'>" . WT_Gedcom_Tag::getLabel('_WT_USER') . "</th>";
    $return .= "<th style='display:none;'>DATE</th>";     //hidden by datatables code
    $return .= "<th style='display:none;'>SORTNAME</th>"; //hidden by datatables code
    $return .= "</tr></thead><tbody>";
    //-- table body

    foreach ($change_ids as $change_id) {
        $record = WT_GedcomRecord::getInstance($change_id);
        if (!$record || !$record->canDisplayDetails()) {
            continue;
        }
        $return .= "<tr><td class='rela list_item'>";
        $indi = false;
        switch ($record->getType()) {
            case "INDI":
                $return .= $record->getSexImage('small', '', '', false);
                $indi = true;
                break;
            case "FAM":
                $return .= '<img src="' . $WT_IMAGES['cfamily'] . '" title="" alt="" height="12" />';
                break;
            case "OBJE":
                $return .= '<img src="' . $record->getMediaIcon() . '" title="" alt="" height="12" />';
                break;
            case "NOTE":
                $return .= '<img src="' . $WT_IMAGES['note'] . '" title="" alt="" height="12" />';
                break;
            case "SOUR":
                $return .= '<img src="' . $WT_IMAGES['source'] . '" title="" alt="" height="12" />';
                break;
            case "REPO":
                $return .= '<img src="' . $WT_IMAGES['repository'] . '" title="" alt="" height="12" />';
                break;
            default:
                break;
        }
        $return .= "</td>";
        ++$n;
        //-- Record name(s)
        $name = $record->getFullName();
        $return .= "<td class='list_value_wrap' align='" . get_align($name) . "'>";
        $return .= "<a href='" . $record->getHtmlUrl() . "' class='list_item name2' dir='" . $TEXT_DIRECTION . "'>" . $name . "</a>";
        if ($indi) {
            $return .= "<div class='indent'>";
            $addname = $record->getAddName();
            if ($addname) {
                $return .= "<a href='" . $record->getHtmlUrl() . "' class='list_item'>" . $addname . "</a>";
            }
            if ($SHOW_MARRIED_NAMES) {
                foreach ($record->getAllNames() as $name) {
                    if ($name['type'] == '_MARNM') {
                        $return .= "<div><a title='" . WT_Gedcom_Tag::getLabel('_MARNM') . "' href='" . $record->getHtmlUrl() . "' class='list_item'>" . $name['full'] . "</a></div>";
                    }
                }
            }
            if ($show_parents) {
                $return .= $record->getPrimaryParentsNames("parents_$table_id details1");
            }
            $return .= "</div>"; //class='indent'
        }
        $return .= "</td>";
        //-- Last change date/time
        $return .= "<td class='list_value_wrap'>" . $record->LastChangeTimestamp(empty($SEARCH_SPIDER)) . "</td>";
        //-- Last change user
        $return .= "<td class='list_value_wrap'>" . $record->LastChangeUser() . "</td>";
        //-- change date (sortable) hidden by datatables code
        $return .= "<td  style='display:none;'>" . $record->LastChangeTimestamp(false, true) . "</td>";
        //-- names (sortable) hidden by datatables code
        $return .= "<td  style='display:none;'>" . $record->getSortName() . "</td></tr>";
    }

    //-- table footer
	$return .= "</tbody>";
	$return .= "</table>";
	if ($n>0) {
		$return .= WT_I18N::translate('Showing %1$s to %2$s of %3$s', 1, $n, $n);
	}
	return $return;
}


/**
 * print a sortable table of events
 * and generates hCalendar records
 * @see http://microformats.org/
 *
 * @param array $datalist contain records that were extracted from the database.
 */
function print_events_table($startjd, $endjd, $events='BIRT MARR DEAT', $only_living=false, $sort_by='anniv') {
	global $TEXT_DIRECTION, $WT_IMAGES;
	$table_id = "ID".floor(microtime()*1000000); // each table requires a unique ID
	?>
	<script type="text/javascript" src="js/jquery/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('#<?php echo $table_id; ?>').dataTable( {
				"sDom": '<"F"li>',
				"bAutoWidth":false,
				"bPaginate": false,
				"bLengthChange": false,
				"bFilter": false,
				"bInfo": false,
				"bJQueryUI": false,
				//"aaSorting": [[ <?php echo $sort_by=='alpha' ? 0 : 3; ?>, 'asc']],
				"aoColumns": [
					/* 0-Record */ { "iDataSort": 1 },
					/* 1-NAME */   { "bVisible": false },
					/* 2-Date */   { "iDataSort": 3 },
					/* 3-DATE */   { "bVisible": false },
					/* 4-Anniv. */  null,
					/* 5-Event */   null
				]
			});		
		});
	</script>
	<?php
	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;

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
		if (!$record->canDisplayDetails() || !canDisplayFact($record->getXref(), $record->getGedId(), $value['factrec'])) {
			continue;
		}
		//-- Counter
		$output ++;

		if ($output==1) {
			//-- First table row:  start table headers, etc. first
			$return .= '<table id="'.$table_id.'" class="list_table center width100">';
			$return .= '<thead><tr>';
			$return .= '<th style="cursor:pointer;" class="list_label">'.WT_I18N::translate('Record').'</th>';
			$return .= '<th style="display:none;">NAME</th>'; //hidden by datables code
			$return .= '<th style="cursor:pointer;" class="list_label">'.WT_Gedcom_Tag::getLabel('DATE').'</th>';
			$return .= '<th style="display:none;">DATE</th>'; //hidden by datables code
			$return .= '<th style="cursor:pointer;" class="list_label"><img src="'.$WT_IMAGES["reminder"].'" alt="'.WT_I18N::translate('Anniversary').'" title="'.WT_I18N::translate('Anniversary').'" border="0" /></th>';
			$return .= '<th style="cursor:pointer;" class="list_label">'.WT_Gedcom_Tag::getLabel('EVEN').'</th>';
			$return .= '</tr></thead><tbody>'."\n";
		}

		$value['name'] = $record->getFullName();
		$value['url'] = $record->getHtmlUrl();
		if ($record->getType()=="INDI") {
			$value['sex'] = $record->getSexImage();
		} else {
			$value['sex'] = '';
		}
		$filtered_events[] = $value;
	}

	// Now we've filtered the list, we can sort by event, if required
	switch ($sort_by) {
	case 'anniv':
		uasort($filtered_events, 'event_sort');
		break;
	case 'alpha':
		uasort($filtered_events, 'event_sort_name');
		break;
	}

	foreach ($filtered_events as $n=>$value) {
		$return .= "<tr>";
		//-- Record name(s)
		$name = $value['name'];
		$return .= '<td class="list_value_wrap" align="'.get_align($name).'">';
		$return .= '<a href="'.$value['url'].'" class="list_item name2" dir="'.$TEXT_DIRECTION.'">'.$name.'</a>';
		if ($value['record']->getType()=="INDI") {
			$return .= $value['sex'];
			$return .= $value['record']->getPrimaryParentsNames("parents_$table_id details1", "none");
		}
		$return .= '</td>';
		//-- NAME
		$return .= '<td style="display:none;">'; //hidden by datables code
		$return .= $value['record']->getSortName();
		$return .= '</td>';
		//-- Event date
		$return .= '<td class="list_value_wrap">';
		$return .= str_replace('<a', '<a name="'.$value['jd'].'"', $value['date']->Display(empty($SEARCH_SPIDER)));
		$return .= '</td>';
		//-- Event date (sortable)
		$return .= '<td style="display:none;">'; //hidden by datables code
		$return .= $n;
		$return .= '</td>';
		//-- Anniversary
		$return .= '<td class="list_value_wrap">';
		$anniv = $value['anniv'];
//		if ($anniv==0) $return .= '<a name="-1">&nbsp;</a>';
		if ($anniv==0) $return .= '&nbsp;';
//		else $return .= "<a name=\"{$anniv}\">{$anniv}</a>";
		else $return .= $anniv;
		$return .= '</td>';
		//-- Event name
		$return .= '<td class="list_value_wrap">';
		$return .= '<a href="'.$value['url'].'" class="list_item">'.WT_Gedcom_Tag::getLabel($value['fact']).'</a>';
		$return .= '&nbsp;</td>';

		$return .= '</tr>'."\n";
	}

	if ($output!=0) {
		//-- table footer
		$return .= '</tbody><tfoot><tr class="sortbottom">';
		$return .= '<td class="list_label">';
		$return .= "<input id=\"cb_parents_$table_id\" type=\"checkbox\" onclick=\"toggleByClassName('DIV', 'parents_$table_id');\" /><label for=\"cb_parents_$table_id\">&nbsp;&nbsp;".WT_I18N::translate('Show parents').'</label><br />';
		$return .= '</td><td class="list_label">';
		$return .= /* I18N: A count of events */ WT_I18N::translate('Total events: %s', WT_I18N::number($output));
		$return .= '</td>';
		$return .= '<td class="list_label">&nbsp;</td><td class="list_label">&nbsp;</td><td class="list_label">&nbsp;</td><td class="list_label">&nbsp;</td>';//DataTables cannot work with colspan
		$return .= '</tr></tfoot>';
		$return .= '</table>';
	}

	// Print a final summary message about restricted/filtered facts
	$summary = "";
	if ($endjd==WT_CLIENT_JD) {
		// We're dealing with the Today's Events block
		if ($output==0) {
			if ($filter==0) {
				$summary = WT_I18N::translate('No events exist for today.');
			} else {
				$summary = WT_I18N::translate('No events for living people exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($output==0) {
			if ($filter==0) {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unsed; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events exist for the next %d day.', 'No events exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			} else {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events for living people exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unsed; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events for living people exist for the next %d day.', 'No events for living people exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			}
		}
	}
	if ($summary!="") {
		$return .= '<strong>'. $summary. '</strong>';
	}

	return $return;
}

/**
 * print a list of events
 *
 * This performs the same function as print_events_table(), but formats the output differently.
 */
function print_events_list($startjd, $endjd, $events='BIRT MARR DEAT', $only_living=false, $sort_by='anniv') {
	global $TEXT_DIRECTION;

	// Did we have any output?  Did we skip anything?
	$output = 0;
	$filter = 0;

	$return = '';

	$filtered_events = array();

	foreach (get_events_list($startjd, $endjd, $events) as $value) {
		$record = WT_GedcomRecord::getInstance($value['id']);
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
		if (!$record->canDisplayDetails() || !canDisplayFact($record->getXref(), $record->getGedId(), $value['factrec'])) {
			continue;
		}
		$output ++;

		$value['name'] = $record->getFullName();
		$value['url'] = $record->getHtmlUrl();
		if ($record->getType()=="INDI") {
			$value['sex'] = $record->getSexImage();
		} else {
			$value['sex'] = '';
		}
		$filtered_events[] = $value;
	}

	// Now we've filtered the list, we can sort by event, if required
	switch ($sort_by) {
	case 'anniv':
		uasort($filtered_events, 'event_sort');
		break;
	case 'alpha':
		uasort($filtered_events, 'event_sort_name');
		break;
	}

	foreach ($filtered_events as $value) {
		$return .= "<a href=\"".$value['url']."\" class=\"list_item name2\" dir=\"".$TEXT_DIRECTION."\">".$value['name']."</a>".$value['sex'];
		$return .= "<br /><div class=\"indent\">";
		$return .= WT_Gedcom_Tag::getLabel($value['fact']).' - '.$value['date']->Display(true);
		if ($value['anniv']!=0) $return .= " (" . WT_I18N::translate('%s year anniversary', $value['anniv']).")";
		if (!empty($value['plac'])) $return .= " - <a href=\"".get_place_url($value['plac'])."\">".$value['plac']."</a>";
		$return .= "</div>";
	}

	// Print a final summary message about restricted/filtered facts
	$summary = "";
	if ($endjd==WT_CLIENT_JD) {
		// We're dealing with the Today's Events block
		if ($output==0) {
			if ($filter==0) {
				$summary = WT_I18N::translate('No events exist for today.');
			} else {
				$summary = WT_I18N::translate('No events for living people exist for today.');
			}
		}
	} else {
		// We're dealing with the Upcoming Events block
		if ($output==0) {
			if ($filter==0) {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unused; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events exist for the next %d day.', 'No events exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			} else {
				if ($endjd==$startjd) {
					$summary = WT_I18N::translate('No events for living people exist for tomorrow.');
				} else {
					// I18N: tanslation for %d==1 is unused; it is translated separately as tomorrow
					$summary = WT_I18N::plural('No events for living people exist for the next %d day.', 'No events for living people exist for the next %d days.', $endjd-$startjd+1, $endjd-$startjd+1);
				}
			}
		}
	}
	if ($summary) {
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
	global $stylesheet;

	require_once WT_ROOT.'includes/cssparser.inc.php';
	$css = new cssparser(false);
	$css->Parse($stylesheet);
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
	$chart_url .= "&amp;chdl=".WT_I18N::translate('Males')."|".WT_I18N::translate('Females')."|".WT_I18N::translate('Average age').": ".$avg; // legend & average age
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
	global $stylesheet;

	require_once WT_ROOT.'includes/cssparser.inc.php';
	$css = new cssparser(false);
	$css->Parse($stylesheet);
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
				if (helptext=='' || helptext==undefined) helptext = <?php echo "'", WT_I18N::translate('Sort by this column.'), "'"; ?>;
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
