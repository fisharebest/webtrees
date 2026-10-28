<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Enums;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;

use function assert;
use function preg_match;

/**
 * The basic components of a relationship path.
 */
enum Relation
{
    case Sister;
    case Brother;
    case Sibling;
    case Mother;
    case Father;
    case Parent;
    case Daughter;
    case Son;
    case Child;
    case Wife;
    case Husband;
    case Spouse;

    /**
     * Determine the relationship between two family members.
     */
    public static function fromFamilyLinks(Individual $individual1, Family $family, Individual $individual2): self
    {
        $gedcom = $family->gedcom();

        preg_match('/\n1 (HUSB|WIFE|CHIL) @' . $individual1->xref() . '@/', $gedcom, $match1);
        preg_match('/\n1 (HUSB|WIFE|CHIL) @' . $individual2->xref() . '@/', $gedcom, $match2);

        // Our code guarantees this, but static analysis tools don't know that.
        assert(isset($match1[1], $match2[1]));

        $link1 = $match1[1];
        $link2 = $match2[1];

        if ($link1 === 'CHIL') {
            if ($link2 === 'CHIL') {
                return match ($individual2->sex()) {
                    Sex::Female => self::Sister,
                    Sex::Male   => self::Brother,
                    default     => self::Sibling,
                };
            }

            return match ($individual2->sex()) {
                Sex::Female => self::Mother,
                Sex::Male   => self::Father,
                default     => self::Parent,
            };
        }

        if ($link2 === 'CHIL') {
            return match ($individual2->sex()) {
                Sex::Female => self::Daughter,
                Sex::Male   => self::Son,
                default     => self::Child,
            };
        }

        return match ($individual2->sex()) {
            Sex::Female => self::Wife,
            Sex::Male   => self::Husband,
            default     => self::Spouse,
        };
    }
}
