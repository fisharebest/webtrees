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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use function explode;
use function implode;
use function preg_replace_callback;

/**
 * Controller for the changes log.
 */
class ChangesLogController extends AbstractAdminController
{
    /**
     * Show the edit history for a tree.
     *
     * @param ServerRequestInterface $request
     * @param UserService            $user_service
     *
     * @return ResponseInterface
     */
    public function changesLog(ServerRequestInterface $request, UserService $user_service): ResponseInterface
    {
        $tree_list = [];
        foreach (Tree::getAll() as $tree) {
            if (Auth::isManager($tree)) {
                $tree_list[$tree->name()] = $tree->title();
            }
        }

        $user_list = ['' => ''];
        foreach ($user_service->all() as $tmp_user) {
            $user_list[$tmp_user->userName()] = $tmp_user->userName();
        }

        $action = $request->getQueryParams()['action'] ?? '';

        // @TODO This ought to be a POST action
        if ($action === 'delete') {
            $this->changesQuery($request)->delete();
        }

        // First and last change in the database.
        $earliest = DB::table('change')->min('change_time');
        $latest   = DB::table('change')->max('change_time');

        $earliest = $earliest ? Carbon::make($earliest) : Carbon::now();
        $latest   = $latest ? Carbon::make($latest) : Carbon::now();

        $earliest = $earliest->toDateString();
        $latest   = $latest->toDateString();

        $ged      = $request->getQueryParams()['ged'] ?? '';
        $from     = $request->getQueryParams()['from'] ?? $earliest;
        $to       = $request->getQueryParams()['to'] ?? $latest;
        $type     = $request->getQueryParams()['type'] ?? '';
        $oldged   = $request->getQueryParams()['oldged'] ?? '';
        $newged   = $request->getQueryParams()['newged'] ?? '';
        $xref     = $request->getQueryParams()['xref'] ?? '';
        $username = $request->getQueryParams()['username'] ?? '';
        $search   = $request->getQueryParams()['search'] ?? [];
        $search   = $search['value'] ?? null;

        if (!array_key_exists($ged, $tree_list)) {
            $ged = reset($tree_list);
        }

        $statuses = [
            ''         => '',
            /* I18N: the status of an edit accepted/rejected/pending */
            'accepted' => I18N::translate('accepted'),
            /* I18N: the status of an edit accepted/rejected/pending */
            'rejected' => I18N::translate('rejected'),
            /* I18N: the status of an edit accepted/rejected/pending */
            'pending'  => I18N::translate('pending'),
        ];

        return $this->viewResponse('admin/changes-log', [
            'action'    => $action,
            'earliest'  => $earliest,
            'from'      => $from,
            'ged'       => $ged,
            'latest'    => $latest,
            'newged'    => $newged,
            'oldged'    => $oldged,
            'search'    => $search,
            'statuses'  => $statuses,
            'title'     => I18N::translate('Changes log'),
            'to'        => $to,
            'tree_list' => $tree_list,
            'type'      => $type,
            'username'  => $username,
            'user_list' => $user_list,
            'xref'      => $xref,
        ]);
    }

    /**
     * Show the edit history for a tree.
     *
     * @param ServerRequestInterface $request
     * @param DatatablesService      $datatables_service
     * @param MyersDiff              $myers_diff
     *
     * @return ResponseInterface
     */
    public function changesLogData(ServerRequestInterface $request, DatatablesService $datatables_service, MyersDiff $myers_diff): ResponseInterface
    {
        $query = $this->changesQuery($request);

        $callback = function (stdClass $row) use ($myers_diff): array {
            $old_lines = explode("\n", $row->old_gedcom);
            $new_lines = explode("\n", $row->new_gedcom);

            $differences = $myers_diff->calculate($old_lines, $new_lines);
            $diff_lines  = [];

            foreach ($differences as $difference) {
                switch ($difference[1]) {
                    case MyersDiff::DELETE:
                        $diff_lines[] = '<del>' . $difference[0] . '</del>';
                        break;
                    case MyersDiff::INSERT:
                        $diff_lines[] = '<ins>' . $difference[0] . '</ins>';
                        break;
                    default:
                        $diff_lines[] = $difference[0];
                }
            }

            // Only convert valid xrefs to links
            $tree   = Tree::findByName($row->gedcom_name);
            $record = GedcomRecord::getInstance($row->xref, $tree);

            return [
                $row->change_id,
                $row->change_time,
                I18N::translate($row->status),
                $record ? '<a href="' . e($record->url()) . '">' . $record->xref() . '</a>' : $row->xref,
                '<div class="gedcom-data" dir="ltr">' .
                preg_replace_callback(
                    '/@(' . Gedcom::REGEX_XREF . ')@/',
                    function (array $match) use ($tree) : string {
                        $record = GedcomRecord::getInstance($match[1], $tree);

                        return $record ? '<a href="' . e($record->url()) . '">' . $match[0] . '</a>' : $match[0];
                    },
                    implode("\n", $diff_lines)
                ) .
                '</div>',
                $row->user_name,
                $row->gedcom_name,
            ];
        };

        return $datatables_service->handle($request, $query, [], [], $callback);
    }

    /**
     * Show the edit history for a tree.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function changesLogDownload(ServerRequestInterface $request): ResponseInterface
    {
        $content = $this->changesQuery($request)
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
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-disposition' => 'attachment; filename="changes.csv"',
        ]);
    }

    /**
     * Generate a query for filtering the changes log.
     *
     * @param ServerRequestInterface $request
     *
     * @return Builder
     */
    private function changesQuery(ServerRequestInterface $request): Builder
    {
        $from     = $request->getQueryParams()['from'] ?? '';
        $to       = $request->getQueryParams()['to'] ?? '';
        $type     = $request->getQueryParams()['type'] ?? '';
        $oldged   = $request->getQueryParams()['oldged'] ?? '';
        $newged   = $request->getQueryParams()['newged'] ?? '';
        $xref     = $request->getQueryParams()['xref'] ?? '';
        $username = $request->getQueryParams()['username'] ?? '';
        $ged      = $request->getQueryParams()['ged'] ?? '';
        $search   = $request->getQueryParams()['search'] ?? [];
        $search   = $search['value'] ?? '';


        $query = DB::table('change')
            ->leftJoin('user', 'user.user_id', '=', 'change.user_id')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'change.gedcom_id')
            ->select(['change.*', DB::raw("IFNULL(user_name, '<none>') AS user_name"), 'gedcom_name']);

        if ($search !== '') {
            $query->where(static function (Builder $query) use ($search): void {
                $query
                    ->whereContains('old_gedcom', $search)
                    ->whereContains('new_gedcom', $search, 'or');
            });
        }

        if ($from !== '') {
            $query->where('change_time', '>=', $from);
        }

        if ($to !== '') {
            // before end of the day
            $query->where('change_time', '<', Carbon::make($to)->addDay());
        }

        if ($type !== '') {
            $query->where('status', '=', $type);
        }

        if ($oldged !== '') {
            $query->whereContains('old_gedcom', $oldged);
        }
        if ($newged !== '') {
            $query->whereContains('new_gedcom', $oldged);
        }

        if ($xref !== '') {
            $query->where('xref', '=', $xref);
        }

        if ($username !== '') {
            $query->whereContains('user_name', $username);
        }

        if ($ged !== '') {
            $query->whereContains('gedcom_name', $ged);
        }

        return $query;
    }
}
