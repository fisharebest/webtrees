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

/**
 * A user's role within a particular tree.
 * The backing values are historic database values.
 */
enum Role: string
{
    case Visitor   = 'none';
    case Member    = 'access';
    case Editor    = 'edit';
    case Moderator = 'accept';
    case Manager   = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Visitor   => I18N::translate('Visitor'),
            self::Member    => I18N::translate('Member'),
            self::Editor    => I18N::translate('Editor'),
            self::Moderator => I18N::translate('Moderator'),
            self::Manager   => I18N::translate('Manager'),
        };
    }
}
