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
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Select a map provider.
 */
class MapProviderPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $data = [
            'neighbourhood' => [
                'title'       => I18N::translate('Neighborhood'),
                'description' => I18N::translate('Social communities, neighborhoods.')
            ],
            'localadmin'    => [
                'title'       => I18N::translate('Local Admin'),
                'description' => I18N::translate('Local administrative boundaries.')
            ],
            'locality'      => [
                'title'       => I18N::translate('Locality'),
                'description' => I18N::translate('Towns, hamlets, cities.')
            ],
            'county'        => [
                'title'       => I18N::translate('County'),
                'description' => I18N::translate('Official governmental area; usually bigger than a locality, almost always smaller than a region.')
            ],
            'region'        => [
                'title'       => I18N::translate('Region'),
                'description' => I18N::translate('States and provinces.')
            ],
            'country'       => [
                'title'       => I18N::translate('Country'),
                'description' => I18N::translate('Places that issue passports, nations, nation-states.')
            ]
        ];

        $select_opts  = [];
        $descriptions = [];
        foreach ($data as $k => $v) {
            $select_opts[$k]           = $v['title'];
            $descriptions[$v['title']] = $v['description'];
        }

        return $this->viewResponse('admin/map-provider', [
            'title'            => I18N::translate('Map provider'),
            'provider'         => Site::getPreference('map-provider'),
            'use_gazetteer'    => Site::getPreference('use_gazetteer'),
            'openroute_key'    => Site::getPreference('openroute_key'),
            'openroute_layers' => Site::getPreference('openroute_layers'),
            'openroute_opts'   => $select_opts,
            'openroute_desc'   => $descriptions
        ]);
    }
}
