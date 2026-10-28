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
 * Children take their mother’s surname.
 */
class MatrilinealSurnameTradition extends DefaultSurnameTradition
{
    public function name(): string
    {
        /* I18N: A system where children take their mother’s surname */
        return I18N::translate('matrilineal');
    }

    public function description(): string
    {
        /* I18N: In the matrilineal surname tradition, ... */
        return I18N::translate('Children take their mother’s surname.');
    }

    /**
     * @return list<string>
     */
    public function newChildNames(Individual|null $father, Individual|null $mother, Sex $sex): array
    {
        if (preg_match(self::REGEX_SPFX_SURN, $this->extractName($mother), $match) === 1) {
            $name = $match['NAME'];
            $spfx = $match['SPFX'];
            $surn = $match['SURN'];

            return [
                $this->buildName($name, ['TYPE' => NameType::VALUE_BIRTH, 'SPFX' => $spfx, 'SURN' => $surn]),
            ];
        }

        return parent::newChildNames($father, $mother, $sex);
    }

    /**
     * @return list<string>
     */
    public function newParentNames(Individual $child, Sex $sex): array
    {
        if ($sex === Sex::Female && preg_match(self::REGEX_SPFX_SURN, $this->extractName($child), $match) === 1) {
            $name = $match['NAME'];
            $spfx = $match['SPFX'];
            $surn = $match['SURN'];

            return [
                $this->buildName($name, ['TYPE' => NameType::VALUE_BIRTH, 'SPFX' => $spfx, 'SURN' => $surn]),
            ];
        }

        return parent::newParentNames($child, $sex);
    }
}
