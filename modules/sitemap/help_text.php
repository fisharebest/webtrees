<?php
/**
 * Sitemap Module help text.
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

case 'SITEMAP':
	$title=i18n::translate('Sitemap information');
	$text=i18n::translate('This module generates Sitemap files for use by search engines. Currently, only the Google search engine supports Sitemap files.<br /><br />There is a Sitemap file for each GEDCOM on your site, and when your site has more than one GEDCOM there is also an index Sitemap file that points to each of the GEDCOMs.  The Sitemap files contain links to pages of your site that you want the search engine to index.<br /><br />After this module has generated the Sitemap files, you should inspect them and make whatever changes are necessary.  You should copy these files to the server directory where webtrees is installed, and then log into the <a href=\"https://www.google.com/webmasters/sitemaps/\" target=\"_blank\">Google webmaster tools</a> site to make Google aware of their presence.<br /><br />For more information on Google and Sitemap files, visit the <a href=\"https://www.google.com/webmasters/sitemaps/docs/en/about.html\" target=\"_blank\">Google webmaster tools</a> site.');
	break;

case 'SM_GEDCOM_SELECT':
	$title=i18n::translate('Select GEDCOMs');
	$text=i18n::translate('Select the GEDCOMs for which you want to create a Sitemap file. You must select at least one.<br /><br />When the <b>No links to private information</b> option is selected, only links to data that is publicly available will be included.');
	break;

case 'SM_ITEM_SELECT':
	$title=i18n::translate('Select items');
	$text=i18n::translate('Select the elements to be included in the Sitemap file.<br /><br />A priority can be specified for all selected elements. This priority is relative to the other priorities in the file.  The update frequency can also be specified. This is an indication of how frequently the data in these items might change. This can influence the time between visits by the search engine, and thus will influence the amount of traffic the site generates.');
	break;
}
