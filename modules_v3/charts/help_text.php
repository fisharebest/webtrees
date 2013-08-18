<?php
// Module help text.
//
// This file is included from the application help_text.php script.
// It simply needs to set $title and $text for the help topic $help_topic
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'index_charts':
	$title=WT_I18N::translate('Charts');
	$text=WT_I18N::translate('This block allows a pedigree, descendancy, or hourglass chart to appear on My Page or the Home Page.  Because of space limitations, the charts should be placed only on the left side of the page.<br><br>When this block appears on the Home Page, the root person and the type of chart to be displayed are determined by the administrator.  When this block appears on the user\'s personalized My Page, these options are determined by the user.<br><br>The behavior of these charts is identical to their behavior when they are called up from the menus.  Click on the box of a person to see more details about them.');
	break;
}
