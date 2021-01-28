<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function response;
use function strlen;

/**
 * Autocomplete for Select2 based controls.
 */
abstract class AbstractSelect2Handler implements RequestHandlerInterface
{
    // For clients that request one page of data at a time.
    private const RESULTS_PER_PAGE = 20;

    // Minimum number of characters for a search.
    public const MINIMUM_INPUT_LENGTH = 2;

    // Wait for the user to pause typing before sending request.
    public const AJAX_DELAY = 350;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = (array) $request->getParsedBody();
        $query  = $params['q'] ?? '';
        $page   = (int) ($params['page'] ?? 1);
        $at     = (bool) ($request->getQueryParams()['at'] ?? false);

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        // Perform the search.
        if (strlen($query) >= self::MINIMUM_INPUT_LENGTH) {
            $results = $this->search($tree, $query, $offset, $limit, $at ? '@' : '');
        } else {
            $results = new Collection();
        }

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
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
     * @return Collection<array<string,string>>
     */
    abstract protected function search(Tree $tree, string $query, int $offset, int $limit, string $at): Collection;
}
