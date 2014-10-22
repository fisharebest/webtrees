<?php
// Update the database schema from version 17 to 18
// - add table to control site access
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

WT_DB::exec(
	"CREATE TABLE IF NOT EXISTS `##site_access_rule` (".
	" site_access_rule_id INTEGER          NOT NULL AUTO_INCREMENT,".
	" ip_address_start     INTEGER UNSIGNED NOT NULL DEFAULT 0,".
	" ip_address_end       INTEGER UNSIGNED NOT NULL DEFAULT 4294967295,".
	" user_agent_pattern   VARCHAR(255)     NOT NULL,".
	" rule                 ENUM('allow', 'deny', 'robot', 'unknown') NOT NULL DEFAULT 'unknown',".
	" comment              VARCHAR(255)     NOT NULL,".
	" updated              TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,".
	" PRIMARY KEY     (site_access_rule_id),".
	" UNIQUE  KEY ix1 (user_agent_pattern, ip_address_start, ip_address_end),".
	"         KEY ix2 (ip_address_start),".
	"         KEY ix3 (ip_address_end),".
	"         KEY ix4 (rule),".
	"         KEY ix5 (user_agent_pattern),".
	"         KEY ix6 (updated)".
	") ENGINE=InnoDB COLLATE=utf8_unicode_ci"
);

WT_DB::exec(
	"INSERT IGNORE INTO `##site_access_rule` (user_agent_pattern, rule, comment) VALUES".
	" ('Mozilla/5.0 (%) Gecko/% %/%', 'allow', 'Gecko-based browsers'),".
	" ('Mozilla/5.0 (%) AppleWebKit/% (KHTML, like Gecko)%', 'allow', 'WebKit-based browsers'),".
	" ('Opera/% (%) Presto/% Version/%', 'allow', 'Presto-based browsers'),".
	" ('Mozilla/% (compatible; MSIE %', 'allow', 'Trident-based browsers'),".
	" ('Mozilla/5.0 (compatible; Konqueror/%', 'allow', 'Konqueror browser')"
);

// Don't call "DROP TABLE IF EXISTS `##wt_ip_address`".
// We can’t easily/safely migrate the data, and the user may
// wish to migrate it manually.

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
