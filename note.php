<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Controller\NoteController;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Note::getInstance(Filter::get('nid', WT_REGEX_XREF), $WT_TREE);
$controller = new NoteController($record);

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
} else {
	http_response_code(404);
	$controller->pageHeader();

	echo View::make('alerts/danger', [
		'alert' => I18N::translate('This note does not exist or you do not have permission to view it.'),
	]);
	return;
}

$families      = $controller->record->linkedFamilies('NOTE');
$individuals   = $controller->record->linkedIndividuals('NOTE');
$notes         = [];
$media_objects = $controller->record->linkedMedia('NOTE');
$sources       = $controller->record->linkedSources('NOTE');

$facts = [];
foreach ($controller->record->getFacts() as $fact) {
	if ($fact->getTag() != 'CONT') {
		$facts[] = $fact;
	}
}

$text = Filter::formatText($controller->record->getNote(), $controller->record->getTree());

echo View::make('note-page', [
	'note'          => $controller->record,
	'families'      => $families,
	'individuals'   => $individuals,
	'sources'       => $sources,
	'media_objects' => $media_objects,
	'notes'         => $notes,
	'facts'         => $facts,
	'text'          => $text
]);
