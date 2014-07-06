<?php
// Check a family tree for structural errors.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License or,
// at your discretion, any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

define('WT_SCRIPT_NAME', 'admin_trees_renumber.php');
require './includes/session.php';

$controller=new WT_Controller_Page();
$controller
	->restrictAccess(\WT\Auth::isManager())
	->setPageTitle(WT_I18N::translate('Merge family tree'))
	->pageHeader();

// Every XREF used by this tree and also any other tree
$xrefs = WT_DB::prepare(
	"SELECT xref, type FROM (" .
	" SELECT i_id AS xref, 'INDI' AS type FROM `##individuals` WHERE i_file = ?" .
	"  UNION " .
	" SELECT f_id AS xref, 'FAM' AS type FROM `##families` WHERE f_file = ?" .
	"  UNION " .
	" SELECT s_id AS xref, 'SOUR' AS type FROM `##sources` WHERE s_file = ?" .
	"  UNION " .
	" SELECT m_id AS xref, 'OBJE' AS type FROM `##media` WHERE m_file = ?" .
	"  UNION " .
	" SELECT o_id AS xref, o_type AS type FROM `##other` WHERE o_file = ? AND o_type NOT IN ('HEAD', 'TRLR')" .
	") AS this_tree JOIN (".
	" SELECT xref FROM `##change` WHERE gedcom_id <> ?" .
	"  UNION " .
	" SELECT i_id AS xref FROM `##individuals` WHERE i_file <> ?" .
	"  UNION " .
	" SELECT f_id AS xref FROM `##families` WHERE f_file <> ?" .
	"  UNION " .
	" SELECT s_id AS xref FROM `##sources` WHERE s_file <> ?" .
	"  UNION " .
	" SELECT m_id AS xref FROM `##media` WHERE m_file <> ?" .
	"  UNION " .
	" SELECT o_id AS xref FROM `##other` WHERE o_file <> ? AND o_type NOT IN ('HEAD', 'TRLR')" .
	") AS other_trees USING (xref)"
)->execute(array(
		WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID,
		WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID
	))->fetchAssoc();

echo '<h2>', $WT_TREE->tree_title_html, '</h2>';

echo WT_I18N::translate(
	/* I18N:  Copy all the records from [family tree 1] into [family tree 2] */ 'Copy all the records from %1$s into %2$s.',
	WT_Filter::escapeHtml(WT_GEDCOM),
	WT_Filter::escapeHtml(WT_GEDCOM)
);

if ($xrefs) {
	echo
		'<p>',
		WT_I18N::plural(
			'This family tree has %s record which uses the same “XREF” as another family tree.',
			'This family tree has %s records which use the same “XREF” as another family tree.',
			count($xrefs), count($xrefs)
		),
		'</p>',
		'<p>',
		WT_Translate('You must renumber one of the trees before you can merge them.'),
		'</p>';
}

if (WT_Filter::get('go')) {
}