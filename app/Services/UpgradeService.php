<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Carbon\Carbon;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Webtrees;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Automatic upgrades.
 */
class UpgradeService
{
    // Only check the webtrees server infrequently.
    private const CHECK_FOR_UPDATE_INTERVAL = 24 * 60 * 60;

    // Fetch information about upgrades from here.
    // Note: earlier versions of webtrees used svn.webtrees.net, so we must maintain both URLs.
    private const UPDATE_URL = 'https://dev.webtrees.net/build/latest-version.txt';

    // If the update server doesn't respond after this time, give up.
    private const HTTP_TIMEOUT = 3.0;

    /**
     * @return bool
     */
    public function isUpgradeAvailable(): bool
    {
        // If the latest version is unavailable, we will have an empty sting which equates to version 0.

        return version_compare(Webtrees::VERSION, $this->fetchLatestVersion()) < 0;
    }

    /**
     * What is the latest version of webtrees.
     *
     * @return string
     */
    public function latestVersion(): string
    {
        $latest_version = $this->fetchLatestVersion();

        [$version] = explode('|', $latest_version);

        return $version;
    }

    /**
     * Where can we download the latest version of webtrees.
     *
     * @return string
     */
    public function downloadUrl(): string
    {
        $latest_version = $this->fetchLatestVersion();

        [, , $url] = explode('|', $latest_version . '||');

        return $url;
    }

    /**
     * Check with the webtrees.net server for the latest version of webtrees.
     * Fetching the remote file can be slow, so check infrequently, and cache the result.
     * Pass the current versions of webtrees, PHP and MySQL, as the response
     * may be different for each. The server logs are used to generate
     * installation statistics which can be found at http://dev.webtrees.net/statistics.html
     *
     * @return string
     */
    private function fetchLatestVersion(): string
    {
        $last_update_timestamp = (int) Site::getPreference('LATEST_WT_VERSION_TIMESTAMP');

        $current_timestamp = Carbon::now()->timestamp;

        if ($last_update_timestamp < $current_timestamp - self::CHECK_FOR_UPDATE_INTERVAL) {
            try {
                $client = new Client([
                    'timeout' => self::HTTP_TIMEOUT,
                ]);

                $response = $client->get(self::UPDATE_URL, [
                    'query' => $this->serverParameters(),
                ]);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    Site::setPreference('LATEST_WT_VERSION', $response->getBody()->getContents());
                    Site::setPreference('LATEST_WT_VERSION_TIMESTAMP', (string) $current_timestamp);
                }
            } catch (RequestException $ex) {
                // Can't connect to the server?
                // Use the existing information about latest versions.
            }
        }

        return Site::getPreference('LATEST_WT_VERSION');
    }

    /**
     * The upgrade server needs to know a little about this server.
     */
    private function serverParameters(): array
    {
        $operating_system = DIRECTORY_SEPARATOR === '/' ? 'u' : 'w';

        return [
            'w' => Webtrees::VERSION,
            'p' => PHP_VERSION,
            'o' => $operating_system,
        ];
    }
}
