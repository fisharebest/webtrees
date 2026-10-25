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

enum Sex: string
{
    case Male    = 'M';
    case Female  = 'F';
    case Unknown = 'U';
    case Other   = 'X';

    public function label(): string
    {
        return match ($this) {
            self::Male    => I18N::translate('Male'),
            self::Female  => I18N::translate('Female'),
            self::Unknown => I18N::translate('Unknown'),
            self::Other   => I18N::translateContext('SEX', 'Other'),
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::Male    => 'wt-sex-m',
            self::Female  => 'wt-sex-f',
            self::Unknown => 'wt-sex-u',
            self::Other   => 'wt-sex-x',
        };
    }

    public function opposite(): self
    {
        return match ($this) {
            self::Female => self::Male,
            self::Male => self::Female,
            default => self::Unknown,
        };
    }
}
