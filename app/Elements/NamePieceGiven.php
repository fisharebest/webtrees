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

use function e;
use function view;

/**
 * NAME_PERSONAL := {Size=1:120}
 * [ <NAME_PIECE> | <NAME_PIECE_GIVEN>, <NAME_PIECE> ]
 * Given name or earned name. Different given names are separated by a comma.
 */
class NamePieceGiven extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 120;

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
        return
            '<div class="input-group">' .
            parent::edit($id, $name, $value, $tree) .
            view('edit/input-addon-keyboard', ['id' => $id]) .
            '</div>';
    }

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
        return '<bdi>' . preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', e($value)) . '</bdi>';
    }
}
