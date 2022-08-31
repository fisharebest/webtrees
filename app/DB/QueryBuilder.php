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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder as BaseQueryBuilder;

/**
 * Add table-prefixes to doctrine/dbal.
 */
class QueryBuilder extends BaseQueryBuilder
{
    public function __construct(Connection $connection, private readonly string $prefix = '')
    {
        parent::__construct($connection);
    }

    public function from($from, $alias = null): self
    {
        return parent::from($this->prefix . $from, $alias ?? $from);
    }

    public function insert(string $table): self
    {
        return parent::insert($this->prefix . $table);
    }

    public function update(string $table): self
    {
        return parent::update($this->prefix . $table);
    }

    public function delete(string $table): self
    {
        return parent::delete($this->prefix . $table);
    }
}
