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

use function preg_replace_callback_array;
use function strtolower;
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

    public function canonical(string $value): string
    {
        $value = parent::canonical($value);
        $upper = strtoupper($value);

        if (in_array($upper, static::KEYWORDS, true)) {
            return $upper;
        }

        return strtolower($value);
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
                return I18N::translate('Child');

            case 'INFANT':
                return I18N::translate('Infant');

            case 'STILLBORN':
                return I18N::translate('Stillborn');
        }

        return preg_replace_callback_array([
            '/\b(\d+)y\b/' => fn (array $match) => I18N::plural('%s year', '%s years', (int) $match[1], I18N::number((float) $match[1])),
            '/\b(\d+)m\b/' => fn (array $match) => I18N::plural('%s month', '%s months', (int) $match[1], I18N::number((float) $match[1])),
            '/\b(\d+)w\b/' => fn (array $match) => I18N::plural('%s week', '%s weeks', (int) $match[1], I18N::number((float) $match[1])),
            '/\b(\d+)d\b/' => fn (array $match) => I18N::plural('%s day', '%s days', (int) $match[1], I18N::number((float) $match[1])),
        ], e($canonical));
    }
}
