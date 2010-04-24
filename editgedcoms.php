<?php
/**
 * UI for online updating of the config file.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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

define('WT_SCRIPT_NAME', 'editgedcoms.php');
require './includes/session.php';

// The gedcom admin page is for gedcom administrators only!
if (!WT_USER_GEDCOM_ADMIN) {
	header('Location: login.php?url=editgedcoms.php');
	exit;
}

// Which directory contains our data files?
$INDEX_DIRECTORY=get_site_setting('INDEX_DIRECTORY');

function import_gedcom_file($gedcom_id, $file_name) {
	global $TBLPREFIX;

	$fp=fopen($file_name, 'rb');
	WT_DB::exec("START TRANSACTION");
	WT_DB::prepare(
		"UPDATE {$TBLPREFIX}gedcom".
		" SET import_gedcom=?, import_offset=1".
		" WHERE gedcom_id=?"
	)
	->bindParam(1, $fp,        PDO::PARAM_LOB)
	->bindParam(2, $gedcom_id, PDO::PARAM_INT)
	->execute();
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
	break;
case 'new_ged':
	$ged_name=basename(safe_POST('ged_name'));
	$gedcom_id=get_id_from_gedcom($ged_name);
	// check it doesn't already exist before we create it
	if (!$gedcom_id) {
		$gedcom_id=get_id_from_gedcom($ged_name, true);
		copy('config_gedcom.php', $INDEX_DIRECTORY.$ged_name.'_conf.php');
		copy('privacy.php',       $INDEX_DIRECTORY.$ged_name.'_priv.php');
		set_gedcom_setting($gedcom_id, 'config',  $INDEX_DIRECTORY.$ged_name.'_conf.php');
		set_gedcom_setting($gedcom_id, 'privacy', $INDEX_DIRECTORY.$ged_name.'_priv.php');
		set_gedcom_setting($gedcom_id, 'title',   i18n::translate('Genealogy from [%s]', $ged_name));

		// I18N: This should be a common/default/placeholder name of a person.  Put slashes around the surname.
		$john_doe=i18n::translate('John /DOE/');
		$note=i18n::translate('Edit this individual and replace their details with your own');
		WT_DB::prepare(
			"UPDATE {$TBLPREFIX}gedcom".
			" SET import_gedcom=?, import_offset=1".
			" WHERE gedcom_id=?"
		)->execute(array("0 HEAD\n0 @I1@ INDI\n1 NAME {$john_doe}\n1 SEX M\n1 BIRT\n2 DATE 1 JAN 1850\n2 NOTE {$note}\n0 TRLR\n", $gedcom_id));
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
	break;
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
	break;
case 'replace_import':
	$gedcom_id=safe_POST('gedcom_id');
	// Make sure the gedcom still exists
	if (get_gedcom_from_id($gedcom_id)) {
		$ged_name=basename(safe_POST('ged_name'));
		import_gedcom_file($gedcom_id, $INDEX_DIRECTORY.$ged_name);
	}
	break;
}

$gedcoms=WT_DB::prepare(
	"SELECT gedcom_id, gedcom_name, import_offset".
	" FROM {$TBLPREFIX}gedcom".
	" ORDER BY gedcom_name"
)->fetchAll();

$all_gedcoms=array();
foreach ($gedcoms as $gedcom) {
	$all_gedcoms[$gedcom->gedcom_id]=$gedcom->gedcom_name;
}

print_header(i18n::translate('GEDCOM administration'));
echo '<h2>', i18n::translate('GEDCOM administration'), '</h2>';

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
	echo '<p>', i18n::translate('This will delete all the genealogical data from <b>%s</b> and replace it with data from another GEDCOM.', $gedcom_name), '</p>';
	echo '<form name="replaceform" method="post" enctype="multipart/form-data" action="', WT_SCRIPT_NAME, '" onsubmit="if (document.replaceform.ged_name.value!=\'', htmlspecialchars($gedcom_name), '\') return confirm(\'', htmlspecialchars(i18n::translate('You have selected a GEDCOM with a different name.  Is this correct?')), '\'); else return true;">';
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
			echo '<p>', i18n::translate('No GEDCOM files found.  You need to copy files to the <b>%s</b> directory on your server.', $INDEX_DIRECTORY);
			echo '</form>';
			echo '<br /><br />';
			echo '<form name="cancel" method="get" action="', WT_SCRIPT_NAME, '"><input type="submit" value="', i18n::translate('Cancel'), '" /></form>';
			print_footer();
			exit;
		}
	}
	echo '<br /><br /><input type="checkbox" name="keep_media', $gedcom_id, '" value="1">';
	echo i18n::translate('If you have created media objects in webtrees, and have edited your gedcom off-line using a program that deletes media objects, then tick this box to merge the current media objects with the new GEDCOM.');
	echo '<br /><br /><input type="submit" value="', i18n::translate('Save'), '" /></form>';
	echo '</form>';
	echo '<form name="cancel" method="get" action="', WT_SCRIPT_NAME, '"><input type="submit" value="', i18n::translate('Cancel'), '" /></form>';
	print_footer();
	exit;
}


// List the gedcoms available to this user
foreach ($gedcoms as $gedcom) {
	if (userGedcomAdmin(WT_USER_ID, $gedcom->gedcom_id)) {

		echo
			'<table class="gedcom_table" width="100%">',
			'<tr><td class="list_label" width="20%">', i18n::translate('GEDCOM name'),
			'</td><td class="list_value"><a href="index.php?ctype=gedcom&ged=', urlencode($gedcom->gedcom_name), '">', htmlspecialchars($gedcom->gedcom_name), ' - ',
			htmlspecialchars(get_gedcom_setting($gedcom->gedcom_id, 'title')), '</a>',
			'</td></tr><tr><td class="list_label">', i18n::translate('GEDCOM administration'),
			'</td><td class="list_value">';

		// The third row shows an optional progress bar and a list of maintenance options
		if ($gedcom->import_offset>0) {
			echo
				'<div id="import', $gedcom->gedcom_id, '"></div>',
				WT_JS_START,
				'$("#import', $gedcom->gedcom_id, '").load("import.php?gedcom_id=', $gedcom->gedcom_id, '&keep_media=', safe_POST('keep_media'.$gedcom->gedcom_id), '");',
				WT_JS_END;
		}
		echo 
			'<table border="0" width="100%"><tr align="center">',
			// configuration
			'<td><a href="editconfig_gedcom.php?ged=', urlencode($gedcom->gedcom_name), '">', i18n::translate('Configuration'), '</a>',
			help_link('gedcom_configfile'),
			'</td>',
			// privacy
			'<td><a href="edit_privacy.php?ged=', urlencode($gedcom->gedcom_name), '">', i18n::translate('Privacy'), '</a>',
			help_link('edit_privacy'),
			'</td>',
			// export
			'<td><a href="javascript:" onclick="window.open(\'', encode_url("export_gedcom.php?export={$gedcom->gedcom_name}"), '\', \'_blank\',\'left=50,top=50,width=500,height=500,resizable=1,scrollbars=1\');">', i18n::translate('Export'), '</a>',
			help_link('export_gedcom.php'),
			'</td>',
			// import
			'<td><a href="', WT_SCRIPT_NAME, '?action=importform&amp;gedcom_id=', $gedcom->gedcom_id, '">', i18n::translate('Import'), '</a>',
			help_link('import_gedcom.php'),
			'</td>',
			// download
			'<td><a href="downloadgedcom.php?ged=', urlencode($gedcom->gedcom_name),'">', i18n::translate('Download'), '</a>',
			help_link('ownload_gedcom'),
			'</td>',
			// upload
			'<td><a href="', WT_SCRIPT_NAME, '?action=uploadform&amp;gedcom_id=', $gedcom->gedcom_id, '">', i18n::translate('Upload'), '</a>',
			help_link(''),
			'</td>',
			// delete
			'<td><a href="editgedcoms.php?action=delete&ged=', urlencode($gedcom->gedcom_name), '" onclick="return confirm(\''.htmlspecialchars(i18n::translate('Permanently delete the GEDCOM %s and all its settings?', $gedcom->gedcom_name)),'\');">', i18n::translate('Delete'), '</a>',
			help_link('delete_gedcom'),
			'</td></tr></table></td></tr></table><br />';
	}
}

// Options for creating new gedcoms and setting defaults
if (WT_USER_IS_ADMIN) {
	echo
		'<br/><table class="gedcom_table"><tr>',
		'<td class="list_label">', i18n::translate('Default GEDCOM'),      help_link('default_gedcom'),        '</td>',
		'<td class="list_label">', i18n::translate('Add a new GEDCOM'),    help_link('help_addgedcom.php'),    '</td>',
		'<td class="list_label">', i18n::translate('Upload a new GEDCOM'), help_link('help_uploadgedcom.php'), '</td>',
		'<td class="list_label">', i18n::translate('Create a new GEDCOM'), help_link('help_addnewgedcom.php'), '</td>',
		'</tr><tr>',
		'<td class="list_value_wrap">',
		'<form name="defaultform" method="post" action="', WT_SCRIPT_NAME, '">',
		'<input type="hidden" name="action" value="setdefault" />',
		'<select name="default_ged" class="header_select" onchange="document.defaultform.submit();">';
	$DEFAULT_GEDCOM=get_site_setting('DEFAULT_GEDCOM');
	foreach ($gedcoms as $gedcom) {
		echo '<option value="', urlencode($gedcom->gedcom_name), '"';
		if ($DEFAULT_GEDCOM==$gedcom->gedcom_name) echo ' selected="selected"';
		echo '>', htmlspecialchars($gedcom->gedcom_name), '</option>';
	}
	echo
		'</select>',
		'</form></td>',
		'<td class="list_value_wrap">',
		'<form name="addform" method="post" action="', WT_SCRIPT_NAME, '">',
		$INDEX_DIRECTORY,
		'<input type="hidden" name="action" value="add_ged" />',
		'<select name="ged_name" onchange="document.addform.submit();" />',
		'<option>', i18n::translate('Select a file'), '</option>',
	$d=opendir($INDEX_DIRECTORY);
	$files=false;
	while (($f=readdir($d))!==false) {
		if (!in_array($f, $all_gedcoms) && !is_dir($INDEX_DIRECTORY.$f) && is_readable($INDEX_DIRECTORY.$f)) {
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
		'<td class="list_value_wrap">',
		'<form name="uploadform" method="post" action="', WT_SCRIPT_NAME, '" enctype="multipart/form-data">',
		'<input type="hidden" name="action" value="upload_ged" />',
		'<input type="file" name="ged_name" onchange="document.uploadform.submit();" />',
		'</form>',
		'</td>',
		'<td class="list_value_wrap">',
		'<form name="createform" method="post" action="', WT_SCRIPT_NAME, '">',
		'<input type="hidden" name="action" value="new_ged" />',
		'<input name="ged_name" />',
		' <input type="submit" value="', i18n::translate('Save') , '"/>',
		'</form>',
		'</td>',
		'</tr></table><br/>';
}

print_footer();
