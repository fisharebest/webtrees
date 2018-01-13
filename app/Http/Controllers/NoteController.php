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
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the note page.
 */
class NoteController extends BaseController {
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

		if ($record === null) {
			return $this->notFound();
		} elseif (!$record->canShow()) {
			return $this->notAllowed();
		} else {
			return $this->viewResponse('note-page', [
				'note' => $record,
				'families'      => $record->linkedFamilies('NOTE'),
				'individuals'   => $record->linkedIndividuals('NOTE'),
				'notes'         => [],
				'media_objects' => $record->linkedMedia('NOTE'),
				'sources'       => $record->linkedSources('NOTE'),
				'facts'         => $this->facts($record),
				'text'          => Filter::formatText($record->getNote(), $tree),
				'menu'          => $this->menu($record),
			]);
		}
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

	/**
	 * @return Response
	 */
	private function notAllowed(): Response {
		return $this->viewResponse('alerts/danger', [
			'alert' => I18N::translate('This note does not exist or you do not have permission to view it.'),
		], Response::HTTP_FORBIDDEN);
	}

	/**
	 * @return Response
	 */
	private function notFound(): Response {
		return $this->viewResponse('alerts/danger', [
			'alert' => I18N::translate('This note does not exist or you do not have permission to view it.'),
		], Response::HTTP_NOT_FOUND);
	}

	/**
	 * @param Note $record
	 *
	 * @return Menu|null
	 */
	private function menu(Note $record) {
		if ($record->isPendingDeletion()) {
			return null;
		}

		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-fam');

		if (Auth::isEditor($record->getTree())) {
			$menu->addSubmenu(new Menu(I18N::translate('Delete'), '#', 'menu-fam-del', [
				'onclick' => 'return delete_record("' . I18N::translate('Are you sure you want to delete “%s”?', strip_tags($record->getFullName())) . '", "' . $record->getXref() . '");',
			]));
		}

		if (Auth::isAdmin() || Auth::isEditor($record->getTree()) && $record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$menu->addSubmenu(new Menu(I18N::translate('Edit the raw GEDCOM'), 'edit_interface.php?action=editraw&amp;ged=' . $record->getTree()->getNameHtml() . '&amp;xref=' . $record->getXref(), 'menu-fam-editraw'));
		}

		return $menu;
	}
}
