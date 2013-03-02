<?php
// Add media to gedcom file
// Edit an existing media item
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'addmedia.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$pid         = safe_REQUEST($_REQUEST, 'pid',         WT_REGEX_XREF); // edit this media object
$linktoid    = safe_REQUEST($_REQUEST, 'linktoid',    WT_REGEX_XREF); // create a new media object, linked to this record
$action      = safe_REQUEST($_REQUEST, 'action');
$filename    = safe_REQUEST($_REQUEST, 'filename',    WT_REGEX_UNSAFE);
$text        = safe_REQUEST($_REQUEST, 'text',        WT_REGEX_UNSAFE);
$tag         = safe_REQUEST($_REQUEST, 'tag',         WT_REGEX_UNSAFE);
$islink      = safe_REQUEST($_REQUEST, 'islink',      WT_REGEX_UNSAFE);
$glevels     = safe_REQUEST($_REQUEST, 'glevels',     WT_REGEX_UNSAFE);
$folder      = safe_REQUEST($_REQUEST, 'folder',      WT_REGEX_UNSAFE);

$update_CHAN = !safe_POST_bool('preserve_last_changed');

$controller = new WT_Controller_Simple();
$controller
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
	->requireMemberLogin()
	->addInlineJavascript('
	// Shared Notes =========================
	function findnote(field) {
		pastefield = field;
		findwin = window.open("find.php?type=note", "_blank", find_window_specs);
		return false;
	}
	var pastefield;
	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}
	function paste_id(value) {
		pastefield.value = value;
	}
	function paste_char(value) {
		pastefield.value += value;
	}
	');
	
$disp = true;
$media = WT_Media::getInstance($pid);
if ($media) {
	$disp = $media->canDisplayDetails();
}
if ($action=='update' || $action=='create') {
	if (!isset($linktoid) || $linktoid=='new') $linktoid='';
	if (!empty($linktoid)) {
		$disp = WT_GedcomRecord::getInstance($linktoid)->canDisplayDetails();
	}
}

if (!WT_USER_CAN_EDIT || !$disp || !$ALLOW_EDIT_GEDCOM) {
	$controller
		->pageHeader()
		->addInlineJavascript('closePopupAndReloadParent();');
	exit;
}

// TODO - there is a lot of common code in the create and update cases....
// .... and also in the admin_media_upload.php script

switch ($action) {
case 'create': // Save the information from the “showmediaform” action
	$controller->setPageTitle(WT_I18N::translate('Create a new media object'));

	// Validate the media folder
	$folderName = str_replace('\\', '/', $folder);
	$folderName = trim($folderName, '/');
	if ($folderName == '.') {
		$folderName = '';
	}
	if ($folderName) {
		$folderName .= '/';
		// Not allowed to use “../”
		if (strpos('/' . $folderName, '/../')!==false) {
			WT_FlashMessages::addMessage('Folder names are not allowed to include “../”');
			break;
		}
	}

	// Make sure the media folder exists
	if (!is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
		if (@mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY, WT_PERM_EXE, true)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s was created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . '</span>'));
		} else {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . '</span>'));
			break;
		}
	}

	// Managers can create new media paths (subfolders).  Users must use existing folders.
	if ($folderName && !is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName)) {
		if (WT_USER_GEDCOM_ADMIN) {
			if (@mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName, WT_PERM_EXE, true)) {
				WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s was created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName . '</span>'));
			} else {
				WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName . '</span>'));
				break;
			}
		} else {
			// Regular users should not have seen this option - so no need for an error message.
			break;
		}
	}

	// The media folder exists.  Now create a thumbnail folder to match it.
	if (!is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName)) {
		if (!@mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName, WT_PERM_EXE, true)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName . '</span>'));
			break;
		}
	}

	// A thumbnail file with no main image?
	if (!empty($_FILES['thumbnail']['name']) && empty($_FILES['mediafile']['name'])) {
		// Assume the user used the wrong field, and treat this as a main image
		$_FILES['mediafile'] = $_FILES['thumbnail'];
		unset($_FILES['thumbnail']);
	}

	// Thumbnails must be images.
	if (!empty($_FILES['thumbnail']['name']) && !preg_match('/^image/', $_FILES['thumbnail']['type'])) {
		WT_FlashMessages::addMessage(WT_I18N::translate('Thumbnails must be images.'));
		break;
	}

	// User-specified filename?
	$filename = $text[0];
	// Use the name of the uploaded file?
	if (!$filename && !empty($_FILES['mediafile']['name'])) {
		$filename = $_FILES['mediafile']['name'];
	}

	// Validate the media path and filename
	if (preg_match('/^https?:\/\//i', $text[0], $match)) {
		// External media needs no further validation
		$fileName   = $filename;
		$folderName = '';
		unset($_FILES['mediafile'], $_FILES['thumbnail']);
	} elseif (preg_match('/([\/\\\\<>])/', $filename, $match)) {
		// Local media files cannot contain certain special characters
		WT_FlashMessages::addMessage(WT_I18N::translate('Filenames are not allowed to contain the character “%s”.', $match[1]));
		$filename = '';
		break;
	} elseif (preg_match('/(\.(php|pl|cgi|bash|sh|bat|exe|com))$/', $filename)) {
		// Do not allow obvious script files.
		WT_FlashMessages::addMessage(WT_I18N::translate('Filenames are not allowed to have the extension “%s”.', $match[1]));
		$filename = '';
		break;
	} elseif (!$filename) {
		WT_FlashMessages::addMessage(WT_I18N::translate('No media file was provided.'));
		break;
	} else {
		$fileName = $filename;
	}

	// Now copy the file to the correct location.
	if (!empty($_FILES['mediafile']['name'])) {
		$serverFileName = WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName . $fileName;
		if (file_exists($serverFileName)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('The file %s already exists.  Use another filename.', $folderName . $fileName));
			$filename = '';
			break;
		}
		if (move_uploaded_file($_FILES['mediafile']['tmp_name'], $serverFileName)) {
			chmod($serverFileName, WT_PERM_FILE);
			AddToLog('Media file ' . $serverFileName . ' uploaded', 'media');
		} else {
			WT_FlashMessages::addMessage(
				WT_I18N::translate('There was an error uploading your file.') .
				'<br>' .
				file_upload_error_text($_FILES['mediafile']['error'])
			);
			$filename = '';
			break;
		}

		// Now copy the (optional thumbnail)
		if (!empty($_FILES['thumbnail']['name']) && preg_match('/^image\/(png|gif|jpeg)', $_FILES['thumbnail']['type'], $match)) {
			$extension = $match[1];
			$thumbFile = preg_replace('/\.[a-z0-9]{3,5}$/', '.' . $extension, $fileName);
			$serverFileName = WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName .  $thumbFile;
			if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $serverFileName)) {
				chmod($serverFileName, WT_PERM_FILE);
				AddToLog('Thumbnail file ' . $serverFileName . ' uploaded', 'media');
			}
		}
	}

	$controller->pageHeader();
	// Build the gedcom record
	$media_id = get_new_xref('OBJE');
	if ($media_id) {
		$newged = '0 @' . $media_id . "@ OBJE\n";
		// Put the filename where handle_updates() will find it
		$text[0] = $folderName . $fileName;

		$newged  = handle_updates($newged);

		if (append_gedrec($newged, WT_GED_ID)) {
			if ($linktoid) {
				linkMedia($media_id, $linktoid, 1);
				AddToLog('Media ID '.$media_id." successfully added to $linktoid.", 'edit');
				$controller->addInlineJavascript('closePopupAndReloadParent();');
			} else {
				AddToLog('Media ID '.$media_id.' successfully added.', 'edit');
				$controller->addInlineJavascript('openerpasteid("' . $media_id . '");');
			}
		}
	}
	echo '<button onclick="closePopupAndReloadParent();">', WT_I18N::translate('close'), '</button>';
	exit;
	
case 'update': // Save the information from the “editmedia” action
	$controller->setPageTitle(WT_I18N::translate('Edit media object'));
	
	// Validate the media folder
	$folderName = str_replace('\\', '/', $folder);
	$folderName = trim($folderName, '/');
	if ($folderName == '.') {
		$folderName = '';
	}
	if ($folderName) {
		$folderName .= '/';
		// Not allowed to use “../”
		if (strpos('/' . $folderName, '/../')!==false) {
			WT_FlashMessages::addMessage('Folder names are not allowed to include “../”');
			break;
		}
	}

	// Make sure the media folder exists
	if (!is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
		if (@mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY, WT_PERM_EXE, true)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s was created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . '</span>'));
		} else {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . '</span>'));
			break;
		}
	}

	// Managers can create new media paths (subfolders).  Users must use existing folders.
	if ($folderName && !is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName)) {
		if (WT_USER_GEDCOM_ADMIN) {
			if (@mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName, WT_PERM_EXE, true)) {
				WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s was created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName . '</span>'));
			} else {
				WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName . '</span>'));
				break;
			}
		} else {
			// Regular users should not have seen this option - so no need for an error message.
			break;
		}
	}

	// The media folder exists.  Now create a thumbnail folder to match it.
	if (!is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName)) {
		if (!@mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName, WT_PERM_EXE, true)) {
			WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s does not exist, and it could not be created.', '<span class="filename">' . WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName . '</span>'));
			break;
		}
	}

	$fileName=$text[0];
	// Validate the media path and filename
	if (preg_match('/^https?:\/\//i', $fileName, $match)) {
		// External media needs no further validation
		$folderName = '';
		unset($_FILES['mediafile'], $_FILES['thumbnail']);
	} elseif (preg_match('/([\/\\\\<>])/', $fileName, $match)) {
		// Local media files cannot contain certain special characters
		WT_FlashMessages::addMessage(WT_I18N::translate('Filenames are not allowed to contain the character “%s”.', $match[1]));
		$fileName = '';
		break;
	} elseif (preg_match('/(\.(php|pl|cgi|bash|sh|bat|exe|com))$/', $fileName)) {
		// Do not allow obvious script files.
		WT_FlashMessages::addMessage(WT_I18N::translate('Filenames are not allowed to have the extension “%s”.', $match[1]));
		$fileName = '';
		break;
	} elseif (!$fileName) {
		WT_FlashMessages::addMessage(WT_I18N::translate('No media file was provided.'));
		break;
	}

	$oldFilename = $media->getFilename();
	$newFilename = $folderName . $fileName;

	//-- check if the file is used in more than one gedcom
	//-- do not allow it to be moved or renamed if it is
	$multi_gedcom=!$media->isExternal() && is_media_used_in_other_gedcom($media->getFilename(), WT_GED_ID);
	if ($multi_gedcom) {
		WT_FlashMessages::addMessage(WT_I18N::translate('This file is linked to another genealogical database on this server.  It cannot be deleted, moved, or renamed until these links have been removed.'));
		break;
	}

	// Cannot rename local to external or vice-versa
	if (isFileExternal($oldFilename) != isFileExternal($newFilename)) {
		WT_FlashMessages::addMessage(WT_I18N::translate('Media file %1$s could not be renamed to %2$s.', '<span class="filename">'.$oldFilename.'</span>', '<span class="filename">'.$newFilename.'</span>'));
		break;
	}

	// Move files on disk (if we can) to reflect the change to the GEDCOM data
	if (!$media->isExternal()) {
		$oldServerFile  = $media->getServerFilename('main');
		$oldServerThumb = $media->getServerFilename('thumb');

		$newmedia = new WT_Media("0 @xxx@ OBJE\n1 FILE " . $newFilename);
		$newServerFile  = $newmedia->getServerFilename('main');
		$newServerThumb = $newmedia->getServerFilename('thumb');

		// We could be either renaming an existing file, or updating a record (with no valid file) to point to a new file
		if ($oldServerFile != $newServerFile) {
			if (!file_exists($newServerFile) || @md5_file($oldServerFile)==md5_file($newServerFile)) {
				if (@rename($oldServerFile, $newServerFile)) {
					WT_FlashMessages::addMessage(WT_I18N::translate('Media file %1$s successfully renamed to %2$s.', '<span class="filename">'.$oldFilename.'</span>', '<span class="filename">'.$newFilename.'</span>'));
				} else {
					WT_FlashMessages::addMessage(WT_I18N::translate('Media file %1$s could not be renamed to %2$s.', '<span class="filename">'.$oldFilename.'</span>', '<span class="filename">'.$newFilename.'</span>'));
				}
			}
			if (!file_exists($newServerFile)) {
					WT_FlashMessages::addMessage(WT_I18N::translate('Media file %s does not exist.',
					'<span class="filename">'.$newFilename.'</span>'));
			}
		}
		if ($oldServerThumb != $newServerThumb) {
			if (!file_exists($newServerThumb) || @md5_file($oldServerFile)==md5_file($newServerThumb)) {
				if (@rename($oldServerThumb, $newServerThumb)) {
					WT_FlashMessages::addMessage(WT_I18N::translate('Thumbnail file %1$s successfully renamed to %2$s.', '<span class="filename">'.$oldFilename.'</span>', '<span class="filename">'.$newFilename.'</span>'));
				} else {
					WT_FlashMessages::addMessage(WT_I18N::translate('Thumbnail file %1$s could not be renamed to %2$s.', '<span class="filename">'.$oldFilename.'</span>', '<span class="filename">'.$newFilename.'</span>'));
				}
			}
			if (!file_exists($newServerThumb)) {
					WT_FlashMessages::addMessage(WT_I18N::translate('Thumbnail file %s does not exist.',
					'<span class="filename">'.$newFilename.'</span>'));
			}
		}
	}

	// Put the filename where handle_updates() will find it.
	$text[0] = $newFilename;

	if (!empty($pid)) {
		$gedrec=find_gedcom_record($pid, WT_GED_ID, true);
	}
	$newrec = "0 @$pid@ OBJE\n";
	$newrec = handle_updates($newrec);
	if (!$update_CHAN) {
		$newrec .= get_sub_record(1, '1 CHAN', $gedrec);
	}
	//-- look for the old record media in the file
	//-- if the old media record does not exist that means it was
	//-- generated at import and we need to append it
	replace_gedrec($pid, WT_GED_ID, $newrec, $update_CHAN);

	if ($pid && $linktoid!='') {
		$link = linkMedia($pid, $linktoid, 1);
		if ($link) {
			AddToLog('Media ID '.$pid." successfully added to $linktoid.", 'edit');
		}
	}
	$controller->pageHeader();
	echo '<button onclick="closePopupAndReloadParent();">', WT_I18N::translate('close'), '</button>';
	exit;
case 'showmediaform':
	$controller->setPageTitle(WT_I18N::translate('Create a new media object'));
	$action='create';
	break;
case 'editmedia':
	$controller->setPageTitle(WT_I18N::translate('Edit media object'));
	$action='update';
	break;
default:
	throw new Exception('Bad $action (' . $action . ') in addmedia.php');
}

$controller->pageHeader();

echo '<div id="addmedia-page">'; //container for media edit pop-up
echo '<form method="post" name="newmedia" action="addmedia.php" enctype="multipart/form-data">';
echo '<input type="hidden" name="action" value="', $action, '">';
echo '<input type="hidden" name="ged" value="', WT_GEDCOM, '">';
echo '<input type="hidden" name="pid" value="', $pid, '">';
if (!empty($linktoid)) {
	echo '<input type="hidden" name="linktoid" value="', $linktoid, '">';
}
echo '<table class="facts_table">';
echo '<tr><td class="topbottombar" colspan="2">';
echo $controller->getPageTitle(), help_link('OBJE');
echo '</td></tr>';
if ($linktoid == 'new' || ($linktoid == '' && $action != 'update')) {
	echo '<tr><td class="descriptionbox wrap width25">';
	echo WT_I18N::translate('Enter a Person, Family, or Source ID'), help_link('add_media_linkid');
	echo '</td><td class="optionbox wrap"><input type="text" name="gid" id="gid" size="6" value="">';
	echo ' ', print_findindi_link('gid');
	echo ' ', print_findfamily_link('gid');
	echo ' ', print_findsource_link('gid');
	echo '<p class="sub">', WT_I18N::translate('Enter or search for the ID of the person, family, or source to which this media item should be linked.'), '</p></td></tr>';
}
$gedrec=find_gedcom_record($pid, WT_GED_ID, true);

// generate a tag (with or without a filename) to populate the edit field
if (preg_match('/\n\d (FILE (.+))/', $gedrec, $match)) {
	// From an existing GEDCOM record
	$gedfile = $match[1];
	$isExternal = isFileExternal($match[2]);
} elseif ($filename) {
	// From Admin->Media->Unused
	$gedfile = 'FILE ' . $filename;
	$isExternal = false;
} else {
	// A new record
	$gedfile = 'FILE';
	$isExternal = false;
}

if ($gedfile) {
	// Box for user to choose to upload file from local computer
	echo '<tr><td class="descriptionbox wrap width25">';
	echo WT_I18N::translate('Media file to upload').help_link('upload_media_file').'</td><td class="optionbox wrap"><input type="file" name="mediafile" onchange="updateFormat(this.value);" size="40"></td></tr>';
	// Check for thumbnail generation support
	if (WT_USER_GEDCOM_ADMIN) {
		echo '<tr><td class="descriptionbox wrap width25">';
		echo WT_I18N::translate('Thumbnail to upload').help_link('upload_thumbnail_file').'</td><td class="optionbox wrap"><input type="file" name="thumbnail" size="40"></td></tr>';
		add_simple_tag("1 $gedfile", '', WT_I18N::translate('File name on server'), '', 'NOCLOSE');
		echo '<p class="sub">' . WT_I18N::translate('Do not change to keep original file name.');
		echo WT_I18N::translate('You may enter a URL, beginning with &laquo;http://&raquo;.') . '</p></td></tr>';
	}
	$fileName = '';
	if ($folder=='.') {
		$folder='';
	}
} else {
	if ($isExternal) {
		$fileName = substr($gedfile, 5);
		$folder = '';
	} else {
		$tmp=substr($gedfile, 5);
		$fileName = basename($tmp);
		$folder = dirname($tmp);
		if ($folder=='.') {
			$folder='';
		}
	}

	echo '<tr>';
	echo '<td class="descriptionbox wrap width25">';
	echo WT_I18N::translate('File name on server'), help_link('upload_server_file');
	echo '</td>';
	echo '<td class="optionbox wrap wrap">';
	if (WT_USER_GEDCOM_ADMIN) {
		echo '<input name="filename" type="text" value="' . htmlspecialchars($fileName) . '" size="40">';
		if (!$isExternal) {
			echo '<p class="sub">' . WT_I18N::translate('Do not change to keep original file name.') . '</p>';
		}
	} else {
		echo htmlspecialchars($fileName);
		echo '<input name="filename" type="hidden" value="' . htmlspecialchars($fileName) . '" size="40">';
	}
	echo '</td>';
	echo '</tr>';
}

// Box for user to choose the folder to store the image
if (!$isExternal) {
	echo '<tr><td class="descriptionbox wrap width25">';
	echo WT_I18N::translate('Folder name on server'), help_link('upload_server_folder'), '</td><td class="optionbox wrap">';
	//-- don’t let regular users change the location of media items
	if ($action!='update' || WT_USER_GEDCOM_ADMIN) {
		$mediaFolders = WT_Query_Media::folderList();
		echo '<span dir="ltr"><select name="folder_list" onchange="document.newmedia.folder.value=this.options[this.selectedIndex].value;">';
		echo '<option';
		if ($folder == '') echo ' selected="selected"';
		echo ' value=""> ', WT_I18N::translate('Choose: '), ' </option>';
		if (WT_USER_IS_ADMIN) {
			echo '<option value="other" disabled>', WT_I18N::translate('Other folder... please type in'), "</option>";
		}
		foreach ($mediaFolders as $f) {
			echo '<option value="', $f, '"';
			if ($folder == $f)
				echo ' selected="selected"';
			echo '>', $f, "</option>";
		}
		echo '</select></span>';
	} else {
		echo $folder;
	}
	if (WT_USER_IS_ADMIN) {
		echo '<br><span dir="ltr"><input type="text" name="folder" size="40" value="', $folder, '"></span>';
		if ($gedfile == 'FILE') {
			echo '<p class="sub">', WT_I18N::translate('This entry is ignored if you have entered a URL into the file name field.'), '</p>';
		}
	} else {
		echo '<input name="folder" type="hidden" value="', addslashes($folder), '">';
	}
	echo '</td></tr>';
} else {
	echo '<input name="folder" type="hidden" value="">';
}
// 2 FORM
if ($gedrec == '')
	$gedform = 'FORM';
else {
	$gedform = get_first_tag(2, 'FORM', $gedrec);
	if (empty($gedform))
		$gedform = 'FORM';
}
$formid = add_simple_tag("2 $gedform");

// automatically set the format field from the filename
$controller->addInlineJavascript('
	function updateFormat(filename) {
		var extsearch=/\.([a-zA-Z]{3,4})$/;
		if (extsearch.exec(filename)) {
			ext = RegExp.$1.toLowerCase();
			if (ext=="jpg") ext="jpeg";
			if (ext=="tif") ext="tiff";
		} else {
			ext = "";
		}
		formfield = document.getElementById("' . $formid . '");
		formfield.value = ext;
	}
');

// 3 TYPE
if ($gedrec == '')
	$gedtype = 'TYPE photo'; // default to ‘Photo’ unless told otherwise
else {
	$temp = str_replace("\r\n", "\n", $gedrec) . "\n";
	$types = preg_match("/3 TYPE(.*)\n/", $temp, $matches);
	if (empty($matches[0]))
		$gedtype = 'TYPE photo'; // default to ‘Photo’ unless told otherwise
	else
		$gedtype = 'TYPE ' . trim($matches[1]);
}
add_simple_tag("3 $gedtype");

// 2 TITL
if ($gedrec == '') {
	$gedtitl = 'TITL';
} else {
	$gedtitl = get_first_tag(2, 'TITL', $gedrec);
	if (empty($gedtitl)) {
		$gedtitl = get_first_tag(1, 'TITL', $gedrec);
	}
	if (empty($gedtitl)) {
		$gedtitl = 'TITL';
	}
}
add_simple_tag("2 $gedtitl");

if (strstr($ADVANCED_NAME_FACTS, '_HEB')!==false) {
	// 3 _HEB
	if ($gedrec == '') {
		$gedtitl = '_HEB';
	} else {
		$gedtitl = get_first_tag(3, '_HEB', $gedrec);
		if (empty($gedtitl)) {
			$gedtitl = '_HEB';
		}
	}
	add_simple_tag("3 $gedtitl");
}

if (strstr($ADVANCED_NAME_FACTS, 'ROMN')!==false) {
	// 3 ROMN
	if ($gedrec == '') {
		$gedtitl = 'ROMN';
	} else {
		$gedtitl = get_first_tag(3, 'ROMN', $gedrec);
		if (empty($gedtitl)) {
			$gedtitl = 'ROMN';
		}
	}
	add_simple_tag("3 $gedtitl");
}

// 2 _PRIM
if ($gedrec == '') {
	$gedprim = '_PRIM';
} else {
	$gedprim = get_first_tag(1, '_PRIM', $gedrec);
	if (empty($gedprim)) {
		$gedprim = '_PRIM';
	}
}
add_simple_tag("1 $gedprim");

//-- print out editing fields for any other data in the media record
$sourceSOUR = '';
if (!empty($gedrec)) {
	preg_match_all('/\n(1 (?!FILE|FORM|TYPE|TITL|_PRIM|_THUM|CHAN|DATA).*(\n[2-9] .*)*)/', $gedrec, $matches);
	foreach ($matches[1] as $subrec) {
		$pieces = explode("\n", $subrec);
		foreach ($pieces as $piece) {
			$ft = preg_match("/(\d) (\w+)(.*)/", $piece, $match);
			if ($ft == 0) continue;
			$subLevel = $match[1];
			$fact = trim($match[2]);
			$event = trim($match[3]);
			if ($fact=='NOTE' || $fact=='TEXT') {
				$event .= get_cont(($subLevel +1), $subrec, false);
			}
			if ($sourceSOUR!='' && $subLevel<=$sourceLevel) {
				// Get rid of all saved Source data
				add_simple_tag($sourceLevel .' SOUR '. $sourceSOUR);
				add_simple_tag(($sourceLevel+1) .' PAGE '. $sourcePAGE);
				add_simple_tag(($sourceLevel+2) .' TEXT '. $sourceTEXT);
				add_simple_tag(($sourceLevel+2) .' DATE '. $sourceDATE, '', WT_Gedcom_Tag::getLabel('DATA:DATE'));
				add_simple_tag(($sourceLevel+1) .' QUAY '. $sourceQUAY);
				$sourceSOUR = '';
			}

			if ($fact=='SOUR') {
				$sourceLevel = $subLevel;
				$sourceSOUR = $event;
				$sourcePAGE = '';
				$sourceTEXT = '';
				$sourceDATE = '';
				$sourceQUAY = '';
				continue;
			}

			// Save all incoming data about this source reference
			if ($sourceSOUR!='') {
				if ($fact=='PAGE') {
					$sourcePAGE = $event;
					continue;
				}
				if ($fact=='TEXT') {
					$sourceTEXT = $event;
					continue;
				}
				if ($fact=='DATE') {
					$sourceDATE = $event;
					continue;
				}
				if ($fact=='QUAY') {
					$sourceQUAY = $event;
					continue;
				}
				continue;
			}

			// Output anything that isn’t part of a source reference
			if (!empty($fact) && $fact != 'CONC' && $fact != 'CONT' && $fact != 'DATA') {
				add_simple_tag($subLevel .' '. $fact .' '. $event);
			}
		}
	}

	if ($sourceSOUR!='') {
		// Get rid of all saved Source data
		add_simple_tag($sourceLevel .' SOUR '. $sourceSOUR);
		add_simple_tag(($sourceLevel+1) .' PAGE '. $sourcePAGE);
		add_simple_tag(($sourceLevel+2) .' TEXT '. $sourceTEXT);
		add_simple_tag(($sourceLevel+2) .' DATE '. $sourceDATE, '', WT_Gedcom_Tag::getLabel('DATA:DATE'));
		add_simple_tag(($sourceLevel+1) .' QUAY '. $sourceQUAY);
	}
}
if (WT_USER_IS_ADMIN) {
	echo "<tr><td class=\"descriptionbox wrap width25\">";
	echo WT_Gedcom_Tag::getLabel('CHAN'), "</td><td class=\"optionbox wrap\">";
	if ($NO_UPDATE_CHAN) {
		echo "<input type=\"checkbox\" checked=\"checked\" name=\"preserve_last_changed\">";
	} else {
		echo "<input type=\"checkbox\" name=\"preserve_last_changed\">";
	}
	echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN'), '<br>';
	$event = new WT_Event(get_sub_record(1, '1 CHAN', $gedrec), null, 0);
	echo format_fact_date($event, new WT_Person(''), false, true);
	echo '</td></tr>';
}
echo '</table>';
			print_add_layer('SOUR', 1);
			print_add_layer('NOTE', 1);
			print_add_layer('SHARED_NOTE', 1);
			print_add_layer('RESN', 1);
		?>
		<p id="save-cancel">
			<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
			<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
		</p>
	</form>
</div>
