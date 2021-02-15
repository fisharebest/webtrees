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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;

/**
 * <PRIORITY>:= integer 0 to 8, defined values: 0 = high, 5 = medium, 8 = low
 */
class ResearchTaskPriority extends AbstractElement
{
    /**
     * Create a default value for this element.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function default(Tree $tree): string
    {
        return '4';
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            '' => '',
            0 => I18N::translate(I18N::number(0)),
            1 => I18N::translate(I18N::number(1)),
            2 => I18N::translate(I18N::number(2)),
            3 => I18N::translate(I18N::number(3)),
            4 => I18N::translate(I18N::number(4)),
            5 => I18N::translate(I18N::number(5)),
            6 => I18N::translate(I18N::number(6)),
            7 => I18N::translate(I18N::number(7)),
            8 => I18N::translate(I18N::number(8)),
        ];
    }
}
