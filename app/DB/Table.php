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

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Fisharebest\Webtrees\DB;

use function array_filter;
use function array_values;

final readonly class Table
{
    /** @var array<Column> */
    public array $columns;

    /** @var array<Index> */
    public array $indexes;

    /** @var array<PrimaryKeyConstraint> */
    public array $primary_keys;


    /** @var array<ForeignKeyConstraint> */
    public array $foreign_keys;

    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public string $name,
        Column|Index|ForeignKeyConstraint|PrimaryKeyConstraint ...$components,
    ) {
        $this->columns = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (Column|Index|ForeignKeyConstraint|PrimaryKeyConstraint $component): bool => $component instanceof Column,
            )
        );

        $this->primary_keys = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (Column|Index|ForeignKeyConstraint|PrimaryKeyConstraint $component): bool => $component instanceof PrimaryKeyConstraint,
            )
        );

        $this->indexes = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (Column|Index|ForeignKeyConstraint|PrimaryKeyConstraint $component): bool => $component instanceof Index,
            )
        );

        $this->foreign_keys = array_values(
            array: array_filter(
                array: $components,
                callback: static fn (Column|Index|ForeignKeyConstraint|PrimaryKeyConstraint $component): bool => $component instanceof ForeignKeyConstraint,
            )
        );
    }

    public function dropForeignKeys(): self
    {
        return new self(
            $this->name,
            ...$this->columns,
            ...$this->indexes,
            ...$this->primary_keys,
        );
    }

    public function toDBAL(): DBALTable
    {
        $table = DBALTable::editor()
            ->setUnquotedName(DB::prefix(identifier: $this->name));

        foreach ($this->columns as $column) {
            $table->addColumn($column);
        }

        foreach ($this->primary_keys as $primary_key) {
            $table->addPrimaryKeyConstraint($primary_key);
        }

        foreach ($this->indexes as $index) {
            $table->addIndex($index);
        }

        foreach ($this->foreign_keys as $foreign_key) {
            $table->addForeignKeyConstraint($foreign_key);
        }

        return $table->create();
    }
}
