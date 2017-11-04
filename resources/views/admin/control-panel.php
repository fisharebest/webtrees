<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h1><?= $title ?></h1>

<!-- WEBSITE / WEBTREES -->
<div class="card mb-4">
	<div class="card-header">
		<h2 class="mb-0">
			<?= I18N::translate('Website') ?>
			<span class="badge badge-secondary">
				<?= WT_VERSION ?>
			</span>
		</h2>
	</div>
	<div class="card-body">
		<?php foreach ($server_warnings as $server_warning): ?>
			<p class="alert alert-warning"><?= $server_warning ?></p>
		<?php endforeach ?>

		<p class="card-text">
			<?= /* I18N: %s is a URL/link to the project website */
			I18N::translate('Support and documentation can be found at %s.', '<a href="https://webtrees.net/">webtrees.net</a>') ?>
		</p>
		<p class="card-text ">
			<?php if ($latest_version === ''): ?>
				<?= I18N::translate('No upgrade information is available.') ?>
			<?php elseif (version_compare(WT_VERSION, $latest_version) < 0): ?>
				<?= I18N::translate('A new version of webtrees is available.') ?>
				<a href="admin_site_upgrade.php" class="error">
					<?= /* I18N: %s is a version number */
					I18N::translate('Upgrade to webtrees %s.', Html::escape($latest_version)) ?>
				</a>
			<?php else: ?>
				<?= I18N::translate('This is the latest version of webtrees. No upgrade is available.') ?>
			<?php endif ?>
		</p>

		<p class="card-text">
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_site_config.php', ['action' => 'site'])) ?>">
				<?= I18N::translate('Website preferences') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_site_config.php', ['action' => 'email'])) ?>">
				<?= I18N::translate('Sending email') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_site_config.php', ['action' => 'login'])) ?>">
				<?= I18N::translate('Sign-in and registration') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_site_config.php', ['action' => 'languages'])) ?>">
				<?= I18N::translate('Languages') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_site_config.php', ['action' => 'tracking'])) ?>">
				<?= I18N::translate('Tracking and analytics') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_site_logs.php', [])) ?>">
				<?= I18N::translate('Website logs') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-clean-data'])) ?>">
				<?= I18N::translate('Clean up data folder') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-server-information'])) ?>">
				<?= I18N::translate('Server information') ?>
			</a>
		</p>
	</div>
</div>

<!-- FAMILY TREES -->
<div class="card mb-4 <?= array_sum($changes) ? 'card-outline-danger' : '' ?>">
	<div class="card-header">
		<h2 class="mb-0">
			<?= I18N::translate('Family trees') ?>
			<span class="badge badge-secondary">
					<?= I18N::number(count($all_trees)) ?>
				</span>
		</h2>
	</div>
	<div class="card-body">
		<table class="table table-sm">
			<caption class="sr-only">
				<?= I18N::translate('Family trees') ?>
			</caption>
			<thead>
				<tr>
					<th><?= I18N::translate('Family tree') ?></th>
					<th class="text-right"><?= I18N::translate('Pending changes') ?></th>
					<th class="d-none d-sm-table-cell text-right"><?= I18N::translate('Individuals') ?></th>
					<th class="d-none d-lg-table-cell text-right"><?= I18N::translate('Families') ?></th>
					<th class="d-none d-sm-table-cell text-right"><?= I18N::translate('Sources') ?></th>
					<th class="d-none d-lg-table-cell text-right"><?= I18N::translate('Repositories') ?></th>
					<th class="d-none d-sm-table-cell text-right"><?= I18N::translate('Media') ?></th>
					<th class="d-none d-lg-table-cell text-right"><?= I18N::translate('Notes') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($all_trees as $tree): ?>
					<tr class="<?= $changes[$tree->getTreeId()] ? 'danger' : '' ?>">
						<td>
							<a href="index.php?ctype=gedcom&amp;ged=<?= $tree->getNameUrl() ?>">
								<?= Html::escape($tree->getName()) ?>
								-
								<?= Html::escape($tree->getTitle()) ?>
							</a>
						</td>
						<td class="text-right">
							<?php if ($changes[$tree->getTreeId()]): ?>
								<a href="<?= Html::escape(Html::url('edit_changes.php', [
									'ged' => $tree->getName(),
									'url' => 'admin.php',
								])) ?>">
									<?= I18N::number($changes[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Pending changes') ?> <?= Html::escape($tree->getTitle()) ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-sm-table-cell text-right">
							<?php if ($individuals[$tree->getTreeId()]): ?>
								<a href="indilist.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::number($individuals[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Individuals') ?> <?= $tree->getTitleHtml() ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-lg-table-cell text-right">
							<?php if ($families[$tree->getTreeId()]): ?>
								<a href="famlist.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::number($families[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Families') ?> <?= $tree->getTitleHtml() ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-sm-table-cell text-right">
							<?php if ($sources[$tree->getTreeId()]): ?>
								<a href="sourcelist.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::number($sources[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Sources') ?> <?= $tree->getTitleHtml() ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-lg-table-cell text-right">
							<?php if ($repositories[$tree->getTreeId()]): ?>
								<a href="repolist.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::number($repositories[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Repositories') ?> <?= $tree->getTitleHtml() ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-sm-table-cell text-right">
							<?php if ($media[$tree->getTreeId()]): ?>
								<a href="medialist.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::number($media[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Media objects') ?> <?= $tree->getTitleHtml() ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-lg-table-cell text-right">
							<?php if ($notes[$tree->getTreeId()]): ?>
								<a href="notelist.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::number($media[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Notes') ?> <?= $tree->getTitleHtml() ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td>
						<?= I18N::translate('Total') ?>
					</td>
					<td class="text-right">
						<?= I18N::number(array_sum($changes)) ?>
					</td>
					<td class="d-none d-sm-table-cell text-right">
						<?= I18N::number(array_sum($individuals)) ?>
					</td>
					<td class="d-none d-lg-table-cell text-right">
						<?= I18N::number(array_sum($families)) ?>
					</td>
					<td class="d-none d-sm-table-cell text-right">
						<?= I18N::number(array_sum($sources)) ?>
					</td>
					<td class="d-none d-lg-table-cell text-right">
						<?= I18N::number(array_sum($repositories)) ?>
					</td>
					<td class="d-none d-sm-table-cell text-right">
						<?= I18N::number(array_sum($media)) ?>
					</td>
					<td class="d-none d-lg-table-cell text-right">
						<?= I18N::number(array_sum($notes)) ?>
					</td>
				</tr>
			</tfoot>
		</table>

		<p class="card-text">
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_trees_manage.php', [])) ?>">
				<?= I18N::translate('Manage family trees') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('index_edit.php', ['gedcom_id' => '-1'])) ?>">
				<?= I18N::translate('Set the default blocks for new family trees') ?>
			</a>
			<?php if (count($all_trees) > 1): ?>
				<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_trees_merge.php', [])) ?>">
					<?= I18N::translate('Merge family trees') ?>
				</a>
			<?php endif ?>
		</p>
	</div>
</div>

<!-- USERS -->
<div class="card mb-4 <?= $unapproved || $unverified ? 'card-outline-danger' : '' ?>">
	<div class="card-header">
		<h2 class="mb-0">
			<?= I18N::translate('Users') ?>
			<span class="badge badge-secondary">
					<?= I18N::number(count($all_users)) ?>
				</span>
		</h2>
	</div>
	<div class="card-body">
		<dl class="row">
			<?php foreach ([I18N::translate('Administrators') => $administrators, I18N::translate('Managers') => $managers, I18N::translate('Moderators') => $moderators, I18N::translate('Not verified by the user') => $unverified, I18N::translate('Not approved by an administrator') => $unapproved] as $label => $list): ?>
				<?php if (!empty($list)): ?>
					<dt class="col-sm-3">
						<?= $label ?>
					</dt>
					<dd class="col-sm-9">
						<?php foreach ($list as $n => $user): ?>
							<?= $n ? I18N::$list_separator : '' ?>
							<a href="admin_users.php?action=edit&user_id=<?= $user->getUserId() ?>" dir="auto">
								<?= Html::escape($user->getRealName()) ?>
							</a>
						<?php endforeach ?>
					</dd>
				<?php endif ?>
			<?php endforeach ?>
		</dl>

		<p class="card-text">
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_users.php', [])) ?>">
				<?= I18N::translate('User administration') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_users.php', ['action' => 'edit'])) ?>">
				<?= I18N::translate('Add a user') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_users_bulk.php', [])) ?>">
				<?= I18N::translate('Send broadcast messages') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin_users.php', ['action' => 'cleanup'])) ?>">
				<?= I18N::translate('Delete inactive users') ?>
			</a>
			<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('index_edit.php', ['user_id' => '-1'])) ?>">
				<?= I18N::translate('Set the default blocks for new users') ?>
			</a>
		</p>
	</div>
</div>

<!-- MODULES -->
<div class="card mb-4">
	<div class="card-header">
		<h2 class="mb-0">
			<?= I18N::translate('Modules') ?>
			<span class="badge badge-secondary">
					<?= I18N::number(count($all_modules)) ?>
				</span>
		</h2>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-sm-6">
				<p class="card-text">
					<a class="btn btn-sm btn-outline-primary mb-2" href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-modules'])) ?>">
						<?= I18N::translate('Module administration') ?>
					</a>
				</p>
				<ul class="fa-ul">
					<li>
						<?= FontAwesome::decorativeIcon('menu', ['class' => 'fa-li']) ?>
						<a href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-menus'])) ?>">
							<?= I18N::translate('Menus') ?>
						</a>
					</li>
					<li>
						<?= FontAwesome::decorativeIcon('tab', ['class' => 'fa-li']) ?>
						<a href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-tabs'])) ?>">
							<?= I18N::translate('Tabs') ?>
						</a>
					</li>
					<li>
						<?= FontAwesome::decorativeIcon('block', ['class' => 'fa-li']) ?>
						<a href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-blocks'])) ?>">
							<?= I18N::translate('Blocks') ?>
						</a>
					</li>
					<li>
						<?= FontAwesome::decorativeIcon('sidebar', ['class' => 'fa-li']) ?>
						<a href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-sidebars'])) ?>">
							<?= I18N::translate('Sidebars') ?>
						</a>
					</li>
					<li>
						<?= FontAwesome::decorativeIcon('chart', ['class' => 'fa-li']) ?>
						<a href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-charts'])) ?>">
							<?= I18N::translate('Charts') ?>
						</a>
					</li>
					<li>
						<?= FontAwesome::decorativeIcon('report', ['class' => 'fa-li']) ?>
						<a href="<?= Html::escape(Html::url('admin.php', ['route' => 'admin-reports'])) ?>">
							<?= I18N::translate('Reports') ?>
						</a>
					</li>
				</ul>
			</div>
			<div class="col-sm-6">
				<p class="card-text">
					<?= I18N::translate('Preferences') ?>
				</p>
				<ul class="fa-ul">
					<?php foreach ($config_modules as $module): ?>
						<li>
							<?= FontAwesome::decorativeIcon('preferences', ['class' => 'fa-li']) ?>
							<a href="<?= Html::escape($module->getConfigLink()) ?>">
								<?= $module->getTitle() ?>
							</a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- OLD FILES -->
<?php if (!empty($files_to_delete)): ?>
	<div class="card mb-4 card-outline-danger">
		<div class="card-header">
			<h2 class="mb-0">
				<?= I18N::translate('Old files found') ?>
			</h2>
		</div>
		<div class="card-body">
			<p class="card-text">
				<?= I18N::translate('Files have been found from a previous version of webtrees. Old files can sometimes be a security risk. You should delete them.') ?>
			</p>
			<ul class="list-unstyled">
				<?php foreach ($files_to_delete as $file_to_delete): ?>
					<li dir="ltr"><code><?= Html::escape($file_to_delete) ?></code></li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
<?php endif ?>
