<?php
// Allow an admin user to download the entire gedcom file.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'admin_trees_download.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_export.php';

$controller=new WT_Controller_Base();
$controller
	->setPageTitle(WT_I18N::translate('Download GEDCOM'))
	->requireManagerLogin();

// Validate user parameters
$action           = safe_GET('action',           'download');
$convert          = safe_GET('convert',          'yes', 'no');
$zip              = safe_GET('zip',              'yes', 'no');
$conv_path        = safe_GET('conv_path',        WT_REGEX_NOSCRIPT);
$conv_slashes     = safe_GET('conv_slashes',     array('forward', 'backward'), 'forward');
$privatize_export = safe_GET('privatize_export', array('none', 'visitor', 'user', 'gedadmin'));

if ($action == 'download') {
	$conv_path = rtrim(str_replace('\\', '/', trim($conv_path)), '/').'/'; // make sure we have a trailing slash here
	if ($conv_path=='/') $conv_path = '';

	$exportOptions = array();
	$exportOptions['privatize'] = $privatize_export;
	$exportOptions['toANSI'] = $convert;
	$exportOptions['path'] = $conv_path;
	$exportOptions['slashes'] = $conv_slashes;
}

$fileName = WT_GEDCOM;
if ($action == "download" && $zip == "yes") {
	require WT_ROOT.'library/pclzip.lib.php';

	$temppath = get_site_setting('INDEX_DIRECTORY') . "tmp/";
	$zipname = "dl" . date("YmdHis") . $fileName . ".zip";
	$zipfile = get_site_setting('INDEX_DIRECTORY') . $zipname;
	$gedname = $temppath . $fileName;

	$removeTempDir = false;
	if (!is_dir(filename_decode($temppath))) {
		$res = mkdir(filename_decode($temppath));
		if ($res !== true) {
			echo "Error : Could not create temporary path!";
			exit;
		}
		$removeTempDir = true;
	}
	$gedout = fopen(filename_decode($gedname), "w");
	export_gedcom($GEDCOM, $gedout, $exportOptions);
	fclose($gedout);
	$comment = "Created by ".WT_WEBTREES." ".WT_VERSION_TEXT." on " . date("r") . ".";
	$archive = new PclZip(filename_decode($zipfile));
	$v_list = $archive->create(filename_decode($gedname), PCLZIP_OPT_COMMENT, $comment, PCLZIP_OPT_REMOVE_PATH, filename_decode($temppath));
	if ($v_list == 0) echo "Error : " . $archive->errorInfo(true);
	else {
		unlink(filename_decode($gedname));
		if ($removeTempDir) rmdir(filename_decode($temppath));
		header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH."downloadbackup.php?fname=".$zipname);
		exit;
	}
	exit;
}

if ($action == "download") {
	header('Content-Type: text/plain; charset=UTF-8');
	// We could open "php://compress.zlib" to create a .gz file or "php://compress.bzip2" to create a .bz2 file
	$gedout = fopen('php://output', 'w');
	if (strtolower(substr($fileName, -4, 4))!='.ged') {
		$fileName.='.ged';
	}
	header('Content-Disposition: attachment; filename="'.$fileName.'"');
	export_gedcom(WT_GEDCOM, $gedout, $exportOptions);
	fclose($gedout);
	exit;
}

$controller->pageHeader();

?>
<h2><?php echo WT_I18N::translate('Download GEDCOM'); ?> - <?php echo htmlspecialchars(WT_GEDCOM); ?></h2>
<form name="convertform" method="get">
	<input type="hidden" name="action" value="download">
	<input type="hidden" name="ged" value="<?php echo WT_GEDCOM; ?>">
	<div id="tree-download" class="ui-helper-clearfix">
		<dl>
			<dt>
				<?php echo WT_I18N::translate('Zip File(s)'), help_link('download_zipped'); ?>
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
				<?php echo WT_I18N::translate('Convert media path to'), help_link('convertPath'); ?>
			</dt>
			<dd>
				<input type="text" name="conv_path" size="30" value="<?php echo $conv_path; ?>" dir="auto">
			</dd>
			<dt>
				<?php echo WT_I18N::translate('Convert media folder separators to'), help_link('convertSlashes'); ?>
			</dt>
			<dd>
				<input type="radio" name="conv_slashes" value="forward" <?php if ($conv_slashes=='forward') echo "checked=\"checked\" "; ?>>&nbsp;&nbsp;<?php echo WT_I18N::translate('Forward slashes : /'); ?>
				<br>
				<input type="radio" name="conv_slashes" value="backward" <?php if ($conv_slashes=='backward') echo "checked=\"checked\" "; ?>>&nbsp;&nbsp;<?php echo WT_I18N::translate('Backslashes : \\'); ?>
			</dd>
		</dl>
	</div>
	<br>
	<input type="submit" value="<?php echo WT_I18N::translate('Download Now'); ?>">
</form>
