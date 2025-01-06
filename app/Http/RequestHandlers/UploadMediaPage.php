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
use Fisharebest\Webtrees\Services\PhpService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function intdiv;

/**
 * Manage media from the control panel.
 */
class UploadMediaPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // How many files to upload on one form.
    private const int MAX_UPLOAD_FILES = 10;

    public function __construct(private MediaFileService $media_file_service, private PhpService $php_service)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $data_filesystem = Registry::filesystem()->data();
        $media_folders   = $this->media_file_service->allMediaFolders($data_filesystem);
        $kb              = intdiv(num1: $this->php_service->uploadMaxFilesize() + 1023, num2: 1024);
        $filesize        = I18N::translate('%s KB', I18N::number($kb));
        $title           = I18N::translate('Upload media files');

        return $this->viewResponse('admin/media-upload', [
            'max_upload_files' => self::MAX_UPLOAD_FILES,
            'filesize'         => $filesize,
            'media_folders'    => $media_folders,
            'title'            => $title,
        ]);
    }
}
