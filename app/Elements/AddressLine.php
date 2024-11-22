<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
 * ADDRESS_LINE := {Size=1:60}
 * Typically used to define a mailing address of an individual when used
 * subordinate to a RESIdent tag. When it is used subordinate to an event tag
 * it is the address of the place where the event took place. The address
 * lines usually contain the addressee’s name and other street and city
 * information so that it forms an address that meets mailing requirements.
 */
class AddressLine extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 60;

    protected const array SUBTAGS = [
        'ADR1' => '0:1:?',
        'ADR2' => '0:1:?',
        'ADR3' => '0:1:?',
        'CITY' => '0:1:?',
        'STAE' => '0:1:?',
        'POST' => '0:1:?',
        'CTRY' => '0:1:?',
    ];

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return $this->canonicalText($value);
    }

    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        return $this->editTextArea($id, $name, $value);
    }
}
