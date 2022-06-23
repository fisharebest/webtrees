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

namespace Fisharebest\Webtrees\Services;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Webtrees;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use ZipArchive;

use function explode;
use function fclose;
use function file_exists;
use function file_put_contents;
use function fopen;
use function ftell;
use function fwrite;
use function rewind;
use function strlen;
use function time;
use function unlink;
use function version_compare;

use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;

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

    // If the update server doesn't respond after this time, give up.
    private const HTTP_TIMEOUT = 3.0;

    private TimeoutService $timeout_service;

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
     *
     * @return void
     */
    public function extractWebtreesZip(string $zip_file, string $target_folder): void
    {
        // The Flysystem ZIP archive adapter is painfully slow, so use the native PHP library.
        $zip = new ZipArchive();

        if ($zip->open($zip_file) === true) {
            $zip->extractTo($target_folder);
            $zip->close();
        } else {
            throw new HttpServerErrorException('Cannot read ZIP file. Is it corrupt?');
        }
    }

    /**
     * Create a list of all the files in a webtrees .ZIP archive
     *
     * @param string $zip_file
     *
     * @return Collection<int,string>
     * @throws FilesystemException
     */
    public function webtreesZipContents(string $zip_file): Collection
    {
        $zip_provider   = new FilesystemZipArchiveProvider($zip_file, 0755);
        $zip_adapter    = new ZipArchiveAdapter($zip_provider, 'webtrees');
        $zip_filesystem = new Filesystem($zip_adapter);

        $files = $zip_filesystem->listContents('', FilesystemReader::LIST_DEEP)
            ->filter(static function (StorageAttributes $attributes): bool {
                return $attributes->isFile();
            })
            ->map(static function (StorageAttributes $attributes): string {
                return $attributes->path();
            });

        return new Collection($files);
    }

    /**
     * Fetch a file from a URL and save it in a filesystem.
     * Use streams so that we can copy files larger than our available memory.
     *
     * @param string             $url
     * @param FilesystemOperator $filesystem
     * @param string             $path
     *
     * @return int The number of bytes downloaded
     * @throws GuzzleException
     * @throws FilesystemException
     */
    public function downloadFile(string $url, FilesystemOperator $filesystem, string $path): int
    {
        // We store the data in PHP temporary storage.
        $tmp = fopen('php://memory', 'wb+');

        // Read from the URL
        $client   = new Client();
        $response = $client->get($url, self::GUZZLE_OPTIONS);
        $stream   = $response->getBody();

        // Download the file to temporary storage.
        while (!$stream->eof()) {
            $data = $stream->read(self::READ_BLOCK_SIZE);

            $bytes_written = fwrite($tmp, $data);

            if ($bytes_written !== strlen($data)) {
                throw new RuntimeException('Unable to write to stream.  Perhaps the disk is full?');
            }

            if ($this->timeout_service->isTimeNearlyUp()) {
                $stream->close();
                throw new HttpServerErrorException(I18N::translate('The server’s time limit has been reached.'));
            }
        }

        $stream->close();

        // Copy from temporary storage to the file.
        $bytes = ftell($tmp);
        rewind($tmp);
        $filesystem->writeStream($path, $tmp);
        fclose($tmp);

        return $bytes;
    }

    /**
     * Move (copy and delete) all files from one filesystem to another.
     *
     * @param FilesystemOperator $source
     * @param FilesystemOperator $destination
     *
     * @return void
     * @throws FilesystemException
     */
    public function moveFiles(FilesystemOperator $source, FilesystemOperator $destination): void
    {
        foreach ($source->listContents('', FilesystemReader::LIST_DEEP) as $attributes) {
            if ($attributes->isFile()) {
                $destination->write($attributes->path(), $source->read($attributes->path()));
                $source->delete($attributes->path());

                if ($this->timeout_service->isTimeNearlyUp()) {
                    throw new HttpServerErrorException(I18N::translate('The server’s time limit has been reached.'));
                }
            }
        }
    }

    /**
     * Delete files in $destination that aren't in $source.
     *
     * @param FilesystemOperator $filesystem
     * @param Collection<int,string> $folders_to_clean
     * @param Collection<int,string> $files_to_keep
     *
     * @return void
     */
    public function cleanFiles(FilesystemOperator $filesystem, Collection $folders_to_clean, Collection $files_to_keep): void
    {
        foreach ($folders_to_clean as $folder_to_clean) {
            try {
                foreach ($filesystem->listContents($folder_to_clean, FilesystemReader::LIST_DEEP) as $path) {
                    if ($path['type'] === 'file' && !$files_to_keep->contains($path['path'])) {
                        try {
                            $filesystem->delete($path['path']);
                        } catch (FilesystemException | UnableToDeleteFile $ex) {
                            // Skip to the next file.
                        }
                    }

                    // If we run out of time, then just stop.
                    if ($this->timeout_service->isTimeNearlyUp()) {
                        return;
                    }
                }
            } catch (FilesystemException $ex) {
                // Skip to the next folder.
            }
        }
    }

    /**
     * @return bool
     */
    public function isUpgradeAvailable(): bool
    {
        // If the latest version is unavailable, we will have an empty string which equates to version 0.

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
     * @return void
     */
    public function startMaintenanceMode(): void
    {
        $message = I18N::translate('This website is being upgraded. Try again in a few minutes.');

        file_put_contents(Webtrees::OFFLINE_FILE, $message);
    }

    /**
     * @return void
     */
    public function endMaintenanceMode(): void
    {
        if (file_exists(Webtrees::OFFLINE_FILE)) {
            unlink(Webtrees::OFFLINE_FILE);
        }
    }

    /**
     * Check with the webtrees.net server for the latest version of webtrees.
     * Fetching the remote file can be slow, so check infrequently, and cache the result.
     * Pass the current versions of webtrees, PHP and MySQL, as the response
     * may be different for each. The server logs are used to generate
     * installation statistics which can be found at https://dev.webtrees.net/statistics.html
     *
     * @return string
     */
    private function fetchLatestVersion(): string
    {
        $last_update_timestamp = (int) Site::getPreference('LATEST_WT_VERSION_TIMESTAMP');

        $current_timestamp = time();

        if ($last_update_timestamp < $current_timestamp - self::CHECK_FOR_UPDATE_INTERVAL) {
            try {
                $client = new Client([
                    'timeout' => self::HTTP_TIMEOUT,
                ]);

                $response = $client->get(self::UPDATE_URL, [
                    'query' => $this->serverParameters(),
                ]);

                if ($response->getStatusCode() === StatusCodeInterface::STATUS_OK) {
                    Site::setPreference('LATEST_WT_VERSION', $response->getBody()->getContents());
                    Site::setPreference('LATEST_WT_VERSION_TIMESTAMP', (string) $current_timestamp);
                }
            } catch (GuzzleException $ex) {
                // Can't connect to the server?
                // Use the existing information about latest versions.
                Log::addErrorLog('Cannot fetch latest webtrees version. ' . $ex->getMessage());
            }
        }

        return Site::getPreference('LATEST_WT_VERSION');
    }

    /**
     * The upgrade server needs to know a little about this server.
     *
     * @return array<string,string>
     */
    private function serverParameters(): array
    {
        $site_uuid = Site::getPreference('SITE_UUID');

        if ($site_uuid === '') {
            $site_uuid = Registry::idFactory()->uuid();
            Site::setPreference('SITE_UUID', $site_uuid);
        }

        $database_type = DB::connection()->getDriverName();

        return [
            'w' => Webtrees::VERSION,
            'p' => PHP_VERSION,
            's' => $site_uuid,
            'd' => $database_type,
        ];
    }
}
