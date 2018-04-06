<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\Functions;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin() || Auth::isManager($WT_TREE))
	->setPageTitle(I18N::translate('Manage family trees'));

// Show a reduced page when there are more than a certain number of trees
$multiple_tree_threshold = (int) Site::getPreference('MULTIPLE_TREE_THRESHOLD', '500');

// Note that glob() returns false instead of an empty array when open_basedir_restriction
// is in force and no files are found. See PHP bug #47358.
if (defined('GLOB_BRACE')) {
	$gedcom_files = glob(WT_DATA_DIR . '*.{ged,Ged,GED}', GLOB_NOSORT | GLOB_BRACE) ?: [];
} else {
	$gedcom_files = array_merge(
		glob(WT_DATA_DIR . '*.ged', GLOB_NOSORT) ?: [],
		glob(WT_DATA_DIR . '*.Ged', GLOB_NOSORT) ?: [],
		glob(WT_DATA_DIR . '*.GED', GLOB_NOSORT) ?: []
	);
}
// Process POST actions
switch (Filter::post('action')) {
	case 'delete':
		$gedcom_id = Filter::postInteger('gedcom_id');
		if (Filter::checkCsrf() && $gedcom_id) {
			$tree = Tree::findById($gedcom_id);
			FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ I18N::translate('The family tree “%s” has been deleted.', e($tree->getTitle())), 'success');
			$tree->delete();
		}
		header('Location: admin_trees_manage.php');

		return;
	case 'setdefault':
		if (Filter::checkCsrf()) {
			Site::setPreference('DEFAULT_GEDCOM', Filter::post('ged'));
			FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ I18N::translate('The family tree “%s” will be shown to visitors when they first arrive at this website.', e($WT_TREE->getTitle())), 'success');
		}
		header('Location: admin_trees_manage.php');

		return;
	case 'new_tree':
		$basename   = basename(Filter::post('tree_name'));
		$tree_title = Filter::post('tree_title');

		if (Filter::checkCsrf() && $basename && $tree_title) {
			if (Tree::findByName($basename)) {
				FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ I18N::translate('The family tree “%s” already exists.', e($basename)), 'danger');
			} else {
				Tree::create($basename, $tree_title);
				FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ I18N::translate('The family tree “%s” has been created.', e($basename)), 'success');
			}
		}
		header('Location: admin_trees_manage.php?ged=' . rawurlencode($basename));

		return;
	case 'replace_upload':
		$gedcom_id          = Filter::postInteger('gedcom_id');
		$keep_media         = Filter::post('keep_media', '1', '0');
		$GEDCOM_MEDIA_PATH  = Filter::post('GEDCOM_MEDIA_PATH');
		$WORD_WRAPPED_NOTES = Filter::post('WORD_WRAPPED_NOTES', '1', '0');
		$tree               = Tree::findById($gedcom_id);

		if (Filter::checkCsrf() && $tree) {
			$tree->setPreference('keep_media', $keep_media);
			$tree->setPreference('GEDCOM_MEDIA_PATH', $GEDCOM_MEDIA_PATH);
			$tree->setPreference('WORD_WRAPPED_NOTES', $WORD_WRAPPED_NOTES);
			if (isset($_FILES['tree_name'])) {
				if ($_FILES['tree_name']['error'] == 0 && is_readable($_FILES['tree_name']['tmp_name'])) {
					$tree->importGedcomFile($_FILES['tree_name']['tmp_name'], $_FILES['tree_name']['name']);
				} else {
					FlashMessages::addMessage(Functions::fileUploadErrorText($_FILES['tree_name']['error']), 'danger');
				}
			} else {
				FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
			}
		}
		header('Location: admin_trees_manage.php');

		return;
	case 'replace_import':
		$basename           = basename(Filter::post('tree_name'));
		$gedcom_id          = Filter::postInteger('gedcom_id');
		$keep_media         = Filter::post('keep_media', '1', '0');
		$GEDCOM_MEDIA_PATH  = Filter::post('GEDCOM_MEDIA_PATH');
		$WORD_WRAPPED_NOTES = Filter::post('WORD_WRAPPED_NOTES', '1', '0');
		$tree               = Tree::findById($gedcom_id);

		if (Filter::checkCsrf() && $tree) {
			$tree->setPreference('keep_media', $keep_media);
			$tree->setPreference('GEDCOM_MEDIA_PATH', $GEDCOM_MEDIA_PATH);
			$tree->setPreference('WORD_WRAPPED_NOTES', $WORD_WRAPPED_NOTES);
			if ($basename) {
				$tree->importGedcomFile(WT_DATA_DIR . $basename, $basename);
			} else {
				FlashMessages::addMessage(I18N::translate('No GEDCOM file was received.'), 'danger');
			}
		}
		header('Location: admin_trees_manage.php');

		return;

	case 'synchronize':
		if (Filter::checkCsrf()) {
			$basenames = [];

			foreach ($gedcom_files as $gedcom_file) {
				$filemtime   = filemtime($gedcom_file); // Only import files that have changed
				$basename    = basename($gedcom_file);
				$basenames[] = $basename;

				$tree = Tree::findByName($basename);
				if (!$tree) {
					$tree = Tree::create($basename, $basename);
				}
				if ($tree->getPreference('filemtime') != $filemtime) {
					$tree->importGedcomFile($gedcom_file, $basename);
					$tree->setPreference('filemtime', $filemtime);
					FlashMessages::addMessage(I18N::translate('The GEDCOM file “%s” has been imported.', e($basename)), 'success');
				}
			}

			foreach (Tree::getAll() as $tree) {
				if (!in_array($tree->getName(), $basenames)) {
					FlashMessages::addMessage(I18N::translate('The family tree “%s” has been deleted.', e($tree->getTitle())), 'success');
					$tree->delete();
				}
			}
		}
		header('Location: admin_trees_manage.php');

		return;
}

$default_tree_title  = /* I18N: Default name for a new tree */ I18N::translate('My family tree');
$default_tree_name   = 'tree';
$default_tree_number = 1;
$existing_trees      = Tree::getNameList();
while (array_key_exists($default_tree_name . $default_tree_number, $existing_trees)) {
	$default_tree_number++;
}
$default_tree_name .= $default_tree_number;

// Process GET actions
switch (Filter::get('action')) {
	case 'importform':
		$controller
			->setPageTitle(I18N::translate('Import a GEDCOM file') . ' — ' . e($WT_TREE->getTitle()))
			->pageHeader();

		echo Bootstrap4::breadcrumbs([
			route('admin-control-panel')              => I18N::translate('Control panel'),
			'admin_trees_manage.php' => I18N::translate('Manage family trees'),
		], $controller->getPageTitle());
		?>

	<h1><?= $controller->getPageTitle() ?></h1>
	<?php

		$tree = Tree::findById(Filter::getInteger('gedcom_id'));
		// Check it exists
		if (!$tree) {
			break;
		}
		$gedcom_filename = $tree->getPreference('gedcom_filename')
		?>
		<p>
		<?= /* I18N: %s is the name of a family tree */ I18N::translate('This will delete all the genealogy data from “%s” and replace it with data from a GEDCOM file.', e($tree->getTitle())) ?>
	</p>
	<form class="form form-horizontal" name="gedcomimportform" method="post" enctype="multipart/form-data" onsubmit="return checkGedcomImportForm('<?= e(I18N::translate('You have selected a GEDCOM file with a different name. Is this correct?')) ?>');">
		<input type="hidden" name="gedcom_id" value="<?= $tree->getTreeId() ?>">
		<input type="hidden" id="gedcom_filename" value="<?= e($gedcom_filename) ?>">
		<?= Filter::getCsrf() ?>

		<fieldset class="form-group">
			<div class="row">
				<legend class="col-form-label col-sm-3">
					<?= /* I18N: A configuration setting */ I18N::translate('Select a GEDCOM file to import') ?>
				</legend>
				<div class="col-sm-9">
					<div class="row">
						<label class="col-sm-3">
							<input type="radio" name="action" id="import-computer" value="replace_upload" checked>
							<?= I18N::translate('A file on your computer') ?>
						</label>
						<div class="col-sm-9">
							<div class="btn btn-default">
								<input type="file" name="tree_name" id="import-computer-file">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3">
							<input type="radio" name="action" id="import-server" value="replace_import">
							<?= I18N::translate('A file on the server') ?>
						</label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-prepend">
									<?= WT_DATA_DIR ?>
								</span>
								<?php
									$d     = opendir(WT_DATA_DIR);
									$files = [];
									while (($f = readdir($d)) !== false) {
										if (!is_dir(WT_DATA_DIR . $f) && is_readable(WT_DATA_DIR . $f)) {
											$fp     = fopen(WT_DATA_DIR . $f, 'rb');
											$header = fread($fp, 64);
											fclose($fp);
											if (preg_match('/^(' . WT_UTF8_BOM . ')?0 *HEAD/', $header)) {
												$files[] = $f;
											}
										}
									}
									echo '<select name="tree_name" class="form-control" id="import-server-file">';
									echo '<option value=""></option>';
									sort($files);
									foreach ($files as $gedcom_file) {
										echo '<option value="', e($gedcom_file), '" ';
										if ($gedcom_file === $gedcom_filename) {
											echo ' selected';
										}
										echo'>', e($gedcom_file), '</option>';
									}
									if (empty($files)) {
										echo '<option disabled selected>', I18N::translate('No GEDCOM files found.'), '</option>';
									}
									echo '</select>';
									?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</fieldset>

		<hr>

		<fieldset class="form-group">
			<div class="row">
				<legend class="col-form-label col-sm-3">
					<?= I18N::translate('Import preferences') ?>
				</legend>
				<div class="col-sm-9">
					<label>
						<input type="checkbox" name="keep_media" value="1" <?= $tree->getPreference('keep_media') ? 'checked' : '' ?>>
						<?= /* I18N: A configuration setting */ I18N::translate('Keep media objects') ?>
					</label>
					<p class="small text-muted">
						<?= I18N::translate('If you have created media objects in webtrees, and have subsequently edited this GEDCOM file using genealogy software that deletes media objects, then select this option to merge the current media objects with the new GEDCOM file.') ?>
					</p>
					<label>
						<input type="checkbox" name="WORD_WRAPPED_NOTES" value="1" <?= $tree->getPreference('WORD_WRAPPED_NOTES') ? 'checked' : '' ?>>
						<?= I18N::translate('Add spaces where long lines were wrapped') ?>
					</label>
					<p class="small text-muted">
						<?= I18N::translate('If you created this GEDCOM file using genealogy software that omits spaces when splitting long lines, then select this option to reinsert the missing spaces.') ?>
					</p>
					<label for="GEDCOM_MEDIA_PATH">
						<?= /* I18N: A media path (e.g. c:\aaa\bbb\ccc\ddd.jpeg) in a GEDCOM file */ I18N::translate('Remove the GEDCOM media path from filenames') ?>
					</label>
					<input
						class="form-control"
						dir="ltr"
						id="GEDCOM_MEDIA_PATH"
						maxlength="255"
						name="GEDCOM_MEDIA_PATH"
						type="text"
						value="<?= e($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')) ?>"
						>
					<p class="small text-muted">
						<?= /* I18N: Help text for the “GEDCOM media path” configuration setting. A “path” is something like “C:\Documents\Genealogy\Photos\John_Smith.jpeg” */ I18N::translate('Some genealogy software creates GEDCOM files that contain media filenames with full paths. These paths will not exist on the web-server. To allow webtrees to find the file, the first part of the path must be removed.') ?>
							<?= /* I18N: Help text for the “GEDCOM media path” configuration setting. %s are all folder names */ I18N::translate('For example, if the GEDCOM file contains %1$s and webtrees expects to find %2$s in the media folder, then you would need to remove %3$s.', '<code>C:\\Documents\\family\\photo.jpeg</code>', '<code>family\\photo.jpeg</code>', '<code>C:\\Documents\\</code>') ?>
					</p>
				</div>
			</div>
		</fieldset>

		<div class="row form-group">
			<div class="offset-sm-3 col-sm-9">
				<button type="submit" class="btn btn-primary">
					<?= /* I18N: A button label. */ I18N::translate('continue') ?>
				</button>
			</div>
		</div>
	</form>

	<script>
    function checkGedcomImportForm (message) {
      var oldFile = $('#gedcom_filename').val();
      var method = $('input[name=action]:checked').val();
      var newFile = method === 'replace_import' ? $('#import-server-file').val() : $('#import-computer-file').val();

      // Some browsers include c:\fakepath\ in the filename.
      newFile = newFile.replace(/.*[/\\]/, '');
      if (newFile !== oldFile && oldFile !== '') {
        return window.confirm(message);
      } else {
        return true;
      }
    }

    document.getElementById("import-computer-file").addEventListener("click", function () {
      document.getElementById("import-computer").checked = true;
    });
    document.getElementById("import-server-file").addEventListener("focus", function () {
      document.getElementById("import-server").checked = true;
    });
	</script>

		<?php

		return;
}

if (!Tree::getAll()) {
	FlashMessages::addMessage(I18N::translate('You need to create a family tree.'), 'info');
}

$controller->pageHeader();

$all_trees = Tree::getAll();
// On sites with hundreds or thousands of trees, this page becomes very large.
// Just show the current tree, the default tree, and unimported trees
if (count($all_trees) >= $multiple_tree_threshold) {
	$all_trees = array_filter($all_trees, function (Tree $x) use ($WT_TREE) {
		return $x->getPreference('imported') === '0' || $WT_TREE->getTreeId() === $x->getTreeId() || $x->getName() === Site::getPreference('DEFAULT_GEDCOM');
	});
}

// List the gedcoms available to this user
echo Bootstrap4::breadcrumbs([
	route('admin-control-panel') => I18N::translate('Control panel'),
], $controller->getPageTitle());
?>

<h1><?= $controller->getPageTitle() ?></h1>

<div id="accordion" role="tablist" aria-multiselectable="true">
	<?php foreach ($all_trees as $tree): ?>
	<?php if (Auth::isManager($tree)): ?>
		<div class="card">
			<div class="card-header" role="tab" id="card-tree-header-<?= $tree->getTreeId() ?>">
				<h2 class="mb-0">
					<i class="fas fa-tree fa-fw"></i>
					<a data-toggle="collapse" data-parent="#accordion" href="#card-tree-content-<?= $tree->getTreeId() ?>" <?= $tree == $WT_TREE || $tree->getPreference('imported') === '0' ? 'aria-expanded="true"' : '' ?> aria-controls="card-tree-content-<?= $tree->getTreeId() ?>">
						<?= e($tree->getName()) ?> — <?= e($tree->getTitle()) ?>
					</a>
				</h2>
			</div>
			<div id="card-tree-content-<?= $tree->getTreeId() ?>" class="collapse<?= $tree == $WT_TREE || $tree->getPreference('imported') === '0' ? ' show' : '' ?>" role="tabpanel" aria-labelledby="panel-tree-header-<?= $tree->getTreeId() ?>">
				<div class="card-body">
					<?php
					$importing = Database::prepare(
						"SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id = ? AND imported = '0' LIMIT 1"
					)->execute([$tree->getTreeId()])->fetchOne();
					if ($importing) {
						?>
						<div id="import<?= $tree->getTreeId() ?>" class="col-xs-12">
							<div class="progress">
								<?= I18N::translate('Calculating…') ?>
							</div>
						</div>
						<?php $controller->addInlineJavascript('$("#import' . $tree->getTreeId() . '").load("import.php?gedcom_id=' . $tree->getTreeId() . '");');
					}
					?>
					<div class="row<?= $importing ? ' hidden' : '' ?>" id="actions<?= $tree->getTreeId() ?>">
					<div class="col-sm-6 col-md-3">
						<h3>
							<a href="index.php?ctype=gedcom&ged=<?= $tree->getNameUrl() ?>">
								<?= I18N::translate('Family tree') ?>
							</a>
						</h3>
						<ul class="fa-ul">
							<!-- PREFERENCES -->
							<li>
								<span class="fa-li"><i class="fas fa-cogs"></i></span>
								<a href="<?= e(Html::url('admin_trees_config.php', ['ged' => $tree->getName(), 'action' => 'general'])) ?>">
									<?= I18N::translate('Preferences') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- PRIVACY -->
							<li>
								<span class="fa-li"><i class="fas fa-lock"></i></span>
								<a href="<?= e(route('tree-privacy', ['ged' => $tree->getName()])) ?>">
									<?= I18N::translate('Privacy') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- HOME PAGE BLOCKS-->
							<li>
								<span class="fa-li"><i class="fas fa-th-large"></i></span>
								<a href="<?= e(route('tree-page-edit', ['ged' => $tree->getName()])) ?>">
									<?= I18N::translate('Change the “Home page” blocks') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- DELETE -->
							<li>
								<span class="fa-li"><i class="far fa-trash-alt"></i></span>
								<a href="#" data-confirm="<?= I18N::translate('Are you sure you want to delete “%s”?', e($tree->getTitle())) ?>" onclick="if (confirm(this.dataset.confirm)) { document.delete_form<?= $tree->getTreeId() ?>.submit(); } return false;">
									<?= I18N::translate('Delete') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
								<form name="delete_form<?= $tree->getTreeId() ?>" method="post">
									<input type="hidden" name="action" value="delete">
									<input type="hidden" name="gedcom_id" value="<?= $tree->getTreeId() ?>">
									<?= Filter::getCsrf() ?>
									<!-- A11Y - forms need submit buttons, but they look ugly here -->
									<button class="sr-only" data-confirm="<?= I18N::translate('Are you sure you want to delete “%s”?', e($tree->getTitle())) ?>" onclick="return confirm(this.dataset.confirm)" type="submit">
										<?= I18N::translate('Delete') ?>
									</button>
								</form>
							</li>
							<!-- SET AS DEFAULT -->
							<?php if (count(Tree::getAll()) > 1): ?>
								<li>
									<span class="fa-li"><i class="far fa-star"></i></span>
									<?php if ($tree->getName() === Site::getPreference('DEFAULT_GEDCOM')): ?>
										<?= I18N::translate('Default family tree') ?>
									<?php else: ?>
										<a href="#" onclick="document.defaultform<?= $tree->getTreeId() ?>.submit();">
											<?= I18N::translate('Set as default') ?>
											<span class="sr-only"><?= e($tree->getTitle()) ?></span>
										</a>
										<form name="defaultform<?= $tree->getTreeId() ?>" method="post">
											<input type="hidden" name="action" value="setdefault">
											<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
											<?= Filter::getCsrf() ?>
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
								<a href="admin_trees_duplicates.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::translate('Find duplicates') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- MERGE -->
							<li>
								<span class="fa-li"><i class="fas fa-code-branch"></i></span>
								<a href="<?= e(route('merge-records', ['ged' => $tree->getName()])) ?>">
									<?= I18N::translate('Merge records') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- UPDATE PLACE NAMES -->
							<li>
								<span class="fa-li"><i class="fas fa-map-marker-alt"></i></span>
								<a href="admin_trees_places.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::translate('Update place names') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- CHECK FOR ERRORS -->
							<li>
								<span class="fa-li"><i class="fas fa-check"></i></span>
								<a href="admin_trees_check.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::translate('Check for errors') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- UNCONNECTED INDIVIDUALS -->
							<li>
								<span class="fa-li"><i class="fas fa-unlink"></i></span>
								<a href="admin_trees_unconnected.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::translate('Find unrelated individuals') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- RENUMBER -->
							<li>
								<span class="fa-li"><i class="fas fa-sort-numeric-down"></i></span>
								<a href="admin_trees_renumber.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::translate('Renumber') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- CHANGES -->
							<li>
								<span class="fa-li"><i class="fas fa-th-list"></i></span>
								<a href="<?= route('admin-changes-log', ['ged' => $tree->getName()]) ?>">
									<?= I18N::translate('Changes log') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
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
								<a href="edit_interface.php?action=add_unlinked_indi&amp;ged=<?= e($tree->getName()) ?>">
									<?= I18N::translate('Individual') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED SOURCE -->
							<li>
								<span class="fa-li"><i class="fas fa-book"></i></span>
								<a href="#" data-href="<?= e(route('create-source', ['tree' => $tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
									<?= I18N::translate('Source') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED REPOSITORY -->
							<li>
								<span class="fa-li"><i class="fas fa-university"></i></span>
								<a href="#" data-href="<?= e(route('create-repository', ['tree' => $tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
									<?= I18N::translate('Repository') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED MEDIA OBJECT -->
							<li>
								<span class="fa-li"><i class="far fa-image"></i></span>
								<a href="#" data-href="<?= e(route('create-media-object', ['tree' => $tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">

									<?= I18N::translate('Media object') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED NOTE -->
							<li>
								<span class="fa-li"><i class="fas fa-paragraph"></i></span>
								<a href="#" data-href="<?= e(route('create-note-object', ['tree' => $tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
									<?= I18N::translate('Shared note') ?>
								</a>
							</li>
							<!-- UNLINKED SUBMITTER -->
							<li>
								<span class="fa-li"><i class="far fa-user"></i></span>
								<a href="#" data-href="<?= e(route('create-submitter', ['tree' => $tree->getName()])) ?>" data-target="#wt-ajax-modal" data-toggle="modal">
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
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
								<a href="admin_trees_download.php?ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::translate('Export') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
									</span>
								</a>
							</li>
							<!-- UPLOAD/IMPORT -->
							<li>
								<span class="fa-li"><i class="fas fa-upload"></i></span>
								<a href="?action=importform&amp;gedcom_id=<?= $tree->getTreeId() ?>&amp;ged=<?= $tree->getNameUrl() ?>">
									<?= I18N::translate('Import') ?>
									<span class="sr-only">
										<?= e($tree->getTitle()) ?>
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
		<div id="card-tree-create-content" class="collapse<?= empty(Tree::getAll()) ? ' show' : '' ?>" role="tabpanel" aria-labelledby="card-tree-create-header">
			<div class="card-body">
				<form class="form-horizontal" method="post">
					<?= Filter::getCsrf() ?>
					<input type="hidden" name="action" value="new_tree">
					<div class="row form-group">
						<label for="tree_title" class="col-sm-2 col-form-label">
							<?= I18N::translate('Family tree title') ?>
						</label>
						<div class="col-sm-10">
							<input
								class="form-control"
								id="tree_title"
								maxlength="255"
								name="tree_title"
								required
								type="text"
								placeholder="<?= $default_tree_title ?>"
							>
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
								<input
									class="form-control"
									id="tree_name"
									maxlength="31"
									name="tree_name"
									pattern="[^&lt;&gt;&amp;&quot;#^$*?{}()\[\]/\\]*"
									required
									type="text"
									value="<?= $default_tree_name ?>"
									>
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
	<?php if (empty(Tree::getAll()) && count(User::all()) === 1): ?>
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
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">
				<i class="fas fa-sync-alt fa-fw"></i>
				<a data-toggle="collapse" data-parent="#accordion" href="#synchronize-gedcom-files">
					<?= I18N::translate('Synchronize family trees with GEDCOM files') ?>
				</a>
			</h2>
		</div>
		<div id="synchronize-gedcom-files" class="panel-collapse collapse">
			<div class="panel-body">
				<p>
					<?= I18N::translate('Create, update, and delete a family tree for every GEDCOM file in the data folder.') ?>
				</p>
				<form method="post" class="form form-horizontal">
					<?= Filter::getCsrf() ?>
					<input type="hidden" name="action" value="synchronize">
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
