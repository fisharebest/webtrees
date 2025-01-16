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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\PlaceHierarchyListModule;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Services\MapDataService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_reverse;
use function e;
use function redirect;
use function route;

/**
 * Show a list of map data.
 */
class MapDataList implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private MapDataService $map_data_service;

    private ModuleService $module_service;

    private TreeService $tree_service;

    /**
     * Dependency injection.
     *
     * @param MapDataService $map_data_service
     * @param ModuleService  $module_service
     * @param TreeService    $tree_service
     */
    public function __construct(
        MapDataService $map_data_service,
        ModuleService $module_service,
        TreeService $tree_service
    ) {
        $this->map_data_service = $map_data_service;
        $this->module_service   = $module_service;
        $this->tree_service     = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id = $request->getAttribute('parent_id');

        if ($parent_id === null) {
            $parent = new PlaceLocation('');
        } else {
            $parent_id = (int) $parent_id;
            $parent = $this->map_data_service->findById($parent_id);
        }

        // Request for a non-existent location?
        if ($parent_id !== null && $parent->id() === null) {
            return redirect(route(self::class));
        }

        // Automatically import any new/missing places.
        $this->map_data_service->importMissingLocations();

        $breadcrumbs = [];

        if ($parent->id() !== null) {
            $breadcrumbs[] = e($parent->locationName());
        }

        $tmp = $parent->parent();

        while ($tmp->id() !== null) {
            $breadcrumbs[route(self::class, ['parent_id' => $tmp->id()])] = $tmp->locationName();

            $tmp = $tmp->parent();
        }

        $title = I18N::translate('Geographic data');

        $breadcrumbs[route(self::class)] = $title;

        $breadcrumbs[route(ControlPanel::class)] = I18N::translate('Control panel');

        $list_module = $this->module_service
            ->findByInterface(PlaceHierarchyListModule::class)
            ->first();

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/locations', [
            'active'       => $this->map_data_service->activePlaces($parent),
            'all_trees'    => $this->tree_service->all(),
            'breadcrumbs'  => array_reverse($breadcrumbs),
            'parent_id'    => $parent_id,
            'placelist'    => $this->map_data_service->getPlaceListLocation($parent_id),
            'list_module'  => $list_module,
            'title'        => $title,
        ]);
    }
}
