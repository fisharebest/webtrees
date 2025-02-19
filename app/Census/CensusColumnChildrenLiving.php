<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Individual;

/**
 * The number of children who are still living.
 */
class CensusColumnChildrenLiving extends AbstractCensusColumn implements CensusColumnInterface
{
    /**
     * Generate the likely value of this census column, based on available information.
     *
     * @param Individual $individual
     * @param Individual $head
     *
     * @return string
     */
    public function generate(Individual $individual, Individual $head): string
    {
        $family = $this->spouseFamily($individual);

        if ($family === null || $individual->sex() !== 'F') {
            return '';
        }

        return (string) $family->children()
            ->filter(function (Individual $child): bool {
                $birth = $child->getBirthDate();
                $death = $child->getDeathDate();

                $born_before = $birth->isOK() && Date::compare($birth, $this->date()) < 0;
                $died_after  = $death->isOK() && Date::compare($death, $this->date()) > 0 || !$death->isOK();

                return $born_before && $died_after;
            })
            ->count();
    }
}
