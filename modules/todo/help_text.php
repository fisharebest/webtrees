<?php
/**
 * Module help text.
 *
 * This file is included from the application help_text.php script.
 * It simply needs to set $title and $text for the help topic $help_topic
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'todo':
	$title=WT_I18N::translate('"To Do" block');
	$text=WT_I18N::translate('This block helps you keep track of <b>_TODO</b> tasks in the database.<br /><br />To add &quot;To Do&quot; tasks to your records, you may first need amend the GEDCOM configuration so that the <b>_TODO</b> fact is in the list of facts that can be added to the records of individuals, families, sources, and repositories.  Each of these lists, which you will find in the Edit Options section of the GEDCOM configuration, is independent.  The order of the list entries is not important; you can add the new entries at the beginning of each list.');
	break;

case 'todo_show_future':
	$title=WT_I18N::translate('Show future tasks');
	$text=WT_I18N::translate('Show &quot;To Do&quot; tasks that have a date in the future.  Otherwise only items with a date in the past are shown.');
	break;

case 'todo_show_other':
	$title=WT_I18N::translate('Show other users\' tasks');
	$text=WT_I18N::translate('Show &quot;To Do&quot; tasks assigned to other users');
	break;

case 'todo_show_unassigned':
	$title=WT_I18N::translate('Show unassigned tasks');
	$text=WT_I18N::translate('Show &quot;To Do&quot; tasks that are not assigned to any user');
	break;

}
?>