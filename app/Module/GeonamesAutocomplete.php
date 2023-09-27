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
use Fisharebest\Webtrees\Site;
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
 * Class GeonamesAutocomplete - use geonames.org to search for place names
 */
class GeonamesAutocomplete extends AbstractModule implements ModuleConfigInterface, ModuleMapAutocompleteInterface
{
    use ModuleConfigTrait;
    use ModuleMapAutocompleteTrait;

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function title(): string
    {
        // I18N: https://www.geonames.org
        return I18N::translate('GeoNames');
    }

    /**
     * Name of the map provider.
     *
     * @return string
     */
    public function description(): string
    {
        $link = '<a href="https://geonames.org">geonames.org</a>';

        return I18N::translate('Search for place names using %s.', $link);
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

        // This was a global setting before it became a module setting...
        $default  = Site::getPreference('geonames');
        $username = $this->getPreference('username', $default);

        return $this->viewResponse('modules/geonames/config', [
            'username' => $username,
            'title'    => $this->title(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $username = Validator::parsedBody($request)->string('username');

        $this->setPreference('username', $username);

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
        // This was a global setting before it became a module setting...
        $default  = Site::getPreference('geonames');
        $username = $this->getPreference('username', $default);

        $uri = Html::url('https://secure.geonames.org/searchJSON', [
            'name_startsWith' => $place,
            'featureClass'    => 'P',
            'lang'            => I18N::languageTag(),
            'username'        => $username,
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

        foreach ($results->geonames as $result) {
            if (($result->countryName ?? null) === 'United Kingdom') {
                // adminName1 will be England, Scotland, etc.
                $result->countryName = null;
            }

            $parts = [
                $result->name,
                $result->adminName2 ?? null,
                $result->adminName1 ?? null,
                $result->countryName ?? null,
            ];

            $places[] = implode(Gedcom::PLACE_SEPARATOR, array_filter($parts));
        }

        usort($places, I18N::comparator());

        return $places;
    }
}
