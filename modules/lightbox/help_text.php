<?php
/**
 * Lightbox Module help text.
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

// Added in VERSION 4.1.6

case 'LIGHTBOX_CONFIG':
	$title=i18n::translate('Configure Lightbox');
	$text=i18n::translate('Configure all aspects of the Lightbox module here.');
	break;

case 'lb_tt_balloon':
	$title=i18n::translate('Album Tab Thumbnail - Notes Link Tooltip');
	$text=i18n::translate('This option lets you determine whether the \'View Notes\' link should show a \'Balloon\' Tooltip or \'Normal\' Tooltip when clicked. <br /><br />The link shown here show you the Notes associated with a Media item (if available).<br />');
	break;
	
// VERSION 4.1.3 
	
case 'mediatab':
	$title=i18n::translate('Media Tab Appearance');
	$text=i18n::translate('This option lets you determine whether the Media tab should be shown on the Individual Information page.<br /><br />When this option is set to <b>Hide</b>, only the <b>Lightbox</b> tab will be shown.<br />');
	break;

case 'lb_al_head_links':
	$title=i18n::translate('Album Tab Header Link appearance');
	$text=i18n::translate('This option lets you determine whether the header area of the Lightbox tab, which contains links to control various aspects of the Lightbox module, should contain only icons, only text, or both.<br /><br />The <b>Icon</b> option is probably not very useful, since you won\'t see any indication of each icon\'s function until your mouse hovers over the icon.<br />');
	break;

case 'lb_al_thumb_links':
	$title=i18n::translate('Album Tab Thumbnails Link appearance');
	$text=i18n::translate('This option lets you determine whether the links area below each thumbnail should show an icon or text.  The links shown here let you edit the Media object\'s details or delete it.<br />');
	break;

case 'lb_ml_thumb_links':
	$title=i18n::translate('Thumbnails Link appearance');
	$text=i18n::translate('This option lets you determine whether the Links area above the Media object\'s details in the MultiMedia list should contain only icons, only text, or both.  The links shown here let you perform various editing actions on the Media object in question.<br /><br />The <b>None</b> option completely hides these links, and thus acts as if the user did not have any editing rights.<br />');
	break;

case 'lb_ss_speed':
	$title=i18n::translate('Slide Show speed');
	$text=i18n::translate('This option determines the length of time each image should be displayed before the Slide Show displays the next image in the sequence.<br />');
	break;

case 'lb_music_file':
	$title=i18n::translate('Slideshow Sound Track');
	$text=i18n::translate('This option lets you specify a sound track to be played whenever the slide show is active.  When you leave this field blank, no sound will play during the slide show.<br /><br />This feature only supports files in the mp3 format.<br />');
	break;

case 'lb_transition':
	$title=i18n::translate('Image Transition speed');
	$text=i18n::translate('This option lets you specify the transition speed when the image changes.  This selection is applied during the slideshow.  It is also applied when you move to the next or previous image when the slideshow is not running.<br /><br />The <b>None</b> option eliminates image transitions so that the new image immediately replaces the old without visible adjustment of the new image\'s dimensions.<br />');
	break;

case 'lb_url_dimensions':
	$title=i18n::translate('Lightbox URL Window dimensions');
	$text=i18n::translate('When clicking on a URL image thumbnail, this option lets you specify the Lightbox URL Window dimensions in pixels.<br /><br />This should normally be less than your current browser window dimensions, and certainly less than your screen resolution.<br />');
	break;

}
