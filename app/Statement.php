<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use PDO;
use PDOStatement;
use stdClass;

/**
 * Extend PHP's native PDOStatement class.
 */
class Statement
{
    /** @var PDOStatement A prepared statement. */
    private $pdo_statement;

    /** @var bool Keep track of calls to execute(), so we can do it automatically. */
    private $executed = false;

    /**
     * Create a Statement object from a PDOStatement object.
     *
     * @param PDOStatement $pdo_statement
     */
    public function __construct(PDOStatement $pdo_statement)
    {
        $this->pdo_statement = $pdo_statement;
    }

    /**
     * Execute a query
     *
     * @param array $bind_variables
     *
     * @throws \Exception
     *
     * @return Statement
     */
    public function execute($bind_variables = []): Statement
    {
        if ($this->executed) {
            throw new \Exception('Statement::execute() called twice.');
        }

        // Parameters may be either named (e.g. :foo) or positional (e.g. ?).
        // Named parameters may take any type. Positional parameters are always strings.
        // Queries should use one format or the other.
        foreach ($bind_variables as $key => $bind_variable) {
            if (is_numeric($key)) {
                // Positional parameters are numeric (starting at 1)
                $key = 1 + $key;
            } else {
                // Named parameters are prefixed with a colon
                $key = ':' . $key;
            }
            switch (gettype($bind_variable)) {
                case 'NULL':
                    $this->pdo_statement->bindValue($key, $bind_variable, PDO::PARAM_NULL);
                    break;
                case 'boolean':
                    $this->pdo_statement->bindValue($key, (int) $bind_variable, PDO::PARAM_INT);
                    break;
                case 'integer':
                    $this->pdo_statement->bindValue($key, $bind_variable, PDO::PARAM_INT);
                    break;
                default:
                    $this->pdo_statement->bindValue($key, $bind_variable, PDO::PARAM_STR);
                    break;
            }
        }

        $this->pdo_statement->execute();

        // If it was a SELECT statement, we cannot run it again.
        $this->executed = strpos($this->pdo_statement->queryString, 'SELECT') === 0;

        return $this;
    }

    /**
     * Close the cursor, and mark it as not-executed, so we can execute
     * it again (perhaps with different parameters).
     *
     * @return void
     */
    public function closeCursor()
    {
        $this->pdo_statement->closeCursor();
        $this->executed = false;
    }

    /**
     * Fetch the next row from the cursor.
     *
     * Execute the query, if necessary. Typically when there are no parameters.
     *
     * @return stdClass|false
     */
    public function fetch()
    {
        if (!$this->executed) {
            $this->execute();
        }

        return $this->pdo_statement->fetch();
    }

    /**
     * Fetch all rows from the cursor, and close it.
     *
     * Execute the query, if necessary. Typically when there are no parameters.
     *
     * @return stdClass[]
     */
    public function fetchAll(): array
    {
        if (!$this->executed) {
            $this->execute();
        }

        $rows = $this->pdo_statement->fetchAll();
        $this->closeCursor();

        return $rows;
    }

    /**
     * Fetch one row, and close the cursor. e.g. SELECT * FROM foo WHERE pk=bar
     *
     * Execute the query, if necessary. Typically when there are no parameters.
     *
     * @return stdClass|null
     */
    public function fetchOneRow()
    {
        if (!$this->executed) {
            $this->execute();
        }

        $row = $this->pdo_statement->fetch();
        $this->closeCursor();

        return $row === false ? null : $row;
    }

    /**
     * Fetch one value and close the cursor. e.g. SELECT MAX(foo) FROM bar
     *
     * Execute the query, if necessary. Typically when there are no parameters.
     *
     * @return string|null
     */
    public function fetchOne()
    {
        if (!$this->executed) {
            $this->execute();
        }

        $value = $this->pdo_statement->fetchColumn();
        $this->closeCursor();

        return $value === false ? null : $value;
    }

    /**
     * Fetch two columns, and return an associative array of col1=>col2
     *
     * Execute the query, if necessary. Typically when there are no parameters.
     *
     * @return string[]
     */
    public function fetchAssoc(): array
    {
        if (!$this->executed) {
            $this->execute();
        }

        $rows = [];
        while ($row = $this->pdo_statement->fetch(PDO::FETCH_NUM)) {
            $rows[$row[0]] = $row[1];
        }
        $this->closeCursor();

        return $rows;
    }

    /**
     * Fetch all the first column, as an array.
     *
     * Execute the query, if necessary. Typically when there are no parameters.
     *
     * @return string[]
     */
    public function fetchOneColumn(): array
    {
        if (!$this->executed) {
            $this->execute();
        }

        $list = [];
        while ($row = $this->pdo_statement->fetch(PDO::FETCH_NUM)) {
            $list[] = $row[0];
        }
        $this->closeCursor();

        return $list;
    }

    /**
     * How many rows were affected by this statement.
     *
     * @return int
     */
    public function rowCount(): int
    {
        return $this->pdo_statement->rowCount();
    }
}
