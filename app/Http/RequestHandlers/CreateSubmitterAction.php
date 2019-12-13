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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params              = (array) $request->getParsedBody();
        $name                = $params['submitter_name'];
        $address             = $params['submitter_address'];
        $privacy_restriction = $params['privacy-restriction'];
        $edit_restriction    = $params['edit-restriction'];

        // Fix whitespace
        $name = trim(preg_replace('/\s+/', ' ', $name));

        $gedcom = "0 @@ SUBM\n1 NAME " . $name;

        if ($address !== '') {
            $gedcom .= "\n1 ADDR " . $address;
        }

        if (in_array($privacy_restriction, ['none', 'privacy', 'confidential'], true)) {
            $gedcom .= "\n1 RESN " . $privacy_restriction;
        }

        if ($edit_restriction === 'locked') {
            $gedcom .= "\n1 RESN " . $edit_restriction;
        }

        $record = $tree->createRecord($gedcom);

        return response([
            'id'   => $record->xref(),
            'text' => view('selects/submitter', [
                'submitter' => $record,
            ]),
            'html' => view('modals/record-created', [
                'title' => I18N::translate('The submitter has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
