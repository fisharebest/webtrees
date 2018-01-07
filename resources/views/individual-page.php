<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2>
	<?= $individual->getFullName() ?><?= $user_link ?>, <?= $individual->getLifeSpan() ?> <?= $age ?>
</h2>

<div class="row">
	<div class="col-sm-8">
		<div class="row">
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
						<div><a href="<?= e(Html::url('edit_interface.php', ['action' => 'reorder-media', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
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

				<?php if ($individual->canEdit()): ?>
					<div class="card">
						<div class="card-header" role="tab" id="name-header-add">
							<div class="card-title mb-0">
								<a href="<?= e(Html::url('edit_interface.php', ['action' => 'addname', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
									<?= I18N::translate('Add a name') ?>
								</a>
								<?php if (count($individual->getFacts('NAME')) > 1): ?>
									<a href="<?= e(Html::url('edit_interface.php', ['action' => 'reorder-names', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
										<?= I18N::translate('Re-order names') ?>
									</a>
								<?php endif ?>

								<?php if (count($individual->getFacts('SEX')) === 0): ?>
									<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'fact' => 'SEX', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
										<?= I18N::translate('Edit the gender') ?>
									</a>
								<?php endif ?>
							</div>
						</div>
					</div>
				<?php endif ?>
			</div>
		</div>

		<div id="individual-tabs">
			<ul class="nav nav-tabs flex-wrap">
				<?php foreach ($tabs as $tab): ?>
					<li class="nav-item">
						<a class="nav-link<?= $tab->isGrayedOut() ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" data-href="<?= e($individual->url()), '&amp;action=ajax&amp;module=', $tab->getName() ?>" href="#<?= $tab->getName() ?>">
							<?= $tab->getTitle() ?>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
			<div class="tab-content">
				<?php	foreach ($tabs as $tab): ?>
					<div id="<?= $tab->getName() ?>" class="tab-pane fade wt-ajax-load" role="tabpanel"><?php if (!$tab->canLoadAjax()): ?>
							<?= $tab->getTabContent() ?>
						<?php endif ?></div>
				<?php endforeach ?>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<?= $sidebar_html ?>
	</div>
</div>

<?= view('modals/ajax') ?>
