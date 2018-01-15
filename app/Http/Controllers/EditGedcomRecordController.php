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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for edit forms and responses.
 */
class EditGedcomRecordController extends BaseController {
	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editRaw(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = GedcomRecord::getInstance($xref, $tree);

		if ($record === null) {
			return $this->recordNotFound();
		} elseif (!$record->canEdit()) {
			return $this->recordNotAllowed();
		}

		return $this->viewResponse('', [
			'record' => $record,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Request
	 */
	public function editRawAction(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = GedcomRecord::getInstance($xref, $tree);

		if ($record === null) {
			return $this->recordNotFound();
		} elseif (!$record->canEdit()) {
			return $this->recordNotAllowed();
		}

		return new RedirectResponse($record->url());
	}
}
