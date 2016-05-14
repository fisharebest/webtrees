<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'calendar.php');
require './includes/session.php';

$CALENDAR_FORMAT = $WT_TREE->getPreference('CALENDAR_FORMAT');

$cal      = Filter::get('cal', '@#D[A-Z ]+@');
$day      = Filter::get('day', '\d\d?');
$month    = Filter::get('month', '[A-Z]{3,5}');
$year     = Filter::get('year', '\d{1,4}(?: B\.C\.)?|\d\d\d\d\/\d\d|\d+(-\d+|[?]+)?');
$view     = Filter::get('view', 'day|month|year', 'day');
$filterev = Filter::get('filterev', '[_A-Z-]+', 'BIRT-MARR-DEAT');
$filterof = Filter::get('filterof', 'all|living|recent', 'all');
$filtersx = Filter::get('filtersx', '[MF]', '');

if ($cal . $day . $month . $year === '') {
	// No date specified? Use the most likely calendar
	$cal = I18N::defaultCalendar()->gedcomCalendarEscape();
}

// Create a CalendarDate from the parameters

// We cannot display new-style/old-style years, so convert to new style
if (preg_match('/^(\d\d)\d\d\/(\d\d)$/', $year, $match)) {
	$year = $match[1] . $match[2];
}

// advanced-year "year range"
if (preg_match('/^(\d+)-(\d+)$/', $year, $match)) {
	if (strlen($match[1]) > strlen($match[2])) {
		$match[2] = substr($match[1], 0, strlen($match[1]) - strlen($match[2])) . $match[2];
	}
	$ged_date = new Date("FROM {$cal} {$match[1]} TO {$cal} {$match[2]}");
	$view     = 'year';
} else {
	// advanced-year "decade/century wildcard"
	if (preg_match('/^(\d+)(\?+)$/', $year, $match)) {
		$y1       = $match[1] . str_replace('?', '0', $match[2]);
		$y2       = $match[1] . str_replace('?', '9', $match[2]);
		$ged_date = new Date("FROM {$cal} {$y1} TO {$cal} {$y2}");
		$view     = 'year';
	} else {
		if ($year < 0) {
			$year = (-$year) . ' B.C.';
		} // need BC to parse date
		$ged_date = new Date("{$cal} {$day} {$month} {$year}");
		$year     = $ged_date->minimumDate()->y; // need negative year for year entry field.
	}
}
$cal_date = $ged_date->minimumDate();

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
	$year = $cal_date->y;
}

// Extract values from date
$days_in_month = $cal_date->daysInMonth();
$days_in_week  = $cal_date->daysInWeek();
$cal_month     = $cal_date->format('%O');
$today_month   = $today->format('%O');

// Invalid dates? Go to monthly view, where they'll be found.
if ($cal_date->d > $days_in_month && $view === 'day') {
	$view = 'month';
}

// All further uses of $cal are to generate URLs
$cal = rawurlencode($cal);

$controller = new PageController;
$controller->setPageTitle(I18N::translate('Anniversary calendar'));

switch ($view) {
case 'day':
	$controller->setPageTitle(I18N::translate('On this day…') . ' ' . $ged_date->display(false));
	break;
case 'month':
	$controller->setPageTitle(I18N::translate('In this month…') . ' ' . $ged_date->display(false, '%F %Y'));
	break;
case 'year':
	$controller->setPageTitle(I18N::translate('In this year…') . ' ' . $ged_date->display(false, '%Y'));
	break;
}

$controller->pageHeader();

?>
<div id="calendar-page">
	<table class="facts_table width100">
		<tbody>
			<tr>
				<td class="facts_label">
					<h2><?php echo $controller->getPageTitle() ?></h2>
				</td>
			</tr>
		</tbody>
	</table>

	<form name="dateform">
		<input type="hidden" name="cal" value="<?php echo $cal ?>">
		<input type="hidden" name="day" value="<?php echo $cal_date->d ?>">
		<input type="hidden" name="month" value="<?php echo $cal_month ?>">
		<input type="hidden" name="year" value="<?php echo $cal_date->y ?>">
		<input type="hidden" name="view" value="<?php echo $view ?>">
		<input type="hidden" name="filterev" value="<?php echo $filterev ?>">
		<input type="hidden" name="filtersx" value="<?php echo $filtersx ?>">
		<input type="hidden" name="filterof" value="<?php echo $filterof ?>">

		<table class="facts_table width100">
			<tr>
				<td class="descriptionbox vmiddle">
					<?php echo I18N::translate('Day') ?>
				</td>
				<td colspan="3" class="optionbox">
					<?php
					for ($d = 1; $d <= $days_in_month; $d++) {
						// Format the day number using the calendar
						$tmp   = new Date($cal_date->format("%@ {$d} %O %E"));
						$d_fmt = $tmp->minimumDate()->format('%j');
						if ($d === $cal_date->d) {
							echo '<span class="error">', $d_fmt, '</span>';
						} else {
							echo '<a href="?cal=', $cal, '&amp;day=', $d, '&amp;month=', $cal_month, '&amp;year=', $cal_date->y, '&amp;filterev=', $filterev, '&amp;filterof=', $filterof, '&amp;filtersx=', $filtersx, '&amp;view=', $view, '">', $d_fmt, '</a>';
						}
						echo ' | ';
					}
					?>
					<a href="?cal=<?php echo $cal ?>&amp;day=<?php echo $today->d ?>&amp;month=<?php echo $today_month ?>&amp;year=<?php echo $today->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=<?php echo $view ?>">
						<b><?php $tmp = new Date($today->format('%@ %A %O %E')); echo $tmp->display() ?></b>
					</a>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo I18N::translate('Month') ?>
				</td>
				<td class="optionbox" colspan="3">
					<?php
					for ($n = 1, $months_in_year = $cal_date->monthsInYear(); $n <= $months_in_year; ++$n) {
						$month_name = $cal_date->monthNameNominativeCase($n, $cal_date->isLeapYear());
						$m          = array_search($n, $cal_date::$MONTH_ABBREV);
						if ($n === 6 && $cal_date instanceof JewishDate && !$cal_date->isLeapYear()) {
							// No month 6 in Jewish non-leap years.
							continue;
						}
						if ($n === 7 && $cal_date instanceof JewishDate && !$cal_date->isLeapYear()) {
							// Month 7 is ADR in Jewish non-leap years.
							$m = 'ADR';
						}
						if ($n === $cal_date->m) {
							$month_name = '<span class="error">' . $month_name . '</span>';
						}
						echo '<a href="?cal=', $cal, '&amp;day=', $cal_date->d, '&amp;month=', $m, '&amp;year=', $cal_date->y, '&amp;filterev=', $filterev, '&amp;filterof=', $filterof, '&amp;filtersx=', $filtersx, '&amp;view=', $view, '">', $month_name, '</a>';
						echo ' | ';
					}
					?>
					<a href="?cal=<?php echo $cal ?>&amp;day=<?php echo min($cal_date->d, $today->daysInMonth()) ?>&amp;month=<?php echo $today_month ?>&amp;year=<?php echo $today->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=<?php echo $view ?>">
						<b><?php echo $today->format('%F %Y') ?></b>
					</a>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox vmiddle">
					<label for="year"><?php echo I18N::translate('Year') ?></label>
				</td>
				<td class="optionbox vmiddle">
					<a href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y === 1 ? -1 : $cal_date->y - 1 ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=<?php echo $view ?>">
						-1
					</a>
					<input type="text" id="year" name="year" value="<?php echo $year ?>" size="4">
					<a href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y === -1 ? 1 : $cal_date->y + 1 ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=<?php echo $view ?>">
						+1
					</a>
					|
					<a href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $today->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=<?php echo $view ?>">
						<?php echo $today->format('%Y') ?>
					</a>
					<?php echo FunctionsPrint::helpLink('annivers_year_select') ?>
				</td>

				<td class="descriptionbox vmiddle">
					<?php echo I18N::translate('Show') ?>
				</td>

				<td class="optionbox vmiddle">
					<?php if (!$WT_TREE->getPreference('HIDE_LIVE_PEOPLE') || Auth::check()): ?>
					<select class="list_value" name="filterof" onchange="document.dateform.submit();">
						<option value="all" <?php echo $filterof === 'all' ? 'selected' : '' ?>>
							<?php echo I18N::translate('All individuals') ?>
						</option>
						<option value="living" <?php echo $filterof === 'living' ? 'selected' : '' ?>>
							<?php echo I18N::translate('Living individuals') ?>
						</option>
						<option value="recent" <?php echo $filterof === 'recent' ? 'selected' : '' ?>>
							<?php echo I18N::translate('Recent years (&lt; 100 yrs)') ?>
						</option>
					</select>
					<?php endif; ?>

					<a title="<?php echo I18N::translate('All individuals') ?>" href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;view=<?php echo $view ?>">
						<i class="<?php echo $filtersx === '' ? 'icon-sex_m_15x15' : 'icon-sex_m_9x9' ?>"></i>
						<i class="<?php echo $filtersx === '' ? 'icon-sex_f_15x15' : 'icon-sex_f_9x9' ?>"></i>
					</a>
					|
					<a title="<?php echo I18N::translate('Males') ?>" href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=M&amp;view=<?php echo $view ?>">
						<i class="<?php echo $filtersx === 'M' ? 'icon-sex_m_15x15' : 'icon-sex_m_9x9' ?>"></i>
					</a>
					|
					<a title="<?php echo I18N::translate('Females') ?>" href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=F&amp;view=<?php echo $view ?>">
						<i class="<?php echo $filtersx === 'F' ? 'icon-sex_f_15x15' : 'icon-sex_f_9x9' ?>"></i>
					</a>

					<select class="list_value" name="filterev" onchange="document.dateform.submit();">
						<option value="BIRT-MARR-DEAT" <?php echo $filterev === 'BIRT-MARR-DEAT' ? 'selected' : '' ?>>
							<?php echo I18N::translate('Vital records') ?>
						</option>
						<option value="" <?php echo $filterev === '' ? 'selected' : '' ?>>
							<?php echo I18N::translate('All') ?>
						</option>
						<option value="BIRT" <?php echo $filterev === 'BIRT' ? 'selected' : '' ?>>
							<?php echo GedcomTag::getLabel('BIRT') ?>
						</option>
						<option value="BAPM-CHR-CHRA" <?php echo $filterev === 'BAPM-CHR-CHRA' ? 'selected' : '' ?>>
							<?php echo GedcomTag::getLabel('BAPM') ?>
						</option>
						<option value="MARR-_COML-_NMR" <?php echo $filterev === 'MARR-_COML-_NMR' ? 'selected' : '' ?>>
							<?php echo GedcomTag::getLabel('MARR') ?>
						</option>
						<option value="DIV-_SEPR" <?php echo $filterev === 'DIV-_SEPR' ? 'selected' : '' ?>>
							<?php echo GedcomTag::getLabel('DIV') ?>
						</option>
						<option value="DEAT" <?php echo $filterev === 'DEAT' ? 'selected' : '' ?>>
							<?php echo GedcomTag::getLabel('DEAT') ?>
						</option>
						<option value="BURI" <?php echo $filterev === 'BURI' ? 'selected' : '' ?>>
							<?php echo GedcomTag::getLabel('BURI') ?>
						</option>
						<option value="IMMI,EMIG" <?php echo $filterev === 'IMMI,EMIG' ? 'selected' : '' ?>>
							<?php echo GedcomTag::getLabel('EMIG') ?>
						</option>
						<option value="EVEN" <?php echo $filterev === 'EVEN' ? 'selected' : '' ?>>
							<?php echo I18N::translate('Custom event') ?>
						</option>
					</select>
				</td>
			</tr>
		</table>

		<table class="width100">
			<tr>
				<td class="topbottombar width50">
					<a class="<?php echo $view === 'day' ? 'error' : '' ?>" href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=day">
						<?php echo I18N::translate('View the day') ?>
					</a>
					|
					<a class="<?php echo $view === 'month' ? 'error' : '' ?>" href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=month">
						<?php echo I18N::translate('View the month') ?>
					</a>
					|
					<a class="<?php echo $view === 'year' ? 'error' : '' ?>" href="?cal=<?php echo $cal ?>&amp;day=<?php echo $cal_date->d ?>&amp;month=<?php echo $cal_month ?>&amp;year=<?php echo $cal_date->y ?>&amp;filterev=<?php echo $filterev ?>&amp;filterof=<?php echo $filterof ?>&amp;filtersx=<?php echo $filtersx ?>&amp;view=year">
						<?php echo I18N::translate('View the year') ?>
					</a>
				</td>
				<td class="topbottombar width50">
					<?php
					$n = 0;
					foreach (Date::calendarNames() as $newcal => $cal_name) {
						$tmp = $cal_date->convertToCalendar($newcal);
						if ($tmp->inValidRange()) {
							if ($n++) {
								echo ' | ';
							}
							if (get_class($tmp) === get_class($cal_date)) {
								echo '<span class="error">', $cal_name, '</span>';
							} else {
								$newcalesc = urlencode($tmp->format('%@'));
								$tmpmonth  = $tmp->format('%O');
								echo '<a href="?cal=', $newcalesc, '&amp;day=', $tmp->d, '&amp;month=', $tmpmonth, '&amp;year=', $tmp->y, '&amp;filterev=', $filterev, '&amp;filterof=', $filterof, '&amp;filtersx=', $filtersx, '&amp;view=', $view, '">', $cal_name, '</a>';
							}
						}
					}
					?>
				</td>
			</tr>
		</table>
	</form>
<?php

// Fetch data for day/month/year views
$found_facts = array();

switch ($view) {
case 'day':
	$found_facts = apply_filter(FunctionsDb::getAnniversaryEvents($cal_date->minJD, $filterev, $WT_TREE), $filterof, $filtersx);
	break;
case 'month':
	$cal_date->d = 0;
	$cal_date->setJdFromYmd();
	// Make a separate list for each day. Unspecified/invalid days go in day 0.
	for ($d = 0; $d <= $days_in_month; ++$d) {
		$found_facts[$d] = array();
	}
	// Fetch events for each day
	for ($jd = $cal_date->minJD; $jd <= $cal_date->maxJD; ++$jd) {
		foreach (apply_filter(FunctionsDb::getAnniversaryEvents($jd, $filterev, $WT_TREE), $filterof, $filtersx) as $fact) {
			$tmp = $fact->getDate()->minimumDate();
			if ($tmp->d >= 1 && $tmp->d <= $tmp->daysInMonth()) {
				// If the day is valid (for its own calendar), display it in the
				// anniversary day (for the display calendar).
				$found_facts[$jd - $cal_date->minJD + 1][] = $fact;
			} else {
				// Otherwise, display it in the "Day not set" box.
				$found_facts[0][] = $fact;
			}
		}
	}
	break;
case 'year':
	$cal_date->m = 0;
	$cal_date->setJdFromYmd();
	$found_facts = apply_filter(FunctionsDb::getCalendarEvents($ged_date->minimumJulianDay(), $ged_date->maximumJulianDay(), $filterev, $WT_TREE), $filterof, $filtersx);
	// Eliminate duplicates (e.g. BET JUL 1900 AND SEP 1900 will appear twice in 1900)
	$found_facts = array_unique($found_facts);
	break;
}

// Group the facts by family/individual
$indis     = array();
$fams      = array();
$cal_facts = array();

switch ($view) {
case 'year':
case 'day':
	foreach ($found_facts as $fact) {
		$record = $fact->getParent();
		$xref   = $record->getXref();
		if ($record instanceof Individual) {
			if (empty($indis[$xref])) {
				$indis[$xref] = calendar_fact_text($fact, true);
			} else {
				$indis[$xref] .= '<br>' . calendar_fact_text($fact, true);
			}
		} elseif ($record instanceof Family) {
			if (empty($indis[$xref])) {
				$fams[$xref] = calendar_fact_text($fact, true);
			} else {
				$fams[$xref] .= '<br>' . calendar_fact_text($fact, true);
			}
		}
	}
	break;
case 'month':
	foreach ($found_facts as $d => $facts) {
		$cal_facts[$d] = array();
		foreach ($facts as $fact) {
			$xref = $fact->getParent()->getXref();
			if (empty($cal_facts[$d][$xref])) {
				$cal_facts[$d][$xref] = calendar_fact_text($fact, false);
			} else {
				$cal_facts[$d][$xref] .= '<br>' . calendar_fact_text($fact, false);
			}
		}
	}
	break;
}

switch ($view) {
case 'year':
case 'day':
	$males   = 0;
	$females = 0;
	echo '<table class="width100"><tr>';
	echo '<td class="descriptionbox center width50"><i class="icon-indis"></i>', I18N::translate('Individuals'), '</td>';
	echo '<td class="descriptionbox center width50"><i class="icon-cfamily"></i>', I18N::translate('Families'), '</td>';
	echo '</tr><tr>';
	echo '<td class="optionbox wrap">';

	$content = calendar_list_text($indis, '<li>', '</li>', true);
	if ($content) {
		echo '<ul>', $content, '</ul>';
	}

	echo '</td>';
	echo '<td class="optionbox wrap">';

	$content = calendar_list_text($fams, '<li>', '</li>', true);
	if ($content) {
		echo '<ul>', $content, '</ul>';
	}

	echo '</td>';
	echo '</tr><tr>';
	echo '<td class="descriptionbox">', I18N::translate('Total individuals: %s', count($indis));
	echo '<br>';
	echo '<i class="icon-sex_m_15x15" title="', I18N::translate('Males'), '"></i> ', $males, ' ';
	echo '<i class="icon-sex_f_15x15" title="', I18N::translate('Females'), '"></i> ', $females, ' ';
	if (count($indis) !== $males + $females) {
		echo '<i class="icon-sex_u_15x15" title="', I18N::translate('All individuals'), '"></i> ', count($indis) - $males - $females;
	}
	echo '</td>';
	echo '<td class="descriptionbox">', I18N::translate('Total families: %s', count($fams)), '</td>';
	echo '</tr></table>';

	break;
case 'month':
// We use JD%7 = 0/Mon…6/Sun. Standard definitions use 0/Sun…6/Sat.
	$week_start    = (I18N::firstDay() + 6) % 7;
	$weekend_start = (I18N::weekendStart() + 6) % 7;
	$weekend_end   = (I18N::weekendEnd() + 6) % 7;
	// The french  calendar has a 10-day week, which starts on primidi
	if ($days_in_week === 10) {
		$week_start    = 0;
		$weekend_start = -1;
		$weekend_end   = -1;
	}
	echo '<table class="width100"><thead><tr>';
	for ($week_day = 0; $week_day < $days_in_week; ++$week_day) {
		$day_name = $cal_date->dayNames(($week_day + $week_start) % $days_in_week);
		if ($week_day == $weekend_start || $week_day == $weekend_end) {
			echo '<th class="descriptionbox weekend" width="' . (100 / $days_in_week) . '%">', $day_name, '</th>';
		} else {
			echo '<th class="descriptionbox" width="' . (100 / $days_in_week) . '%">', $day_name, '</th>';
		}
	}
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	// Print days 1 to n of the month, but extend to cover "empty" days before/after the month to make whole weeks.
	// e.g. instead of 1 -> 30 (=30 days), we might have -1 -> 33 (=35 days)
	$start_d = 1 - ($cal_date->minJD - $week_start) % $days_in_week;
	$end_d   = $days_in_month + ($days_in_week - ($cal_date->maxJD - $week_start + 1) % $days_in_week) % $days_in_week;
	// Make sure that there is an empty box for any leap/missing days
	if ($start_d === 1 && $end_d === $days_in_month && count($found_facts[0]) > 0) {
		$end_d += $days_in_week;
	}
	for ($d = $start_d; $d <= $end_d; ++$d) {
		if (($d + $cal_date->minJD - $week_start) % $days_in_week === 1) {
			echo '<tr>';
		}
		echo '<td class="optionbox wrap">';
		if ($d < 1 || $d > $days_in_month) {
			if (count($cal_facts[0]) > 0) {
				echo '<span class="cal_day">', I18N::translate('Day not set'), '</span><br style="clear: both;">';
				echo '<div class="details1" style="height: 180px; overflow: auto;">';
				echo calendar_list_text($cal_facts[0], '', '', false);
				echo '</div>';
				$cal_facts[0] = array();
			}
		} else {
			// Format the day number using the calendar
			$tmp   = new Date($cal_date->format("%@ {$d} %O %E"));
			$d_fmt = $tmp->minimumDate()->format('%j');
			if ($d === $today->d && $cal_date->m === $today->m) {
				echo '<span class="cal_day current_day">', $d_fmt, '</span>';
			} else {
				echo '<span class="cal_day">', $d_fmt, '</span>';
			}
			// Show a converted date
			foreach (explode('_and_', $CALENDAR_FORMAT) as $convcal) {
				switch ($convcal) {
				case 'french':
					$alt_date = new FrenchDate($cal_date->minJD + $d - 1);
					break;
				case 'gregorian':
					$alt_date = new GregorianDate($cal_date->minJD + $d - 1);
					break;
				case 'jewish':
					$alt_date = new JewishDate($cal_date->minJD + $d - 1);
					break;
				case 'julian':
					$alt_date = new JulianDate($cal_date->minJD + $d - 1);
					break;
				case 'hijri':
					$alt_date = new HijriDate($cal_date->minJD + $d - 1);
					break;
				case 'jalali':
					$alt_date = new JalaliDate($cal_date->minJD + $d - 1);
					break;
				default:
					break 2;
				}
				if (get_class($alt_date) !== get_class($cal_date)) {
					echo '<span class="rtl_cal_day">' . $alt_date->format("%j %M") . '</span>';
					// Just show the first conversion
					break;
				}
			}
			echo '<br style="clear: both;"><div class="details1" style="height: 180px; overflow: auto;">';
			echo calendar_list_text($cal_facts[$d], '', '', false);
			echo '</div>';
		}
		echo '</td>';
		if (($d + $cal_date->minJD - $week_start) % $days_in_week === 0) {
			echo '</tr>';
		}
	}
	echo '</tbody>';
	echo '</table>';
	break;
}
echo '</div>'; //close "calendar-page"

/**
 * Filter a list of anniversaries
 *
 * @param Fact[] $facts
 * @param string $filterof
 * @param string $filtersx
 *
 * @return array
 */
function apply_filter($facts, $filterof, $filtersx) {
	$filtered      = array();
	$hundred_years = WT_CLIENT_JD - 36525;
	foreach ($facts as $fact) {
		$record = $fact->getParent();
		if ($filtersx) {
			// Filter on sex
			if ($record instanceof Individual && $filtersx !== $record->getSex()) {
				continue;
			}
			// Can't display families if the sex filter is on.
			if ($record instanceof Family) {
				continue;
			}
		}
		// Filter living individuals
		if ($filterof === 'living') {
			if ($record instanceof Individual && $record->isDead()) {
				continue;
			}
			if ($record instanceof Family) {
				$husb = $record->getHusband();
				$wife = $record->getWife();
				if ($husb && $husb->isDead() || $wife && $wife->isDead()) {
					continue;
				}
			}
		}
		// Filter on recent events
		if ($filterof === 'recent' && $fact->getDate()->maximumJulianDay() < $hundred_years) {
			continue;
		}
		$filtered[] = $fact;
	}

	return $filtered;
}

/**
 * Format an anniversary display.
 *
 * @param Fact $fact
 * @param bool $show_places
 *
 * @return string
 */
function calendar_fact_text(Fact $fact, $show_places) {
	$text = $fact->getLabel() . ' — ' . $fact->getDate()->display(true, null, false);
	if ($fact->anniv) {
		$text .= ' (' . I18N::translate('%s year anniversary', $fact->anniv) . ')';
	}
	if ($show_places && $fact->getAttribute('PLAC')) {
		$text .= ' — ' . $fact->getAttribute('PLAC');
	}

	return $text;
}

/**
 * Format a list of facts for display
 *
 * @param Fact[] $list
 * @param string $tag1
 * @param string $tag2
 * @param bool   $show_sex_symbols
 *
 * @return string
 */
function calendar_list_text($list, $tag1, $tag2, $show_sex_symbols) {
	global $males, $females, $WT_TREE;

	$html = '';

	foreach ($list as $id => $facts) {
		$tmp = GedcomRecord::getInstance($id, $WT_TREE);
		$html .= $tag1 . '<a href="' . $tmp->getHtmlUrl() . '">' . $tmp->getFullName() . '</a> ';
		if ($show_sex_symbols && $tmp instanceof Individual) {
			switch ($tmp->getSex()) {
			case 'M':
				$html .= '<i class="icon-sex_m_9x9" title="' . I18N::translate('Male') . '"></i>';
				++$males;
				break;
			case 'F':
				$html .= '<i class="icon-sex_f_9x9" title="' . I18N::translate('Female') . '"></i>';
				++$females;
				break;
			default:
				$html .= '<i class="icon-sex_u_9x9" title="' . I18N::translateContext('unknown gender', 'Unknown') . '"></i>';
				break;
			}
		}
		$html .= '<div class="indent">' . $facts . '</div>' . $tag2;
	}

	return $html;
}
