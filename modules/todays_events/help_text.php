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
case 'recent_changes':
	$title=i18n::translate('On This Day');
	$text=i18n::translate('The On This Day, in Your History... block shows anniversaries of events for today.  You can configure the amount of detail shown.');
	$text.='<ul><li>';

	// TODO: Other options of this block

	$text.=i18n::translate('Add a scrollbar when block contents grow: ');
	$text.=i18n::translate('If set to "no" the block will expand vertically to display the full list. If set to "yes" the block will be the height set in your theme\'s style sheet, with scroll bars to view long lists.');
	$text.='</li></ul>';
	break;
}
?>