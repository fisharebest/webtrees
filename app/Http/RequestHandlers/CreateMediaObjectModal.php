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

use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Show a form to create a new media object.
 */
class CreateMediaObjectModal implements RequestHandlerInterface
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree            = Validator::attributes($request)->tree();
        $max_upload_size = $this->media_file_service->maxUploadFilesize();
        $media_types     = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->values();
        $unused_files    = $this->media_file_service->unusedFiles($tree);

        return response(view('modals/create-media-object', [
            'max_upload_size' => $max_upload_size,
            'media_types'     => $media_types,
            'tree'            => $tree,
            'unused_files'    => $unused_files,
        ]));
    }
}
