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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Show, accept and reject pending changes.
 */
class PendingChangesController extends AbstractBaseController
{
    /**
     * Accept all changes to a tree.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function acceptAllChanges(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $url = $request->get('url', '');

        $changes = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->get();

        foreach ($changes as $change) {
            if (empty($change->new_gedcom)) {
                // delete
                FunctionsImport::updateRecord($change->old_gedcom, $tree, true);
            } else {
                // add/update
                FunctionsImport::updateRecord($change->new_gedcom, $tree, false);
            }

            DB::table('change')
                ->where('change_id', '=', $change->change_id)
                ->update(['status' => 'accepted']);

            Log::addEditLog('Accepted change ' . $change->change_id . ' for ' . $change->xref . ' / ' . $tree->name(), $tree);
        }

        return redirect(route('show-pending', [
            'ged' => $tree->name(),
            'url' => $url,
        ]));
    }

    /**
     * Accept a change (and all previous changes) to a single record.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function acceptChange(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $url       = $request->get('url', '');
        $xref      = $request->get('xref', '');
        $change_id = (int) $request->get('change_id');

        $changes = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('xref', '=', $xref)
            ->where('change_id', '<=', $change_id)
            ->where('status', '=', 'pending')
            ->orderBy('change_id')
            ->get();

        foreach ($changes as $change) {
            if (empty($change->new_gedcom)) {
                // delete
                FunctionsImport::updateRecord($change->old_gedcom, $tree, true);
            } else {
                // add/update
                FunctionsImport::updateRecord($change->new_gedcom, $tree, false);
            }

            DB::table('change')
                ->where('change_id', '=', $change->change_id)
                ->update(['status' => 'accepted']);

            Log::addEditLog('Accepted change ' . $change->change_id . ' for ' . $change->xref . ' / ' . $tree->name(), $tree);
        }

        return redirect(route('show-pending', [
            'ged' => $tree->name(),
            'url' => $url,
        ]));
    }

    /**
     * Accept all changes to a single record.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function acceptChanges(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $record = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, false);

        if ($record && Auth::isModerator($tree)) {
            if ($record->isPendingDeletion()) {
                /* I18N: %s is the name of a genealogy record */
                FlashMessages::addMessage(I18N::translate('“%s” has been deleted.', $record->fullName()));
            } else {
                /* I18N: %s is the name of a genealogy record */
                FlashMessages::addMessage(I18N::translate('The changes to “%s” have been accepted.', $record->fullName()));
            }
            FunctionsImport::acceptAllChanges($record->xref(), $record->tree());
        }

        return response();
    }

    /**
     * Reject all changes to a tree.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function rejectAllChanges(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $url = $request->get('url', '');

        DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->update(['status' => 'rejected']);

        return redirect(route('show-pending', [
            'ged' => $tree->name(),
            'url' => $url,
        ]));
    }

    /**
     * Reject a change (and all subsequent changes) to a single record.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function rejectChange(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $url       = $request->get('url', '');
        $xref      = $request->get('xref', '');
        $change_id = (int) $request->get('change_id');

        // Reject a change, and subsequent changes to the same record
        DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('xref', '=', $xref)
            ->where('change_id', '>=', $change_id)
            ->where('status', '=', 'pending')
            ->update(['status' => 'rejected']);

        return redirect(route('show-pending', [
            'ged' => $tree->name(),
            'url' => $url,
        ]));
    }

    /**
     * Accept all changes to a single record.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function rejectChanges(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $record = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record);

        if (Auth::isModerator($tree)) {
            DB::table('change')
                ->where('gedcom_id', '=', $record->tree()->id())
                ->where('xref', '=', $record->xref())
                ->where('status', '=', 'pending')
                ->update(['status' => 'rejected']);

            /* I18N: %s is the name of an individual, source or other record */
            FlashMessages::addMessage(I18N::translate('The changes to “%s” have been rejected.', $record->fullName()));
        }

        return response();
    }

    /**
     * Show the pending changes for the current tree.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function showChanges(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $url = $request->get('url', route('tree-page', ['ged' => $tree->name()]));

        $rows = DB::table('change')
            ->join('user', 'user.user_id', '=', 'change.user_id')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'change.gedcom_id')
            ->where('status', '=', 'pending')
            ->orderBy('change.gedcom_id')
            ->orderBy('change.xref')
            ->orderBy('change.change_id')
            ->select(['change.*', 'user.user_name', 'user.real_name', 'gedcom_name'])
            ->get();

        $changes = [];
        foreach ($rows as $row) {
            $row->change_time = Carbon::make($row->change_time);

            $change_tree = Tree::findById((int) $row->gedcom_id);

            preg_match('/^0 (?:@' . Gedcom::REGEX_XREF . '@ )?(' . Gedcom::REGEX_TAG . ')/', $row->old_gedcom . $row->new_gedcom, $match);

            switch ($match[1]) {
                case 'INDI':
                    $row->record = new Individual($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case 'FAM':
                    $row->record = new Family($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case 'SOUR':
                    $row->record = new Source($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case 'REPO':
                    $row->record = new Repository($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case 'OBJE':
                    $row->record = new Media($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case 'NOTE':
                    $row->record = new Note($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                default:
                    $row->record = new GedcomRecord($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
            }

            $changes[$row->gedcom_id][$row->xref][] = $row;
        }

        $title = I18N::translate('Pending changes');

        // If the current tree has changes, activate that tab.  Otherwise activate the first tab.
        if (empty($changes[$tree->id()])) {
            reset($changes);
            $active_tree_id = key($changes);
        } else {
            $active_tree_id = $tree->id();
        }

        return $this->viewResponse('pending-changes-page', [
            'active_tree_id' => $active_tree_id,
            'changes'        => $changes,
            'title'          => $title,
            'url'            => $url,
        ]);
    }
}
