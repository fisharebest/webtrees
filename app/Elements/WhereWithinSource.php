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

use Fisharebest\Webtrees\Http\RequestHandlers\AutoCompleteCitation;
use Fisharebest\Webtrees\Tree;

use function e;
use function route;

/**
 * WHERE_WITHIN_SOURCE := {Size=1:248}
 * Specific location with in the information referenced. For a published work, this could include
 * the volume of a multi-volume work and the page number(s). For a periodical, it could include
 * volume, issue, and page numbers. For a newspaper, it could include a column number and page
 * number. For an unpublished source or microfilmed works, this could be a film or sheet number,
 * page number, frame number, etc. A census record might have an enumerating district, page number,
 * line number, dwelling number, and family number. The data in this field should be in the form of
 * a label and value pair, such as Label1: value, Label2: value, with each pair being separated by
 * a comma. For example, Film: 1234567, Frame: 344, Line: 28.
 */
class WhereWithinSource extends AbstractElement
{
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
        return '<input data-wt-autocomplete-url="' . e(route(AutoCompleteCitation::class, ['tree' => $tree->name()])) . '" data-wt-autocomplete-extra="SOUR" autocomplete="off" class="form-control" type="text" id="' . e($id) . '" name="' . e($name) . '" value="' . e($value) . '" />';
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
        return $this->valueAutoLink($value);
    }
}
