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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Tree;

/**
 * EVENT_DESCRIPTOR := {Size=1:90}
 * Text describing a particular event pertaining to the individual or family.
 * This event value is usually assigned to the EVEN tag. The classification as
 * to the difference between this specific event and other occurrences of the
 * EVENt tag is indicated by the use of a subordinate TYPE tag selected from
 * the EVENT_DETAIL structure. For example;
 * 1 EVEN Appointed Zoning Committee Chairperson
 * 2 TYPE Civic Appointments
 * 2 DATE FROM JAN 1952 TO JAN 1956
 * 2 PLAC Cove, Cache, Utah
 * 2 AGNC Cove City Redevelopment
 */
class EventDescriptor extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 90;

    protected const SUBTAGS = [
        'DATE' => '0:1',
        'PLAC' => '0:1',
        'NOTE' => '0:M',
        'OBJE' => '0:M',
        'SOUR' => '0:M',
        'RESN' => '0:1',
    ];

    /**
     * Display the value of this type of element.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function value(string $value, Tree $tree): string
    {
        // This is a special value used for creating events of close relatives.
        if ($value === 'CLOSE_RELATIVE') {
            return '';
        }

        return parent::value($value, $tree);
    }
}
