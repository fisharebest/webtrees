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

use Doctrine\DBAL\Schema\Table as DBALTable;
use Fisharebest\Webtrees\DB;

use function array_filter;
use function array_values;

/**
 * Fluent/immutable constructors for doctrine/dbal.
 */
final class Table extends DBALTable
{
    /** @var array<int,Column> */
    private array $columns;

    /** @var array<int,Index> */
    private array $indexes;

    /** @var array<int,PrimaryKey> */
    private array $primary_keys;

    /** @var array<int,UniqueIndex> */
    private array $unique_indexes;

    /** @var array<int,ForeignKey> */
    private array $foreign_keys;

    public function __construct(private readonly string $name, Column|Index|UniqueIndex|ForeignKey|PrimaryKey ...$components)
    {
        $column_filter       = static fn (Column|Index|UniqueIndex|ForeignKey|PrimaryKey $component): bool => $component instanceof Column;
        $foreign_key_filter  = static fn (Column|Index|UniqueIndex|ForeignKey|PrimaryKey $component): bool => $component instanceof ForeignKey;
        $index_filter        = static fn (Column|Index|UniqueIndex|ForeignKey|PrimaryKey $component): bool => $component instanceof Index;
        $primary_key_filter  = static fn (Column|Index|UniqueIndex|ForeignKey|PrimaryKey $component): bool => $component instanceof PrimaryKey;
        $unique_index_filter = static fn (Column|Index|UniqueIndex|ForeignKey|PrimaryKey $component): bool => $component instanceof UniqueIndex;

        $this->columns        = array_values(array: array_filter(array: $components, callback: $column_filter));
        $this->foreign_keys   = array_values(array: array_filter(array: $components, callback: $foreign_key_filter));
        $this->indexes        = array_values(array: array_filter(array: $components, callback: $index_filter));
        $this->primary_keys   = array_values(array: array_filter(array: $components, callback: $primary_key_filter));
        $this->unique_indexes = array_values(array: array_filter(array: $components, callback: $unique_index_filter));

        array_walk(array: $this->indexes, callback: self::namedIndex(...));
        array_walk(array: $this->unique_indexes, callback: self::namedUniqueIndex(...));
        array_walk(array: $this->foreign_keys, callback: self::namedForeignKey(...));

        parent::__construct(
            name: DB::prefix($name),
            columns: $this->columns,
            indexes: [...$this->primary_keys, ...$this->unique_indexes, ...$this->indexes],
            uniqueConstraints: [],
            fkConstraints: $this->foreign_keys,
            options: [],
        );
    }

    private function namedIndex(Index &$index, int|string $n): void
    {
        $n     = 1 + (int) $n;
        $name  = DB::prefix($this->name . '_ix' . $n);
        $index = $index->name($name);
    }

    private function namedUniqueIndex(UniqueIndex &$unique_index, int|string $n): void
    {
        $n            = 1 + (int) $n;
        $name         = DB::prefix($this->name . '_ux' . $n);
        $unique_index = $unique_index->name($name);
    }

    private function namedForeignKey(ForeignKey &$foreign_key, int|string $n): void
    {
        $n           = 1 + (int) $n;
        $name        = DB::prefix($this->name . '_fk' . $n);
        $foreign_key = $foreign_key->name($name);
    }

    public function dropColumn(string $name): self
    {
        $columns = array_filter(array: $this->columns, callback: static fn (Column $column): bool => $column->getName() !== $name);

        return new self($this->name, ...$columns, ...$this->indexes, ...$this->primary_keys, ...$this->unique_keys, ...$this->foreign_keys);
    }
}
