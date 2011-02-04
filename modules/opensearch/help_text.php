<?php
/**
 * OpenSearch Module help text.
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

case 'OPENSEARCH':
	$title=WT_I18N::translate('OpenSearch information');
	$text=WT_I18N::translate('This module generates OpenSearch document files. The OpenSearch description document can be used to describe the web interface of a search engine on this site. You should copy these files to the server directory where webtrees is installed.');
	$text.='<br /><br />';
	$text.=WT_I18N::translate('Then it is neccesairly to put the following code in the header of page:');
	$text.='<br /><b>&lt;link rel="Search" type="application/opensearchdescription+xml" title="';
	$text.=WT_I18N::translate('Search');
	$text.='" href="opensearch.xml" /&gt;</b>';
	$text.='<br /><br />';
	$text.=WT_I18N::translate('For more information on OpenSearch files, visit the <a href="http://www.opensearch.org/Home" target=\"_blank\">OpenSearch site</a>.');
	break;
}
