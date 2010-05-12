<?php
/**
 * Interface to review/accept/reject changes made by editing online.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Edit
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'edit_changes.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

if (!WT_USER_CAN_ACCEPT) {
	header('Location: login.php?url=edit_changes.php');
	exit;
}

$action=safe_GET('action');
$change_id=safe_GET('change_id');
$index =safe_GET('index');
$ged   =safe_GET('ged');

print_simple_header(i18n::translate('Review GEDCOM changes'));
?>
<script language="JavaScript" type="text/javascript">
<!--
	function show_gedcom_record(xref) {
		var recwin = window.open("gedrecord.php?fromfile=1&pid="+xref, "_blank", "top=50, left=50, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");
	}
	function showchanges() {
		window.location = '<?php echo WT_SCRIPT_NAME; ?>';
	}

	function show_diff(diffurl) {
		window.opener.location = diffurl;
		return false;
	}
//-->
</script>
<?php
echo '<div class="center"><span class="subheaders">', i18n::translate('Review GEDCOM Changes'), '</span><br /><br />';

switch ($action) {
case 'undo':
	$gedcom_id=WT_DB::prepare("SELECT gedcom_id FROM {$TBLPREFIX}change WHERE change_id=?")->execute(array($change_id))->fetchOne();
	$xref     =WT_DB::prepare("SELECT xref      FROM {$TBLPREFIX}change WHERE change_id=?")->execute(array($change_id))->fetchOne();
	// Undo a change, and subsequent changes to the same record
	WT_DB::prepare(
		"UPDATE {$TBLPREFIX}change".
		" SET   status     = 'rejected'".
		" WHERE status     = 'pending'".
		"	AND   gedcom_id  = ?".
		"	AND   xref       = ?".
		"	AND   change_id >= ?"
	)->execute(array($gedcom_id, $xref, $change_id));
	echo '<b>', i18n::translate('Undo successful'), '</b>';
	break;
case 'accept':
	$gedcom_id=WT_DB::prepare("SELECT gedcom_id FROM {$TBLPREFIX}change WHERE change_id=?")->execute(array($change_id))->fetchOne();
	$xref     =WT_DB::prepare("SELECT xref      FROM {$TBLPREFIX}change WHERE change_id=?")->execute(array($change_id))->fetchOne();
	// Accept a change, and all previous changes to the same record
	$changes=WT_DB::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, new_gedcom".
		" FROM  {$TBLPREFIX}change c".
		" JOIN  {$TBLPREFIX}gedcom g USING (gedcom_id)".
		" WHERE c.status   = 'pending'".
		"	AND   gedcom_id  = ?".
		"	AND   xref       = ?".
		"	AND   change_id <= ?".
		" ORDER BY change_id"
	)->execute(array($gedcom_id, $xref, $change_id))->fetchAll();
	foreach ($changes as $change) {
		update_record($change->new_gedcom, $change->gedcom_id, empty($change->new_gedcom));
		WT_DB::prepare("UPDATE {$TBLPREFIX}change SET status='accepted' WHERE change_id=?")->execute(array($change->change_id));
		AddToLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database", 'edit');
	}
	echo '<b>', i18n::translate('Changes successfully accepted into database'), '</b>';
	break;
case 'undoall':
	WT_DB::prepare(
		"UPDATE {$TBLPREFIX}change".
		" SET status='rejected'".
		" WHERE status='pending' AND gedcom_id=?"
	)->execute(array(get_id_from_gedcom($ged)));
	echo '<b>', i18n::translate('Undo successful'), '</b>';
	break;
case 'acceptall':
	$changes=WT_DB::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, new_gedcom".
		" FROM {$TBLPREFIX}change c".
		" JOIN {$TBLPREFIX}gedcom g USING (gedcom_id)".
		" WHERE c.status='pending' AND gedcom_id=?".
		" ORDER BY change_id"
	)->execute(array(get_id_from_gedcom($ged)))->fetchAll();
	foreach ($changes as $change) {
		update_record($change->new_gedcom, $change->gedcom_id, empty($change->new_gedcom));
		WT_DB::prepare("UPDATE {$TBLPREFIX}change SET status='accepted' WHERE change_id=?")->execute(array($change->change_id));
		AddToLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database", 'edit');
	}
	echo '<b>', i18n::translate('Changes successfully accepted into database'), '</b>';
	break;
}

$changed_gedcoms=WT_DB::prepare(
	"SELECT g.gedcom_name".
	" FROM {$TBLPREFIX}change c".
	" JOIN {$TBLPREFIX}gedcom g USING (gedcom_id)".
	" WHERE c.status='pending'".
	" GROUP BY g.gedcom_name"
)->fetchOneColumn();

if (!$changed_gedcoms) {
	echo '<br /><br /><b>', i18n::translate('There are currently no changes to be reviewed.'), '</b>';
} else {
	$changes=WT_DB::prepare(
		"SELECT c.*, u.user_name, u.real_name, g.gedcom_name".
		" FROM {$TBLPREFIX}change c".
		" JOIN {$TBLPREFIX}user   u USING (user_id)".
		" JOIN {$TBLPREFIX}gedcom g USING (gedcom_id)".
		" WHERE c.status='pending'".
		" ORDER BY gedcom_id, c.xref, c.change_id"
	)->fetchAll();

	$output = '<br /><br /><table class="list_table">';
	$prev_xref=null;
	$prev_gedcom_id=null;
	foreach ($changes as $change) {
		if ($change->xref!=$prev_xref || $change->gedcom_id!=$prev_gedcom_id) {
			if ($prev_xref) {
				$output.='</table></td></tr>';
			}
			$prev_xref     =$change->xref;
			$prev_gedcom_id=$change->gedcom_id;
			$output.='<tr><td class="list_value '.$TEXT_DIRECTION.'">';
			$GEDCOM=$change->gedcom_name;
			$record=GedcomRecord::getInstance($change->xref);
			$output.='<b>'.PrintReady($record->getFullName()).'</b> '.getLRM().'('.$record->getXref().')'.getLRM().'<br />';
			$output.='<a href="javascript:;" onclick="return show_diff(\''.encode_url($record->getLinkUrl().'&show_changes=yes').'\');">'.i18n::translate('View Change Diff').'</a> | ';
			$output.="<a href=\"javascript:show_gedcom_record('".$change->xref."');\">".i18n::translate('View GEDCOM Record')."</a> | ";
			$output.="<a href=\"javascript:;\" onclick=\"return edit_raw('".$change->xref."');\">".i18n::translate('Edit raw GEDCOM record')."</a><br />";
			$output.='<div class="indent">';
			$output.=i18n::translate('The following changes were made to this record:').'<br />';
			$output.='<table class="list_table"><tr>';
			$output.='<td class="list_label">'.i18n::translate('Accept').'</td>';
			$output.='<td class="list_label">'.i18n::translate('Type').'</td>';
			$output.='<td class="list_label">'.i18n::translate('User name').'</td>';
			$output.='<td class="list_label">'.i18n::translate('Date').'</td>';
			$output.='<td class="list_label">GEDCOM</td>';
			$output.='<td class="list_label">'.i18n::translate('Undo').'</td>';
			$output.='</tr>';
		}
		$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"".encode_url("edit_changes.php?action=accept&change_id={$change->change_id}")."\">".i18n::translate('Accept')."</a></td>";
		$output .= "<td class=\"list_value $TEXT_DIRECTION\"><b>";
		if ($change->old_gedcom=='') {
			$output.=i18n::translate('Append record');
		} elseif ($change->new_gedcom=='') {
			$output.=i18n::translate('Delete record');
		} else {
			$output.=i18n::translate('Replace record');
		}
		echo "</b></td>";
		$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"javascript:;\" onclick=\"return reply('".$change->user_name."', '".i18n::translate('Review GEDCOM Changes')."')\" alt=\"".i18n::translate('Send Message')."\">";
		$output .= PrintReady($change->real_name);
 		$output .= PrintReady("&nbsp;(".$change->user_name.")")."</a></td>";
 		$output .= "<td class=\"list_value $TEXT_DIRECTION\">".$change->change_time."</td>";
		$output .= "<td class=\"list_value $TEXT_DIRECTION\">".$change->gedcom_name."</td>";
		$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"".encode_url("edit_changes.php?action=undo&change_id={$change->change_id}")."\">".i18n::translate('Undo')."</a></td>";
		$output.='</tr>';
	}
	$output .= "</table></td></tr></td></tr></table>";

	//-- Now for the global Action bar:
	$output2 = "<br /><table class=\"list_table\">";
	// Row 1 column 1: title "Accept all"
	$output2 .= "<tr><td class=\"list_label\">".i18n::translate('Accept all changes')."</td>";
	// Row 1 column 2: separator
	$output2 .= "<td class=\"list_label width25\">&nbsp;</td>";
	// Row 1 column 3: title "Undo all"
	$output2 .= "<td class=\"list_label\">".i18n::translate('Undo all changes')."</td></tr>";

	// Row 2 column 1: action "Accept all"
	$output2 .= "<tr><td class=\"list_value\">";
	$count = 0;
	foreach ($changed_gedcoms as $gedcom_name) {
		if ($count!=0) $output2.="<br /><br />";
		$output2 .= "<a href=\"".encode_url("edit_changes.php?action=acceptall&ged={$gedcom_name}")."\">$gedcom_name - ".i18n::translate('Accept all changes')."</a>";
		$count ++;
	}
	$output2 .= "</td>";
	// Row 2 column 2: separator
	$output2 .= "<td class=\"list_value width25\">&nbsp;</td>";
	// Row 2 column 3: action "Undo all"
	$output2 .= "<td class=\"list_value\">";
	$count = 0;
	foreach ($changed_gedcoms as $gedcom_name) {
		if ($count!=0) $output2.="<br /><br />";
		$output2 .= "<a href=\"".encode_url("edit_changes.php?action=undoall&ged={$gedcom_name}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to undo all of the changes for this GEDCOM?')."');\">$gedcom_name - ".i18n::translate('Undo all changes')."</a>";
		$count ++;
	}
	$output2 .= '</td></tr></table>';

	echo '<center>', i18n::translate('Decide for each change to either accept or reject it.<br /><br />To accept all changes at once, click <b>"Accept all changes"</b> in the box below.<br />To get more information about a change,<br />click <b>"View change diff"</b> to see the differences,<br />or click <b>"View GEDCOM record"</b> to see the new data in GEDCOM format.'), '<br />', $output2, $output, $output2, '</center>';
}

echo '</div>';

echo "<br /><br /><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">", i18n::translate('Close Window'), '</a><br /></center>';
print_simple_footer();
?>
