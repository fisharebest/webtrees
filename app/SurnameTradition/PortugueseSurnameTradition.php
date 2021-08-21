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
 * Children take one surname from the mother and one surname from the father.
 *
 * Mother: Maria /AAAA/ /BBBB/
 * Father: Jose  /CCCC/ /DDDD/
 * Child:  Pablo /DDDD/ /BBBB/
 */
class PortugueseSurnameTradition extends DefaultSurnameTradition
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
        if (preg_match(self::REGEX_SURNS, $this->extractName($father), $match_father)) {
            $father_surname = $match_father['SURN2'];
        } else {
            $father_surname = '';
        }

        if (preg_match(self::REGEX_SURNS, $this->extractName($mother), $match_mother)) {
            $mother_surname = $match_mother['SURN2'];
        } else {
            $mother_surname = '';
        }

        return [
            $this->buildName('/' . $father_surname . '/ /' . $mother_surname . '/', [
                'TYPE' => 'birth',
                'SURN' => trim($father_surname . ',' . $mother_surname, ','),
            ]),
        ];
    }

    /**
     * What name is given to a new parent
     *
     * @param Individual $child
     * @param string     $sex
     *
     * @return array<int,string>
     */
    public function newParentNames(Individual $child, string $sex): array
    {
        if (preg_match(self::REGEX_SURNS, $this->extractName($child), $match)) {
            switch ($sex) {
                case 'M':
                    return [
                        $this->buildName('// /' . $match['SURN1'] . '/', [
                            'TYPE' => 'birth',
                            'SURN' => $match['SURN1'],
                        ]),
                    ];

                case 'F':
                    return [
                        $this->buildName('// /' . $match['SURN2'] . '/', [
                            'TYPE' => 'birth',
                            'SURN' => $match['SURN2'],
                        ]),
                    ];
            }
        }

        return [
            $this->buildName('// //', ['TYPE' => 'birth']),
        ];
    }

    /**
     * What names are given to a new spouse
     *
     * @param Individual $spouse
     * @param string     $sex
     *
     * @return array<int,string>
     */
    public function newSpouseNames(Individual $spouse, string $sex): array
    {
        return [
            $this->buildName('// //', ['TYPE' => 'birth']),
        ];
    }
}
