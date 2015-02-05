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

use Zend_Session;

define('WT_SCRIPT_NAME', 'hourglass_ajax.php');
require './includes/session.php';

$controller = new HourglassController;

header('Content-type: text/html; charset=UTF-8');

Zend_Session::writeClose();

// -- print html header information
if (Filter::get('type') == 'desc') {
	$controller->printDescendency(Individual::getInstance($controller->pid), 1, false);
} else {
	$controller->printPersonPedigree(Individual::getInstance($controller->pid), 0);
}
