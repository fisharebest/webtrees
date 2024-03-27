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

use Doctrine\DBAL\Schema\Column as DBALColumn;

/**
 * Fluent/immutable constructors for doctrine/dbal.
 */
final class Column extends DBALColumn
{
    public function __construct(
        private readonly string $name,
        private readonly ColumnType $type,
        private readonly int $length = 0,
        private readonly int $precision = 0,
        private readonly int $scale = 0,
        private readonly bool $unsigned = false,
        private readonly bool $fixed = false,
        private readonly bool $nullable = false,
        private readonly float|int|string|null $default = null,
        private readonly bool $autoincrement = false,
        private readonly string|null $collation = null,
    ) {
        parent::__construct(
            name: $name,
            type: ColumnType::toDBALType(column_type: $type),
            options: [
                'length'          => $length,
                'precision'       => $precision,
                'scale'           => $scale,
                'unsigned'        => $unsigned,
                'fixed'           => $fixed,
                'notnull'         => !$nullable,
                'default'         => $default,
                'autoincrement'   => $autoincrement,
                'platformOptions' => ['collation' => $collation],
            ],
        );
    }

    public function autoincrement(): self
    {
        return new self(
            name: $this->name,
            type: $this->type,
            length: $this->length,
            precision: $this->precision,
            scale: $this->scale,
            unsigned: $this->unsigned,
            fixed: $this->fixed,
            nullable: $this->nullable,
            default: $this->default,
            autoincrement: true,
            collation: $this->collation,
        );
    }

    public function default(float|int|string $default): self
    {
        return new self(
            name: $this->name,
            type: $this->type,
            length: $this->length,
            precision: $this->precision,
            scale: $this->scale,
            unsigned: $this->unsigned,
            fixed: $this->fixed,
            nullable: $this->nullable,
            default: $default,
            autoincrement: $this->autoincrement,
            collation: $this->collation,
        );
    }

    public function fixed(): self
    {
        return new self(
            name: $this->name,
            type: $this->type,
            length: $this->length,
            precision: $this->precision,
            scale: $this->scale,
            unsigned: $this->unsigned,
            fixed: true,
            nullable: $this->nullable,
            default: $this->default,
            autoincrement: $this->autoincrement,
            collation: $this->collation,
        );
    }

    public function nullable(): self
    {
        return new self(
            name: $this->name,
            type: $this->type,
            length: $this->length,
            precision: $this->precision,
            scale: $this->scale,
            unsigned: $this->unsigned,
            fixed: $this->fixed,
            nullable: true,
            default: $this->default,
            autoincrement: $this->autoincrement,
            collation: $this->collation,
        );
    }
}
