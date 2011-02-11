<?php
/**
 * Googlemap configuration User Interface.
 *
 * Provides links for administrators to get to other administrative areas of the site
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 01 September 2005
 *
 * @package webtrees
 * @subpackage Admin
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require 'modules/googlemap/defaultconfig.php';

if (file_exists('modules/googlemap/config.php')) {
	require 'modules/googlemap/config.php';
}

print_header(WT_I18N::translate('Google Maps'));

if (WT_USER_IS_ADMIN) { 
	echo '<table id="gm_config"><tr>',
		'<th><a ', (safe_GET('mod_action')=="admin_editconfig" ? 'class="current" ' : ''), 'href="module.php?mod=googlemap&mod_action=admin_editconfig">', WT_I18N::translate('Google Maps configuration'), '</a>', help_link('GOOGLEMAP_CONFIG','googlemap'), '</th>',
		'<th><a ', (safe_GET('mod_action')=="admin_places" ? 'class="current" ' : ''), 'href="module.php?mod=googlemap&mod_action=admin_places">', WT_I18N::translate('Edit geographic place locations'), '</a>', help_link('PLE_EDIT','googlemap'), '</th>',
		'<th><a ', (safe_GET('mod_action')=="admin_placecheck" ? 'class="current" ' : ''), 'href="module.php?mod=googlemap&mod_action=admin_placecheck">', WT_I18N::translate('Place Check'), '</a>', help_link('GOOGLEMAP_PLACECHECK','googlemap'), '</th>',
		'</tr></table>';
	print_footer();
} else {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url=module.php?mod=googlemap&mod_action=admin_config');
	exit;
}
	

