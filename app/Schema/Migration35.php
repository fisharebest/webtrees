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
namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;

/**
 * Upgrade the database schema from version 35 to version 36.
 */
class Migration35 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     */
    public function upgrade()
    {
        // Use LONGTEXT instead of TEXT and MEDIUMTEXT, and make NOT NULL.
        Database::exec("UPDATE `##news`   SET body          = '' WHERE body          IS NULL");
        Database::exec("UPDATE `##other`  SET o_gedcom      = '' WHERE o_gedcom      IS NULL");
        Database::exec("UPDATE `##places` SET p_std_soundex = '' WHERE p_std_soundex IS NULL");
        Database::exec("UPDATE `##places` SET p_dm_soundex  = '' WHERE p_dm_soundex  IS NULL");
        Database::exec("ALTER TABLE `##block_setting`  CHANGE setting_value setting_value LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##change`         CHANGE new_gedcom    new_gedcom    LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##change`         CHANGE old_gedcom    old_gedcom    LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##families`       CHANGE f_gedcom      f_gedcom      LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##individuals`    CHANGE i_gedcom      i_gedcom      LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##log`            CHANGE log_message   log_message   LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##media`          CHANGE m_gedcom      m_gedcom      LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##message`        CHANGE body          body          LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##module_setting` CHANGE setting_value setting_value LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##news`           CHANGE body          body          LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##other`          CHANGE o_gedcom      o_gedcom      LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##places`         CHANGE p_std_soundex p_std_soundex LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##places`         CHANGE p_dm_soundex  p_dm_soundex  LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        Database::exec("ALTER TABLE `##sources`        CHANGE s_gedcom      s_gedcom      LONGTEXT COLLATE utf8_unicode_ci NOT NULL");
        // Use LONGBLOB instead of MEDIUMBLOB.
        Database::exec("ALTER TABLE `##gedcom_chunk`   CHANGE chunk_data    chunk_data    LONGBLOB NOT NULL");
        Database::exec("ALTER TABLE `##session`        CHANGE session_data  session_data  LONGBLOB NOT NULL");
    }
}
