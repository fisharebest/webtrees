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

/**
 * Heredis custom tag INDI:_FIL - Child status
 */
class HeredisFIL extends AbstractElement
{

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            'LEGITIMATE_CHILD'     => I18N::translate('Légitime'),
            'NATURAL_CHILD'        => I18N::translate('Naturel'),
            'RECOGNIZED_CHILD'     => I18N::translate('Reconnu'),
            'LEGITIMIZED_CHILD'    => I18N::translate('Légitimé'),
            'CHILD_FOUND'          => I18N::translate('Trouvé'),
            'ADOPTED_CHILD'        => I18N::translate('Adopté'),
            'ADULTEROUS_CHILD'     => I18N::translate('Adultérin'),
            'STILLBORN_CHILD'      => I18N::translate('Mort-Né'),
            'RELATIONSHIP_UNKNOW'  => I18N::translate('Non Connue'),
        ];
    }

}
