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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Enums\ImageOperation;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function implode;
use function response;

final class AdminMediaFileThumbnail implements RequestHandlerInterface
{
    private const int THUMBNAIL_CACHE_TTL = 8640000;

    public function __construct(
        private readonly MediaFileService $media_file_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $filesystem = Registry::filesystem()->data();
        $path       = Validator::queryParams($request)->string('path');

        $media_folders = $this->media_file_service->allMediaFolders($filesystem)->all();

        foreach ($media_folders as $media_folder) {
            if (str_starts_with($path, $media_folder)) {
                $image_factory = Registry::imageFactory();
                $mime_type     = $image_factory->fileMimeType($filesystem, $path);
                $last_modified = $filesystem->lastModified(path: $path);
                $cache_key     = implode(separator: ':', array: [
                    'admin',
                    $path,
                    (string) $last_modified,
                    '120',
                    '120',
                    ImageOperation::Contain->value,
                ]);
                $thumbnail = Registry::cache()->file()->remember(
                    key: $cache_key,
                    closure: fn (): string => $image_factory->thumbnailContents(
                        filesystem: $filesystem,
                        path: $path,
                        width: 120,
                        height: 120,
                        operation: ImageOperation::Contain,
                    ),
                    ttl: self::THUMBNAIL_CACHE_TTL,
                );

                return response($thumbnail)
                    ->withHeader('content-type', $mime_type)
                    ->withHeader('content-security-policy', 'default-src none');
            }
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “path” is invalid.'));
    }
}
