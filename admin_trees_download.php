<?php
namespace Fisharebest\Webtrees;

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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use PclZip;
use Zend_Session;

define('WT_SCRIPT_NAME', 'admin_trees_download.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->setPageTitle(I18N::translate('Download GEDCOM') . ' — ' . Filter::escapeHtml(WT_GEDCOM))
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
	$download_filename = WT_GEDCOM;
	if (strtolower(substr($download_filename, -4, 4)) != '.ged') {
		$download_filename .= '.ged';
	}

	if ($zip === 'yes') {
		$temp_dir = WT_DATA_DIR . 'tmp-' . WT_GEDCOM . '-' . date('YmdHis') . '/';
		$zip_file = $download_filename . '.zip';

		if (!File::mkdir($temp_dir)) {
			echo "Error : Could not create temporary path!";

			return;
		}

		// Create the unzipped GEDCOM on disk, so we can ZIP it.
		$stream = fopen($temp_dir . $download_filename, "w");
		export_gedcom(WT_GEDCOM, $stream, $exportOptions);
		fclose($stream);

		// Create a ZIP file containing the GEDCOM file.
		$comment = "Created by " . WT_WEBTREES . " " . WT_VERSION . " on " . date("r") . ".";
		$archive = new PclZip($temp_dir . $zip_file);
		$v_list = $archive->add($temp_dir . $download_filename, PCLZIP_OPT_COMMENT, $comment, PCLZIP_OPT_REMOVE_PATH, $temp_dir);
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
		Zend_Session::writeClose();
		header('Content-Type: text/plain; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $download_filename . '"');
		// Stream the GEDCOM file straight to the browser.
		// We could open "php://compress.zlib" to create a .gz file or "php://compress.bzip2" to create a .bz2 file
		$stream = fopen('php://output', 'w');
		export_gedcom(WT_GEDCOM, $stream, $exportOptions);
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

<form class="form form-horizontal">
	<input type="hidden" name="action" value="download">
	<input type="hidden" name="ged" value="<?php echo WT_GEDCOM; ?>">
	<div id="tree-download">
		<dl>
			<dt>
				<?php echo I18N::translate('Zip file(s)'), help_link('download_zipped'); ?>
			</dt>
			<dd>
				<input type="checkbox" name="zip" value="yes">
			</dd>
			<dt>
				<?php echo I18N::translate('Apply privacy settings?'), help_link('apply_privacy'); ?>
			</dt>
			<dd>
				<input type="radio" name="privatize_export" value="none" checked>&nbsp;&nbsp;<?php echo I18N::translate('None'); ?>
				<br>
				<input type="radio" name="privatize_export" value="gedadmin">&nbsp;&nbsp;<?php echo I18N::translate('Manager'); ?>
				<br>
				<input type="radio" name="privatize_export" value="user">&nbsp;&nbsp;<?php echo I18N::translate('Member'); ?>
				<br>
				<input type="radio" name="privatize_export" value="visitor">&nbsp;&nbsp;<?php echo I18N::translate('Visitor'); ?>
			</dd>
			<dt>
				<?php echo I18N::translate('Convert from UTF-8 to ANSI (ISO-8859-1)'), help_link('utf8_ansi'); ?>
			</dt>
			<dd>
				<input type="checkbox" name="convert" value="yes">
			</dd>
			<dt>
				<?php echo I18N::translate('Add the GEDCOM media path to filenames'), help_link('GEDCOM_MEDIA_PATH'); ?>
			</dt>
			<dd>
				<input type="checkbox" name="conv_path" value="<?php echo Filter::escapeHtml($GEDCOM_MEDIA_PATH); ?>">
				<span dir="auto"><?php echo Filter::escapeHtml($GEDCOM_MEDIA_PATH); ?></span>
			</dd>
		</dl>
	</div>
	<br>
	<input type="submit" value="<?php echo I18N::translate('continue'); ?>">
</form>
