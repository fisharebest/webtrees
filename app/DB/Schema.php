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

use function array_any;
use function array_filter;
use function array_map;

final readonly class Schema
{
    /** @param array<Table> $tables */
    public function __construct(
        private array $tables,
    ) {
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

    public function hasTable(string $name): bool
    {
        return array_any($this->tables, static fn (Table $table): bool => $table->name === $name);
    }

    public function toDBAL(): DBALSchema
    {
        $tables = array_map(
            static fn (Table $table) => $table->toDBAL(),
            $this->tables,
        );

        return new DBALSchema(tables: $tables);
    }
}
