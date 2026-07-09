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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;

final readonly class GedcomRecordComparator
{
    public static function byType(GedcomRecord $first, GedcomRecord $second): int
    {
        return $first->tag() <=> $second->tag();
    }

    public static function byName(GedcomRecord $first, GedcomRecord $second): int
    {
        if ($first->canShowName()) {
            if ($second->canShowName()) {
                return I18N::compare($first->sortName(), $second->sortName());
            }

            return -1; // only $second is private
        }

        if ($second->canShowName()) {
            return 1; // only $first is private
        }

        return 0; // both $first and $second private
    }

    public static function byLastChange(GedcomRecord $first, GedcomRecord $second): int
    {
        return $first->lastChangeTimestamp() <=> $second->lastChangeTimestamp();
    }
}
