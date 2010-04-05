<?php
/**
* A landing spot for pages that are restricted from search engines.
* WARNING: The functions print_header() and print_simple_header()
* cannot be called from here because they would cause an infinite
* back to here.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
* Author: Mike Elliott (coloredpixels)
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
* This Page Is Valid XHTML 1.0 Transitional! > 21 August 2005
*
* @package webtrees
* @version $Id$
*/

define('WT_SCRIPT_NAME', 'search_engine.php');
require './includes/session.php';

header('Content-Type: text/html; charset=UTF-8');

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml" ', i18n::html_markup, '><head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo '<link rel="stylesheet" href="', $stylesheet, '" type="text/css" media="all" />';
if ($rtl_stylesheet && $TEXT_DIRECTION=='rtl') {
	echo '<link rel="stylesheet" href="', $rtl_stylesheet, '" type="text/css" media="all" />';
}
echo '<meta name="robots" content="noindex,follow" />';
echo '<meta name="generator" content="', WT_WEBTREES, ' - ', WT_WEBTREES_URL, '" />';
echo '<title>', i18n::translate('Search Engine Spider Detected'), '</title>';
echo '</head><body>';
echo '<div class="helptext">', i18n::translate('webtrees automatically provides search engines with smaller data files with fewer links.  The data is limited to the individual and immediate family, without adding information about grand parents or grand children.  Many reports and server-intensive pages like the calendar are off limits to the spiders.<br /><br />Attempts by the spiders to go to those pages result in showing this page.  If you are seeing this text, the software believes you are a search engine spider.  Below is the list of pages that are allowed to be spidered and will provide the abbreviated data.<br /><br />Real users who follow search engine links into this site will see the full pages and data, and not this page.');
if ($SEARCH_SPIDER) {
	echo '<br /><br />', i18n::translate('Search Engine Spider Detected'), ': ', $SEARCH_SPIDER, '<br />';
}
echo '</div><br />';

// List of indis from each gedcom
$all_gedcoms=get_all_gedcoms();
if ($ALLOW_CHANGE_GEDCOM && count($all_gedcoms)>1) {
	foreach ($all_gedcoms as $ged_id=>$gedcom) {
		$title=i18n::translate('Home Page').' - '.PrintReady(get_gedcom_setting($ged_id, 'title'));
		echo '<a href="', encode_url("index.php?ged={$gedcom}"), '"><b>', $title, '</b></a><br />';
	}
	echo '<br />';
	foreach ($all_gedcoms as $ged_id=>$gedcom) {
		$title=i18n::translate('Individuals').' - '.PrintReady(get_gedcom_setting($ged_id, 'title'));
		echo '<a href="', encode_url("indilist.php?ged={$gedcom}"), '"><b>', $title, '</b></a><br />';
	}
} else {
	$title=i18n::translate('Home Page');
	echo '<a href="', encode_url("index.php?ged={$GEDCOM}"), '"><b>', $title, '</b></a><br />';
	$title=i18n::translate('Individuals');
	echo '<a href="', encode_url("indilist.php?ged={$GEDCOM}"), '"><b>', $title, '</b></a><br />';
}

echo '</body></html>';
?>
