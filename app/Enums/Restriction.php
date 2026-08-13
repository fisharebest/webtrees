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

use function str_contains;
use function strtoupper;

enum Restriction: string
{
    case Undefined           = '';
    case None                = 'NONE';
    case NoneLocked          = 'NONE, LOCKED';
    case Privacy             = 'PRIVACY';
    case PrivacyLocked       = 'PRIVACY, LOCKED';
    case Confidential        = 'CONFIDENTIAL';
    case ConfidentialLocked  = 'CONFIDENTIAL, LOCKED';
    case Locked              = 'LOCKED';

    public static function fromString(string $value): self
    {
        $value = strtoupper($value);

        $locked       = str_contains($value, self::Locked->value);
        $privacy      = str_contains($value, self::Privacy->value);
        $confidential = str_contains($value, self::Confidential->value);
        $none         = str_contains($value, self::None->value);

        return match (true) {
            $locked && $confidential => self::ConfidentialLocked,
            $locked && $privacy      => self::PrivacyLocked,
            $locked && $none         => self::NoneLocked,
            $locked                  => self::Locked,
            $confidential            => self::Confidential,
            $privacy                 => self::Privacy,
            $none                    => self::None,
            default                  => self::Undefined,
        };
    }

    /**
     * A human-readable label for use in the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Undefined          => '',
            self::None               => I18N::translate('Show to visitors'),
            self::NoneLocked         => I18N::translate('Show to visitors') . ' — ' . I18N::translate('Only managers can edit'),
            self::Privacy            => I18N::translate('Show to members'),
            self::PrivacyLocked      => I18N::translate('Show to members') . ' — ' . I18N::translate('Only managers can edit'),
            self::Confidential       => I18N::translate('Show to managers'),
            self::ConfidentialLocked => I18N::translate('Show to managers') . ' — ' . I18N::translate('Only managers can edit'),
            self::Locked             => I18N::translate('Only managers can edit'),
        };
    }

    /**
     * The access level required to view a record with this restriction.
     * Returns null for restrictions that do not control visibility.
     */
    public function accessLevel(): AccessLevel|null
    {
        return match ($this) {
            self::None, self::NoneLocked                 => AccessLevel::Public,
            self::Privacy, self::PrivacyLocked           => AccessLevel::Member,
            self::Confidential, self::ConfidentialLocked => AccessLevel::Manager,
            self::Undefined, self::Locked                => null,
        };
    }

    /**
     * Whether this restriction prevents editing by non-managers.
     */
    public function isLocked(): bool
    {
        return match ($this) {
            self::NoneLocked, self::PrivacyLocked, self::ConfidentialLocked, self::Locked => true,
            default                                                                       => false,
        };
    }

    /**
     * Whether this restriction allows editing by non-managers.
     */
    public function isUnlocked(): bool
    {
        return !$this->isLocked();
    }
}
