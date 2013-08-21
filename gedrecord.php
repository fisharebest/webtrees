<?php
// This is a default viewer for non-standard records (e.g. SUBM, SUBN)
// that have no dedicated page of their own.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

define('WT_SCRIPT_NAME', 'gedrecord.php');
require './includes/session.php';

$controller=new WT_Controller_Page();

$obj=WT_GedcomRecord::getInstance(safe_GET_xref('pid'));

if (
	$obj instanceof WT_Individual ||
	$obj instanceof WT_Family ||
	$obj instanceof WT_Source ||
	$obj instanceof WT_Repository ||
	$obj instanceof WT_Note ||
	$obj instanceof WT_Media
) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.$obj->getRawUrl());
	exit;
} elseif (!$obj || !$obj->canShow()) {
	$controller->pageHeader();
	print_privacy_error();
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
