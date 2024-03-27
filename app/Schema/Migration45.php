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

namespace Fisharebest\Webtrees\Schema;

use Doctrine\DBAL\Driver\AbstractMySQLDriver;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\DB\WebtreesSchema;

use function usort;
use function var_dump;

/**
 * Upgrade the database schema from version 45 to version 46.
 */
class Migration45 implements MigrationInterface
{
    /**
     * Upgrade to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        $platform = DB::getDBALConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping(dbType: 'enum', doctrineType: 'string');

        $schema_manager = DB::getDBALConnection()->createSchemaManager();
        $comparator     = $schema_manager->createComparator();
        $source         = $schema_manager->introspectSchema();
        $target         = WebtreesSchema::schema();

        // doctrine/dbal 4.0 does not have the concept of "saveSQL"
        foreach ($source->getTables() as $table) {
            if (!$target->hasTable($table->getName())) {
                $source->dropTable($table->getName());
            }
        }

        $schema_diff = $comparator->compareSchemas(oldSchema: $source, newSchema: $target);
        $queries     = $platform->getAlterSchemaSQL(diff: $schema_diff);

        // Workaround for https://github.com/doctrine/dbal/issues/6092
        $phase = static fn (string $query): int => match (true) {
            str_contains(haystack: $query, needle: 'DROP FOREIGN KEY') => 1,
            default                                                    => 2,
            str_contains(haystack: $query, needle: 'FOREIGN KEY')  => 3,
        };
        $fn = static fn (string $query1, string $query2): int => $phase(query: $query1) <=> $phase(query: $query2);
        usort(array: $queries, callback: $fn);

        foreach ($queries as $query) {
            echo '<p>' . $query . '</p>';
            DB::exec(sql: $query);
        }
        exit;
    }
}
