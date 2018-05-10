<?php
	/**
	 * webtrees: online genealogy
	 * Copyright (C) 2018 webtrees development team
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
	declare(strict_types=1);

	namespace Fisharebest\Webtrees\Http\Controllers;

	use Fisharebest\Webtrees\Date;
	use Fisharebest\Webtrees\Date\FrenchDate;
	use Fisharebest\Webtrees\Date\GregorianDate;
	use Fisharebest\Webtrees\Date\HijriDate;
	use Fisharebest\Webtrees\Date\JalaliDate;
	use Fisharebest\Webtrees\Date\JewishDate;
	use Fisharebest\Webtrees\Date\JulianDate;
	use Fisharebest\Webtrees\Fact;
	use Fisharebest\Webtrees\Family;
	use Fisharebest\Webtrees\Functions\FunctionsDb;
	use Fisharebest\Webtrees\GedcomRecord;
	use Fisharebest\Webtrees\I18N;
	use Fisharebest\Webtrees\Individual;
	use Fisharebest\Webtrees\Tree;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/**
	 * Show anniveraries for events in a given day/month/year.
	 */
	class CalendarController extends AbstractBaseController {
		/**
		 * A form to request the page parameters.
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function page(Request $request): Response {
			$cal      = $request->get('cal', '');
			$day      = $request->get('day', '');
			$month    = $request->get('month', '');
			$year     = $request->get('year', '');
			$view     = $request->get('view', 'day');
			$filterev = $request->get('filterev', 'BIRT-MARR-DEAT');
			$filterof = $request->get('filterof', 'all');
			$filtersx = $request->get('filtersx', '');

			if ($cal . $day . $month . $year === '') {
				// No date specified? Use the most likely calendar
				$cal = I18N::defaultCalendar()->gedcomCalendarEscape();
			}

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
			$cal_month     = $cal_date->format('%O');
			$today_month   = $today->format('%O');

			// Invalid dates? Go to monthly view, where they'll be found.
			if ($cal_date->d > $days_in_month && $view === 'day') {
				$view = 'month';
			}

			$title = I18N::translate('Anniversary calendar');

			switch ($view) {
				case 'day':
					$title = I18N::translate('On this day…') . ' ' . $ged_date->display(false);
					break;
				case 'month':
					$title = I18N::translate('In this month…') . ' ' . $ged_date->display(false, '%F %Y');
					break;
				case 'year':
					$title = I18N::translate('In this year…') . ' ' . $ged_date->display(false, '%Y');
					break;
			}

			return $this->viewResponse('calendar-page', [
				'cal'           => $cal,
				'cal_date'      => $cal_date,
				'cal_month'     => $cal_month,
				'day'           => $day,
				'days_in_month' => $days_in_month,
				'filterev'      => $filterev,
				'filterof'      => $filterof,
				'filtersx'      => $filtersx,
				'month'         => $month,
				'title'         => $title,
				'today'         => $today,
				'today_month'   => $today_month,
				'view'          => $view,
				'year'          => $year,
			]);
		}

		/**
		 * Show anniveraries that occured on a given day/month/year.
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function calendar(Request $request): Response {
			/** @var Tree $tree */
			$tree = $request->attributes->get('tree');

			$CALENDAR_FORMAT = $tree->getPreference('CALENDAR_FORMAT');

			$cal      = $request->get('cal', '');
			$day      = $request->get('day', '');
			$month    = $request->get('month', '');
			$year     = $request->get('year', '');
			$view     = $request->get('view', '');
			$filterev = $request->get('filterev', 'BIRT-MARR-DEAT');
			$filterof = $request->get('filterof', 'all');
			$filtersx = $request->get('filtersx', '');

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

			// Extract values from date
			$days_in_month = $cal_date->daysInMonth();
			$days_in_week  = $cal_date->daysInWeek();

			// Invalid dates? Go to monthly view, where they'll be found.
			if ($cal_date->d > $days_in_month && $view === 'day') {
				$view = 'month';
			}

			/** @var Fact[] $found_facts */
			$found_facts = [];

			switch ($view) {
				case 'day':
					$found_facts = $this->applyFilter(FunctionsDb::getAnniversaryEvents($cal_date->minJD, $filterev, $tree), $filterof, $filtersx);
					break;
				case 'month':
					$cal_date->d = 0;
					$cal_date->setJdFromYmd();
					// Make a separate list for each day. Unspecified/invalid days go in day 0.
					for ($d = 0; $d <= $days_in_month; ++$d) {
						$found_facts[$d] = [];
					}
					// Fetch events for each day
					for ($jd = $cal_date->minJD; $jd <= $cal_date->maxJD; ++$jd) {
						foreach ($this->applyFilter(FunctionsDb::getAnniversaryEvents($jd, $filterev, $tree), $filterof, $filtersx) as $fact) {
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
					$found_facts = $this->applyFilter(FunctionsDb::getCalendarEvents($ged_date->minimumJulianDay(), $ged_date->maximumJulianDay(), $filterev, $tree), $filterof, $filtersx);
					// Eliminate duplicates (e.g. BET JUL 1900 AND SEP 1900 will appear twice in 1900)
					$found_facts = array_unique($found_facts);
					break;
			}

			// Group the facts by family/individual
			$indis     = [];
			$fams      = [];
			$cal_facts = [];

			switch ($view) {
				case 'year':
				case 'day':
					foreach ($found_facts as $fact) {
						$record = $fact->getParent();
						$xref   = $record->getXref();
						if ($record instanceof Individual) {
							if (empty($indis[$xref])) {
								$indis[$xref] = $this->calendarFactText($fact, true);
							} else {
								$indis[$xref] .= '<br>' . $this->calendarFactText($fact, true);
							}
						} elseif ($record instanceof Family) {
							if (empty($indis[$xref])) {
								$fams[$xref] = $this->calendarFactText($fact, true);
							} else {
								$fams[$xref] .= '<br>' . $this->calendarFactText($fact, true);
							}
						}
					}
					break;
				case 'month':
					foreach ($found_facts as $d => $facts) {
						$cal_facts[$d] = [];
						foreach ($facts as $fact) {
							$xref = $fact->getParent()->getXref();
							if (empty($cal_facts[$d][$xref])) {
								$cal_facts[$d][$xref] = $this->calendarFactText($fact, false);
							} else {
								$cal_facts[$d][$xref] .= '<br>' . $this->calendarFactText($fact, false);
							}
						}
					}
					break;
			}

			ob_start();

			switch ($view) {
				case 'year':
				case 'day':
					echo '<table class="width100"><tr>';
					echo '<td class="descriptionbox center width50"><i class="icon-indis"></i>', I18N::translate('Individuals'), '</td>';
					echo '<td class="descriptionbox center width50"><i class="icon-cfamily"></i>', I18N::translate('Families'), '</td>';
					echo '</tr><tr>';
					echo '<td class="optionbox wrap">';

					$content = $this->calendarListText($indis, '<li>', '</li>', $tree);
					if ($content) {
						echo '<ul>', $content, '</ul>';
					}

					echo '</td>';
					echo '<td class="optionbox wrap">';

					$content = $this->calendarListText($fams, '<li>', '</li>', $tree);
					if ($content) {
						echo '<ul>', $content, '</ul>';
					}

					echo '</td>';
					echo '</tr><tr>';
					echo '<td class="descriptionbox">', I18N::translate('Total individuals: %s', count($indis)), '</td>';
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
								echo $this->calendarListText($cal_facts[0], '', '', $tree);
								echo '</div>';
								$cal_facts[0] = [];
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
								if (get_class($alt_date) !== get_class($cal_date) && $alt_date->inValidRange()) {
									echo '<span class="rtl_cal_day">' . $alt_date->format('%j %M') . '</span>';
									// Just show the first conversion
									break;
								}
							}
							echo '<br style="clear: both;"><div class="details1" style="height: 180px; overflow: auto;">';
							echo $this->calendarListText($cal_facts[$d], '', '', $tree);
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

			$html = ob_get_clean();

			return new Response($html);
		}

		/**
		 * Filter a list of anniversaries
		 *
		 * @param Fact[] $facts
		 * @param string $filterof
		 * @param string $filtersx
		 *
		 * @return Fact[]
		 */
		private function applyFilter(array $facts, string $filterof, string $filtersx): array  {
			$filtered      = [];
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
		private function calendarFactText(Fact $fact, bool $show_places): string {
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
		 * @param Tree   $tree
		 *
		 * @return string
		 */
		private function calendarListText(array $list, string $tag1, string $tag2, Tree $tree): string {
			$html = '';

			foreach ($list as $id => $facts) {
				$tmp  = GedcomRecord::getInstance($id, $tree);
				$html .= $tag1 . '<a href="' . e($tmp->url()) . '">' . $tmp->getFullName() . '</a> ';
				$html .= '<div class="indent">' . $facts . '</div>' . $tag2;
			}

			return $html;
		}
	}
