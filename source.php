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

use Fisharebest\Webtrees\Controller\SourceController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Source::getInstance(Filter::get('sid', WT_REGEX_XREF), $WT_TREE);
$controller = new SourceController($record);

if ($controller->record && $controller->record->canShow()) {
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This source has been deleted. You should review the deletion and then %1$s or %2$s it.',
				'<a href="#" class="alert-link" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
				'<a href="#" class="alert-link" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This source has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This source has been edited. You should review the changes and then %1$s or %2$s them.',
				'<a href="#" class="alert-link" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
				'<a href="#" class="alert-link" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This source has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	}
	$controller->pageHeader();
} else {
	FlashMessages::addMessage(I18N::translate('This source does not exist or you do not have permission to view it.'), 'danger');
	http_response_code(404);
	$controller->pageHeader();

	return;
}

$families      = $controller->record->linkedFamilies('SOUR');
$individuals   = $controller->record->linkedIndividuals('SOUR');
$notes         = $controller->record->linkedNotes('SOUR');
$media_objects = $controller->record->linkedMedia('SOUR');
$facts         = $controller->record->getFacts();

usort(
	$facts,
	function (Fact $x, Fact $y) {
		static $order = [
			'TITL' => 0,
			'ABBR' => 1,
			'AUTH' => 2,
			'DATA' => 3,
			'PUBL' => 4,
			'TEXT' => 5,
			'NOTE' => 6,
			'OBJE' => 7,
			'REFN' => 8,
			'RIN'  => 9,
			'_UID' => 10,
			'CHAN' => 11,
		];

		return
			(array_key_exists($x->getTag(), $order) ? $order[$x->getTag()] : PHP_INT_MAX)
			-
			(array_key_exists($y->getTag(), $order) ? $order[$y->getTag()] : PHP_INT_MAX);
	}
);

echo View::make('source-page', [
	'source'        => $controller->record,
	'families'      => $families,
	'individuals'   => $individuals,
	'media_objects' => $media_objects,
	'notes'         => $notes,
	'facts'         => $facts,
]);
