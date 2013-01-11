<?php
// Add media to gedcom file
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

$controller=new WT_Controller_Simple();
$controller
	->addExternalJavascript(WT_JQUERY_URL)
	->addExternalJavascript(WT_JQUERYUI_URL)
	->addExternalJavascript(WT_STATIC_URL.'js/webtrees.js')
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
	->setPageTitle(WT_I18N::translate('Create a new media object'))
	->pageHeader()
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
	function checkpath(folder) {
		value = folder.value;
		if (value.substr(value.length-1, 1) == "/") value = value.substr(0, value.length-1);
		if (value.substr(0, 1) == "/") value = value.substr(1, value.length-1);
		result = value.split("/");
		if (result.length > '.$MEDIA_DIRECTORY_LEVELS.  ') {
			alert("' . WT_I18N::translate('You can enter no more than %s subdirectory names', $MEDIA_DIRECTORY_LEVELS) . '");
			folder.focus();
		}
	}
	');

// TODO use GET/POST, rather than $_REQUEST
// TODO decide what validation is required on these input parameters
$pid        =safe_REQUEST($_REQUEST, 'pid',         WT_REGEX_XREF);
$mid        =safe_REQUEST($_REQUEST, 'mid',         WT_REGEX_XREF);
$gid        =safe_REQUEST($_REQUEST, 'gid',         WT_REGEX_XREF);
$linktoid   =safe_REQUEST($_REQUEST, 'linktoid',    WT_REGEX_XREF);
$action     =safe_REQUEST($_REQUEST, 'action',      WT_REGEX_NOSCRIPT, 'showmediaform');
$folder     =safe_REQUEST($_REQUEST, 'folder',      WT_REGEX_UNSAFE);
$oldFolder  =safe_REQUEST($_REQUEST, 'oldFolder',   WT_REGEX_UNSAFE);
$filename   =safe_REQUEST($_REQUEST, 'filename',    WT_REGEX_UNSAFE);
$oldFilename=safe_REQUEST($_REQUEST, 'oldFilename', WT_REGEX_UNSAFE, $filename);
$level      =safe_REQUEST($_REQUEST, 'level',       WT_REGEX_UNSAFE);
$text       =safe_REQUEST($_REQUEST, 'text',        WT_REGEX_UNSAFE);
$tag        =safe_REQUEST($_REQUEST, 'tag',         WT_REGEX_UNSAFE);
$islink     =safe_REQUEST($_REQUEST, 'islink',      WT_REGEX_UNSAFE);
$glevels    =safe_REQUEST($_REQUEST, 'glevels',     WT_REGEX_UNSAFE);

$update_CHAN=!safe_POST_bool('preserve_last_changed');

$disp = true;
if (empty($pid) && !empty($mid)) $pid = $mid;
if (!empty($pid)) {
	$disp = WT_GedcomRecord::getInstance($pid)->canDisplayDetails();
}
if ($action=='update' || $action=='newentry') {
	if (!isset($linktoid) || $linktoid=='new') $linktoid='';
	if (empty($linktoid) && !empty($gid)) $linktoid = $gid;
	if (!empty($linktoid)) {
		$disp = WT_GedcomRecord::getInstance($linktoid)->canDisplayDetails();

	}
}

if (!WT_USER_CAN_EDIT || !$disp || !$ALLOW_EDIT_GEDCOM) {
	$controller->addInlineJavascript('opener.window.location.reload(); window.close();');
	exit;
}

// Naming conventions used in this script:
// folderName - this is the link to the folder in the standard media folder; the one that is stored in the gedcom.
// serverFolderName - this is where the file is physically located.  if the media firewall is enabled it is in the protected media folder.  if not it is the same as folderName.
// thumbFolderName - this is the link to the thumb folder in the standard media folder
// serverThumbFolderName - this is where the thumbnail file is physically located

switch ($action) {
case 'newentry':
	if (empty($level)) $level = 1;

	$error = '';
	$mediaFile = '';
	$thumbFile = '';
	if (!empty($_FILES['mediafile']['name']) || !empty($_FILES['thumbnail']['name'])) {
		// Validate and correct folder names
		$folderName = trim(trim(safe_POST('folder', WT_REGEX_NOSCRIPT)), '/');
		$folderName = check_media_depth($folderName.'/y.z', 'BACK');
		$folderName = dirname($folderName).'/';
		$thumbFolderName = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $folderName);

		$realFolderName = get_media_firewall_path($folderName);
		$realThumbFolderName = get_media_firewall_path($thumbFolderName);
		// make sure the dirs exist
		@mkdirs($realFolderName);
		@mkdirs($realThumbFolderName);

		$error = '';

		// Determine filename on server
		if (WT_USER_GEDCOM_ADMIN && !empty($text[0])) $fileName = trim(trim($text[0]), '/');
		else $fileName = '';
		$parts = pathinfo_utf($fileName);
		if (!empty($parts['basename'])) {
			// User supplied a name to be used on the server
			$mediaFile = $parts['basename']; // Use the supplied name
			if (empty($parts['extension']) || !in_array(strtolower($parts['extension']), $MEDIATYPE)) {
				// Strip invalid extension from supplied name
				$lastDot = strrpos($mediaFile, '.');
				if ($lastDot !== false) $mediaFile = substr($mediaFile, 0, $lastDot);
				// Use extension of original uploaded filename
				if (!empty($_FILES['mediafile']['name'])) $parts = pathinfo_utf($_FILES['mediafile']['name']);
				else $parts = pathinfo_utf($_FILES['thumbnail']['name']);
				if (!empty($parts['extension'])) $mediaFile .= '.'.$parts['extension'];
			}
		} else {
			// User did not specify a name to be used on the server:  use the original uploaded filename
			if (!empty($_FILES['mediafile']['name'])) $parts = pathinfo_utf($_FILES['mediafile']['name']);
			else $parts = pathinfo_utf($_FILES['thumbnail']['name']);
			$mediaFile = $parts['basename'];
		}
		if (!empty($_FILES['mediafile']['name'])) {
			$newFile = $realFolderName.$mediaFile;
			// Copy main media file into the destination folder
			if (file_exists(filename_decode($newFile))) {
				$error .= WT_I18N::translate('Media file already exists.').'&nbsp;&nbsp;'.$newFile.'<br>';
			} else {
				if (!move_uploaded_file($_FILES['mediafile']['tmp_name'], filename_decode($newFile))) {
					// the file cannot be copied
					$error .= WT_I18N::translate('There was an error uploading your file.').'<br>'.file_upload_error_text($_FILES['mediafile']['error']).'<br>';
				} else {
					@chmod(filename_decode($newFile), WT_PERM_FILE);
					AddToLog("Media file {$folderName}{$mediaFile} uploaded", 'media');
				}
			}
		}
		if ($error=='' && !empty($_FILES['thumbnail']['name'])) {
			$newThum = $realThumbFolderName.$mediaFile;
			// Copy user-supplied thumbnail file into the destination folder
			if (file_exists(filename_decode($newThum))) {
				$error .= WT_I18N::translate('Media thumbnail already exists.').'&nbsp;&nbsp;'.$newThum.'<br>';
			} else {
				if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], filename_decode($newThum))) {
					// the file cannot be copied
					$error .= WT_I18N::translate('There was an error uploading your file.').'<br>'.file_upload_error_text($_FILES['thumbnail']['error']).'<br>';
				} else {
					@chmod(filename_decode($newThum), WT_PERM_FILE);
					AddToLog("Media file {$thumbFolderName}{$mediaFile} uploaded", 'media');
				}
			}
		}
		if ($error=='' && empty($_FILES['mediafile']['name']) && !empty($_FILES['thumbnail']['name'])) {
			// Copy user-supplied thumbnail file into the main destination folder
			if (!copy(filename_decode($whichFile1), filename_decode($whichFile2))) {
				// the file cannot be copied
				$error .= WT_I18N::translate('There was an error uploading your file.').'<br>'.WT_I18N::translate('The file %s could not be copied from %s', $realThumbFolderName.$mediaFile, $realThumbFolderName.$mediaFile).'<br>';
			} else {
				@chmod(filename_decode($whichFile2), WT_PERM_FILE);
				AddToLog("Media file {$folderName}{$mediaFile} copied from {$thumbFolderName}{$mediaFile}", 'media');
			}
		}
		if ($error=='' && !empty($_FILES['mediafile']['name']) && empty($_FILES['thumbnail']['name'])) {
			if (safe_POST('genthumb', 'yes', 'no') == 'yes') {
				// Generate thumbnail from main image
				$parts = pathinfo_utf($mediaFile);
				if (!empty($parts['extension'])) {
					$ext = strtolower($parts['extension']);
					if (isImageTypeSupported($ext)) {
						$thumbnail = $thumbFolderName.$mediaFile;
						$okThumb = generate_thumbnail($folderName.$mediaFile, $thumbnail, 'OVERWRITE');
						if (!$okThumb) {
							$error .= WT_I18N::translate('Thumbnail %s could not be generated automatically.', $thumbnail);
						} else {
							echo WT_I18N::translate('Thumbnail %s generated automatically.', $thumbnail);
							echo '<br>';
							AddToLog("Media thumbnail {$thumbnail} generated", 'media');
						}
					}
				}
			}
		}
		// Let’s see if there are any errors generated and print it
		if (!empty($error)) {
			echo '<span class="error">', $error, '</span><br>';
			$mediaFile = '';
			$finalResult = false;
		} else $finalResult = true;
	}
	if ($mediaFile=='') {
		// No upload: should be an existing file on server
		if ($tag[0]=='FILE') {
			if (!empty($text[0])) {
				$isExternal = isFileExternal($text[0]);
				if ($isExternal) {
					$fileName = $text[0];
					$mediaFile = $fileName;
					$folderName = '';
				} else {
					$fileName = check_media_depth($text[0], 'BACK');
					$mediaFile = basename($fileName);
					$folderName = dirname($fileName).'/';
				}
			}
			if ($mediaFile=='') {
				echo '<span class="error">', WT_I18N::translate('Blank name or illegal characters in name'), '</span><br>';
				$finalResult = false;
			} else $finalResult = true;
		} else {
			//-- check if the file is used in more than one gedcom
			//-- do not allow it to be moved or renamed if it is
			$myFile = str_replace($MEDIA_DIRECTORY, '', $oldFolder.$oldFilename);
			$multi_gedcom=is_media_used_in_other_gedcom($myFile, WT_GED_ID);

			// Handle Admin request to rename or move media file
			if ($filename!=$oldFilename) {
				$parts = pathinfo_utf($filename);
				if (empty($parts['extension']) || !in_array(strtolower($parts['extension']), $MEDIATYPE)) {
					$parts = pathinfo_utf($oldFilename);
					$filename .= '.'.$parts['extension'];
				}
			}
			if (substr($folder, -1)!='/') $folder .= '/';
			if ($folder=='/') $folder = '';
			$folder = check_media_depth($folder.'y.z', 'BACK');
			$folder = dirname($folder).'/';
			if (substr($oldFolder, -1)!='/') $oldFolder .= '/';
			if ($oldFolder=='/') $oldFolder = '';
			$oldFolder = check_media_depth($oldFolder.'y.z', 'BACK');
			$oldFolder = dirname($oldFolder).'/';

			$finalResult = true;
			if ($filename!=$oldFilename || $folder!=$oldFolder) {
				if ($multi_gedcom) {
					echo '<span class="error">', WT_I18N::translate('This file is linked to another genealogical database on this server.  It cannot be deleted, moved, or renamed until these links have been removed.'), '<br><br><b>';
					if ($filename!=$oldFilename) {
						echo WT_I18N::translate('Media file could not be moved or renamed.');
					} else {
						echo WT_I18N::translate('Media file could not be moved.');
					}
					echo '</b></span><br>';
					$finalResult = false;
				} else {
					$oldMainFile = $oldFolder.$oldFilename;
					$newMainFile = $folder.$filename;
					$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldMainFile);
					$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $newMainFile);
					if (media_exists($oldMainFile) == 3) {
						// the file is in the media firewall folder
						$oldMainFile = get_media_firewall_path($oldMainFile);
						$newMainFile = get_media_firewall_path($newMainFile);
					}
					if (media_exists($oldThumFile) == 3) {
						$oldThumFile = get_media_firewall_path($oldThumFile);
						$newThumFile = get_media_firewall_path($newThumFile);
					}
					$isMain = file_exists(filename_decode($oldMainFile));
					$okMain = !file_exists(filename_decode($newMainFile));
					$isThum = file_exists(filename_decode($oldThumFile));
					$okThum = !file_exists(filename_decode($newThumFile));
					if ($okMain && $okThum) {
						// make sure the directories exist before moving the files
						mkdirs(dirname($newMainFile).'/');
						mkdirs(dirname($newThumFile).'/');
						if ($isMain) $okMain = @rename(filename_decode($oldMainFile), filename_decode($newMainFile));
						if ($isThum) $okThum = @rename(filename_decode($oldThumFile), filename_decode($newThumFile));
					}

					// Build text to tell Admin about the success or failure of the requested operation
					$mediaAction = 0;
					if ($filename!=$oldFilename) $mediaAction = 1;
					if ($folder!=$oldFolder) $mediaAction = $mediaAction + 2;

					if (!$isMain) {
						echo WT_I18N::translate(
							'Media file %s does not exist.',
							'<span class="filename">'.$oldFolder.$oldFilename.'</span>'
						);
					} else {
						if ($okMain) {
							echo WT_I18N::translate(
								'Media file %1$s successfully renamed to %2$s.',
								'<span class="filename">'.$oldFolder.$oldFilename.'</span>',
								'<span class="filename">'.$folder.$filename.'</span>'
							);
						} else {
							$finalResult = false;
							echo '<span class="error">';
							echo WT_I18N::translate(
								'Media file %1$s could not be renamed to %2$s.',
								'<span class="filename">'.$oldFolder.$oldFilename.'</span>',
								'<span class="filename">'.$folder.$filename.'</span>'
							);
							echo '</span>';
						}
					}
					echo '<br>';

					if (!$isThum) {
						echo WT_I18N::translate(
							'Thumbnail file %s does not exist.',
							'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldFolder).$oldFilename.'</span>'
						);
					} else {
						if ($okThum) {
							echo WT_I18N::translate(
								'Thumbnail file %1$s successfully renamed to %2$s.',
								'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldFolder).$oldFilename.'</span>',
								'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $folder).$filename.'</span>'
							);
						} else {
							$finalResult = false;
							echo '<span class="error">';
							echo WT_I18N::translate(
								'Thumbnail file %1$s could not be renamed to %2$s.',
								'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldFolder).$oldFilename.'</span>',
								'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $folder).$filename.'</span>'
							);
							echo '</span>';
						}
					}
					echo '<br>';
				}
			}

			// Insert the 1 FILE xxx record into the arrays used by function handle_updates()
			$glevels = array_merge(array('1'), $glevels);
			$tag = array_merge(array('FILE'), $tag);
			$islink = array_merge(array(0), $islink);
			$text = array_merge(array($folder.$filename), $text);

			$mediaFile = $filename;
			$folderName = $folder;
		}
	}

	if ($finalResult && $mediaFile!='') {
		// NOTE: Build the gedcom record
		// NOTE: Level 0
		$media_id = get_new_xref('OBJE');
		$newged = '0 @'.$media_id."@ OBJE\n";
		//-- set the FILE text to the correct file location in the standard media folder
		if (WT_USER_GEDCOM_ADMIN) $text[0] = $folderName.$mediaFile;
		else $newged .= '1 FILE '.$folderName.$mediaFile."\n";

		$newged = handle_updates($newged);

		$media_obje = new WT_Media($newged);
		$mediaid = append_gedrec($newged, WT_GED_ID);
		if ($mediaid) {
			if ($linktoid) {
				linkMedia($mediaid, $linktoid, $level);
				AddToLog('Media ID '.$media_id." successfully added to $linktoid.", 'edit');
				$controller->addInlineJavascript('closePopupAndReloadParent();');
			} else {
				AddToLog('Media ID '.$mediaid.' successfully added.', 'edit');
				$controller->addInlineJavascript('openerpasteid("' . $mediaid . '");');
			}
		}
	}
	echo '<button onclick="closePopupAndReloadParent();">', WT_I18N::translate('close'), '</button>';
	break;
	
case 'update':
	if (empty($level)) $level = 1;
	//-- check if the file is used in more than one gedcom
	//-- do not allow it to be moved or renamed if it is
	$myFile = str_replace($MEDIA_DIRECTORY, '', $oldFolder.$oldFilename);
	$multi_gedcom=is_media_used_in_other_gedcom($myFile, WT_GED_ID);

	$isExternal = isFileExternal($oldFilename) || isFileExternal($filename);
	$finalResult = true;

	// Handle Admin request to rename or move media file
	if (!$isExternal) {
		if ($filename!=$oldFilename) {
			$parts = pathinfo_utf($filename);
			if (empty($parts['extension']) || !in_array(strtolower($parts['extension']), $MEDIATYPE)) {
				$parts = pathinfo_utf($oldFilename);
				$filename .= '.'.$parts['extension'];
			}
		}
		if (!isset($folder) && isset($oldFolder)) $folder = $oldFolder;
		$folder = trim($folder);
		if (substr($folder, -1)!='/') $folder .= '/';
		if ($folder=='/') $folder = '';
		$folder = check_media_depth($folder.'y.z', 'BACK');
		$folder = dirname($folder).'/';
		if (substr($oldFolder, -1)!='/') $oldFolder .= '/';
		if ($oldFolder=='/') $oldFolder = '';
		$oldFolder = check_media_depth($oldFolder.'y.z', 'BACK');
		$oldFolder = dirname($oldFolder).'/';
	}

	if ($filename!=$oldFilename || $folder!=$oldFolder) {
		if ($multi_gedcom) {
			echo '<span class="error">', WT_I18N::translate('This file is linked to another genealogical database on this server.  It cannot be deleted, moved, or renamed until these links have been removed.'), '<br><br><b>';
			if ($filename!=$oldFilename) {
				echo WT_I18N::translate('Media file could not be moved or renamed.');
			} else {
				echo WT_I18N::translate('Media file could not be moved.');
			}
			echo '</b></span><br>';
			$finalResult = false;
		} else if (!$isExternal) {
			$oldMainFile = $oldFolder.$oldFilename;
			$newMainFile = $folder.$filename;
			$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldMainFile);
			$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $newMainFile);
			if (media_exists($oldMainFile) == 3) {
				// the file is in the media firewall folder
				$oldMainFile = get_media_firewall_path($oldMainFile);
				$newMainFile = get_media_firewall_path($newMainFile);
			}
			if (media_exists($oldThumFile) == 3) {
				$oldThumFile = get_media_firewall_path($oldThumFile);
				$newThumFile = get_media_firewall_path($newThumFile);
			}
			$isMain = file_exists(filename_decode($oldMainFile));
			$okMain = !file_exists(filename_decode($newMainFile));
			$isThum = file_exists(filename_decode($oldThumFile));
			$okThum = !file_exists(filename_decode($newThumFile));
			if ($okMain && $okThum) {
				// make sure the directories exist before moving the files
				mkdirs(dirname($newMainFile).'/');
				mkdirs(dirname($newThumFile).'/');
				if ($isMain) $okMain = @rename(filename_decode($oldMainFile), filename_decode($newMainFile));
				if ($isThum) $okThum = @rename(filename_decode($oldThumFile), filename_decode($newThumFile));
			}

			// Build text to tell Admin about the success or failure of the requested operation
			$mediaAction = 0;
			if ($filename!=$oldFilename) $mediaAction = 1;
			if ($folder!=$oldFolder) $mediaAction = $mediaAction + 2;

			if (!$isMain) {
				echo WT_I18N::translate(
					'Media file %s does not exist.',
					'<span class="filename">'.$oldFolder.$oldFilename.'</span>'
				);
			} else {
				if ($okMain) {
					echo WT_I18N::translate(
						'Media file %1$s successfully renamed to %2$s.',
						'<span class="filename">'.$oldFolder.$oldFilename.'</span>',
						'<span class="filename">'.$folder.$filename.'</span>'
					);
				} else {
					$finalResult = false;
					echo '<span class="error">';
					echo WT_I18N::translate(
						'Media file %1$s could not be renamed to %2$s.',
						'<span class="filename">'.$oldFolder.$oldFilename.'</span>',
						'<span class="filename">'.$folder.$filename.'</span>'
					);
					echo '</span>';
				}
			}
			echo '<br>';

			if (!$isThum) {
				echo WT_I18N::translate(
					'Thumbnail file %s does not exist.',
					'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldFolder).$oldFilename.'</span>'
				);
			} else {
				if ($okThum) {
					echo WT_I18N::translate(
						'Thumbnail file %1$s successfully renamed to %2$s.',
						'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldFolder).$oldFilename.'</span>',
						'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $folder).$filename.'</span>'
					);
				} else {
					$finalResult = false;
					echo '<span class="error">';
					echo WT_I18N::translate(
						'Thumbnail file %1$s could not be renamed to %2$s.',
						'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $oldFolder).$oldFilename.'</span>',
						'<span class="filename">'.str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY.'thumbs/', $folder).$filename.'</span>'
					);
					echo '</span>';
				}
			}
			echo '<br>';
		}
	}

	if ($finalResult) {
		// Insert the 1 FILE xxx record into the arrays used by function handle_updates()
		$glevels = array_merge(array('1'), $glevels);
		$tag = array_merge(array('FILE'), $tag);
		$islink = array_merge(array(0), $islink);
		$text = array_merge(array($folder.$filename), $text);

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
			$link = linkMedia($pid, $linktoid, $level);
			if ($link) {
				AddToLog('Media ID '.$pid." successfully added to $linktoid.", 'edit');
			}
		}
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	echo '<button onclick="closePopupAndReloadParent();">', WT_I18N::translate('close'), '</button>';
	break;
	
case 'delete':
	if (delete_gedrec($pid, WT_GED_ID)) {
		AddToLog('Media ID '.$pid.' successfully deleted.', 'edit');
		$controller->addInlineJavascript('closePopupAndReloadParent();');
	}
	echo '<button onclick="closePopupAndReloadParent();">', WT_I18N::translate('close'), '</button>';
	break;

case 'showmediaform':
	if (empty($level)) $level = 1;
	show_media_form($pid, 'newentry', $filename, $linktoid, $level);
	break;
	
case 'editmedia':
	if (empty($level)) $level = 1;
	show_media_form($pid, 'update', $filename, $linktoid, $level);
	break;
}


// print a form for editing or adding media items
// $pid      the id of the media item to edit
// $action   the action to take after the form is posted
// $filename allows you to provide a filename to go in the FILE tag for new media items
// $linktoid the id of the person/family/source to link a new media item to
// $level    The level at which this media item should be added
function show_media_form($pid, $action = 'newentry', $filename = '', $linktoid = '', $level = 1) {
	global $WORD_WRAPPED_NOTES, $ADVANCED_NAME_FACTS, $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY, $THUMBNAIL_WIDTH, $NO_UPDATE_CHAN;

	$AUTO_GENERATE_THUMBS=get_gedcom_setting(WT_GED_ID, 'AUTO_GENERATE_THUMBS');

	// NOTE: add a table and form to easily add new values to the table
	echo '<div id="addmedia-page">'; //container for media edit pop-up
	echo '<form method="post" name="newmedia" action="addmedia.php" enctype="multipart/form-data">';
	echo '<input type="hidden" name="action" value="', $action, '">';
	echo '<input type="hidden" name="ged" value="', WT_GEDCOM, '">';
	echo '<input type="hidden" name="pid" value="', $pid, '">';
	if (!empty($linktoid)) {
		echo '<input type="hidden" name="linktoid" value="', $linktoid, '">';
	}
	echo '<input type="hidden" name="level" value="', $level, '">';
	echo '<table class="facts_table">';
	echo '<tr><td class="topbottombar" colspan="2">';
	if ($action == 'newentry') {
		echo WT_I18N::translate('Create a new media object');
	} else {
		echo WT_I18N::translate('Edit media object');
	}
	echo help_link('OBJE');
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

	// 0 OBJE
	// 1 FILE
	if ($gedrec == '') {
		$gedfile = 'FILE';
		if ($filename != '')
			$gedfile = 'FILE ' . $filename;
	} else {
		$gedfile = get_first_tag(1, 'FILE', $gedrec);
		if (empty($gedfile))
			$gedfile = 'FILE';
	}
	if ($gedfile != 'FILE') {
		$gedfile = 'FILE ' . check_media_depth(substr($gedfile, 5));
		$readOnly = 'READONLY';
	} else {
		$readOnly = '';
	}
	if ($gedfile == 'FILE') {
		// Box for user to choose to upload file from local computer
		echo '<tr><td class="descriptionbox wrap width25">';
		echo WT_I18N::translate('Media file to upload').help_link('upload_media_file').'</td><td class="optionbox wrap"><input type="file" name="mediafile" onchange="updateFormat(this.value);" size="40"></td></tr>';
		// Check for thumbnail generation support
		if (WT_USER_GEDCOM_ADMIN) {
			$ThumbSupport = '';
		// Check for thumbnail generation support
			$thumbSupport = '';
			if ($AUTO_GENERATE_THUMBS) {
				if (function_exists('imagecreatefromgif') && function_exists('imagegif')) $thumbSupport .= ', GIF';
				if (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) $thumbSupport .= ', JPG';
				if (function_exists('imagecreatefrompng') && function_exists('imagepng')) $thumbSupport .= ', PNG';
			}

			if ($thumbSupport != '') {
				$thumbSupport = substr($thumbSupport, 2); // Trim off first “, ”
				echo '<tr><td class="descriptionbox wrap width25">';
				echo WT_I18N::translate('Automatic thumbnail'), help_link('generate_thumb');
				echo '</td><td class="optionbox wrap">';
				echo '<input type="checkbox" name="genthumb" value="yes" checked="checked">';
				echo '&nbsp;&nbsp;&nbsp;' . WT_I18N::translate('Generate thumbnail automatically from ') . $thumbSupport;
				echo '</td></tr>';
			}
			echo '<tr><td class="descriptionbox wrap width25">';
			echo WT_I18N::translate('Thumbnail to upload').help_link('upload_thumbnail_file').'</td><td class="optionbox wrap"><input type="file" name="thumbnail" size="40"></td></tr>';
		} else {
			echo '<input type="hidden" name="genthumb" value="yes">';
		}
	}
	// Filename on server
	$isExternal = isFileExternal($gedfile);
	if ($gedfile == 'FILE') {
		if (WT_USER_GEDCOM_ADMIN) {
			add_simple_tag("1 $gedfile", '', WT_I18N::translate('File name on server'), '', 'NOCLOSE');
			echo '<p class="sub">' . WT_I18N::translate('Do not change to keep original file name.');
			echo WT_I18N::translate('You may enter a URL, beginning with &laquo;http://&raquo;.') . '</p></td></tr>';
		}
		$fileName = '';
		$folder = '';
	} else {
		if ($isExternal) {
			$fileName = substr($gedfile, 5);
			$folder = '';
		} else {
			$parts = pathinfo_utf(substr($gedfile, 5));
			$fileName = $parts['basename'];
			$folder = $parts['dirname'] . '/';
		}

		echo '<tr>';
		echo '<td class="descriptionbox wrap width25">';
		echo '<input name="oldFilename" type="hidden" value="' . htmlspecialchars($fileName) . '">';
		echo WT_I18N::translate('File name on server'), help_link('upload_server_file');
		echo '</td>';
		echo '<td class="optionbox wrap wrap">';
		if (WT_USER_GEDCOM_ADMIN) {
			echo '<input name="filename" type="text" value="' . htmlspecialchars($fileName) . '" size="40"';
			if ($isExternal)
				echo '>';
			else
				echo '><p class="sub">' . WT_I18N::translate('Do not change to keep original file name.') . '</p>';
		} else {
			echo $fileName;
			echo '<input name="filename" type="hidden" value="' . htmlspecialchars($fileName) . '" size="40">';
		}
		echo '</td>';
		echo '</tr>';

	}

	// Box for user to choose the folder to store the image
	if (!$isExternal && $MEDIA_DIRECTORY_LEVELS > 0) {
		echo '<tr><td class="descriptionbox wrap width25">';
		// Strip $MEDIA_DIRECTORY from the folder name
		if (substr($folder, 0, strlen($MEDIA_DIRECTORY)) == $MEDIA_DIRECTORY) $folder = substr($folder, strlen($MEDIA_DIRECTORY));
		echo WT_I18N::translate('Folder name on server'), help_link('upload_server_folder'), '</td><td class="optionbox wrap">';
		//-- don’t let regular users change the location of media items
		if ($action!='update' || WT_USER_GEDCOM_ADMIN) {
			$mediaFolders = get_media_folders();
			echo '<span dir="ltr"><select name="folder_list" onchange="document.newmedia.folder.value=this.options[this.selectedIndex].value;">';
			echo '<option';
			if ($folder == '/') echo ' selected="selected"';
			echo ' value="/"> ', WT_I18N::translate('Choose: '), ' </option>';
			if (WT_USER_IS_ADMIN) echo '<option value="other" disabled>', WT_I18N::translate('Other folder... please type in'), "</option>";
			foreach ($mediaFolders as $f) {
				if (!strpos($f, ".svn")) {    //Do not echo subversion directories
					// Strip $MEDIA_DIRECTORY from the folder name
					if (substr($f, 0, strlen($MEDIA_DIRECTORY)) == $MEDIA_DIRECTORY) $f = substr($f, strlen($MEDIA_DIRECTORY));
					if ($f == '') $f = '/';
					echo '<option value="', $f, '"';
					if ($folder == $f && $f != '/')
						echo ' selected="selected"';
					echo '>', $f, "</option>";
				}
			}
			echo '</select></span>';
		}
		else echo $folder;
		echo '<input name="oldFolder" type="hidden" value="', addslashes($folder), '">';
		if (WT_USER_IS_ADMIN) {
			echo '<br><span dir="ltr"><input type="text" name="folder" size="40" value="', $folder, '" onblur="checkpath(this)"></span>';
			if ($MEDIA_DIRECTORY_LEVELS>0) {
				echo '<p class="sub">', WT_I18N::translate('You can enter up to %s folder names to follow the default &laquo;%s&raquo;.<br />Do not enter the &laquo;%s&raquo; part of the destination folder name.', $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY), '</p>';
			}
			if ($gedfile == 'FILE') {
				echo '<p class="sub">', WT_I18N::translate('This entry is ignored if you have entered a URL into the file name field.'), '</p>';
			}
		} else echo '<input name="folder" type="hidden" value="', addslashes($folder), '">';
		echo '</td></tr>';
	} else {
		echo '<input name="oldFolder" type="hidden" value="">';
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
	?>
	<script>
		var formid = '<?php echo $formid; ?>';
		function updateFormat(filename) {
			var extsearch=/\.([a-zA-Z]{3,4})$/;
			ext='';
			if (extsearch.exec(filename)) {
				ext = RegExp.$1.toLowerCase();
				if (ext=='jpg') ext='jpeg';
				if (ext=='tif') ext='tiff';
			}
			formfield = document.getElementById(formid);
			formfield.value = ext;
		}
	</script>
			<?php
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
	<?php
}
