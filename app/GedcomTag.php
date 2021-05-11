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

namespace Fisharebest\Webtrees;

use function str_contains;

/**
 * Static GEDCOM data for tags
 */
class GedcomTag
{
    /**
     * Translate a tag, for an (optional) record
     *
     * @param string $tag
     *
     * @return string
     */
    public static function getLabel($tag): string
    {
        return Registry::elementFactory()->make($tag)->label();
    }

    /**
     * Translate a label/value pair, such as “Occupation: Farmer”
     *
     * @param string            $tag
     * @param string            $value
     * @param GedcomRecord|null $record
     * @param string|null       $element
     *
     * @return string
     */
    public static function getLabelValue(string $tag, string $value, GedcomRecord $record = null, $element = 'div'): string
    {
        return
            '<' . $element . ' class="fact_' . $tag . '">' .
            /* I18N: a label/value pair, such as “Occupation: Farmer”. Some languages may need to change the punctuation. */
            I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', self::getLabel($tag), $value) .
            '</' . $element . '>';
    }
}
