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
 * Heredis custom tag *:*:_RECH - Research data of an event.
 */
class HeredisRechElement extends EmptyElement
{
    protected const array SUBTAGS = [
        '_PROJ' => '0:1',
        'TYPE'  => '0:1',
        'PLAC'  => '0:1',
        'DATE'  => '0:1',
        'REFN'  => '0:1',
        'WWW'   => '0:1',
        'NOTE'  => '0:1',
    ];

    /**
     * Should we collapse the children of this element when editing?
     * FIXME: label is not shown, it does not have a control to collapse
     *
     * @return bool
     */
    public function collapseChildren(): bool
    {
        return true;
    }
}
