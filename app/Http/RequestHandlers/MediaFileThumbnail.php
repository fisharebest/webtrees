<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Create a thumbnail of a media file.
 */
class MediaFileThumbnail implements RequestHandlerInterface
{
    /**
     * Show an image/thumbnail, with/without a watermark.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        $params  = $request->getQueryParams();
        $xref    = Validator::queryParams($request)->isXref()->string('xref');
        $fact_id = Validator::queryParams($request)->string('fact_id');
        $media   = Registry::mediaFactory()->make($xref, $tree);

        if ($media === null) {
            return Registry::imageFactory()->replacementImageResponse((string) StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if (!$media->canShow()) {
            return Registry::imageFactory()->replacementImageResponse((string) StatusCodeInterface::STATUS_FORBIDDEN);
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
                    return Registry::imageFactory()->replacementImageResponse((string) StatusCodeInterface::STATUS_FORBIDDEN)
                        ->withHeader('x-signature-exception', 'Signature mismatch');
                }

                $image_factory = Registry::imageFactory();

                $response = $image_factory->mediaFileThumbnailResponse(
                    $media_file,
                    (int) $params['w'],
                    (int) $params['h'],
                    $params['fit'],
                    $image_factory->fileNeedsWatermark($media_file, $user)
                );

                return $response->withHeader('cache-control', 'public,max-age=31536000');
            }
        }

        return Registry::imageFactory()->replacementImageResponse((string) StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
