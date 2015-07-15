<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\SimpleController;
use Fisharebest\Webtrees\Functions\FunctionsImport;

define('WT_SCRIPT_NAME', 'edit_changes.php');
require './includes/session.php';

$controller = new SimpleController;
$controller
	->restrictAccess(Auth::isModerator($WT_TREE))
	->setPageTitle(I18N::translate('Pending changes'))
	->pageHeader()
	->addInlineJavascript("
		function show_diff(diffurl) {
			window.opener.location = diffurl;
			return false;
		}
	");

$action    = Filter::get('action');
$change_id = Filter::getInteger('change_id');
$index     = Filter::get('index');
$ged       = Filter::getInteger('ged');

echo '<div id="pending"><h2>', I18N::translate('Pending changes'), '</h2>';

switch ($action) {
case 'undo':
	$gedcom_id = Database::prepare("SELECT gedcom_id FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	$xref      = Database::prepare("SELECT xref      FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	// Undo a change, and subsequent changes to the same record
	Database::prepare(
		"UPDATE `##change`" .
		" SET   status     = 'rejected'" .
		" WHERE status     = 'pending'" .
		" AND   gedcom_id  = ?" .
		" AND   xref       = ?" .
		" AND   change_id >= ?"
	)->execute(array($gedcom_id, $xref, $change_id));
	break;
case 'accept':
	$gedcom_id = Database::prepare("SELECT gedcom_id FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	$xref      = Database::prepare("SELECT xref      FROM `##change` WHERE change_id=?")->execute(array($change_id))->fetchOne();
	// Accept a change, and all previous changes to the same record
	$changes = Database::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, old_gedcom, new_gedcom" .
		" FROM  `##change` c" .
		" JOIN  `##gedcom` g USING (gedcom_id)" .
		" WHERE c.status   = 'pending'" .
		" AND   gedcom_id  = ?" .
		" AND   xref       = ?" .
		" AND   change_id <= ?" .
		" ORDER BY change_id"
	)->execute(array($gedcom_id, $xref, $change_id))->fetchAll();
	foreach ($changes as $change) {
		if (empty($change->new_gedcom)) {
			// delete
			FunctionsImport::updateRecord($change->old_gedcom, $gedcom_id, true);
		} else {
			// add/update
			FunctionsImport::updateRecord($change->new_gedcom, $gedcom_id, false);
		}
		Database::prepare("UPDATE `##change` SET status='accepted' WHERE change_id=?")->execute(array($change->change_id));
		Log::addEditLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database");
	}
	break;
case 'undoall':
	Database::prepare(
		"UPDATE `##change`" .
		" SET status='rejected'" .
		" WHERE status='pending' AND gedcom_id=?"
	)->execute(array($WT_TREE->getTreeId()));
	break;
case 'acceptall':
	$changes = Database::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, old_gedcom, new_gedcom" .
		" FROM `##change` c" .
		" JOIN `##gedcom` g USING (gedcom_id)" .
		" WHERE c.status='pending' AND gedcom_id=?" .
		" ORDER BY change_id"
	)->execute(array($WT_TREE->getTreeId()))->fetchAll();
	foreach ($changes as $change) {
		if (empty($change->new_gedcom)) {
			// delete
			FunctionsImport::updateRecord($change->old_gedcom, $change->gedcom_id, true);
		} else {
			// add/update
			FunctionsImport::updateRecord($change->new_gedcom, $change->gedcom_id, false);
		}
		Database::prepare("UPDATE `##change` SET status='accepted' WHERE change_id=?")->execute(array($change->change_id));
		Log::addEditLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database");
	}
	break;
}

$changed_gedcoms = Database::prepare(
	"SELECT g.gedcom_name" .
	" FROM `##change` c" .
	" JOIN `##gedcom` g USING (gedcom_id)" .
	" WHERE c.status='pending'" .
	" GROUP BY g.gedcom_name"
)->fetchOneColumn();

if ($changed_gedcoms) {
	$changes = Database::prepare(
		"SELECT c.*, u.user_name, u.real_name, g.gedcom_name, new_gedcom, old_gedcom" .
		" FROM `##change` c" .
		" JOIN `##user`   u USING (user_id)" .
		" JOIN `##gedcom` g USING (gedcom_id)" .
		" WHERE c.status='pending'" .
		" ORDER BY gedcom_id, c.xref, c.change_id"
	)->fetchAll();

	$output         = '<br><br><table class="list_table">';
	$prev_xref      = null;
	$prev_gedcom_id = null;
	foreach ($changes as $change) {
		$tree = Tree::findById($change->gedcom_id);
		preg_match('/^0 @' . WT_REGEX_XREF . '@ (' . WT_REGEX_TAG . ')/', $change->old_gedcom . $change->new_gedcom, $match);
		switch ($match[1]) {
		case 'INDI':
			$record = new Individual($change->xref, $change->old_gedcom, $change->new_gedcom, $tree);
			break;
		case 'FAM':
			$record = new Family($change->xref, $change->old_gedcom, $change->new_gedcom, $tree);
			break;
		case 'SOUR':
			$record = new Source($change->xref, $change->old_gedcom, $change->new_gedcom, $tree);
			break;
		case 'REPO':
			$record = new Repository($change->xref, $change->old_gedcom, $change->new_gedcom, $tree);
			break;
		case 'OBJE':
			$record = new Media($change->xref, $change->old_gedcom, $change->new_gedcom, $tree);
			break;
		case 'NOTE':
			$record = new Note($change->xref, $change->old_gedcom, $change->new_gedcom, $tree);
			break;
		default:
			$record = new GedcomRecord($change->xref, $change->old_gedcom, $change->new_gedcom, $tree);
			break;
		}
		if ($change->xref != $prev_xref || $change->gedcom_id != $prev_gedcom_id) {
			if ($prev_xref) {
				$output .= '</table></td></tr>';
			}
			$prev_xref      = $change->xref;
			$prev_gedcom_id = $change->gedcom_id;
			$output .= '<tr><td class="list_value">';
			$output .= '<b><a href="#" onclick="return show_diff(\'' . $record->getHtmlUrl() . '\');"> ' . $record->getFullName() . '</a></b>';
			$output .= '<div class="indent">';
			$output .= '<table class="list_table"><tr>';
			$output .= '<td class="list_label">' . I18N::translate('Accept') . '</td>';
			$output .= '<td class="list_label">' . I18N::translate('Changes') . '</td>';
			$output .= '<td class="list_label">' . I18N::translate('User') . '</td>';
			$output .= '<td class="list_label">' . I18N::translate('Date') . '</td>';
			$output .= '<td class="list_label">' . I18N::translate('Family tree') . '</td>';
			$output .= '<td class="list_label">' . I18N::translate('Undo') . '</td>';
			$output .= '</tr>';
		}
		$output .= '<td class="list_value"><a href="edit_changes.php?action=accept&amp;change_id=' . $change->change_id . '">' . I18N::translate('Accept') . '</a></td>';
		$output .= '<td class="list_value">';
		foreach ($record->getFacts() as $fact) {
			if ($fact->getTag() != 'CHAN') {
				if ($fact->isPendingAddition()) {
					$output .= '<div class="new" title="' . strip_tags($fact->summary()) . '">' . $fact->getLabel() . '</div>';
				} elseif ($fact->isPendingDeletion()) {
					$output .= '<div class="old" title="' . strip_tags($fact->summary()) . '">' . $fact->getLabel() . '</div>';
				}
			}
		}
		echo '</td>';
		$output .= "<td class=\"list_value\"><a href=\"#\" onclick=\"return reply('" . $change->user_name . "', '" . I18N::translate('Moderate pending changes') . "')\" alt=\"" . I18N::translate('Send a message') . "\">";
		$output .= Filter::escapeHtml($change->real_name);
		$output .= ' - ' . Filter::escapeHtml($change->user_name) . '</a></td>';
		$output .= '<td class="list_value">' . $change->change_time . '</td>';
		$output .= '<td class="list_value">' . $change->gedcom_name . '</td>';
		$output .= '<td class="list_value"><a href="edit_changes.php?action=undo&amp;change_id=' . $change->change_id . '">' . I18N::translate('Undo') . '</a></td>';
		$output .= '</tr>';
	}
	$output .= '</table></td></tr></td></tr></table>';

	//-- Now for the global Action bar:
	$output2 = '<br><table class="list_table">';
	// Row 1 column 1: title "Accept all"
	$output2 .= '<tr><td class="list_label">' . I18N::translate('Approve all changes') . '</td>';
	// Row 1 column 2: title "Undo all"
	$output2 .= '<td class="list_label">' . I18N::translate('Undo all changes') . '</td></tr>';

	// Row 2 column 1: action "Accept all"
	$output2 .= '<tr><td class="list_value">';
	$count = 0;
	foreach ($changed_gedcoms as $gedcom_name) {
		if ($count != 0) {
			$output2 .= '<br>';
		}
		$output2 .= '<a href="edit_changes.php?action=acceptall&amp;ged=' . rawurlencode($gedcom_name) . '">' . $gedcom_name . ' - ' . I18N::translate('Approve all changes') . '</a>';
		$count++;
	}
	$output2 .= '</td>';
	// Row 2 column 2: action "Undo all"
	$output2 .= '<td class="list_value">';
	$count = 0;
	foreach ($changed_gedcoms as $gedcom_name) {
		if ($count != 0) {
			$output2 .= '<br>';
		}
		$output2 .= '<a href="edit_changes.php?action=undoall&amp;ged=' . rawurlencode($gedcom_name) . "\" onclick=\"return confirm('" . I18N::translate('Are you sure you want to undo all the changes to this family tree?') . "');\">$gedcom_name - " . I18N::translate('Undo all changes') . '</a>';
		$count++;
	}
	$output2 .= '</td></tr></table>';

	echo
		$output2, $output, $output2,
		'<br><br><br><br>',
		'<p id="save-cancel">',
		'<input type="button" class="cancel" value="', I18N::translate('close'), '" onclick="closePopupAndReloadParent();">',
		'</p>';
} else {
	// No pending changes - refresh the parent window and close this one
	$controller->addInlineJavascript('closePopupAndReloadParent();');
}

echo '</div>';
