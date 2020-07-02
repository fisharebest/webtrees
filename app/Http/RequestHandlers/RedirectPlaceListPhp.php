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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Redirect URLs created by webtrees 1.x (and PhpGedView).
 */
class RedirectPlaceListPhp implements RequestHandlerInterface
{
    /** @var TreeService */
    private $tree_service;

    /**
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query   = $request->getQueryParams();
        $ged     = $query['ged'] ?? Site::getPreference('DEFAULT_GEDCOM');
        $parent  = $query['parent'] ?? [];
        $display = $query['display'] ?? null;

        $tree = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            // Check the place exists in the database, to avoid creating new places.
            $place_id = 0;

            foreach ($parent as $place_name) {
                $place_id = (int) DB::table('places')
                    ->where('p_file', '=', $tree->id())
                    ->where('p_place', '=', $place_name)
                    ->where('p_parent_id', '=', $place_id)
                    ->value('p_id');
            }

            $url = route('module', [
                'module'   => 'places_list',
                'action'   => 'List',
                'action2'  => $display,
                'place_id' => $place_id,
                'tree'     => $tree->name(),
            ]);

            return redirect($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        throw new HttpNotFoundException();
    }
}
