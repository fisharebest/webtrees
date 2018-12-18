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
 * Upgrade the database schema from version 37 to version 38.
 */
class Migration37 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     *
     * @return void
     */
    public function upgrade(): void
    {
        Database::prepare(
            "DROP TABLE IF EXISTS `##site_access_rule`"
        )->execute();

        try {
            Database::prepare(
                "INSERT INTO `##site_setting` (setting_name, setting_value)" .
                " SELECT 'next_xref', MAX(next_id) FROM `##next_id`"
            )->execute();
        } catch (PDOException $ex) {
            // Already done?
        }

        Database::prepare(
            "DELETE FROM `##gedcom_setting` WHERE setting_name in ('FAM_ID_PREFIX', 'GEDCOM_ID_PREFIX', 'MEDIA_ID_PREFIX', 'NOTE_ID_PREFIX', 'REPO_ID_PREFIX', 'SOURCE_ID_PREFIX')"
        )->execute();

        Database::prepare(
            "DROP TABLE IF EXISTS `##next_id`"
        )->execute();

        Database::prepare(
            "CREATE TABLE IF NOT EXISTS `##media_file` (" .
            "id                   INTEGER AUTO_INCREMENT NOT NULL PRIMARY KEY," .
            "m_id                 VARCHAR(20)  NOT NULL," .
            "m_file               INTEGER      NOT NULL," .
            "multimedia_file_refn VARCHAR(512) NOT NULL," . // GEDCOM only allows 30 characters
            "multimedia_format    VARCHAR(4)   NOT NULL," .
            "source_media_type    VARCHAR(15)  NOT NULL," .
            "descriptive_title    VARCHAR(248) NOT NULL," .
            "KEY `##media_file_ix1` (m_id, m_file)," .
            "KEY `##media_file_ix2` (m_file, m_id)," .
            "KEY `##media_file_ix3` (m_file, multimedia_file_refn)," .
            "KEY `##media_file_ix4` (m_file, multimedia_format)," .
            "KEY `##media_file_ix5` (m_file, source_media_type)," .
            "KEY `##media_file_ix6` (m_file, descriptive_title)" .
            ") ENGINE=InnoDB ROW_FORMAT=COMPRESSED COLLATE=utf8_unicode_ci"
        )->execute();

        try {
            Database::prepare(
                "INSERT INTO `##media_file` (" .
                "m_id, m_file, multimedia_file_refn, multimedia_format, source_media_type, descriptive_title" .
                ") SELECT m_id, m_file, m_filename, m_ext, LEFT(m_type, 15), m_titl FROM `##media`"
            )->execute();
        } catch (PDOException $ex) {
            // Already done?
        }

        try {
            Database::prepare(
                "ALTER TABLE `##media`" .
                " DROP COLUMN m_filename," .
                " DROP COLUMN m_ext," .
                " DROP COLUMN m_type," .
                " DROP COLUMN m_titl"
            )->execute();
        } catch (PDOException $ex) {
            // Already done?
        }
    }
}
