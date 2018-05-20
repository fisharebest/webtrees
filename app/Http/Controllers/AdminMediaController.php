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
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * Controller for media administration.
 */
class AdminMediaController extends AbstractBaseController {
	// How many files to upload on one form.
	const MAX_UPLOAD_FILES = 10;

	protected $layout = 'layouts/administration';

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function index(Request $request): Response {
		$files        = $request->get('files', 'local'); // local|unused|external
		$media_folder = $request->get('media_folder', '');
		$media_path   = $request->get('media_path', '');
		$subfolders   = $request->get('subfolders', 'include'); // include/exclude

		$media_folders = $this->allMediaFolders();
		$media_paths   = $this->mediaPaths($media_folder);

		// Preserve the pagination/filtering/sorting between requests, so that the
		// browser’s back button works. Pagination is dependent on the currently
		// selected folder.
		$table_id = md5($files . $media_folder . $media_path . $subfolders);

		$title = I18N::translate('Manage media');

		return $this->viewResponse('admin/media', [
			'files'         => $files,
			'media_folder'  => $media_folder,
			'media_folders' => $media_folders,
			'media_path'    => $media_path,
			'media_paths'   => $media_paths,
			'subfolders'    => $subfolders,
			'table_id'      => $table_id,
			'title'         => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function delete(Request $request): Response {
		$delete_file  = $request->get('file', '');
		$media_folder = $request->get('folder', '');

		// Only delete valid (i.e. unused) media files
		$disk_files = $this->allDiskFiles($media_folder, '', 'include', '');

		// Check file exists? Maybe it was already deleted or renamed.
		if (in_array($delete_file, $disk_files)) {
			$tmp = WT_DATA_DIR . $media_folder . $delete_file;
			try {
				unlink($tmp);
				FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', Html::filename($tmp)), 'info');
			} catch (Throwable $ex) {
				DebugBar::addThrowable($ex);

				FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', Html::filename($tmp)) . '<hr><samp dir="ltr">' . $ex->getMessage() . '</samp>', 'danger');
			}
		}

		return new Response;
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function data(Request $request): JsonResponse {
		$files  = $request->get('files'); // local|external|unused
		$search = $request->get('search');
		$search = $search['value'];
		$start  = (int) $request->get('start');
		$length = (int) $request->get('length');

		// family tree setting MEDIA_DIRECTORY
		$media_folders = $this->allMediaFolders();
		$media_folder  = $request->get('media_folder', '');
		// User folders may contain special characters. Restrict to actual folders.
		if (!array_key_exists($media_folder, $media_folders)) {
			$media_folder = reset($media_folders);
		}

		// prefix to filename
		$media_paths = $this->mediaPaths($media_folder);
		$media_path  = $request->get('media_path', '');
		// User paths may contain special characters. Restrict to actual paths.
		if (!array_key_exists($media_path, $media_paths)) {
			$media_path = reset($media_paths);
		}

		// subfolders within $media_path
		$subfolders = $request->get('subfolders'); // include|exclude

		switch ($files) {
			case 'local':
				// Filtered rows
				$SELECT1 =
					"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS TRIM(LEADING :media_path_1 FROM multimedia_file_refn) AS media_path, m_id AS xref, descriptive_title, m_file AS gedcom_id, m_gedcom AS gedcom" .
					" FROM  `##media`" .
					" JOIN  `##media_file` USING (m_file, m_id)" .
					" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
					" JOIN  `##gedcom` USING (gedcom_id)" .
					" WHERE setting_value = :media_folder" .
					" AND   multimedia_file_refn LIKE CONCAT(:media_path_2, '%')" .
					" AND   (SUBSTRING_INDEX(multimedia_file_refn, '/', -1) LIKE CONCAT('%', :search_1, '%')" .
					"  OR   descriptive_title LIKE CONCAT('%', :search_2, '%'))" .
					" AND   multimedia_file_refn NOT LIKE 'http://%'" .
					" AND   multimedia_file_refn NOT LIKE 'https://%'";
				$ARGS1   = [
					'media_path_1' => $media_path,
					'media_folder' => $media_folder,
					'media_path_2' => Database::escapeLike($media_path),
					'search_1'     => Database::escapeLike($search),
					'search_2'     => Database::escapeLike($search),
				];
				// Unfiltered rows
				$SELECT2 =
					"SELECT SQL_CACHE COUNT(*)" .
					" FROM  `##media`" .
					" JOIN  `##media_file` USING (m_file, m_id)" .
					" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
					" WHERE setting_value = :media_folder" .
					" AND   multimedia_file_refn LIKE CONCAT(:media_path_3, '%')" .
					" AND   multimedia_file_refn NOT LIKE 'http://%'" .
					" AND   multimedia_file_refn NOT LIKE 'https://%'";
				$ARGS2   = [
					'media_folder' => $media_folder,
					'media_path_3' => $media_path,
				];

				if ($subfolders == 'exclude') {
					$SELECT1               .= " AND multimedia_file_refn NOT LIKE CONCAT(:media_path_4, '%/%')";
					$ARGS1['media_path_4'] = Database::escapeLike($media_path);
					$SELECT2               .= " AND multimedia_file_refn NOT LIKE CONCAT(:media_path_4, '%/%')";
					$ARGS2['media_path_4'] = Database::escapeLike($media_path);
				}

				$order   = $request->get('order', []);
				$SELECT1 .= " ORDER BY ";
				if ($order) {
					foreach ($order as $key => $value) {
						if ($key > 0) {
							$SELECT1 .= ',';
						}
						// Columns in datatables are numbered from zero.
						// Columns in MySQL are numbered starting with one.
						switch ($value['dir']) {
							case 'asc':
								$SELECT1 .= ":col_" . $key . " ASC";
								break;
							case 'desc':
								$SELECT1 .= ":col_" . $key . " DESC";
								break;
						}
						$ARGS1['col_' . $key] = 1 + $value['column'];
					}
				} else {
					$SELECT1 = " 1 ASC";
				}

				if ($length > 0) {
					$SELECT1         .= " LIMIT :length OFFSET :start";
					$ARGS1['length'] = $length;
					$ARGS1['start']  = $start;
				}

				$rows = Database::prepare($SELECT1)->execute($ARGS1)->fetchAll();
				// Total filtered/unfiltered rows
				$recordsFiltered = Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
				$recordsTotal    = Database::prepare($SELECT2)->execute($ARGS2)->fetchOne();

				$data = [];
				foreach ($rows as $row) {
					$media       = Media::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
					$media_files = $media->mediaFiles();
					$media_files = array_map(function (MediaFile $media_file) {
						return $media_file->displayImage(150, 150, '', []);
					}, $media_files);
					$data[]      = [
						$this->mediaFileInfo($media_folder, $media_path, $row->media_path),
						implode('', $media_files),
						$this->mediaObjectInfo($media),
					];
				}
				break;

			case 'external':
				// Filtered rows
				$SELECT1 =
					"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS multimedia_file_refn, m_id AS xref, descriptive_title, m_file AS gedcom_id, m_gedcom AS gedcom" .
					" FROM  `##media`" .
					" JOIN  `##media_file` USING (m_id, m_file)" .
					" WHERE (multimedia_file_refn LIKE 'http://%' OR multimedia_file_refn LIKE 'https://%')" .
					" AND   (multimedia_file_refn LIKE CONCAT('%', :search_1, '%') OR descriptive_title LIKE CONCAT('%', :search_2, '%'))";
				$ARGS1   = [
					'search_1' => Database::escapeLike($search),
					'search_2' => Database::escapeLike($search),
				];
				// Unfiltered rows
				$SELECT2 =
					"SELECT SQL_CACHE COUNT(*)" .
					" FROM  `##media`" .
					" JOIN  `##media_file` USING (m_id, m_file)" .
					" WHERE (multimedia_file_refn LIKE 'http://%' OR multimedia_file_refn LIKE 'https://%')";
				$ARGS2   = [];

				$order   = $request->get('order', []);
				$SELECT1 .= " ORDER BY ";
				if ($order) {
					foreach ($order as $key => $value) {
						if ($key > 0) {
							$SELECT1 .= ',';
						}
						// Columns in datatables are numbered from zero.
						// Columns in MySQL are numbered starting with one.
						switch ($value['dir']) {
							case 'asc':
								$SELECT1 .= ":col_" . $key . " ASC";
								break;
							case 'desc':
								$SELECT1 .= ":col_" . $key . " DESC";
								break;
						}
						$ARGS1['col_' . $key] = 1 + $value['column'];
					}
				} else {
					$SELECT1 = " 1 ASC";
				}

				if ($length > 0) {
					$SELECT1         .= " LIMIT :length OFFSET :start";
					$ARGS1['length'] = $length;
					$ARGS1['start']  = $start;
				}

				$rows = Database::prepare($SELECT1)->execute($ARGS1)->fetchAll();

				// Total filtered/unfiltered rows
				$recordsFiltered = Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
				$recordsTotal    = Database::prepare($SELECT2)->execute($ARGS2)->fetchOne();

				$data = [];
				foreach ($rows as $row) {
					$media  = Media::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
					$data[] = [
						GedcomTag::getLabelValue('URL', $row->multimedia_file_refn),
						$media->displayImage(150, 150, '', []),
						$this->mediaObjectInfo($media),
					];
				}
				break;

			case 'unused':
				// Which trees use this media folder?
				$media_trees = Database::prepare(
					"SELECT gedcom_name, gedcom_name" .
					" FROM `##gedcom`" .
					" JOIN `##gedcom_setting` USING (gedcom_id)" .
					" WHERE setting_name='MEDIA_DIRECTORY' AND setting_value = :media_folder AND gedcom_id > 0"
				)->execute([
					'media_folder' => $media_folder,
				])->fetchAssoc();

				$disk_files = $this->allDiskFiles($media_folder, $media_path, $subfolders, $search);
				$db_files   = $this->allMediaFiles($media_folder, $media_path, $search);

				// All unused files
				$unused_files = array_diff($disk_files, $db_files);
				$recordsTotal = count($unused_files);

				// Filter unused files
				if ($search) {
					$unused_files = array_filter($unused_files, function ($x) use ($search) {
						return strpos($x, $search) !== false;
					});
				}
				$recordsFiltered = count($unused_files);

				// Sort files - only option is column 0
				sort($unused_files);
				$order = $request->get('order', []);
				if ($order && $order[0]['dir'] === 'desc') {
					$unused_files = array_reverse($unused_files);
				}

				// Paginate unused files
				$unused_files = array_slice($unused_files, $start, $length);

				$data = [];
				foreach ($unused_files as $unused_file) {
					$imgsize = getimagesize(WT_DATA_DIR . $media_folder . $media_path . $unused_file);
					// We can’t create a URL (not in public_html) or use the media firewall (no such object)
					if ($imgsize === false) {
						$img = '-';
					} else {
						$url = route('unused-media-thumbnail', ['folder' => $media_folder, 'file' => $media_path . $unused_file, 'w' => 100, 'h' => 100]);
						$img = '<img src="' . e($url) . '">';
					}

					// Is there a pending record for this file?
					$exists_pending = Database::prepare(
						"SELECT 1 FROM `##change` WHERE status='pending' AND new_gedcom LIKE CONCAT('%\n1 FILE ', :unused_file, '\n%')"
					)->execute([
						'unused_file' => Database::escapeLike($unused_file),
					])->fetchOne();

					// Form to create new media object in each tree
					$create_form = '';
					if (!$exists_pending) {
						foreach ($media_trees as $media_tree) {
							$create_form .=
								'<p><a href="#" data-toggle="modal" data-target="#modal-create-media-from-file" data-file="' . e($unused_file) . '" data-tree="' . e($media_tree) . '" onclick="document.getElementById(\'file\').value=this.dataset.file; document.getElementById(\'ged\').value=this.dataset.tree;">' . I18N::translate('Create') . '</a> — ' . e($media_tree) . '<p>';
						}
					}

					$delete_link = '<p><a data-confirm="' . I18N::translate('Are you sure you want to delete “%s”?', e($unused_file)) . '" data-url="' . e(route('admin-media-delete', ['file' => $media_path . $unused_file, 'folder' => $media_folder])) . '" onclick="if (confirm(this.dataset.confirm)) jQuery.post(this.dataset.url, function (){location.reload();})" href="#">' . I18N::translate('Delete') . '</a></p>';

					$data[] = [
						$this->mediaFileInfo($media_folder, $media_path, $unused_file) . $delete_link,
						$img,
						$create_form,
					];
				}
				break;

			default:
				throw new BadRequestHttpException;
		}

		// See http://www.datatables.net/usage/server-side
		return new JsonResponse([
			'draw'            => $request->get('draw'),
			'recordsTotal'    => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data'            => $data,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function upload(Request $request): Response {
		$media_folders = $this->folderListAll();

		$filesize = ini_get('upload_max_filesize');
		if (empty($filesize)) {
			$filesize = '2M';
		}

		$title = I18N::translate('Upload media files');

		return $this->viewResponse('admin/media-upload', [
			'max_upload_files' => self::MAX_UPLOAD_FILES,
			'filesize'         => $filesize,
			'media_folders'    => $media_folders,
			'title'            => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function uploadAction(Request $request): RedirectResponse {
		$all_folders = $this->folderListAll();

		for ($i = 1; $i < self::MAX_UPLOAD_FILES; $i++) {
			if (!empty($_FILES['mediafile' . $i]['name'])) {
				$folder   = $request->get('folder' . $i, '');
				$filename = $request->get('filename' . $i, '');

				// If no filename specified, use the original filename.
				if ($filename === '') {
					$filename = $_FILES['mediafile' . $i]['name'];
				}

				// Validate the folder
				if (!in_array($folder, $all_folders)) {
					break;
				}

				// Validate the filename.
				$filename = str_replace('\\', '/', $filename);
				$filename = trim($filename, '/');

				if (strpos('/' . $filename, '/../') !== false) {
					FlashMessages::addMessage('Folder names are not allowed to include “../”');
					continue;
				} elseif (preg_match('/([:])/', $filename, $match)) {
					// Local media files cannot contain certain special characters, especially on MS Windows
					FlashMessages::addMessage(I18N::translate('Filenames are not allowed to contain the character “%s”.', $match[1]));
					continue;
				} elseif (preg_match('/(\.(php|pl|cgi|bash|sh|bat|exe|com|htm|html|shtml))$/i', $filename, $match)) {
					// Do not allow obvious script files.
					FlashMessages::addMessage(I18N::translate('Filenames are not allowed to have the extension “%s”.', $match[1]));
					continue;
				}

				// The new filename may have created a new sub-folder.
				$full_path = WT_DATA_DIR . $folder . $filename;
				$folder    = dirname($full_path);

				// Make sure the media folder exists
				if (!is_dir($folder)) {
					if (File::mkdir($folder)) {
						FlashMessages::addMessage(I18N::translate('The folder %s has been created.', Html::filename($folder)), 'info');
					} else {
						FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename($folder)), 'danger');
						continue;
					}
				}

				if (file_exists($full_path)) {
					FlashMessages::addMessage(I18N::translate('The file %s already exists. Use another filename.', $full_path, 'error'));
					continue;
				}

				// Now copy the file to the correct location.
				if (move_uploaded_file($_FILES['mediafile' . $i]['tmp_name'], $full_path)) {
					FlashMessages::addMessage(I18N::translate('The file %s has been uploaded.', Html::filename($full_path)), 'success');
					Log::addMediaLog('Media file ' . $full_path . ' uploaded');
				} else {
					FlashMessages::addMessage(I18N::translate('There was an error uploading your file.') . '<br>' . Functions::fileUploadErrorText($_FILES['mediafile' . $i]['error']), 'danger');
				}
			}
		}

		$url = route('admin-media-upload');

		return new RedirectResponse($url);
	}

	/**
	 * Generate a list of all folders from all the trees.
	 *
	 * @return string[]
	 */
	private function folderListAll(): array {
		$folders = Database::prepare(
			"SELECT SQL_CACHE CONCAT(setting_value, LEFT(multimedia_file_refn, CHAR_LENGTH(multimedia_file_refn) - CHAR_LENGTH(SUBSTRING_INDEX(multimedia_file_refn, '/', -1))))" .
			" FROM  `##gedcom_setting` AS gs" .
			" JOIN  `##media_file` AS m ON m.m_file = gs.gedcom_id AND gs.setting_name = 'MEDIA_DIRECTORY'" .
			" WHERE multimedia_file_refn NOT LIKE 'http://%'" .
			" AND   multimedia_file_refn NOT LIKE 'https://%'" .
			" AND   gs.gedcom_id > 0" .
			" GROUP BY 1" .
			" UNION" .
			" SELECT setting_value FROM `##gedcom_setting` WHERE setting_name = 'MEDIA_DIRECTORY'" .
			" ORDER BY 1"
		)->execute()->fetchOneColumn();

		return $folders;
	}

	/**
	 * A unique list of media folders, from all trees.
	 *
	 * @return string[]
	 */
	private function allMediaFolders(): array {
		return Database::prepare(
			"SELECT SQL_CACHE setting_value, setting_value" .
			" FROM `##gedcom_setting`" .
			" WHERE setting_name='MEDIA_DIRECTORY' AND gedcom_id > 0" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute([])->fetchAssoc();
	}

	/**
	 * Generate a list of media paths (within a media folder) used by all media objects.
	 *
	 * @param string $media_folder
	 *
	 * @return string[]
	 */
	private function mediaPaths(string $media_folder): array {
		$media_paths = Database::prepare(
			"SELECT SQL_CACHE LEFT(multimedia_file_refn, CHAR_LENGTH(multimedia_file_refn) - CHAR_LENGTH(SUBSTRING_INDEX(multimedia_file_refn, '/', -1))) AS media_path" .
			" FROM  `##media`" .
			" JOIN  `##media_file` USING (m_file, m_id)" .
			" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
			" WHERE setting_value = :media_folder" .
			" AND   multimedia_file_refn NOT LIKE 'http://%'" .
			" AND   multimedia_file_refn NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute([
			'media_folder' => $media_folder,
		])->fetchOneColumn();

		if (empty($media_paths) || $media_paths[0] !== '') {
			// Always include a (possibly empty) top-level folder
			array_unshift($media_paths, '');
		}

		return array_combine($media_paths, $media_paths);
	}

	/**
	 * Search a folder (and optional subfolders) for filenames that match a search pattern.
	 *
	 * @param string $dir
	 * @param bool   $recursive
	 * @param string $filter
	 *
	 * @return string[]
	 */
	private function scanFolders(string $dir, bool $recursive, string $filter): array {
		$files = [];

		// $dir comes from the database. The actual folder may not exist.
		if (is_dir($dir)) {
			foreach (scandir($dir) as $path) {
				if (is_dir($dir . $path)) {
					// What if there are user-defined subfolders “thumbs” or “watermarks”?
					if ($path != '.' && $path != '..' && $path != 'thumbs' && $path != 'watermark' && $recursive) {
						foreach ($this->scanFolders($dir . $path . '/', $recursive, $filter) as $subpath) {
							$files[] = $path . '/' . $subpath;
						}
					}
				} elseif (!$filter || stripos($path, $filter) !== false) {
					$files[] = $path;
				}
			}
		}

		return $files;
	}

	/**
	 * Fetch a list of all files on disk
	 *
	 * @param string $media_folder Location of root folder
	 * @param string $media_path   Any subfolder
	 * @param string $subfolders   Include or exclude subfolders
	 * @param string $filter       Filter files whose name contains this test
	 *
	 * @return string[]
	 */
	private function allDiskFiles(string $media_folder, string $media_path, string $subfolders, string $filter): array {
		return $this->scanFolders(WT_DATA_DIR . $media_folder . $media_path, $subfolders == 'include', $filter);
	}

	/**
	 * Fetch a list of all files on in the database.
	 *
	 * @param string $media_folder
	 * @param string $media_path
	 * @param string $filter
	 *
	 * @return string[]
	 */
	private function allMediaFiles(string $media_folder, string $media_path, string $filter): array {
		return Database::prepare(
			"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS TRIM(LEADING :media_path_1 FROM multimedia_file_refn) AS media_path, 'OBJE' AS type, descriptive_title, m_id AS xref, m_file AS ged_id, m_gedcom AS gedrec, multimedia_file_refn" .
			" FROM  `##media`" .
			" JOIN  `##media_file` USING (m_file, m_id)" .
			" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
			" JOIN  `##gedcom`         USING (gedcom_id)" .
			" WHERE setting_value = :media_folder" .
			" AND   multimedia_file_refn LIKE CONCAT(:media_path_2, '%')" .
			" AND   (SUBSTRING_INDEX(multimedia_file_refn, '/', -1) LIKE CONCAT('%', :filter_1, '%')" .
			"  OR   descriptive_title LIKE CONCAT('%', :filter_2, '%'))" .
			" AND   multimedia_file_refn NOT LIKE 'http://%'" .
			" AND   multimedia_file_refn NOT LIKE 'https://%'"
		)->execute([
			'media_path_1' => $media_path,
			'media_folder' => $media_folder,
			'media_path_2' => Database::escapeLike($media_path),
			'filter_1'     => Database::escapeLike($filter),
			'filter_2'     => Database::escapeLike($filter),
		])->fetchOneColumn();
	}

	/**
	 * Generate some useful information and links about a media file.
	 *
	 * @param string $media_folder
	 * @param string $media_path
	 * @param string $file
	 *
	 * @return string
	 */
	private function mediaFileInfo(string $media_folder, string $media_path, string $file): string {
		$html = '<dl>';
		$html .= '<dt>' . I18N::translate('Filename') . '</dt>';
		$html .= '<dd>' . e($file) . '</dd>';

		$full_path = WT_DATA_DIR . $media_folder . $media_path . $file;
		try {
			$size = filesize($full_path);
			$size = (int) (($size + 1023) / 1024); // Round up to next KB
			$size = /* I18N: size of file in KB */
				I18N::translate('%s KB', I18N::number($size));
			$html .= '<dt>' . I18N::translate('File size') . '</dt>';
			$html .= '<dd>' . $size . '</dd>';

			try {
				$imgsize = getimagesize($full_path);
				$html    .= '<dt>' . I18N::translate('Image dimensions') . '</dt>';
				$html    .= '<dd>' . /* I18N: image dimensions, width × height */
					I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1'])) . '</dd>';
			} catch (Throwable $ex) {
				DebugBar::addThrowable($ex);

				// Not an image, or not a valid image?
			}

			$html .= '</dl>';
		} catch (Throwable $ex) {
			DebugBar::addThrowable($ex);

			// Not a file?  Not an image?
		}

		return $html;
	}

	/**
	 * Generate some useful information and links about a media object.
	 *
	 * @param Media $media
	 *
	 * @return string HTML
	 */
	private function mediaObjectInfo(Media $media) {
		$html = '<b><a href="' . e($media->url()) . '">' . $media->getFullName() . '</a></b>' . '<br><i>' . e($media->getNote()) . '</i></br><br>';

		$linked = [];
		foreach ($media->linkedIndividuals('OBJE') as $link) {
			$linked[] = '<a href="' . e($link->url()) . '">' . $link->getFullName() . '</a>';
		}
		foreach ($media->linkedFamilies('OBJE') as $link) {
			$linked[] = '<a href="' . e($link->url()) . '">' . $link->getFullName() . '</a>';
		}
		foreach ($media->linkedSources('OBJE') as $link) {
			$linked[] = '<a href="' . e($link->url()) . '">' . $link->getFullName() . '</a>';
		}
		foreach ($media->linkedNotes('OBJE') as $link) {
			// Invalid GEDCOM - you cannot link a NOTE to an OBJE
			$linked[] = '<a href="' . e($link->url()) . '">' . $link->getFullName() . '</a>';
		}
		foreach ($media->linkedRepositories('OBJE') as $link) {
			// Invalid GEDCOM - you cannot link a REPO to an OBJE
			$linked[] = '<a href="' . e($link->url()) . '">' . $link->getFullName() . '</a>';
		}
		if (!empty($linked)) {
			$html .= '<ul>';
			foreach ($linked as $link) {
				$html .= '<li>' . $link . '</li>';
			}
			$html .= '</ul>';
		} else {
			$html .= '<div class="alert alert-danger">' . I18N::translate('There are no links to this media object.') . '</div>';
		}

		return $html;
	}
}
