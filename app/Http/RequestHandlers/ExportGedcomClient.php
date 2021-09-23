<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function addcslashes;
use function assert;
use function fclose;
use function pathinfo;
use function strtolower;
use function tmpfile;

use const PATHINFO_EXTENSION;

/**
 * Download a GEDCOM file to the client.
 */
class ExportGedcomClient implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private GedcomExportService $gedcom_export_service;

    private ResponseFactoryInterface $response_factory;

    private StreamFactoryInterface $stream_factory;

    /**
     * ExportGedcomServer constructor.
     *
     * @param GedcomExportService      $gedcom_export_service
     * @param ResponseFactoryInterface $response_factory
     * @param StreamFactoryInterface   $stream_factory
     */
    public function __construct(
        GedcomExportService $gedcom_export_service,
        ResponseFactoryInterface $response_factory,
        StreamFactoryInterface $stream_factory
    ) {
        $this->gedcom_export_service = $gedcom_export_service;
        $this->response_factory = $response_factory;
        $this->stream_factory = $stream_factory;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws FilesystemException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = Registry::filesystem()->data();

        $params = (array) $request->getParsedBody();

        $convert          = (bool) ($params['convert'] ?? false);
        $zip              = (bool) ($params['zip'] ?? false);
        $media            = (bool) ($params['media'] ?? false);
        $media_path       = $params['media-path'] ?? '';
        $privatize_export = $params['privatize_export'];

        $access_levels = [
            'gedadmin' => Auth::PRIV_NONE,
            'user'     => Auth::PRIV_USER,
            'visitor'  => Auth::PRIV_PRIVATE,
            'none'     => Auth::PRIV_HIDE,
        ];

        $access_level = $access_levels[$privatize_export];
        $encoding     = $convert ? 'ANSI' : 'UTF-8';

        // What to call the downloaded file
        $download_filename = $tree->name();

        // Force a ".ged" suffix
        if (strtolower(pathinfo($download_filename, PATHINFO_EXTENSION)) !== 'ged') {
            $download_filename .= '.ged';
        }

        if ($zip || $media) {
            $resource = $this->gedcom_export_service->export($tree, true, $encoding, $access_level, $media_path);

            $path = $tree->getPreference('MEDIA_DIRECTORY');

            // Create a new/empty .ZIP file
            $temp_zip_file  = stream_get_meta_data(tmpfile())['uri'];
            $zip_provider   = new FilesystemZipArchiveProvider($temp_zip_file, 0755);
            $zip_adapter    = new ZipArchiveAdapter($zip_provider);
            $zip_filesystem = new Filesystem($zip_adapter);
            $zip_filesystem->writeStream($download_filename, $resource);
            fclose($resource);

            if ($media) {
                $media_filesystem = $tree->mediaFilesystem($data_filesystem);

                $records = DB::table('media')
                    ->where('m_file', '=', $tree->id())
                    ->get()
                    ->map(Registry::mediaFactory()->mapper($tree))
                    ->filter(GedcomRecord::accessFilter());

                foreach ($records as $record) {
                    foreach ($record->mediaFiles() as $media_file) {
                        $from = $media_file->filename();
                        $to   = $path . $media_file->filename();
                        if (!$media_file->isExternal() && $media_filesystem->fileExists($from) && !$zip_filesystem->fileExists($to)) {
                            $zip_filesystem->writeStream($to, $media_filesystem->readStream($from));
                        }
                    }
                }
            }

            $stream   = $this->stream_factory->createStreamFromFile($temp_zip_file);
            $filename = addcslashes($download_filename, '"') . '.zip';

            return $this->response_factory->createResponse()
                ->withBody($stream)
                ->withHeader('Content-Type', 'application/zip')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        $resource = $this->gedcom_export_service->export($tree, true, $encoding, $access_level, $media_path);

        $charset = $convert ? 'ISO-8859-1' : 'UTF-8';
        $stream  = $this->stream_factory->createStreamFromResource($resource);

        return $this->response_factory->createResponse()
            ->withBody($stream)
            ->withHeader('Content-Type', 'text/x-gedcom; charset=' . $charset)
            ->withHeader('Content-Disposition', 'attachment; filename="' . addcslashes($download_filename, '"') . '"');
    }
}
