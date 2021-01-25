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

use Fisharebest\Webtrees\Services\SearchService;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Autocomplete handler
 */
abstract class AbstractAutocompleteHandler implements RequestHandlerInterface
{
    // The client software only shows the first few results
    protected const LIMIT = 10;

    // Tell the browser to cache the results
    protected const CACHE_LIFE = 1200;

    /** @var SearchService */
    protected $search_service;

    /**
     * @param SearchService $search_service
     */
    public function __construct(SearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->search($request)
            ->map(static function (string $datum): array {
                return ['value' => $datum];
            });

        return response($data)
            ->withHeader('Cache-Control', 'public,max-age=' . static::CACHE_LIFE);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return Collection<string>
     */
    abstract protected function search(ServerRequestInterface $request): Collection;
}
