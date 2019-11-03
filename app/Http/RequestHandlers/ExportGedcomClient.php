<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function addcslashes;
use function app;
use function assert;
use function fclose;
use function fopen;
use function pathinfo;
use function rewind;
use function strtolower;
use function sys_get_temp_dir;
use function tempnam;
use function tmpfile;

use const PATHINFO_EXTENSION;

/**
 * Download a GEDCOM file to the client.
 */
class ExportGedcomClient implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
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

        $convert          = (bool) ($request->getParsedBody()['convert'] ?? false);
        $zip              = (bool) ($request->getParsedBody()['zip'] ?? false);
        $media            = (bool) ($request->getParsedBody()['media'] ?? false);
        $media_path       = $request->getParsedBody()['media-path'] ?? '';
        $privatize_export = $request->getParsedBody()['privatize_export'];

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
            // Export the GEDCOM to an in-memory stream.
            $tmp_stream = tmpfile();
            FunctionsExport::exportGedcom($tree, $tmp_stream, $access_level, $media_path, $encoding);
            rewind($tmp_stream);

            $path = $tree->getPreference('MEDIA_DIRECTORY', 'media/');

            // Create a new/empty .ZIP file
            $temp_zip_file  = tempnam(sys_get_temp_dir(), 'webtrees-zip-');
            $zip_adapter    = new ZipArchiveAdapter($temp_zip_file);
            $zip_filesystem = new Filesystem($zip_adapter);
            $zip_filesystem->writeStream($download_filename, $tmp_stream);
            fclose($tmp_stream);

            if ($media) {
                $manager = new MountManager([
                    'media' => $tree->mediaFilesystem($data_filesystem),
                    'zip'   => $zip_filesystem,
                ]);

                $records = DB::table('media')
                    ->where('m_file', '=', $tree->id())
                    ->get()
                    ->map(Media::rowMapper())
                    ->filter(GedcomRecord::accessFilter());

                foreach ($records as $record) {
                    foreach ($record->mediaFiles() as $media_file) {
                        $from = 'media://' . $media_file->filename();
                        $to   = 'zip://' . $path . $media_file->filename();
                        if (!$media_file->isExternal() && $manager->has($from)) {
                            $manager->copy($from, $to);
                        }
                    }
                }
            }

            // Need to force-close ZipArchive filesystems.
            $zip_adapter->getArchive()->close();

            // Use a stream, so that we do not have to load the entire file into memory.
            $stream   = app(StreamFactoryInterface::class)->createStreamFromFile($temp_zip_file);
            $filename = addcslashes($download_filename, '"') . '.zip';

            /** @var ResponseFactoryInterface $response_factory */
            $response_factory = app(ResponseFactoryInterface::class);

            return $response_factory->createResponse()
                ->withBody($stream)
                ->withHeader('Content-Type', 'application/zip')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        $resource = fopen('php://temp', 'wb+');
        FunctionsExport::exportGedcom($tree, $resource, $access_level, $media_path, $encoding);
        rewind($resource);

        $charset = $convert ? 'ISO-8859-1' : 'UTF-8';

        /** @var StreamFactoryInterface $response_factory */
        $stream_factory = app(StreamFactoryInterface::class);

        $stream = $stream_factory->createStreamFromResource($resource);

        /** @var ResponseFactoryInterface $response_factory */
        $response_factory = app(ResponseFactoryInterface::class);

        return $response_factory->createResponse()
            ->withBody($stream)
            ->withHeader('Content-Type', 'text/x-gedcom; charset=' . $charset)
            ->withHeader('Content-Disposition', 'attachment; filename="' . addcslashes($download_filename, '"') . '"');
    }
}
