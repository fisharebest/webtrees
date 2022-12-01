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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Search for genealogy data
 */
class SearchQuickAction implements RequestHandlerInterface
{
    /**
     * The "omni-search" box in the header.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $query = Validator::parsedBody($request)->string('query');

        // Was the search query an XREF in the current tree?
        // If so, go straight to it.
        $record = Registry::gedcomRecordFactory()->make($query, $tree);

        if ($record instanceof GedcomRecord && $record->canShow()) {
            return redirect($record->url());
        }

        return redirect(route(SearchGeneralPage::class, ['tree' => $tree->name(), 'query' => $query]));
    }
}
