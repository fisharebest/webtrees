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
 * @version $Id: help_text.php 8554 2010-05-31 16:51:39Z greg $
 */

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'recent_changes':
	$title=i18n::translate('Recent changes block');
	$text=i18n::translate('This block shows you the most recent changes to the GEDCOM as recorded by the CHAN GEDCOM tag.');
	$text.='<ul><li>';
	$text.=i18n::translate('Number of days to show: This is the number of days that <b>webtrees</b> should use when searching for events');
	$text.=' (<i>'.i18n::plural('maximum %d day', 'maximum %d days', 30, 30).'</i>).';
	$text.='</li><li>';
	$text.=i18n::translate('Should this block be hidden when it is empty?: Provides the option to hide the block if there are no changes to display.');
	$text.='</li><li>';
	$text.=i18n::translate('Add a scrollbar when block contents grow: If set to "no" the block will expand vertically to display the full list. If set to "yes" the block will be the height set your theme\'s style sheet, with scroll bars to view long lists  ');
	$text.='</li></ul>';
	break;
}
