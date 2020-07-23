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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MapDataService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function array_reverse;
use function redirect;
use function route;

/**
 * Show a list of map data.
 */
class MapDataList implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var MapDataService */
    private $map_data_service;

    /**
     * Dependency injection.
     *
     * @param MapDataService $map_data_service
     */
    public function __construct(MapDataService $map_data_service)
    {
        $this->map_data_service = $map_data_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parent_id   = (int) ($request->getQueryParams()['parent_id'] ?? 0);
        $title       = I18N::translate('Geographic data');
        $parent      = $this->map_data_service->findById($parent_id);

        // Request for a non-existent location?
        if ($parent_id !== $parent->id()) {
            return redirect(route(__CLASS__));
        }

        // Automatically import any new/missing places.
        $this->map_data_service->importMissingChildren($parent);

        $breadcrumbs = [$parent->locationName()];

        $tmp = $parent->parent();

        while ($tmp->id() !== 0) {
            $breadcrumbs[route(__CLASS__, ['parent_id' => $tmp->id()])] = $tmp->locationName();

            $tmp = $tmp->parent();
        }

        $breadcrumbs[route(__CLASS__)]           = $title;
        $breadcrumbs[route(ControlPanel::class)] = I18N::translate('Control panel');

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/locations', [
            'title'       => $title,
            'active'      => $this->map_data_service->activePlaces($parent),
            'breadcrumbs' => array_reverse($breadcrumbs),
            'parent_id'   => $parent_id,
            'placelist'   => $this->getPlaceListLocation($parent_id),
        ]);
    }


    /**
     * Find all of the places in the hierarchy
     *
     * @param int $id
     *
     * @return stdClass[]
     */
    private function getPlaceListLocation(int $id): array
    {
        return DB::table('placelocation')
            ->where('pl_parent_id', '=', $id)
            ->orderBy(new Expression('pl_place /*! COLLATE ' . I18N::collation() . ' */'))
            ->get()
            ->map(function (stdClass $row): stdClass {
                // Find/count places without co-ordinates
                $children = $this->childLocationStatus((int) $row->pl_id);

                $row->child_count = (int) $children->child_count;
                $row->no_coord    = (int) $children->no_coord;

                return $row;
            })
            ->all();
    }

    /**
     * How many children does place have?  How many have co-ordinates?
     *
     * @param int $parent_id
     *
     * @return stdClass
     */
    private function childLocationStatus(int $parent_id): stdClass
    {
        $prefix = DB::connection()->getTablePrefix();

        $expression =
            $prefix . 'p0.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p0.pl_lati, '') = '' OR " .
            $prefix . 'p1.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p1.pl_lati, '') = '' OR " .
            $prefix . 'p2.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p2.pl_lati, '') = '' OR " .
            $prefix . 'p3.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p3.pl_lati, '') = '' OR " .
            $prefix . 'p4.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p4.pl_lati, '') = '' OR " .
            $prefix . 'p5.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p5.pl_lati, '') = '' OR " .
            $prefix . 'p6.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p6.pl_lati, '') = '' OR " .
            $prefix . 'p7.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p7.pl_lati, '') = '' OR " .
            $prefix . 'p8.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p8.pl_lati, '') = '' OR " .
            $prefix . 'p9.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p9.pl_lati, '') = ''";

        return DB::table('placelocation AS p0')
            ->leftJoin('placelocation AS p1', 'p1.pl_parent_id', '=', 'p0.pl_id')
            ->leftJoin('placelocation AS p2', 'p2.pl_parent_id', '=', 'p1.pl_id')
            ->leftJoin('placelocation AS p3', 'p3.pl_parent_id', '=', 'p2.pl_id')
            ->leftJoin('placelocation AS p4', 'p4.pl_parent_id', '=', 'p3.pl_id')
            ->leftJoin('placelocation AS p5', 'p5.pl_parent_id', '=', 'p4.pl_id')
            ->leftJoin('placelocation AS p6', 'p6.pl_parent_id', '=', 'p5.pl_id')
            ->leftJoin('placelocation AS p7', 'p7.pl_parent_id', '=', 'p6.pl_id')
            ->leftJoin('placelocation AS p8', 'p8.pl_parent_id', '=', 'p7.pl_id')
            ->leftJoin('placelocation AS p9', 'p9.pl_parent_id', '=', 'p8.pl_id')
            ->where('p0.pl_parent_id', '=', $parent_id)
            ->select([new Expression('COUNT(*) AS child_count'), new Expression('SUM(' . $expression . ') AS no_coord')])
            ->first();
    }
}
