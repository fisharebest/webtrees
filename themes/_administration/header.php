<?php
// Header for webtrees administration theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

use WT\Auth;

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// This theme uses the jQuery “colorbox” plugin to display images
$this
	->addExternalJavascript(WT_JQUERY_COLORBOX_URL)
	->addExternalJavascript(WT_JQUERY_WHEELZOOM_URL)
	->addInlineJavascript('activate_colorbox();')
	->addInlineJavascript('jQuery.extend(jQuery.colorbox.settings, {width:"75%", height:"75%", transition:"none", slideshowStart:"'. WT_I18N::translate('Play').'", slideshowStop:"'. WT_I18N::translate('Stop').'"})')
	->addInlineJavascript('
		jQuery.extend(jQuery.colorbox.settings, {
			title: function() {
				var img_title = jQuery(this).data("title");
				return img_title;
			}
		});
	');
echo
	'<!DOCTYPE html>',
	'<html ', WT_I18N::html_markup(), '>',
	'<head>',
	'<meta charset="UTF-8">',
	'<meta http-equiv="X-UA-Compatible" content="IE=edge">',
	'<meta name="robots" content="noindex,nofollow">',
	'<title>', htmlspecialchars($title), '</title>',
	'<link rel="icon" href="', WT_CSS_URL, 'favicon.png" type="image/png">',
	'<link rel="stylesheet" href="', WT_THEME_URL, 'jquery-ui-1.10.3/jquery-ui-1.10.3.custom.css" type="text/css">',
	'<link rel="stylesheet" href="', WT_CSS_URL, 'style.css" type="text/css">',
	'<!--[if IE]>',
	'<link type="text/css" rel="stylesheet" href="', WT_CSS_URL, 'msie.css">',
	'<![endif]-->';

echo
	$javascript,
	'</head>',
	'<body id="body">',
// Header
	'<div id="admin_head" class="ui-widget-content">',
	'<i class="icon-webtrees"></i>',
	'<div id="title"><a href="admin.php">', WT_I18N::translate('Administration'), '</a></div>',
	'<div id="links">',
	'<a href="index.php">', WT_I18N::translate('My page'), '</a> | ',
	logout_link(),
	'<span> | </span>',
	'<ul class="langmenu">';
	$language_menu=WT_MenuBar::getLanguageMenu();
		if ($language_menu) {
			echo $language_menu->getMenuAsList();
		}
	echo '</ul>';
	if (WT_USER_CAN_ACCEPT && exists_pending_change()) {
	echo ' | <li><a href="#" onclick="window.open(\'edit_changes.php\',\'_blank\', chan_window_specs); return false;" style="color:red;">', WT_I18N::translate('Pending changes'), '</a></li>';
	}
	echo '</div>',
	'<div id="info">',
	WT_WEBTREES, ' ', WT_VERSION,
	'<br>',
	/* I18N: The local time on the server */
	WT_I18N::translate('Server time'), ' —  ', format_timestamp(WT_SERVER_TIMESTAMP),
	'<br>',
	/* I18N: The local time on the client/browser */
	WT_I18N::translate('Client time'), ' — ', format_timestamp(WT_CLIENT_TIMESTAMP),
	'<br>',
	/* I18N: Timezone - http://en.wikipedia.org/wiki/UTC */
	WT_I18N::translate('UTC'), ' — ', format_timestamp(WT_TIMESTAMP),
	'</div>',
	'</div>',
// Side menu
	'<div id="admin_menu" class="ui-widget-content">',
	'<ul>',
	'<li><a ', (WT_SCRIPT_NAME=='admin.php' ? 'class="current" ' : ''), 'href="admin.php">', WT_I18N::translate('Administration'), '</a></li>';
if (Auth::isAdmin()) {
	echo
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_site_config.php'  ? 'class="current" ' : ''), 'href="admin_site_config.php">',  WT_I18N::translate('Site configuration'    ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_site_logs.php'    ? 'class="current" ' : ''), 'href="admin_site_logs.php">',    WT_I18N::translate('Logs'                  ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_site_readme.php'  ? 'class="current" ' : ''), 'href="admin_site_readme.php">',  WT_I18N::translate('README documentation'  ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_site_info.php'    ? 'class="current" ' : ''), 'href="admin_site_info.php">',    WT_I18N::translate('PHP information'       ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_site_access.php'  ? 'class="current" ' : ''), 'href="admin_site_access.php">',  WT_I18N::translate('Site access rules'     ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_site_clean.php'   ? 'class="current" ' : ''), 'href="admin_site_clean.php">',   WT_I18N::translate('Clean up data folder'), '</a></li>',
		'</ul></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_trees_manage.php' ? 'class="current" ' : ''), 'href="admin_trees_manage.php">', WT_I18N::translate('Family trees'          ), '</a></li>';
} else {
	echo '<li>', WT_I18N::translate('Family trees'), '</li>';
}
echo '<li><ul>';
//-- gedcom list
foreach (WT_Tree::getAll() as $tree) {
	if (Auth::isManager($tree)) {
		// Add a title="" element, since long tree titles are cropped
		echo
			'<li><span><a ', (WT_SCRIPT_NAME=='admin_trees_config.php' && WT_GED_ID==$tree->tree_id ? 'class="current" ' : ''), 'href="admin_trees_config.php?ged='.$tree->tree_name_url.'" title="', WT_Filter::escapeHtml($tree->tree_title), '" dir="auto">', $tree->tree_title_html,
			'</a></span></li>';
	}
}
echo
	'<li><a ', (WT_SCRIPT_NAME=='admin_site_merge.php'   ? 'class="current" ' : ''), 'href="admin_site_merge.php">',   WT_I18N::translate('Merge records'), '</a></li>',
	'<li><a ', (WT_SCRIPT_NAME=='admin_trees_merge.php'   ? 'class="current" ' : ''), 'href="admin_trees_merge.php">', WT_I18N::translate('Merge family trees'), '</a></li>',
	'<li><a ', (WT_SCRIPT_NAME=='admin_site_other.php'   ? 'class="current" ' : ''), 'href="admin_site_other.php">',   WT_I18N::translate('Add unlinked records'), '</a></li>',
	'<li><a ', (WT_SCRIPT_NAME=='admin_trees_places.php' ? 'class="current" ' : ''), 'href="admin_trees_places.php">', WT_I18N::translate('Update place names'), '</a></li>',
	'<li><a ', (WT_SCRIPT_NAME=='admin_trees_check.php'  ? 'class="current" ' : ''), 'href="admin_trees_check.php">',  WT_I18N::translate('Check for errors'), '</a></li>',
	'<li><a ', (WT_SCRIPT_NAME=='admin_site_change.php'  ? 'class="current" ' : ''), 'href="admin_site_change.php">',  WT_I18N::translate('Changes log'),'</a></li>',
	'<li><a href="index_edit.php?gedcom_id=-1" onclick="return modalDialog(\'index_edit.php?gedcom_id=-1'.'\', \'',    WT_I18N::translate('Set the default blocks for new family trees'), '\');">', WT_I18N::translate('Set the default blocks'), '</a></li>',
	'</ul></li>';

if (Auth::isAdmin()) {
	echo
		'<li><a ', (WT_SCRIPT_NAME=='admin_users.php' && WT_Filter::get('action')!="cleanup"&& WT_Filter::get('action')!="createform" ? 'class="current" ' : ''), 'href="admin_users.php">',
		WT_I18N::translate('Users'),
		'</a></li>',
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_users.php' && WT_Filter::get('action')=='createform' ? 'class="current" ' : ''), 'href="admin_users.php?action=createform">', WT_I18N::translate('Add a new user'), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_users_bulk.php' ? 'class="current" ' : ''), 'href="admin_users_bulk.php">', WT_I18N::translate('Send broadcast messages'), '</a>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_users.php' && WT_Filter::get('action')=='cleanup' ? 'class="current" ' : ''), 'href="admin_users.php?action=cleanup">', WT_I18N::translate('Delete inactive users'), '</a></li>',
		'<li><a href="index_edit.php?user_id=-1" onclick="return modalDialog(\'index_edit.php?user_id=-1'.'\', \'', WT_I18N::translate('Set the default blocks for new users'), '\');">', WT_I18N::translate('Set the default blocks'), '</a></li>',
		'</ul></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_media.php' ? 'class="current" ' : ''), 'href="admin_media.php">', WT_I18N::translate('Media'), '</a></li>',
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_media_upload.php' ? 'class="current" ' : ''), 'href="admin_media_upload.php">', WT_I18N::translate('Upload media files'), '</a></li>',
		'</ul></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_modules.php' ? 'class="current" ' : ''), 'href="admin_modules.php">',
		WT_I18N::translate('Modules'),
		'</a></li>',
		'<li><ul>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_module_menus.php'   ? 'class="current" ' : ''), 'href="admin_module_menus.php">',   WT_I18N::translate('Menus'  ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_module_tabs.php'    ? 'class="current" ' : ''), 'href="admin_module_tabs.php">',    WT_I18N::translate('Tabs'   ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_module_blocks.php'  ? 'class="current" ' : ''), 'href="admin_module_blocks.php">',  WT_I18N::translate('Blocks' ), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_module_sidebar.php' ? 'class="current" ' : ''), 'href="admin_module_sidebar.php">', WT_I18N::translate('Sidebar'), '</a></li>',
		'<li><a ', (WT_SCRIPT_NAME=='admin_module_reports.php' ? 'class="current" ' : ''), 'href="admin_module_reports.php">', WT_I18N::translate('Reports'), '</a></li>',
		'</ul></li>';
	foreach (WT_Module::getActiveModules(true) as $module) {
		if ($module instanceof WT_Module_Config) {
			echo '<li><span><a ', (WT_SCRIPT_NAME=='module.php' && WT_Filter::get('mod')==$module->getName() ? 'class="current" ' : ''), 'href="', $module->getConfigLink(), '">', $module->getTitle(), '</a></span></li>';
		}
	}
}
echo
	'</ul>',
	'</div>',
	'<div id="admin_content" class="ui-widget-content">',
	WT_FlashMessages::getHtmlMessages(); // Feedback from asynchronous actions;
