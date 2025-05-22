<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

/**
 * A level 0 header record
 */
class HeaderRecord extends AbstractElement
{
    protected const array SUBTAGS = [
        'SOUR' => '1:1',
        'DEST' => '0:1',
        'DATE' => '0:1',
        'SUBM' => '1:1',
        'SUBN' => '0:1',
        'FILE' => '0:1',
        'COPR' => '0:1',
        'GEDC' => '0:1',
        'CHAR' => '0:1',
        'LANG' => '0:1',
        'PLAC' => '0:1',
        'NOTE' => '0:1',
    ];
}
