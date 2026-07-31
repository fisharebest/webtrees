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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Elements\NameType;
use Fisharebest\Webtrees\Enums\Sex;
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
    public function name(): string
    {
        return I18N::translateContext('Surname tradition', 'Icelandic');
    }

    public function description(): string
    {
        /* I18N: In the Icelandic surname tradition, ... */
        return I18N::translate('Children take a patronym instead of a surname.');
    }

    /**
     * A default/empty name
     */
    public function defaultName(): string
    {
        return '';
    }

    /**
     * @return list<string>
     */
    public function newChildNames(Individual|null $father, Individual|null $mother, Sex $sex): array
    {
        if (preg_match(self::REGEX_GIVN, $this->extractName($father), $match) === 1) {
            switch ($sex) {
                case Sex::Male:
                    $givn = $match['GIVN'] . 'sson';

                    return [
                        $this->buildName($givn, ['TYPE' => NameType::VALUE_BIRTH, 'GIVN' => $givn]),
                    ];

                case Sex::Female:
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
     * @return list<string>
     */
    public function newParentNames(Individual $child, Sex $sex): array
    {
        if ($sex === Sex::Male && preg_match('~(?<GIVN>[^ /]+)(:?sson)$~', $this->extractName($child), $match) === 1) {
            return [
                $this->buildName($match['GIVN'], ['TYPE' => NameType::VALUE_BIRTH, 'GIVN' => $match['GIVN']]),
            ];
        }

        if ($sex === Sex::Female && preg_match('~(?<GIVN>[^ /]+)(:?sdottir)$~', $this->extractName($child), $match) === 1) {
            return [
                $this->buildName($match['GIVN'], ['TYPE' => NameType::VALUE_BIRTH, 'GIVN' => $match['GIVN']]),
            ];
        }

        return [
            $this->buildName('', ['TYPE' => NameType::VALUE_BIRTH]),
        ];
    }

    /**
     * @return list<string>
     */
    public function newSpouseNames(Individual $spouse, Sex $sex): array
    {
        return [
            $this->buildName('', ['TYPE' => NameType::VALUE_BIRTH]),
        ];
    }
}
