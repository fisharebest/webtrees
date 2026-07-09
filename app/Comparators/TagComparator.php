<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Comparators;

use function array_search;

final class TagComparator
{
    public const array FACT_ORDER = [
        'SEX',
        'NAME',
        'BIRT',
        'ALIA',
        'ADOP',
        'CHR',
        'BAPM',
        'FCOM',
        'CONF',
        'BARM',
        'BASM',
        'EDUC',
        'GRAD',
        'EMIG',
        'IMMI',
        'NATU',
        'ENGA',
        'MARB',
        'MARC',
        'MARL',
        'MARR',
        'DIVF',
        'MARS',
        'DIV',
        'ANUL',
        'CENS',
        'OCCU',
        'RESI',
        'PROP',
        'CHRA',
        'RETI',
        'FACT',
        'EVEN',
        'NMR',
        'NCHI',
        'WILL',
        'DEAT',
        'CREM',
        'BURI',
        'PROB',
        'TITL',
        'COMM',
        'NATI',
        'CITN',
        'CAST',
        'RELI',
        'SSN',
        'IDNO',
        'TEMP',
        'SLGC',
        'BAPL',
        'CONL',
        'ENDL',
        'SLGS',
        '_FSFTID',
        'AFN',
        'REFN',
        'REF',
        'RIN',
        'OBJE',
        'NOTE',
        'SOUR',
        'CREA',
        'CHAN',
        '_TODO',
        '_UID',
    ];

    public static function order(string $tag): int
    {
        $order = array_search($tag, self::FACT_ORDER, true);

        if ($order === false) {
            // Should always find EVEN!
            $order = (int) array_search('EVEN', self::FACT_ORDER, true);
        }

        return $order;
    }

    public static function byOrder(string $first_tag, string $second_tag): int
    {
        return self::order($first_tag) <=> self::order($second_tag);
    }
}
