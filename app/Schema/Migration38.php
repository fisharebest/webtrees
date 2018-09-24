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

namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;
use PDOException;

/**
 * Upgrade the database schema from version 38 to version 39.
 */
class Migration38 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     *
     * @return void
     */
    public function upgrade()
    {
        // The geographic data is now handled by the core code.
        // The following migrations were once part of the old googlemap module.
        try {
            // Create the tables, as per PhpGedView 4.2.1
            Database::exec(
                "CREATE TABLE IF NOT EXISTS `##placelocation` (" .
                " pl_id        INTEGER      NOT NULL," .
                " pl_parent_id INTEGER          NULL," .
                " pl_level     INTEGER          NULL," .
                " pl_place     VARCHAR(255)     NULL," .
                " pl_long      VARCHAR(30)      NULL," .
                " pl_lati      VARCHAR(30)      NULL," .
                " pl_zoom      INTEGER          NULL," .
                " pl_icon      VARCHAR(255)     NULL," .
                " PRIMARY KEY     (pl_id)," .
                "         KEY ix1 (pl_level)," .
                "         KEY ix2 (pl_long)," .
                "         KEY ix3 (pl_lati)," .
                "         KEY ix4 (pl_place)," .
                "         KEY ix5 (pl_parent_id)" .
                ") COLLATE utf8_unicode_ci ENGINE=InnoDB"
            );
        } catch (PDOException $ex) {
            // Already done?
        }

        try {
            Database::exec(
                "ALTER TABLE `##placelocation` ADD (" .
                " pl_media      VARCHAR(60)     NULL," .
                " sv_long       FLOAT           NOT NULL DEFAULT 0," .
                " sv_lati       FLOAT           NOT NULL DEFAULT 0," .
                " sv_bearing    FLOAT           NOT NULL DEFAULT 0," .
                " sv_elevation  FLOAT           NOT NULL DEFAULT 0," .
                " sv_zoom       FLOAT           NOT NULL DEFAULT 1" .
                ")"
            );
        } catch (PDOException $ex) {
            // Already done?
        }

        try {
            Database::exec(
                "ALTER TABLE `##placelocation`" .
                " DROP COLUMN pl_media," .
                " DROP COLUMN sv_long," .
                " DROP COLUMN sv_lati," .
                " DROP COLUMN sv_bearing," .
                " DROP COLUMN sv_elevation," .
                " DROP COLUMN sv_zoom," .
                " DROP INDEX ix1," .
                " DROP INDEX ix2," .
                " DROP INDEX ix3," .
                " DROP INDEX ix4," .
                " DROP INDEX ix5," .
                " ADD UNIQUE INDEX ix1 (pl_parent_id, pl_place)," .
                " ADD INDEX ix2 (pl_parent_id)," .
                " ADD INDEX ix3 (pl_place)"
            );
        } catch (PDOException $ex) {
            // Already done?
        }

        // Convert flag icons from .gif to .png
        Database::exec("UPDATE `##placelocation` SET pl_icon=REPLACE(pl_icon, '.gif', '.png')");

        // Delete old settings
        Database::exec("DELETE FROM `##module_setting` WHERE module_name='googlemap'");
        Database::exec("DELETE FROM `##module_privacy` WHERE module_name='googlemap'");
        Database::exec("DELETE FROM `##module` WHERE module_name='googlemap'");
    }
}
