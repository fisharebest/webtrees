<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

use function addcslashes;

/**
 * Manage pending changes
 */
class PendingChangesService
{
    /**
     * Accept all changes to a tree.
     *
     * @param Tree $tree
     *
     * @return void
     */
    public function acceptTree(Tree $tree): void
    {
        $changes = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->get();

        foreach ($changes as $change) {
            if ($change->new_gedcom === '') {
                // delete
                FunctionsImport::updateRecord($change->old_gedcom, $tree, true);
            } else {
                // add/update
                FunctionsImport::updateRecord($change->new_gedcom, $tree, false);
            }

            DB::table('change')
                ->where('change_id', '=', $change->change_id)
                ->update(['status' => 'accepted']);
        }
    }

    /**
     * Accept all changes to a record.
     *
     * @param GedcomRecord $record
     */
    public function acceptRecord(GedcomRecord $record): void
    {
        $changes = DB::table('change')
            ->where('gedcom_id', '=', $record->tree()->id())
            ->where('xref', '=', $record->xref())
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->get();

        foreach ($changes as $change) {
            if ($change->new_gedcom === '') {
                // delete
                FunctionsImport::updateRecord($change->old_gedcom, $record->tree(), true);
            } else {
                // add/update
                FunctionsImport::updateRecord($change->new_gedcom, $record->tree(), false);
            }

            DB::table('change')
                ->where('change_id', '=', $change->change_id)
                ->update(['status' => 'accepted']);
        }
    }

    /**
     * Accept a change (and previous changes) to a record.
     *
     * @param GedcomRecord $record
     * @param string $change_id
     */
    public function acceptChange(GedcomRecord $record, string $change_id): void
    {
        $changes = DB::table('change')
            ->where('gedcom_id', '=', $record->tree()->id())
            ->where('xref', '=', $record->xref())
            ->where('change_id', '<=', $change_id)
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->get();

        foreach ($changes as $change) {
            if ($change->new_gedcom === '') {
                // delete
                FunctionsImport::updateRecord($change->old_gedcom, $record->tree(), true);
            } else {
                // add/update
                FunctionsImport::updateRecord($change->new_gedcom, $record->tree(), false);
            }

            DB::table('change')
                ->where('change_id', '=', $change->change_id)
                ->update(['status' => 'accepted']);
        }
    }

    /**
     * Reject all changes to a tree.
     *
     * @param Tree $tree
     */
    public function rejectTree(Tree $tree): void
    {
        DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->update(['status' => 'rejected']);
    }

    /**
     * Reject a change (subsequent changes) to a record.
     *
     * @param GedcomRecord $record
     * @param string       $change_id
     */
    public function rejectChange(GedcomRecord $record, string $change_id): void
    {
        DB::table('change')
            ->where('gedcom_id', '=', $record->tree()->id())
            ->where('xref', '=', $record->xref())
            ->where('change_id', '>=', $change_id)
            ->where('status', '=', 'pending')
            ->update(['status' => 'rejected']);
    }

    /**
     * Reject all changes to a record.
     *
     * @param GedcomRecord $record
     */
    public function rejectRecord(GedcomRecord $record): void
    {
        DB::table('change')
            ->where('gedcom_id', '=', $record->tree()->id())
            ->where('xref', '=', $record->xref())
            ->where('status', '=', 'pending')
            ->update(['status' => 'rejected']);
    }

    /**
     * Generate a query for filtering the changes log.
     *
     * @param string[] $params
     *
     * @return Builder
     */
    public function changesQuery(array $params): Builder
    {
        $tree     = $params['tree'];
        $from     = $params['from'] ?? '';
        $to       = $params['to'] ?? '';
        $type     = $params['type'] ?? '';
        $oldged   = $params['oldged'] ?? '';
        $newged   = $params['newged'] ?? '';
        $xref     = $params['xref'] ?? '';
        $username = $params['username'] ?? '';

        $query = DB::table('change')
            ->leftJoin('user', 'user.user_id', '=', 'change.user_id')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'change.gedcom_id')
            ->select(['change.*', new Expression("COALESCE(user_name, '<none>') AS user_name"), 'gedcom_name'])
            ->where('gedcom_name', '=', $tree);

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
            $query->where('old_gedcom', 'LIKE', '%' . addcslashes($oldged, '\\%_') . '%');
        }
        if ($newged !== '') {
            $query->where('new_gedcom', 'LIKE', '%' . addcslashes($newged, '\\%_') . '%');
        }

        if ($xref !== '') {
            $query->where('xref', '=', $xref);
        }

        if ($username !== '') {
            $query->where('user_name', 'LIKE', '%' . addcslashes($username, '\\%_') . '%');
        }

        return $query;
    }
}
