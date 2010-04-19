<?php
/**
 * Popup window that will allow a user to search for a media
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * @author PGV Development Team
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */

 /* TODO:
 *	Add check for missing index.php files when creating a directory
 *	Add an option to generate thumbnails for all files on the page
 *	Add filter for correct media like php, gif etc.
 *  Check for URL instead of physical file
 *	Check array buld up use ID_GEDCOM for aray key
 */

 /* Standard variable convention media.php
 *	$filename = Filename of the media item
 *	$thumbnail = Filename of the thumbnail of the media item
 *	$gedfile = Name of the GEDCOM file
 *	$medialist = Array with all media items
 *  $directory = Current directory, starting with $MEDIA_DIRECTORY.  Has trailing "/".
 *  $dirs = list of subdirectories within current directory.  Built with medialist.
 */

define('WT_SCRIPT_NAME', 'media.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_import.php';
require_once WT_ROOT.'includes/functions/functions_mediadb.php';

/**
 * This functions checks if an existing directory is physically writeable
 * The standard PHP function only checks for the R/O attribute and doesn't
 * detect authorisation by ACL.
 */
function dir_is_writable($dir) {
	$err_write = false;
	$handle = @fopen(filename_decode($dir."x.y"), "w+");
	if	($handle) {
		$i = fclose($handle);
		$err_write = true;
		@unlink(filename_decode($dir."x.y"));
	}
	return($err_write);
}

/**
 * Moves a file from one location to another, creating destination directory if needed
 * used by the routines that move files between the standard media directory and the protected media directory
 */
function move_file($src, $dest) {
	global $MEDIA_FIREWALL_ROOTDIR, $MEDIA_DIRECTORY;

	// sometimes thumbnail files are set to something like "images/media.gif", this ensures we do not move them
	// check to make sure the src file is in the standard or protected media directories
	if (preg_match("'^($MEDIA_FIREWALL_ROOTDIR)?$MEDIA_DIRECTORY'", $src)==0){
		return false;
	}
	// check to make sure the dest file is in the standard or protected media directories
	if (preg_match("'^($MEDIA_FIREWALL_ROOTDIR)?$MEDIA_DIRECTORY'", $dest)==0){
		return false;
	}

	$destdir = dirname($dest);
	if (!is_dir($destdir)) {
		@mkdirs($destdir);
		if (!is_dir($destdir)) {
			print "<div class=\"error\">".i18n::translate('Directory could not be created')." [".$destdir."]</div>";
			return false;
		}
	}
	if(!rename($src, $dest)) {
		print "<div class=\"error\">".i18n::translate('Media file could not be moved.')." [".$src."]</div>";
		return false;
	}
	print "<div>".i18n::translate('Media file moved.')." [".$src."]</div>";
	return true;
}

/**
* Recursively moves files from standard media directory to the protected media directory
* and vice-versa.  Operates directly on the filesystem, does not use the db.
*/
function move_files($path, $protect) {
	global $MEDIA_FIREWALL_THUMBS, $starttime;
	$timelimit=get_site_setting('MAX_EXECUTION_TIME');
	if ($dir=@opendir($path)) {
		while (($element=readdir($dir))!== false) {
			$exectime = time() - $starttime;
			if (($timelimit != 0) && ($timelimit - $exectime) < 3) {
				// bail now to ensure nothing is lost
				print "<div class=\"error\">".i18n::translate('The execution time limit was reached.  Try the command again to move the rest of the files.')."</div>";
				return;
			}
			// do not move certain files...
			if ($element!= "." && $element!= ".." && $element!=".svn" && $element!="watermark" && $element!="thumbs" && $element!=".htaccess" && $element!="index.php" && $element!="MediaInfo.txt" && $element!="ThumbsInfo.txt") {
				$filename = $path."/".$element;
				if (is_dir($filename)) {
					// call this function recursively on this directory
					move_files($filename, $protect);
				} else {
					if ($protect) {
						// Move single file and optionally its corresponding thumbnail to protected dir
						if (file_exists($filename)) {
							move_file($filename, get_media_firewall_path($filename));
						}
						if ($MEDIA_FIREWALL_THUMBS) {
							$thumbnail = thumbnail_file($filename, false);
							if (file_exists($thumbnail)) {
								move_file($thumbnail, get_media_firewall_path($thumbnail));
							}
						}
					} else {
						// Move single file and its corresponding thumbnail to standard dir
						$filename = get_media_standard_path($filename);
						if (file_exists(get_media_firewall_path($filename))) {
							move_file(get_media_firewall_path($filename), $filename);
						}
						$thumbnail = thumbnail_file($filename, false);
						if (file_exists(get_media_firewall_path($thumbnail))) {
							move_file(get_media_firewall_path($thumbnail), $thumbnail);
						}
					}
				}
			}
		}
		print "</td></tr></table>";
		$action="filter";
		closedir($dir);
	}
	return;
}

/**
* Recursively sets the permissions on files
* Operates directly on the filesystem, does not use the db.
*/
function set_perms($path) {
	global $MEDIA_FIREWALL_ROOTDIR, $MEDIA_DIRECTORY, $starttime;
	if (preg_match("'^($MEDIA_FIREWALL_ROOTDIR)?$MEDIA_DIRECTORY'", $path."/")==0){
		return false;
	}
	$timelimit=get_site_setting('MAX_EXECUTION_TIME');
	if ($dir=@opendir($path)) {
		while (($element=readdir($dir))!== false) {
			$exectime = time() - $starttime;
			if (($timelimit != 0) && ($timelimit - $exectime) < 3) {
				// bail now to ensure nothing is lost
				print "<div class=\"error\">".i18n::translate('The execution time limit was reached.  Try the command again on a smaller directory.')."</div>";
				return;
			}
			// do not set perms on certain files...
			if ($element!= "." && $element!= ".." && $element!=".svn") {
				$fullpath = $path."/".$element;
				if (is_dir($fullpath)) {
					if (@chmod($fullpath, WT_PERM_EXE)) {
						print "<div>".i18n::translate('Permissions Set')." [".decoct(WT_PERM_EXE)."] [".$fullpath."]</div>";
					} else {
						print "<div>".i18n::translate('Permissions Not Set')." [".decoct(WT_PERM_EXE)."] [".$fullpath."]</div>";
					}
					// call this function recursively on this directory
					set_perms($fullpath);
				} else {
					if (@chmod($fullpath, WT_PERM_FILE)) {
						print "<div>".i18n::translate('Permissions Set')." [".decoct(WT_PERM_FILE)."] [".$fullpath."]</div>";
					} else {
						print "<div>".i18n::translate('Permissions Not Set')." [".decoct(WT_PERM_FILE)."] [".$fullpath."]</div>";
					}
				}
			}
		}
		closedir($dir);
	}
	return;
}

// global var used by recursive functions
$starttime = time();

// TODO Determine source and validation requirements for these variables
$filename=decrypt(safe_REQUEST($_REQUEST, 'filename'));
$directory=safe_REQUEST($_REQUEST, 'directory', WT_REGEX_NOSCRIPT, $MEDIA_DIRECTORY);
$movetodir=safe_REQUEST($_REQUEST, 'movetodir');
$movefile=safe_REQUEST($_REQUEST, 'movefile');
$action=safe_REQUEST($_REQUEST, 'action', WT_REGEX_ALPHA, 'filter');
$subclick=safe_REQUEST($_REQUEST, 'subclick', WT_REGEX_ALPHA, 'none');
$media=safe_REQUEST($_REQUEST, 'media');
$filter=safe_REQUEST($_REQUEST, 'filter', WT_REGEX_NOSCRIPT);
$sortby=safe_REQUEST($_REQUEST, 'sortby', 'file', 'title');
$level=safe_REQUEST($_REQUEST, 'level', WT_REGEX_INTEGER, 0);
$start=safe_REQUEST($_REQUEST, 'start', WT_REGEX_INTEGER, 0);
$max=safe_REQUEST($_REQUEST, 'max', WT_REGEX_INTEGER, 20);

$showthumb=safe_REQUEST($_REQUEST, 'showthumb');

$all=safe_REQUEST($_REQUEST, 'all', 'yes', 'no');

if (isset($_REQUEST['xref'])) $xref = $_REQUEST['xref'];

if (count($_POST) == 0) $showthumb = true;

$thumbget = "";
if ($showthumb) $thumbget = "&showthumb=true";

//-- prevent script from accessing an area outside of the media directory
//-- and keep level consistency
if (($level < 0) || ($level > $MEDIA_DIRECTORY_LEVELS)){
	$directory = $MEDIA_DIRECTORY;
	$level = 0;
} elseif (preg_match("'^$MEDIA_DIRECTORY'", $directory)==0){
	$directory = $MEDIA_DIRECTORY;
	$level = 0;
}

$thumbdir = str_replace($MEDIA_DIRECTORY, $MEDIA_DIRECTORY."thumbs/", $directory);
$directory_fw = get_media_firewall_path($directory);
$thumbdir_fw = get_media_firewall_path($thumbdir);

//-- only allow users with Admin privileges to access script.
if (!WT_USER_IS_ADMIN || !$ALLOW_EDIT_GEDCOM) {
	header("Location: login.php?url=media.php");
	exit;
}

//-- TODO add check for -- admin can manipulate files
$fileaccess = false;
if (WT_USER_IS_ADMIN) {
	$fileaccess = true;
}

// Print the header of the page
print_header(i18n::translate('Manage MultiMedia'));
?>
<script language="JavaScript" type="text/javascript">
<!--
function pasteid(id) {
	window.opener.paste_id(id);
	window.close();
}

function ilinkitem(mediaid, type) {
	window.open('inverselink.php?mediaid='+mediaid+'&linkto='+type+'&'+sessionname+'='+sessionid, '_blank', 'top=50, left=50, width=570, height=650, resizable=1, scrollbars=1');
	return false;
}

function checknames(frm) {
	if (document.managemedia.subclick) button = document.managemedia.subclick.value;
	if (button == "all") {
		frm.filter.value = "";
		return true;
	}
	else if (frm.filter.value.length < 2) {
		alert("<?php print i18n::translate('Please enter more than one character'); ?>");
		frm.filter.focus();
		return false;
	}
	return true;
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

function showchanges() {
	window.location = '<?php echo WT_SCRIPT_NAME."?show_changes=yes&directory=".$directory."&level=".$level."&filter=".$filter."&subclick=".$subclick; ?>';
}

//-->
</script>
<script src="js/webtrees.js" language="JavaScript" type="text/javascript"></script>
<?php
if (check_media_structure()) {
	print "<div id=\"uploadmedia\" style=\"display:none\">";
	// Check if Media Directory is writeable or if Media features are enabled
	// If one of these is not true then do not continue
	if (!dir_is_writable($MEDIA_DIRECTORY) || !$MULTI_MEDIA) {
		print "<span class=\"error\"><b>";
		print i18n::translate('Uploading media files is not allowed because multi-media items have been disabled or because the media directory is not writable.');
		print "</b></span><br />";
	} else {
		show_mediaUpload_form('media.php', $showthumb);		// We have the green light to upload media, print the form
	}
	print "</div><br />";

	ob_start();		// Save output until action table has been printed

	if ($action == "deletedir") {
		print "<table class=\"list_table width100\">";
		print "<tr><td class=\"messagebox\">";
		// Check if media directory and thumbs directory are empty
		$clean = false;
		$files = array();
		$thumbfiles = array();
		$files_fw = array();
		$thumbfiles_fw = array();
		$resdir = false;
		$resthumb = false;
		// Media directory check
		if (@is_dir(filename_decode($directory))) {
			$handle = opendir(filename_decode($directory));
			$files = array();
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $BADMEDIA)) $files[] = $file;
			}
		} else {
			print "<div class=\"error\">".$directory." ".i18n::translate('Directory does not exist.')."</div>";
			AddToLog('Directory does not exist.'.$directory, 'media');
		}

		// Thumbs directory check
		if (@is_dir(filename_decode($thumbdir))) {
			$handle = opendir(filename_decode($thumbdir));
			$thumbfiles = array();
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $BADMEDIA)) $thumbfiles[] = $file;
			}
			closedir($handle);
		}

		// Media Firewall Media directory check
		if (@is_dir(filename_decode($directory_fw))) {
			$handle = opendir(filename_decode($directory_fw));
			$files_fw = array();
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $BADMEDIA)) $files_fw[] = $file;
			}
		}

		// Media Firewall Thumbs directory check
		if (@is_dir(filename_decode($thumbdir_fw))) {
			$handle = opendir(filename_decode($thumbdir_fw));
			$thumbfiles_fw = array();
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $BADMEDIA)) $thumbfiles_fw[] = $file;
			}
			closedir($handle);
		}

		if (!isset($error)) {
			if (count($files) > 0 ) {
				print "<div class=\"error\">".$directory." -- ".i18n::translate('Directory not empty.')."</div>";
				AddToLog($directory." -- ".i18n::translate('Directory not empty.'), 'media');
				$clean = false;
			}
			if (count($thumbfiles) > 0) {
				print "<div class=\"error\">".$thumbdir." -- ".i18n::translate('Directory not empty.')."</div>";
				AddToLog($thumbdir." -- ".i18n::translate('Directory not empty.'), 'media');
				$clean = false;
			}
			if (count($files_fw) > 0 ) {
				print "<div class=\"error\">".$directory_fw." -- ".i18n::translate('Directory not empty.')."</div>";
				AddToLog($directory_fw." -- ".i18n::translate('Directory not empty.'), 'media');
				$clean = false;
			}
			if (count($thumbfiles_fw) > 0) {
				print "<div class=\"error\">".$thumbdir_fw." -- ".i18n::translate('Directory not empty.')."</div>";
				AddToLog($thumbdir_fw." -- ".i18n::translate('Directory not empty.'), 'media');
				$clean = false;
			}
			else $clean = true;
		}

		// Only start deleting if all directories are empty
		if ($clean) {
			$resdir = true;
			$resthumb = true;
			$resdir_fw = true;
			$resthumb_fw = true;
			if (file_exists(filename_decode($directory."index.php"))) @unlink(filename_decode($directory."index.php"));
			if (@is_dir(filename_decode($directory))) $resdir = @rmdir(filename_decode(substr($directory, 0, -1)));
			if (file_exists(filename_decode($thumbdir."index.php"))) @unlink(filename_decode($thumbdir."index.php"));
			if (@is_dir(filename_decode($thumbdir))) $resthumb = @rmdir(filename_decode(substr($thumbdir, 0, -1)));
			if (file_exists(filename_decode($directory_fw."index.php"))) @unlink(filename_decode($directory_fw."index.php"));
			if (@is_dir(filename_decode($directory_fw))) $resdir_fw = @rmdir(filename_decode(substr($directory_fw, 0, -1)));
			if (file_exists(filename_decode($thumbdir_fw."index.php"))) @unlink(filename_decode($thumbdir_fw."index.php"));
			if (@is_dir(filename_decode($thumbdir_fw))) $resthumb_fw = @rmdir(filename_decode(substr($thumbdir_fw, 0, -1)));
			if ($resdir && $resthumb && $resdir_fw && $resthumb_fw) {
				print i18n::translate('Media and thumbnail directories successfully removed.');
				AddToLog($directory." -- ".i18n::translate('Media and thumbnail directories successfully removed.'), 'media');
			} else {
				if (!$resdir) {
					print "<div class=\"error\">".i18n::translate('Media directory not removed.')."</div>";
					AddToLog($directory." -- ".i18n::translate('Media directory not removed.'), 'media');
				} else if (!$resdir_fw) {
					print "<div class=\"error\">".i18n::translate('Media directory not removed.')."</div>";
					AddToLog($directory_fw." -- ".i18n::translate('Media directory not removed.'), 'media');
				} else {
					print i18n::translate('Media directory successfully removed.');
					AddToLog($directory." -- ".i18n::translate('Media directory successfully removed.'), 'media');
				}
				if (!$resthumb) {
					print "<div class=\"error\">".i18n::translate('Thumbnail directory not removed.')."</div>";
					AddToLog($thumbdir." -- ".i18n::translate('Thumbnail directory not removed.'), 'media');
				} else if (!$resthumb_fw) {
					print "<div class=\"error\">".i18n::translate('Thumbnail directory not removed.')."</div>";
					AddToLog($thumbdir_fw." -- ".i18n::translate('Thumbnail directory not removed.'), 'media');
				} else {
					print i18n::translate('Thumbnail directory successfully removed.');
					AddToLog($thumbdir." -- ".i18n::translate('Thumbnail directory successfully removed.'), 'media');
				}

			}
		}

		// Back up to this directory's parent
		$i = strrpos(substr($directory, 0, -1), '/');
		$directory = trim(substr($directory, 0, $i), '/').'/';
		$action="filter";
		print "</td></tr></table>";
	}
/**
 * This action generates a thumbnail for the file
 *
 * @name $action->thumbnail
 */
	if ($action == "thumbnail") {
		print "<table class=\"list_table $TEXT_DIRECTION width100\">";
		print "<tr><td class=\"messagebox wrap\">";
		// TODO: add option to generate thumbnails for all images on page
		// Cycle through $medialist and skip all exisiting thumbs

		// Check if $all is true, if so generate thumbnails for all files that do
		// not yet have any thumbnails created. Otherwise only the file specified.
		if ($all == 'yes') {
			$medialist = get_medialist(true, $directory);
			foreach ($medialist as $key => $media) {
				if (!($MEDIA_EXTERNAL && isFileExternal($filename))) {
					// why doesn't this use thumbnail_file??
					$thumbnail = str_replace("$MEDIA_DIRECTORY", $MEDIA_DIRECTORY."thumbs/", check_media_depth($media["FILE"], "NOTRUNC"));
					if (!$media["THUMBEXISTS"]) {
						if (generate_thumbnail($media["FILE"], $thumbnail)) {
							echo i18n::translate('Thumbnail %s generated automatically.', $thumbnail);
							AddToChangeLog("Thumbnail {$thumbnail} generated automatically.");
						}
						else {
							echo "<span class=\"error\">";
							echo i18n::translate('Thumbnail %s could not be generated automatically.', $thumbnail);
							echo "</span>";
							AddToChangeLog("Thumbnail {$thumbnail} could not be generated automatically.");
						}
						echo "<br />";
					}
				}
			}
		}
		else if ($all != 'yes') {
			if (!($MEDIA_EXTERNAL && isFileExternal($filename))) {
				$thumbnail = str_replace("$MEDIA_DIRECTORY", $MEDIA_DIRECTORY."thumbs/", check_media_depth($filename, "NOTRUNC"));
				if (generate_thumbnail($filename, $thumbnail)) {
					echo i18n::translate('Thumbnail %s generated automatically.', $thumbnail);
					AddToChangeLog("Thumbnail {$thumbnail} generated automatically.");
				}
				else {
					echo "<span class=\"error\">";
					echo i18n::translate('Thumbnail %s could not be generated automatically.', $thumbnail);
					echo "</span>";
					AddToChangeLog("Thumbnail {$thumbnail} could not be generated automatically.");
				}
			}
		}
		$action = "filter";
		echo "</td></tr></table>";
	}

	// Move single file and optionally its corresponding thumbnail to protected dir
	if ($action == "moveprotected") {
		print "<table class=\"list_table $TEXT_DIRECTION width100\">";
		print "<tr><td class=\"messagebox wrap\">";
		if (strpos($filename, "../") !== false) {
			// don't allow user to access directories outside of media dir
			print "<div class=\"error\">".i18n::translate('Blank name or illegal characters in name')."</div>";
		} else {
			if (file_exists($filename)) {
				move_file($filename, get_media_firewall_path($filename));
			}
			if ($MEDIA_FIREWALL_THUMBS) {
				$thumbnail = thumbnail_file($filename, false);
				if (file_exists($thumbnail)) {
					move_file($thumbnail, get_media_firewall_path($thumbnail));
				}
			}
		}
		print "</td></tr></table>";
		$action="filter";
	}

	// Move single file and its corresponding thumbnail to standard dir
	if ($action == "movestandard") {
		print "<table class=\"list_table $TEXT_DIRECTION width100\">";
		print "<tr><td class=\"messagebox wrap\">";
		if (strpos($filename, "../") !== false) {
			// don't allow user to access directories outside of media dir
			print "<div class=\"error\">".i18n::translate('Blank name or illegal characters in name')."</div>";
		} else {
			if (file_exists(get_media_firewall_path($filename))) {
				move_file(get_media_firewall_path($filename), $filename);
			}
			$thumbnail = thumbnail_file($filename, false);
			if (file_exists(get_media_firewall_path($thumbnail))) {
				move_file(get_media_firewall_path($thumbnail), $thumbnail);
			}
		}
		print "</td></tr></table>";
		$action="filter";
	}

	// Move entire dir and all subdirs to protected dir
	if ($action == "movedirprotected") {
		print "<table class=\"list_table $TEXT_DIRECTION width100\">";
		print "<tr><td class=\"messagebox wrap\">";
		print "<strong>".i18n::translate('Move to protected')."<br />";
		move_files(substr($directory, 0, -1), true);
		print "</td></tr></table>";
		$action="filter";
	}

	// Move entire dir and all subdirs to standard dir
	if ($action == "movedirstandard") {
		print "<table class=\"list_table $TEXT_DIRECTION width100\">";
		print "<tr><td class=\"messagebox wrap\">";
		print "<strong>".i18n::translate('Move to standard')."<br />";
		move_files(substr(get_media_firewall_path($directory), 0, -1), false);
		print "</td></tr></table>";
		$action="filter";
	}

	if ($action == "setpermsfix") {
		print "<table class=\"list_table $TEXT_DIRECTION width100\">";
		print "<tr><td class=\"messagebox wrap\">";
		print "<strong>".i18n::translate('Correct read/write/execute permissions')."<br />";
		set_perms(substr($directory, 0, -1));
		set_perms(substr(get_media_firewall_path($directory), 0, -1));
		print "</td></tr></table>";
		$action="filter";
	}

	// Upload media items
	if ($action == "upload") {
		process_uploadMedia_form();
		$medialist = get_medialist();
		$action = "filter";
	}

	$allowDelete = true;
	$removeObject = true;
	// Remove object: same as Delete file, except file isn't deleted
	if ($action == "removeobject") {
		$action = "deletefile";
		$allowDelete = false;
		$removeObject = true;
	}

	// Remove link: same as Delete file, except file isn't deleted
	if ($action == "removelinks") {
		$action = "deletefile";
		$allowDelete = false;
		$removeObject = false;
	}

	// Delete file
	if ($action == "deletefile") {
		print "<table class=\"list_table $TEXT_DIRECTION width100\">";
		print "<tr><td class=\"messagebox wrap\">";
		$xrefs = array($xref);
		$onegedcom = true;
		//-- get all of the XREFS associated with this record
		//-- and check if the file is used in multiple gedcoms
		$myFile = str_replace($MEDIA_DIRECTORY, "", $filename);
		//-- figure out how many levels are in this file
		$mlevels = preg_split("~[/\\\]~", $filename);

		$statement=WT_DB::prepare("SELECT * FROM {$TBLPREFIX}media WHERE m_file LIKE ?")->execute(array("%{$myFile}"));
		while ($row=$statement->fetch(PDO::FETCH_ASSOC)) {
			$rlevels = preg_split("~[/\\\]~", $row["m_file"]);
			//-- make sure we only delete a file at the same level of directories
			//-- see 1825257
			$match = true;
			$k=0;
			$i=count($rlevels)-1;
			$j=count($mlevels)-1;
			while($i>=0 && $j>=0) {
				if ($rlevels[$i] != $mlevels[$j]) {
					$match = false;
					break;
				}
				$j--;
				$i--;
				$k++;
				if ($k>$MEDIA_DIRECTORY_LEVELS) break;
			}
			if ($match) {
				if ($row["m_gedfile"]!=WT_GED_ID) $onegedcom = false;
				else $xrefs[] = $row["m_media"];
			}
		}
		$statement->closeCursor();
		$xrefs = array_unique($xrefs);

		$finalResult = true;
		if ($allowDelete) {
			if (!$onegedcom) {
				print "<span class=\"error\">".i18n::translate('This file is linked to another genealogical database on this server.  It cannot be deleted, moved, or renamed until these links have been removed.')."<br /><br /><b>".i18n::translate('Media file could not be deleted.')."</b></span><br />";
				$finalResult = false;
			}
			if (isFileExternal($filename)) {
				print "<span class=\"error\">".i18n::translate('This media object does not exist as a file on this server.  It cannot be deleted, moved, or renamed.')."<br /><br /><b>".i18n::translate('Media file could not be deleted.')."</b></span><br />";
				$finalResult = false;
			}
			if ($finalResult) {
				// Check if file exists. If so, delete it
				$server_filename = get_server_filename($filename);
				if (file_exists($server_filename) && $allowDelete) {
					if (@unlink($server_filename)) {
						print i18n::translate('Media file successfully deleted.')."<br />";
						AddToChangeLog($server_filename." -- ".i18n::translate('Media file successfully deleted.'));
					} else {
						$finalResult = false;
						print "<span class=\"error\">".i18n::translate('Media file could not be deleted.')."</span><br />";
						AddToChangeLog($server_filename." -- ".i18n::translate('Media file could not be deleted.'));
					}
				}

				// Check if thumbnail exists. If so, delete it.
				$thumbnail = str_replace("$MEDIA_DIRECTORY", $MEDIA_DIRECTORY."thumbs/", $filename);
				$server_thumbnail = get_server_filename($thumbnail);
				if (file_exists($server_thumbnail) && $allowDelete) {
					if (@unlink($server_thumbnail)) {
						print i18n::translate('Thumbnail file successfully deleted.')."<br />";
						AddToChangeLog($server_thumbnail." -- ".i18n::translate('Thumbnail file successfully deleted.'));
					} else {
						$finalResult = false;
						print "<span class=\"error\">".i18n::translate('Thumbnail file could not be deleted.')."</span><br />";
						AddToChangeLog($server_thumbnail." -- ".i18n::translate('Thumbnail file could not be deleted.'));
					}
				}
			}
		}

		//-- loop through all of the found xrefs and delete any references to them
		foreach($xrefs as $ind=>$xref) {
			// Remove references to media file from gedcom and database
			// Check for XREF
			if ($xref != "") {
				$links = get_media_relations($xref);
				foreach($links as $pid=>$type) {
					$gedrec = find_gedcom_record($pid, WT_GED_ID, true);
					$gedrec = remove_subrecord($gedrec, "OBJE", $xref, -1);
					replace_gedrec($pid, WT_GED_ID, $gedrec);
					echo i18n::translate('Record %s successfully updated.', $pid), '<br />';
				}

				// Record changes to the Media object
				accept_all_changes($xref, WT_GED_ID);
				$objerec = find_gedcom_record($xref, WT_GED_ID);
	
				// Remove media object from gedcom
				delete_gedrec($xref, WT_GED_ID);
				echo i18n::translate('Record %s successfully removed from GEDCOM.', $xref), '<br />';

				// Add the same file as a new object
				if ($finalResult && !$removeObject && $objerec!="") {
					$xref = get_new_xref("OBJE");
					$objerec = preg_replace("/0 @.*@ OBJE/", "0 @".$xref."@ OBJE", $objerec);
					if(append_gedrec($objerec, WT_GED_ID)) {
						echo i18n::translate('Record %s successfully added to GEDCOM.', $xref);
					} else {
						$finalResult = false;
						echo "<span class=\"error\">";
						echo i18n::translate('Record %s could not be added to GEDCOM.', $xref);
						echo "</span>";
					}
					print "<br />";
				}
			}
		}
		if ($finalResult) print i18n::translate('Update successful');
		$action = "filter";
		print "</td></tr></table>";
	}

/**
 * Generate link flyout menu
 *
 * @param string $mediaid
 */
	function print_link_menu($mediaid) {
		global $TEXT_DIRECTION;

		$classSuffix = "";
		if ($TEXT_DIRECTION=="rtl") $classSuffix = "_rtl";

		// main link displayed on page
		$menu = new Menu();
		
		// GEDFact assistant Add Media Links =======================
		if (file_exists('modules/GEDFact_assistant/_MEDIA/media_1_ctrl.php')) {
			$menu->addLabel(i18n::translate('Manage links'));
			$menu->addOnclick("return ilinkitem('$mediaid', 'manage')");
			$menu->addClass("", "", "submenu");
			$menu->addFlyout("left");
			// Do not print submunu
			
		} else {
			$menu->addLabel(i18n::translate('Set link'));
			$menu->addOnclick("return ilinkitem('$mediaid', 'person')");
			$submenu = new Menu(i18n::translate('To Person'));
			$submenu->addClass("submenuitem".$classSuffix, "submenuitem_hover".$classSuffix);
			$submenu->addOnclick("return ilinkitem('$mediaid', 'person')");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('To Family'));
			$submenu->addClass("submenuitem".$classSuffix, "submenuitem_hover".$classSuffix);
			$submenu->addOnclick("return ilinkitem('$mediaid', 'family')");
			$menu->addSubMenu($submenu);

			$submenu = new Menu(i18n::translate('To Source'));
			$submenu->addClass("submenuitem".$classSuffix, "submenuitem_hover".$classSuffix);
			$submenu->addOnclick("return ilinkitem('$mediaid', 'source')");
			$menu->addSubMenu($submenu);
		}
		$menu->printMenu();
	}

	$savedOutput = ob_get_clean();

?>

<form name="managemedia" method="post" onsubmit="return checknames(this);" action="media.php">
	<input type="hidden" name="thumbdir" value="<?php print $thumbdir; ?>" />
	<input type="hidden" name="level" value="<?php print $level; ?>" />
	<input type="hidden" name="all" value="true" />
	<input type="hidden" name="subclick" />
	<table class="facts_table center width75 <?php print $TEXT_DIRECTION; ?>">
	<tr><td class="topbottombar" colspan="4"><?php echo i18n::translate('Manage MultiMedia'), help_link('manage_media'); ?></td></tr>
	<?php
	if ($TEXT_DIRECTION=='ltr') $legendAlign = 'align="right"';
	else $legendAlign = 'align="left"';
	?>

	<!-- // NOTE: Row 1 left: Sort sequence -->
	<tr><td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php echo i18n::translate('Sequence'), help_link('sortby'); ?></td>
	<td class="optionbox wrap"><select name="sortby">
		<option value="title" <?php if ($sortby=='title') print "selected=\"selected\"";?>><?php print i18n::translate('TITL');?></option>
		<option value="file" <?php if ($sortby=='file') print "selected=\"selected\"";?>><?php print i18n::translate('FILE');?></option>
	</select></td>

	<!-- // NOTE: Row 1 right, Upload media files -->
	<td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php echo i18n::translate('Upload Media files'), help_link('upload_media'); ?></td>
	<td class="optionbox wrap"><?php print "<a href=\"#\" onclick=\"expand_layer('uploadmedia');\">".i18n::translate('Upload Media files')."</a>"; ?></td></tr>

	<!-- // NOTE: Row 2 left: Filter options -->
	<tr><td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php echo i18n::translate('Filter'), help_link('simple_filter'); ?></td>
	<td class="optionbox wrap">
		<?php
			// Directory pick list
			if (empty($directory)) {
				if (!empty($_SESSION['upload_folder'])) $directory = $_SESSION['upload_folder'];
				else $directory = $MEDIA_DIRECTORY;
			}
			if ($MEDIA_DIRECTORY_LEVELS > 0) {
				$folders = get_media_folders();
				print "<span dir=\"ltr\"><select name=\"directory\">\n";
				foreach($folders as $f) {
					print "<option value=\"".$f."\"";
					if ($directory==$f) print " selected=\"selected\"";
					print ">{$f}</option>\n";
				}
				print "</select></span><br />";
			} else print "<input name=\"directory\" type=\"hidden\" value=\"ALL\" />";
		// Text field for filter
		?>
		<input type="text" name="filter" value="<?php if($filter) print $filter;?>" /><br /><input type="submit" name="search" value="<?php print i18n::translate('Filter');?>" onclick="this.form.subclick.value=this.name" />&nbsp;&nbsp;&nbsp;<input type="submit" name="all" value="<?php print i18n::translate('Display all'); ?>" onclick="this.form.subclick.value=this.name" /></td>

	<!-- // NOTE: Row 2 right: Add media -->
	<td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php echo i18n::translate('Add Media'), help_link('add_media'); ?></td>
	<td class="optionbox wrap"><a href="javascript: <?php echo i18n::translate('Add Media'); ?>" onclick="window.open('addmedia.php?action=showmediaform&linktoid=new', '_blank', 'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1'); return false;"> <?php echo i18n::translate('Add a new Media item'); ?></a></td></tr>

	<!-- // NOTE: Row 3 left: Show thumbnails -->
	<tr><td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php echo i18n::translate('Show thumbnails'), help_link('show_thumb'); ?></td>
	<td class="optionbox wrap"><input type="checkbox" name="showthumb" value="true" <?php if ($showthumb) print "checked=\"checked\""; ?> onclick="submit();" /></td>

	<!-- // NOTE: Row 3 right: Generate missing thumbnails -->
	<?php
		$tempURL = "media.php?";
		if (!empty($filter)) $tempURL .= "filter={$filter}&";
		if (!empty($subclick)) $tempURL .= "subclick={$subclick}&";
		$tempURL .= "action=thumbnail&sortby={$sortby}&all=yes&level={$level}&directory={$directory}".$thumbget;
		?>
	<td class="descriptionbox wrap width25" <?php print $legendAlign;?>><?php echo i18n::translate('Missing thumbnails'), help_link('gen_missing_thumbs'); ?></td>
	<td class="optionbox wrap"><a href="<?php print encode_url($tempURL);?>"><?php print i18n::translate('Create missing thumbnails');?></a></td></tr>
	</table>
</form>
<?php

	if (!empty($savedOutput)) print $savedOutput;		// Print everything we have saved up

	if ($action == "filter" && $subclick != "none") {
		if (empty($directory)) $directory = $MEDIA_DIRECTORY;
			// only check for externalLinks when dealing with the root folder
			$showExternal = ($directory == $MEDIA_DIRECTORY) ? true : false;
			$medialist=get_medialist(true, $directory, false, false, $showExternal);

		
		// Get the list of media items
/**
 * This is the default action for the page
 *
 * Displays a list of dirs and files. Displaying only
 * thumbnails as the images may be large and we do not want large delays
 * while administering the file structure
 *
 * @name $action->filter
 */
		// Show link to previous folder
		$levels = explode('/', $directory);
		$pdir = '';
		for($i=0; $i<count($levels)-2; $i++) $pdir.=$levels[$i].'/';
		if ($pdir != '') {
			$uplink = "<a href=\"".encode_url("media.php?directory={$pdir}&sortby={$sortby}&level=".($level-1).$thumbget)."\">";
			if ($TEXT_DIRECTION=="rtl") $uplink .= getLRM();
			$uplink .= $pdir;
			if ($TEXT_DIRECTION=="rtl") $uplink .= getLRM();
			$uplink .= "</a>\n";

			$uplink2 = "<a href=\"".encode_url("media.php?directory={$pdir}&sortby={$sortby}&level=".($level-1).$thumbget)."\"><img class=\"icon\" src=\"".$WT_IMAGE_DIR."/";
			$uplink2 .= $WT_IMAGES["larrow"]["other"];
			$uplink2 .= "\" alt=\"\" /></a>\n";
		}

		// Start of media directory table
		print "<table class=\"list_table width50 $TEXT_DIRECTION\">";

		// Tell the user where he is
		print "<tr>";
			print "<td class=\"topbottombar\" colspan=\"2\">";
				print i18n::translate('Current directory');
				print "<br />";
				print PrintReady(substr($directory, 0, -1));

				print "<br /><br />";
				print "<form name=\"blah2\" action=\"media.php\" method=\"post\">";
				print "<input type=\"hidden\" name=\"directory\" value=\"".$directory."\" />";
				print "<input type=\"hidden\" name=\"level\" value=\"".($level)."\" />";
				print "<input type=\"hidden\" name=\"dir\" value=\"".$directory."\" />";
				print "<input type=\"hidden\" name=\"action\" value=\"\" />";
				print "<input type=\"hidden\" name=\"showthumb\" value=\"{$showthumb}\" />";
				print "<input type=\"hidden\" name=\"sortby\" value=\"{$sortby}\" />";
			if ($USE_MEDIA_FIREWALL) {
				print "<input type=\"submit\" value=\"".i18n::translate('Move to standard')."\" onclick=\"this.form.action.value='movedirstandard';\" />";
				print "<input type=\"submit\" value=\"".i18n::translate('Move to protected')."\" onclick=\"this.form.action.value='movedirprotected';\" />";
				print help_link('move_mediadirs');
				print "<br />";
			}
				print "<input type=\"submit\" value=\"".i18n::translate('Correct read/write/execute permissions')."\" onclick=\"this.form.action.value='setpermsfix';\" />";
				print help_link('setperms');

				print "</form>";

			print "</td>";
		print "</tr>";

		// display the directory list
		if (count($dirs) || $pdir != '') {
			sort($dirs);
			if ($pdir != '') {
				print "<tr>";
					print "<td class=\"optionbox center width10\">";
						print $uplink2;
					print "</td>";
					print "<td class=\"descriptionbox $TEXT_DIRECTION\">";
						print $uplink;
					print "</td>";
				print "</tr>";
			}

			foreach ($dirs as $indexval => $dir) {
				if ($dir{0}!=".") {
				print "<tr>";
					print "<td class=\"optionbox center width10\">";
						// directory options
						print "<form name=\"blah\" action=\"media.php\" method=\"post\">";
						print "<input type=\"hidden\" name=\"directory\" value=\"".$directory.$dir."/\" />";
						print "<input type=\"hidden\" name=\"parentdir\" value=\"".$directory."\" />";
						print "<input type=\"hidden\" name=\"level\" value=\"".($level)."\" />";
						print "<input type=\"hidden\" name=\"dir\" value=\"".$dir."\" />";
						print "<input type=\"hidden\" name=\"action\" value=\"\" />";
						print "<input type=\"hidden\" name=\"showthumb\" value=\"{$showthumb}\" />";
						print "<input type=\"hidden\" name=\"sortby\" value=\"{$sortby}\" />";
						print "<input type=\"image\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["remove"]["other"]."\" alt=\"".i18n::translate('Delete')."\" onclick=\"this.form.action.value='deletedir';return confirm('".i18n::translate('Are you sure you want to delete this folder?')."');\" />";
						if ($USE_MEDIA_FIREWALL) {
							print "<br /><input type=\"submit\" value=\"".i18n::translate('Move to standard')."\" onclick=\"this.form.level.value=(this.form.level.value*1)+1;this.form.action.value='movedirstandard';\" />";
							print "<br /><input type=\"submit\" value=\"".i18n::translate('Move to protected')."\" onclick=\"this.form.level.value=(this.form.level.value*1)+1;this.form.action.value='movedirprotected';\" />";
						}

						print "</form>";
					print "</td>";
					print "<td class=\"descriptionbox $TEXT_DIRECTION\">";
						print "<a href=\"".encode_url("media.php?directory={$directory}{$dir}/&sortby={$sortby}&level=".($level+1).$thumbget)."\">";
						if ($TEXT_DIRECTION=="rtl") print getRLM();
						print $dir;
						if ($TEXT_DIRECTION=="rtl") print getRLM();
						print "</a>";
					print "</td>";
				print "</tr>";
				}
			}
		}
		print "</table>";
		print "<br />";

		// display the images
		if (count($medialist) && ($subclick=='search' || $subclick=='all')) {
			if (WT_USE_LIGHTBOX) {
				// Get Lightbox config variables
				require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
				require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
			}

			// Sort the media list according to the user's wishes
			$sortedMediaList = $medialist;	// Default sort (by title) has already been done
			if ($sortby=='file') uasort($sortedMediaList, 'filesort');

			// Set up for two passes, the first showing URLs, the second normal files
			print "\n\t<table class=\"list_table width100\">";
			if ($directory==$MEDIA_DIRECTORY) {
				$httpFilter = "http";
				$passStart = 1;
			} else {
				$httpFilter = "";
				$passStart = 2;
			}
			for ($passCount=$passStart; $passCount<3; $passCount++) {
				$printDone = false;
				foreach ($sortedMediaList as $indexval => $media) {
					while (true) {
						if (!filterMedia($media, $filter, $httpFilter)) break;
						$isExternal = isFileExternal($media["FILE"]);
						if ($passCount==1 && !$isExternal) break;
						if ($passCount==2 && $isExternal) break;
						$imgsize = findImageSize($media["FILE"]);
						$imgwidth = $imgsize[0]+40;
						$imgheight = $imgsize[1]+150;

						$changeClass = "";
						if ($media["CHANGE"]=="delete") $changeClass = "change_old";
						if ($media["CHANGE"]=="replace") $changeClass = "change_new";
						if ($media["CHANGE"]=="append") $changeClass = "change_new";

						// Show column with file operations options
						$printDone = true;
						print "<tr><td class=\"optionbox $changeClass $TEXT_DIRECTION width20\">";

						if ($media["CHANGE"]!="delete") {
							// Edit File
							$tempURL = "addmedia.php?action=";
							if ($media["XREF"] != "") {
								$tempURL .= "editmedia&pid={$media['XREF']}&linktoid=";
								if (!$media["LINKED"]) {
									$tempURL .= "new";
								} else {
									foreach ($media["LINKS"] as $linkToID => $temp) break;
									$tempURL .= $linkToID;
								}
							} else {
								$tempURL .= 'showmediaform&filename='.encrypt($media['FILE']).'&linktoid=new';
							}
							echo "<a href=\"javascript:", i18n::translate('Edit'), "\" onclick=\"window.open('", encode_url($tempURL, false), "', '_blank', 'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1'); return false;\">", i18n::translate('Edit'), "</a><br />";

							// Edit Raw
							if ($media["XREF"] != "") {
								print "<a href=\"javascript:".i18n::translate('Edit raw GEDCOM record')."\" onclick=\"return edit_raw('".$media['XREF']."');\">".i18n::translate('Edit raw GEDCOM record')."</a><br />\n";
							}

							// Delete File
							// 		don't delete external files
							//		don't delete files linked to more than 1 object
							$objectCount = 0;
							if (!$isExternal) {
								foreach ($medialist as $tempMedia) {
									if ($media["EXISTS"] && $media["FILE"]==$tempMedia["FILE"]) $objectCount++;
								}
								unset($tempMedia);
							}
							if (!$isExternal && $objectCount<2) {
								$tempURL = "media.php?";
								if (!empty($filter)) $tempURL.= "filter={$filter}&";
								$tempURL .= "action=deletefile&showthumb={$showthumb}&sortby={$sortby}&filter={$filter}&subclick={$subclick}&filename=".encrypt($media['FILE'])."&directory={$directory}&level={$level}&xref={$media['XREF']}&gedfile={$media['GEDFILE']}";
								print "<a href=\"".encode_url($tempURL)."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this file?')."');\">".i18n::translate('Delete file')."</a><br />";
							}

							// Remove Object
							if (!empty($media["XREF"])) {
								$tempURL = "media.php?";
								if (!empty($filter)) $tempURL .= "filter={$filter}&";
								$tempURL .= "action=removeobject&showthumb={$showthumb}&sortby={$sortby}&filter={$filter}&subclick={$subclick}&filename=".encrypt($media['FILE'])."&directory={$directory}&level={$level}&xref={$media['XREF']}&gedfile={$media['GEDFILE']}";
								print "<a href=\"".encode_url($tempURL)."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to remove this object from the database?')."');\">".i18n::translate('Remove object')."</a><br />";
							}

							// Remove links
							if ($media["LINKED"]) {
								$tempURL = "media.php?";
								if (!empty($filter)) $tempURL .= "filter={$filter}&";
								$tempURL .= "action=removelinks&showthumb={$showthumb}&sortby={$sortby}&filter={$filter}&subclick={$subclick}&filename=".encrypt($media['FILE'])."&directory={$directory}&level={$level}&xref={$media['XREF']}&gedfile={$media['GEDFILE']}";
							//	print "<a href=\"".encode_url($tempURL)."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to remove all links to this object?')."');\">".i18n::translate('Remove links')."</a><br />";
							}

							// Add or Remove Links
							// Only add or remove links to media that is in the DB
							if ($media["XREF"] != "") {
								print_link_menu($media["XREF"]);
							}


							// Move image between standard and protected directories
							if ($USE_MEDIA_FIREWALL && ($media["EXISTS"] > 1)) {
								$tempURL = "media.php?";
								if ($media["EXISTS"] == 2) {
									$tempURL .= "action=moveprotected";
									$message=i18n::translate('Move to protected directory');
								}
								if ($media["EXISTS"] == 3) {
									$tempURL .= "action=movestandard";
									$message=i18n::translate('Move to standard directory');
								}
								$tempURL .= "&showthumb={$showthumb}&sortby={$sortby}&filename=".encrypt($media['FILE'])."&directory={$directory}&level={$level}&xref={$media['XREF']}&gedfile=".$media["GEDFILE"];
								print "<a href=\"".encode_url($tempURL)."\">".$message."</a><br />";
							}

							// Generate thumbnail
							if (!$isExternal && (empty($media["THUMB"]) || !$media["THUMBEXISTS"])) {
								$ct = preg_match("/\.([^\.]+)$/", $media["FILE"], $match);
								if ($ct>0) $ext = strtolower(trim($match[1]));
								if ($ext=="jpg" || $ext=="jpeg" || $ext=="gif" || $ext=="png") {
									$tempURL = "media.php?";
									if (!empty($filter)) $tempURL .= "filter={$filter}&";
									$tempURL .= "action=thumbnail&all=no&sortby={$sortby}&level={$level}&directory={$directory}&filename=".encrypt($media["FILE"]).$thumbget;
									print "<a href=\"".encode_url($tempURL)."\">".i18n::translate('Create thumbnail')."</a>";
								}
							}

						}
						// NOTE: Close column for file operations
						print "</td>";


						$name = trim($media["TITL"]);
						// Get media item Notes
						$haystack = $media["GEDCOM"];
						$needle   = "1 NOTE";
						$before   = substr($haystack, 0, strpos($haystack, $needle));
						$after    = substr(strstr($haystack, $needle), strlen($needle));
						$worked   = str_replace("1 NOTE", "1 NOTE<br />", $after);
						$final    = $before.$needle.$worked;
						$notes    = PrintReady(htmlspecialchars(addslashes(print_fact_notes($final, 1, true, true)), ENT_COMPAT, 'UTF-8'));

						// Get info on how to handle this media file
						$mediaInfo = mediaFileInfo($media["FILE"], $media["THUMB"], $media["XREF"], $name, $notes);

						//-- Thumbnail field
						if ($showthumb) {
							print "\n\t\t\t<td class=\"optionbox $changeClass $TEXT_DIRECTION width10\">";
							echo '<center><a href="', $mediaInfo['url'], '">';
							echo '<img src="', $mediaInfo['thumb'], '" align="middle" class="thumbnail" border="none"', $mediaInfo['width'];
							echo ' alt="', $name, '" /></a></center>';
							echo '</td>';
						}

						//-- name and size field
						print "\n\t\t\t<td class=\"optionbox $changeClass $TEXT_DIRECTION wrap\">";
						if ($media["TITL"]!="" && begRTLText($media["TITL"]) && $TEXT_DIRECTION=="ltr") {
							if (!empty($media["XREF"])) {
								print "(".$media["XREF"].")";
								print "&nbsp;&nbsp;&nbsp;";
							}
							if ($media["TITL"]!="") print "<b>".PrintReady($media["TITL"])."</b><br />";
						} else {
							if ($media["TITL"]!="") print "<b>".PrintReady($media["TITL"])."</b>&nbsp;&nbsp;&nbsp;";
							if (!empty($media["XREF"])) {
								if ($TEXT_DIRECTION=="rtl") print getRLM();
								print "(".$media["XREF"].")";
								if ($TEXT_DIRECTION=="rtl") print getRLM();
								print "<br />";
							}
						}
						if (!$isExternal && !$media["EXISTS"]) print "<span dir=\"ltr\">".PrintReady($media["FILE"])."</span><br /><span class=\"error\">".i18n::translate('The filename entered does not exist.')."</span><br />";
						else {
							if (substr($mediaInfo['type'], 0, 4) == 'url_') $tempText = 'URL';
							else $tempText = PrintReady($media["FILE"]);
							if (!empty($media["XREF"])) {
								echo '<a href="', 'mediaviewer.php?mid=', $media["XREF"], '"><span dir="ltr">', $tempText, '</span></a><br />';
							} else {
								echo '<span dir="ltr">', $tempText, '</span><br />';
							}
						}
						if (substr($mediaInfo['type'], 0, 4) != 'url_' && !empty($imgsize[0])) {
							print "<sub>&nbsp;&nbsp;".i18n::translate('Image Dimensions')." -- ".$imgsize[0]."x".$imgsize[1]."</sub><br />";
						}
						print_fact_notes($media["GEDCOM"], 1);
						print_fact_sources($media["GEDCOM"], 1);
						if ($media["LINKED"]) {
							PrintMediaLinks($media["LINKS"], "normal");
						} else {
							print "<br />".i18n::translate('This media object is not linked to any GEDCOM record.');
						}


						if ($USE_MEDIA_FIREWALL) {
							print "<br /><br />";
							if ($media["EXISTS"]) {
								switch ($media["EXISTS"]) {
								case 1:
									echo i18n::translate('This media object is located on an external server');
									break;
								case 2:
									echo i18n::translate('This media object is in the standard media directory');
									break;
								case 3:
									echo i18n::translate('This media object is in the protected media directory');
									break;
								}
								echo '<br />';
							}
							if ($media["THUMBEXISTS"]) {
								switch ($media["EXISTS"]) {
								case 1:
									echo i18n::translate('This thumbnail is located on an external server');
									break;
								case 2:
									echo i18n::translate('This thumbnail is in the standard media directory');
									break;
								case 3:
									echo i18n::translate('This thumbnail is in the protected media directory');
									break;
								}
								echo '<br />';
							}
						}

						print "\n\t\t\t</td></tr>";
						break;
					}
				}
				if ($passCount==1 && $printDone) print "<tr><td class=\"optionbox\" colspan=\"3\">&nbsp;</td></tr>";
			}
			print "\n\t</table><br />";
		}
	}

	?>
<?php
}
else print i18n::translate('The media folder is corrupted.');
print_footer();
?>
