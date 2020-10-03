<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function redirect;

/**
 * Download a media file.
 */
class MediaFileDownload implements RequestHandlerInterface
{
    /**
     * Download a non-image media file.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user = $request->getAttribute('user');
        assert($user instanceof UserInterface);

        $image_factory = Registry::imageFactory();

        $disposition = $request->getQueryParams()['disposition'] ?? 'inline';
        assert($disposition === 'inline' || $disposition === 'attachment');

        $params  = $request->getQueryParams();
        $xref    = $params['xref'];
        $fact_id = $params['fact_id'];
        $media   = Registry::mediaFactory()->make($xref, $tree);
        $media   = Auth::checkMediaAccess($media);

        foreach ($media->mediaFiles() as $media_file) {
            if ($media_file->factId() === $fact_id) {
                if ($media_file->isExternal()) {
                    return redirect($media_file->filename());
                }

                $watermark = $media_file->isImage() && $image_factory->fileNeedsWatermark($media_file, $user);
                $download  = $disposition === 'attachment';

                return $image_factory->mediaFileResponse($media_file, $watermark, $download);
            }
        }

        return $image_factory->replacementImageResponse((string) StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
