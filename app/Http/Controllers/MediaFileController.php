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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Flysystem\Adapter\ChrootAdapter;
use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Exceptions\MediaNotFoundException;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Intervention\Image\Exception\NotReadableException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\ServerFactory;
use League\Glide\Signatures\Signature;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function addcslashes;
use function array_map;
use function assert;
use function basename;
use function dirname;
use function explode;
use function extension_loaded;
use function implode;
use function md5;
use function pathinfo;
use function redirect;
use function response;
use function strlen;
use function strtolower;

use const PATHINFO_EXTENSION;

/**
 * Controller for the media page and displaying images.
 */
class MediaFileController extends AbstractBaseController
{
    /**
     * Download a non-image media file.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mediaDownload(ServerRequestInterface $request): ResponseInterface
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

        if ($media === null) {
            throw new MediaNotFoundException();
        }

        if (!$media->canShow()) {
            throw new HttpAccessDeniedException();
        }

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

    /**
     * Show an image/thumbnail, with/without a watermark.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function mediaThumbnail(ServerRequestInterface $request): ResponseInterface
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
            return $this->httpStatusAsImage(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if (!$media->canShow()) {
            return $this->httpStatusAsImage(StatusCodeInterface::STATUS_FORBIDDEN);
        }

        foreach ($media->mediaFiles() as $media_file) {
            if ($media_file->factId() === $fact_id) {
                if ($media_file->isExternal()) {
                    return redirect($media_file->filename());
                }

                if ($media_file->isImage()) {
                    return $this->generateImage($media_file, $data_filesystem, $request->getQueryParams());
                }

                return $this->fileExtensionAsImage($media_file->filename());
            }
        }

        return $this->httpStatusAsImage(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    /**
     * Send a dummy image, to replace one that could not be found or created.
     *
     * @param int $status HTTP status code
     *
     * @return ResponseInterface
     */
    private function httpStatusAsImage(int $status): ResponseInterface
    {
        $svg = view('errors/image-svg', ['status' => $status]);

        // We can't use the actual status code, as browsers won't show images with 4xx/5xx
        return response($svg, StatusCodeInterface::STATUS_OK, [
            'Content-Type'   => 'image/svg+xml',
            'Content-Length' => (string) strlen($svg),
        ]);
    }

    /**
     * Generate a thumbnail image for a file.
     *
     * @param MediaFile           $media_file
     * @param FilesystemInterface $data_filesystem
     * @param array               $params
     *
     * @return ResponseInterface
     */
    private function generateImage(MediaFile $media_file, FilesystemInterface $data_filesystem, array $params): ResponseInterface
    {
        try {
            // Validate HTTP signature
            unset($params['route']);
            $params['tree'] = $media_file->media()->tree()->name();
            $this->glideSignature()->validateRequest('', $params);

            $path = $media_file->media()->tree()->getPreference('MEDIA_DIRECTORY', 'media/') .  $media_file->filename();
            $folder = dirname($path);

            $cache_path           = 'thumbnail-cache/' . md5($folder);
            $cache_filesystem     = new Filesystem(new ChrootAdapter($data_filesystem, $cache_path));
            $source_filesystem    = $media_file->media()->tree()->mediaFilesystem($data_filesystem);
            $watermark_filesystem = new Filesystem(new Local('resources/img'));

            $server = ServerFactory::create([
                'cache'      => $cache_filesystem,
                'driver'     => $this->graphicsDriver(),
                'source'     => $source_filesystem,
                'watermarks' => $watermark_filesystem,
            ]);

            // Workaround for https://github.com/thephpleague/glide/issues/227
            $file = implode('/', array_map('rawurlencode', explode('/', $media_file->filename())));

            $path = $server->makeImage($file, $params);

            return response($server->getCache()->read($path), StatusCodeInterface::STATUS_OK, [
                'Content-Type'   => $server->getCache()->getMimetype($path) ?: Mime::DEFAULT_TYPE,
                'Content-Length' => (string) $server->getCache()->getSize($path),
                'Cache-Control'  => 'public,max-age=31536000',
            ]);
        } catch (SignatureException $ex) {
            return $this->httpStatusAsImage(StatusCodeInterface::STATUS_FORBIDDEN)
                ->withHeader('X-Signature-Exception', $ex->getMessage());
        } catch (FileNotFoundException $ex) {
            return $this->httpStatusAsImage(StatusCodeInterface::STATUS_NOT_FOUND);
        } catch (Throwable $ex) {
            Log::addErrorLog('Cannot create thumbnail ' . $ex->getMessage());

            return $this->httpStatusAsImage(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate a signature, to verify the request parameters.
     *
     * @return Signature
     */
    private function glideSignature(): Signature
    {
        $glide_key = Site::getPreference('glide-key');

        return SignatureFactory::create($glide_key);
    }

    /**
     * Which graphics driver should we use for glide/intervention?
     * Prefer ImageMagick
     *
     * @return string
     */
    private function graphicsDriver(): string
    {
        if (extension_loaded('imagick')) {
            $driver = 'imagick';
        } else {
            $driver = 'gd';
        }

        return $driver;
    }

    /**
     * Send a dummy image, to replace a non-image file.
     *
     * @param string $filename
     *
     * @return ResponseInterface
     */
    private function fileExtensionAsImage(string $filename): ResponseInterface
    {
        $extension = '.' . strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="#88F" /><text x="5" y="60" font-family="Verdana" font-size="30">' . $extension . '</text></svg>';

        return response($svg, StatusCodeInterface::STATUS_OK, [
            'Content-Type'   => 'image/svg+xml',
            'Content-Length' => (string) strlen($svg),
        ]);
    }

    /**
     * Generate a thumbnail for an unsed media file (i.e. not used by any media object).
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function unusedMediaThumbnail(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $params = $request->getQueryParams();

        $path   = $params['path'];

        // Workaround for https://github.com/thephpleague/glide/issues/227
        $path = implode('/', array_map('rawurlencode', explode('/', $path)));

        $folder = dirname($path);
        $file   = basename($path);

        try {
            $cache_path        = 'thumbnail-cache/' . md5($folder);
            $cache_filesystem  = new Filesystem(new ChrootAdapter($data_filesystem, $cache_path));
            $source_filesystem = new Filesystem(new ChrootAdapter($data_filesystem, $folder));

            $server = ServerFactory::create([
                'cache'  => $cache_filesystem,
                'driver' => $this->graphicsDriver(),
                'source' => $source_filesystem,
            ]);

            $thumbnail = $server->makeImage($file, $params);
            $cache     = $server->getCache();

            return response($cache->read($thumbnail), StatusCodeInterface::STATUS_OK, [
                'Content-Type'   => $cache->getMimetype($thumbnail) ?: Mime::DEFAULT_TYPE,
                'Content-Length' => (string) $cache->getSize($thumbnail),
                'Cache-Control'  => 'public,max-age=31536000',
            ]);
        } catch (FileNotFoundException $ex) {
            return $this->httpStatusAsImage(StatusCodeInterface::STATUS_NOT_FOUND);
        } catch (NotReadableException | Throwable $ex) {
            return $this->httpStatusAsImage(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
