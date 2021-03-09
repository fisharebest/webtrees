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

use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MapProviderService;
use Fisharebest\Webtrees\Site;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function explode;
use function redirect;
use function route;
use function serialize;
use function substr;

/**
 * Select a map provider.
 */
class MapProviderAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $settings = (array) $request->getParsedBody();

        Registry::cache()->array()->forget('map-layers');
        Registry::cache()->array()->forget('default-map-layers');

        if ($settings['provider'] !== '' && $settings['style'] !== '') {
            Site::setPreference('default-map-provider', $settings['provider'] . '.' . $settings['style']);
        } else {
            Site::setPreference('default-map-provider', MapProviderService::SYSTEM_DEFAULT);
        }

        unset($settings['provider']);
        unset($settings['style']);

        $user_parameters = [];
        foreach ($settings as $key => $value) {
            list($type, $provider_key, $name) = explode('-', $key);
            if ($type === 'user') {
                $user_parameters[$provider_key][] = [
                    'parameter_name'  => $name,
                    'parameter_value' => $value,
                ];
            } else {
                Site::setPreference(substr($provider_key . '-' . $name, 0, 32), $value); // database limit is 32
            }
        }

        foreach ($user_parameters as $key => $user_parameters) {
            foreach ($user_parameters as $parm) {
                DB::table('map_parameters as p1')
                    ->join('map_names as n1', 'p1.parent_id', '=', 'n1.id')
                    ->where('n1.key_name', '=', $key)
                    ->where('p1.parameter_name', '=', $parm['parameter_name'])
                    ->update(['p1.parameter_value' => serialize($parm['parameter_value'])]);
            }
        }

        return redirect(route(MapProviderPage::class));
    }
}
