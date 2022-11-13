<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Database;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection as DbalConnection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Exception;

/**
 * Extend doctrine/dbal to support prefixed table names.
 */
class Connection extends DbalConnection
{
    private string $prefix;

    /**
     * Initializes a new instance of the Connection class.
     *
     * @param string              $prefix
     * @param array<string,mixed> $params
     * @param Driver              $driver
     * @param Configuration|null  $config
     * @param EventManager|null   $eventManager
     *
     * @throws Exception
     */
    public function __construct(
        string $prefix,
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ) {
        $this->prefix = $prefix;

        parent::__construct($params, $driver, $config, $eventManager);
    }
}
