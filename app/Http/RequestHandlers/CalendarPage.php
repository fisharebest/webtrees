<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * Show anniversaries for events in a given day/month/year.
 */
class CalendarPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var CalendarService */
    private $calendar_service;

    /** @var LocalizationService */
    private $localization_service;

    /**
     * CalendarPage constructor.
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
    public function handle(ServerRequestInterface $request): ResponseInterface
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
}
