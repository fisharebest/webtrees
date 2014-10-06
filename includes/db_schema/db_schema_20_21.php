<?php
// Update the database schema from version 20-21
// - delete some old/unused configuration settings
// - delete the wt_media_mapping table
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

// Delete old settings
WT_DB::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('MEDIA_EXTERNAL')");

// Delete old table
WT_DB::exec("DROP TABLE IF EXISTS `##media_mapping`");

// Make this table look like all the others
try {
	WT_DB::exec(
		"ALTER TABLE `##media`" .
		" DROP   m_id," .
		" CHANGE m_media   m_id       VARCHAR(20)  COLLATE utf8_unicode_ci NOT NULL," .
		" CHANGE m_file    m_filename VARCHAR(512) COLLATE utf8_unicode_ci DEFAULT NULL," .
		" CHANGE m_gedfile m_file     INTEGER                              NOT NULL," .
		" CHANGE m_gedrec  m_gedcom   MEDIUMTEXT   COLLATE utf8_unicode_ci DEFAULT NULL," .
		" ADD    m_type               VARCHAR(20)  COLLATE utf8_unicode_ci NULL AFTER m_ext,".
		" ADD    PRIMARY KEY     (m_file, m_id)," .
		" ADD            KEY ix2 (m_ext, m_type)," .
		" ADD            KEY ix3 (m_titl)"
	);
} catch (PDOException $ex) {
	// Assume we've already done this
}

// Populate the new column
WT_DB::exec("UPDATE `##media` SET m_type = SUBSTRING_INDEX(SUBSTRING_INDEX(m_gedcom, '\n3 TYPE ', -1), '\n', 1) WHERE m_gedcom like '%\n3 TYPE %'");

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
