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
case 'archive':
	$title=WT_I18N::translate('View archive');
	$text=WT_I18N::translate('To reduce the height of the News block, the administrator has hidden some articles. You can reveal these hidden articles by clicking the <strong>View archive</strong> link.');
	break;
case 'flag':
	$title=WT_I18N::translate('Limit') . ':';
	$text=WT_I18N::translate('Enter the limiting value here. If you have opted to limit the News article display according to age, any article older than the number of days entered here will be hidden from view. If you have opted to limit the News article display by number, only the specified number of recent articles, ordered by age, will be shown. The remaining articles will be hidden from view. Zeros entered here will disable the limit, causing all News articles to be shown.');
	break;
case 'limit':
	$title=WT_I18N::translate('Limit display by') . ':';
	$text=WT_I18N::translate('You can limit the number of News articles displayed, thereby reducing the height of the GEDCOM News block.<br><br>This option determines whether any limits should be applied or whether the limit should be according to the age of the article or according to the number of articles.');
	break;
}
