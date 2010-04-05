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
case 'add_by_id':
	$title=i18n::translate('Add by ID');
	$text=i18n::translate('This input box lets you enter an individual\'s ID number so he can be added to the Clippings Cart.  Once added you\'ll be offered options to link that individual\'s relations to your Clippings Cart.<br /><br />If you do not know an individual\'s ID number, you can perform a search by name by pressing the Person icon next to the Add button.');
	break;

case 'clip_cart':
	$title=i18n::translate('Clippings Cart');
	$text=i18n::translate('This box shows the contents of your Clippings Cart.  The <i>Types</i> column indicates the type of each entry, which can be Individual (INDI), Family (FAM), Source (SOUR), Repository (REPO), Note (NOTE), and Media (OBJE);  each is represented by its own icon.  The <i>ID</i> column shows the ID number for each item of that particular type.  The <i>Name / Description</i> column gives either the name of the family or individual, or a description of the item.  The Remove button will remove that record from the Clippings Cart.  <b>Confirmation to remove is NOT asked for.</b>');
	break;

case 'empty_cart':
	$title=i18n::translate('Empty Cart');
	$text=i18n::translate('When you click this link your Clippings Cart will be totally emptied.<br /><br />If you don\'t want to remove all persons, families, etc. from the Clippings Cart, you can remove items individually by clicking the <b>Remove</b> link in the Name boxes.  There is <u>no</u> confirmation dialog when you click either of these links;  the requested deletion takes place immediately.');
	break;
}
