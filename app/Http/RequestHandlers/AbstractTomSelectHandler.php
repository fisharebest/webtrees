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

use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Autocomplete for TomSelect based controls.
 */
abstract class AbstractTomSelectHandler implements RequestHandlerInterface
{
    // For clients that request one page of data at a time.
    private const RESULTS_PER_PAGE = 50;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $at    = Validator::queryParams($request)->isInArray(['', '@'])->string('at');
        $page  = Validator::queryParams($request)->integer('page', 1);
        $query = Validator::queryParams($request)->string('query');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        // Perform the search.
        if ($query !== '') {
            $results = $this->search($tree, $query, $offset, $limit, $at ? '@' : '');
        } else {
            $results = new Collection();
        }

        if ($results->count() > self::RESULTS_PER_PAGE) {
            $next_url = route(static::class, ['tree' => $tree->name(), 'at' => $at ? '@' : '', 'page' => $page + 1]);
        } else {
            $next_url = null;
        }

        return response([
            'data'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'nextUrl' => $next_url,
        ]);
    }

    /**
     * Perform the search
     *
     * @param Tree   $tree
     * @param string $query
     * @param int    $offset
     * @param int    $limit
     * @param string $at    "@" or ""
     *
     * @return Collection<int,array{text:string,value:string}>
     */
    abstract protected function search(Tree $tree, string $query, int $offset, int $limit, string $at): Collection;
}
