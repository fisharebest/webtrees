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

namespace Fisharebest\Webtrees;

/**
 * A list of known or supported mime types
 */
class Mime
{
    public const DEFAULT_TYPE = 'application/octet-stream';

    // Convert extension to mime-type
    public const TYPES = [
        'BMP'  => 'image/bmp',
        'CSS'  => 'text/css',
        'DOC'  => 'application/msword',
        'DOCX' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'GED'  => 'text/vnd.familysearch.gedcom',
        'GIF'  => 'image/gif',
        'FLAC' => 'audio/flac',
        'HEIF' => 'image/heif',
        'HTM'  => 'text/html',
        'HTML' => 'text/html',
        'ICO'  => 'image/x-icon',
        'JPE'  => 'image/jpeg',
        'JPEG' => 'image/jpeg',
        'JPG'  => 'image/jpeg',
        'JS'   => 'application/javascript',
        'JSON' => 'application/json',
        'MOV'  => 'video/quicktime',
        'M4V'  => 'video/mp4',
        'MKV'  => 'video/x-matroska',
        'MP3'  => 'audio/mpeg',
        'MP4'  => 'video/mp4',
        'OGA'  => 'audio/ogg',
        'OGG'  => 'audio/ogg',
        'OGV'  => 'video/ogg',
        'PDF'  => 'application/pdf',
        'PNG'  => 'image/png',
        'RAR'  => 'application/x-rar-compressed',
        'SVG'  => 'image/svg+xml',
        'TIF'  => 'image/tiff',
        'TIFF' => 'image/tiff',
        'TXT'  => 'text/plain',
        'WEBM' => 'video/webm',
        'WEBP' => 'image/webp',
        'WMV'  => 'video/x-ms-wmv',
        'XLS'  => 'application/vnd-ms-excel',
        'XLSX' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'XML'  => 'application/xml',
        'ZIP'  => 'application/zip',
    ];
}
