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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Webtrees;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Run the housekeeping service at irregular intervals.
 */
class DoHousekeeping implements MiddlewareInterface
{
    // Delete cache files after 1 hour.
    private const MAX_CACHE_AGE = 60 * 60;

    // Delete thumnnails after 90 days.
    private const MAX_THUMBNAIL_AGE = 60 * 60 * 24 * 90;

    // Delete files in /data/tmp after 1 hour.
    private const TMP_DIR          = 'data/tmp';
    private const MAX_TMP_FILE_AGE = 60 * 60;

    // Delete error logs after 90 days.
    private const MAX_LOG_AGE = 60 * 60 * 24 * 90;

    // Delete inactive sessions after 1 day.
    private const MAX_SESSION_AGE = 60 * 60 * 24;

    // Run the cleanup every 100 requests.
    private const PROBABILITY = 100;

    /** @var HousekeepingService */
    private $housekeeping_service;

    /**
     * Housekeeping constructor.
     *
     * @param HousekeepingService $housekeeping_service
     */
    public function __construct(HousekeepingService $housekeeping_service)
    {
        $this->housekeeping_service = $housekeeping_service;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // Run the cleanup after random page requests.
        if ($request->getMethod() === RequestMethodInterface::METHOD_GET && random_int(1, self::PROBABILITY) === 1) {
            $this->runHousekeeping();
        }

        return $response;
    }

    /**
     * Run the various housekeeping services.
     *
     * @return void
     */
    private function runHousekeeping(): void
    {
        $data_filesystem = app(FilesystemInterface::class);
        $root_filesystem = new Filesystem(new Local(Webtrees::ROOT_DIR));

        // Clear files in the (user-specified) data folder - which might not be local files
        $this->housekeeping_service->deleteOldFiles($data_filesystem, 'cache', self::MAX_CACHE_AGE);

        $this->housekeeping_service->deleteOldFiles($data_filesystem, 'thumbnail-cache', self::MAX_THUMBNAIL_AGE);

        // Clear files in /data - which need to be local files
        $this->housekeeping_service->deleteOldFiles($root_filesystem, self::TMP_DIR, self::MAX_TMP_FILE_AGE);

        // Clear entries in database tables
        $this->housekeeping_service->deleteOldLogs(self::MAX_LOG_AGE);

        $this->housekeeping_service->deleteOldSessions(self::MAX_SESSION_AGE);
    }
}
