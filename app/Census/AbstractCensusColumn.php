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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;

use function array_slice;
use function explode;
use function implode;

abstract readonly class AbstractCensusColumn
{
    public function __construct(
        private CensusInterface $census,
        private string $abbreviation,
        private string $title,
    ) {
    }

    public function abbreviation(): string
    {
        return $this->abbreviation;
    }

    public function title(): string
    {
        return $this->title;
    }

    protected function father(Individual $individual): Individual|null
    {
        $family = $individual->childFamilies()->first();

        if ($family instanceof Family) {
            return $family->husband();
        }

        return null;
    }

    protected function mother(Individual $individual): Individual|null
    {
        $family = $individual->childFamilies()->first();

        if ($family instanceof Family) {
            return $family->wife();
        }

        return null;
    }

    protected function spouseFamily(Individual $individual): Family|null
    {
        return $individual->spouseFamilies()
                // Exclude families that were created after this census date
            ->filter(fn (Family $family): bool => Date::compare($family->getMarriageDate(), $this->date()) <= 0)
            ->sort(Family::marriageDateComparator())
            ->last();
    }

    /**
     * @return array<string>
     */
    protected function nameAtCensusDate(Individual $individual): array
    {
        $names  = $individual->getAllNames();
        $name   = $names[0];
        $family = $this->spouseFamily($individual);

        if ($family instanceof Family) {
            $spouse = $family->spouse($individual);

            if ($spouse instanceof Individual) {
                foreach ($family->facts(['MARR']) as $marriage) {
                    if ($marriage->date()->isOK()) {
                        foreach ($names as $individual_name) {
                            foreach ($spouse->getAllNames() as $spouse_name) {
                                if ($individual_name['type'] === '_MARNM' && $individual_name['surn'] === $spouse_name['surn']) {
                                    return $individual_name;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $name;
    }

    protected function date(): Date
    {
        return new Date($this->census->censusDate());
    }

    protected function lastPartOfPlace(string $place): string
    {
        $parts = explode(', ', $place);

        return end($parts);
    }

    /**
     * Remove the country of a place name, where it is the same as the census place
     */
    protected function notCountry(string $place): string
    {
        $parts = explode(', ', $place);

        if (end($parts) === $this->place()) {
            return implode(', ', array_slice($parts, 0, -1));
        }

        return $place;
    }

    protected function place(): string
    {
        return $this->census->censusPlace();
    }
}
