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
require WT_ROOT.'includes/functions/functions_export.php';

$controller=new WT_Controller_Ajax();
$controller
	->pageHeader()
	->setPageTitle(WT_I18N::translate('Export'))
	->requireManagerLogin();

$filename = WT_Site::preference('INDEX_DIRECTORY').WT_GEDCOM;
if (strtolower(substr($filename, -4))!=='.ged') {
	$filename.='.ged';
}
$gedout = fopen($filename.'.tmp', 'w');
if ($gedout) {
	$start = microtime(true);

	$exportOptions = array();
	$exportOptions['privatize'] = 'none';
	$exportOptions['toANSI'] = 'no';
	$exportOptions['path'] = $MEDIA_DIRECTORY;
	$exportOptions['slashes'] = 'forward';

	export_gedcom(WT_GEDCOM, $gedout, $exportOptions);

	$end = microtime(true);
	fclose($gedout);
	if (file_exists($filename)) {
		unlink($filename);
	}
	if (rename($filename.'.tmp', $filename)) {
		echo '<p>', /* I18N: %s is a filename */ WT_I18N::translate('Family tree exported to %s.', realpath($filename)), '</p>';
	} else {
		echo '<p class="error">', /* I18N: %s is a filename */ WT_I18N::translate('Unable to create %s.  Check the permissions.', $filename), '</p>';
	}
} else {
	echo '<p class="error">', /* I18N: %s is a filename */ WT_I18N::translate('Unable to create %s.  Check the permissions.', $filename.'.tmp'), '</p>';
}
