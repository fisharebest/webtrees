<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsCharts; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Theme; ?>

<?php if ($record->isPendingDeletion()): ?>
	<?php if (Auth::isModerator($record->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This family has been deleted. You should review the deletion and then %1$s or %2$s it.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($record->getXref()) . '\', \'' . e($record->getTree()->getName()) .  '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . e($record->getXref()) . '\', \'' . e($record->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($record->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This family has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php elseif ($record->isPendingAddition()): ?>
	<?php if (Auth::isModerator($record->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This family has been edited. You should review the changes and then %1$s or %2$s them.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($record->getXref()) . '\', \'' . e($record->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . e($record->getXref()) . '\', \'' . e($record->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>' ) . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($record->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This family has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php endif ?>

<div class="d-flex mb-4">
	<h2 class="wt-page-title mx-auto">
		<?= $record->getFullName() ?>
	</h2>
	<?php if ($record->canEdit() && !$record->isPendingDeletion()): ?>
		<?= view('family-page-menu', ['record' => $record]) ?>
	<?php endif ?>
</div>

<div class="wt-page-content">
	<table id="family-table" class="w-100" role="presentation">
		<tr style="vertical-align:top;">
			<td style="width: <?= Theme::theme()->parameter('chart-box-x') + 30 ?>px;">
				<?php FunctionsCharts::printFamilyChildren($record) ?>
			</td>
			<td>
				<table class="w-100" role="presentation">
					<tr>
						<td class="subheaders"><?= I18N::translate('Parents') ?></td>
						<td class="subheaders"><?= I18N::translate('Grandparents') ?></td>
					</tr>
					<tr>
						<td colspan="2">
							<?php FunctionsCharts::printFamilyParents($record) ?>
							<?php if (Auth::isEditor($record->getTree())): ?>
								<?php if ($record->getHusband() === null): ?>
									<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_spouse_to_family', 'ged=' => $record->getTree()->getName(), 'xref' => $record->getXref(), 'famtag' => 'HUSB'])) ?>>
										<?= I18N::translate('Add a father') ?>
									</a>
									<br>
								<?php endif ?>
								<?php if ($record->getWife() === null): ?>
									<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_spouse_to_family', 'ged=' => $record->getTree()->getName(), 'xref' => $record->getXref(), 'famtag' => 'WIFE'])) ?>>
										<?= I18N::translate('Add a mother') ?>
									</a>
									<br>
								<?php endif ?>
							<?php endif ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<span class="subheaders"><?= I18N::translate('Family group information') ?></span>
	<table class="table wt-facts-table">
		<?php if (empty($facts)): ?>
			<tr>
				<td class="messagebox" colspan="2">
					<?= I18N::translate('No facts exist for this family.') ?>
				</td>
			</tr>
		<?php else: ?>
			<?php foreach ($facts as $fact): ?>
				<?php FunctionsPrintFacts::printFact($fact, $record) ?>
			<?php endforeach ?>
		<?php endif ?>

		<?php if (Auth::isEditor($record->getTree())): ?>
			<?php FunctionsPrint::printAddNewFact($record->getXref(), $facts, 'FAM') ?>
			<tr>
				<th scope="row">
					<?= I18N::translate('Note') ?>
				</th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $record->getTree()->getName(), 'xref' => $record->getXref(), 'fact' => 'NOTE'])) ?>">
						<?= I18N::translate('Add a note') ?>
					</a>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?= I18N::translate('Shared note') ?>
				</th>
				<td class="optionbox">
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $record->getTree()->getName(), 'xref' => $record->getXref(), 'fact' => 'SHARED_NOTE'])) ?>">
						<?= I18N::translate('Add a shared note') ?>
					</a>
				</td>
			</tr>

			<?php if ($record->getTree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($record->getTree())): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Media object') ?>
					</th>
					<td class="optionbox">
						<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add-media-link', 'ged' => $record->getTree()->getName(), 'xref' => $record->getXref()]))  ?>">
							<?= I18N::translate('Add a media object') ?>
						</a>
					</td>
				</tr>
			<?php endif ?>

			<tr>
				<th scope="row">
					<?= I18N::translate('Source') ?>
				</th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $record->getTree()->getName(), 'xref' => $record->getXref(), 'fact' => 'SOUR'])) ?>">
						<?= I18N::translate('Add a source citation') ?>
					</a>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>

<?= view('modals/ajax') ?>
