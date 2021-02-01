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

use Fisharebest\Webtrees\Http\RequestHandlers\CreateNoteModal;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function e;
use function route;
use function trim;
use function view;

/**
 * XREF:NOTE := {Size=1:22}
 * A pointer to, or a cross-reference identifier of, a note record.
 */
class XrefNote extends AbstractXrefElement
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
        $select = view('components/select-note', [
            'id'   => $id,
            'name' => $name,
            'note' => Registry::noteFactory()->make(trim($value, '@'), $tree),
            'tree' => $tree,
            'at'   => '@',
        ]);

        return
            '<div class="input-group">' .
            '<div class="input-group-prepend">' .
            '<button class="btn btn-secondary" type="button" data-toggle="modal" data-backdrop="static" data-target="#wt-ajax-modal" data-href="' . e(route(CreateNoteModal::class, ['tree' => $tree->name()])) . '" data-select-id="' . $id . '" title="' . I18N::translate('Create a shared note') . '">' .
            view('icons/add') .
            '</button>' .
            '</div>' .
            $select .
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
        return $this->valueXrefLink($value, $tree, Registry::noteFactory());
    }
}
