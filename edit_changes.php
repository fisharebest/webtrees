<?php
// Interface to moderate pending changes.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'edit_changes.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller=new WT_Controller_Simple();
$controller
	->requireAcceptLogin()
	->setPageTitle(WT_I18N::translate('Pending changes'))
	->addExternalJavascript(WT_JQUERY_URL)
	->addExternalJavascript(WT_STATIC_URL.'js/webtrees.js')
	->pageHeader();

$action   =safe_GET('action');
$change_id=safe_GET('change_id');
$index    =safe_GET('index');
$ged      =safe_GET('ged');

echo '<script>';
?>
	function show_gedcom_record(xref) {
		var recwin = window.open("gedrecord.php?fromfile=1&pid="+xref, "_blank", edit_window_specs);
	}

	function show_diff(diffurl) {
		window.opener.location = diffurl;
		return false;
	}
<?php
echo '</script>';
echo '<div id="pending"><h2>', WT_I18N::translate('Pending changes'), '</h2>';

switch ($action) {
case 'undo':
	$gedcom_id=WT_DB::prepare("SELECT gedcom_id FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	$xref     =WT_DB::prepare("SELECT xref      FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	// Undo a change, and subsequent changes to the same record
	WT_DB::prepare(
		"UPDATE `##change`".
		" SET   status     = 'rejected'".
		" WHERE status     = 'pending'".
		" AND   gedcom_id  = ?".
		" AND   xref       = ?".
		" AND   change_id >= ?"
	)->execute(array($gedcom_id, $xref, $change_id));
	break;
case 'accept':
	$gedcom_id=WT_DB::prepare("SELECT gedcom_id FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	$xref     =WT_DB::prepare("SELECT xref      FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	// Accept a change, and all previous changes to the same record
	$changes=WT_DB::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, old_gedcom, new_gedcom".
		" FROM  `##change` c".
		" JOIN  `##gedcom` g USING (gedcom_id)".
		" WHERE c.status   = 'pending'".
		" AND   gedcom_id  = ?".
		" AND   xref       = ?".
		" AND   change_id <= ?".
		" ORDER BY change_id"
	)->execute(array($gedcom_id, $xref, $change_id))->fetchAll();
	foreach ($changes as $change) {
		if (empty($change->new_gedcom)) {
			// delete
			update_record($change->old_gedcom, $gedcom_id, true);
		} else {
			// add/update
			update_record($change->new_gedcom, $gedcom_id, false);
		}
		WT_DB::prepare("UPDATE `##change` SET status='accepted' WHERE change_id=?")->execute(array($change->change_id));
		AddToLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database", 'edit');
	}
	break;
case 'undoall':
	WT_DB::prepare(
		"UPDATE `##change`".
		" SET status='rejected'".
		" WHERE status='pending' AND gedcom_id=?"
	)->execute(array(WT_GED_ID));
	break;
case 'acceptall':
	$changes=WT_DB::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, old_gedcom, new_gedcom".
		" FROM `##change` c".
		" JOIN `##gedcom` g USING (gedcom_id)".
		" WHERE c.status='pending' AND gedcom_id=?".
		" ORDER BY change_id"
	)->execute(array(WT_GED_ID))->fetchAll();
	foreach ($changes as $change) {
		if (empty($change->new_gedcom)) {
			// delete
			update_record($change->old_gedcom, $change->gedcom_id, true);
		} else {
			// add/update
			update_record($change->new_gedcom, $change->gedcom_id, false);
		}
		WT_DB::prepare("UPDATE `##change` SET status='accepted' WHERE change_id=?")->execute(array($change->change_id));
		AddToLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database", 'edit');
	}
	break;
}

$changed_gedcoms=WT_DB::prepare(
	"SELECT g.gedcom_name".
	" FROM `##change` c".
	" JOIN `##gedcom` g USING (gedcom_id)".
	" WHERE c.status='pending'".
	" GROUP BY g.gedcom_name"
)->fetchOneColumn();

if ($changed_gedcoms) {
	$changes=WT_DB::prepare(
		"SELECT c.*, u.user_name, u.real_name, g.gedcom_name, IF(new_gedcom='', old_gedcom, new_gedcom) AS gedcom".
		" FROM `##change` c".
		" JOIN `##user`   u USING (user_id)".
		" JOIN `##gedcom` g USING (gedcom_id)".
		" WHERE c.status='pending'".
		" ORDER BY gedcom_id, c.xref, c.change_id"
	)->fetchAll();

	$output = '<br><br><table class="list_table">';
	$prev_xref=null;
	$prev_gedcom_id=null;
	foreach ($changes as $change) {
		if ($change->xref!=$prev_xref || $change->gedcom_id!=$prev_gedcom_id) {
			if ($prev_xref) {
				$output.='</table></td></tr>';
			}
			$prev_xref     =$change->xref;
			$prev_gedcom_id=$change->gedcom_id;
			$output.='<tr><td class="list_value">';
			$GEDCOM=$change->gedcom_name;
			$record=WT_GedcomRecord::getInstance($change->xref);
			if (!$record) {
				// When a record has been both added and deleted, then
				// neither the original nor latest version will exist.
				// This prevents us from displaying it...
				// This generates a record of some sorts from the last-but-one
				// version of the record.
				$record=new WT_GedcomRecord($change->gedcom);
			}
			$output.='<b>'.$record->getFullName().'</b><br>';
			$output.='<a href="#" onclick="return show_diff(\''.$record->getHtmlUrl().'\');">'.WT_I18N::translate('View the changes').'</a> | ';
			$output.="<a href=\"#\" onclick=\"show_gedcom_record('".$change->xref."');\">".WT_I18N::translate('View GEDCOM Record')."</a> | ";
			$output.="<a href=\"#\" onclick=\"return edit_raw('".$change->xref."');\">".WT_I18N::translate('Edit raw GEDCOM record').'</a><br>';
			$output.='<div class="indent">';
			$output.=WT_I18N::translate('The following changes were made to this record:').'<br>';
			$output.='<table class="list_table"><tr>';
			$output.='<td class="list_label">'.WT_I18N::translate('Accept').'</td>';
			$output.='<td class="list_label">'.WT_I18N::translate('Type').'</td>';
			$output.='<td class="list_label">'.WT_I18N::translate('User').'</td>';
			$output.='<td class="list_label">'.WT_I18N::translate('Date').'</td>';
			$output.='<td class="list_label">'.WT_I18N::translate('Family tree').'</td>';
			$output.='<td class="list_label">'.WT_I18N::translate('Undo').'</td>';
			$output.='</tr>';
		}
		$output .= '<td class="list_value"><a href="edit_changes.php?action=accept&amp;ged='.rawurlencode($change->gedcom_name).'&amp;change_id='.$change->change_id.'">'.WT_I18N::translate('Accept').'</a></td>';
		$output .= '<td class="list_value"><b>';
		if ($change->old_gedcom=='') {
			$output.=WT_I18N::translate('Append record');
		} elseif ($change->new_gedcom=='') {
			$output.=WT_I18N::translate('Delete record');
		} else {
			$output.=WT_I18N::translate('Replace record');
		}
		echo '</b></td>';
		$output .= "<td class=\"list_value\"><a href=\"#\" onclick=\"return reply('".$change->user_name."', '".WT_I18N::translate('Moderate pending changes')."')\" alt=\"".WT_I18N::translate('Send Message')."\">";
		$output .= htmlspecialchars($change->real_name);
		$output .= ' - '.htmlspecialchars($change->user_name).'</a></td>';
		$output .= '<td class="list_value">'.$change->change_time.'</td>';
		$output .= '<td class="list_value">'.$change->gedcom_name.'</td>';
		$output .= '<td class="list_value"><a href="edit_changes.php?action=undo&amp;ged='.rawurlencode($change->gedcom_name).'&amp;change_id='.$change->change_id.'">'.WT_I18N::translate('Undo').'</a></td>';
		$output.='</tr>';
	}
	$output .= '</table></td></tr></td></tr></table>';

	//-- Now for the global Action bar:
	$output2 = '<br><table class="list_table">';
	// Row 1 column 1: title "Accept all"
	$output2 .= '<tr><td class="list_label">'.WT_I18N::translate('Approve all changes').'</td>';
	// Row 1 column 2: title "Undo all"
	$output2 .= '<td class="list_label">'.WT_I18N::translate('Undo all changes').'</td></tr>';

	// Row 2 column 1: action "Accept all"
	$output2 .= '<tr><td class="list_value">';
	$count = 0;
	foreach ($changed_gedcoms as $gedcom_name) {
		if ($count!=0) $output2.='<br>';
		$output2 .= '<a href="edit_changes.php?action=acceptall&amp;ged='.rawurlencode($gedcom_name).'">'.$gedcom_name.' - '.WT_I18N::translate('Approve all changes').'</a>';
		$count ++;
	}
	$output2 .= '</td>';
	// Row 2 column 2: action "Undo all"
	$output2 .= '<td class="list_value">';
	$count = 0;
	foreach ($changed_gedcoms as $gedcom_name) {
		if ($count!=0) {
			$output2.='<br>';
		}
		$output2 .= '<a href="edit_changes.php?action=undoall&amp;ged='.rawurlencode($gedcom_name)."\" onclick=\"return confirm('".WT_I18N::translate('Are you sure you want to undo all of the changes for this GEDCOM?')."');\">$gedcom_name - ".WT_I18N::translate('Undo all changes').'</a>';
		$count++;
	}
	$output2 .= '</td></tr></table>';

	echo
		$output2, $output, $output2,
		'<p class="center"><a href="#" onclick="closePopupAndReloadParent();">', WT_I18N::translate('Close Window'), '</a></p>';
} else {
	// No pending changes - refresh the parent window and close this one
	$controller->addInlineJavascript('closePopupAndReloadParent();');
}

echo '</div>';
