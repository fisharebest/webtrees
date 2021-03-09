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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\MapProviderService;
use Fisharebest\Webtrees\Site;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Manage map providers.
 */
class MapProviderPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var MapProviderService */
    private $map_provider_service;

    /**
     * Dependency injection.
     *
     * @param MapProviderService $map_provider_service
     */
    public function __construct(MapProviderService $map_provider_service)
    {
        $this->map_provider_service = $map_provider_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout  = 'layouts/administration';
        $default       = $this->map_provider_service->defaultProvider();
        $provider_data = [];

        $help = DB::table('map_parameters as p1')
            ->join('map_names as n1', 'p1.parent_id', '=', 'n1.id')
            ->whereNull('n1.provider_id')
            ->where('p1.parameter_name', '=', 'help')
            ->pluck('p1.parameter_value', 'n1.key_name')
            ->map(function ($item) {
                return unserialize($item ?? '');
            });

        foreach ($this->map_provider_service->providers('added') as $key => $name) {
            $provider_data[] = (object) [
                'title'      => $name,
                'key'        => $key,
                'parameters' => $this->map_provider_service->userParameters($key),
                'enabled'    => (int) Site::getPreference($key . '-enabled'),
                'help_url'   => $help->get($key),
            ];
        }

        return $this->viewResponse('admin/map-providers', [
            'title'    => I18N::translate('Map Providers'),
            'data'     => (object) [
                'current_default'  => $default,
                'system_default'   => $this->map_provider_service->systemDefault(),
                'providers'        => $this->map_provider_service->providers(),
                'styles'           => $this->map_provider_service->styles($default->get('provider')),
                'provider_data'    => $provider_data,
            ],
        ]);
    }
}
