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
        $title        = Validator::parsedBody($request)->isNotEmpty()->string('source-title');
        $abbreviation = Validator::parsedBody($request)->string('source-abbreviation');
        $author       = Validator::parsedBody($request)->string('source-author');
        $publication  = Validator::parsedBody($request)->string('source-publication');
        $repository   = Validator::parsedBody($request)->isXref()->string('source-repository', '');
        $call_number  = Validator::parsedBody($request)->string('source-call-number');
        $text         = Validator::parsedBody($request)->string('source-text');
        $restriction  = Validator::parsedBody($request)->string('restriction');

        $title        = Registry::elementFactory()->make('SOUR:TITL')->canonical($title);
        $abbreviation = Registry::elementFactory()->make('SOUR:ABBR')->canonical($abbreviation);
        $author       = Registry::elementFactory()->make('SOUR:AUTH')->canonical($author);
        $publication  = Registry::elementFactory()->make('SOUR:PUBL')->canonical($publication);
        $repository   = Registry::elementFactory()->make('SOUR:REPO')->canonical($repository);
        $call_number  = Registry::elementFactory()->make('SOUR:REPO:CALN')->canonical($call_number);
        $text         = Registry::elementFactory()->make('SOUR:TEXT')->canonical($text);
        $restriction  = Registry::elementFactory()->make('SOUR:RESN')->canonical($restriction);

        $gedcom = "0 @@ SOUR\n1 TITL " . strtr($title, ["\n" => "\n2 CONT "]);

        if ($abbreviation !== '') {
            $gedcom .= "\n1 ABBR " . strtr($abbreviation, ["\n" => "\n2 CONT "]);
        }

        if ($author !== '') {
            $gedcom .= "\n1 AUTH " . strtr($author, ["\n" => "\n2 CONT "]);
        }

        if ($publication !== '') {
            $gedcom .= "\n1 PUBL " . strtr($publication, ["\n" => "\n2 CONT "]);
        }

        if ($text !== '') {
            $gedcom .= "\n1 TEXT " . strtr($text, ["\n" => "\n2 CONT "]);
        }

        if ($repository !== '') {
            $gedcom .= "\n1 REPO @" . $repository . '@';

            if ($call_number !== '') {
                $gedcom .= "\n2 CALN " . strtr($call_number, ["\n" => "\n3 CONT "]);
            }
        }

        if ($restriction !== '') {
            $gedcom .= "\n1 RESN " . strtr($restriction, ["\n" => "\n2 CONT "]);
        }

        $record = $tree->createRecord($gedcom);

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
