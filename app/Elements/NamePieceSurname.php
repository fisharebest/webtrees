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

use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompleteSurname;
use Fisharebest\Webtrees\Tree;

use function e;
use function view;

/**
 * NAME_PIECE_SUFFIX := {Size=1:30}
 * [ <NAME_PIECE> | <NAME_PIECE_SURNAME>, <NAME_PIECE> ]
 * Surname or family name. Different surnames are separated by a comma.
 */
class NamePieceSurname extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 30;

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
            '<input data-wt-autocomplete-url="' . e(route(AutoCompleteSurname::class, ['tree' => $tree->name()])) . '" autocomplete="off" class="form-control" dir="auto" type="text" id="' . e($id) . '" name="' . e($name) . '" value="' . e($value) . '" />' .
            view('edit/input-addon-keyboard', ['id' => $id]) .
            view('edit/input-addon-help', ['topic' => 'SURN']) .
            '</div>';
    }
}
