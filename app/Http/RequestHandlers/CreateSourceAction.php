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
 * Process a form to create a new source.
 */
class CreateSourceAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree         = Validator::attributes($request)->tree();
        $title        = Validator::parsedBody($request)->string('source-title');
        $abbreviation = Validator::parsedBody($request)->string('source-abbreviation');
        $author       = Validator::parsedBody($request)->string('source-author');
        $publication  = Validator::parsedBody($request)->string('source-publication');
        $repository   = Validator::parsedBody($request)->string('source-repository');
        $call_number  = Validator::parsedBody($request)->string('source-call-number');
        $text         = Validator::parsedBody($request)->string('source-text');
        $restriction  = Validator::parsedBody($request)->string('restriction');

        // Fix non-printing characters
        $title        = trim(preg_replace('/\s+/', ' ', $title));
        $abbreviation = trim(preg_replace('/\s+/', ' ', $abbreviation));
        $author       = trim(preg_replace('/\s+/', ' ', $author));
        $publication  = trim(preg_replace('/\s+/', ' ', $publication));
        $repository   = trim(preg_replace('/\s+/', ' ', $repository));
        $call_number  = trim(preg_replace('/\s+/', ' ', $call_number));

        // Convert HTML line endings to GEDCOM continuations
        $text = strtr($text, ["\r\n" => "\n2 CONT "]);

        $gedcom = "0 @@ SOUR\n\n1 TITL " . $title;

        if ($abbreviation !== '') {
            $gedcom .= "\n1 ABBR " . $abbreviation;
        }

        if ($author !== '') {
            $gedcom .= "\n1 AUTH " . $author;
        }

        if ($publication !== '') {
            $gedcom .= "\n1 PUBL " . $publication;
        }

        if ($text !== '') {
            $gedcom .= "\n1 TEXT " . $text;
        }

        if ($repository !== '') {
            $gedcom .= "\n1 REPO @" . $repository . '@';

            if ($call_number !== '') {
                $gedcom .= "\n2 CALN " . $call_number;
            }
        }

        if (in_array($restriction, ['none', 'privacy', 'confidential', 'locked'], true)) {
            $gedcom .= "\n1 RESN " . $restriction;
        }

        $record = $tree->createRecord($gedcom);
        $record = Registry::sourceFactory()->new($record->xref(), $record->gedcom(), null, $tree);

        // value and text are for autocomplete
        // html is for interactive modals
        return response([
            'value' => '@' . $record->xref() . '@',
            'text'  => view('selects/source', ['source' => $record]),
            'html'  => view('modals/record-created', [
                'title' => I18N::translate('The source has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
