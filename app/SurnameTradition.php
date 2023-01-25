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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;

/**
 * @deprecated - Will be removed in 2.2.0 - use the factory to create surname traditions.
 */
class SurnameTradition
{
    /**
     * Create a surname tradition object for a given surname tradition name.
     *
     * @param string $name Internal name of the surname tradition
     *
     * @return SurnameTraditionInterface
     * @deprecated - Will be removed in 2.2.0 - use the factory to create surname traditions.
     */
    public static function create(string $name): SurnameTraditionInterface
    {
        return Registry::surnameTraditionFactory()->make($name);
    }

    /**
     * A list of known surname traditions, with their descriptions
     *
     * @return array<string>
     * @deprecated - Will be removed in 2.2.0 - use the factory to create surname traditions.
     */
    public static function allDescriptions(): array
    {
        return Registry::surnameTraditionFactory()->list();
    }
}
