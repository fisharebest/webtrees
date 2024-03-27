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

namespace Fisharebest\Webtrees\DB\Drivers;

use Doctrine\DBAL\Driver\PDO\Connection;
use PDO;
use PDOException;
use RuntimeException;
use SensitiveParameter;

use function is_bool;
use function is_int;

/**
 * Common functionality for all drivers.
 */
trait DriverTrait
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function connect(
        #[SensitiveParameter]
        array $params,
    ): Connection {
        return new Connection($this->pdo);
    }

    /**
     * Prepare, bind and execute a select query.
     *
     * @param string                            $sql
     * @param array<bool|int|float|string|null> $bindings
     *
     * @return array<object>
     */
    public function query(string $sql, array $bindings = []): array
    {
        try {
            $statement = $this->pdo->prepare($sql);
        } catch (PDOException) {
            $statement = false;
        }

        if ($statement === false) {
            throw new RuntimeException('Failed to prepare statement: ' . $sql);
        }

        foreach ($bindings as $param => $value) {
            $type = match (true) {
                $value === null        => PDO::PARAM_NULL,
                is_bool(value: $value) => PDO::PARAM_BOOL,
                is_int(value: $value)  => PDO::PARAM_INT,
                default                => PDO::PARAM_STR,
            };

            if (is_int(value: $param)) {
                // Positional parameters are numeric, starting at 1.
                $statement->bindValue(param: $param + 1, value: $value, type: $type);
            } else {
                // Named parameters are (optionally) prefixed with a colon.
                $statement->bindValue(param: ':' . $param, value: $value, type: $type);
            }
        }

        if ($statement->execute()) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        }

        throw new RuntimeException('Failed to execute statement: ' . $sql);
    }
}
