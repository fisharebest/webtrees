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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * Process a form to create a new note object.
 */
class CreateNoteAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params              = (array) $request->getParsedBody();
        $note                = $params['note'];
        $privacy_restriction = $params['privacy-restriction'];
        $edit_restriction    = $params['edit-restriction'];

        // Convert HTML line endings to GEDCOM continuations
        $note = strtr($note, ["\r\n" => "\n1 CONT "]);

        $gedcom = '0 @@ NOTE ' . $note;

        if (in_array($privacy_restriction, ['none', 'privacy', 'confidential'], true)) {
            $gedcom .= "\n1 RESN " . $privacy_restriction;
        }

        if ($edit_restriction === 'locked') {
            $gedcom .= "\n1 RESN " . $edit_restriction;
        }

        $record = $tree->createRecord($gedcom);
        $record = Registry::noteFactory()->new($record->xref(), $record->gedcom(), null, $tree);

        // id and text are for select2 / autocomplete
        // html is for interactive modals
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
