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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\CalendarDate;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;

/**
 * Calculate anniversaries, etc.
 */
class CalendarService
{
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
            $calendar_date->d = 1;
            $calendar_date->m = $month_number;
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
     * Get a list of events which occured during a given date range.
     *
     * @param int    $jd1   the start range of julian day
     * @param int    $jd2   the end range of julian day
     * @param string $facts restrict the search to just these facts or leave blank for all
     * @param Tree   $tree  the tree to search
     *
     * @return Fact[]
     */
    public function getCalendarEvents(int $jd1, int $jd2, string $facts, Tree $tree): array
    {
        // If no facts specified, get all except these
        $skipfacts = 'CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR';

        $found_facts = [];

        // Events that start or end during the period
        $where = "WHERE (d_julianday1>={$jd1} AND d_julianday1<={$jd2} OR d_julianday2>={$jd1} AND d_julianday2<={$jd2})";

        // Restrict to certain types of fact
        if (empty($facts)) {
            $excl_facts = "'" . preg_replace('/\W+/', "','", $skipfacts) . "'";
            $where      .= " AND d_fact NOT IN ({$excl_facts})";
        } else {
            $incl_facts = "'" . preg_replace('/\W+/', "','", $facts) . "'";
            $where      .= " AND d_fact IN ({$incl_facts})";
        }
        // Only get events from the current gedcom
        $where .= " AND d_file=" . $tree->getTreeId();

        // Now fetch these events
        $ind_sql = "SELECT d_gid AS xref, i_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact, d_type FROM `##dates`, `##individuals` {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_julianday1";
        $fam_sql = "SELECT d_gid AS xref, f_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact, d_type FROM `##dates`, `##families`    {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_julianday1";

        foreach (['INDI' => $ind_sql, 'FAM'  => $fam_sql] as $type => $sql) {
            $rows = Database::prepare($sql)->fetchAll();

            foreach ($rows as $row) {
                if ($type === 'INDI') {
                    $record = Individual::getInstance($row->xref, $tree, $row->gedcom);
                } else {
                    $record = Family::getInstance($row->xref, $tree, $row->gedcom);
                }
                $anniv_date = new Date($row->d_type . ' ' . $row->d_day . ' ' . $row->d_month . ' ' . $row->d_year);
                foreach ($record->getFacts() as $fact) {
                    // For date ranges, we need a match on either the start/end.
                    if (($fact->getDate()->minimumJulianDay() === $anniv_date->minimumJulianDay() || $fact->getDate()->maximumJulianDay() == $anniv_date->maximumJulianDay()) && $fact->getTag() === $row->d_fact) {
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
     * @param int     $jd1
     * @param int     $jd2
     * @param string  $events
     * @param bool    $only_living
     * @param string  $sort_by
     * @param Tree    $tree
     *
     * @return Fact[]
     */
    public function getEventsList(int $jd1, int $jd2, string $events, bool $only_living, string $sort_by, Tree $tree): array
    {
        $found_facts = [];
        $facts       = [];

        foreach (range($jd1, $jd2) as $jd) {
            $found_facts = array_merge($found_facts, $this->getAnniversaryEvents($jd, $events, $tree));
        }

        foreach ($found_facts as $fact) {
            $record = $fact->getParent();
            // only living people ?
            if ($only_living) {
                if ($record instanceof Individual && $record->isDead()) {
                    continue;
                }
                if ($record instanceof Family) {
                    $husb = $record->getHusband();
                    if ($husb === null || $husb->isDead()) {
                        continue;
                    }
                    $wife = $record->getWife();
                    if ($wife === null || $wife->isDead()) {
                        continue;
                    }
                }
            }
            $facts[] = $fact;
        }

        switch ($sort_by) {
            case 'anniv':
                uasort($facts, function (Fact $x, Fact $y): int {
                    return Fact::compareDate($y, $x);
                });
                break;
            case 'alpha':
                uasort($facts, function (Fact $x, Fact $y): int {
                    return GedcomRecord::compare($x->getParent(), $y->getParent());
                });
                break;
        }

        return $facts;
    }

    /**
     * Get a list of events whose anniversary occured on a given julian day.
     * Used on the on-this-day/upcoming blocks and the day/month calendar views.
     *
     * @param int    $jd    the julian day
     * @param string $facts restrict the search to just these facts or leave blank for all
     * @param Tree   $tree  the tree to search
     *
     * @return Fact[]
     */
    public function getAnniversaryEvents($jd, $facts, Tree $tree): array
    {
        $found_facts = [];

        $anniversaries = [
            new GregorianDate($jd),
            new JulianDate($jd),
            new FrenchDate($jd),
            new JewishDate($jd),
            new HijriDate($jd),
            new JalaliDate($jd),
        ];

        foreach ($anniversaries as $anniv) {
            // Build a SQL where clause to match anniversaries in the appropriate calendar.
            $ind_sql =
                "SELECT DISTINCT i_id AS xref, i_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact" .
                " FROM `##dates` JOIN `##individuals` ON d_gid = i_id AND d_file = i_file" .
                " WHERE d_type = :type AND d_file = :tree_id";
            $fam_sql =
                "SELECT DISTINCT f_id AS xref, f_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact" .
                " FROM `##dates` JOIN `##families` ON d_gid = f_id AND d_file = f_file" .
                " WHERE d_type = :type AND d_file = :tree_id";
            $args = [
                'type'    => $anniv->format('%@'),
                'tree_id' => $tree->getTreeId(),
            ];

            $where = "";
            // SIMPLE CASES:
            // a) Non-hebrew anniversaries
            // b) Hebrew months TVT, SHV, IYR, SVN, TMZ, AAV, ELL
            if (!$anniv instanceof JewishDate || in_array($anniv->m, [
                    1,
                    5,
                    6,
                    9,
                    10,
                    11,
                    12,
                    13,
                ])) {
                // Dates without days go on the first day of the month
                // Dates with invalid days go on the last day of the month
                if ($anniv->d === 1) {
                    $where .= " AND d_day <= 1";
                } elseif ($anniv->d === $anniv->daysInMonth()) {
                    $where       .= " AND d_day >= :day";
                    $args['day'] = $anniv->d;
                } else {
                    $where       .= " AND d_day = :day";
                    $args['day'] = $anniv->d;
                }
                $where .= " AND d_mon = :month";
                $args['month'] = $anniv->m;
            } else {
                // SPECIAL CASES:
                switch ($anniv->m) {
                    case 2:
                        // 29 CSH does not include 30 CSH (but would include an invalid 31 CSH if there were no 30 CSH)
                        if ($anniv->d === 1) {
                            $where .= " AND d_day <= 1 AND d_mon = 2";
                        } elseif ($anniv->d === 30) {
                            $where .= " AND d_day >= 30 AND d_mon = 2";
                        } elseif ($anniv->d === 29 && $anniv->daysInMonth() === 29) {
                            $where .= " AND (d_day = 29 OR d_day > 30) AND d_mon = 2";
                        } else {
                            $where .= " AND d_day = :day AND d_mon = 2";
                            $args['day'] = $anniv->d;
                        }
                        break;
                    case 3:
                        // 1 KSL includes 30 CSH (if this year didn’t have 30 CSH)
                        // 29 KSL does not include 30 KSL (but would include an invalid 31 KSL if there were no 30 KSL)
                        if ($anniv->d === 1) {
                            $tmp = new JewishDate([
                                $anniv->y,
                                'CSH',
                                1,
                            ]);
                            if ($tmp->daysInMonth() === 29) {
                                $where .= " AND (d_day <= 1 AND d_mon = 3 OR d_day = 30 AND d_mon = 2)";
                            } else {
                                $where .= " AND d_day <= 1 AND d_mon = 3";
                            }
                        } elseif ($anniv->d === 30) {
                            $where .= " AND d_day >= 30 AND d_mon = 3";
                        } elseif ($anniv->d == 29 && $anniv->daysInMonth() === 29) {
                            $where .= " AND (d_day = 29 OR d_day > 30) AND d_mon = 3";
                        } else {
                            $where .= " AND d_day = :day AND d_mon = 3";
                            $args['day'] = $anniv->d;
                        }
                        break;
                    case 4:
                        // 1 TVT includes 30 KSL (if this year didn’t have 30 KSL)
                        if ($anniv->d === 1) {
                            $tmp = new JewishDate([
                                $anniv->y,
                                'KSL',
                                1,
                            ]);
                            if ($tmp->daysInMonth() === 29) {
                                $where .= " AND (d_day <=1 AND d_mon = 4 OR d_day = 30 AND d_mon = 3)";
                            } else {
                                $where .= " AND d_day <= 1 AND d_mon = 4";
                            }
                        } elseif ($anniv->d === $anniv->daysInMonth()) {
                            $where       .= " AND d_day >= :day AND d_mon=4";
                            $args['day'] = $anniv->d;
                        } else {
                            $where       .= " AND d_day = :day AND d_mon=4";
                            $args['day'] = $anniv->d;
                        }
                        break;
                    case 7: // ADS includes ADR (non-leap)
                        if ($anniv->d === 1) {
                            $where .= " AND d_day <= 1";
                        } elseif ($anniv->d === $anniv->daysInMonth()) {
                            $where       .= " AND d_day >= :day";
                            $args['day'] = $anniv->d;
                        } else {
                            $where       .= " AND d_day = :day";
                            $args['day'] = $anniv->d;
                        }
                        $where .= " AND (d_mon = 6 AND MOD(7 * d_year + 1, 19) >= 7 OR d_mon = 7)";
                        break;
                    case 8: // 1 NSN includes 30 ADR, if this year is non-leap
                        if ($anniv->d === 1) {
                            if ($anniv->isLeapYear()) {
                                $where .= " AND d_day <= 1 AND d_mon = 8";
                            } else {
                                $where .= " AND (d_day <= 1 AND d_mon = 8 OR d_day = 30 AND d_mon = 6)";
                            }
                        } elseif ($anniv->d === $anniv->daysInMonth()) {
                            $where       .= " AND d_day >= :day AND d_mon = 8";
                            $args['day'] = $anniv->d;
                        } else {
                            $where       .= " AND d_day = :day AND d_mon = 8";
                            $args['day'] = $anniv->d;
                        }
                        break;
                }
            }
            // Only events in the past (includes dates without a year)
            $where .= " AND d_year <= :year";
            $args['year'] = $anniv->y;

            if ($facts) {
                // Restrict to certain types of fact
                $where .= " AND d_fact IN (";
                preg_match_all('/([_A-Z]+)/', $facts, $matches);
                foreach ($matches[1] as $n => $fact) {
                    $where              .= $n ? ", " : "";
                    $where              .= ":fact_" . $n;
                    $args['fact_' . $n] = $fact;
                }
                $where .= ")";
            } else {
                // If no facts specified, get all except these
                $where .= " AND d_fact NOT IN ('CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL', 'CENS', 'RESI', '_TODO')";
            }

            $order_by = " ORDER BY d_day, d_year DESC";

            // Now fetch these anniversaries
            foreach ([
                         'INDI' => $ind_sql . $where . $order_by,
                         'FAM'  => $fam_sql . $where . $order_by,
                     ] as $type => $sql) {
                $rows = Database::prepare($sql)->execute($args)->fetchAll();
                foreach ($rows as $row) {
                    if ($type === 'INDI') {
                        $record = Individual::getInstance($row->xref, $tree, $row->gedcom);
                    } else {
                        $record = Family::getInstance($row->xref, $tree, $row->gedcom);
                    }
                    $anniv_date = new Date($row->d_type . ' ' . $row->d_day . ' ' . $row->d_month . ' ' . $row->d_year);
                    foreach ($record->getFacts() as $fact) {
                        if (($fact->getDate()->minimumJulianDay() === $anniv_date->minimumJulianDay() || $fact->getDate()->maximumJulianDay() === $anniv_date->maximumJulianDay()) && $fact->getTag() === $row->d_fact) {
                            $fact->anniv   = $row->d_year === '0' ? 0 : $anniv->y - $row->d_year;
                            $fact->jd      = $jd;
                            $found_facts[] = $fact;
                        }
                    }
                }
            }
        }

        return $found_facts;
    }
}
