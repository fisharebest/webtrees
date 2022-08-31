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

namespace Fisharebest\Webtrees\DB;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\ForeignKeyConstraint as DBALForeignKey;
use Doctrine\DBAL\Schema\Index as DBALIndex;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Fisharebest\Webtrees\DB;

use function array_filter;
use function array_map;

/**
 * Simplify constructor arguments for doctrine/dbal.
 */
final class Table extends DBALTable implements ComponentInterface
{
    public function __construct(string $name, ComponentInterface ...$components)
    {
        $column_filter       = static fn (ComponentInterface $component) => $component instanceof Column;
        $foreign_key_filter  = static fn (ComponentInterface $component) => $component instanceof ForeignKey;
        $index_filter        = static fn (ComponentInterface $component) => $component instanceof Index;
        $primary_key_filter  = static fn (ComponentInterface $component) => $component instanceof PrimaryKey;
        $unique_index_filter = static fn (ComponentInterface $component) => $component instanceof UniqueIndex;

        $columns        = array_filter(array: $components, callback: $column_filter);
        $foreign_keys   = array_filter(array: $components, callback: $foreign_key_filter);
        $indexes        = array_filter(array: $components, callback: $index_filter);
        $primary_keys   = array_filter(array: $components, callback: $primary_key_filter);
        $unique_indexes = array_filter(array: $components, callback: $unique_index_filter);

        $column_mapper       = static fn (Column $column): DBALColumn => $column->toDBAL();
        $foreign_key_mapper  = static fn (ForeignKey $foreign_key): DBALForeignKey => $foreign_key->toDBAL($name);
        $index_mapper        = static fn (Index $index): DBALIndex => $index->toDBAL($name);
        $primary_key_mapper  = static fn (PrimaryKey $primary_key): DBALIndex => $primary_key->toDBAL();
        $unique_index_mapper = static fn (UniqueIndex $unique_index): DBALIndex => $unique_index->toDBAL($name);

        $columns        = array_map(callback: $column_mapper, array: $columns);
        $foreign_keys   = array_map(callback: $foreign_key_mapper, array: $foreign_keys);
        $indexes        = array_map(callback: $index_mapper, array: $indexes);
        $primary_keys   = array_map(callback: $primary_key_mapper, array: $primary_keys);
        $unique_indexes = array_map(callback: $unique_index_mapper, array: $unique_indexes);

        parent::__construct(
            name: DB::prefix($name),
            columns: $columns,
            indexes: [...$primary_keys, ...$unique_indexes, ...$indexes],
            uniqueConstraints: [],
            fkConstraints: $foreign_keys,
            options: [],
        );
    }
}
