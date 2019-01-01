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

namespace Fisharebest\Webtrees;

use Throwable;

/**
 * File manipulation utilities.
 */
class File
{
    /**
     * Create a folder, and sub-folders, if it does not already exist
     *
     * @param string $path
     *
     * @return bool Does the folder now exist
     */
    public static function mkdir($path)
    {
        if (is_dir($path)) {
            return true;
        }

        if (dirname($path) && !is_dir(dirname($path))) {
            self::mkdir(dirname($path));
        }
        try {
            mkdir($path);

            return true;
        } catch (Throwable $ex) {
            return false;
        }
    }
}
