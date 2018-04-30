<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?php if ($individual->isPendingDeletion()): ?>
	<?php if (Auth::isModerator($individual->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This individual has been deleted. You should review the deletion and then %1$s or %2$s it.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($individual->getXref()) . '\', \'' . e($individual->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . e($individual->getXref()) . '\', \'' . e($individual->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($individual->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This individual has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php elseif ($individual->isPendingAddition()): ?>
	<?php if (Auth::isModerator($individual->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => /* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate( 'This individual has been edited. You should review the changes and then %1$s or %2$s them.', '<a href="#" class="alert-link" onclick="accept_changes(\'' . e($individual->getXref()) . '\', \'' . e($individual->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>', '<a href="#" class="alert-link" onclick="reject_changes(\'' . e($individual->getXref()) . '\', \'' . e($individual->getTree()->getName()) . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>' ) . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php elseif (Auth::isEditor($individual->getTree())): ?>
		<?= view('alerts/warning-dissmissible', ['alert' => I18N::translate('This individual has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes')]) ?>
	<?php endif ?>
<?php endif ?>

<div class="d-flex mb-4">
	<h2 class="wt-page-title mx-auto">
		<?= $individual->getFullName() ?><?= $user_link ?>, <?= $individual->getLifeSpan() ?> <?= $age ?>
	</h2>
	<?php if ($individual->canEdit() && !$individual->isPendingDeletion()): ?>
		<?= view('individual-page-menu', ['individual' => $individual, 'count_names' => $count_names, 'count_sex' => $count_sex]) ?>
	<?php endif ?>
</div>

<div class="row">
	<div class="col-sm-8">
		<div class="row mb-4">
			<!-- Individual images -->
			<div class="col-sm-3">
				<?php if (empty($individual_media)): ?>
					<i class="wt-silhouette wt-silhouette-<?= $individual->getSex() ?>"></i>
				<?php elseif (count($individual_media) === 1): ?>
					<?= $individual_media[0]->displayImage(200, 260, 'crop', ['class' => 'img-thumbnail img-fluid w-100']) ?>
				<?php else: ?>
					<div id="individual-images" class="carousel slide" data-ride="carousel" data-interval="false">
						<div class="carousel-inner">
							<?php foreach ($individual_media as $n => $media_file): ?>
								<div class="carousel-item <?= $n === 0 ? 'active' : '' ?>">
									<?= $media_file->displayImage(200, 260, 'crop', ['class' => 'img-thumbnail img-fluid w-100']) ?>
								</div>
							<?php endforeach ?>
						</div>
						<a class="carousel-control-prev" href="#individual-images" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only"><?= I18N::translate('previous') ?></span>
						</a>
						<a class="carousel-control-next" href="#individual-images" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only"><?= I18N::translate('next') ?></span>
						</a>
					</div>

				<?php endif ?>

				<?php if (Auth::isEditor($individual->getTree())): ?>
					<?php if (count($individual->getFacts('OBJE')) > 1): ?>
						<div><a href="<?= e(route('reorder-media', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
								<?= I18N::translate('Re-order media') ?>
							</a></div>
					<?php endif ?>

					<?php if ($individual->getTree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($individual->getTree())): ?>
						<div><a href="<?= e(Html::url('edit_interface.php', ['action' => 'add-media-link', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
								<?= I18N::translate('Add a media object') ?>
							</a></div>
					<?php endif ?>
				<?php endif ?>
			</div>

			<!-- Name accordion -->
			<div class="col-sm-9" id="individual-names" role="tablist">
				<?php foreach ($name_records as $name_record): ?>
					<?= $name_record ?>
				<?php endforeach ?>

				<?php foreach ($sex_records as $sex_record): ?>
					<?= $sex_record ?>
				<?php endforeach ?>
			</div>
		</div>

		<div id="individual-tabs">
			<ul class="nav nav-tabs flex-wrap">
				<?php foreach ($tabs as $tab): ?>
					<li class="nav-item">
						<a class="nav-link<?= $tab->isGrayedOut($individual) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" data-href="<?= e(route('individual-tab', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName(), 'module' => $tab->getName()])) ?>" href="#<?= $tab->getName() ?>">
							<?= $tab->getTitle() ?>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
			<div class="tab-content">
				<?php	foreach ($tabs as $tab): ?>
					<div id="<?= $tab->getName() ?>" class="tab-pane fade wt-ajax-load" role="tabpanel"><?php if (!$tab->canLoadAjax()): ?>
							<?= $tab->getTabContent($individual) ?>
						<?php endif ?></div>
				<?php endforeach ?>
			</div>
		</div>
	</div>
	<div class="col-sm-4" id="sidebar" role="tablist">
		<?php foreach ($sidebars as $sidebar): ?>
			<div class="card">
				<div class="card-header" role="tab" id="sidebar-header-<?= $sidebar->getName() ?>">
					<div class="card-title mb-0">
						<a data-toggle="collapse" data-parent="#sidebar" href="#sidebar-content-<?= $sidebar->getName() ?>" aria-expanded="<?= $sidebar->getName() === 'family_nav' ? 'true' : 'false' ?>" aria-controls="sidebar-content-<?= $sidebar->getName() ?>">
							<?= $sidebar->getTitle() ?>
						</a>
					</div>
				</div>
				<div id="sidebar-content-<?= $sidebar->getName() ?>" class="collapse<?= $sidebar->getName() === 'family_nav' ? ' show' : '' ?>" role="tabpanel" aria-labelledby="sidebar-header-<?= $sidebar->getName() ?>">
					<div class="card-body">
						<?= $sidebar->getSidebarContent($individual) ?></div>
				</div>
			</div>
		<?php endforeach ?>
	</div>
</div>

<?php View::push('javascript') ?>
<script>
  'use strict';

  // If the URL contains a fragment, then activate the corresponding tab.
  // Use a prefix on the fragment, to prevent scrolling to the element.
  var target = window.location.hash.replace("tab-", "");
  var tab = $("#individual-tabs .nav-link[href='" + target + "']");
  // If not, then activate the first tab.
  if (tab.length === 0) {
    tab = $("#individual-tabs .nav-link:first");
  }
  tab.tab("show");
</script>
<?php View::endpush() ?>

<?= view('modals/ajax') ?>
