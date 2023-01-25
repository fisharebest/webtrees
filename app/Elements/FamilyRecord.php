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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Site;

/**
 * A level 0 family record
 */
class FamilyRecord extends AbstractElement
{
    protected const SUBTAGS = [
        'ANUL' => '0:M',
        'CENS' => '0:M',
        'CHAN' => '0:1',
        'CHIL' => '0:M',
        'DIV'  => '0:M',
        'DIVF' => '0:M',
        'ENGA' => '0:M',
        'EVEN' => '0:M',
        'HUSB' => '0:1',
        'MARB' => '0:M',
        'MARC' => '0:M',
        'MARL' => '0:M',
        'MARR' => '0:M',
        'MARS' => '0:M',
        'NCHI' => '0:1',
        'NOTE' => '0:M',
        'OBJE' => '0:M',
        'REFN' => '0:M',
        'RESI' => '0:M',
        'RESN' => '0:1',
        'RIN'  => '0:1',
        'SLGS' => '0:M',
        'SOUR' => '0:M',
        'SUBM' => '0:M',
        'WIFE' => '0:1',
    ];

    /**
     * @return array<string,string>
     */
    public function subtags(): array
    {
        $subtags = parent::subtags();

        if (Site::getPreference('HIDE_FAM_RESI') === '1') {
            unset($subtags['RESI']);
        }

        if (Site::getPreference('HIDE_FAM_CENS') === '1') {
            unset($subtags['CENS']);
        }

        return $subtags;
    }
}
