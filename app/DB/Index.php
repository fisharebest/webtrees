<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2024 webtrees development team
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

namespace Fisharebest\Webtrees\DB;

use Doctrine\DBAL\Schema\Index as DBALIndex;

/**
 * Fluent/immutable constructors for doctrine/dbal.
 */
final class Index extends DBALIndex
{
    /**
     * @param array<int,string> $columns
     */
    public function __construct(private readonly array $columns, string|null $name = null)
    {
        parent::__construct(name: $name, columns: $this->columns);
    }

    public function name(string $name): self
    {
        return new self(columns: $this->columns, name: $name);
    }
}
