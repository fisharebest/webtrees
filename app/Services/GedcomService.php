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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Gedcom;

/**
 * Utilities for manipulating GEDCOM data.
 */
class GedcomService
{
    // User defined tags begin with an underscore
    private const USER_DEFINED_TAG_PREFIX = '_';

    // Some applications, such as FTM, use GEDCOM tag names instead of the tags.
    private const TAG_NAMES = [
        'ABBREVIATION'      => 'ABBR',
        'ADDRESS'           => 'ADDR',
        'ADDRESS1'          => 'ADR1',
        'ADDRESS2'          => 'ADR2',
        'ADOPTION'          => 'ADOP',
        'AGENCY'            => 'AGNC',
        'ALIAS'             => 'ALIA',
        'ANCESTORS'         => 'ANCE',
        'ANCES_INTEREST'    => 'ANCI',
        'ANULMENT'          => 'ANUL',
        'ASSOCIATES'        => 'ASSO',
        'AUTHOR'            => 'AUTH',
        'BAPTISM-LDS'       => 'BAPL',
        'BAPTISM'           => 'BAPM',
        'BAR_MITZVAH'       => 'BARM',
        'BAS_MITZVAH'       => 'BASM',
        'BIRTH'             => 'BIRT',
        'BLESSING'          => 'BLES',
        'BURIAL'            => 'BURI',
        'CALL_NUMBER'       => 'CALN',
        'CASTE'             => 'CAST',
        'CAUSE'             => 'CAUS',
        'CENSUS'            => 'CENS',
        'CHANGE'            => 'CHAN',
        'CHARACTER'         => 'CHAR',
        'CHILD'             => 'CHIL',
        'CHRISTENING'       => 'CHR',
        'ADULT_CHRISTENING' => 'CHRA',
        'CONCATENATION'     => 'CONC',
        'CONFIRMATION'      => 'CONF',
        'CONFIRMATION-LDS'  => 'CONL',
        'CONTINUED'         => 'CONT',
        'COPYRIGHT'         => 'COPY',
        'CORPORTATE'        => 'CORP',
        'CREMATION'         => 'CREM',
        'COUNTRY'           => 'CTRY',
        'DEATH'             => 'DEAT',
        'DESCENDANTS'       => 'DESC',
        'DESCENDANTS_INT'   => 'DESI',
        'DESTINATION'       => 'DEST',
        'DIVORCE'           => 'DIV',
        'DIVORCE_FILED'     => 'DIVF',
        'PHY_DESCRIPTION'   => 'DSCR',
        'EDUCATION'         => 'EDUC',
        'EMIGRATION'        => 'EMIG',
        'ENDOWMENT'         => 'ENDL',
        'ENGAGEMENT'        => 'ENGA',
        'EVENT'             => 'EVEN',
        'FAMILY'            => 'FAM',
        'FAMILY_CHILD'      => 'FAMC',
        'FAMILY_FILE'       => 'FAMF',
        'FAMILY_SPOUSE'     => 'FAMS',
        'FACIMILIE'         => 'FAX',
        'FIRST_COMMUNION'   => 'FCOM',
        'FORMAT'            => 'FORM',
        'PHONETIC'          => 'FONE',
        'GEDCOM'            => 'GEDC',
        'GIVEN_NAME'        => 'GIVN',
        'GRADUATION'        => 'GRAD',
        'HEADER'            => 'HEAD',
        'HUSBAND'           => 'HUSB',
        'IDENT_NUMBER'      => 'IDNO',
        'IMMIGRATION'       => 'IMMI',
        'INDIVIDUAL'        => 'INDI',
        'LANGUAGE'          => 'LANG',
        'LATITUDE'          => 'LATI',
        'LONGITUDE'         => 'LONG',
        'MARRIAGE_BANN'     => 'MARB',
        'MARR_CONTRACT'     => 'MARC',
        'MARR_LICENSE'      => 'MARL',
        'MARRIAGE'          => 'MARR',
        'MEDIA'             => 'MEDI',
        'NATIONALITY'       => 'NATI',
        'NATURALIZATION'    => 'NATU',
        'CHILDREN_COUNT'    => 'NCHI',
        'NICKNAME'          => 'NICK',
        'MARRIAGE_COUNT'    => 'NMR',
        'NAME_PREFIX'       => 'NPFX',
        'NAME_SUFFIX'       => 'NSFX',
        'OBJECT'            => 'OBJE',
        'OCCUPATION'        => 'OCCU',
        'ORDINANCE'         => 'ORDI',
        'ORDINATION'        => 'ORDN',
        'PEDIGREE'          => 'PEDI',
        'PHONE'             => 'PHON',
        'PLACE'             => 'PLAC',
        'POSTAL_CODE'       => 'POST',
        'PROBATE'           => 'PROB',
        'PROPERTY'          => 'PROP',
        'PUBLICATION'       => 'PUBL',
        'QUALITY_OF_DATA'   => 'QUAY',
        'REFERENCE'         => 'REFN',
        'RELATIONSHIP'      => 'RELA',
        'RELIGION'          => 'RELI',
        'REPOSITORY'        => 'REPO',
        'RESIDENCE'         => 'RESI',
        'RESTRICTION'       => 'RESN',
        'RETIREMENT'        => 'RETI',
        'REC_FILE_NUMBER'   => 'RFN',
        'REC_ID_NUMBER'     => 'RIN',
        'ROMANIZED'         => 'ROMN',
        'SEALING_CHILD'     => 'SLGC',
        'SEALING_SPOUSE'    => 'SLGS',
        'SOURCE'            => 'SOUR',
        'SURN_PREFIX'       => 'SPFX',
        'SOC_SEC_NUMBER'    => 'SSN',
        'STATE'             => 'STAE',
        'STATUS'            => 'STAT',
        'SUBMITTER'         => 'SUBM',
        'SUBMISSION'        => 'SUBN',
        'SURNAME'           => 'SURN',
        'TEMPLE'            => 'TEMP',
        'TITLE'             => 'TITL',
        'TRAILER'           => 'TRLR',
        'VERSION'           => 'VERS',
        'WEB'               => 'WWW',
        '_DEATH_OF_SPOUSE'  => 'DETS',
        '_DEGREE'           => '_DEG',
        '_MEDICAL'          => '_MCL',
        '_MILITARY_SERVICE' => '_MILT',
    ];

    // Custom tags used by other applications, with direct synonyms
    private const TAG_SYNONYMS = [
        // Convert PhpGedView tag to webtrees
        '_PGVU'     => '_WT_USER',
        '_PGV_OBJS' => '_WT_OBJE_SORT',
    ];

    // SEX tags
    private const SEX_FEMALE  = 'F';
    private const SEX_MALE    = 'M';
    private const SEX_UNKNOWN = 'U';

    /**
     * Convert a GEDCOM tag to a canonical form.
     *
     * @param string $tag
     *
     * @return string
     */
    public function canonicalTag(string $tag): string
    {
        $tag = strtoupper($tag);

        $tag = self::TAG_NAMES[$tag] ?? self::TAG_SYNONYMS[$tag] ?? $tag;

        return $tag;
    }

    /**
     * @param string $tag
     *
     * @return bool
     */
    public function isUserDefinedTag(string $tag): bool
    {
        return substr_compare($tag, self::USER_DEFINED_TAG_PREFIX, 0, 1) === 0;
    }

    /**
     * @param string $text
     *
     * @return float|null
     */
    public function readLatitude(string $text): ?float
    {
        return $this->readDegrees($text, Gedcom::LATITUDE_NORTH, Gedcom::LATITUDE_SOUTH);
    }

    /**
     * @param string $text
     *
     * @return float|null
     */
    public function readLongitude(string $text): ?float
    {
        return $this->readDegrees($text, Gedcom::LONGITUDE_EAST, Gedcom::LONGITUDE_WEST);
    }

    /**
     * @param string $text
     * @param string $positive
     * @param string $negative
     *
     * @return float|null
     */
    private function readDegrees(string $text, string $positive, string $negative): ?float
    {
        $text       = trim($text);
        $hemisphere = substr($text, 0, 1);
        $degrees    = substr($text, 1);

        // Match a valid GEDCOM format
        if (is_numeric($degrees)) {
            $hemisphere = strtoupper($hemisphere);
            $degrees    = (float) $degrees;

            if ($hemisphere === $positive) {
                return $degrees;
            }

            if ($hemisphere === $negative) {
                return -$degrees;
            }
        }

        // Just a number?
        if (is_numeric($text)) {
            return (float) $text;
        }

        // Can't match anything.
        return null;
    }

    /**
     * Although empty placenames are valid "Town, , Country", it is only meaningful
     * when structured places are used (PLAC:FORM town, county, country), and
     * structured places are discouraged.
     *
     * @param string $text
     *
     * @return array<string>
     */
    public function readPlace(string $text): array
    {
        $text = trim($text);

        return preg_split(Gedcom::PLACE_SEPARATOR_REGEX, $text);
    }

    /**
     * @param string[] $place
     *
     * @return string
     */
    public function writePlace(array $place): string
    {
        return implode(Gedcom::PLACE_SEPARATOR, $place);
    }

    /**
     * Some applications use non-standard values for unknown.
     *
     * @param string $text
     *
     * @return string
     */
    public function readSex(string $text): string
    {
        $text = strtoupper($text);

        if ($text !== self::SEX_MALE && $text !== self::SEX_FEMALE) {
            $text = self::SEX_UNKNOWN;
        }

        return $text;
    }
}
