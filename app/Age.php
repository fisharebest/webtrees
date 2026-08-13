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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Enums\DateType;

use function max;
use function preg_match;
use function str_starts_with;
use function trim;
use function view;

/**
 * The difference between two GEDCOM dates, with support for date ranges.
 */
class Age
{
    private int $min_years;

    private int $min_months;

    private int $min_days;

    private int $max_years;

    private int $max_months;

    private int $max_days;

    private int $total_days;

    private bool $is_exact;

    private bool $is_range;

    private bool $is_valid;

    public function __construct(Date $birth_date, Date $event_date)
    {
        $this->is_valid =
            $birth_date->isOK() &&
            $event_date->isOK() &&
            $birth_date->type !== DateType::Before &&
            $birth_date->type !== DateType::After &&
            $event_date->type !== DateType::Before &&
            $event_date->type !== DateType::After;

        if (!$this->is_valid) {
            $this->min_years  = -1;
            $this->min_months = -1;
            $this->min_days    = -1;
            $this->max_years  = -1;
            $this->max_months = -1;
            $this->max_days   = -1;
            $this->total_days = -1;
            $this->is_exact   = false;
            $this->is_range   = false;

            return;
        }

        // For minimum age: latest possible birth to earliest possible event
        $lateBirth  = $birth_date->maximumDate();
        $earlyEvent = $event_date->minimumDate();

        // For maximum age: earliest possible birth to latest possible event
        $earlyBirth = $birth_date->minimumDate();
        $lateEvent  = $event_date->maximumDate();

        [$this->min_years, $this->min_months, $this->min_days] = $lateBirth->ageDifference($earlyEvent);
        [$this->max_years, $this->max_months, $this->max_days] = $earlyBirth->ageDifference($lateEvent);

        // Backward-compatible total days (matches original calculation)
        $this->total_days = $lateEvent->minimumJulianDay() - $earlyBirth->minimumJulianDay();

        // Precision: suppress days/months if dates lack that precision
        $has_day =
            $earlyBirth->day() !== 0 &&
            $earlyEvent->day() !== 0 &&
            $lateBirth->day() !== 0 &&
            $lateEvent->day() !== 0;

        $has_month =
            $earlyBirth->month() !== 0 &&
            $earlyEvent->month() !== 0 &&
            $lateBirth->month() !== 0 &&
            $lateEvent->month() !== 0;

        if (!$has_day) {
            $this->min_days = 0;
            $this->max_days = 0;
        }

        if (!$has_month) {
            $this->min_months = 0;
            $this->max_months = 0;
        }

        // Determine if it's truly a range (min and max differ)
        $this->is_range =
            $this->min_years !== $this->max_years ||
            $this->min_months !== $this->max_months ||
            $this->min_days !== $this->max_days;

        // Exact means full day precision and not a range
        $this->is_exact = $has_day && !$this->is_range;
    }

    /**
     * Show an age in a human-friendly form, such as "34 years", "8 months", "20 days".
     * For date ranges, shows "43–47 years".
     * Shows a warning icon for negative or implausible ages.
     */
    public function toString(): string
    {
        if (!$this->is_valid) {
            return '';
        }

        if ($this->isNegative()) {
            return view('icons/warning');
        }

        // Range display: years differ
        if ($this->is_range) {
            $display_min = max(0, $this->min_years);

            if ($display_min !== $this->max_years) {
                /* I18N: plural form is based on the second year */
                $age = I18N::plural('%s–%s year', '%s–%s years', $this->max_years, I18N::number($display_min), I18N::number($this->max_years));

                return $this->withWarningIfImplausible($age);
            }
        }

        // Single value display
        if ($this->max_years > 0) {
            $age = I18N::plural('%s year', '%s years', $this->max_years, I18N::number($this->max_years));

            return $this->withWarningIfImplausible($age);
        }

        if ($this->max_months > 0) {
            return I18N::plural('%s month', '%s months', $this->max_months, I18N::number($this->max_months));
        }

        if ($this->max_days > 0 || $this->is_exact) {
            return I18N::plural('%s day', '%s days', $this->max_days, I18N::number($this->max_days));
        }

        return I18N::number(0);
    }

    /**
     * How many days between two events?
     * If either date is invalid return -1.
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
     * Return -1 for invalid dates.
     */
    public function ageYears(): int
    {
        if ($this->is_valid) {
            return $this->max_years;
        }

        return -1;
    }

    /**
     * How many years between two events, as a localized string?
     * Shows a warning icon for negative or implausible ages.
     */
    public function ageYearsString(): string
    {
        if (!$this->is_valid) {
            return '';
        }

        if ($this->max_years < 0) {
            return view('icons/warning');
        }

        if ($this->max_years > 120) {
            return I18N::number($this->max_years) . ' ' . view('icons/warning');
        }

        return I18N::number($this->max_years);
    }

    /**
     * Is the calculated age exactly zero? (event on the same day as birth)
     */
    public function isZero(): bool
    {
        return $this->is_valid
            && $this->is_exact
            && $this->max_years === 0
            && $this->max_months === 0
            && $this->max_days === 0;
    }

    /**
     * Are the dates reversed (negative age)?
     */
    public function isNegative(): bool
    {
        return $this->is_valid && $this->max_years < 0;
    }

    /**
     * Is this a range (min and max ages differ)?
     */
    public function isRange(): bool
    {
        return $this->is_range;
    }

    /**
     * Are both dates valid?
     */
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    /**
     * Is the age implausibly large (over 120 years)?
     */
    public function isImplausible(): bool
    {
        return $this->is_valid && $this->max_years > 120;
    }

    /**
     * Does the calculated age match a recorded GEDCOM age string?
     *
     * Recorded ages with '<' or '>' prefix always return false (always show calculated age).
     * An empty recorded age returns false.
     * Otherwise, compare the recorded values against our calculated range.
     *
     * @param string $recorded Raw GEDCOM age value, e.g. "3y", "1y 2m 3d", "<1y"
     */
    public function matchesRecordedAge(string $recorded): bool
    {
        if (!$this->is_valid) {
            return false;
        }

        $recorded = trim($recorded);

        if ($recorded === '') {
            return false;
        }

        // Always show calculated age alongside recorded ages with younger/older modifiers.
        if (str_starts_with($recorded, '<') || str_starts_with($recorded, '>')) {
            return false;
        }

        // Parse recorded age components
        preg_match('/(\d+)y/', $recorded, $y);
        preg_match('/(\d+)m/', $recorded, $m);
        preg_match('/(\d+)d/', $recorded, $d);

        $recYears  = (int) ($y[1] ?? 0);
        $recMonths = (int) ($m[1] ?? 0);
        $recDays   = (int) ($d[1] ?? 0);

        // Compare at the precision level of the recorded age
        if ($recMonths === 0 && $recDays === 0) {
            // Year-only comparison
            return $recYears >= $this->min_years && $recYears <= $this->max_years;
        }

        if ($recDays === 0) {
            // Year+month comparison
            $recTotal = $recYears * 12 + $recMonths;
            $minTotal = $this->min_years * 12 + $this->min_months;
            $maxTotal = $this->max_years * 12 + $this->max_months;

            return $recTotal >= $minTotal && $recTotal <= $maxTotal;
        }

        // Full precision comparison (approximate using 30 days/month)
        $recTotal = ($recYears * 12 + $recMonths) * 30 + $recDays;
        $minTotal = ($this->min_years * 12 + $this->min_months) * 30 + $this->min_days;
        $maxTotal = ($this->max_years * 12 + $this->max_months) * 30 + $this->max_days;

        return $recTotal >= $minTotal && $recTotal <= $maxTotal;
    }

    /**
     * Append a warning icon if the age is implausibly large.
     */
    private function withWarningIfImplausible(string $age): string
    {
        if ($this->max_years > 120) {
            return $age . ' ' . view('icons/warning');
        }

        return $age;
    }
}
