<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;

/**
 * Class GoogleMaps - use maps within webtrees
 */
class GoogleMaps extends AbstractModule implements ModuleConfigInterface, ModuleMapProviderInterface
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
        $link = '<a href="https://www.google.com/maps" dir="ltr">www.google.com/maps</a>';

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

        return $this->viewResponse('modules/google-maps/config', [
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
        return I18N::translate('Google™ maps');
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
                'GM_API_KEY'  => $api_key,
                'attribution' => 'Map data &copy2021 Google LLC <a href="https://www.google.com/intl/en-GB_US/help/terms_maps">Terms of use</a>',
                'default'     => true,
                'lyrs'        => 'm',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['mt0', 'mt1', 'mt2', 'mt3'],
                'label'       => 'Streets',
                'url'         => 'https://{s}.google.com/vt/lyrs={lyrs}&x={x}&y={y}&z={z}',
            ],
            (object) [
                'GM_API_KEY'  => $api_key,
                'attribution' => 'Map data &copy2021 Google LLC <a href="https://www.google.com/intl/en-GB_US/help/terms_maps">Terms of use</a>',
                'default'     => false,
                'lyrs'        => 'y',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['mt0', 'mt1', 'mt2', 'mt3'],
                'label'       => 'Hybrid',
                'url'         => 'https://{s}.google.com/vt/lyrs={lyrs}&x={x}&y={y}&z={z}',
            ],
            (object) [
                'GM_API_KEY'  => $api_key,
                'attribution' => 'Map data &copy2021 Google LLC <a href="https://www.google.com/intl/en-GB_US/help/terms_maps">Terms of use</a>',
                'default'     => false,
                'lyrs'        => 's',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['mt0', 'mt1', 'mt2', 'mt3'],
                'label'       => 'Satellite',
                'url'         => 'https://{s}.google.com/vt/lyrs={lyrs}&x={x}&y={y}&z={z}',
            ],
            (object) [
                'GM_API_KEY'  => $api_key,
                'attribution' => 'Map data &copy2021 Google LLC <a href="https://www.google.com/intl/en-GB_US/help/terms_maps">Terms of use</a>',
                'default'     => false,
                'lyrs'        => 'p',
                'maxZoom'     => 20,
                'minZoom'     => 2,
                'subdomains'  => ['mt0', 'mt1', 'mt2', 'mt3'],
                'label'       => 'Terrain',
                'url'         => 'https://{s}.google.com/vt/lyrs={lyrs}&x={x}&y={y}&z={z}',
            ],
        ];
    }
}
