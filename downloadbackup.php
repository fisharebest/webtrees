<?php
// Allow an admin user to download the backup file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2005  John Finlay and Others
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

define('WT_SCRIPT_NAME', 'downloadbackup.php');
require './includes/session.php';

$fname = WT_Filter::get('fname');

if (!WT_USER_GEDCOM_ADMIN || !preg_match('/\.zip$/', $fname)) {
	$controller=new WT_Controller_Page();
	$controller
		->setPageTitle(WT_I18N::translate('Error'))
		->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('You do not have permission to view this page.'), '</p>';
	exit;
}

header('Pragma: public'); // required
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private',false); // required for certain browsers
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.$fname.'"');
header('Content-length: '.filesize(WT_DATA_DIR.$fname));
header('Content-Transfer-Encoding: binary');
readfile(WT_DATA_DIR.$fname);
