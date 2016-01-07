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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'admin_site_merge.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Merge records') . ' — ' . $WT_TREE->getTitleHtml())
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

$gid1  = Filter::post('gid1', WT_REGEX_XREF, Filter::get('gid1', WT_REGEX_XREF));
$gid2  = Filter::post('gid2', WT_REGEX_XREF, Filter::get('gid2', WT_REGEX_XREF));
$keep1 = Filter::postArray('keep1');
$keep2 = Filter::postArray('keep2');
$rec1  = GedcomRecord::getInstance($gid1, $WT_TREE);
$rec2  = GedcomRecord::getInstance($gid2, $WT_TREE);

if ($gid1 && !$rec1) {
	FlashMessages::addMessage(I18N::translate('%1$s does not exist.', $gid1), 'danger');
}

if ($gid2 && !$rec2) {
	FlashMessages::addMessage(I18N::translate('%1$s does not exist.', $gid2), 'danger');
}

if ($rec1 && $rec2 && $rec1->getXref() === $rec2->getXref()) {
	FlashMessages::addMessage(I18N::translate('You entered the same IDs.  You cannot merge the same records.'), 'danger');
}

if ($rec1 && $rec2 && $rec1::RECORD_TYPE !== $rec2::RECORD_TYPE) {
	FlashMessages::addMessage(I18N::translate('Records are not the same type.  Cannot merge records that are not the same type.'), 'danger');
}

// Facts found both records
$facts = array();
// Facts found in only one record
$facts1 = array();
$facts2 = array();

if ($rec1) {
	foreach ($rec1->getFacts() as $fact) {
		if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
			$facts1[$fact->getFactId()] = $fact;
		}
	}
}

if ($rec2) {
	foreach ($rec2->getFacts() as $fact) {
		if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
			$facts2[$fact->getFactId()] = $fact;
		}
	}
}

foreach ($facts1 as $id1 => $fact1) {
	foreach ($facts2 as $id2 => $fact2) {
		if ($fact1->getFactId() === $fact2->getFactId()) {
			$facts[] = $fact1;
			unset($facts1[$id1]);
			unset($facts2[$id2]);
		}
	}
}

if ($rec1 && $rec2 && $rec1->getXref() !== $rec2->getXref() && $rec1::RECORD_TYPE === $rec2::RECORD_TYPE && Filter::post('action') === 'merge' && Filter::checkCsrf()) {
	$ids = FunctionsDb::fetchAllLinks($gid2, $WT_TREE->getTreeId());

	// If we are not auto-accepting, then we can show a link to the pending deletion
	if (Auth::user()->getPreference('auto_accept')) {
		$record2_name = $rec2->getFullName();
	} else {
		$record2_name = '<a class="alert-link" href="' . $rec2->getHtmlUrl() . '">' . $rec2->getFullName() . '</a>';
	}

	foreach ($ids as $id) {
		$record = GedcomRecord::getInstance($id, $WT_TREE);
		if (!$record->isPendingDeletion()) {
			FlashMessages::addMessage(I18N::translate(
				/* I18N: The placeholders are the names of individuals, sources, etc. */
				'The link from “%1$s” to “%2$s” has been updated.',
					'<a class="alert-link" href="' . $record->getHtmlUrl() . '">' . $record->getFullName() . '</a>',
					$record2_name
			), 'info');
			$gedcom = str_replace("@$gid2@", "@$gid1@", $record->getGedcom());
			$gedcom = preg_replace(
				'/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))((?:\n1.*(?:\n[2-9].*)*)*\1)/',
				'$2',
				$gedcom
			);
			$record->updateRecord($gedcom, true);
		}
	}
	// Update any linked user-accounts
	Database::prepare(
		"UPDATE `##user_gedcom_setting`" .
		" SET setting_value=?" .
		" WHERE gedcom_id=? AND setting_name='gedcomid' AND setting_value=?"
	)->execute(array($gid2, $WT_TREE->getTreeId(), $gid1));

	// Merge hit counters
	$hits = Database::prepare(
		"SELECT page_name, SUM(page_count)" .
		" FROM `##hit_counter`" .
		" WHERE gedcom_id=? AND page_parameter IN (?, ?)" .
		" GROUP BY page_name"
	)->execute(array($WT_TREE->getTreeId(), $gid1, $gid2))->fetchAssoc();

	foreach ($hits as $page_name => $page_count) {
		Database::prepare(
			"UPDATE `##hit_counter` SET page_count=?" .
			" WHERE gedcom_id=? AND page_name=? AND page_parameter=?"
		)->execute(array($page_count, $WT_TREE->getTreeId(), $page_name, $gid1));
	}
	Database::prepare(
		"DELETE FROM `##hit_counter`" .
		" WHERE gedcom_id=? AND page_parameter=?"
	)->execute(array($WT_TREE->getTreeId(), $gid2));

	$gedcom = "0 @" . $rec1->getXref() . "@ " . $rec1::RECORD_TYPE;
	foreach ($facts as $fact_id => $fact) {
		$gedcom .= "\n" . $fact->getGedcom();
	}
	foreach ($facts1 as $fact_id => $fact) {
		if (in_array($fact_id, $keep1)) {
			$gedcom .= "\n" . $fact->getGedcom();
		}
	}
	foreach ($facts2 as $fact_id => $fact) {
		if (in_array($fact_id, $keep2)) {
			$gedcom .= "\n" . $fact->getGedcom();
		}
	}

	$rec1->updateRecord($gedcom, true);
	$rec2->deleteRecord();
	FunctionsDb::updateFavorites($gid2, $gid1, $WT_TREE);
	FlashMessages::addMessage(I18N::translate(
	/* I18N: Records are individuals, sources, etc. */
		'The records “%1$s” and “%2$s” have been merged.',
		'<a class="alert-link" href="' . $rec1->getHtmlUrl() . '">' . $rec1->getFullName() . '</a>',
		$record2_name
	), 'success');

	header('Location: ' . WT_BASE_URL . Filter::post('url', 'admin_trees_duplicates\.php', WT_SCRIPT_NAME));

	return;
}

$controller->pageHeader();

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>
<h1><?php echo $controller->getPageTitle(); ?></h1>

<?php if ($rec1 && $rec2 && $rec1->getXref() !== $rec2->getXref() && $rec1::RECORD_TYPE === $rec2::RECORD_TYPE): ?>

<form method="post">
	<input type="hidden" name="action" value="merge">
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
	<input type="hidden" name="url" value="<?php echo Filter::get('url', 'admin_trees_duplicates\.php'); ?>">
	<?php echo Filter::getCsrf(); ?>
	<p>
		<?php echo I18N::translate('Select the facts and events to keep from both records.'); ?>
	</p>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">
				<?php echo I18N::translate('The following facts and events were found in both records.'); ?>
			</h2>
		</div>
		<div class="panel-body">
			<?php if ($facts): ?>
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>
							<?php echo I18N::translate('Select'); ?>
						</th>
						<th>
							<?php echo I18N::translate('Details'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($facts as $fact_id => $fact): ?>
					<tr>
						<td>
							<input type="checkbox" name="keep1[]" value="<?php echo $fact->getFactId(); ?>" checked>
						</td>
						<td>
							<div class="gedcom-data" dir="ltr"><?php echo Filter::escapeHtml($fact->getGedcom()); ?></div>
							<?php if ($fact->getTarget()): ?>
							<a href="<?php echo $fact->getTarget()->getHtmlUrl(); ?>">
								<?php echo $fact->getTarget()->getFullName(); ?>
							</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php else: ?>
			<p>
				<?php echo I18N::translate('No matching facts found'); ?>
			</p>
			<?php endif; ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">
						<?php echo /* I18N: the name of an individual, source, etc. */ I18N::translate('The following facts and events were only found in the record of %s.', '<a href="' . $rec1->getHtmlUrl() . '">' . $rec1->getFullName()) . '</a>'; ?>
					</h2>
				</div>
				<div class="panel-body">
					<?php if ($facts1): ?>
						<table class="table table-bordered table-condensed">
							<thead>
							<tr>
								<th>
									<?php echo I18N::translate('Select'); ?>
								</th>
								<th>
									<?php echo I18N::translate('Details'); ?>
								</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($facts1 as $fact_id => $fact): ?>
								<tr>
									<td>
										<input type="checkbox" name="keep1[]" value="<?php echo $fact->getFactId(); ?>" checked>
									</td>
									<td>
										<div class="gedcom-data" dir="ltr"><?php echo Filter::escapeHtml($fact->getGedcom()); ?></div>
										<?php if ($fact->getTarget()): ?>
											<a href="<?php echo $fact->getTarget()->getHtmlUrl(); ?>">
												<?php echo $fact->getTarget()->getFullName(); ?>
											</a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php else: ?>
						<p>
							<?php echo I18N::translate('No matching facts found'); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">
						<?php echo /* I18N: the name of an individual, source, etc. */ I18N::translate('The following facts and events were only found in the record of %s.', '<a href="' . $rec2->getHtmlUrl() . '">' . $rec2->getFullName()) . '</a>'; ?>
					</h2>
				</div>
				<div class="panel-body">
					<?php if ($facts2): ?>
						<table class="table table-bordered table-condensed">
							<thead>
							<tr>
								<th>
									<?php echo I18N::translate('Select'); ?>
								</th>
								<th>
									<?php echo I18N::translate('Details'); ?>
								</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($facts2 as $fact_id => $fact): ?>
								<tr>
									<td>
										<input type="checkbox" name="keep2[]" value="<?php echo $fact->getFactId(); ?>" checked>
									</td>
									<td>
										<div class="gedcom-data" dir="ltr"><?php echo Filter::escapeHtml($fact->getGedcom()); ?></div>
										<?php if ($fact->getTarget()): ?>
											<a href="<?php echo $fact->getTarget()->getHtmlUrl(); ?>">
												<?php echo $fact->getTarget()->getFullName(); ?>
											</a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php else: ?>
						<p>
							<?php echo I18N::translate('No matching facts found'); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<button type="submit" class="btn btn-primary">
		<i class="fa fa-check"></i>
		<?php echo I18N::translate('save'); ?>
	</button>
</form>

<?php else: ?>

<form class="form form-horizontal">
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
	<p><?php echo /* I18N: Records are indviduals, sources, etc. */ I18N::translate('Select two records to merge.'); ?></p>

	<div class="form-group">
		<div class="control-label col-sm-3">
			<label for="gid1">
				<?php echo /* I18N: Record is an indvidual, source, etc. */ I18N::translate('First record'); ?>
			</label>
		</div>
		<div class="col-sm-9">
			<input data-autocomplete-type="IFSRO" type="text" name="gid1" id="gid1" maxlength="20" value="<?php echo $gid1; ?>">
			<?php echo FunctionsPrint::printFindIndividualLink('gid1'); ?>
			<?php echo FunctionsPrint::printFindFamilyLink('gid1'); ?>
			<?php echo FunctionsPrint::printFindSourceLink('gid1'); ?>
			<?php echo FunctionsPrint::printFindRepositoryLink('gid1'); ?>
			<?php echo FunctionsPrint::printFindMediaLink('gid1'); ?>
			<?php echo FunctionsPrint::printFindNoteLink('gid1'); ?>
		</div>
	</div>

	<div class="form-group">
		<div class="control-label col-sm-3">
			<label for="gid2">
				<?php echo /* I18N: Record is an indvidual, source, etc. */ I18N::translate('Second record'); ?>
			</label>
		</div>
		<div class="col-sm-9">
			<input data-autocomplete-type="IFSRO" type="text" name="gid2" id="gid2" maxlength="20" value="<?php echo $gid2; ?>" >
			<?php echo FunctionsPrint::printFindIndividualLink('gid2'); ?>
			<?php echo FunctionsPrint::printFindFamilyLink('gid2'); ?>
			<?php echo FunctionsPrint::printFindSourceLink('gid2'); ?>
			<?php echo FunctionsPrint::printFindRepositoryLink('gid2'); ?>
			<?php echo FunctionsPrint::printFindMediaLink('gid2'); ?>
			<?php echo FunctionsPrint::printFindNoteLink('gid2'); ?>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?php echo I18N::translate('continue'); ?>
			</button>
		</div>
	</div>

</form>

<?php endif; ?>
