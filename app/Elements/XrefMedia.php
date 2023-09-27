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
use Fisharebest\Webtrees\Http\RequestHandlers\CreateMediaObjectModal;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function e;
use function preg_match;
use function route;
use function trim;
use function view;

/**
 * XREF:OBJE := {Size=1:22}
 * A pointer to, or a cross-reference identifier of, a multimedia object.
 */
class XrefMedia extends AbstractXrefElement
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
        $select = view('components/select-media', [
            'id'    => $id,
            'name'  => $name,
            'media' => Registry::mediaFactory()->make(trim($value, '@'), $tree),
            'tree'  => $tree,
            'at'    => '@',
        ]);

        return
            '<div class="input-group">' .
            '<button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-backdrop="static" data-bs-target="#wt-ajax-modal" data-wt-href="' . e(route(CreateMediaObjectModal::class, ['tree' => $tree->name()])) . '" data-wt-select-id="' . $id . '" title="' . I18N::translate('Create a media object') . '">' .
            view('icons/add') .
            '</button>' .
            $select .
            '</div>';
    }


    /**
     * Create a label/value pair for this element.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function labelValue(string $value, Tree $tree): string
    {
        // Show the image instead of the label.
        if (preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $value, $match) === 1) {
            $media = Registry::mediaFactory()->make($match[1], $tree);

            if ($media === null) {
                return parent::labelValue($value, $tree);
            }

            $media_file = $media->mediaFiles()->first();

            if ($media_file === null) {
                return parent::labelValue($value, $tree);
            }

            $label = $media_file->displayImage(100, 100, 'contain', []);
            $value = '<a href="' . e($media->url()) . '">' . $media->fullName() . '</a>';

            $label_html = '<div class="pe-1 pb-1 wt-media-link-image">' . $label . '</div>';
            $value_html = '<div class="wt-media-link-title">' . $value . '</div>';

            return '<div class="d-flex align-items-center wt-media-link">' . $label_html . $value_html . '</div>';
        }

        return parent::labelValue($value, $tree);
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
        return $this->valueXrefLink($value, $tree, Registry::mediaFactory());
    }
}
