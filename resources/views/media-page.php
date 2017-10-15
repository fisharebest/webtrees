<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintLists; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $media->getFullName() ?>
</h2>

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
			<div class="row">
				<div class="col-sm-4">
					<?= $media->displayImage(400, 600, '', ['class' => 'img-thumbnail']) ?>
				</div>
				<div class="col-sm-8">
					<table class="table wt-facts-table">
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
									<a href="<?= Html::escape(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $media->getTree()->getName(), 'xref' => $media->getXref(), 'fact' => 'SOUR'])) ?>">
										<?= I18N::translate('Add a source citation') ?>
									</a>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?= I18N::translate('Shared note') ?>
								</th>
								<td>
									<a href="<?= Html::escape(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $media->getTree()->getName(), 'xref' => $media->getXref(), 'fact' => 'SHARED_NOTE'])) ?>">
										<?= I18N::translate('Add a shared note') ?>
									</a>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?= I18N::translate('Restriction') ?>
								</th>
								<td>
									<a href="<?= Html::escape(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $media->getTree()->getName(), 'xref' => $media->getXref(), 'fact' => 'RESN'])) ?>">
										<?= I18N::translate('Add a restriction') ?>
									</a>
								</td>
							</tr>
						<?php endif ?>
					</table>
				</div>
			</div>
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
