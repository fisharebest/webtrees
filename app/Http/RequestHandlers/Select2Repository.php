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

use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function view;

/**
 * Autocomplete for repositories.
 */
class Select2Repository extends AbstractSelect2Handler
{
    /**
     * Perform the search
     *
     * @param Tree   $tree
     * @param string $query
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection<array<string,string>>
     */
    protected function search(Tree $tree, string $query, int $offset, int $limit): Collection
    {
        // Search by XREF
        $repository = Repository::getInstance($query, $tree);

        if ($repository instanceof Repository) {
            $results = new Collection([$repository]);
        } else {
            $results = $this->search_service->searchRepositories([$tree], [$query], $offset, $limit);
        }

        return $results->map(static function (Repository $repository): array {
            return [
                'id'    => $repository->xref(),
                'text'  => view('selects/repository', ['repository' => $repository]),
                'title' => ' ',
            ];
        });
    }
}
