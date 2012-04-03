<?php
// Check a family tree for structural errors.
//
// Note that the tests and error messages are not yet finalised.  Wait until the code has stabilised before
// adding I18N.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2006-2009 Greg Roach, all rights reserved
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
//$Id$

define('WT_SCRIPT_NAME', 'admin_trees_check.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller=new WT_Controller_Base();
$controller
	->requireManagerLogin()
	->setPageTitle(WT_I18N::translate('Check for errors'))
	->pageHeader();

echo '<form method="get" action="', WT_SCRIPT_NAME, '">';
echo '<input type="hidden" name="go" value="1">';
echo select_edit_control('ged', array_combine(get_all_gedcoms(), get_all_gedcoms()), null, WT_GEDCOM);
echo '<input type="submit" value="', $controller->getPageTitle(), '">';
echo '</form>';

if (!safe_GET('go')) {
	exit;
}

// We need to work with raw GEDCOM data, as we are looking for errors
// which may prevent the WT_GedcomRecord objects from working...

$rows=WT_DB::prepare(
	"SELECT i_id    AS xref, 'INDI' AS type, i_gedcom AS gedrec FROM `##individuals` WHERE i_file=?".
	" UNION ".
	"SELECT f_id    AS xref, 'FAM'  AS type, f_gedcom AS gedrec FROM `##families`    WHERE f_file=?".
	" UNION ".
	"SELECT s_id    AS xref, 'SOUR' AS type, s_gedcom AS gedrec FROM `##sources`     WHERE s_file=?".
	" UNION ".
	"SELECT m_media AS xref, 'OBJE' AS type, m_gedrec AS gedrec FROM `##media`       WHERE m_gedfile=?".
	" UNION ".
	"SELECT o_id    AS xref, o_type AS type, o_gedcom AS gedrec FROM `##other`       WHERE o_file=? AND o_type NOT IN ('HEAD', 'TRLR')"
)->execute(array(WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID))->fetchAll();

$records=array();
foreach ($rows as $row) {
	$records[$row->xref]=$row;
}

// Need to merge pending new/changed/deleted records

$rows=WT_DB::prepare(
	" SELECT xref, SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(CASE WHEN old_gedcom='' THEN new_gedcom ELSE old_gedcom END, '\n', 1), ' ', 3), ' ', -1) AS type, new_gedcom AS gedrec".
	" FROM (".
	"  SELECT MAX(change_id) AS change_id".
	"  FROM `##change`".
	"  WHERE gedcom_id=?".
	"  GROUP BY xref".
	" ) AS t1".
	" JOIN `##change` t2 USING (change_id)"
)->execute(array(WT_GED_ID))->fetchAll();

foreach ($rows as $row) {
	if ($row->gedrec) {
		// new/updated record
		$records[$row->xref]=$row;
	} else {
		// deleted record
		unset($records[$row->xref]);
	}
}

// Keep a list of upper case XREFs, to detect mismatches.
$ukeys=array();
foreach (array_keys($records) as $key) {
	$ukeys[strtoupper($key)]=$key;
}

// LOOK FOR BROKEN LINKS
$XREF_LINKS=array(
	'NOTE'=>'NOTE',
	'SOUR'=>'SOUR',
	'REPO'=>'REPO',
	'OBJE'=>'OBJE',
	'FAMC'=>'FAM',
	'FAMS'=>'FAM',
	//'ADOP'=>'FAM', // Need to handle this case specially.  We may have both ADOP and FAMC links to the same FAM, but only store one.
	'HUSB'=>'INDI',
	'WIFE'=>'INDI',
	'CHIL'=>'INDI',
	'ASSO'=>'INDI',
	'ALIA'=>'INDI',
	'ANCI'=>'SUBM',
	'DESI'=>'SUBM',
	'_WT_OBJE_SORT'=>'OBJE'
);

$RECORD_LINKS=array(
	'INDI'=>array('FAMC', 'FAMS', 'OBJE', 'NOTE', 'SOUR', 'ASSO'),
	'FAM' =>array('HUSB','WIFE',  'CHIL', 'OBJE', 'NOTE', 'SOUR', 'ASSO'),
	'SOUR'=>array('NOTE', 'OBJE', 'REPO'),
	'NOTE'=>array(), // The spec also allows SOUR, but we treat this as a warning
	'REPO'=>array('NOTE'),
	'OBJE'=>array('NOTE'), // The spec also allows SOUR, but we treat this as a warning
	'SUBM'=>array(),
	'SUBN'=>array(),
);

$errors=false;

echo '<fieldset><legend>Key</legend>';
echo '<p class="ui-state-error">These errors may cause problems for webtrees.</p>';
echo '<p class="ui-state-highlight">These warnings may cause problems for other applications.</p>';
if (get_gedcom_setting(WT_GED_ID, 'HIDE_GEDCOM_ERRORS')) {
	echo '<p>You may wish to enable the display of GEDCOM errors in the family tree preferences.</p>';
}
echo '</fieldset>';

// Generate lists of all links
$all_links=array();
$upper_links=array();
foreach ($records as $record) {
	$all_links[$record->xref]=array();
	$upper_links[strtoupper($record->xref)]=$record->xref;
	preg_match_all('/\n\d ([^ ]+) @([^#@][^@]*)@/', $record->gedrec, $matches, PREG_SET_ORDER);
	foreach ($matches as $match) {
		$all_links[$record->xref][$match[2]]=$match[1];
	}
}

foreach ($all_links as $xref1=>$links) {
	$type1=$records[$xref1]->type;
	foreach ($links as $xref2=>$type2) {
		$type3=@$records[$xref2]->type;
		if (!array_key_exists($xref2, $all_links)) {
			if (array_key_exists(strtoupper($xref2), $upper_links)) {
				echo error('The record '.make_link($xref1).' contains a link to a non-existant record, <b>'.$type2.':'.$xref2.'</b>.  Did you mean the record '.make_link(strtoupper($xref2)).'?');
			} else {
				echo error('The record '.make_link($xref1).' contains a link to a non-existant record, <b>'.$type2.':'.$xref2.'</b>.');
			}
		} elseif ($type2=='SOUR' && $type1=='NOTE') {
			echo warning('The note '.make_link($xref1).' contains a linked source, '.make_link($xref2).'. Notes are intended to add explanations and comments to other records.  They would not normally have their own sources.');
		} elseif ($type2=='SOUR' && $type1=='OBJE') {
			echo warning('The media object '.make_link($xref1).' contains a linked source, '.make_link($xref2).'. Media objects are inteded to illustrate other records, facts, and source/citations.  They would not normally have their own sources.');
		} elseif (!array_key_exists($type1, $RECORD_LINKS) || !in_array($type2, $RECORD_LINKS[$type1])) {
			echo error('The record '.make_link($xref1).' has an invalid link: <b>'.$type2.':'.$xref2.'</b>.');
		} elseif (!array_key_exists($type2, $XREF_LINKS)) {
			echo error('The record '.make_link($xref1).' contains a '.$type2.' link to '.make_link($xref2).', but '.$type2.' is not a valid link.');
		} elseif ($XREF_LINKS[$type2]!=$type3) {
			// Target XREF does exist - but is invalid
			echo error('The record '.make_link($xref1).' contains a '.$type2.' link to '.make_link($xref2).', but this record is a '.$type3.'.');
		} else {
			switch ($type2) {
			case 'FAMC':
				if (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='CHIL') {
					echo error('The individual '.make_link($xref1).' links to '.make_link($xref2).' as a child, but this family does not link back to the child.');
				}
				break;
			case 'FAMS':
				if (!array_key_exists($xref1, $all_links[$xref2]) || ($all_links[$xref2][$xref1]!='HUSB' && $all_links[$xref2][$xref1]!='WIFE')) {
					echo error('The individual '.make_link($xref1).' links to '.make_link($xref2).' as a spouse, but this family does not link back to the spouse.');
				}
				break;
			case 'HUSB':
			case 'WIFE':
				if (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='FAMS') {
					echo error('The family '.make_link($xref1).' contains '.make_link($xref2).' as a spouse, but this individual does not link back to the family.');
				}
				break;
			case 'CHIL':
				if (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='FAMC') {
					echo error('The family '.make_link($xref1).' contains '.make_link($xref2).' as a child, but this individual does not link back to the family.');
				}
				break;
			}
		}
	}
}

function make_link($xref) {
	return '<b><a href="gedrecord.php?pid='.$xref.'">'.$xref.'</a></b>';
}
function error($message) {
	global $errors;
	$errors=true;
	return '<p class="ui-state-error">'.$message.'</p>';
}
function warning($message) {
	global $errors;
	$errors=true;
	return '<p class="ui-state-highlight">'.$message.'</p>';
}

if (!$errors) {
	echo '<p>No errors were found.</p>';
}
