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
    public const BIRTH_EVENTS = ['BIRT', 'CHR', 'BAPM'];

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

    // These preferences control multiple tag definitions
    public const HIDDEN_TAGS = [
        // Individual names
        'NAME_NPFX' => ['INDI:NAME:NPFX', 'INDI:NAME:FONE:NPFX', 'INDI:NAME:ROMN:NPFX'],
        'NAME_SPFX' => ['INDI:NAME:SPFX', 'INDI:NAME:FONE:SPFX', 'INDI:NAME:ROMN:SPFX'],
        'NAME_NSFX' => ['INDI:NAME:NSFX', 'INDI:NAME:FONE:NSFX', 'INDI:NAME:ROMN:NSFX'],
        'NAME_NICK' => ['INDI:NAME:NICK', 'INDI:NAME:FONE:NICK', 'INDI:NAME:ROMN:NICK'],
        'NAME_FONE' => ['INDI:NAME:FONE'],
        'NAME_ROMN' => ['INDI:NAME:ROMN'],
        'NAME_NOTE' => ['INDI:NAME:NOTE'],
        'NAME_SOUR' => ['INDI:NAME:SOUR'],
        // Places
        'PLAC_MAP'  => ['PLAC:MAP'],
        'PLAC_FONE' => ['PLAC:FONE'],
        'PLAC_ROMN' => ['PLAC:ROMN'],
        'PLAC_FORM' => ['PLAC:FORM', 'HEAD:PLAC'],
        'PLAC_NOTE' => ['PLAC:NOTE'],
        // Addresses
        'ADDR_FAX'  => ['FAX'],
        'ADDR_PHON' => ['PHON'],
        'ADDR_WWW'  => ['WWW'],
        // Source citations
        'SOUR_EVEN' => [':SOUR:EVEN'],
        'SOUR_DATE' => [':SOUR:DATA:DATE'],
        'SOUR_NOTE' => [':SOUR:NOTE'],
        'SOUR_QUAY' => [':SOUR:QUAY'],
        // Sources
        'SOUR_DATA' => ['SOUR:DATA:EVEN', 'SOUR:DATA:AGNC', 'SOUR:DATA:NOTE'],
        // Individuals
        'BIRT_FAMC' => ['INDI:BIRT:FAMC'],
        'RELI'      => ['INDI:RELI'],
        'BAPM'      => ['INDI:BAPM'],
        'CHR'       => ['INDI:CHR', 'INDI:CHRA'],
        'FCOM'      => ['INDI:FCOM', 'INDI:CONF'],
        'ORDN'      => ['INDI:ORDN'],
        'BARM'      => ['INDI:BARM', 'INDI:BASM'],
        'ALIA'      => ['INDI:ALIA'],
        'ASSO'      => ['INDI:ASSO'],
        // Families
        'ENGA'      => ['FAM:ENGA'],
        'MARB'      => ['FAM:MARB'],
        'MARC'      => ['FAM:MARC'],
        'MARL'      => ['FAM:MARL'],
        'MARS'      => ['FAM:MARS'],
        'ANUL'      => ['FAM:ANUL'],
        'DIVF'      => ['FAM:DIVF'],
        'FAM_RESI'  => ['FAM:RESI'],
        'FAM_CENS'  => ['FAM:CENS'],
        // LDS church
        'LDS'       => ['INDI:BAPL', 'INDI:CONL', 'INDI:ENDL', 'INDI:SLGC', 'FAM:SLGS', 'HEAD:SUBN'],
        // Identifiers
        'AFN'       => ['INDI:AFN'],
        'IDNO'      => ['INDI:IDNO'],
        'SSN'       => ['INDI:SSN'],
        'RFN'       => ['RFN'],
        'REFN'      => ['REFN'],
        'RIN'       => ['RIN'],
        // Submitters
        'SUBM'      => ['INDI:SUBM', 'FAM:SUBM'],
        'ANCI'      => ['INDI:ANCI', 'INDI:DESI'],
    ];
}
