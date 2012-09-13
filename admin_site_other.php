<?php
// Miscellaneous administrative functions
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Partly Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'admin_site_other.php');
require './includes/session.php';

$controller=new WT_Controller_Base();
$controller->setPageTitle(WT_I18N::translate('Add unlinked records'));
$controller->pageHeader();

// The addnewXXX() functions work only for the default tree.
// Choose one...
$html='<p><form method="post" action="'.WT_SCRIPT_NAME.'" name="tree"><select name="ged" onChange="tree.submit();">';
$n=0;
foreach (WT_Tree::getAll() as $tree) {
	if (userGedcomAdmin(WT_USER_ID, $tree->tree_id)) {
		$html.='<option value="'.$tree->tree_name_url.'"';
		if ($tree->tree_id==WT_GED_ID) {
			$html.=' selected="selected"';
		}
		$html.='>'.$tree->tree_title_html.'</option>';
		++$n;
	}
}
$html.='</select></form></p>';

// Don't show gedcom list if there is only one...
if ($n==1) {
	$html='';
}


echo
	'<div id="other">',
	'<p>', WT_I18N::translate('Add unlinked records'), '</p>',
	$html,
	'<table id="other">',
	'<tr><td>',
	'<a href="#" onclick="addnewchild(\'\'); return false;">', WT_I18N::translate('Add an unlinked person'), '</a>',
	help_link('edit_add_unlinked_person'),
	'</td></tr>',
	'<tr><td>',
	'<a href="#" onclick="addnewnote(\'\'); return false;">', WT_I18N::translate('Add an unlinked note'), '</a>',
	help_link('edit_add_unlinked_note'),
	'</td></tr>',
	'<tr><td>',
	'<a href="#" onclick="addnewsource(\'\'); return false;">', WT_I18N::translate('Add an unlinked source'), '</a>',
	help_link('edit_add_unlinked_source'),
	'</td></tr>',
	'</table>',
	'</div>';
