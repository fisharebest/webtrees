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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function redirect;
use function round;

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
        $parent_id = Validator::parsedBody($request)->string('parent_id');
        $place_id  = Validator::parsedBody($request)->string('place_id');
        $latitude  = Validator::parsedBody($request)->string('new_place_lati');
        $longitude = Validator::parsedBody($request)->string('new_place_long');
        $name      = Validator::parsedBody($request)->string('new_place_name');
        $url       = Validator::parsedBody($request)->isLocalUrl()->string('url');

        $name      = mb_substr($name, 0, 120);
        $place_id  = $place_id === '' ? null : $place_id;
        $parent_id = $parent_id === '' ? null : $parent_id;

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

        if ($place_id === null) {
            $exists_query = DB::table('place_location')->where('place', '=', $name);

            if ($parent_id === null) {
                $exists_query->whereNull('parent_id');
            } else {
                $exists_query->where('parent_id', '=', $parent_id);
            }

            if (!$exists_query->exists()) {
                DB::table('place_location')->insert([
                    'parent_id' => $parent_id,
                    'place'     => $name,
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                ]);

                $message = I18N::translate('The location has been created', e($name));
                FlashMessages::addMessage($message, 'success');
            }
        } else {
            DB::table('place_location')
                ->where('id', '=', $place_id)
                ->update([
                    'place'     => $name,
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                ]);

            $message = I18N::translate('The details for “%s” have been updated.', e($name));
            FlashMessages::addMessage($message, 'success');
        }

        return redirect($url);
    }
}
