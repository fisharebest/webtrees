<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Site;

/**
 * Upgrade the database schema from version 0 (empty database) to version 1.
 */
class Migration0 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##gedcom` (" .
			" gedcom_id     INTEGER AUTO_INCREMENT NOT NULL," .
			" gedcom_name   VARCHAR(255)           NOT NULL," .
			" sort_order    INTEGER                NOT NULL DEFAULT 0," .
			" PRIMARY KEY                (gedcom_id)," .
			" UNIQUE  KEY `##gedcom_ix1` (gedcom_name)," .
			"         KEY `##gedcom_ix2` (sort_order)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##site_setting` (" .
			" setting_name  VARCHAR(32)  NOT NULL," .
			" setting_value VARCHAR(255) NOT NULL," .
			" PRIMARY KEY (setting_name)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##gedcom_setting` (" .
			" gedcom_id     INTEGER      NOT NULL," .
			" setting_name  VARCHAR(32)  NOT NULL," .
			" setting_value VARCHAR(255) NOT NULL," .
			" PRIMARY KEY                        (gedcom_id, setting_name)," .
			" FOREIGN KEY `##gedcom_setting_fk1` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##user` (" .
			" user_id   INTEGER AUTO_INCREMENT NOT NULL," .
			" user_name VARCHAR(32)            NOT NULL," .
			" real_name VARCHAR(64)            NOT NULL," .
			" email     VARCHAR(64)            NOT NULL," .
			" password  VARCHAR(128)           NOT NULL," .
			" PRIMARY KEY              (user_id)," .
			" UNIQUE  KEY `##user_ix1` (user_name)," .
			" UNIQUE  KEY `##user_ix2` (email)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##user_setting` (" .
			" user_id       INTEGER      NOT NULL," .
			" setting_name  VARCHAR(32)  NOT NULL," .
			" setting_value VARCHAR(255) NOT NULL," .
			" PRIMARY KEY                      (user_id, setting_name)," .
			" FOREIGN KEY `##user_setting_fk1` (user_id) REFERENCES `##user` (user_id) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##user_gedcom_setting` (" .
			" user_id       INTEGER      NOT NULL," .
			" gedcom_id     INTEGER      NOT NULL," .
			" setting_name  VARCHAR(32)  NOT NULL," .
			" setting_value VARCHAR(255) NOT NULL," .
			" PRIMARY KEY                             (user_id, gedcom_id, setting_name)," .
			" FOREIGN KEY `##user_gedcom_setting_fk1` (user_id)   REFERENCES `##user` (user_id) /* ON DELETE CASCADE */," .
			" FOREIGN KEY `##user_gedcom_setting_fk2` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##log` (" .
			" log_id      INTEGER AUTO_INCREMENT NOT NULL," .
			" log_time    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP," .
			" log_type    ENUM('auth', 'config', 'debug', 'edit', 'error', 'media', 'search') NOT NULL," .
			" log_message TEXT         NOT NULL," .
			" ip_address  VARCHAR(40)  NOT NULL," .
			" user_id     INTEGER          NULL," .
			" gedcom_id   INTEGER          NULL," .
			" PRIMARY KEY             (log_id)," .
			"         KEY `##log_ix1` (log_time)," .
			"         KEY `##log_ix2` (log_type)," .
			"         KEY `##log_ix3` (ip_address)," .
			" FOREIGN KEY `##log_fk1` (user_id)   REFERENCES `##user`(user_id) /* ON DELETE SET NULL */," .
			" FOREIGN KEY `##log_fk2` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE SET NULL */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##change` (" .
			" change_id      INTEGER AUTO_INCREMENT                  NOT NULL," .
			" change_time    TIMESTAMP                               NOT NULL DEFAULT CURRENT_TIMESTAMP," .
			" status         ENUM('accepted', 'pending', 'rejected') NOT NULL DEFAULT 'pending'," .
			" gedcom_id      INTEGER                                 NOT NULL," .
			" xref           VARCHAR(20)                             NOT NULL," .
			" old_gedcom     MEDIUMTEXT                              NOT NULL," .
			" new_gedcom     MEDIUMTEXT                              NOT NULL," .
			" user_id        INTEGER                                 NOT NULL," .
			" PRIMARY KEY                (change_id)," .
			"         KEY `##change_ix1` (gedcom_id, status, xref)," .
			" FOREIGN KEY `##change_fk1` (user_id)   REFERENCES `##user` (user_id) /* ON DELETE RESTRICT */," .
			" FOREIGN KEY `##change_fk2` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##message` (" .
			" message_id INTEGER AUTO_INCREMENT NOT NULL," .
			" sender     VARCHAR(64)            NOT NULL," . // username or email address
			" ip_address VARCHAR(40)            NOT NULL," . // long enough for IPv6
			" user_id    INTEGER                NOT NULL," .
			" subject    VARCHAR(255)           NOT NULL," .
			" body       TEXT                   NOT NULL," .
			" created    TIMESTAMP              NOT NULL DEFAULT CURRENT_TIMESTAMP," .
			" PRIMARY KEY                 (message_id)," .
			" FOREIGN KEY `##message_fk1` (user_id)   REFERENCES `##user` (user_id) /* ON DELETE RESTRICT */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##default_resn` (" .
			" default_resn_id INTEGER AUTO_INCREMENT                             NOT NULL," .
			" gedcom_id       INTEGER                                            NOT NULL," .
			" xref            VARCHAR(20)                                            NULL," .
			" tag_type        VARCHAR(15)                                            NULL," .
			" resn            ENUM ('none', 'privacy', 'confidential', 'hidden') NOT NULL," .
			" comment         VARCHAR(255)                                           NULL," .
			" updated         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP," .
			" PRIMARY KEY                      (default_resn_id)," .
			" UNIQUE  KEY `##default_resn_ix1` (gedcom_id, xref, tag_type)," .
			" FOREIGN KEY `##default_resn_fk1` (gedcom_id)  REFERENCES `##gedcom` (gedcom_id)" .
			") ENGINE=InnoDB COLLATE=utf8_unicode_ci"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##individuals` (" .
			" i_id     VARCHAR(20)         NOT NULL," .
			" i_file   INTEGER             NOT NULL," .
			" i_rin    VARCHAR(20)         NOT NULL," .
			" i_sex    ENUM('U', 'M', 'F') NOT NULL," .
			" i_gedcom MEDIUMTEXT          NOT NULL," .
			" PRIMARY KEY                     (i_id, i_file)," .
			" UNIQUE  KEY `##individuals_ix1` (i_file, i_id)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##families` (" .
			" f_id      VARCHAR(20)  NOT NULL," .
			" f_file    INTEGER      NOT NULL," .
			" f_husb    VARCHAR(20)      NULL," .
			" f_wife    VARCHAR(20)      NULL," .
			" f_gedcom  MEDIUMTEXT   NOT NULL," .
			" f_numchil INTEGER      NOT NULL," .
			" PRIMARY KEY                  (f_id, f_file)," .
			" UNIQUE  KEY `##families_ix1` (f_file, f_id)," .
			"         KEY `##families_ix2` (f_husb)," .
			"         KEY `##families_ix3` (f_wife)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##places` (" .
			" p_id          INTEGER AUTO_INCREMENT NOT NULL," .
			" p_place       VARCHAR(150)               NULL," .
			" p_parent_id   INTEGER                    NULL," .
			" p_file        INTEGER               NOT  NULL," .
			" p_std_soundex TEXT                       NULL," .
			" p_dm_soundex  TEXT                       NULL," .
			" PRIMARY KEY                (p_id)," .
			"         KEY `##places_ix1` (p_file, p_place)," .
			" UNIQUE  KEY `##places_ix2` (p_parent_id, p_file, p_place)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##placelinks` (" .
			" pl_p_id INTEGER NOT NULL," .
			" pl_gid  VARCHAR(20)  NOT NULL," .
			" pl_file INTEGER  NOT NULL," .
			" PRIMARY KEY                    (pl_p_id, pl_gid, pl_file)," .
			"         KEY `##placelinks_ix1` (pl_p_id)," .
			"         KEY `##placelinks_ix2` (pl_gid)," .
			"         KEY `##placelinks_ix3` (pl_file)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##dates` (" .
			" d_day        TINYINT     NOT NULL," .
			" d_month      CHAR(5)         NULL," .
			" d_mon        TINYINT     NOT NULL," .
			" d_year       SMALLINT    NOT NULL," .
			" d_julianday1 MEDIUMINT   NOT NULL," .
			" d_julianday2 MEDIUMINT   NOT NULL," .
			" d_fact       VARCHAR(15) NOT NULL," .
			" d_gid        VARCHAR(20) NOT NULL," .
			" d_file       INTEGER     NOT NULL," .
			" d_type       ENUM ('@#DGREGORIAN@', '@#DJULIAN@', '@#DHEBREW@', '@#DFRENCH R@', '@#DHIJRI@', '@#DROMAN@', '@#DJALALI@') NOT NULL," .
			" KEY `##dates_ix1` (d_day)," .
			" KEY `##dates_ix2` (d_month)," .
			" KEY `##dates_ix3` (d_mon)," .
			" KEY `##dates_ix4` (d_year)," .
			" KEY `##dates_ix5` (d_julianday1)," .
			" KEY `##dates_ix6` (d_julianday2)," .
			" KEY `##dates_ix7` (d_gid)," .
			" KEY `##dates_ix8` (d_file)," .
			" KEY `##dates_ix9` (d_type)," .
			" KEY `##dates_ix10` (d_fact, d_gid)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##media` (" .
			" m_id       VARCHAR(20)            NOT NULL," .
			" m_ext      VARCHAR(6)                 NULL," .
			" m_type     VARCHAR(20)                NULL," .
			" m_titl     VARCHAR(255)               NULL," .
			" m_filename VARCHAR(512)               NULL," .
			" m_file     INTEGER                NOT NULL," .
			" m_gedcom   MEDIUMTEXT                 NULL," .
			" PRIMARY KEY               (m_file, m_id)," .
			" UNIQUE  KEY `##media_ix1` (m_id, m_file)," .
			"         KEY `##media_ix2` (m_ext, m_type)," .
			"         KEY `##media_ix3` (m_titl)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##next_id` (" .
			" gedcom_id   INTEGER     NOT NULL," .
			" record_type VARCHAR(15) NOT NULL," .
			" next_id     DECIMAL(20) NOT NULL," .
			" PRIMARY KEY                 (gedcom_id, record_type)," .
			" FOREIGN KEY `##next_id_fk1` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##other` (" .
			" o_id     VARCHAR(20) NOT NULL," .
			" o_file   INTEGER     NOT NULL," .
			" o_type   VARCHAR(15) NOT NULL," .
			" o_gedcom MEDIUMTEXT      NULL," .
			" PRIMARY KEY               (o_id, o_file)," .
			" UNIQUE  KEY `##other_ix1` (o_file, o_id)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##sources` (" .
			" s_id     VARCHAR(20)    NOT NULL," .
			" s_file   INTEGER        NOT NULL," .
			" s_name   VARCHAR(255)   NOT NULL," .
			" s_gedcom MEDIUMTEXT     NOT NULL," .
			" PRIMARY KEY                 (s_id, s_file)," .
			" UNIQUE  KEY `##sources_ix1` (s_file, s_id)," .
			"         KEY `##sources_ix2` (s_name)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##link` (" .
			" l_file    INTEGER     NOT NULL," .
			" l_from    VARCHAR(20) NOT NULL," .
			" l_type    VARCHAR(15) NOT NULL," .
			" l_to      VARCHAR(20) NOT NULL," .
			" PRIMARY KEY              (l_from, l_file, l_type, l_to)," .
			" UNIQUE  KEY `##link_ix1` (l_to, l_file, l_type, l_from)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##name` (" .
			" n_file             INTEGER      NOT NULL," .
			" n_id               VARCHAR(20)  NOT NULL," .
			" n_num              INTEGER      NOT NULL," .
			" n_type             VARCHAR(15)  NOT NULL," .
			" n_sort             VARCHAR(255) NOT NULL," . // e.g. “GOGH,VINCENT WILLEM”
			" n_full             VARCHAR(255) NOT NULL," . // e.g. “Vincent Willem van GOGH”
			// These fields are only used for INDI records
			" n_surname          VARCHAR(255)     NULL," . // e.g. “van GOGH”
			" n_surn             VARCHAR(255)     NULL," . // e.g. “GOGH”
			" n_givn             VARCHAR(255)     NULL," . // e.g. “Vincent Willem”
			" n_soundex_givn_std VARCHAR(255)     NULL," .
			" n_soundex_surn_std VARCHAR(255)     NULL," .
			" n_soundex_givn_dm  VARCHAR(255)     NULL," .
			" n_soundex_surn_dm  VARCHAR(255)     NULL," .
			" PRIMARY KEY              (n_id, n_file, n_num)," .
			"         KEY `##name_ix1` (n_full, n_id, n_file)," .
			"         KEY `##name_ix2` (n_surn, n_file, n_type, n_id)," .
			"         KEY `##name_ix3` (n_givn, n_file, n_type, n_id)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##module` (" .
			" module_name   VARCHAR(32)                 NOT NULL," .
			" status        ENUM('enabled', 'disabled') NOT NULL DEFAULT 'enabled'," .
			" tab_order     INTEGER                         NULL, " .
			" menu_order    INTEGER                         NULL, " .
			" sidebar_order INTEGER                         NULL," .
			" PRIMARY KEY (module_name)" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##module_setting` (" .
			" module_name   VARCHAR(32) NOT NULL," .
			" setting_name  VARCHAR(32) NOT NULL," .
			" setting_value MEDIUMTEXT  NOT NULL," .
			" PRIMARY KEY                        (module_name, setting_name)," .
			" FOREIGN KEY `##module_setting_fk1` (module_name) REFERENCES `##module` (module_name) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##module_privacy` (" .
			" module_name   VARCHAR(32) NOT NULL," .
			" gedcom_id     INTEGER     NOT NULL," .
			" component     ENUM('block', 'chart', 'menu', 'report', 'sidebar', 'tab', 'theme') NOT NULL," .
			" access_level  TINYINT     NOT NULL," .
			" PRIMARY KEY                        (module_name, gedcom_id, component)," .
			" FOREIGN KEY `##module_privacy_fk1` (module_name) REFERENCES `##module` (module_name) /* ON DELETE CASCADE */," .
			" FOREIGN KEY `##module_privacy_fk2` (gedcom_id)   REFERENCES `##gedcom` (gedcom_id)   /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##block` (" .
			" block_id    INTEGER AUTO_INCREMENT NOT NULL," .
			" gedcom_id   INTEGER                    NULL," .
			" user_id     INTEGER                    NULL," .
			" xref        VARCHAR(20)                NULL," .
			" location    ENUM('main', 'side')       NULL," .
			" block_order INTEGER                NOT NULL," .
			" module_name VARCHAR(32)            NOT NULL," .
			" PRIMARY KEY               (block_id)," .
			" FOREIGN KEY `##block_fk1` (gedcom_id)   REFERENCES `##gedcom` (gedcom_id),  /* ON DELETE CASCADE */" .
			" FOREIGN KEY `##block_fk2` (user_id)     REFERENCES `##user`   (user_id),    /* ON DELETE CASCADE */" .
			" FOREIGN KEY `##block_fk3` (module_name) REFERENCES `##module` (module_name) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##block_setting` (" .
			" block_id      INTEGER     NOT NULL," .
			" setting_name  VARCHAR(32) NOT NULL," .
			" setting_value TEXT        NOT NULL," .
			" PRIMARY KEY                       (block_id, setting_name)," .
			" FOREIGN KEY `##block_setting_fk1` (block_id) REFERENCES `##block` (block_id) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);
		Database::exec(
			"CREATE TABLE IF NOT EXISTS `##hit_counter` (" .
			" gedcom_id      INTEGER     NOT NULL," .
			" page_name      VARCHAR(32) NOT NULL," .
			" page_parameter VARCHAR(32) NOT NULL," .
			" page_count     INTEGER     NOT NULL," .
			" PRIMARY KEY                     (gedcom_id, page_name, page_parameter)," .
			" FOREIGN KEY `##hit_counter_fk1` (gedcom_id) REFERENCES `##gedcom` (gedcom_id) /* ON DELETE CASCADE */" .
			") COLLATE utf8_unicode_ci ENGINE=InnoDB"
		);

		// Set the default site preferences
		Site::setPreference('INDEX_DIRECTORY', 'data/');
		Site::setPreference('USE_REGISTRATION_MODULE', '1');
		Site::setPreference('ALLOW_USER_THEMES', '1');
		Site::setPreference('ALLOW_CHANGE_GEDCOM', '1');
		Site::setPreference('SESSION_TIME', '7200');
		Site::setPreference('SMTP_ACTIVE', 'internal');
		Site::setPreference('SMTP_HOST', 'localhost');
		Site::setPreference('SMTP_PORT', '25');
		Site::setPreference('SMTP_AUTH', '1');
		Site::setPreference('SMTP_AUTH_USER', '');
		Site::setPreference('SMTP_AUTH_PASS', '');
		Site::setPreference('SMTP_SSL', 'none');
		Site::setPreference('SMTP_HELO', $_SERVER['SERVER_NAME']);
		Site::setPreference('SMTP_FROM_NAME', $_SERVER['SERVER_NAME']);

		// Search for all installed modules, and enable them.
		Module::getInstalledModules('enabled');
	}
}
