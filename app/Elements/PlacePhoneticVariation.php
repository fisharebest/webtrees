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

/**
 * PLACE_PHONETIC_VARIATION := {Size=1:120}
 * The phonetic variation of the place name is written in the same form as was
 * the place name used in the superior <PLACE_NAME> primitive, but phonetically
 * written using the method indicated by the subordinate <PHONETIC_TYPE> value,
 * for example if hiragana was used to provide a reading of a a name written in
 * kanji, then the <PHONETIC_TYPE> value would indicate kana.
 * (See <PHONETIC_TYPE> page 57.)
 */
class PlacePhoneticVariation extends AbstractElement
{
}
