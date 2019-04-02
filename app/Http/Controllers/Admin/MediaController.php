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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Services\DatatablesService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

/**
 * Controller for media administration.
 */
class MediaController extends AbstractAdminController
{
    // How many files to upload on one form.
    private const MAX_UPLOAD_FILES = 10;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $files        = $request->getQueryParams()['files'] ?? 'local'; // local|unused|external
        $media_folder = $request->getQueryParams()['media_folder'] ?? '';
        $subfolders   = $request->getQueryParams()['subfolders'] ?? 'include'; // include/exclude

        $media_folders = $this->allMediaFolders();

        // Preserve the pagination/filtering/sorting between requests, so that the
        // browser’s back button works. Pagination is dependent on the currently
        // selected folder.
        $table_id = md5($files . $media_folder . $subfolders);

        $title = I18N::translate('Manage media');

        return $this->viewResponse('admin/media', [
            'data_folder'   => WT_DATA_DIR,
            'files'         => $files,
            'media_folder'  => $media_folder,
            'media_folders' => $media_folders,
            'subfolders'    => $subfolders,
            'table_id'      => $table_id,
            'title'         => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $delete_file  = $request->getParsedBody()['file'];
        $media_folder = $request->getParsedBody()['folder'];

        // Only delete valid (i.e. unused) media files
        $disk_files = $this->allDiskFiles($media_folder, 'include');

        // Check file exists? Maybe it was already deleted or renamed.
        if (in_array($delete_file, $disk_files)) {
            $tmp = WT_DATA_DIR . $media_folder . $delete_file;
            try {
                unlink($tmp);
                FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', Html::filename($tmp)), 'info');
            } catch (Throwable $ex) {
                FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', Html::filename($tmp)) . '<hr><samp dir="ltr">' . $ex->getMessage() . '</samp>', 'danger');
            }
        }

        return response();
    }

    /**
     * @param ServerRequestInterface $request
     * @param DatatablesService      $datatables_service
     *
     * @return ResponseInterface
     */
    public function data(ServerRequestInterface $request, DatatablesService $datatables_service): ResponseInterface
    {
        $files  = $request->getQueryParams()['files']; // local|external|unused
        $search = $request->getQueryParams()['search'];
        $search = $search['value'];
        $start  = (int) $request->getQueryParams()['start'];
        $length = (int) $request->getQueryParams()['length'];

        // Files within this folder
        $media_folder = $request->getQueryParams()['media_folder'];

        // subfolders within $media_path
        $subfolders = $request->getQueryParams()['subfolders']; // include|exclude

        $search_columns = ['multimedia_file_refn', 'descriptive_title'];

        $sort_columns = [
            0 => 'multimedia_file_refn',
            2 => DB::raw('descriptive_title || multimedia_file_refn'),
        ];

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
                    ->select(['media.*', 'multimedia_file_refn', 'descriptive_title']);

                $query->where(DB::raw('setting_value || multimedia_file_refn'), 'LIKE', $media_folder . '%');

                if ($subfolders === 'exclude') {
                    $query->where(DB::raw('setting_value || multimedia_file_refn'), 'NOT LIKE', $media_folder . '%/%');
                }

                return $datatables_service->handle($request, $query, $search_columns, $sort_columns, function (stdClass $row): array {
                    /** @var Media $media */
                    $media = Media::rowMapper()($row);

                    $media_files = $media->mediaFiles()
                        ->filter(static function (MediaFile $media_file) use ($row): bool {
                            return $media_file->filename() == $row->multimedia_file_refn;
                        })
                        ->map(static function (MediaFile $media_file): string {
                            return $media_file->displayImage(150, 150, '', []);
                        })
                        ->implode('');

                    return [
                        $row->multimedia_file_refn,
                        $media_files,
                        $this->mediaObjectInfo($media),
                    ];
                });

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

                return $datatables_service->handle($request, $query, $search_columns, $sort_columns, function (stdClass $row): array {
                    /** @var Media $media */
                    $media = Media::rowMapper()($row);

                    $media_files = $media->mediaFiles()
                        ->filter(static function (MediaFile $media_file) use ($row): bool {
                            return $media_file->filename() === $row->multimedia_file_refn;
                        })
                        ->map(static function (MediaFile $media_file): string {
                            return $media_file->displayImage(150, 150, '', []);
                        })
                        ->implode('');

                    return [
                        $row->multimedia_file_refn,
                        $media_files,
                        $this->mediaObjectInfo($media),
                    ];
                });

            case 'unused':
                // Which trees use which media folder?
                $media_trees = DB::table('gedcom')
                    ->join('gedcom_setting', 'gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
                    ->where('setting_name', '=', 'MEDIA_DIRECTORY')
                    ->where('gedcom.gedcom_id', '>', 0)
                    ->pluck('setting_value', 'gedcom_name');

                $disk_files = $this->allDiskFiles($media_folder, $subfolders);
                $db_files   = $this->allMediaFiles($media_folder, $subfolders);

                // All unused files
                $unused_files = array_diff($disk_files, $db_files);
                $recordsTotal = count($unused_files);

                // Filter unused files
                if ($search) {
                    $unused_files = array_filter($unused_files, static function (string $x) use ($search): bool {
                        return strpos($x, $search) !== false;
                    });
                }
                $recordsFiltered = count($unused_files);

                // Sort files - only option is column 0
                sort($unused_files);
                $order = $request->getQueryParams()['order'];
                if ($order && $order[0]['dir'] === 'desc') {
                    $unused_files = array_reverse($unused_files);
                }

                // Paginate unused files
                $unused_files = array_slice($unused_files, $start, $length);

                $data = [];
                foreach ($unused_files as $unused_file) {
                    $imgsize = getimagesize(WT_DATA_DIR . $media_folder . $unused_file);
                    // We can’t create a URL (not in public_html) or use the media firewall (no such object)
                    if ($imgsize === false) {
                        $img = '-';
                    } else {
                        $url = route('unused-media-thumbnail', [
                            'folder' => $media_folder,
                            'file'   => $unused_file,
                            'w'      => 100,
                            'h'      => 100,
                        ]);
                        $img = '<img src="' . e($url) . '">';
                    }

                    // Form to create new media object in each tree
                    $create_form = '';
                    foreach ($media_trees as $media_tree => $media_directory) {
                        if (Str::startsWith($media_folder . $unused_file, $media_directory)) {
                            $tmp         = substr($media_folder . $unused_file, strlen($media_directory));
                            $create_form .=
                                '<p><a href="#" data-toggle="modal" data-target="#modal-create-media-from-file" data-file="' . e($tmp) . '" data-tree="' . e($media_tree) . '" onclick="document.getElementById(\'file\').value=this.dataset.file; document.getElementById(\'ged\').value=this.dataset.tree;">' . I18N::translate('Create') . '</a> — ' . e($media_tree) . '<p>';
                        }
                    }

                    $delete_link = '<p><a data-confirm="' . I18N::translate('Are you sure you want to delete “%s”?', e($unused_file)) . '" data-url="' . e(route('admin-media-delete', [
                            'file'   => $unused_file,
                            'folder' => $media_folder,
                        ])) . '" onclick="if (confirm(this.dataset.confirm)) jQuery.post(this.dataset.url, function (){document.location.reload();})" href="#">' . I18N::translate('Delete') . '</a></p>';

                    $data[] = [
                        $this->mediaFileInfo($media_folder, $unused_file) . $delete_link,
                        $img,
                        $create_form,
                    ];
                }
                break;

            default:
                throw new BadRequestHttpException();
        }

        // See http://www.datatables.net/usage/server-side
        return response([
            'draw'            => (int) $request->getQueryParams()['draw'],
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * @return ResponseInterface
     */
    public function upload(): ResponseInterface
    {
        $media_folders = $this->allMediaFolders();

        $filesize = ini_get('upload_max_filesize');
        if (empty($filesize)) {
            $filesize = '2M';
        }

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
        $all_folders = $this->allMediaFolders();

        for ($i = 1; $i < self::MAX_UPLOAD_FILES; $i++) {
            if (!empty($_FILES['mediafile' . $i]['name'])) {
                $folder   = $request->get('folder' . $i, '');
                $filename = $request->get('filename' . $i, '');

                // If no filename specified, use the original filename.
                if ($filename === '') {
                    $filename = $_FILES['mediafile' . $i]['name'];
                }

                // Validate the folder
                if (!$all_folders->contains($folder)) {
                    break;
                }

                // Validate the filename.
                $filename = str_replace('\\', '/', $filename);
                $filename = trim($filename, '/');

                if (strpos('/' . $filename, '/../') !== false) {
                    FlashMessages::addMessage('Folder names are not allowed to include “../”');
                    continue;
                }

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

                // The new filename may have created a new sub-folder.
                $full_path = WT_DATA_DIR . $folder . $filename;
                $folder    = dirname($full_path);

                // Make sure the media folder exists
                if (!is_dir($folder)) {
                    if (File::mkdir($folder)) {
                        FlashMessages::addMessage(I18N::translate('The folder %s has been created.', Html::filename($folder)), 'info');
                    } else {
                        FlashMessages::addMessage(I18N::translate('The folder %s does not exist, and it could not be created.', Html::filename($folder)), 'danger');
                        continue;
                    }
                }

                if (file_exists($full_path)) {
                    FlashMessages::addMessage(I18N::translate('The file %s already exists. Use another filename.', $full_path, 'error'));
                    continue;
                }

                // Now copy the file to the correct location.
                if (move_uploaded_file($_FILES['mediafile' . $i]['tmp_name'], $full_path)) {
                    FlashMessages::addMessage(I18N::translate('The file %s has been uploaded.', Html::filename($full_path)), 'success');
                    Log::addMediaLog('Media file ' . $full_path . ' uploaded');
                } else {
                    FlashMessages::addMessage(I18N::translate('There was an error uploading your file.') . '<br>' . Functions::fileUploadErrorText($_FILES['mediafile' . $i]['error']), 'danger');
                }
            }
        }

        $url = route('admin-media-upload');

        return redirect($url);
    }

    /**
     * Generate a list of all folders from all the trees.
     *
     * @return Collection
     * @return string[]
     */
    private function allMediaFolders(): Collection
    {
        $base_folders = DB::table('gedcom_setting')
            ->where('setting_name', '=', 'MEDIA_DIRECTORY')
            ->select(DB::raw("setting_value || 'dummy.jpeg' AS path"));

        return DB::table('media_file')
            ->join('gedcom_setting', 'gedcom_id', '=', 'm_file')
            ->where('setting_name', '=', 'MEDIA_DIRECTORY')
            ->where('multimedia_file_refn', 'LIKE', '%/%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->select(DB::raw('setting_value || multimedia_file_refn AS path'))
            ->union($base_folders)
            ->pluck('path')
            ->map(static function (string $path): string {
                return dirname($path) . '/';
            })
            ->unique()
            ->sort()
            ->mapWithKeys(static function (string $path): array {
                return [$path => $path];
            });
    }

    /**
     * Search a folder (and optional subfolders) for filenames that match a search pattern.
     *
     * @param string $dir
     * @param bool   $recursive
     *
     * @return string[]
     */
    private function scanFolders(string $dir, bool $recursive): array
    {
        $files = [];

        // $dir comes from the database. The actual folder may not exist.
        if (is_dir($dir)) {
            foreach (scandir($dir, SCANDIR_SORT_NONE) as $path) {
                if (is_dir($dir . $path)) {
                    // What if there are user-defined subfolders “thumbs” or “watermarks”?
                    if ($path !== '.' && $path !== '..' && $path !== 'thumbs' && $path !== 'watermark' && $recursive) {
                        foreach ($this->scanFolders($dir . $path . '/', $recursive) as $subpath) {
                            $files[] = $path . '/' . $subpath;
                        }
                    }
                } else {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }

    /**
     * Fetch a list of all files on disk
     *
     * @param string $media_folder Location of root folder
     * @param string $subfolders   Include or exclude subfolders
     *
     * @return string[]
     */
    private function allDiskFiles(string $media_folder, string $subfolders): array
    {
        return $this->scanFolders(WT_DATA_DIR . $media_folder, $subfolders === 'include');
    }

    /**
     * Fetch a list of all files on in the database.
     *
     * @param string $media_folder
     * @param string $subfolders
     *
     * @return string[]
     */
    private function allMediaFiles(string $media_folder, string $subfolders): array
    {
        $query = DB::table('media_file')
            ->join('gedcom_setting', 'gedcom_id', '=', 'm_file')
            ->where('setting_name', '=', 'MEDIA_DIRECTORY')
            ->where('multimedia_file_refn', 'LIKE', '%/%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->where(DB::raw('setting_value || multimedia_file_refn'), 'LIKE', $media_folder . '%')
            ->select(DB::raw('setting_value || multimedia_file_refn AS path'))
            ->orderBy('path');

        if ($subfolders === 'exclude') {
            $query->where('path', 'NOT LIKE', $media_folder . '%/%');
        }

        return $query->pluck('path')->all();
    }

    /**
     * Generate some useful information and links about a media file.
     *
     * @param string $media_folder
     * @param string $file
     *
     * @return string
     */
    private function mediaFileInfo(string $media_folder, string $file): string
    {
        $html = '<dl>';
        $html .= '<dt>' . I18N::translate('Filename') . '</dt>';
        $html .= '<dd>' . e($file) . '</dd>';

        $full_path = WT_DATA_DIR . $media_folder . $file;
        try {
            $size = filesize($full_path);
            $size = intdiv($size + 1023, 1024); // Round up to next KB
            /* I18N: size of file in KB */
            $size = I18N::translate('%s KB', I18N::number($size));
            $html .= '<dt>' . I18N::translate('File size') . '</dt>';
            $html .= '<dd>' . $size . '</dd>';

            try {
                $imgsize = getimagesize($full_path);
                $html    .= '<dt>' . I18N::translate('Image dimensions') . '</dt>';
                /* I18N: image dimensions, width × height */
                $html .= '<dd>' . I18N::translate('%1$s × %2$s pixels', I18N::number($imgsize['0']), I18N::number($imgsize['1'])) . '</dd>';
            } catch (Throwable $ex) {
                // Not an image, or not a valid image?
            }

            $html .= '</dl>';
        } catch (Throwable $ex) {
            // Not a file?  Not an image?
        }

        return $html;
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
        if (!empty($linked)) {
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
}
