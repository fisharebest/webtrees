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

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;

use function e;
use function preg_match;

/**
 * NOTE can be text or an XREF.
 */
class NoteStructure extends AbstractElement
{
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
        $submitter_text = new SubmitterText('');
        $xref_note      = new XrefNote('');

        // Existing shared note.
        if (preg_match('/^@' . Gedcom::REGEX_XREF . '@$/', $value)) {
            return $xref_note->edit($id, $name, $value, $tree);
        }

        // Existing inline note.
        if ($value !== '') {
            return $submitter_text->edit($id, $name, $value, $tree);
        }

        $options = [
            'inline' => I18N::translate('inline note'),
            'shared' => I18N::translate('shared note'),
        ];

        // New note - either inline or shared
        return
            '<div id="' . e($id) . '-note-structure">' .
            '<div id="' . e($id) . '-options">' .
            view('components/radios-inline', ['name' => $id . '-options', 'options' => $options, 'selected' => 'inline']) .
            '</div>' .
            '<div id="' . e($id) . '-inline">' .
            $submitter_text->edit($id, $name, $value, $tree) .
            '</div>' .
            '<div id="' . e($id) . '-shared" class="d-none">' .
            $xref_note->edit($id . '-select', $name, $value, $tree) .
            '</div>' .
            '</div>' .
            '<script>' .
            'document.getElementById("' . e($id) . '-shared").querySelector("select").disabled=true;' .
            'document.getElementById("' . e($id) . '-options").addEventListener("change", function(){' .
            ' document.getElementById("' . e($id) . '-inline").classList.toggle("d-none");' .
            ' document.getElementById("' . e($id) . '-shared").classList.toggle("d-none");' .
            ' const inline = document.getElementById("' . e($id) . '-inline").querySelector("textarea");' .
            ' const shared = document.getElementById("' . e($id) . '-shared").querySelector("select");' .
            ' inline.disabled = !inline.disabled;' .
            ' shared.disabled = !shared.disabled;' .
            '})' .
            '</script>';
    }
}
