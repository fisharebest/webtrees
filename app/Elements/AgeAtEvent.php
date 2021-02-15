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

    public function canonical(string $value): string
    {
        $value = parent::canonical($value);
        $upper = strtoupper($value);

        if ($upper === 'CHILD' || $upper === 'INFANT' || $upper === 'STILLBORN') {
            return $upper;
        }

        return strtolower($value);
    }
}
