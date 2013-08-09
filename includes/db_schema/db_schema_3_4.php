<?php
// Update the database schema from version 3 to version 4
// - update the max_relation_path_length from a separate
// user setting and gedcom setting to a combined user-gedcom
// setting.
//
// Also clean out some old/unused values.
// 
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

self::exec(
	"INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value)".
	" SELECT u.user_id, g.gedcom_id, 'RELATIONSHIP_PATH_LENGTH', LEAST(us1.setting_value, gs1.setting_value)".
	" FROM   `##user` u".
	" CROSS  JOIN `##gedcom` g".
	" LEFT   JOIN `##user_setting`   us1 ON (u.user_id  =us1.user_id   AND us1.setting_name='max_relation_path')".
	" LEFT   JOIN `##user_setting`   us2 ON (u.user_id  =us2.user_id   AND us2.setting_name='relationship_privacy')".
	" LEFT   JOIN `##gedcom_setting` gs1 ON (g.gedcom_id=gs1.gedcom_id AND gs1.setting_name='MAX_RELATION_PATH_LENGTH')".
	" LEFT   JOIN `##gedcom_setting` gs2 ON (g.gedcom_id=gs2.gedcom_id AND gs2.setting_name='USE_RELATIONSHIP_PRIVACY')".
	" WHERE  us2.setting_value AND gs2.setting_value"
);

// Delete old/unused settings
self::exec(
	"DELETE FROM `##site_setting` WHERE setting_name IN ('SESSION_SAVE_PATH')"
);
self::exec(
	"DELETE FROM `##gedcom_setting` WHERE setting_name IN ('HOME_SITE_TEXT', 'HOME_SITE_URL', 'CHECK_MARRIAGE_RELATIONS', 'MAX_RELATION_PATH_LENGTH', 'USE_RELATIONSHIP_PRIVACY')"
);
self::exec(
	"DELETE FROM `##user_setting` WHERE setting_name IN ('loggedin', 'relationship_privacy', 'max_relation_path_length')"
);

// Fix Mc/Mac problems - See SVN9701
self::exec(
	"UPDATE `##name` SET n_surn=CONCAT('MC', SUBSTRING(n_surn, 4)) WHERE n_surn LIKE 'MC0%'"
);

// Update the version to indicate success
WT_Site::preference($schema_name, $next_version);
