<?php
// Module help text.
//
// This file is included from the application help_text.php script.
// It simply needs to set $title and $text for the help topic $help_topic
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
//
// $Id: help_text.php 13034 2012-06-30 04:03:13Z JustCarmen $

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'choose_images':
	$title=WT_I18N::translate('Showing images in the Fancy Image Bar');
	$text=WT_I18N::translate('Here you can choose which images should be shown in the Fancy Image Bar. Uncheck the images you do not want to show in the Fancy Image Bar.<br/>The amount of images needed depends on the width of the users screen. Normally about 20 images should be enough.<br/>If there are less images choosen, images will be repeated to fill up the entire Fancy Image Bar.<br/><br>The Fancy Image Bar module respects privacy settings!');
	break;
}
