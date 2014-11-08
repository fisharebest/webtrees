<?php
// Display Events on a Calendar
//
// Displays events on a daily, monthly, or yearly calendar.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

define('WT_SCRIPT_NAME', 'calendar.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller = new WT_Controller_Page();
$controller->setPageTitle(WT_I18N::translate('Anniversary calendar'));
$controller->pageHeader();

$cal      = WT_Filter::get('cal', '@#D[A-Z ]+@');
$day      = WT_Filter::get('day', '\d\d?');
$month    = WT_Filter::get('month', '[A-Z]{3,5}');
$year     = WT_Filter::get('year', '\d{1,4}(?: B\.C\.)?|\d\d\d\d\/\d\d|\d+(-\d+|[?]+)?');
$action   = WT_Filter::get('action', 'year|today|calendar', 'today');
$filterev = WT_Filter::get('filterev', 'all|bdm|' . WT_REGEX_TAG, 'bdm');
$filterof = WT_Filter::get('filterof', 'all|living|recent', 'all');
$filtersx = WT_Filter::get('filtersx', '[MF]');

if ($cal . $day . $month . $year == '') {
	// No date specified?  Use the most likely calendar
	switch (WT_LOCALE) {
	case 'fa':
		$cal = '@#DJALALI@';
		break;
	case 'ar':
		$cal = '@#DHIJRI@';
		break;
	case 'he':
		$cal = '@#DHEBREW@';
		break;
	default:
		$cal = '@#DGREGORIAN@';
		break;
	}
}

// Create a WT_Date_Calendar from the parameters

// We cannot display new-style/old-style years, so convert to new style
if (preg_match('/^(\d\d)\d\d\/(\d\d)$/', $year, $match)) {
	$year = $match[1] . $match[2];
}

// advanced-year "year range"
if (preg_match('/^(\d+)-(\d+)$/', $year, $match)) {
	if (strlen($match[1]) > strlen($match[2])) {
		$match[2] = substr($match[1], 0, strlen($match[1]) - strlen($match[2])) . $match[2];
	}
	$ged_date = new WT_Date("FROM {$cal} {$match[1]} TO {$cal} {$match[2]}");
	$action   = 'year';
} else {
	// advanced-year "decade/century wildcard"
	if (preg_match('/^(\d+)(\?+)$/', $year, $match)) {
		$y1       = $match[1] . str_replace('?', '0', $match[2]);
		$y2       = $match[1] . str_replace('?', '9', $match[2]);
		$ged_date = new WT_Date("FROM {$cal} {$y1} TO {$cal} {$y2}");
		$action   = 'year';
	} else {
		if ($year < 0) {
			$year = (-$year) . " B.C.";
		} // need BC to parse date
		$ged_date = new WT_Date("{$cal} {$day} {$month} {$year}");
		$year     = $ged_date->date1->y; // need negative year for year entry field.
	}
}
$cal_date = &$ged_date->date1;

// Fill in any missing bits with todays date
$today = $cal_date->today();
if ($cal_date->d === 0) {
	$cal_date->d = $today->d;
}
if ($cal_date->m === 0) {
	$cal_date->m = $today->m;
}
if ($cal_date->y === 0) {
	$cal_date->y = $today->y;
}

$cal_date->setJdFromYmd();

if ($year === 0) {
	$year=$cal_date->y;
}

// Extract values from date
$days_in_month=$cal_date->daysInMonth();
$days_in_week=$cal_date->daysInWeek();
$cal_month=$cal_date->format('%O');
$today_month=$today->format('%O');

// Invalid dates?  Go to monthly view, where they'll be found.
if ($cal_date->d>$days_in_month && $action=='today')
	$action='calendar';
echo '<div id="calendar-page">';

// Calendar form
echo '<form name="dateform" method="get" action="calendar.php">';
echo "<input type=\"hidden\" name=\"cal\" value=\"{$cal}\">";
echo "<input type=\"hidden\" name=\"day\" value=\"{$cal_date->d}\">";
echo "<input type=\"hidden\" name=\"month\" value=\"{$cal_month}\">";
echo "<input type=\"hidden\" name=\"year\" value=\"{$cal_date->y}\">";
echo "<input type=\"hidden\" name=\"action\" value=\"{$action}\">";
echo "<input type=\"hidden\" name=\"filterev\" value=\"{$filterev}\">";
echo "<input type=\"hidden\" name=\"filtersx\" value=\"{$filtersx}\">";
echo "<input type=\"hidden\" name=\"filterof\" value=\"{$filterof}\">";
echo '<table class="facts_table width100">';
echo '<tr><td class="facts_label" colspan="4"><h2>';

// All further uses of $cal are to generate URLs
$cal=rawurlencode($cal);

switch ($action) {
case 'today':
	echo WT_I18N::translate('On this day…').'<br>'.$ged_date->display();
	break;
case 'calendar':
	echo WT_I18N::translate('In this month…').'<br>'.$ged_date->display(false, '%F %Y');
	break;
case 'year':
	echo WT_I18N::translate('In this year…').'<br>'.$ged_date->display(false, '%Y');
	break;
}
echo '</h2></td></tr>';

// Day selector
echo '<tr><td class="descriptionbox vmiddle">';
echo WT_I18N::translate('Day'), '</td><td colspan="3" class="optionbox">';
for ($d=1; $d<=$days_in_month; $d++) {
	// Format the day number using the calendar
	$tmp=new WT_Date($cal_date->format("%@ {$d} %O %E"));
	$d_fmt=$tmp->date1->format('%j');
	if ($d==$cal_date->d)
		echo "<span class=\"error\">{$d_fmt}</span>";
	else
		echo "<a href=\"calendar.php?cal={$cal}&amp;day={$d}&amp;month={$cal_month}&amp;year={$cal_date->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}"."\">{$d_fmt}</a>";
	echo ' | ';
}
$tmp=new WT_Date($today->format('%@ %A %O %E')); // Need a WT_Date object to get localisation
echo "<a href=\"calendar.php?cal={$cal}&amp;day={$today->d}&amp;month={$today_month}&amp;year={$today->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\"><b>".$tmp->display().'</b></a>';
echo '</td></tr>';
// Month selector
echo '<tr><td class="descriptionbox vmiddle">';
echo WT_I18N::translate('Month'), '</td>';
echo '<td class="optionbox" colspan="3">';
for ($n = 1, $months_in_year = $cal_date->monthsInYear(); $n <= $months_in_year; ++$n) {
	$month_name = $cal_date->monthNameNominativeCase($n, $cal_date->isLeapYear());
	$m = array_search($n, $cal_date::$MONTH_ABBREV);
	if ($n == 6 && $cal_date instanceof WT_Date_Jewish && !$cal_date->isLeapYear()) {
		// No month 6 in Jewish non-leap years.
		continue;
	}
	if ($n == 7 && $cal_date instanceof WT_Date_Jewish && !$cal_date->isLeapYear()) {
		// Month 7 is ADR in Jewish non-leap years.
		$m = 'ADR';
	}
	if ($n == $cal_date->m) {
		$month_name = '<span class="error">' . $month_name . '</span>';
	}
	echo "<a href=\"calendar.php?cal={$cal}&amp;day={$cal_date->d}&amp;month={$m}&amp;year={$cal_date->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\">{$month_name}</a>";
	echo ' | ';
}
echo "<a href=\"calendar.php?cal={$cal}&amp;day=".min($cal_date->d, $today->daysInMonth())."&amp;month={$today_month}&amp;year={$today->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\"><b>".$today->format('%F %Y').'</b></a></td></tr>';
// Year selector
echo '<tr><td class="descriptionbox vmiddle">';
echo WT_I18N::translate('Year'), '</td>';
echo '<td class="optionbox vmiddle">';
echo "<a href=\"calendar.php?cal={$cal}&amp;day={$cal_date->d}&amp;month={$cal_month}&amp;year=".($cal_date->y==1?-1:$cal_date->y-1)."&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\">-1</a>";
echo " <input type=\"text\" name=\"year\" value=\"{$year}\" size=\"4\"> ";
echo "<a href=\"calendar.php?cal={$cal}&amp;day={$cal_date->d}&amp;month={$cal_month}&amp;year=".($cal_date->y==-1?1:$cal_date->y+1)."&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\">+1</a>";
echo " | <a href=\"calendar.php?cal={$cal}&amp;day={$cal_date->d}&amp;month={$cal_month}&amp;year={$today->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\"><b>".$today->format('%Y')."</b></a>";
echo help_link('annivers_year_select');
echo '</td> ';

// Filtering options

echo '<td class="descriptionbox vmiddle">';
echo WT_I18N::translate('Show'), '</td>';

echo '<td class="optionbox vmiddle">';
echo '<select class="list_value" name="filterof" onchange="document.dateform.submit();">';
echo '<option value="all"';
if ($filterof == "all") echo ' selected="selected"';
echo '>', WT_I18N::translate('All individuals'), '</option>';
if (!$HIDE_LIVE_PEOPLE || Auth::check()) {
	echo '<option value="living"';
if ($filterof == "living") echo ' selected="selected"';
	echo '>', WT_I18N::translate('Living individuals'), '</option>';
}
echo '<option value="recent"';
if ($filterof == "recent") echo ' selected="selected"';
echo '>', WT_I18N::translate('Recent years (&lt; 100 yrs)'), '</option>';
echo '</select>';

echo '&nbsp;&nbsp;&nbsp;';

if ($filtersx=="") {
	echo '<i class="icon-sex_m_15x15" title="', WT_I18N::translate('All individuals'), '"></i>';
	echo '<i class="icon-sex_f_15x15" title="', WT_I18N::translate('All individuals'), '"></i> | ';
} else {
	echo '<a href="calendar.php?cal=', $cal, '&amp;day=', $cal_date->d, '&amp;month=', $cal_month, '&amp;year=', $cal_date->y, '&amp;filterev=', $filterev, '&amp;filterof=', $filterof, '&amp;action=', $action, '">';
	echo '<i class="icon-sex_m_9x9" title="', WT_I18N::translate('All individuals'), '"></i>';
	echo '<i class="icon-sex_f_9x9" title="', WT_I18N::translate('All individuals'), '"></i></a> | ';
}
if ($filtersx=="M") {
	echo '<i class="icon-sex_m_15x15" title="', WT_I18N::translate('Males'), '"></i> | ';
} else {
	echo '<a class="icon-sex_m_9x9" title="', WT_I18N::translate('Males'), '" href="calendar.php?cal=', $cal, '&amp;day=', $cal_date->d, '&amp;month=', $cal_month, '&amp;year=', $cal_date->y, '&amp;filterev=', $filterev, '&amp;filterof=', $filterof, '&amp;filtersx=M&amp;action=', $action, '"></a> | ';
}
if ($filtersx=="F")
	echo '<i class="icon-sex_f_15x15" title="', WT_I18N::translate('Females'), '"></i>';
else {
	echo '<a class="icon-sex_f_9x9" title="', WT_I18N::translate('Females'), '" href="calendar.php?cal=', $cal, '&amp;day=', $cal_date->d, '&amp;month=', $cal_month, '&amp;year=', $cal_date->y, '&amp;filterev=', $filterev, '&amp;filterof=', $filterof, '&amp;filtersx=F&amp;action=', $action, '"></a>';
}

echo '&nbsp;&nbsp;&nbsp;';

echo "<input type=\"hidden\" name=\"filterev\" value=\"$filterev\">";
echo '<select class="list_value" name="filterev" onchange="document.dateform.submit();">';
echo '<option value="bdm"';
if ($filterev == "bdm") echo ' selected="selected"';
echo '>', WT_I18N::translate('Vital records'), '</option>';
echo '<option value="all"';
if ($filterev == "all") echo ' selected="selected"';
echo '>', WT_I18N::translate('All'), '</option>';
echo '<option value="BIRT"';
if ($filterev == "BIRT") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('BIRT'), '</option>';
echo '<option value="CHR"';
if ($filterev == "CHR") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('CHR'), '</option>';
echo '<option value="CHRA"';
if ($filterev == "CHRA") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('CHRA'), '</option>';
echo '<option value="BAPM"';
if ($filterev == "BAPM") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('BAPM'), '</option>';
echo '<option value="_COML"';
if ($filterev == "_COML") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('_COML'), '</option>';
echo '<option value="MARR"';
if ($filterev == "MARR") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('MARR'), '</option>';
echo '<option value="_SEPR"';
if ($filterev == "_SEPR") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('_SEPR'), '</option>';
echo '<option value="DIV"';
if ($filterev == "DIV") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('DIV'), '</option>';
echo '<option value="DEAT"';
if ($filterev == "DEAT") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('DEAT'), '</option>';
echo '<option value="BURI"';
if ($filterev == "BURI") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('BURI'), '</option>';
echo '<option value="IMMI"';
if ($filterev == "IMMI") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('IMMI'), '</option>';
echo '<option value="EMIG"';
if ($filterev == "EMIG") echo ' selected="selected"';
echo '>', WT_Gedcom_Tag::getLabel('EMIG'), '</option>';
echo '<option value="EVEN"';
if ($filterev == "EVEN") echo ' selected="selected"';
echo '>', WT_I18N::translate('Custom event'), '</option>';
echo '</select>';


echo '</td></tr>';
echo '</table></form>';
echo "<table class=\"width100\">";

// Day/Month/Year and calendar selector
echo '<tr><td class="topbottombar width50">';
if ($action=='today') {
	echo '<span class="error">', WT_I18N::translate('View day'), '</span>';
} else {
	echo "<a href=\"calendar.php?cal={$cal}&amp;day={$cal_date->d}&amp;month={$cal_month}&amp;year={$cal_date->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action=today\">", WT_I18N::translate('View day'), "</a>";
}
if ($action=='calendar') {
	echo ' | <span class="error">', WT_I18N::translate('View month'), '</span>';
} else {
	echo " | <a href=\"calendar.php?cal={$cal}&amp;day={$cal_date->d}&amp;month={$cal_month}&amp;year={$cal_date->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action=calendar\">", WT_I18N::translate('View month'), "</a>";
}
if ($action=='year') {
	echo ' | <span class="error">', WT_I18N::translate('View year'), '</span>';
} else {
	echo " | <a href=\"calendar.php?cal={$cal}&amp;day={$cal_date->d}&amp;month={$cal_month}&amp;year={$cal_date->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action=year\">", WT_I18N::translate('View year'), "</a>";
}
echo '</td><td class="topbottombar width50">';
$n=0;
foreach (array(
	'gregorian'=>WT_Date_Gregorian::calendarName(),
	'julian'   =>WT_Date_Julian::calendarName(),
	'jewish'   =>WT_Date_Jewish::calendarName(),
	'french'   =>WT_Date_French::calendarName(),
	'hijri'    =>WT_Date_Hijri::calendarName(),
	'jalali'   =>WT_Date_Jalali::calendarName(),
) as $newcal=>$cal_name) {
	$tmp=$cal_date->convertToCalendar($newcal);
	if ($tmp->inValidRange()) {
		if ($n++) {
			echo ' | ';
		}
		if (get_class($tmp)==get_class($cal_date)) {
			echo "<span class=\"error\">{$cal_name}</span>";
		} else {
			$newcalesc=urlencode($tmp->format('%@'));
			$tmpmonth=$tmp->format('%O');
			echo "<a href=\"calendar.php?cal={$newcalesc}&amp;day={$tmp->d}&amp;month={$tmpmonth}&amp;year={$tmp->y}&amp;filterev={$filterev}&amp;filterof={$filterof}&amp;filtersx={$filtersx}&amp;action={$action}\">{$cal_name}</a>";
		}
	}
}
echo "</td></tr>";
echo "</table>";

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
	$cal_date->d = 0;
	$cal_date->setJdFromYmd();
	// Make a separate list for each day.  Unspecified/invalid days go in day 0.
	$found_facts = array();
	for ($d = 0; $d <= $days_in_month; ++$d) {
		$found_facts[$d] = array();
	}
	// Fetch events for each day
	for ($jd = $cal_date->minJD; $jd <= $cal_date->maxJD; ++$jd)
		foreach (apply_filter(get_anniversary_events($jd, $events), $filterof, $filtersx) as $fact) {
			$tmp = $fact->getDate()->minDate();
			if ($tmp->d >= 1 && $tmp->d <= $tmp->daysInMonth()) {
				// If the day is valid (for its own calendar), display it in the
				// anniversary day (for the display calendar).
				$found_facts[$jd-$cal_date->minJD + 1][] = $fact;
			} else {
				// Otherwise, display it in the "Day not set" box.
				$found_facts[0][] = $fact;
			}
		}
	break;
case 'year':
	$cal_date->m=0;
	$cal_date->setJdFromYmd();
	$found_facts=apply_filter(get_calendar_events($ged_date->MinJD(), $ged_date->MaxJD(), $events), $filterof, $filtersx);
	// Eliminate duplicates (e.g. BET JUL 1900 AND SEP 1900 will appear twice in 1900)
	$found_facts = array_unique($found_facts);
	break;
}

// Group the facts by family/individual
switch ($action) {
case 'year':
case 'today':
	$indis=array();
	$fams=array();
	foreach ($found_facts as $fact) {
		$record = $fact->getParent();
		$xref   = $record->getXref();
		if ($record instanceof WT_Individual) {
			if (empty($indis[$xref])) {
				$indis[$xref] = calendar_fact_text($fact, true);
			} else {
				$indis[$xref] .= '<br>' . calendar_fact_text($fact, true);
			}
		} elseif ($record instanceof WT_Family) {
			if (empty($indis[$xref])) {
				$fams[$xref] = calendar_fact_text($fact, true);
			} else {
				$fams[$xref] .= '<br>' . calendar_fact_text($fact, true);
			}
		}
	}
	break;
case 'calendar':
	$cal_facts=array();
	foreach ($found_facts as $d=>$facts) {
		$cal_facts[$d]=array();
		foreach ($facts as $fact) {
			$xref=$fact->getParent()->getXref();
			if (empty($cal_facts[$d][$xref]))
				$cal_facts[$d][$xref]=calendar_fact_text($fact, false);
			else
				$cal_facts[$d][$xref].='<br>'.calendar_fact_text($fact, false);
		}
	}
	break;
}

switch ($action) {
case 'year':
case 'today':
	echo '<table class="width100"><tr>';
	// Table headings
	echo '<td class="descriptionbox center width50"><i class="icon-indis"></i>', WT_I18N::translate('Individuals'), '</td>';
	echo '<td class="descriptionbox center width50"><i class="icon-cfamily"></i>', WT_I18N::translate('Families'), '</td>';
	echo '</tr><tr>';
	// Table rows
	$males=0;
	$females=0;
	$numfams=0;
	echo '<td class="optionbox wrap">';

	// Avoid an empty unordered list
	$content = calendar_list_text($indis, '<li>', '</li>', true);
	if ($content) {
		echo '<ul>', $content, '</ul>';
	}

	echo '</td>';
	echo '<td class="optionbox wrap">';

	// Avoid an empty unordered list
	$content = calendar_list_text($fams, '<li>', '</li>', true);
	if ($content) {
		echo '<ul>', $content, '</ul>';
	}

	echo '</td>';
	echo "</tr><tr>";
	// Table footers
	echo '<td class="descriptionbox">', WT_I18N::translate('Total individuals: %s', count($indis));
	echo '<br>';
	echo '<i class="icon-sex_m_15x15" title="', WT_I18N::translate('Males'), '"></i> ', $males, '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<i class="icon-sex_f_15x15" title="', WT_I18N::translate('Males'), '"></i> ', $females, '&nbsp;&nbsp;&nbsp;&nbsp;';
	if (count($indis)!=$males+$females) {
		echo '<i class="icon-sex_u_15x15" title="', WT_I18N::translate('All individuals'), '"></i> ', count($indis)-$males-$females;
	}
	echo '</td>';
	echo '<td class="descriptionbox">', WT_I18N::translate('Total families: %s', count($fams)), '</td>';
	echo '</tr></table>';

	break;
case 'calendar':
	// We use JD%7 = 0/Mon...6/Sun.  Config files use 0/Sun...6/Sat.  Add 6 to convert.
	$week_start=($WEEK_START+6)%$days_in_week;
	// The french  calendar has a 10-day week, but our config only lets us choose
	// mon-sun as a start day.  Force french calendars to start on primidi
	if ($days_in_week==10) {
		$week_start=0;
	}
	echo "<table class=\"list_table width100\"><tr>";
	for ($week_day=0; $week_day<$days_in_week; ++$week_day) {
		$day_name=$cal_date->dayNames(($week_day+$week_start) % $days_in_week);
		echo "<td class=\"descriptionbox\" width=\"".(100/$days_in_week)."%\">{$day_name}</td>";
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
				echo '<span class="cal_day">', WT_I18N::translate('Day not set'), '</span><br style="clear: both;">';
				echo '<div class="details1" style="height: 150px; overflow: auto;">';
				echo calendar_list_text($cal_facts[0], '', '', false);
				echo '</div>';
				$cal_facts[0]=array();
			} else
				echo '&nbsp;';
		else {
			// Format the day number using the calendar
			$tmp=new WT_Date($cal_date->format("%@ {$d} %O %E")); $d_fmt=$tmp->date1->format('%j');
			if ($d==$today->d && $cal_date->m==$today->m)
				echo "<span class=\"cal_day current_day\">{$d_fmt}</span>";
			else
				echo "<span class=\"cal_day\">{$d_fmt}</span>";
			// Show a converted date
			foreach (explode('_and_', $CALENDAR_FORMAT) as $convcal) {
				switch ($convcal) {
				case 'french':
					$alt_date = new WT_Date_French($cal_date->minJD + $d - 1);
					break;
				case 'gregorian':
					$alt_date = new WT_Date_Gregorian($cal_date->minJD + $d - 1);
					break;
				case 'jewish':
					$alt_date = new WT_Date_Jewish($cal_date->minJD + $d - 1);
					break;
				case 'julian':
					$alt_date = new WT_Date_Julian($cal_date->minJD + $d - 1);
					break;
				case 'hijri':
					$alt_date = new WT_Date_Hijri($cal_date->minJD + $d - 1);
					break;
				case 'jalali':
					$alt_date = new WT_Date_Jalali($cal_date->minJD + $d - 1);
					break;
				default:
					break 2;
				}
				if (get_class($alt_date)!=get_class($cal_date)) {
					echo "<span class=\"rtl_cal_day\">".$alt_date->format("%j %M")."</span>";
					// Just show the first conversion
					break;
				}
			}
			echo '<br style="clear: both;"><div class="details1" style="height: 150px; overflow: auto;">';
			echo calendar_list_text($cal_facts[$d], '', '', false);
			echo '</div>';
		}
		echo '</td>';
		if (($d+$cal_date->minJD-$week_start) % $days_in_week==0) {
			echo '</tr>';
		}
	}
	echo '</table>';
	break;
}
echo '</div>'; //close "calendar-page"

/////////////////////////////////////////////////////////////////////////////////
// Filter a list of anniversaries
/////////////////////////////////////////////////////////////////////////////////
function apply_filter($facts, $filterof, $filtersx) {
	$filtered = array();
	$hundred_years = WT_CLIENT_JD - 36525;
	foreach ($facts as $fact) {
		$record = $fact->getParent();
		if ($filtersx) {
			// Filter on sex
			if ($record instanceof WT_Individual && $filtersx != $record->getSex()) {
				continue;
			}
			// Can't display families if the sex filter is on.
			if ($record instanceof WT_Family) {
				continue;
			}
		}
		// Filter living individuals
		if ($filterof == 'living') {
			if ($record instanceof WT_Individual && $record->isDead()) {
				continue;
			}
			if ($record instanceof WT_Family) {
				$husb = $record->getHusband();
				$wife = $record->getWife();
				if ($husb && $husb->isDead() || $wife && $wife->isDead()) {
					continue;
				}
			}
		}
		// Filter on recent events
		if ($filterof == 'recent' && $fact->getDate()->MaxJD()<$hundred_years) {
			continue;
		}
		$filtered[] = $fact;
	}
	return $filtered;
}

////////////////////////////////////////////////////////////////////////////////
// Format an anniversary display.
////////////////////////////////////////////////////////////////////////////////
function calendar_fact_text(WT_Fact $fact, $show_places) {
	$text = $fact->getLabel().' — '.$fact->getDate()->display(true, null, false);
	if ($fact->anniv) {
		$text .= ' (' . WT_I18N::translate('%s year anniversary', $fact->anniv) . ')';
	}
	if ($show_places && $fact->getAttribute('PLAC')) {
		$text .= ' — ' . $fact->getAttribute('PLAC');
	}
	return $text;
}

////////////////////////////////////////////////////////////////////////////////
// Format a list of facts for display
////////////////////////////////////////////////////////////////////////////////
function calendar_list_text($list, $tag1, $tag2, $show_sex_symbols) {
	global $males, $females;

	$html = '';

	foreach ($list as $id=>$facts) {
		$tmp = WT_GedcomRecord::GetInstance($id);
		$html .= $tag1 . '<a href="' . $tmp->getHtmlUrl() . '">' . $tmp->getFullName() . '</a> ';
		if ($show_sex_symbols && $tmp instanceof WT_Individual)
			switch ($tmp->getSex()) {
			case 'M':
				$html .= '<i class="icon-sex_m_9x9" title="' . WT_I18N::translate('Male') . '"></i>';
				++$males;
				break;
			case 'F':
				$html .= '<i class="icon-sex_f_9x9" title="' . WT_I18N::translate('Female') . '"></i>';
				++$females;
				break;
			default:
				$html .= '<i class="icon-sex_u_9x9" title="' . WT_I18N::translate_c('unknown gender', 'Unknown') . '"></i>';
				break;
			}
		$html .= '<div class="indent">' . $facts . '</div>' . $tag2;
	}

	return $html;
}
