<?php
// Update the database schema from version 15 to 16
// - delete old config settings
// - add extra columns to wt_default_resn
// - increase size of session_id column, to account for new session hash algorithms
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 Greg Roach
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

// Remove old settings
self::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN('GEDCOM_DEFAULT_TAB', 'LINK_ICONS', 'ZOOM_BOXES', 'SHOW_LIST_PLACES', 'SHOW_CONTEXT_HELP')");
self::exec("DELETE FROM `##user_setting` WHERE setting_name='defaulttab'");

// There is no way to add a RESN tag to NOTE objects
self::exec("UPDATE `##gedcom_setting` SET setting_value='SOUR,RESN' WHERE setting_name='NOTE_FACTS_ADD' AND setting_value='SOUR'");

// This needs to be an absolute URL.  If not set, it defaults to the full path to login.php
self::exec("DELETE FROM `##site_setting` WHERE setting_name='LOGIN_URL' AND setting_value='login.php'");
// No need for an empty value
self::exec("DELETE FROM `##site_setting` WHERE setting_name='SERVER_URL' AND setting_value=''");

// Later PHP versions use session IDs longer than 32 chars.
self::exec("ALTER TABLE `##session` CHANGE session_id session_id CHAR(128) COLLATE utf8_unicode_ci NOT NULL");

// Update the version to indicate success
WT_Site::preference($schema_name, $next_version);

