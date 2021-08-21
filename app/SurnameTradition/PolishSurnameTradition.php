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
 * Children take their father’s surname. Wives take their husband’s surname. Surnames are inflected to indicate an individual’s sex.
 */
class PolishSurnameTradition extends PaternalSurnameTradition
{
    // Inflect a surname for females
    private const INFLECT_FEMALE = [
        'cki\b'  => 'cka',
        'dzki\b' => 'dzka',
        'ski\b'  => 'ska',
        'żki\b'  => 'żka',
    ];

    // Inflect a surname for males
    private const INFLECT_MALE = [
        'cka\b'  => 'cki',
        'dzka\b' => 'dzki',
        'ska\b'  => 'ski',
        'żka\b'  => 'żki',
    ];

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
        if (preg_match(self::REGEX_SURN, $this->extractName($father), $match)) {
            if ($sex === 'F') {
                $name = $this->inflect($match['NAME'], self::INFLECT_FEMALE);
            } else {
                $name = $this->inflect($match['NAME'], self::INFLECT_MALE);
            }

            $surn = $this->inflect($match['SURN'], self::INFLECT_MALE);

            return [$this->buildName($name, ['TYPE' => 'birth', 'SURN' => $surn])];
        }

        return [$this->buildName('//', ['TYPE' => 'birth'])];
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
        if ($sex === 'M' && preg_match(self::REGEX_SURN, $this->extractName($child), $match)) {
            $name = $this->inflect($match['NAME'], self::INFLECT_MALE);
            $surn = $this->inflect($match['SURN'], self::INFLECT_MALE);

            return [
                $this->buildName($name, ['TYPE' => 'birth', 'SURN' => $surn]),
            ];
        }

        return [$this->buildName('//', ['TYPE' => 'birth'])];
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
        if ($sex === 'F' && preg_match(self::REGEX_SURN, $this->extractName($spouse), $match)) {
            $name = $this->inflect($match['NAME'], self::INFLECT_FEMALE);
            $surn = $this->inflect($match['SURN'], self::INFLECT_MALE);

            return [
                $this->buildName('//', ['TYPE' => 'birth']),
                $this->buildName($name, ['TYPE' => 'married', 'SURN' => $surn]),
            ];
        }

        return [$this->buildName('//', ['TYPE' => 'birth'])];
    }
}
