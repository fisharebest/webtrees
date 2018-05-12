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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for tree administration.
 */
class AdminTreesController extends AbstractBaseController {
	// Show a reduced page when there are more than a certain number of trees
	const MULTIPLE_TREE_THRESHOLD = 500;

	protected $layout = 'layouts/administration';

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function index(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$multiple_tree_threshold = (int) Site::getPreference('MULTIPLE_TREE_THRESHOLD', self::MULTIPLE_TREE_THRESHOLD);
		$gedcom_files            = $this->gedcomFiles(WT_DATA_DIR);

		$all_trees = Tree::getAll();

		// On sites with hundreds or thousands of trees, this page becomes very large.
		// Just show the current tree, the default tree, and unimported trees
		if (count($all_trees) >= $multiple_tree_threshold) {
			$all_trees = array_filter($all_trees, function (Tree $x) use ($tree) {
				return $x->getPreference('imported') === '0' || $tree->getTreeId() === $x->getTreeId() || $x->getName() === Site::getPreference('DEFAULT_GEDCOM');
			});
		}

		$default_tree_name  = $this->generateNewTreeName();
		$default_tree_title = I18N::translate('My family tree');

		$all_users = User::all();

		$title = I18N::translate('Manage family trees');

		return $this->viewResponse('admin/trees', [
			'all_trees'               => $all_trees,
			'all_users'               => $all_users,
			'default_tree_name'       => $default_tree_name,
			'default_tree_title'      => $default_tree_title,
			'gedcom_files'            => $gedcom_files,
			'multiple_tree_threshold' => $multiple_tree_threshold,
			'title'                   => $title,
		]);
	}

	/**
	 * Generate a unqiue name for new trees
	 *
	 * @return string
	 */
	private function generateNewTreeName(): string {
		$tree_name      = 'tree';
		$tree_number    = 1;
		$existing_trees = Tree::getNameList();

		while (array_key_exists($tree_name . $tree_number, $existing_trees)) {
			$tree_number++;
		}

		return $tree_name . $tree_number;
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function importForm(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$default_gedcom_file = $tree->getPreference('gedcom_filename');
		$gedcom_media_path   = $tree->getPreference('GEDCOM_MEDIA_PATH');
		$gedcom_files        = $this->gedcomFiles(WT_DATA_DIR);

		$title = I18N::translate('Import a GEDCOM file') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/tree-import', [
			'default_gedcom_file' => $default_gedcom_file,
			'gedcom_files'        => $gedcom_files,
			'gedcom_media_path'   => $gedcom_media_path,
			'title'               => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function importAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$source             = $request->get('source');
		$keep_media         = (bool) $request->get('keep_media');
		$WORD_WRAPPED_NOTES = (bool) $request->get('WORD_WRAPPED_NOTES');
		$GEDCOM_MEDIA_PATH  = $request->get('GEDCOM_MEDIA_PATH');

		// Save these choices as defaults
		$tree->setPreference('keep_media', $keep_media ? '1' : '0');
		$tree->setPreference('WORD_WRAPPED_NOTES', $WORD_WRAPPED_NOTES ? '1' : '0');
		$tree->setPreference('GEDCOM_MEDIA_PATH', $GEDCOM_MEDIA_PATH);

		if ($source === 'client') {
			if (isset($_FILES['tree_name'])) {
				if ($_FILES['tree_name']['error'] == 0 && is_readable($_FILES['tree_name']['tmp_name'])) {
					$tree->importGedcomFile($_FILES['tree_name']['tmp_name'], $_FILES['tree_name']['name']);
				} else {
					FlashMessages::addMessage(Functions::fileUploadErrorText($_FILES['tree_name']['error']), 'danger');
				}
			} else {
				FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
			}
		}

		if ($source === 'server') {
			$basename = basename($request->get('tree_name'));

			if ($basename) {
				$tree->importGedcomFile(WT_DATA_DIR . $basename, $basename);
			} else {
				FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
			}
		}

		$url = route('admin-trees', ['ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function create(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$tree_name  = $request->get('tree_name', '');
		$tree_title = $request->get('tree_title', '');

		// We use the tree name as a file name, so no directory separators allowed.
		$tree_name = basename($tree_name);

		if ($tree_name !== '' && $tree_title !== '') {
			if (Tree::findByName($tree_name)) {
				FlashMessages::addMessage(I18N::translate('The family tree “%s” already exists.', e($tree_name)), 'danger');
			} else {
				$tree = Tree::create($tree_name, $tree_title);
				FlashMessages::addMessage(I18N::translate('The family tree “%s” has been created.', e($tree->getName())), 'success');
			}
		}

		$url = route('admin-trees', ['ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function delete(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		FlashMessages::addMessage(/* I18N: %s is the name of a family tree */
			I18N::translate('The family tree “%s” has been deleted.', e($tree->getTitle())), 'success');

		$tree->delete();

		$url = route('admin-trees');

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function synchronize(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$gedcom_files = $this->gedcomFiles(WT_DATA_DIR);

		foreach ($gedcom_files as $gedcom_file) {
			// Only import files that have changed
			$filemtime = (string) filemtime(WT_DATA_DIR . $gedcom_file);

			$tree = Tree::findByName($gedcom_file) ?? Tree::create($gedcom_file, $gedcom_file);

			if ($tree->getPreference('filemtime') !== $filemtime) {
				$tree->importGedcomFile(WT_DATA_DIR . $gedcom_file, $gedcom_file);
				$tree->setPreference('filemtime', $filemtime);

				FlashMessages::addMessage(I18N::translate('The GEDCOM file “%s” has been imported.', e($gedcom_file)), 'success');
			}
		}

		foreach (Tree::getAll() as $tree) {
			if (!in_array($tree->getName(), $gedcom_files)) {
				FlashMessages::addMessage(I18N::translate('The family tree “%s” has been deleted.', e($tree->getTitle())), 'success');
				$tree->delete();
			}
		}

		$url = route('admin-trees', ['ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function setDefault(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		Site::setPreference('DEFAULT_GEDCOM', $tree->getName());

		FlashMessages::addMessage(/* I18N: %s is the name of a family tree */
			I18N::translate('The family tree “%s” will be shown to visitors when they first arrive at this website.', e($tree->getTitle())), 'success');

		$url = route('admin-trees');

		return new RedirectResponse($url);
	}

	/**
	 * Find a list of GEDCOM files in a folder
	 *
	 * @param string $folder
	 *
	 * @return array
	 */
	private function gedcomFiles(string $folder): array {
		$d     = opendir($folder);
		$files = [];
		while (($f = readdir($d)) !== false) {
			if (!is_dir(WT_DATA_DIR . $f) && is_readable(WT_DATA_DIR . $f)) {
				$fp     = fopen(WT_DATA_DIR . $f, 'rb');
				$header = fread($fp, 64);
				fclose($fp);
				if (preg_match('/^(' . WT_UTF8_BOM . ')?0 *HEAD/', $header)) {
					$files[] = $f;
				}
			}
		}
		sort($files);

		return $files;
	}
}
