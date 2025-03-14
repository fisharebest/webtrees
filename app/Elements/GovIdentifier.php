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

use Fisharebest\Webtrees\Tree;

use function e;
use function rawurlencode;

/**
 * A custom field used in _LOC records
 */
class GovIdentifier extends AbstractElement
{
    protected const string EXTERNAL_URL = 'https://gov.genealogy.net/item/show/';

    protected const int MAXIMUM_LENGTH = 14;

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
        $canonical = $this->canonical($value);
        $url       = static::EXTERNAL_URL . rawurlencode($canonical);

        return '<a dir="ltr" href="' . e($url) . '" rel="nofollow">' . e($canonical) . '</a>';
    }
}
