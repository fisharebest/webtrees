<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Database; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Site; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<?php if (empty($all_trees)): ?>
	<div class="alert alert-info">
		<?= I18N::translate('You need to create a family tree.') ?>
	</div>
<?php endif ?>

<div id="accordion" role="tablist" aria-multiselectable="true">
	<?php foreach ($all_trees as $managed_tree): ?>
		<?php if (Auth::isManager($managed_tree)): ?>
			<div class="card">
				<div class="card-header" role="tab" id="card-tree-header-<?= $managed_tree->getTreeId() ?>">
					<h2 class="mb-0">
						<i class="fas fa-tree fa-fw"></i>
						<a data-toggle="collapse" data-parent="#accordion" href="#card-tree-content-<?= $managed_tree->getTreeId() ?>" <?= $managed_tree == $tree || $managed_tree->getPreference('imported') === '0' ? 'aria-expanded="true"' : '' ?> aria-controls="card-tree-content-<?= $managed_tree->getTreeId() ?>">
							<?= e($managed_tree->getName()) ?> — <?= e($managed_tree->getTitle()) ?>
						</a>
					</h2>
				</div>
				<div id="card-tree-content-<?= $managed_tree->getTreeId() ?>" class="collapse<?= $managed_tree == $tree || $managed_tree->getPreference('imported') === '0' ? ' show' : '' ?>" role="tabpanel" aria-labelledby="panel-tree-header-<?= $managed_tree->getTreeId() ?>">
					<div class="card-body">
						<?php $importing = Database::prepare( "SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id = ? AND imported = '0' LIMIT 1" )->execute([$managed_tree->getTreeId()])->fetchOne() ?>
						<?php if ($importing): ?>
							<div id="import<?= $managed_tree->getTreeId() ?>" class="col-xs-12">
								<div class="progress">
									<?= I18N::translate('Calculating…') ?>
								</div>
							</div>
							<?php View::push('javascript'); ?>
							<script>
                $("#import<?= $managed_tree->getTreeId() ?>").load("<?= route('import', ['ged' => $managed_tree->getName()]) ?>", {});
							</script>
							<?php View::endpush() ?>
						<?php endif ?>
						<div class="row<?= $importing ? ' d-none' : '' ?>" id="actions<?= $managed_tree->getTreeId() ?>">
							<div class="col-sm-6 col-md-3">
								<h3>
									<a href="<?= e(route('tree-page', ['ged' => $managed_tree->getName()])) ?>">
										<?= I18N::translate('Family tree') ?>
									</a>
								</h3>
								<ul class="fa-ul">
									<!-- PREFERENCES -->
									<li>
										<span class="fa-li"><i class="fas fa-cogs"></i></span>
										<a href="<?= e(route('admin-trees-preferences', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Preferences') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- PRIVACY -->
									<li>
										<span class="fa-li"><i class="fas fa-lock"></i></span>
										<a href="<?= e(route('tree-privacy', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Privacy') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- HOME PAGE BLOCKS-->
									<li>
										<span class="fa-li"><i class="fas fa-th-large"></i></span>
										<a href="<?= e(route('tree-page-edit', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Change the “Home page” blocks') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- DELETE -->
									<li>
										<span class="fa-li"><i class="far fa-trash-alt"></i></span>
										<a href="#" data-confirm="<?= I18N::translate('Are you sure you want to delete “%s”?', e($managed_tree->getTitle())) ?>" onclick="if (confirm(this.dataset.confirm)) { document.delete_form<?= $managed_tree->getTreeId() ?>.submit(); } return false;">
											<?= I18N::translate('Delete') ?>
											<span class="sr-only">
												<?= e($managed_tree->getTitle()) ?>
											</span>
										</a>
										<form name="delete_form<?= $managed_tree->getTreeId() ?>" method="post" action="<?= route('admin-trees-delete', ['ged' => $tree->getName()]) ?>">
											<?= csrf_field() ?>
											<!-- A11Y - forms need submit buttons, but they look ugly here -->
											<button class="sr-only" data-confirm="<?= I18N::translate('Are you sure you want to delete “%s”?', e($managed_tree->getTitle())) ?>" onclick="return confirm(this.dataset.confirm)" type="submit">
												<?= I18N::translate('Delete') ?>
											</button>
										</form>
									</li>
									<!-- SET AS DEFAULT -->
									<?php if (count($all_trees) > 1): ?>
										<li>
											<span class="fa-li"><i class="far fa-star"></i></span>
											<?php if ($managed_tree->getName() === Site::getPreference('DEFAULT_GEDCOM')): ?>
												<?= I18N::translate('Default family tree') ?>
											<?php else: ?>
												<a href="#" onclick="document.defaultform<?= $managed_tree->getTreeId() ?>.submit();">
													<?= I18N::translate('Set as default') ?>
													<span class="sr-only"><?= e($managed_tree->getTitle()) ?></span>
												</a>
												<form name="defaultform<?= $managed_tree->getTreeId() ?>" method="post" action="<?= route('admin-trees-default', ['ged' => $managed_tree->getName()]) ?>">
													<?= csrf_field() ?>
													<!-- A11Y - forms need submit buttons, but they look ugly here -->
													<button class="sr-only" type="submit">
														<?= I18N::translate('Set as default') ?>
													</button>
												</form>
											<?php endif ?>
										</li>
									<?php endif ?>
								</ul>
							</div>
							<div class="col-sm-6 col-md-3">
								<h3>
									<?= /* I18N: Individuals, sources, dates, places, etc. */ I18N::translate('Genealogy data') ?>
								</h3>
								<ul class="fa-ul">
									<!-- FIND DUPLICATES -->
									<li>
										<span class="fa-li"><i class="far fa-copy"></i></span>
										<a href="<?= e(route('admin-trees-duplicates', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Find duplicates') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- MERGE -->
									<li>
										<span class="fa-li"><i class="fas fa-code-branch"></i></span>
										<a href="<?= e(route('merge-records', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Merge records') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- UPDATE PLACE NAMES -->
									<li>
										<span class="fa-li"><i class="fas fa-map-marker-alt"></i></span>
										<a href="<?= e(route('admin-trees-places', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Update place names') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- CHECK FOR ERRORS -->
									<li>
										<span class="fa-li"><i class="fas fa-check"></i></span>
										<a href="<?= e(route('admin-trees-check', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Check for errors') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- UNCONNECTED INDIVIDUALS -->
									<li>
										<span class="fa-li"><i class="fas fa-unlink"></i></span>
										<a href="<?= e(route('admin-trees-unconnected', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Find unrelated individuals') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- RENUMBER -->
									<li>
										<span class="fa-li"><i class="fas fa-sort-numeric-down"></i></span>
										<a href="<?= e(route('admin-trees-renumber', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Renumber') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- CHANGES -->
									<li>
										<span class="fa-li"><i class="fas fa-th-list"></i></span>
										<a href="<?= route('admin-changes-log', ['ged' => $managed_tree->getName()]) ?>">
											<?= I18N::translate('Changes log') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
								</ul>
							</div>
							<div class="clearfix visible-sm-block"></div>
							<div class="col-sm-6 col-md-3">
								<h3>
									<?= I18N::translate('Add unlinked records') ?>
								</h3>
								<ul class="fa-ul">
									<!-- UNLINKED INDIVIDUAL -->
									<li>
										<span class="fa-li"><i class="far fa-user"></i></span>
										<a href="edit_interface.php?action=add_unlinked_indi&amp;ged=<?= e($managed_tree->getName()) ?>">
											<?= I18N::translate('Individual') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- UNLINKED SOURCE -->
									<li>
										<span class="fa-li"><i class="fas fa-book"></i></span>
										<a href="#" data-href="<?= e(route('create-source', ['tree' => $managed_tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
											<?= I18N::translate('Source') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- UNLINKED REPOSITORY -->
									<li>
										<span class="fa-li"><i class="fas fa-university"></i></span>
										<a href="#" data-href="<?= e(route('create-repository', ['tree' => $managed_tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
											<?= I18N::translate('Repository') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- UNLINKED MEDIA OBJECT -->
									<li>
										<span class="fa-li"><i class="far fa-image"></i></span>
										<a href="#" data-href="<?= e(route('create-media-object', ['tree' => $managed_tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">

											<?= I18N::translate('Media object') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- UNLINKED NOTE -->
									<li>
										<span class="fa-li"><i class="fas fa-paragraph"></i></span>
										<a href="#" data-href="<?= e(route('create-note-object', ['tree' => $managed_tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
									<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
											<?= I18N::translate('Shared note') ?>
										</a>
									</li>
									<!-- UNLINKED SUBMITTER -->
									<li>
										<span class="fa-li"><i class="far fa-user"></i></span>
										<a href="#" data-href="<?= e(route('create-submitter', ['tree' => $managed_tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
									<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
											<?= I18N::translate('Submitter') ?>
										</a>
									</li>
								</ul>
							</div>
							<div class="col-sm-6 col-md-3">
								<h3>
									<?= I18N::translate('GEDCOM file') ?>
								</h3>
								<ul class="fa-ul">
									<!-- DOWNLOAD/Export -->
									<li>
										<span class="fa-li"><i class="fas fa-download"></i></span>
										<a href="<?= e(route('admin-trees-export', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Export') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
									<!-- UPLOAD/IMPORT -->
									<li>
										<span class="fa-li"><i class="fas fa-upload"></i></span>
										<a href="<?= e(route('admin-trees-import', ['ged' => $managed_tree->getName()])) ?>">
											<?= I18N::translate('Import') ?>
											<span class="sr-only">
										<?= e($managed_tree->getTitle()) ?>
									</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif ?>
	<?php endforeach ?>

	<?php if (Auth::isAdmin()): ?>
		<div class="card">
			<div class="card-header" id="card-tree-create-header">
				<h2 class="mb-0">
					<i class="fas fa-plus fa-fw"></i>
					<a data-toggle="collapse" data-parent="#accordion" href="#card-tree-create-content" aria-controls="card-tree-create-content">
						<?= I18N::translate('Create a family tree') ?>
					</a>
				</h2>
			</div>
			<div id="card-tree-create-content" class="collapse<?= empty($all_trees) ? ' show' : '' ?>" role="tabpanel" aria-labelledby="card-tree-create-header">
				<div class="card-body">
					<form class="form-horizontal" method="post" action="<?= e(route('admin-trees-create')) ?>">
						<?= csrf_field() ?>
						<div class="row form-group">
							<label for="tree_title" class="col-sm-2 col-form-label">
								<?= I18N::translate('Family tree title') ?>
							</label>
							<div class="col-sm-10">
								<input class="form-control" id="tree_title" maxlength="255" name="tree_title" required type="text" placeholder="<?= $default_tree_title ?>">
							</div>
						</div>
						<div class="row form-group">
							<label for="tree_name" class="col-sm-2 col-form-label">
								<?= I18N::translate('URL') ?>
							</label>
							<div class="col-sm-10">
								<div class="input-group" dir="ltr">
									<div class="input-group-prepend">
									<span class="input-group-text">
										<?= WT_BASE_URL ?>?ged=
									</span>
									</div>
									<input class="form-control" id="tree_name" maxlength="31" name="tree_name" pattern="[^&lt;&gt;&amp;&quot;#^$*?{}()\[\]/\\]*" required type="text" value="<?= $default_tree_name ?>">
								</div>
								<p class="small text-muted">
									<?= I18N::translate('Avoid spaces and punctuation. A family name might be a good choice.') ?>
								</p>
							</div>
						</div>
						<div class="row form-group">
							<div class="offset-sm-2 col-sm-10">
								<button type="submit" class="btn btn-primary">
									<i class="fas fa-check" aria-hidden="true"></i>
									<?= /* I18N: A button label. */ I18N::translate('create') ?>
								</button>
								<p class="small text-muted">
									<?= I18N::translate('After creating the family tree, you will be able to import data from a GEDCOM file.') ?>
								</p>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php endif ?>

	<!-- display link to PhpGedView-WT transfer wizard on first visit to this page, before any GEDCOM is loaded -->
	<?php if (empty($all_trees) && count($all_users) === 1): ?>
		<div class="card">
			<div class="card-header" id="card-pgv-wizard-header">
				<h2 class="mb-0">
					<i class="fas fa-magic fa-fw"></i>
					<a data-toggle="collapse" data-parent="#accordion" href="#card-pgv-wizard-content" aria-controls="card-pgv-wizard-content">
						<?= I18N::translate('PhpGedView to webtrees transfer wizard') ?>
					</a>
				</h2>
			</div>
			<div id="card-pgv-wizard-content" class="collapse show" role="tabpanel" aria-labelledby="card-pgv-wizard-header">
				<div class="card-body">
					<p>
						<?= I18N::translate('The PhpGedView to webtrees wizard is an automated process to assist administrators make the move from a PhpGedView installation to a new webtrees one. It will transfer all PhpGedView GEDCOM and other database information directly to your new webtrees database. The following requirements are necessary:') ?>
					</p>
					<ul>
						<li>
							<?= I18N::translate('webtrees’ database must be on the same server as PhpGedView’s') ?>
						</li>
						<li>
							<?= /* I18N: %s is a number */ I18N::translate('PhpGedView must be version 4.2.3, or any SVN up to #%s', I18N::digits(7101)) ?>
						</li>
						<li>
							<?= I18N::translate('All changes in PhpGedView must be accepted') ?>
						</li>
						<li>
							<?= I18N::translate('All existing PhpGedView users must have distinct email addresses') ?>
						</li>
					</ul>
					<p>
						<?= I18N::translate('<b>Important note:</b> The transfer wizard is not able to assist with moving media items. You will need to set up and move or copy your media configuration and objects separately after the transfer wizard is finished.') ?>
					</p>
					<p>
						<a href="admin_pgv_to_wt.php">
							<?= I18N::translate('PhpGedView to webtrees transfer wizard') ?>
						</a>
					</p>
				</div>
			</div>
		</div>
	<?php endif ?>

	<!-- BULK LOAD/SYNCHRONISE GEDCOM FILES -->
	<?php if (count($gedcom_files) >= $multiple_tree_threshold): ?>
		<div class="card">
			<div class="card-header" id="card-tree-create-header">
				<h2 class="mb-0">
					<i class="fas fa-sync-alt fa-fw"></i>
					<a data-toggle="collapse" data-parent="#accordion" href="#synchronize-gedcom-files">
						<?= I18N::translate('Synchronize family trees with GEDCOM files') ?>
					</a>
				</h2>
			</div>

			<div id="synchronize-gedcom-files" class="panel-collapse collapse">
				<div class="card-body">
					<p>
						<?= I18N::translate('Create, update, and delete a family tree for every GEDCOM file in the data folder.') ?>
					</p>
					<form method="post" class="form form-horizontal" action="<?= e(route('admin-trees-sync', ['ged' => $tree->getName()])) ?>">
						<?= csrf_field() ?>
						<button type="submit" class="btn btn-danger">
							<i class="fas fa-sync"></i>
							<?= /* I18N: A button label. */ I18N::translate('continue') ?>
						</button>
						<p class="small text-muted">
							<?= I18N::translate('Caution! This may take a long time. Be patient.') ?>
						</p>
					</form>
				</div>
			</div>
		</div>
	<?php endif ?>
</div>

<?= view('modals/ajax') ?>
