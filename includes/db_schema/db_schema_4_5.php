<?php
/**
 * Update the database schema from version 4 to version 5
 * - add support for sorting gedcoms non-alphabetically
 *
 * Also clean out some old/unused values and files.
 * 
 * The script should assume that it can be interrupted at
 * any point, and be able to continue by re-running the script.
 * Fatal errors, however, should be allowed to throw exceptions,
 * which will be caught by the framework.
 * It shouldn't do anything that might take more than a few
 * seconds, for systems with low timeout values.
 *
 * phpGedView: Genealogy Viewer
 * Copyright (C) 2010 Greg Roach
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_DB_SCHEMA_4_5', '');

try {
	self::exec("ALTER TABLE `##gedcom` ADD COLUMN sort_order INTEGER NOT NULL DEFAULT 0");
} catch (PDOException $ex) {
	// If this fails, it has probably already been done.
}

try {
	self::exec("ALTER TABLE `##gedcom` ADD INDEX ix1 (sort_order)");
} catch (PDOException $ex) {
	// If this fails, it has probably already been done.
}

// No longer used
self::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('PAGE_AFTER_LOGIN')");

// Change of defaults - do not add ASSO, etc. to NOTE objects
self::exec("UPDATE `##gedcom_setting` SET setting_value='SOUR' WHERE setting_value='ASSO,SOUR,NOTE,REPO' AND setting_name='NOTE_FACTS_ADD'");

// Update the version to indicate success
set_site_setting($schema_name, $next_version);

// We may not have permission to do this.  But we can try....
// Removed in 1.0.2
@unlink(WT_ROOT.'includes/classes/class_geclippings.php');
@unlink(WT_ROOT.'includes/classes/class_gedownloadgedcom.php');
@unlink(WT_ROOT.'includes/classes/class_gewebservice.php');
@unlink(WT_ROOT.'includes/classes/class_grampsexport.php');
@unlink(WT_ROOT.'language/en.mo');
// Removed in 1.0.3
@unlink(WT_ROOT.'themechange.php');
// Removed in 1.0.4
@unlink(WT_ROOT.'themes/fab/images/notes.gif');
// Removed in 1.0.5
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_doors_0.php');
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_doors_1.php');
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_tabs_0.php');
@unlink(WT_ROOT.'modules/lightbox/functions/lb_indi_tabs_1.php');
// Removed in 1.0.6
@unlink(WT_ROOT.'includes/set_gedcom_defaults.php');
@unlink(WT_ROOT.'includes/functions/functions.ar.php');
@unlink(WT_ROOT.'includes/functions/functions.en.php');
@unlink(WT_ROOT.'includes/functions/functions.fr.php');
@unlink(WT_ROOT.'includes/functions/functions.pl.php');
@unlink(WT_ROOT.'includes/functions/functions.tr.php');
@rmdir (WT_ROOT.'includes/extras');
