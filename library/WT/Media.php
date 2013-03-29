<?php
// Class that defines a media object
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Media extends WT_GedcomRecord {
	public $title = null; // TODO: these should be private, with getTitle() and getFilename() functions
	public $file  = null;

	// Create a Media object from either raw GEDCOM data or a database row
	public function __construct($data) {
		parent::__construct($data);

		if (is_array($data)) {
			// Construct from a row from the database
			$this->file =$data['m_filename'];
			$this->title=$data['m_titl'];
		} else {
			// Construct from raw GEDCOM data
			if (preg_match('/\n1 FILE (.+)/', $data, $match)) {
				$this->file = $match[1];
			} else {
				$this->file = '';
			}
			if (preg_match('/\n\d TITL (.+)/', $data, $match)) {
				$this->title = $match[1];
			} else {
				$this->title = $this->file;
			}
		}
	}

	// Implement media-specific privacy logic ...
	protected function _canDisplayDetailsByType($access_level) {
		// Hide media objects if they are attached to private records
		$linked_ids=WT_DB::prepare(
			"SELECT l_from FROM `##link` WHERE l_to=? AND l_file=?"
		)->execute(array($this->xref, $this->ged_id))->fetchOneColumn();
		foreach ($linked_ids as $linked_id) {
			$linked_record=WT_GedcomRecord::getInstance($linked_id);
			if ($linked_record && !$linked_record->canDisplayDetails($access_level)) {
				return false;
			}
		}

		// ... otherwise apply default behaviour
		return parent::_canDisplayDetailsByType($access_level);
	}

	// Fetch the record from the database
	protected static function fetchGedcomRecord($xref, $ged_id) {
		static $statement=null;

		if ($statement===null) {
			$statement=WT_DB::prepare(
				"SELECT 'OBJE' AS type, m_id AS xref, m_file AS ged_id, m_gedcom AS gedrec, m_titl, m_filename".
				" FROM `##media` WHERE m_id=? AND m_file=?"
			);
		}
		return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
	}

	/**
	 * get the media note from the gedcom
	 * @return string
	 */
	public function getNote() {
		return get_gedcom_value('NOTE', 1, $this->getGedcomRecord());
	}

	/**
	 * get the main media filename
	 * @return string
	 */
	public function getFilename() {
		return $this->file;
	}

	// Get the filename on the server - for those (very few!) functions which actually
	// need the filename, such as mediafirewall.php and the PDF reports.
	public function getServerFilename($which='main') {
		global $MEDIA_DIRECTORY, $THUMBNAIL_WIDTH;

		if ($this->isExternal() || !$this->file) {
			// External image, or (in the case of corrupt GEDCOM data) no image at all
			return $this->file;
		} elseif ($which=='main') {
			// Main image
			return WT_DATA_DIR . $MEDIA_DIRECTORY . $this->file;
		} else {
			// Thumbnail
			$file = WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $this->file;
			// Does the thumbnail exist?
			if (file_exists($file)) {
				return $file;
			}
			// Does a user-generated thumbnail exist?
			$user_thumb = preg_replace('/\.[a-z0-9]{3,5}$/i', '.png', $file);
			if (file_exists($user_thumb)) {
				return $user_thumb;
			}
			// Does the folder exist for this thumbnail?
			if (!is_dir(dirname($file)) && !@mkdir(dirname($file, WT_PERM_EXE, true))) {
				AddToLog('The folder ' . dirname($file) . ' could not be created for ' . $this->getXref(), 'media');
				return $file;
			}
			// Is there a corresponding main image?
			$main_file = WT_DATA_DIR . $MEDIA_DIRECTORY . $this->file;
			if (!file_exists($main_file)) {
				AddToLog('The file ' . $main_file . ' does not exist for ' . $this->getXref(), 'media');
				return $file;
			}
			// Try to create a thumbnail automatically
			$imgsize = getimagesize($main_file);
			if ($imgsize[0] && $imgsize[1]) {
				// Image small enough to be its own thumbnail?
				if ($imgsize[0] < $THUMBNAIL_WIDTH) {
					AddToLog('Thumbnail created for ' . $main_file . ' (copy of main image)', 'media');
					@copy($main_file, $file);
				} else {
					if (hasMemoryForImage($main_file)) {
						switch ($imgsize['mime']) {
						case 'image/png':  $main_image = @imagecreatefrompng ($main_file); break;
						case 'image/gif':  $main_image = @imagecreatefromgif ($main_file); break;
						case 'image/jpeg': $main_image = @imagecreatefromjpeg($main_file); break;
						default:           return $file; // Nothing else we can do :-(
						}
						if ($main_image) {
							// How big should the thumbnail be?
							$width  = $THUMBNAIL_WIDTH;
							$height = round($imgsize[1] * ($width/$imgsize[0]));
							$thumb_image = @imagecreatetruecolor($width, $height);
							@imagecopyresampled($thumb_image, $main_image, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
							switch ($imgsize['mime']) {
							case 'image/png':  @imagepng ($thumb_image, $file); break;
							case 'image/gif':  @imagegif ($thumb_image, $file); break;
							case 'image/jpeg': @imagejpeg($thumb_image, $file); break;
							}
							@imagedestroy($main_image);
							@imagedestroy($thumb_image);
							AddToLog('Thumbnail created for ' . $main_file, 'media');
						} else {
							AddToLog('Failed to create thumbnail for ' . $main_file, 'media');
						}
					} else {
						AddToLog('Not enough memory to create thumbnail for ' . $main_file, 'media');
					}
				}
			}
			return $file;
		}
	}

	/**
	 * check if the file exists on this server
	 * @param which string - specify either 'main' or 'thumb'
	 * @return boolean
	 */
	public function fileExists($which='main') {
		return @file_exists($this->getServerFilename($which));
	}

	// determine if the file is an external url
	public function isExternal() {
		return strpos($this->file, '://') !== false;
	}

	/**
	 * get the media file size in KB
	 * @param which string - specify either 'main' or 'thumb'
	 * @return string
	 */
	public function getFilesize($which='main') {
		$size = $this->getFilesizeraw($which);
		if ($size) $size=(int)(($size+1023)/1024); // add some bytes to be sure we never return “0 KB”
		return /* I18N: size of file in KB */ WT_I18N::translate('%s KB', WT_I18N::number($size));
	}

	/**
	 * get the media file size, unformatted
	 * @param which string - specify either 'main' or 'thumb'
	 * @return number
	 */
	public function getFilesizeraw($which='main') {
		if ($this->fileExists($which)) return @filesize($this->getServerFilename($which));
		return 0;
	}

	/**
	 * get filemtime for the media file
	 * @param which string - specify either 'main' or 'thumb'
	 * @return number
	 */
	public function getFiletime($which='main') {
		if ($this->fileExists($which)) return @filemtime($this->getServerFilename($which));
		return 0;
	}

	/**
	 * generate an etag specific to this media item and the current user
	 * @param which string - specify either 'main' or 'thumb'
	 * @return number
	 */
	public function getEtag($which='main') {
		// setup the etag.  use enough info so that if anything important changes, the etag won’t match
		global $SHOW_NO_WATERMARK;
		if ($this->isExternal()) {
			// etag not really defined for external media
			return '';
		}
		$etag_string = basename($this->getServerFilename($which)).$this->getFiletime($which).WT_GEDCOM.WT_USER_ACCESS_LEVEL.$SHOW_NO_WATERMARK;
		$etag_string = dechex(crc32($etag_string));
		return ($etag_string);
	}

	/**
	 * get the media type from the gedcom
	 * @return string
	 */
	public function getMediaType() {
		$mediaType = strtolower(get_gedcom_value('FORM:TYPE', 2, $this->getGedcomRecord()));
		return $mediaType;
	}

	// Is this object marked as a highlighted image?
	public function isPrimary() {
		if (preg_match('/\n\d _PRIM ([YN])/', $this->getGedcomRecord(), $match)) {
			return $match[1];
		} else {
			return '';
		}
	}

	/**
	 * get image properties
	 * @param which string - specify either 'main' or 'thumb'
	 * @param addWidth int - amount to add to width
	 * @param addHeight int - amount to add to height
	 * @return array
	 */
	public function getImageAttributes($which='main',$addWidth=0,$addHeight=0) {
		global $THUMBNAIL_WIDTH;
		$var=$which.'imagesize';
		if (!empty($this->$var)) return $this->$var;
		$imgsize = array();
		if ($this->fileExists($which)) {
			$imgsize=@getimagesize($this->getServerFilename($which)); // [0]=width [1]=height [2]=filetype ['mime']=mimetype
			if (is_array($imgsize) && !empty($imgsize['0'])) {
				// this is an image
				$imgsize[0]=$imgsize[0]+0;
				$imgsize[1]=$imgsize[1]+0;
				$imgsize['adjW']=$imgsize[0]+$addWidth; // adjusted width
				$imgsize['adjH']=$imgsize[1]+$addHeight; // adjusted height
				$imageTypes=array('','GIF','JPG','PNG','SWF','PSD','BMP','TIFF','TIFF','JPC','JP2','JPX','JB2','SWC','IFF','WBMP','XBM');
				$imgsize['ext']=$imageTypes[0+$imgsize[2]];
				// this is for display purposes, always show non-adjusted info
				$imgsize['WxH']=/* I18N: image dimensions, width × height */ WT_I18N::translate('%1$s × %2$s pixels', WT_I18N::number($imgsize['0']), WT_I18N::number($imgsize['1']));
				$imgsize['imgWH']=' width="'.$imgsize['adjW'].'" height="'.$imgsize['adjH'].'" ';
				if ( ($which=='thumb') && ($imgsize['0'] > $THUMBNAIL_WIDTH) ) {
					// don’t let large images break the dislay
					$imgsize['imgWH']=' width="'.$THUMBNAIL_WIDTH.'" ';
				}
			}
		}

		if (!is_array($imgsize) || empty($imgsize['0'])) {
			// this is not an image, OR the file doesn’t exist OR it is a url
			$imgsize[0]=0;
			$imgsize[1]=0;
			$imgsize['adjW']=0;
			$imgsize['adjH']=0;
			$imgsize['ext']='';
			$imgsize['mime']='';
			$imgsize['WxH']='';
			$imgsize['imgWH']='';
			if ($this->isExternal($which)) {
				// don’t let large external images break the dislay
				$imgsize['imgWH']=' width="'.$THUMBNAIL_WIDTH.'" ';
			}
		}

		if (empty($imgsize['mime'])) {
			// this is not an image, OR the file doesn’t exist OR it is a url
			// set file type equal to the file extension - can’t use parse_url because this may not be a full url
			$exp = explode('?', $this->file);
			$pathinfo = pathinfo($exp[0]);
			$imgsize['ext']=@strtoupper($pathinfo['extension']);
			// all mimetypes we wish to serve with the media firewall must be added to this array.
			$mime=array('DOC'=>'application/msword', 'MOV'=>'video/quicktime', 'MP3'=>'audio/mpeg', 'PDF'=>'application/pdf',
			'PPT'=>'application/vnd.ms-powerpoint', 'RTF'=>'text/rtf', 'SID'=>'image/x-mrsid', 'TXT'=>'text/plain', 'XLS'=>'application/vnd.ms-excel',
			'WMV'=>'video/x-ms-wmv');
			if (empty($mime[$imgsize['ext']])) {
				// if we don’t know what the mimetype is, use something ambiguous
				$imgsize['mime']='application/octet-stream';
				if ($this->fileExists($which)) {
					// alert the admin if we cannot determine the mime type of an existing file
					// as the media firewall will be unable to serve this file properly
					AddToLog('Media Firewall error: >Unknown Mimetype< for file >'.$this->file.'<', 'media');
				}
			} else {
				$imgsize['mime']=$mime[$imgsize['ext']];
			}
		}
		$this->$var=$imgsize;
		return $this->$var;
	}

	// Generate a URL to this record, suitable for use in HTML
	public function getHtmlUrl() {
		return parent::_getLinkUrl('mediaviewer.php?mid=', '&amp;');
	}
	// Generate a URL to this record, suitable for use in javascript, HTTP headers, etc.
	public function getRawUrl() {
		return parent::_getLinkUrl('mediaviewer.php?mid=', '&');
	}

	// Generate a URL directly to the media file
	public function getHtmlUrlDirect($which='main', $download=false) {
		// “cb” is “cache buster”, so clients will make new request if anything significant about the user or the file changes
		// The extension is there so that image viewers (e.g. colorbox) can do something sensible
		$thumbstr = ($which=='thumb') ? '&thumb=1' : '';
		$downloadstr = ($download) ? '&dl=1' : '';
		return
			'mediafirewall.php?mid=' . $this->getXref() . $thumbstr . $downloadstr .
			'&ged=' . rawurlencode(get_gedcom_from_id($this->ged_id)) .
			'&cb=' . $this->getEtag($which);
	}

	// What file extension is used by this file?
	public function extension() {
		if (preg_match('/\.([a-zA-Z0-9]+)$/', $this->file, $match)) {
			return strtolower($match[1]);
		} else {
			return '';
		}
	}

	// What is the mime-type of this object?
	// For simplicity and efficiency, use the extension, rather than the contents.
	public function mimeType() {
		// Themes contain icon definitions for some/all of these mime-types
		switch ($this->extension()) {
		case 'bmp':  return 'image/bmp';
		case 'doc':  return 'application/msword';
		case 'docx': return 'application/msword';
		case 'ged':  return 'text/x-gedcom';
		case 'gif':  return 'image/gif';
		case 'htm':  return 'text/html';
		case 'html': return 'text/html';
		case 'jpeg': return 'image/jpeg';
		case 'jpg':  return 'image/jpeg';
		case 'mov':  return 'video/quicktime';
		case 'mp3':  return 'audio/mpeg';
		case 'ogv':  return 'video/ogg';
		case 'pdf':  return 'application/pdf';
		case 'png':  return 'image/png';
		case 'rar':  return 'application/x-rar-compressed';
		case 'swf':  return 'application/x-shockwave-flash';
		case 'svg':  return 'image/svg';
		case 'tif':  return 'image/tiff';
		case 'tiff': return 'image/tiff';
		case 'xls':  return 'application/vnd-ms-excel';
		case 'xlsx': return 'application/vnd-ms-excel';
		case 'wmv':  return 'video/x-ms-wmv';
		case 'zip':  return 'application/zip';
		default:     return 'application/octet-stream';
		}
	}

	// Display an image-thumbnail or a media-icon, and add markup for image viewers such as colorbox.
	// TODO - take a size parameter and generate different thumbnails for each size, rather than
	// always send the same image and resize it in the browser.
	public function displayImage() {
		if ($this->isExternal() || !file_exists($this->getServerFilename('thumb'))) {
			// Use an icon
			$mime_type = str_replace('/', '-', $this->mimeType());
			$image =
				'<i' .
				' dir="'   . 'auto'                           . '"' . // For the tool-tip
				' class="' . 'icon-mime-' . $mime_type        . '"' .
				' title="' . strip_tags($this->getFullName()) . '"' .
				'></i>';
		} else {
			$imgsize = getimagesize($this->getServerFilename('thumb'));
			// Use a thumbnail image
			$image =
				'<img' . 
				' dir="'   . 'auto'                           . '"' . // For the tool-tip
				' src="'   . $this->getHtmlUrlDirect('thumb') . '"' .
				' alt="'   . strip_tags($this->getFullName()) . '"' .
				' title="' . strip_tags($this->getFullName()) . '"' .
				$imgsize[3] . // height="yyy" width="xxx"
				'>';
		}

		return
			'<a' .
			' class="'          . 'gallery'                          . '"' .
			' href="'           . $this->getHtmlUrlDirect('main')    . '"' .
			' type="'           . $this->mimeType()                  . '"' .
			' data-obje-url="'  . $this->getHtmlUrl()                . '"' .
			' data-obje-note="' . htmlspecialchars($this->getNote()) . '"' .
			' data-title="'     . strip_tags($this->getFullName())   . '"' .
			'>' . $image . '</a>';
	}

	// If this object has no name, what do we call it?
	public function getFallBackName() {
		if ($this->canDisplayDetails()) {
			return basename($this->file);
		} else {
			return $this->getXref();
		}
	}

	// Get an array of structures containing all the names in the record
	public function getAllNames() {
		if (strpos($this->getGedcomRecord(), "\n1 TITL ")) {
			// Earlier gedcom versions had level 1 titles
			return parent::_getAllNames('TITL', 1);
		} else {
			// Later gedcom versions had level 2 titles
			return parent::_getAllNames('TITL', 2);
		}
	}

	// Extra info to display when displaying this record in a list of
	// selection items or favorites.
	public function format_list_details() {
		require_once WT_ROOT.'includes/functions/functions_print_facts.php';
		ob_start();
		print_media_links('1 OBJE @'.$this->getXref().'@', 1, $this->getXref());
		return ob_get_clean();
	}
}
