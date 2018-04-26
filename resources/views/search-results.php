<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-search-results">
	<ul class="nav nav-tabs wt-search-results-tabs" role="tablist">
		<?php if ($search_individuals): ?>
			<li class="nav-item">
				<a class="nav-link <?= empty($individuals) ? 'text-muted' : '' ?>" id="individuals-tab" data-toggle="tab" href="#individuals" role="tab" aria-controls="individuals">
					<?= I18N::translate('Individuals') ?>
					<span class="badge badge-secondary">
						<?= I18N::number(count($individuals)) ?>
					</span>
				</a>
			</li>
		<?php endif ?>

		<?php if ($search_families): ?>
			<li class="nav-item">
				<a class="nav-link <?= empty($families) ? 'text-muted' : '' ?>" id="families-tab" data-toggle="tab" href="#families" role="tab" aria-controls="families">
					<?= I18N::translate('Families') ?>
					<span class="badge badge-secondary">
						<?= I18N::number(count($families)) ?>
					</span>
				</a>
			</li>
		<?php endif ?>

		<?php if ($search_sources): ?>
			<li class="nav-item">
				<a class="nav-link <?= empty($sources) ? 'text-muted' : '' ?>" id="sources-tab" data-toggle="tab" href="#sources" role="tab" aria-controls="sources">
					<?= I18N::translate('Sources') ?>
					<span class="badge badge-secondary">
						<?= I18N::number(count($sources)) ?>
					</span>
				</a>
			</li>
		<?php endif ?>

		<?php if ($search_repositories): ?>
			<li class="nav-item">
				<a class="nav-link <?= empty($repositories) ? 'text-muted' : '' ?>" id="repositories-tab" data-toggle="tab" href="#repositories" role="tab" aria-controls="repositories">
					<?= I18N::translate('Repositories') ?>
					<span class="badge badge-secondary">
						<?= I18N::number(count($repositories)) ?>
					</span>
				</a>
			</li>
		<?php endif ?>

		<?php if ($search_notes): ?>
			<li class="nav-item">
				<a class="nav-link <?= empty($notes) ? 'text-muted' : '' ?>" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes">
					<?= I18N::translate('Notes') ?>
					<span class="badge badge-secondary">
						<?= I18N::number(count($notes)) ?>
					</span>
				</a>
			</li>
		<?php endif ?>
	</ul>

	<div class="tab-content wt-search-results-content">
		<?php if ($search_individuals): ?>
			<div class="tab-pane fade" id="individuals" role="tabpanel" aria-labelledby="individuals-tab">
				<?= view('tables/individuals', ['individuals' => $individuals]) ?>
			</div>
		<?php endif ?>

		<?php if ($search_families): ?>
			<div class="tab-pane fade" id="families" role="tabpanel" aria-labelledby="families-tab">
				<?= view('tables/families', ['families' => $families]) ?>
			</div>
		<?php endif ?>

		<?php if ($search_sources): ?>
			<div class="tab-pane fade" id="sources" role="tabpanel" aria-labelledby="sources-tab">
				<?= view('tables/sources', ['sources' => $sources]) ?>
			</div>
		<?php endif ?>

		<?php if ($search_repositories): ?>
			<div class="tab-pane fade" id="repositories" role="tabpanel" aria-labelledby="repositories-tab">
				<?= view('tables/repositories', ['repositories' => $repositories]) ?>
			</div>
		<?php endif ?>

		<?php if ($search_notes): ?>
			<div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
				<?= view('tables/notes', ['notes' => $notes]) ?>
			</div>
		<?php endif ?>
	</div>
</div>

<?php View::push('javascript') ?>
<script>
  $('.wt-search-results-tabs li:first-child a').tab('show');
</script>
<?php View::endpush() ?>
