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
use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Exceptions\MediaNotFoundException;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function addcslashes;
use function assert;
use function redirect;
use function response;
use function strlen;

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

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $disposition = $request->getQueryParams()['disposition'] ?? 'inline';
        assert($disposition === 'inline' || $disposition === 'attachment');

        $params  = $request->getQueryParams();
        $xref    = $params['xref'];
        $fact_id = $params['fact_id'];
        $media   = Factory::media()->make($xref, $tree);
        $media   = Auth::checkMediaAccess($media);

        foreach ($media->mediaFiles() as $media_file) {
            if ($media_file->factId() === $fact_id) {
                if ($media_file->isExternal()) {
                    return redirect($media_file->filename());
                }

                if ($media_file->fileExists($data_filesystem)) {
                    $data = $media_file->media()->tree()->mediaFilesystem($data_filesystem)->read($media_file->filename());

                    return response($data, StatusCodeInterface::STATUS_OK, [
                        'Content-Type'        => $media_file->mimeType(),
                        'Content-Length'      => (string) strlen($data),
                        'Content-Disposition' => $disposition . '; filename="' . addcslashes($media_file->filename(), '"') . '"',
                    ]);
                }
            }
        }

        throw new HttpNotFoundException();
    }
}
