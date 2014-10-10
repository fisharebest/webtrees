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

use WT\Auth;

define('WT_SCRIPT_NAME', 'admin_trees_check.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isManager())
	->setPageTitle(WT_I18N::translate('Check for errors'))
	->pageHeader();

echo '<form method="get" action="', WT_SCRIPT_NAME, '">';
echo '<input type="hidden" name="go" value="1">';
echo select_edit_control('ged', WT_Tree::getNameList(), null, WT_GEDCOM);
echo '<input type="submit" value="', $controller->getPageTitle(), '">';
echo '</form>';

if (!WT_Filter::get('go')) {
	exit;
}

// We need to work with raw GEDCOM data, as we are looking for errors
// which may prevent the WT_GedcomRecord objects from working...

$rows=WT_DB::prepare(
	"SELECT i_id AS xref, 'INDI' AS type, i_gedcom AS gedrec FROM `##individuals` WHERE i_file=?".
	" UNION ".
	"SELECT f_id AS xref, 'FAM'  AS type, f_gedcom AS gedrec FROM `##families`    WHERE f_file=?".
	" UNION ".
	"SELECT s_id AS xref, 'SOUR' AS type, s_gedcom AS gedrec FROM `##sources`     WHERE s_file=?".
	" UNION ".
	"SELECT m_id AS xref, 'OBJE' AS type, m_gedcom AS gedrec FROM `##media`       WHERE m_file=?".
	" UNION ".
	"SELECT o_id AS xref, o_type AS type, o_gedcom AS gedrec FROM `##other`       WHERE o_file=? AND o_type NOT IN ('HEAD', 'TRLR')"
)->execute(array(WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID))->fetchAll();

$records=array();
foreach ($rows as $row) {
	$records[$row->xref]=$row;
}

// Need to merge pending new/changed/deleted records

$rows=WT_DB::prepare(
	"SELECT xref, SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(CASE WHEN old_gedcom='' THEN new_gedcom ELSE old_gedcom END, '\n', 1), ' ', 3), ' ', -1) AS type, new_gedcom AS gedrec".
	" FROM (".
	"  SELECT MAX(change_id) AS change_id".
	"  FROM `##change`".
	"  WHERE gedcom_id=? AND status='pending'".
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
	'NOTE'          => 'NOTE',
	'SOUR'          => 'SOUR',
	'REPO'          => 'REPO',
	'OBJE'          => 'OBJE',
	'SUBM'          => 'SUBM',
	'FAMC'          => 'FAM',
	'FAMS'          => 'FAM',
	//'ADOP'=>'FAM', // Need to handle this case specially.  We may have both ADOP and FAMC links to the same FAM, but only store one.
	'HUSB'          => 'INDI',
	'WIFE'          => 'INDI',
	'CHIL'          => 'INDI',
	'ASSO'          => 'INDI',
	'_ASSO'         => 'INDI', // A webtrees extension
	'ALIA'          => 'INDI',
	'AUTH'          => 'INDI', // A webtrees extension
	'ANCI'          => 'SUBM',
	'DESI'          => 'SUBM',
	'_WT_OBJE_SORT' => 'OBJE',
	'_LOC'          => '_LOC',
);

$RECORD_LINKS=array(
	'INDI'=>array('NOTE', 'OBJE', 'SOUR', 'SUBM', 'ASSO', '_ASSO', 'FAMC', 'FAMS', 'ALIA', '_WT_OBJE_SORT', '_LOC'),
	'FAM' =>array('NOTE', 'OBJE', 'SOUR', 'SUBM', 'ASSO', '_ASSO', 'HUSB', 'WIFE', 'CHIL', '_LOC'),
	'SOUR'=>array('NOTE', 'OBJE', 'REPO', 'AUTH'),
	'REPO'=>array('NOTE'),
	'OBJE'=>array('NOTE'), // The spec also allows SOUR, but we treat this as a warning
	'NOTE'=>array(), // The spec also allows SOUR, but we treat this as a warning
	'SUBM'=>array('NOTE', 'OBJE'),
	'SUBN'=>array('SUBM'),
	'_LOC'=>array('SOUR', 'OBJE', '_LOC'),
);

$errors=false;

echo '<fieldset><legend>', WT_I18N::translate('Types of error'), '</legend>';
echo '<p class="ui-state-error">',     WT_I18N::translate('This may cause a problem for webtrees.'),           '</p>';
echo '<p class="ui-state-highlight">', WT_I18N::translate('This may cause a problem for other applications.'), '</p>';
echo '<p class="warning-bad-data">',   WT_I18N::translate('This may be a mistake in your data.'),              '</p>';
echo '</fieldset>';

// Generate lists of all links
$all_links=array();
$upper_links=array();
foreach ($records as $record) {
	$all_links[$record->xref]=array();
	$upper_links[strtoupper($record->xref)]=$record->xref;
	preg_match_all('/\n\d ('.WT_REGEX_TAG.') @([^#@\n][^\n@]*)@/', $record->gedrec, $matches, PREG_SET_ORDER);
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
				echo warning(
					link_message($type1, $xref1, $type2, $xref2).' '.
					/* I18N: placeholders are GEDCOM XREFs, such as R123 */ WT_I18N::translate('%1$s does not exist.  Did you mean %2$s?', format_link($xref2), format_link($upper_links[strtoupper($xref2)]))
				);
			} else {
				echo error(
					link_message(
						$type1, $xref1, $type2, $xref2).' '.
						/* I18N: placeholders are GEDCOM XREFs, such as R123 */ WT_I18N::translate('%1$s does not exist.', format_link($xref2))
				);
			}
		} elseif ($type2=='SOUR' && $type1=='NOTE') {
			// Notes are intended to add explanations and comments to other records.  They should not have their own sources.
		} elseif ($type2=='SOUR' && $type1=='OBJE') {
			// Media objects are intended to illustrate other records, facts, and source/citations.  They should not have their own sources.
		} elseif ($type2=='OBJE' && $type1=='REPO') {
			echo warning(
				link_message($type1, $xref1, $type2, $xref2) . ' ' .  WT_I18N::translate('This type of link is not allowed here.')
			);
		} elseif (!array_key_exists($type1, $RECORD_LINKS) || !in_array($type2, $RECORD_LINKS[$type1]) || !array_key_exists($type2, $XREF_LINKS)) {
			echo error(
				link_message($type1, $xref1, $type2, $xref2).' '.
				WT_I18N::translate('This type of link is not allowed here.')
			);
		} elseif ($XREF_LINKS[$type2]!=$type3) {
			// Target XREF does exist - but is invalid
			echo error(
				link_message($type1, $xref1, $type2, $xref2).' '.
				/* I18N: %1$s is an internal ID number such as R123.  %2$s and %3$s are record types, such as INDI or SOUR */ WT_I18N::translate('%1$s is a %2$s but a %3$s is expected.', format_link($xref2), format_type($type3), format_type($type2))
			);
		} elseif (
			$type2=='FAMC' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='CHIL') ||
			$type2=='FAMS' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='HUSB' && $all_links[$xref2][$xref1]!='WIFE') ||
			$type2=='CHIL' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='FAMC') ||
			$type2=='HUSB' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='FAMS') ||
			$type2=='WIFE' && (!array_key_exists($xref1, $all_links[$xref2]) || $all_links[$xref2][$xref1]!='FAMS')
		) {
			echo error(
			link_message($type1, $xref1, $type2, $xref2).' '.
				/* I18N: %1$s and %2$s are internal ID numbers such as R123 */ WT_I18N::translate('%1$s does not have a link back to %2$s.', format_link($xref2), format_link($xref1))
			);
		}
	}
}

function link_message($type1, $xref1, $type2, $xref2) {
	return
		/* I18N: The placeholders are GEDCOM XREFs and tags.  e.g. “INDI I123 contains a FAMC link to F234.” */ WT_I18N::translate(
			'%1$s %2$s has a %3$s link to %4$s.',
			format_type($type1),
			format_link($xref1),
			format_type($type2),
			format_link($xref2)
		);
}

function format_link($xref) {
	return '<b><a href="gedrecord.php?pid='.$xref.'">'.$xref.'</a></b>';
}

function format_type($type) {
	return '<b title="'.strip_tags(WT_Gedcom_Tag::getLabel($type)).'">'.$type.'</b>';
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
	echo '<p>', WT_I18N::translate('No errors were found.'), '</p>';
}
