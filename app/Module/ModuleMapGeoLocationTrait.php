<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function json_decode;
use function strlen;

use const JSON_THROW_ON_ERROR;

/**
 * Trait ModuleMapGeoLocationTrait - default implementation of ModuleMapGeoLocationInterface
 */
trait ModuleMapGeoLocationTrait
{
    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Use an external service to find locations.');
    }

    /**
     * @param string $place
     *
     * @return array<string>
     */
    public function searchPlaceNames(string $place): array
    {
        if (strlen($place) <= 2) {
            return [];
        }

        $key   = $this->name() . $place;
        $cache = Registry::cache()->file();
        $ttl   = 86400;

        return $cache->remember($key, function () use ($place) {
            $request = $this->searchLocationsRequest($place);

            $client = new Client([
                'timeout' => 3,
            ]);

            $response = $client->send($request);

            if ($response->getStatusCode() === StatusCodeInterface::STATUS_OK) {
                return $this->extractLocationsFromResponse($response);
            }

            return [];
        }, $ttl);
    }

    /**
     * @param string $place
     *
     * @return RequestInterface
     */
    protected function searchLocationsRequest(string $place): RequestInterface
    {
        $uri = Html::url('https://nominatim.openstreetmap.org/search', [
            'accept-language' => I18N::languageTag(),
            'format'          => 'jsonv2',
            'limit'           => 50,
            'q'               => $place,
        ]);

        return new Request('GET', $uri);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array<string>
     */
    protected function extractLocationsFromResponse(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();

        try {
            return json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            return [];
        }
    }
}
