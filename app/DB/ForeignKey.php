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

use Doctrine\DBAL\Schema\ForeignKeyConstraint as DBALForeignKey;
use Fisharebest\Webtrees\DB;

use function implode;

/**
 * Simplify constructor arguments for doctrine/dbal.
 *
 * @internal - use DB::foreignKey()
 */
final readonly class ForeignKey implements ComponentInterface
{
    /**
     * @param array<array-key,string> $local_columns
     * @param string                  $foreign_table
     * @param array<array-key,string> $foreign_columns
     * @param string                  $on_delete
     * @param string                  $on_update
     */
    public function __construct(
        private readonly array $local_columns,
        private readonly string $foreign_table,
        private readonly array $foreign_columns,
        private readonly string $on_delete = 'NO ACTION',
        private readonly string $on_update = 'NO ACTION',
    ) {
    }

    public function onDeleteCascade(): self
    {
        return new self(
            local_columns: $this->local_columns,
            foreign_table: $this->foreign_table,
            foreign_columns: $this->foreign_columns,
            on_delete: 'CASCADE',
            on_update: $this->on_update,
        );
    }

    public function onDeleteSetNull(): self
    {
        return new self(
            local_columns: $this->local_columns,
            foreign_table: $this->foreign_table,
            foreign_columns: $this->foreign_columns,
            on_delete: 'SET NULL',
            on_update: $this->on_update,
        );
    }

    public function onUpdateCascade(): self
    {
        return new self(
            local_columns: $this->local_columns,
            foreign_table: $this->foreign_table,
            foreign_columns: $this->foreign_columns,
            on_delete: $this->on_delete,
            on_update: 'CASCADE',
        );
    }

    public function toDBAL(string $table): DBALForeignKey
    {
        return new DBALForeignKey(
            localColumnNames: $this->local_columns,
            foreignTableName: DB::prefix($this->foreign_table),
            foreignColumnNames: $this->foreign_columns,
            name: DB::prefix(implode('_', ['fk', $table, ...$this->local_columns])),
            options: ['onDelete' => $this->on_delete, 'onUpdate' => $this->on_update],
        );
    }
}
