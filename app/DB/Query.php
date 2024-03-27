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

use Fisharebest\Webtrees\Arr;
use Fisharebest\Webtrees\DB;
use LogicException;

/**
 * Simplify constructor arguments for doctrine/dbal.
 *
 * @internal - use DB::select(), DB::update(), DB::insertInto(), DB::deleteFrom()
 */
final class Query
{
    /**
     * @param array<int|Expression,string|Expression> $columns
     */
    public function __construct(
        private readonly array $columns = [],
        private readonly bool $distinct = false,
        private readonly string $table = '',
        private readonly int $offset = 0,
        private readonly int $limit = 0,
    ) {
    }

    public function distinct(): self
    {
        return new self(
            columns: $this->columns,
            distinct: true,
            table: $this->table,
            offset: $this->offset,
            limit: $this->limit,
        );
    }

    public function from(string $table): self
    {
        return new self(
            columns: $this->columns,
            distinct: $this->distinct,
            table: DB::prefix($table),
            offset: $this->offset,
            limit: $this->limit,
        );
    }

    /**
     * This is an update query.  Return the count of updated rows.
     *
     * @param array<string,string|Expression> $updates
     *
     * @return int
     */
    public function set(array $updates): int
    {
        if (
            $this->columns !== [] ||
            $this->distinct === true ||
            $this->table === '' ||
            $this->offset !== 0 ||
            $this->limit !== 0
        ) {
            throw new LogicException('Invalid SQL query definition');
        }

        return 0;
    }

    /**
     * @return Arr<int,object>
     */
    public function rows(): Arr
    {
        return new Arr();
    }

    /**
     * @return Arr<int|string,int|string>
     */
    public function pluck(): Arr
    {
        return new Arr();
    }

    public function firstRow(): object
    {
        return (object) [];
    }

    public function first(): string|int|float|null
    {
        return 0;
    }
}
