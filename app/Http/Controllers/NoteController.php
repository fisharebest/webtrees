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

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the note page.
 */
class NoteController extends AbstractBaseController {
	/**
	 * Show a note's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function show(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = Note::getInstance($xref, $tree);

		$this->checkNoteAccess($record, false);

		return $this->viewResponse('note-page', [
			'facts'         => $this->facts($record),
			'families'      => $record->linkedFamilies('NOTE'),
			'individuals'   => $record->linkedIndividuals('NOTE'),
			'note'          => $record,
			'notes'         => [],
			'media_objects' => $record->linkedMedia('NOTE'),
			'meta_robots'   => 'index,follow',
			'sources'       => $record->linkedSources('NOTE'),
			'text'          => Filter::formatText($record->getNote(), $tree),
			'title'         => $record->getFullName(),
		]);
	}

	/**
	 * @param Note $record
	 *
	 * @return array
	 */
	private function facts(Note $record): array {
		$facts = [];
		foreach ($record->getFacts() as $fact) {
			if ($fact->getTag() != 'CONT') {
				$facts[] = $fact;
			}
		}

		return $facts;
	}
}
