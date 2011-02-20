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
	$title=WT_I18N::translate('Recent changes block');
	$text=WT_I18N::translate('This block shows you the most recent changes to the GEDCOM as recorded by the CHAN GEDCOM tag.');
	$text.='<ul><li>';
	$text.=WT_I18N::translate('Number of days to show: This is the number of days that <b>webtrees</b> should use when searching for events');
	$text.=' (<i>'.WT_I18N::plural('maximum %d day', 'maximum %d days', 30, 30).'</i>).';
	$text.='</li><li>';
	$text.=WT_I18N::translate('Presentation Style: Display as a list or in a table.');
	$text.='</li><li>';
	$text.=WT_I18N::translate('Display Parents: Select whether to display the person\'s parents.');
	$text.='</li><li>';
	$text.=WT_I18N::translate('Sort Style: Only used when viewing a list; for table view, click on the table headings to sort.');
        $text.='</li><li>';
	$text.=WT_I18N::translate('Should this block be hidden when it is empty?: Provides the option to hide the block if there are no changes to display.');
	$text.='</li></ul>';
	break;
}
?>