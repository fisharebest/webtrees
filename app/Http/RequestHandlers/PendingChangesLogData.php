<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function explode;
use function implode;
use function preg_replace_callback;

final class PendingChangesLogData implements RequestHandlerInterface
{
    private DatatablesService $datatables_service;

    private MyersDiff $myers_diff;

    private PendingChangesService $pending_changes_service;

    /**
     * @param DatatablesService     $datatables_service
     * @param MyersDiff             $myers_diff
     * @param PendingChangesService $pending_changes_service
     */
    public function __construct(
        DatatablesService $datatables_service,
        MyersDiff $myers_diff,
        PendingChangesService $pending_changes_service
    ) {
        $this->datatables_service      = $datatables_service;
        $this->myers_diff              = $myers_diff;
        $this->pending_changes_service = $pending_changes_service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree           = Validator::attributes($request)->tree();
        $params         = $request->getQueryParams();
        $params['tree'] = $tree->name();

        $query = $this->pending_changes_service->changesQuery($params);

        $callback = function (object $row) use ($tree): array {
            $old_lines = $row->old_gedcom === '' ? [] : explode("\n", $row->old_gedcom);
            $new_lines = $row->new_gedcom === '' ? [] : explode("\n", $row->new_gedcom);

            $differences = $this->myers_diff->calculate($old_lines, $new_lines);
            $diff_lines  = [];

            foreach ($differences as $difference) {
                switch ($difference[1]) {
                    case MyersDiff::DELETE:
                        $diff_lines[] = '<del>' . e($difference[0]) . '</del>';
                        break;
                    case MyersDiff::INSERT:
                        $diff_lines[] = '<ins>' . e($difference[0]) . '</ins>';
                        break;
                    default:
                        $diff_lines[] = e($difference[0]);
                }
            }

            // Only convert valid xrefs to links
            $record = Registry::gedcomRecordFactory()->make($row->xref, $tree);

            $change_time = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row->change_time, new DateTimeZone('UTC'))
                ->setTimezone(new DateTimeZone(Auth::user()->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC')))
                ->format('Y-m-d H:i:s T');

            return [
                $row->change_id,
                $change_time,
                I18N::translate($row->status),
                $record ? '<a href="' . e($record->url()) . '">' . $record->xref() . '</a>' : $row->xref,
                '<div class="gedcom-data" dir="ltr">' .
                preg_replace_callback(
                    '/@(' . Gedcom::REGEX_XREF . ')@/',
                    static function (array $match) use ($tree): string {
                        $record = Registry::gedcomRecordFactory()->make($match[1], $tree);

                        return $record ? '<a href="' . e($record->url()) . '">' . $match[0] . '</a>' : $match[0];
                    },
                    implode("\n", $diff_lines)
                ) .
                '</div>',
                $row->user_name,
                $row->gedcom_name,
            ];
        };

        return $this->datatables_service->handleQuery($request, $query, [], [], $callback);
    }
}
