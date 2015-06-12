<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Query\QueryMedia;

define('WT_SCRIPT_NAME', 'admin_media_upload.php');
require './includes/session.php';

$MEDIA_DIRECTORY = $WT_TREE->getPreference('MEDIA_DIRECTORY');

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Upload media files'));

$action = Filter::post('action');

if ($action == "upload") {
	for ($i = 1; $i < 6; $i++) {
		if (!empty($_FILES['mediafile' . $i]["name"]) || !empty($_FILES['thumbnail' . $i]["name"])) {
			$folder = Filter::post('folder' . $i);

			// Validate the media folder
			$folderName = str_replace('\\', '/', $folder);
			$folderName = trim($folderName, '/');
			if ($folderName == '.') {
				$folderName = '';
			}
			if ($folderName) {
				$folderName .= '/';
				// Not allowed to use “../”
				if (strpos('/' . $folderName, '/../') !== false) {
					FlashMessages::addMessage('Folder names are not allowed to include “../”');
					break;
				}
			}

			// Make sure the media folder exists
			if (!is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
				if (File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY)) {
					FlashMessages::addMessage(I18N::translate('The folder %s has been created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)));
				} else {
					FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY)), 'danger');
					break;
				}
			}

			// Managers can create new media paths (subfolders).  Users must use existing folders.
			if ($folderName && !is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName)) {
				if (Auth::isManager($WT_TREE)) {
					if (File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName)) {
						FlashMessages::addMessage(I18N::translate('The folder %s has been created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName)));
					} else {
						FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName)), 'danger');
						break;
					}
				} else {
					// Regular users should not have seen this option - so no need for an error message.
					break;
				}
			}

			// The media folder exists.  Now create a thumbnail folder to match it.
			if (!is_dir(WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName)) {
				if (!File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName)) {
					FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename(WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName)));
					break;
				}
			}

			// A thumbnail file with no main image?
			if (!empty($_FILES['thumbnail' . $i]['name']) && empty($_FILES['mediafile' . $i]['name'])) {
				// Assume the user used the wrong field, and treat this as a main image
				$_FILES['mediafile' . $i] = $_FILES['thumbnail' . $i];
				unset($_FILES['thumbnail' . $i]);
			}

			// Thumbnail files must contain images.
			if (!empty($_FILES['thumbnail' . $i]['name']) && !preg_match('/^image\/(png|gif|jpeg)/', $_FILES['thumbnail' . $i]['type'])) {
				FlashMessages::addMessage(I18N::translate('Thumbnail files must contain images.'));
				break;
			}

			// User-specified filename?
			$filename = Filter::post('filename' . $i);
			// Use the name of the uploaded file?
			if (!$filename && !empty($_FILES['mediafile' . $i]['name'])) {
				$filename = $_FILES['mediafile' . $i]['name'];
			}

			// Validate the media path and filename
			if (preg_match('/([\/\\\\<>])/', $filename, $match)) {
				// Local media files cannot contain certain special characters
				FlashMessages::addMessage(I18N::translate('Filenames are not allowed to contain the character “%s”.', $match[1]));
				$filename = '';
				break;
			} elseif (preg_match('/(\.(php|pl|cgi|bash|sh|bat|exe|com|htm|html|shtml))$/i', $filename, $match)) {
				// Do not allow obvious script files.
				FlashMessages::addMessage(I18N::translate('Filenames are not allowed to have the extension “%s”.', $match[1]));
				$filename = '';
				break;
			} elseif (!$filename) {
				FlashMessages::addMessage(I18N::translate('No media file was provided.'));
				break;
			} else {
				$fileName = $filename;
			}

			// Now copy the file to the correct location.
			if (!empty($_FILES['mediafile' . $i]['name'])) {
				$serverFileName = WT_DATA_DIR . $MEDIA_DIRECTORY . $folderName . $fileName;
				if (file_exists($serverFileName)) {
					FlashMessages::addMessage(I18N::translate('The file %s already exists.  Use another filename.', $folderName . $fileName));
					$filename = '';
					break;
				}
				if (move_uploaded_file($_FILES['mediafile' . $i]['tmp_name'], $serverFileName)) {
					FlashMessages::addMessage(I18N::translate('The file %s has been uploaded.', Html::filename($serverFileName)));
					Log::addMediaLog('Media file ' . $serverFileName . ' uploaded');
				} else {
					FlashMessages::addMessage(
						I18N::translate('There was an error uploading your file.') .
						'<br>' .
						Functions::fileUploadErrorText($_FILES['mediafile' . $i]['error'])
					);
					$filename = '';
					break;
				}

				// Now copy the (optional thumbnail)
				if (!empty($_FILES['thumbnail' . $i]['name']) && preg_match('/^image\/(png|gif|jpeg)/', $_FILES['thumbnail' . $i]['type'], $match)) {
					$extension      = $match[1];
					$thumbFile      = preg_replace('/\.[a-z0-9]{3,5}$/', '.' . $extension, $fileName);
					$serverFileName = WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $folderName . $thumbFile;
					if (move_uploaded_file($_FILES['thumbnail' . $i]['tmp_name'], $serverFileName)) {
						FlashMessages::addMessage(I18N::translate('The file %s has been uploaded.', Html::filename($serverFileName)));
						Log::addMediaLog('Thumbnail file ' . $serverFileName . ' uploaded');
					}
				}
			}
		}
	}
}

$controller->pageHeader();

$mediaFolders = QueryMedia::folderListAll();

// Determine file size limit
$filesize = ini_get('upload_max_filesize');
if (empty($filesize)) {
	$filesize = "2M";
}

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<p>
	<?php echo I18N::translate('Upload one or more media files from your local computer.  Media files can be pictures, video, audio, or other formats.'); ?>
	<?php echo I18N::translate('Maximum upload size: '), $filesize, '</p>'; ?>
</p>

<?php

// Print the form
echo '<form name="uploadmedia" enctype="multipart/form-data" method="post" action="', WT_SCRIPT_NAME, '">';
echo '<input type="hidden" name="action" value="upload">';

// Print 5 forms for uploading images
for ($i = 1; $i < 6; $i++) {
	echo '<table class="upload_media">';
	echo '<tr><th>', I18N::translate('Media file'), ':&nbsp;&nbsp;', $i, '</th></tr>';
	echo '<tr><td>';
	echo I18N::translate('Media file to upload');
	echo '</td>';
	echo '<td>';
	echo '<input name="mediafile', $i, '" type="file" size="40">';
	echo '</td></tr>';
	echo '<tr><td>';
	echo I18N::translate('Thumbnail to upload');
	echo '</td>';
	echo '<td>';
	echo '<input name="thumbnail', $i, '" type="file" size="40">';
	if ($i === 1) {
		echo '<p class="small text-muted">', I18N::translate('Choose the thumbnail image that you want to upload.  Although thumbnails can be generated automatically for images, you may wish to generate your own thumbnail, especially for other media types.  For example, you can provide a still image from a video, or a photograph of the individual who made an audio recording.'), '</p>';
	}
	echo '</td></tr>';

	if (Auth::isManager($WT_TREE)) {
		echo '<tr><td>';
		echo I18N::translate('Filename on server');
		echo '</td>';
		echo '<td>';
		echo '<input name="filename', $i, '" type="text" size="40">';
		if ($i == 1) {
			echo '<p class="small text-muted">', I18N::translate('Do not change to keep original filename.'), "</p>";
			echo '<p class="small text-muted">', I18N::translate('The media file you are uploading can be, and probably should be, named differently on the server than it is on your local computer.  This is so because often the local filename has meaning to you but is much less meaningful to others visiting this website.  Consider also the possibility that you and someone else both try to upload different files called “granny.jpg“.<br><br>In this field, you specify the new name of the file you are uploading.  The name you enter here will also be used to name the thumbnail, which can be uploaded separately or generated automatically.  You do not need to enter the filename extension (jpg, gif, pdf, doc, etc.)<br><br>Leave this field blank to keep the original name of the file you have uploaded from your local computer.'), '</p>';
		}
		echo '</td></tr>';
	} else {
		echo '<tr style="display:none;"><td><input type="hidden" name="filename', $i, '" value=""></td></tr>';
	}

	if (Auth::isManager($WT_TREE)) {
		echo '<tr><td>';
		echo I18N::translate('Folder name on server');
		echo '</td>';
		echo '<td>';

		echo '<select name="folder_list', $i, '" onchange="document.uploadmedia.folder', $i, '.value=this.options[this.selectedIndex].value;">';
		echo '<option';
		echo ' value="/"> ', I18N::translate('Choose: '), ' </option>';
		if (Auth::isAdmin()) {
			echo '<option value="other" disabled>', I18N::translate('Other folder… please type in'), "</option>";
		}
		foreach ($mediaFolders as $f) {
			echo '<option value="', Filter::escapeHtml($f), '">', Filter::escapeHtml($f), "</option>";
		}
		echo "</select>";
		if (Auth::isAdmin()) {
			echo '<br><input name="folder', $i, '" type="text" size="40" value="">';
		} else {
			echo '<input name="folder', $i, '" type="hidden" value="">';
		}
		if ($i === 1) {
			echo '<p class="small text-muted">', I18N::translate('If you have a large number of media files, you can organize them into folders and subfolders.'), '</p>';
		}
		echo '</td></tr>';
	} else {
		echo '<tr style="display:none;"><td><input name="folder', $i, '" type="hidden" value=""></td></tr>';
	}
	echo '</table>';
}
// Print the Submit button for uploading the media
echo '<input type="submit" value="', I18N::translate('Upload'), '">';
echo '</form>';
