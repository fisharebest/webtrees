<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\I18N;

use function strtr;

/**
 * LDS_BAPTISM_DATE_STATUS := {Size=5:10}
 * [ CANCELED | COMPLETED | DNS | EXCLUDED | DNS/CAN | PRE-1970 | SUBMITTED | UNCLEARED ]
 * CANCELED  = Canceled and considered invalid.
 * COMPLETED = Completed but the date is not known.
 * DNS       = This ordinance is not authorized.
 * EXCLUDED  = Patron excluded this ordinance from being cleared in this submission.
 * DNS/CAN   = This ordinance is not authorized, previous sealing cancelled.
 * PRE-1970  = (See pre-1970 under LDS_BAPTISM_DATE_STATUS on page 51.)
 * SUBMITTED = Ordinance was previously submitted.
 * UNCLEARED = Data for clearing ordinance request was insufficient.
 */
class LdsSpouseSealingDateStatus extends AbstractElement
{
    /**
     * Convert a value to a canonical form.
     * Some applications use the British spelling instead of the US spelling.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        // PhpGedView misspells this tag.
        return strtr(strtoupper(parent::canonical($value)), ['CANCELLED' => 'CANCELED']);
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            ''          => '',
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'CANCELED'  => I18N::translate('Sealing canceled (divorce)'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'COMPLETED' => I18N::translate('Completed; date unknown'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'DNS'       => I18N::translate('Do not seal: unauthorized'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'DNS/CAN'   => I18N::translate('Do not seal, previous sealing canceled'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'EXCLUDED'  => I18N::translate('Excluded from this submission'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'PRE-1970'  => I18N::translate('Completed before 1970; date not available'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'SUBMITTED' => I18N::translate('Submitted but not yet cleared'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'UNCLEARED' => I18N::translate('Uncleared: insufficient data'),
        ];
    }
}
