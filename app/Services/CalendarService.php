<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Database\Query\Predicates;
use Fisharebest\ExtCalendar\PersianCalendar;
use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\Comparators\GedcomRecordComparator;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\AbstractCalendarDate;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function array_merge;
use function assert;
use function in_array;
use function preg_match_all;
use function range;

/**
 * Calculate anniversaries, etc.
 */
class CalendarService
{
    // If no facts specified, get all except these
    protected const array SKIP_FACTS = ['CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL', 'CENS', 'RESI', 'NOTE', 'ADDR', 'OBJE', 'SOUR', '_TODO'];

    /**
     * Get the list of current and upcoming events, sorted by anniversary date
     *
     * @return Collection<int,Fact>
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
                    $compare = $x->jd <=> $y->jd;

                    return $compare !== 0 ? $compare : $x->date()->minimumJulianDay() <=> $y->date()->minimumJulianDay();
                });
                break;

            case 'anniv_desc':
                $facts = $facts->sort(static function (Fact $x, Fact $y): int {
                    $compare = $x->jd <=> $y->jd;

                    return $compare !== 0 ? $compare : $y->date()->minimumJulianDay() <=> $x->date()->minimumJulianDay();
                });
                break;

            case 'alpha':
                $facts = $facts->sort(static fn (Fact $x, Fact $y): int => GedcomRecordComparator::byName($x->record(), $y->record()));
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
     * @return array<Fact>
     */
    public function getAnniversaryEvents(int $jd, string $facts, Tree $tree, string $filterof = '', string $filtersx = ''): array
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
            $query = DB::query()
                ->from('dates')
                ->distinct()
                ->whereEqual('d_file', $tree->id())
                ->whereEqual('d_type', $anniv->calendarEscape()->value);

            // SIMPLE CASES:
            // a) Non-hebrew anniversaries
            // b) Hebrew months TVT, SHV, IYR, SVN, TMZ, AAV, ELL
            if (!$anniv instanceof JewishDate || in_array($anniv->month(), [1, 5, 6, 9, 10, 11, 12, 13], true)) {
                $query = $this->defaultAnniversaries($query, $anniv);
            } else {
                // SPECIAL CASES:
                switch ($anniv->month()) {
                    case 2:
                        $query = $this->cheshvanAnniversaries($query, $anniv);
                        break;
                    case 3:
                        $query = $this->kislevAnniversaries($query, $anniv);
                        break;
                    case 4:
                        $query = $this->tevetAnniversaries($query, $anniv);
                        break;
                    case 7:
                        $query = $this->adarIIAnniversaries($query, $anniv);
                        break;
                    case 8:
                        $query = $this->nisanAnniversaries($query, $anniv);
                        break;
                }
            }
            // Only events in the past (includes dates without a year)
            $query = $query->whereLessThanOrEqual('d_year', $anniv->year());

            if ($facts === '') {
                // If no facts specified, get all except these
                $query = $query->whereNotIn('d_fact', self::SKIP_FACTS);
            } else {
                // Restrict to certain types of fact
                preg_match_all('/([_A-Z]+)/', $facts, $matches);

                $query = $query->whereIn('d_fact', $matches[1]);
            }

            if ($filterof === 'recent') {
                // Approximately 100 years ago, used as a rough filter.
                $query = $query->whereGreaterThanOrEqual('d_julianday1', Registry::timestampFactory()->todayJulianDay() - 36525);
            }

            $ind_query = $query
                ->select('i_id AS xref', 'i_gedcom AS gedcom', 'd_type', 'd_day', 'd_month', 'd_year', 'd_fact')
                ->join('individuals', static fn (JoinOn $j): JoinOn => $j->on('d_gid', 'i_id')->on('d_file', 'i_file'));

            if ($filtersx !== '') {
                $ind_query = $ind_query->whereEqual('i_sex', $filtersx);
            }

            $queries = ['INDI' => $ind_query->orderBy('d_day', DB::desc('d_year'))];
            if ($filtersx === '') {
                $fam_query = $query
                    ->select('f_id AS xref', 'f_gedcom AS gedcom', 'd_type', 'd_day', 'd_month', 'd_year', 'd_fact')
                    ->join('families', static fn (JoinOn $j): JoinOn => $j->on('d_gid', 'f_id')->on('d_file', 'f_file'))
                    ->orderBy('d_day', DB::desc('d_year'));

                $queries['FAM'] = $fam_query;
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

                        if ($filterof === 'living' && ($husb !== null && $husb->isDead() || $wife !== null && $wife->isDead())) {
                            continue;
                        }
                    }

                    $anniv_date = new Date($row->d_type . ' ' . $row->d_day . ' ' . $row->d_month . ' ' . $row->d_year);

                    // The record may have multiple facts of this type.
                    // Find the ones that match the date.
                    foreach ($record->facts([$row->d_fact]) as $fact) {
                        $min_date = $fact->date()->minimumDate();
                        $max_date = $fact->date()->maximumDate();

                        if ($min_date->minimumJulianDay() === $anniv_date->minimumJulianDay() && $min_date->calendarEscape()->value === $row->d_type || $max_date->maximumJulianDay() === $anniv_date->maximumJulianDay() && $max_date->calendarEscape()->value === $row->d_type) {
                            $fact->anniv   = $row->d_year === '0' ? 0 : $anniv->year() - $row->d_year;
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
     */
    private function defaultAnniversaries(QueryBuilder $query, AbstractCalendarDate $anniv): QueryBuilder
    {
        if ($anniv->day() === 1) {
            $query = $query->whereLessThanOrEqual('d_day', 1);
        } elseif ($anniv->day() === $anniv->daysInMonth()) {
            $query = $query->whereGreaterThanOrEqual('d_day', $anniv->daysInMonth());
        } else {
            $query = $query->whereEqual('d_day', $anniv->day());
        }

        return $query->whereEqual('d_mon', $anniv->month());
    }

    /**
     * 29 CSH does not include 30 CSH (but would include an invalid 31 CSH if there were no 30 CSH).
     */
    private function cheshvanAnniversaries(QueryBuilder $query, JewishDate $anniv): QueryBuilder
    {
        if ($anniv->day === 29 && $anniv->daysInMonth() === 29) {
            return $query
                ->whereEqual('d_mon', 2)
                ->whereGreaterThanOrEqual('d_day', 29)
                ->whereNotEqual('d_day', 30);
        }

        return $this->defaultAnniversaries($query, $anniv);
    }

    /**
     * 1 KSL includes 30 CSH (if this year didn’t have 30 CSH).
     * 29 KSL does not include 30 KSL (but would include an invalid 31 KSL if there were no 30 KSL).
     */
    private function kislevAnniversaries(QueryBuilder $query, JewishDate $anniv): QueryBuilder
    {
        $tmp = new JewishDate([(string) $anniv->year(), 'CSH', '1']);

        if ($anniv->day() === 1 && $tmp->daysInMonth() === 29) {
            return $query->whereNested(static fn (Predicates $p): Predicates => $p
                ->nested(static fn (Predicates $p): Predicates => $p
                    ->lessThanOrEqual('d_day', 1)
                    ->equal('d_mon', 3))
                ->orNested(static fn (Predicates $p): Predicates => $p
                    ->equal('d_day', 30)
                    ->equal('d_mon', 2)));
        }

        if ($anniv->day === 29 && $anniv->daysInMonth() === 29) {
            return $query
                ->whereEqual('d_mon', 3)
                ->whereGreaterThanOrEqual('d_day', 29)
                ->whereNotEqual('d_day', 30);
        }

        return $this->defaultAnniversaries($query, $anniv);
    }

    /**
     * 1 TVT includes 30 KSL (if this year didn’t have 30 KSL).
     */
    private function tevetAnniversaries(QueryBuilder $query, JewishDate $anniv): QueryBuilder
    {
        $tmp = new JewishDate([(string) $anniv->year(), 'KSL', '1']);

        if ($anniv->day === 1 && $tmp->daysInMonth() === 29) {
            return $query->whereNested(static fn (Predicates $p): Predicates => $p
                ->nested(static fn (Predicates $p): Predicates => $p
                    ->lessThanOrEqual('d_day', 1)
                    ->equal('d_mon', 4))
                ->orNested(static fn (Predicates $p): Predicates => $p
                    ->equal('d_day', 30)
                    ->equal('d_mon', 3)));
        }

        return $this->defaultAnniversaries($query, $anniv);
    }

    /**
     * ADS includes non-leap ADR.
     */
    private function adarIIAnniversaries(QueryBuilder $query, JewishDate $anniv): QueryBuilder
    {
        if ($anniv->day() === 1) {
            $query = $query->whereLessThanOrEqual('d_day', 1);
        } elseif ($anniv->day() === $anniv->daysInMonth()) {
            $query = $query->whereGreaterThanOrEqual('d_day', $anniv->daysInMonth());
            if ($anniv->daysInMonth() === 29) {
                // On short months, 30th Adar shown on 1st Nissan
                $query = $query->whereNotEqual('d_day', 30);
            }
        } else {
            $query = $query->whereEqual('d_day', $anniv->day());
        }

        if ($anniv->isLeapYear()) {
            return $query->whereEqual('d_mon', 7);
        }

        return $query->whereIn('d_mon', [6, 7]);
    }

    /**
     * 1 NSN includes 30 ADR, if this year is non-leap.
     */
    private function nisanAnniversaries(QueryBuilder $query, JewishDate $anniv): QueryBuilder
    {
        if ($anniv->day === 1 && !$anniv->isLeapYear()) {
            return $query->whereNested(static fn (Predicates $p): Predicates => $p
                ->nested(static fn (Predicates $p): Predicates => $p
                    ->lessThanOrEqual('d_day', 1)
                    ->equal('d_mon', 8))
                ->orNested(static fn (Predicates $p): Predicates => $p
                    ->equal('d_day', 30)
                    ->equal('d_mon', 6)));
        }

        return $this->defaultAnniversaries($query, $anniv);
    }
}
