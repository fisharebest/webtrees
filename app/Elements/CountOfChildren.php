<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
 * COUNT_OF_CHILDREN := {Size=1:3}
 * The known number of children of this individual from all marriages or, if
 * subordinate to a family record, the reported number of children known to
 * belong to this family, regardless of whether the associated children are
 * represented in the corresponding structure. This is not necessarily the
 * count of children listed in a family structure.
 */
class CountOfChildren extends AbstractElement
{
    protected const SUBTAGS = [
        'TYPE'  => '0:1:?',
        'DATE'  => '0:1',
        'PLAC'  => '0:1:?',
        'ADDR'  => '0:1:?',
        'EMAIL' => '0:1:?',
        'WWW'   => '0:1:?',
        'PHON'  => '0:1:?',
        'FAX'   => '0:1:?',
        'CAUS'  => '0:1:?',
        'AGNC'  => '0:1:?',
        'RELI'  => '0:1:?',
        'NOTE'  => '0:M',
        'OBJE'  => '0:M',
        'SOUR'  => '0:M',
        'RESN'  => '0:1',
    ];

    /**
     * Display the value of this type of element.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function value(string $value, Tree $tree): string
    {
        return $this->valueNumeric($value);
    }
}
