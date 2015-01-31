<?php
// UI for online updating of the GEDCOM configuration.
//
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\Theme;
use WT\User;

define('WT_SCRIPT_NAME', 'admin_trees_manage.php');
require './includes/session.php';

$controller = new WT_Controller_Page;
$controller
	->restrictAccess(Auth::isManager())
	->setPageTitle(WT_I18N::translate('Manage family trees'));

$gedcom_files = glob(WT_DATA_DIR . '*.{ged,Ged,GED}', GLOB_NOSORT | GLOB_BRACE);

// Process POST actions
switch (WT_Filter::post('action')) {
case 'delete':
	$gedcom_id = WT_Filter::postInteger('gedcom_id');
	if (WT_Filter::checkCsrf() && $gedcom_id) {
		$tree = WT_Tree::get($gedcom_id);
		WT_FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ WT_I18N::translate('The family tree “%s” has been deleted.', WT_Filter::escapeHtml($tree->tree_name)), 'success');
		$tree->delete();
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
case 'setdefault':
	if (WT_Filter::checkCsrf()) {
		WT_Site::setPreference('DEFAULT_GEDCOM', WT_Filter::post('ged'));
		WT_FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ WT_I18N::translate('The family tree “%s” will be shown to visitors when they first arrive at this website.', WT_Filter::escapeHtml($WT_TREE->tree_name)), 'success');
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
case 'new_tree':
	$basename   = basename(WT_Filter::post('tree_name'));
	$tree_title = WT_Filter::post('tree_title');

	if (WT_Filter::checkCsrf() && $basename && $tree_title) {
		if (WT_Tree::getIdFromName($basename)) {
			WT_FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ WT_I18N::translate('The family tree “%s” already exists.', WT_Filter::escapeHtml($basename)), 'danger');
		} else {
			WT_Tree::create($basename, $tree_title);
			WT_FlashMessages::addMessage(/* I18N: %s is the name of a family tree */ WT_I18N::translate('The family tree “%s” has been created.', WT_Filter::escapeHtml($basename)), 'success');
		}
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME . '?ged=' . $basename);

	return;
case 'replace_upload':
	$gedcom_id = WT_Filter::postInteger('gedcom_id');
	$keep_media = WT_Filter::postBool('keep_media');
	$tree      = WT_Tree::get($gedcom_id);

	if (WT_Filter::checkCsrf() && $tree) {
		foreach ($_FILES as $FILE) {
			if ($FILE['error'] == 0 && is_readable($FILE['tmp_name'])) {
				$tree->importGedcomFile($FILE['tmp_name'], $FILE['name'], $keep_media);
			}
		}
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
case 'replace_import':
	$basename   = basename(WT_Filter::post('tree_name'));
	$gedcom_id  = WT_Filter::postInteger('gedcom_id');
	$keep_media = WT_Filter::postBool('keep_media');
	$tree       = WT_Tree::get($gedcom_id);

	if (WT_Filter::checkCsrf() && $tree && $basename) {
		$tree->importGedcomFile(WT_DATA_DIR . $basename, $basename, $keep_media);
	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;

case 'bulk-import':
	if (WT_Filter::checkCsrf()) {
		$tree_names = WT_Tree::getNameList();
		$basenames  = array();

		foreach ($gedcom_files as $gedcom_file) {
			$filemtime   = filemtime($gedcom_file); // Only import files that have changed
			$basename    = basename($gedcom_file);
			$basenames[] = $basename;

			$tree = WT_Tree::create($basename, $basename);
			if ($tree->getPreference('filemtime') != $filemtime) {
				$tree->importGedcomFile($gedcom_file, $basename, false);
				$tree->setPreference('filemtime', $filemtime);
				WT_FlashMessages::addMessage(WT_I18N::translate('The GEDCOM file “%s” has been imported.', WT_Filter::escapeHtml($basename)), 'success');
			}
		}

		foreach (WT_Tree::getAll() as $tree) {
			if (!in_array($tree->tree_name, $basenames)) {
				WT_FlashMessages::addMessage(WT_I18N::translate('The family tree “%s” has been deleted.', WT_Filter::escapeHtml($tree->tree_name)), 'success');
				$tree->delete();
			}
		}

	}
	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
}

$default_tree_title  = /* I18N: Default name for a new tree */ WT_I18N::translate('My family tree');
$default_tree_name   = 'tree';
$default_tree_number = 1;
$existing_trees      = WT_Tree::getNameList();
while (array_key_exists($default_tree_name . $default_tree_number, $existing_trees)) {
	$default_tree_number++;
}
$default_tree_name .= $default_tree_number;

// Process GET actions
switch (WT_Filter::get('action')) {
case 'uploadform':
case 'importform':
	if (WT_Filter::get('action') === 'uploadform') {
		$controller->setPageTitle(WT_I18N::translate('Upload family tree'));
	} else {
		$controller->setPageTitle(WT_I18N::translate('Import family tree'));
	}

	$controller->pageHeader();

	?>
	<ol class="breadcrumb small">
		<li><a href="admin.php"><?php echo WT_I18N::translate('Control panel'); ?></a></li>
		<li><a href="admin_trees_manage.php"><?php echo WT_I18N::translate('Manage family trees'); ?></a></li>
		<li class="active"><?php echo $controller->getPageTitle(); ?></li>
	</ol>

	<h1><?php echo $controller->getPageTitle(); ?></h1>
	<?php

	$tree = WT_Tree::get(WT_Filter::getInteger('gedcom_id'));
	// Check it exists
	if (!$tree) {
		break;
	}
	echo '<p>', /* I18N: %s is the name of a family tree */ WT_I18N::translate('This will delete all the genealogical data from “%s” and replace it with data from another GEDCOM file.', $tree->tree_name_html), '</p>';
	// the javascript in the next line strips any path associated with the file before comparing it to the current GEDCOM name (both Chrome and IE8 include c:\fakepath\ in the filename).
	$previous_gedcom_filename = $tree->getPreference('gedcom_filename');
	echo '<form name="replaceform" method="post" enctype="multipart/form-data" action="', WT_SCRIPT_NAME, '" onsubmit="var newfile = document.replaceform.ged_name.value; newfile = newfile.substr(newfile.lastIndexOf(\'\\\\\')+1); if (newfile!=\'', WT_Filter::escapeHtml($previous_gedcom_filename), '\' && \'\' != \'', WT_Filter::escapeHtml($previous_gedcom_filename), '\') return confirm(\'', WT_Filter::escapeHtml(WT_I18N::translate('You have selected a GEDCOM file with a different name.  Is this correct?')), '\'); else return true;">';
	echo '<input type="hidden" name="gedcom_id" value="', $tree->tree_id, '">';
	echo WT_Filter::getCsrf();
	if (WT_Filter::get('action') == 'uploadform') {
		echo '<input type="hidden" name="action" value="replace_upload">';
		echo '<input type="file" name="tree_name">';
	} else {
		echo '<input type="hidden" name="action" value="replace_import">';
		$d = opendir(WT_DATA_DIR);
		$files = array();
		while (($f = readdir($d)) !== false) {
			if (!is_dir(WT_DATA_DIR . $f) && is_readable(WT_DATA_DIR . $f)) {
				$fp = fopen(WT_DATA_DIR . $f, 'rb');
				$header = fread($fp, 64);
				fclose($fp);
				if (preg_match('/^(' . WT_UTF8_BOM . ')?0 *HEAD/', $header)) {
					$files[] = $f;
				}
			}
		}
		if ($files) {
			sort($files);
			echo WT_DATA_DIR, '<select name="tree_name">';
			foreach ($files as $gedcom_file) {
				echo '<option value="', WT_Filter::escapeHtml($gedcom_file), '" ';
				if ($gedcom_file == $previous_gedcom_filename) {
					echo '';
				}
				echo'>', WT_Filter::escapeHtml($gedcom_file), '</option>';
			}
			echo '</select>';
		} else {
			echo '<p>', /* I18N: %s is the name of a folder */ WT_I18N::translate('No GEDCOM files found.  You need to copy files to the “%s” folder on your server.', WT_DATA_DIR);
			echo '</form>';

			return;
		}
	}
	echo '<br><br><input type="checkbox" name="keep_media" value="1">';
	echo WT_I18N::translate('If you have created media objects in webtrees, and have edited your gedcom off-line using a program that deletes media objects, then check this box to merge the current media objects with the new GEDCOM file.');
	echo '<br><br><input type="submit" value="', /* I18N: A button label */ WT_I18N::translate('continue'), '">';
	echo '</form>';

	return;
}

if (!WT_Tree::getAll()) {
	WT_FlashMessages::addMessage(WT_I18N::translate('You need to create a family tree.'), 'info');
}

$controller->pageHeader();

// List the gedcoms available to this user
?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo WT_I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo WT_I18N::translate('Manage family trees'); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<div class="panel-group" id="accordion" role="tablist">
	<?php foreach (WT_Tree::GetAll() as $tree): ?>
	<?php if (Auth::isManager($tree)): ?>
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="panel-tree-<?php echo $tree->tree_id; ?>">
			<h2 class="panel-title">
				<i class="fa fa-fw fa-tree"></i>
				<a data-toggle="collapse" data-parent="#accordion" href="#tree-<?php echo $tree->tree_id; ?>" aria-expanded="true" aria-controls="tree-<?php echo $tree->tree_id; ?>">
					<?php echo WT_Filter::escapeHtml($tree->tree_name); ?> — <?php echo WT_Filter::escapeHtml($tree->tree_title); ?>
				</a>
			</h2>
		</div>
		<div id="tree-<?php echo $tree->tree_id; ?>" class="panel-collapse collapse<?php echo $tree->tree_id === WT_GED_ID || $tree->getPreference('imported') === '0' ? ' in' : ''; ?>" role="tabpanel" aria-labelledby="panel-tree-<?php echo $tree->tree_id; ?>">
			<div class="panel-body">
				<?php

		// The third row shows an optional progress bar and a list of maintenance options
		$importing = WT_DB::prepare(
			"SELECT 1 FROM `##gedcom_chunk` WHERE gedcom_id = ? AND imported = '0' LIMIT 1"
		)->execute(array($tree->tree_id))->fetchOne();
		if ($importing) {
				?>
				<div id="import<?php echo $tree->tree_id; ?>" class="col-xs-12">
					<div class="progress">
						<?php echo WT_I18N::translate('Calculating…'); ?>
					</div>
				</div>
				<?php
			$controller->addInlineJavascript(
				'jQuery("#import' . $tree->tree_id . '").load("import.php?gedcom_id=' . $tree->tree_id . '");'
			);
		}
				?>
				<div class="row<?php echo $importing ? ' hidden' : ''; ?>" id="actions<?php echo $tree->tree_id; ?>">
					<div class="col-sm-6 col-md-3">
						<h3>
							<a href="index.php?ctype=gedcom&ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
								<?php echo WT_I18N::translate('Family tree'); ?>
							</a>
						</h3>
						<ul class="fa-ul">
							<!-- PREFERENCES -->
							<li>
								<i class="fa fa-li fa-cogs"></i>
								<a href="admin_trees_config.php?action=general&amp;ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Preferences'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- PRIVACY -->
							<li>
								<i class="fa fa-li fa-lock"></i>
								<a href="admin_trees_config.php?action=privacy&amp;ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Privacy'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- HOME PAGE BLOCKS-->
							<li>
								<i class="fa fa-li fa-th-large"></i>
								<a href="index_edit.php?gedcom_id=<?php echo $tree->tree_id; ?>">
									<?php echo WT_I18N::translate('Change the “Home page” blocks'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- DELETE -->
							<li>
								<i class="fa fa-li fa-trash-o"></i>
								<a href="#" onclick="if (confirm('<?php echo WT_Filter::escapeJs(WT_I18N::translate('Are you sure you want to delete “%s”?', $tree->tree_name)); ?>')) { document.delete_form<?php echo $tree->tree_id; ?>.submit(); } return false;">
									<?php echo WT_I18N::translate('Delete'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
								<form name="delete_form<?php echo $tree->tree_id; ?>" method="post">
									<input type="hidden" name="action" value="delete">
									<input type="hidden" name="gedcom_id" value="<?php echo $tree->tree_id; ?>">
									<?php echo WT_Filter::getCsrf(); ?>
									<!-- A11Y - forms need submit buttons, but they look ugly here -->
									<button class="sr-only" onclick="return confirm('<?php echo WT_Filter::escapeJs(WT_I18N::translate('Are you sure you want to delete “%s”?', $tree->tree_name)); ?>')" type="submit">
										<?php echo WT_I18N::translate('Delete'); ?>
									</button>
								</form>
							</li>
							<!-- SET AS DEFAULT -->
							<?php if (count(WT_Tree::getAll()) > 1): ?>
								<li>
									<i class="fa fa-li fa-star"></i>
									<?php if ($tree->tree_name == WT_Site::getPreference('DEFAULT_GEDCOM')): ?>
										<?php echo WT_I18N::translate('Default family tree'); ?>
									<?php else: ?>
										<a href="#" onclick="document.defaultform<?php echo $tree->tree_id; ?>.submit();">
											<?php echo WT_I18N::translate('Set as default'); ?>
											<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
										</a>
										<form name="defaultform<?php echo $tree->tree_id; ?>" method="post">
											<input type="hidden" name="action" value="setdefault">
											<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
											<?php echo WT_Filter::getCsrf(); ?>
											<!-- A11Y - forms need submit buttons, but they look ugly here -->
											<button class="sr-only" type="submit">
												<?php echo WT_I18N::translate('Set as default'); ?>
											</button>
										</form>
									<?php endif; ?>
								</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="col-sm-6 col-md-3">
						<h3>
							<?php echo /* I18N: Individuals, sources, dates, places, etc. */ WT_I18N::translate('Genealogy data'); ?>
						</h3>
						<ul class="fa-ul">
							<!-- MERGE -->
							<li>
								<i class="fa fa-li fa-code-fork"></i>
								<a href="admin_site_merge.php?ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Merge records'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- UPDATE PLACE NAMES -->
							<li>
								<i class="fa fa-li fa-map-marker"></i>
								<a href="admin_trees_places.php?ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Update place names'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- CHECK FOR ERRORS -->
							<li>
								<i class="fa fa-li fa-check"></i>
								<a href="admin_trees_check.php?ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Check for errors'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- RENUMBER -->
							<li>
								<i class="fa fa-li fa-sort-numeric-asc"></i>
								<a href="admin_trees_renumber.php?ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Renumber'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- CHANGES -->
							<li>
								<i class="fa fa-li fa-th-list"></i>
								<a href="admin_site_change.php?gedc=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Changes log'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
						</ul>
					</div>
					<div class="clearfix visible-sm-block"></div>
					<div class="col-sm-6 col-md-3">
						<h3>
							<?php echo WT_I18N::translate('Add unlinked records'); ?>
						</h3>
						<ul class="fa-ul">
							<!-- UNLINKED INDIVIDUAL -->
							<li>
								<i class="fa fa-li fa-user"></i>
								<a href="#" onclick="add_unlinked_indi(); return false;">
									<?php echo WT_I18N::translate('Individual'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED SOURCE -->
							<li>
								<i class="fa fa-li fa-book"></i>
								<a href="#" onclick="addnewsource(''); return false;">
									<?php echo WT_I18N::translate('Source'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED REPOSITORY -->
							<li>
								<i class="fa fa-li fa-university"></i>
								<a href="#" onclick="addnewrepository(''); return false;">
									<?php echo WT_I18N::translate('Repository'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED MEDIA OBJECT -->
							<li>
								<i class="fa fa-li fa-photo"></i>
								<a href="#" onclick="window.open('addmedia.php?action=showmediaform', '_blank', edit_window_specs); return false;">
									<?php echo WT_I18N::translate('Media object'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- UNLINKED NOTE -->
							<li>
								<i class="fa fa-li fa-paragraph"></i>
								<a href="#" onclick="addnewnote(''); return false;">
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
									<?php echo WT_I18N::translate('Shared note'); ?>
								</a>
							</li>
						</ul>
					</div>
					<div class="col-sm-6 col-md-3">
						<h3>
							<?php echo WT_I18N::translate('GEDCOM file'); ?>
						</h3>
						<ul class="fa-ul">
							<!-- DOWNLOAD -->
							<li>
								<i class="fa fa-li fa-download"></i>
								<a href="admin_trees_download.php?ged=<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<?php echo WT_I18N::translate('Download'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- UPLOAD -->
							<li>
								<i class="fa fa-li fa-upload"></i>
								<a href="?action=uploadform&amp;gedcom_id=<?php echo $tree->tree_id; ?>">
									<?php echo WT_I18N::translate('Upload'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
							<!-- EXPORT -->
							<li>
								<form action="admin_trees_export.php" method="post">
									<?php echo WT_Filter::getCsrf(); ?>
									<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>">
									<i class="fa fa-li fa-file-text"></i>
									<input type="submit" class="hide"><!-- for WCAG2 -->
									<a href="#" onclick="jQuery(this).closest('form').submit();">
										<?php echo WT_I18N::translate('Export'); ?>
									</a>
								</form>
							</li>
							<!-- IMPORT -->
							<li>
								<i class="fa fa-li fa-file-text-o"></i>
								<a href="?action=importform&amp;gedcom_id=<?php echo $tree->tree_id; ?>">
									<?php echo WT_I18N::translate('Import'); ?>
									<span class="sr-only">
										<?php echo WT_Filter::escapeHtml($tree->tree_name); ?>
									</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php endforeach; ?>
	<?php if (Auth::isAdmin()): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">
				<i class="fa fa-fw fa-plus"></i>
				<a data-toggle="collapse" data-parent="#accordion" href="#create-a-new-family-tree">
					<?php echo WT_I18N::translate('Create a new family tree'); ?>
				</a>
			</h2>
		</div>
		<div id="create-a-new-family-tree" class="panel-collapse collapse<?php echo WT_Tree::getAll() ? '' : ' in'; ?>">
			<div class="panel-body">
				<form role="form" class="form-horizontal" method="post">
					<?php echo WT_Filter::getCsrf(); ?>
					<input type="hidden" name="action" value="new_tree">
					<div class="form-group">
						<label for="tree_title" class="col-sm-2 control-label">
							<?php echo WT_I18N::translate('Family tree title'); ?>
						</label>
						<div class="col-sm-10">
							<input
								class="form-control"
								id="tree_title"
								maxlength="255"
								name="tree_title"
								required
								type="text"
								value="<?php echo $default_tree_title; ?>"
							>
						</div>
					</div>
					<div class="form-group">
						<label for="tree_name" class="col-sm-2 control-label">
							<?php echo WT_I18N::translate('URL'); ?>
						</label>
						<div class="col-sm-10">
							<div class="input-group">
								<span class="input-group-addon">
									<?php echo WT_BASE_URL; ?>?ged=
								</span>
								<input
									class="form-control"
									id="tree_name"
									maxlength="31"
									name="tree_name"
									pattern="[^&lt;&gt;&amp;&quot;#^$.*?{}()\[\]/\\]*"
									required
									type="text"
									value="<?php echo $default_tree_name; ?>"
									>
							</div>
							<p class="small text-muted">
								<?php echo WT_I18N::translate('Avoid spaces and puncutation.  A family name might be a good choice.'); ?>
							</p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-check"></i>
								<?php echo /* I18N: Button label */ WT_I18N::translate('create'); ?>
							</button>
							<p class="small text-muted">
								<?php echo WT_I18N::translate('After creating the family tree, you will be able to upload or import data from a GEDCOM file.'); ?>
							</p>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<!-- display link to PGV-WT transfer wizard on first visit to this page, before any GEDCOM is loaded -->
	<?php if (count(WT_Tree::GetAll()) === 0 && count(User::all()) === 1): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">
			<i class="fa fa-fw fa-magic"></i>
			<a data-toggle="collapse" data-parent="#accordion" href="#pgv-import-wizard">
				<?php echo WT_I18N::translate('PhpGedView to webtrees transfer wizard'); ?>
			</a>
		</h2>
		</div>
		<div id="pgv-import-wizard" class="panel-collapse collapse">
			<div class="panel-body">
				<p>
					<?php echo WT_I18N::translate('The PGV to webtrees wizard is an automated process to assist administrators make the move from a PGV installation to a new webtrees one.  It will transfer all PGV GEDCOM and other database information directly to your new webtrees database.  The following requirements are necessary:'); ?>
				</p>
				<ul>
					<li>
						<?php echo WT_I18N::translate('webtrees’ database must be on the same server as PGV’s'); ?>
					</li>
					<li>
						<?php echo /* I18N: %s is a number */ WT_I18N::translate('PGV must be version 4.2.3, or any SVN up to #%s', WT_I18N::digits(7101)); ?>
					</li>
					<li>
						<?php echo WT_I18N::translate('All changes in PGV must be accepted'); ?>
					</li>
					<li>
						<?php echo WT_I18N::translate('All existing PGV users must have distinct email addresses'); ?>
					</li>
				</ul>
				<p>
					<?php echo WT_I18N::translate('<b>Important note:</b> The transfer wizard is not able to assist with moving media items.  You will need to set up and move or copy your media configuration and objects separately after the transfer wizard is finished.'); ?>
				</p>
				<p>
					<a href="admin_pgv_to_wt.php">
						<?php echo WT_I18N::translate('Click here for PhpGedView to webtrees transfer wizard'); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- BULK LOAD/SYNCHRONISE GEDCOM FILES -->
	<?php if (count($gedcom_files) >= 25): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">
			<i class="fa fa-fw fa-refresh"></i>
			<a data-toggle="collapse" data-parent="#accordion" href="#pgv-import-wizard">
				<?php echo WT_I18N::translate('Bulk import GEDCOM files'); ?>
			</a>
		</h2>
		</div>
		<div id="pgv-import-wizard" class="panel-collapse collapse">
			<div class="panel-body">
				<p>
					<?php echo WT_I18N::translate('Create or update a family tree for every GEDCOM file in the data folder.'); ?>
				</p>
				<form method="post" class="form form-horizontal">
					<?php echo WT_Filter::getCsrf(); ?>
					<input type="hidden" name="action" value="bulk-import">
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-check"></i>
						<?php echo /* I18N: Button label */ WT_I18N::translate('continue'); ?>
					</button>
					<p class="small text-muted">
						<?php echo WT_I18N::translate('Caution!  This may take a long time.  Be patient.'); ?>
					</p>
				</form>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
