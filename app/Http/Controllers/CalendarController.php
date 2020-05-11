<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function count;
use function e;
use function explode;
use function get_class;
use function ob_get_clean;
use function ob_start;
use function preg_match;
use function range;
use function redirect;
use function response;
use function route;
use function str_replace;
use function strlen;
use function substr;
use function view;

/**
 * Show anniveraries for events in a given day/month/year.
 */
class CalendarController extends AbstractBaseController
{
    /** @var CalendarService */
    private $calendar_service;

    /** @var LocalizationService */
    private $localization_service;

    /**
     * CalendarController constructor.
     *
     * @param CalendarService     $calendar_service
     * @param LocalizationService $localization_service
     */
    public function __construct(CalendarService $calendar_service, LocalizationService $localization_service)
    {
        $this->calendar_service     = $calendar_service;
        $this->localization_service = $localization_service;
    }

    /**
     * A form to request the page parameters.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function page(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $view     = $request->getAttribute('view');
        $cal      = $request->getQueryParams()['cal'] ?? '';
        $day      = $request->getQueryParams()['day'] ?? '';
        $month    = $request->getQueryParams()['month'] ?? '';
        $year     = $request->getQueryParams()['year'] ?? '';
        $filterev = $request->getQueryParams()['filterev'] ?? 'BIRT-MARR-DEAT';
        $filterof = $request->getQueryParams()['filterof'] ?? 'all';
        $filtersx = $request->getQueryParams()['filtersx'] ?? '';

        if ($cal . $day . $month . $year === '') {
            // No date specified? Use the most likely calendar
            $cal = $this->localization_service->calendar(I18N::locale())->gedcomCalendarEscape();
        }

        // need BC to parse date
        if ($year < 0) {
            $year = (-$year) . ' B.C.';
        }
        $ged_date = new Date("{$cal} {$day} {$month} {$year}");
        // need negative year for year entry field.
        $year     = $ged_date->minimumDate()->year;
        $cal_date = $ged_date->minimumDate();

        // Fill in any missing bits with todays date
        $today = $cal_date->today();
        if ($cal_date->day === 0) {
            $cal_date->day = $today->day;
        }
        if ($cal_date->month === 0) {
            $cal_date->month = $today->month;
        }
        if ($cal_date->year === 0) {
            $cal_date->year = $today->year;
        }

        $cal_date->setJdFromYmd();

        if ($year === 0) {
            $year = $cal_date->year;
        }

        // Extract values from date
        $days_in_month = $cal_date->daysInMonth();
        $cal_month     = $cal_date->format('%O');
        $today_month   = $today->format('%O');

        // Invalid dates? Go to monthly view, where they'll be found.
        if ($cal_date->day > $days_in_month && $view === 'day') {
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
            'months'        => $this->calendar_service->calendarMonthsInYear($cal, $year),
            'title'         => $title,
            'today'         => $today,
            'today_month'   => $today_month,
            'tree'          => $tree,
            'view'          => $view,
            'year'          => $year,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $view = $request->getAttribute('view');

        $params = (array) $request->getParsedBody();

        return redirect(route('calendar', [
            'cal'      => $params['cal'],
            'day'      => $params['day'],
            'filterev' => $params['filterev'],
            'filterof' => $params['filterof'],
            'filtersx' => $params['filtersx'],
            'month'    => $params['month'],
            'tree'     => $tree->name(),
            'view'     => $view,
            'year'     => $params['year'],
        ]));
    }

    /**
     * Show anniveraries that occured on a given day/month/year.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function calendar(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $view            = $request->getAttribute('view');
        $CALENDAR_FORMAT = $tree->getPreference('CALENDAR_FORMAT');

        $cal      = $request->getQueryParams()['cal'] ?? '';
        $day      = $request->getQueryParams()['day'] ?? '';
        $month    = $request->getQueryParams()['month'] ?? '';
        $year     = $request->getQueryParams()['year'] ?? '';
        $filterev = $request->getQueryParams()['filterev'] ?? 'BIRT-MARR-DEAT';
        $filterof = $request->getQueryParams()['filterof'] ?? 'all';
        $filtersx = $request->getQueryParams()['filtersx'] ?? '';

        if ($cal . $day . $month . $year === '') {
            // No date specified? Use the most likely calendar
            $cal = $this->localization_service->calendar(I18N::locale())->gedcomCalendarEscape();
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
        } elseif (preg_match('/^(\d+)(\?+)$/', $year, $match)) {
            // advanced-year "decade/century wildcard"
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
        $cal_date = $ged_date->minimumDate();

        // Fill in any missing bits with todays date
        $today = $cal_date->today();
        if ($cal_date->day === 0) {
            $cal_date->day = $today->day;
        }
        if ($cal_date->month === 0) {
            $cal_date->month = $today->month;
        }
        if ($cal_date->year === 0) {
            $cal_date->year = $today->year;
        }

        $cal_date->setJdFromYmd();

        // Extract values from date
        $days_in_month = $cal_date->daysInMonth();
        $days_in_week  = $cal_date->daysInWeek();

        // Invalid dates? Go to monthly view, where they'll be found.
        if ($cal_date->day > $days_in_month && $view === 'day') {
            $view = 'month';
        }

        // Day and year share the same layout.
        if ($view === 'day' || $view === 'year') {
            if ($view === 'day') {
                $anniversary_facts = $this->calendar_service->getAnniversaryEvents($cal_date->minimumJulianDay(), $filterev, $tree);
            } else {
                $ged_year = new Date($cal . ' ' . $year);
                $anniversary_facts = $this->calendar_service->getCalendarEvents($ged_year->minimumJulianDay(), $ged_year->maximumJulianDay(), $filterev, $tree);
            }

            $anniversary_facts   = $this->applyFilter($anniversary_facts, $filterof, $filtersx);
            $anniversaries = Collection::make($anniversary_facts)
                ->unique()
                ->sort(static function (Fact $x, Fact $y): int {
                    return $x->date()->minimumJulianDay() <=> $y->date()->minimumJulianDay();
                });

            $family_anniversaries = $anniversaries->filter(static function (Fact $f): bool {
                return $f->record() instanceof Family;
            });

            $individual_anniversaries = $anniversaries->filter(static function (Fact $f): bool {
                return $f->record() instanceof Individual;
            });

            return response(view('calendar-list', [
                'family_anniversaries'     => $family_anniversaries,
                'individual_anniversaries' => $individual_anniversaries,
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
            foreach ($this->applyFilter($this->calendar_service->getAnniversaryEvents($jd, $filterev, $tree), $filterof, $filtersx) as $fact) {
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
                $text = $text = $fact->label() . ' — ' . $fact->date()->display(true, null, false);
                if ($fact->anniv > 0) {
                    $text .= ' (' . I18N::translate('%s year anniversary', $fact->anniv) . ')';
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
        // The french  calendar has a 10-day week, which starts on primidi
        if ($days_in_week === 10) {
            $week_start    = 0;
            $weekend_start = -1;
            $weekend_end   = -1;
        }

        ob_start();

        echo '<table class="w-100"><thead><tr>';
        for ($week_day = 0; $week_day < $days_in_week; ++$week_day) {
            $day_name = $cal_date->dayNames(($week_day + $week_start) % $days_in_week);
            if ($week_day === $weekend_start || $week_day === $weekend_end) {
                echo '<th class="wt-page-options-label weekend" width="' . (100 / $days_in_week) . '%">', $day_name, '</th>';
            } else {
                echo '<th class="wt-page-options-label" width="' . (100 / $days_in_week) . '%">', $day_name, '</th>';
            }
        }
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        // Print days 1 to n of the month, but extend to cover "empty" days before/after the month to make whole weeks.
        // e.g. instead of 1 -> 30 (=30 days), we might have -1 -> 33 (=35 days)
        $start_d   = 1 - ($cal_date->minimumJulianDay() - $week_start) % $days_in_week;
        $end_d     = $days_in_month + ($days_in_week - ($cal_date->maximumJulianDay() - $week_start + 1) % $days_in_week) % $days_in_week;
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
                    echo '<span class="cal_day">', I18N::translate('Day not set'), '</span><br style="clear: both;">';
                    echo '<div class="small" style="height: 180px; overflow: auto;">';
                    echo $this->calendarListText($cal_facts[0], '', '', $tree);
                    echo '</div>';
                    $cal_facts[0] = [];
                }
            } else {
                // Format the day number using the calendar
                $tmp   = new Date($cal_date->format("%@ {$d} %O %E"));
                $d_fmt = $tmp->minimumDate()->format('%j');
                if ($d === $today->day && $cal_date->month === $today->month) {
                    echo '<span class="cal_day current_day">', $d_fmt, '</span>';
                } else {
                    echo '<span class="cal_day">', $d_fmt, '</span>';
                }
                // Show a converted date
                foreach (explode('_and_', $CALENDAR_FORMAT) as $convcal) {
                    switch ($convcal) {
                        case 'french':
                            $alt_date = new FrenchDate($cal_date->minimumJulianDay() + $d - 1);
                            break;
                        case 'gregorian':
                            $alt_date = new GregorianDate($cal_date->minimumJulianDay() + $d - 1);
                            break;
                        case 'jewish':
                            $alt_date = new JewishDate($cal_date->minimumJulianDay() + $d - 1);
                            break;
                        case 'julian':
                            $alt_date = new JulianDate($cal_date->minimumJulianDay() + $d - 1);
                            break;
                        case 'hijri':
                            $alt_date = new HijriDate($cal_date->minimumJulianDay() + $d - 1);
                            break;
                        case 'jalali':
                            $alt_date = new JalaliDate($cal_date->minimumJulianDay() + $d - 1);
                            break;
                        case 'none':
                        default:
                            $alt_date = $cal_date;
                            break;
                    }
                    if (get_class($alt_date) !== get_class($cal_date) && $alt_date->inValidRange()) {
                        echo '<span class="rtl_cal_day">' . $alt_date->format('%j %M') . '</span>';
                        // Just show the first conversion
                        break;
                    }
                }
                echo '<br style="clear: both;"><div class="small" style="height: 180px; overflow: auto;">';
                echo $this->calendarListText($cal_facts[$d], '', '', $tree);
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
     * Filter a list of anniversaries
     *
     * @param Fact[] $facts
     * @param string $filterof
     * @param string $filtersx
     *
     * @return Fact[]
     */
    private function applyFilter(array $facts, string $filterof, string $filtersx): array
    {
        $filtered      = [];
        $hundred_years_ago = Carbon::now()->subYears(100)->julianDay();
        foreach ($facts as $fact) {
            $record = $fact->record();
            if ($filtersx) {
                // Filter on sex
                if ($record instanceof Individual && $filtersx !== $record->sex()) {
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
                    $husb = $record->husband();
                    $wife = $record->wife();
                    if ($husb && $husb->isDead() || $wife && $wife->isDead()) {
                        continue;
                    }
                }
            }
            // Filter on recent events
            if ($filterof === 'recent' && $fact->date()->maximumJulianDay() < $hundred_years_ago) {
                continue;
            }
            $filtered[] = $fact;
        }

        return $filtered;
    }

    /**
     * Format a list of facts for display
     *
     * @param string[] $list
     * @param string   $tag1
     * @param string   $tag2
     * @param Tree     $tree
     *
     * @return string
     */
    private function calendarListText(array $list, string $tag1, string $tag2, Tree $tree): string
    {
        $html = '';

        foreach ($list as $xref => $facts) {
            $tmp  = Factory::gedcomRecord()->make((string) $xref, $tree);
            $html .= $tag1 . '<a href="' . e($tmp->url()) . '">' . $tmp->fullName() . '</a> ';
            $html .= '<div class="indent">' . $facts . '</div>' . $tag2;
        }

        return $html;
    }
}
