<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Exceptions;

use Fisharebest\Webtrees\I18N;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

use function e;

use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_EXTENSION;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_PARTIAL;

/**
 * Exception thrown when a file upload fails.
 */
class FileUploadException extends RuntimeException
{
    /**
     * GedcomErrorException constructor.
     *
     * @param UploadedFileInterface|null $uploaded_file
     */
    public function __construct(?UploadedFileInterface $uploaded_file)
    {
        if ($uploaded_file === null) {
            parent::__construct(I18N::translate('No file was received. Please try again.'));

            return;
        }

        switch ($uploaded_file->getError()) {
            case UPLOAD_ERR_OK:
                $message = I18N::translate('File successfully uploaded');
                break;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                // I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
                $message = I18N::translate('The uploaded file exceeds the allowed size.');
                break;

            case UPLOAD_ERR_PARTIAL:
                // I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
                $message = I18N::translate('The file was only partially uploaded. Please try again.');
                break;

            case UPLOAD_ERR_NO_FILE:
                // I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
                $message = I18N::translate('No file was received. Please try again.');
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                // I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
                $message = I18N::translate('The PHP temporary folder is missing.');
                break;

            case UPLOAD_ERR_CANT_WRITE:
                // I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
                $message = I18N::translate('PHP failed to write to disk.');
                break;

            case UPLOAD_ERR_EXTENSION:
                // I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
                $message = I18N::translate('PHP blocked the file because of its extension.');
                break;

            default:
                $message = 'Error: ' . $uploaded_file->getError();
                break;
        }

        $filename = $uploaded_file->getClientFilename() ?? '????????.???';

        $message =
            I18N::translate('There was an error uploading your file.') .
            '<br>' .
            I18N::translate('%1$s: %2$s', I18N::translate('Filename'), e($filename)) .
            '<br>' .
            $message;

        parent::__construct($message);
    }
}
