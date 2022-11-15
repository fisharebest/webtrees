<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Manage media from the control panel.
 */
class ManageMediaPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

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

        $data_filesystem      = Registry::filesystem()->data();
        $data_filesystem_name = Registry::filesystem()->dataName();

        $files         = Validator::queryParams($request)->isInArray(['local', 'external', 'unused'])->string('files', 'local');
        $subfolders    = Validator::queryParams($request)->isInArray(['include', 'exclude'])->string('subfolders', 'exclude');
        $media_folders = $this->media_file_service->allMediaFolders($data_filesystem);
        $media_folder  = Validator::queryParams($request)->string('media_folder', $media_folders->first() ?? '');
        $media_types   = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->values();

        $title = I18N::translate('Manage media');

        return $this->viewResponse('admin/media', [
            'data_folder'   => $data_filesystem_name,
            'files'         => $files,
            'media_folder'  => $media_folder,
            'media_folders' => $media_folders,
            'media_types'   => $media_types,
            'subfolders'    => $subfolders,
            'title'         => $title,
        ]);
    }
}
