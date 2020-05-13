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

namespace Fisharebest\Webtrees;

/**
 * A list of known or supported mime types
 */
class Mime
{
    public const DEFAULT_TYPE = 'application/octet-stream';

    // Convert extension to mime-type
    public const TYPES = [
        'bmp'  => 'image/bmp',
        'css'  => 'text/css',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'ged'  => 'text/x-gedcom',
        'gif'  => 'image/gif',
        'flac' => 'audio/flac',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'ico'  => 'image/x-icon',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'mov'  => 'video/quicktime',
        'mp3'  => 'audio/mpeg',
        'mp4'  => 'video/mp4',
        'oga'  => 'audio/ogg',
        'ogg'  => 'audio/ogg',
        'ogv'  => 'video/ogg',
        'pdf'  => 'application/pdf',
        'png'  => 'image/png',
        'rar'  => 'application/x-rar-compressed',
        'svg'  => 'image/svg',
        'swf'  => 'application/x-shockwave-flash',
        'tif'  => 'image/tiff',
        'tiff' => 'image/tiff',
        'txt'  => 'text/plain',
        'wmv'  => 'video/x-ms-wmv',
        'xls'  => 'application/vnd-ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml'  => 'application/xml',
        'zip'  => 'application/zip',
    ];
}
