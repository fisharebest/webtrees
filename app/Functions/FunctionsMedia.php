<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Functions;

/**
 * Class FunctionsMedia - common functions
 */
class FunctionsMedia
{
    /**
     * Convert raw values from php.ini file into bytes
     *
     * @param string $val
     *
     * @return int
     */
    public static function sizeToBytes($val)
    {
        if (!$val) {
            // no value was passed in, assume no limit and return -1
            $val = -1;
        }
        switch (substr($val, -1)) {
            case 'g':
            case 'G':
                return (int)$val * 1024 * 1024 * 1024;
            case 'm':
            case 'M':
                return (int)$val * 1024 * 1024;
            case 'k':
            case 'K':
                return (int)$val * 1024;
            default:
                return (int)$val;
        }
    }

    /**
     * Send a dummy image, where one could not be found or created.
     *
     * @param int    $status HTTP status code, such as 404 for "Not found"
     * @param string $message
     */
    public static function outputHttpStatusAsImage($status, $message)
    {
        $width      = 100;
        $height     = 100;
        $image      = imagecreatetruecolor($width, $height);
        $foreground = imagecolorallocate($image, 255, 0, 0);
        $background = imagecolorallocate($image, 224, 224, 224);

        // Draw a border
        imagefilledrectangle($image, 0, 0, $width, $height, $foreground);
        imagefilledrectangle($image, 1, 1, $width - 2, $height - 2, $background);

        // Draw text
        imagestring($image, 5, 5, 30, (string)$status, $foreground);
        imagestring($image, 5, 5, 50, $message, $foreground);


        http_response_code(404);
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }
}
