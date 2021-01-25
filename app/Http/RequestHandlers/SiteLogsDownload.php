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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\SiteLogsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function response;
use function str_replace;

/**
 * Download logs.
 */
class SiteLogsDownload implements RequestHandlerInterface
{
    /** @var SiteLogsService */
    private $site_logs_service;

    /**
     * @param SiteLogsService $site_logs_service
     */
    public function __construct(SiteLogsService $site_logs_service)
    {
        $this->site_logs_service = $site_logs_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $content = $this->site_logs_service->logsQuery($request->getQueryParams())
            ->orderBy('log_id')
            ->get()
            ->map(static function (stdClass $row): string {
                return
                    '"' . $row->log_time . '",' .
                    '"' . $row->log_type . '",' .
                    '"' . str_replace('"', '""', $row->log_message) . '",' .
                    '"' . $row->ip_address . '",' .
                    '"' . str_replace('"', '""', $row->user_name) . '",' .
                    '"' . str_replace('"', '""', $row->gedcom_name) . '"' .
                    "\n";
            })
            ->implode('');

        return response($content, StatusCodeInterface::STATUS_OK, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="webtrees-logs.csv"',
        ]);
    }
}
