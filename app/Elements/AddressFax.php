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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Tree;

use function e;
use function preg_match;

/**
 * ADDRESS_FAX := {Size=5:60}
 * A FAX telephone number appropriate for sending data facsimiles.
 */
class AddressFax extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 60;

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
        if (preg_match('/^[+0-9-]+$/', $value)) {
            return '<span dir="ltr">' . e($value) . '</span>';
        }

        return e($value);
    }
}
