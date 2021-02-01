<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use function strtolower;

/**
 * RESTRICTION_NOTICE := {Size=6:7}
 * [confidential | locked | privacy ]
 * The restriction notice is defined for Ancestral File usage. Ancestral File
 * download GEDCOM files may contain this data.
 * Where:
 * confidential = This data was marked as confidential by the user. In some systems data marked as
 *                confidential will be treated differently, for example, there might be an option
 *                that would stop confidential data from appearing on printed reports or would
 *                prevent that information from being exported.
 * locked       = Some records in Ancestral File have been satisfactorily proven by evidence, but
 *                because of source conflicts or incorrect traditions, there are repeated attempts
 *                to change this record. By arrangement, the Ancestral File Custodian can lock a
 *                record so that it cannot be changed without an agreement from the person assigned
 *                as the steward of such a record. The assigned steward is either the submitter
 *                listed for the record or Family History Support when no submitter is listed.
 * privacy      = Indicate that information concerning this record is not present due to rights of
 *                or an approved request for privacy. For example, data from requested downloads of
 *                the Ancestral File may have individuals marked with ‘privacy’ if they are assumed
 *                living, that is they were born within the last 110 years and there isn’t a death
 *                date. In certain cases family records may also be marked with the RESN tag of
 *                privacy if either individual acting in the role of HUSB or WIFE is assumed living.
 */
class RestrictionNotice extends AbstractElement
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
        return strtolower(parent::canonical($value));
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        // Note: "1 RESN none" is not valid gedcom.
        // However, webtrees privacy rules will interpret it as "show an otherwise private record to public".

        return [
            ''             => '',
            'none'         => '<i class="icon-resn-none"></i> ' . I18N::translate('Show to visitors'),
            'privacy'      => '<i class="icon-resn-privacy"></i> ' . I18N::translate('Show to members'),
            'confidential' => '<i class="icon-resn-confidential"></i> ' . I18N::translate('Show to managers'),
            'locked'       => '<i class="icon-resn-locked"></i> ' . I18N::translate('Only managers can edit'),
        ];
    }
}
