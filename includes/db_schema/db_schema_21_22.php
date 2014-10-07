<?php
// Update the database schema from version 21-22
// - delete some old/unused configuration settings
// - data update for 1.4.0 media changes
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

// Data fix for bug #1072477
WT_DB::exec("UPDATE `##default_resn` SET xref    =NULL WHERE xref    =''");
WT_DB::exec("UPDATE `##default_resn` SET tag_type=NULL WHERE tag_type=''");

// Delete old settings
WT_DB::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('AUTO_GENERATE_THUMBS', 'POSTAL_CODE', 'MEDIA_DIRECTORY_LEVELS', 'USE_MEDIA_VIEWER')");

// Delete old settings
WT_DB::exec("DELETE FROM `##module_setting` WHERE module_name='lightbox'");

// Very old versions of phpGedView allowed media paths beginning “./”
// Remove these
WT_DB::exec(
	"UPDATE `##media` m".
	" SET".
	"  m_filename = TRIM(LEADING './' FROM m_filename),".
	"  m_gedcom   = REPLACE(m_gedcom, '\n1 FILE ./', '\n1 FILE ')"
);
WT_DB::exec(
	"UPDATE `##change` c".
	" SET new_gedcom = REPLACE(new_gedcom, '\n1 FILE ./', '\n1 FILE ')".
	" WHERE status = 'pending'"
);

// Previous versions of webtrees included the MEDIA_DIRECTORY setting in the
// FILE tag of the OBJE records.  Remove it…
WT_DB::exec(
	"UPDATE `##media` m".
	" JOIN `##gedcom_setting` gs ON (m.m_file = gs.gedcom_id AND gs.setting_name = 'MEDIA_DIRECTORY')".
	" SET".
	"  m_filename = TRIM(LEADING gs.setting_value FROM m_filename),".
	"  m_gedcom   = REPLACE(m_gedcom, CONCAT('\n1 FILE ', gs.setting_value), '\n1 FILE ')"
);
// …don’t forget pending changes
WT_DB::exec(
	"UPDATE `##change` c".
	" JOIN `##gedcom_setting` gs ON (c.gedcom_id = gs.gedcom_id AND gs.setting_name = 'MEDIA_DIRECTORY')".
	" SET new_gedcom = REPLACE(new_gedcom, CONCAT('\n1 FILE ', gs.setting_value), '\n1 FILE ')".
	" WHERE status = 'pending'"
);

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
