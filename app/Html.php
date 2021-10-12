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

namespace Fisharebest\Webtrees;

use function e;
use function http_build_query;
use function implode;
use function is_int;
use function is_string;
use function strtr;

use const PHP_QUERY_RFC3986;

/**
 * Class Html - Add HTML markup to elements consistently.
 */
class Html
{
    /**
     * Convert an array of HTML attributes to an HTML string.
     *
     * @param array<string,string|int|bool> $attributes
     *
     * @return string
     */
    public static function attributes(array $attributes): string
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $html[] = e($key) . '="' . e($value) . '"';
            } elseif (is_int($value)) {
                $html[] = e($key) . '="' . $value . '"';
            } elseif ($value === true) {
                $html[] = e($key) . '="' . e($key) . '"';
            }
        }

        return implode(' ', $html);
    }

    /**
     * Encode a URL.
     *
     * @param string                            $path
     * @param array<bool|int|string|array|null> $data
     *
     * @return string
     */
    public static function url(string $path, array $data): string
    {
        $path = strtr($path, [' ' => '%20']);

        if ($data !== []) {
            $path .= '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        }

        return $path;
    }

    /**
     * Filenames are (almost?) always LTR, even on RTL systems.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function filename(string $filename): string
    {
        return '<samp class="filename" dir="ltr">' . e($filename) . '</samp>';
    }
}
