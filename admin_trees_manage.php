<?php
// UI for online updating of the GEDCOM configuration.
//
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
use WT\User;

define('WT_SCRIPT_NAME', 'admin_trees_manage.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Family trees'));

// Don’t allow the user to cancel the request.  We do not want to be left
// with an incomplete transaction.
ignore_user_abort(true);

// $path is the full path to the (possibly temporary) file.
// $filename is the actual filename (no folder).
function import_gedcom_file($gedcom_id, $path, $filename) {
	// Read the file in blocks of roughly 64K.  Ensure that each block
	// contains complete gedcom records.  This will ensure we don’t split
	// multi-byte characters, as well as simplifying the code to import
	// each block.

	$file_data='';
	$fp=fopen($path, 'rb');

	WT_DB::exec("START TRANSACTION");
	WT_DB::prepare("DELETE FROM `##gedcom_chunk` WHERE gedcom_id=?")->execute(array($gedcom_id));

	while (!feof($fp)) {
		$file_data.=fread($fp, 65536);
		// There is no strrpos() function that searches for substrings :-(
		for ($pos=strlen($file_data)-1; $pos>0; --$pos) {
			if ($file_data[$pos]=='0' && ($file_data[$pos-1]=="\n" || $file_data[$pos-1]=="\r")) {
				// We’ve found the last record boundary in this chunk of data
				break;
			}
		}
		if ($pos) {
			WT_DB::prepare(
				"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data) VALUES (?, ?)"
			)->execute(array($gedcom_id, substr($file_data, 0, $pos)));
			$file_data=substr($file_data, $pos);
		}
	}
	WT_DB::prepare(
		"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data) VALUES (?, ?)"
	)->execute(array($gedcom_id, $file_data));

	WT_Tree::get($gedcom_id)->setPreference('gedcom_filename', $filename);
	WT_DB::exec("COMMIT");
	fclose($fp);
}

// Process POST actions
switch (WT_Filter::post('action')) {
case 'delete':
	$gedcom_id = WT_Filter::postInteger('gedcom_id');
	if (WT_Filter::checkCsrf() && $gedcom_id) {
		WT_Tree::get($gedcom_id)->delete();
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);
	break;
case 'setdefault':
	if (WT_Filter::checkCsrf()) {
		WT_Site::setPreference('DEFAULT_GEDCOM', WT_Filter::post('default_ged'));
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);
	exit;
case 'new_tree':
	$ged_name=basename(WT_Filter::post('ged_name'));
	if (WT_Filter::checkCsrf() && $ged_name) {
		WT_Tree::create($ged_name);
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);
	exit;
case 'replace_upload':
	$gedcom_id = WT_Filter::postInteger('gedcom_id');
	// Make sure the gedcom still exists
	if (WT_Filter::checkCsrf() && WT_Tree::get($gedcom_id)) {
		foreach ($_FILES as $FILE) {
			if ($FILE['error'] == 0 && is_readable($FILE['tmp_name'])) {
				import_gedcom_file($gedcom_id, $FILE['tmp_name'], $FILE['name']);
			}
		}
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?keep_media' . $gedcom_id . '=' . WT_Filter::postBool('keep_media' . $gedcom_id));
	exit;
case 'replace_import':
	$gedcom_id = WT_Filter::postInteger('gedcom_id');
	// Make sure the gedcom still exists
	if (WT_Filter::checkCsrf() && WT_Tree::get($gedcom_id)) {
		$ged_name = basename(WT_Filter::post('ged_name'));
		import_gedcom_file($gedcom_id, WT_DATA_DIR.$ged_name, $ged_name);
	}
	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME . '?keep_media' . $gedcom_id . '=' . WT_Filter::postBool('keep_media' . $gedcom_id));
	exit;
}

$controller->pageHeader();

// Process GET actions
switch (WT_Filter::get('action')) {
case 'uploadform':
case 'importform':
	$tree=WT_Tree::get(WT_Filter::getInteger('gedcom_id'));
	// Check it exists
	if (!$tree) {
		break;
	}
	echo '<p>', WT_I18N::translate('This will delete all the genealogical data from <b>%s</b> and replace it with data from another GEDCOM.', $tree->tree_name_html), '</p>';
	// the javascript in the next line strips any path associated with the file before comparing it to the current GEDCOM name (both Chrome and IE8 include c:\fakepath\ in the filename).
	$previous_gedcom_filename = $tree->getPreference('gedcom_filename');
	echo '<form name="replaceform" method="post" enctype="multipart/form-data" action="', WT_SCRIPT_NAME, '" onsubmit="var newfile = document.replaceform.ged_name.value; newfile = newfile.substr(newfile.lastIndexOf(\'\\\\\')+1); if (newfile!=\'', WT_Filter::escapeHtml($previous_gedcom_filename), '\' && \'\' != \'', WT_Filter::escapeHtml($previous_gedcom_filename), '\') return confirm(\'', WT_Filter::escapeHtml(WT_I18N::translate('You have selected a GEDCOM file with a different name.  Is this correct?')), '\'); else return true;">';
	echo '<input type="hidden" name="gedcom_id" value="', $tree->tree_id, '">';
	echo WT_Filter::getCsrf();
	if (WT_Filter::get('action')=='uploadform') {
		echo '<input type="hidden" name="action" value="replace_upload">';
		echo '<input type="file" name="ged_name">';
	} else {
		echo '<input type="hidden" name="action" value="replace_import">';
		$d=opendir(WT_DATA_DIR);
		$files=array();
		while (($f=readdir($d))!==false) {
			if (!is_dir(WT_DATA_DIR.$f) && is_readable(WT_DATA_DIR.$f)) {
				$fp=fopen(WT_DATA_DIR.$f, 'rb');
				$header=fread($fp, 64);
				fclose($fp);
				if (preg_match('/^('.WT_UTF8_BOM.')?0 *HEAD/', $header)) {
					$files[]=$f;
				}
			}
		}
		if ($files) {
			sort($files);
			echo WT_DATA_DIR, '<select name="ged_name">';
			foreach ($files as $file) {
				echo '<option value="', WT_Filter::escapeHtml($file), '"';
				if ($file==$previous_gedcom_filename) {
					echo ' selected="selected"';
				}
				echo'>', WT_Filter::escapeHtml($file), '</option>';
			}
			echo '</select>';
		} else {
			echo '<p>', WT_I18N::translate('No GEDCOM files found.  You need to copy files to the <b>%s</b> directory on your server.', WT_DATA_DIR);
			echo '</form>';
			exit;
		}
	}
	echo '<br><br><input type="checkbox" name="keep_media', $tree->tree_id, '" value="1">';
	echo WT_I18N::translate('If you have created media objects in webtrees, and have edited your gedcom off-line using a program that deletes media objects, then check this box to merge the current media objects with the new GEDCOM file.');
	echo '<br><br><input type="submit" value="', WT_I18N::translate('continue'), '">';
	echo '</form>';
	exit;
}

// List the gedcoms available to this user
foreach (WT_Tree::GetAll() as $tree) {
	if (Auth::isManager($tree)) {

		echo
			'<table class="gedcom_table">',
			'<tr><th>', WT_I18N::translate('Family tree'),
			'</th><th><a class="accepted" href="index.php?ctype=gedcom&amp;ged=', $tree->tree_name_url, '" dir="auto">',
			$tree->tree_title_html, '</a>',
			'</th></tr><tr><th class="accepted">', $tree->tree_name_html,
			'</th><td>';

		// The third row shows an optional progress bar and a list of maintenance options
		$importing = WT_DB::prepare(
			"SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id = ? AND imported = '0' LIMIT 1"
		)->execute(array($tree->tree_id))->fetchOne();
		if ($importing) {
			$in_progress = WT_DB::prepare(
				"SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id = ? AND imported = '1' LIMIT 1"
			)->execute(array($tree->tree_id))->fetchOne();
			if (!$in_progress) {
				echo '<div id="import', $tree->tree_id, '"><div id="progressbar', $tree->tree_id, '"><div style="position:absolute;">', WT_I18N::translate('Deleting old genealogy data…'), '</div></div></div>';
				$controller->addInlineJavascript(
				'jQuery("#progressbar'.$tree->tree_id.'").progressbar({value: 0});'
			);
			} else {
				echo '<div id="import', $tree->tree_id, '"></div>';
			}
			$controller->addInlineJavascript(
				'jQuery("#import'.$tree->tree_id.'").load("import.php?gedcom_id='.$tree->tree_id.'&keep_media'.$tree->tree_id.'='.WT_Filter::get('keep_media'.$tree->tree_id).'");'
			);
			echo '<table border="0" width="100%" id="actions', $tree->tree_id, '" style="display:none">';
		} else {
			echo '<table border="0" width="100%" id="actions', $tree->tree_id, '">';
		}
		echo
			'<tr align="center">',
			// export
			'<td><a href="admin_trees_export.php?ged=', $tree->tree_name_url, '" onclick="return modalDialog(\'admin_trees_export.php?ged=', $tree->tree_name_url, '\', \'', WT_I18N::translate('Export'), '\');">', WT_I18N::translate('Export'), '</a>',
			help_link('export_gedcom'),
			'</td>',
			// import
			'<td><a href="', WT_SCRIPT_NAME, '?action=importform&amp;gedcom_id=', $tree->tree_id, '">', WT_I18N::translate('Import'), '</a>',
			help_link('import_gedcom'),
			'</td>',
			// download
			'<td><a href="admin_trees_download.php?ged=', $tree->tree_name_url,'">', WT_I18N::translate('Download'), '</a>',
			help_link('download_gedcom'),
			'</td>',
			// upload
			'<td><a href="', WT_SCRIPT_NAME, '?action=uploadform&amp;gedcom_id=', $tree->tree_id, '">', WT_I18N::translate('Upload'), '</a>',
			help_link('upload_gedcom'),
			'</td>',
			// delete
			'<td>',
			'<a href="#" onclick="if (confirm(\''.WT_Filter::escapeJs(WT_I18N::translate('Are you sure you want to delete “%s”?', $tree->tree_name)),'\')) document.delete_form', $tree->tree_id, '.submit(); return false;">', WT_I18N::translate('Delete'), '</a>',
			'<form name="delete_form', $tree->tree_id ,'" method="post" action="', WT_SCRIPT_NAME ,'">',
			'<input type="hidden" name="action" value="delete">',
			'<input type="hidden" name="gedcom_id" value="', $tree->tree_id, '">',
			WT_Filter::getCsrf(),
			'</form>',
			'</td></tr></table></td></tr></table><br>';
	}
}
?>

<?php if (Auth::isAdmin()): ?>
	<table class="gedcom_table2">
		<tr>
			<?php if (count(WT_Tree::GetAll())>1): ?>
			<th>
				<?php echo WT_I18N::translate('Default family tree'), help_link('default_gedcom'); ?>
			</th>
			<?php endif; ?>
			<th>
				<?php echo WT_I18N::translate('Create a new family tree'), help_link('add_new_gedcom'); ?>
			</th>
		</tr>
		<tr>
			<?php if (count(WT_Tree::GetAll())>1): ?>
			<td>
				<form method="post">
					<input type="hidden" name="action" value="setdefault">
					<?php echo WT_Filter::getCsrf(); ?>
					<?php echo select_edit_control('default_ged', WT_Tree::getNameList(), '', WT_Site::getPreference('DEFAULT_GEDCOM'), 'onchange="document.defaultform.submit();"'); ?>
					<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
				</form>
			</td>
			<?php endif; ?>
			<td class="button">
				<form method="post">
					<?php echo WT_Filter::getCsrf(); ?>
					<input type="hidden" name="action" value="new_tree">
					<input name="ged_name">
					<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
				</form>
			</td>
		</tr>
	</table>
<?php endif; ?>
<br>
<?php
// display link to PGV-WT transfer wizard on first visit to this page, before any GEDCOM is loaded
if (count(WT_Tree::GetAll())==0 && count(User::all())==1) {
	echo
		'<div class="center">',
		'<a style="color:green; font-weight:bold;" href="admin_pgv_to_wt.php">',
		WT_I18N::translate('Click here for PhpGedView to webtrees transfer wizard'),
		'</a>',
		help_link('PGV_WIZARD'),
		'</div>';
}

