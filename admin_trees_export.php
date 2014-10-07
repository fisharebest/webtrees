<?php
// Exports data from the database to a gedcom file
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

define('WT_SCRIPT_NAME', 'admin_trees_export.php');
require './includes/session.php';

$controller = new WT_Controller_Ajax();
$controller
	->pageHeader()
	->restrictAccess(Auth::isManager());

$filename = WT_DATA_DIR . $WT_TREE->tree_name;
// Force a ".ged" suffix
if (strtolower(substr($filename, -4)) != '.ged') {
	$filename .= '.ged';
}

if ($WT_TREE->exportGedcom($filename)) {
	echo '<p>', /* I18N: %s is a filename */ WT_I18N::translate('Family tree exported to %s.', '<span dir="ltr">' . $filename . '</span>'), '</p>';
} else {
	echo '<p class="error">', /* I18N: %s is a filename */ WT_I18N::translate('Unable to create %s.  Check the permissions.', $filename), '</p>';
}
