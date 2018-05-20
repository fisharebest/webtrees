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

use Fisharebest\Algorithm\ConnectedComponent;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
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

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function unconnected(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$associates = (bool) $request->get('associates');

		if ($associates) {
			$sql = "SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC', 'ASSO', '_ASSO')";
		} else {
			$sql = "SELECT l_from, l_to FROM `##link` WHERE l_file = :tree_id AND l_type IN ('FAMS', 'FAMC')";
		}

		$rows  = Database::prepare($sql)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();
		$graph = [];

		foreach ($rows as $row) {
			$graph[$row->l_from][$row->l_to] = 1;
			$graph[$row->l_to][$row->l_from] = 1;
		}

		$algorithm  = new ConnectedComponent($graph);
		$components = $algorithm->findConnectedComponents();
		$root       = $tree->significantIndividual($user);
		$xref       = $root->getXref();

		/** @var Individual[][] */
		$individual_groups = [];

		foreach ($components as $component) {
			if (!in_array($xref, $component)) {
				$individuals = [];
				foreach ($component as $xref) {
					$individuals[] = Individual::getInstance($xref, $tree);
				}
				// The database query may return pending additions/deletions, which may not exist.
				$individual_groups[] = array_filter($individuals);
			}
		}

		$title = I18N::translate('Find unrelated individuals') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-unconnected', [
			'associates'        => $associates,
			'root'              => $root,
			'individual_groups' => $individual_groups,
			'title'             => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function renumber(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xrefs = $this->duplicateXrefs($tree);

		$title = I18N::translate(/* I18N: Renumber the records in a family tree */ 'Renumber family tree') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-renumber', [
			'title' => $title,
			'xrefs' => $xrefs,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function renumberAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xrefs = $this->duplicateXrefs($tree);

		foreach ($xrefs as $old_xref => $type) {
			$new_xref = $tree->getNewXref();
			switch ($type) {
				case 'INDI':
					Database::prepare(
						"UPDATE `##individuals` SET i_id = ?, i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_id = ? AND i_file = ?"
					)->execute([$new_xref, "0 @$old_xref@ INDI\n", "0 @$new_xref@ INDI\n", $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = 'HUSB') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " HUSB @$old_xref@", " HUSB @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = 'WIFE') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " WIFE @$old_xref@", " WIFE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = 'CHIL') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " CHIL @$old_xref@", " CHIL @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = 'ASSO') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " ASSO @$old_xref@", " ASSO @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = '_ASSO') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " _ASSO @$old_xref@", " _ASSO @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ? AND l_type = 'ASSO') SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " ASSO @$old_xref@", " ASSO @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ? AND l_type = '_ASSO') SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " _ASSO @$old_xref@", " _ASSO @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##placelinks` SET pl_gid = ? WHERE pl_gid = ? AND pl_file = ?"
					)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##dates` SET d_gid = ? WHERE d_gid = ? AND d_file = ?"
					)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##user_gedcom_setting` SET setting_value = ? WHERE setting_value = ? AND gedcom_id = ? AND setting_name IN ('gedcomid', 'rootid')"
					)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
					break;
				case 'FAM':
					Database::prepare(
						"UPDATE `##families` SET f_id = ?, f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_id = ? AND f_file = ?"
					)->execute([$new_xref, "0 @$old_xref@ FAM\n", "0 @$new_xref@ FAM\n", $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ? AND l_type = 'FAMC') SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " FAMC @$old_xref@", " FAMC @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ? AND l_type = 'FAMS') SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " FAMS @$old_xref@", " FAMS @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##placelinks` SET pl_gid = ? WHERE pl_gid = ? AND pl_file = ?"
					)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##dates` SET d_gid = ? WHERE d_gid = ? AND d_file = ?"
					)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
					break;
				case 'SOUR':
					Database::prepare(
						"UPDATE `##sources` SET s_id = ?, s_gedcom = REPLACE(s_gedcom, ?, ?) WHERE s_id = ? AND s_file = ?"
					)->execute([$new_xref, "0 @$old_xref@ SOUR\n", "0 @$new_xref@ SOUR\n", $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ? AND l_type = 'SOUR') SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " SOUR @$old_xref@", " SOUR @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = 'SOUR') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " SOUR @$old_xref@", " SOUR @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##media` JOIN `##link` ON (l_file = m_file AND l_to = ? AND l_type = 'SOUR') SET m_gedcom = REPLACE(m_gedcom, ?, ?) WHERE m_file = ?"
					)->execute([$old_xref, " SOUR @$old_xref@", " SOUR @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##other` JOIN `##link` ON (l_file = o_file AND l_to = ? AND l_type = 'SOUR') SET o_gedcom = REPLACE(o_gedcom, ?, ?) WHERE o_file = ?"
					)->execute([$old_xref, " SOUR @$old_xref@", " SOUR @$new_xref@", $tree->getTreeId()]);
					break;
				case 'REPO':
					Database::prepare(
						"UPDATE `##other` SET o_id = ?, o_gedcom = REPLACE(o_gedcom, ?, ?) WHERE o_id = ? AND o_file = ?"
					)->execute([$new_xref, "0 @$old_xref@ REPO\n", "0 @$new_xref@ REPO\n", $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##sources` JOIN `##link` ON (l_file = s_file AND l_to = ? AND l_type = 'REPO') SET s_gedcom = REPLACE(s_gedcom, ?, ?) WHERE s_file = ?"
					)->execute([$old_xref, " REPO @$old_xref@", " REPO @$new_xref@", $tree->getTreeId()]);
					break;
				case 'NOTE':
					Database::prepare(
						"UPDATE `##other` SET o_id = ?, o_gedcom = REPLACE(REPLACE(o_gedcom, ?, ?), ?, ?) WHERE o_id = ? AND o_file = ?"
					)->execute([$new_xref, "0 @$old_xref@ NOTE\n", "0 @$new_xref@ NOTE\n", "0 @$old_xref@ NOTE ", "0 @$new_xref@ NOTE ", $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ? AND l_type = 'NOTE') SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " NOTE @$old_xref@", " NOTE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = 'NOTE') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " NOTE @$old_xref@", " NOTE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##media` JOIN `##link` ON (l_file = m_file AND l_to = ? AND l_type = 'NOTE') SET m_gedcom = REPLACE(m_gedcom, ?, ?) WHERE m_file = ?"
					)->execute([$old_xref, " NOTE @$old_xref@", " NOTE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##sources` JOIN `##link` ON (l_file = s_file AND l_to = ? AND l_type = 'NOTE') SET s_gedcom = REPLACE(s_gedcom, ?, ?) WHERE s_file = ?"
					)->execute([$old_xref, " NOTE @$old_xref@", " NOTE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##other` JOIN `##link` ON (l_file = o_file AND l_to = ? AND l_type = 'NOTE') SET o_gedcom = REPLACE(o_gedcom, ?, ?) WHERE o_file = ?"
					)->execute([$old_xref, " NOTE @$old_xref@", " NOTE @$new_xref@", $tree->getTreeId()]);
					break;
				case 'OBJE':
					Database::prepare(
						"UPDATE `##media` SET m_id = ?, m_gedcom = REPLACE(m_gedcom, ?, ?) WHERE m_id = ? AND m_file = ?"
					)->execute([$new_xref, "0 @$old_xref@ OBJE\n", "0 @$new_xref@ OBJE\n", $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##media_file` SET m_id = ? WHERE m_id = ? AND m_file = ?"
					)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ? AND l_type = 'OBJE') SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " OBJE @$old_xref@", " OBJE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ? AND l_type = 'OBJE') SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " OBJE @$old_xref@", " OBJE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##media` JOIN `##link` ON (l_file = m_file AND l_to = ? AND l_type = 'OBJE') SET m_gedcom = REPLACE(m_gedcom, ?, ?) WHERE m_file = ?"
					)->execute([$old_xref, " OBJE @$old_xref@", " OBJE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##sources` JOIN `##link` ON (l_file = s_file AND l_to = ? AND l_type = 'OBJE') SET s_gedcom = REPLACE(s_gedcom, ?, ?) WHERE s_file = ?"
					)->execute([$old_xref, " OBJE @$old_xref@", " OBJE @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##other` JOIN `##link` ON (l_file = o_file AND l_to = ? AND l_type = 'OBJE') SET o_gedcom = REPLACE(o_gedcom, ?, ?) WHERE o_file = ?"
					)->execute([$old_xref, " OBJE @$old_xref@", " OBJE @$new_xref@", $tree->getTreeId()]);
					break;
				default:
					Database::prepare(
						"UPDATE `##other` SET o_id = ?, o_gedcom = REPLACE(o_gedcom, ?, ?) WHERE o_id = ? AND o_file = ?"
					)->execute([$new_xref, "0 @$old_xref@ $type\n", "0 @$new_xref@ $type\n", $old_xref, $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##individuals` JOIN `##link` ON (l_file = i_file AND l_to = ?) SET i_gedcom = REPLACE(i_gedcom, ?, ?) WHERE i_file = ?"
					)->execute([$old_xref, " @$old_xref@", " @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##families` JOIN `##link` ON (l_file = f_file AND l_to = ?) SET f_gedcom = REPLACE(f_gedcom, ?, ?) WHERE f_file = ?"
					)->execute([$old_xref, " @$old_xref@", " @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##media` JOIN `##link` ON (l_file = m_file AND l_to = ?) SET m_gedcom = REPLACE(m_gedcom, ?, ?) WHERE m_file = ?"
					)->execute([$old_xref, " @$old_xref@", " @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##sources` JOIN `##link` ON (l_file = s_file AND l_to = ?) SET s_gedcom = REPLACE(s_gedcom, ?, ?) WHERE s_file = ?"
					)->execute([$old_xref, " @$old_xref@", " @$new_xref@", $tree->getTreeId()]);
					Database::prepare(
						"UPDATE `##other` JOIN `##link` ON (l_file = o_file AND l_to = ?) SET o_gedcom = REPLACE(o_gedcom, ?, ?) WHERE o_file = ?"
					)->execute([$old_xref, " @$old_xref@", " @$new_xref@", $tree->getTreeId()]);
					break;
			}
			Database::prepare(
				"UPDATE `##name` SET n_id = ? WHERE n_id = ? AND n_file = ?"
			)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
			Database::prepare(
				"UPDATE `##default_resn` SET xref = ? WHERE xref = ? AND gedcom_id = ?"
			)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
			Database::prepare(
				"UPDATE `##hit_counter` SET page_parameter = ? WHERE page_parameter = ? AND gedcom_id = ?"
			)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
			Database::prepare(
				"UPDATE `##link` SET l_from = ? WHERE l_from = ? AND l_file = ?"
			)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
			Database::prepare(
				"UPDATE `##link` SET l_to = ? WHERE l_to = ? AND l_file = ?"
			)->execute([$new_xref, $old_xref, $tree->getTreeId()]);

			unset($xrefs[$old_xref]);

			try {
				Database::prepare(
					"UPDATE `##favorite` SET xref = ? WHERE xref = ? AND gedcom_id = ?"
				)->execute([$new_xref, $old_xref, $tree->getTreeId()]);
			} catch (\Exception $ex) {
				DebugBar::addThrowable($ex);

				// Perhaps the favorites module was not installed?
			}

			// How much time do we have left?
			if (microtime(true) - WT_START_TIME > ini_get('max_execution_time') - 5) {
				FlashMessages::addMessage(I18N::translate('The server’s time limit has been reached.'), 'warning');
				break;
			}
		}

		$url = route('admin-trees-renumber', ['ged' => $tree->getName()]);

		return new RedirectResponse($url);
	}

	/**
	 * Every XREF used by this tree and also used by some other tree
	 * 
	 * @param Tree $tree
	 *
	 * @return string[]
	 */
	private function duplicateXrefs(Tree $tree): array {
		return Database::prepare(
			"SELECT xref, type FROM (" .
			" SELECT i_id AS xref, 'INDI' AS type FROM `##individuals` WHERE i_file = :tree_id_1" .
			"  UNION " .
			" SELECT f_id AS xref, 'FAM' AS type FROM `##families` WHERE f_file = :tree_id_2" .
			"  UNION " .
			" SELECT s_id AS xref, 'SOUR' AS type FROM `##sources` WHERE s_file = :tree_id_3" .
			"  UNION " .
			" SELECT m_id AS xref, 'OBJE' AS type FROM `##media` WHERE m_file = :tree_id_4" .
			"  UNION " .
			" SELECT o_id AS xref, o_type AS type FROM `##other` WHERE o_file = :tree_id_5 AND o_type NOT IN ('HEAD', 'TRLR')" .
			") AS this_tree JOIN (" .
			" SELECT xref FROM `##change` WHERE gedcom_id <> :tree_id_6" .
			"  UNION " .
			" SELECT i_id AS xref FROM `##individuals` WHERE i_file <> :tree_id_7" .
			"  UNION " .
			" SELECT f_id AS xref FROM `##families` WHERE f_file <> :tree_id_8" .
			"  UNION " .
			" SELECT s_id AS xref FROM `##sources` WHERE s_file <> :tree_id_9" .
			"  UNION " .
			" SELECT m_id AS xref FROM `##media` WHERE m_file <> :tree_id_10" .
			"  UNION " .
			" SELECT o_id AS xref FROM `##other` WHERE o_file <> :tree_id_11 AND o_type NOT IN ('HEAD', 'TRLR')" .
			") AS other_trees USING (xref)"
		)->execute([
			'tree_id_1'  => $tree->getTreeId(),
			'tree_id_2'  => $tree->getTreeId(),
			'tree_id_3'  => $tree->getTreeId(),
			'tree_id_4'  => $tree->getTreeId(),
			'tree_id_5'  => $tree->getTreeId(),
			'tree_id_6'  => $tree->getTreeId(),
			'tree_id_7'  => $tree->getTreeId(),
			'tree_id_8'  => $tree->getTreeId(),
			'tree_id_9'  => $tree->getTreeId(),
			'tree_id_10' => $tree->getTreeId(),
			'tree_id_11' => $tree->getTreeId(),
		])->fetchAssoc();
	}
}
