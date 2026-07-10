<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Factories;

use DOMDocument;
use DOMElement;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\ImageFactoryInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Enums\ExifOrientation;
use Fisharebest\Webtrees\Enums\ImageOperation;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Webtrees;
use GdImage;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToRetrieveMetadata;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

use function addcslashes;
use function basename;
use function file_get_contents;
use function fclose;
use function fopen;
use function get_class;
use function implode;
use function imagealphablending;
use function imagebmp;
use function imagecolorallocatealpha;
use function imagecopy;
use function imagecopyresampled;
use function imagecreatefromstring;
use function imagecreatetruecolor;
use function imagefilledrectangle;
use function imageflip;
use function imagegif;
use function imagejpeg;
use function imagepng;
use function imagerotate;
use function imagesavealpha;
use function imagesx;
use function imagesy;
use function imagewebp;
use function in_array;
use function is_array;
use function is_string;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function max;
use function min;
use function ob_end_clean;
use function ob_get_clean;
use function ob_start;
use function pathinfo;
use function preg_replace;
use function round;
use function response;
use function rewind;
use function str_starts_with;
use function strtolower;
use function view;
use function fwrite;

use const IMG_FLIP_HORIZONTAL;
use const IMG_FLIP_VERTICAL;
use const LIBXML_NONET;
use const PATHINFO_EXTENSION;

class ImageFactory implements ImageFactoryInterface
{
    // GD does not expose source compression quality, so use stable defaults.
    protected const int IMAGE_QUALITY     = 90;
    protected const int THUMBNAIL_QUALITY = 70;

    protected const string WATERMARK_FILE = 'resources/img/watermark.png';

    protected const int THUMBNAIL_CACHE_TTL = 8640000;

    private const array DANGEROUS_SVG_TAGS = [
        'script',
        'foreignobject',
        'iframe',
        'object',
        'embed',
        'handler',
    ];

    public const array SUPPORTED_FORMATS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/tiff' => 'tif',
        'image/bmp'  => 'bmp',
        'image/webp' => 'webp',
    ];

    public function __construct(
        private PhpService $php_service,
    ) {
    }

    public function fileResponse(FilesystemOperator $filesystem, string $path, bool $download): ResponseInterface
    {
        try {
            try {
                $mime_type = $filesystem->mimeType(path: $path);
            } catch (UnableToRetrieveMetadata) {
                $mime_type = Mime::DEFAULT_TYPE;
            }

            $filename = $download ? addcslashes(string: basename(path: $path), characters: '"') : '';

            return $this->imageResponse(data: $filesystem->read(location: $path), mime_type: $mime_type, filename: $filename);
        } catch (FilesystemException $ex) {
            return $this->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_NOT_FOUND)
                ->withHeader('x-file-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        }
    }

    public function thumbnailResponse(
        FilesystemOperator $filesystem,
        string $path,
        int $width,
        int $height,
        ImageOperation $operation,
    ): ResponseInterface {
        try {
            $mime_type   = $filesystem->mimeType(path: $path);
            $binary      = $filesystem->read(location: $path);
            $orientation = $this->extractExifOrientation(binary: $binary);
            $image       = $this->decodeImage(binary: $binary);
            $image       = $this->autoRotateImage(image: $image, orientation: $orientation);
            $image       = $this->resizeImage(image: $image, width: $width, height: $height, operation: $operation);
            $data        = $this->encodeImage(image: $image, mime_type: $mime_type, quality: self::THUMBNAIL_QUALITY);

            return $this->imageResponse(data: $data, mime_type: $mime_type, filename: '');
        } catch (FilesystemException $ex) {
            return $this
                ->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_NOT_FOUND)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        } catch (RuntimeException $ex) {
            return $this
                ->replacementImageResponse(text: '.' . pathinfo(path: $path, flags: PATHINFO_EXTENSION))
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        } catch (Throwable $ex) {
            return $this
                ->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        }
    }

    public function mediaFileResponse(MediaFile $media_file, bool $add_watermark, bool $download): ResponseInterface
    {
        $filesystem = $media_file->media()->tree()->mediaFilesystem();
        $path       = $media_file->filename();

        if (!$add_watermark || !$media_file->isImage()) {
            return $this->fileResponse(filesystem: $filesystem, path: $path, download: $download);
        }

        try {
            $mime_type   = $media_file->mimeType();
            $binary      = $filesystem->read(location: $path);
            $orientation = $this->extractExifOrientation(binary: $binary);
            $image       = $this->decodeImage(binary: $binary);
            $image       = $this->autoRotateImage(image: $image, orientation: $orientation);
            $watermark   = $this->createWatermark(width: imagesx($image), height: imagesy($image), media_file: $media_file);
            $image       = $this->addWatermark(image: $image, watermark: $watermark);
            $filename    = $download ? basename(path: $path) : '';
            $data        = $this->encodeImage(image: $image, mime_type: $mime_type, quality: self::IMAGE_QUALITY);

            return $this->imageResponse(data: $data, mime_type: $mime_type, filename: $filename);
        } catch (RuntimeException $ex) {
            return $this->replacementImageResponse(text: pathinfo(path: $path, flags: PATHINFO_EXTENSION))
                ->withHeader('x-image-exception', $ex->getMessage());
        } catch (FilesystemException $ex) {
            return $this->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_NOT_FOUND)
                ->withHeader('x-image-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        } catch (Throwable $ex) {
            return $this->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('x-image-exception', $ex->getMessage());
        }
    }

    public function mediaFileThumbnail(
        MediaFile $media_file,
        int $width,
        int $height,
        ImageOperation $operation,
        bool $add_watermark,
    ): string {
        // Where are the images stored?
        $filesystem = $media_file->media()->tree()->mediaFilesystem();

        // Where is the image stored in the filesystem?
        $path = $media_file->filename();

        $key = implode(separator: ':', array: [
            $media_file->media()->tree()->name(),
            $path,
            $filesystem->lastModified(path: $path),
            (string) $width,
            (string) $height,
            $operation->value,
            (string) $add_watermark,
        ]);

        $closure = function () use ($filesystem, $path, $width, $height, $operation, $add_watermark, $media_file): string {
            $mime_type   = $media_file->mimeType();
            $binary      = $filesystem->read(location: $path);
            $orientation = $this->extractExifOrientation(binary: $binary);
            $image       = $this->decodeImage(binary: $binary);
            $image       = $this->autoRotateImage(image: $image, orientation: $orientation);
            $image       = $this->resizeImage(image: $image, width: $width, height: $height, operation: $operation);

            if ($add_watermark) {
                $watermark = $this->createWatermark(width: imagesx($image), height: imagesy($image), media_file: $media_file);
                $image     = $this->addWatermark(image: $image, watermark: $watermark);
            }

            return $this->encodeImage(image: $image, mime_type: $mime_type, quality: self::THUMBNAIL_QUALITY);
        };

        return Registry::cache()->file()->remember(key: $key, closure: $closure, ttl: static::THUMBNAIL_CACHE_TTL);
    }

    public function mediaFileThumbnailResponse(
        MediaFile $media_file,
        int $width,
        int $height,
        ImageOperation $operation,
        bool $add_watermark,
    ): ResponseInterface {
        // Where are the images stored?
        $filesystem = $media_file->media()->tree()->mediaFilesystem();

        // Where is the image stored in the filesystem?
        $path = $media_file->filename();

        try {
            $mime_type = $filesystem->mimeType(path: $path);

            $data = $this->mediaFileThumbnail($media_file, $width, $height, $operation, $add_watermark);

            return $this->imageResponse(data: $data, mime_type: $mime_type, filename: '');
        } catch (FilesystemException $ex) {
            return $this
                ->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_NOT_FOUND)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        } catch (Throwable $ex) {
            return $this
                ->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        }
    }

    public function fileNeedsWatermark(MediaFile $media_file, UserInterface $user): bool
    {
        $tree = $media_file->media()->tree();

        return Auth::accessLevel(tree: $tree, user: $user) > (int) $tree->getPreference(setting_name: 'SHOW_NO_WATERMARK');
    }

    public function thumbnailNeedsWatermark(MediaFile $media_file, UserInterface $user): bool
    {
        return $this->fileNeedsWatermark(media_file: $media_file, user: $user);
    }

    public function createWatermark(int $width, int $height, MediaFile $media_file): GdImage
    {
        $this->requireGdExtension();

        $watermark_path = Webtrees::ROOT_DIR . static::WATERMARK_FILE;
        $watermark_data = file_get_contents($watermark_path);

        if (!is_string($watermark_data)) {
            throw new RuntimeException(message: 'Unable to read watermark image: ' . $watermark_path);
        }

        $watermark = $this->decodeImage(binary: $watermark_data);

        return $this->resizeImage(image: $watermark, width: $width, height: $height, operation: ImageOperation::Contain);
    }

    public function addWatermark(GdImage $image, GdImage $watermark): GdImage
    {
        $watermark_width  = imagesx($watermark);
        $watermark_height = imagesy($watermark);
        $image_width      = imagesx($image);
        $image_height     = imagesy($image);
        $position_x       = (int) round(($image_width - $watermark_width) / 2);
        $position_y       = (int) round(($image_height - $watermark_height) / 2);

        imagealphablending($image, true);
        imagesavealpha($image, true);
        imagecopy($image, $watermark, $position_x, $position_y, 0, 0, $watermark_width, $watermark_height);

        return $image;
    }

    public function replacementImageResponse(string $text): ResponseInterface
    {
        // We can't create a PNG/BMP/JPEG image when the GD extension is unavailable.
        $svg = view(name: 'errors/image-svg', data: ['status' => $text]);

        // We can't send the actual status code, as browsers won't show images with 4xx/5xx.
        return response(content: $svg)
            ->withHeader('content-type', 'image/svg+xml')
            ->withHeader('content-security-policy', 'default-src none');
    }

    private function imageResponse(string $data, string $mime_type, string $filename): ResponseInterface
    {
        if ($mime_type === 'image/svg+xml') {
            if (!$this->php_service->extensionLoaded(extension: 'dom')) {
                return $this->replacementImageResponse(text: 'DOM')
                    ->withHeader('x-image-exception', 'Need the PHP dom extension to verify SVG files.');
            }

            if ($this->svgContainsActiveContent(data: $data)) {
                return $this->replacementImageResponse(text: 'XSS')
                    ->withHeader('x-image-exception', 'SVG image blocked due to XSS.');
            }
        }

        // HTML files may contain JavaScript and iframes, so use content-security-policy to disable them.
        $response = response($data)
            ->withHeader('content-type', $mime_type)
            ->withHeader('content-security-policy', 'default-src none');

        if ($filename === '') {
            return $response;
        }

        $filename = addcslashes(string: basename(path: $filename), characters: '"');

        return $response->withHeader('content-disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Determine whether an SVG document contains active content (script elements,
     * event-handler attributes, javascript: URLs) that could execute in a browser.
     * Although we have a content-security-policy to disable scripts, a user may
     * download this file and distribute it, so better to block it.
     */
    private function svgContainsActiveContent(string $data): bool
    {
        $previous_error_state = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument();
            // LIBXML_NONET disables network access so external DTDs and entities
            // cannot be fetched — mitigates XXE/SSRF on malformed payloads.
            $loaded = $document->loadXML($data, LIBXML_NONET);

            if ($loaded === false || !$document->documentElement instanceof DOMElement) {
                // Malformed SVG — treat conservatively and block.
                return true;
            }

            return $this->svgElementIsDangerous($document->documentElement);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous_error_state);
        }
    }

    private function svgElementIsDangerous(DOMElement $element): bool
    {
        if (in_array(strtolower($element->localName), self::DANGEROUS_SVG_TAGS, true)) {
            return true;
        }

        foreach ($element->attributes as $attribute) {
            $name = strtolower($attribute->nodeName);

            // Event-handler attributes such as "onload"
            if (str_starts_with($name, 'on')) {
                return true;
            }

            // Normalize whitespace to catch malformed values that browsers might accept,
            // such as "java\tscript:".
            $value         = (string) $attribute->nodeValue;
            $value_compact = preg_replace('/\s+/', '', $value) ?? '';

            if (str_starts_with(strtolower($value_compact), 'javascript:')) {
                return true;
            }
        }

        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMElement && $this->svgElementIsDangerous(element: $child)) {
                return true;
            }
        }

        return false;
    }

    private function requireGdExtension(): void
    {
        if (!$this->php_service->extensionLoaded(extension: 'gd')) {
            throw new RuntimeException(message: 'The PHP GD extension is not installed.');
        }
    }

    private function decodeImage(string $binary): GdImage
    {
        $this->requireGdExtension();

        $image = imagecreatefromstring($binary);

        if (!$image instanceof GdImage) {
            throw new RuntimeException(message: 'Unable to decode image data');
        }

        return $image;
    }

    private function autoRotateImage(GdImage $image, ExifOrientation $orientation): GdImage
    {
        return match ($orientation) {
            ExifOrientation::MirrorHorizontal         => $this->flipImage(image: $image, mode: IMG_FLIP_HORIZONTAL),
            ExifOrientation::Rotate180                => $this->rotateImage(image: $image, angle: 180),
            ExifOrientation::MirrorVertical           => $this->flipImage(image: $image, mode: IMG_FLIP_VERTICAL),
            ExifOrientation::Transpose                => $this->flipImage(image: $this->rotateImage(image: $image, angle: -90), mode: IMG_FLIP_HORIZONTAL),
            ExifOrientation::Rotate90Clockwise        => $this->rotateImage(image: $image, angle: -90),
            ExifOrientation::Transverse               => $this->flipImage(image: $this->rotateImage(image: $image, angle: 90), mode: IMG_FLIP_HORIZONTAL),
            ExifOrientation::Rotate90CounterClockwise => $this->rotateImage(image: $image, angle: 90),
            ExifOrientation::Normal                   => $image,
        };
    }

    private function extractExifOrientation(string $binary): ExifOrientation
    {

        if (!$this->php_service->functionExists(function: 'exif_read_data')) {
            return ExifOrientation::Normal;
        }

        $stream = fopen('php://temp', 'r+b');

        if ($stream === false) {
            throw new RuntimeException(message: 'Unable to allocate temporary stream for EXIF metadata.');
        }

        fwrite($stream, $binary);
        rewind($stream);

        $metadata = exif_read_data($stream, 'IFD0');
        fclose($stream);

        if (!is_array($metadata)) {
            return ExifOrientation::Normal;
        }

        $orientation = (int) ($metadata['Orientation'] ?? ExifOrientation::Normal->value);

        return ExifOrientation::tryFrom($orientation) ?? ExifOrientation::Normal;
    }

    private function rotateImage(GdImage $image, int $angle): GdImage
    {
        $rotated = imagerotate($image, $angle, 0);

        if (!$rotated instanceof GdImage) {
            throw new RuntimeException(message: 'Unable to rotate image.');
        }

        return $rotated;
    }

    private function flipImage(GdImage $image, int $mode): GdImage
    {
        if (!imageflip($image, $mode)) {
            throw new RuntimeException(message: 'Unable to flip image.');
        }

        return $image;
    }

    private function encodeImage(GdImage $image, string $mime_type, int $quality): string
    {
        $this->requireGdExtension();

        ob_start();

        $written = match ($mime_type) {
            'image/jpeg' => imagejpeg($image, null, $quality),
            'image/png'  => imagepng($image, null, (int) round((100 - $quality) * 9 / 100)),
            'image/gif'  => imagegif($image),
            'image/bmp'  => imagebmp($image),
            'image/webp' => $this->php_service->functionExists(function: 'imagewebp') ? imagewebp($image, null, $quality) : false,
            default      => false,
        };

        if ($written !== true) {
            ob_end_clean();
            throw new RuntimeException(message: 'Unable to encode image data for MIME type: ' . $mime_type);
        }

        $data = ob_get_clean();

        if (!is_string($data)) {
            throw new RuntimeException(message: 'Unable to capture encoded image data.');
        }

        return $data;
    }

    private function resizeImage(GdImage $image, int $width, int $height, ImageOperation $operation): GdImage
    {
        return match ($operation) {
            ImageOperation::Crop    => $this->cropToFit(image: $image, width: $width, height: $height),
            ImageOperation::Contain => $this->scaleToFit(image: $image, width: $width, height: $height),
        };
    }


    private function cropToFit(GdImage $image, int $width, int $height): GdImage
    {
        $source_width  = imagesx($image);
        $source_height = imagesy($image);
        $target_ratio  = $width / $height;
        $source_ratio  = $source_width / $source_height;

        if ($source_ratio > $target_ratio) {
            $crop_height = $source_height;
            $crop_width  = (int) round($crop_height * $target_ratio);
            $source_x    = (int) round(($source_width - $crop_width) / 2);
            $source_y    = 0;
        } else {
            $crop_width  = $source_width;
            $crop_height = (int) round($crop_width / $target_ratio);
            $source_x    = 0;
            $source_y    = (int) round(($source_height - $crop_height) / 2);
        }

        $target = $this->createCanvas(width: $width, height: $height);

        imagecopyresampled($target, $image, 0, 0, $source_x, $source_y, $width, $height, $crop_width, $crop_height);

        return $target;
    }

    private function scaleToFit(GdImage $image, int $width, int $height): GdImage
    {
        $source_width  = imagesx($image);
        $source_height = imagesy($image);
        $scale         = min($width / $source_width, $height / $source_height);
        $target_width  = max(1, (int) round($source_width * $scale));
        $target_height = max(1, (int) round($source_height * $scale));
        $target        = $this->createCanvas(width: $target_width, height: $target_height);

        imagecopyresampled($target, $image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);

        return $target;
    }

    private function createCanvas(int $width, int $height): GdImage
    {
        $target = imagecreatetruecolor($width, $height);

        if (!$target instanceof GdImage) {
            throw new RuntimeException(message: 'Unable to allocate image canvas.');
        }

        imagealphablending($target, false);
        imagesavealpha($target, true);

        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $width, $height, $transparent);

        return $target;
    }
}
