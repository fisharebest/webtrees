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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\ExtCalendar\PersianCalendar;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\AbstractCalendarDate;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

use function array_merge;
use function in_array;
use function preg_match_all;
use function range;

/**
 * Calculate anniversaries, etc.
 */
class CalendarService
{
    // If no facts specified, get all except these
    protected const SKIP_FACTS = ['CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL', 'CENS', 'RESI', 'NOTE', 'ADDR', 'OBJE', 'SOUR', '_TODO'];

    /**
     * List all the months in a given year.
     *
     * @param string $calendar
     * @param int    $year
     *
     * @return string[]
     */
    public function calendarMonthsInYear(string $calendar, int $year): array
    {
        $date          = new Date($calendar . ' ' . $year);
        $calendar_date = $date->minimumDate();
        $month_numbers = range(1, $calendar_date->monthsInYear());
        $month_names   = [];

        foreach ($month_numbers as $month_number) {
            $calendar_date->day   = 1;
            $calendar_date->month = $month_number;
            $calendar_date->setJdFromYmd();

            if ($month_number === 6 && $calendar_date instanceof JewishDate && !$calendar_date->isLeapYear()) {
                // No month 6 in Jewish non-leap years.
                continue;
            }

            if ($month_number === 7 && $calendar_date instanceof JewishDate && !$calendar_date->isLeapYear()) {
                // Month 7 is ADR in Jewish non-leap years (and ADS in others).
                $mon = 'ADR';
            } else {
                $mon = $calendar_date->format('%O');
            }

            $month_names[$mon] = $calendar_date->format('%F');
        }

        return $month_names;
    }

    /**
     * Get a list of events which occurred during a given date range.
     *
     * @param int    $jd1      the start range of julian day
     * @param int    $jd2      the end range of julian day
     * @param string $facts    restrict the search to just these facts or leave blank for all
     * @param Tree   $tree     the tree to search
     * @param string $filterof filter by living/recent
     * @param string $filtersx filter by sex
     *
     * @return Fact[]
     */
    public function getCalendarEvents(int $jd1, int $jd2, string $facts, Tree $tree, string $filterof = '', string $filtersx = ''): array
    {
        // Events that start or end during the period
        $query = DB::table('dates')
            ->where('d_file', '=', $tree->id())
            ->where(static function (Builder $query) use ($jd1, $jd2): void {
                $query->where(static function (Builder $query) use ($jd1, $jd2): void {
                    $query
                        ->where('d_julianday1', '>=', $jd1)
                        ->where('d_julianday1', '<=', $jd2);
                })->orWhere(static function (Builder $query) use ($jd1, $jd2): void {
                    $query
                        ->where('d_julianday2', '>=', $jd1)
                        ->where('d_julianday2', '<=', $jd2);
                });
            });

        // Restrict to certain types of fact
        if ($facts === '') {
            $query->whereNotIn('d_fact', self::SKIP_FACTS);
        } else {
            preg_match_all('/([_A-Z]+)/', $facts, $matches);

            $query->whereIn('d_fact', $matches[1]);
        }

        if ($filterof === 'recent') {
            $query->where('d_julianday1', '>=', Carbon::now()->subYears(100)->julianDay());
        }

        $ind_query = (clone $query)
            ->join('individuals', static function (JoinClause $join): void {
                $join->on('d_gid', '=', 'i_id')->on('d_file', '=', 'i_file');
            })
            ->select(['i_id AS xref', 'i_gedcom AS gedcom', 'd_type', 'd_day', 'd_month', 'd_year', 'd_fact', 'd_type']);

        $queries = ['INDI' => $ind_query];

        if ($filtersx === '') {
            $fam_query = (clone $query)
                ->join('families', static function (JoinClause $join): void {
                    $join->on('d_gid', '=', 'f_id')->on('d_file', '=', 'f_file');
                })
                ->select(['f_id AS xref', 'f_gedcom AS gedcom', 'd_type', 'd_day', 'd_month', 'd_year', 'd_fact', 'd_type']);

            $queries['FAM'] = $fam_query;
        } else {
            $queries['INDI']->where('i_sex', '=', $filtersx);
        }

        // Now fetch these events
        $found_facts = [];

        foreach ($queries as $type => $record_query) {
            foreach ($record_query->get() as $row) {
                if ($type === 'INDI') {
                    $record = Registry::individualFactory()->make($row->xref, $tree, $row->gedcom);
                    assert($record instanceof Individual);

                    if ($filterof === 'living' && $record->isDead()) {
                        continue;
                    }
                } else {
                    $record = Registry::familyFactory()->make($row->xref, $tree, $row->gedcom);
                    assert($record instanceof Family);
                    $husb = $record->husband();
                    $wife = $record->wife();

                    if ($filterof === 'living' && ($husb && $husb->isDead() || $wife && $wife->isDead())) {
                        continue;
                    }
                }

                $anniv_date = new Date($row->d_type . ' ' . $row->d_day . ' ' . $row->d_month . ' ' . $row->d_year);

                foreach ($record->facts([$row->d_fact]) as $fact) {
                    // For date ranges, we need a match on either the start/end.
                    if (($fact->date()->minimumJulianDay() === $anniv_date->minimumJulianDay() || $fact->date()->maximumJulianDay() === $anniv_date->maximumJulianDay())) {
                        $fact->anniv   = 0;
                        $found_facts[] = $fact;
                    }
                }
            }
        }

        return $found_facts;
    }

    /**
     * Get the list of current and upcoming events, sorted by anniversary date
     *
     * @param int    $jd1
     * @param int    $jd2
     * @param string $events
     * @param bool   $only_living
     * @param string $sort_by
     * @param Tree   $tree
     *
     * @return Collection<Fact>
     */
    public function getEventsList(int $jd1, int $jd2, string $events, bool $only_living, string $sort_by, Tree $tree): Collection
    {
        $found_facts = [];
        $facts       = new Collection();

        foreach (range($jd1, $jd2) as $jd) {
            $found_facts = array_merge($found_facts, $this->getAnniversaryEvents($jd, $events, $tree));
        }

        foreach ($found_facts as $fact) {
            $record = $fact->record();
            // only living people ?
            if ($only_living) {
                if ($record instanceof Individual && $record->isDead()) {
                    continue;
                }
                if ($record instanceof Family) {
                    $husb = $record->husband();
                    if ($husb === null || $husb->isDead()) {
                        continue;
                    }
                    $wife = $record->wife();
                    if ($wife === null || $wife->isDead()) {
                        continue;
                    }
                }
            }
            $facts->push($fact);
        }

        switch ($sort_by) {
            case 'anniv':
            case 'anniv_asc':
                $facts = $facts->sort(static function (Fact $x, Fact $y): int {
                    return $x->jd <=> $y->jd ?: $x->date()->minimumJulianDay() <=> $y->date()->minimumJulianDay();
                });
                break;

            case 'anniv_desc':
                $facts = $facts->sort(static function (Fact $x, Fact $y): int {
                    return $x->jd <=> $y->jd ?: $y->date()->minimumJulianDay() <=> $x->date()->minimumJulianDay();
                });
                break;

            case 'alpha':
                $facts = $facts->sort(static function (Fact $x, Fact $y): int {
                    return GedcomRecord::nameComparator()($x->record(), $y->record());
                });
                break;
        }

        return $facts->values();
    }

    /**
     * Get a list of events whose anniversary occurred on a given julian day.
     * Used on the on-this-day/upcoming blocks and the day/month calendar views.
     *
     * @param int    $jd       the julian day
     * @param string $facts    restrict the search to just these facts or leave blank for all
     * @param Tree   $tree     the tree to search
     * @param string $filterof filter by living/recent
     * @param string $filtersx filter by sex
     *
     * @return Fact[]
     */
    public function getAnniversaryEvents($jd, string $facts, Tree $tree, string $filterof = '', string $filtersx = ''): array
    {
        $found_facts = [];

        $anniversaries = [
            new GregorianDate($jd),
            new JulianDate($jd),
            new FrenchDate($jd),
            new JewishDate($jd),
            new HijriDate($jd),
        ];

        // There is a bug in the Persian Calendar that gives zero months for invalid dates
        if ($jd > (new PersianCalendar())->jdStart()) {
            $anniversaries[] = new JalaliDate($jd);
        }

        foreach ($anniversaries as $anniv) {
            // Build a query to match anniversaries in the appropriate calendar.
            $query = DB::table('dates')
                ->distinct()
                ->where('d_file', '=', $tree->id())
                ->where('d_type', '=', $anniv->format('%@'));

            // SIMPLE CASES:
            // a) Non-hebrew anniversaries
            // b) Hebrew months TVT, SHV, IYR, SVN, TMZ, AAV, ELL
            if (!$anniv instanceof JewishDate || in_array($anniv->month, [1, 5, 6, 9, 10, 11, 12, 13], true)) {
                $this->defaultAnniversaries($query, $anniv);
            } else {
                // SPECIAL CASES:
                switch ($anniv->month) {
                    case 2:
                        $this->cheshvanAnniversaries($query, $anniv);
                        break;
                    case 3:
                        $this->kislevAnniversaries($query, $anniv);
                        break;
                    case 4:
                        $this->tevetAnniversaries($query, $anniv);
                        break;
                    case 7:
                        $this->adarIIAnniversaries($query, $anniv);
                        break;
                    case 8:
                        $this->nisanAnniversaries($query, $anniv);
                        break;
                }
            }
            // Only events in the past (includes dates without a year)
            $query->where('d_year', '<=', $anniv->year());

            if ($facts === '') {
                // If no facts specified, get all except these
                $query->whereNotIn('d_fact', self::SKIP_FACTS);
            } else {
                // Restrict to certain types of fact
                preg_match_all('/([_A-Z]+)/', $facts, $matches);

                $query->whereIn('d_fact', $matches[1]);
            }

            if ($filterof === 'recent') {
                $query->where('d_julianday1', '>=', Carbon::now()->subYears(100)->julianDay());
            }

            $query
                ->orderBy('d_day')
                ->orderBy('d_year', 'DESC');

            $ind_query = (clone $query)
                ->join('individuals', static function (JoinClause $join): void {
                    $join->on('d_gid', '=', 'i_id')->on('d_file', '=', 'i_file');
                })
                ->select(['i_id AS xref', 'i_gedcom AS gedcom', 'd_type', 'd_day', 'd_month', 'd_year', 'd_fact']);

            $queries = ['INDI' => $ind_query];

            if ($filtersx === '') {
                $fam_query = (clone $query)
                    ->join('families', static function (JoinClause $join): void {
                        $join->on('d_gid', '=', 'f_id')->on('d_file', '=', 'f_file');
                    })
                    ->select(['f_id AS xref', 'f_gedcom AS gedcom', 'd_type', 'd_day', 'd_month', 'd_year', 'd_fact']);

                $queries['FAM'] = $fam_query;
            } else {
                $queries['INDI']->where('i_sex', '=', $filtersx);
            }

            // Now fetch these anniversaries
            foreach ($queries as $type => $record_query) {
                foreach ($record_query->get() as $row) {
                    if ($type === 'INDI') {
                        $record = Registry::individualFactory()->make($row->xref, $tree, $row->gedcom);
                        assert($record instanceof Individual);

                        if ($filterof === 'living' && $record->isDead()) {
                            continue;
                        }
                    } else {
                        $record = Registry::familyFactory()->make($row->xref, $tree, $row->gedcom);
                        assert($record instanceof Family);
                        $husb = $record->husband();
                        $wife = $record->wife();

                        if ($filterof === 'living' && ($husb && $husb->isDead() || $wife && $wife->isDead())) {
                            continue;
                        }
                    }

                    $anniv_date = new Date($row->d_type . ' ' . $row->d_day . ' ' . $row->d_month . ' ' . $row->d_year);

                    // The record may have multiple facts of this type.
                    // Find the ones that match the date.
                    foreach ($record->facts([$row->d_fact]) as $fact) {
                        $min_date = $fact->date()->minimumDate();
                        $max_date = $fact->date()->maximumDate();

                        if ($min_date->minimumJulianDay() === $anniv_date->minimumJulianDay() && $min_date::ESCAPE === $row->d_type || $max_date->maximumJulianDay() === $anniv_date->maximumJulianDay() && $max_date::ESCAPE === $row->d_type) {
                            $fact->anniv   = $row->d_year === '0' ? 0 : $anniv->year - $row->d_year;
                            $fact->jd      = $jd;
                            $found_facts[] = $fact;
                        }
                    }
                }
            }
        }

        return $found_facts;
    }

    /**
     * By default, missing days have anniversaries on the first of the month,
     * and invalid days have anniversaries on the last day of the month.
     *
     * @param Builder              $query
     * @param AbstractCalendarDate $anniv
     */
    private function defaultAnniversaries(Builder $query, AbstractCalendarDate $anniv): void
    {
        if ($anniv->day() === 1) {
            $query->where('d_day', '<=', 1);
        } elseif ($anniv->day() === $anniv->daysInMonth()) {
            $query->where('d_day', '>=', $anniv->daysInMonth());
        } else {
            $query->where('d_day', '=', $anniv->day());
        }

        $query->where('d_mon', '=', $anniv->month());
    }

    /**
     * 29 CSH does not include 30 CSH (but would include an invalid 31 CSH if there were no 30 CSH).
     *
     * @param Builder    $query
     * @param JewishDate $anniv
     */
    private function cheshvanAnniversaries(Builder $query, JewishDate $anniv): void
    {
        if ($anniv->day === 29 && $anniv->daysInMonth() === 29) {
            $query
                ->where('d_mon', '=', 2)
                ->where('d_day', '>=', 29)
                ->where('d_day', '<>', 30);
        } else {
            $this->defaultAnniversaries($query, $anniv);
        }
    }

    /**
     * 1 KSL includes 30 CSH (if this year didn’t have 30 CSH).
     * 29 KSL does not include 30 KSL (but would include an invalid 31 KSL if there were no 30 KSL).
     *
     * @param Builder    $query
     * @param JewishDate $anniv
     */
    private function kislevAnniversaries(Builder $query, JewishDate $anniv): void
    {
        $tmp = new JewishDate([(string) $anniv->year, 'CSH', '1']);

        if ($anniv->day() === 1 && $tmp->daysInMonth() === 29) {
            $query->where(static function (Builder $query): void {
                $query->where(static function (Builder $query): void {
                    $query->where('d_day', '<=', 1)->where('d_mon', '=', 3);
                })->orWhere(static function (Builder $query): void {
                    $query->where('d_day', '=', 30)->where('d_mon', '=', 2);
                });
            });
        } elseif ($anniv->day === 29 && $anniv->daysInMonth() === 29) {
            $query
                ->where('d_mon', '=', 3)
                ->where('d_day', '>=', 29)
                ->where('d_day', '<>', 30);
        } else {
            $this->defaultAnniversaries($query, $anniv);
        }
    }

    /**
     * 1 TVT includes 30 KSL (if this year didn’t have 30 KSL).
     *
     * @param Builder    $query
     * @param JewishDate $anniv
     */
    private function tevetAnniversaries(Builder $query, JewishDate $anniv): void
    {
        $tmp = new JewishDate([(string) $anniv->year, 'KSL', '1']);

        if ($anniv->day === 1 && $tmp->daysInMonth() === 29) {
            $query->where(static function (Builder $query): void {
                $query->where(static function (Builder $query): void {
                    $query->where('d_day', '<=', 1)->where('d_mon', '=', 4);
                })->orWhere(static function (Builder $query): void {
                    $query->where('d_day', '=', 30)->where('d_mon', '=', 3);
                });
            });
        } else {
            $this->defaultAnniversaries($query, $anniv);
        }
    }

    /**
     * ADS includes non-leap ADR.
     *
     * @param Builder    $query
     * @param JewishDate $anniv
     */
    private function adarIIAnniversaries(Builder $query, JewishDate $anniv): void
    {
        if ($anniv->day() === 1) {
            $query->where('d_day', '<=', 1);
        } elseif ($anniv->day() === $anniv->daysInMonth()) {
            $query->where('d_day', '>=', $anniv->daysInMonth());
            if ($anniv->daysInMonth() === 29) {
                // On short months, 30th Adar shown on 1st Nissan
                $query->where('d_day', '<>', 30);
            }
        } else {
            $query->where('d_day', '=', $anniv->day());
        }

        if ($anniv->isLeapYear()) {
            $query->where('d_mon', '=', 7);
        } else {
            $query->whereIn('d_mon', [6, 7]);
        }
    }

    /**
     * 1 NSN includes 30 ADR, if this year is non-leap.
     *
     * @param Builder    $query
     * @param JewishDate $anniv
     */
    private function nisanAnniversaries(Builder $query, JewishDate $anniv): void
    {
        if ($anniv->day === 1 && !$anniv->isLeapYear()) {
            $query->where(static function (Builder $query): void {
                $query->where(static function (Builder $query): void {
                    $query->where('d_day', '<=', 1)->where('d_mon', '=', 8);
                })->orWhere(static function (Builder $query): void {
                    $query->where('d_day', '=', 30)->where('d_mon', '=', 6);
                });
            });
        } else {
            $this->defaultAnniversaries($query, $anniv);
        }
    }
}
