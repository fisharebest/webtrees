<?php
// Header for webtrees administration theme
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

echo
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
	'<html xmlns="http://www.w3.org/1999/xhtml" ', i18n::html_markup(), '>',
	'<head>',
	'<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />',
	'<title>', htmlspecialchars($title), '</title>',
	'<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />',
	'<link rel="stylesheet" href="', WT_THEME_DIR, 'jquery/jquery-ui_theme.css" type="text/css" />',
	'<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />',
	$javascript,
	'</head>',
	'<body id="body">',
// Header
	'<div id="admin_head" class="ui-widget-content">',
	'<div id="title"><a href="administration.php">', i18n::translate('Administration'), '</a></div>',
	'<div id="links">',
	'<a href="index.php">', i18n::translate('My page'), '</a> | ',
	logout_link(),
	'<span>';
	$language_menu=WT_MenuBar::getLanguageMenu();
		if ($language_menu) {
			echo ' | ', $language_menu->getMenuAsList();
		}
	echo '</span>';
	if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
	echo ' | <li><a href="javascript:;" onclick="window.open(\'edit_changes.php\',\'_blank\',\'width=600,height=500,resizable=1,scrollbars=1\'); return false;" style="color:red;">', i18n::translate('Pending changes'), '</a></li>';
	}
	echo '</div>',
	'<div id="info">',
	WT_WEBTREES, ' ', WT_VERSION_TEXT,
	'<br />',
	i18n::translate('Current Server Time:'), ' ', format_timestamp(time()),
	'<br />',
	i18n::translate('Current User Time:'), ' ', format_timestamp(client_time()),
	'</div>',
	'</div>',
// Side menu 
	'<div id="admin_menu" class="ui-widget-content">',
	'<ul>',
	'<li><a ', (WT_SCRIPT_NAME=="administration.php" ? 'class="current" ' : ''), 'href="administration.php">',
	i18n::translate('Administration'),
	'</a></li>';
if (WT_USER_IS_ADMIN) {
	echo
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_site_config.php" ? 'class="current" ' : ''), 'href="admin_site_config.php">',
		i18n::translate('Server configuration'),
		'</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_site_logs.php" ? 'class="current" ' : ''), 'href="admin_site_logs.php">',
		i18n::translate('Logs'),
		'</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_site_readme.php" ? 'class="current" ' : ''), 'href="admin_site_readme.php">', i18n::translate('README documentation'), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_site_info.php" ? 'class="current" ' : ''), 'href="admin_site_info.php?action=phpinfo">', i18n::translate('PHP information'), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_site_manageservers.php" ? 'class="current" ' : ''), 'href="admin_site_ipaddress.php">', i18n::translate('Manage sites'), '</a></li>',
		'</ul></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_trees_manage.php" ? 'class="current" ' : ''), 'href="admin_trees_manage.php">',
		i18n::translate('Family trees'),
		'</a></li>';
} else {
	echo '<li>', i18n::translate('Family trees'), '</li>';
}
echo '<li><ul>';
//-- gedcom list
foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
	if (userGedcomAdmin(WT_USER_ID, $ged_id)) {
		echo
			'<li><a ', (WT_SCRIPT_NAME=="admin_trees_config.php" && WT_GED_ID==$ged_id ? 'class="current" ' : ''), 'href="admin_trees_config.php?ged='.rawurlencode($gedcom).'">',
			htmlspecialchars(get_gedcom_setting($ged_id, 'title')),
			'</a></li>';
	}
}
echo '</ul></li>';
if (WT_USER_IS_ADMIN) {
	echo
		'<li><a ', (WT_SCRIPT_NAME=="admin_users_list.php" ? 'class="current" ' : ''), 'href="useradmin.php">',
		i18n::translate('Users'),
		'</a></li>',
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_users_add.php" ? 'class="current" ' : ''), 'href="admin_users_add.php?action=createform">', i18n::translate('Add user'), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="#" ? 'class="current" ' : ''), 'href="useradmin.php">',
		i18n::translate('Bulk messaging'),
		'</a></li>',
		'</ul></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_media.php" ? 'class="current" ' : ''), 'href="admin_media.php">',
		i18n::translate('Media'),
		'</a></li>',
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_media_upload.php" ? 'class="current" ' : ''), 'href="admin_media_upload.php">',
		i18n::translate('Upload media files'),
		'</a></li>',
		'</ul></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_modules.php" ? 'class="current" ' : ''), 'href="admin_modules.php">',
		i18n::translate('Modules'),
		'</a></li>',
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_module_menus.php" ? 'class="current" ' : ''), 'href="admin_module_menus.php">',
		i18n::translate('Menus'),
		'</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_module_tabs.php" ? 'class="current" ' : ''), 'href="admin_module_tabs.php">',
		i18n::translate('Tabs'),
		'</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_module_blocks.php" ? 'class="current" ' : ''), 'href="admin_module_blocks.php">',
		i18n::translate('Blocks'),
		'</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_module_sidebar.php" ? 'class="current" ' : ''), 'href="admin_module_sidebar.php">',
		i18n::translate('Sidebar'),
		'</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=="admin_module_reports.php" ? 'class="current" ' : ''), 'href="admin_module_reports.php">',
		i18n::translate('Reports'),
		'</a></li>',
		'</ul></li>';
	foreach (WT_Module::getInstalledModules() as $module) {
		if ($module instanceof WT_Module_Config) {
			echo '<li><i><a ', (WT_SCRIPT_NAME=="module.php" && safe_GET('mod')==$module->getName() ? 'class="current" ' : ''), 'href="', $module->getConfigLink(), '">', $module->getTitle(), '</a></i></li>';
		}
	}
}
echo
	'</ul>',
	'</div>',
	// Content -->
	'<div id="admin_content" class="ui-widget-content">';
