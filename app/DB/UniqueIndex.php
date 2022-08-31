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

use Doctrine\DBAL\Schema\Index as DBALIndex;
use Fisharebest\Webtrees\DB;

use function implode;

/**
 * Simplify constructor arguments for doctrine/dbal.
 *
 * @internal - use DB::uniqueIndex()
 */
final class UniqueIndex implements ComponentInterface
{
    /**
     * @param array<array-key,string> $columns
     */
    public function __construct(private readonly array $columns)
    {
    }

    public function toDBAL(string $table): DBALIndex
    {
        return new DBALIndex(
            name: DB::prefix(implode('_', ['ux', $table, ...$this->columns])),
            columns: $this->columns,
            isUnique: true
        );
    }
}
