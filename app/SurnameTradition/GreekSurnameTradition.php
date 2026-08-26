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
 * Greek — Children take their father's surname. Wives take their husband's surname. Surnames are inflected to indicate an individual's sex.
 */
class GreekSurnameTradition extends PaternalSurnameTradition
{
    // Inflect a surname for females (masculine to feminine)
    private const array INFLECT_FEMALE = [
        // Greek script — specific suffixes first to avoid premature general matches
        'όπουλος\b' => 'οπούλου',
        'ίδης\b'    => 'ίδου',
        'άδης\b'    => 'άδου',
        'ός\b'      => 'ού',
        'ος\b'      => 'ου',
        'ής\b'      => 'ή',
        'ης\b'      => 'η',
        'άς\b'      => 'ά',
        'ας\b'      => 'α',
        // Latin transliterations — distinctive Greek patterns
        'opoulos\b' => 'opoulou',
        'akis\b'    => 'aki',
        'idis\b'    => 'idou',
        'adis\b'    => 'adou',
    ];

    // Inflect a surname for males (feminine to masculine)
    private const array INFLECT_MALE = [
        // Greek script — specific suffixes first
        'οπούλου\b' => 'όπουλος',
        'ίδου\b'    => 'ίδης',
        'άδου\b'    => 'άδης',
        'ού\b'      => 'ός',
        'ου\b'      => 'ος',
        'ή\b'       => 'ής',
        'η\b'       => 'ης',
        'ά\b'       => 'άς',
        'α\b'       => 'ας',
        // Latin transliterations
        'opoulou\b' => 'opoulos',
        'aki\b'     => 'akis',
        'idou\b'    => 'idis',
        'adou\b'    => 'adis',
    ];

    public function name(): string
    {
        return I18N::translateContext('Surname tradition', 'Greek');
    }

    public function description(): string
    {
        /* I18N: In the Greek surname tradition, ... */
        return
            I18N::translate('Children take their father’s surname.') . ' ' .
            I18N::translate('Wives take their husband’s surname.') . ' ' .
            I18N::translate('Surnames are inflected to indicate an individual’s sex.');
    }

    /**
     * @return list<string>
     */
    public function newChildNames(Individual|null $father, Individual|null $mother, Sex $sex): array
    {
        if (preg_match(self::REGEX_SURN, $this->extractName($father), $match) === 1) {
            if ($sex === Sex::Female) {
                $name = $this->inflect($match['NAME'], self::INFLECT_FEMALE);
            } else {
                $name = $this->inflect($match['NAME'], self::INFLECT_MALE);
            }

            $surn = $this->inflect($match['SURN'], self::INFLECT_MALE);

            return [$this->buildName($name, ['TYPE' => NameType::VALUE_BIRTH, 'SURN' => $surn])];
        }

        return [$this->buildName('//', ['TYPE' => NameType::VALUE_BIRTH])];
    }

    /**
     * @return list<string>
     */
    public function newParentNames(Individual $child, Sex $sex): array
    {
        if ($sex === Sex::Male && preg_match(self::REGEX_SURN, $this->extractName($child), $match) === 1) {
            $name = $this->inflect($match['NAME'], self::INFLECT_MALE);
            $surn = $this->inflect($match['SURN'], self::INFLECT_MALE);

            return [
                $this->buildName($name, ['TYPE' => NameType::VALUE_BIRTH, 'SURN' => $surn]),
            ];
        }

        return [$this->buildName('//', ['TYPE' => NameType::VALUE_BIRTH])];
    }

    /**
     * @return list<string>
     */
    public function newSpouseNames(Individual $spouse, Sex $sex): array
    {
        if ($sex === Sex::Female && preg_match(self::REGEX_SURN, $this->extractName($spouse), $match) === 1) {
            $name = $this->inflect($match['NAME'], self::INFLECT_FEMALE);
            $surn = $this->inflect($match['SURN'], self::INFLECT_MALE);

            return [
                $this->buildName('//', ['TYPE' => NameType::VALUE_BIRTH]),
                $this->buildName($name, ['TYPE' => NameType::VALUE_MARRIED, 'SURN' => $surn]),
            ];
        }

        return [$this->buildName('//', ['TYPE' => NameType::VALUE_BIRTH])];
    }
}
