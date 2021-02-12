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

use function mb_substr;
use function pathinfo;
use function str_ends_with;
use function strtolower;

use const PATHINFO_EXTENSION;

/**
 * NAME_OF_FAMILY_FILE := {Size=1:120}
 * Name under which family names for ordinances are stored in the temple's
 * family file.
 */
class NameOfFamilyFile extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 120;

    /**
     * Create a default value for this element.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function default(Tree $tree): string
    {
        $value = mb_substr($tree->name(), 0, self::MAXIMUM_LENGTH);

        $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));

        if ($extension !== 'ged') {
            $value = mb_substr($tree->name(), 0, self::MAXIMUM_LENGTH - 4) . '.ged';
        }

        return $value;
    }
}
