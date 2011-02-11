<?php
/**
* Miscellaneous administrative functions
*
*
* webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Partly Derived from PhpGedView
* Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
* @package webtrees
* @subpackage admin
* @version $Id$
*/

define('WT_SCRIPT_NAME', 'admin_site_other.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

$ged=$GEDCOM;
$gid1=safe_POST_xref('gid1');
$gid2=safe_POST_xref('gid2');
$action=safe_POST('action', WT_REGEX_ALPHA, 'choose');
$ged2=safe_POST('ged2', WT_REGEX_NOSCRIPT, $GEDCOM);
$keep1=safe_POST('keep1', WT_REGEX_UNSAFE);
$keep2=safe_POST('keep2', WT_REGEX_UNSAFE);
if (empty($keep1)) $keep1=array();
if (empty($keep2)) $keep2=array();

print_header(WT_I18N::translate('Add unlinked records'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

echo
	'<div id="other">',
	'<p>', WT_I18N::translate('Add unlinked records'), '</p>',
	'<table id="other">',
	'<tr><td>',
	'<a href="javascript:;" onclick="addnewchild(\'\'); return false;">', WT_I18N::translate('Add an unlinked person'), '</a>',
	help_link('edit_add_unlinked_person'),
	'</td></tr>',
	'<tr><td>',
	'<a href="javascript:;" onclick="addnewnote(\'\'); return false;">', WT_I18N::translate('Add an unlinked note'), '</a>',
	help_link('edit_add_unlinked_note'),
	'</td></tr>',
	'<tr><td>',
	'<a href="javascript:;" onclick="addnewsource(\'\'); return false;">', WT_I18N::translate('Add an unlinked source'), '</a>',
	help_link('edit_add_unlinked_source'),
	'</td></tr>',
	'</table>',
	'</div>';

print_footer();
