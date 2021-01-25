<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Webtrees;
use Imagick;
use Intervention\Image\Constraint;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

use function addcslashes;
use function basename;
use function extension_loaded;
use function pathinfo;
use function response;
use function strlen;
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

    protected const INTERVENTION_DRIVERS = ['imagick', 'gd'];

    protected const INTERVENTION_FORMATS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/tiff' => 'tif',
        'image/bmp'  => 'bmp',
        'image/webp' => 'webp',
    ];

    /**
     * Send the original file - either inline or as a download.
     *
     * @param FilesystemInterface $filesystem
     * @param string              $path
     * @param bool                $download
     *
     * @return ResponseInterface
     */
    public function fileResponse(FilesystemInterface $filesystem, string $path, bool $download): ResponseInterface
    {
        try {
            $data = $filesystem->read($path);

            $headers = [
                'Content-Type'   => $filesystem->getMimetype($path),
                'Content-Length' => (string) strlen($data),
            ];

            if ($download) {
                $headers['Content-Disposition'] = 'attachment; filename="' . addcslashes(basename($path), '"');
            }

            return response($data, StatusCodeInterface::STATUS_OK, $headers);
        } catch (FileNotFoundException $ex) {
            return $this->replacementImageResponse((string) StatusCodeInterface::STATUS_NOT_FOUND);
        }
    }

    /**
     * Send a thumbnail.
     *
     * @param FilesystemInterface $filesystem
     * @param string              $path
     * @param int                 $width
     * @param int                 $height
     * @param string              $fit
     *
     *
     * @return ResponseInterface
     */
    public function thumbnailResponse(
        FilesystemInterface $filesystem,
        string $path,
        int $width,
        int $height,
        string $fit
    ): ResponseInterface {
        try {
            $image = $this->imageManager()->make($filesystem->readStream($path));
            $image = $this->autorotateImage($image);
            $image = $this->resizeImage($image, $width, $height, $fit);

            $format  = static::INTERVENTION_FORMATS[$image->mime()] ?? 'jpg';
            $quality = $this->extractImageQuality($image, static::GD_DEFAULT_THUMBNAIL_QUALITY);
            $data    = (string) $image->encode($format, $quality);

            return $this->imageResponse($data, $image->mime(), '');
        } catch (NotReadableException $ex) {
            return $this->replacementImageResponse('.' . pathinfo($path, PATHINFO_EXTENSION));
        } catch (FileNotFoundException $ex) {
            return $this->replacementImageResponse((string) StatusCodeInterface::STATUS_NOT_FOUND);
        } catch (Throwable $ex) {
            return $this->replacementImageResponse((string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('X-Thumbnail-Exception', $ex->getMessage());
        }
    }

    /**
     * Create a full-size version of an image.
     *
     * @param MediaFile $media_file
     * @param bool      $add_watermark
     * @param bool      $download
     *
     * @return ResponseInterface
     */
    public function mediaFileResponse(MediaFile $media_file, bool $add_watermark, bool $download): ResponseInterface
    {
        $filesystem = Registry::filesystem()->media($media_file->media()->tree());
        $filename   = $media_file->filename();

        if (!$add_watermark || !$media_file->isImage()) {
            return $this->fileResponse($filesystem, $filename, $download);
        }

        try {
            $image = $this->imageManager()->make($filesystem->readStream($filename));
            $image = $this->autorotateImage($image);

            $watermark_image = $this->createWatermark($image->width(), $image->height(), $media_file);

            $image = $this->addWatermark($image, $watermark_image);

            $download_filename = $download ? basename($filename) : '';

            $format  = static::INTERVENTION_FORMATS[$image->mime()] ?? 'jpg';
            $quality = $this->extractImageQuality($image, static::GD_DEFAULT_IMAGE_QUALITY);
            $data    = (string) $image->encode($format, $quality);

            return $this->imageResponse($data, $image->mime(), $download_filename);
        } catch (NotReadableException $ex) {
            return $this->replacementImageResponse(pathinfo($filename, PATHINFO_EXTENSION))
                ->withHeader('X-Image-Exception', $ex->getMessage());
        } catch (FileNotFoundException $ex) {
            return $this->replacementImageResponse((string) StatusCodeInterface::STATUS_NOT_FOUND);
        } catch (Throwable $ex) {
            return $this->replacementImageResponse((string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('X-Image-Exception', $ex->getMessage());
        }
    }

    /**
     * Create a smaller version of an image.
     *
     * @param MediaFile $media_file
     * @param int       $width
     * @param int       $height
     * @param string    $fit
     * @param bool      $add_watermark
     *
     * @return ResponseInterface
     */
    public function mediaFileThumbnailResponse(
        MediaFile $media_file,
        int $width,
        int $height,
        string $fit,
        bool $add_watermark
    ): ResponseInterface {
        // Where are the images stored.
        $filesystem = Registry::filesystem()->media($media_file->media()->tree());

        // Where is the image stored in the filesystem.
        $path = $media_file->filename();

        try {
            $mime_type = $filesystem->getMimetype($path);

            $key = implode(':', [
                $media_file->media()->tree()->name(),
                $path,
                $filesystem->getTimestamp($path),
                (string) $width,
                (string) $height,
                $fit,
                (string) $add_watermark,
            ]);

            $closure = function () use ($filesystem, $path, $width, $height, $fit, $add_watermark, $media_file): string {
                $image = $this->imageManager()->make($filesystem->readStream($path));
                $image = $this->autorotateImage($image);
                $image = $this->resizeImage($image, $width, $height, $fit);

                if ($add_watermark) {
                    $watermark = $this->createWatermark($image->width(), $image->height(), $media_file);
                    $image     = $this->addWatermark($image, $watermark);
                }

                $format  = static::INTERVENTION_FORMATS[$image->mime()] ?? 'jpg';
                $quality = $this->extractImageQuality($image, static::GD_DEFAULT_THUMBNAIL_QUALITY);

                return (string) $image->encode($format, $quality);
            };

            // Images and Responses both contain resources - which cannot be serialized.
            // So cache the raw image data.
            $data = Registry::cache()->file()->remember($key, $closure, static::THUMBNAIL_CACHE_TTL);

            return $this->imageResponse($data, $mime_type, '');
        } catch (NotReadableException $ex) {
            return $this->replacementImageResponse('.' . pathinfo($path, PATHINFO_EXTENSION));
        } catch (FileNotFoundException $ex) {
            return $this->replacementImageResponse((string) StatusCodeInterface::STATUS_NOT_FOUND);
        } catch (Throwable $ex) {
            return $this->replacementImageResponse((string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('X-Thumbnail-Exception', $ex->getMessage());
        }
    }

    /**
     * Does a full-sized image need a watermark?
     *
     * @param MediaFile     $media_file
     * @param UserInterface $user
     *
     * @return bool
     */
    public function fileNeedsWatermark(MediaFile $media_file, UserInterface $user): bool
    {
        $tree = $media_file->media()->tree();

        return Auth::accessLevel($tree, $user) > $tree->getPreference('SHOW_NO_WATERMARK');
    }

    /**
     * Does a thumbnail image need a watermark?
     *
     * @param MediaFile     $media_file
     * @param UserInterface $user
     *
     * @return bool
     */
    public function thumbnailNeedsWatermark(MediaFile $media_file, UserInterface $user): bool
    {
        return $this->fileNeedsWatermark($media_file, $user);
    }

    /**
     * Create a watermark image, perhaps specific to a media-file.
     *
     * @param int       $width
     * @param int       $height
     * @param MediaFile $media_file
     *
     * @return Image
     */
    public function createWatermark(int $width, int $height, MediaFile $media_file): Image
    {
        return $this->imageManager()
            ->make(Webtrees::ROOT_DIR . static::WATERMARK_FILE)
            ->resize($width, $height, static function (Constraint $constraint) {
                $constraint->aspectRatio();
            });
    }

    /**
     * Add a watermark to an image.
     *
     * @param Image $image
     * @param Image $watermark
     *
     * @return Image
     */
    public function addWatermark(Image $image, Image $watermark): Image
    {
        return $image->insert($watermark, 'center');
    }

    /**
     * Send a replacement image, to replace one that could not be found or created.
     *
     * @param string $text HTTP status code or file extension
     *
     * @return ResponseInterface
     */
    public function replacementImageResponse(string $text): ResponseInterface
    {
        // We can't create a PNG/BMP/JPEG image, as the GD/IMAGICK libraries may be missing.
        $svg = view('errors/image-svg', ['status' => $text]);

        // We can't send the actual status code, as browsers won't show images with 4xx/5xx.
        return response($svg, StatusCodeInterface::STATUS_OK, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }

    /**
     * @param string $data
     * @param string $mime_type
     * @param string $filename
     *
     * @return ResponseInterface
     */
    protected function imageResponse(string $data, string $mime_type, string $filename): ResponseInterface
    {
        $headers = [
            'Content-Type'   => $mime_type,
            'Content-Length' => (string) strlen($data),
        ];

        if ($filename !== '') {
            $headers['Content-Disposition'] = 'attachment; filename="' . addcslashes(basename($filename), '"');
        }

        return response($data, StatusCodeInterface::STATUS_OK, $headers);
    }

    /**
     * @return ImageManager
     * @throws RuntimeException
     */
    protected function imageManager(): ImageManager
    {
        foreach (static::INTERVENTION_DRIVERS as $driver) {
            if (extension_loaded($driver)) {
                return new ImageManager(['driver' => $driver]);
            }
        }

        throw new RuntimeException('No PHP graphics library is installed.  Need Imagick or GD');
    }

    /**
     * Apply EXIF rotation to an image.
     *
     * @param Image $image
     *
     * @return Image
     */
    protected function autorotateImage(Image $image): Image
    {
        try {
            // Auto-rotate using EXIF information.
            return $image->orientate();
        } catch (NotSupportedException $ex) {
            // If we can't auto-rotate the image, then don't.
            return $image;
        }
    }

    /**
     * Resize an image.
     *
     * @param Image  $image
     * @param int    $width
     * @param int    $height
     * @param string $fit
     *
     * @return Image
     */
    protected function resizeImage(Image $image, int $width, int $height, string $fit): Image
    {
        switch ($fit) {
            case 'crop':
                return $image->fit($width, $height);
            case 'contain':
                return $image->resize($width, $height, static function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
        }

        return $image;
    }

    /**
     * Extract the quality/compression parameter from an image.
     *
     * @param Image $image
     * @param int   $default
     *
     * @return int
     */
    protected function extractImageQuality(Image $image, int $default): int
    {
        $core = $image->getCore();

        if ($core instanceof Imagick) {
            return $core->getImageCompressionQuality() ?: $default;
        }

        return $default;
    }
}
