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

use function preg_replace;
use function trim;

/**
 * PLACE_HIERARCHY := {Size=1:120}
 * This shows the jurisdictional entities that are named in a sequence from the
 * lowest to the highest jurisdiction. The jurisdictions are separated by
 * commas, and any jurisdiction's name that is missing is still accounted for
 * by a comma. When a PLAC.FORM structure is included in the HEADER of a GEDCOM
 * transmission, it implies that all place names follow this jurisdictional
 * format and each jurisdiction is accounted for by a comma, whether the name
 * is known or not. When the PLAC.FORM is subordinate to an event, it
 * temporarily overrides the implications made by the PLAC.FORM structure
 * stated in the HEADER. This usage is not common and, therefore, not
 * encouraged. It should only be used when a system has over-structured its
 * place-names.
 */
class PlaceHierarchy extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 120;

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = parent::canonical($value);
        $value = preg_replace('/[, ]*,[, ]*/', ', ', $value);

        return trim($value, ', ');
    }
}
