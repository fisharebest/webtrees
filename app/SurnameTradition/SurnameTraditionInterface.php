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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Enums\Sex;
use Fisharebest\Webtrees\Individual;

/**
 * Various cultures have different traditions for the use of surnames within families.
 * By providing defaults for new individuals, we can speed up data entry and reduce errors.
 */
interface SurnameTraditionInterface
{
    public function name(): string;

    public function description(): string;

    /**
     * A default/empty name
     */
    public function defaultName(): string;

    /**
     * @return list<string>
     */
    public function newChildNames(Individual|null $father, Individual|null $mother, Sex $sex): array;

    /**
     * @return list<string>
     */
    public function newParentNames(Individual $child, Sex $sex): array;

    /**
     * @return list<string>
     */
    public function newSpouseNames(Individual $spouse, Sex $sex): array;
}
