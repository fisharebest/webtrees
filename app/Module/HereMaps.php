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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;

/**
 * Class HereMaps - use maps within webtrees
 */
class HereMaps extends AbstractModule implements ModuleConfigInterface, ModuleMapProviderInterface
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
        $link = '<a href="https://www.here.com" dir="ltr">www.here.com</a>';

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

        return $this->viewResponse('modules/here-maps/config', [
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
        return /* I18N: https://wego.here.com */ I18N::translate('Here maps');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        $this->setPreference('api_key', $params['api_key'] ?? '');

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
                'apiKey'      => $api_key,
                'attribution' => '<a href="https://legal.here.com/en/terms/serviceterms/us">Terms of use</a> ©1987-2021 HERE',
                'base'        => 'base',
                'format'      => 'png8',
                'label'       => 'Normal',
                'mapID'       => 'newest',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'size'        => 512,
                'subdomains'  => ['1', '2', '3', '4'],
                'type'        => 'maptile',
                'url'         => 'https://{s}.{base}.maps.ls.hereapi.com/maptile/2.1/{type}/{mapID}/{variant}/{z}/{x}/{y}/{size}/{format}?apiKey={apiKey}',
                'variant'     => 'normal.day',
            ],
            (object) [
                'apiKey'      => $api_key,
                'attribution' => '<a href="https://legal.here.com/en/terms/serviceterms/us">Terms of use</a> ©1987-2021 HERE',
                'base'        => 'base',
                'format'      => 'png8',
                'label'       => 'Grey',
                'mapID'       => 'newest',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'size'        => 512,
                'subdomains'  => ['1', '2', '3', '4'],
                'type'        => 'maptile',
                'url'         => 'https://{s}.{base}.maps.ls.hereapi.com/maptile/2.1/{type}/{mapID}/{variant}/{z}/{x}/{y}/{size}/{format}?apiKey={apiKey}',
                'variant'     => 'normal.day.grey',
            ],
            (object) [
                'apiKey'      => $api_key,
                'attribution' => '<a href="https://legal.here.com/en/terms/serviceterms/us">Terms of use</a> ©1987-2021 HERE',
                'base'        => 'aerial',
                'format'      => 'png8',
                'label'       => 'Terrain',
                'mapID'       => 'newest',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'size'        => 512,
                'subdomains'  => ['1', '2', '3', '4'],
                'type'        => 'maptile',
                'url'         => 'https://{s}.{base}.maps.ls.hereapi.com/maptile/2.1/{type}/{mapID}/{variant}/{z}/{x}/{y}/{size}/{format}?apiKey={apiKey}',
                'variant'     => 'terrain.day',
            ],
        ];
    }
}
