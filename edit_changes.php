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

require $INDEX_DIRECTORY.'pgv_changes.php';

$action=safe_GET('action');
$cid   =safe_GET('cid');
$index =safe_GET('index');
$ged   =safe_GET('ged');

print_simple_header(i18n::translate('Review GEDCOM Changes'));
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
	if (undo_change($cid, $index)) {
		echo '<b>', i18n::translate('Undo successful'), '</b>';
	}
	break;
case 'accept':
	if (accept_changes($cid)) {
		echo '<b>', i18n::translate('Changes successfully accepted into database'), '</b>';
	}
	break;
case 'undoall':
	//-- alert that we only want to save the file and changes once
	$manual_save = true;
	foreach ($pgv_changes as $cid=>$changes) {
		if ($changes[0]['gedcom']==$ged) {
			undo_change($cid, 0);
		}
	}
	write_changes();
	$manual_save = false;
	echo '<b>', i18n::translate('Undo successful'), '</b>';
	break;
case 'acceptall':
	//-- only save the file and changes once
	$manual_save = true;
	foreach ($pgv_changes as $cid=>$changes) {
		if ($changes[0]['gedcom']==$ged) {
			accept_changes($cid);
		}
	}
	write_changes();
	$manual_save = false;
	if ($SYNC_GEDCOM_FILE) {
		write_file();
	}
	echo '<b>', i18n::translate('Changes successfully accepted into database'), '</b>';
	break;
}

if (empty($pgv_changes)) {
	echo '<br /><br /><b>', i18n::translate('There are currently no changes to be reviewed.'), '</b>';
} else {
	$output = '<br /><br /><table class="list_table"><tr><td class="list_value '.$TEXT_DIRECTION.'">';
	$changedgedcoms = array();
	foreach ($pgv_changes as $cid=>$changes) {
		foreach ($changes as $i=>$change) {
			if ($i==0) {
				$changedgedcoms[$change['gedcom']] = true;
				$GEDCOM=$change['gedcom'];

				$record=GedcomRecord::getInstance($change['gid']);
				$output.='<b>'.PrintReady($record->getFullName()).'</b> '.getLRM().'('.$record->getXref().')'.getLRM().'<br />';
				$output.='<a href="javascript:;" onclick="return show_diff(\''.encode_url($record->getLinkUrl().'&show_changes=yes').'\');">'.i18n::translate('View Change Diff').'</a> | ';
				$output.="<a href=\"javascript:show_gedcom_record('".$change['gid']."');\">".i18n::translate('View GEDCOM Record')."</a> | ";
				$output.="<a href=\"javascript:;\" onclick=\"return edit_raw('".$change['gid']."');\">".i18n::translate('Edit raw GEDCOM record')."</a><br />";
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
			if ($i==count($changes)-1) {
				$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"".encode_url("edit_changes.php?action=accept&cid={$cid}")."\">".i18n::translate('Accept')."</a></td>";
			} else {
				$output .= "<td class=\"list_value $TEXT_DIRECTION\">&nbsp;</td>";
			}
			$output .= "<td class=\"list_value $TEXT_DIRECTION\"><b>";
			switch ($change['type']) {
			case 'append':  $output.=i18n::translate('Append record'); break;
			case 'delete':  $output.=i18n::translate('Delete record'); break;
			case 'replace': $output.=i18n::translate('Replace record'); break;
			}
			echo "</b></td>";
			$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"javascript:;\" onclick=\"return reply('".$change['user']."', '".i18n::translate('Review GEDCOM Changes')."')\" alt=\"".i18n::translate('Send Message')."\">";
			if ($user_id=get_user_id($change['user'])) {
				$output.=PrintReady(getUserFullName($user_id));
			}
 			$output .= PrintReady("&nbsp;(".$change['user'].")")."</a></td>";
 			$output .= "<td class=\"list_value $TEXT_DIRECTION\">".format_timestamp($change['time'])."</td>";
			$output .= "<td class=\"list_value $TEXT_DIRECTION\">".$change['gedcom']."</td>";
			if ($i==count($changes)-1) {
				$output .= "<td class=\"list_value $TEXT_DIRECTION\"><a href=\"".encode_url("edit_changes.php?action=undo&cid={$cid}&index={$i}")."\">".i18n::translate('Undo')."</a></td>";
			} else {
				$output .= "<td class=\"list_value $TEXT_DIRECTION\">&nbsp;</td>";
			}
			$output.='</tr>';
			if ($i==count($changes)-1) {
				$output.='</table></div><br />';
			}
		}
	}
	$output .= "</td></tr></table>";

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
	foreach ($changedgedcoms as $ged=>$value) {
		if ($count!=0) $output2.="<br /><br />";
		$output2 .= "<a href=\"".encode_url("edit_changes.php?action=acceptall&ged={$ged}")."\">$ged - ".i18n::translate('Accept all changes')."</a>";
		$count ++;
	}
	$output2 .= "</td>";
	// Row 2 column 2: separator
	$output2 .= "<td class=\"list_value width25\">&nbsp;</td>";
	// Row 2 column 3: action "Undo all"
	$output2 .= "<td class=\"list_value\">";
	$count = 0;
	foreach ($changedgedcoms as $ged=>$value) {
		if ($count!=0) $output2.="<br /><br />";
		$output2 .= "<a href=\"".encode_url("edit_changes.php?action=undoall&ged={$ged}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to undo all of the changes for this GEDCOM?')."');\">$ged - ".i18n::translate('Undo all changes')."</a>";
		$count ++;
	}
	$output2 .= '</td></tr></table>';

	echo '<center>', i18n::translate('Decide for each change to either accept or reject it.<br /><br />To accept all changes at once, click <b>"Accept all changes"</b> in the box below.<br />To get more information about a change,<br />click <b>"View change diff"</b> to see the differences,<br />or click <b>"View GEDCOM record"</b> to see the new data in GEDCOM format.'), '<br />', $output2, $output, $output2, '</center>';
}

echo '</div>';

echo "<br /><br /><center><a href=\"javascript:;\" onclick=\"if (window.opener.showchanges) window.opener.showchanges(); window.close();\">", i18n::translate('Close Window'), '</a><br /></center>';
print_simple_footer();
?>
