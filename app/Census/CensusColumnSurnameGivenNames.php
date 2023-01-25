<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * The individual's full name.
 */
class CensusColumnSurnameGivenNames extends AbstractCensusColumn implements CensusColumnInterface
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
        $name        = $this->nameAtCensusDate($individual);
        $surname     = strtr($name['surname'], [Individual::NOMEN_NESCIO => I18N::translateContext('Unknown surname', '…')]);
        $given_names = strtr($name['givn'], [Individual::PRAENOMEN_NESCIO => I18N::translateContext('Unknown given name', '…')]);

        return $surname . ', ' . $given_names;
    }
}
