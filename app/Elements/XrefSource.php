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

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\RequestHandlers\CreateSourceModal;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function e;
use function route;
use function str_ends_with;
use function str_starts_with;
use function trim;
use function view;

/**
 * XREF:SOUR := {Size=1:22}
 * A pointer to, or a cross-reference identifier of, a SOURce record.
 */
class XrefSource extends AbstractXrefElement
{
    protected const array SUBTAGS = [
        'PAGE' => '0:1',
        'EVEN' => '0:1',
        'DATA' => '0:1',
        'OBJE' => '0:M',
        'NOTE' => '0:M',
        'QUAY' => '0:1',
    ];

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
        // Other applications create sources with text, rather than XREFs
        if ($value === '' || preg_match('/^@' . Gedcom::REGEX_XREF . '@$/', $value)) {
            $select = view('components/select-source', [
                'id'     => $id,
                'name'   => $name,
                'source' => Registry::sourceFactory()->make(trim($value, '@'), $tree),
                'tree'   => $tree,
                'at'     => '@',
            ]);

            return
                '<div class="input-group">' .
                '<button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#wt-ajax-modal" data-wt-href="' . e(route(CreateSourceModal::class, ['tree' => $tree->name()])) . '" data-wt-select-id="' . $id . '" title="' . I18N::translate('Create a source') . '">' .
                view('icons/add') .
                '</button>' .
                $select .
                '</div>';
        }

        return $this->editTextArea($id, $name, $value);
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
        if (str_starts_with($value, '@') && str_ends_with($value, '@')) {
            return $this->valueXrefLink($value, $tree, Registry::sourceFactory());
        }

        // Inline sources are deprecated - but used by some historic events
        return $this->valueFormatted($value, $tree);
    }
}
