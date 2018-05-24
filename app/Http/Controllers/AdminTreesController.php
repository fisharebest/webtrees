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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\SurnameTradition;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use stdClass;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Controller for tree administration.
 */
class AdminTreesController extends AbstractBaseController {
	// Show a reduced page when there are more than a certain number of trees
	const MULTIPLE_TREE_THRESHOLD = 500;

	protected $layout = 'layouts/administration';

	/**
	 * /**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function check(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		// We need to work with raw GEDCOM data, as we are looking for errors
		// which may prevent the GedcomRecord objects from working.

		$rows = Database::prepare(
			"SELECT i_id AS xref, 'INDI' AS type, i_gedcom AS gedrec FROM `##individuals` WHERE i_file=?" .
			" UNION " .
			"SELECT f_id AS xref, 'FAM'  AS type, f_gedcom AS gedrec FROM `##families`    WHERE f_file=?" .
			" UNION " .
			"SELECT s_id AS xref, 'SOUR' AS type, s_gedcom AS gedrec FROM `##sources`     WHERE s_file=?" .
			" UNION " .
			"SELECT m_id AS xref, 'OBJE' AS type, m_gedcom AS gedrec FROM `##media`       WHERE m_file=?" .
			" UNION " .
			"SELECT o_id AS xref, o_type AS type, o_gedcom AS gedrec FROM `##other`       WHERE o_file=? AND o_type NOT IN ('HEAD', 'TRLR')"
		)->execute([$tree->getTreeId(), $tree->getTreeId(), $tree->getTreeId(), $tree->getTreeId(), $tree->getTreeId()])->fetchAll();

		$records = [];
		foreach ($rows as $row) {
			$records[$row->xref] = $row;
		}

		// Need to merge pending new/changed/deleted records

		$rows = Database::prepare(
			"SELECT xref, SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(CASE WHEN old_gedcom='' THEN new_gedcom ELSE old_gedcom END, '\n', 1), ' ', 3), ' ', -1) AS type, new_gedcom AS gedrec" .
			" FROM (" .
			"  SELECT MAX(change_id) AS change_id" .
			"  FROM `##change`" .
			"  WHERE gedcom_id=? AND status='pending'" .
			"  GROUP BY xref" .
			" ) AS t1" .
			" JOIN `##change` t2 USING (change_id)"
		)->execute([$tree->getTreeId()])->fetchAll();

		foreach ($rows as $row) {
			if ($row->gedrec) {
				// new/updated record
				$records[$row->xref] = $row;
			} else {
				// deleted record
				unset($records[$row->xref]);
			}
		}

		// Keep a list of upper case XREFs, to detect mismatches.
		$ukeys = [];
		foreach (array_keys($records) as $key) {
			$ukeys[strtoupper($key)] = $key;
		}

		// LOOK FOR BROKEN LINKS
		$XREF_LINKS = [
			'NOTE'          => 'NOTE',
			'SOUR'          => 'SOUR',
			'REPO'          => 'REPO',
			'OBJE'          => 'OBJE',
			'SUBM'          => 'SUBM',
			'FAMC'          => 'FAM',
			'FAMS'          => 'FAM',
			//'ADOP'=>'FAM', // Need to handle this case specially. We may have both ADOP and FAMC links to the same FAM, but only store one.
			'HUSB'          => 'INDI',
			'WIFE'          => 'INDI',
			'CHIL'          => 'INDI',
			'ASSO'          => 'INDI',
			'_ASSO'         => 'INDI', // A webtrees extension
			'ALIA'          => 'INDI',
			'AUTH'          => 'INDI', // A webtrees extension
			'ANCI'          => 'SUBM',
			'DESI'          => 'SUBM',
			'_WT_OBJE_SORT' => 'OBJE',
			'_LOC'          => '_LOC',
		];

		$RECORD_LINKS = [
			'INDI' => ['NOTE', 'OBJE', 'SOUR', 'SUBM', 'ASSO', '_ASSO', 'FAMC', 'FAMS', 'ALIA', '_WT_OBJE_SORT', '_LOC'],
			'FAM'  => ['NOTE', 'OBJE', 'SOUR', 'SUBM', 'ASSO', '_ASSO', 'HUSB', 'WIFE', 'CHIL', '_LOC'],
			'SOUR' => ['NOTE', 'OBJE', 'REPO', 'AUTH'],
			'REPO' => ['NOTE'],
			'OBJE' => ['NOTE'], // The spec also allows SOUR, but we treat this as a warning
			'NOTE' => [], // The spec also allows SOUR, but we treat this as a warning
			'SUBM' => ['NOTE', 'OBJE'],
			'SUBN' => ['SUBM'],
			'_LOC' => ['SOUR', 'OBJE', '_LOC'],
		];

		$errors   = [];
		$warnings = [];

		// Generate lists of all links
		$all_links   = [];
		$upper_links = [];
		foreach ($records as $record) {
			$all_links[$record->xref]               = [];
			$upper_links[strtoupper($record->xref)] = $record->xref;
			preg_match_all('/\n\d (' . WT_REGEX_TAG . ') @([^#@\n][^\n@]*)@/', $record->gedrec, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				$all_links[$record->xref][$match[2]] = $match[1];
			}
		}

		foreach ($all_links as $xref1 => $links) {
			$type1 = $records[$xref1]->type;
			foreach ($links as $xref2 => $type2) {
				$type3 = isset($records[$xref2]) ? $records[$xref2]->type : '';
				if (!array_key_exists($xref2, $all_links)) {
					if (array_key_exists(strtoupper($xref2), $upper_links)) {
						$warnings[] =
							$this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' .
							/* I18N: placeholders are GEDCOM XREFs, such as R123 */
							I18N::translate('%1$s does not exist. Did you mean %2$s?', $this->checkLink($tree, $xref2), $this->checkLink($tree, $upper_links[strtoupper($xref2)]));
					} else {
						$errors[] =
							$this->checkLinkMessage($tree,
								$type1, $xref1, $type2, $xref2) . ' ' .
							/* I18N: placeholders are GEDCOM XREFs, such as R123 */
							I18N::translate('%1$s does not exist.', $this->checkLink($tree, $xref2));
					}
				} elseif ($type2 === 'SOUR' && $type1 === 'NOTE') {
					// Notes are intended to add explanations and comments to other records. They should not have their own sources.
				} elseif ($type2 === 'SOUR' && $type1 === 'OBJE') {
					// Media objects are intended to illustrate other records, facts, and source/citations. They should not have their own sources.
				} elseif ($type2 === 'OBJE' && $type1 === 'REPO') {
					$warnings[] =
						$this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) .
						' ' .
						I18N::translate('This type of link is not allowed here.');
				} elseif (!array_key_exists($type1, $RECORD_LINKS) || !in_array($type2, $RECORD_LINKS[$type1]) || !array_key_exists($type2, $XREF_LINKS)) {
					$errors[] =
						$this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) .
						' ' .
						I18N::translate('This type of link is not allowed here.');
				} elseif ($XREF_LINKS[$type2] !== $type3) {
					// Target XREF does exist - but is invalid
					$errors[] =
						$this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' .
						/* I18N: %1$s is an internal ID number such as R123. %2$s and %3$s are record types, such as INDI or SOUR */
						I18N::translate('%1$s is a %2$s but a %3$s is expected.', $this->checkLink($tree, $xref2), $this->formatType($type3), $this->formatType($type2));
				} elseif (
					$type2 === 'FAMC' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'CHIL') ||
					$type2 === 'FAMS' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'HUSB' && $all_links[$xref2][$xref1] !== 'WIFE') ||
					$type2 === 'CHIL' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'FAMC') ||
					$type2 === 'HUSB' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'FAMS') ||
					$type2 === 'WIFE' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1] !== 'FAMS')
				) {
					$errors[] =
						$this->checkLinkMessage($tree, $type1, $xref1, $type2, $xref2) . ' ' .
						/* I18N: %1$s and %2$s are internal ID numbers such as R123 */
						I18N::translate('%1$s does not have a link back to %2$s.', $this->checkLink($tree, $xref2), $this->checkLink($tree, $xref1));
				}
			}
		}

		$title = I18N::translate('Check for errors') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-check', [
			'errors'   => $errors,
			'title'    => $title,
			'tree'     => $tree,
			'warnings' => $warnings,
		]);
	}

	/**
	 * Create a message linking one record to another.
	 *
	 * @param Tree   $tree
	 * @param string $type1
	 * @param string $xref1
	 * @param string $type2
	 * @param string $xref2
	 *
	 * @return string
	 */
	private function checkLinkMessage(Tree $tree, $type1, $xref1, $type2, $xref2) {
		return /* I18N: The placeholders are GEDCOM XREFs and tags. e.g. “INDI I123 contains a FAMC link to F234.” */
			I18N::translate(
				'%1$s %2$s has a %3$s link to %4$s.',
				$this->formatType($type1),
				$this->checkLink($tree, $xref1),
				$this->formatType($type2),
				$this->checkLink($tree, $xref2)
			);
	}


	/**
	 * Format a link to a record.
	 *
	 * @param Tree   $tree
	 * @param string $xref
	 *
	 * @return string
	 */
	private function checkLink(Tree $tree, string $xref) {
		return '<b><a href="' . e(route('record', ['xref' => $xref, 'ged' => $tree->getName()])) . '">' . $xref . '</a></b>';
	}

	/**
	 * Format a record type.
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	private function formatType($type) {
		return '<b title="' . GedcomTag::getLabel($type) . '">' . $type . '</b>';
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
	 * @return Response
	 */
	public function duplicates(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$duplicates = $this->duplicateRecords($tree);

		$title = I18N::translate('Find duplicates') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-duplicates', [
			'duplicates' => $duplicates,
			'title'      => $title,
			'tree'       => $tree,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function export(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$title = I18N::translate('Export a GEDCOM file') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-export', [
			'title' => $title,
			'tree'  => $tree,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function exportClient(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		// Validate user parameters
		$convert          = (bool) $request->get('convert');
		$zip              = (bool) $request->get('zip');
		$media            = (bool) $request->get('media');
		$media_path       = $request->get('media-path');
		$privatize_export = $request->get('privatize_export');

		$exportOptions = [
			'privatize' => $privatize_export,
			'toANSI'    => $convert ? 'yes' : 'no',
			'path'      => $media_path,
		];

		// What to call the downloaded file
		$download_filename = $tree->getName();
		if (strtolower(substr($download_filename, -4, 4)) != '.ged') {
			$download_filename .= '.ged';
		}

		if ($zip || $media) {
			// Export the GEDCOM to an in-memory stream.
			$tmp_stream = tmpfile();
			FunctionsExport::exportGedcom($tree, $tmp_stream, $exportOptions);
			rewind($tmp_stream);

			// Create a new/empty .ZIP file
			$temp_zip_file  = tempnam(sys_get_temp_dir(), 'webtrees-zip-');
			$zip_filesystem = new Filesystem(new ZipArchiveAdapter($temp_zip_file));
			$zip_filesystem->writeStream($download_filename, $tmp_stream);

			if ($media) {
				$rows = Database::prepare(
					"SELECT m_id, m_gedcom FROM `##media` WHERE m_file = :tree_id"
				)->execute([
					'tree_id' => $tree->getTreeId(),
				])->fetchAll();
				$path = $tree->getPreference('MEDIA_DIRECTORY');
				foreach ($rows as $row) {
					$record = Media::getInstance($row->m_id, $tree, $row->m_gedcom);
					if ($record->canShow()) {
						foreach ($record->mediaFiles() as $media_file) {
							if (file_exists($media_file->getServerFilename())) {
								$fp = fopen($media_file->getServerFilename(), 'r');
								$zip_filesystem->writeStream($path . $media_file->filename(), $fp);
								fclose($fp);
							}
						}
					}
				}
			}

			// The ZipArchiveAdapter may or may not close the stream.
			if (is_resource($tmp_stream)) {
				fclose($tmp_stream);
			}

			// Need to force-close the filesystem
			$zip_filesystem = null;

			$response = new BinaryFileResponse($temp_zip_file);
			$response->deleteFileAfterSend(true);

			$response->headers->set('Content-Type', 'application/zip');
			$response->setContentDisposition(
				ResponseHeaderBag::DISPOSITION_ATTACHMENT,
				$download_filename . '.zip'
			);
		} else {
			$response = new StreamedResponse(function () use ($tree, $exportOptions) {
				$stream = fopen('php://output', 'w');
				FunctionsExport::exportGedcom($tree, $stream, $exportOptions);
				fclose($stream);
			});

			$charset = $convert ? 'ISO-8859-1' : 'UTF-8';

			$response->headers->set('Content-Type', 'text/plain; charset=' . $charset);
			$contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $download_filename);
			$response->headers->set('Content-Disposition', $contentDisposition);
		}

		return $response;
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function exportServer(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$filename = WT_DATA_DIR . $tree->getName();

		// Force a ".ged" suffix
		if (strtolower(substr($filename, -4)) != '.ged') {
			$filename .= '.ged';
		}

		try {
			// To avoid partial trees on timeout/diskspace/etc, write to a temporary file first
			$stream = fopen($filename . '.tmp', 'w');
			$tree->exportGedcom($stream);
			fclose($stream);
			rename($filename . '.tmp', $filename);

			FlashMessages::addMessage(/* I18N: %s is a filename */
				I18N::translate('The family tree has been exported to %s.', Html::filename($filename)), 'success');
		} catch (Throwable $ex) {
			DebugBar::addThrowable($ex);

			FlashMessages::addMessage(
				I18N::translate('The file %s could not be created.', Html::filename($filename)) . '<hr><samp dir="ltr">' . $ex->getMessage() . '</samp>',
				'danger'
			);
		}

		$url = route('admin-trees', [
			'ged' => $tree->getName(),
		]);

		return new RedirectResponse($url);
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
	 * @return Response
	 */
	public function importForm(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$default_gedcom_file = $tree->getPreference('gedcom_filename');
		$gedcom_media_path   = $tree->getPreference('GEDCOM_MEDIA_PATH');
		$gedcom_files        = $this->gedcomFiles(WT_DATA_DIR);

		$title = I18N::translate('Import a GEDCOM file') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-import', [
			'default_gedcom_file' => $default_gedcom_file,
			'gedcom_files'        => $gedcom_files,
			'gedcom_media_path'   => $gedcom_media_path,
			'title'               => $title,
		]);
	}

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
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function merge(Request $request): Response {
		$tree1_name = $request->get('tree1_name');
		$tree2_name = $request->get('tree2_name');

		$tree1 = Tree::findByName($tree1_name);
		$tree2 = Tree::findByName($tree2_name);

		if ($tree1 !== null && $tree2 !== null && $tree1->getTreeId() !== $tree2->getTreeId()) {
			$xrefs = $this->commonXrefs($tree1, $tree2);
		} else {
			$xrefs = [];
		}

		$tree_list = Tree::getNameList();

		$title = I18N::translate(I18N::translate('Merge family trees'));

		return $this->viewResponse('admin/trees-merge', [
			'tree_list' => $tree_list,
			'tree1'     => $tree1,
			'tree2'     => $tree2,
			'title'     => $title,
			'xrefs'     => $xrefs,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function mergeAction(Request $request): RedirectResponse {
		$tree1_name = $request->get('tree1_name');
		$tree2_name = $request->get('tree2_name');

		$tree1 = Tree::findByName($tree1_name);
		$tree2 = Tree::findByName($tree2_name);

		if ($tree1 !== null && $tree2 !== null && $tree1 !== $tree2 && empty($this->commonXrefs($tree1, $tree2))) {
			Database::prepare(
				"INSERT INTO `##individuals` (i_id, i_file, i_rin, i_sex, i_gedcom)" .
				" SELECT i_id, ?, i_rin, i_sex, i_gedcom FROM `##individuals` AS individuals2 WHERE i_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##families` (f_id, f_file, f_husb, f_wife, f_gedcom, f_numchil)" .
				" SELECT f_id, ?, f_husb, f_wife, f_gedcom, f_numchil FROM `##families` AS families2 WHERE f_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##sources` (s_id, s_file, s_name, s_gedcom)" .
				" SELECT s_id, ?, s_name, s_gedcom FROM `##sources` AS sources2 WHERE s_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##media` (m_id, m_file, m_gedcom)" .
				" SELECT m_id, ?, m_gedcom FROM `##media` AS media2 WHERE m_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##media_file` (m_id, m_file, multimedia_file_refn, multimedia_format, source_media_type, descriptive_title)" .
				" SELECT m_id, ?, multimedia_file_refn, multimedia_format, source_media_type, descriptive_title FROM `##media_file` AS media_file2 WHERE m_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##other` (o_id, o_file, o_type, o_gedcom)" .
				" SELECT o_id, ?, o_type, o_gedcom FROM `##other` AS other2 WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##name` (n_file, n_id, n_num, n_type, n_sort, n_full, n_surname, n_surn, n_givn, n_soundex_givn_std, n_soundex_surn_std, n_soundex_givn_dm, n_soundex_surn_dm)" .
				" SELECT ?, n_id, n_num, n_type, n_sort, n_full, n_surname, n_surn, n_givn, n_soundex_givn_std, n_soundex_surn_std, n_soundex_givn_dm, n_soundex_surn_dm FROM `##name` AS name2 WHERE n_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##placelinks` (pl_p_id, pl_gid, pl_file)" .
				" SELECT pl_p_id, pl_gid, ? FROM `##placelinks` AS placelinks2 WHERE pl_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##dates` (d_day, d_month, d_mon, d_year, d_julianday1, d_julianday2, d_fact, d_gid, d_file, d_type)" .
				" SELECT d_day, d_month, d_mon, d_year, d_julianday1, d_julianday2, d_fact, d_gid, ?, d_type FROM `##dates` AS dates2 WHERE d_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##default_resn` (gedcom_id, xref, tag_type, resn)" .
				" SELECT ?, xref, tag_type, resn FROM `##default_resn` AS default_resn2 WHERE gedcom_id = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			Database::prepare(
				"INSERT INTO `##link` (l_file, l_from, l_type, l_to)" .
				" SELECT ?, l_from, l_type, l_to FROM `##link` AS link2 WHERE l_file = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			// This table may contain old (deleted) references, which could clash. IGNORE these.
			Database::prepare(
				"INSERT IGNORE INTO `##change` (change_time, status, gedcom_id, xref, old_gedcom, new_gedcom, user_id)" .
				" SELECT change_time, status, ?, xref, old_gedcom, new_gedcom, user_id FROM `##change` AS change2 WHERE gedcom_id = ?"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			// This table may contain old (deleted) references, which could clash. IGNORE these.
			Database::prepare(
				"INSERT IGNORE INTO `##hit_counter` (gedcom_id, page_name, page_parameter, page_count)" .
				" SELECT ?, page_name, page_parameter, page_count FROM `##hit_counter` AS hit_counter2 WHERE gedcom_id = ? AND page_name <> 'index.php'"
			)->execute([$tree2->getTreeId(), $tree1->getTreeId()]);

			FlashMessages::addMessage(I18N::translate('The family trees have been merged successfully.'), 'success');

			$url = route('admin-trees', [
				'ged' => $tree2->getName(),
			]);
		} else {
			$url = route('admin-trees-merge', [
				'tree1_name' => $tree1->getName(),
				'tree2_name' => $tree2->getName(),
			]);
		}

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function places(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$search  = $request->get('search', '');
		$replace = $request->get('replace', '');

		if ($search !== '' && $replace !== '') {
			$changes = $this->changePlacesPreview($tree, $search, $replace);
		} else {
			$changes = [];
		}

		$title = I18N::translate(/* I18N: Renumber the records in a family tree */
				'Renumber family tree') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-places', [
			'changes' => $changes,
			'replace' => $replace,
			'search'  => $search,
			'title'   => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function placesAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$search  = $request->get('search', '');
		$replace = $request->get('replace', '');

		$changes = $this->changePlacesUpdate($tree, $search, $replace);

		$feedback = I18N::translate('The following places have been changed:') . '<ul>';
		foreach ($changes as $old_place => $new_place) {
			$feedback .= '<li>' . e($old_place) . ' &rarr; ' . e($new_place) . '</li>';
		}
		$feedback .= '</ul>';

		FlashMessages::addMessage($feedback, 'success');

		$url = route('admin-trees-places', [
			'ged'     => $tree->getName(),
			'replace' => $replace,
			'search'  => $search,
		]);

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function preferences(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$french_calendar_start    = new Date('22 SEP 1792');
		$french_calendar_end      = new Date('31 DEC 1805');
		$gregorian_calendar_start = new Date('15 OCT 1582');

		$surname_list_styles = [
			'style1' => /* I18N: Layout option for lists of names */
				I18N::translate('list'),
			'style2' => /* I18N: Layout option for lists of names */
				I18N::translate('table'),
			'style3' => /* I18N: Layout option for lists of names */
				I18N::translate('tag cloud'),
		];

		$page_layouts = [
			0 => /* I18N: page orientation */
				I18N::translate('Portrait'),
			1 => /* I18N: page orientation */
				I18N::translate('Landscape'),
		];

		$formats = [
			''         => /* I18N: None of the other options */
				I18N::translate('none'),
			'markdown' => /* I18N: https://en.wikipedia.org/wiki/Markdown */
				I18N::translate('markdown'),
		];

		$source_types = [
			0 => I18N::translate('none'),
			1 => I18N::translate('facts'),
			2 => I18N::translate('records'),
		];

		$theme_options = ['' => I18N::translate('<default theme>')] + Theme::themeNames();

		$privacy_options = [
			Auth::PRIV_USER => I18N::translate('Show to members'),
			Auth::PRIV_NONE => I18N::translate('Show to managers'),
			Auth::PRIV_HIDE => I18N::translate('Hide from everyone'),
		];

		$tags = array_unique(array_merge(
			explode(',', $tree->getPreference('INDI_FACTS_ADD')), explode(',', $tree->getPreference('INDI_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('FAM_FACTS_ADD')), explode(',', $tree->getPreference('FAM_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('NOTE_FACTS_ADD')), explode(',', $tree->getPreference('NOTE_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('SOUR_FACTS_ADD')), explode(',', $tree->getPreference('SOUR_FACTS_UNIQUE')),
			explode(',', $tree->getPreference('REPO_FACTS_ADD')), explode(',', $tree->getPreference('REPO_FACTS_UNIQUE')),
			['SOUR', 'REPO', 'OBJE', '_PRIM', 'NOTE', 'SUBM', 'SUBN', '_UID', 'CHAN']
		));

		$all_tags = [];
		foreach ($tags as $tag) {
			if ($tag) {
				$all_tags[$tag] = GedcomTag::getLabel($tag);
			}
		}

		uasort($all_tags, '\Fisharebest\Webtrees\I18N::strcasecmp');

		$resns = Database::prepare(
			"SELECT default_resn_id, tag_type, xref, resn" .
			" FROM `##default_resn`" .
			" LEFT JOIN `##name` ON (gedcom_id=n_file AND xref=n_id AND n_num=0)" .
			" WHERE gedcom_id=?" .
			" ORDER BY xref IS NULL, n_sort, xref, tag_type"
		)->execute([$tree->getTreeId()])->fetchAll();

		foreach ($resns as $resn) {
			$resn->record = GedcomRecord::getInstance($resn->xref, $tree);
			if ($resn->tag_type) {
				$resn->tag_label = GedcomTag::getLabel($resn->tag_type);
			} else {
				$resn->tag_label = '';
			}
		}
		usort($resns, function (stdClass $x, stdClass $y) {
			return I18N::strcasecmp($x->tag_label, $y->tag_label);
		});

		// For historical reasons, we have two fields in one
		$calendar_formats = explode('_and_', $tree->getPreference('CALENDAR_FORMAT') . '_and_');

		// Split into separate fields
		$relatives_events = explode(',', $tree->getPreference('SHOW_RELATIVES_EVENTS'));

		$pedigree_individual = Individual::getInstance($tree->getPreference('PEDIGREE_ROOT_ID'), $tree);

		$members = array_filter(User::all(), function (User $user) use ($tree) {
			return Auth::isMember($tree, $user);
		});

		$all_fam_facts  = GedcomTag::getPicklistFacts('FAM');
		$all_indi_facts = GedcomTag::getPicklistFacts('INDI');
		$all_name_facts = GedcomTag::getPicklistFacts('NAME');
		$all_plac_facts = GedcomTag::getPicklistFacts('PLAC');
		$all_repo_facts = GedcomTag::getPicklistFacts('REPO');
		$all_sour_facts = GedcomTag::getPicklistFacts('SOUR');

		$all_surname_traditions = SurnameTradition::allDescriptions();

		$tree_count = count(Tree::getAll());

		$title = I18N::translate('Preferences') . ' — ' . e($tree->getTitle());

		return $this->viewResponse('admin/trees-preferences', [
			'all_fam_facts'            => $all_fam_facts,
			'all_indi_facts'           => $all_indi_facts,
			'all_name_facts'           => $all_name_facts,
			'all_plac_facts'           => $all_plac_facts,
			'all_repo_facts'           => $all_repo_facts,
			'all_sour_facts'           => $all_sour_facts,
			'all_surname_traditions'   => $all_surname_traditions,
			'calendar_formats'         => $calendar_formats,
			'formats'                  => $formats,
			'french_calendar_end'      => $french_calendar_end,
			'french_calendar_start'    => $french_calendar_start,
			'gregorian_calendar_start' => $gregorian_calendar_start,
			'members'                  => $members,
			'page_layouts'             => $page_layouts,
			'pedigree_individual'      => $pedigree_individual,
			'privacy_options'          => $privacy_options,
			'relatives_events'         => $relatives_events,
			'source_types'             => $source_types,
			'surname_list_styles'      => $surname_list_styles,
			'theme_options'            => $theme_options,
			'title'                    => $title,
			'tree'                     => $tree,
			'tree_count'               => $tree_count,
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

		$title = I18N::translate(/* I18N: Renumber the records in a family tree */
				'Renumber family tree') . ' — ' . e($tree->getTitle());

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
	public function preferencesUpdate(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		// Coming soon
		if ((bool) $request->get('all_trees')) {
			FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.'), 'success');
		}
		if ((bool) $request->get('new_trees')) {
			FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.'), 'success');
		}

		$tree->setPreference('ADVANCED_NAME_FACTS', implode(',', $request->get('ADVANCED_NAME_FACTS', [])));
		$tree->setPreference('ADVANCED_PLAC_FACTS', implode(',', $request->get('ADVANCED_PLAC_FACTS', [])));
		$tree->setPreference('ALLOW_THEME_DROPDOWN', (string) (bool) $request->get('ALLOW_THEME_DROPDOWN'));
		// For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
		// e.g. "gregorian_and_jewish"
		$tree->setPreference('CALENDAR_FORMAT', implode('_and_', array_unique([
			$request->get('CALENDAR_FORMAT0',  'none'),
			$request->get('CALENDAR_FORMAT1',  'none'),
		])));
		$tree->setPreference('CHART_BOX_TAGS', implode(',', $request->get('CHART_BOX_TAGS', [])));
		$tree->setPreference('CONTACT_USER_ID', $request->get('CONTACT_USER_ID'));
		$tree->setPreference('DEFAULT_PEDIGREE_GENERATIONS', (string) (int) $request->get('DEFAULT_PEDIGREE_GENERATIONS'));
		$tree->setPreference('EXPAND_NOTES', (string) (bool) $request->get('EXPAND_NOTES'));
		$tree->setPreference('EXPAND_SOURCES', (string) (bool) $request->get('EXPAND_SOURCES'));
		$tree->setPreference('FAM_FACTS_ADD', implode(',', $request->get('FAM_FACTS_ADD', [])));
		$tree->setPreference('FAM_FACTS_QUICK', implode(',', $request->get('FAM_FACTS_QUICK', [])));
		$tree->setPreference('FAM_FACTS_UNIQUE', implode(',', $request->get('FAM_FACTS_UNIQUE', [])));
		$tree->setPreference('FULL_SOURCES', (string) (bool) $request->get('FULL_SOURCES'));
		$tree->setPreference('FORMAT_TEXT', $request->get('FORMAT_TEXT'));
		$tree->setPreference('GENERATE_UIDS', (string) (bool) $request->get('GENERATE_UIDS'));
		$tree->setPreference('GEONAMES_ACCOUNT', $request->get('GEONAMES_ACCOUNT'));
		$tree->setPreference('HIDE_GEDCOM_ERRORS', (string) (bool) $request->get('HIDE_GEDCOM_ERRORS'));
		$tree->setPreference('INDI_FACTS_ADD', implode(',', $request->get('INDI_FACTS_ADD', [])));
		$tree->setPreference('INDI_FACTS_QUICK', implode(',', $request->get('INDI_FACTS_QUICK', [])));
		$tree->setPreference('INDI_FACTS_UNIQUE', implode(',', $request->get('INDI_FACTS_UNIQUE', [])));
		$tree->setPreference('LANGUAGE', $request->get('LANGUAGE'));
		$tree->setPreference('MAX_DESCENDANCY_GENERATIONS', (string) (int) $request->get('MAX_DESCENDANCY_GENERATIONS'));
		$tree->setPreference('MAX_PEDIGREE_GENERATIONS', (string) (int) $request->get('MAX_PEDIGREE_GENERATIONS'));
		$tree->setPreference('MEDIA_UPLOAD', $request->get('MEDIA_UPLOAD'));
		$tree->setPreference('META_DESCRIPTION', $request->get('META_DESCRIPTION'));
		$tree->setPreference('META_TITLE', $request->get('META_TITLE'));
		$tree->setPreference('NO_UPDATE_CHAN', (string) (bool) $request->get('NO_UPDATE_CHAN'));
		$tree->setPreference('PEDIGREE_LAYOUT', (string) (bool) $request->get('PEDIGREE_LAYOUT'));
		$tree->setPreference('PEDIGREE_ROOT_ID', $request->get('PEDIGREE_ROOT_ID', WT_REGEX_XREF));
		$tree->setPreference('PEDIGREE_SHOW_GENDER', (string) (bool) $request->get('PEDIGREE_SHOW_GENDER'));
		$tree->setPreference('PREFER_LEVEL2_SOURCES', $request->get('PREFER_LEVEL2_SOURCES'));
		$tree->setPreference('QUICK_REQUIRED_FACTS', implode(',', $request->get('QUICK_REQUIRED_FACTS', [])));
		$tree->setPreference('QUICK_REQUIRED_FAMFACTS', implode(',', $request->get('QUICK_REQUIRED_FAMFACTS', [])));
		$tree->setPreference('REPO_FACTS_ADD', implode(',', $request->get('REPO_FACTS_ADD', [])));
		$tree->setPreference('REPO_FACTS_QUICK', implode(',', $request->get('REPO_FACTS_QUICK', [])));
		$tree->setPreference('REPO_FACTS_UNIQUE', implode(',', $request->get('REPO_FACTS_UNIQUE', [])));
		$tree->setPreference('SHOW_COUNTER', (string) (bool) $request->get('SHOW_COUNTER'));
		$tree->setPreference('SHOW_EST_LIST_DATES', (string) (bool) $request->get('SHOW_EST_LIST_DATES'));
		$tree->setPreference('SHOW_FACT_ICONS', (string) (bool) $request->get('SHOW_FACT_ICONS'));
		$tree->setPreference('SHOW_GEDCOM_RECORD', (string) (bool) $request->get('SHOW_GEDCOM_RECORD'));
		$tree->setPreference('SHOW_HIGHLIGHT_IMAGES', (string) (bool) $request->get('SHOW_HIGHLIGHT_IMAGES'));
		$tree->setPreference('SHOW_LAST_CHANGE', (string) (bool) $request->get('SHOW_LAST_CHANGE'));
		$tree->setPreference('SHOW_LDS_AT_GLANCE', (string) (bool) $request->get('SHOW_LDS_AT_GLANCE'));
		$tree->setPreference('SHOW_MEDIA_DOWNLOAD', $request->get('SHOW_MEDIA_DOWNLOAD'));
		$tree->setPreference('SHOW_NO_WATERMARK', $request->get('SHOW_NO_WATERMARK'));
		$tree->setPreference('SHOW_PARENTS_AGE', (string) (bool) $request->get('SHOW_PARENTS_AGE'));
		$tree->setPreference('SHOW_PEDIGREE_PLACES', $request->get('SHOW_PEDIGREE_PLACES'));
		$tree->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', (string) (bool) $request->get('SHOW_PEDIGREE_PLACES_SUFFIX'));
		$tree->setPreference('SHOW_RELATIVES_EVENTS', implode(',', $request->get('SHOW_RELATIVES_EVENTS', [])));
		$tree->setPreference('SOUR_FACTS_ADD', implode(',', $request->get('SOUR_FACTS_ADD', [])));
		$tree->setPreference('SOUR_FACTS_QUICK', implode(',', $request->get('SOUR_FACTS_QUICK', [])));
		$tree->setPreference('SOUR_FACTS_UNIQUE', implode(',', $request->get('SOUR_FACTS_UNIQUE', [])));
		$tree->setPreference('SUBLIST_TRIGGER_I', (string) (int) $request->get('SUBLIST_TRIGGER_I', 200));
		$tree->setPreference('SURNAME_LIST_STYLE', $request->get('SURNAME_LIST_STYLE'));
		$tree->setPreference('SURNAME_TRADITION', $request->get('SURNAME_TRADITION'));
		$tree->setPreference('THEME_DIR', $request->get('THEME_DIR'));
		$tree->setPreference('USE_SILHOUETTE', (string) (bool) $request->get('USE_SILHOUETTE'));
		$tree->setPreference('WEBMASTER_USER_ID', $request->get('WEBMASTER_USER_ID'));
		$tree->setPreference('WEBTREES_EMAIL', $request->get('WEBTREES_EMAIL'));
		$tree->setPreference('title', $request->get('title'));

		// Only accept valid folders for MEDIA_DIRECTORY
		$MEDIA_DIRECTORY = preg_replace('/[\/\\\\]+/', '/', $request->get('MEDIA_DIRECTORY') . '/');
		if (substr($MEDIA_DIRECTORY, 0, 1) === '/') {
			$MEDIA_DIRECTORY = substr($MEDIA_DIRECTORY, 1);
		}

		if ($MEDIA_DIRECTORY) {
			if (is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
				$tree->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
			} elseif (File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
				$tree->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
				FlashMessages::addMessage(I18N::translate('The folder %s has been created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)), 'info');
			} else {
				FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)), 'danger');
			}
		}

		$gedcom = $request->get('gedcom');
		if ($gedcom && $gedcom !== $tree->getName()) {
			try {
				Database::prepare("UPDATE `##gedcom` SET gedcom_name = ? WHERE gedcom_id = ?")->execute([$gedcom, $tree->getTreeId()]);
				Database::prepare("UPDATE `##site_setting` SET setting_value = ? WHERE setting_name='DEFAULT_GEDCOM' AND setting_value = ?")->execute([$gedcom, $tree->getName()]);
			} catch (\Exception $ex) {
				DebugBar::addThrowable($ex);

				// Probably a duplicate name.
			}
		}

		FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->getTitle())), 'success');

		$url = route('admin-trees', ['ged' => $tree->getName()]);

		return new RedirectResponse($url);
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
	 * Find a list of place names that would be updated.
	 *
	 * @param Tree   $tree
	 * @param string $search
	 * @param string $replace
	 *
	 * @return string[]
	 */
	private function changePlacesPreview(Tree $tree, string $search, string $replace): array {
		$changes = [];

		$rows = Database::prepare(
			"SELECT i_id AS xref, COALESCE(new_gedcom, i_gedcom) AS gedcom" .
			" FROM `##individuals`" .
			" LEFT JOIN `##change` ON (i_id = xref AND i_file=gedcom_id AND status='pending')" .
			" WHERE i_file = ?" .
			" AND COALESCE(new_gedcom, i_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
		)->execute([$tree->getTreeId(), preg_quote($search)])->fetchAll();
		foreach ($rows as $row) {
			$record = Individual::getInstance($row->xref, $tree, $row->gedcom);
			foreach ($record->getFacts() as $fact) {
				$old_place = $fact->getAttribute('PLAC');
				if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
					$new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
					$changes[$old_place] = $new_place;
				}
			}
		}
		$rows = Database::prepare(
			"SELECT f_id AS xref, COALESCE(new_gedcom, f_gedcom) AS gedcom" .
			" FROM `##families`" .
			" LEFT JOIN `##change` ON (f_id = xref AND f_file=gedcom_id AND status='pending')" .
			" WHERE f_file = ?" .
			" AND COALESCE(new_gedcom, f_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
		)->execute([$tree->getTreeId(), preg_quote($search)])->fetchAll();
		foreach ($rows as $row) {
			$record = Family::getInstance($row->xref, $tree, $row->gedcom);
			foreach ($record->getFacts() as $fact) {
				$old_place = $fact->getAttribute('PLAC');
				if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
					$new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
					$changes[$old_place] = $new_place;
				}
			}
		}

		asort($changes);

		return $changes;
	}

	/**
	 * Find a list of place names that would be updated.
	 *
	 * @param Tree   $tree
	 * @param string $search
	 * @param string $replace
	 *
	 * @return string[]
	 */
	private function changePlacesUpdate(Tree $tree, string $search, string $replace): array {
		$changes = [];

		$rows = Database::prepare(
			"SELECT i_id AS xref, COALESCE(new_gedcom, i_gedcom) AS gedcom" .
			" FROM `##individuals`" .
			" LEFT JOIN `##change` ON (i_id = xref AND i_file=gedcom_id AND status='pending')" .
			" WHERE i_file = ?" .
			" AND COALESCE(new_gedcom, i_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
		)->execute([$tree->getTreeId(), preg_quote($search)])->fetchAll();
		foreach ($rows as $row) {
			$record = Individual::getInstance($row->xref, $tree, $row->gedcom);
			foreach ($record->getFacts() as $fact) {
				$old_place = $fact->getAttribute('PLAC');
				if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
					$new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
					$changes[$old_place] = $new_place;
					$gedcom              = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->getGedcom());
					$record->updateFact($fact->getFactId(), $gedcom, false);
				}
			}
		}
		$rows = Database::prepare(
			"SELECT f_id AS xref, COALESCE(new_gedcom, f_gedcom) AS gedcom" .
			" FROM `##families`" .
			" LEFT JOIN `##change` ON (f_id = xref AND f_file=gedcom_id AND status='pending')" .
			" WHERE f_file = ?" .
			" AND COALESCE(new_gedcom, f_gedcom) REGEXP CONCAT('\n2 PLAC ([^\n]*, )*', ?, '(\n|$)')"
		)->execute([$tree->getTreeId(), preg_quote($search)])->fetchAll();
		foreach ($rows as $row) {
			$record = Family::getInstance($row->xref, $tree, $row->gedcom);
			foreach ($record->getFacts() as $fact) {
				$old_place = $fact->getAttribute('PLAC');
				if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
					$new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
					$changes[$old_place] = $new_place;
					$gedcom              = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->getGedcom());
					$record->updateFact($fact->getFactId(), $gedcom, false);
				}
			}
		}

		asort($changes);

		return $changes;
	}

	/**
	 * Every XREF used by two trees at the same time.
	 *
	 * @param Tree $tree1
	 * @param Tree $tree2
	 *
	 * @return string[]
	 */
	private function commonXrefs(Tree $tree1, Tree $tree2): array {
		return Database::prepare(
			"SELECT xref, type FROM (" .
			" SELECT i_id AS xref, 'INDI' AS type FROM `##individuals` WHERE i_file = ?" .
			"  UNION " .
			" SELECT f_id AS xref, 'FAM' AS type FROM `##families` WHERE f_file = ?" .
			"  UNION " .
			" SELECT s_id AS xref, 'SOUR' AS type FROM `##sources` WHERE s_file = ?" .
			"  UNION " .
			" SELECT m_id AS xref, 'OBJE' AS type FROM `##media` WHERE m_file = ?" .
			"  UNION " .
			" SELECT o_id AS xref, o_type AS type FROM `##other` WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')" .
			") AS this_tree JOIN (" .
			" SELECT xref FROM `##change` WHERE gedcom_id = ?" .
			"  UNION " .
			" SELECT i_id AS xref FROM `##individuals` WHERE i_file = ?" .
			"  UNION " .
			" SELECT f_id AS xref FROM `##families` WHERE f_file = ?" .
			"  UNION " .
			" SELECT s_id AS xref FROM `##sources` WHERE s_file = ?" .
			"  UNION " .
			" SELECT m_id AS xref FROM `##media` WHERE m_file = ?" .
			"  UNION " .
			" SELECT o_id AS xref FROM `##other` WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')" .
			") AS other_trees USING (xref)"
		)->execute([
			$tree1->getTreeId(),
			$tree1->getTreeId(),
			$tree1->getTreeId(),
			$tree1->getTreeId(),
			$tree1->getTreeId(),
			$tree2->getTreeId(),
			$tree2->getTreeId(),
			$tree2->getTreeId(),
			$tree2->getTreeId(),
			$tree2->getTreeId(),
			$tree2->getTreeId(),
		])->fetchAssoc();
	}

	/**
	 * @param Tree $tree
	 *
	 * @return array
	 */
	private function duplicateRecords(Tree $tree): array {
		$repositories = Database::prepare(
			"SELECT GROUP_CONCAT(n_id) AS xrefs " .
			" FROM `##other`" .
			" JOIN `##name` ON o_id = n_id AND o_file = n_file" .
			" WHERE o_file = :tree_id AND o_type = 'REPO'" .
			" GROUP BY n_full" .
			" HAVING COUNT(n_id) > 1"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$repositories = array_map(
			function (stdClass $x) use ($tree) {
				$tmp = explode(',', $x->xrefs);

				return array_map(function ($y) use ($tree) {
					return Repository::getInstance($y, $tree);
				}, $tmp);
			}, $repositories
		);

		$sources = Database::prepare(
			"SELECT GROUP_CONCAT(n_id) AS xrefs " .
			" FROM `##sources`" .
			" JOIN `##name` ON s_id = n_id AND s_file = n_file" .
			" WHERE s_file = :tree_id" .
			" GROUP BY n_full" .
			" HAVING COUNT(n_id) > 1"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$sources = array_map(
			function (stdClass $x) use ($tree) {
				$tmp = explode(',', $x->xrefs);

				return array_map(function ($y) use ($tree) {
					return Source::getInstance($y, $tree);
				}, $tmp);
			}, $sources
		);

		$individuals = Database::prepare(
			"SELECT DISTINCT GROUP_CONCAT(d_gid ORDER BY d_gid) AS xrefs" .
			" FROM `##dates` AS d" .
			" JOIN `##name` ON d_file = n_file AND d_gid = n_id" .
			" WHERE d_file = :tree_id AND d_fact IN ('BIRT', 'CHR', 'BAPM', 'DEAT', 'BURI')" .
			" GROUP BY d_day, d_month, d_year, d_type, d_fact, n_type, n_full" .
			" HAVING COUNT(DISTINCT d_gid) > 1"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$individuals = array_map(
			function (stdClass $x) use ($tree) {
				$tmp = explode(',', $x->xrefs);

				return array_map(function ($y) use ($tree) {
					return Individual::getInstance($y, $tree);
				}, $tmp);
			}, $individuals
		);

		$families = Database::prepare(
			"SELECT GROUP_CONCAT(f_id) AS xrefs " .
			" FROM `##families`" .
			" WHERE f_file = :tree_id" .
			" GROUP BY LEAST(f_husb, f_wife), GREATEST(f_husb, f_wife)" .
			" HAVING COUNT(f_id) > 1"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$families = array_map(
			function (stdClass $x) use ($tree) {
				$tmp = explode(',', $x->xrefs);

				return array_map(function ($y) use ($tree) {
					return Family::getInstance($y, $tree);
				}, $tmp);
			}, $families
		);

		$media = Database::prepare(
			"SELECT GROUP_CONCAT(m_id) AS xrefs " .
			" FROM `##media`" .
			" JOIN `##media_file` USING (m_id, m_file)" .
			" WHERE m_file = :tree_id AND descriptive_title <> ''" .
			" GROUP BY descriptive_title" .
			" HAVING COUNT(m_id) > 1"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		$media = array_map(
			function (stdClass $x) use ($tree) {
				$tmp = explode(',', $x->xrefs);

				return array_map(function ($y) use ($tree) {
					return Media::getInstance($y, $tree);
				}, $tmp);
			}, $media
		);

		return [
			I18N::translate('Repositories')  => $repositories,
			I18N::translate('Sources')       => $sources,
			I18N::translate('Individuals')   => $individuals,
			I18N::translate('Families')      => $families,
			I18N::translate('Media objects') => $media,
		];
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
}
