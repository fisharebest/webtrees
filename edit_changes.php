<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsImport;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isModerator($WT_TREE))
	->setPageTitle(I18N::translate('Pending changes'))
	->pageHeader();

$action    = Filter::get('action');
$change_id = Filter::getInteger('change_id');

switch ($action) {
case 'undo':
	$gedcom_id = Database::prepare("SELECT gedcom_id FROM `##change` WHERE change_id=?")->execute([$change_id])->fetchOne();
	$xref      = Database::prepare("SELECT xref      FROM `##change` WHERE change_id=?")->execute([$change_id])->fetchOne();
	// Undo a change, and subsequent changes to the same record
	Database::prepare(
		"UPDATE `##change`" .
		" SET   status     = 'rejected'" .
		" WHERE status     = 'pending'" .
		" AND   gedcom_id  = ?" .
		" AND   xref       = ?" .
		" AND   change_id >= ?"
	)->execute([$gedcom_id, $xref, $change_id]);
	break;
case 'accept':
	$gedcom_id = Database::prepare("SELECT gedcom_id FROM `##change` WHERE change_id=?")->execute([$change_id])->fetchOne();
	$xref      = Database::prepare("SELECT xref      FROM `##change` WHERE change_id=?")->execute([$change_id])->fetchOne();
	// Accept a change, and all previous changes to the same record
	$all_changes = Database::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, old_gedcom, new_gedcom" .
		" FROM  `##change` c" .
		" JOIN  `##gedcom` g USING (gedcom_id)" .
		" WHERE c.status   = 'pending'" .
		" AND   gedcom_id  = ?" .
		" AND   xref       = ?" .
		" AND   change_id <= ?" .
		" ORDER BY change_id"
	)->execute([$gedcom_id, $xref, $change_id])->fetchAll();
	foreach ($all_changes as $change) {
		if (empty($change->new_gedcom)) {
			// delete
			FunctionsImport::updateRecord($change->old_gedcom, $gedcom_id, true);
		} else {
			// add/update
			FunctionsImport::updateRecord($change->new_gedcom, $gedcom_id, false);
		}
		Database::prepare("UPDATE `##change` SET status='accepted' WHERE change_id=?")->execute([$change->change_id]);
		Log::addEditLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database");
	}
	break;
case 'undoall':
	Database::prepare(
		"UPDATE `##change`" .
		" SET status='rejected'" .
		" WHERE status='pending' AND gedcom_id=?"
	)->execute([$WT_TREE->getTreeId()]);
	break;
case 'acceptall':
	$all_changes = Database::prepare(
		"SELECT change_id, gedcom_id, gedcom_name, xref, old_gedcom, new_gedcom" .
		" FROM `##change` c" .
		" JOIN `##gedcom` g USING (gedcom_id)" .
		" WHERE c.status='pending' AND gedcom_id=?" .
		" ORDER BY change_id"
	)->execute([$WT_TREE->getTreeId()])->fetchAll();
	foreach ($all_changes as $change) {
		if (empty($change->new_gedcom)) {
			// delete
			FunctionsImport::updateRecord($change->old_gedcom, $change->gedcom_id, true);
		} else {
			// add/update
			FunctionsImport::updateRecord($change->new_gedcom, $change->gedcom_id, false);
		}
		Database::prepare("UPDATE `##change` SET status='accepted' WHERE change_id=?")->execute([$change->change_id]);
		Log::addEditLog("Accepted change {$change->change_id} for {$change->xref} / {$change->gedcom_name} into database");
	}
	break;
}

$rows = Database::prepare(
	"SELECT c.*, UNIX_TIMESTAMP(c.change_time) + :offset AS change_timestamp, u.user_name, u.real_name, g.gedcom_name, new_gedcom, old_gedcom" .
	" FROM `##change` c" .
	" JOIN `##user`   u USING (user_id)" .
	" JOIN `##gedcom` g USING (gedcom_id)" .
	" WHERE c.status='pending'" .
	" ORDER BY gedcom_id, c.xref, c.change_id"
)
	->execute(['offset' => WT_TIMESTAMP_OFFSET])
	->fetchAll();

$all_changes = [];
foreach ($rows as $row) {
	$tree = Tree::findById($row->gedcom_id);
	preg_match('/^0 (?:@' . WT_REGEX_XREF . '@ )?(' . WT_REGEX_TAG . ')/', $row->old_gedcom . $row->new_gedcom, $match);

	switch ($match[1]) {
	case 'INDI':
		$row->record = new Individual($row->xref, $row->old_gedcom, $row->new_gedcom, $tree);
		break;
	case 'FAM':
		$row->record = new Family($row->xref, $row->old_gedcom, $row->new_gedcom, $tree);
		break;
	case 'SOUR':
		$row->record = new Source($row->xref, $row->old_gedcom, $row->new_gedcom, $tree);
		break;
	case 'REPO':
		$row->record = new Repository($row->xref, $row->old_gedcom, $row->new_gedcom, $tree);
		break;
	case 'OBJE':
		$row->record = new Media($row->xref, $row->old_gedcom, $row->new_gedcom, $tree);
		break;
	case 'NOTE':
		$row->record = new Note($row->xref, $row->old_gedcom, $row->new_gedcom, $tree);
		break;
	default:
		$row->record = new GedcomRecord($row->xref, $row->old_gedcom, $row->new_gedcom, $tree);
		break;
	}
	$all_changes[$row->gedcom_id][$row->xref][] = $row;
}

?>

<h2><?= $controller->getPageTitle() ?></h2>

<?php if (empty($all_changes)): ?>
<p><?= I18N::translate('There are no pending changes.') ?></p>
<?php endif ?>

<?php foreach ($all_changes as $gedcom_name => $gedcom_changes): ?>

<h3>
	<?= Tree::findById($gedcom_name)->getTitleHtml() ?>
	—
	<a href="edit_changes.php?action=acceptall&amp;ged=<?= Filter::escapeHtml($gedcom_name) ?>">
		<?= I18N::translate('Accept all changes') ?>
	</a>
	—
	<a href="edit_changes.php?action=undoall&amp;ged=<?= Filter::escapeHtml($gedcom_name) ?>" onclick="return confirm('<?= I18N::translate('Are you sure you want to reject all the changes to this family tree?') ?>');">
		<?= I18N::translate('Reject all changes') ?>
	</a>
</h3>

<?php foreach ($gedcom_changes as $xref => $record_changes): ?>
<h4><?= $record_changes[0]->record->getFullName() ?></h4>
<table class="table">
	<thead>
		<tr>
			<th><?= I18N::translate('Accept') ?></th>
			<th><?= I18N::translate('Changes') ?></th>
			<th><?= I18N::translate('User') ?></th>
			<th><?= I18N::translate('Date') ?></th>
			<th><?= I18N::translate('Reject') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($record_changes as $record_change): ?>
		<tr>
			<td>
				<a href="edit_changes.php?action=accept&amp;change_id=<?= $record_change->change_id ?>"><?= I18N::translate('Accept') ?></a>
			</td>
			<td>
				<?php foreach ($record_change->record->getFacts() as $fact): ?>
					<?php if ($fact->getTag() !== 'CHAN' && $fact->isPendingAddition()): ?>
						<div class="new" title="<?= strip_tags($fact->summary()) ?>"><?= $fact->getLabel() ?></div>
					<?php elseif ($fact->getTag() !== 'CHAN' && $fact->isPendingDeletion()): ?>
						<div class="old" title="<?= strip_tags($fact->summary()) ?>"><?= $fact->getLabel() ?></div>
					<?php endif ?>
				<?php endforeach ?>
			</td>
			<td>
				<a href="message.php?to=<?= Filter::escapeUrl($record_change->user_name) ?>&amp;subject=<?= Filter::escapeUrl(I18N::translate('Moderate pending changes')) ?>&amp;ged=<?= $WT_TREE->getNameUrl() ?>" title="<?= I18N::translate('Send a message') ?>">
					<?= Filter::escapeHtml($record_change->real_name)?> - <?= Filter::escapeHtml($record_change->user_name) ?>
				</a>
			</td>
			<td>
				<?= FunctionsDate::formatTimestamp($record_change->change_timestamp) ?>
			</td>
			<td>
				<a href="edit_changes.php?action=undo&amp;change_id=<?= $record_change->change_id ?>"><?= I18N::translate('Reject') ?></a>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php endforeach ?>
<?php endforeach ?>

