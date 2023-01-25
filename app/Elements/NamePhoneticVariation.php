<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

/**
 * NAME_PHONETIC_VARIATION := {Size=1:120}
 * The phonetic variation of the name is written in the same form as the was
 * the name used in the superior <NAME_PERSONAL> primitive, but phonetically
 * written using the method indicated by the subordinate <PHONETIC_TYPE> value,
 * for example if hiragana was used to provide a reading of a name written in
 * kanji, then the <PHONETIC_TYPE> value would indicate ‘kana’. See page 57.
 */
class NamePhoneticVariation extends NamePersonal
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
}
