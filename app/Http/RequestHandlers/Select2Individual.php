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

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function view;

/**
 * Autocomplete for individuals.
 */
class Select2Individual extends AbstractSelect2Handler
{
    /**
     * Perform the search
     *
     * @param Tree   $tree
     * @param string $query
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection
     */
    protected function search(Tree $tree, string $query, int $offset, int $limit): Collection
    {
        // Allow private records to be selected by XREF?
        $filter = $tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1' ? null : Individual::accessFilter();

        return $this->search_service
            ->searchIndividualNames([$tree], [$query], $offset, $limit)
            ->prepend(Individual::getInstance($query, $tree))
            ->filter($filter)
            ->values()
            ->map(static function (Individual $individual): array {
                return [
                    'id'    => $individual->xref(),
                    'text'  => view('selects/individual', ['individual' => $individual]),
                    'title' => ' ',
                ];
            });
    }
}
