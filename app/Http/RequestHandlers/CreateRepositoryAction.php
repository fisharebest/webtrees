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

use function preg_replace;
use function response;
use function trim;
use function view;

/**
 * Process a form to create a new repository.
 */
class CreateRepositoryAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $name        = Validator::parsedBody($request)->string('name');
        $address     = Validator::parsedBody($request)->string('address');
        $url         = Validator::parsedBody($request)->string('url');
        $restriction = Validator::parsedBody($request)->isInArray(['', 'NONE', 'PRIVACY', 'CONFIDENTIAL', 'LOCKED'])->string('restriction');

        // Fix non-printing characters
        $name = trim(preg_replace('/\s+/', ' ', $name));

        $gedcom = "0 @@ REPO\n1 NAME " . $name;

        if ($address !== '') {
            $gedcom .= "\n1 ADDR " . strtr($address, ["\r\n" => "\n2 CONT "]);
        }

        if ($url !== '') {
            $gedcom .= "\n1 WWW " . $url;
        }

        if ($restriction !== '') {
            $gedcom .= "\n1 RESN " . $restriction;
        }

        $record = $tree->createRecord($gedcom);
        $record = Registry::repositoryFactory()->new($record->xref(), $record->gedcom(), null, $tree);

        // value and text are for autocomplete
        // html is for interactive modals
        return response([
            'value' => '@' . $record->xref() . '@',
            'text'  => view('selects/repository', ['repository' => $record]),
            'html'  => view('modals/record-created', [
                'title' => I18N::translate('The repository has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
