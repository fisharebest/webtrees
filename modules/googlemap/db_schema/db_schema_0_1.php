<?php
/**
 * Update the GM module database schema from version 0 to version 1
 *
 * Version 0: empty database
 * Version 1: create the tables, as per PGV 4.2.1
 *
 * The script should assume that it can be interrupted at
 * any point, and be able to continue by re-running the script.
 * Fatal errors, however, should be allowed to throw exceptions,
 * which will be caught by the framework.
 * It shouldn't do anything that might take more than a few
 * seconds, for systems with low timeout values.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009 Greg Roach
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

define('WT_GM_DB_SCHEMA_0_1', '');

// Create all of the tables needed for this module
if (!WT_DB::table_exists("{$TBLPREFIX}placelocation")) {
	WT_DB::exec(
		"CREATE TABLE {$TBLPREFIX}placelocation (".
		" pl_id        INTEGER         NOT NULL,".
		" pl_parent_id INTEGER             NULL,".
		" pl_level     INTEGER             NULL,".
		" pl_place     VARCHAR(255)     NULL,".
		" pl_long      VARCHAR(30)      NULL,".
		" pl_lati      VARCHAR(30)      NULL,".
		" pl_zoom      INTEGER             NULL,".
		" pl_icon      VARCHAR(255)     NULL,".
		" PRIMARY KEY (pl_id)".
		") ".self::$UTF8_TABLE
	);
	self::exec("CREATE INDEX {$TBLPREFIX}pl_level     ON {$TBLPREFIX}placelocation (pl_level    )");
	self::exec("CREATE INDEX {$TBLPREFIX}pl_long      ON {$TBLPREFIX}placelocation (pl_long     )");
	self::exec("CREATE INDEX {$TBLPREFIX}pl_lati      ON {$TBLPREFIX}placelocation (pl_lati     )");
	self::exec("CREATE INDEX {$TBLPREFIX}pl_name      ON {$TBLPREFIX}placelocation (pl_place    )");
	self::exec("CREATE INDEX {$TBLPREFIX}pl_parent_id ON {$TBLPREFIX}placelocation (pl_parent_id)");

}

// Update the version to indicate sucess
set_site_setting($schema_name, $next_version);
