<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Exceptions\FileUploadException;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\FilesystemReader;
use League\Flysystem\StorageAttributes;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

use function array_combine;
use function array_diff;
use function array_intersect;
use function dirname;
use function explode;
use function ini_get;
use function intdiv;
use function min;
use function pathinfo;
use function sha1;
use function sort;
use function str_contains;
use function strlen;
use function strtoupper;
use function strtr;
use function trim;

use const PATHINFO_EXTENSION;
use const UPLOAD_ERR_OK;

/**
 * Managing media files.
 */
class MediaFileService
{
    private const IGNORE_FOLDERS = [
        // Old versions of webtrees
        'thumbs',
        'watermarks',
        // Windows
        'Thumbs.db',
        // Synology
        '@eaDir',
        // QNAP,
        '.@__thumb',
        // WebDAV,
        '_DAV',
    ];

    /**
     * What is the largest file a user may upload?
     */
    public function maxUploadFilesize(): string
    {
        $sizePostMax   = $this->parseIniFileSize((string) ini_get('post_max_size'));
        $sizeUploadMax = $this->parseIniFileSize((string) ini_get('upload_max_filesize'));

        $bytes = min($sizePostMax, $sizeUploadMax);
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

        $units = [
            'g' => 1073741824,
            'G' => 1073741824,
            'm' => 1048576,
            'M' => 1048576,
            'k' => 1024,
            'K' => 1024,
        ];

        $number *= $units[substr($size, -1)] ?? 1;

        if (is_float($number)) {
            // Probably a 32bit version of PHP, with an INI setting >= 2GB
            return PHP_INT_MAX;
        }

        return $number;
    }

    /**
     * A list of media files not already linked to a media object.
     *
     * @param Tree $tree
     *
     * @return array<string>
     */
    public function unusedFiles(Tree $tree): array
    {
        $used_files = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->pluck('multimedia_file_refn')
            ->all();

        $media_filesystem = $tree->mediaFilesystem();
        $disk_files       = $this->allFilesOnDisk($media_filesystem, '', FilesystemReader::LIST_DEEP)->all();
        $unused_files     = array_diff($disk_files, $used_files);

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
     * @throws FilesystemException
     */
    public function uploadFile(ServerRequestInterface $request): string
    {
        $tree          = Validator::attributes($request)->tree();
        $file_location = Validator::parsedBody($request)->string('file_location');

        switch ($file_location) {
            case 'url':
                $remote = Validator::parsedBody($request)->string('remote');

                if (str_contains($remote, '://')) {
                    return $remote;
                }

                return '';

            case 'unused':
                $unused = Validator::parsedBody($request)->string('unused');

                if ($tree->mediaFilesystem()->fileExists($unused)) {
                    return $unused;
                }

                return '';

            case 'upload':
                $folder   = Validator::parsedBody($request)->string('folder');
                $auto     = Validator::parsedBody($request)->string('auto');
                $new_file = Validator::parsedBody($request)->string('new_file');

                $uploaded_file = $request->getUploadedFiles()['file'] ?? null;

                if ($uploaded_file === null || $uploaded_file->getError() !== UPLOAD_ERR_OK) {
                    throw new FileUploadException($uploaded_file);
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
                if ($auto === '1' || $tree->mediaFilesystem()->fileExists($folder . $file)) {
                    $folder    = '';
                    $extension = pathinfo($uploaded_file->getClientFilename(), PATHINFO_EXTENSION);
                    $file      = sha1((string) $uploaded_file->getStream()) . '.' . $extension;
                }

                try {
                    $tree->mediaFilesystem()->writeStream($folder . $file, $uploaded_file->getStream()->detach());

                    return $folder . $file;
                } catch (RuntimeException | InvalidArgumentException $ex) {
                    FlashMessages::addMessage(I18N::translate('There was an error uploading your file.'));

                    return '';
                }
        }

        return '';
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
        $gedcom = '1 FILE ' . $file;

        if (str_contains($file, '://')) {
            $format = '';
        } else {
            $format = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
            $format = Registry::elementFactory()->make('OBJE:FILE:FORM')->canonical($format);
        }

        if ($format !== '') {
            $gedcom .= "\n2 FORM " . strtr($format, ["\n" => "\n3 CONT "]);
        } elseif ($type !== '') {
            $gedcom .= "\n2 FORM";
        }

        if ($type !== '') {
            $gedcom .= "\n3 TYPE " . strtr($type, ["\n" => "\n4 CONT "]);
        }

        if ($title !== '') {
            $gedcom .= "\n2 TITL " . strtr($title, ["\n" => "\n3 CONT "]);
        }

        if ($note !== '') {
            // Convert HTML line endings to GEDCOM continuations
            $gedcom .= "\n1 NOTE " . strtr($note, ["\n" => "\n2 CONT "]);
        }

        return $gedcom;
    }

    /**
     * Fetch a list of all files on disk (in folders used by any tree).
     *
     * @param FilesystemOperator $filesystem $filesystem to search
     * @param string             $folder     Root folder
     * @param bool               $subfolders Include subfolders
     *
     * @return Collection<int,string>
     */
    public function allFilesOnDisk(FilesystemOperator $filesystem, string $folder, bool $subfolders): Collection
    {
        try {
            $files = $filesystem
                ->listContents($folder, $subfolders)
                ->filter(fn (StorageAttributes $attributes): bool => $attributes->isFile())
                ->filter(fn (StorageAttributes $attributes): bool => !$this->ignorePath($attributes->path()))
                ->map(fn (StorageAttributes $attributes): string => $attributes->path())
                ->toArray();
        } catch (FilesystemException $ex) {
            $files = [];
        }

        return new Collection($files);
    }

    /**
     * Fetch a list of all files on in the database.
     *
     * @param string $media_folder Root folder
     * @param bool   $subfolders   Include subfolders
     *
     * @return Collection<int,string>
     */
    public function allFilesInDatabase(string $media_folder, bool $subfolders): Collection
    {
        $path = DB::concat(['setting_value', 'multimedia_file_refn']);

        $query = DB::table('media_file')
            ->join('gedcom_setting', 'gedcom_id', '=', 'm_file')
            ->where('setting_name', '=', 'MEDIA_DIRECTORY')
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->where(new Expression($path), 'LIKE', $media_folder . '%');

        if (!$subfolders) {
            $query->where(new Expression($path), 'NOT LIKE', $media_folder . '%/%');
        }

        return $query
            ->orderBy(new Expression($path))
            ->pluck(new Expression($path . ' AS value'));
    }

    /**
     * Generate a list of all folders used by a tree.
     *
     * @param Tree $tree
     *
     * @return Collection<int,string>
     * @throws FilesystemException
     */
    public function mediaFolders(Tree $tree): Collection
    {
        $folders = $tree->mediaFilesystem()
            ->listContents('', FilesystemReader::LIST_DEEP)
            ->filter(fn (StorageAttributes $attributes): bool => $attributes->isDir())
            ->filter(fn (StorageAttributes $attributes): bool => !$this->ignorePath($attributes->path()))
            ->map(fn (StorageAttributes $attributes): string => $attributes->path())
            ->toArray();

        return new Collection($folders);
    }

    /**
     * Generate a list of all folders in either the database or the filesystem.
     *
     * @param FilesystemOperator $data_filesystem
     *
     * @return Collection<array-key,string>
     * @throws FilesystemException
     */
    public function allMediaFolders(FilesystemOperator $data_filesystem): Collection
    {
        $db_folders = DB::table('media_file')
            ->leftJoin('gedcom_setting', static function (JoinClause $join): void {
                $join
                    ->on('gedcom_id', '=', 'm_file')
                    ->where('setting_name', '=', 'MEDIA_DIRECTORY');
            })
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->pluck(new Expression("COALESCE(setting_value, 'media/') || multimedia_file_refn AS value"))
            ->map(static fn (string $path): string => dirname($path) . '/');

        $media_roots = DB::table('gedcom')
            ->leftJoin('gedcom_setting', static function (JoinClause $join): void {
                $join
                    ->on('gedcom.gedcom_id', '=', 'gedcom_setting.gedcom_id')
                    ->where('setting_name', '=', 'MEDIA_DIRECTORY');
            })
            ->where('gedcom.gedcom_id', '>', '0')
            ->pluck(new Expression("COALESCE(setting_value, 'media/') AS value"))
            ->uniqueStrict();

        $disk_folders = new Collection($media_roots);

        foreach ($media_roots as $media_folder) {
            $tmp = $data_filesystem
                ->listContents($media_folder, FilesystemReader::LIST_DEEP)
                ->filter(fn (StorageAttributes $attributes): bool => $attributes->isDir())
                ->filter(fn (StorageAttributes $attributes): bool => !$this->ignorePath($attributes->path()))
                ->map(fn (StorageAttributes $attributes): string => $attributes->path() . '/')
                ->toArray();

            $disk_folders = $disk_folders->concat($tmp);
        }

        return $disk_folders->concat($db_folders)
            ->uniqueStrict()
            ->sort(I18N::comparator())
            ->mapWithKeys(static function (string $folder): array {
                return [$folder => $folder];
            });
    }

    /**
     * Ignore special media folders.
     *
     * @param string $path
     *
     * @return bool
     */
    private function ignorePath(string $path): bool
    {
        return array_intersect(self::IGNORE_FOLDERS, explode('/', $path)) !== [];
    }
}
