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

use Doctrine\DBAL\Schema\Table as DBALTable;
use Exception;
use Fisharebest\Webtrees\DB;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function implode;

/**
 * Fluent/immutable constructors for doctrine/dbal.
 */
final class Table extends DBALTable
{
    /** @var list<Column> */
    public readonly array $columns;

    /** @var list<Index> */
    public readonly array $indexes;

    /** @var list<PrimaryKey> */
    public readonly array $primary_keys;

    /** @var list<UniqueIndex> */
    public readonly array $unique_indexes;

    /** @var list<ForeignKey> */
    public readonly array $foreign_keys;

    public function __construct(
        public readonly string $name,
        Column|Index|UniqueIndex|ForeignKey|PrimaryKey ...$components,
    ) {
        $this->columns = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (mixed $component): bool => $component instanceof Column,
            )
        );

        $this->primary_keys = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (mixed $component): bool => $component instanceof PrimaryKey,
            )
        );

        $indexes = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (mixed $component): bool => $component instanceof Index,
            )
        );

        $this->indexes = array_map(
            $this->namedIndex(...),
            $indexes,
            array_keys(array: $indexes),
        );

        $unique_indexes = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (mixed $component): bool => $component instanceof UniqueIndex,
            )
        );

        $this->unique_indexes = array_map(
            $this->namedUniqueIndex(...),
            $unique_indexes,
            array_keys(array: $unique_indexes),
        );

        $foreign_keys = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (mixed $component): bool => $component instanceof ForeignKey,
            )
        );

        $this->foreign_keys = array_map(
            $this->namedForeignKey(...),
            $foreign_keys,
            array_keys(array: $foreign_keys),
        );

        parent::__construct(
            name: DB::prefix(identifier: $name),
            columns: $this->columns,
            indexes: [...$this->primary_keys, ...$this->unique_indexes, ...$this->indexes],
            fkConstraints: $this->foreign_keys,
        );

        foreach ($this->foreign_keys as $foreign_key) {
            if (!$this->columnsAreIndexed(columnNames: $foreign_key->getLocalColumns())) {
                $columns = implode(separator: ', ', array: $foreign_key->getLocalColumns());
                throw new Exception(message: 'Table: ' . $name . ': Foreign key columns must be indexed: ' . $columns);
            }
        }
    }

    private function namedIndex(Index $index, int $n): Index
    {
        return $index->name(name: DB::prefix(identifier: $this->name . '_ix' . $n + 1));
    }

    private function namedUniqueIndex(UniqueIndex $unique_index, int $n): UniqueIndex
    {
        return $unique_index->name(name: DB::prefix(identifier: $this->name . '_ux' . $n + 1));
    }

    private function namedForeignKey(ForeignKey $foreign_key, int $n): ForeignKey
    {
        return $foreign_key->name(name: DB::prefix(identifier: $this->name . '_fk' . $n + 1));
    }

    public function dropColumn(string $name): self
    {
        $columns = array_filter(array: $this->columns, callback: static fn (Column $column): bool => $column->getName() !== $name);

        return new self(
            $this->name,
            ...$columns,
            ...$this->indexes,
            ...$this->primary_keys,
            ...$this->unique_indexes,
            ...$this->foreign_keys,
        );
    }

    public function dropForeignKeys(): self
    {
        return new self(
            $this->name,
            ...$this->columns,
            ...$this->indexes,
            ...$this->primary_keys,
            ...$this->unique_indexes,
        );
    }
}
