<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use League\Glide\Urls\UrlBuilderFactory;

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
		)->execute([
			$this->xref, $this->tree->getTreeId(),
		])->fetchOneColumn();
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
		)->execute([
			'xref'    => $xref,
			'tree_id' => $tree_id,
		])->fetchOne();
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
	 * @return string
	 */
	public function getServerFilename() {
		$MEDIA_DIRECTORY = $this->tree->getPreference('MEDIA_DIRECTORY');

		if ($this->isExternal() || !$this->file) {
			// External image, or (in the case of corrupt GEDCOM data) no image at all
			return $this->file;
		} else {
			// Main image
			return WT_DATA_DIR . $MEDIA_DIRECTORY . $this->file;
		}
	}

	/**
	 * check if the file exists on this server
	 *
	 * @return bool
	 */
	public function fileExists() {
		return file_exists($this->getServerFilename());
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
	 * @return string
	 */
	public function getFilesize() {
		$size = $this->getFilesizeraw();
		// Round up to the nearest KB.
		$size = (int) (($size + 1023) / 1024);

		return /* I18N: size of file in KB */
			I18N::translate('%s KB', I18N::number($size));
	}

	/**
	 * get the media file size, unformatted
	 *
	 * @return int
	 */
	public function getFilesizeraw() {
		try {
			return filesize($this->getServerFilename());
		} catch (\ErrorException $ex) {
			return 0;
		}
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
	 * get image properties
	 *
	 * @return array
	 */
	public function getImageAttributes() {
		$imgsize = [];
		if ($this->fileExists()) {
			try {
				$imgsize = getimagesize($this->getServerFilename());
				if (is_array($imgsize) && !empty($imgsize['0'])) {
					// this is an image
					$imageTypes      = ['', 'GIF', 'JPG', 'PNG', 'SWF', 'PSD', 'BMP', 'TIFF', 'TIFF', 'JPC', 'JP2', 'JPX', 'JB2', 'SWC', 'IFF', 'WBMP', 'XBM'];
					$imgsize['ext']  = $imageTypes[0 + $imgsize[2]];
					// this is for display purposes, always show non-adjusted info
					$imgsize['WxH']  = /* I18N: image dimensions, width × height */
						I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1']));
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
			$imgsize['ext']   = '';
			$imgsize['mime']  = '';
			$imgsize['WxH']   = '';
		}

		if (empty($imgsize['mime'])) {
			// this is not an image, OR the file doesn’t exist OR it is a url
			// set file type equal to the file extension - can’t use parse_url because this may not be a full url
			$exp            = explode('?', $this->file);
			$imgsize['ext'] = strtoupper(pathinfo($exp[0], PATHINFO_EXTENSION));
			// all mimetypes we wish to serve with the media firewall must be added to this array.
			$mime = [
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
			];
			if (empty($mime[$imgsize['ext']])) {
				// if we don’t know what the mimetype is, use something ambiguous
				$imgsize['mime'] = 'application/octet-stream';
				if ($this->fileExists()) {
					// alert the admin if we cannot determine the mime type of an existing file
					// as the media firewall will be unable to serve this file properly
					Log::addMediaLog('Media Firewall error: >Unknown Mimetype< for file >' . $this->file . '<');
				}
			} else {
				$imgsize['mime'] = $mime[$imgsize['ext']];
			}
		}

		return $imgsize;
	}

	/**
	 * Generate a URL for an image.
	 *
	 * @param int    $width  Maximum width in pixels
	 * @param int    $height Maximum height in pixels
	 * @param string $fit    "crop" or "contain"
	 *
	 * @return string
	 */
	public function imageUrl($width, $height, $fit) {
		// Sign the URL, to protect against mass-resize attacks.
		$glide_key = Site::getPreference('glide-key');
		if (empty($glide_key)) {
			$glide_key = bin2hex(random_bytes(128));
			Site::setPreference('glide-key', $glide_key);
		}

		if (Auth::accessLevel($this->getTree()) > $this->getTree()->getPreference('SHOW_NO_WATERMARK')) {
			$mark = 'watermark.png';
		} else {
			$mark = '';
		}

		$url = UrlBuilderFactory::create(WT_BASE_URL, $glide_key)
			->getUrl('mediafirewall.php', [
				'mid'       => $this->getXref(),
				'ged'       => $this->tree->getName(),
				'w'         => $width,
				'h'         => $height,
				'fit'       => $fit,
				'mark'      => $mark,
				'markh'     => '100h',
				'markw'     => '100w',
				'markalpha' => 25,
				'or'        => 0, // Intervention uses exif_read_data() which is very buggy.
			]);

		return $url;
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
		case 'mp4':
			return 'video/mp4';
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
	 * @param int      $width      Pixels
	 * @param int      $height     Pixels
	 * @param string   $fit        "crop" or "contain"
	 * @param string[] $attributes Additional HTML attributes
	 *
	 * @return string
	 */
	public function displayImage($width, $height, $fit, $attributes = []) {
		// Default image for external, missing or corrupt images.
		$image
			= '<i' .
			' dir="auto"' . // For the tool-tip
			' class="icon-mime-' . str_replace('/', '-', $this->mimeType()) . '"' .
			' title="' . strip_tags($this->getFullName()) . '"' .
			'></i>';

		// Use a thumbnail image.
		if ($this->isExternal()) {
			$src    = $this->getFilename();
			$srcset = [];
		} else {
			// Generate multiple images for displays with higher pixel densities.
			$src    = $this->imageUrl($width, $height, $fit);
			$srcset = [];
			foreach ([2, 3, 4] as $x) {
				$srcset[] = $this->imageUrl($width * $x, $height * $x, $fit) . ' ' . $x . 'x';
			}
		}

		$image = '<img ' . Html::attributes($attributes + [
					'dir'    => 'auto',
					'src'    => $src,
					'srcset' => implode(',', $srcset),
					'alt'    => strip_tags($this->getFullName()),
				]) . '>';

		$attributes = Html::attributes([
			'class' => 'gallery',
			'type'  => $this->mimeType(),
			'href'  => $this->imageUrl(0, 0, ''),
		]);

		return '<a ' . $attributes . '>' . $image . '</a>';
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
}
