<?php
// Update the database schema from version 27-28
// - delete unused settings and update indexes
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

// Delete old/unused settings
WT_DB::exec(
	"DELETE FROM `##gedcom_setting` WHERE setting_name IN ('USE_GEONAMES')"
);

try {
	// Indexes created by setup.php or schema update 17-18
	WT_DB::exec("ALTER TABLE `##site_access_rule` DROP INDEX ix1, DROP INDEX ix2, DROP INDEX ix3");
	// Indexes created by schema update 17-18
	WT_DB::exec("ALTER TABLE `##site_access_rule` DROP INDEX ix4, DROP INDEX ix5, DROP INDEX ix6");
} catch (Exception $ex) {
	// Already done?
}

// User data may contains duplicates - these will prevent us from creating the new indexes
WT_DB::exec(
	"DELETE t1 FROM `##site_access_rule` AS t1 JOIN (SELECT MIN(site_access_rule_id) AS site_access_rule_id, ip_address_end, ip_address_start, user_agent_pattern FROM `##site_access_rule`) AS t2 ON t1.ip_address_end = t2.ip_address_end AND t1.ip_address_start = t2.ip_address_start AND t1.user_agent_pattern = t2.user_agent_pattern AND t1.site_access_rule_id <> t2.site_access_rule_id"
);

// ix1 - covering index for visitor lookup
// ix2 - for total counts in admin page
try {
	WT_DB::exec(
		"ALTER TABLE `##site_access_rule`" .
		" ADD UNIQUE INDEX `##site_access_rule_ix1` (ip_address_end, ip_address_start, user_agent_pattern, rule)," .
		" ADD        INDEX `##site_access_rule_ix2` (rule)"
	);
} catch (Exception $ex) {
	// Already done?
}

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
