<?php
// Display an hourglass chart
//
// Set the root person using the $pid variable
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2007  John Finlay and Others
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

define('WT_SCRIPT_NAME', 'hourglass_ajax.php');
require './includes/session.php';

$controller = new WT_Controller_Hourglass();

header('Content-type: text/html; charset=UTF-8');

Zend_Session::writeClose();

// -- print html header information
if (WT_Filter::get('type')=='desc') {
	$controller->printDescendency(WT_Individual::getInstance($controller->pid), 1, false);
} else {
	$controller->printPersonPedigree(WT_Individual::getInstance($controller->pid), 0);
}
