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
 * A level 0 individual record
 */
class IndividualRecord extends AbstractElement
{
    protected const SUBTAGS = [
        'ADOP' => '0:M',
        'AFN'  => '0:1',
        'ALIA' => '0:M',
        'ANCI' => '0:M',
        'ASSO' => '0:M',
        'BAPL' => '0:M',
        'BAPM' => '0:M',
        'BARM' => '0:M',
        'BASM' => '0:M',
        'BIRT' => '0:M',
        'BLES' => '0:M',
        'BURI' => '0:M',
        'CAST' => '0:M',
        'CENS' => '0:M',
        'CHAN' => '0:1',
        'CHR'  => '0:M',
        'CHRA' => '0:M',
        'CONF' => '0:M',
        'CONL' => '0:M',
        'CREM' => '0:M',
        'DEAT' => '0:M',
        'DESI' => '0:M',
        'DSCR' => '0:M',
        'EDUC' => '0:M',
        'EMIG' => '0:M',
        'ENDL' => '0:M',
        'EVEN' => '0:M',
        'FACT' => '0:M',
        'FAMC' => '0:M',
        'FAMS' => '0:M',
        'FCOM' => '0:M',
        'GRAD' => '0:M',
        'IDNO' => '0:M',
        'IMMI' => '0:M',
        'NAME' => '0:M',
        'NATI' => '0:M',
        'NATU' => '0:M',
        'NCHI' => '0:M',
        'NMR'  => '0:M',
        'NOTE' => '0:M',
        'OBJE' => '0:M',
        'OCCU' => '0:M',
        'ORDN' => '0:M',
        'PROB' => '0:M',
        'PROP' => '0:M',
        'REFN' => '0:M',
        'RELI' => '0:M',
        'RESI' => '0:M',
        'RESN' => '0:1',
        'RETI' => '0:M',
        'RFN'  => '0:1',
        'RIN'  => '0:1',
        'SEX'  => '0:1',
        'SLGC' => '0:M',
        'SOUR' => '0:M',
        'SSN'  => '0:M',
        'SUBM' => '0:M',
        'TITL' => '0:M',
        'WILL' => '0:M',
    ];
}
