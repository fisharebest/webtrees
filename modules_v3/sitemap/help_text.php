<?php
// Module help text.
//
// This file is included from the application help_text.php script.
// It simply needs to set $title and $text for the help topic $help_topic
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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

case 'SITEMAP':
	$title=
		/* I18N: Sitemaps are defined at www.sitemaps.org */
		WT_I18N::translate('Sitemaps');
	$text=
		'<p>'.
		/* I18N: The www.sitemaps.org site is translated into many languages (e.g. http://www.sitemaps.org/fr/) - choose an appropriate URL. */
		WT_I18N::translate('Sitemaps are a way for webmasters to tell search engines about the pages on a website that are available for crawling.  All major search engines support sitemaps.  For more information, see <a href="http://www.sitemaps.org/">www.sitemaps.org</a>.').
		'</p><p>'.
		WT_I18N::translate('After generating the sitemap files, you should upload them to the webtrees installation directory on the web-server.').
		'</p>';
	// If we are installed in the domain root, then robots.txt is the best approach.
	// Otherwise, we must submit to each search engine individually.
	if (WT_SCRIPT_PATH=='/') {
		$text.=
			'<p>'.
			/* I18N: Do not translate the text "robots.txt" - the file must have exactly this name. */
			WT_I18N::translate('To tell search engines that sitemaps are available, you should add the following line to your robots.txt file.').
			'</p><pre>Sitemap:'.WT_SERVER_NAME.WT_SCRIPT_PATH.'SitemapIndex.xml</pre>';
	} else {
		$text.=
			'<p>'.
			WT_I18N::translate('To tell search engines that sitemaps are available, you can use the following links.').
			'</p><ul>'.
			// This list comes from http://en.wikipedia.org/wiki/Sitemaps
			'<li><a href="http://www.google.com/webmasters/tools/ping?sitemap='.WT_SERVER_NAME.WT_SCRIPT_PATH.'SitemapIndex.xml">Google</a></li>'.
			'<li><a href="http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=SitemapWriter&amp;url='.WT_SERVER_NAME.WT_SCRIPT_PATH.'SitemapIndex.xml">Yahoo</a></li>'.
			'<li><a href="http://submissions.ask.com/ping?sitemap='.WT_SERVER_NAME.WT_SCRIPT_PATH.'SitemapIndex.xml">Ask</a></li>'.
			'<li><a href="http://www.bing.com/webmaster/ping.aspx?siteMap='.WT_SERVER_NAME.WT_SCRIPT_PATH.'SitemapIndex.xml">Bing</a></li>'.
			'</ul>';
	}
	break;

case 'SM_GEDCOM_SELECT':
	$title=WT_I18N::translate('Select GEDCOMs');
	$text=WT_I18N::translate('Select the GEDCOMs for which you want to create a Sitemap file. You must select at least one.<br /><br />When the <b>No links to private information</b> option is selected, only links to data that is publicly available will be included.');
	break;

case 'SM_ITEM_SELECT':
	$title=WT_I18N::translate('Select items');
	$text=WT_I18N::translate('Select the elements to be included in the Sitemap file.<br /><br />A priority can be specified for all selected elements. This priority is relative to the other priorities in the file.  The update frequency can also be specified. This is an indication of how frequently the data in these items might change. This can influence the time between visits by the search engine, and thus will influence the amount of traffic the site generates.');
	break;
}
