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

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

use function e;
use function explode;
use function preg_match;
use function strip_tags;
use function view;

/**
 * NOTE can be text or an XREF.
 */
class NoteStructure extends SubmitterText
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
            ' if (shared.disabled) { shared.tomselect.disable(); } else { shared.tomselect.enable(); }' .
            '})' .
            '</script>';
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
        // A note structure can contain an inline note or a linked to a shared note.
        if (preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $value, $match) === 1) {
            $note = Registry::noteFactory()->make($match[1], $tree);

            if ($note === null) {
                return parent::labelValue($value, $tree);
            }

            $value         = $note->getNote();
            $element       = Registry::elementFactory()->make('NOTE');
            $label         = $element->label();
            $html          = $this->valueFormatted($value, $tree);
            $first_line    = '<a href="' . e($note->url()) . '">' . $note->fullName() . '</a>';
            $one_line_only = strip_tags($note->fullName()) === strip_tags($value);
        } else {
            $label         = I18N::translate('Note');
            $html          = $this->valueFormatted($value, $tree);
            [$first_line]  = explode("\n", strip_tags($html));
            $first_line    = Str::limit($first_line, 100, I18N::translate('…'));
            $one_line_only = !str_contains($value, "\n") && mb_strlen($value) <= 100;
        }

        $id       = 'collapse-' . Uuid::uuid4()->toString();
        $expanded = $tree->getPreference('EXPAND_NOTES') === '1';

        if ($one_line_only) {
            return
                 '<div class="fact_NOTE">' .
                 I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', $label, $html) .
                 '</div>';
        }

        return
            '<div class="fact_NOTE">' .
            '<a href="#' . e($id) . '" role="button" data-bs-toggle="collapse" aria-controls="' . e($id) . '" aria-expanded="' . ($expanded ? 'true' : 'false') . '">' .
            view('icons/expand') .
            view('icons/collapse') .
            '</a>' .
            '<span class="label">' . $label . ':</span> ' . $first_line .
            '</div>' .
            '<div id="' . e($id) . '" class="ps-4 collapse ' . ($expanded ? 'show' : '') . '">' .
            $html .
            '</div>';
    }
}
