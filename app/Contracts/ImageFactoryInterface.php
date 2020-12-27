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

namespace Fisharebest\Webtrees\Contracts;

use Fisharebest\Webtrees\MediaFile;
use Intervention\Image\Image;
use League\Flysystem\FilesystemOperator;
use Psr\Http\Message\ResponseInterface;

/**
 * Make an image (from another image).
 */
interface ImageFactoryInterface
{
    /**
     * Send the original file - either inline or as a download.
     *
     * @param FilesystemOperator $filesystem
     * @param string             $path
     * @param bool               $download
     *
     * @return ResponseInterface
     */
    public function fileResponse(FilesystemOperator $filesystem, string $path, bool $download): ResponseInterface;

    /**
     * Send the original file - either inline or as a download.
     *
     * @param FilesystemOperator $filesystem
     * @param string             $path
     * @param int                $width
     * @param int                $height
     * @param string             $fit
     *
     * @return ResponseInterface
     */
    public function thumbnailResponse(
        FilesystemOperator $filesystem,
        string $path,
        int $width,
        int $height,
        string $fit
    ): ResponseInterface;

    /**
     * Create a full-size version of an image.
     *
     * @param MediaFile $media_file
     * @param bool      $add_watermark
     * @param bool      $download
     *
     * @return ResponseInterface
     */
    public function mediaFileResponse(MediaFile $media_file, bool $add_watermark, bool $download): ResponseInterface;

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
    ): ResponseInterface;

    /**
     * Does a full-sized image need a watermark?
     *
     * @param MediaFile     $media_file
     * @param UserInterface $user
     *
     * @return bool
     */
    public function fileNeedsWatermark(MediaFile $media_file, UserInterface $user): bool;

    /**
     * Does a thumbnail image need a watermark?
     *
     * @param MediaFile     $media_file
     * @param UserInterface $user
     *
     * @return bool
     */
    public function thumbnailNeedsWatermark(MediaFile $media_file, UserInterface $user): bool;

    /**
     * Create a watermark image, perhaps specific to a media-file.
     *
     * @param int       $width
     * @param int       $height
     * @param MediaFile $media_file
     *
     * @return Image
     */
    public function createWatermark(int $width, int $height, MediaFile $media_file): Image;

    /**
     * Add a watermark to an image.
     *
     * @param Image $image
     * @param Image $watermark
     *
     * @return Image
     */
    public function addWatermark(Image $image, Image $watermark): Image;

    /**
     * Send a replacement image, to replace one that could not be found or created.
     *
     * @param string $text HTTP status code or file extension
     *
     * @return ResponseInterface
     */
    public function replacementImageResponse(string $text): ResponseInterface;
}
