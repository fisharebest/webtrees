<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintLists; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?php if ($media->isPendingDeletion()): ?>
	<?php if (Auth::isModerator($media->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This media object has been deleted. You should review the deletion and then %1$s or %2$s it.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($media->getXref()) . '\', \'' . e($media->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . e($media->getXref()) . '\', \'' . e($media->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($media->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This media object has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php elseif ($media->isPendingAddition()): ?>
	<?php if (Auth::isModerator($media->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This media object has been edited. You should review the changes and then %1$s or %2$s them.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($media->getXref()) . '\', \'' . e($media->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . e($media->getXref()) . '\', \'' . e($media->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>' ) . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($media->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This media object has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php endif ?>

<div class="d-flex mb-4">
	<h2 class="wt-page-title mx-auto">
		<?= $media->getFullName() ?>
	</h2>
	<?php if ($media->canEdit() && !$media->isPendingDeletion()): ?>
		<?= view('media-page-menu', ['record' => $media]) ?>
	<?php endif ?>
</div>

<div class="wt-page-content">
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" role="tab" href="#details">
				<?= I18N::translate('Details') ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($individuals) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#individuals">
				<?= I18N::translate('Individuals') ?>
				<?= Bootstrap4::badgeCount($individuals) ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($families) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#families">
				<?= I18N::translate('Families') ?>
				<?= Bootstrap4::badgeCount($families) ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($sources) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#sources">
				<?= I18N::translate('Sources') ?>
				<?= Bootstrap4::badgeCount($sources) ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($notes) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#notes">
				<?= I18N::translate('Notes') ?>
				<?= Bootstrap4::badgeCount($notes) ?>
			</a>
		</li>
	</ul>

	<div class="tab-content mt-4">
		<div class="tab-pane active fade show" role="tabpanel" id="details">
			<table class="table wt-facts-table">
				<?php foreach ($media->mediaFiles() as $media_file): ?>
				<tr class="<?= $media_file->isPendingAddition() ? 'new' : '' ?><?= $media_file->isPendingDeletion() ? 'old' : '' ?>">
					<th scope="row">
						<?= I18N::translate('Media file') ?>
						<?php if ($media->canEdit()): ?>
							<div class="editfacts">
								<?= FontAwesome::linkIcon('edit', I18N::translate('Edit'), ['class' => 'btn btn-link', 'href' => '#', 'data-toggle' => 'modal', 'data-target' => '#wt-ajax-modal', 'data-href' => route('edit-media-file', ['ged' => $media->getTree()->getName(), 'xref' => $media->getXref(), 'fact_id' => $media_file->factId()])]) ?>
								<?php if (count($media->mediaFiles()) > 1): ?>
									<?= FontAwesome::linkIcon('delete', I18N::translate('Delete'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return delete_fact("' . I18N::translate('Are you sure you want to delete this fact?') . '", "' . e($media->getTree()->getName()) . '", , "' . e($media->getXref()) . '", "' . $media_file->factId() . '");']) ?>
								<?php endif ?>
							</div>
						<?php endif ?>
					</th>
					<td class="d-flex justify-content-between">
						<div>
							<?php if ($media_file->isExternal()): ?>
								<?= GedcomTag::getLabelValue('URL', $media_file->filename()) ?>
							<?php elseif (Auth::isEditor($media->getTree())): ?>
								<?= GedcomTag::getLabelValue('FILE', $media_file->filename()) ?>
								<?php if ($media_file->fileExists()): ?>
									<?php if ($media->getTree()->getPreference('SHOW_MEDIA_DOWNLOAD') >= Auth::accessLevel($media->getTree())): ?>
									— <a href="<?= $media_file->imageUrl(0, 0, '') ?>">
											<?= I18N::translate('Download file') ?>
										</a>
									<?php endif ?>
								<?php elseif (!$media_file->isExternal()): ?>
									<p class="alert alert-danger">
										<?= I18N::translate('The file “%s” does not exist.', $media_file->filename()) ?>
									</p>
								<?php endif ?>
							<?php endif ?>
							<?= GedcomTag::getLabelValue('TITL', $media_file->title()) ?>
							<?= GedcomTag::getLabelValue('TYPE', $media_file->type()) ?>
							<?= GedcomTag::getLabelValue('FORM', $media_file->format()) ?>
						</div>
						<div>
							<?php if (!$media_file->isExternal()): ?>
								<?= $media_file->displayImage(200, 150, 'contain', []) ?>
							<?php endif ?>
						</div>
					</td>
				</tr>
				<?php endforeach ?>
				<?php foreach ($facts as $fact): ?>
					<?php FunctionsPrintFacts::printFact($fact, $media) ?>
				<?php endforeach ?>
				<?php if ($media->canEdit()): ?>
					<?php FunctionsPrint::printAddNewFact($media->getXref(), $facts, 'OBJE') ?>
					<tr>
						<th>
							<?= I18N::translate('Source') ?>
						</th>
						<td>
							<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $media->getTree()->getName(), 'xref' => $media->getXref(), 'fact' => 'SOUR'])) ?>">
								<?= I18N::translate('Add a source citation') ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?= I18N::translate('Shared note') ?>
						</th>
						<td>
							<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $media->getTree()->getName(), 'xref' => $media->getXref(), 'fact' => 'SHARED_NOTE'])) ?>">
								<?= I18N::translate('Add a shared note') ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?= I18N::translate('Restriction') ?>
						</th>
						<td>
							<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $media->getTree()->getName(), 'xref' => $media->getXref(), 'fact' => 'RESN'])) ?>">
								<?= I18N::translate('Add a restriction') ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?= I18N::translate('Media file') ?>
						</th>
						<td>
							<a href="#" data-href="<?= e(route('add-media-file', ['ged' => $media->getTree()->getName(), 'xref' => $media->getXref()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
								<?= I18N::translate('Add a media file') ?>
							</a>
						</td>
					</tr>
				<?php endif ?>
			</table>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="individuals">
			<?= FunctionsPrintLists::individualTable($individuals) ?>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="families">
			<?= FunctionsPrintLists::familyTable($families) ?>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="sources">
			<?= FunctionsPrintLists::sourceTable($sources) ?>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="notes">
			<?= FunctionsPrintLists::noteTable($notes) ?>
		</div>
	</div>
</div>

<?= view('modals/ajax') ?>
