<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Schema\MigrationInterface;
use Fisharebest\Webtrees\Schema\SeedDefaultResnTable;
use Fisharebest\Webtrees\Schema\SeedGedcomTable;
use Fisharebest\Webtrees\Schema\SeedUserTable;
use Fisharebest\Webtrees\Site;
use Illuminate\Database\Capsule\Manager as DB;
use PDOException;

/**
 * Update the database schema.
 */
class MigrationService
{
    /**
     * Run a series of scripts to bring the database schema up to date.
     *
     * @param string $namespace      Where to find our MigrationXXX classes
     * @param string $schema_name    Which schema to update.
     * @param int    $target_version Upgrade to this version
     *
     * @return bool  Were any updates applied
     * @throws PDOException
     */
    public function updateSchema(string $namespace, string $schema_name, int $target_version): bool
    {
        try {
            $current_version = (int) Site::getPreference($schema_name);
        } catch (PDOException) {
            // During initial installation, the site_preference table won’t exist.
            $current_version = 0;
        }

        if ($current_version < $target_version) {
            try {
                $this->transactionalTables();
            } catch (PDOException) {
                // There is probably nothing we can do.
            }
        }

        $updates_applied = false;

        // Update the schema, one version at a time.
        while ($current_version < $target_version) {
            $class = $namespace . '\\Migration' . $current_version;
            /** @var MigrationInterface $migration */
            $migration = new $class();
            $migration->upgrade();
            $current_version++;
            Site::setPreference($schema_name, (string) $current_version);
            $updates_applied = true;
        }

        return $updates_applied;
    }

    /**
     * Upgrades from older installations may have MyISAM or other non-transactional tables.
     * These could prevent us from creating foreign key constraints.
     *
     * @return void
     * @throws PDOException
     */
    private function transactionalTables(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            return;
        }

        $sql = "SELECT table_name FROM information_schema.tables JOIN information_schema.engines USING (engine) WHERE table_schema = ? AND LEFT(table_name, ?) = ? AND transactions <> 'YES'";

        $bindings = [
            $connection->getDatabaseName(),
            mb_strlen($connection->getTablePrefix()),
            $connection->getTablePrefix(),
        ];

        $rows = DB::connection()->select($sql, $bindings);

        foreach ($rows as $row) {
            $table = $row->TABLE_NAME ?? $row->table_name;
            $alter_sql = 'ALTER TABLE `' . $table . '` ENGINE=InnoDB';
            DB::connection()->statement($alter_sql);
        }
    }

    /**
     *  Write default data to the database.
     *
     * @return void
     */
    public function seedDatabase(): void
    {
        (new SeedUserTable())->run();
        (new SeedGedcomTable())->run();
        (new SeedDefaultResnTable())->run();
    }
}
