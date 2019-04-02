<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for edit forms and responses.
 */
class EditNoteController extends AbstractEditController
{
    /**
     * Show a form to create a new note object.
     *
     * @return ResponseInterface
     */
    public function createNoteObject(): ResponseInterface
    {
        return response(view('modals/create-note-object'));
    }

    /**
     * Show a form to create a new note object.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function editNoteObject(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $note = Note::getInstance($xref, $tree);

        Auth::checkNoteAccess($note, true);

        return $this->viewResponse('edit/shared-note', [
            'note'  => $note,
            'title' => I18N::translate('Edit the shared note'),
            'tree'  => $tree,
        ]);
    }

    /**
     * Show a form to create a new note object.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function updateNoteObject(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $note = Note::getInstance($xref, $tree);

        Auth::checkNoteAccess($note, true);

        $NOTE = $request->get('NOTE');

        // "\" and "$" are signficant in replacement strings, so escape them.
        $NOTE = str_replace([
            '\\',
            '$',
        ], [
            '\\\\',
            '\\$',
        ], $NOTE);

        $gedrec = preg_replace(
            '/^0 @' . $note->xref() . '@ NOTE.*(\n1 CONT.*)*/',
            '0 @' . $note->xref() . '@ NOTE ' . preg_replace("/\r?\n/", "\n1 CONT ", $NOTE),
            $note->gedcom()
        );

        $note->updateRecord($gedrec, true);

        return redirect($note->url());
    }

    /**
     * Process a form to create a new note object.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function createNoteObjectAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $note                = $request->get('note', '');
        $privacy_restriction = $request->get('privacy-restriction', '');
        $edit_restriction    = $request->get('edit-restriction', '');

        // Convert line endings to GEDDCOM continuations
        $note = preg_replace('/\r|\r\n|\n|\r/', "\n1 CONT ", $note);

        $gedcom = '0 @@ NOTE ' . $note;

        if (in_array($privacy_restriction, [
            'none',
            'privacy',
            'confidential',
        ])) {
            $gedcom .= "\n1 RESN " . $privacy_restriction;
        }

        if (in_array($edit_restriction, ['locked'])) {
            $gedcom .= "\n1 RESN " . $edit_restriction;
        }

        $record = $tree->createRecord($gedcom);

        return response([
            'id'   => $record->xref(),
            'text' => view('selects/note', [
                'note' => $record,
            ]),
            'html' => view('modals/record-created', [
                'title' => I18N::translate('The note has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
