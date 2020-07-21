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
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\FilesystemInterface;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function pathinfo;
use function redirect;
use function strtolower;

use const PATHINFO_EXTENSION;

/**
 * Controller for the media page and displaying images.
 */
class MediaFileThumbnail implements RequestHandlerInterface
{
    /** @var MediaFileService */
    private $media_file_service;

    /**
     * MediaFileController constructor.
     *
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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $params  = $request->getQueryParams();
        $xref    = $params['xref'];
        $fact_id = $params['fact_id'];
        $media   = Factory::media()->make($xref, $tree);

        if ($media === null) {
            return $this->media_file_service->replacementImage((string) StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if (!$media->canShow()) {
            return $this->media_file_service->replacementImage((string) StatusCodeInterface::STATUS_FORBIDDEN);
        }

        foreach ($media->mediaFiles() as $media_file) {
            if ($media_file->factId() === $fact_id) {
                if ($media_file->isExternal()) {
                    return redirect($media_file->filename());
                }

                // Validate HTTP signature
                unset($params['route']);
                $params['tree'] = $media_file->media()->tree()->name();

                try {
                    SignatureFactory::create(Site::getPreference('glide-key'))
                        ->validateRequest('', $params);
                } catch (SignatureException $ex) {
                    return $this->media_file_service->replacementImage((string) StatusCodeInterface::STATUS_FORBIDDEN)
                        ->withHeader('X-Signature-Exception', $ex->getMessage());
                }

                if ($media_file->isImage()) {
                    $media_folder = $media_file->media()->tree()->getPreference('MEDIA_DIRECTORY', 'media/');
                    $file         = $media_file->filename();

                    return $this->media_file_service->generateImage($media_folder, $file, $data_filesystem, $request->getQueryParams());
                }

                // Shouldn't usually get here, as we only generate these URLs for images.
                $extension = '.' . strtolower(pathinfo($media_file->filename(), PATHINFO_EXTENSION));

                return $this->media_file_service->replacementImage($extension);
            }
        }

        return $this->media_file_service->replacementImage((string) StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
