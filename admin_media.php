<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees;

use ErrorException;
use Fisharebest\Webtrees\Controller\AjaxController;
use Fisharebest\Webtrees\Controller\PageController;

// @TODO MediaFile
require 'includes/session.php';

// type of file/object to include
$files = Filter::get('files', 'local|external|unused', 'local');

// family tree setting MEDIA_DIRECTORY
$media_folders = all_media_folders();
$media_folder  = Filter::get('media_folder', null, ''); // MySQL needs an empty string, not NULL
// User folders may contain special characters. Restrict to actual folders.
if (!array_key_exists($media_folder, $media_folders)) {
	$media_folder = reset($media_folders);
}

// prefix to filename
$media_paths = media_paths($media_folder);
$media_path  = Filter::get('media_path', null, ''); // MySQL needs an empty string, not NULL
// User paths may contain special characters. Restrict to actual paths.
if (!array_key_exists($media_path, $media_paths)) {
	$media_path = reset($media_paths);
}

// subfolders within $media_path
$subfolders = Filter::get('subfolders', 'include|exclude', 'include');
$action     = Filter::get('action');

////////////////////////////////////////////////////////////////////////////////
// POST callback for file deletion
////////////////////////////////////////////////////////////////////////////////
$delete_file = Filter::post('delete');
if ($delete_file) {
	$controller = new AjaxController;
	// Only delete valid (i.e. unused) media files
	$media_folder = Filter::post('media_folder');
	$disk_files   = all_disk_files($media_folder, '', 'include', '');
	// Check file exists? Maybe it was already deleted or renamed.
	if (in_array($delete_file, $disk_files)) {
		$tmp = WT_DATA_DIR . $media_folder . $delete_file;
		try {
			unlink($tmp);
			FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', Html::filename($tmp)), 'success');
		} catch (ErrorException $ex) {
			DebugBar::addThrowable($ex);

			FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', Html::filename($tmp)) . '<hr><samp dir="ltr">' . $ex->getMessage() . '</samp>', 'danger');
		}
		// Delete any corresponding thumbnail
		$tmp = WT_DATA_DIR . $media_folder . 'thumbs/' . $delete_file;
		if (file_exists($tmp)) {
			try {
				unlink($tmp);
				FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', Html::filename($tmp)), 'success');
			} catch (ErrorException $ex) {
				DebugBar::addThrowable($ex);

				FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', Html::filename($tmp)) . '<hr><samp dir="ltr">' . $ex->getMessage() . '</samp>', 'danger');
			}
		}
	}
	$controller->pageHeader();

	return;
}

////////////////////////////////////////////////////////////////////////////////
// GET callback for server-side pagination
////////////////////////////////////////////////////////////////////////////////

switch ($action) {
	case 'load_json':
		$search = Filter::get('search');
		$search = $search['value'];
		$start  = Filter::getInteger('start');
		$length = Filter::getInteger('length');

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
				$ARGS1 = [
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
				$ARGS2 = [
					'media_folder' => $media_folder,
					'media_path_3' => $media_path,
				];

				if ($subfolders == 'exclude') {
					$SELECT1 .= " AND multimedia_file_refn NOT LIKE CONCAT(:media_path_4, '%/%')";
					$ARGS1['media_path_4'] = Database::escapeLike($media_path);
					$SELECT2 .= " AND multimedia_file_refn NOT LIKE CONCAT(:media_path_4, '%/%')";
					$ARGS2['media_path_4'] = Database::escapeLike($media_path);
				}

				$order = Filter::getArray('order');
				$SELECT1 .= " ORDER BY ";
				if ($order) {
					foreach ($order as $key => $value) {
						if ($key > 0) {
							$SELECT1 .= ',';
						}
						// Datatables numbers columns 0, 1, 2
						// MySQL numbers columns 1, 2, 3
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
				$SELECT1 .= " LIMIT :length OFFSET :start";
				$ARGS1['length'] = $length;
				$ARGS1['start']  = $start;
			}

			$rows = Database::prepare($SELECT1)->execute($ARGS1)->fetchAll();
			// Total filtered/unfiltered rows
			$recordsFiltered = Database::prepare("SELECT FOUND_ROWS()")->fetchOne();
			$recordsTotal    = Database::prepare($SELECT2)->execute($ARGS2)->fetchOne();

			$data = [];
			foreach ($rows as $row) {
				$media = Media::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
				$media_files = $media->mediaFiles();
				$media_files = array_map(function (MediaFile $media_file) {
					return $media_file->displayImage(150, 150, '', []);
				}, $media_files);
				$data[] = [
					mediaFileInfo($media_folder, $media_path, $row->media_path),
					implode('', $media_files),
					mediaObjectInfo($media),
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
		$ARGS1 = [
			'search_1' => Database::escapeLike($search),
			'search_2' => Database::escapeLike($search),
		];
		// Unfiltered rows
		$SELECT2 =
			"SELECT SQL_CACHE COUNT(*)" .
			" FROM  `##media`" .
			" JOIN  `##media_file` USING (m_id, m_file)" .
			" WHERE (multimedia_file_refn LIKE 'http://%' OR multimedia_file_refn LIKE 'https://%')";
		$ARGS2 = [];

		$order = Filter::getArray('order');
		$SELECT1 .= " ORDER BY ";
		if ($order) {
			foreach ($order as $key => $value) {
				if ($key > 0) {
					$SELECT1 .= ',';
				}
				// Datatables numbers columns 0, 1, 2
				// MySQL numbers columns 1, 2, 3
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
			$SELECT1 .= " LIMIT :length OFFSET :start";
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
				mediaObjectInfo($media),
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

		$disk_files = all_disk_files($media_folder, $media_path, $subfolders, $search);
		$db_files   = all_media_files($media_folder, $media_path, $subfolders, $search);

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
		$order = Filter::get('order');
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
						'<p><a href="#" data-toggle="modal" data-target="#modal-create-media-from-file" data-file="' . Html::escape($unused_file) . '" data-tree="' . Html::escape($media_tree) . '" onclick="document.getElementById(\'file\').value=this.dataset.file; document.getElementById(\'ged\').value=this.dataset.tree;">' . I18N::translate('Create') . '</a> — ' . Html::escape($media_tree) . '<p>';
				}
			}

			$delete_link = '<p><a data-confirm="' . I18N::translate('Are you sure you want to delete “%s”?', Html::escape($unused_file)) . '" data-file="' . Html::escape($media_path . $unused_file) . '" data-folder="' . Html::escape($media_folder) . '" onclick="if (confirm(this.dataset.confirm)) jQuery.post(\'admin_media.php\',{delete: this.dataset.file, media_folder: this.dataset.folder},function(){location.reload();})" href="#">' . I18N::translate('Delete') . '</a></p>';

			$data[] = [
				mediaFileInfo($media_folder, $media_path, $unused_file) . $delete_link,
				$img,
				$create_form,
			];
		}
		break;

	default:
		throw new \DomainException('Invalid action');
	}

	header('Content-type: application/json');
	// See http://www.datatables.net/usage/server-side
	echo json_encode([
		'draw'            => Filter::getInteger('draw'), // String, but always an integer
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data,
	]);

	return;
}

/**
 * A unique list of media folders, from all trees.
 *
 * @return string[]
 */
function all_media_folders() {
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
function media_paths($media_folder) {
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
function scan_dirs($dir, $recursive, $filter) {
	$files = [];

	// $dir comes from the database. The actual folder may not exist.
	if (is_dir($dir)) {
		foreach (scandir($dir) as $path) {
			if (is_dir($dir . $path)) {
				// What if there are user-defined subfolders “thumbs” or “watermarks”?
				if ($path != '.' && $path != '..' && $path != 'thumbs' && $path != 'watermark' && $recursive) {
					foreach (scan_dirs($dir . $path . '/', $recursive, $filter) as $subpath) {
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
function all_disk_files($media_folder, $media_path, $subfolders, $filter) {
	return scan_dirs(WT_DATA_DIR . $media_folder . $media_path, $subfolders == 'include', $filter);
}

/**
 * Fetch a list of all files on in the database.
 *
 * The subfolders parameter is not implemented. However, as we
 * currently use this function as an exclusion list, it is harmless
 * to always include sub-folders.
 *
 * @param string $media_folder
 * @param string $media_path
 * @param string $subfolders
 * @param string $filter
 *
 * @return string[]
 */
function all_media_files($media_folder, $media_path, $subfolders, $filter) {
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
function mediaFileInfo($media_folder, $media_path, $file) {
	$html = '<dl>';
	$html .= '<dt>' . I18N::translate('Filename') . '</dt>';
	$html .= '<dd>' . Html::escape($file) . '</dd>';

	$full_path = WT_DATA_DIR . $media_folder . $media_path . $file;
	try {
		$size = filesize($full_path);
		$size = (int) (($size + 1023) / 1024); // Round up to next KB
		$size = /* I18N: size of file in KB */ I18N::translate('%s KB', I18N::number($size));
		$html .= '<dt>' . I18N::translate('File size') . '</dt>';
		$html .= '<dd>' . $size . '</dd>';

		try {
			$imgsize = getimagesize($full_path);
			$html .= '<dt>' . I18N::translate('Image dimensions') . '</dt>';
			$html .= '<dd>' . /* I18N: image dimensions, width × height */
				I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1'])) . '</dd>';
		} catch (ErrorException $ex) {
			DebugBar::addThrowable($ex);

			// Not an image, or not a valid image?
		}

		$html .= '</dl>';
	} catch (ErrorException $ex) {
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
function mediaObjectInfo(Media $media) {
	$html = '<b><a href="' . $media->getHtmlUrl() . '">' . $media->getFullName() . '</a></b>' . '<br><i>' . Html::escape($media->getNote()) . '</i></br><br>';

	$linked = [];
	foreach ($media->linkedIndividuals('OBJE') as $link) {
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedFamilies('OBJE') as $link) {
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedSources('OBJE') as $link) {
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedNotes('OBJE') as $link) {
		// Invalid GEDCOM - you cannot link a NOTE to an OBJE
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedRepositories('OBJE') as $link) {
		// Invalid GEDCOM - you cannot link a REPO to an OBJE
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
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

////////////////////////////////////////////////////////////////////////////////
// Start here
////////////////////////////////////////////////////////////////////////////////

// Preserve the pagination/filtering/sorting between requests, so that the
// browser’s back button works. Pagination is dependent on the currently
// selected folder.
$table_id = md5($files . $media_folder . $media_path . $subfolders);

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(I18N::translate('Manage media'))
	->pageHeader()
	->addInlineJavascript('
	$("#media-table-' . $table_id . '").dataTable({
		processing: true,
		serverSide: true,
		ajax: "admin_media.php?action=load_json&files=' . $files . '&media_folder=' . $media_folder . '&media_path=' . $media_path . '&subfolders=' . $subfolders . '",
		' . I18N::datatablesI18N([5, 10, 20, 50, 100, 500, 1000, -1]) . ',
		autoWidth:false,
		pageLength: 10,
		pagingType: "full_numbers",
		stateSave: true,
		stateDuration: 300,
		columns: [
			{},
			{ sortable: false },
			{ sortable: ' . ($files === 'unused' ? 'false' : 'true') . ' }
		]
	});
	');

echo Bootstrap4::breadcrumbs([
	route('admin-control-panel') => I18N::translate('Control panel'),
], $controller->getPageTitle());
?>

<h1><?= $controller->getPageTitle() ?></h1>

<form>
	<table class="table table-bordered table-sm">
		<thead>
			<tr>
				<th><?= I18N::translate('Media files') ?></th>
				<th><?= I18N::translate('Media folders') ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<label>
						<input type="radio" name="files" value="local" <?= $files === 'local' ? 'checked' : '' ?> onchange="this.form.submit();">
						<?= /* I18N: “Local files” are stored on this computer */ I18N::translate('Local files') ?>
					</label>
					<br>
					<label>
						<input type="radio" name="files" value="external" <?= $files === 'external' ? 'checked' : '' ?> onchange="this.form.submit();">
						<?= /* I18N: “External files” are stored on other computers */ I18N::translate('External files') ?>
					</label>
					<br>
					<label>
						<input type="radio" name="files" value="unused" <?= $files === 'unused' ? 'checked' : '' ?> onchange="this.form.submit();">
						<?= I18N::translate('Unused files') ?>
					</label>
				</td>
				<td>
					<?php if ($files === 'local' || $files === 'unused'): ?>

					<div dir="ltr" class="form-inline">
						<?php if (count($media_folders) > 1): ?>
						<?= WT_DATA_DIR . Bootstrap4::select($media_folders, $media_folder, ['name' => 'media_folder', 'onchange' => 'this.form.submit();']) ?>
						<?php else: ?>
						<?= WT_DATA_DIR . Html::escape($media_folder) ?>
						<input type="hidden" name="media_folder" value="<?= Html::escape($media_folder) ?>">
						<?php endif ?>
					</div>

					<?php if (count($media_paths) > 1): ?>
					<?= Bootstrap4::select($media_paths, $media_path, ['name' => 'media_path', 'onchange' => 'this.form.submit();']) ?>
					<?php else: ?>
					<?= Html::escape($media_path) ?>
					<input type="hidden" name="media_path" value="<?= Html::escape($media_path) ?>">
					<?php endif ?>

					<label>
						<input type="radio" name="subfolders" value="include" <?= $subfolders === 'include' ? 'checked' : '' ?> onchange="this.form.submit();">
						<?= I18N::translate('Include subfolders') ?>
					</label>
					<br>
					<label>
						<input type="radio" name="subfolders" value="exclude" <?= $subfolders === 'exclude' ? ' checked' : '' ?> onchange="this.form.submit();">
						<?= I18N::translate('Exclude subfolders') ?>
					</label>

					<?php elseif ($files === 'external'): ?>

					<?= I18N::translate('External media files have a URL instead of a filename.') ?>
					<input type="hidden" name="media_folder" value="<?= Html::escape($media_folder) ?>">
					<input type="hidden" name="media_path" value="<?= Html::escape($media_path) ?>">

					<?php endif ?>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<br>
<br>
<table class="table table-bordered table-sm" id="media-table-<?= $table_id ?>">
	<thead>
		<tr>
			<th><?= I18N::translate('Media file') ?></th>
			<th><?= I18N::translate('Media') ?></th>
			<th><?= I18N::translate('Media object') ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?= View::make('modals/create-media-from-file') ?>
