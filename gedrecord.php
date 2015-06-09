<?php
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
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;

define('WT_SCRIPT_NAME', 'gedrecord.php');
require './includes/session.php';

$controller = new PageController;

$obj = GedcomRecord::getInstance(Filter::get('pid', WT_REGEX_XREF), $WT_TREE);

if (
	$obj instanceof Individual ||
	$obj instanceof Family ||
	$obj instanceof Source ||
	$obj instanceof Repository ||
	$obj instanceof Note ||
	$obj instanceof Media
) {
	header('Location: ' . WT_BASE_URL . $obj->getRawUrl());

	return;
} elseif (!$obj || !$obj->canShow()) {
	$controller->pageHeader();
	echo '<div class="error">', I18N::translate('This information is private and cannot be shown.'), '</div>';
} else {
	$controller->pageHeader();
	echo
		'<pre style="white-space:pre-wrap; word-wrap:break-word;">',
		preg_replace(
			'/@(' . WT_REGEX_XREF . ')@/', '@<a href="gedrecord.php?pid=$1">$1</a>@',
			Filter::escapeHtml($obj->getGedcom())
		),
		'</pre>';
}
