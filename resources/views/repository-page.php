<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintLists; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $repository->getFullName() ?>
</h2>

<div class="wt-page-content">
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" role="tab" href="#details">
				<?= I18N::translate('Details') ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link<?= empty($sources) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#sources">
				<?= I18N::translate('Sources') ?>
				<?= Bootstrap4::badgeCount($sources) ?>
			</a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade show active" role="tabpanel" id="details">
			<table class="table wt-facts-table">
				<?php foreach ($facts as $fact): ?>
					<?php FunctionsPrintFacts::printFact($fact, $repository) ?>
				<?php endforeach ?>

				<?php if ($repository->canEdit()): ?>
					<?php FunctionsPrint::printAddNewFact($repository->getXref(), $facts, 'REPO') ?>
				<?php endif ?>
			</table>
		</div>

		<div class="tab-pane fade" role="tabpanel" id="sources">
			<?= FunctionsPrintLists::sourceTable($sources) ?>
		</div>
	</div>
</div>

<?= view('modals/ajax') ?>
