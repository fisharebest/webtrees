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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for edit forms and responses.
 */
class EditRepositoryController extends AbstractEditController
{
    /**
     * Show a form to create a new repository.
     *
     * @return ResponseInterface
     */
    public function createRepository(): ResponseInterface
    {
        return response(view('modals/create-repository'));
    }

    /**
     * Process a form to create a new repository.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function createRepositoryAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $name                = $request->get('repository-name', '');
        $privacy_restriction = $request->get('privacy-restriction', '');
        $edit_restriction    = $request->get('edit-restriction', '');

        // Fix whitespace
        $name = trim(preg_replace('/\s+/', ' ', $name));

        $gedcom = "0 @@ REPO\n1 NAME " . $name;

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

        // id and text are for select2 / autocomplete
        // html is for interactive modals
        return response([
            'id'   => $record->xref(),
            'text' => view('selects/repository', [
                'repository' => $record,
            ]),
            'html' => view('modals/record-created', [
                'title' => I18N::translate('The repository has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
