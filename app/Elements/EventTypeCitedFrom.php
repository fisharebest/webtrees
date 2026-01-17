<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;

use function uasort;

/**
 * EVENT_OR_FACT_CLASSIFICATION := {Size=1:15}
 * [ <EVENT_ATTRIBUTE_TYPE> ]
 * A code that indicates the type of event which was responsible for the source
 * entry being recorded. For example, if the entry was created to record a
 * birth of a child, then the type would be BIRT regardless of the assertions
 * made from that record, such as the mother's name or mother's birth date.
 * This will allow a prioritized best view choice and a determination of the
 * certainty associated with the source used in asserting the cited fact.
 */
class EventTypeCitedFrom extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 15;

    protected const array SUBTAGS = [
        'ROLE' => '0:1',
    ];

    protected const array EVENT_TYPES = [
        'FAM' => [
            'ANUL',
            'CENS',
            'DIV',
            'DIVF',
            'ENGA',
            'MARR',
            'MARB',
            'MARC',
            'MARL',
            'MARS',
            'EVEN',
        ],
        'INDI' => [
            'ADOP',
            'BAPM',
            'BARM',
            'BASM',
            'BIRT',
            'BLES',
            'BURI',
            'CAST',
            'CENS',
            'CHR',
            'CHRA',
            'CONF',
            'CREM',
            'DEAT',
            'EDUC',
            'EMIG',
            'EVEN',
            'FACT',
            'FCOM',
            'GRAD',
            'IMMI',
            'NATI',
            'NATU',
            'OCCU',
            'ORDN',
            'PROB',
            'PROP',
            'RELI',
            'RESI',
            'RETI',
            'TITL',
            'WILL',
        ],
    ];

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        $values = ['' => ''];

        foreach (self::EVENT_TYPES as $record_type => $tags) {
            foreach ($tags as $tag) {
                $element = Registry::elementFactory()->make($record_type . ':' . $tag);

                if (!$element instanceof UnknownElement) {
                    $values[$tag] = $element->label();
                }
            }
        }

        uasort($values, I18N::comparator());

        return $values;
    }
}
