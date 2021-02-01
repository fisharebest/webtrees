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

/**
 * NAME_TYPE := {Size=5:30}
 * [ aka | birth | immigrant | maiden | married | <user defined>]
 * Indicates the name type, for example the name issued or assumed as an immigrant.
 * aka          = also known as, alias, etc.
 * birth        = name given on birth certificate.
 * immigrant    = name assumed at the time of immigration.
 * maiden       = maiden name, name before first marriage.
 * married      =name was persons previous married name.
 * user_defined = other text name that defines the name type.
 */
class NameType extends AbstractElement
{
    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            ''          => '',
            'adopted'   => /* I18N: The name given to a child by its adoptive parents */
                I18N::translate('adopted name'),
            'aka'       => /* I18N: The name by which an individual is also known. e.g. a professional name or a stage name */
                I18N::translate('also known as'),
            'birth'     => /* I18N: The name given to an individual at their birth */
                I18N::translate('birth name'),
            'change'    => /* I18N: A name chosen by an individual, to replace their existing name (whether legal or otherwise) */
                I18N::translate('change of name'),
            'estate'    => /* I18N: A name given to an individual, from the farm or estate on which they lived or worked */
                I18N::translate('estate name'),
            'immigrant' => /* I18N: A name taken on immigration - e.g. migrants to the USA frequently anglicized their names */
                I18N::translate('immigration name'),
            'maiden'    => /* I18N: A woman’s name, before she marries (in cultures where women take their new husband’s name on marriage) */
                I18N::translate('maiden name'),
            'married'   => /* I18N: A name taken on marriage - usually the wife takes the husband’s surname */
                I18N::translate('married name'),
            'religious' => /* I18N: A name taken when entering a religion or a religious order */
                I18N::translate('religious name'),
        ];
    }
}
