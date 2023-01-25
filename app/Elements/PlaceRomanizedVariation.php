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
 * PLACE_ROMANIZED_VARIATION := {Size=1:120}
 * The romanized variation of the place name is written in the same form
 * prescribed for the place name used in the superior <PLACE_NAME> context. The
 * method used to romanize the name is indicated by the line_value of the
 * subordinate <ROMANIZED_TYPE>, for example if romaji was used to provide a
 * reading of a place name written in kanji, then the <ROMANIZED_TYPE>
 * subordinate to the ROMN tag would indicate ‘romaji’. (See <ROMANIZED_TYPE>
 * page 61.)
 */
class PlaceRomanizedVariation extends AbstractElement
{
}
