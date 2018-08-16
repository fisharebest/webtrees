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

namespace Fisharebest\Webtrees\Http\Middleware;

use Closure;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Session;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Run the housekeeping service at irregular intervals.
 */
class Housekeeping implements MiddlewareInterface
{
    // Delete cache files after 1 hour.
    const MAX_CACHE_AGE = 60 * 60;

    // Delete thumnnails after 90 days.
    const MAX_THUMBNAIL_AGE = 60 * 60 * 24 * 90;

    // Delete error logs after 90 days.
    const MAX_LOG_AGE = 60 * 60 * 24 * 90;

    // Delete inactive sessions after 1 day.
    const MAX_SESSION_AGE = 60 * 60 * 24;

    // Run the cleanup every 100 requests.
    const PROBABILITY = 100;

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
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws AccessDeniedHttpException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Run the cleanup after random page requests.
        if (random_int(1, self::PROBABILITY) === 1) {
            $this->runHousekeeping();
        }

        return $response;
    }

    /**
     * Run the various housekeeping services.
     */
    private function runHousekeeping()
    {
        $filesystem = new Filesystem(new Local(WT_DATA_DIR));

        $this->housekeeping_service->deleteOldCacheFiles($filesystem, 'cache', self::MAX_CACHE_AGE);

        $this->housekeeping_service->deleteOldCacheFiles($filesystem, 'thumbnail-cache', self::MAX_THUMBNAIL_AGE);

        $this->housekeeping_service->deleteOldLogs(self::MAX_LOG_AGE);

        $this->housekeeping_service->deleteOldSessions(self::MAX_SESSION_AGE);
    }
}
