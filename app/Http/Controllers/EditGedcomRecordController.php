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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for edit forms and responses.
 */
class EditGedcomRecordController extends AbstractBaseController {
	const GEDCOM_FACT_REGEX = '^(1 .*(\n2 .*(\n3 .*(\n4 .*(\n5 .*(\n6 .*))))))?$';

	/**
	 * Copy a fact to the clipboard.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function copyFact(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref    = $request->get('xref', '');
		$fact_id = $request->get('fact_id');

		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		foreach ($record->getFacts() as $fact) {
			if ($fact->getFactId() == $fact_id) {
				switch ($fact->getTag()) {
					case 'NOTE':
					case 'SOUR':
					case 'OBJE':
						$type = 'all'; // paste this anywhere
						break;
					default:
						$type = $record::RECORD_TYPE; // paste only to the same record type
						break;
				}
				$clipboard = Session::get('clipboard');
				if (!is_array($clipboard)) {
					$clipboard = [];
				}
				$clipboard[$fact_id] = [
					'type'    => $type,
					'factrec' => $fact->getGedcom(),
					'fact'    => $fact->getTag(),
				];

				// The clipboard only holds 10 facts
				$clipboard = array_slice($clipboard, -10);

				Session::put('clipboard', $clipboard);
				FlashMessages::addMessage(I18N::translate('The record has been copied to the clipboard.'));
				break;
			}
		}

		return new Response;
	}

	/**
	 * Delete a fact.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function deleteFact(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref    = $request->get('xref', '');
		$fact_id = $request->get('fact_id');

		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		foreach ($record->getFacts() as $fact) {
			if ($fact->getFactId() == $fact_id && $fact->canShow() && $fact->canEdit()) {
				$record->deleteFact($fact_id, true);
				break;
			}
		}

		return new Response;
	}

	/**
	 * Delete a record.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function deleteRecord(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref', '');

		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		if ($record && Auth::isEditor($record->getTree()) && $record->canShow() && $record->canEdit()) {
			// Delete links to this record
			foreach (FunctionsDb::fetchAllLinks($record->getXref(), $record->getTree()->getTreeId()) as $xref) {
				$linker     = GedcomRecord::getInstance($xref, $tree);
				$old_gedcom = $linker->getGedcom();
				$new_gedcom = FunctionsEdit::removeLinks($old_gedcom, $record->getXref());
				// FunctionsDb::fetch_all_links() does not take account of pending changes. The links (or even the
				// record itself) may have already been deleted.
				if ($old_gedcom !== $new_gedcom) {
					// If we have removed a link from a family to an individual, and it has only one member
					if (preg_match('/^0 @' . WT_REGEX_XREF . '@ FAM/', $new_gedcom) && preg_match_all('/\n1 (HUSB|WIFE|CHIL) @(' . WT_REGEX_XREF . ')@/', $new_gedcom, $match) == 1) {
						// Delete the family
						$family = GedcomRecord::getInstance($xref, $tree);
						FlashMessages::addMessage(/* I18N: %s is the name of a family group, e.g. “Husband name + Wife name” */
							I18N::translate('The family “%s” has been deleted because it only has one member.', $family->getFullName()));
						$family->deleteRecord();
						// Delete any remaining link to this family
						if ($match) {
							$relict     = GedcomRecord::getInstance($match[2][0], $tree);
							$new_gedcom = $relict->getGedcom();
							$new_gedcom = FunctionsEdit::removeLinks($new_gedcom, $linker->getXref());
							$relict->updateRecord($new_gedcom, false);
							FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */
								I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $relict->getFullName(), $family->getFullName()));
						}
					} else {
						// Remove links from $linker to $record
						FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */
							I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $linker->getFullName(), $record->getFullName()));
						$linker->updateRecord($new_gedcom, false);
					}
				}
			}
			// Delete the record itself
			$record->deleteRecord();
		}

		return new Response;
	}

	/**
	 * Paste a fact from the clipboard into a record.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function pasteFact(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref    = $request->get('xref', '');
		$fact_id = $request->get('fact_id');

		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		$clipboard = Session::get('clipboard');

		if (isset($clipboard[$fact_id])) {
			$record->createFact($clipboard[$fact_id]['factrec'], true);
		}

		return new Response;
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editRawFact(Request $request): Response {
		/** @var Tree $tree */
		$tree    = $request->attributes->get('tree');
		$xref    = $request->get('xref');
		$fact_id = $request->get('fact_id');
		$record  = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		$title = I18N::translate('Edit the raw GEDCOM') . ' - ' . $record->getFullName();

		foreach ($record->getFacts() as $fact) {
			if (!$fact->isPendingDeletion() && $fact->getFactId() === $fact_id) {
				return $this->viewResponse('edit/raw-gedcom-fact', [
					'pattern' => self::GEDCOM_FACT_REGEX,
					'fact'    => $fact,
					'title'   => $title,
				]);
			}
		}

		return new RedirectResponse($record->url());
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editRawFactAction(Request $request): Response {
		/** @var Tree $tree */
		$tree    = $request->attributes->get('tree');
		$xref    = $request->get('xref');
		$fact_id = $request->get('fact_id');
		$gedcom  = $request->get('gedcom');

		$record = GedcomRecord::getInstance($xref, $tree);

		// Cleanup the client’s bad editing?
		$gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom); // Empty lines
		$gedcom = trim($gedcom); // Leading/trailing spaces

		$this->checkRecordAccess($record, true);

		foreach ($record->getFacts() as $fact) {
			if (!$fact->isPendingDeletion() && $fact->getFactId() === $fact_id && $fact->canEdit()) {
				$record->updateFact($fact_id, $gedcom, false);
				break;
			}
		}

		return new RedirectResponse($record->url());
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editRawRecord(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		$title = I18N::translate('Edit the raw GEDCOM') . ' - ' . $record->getFullName();

		return $this->viewResponse('edit/raw-gedcom-record', [
			'pattern' => self::GEDCOM_FACT_REGEX,
			'record'  => $record,
			'title'   => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editRawRecordAction(Request $request): Response {
		/** @var Tree $tree */
		$tree     = $request->attributes->get('tree');
		$xref     = $request->get('xref');
		$facts    = (array) $request->get('fact');
		$fact_ids = (array) $request->get('fact_id');
		$record   = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		$gedcom = '0 @' . $record->getXref() . '@ ' . $record::RECORD_TYPE;

		// Retain any private facts
		foreach ($record->getFacts(null, false, Auth::PRIV_HIDE) as $fact) {
			if (!in_array($fact->getFactId(), $fact_ids) && !$fact->isPendingDeletion()) {
				$gedcom .= "\n" . $fact->getGedcom();
			}
		}
		// Append the updated facts
		foreach ($facts as $fact) {
			$gedcom .= "\n" . $fact;
		}

		// Empty lines and MSDOS line endings.
		$gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom);
		$gedcom = trim($gedcom);

		$record->updateRecord($gedcom, false);

		return new RedirectResponse($record->url());
	}
}
