<?php
/**
 * Lightbox Module help text.
 *
 * This file is included from the application help_text.php script.
 * It simply needs to set $title and $text for the help topic $help_topic
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
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
case 'mediatab':
	$title=WT_I18N::translate('Media Tab Appearance');
	$text=WT_I18N::translate('This option lets you determine whether the Media tab should be shown on the Individual Information page.<br /><br />When this option is set to <b>Hide</b>, only the <b>Lightbox</b> tab will be shown.<br />');
	break;
case 'lb_ss_speed':
	$title=WT_I18N::translate('Slide Show speed');
	$text=WT_I18N::translate('This option determines the length of time each image should be displayed before the Slide Show displays the next image in the sequence.<br />');
	break;
case 'lb_music_file':
	$title=WT_I18N::translate('Slideshow sound track');
	$text=WT_I18N::translate('This option lets you specify a sound track to be played whenever the slide show is active.  When you leave this field blank, no sound will play during the slide show.<br /><br />This feature only supports files in the mp3 format.<br />');
	break;

case 'lb_transition':
	$title=WT_I18N::translate('Image Transition speed');
	$text=WT_I18N::translate('This option lets you specify the transition speed when the image changes.  This selection is applied during the slideshow.  It is also applied when you move to the next or previous image when the slideshow is not running.<br /><br />The <b>None</b> option eliminates image transitions so that the new image immediately replaces the old without visible adjustment of the new image\'s dimensions.<br />');
	break;
case 'lb_url_dimensions':
	$title=WT_I18N::translate('Lightbox URL Window dimensions');
	$text=WT_I18N::translate('When clicking on a URL image thumbnail, this option lets you specify the Lightbox URL Window dimensions in pixels.<br /><br />This should normally be less than your current browser window dimensions, and certainly less than your screen resolution.<br />');
	break;
}
