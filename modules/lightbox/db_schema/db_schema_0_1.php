<?php
/**
 * Update the LB module database schema from version 0 to version 1
 *
 * Version 0: empty database
 * Version 1: move the configuration from lb_config.php (PGV 4.2.3 and earlier) to the pgv_site_setting table
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

define('WT_LB_DB_SCHEMA_0_1', '');

if (file_exists(WT_ROOT.'modules/lightbox/lb_config.php')) {
	// Use @, in case the lb_config.php file is incomplete/corrupt
	@require_once WT_ROOT.'modules/lightbox/lb_config.php';
  @set_module_setting('lightbox', 'LB_ENABLED',        $mediatab);
  @set_module_setting('lightbox', 'LB_AL_HEAD_LINKS',  $LB_AL_HEAD_LINKS);
  @set_module_setting('lightbox', 'LB_AL_THUMB_LINKS', $LB_AL_THUMB_LINKS);
  @set_module_setting('lightbox', 'LB_TT_BALLOON',     $LB_TT_BALLOON);
  @set_module_setting('lightbox', 'LB_ML_THUMB_LINKS', $LB_ML_THUMB_LINKS);
  @set_module_setting('lightbox', 'LB_MUSIC_FILE',     $LB_MUSIC_FILE);
  @set_module_setting('lightbox', 'LB_SS_SPEED',       $LB_SS_SPEED);
  @set_module_setting('lightbox', 'LB_TRANSITION',     $LB_TRANSITION);
	@set_module_setting('lightbox', 'LB_URL_WIDTH',      $LB_URL_WIDTH);
	@set_module_setting('lightbox', 'LB_URL_HEIGHT',     $LB_URL_HEIGHT);
	@unlink(WT_ROOT.'modules/lightbox/lb_config.php');
}

// Update the version to indicate sucess
set_site_setting($schema_name, $next_version);
