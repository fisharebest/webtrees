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
	// Don't use export_gedcom() - which includes pending records, privacy filtering,
	// media path rewriting, etc.
	// Instead simply dump the data to the file, which requires much less memory and CPU.

	$buffer=reformat_record_export(gedcom_header(WT_GEDCOM));

	// Individuals
	$rows=WT_DB::prepare(
		"SELECT i_gedcom FROM `##individuals` WHERE i_file=?"
	)->execute(array(WT_GED_ID))->fetchOneColumn();
	foreach ($rows as $row) {
		$buffer.=reformat_record_export($row);
		if (strlen($buffer)>65536) {
			fwrite($gedout, $buffer);
			$buffer='';
		}
	}

	// Families
	$rows=WT_DB::prepare(
		"SELECT f_gedcom FROM `##families` WHERE f_file=?"
	)->execute(array(WT_GED_ID))->fetchOneColumn();
	foreach ($rows as $row) {
		$buffer.=reformat_record_export($row);
		if (strlen($buffer)>65536) {
			fwrite($gedout, $buffer);
			$buffer='';
		}
	}

	// Sources
	$rows=WT_DB::prepare(
		"SELECT s_gedcom FROM `##sources` WHERE s_file=?"
	)->execute(array(WT_GED_ID))->fetchOneColumn();
	foreach ($rows as $row) {
		$buffer.=reformat_record_export($row);
		if (strlen($buffer)>65536) {
			fwrite($gedout, $buffer);
			$buffer='';
		}
	}

	// Repositories and notes
	$rows=WT_DB::prepare(
		"SELECT o_gedcom FROM `##other` WHERE o_file=? AND o_type NOT IN ('HEAD', 'TRLR')"
	)->execute(array(WT_GED_ID))->fetchOneColumn();
	foreach ($rows as $row) {
		$buffer.=reformat_record_export($row);
		if (strlen($buffer)>65536) {
			fwrite($gedout, $buffer);
			$buffer='';
		}
	}

	// Media
	$rows=WT_DB::prepare(
		"SELECT m_gedcom FROM `##media` WHERE m_file=?"
	)->execute(array(WT_GED_ID))->fetchOneColumn();
	foreach ($rows as $row) {
		$buffer.=reformat_record_export($row);
		if (strlen($buffer)>65536) {
			fwrite($gedout, $buffer);
			$buffer='';
		}
	}

	fwrite($gedout, $buffer."0 TRLR".WT_EOL);
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
