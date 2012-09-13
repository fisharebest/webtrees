<?php
// UI for online updating of the GEDCOM configuration.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'admin_trees_manage.php');
require './includes/session.php';

$controller=new WT_Controller_Base();
$controller
	->requireManagerLogin()
	->setPageTitle(WT_I18N::translate('Family trees'));

// Don't allow the user to cancel the request.  We do not want to be left
// with an incomplete transaction.
ignore_user_abort(true);

// $path is the full path to the (possibly temporary) file.
// $filename is the actual filename (no directory).
function import_gedcom_file($gedcom_id, $path, $filename) {
	// Read the file in blocks of roughly 64K.  Ensure that each block
	// contains complete gedcom records.  This will ensure we don't split
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
				// We've found the last record boundary in this chunk of data
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

	set_gedcom_setting($gedcom_id, 'gedcom_filename', $filename);
	WT_DB::exec("COMMIT");
	fclose($fp);
}

// Process GET actions
switch (safe_GET('action')) {
case 'delete':
	WT_Tree::delete(WT_GED_ID);
	break;
}

// Process POST actions
switch (safe_POST('action')) {
case 'setdefault':
	set_site_setting('DEFAULT_GEDCOM', safe_POST('default_ged'));
	break;
case 'new_ged':
	$ged_name=basename(safe_POST('ged_name'));
	if ($ged_name) {
		WT_Tree::create($ged_name);
	}
	break;
case 'replace_upload':
	$gedcom_id=safe_POST('gedcom_id');
	// Make sure the gedcom still exists
	if (get_gedcom_from_id($gedcom_id)) {
		foreach ($_FILES as $FILE) {
			if ($FILE['error']==0 && is_readable($FILE['tmp_name'])) {
				import_gedcom_file($gedcom_id, $FILE['tmp_name'], $FILE['name']);
			}
		}
	}
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME.'?keep_media'.$gedcom_id.'='.safe_POST_bool('keep_media'.$gedcom_id));
	exit;
case 'replace_import':
	$gedcom_id=safe_POST('gedcom_id');
	// Make sure the gedcom still exists
	if (get_gedcom_from_id($gedcom_id)) {
		$ged_name=basename(safe_POST('ged_name'));
		import_gedcom_file($gedcom_id, WT_DATA_DIR.$ged_name, $ged_name);
	}
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME.'?keep_media'.$gedcom_id.'='.safe_POST_bool('keep_media'.$gedcom_id));
	exit;
}

$controller->pageHeader();

// Process GET actions
switch (safe_GET('action')) {
case 'uploadform':
case 'importform':
	$gedcom_id=safe_GET('gedcom_id');
	$gedcom_name=get_gedcom_from_id($gedcom_id);
	// Check it exists
	if (!$gedcom_name) {
		break;
	}
	echo '<p>', WT_I18N::translate('This will delete all the genealogical data from <b>%s</b> and replace it with data from another GEDCOM.', $gedcom_name), '</p>';
	// the javascript in the next line strips any path associated with the file before comparing it to the current GEDCOM name (both Chrome and IE8 include c:\fakepath\ in the filename).  
	$previous_gedcom_filename=get_gedcom_setting($gedcom_id, 'gedcom_filename');
	echo '<form name="replaceform" method="post" enctype="multipart/form-data" action="', WT_SCRIPT_NAME, '" onsubmit="var newfile = document.replaceform.ged_name.value; newfile = newfile.substr(newfile.lastIndexOf(\'\\\\\')+1); if (newfile!=\'', htmlspecialchars($previous_gedcom_filename), '\' && \'\' != \'', htmlspecialchars($previous_gedcom_filename), '\') return confirm(\'', htmlspecialchars(WT_I18N::translate('You have selected a GEDCOM with a different name.  Is this correct?')), '\'); else return true;">';
	echo '<input type="hidden" name="gedcom_id" value="', $gedcom_id, '">';
	if (safe_GET('action')=='uploadform') {
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
			echo WT_DATA_DIR, '<select name="ged_name">';
			foreach ($files as $file) {
				echo '<option value="', htmlspecialchars($file), '"';
				if ($file==$previous_gedcom_filename) {
					echo ' selected="selected"';
				}
				echo'>', htmlspecialchars($file), '</option>';
			}
			echo '</select>';
		} else {
			echo '<p>', WT_I18N::translate('No GEDCOM files found.  You need to copy files to the <b>%s</b> directory on your server.', WT_DATA_DIR);
			echo '</form>';
			exit;
		}
	}
	echo '<br><br><input type="checkbox" name="keep_media', $gedcom_id, '" value="1">';
	echo WT_I18N::translate('If you have created media objects in webtrees, and have edited your gedcom off-line using a program that deletes media objects, then check this box to merge the current media objects with the new GEDCOM.');
	echo '<br><br><input type="submit" value="', WT_I18N::translate('Continue'), '">';
	echo '</form>';
	exit;
}


// List the gedcoms available to this user
foreach (WT_Tree::GetAll() as $tree) {
	if (userGedcomAdmin(WT_USER_ID, $tree->tree_id)) {

		echo
			'<table class="gedcom_table">',
			'<tr><th>', WT_I18N::translate('Family tree'),
			'</th><th><a class="accepted" href="index.php?ctype=gedcom&amp;ged=', $tree->tree_name_url, '">',
			$tree->tree_title_html, '</a>',
			'</th></tr><tr><th class="accepted">', $tree->tree_name_html,
			'</th><td>';

		// The third row shows an optional progress bar and a list of maintenance options
		$importing=WT_DB::prepare(
			"SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id=? AND imported=0 LIMIT 1"
		)->execute(array($tree->tree_id))->fetchOne();
		if ($importing) {
			$in_progress=WT_DB::prepare(
				"SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id=? AND imported=1 LIMIT 1"
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
				'jQuery("#import'.$tree->tree_id.'").load("import.php?gedcom_id='.$tree->tree_id.'&keep_media'.$tree->tree_id.'='.safe_GET('keep_media'.$tree->tree_id).'");'
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
			'<td><a href="', WT_SCRIPT_NAME, '?action=delete&amp;ged=', $tree->tree_name_url, '" onclick="return confirm(\''.WT_I18N::translate('Permanently delete the GEDCOM %s and all its settings?', $tree->tree_title_html),'\');">', WT_I18N::translate('Delete'), '</a>',
			'</td></tr></table></td></tr></table><br>';
	}
}

// Options for creating new gedcoms and setting defaults
if (WT_USER_IS_ADMIN) {
	echo '<table class="gedcom_table2"><tr>';
	if (count(WT_Tree::GetAll())>1) {
		echo '<th>', WT_I18N::translate('Default family tree'), help_link('default_gedcom'), '</th>';
	}
	echo '<th>', WT_I18N::translate('Create a new family tree'), help_link('add_new_gedcom'), '</th></tr><tr>';
	if (count(WT_Tree::GetAll())>1) {
		echo
			'<td><form name="defaultform" method="post" action="', WT_SCRIPT_NAME, '">',
			'<input type="hidden" name="action" value="setdefault">',
			'<select name="default_ged" class="header_select" onchange="document.defaultform.submit();">';
		$DEFAULT_GEDCOM=get_site_setting('DEFAULT_GEDCOM');
		if (empty($DEFAULT_GEDCOM)) {
			echo '<option value="" selected="selected"></option>';
		}
		foreach (WT_Tree::GetAll() as $tree) {
			echo '<option value="', $tree->tree_name_html, '"';
			if ($DEFAULT_GEDCOM==$tree->tree_name) echo ' selected="selected"';
			echo '>', $tree->tree_name_html, '</option>';
		}
		echo '</select></form></td>';
	}
	echo
		'<td class="button">',
		'<form name="createform" method="post" action="', WT_SCRIPT_NAME, '">',
		'<input type="hidden" name="action" value="new_ged">',
		'<input name="ged_name">',
		' <input type="submit" value="', WT_I18N::translate('Save') , '">',
		'</form>',
		'</td>',
		'</tr></table><br>';

		// display link to PGV-WT transfer wizard on first visit to this page, before any GEDCOM is loaded
		if (count(WT_Tree::GetAll())==0 && get_user_count()==1) {
			echo
				'<div class="center">',
				'<a style="color:green; font-weight:bold;" href="admin_pgv_to_wt.php">',
				WT_I18N::translate('Click here for PhpGedView to <b>webtrees</b> transfer wizard'),
				'</a>',
				help_link('PGV_WIZARD'),
				'</div>';
		}
}	

