<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

use Fisharebest\Webtrees\Functions\FunctionsMedia;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;

/**
 * A GEDCOM media (OBJE) object.
 */
class Media extends GedcomRecord {
	const RECORD_TYPE = 'OBJE';
	const URL_PREFIX  = 'mediaviewer.php?mid=';

	/** @var string The "TITL" value from the GEDCOM */
	private $title = '';

	/** @var string The "FILE" value from the GEDCOM */
	private $file = '';

	/**
	 * Create a GedcomRecord object from raw GEDCOM data.
	 *
	 * @param string      $xref
	 * @param string      $gedcom  an empty string for new/pending records
	 * @param string|null $pending null for a record with no pending edits,
	 *                             empty string for records with pending deletions
	 * @param Tree        $tree
	 */
	public function __construct($xref, $gedcom, $pending, $tree) {
		parent::__construct($xref, $gedcom, $pending, $tree);

		if (preg_match('/\n1 FILE (.+)/', $gedcom . $pending, $match)) {
			$this->file = $match[1];
		}
		if (preg_match('/\n\d TITL (.+)/', $gedcom . $pending, $match)) {
			$this->title = $match[1];
		}
	}

	/**
	 * Each object type may have its own special rules, and re-implement this function.
	 *
	 * @param int $access_level
	 *
	 * @return bool
	 */
	protected function canShowByType($access_level) {
		// Hide media objects if they are attached to private records
		$linked_ids = Database::prepare(
			"SELECT l_from FROM `##link` WHERE l_to = ? AND l_file = ?"
		)->execute(array(
			$this->xref, $this->tree->getTreeId(),
		))->fetchOneColumn();
		foreach ($linked_ids as $linked_id) {
			$linked_record = GedcomRecord::getInstance($linked_id, $this->tree);
			if ($linked_record && !$linked_record->canShow($access_level)) {
				return false;
			}
		}

		// ... otherwise apply default behaviour
		return parent::canShowByType($access_level);
	}

	/**
	 * Fetch data from the database
	 *
	 * @param string $xref
	 * @param int    $tree_id
	 *
	 * @return null|string
	 */
	protected static function fetchGedcomRecord($xref, $tree_id) {
		return Database::prepare(
			"SELECT m_gedcom FROM `##media` WHERE m_id = :xref AND m_file = :tree_id"
		)->execute(array(
			'xref'    => $xref,
			'tree_id' => $tree_id,
		))->fetchOne();
	}

	/**
	 * Get the first note attached to this media object
	 *
	 * @return null|string
	 */
	public function getNote() {
		$note = $this->getFirstFact('NOTE');
		if ($note) {
			$text = $note->getValue();
			if (preg_match('/^@' . WT_REGEX_XREF . '@$/', $text)) {
				$text = $note->getTarget()->getNote();
			}

			return $text;
		} else {
			return '';
		}
	}

	/**
	 * Get the main media filename
	 *
	 * @return string
	 */
	public function getFilename() {
		return $this->file;
	}

	/**
	 * Get the media's title (name)
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Get the filename on the server - for those (very few!) functions which actually
	 * need the filename, such as mediafirewall.php and the PDF reports.
	 *
	 * @param string $which
	 *
	 * @return string
	 */
	public function getServerFilename($which = 'main', $individual_names = false) {
		$MEDIA_DIRECTORY = $this->tree->getPreference('MEDIA_DIRECTORY');
		$THUMBNAIL_WIDTH = $this->tree->getPreference('THUMBNAIL_WIDTH');

		$main_file = WT_DATA_DIR . $MEDIA_DIRECTORY . $this->file;

		if ($this->isExternal() || !$this->file) {
			// External image, or (in the case of corrupt GEDCOM data) no image at all
			return $this->file;
		} elseif ($which == 'main') {
			// Main image
			return $main_file;
		} else {
			// Thumbnail
			$file = WT_DATA_DIR . $MEDIA_DIRECTORY . 'thumbs/' . $this->file;
			// Is this a thumbnail of an individual?
			if ($individual_names) {
				// Get face data from file
				$faces = $this->getFaceDataFromFile($main_file);
				// Found faces?
				if ($faces) {
					// Search name in image with priority:
					$found = false;
					$found3 = false;
					// 1 - Name identical
					foreach ($individual_names as $individual_name) {
						foreach ($faces as $name=>$face){
							if ($individual_name['fullNN'] == $name) {
								$found = true;
								break 2;
							}
						}
					}
					if (!$found) {
						// 2 - Name contains one first name and surename
						foreach ($individual_names as $individual_name) {
							$givns=explode(' ',$individual_name['givn']);
							foreach ($givns as $givn) {
								foreach ($faces as $name=>$face){
									if (strpos($name,$givn) !== false) {
										if (!$found3) $found3=$name;
										if (strpos($name, $individual_name['surn']) !== false) {
											$found = true;
											break 3;
										}
									}
								}
							}
						}
					}
					if (!$found && $found3) {
						// 3 - Name contains one first name
						$name = $found3;
						$face = $faces[$name];
						$found = true;
					}
					// Found name in image?
					if ($found) {
						// Does thumbnail for this name exists?
						$pathinfo=pathinfo($file);
						$file=$pathinfo['dirname'].'/'.$pathinfo['filename']." ".strtr($name,array('/'=>'_')).'.'.$pathinfo['extension'];
						if (!file_exists($file)) {
							$this->createFaceThumbnail($main_file,$face,$file,$THUMBNAIL_WIDTH);
						}
					}
				}
			}
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
			if (!is_dir(dirname($file)) && !File::mkdir(dirname($file))) {
				Log::addMediaLog('The folder ' . dirname($file) . ' could not be created for ' . $this->getXref());

				return $file;
			}
			// Is there a corresponding main image?
			if (!file_exists($main_file)) {
				Log::addMediaLog('The file ' . $main_file . ' does not exist for ' . $this->getXref());

				return $file;
			}
			// Try to create a thumbnail automatically
			try {
				$imgsize = getimagesize($main_file);
				// Image small enough to be its own thumbnail?
				if ($imgsize[0] > 0 && $imgsize[0] <= $THUMBNAIL_WIDTH) {
					try {
						copy($main_file, $file);
						Log::addMediaLog('Thumbnail created for ' . $main_file . ' (copy of main image)');
					} catch (\ErrorException $ex) {
						Log::addMediaLog('Thumbnail could not be created for ' . $main_file . ' (copy of main image)');
					}
				} else {
					if (FunctionsMedia::hasMemoryForImage($main_file)) {
						try {
							switch ($imgsize['mime']) {
							case 'image/png':
								$main_image = imagecreatefrompng($main_file);
								break;
							case 'image/gif':
								$main_image = imagecreatefromgif($main_file);
								break;
							case 'image/jpeg':
								$main_image = imagecreatefromjpeg($main_file);
								break;
							default:
								return $file; // Nothing else we can do :-(
							}
							if ($main_image) {
								// How big should the thumbnail be?
								$width       = $THUMBNAIL_WIDTH;
								$height      = round($imgsize[1] * ($width / $imgsize[0]));
								$thumb_image = imagecreatetruecolor($width, $height);
								// Create a transparent background, instead of the default black one
								imagesavealpha($thumb_image, true);
								imagefill($thumb_image, 0, 0, imagecolorallocatealpha($thumb_image, 0, 0, 0, 127));
								// Shrink the image
								imagecopyresampled($thumb_image, $main_image, 0, 0, 0, 0, $width, $height, $imgsize[0], $imgsize[1]);
								switch ($imgsize['mime']) {
								case 'image/png':
									imagepng($thumb_image, $file);
									break;
								case 'image/gif':
									imagegif($thumb_image, $file);
									break;
								case 'image/jpeg':
									imagejpeg($thumb_image, $file);
									break;
								}
								imagedestroy($main_image);
								imagedestroy($thumb_image);
								Log::addMediaLog('Thumbnail created for ' . $main_file);
							}
						} catch (\ErrorException $ex) {
							Log::addMediaLog('Failed to create thumbnail for ' . $main_file);
						}
					} else {
						Log::addMediaLog('Not enough memory to create thumbnail for ' . $main_file);
					}
				}
			} catch (\ErrorException $ex) {
				// Not an image, or not a valid image?
			}

			return $file;
		}
	}

	/**
	 * check if the file exists on this server
	 *
	 * @param string $which specify either 'main' or 'thumb'
	 *
	 * @return bool
	 */
	public function fileExists($which = 'main') {
		return file_exists($this->getServerFilename($which));
	}

	/**
	 * Determine if the file is an external url
	 *
	 * @return bool
	 */
	public function isExternal() {
		return strpos($this->file, '://') !== false;
	}

	/**
	 * get the media file size in KB
	 *
	 * @param string $which specify either 'main' or 'thumb'
	 *
	 * @return string
	 */
	public function getFilesize($which = 'main') {
		$size = $this->getFilesizeraw($which);
		// Round up to the nearest KB.
		$size = (int) (($size + 1023) / 1024);

		return /* I18N: size of file in KB */ I18N::translate('%s KB', I18N::number($size));
	}

	/**
	 * get the media file size, unformatted
	 *
	 * @param string $which specify either 'main' or 'thumb'
	 *
	 * @return int
	 */
	public function getFilesizeraw($which = 'main') {
		try {
			return filesize($this->getServerFilename($which));
		} catch (\ErrorException $ex) {
			return 0;
		}
	}

	/**
	 * get filemtime for the media file
	 *
	 * @param string $which specify either 'main' or 'thumb'
	 *
	 * @return int
	 */
	public function getFiletime($which = 'main') {
		try {
			return filemtime($this->getServerFilename($which));
		} catch (\ErrorException $ex) {
			return 0;
		}
	}

	/**
	 * Generate an etag specific to this media item and the current user
	 *
	 * @param string $which - specify either 'main' or 'thumb'
	 *
	 * @return string
	 */
	public function getEtag($which = 'main') {
		if ($this->isExternal()) {
			// etag not really defined for external media

			return '';
		}
		$etag_string = basename($this->getServerFilename($which)) . $this->getFiletime($which) . $this->tree->getName() . Auth::accessLevel($this->tree) . $this->tree->getPreference('SHOW_NO_WATERMARK');
		$etag_string = dechex(crc32($etag_string));

		return $etag_string;
	}

	/**
	 * Deprecated? This does not need to be a function here.
	 *
	 * @return string
	 */
	public function getMediaType() {
		if (preg_match('/\n\d TYPE (.+)/', $this->gedcom, $match)) {
			return strtolower($match[1]);
		} else {
			return '';
		}
	}

	/**
	 * Is this object marked as a highlighted image?
	 *
	 * @return string
	 */
	public function isPrimary() {
		if (preg_match('/\n\d _PRIM ([YN])/', $this->getGedcom(), $match)) {
			return $match[1];
		} else {
			return '';
		}
	}

	/**
	 * get image properties
	 *
	 * @param string $which     specify either 'main' or 'thumb'
	 * @param int    $addWidth  amount to add to width
	 * @param int    $addHeight amount to add to height
	 *
	 * @return array
	 */
	public function getImageAttributes($which = 'main', $addWidth = 0, $addHeight = 0) {
		$THUMBNAIL_WIDTH = $this->tree->getPreference('THUMBNAIL_WIDTH');

		$var = $which . 'imagesize';
		if (!empty($this->$var)) {
			return $this->$var;
		}
		$imgsize = array();
		if ($this->fileExists($which)) {

			try {
				$imgsize = getimagesize($this->getServerFilename($which));
				if (is_array($imgsize) && !empty($imgsize['0'])) {
					// this is an image
					$imgsize[0]      = $imgsize[0] + 0;
					$imgsize[1]      = $imgsize[1] + 0;
					$imgsize['adjW'] = $imgsize[0] + $addWidth; // adjusted width
					$imgsize['adjH'] = $imgsize[1] + $addHeight; // adjusted height
					$imageTypes      = array('', 'GIF', 'JPG', 'PNG', 'SWF', 'PSD', 'BMP', 'TIFF', 'TIFF', 'JPC', 'JP2', 'JPX', 'JB2', 'SWC', 'IFF', 'WBMP', 'XBM');
					$imgsize['ext']  = $imageTypes[0 + $imgsize[2]];
					// this is for display purposes, always show non-adjusted info
					$imgsize['WxH']   =
						/* I18N: image dimensions, width × height */
						I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1']));
					$imgsize['imgWH'] = ' width="' . $imgsize['adjW'] . '" height="' . $imgsize['adjH'] . '" ';
					if (($which == 'thumb') && ($imgsize['0'] > $THUMBNAIL_WIDTH)) {
						// don’t let large images break the dislay
						$imgsize['imgWH'] = ' width="' . $THUMBNAIL_WIDTH . '" ';
					}
				}
			} catch (\ErrorException $ex) {
				// Not an image, or not a valid image?
				$imgsize = false;
			}
		}

		if (!is_array($imgsize) || empty($imgsize['0'])) {
			// this is not an image, OR the file doesn’t exist OR it is a url
			$imgsize[0]       = 0;
			$imgsize[1]       = 0;
			$imgsize['adjW']  = 0;
			$imgsize['adjH']  = 0;
			$imgsize['ext']   = '';
			$imgsize['mime']  = '';
			$imgsize['WxH']   = '';
			$imgsize['imgWH'] = '';
			if ($this->isExternal()) {
				// don’t let large external images break the dislay
				$imgsize['imgWH'] = ' width="' . $THUMBNAIL_WIDTH . '" ';
			}
		}

		if (empty($imgsize['mime'])) {
			// this is not an image, OR the file doesn’t exist OR it is a url
			// set file type equal to the file extension - can’t use parse_url because this may not be a full url
			$exp            = explode('?', $this->file);
			$imgsize['ext'] = strtoupper(pathinfo($exp[0], PATHINFO_EXTENSION));
			// all mimetypes we wish to serve with the media firewall must be added to this array.
			$mime = array(
				'DOC' => 'application/msword',
				'MOV' => 'video/quicktime',
				'MP3' => 'audio/mpeg',
				'PDF' => 'application/pdf',
				'PPT' => 'application/vnd.ms-powerpoint',
				'RTF' => 'text/rtf',
				'SID' => 'image/x-mrsid',
				'TXT' => 'text/plain',
				'XLS' => 'application/vnd.ms-excel',
				'WMV' => 'video/x-ms-wmv',
			);
			if (empty($mime[$imgsize['ext']])) {
				// if we don’t know what the mimetype is, use something ambiguous
				$imgsize['mime'] = 'application/octet-stream';
				if ($this->fileExists($which)) {
					// alert the admin if we cannot determine the mime type of an existing file
					// as the media firewall will be unable to serve this file properly
					Log::addMediaLog('Media Firewall error: >Unknown Mimetype< for file >' . $this->file . '<');
				}
			} else {
				$imgsize['mime'] = $mime[$imgsize['ext']];
			}
		}
		$this->$var = $imgsize;

		return $this->$var;
	}

	/**
	 * Generate a URL directly to the media file
	 *
	 * @param string $which
	 * @param bool   $download
	 *
	 * @return string
	 */
	public function getHtmlUrlDirect($which = 'main', $download = false, $individual_names = false) {
		// “cb” is “cache buster”, so clients will make new request if anything significant about the user or the file changes
		// The extension is there so that image viewers (e.g. colorbox) can do something sensible
		$thumbstr    = ($which == 'thumb') ? '&amp;thumb=1' : '';
		$downloadstr = ($download) ? '&dl=1' : '';
		$facedata = ($individual_names) ? '&face='.urlencode(serialize($individual_names)) : '';

		return
			'mediafirewall.php?mid=' . $this->getXref() . $thumbstr . $downloadstr . $facedata .
			'&amp;ged=' . $this->tree->getNameUrl() .
			'&amp;cb=' . $this->getEtag($which);
	}

	/**
	 * What file extension is used by this file?
	 *
	 * @return string
	 */
	public function extension() {
		if (preg_match('/\.([a-zA-Z0-9]+)$/', $this->file, $match)) {
			return strtolower($match[1]);
		} else {
			return '';
		}
	}

	/**
	 * What is the mime-type of this object?
	 * For simplicity and efficiency, use the extension, rather than the contents.
	 *
	 * @return string
	 */
	public function mimeType() {
		// Themes contain icon definitions for some/all of these mime-types
		switch ($this->extension()) {
		case 'bmp':
			return 'image/bmp';
		case 'doc':
			return 'application/msword';
		case 'docx':
			return 'application/msword';
		case 'ged':
			return 'text/x-gedcom';
		case 'gif':
			return 'image/gif';
		case 'htm':
			return 'text/html';
		case 'html':
			return 'text/html';
		case 'jpeg':
			return 'image/jpeg';
		case 'jpg':
			return 'image/jpeg';
		case 'mov':
			return 'video/quicktime';
		case 'mp3':
			return 'audio/mpeg';
		case 'ogv':
			return 'video/ogg';
		case 'pdf':
			return 'application/pdf';
		case 'png':
			return 'image/png';
		case 'rar':
			return 'application/x-rar-compressed';
		case 'swf':
			return 'application/x-shockwave-flash';
		case 'svg':
			return 'image/svg';
		case 'tif':
			return 'image/tiff';
		case 'tiff':
			return 'image/tiff';
		case 'xls':
			return 'application/vnd-ms-excel';
		case 'xlsx':
			return 'application/vnd-ms-excel';
		case 'wmv':
			return 'video/x-ms-wmv';
		case 'zip':
			return 'application/zip';
		default:
			return 'application/octet-stream';
		}
	}

	/**
	 * Display an image-thumbnail or a media-icon, and add markup for image viewers such as colorbox.
	 *
	 * @return string
	 */
	public function displayImage($individual_names = false) {
		$server_filename = $this->getServerFilename('thumb', $individual_names);
		if ($this->isExternal() || !file_exists($server_filename)) {
			// Use an icon
			$mime_type = str_replace('/', '-', $this->mimeType());
			$image     =
				'<i' .
				' dir="' . 'auto' . '"' . // For the tool-tip
				' class="' . 'icon-mime-' . $mime_type . '"' .
				' title="' . strip_tags($this->getFullName()) . '"' .
				'></i>';
		} else {
			$imgsize = getimagesize($server_filename);
			// Use a thumbnail image
			$image =
				'<img' .
				' dir="' . 'auto' . '"' . // For the tool-tip
				' src="' . $this->getHtmlUrlDirect('thumb', false, $individual_names) . '"' .
				' alt="' . strip_tags($this->getFullName()) . '"' .
				' title="' . strip_tags($this->getFullName()) . '"' .
				' ' . $imgsize[3] . // height="yyy" width="xxx"
				'>';
		}

		return
			'<a' .
			' class="' . 'gallery' . '"' .
			' href="' . $this->getHtmlUrlDirect('main') . '"' .
			' type="' . $this->mimeType() . '"' .
			' data-obje-url="' . $this->getHtmlUrl() . '"' .
			' data-obje-note="' . Filter::escapeHtml($this->getNote()) . '"' .
			' data-title="' . Filter::escapeHtml($this->getFullName()) . '"' .
			'>' . $image . '</a>';
	}

	/**
	 * If this object has no name, what do we call it?
	 *
	 * @return string
	 */
	public function getFallBackName() {
		if ($this->canShow()) {
			return basename($this->file);
		} else {
			return $this->getXref();
		}
	}

	/**
	 * Extract names from the GEDCOM record.
	 */
	public function extractNames() {
		// Earlier gedcom versions had level 1 titles
		// Later gedcom versions had level 2 titles
		$this->extractNamesFromFacts(2, 'TITL', $this->getFacts('FILE'));
		$this->extractNamesFromFacts(1, 'TITL', $this->getFacts('TITL'));
	}

	/**
	 * This function should be redefined in derived classes to show any major
	 * identifying characteristics of this record.
	 *
	 * @return string
	 */
	public function formatListDetails() {
		ob_start();
		FunctionsPrintFacts::printMediaLinks('1 OBJE @' . $this->getXref() . '@', 1);

		return ob_get_clean();
	}
	
	/**
	 * This function creates a single face thumbnail
	 */
	public function createFaceThumbnail($filename,$face,$thumbname,$width=false) {
		$this->readFileInformation($filename,$image,$info,$exif);
		$this->createThumbnail($face,$image,$info,$exif,$thumbname,$width);
	}
	
	/**
	 * This function reads file information for thumbnail generation
	 */
	public function readFileInformation($filename,&$image,&$info,&$exif) {
		$image=imagecreatefromjpeg($filename);
		$info=array(
			'x'=>imagesx($image),
			'y'=>imagesy($image),
		);
		$exif=exif_read_data($filename);
	}
	
	/**
	 * This function creates the thumbnail
	 *
	 * @return array
	 */
	public function createThumbnail($face,$image,$info,$exif,$thumbname,$width=false) {
		$rect=$this->getFaceCoordinates($face,$info);
		// Create thumbnail
		if($rect) {
			$crop=imagecrop($image,$rect);
			$crop=$this->rotateImageByExif($crop,$exif);
			if($width) {
				$thumb=imagescale($crop,$width);
				imagejpeg($thumb,$thumbname);
			} else {
				imagejpeg($crop,$thumbname);
			}
		}
		return $rect;
	}
	
	/**
	 * This function returns face information from a file
	 *
	 * @return array
	 */
	public function getFaceDataFromFile($filename) {
		// Does the image have a XMP tag?
		$xmp=$this->getXMPTagFromFile($filename);
		if($xmp) {
			// Get face data from XMP tag
			return $this->getFaceDataFromXMPTag($xmp);
		}
	}

	/**
	 * This function extracts the XMP tag from a file
	 *
	 * @return string
	 */
	public function getXMPTagFromFile($filename) {
		$xmp=false;
		require_once('JPEG.php');
		$headers=get_jpeg_header_data($filename);
		foreach($headers as $header){
			if($header['SegName']=='APP1' && substr($header['SegData'],0,28)=='http://ns.adobe.com/xap/1.0/'){
				$xmp=substr($header['SegData'],29);
			}
		}
		
		return $xmp;
	}
	
	/**
	 * This function returns face information from a XMP tag
	 *
	 * @return array
	 */
	public function getFaceDataFromXMPTag($xmp) {
		$faces=array();
		$name='';

		$xml_parser=xml_parser_create('UTF-8');
		xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE,0);
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_parse_into_struct($xml_parser, $xmp, $vals, $index);
		xml_parser_free($xml_parser);
		
		foreach($vals as $val) {
			switch($val['tag']){
				case 'rdf:Description':
					if($val['type']=='open' && !empty($val['attributes']['mwg-rs:Name']) && $val['attributes']['mwg-rs:Type']=='Face') {
						$name=trim($val['attributes']['mwg-rs:Name']);
					}
					if($val['type']=='close') {
						$name='';
					}
					break;
				case 'mwg-rs:Area':
					if($val['type']='complete' && $name) {
						$faces[$name]=$val['attributes'];
					}
					break;
			}
		}
		
		return $faces;
	}

	/**
	 * This function converts XMP coordinates to pixels
	 *
	 * @return array
	 */
	public function getFaceCoordinates($face,$info) {
		if($face['stArea:unit']=='normalized') {
			$rect=array(
				'x'=>intval(round($info['x']*($face['stArea:x']-$face['stArea:w']/2))),
				'y'=>intval(round($info['y']*($face['stArea:y']-$face['stArea:h']/2))),
				'width'=>intval(round($info['x']*$face['stArea:w'])),
				'height'=>intval(round($info['y']*$face['stArea:h'])),
			);

			return $rect;
		}
	}

	public function rotateImageByExif($image,$exif) {
		if(isset($exif['Orientation'])) {
			switch($exif['Orientation']) {
				case 2:
					imageflip($image,IMG_FLIP_VERTICAL);
					break;
				case 3:
					$image=imagerotate($image,180,0);
					break;
				case 4:
					imageflip($image,IMG_FLIP_HORIZONTAL);
					break;
				case 5:
					$image=imagerotate($image,270,0);
					imageflip($image,IMG_FLIP_VERTICAL);
					break;
				case 6:
					$image=imagerotate($image,270,0);
					break;
				case 7:
					$image=imagerotate($image,90,0);
					imageflip($image,IMG_FLIP_VERTICAL);
					break;
				case 8:
					$image=imagerotate($image,90,0);
					break;
			}
		}
		
		return $image;
	}
}
