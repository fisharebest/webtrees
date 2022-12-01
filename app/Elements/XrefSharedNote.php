<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
 * XREF:SNOTE := {Size=1:22}
 * A pointer to, or a cross-reference identifier of, a shared note record.
 */
class XrefSharedNote extends AbstractXrefElement
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
        $select = view('components/select-shared-note', [
            'id'          => $id,
            'name'        => $name,
            'shared_note' => Registry::sharedNoteFactory()->make(trim($value, '@'), $tree),
            'tree'        => $tree,
            'at'          => '@',
        ]);

        return
            '<div class="input-group">' .
            '<button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-backdrop="static" data-bs-target="#wt-ajax-modal" data-wt-href="' . e(route(CreateNoteModal::class, ['tree' => $tree->name()])) . '" data-wt-select-id="' . $id . '" title="' . I18N::translate('Create a shared note') . '">' .
            view('icons/add') .
            '</button>' .
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
        return $this->valueXrefLink($value, $tree, Registry::sharedNoteFactory());
    }
}
