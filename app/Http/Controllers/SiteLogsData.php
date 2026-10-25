<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\SiteLogsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function e;

final class SiteLogsData
{
    private DatatablesService $datatables_service;

    private SiteLogsService $site_logs_service;

    public function __construct(
        private UserInterface $user,
        DatatablesService $datatables_service,
        SiteLogsService $site_logs_service
    ) {
        $this->datatables_service = $datatables_service;
        $this->site_logs_service  = $site_logs_service;
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->site_logs_service->logsQuery($request);

        return $this->datatables_service->handleQuery($request, $query, [], [], function (object $row): array {
            $log_time = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row->log_time, new DateTimeZone('UTC'))
                ->setTimezone(new DateTimeZone($this->user->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC')))
                ->format('Y-m-d H:i:s T');

            return [
                $row->log_id,
                $log_time,
                $row->log_type,
                '<span class="ut">' . e($row->log_message) . '</span>',
                e($row->ip_address),
                '<span class="ut">' . e($row->user_name) . '</span>',
                '<span class="ut">' . e($row->gedcom_name) . '</span>',
            ];
        });
    }
}
