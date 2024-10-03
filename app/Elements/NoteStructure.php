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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function e;
use function preg_match;
use function strip_tags;
use function substr_count;
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
        $id       = Registry::idFactory()->id();
        $expanded = $tree->getPreference('EXPAND_NOTES') === '1';

        // A note structure can contain an inline note or a linked to a shared note.
        if (preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $value, $match) === 1) {
            $note = Registry::noteFactory()->make($match[1], $tree);

            if ($note === null) {
                return parent::labelValue($value, $tree);
            }

            if (!$note->canShow()) {
                return '';
            }

            $label         = '<span class="label">' . I18N::translate('Shared note') . '</span>';
            $value         = $note->getNote();
            $html          = $this->valueFormatted($value, $tree);
            $first_line    = '<a href="' . e($note->url()) . '">' . $note->fullName() . '</a>';

            // Shared note where the title is the same as the text
            if ($html === '<p>' . strip_tags($note->fullName()) . '</p>') {
                $value = '<a href="' . e($note->url()) . '">' . strip_tags($html) . '</a>';

                return '<div>' . I18N::translate('%1$s: %2$s', $label, $value) . '</div>';
            }

            return
                '<div class="wt-text-overflow-elipsis">' .
                '<button type="button" class="btn btn-text p-0" href="#' . e($id) . '" data-bs-toggle="collapse" aria-controls="' . e($id) . '" aria-expanded="' . ($expanded ? 'true' : 'false') . '">' .
                view('icons/expand') .
                view('icons/collapse') .
                '</button> ' .
                '<span class="label">' . $label . ':</span> ' . $first_line .
                '</div>' .
                '<div id="' . e($id) . '" class="ps-4 collapse ut' . ($expanded ? 'show' : '') . '">' .
                $html .
                '</div>';
        }

        $label = '<span class="label">' . I18N::translate('Note') . '</span>';
        $html  = $this->valueFormatted($value, $tree);

        // Inline note with only one paragraph and inline markup?
        if ($html === strip_tags($html, ['a', 'em', 'p', 'strong']) && substr_count($html, '<p>') === 1) {
            $html  = strip_tags($html, ['a', 'em', 'strong']);
            $value = '<span class="ut">' . $html . '</span>';

            return '<div>' . I18N::translate('%1$s: %2$s', $label, $value) . '</div>';
        }

        $value = e(Note::firstLineOfTextFromHtml($html));
        $value = '<span class="ut collapse ' . ($expanded ? '' : 'show') . ' ' . e($id) . '">' . $value . '</span>';

        return
            '<div class="wt-text-overflow-elipsis">' .
            '<button type="button" class="btn btn-text p-0" href="#" data-bs-target=".' . e($id) . '" data-bs-toggle="collapse" aria-controls="' . e($id) . '" aria-expanded="' . ($expanded ? 'true' : 'false') . '">' .
            view('icons/expand') .
            view('icons/collapse') .
            '</button> ' .
            I18N::translate('%1$s: %2$s', $label, $value) .
            '</div>' .
            '<div class="ps-4 collapse ut ' . ($expanded ? 'show' : '') . ' ' . e($id) . '">' .
            $html .
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
        if (preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $value, $match) === 1) {
            $note = Registry::noteFactory()->make($match[1], $tree);

            if ($note instanceof Note) {
                $value = $note->getNote();
            }
        }

        return parent::value($value, $tree);
    }
}
