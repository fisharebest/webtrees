<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function view;

/**
 * Autocomplete for submitters.
 */
class Select2Submitter extends AbstractSelect2Handler
{
    /** @var SearchService */
    protected $search_service;

    /**
     * Select2Submitter constructor.
     *
     * @param SearchService $search_service
     */
    public function __construct(
        SearchService $search_service
    ) {
        $this->search_service = $search_service;
    }

    /**
     * Perform the search
     *
     * @param Tree   $tree
     * @param string $query
     * @param int    $offset
     * @param int    $limit
     * @param string $at
     *
     * @return Collection<array<string,string>>
     */
    protected function search(Tree $tree, string $query, int $offset, int $limit, string $at): Collection
    {
        // Search by XREF
        $submitter = Registry::submitterFactory()->make($query, $tree);

        if ($submitter instanceof Submitter) {
            $results = new Collection([$submitter]);
        } else {
            $results = $this->search_service->searchSubmitters([$tree], [$query], $offset, $limit);
        }

        return $results->map(static function (Submitter $submitter) use ($at): array {
            return [
                'id'    => $at . $submitter->xref() . $at,
                'text'  => view('selects/submitter', ['submitter' => $submitter]),
                'title' => ' ',
            ];
        });
    }
}
