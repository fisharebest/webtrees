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
 * The status of an edit in the change log.
 */
enum ChangeStatus: string
{
    case Accepted = 'accepted';
    case Pending  = 'pending';
    case Rejected = 'rejected';

    /**
     * A human-readable label for use in the UI.
     */
    public function label(): string
    {
        return match ($this) {
            /* I18N: the status of an edit accepted/rejected/pending */
            self::Accepted => I18N::translate('accepted'),
            /* I18N: the status of an edit accepted/rejected/pending */
            self::Pending  => I18N::translate('pending'),
            /* I18N: the status of an edit accepted/rejected/pending */
            self::Rejected => I18N::translate('rejected'),
        };
    }
}
