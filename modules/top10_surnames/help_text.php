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
case 'top10_surnames':
	$title=i18n::translate('Top surnames block');
	$text =i18n::translate('This block displays the most frequently occurring surnames in the database. The actual number of surnames shown in this block is configurable. Using the GEDCOM administration function, you can also configure names to remove from this list.');
	$text.='<p>';
	$text.=i18n::translate('The configuration settings for this block allow changes to the number of names displayed, the presentaion style, and an option to use scroll bars with long lists.');
	$text.='</p>';
	break;

case 'style':
	$title=i18n::translate('Presentation style');
	$text =i18n::translate('Choose from one of these styles: ');
	$text.='<br /><br /><dl><dt>';
	$text.='List: ';
	$text.='</dt><dd>';
	$text.=i18n::translate('A vertical, bulleted list of names');
	$text.='</dd><br /><dt>';
	$text.='Array: ';
	$text.='</dt><dd>';
	$text.=i18n::translate('A simple list of names separated by semi-colons. Useful where vertical space is limited.');
	$text.='</dd><br /><dt>';
	$text.='Table: ';
	$text.='</dt><dd>';
	$text.=i18n::translate('A tabular structure, including sequence numbers, and separate indication of similar names like "van" and "Van".');
	$text.='</dd><br /><dt>';
	$text.='Tag cloud: ';
	$text.='</dt><dd>';
	$text.=i18n::translate('In this style, the surnames are shown in a list, and the font size used for each name depends on the number of occurrences of that name in the database.');
	$text.='</dd></dl>';
	break;

case 'scrollbars':
	$title=i18n::translate('Scrollbars');
	$text ='<dl><dt>';
	$text.=i18n::translate('Add a scrollbar when block contents grow: ');
	$text.='</dt><dd>';
	$text.=i18n::translate('If set to "no" the block will expand vertically to display the full list. If set to "yes" the block will be the height set in your theme\'s style sheet, with scroll bars to view long lists.');
	$text.='</dd></dl>';
	break;
}
?>