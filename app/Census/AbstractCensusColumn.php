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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;

use function array_slice;
use function explode;
use function implode;

/**
 * Definitions for a census column
 */
class AbstractCensusColumn
{
    /** @var CensusInterface */
    private $census;

    /** @var string */
    private $abbr;

    /** @var string */
    private $title;

    /**
     * Create a column for a census
     *
     * @param CensusInterface $census - The census to which this column forms part.
     * @param string          $abbr   - The abbreviated on-screen name "BiC"
     * @param string          $title  - The full column heading "Born in the county"
     */
    public function __construct(CensusInterface $census, string $abbr, string $title)
    {
        $this->census = $census;
        $this->abbr   = $abbr;
        $this->title  = $title;
    }

    /**
     * A short version of the column's name.
     *
     * @return string
     */
    public function abbreviation(): string
    {
        return $this->abbr;
    }

    /**
     * Find the father of an individual
     *
     * @param Individual $individual
     *
     * @return Individual|null
     */
    public function father(Individual $individual): ?Individual
    {
        $family = $individual->childFamilies()->first();

        if ($family instanceof Family) {
            return $family->husband();
        }

        return null;
    }

    /**
     * Find the mother of an individual
     *
     * @param Individual $individual
     *
     * @return Individual|null
     */
    public function mother(Individual $individual): ?Individual
    {
        $family = $individual->childFamilies()->first();

        if ($family instanceof Family) {
            return $family->wife();
        }

        return null;
    }

    /**
     * Find the current spouse family of an individual
     *
     * @param Individual $individual
     *
     * @return Family|null
     */
    public function spouseFamily(Individual $individual): ?Family
    {
        return $individual->spouseFamilies()
            ->filter(function (Family $family): bool {
                // Exclude families that were created after this census date
                return Date::compare($family->getMarriageDate(), $this->date()) <= 0;
            })
            ->sort(Family::marriageDateComparator())
            ->last();
    }

    /**
     * When did this census occur
     *
     * @return Date
     */
    public function date(): Date
    {
        return new Date($this->census->censusDate());
    }

    /**
     * The full version of the column's name.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Extract the country (last part) of a place name.
     *
     * @param string $place - e.g. "London, England"
     *
     * @return string - e.g. "England"
     */
    protected function lastPartOfPlace(string $place): string
    {
        $parts = explode(', ', $place);

        return end($parts);
    }

    /**
     * Remove the country of a place name, where it is the same as the census place
     *
     * @param string $place - e.g. "London, England"
     *
     * @return string - e.g. "London" (for census of England) and "London, England" elsewhere
     */
    protected function notCountry(string $place): string
    {
        $parts = explode(', ', $place);

        if (end($parts) === $this->place()) {
            return implode(', ', array_slice($parts, 0, -1));
        }

        return $place;
    }

    /**
     * Where did this census occur
     *
     * @return string
     */
    public function place(): string
    {
        return $this->census->censusPlace();
    }
}
