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

use function preg_match;
use function strpos;
use function trim;

/**
 * MULTIMEDIA_FILE_REFERENCE := {Size=1:30}
 * A complete local or remote file reference to the auxiliary data to be linked
 * to the GEDCOM context. Remote reference would include a network address
 * where the multimedia data may be obtained.
 */
class MultimediaFileReference extends AbstractElement
{
    protected const SUBTAGS = [
        'FORM' => '0:1',
        'TITL' => '0:1',
    ];

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        // Leading/trailing/multiple spaces are valid in filenames.
        return strtr($value, ["\t" => '', "\r" => '', "\n" => '']);
    }

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

        if (preg_match(static::REGEX_URL, $canonical)) {
            return '<a href="' . e($canonical) . '">' . e($canonical) . '</a>';
        }

        return parent::value($value, $tree);
    }
}
