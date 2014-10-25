<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

define('WT_SCRIPT_NAME', 'admin_media.php');
require './includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

// type of file/object to include
$files = WT_Filter::get('files', 'local|external|unused', 'local');

// family tree setting MEDIA_DIRECTORY
$media_folders = all_media_folders();
$media_folder  = WT_Filter::get('media_folder', null, ''); // MySQL needs an empty string, not NULL
// User folders may contain special characters.  Restrict to actual folders.
if (!array_key_exists($media_folder, $media_folders)) {
	$media_folder = reset($media_folders);
}

// prefix to filename
$media_paths = media_paths($media_folder);
$media_path  = WT_Filter::get('media_path', null, ''); // MySQL needs an empty string, not NULL
// User paths may contain special characters.  Restrict to actual paths.
if (!array_key_exists($media_path, $media_paths)) {
	$media_path = reset($media_paths);
}

// subfolders within $media_path
$subfolders = WT_Filter::get('subfolders', 'include|exclude', 'include');
$action     = WT_Filter::get('action');

////////////////////////////////////////////////////////////////////////////////
// POST callback for file deletion
////////////////////////////////////////////////////////////////////////////////
$delete_file = WT_Filter::post('delete');
if ($delete_file) {
	$controller = new WT_Controller_Ajax;
	// Only delete valid (i.e. unused) media files
	$media_folder = WT_Filter::post('media_folder', null, ''); // MySQL needs an empty string, not NULL
	$disk_files = all_disk_files ($media_folder, '', 'include', '');
	if (in_array($delete_file, $disk_files)) {
		$tmp = WT_DATA_DIR . $media_folder . $delete_file;
		if (@unlink($tmp)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('The file %s was deleted.', $tmp));
		} else {
			WT_FlashMessages::addMessage(WT_I18N::translate('The file %s could not be deleted.', $tmp));
		}
		$tmp = WT_DATA_DIR . $media_folder . 'thumbs/' . $delete_file;
		if (file_exists($tmp)) {
			if (@unlink($tmp)) {
				WT_FlashMessages::addMessage(WT_I18N::translate('The file %s was deleted.', $tmp));
			} else {
				WT_FlashMessages::addMessage(WT_I18N::translate('The file %s could not be deleted.', $tmp));
			}
		}
	} else {
		// File no longer exists?  Maybe it was already deleted or renamed.
	}
	$controller->pageHeader();
	exit;
}

////////////////////////////////////////////////////////////////////////////////
// GET callback for server-side pagination
////////////////////////////////////////////////////////////////////////////////

switch($action) {
case 'load_json':
	Zend_Session::writeClose();
	$search = WT_Filter::get('search');
	$search = $search['value'];
	$start  = WT_Filter::getInteger('start');
	$length = WT_Filter::getInteger('length');

	switch ($files) {
	case 'local':
		// Filtered rows
		$SELECT1 =
				"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS TRIM(LEADING ? FROM m_filename) AS media_path, m_id AS xref, m_titl, m_file AS gedcom_id, m_gedcom AS gedcom" .
				" FROM  `##media`" .
				" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
				" JOIN  `##gedcom` USING (gedcom_id)" .
				" WHERE setting_value=?" .
				" AND   m_filename LIKE CONCAT(?, '%')" .
				" AND   (SUBSTRING_INDEX(m_filename, '/', -1) LIKE CONCAT('%', ?, '%')" .
				"  OR   m_titl LIKE CONCAT('%', ?, '%'))" .
				" AND   m_filename NOT LIKE 'http://%'" .
				" AND   m_filename NOT LIKE 'https://%'";
		$ARGS1 = array(
			$media_path,
			$media_folder,
			WT_Filter::escapeLike($media_path),
			WT_Filter::escapeLike($search),
			WT_Filter::escapeLike($search)
		);
		// Unfiltered rows
		$SELECT2 =
				"SELECT SQL_CACHE COUNT(*)" .
				" FROM  `##media`" .
				" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
				" WHERE setting_value=?" .
				" AND   m_filename LIKE CONCAT(?, '%')" .
				" AND   m_filename NOT LIKE 'http://%'" .
				" AND   m_filename NOT LIKE 'https://%'";
		$ARGS2 = array(
			$media_folder,
			$media_path
		);

		if ($subfolders=='exclude') {
			$SELECT1 .= " AND m_filename NOT LIKE CONCAT(?, '%/%')";
			$ARGS1[] = WT_Filter::escapeLike($media_path);
			$SELECT2 .= " AND m_filename NOT LIKE CONCAT(?, '%/%')";
			$ARGS2[] = WT_Filter::escapeLike($media_path);
		}

		if ($length > 0) {
			$LIMIT = " LIMIT " . $start . ',' . $length;
		} else {
			$LIMIT = "";
		}
		$order = WT_Filter::getArray('order');
		if ($order) {
			$ORDER_BY = " ORDER BY ";
			foreach ($order as $key => $value) {
				if ($key > 0) {
					$ORDER_BY .= ',';
				}
				// Datatables numbers columns 0, 1, 2, ...
				// MySQL numbers columns 1, 2, 3, ...
				switch ($value['dir']) {
				case 'asc':
					$ORDER_BY .= (1 + $value['column']) . ' ASC ';
					break;
				case 'desc':
					$ORDER_BY .= (1 + $value['column']) . ' DESC ';
					break;
				}
			}
		} else {
			$ORDER_BY = " ORDER BY 1 ASC";
		}

		$rows = WT_DB::prepare($SELECT1.$ORDER_BY.$LIMIT)->execute($ARGS1)->fetchAll();
		// Total filtered/unfiltered rows
		$recordsFiltered = WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
		$recordsTotal    = WT_DB::prepare($SELECT2)->execute($ARGS2)->fetchOne();

		$data = array();
		foreach ($rows as $row) {
			$media = WT_Media::getInstance($row->xref, $row->gedcom_id);
			$data[] = array(
				mediaFileInfo($media_folder, $media_path, $row->media_path),
				$media->displayImage(),
				mediaObjectInfo($media),
			);
		}
		break;

	case 'external':
		// Filtered rows
		$SELECT1 =
				"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS m_filename, m_id AS xref, m_titl, m_file AS gedcom_id, m_gedcom AS gedcom" .
				" FROM  `##media`" .
				" WHERE (m_filename LIKE 'http://%' OR m_filename LIKE 'https://%')" .
				" AND   (m_filename LIKE CONCAT('%', ?, '%') OR m_titl LIKE CONCAT('%', ?, '%'))";
		$ARGS1 = array(
			WT_Filter::escapeLike($search),
			WT_Filter::escapeLike($search)
		);
		// Unfiltered rows
		$SELECT2 =
				"SELECT SQL_CACHE COUNT(*)" .
				" FROM  `##media`" .
				" WHERE (m_filename LIKE 'http://%' OR m_filename LIKE 'https://%')";
		$ARGS2 = array();

		if ($length>0) {
			$LIMIT = " LIMIT " . $start . ',' . $length;
		} else {
			$LIMIT = "";
		}
		$order = WT_Filter::getArray('order');
		if ($order) {
			$ORDER_BY = " ORDER BY ";
			foreach ($order as $key => $value) {
				if ($key > 0) {
					$ORDER_BY .= ',';
				}
				// Datatables numbers columns 0, 1, 2, ...
				// MySQL numbers columns 1, 2, 3, ...
				switch ($value['dir']) {
				case 'asc':
					$ORDER_BY .= (1 + $value['column']).' ASC ';
					break;
				case 'desc':
					$ORDER_BY .= (1 + $value['column']).' DESC ';
					break;
				}
			}
		} else {
			$ORDER_BY = " ORDER BY 1 ASC";
		}

		$rows = WT_DB::prepare($SELECT1.$ORDER_BY.$LIMIT)->execute($ARGS1)->fetchAll();

		// Total filtered/unfiltered rows
		$recordsFiltered = WT_DB::prepare("SELECT FOUND_ROWS()")->fetchOne();
		$recordsTotal    = WT_DB::prepare($SELECT2)->execute($ARGS2)->fetchOne();

		$data = array();
		foreach ($rows as $row) {
			$media = WT_Media::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			$data[] = array(
			 	WT_Gedcom_Tag::getLabelValue('URL', $row->m_filename),
				$media->displayImage(),
				mediaObjectInfo($media),
			);
		}
		break;

	case 'unused':
		// Which trees use this media folder?
		$media_trees = WT_DB::prepare(
			"SELECT gedcom_name, gedcom_name" .
			" FROM `##gedcom`" .
			" JOIN `##gedcom_setting` USING (gedcom_id)" .
			" WHERE setting_name='MEDIA_DIRECTORY' AND setting_value=?"
		)->execute(array($media_folder))->fetchAssoc();

		$disk_files = all_disk_files ($media_folder, $media_path, $subfolders, $search);
		$db_files   = all_media_files($media_folder, $media_path, $subfolders, $search);

		// All unused files
		$unused_files  = array_diff($disk_files, $db_files);
		$recordsTotal = count($unused_files);

		// Filter unused files
		if ($search) {
			$unused_files = array_filter($unused_files, function ($x) use ($search) { return strpos($x, $search) !== false; });
		}
		$recordsFiltered = count($unused_files);

		// Sort files - only option is column 0
		sort($unused_files);
		$order = WT_Filter::get('order');
		if ($order && $order[0]['dir'] === 'desc') {
			$unused_files = array_reverse($unused_files);
		}

		// Paginate unused files
		$unused_files = array_slice($unused_files, $start, $length);

		$data = array();
		foreach ($unused_files as $unused_file) {
			$full_path  = WT_DATA_DIR . $media_folder .             $media_path . $unused_file;
			$thumb_path = WT_DATA_DIR . $media_folder . 'thumbs/' . $media_path . $unused_file;
			if (!file_exists($thumb_path)) {
				$thumb_path = $full_path;
			}

			$imgsize=@getimagesize($thumb_path);
			if ($imgsize && $imgsize[0] && $imgsize[1]) {
				// We can’t create a URL (not in public_html) or use the media firewall (no such object)
				// so just the base64-encoded image inline.
				$img = '<img src="data:' . $imgsize['mime'] . ';base64,' . base64_encode(file_get_contents($thumb_path)) . '" class="thumbnail" ' . $imgsize[3] . '" style="max-width:100px;height:auto;">';
			} else {
				$img = '-';
			}

			// Is there a pending record for this file?
			$exists_pending = WT_DB::prepare(
				"SELECT 1 FROM `##change` WHERE status='pending' AND new_gedcom LIKE CONCAT('%\n1 FILE ', ?, '\n%')"
			)->execute(array(WT_Filter::escapeLike($unused_file)))->fetchOne();

			// Form to create new media object in each tree
			$create_form='';
			if (!$exists_pending) {
				foreach ($media_trees as $media_tree) {
					$create_form .=
						'<p><a onclick="window.open(\'addmedia.php?action=showmediaform&amp;ged=' . rawurlencode($media_tree) . '&amp;filename=' . rawurlencode($unused_file) . '\', \'_blank\', edit_window_specs); return false;">' .  WT_I18N::translate('Create') . '</a> — ' . WT_Filter::escapeHtml($media_tree) . '<p>';
				}
			}

			$conf        = WT_I18N::translate('Are you sure you want to delete “%s”?', $unused_file);
			$delete_link =
				'<p><a onclick="if (confirm(\'' . WT_Filter::escapeJs($conf) . '\')) jQuery.post(\'admin_media.php\',{delete:\'' .WT_Filter::escapeJs($media_path . $unused_file) . '\',media_folder:\'' . WT_Filter::escapeJs($media_folder) . '\'},function(){location.reload();})" href="#">' . WT_I18N::Translate('Delete') . '</a></p>';

			$data[] = array(
				mediaFileInfo($media_folder, $media_path, $unused_file) . $delete_link,
				$img,
				$create_form,
			);
		}
		break;
	}

	header('Content-type: application/json');
	echo json_encode(array( // See http://www.datatables.net/usage/server-side
		'draw'            => WT_Filter::getInteger('draw'), // String, but always an integer
		'recordsTotal'    => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data'            => $data
	));
	exit;
}

/**
 * A unique list of media folders, from all trees.
 *
 * @return string[]
 */
function all_media_folders() {
	return WT_DB::prepare(
		"SELECT SQL_CACHE setting_value, setting_value" .
		" FROM `##gedcom_setting`" .
		" WHERE setting_name='MEDIA_DIRECTORY'" .
		" GROUP BY 1" .
		" ORDER BY 1"
	)->execute(array(WT_GED_ID))->fetchAssoc();
}

/**
 * Generate a list of media paths (within a media folder) used by all media objects.
 *
 * @param string $media_folder
 *
 * @return string[]
 */
function media_paths($media_folder) {
	$media_paths = WT_DB::prepare(
		"SELECT SQL_CACHE LEFT(m_filename, CHAR_LENGTH(m_filename) - CHAR_LENGTH(SUBSTRING_INDEX(m_filename, '/', -1))) AS media_path" .
		" FROM  `##media`" .
		" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
		" WHERE setting_value=?" .
		"	AND   m_filename NOT LIKE 'http://%'" .
		" AND   m_filename NOT LIKE 'https://%'" .
		" GROUP BY 1" .
		" ORDER BY 1"
	)->execute(array($media_folder))->fetchOneColumn();

	if (!$media_paths || reset($media_paths)!='') {
		// Always include a (possibly empty) top-level folder
		array_unshift($media_paths, '');
	}

	return array_combine($media_paths, $media_paths);
}

/**
 * Search a folder (and optional subfolders) for filenames that match a search pattern.
 *
 * @param string  $dir
 * @param boolean $recursive
 * @param string  $filter
 *
 * @return string[]
 */
function scan_dirs($dir, $recursive, $filter) {
	$files = array();

	// $dir comes from the database.  The actual folder may not exist.
	if (is_dir($dir)) {
		foreach (scandir($dir) as $path) {
			if (is_dir($dir . $path)) {
				// TODO - but what if there are user-defined subfolders “thumbs” or “watermarks”…
				if ($path!='.' && $path!='..' && $path!='thumbs' && $path!='watermark' && $recursive) {
					foreach (scan_dirs($dir . $path . '/', $recursive, $filter) as $subpath) {
						$files[] = $path . '/' . $subpath;
					}
				}
			} elseif (!$filter || stripos($path, $filter)!==false) {
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
	return scan_dirs(WT_DATA_DIR . $media_folder . $media_path, $subfolders=='include', $filter);
}

/**
 * Fetch a list of all files on in the database.
 *
 * @todo The subfolders parameter is not implemented.  However, as we
 *       currently use this function as an exclusion list, it is harmless
 *       to always include sub-folders.
 *
 * @param string $media_folder
 * @param string $media_path
 * @param string $subfolders
 * @param string $filter
 *
 * @return string[]
 */
function all_media_files($media_folder, $media_path, $subfolders, $filter) {
	return WT_DB::prepare(
		"SELECT SQL_CACHE SQL_CALC_FOUND_ROWS TRIM(LEADING ? FROM m_filename) AS media_path, 'OBJE' AS type, m_titl, m_id AS xref, m_file AS ged_id, m_gedcom AS gedrec, m_filename" .
		" FROM  `##media`" .
		" JOIN  `##gedcom_setting` ON (m_file = gedcom_id AND setting_name = 'MEDIA_DIRECTORY')" .
		" JOIN  `##gedcom`         USING (gedcom_id)" .
		" WHERE setting_value=?" .
		" AND   m_filename LIKE CONCAT(?, '%')" .
		" AND   (SUBSTRING_INDEX(m_filename, '/', -1) LIKE CONCAT('%', ?, '%')" .
		"  OR   m_titl LIKE CONCAT('%', ?, '%'))" .
		"	AND   m_filename NOT LIKE 'http://%'" .
		" AND   m_filename NOT LIKE 'https://%'"
	)->execute(array(
		$media_path,
		$media_folder,
		WT_Filter::escapeLike($media_path),
		WT_Filter::escapeLike($filter),
		WT_Filter::escapeLike($filter)
	))->fetchOneColumn();
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
	$html = '<b>' . WT_Filter::escapeHtml($file). '</b>';

	$full_path = WT_DATA_DIR . $media_folder . $media_path . $file;
	if ($file && file_exists($full_path)) {
		$size = @filesize($full_path);
		if ($size!==false) {
			$size = (int)(($size+1023)/1024); // Round up to next KB
			$size = /* I18N: size of file in KB */ WT_I18N::translate('%s KB', WT_I18N::number($size));
			$html .= WT_Gedcom_Tag::getLabelValue('__FILE_SIZE__', $size);
			$imgsize = @getimagesize($full_path);
			if (is_array($imgsize)) {
				$imgsize = /* I18N: image dimensions, width × height */ WT_I18N::translate('%1$s × %2$s pixels', WT_I18N::number($imgsize['0']), WT_I18N::number($imgsize['1']));
				$html .= WT_Gedcom_Tag::getLabelValue('__IMAGE_SIZE__', $imgsize);
			}

		} else {
			$html .= '<div class="error">' . WT_I18N::translate('This media file exists, but cannot be accessed.') . '</div>' ;
		}
	} else {
		$html .= '<div class="error">' . WT_I18N::translate('This media file does not exist.') . '</div>' ;
	}
	return $html;
}

/**
 * Generate some useful information and links about a media object.
 *
 * @param WT_Media $media
 *
 * @return string HTML
 */
function mediaObjectInfo(WT_Media $media) {
	$xref   = $media->getXref();
	$gedcom = WT_Tree::getNameFromId($media->getGedcomId());
	$name   = $media->getFullName();

	$html   =
		'<b>' . $name . '</b>' .
		'<div><i>' . WT_Filter::escapeHtml($media->getNote()) . '</i></div>' .
		'<br>' .
		'<a href="' . $media->getHtmlUrl() . '">' . WT_I18N::translate('View') . '</a>';

		$html .=
			' - ' .
			'<a onclick="window.open(\'addmedia.php?action=editmedia&amp;pid=' . $xref . '&ged=' . WT_Filter::escapeJs($gedcom) . '\', \'_blank\', edit_window_specs)" href="#">' . WT_I18N::Translate('Edit') . '</a>' .
			' - ' .
			'<a onclick="return delete_media(\'' . WT_Filter::escapeJs(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($media->getFullName()))) . '\', \'' . $media->getXref() . '\', \'' . WT_Filter::escapeJs($gedcom) . '\');" href="#">' . WT_I18N::Translate('Delete') . '</a>' .
			' - ';

	if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
		$html .= '<a onclick="return ilinkitem(\'' . $xref . '\', \'manage\', \'' . $gedcom . '\')" href="#">' . WT_I18N::Translate('Manage links') . '</a>';
	} else {
		global $TEXT_DIRECTION;
		$classSuffix = $TEXT_DIRECTION=='rtl' ? '_rtl' : '';

		$menu = new WT_Menu();
		$menu->addLabel(WT_I18N::translate('Set link'));
		$menu->addClass('', 'submenu');
		$submenu = new WT_Menu(WT_I18N::translate('To individual'));
		$submenu->addClass("submenuitem".$classSuffix);
		$submenu->addOnClick("return ilinkitem('$xref', 'person', '$gedcom')");
		$menu->addSubMenu($submenu);

		$submenu = new WT_Menu(WT_I18N::translate('To family'));
		$submenu->addClass("submenuitem".$classSuffix);
		$submenu->addOnClick("return ilinkitem('$xref', 'family', '$gedcom')");
		$menu->addSubMenu($submenu);

		$submenu = new WT_Menu(WT_I18N::translate('To source'));
		$submenu->addClass("submenuitem".$classSuffix);
		$submenu->addOnClick("return ilinkitem('$xref', 'source', '$gedcom')");
		$menu->addSubMenu($submenu);
		$html .= '<div style="display:inline-block;">' . $menu->getMenu() . '</div>';
	}
	$html .= '<br><br>';

	$linked = array();
	foreach ($media->linkedIndividuals('OBJE') as $link) {
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedFamilies('OBJE') as $link) {
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedSources('OBJE') as $link) {
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedNotes('OBJE') as $link) { // Invalid GEDCOM - you cannot link a NOTE to an OBJE
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	foreach ($media->linkedRepositories('OBJE') as $link) { // Invalid GEDCOM - you cannot link a REPO to an OBJE
		$linked[] = '<a href="' . $link->getHtmlUrl() . '">' . $link->getFullName() . '</a>';
	}
	if ($linked) {
		$html .= '<ul>';
		foreach ($linked as $link) {
			$html .= '<li>' . $link . '</li>';
		}
		$html .= '</ul>';
	} else {
		$html .= '<div class="error">' . WT_I18N::translate('This media object is not linked to any other record.') . '</div>';
	}

	return $html;
}

////////////////////////////////////////////////////////////////////////////////
// Start here
////////////////////////////////////////////////////////////////////////////////

// Preserver the pagination/filtering/sorting between requests, so that the
// browser’s back button works.  Pagination is dependent on the currently
// selected folder.
$table_id=md5($files.$media_folder.$media_path.$subfolders);

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Media'))
	->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
	->pageHeader()
	->addInlineJavascript('
	jQuery("#media-table-' . $table_id . '").dataTable({
		dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
		processing: true,
		serverSide: true,
		ajax: "'.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME.'?action=load_json&files='.$files.'&media_folder='.$media_folder.'&media_path='.$media_path.'&subfolders='.$subfolders.'",
		' . WT_I18N::datatablesI18N(array(5, 10, 20, 50, 100, 500, 1000, -1)) . ',
		jQueryUI: true,
		autoWidth:false,
		pageLength: 10,
		pagingType: "full_numbers",
		stateSave: true,
		stateDuration: 300,
		columns: [
			{},
			{ sortable: false },
			{ sortable: ' . ($files=='unused' ? 'false' : 'true') . ' }
		]
	});
	');
?>

<form method="get" action="<?php echo WT_SCRIPT_NAME; ?>">
	<table class="media_items">
		<tr>
			<th><?php echo WT_I18N::translate('Media files'); ?></th>
			<th><?php echo WT_I18N::translate('Media folders'); ?></th>
		</tr>
		<tr>
			<td>
				<input type="radio" name="files" value="local"<?php echo $files=='local' ? ' checked="checked"' : ''; ?> onchange="this.form.submit();">
				<?php echo /* I18N: “Local files” are stored on this computer */ WT_I18N::translate('Local files'); ?>
				<br>
				<input type="radio" name="files" value="external"<?php echo $files=='external' ? ' checked="checked"' : ''; ?> onchange="this.form.submit();">
				<?php echo /* I18N: “External files” are stored on other computers */ WT_I18N::translate('External files'); ?>
				<br>
				<input type="radio" name="files" value="unused"<?php echo $files=='unused' ? ' checked="checked"' : ''; ?> onchange="this.form.submit();">
				<?php echo WT_I18N::translate('Unused files'); ?>
			</td>
			<td>
				<?php
					switch ($files) {
					case 'local':
					case 'unused':
						$extra = 'onchange="this.form.submit();"';
						echo
							'<span dir="ltr">', // The full path will be LTR or mixed LTR/RTL.  Force LTR.
							WT_DATA_DIR;
						// Don’t show a list of media folders if it just contains one folder
						if (count($media_folders)>1) {
							echo '&nbsp;', select_edit_control('media_folder', $media_folders, null, $media_folder, $extra);
						} else {
							echo $media_folder, '<input type="hidden" name="media_folder" value="', WT_Filter::escapeHtml($media_folder), '">';
						}
						// Don’t show a list of subfolders if it just contains one subfolder
						if (count($media_paths)>1) {
							echo '&nbsp;', select_edit_control('media_path', $media_paths, null, $media_path, $extra);
						} else {
							echo $media_path, '<input type="hidden" name="media_path" value="', WT_Filter::escapeHtml($media_path), '">';
						}
						echo
							'</span>',
							'<div>',
							'<input type="radio" name="subfolders" value="include"', ($subfolders=='include' ? ' checked="checked"' : ''), ' onchange="this.form.submit();">',
							WT_I18N::translate('Include subfolders'),
							'<br>',
							'<input type="radio" name="subfolders" value="exclude"', ($subfolders=='exclude' ? ' checked="checked"' : ''), ' onchange="this.form.submit();">',
							WT_I18N::translate('Exclude subfolders'),
							'</div>';
						break;
					case 'external':
						echo WT_I18N::translate('External media files have a URL instead of a filename.');
						echo '<input type="hidden" name="media_folder" value="', WT_Filter::escapeHtml($media_folder), '">';
						echo '<input type="hidden" name="media_path" value="',   WT_Filter::escapeHtml($media_path),   '">';
						break;
					}
				?>
			</td>
		</tr>
	</table>
</form>
<br>
<br>
<table class="media_table" id="media-table-<?php echo $table_id ?>">
	<thead>
		<tr>
			<th><?php echo WT_I18N::translate('Media file'); ?></th>
			<th><?php echo WT_I18N::translate('Media'); ?></th>
			<th><?php echo WT_I18N::translate('Media object'); ?></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
