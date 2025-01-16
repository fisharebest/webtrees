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

use function array_key_exists;
use function str_ends_with;
use function str_starts_with;
use function strtoupper;

/**
 * ROLE_IN_EVENT := {Size=1:15}
 * [ CHIL | HUSB | WIFE | MOTH | FATH | SPOU | (<ROLE_DESCRIPTOR>) ]
 * Indicates what role this person played in the event that is being cited in this context. For
 * example, if you cite a child's birth record as the source of the mother's name, the value for
 * this field is "MOTH." If you describe the groom of a marriage, the role is "HUSB." If the role
 * is something different than one of the six relationship role tags listed above then enclose the
 * role name within matching parentheses.
 */
class RoleInEvent extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 15;

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = parent::canonical($value);
        $upper = strtoupper($value);

        if (array_key_exists($upper, $this->values())) {
            return $upper;
        }

        if (!str_starts_with($value, '(')) {
            $value = '(' . $value;
        }

        if (!str_ends_with($value, ')')) {
            return $value . ')';
        }

        return $value;
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            ''     => '',
            'CHIL' => I18N::translate('child'),
            'HUSB' => I18N::translate('husband'),
            'WIFE' => I18N::translate('wife'),
            'MOTH' => I18N::translate('mother'),
            'FATH' => I18N::translate('father'),
            'SPOU' => I18N::translate('spouse'),
        ];
    }
}
