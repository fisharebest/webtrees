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
use Fisharebest\Webtrees\Functions\FunctionsPrint;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$record     = Repository::getInstance(Filter::get('rid', WT_REGEX_XREF), $WT_TREE);
$controller = new RepositoryController($record);

if ($controller->record && $controller->record->canShow()) {
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This repository has been deleted. You should review the deletion and then %1$s or %2$s it.',
				'<a href="#" class="alert-link" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
				'<a href="#" class="alert-link" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This repository has been deleted. The deletion will need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			FlashMessages::addMessage(/* I18N: %1$s is “accept”, %2$s is “reject”. These are links. */ I18N::translate(
				'This repository has been edited. You should review the changes and then %1$s or %2$s them.',
				'<a href="#" class="alert-link" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
				'<a href="#" class="alert-link" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
			) . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		} elseif (Auth::isEditor($controller->record->getTree())) {
			FlashMessages::addMessage(I18N::translate('This repository has been edited. The changes need to be reviewed by a moderator.') . ' ' . FunctionsPrint::helpLink('pending_changes'), 'warning');
		}
	}
	$controller->pageHeader();
} else {
	FlashMessages::addMessage(I18N::translate('This repository does not exist or you do not have permission to view it.'), 'danger');
	http_response_code(404);
	$controller->pageHeader();

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
