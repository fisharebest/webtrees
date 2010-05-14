<?php
/**
 * Display Events on a Calendar
 *
 * Displays events on a daily, monthly, or yearly calendar.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 3 September 2005
 *
 * $Id$
 * @package webtrees
 * @subpackage Calendar
 */

define('WT_SCRIPT_NAME', 'calendar.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_print_lists.php';

if (isset($_REQUEST['cal'])) $cal = $_REQUEST['cal'];
if (isset($_REQUEST['day'])) $day = $_REQUEST['day'];
if (isset($_REQUEST['month'])) $month = $_REQUEST['month'];
if (isset($_REQUEST['year'])) $year = $_REQUEST['year'];
if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['filterev'])) $filterev = $_REQUEST['filterev'];
if (isset($_REQUEST['filterof'])) $filterof = $_REQUEST['filterof'];
if (isset($_REQUEST['filtersx'])) $filtersx = $_REQUEST['filtersx'];

// Set undefined parameters to defaults
if (empty($cal     )) $cal     ='';
if (empty($day     )) $day     ='';
if (empty($month   )) $month   ='';
if (empty($year    )) $year    ='';
if (empty($action  )) $action  ='today';
if (empty($filterev)) $filterev='bdm';
if (empty($filterof)) $filterof='all';
if (empty($filtersx)) $filtersx='';

// Create a CalendarDate from the parameters
$cal=urldecode($cal);
// advance-year "year range"
if (preg_match('/^(\d+)-(\d+)$/', $year, $match)) {
	if (strlen($match[1]) > strlen($match[2]))
		$match[2]=substr($match[1], 0, strlen($match[1])-strlen($match[2])).$match[2];
	$ged_date=new GedcomDate("FROM {$cal} {$match[1]} TO {$cal} {$match[2]}");
	$action='year';
} else
	// advanced-year "decade/century wildcard"
	if (preg_match('/^(\d+)(\?+)$/', $year, $match)) {
		$y1=$match[1].str_replace('?', '0', $match[2]);
		$y2=$match[1].str_replace('?', '9', $match[2]);
		$ged_date=new GedcomDate("FROM {$cal} {$y1} TO {$cal} {$y2}");
		$action='year';
	} else {
		if ($year<0)
			$year=(-$year)."B.C."; // need BC to parse date
		$ged_date=new GedcomDate("{$cal} {$day} {$month} {$year}");
		$year=$ged_date->date1->y; // need negative year for year entry field.
	}
$cal_date=&$ged_date->date1;
$cal=urlencode($cal);

// Invalid month?  Pick a sensible one.
if ($cal_date->CALENDAR_ESCAPE()=='@#DHEBREW@' && $cal_date->m==7 && $cal_date->y!=0 && !$cal_date->IsLeapYear())
	$cal_date->m=6;

// Fill in any missing bits with todays date
$today=$cal_date->Today();
if ($cal_date->d==0) $cal_date->d=$today->d;
if ($cal_date->m==0) $cal_date->m=$today->m;
if ($cal_date->y==0) $cal_date->y=$today->y;
$cal_date->SetJDfromYMD();
if ($year==0)
	$year=$cal_date->y;

// Extract values from date
$days_in_month=$cal_date->DaysInMonth();
$days_in_week=$cal_date->DaysInWeek();
$cal_month=$cal_date->Format('%O');
$today_month=$today->Format('%O');

// Invalid dates?  Go to monthly view, where they'll be found.
if ($cal_date->d>$days_in_month && $action=='today')
	$action='calendar';

// Print the header stuff
print_header(i18n::translate('Anniversary calendar'));
echo '<div style="text-align: center;" id="calendar_page">';

if ($view!='preview') {
	// Calendar form
	echo '<form name="dateform" method="get" action="calendar.php">';
	echo "<input type=\"hidden\" name=\"cal\"      value=\"{$cal}\"         />";
	echo "<input type=\"hidden\" name=\"day\"      value=\"{$cal_date->d}\" />";
	echo "<input type=\"hidden\" name=\"month\"    value=\"{$cal_month}\"   />";
	echo "<input type=\"hidden\" name=\"year\"     value=\"{$cal_date->y}\" />";
	echo "<input type=\"hidden\" name=\"action\"   value=\"{$action}\"      />";
	echo "<input type=\"hidden\" name=\"filterev\" value=\"{$filterev}\"    />";
	echo "<input type=\"hidden\" name=\"filtersx\" value=\"{$filtersx}\"    />";
	echo "<input type=\"hidden\" name=\"filterof\" value=\"{$filterof}\"    />";
}
echo '<table class="facts_table '.$TEXT_DIRECTION.' width100">';
echo '<tr><td class="facts_label" colspan="8"><h2>';

switch ($action) {
case 'today':
	echo i18n::translate('On This Day ...').'<br/>'.$ged_date->Display(false);
	break;
case 'calendar':
	echo i18n::translate('In This Month ...').'<br/>'.$ged_date->Display(false, '%F %Y');
	break;
case 'year':
	echo i18n::translate('In This Year ...').'<br/>'.$ged_date->Display(false, '%Y');
	break;
}
echo '</h2></td></tr>';

if ($view!='preview') {
	// Day selector
	echo '<tr><td class="descriptionbox vmiddle">';
	echo i18n::translate('Day'), help_link('annivers_date_select'), '</td><td colspan="7" class="optionbox">';
	for($d=1; $d<=$days_in_month; $d++) {
		// Format the day number using the calendar
		$tmp=new GedcomDate($cal_date->Format("%@ {$d} %O %E")); $d_fmt=$tmp->date1->Format('%j');
		if ($d==$cal_date->d)
			echo "<span class=\"error\">{$d_fmt}</span>";
		else
			echo "<a href=\"".encode_url("calendar.php?cal={$cal}&day={$d}&month={$cal_month}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action={$action}")."\">{$d_fmt}</a>";
		echo ' | ';
	}
	$tmp=new GedcomDate($today->Format('%@ %A %O %E')); // Need a GedcomDate object to get localisation
	echo "<a href=\"calendar.php?cal={$cal}&amp;day={$today->d}&amp;month={$today_month}&amp;year={$today->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\"><b>".$tmp->Display(true, NULL, array()).'</b></a>';
	// Month selector
	echo '<tr><td class="descriptionbox vmiddle">';
	echo i18n::translate('Month'), help_link('annivers_month_select'), '</td>';
	echo '<td class="optionbox" colspan="7">';
	for ($n=1; $n<=$cal_date->NUM_MONTHS(); ++$n) {
		$month_name=$cal_date->NUM_TO_MONTH_NOMINATIVE($n, $cal_date->IsLeapYear());
		$m=$cal_date->NUM_TO_GEDCOM_MONTH($n, $cal_date->IsLeapYear());
		if ($n==$cal_date->m)
			$month_name="<span class=\"error\">{$month_name}</span>";
		echo "<a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$m}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action={$action}")."\">{$month_name}</a>";
		echo ' | ';
	}
	echo "<a href=\"".encode_url("calendar.php?cal={$cal}&day=".min($cal_date->d, $today->DaysInMonth())."&month={$today_month}&year={$today->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action={$action}")."\"><b>".$today->Format('%F %Y').'</b></a></td></tr>';
	// Year selector
	echo '<tr><td class="descriptionbox vmiddle">';
	echo i18n::translate('Year'), help_link('annivers_year_select'), '</td>';
	echo "<td class=\"optionbox vmiddle\">";
	echo "<a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year=".($cal_date->y==1?-1:$cal_date->y-1)."&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action={$action}")."\">-1</a>";
	echo " <input type=\"text\" name=\"year\" value=\"{$year}\" size=\"7\" /> ";
	echo "<a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year=".($cal_date->y==-1?1:$cal_date->y+1)."&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action={$action}")."\">+1</a>";
	echo " | <a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year={$today->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action={$action}")."\"><b>".$today->Format('%Y')."</b></a>";
	echo "</td> ";
	// Filtering options
	echo "<td class=\"descriptionbox vmiddle\">";
	echo i18n::translate('Show'), help_link('annivers_show'), '</td>';
	echo "<td class=\"optionbox vmiddle\">";
	echo "<select class=\"list_value\" name=\"filterof\" onchange=\"document.dateform.submit();\">";
	echo "<option value=\"all\"";
	if ($filterof == "all") echo " selected=\"selected\"";
	echo ">".i18n::translate('All People')."</option>";
	if (!$HIDE_LIVE_PEOPLE || WT_USER_ID) {
		echo "<option value=\"living\"";
		if ($filterof == "living") echo " selected=\"selected\"";
		echo ">".i18n::translate('Living People')."</option>";
	}
	echo "<option value=\"recent\"";
	if ($filterof == "recent") echo " selected=\"selected\"";
	echo ">".i18n::translate('Recent Years (&lt; 100 yrs)')."</option>";
	echo "</select>";

	echo "</td>";
	echo "<td class=\"descriptionbox vmiddle\">";
	echo i18n::translate('Gender'), help_link('annivers_sex'), '</td>';
	echo "<td class=\"optionbox vmiddle\">";
	if ($filtersx=="") {
		echo Person::sexImage('M', 'large', 'vertical-align: middle', i18n::translate('All People'));
		echo Person::sexImage('F', 'large', 'vertical-align: middle', i18n::translate('All People')), ' | ';
	} else {
		echo "<a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx=&action={$action}")."\">";
		echo Person::sexImage('M', 'small', 'vertical-align: middle', i18n::translate('All People'));
		echo Person::sexImage('F', 'small', 'vertical-align: middle', i18n::translate('All People')), '</a> | ';
	}
	if ($filtersx=="M") {
		echo Person::sexImage('M', 'large', 'vertical-align: middle', i18n::translate('Males')), ' | ';
	} else {
		echo "<a href=\"", encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx=M&action={$action}"), "\">";
		echo Person::sexImage('M', 'small', 'vertical-align: middle', i18n::translate('Males')), '</a> | ';
	}
	if ($filtersx=="F")
		echo Person::sexImage('F', 'large', 'vertical-align: middle', i18n::translate('Females')), ' | ';
	else {
		echo "<a href=\"", encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx=F&action={$action}"), "\">";
		echo Person::sexImage('F', 'small', 'vertical-align: middle', i18n::translate('Females')), '</a>';
	}
	echo "</td>";
	echo "<td class=\"descriptionbox vmiddle\">";
	echo i18n::translate('Show events of'), help_link('annivers_event'), '</td>';
	echo "<td class=\"optionbox\">";
	echo "<input type=\"hidden\" name=\"filterev\" value=\"$filterev\" />";
	echo "<select class=\"list_value\" name=\"filterev\" onchange=\"document.dateform.submit();\">";
	echo "<option value=\"bdm\"";
	if ($filterev == "bdm") echo " selected=\"selected\"";
	echo ">".i18n::translate('Births, Deaths, Marriages')."</option>";
	echo "<option value=\"all\"";
	if ($filterev == "all") echo " selected=\"selected\"";
	echo ">".i18n::translate('All')."</option>";
	echo "<option value=\"BIRT\"";
	if ($filterev == "BIRT") echo " selected=\"selected\"";
	echo ">".translate_fact('BIRT')."</option>";
	echo "<option value=\"CHR\"";
	if ($filterev == "CHR") echo " selected=\"selected\"";
	echo ">".translate_fact('CHR')."</option>";
	echo "<option value=\"CHRA\"";
	if ($filterev == "CHRA") echo " selected=\"selected\"";
	echo ">".translate_fact('CHRA')."</option>";
	echo "<option value=\"BAPM\"";
	if ($filterev == "BAPM") echo " selected=\"selected\"";
	echo ">".translate_fact('BAPM')."</option>";
	echo "<option value=\"_COML\"";
	if ($filterev == "_COML") echo " selected=\"selected\"";
	echo ">".translate_fact('_COML')."</option>";
	echo "<option value=\"MARR\"";
	if ($filterev == "MARR") echo " selected=\"selected\"";
	echo ">".translate_fact('MARR')."</option>";
	echo "<option value=\"_SEPR\"";
	if ($filterev == "_SEPR") echo " selected=\"selected\"";
	echo ">".translate_fact('_SEPR')."</option>";
	echo "<option value=\"DIV\"";
	if ($filterev == "DIV") echo " selected=\"selected\"";
	echo ">".translate_fact('DIV')."</option>";
	echo "<option value=\"DEAT\"";
	if ($filterev == "DEAT") echo " selected=\"selected\"";
	echo ">".translate_fact('DEAT')."</option>";
	echo "<option value=\"BURI\"";
	if ($filterev == "BURI") echo " selected=\"selected\"";
	echo ">".translate_fact('BURI')."</option>";
	echo "<option value=\"IMMI\"";
	if ($filterev == "IMMI") echo " selected=\"selected\"";
	echo ">".translate_fact('IMMI')."</option>";
	echo "<option value=\"EMIG\"";
	if ($filterev == "EMIG") echo " selected=\"selected\"";
	echo ">".translate_fact('EMIG')."</option>";
	echo "<option value=\"EVEN\"";
	if ($filterev == "EVEN") echo " selected=\"selected\"";
	echo ">".i18n::translate('Custom Event')."</option>";
	echo "</select>";
	echo "</td></tr>";
	// Day/Month/Year and calendar selector
	echo '<tr><td class="topbottombar" colspan="4">';
	if ($action=='today') {
		echo "<span class=\"error\">", i18n::translate('View Day'), "</span>";
	} else {
		echo "<a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action=today")."\">", i18n::translate('View Day'), "</a>";
	}
	if ($action=='calendar') {
		echo " | <span class=\"error\">", i18n::translate('View Month'), "</span>";
	} else {
		echo " | <a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action=calendar")."\">", i18n::translate('View Month'), "</a>";
	}
	if ($action=='year') {
		echo " | <span class=\"error\">", i18n::translate('View Year'), "</span>";
	} else {
		echo " | <a href=\"".encode_url("calendar.php?cal={$cal}&day={$cal_date->d}&month={$cal_month}&year={$cal_date->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action=year")."\">", i18n::translate('View Year'), "</a>";
	}
	echo help_link('day_month');
	echo '</td><td class="topbottombar" colspan="4">';
	$n=0;
	foreach (array(
		'gregorian'=>i18n::translate('Gregorian'),
		'julian'=>i18n::translate('Julian'),
		'jewish'=>i18n::translate('Jewish'),
		'french'=>i18n::translate('French'),
		'hijri'=>i18n::translate('Hijri')
	) as $newcal=>$cal_name) {
		$tmp=$cal_date->convert_to_cal($newcal);
		if ($tmp->InValidRange()) {
			if ($n++) {
				echo ' | ';
			}
			if ($tmp->CALENDAR_ESCAPE()==$cal_date->CALENDAR_ESCAPE()) {
				echo "<span class=\"error\">{$cal_name}</span>";
			} else {
				$newcalesc=urlencode($tmp->CALENDAR_ESCAPE());
				$tmpmonth=$tmp->FormatGedcomMonth();
				echo "<a href=\"".encode_url("calendar.php?cal={$newcalesc}&day={$tmp->d}&month={$tmpmonth}&year={$tmp->y}&filterev={$filterev}&filterof={$filterof}&filtersx={$filtersx}&action={$action}")."\">{$cal_name}</a>";
			}
		}
	}
	echo "</td></tr>";
} // print preview
echo "</table>\n</form>\n";

// Convert event filter option to a list of gedcom event codes
if ($filterev=='all') {
	$events='';
} else {
	if ($filterev=='bdm') {
		$events='BIRT MARR DEAT';
	} else {
		$events=$filterev;
	}
}

// Fetch data for day/month/year views
switch ($action) {
case 'today':
	$found_facts=apply_filter(get_anniversary_events($cal_date->minJD, $events), $filterof, $filtersx);
	break;
case 'calendar':
	$cal_date->d=0;
	$cal_date->SetJDfromYMD();
	// Make a separate list for each day.  Unspecified/invalid days go in day 0.
	$found_facts=array();
	for ($d=0; $d<=$days_in_month; ++$d)
		$found_facts[$d]=array();
	// Fetch events for each day
	for ($jd=$cal_date->minJD; $jd<=$cal_date->maxJD; ++$jd)
		foreach (apply_filter(get_anniversary_events($jd, $events), $filterof, $filtersx) as $event) {
			$tmp=$event['date']->MinDate();
			if ($tmp->d>=1 && $tmp->d<=$tmp->DaysInMonth())
				$d=$jd-$cal_date->minJD+1;
			else
				$d=0;
			$found_facts[$d][]=$event;
		}
	break;
case 'year':
	$cal_date->m=0;
	$cal_date->SetJDfromYMD();
	$found_facts=apply_filter(get_calendar_events($ged_date->MinJD(), $ged_date->MaxJD(), $events), $filterof, $filtersx);
	// Eliminate duplictes (e.g. BET JUL 1900 AND SEP 1900 will appear twice in 1900)
	foreach ($found_facts as $key=>$value)
		$found_facts[$key]=serialize($found_facts[$key]);
	$found_facts=array_unique($found_facts);
	foreach ($found_facts as $key=>$value)
		$found_facts[$key]=unserialize($found_facts[$key]);
	break;
}

// Group the facts by family/individual
switch ($action) {
case 'year':
case 'today':
	$indis=array();
	$fams=array();
	foreach ($found_facts as $fact) {
		$fact_text=calendar_fact_text($fact, true);
		switch ($fact['objtype']) {
		case 'INDI':
			if (empty($indis[$fact['id']]))
				$indis[$fact['id']]=$fact_text;
			else
				$indis[$fact['id']].='<br/>'.$fact_text;
			break;
		case 'FAM':
			if (empty($fams[$fact['id']]))
				$fams[$fact['id']]=$fact_text;
			else
				$fams[$fact['id']].='<br/>'.$fact_text;
			break;
		}
	}
	break;
case 'calendar':
	$cal_facts=array();
	foreach ($found_facts as $d=>$facts) {
		$cal_facts[$d]=array();
		foreach ($facts as $fact) {
			$id=$fact['id'];
			if (empty($cal_facts[$d][$id]))
				$cal_facts[$d][$id]=calendar_fact_text($fact, false);
			else
				$cal_facts[$d][$id].='<br/>'.calendar_fact_text($fact, false);
		}
	}
	break;
}

switch ($action) {
case 'year':
case 'today':
	echo "<table class=\"center {$TEXT_DIRECTION} width100\"><tr>";
	// Table headings
	echo "<td class=\"descriptionbox center width50\"><img src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['indis']['small']}\" border=\"0\" title=\"", i18n::translate('Individuals'), "\" alt=\"", i18n::translate('Individuals'), "\" />&nbsp;&nbsp;&nbsp;", i18n::translate('Individuals'), "</td>";
	echo "<td class=\"descriptionbox center width50\"><img src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['cfamily']['small']}\" border=\"0\" title=\"", i18n::translate('Families'), "\" alt=\"", i18n::translate('Families'), "\" />&nbsp;&nbsp;&nbsp;", i18n::translate('Families'), "</td>";
	echo "</tr><tr>";
	// Table rows
	$males=0;
	$females=0;
	$numfams=0;
	echo "<td class=\"optionbox {$TEXT_DIRECTION} wrap\">";

	// Avoid an empty unordered list
	ob_start();
	echo calendar_list_text($indis, "<li>", "</li>", true);
	$content = ob_get_clean();
	if (!empty($content)) {
		echo '<ul>', $content, '</ul>';
	}

	echo '</td>';
	echo "<td class=\"optionbox {$TEXT_DIRECTION} wrap\">";

	// Avoid an empty unordered list
	ob_start();
	echo calendar_list_text($fams, "<li>", "</li>", true);
	$content = ob_get_clean();
	if (!empty($content)) {
		echo '<ul>', $content, '</ul>';
	}

	echo '</td>';
	echo "</tr><tr>";
	// Table footers
	echo "<td class=\"descriptionbox\">", i18n::translate('Total individuals'), " ";
	echo count($indis);
	echo "<br />";
	echo Person::sexImage('M', 'small', 'vertical-align: middle', i18n::translate('Males')), "&nbsp;{$males}&nbsp;&nbsp;&nbsp;&nbsp;";
	echo Person::sexImage('F', 'small', 'vertical-align: middle', i18n::translate('Females')), "&nbsp;{$females}&nbsp;&nbsp;&nbsp;&nbsp;";
	if (count($indis)!=$males+$females)
		echo Person::sexImage('U', 'small', 'vertical-align: middle', i18n::translate('All People')), '&nbsp;', count($indis)-$males-$females;
	echo "</td>";
	echo "<td class=\"descriptionbox\">", i18n::translate('Total families'), " ".count($fams)."</td>";
	echo "</tr></table>";

	break;
case 'calendar':
	// We use JD%7 = 0/Mon...6/Sun.  Config files use 0/Sun...6/Sat.  Add 6 to convert.
	$week_start=($WEEK_START+6)%$days_in_week;
	// The french  calendar has a 10-day week, but our config only lets us choose
	// mon-sun as a start day.  Force french calendars to start on primidi
	if ($days_in_week==10) {
		$week_start=0;
	}
	echo "<table class=\"list_table width100 $TEXT_DIRECTION\"><tr>";
	for ($week_day=0; $week_day<$days_in_week; ++$week_day) {
		$day_name=$cal_date->LONG_DAYS_OF_WEEK(($week_day+$week_start) % $days_in_week);
		echo "<td class=\"descriptionbox\" width=\"".floor(100/$days_in_week)."%\">{$day_name}</td>";
	}
	echo "</tr>";
	// Print days 1-n of the month...
	// ...but extend to cover "empty" days before/after the month to make whole weeks.
	// e.g. instead of 1 -> 30 (=30 days), we might have -1 -> 33 (=35 days)
	$start_d=1-($cal_date->minJD-$week_start) % $days_in_week;
	$end_d=$days_in_month+($days_in_week-($cal_date->maxJD-$week_start+1) % $days_in_week) % $days_in_week;
	// Make sure that there is an empty box for any leap/missing days
	if ($start_d==1 && $end_d==$days_in_month && count($found_facts[0])>0)
		$end_d+=$days_in_week;
	for ($d=$start_d; $d<=$end_d; ++$d) {
		if (($d+$cal_date->minJD-$week_start) % $days_in_week==1)
			echo "<tr>";
			echo "<td class=\"optionbox wrap\">";
		if ($d<1 || $d>$days_in_month)
			if (count($cal_facts[0])>0) {
				echo "<span class=\"cal_day\">", i18n::translate('Day not set'), "</span><br style=\"clear: both\" />";
				echo "<div class=\"details1\" style=\"height: 150px; overflow: auto;\">";
				echo calendar_list_text($cal_facts[0], "", "", false);
				echo "</div>";
				$cal_facts[0]=array();
			} else
				echo '&nbsp;';
		else {
			// Format the day number using the calendar
			$tmp=new GedcomDate($cal_date->Format("%@ {$d} %O %E")); $d_fmt=$tmp->date1->Format('%j');
			if ($d==$today->d && $cal_date->m==$today->m)
				echo "<span class=\"cal_day current_day\">{$d_fmt}</span>";
			else
				echo "<span class=\"cal_day\">{$d_fmt}</span>";
			// Show a converted date
			foreach (explode('_and_', $CALENDAR_FORMAT) as $convcal) {
				$alt_date=$cal_date->convert_to_cal($convcal);
				if ($alt_date->CALENDAR_ESCAPE()!=$cal_date->CALENDAR_ESCAPE()) {
					list($alt_date->y, $alt_date->m, $alt_date->d)=$alt_date->JDtoYMD($cal_date->minJD+$d-1);
					$alt_date->SetJDfromYMD();
					echo "<span class=\"rtl_cal_day\">".$alt_date->Format("%j %M")."</span>";
					break;
				}
			}
			echo "<br style=\"clear: both\" /><div class=\"details1\" style=\"height: 150px; overflow: auto;\">";
			echo calendar_list_text($cal_facts[$d], "", "", false);
			echo "</div>";
		}
		echo "</td>";
		if (($d+$cal_date->minJD-$week_start) % $days_in_week==0) {
			echo '</tr>';
		}
	}
	echo '</table>';
	break;
}

if ($view=="preview") {
	// Print details of any filtering
	$filters=array();
	if ($filterof=='living') {
		$filters[]=i18n::translate('Living People');
	}
	if ($filterof=='recent') {
		$filters[]=i18n::translate('Recent Years (&lt; 100 yrs)');
	}
	if ($filtersx=='M') {
		$filters[]=i18n::translate('Male');
	}
	if ($filtersx=='F') {
		$filters[]=i18n::translate('Female');
	}
	if ($filterev=='bdm') {
		$filters[]=i18n::translate('Births, Deaths, Marriages');
	} elseif ($filterev!='all') {
			$filters[].=i18n::translate($filterev);
	}
	$filtertext=implode(' - ', $filters);
	if (!empty($filters)) {
		$filtertext="(".i18n::translate('Filter').": {$filtertext})";
	}
	echo '<br />', get_gedcom_setting(WT_GED_ID, 'title'), ' ', $filtertext;
}
echo "</div><br />";
print_footer();

/////////////////////////////////////////////////////////////////////////////////
// Filter a list of facts
/////////////////////////////////////////////////////////////////////////////////
function apply_filter($facts, $filterof, $filtersx) {
	$filtered=array();
	$hundred_years=server_jd()-36525;
	foreach ($facts as $fact) {
		$tmp=GedcomRecord::GetInstance($fact['id']);
		// Filter on sex
		if ($fact['objtype']=='INDI' && $filtersx!='' && $filtersx!=$tmp->getSex())
			continue;
		// Can't display families if the sex filter is on.
		// TODO: but we could show same-sex partnerships....
		if ($fact['objtype']=='FAM' && $filtersx!='')
			continue;
		// Filter on age of event
		if ($filterof=='living') {
			if ($fact['objtype']=='INDI' && $tmp->isDead())
			continue;
			if ($fact['objtype']=='FAM') {
				$husb=$tmp->getHusband();
				$wife=$tmp->getWife();
				if (!empty($husb) && $husb->isDead())
					continue;
				if (!empty($wife) && $wife->isDead())
					continue;
			}
		}
		if ($filterof=='recent' && $fact['date']->MaxJD()<$hundred_years)
			continue;
		// Finally, check for privacy rules before adding fact.
		if ($tmp->canDisplayDetails())
			$filtered[]=$fact;
	}
	return $filtered;
}

////////////////////////////////////////////////////////////////////////////////
// Format a fact for display.  Include the date, the event type, and optionally
// the place.
////////////////////////////////////////////////////////////////////////////////
function calendar_fact_text($fact, $show_places) {
	global $TEXT_DIRECTION;
	$text=translate_fact($fact['fact']).' - '.$fact['date']->Display(true, "", array());
	if ($fact['anniv']>0)
		$text.=' <span dir="'.$TEXT_DIRECTION.'">('.i18n::translate('%s year anniversary', $fact['anniv']).')</span>';
	if ($show_places && !empty($fact['plac']))
		$text.=' - '.$fact['plac'];
	return $text;
}

////////////////////////////////////////////////////////////////////////////////
// Format a list of facts for display
////////////////////////////////////////////////////////////////////////////////
function calendar_list_text($list, $tag1, $tag2, $show_sex_symbols) {
	global $WT_IMAGE_DIR, $WT_IMAGES;
	global $males, $females;
	foreach ($list as $id=>$facts) {
		$tmp=GedcomRecord::GetInstance($id);
		echo "{$tag1}<a href=\"".encode_url($tmp->getLinkUrl())."\">".PrintReady($tmp->getFullName())."</a>&nbsp;";
		if ($show_sex_symbols && $tmp->getType()=='INDI')
			switch ($tmp->getSex()) {
			case 'M':
				echo Person::sexImage('M', 'small', 'vertical-align: middle', i18n::translate('Male'));
				++$males;
				break;
			case 'F':
				echo Person::sexImage('F', 'small', 'vertical-align: middle', i18n::translate('Female'));
				++$females;
				break;
			default:
				echo Person::sexImage('U', 'small', 'vertical-align: middle', i18n::translate('unknown'));
				break;
			}
			echo "<div class=\"indent\">".$facts."</div>{$tag2}";
	}
}

?>
