<?php
// Update the database schema from version 21-22
// - delete some old/unused configuration settings
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 Greg Roach
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
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Delete old settings
self::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('POSTAL_CODE', 'MEDIA_DIRECTORY_LEVELS')");

// Previous versions of webtrees included the MEDIA_DIRECTORY setting in the
// FILE tag of the OBJE records.  Remove it.
self::exec(
	"UPDATE `##media` m".
	" JOIN `##gedcom_setting` gs ON (m.m_file = gs.gedcom_id AND gs.setting_name = 'MEDIA_DIRECTORY')".
	" SET".
	"  m_filename = TRIM(LEADING gs.setting_value FROM m_filename),".
	"  m_gedcom   = REPLACE(m_gedcom, CONCAT('\n1 FILE ', gs.setting_value), '\n1 FILE ')"
);

self::exec(
	"UPDATE `##change` c".
	" JOIN `##gedcom_setting` gs ON (c.gedcom_id = gs.gedcom_id AND gs.setting_name = 'MEDIA_DIRECTORY')".
	" SET new_gedcom = REPLACE(new_gedcom, CONCAT('\n1 FILE ', gs.setting_value), '\n1 FILE ')".
	" WHERE status = 'pending'"
);

// Update the version to indicate success
WT_Site::preference($schema_name, $next_version);
