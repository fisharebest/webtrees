<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use function uasort;

/**
 * An event which never happened.
 */
class FamilyNonEvent extends AbstractElement
{
    protected const SUBTAGS = [
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
            'ANUL' => Registry::elementFactory()->make('FAM:ANUL')->label(),
            'CENS' => Registry::elementFactory()->make('FAM:CENS')->label(),
            'DIV'  => Registry::elementFactory()->make('FAM:DIV')->label(),
            'DIVF' => Registry::elementFactory()->make('FAM:DIVF')->label(),
            'ENGA' => Registry::elementFactory()->make('FAM:ENGA')->label(),
            'MARB' => Registry::elementFactory()->make('FAM:MARB')->label(),
            'MARC' => Registry::elementFactory()->make('FAM:MARC')->label(),
            'MARL' => Registry::elementFactory()->make('FAM:MARL')->label(),
            'MARS' => Registry::elementFactory()->make('FAM:MARS')->label(),
            'MARR' => Registry::elementFactory()->make('FAM:MARR')->label(),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}
