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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function assert;
use function response;

/**
 * Download pending changes.
 */
class PendingChangesLogDownload implements RequestHandlerInterface
{
    /** @var PendingChangesService */
    private $pending_changes_service;

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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params         = $request->getQueryParams();
        $params['tree'] = $tree->name();

        $content = $this->pending_changes_service->changesQuery($params)
            ->get()
            ->map(static function (stdClass $row): string {
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
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="changes.csv"',
        ]);
    }
}
