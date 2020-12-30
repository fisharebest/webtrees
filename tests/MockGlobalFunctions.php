<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
 * Class MockGlobalFunctions
 */
abstract class MockGlobalFunctions
{
    /**
     * Mock version of microtime()
     *
     * @param bool $get_as_float
     *
     * @return float|int[]
     */
    abstract public function microtime(bool $get_as_float);

    /**
     * Mock version of ini_get()
     *
     * @param string $varname
     *
     * @return string
     */
    abstract public function iniGet(string $varname): string;
}
