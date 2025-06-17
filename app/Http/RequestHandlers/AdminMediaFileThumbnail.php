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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Create a thumbnail of a file, for use on the admin page.
 */
class AdminMediaFileThumbnail implements RequestHandlerInterface
{
    private MediaFileService $media_file_service;

    /**
     * @param MediaFileService $media_file_service
     */
    public function __construct(MediaFileService $media_file_service)
    {
        $this->media_file_service = $media_file_service;
    }

    /**
     * Show an image/thumbnail, with/without a watermark.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $filesystem = Registry::filesystem()->data();
        $path       = Validator::queryParams($request)->string('path');

        $media_folders = $this->media_file_service->allMediaFolders($filesystem)->all();

        foreach ($media_folders as $media_folder) {
            if (str_starts_with($path, $media_folder)) {
                return Registry::imageFactory()->thumbnailResponse($filesystem, $path, 120, 120, 'contain');
            }
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “path” is invalid.'));
    }
}
