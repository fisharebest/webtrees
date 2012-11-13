<?php
// Class that defines a media object
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
//
// Modifications Copyright (c) 2010 Greg Roach
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
	var $title         =null;
	var $file          =null;
	var $note          =null;
	var $localfilename =null;
	var $serverfilename=null;
	var $fileexists    =false;
	var $thumbfilename =null;
	var $thumbserverfilename=null;
	var $thumbfileexists=false;
	var $mainimagesize  =null;
	var $thumbimagesize =null;

	// Create a Media object from either raw GEDCOM data or a database row
	public function __construct($data) {
		if (is_array($data)) {
			// Construct from a row from the database
			$this->title=$data['m_titl'];
			$this->file =$data['m_filename'];
		} else {
			// Construct from raw GEDCOM data
			$this->title = get_gedcom_value('TITL', 1, $data);
			if (empty($this->title)) {
				$this->title = get_gedcom_value('TITL', 2, $data);
			}
			$this->file = get_gedcom_value('FILE', 1, $data);
		}
		if (empty($this->title)) $this->title = $this->file;

		parent::__construct($data);
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
		if (is_null($this->note)) {
			$this->note=get_gedcom_value('NOTE', 1, $this->getGedcomRecord());
		}
		return $this->note;
	}

	/**
	 * get the media icon filename
	 * @return string
	 */
	public function getMediaIcon() {
		return media_icon_file($this->file);
	}

	/**
	 * get the main media filename
	 * @return string
	 */
	public function getFilename() {
		return $this->file;
	}

	/**
	 * get the relative file path of the image on the server
	 * @param which string - specify either 'main' or 'thumb'
	 * @return string
	 */
	public function getLocalFilename($which='main') {
		if ($which=='main') {
			if (!$this->localfilename) $this->localfilename=check_media_depth($this->file);
			return $this->localfilename;
		} else {
			// this is a convenience method
			return $this->getThumbnail(false);
		}
	}

	/**
	 * get the filename on the server, either in the standard or protected directory
	 * @param which string - specify either 'main' or 'thumb'
	 * @return string
	 */
	public function getServerFilename($which='main') {
		if ($which=='main') {
			if ($this->serverfilename) return $this->serverfilename;
			$localfilename = $this->getLocalFilename($which);
			if (!empty($localfilename) && !$this->isExternal()) {
				if (file_exists($localfilename)) {
					// found image in unprotected directory
					$this->fileexists = 2;
					$this->serverfilename = $localfilename;
					return $this->serverfilename;
				}
				$protectedfilename = get_media_firewall_path($localfilename);
				if (file_exists($protectedfilename)) {
					// found image in protected directory
					$this->fileexists = 3;
					$this->serverfilename = $protectedfilename;
					return $this->serverfilename;
				}
			}
			// file doesn't exist or is external, return the standard localfilename for backwards compatibility
			$this->fileexists = false;
			$this->serverfilename = $localfilename;
			return $this->serverfilename;
		} else {
			if (!$this->thumbfilename) $this->getThumbnail(false);
			return $this->thumbserverfilename;
		}
	}

	/**
	 * check if the file exists on this server
	 * @param which string - specify either 'main' or 'thumb'
	 * @return boolean
	 */
	public function fileExists($which='main') {
		if ($which=='main') {
			if (!$this->serverfilename) $this->getServerFilename();
			return $this->fileexists;
		} else {
			if (!$this->thumbfilename) $this->getThumbnail(false);
			return $this->thumbfileexists;
		}
	}

	/**
	 * determine if the file is an external url
	 * operates on the main url
	 * @return boolean
	 */
	public function isExternal() {
		return isFileExternal($this->getLocalFilename('main'));
	}

	/**
	 * determine if the thumb file is a media icon
	 * operates on the thumb file
	 * @return boolean
	 */
	public function isMediaIcon() {
		$thumb=$this->getThumbnail(false);
		return (strpos($thumb, "themes/")!==false); 
	}

	/**
	 * get the thumbnail filename
	 * @return string
	 */
	public function getThumbnail($generateThumb = true) {
		if ($this->thumbfilename) return $this->thumbfilename;

		$localfilename = thumbnail_file($this->getLocalFilename(),$generateThumb);
		// Note that localfilename could be in WT_IMAGES
		$this->thumbfilename = $localfilename;
		if (!empty($localfilename) && !$this->isExternal()) {
			if (file_exists($localfilename)) {
				// found image in unprotected directory
				$this->thumbfileexists = 2;
				$this->thumbserverfilename = $localfilename;
				return $this->thumbfilename;
			}
			$protectedfilename = get_media_firewall_path($localfilename);
			if (file_exists($protectedfilename)) {
				// found image in protected directory
				$this->thumbfileexists = 3;
				$this->thumbserverfilename = $protectedfilename;
				return $this->thumbfilename;
			}
		}

		// this should never happen, since thumbnail_file will return something in WT_IMAGES if a thumbnail can't be found
		$this->thumbfileexists = false;
		$this->thumbserverfilename = $localfilename;
		return $this->thumbfilename;
	}


	/**
	 * get the media file size in KB
	 * @param which string - specify either 'main' or 'thumb'
	 * @return string
	 */
	public function getFilesize($which='main') {
		$size = $this->getFilesizeraw($which);
		if ($size) $size=(int)(($size+1023)/1024); // add some bytes to be sure we never return "0 KB"
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
		// setup the etag.  use enough info so that if anything important changes, the etag won't match
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
	 * get the media FORM from the gedcom.  if not defined, calculate from file extension 
	 * @return string
	 */
	public function getMediaFormat() {
		$mediaFormat = get_gedcom_value('FORM', 2, $this->getGedcomRecord());
		if (!$mediaFormat) {
			$imgsize=$this->getImageAttributes('main');
			$mediaFormat=$imgsize['ext'];
		}
		return $mediaFormat;
	}

	/**
	 * get the media type from the gedcom
	 * @return string
	 */
	public function getMediaType() {
		$mediaType = strtolower(get_gedcom_value('FORM:TYPE', 2, $this->getGedcomRecord()));
		return $mediaType;
	}

	/**
	 * get the media _PRIM from the gedcom
	 * @return string
	 */
	public function isPrimary() {
		$prim = get_gedcom_value("_PRIM", 1, $this->getGedcomRecord());
		return $prim;
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
				$imgsize['WxH']=/* I18N: image dimensions, width x height */ WT_I18N::translate('%1$s × %2$s pixels', WT_I18N::number($imgsize['0']), WT_I18N::number($imgsize['1']));
				$imgsize['imgWH']=' width="'.$imgsize['adjW'].'" height="'.$imgsize['adjH'].'" ';
				if ( ($which=='thumb') && ($imgsize['0'] > $THUMBNAIL_WIDTH) ) {
					// don't let large images break the dislay
					$imgsize['imgWH']=' width="'.$THUMBNAIL_WIDTH.'" ';
				}
			}
		}

		if (!is_array($imgsize) || empty($imgsize['0'])) {
			// this is not an image, OR the file doesn't exist OR it is a url
			$imgsize[0]=0;
			$imgsize[1]=0;
			$imgsize['adjW']=0;
			$imgsize['adjH']=0;
			$imgsize['ext']='';
			$imgsize['mime']='';
			$imgsize['WxH']='';
			$imgsize['imgWH']='';
			if ($this->isExternal($which)) {
				// don't let large external images break the dislay
				$imgsize['imgWH']=' width="'.$THUMBNAIL_WIDTH.'" ';
			}
		}

		if (empty($imgsize['mime'])) {
			// this is not an image, OR the file doesn't exist OR it is a url
			// set file type equal to the file extension - can't use parse_url because this may not be a full url
			$exp = explode('?', $this->file);
			$pathinfo = pathinfo($exp[0]);
			$imgsize['ext']=@strtoupper($pathinfo['extension']);
			// all mimetypes we wish to serve with the media firewall must be added to this array.
			$mime=array('DOC'=>'application/msword', 'MOV'=>'video/quicktime', 'MP3'=>'audio/mpeg', 'PDF'=>'application/pdf',
			'PPT'=>'application/vnd.ms-powerpoint', 'RTF'=>'text/rtf', 'SID'=>'image/x-mrsid', 'TXT'=>'text/plain', 'XLS'=>'application/vnd.ms-excel',
			'WMV'=>'video/x-ms-wmv');
			if (empty($mime[$imgsize['ext']])) {
				// if we don't know what the mimetype is, use something ambiguous
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


	/**
	 * Generate a URL directly to the media file, suitable for use in HTML
	 * @param which string - specify either 'main' or 'thumb'
	 * @param separator string - specify either '&amp;' or '&'
	 * @return string
	 */
	public function getHtmlUrlDirect($which='main', $download=false, $separator = '&amp;') {

	 	if ($this->isExternal()) {
			// this is an external file, do not try to access it through the media firewall
			if ($separator == '&') {
				return rawurlencode($this->getFilename());
			} else {
				return $this->getFilename();
			}
		} else {
			// 'cb' is 'cache buster', so clients will make new request if anything significant about the user or the file changes
			$thumbstr = ($which=='thumb') ? $separator.'thumb=1' : '';
			$downloadstr = ($download) ? $separator.'dl=1' : '';
			return 'mediafirewall.php?mid='.$this->getXref().$thumbstr.$downloadstr.$separator.'ged='.rawurlencode(get_gedcom_from_id($this->ged_id)).$separator.'cb='.$this->getEtag($which);
		}
	}
	// Generate a URL directly to the media file, suitable for use in javascript, HTTP headers, etc.
	public function getRawUrlDirect($which='main', $download=false) {
		return $this->getHtmlUrlDirect($which, $download, '&');
	}

	/**
	 * if this is a Google Streetview url, return the HTML required to display it
	* if not a Google Streetview url, return ''
	* @return string
	 */
	public function getHtmlForStreetview() {
		if (strpos($this->getHtmlUrlDirect('main'), 'http://maps.google.')===0) {
			return '<iframe style="float:left; padding:5px;" width="264" height="176" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$this->getHtmlUrlDirect('main').'&amp;output=svembed"></iframe>';
		}
		return '';
	}

	/**
	 * builds html snippet with javascript, etc appropriate to view the media file
	 * @param array with optional parameters: 
	 *    'obeyViewerOption'=>true|false, default is 'true'
	 *    'uselightbox'=>true|false,  default is true - if set to true, will use global settings for lightbox.  if false, will not use regardless of global settings
	 *    'uselightbox_fallback'=>true|false,  default is true - if lb is not available, should we use  fallback javascript (true) or link directly to media viewer (false)
	 *    'usejavascript'=>true|false,  default is true - set to false to ensure no javascript will be used in the link
	 *    'clearbox'=>'general'|'general_1' etc
	 *    'img_title'=>string (optional) - image title to override the default.  must run htmlspecialchars() priort to sending
	 * @return string, suitable for use inside an a tag: '<a href="'.$this->getHtmlUrlSnippet().'">';
	 */
	public function getHtmlUrlSnippet(array $config = array()) {
		global $USE_MEDIA_VIEWER;

		$default_config=array(
			'obeyViewerOption'=>true,
			'uselightbox'=>true,
			'uselightbox_fallback'=>true,
			'usejavascript'=>true,
			'clearbox'=>'general',
			'img_title'=>''
		 );
		$config=array_merge($default_config, $config);

		$urltype = get_url_type($this->getLocalFilename());
		$notes=($this->getNote()) ? htmlspecialchars(print_fact_notes("1 NOTE ".$this->getNote(), 1, true, true)) : '';
		if ($config['img_title']) {
			$config['img_title']=strip_tags($config['img_title']);
		} else {
			$config['img_title']=strip_tags($this->getFullName());
		}

		// -- Determine the correct URL to open this media file
		while (true) {
			if (WT_USE_LIGHTBOX && $config['uselightbox'] && $config['usejavascript'] && (WT_THEME_DIR!=WT_THEMES_DIR.'_administration/')) {
				// Lightbox is installed
				switch ($urltype) {
				case 'url_flv':
					$url = 'js/jw_player/flvVideo.php?flvVideo='.$this->getRawUrlDirect('main') . "\" rel='clearbox(500, 392, click)' rev=\"" . $this->getXref() . "::" . get_gedcom_from_id($this->ged_id) . "::" . htmlspecialchars($config['img_title']) . "::" . htmlspecialchars($notes);
					break 2;
				case 'local_flv':
					$url = 'js/jw_player/flvVideo.php?flvVideo='.WT_SERVER_NAME.WT_SCRIPT_PATH.$this->getRawUrlDirect('main') . "\" rel='clearbox(500, 392, click)' rev=\"" . $this->getXref() . "::" . get_gedcom_from_id($this->ged_id) . "::" . htmlspecialchars($config['img_title']) . "::" . htmlspecialchars($notes);
					break 2;
				case 'url_audio':
				case 'url_wmv':
					$url = 'js/jw_player/wmvVideo.php?wmvVideo='.$this->getRawUrlDirect('main') . "\" rel='clearbox(500, 392, click)' rev=\"" . $this->getXref() . "::" . get_gedcom_from_id($this->ged_id) . "::" . htmlspecialchars($config['img_title']) . "::" . htmlspecialchars($notes);
					break 2;
				case 'local_audio':
				case 'local_wmv':
					$url = 'js/jw_player/wmvVideo.php?wmvVideo='.WT_SERVER_NAME.WT_SCRIPT_PATH.$this->getRawUrlDirect('main') . "\" rel='clearbox(500, 392, click)' rev=\"" . $this->getXref() . "::" . get_gedcom_from_id($this->ged_id) . "::" . htmlspecialchars($config['img_title']) . "::" . htmlspecialchars($notes);
					break 2;
				case 'url_image':
				case 'local_image':
					$url = $this->getHtmlUrlDirect('main') . "\" rel=\"clearbox[" . $config['clearbox'] . "]\" rev=\"" . $this->getXref() . "::" . get_gedcom_from_id($this->ged_id) . "::" . htmlspecialchars($config['img_title']) . "::" . htmlspecialchars($notes);
					break 2;
				case 'url_picasa':
				case 'url_page':
				case 'url_pdf':
				case 'url_other':
				case 'url_document':
				// case 'local_other':
				case 'local_page':
				case 'local_pdf':
				case 'local_document':
					$url = $this->getHtmlUrlDirect('main') . "\" rel='clearbox(" . get_module_setting('lightbox', 'LB_URL_WIDTH',  '1000') . ',' . get_module_setting('lightbox', 'LB_URL_HEIGHT', '600') . ", click)' rev=\"" . $this->getXref() . "::" . get_gedcom_from_id($this->ged_id) . "::" . htmlspecialchars($config['img_title']) . "::" . htmlspecialchars($notes);
					break 2;
				case 'url_streetview':
					// need to call getHtmlForStreetview() instead of getHtmlUrlSnippet()
					break 2;
				}
			}
			if ($config['uselightbox_fallback'] && $config['usejavascript']) {
				// Lightbox is not installed or Lightbox is not appropriate for this media type
				switch ($urltype) {
				case 'url_flv':
					$url = "#\" onclick=\" var winflv = window.open('".'js/jw_player/flvVideo.php?flvVideo='.$this->getRawUrlDirect('main') . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
					break 2;
				case 'local_flv':
					$url = "#\" onclick=\" var winflv = window.open('".'js/jw_player/flvVideo.php?flvVideo='.WT_SERVER_NAME.WT_SCRIPT_PATH.$this->getRawUrlDirect('main') . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
					break 2;
				case 'url_audio':
				case 'url_wmv':
					$url = "#\" onclick=\" var winwmv = window.open('".'js/jw_player/wmvVideo.php?wmvVideo='.$this->getRawUrlDirect('main') . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
					break 2;
				case 'local_audio':
				case 'local_wmv':
					$url = "#\" onclick=\" var winwmv = window.open('".'js/jw_player/wmvVideo.php?wmvVideo='.WT_SERVER_NAME.WT_SCRIPT_PATH.$this->getRawUrlDirect('main') . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
					break 2;
				case 'url_image':
				case 'local_image':
					$imgsize = $this->getImageAttributes('main',40,150);
					if ($imgsize['0']) {
						$url = "#\" onclick=\"var winimg = window.open('".$this->getRawUrlDirect('main')."', 'winimg', 'width=".$imgsize['adjW'].", height=".$imgsize['adjH'].", left=200, top=200'); if (window.focus) {winimg.focus();}";
					} else {
						$url = $this->getHtmlUrl();
					}
					break 2;
				case 'url_picasa':
				case 'url_page':
				case 'url_pdf':
				case 'url_other':
				case 'url_document':
					$url = "#\" onclick=\"var winurl = window.open('".$this->getRawUrlDirect('main')."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
					break 2;
				case 'local_other';
				case 'local_page':
				case 'local_pdf':
				case 'local_document':
					$url = "#\" onclick=\"var winurl = window.open('".WT_SERVER_NAME.WT_SCRIPT_PATH.$this->getRawUrlDirect('main')."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
					break 2;
				case 'url_streetview':
					// need to call getHtmlForStreetview() instead of getHtmlUrlSnippet()
					break 2;
				}
			}

			// final option if nothing else worked
			if (($USE_MEDIA_VIEWER && $config['obeyViewerOption']) || !$config['usejavascript']) {
				$url = $this->getHtmlUrl();
			} else {
				$imgsize = $this->getImageAttributes('main',40,150);
				if ($imgsize['0']) {
					$url = str_replace('mediaviewer.php?','imageview.php?', $this->getHtmlUrl());
					$url = "#\" onclick=\"return openImage('".$url."', ".$imgsize['adjW'].", ".$imgsize['adjH'].");";
				} else {
					$url = $this->getHtmlUrl();
				}
			}
			break;
		}

		return $url;
	}

	/**
	 * returns the complete HTML needed to render a thumbnail image that is linked to the main image
	 * @param array with optional parameters: 
	 *    'download'=>true|false, default is false - whether or not to show a 'download file' link
	 *    'display_type'=>'normal'|'pedigree_person'|'treeview'|'googlemap' the type of image this is
	 *    'img_id'=>string (optional) - if this image needs an id, set it here
	 *    'class'=>string (optional) - class to assign to image
	 *    'img_title'=>string (optional) - image title to override the default.  must run htmlspecialchars() priort to sending
	 *    'addslashes'=>true|false, default is false - if result will be stored in javascript array (such as googlemaps) set to true
	 *    'oktolink'=>true|false, default is true - whether to include link to main image
	 *    'alertnotfound'=>true|false, default is false - whether to display error when main image is missing
	 *    'show_full'=>true|false, default is true - whether to show or hide the image 
	 * @return string
	 */
	public function displayMedia(array $config = array()) {
		global $TEXT_DIRECTION,$SHOW_MEDIA_DOWNLOAD;

		$default_config=array(
			'download'=>false,
			'display_type'=>'normal',
			'img_id'=>'',
			'class'=>'thumbnail',
			'img_title'=>'',
			'addslashes'=>false,
			'oktolink'=>true,
			'alertnotfound'=>false,
			'show_full'=>true
		 );
		$config=array_merge($default_config, $config);
		if ($this->getHtmlForStreetview()) {
			$output=$this->getHtmlForStreetview();
		} else {

			if ($config['display_type']=='pedigree_person') {
				// 
				$config['uselightbox_fallback']=false;
				$config['clearbox']='general_2';        
				$imgsizeped=$this->getImageAttributes('thumb');
				$config['class']='pedigree_image';
			}
			if ($config['display_type']=='treeview') {
				// 
				$config['uselightbox_fallback']=false;
				$imgsizeped=$this->getImageAttributes('thumb');
				$config['class']='tv_link pedigree_image';
			}
			if ($config['display_type']=='googlemap') {
				// used on google maps tab on indi page
				$config['oktolink']=false;
				$config['addslashes']=true;
				$imgsizeped=$this->getImageAttributes('thumb');
				$config['class']='pedigree_image';
			}

			$mainexists=$this->isExternal() || $this->fileExists('main');
			$idstr=($config['img_id']) ? 'id="'.$config['img_id'].'"' : '';
			$stylestr=($config['show_full']) ? '' : ' style="display: none;" ';
			if ($config['img_title']) {
				$config['img_title']=strip_tags($config['img_title']);
			} else {
				$config['img_title']=strip_tags($this->getFullName());
			}
			$sizestr='';
			if ($config['class']=='thumbnail') {
				// only set width/height when class==thumbnail, all other classes control the width/height
				$imgsize=$this->getImageAttributes('thumb');
				$sizestr=$imgsize['imgWH'];
			}

			$output='';
			if ($config['oktolink'] && $mainexists) $output .= '<a class="media_container" href="'.$this->getHtmlUrlSnippet($config).'">';
			$output .= '<img '.$idstr.' src="'.$this->getHtmlUrlDirect('thumb').'" '.$sizestr.' class="'.$config['class'].'"';
			$output .= ' alt="'.$config['img_title'].'" title="'.$config['img_title'].'" '.$stylestr.'>';
			if ($config['oktolink'] && $mainexists) {
				$output .= '</a>';
				if ($config['download'] && $SHOW_MEDIA_DOWNLOAD) {
					$output .= '<div><a href="'.$this->getHtmlUrlDirect('main', true).'">'.WT_I18N::translate('Download File').'</a></div>';
				}
			} else if ($config['alertnotfound'] && !$mainexists) {
				$output .= '<p class="ui-state-error">' . WT_I18N::translate('The file “%s” does not exist.', $this->getLocalFilename()) . '</p>';
				
			}
		}
		if ($config['addslashes']) {
			// the image string will be used in javascript code, such as googlemaps
			$output=addslashes($output);
		}
		return $output;
	}

	/**
	 * output the list of linked records
	 * @param size='small'|'normal'
	 * @return string
	 */
	public function printLinkedRecords($size = "small") {
		if ($size != "small") $size = "normal";
		$linkList = array ();

		foreach ($this->fetchLinkedIndividuals() as $indi) {
			if ($indi->canDisplaydetails()) {
				$linkItem=array ();
				$linkItem['MEDIASORT']='A'.$indi->getSortName();
				$linkItem['record']=$indi;
				$linkList[]=$linkItem;
			}
		}
		foreach ($this->fetchLinkedFamilies() as $fam) {
			if ($fam->canDisplaydetails()) {
				$linkItem=array ();
				$linkItem['MEDIASORT']='B'.$fam->getSortName();
				$linkItem['record']=$fam;
				$linkList[]=$linkItem;
			}
		}
		foreach ($this->fetchLinkedSources() as $sour) {
			if ($sour->canDisplaydetails()) {
				$linkItem=array ();
				$linkItem['MEDIASORT']='C'.$sour->getSortName();
				$linkItem['record']=$sour;
				$linkList[]=$linkItem;
			}
		}

		uasort($linkList, "mediasort");

		$output="";
		if ($size == "small") $output.="<sub>";
		$prev_record=null;
		foreach ($linkList as $linkItem) {
			$record=$linkItem['record'];
			if ($prev_record && $prev_record->getType()!=$record->getType()) {
				$output.='<br>';
			}
			$output.='<br><a class="media_link" href="'.$record->getHtmlUrl().'">';
			switch ($record->getType()) {
			case 'INDI':
				$output.=WT_I18N::translate('View Person');
				break;
			case 'FAM':
				$output.=WT_I18N::translate('View Family');
				break;
			case 'SOUR':
				$output.=WT_I18N::translate('View Source');
				break;
			}
			$output.=' -- '.$record->getFullname().'</a>';
			$prev_record=$record;
		}
		if ($size == "small") $output.="</sub>";
		return ($output);
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
