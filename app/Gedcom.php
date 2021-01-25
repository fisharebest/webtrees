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

namespace Fisharebest\Webtrees;

/**
 * GEDCOM 5.5.1 specification
 */
class Gedcom
{
    // Use MSDOS style line endings, for maximum compatibility.
    public const EOL = "\r\n";

    // 255 less the EOL character.
    public const LINE_LENGTH = 253;

    // Gedcom tags which indicate the start of life.
    public const BIRTH_EVENTS = ['BIRT', 'CHR', 'BAPM', 'ADOP'];

    // Gedcom tags which indicate the end of life.
    public const DEATH_EVENTS = ['DEAT', 'BURI', 'CREM'];

    // Gedcom tags which indicate the start of a relationship.
    public const MARRIAGE_EVENTS = ['MARR', '_NMR'];

    // Gedcom tags which indicate the end of a relationship.
    public const DIVORCE_EVENTS = ['DIV', 'ANUL', '_SEPR'];

    // Regular expression to match a GEDCOM tag.
    public const REGEX_TAG = '[_A-Z][_A-Z0-9]*';

    // Regular expression to match a GEDCOM XREF.
    public const REGEX_XREF = '[A-Za-z0-9:_.-]{1,20}';

    // UTF-8 encoded files may begin with an optional byte-order-mark (U+FEFF).
    public const UTF8_BOM = "\xEF\xBB\xBF";

    // Separates parts of a place name.
    public const PLACE_SEPARATOR = ', ';

    // Regex to match a (badly formed) GEDCOM place separator.
    public const PLACE_SEPARATOR_REGEX = '/ *,[, ]*/';

    // LATI and LONG tags
    public const LATITUDE_NORTH = 'N';
    public const LATITUDE_SOUTH = 'S';
    public const LONGITUDE_EAST = 'E';
    public const LONGITUDE_WEST = 'W';

    // Not all record types allow a CHAN event.
    public const RECORDS_WITH_CHAN = [
        Family::RECORD_TYPE,
        Individual::RECORD_TYPE,
        Media::RECORD_TYPE,
        Note::RECORD_TYPE,
        Repository::RECORD_TYPE,
        Source::RECORD_TYPE,
        Submitter::RECORD_TYPE,
    ];
}
