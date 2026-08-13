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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;

enum ContactMethod: string
{
    case Internal         = 'messaging';
    case InternalAndEmail = 'messaging2';
    case Email            = 'messaging3';
    case None             = 'none';

    public static function fromUser(UserInterface $user): self
    {
        $value = $user->getPreference(UserInterface::PREF_CONTACT_METHOD);

        // The 'mailto' option was removed in 2.3.0
        if ($value === 'mailto') {
            return self::Email;
        }

        return self::tryFrom($value) ?? self::InternalAndEmail;
    }

    public function label(): string
    {
        return match ($this) {
            self::Internal         => I18N::translate('Inbox only'),
            self::InternalAndEmail => I18N::translate('Inbox and email'),
            self::Email            => I18N::translate('Email only'),
            self::None             => I18N::translate('No contact'),
        };
    }

    public function sendsInternalMessage(): bool
    {
        return $this !== self::Email;
    }

    public function sendsEmail(): bool
    {
        return $this !== self::Internal;
    }

    public function isContactable(): bool
    {
        return $this !== self::None;
    }

    public function isNotContactable(): bool
    {
        return $this === self::None;
    }
}
