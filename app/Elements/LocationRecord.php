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
 * A level 0 location record
 */
class LocationRecord extends AbstractElement
{
    protected const array SUBTAGS = [
        'NAME'        => '1:M',
        'TYPE'        => '0:M',
        '_POST'       => '0:M',
        '_GOV'        => '0:1',
        'MAP'         => '0:1',
        '_MAIDENHEAD' => '0:1',
        'RELI'        => '0:1',
        'EVEN'        => '0:M',
        '_LOC'        => '0:M',
        '_DMGD'       => '0:M',
        '_AIDN'       => '0:M',
        'OBJE'        => '0:M',
        'NOTE'        => '0:M',
        'SOUR'        => '0:M',
        'CHAN'        => '0:1',
    ];
}
