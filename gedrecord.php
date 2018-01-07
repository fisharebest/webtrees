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

use Fisharebest\Webtrees\Controller\GedcomRecordController;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record = GedcomRecord::getInstance(Filter::get('pid', WT_REGEX_XREF), $WT_TREE);
if (
	$record instanceof Individual ||
	$record instanceof Family ||
	$record instanceof Source ||
	$record instanceof Repository ||
	$record instanceof Note ||
	$record instanceof Media
) {
	header('Location: ' . $record->url());

	return;
}
$controller = new GedcomRecordController($record);

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
} else {
	http_response_code(404);
	$controller->pageHeader();

	echo View::make('alerts/error', [
		'alert' => I18N::translate('This record does not exist or you do not have permission to view it.'),
	]);

	return;
}

$individuals   = $controller->record->linkedIndividuals('SUBM');
$families      = $controller->record->linkedFamilies('SUBM');
$media_objects = $controller->record->linkedMedia('SUBM');
$sources       = $controller->record->linkedSources('SUBM');
$notes         = $controller->record->linkedNotes('SUBM');
$facts         = $controller->record->getFacts();

echo View::make('gedcom-record-page', [
	'facts'         => $facts,
	'families'      => $families,
	'individuals'   => $individuals,
	'media_objects' => $media_objects,
	'notes'         => $notes,
	'record'        => $controller->record,
	'sources'       => $sources,
]);
