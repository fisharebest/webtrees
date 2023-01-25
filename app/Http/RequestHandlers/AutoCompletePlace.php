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

use Fisharebest\Webtrees\Module\ModuleMapAutocompleteInterface;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Autocomplete handler for places
 */
class AutoCompletePlace extends AbstractAutocompleteHandler
{
    private ModuleService $module_service;

    /**
     * @param ModuleService $module_service
     * @param SearchService $search_service
     */
    public function __construct(ModuleService $module_service, SearchService $search_service)
    {
        parent::__construct($search_service);

        $this->module_service = $module_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return Collection<int,string>
     */
    protected function search(ServerRequestInterface $request): Collection
    {
        $tree  = Validator::attributes($request)->tree();
        $query = Validator::queryParams($request)->string('query');

        $data = $this->search_service
            ->searchPlaces($tree, $query, 0, static::LIMIT)
            ->map(static function (Place $place): string {
                return $place->gedcomName();
            });

        // No place found? Use external gazetteers.
        foreach ($this->module_service->findByInterface(ModuleMapAutocompleteInterface::class) as $module) {
            if ($data->isEmpty()) {
                $data = $data->concat($module->searchPlaceNames($query))->sort();
            }
        }

        return $data;
    }
}
