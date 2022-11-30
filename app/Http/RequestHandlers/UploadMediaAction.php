<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Exceptions\FileUploadException;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToWriteFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function preg_match;
use function redirect;
use function route;
use function str_replace;
use function substr;
use function trim;

use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

/**
 * Manage media from the control panel.
 */
class UploadMediaAction implements RequestHandlerInterface
{
    private MediaFileService $media_file_service;

    /**
     * MediaController constructor.
     *
     * @param MediaFileService $media_file_service
     */
    public function __construct(MediaFileService $media_file_service)
    {
        $this->media_file_service = $media_file_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();
        $all_folders     = $this->media_file_service->allMediaFolders($data_filesystem);

        foreach ($request->getUploadedFiles() as $key => $uploaded_file) {
            if ($uploaded_file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($uploaded_file->getError() !== UPLOAD_ERR_OK) {
                throw new FileUploadException($uploaded_file);
            }

            $key      = substr($key, 9);
            $folder   = Validator::parsedBody($request)->string('folder' . $key);
            $filename = Validator::parsedBody($request)->string('filename' . $key);

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

            try {
                $file_exists = $data_filesystem->fileExists($path);
            } catch (FilesystemException | UnableToCheckFileExistence) {
                $file_exists = false;
            }

            if ($file_exists) {
                FlashMessages::addMessage(I18N::translate('The file %s already exists. Use another filename.', $path, 'error'));
                continue;
            }

            // Now copy the file to the correct location.
            try {
                $data_filesystem->writeStream($path, $uploaded_file->getStream()->detach());
                FlashMessages::addMessage(I18N::translate('The file %s has been uploaded.', Html::filename($path)), 'success');
                Log::addMediaLog('Media file ' . $path . ' uploaded');
            } catch (FilesystemException | UnableToWriteFile $ex) {
                FlashMessages::addMessage(I18N::translate('There was an error uploading your file.') . '<br>' . e($ex->getMessage()), 'danger');
            }
        }

        $url = route(UploadMediaPage::class);

        return redirect($url);
    }
}
