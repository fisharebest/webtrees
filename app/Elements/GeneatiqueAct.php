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

/**
 * _ACT tags from Geneatique
 */
class GeneatiqueAct extends AbstractElement
{
    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            ''   => '',
            'al' => I18N::translate('online'),
            'ca' => I18N::translate('local'),
            'c'  => I18N::translateContext('NOUN', 'copy'),
            'p'  => I18N::translate('photocopy'),
            'e'  => I18N::translateContext('NOUN', 'extract'),
            '<'  => I18N::translate('before'),
            '>'  => I18N::translate('after'),
        ];
    }
}
