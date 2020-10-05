<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function is_array;
use function json_decode;
use function rawurlencode;

/**
 * Autocomplete handler for places
 */
class AutoCompletePlace extends AbstractAutocompleteHandler
{
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

        $geonames = Site::getPreference('geonames');

        if ($data->isEmpty() && $geonames !== '') {
            // No place found? Use an external gazetteer
            $url =
                'https://secure.geonames.org/searchJSON' .
                '?name_startsWith=' . rawurlencode($query) .
                '&lang=' . I18N::languageTag() .
                '&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC' .
                '&style=full' .
                '&username=' . rawurlencode($geonames);

            // Read from the URL
            $client = new Client();
            try {
                $json   = $client->get($url, self::GUZZLE_OPTIONS)->getBody()->__toString();
                $places = json_decode($json, true);
                if (isset($places['geonames']) && is_array($places['geonames'])) {
                    foreach ($places['geonames'] as $k => $place) {
                        $data->add($place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName']);
                    }
                }
            } catch (RequestException $ex) {
                // Service down?  Quota exceeded?
            }
        }

        return new Collection($data);
    }
}
