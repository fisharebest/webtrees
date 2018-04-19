<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintLists; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?php if ($repository->isPendingDeletion()): ?>
	<?php if (Auth::isModerator($repository->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This repository has been deleted. You should review the deletion and then %1$s or %2$s it.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($repository->getXref()) . '\', \'' . e($repository->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . e($repository->getXref()) . '\', \'' . e($repository->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($repository->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This repository has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php elseif ($repository->isPendingAddition()): ?>
	<?php if (Auth::isModerator($repository->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This repository has been edited. You should review the changes and then %1$s or %2$s them.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($repository->getXref()) . '\', \'' . e($repository->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . $repository->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>' ) . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($repository->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This repository has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php endif ?>

<div class="d-flex mb-4">
	<h2 class="wt-page-title mx-auto">
		<?= $repository->getFullName() ?>
	</h2>
	<?php if ($repository->canEdit() && !$repository->isPendingDeletion()): ?>
		<?= view('repository-page-menu', ['record' => $repository]) ?>
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
