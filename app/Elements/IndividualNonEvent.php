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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;

use function strtoupper;

/**
 * An event which never happened.
 */
class IndividualNonEvent extends AbstractElement
{
    protected const array SUBTAGS = [
        'DATE' => '0:1',
        'NOTE' => '0:1',
        'SOUR' => '0:1',
    ];

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return strtoupper(parent::canonical($value));
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        $values = [
            ''     => '',
            'ADOP' => Registry::elementFactory()->make('INDI:ADOP')->label(),
            'BAPM' => Registry::elementFactory()->make('INDI:BAPM')->label(),
            'BARM' => Registry::elementFactory()->make('INDI:BARM')->label(),
            'BASM' => Registry::elementFactory()->make('INDI:BASM')->label(),
            'BIRT' => Registry::elementFactory()->make('INDI:BIRT')->label(),
            'BLES' => Registry::elementFactory()->make('INDI:BLES')->label(),
            'BURI' => Registry::elementFactory()->make('INDI:BURI')->label(),
            'CENS' => Registry::elementFactory()->make('INDI:CENS')->label(),
            'CHR'  => Registry::elementFactory()->make('INDI:CHR')->label(),
            'CHRA' => Registry::elementFactory()->make('INDI:CHRA')->label(),
            'CONF' => Registry::elementFactory()->make('INDI:CONF')->label(),
            'CREM' => Registry::elementFactory()->make('INDI:CREM')->label(),
            'DEAT' => Registry::elementFactory()->make('INDI:DEAT')->label(),
            'EMIG' => Registry::elementFactory()->make('INDI:EMIG')->label(),
            'FCOM' => Registry::elementFactory()->make('INDI:FCOM')->label(),
            'GRAD' => Registry::elementFactory()->make('INDI:GRAD')->label(),
            'IMMI' => Registry::elementFactory()->make('INDI:IMMI')->label(),
            'NATU' => Registry::elementFactory()->make('INDI:NATU')->label(),
            'ORDN' => Registry::elementFactory()->make('INDI:ORDN')->label(),
            'PROB' => Registry::elementFactory()->make('INDI:PROB')->label(),
            'RETI' => Registry::elementFactory()->make('INDI:RETI')->label(),
            'WILL' => Registry::elementFactory()->make('INDI:WILL')->label(),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}
