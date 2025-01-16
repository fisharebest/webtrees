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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;
use function redirect;

/**
 * Class MapBox - use maps within webtrees
 */
class MapBox extends AbstractModule implements ModuleConfigInterface, ModuleMapProviderInterface
{
    use ModuleConfigTrait;
    use ModuleMapProviderTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function description(): string
    {
        $link = '<a href="https://www.mapbox.com" dir="ltr">www.mapbox.com</a>';

        // I18N: %s is a link/URL
        return I18N::translate('Create maps using %s.', $link);
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return ResponseInterface
     */
    public function getAdminAction(): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $api_key = $this->getPreference('api_key');

        if ($api_key === '') {
            $message = I18N::translate('This service requires an API key.');

            if (Auth::isAdmin()) {
                $message = '<a href="' . e($this->getConfigLink()) . '">' . $message . '</a>';
            }

            throw new HttpServerErrorException($message);
        }

        return $this->viewResponse('modules/map-box/config', [
            'api_key' => $api_key,
            'title'   => $this->title(),
        ]);
    }

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function title(): string
    {
        return /* I18N: mapbox.com */ I18N::translate('Mapbox');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $api_key = Validator::parsedBody($request)->string('api_key');

        $this->setPreference('api_key', $api_key);

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->title()), 'success');

        return redirect($this->getConfigLink());
    }

    /**
     * Parameters to create a TileLayer in LeafletJs.
     *
     * @return array<object>
     */
    public function leafletJsTileLayers(): array
    {
        $api_key = $this->getPreference('api_key');

        return [
            (object) [
                'accessToken' => $api_key,
                'attribution' => '©<a href="https://www.mapbox.com/about/maps">Mapbox</a> ©<a href="https://www.openstreetmap.org/copyrightt">OpenStreetMap</a> <strong><a href="https://www.mapbox.com/map-feedback">Improve this map</a></strong>',
                'default'     => false,
                'id'          => 'dark-v10',
                'label'       => 'Dark',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['a', 'b', 'c', 'd'],
                'tileSize'    => 512,
                'url'         => 'https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
                'zoomOffset'  => -1,
            ],
            (object) [
                'accessToken' => $api_key,
                'attribution' => '©<a href="https://www.mapbox.com/about/maps">Mapbox</a> ©<a href="https://www.openstreetmap.org/copyrightt">OpenStreetMap</a> <strong><a href="https://www.mapbox.com/map-feedback">Improve this map</a></strong>',
                'default'     => true,
                'id'          => 'light-v10',
                'label'       => 'Light',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['a', 'b', 'c', 'd'],
                'tileSize'    => 512,
                'url'         => 'https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
                'zoomOffset'  => -1,
            ],
            (object) [
                'accessToken' => $api_key,
                'attribution' => '©<a href="https://www.mapbox.com/about/maps">Mapbox</a> ©<a href="https://www.openstreetmap.org/copyrightt">OpenStreetMap</a> <strong><a href="https://www.mapbox.com/map-feedback">Improve this map</a></strong>',
                'default'     => false,
                'id'          => 'outdoors-v11',
                'label'       => 'Outdoors',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['a', 'b', 'c', 'd'],
                'tileSize'    => 512,
                'url'         => 'https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
                'zoomOffset'  => -1,
            ],
            (object) [
                'accessToken' => $api_key,
                'attribution' => '©<a href="https://www.mapbox.com/about/maps">Mapbox</a> ©<a href="https://www.openstreetmap.org/copyrightt">OpenStreetMap</a> <strong><a href="https://www.mapbox.com/map-feedback">Improve this map</a></strong>',
                'default'     => false,
                'id'          => 'satellite-v9',
                'label'       => 'Satellite',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['a', 'b', 'c', 'd'],
                'tileSize'    => 512,
                'url'         => 'https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
                'zoomOffset'  => -1,
            ],
            (object) [
                'accessToken' => $api_key,
                'attribution' => '©<a href="https://www.mapbox.com/about/maps">Mapbox</a> ©<a href="https://www.openstreetmap.org/copyrightt">OpenStreetMap</a> <strong><a href="https://www.mapbox.com/map-feedback">Improve this map</a></strong>',
                'default'     => false,
                'id'          => 'streets-v11',
                'label'       => 'Streets',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['a', 'b', 'c', 'd'],
                'tileSize'    => 512,
                'url'         => 'https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}',
                'zoomOffset'  => -1,
            ],
        ];
    }
}
