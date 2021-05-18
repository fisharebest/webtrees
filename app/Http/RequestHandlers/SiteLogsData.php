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

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\SiteLogsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function e;

/**
 * Find logs.
 */
class SiteLogsData implements RequestHandlerInterface
{
    private DatatablesService $datatables_service;

    private SiteLogsService $site_logs_service;

    /**
     * @param DatatablesService $datatables_service
     * @param SiteLogsService   $site_logs_service
     */
    public function __construct(
        DatatablesService $datatables_service,
        SiteLogsService $site_logs_service
    ) {
        $this->datatables_service = $datatables_service;
        $this->site_logs_service  = $site_logs_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->site_logs_service->logsQuery($request->getQueryParams());

        return $this->datatables_service->handleQuery($request, $query, [], [], static function (stdClass $row): array {
            return [
                $row->log_id,
                Carbon::make($row->log_time)->local()->format('Y-m-d H:i:s'),
                $row->log_type,
                '<span dir="auto">' . e($row->log_message) . '</span>',
                '<span dir="auto">' . e($row->ip_address) . '</span>',
                '<span dir="auto">' . e($row->user_name) . '</span>',
                '<span dir="auto">' . e($row->gedcom_name) . '</span>',
            ];
        });
    }
}
