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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Individual;

/**
 * Children take their mother’s surname.
 */
class MatrilinealSurnameTradition extends DefaultSurnameTradition
{
    /**
     * What name is given to a new child
     *
     * @param Individual|null $father
     * @param Individual|null $mother
     * @param string          $sex
     *
     * @return array<int,string>
     */
    public function newChildNames(?Individual $father, ?Individual $mother, string $sex): array
    {
        if (preg_match(self::REGEX_SPFX_SURN, $this->extractName($mother), $match)) {
            $name = $match['NAME'];
            $spfx = $match['SPFX'];
            $surn = $match['SURN'];

            return [
                $this->buildName($name, ['TYPE' => 'birth', 'SPFX' => $spfx, 'SURN' => $surn]),
            ];
        }

        return parent::newChildNames($father, $mother, $sex);
    }

    /**
     * What name is given to a new parent
     *
     * @param Individual $child
     * @param string                           $sex
     *
     * @return array<int,string>
     */
    public function newParentNames(Individual $child, string $sex): array
    {
        if ($sex === 'F' && preg_match(self::REGEX_SPFX_SURN, $this->extractName($child), $match)) {
            $name = $match['NAME'];
            $spfx = $match['SPFX'];
            $surn = $match['SURN'];

            return [
                $this->buildName($name, ['TYPE' => 'birth', 'SPFX' => $spfx, 'SURN' => $surn]),
            ];
        }

        return parent::newParentNames($child, $sex);
    }
}
