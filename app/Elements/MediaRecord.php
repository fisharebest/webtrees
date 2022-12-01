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

namespace Fisharebest\Webtrees\Elements;

/**
 * A level 0 media record
 */
class MediaRecord extends AbstractElement
{
    protected const SUBTAGS = [
        'FILE' => '1:M',
        'REFN' => '0:M',
        'RIN'  => '0:1',
        'NOTE' => '0:M',
        'SOUR' => '0:M',
        'RESN' => '0:1',
        'CHAN' => '0:1',
    ];
}
