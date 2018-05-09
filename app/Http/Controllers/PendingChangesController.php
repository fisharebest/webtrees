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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Show, accept and reject pending changes.
 */
class PendingChangesController extends AbstractBaseController {
	/**
	 * Accept all changes to a tree.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function acceptAllChanges(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$url = $request->get('url', '');

		$changes = Database::prepare(
			"SELECT change_id, xref, old_gedcom, new_gedcom" .
			" FROM `##change` c" .
			" JOIN `##gedcom` g USING (gedcom_id)" .
			" WHERE c.status = 'pending' AND gedcom_id = :tree_id" .
			" ORDER BY change_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		foreach ($changes as $change) {
			if (empty($change->new_gedcom)) {
				// delete
				FunctionsImport::updateRecord($change->old_gedcom, $change->gedcom_id, true);
			} else {
				// add/update
				FunctionsImport::updateRecord($change->new_gedcom, $change->gedcom_id, false);
			}

			Database::prepare(
				"UPDATE `##change` SET status = 'accepted' WHERE change_id = :change_id"
			)->execute([
				'change_id' => $change->change_id,
			]);

			Log::addEditLog('Accepted change ' . $change->change_id . ' for ' . $change->xref . ' / ' . $tree->getName());
		}

		return new RedirectResponse(route('show-pending', [
			'ged' => $tree->getName(),
			'url' => $url,
		]));
	}

	/**
	 * Accept a change (and all previous changes) to a single record.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function acceptChange(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$url       = $request->get('url', '');
		$xref      = $request->get('xref', '');
		$change_id = (int) $request->get('change_id');

		$changes = Database::prepare(
			"SELECT change_id, xref, old_gedcom, new_gedcom" .
			" FROM  `##change` c" .
			" JOIN  `##gedcom` g USING (gedcom_id)" .
			" WHERE c.status   = 'pending'" .
			" AND   gedcom_id  = :tree_id" .
			" AND   xref       = :xref" .
			" AND   change_id <= :change_id" .
			" ORDER BY change_id"
		)->execute([
			'tree_id'   => $tree->getTreeId(),
			'xref'      => $xref,
			'change_id' => $change_id,
		])->fetchAll();

		foreach ($changes as $change) {
			if (empty($change->new_gedcom)) {
				// delete
				FunctionsImport::updateRecord($change->old_gedcom, $tree->getTreeId(), true);
			} else {
				// add/update
				FunctionsImport::updateRecord($change->new_gedcom, $tree->getTreeId(), false);
			}
			Database::prepare(
				"UPDATE `##change` SET status = 'accepted' WHERE change_id = :change_id"
			)->execute([
				'change_id' => $change->change_id,
			]);

			Log::addEditLog('Accepted change ' . $change->change_id . ' for ' . $change->xref . ' / ' . $tree->getName());
		}

		return new RedirectResponse(route('show-pending', [
			'ged' => $tree->getName(),
			'url' => $url,
		]));
	}

	/**
	 * Accept all changes to a single record.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function acceptChanges(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, false);

		if ($record && Auth::isModerator($tree)) {
			if ($record->isPendingDeletion()) {
				FlashMessages::addMessage(/* I18N: %s is the name of a genealogy record */
					I18N::translate('“%s” has been deleted.', $record->getFullName()));
			} else {
				FlashMessages::addMessage(/* I18N: %s is the name of a genealogy record */
					I18N::translate('The changes to “%s” have been accepted.', $record->getFullName()));
			}
			FunctionsImport::acceptAllChanges($record->getXref(), $record->getTree()->getTreeId());
		}

		return new Response;
	}

	/**
	 * Reject all changes to a tree.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function rejectAllChanges(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$url = $request->get('url', '');

		Database::prepare(
			"UPDATE `##change` SET status = 'rejected' WHERE status = 'pending' AND gedcom_id = :tree_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		]);

		return new RedirectResponse(route('show-pending', [
			'ged' => $tree->getName(),
			'url' => $url,
		]));
	}

	/**
	 * Reject a change (and all subsequent changes) to a single record.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function rejectChange(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$url       = $request->get('url', '');
		$xref      = $request->get('xref', '');
		$change_id = (int) $request->get('change_id');

		// Reject a change, and subsequent changes to the same record
		Database::prepare(
			"UPDATE `##change`" .
			" SET   status     = 'rejected'" .
			" WHERE status     = 'pending'" .
			" AND   gedcom_id  = :tree_id" .
			" AND   xref       = :xref" .
			" AND   change_id >= :change_id"
		)->execute([
			'tree_id'   => $tree->getTreeid(),
			'xref'      => $xref,
			'change_id' => $change_id,
		]);

		return new RedirectResponse(route('show-pending', [
			'ged' => $tree->getName(),
			'url' => $url,
		]));
	}

	/**
	 * Accept all changes to a single record.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function rejectChanges(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, false);

		if ($record && Auth::isModerator($tree)) {
			FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ I18N::translate('The changes to “%s” have been rejected.', $record->getFullName()));
			FunctionsImport::rejectAllChanges($record);
		}

		return new Response;
	}

	/**
	 * Show the pending changes for the current tree.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function showChanges(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$url = $request->get('url', route('tree-page', ['ged' => $tree->getName()]));

		$rows = Database::prepare(
			"SELECT c.*, UNIX_TIMESTAMP(c.change_time) + :offset AS change_timestamp, u.user_name, u.real_name, g.gedcom_name, new_gedcom, old_gedcom" .
			" FROM `##change` c" .
			" JOIN `##user`   u USING (user_id)" .
			" JOIN `##gedcom` g USING (gedcom_id)" .
			" WHERE c.status='pending'" .
			" ORDER BY gedcom_id, c.xref, c.change_id"
		)->execute([
			'offset' => WT_TIMESTAMP_OFFSET,
		])->fetchAll();

		$changes = [];
		foreach ($rows as $row) {
			$change_tree = Tree::findById($row->gedcom_id);

			preg_match('/^0 (?:@' . WT_REGEX_XREF . '@ )?(' . WT_REGEX_TAG . ')/', $row->old_gedcom . $row->new_gedcom, $match);

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
		if (empty($changes[$tree->getTreeId()])) {
			reset($changes);
			$active_tree_id = key($changes);
		} else {
			$active_tree_id = $tree->getTreeId();
		}

		return $this->viewResponse('pending-changes-page', [
			'active_tree_id' => $active_tree_id,
			'changes'        => $changes,
			'title'          => $title,
			'url'            => $url,
		]);
	}
}
