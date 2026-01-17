<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CreateSubmissionAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree      = Validator::attributes($request)->tree();
        $submitter = Validator::parsedBody($request)->isXref()->string('submitter');

        $submitter = Registry::elementFactory()->make('SUBN:SUBM')->canonical($submitter);

        $gedcom = "0 @@ SUBN\n1 SUBM @" . $submitter . '@';

        $record = $tree->createRecord($gedcom);

        // value and text are for autocomplete
        // html is for interactive modals
        return response([
            'value' => '@' . $record->xref() . '@',
            'text'  => view('selects/submission', ['submission' => $record]),
            'html'  => view('modals/record-created', [
                'title' => I18N::translate('The submission has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
