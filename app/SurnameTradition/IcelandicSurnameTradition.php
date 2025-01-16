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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Elements\NameType;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Children take a patronym instead of a surname.
 *
 * Sons get their father’s given name plus “sson”
 * Daughters get their father’s given name plus “sdottir”
 */
class IcelandicSurnameTradition extends DefaultSurnameTradition
{
    /**
     * The name of this surname tradition
     *
     * @return string
     */
    public function name(): string
    {
        return I18N::translateContext('Surname tradition', 'Icelandic');
    }

    /**
     * A short description of this surname tradition
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: In the Icelandic surname tradition, ... */
        return I18N::translate('Children take a patronym instead of a surname.');
    }

    /**
     * A default/empty name
     *
     * @return string
     */
    public function defaultName(): string
    {
        return '';
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
        if (preg_match(self::REGEX_GIVN, $this->extractName($father), $match) === 1) {
            switch ($sex) {
                case 'M':
                    $givn = $match['GIVN'] . 'sson';

                    return [
                        $this->buildName($givn, ['TYPE' => NameType::VALUE_BIRTH, 'GIVN' => $givn]),
                    ];

                case 'F':
                    $givn = $match['GIVN'] . 'sdottir';

                    return [
                        $this->buildName($givn, ['TYPE' => NameType::VALUE_BIRTH, 'GIVN' => $givn]),
                    ];
            }
        }

        return [
            $this->buildName('', ['TYPE' => NameType::VALUE_BIRTH]),
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
        if ($sex === 'M' && preg_match('~(?<GIVN>[^ /]+)(:?sson)$~', $this->extractName($child), $match) === 1) {
            return [
                $this->buildName($match['GIVN'], ['TYPE' => NameType::VALUE_BIRTH, 'GIVN' => $match['GIVN']]),
            ];
        }

        if ($sex === 'F' && preg_match('~(?<GIVN>[^ /]+)(:?sdottir)$~', $this->extractName($child), $match) === 1) {
            return [
                $this->buildName($match['GIVN'], ['TYPE' => NameType::VALUE_BIRTH, 'GIVN' => $match['GIVN']]),
            ];
        }

        return [
            $this->buildName('', ['TYPE' => NameType::VALUE_BIRTH]),
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
            $this->buildName('', ['TYPE' => NameType::VALUE_BIRTH]),
        ];
    }
}
