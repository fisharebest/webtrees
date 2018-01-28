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
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
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
	 * Show a list of all media records.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function mediaList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$formats = GedcomTag::getFileFormTypes();

		$action    = $request->get('action');
		$page      = (int) $request->get('page');
		$max       = (int) $request->get('max', 20);
		$folder    = $request->get('folder', '');
		$filter    = $request->get('filter', '');
		$subdirs   = $request->get('subdirs', '1');
		$form_type = $request->get('form_type', '');

		$folders = $this->allFolders($tree);

		if ($action === '1') {
			$media_objects = $this->allMedia(
				$tree,
				$folder,
				$subdirs === '1' ? 'include' : 'exclude',
				'title',
				$filter,
				$form_type
			);
		} else {
			$media_objects = [];
		}

		// Pagination
		$count = count($media_objects);
		$pages = (int) (($count + $max - 1) / $max);
		$page  = max(min($page, $pages), 1);

		$media_objects = array_slice($media_objects, ($page - 1) * $max, $max);

		return $this->viewResponse('media-list-page', [
			'count'         => $count,
			'filter'        => $filter,
			'folder'        => $folder,
			'folders'       => $folders,
			'formats'       => $formats,
			'form_type'     => $form_type,
			'max'           => $max,
			'media_objects' => $media_objects,
			'page'          => $page,
			'pages'         => $pages,
			'subdirs'       => $subdirs,
			'title'         => I18N::translate('Media'),
			'tree'          => $tree,
		]);
	}

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
	 * Generate a list of all the folders in a current tree.
	 *
	 * @param Tree $tree
	 *
	 * @return string[]
	 */
	private function allFolders(Tree $tree) {
		$folders = Database::prepare(
			"SELECT SQL_CACHE LEFT(multimedia_file_refn, CHAR_LENGTH(multimedia_file_refn) - CHAR_LENGTH(SUBSTRING_INDEX(multimedia_file_refn, '/', -1))) AS media_path" .
			" FROM  `##media_file`" .
			" WHERE m_file = ?" .
			" AND   multimedia_file_refn NOT LIKE 'http://%'" .
			" AND   multimedia_file_refn NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute([
			$tree->getTreeId(),
		])->fetchOneColumn();

		// Ensure we have an empty (top level) folder.
		if (!$folders || reset($folders) !== '') {
			array_unshift($folders, '');
		}

		return array_combine($folders, $folders);
	}


	/**
	 * Generate a list of all the media objects matching the criteria in a current tree.
	 *
	 * @param Tree   $tree       find media in this tree
	 * @param string $folder     folder to search
	 * @param string $subfolders either "include" or "exclude"
	 * @param string $sort       either "file" or "title"
	 * @param string $filter     optional search string
	 * @param string $form_type  option OBJE/FILE/FORM/TYPE
	 *
	 * @return Media[]
	 */
	private function allMedia(Tree $tree, string $folder, string $subfolders, string $sort, string $filter, string $form_type): array {
		// All files in the folder, plus external files
		$sql =
			"SELECT m_id AS xref, m_gedcom AS gedcom" .
			" FROM `##media`" .
			" JOIN `##media_file` USING (m_id, m_file)" .
			" WHERE m_file = ?";
		$args = [
			$tree->getTreeId(),
		];

		// Only show external files when we are looking at the root folder
		if ($folder == '') {
			$sql_external = " OR multimedia_file_refn LIKE 'http://%' OR multimedia_file_refn LIKE 'https://%'";
		} else {
			$sql_external = "";
		}

		// Include / exclude subfolders (but always include external)
		switch ($subfolders) {
			case 'include':
				$sql .= " AND (multimedia_file_refn LIKE CONCAT(?, '%') $sql_external)";
				$args[] = Database::escapeLike($folder);
				break;
			case 'exclude':
				$sql .= " AND (multimedia_file_refn LIKE CONCAT(?, '%') AND multimedia_file_refn NOT LIKE CONCAT(?, '%/%') $sql_external)";
				$args[] = Database::escapeLike($folder);
				$args[] = Database::escapeLike($folder);
				break;
		}

		// Apply search terms
		if ($filter) {
			$sql .= " AND (SUBSTRING_INDEX(multimedia_file_refn, '/', -1) LIKE CONCAT('%', ?, '%') OR descriptive_title LIKE CONCAT('%', ?, '%'))";
			$args[] = Database::escapeLike($filter);
			$args[] = Database::escapeLike($filter);
		}

		if ($form_type) {
			$sql .= " AND source_media_type = ?";
			$args[] = $form_type;
		}

		switch ($sort) {
			case 'file':
				$sql .= " ORDER BY multimedia_file_refn";
				break;
			case 'title':
				$sql .= " ORDER BY descriptive_title";
				break;
		}

		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		$list = [];
		foreach ($rows as $row) {
			$media = Media::getInstance($row->xref, $tree, $row->gedcom);
			if ($media->canShow()) {
				$list[] = $media;
			}
		}

		return $list;
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
