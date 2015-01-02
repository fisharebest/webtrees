<?php
/*
 * JustBlack Theme Options Module - Help text
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2014 webtrees development team.
 * Copyright (C) 2014 JustCarmen.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'media_menu':
	$title=WT_I18N::translate('Media menu in topmenu');
	$text=WT_I18N::translate('<p>If this option is set the Media Menu will be moved to the topmenu. The names of first level media folders will appear as submenu of the new Media Menu.</p>'.
                            '<p>Warning: these menu links are not translated automatically. If you want your menus to be translated you will have to do it manually with the instructions in the webrees WIKI.</p>');
	break;
case 'media_folder':
	$title=WT_I18N::translate('Choose a default media folder');
	$text=WT_I18N::translate('<p>The default media folder will be used as link on the main media menu icon. If you click on this icon the medialist page for this folder appears.</p>');
	break;
case 'subfolders':
	$title=WT_I18N::translate('Subfolders');
	$text=WT_I18N::translate('<p>If you set this option the results on the media list page will include subfolders.</p>');
	break;
case 'sort_topmenu':
	$title=WT_I18N::translate('Sorting the topmenu');
	$text=WT_I18N::translate('<p>Click a row, then drag-and-drop to re-order the topmenu items. Then click the \'save\' button.</p>');
	break;
}