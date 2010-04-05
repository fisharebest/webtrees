<?php
/**
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009  PGV Development Team.  All rights reserved.
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
 * @package webtrees
 * @subpackage search
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'opensearch.php');
require './includes/session.php';

header('Content-Type: application/opensearchdescription+xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">';
echo '<ShortName>' . get_gedcom_setting(WT_GED_ID, 'title') . ' ' . i18n::translate('Search')  . '</ShortName>';
echo '<Description>' .  get_gedcom_setting(WT_GED_ID, 'title') . ' ' . i18n::translate('Search') . '</Description>';
echo '<InputEncoding>UTF-8</InputEncoding>';
echo '<Url type="text/html" template="' . $SERVER_URL. 'search.php?action=general&amp;topsearch=yes&amp;query={searchTerms}"/>';
echo '<Url type="application/x-suggestions+json" template="' . $SERVER_URL. 'autocomplete.php?limit=20&amp;field=NAME&amp;fmt=json&amp;q={searchTerms}"/>';
echo'<Image height="16" width="16" type="image/x-icon">' . $SERVER_URL. $FAVICON . '</Image>';
echo '</OpenSearchDescription>';
?>
