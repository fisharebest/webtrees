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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Enums\ImageOperation;
use Fisharebest\Webtrees\Exceptions\ImageException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function basename;
use function implode;
use function redirect;
use function response;

final class MediaFileThumbnail implements RequestHandlerInterface
{
    private const int THUMBNAIL_CACHE_TTL = 8640000;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        $params  = $request->getQueryParams();
        $xref    = Validator::queryParams($request)->isXref()->string('xref');
        $fact_id = Validator::queryParams($request)->string('fact_id');
        $media   = Registry::mediaFactory()->make($xref, $tree);

        if ($media === null) {
            throw new ImageException(
                status_code: StatusCodeInterface::STATUS_NOT_FOUND,
                filename: $xref,
                error: 'Not found',
            );
        }

        if (!$media->canShow()) {
            throw new ImageException(
                status_code: StatusCodeInterface::STATUS_FORBIDDEN,
                filename: $xref,
                error: 'Access denied',
            );
        }

        foreach ($media->mediaFiles() as $media_file) {
            if ($media_file->factId() === $fact_id) {
                if ($media_file->isExternal()) {
                    return redirect($media_file->filename());
                }

                // Validate HTTP signature
                unset($params['route']);
                $params['tree'] = $media_file->media()->tree()->name();

                if ($media_file->signature($params) !== $params['s']) {
                    throw new ImageException(
                        status_code: StatusCodeInterface::STATUS_FORBIDDEN,
                        filename: $media_file->filename(),
                        error: 'Bad signature',
                    );
                }

                $image_factory = Registry::imageFactory();
                $operation     = ImageOperation::from($params['fit']);

                $width         = (int) $params['w'];
                $height        = (int) $params['h'];
                $add_watermark = Auth::needsWatermark($media_file->media()->tree(), $user);
                $path          = $media_file->filename();
                $filename      = basename($path);
                $filesystem    = $media_file->media()->tree()->mediaFilesystem();

                try {
                    $last_modified = $filesystem->lastModified(path: $path);
                } catch (FilesystemException $exception) {
                    throw new ImageException(
                        status_code: StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                        filename: $filename,
                        error: 'File is not readable',
                    );
                }

                $cache_key = implode(separator: ':', array: [
                    $media_file->media()->tree()->name(),
                    $path,
                    (string) $last_modified,
                    (string) $width,
                    (string) $height,
                    $operation->value,
                    (string) $add_watermark,
                ]);

                $thumbnail = Registry::cache()->file()->remember(
                    key: $cache_key,
                    closure: fn (): string => $image_factory->mediaFileThumbnail(
                        media_file: $media_file,
                        width: $width,
                        height: $height,
                        operation: $operation,
                        add_watermark: $add_watermark,
                    ),
                    ttl: self::THUMBNAIL_CACHE_TTL,
                );

                return response($thumbnail)
                    ->withHeader('content-type', $media_file->mimeType())
                    ->withHeader('content-security-policy', 'default-src none')
                    ->withHeader('cache-control', 'public,max-age=31536000');
            }
        }

        throw new ImageException(
            status_code: StatusCodeInterface::STATUS_NOT_FOUND,
            filename: $xref,
            error: 'No image',
        );
    }
}
