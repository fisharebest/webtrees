<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h1><?= I18N::translate('Control panel') ?></h1>

<!-- SERVER WARNINGS -->
<?php if (!empty($server_warnings)): ?>
	<div class="card">
		<div class="card-header" role="tab" id="card-server-header">
			<h2 class="mb-0">
				<?= I18N::translate('Server information') ?>
			</h2>
		</div>

		<div class="card-body">
			<?php foreach ($server_warnings as $server_warning): ?>
				<div class="alert alert-warning"><?= $server_warning ?></div>
			<?php endforeach ?>
		</div>
	</div>
<?php endif ?>

<!-- WEBTREES VERSION -->
<div class="card mb-4 <?= Auth::isAdmin() && $update_available ? 'card-outline-danger' : '' ?>">
	<div class="card-header" role="tab" id="card-serever-version">
		<h2 class="mb-0">
			<?= WT_WEBTREES ?> <?= WT_VERSION ?>
		</h2>
	</div>
	<div class="card-body">
		<p>
			<?= /* I18N: %s is a URL/link to the project website */
			I18N::translate('Support and documentation can be found at %s.', '<a href="https://webtrees.net/">webtrees.net</a>') ?>
		</p>
		<?php if (Auth::isAdmin()): ?>
			<p>
				<?php if ($latest_version === ''): ?>
					<?= I18N::translate('No upgrade information is available.') ?>
				<?php elseif ($update_available): ?>
					<?= I18N::translate('A new version of webtrees is available.') ?>
					<a href="admin_site_upgrade.php" class="error">
						<?= /* I18N: %s is a version number */
						I18N::translate('Upgrade to webtrees %s.', Html::escape($latest_version)) ?>
					</a>
				<?php else: ?>
					<?= I18N::translate('This is the latest version of webtrees. No upgrade is available.') ?>
				<?php endif ?>
			</p>
		<?php endif ?>
	</div>
</div>

<!-- FAMILY TREES -->
<div class="card mb-4 <?= array_sum($changes) ? 'card-outline-danger' : '' ?>">
	<div class="card-header" role="tab" id="card-trees-header">
		<h2 class="mb-0">
			<?= I18N::translate('Family trees') ?>
			<a href="admin_trees_manage.php" class="badge badge-primary">
				<?= \Fisharebest\Webtrees\FontAwesome::decorativeIcon('preferences') ?>
			</a>
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
						-
						<?= I18N::plural('%s family tree', '%s family trees', count($all_trees), I18N::number(count($all_trees))) ?>
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
	</div>
</div>

<!-- USERS -->
<?php if (Auth::isAdmin()): ?>
	<div class="card mb-4 <?= $unapproved || $unverified ? 'card-outline-danger' : '' ?>">
		<div class="card-header" role="tab" id="card-users-header">
			<h2 class="mb-0">
				<?= I18N::translate('Users') ?>
			</h2>
		</div>
		<div class="card-body">
			<table class="table table-responsive table-sm">
				<caption class="sr-only">
					<?= I18N::translate('Users') ?>
				</caption>
				<tbody>
					<tr>
						<th class="col-xs-3">
							<?= I18N::translate('Total number of users') ?>
						</th>
						<td class="col-xs-9">
							<a href="admin_users.php">
								<?= I18N::number($total_users) ?>
							</a>
						</td>
					</tr>
					<tr>
						<th>
							<?= I18N::translate('Administrators') ?>
						</th>
						<td>
							<?php foreach ($administrators as $n => $user): ?>
								<?= $n ? I18N::$list_separator : '' ?>
								<a href="admin_users.php?action=edit&user_id=<?= $user->user_id ?>" dir="auto">
									<?= Html::escape($user->real_name) ?>
								</a>
							<?php endforeach ?>
						</td>
					</tr>
					<tr>
						<th>
							<?= I18N::translate('Managers') ?>
						</th>
						<td>
							<?php foreach ($managers as $n => $user): ?>
								<?= $n ? I18N::$list_separator : '' ?>
								<a href="admin_users.php?action=edit&user_id=<?= $user->user_id ?>" dir="auto">
									<?= Html::escape($user->real_name) ?>
								</a>
							<?php endforeach ?>
						</td>
					</tr>
					<tr>
						<th>
							<?= I18N::translate('Moderators') ?>
						</th>
						<td>
							<?php foreach ($moderators as $n => $user): ?>
								<?= $n ? I18N::$list_separator : '' ?>
								<a href="admin_users.php?action=edit&user_id=<?= $user->user_id ?>" dir="auto">
									<?= Html::escape($user->real_name) ?>
								</a>
							<?php endforeach ?>
						</td>
					</tr>
					<tr class="<?= $unverified ? 'danger' : '' ?>">
						<th>
							<?= I18N::translate('Not verified by the user') ?>
						</th>
						<td>
							<?php foreach ($unverified as $n => $user): ?>
								<?= $n ? I18N::$list_separator : '' ?>
								<a href="admin_users.php?action=edit&user_id=<?= $user->user_id ?>" dir="auto">
									<?= Html::escape($user->real_name) ?>
								</a>
							<?php endforeach ?>
						</td>
					</tr>
					<tr class="<?= $unapproved ? 'danger' : '' ?>">
						<th>
							<?= I18N::translate('Not approved by an administrator') ?>
						</th>
						<td>
							<?php foreach ($unapproved as $n => $user): ?>
								<?= $n ? I18N::$list_separator : '' ?>
								<a href="admin_users.php?action=edit&user_id=<?= $user->user_id ?>" dir="auto">
									<?= Html::escape($user->real_name) ?>
								</a>
							<?php endforeach ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php endif ?>

<!-- MODULES -->
<div class="card mb-4">
	<div class="card-header" role="tab" id="card-old-files-header">
		<h2 class="mb-0">
			<?= I18N::translate('Modules') ?>
		</h2>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-sm-6">
				<a href="admin_modules.php">
					<?= I18N::translate('Module administration') ?>
				</a>
				<ul>
					<li>
						<a href="admin_module_menus.php">
							<?= I18N::translate('Menus') ?>
						</a>
					</li>
					<li>
						<a href="admin_module_tabs.php">
							<?= I18N::translate('Tabs') ?>
						</a>
					</li>
					<li>
						<a href="admin_module_blocks.php">
							<?= I18N::translate('Blocks') ?>
						</a>
					</li>
					<li>
						<a href="admin_module_sidebar.php">
							<?= I18N::translate('Sidebars') ?>
						</a>
					</li>
					<li>
						<a href="admin_module_charts.php">
							<?= I18N::translate('Charts') ?>
						</a>
					</li>
					<li>
						<a href="admin_module_repots.php">
							<?= I18N::translate('Reports') ?>
						</a>
					</li>
				</ul>
			</div>
			<div class="col-sm-6">
				<?= I18N::translate('Preferences') ?>
				<ul class="fa-ul">
					<?php foreach ($config_modules as $module): ?>
						<li>
							<i class="fa-li fa fa-cogs"></i>
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
<?php if (Auth::isAdmin() && !empty($files_to_delete)): ?>
	<div class="card mb-4 card-outline-danger">
		<div class="card-header" role="tab" id="card-old-files-header">
			<h2 class="mb-0">
				<?= I18N::translate('Old files found') ?>
			</h2>
		</div>
		<div class="card-body">
			<p>
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
