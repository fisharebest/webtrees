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

use function redirect;

/**
 * Class BingMaps - use maps within webtrees
 */
class BingMaps extends AbstractModule implements ModuleConfigInterface, ModuleMapProviderInterface
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
        $link = '<a href="https://www.bing.com/maps" dir="ltr">www.bing.com/maps</a>';

        // I18N: %s is a link/URL
        return I18N::translate('Create maps using %s.', $link);
    }

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

        return $this->viewResponse('modules/bing-maps/config', [
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
        return I18N::translate('Bing™ maps');
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

        if ($api_key === '') {
            $message = I18N::translate('This service requires an API key.');

            if (Auth::isAdmin()) {
                $message = '<a href="' . e($this->getConfigLink()) . '">' . $message . '</a>';
            }

            throw new HttpServerErrorException($message);
        }

        return [
            (object) [
                'bingMapsKey' => $api_key,
                'default'     => false,
                'imagerySet'  => 'Aerial',
                'label'       => 'Aerial',
                'maxZoom'     => 15,
                'minZoom'     => 2,
            ],
            (object) [
                'bingMapsKey' => $api_key,
                'default'     => false,
                'imagerySet'  => 'CanvasDark',
                'label'       => 'Dark',
                'maxZoom'     => 15,
                'minZoom'     => 2,
            ],
            (object) [
                'bingMapsKey' => $api_key,
                'default'     => false,
                'imagerySet'  => 'CanvasGray',
                'label'       => 'Grey',
                'maxZoom'     => 15,
                'minZoom'     => 2,
            ],
            (object) [
                'bingMapsKey' => $api_key,
                'default'     => true,
                'imagerySet'  => 'CanvasLight',
                'label'       => 'Light',
                'maxZoom'     => 15,
                'minZoom'     => 2,
            ],
            (object) [
                'bingMapsKey' => $api_key,
                'default'     => false,
                'imagerySet'  => 'Road',
                'label'       => 'Road',
                'maxZoom'     => 15,
                'minZoom'     => 2,
            ],
        ];
    }
}
