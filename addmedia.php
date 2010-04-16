<?php
/**
 * Add media to gedcom file
 *
 * This file allows the user to maintain a seperate table
 * of media files and associate them with individuals in the gedcom
 * and then add these records later.
 * Requires SQL mode.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @package webtrees
 * @subpackage MediaDB
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'addmedia.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_print_lists.php';
require WT_ROOT.'includes/functions/functions_edit.php';

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

$filename = decrypt($filename);
$oldFilename = decrypt($oldFilename);

print_simple_header(i18n::translate('Add a new Media item'));
$disp = true;
if (empty($pid) && !empty($mid)) $pid = $mid;
if (!empty($pid)) {
	if (!isset($pgv_changes[$pid."_".WT_GEDCOM])) {
		$gedrec = find_media_record($pid, WT_GED_ID);
	} else {
		$gedrec = find_updated_record($pid, WT_GED_ID);
	}
	$disp = displayDetailsById($pid, "OBJE");
}
if ($action=="update" || $action=="newentry") {
	if (!isset($linktoid) || $linktoid=="new") $linktoid="";
	if (empty($linktoid) && !empty($gid)) $linktoid = $gid;
	if (!empty($linktoid)) {
		$disp = displayDetailsById($linktoid);
	}
}

if (!WT_USER_CAN_EDIT || !$disp || !$ALLOW_EDIT_GEDCOM) {
	print i18n::translate('<b>Access Denied</b><br />You do not have access to this resource.');
	//-- display messages as to why the editing access was denied
	if (!WT_USER_CAN_EDIT) print "<br />".i18n::translate('This user name cannot edit this GEDCOM.');
	if (!$ALLOW_EDIT_GEDCOM) print "<br />".i18n::translate('Editing this GEDCOM has been disabled by the administrator.');
	if (!$disp) {
		print "<br />".i18n::translate('Privacy settings prevent you from editing this record.');
		if (!empty($pid)) print "<br />".i18n::translate('You have no access to')." pid $pid.";
	}
	print "<br /><br /><div class=\"center\"><a href=\"javascript: ".i18n::translate('Close Window')."\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".i18n::translate('Close Window')."</a></div>\n";
	print_simple_footer();
	exit;
}

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'/js/autocomplete.js.htm';
echo WT_JS_START;
?>
	// Shared Notes =========================
	function findnote(field) {
		pastefield = field;
		findwin = window.open('find.php?type=note', '_blank', 'left=50, top=50, width=600, height=520, resizable=1, scrollbars=1');
		return false;
	}
	var language_filter, magnify;
	var pastefield;
	language_filter = "";
	magnify = "";
	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}
	function paste_id(value) {
		pastefield.value = value;
	}
	function paste_char(value, lang, mag) {
		pastefield.value += value;
		language_filter = lang;
		magnify = mag;
	}
	function checkpath(folder) {
		value = folder.value;
		if (value.substr(value.length-1, 1) == "/") value = value.substr(0, value.length-1);
		if (value.substr(0, 1) == "/") value = value.substr(1, value.length-1);
		result = value.split("/");
		if (result.length > <?php print $MEDIA_DIRECTORY_LEVELS; ?>) {
			alert('<?php echo i18n::translate('You can enter no more than %s subdirectory names', $MEDIA_DIRECTORY_LEVELS); ?>');
			folder.focus();
			return false;
		}
	}
<?php
echo WT_JS_END;

// Naming conventions used in this script:
// folderName - this is the link to the folder in the standard media directory; the one that is stored in the gedcom.
// serverFolderName - this is where the file is physically located.  if the media firewall is enabled it is in the protected media directory.  if not it is the same as folderName.
// thumbFolderName - this is the link to the thumb folder in the standard media directory
// serverThumbFolderName - this is where the thumbnail file is physically located

if (empty($action)) $action="showmediaform";

// **** begin action "newentry"
// NOTE: Store the entered data
if ($action=="newentry") {
	if (empty($level)) $level = 1;

	$error = "";
	$mediaFile = "";
	$thumbFile = "";
	if (!empty($_FILES['mediafile']["name"]) || !empty($_FILES['thumbnail']["name"])) {
		// Validate and correct folder names
		$folderName = trim(trim(safe_POST('folder', WT_REGEX_NOSCRIPT)), '/');
		$folderName = check_media_depth($folderName."/y.z", "BACK");
		$folderName = dirname($folderName)."/";
		$thumbFolderName = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folderName);

		$_SESSION["upload_folder"] = $folderName; // store standard media folder in session
		$realFolderName = $folderName;
		$realThumbFolderName = $thumbFolderName;
		if ($USE_MEDIA_FIREWALL) {
			$realFolderName = get_media_firewall_path($folderName);
			if ($MEDIA_FIREWALL_THUMBS) $realThumbFolderName = get_media_firewall_path($thumbFolderName);
		}
		// make sure the dirs exist
		@mkdirs($realFolderName);
		@mkdirs($realThumbFolderName);

		$error = "";

		// Determine file name on server
		if (WT_USER_GEDCOM_ADMIN && !empty($text[0])) $fileName = trim(trim($text[0]), '/');
		else $fileName = '';
		$parts = pathinfo_utf($fileName);
		if (!empty($parts["basename"])) {
			// User supplied a name to be used on the server
			$mediaFile = $parts["basename"];	// Use the supplied name
			if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
				// Strip invalid extension from supplied name
				$lastDot = strrpos($mediaFile, '.');
				if ($lastDot !== false) $mediaFile = substr($mediaFile, 0, $lastDot);
				// Use extension of original uploaded file name
				if (!empty($_FILES["mediafile"]["name"])) $parts = pathinfo_utf($_FILES["mediafile"]["name"]);
				else $parts = pathinfo_utf($_FILES["thumbnail"]["name"]);
				if (!empty($parts["extension"])) $mediaFile .= ".".$parts["extension"];
			}
		} else {
			// User did not specify a name to be used on the server:  use the original uploaded file name
			if (!empty($_FILES["mediafile"]["name"])) $parts = pathinfo_utf($_FILES["mediafile"]["name"]);
			else $parts = pathinfo_utf($_FILES["thumbnail"]["name"]);
			$mediaFile = $parts["basename"];
		}
		if (!empty($_FILES["mediafile"]["name"])) {
			$newFile = $realFolderName.$mediaFile;
			// Copy main media file into the destination directory
			if (file_exists(filename_decode($newFile))) {
				$error .= i18n::translate('Media file already exists.')."&nbsp;&nbsp;".$newFile."<br />";
			} else {
				if (!move_uploaded_file($_FILES["mediafile"]["tmp_name"], filename_decode($newFile))) {
					// the file cannot be copied
					$error .= i18n::translate('There was an error uploading your file.')."<br />".file_upload_error_text($_FILES["mediafile"]["error"])."<br />";
				} else {
					@chmod(filename_decode($newFile), WT_PERM_FILE);
					AddToLog("Media file {$folderName}{$mediaFile} uploaded", 'media');
				}
			}
		}
		if ($error=="" && !empty($_FILES["thumbnail"]["name"])) {
			$newThum = $realThumbFolderName.$mediaFile;
			// Copy user-supplied thumbnail file into the destination directory
			if (file_exists(filename_decode($newThum))) {
				$error .= i18n::translate('Media thumbnail already exists.')."&nbsp;&nbsp;".$newThum."<br />";
			} else {
				if (!move_uploaded_file($_FILES["thumbnail"]["tmp_name"], filename_decode($newThum))) {
					// the file cannot be copied
					$error .= i18n::translate('There was an error uploading your file.')."<br />".file_upload_error_text($_FILES["thumbnail"]["error"])."<br />";
				} else {
					@chmod(filename_decode($newThum), WT_PERM_FILE);
					AddToLog("Media file {$thumbFolderName}{$mediaFile} uploaded", 'media');
				}
			}
		}
		if ($error=="" && empty($_FILES["mediafile"]["name"]) && !empty($_FILES["thumbnail"]["name"])) {
			// Copy user-supplied thumbnail file into the main destination directory
			if (!copy(filename_decode($whichFile1), filename_decode($whichFile2))) {
				// the file cannot be copied
				$error .= i18n::translate('There was an error uploading your file.')."<br />".i18n::translate('The file %s could not be copied from %s', $realThumbFolderName.$mediaFile, $realThumbFolderName.$mediaFile)."<br />";
			} else {
				@chmod(filename_decode($whichFile2), WT_PERM_FILE);
				AddToLog("Media file {$folderName}{$mediaFile} copied from {$thumbFolderName}{$mediaFile}", 'media');
			}
		}
		if ($error=="" && !empty($_FILES["mediafile"]["name"]) && empty($_FILES["thumbnail"]["name"])) {
			if (safe_POST('genthumb', 'yes', 'no') == 'yes') {
				// Generate thumbnail from main image
				$parts = pathinfo_utf($mediaFile);
				if (!empty($parts["extension"])) {
					$ext = strtolower($parts["extension"]);
					if (isImageTypeSupported($ext)) {
						$thumbnail = $thumbFolderName.$mediaFile;
						$okThumb = generate_thumbnail($folderName.$mediaFile, $thumbnail, "OVERWRITE");
						if (!$okThumb) {
							$error .= i18n::translate('Thumbnail %s could not be generated automatically.', $thumbnail);
						} else {
							echo i18n::translate('Thumbnail %s generated automatically.', $thumbnail);
							print "<br />";
							AddToLog("Media thumbnail {$thumbnail} generated", 'media');
						}
					}
				}
			}
		}
		// Let's see if there are any errors generated and print it
		if (!empty($error)) {
			echo '<span class="error">', $error, "</span><br />\n";
			$mediaFile = "";
			$finalResult = false;
		} else $finalResult = true;
	}
	if ($mediaFile=="") {
		// No upload: should be an existing file on server
		if ($tag[0]=="FILE") {
			if (!empty($text[0])) {
				$isExternal = isFileExternal($text[0]);
				if ($isExternal) {
					$fileName = $text[0];
					$mediaFile = $fileName;
					$folderName = "";
				} else {
					$fileName = check_media_depth($text[0], "BACK");
					$mediaFile = basename($fileName);
					$folderName = dirname($fileName)."/";
				}
			}
			if ($mediaFile=="") {
				echo '<span class="error">', i18n::translate('Blank name or illegal characters in name'), "</span><br />\n";
				$finalResult = false;
			} else $finalResult = true;
		} else {
			//-- check if the file is used in more than one gedcom
			//-- do not allow it to be moved or renamed if it is
			$myFile = str_replace($MEDIA_DIRECTORY, "", $oldFolder.$oldFilename);
			$multi_gedcom=is_media_used_in_other_gedcom($myFile, WT_GED_ID);

			// Handle Admin request to rename or move media file
			if ($filename!=$oldFilename) {
				$parts = pathinfo_utf($filename);
				if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
					$parts = pathinfo_utf($oldFilename);
					$filename .= ".".$parts["extension"];
				}
			}
			if (substr($folder, -1)!="/") $folder .= "/";
			if ($folder=="/") $folder = "";
			$folder = check_media_depth($folder."y.z", "BACK");
			$folder = dirname($folder)."/";
			if (substr($oldFolder, -1)!="/") $oldFolder .= "/";
			if ($oldFolder=="/") $oldFolder = "";
			$oldFolder = check_media_depth($oldFolder."y.z", "BACK");
			$oldFolder = dirname($oldFolder)."/";
			$_SESSION["upload_folder"] = $folder; // store standard media folder in session

			$finalResult = true;
			if ($filename!=$oldFilename || $folder!=$oldFolder) {
				if ($multi_gedcom) {
					echo '<span class="error">', i18n::translate('This file is linked to another genealogical database on this server.  It cannot be deleted, moved, or renamed until these links have been removed.'), '<br /><br /><b>';
					if ($filename!=$oldFilename) print i18n::translate('Media file could not be moved or renamed.');
					else print i18n::translate('Media file could not be moved.');
					print "</b></span><br />";
					$finalResult = false;
				} else {
					$oldMainFile = $oldFolder.$oldFilename;
					$newMainFile = $folder.$filename;
					$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldMainFile);
					$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $newMainFile);
					if (media_exists($oldMainFile) == 3) {
						// the file is in the media firewall directory
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
						mkdirs(dirname($newMainFile)."/");
						mkdirs(dirname($newThumFile)."/");
						if ($isMain) $okMain = @rename(filename_decode($oldMainFile), filename_decode($newMainFile));
						if ($isThum) $okThum = @rename(filename_decode($oldThumFile), filename_decode($newThumFile));
					}

					// Build text to tell Admin about the success or failure of the requested operation
					$mediaAction = 0;
					if ($filename!=$oldFilename) $mediaAction = 1;
					if ($folder!=$oldFolder) $mediaAction = $mediaAction + 2;

					if (!$isMain) {
						echo i18n::translate('Main media file <b>%s%s</b> does not exist.', $oldFolder, $oldFilename);
					} else {
						if ($okMain) {
							switch ($mediaAction) {
							case 1:
								echo i18n::translate('Main media file <b>%s</b> successfully renamed to <b>%s</b>.', $oldFilename, $filename);
								break;
							case 2:
								echo i18n::translate('Main media file <b>%s</b> successfully moved from <b>%s</b> to <b>%s</b>.', $oldFilename, $oldFolder, $folder);
								break;
							case 3:
								echo i18n::translate('Main media file successfully moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', $oldFolder, $oldFilename, $folder, $filename);
								break;
							}
						} else {
							$finalResult = false;
							echo '<span class="error">';
							switch ($mediaAction) {
							case 1:
								echo i18n::translate('Main media file <b>%s</b> could not be renamed to <b>%s</b>.', $oldFilename, $filename);
								break;
							case 2:
								echo i18n::translate('Main media file <b>%s</b> could not be moved from <b>%s</b> to <b>%s</b>.', $oldFilename, $oldFolder, $folder);
								break;
							case 3:
								echo i18n::translate('Main media file could not be moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', $oldFolder, $oldFilename, $folder, $filename);
								break;
							}
							echo '</span>';
						}
					}
					print "<br />";

					if (!$isThum) {
						echo i18n::translate('Thumbnail file <b>%s%s</b> does not exist.', $oldFolder, $oldFilename);
					} else {
						if ($okThum) {
							switch ($mediaAction) {
							case 1:
								echo i18n::translate('Thumbnail file <b>%s</b> successfully renamed to <b>%s</b>.', $oldFilename, $filename);
								break;
							case 2:
								echo i18n::translate('Thumbnail file <b>%s</b> successfully moved from <b>%s</b> to <b>%s</b>.', $oldFilename, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder));
								break;
							case 3:
								echo i18n::translate('Thumbnail file successfully moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), $oldFilename, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder), $filename);
								break;
							}
						} else {
							$finalResult = false;
							echo '<span class="error">';
							switch ($mediaAction) {
							case 1:
								echo i18n::translate('Main media file <b>%s</b> could not be renamed to <b>%s</b>.', $oldFilename, $filename);
								break;
							case 2:
								echo i18n::translate('Main media file <b>%s</b> could not be moved from <b>%s</b> to <b>%s</b>.', $oldFilename, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder));
								break;
							case 3:
								echo i18n::translate('Main media file could not be moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), $oldFilename, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder), $filename);
								break;
							}
							echo '</span>';
						}
					}
					print "<br />";
				}
			}

			// Insert the 1 FILE xxx record into the arrays used by function handle_updates()
			$glevels = array_merge(array("1"), $glevels);
			$tag = array_merge(array("FILE"), $tag);
			$islink = array_merge(array(0), $islink);
			$text = array_merge(array($folder.$filename), $text);

			$mediaFile = $filename;
			$folderName = $folder;
		}
	}

	if ($finalResult && $mediaFile!="") {
		// NOTE: Build the gedcom record
		// NOTE: Level 0
		$media_id = get_new_xref("OBJE");
		$newged = "0 @".$media_id."@ OBJE\n";
		//-- set the FILE text to the correct file location in the standard media directory
		if (WT_USER_GEDCOM_ADMIN) $text[0] = $folderName.$mediaFile;
		else $newged .= "1 FILE ".$folderName.$mediaFile."\n";

		$newged = handle_updates($newged);

		require_once WT_ROOT.'includes/classes/class_media.php';
		$media_obje = new Media($newged);
		$mediaid = Media::in_obje_list($media_obje);
		if (!$mediaid) $mediaid = append_gedrec($newged, $linktoid);
		if ($mediaid) {
			AddToChangeLog("Media ID ".$mediaid." successfully added.");
			if ($linktoid!="") $link = linkMedia($mediaid, $linktoid, $level);
			else $link = false;
			if ($link) {
				AddToChangeLog("Media ID ".$media_id." successfully added to $linktoid.");
			} else {
				echo "<a href=\"javascript://OBJE $mediaid\" onclick=\"openerpasteid('$mediaid'); return false;\">", i18n::translate('Paste the following ID into your editing fields to reference the newly created record '), " <b>$mediaid</b></a><br /><br />\n";
				echo WT_JS_START;
				echo "openerpasteid('", $mediaid, "');";
				echo WT_JS_END;
			}
		}
		print i18n::translate('Update successful');
	}
}
// **** end action "newentry"

// **** begin action "update"
if ($action == "update") {
	if (empty($level)) $level = 1;
	//-- check if the file is used in more than one gedcom
	//-- do not allow it to be moved or renamed if it is
	$myFile = str_replace($MEDIA_DIRECTORY, "", $oldFolder.$oldFilename);
	$multi_gedcom=is_media_used_in_other_gedcom($myFile, WT_GED_ID);

	$isExternal = isFileExternal($oldFilename) || isFileExternal($filename);
	$finalResult = true;

	// Handle Admin request to rename or move media file
	if (!$isExternal) {
		if ($filename!=$oldFilename) {
			$parts = pathinfo_utf($filename);
			if (empty($parts["extension"]) || !in_array(strtolower($parts["extension"]), $MEDIATYPE)) {
				$parts = pathinfo_utf($oldFilename);
				$filename .= ".".$parts["extension"];
			}
		}
		if (!isset($folder) && isset($oldFolder)) $folder = $oldFolder;
		$folder = trim($folder);
		if (substr($folder, -1)!="/") $folder .= "/";
		if ($folder=="/") $folder = "";
		$folder = check_media_depth($folder."y.z", "BACK");
		$folder = dirname($folder)."/";
		if (substr($oldFolder, -1)!="/") $oldFolder .= "/";
		if ($oldFolder=="/") $oldFolder = "";
		$oldFolder = check_media_depth($oldFolder."y.z", "BACK");
		$oldFolder = dirname($oldFolder)."/";
	}

	if ($filename!=$oldFilename || $folder!=$oldFolder) {
		if ($multi_gedcom) {
			echo '<span class="error">', i18n::translate('This file is linked to another genealogical database on this server.  It cannot be deleted, moved, or renamed until these links have been removed.'), '<br /><br /><b>';
			if ($filename!=$oldFilename) print i18n::translate('Media file could not be moved or renamed.');
			else print i18n::translate('Media file could not be moved.');
			print "</b></span><br />";
			$finalResult = false;
		} else if (!$isExternal) {
			$oldMainFile = $oldFolder.$oldFilename;
			$newMainFile = $folder.$filename;
			$oldThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldMainFile);
			$newThumFile = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $newMainFile);
			if (media_exists($oldMainFile) == 3) {
				// the file is in the media firewall directory
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
				mkdirs(dirname($newMainFile)."/");
				mkdirs(dirname($newThumFile)."/");
				if ($isMain) $okMain = @rename(filename_decode($oldMainFile), filename_decode($newMainFile));
				if ($isThum) $okThum = @rename(filename_decode($oldThumFile), filename_decode($newThumFile));
			}

			// Build text to tell Admin about the success or failure of the requested operation
			$mediaAction = 0;
			if ($filename!=$oldFilename) $mediaAction = 1;
			if ($folder!=$oldFolder) $mediaAction = $mediaAction + 2;

			if (!$isMain) {
				echo i18n::translate('Main media file <b>%s%s</b> does not exist.', $oldFolder, $oldFilename);
			} else {
				if ($okMain) {
					switch ($mediaAction) {
					case 1:
						echo i18n::translate('Main media file <b>%s</b> successfully renamed to <b>%s</b>.', $oldFilename, $filename);
						break;
					case 2:
						echo i18n::translate('Main media file <b>%s</b> successfully moved from <b>%s</b> to <b>%s</b>.', $oldFilename, $oldFolder, $folder);
						break;
					case 3:
						echo i18n::translate('Main media file successfully moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', $oldFolder, $oldFilename, $folder, $filename);
						break;
					}
				} else {
					$finalResult = false;
					echo '<span class="error">';
					switch ($mediaAction) {
					case 1:
						echo i18n::translate('Main media file <b>%s</b> could not be renamed to <b>%s</b>.', $oldFilename, $filename);
						break;
					case 2:
						echo i18n::translate('Main media file <b>%s</b> could not be moved from <b>%s</b> to <b>%s</b>.', $oldFilename, $oldFolder, $folder);
						break;
					case 3:
						echo i18n::translate('Main media file could not be moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', $oldFolder, $oldFilename, $folder, $filename);
						break;
					}
					echo '</span>';
				}
			}
			print "<br />";

			if (!$isThum) {
				echo i18n::translate('Thumbnail file <b>%s%s</b> does not exist.', str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), $oldFilename);
			} else {
				if ($okThum) {
					switch ($mediaAction) {
					case 1:
						echo i18n::translate('Thumbnail file <b>%s</b> successfully renamed to <b>%s</b>.', $oldFilename, $filename);
						break;
					case 2:
						echo i18n::translate('Thumbnail file <b>%s</b> successfully moved from <b>%s</b> to <b>%s</b>.', $oldFilename, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder));
						break;
					case 3:
						echo i18n::translate('Thumbnail file successfully moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), $oldFilename, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder), $filename);
						break;
					}
				} else {
					$finalResult = false;
					echo '<span class="error">';
					switch ($mediaAction) {
					case 1:
						echo i18n::translate('Main media file <b>%s</b> could not be renamed to <b>%s</b>.', $oldFilename, $filename);
						break;
					case 2:
						echo i18n::translate('Main media file <b>%s</b> could not be moved from <b>%s</b> to <b>%s</b>.', $oldFileName, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder));
						break;
					case 3:
						echo i18n::translate('Main media file could not be moved and renamed from <b>%s%s</b> to <b>%s%s</b>.', str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $oldFolder), $oldFilename, str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $folder), $filename);
						break;
					}
					echo '</span>';
				}
			}
			print "<br />";
		}
	}

	if ($finalResult) {
		$_SESSION["upload_folder"] = $folder; // store standard media folder in session

		// Insert the 1 FILE xxx record into the arrays used by function handle_updates()
		$glevels = array_merge(array("1"), $glevels);
		$tag = array_merge(array("FILE"), $tag);
		$islink = array_merge(array(0), $islink);
		$text = array_merge(array($folder.$filename), $text);

		if (!empty($pid)) {
			if (!isset($pgv_changes[$pid."_".WT_GEDCOM])) {
				$gedrec = find_gedcom_record($pid, WT_GED_ID);
			} else {
				$gedrec = find_updated_record($pid, WT_GED_ID);
			}
		}
		$newrec = "0 @$pid@ OBJE\n";
		$newrec = handle_updates($newrec);
		if (!$update_CHAN) {
			$newrec .= get_sub_record(1, "1 CHAN", $gedrec);
		}
		//print("[".$newrec."]");
		//-- look for the old record media in the file
		//-- if the old media record does not exist that means it was
		//-- generated at import and we need to append it
		if (replace_gedrec($pid, $newrec, $update_CHAN)) {
			AddToChangeLog("Media ID ".$pid." successfully updated.");
		}

		if ($pid && $linktoid!="") {
			$link = linkMedia($pid, $linktoid, $level);
			if ($link) {
				AddToChangeLog("Media ID ".$pid." successfully added to $linktoid.");
			}
		}
	}

	if ($finalResult) print i18n::translate('Update successful');
}
// **** end action "update"

// **** begin action "delete"
if ($action=="delete") {
	if (delete_gedrec($pid)) {
		AddToChangeLog("Media ID ".$pid." successfully deleted.");
		print i18n::translate('Update successful');
	}
}
// **** end action "delete"

// **** begin action "showmediaform"
if ($action=="showmediaform") {
	if (!isset($pid)) $pid = "";
	if (empty($level)) $level = 1;
	if (!isset($linktoid)) $linktoid = "";
	show_media_form($pid, "newentry", $filename, $linktoid, $level);
}
// **** end action "showmediaform"


// **** begin action "editmedia"
if ($action=="editmedia") {
	if (!isset($pid)) $pid = "";
	if (empty($level)) $level = 1;
	show_media_form($pid, "update", $filename, $linktoid, $level);
}
// **** end action "editmedia"

print "<br />";
print "<div class=\"center\"><a href=\"#\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">".i18n::translate('Close Window')."</a></div>\n";
print "<br />";
print_simple_footer();
?>
