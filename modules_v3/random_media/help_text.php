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
// $Id$

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'random_media_ajax_controls':
	$title=WT_I18N::translate('Show slide show controls?');
	$text=WT_I18N::translate('You can use this setting to show or hide the slideshow controls of the Random Media block.<br /><br />These controls allow the user to jump to another random object or to play through randomly selected media like a slideshow. The slideshow changes the contents of the block without preloading information from the server and without reloading the entire page.');
	break;

case 'random_media_filter':
	$title=WT_I18N::translate('Media filter');
	$text=WT_I18N::translate('You can restrict what the Random Media block is permitted to show according to the format and type of media item.  When a given checkbox is checked, the Random Media block is allowed to display media items of that format or type.<br /><br />Format or Type codes that exist in your database but are not in these checkbox lists are assumed to have the corresponding checkbox checked.  For example, if your database contains Media objects of format <b><i>pdf</i></b>, the Random Media block is always permitted to display them.  Similarly, if your database contains Media objects of type <b><i>special</i></b>, the Random Media block is always permitted to display them.');
	break;

case 'random_media_persons_or_all':
	$title=WT_I18N::translate('Show only persons, events, or all?');
	$text=WT_I18N::translate('This option lets you determine the type of media to show.<br /><br />When you select <b>Persons</b>, only media associated with persons will be shown.  Usually, this would be a person\'s photograph.  When you select <b>Events</b>, only media associated with facts or events will be shown.  This might be an image of a certificate.  When you select <b>ALL</b>, this block will show all types of media.');
	break;

case 'random_media_start_slide':
	$title=WT_I18N::translate('Start slide show on page load?');
	$text=WT_I18N::translate('Should the slideshow start automatically when the page is loaded.<br /><br />The slideshow changes the contents of the block without preloading information from the server and without reloading the entire page.');
	break;

}
