<?php

// Action script for the JustBlack theme
//
// webtrees: Web based Family History software
// Copyright (C) 2014 JustCarmen.
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

define('WT_SCRIPT_NAME', 'action.php');
chdir('../../../'); // change the directory to the root of webtrees to load the required files from session.php.
require './includes/session.php';

header('Content-type: text/html; charset=UTF-8');

if (!WT_Filter::checkCsrf()) {
	Zend_Session::writeClose();
	header('HTTP/1.0 406 Not Acceptable');
	exit;
}

$action = WT_Filter::get('action');
switch ($action) {
	case 'imagetype':
		$xrefs = WT_Filter::postArray('xrefs');

		$data = array();
		foreach ($xrefs as $xref) {
			$row = WT_DB::prepare("SELECT m_type as imagetype FROM `##media` WHERE m_id=?")
				->execute(array($xref))
				->fetchOneRow(PDO::FETCH_ASSOC);

			$data[$xref] = $row['imagetype'];
		};

		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode((object) $data);
		break;
	default:
		header('HTTP/1.0 404 Not Found');
		break;
}