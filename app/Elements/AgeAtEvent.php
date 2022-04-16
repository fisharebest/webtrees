<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use function preg_replace_callback_array;
use function strtoupper;

/**
 * AGE_AT_EVENT := {Size=1:12}
 * [ < | > | <NULL>]
 * [ YYy MMm DDDd | YYy | MMm | DDDd |
 * YYy MMm | YYy DDDd | MMm DDDd |
 * CHILD | INFANT | STILLBORN ]
 * ]
 * Where:
 * >         = greater than indicated age
 * <         = less than indicated age
 * y         = a label indicating years
 * m         = a label indicating months
 * d         = a label indicating days
 * YY        = number of full years
 * MM        = number of months
 * DDD       = number of days
 * CHILD     = age < 8 years
 * INFANT    = age <1year
 * STILLBORN = died just prior, at, or near birth, 0 years
 */
class AgeAtEvent extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 12;

    protected const KEYWORDS = ['CHILD', 'INFANT', 'STILLBORN'];

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return strtoupper(parent::canonical($value));
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

        switch ($canonical) {
            case 'CHILD':
                return I18N::translate('child');

            case 'INFANT':
                return I18N::translate('infant');

            case 'STILLBORN':
                return I18N::translate('stillborn');
        }

        return preg_replace_callback_array([
            '/\b(\d+)Y\b/' => fn (array $match) => I18N::plural('%s year', '%s years', (int) $match[1], I18N::number((float) $match[1])),
            '/\b(\d+)M\b/' => fn (array $match) => I18N::plural('%s month', '%s months', (int) $match[1], I18N::number((float) $match[1])),
            '/\b(\d+)W\b/' => fn (array $match) => I18N::plural('%s week', '%s weeks', (int) $match[1], I18N::number((float) $match[1])),
            '/\b(\d+)D\b/' => fn (array $match) => I18N::plural('%s day', '%s days', (int) $match[1], I18N::number((float) $match[1])),
        ], e($canonical));
    }
}
