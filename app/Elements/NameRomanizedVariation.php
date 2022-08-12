<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fisharebest\Webtrees\Tree;

use function view;

/**
 * NAME_ROMANIZED_VARIATION := {Size=1:120}
 * The romanized variation of the name is written in the same form prescribed
 * for the name used in the superior <NAME_PERSONAL> context. The method used
 * to romanize the name is indicated by the line_value of the subordinate
 * <ROMANIZED_TYPE>, for example if romaji was used to provide a reading of a
 * name written in kanji, then the ROMANIZED_TYPE subordinate to the ROMN tag
 * would indicate romaji. See page 61.
 */
class NameRomanizedVariation extends NamePersonal
{
    protected const SUBTAGS = [
        'TYPE' => '1:1',
        'NPFX' => '0:1',
        'GIVN' => '0:1',
        'SPFX' => '0:1',
        'SURN' => '0:1',
        'NSFX' => '0:1',
        'NICK' => '0:1',
        'NOTE' => '0:M',
        'SOUR' => '0:M',
    ];

    /**
     * Should we collapse the children of this element when editing?
     *
     * @return bool
     */
    public function collapseChildren(): bool
    {
        return true;
    }

    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        return
            '<div class="input-group">' .
            parent::edit($id, $name, $value, $tree) .
            view('edit/input-addon-help', ['topic' => 'ROMN']) .
            '</div>';
    }
}
