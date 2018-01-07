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

use Fisharebest\Webtrees\Controller\MediaController;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Media::getInstance(Filter::get('mid', WT_REGEX_XREF), $WT_TREE);
$controller = new MediaController($record);

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
} else {
	http_response_code(404);
	$controller->pageHeader();

	echo View::make('alerts/danger', [
		'alert' => I18N::translate('This media object does not exist or you do not have permission to view it.'),
	]);

	return;
}

echo View::make('media-page', [
	'media'       => $controller->record,
	'individuals' => $controller->record->linkedIndividuals('OBJE'),
	'families'    => $controller->record->linkedFamilies('OBJE'),
	'sources'     => $controller->record->linkedSources('OBJE'),
	'notes'       => $controller->record->linkedNotes('OBJE'),
	'facts'       => array_filter($controller->getFacts(), function (Fact $fact) { return $fact->getTag() !== 'FILE'; }),
]);
