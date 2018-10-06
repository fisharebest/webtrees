<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Automatic upgrades.
 */
class UpgradeService
{
    // Regular expression to match a version string such as "1.7.10" or "2.0.0-alpha.1"
    const REGEX_VERSION = '\d+\.\d+\.\d+(-[a-z0-9.-]+)?';

    // Only check the webtrees server infrequently.
    const CHECK_FOR_UPDATE_INTERVAL = 24 * 60 * 60;

    // Fetch information about upgrades from here.
    // Note: earlier versions of webtrees used svn.webtrees.net, so we must maintain both URLs.
    const UPDATE_URL = 'https://dev.webtrees.net/build/latest-version.txt';

    // If the update server doesn't respond after this time, give up.
    const HTTP_TIMEOUT = 3.0;

    /**
     * @return bool
     */
    public function isUpgradeAvailable(): bool
    {
        // If the latest version is unavailable, we will have an empty sting which equates to version 0.

        return version_compare(WT_VERSION, $this->fetchLatestVersion()) < 0;
    }

    /**
     * What is the latest version of webtrees.
     *
     * @return string
     */
    public function latestVersion(): string
    {
        $latest_version = $this->fetchLatestVersion();

        list($version) = explode('|', $latest_version);

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

        list(, , $url) = explode('|', $latest_version . '||');

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

        if ($last_update_timestamp < WT_TIMESTAMP - self::CHECK_FOR_UPDATE_INTERVAL) {
            try {
                $client = new Client([
                    'timeout' => self::HTTP_TIMEOUT,
                ]);

                $response = $client->get(self::UPDATE_URL, [
                    'query' => $this->serverParameters(),
                ]);

                if ($response->getStatusCode() === Response::HTTP_OK) {
                    Site::setPreference('LATEST_WT_VERSION', $response->getBody()->getContents());
                    Site::setPreference('LATEST_WT_VERSION_TIMESTAMP', (string) WT_TIMESTAMP);
                }
            } catch (RequestException $ex) {
                DebugBar::addThrowable($ex);
            }
        }

        return Site::getPreference('LATEST_WT_VERSION');
    }

    /**
     * The upgrade server needs to know a little about this server.
     */
    private function serverParameters(): array
    {
        $mysql_version = Database::prepare("SHOW VARIABLES LIKE 'version'")->fetchOneRow();

        $operating_system = DIRECTORY_SEPARATOR === '/' ? 'u' : 'w';

        return [
            'w' => WT_VERSION,
            'p' => PHP_VERSION,
            'm' => $mysql_version->value,
            'o' => $operating_system,
        ];
    }
}
