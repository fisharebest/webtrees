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
use Fisharebest\Webtrees\Exceptions\InternalServerErrorException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Webtrees;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use function rewind;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

/**
 * Automatic upgrades.
 */
class UpgradeService
{
    // Options for fetching files using GuzzleHTTP
    private const GUZZLE_OPTIONS = [
        'connect_timeout' => 25,
        'read_timeout'    => 25,
        'timeout'         => 55,
    ];

    // Transfer stream data in blocks of this number of bytes.
    private const READ_BLOCK_SIZE = 65535;

    // Only check the webtrees server once per day.
    private const CHECK_FOR_UPDATE_INTERVAL = 24 * 60 * 60;

    // Fetch information about upgrades from here.
    // Note: earlier versions of webtrees used svn.webtrees.net, so we must maintain both URLs.
    private const UPDATE_URL = 'https://dev.webtrees.net/build/latest-version.txt';

    // Create this file to put the site into maintenance mode.
    private const LOCK_FILE = 'data/offline.txt';

    // If the update server doesn't respond after this time, give up.
    private const HTTP_TIMEOUT = 3.0;

    /** @var TimeoutService */
    private $timeout_service;

    /**
     * UpgradeService constructor.
     *
     * @param TimeoutService $timeout_service
     */
    public function __construct(TimeoutService $timeout_service)
    {
        $this->timeout_service = $timeout_service;
    }

    /**
     * Unpack webtrees.zip.
     *
     * @param string $zip_file
     * @param string $target_folder
     */
    public function extractWebtreesZip(string $zip_file, string $target_folder)
    {
        // The Flysystem ZIP archive adapter is painfully slow, so use the native PHP library.
        $zip = new ZipArchive();

        if ($zip->open($zip_file)) {
            $zip->extractTo($target_folder);
            $zip->close();
        } else {
            throw new InternalServerErrorException('Cannot read ZIP file. Is it corrupt?');
        }
    }

    /**
     * Create a list of all the files in a webtrees .ZIP archive
     *
     * @return Collection
     */
    public function webtreesZipContents($zip_file): Collection
    {
        $zip_adapter    = new ZipArchiveAdapter($zip_file, null, 'webtrees');
        $zip_filesystem = new Filesystem(new CachedAdapter($zip_adapter, new Memory()));
        $paths          = new Collection($zip_filesystem->listContents('', true));

        return $paths->filter(function (array $path): bool {
            return $path['type'] === 'file';
        })
       ->map(function (array $path): string {
           return $path['path'];
       });
    }

    /**
     * Fetch a file from a URL and save it in a filesystem.
     * Use streams so that we can copy files larger than our available memory.
     *
     * @param string     $url
     * @param Filesystem $filesystem
     * @param string     $path
     *
     * @return int The number of bytes downloaded
     */
    public function downloadFile(string $url, Filesystem $filesystem, string $path): int
    {
        // Overwrite any previous/partial/failed download.
        if ($filesystem->has($path)) {
            $filesystem->delete($path);
        }

        // We store the data in PHP temporary storage.
        $tmp = fopen('php://temp', 'w+');

        // Read from the URL
        $client   = new Client();
        $response = $client->get($url, self::GUZZLE_OPTIONS);
        $stream   = $response->getBody();

        // Download the file to temporary storage.
        while (!$stream->eof()) {
            fwrite($tmp, $stream->read(self::READ_BLOCK_SIZE));

            if (!$this->timeout_service->isTimeNearlyUp()) {
                throw new InternalServerErrorException(I18N::translate('The server’s time limit has been reached.'));
            }
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        // Copy from temporary storage to the file.
        $bytes = ftell($stream);
        rewind($tmp);
        $filesystem->writeStream($path, $tmp);
        fclose($tmp);

        return $bytes;
    }

    /**
     * Move (copy and delete) all files from one filesystem to another.
     *
     * @param Filesystem $source
     * @param Filesystem $destination
     */
    public function moveFiles(Filesystem $source, Filesystem $destination)
    {
        foreach ($source->listContents() as $path) {
            if ($path['type'] === 'file') {
                $destination->put($path['path'], $source->read($path['path']));
                $source->delete($path['path']);

                if (!$this->timeout_service->isTimeNearlyUp()) {
                    throw new InternalServerErrorException(I18N::translate('The server’s time limit has been reached.'));
                }
            }
        }
    }

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

    public function startMaintenanceMode(): void
    {
        $message = I18N::translate('This website is being upgraded. Try again in a few minutes.');

        file_put_contents(WT_ROOT . self::LOCK_FILE, $message);
    }

    public function endMaintenanceMode(): void
    {
        if (file_exists(WT_ROOT . self::LOCK_FILE)) {
            unlink(WT_ROOT . self::LOCK_FILE);
        }
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
