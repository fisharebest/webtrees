<?php
// Update the database schema from version 16 to 17
// - add dummy users/trees to store default settings
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

// Add a "default" user, to store default settings
WT_DB::exec("INSERT IGNORE INTO `##user` (user_id, user_name, real_name, email, password) VALUES (-1, 'DEFAULT_USER', 'DEFAULT_USER', 'DEFAULT_USER', 'DEFAULT_USER')");

// Add the initial default block settings

WT_DB::exec("INSERT IGNORE INTO `##block` (user_id, location, block_order, module_name) VALUES (-1, 'main', 1, 'todays_events'), (-1, 'main', 2, 'user_messages'), (-1, 'main', 3, 'user_favorites'), (-1, 'side', 1, 'user_welcome'), (-1, 'side', 2, 'random_media'), (-1, 'side', 3, 'upcoming_events'), (-1, 'side', 4, 'logged_in')");

// Add a "default" tree, to store default settings
WT_DB::exec("INSERT IGNORE INTO `##gedcom` (gedcom_id, gedcom_name) VALUES (-1, 'DEFAULT_TREE')");

// Add the initial default block settings
WT_DB::exec("INSERT IGNORE INTO `##block` (gedcom_id, location, block_order, module_name) VALUES (-1, 'main', 1, 'gedcom_stats'), (-1, 'main', 2, 'gedcom_news'), (-1, 'main', 3, 'gedcom_favorites'), (-1, 'main', 4, 'review_changes'), (-1, 'side', 1, 'gedcom_block'), (-1, 'side', 2, 'random_media'), (-1, 'side', 3, 'todays_events'), (-1, 'side', 4, 'logged_in')");

// Some modules (e.g. sitemap) require larger settings
WT_DB::exec("ALTER TABLE `##module_setting` CHANGE setting_value setting_value MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL");

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
