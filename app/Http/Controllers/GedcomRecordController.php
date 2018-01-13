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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the gedcom record page.
 */
class GedcomRecordController extends BaseController {
	/**
	 * Show a gedcom record's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function show(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = GedcomRecord::getInstance($xref, $tree);

		if ($record === null) {
			return $this->notFound();
		} elseif (!$record->canShow()) {
			return $this->notAllowed();
		} elseif ($this->hasCustomPage($record)) {
			return new RedirectResponse($record->url());
		} else {
			return $this->viewResponse('gedcom-record-page', [
				'record'        => $record,
				'families'      => $record->linkedFamilies($record::RECORD_TYPE),
				'individuals'   => $record->linkedIndividuals($record::RECORD_TYPE),
				'notes'         => $record->linkedNotes($record::RECORD_TYPE),
				'media_objects' => $record->linkedMedia($record::RECORD_TYPE),
				'sources'       => $record->linkedSources($record::RECORD_TYPE),
				'facts'         => $record->getFacts(),
			]);
		}
	}

	/**
	 * Is there a better place to display this record?
	 *
	 * @param GedcomRecord $record
	 *
	 * @return bool
	 */
	private function hasCustomPage(GedcomRecord $record): bool {
		return
			$record instanceof Individual ||
			$record instanceof Family ||
			$record instanceof Source ||
			$record instanceof Repository ||
			$record instanceof Note ||
			$record instanceof Media;
	}

	/**
	 * @return Response
	 */
	private function notAllowed(): Response {
		return $this->viewResponse('alerts/danger', [
			'alert' => I18N::translate('This record does not exist or you do not have permission to view it.'),
		], Response::HTTP_FORBIDDEN);
	}

	/**
	 * @return Response
	 */
	private function notFound(): Response {
		return $this->viewResponse('alerts/danger', [
			'alert' => I18N::translate('This record does not exist or you do not have permission to view it.'),
		], Response::HTTP_NOT_FOUND);
	}
}
