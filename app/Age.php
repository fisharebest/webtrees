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

namespace Fisharebest\Webtrees;

use function view;

/**
 * The difference between two GEDCOM dates.
 */
class Age
{
    private int $years;

    private int $months;

    private int $days;

    private int $total_days;

    private bool $is_exact;

    private bool $is_valid;

    /**
     * @param Date $x The first date
     * @param Date $y The second date
     */
    public function __construct(Date $x, Date $y)
    {
        // If the dates are ranges, use the start/end calendar dates.
        $start = $x->minimumDate();
        $end   = $y->maximumDate();

        [$this->years, $this->months, $this->days] = $start->ageDifference($end);

        $this->total_days = $end->minimumJulianDay() - $start->minimumJulianDay();

        // Use the same precision as found in the dates.
        if ($start->day() === 0 || $end->day() === 0) {
            $this->days = 0;
        }

        if ($start->month() === 0 || $end->month() === 0) {
            $this->months = 0;
        }

        // Are the dates exact?
        $this->is_exact = $start->day() !== 0 && $end->day() !== 0;

        // Are the dates valid?
        $this->is_valid = $x->isOK() && $y->isOK();
    }

    /**
     * Show an age in a human-friendly form, such as "34 years", "8 months", "20 days".
     * Show an empty string for invalid/missing dates.
     * Show a warning icon for negative ages.
     * Show zero ages without any units.
     *
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->is_valid) {
            return '';
        }

        if ($this->years < 0) {
            return view('icons/warning');
        }

        if ($this->years > 0) {
            return I18N::plural('%s year', '%s years', $this->years, I18N::number($this->years));
        }

        if ($this->months > 0) {
            return I18N::plural('%s month', '%s months', $this->months, I18N::number($this->months));
        }

        if ($this->days > 0 || $this->is_exact) {
            return I18N::plural('%s day', '%s days', $this->days, I18N::number($this->days));
        }

        return I18N::number(0);
    }

    /**
     * How many days between two events?
     * If either date is invalid return -1.
     *
     * @return int
     */
    public function ageDays(): int
    {
        if ($this->is_valid) {
            return $this->total_days;
        }

        return -1;
    }

    /**
     * How many years between two events?
     * Return -1 for invalid or reversed dates.
     *
     * @return int
     */
    public function ageYears(): int
    {
        if ($this->is_valid) {
            return $this->years;
        }

        return -1;
    }

    /**
     * How many years between two events?
     * If either date is invalid return -1.
     *
     * @return string
     */
    public function ageYearsString(): string
    {
        if (!$this->is_valid) {
            return '';
        }

        if ($this->years < 0) {
            return view('icons/warning');
        }

        return I18N::number($this->years);
    }
}
