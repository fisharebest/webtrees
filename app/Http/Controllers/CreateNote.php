<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validate;
use Psr\Http\Message\ResponseInterface;

use function response;
use function view;

final class CreateNote
{
    public function __construct(
        private Validate $validate,
    ) {
    }

    public function get(Tree $tree): ResponseInterface
    {
        return response(view('modals/create-note-object', [
            'tree' => $tree,
        ]));
    }

    public function post(Tree $tree, string $note, string $restriction): ResponseInterface
    {
        $this->validate->notEmpty($note, 'note');

        $note        = Registry::elementFactory()->make('NOTE:CONT')->canonical($note);
        $restriction = Registry::elementFactory()->make('NOTE:RESN')->canonical($restriction);

        $gedcom = '0 @@ NOTE ' . strtr($note, ["\n" => "\n1 CONT "]);

        if ($restriction !== '') {
            $gedcom .= "\n1 RESN " . strtr($restriction, ["\n" => "\n2 CONT "]);
        }

        $record = $tree->createRecord($gedcom);

        // value and text are for autocomplete
        // HTML is for interactive modals
        return response([
            'value' => '@' . $record->xref() . '@',
            'text'  => view('selects/note', ['note' => $record]),
            'html'  => view('modals/record-created', [
                'title' => I18N::translate('The note has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
