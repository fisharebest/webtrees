<?php

/**
 * Allow an admin user to download the entire gedcom	file.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'downloadgedcom.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_export.php';

// Validate user parameters
if (!isset($_SESSION['exportConvPath'])) $_SESSION['exportConvPath'] = $MEDIA_DIRECTORY;
if (!isset($_SESSION['exportConvSlashes'])) $_SESSION['exportConvSlashes'] = 'forward';

$ged				= safe_GET('ged',				get_all_gedcoms());
$action				= safe_GET('action',			'download');
$remove				= safe_GET('remove',			'yes', 'no');
$convert			= safe_GET('convert',			'yes', 'no');
$zip				= safe_GET('zip',				'yes', 'no');
$conv_path			= safe_GET('conv_path',			WT_REGEX_NOSCRIPT,				$_SESSION['exportConvPath']);
$conv_slashes		= safe_GET('conv_slashes',		array('forward', 'backward'),	$_SESSION['exportConvSlashes']);
$privatize_export	= safe_GET('privatize_export',	array('none', 'visitor', 'user', 'gedadmin', 'admin'));
$filetype			= safe_GET('filetype',			array('gedcom', 'gramps'));

$conv_path = stripLRMRLM($conv_path);
$_SESSION['exportConvPath'] = $conv_path;		// remember this for the next Download
$_SESSION['exportConvSlashes'] = $conv_slashes;

if (!WT_USER_GEDCOM_ADMIN || !$ged) {
	header("Location: editgedcoms.php");
	exit;
}

if ($action == 'download') {
	$conv_path = rtrim(str_replace('\\', '/', trim($conv_path)), '/').'/';	// make sure we have a trailing slash here
	if ($conv_path=='/') $conv_path = '';

	$exportOptions = array();
	$exportOptions['privatize'] = $privatize_export;
	$exportOptions['toANSI'] = $convert;
	$exportOptions['noCustomTags'] = $remove;
	$exportOptions['path'] = $conv_path;
	$exportOptions['slashes'] = $conv_slashes;
}

if ($action == "download" && $zip == "yes") {
	require WT_ROOT.'library/pclzip.lib.php';

	$temppath = $INDEX_DIRECTORY . "tmp/";
	$fileName = $ged;
	if ($filetype =="gramps") $fileName = $ged.".gramps";
	$zipname = "dl" . date("YmdHis") . $fileName . ".zip";
	$zipfile = $INDEX_DIRECTORY . $zipname;
	$gedname = $temppath . $fileName;

	$removeTempDir = false;
	if (!is_dir(filename_decode($temppath))) {
		$res = mkdir(filename_decode($temppath));
		if ($res !== true) {
			print "Error : Could not create temporary path!";
			exit;
		}
		$removeTempDir = true;
	}
	$gedout = fopen(filename_decode($gedname), "w");
	switch ($filetype) {
	case 'gedcom':
		export_gedcom($GEDCOM, $gedout, $exportOptions);
		break;
	case 'gramps':
		export_gramps($GEDCOM, $gedout, $exportOptions);
		break;
	}
	fclose($gedout);
	$comment = "Created by ".WT_WEBTREES." ".WT_VERSION_TEXT." on " . date("r") . ".";
	$archive = new PclZip(filename_decode($zipfile));
	$v_list = $archive->create(filename_decode($gedname), PCLZIP_OPT_COMMENT, $comment, PCLZIP_OPT_REMOVE_PATH, filename_decode($temppath));
	if ($v_list == 0) print "Error : " . $archive->errorInfo(true);
	else {
		unlink(filename_decode($gedname));
		if ($removeTempDir) rmdir(filename_decode($temppath));
		header("Location: ".encode_url("downloadbackup.php?fname={$zipname}", false));
		exit;
	}
	exit;
}

if ($action == "download") {
	header('Content-Type: text/plain; charset=UTF-8');
	// We could open "php://compress.zlib" to create a .gz file or "php://compress.bzip2" to create a .bz2 file
	$gedout = fopen('php://output', 'w');
	switch ($filetype) {
	case 'gedcom':
		if (strtolower(substr($ged, -4, 4))!='.ged') {
			$ged.='.ged';
		}
		header('Content-Disposition: attachment; filename="'.$ged.'"');
		export_gedcom($GEDCOM, $gedout, $exportOptions);
		break;
	case 'gramps':
		header('Content-Disposition: attachment; filename="'.$ged.'.gramps"');
		export_gramps($GEDCOM, $gedout, $exportOptions);
		break;
	}
	fclose($gedout);
	exit;
}

print_header(i18n::translate('Download GEDCOM'));

?>
<div class="center"><h2><?php print i18n::translate('Download GEDCOM'); ?></h2></div>
<br />
<form name="convertform" method="get">
	<input type="hidden" name="action" value="download" />
	<input type="hidden" name="ged" value="<?php print $ged; ?>" />
	<table class="list_table width50" border="0" valign="top">
	<tr><td colspan="2" class="facts_label03"><?php print i18n::translate('Options:'); ?></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('File Type'), help_link('file_type'); ?></td>
		<td class="optionbox">
		<?php if ($TEXT_DIRECTION=='ltr') { ?>
			<input type="radio" name="filetype" checked="checked" value="gedcom" />&nbsp;&nbsp;GEDCOM<br/><input type="radio" name="filetype" value="gramps" />&nbsp;&nbsp;Gramps XML
		<?php } else { ?>
			GEDCOM&nbsp;&nbsp;<?php print getLRM();?><input type="radio" name="filetype" checked="checked" value="gedcom" /><?php print getLRM();?><br />Gramps XML&nbsp;&nbsp;<?php print getLRM();?><input type="radio" name="filetype" value="gramps" /><?php print getLRM();?>
		<?php } ?>
		</td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Zip File(s)'), help_link('download_zipped'); ?></td>
		<td class="list_value"><input type="checkbox" name="zip" value="yes" checked="checked" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Apply privacy settings?'), help_link('apply_privacy'); ?></td>
		<td class="list_value">
		<?php if (WT_USER_IS_ADMIN) { ?>
			<input type="radio" name="privatize_export" value="none" checked="checked" />&nbsp;&nbsp;<?php print i18n::translate('None'); ?><br />
			<input type="radio" name="privatize_export" value="visitor" />&nbsp;&nbsp;<?php print i18n::translate('Visitor'); ?><br />
		<?php } else { ?>
			<input type="radio" name="privatize_export" value="none" DISABLED />&nbsp;&nbsp;<?php print i18n::translate('None'); ?><br />
			<input type="radio" name="privatize_export" value="visitor" checked="checked" />&nbsp;&nbsp;<?php print i18n::translate('Visitor'); ?><br />
		<?php } ?>
		<input type="radio" name="privatize_export" value="user" />&nbsp;&nbsp;<?php print i18n::translate('Authenticated user'); ?><br />
		<input type="radio" name="privatize_export" value="gedadmin" />&nbsp;&nbsp;<?php print i18n::translate('GEDCOM administrator'); ?><br />
		<input type="radio" name="privatize_export" value="admin"<?php if (!WT_USER_IS_ADMIN) print " DISABLED"; ?> />&nbsp;&nbsp;<?php print i18n::translate('Site administrator'); ?>
		</td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Convert from UTF-8 to ANSI (ISO-8859-1)'), help_link('utf8_ansi'); ?></td>
		<td class="list_value"><input type="checkbox" name="convert" value="yes" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Remove custom PGV tags? (eg. _WT_USER, _THUM)'), help_link('remove_tags'); ?></td>
		<td class="list_value"><input type="checkbox" name="remove" value="yes" checked="checked" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Convert media path to'), help_link('convertPath');?></td>
		<td class="list_value"><input type="text" name="conv_path" size="30" value="<?php echo getLRM(), $conv_path, getLRM();?>" /></td></tr>
	<tr><td class="descriptionbox width50 wrap"><?php echo i18n::translate('Convert media folder separators to'), help_link('convertSlashes');?></td>
		<td class="list_value">
		<input type="radio" name="conv_slashes" value="forward" <?php if ($conv_slashes=='forward') print "checked=\"checked\" "; ?>/>&nbsp;&nbsp;<?php print i18n::translate('Forward slashes : /');?><br />
		<input type="radio" name="conv_slashes" value="backward" <?php if ($conv_slashes=='backward') print "checked=\"checked\" "; ?>/>&nbsp;&nbsp;<?php print i18n::translate('Backslashes : \\');?>
		</td></tr>
	<tr><td class="facts_label03" colspan="2">
	<input type="submit" value="<?php print i18n::translate('Download Now'); ?>" />
	<input type="button" value="<?php print i18n::translate('Back');?>" onclick="window.location='editgedcoms.php';"/></td></tr>
	</table><br />
	<br /><br />
</form>
<?php

print i18n::translate('NOTE: Large databases can take a long time to process before downloading.  If PHP times out before the download finishes, the downloaded file may not be complete.<br /><br />To make sure that the file was downloaded correctly, check that the last line of a file in GEDCOM format is <b>0&nbsp;TRLR</b> or that the last line of a file in XML format is <b>&lt;/database&gt;</b>.  These files are text; you can use any suitable text editor, but be sure to <u>not</u> save the downloaded file after you have inspected it.<br /><br />In general, it could take as much time to download as it took to import your original GEDCOM file.') . "<br /><br /><br />\n";
print_footer();
?>
