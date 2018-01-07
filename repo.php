<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\RepositoryController;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Repository::getInstance(Filter::get('rid', WT_REGEX_XREF), $WT_TREE);
$controller = new RepositoryController($record);

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
} else {
	http_response_code(404);
	$controller->pageHeader();

	echo View::make('alerts/error', [
		'alert' => I18N::translate('This repository does not exist or you do not have permission to view it.'),
	]);

	return;
}

$sources = $controller->record->linkedSources('REPO');
$facts   = $controller->record->getFacts();

usort(
	$facts,
	function (Fact $x, Fact $y) {
		static $order = [
			'NAME' => 0,
			'ADDR' => 1,
			'NOTE' => 2,
			'WWW'  => 3,
			'REFN' => 4,
			'RIN'  => 5,
			'_UID' => 6,
			'CHAN' => 7,
		];

		return
			(array_key_exists($x->getTag(), $order) ? $order[$x->getTag()] : PHP_INT_MAX)
			-
			(array_key_exists($y->getTag(), $order) ? $order[$y->getTag()] : PHP_INT_MAX);
	}
);

echo View::make('repository-page', [
	'repository' => $controller->record,
	'sources'    => $sources,
	'facts'      => $facts,
]);
