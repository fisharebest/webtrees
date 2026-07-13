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

namespace Fisharebest\Webtrees\Comparators;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;

use function explode;

final class FactComparator
{
    public static function byDate(Fact $first, Fact $second): int
    {
        if ($first->date()->isOK() && $second->date()->isOK()) {
            $result = Date::compare($first->date(), $second->date());

            // Same date? Use type as a tie-break.
            if ($result === 0) {
                $result = self::byType($first, $second);
            }

            return $result;
        }

        // One or both events have no date - stable sort preserves original order.
        return 0;
    }

    public static function byType(Fact $first, Fact $second): int
    {
        $first_tag  = self::effectiveTag($first);
        $second_tag = self::effectiveTag($second);

        // Same type: dated before undated, otherwise preserve original order.
        if ($first_tag === $second_tag) {
            if ($first->attribute('DATE') !== '' && $second->attribute('DATE') === '') {
                return -1;
            }

            if ($second->attribute('DATE') !== '' && $first->attribute('DATE') === '') {
                return 1;
            }

            return 0;
        }

        return TagComparator::byOrder($first_tag, $second_tag);
    }

    /**
     * Return the numeric type-order position of a fact in the lifecycle sequence.
     */
    public static function typeOrder(Fact $fact): int
    {
        $tag = self::effectiveTag($fact);

        return TagComparator::order($tag);
    }

    /**
     * Determine the effective tag for sorting purposes.
     * NO events sort as the event they negate; associate events sort as EVEN.
     */
    private static function effectiveTag(Fact $fact): string
    {
        [, $tag] = explode(':', $fact->tag(), 2);

        if ($tag === 'NO') {
            return $fact->value();
        }

        if ($fact->id() === 'asso') {
            return 'EVEN';
        }

        return $tag;
    }
}
