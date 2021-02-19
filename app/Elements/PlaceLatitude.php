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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Tree;

/**
 * PLACE_LATITUDE := {Size=5:8}
 * The value specifying the latitudinal coordinate of the place name. The
 * latitude coordinate is the direction North or South from the equator in
 * degrees and fraction of degrees carried out to give the desired accuracy.
 * For example: 18 degrees, 9 minutes, and 3.4 seconds North would be
 * formatted as N18.150944. Minutes and seconds are converted by dividing
 * the minutes value by 60 and the seconds value by 3600 and adding the
 * results together. This sum becomes the fractional part of the degree’s
 * value.
 */
class PlaceLatitude extends AbstractElement
{
    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        return '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" onchange="webtrees.reformatLatitude(this)" />';
    }
}
