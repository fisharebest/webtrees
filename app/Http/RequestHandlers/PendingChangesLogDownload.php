<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Download pending changes.
 */
class PendingChangesLogDownload implements RequestHandlerInterface
{
    private PendingChangesService $pending_changes_service;

    /**
     * @param PendingChangesService $pending_changes_service
     */
    public function __construct(PendingChangesService $pending_changes_service)
    {
        $this->pending_changes_service = $pending_changes_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree           = Validator::attributes($request)->tree();
        $params         = $request->getQueryParams();
        $params['tree'] = $tree->name();

        $content = $this->pending_changes_service->changesQuery($params)
            ->get()
            ->map(static function (object $row): string {
                // Convert to CSV
                return implode(',', [
                    '"' . $row->change_time . '"',
                    '"' . $row->status . '"',
                    '"' . $row->xref . '"',
                    '"' . str_replace('"', '""', $row->old_gedcom) . '"',
                    '"' . str_replace('"', '""', $row->new_gedcom) . '"',
                    '"' . str_replace('"', '""', $row->user_name) . '"',
                    '"' . str_replace('"', '""', $row->gedcom_name) . '"',
                ]);
            })
            ->implode("\n");

        return response($content, StatusCodeInterface::STATUS_OK, [
            'content-type'        => 'text/csv; charset=UTF-8',
            'content-disposition' => 'attachment; filename="changes.csv"',
        ]);
    }
}
