<?php
/**
 * UI for online updating of the GEDCOM configuration.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'admin_trees_manage.php');

require './includes/session.php';

// The gedcom admin page is for managers only!
if (!WT_USER_GEDCOM_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

// Which directory contains our data files?
$INDEX_DIRECTORY=get_site_setting('INDEX_DIRECTORY');

// Don't allow the user to cancel the request.  We do not want to be left
// with an incomplete transaction.
ignore_user_abort(true);

function import_gedcom_file($gedcom_id, $file_name) {
	// Read the file in blocks of roughly 64K.  Ensure that each block
	// contains complete gedcom records.  This will ensure we don't split
	// multi-byte characters, as well as simplifying the code to import
	// each block.

	$file_data='';
	$fp=fopen($file_name, 'rb');

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

	WT_DB::exec("COMMIT");
	fclose($fp);
}

// Process GET actions
switch (safe_GET('action')) {
case 'delete':
	$ged=safe_GET('ged');
	delete_gedcom(get_id_from_gedcom($ged));
	break;
}

// Process POST actions
switch (safe_POST('action')) {
case 'setdefault':
	set_site_setting('DEFAULT_GEDCOM', safe_POST('default_ged'));
	break;
case 'add_ged':
	$ged_name=basename(safe_POST('ged_name'));
	$gedcom_id=get_id_from_gedcom($ged_name);
	// check it doesn't already exist before we create it
	if (!$gedcom_id && file_exists($INDEX_DIRECTORY.$ged_name)) {
		$gedcom_id=get_id_from_gedcom($ged_name, true);
		import_gedcom_file($gedcom_id, $INDEX_DIRECTORY.$ged_name);
	}
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME);
	exit;
case 'new_ged':
	$ged_name=basename(safe_POST('ged_name'));
	$gedcom_id=get_id_from_gedcom($ged_name);
	// check it doesn't already exist before we create it
	if (!$gedcom_id) {
		$gedcom_id=get_id_from_gedcom($ged_name, true);
		// I18N: This should be a common/default/placeholder name of a person.  Put slashes around the surname.
		$john_doe=WT_I18N::translate('John /DOE/');
		$note=WT_I18N::translate('Edit this individual and replace their details with your own');
		WT_DB::prepare("DELETE FROM `##gedcom_chunk` WHERE gedcom_id=?")->execute(array($gedcom_id));
		WT_DB::prepare(
			"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data) VALUES (?, ?)"
		)->execute(array(
			$gedcom_id,
			"0 HEAD\n0 @I1@ INDI\n1 NAME {$john_doe}\n1 SEX M\n1 BIRT\n2 DATE 01 JAN 1850\n2 NOTE {$note}\n0 TRLR\n"
		));
	}
	break;
case 'upload_ged':
	foreach ($_FILES as $FILE) {
		if ($FILE['error']==0 && is_readable($FILE['tmp_name'])) {
			$ged_name=$FILE['name'];
			$gedcom_id=get_id_from_gedcom($ged_name);
			// check it doesn't already exist before we create it
			if (!$gedcom_id) {
				$gedcom_id=get_id_from_gedcom($ged_name, true);
				import_gedcom_file($gedcom_id, $FILE['tmp_name']);
			}
		}
	}
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME);
	exit;
case 'replace_upload':
	$gedcom_id=safe_POST('gedcom_id');
	// Make sure the gedcom still exists
	if (get_gedcom_from_id($gedcom_id)) {
		foreach ($_FILES as $FILE) {
			if ($FILE['error']==0 && is_readable($FILE['tmp_name'])) {
				import_gedcom_file($gedcom_id, $FILE['tmp_name']);
			}
		}
	}
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME);
	exit;
case 'replace_import':
	$gedcom_id=safe_POST('gedcom_id');
	// Make sure the gedcom still exists
	if (get_gedcom_from_id($gedcom_id)) {
		$ged_name=basename(safe_POST('ged_name'));
		import_gedcom_file($gedcom_id, $INDEX_DIRECTORY.$ged_name);
	}
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.WT_SCRIPT_NAME);
	exit;
}

$gedcoms=get_all_gedcoms();

print_header(WT_I18N::translate('Manage family trees'));

// "Help for this page" link
echo '<div id="page_help">', help_link('gedcom_administration'), '</div>';

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
	echo '<form name="replaceform" method="post" enctype="multipart/form-data" action="', WT_SCRIPT_NAME, '" onsubmit="var newfile = document.replaceform.ged_name.value; newfile = newfile.substr(newfile.lastIndexOf(\'\\\\\')+1); if (newfile!=\'', htmlspecialchars($gedcom_name), '\') return confirm(\'', htmlspecialchars(WT_I18N::translate('You have selected a GEDCOM with a different name.  Is this correct?')), '\'); else return true;">';
	echo '<input type="hidden" name="gedcom_id" value="', $gedcom_id, '" />';
	if (safe_GET('action')=='uploadform') {
		echo '<input type="hidden" name="action" value="replace_upload" />';
		echo '<input type="file" name="ged_name" />';
	} else {
		echo '<input type="hidden" name="action" value="replace_import" />';
		$d=opendir($INDEX_DIRECTORY);
		$files=array();
		while (($f=readdir($d))!==false) {
			if (!is_dir($INDEX_DIRECTORY.$f) && is_readable($INDEX_DIRECTORY.$f)) {
				$fp=fopen($INDEX_DIRECTORY.$f, 'rb');
				$header=fread($fp, 64);
				fclose($fp);
				if (preg_match('/^('.WT_UTF8_BOM.')?0 *HEAD/', $header)) {
					$files[]=$f;
				}
			}
		}
		if ($files) {
			echo $INDEX_DIRECTORY, '<select name="ged_name" />';
			foreach ($files as $file) {
				echo '<option value="', htmlspecialchars($file), '"';
				if ($file==$gedcom_name) {
					echo ' selected="selected"';
				}
				echo'>', htmlspecialchars($file), '</option>';
			}
			echo '</select>';
		} else {
			echo '<p>', WT_I18N::translate('No GEDCOM files found.  You need to copy files to the <b>%s</b> directory on your server.', $INDEX_DIRECTORY);
			echo '</form>';
			echo '<br /><br />';
			echo '<form name="cancel" method="get" action="', WT_SCRIPT_NAME, '"><input type="submit" value="', WT_I18N::translate('Cancel'), '" /></form>';
			print_footer();
			exit;
		}
	}
	echo '<br /><br /><input type="checkbox" name="keep_media', $gedcom_id, '" value="1">';
	echo WT_I18N::translate('If you have created media objects in webtrees, and have edited your gedcom off-line using a program that deletes media objects, then check this box to merge the current media objects with the new GEDCOM.');
	echo '<br /><br /><input type="submit" value="', WT_I18N::translate('Save'), '" /></form>';
	echo '</form>';
	echo '<form name="cancel" method="get" action="', WT_SCRIPT_NAME, '"><input type="submit" value="', WT_I18N::translate('Cancel'), '" /></form>';
	print_footer();
	exit;
}


// List the gedcoms available to this user
foreach ($gedcoms as $gedcom_id=>$gedcom_name) {
	if (userGedcomAdmin(WT_USER_ID, $gedcom_id)) {

		echo
			'<table class="gedcom_table" width="100%">',
			'<tr><th>', WT_I18N::translate('GEDCOM name'),
			'</th><th class="accepted" a href="index.php?ctype=gedcom&ged=', rawurlencode($gedcom_name), '">', htmlspecialchars($gedcom_name), ' - ',
			WT_I18N::translate('%s', get_gedcom_setting($gedcom_id, 'title')), '</a>',
			'</th></tr><tr><th>', WT_I18N::translate('GEDCOM administration'),
			'</th><td>';

		// The third row shows an optional progress bar and a list of maintenance options
		$importing=WT_DB::prepare(
			"SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id=? AND imported=0 LIMIT 1"
		)->execute(array($gedcom_id))->fetchOne();
		if ($importing) {
			echo
				'<div id="import', $gedcom_id, '"></div>',
				WT_JS_START,
				'jQuery("#import', $gedcom_id, '").load("import.php?gedcom_id=', $gedcom_id, '&keep_media=', safe_POST('keep_media'.$gedcom_id), '");',
				WT_JS_END,
				'<table border="0" width="100%" id="actions', $gedcom_id, '" style="display:none">';
		} else {
			echo '<table border="0" width="100%" id="actions', $gedcom_id, '">';
		}
		echo
			'<tr align="center">',
			// export
			'<td><a href="javascript:" onclick="window.open(\'', "export_gedcom.php?export=", rawurlencode($gedcom_name), '\', \'_blank\',\'left=50,top=50,width=500,height=500,resizable=1,scrollbars=1\');">', WT_I18N::translate('Export'), '</a>',
			help_link('export_gedcom'),
			'</td>',
			// import
			'<td><a href="', WT_SCRIPT_NAME, '?action=importform&amp;gedcom_id=', $gedcom_id, '">', WT_I18N::translate('Import'), '</a>',
			help_link('import_gedcom'),
			'</td>',
			// download
			'<td><a href="downloadgedcom.php?ged=', rawurlencode($gedcom_name),'">', WT_I18N::translate('Download'), '</a>',
			help_link('download_gedcom'),
			'</td>',
			// upload
			'<td><a href="', WT_SCRIPT_NAME, '?action=uploadform&amp;gedcom_id=', $gedcom_id, '">', WT_I18N::translate('Upload'), '</a>',
			help_link('upload_gedcom'),
			'</td>',
			// delete
			'<td><a href="', WT_SCRIPT_NAME, '?action=delete&ged=', rawurlencode($gedcom_name), '" onclick="return confirm(\''.htmlspecialchars(WT_I18N::translate('Permanently delete the GEDCOM %s and all its settings?', $gedcom_name)),'\');">', WT_I18N::translate('Delete'), '</a>',
			help_link('delete_gedcom'),
			'</td></tr></table></td></tr></table><br />';
	}
}

// Options for creating new gedcoms and setting defaults
if (WT_USER_IS_ADMIN) {
	echo
		'<table class="gedcom_table2"><tr>',
		'<th>', WT_I18N::translate('Default GEDCOM'),      help_link('default_gedcom'), '</th>',
		'<th>', WT_I18N::translate('Add a new GEDCOM'),    help_link('add_gedcom'),     '</th>',
		'<th>', WT_I18N::translate('Upload a new GEDCOM'), help_link('upload_gedcom'),  '</th>',
		'<th>', WT_I18N::translate('Create a new GEDCOM'), help_link('add_new_gedcom'), '</th>',
		'</tr><tr>',
		'<td>',
		'<form name="defaultform" method="post" action="', WT_SCRIPT_NAME, '">',
		'<input type="hidden" name="action" value="setdefault" />',
		'<select name="default_ged" class="header_select" onchange="document.defaultform.submit();">';
	$DEFAULT_GEDCOM=get_site_setting('DEFAULT_GEDCOM');
	if (empty($DEFAULT_GEDCOM)) {
		echo '<option value="" selected="selected"></option>';
	}
	foreach ($gedcoms as $gedcom_name) {
		echo '<option value="', urlencode($gedcom_name), '"';
		if ($DEFAULT_GEDCOM==$gedcom_name) echo ' selected="selected"';
		echo '>', htmlspecialchars($gedcom_name), '</option>';
	}
	echo
		'</select>',
		'</form></td>',
		'<td>',
		'<form name="addform" method="post" action="', WT_SCRIPT_NAME, '">',
		$INDEX_DIRECTORY,
		'<input type="hidden" name="action" value="add_ged" />',
		'<select name="ged_name" onchange="document.addform.submit();" />',
		'<option>', WT_I18N::translate('&lt;select&gt;'), '</option>',
	$d=opendir($INDEX_DIRECTORY);
	$files=false;
	while (($f=readdir($d))!==false) {
		if (!in_array($f, $gedcoms) && !is_dir($INDEX_DIRECTORY.$f) && is_readable($INDEX_DIRECTORY.$f)) {
			$fp=fopen($INDEX_DIRECTORY.$f, 'rb');
			$header=fread($fp, 64);
			fclose($fp);
			if (preg_match('/^('.WT_UTF8_BOM.')?0 *HEAD/', $header)) {
				echo '<option>', htmlspecialchars($f), '</option>';
				$files=true;
			}
		}
	}
	echo
		'</select>',
		'</form>',
		'</td>',
		'<td class="button">',
		'<form name="uploadform" method="post" action="', WT_SCRIPT_NAME, '" enctype="multipart/form-data">',
		'<input type="hidden" name="action" value="upload_ged" />',
		'<input type="file" name="ged_name" onchange="document.uploadform.submit();" />',
		'</form>',
		'</td>',
		'<td class="button">',
		'<form name="createform" method="post" action="', WT_SCRIPT_NAME, '">',
		'<input type="hidden" name="action" value="new_ged" />',
		'<input name="ged_name" />',
		' <input type="submit" value="', WT_I18N::translate('Save') , '"/>',
		'</form>',
		'</td>',
		'</tr></table><br/>';

		// display link to PGV-WT transfer wizard on first visit to this page, before any GEDCOM is loaded
		if (count($gedcoms)==0 && get_user_count()==1) {
			echo
				'<div class="center">',
				'<a style="color:green; font-weight:bold;" href="pgv_to_wt.php">',
				WT_I18N::translate('Click here for PhpGedView to <b>webtrees</b> transfer wizard'),
				'</a>',
				help_link('PGV_WIZARD'),
				'</div>';
		}
}
print_footer();
