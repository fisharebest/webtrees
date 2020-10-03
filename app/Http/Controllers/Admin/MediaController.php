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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Http\RequestHandlers\AdminMediaFileDownload;
use Fisharebest\Webtrees\Http\RequestHandlers\AdminMediaFileThumbnail;
use Fisharebest\Webtrees\Http\RequestHandlers\DeletePath;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use stdClass;
use Throwable;

use function assert;
use function e;
use function getimagesize;
use function ini_get;
use function intdiv;
use function preg_match;
use function redirect;
use function route;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;
use function trim;
use function view;

use const UPLOAD_ERR_OK;

/**
 * Controller for media administration.
 */
class MediaController extends AbstractAdminController
{
    // How many files to upload on one form.
    private const MAX_UPLOAD_FILES = 10;

    /** @var DatatablesService */
    private $datatables_service;

    /** @var MediaFileService */
    private $media_file_service;

    /** @var TreeService */
    private $tree_service;

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
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem      = Registry::filesystem()->data();
        $data_filesystem_name = Registry::filesystem()->dataName();

        $files         = $request->getQueryParams()['files'] ?? 'local'; // local|unused|external
        $subfolders    = $request->getQueryParams()['subfolders'] ?? 'include'; // include|exclude
        $media_folders = $this->media_file_service->allMediaFolders($data_filesystem);
        $media_folder  = $request->getQueryParams()['media_folder'] ?? $media_folders->first() ?? '';

        $title = I18N::translate('Manage media');

        return $this->viewResponse('admin/media', [
            'data_folder'   => $data_filesystem_name,
            'files'         => $files,
            'media_folder'  => $media_folder,
            'media_folders' => $media_folders,
            'subfolders'    => $subfolders,
            'title'         => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        return redirect(route('admin-media', [
            'files'        => $params['files'],
            'media_folder' => $params['media_folder'] ?? '',
            'subfolders'   => $params['subfolders'] ?? 'include',
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function data(ServerRequestInterface $request): ResponseInterface
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
        $callback = function (stdClass $row): array {
            $tree = $this->tree_service->find((int) $row->m_file);
            $media = Registry::mediaFactory()->make($row->m_id, $tree, $row->m_gedcom);
            assert($media instanceof Media);

            $path = $row->media_folder . $row->multimedia_file_refn;

            try {
                $mime_type = Registry::filesystem()->data()->getMimeType($path) ?: Mime::DEFAULT_TYPE;

                if (str_starts_with($mime_type, 'image/')) {
                    $url = route(AdminMediaFileThumbnail::class, ['path' => $path]);
                    $img = '<img src="' . e($url) . '">';
                } else {
                    $img = view('icons/mime', ['type' => $mime_type]);
                }

                $url = route(AdminMediaFileDownload::class, ['path' => $path]);
                $img = '<a href="' . e($url) . '" type="' . $mime_type . '" class="gallery">' . $img . '</a>';
            } catch (FileNotFoundException $ex) {
                $url = route(AdminMediaFileThumbnail::class, ['path' => $path]);
                $img = '<img src="' . e($url) . '">';
            }

            return [
                $row->multimedia_file_refn,
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
                    ->join('gedcom_setting', 'gedcom_id', '=', 'media.m_file')
                    ->where('setting_name', '=', 'MEDIA_DIRECTORY')
                    ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
                    ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
                    ->select(['media.*', 'multimedia_file_refn', 'descriptive_title', 'setting_value AS media_folder']);

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
                    ->select(['media.*', 'multimedia_file_refn', 'descriptive_title']);

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
                    $mime_type = $data_filesystem->getMimeType($row[0]) ?: Mime::DEFAULT_TYPE;

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
                            $tmp         = substr($row[0], strlen($media_directory));
                            $create_form .=
                                '<p><a href="#" data-toggle="modal" data-backdrop="static" data-target="#modal-create-media-from-file" data-file="' . e($tmp) . '" data-url="' . e(route('create-media-from-file', ['tree' => $media_tree])) . '" onclick="document.getElementById(\'modal-create-media-from-file-form\').action=this.dataset.url; document.getElementById(\'file\').value=this.dataset.file;">' . I18N::translate('Create') . '</a> — ' . e($media_tree) . '<p>';
                        }
                    }

                    $delete_link = '<p><a data-confirm="' . I18N::translate('Are you sure you want to delete “%s”?', e($row[0])) . '" data-post-url="' . e(route(DeletePath::class, [
                            'path'   => $row[0],
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
     * @param FilesystemInterface $data_filesystem
     * @param string              $file
     *
     * @return string
     */
    private function mediaFileInfo(FilesystemInterface $data_filesystem, string $file): string
    {
        $html = '<dl>';
        $html .= '<dt>' . I18N::translate('Filename') . '</dt>';
        $html .= '<dd>' . e($file) . '</dd>';

        if ($data_filesystem->has($file)) {
            $size = $data_filesystem->getSize($file);
            $size = intdiv($size + 1023, 1024); // Round up to next KB
            /* I18N: size of file in KB */
            $size = I18N::translate('%s KB', I18N::number($size));
            $html .= '<dt>' . I18N::translate('File size') . '</dt>';
            $html .= '<dd>' . $size . '</dd>';

            try {
                // This will work for local filesystems.  For remote filesystems, we will
                // need to copy the file locally to work out the image size.
                $imgsize = getimagesize(Webtrees::DATA_DIR .  $file);
                $html    .= '<dt>' . I18N::translate('Image dimensions') . '</dt>';
                /* I18N: image dimensions, width × height */
                $html .= '<dd>' . I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1'])) . '</dd>';
            } catch (Throwable $ex) {
                // Not an image, or not a valid image?
            }
        }

        $html .= '</dl>';

        return $html;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function upload(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $media_folders = $this->media_file_service->allMediaFolders($data_filesystem);

        $filesize = ini_get('upload_max_filesize') ?: '2M';

        $title = I18N::translate('Upload media files');

        return $this->viewResponse('admin/media-upload', [
            'max_upload_files' => self::MAX_UPLOAD_FILES,
            'filesize'         => $filesize,
            'media_folders'    => $media_folders,
            'title'            => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function uploadAction(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $params = (array) $request->getParsedBody();

        $all_folders = $this->media_file_service->allMediaFolders($data_filesystem);

        foreach ($request->getUploadedFiles() as $key => $uploaded_file) {
            assert($uploaded_file instanceof UploadedFileInterface);
            if ($uploaded_file->getClientFilename() === '') {
                continue;
            }
            if ($uploaded_file->getError() !== UPLOAD_ERR_OK) {
                FlashMessages::addMessage(Functions::fileUploadErrorText($uploaded_file->getError()), 'danger');
                continue;
            }
            $key = substr($key, 9);

            $folder   = $params['folder' . $key];
            $filename = $params['filename' . $key];

            // If no filename specified, use the original filename.
            if ($filename === '') {
                $filename = $uploaded_file->getClientFilename();
            }

            // Validate the folder
            if (!$all_folders->contains($folder)) {
                break;
            }

            // Validate the filename.
            $filename = str_replace('\\', '/', $filename);
            $filename = trim($filename, '/');

            if (preg_match('/([:])/', $filename, $match)) {
                // Local media files cannot contain certain special characters, especially on MS Windows
                FlashMessages::addMessage(I18N::translate('Filenames are not allowed to contain the character “%s”.', $match[1]));
                continue;
            }

            if (preg_match('/(\.(php|pl|cgi|bash|sh|bat|exe|com|htm|html|shtml))$/i', $filename, $match)) {
                // Do not allow obvious script files.
                FlashMessages::addMessage(I18N::translate('Filenames are not allowed to have the extension “%s”.', $match[1]));
                continue;
            }

            $path = $folder . $filename;

            if ($data_filesystem->has($path)) {
                FlashMessages::addMessage(I18N::translate('The file %s already exists. Use another filename.', $path, 'error'));
                continue;
            }

            // Now copy the file to the correct location.
            try {
                $data_filesystem->writeStream($path, $uploaded_file->getStream()->detach());
                FlashMessages::addMessage(I18N::translate('The file %s has been uploaded.', Html::filename($path)), 'success');
                Log::addMediaLog('Media file ' . $path . ' uploaded');
            } catch (Throwable $ex) {
                FlashMessages::addMessage(I18N::translate('There was an error uploading your file.') . '<br>' . e($ex->getMessage()), 'danger');
            }
        }

        $url = route('admin-media-upload');

        return redirect($url);
    }
}
