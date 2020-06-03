<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

/**
 * Application configuration data. Data here has no GUI to edit it,
 * although most of it can be altered to customise local installations.
 */
class Config
{
    /**
     * NPFX tags - name prefixes
     *
     * @return array<string>
     */
    public static function namePrefixes(): array
    {
        return [
            'Adm',
            'Amb',
            'Brig',
            'Can',
            'Capt',
            'Chan',
            'Chapln',
            'Cmdr',
            'Col',
            'Cpl',
            'Cpt',
            'Dr',
            'Gen',
            'Gov',
            'Hon',
            'Lady',
            'Lt',
            'Mr',
            'Mrs',
            'Ms',
            'Msgr',
            'Pfc',
            'Pres',
            'Prof',
            'Pvt',
            'Rabbi',
            'Rep',
            'Rev',
            'Sen',
            'Sgt',
            'Sir',
            'Sr',
            'Sra',
            'Srta',
            'Ven',
        ];
    }

    /**
     * FILE:FORM tags - file formats
     *
     * @return array<string>
     */
    public static function fileFormats(): array
    {
        return [
            'avi',
            'bmp',
            'gif',
            'jpeg',
            'mp3',
            'ole',
            'pcx',
            'png',
            'tiff',
            'wav',
        ];
    }

    /**
     * Facts and events that don't normally have a value
     *
     * @return array<string>
     */
    public static function emptyFacts(): array
    {
        return [
            'ADOP',
            'ANUL',
            'BAPL',
            'BAPM',
            'BARM',
            'BASM',
            'BIRT',
            'BLES',
            'BURI',
            'CENS',
            'CHAN',
            'CHR',
            'CHRA',
            'CONF',
            'CONL',
            'CREM',
            'DATA',
            'DEAT',
            'DIV',
            'DIVF',
            'EMIG',
            'ENDL',
            'ENGA',
            'FCOM',
            'GRAD',
            'HUSB',
            'IMMI',
            'MAP',
            'MARB',
            'MARC',
            'MARL',
            'MARR',
            'MARS',
            'NATU',
            'ORDN',
            'PROB',
            'RESI',
            'RETI',
            'SLGC',
            'SLGS',
            'WIFE',
            'WILL',
            '_HOL',
            '_NMR',
            '_NMAR',
            '_SEPR',
        ];
    }

    /**
     * Tags that don't require a PLAC subtag
     *
     * @return array<string>
     */
    public static function nonPlaceFacts(): array
    {
        return [
            'ENDL',
            'NCHI',
            'REFN',
            'SLGC',
            'SLGS',
        ];
    }

    /**
     * Tags that don't require a DATE subtag
     *
     * @return array<string>
     */
    public static function nonDateFacts(): array
    {
        return [
            'ABBR',
            'ADDR',
            'AFN',
            'ALIA',
            'AUTH',
            'CHIL',
            'EMAIL',
            'FAX',
            'FILE',
            'HUSB',
            'LANG',
            'NAME',
            'NCHI',
            'NOTE',
            'OBJE',
            'PHON',
            'PUBL',
            'REFN',
            'REPO',
            'RESN',
            'SEX',
            'SOUR',
            'SSN',
            'TEXT',
            'WIFE',
            'WWW',
            '_EMAIL',
        ];
    }

    /**
     * Tags that require a DATE:TIME as well as a DATE
     *
     * @return array<string>
     */
    public static function dateAndTime(): array
    {
        return [
            'BIRT',
            'DEAT',
        ];
    }

    /**
     * Level 2 tags that apply to specific Level 1 tags
     * Tags are applied in the order they appear here.
     *
     * @return array<string,array<string>>
     */
    public static function levelTwoTags(): array
    {
        return [
            'TYPE'     => [
                'EVEN',
                'FACT',
                'GRAD',
                'IDNO',
                'MARR',
                'ORDN',
                'SSN',
            ],
            'AGNC'     => [
                'EDUC',
                'GRAD',
                'OCCU',
                'ORDN',
                'RETI',
            ],
            'CALN'     => [
                'REPO',
            ],
            'CEME'     => [
                // CEME is NOT a valid 5.5.1 tag
                //'BURI',
            ],
            'RELA'     => [
                'ASSO',
                '_ASSO',
            ],
            'DATE'     => [
                'ADOP',
                'ANUL',
                'BAPL',
                'BAPM',
                'BARM',
                'BASM',
                'BIRT',
                'BLES',
                'BURI',
                'CENS',
                'CENS',
                'CHR',
                'CHRA',
                'CONF',
                'CONL',
                'CREM',
                'DEAT',
                'DIV',
                'DIVF',
                'DSCR',
                'EDUC',
                'EMIG',
                'ENDL',
                'ENGA',
                'EVEN',
                'FCOM',
                'GRAD',
                'IMMI',
                'MARB',
                'MARC',
                'MARL',
                'MARR',
                'MARS',
                'NATU',
                'OCCU',
                'ORDN',
                'PROB',
                'PROP',
                'RELI',
                'RESI',
                'RETI',
                'SLGC',
                'SLGS',
                'TITL',
                'WILL',
                '_TODO',
            ],
            'AGE'      => [
                'CENS',
                'DEAT',
            ],
            'TEMP'     => [
                'BAPL',
                'CONL',
                'ENDL',
                'SLGC',
                'SLGS',
            ],
            'PLAC'     => [
                'ADOP',
                'ANUL',
                'BAPL',
                'BAPM',
                'BARM',
                'BASM',
                'BIRT',
                'BLES',
                'BURI',
                'CENS',
                'CHR',
                'CHRA',
                'CONF',
                'CONL',
                'CREM',
                'DEAT',
                'DIV',
                'DIVF',
                'EDUC',
                'EMIG',
                'ENDL',
                'ENGA',
                'EVEN',
                'FCOM',
                'GRAD',
                'IMMI',
                'MARB',
                'MARC',
                'MARL',
                'MARR',
                'MARS',
                'NATU',
                'OCCU',
                'ORDN',
                'PROB',
                'PROP',
                'RELI',
                'RESI',
                'RETI',
                'SLGC',
                'SLGS',
                'SSN',
                'TITL',
                'WILL',
            ],
            'STAT'     => [
                'BAPL',
                'CONL',
                'ENDL',
                'SLGC',
                'SLGS',
            ],
            'ADDR'     => [
                'BAPM',
                'BIRT',
                'BURI',
                'CENS',
                'CHR',
                'CHRA',
                'CONF',
                'CREM',
                'DEAT',
                'EDUC',
                'EVEN',
                'GRAD',
                'MARR',
                'OCCU',
                'ORDN',
                'PROP',
                'RESI',
            ],
            'CAUS'     => [
                'DEAT',
            ],
            'PHON'     => [
                'OCCU',
                'RESI',
            ],
            'FAX'      => [
                'OCCU',
                'RESI',
            ],
            'WWW'      => [
                'OCCU',
                'RESI',
            ],
            'EMAIL'    => [
                'OCCU',
                'RESI',
            ],
            'HUSB'     => [
                'MARR',
            ],
            'WIFE'     => [
                'MARR',
            ],
            'FAMC'     => [
                'ADOP',
                'SLGC',
            ],
            'EVEN'     => [
                'DATA',
            ],
            '_WT_USER' => [
                '_TODO',
            ],
            // See https://bugs.launchpad.net/webtrees/+bug/1082666
            'RELI'     => [
                'CHR',
                'CHRA',
                'BAPM',
                'MARR',
                'BURI',
            ],
        ];
    }

    /**
     * A list of facts/events that generally have two associates
     * (two witnesses, two godparents, etc.)
     *
     * @return array<string>
     */
    public static function twoAssociates(): array
    {
        return [
            'CHR',
            'BAPM',
            'MARR',
        ];
    }
}
