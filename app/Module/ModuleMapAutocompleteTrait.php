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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Registry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait ModuleMapAutocompleteTrait - default implementation of ModuleMapAutocompleteInterface
 */
trait ModuleMapAutocompleteTrait
{
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

        try {
            return $cache->remember($key, function () use ($place) {
                $request = $this->createPlaceNameSearchRequest($place);

                $client = new Client([
                    'timeout' => 3,
                ]);

                $response = $client->send($request);

                if ($response->getStatusCode() === StatusCodeInterface::STATUS_OK) {
                    return $this->parsePlaceNameSearchResponse($response);
                }

                return [];
            }, $ttl);
        } catch (RequestException $ex) {
            // Service down?  Quota exceeded?
            // Don't try for another hour.
            $cache->remember($key, fn () => [], 3600);

            return [];
        }
    }

    /**
     * @param string $place
     *
     * @return RequestInterface
     */
    protected function createPlaceNameSearchRequest(string $place): RequestInterface
    {
        return new Request('GET', '');
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array<string>
     */
    protected function parsePlaceNameSearchResponse(ResponseInterface $response): array
    {
        return [];
    }
}
