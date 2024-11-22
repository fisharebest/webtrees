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
 * CHILD_LINKAGE_STATUS := {Size=1:15}
 * [challenged | disproven | proven]
 * A status code that allows passing on the users opinion of the status of a
 * child to family link.
 * challenged = Linking this child to this family is suspect, but the linkage
 *              has been neither proven nor disproven.
 * disproven  = There has been a claim by some that this child belongs to this
 *              family, but the linkage has been disproven.
 * proven     = There has been a claim by some that this child does not belongs
 *              to this family, but the linkage has been proven.
 */
class ChildLinkageStatus extends AbstractElement
{
    public const string VALUE_CHALLENGED = 'CHALLENGED';
    public const string VALUE_DISPROVEN = 'DISPROVEN';
    public const string VALUE_PROVEN    = 'PROVEN';

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
            ''                     => '',
            self::VALUE_CHALLENGED => /* I18N: Status of child-parent link */ I18N::translate('challenged'),
            self::VALUE_DISPROVEN  => /* I18N: Status of child-parent link */ I18N::translate('disproven'),
            self::VALUE_PROVEN     => /* I18N: Status of child-parent link */ I18N::translate('proven'),
        ];
    }
}
