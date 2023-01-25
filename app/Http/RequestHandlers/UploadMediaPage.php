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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function ini_get;

/**
 * Manage media from the control panel.
 */
class UploadMediaPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // How many files to upload on one form.
    private const MAX_UPLOAD_FILES = 10;

    private MediaFileService $media_file_service;

    /**
     * MediaController constructor.
     *
     * @param MediaFileService $media_file_service
     */
    public function __construct(MediaFileService $media_file_service)
    {
        $this->media_file_service = $media_file_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $data_filesystem = Registry::filesystem()->data();

        $media_folders = $this->media_file_service->allMediaFolders($data_filesystem);

        $filesize = ini_get('upload_max_filesize') ?: '2M';

        $title = I18N::translate('Upload media files');

        return $this->viewResponse('admin/media-upload', [
            'max_upload_files' => self::MAX_UPLOAD_FILES,
            'filesize'         => $filesize,
            'media_folders'    => $media_folders,
            'title'            => $title,
        ]);
    }
}
