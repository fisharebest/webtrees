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

use Exception;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_merge;
use function assert;
use function explode;
use function implode;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function rawurlencode;

use const JSON_ERROR_NONE;

/**
 * Autocomplete handler for places
 */
class AutoCompletePlace extends AbstractAutocompleteHandler
{
    // Gazetteer urls
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
    private const OPENROUTE_URL = 'https://api.openrouteservice.org/geocode/autocomplete';

    // Options for fetching files using GuzzleHTTP
    private const GUZZLE_OPTIONS = [
        'connect_timeout' => 3,
        'read_timeout'    => 3,
        'timeout'         => 3,
    ];

    protected function search(ServerRequestInterface $request): Collection
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $query = $request->getAttribute('query');

        $data = $this->search_service
            ->searchPlaces($tree, $query, 0, static::LIMIT)
            ->map(static function (Place $place): string {
                return $place->gedcomName();
            });

        if ((bool) Site::getPreference('use_gazetteer')) {
            $data = $data->concat($this->searchGazetteer($query))->unique();
            $data = $data->slice(0, static::LIMIT);
        }

        return new Collection($data);
    }

    /**
     *
     * @param string $query
     *
     * @return Collection<string>
     */
    private function searchGazetteer($query): collection
    {
        $key          = Site::getPreference('openroute_key');
        $data         = new Collection();
        $user_service = new UserService();

        if ($key === '') {
            $url = self::NOMINATIM_URL;
            $qry = [
                'q'               => rawurlencode($query),
                'format'          => 'jsonv2',
                'limit'           => static::LIMIT,
                'accept-language' => I18N::languageTag(),
                'featuretype'     => 'settlement',
                'email'           => rawurlencode($user_service->administrators()->first()->email()),
            ];
        } else {
            $url = self::OPENROUTE_URL;
            $qry = [
                'api_key' => $key,
                'text'    => rawurlencode($query),
                'layers'  => 'coarse',
                'size'    => static::LIMIT,
            ];
        }

        // Read from the URL
        $client = new Client();
        try {
            $json     = $client->get($url, array_merge(self::GUZZLE_OPTIONS, ['query' => $qry]))->getBody()->__toString();
            $results  = json_decode($json, false);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(I18N::translate('Geocoder: %s', json_last_error_msg()));
            }
            if ($key === '') {
                // Use Nominatim
                foreach ($results as $result) {
                    $data->add($result->display_name);
                }
            } else {
                // Use Openroutesearch
                $place_elements = array_filter(explode(',', Site::getPreference('openroute_layers')));
                foreach ($results->features as $result) {
                    $place_parts = [];
                    foreach ($place_elements as $place_element) {
                        if (isset($result->properties->{$place_element})) {
                            $place_parts[] = $result->properties->{$place_element};
                        }
                    }
                    // If no elements are selected in the control panel
                    // or none of the selected elements are present in the place
                    // then default to the label which is always present (I think)
                    $data->add(implode(Gedcom::PLACE_SEPARATOR, $place_parts) ?: $result->properties->label);
                }
            }
        } catch (Exception | RequestException $ex) {
            // Json error? Service down?  Quota exceeded?
            $data->add($ex->getMessage());
        }

        return $data;
    }
}
