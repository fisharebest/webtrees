<?php
// Allow an admin user to download the entire gedcom file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

define('WT_SCRIPT_NAME', 'admin_trees_download.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_export.php';

$controller = new WT_Controller_Page();
$controller
	->setPageTitle(WT_I18N::translate('Download GEDCOM'))
	->restrictAccess(Auth::isManager());

// Validate user parameters
$action           = WT_Filter::get('action',           'download');
$convert          = WT_Filter::get('convert',          'yes|no', 'no');
$zip              = WT_Filter::get('zip',              'yes|no', 'no');
$conv_path        = WT_Filter::get('conv_path');
$privatize_export = WT_Filter::get('privatize_export', 'none|visitor|user|gedadmin');

if ($action == 'download') {
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

	if ($zip == "yes") {
		require WT_ROOT.'library/pclzip.lib.php';

		$temp_dir = WT_DATA_DIR . 'tmp-' . WT_GEDCOM . '-' . date("YmdHis") . '/';
		$zip_file  = $download_filename . ".zip";

		if (!WT_File::mkdir($temp_dir)) {
			echo "Error : Could not create temporary path!";
			exit;
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
			header('Content-length: '.filesize($temp_dir . $zip_file));
			readfile($temp_dir . $zip_file);
			WT_File::delete($temp_dir);
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
	exit;
}

$controller->pageHeader();

?>
<h2><?php echo $controller->getPageTitle(); ?> - <?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?></h2>
<form name="convertform" method="get">
	<input type="hidden" name="action" value="download">
	<input type="hidden" name="ged" value="<?php echo WT_GEDCOM; ?>">
	<div id="tree-download" class="ui-helper-clearfix">
		<dl>
			<dt>
				<?php echo WT_I18N::translate('Zip file(s)'), help_link('download_zipped'); ?>
			</dt>
			<dd>
				<input type="checkbox" name="zip" value="yes">
			</dd>
			<dt>
				<?php echo WT_I18N::translate('Apply privacy settings?'), help_link('apply_privacy'); ?>
			</dt>
			<dd>
				<input type="radio" name="privatize_export" value="none" checked="checked">&nbsp;&nbsp;<?php echo WT_I18N::translate('None'); ?>
				<br>
				<input type="radio" name="privatize_export" value="gedadmin">&nbsp;&nbsp;<?php echo WT_I18N::translate('Manager'); ?>
				<br>
				<input type="radio" name="privatize_export" value="user">&nbsp;&nbsp;<?php echo WT_I18N::translate('Member'); ?>
				<br>
				<input type="radio" name="privatize_export" value="visitor">&nbsp;&nbsp;<?php echo WT_I18N::translate('Visitor'); ?>
			</dd>
			<dt>
				<?php echo WT_I18N::translate('Convert from UTF-8 to ANSI (ISO-8859-1)'), help_link('utf8_ansi'); ?>
			</dt>
			<dd>
				<input type="checkbox" name="convert" value="yes">
			</dd>
			<dt>
				<?php echo WT_I18N::translate('Add the GEDCOM media path to filenames'), help_link('GEDCOM_MEDIA_PATH'); ?>
			</dt>
			<dd>
				<input type="checkbox" name="conv_path" value="<?php echo WT_Filter::escapeHtml($GEDCOM_MEDIA_PATH); ?>">
				<span dir="auto"><?php echo WT_Filter::escapeHtml($GEDCOM_MEDIA_PATH); ?></span>
			</dd>
		</dl>
	</div>
	<br>
	<input type="submit" value="<?php echo WT_I18N::translate('continue'); ?>">
</form>
