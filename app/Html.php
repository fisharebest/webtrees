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
declare(strict_types=1);

namespace Fisharebest\Webtrees;

/**
 * Class Html - Add HTML markup to elements consistently.
 */
class Html
{
    /**
     * Convert an array of HTML attributes to an HTML string.
     *
     * @param mixed[] $attributes
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
                $html[] = e($key) . '="' . (string) $value . '"';
            } elseif ($value !== false) {
                $html[] = e($key);
            }
        }

        return implode(' ', $html);
    }

    /**
     * Encode a URL.
     *
     * @param string $path
     * @param array  $data
     *
     * @return string
     */
    public static function url($path, array $data): string
    {
        $path = strtr($path, ' ', '%20');

        return $path . '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Filenames are (almost?) always LTR, even on RTL systems.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function filename($filename): string
    {
        return '<samp class="filename" dir="ltr">' . e($filename) . '</samp>';
    }
}
