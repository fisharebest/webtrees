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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;

/**
 * Access levels control who can view records, facts, and modules.
 * The backing values are historic database values.
 */
enum AccessLevel: int
{
    case Hidden  = -1; // Hide from everyone
    case Manager = 0;  // Show to managers
    case Member  = 1;  // Show to members
    case Public  = 2;  // Show to visitors

    /**
     * Create an access level from a tree preference setting.
     */
    public static function fromTreePreference(Tree $tree, string $setting_name): self
    {
        return self::from((int) $tree->getPreference($setting_name));
    }

    public function label(): string
    {
        return match ($this) {
            self::Hidden  => I18N::translate('Hide from everyone'),
            self::Manager => I18N::translate('Show to managers'),
            self::Member  => I18N::translate('Show to members'),
            self::Public  => I18N::translate('Show to visitors'),
        };
    }

    /**
     * Does a user's access level grant them access to data at this level?
     *
     * Managers can see everything except hidden data.
     * Members can see member-level and public data.
     * Public users can only see public data.
     */
    public function allows(self $user_level): bool
    {
        return match ($user_level) {
            self::Manager => $this !== self::Hidden,
            self::Member  => $this === self::Member || $this === self::Public,
            self::Public  => $this === self::Public,
            self::Hidden  => false,
        };
    }

    public function disallows(self $user_level): bool
    {
        return !$this->allows($user_level);
    }
}
