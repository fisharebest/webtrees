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
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function implode;
use function json_decode;
use function redirect;
use function usort;

use const JSON_THROW_ON_ERROR;

/**
 * Class OpenRouteServiceAutocomplete - use openrouteservice.org to search for place names
 */
class OpenRouteServiceAutocomplete extends AbstractModule implements ModuleConfigInterface, ModuleMapAutocompleteInterface
{
    use ModuleConfigTrait;
    use ModuleMapAutocompleteTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function description(): string
    {
        $link = '<a href="https://openrouteservice.org">openrouteservice.org</a>';

        return I18N::translate('Search for place names using %s.', $link);
    }

    /**
     * @return ResponseInterface
     */
    public function getAdminAction(): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $api_key = $this->getPreference('api_key');

        return $this->viewResponse('modules/openrouteservice/config', [
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
        return I18N::translate('OpenRouteService');
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
     * @param string $place
     *
     * @return RequestInterface
     */
    protected function createPlaceNameSearchRequest(string $place): RequestInterface
    {
        $api_key = $this->getPreference('api_key');

        $uri = Html::url('https://api.openrouteservice.org/geocode/autocomplete', [
            'api_key' => $api_key,
            'text'    => $place,
            'layers'  => 'coarse',
        ]);

        return new Request('GET', $uri);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array<string>
     */
    protected function parsePlaceNameSearchResponse(ResponseInterface $response): array
    {
        $body    = $response->getBody()->getContents();
        $places  = [];
        $results = json_decode($body, false, 512, JSON_THROW_ON_ERROR);

        foreach ($results->features as $result) {
            $result->properties->name ??= null;
            $result->properties->county ??= null;
            $result->properties->region ??= null;
            $result->properties->macroregion ??= null;
            $result->properties->country ??= null;

            if ($result->properties->country === 'United Kingdom') {
                // macroregion will contain England, Scotland, etc.
                $result->properties->country = null;
                // region will contain the county.
                $result->properties->region = null;
            }

            $parts = [
                $result->properties->name,
                $result->properties->county,
                $result->properties->region,
                $result->properties->macroregion,
                $result->properties->country,
            ];

            $places[] = implode(Gedcom::PLACE_SEPARATOR, array_filter($parts)) ?: $result->properties->label;
        }

        usort($places, I18N::comparator());

        return $places;
    }
}
