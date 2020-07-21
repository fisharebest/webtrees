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

namespace Fisharebest\Webtrees\Services;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Flysystem\Adapter\ChrootAdapter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\ServerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Throwable;

use function array_combine;
use function array_diff;
use function array_filter;
use function array_map;
use function assert;
use function dirname;
use function explode;
use function extension_loaded;
use function implode;
use function ini_get;
use function intdiv;
use function min;
use function pathinfo;
use function preg_replace;
use function response;
use function sha1;
use function sort;
use function str_contains;
use function strlen;
use function strtolower;
use function strtr;
use function substr;
use function trim;
use function view;

use const PATHINFO_EXTENSION;
use const UPLOAD_ERR_OK;

/**
 * Managing media files.
 */
class MediaFileService
{
    public const EDIT_RESTRICTIONS = [
        'locked',
    ];

    public const PRIVACY_RESTRICTIONS = [
        'none',
        'privacy',
        'confidential',
    ];

    public const EXTENSION_TO_FORM = [
        'jpg' => 'jpeg',
        'tif' => 'tiff',
    ];

    public const SUPPORTED_LIBRARIES = ['imagick', 'gd'];

    /**
     * What is the largest file a user may upload?
     */
    public function maxUploadFilesize(): string
    {
        $sizePostMax = $this->parseIniFileSize(ini_get('post_max_size'));
        $sizeUploadMax = $this->parseIniFileSize(ini_get('upload_max_filesize'));

        $bytes =  min($sizePostMax, $sizeUploadMax);
        $kb    = intdiv($bytes + 1023, 1024);

        return I18N::translate('%s KB', I18N::number($kb));
    }

    /**
     * Returns the given size from an ini value in bytes.
     *
     * @param string $size
     *
     * @return int
     */
    private function parseIniFileSize(string $size): int
    {
        $number = (int) $size;

        switch (substr($size, -1)) {
            case 'g':
            case 'G':
                return $number * 1073741824;
            case 'm':
            case 'M':
                return $number * 1048576;
            case 'k':
            case 'K':
                return $number * 1024;
            default:
                return $number;
        }
    }

    /**
     * A list of key/value options for media types.
     *
     * @param string $current
     *
     * @return array<string,string>
     */
    public function mediaTypes($current = ''): array
    {
        $media_types = GedcomTag::getFileFormTypes();

        $media_types = ['' => ''] + [$current => $current] + $media_types;

        return $media_types;
    }

    /**
     * A list of media files not already linked to a media object.
     *
     * @param Tree                $tree
     * @param FilesystemInterface $data_filesystem
     *
     * @return array<string>
     */
    public function unusedFiles(Tree $tree, FilesystemInterface $data_filesystem): array
    {
        $used_files = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->pluck('multimedia_file_refn')
            ->all();

        $disk_files = $tree->mediaFilesystem($data_filesystem)->listContents('', true);

        $disk_files = array_filter($disk_files, static function (array $item) {
            // Older versions of webtrees used a couple of special folders.
            return
                $item['type'] === 'file' &&
                !str_contains($item['path'], '/thumbs/') &&
                !str_contains($item['path'], '/watermarks/');
        });

        $disk_files = array_map(static function (array $item): string {
            return $item['path'];
        }, $disk_files);

        $unused_files = array_diff($disk_files, $used_files);

        sort($unused_files);

        return array_combine($unused_files, $unused_files);
    }

    /**
     * Store an uploaded file (or URL), either to be added to a media object
     * or to create a media object.
     *
     * @param ServerRequestInterface $request
     *
     * @return string The value to be stored in the 'FILE' field of the media object.
     */
    public function uploadFile(ServerRequestInterface $request): string
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $params        = (array) $request->getParsedBody();
        $file_location = $params['file_location'];

        switch ($file_location) {
            case 'url':
                $remote = $params['remote'];

                if (str_contains($remote, '://')) {
                    return $remote;
                }

                return '';

            case 'unused':
                $unused = $params['unused'];

                if ($tree->mediaFilesystem($data_filesystem)->has($unused)) {
                    return $unused;
                }

                return '';

            case 'upload':
            default:
                $folder   = $params['folder'];
                $auto     = $params['auto'];
                $new_file = $params['new_file'];

                /** @var UploadedFileInterface|null $uploaded_file */
                $uploaded_file = $request->getUploadedFiles()['file'];
                if ($uploaded_file === null || $uploaded_file->getError() !== UPLOAD_ERR_OK) {
                    return '';
                }

                // The filename
                $new_file = strtr($new_file, ['\\' => '/']);
                if ($new_file !== '' && !str_contains($new_file, '/')) {
                    $file = $new_file;
                } else {
                    $file = $uploaded_file->getClientFilename();
                }

                // The folder
                $folder = strtr($folder, ['\\' => '/']);
                $folder = trim($folder, '/');
                if ($folder !== '') {
                    $folder .= '/';
                }

                // Generate a unique name for the file?
                if ($auto === '1' || $tree->mediaFilesystem($data_filesystem)->has($folder . $file)) {
                    $folder    = '';
                    $extension = pathinfo($uploaded_file->getClientFilename(), PATHINFO_EXTENSION);
                    $file      = sha1((string) $uploaded_file->getStream()) . '.' . $extension;
                }

                try {
                    $tree->mediaFilesystem($data_filesystem)->putStream($folder . $file, $uploaded_file->getStream()->detach());

                    return $folder . $file;
                } catch (RuntimeException | InvalidArgumentException $ex) {
                    FlashMessages::addMessage(I18N::translate('There was an error uploading your file.'));

                    return '';
                }
        }
    }

    /**
     * Convert the media file attributes into GEDCOM format.
     *
     * @param string $file
     * @param string $type
     * @param string $title
     * @param string $note
     *
     * @return string
     */
    public function createMediaFileGedcom(string $file, string $type, string $title, string $note): string
    {
        // Tidy non-printing characters
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        $gedcom = '1 FILE ' . $file;

        $format = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $format = self::EXTENSION_TO_FORM[$format] ?? $format;

        if ($format !== '') {
            $gedcom .= "\n2 FORM " . $format;
        } elseif ($type !== '') {
            $gedcom .= "\n2 FORM";
        }

        if ($type !== '') {
            $gedcom .= "\n3 TYPE " . $type;
        }

        if ($title !== '') {
            $gedcom .= "\n2 TITL " . $title;
        }

        if ($note !== '') {
            // Convert HTML line endings to GEDCOM continuations
            $gedcom .= "\n1 NOTE " . strtr($note, ["\r\n" => "\n2 CONT "]);
        }

        return $gedcom;
    }

    /**
     * Fetch a list of all files on disk (in folders used by any tree).
     *
     * @param FilesystemInterface $data_filesystem Fileystem to search
     * @param string              $media_folder    Root folder
     * @param bool                $subfolders      Include subfolders
     *
     * @return Collection<string>
     */
    public function allFilesOnDisk(FilesystemInterface $data_filesystem, string $media_folder, bool $subfolders): Collection
    {
        $array = $data_filesystem->listContents($media_folder, $subfolders);

        return Collection::make($array)
            ->filter(static function (array $metadata): bool {
                return
                    $metadata['type'] === 'file' &&
                    !str_contains($metadata['path'], '/thumbs/') &&
                    !str_contains($metadata['path'], '/watermark/');
            })
            ->map(static function (array $metadata): string {
                return $metadata['path'];
            });
    }

    /**
     * Fetch a list of all files on in the database.
     *
     * @param string $media_folder Root folder
     * @param bool   $subfolders   Include subfolders
     *
     * @return Collection<string>
     */
    public function allFilesInDatabase(string $media_folder, bool $subfolders): Collection
    {
        $query = DB::table('media_file')
            ->join('gedcom_setting', 'gedcom_id', '=', 'm_file')
            ->where('setting_name', '=', 'MEDIA_DIRECTORY')
            //->where('multimedia_file_refn', 'LIKE', '%/%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->where(new Expression('setting_value || multimedia_file_refn'), 'LIKE', $media_folder . '%')
            ->select(new Expression('setting_value || multimedia_file_refn AS path'))
            ->orderBy(new Expression('setting_value || multimedia_file_refn'));

        if (!$subfolders) {
            $query->where(new Expression('setting_value || multimedia_file_refn'), 'NOT LIKE', $media_folder . '%/%');
        }

        return $query->pluck('path');
    }

    /**
     * Generate a list of all folders in either the database or the filesystem.
     *
     * @param FilesystemInterface $data_filesystem
     *
     * @return Collection<string,string>
     */
    public function allMediaFolders(FilesystemInterface $data_filesystem): Collection
    {
        $db_folders = DB::table('media_file')
            ->join('gedcom_setting', 'gedcom_id', '=', 'm_file')
            ->where('setting_name', '=', 'MEDIA_DIRECTORY')
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->select(new Expression('setting_value || multimedia_file_refn AS path'))
            ->pluck('path')
            ->map(static function (string $path): string {
                return dirname($path) . '/';
            });

        $media_roots = DB::table('gedcom_setting')
            ->where('setting_name', '=', 'MEDIA_DIRECTORY')
            ->where('gedcom_id', '>', '0')
            ->pluck('setting_value')
            ->uniqueStrict();

        $disk_folders = new Collection($media_roots);

        foreach ($media_roots as $media_folder) {
            $tmp = Collection::make($data_filesystem->listContents($media_folder, true))
                ->filter(static function (array $metadata) {
                    return $metadata['type'] === 'dir';
                })
                ->map(static function (array $metadata): string {
                    return $metadata['path'] . '/';
                })
                ->filter(static function (string $dir): bool {
                    return !str_contains($dir, '/thumbs/') && !str_contains($dir, 'watermarks');
                });

            $disk_folders = $disk_folders->concat($tmp);
        }

        return $disk_folders->concat($db_folders)
            ->uniqueStrict()
            ->mapWithKeys(static function (string $folder): array {
                return [$folder => $folder];
            });
    }

    /**
     * Send a replacement image, to replace one that could not be found or created.
     *
     * @param string $status HTTP status code or file extension
     *
     * @return ResponseInterface
     */
    public function replacementImage(string $status): ResponseInterface
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
     * @param string              $folder
     * @param string              $file
     * @param FilesystemInterface $filesystem
     * @param array<string>       $params
     *
     * @return ResponseInterface
     */
    public function generateImage(string $folder, string $file, FilesystemInterface $filesystem, array $params): ResponseInterface
    {
        // Automatic rotation only works when the php-exif library is loaded.
        if (!extension_loaded('exif')) {
            $params['or'] = '0';
        }

        try {
            $cache_path           = 'thumbnail-cache/' . $folder;
            $cache_filesystem     = new Filesystem(new ChrootAdapter($filesystem, $cache_path));
            $source_filesystem    = new Filesystem(new ChrootAdapter($filesystem, $folder));
            $watermark_filesystem = new Filesystem(new Local('resources/img'));

            $server = ServerFactory::create([
                'cache'      => $cache_filesystem,
                'driver'     => $this->graphicsDriver(),
                'source'     => $source_filesystem,
                'watermarks' => $watermark_filesystem,
            ]);

            // Workaround for https://github.com/thephpleague/glide/issues/227
            $file = implode('/', array_map('rawurlencode', explode('/', $file)));

            $thumbnail = $server->makeImage($file, $params);
            $cache     = $server->getCache();

            return response($cache->read($thumbnail), StatusCodeInterface::STATUS_OK, [
                'Content-Type'   => $cache->getMimetype($thumbnail) ?: Mime::DEFAULT_TYPE,
                'Content-Length' => (string) $cache->getSize($thumbnail),
                'Cache-Control'  => 'public,max-age=31536000',
            ]);
        } catch (FileNotFoundException $ex) {
            return $this->replacementImage((string) StatusCodeInterface::STATUS_NOT_FOUND);
        } catch (Throwable $ex) {
            return $this->replacementImage((string) StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
                ->withHeader('X-Thumbnail-Exception', $ex->getMessage());
        }
    }

    /**
     * Which graphics driver should we use for glide/intervention?
     * Prefer ImageMagick
     *
     * @return string
     */
    private function graphicsDriver(): string
    {
        foreach (self::SUPPORTED_LIBRARIES as $library) {
            if (extension_loaded($library)) {
                return $library;
            }
        }

        throw new RuntimeException('No PHP graphics library is installed.');
    }
}
