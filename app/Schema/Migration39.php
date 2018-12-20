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
 * Upgrade the database schema from version 39 to version 40.
 */
class Migration39 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        // The following migrations were once part of the favorites module.

        // Create the tables, as per PhpGedView 4.2.1
        Database::exec(
            "CREATE TABLE IF NOT EXISTS `##favorites` (" .
            " fv_id       INTEGER AUTO_INCREMENT NOT NULL," .
            " fv_username VARCHAR(32)            NOT NULL," .
            " fv_gid      VARCHAR(20)                NULL," .
            " fv_type     VARCHAR(15)                NULL," .
            " fv_file     VARCHAR(100)               NULL," .
            " fv_url      VARCHAR(255)               NULL," .
            " fv_title    VARCHAR(255)               NULL," .
            " fv_note     TEXT                       NULL," .
            " PRIMARY KEY (fv_id)," .
            "         KEY ix1 (fv_username)" .
            ") COLLATE utf8_unicode_ci ENGINE=InnoDB"
        );

        // Add the new columns
        try {
            Database::exec(
                "ALTER TABLE `##favorites`" .
                " CHANGE fv_id    favorite_id   INTEGER AUTO_INCREMENT NOT NULL," .
                " CHANGE fv_gid   xref          VARCHAR(20) NULL," .
                " CHANGE fv_type  favorite_type ENUM('INDI', 'FAM', 'SOUR', 'REPO', 'OBJE', 'NOTE', 'URL') NOT NULL," .
                " CHANGE fv_url   url           VARCHAR(255) NULL," .
                " CHANGE fv_title title         VARCHAR(255) NULL," .
                " CHANGE fv_note  note          VARCHAR(1000) NULL," .
                " ADD user_id   INTEGER     NULL AFTER favorite_id," .
                " ADD gedcom_id INTEGER NOT NULL AFTER user_id," .
                " DROP KEY ix1," .
                " ADD KEY news_ix1 (gedcom_id, user_id)"
            );
        } catch (PDOException $ex) {
            // Already updated?
        }

        // Migrate data from the old columns to the new ones
        try {
            Database::exec(
                "UPDATE `##favorites` f" .
                " LEFT JOIN `##gedcom` g ON (f.fv_file    =g.gedcom_name)" .
                " LEFT JOIN `##user`   u ON (f.fv_username=u.user_name)" .
                " SET f.gedcom_id=g.gedcom_id, f.user_id=u.user_id"
            );
        } catch (PDOException $ex) {
            // Already updated?
        }

        // Delete orphaned rows
        Database::exec(
            "DELETE FROM `##favorites` WHERE user_id IS NULL AND gedcom_id IS NULL"
        );

        // Delete the old column
        try {
            Database::exec(
                "ALTER TABLE `##favorites` DROP fv_username, DROP fv_file"
            );
        } catch (PDOException $ex) {
            // Already updated?
        }

        // Rename the table
        try {
            Database::exec(
                "RENAME TABLE `##favorites` TO `##favorite`"
            );
        } catch (PDOException $ex) {
            // Already updated?
        }

        // Add foreign key constraints
        // Delete any data that might violate the new constraints
        Database::exec(
            "DELETE FROM `##favorite`" .
            " WHERE user_id   NOT IN (SELECT user_id   FROM `##user`  )" .
            " OR    gedcom_id NOT IN (SELECT gedcom_id FROM `##gedcom`)"
        );

        // Add the new constraints
        try {
            Database::exec(
                "ALTER TABLE `##favorite`" .
                " ADD FOREIGN KEY `##favorite_fk1` (user_id  ) REFERENCES `##user`   (user_id) ON DELETE CASCADE," .
                " ADD FOREIGN KEY `##favorite_fk2` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) ON DELETE CASCADE"
            );
        } catch (PDOException $ex) {
            // Already updated?
        }
    }
}
