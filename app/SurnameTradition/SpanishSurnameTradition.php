<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Elements\NameType;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Children take one surname from the father and one surname from the mother.
 *
 * Mother: Maria /AAAA/ /BBBB/
 * Father: Jose  /CCCC/ /DDDD/
 * Child:  Pablo /CCCC/ /AAAA/
 */
class SpanishSurnameTradition extends DefaultSurnameTradition
{
    /**
     * The name of this surname tradition
     *
     * @return string
     */
    public function name(): string
    {
        return I18N::translateContext('Surname tradition', 'Spanish');
    }

    /**
     * A short description of this surname tradition
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: In the Spanish surname tradition, ... */
        return I18N::translate('Children take one surname from the father and one surname from the mother.');
    }

    /**
     * A default/empty name
     *
     * @return string
     */
    public function defaultName(): string
    {
        return '// //';
    }

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
        if (preg_match(self::REGEX_SURNS, $this->extractName($father), $match_father) === 1) {
            $father_surname = $match_father['SURN1'];
        } else {
            $father_surname = '';
        }

        if (preg_match(self::REGEX_SURNS, $this->extractName($mother), $match_mother) === 1) {
            $mother_surname = $match_mother['SURN1'];
        } else {
            $mother_surname = '';
        }

        return [
            $this->buildName('/' . $father_surname . '/ /' . $mother_surname . '/', [
                'TYPE' => NameType::VALUE_BIRTH,
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
        if (preg_match(self::REGEX_SURNS, $this->extractName($child), $match) === 1) {
            switch ($sex) {
                case 'M':
                    return [
                        $this->buildName('/' . $match['SURN1'] . '/ //', [
                            'TYPE' => NameType::VALUE_BIRTH,
                            'SURN' => $match['SURN1'],
                        ]),
                    ];

                case 'F':
                    return [
                        $this->buildName('/' . $match['SURN2'] . '/ //', [
                            'TYPE' => NameType::VALUE_BIRTH,
                            'SURN' => $match['SURN2'],
                        ]),
                    ];
            }
        }

        return [
            $this->buildName('// //', ['TYPE' => NameType::VALUE_BIRTH]),
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
            $this->buildName('// //', ['TYPE' => NameType::VALUE_BIRTH]),
        ];
    }
}
