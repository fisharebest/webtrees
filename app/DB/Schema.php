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

namespace Fisharebest\Webtrees\DB;

use Doctrine\DBAL\Schema\Schema as DBALSchema;

use function array_filter;
use function array_map;

/**
 * Fluent/immutable constructors for doctrine/dbal.
 */
final class Schema extends DBALSchema
{
    /** @param list<Table> $tables */
    public function __construct(private readonly array $tables)
    {
        parent::__construct(tables: $tables);
    }

    public function dropTable(string $name): self
    {
        return new self(
            tables: array_filter(
                array: $this->tables,
                callback: static fn(Table $table): bool => $table->name !== $name,
            )
        );
    }

    public function dropForeignKeys(): self
    {
        return new self(
            tables: array_map(
                callback: static fn(Table $table) => $table->dropForeignKeys(),
                array: $this->tables,
            )
        );
    }
}
