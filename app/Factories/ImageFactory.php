<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\ImageFactoryInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Webtrees;
use Imagick;
use Intervention\Gif\Exceptions\NotReadableException;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use InvalidArgumentException;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

use function addcslashes;
use function basename;
use function extension_loaded;
use function get_class;
use function implode;
use function pathinfo;
use function response;
use function str_contains;
use function view;

use const PATHINFO_EXTENSION;

/**
 * Make an image (from another image).
 */
class ImageFactory implements ImageFactoryInterface
{
    // Imagick can detect the quality setting for images.  GD cannot.
    protected const GD_DEFAULT_IMAGE_QUALITY     = 90;
    protected const GD_DEFAULT_THUMBNAIL_QUALITY = 70;

    protected const WATERMARK_FILE = 'resources/img/watermark.png';

    protected const THUMBNAIL_CACHE_TTL = 8640000;

    public const SUPPORTED_FORMATS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/tiff' => 'tif',
        'image/bmp'  => 'bmp',
        'image/webp' => 'webp',
    ];

    /**
     * Send the original file - either inline or as a download.
     */
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
        } catch (UnableToReadFile | FilesystemException $ex) {
            return $this->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_NOT_FOUND)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        }
    }

    /**
     * Send a thumbnail.
     */
    public function thumbnailResponse(
        FilesystemOperator $filesystem,
        string $path,
        int $width,
        int $height,
        string $fit
    ): ResponseInterface {
        try {
            $mime_type = $filesystem->mimeType(path: $path);
            $image     = $this->imageManager()->read(input: $filesystem->readStream($path));
            $image     = $this->resizeImage(image: $image, width: $width, height: $height, fit: $fit);
            $quality   = $this->extractImageQuality(image: $image, default: static::GD_DEFAULT_THUMBNAIL_QUALITY);
            $data      = $image->encodeByMediaType(type: $mime_type, quality: $quality)->toString();

            return $this->imageResponse(data: $data, mime_type: $mime_type, filename: '');
        } catch (FilesystemException | UnableToReadFile $ex) {
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

    /**
     * Create a full-size version of an image.
     */
    public function mediaFileResponse(MediaFile $media_file, bool $add_watermark, bool $download): ResponseInterface
    {
        $filesystem = $media_file->media()->tree()->mediaFilesystem();
        $path       = $media_file->filename();

        if (!$add_watermark || !$media_file->isImage()) {
            return $this->fileResponse(filesystem: $filesystem, path: $path, download: $download);
        }

        try {
            $mime_type = $media_file->mimeType();
            $image     = $this->imageManager()->read(input: $filesystem->readStream($path));
            $watermark = $this->createWatermark(width: $image->width(), height: $image->height(), media_file: $media_file);
            $image     = $this->addWatermark(image: $image, watermark: $watermark);
            $filename  = $download ? basename(path: $path) : '';
            $quality   = $this->extractImageQuality(image: $image, default: static::GD_DEFAULT_IMAGE_QUALITY);
            $data      = $image->encodeByMediaType(type: $mime_type, quality:  $quality)->toString();

            return $this->imageResponse(data: $data, mime_type: $mime_type, filename: $filename);
        } catch (NotReadableException $ex) {
            return $this->replacementImageResponse(text: pathinfo(path: $path, flags: PATHINFO_EXTENSION))
                ->withHeader('x-image-exception', $ex->getMessage());
        } catch (FilesystemException | UnableToReadFile $ex) {
            return $this->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_NOT_FOUND)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        } catch (Throwable $ex) {
            return $this->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('x-image-exception', $ex->getMessage());
        }
    }

    /**
     * Create a smaller version of an image.
     */
    public function mediaFileThumbnailResponse(
        MediaFile $media_file,
        int $width,
        int $height,
        string $fit,
        bool $add_watermark
    ): ResponseInterface {
        // Where are the images stored.
        $filesystem = $media_file->media()->tree()->mediaFilesystem();

        // Where is the image stored in the filesystem.
        $path = $media_file->filename();

        try {
            $mime_type = $filesystem->mimeType(path: $path);

            $key = implode(separator: ':', array: [
                $media_file->media()->tree()->name(),
                $path,
                $filesystem->lastModified(path: $path),
                (string) $width,
                (string) $height,
                $fit,
                (string) $add_watermark,
            ]);

            $closure = function () use ($filesystem, $path, $width, $height, $fit, $add_watermark, $media_file): string {
                $image = $this->imageManager()->read(input: $filesystem->readStream($path));
                $image = $this->resizeImage(image: $image, width: $width, height: $height, fit: $fit);

                if ($add_watermark) {
                    $watermark = $this->createWatermark(width: $image->width(), height: $image->height(), media_file: $media_file);
                    $image     = $this->addWatermark(image: $image, watermark: $watermark);
                }

                $quality = $this->extractImageQuality(image: $image, default:  static::GD_DEFAULT_THUMBNAIL_QUALITY);

                return $image->encodeByMediaType(type: $media_file->mimeType(), quality: $quality)->toString();
            };

            // Images and Responses both contain resources - which cannot be serialized.
            // So cache the raw image data.
            $data = Registry::cache()->file()->remember(key: $key, closure: $closure, ttl: static::THUMBNAIL_CACHE_TTL);

            return $this->imageResponse(data: $data, mime_type:  $mime_type, filename:  '');
        } catch (NotReadableException $ex) {
            return $this
                ->replacementImageResponse(text: '.' . pathinfo(path: $path, flags:  PATHINFO_EXTENSION))
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        } catch (FilesystemException | UnableToReadFile $ex) {
            return $this
                ->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_NOT_FOUND)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        } catch (Throwable $ex) {
            return $this
                ->replacementImageResponse(text: (string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('x-thumbnail-exception', get_class(object: $ex) . ': ' . $ex->getMessage());
        }
    }

    /**
     * Does a full-sized image need a watermark?
     */
    public function fileNeedsWatermark(MediaFile $media_file, UserInterface $user): bool
    {
        $tree = $media_file->media()->tree();

        return Auth::accessLevel(tree: $tree, user: $user) > (int) $tree->getPreference(setting_name: 'SHOW_NO_WATERMARK');
    }

    /**
     * Does a thumbnail image need a watermark?
     */
    public function thumbnailNeedsWatermark(MediaFile $media_file, UserInterface $user): bool
    {
        return $this->fileNeedsWatermark(media_file: $media_file, user:  $user);
    }

    /**
     * Create a watermark image, perhaps specific to a media-file.
     */
    public function createWatermark(int $width, int $height, MediaFile $media_file): ImageInterface
    {
        return $this->imageManager()
            ->read(input: Webtrees::ROOT_DIR . static::WATERMARK_FILE)
            ->contain(width: $width, height: $height);
    }

    /**
     * Add a watermark to an image.
     */
    public function addWatermark(ImageInterface $image, ImageInterface $watermark): ImageInterface
    {
        return $image->place(element: $watermark, position:  'center');
    }

    /**
     * Send a replacement image, to replace one that could not be found or created.
     */
    public function replacementImageResponse(string $text): ResponseInterface
    {
        // We can't create a PNG/BMP/JPEG image, as the GD/IMAGICK libraries may be missing.
        $svg = view(name: 'errors/image-svg', data: ['status' => $text]);

        // We can't send the actual status code, as browsers won't show images with 4xx/5xx.
        return response(content: $svg, code: StatusCodeInterface::STATUS_OK, headers: [
            'content-type' => 'image/svg+xml',
        ]);
    }

    /**
     * Create a response from image data.
     */
    protected function imageResponse(string $data, string $mime_type, string $filename): ResponseInterface
    {
        if ($mime_type === 'image/svg+xml' && str_contains(haystack: $data, needle: '<script')) {
            return $this->replacementImageResponse(text: 'XSS')
                ->withHeader('x-image-exception', 'SVG image blocked due to XSS.');
        }

        // HTML files may contain javascript and iframes, so use content-security-policy to disable them.
        $response = response($data)
            ->withHeader('content-type', $mime_type)
            ->withHeader('content-security-policy', 'script-src none;frame-src none');

        if ($filename === '') {
            return $response;
        }

        return $response
            ->withHeader('content-disposition', 'attachment; filename="' . addcslashes(string: basename(path: $filename), characters: '"'));
    }

    /**
     * Choose an image library, based on what is installed.
     */
    protected function imageManager(): ImageManager
    {
        if (extension_loaded(extension: 'imagick')) {
            return new ImageManager(driver: new ImagickDriver());
        }

        if (extension_loaded(extension: 'gd')) {
            return new ImageManager(driver: new GdDriver());
        }

        throw new RuntimeException(message: 'No PHP graphics library is installed.  Need Imagick or GD');
    }

    /**
     * Resize an image.
     */
    protected function resizeImage(ImageInterface $image, int $width, int $height, string $fit): ImageInterface
    {
        return match ($fit) {
            'crop'    => $image->cover(width: $width, height: $height),
            'contain' => $image->scale(width: $width, height: $height),
            default   => throw new InvalidArgumentException(message: 'Unknown fit type: ' . $fit),
        };
    }

    /**
     * Extract the quality/compression parameter from an image.
     */
    protected function extractImageQuality(ImageInterface $image, int $default): int
    {
        $native = $image->core()->native();

        if ($native instanceof Imagick) {
            return $native->getImageCompressionQuality();
        }

        return $default;
    }
}
