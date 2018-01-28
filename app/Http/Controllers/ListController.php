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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for lists of GEDCOM records.
 */
class ListController extends BaseController {
	/**
	 * Show a list of all note records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function noteList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$notes = $this->allNotes($tree);

		return $this->viewResponse('note-list-page', [
			'notes' => $notes,
			'title' => I18N::translate('Shared notes'),
		]);
	}

	/**
	 * Show a list of all repository records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function repositoryList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$repositories = $this->allrepositories($tree);

		return $this->viewResponse('repository-list-page', [
			'repositories' => $repositories,
			'title'        => I18N::translate('Repositories'),
		]);
	}

	/**
	 * Show a list of all source records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function sourceList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$sources = $this->allSources($tree);

		return $this->viewResponse('source-list-page', [
			'sources' => $sources,
			'title'   => I18N::translate('Sources'),
		]);
	}

	/**
	 * Find all the note records in a tree.
	 *
	 * @param Tree $tree
	 *
	 * @return array
	 */
	private function allNotes(Tree $tree): array {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'NOTE' AND o_file = :tree_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$list = [];
		foreach ($rows as $row) {
			$list[] = Note::getInstance($row->xref, $tree, $row->gedcom);
		}

		return array_filter($list, function (Note $x) {
			return $x->canShowName();
		});
	}

	/**
	 * Find all the repository record in a tree.
	 *
	 * @param Tree $tree
	 *
	 * @return array
	 */
	private function allRepositories(Tree $tree): array {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'REPO' AND o_file = ?"
		)->execute([
			$tree->getTreeId(),
		])->fetchAll();

		$list = [];
		foreach ($rows as $row) {
			$list[] = Repository::getInstance($row->xref, $tree, $row->gedcom);
		}

		return array_filter($list, function (Repository $x) {
			return $x->canShowName();
		});
	}

	/**
	 * Find all the source records in a tree.
	 *
	 * @param Tree $tree
	 *
	 * @return array
	 */
	private function allSources(Tree $tree): array {
		$rows = Database::prepare(
			"SELECT s_id AS xref, s_gedcom AS gedcom FROM `##sources` WHERE s_file = :tree_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$list = [];
		foreach ($rows as $row) {
			$list[] = Source::getInstance($row->xref, $tree, $row->gedcom);
		}

		return array_filter($list, function (Source $x) {
			return $x->canShow();
		});
	}
}
