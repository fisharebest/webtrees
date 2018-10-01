<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);


namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;

/**
 * The individual's sex.
 */
class CensusColumnSexMF extends AbstractCensusColumn implements CensusColumnInterface
{
    /** @var string Text to display for male individuals */
    protected $male = 'M';

    /** @var string Text to display for female individuals */
    protected $female = 'F';

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
        switch ($individual->getSex()) {
            case 'M':
                return $this->male;
            case 'F':
                return $this->female;
            default:
                return '';
        }
    }
}
