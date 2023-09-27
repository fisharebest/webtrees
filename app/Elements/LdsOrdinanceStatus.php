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
 * g7:ord-STAT
 */
class LdsOrdinanceStatus extends AbstractElement
{
    private const CORRECTIONS = [
        // PhpGedView misspells this tag.
        'CANCELLED' => 'CANCELED',
        // GEDCOM 5.5.1
        'DNS/CAN'   => 'DNS_CAN',
        'PRE-1970'  => 'PRE_1970',
    ];

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return strtr(strtoupper(parent::canonical($value)), self::CORRECTIONS);
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
            'CANCELED'  => I18N::translate('Sealing canceled (divorce)'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'CHILD'     => I18N::translate('Died as a child: exempt'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'COMPLETED' => I18N::translate('Completed; date unknown'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'DNS'       => I18N::translate('Do not seal: unauthorized'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'DNS/CAN'   => I18N::translate('Do not seal, previous sealing canceled'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'EXCLUDED'  => I18N::translate('Excluded from this submission'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'INFANT'  => I18N::translate('Died less than 1 year old, sealing not required.'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'PRE_1970'  => I18N::translate('Completed before 1970; date not available'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'STILLBORN' => I18N::translate('Stillborn: exempt'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'SUBMITTED' => I18N::translate('Submitted but not yet cleared'),
            /* I18N: LDS sealing status; see https://en.wikipedia.org/wiki/Sealing_(Mormonism) */
            'UNCLEARED' => I18N::translate('Uncleared: insufficient data'),
        ];
    }
}
