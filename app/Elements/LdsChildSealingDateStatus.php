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

use function strtoupper;

/**
 * LDS_BAPTISM_DATE_STATUS := {Size=5:10}
 * [ BIC | COMPLETED | EXCLUDED | DNS | PRE-1970 | STILLBORN | SUBMITTED | UNCLEARED ]
 * BIC       = Born in the covenant receiving blessing of child to parent sealing.
 * EXCLUDED  = Patron excluded this ordinance from being cleared in this submission.
 * PRE-1970  = (See pre-1970 under LDS_BAPTISM_DATE_STATUS on page 51.)
 * STILLBORN = Stillborn, baptism not required.
 * SUBMITTED = Ordinance was previously submitted.
 * UNCLEARED = Data for clearing ordinance request was insufficient.
 */
class LdsChildSealingDateStatus extends AbstractElement
{
    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return strtoupper(parent::canonical($value));
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
            'BIC'       => I18N::translate('Born in the covenant'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'EXCLUDED'  => I18N::translate('Excluded from this submission'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'PRE-1970'  => I18N::translate('Completed before 1970; date not available'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'STILLBORN' => I18N::translate('Stillborn: exempt'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'SUBMITTED' => I18N::translate('Submitted but not yet cleared'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'UNCLEARED' => I18N::translate('Uncleared: insufficient data'),
        ];
    }
}
