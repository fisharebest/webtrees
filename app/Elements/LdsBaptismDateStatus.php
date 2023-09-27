<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
 * [ CHILD | COMPLETED | EXCLUDED | PRE-1970 | STILLBORN | SUBMITTED | UNCLEARED ]
 * A code indicating the status of an LDS baptism and confirmation date where:
 * CHILD     = Died before becoming eight years old, baptism not required.
 * COMPLETED = Completed but the date is not known.
 * EXCLUDED  = Patron excluded this ordinance from being cleared in this submission.
 * PRE-1970  = Ordinance is likely completed, another ordinance for this person
 *             was converted from temple records of work completed before 1970,
 *             therefore this ordinance is assumed to be complete until all
 *             records are converted.
 * STILLBORN = Stillborn, baptism not required.
 * SUBMITTED = Ordinance was previously submitted.
 * UNCLEARED = Data for clearing ordinance request was insufficient.
 */
class LdsBaptismDateStatus extends AbstractElement
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
            'CHILD'     => I18N::translate('Died as a child: exempt'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'COMPLETED' => I18N::translate('Completed; date unknown'),
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
