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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

/**
 * Process a form to create a new submitter.
 */
class CreateSubmitterAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $name        = Validator::parsedBody($request)->string('submitter_name');
        $address     = Validator::parsedBody($request)->string('submitter_address');
        $email       = Validator::parsedBody($request)->string('submitter_email');
        $phone       = Validator::parsedBody($request)->string('submitter_phone');
        $restriction = Validator::parsedBody($request)->string('restriction');

        // Fix non-printing characters
        $name = trim(preg_replace('/\s+/', ' ', $name));

        $gedcom = "0 @@ SUBM\n1 NAME " . $name;

        if ($address !== '') {
            $gedcom .= "\n1 ADDR " . $address;
        }

        if ($email !== '') {
            $gedcom .= "\n1 EMAIL " . $email;
        }

        if ($phone !== '') {
            $gedcom .= "\n1 PHON " . $phone;
        }

        if (in_array($restriction, ['none', 'privacy', 'confidential', 'locked'], true)) {
            $gedcom .= "\n1 RESN " . $restriction;
        }

        $record = $tree->createRecord($gedcom);
        $record = Registry::submitterFactory()->new($record->xref(), $record->gedcom(), null, $tree);

        // value and text are for autocomplete
        // html is for interactive modals
        return response([
            'value' => '@' . $record->xref() . '@',
            'text'  => view('selects/submitter', ['submitter' => $record]),
            'html'  => view('modals/record-created', [
                'title' => I18N::translate('The submitter has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
