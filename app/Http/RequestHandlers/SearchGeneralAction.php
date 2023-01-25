<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Search for genealogy data
 */
class SearchGeneralAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return redirect(route(SearchGeneralPage::class, [
            'query'               => Validator::parsedBody($request)->string('query'),
            'search_families'     => Validator::parsedBody($request)->boolean('search_families', false),
            'search_individuals'  => Validator::parsedBody($request)->boolean('search_individuals', false),
            'search_locations'    => Validator::parsedBody($request)->boolean('search_locations', false),
            'search_notes'        => Validator::parsedBody($request)->boolean('search_notes', false),
            'search_repositories' => Validator::parsedBody($request)->boolean('search_repositories', false),
            'search_sources'      => Validator::parsedBody($request)->boolean('search_sources', false),
            'search_trees'        => Validator::parsedBody($request)->array('search_trees'),
            'tree'                => Validator::attributes($request)->tree()->name(),
        ]));
    }
}
