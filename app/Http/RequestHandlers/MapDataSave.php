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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function round;
use function route;

/**
 * Controller for maintaining geographic data.
 */
class MapDataSave implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        $place_id  = $params['place_id'] ?? '';
        $parent_id = $params['parent_id'] ?? null;
        $latitude  = $params['new_place_lati'] ?? '';
        $longitude = $params['new_place_long'] ?? '';
        $name      = mb_substr($params['new_place_name'] ?? '', 0, 120);

        if ($latitude === '' || $longitude === '') {
            $latitude  = null;
            $longitude = null;
        } else {
            // 5 decimal places (locate to within about 1 metre)
            $latitude  = round((float) $latitude, 5);
            $longitude = round((float) $longitude, 5);

            // 0,0 is only allowed at the top level
            if ($parent_id !== null && $latitude === 0.0 && $longitude === 0.0) {
                $latitude  = null;
                $longitude = null;
            }
        }

        if ($place_id === '') {
            DB::table('place_location')->insert([
                'parent_id' => $parent_id,
                'place'     => $name,
                'latitude'  => $latitude,
                'longitude' => $longitude,
            ]);
        } else {
            DB::table('place_location')
                ->where('id', '=', $place_id)
                ->update([
                    'place'     => $name,
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                ]);
        }

        $message = I18N::translate('The details for “%s” have been updated.', e($name));
        FlashMessages::addMessage($message, 'success');

        $url = route(MapDataList::class, ['parent_id' => $parent_id]);

        return redirect($url);
    }
}
