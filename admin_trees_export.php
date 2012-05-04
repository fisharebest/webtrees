<?php
// Exports data from the database to a gedcom file
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

define('WT_SCRIPT_NAME', 'admin_trees_export.php');
require './includes/session.php';

$controller=new WT_Controller_Ajax();
$controller
	->pageHeader()
	->setPageTitle(WT_I18N::translate('Export'))
	->requireManagerLogin();

require_once WT_ROOT.'includes/functions/functions_export.php';

// Which gedcoms do we have permission to export?
$gedcoms=array();
foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
	if (userGedcomAdmin(WT_USER_ID, $ged_id)) {
		$gedcoms[$ged_id]=$gedcom;
	}
}

// If we don't have permission to administer any gedcoms, redirect to
// this page, which will force a login and provide a list.
if (empty($gedcoms)) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'admin_trees_manage.php');
}

// Which gedcom have we requested to export
$export = safe_GET('export', preg_quote_array($gedcoms));

if ($export) {
	$ged_id = get_id_from_gedcom($export);
	$filename = get_site_setting('INDEX_DIRECTORY').$export;
	echo '<p>', htmlspecialchars(filename_decode($export)), ' => ', $filename, '</p>';
	flush();
	$gedout = fopen($filename.'.tmp', 'w');
	if ($gedout) {
		$start = microtime(true);

		$exportOptions = array();
		$exportOptions['privatize'] = 'none';
		$exportOptions['toANSI'] = 'no';
		$exportOptions['path'] = $MEDIA_DIRECTORY;
		$exportOptions['slashes'] = 'forward';

		export_gedcom($export, $gedout, $exportOptions);

		$end = microtime(true);
		fclose($gedout);
		if (file_exists($filename)) {
			unlink($filename);
		}
		if (rename($filename.'.tmp', $filename)) {
			echo '<p>', /* I18N: %s is a filename */ WT_I18N::translate('Family tree exported to %s.', $filename), '</p>';
		} else {
			echo '<p class="error">', /* I18N: %s is a filename */ WT_I18N::translate('Unable to create %s.  Check the permissions.', $filename), '</p>';
		}
	} else {
		echo '<p class="error">', /* I18N: %s is a filename */ WT_I18N::translate('Unable to create %s.  Check the permissions.', $filename.'.tmp'), '</p>';
	}
} else {
	echo '<h1>', WT_I18N::translate('Export family tree'), '</h1>';
	echo '<ul>';
	foreach ($gedcoms as $ged_id=>$gedcom) {
		echo '<li><a href="?export=', rawurlencode($gedcom), '">', $gedcom, ' => ', htmlspecialchars(filename_decode(realpath(get_gedcom_setting($ged_id, 'path')))), '</a></li>';
	}
	echo '</ul>';
}

echo '<p class="center"><a href="#" onclick="return modalDialogSubmitAjax(this);">', WT_I18N::translate('Close Window'), '</a></p>';
