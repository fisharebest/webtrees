<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsExport;
use PclZip;

define('WT_SCRIPT_NAME', 'admin_trees_download.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->setPageTitle(I18N::translate($WT_TREE->getTitleHtml()) . ' — ' . I18N::translate('Export a GEDCOM file'))
	->restrictAccess(Auth::isManager($WT_TREE));

// Validate user parameters
$action           = Filter::get('action', 'download');
$convert          = Filter::get('convert', 'yes|no', 'no');
$zip              = Filter::get('zip', 'yes|no', 'no');
$conv_path        = Filter::get('conv_path');
$privatize_export = Filter::get('privatize_export', 'none|visitor|user|gedadmin');

if ($action === 'download') {
	$exportOptions = array(
		'privatize' => $privatize_export,
		'toANSI'    => $convert,
		'path'      => $conv_path,
	);

	// What to call the downloaded file
	$download_filename = $WT_TREE->getName();
	if (strtolower(substr($download_filename, -4, 4)) != '.ged') {
		$download_filename .= '.ged';
	}

	if ($zip === 'yes') {
		$temp_dir = WT_DATA_DIR . 'tmp-' . $WT_TREE->getName() . '-' . date('YmdHis') . '/';
		$zip_file = $download_filename . '.zip';

		if (!File::mkdir($temp_dir)) {
			echo "Error : Could not create temporary path!";

			return;
		}

		// Create the unzipped GEDCOM on disk, so we can ZIP it.
		$stream = fopen($temp_dir . $download_filename, "w");
		FunctionsExport::exportGedcom($WT_TREE, $stream, $exportOptions);
		fclose($stream);

		// Create a ZIP file containing the GEDCOM file.
		$comment = "Created by " . WT_WEBTREES . " " . WT_VERSION . " on " . date("r") . ".";
		$archive = new PclZip($temp_dir . $zip_file);
		$v_list  = $archive->add($temp_dir . $download_filename, \PCLZIP_OPT_COMMENT, $comment, \PCLZIP_OPT_REMOVE_PATH, $temp_dir);
		if ($v_list == 0) {
			echo "Error : " . $archive->errorInfo(true);
		} else {
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="' . $zip_file . '"');
			header('Content-length: ' . filesize($temp_dir . $zip_file));
			readfile($temp_dir . $zip_file);
			File::delete($temp_dir);
		}
	} else {
		header('Content-Type: text/plain; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"');
		// Stream the GEDCOM file straight to the browser.
		// We could open "php://compress.zlib" to create a .gz file or "php://compress.bzip2" to create a .bz2 file
		$stream = fopen('php://output', 'w');
		FunctionsExport::exportGedcom($WT_TREE, $stream, $exportOptions);
		fclose($stream);
	}

	return;
}

$controller->pageHeader();

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_trees_manage.php"><?php echo I18N::translate('Manage family trees'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<form class="form form-horizontal" method="post" action="admin_trees_export.php">
	<?php echo Filter::getCsrf(); ?>
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">

	<div class="form-group">
		<label for="submit-export" class="col-sm-3 control-label">
			<?php echo I18N::translate('A file on the server'); ?>
		</label>
		<div class="col-sm-9">
			<button id="submit-export" type="submit" class="btn btn-primary">
				<?php echo /* I18N: A button label */ I18N::translate('continue'); ?>
			</button>
		</div>
	</div>
</form>

<hr>

<form class="form form-horizontal">
	<input type="hidden" name="action" value="download">
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">

	<!-- DOWNLOAD OPTIONS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo I18N::translate('Export options'); ?>
		</legend>

		<!-- ZIP FILES -->
		<div class="col-sm-9">
			<label>
				<input type="checkbox" name="zip" value="yes">
				<?php echo I18N::translate('Compress the GEDCOM file'); ?>
			</label>
			<p class="small muted">
				<?php echo I18N::translate('To reduce the size of the download, you can compress the data into a .ZIP file.  You will need to uncompress the .ZIP file before you can use it.'); ?>
			</p>

		<!-- CONVERT TO ISO8859-1 -->
			<label>
				<input type="checkbox" name="convert" value="yes">
				<?php echo I18N::translate('Convert from UTF-8 to ISO-8859-1'); ?>
			</label>
			<p class="small muted">
				<?php echo I18N::translate('webtrees uses UTF-8 encoding for accented letters, special characters and non-latin scripts.  If you want to use this GEDCOM file with genealogy software that does not support UTF-8, then you can create it using ISO-8859-1 encoding.'); ?>
			</p>

			<!-- GEDCOM_MEDIA_PATH -->
			<?php if ($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')): ?>
			<label>
				<input type="checkbox" name="conv_path" value="<?php echo Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')); ?>">
				<?php echo /* I18N: A media path (e.g. C:\aaa\bbb\ccc\) in a GEDCOM file */ I18N::translate('Add the GEDCOM media path to filenames'); ?>
			</label>
			<p>
				<?php echo /* I18N: %s is the name of a folder. */ I18N::translate('Media filenames will be prefixed by %s.', '<code dir="ltr">' . Filter::escapeHtml($WT_TREE->getPreference('GEDCOM_MEDIA_PATH')) . '</code>'); ?>
			</p>
			<?php endif; ?>
		</div>
	</fieldset>

	<!-- PRIVACY OPTIONS -->
	<fieldset class="form-group">
		<legend class="control-label col-sm-3">
			<?php echo I18N::translate('Apply privacy settings'); ?>
		</legend>

		<div class="col-sm-9">
			<label>
				<input type="radio" name="privatize_export" value="none" checked>
				<?php echo I18N::translate('None'); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="privatize_export" value="gedadmin">
				<?php echo I18N::translate('Manager'); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="privatize_export" value="user">
				<?php echo I18N::translate('Member'); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="privatize_export" value="visitor">
				<?php echo I18N::translate('Visitor'); ?>
			</label>
		</div>
	</fieldset>

	<div class="form-group">
		<label for="submit-export" class="col-sm-3 control-label">
			<?php echo I18N::translate('A file on your computer'); ?>
		</label>
		<div class="col-sm-9">
			<button id="submit-export" type="submit" class="btn btn-primary">
				<?php echo /* I18N: A button label */ I18N::translate('continue'); ?>
			</button>
		</div>
	</div>
</form>

</form>
