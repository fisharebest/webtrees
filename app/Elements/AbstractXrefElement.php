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

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tree;

use function e;
use function preg_match;

/**
 * Common behaviour for all XREF links
 */
class AbstractXrefElement extends AbstractElement
{
    /**
     * Escape @ signs in a GEDCOM export.
     *
     * @param string $value
     *
     * @return string
     */
    public function escape(string $value): string
    {
        return $value;
    }

    /**
     * Display the value of this type of element - convert XREFs to links.
     *
     * @param string $value
     * @param Tree   $tree
     * @param mixed  $factory We can type-hint this from PHP 7.4
     *
     * @return string
     */
    protected function valueXrefLink(string $value, Tree $tree, $factory): string
    {
        if (preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $value, $match)) {
            $record = $factory->make($match[1], $tree);

            if ($record instanceof GedcomRecord) {
                return '<a href="' . e($record->url()) . '">' . $record->fullName() . '</a>';
            }
        }

        return '<span class="error">' . e($value) . '</span>';
    }
}
