<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

/**
 * Controller for edit forms and responses.
 */
class EditNoteController extends AbstractEditController
{
    /**
     * Show a form to create a new note object.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editNoteObject(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');

        $note = Factory::note()->make($xref, $tree);
        $note = Auth::checkNoteAccess($note, true);

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
     *
     * @return ResponseInterface
     */
    public function updateNoteObject(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');

        $note = Factory::note()->make($xref, $tree);
        $note = Auth::checkNoteAccess($note, true);

        $params = (array) $request->getParsedBody();

        $NOTE = $params['NOTE'];

        // Convert HTML line endings to GEDCOM continuations
        $NOTE = strtr($NOTE, ["\r\n" => "\n1 CONT "]);

        // "\" and "$" are significant in preg replacement strings, so escape them.
        $NOTE = str_replace(['\\', '$'], ['\\\\', '\\$'], $NOTE);

        $gedrec = preg_replace(
            '/^0 @' . $note->xref() . '@ NOTE.*(\n1 CONT.*)*/',
            '0 @' . $note->xref() . '@ NOTE ' . $NOTE,
            $note->gedcom()
        );

        $note->updateRecord($gedrec, true);

        return redirect($note->url());
    }
}
