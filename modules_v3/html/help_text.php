<?php
// Help text for the HTML block.
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
case 'block_html_content':
	$title=WT_I18N::translate('Content');
	$text=WT_I18N::translate('As well as using the toolbar to apply HTML formatting, you can insert database fields which are updated automatically.  These special fields are marked with <b>#</b> characters.  For example <b>#totalFamilies#</b> will be replaced with the actual number of families in the database.  Advanced users may wish to apply CSS classes to their text, so that the formatting matches the currently selected theme.');
	break;

case 'block_html_template':
	$title=WT_I18N::translate('Templates');
	$text=WT_I18N::translate('To assist you in getting started with this block, we have created several standard templates.  When you select one of these templates, the text area will contain a copy that you can then alter to suit your site’s requirements.');
	break;
}
