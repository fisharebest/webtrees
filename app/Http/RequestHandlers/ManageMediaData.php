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

use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\TreeService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function assert;
use function e;
use function getimagesizefromstring;
use function intdiv;
use function route;
use function str_starts_with;
use function strlen;
use function substr;
use function view;

/**
 * Manage media from the control panel.
 */
class ManageMediaData implements RequestHandlerInterface
{
    private DatatablesService $datatables_service;

    private MediaFileService $media_file_service;

    private TreeService $tree_service;

    /**
     * MediaController constructor.
     *
     * @param DatatablesService $datatables_service
     * @param MediaFileService  $media_file_service
     * @param TreeService       $tree_service
     */
    public function __construct(
        DatatablesService $datatables_service,
        MediaFileService $media_file_service,
        TreeService $tree_service
    ) {
        $this->datatables_service = $datatables_service;
        $this->media_file_service = $media_file_service;
        $this->tree_service       = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $files = $request->getQueryParams()['files']; // local|external|unused

        // Files within this folder
        $media_folder = $request->getQueryParams()['media_folder'];

        // Show sub-folders within $media_folder
        $subfolders = $request->getQueryParams()['subfolders']; // include|exclude

        $search_columns = ['multimedia_file_refn', 'descriptive_title'];

        $sort_columns = [
            0 => 'multimedia_file_refn',
            2 => new Expression('descriptive_title || multimedia_file_refn'),
        ];

        // Convert a row from the database into a row for datatables
        $callback = function (object $row): array {
            $tree  = $this->tree_service->find((int) $row->m_file);
            $media = Registry::mediaFactory()->make($row->m_id, $tree, $row->m_gedcom);
            assert($media instanceof Media);

            $is_http  = str_starts_with($row->multimedia_file_refn, 'http://');
            $is_https = str_starts_with($row->multimedia_file_refn, 'https://');

            if ($is_http || $is_https) {
                return [
                    '<a href="' . e($row->multimedia_file_refn) . '">' . e($row->multimedia_file_refn) . '</a>',
                    view('icons/mime', ['type' => Mime::DEFAULT_TYPE]),
                    $this->mediaObjectInfo($media),
                ];
            }

            try {
                $path = $row->media_folder . $row->multimedia_file_refn;

                try {
                    $mime_type = Registry::filesystem()->data()->mimeType($path);
                } catch (UnableToRetrieveMetadata $ex) {
                    $mime_type = Mime::DEFAULT_TYPE;
                }

                if (str_starts_with($mime_type, 'image/')) {
                    $url = route(AdminMediaFileThumbnail::class, ['path' => $path]);
                    $img = '<img src="' . e($url) . '">';
                } else {
                    $img = view('icons/mime', ['type' => $mime_type]);
                }

                $url = route(AdminMediaFileDownload::class, ['path' => $path]);
                $img = '<a href="' . e($url) . '" type="' . $mime_type . '" class="gallery">' . $img . '</a>';
            } catch (UnableToReadFile $ex) {
                $url = route(AdminMediaFileThumbnail::class, ['path' => $path]);
                $img = '<img src="' . e($url) . '">';
            }

            return [
                e($row->multimedia_file_refn),
                $img,
                $this->mediaObjectInfo($media),
            ];
        };

        switch ($files) {
            case 'local':
                $query = DB::table('media_file')
                    ->join('media', static function (JoinClause $join): void {
                        $join
                            ->on('media.m_file', '=', 'media_file.m_file')
                            ->on('media.m_id', '=', 'media_file.m_id');
                    })
                    ->leftJoin('gedcom_setting', static function (JoinClause $join): void {
                        $join
                            ->on('gedcom_setting.gedcom_id', '=', 'media.m_file')
                            ->where('setting_name', '=', 'MEDIA_DIRECTORY');
                    })
                    ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
                    ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
                    ->select([
                        'media.*',
                        'multimedia_file_refn',
                        'descriptive_title',
                        new Expression("COALESCE(setting_value, 'media/') AS media_folder"),
                    ]);

                $query->where(new Expression('setting_value || multimedia_file_refn'), 'LIKE', $media_folder . '%');

                if ($subfolders === 'exclude') {
                    $query->where(new Expression('setting_value || multimedia_file_refn'), 'NOT LIKE', $media_folder . '%/%');
                }

                return $this->datatables_service->handleQuery($request, $query, $search_columns, $sort_columns, $callback);

            case 'external':
                $query = DB::table('media_file')
                    ->join('media', static function (JoinClause $join): void {
                        $join
                            ->on('media.m_file', '=', 'media_file.m_file')
                            ->on('media.m_id', '=', 'media_file.m_id');
                    })
                    ->where(static function (Builder $query): void {
                        $query
                            ->where('multimedia_file_refn', 'LIKE', 'http://%')
                            ->orWhere('multimedia_file_refn', 'LIKE', 'https://%');
                    })
                    ->select([
                        'media.*',
                        'multimedia_file_refn',
                        'descriptive_title',
                        new Expression("'' AS media_folder"),
                    ]);

                return $this->datatables_service->handleQuery($request, $query, $search_columns, $sort_columns, $callback);

            case 'unused':
                // Which trees use which media folder?
                $media_trees = DB::table('gedcom')
                    ->join('gedcom_setting', 'gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                    ->where('setting_name', '=', 'MEDIA_DIRECTORY')
                    ->where('gedcom.gedcom_id', '>', 0)
                    ->pluck('setting_value', 'gedcom_name');

                $disk_files = $this->media_file_service->allFilesOnDisk($data_filesystem, $media_folder, $subfolders === 'include');
                $db_files   = $this->media_file_service->allFilesInDatabase($media_folder, $subfolders === 'include');

                // All unused files
                $unused_files = $disk_files->diff($db_files)
                    ->map(static function (string $file): array {
                        return (array) $file;
                    });

                $search_columns = [0];
                $sort_columns   = [0 => 0];

                $callback = function (array $row) use ($data_filesystem, $media_trees): array {
                    try {
                        $mime_type = $data_filesystem->mimeType($row[0]) ?: Mime::DEFAULT_TYPE;
                    } catch (FileSystemException | UnableToRetrieveMetadata $ex) {
                        $mime_type = Mime::DEFAULT_TYPE;
                    }


                    if (str_starts_with($mime_type, 'image/')) {
                        $url = route(AdminMediaFileThumbnail::class, ['path' => $row[0]]);
                        $img = '<img src="' . e($url) . '">';
                    } else {
                        $img = view('icons/mime', ['type' => $mime_type]);
                    }

                    $url = route(AdminMediaFileDownload::class, ['path' => $row[0]]);
                    $img = '<a href="' . e($url) . '">' . $img . '</a>';

                    // Form to create new media object in each tree
                    $create_form = '';
                    foreach ($media_trees as $media_tree => $media_directory) {
                        if (str_starts_with($row[0], $media_directory)) {
                            $tmp = substr($row[0], strlen($media_directory));
                            $create_form .=
                                '<p><a href="#" data-bs-toggle="modal" data-bs-backdrop="static" data-bs-target="#modal-create-media-from-file" data-file="' . e($tmp) . '" data-url="' . e(route(CreateMediaObjectFromFile::class, ['tree' => $media_tree])) . '" onclick="document.getElementById(\'modal-create-media-from-file-form\').action=this.dataset.url; document.getElementById(\'file\').value=this.dataset.file;">' . I18N::translate('Create') . '</a> — ' . e($media_tree) . '<p>';
                        }
                    }

                    $delete_link = '<p><a data-wt-confirm="' . I18N::translate('Are you sure you want to delete “%s”?', e($row[0])) . '" data-wt-post-url="' . e(route(DeletePath::class, [
                            'path' => $row[0],
                        ])) . '" href="#">' . I18N::translate('Delete') . '</a></p>';

                    return [
                        $this->mediaFileInfo($data_filesystem, $row[0]) . $delete_link,
                        $img,
                        $create_form,
                    ];
                };

                return $this->datatables_service->handleCollection($request, $unused_files, $search_columns, $sort_columns, $callback);

            default:
                throw new HttpNotFoundException();
        }
    }

    /**
     * Generate some useful information and links about a media object.
     *
     * @param Media $media
     *
     * @return string HTML
     */
    private function mediaObjectInfo(Media $media): string
    {
        $html = '<b><a href="' . e($media->url()) . '">' . $media->fullName() . '</a></b>' . '<br><i>' . e($media->getNote()) . '</i></br><br>';

        $linked = [];
        foreach ($media->linkedIndividuals('OBJE') as $link) {
            $linked[] = '<a href="' . e($link->url()) . '">' . $link->fullName() . '</a>';
        }
        foreach ($media->linkedFamilies('OBJE') as $link) {
            $linked[] = '<a href="' . e($link->url()) . '">' . $link->fullName() . '</a>';
        }
        foreach ($media->linkedSources('OBJE') as $link) {
            $linked[] = '<a href="' . e($link->url()) . '">' . $link->fullName() . '</a>';
        }
        foreach ($media->linkedNotes('OBJE') as $link) {
            // Invalid GEDCOM - you cannot link a NOTE to an OBJE
            $linked[] = '<a href="' . e($link->url()) . '">' . $link->fullName() . '</a>';
        }
        foreach ($media->linkedRepositories('OBJE') as $link) {
            // Invalid GEDCOM - you cannot link a REPO to an OBJE
            $linked[] = '<a href="' . e($link->url()) . '">' . $link->fullName() . '</a>';
        }
        foreach ($media->linkedLocations('OBJE') as $link) {
            $linked[] = '<a href="' . e($link->url()) . '">' . $link->fullName() . '</a>';
        }
        if ($linked !== []) {
            $html .= '<ul>';
            foreach ($linked as $link) {
                $html .= '<li>' . $link . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<div class="alert alert-danger">' . I18N::translate('There are no links to this media object.') . '</div>';
        }

        return $html;
    }

    /**
     * Generate some useful information and links about a media file.
     *
     * @param FilesystemOperator $data_filesystem
     * @param string             $file
     *
     * @return string
     */
    private function mediaFileInfo(FilesystemOperator $data_filesystem, string $file): string
    {
        $html = '<dl>';
        $html .= '<dt>' . I18N::translate('Filename') . '</dt>';
        $html .= '<dd>' . e($file) . '</dd>';

        try {
            $file_exists = $data_filesystem->fileExists($file);
        } catch (FilesystemException | UnableToCheckFileExistence $ex) {
            $file_exists = false;
        }

        if ($file_exists) {
            try {
                $size = $data_filesystem->fileSize($file);
            } catch (FilesystemException | UnableToRetrieveMetadata $ex) {
                $size = 0;
            }
            $size = intdiv($size + 1023, 1024); // Round up to next KB
            /* I18N: size of file in KB */
            $size = I18N::translate('%s KB', I18N::number($size));
            $html .= '<dt>' . I18N::translate('File size') . '</dt>';
            $html .= '<dd>' . $size . '</dd>';

            try {
                // This will work for local filesystems.  For remote filesystems, we will
                // need to copy the file locally to work out the image size.
                $imgsize = getimagesizefromstring($data_filesystem->read($file));
                $html .= '<dt>' . I18N::translate('Image dimensions') . '</dt>';
                /* I18N: image dimensions, width × height */
                $html .= '<dd>' . I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1'])) . '</dd>';
            } catch (FilesystemException | UnableToReadFile | Throwable $ex) {
                // Not an image, or not a valid image?
            }
        }

        $html .= '</dl>';

        return $html;
    }
}
