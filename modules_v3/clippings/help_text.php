<?php
// Module help text.
//
// This file is included from the application help_text.php script.
// It simply needs to set $title and $text for the help topic $help_topic
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

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'add_by_id':
	$title=WT_I18N::translate('Add by ID');
	$text=WT_I18N::translate('This input box lets you enter an individual’s ID number so he can be added to the clippings cart.  Once added you’ll be offered options to link that individual’s relations to your clippings cart.<br><br>If you do not know an individual’s ID number, you can perform a search by name by pressing the individual icon next to the “Add” button.');
	break;

case 'empty_cart':
	$title=WT_I18N::translate('Empty the clippings cart');
	$text=WT_I18N::translate('When you click this link your clippings cart will be totally emptied.<br><br>If you don’t want to remove all individuals, families, etc. from the clippings cart, you can remove items individually by clicking the <b>Remove</b> link in the name boxes.  There is <u>no</u> confirmation dialog when you click either of these links;  the requested deletion takes place immediately.');
	break;
}
