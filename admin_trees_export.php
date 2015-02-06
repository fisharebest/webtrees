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

define('WT_SCRIPT_NAME', 'admin_trees_export.php');
require './includes/session.php';

if (Auth::isManager($WT_TREE) && Filter::checkCsrf()) {
	$filename = WT_DATA_DIR . $WT_TREE->getName();
	// Force a ".ged" suffix
	if (strtolower(substr($filename, -4)) != '.ged') {
		$filename .= '.ged';
	}

	if ($WT_TREE->exportGedcom($filename)) {
		FlashMessages::addMessage(/* I18N: %s is a filename */ I18N::translate('Family tree exported to %s.', '<span dir="ltr">' . $filename . '</span>'), 'success');
	} else {
		FlashMessages::addMessage(/* I18N: %s is a filename */ I18N::translate('Unable to create %s.  Check the permissions.', $filename), 'danger');
	}
}

header('Location: ' . WT_BASE_URL . 'admin_trees_manage.php');

