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
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
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
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editRawFact(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$fact_id = $request->get('fact_id');
		$record = GedcomRecord::getInstance($xref, $tree);

		$this->checkRecordAccess($record, true);

		$title = I18N::translate('Edit the raw GEDCOM') . ' - ' . $record->getFullName();

		foreach ($record->getFacts() as $fact) {
			if (!$fact->isPendingDeletion() && $fact->getFactId() === $fact_id) {
				return $this->viewResponse('edit/raw-gedcom-fact', [
					'pattern' => self::GEDCOM_FACT_REGEX,
					'fact'    => $fact,
					'title'   => $title,
					'tree'    => $tree,
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

		$record  = GedcomRecord::getInstance($xref, $tree);

		// Cleanup the client’s bad editing?
		$gedcom  = preg_replace('/[\r\n]+/', "\n", $gedcom); // Empty lines
		$gedcom  = trim($gedcom); // Leading/trailing spaces

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
			'tree'    => $tree,
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
