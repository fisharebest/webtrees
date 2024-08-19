<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function count;
use function e;
use function explode;
use function get_class;
use function ob_get_clean;
use function ob_start;
use function range;
use function response;
use function view;

/**
 * Show anniversaries for events in a given day/month/year.
 */
class CalendarEvents implements RequestHandlerInterface
{
    public function __construct(
        private readonly CalendarService $calendar_service,
    ) {
    }

    /**
     * Show anniversaries that occurred on a given day/month/year.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $view     = Validator::attributes($request)->isInArray(['day', 'month', 'year'])->string('view');
        $cal      = Validator::queryParams($request)->string('cal');
        $day      = Validator::queryParams($request)->string('day');
        $month    = Validator::queryParams($request)->string('month');
        $year     = Validator::queryParams($request)->string('year');
        $filterev = Validator::queryParams($request)->string('filterev');
        $filterof = Validator::queryParams($request)->string('filterof');
        $filtersx = Validator::queryParams($request)->string('filtersx');

        $ged_date = new Date($cal . ' ' . $day . ' ' . $month . ' ' . $year);
        $cal_date = $ged_date->minimumDate();
        $today    = $cal_date->today();

        $days_in_month = $cal_date->daysInMonth();
        $days_in_week  = $cal_date->daysInWeek();

        $CALENDAR_FORMAT = $tree->getPreference('CALENDAR_FORMAT');

        // Day and year share the same layout.
        if ($view !== 'month') {
            if ($view === 'day') {
                $anniversary_facts = $this->calendar_service->getAnniversaryEvents($cal_date->minimumJulianDay(), $filterev, $tree, $filterof, $filtersx);
            } else {
                $ged_year          = new Date($cal . ' ' . $year);
                $anniversary_facts = $this->calendar_service->getCalendarEvents($ged_year->minimumJulianDay(), $ged_year->maximumJulianDay(), $filterev, $tree, $filterof, $filtersx);
            }

            $anniversaries = Collection::make($anniversary_facts)
                ->unique()
                ->sort(static fn (Fact $x, Fact $y): int => $x->date()->minimumJulianDay() <=> $y->date()->minimumJulianDay());

            $family_anniversaries = $anniversaries->filter(static fn (Fact $f): bool => $f->record() instanceof Family);

            $individual_anniversaries = $anniversaries->filter(static fn (Fact $f): bool => $f->record() instanceof Individual);

            $family_count = $family_anniversaries
                ->map(static fn (Fact $x): string => $x->record()->xref())
                ->unique()
                ->count();

            $individual_count = $individual_anniversaries
                ->map(static fn (Fact $x): string => $x->record()->xref())
                ->unique()
                ->count();

            return response(view('calendar-list', [
                'family_anniversaries'     => $family_anniversaries,
                'individual_anniversaries' => $individual_anniversaries,
                'family_count'             => $family_count,
                'individual_count'         => $individual_count,
            ]));
        }

        $found_facts = [];

        $cal_date->day = 0;
        $cal_date->setJdFromYmd();
        // Make a separate list for each day. Unspecified/invalid days go in day 0.
        for ($d = 0; $d <= $days_in_month; ++$d) {
            $found_facts[$d] = [];
        }
        // Fetch events for each day
        $jds = range($cal_date->minimumJulianDay(), $cal_date->maximumJulianDay());

        foreach ($jds as $jd) {
            foreach ($this->calendar_service->getAnniversaryEvents($jd, $filterev, $tree, $filterof, $filtersx) as $fact) {
                $tmp = $fact->date()->minimumDate();
                if ($tmp->day >= 1 && $tmp->day <= $tmp->daysInMonth()) {
                    // If the day is valid (for its own calendar), display it in the
                    // anniversary day (for the display calendar).
                    $found_facts[$jd - $cal_date->minimumJulianDay() + 1][] = $fact;
                } else {
                    // Otherwise, display it in the "Day not set" box.
                    $found_facts[0][] = $fact;
                }
            }
        }

        $cal_facts = [];

        foreach ($found_facts as $d => $facts) {
            $cal_facts[$d] = [];
            foreach ($facts as $fact) {
                $xref = $fact->record()->xref();
                $text = $fact->label() . ' — ' . $fact->date()->display($tree);
                if ($fact->anniv > 0) {
                    $text .= ' (' . I18N::translate('%s year anniversary', I18N::number($fact->anniv)) . ')';
                }
                if (empty($cal_facts[$d][$xref])) {
                    $cal_facts[$d][$xref] = $text;
                } else {
                    $cal_facts[$d][$xref] .= '<br>' . $text;
                }
            }
        }
        // We use JD%7 = 0/Mon…6/Sun. Standard definitions use 0/Sun…6/Sat.
        $week_start    = (I18N::locale()->territory()->firstDay() + 6) % 7;
        $weekend_start = (I18N::locale()->territory()->weekendStart() + 6) % 7;
        $weekend_end   = (I18N::locale()->territory()->weekendEnd() + 6) % 7;

        // The French calendar has a 10-day week, which starts on primidi.
        if ($days_in_week === 10) {
            $week_start    = 0;
            $weekend_start = -1;
            $weekend_end   = -1;
        }

        ob_start();

        echo '<table class="w-100 wt-calendar-month"><thead><tr>';
        for ($week_day = 0; $week_day < $days_in_week; ++$week_day) {
            $day_name = $cal_date->dayNames(($week_day + $week_start) % $days_in_week);
            if ($week_day === $weekend_start || $week_day === $weekend_end) {
                echo '<th class="wt-page-options-label weekend" width="', 100 / $days_in_week, '%">', $day_name, '</th>';
            } else {
                echo '<th class="wt-page-options-label" width="', 100 / $days_in_week, '%">', $day_name, '</th>';
            }
        }
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        // Print days 1 to n of the month, but extend to cover "empty" days before/after the month to make whole weeks.
        // e.g. instead of 1 -> 30 (=30 days), we might have -1 -> 33 (=35 days)
        $start_d = 1 - ($cal_date->minimumJulianDay() - $week_start) % $days_in_week;
        $end_d   = $days_in_month + ($days_in_week - ($cal_date->maximumJulianDay() - $week_start + 1) % $days_in_week) % $days_in_week;
        // Make sure that there is an empty box for any leap/missing days
        if ($start_d === 1 && $end_d === $days_in_month && count($found_facts[0]) > 0) {
            $end_d += $days_in_week;
        }
        for ($d = $start_d; $d <= $end_d; ++$d) {
            if (($d + $cal_date->minimumJulianDay() - $week_start) % $days_in_week === 1) {
                echo '<tr>';
            }
            echo '<td class="wt-page-options-value">';
            if ($d < 1 || $d > $days_in_month) {
                if (count($cal_facts[0]) > 0) {
                    echo '<div class="cal_day">', I18N::translate('Day not set'), '</div>';
                    echo '<div class="small" style="height: 180px; overflow: auto;">';
                    echo $this->calendarListText($cal_facts[0], $tree);
                    echo '</div>';
                    $cal_facts[0] = [];
                }
            } else {
                // Format the day number using the calendar
                $tmp   = new Date($cal_date->format('%@ ' . $d . ' %O %E'));
                $d_fmt = $tmp->minimumDate()->format('%j');
                echo '<div class="d-flex d-flex justify-content-between">';
                if ($d === $today->day && $cal_date->month === $today->month) {
                    echo '<span class="cal_day current_day">', $d_fmt, '</span>';
                } else {
                    echo '<span class="cal_day">', $d_fmt, '</span>';
                }
                // Show a converted date
                foreach (explode('_and_', $CALENDAR_FORMAT) as $convcal) {
                    $alt_date = match ($convcal) {
                        'french'    => new FrenchDate($cal_date->minimumJulianDay() + $d - 1),
                        'gregorian' => new GregorianDate($cal_date->minimumJulianDay() + $d - 1),
                        'jewish'    => new JewishDate($cal_date->minimumJulianDay() + $d - 1),
                        'julian'    => new JulianDate($cal_date->minimumJulianDay() + $d - 1),
                        'hijri'     => new HijriDate($cal_date->minimumJulianDay() + $d - 1),
                        'jalali'    => new JalaliDate($cal_date->minimumJulianDay() + $d - 1),
                        default     => $cal_date,
                    };

                    if (get_class($alt_date) !== get_class($cal_date) && $alt_date->inValidRange()) {
                        echo '<span class="rtl_cal_day">' . $alt_date->format('%j %M') . '</span>';
                        // Just show the first conversion
                        break;
                    }
                }
                echo '</div>';
                echo '<div class="small" style="height: 180px; overflow: auto;">';
                echo $this->calendarListText($cal_facts[$d], $tree);
                echo '</div>';
            }
            echo '</td>';
            if (($d + $cal_date->minimumJulianDay() - $week_start) % $days_in_week === 0) {
                echo '</tr>';
            }
        }
        echo '</tbody>';
        echo '</table>';

        return response(ob_get_clean());
    }

    /**
     * Format a list of facts for display
     *
     * @param array<string> $list
     */
    private function calendarListText(array $list, Tree $tree): string
    {
        $html = '';

        foreach ($list as $xref => $facts) {
            $tmp = Registry::gedcomRecordFactory()->make((string) $xref, $tree);
            $html .= '<a href="' . e($tmp->url()) . '">' . $tmp->fullName() . '</a> ';
            $html .= '<div class="indent">' . $facts . '</div>';
        }

        return $html;
    }
}
