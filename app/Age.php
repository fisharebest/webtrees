<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees;

use function implode;
use function preg_match;
use function strtolower;
use function substr;
use function trim;

/**
 * Representation of a GEDCOM age.
 *
 * Ages may be a keyword (stillborn, infant, child) or a number of years,
 * months and/or days such as "6y 3m".
 */
class Age
{
    // GEDCOM keyword: died just prior, at, or near birth, 0 years
    private const KEYWORD_STILLBORN = 'stillborn';

    // GEDCOM keyword: age < 1 year
    private const KEYWORD_INFANT = 'infant';

    // GEDCOM keyword: age < 8 years
    private const KEYWORD_CHILD = 'child';

    // GEDCOM symbol: aged less than
    private const SYMBOL_LESS_THAN = '<';

    // GEDCOM symbol: aged more than
    private const SYMBOL_MORE_THAN = '>';

    // GEDCOM symbol: number of years
    private const SYMBOL_YEARS = 'y';

    // GEDCOM symbol: number of months
    private const SYMBOL_MONTHS = 'm';

    // GEDCOM symbol: number of weeks (this is a non-standard extension)
    private const SYMBOL_WEEKS = 'w';

    // GEDCOM symbol: number of days
    private const SYMBOL_DAYS = 'd';

    /** @var string */
    private $keyword = '';

    /** @var string */
    private $qualifier = '';

    /** @var int */
    private $years = 0;

    /** @var int */
    private $months = 0;

    /** @var int */
    private $weeks = 0;

    /** @var int */
    private $days = 0;

    /**
     * Age constructor.
     *
     * @param string $age
     */
    public function __construct(string $age)
    {
        $age = strtolower(trim($age));

        // Keywords
        if ($age === self::KEYWORD_STILLBORN || $age === self::KEYWORD_INFANT || $age === self::KEYWORD_CHILD) {
            $this->keyword = $age;

            return;
        }

        // Qualifier
        $qualifier = substr($age, 0, 1);

        if ($qualifier === self::SYMBOL_LESS_THAN || $qualifier === self::SYMBOL_MORE_THAN) {
            $this->qualifier = $qualifier;
        }

        // Number of years, months, weeks and days.
        $this->years  = $this->extractNumber($age, self::SYMBOL_YEARS);
        $this->months = $this->extractNumber($age, self::SYMBOL_MONTHS);
        $this->weeks  = $this->extractNumber($age, self::SYMBOL_WEEKS);
        $this->days   = $this->extractNumber($age, self::SYMBOL_DAYS);
    }

    /**
     * Convert an age to localised text.
     */
    public function asText(): string
    {
        if ($this->keyword === self::KEYWORD_STILLBORN) {
            // I18N: An individual’s age at an event. e.g. Died 14 Jan 1900 (stillborn)
            return I18N::translate('(stillborn)');
        }

        if ($this->keyword === self::KEYWORD_INFANT) {
            // I18N: An individual’s age at an event. e.g. Died 14 Jan 1900 (in infancy)
            return I18N::translate('(in infancy)');
        }

        if ($this->keyword === self::KEYWORD_CHILD) {
            // I18N: An individual’s age at an event. e.g. Died 14 Jan 1900 (in childhood)
            return I18N::translate('(in childhood)');
        }

        $age = [];

        // Show a zero age as "0 years", not "0 days"
        if ($this->years > 0 || $this->months === 0 && $this->weeks === 0 && $this->days === 0) {
            // I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
            $age[] = I18N::plural('%s year', '%s years', $this->years, I18N::number($this->years));
        }

        if ($this->months > 0) {
            // I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
            $age[] = I18N::plural('%s month', '%s months', $this->months, I18N::number($this->months));
        }

        if ($this->weeks > 0) {
            // I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
            $age[] = I18N::plural('%s week', '%s weeks', $this->weeks, I18N::number($this->weeks));
        }

        if ($this->days > 0) {
            // I18N: Part of an age string. e.g. 5 years, 4 months and 3 days
            $age[] = I18N::plural('%s day', '%s days', $this->days, I18N::number($this->days));
        }

        // If an age is just a number of years, only show the number
        if ($this->years > 0 && $this->months === 0 && $this->weeks === 0 && $this->days === 0) {
            $age = [I18N::number($this->years)];
        }

        $age_string = implode(I18N::$list_separator, $age);

        if ($this->qualifier === self::SYMBOL_LESS_THAN) {
            // I18N: Description of an individual’s age at an event. For example, Died 14 Jan 1900 (aged less than 21 years)
            return I18N::translate('(aged less than %s)', $age_string);
        }

        if ($this->qualifier === self::SYMBOL_MORE_THAN) {
            // I18N: Description of an individual’s age at an event. For example, Died 14 Jan 1900 (aged more than 21 years)
            return I18N::translate('(aged more than %s)', $age_string);
        }

        // I18N: Description of an individual’s age at an event. For example, Died 14 Jan 1900 (aged 43 years)
        return I18N::translate('(aged %s)', $age_string);
    }

    /**
     * Extract a number of days/weeks/months/years from the age string.
     *
     * @param string $age
     * @param string $suffix
     *
     * @return int
     */
    private function extractNumber(string $age, string $suffix): int
    {
        if (preg_match('/(\d+) *' . $suffix . '/', $age, $match)) {
            return (int) $match[1];
        }

        return 0;
    }
}
