<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<form action="<?= e(route('merge-records', ['ged' => $tree->getName(), 'xref1' => $record1->getXref(), 'xref2' => $record2->getXref()])) ?>" method="POST">
	<?= csrf_field() ?>
	<p>
		<?= I18N::translate('Select the facts and events to keep from both records.') ?>
	</p>
	<div class="card mb-4">
		<div class="card-header">
			<h2 class="card-title">
				<?= I18N::translate('The following facts and events were found in both records.') ?>
			</h2>
		</div>
		<div class="card-body">
			<?php if (!empty($facts)): ?>
				<table class="table table-bordered table-sm">
					<thead>
						<tr>
							<th>
								<?= I18N::translate('Select') ?>
							</th>
							<th>
								<?= I18N::translate('Details') ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($facts as $fact_id => $fact): ?>
							<tr>
								<td>
									<input type="checkbox" name="keep1[]" value="<?= $fact->getFactId() ?>" checked>
								</td>
								<td>
									<div class="gedcom-data" dir="ltr"><?= e($fact->getGedcom()) ?></div>
									<?php if ($fact->getTarget()): ?>
										<a href="<?= e($fact->getTarget()->url()) ?>">
											<?= $fact->getTarget()->getFullName() ?>
										</a>
									<?php endif ?>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			<?php else: ?>
				<p>
					<?= I18N::translate('No matching facts found') ?>
				</p>
			<?php endif ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6">
			<div class="card">
				<div class="card-header">
					<h2 class="card-title">
						<?= /* I18N: the name of an individual, source, etc. */ I18N::translate('The following facts and events were only found in the record of %s.', '<a href="' . e($record1->url()) . '">' . $record1->getFullName()) . '</a>' ?>
					</h2>
				</div>
				<div class="card-body">
					<?php if (!empty($facts1)): ?>
						<table class="table table-bordered table-sm">
							<thead>
								<tr>
									<th>
										<?= I18N::translate('Select') ?>
									</th>
									<th>
										<?= I18N::translate('Details') ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($facts1 as $fact_id => $fact): ?>
									<tr>
										<td>
											<input type="checkbox" name="keep1[]" value="<?= $fact->getFactId() ?>" checked>
										</td>
										<td>
											<div class="gedcom-data" dir="ltr"><?= e($fact->getGedcom()) ?></div>
											<?php if ($fact->getTarget()): ?>
												<a href="<?= e($fact->getTarget()->url()) ?>">
													<?= $fact->getTarget()->getFullName() ?>
												</a>
											<?php endif ?>
										</td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					<?php else: ?>
						<p>
							<?= I18N::translate('No matching facts found') ?>
						</p>
					<?php endif ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="card">
				<div class="card-header">
					<h2 class="card-title">
						<?= /* I18N: the name of an individual, source, etc. */ I18N::translate('The following facts and events were only found in the record of %s.', '<a href="' . e($record2->url()) . '">' . $record2->getFullName()) . '</a>' ?>
					</h2>
				</div>
				<div class="card-body">
					<?php if (!empty($facts2)): ?>
						<table class="table table-bordered table-sm">
							<thead>
								<tr>
									<th>
										<?= I18N::translate('Select') ?>
									</th>
									<th>
										<?= I18N::translate('Details') ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($facts2 as $fact_id => $fact): ?>
									<tr>
										<td>
											<input type="checkbox" name="keep2[]" value="<?= $fact->getFactId() ?>" checked>
										</td>
										<td>
											<div class="gedcom-data" dir="ltr"><?= e($fact->getGedcom()) ?></div>
											<?php if ($fact->getTarget()): ?>
												<a href="<?= e($fact->getTarget()->url()) ?>">
													<?= $fact->getTarget()->getFullName() ?>
												</a>
											<?php endif ?>
										</td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					<?php else: ?>
						<p>
							<?= I18N::translate('No matching facts found') ?>
						</p>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>

	<button type="submit" class="btn btn-primary">
		<i class="fas fa-check"></i>
		<?= I18N::translate('save') ?>
	</button>
</form>
