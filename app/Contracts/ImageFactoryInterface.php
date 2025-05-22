<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Intervention\Image\Interfaces\ImageInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Http\Message\ResponseInterface;

/**
 * Make an image (from another image).
 */
interface ImageFactoryInterface
{
    /**
     * Send the original file - either inline or as a download.
     */
    public function fileResponse(FilesystemOperator $filesystem, string $path, bool $download): ResponseInterface;

    /**
     * Send the original file - either inline or as a download.
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
     */
    public function mediaFileResponse(MediaFile $media_file, bool $add_watermark, bool $download): ResponseInterface;

    /**
     * Create a smaller version of an image.
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
     */
    public function fileNeedsWatermark(MediaFile $media_file, UserInterface $user): bool;

    /**
     * Does a thumbnail image need a watermark?
     */
    public function thumbnailNeedsWatermark(MediaFile $media_file, UserInterface $user): bool;

    /**
     * Create a watermark image, perhaps specific to a media-file.
     */
    public function createWatermark(int $width, int $height, MediaFile $media_file): ImageInterface;

    /**
     * Add a watermark to an image.
     */
    public function addWatermark(ImageInterface $image, ImageInterface $watermark): ImageInterface;

    /**
     * Send a replacement image, to replace one that could not be found or created.
     */
    public function replacementImageResponse(string $text): ResponseInterface;
}
