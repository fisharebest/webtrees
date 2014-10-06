<?php
// This is a default viewer for non-standard records (e.g. SUBM, SUBN)
// that have no dedicated page of their own.
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

define('WT_SCRIPT_NAME', 'gedrecord.php');
require './includes/session.php';

$controller = new WT_Controller_Page();

$obj = WT_GedcomRecord::getInstance(WT_Filter::get('pid', WT_REGEX_XREF));

if (
	$obj instanceof WT_Individual ||
	$obj instanceof WT_Family     ||
	$obj instanceof WT_Source     ||
	$obj instanceof WT_Repository ||
	$obj instanceof WT_Note       ||
	$obj instanceof WT_Media
) {
	Zend_Session::writeClose();
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.$obj->getRawUrl());
	exit;
} elseif (!$obj || !$obj->canShow()) {
	$controller->pageHeader();
	echo '<div class="error">', WT_I18N::translate('This information is private and cannot be shown.'), '</div>';
} else {
	$controller->pageHeader();
	echo
		'<pre style="white-space:pre-wrap; word-wrap:break-word;">',
		preg_replace(
			'/@('.WT_REGEX_XREF.')@/', '@<a href="gedrecord.php?pid=$1">$1</a>@',
			WT_Filter::escapeHtml($obj->getGedcom())
		),
		'</pre>';
}
