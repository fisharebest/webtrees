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

namespace Fisharebest\Webtrees\Elements;

/**
 * A level 0 source record
 */
class SourceRecord extends AbstractElement
{
    protected const SUBTAGS = [
        'TITL' => '0:1',
        'ABBR' => '0:1',
        'AUTH' => '0:1',
        'PUBL' => '0:1',
        'REPO' => '0:M',
        'TEXT' => '0:1',
        'OBJE' => '0:M',
        'NOTE' => '0:M',
        'DATA' => '0:1',
        'REFN' => '0:1',
        'RIN'  => '0:1',
        'CHAN' => '0:1',
    ];
}
