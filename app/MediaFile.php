<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use League\Glide\Urls\UrlBuilderFactory;
use Throwable;

/**
 * A GEDCOM media file.  A media object can contain many media files,
 * such as scans of both sides of a document, the transcript of an audio
 * recording, etc.
 */
class MediaFile {
	const MIME_TYPES = [
		'bmp'  => 'image/bmp',
		'doc'  => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'ged'  => 'text/x-gedcom',
		'gif'  => 'image/gif',
		'html' => 'text/html',
		'htm'  => 'text/html',
		'jpeg' => 'image/jpeg',
		'jpg'  => 'image/jpeg',
		'mov'  => 'video/quicktime',
		'mp3'  => 'audio/mpeg',
		'mp4'  => 'video/mp4',
		'ogv'  => 'video/ogg',
		'pdf'  => 'application/pdf',
		'png'  => 'image/png',
		'rar'  => 'application/x-rar-compressed',
		'swf'  => 'application/x-shockwave-flash',
		'svg'  => 'image/svg',
		'tiff' => 'image/tiff',
		'tif'  => 'image/tiff',
		'xls'  => 'application/vnd-ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'wmv'  => 'video/x-ms-wmv',
		'zip'  => 'application/zip',
	];

	/** @var string The filename */
	private $multimedia_file_refn = '';

	/** @var string The file extension; jpeg, txt, mp4, etc. */
	private $multimedia_format = '';

	/** @var string The type of document; newspaper, microfiche, etc. */
	private $source_media_type = '';
	/** @var string The filename */

	/** @var string The name of the document */
	private $descriptive_title = '';

	/** @var Media $media The media object to which this file belongs */
	private $media;

	/** @var string */
	private $fact_id;

	/**
	 * Create a MediaFile from raw GEDCOM data.
	 *
	 * @param string $gedcom
	 * @param Media  $media
	 */
	public function __construct($gedcom, Media $media) {
		$this->media   = $media;
		$this->fact_id = md5($gedcom);

		if (preg_match('/^\d FILE (.+)/m', $gedcom, $match)) {
			$this->multimedia_file_refn = $match[1];
		}

		if (preg_match('/^\d FORM (.+)/m', $gedcom, $match)) {
			$this->multimedia_format = $match[1];
		}

		if (preg_match('/^\d TYPE (.+)/m', $gedcom, $match)) {
			$this->source_media_type = $match[1];
		}

		if (preg_match('/^\d TITL (.+)/m', $gedcom, $match)) {
			$this->descriptive_title = $match[1];
		}
	}

	/**
	 * Get the filename.
	 *
	 * @return string
	 */
	public function filename(): string {
		return $this->multimedia_file_refn;
	}

	/**
	 * Get the base part of the filename.
	 *
	 * @return string
	 */
	public function basename(): string {
		return basename($this->multimedia_file_refn);
	}

	/**
	 * Get the folder part of the filename.
	 *
	 * @return string
	 */
	public function dirname(): string {
		$dirname = dirname($this->multimedia_file_refn);

		if ($dirname === '.') {
			return '';
		} else {
			return $dirname;
		}
	}

	/**
	 * Get the format.
	 *
	 * @return string
	 */
	public function format(): string {
		return $this->multimedia_format;
	}

	/**
	 * Get the type.
	 *
	 * @return string
	 */
	public function type(): string {
		return $this->source_media_type;
	}

	/**
	 * Get the title.
	 *
	 * @return string
	 */
	public function title(): string {
		return $this->descriptive_title;
	}

	/**
	 * Get the fact ID.
	 *
	 * @return string
	 */
	public function factId(): string {
		return $this->fact_id;
	}

	/**
	 * @return bool
	 */
	public function isPendingAddition() {
		foreach ($this->media->getFacts() as $fact) {
			if ($fact->getFactId() === $this->fact_id) {
				return $fact->isPendingAddition();
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isPendingDeletion() {
		foreach ($this->media->getFacts() as $fact) {
			if ($fact->getFactId() === $this->fact_id) {
				return $fact->isPendingDeletion();
			}
		}

		return false;
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
		if ($this->isExternal()) {
			$src    = $this->multimedia_file_refn;
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
					'alt'    => htmlspecialchars_decode(strip_tags($this->media->getFullName())),
				]) . '>';

		if ($this->isImage()) {
			$attributes = Html::attributes([
				'class'      => 'gallery',
				'type'       => $this->mimeType(),
				'href'       => $this->imageUrl(0, 0, 'contain'),
				'data-title' => htmlspecialchars_decode(strip_tags($this->media->getFullName())),
			]);
		} else {
			$attributes = Html::attributes([
				'type' => $this->mimeType(),
				'href' => $this->downloadUrl(),
			]);
		}

		return '<a ' . $attributes . '>' . $image . '</a>';
	}

	/**
	 * A list of image attributes
	 *
	 * @return string[]
	 */
	public function attributes(): array {
		$attributes = [];

		if (!$this->isExternal() || $this->fileExists()) {
			$file = $this->folder() . $this->multimedia_file_refn;

			$attributes['__FILE_SIZE__'] = $this->fileSizeKB();

			$imgsize = getimagesize($file);
			if (is_array($imgsize) && !empty($imgsize['0'])) {
				$attributes['__IMAGE_SIZE__'] = I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1']));
			}
		}

		return $attributes;
	}

	/**
	 * check if the file exists on this server
	 *
	 * @return bool
	 */
	public function fileExists() {
		return !$this->isExternal() && file_exists($this->folder() . $this->multimedia_file_refn);
	}

	/**
	 * Is the media file actually a URL?
	 */
	public function isExternal(): bool {
		return strpos($this->multimedia_file_refn, '://') !== false;
	}

	/**
	 * Is the media file an image?
	 */
	public function isImage(): bool {
		return in_array($this->extension(), ['jpeg', 'jpg', 'gif', 'png']);
	}

	/**
	 * Where is the file stored on disk?
	 */
	public function folder(): string {
		return WT_DATA_DIR . $this->media->getTree()->getPreference('MEDIA_DIRECTORY');
	}

	/**
	 * A user-friendly view of the file size
	 *
	 * @return int
	 */
	private function fileSizeBytes(): int {
		try {
			return filesize($this->folder() . $this->multimedia_file_refn);
		} catch (Throwable $ex) {
			DebugBar::addThrowable($ex);

			return 0;
		}
	}

	/**
	 * get the media file size in KB
	 *
	 * @return string
	 */
	public function fileSizeKB() {
		$size = $this->filesizeBytes();
		$size = (int) (($size + 1023) / 1024);

		return /* I18N: size of file in KB */ I18N::translate('%s KB', I18N::number($size));
	}

	/**
	 * Get the filename on the server - for those (very few!) functions which actually
	 * need the filename, such as the PDF reports.
	 *
	 * @return string
	 */
	public function getServerFilename() {
		$MEDIA_DIRECTORY = $this->media->getTree()->getPreference('MEDIA_DIRECTORY');

		if ($this->isExternal() || !$this->multimedia_file_refn) {
			// External image, or (in the case of corrupt GEDCOM data) no image at all
			return $this->multimedia_file_refn;
		} else {
			// Main image
			return WT_DATA_DIR . $MEDIA_DIRECTORY . $this->multimedia_file_refn;
		}
	}

	/**
	 * Generate a URL to download a non-image media file.
	 *
	 * @return string
	 */
	public function downloadUrl() {
		return route('media-download', [
			'xref'    => $this->media->getXref(),
			'ged'     => $this->media->getTree()->getName(),
			'fact_id' => $this->fact_id,
		]);
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

		if (Auth::accessLevel($this->media->getTree()) > $this->media->getTree()->getPreference('SHOW_NO_WATERMARK')) {
			$mark = 'watermark.png';
		} else {
			$mark = '';
		}

		$url_builder = UrlBuilderFactory::create(WT_BASE_URL, $glide_key);

		$url = $url_builder->getUrl('index.php', [
			'route'     => 'media-thumbnail',
			'xref'      => $this->media->getXref(),
			'ged'       => $this->media->getTree()->getName(),
			'fact_id'   => $this->fact_id,
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
		if (preg_match('/\.([a-zA-Z0-9]+)$/', $this->multimedia_file_refn, $match)) {
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
		return self::MIME_TYPES[$this->extension()] ?? 'application/octet-stream';
	}
}
